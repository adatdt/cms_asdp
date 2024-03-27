<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Shift_balance extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('shift_balance_model');
        $this->load->model('global_model');

        $this->_table    = 'app.t_mtr_port';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'shift_management/shift_balance';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->port_model->portList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Opening Balance',
            'content'  => 'shift_balance/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $data['title'] = 'Tambah Opening Balance';
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
    }

}
