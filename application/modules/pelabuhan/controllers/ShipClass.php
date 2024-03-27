<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class ShipClass extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('shipClassModel','shipClass');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_ship_class';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pelabuhan/shipClass';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->shipClass->dataList();
            $rows["tokenHash"] = $this->security->get_csrf_hash();
            $rows["csrfName"] = $this->security->get_csrf_token_name();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Layanan',
            'content'  => 'shipClass/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Layanan';

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $this->form_validation->set_rules('name', 'Nama Kelas', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'Nama kelas memuat invalid karakter'));

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');        
        if($this->form_validation->run()==false)
        {
            echo $res=json_api(0,validation_errors());
            exit;
        }
        $name=trim($this->input->post('name'));

   
        
        $data=array(
                    'name'=>$name,
                    'status'=>1,
                    'created_on'=>date('Y-m-d H:i:s'),
                    'created_by'=>$this->session->userdata("id"), // bawaan poc masih pakai user id 
                    );

        $checkDataName=$this->shipClass->select_data("app.t_mtr_passanger_type", " where upper(name)=upper('{$name}') and  status !='-5' ")->row();
        

        if(count((array)$checkDataName)>0)
        {
            echo $res=json_api(0,"Nama sudah ada");   
        }        
        else
        {

            $this->db->trans_begin();

            $this->shipClass->insert_data($this->_table,$data);

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



         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/shipClass/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);

        $data['title'] = 'Edit Layanan';
        $data['id']=$id;
        $data['detail']=$this->shipClass->select_data($this->_table," where id=".$id_decode." ")->row();

        $this->load->view($this->_module.'/edit',$data);
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $this->form_validation->set_rules('id', 'id', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid id'));
        $this->form_validation->set_rules('name', 'Nama Kelas', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'Nama kelas memuat invalid karakter'));

        $this->form_validation->set_message('required','%s harus diisi!');
        if($this->form_validation->run() == FALSE)
        {
            echo $res=json_api(0,validation_errors());
            exit;
        }

        $name=trim($this->input->post('name'));
        $id=$this->enc->decode($this->input->post("id"));


        
        $data=array(
                    'name'=>$name,
                    'updated_on'=>date("Y-m-d H:i:s"),
                    'updated_by'=>$this->session->userdata("id"), // bawaan poc masih pakai user id 
                    );

        // print_r($data); exit;

        $checkDataName=$this->shipClass->select_data($this->_table, " where upper(name)=upper('{$name}') and   id!={$id} ")->row();


        if(count((array)$checkDataName)>0)
        {
            echo $res=json_api(0,"Nama sudah ada");
        }        
        else
        {

            $this->db->trans_begin();

            $this->shipClass->update_data($this->_table,$data, " id={$id} ");

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

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/shipClass/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('id'), // masih bawaan poc
        );


        $this->db->trans_begin();
        $this->shipClass->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            if ($d[1]==1)
            {
                echo $res=json_api(0, 'Gagal aktif');
            }
            else
            {
                echo $res=json_api(0, 'Gagal non aktif');
            }
            
        }
        else
        {
            $this->db->trans_commit();
            if ($d[1]==1)
            {
                echo $res=json_api(1, 'Berhasil aktif data');
            }
            else
            {
                echo $res=json_api(1, 'Berhasil non aktif data');
            }
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/shipClass/action_change';
        $d[1]==1?$logMethod='DISABLE':$logMethod='ENABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('id'), // masih bawaan poc pake id
            );

        $id = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->shipClass->update_data($this->_table,$data," id='".$id."'");

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

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/shipClass/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


}
