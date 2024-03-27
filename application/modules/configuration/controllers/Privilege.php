<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Privilege extends MY_Controller {

	public function __construct() {
		parent::__construct ();
		logged_in ();
		$this->load->model('privilege_model');
	}

	public function index(){	
        checkUrlAccess(uri_string(),'view');
		if ($this->input->is_ajax_request()) {
			$rows = $this->privilege_model->privilegeList();
			echo json_encode ( $rows );
			exit ();
		}

		$data = array(
	        'home'       => 'Home',
	        'url_home'   => site_url('home'),
	        'parent'     => 'Konfigurasi Sistem',
	        'url_parent' => '#',
	        'title'      => 'Hak Akses',
	        'content'    => 'privilege/index',
	        'usergroup'  => $this->list_user_group(),
        );

		$this->load->view ( 'default', $data );
	}

	function get_list(){
    	validate_ajax();
		echo json_encode($this->privilege_model->get_list());
	}

	function list_user_group(){
        $datas      = $this->global_model->selectAll('core.t_mtr_user_group');
        $data['']   = '';

        if($datas){
            foreach($datas as $row){
                $data[$row->id] = strtoupper($row->name);
            }
        }
        
        return $data;
    }

    function action_privilege(){
    	// print_r($this->input->post());exit;
    	// echo json_api(2,'Silahkan pilih salah satu action');exit;

    	$post  		  = $this->input->post();
    	$t_privilege  = 'core.t_mtr_privilege_web';
    	$insert 	  = array();
    	$update 	  = array();
    	$privilege 	  = array();
    	$arr 		  = array();
    	$check 		  = $this->privilege_model->privilege_web_by_group($post['group_id']);

    	if(empty($post['actions'])){
    		if($check){
				$data = array(
		    		'status' => 0,
			    	'user_group_id' => $post['group_id']
			    );
			    $this->global_model->updateData($t_privilege, $data, 'user_group_id');
				$response = json_api(1,'Set Privilege Berhasil');
    		}else{
				$response = json_api(0,'Silahkan pilih salah satu action');
    		}
    	}else{
    		foreach ($post['actions'] as $key => $value) {
    			if(is_array($post['actions'][$key])){
    				$privilege[] = $value['privilege_id'];

	    			if($value['privilege_id'] == 0){
		    			$insert[] = array(
		    				'user_group_id' => $post['group_id'],
		    				'menu_id' => $value['menu_id'],
		    				'menu_detail_id' => $value['detail_id']
		    			);
		    		}else{
		    			if($value['status'] == 0){
		    				$update[] = array(
		    					'status' => 1,
			    				'id' => $value['privilege_id']
			    			);
		    			}
		    		}
    			} 		
	    	}

	    	foreach ($check as $key => $value) {
	    		$arr[] = $value->id;
	    	}

	    	$diff = array_diff($arr,$privilege);

	    	if($diff){
	    		foreach ($diff as $val) {
	    			$data = array(
	    				'status' => 0,
			    		'id' => $val
	    			);
	    			$this->global_model->updateData($t_privilege, $data, 'id');
	    		}
	    	}

	    	if($insert){
	    		$this->global_model->insertBatch($t_privilege, $insert);
	    	}

	    	if($update){
	    		$this->global_model->updateBatch($t_privilege, $update, 'id');
	    	}

			$response = json_api(1,'Set Privilege Berhasil');
    	}

    	echo $response;

    	/* log-file */
    	$created_by = $this->session->userdata('username');   
    	$data = array('group_id'=>$post['group_id']);
    	$this->log_activitytxt->createLog($created_by, uri_string(), 'set_privilege', json_encode($post), $response); 
    }
}
