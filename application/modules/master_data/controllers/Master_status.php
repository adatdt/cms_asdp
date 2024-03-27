<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Master_status extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_master_status','status');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_status';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/master_status';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->status->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master Status',
            'content'  => 'master_status/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Status';

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $tbl_name=trim($this->input->post('tbl_name'));
        $status=trim($this->input->post('status'));
        $description=trim($this->input->post('description'));

        $this->form_validation->set_rules('tbl_name', 'Nama Tabel ', 'required');
        $this->form_validation->set_rules('status', 'Kode Status ', 'required|integer');
        $this->form_validation->set_rules('description', 'Keterangan ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');


        
        $data=array(
                    'tbl_name'=>$tbl_name,
                    'description'=>$description,
                    'status'=>$status,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // ceck data jika nama tabel dengan status sama sudah ada
        $check=$this->status->select_data($this->_table," where upper(tbl_name)=upper('".$tbl_name."') and status={$status} ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama tabel dan status sudah ada .");
        }
        else
        {

            $this->db->trans_begin();

            $this->status->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/master_status/action_add';
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

        $data['title'] = 'Edit Status';
        $data['id'] = $id;
        $data['detail']=$this->status->select_data($this->_table,"where id=$id_decode")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }


    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id_data')); 
        $tbl_name=trim($this->input->post('tbl_name'));
        $status=trim($this->input->post('status'));
        $description=trim($this->input->post('description'));

        $this->form_validation->set_rules('tbl_name', 'Nama Tabel ', 'required');
        $this->form_validation->set_rules('status', 'Kode Status ', 'required|integer');
        $this->form_validation->set_rules('description', 'Keterangan ', 'required');
        $this->form_validation->set_rules('id_data', 'Nama Tabel ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');

        $data=array(
                    'tbl_name'=>$tbl_name,
                    'description'=>$description,
                    'status'=>$status,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        // ceck data jika nama tabel dengan status sama sudah ada
        $check=$this->status->select_data($this->_table," where upper(tbl_name)=upper('".$tbl_name."') and status={$status} and id !={$id} ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if ($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama tabel dan status sudah ada .");
        }
        else
        {

            $this->db->trans_begin();

            $this->status->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'master_data/master_status/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }



    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        // full delete permanent tidak ganti flag

        $id = $this->enc->decode($id);

        $data=array('id'=>$id);

        $this->db->trans_begin();
        $this->status->delete_data($this->_table," id='".$id."'");

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
        $logUrl      = site_url().'master_data/master_status/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
