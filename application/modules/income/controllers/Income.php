<?php
/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Income extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('global_model');
		$this->load->model('menu/menu_model');
		$this->load->model('income_model');
		$this->load->helper('nutech_helper');
        logged_in();
    }

    public function index()
    {
		 $this->check_access('checkin', 'view');
    	if ($this->input->server('REQUEST_METHOD') == 'POST')
    	{
    		$rows = $this->income_model->invoiceList();

            echo json_encode($rows);
            exit;
    	}

    	$data = array(
    		'home' => 'Dashboard',
    		'url_home' => site_url(),
    		'title' => 'Invoice',
    		'content' => 'index',

    	);

    	$this->load->view ('default', $data);
    }

}
