<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Email extends MY_Controller
{

	public function __construct(){
		parent::__construct();
        $this->load->model('email_model');
        $this->_table    = 'core.t_trx_email';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'email';
	}
	
	public function index(){
        checkUrlAccess(uri_string(),'view');
		if ( $this->input->is_ajax_request() ) {
            $rows = $this->email_model->emailList();

            echo json_encode($rows);
            exit;
        }
		
		$data = array(
	        'home'          => 'Home',
	        'url_home'      => site_url('home'),
	        'title'         => 'Email',
	        'content'       => 'index',
        );
		
		$this->load->view('default',$data);
	}
	
	public function detail($param){
        checkUrlAccess($this->_module,'detail');
		$id = $this->enc->decode($param);
		
		$data = array(
			'home'          => 'Home',
			'url_home'      => site_url('home'),
			'parent1'		=> 'Email',
			'url_parent1'	=> site_url('email'),
			'title'         => 'Detail Email',
			'email'			=> $this->email_model->getData('core.t_trx_email', 'id='.$id)->row(),
			'cc'			=> $this->email_model->getData('core.t_trx_email_cc', 'email_id='.$id)->result(),
			'bc'			=> $this->email_model->getData('core.t_trx_email_bcc', 'email_id='.$id)->result(),
			// 'content'       => 'detail',
		);
			
		// $this->load->view('default',$data);

		$this->load->view('detail',$data);
	}

	public function change_status($param)
	{
        validate_ajax();
        $id = $this->enc->decode($param);

        /* data */
        $data = 
	        array(
	            'status' =>0,
	            'updated_by'=>$this->session->userdata("username"),
	            'updated_on'=>date('Y-m-d H:i:s'),
	        );

	    $this->db->trans_begin();
        $this->email_model->update_data($this->_table, $data, "id=$id");

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $response=json_api(0, 'Gagal kirim ulang');
        }
        else
        {
            $this->db->trans_commit();
            $response=json_api(1, 'Berhasil kirim ulang email');
        }

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'delete', json_encode($data), $response); 
        echo $response;
    }

	// public function change_status($param){
 //        validate_ajax();
 //        $id = $this->enc->decode($param);

 //        /* data */
 //        $data = array(
 //            'id' => $id,
 //            'status' => 0
 //        );

 //        $query = $this->global_model->updateData($this->_table, $data, 'id');
 //        if($query){
 //            $response = json_api(1,'Ganti status berhasil');
 //        }else{
 //            $response = json_encode($this->db->error()); 
 //        }
 //        $this->log_activitytxt->createLog($this->_username, uri_string(), 'delete', json_encode($data), $response); 
 //        echo $response;
 //    }


}
