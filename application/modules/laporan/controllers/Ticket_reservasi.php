<?php

class Ticket_reservasi extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('ticket_reservasi_model','m_reservasi');
		$this->_module   = 'laporan/ticket_reservasi';
	}

	public function index() {
		error_reporting(0);
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->m_reservasi->list_data();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Pendapatan Tiket Reservasi',
			'content' => 'ticket_reservasi/index',
			'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view('default', $data);
	}

	function detail($param) {
		checkUrlAccess($this->_module,'detail');
		$param_decoded = $this->enc->decode($param);
		$exp = explode('|', $param_decoded);
		$tanggal = $exp[0];
		$id_origin = $exp[1];
		$id_destination = $exp[2];
		// echo $exp[0];exit;
		// $data_detail = $this->m_reservasi->list_detail($tanggal,$id_origin,$id_destination);

		// echo json_encode($data_detail);exit;

		// echo $exp[0];
		// echo "<br>";
		// echo $exp[1];exit;

		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Rincian Pendapatan Tiket Reservasi',
			'content' => 'ticket_reservasi/detail',
			'payment_date' => $tanggal,
			'param' => $param,
			'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module,'download_excel'),
		);
		
		$this->load->view('default', $data);
	}
	
	function get_detail_list($param) {
		validate_ajax();
		$param_decoded = $this->enc->decode($param);
		$exp = explode('|', $param_decoded);

		$tanggal = $exp[0];
		$id_origin = $exp[1];
		$id_destination = $exp[2];

		$data = $this->m_reservasi->list_detail($tanggal,$id_origin,$id_destination);
		echo json_encode($data);
	}

}