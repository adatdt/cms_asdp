<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Refund extends MY_Controller
{

	public function __construct(){
		parent::__construct();
		$this->load->model('refund_model');
        $this->_module   = 'refund';
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()) {
			$rows = $this->refund_model->refundList();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home'        => 'Home',
            'url_home'    => site_url('home'),
            'parent'      => 'Booking Management',
            'url_parent'  => '#',
			'title'         => 'Refund',
			'content'       => 'index',
		);

		$this->load->view('default', $data);
	}
	
	public function detail($param){
        checkUrlAccess($this->_module,'detail');
        $exp = explode('|', $this->enc->decode($param));
		$data = array(
			'home'        => 'Home',
	        'url_home'    => site_url('home'),
	        'parent1'     => 'Booking Management',
	        'url_parent1' => '#',
	        'parent2'     => 'Refund',
	        'url_parent2' => site_url('refund'),
			'title'       => 'Detail Refund',
			'service'     => $exp[1],
			'header'	  => $this->refund_model->getDetail($exp[0])->row(),
			'detail'	  => $this->refund_model->getDetailRefund($exp[0])->result(),
			'content'     => 'detail',
		);
		$this->load->view('default', $data);
	}
}
