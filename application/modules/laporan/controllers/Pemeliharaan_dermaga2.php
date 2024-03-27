<?php

error_reporting(0);
class Pemeliharaan_dermaga extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('pemeliharaan_dermaga_model', 'model');
        $this->load->model('global_model');
		$this->_module   = 'laporan/pemeliharaan_dermaga';
		$this->load->library('Html2pdf');

	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->model->dataList();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' 			=> 'Beranda',
			'url_home' 		=> site_url('home'),
			'title' 		=> 'Pemeliharaan Dermaga',
			'content' 		=> $this->_module.'/index',
            'url_datatables'=> current_url(),
			'branch'		=> $this->global_model->select_data("app.t_mtr_branch","where status not in (-5) order by branch_name asc")->result(),
            'shift'         => $this->global_model->select_data("app.t_mtr_shift","where status not in (-5) order by shift_name asc")->result()
		);

		$this->load->view ('default', $data);
	}

	public function detail($assignment_code=''){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $code=$this->enc->decode($assignment_code);
        if(!$code){
        	$this->load->view('error_404');
        	return false;
        }

        $data['home']     	= 'Home';
        $data['url_home'] 	= site_url('home');
        $data['title'] 		= 'Detail';
        $data['content']  	= $this->_module.'/detail_modal';
        $data['detail_trip']   = $this->model->detail_trip(" where up.assignment_code = '$code' ")->row();
        $data['detail_passenger']   = $this->model->list_detail_passanger(" where ob.assignment_code = '$code' ")->result();
        $data['sub_total_passenger']   = $this->model->sub_total_passanger(" where ob.assignment_code = '$code' ")->row();
        $data['detail_vehicle']  = $this->model->list_detail_vehicle(" where ob.assignment_code = '$code' ")->result();
        $data['sub_total_vehicle']   = $this->model->sub_total_vehicle(" where ob.assignment_code = '$code' ")->row();
        $data['report_title'] = "FORMULIR LAPORAN PENDAPATAN PEMELIHARAAN DERMAGA PELABUHAN PER-SHIFT";

        $this->load->view($this->_module.'/detail_modal',$data); 
    }

	function download_pdf($assignment_code=''){
        $this->global_model->checkAccessMenuAction($this->_module,'download_pdf');

        $code=$this->enc->decode($assignment_code);
        if(!$code){
        	$this->load->view('error_404');
        	return false;
        }

        $data['home']     	= 'Home';
        $data['url_home'] 	= site_url('home');
        $data['title'] 		= 'Detail';
        $data['content']  	= $this->_module.'/detail_modal';
        $data['detail_trip']   = $this->model->detail_trip(" where up.assignment_code = '$code' ")->row();
        $data['detail_passenger']   = $this->model->list_detail_passanger(" where ob.assignment_code = '$code' ")->result();
        $data['sub_total_passenger']   = $this->model->sub_total_passanger(" where ob.assignment_code = '$code' ")->row();
        $data['detail_vehicle']  = $this->model->list_detail_vehicle(" where ob.assignment_code = '$code' ")->result();
        $data['sub_total_vehicle']   = $this->model->sub_total_vehicle(" where ob.assignment_code = '$code' ")->row();
        $data['report_title'] = "FORMULIR LAPORAN PENDAPATAN PEMELIHARAAN DERMAGA PELABUHAN PER-SHIFT";

        $this->load->view($this->_module.'/pdf',$data);
	}
}
