<?php
/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Boarding extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('global_model');
	//	$this->load->model('menu/menu_model');
		$this->load->model('boarding_model');
		$this->load->helper('nutech_helper');
        logged_in();
    }

    public function index()
    {
		 $this->check_access('gatein', 'view');
    	if ($this->input->server('REQUEST_METHOD') == 'POST')
    	{
    		$rows = $this->boarding_model->boardingList();

            echo json_encode($rows);
            exit;
    	}

    	$data = array(
    		'home' => 'Dashboard',
    		'url_home' => site_url(),
    		'title' => 'Boarding',
    		'content' => 'index',
			'tab'=>'passanger',
    	);

    	$this->load->view ('default', $data);
    }
	
	public function vehicleList()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST')
    	{
    		$rows = $this->boarding_model->boardingVehicleList();

            echo json_encode($rows);
            exit;
    	}
	}
	
		public function passangerVehicleList()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST')
    	{
    		$rows = $this->boarding_model->passangerVehicleList();

            echo json_encode($rows);
            exit;
    	}
	}

}
