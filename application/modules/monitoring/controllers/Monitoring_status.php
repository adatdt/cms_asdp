<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Monitoring_status extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('M_status','mstatus');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_dock';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'monitoring/M_status';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        
        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Monitoring Status',
            //'tampil'   => $this->get_data(),
            // 'table'    => $this->create_table(),
            'option_server'   => $this->create_option_server_id(),
            'option_table' => $this->create_option_table_name(),
            // 'date_p'   =>  date('Y-m-d', strtotime("-1 day", strtotime(date("Y-m-d")))),
            'date_p'   =>  date('Y-m-d H:i:s',time() - 3600),
            'content'  => 'monitoring_status/index'
        );

		$this->load->view('default', $data);
    }

    public function get_data(){
        validate_ajax();
        $this->form_validation->set_rules('start_date', 'start_date', 'trim|required|callback_validate_date_time_format', array('validate_date_time_format' => 'Invalid format tanggal tanggal mulai'));
        $this->form_validation->set_rules('end_date', 'end_date', 'trim|required|callback_validate_date_time_format', array('validate_date_time_format' => 'Invalid format tanggal tanggal akhir'));
        $this->form_validation->set_rules('server_id', 'server_id', 'trim|required|alpha_numeric|max_length[4]');
        $this->form_validation->set_rules('tbl_name', 'tbl_name', 'trim|required|callback_special_char', array('special_char' => 'tbl_name memuat invalid karakter'));
        if ($this->form_validation->run() == FALSE) 
        {
            echo $res = json_api(0, validation_errors(),[]);
            exit;
        }
        $json = $this->mstatus->get_count_data();

        $json["tokenHash"] = $this->security->get_csrf_hash();
        $json["csrfName"] = $this->security->get_csrf_token_name();
        echo json_encode($json);
    }

    private function create_option_server_id(){
        $html = '';
        $data = $this->mstatus->server_local();
        foreach($data as $key => $val){
            $html .= '<option value="'.$val.'">'.$key.'</option>';
        }

        return $html;
    }

    private function create_option_table_name(){
        $html = '';
        $data = $this->mstatus->get_table();
        foreach($data as $key => $val){
            $html .= '<option value="'.$val.'">'.$val.'</option>';
        } 
        return $html;
    }

    

}
