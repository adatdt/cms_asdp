<?php

error_reporting(0);
class Pendapatan_masuk_pelabuhan extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('pendapatan_masuk_pelabuhan_model', 'pendapatan_masuk');
        $this->load->model('global_model');
		$this->_module   = 'laporan/pendapatan_masuk_pelabuhan';
		$this->load->library('Html2pdf');

	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->pendapatan_masuk->dataList();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' 			=> 'Beranda',
			'url_home' 		=> site_url('home'),
			'title' 		=> 'Pendapatan Pas Masuk Pelabuhan Per-shift',
			'content' 		=> 'pendapatan_masuk_pelabuhan/index',
			'branch'		=> $this->global_model->select_data("app.t_mtr_branch","where status in (1) order by branch_name asc")->result(),
            'shift'         => $this->global_model->select_data("app.t_mtr_shift","where status in (1) order by shift_name asc")->result(),
			// 'download_pdf' 	=> checkBtnAccess($this->_module,'download_pdf'),
			// 'download_excel'=> checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	public function detail($assignment_code,$ship_class_id){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $ship_class_id=$this->enc->decode($ship_class_id);
        $code=$this->enc->decode($assignment_code);
        $data['home']     	= 'Home';
        $data['url_home'] 	= site_url('home');
        $data['title'] 		= 'Detail';
        $data['content']  	= 'pendapatan_masuk_pelabuhan/detail_modal';
        $data['detail_trip']   = $this->pendapatan_masuk->detail_trip(" where up.assignment_code = '$code' ",$ship_class_id)->row();
        $data['detail_passenger']   = $this->pendapatan_masuk->list_detail_passanger(" where ob.assignment_code = '$code' ",$ship_class_id)->result();
        $data['sub_total_passenger']   = $this->pendapatan_masuk->sub_total_passanger(" where ob.assignment_code = '$code' ",$ship_class_id)->row();
        $data['detail_vehicle']  = $this->pendapatan_masuk->list_detail_vehicle(" where ob.assignment_code = '$code' ",$ship_class_id)->result();
        $data['sub_total_vehicle']   = $this->pendapatan_masuk->sub_total_vehicle(" where ob.assignment_code = '$code' ",$ship_class_id)->row();

        $this->load->view($this->_module.'/detail_modal',$data); 
    }

	function download_pdf(){
        // $this->load->library('pdf');
        if (!$this->input->get('id')) {
            redirect('/');
        } else {
            $ship_class_id = $this->enc->decode($this->input->get('ship_class_id'));
            $code = $this->enc->decode($this->input->get('id'));

            if ($this->pendapatan_masuk->detail_trip(" where up.assignment_code = '$code' ")->num_rows() > 0) {
                $data['title'] = 'FORMULIR LAPORAN PENDAPATAN PAS MASUK PELABUHAN PER-SHIFT';
                $data['detail_trip']   = $this->pendapatan_masuk->detail_trip(" where up.assignment_code = '$code' ",$ship_class_id)->row();
                $data['detail_passenger']   = $this->pendapatan_masuk->list_detail_passanger(" where ob.assignment_code = '$code' ",$ship_class_id)->result();
                $data['sub_total_passenger']   = $this->pendapatan_masuk->sub_total_passanger(" where ob.assignment_code = '$code' ",$ship_class_id)->row();
                $data['detail_vehicle']  = $this->pendapatan_masuk->list_detail_vehicle(" where ob.assignment_code = '$code' ",$ship_class_id)->result();
                $data['sub_total_vehicle']   = $this->pendapatan_masuk->sub_total_vehicle(" where ob.assignment_code = '$code' ",$ship_class_id)->row();

                $this->load->view($this->_module.'/pdf',$data);
            } else {
                redirect('/');
            }
        }
    }
}
