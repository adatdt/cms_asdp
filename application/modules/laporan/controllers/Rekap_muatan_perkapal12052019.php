<?php

error_reporting(E_ALL ^ E_WARNING);
class Rekap_muatan_perkapal extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('rekap_muatan_perkapal_model', 'rekap_muatan');
        $this->load->model('global_model');
		$this->_module   = 'laporan/rekap_muatan_perkapal';
		$this->load->library('Html2pdf');

	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->rekap_muatan->dataList();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' 			=> 'Beranda',
			'url_home' 		=> site_url('home'),
			'title' 		=> 'Rekap Muatan Per-kapal dan Per-Trip',
			'content' 		=> 'rekap_muatan_perkapal/index',
			'port'			=> $this->global_model->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result(),
            // 'dock'			=>$this->global_model->select_data("app.t_mtr_dock","where status not in (-5) order by name asc")->result(),
			'download_pdf' 	=> checkBtnAccess($this->_module,'download_pdf'),
			'download_excel'=> checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	public function detail($schedule_code){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $code=$this->enc->decode($schedule_code);

        $data['home']     	= 'Home';
        $data['url_home'] 	= site_url('home');
        $data['title'] 		= 'Detail';
        $data['content']  	= 'rekap_muatan_perkapal/detail_modal';
        $data['detail_trip']   = $this->rekap_muatan->detail_trip(" where sail.schedule_code = '$code' ")->row();
        $data['detail_passenger']   = $this->rekap_muatan->list_detail_passanger(" where sail.schedule_code = '$code' ")->result();
        $data['sub_total_passenger']   = $this->rekap_muatan->sub_total_passanger(" where sail.schedule_code = '$code' ")->row();
        $data['detail_vehicle']  = $this->rekap_muatan->list_detail_vehicle(" where sail.schedule_code = '$code' ")->result();
        $data['sub_total_vehicle']   = $this->rekap_muatan->sub_total_vehicle(" where sail.schedule_code = '$code' ")->row();
        $data['dock_fare']   = $this->rekap_muatan->dock_fare(" where schedule.schedule_code = '$code' ")->row();
        $data['adm_fee']   = $this->rekap_muatan->adm_fee(" where param_name = 'adm_fee' and status = 1 ")->row();

        $this->load->view($this->_module.'/detail_modal',$data); 
    }

	function download_pdf(){
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
