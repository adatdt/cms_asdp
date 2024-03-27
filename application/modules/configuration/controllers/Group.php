<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/**
 * -------------------
 * CLASS NAME : Group
 * -------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Group extends MY_Controller {

	public function __construct() {
		parent::__construct();
        logged_in();

		$this->load->model('group_model');
        $this->_table    = 'core.t_mtr_user_group';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'configuration/group';
	}

	public function index() {
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->group_model->groupList();
            echo json_encode($rows);
            exit;
        }

		$data = array(
	        'home'        => 'Home',
	        'url_home'    => site_url('home'),
	        'parent'      => 'Konfigurasi Sistem',
	        'url_parent'  => '#',
	        'title'       => 'Grup',
	        'content'     => 'group/index',
            'btn_add'     => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
        );

		$this->load->view ( 'default', $data );
	}

	public function add(){
		validate_ajax();
        $data['title'] = 'Tambah Grup';
        $this->load->view($this->_module.'/add',$data);
	}

	public function action_add(){
        validate_ajax();
        $post = $this->input->post();
        $name = strtoupper($post['name']);
        
        /* validation */
        $this->form_validation->set_rules('name', 'Nama Grup', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'nama grup memuat invalid karakter'));
        $this->form_validation->set_message('required','%s harus diisi!');

        /* data post */
        $data = array(
            'name' => $name
        );

        if($this->form_validation->run() == FALSE){
            $response = json_api(0,validation_errors());
        }else{
            $check = $this->global_model->checkData($this->_table,array('UPPER(name)' => $name));
            if($check){
                $response =  json_api(0,'Nama Grup '.$post['name'].' Sudah Ada'); 
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
        $data['title'] = 'Edit Grup';
        $this->load->view($this->_module.'/edit',$data);
    }

    public function action_edit(){
        validate_ajax();
        $post = $this->input->post();
        $name = strtoupper($post['name']);

        /* validation */
        $this->form_validation
        ->set_rules('id', 'ID Grup', 'trim|required')
        ->set_rules('name', 'Nama Grup', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'nama grup memuat invalid karakter'));
        $this->form_validation->set_message('required','%s harus diisi!');

        $id = $this->enc->decode($post['id']);
        /* data post */
        $data = array(
        	'id' => $id,
            'name' => $name
        );

        if($this->form_validation->run() == FALSE){
            $response = json_api(0,validation_errors());
        }else{
            $check = $this->global_model->checkData(
                $this->_table,
                array('UPPER(name)' => $name),
                'id',$id
            );
            if($check){
                $response =  json_api(0,'Nama Grup '.$post['name'].' Sudah Ada'); 
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
