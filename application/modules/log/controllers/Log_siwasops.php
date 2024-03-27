<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Log_siwasops extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_log_siwasops','siwasops');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_log_siwasops';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'log/log_siwasops';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->siwasops->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->siwasops->get_identity_app();
        
        if($get_identity==0)
        {
            // ambil port berdasarkan user
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->siwasops->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id'))->result();
                $row_port=1;
            }
            else
            {
                $port=$this->siwasops->select_data("app.t_mtr_port","where status !='-5' order by name asc")->result();
                $row_port=0;
            }
        }
        else
        {
            $port=$this->siwasops->select_data("app.t_mtr_port","where id=".$get_identity)->result();
            $row_port=1;
        }        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Log Siwasops',
            'content'  => 'log_siwasops/index',
            'port' => $port,
            'row_port' => $row_port,
        );

		$this->load->view('default', $data);
	}

    public function detail($param)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'detail');

        $boarding_code = $param;


        
        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title'] = 'Data Log Siwasops';
        $data['content']  = 'log/log_siwasops/detail_modal';

        $data['data_boarding'] = $this->siwasops->data_boarding($boarding_code)->row();
        $data['data_log'] = $this->siwasops->list_log_siwasops($boarding_code)->result();
        $data['log_count'] = $this->siwasops->list_log_siwasops($boarding_code)->num_rows();

        $this->load->view($this->_module . '/detail_modal', $data);

        // $this->load->view('default',$data);   
    }

}