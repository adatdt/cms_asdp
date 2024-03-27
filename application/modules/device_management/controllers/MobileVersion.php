<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );


class MobileVersion extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('mobileVersionModel','device');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_version';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'device_management/mobileVersion';

        // $this->dbView =$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
	}

    /*
        Document   : Pelabuhan
        Created on : 10 juli, 2023
        Author     : soma
        Description: Enhancement pasca angleb 2023
    */


	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->device->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Tipe Versi',
            'content'  => 'mobileVersion/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'btn_excel'=> generate_button_new($this->_module, 'import_excel',  site_url($this->_module.'/import_excel')),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Tipe Versi';

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $this->form_validation->set_rules('type', 'Tipe Versi', 'trim|required|max_length[30]');
        $this->form_validation->set_rules('version', 'Versi', 'trim|required|max_length[10]');
        $this->form_validation->set_rules('description', 'Keterangan', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!');


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else
        {
            $type=trim($this->input->post('type'));
            $version=trim($this->input->post('version'));
            $description=trim($this->input->post('description'));
            $type2=str_replace(" ","_",$type);

            $check =$this->device->select_data($this->_table," where upper(type)=upper('".$type2."') and version='{$version}' and status<>'-5' ");

            $data=array(
                        'type'=>strtoupper($type2),
                        'version'=>$version,
                        'description'=>$description,
                        'status'=>1,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata('username'),
                        );
            
            if($check->num_rows()>0)
            {
                echo $res=json_api(0, 'Nama sudah ada');   
            }
            else
            {
                $this->db->trans_begin();
                $this->device->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'device_management/device_type/mobileVersion';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($id);

        $data['title'] = 'Edit Tipe Perangkat';
        $data['detail']=$this->device->select_data($this->_table,"where id='".$id."' ")->row();
        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $this->form_validation->set_rules('id', 'Tipe Versi', 'required');
        $this->form_validation->set_rules('type', 'Tipe Versi', 'trim|required|max_length[30]');
        $this->form_validation->set_rules('version', 'Versi', 'trim|required|max_length[10]');
        $this->form_validation->set_rules('description', 'Keterangan', 'trim|required');


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else
        {
            $id=$this->enc->decode($this->input->post('id'));

            $type=trim($this->input->post('type'));
            $version=trim($this->input->post('version'));
            $description=trim($this->input->post('description'));
            $type2=str_replace(" ","_",$type);

            $check =$this->device->select_data($this->_table," where upper(type)=upper('".$type2."') and version='{$version}' and status<>'-5' and id<>'{$id}' ");

                 $data=array(
                        'type'=>strtoupper($type2),
                        'version'=>$version,
                        'description'=>$description,
                        'updated_on'=>date("Y-m-d H:i:s"),
                        'updated_by'=>$this->session->userdata('username'),
                        );

                        if($check->num_rows()>0)
                        {
                            echo $res=json_api(0, 'Data sudah ada');
                        }
                        else
                        {
                            $this->db->trans_begin();
                            $this->device->update_data($this->_table,$data,"id=$id");
                
                            if ($this->db->trans_status() === FALSE)
                            {
                                $this->db->trans_rollback();
                                echo $res=json_api(0, 'Gagal edit data ');
                            }
                            else
                            {
                                $this->db->trans_commit();
                                echo $res=json_api(1, 'Berhasil edit data');
                            }   
                        }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'device_management/mobileVersion/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');
        $id = $this->enc->decode($id);

        $data = [];
        if(!$id || empty($id)){
            echo $res=json_api(0, 'Gagal delete data');            
        }
        else{

            $data=array(
                'status'=>-5,
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
                );



            $this->db->trans_begin();
            $this->device->update_data($this->_table,$data,"id=".$id);

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
        $logUrl      = site_url().'device_management/mobileVersion/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $data = [];

        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal update status');
            
        }
        else{
            $d = explode('|', $p);

            /* data */
            $data = array(
                'status' => $d[1],
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );


            $this->db->trans_begin();
            $this->device->update_data($this->_table,$data,"id=".$d[0]);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal update status');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil update status');
            }   
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'device_management/mobileVersion/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    /*
        Document   : Pelabuhan
        Created on : 10 juli, 2023
        Author     : soma
        Description: end Enhancement pasca angleb 2023
    */

}
