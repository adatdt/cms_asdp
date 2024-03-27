<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Log_finnet extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_log_finnet','finnet');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_gate_in';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'log/log_finnet';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->finnet->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->finnet->get_identity_app();
        
        if($get_identity==0)
        {
            // ambil port berdasarkan user
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->finnet->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id'))->result();
                $row_port=1;
            }
            else
            {
                $port=$this->finnet->select_data("app.t_mtr_port","where status !='-5' order by name asc")->result();
                $row_port=0;
            }
        }
        else
        {
            $port=$this->finnet->select_data("app.t_mtr_port","where id=".$get_identity)->result();
            $row_port=1;
        }        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Log Finnet',
            'content'  => 'log_finnet/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'service'  => $this->finnet->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'port'=>$port,
            'row_port'=>$row_port,
            'gs'=>$this->check_gs()==12?"false":"true",
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),
            'team'=>$this->finnet->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
	}


    public function detail($booking_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $id=$this->enc->decode($booking_code);

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title']    = 'detail booking';
        $data['title'] = 'Detail Booking';
        $data['content']  = 'booking/detail';
        $data['id']       = $booking_code;
        $data['port']=$this->booking->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['booking']=$this->booking->select_data("$this->_table","where booking_code ='".$id."' ")->row();

        $this->load->view($this->_module.'/detail_modal',$data); 

        // $this->load->view('default',$data);   
    }

    public function download_excel()
    {

        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");

        $data = $this->finnet->download()->result();

        $file_name = 'Log finnet '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');


        $header = array(
            'NO' =>'string',
            'TANGGAL TRANSAKSI' =>'string',
            'KODE PERANGKAT' =>'string',
            'NAMA PERANGKAT' =>'string',
            'TIPE PERANGKAT' =>'string',
            'STATE' =>'string',
            'DATA TRANSAKSI' =>'string',
        );

        $no=1;


        foreach ($data as $key => $value) {
            $rows[] = array($no,
                            $value->finnet_date,
                            $value->terminal_code,
                            $value->terminal_name,
                            $value->terminal_type_name,
                            $value->state,
                            $value->data_finnet,
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

    function check_gs()
    {
        // checking apakan user gs ,
        $check_gs=$this->finnet->select_data("core.t_mtr_user", " where id=".$this->session->userdata("id"))->row();

        return $check_gs->user_group_id;
    }

}
