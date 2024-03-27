<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Ftp_brilink extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_ftp_brilink','brilink');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_monitoring_ftp_brilink';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'monitoring/ftp_brilink';

        $this->dbView = checkReplication();
        $this->dbAction = $this->load->database("dbAction", TRUE);
	}

    /*
    Document   : Monitoring
    Created on : 21 juli, 2023
    Author     : soma
    Description: Enhancement cms penerapan validasi dan token csrf 
    */

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('tipe')){
                $this->form_validation->set_rules('tipe', 'Tipe FTP', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid tipe ftp'));
            }
            if($this->input->post('dateFrom')){
                $this->form_validation->set_rules('dateFrom', 'Tanggal awal', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal awal'));
            }
            if($this->input->post('dateTo')){
                $this->form_validation->set_rules('dateTo', 'Tanggal akhir', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal akhir'));
            }
            if($this->input->post('searchData')){
                $this->form_validation->set_rules('searchData', 'searchData', 'trim|callback_special_char', array('special_char' => 'search has contain invalid characters'));
            }
            
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->brilink->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Status FTP Brilink',
            'content'  => 'ftp_brilink/index',

        );

		$this->load->view('default', $data);
	}

    /*
    Document   : Monitoring
    Created on : 21 juli, 2023
    Author     : soma
    Description: end Enhancement cms penerapan validasi dan token csrf 
    */


}
