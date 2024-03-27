<?php
/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Emoney extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('global_model');
		// $this->load->model('configuration/menu/menu_model');
		$this->load->model('emoney_model');
		$this->load->helper('nutech_helper');
        logged_in();
    }

    public function index(){
		checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
    		$rows = $this->emoney_model->emoneyList();

            echo json_encode($rows);
            exit;
    	}

    	$data = array(
            'home'        => 'Home',
            'url_home'    => site_url('home'),
            'parent'      => 'Booking Management',
            'url_parent'  => '#',
    		'title'       => 'Pembayaran Emoney',
    		'content'     => 'index',
    	);

    	$this->load->view ('default', $data);
    }

}
