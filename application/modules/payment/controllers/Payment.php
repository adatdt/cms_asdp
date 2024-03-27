<?php
/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Payment extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('payment_model');
    }

    public function index(){
		checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
    		$rows = $this->payment_model->invoiceList();
            echo json_encode($rows);
            exit;
    	}

    	$data = array(
    		'home'        => 'Home',
            'url_home'    => site_url('home'),
            'parent'      => 'Booking Management',
            'url_parent'  => '#',
    		'title'       => 'Pembayaran',
    		'content'     => 'index',

    	);

    	$this->load->view ('default', $data);
    }

}
