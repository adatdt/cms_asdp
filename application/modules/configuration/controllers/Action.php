<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/**
 * -------------------
 * CLASS NAME : Groups
 * -------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Action extends MY_Controller {

	public function __construct() {
		parent::__construct();
        logged_in();

		$this->load->model('action_model');
        $this->_table    = 'core.t_mtr_menu_action';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'configuration/action';
	}

	public function index() {	 
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->action_model->groupList();
            echo json_encode($rows);
            exit;
        }

		$data = array(
	        'home'        => 'Home',
	        'url_home'    => site_url('home'),
	        'parent'      => 'Konfigurasi Sistem',
	        'url_parent'  => '#',
	        'title'       => 'List Aksi',
	        'content'     => 'action/index',
            'btn_add'     => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
        );

		$this->load->view ( 'default', $data );
	}

	public function add(){
		validate_ajax();
        $data['title'] = 'Tambah Aksi';
        $this->load->view($this->_module.'/add',$data);
	}

	public function action_add(){
        validate_ajax();
        $post = $this->input->post();
        $name = strtolower($post['name']);
        
        /* validation */
        $this->form_validation->set_rules('name', 'Nama Grup', 'trim|required|max_length[20]');
        $this->form_validation->set_message('required','%s harus diisi!');

        /* data post */
        $data = array(
            'action_name' => $name
        );

        if($this->form_validation->run() == FALSE){
            $response = json_api(0,validation_errors());
        }else{
            $check = $this->global_model->checkData($this->_table,array('LOWER(action_name)' => $name));
            if($check){
                $response =  json_api(0,'Nama Aksi '.$post['name'].' Sudah Ada'); 
            }else{
                $query = $this->global_model->saveData($this->_table, $data);
                if($query){
                    $response = json_api(1,'Simpan Data Berhasil');
                }else{
                    $response = json_encode($this->db->error()); 
                }
            }
        }

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data), $response); 
        echo $response;
    }

    public function edit($param){
        validate_ajax();
        $data['id']    = $param;
        $data['row']   = $this->global_model->selectById($this->_table, 'id', $this->enc->decode($param));
        $data['title'] = 'Edit Aksi';
        $this->load->view($this->_module.'/edit',$data);
    }

    public function action_edit(){
        validate_ajax();

        /* validation */
        $this->form_validation
        ->set_rules('id', 'ID Aksi', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid ID aksi'))
        ->set_rules('name', 'Nama Aksi', 'trim|required|max_length[20]');
        $this->form_validation->set_message('required','%s harus diisi!');

        

        if($this->form_validation->run() == FALSE){
            $response = json_api(0,validation_errors());
        }else{
            $post = $this->input->post();
            $name = strtolower($post['name']);
            $id = $this->enc->decode($post['id']);
            /* data post */
            $data = array(
                'id' => $id,
                'action_name' => $name
            );
            
            $check = $this->global_model->checkData(
                $this->_table,
                array('LOWER(action_name)' => $name),
                'id',$id
            );
            if($check){
                $response =  json_api(0,'Nama AKsi '.$post['name'].' Sudah Ada'); 
            }else{
                $query = $this->global_model->updateData($this->_table, $data, 'id');
                if($query){
                    $response = json_api(1,'Update Data Berhasil');
                }else{
                    $response = json_encode($this->db->error()); 
                }
            }
        }

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'update', json_encode($data), $response); 
        echo $response;
    }

    public function action_delete($param){
        validate_ajax();
        $id = $this->enc->decode($param);
        
        $data = [];
        if(!$id || empty($id)){
            echo $res=json_api(0, 'Gagal delete data');            
        }
        else{

            /* data */
            $data = array(
                'id' => $id,
                'status' => -5
            );

            $query = $this->global_model->updateData($this->_table, $data, 'id');
            if($query){
                $response = json_api(1,'Delete Data Berhasil');
            }else{
                $response = json_encode($this->db->error()); 
            }

            $this->log_activitytxt->createLog($this->_username, uri_string(), 'delete', json_encode($data), $response); 
            echo $response;
        }
    }
}
