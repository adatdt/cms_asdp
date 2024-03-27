<?php

error_reporting(E_ALL ^ E_WARNING);
class Ship_income extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('ship_income_model');
		$this->_module   = 'laporan/ship_income';
		$this->load->library('Html2pdf');

	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->ship_income_model->shipIncomeList();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Pendapatan Kapal',
			'content' => 'ship_income/index',
			'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	public function detail($param,$sail_id){
		checkUrlAccess($this->_module,'detail');
		// echo $this->enc->decode($ship_id);exit;

		$get = $this->enc->decode($param);
		// echo $get;exit;
		// if($this->input->is_ajax_request()){
		// 	$rows = $this->ship_income_model->newdetail($get);
		// 	echo json_encode($rows);
		// 	exit;
		// }

		$data = array(
			'home'        => 'Home',
			'url_home'    => site_url('home'),
			'parent1'     => 'Laporan',
			'url_parent1' => '#',
			'parent2'     => 'Pendapatan Kapal',
			'url_parent2' => site_url('laporan/ship_income'),
			'title'       => 'Detail Pendapatan Kapal',
			'boarding_code' => $param,
			'header'      => $this->ship_income_model->getDetailHeader($this->enc->decode($sail_id)),
			'url'         => site_url('laporan/ship_income/get_detail_list/'.$param),
			'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module,'download_excel'),
			'content'     => 'ship_income/detail',
		);

		$this->load->view('default', $data);
	}

	function get_detail_list($param) {
		validate_ajax();
		$boarding_code = $this->enc->decode($param);

		$data = $this->ship_income_model->newdetail($boarding_code);
		echo json_encode($data);
	}

	function download_pdf()
	{
		$id=$this->input->get('id');
		$get = $this->enc->decode($id);
		$kendaraan=$this->ship_income_model->get_pdf_kendaraan($get)->result();
		$penumpang=$this->ship_income_model->get_pdf_penumpang($get)->result();

		foreach ($kendaraan as $key => $value) {
			$total_kendaraan[]=$value->production;
		}

		foreach ($penumpang as $key => $value) {
			$total_penumpang[]=$value->production;
		}

		$total_ticket= array_sum($total_kendaraan)+array_sum($total_penumpang);


		$data=array(
			'penumpang'=>$penumpang,
			'kendaraan'=>$kendaraan,
			'total_ticket'=>$total_ticket,
			'nama_kapal'=>$this->input->get('nama_kapal'),
			'trx_date'=>$this->input->get('trx_date'),
		);
		$this->load->view('laporan/ship_income/pdf',$data);
	}
}