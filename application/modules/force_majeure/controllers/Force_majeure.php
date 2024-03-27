<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Force_majeure extends MY_Controller{
	public function __construct(){
		parent::__construct();

		logged_in();
		$this->load->model('force_majeure_model');
        $this->_username  = $this->session->userdata('username');
        $this->_module    = 'force_majeure';
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->force_majeure_model->forceList();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home'    => 'Home',
			'url_home'=> site_url('home'),
			'title'   => 'Force Majeure',
			'content' => 'index',
            'btn_add' => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
		);

		$this->load->view('default', $data);
	}

	public function add() {   
        validate_ajax();
        $data['title'] = 'Tambah Force Majeure';
        $this->load->view($this->_module.'/add',$data);
	}
	
	public function detail($param){
        checkUrlAccess($this->_module,'detail');
        $id = $this->enc->decode($param);

		$data = array(
			'home'        => 'Home',
	        'url_home'    => site_url('home'),
	        'parent1'     => 'Booking Management',
	        'url_parent1' => '#',
	        'parent2'     => 'Force Majeure',
	        'url_parent2' => site_url('force_majeure'),
			'title'       => 'Detail Force Majeure',
			'header'	  => $this->force_majeure_model->getDetail($id)->row(),
			'detail'	  => $this->force_majeure_model->getDetailForce($id)->result(),
			'content'     => 'detail',
		);

		$this->load->view('default', $data);
	}

	public function action_add() {
        validate_ajax();
        $post = $this->input->post();

        /* validation */
        $this->form_validation
            ->set_rules('date', 'Tanggal', 'trim|required')
            ->set_rules('remark', 'Keterangan', 'trim|required');
        $this->form_validation->set_message('required','%s harus diisi!');

        /* data post */
        $data = array(
            'date' => $post['date'],
            'remark' => $post['remark']
        );

        if($this->form_validation->run() == FALSE){
            $response = json_api(0,validation_errors());
        }else{
            $passanger    = $this->force_majeure_model->checkBooking('t_trx_booking_passanger', $post['date'])->result();
            $vehicle      = $this->force_majeure_model->checkBooking('t_trx_booking_vehicle', $post['date'])->result();
            $checkBooking = array_merge($passanger,$vehicle);

            if($checkBooking){
                if($passanger){
                    foreach ($passanger as $key => $value) {
                        $dataPass = array(
                            'status' => -3,
                            'ticket_number' => $value->ticket_number
                        );
                        $this->global_model->updateData('app.t_trx_booking_passanger', $dataPass, 'ticket_number');
                    }
                }

                if($vehicle){
                    foreach ($vehicle as $key => $value) {
                        $dataVe = array(
                            'status' => -3,
                            'ticket_number' => $value->ticket_number
                        );
                    $this->global_model->updateData('app.t_trx_booking_vehicle', $dataVe, 'ticket_number');
                    }
                }

                $id = $this->global_model->saveData('app.t_trx_force_major',$data);

                $listID = array();
                foreach ($checkBooking as $key => $value){
                    $checkTicket = $this->force_majeure_model->checkForceDetail($value->ticket_number)->result();
                    $dataDetail  = array(
                        'force_major_id' => $id,
                        'ticket_number' => $value->ticket_number
                    );

                    $listID[] = $value->booking_id;

                    if(!$checkTicket){
                        $this->global_model->saveData('app.t_trx_force_major_detail',$dataDetail);
                    }
                 }

                foreach (array_unique($listID) as $key => $value) {
                    $dataBooking = array(
                        'status' => -3,
                        'id' => $value
                    );
                    $this->global_model->updateData('app.t_trx_booking', $dataBooking, 'id');
                }

                if($id){
                    $response = json_api(1,'Simpan Data Berhasil');
                }else{
                    $response = json_encode($this->db->error()); 
                }
            }else{
                $response = json_api(0,'Tambah Force Majeure Gagal, tidak Ada Keberangkatan pada Tanggal '.format_date($post['date']).'');
            }
        }

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data), $response); 
        echo $response;

     //    $post = $this->input->post();

     //    $data = array(
     //    	'date' => $post['date'],
     //    	'remark' => $post['remark']
     //    );

     //    $passanger = $this->force_majeure_model->checkBooking('t_trx_booking_passanger', $post['date'])->result();
    	// $vehicle = $this->force_majeure_model->checkBooking('t_trx_booking_vehicle', $post['date'])->result();
    	// $checkBooking = array_merge($passanger,$vehicle);

    	// if($passanger){
    	// 	foreach ($passanger as $key => $value) {
    	// 		$dataPass = array(
    	// 			'status' => -3,
    	// 			'ticket_number' => $value->ticket_number
    	// 		);
    	// 		$this->force_majeure_model->updateData('app.t_trx_booking_passanger', $dataPass, 'ticket_number');
    	// 	}
    	// }

    	// if($vehicle){
    	// 	foreach ($vehicle as $key => $value) {
    	// 		$dataVe = array(
    	// 			'status' => -3,
    	// 			'ticket_number' => $value->ticket_number
    	// 		);
    	// 		$this->force_majeure_model->updateData('app.t_trx_booking_vehicle', $dataVe, 'ticket_number');
    	// 	}
    	// }

     //    if($checkBooking){
     //    	$id = 1;
     //    	$id = $this->force_majeure_model->insertForce('t_trx_force_major',$data);

     //        $listID = array();
     //    	foreach ($checkBooking as $key => $value){
     //    		$checkTicket = $this->force_majeure_model->checkForceDetail($value->ticket_number)->result();
     //    		$dataDetail  = array(
     //    			'force_major_id' => $id,
     //    			'ticket_number' => $value->ticket_number
     //    		);

     //            $listID[] = $value->booking_id;

     //    		if(!$checkTicket){
     //    			$this->force_majeure_model->insertForce('t_trx_force_major_detail',$dataDetail);
     //    		}
     //    	}

     //        foreach (array_unique($listID) as $key => $value) {
     //            $dataBooking = array(
     //                'status' => -3,
     //                'id' => $value
     //            );
     //            $this->force_majeure_model->updateData('app.t_trx_booking', $dataBooking, 'id');
     //        }

     //    	$this->session->set_flashdata('status', '<b> <i class="fa fa-check-circle"></i> Sukses! </b>');
     //      	$this->session->set_flashdata('message', 'Data berhasil ditambah.');
     //    	$res = 'success';
     //    }else{
     //    	$this->session->set_flashdata('status', '<b><i class="fa fa-times-circle"></i> Gagal! </b>');
     //      	$this->session->set_flashdata('message', 'Data tidak ditemukan.');
     //    }
        
     //    $created_by   = $this->session->userdata('username');
     //    $log_url      = site_url().'force_majeure';
     //    $log_method   = 'insert';
     //    $log_param    = json_encode($data);
     //    $log_response = json_encode($res);   

     //    $this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response); 

     //    redirect('force_majeure');
    }
}
