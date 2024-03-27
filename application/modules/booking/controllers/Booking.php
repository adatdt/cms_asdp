<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Booking extends MY_Controller
{

	public function __construct(){
		parent::__construct();
        $this->load->model('booking_model');
        $this->_module   = 'booking';
	}

	public function index(){
        checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
            $rows = $this->booking_model->bookingList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
	        'home'          => 'Home',
	        'url_home'      => site_url('home'),
	        'parent'        => 'Booking Management',
	        'url_parent'    => '#',
	        'title'         => 'Booking',
	        'content'       => 'index',
        );

		$this->load->view('default', $data);
	}
	
	public function detail($param){
        checkUrlAccess($this->_module,'detail');
		$id = $this->enc->decode($param);
		
		$idbooking = $this->booking_model->getById($id)->row();
	
		if ($idbooking->service_id == 1){
			$data = array(
			'home'          => 'Home',
			'url_home'      => site_url('home'),
			'parent'		=> 'Booking Management',
			'url_parent'	=> '#',
			'parent1'		=> 'Booking',
			'url_parent1'	=> site_url('booking'),
			'title'         => 'Detail Booking Penumpang',
			'detail'		=> $this->booking_model->getDetail($id)->row(),
			'booking_passanger' =>$this->booking_model->getDetailPassanger($id)->result(),
			'pssgr'	=> $this->booking_model->getNamePassanger($id)->result(),
			'content'       => 'detail',
			);
			
		}else{
			$data = array(
			'home'          => 'Home',
			'url_home'      => site_url('home'),
			'parent'		=> 'Booking Management',
			'url_parent'	=> '#',
			'parent1'		=> 'Booking',
			'url_parent1'	=> site_url('booking'),
			'title'         => 'Detail Booking Kendaraan',
			'detail'		=> $this->booking_model->getDetail($id)->row(),
			'booking_vehicle'=> $this->booking_model->getVehicleClass($id)->result(),
			'booking_vehicle2'=>$this->booking_model->getBookingVehicle($id)->result(),
			'pssgr'	=> $this->booking_model->getNamePassanger($id)->result(),
			'content'       => 'detail_vehicle',
			);
		}
		
		$this->load->view('default', $data);
	}
}