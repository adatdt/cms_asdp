<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Crm extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('Models_crm','crm');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        // $this->_table    = 'app.t_mtr_dock';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'crm';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        
        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'CRM',
            //'tampil'   => $this->get_data(),
            'table'    => '',
            'option'   => '',
            // 'date_p'   =>  date('Y-m-d', strtotime("-1 day", strtotime(date("Y-m-d")))),
            'date_p'   =>  date('Y-m-d H:i:s',time() - 3600),
            'content'  => 'index'
        );

		$this->load->view('default', $data);
    }

    public function get_data(){
        validate_ajax();
        $json = $this->crm->get_data_crm();
        echo json_encode($json);
    }

    public function download_excel(){
        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M');

        $data = $this->crm->download()->result();

        $file_name = 'Crm';
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

        $header = array(
            'NO' => 'string',
            'ID NUMBER' => 'string',
            'NAMA PENUMPANG' => 'string',
            'SERVICE' => 'string',
            'KODE BOOKING' => 'string',
            'TANGGAL BOOKING' => 'string',
            'KEBERANGKATAN' => 'string',
            'TUJUAN' => 'string',
            'NAMA KAPAL' => 'string',
            'KELAS KAPAL' => 'string',
            'KODE BOARDING' => 'string',
            'TANGGAL BOARDING' => 'string',
            'STATUS' => 'string'
        );

        $no = 0;
        foreach($data as $key => $value){
            $no++;
            $rows[] = array($no,
                            $value->id_number,
                            $value->name,
                            $value->service_name,
                            $value->booking_code,
                            $value->booking_date,
                            $value->origin,
                            $value->destination,
                            $value->ship_name,
                            $value->ship_class,
                            $value->boarding_code,
                            $value->boarding_date,
                            $value->status_name
            );
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1',$header,$styles1);

        foreach($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->writeToStdOut();
        
    }

}
