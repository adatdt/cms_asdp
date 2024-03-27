<?php
/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Checkin extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('checkin_model');
    }

    public function index(){
		checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
    		$rows = $this->checkin_model->checkInList();
            echo json_encode($rows);
            exit;
    	}

    	$data = array(
            'home'        => 'Home',
            'url_home'    => site_url('home'),
            'parent'      => 'Booking Management',
            'url_parent'  => '#',
    		'title'       => 'Check in',
    		'content'     => 'index',
    	);

    	$this->load->view ('default', $data);
    }

}
