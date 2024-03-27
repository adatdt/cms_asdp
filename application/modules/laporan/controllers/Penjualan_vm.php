<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
error_reporting(0);

class Penjualan_vm extends MY_Controller{
    public function __construct(){
        parent::__construct();

        logged_in();
        $this->load->model('Penjualan_vm_model','m_vm');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_username = $this->session->userdata('username');
        $this->_module   = 'laporan/penjualan_vm';

        $this->dbView = $this->load->database("dbView", TRUE);
        $this->dbAction = $this->load->database("dbAction", TRUE);        
    }

    public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->m_vm->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity = $this->m_vm->get_identity_app();
        // port berdasarkan user

        if($get_identity == 0){
            if(!empty($this->session->userdata('port_id')) ){
                $port = $this->global_model->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')."")->result();
                $row_port = 1;
            }else {
                $port = $this->global_model->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result();
                $row_port = 0;
            }

        }else{
            $port = $this->global_model->select_data("app.t_mtr_port","where id=".$get_identity."")->result();
            $row_port = 1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Laporan Penjualan Vending Machine',
            'content'  => 'penjualan_vm/index',
            'port'     => $port,
            'shift'    => $this->global_model->select_data("app.t_mtr_shift","where status=1 order by id asc")->result(),
            'vm'       => $this->m_vm->getvm(),
            'btn_excel' =>checkBtnAccess($this->_module,'download_excel'),
        );

        $this->load->view('default', $data);
    }

    public function penumpang(){
        if($this->input->is_ajax_request()){
            $rows = $this->m_vm->listPenumpang();
            echo json_encode($rows);
            exit;
        }
    }

    public function get_vm($port_id="")
    {
        validate_ajax();
        $port_id = $this->enc->decode($port_id);

        if (!$port_id) {
            $data = $this->m_vm->getvm();
            $option = '<option value="" selected>Semua</option>';
            foreach ($data as $key => $value) {
                $option .= '<option value="'.$this->enc->encode($value->terminal_code).'">'.$value->terminal_name.'</option>';
            }
            echo $option;
        }else{
            $data = $this->m_vm->getvm($port_id);
            $option = '<option value="" selected>Semua</option>';
            foreach ($data as $key => $value) {
                $option .= '<option value="'.$this->enc->encode($value->terminal_code).'">'.$value->terminal_name.'</option>';
            }
            echo $option;
        }
    }

    public function get_regu($port_id="")
    {
        validate_ajax();
        $port_id = $this->enc->decode($port_id);

        if (!$port_id) {
            $option = '<option value="" selected>Semua</option>';
            echo $option;
        }else{
            $data = $this->m_vm->get_team($port_id);
            $option = '<option value="" selected>Semua</option>';
            foreach ($data as $key => $value) {
                $option .= '<option value="'.$this->enc->encode($value->team_code).'">'.$value->team_name.'</option>';
            }
            echo $option;
        }
    }

    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
        $dateFrom = $this->input->get("dateFrom");
        $dateTo = $this->input->get("dateTo");

        $data = $this->m_vm->download()->result();

        $file_name = 'Penjualan_vm_'.$dateFrom.'_s/d_'.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

        $header = array(
           "NO." => 'string',
           "NAMA PERANGKAT" => 'string',
           "KODE BOOKING" => 'string',
           "NOMOR TIKET" => 'string',
           "GOLONGAN" => 'string',
           "METODE BAYAR" => 'string',
           "KELAS" => 'string',
           "TANGGAL SHIFT" => 'string',
           "SHIFT" => 'string',
           "REGU" => 'string',
           "NAMA PENGGUNA JASA" => 'string',
           "NO IDENTITAS" => 'string',
           "NAMA KAPAL" => 'string',
           "TANGGAL KLAIM" => 'string',
           "TARIF (Rp)" => 'number',
        );

        $no = 1;
        foreach ($data as $key => $row) {

            $rows[] = array(
                $no,
                $row->terminal_name,
                $row->booking_code,
                $row->ticket_number,
                $row->golongan,
                $row->payment_type,
                $row->kelas,
                $row->trans_date,
                $row->shift,
                $row->regu,
                $row->customer_name,
                "'".$row->id_number,
                $row->ship,
                $row->naik_kapal,
                $row->tarif
            );
            $no++;
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header,$styles1);

        foreach($rows as $row)
        $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }

}