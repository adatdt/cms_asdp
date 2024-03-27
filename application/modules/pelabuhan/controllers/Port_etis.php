<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Port_etis extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('port_etis_model');
        $this->load->model('global_model');

        $this->_table    = 'app.t_mtr_port_etis';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pelabuhan/port_etis';
	}

    /*
    Document   : Pelabuhan
    Created on : 28 juli, 2023
    Author     : soma
    Description: Enhancement pasca angleb 2023
    */


    public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('searchData')){
                $this->form_validation->set_rules('searchData', 'searchData', 'trim|callback_special_char', array('special_char' => 'search has contain invalid characters'));
            }
            
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->port_etis_model->portEtisList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Pelabuhan Lain',
            'content'  => 'port_etis/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
        );

        $this->load->view('default', $data);
    }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Pelabuhan Lain';
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        /* validation */
        $this->form_validation->set_rules('name', 'Nama Pelabuhan', 'trim|required|max_length[255]|callback_special_char', array('special_char' => 'nama pelabuhan mengandung invalid karakter'));
        $this->form_validation->set_rules('city', 'Nama Provinsi', 'trim|required|max_length[255]|callback_special_char', array('special_char' => 'nama provinsi mengandung invalid karakter'));
        $this->form_validation->set_rules('url', 'Domain URL', 'trim|required');
       

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('max_length','{field} Maximal  {param} karakter! ');
        // $this->form_validation->set_message('url','%s tidak valid!');
        
        $data = null;
        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else
        {
            $post = $this->input->post();
            $name = trim($post['name']);
            $city = trim($post['city']);
            $url  = trim($post['url']);

            /* data post */

            $data = array(
                'name' => $name,
                'city' => $city,
                'url' => $url,
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata('username'),
            );

            $check = $this->global_model->checkData($this->_table,array('(name)' => $name));

            $check = $this->port_etis_model->select_data($this->_table," where (name)='{$name}' and status not in (-5) ");

            // $checkOrdering = $this->port_etis_model->select_data($this->_table,' where "order"='."'{$ordering}'".' and status not in (-5) ');

            if($check->num_rows()>0)
            {
                echo $res =  json_api(0,'Nama Pelabuhan '.$name.' Sudah Ada'); 
            }
        
            else
            {
                $this->db->trans_begin();

                $this->port_etis_model->insert_data($this->_table,$data);

                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                    echo $res=json_api(0, 'Gagal tambah data');
                }
                else
                {
                    $this->db->trans_commit();
                    echo $res=json_api(1, 'Berhasil tambah data');
                }
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/port_etis/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param){
        validate_ajax();
        $p = $this->enc->decode($param);
        $data = [];

        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal update data');
            
        }
        else{
            $d = explode('|', $p);

            /* data */
            $data = array(
                'id' => $d[0],
                'status' => $d[1]
            );

            $query = $this->global_model->updateData($this->_table, $data, 'id');
            if($query){
                $response = json_api(1,'Update Status Berhasil');
            }else{
                $response = json_encode($this->db->error()); 
            }

            $this->log_activitytxt->createLog($this->_username, uri_string(), 'change_status', json_encode($data), $response); 
            echo $response;
        }
    }

    public function action_delete($param){
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $id   = $this->enc->decode($param);

        $data = [];
        if(!$id || empty($id)){
            echo $res=json_api(0, 'Gagal delete data');            
        }
        else{

            /* data */
            $data = array(
                'id' => $id,
                'status' => -5,
                'updated_by'=>$this->session->userdata('username'),
                'updated_on'=>date("Y-m-d H:i:s"),
            );

            $this->db->trans_begin();
            $this->port_etis_model->update_data($this->_table,$data,"id=$id");

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal delete data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil delete data');
            }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/port_etis/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($param){
        validate_ajax();
        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $decode_id=$this->enc->decode($param);
        $checkData=$this->port_etis_model->select_data($this->_table," where id='{$decode_id}' ")->row();

        $data['id']    = $param;
        $data['row']   = $this->global_model->selectById($this->_table, 'id', $this->enc->decode($param));
        $data['title'] = 'Edit Pelabuhan Lain';
        $this->load->view($this->_module.'/edit',$data);
    }

    public function action_edit(){
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $post = $this->input->post();
        $name = trim($post['name']);
        $city = trim($post['city']);
        $url  = trim($post['url']);

        /* validation */
        $this->form_validation->set_rules('id', 'ID Pelabuhan', 'trim|required');
        $this->form_validation->set_rules('name', 'Nama Pelabuhan', 'trim|required|max_length[255]|callback_special_char', array('special_char' => 'nama pelabuhan mengandung invalid karakter'));
        $this->form_validation->set_rules('city', 'Nama Provinsi', 'trim|required|max_length[255]|callback_special_char', array('special_char' => 'nama provinsi mengandung invalid karakter'));
        $this->form_validation->set_rules('url', 'Domain URL', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('max_length','{field} Maximal  {param} karakter! ');

        $id = $this->enc->decode($post['id']);      

        /* data post */
        $data = array(
            'id'   => $id,
            'name' => $name,
            'city' => $city,
            'url'  => $url,
            'updated_on'=>date('Y-m-d H:i:s'),
            'updated_by'=>$this->session->userdata('username'),
        );        

        $check = $this->port_etis_model->select_data($this->_table," where (name)='{$name}' and status not in (-5) and id !={$id} ");

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }elseif($check->num_rows()>0){
            echo $res =  json_api(0,'Nama Pelabuhan '.$post['name'].' Sudah Ada'); 
        }               
        else
        {
            $this->db->trans_begin();

            $this->port_etis_model->update_data($this->_table,$data,"id=$id");

            if ($this->db->trans_status() === FALSE)
            {   
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data');
            }
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/port_etis/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    /*
    Document   : Pelabuhan
    Created on : 28 juli, 2023
    Author     : soma
    Description: Enhancement pasca angleb 2023
    */

}