<?php
/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Gatein extends MY_Controller {

	public function __construct(){
		parent::__construct();		
		$this->load->model('gatein_model');
    }

    public function index(){
		checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){    		
            $rows = $this->gatein_model->gateInList();
            echo json_encode($rows);
            exit;
    	}

    	$data = array(
    		'home'       => 'Home',
            'url_home'   => site_url('home'),
            'parent'     => 'Booking Management',
            'url_parent' => '#',
    		'title'      => 'Gate In',
    		'content'    => 'index',
			'tab'        =>'passanger'
    	);

    	$this->load->view ('default', $data);
    }
	
	public function gateInVehicleList(){
	    if ($this->input->is_ajax_request()){
    		$rows = $this->gatein_model->gateInVehicleList();

            echo json_encode($rows);
            exit;
    	}
	}

}
