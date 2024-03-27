<?php

error_reporting(0);

class Ticket_tracking extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('m_ticket_tracking');
		$this->_module   = 'transaction/ticket_tracking';

		$this->dbAction = $this->load->database("dbAction", TRUE);
        // $this->dbView = $this->load->database("dbView", TRUE);		
        $this->dbView = checkReplication();
	}

	public function index($ticket_number = ''){
		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Ticket Tracking',
			'content' => 'ticket_tracking/index',
		);

		if($ticket_number){
			$data['cari'] = $ticket_number;
		}

		$this->load->view ('default', $data);
	}

	public function track(){
		if($this->input->is_ajax_request())
		{
			$searchCari=$this->input->post("searchCari");
			
			if($searchCari=='ticketNumber')
			{
				$rows = $this->m_ticket_tracking->get_ticket();

			}
			else
			{
				$rows = $this->m_ticket_tracking->get_booking_track();
			}
			echo json_encode($rows);
			exit;
		}
	}

	public function detail($ticket_number, $param)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $ticket_number=$this->enc->decode($ticket_number);

		$data['title'] 			= 'Detail Ticket';
		$data['param']			= $param;
		$data['ticket_number']  = $ticket_number;

        $this->load->view($this->_module.'/detail',$data);
	}

	public function data_ticket(){
		if($this->input->is_ajax_request()){
			$data = $this->m_ticket_tracking->get_data_ticket();
			echo json_encode($data);
			exit;
		}
	}

	public function booking(){
		if($this->input->is_ajax_request()){
			$data = $this->m_ticket_tracking->get_booking();
			echo json_encode($data);
			exit;
		}
	}

	public function payment(){
		if($this->input->is_ajax_request()){
			$data = $this->m_ticket_tracking->get_payment();
			echo json_encode($data);
			exit;
		}
	}

	public function check_in(){
		if($this->input->is_ajax_request()){
			$data = $this->m_ticket_tracking->get_check_in();
			echo json_encode($data);
			exit;
		}
	}

	public function refund(){
		if($this->input->is_ajax_request()){
			$data = $this->m_ticket_tracking->get_refund();
			echo json_encode($data);
			exit;
		}
	}	

	public function reschedule(){
		if($this->input->is_ajax_request()){
			$data = $this->m_ticket_tracking->get_reschedule();
			echo json_encode($data);
			exit;
		}
	}		

	public function gate_in(){
		if($this->input->is_ajax_request()){
			$data = $this->m_ticket_tracking->get_gate_in();
			echo json_encode($data);
			exit;
		}
	}

	public function boarding(){
		if($this->input->is_ajax_request()){
			$data = $this->m_ticket_tracking->get_boarding();
			echo json_encode($data);
			exit;
		}
	}

	public function muntah(){
		if($this->input->is_ajax_request()){
			$data = $this->m_ticket_tracking->get_muntah();
			echo json_encode($data);
			exit;
		}
	}

	public function pindah(){
		if($this->input->is_ajax_request()){
			$data = $this->m_ticket_tracking->get_pindah();
			echo json_encode($data);
			exit;
		}
	}
}