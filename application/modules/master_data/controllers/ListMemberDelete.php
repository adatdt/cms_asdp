<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );


class ListMemberDelete extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('ListMemberDeleteModel','member');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('Html2pdf');

        $this->_table    = 'app.t_mtr_member';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/listMemberDelete';

        $this->dbView=checkReplication();
        $this->dbAction = $this->load->database("dbAction", TRUE);
	}
	public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->member->dataList();
            echo json_encode($rows);
            exit;
        }

        $btnExcel='<button class="btn btn-sm btn-warning download" id="download_excel" disabled >Excel</button>';
        $btnPdf='<button class="btn btn-sm btn-warning download" id="download_pdf" target="_blank" disabled>Pdf</button>';
        $excel = generate_button($this->_module, "download_excel", $btnExcel);
        $pdf = generate_button($this->_module, "download_pdf", $btnPdf);

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Hapus Akun Member',
            'content'  => 'listMemberDelete/index',
            "excel"=> $excel,
            "pdf"=>$pdf
        );

		$this->load->view('default', $data);
	}

    public function downloadExcel()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("dateFrom");
        $dateTo = $this->input->get("dateTo");

        $data = $this->member->download();


        $file_name = 'Hapus Akun Member Tanggal ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');

        $widthCell = [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20];
        $styles1 = array('height' => 50,
                    'widths' => $widthCell,
                    'font' => 'Arial',
                    'font-size' =>10,
                    'font-style' => 'bold',
                    'fill' => '#eee',
                    'halign' => 'center',
                    'border' => 'left,right,top,bottom');


        $header = array(
            "NO"=>"string",
            "AKUN"=>"string",
            "NAMA LENGKAP"=>"string",
            "NO HP"=>"string",
            "DATE CREATE AKUN"=>"string",
            "DATE DELETE AKUN"=>"string",
            "ALASAN DELETE AKUN"=>"string");

        $no = 1;
        $rows = [];
        foreach ($data as $key => $value) {
            
            $rows[] = array(
                $no,
                $value->email,
                $value->fullname,
                $value->phone_number,
                $value->account_created_on,
                $value->account_delete_date,
                $value->reason_text_selected,
            );
            $no++;
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header, $styles1);

        foreach ($rows as $row) {
            $writer->writeSheetRow('Sheet1', $row);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }

    public function downloadPdf()
    {
        // $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");
        

        $data['data'] = $this->member->download();
        $data['departDateFrom'] = $dateFrom;
        $data['departDateTo'] = $dateTo;

        // print_r($data); exit;

        // echo "hai";
        $this->load->view('listMemberDelete/pdf',$data);

    }        


}
