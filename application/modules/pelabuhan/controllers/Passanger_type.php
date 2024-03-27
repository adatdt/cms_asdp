<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Passanger_type extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_passanger_type','passanger');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_passanger_type';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pelabuhan/passanger_type';
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
            $rows = $this->passanger->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Tipe Penumpang Pejalan Kaki',
            'content'  => 'passanger_type/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Tipe Penumpang Pejalan Kaki';

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        /* validation */
        $this->form_validation->set_rules('name', 'Nama ', 'trim|required|max_length[50]|callback_special_char', array('special_char' => 'nama mengandung invalid karakter'));
        $this->form_validation->set_rules('code', 'Kode Tipe ', 'trim|required|max_length[10]');
        $this->form_validation->set_rules('description', 'Keterangan ', 'trim|required');
        $this->form_validation->set_rules('maxAge', 'Maximal Umur ', 'trim|required|numeric');
        $this->form_validation->set_rules('minAge', 'Minimal Umur ', 'trim|required|numeric');
        $this->form_validation->set_rules('ordering', 'Urutan ', 'trim|required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!')
                                ->set_message('numeric','%s harus angka!')
                                ->set_message('max_length','{field} Maximal  {param} karakter! ');

        
        $data = null;
        if($this->form_validation->run()==false)
        {
            echo $res=json_api(0,validation_errors());
        } 
        else 
        {
            $name=$this->input->post('name');
            $description=$this->input->post('description');
            $maxAge=$this->input->post('maxAge');
            $minAge=$this->input->post('minAge');
            $ordering=$this->input->post('ordering');
            $code=$this->input->post('code');

            $data=array(
                'name'=>$name,
                'description'=>$description,
                'status'=>1,
                'max_age'=>$maxAge,
                'min_age'=>$minAge,
                'ordering'=>$ordering,
                'code'=>strtolower($code),
                'created_on'=>date('Y-m-d H:i:s'),
                'created_by'=>$this->session->userdata("id"), // bawaan poc masih pakai user id 
                );

                $checkDataName=$this->passanger->select_data("app.t_mtr_passanger_type", " where upper(name)=upper('{$name}') and  status !='-5' ")->row();  
                // check ordering
                $checkOrdering=$this->passanger->select_data("app.t_mtr_passanger_type", " where ordering='{$ordering}' and  status !='-5' ")->row();
                // check Kode
                $checkCode=$this->passanger->select_data("app.t_mtr_passanger_type", " where code='{$code}' and  status !='-5' ")->row();

                if(count((array)$checkDataName)>0)
                {
                    echo $res=json_api(0,"Nama sudah ada");   
                }
                else if(count((array)$checkOrdering)>0)
                {
                    echo $res=json_api(0,"Urutan sudah ada");   
                }
                else if(count((array)$checkCode)>0)
                {
                    echo $res=json_api(0,"Kode sudah ada");
                }        
                else if ($minAge>=$maxAge)            
                {
                    echo $res=json_api(0," Min Umur Harus lebih kecil dari max umur");
                }
                else
                {

                    $this->db->trans_begin();

                    $this->passanger->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'pelabuhan/passanger_type/action_add';
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

        $data['title'] = 'Edit Tipe Penumpang Pejalan Kaki';
        $data['id']=$id;
        $data['detail']=$this->passanger->select_data("app.t_mtr_passanger_type"," where id=".$id_decode." ")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        /* validation */
        // $this->form_validation->set_rules('code', 'Kode Tipe ', 'required');
        $this->form_validation->set_rules('name', 'Nama ', 'trim|required|max_length[50]|callback_special_char', array('special_char' => 'nama mengandung invalid karakter'));
        $this->form_validation->set_rules('description', 'Keterangan ', 'trim|required');
        $this->form_validation->set_rules('maxAge', 'Maximal Umur ', 'trim|required|numeric');
        $this->form_validation->set_rules('minAge', 'Minimal Umur ', 'trim|required|numeric');
        $this->form_validation->set_rules('ordering', 'Urutan ', 'trim|required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!')
                                ->set_message('numeric','%s harus angka!')
                                ->set_message('max_length','{field} Maximal  {param} karakter! ');


        $data = null;
        if($this->form_validation->run() == FALSE)
        {
            echo $res=json_api(0,validation_errors());
        }
        else 
        {
            $name=$this->input->post('name');
            $description=$this->input->post('description');
            $maxAge=$this->input->post('maxAge');
            $minAge=$this->input->post('minAge');
            $ordering=$this->input->post('ordering');
            // $code=trim($this->input->post('code'));
            $id=$this->enc->decode($this->input->post("id"));

            $data=array(
                'name'=>$name,
                'description'=>$description,
                'max_age'=>$maxAge,
                'min_age'=>$minAge,
                'ordering'=>$ordering,
                // 'code'=>strtolower($code),
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata("id"), // bawaan poc masih pakai user id 
                );

                // print_r($data); exit;

                $checkData=$this->passanger->select_data("app.t_mtr_passanger_type", " where id={$id} ")->row();
                $checkDataName=$this->passanger->select_data("app.t_mtr_passanger_type", " where upper(name)=upper('{$name}') and   id!={$id} ")->row();
                $checkOrdering=$this->passanger->select_data("app.t_mtr_passanger_type", " where ordering='{$ordering}' and   id!={$id} ")->row();
                // $checkCode=$this->passanger->select_data("app.t_mtr_passanger_type", " where code='{$code}' and   id!={$id} ")->row();

            if(count((array)$checkData)<1)
            {
                echo $res=json_api(0,"Data tidak ada");
            }
            else if(count((array)$checkDataName)>0)
            {
                echo $res=json_api(0,"Nama sudah ada");
            }        
            else if(count((array)$checkOrdering)>0)
            {
                echo $res=json_api(0,"Urutan sudah ada");
            }                
            // else if(count((array)$checkCode)>0)
            // {
            //     echo $res=json_api(0,"Kode sudah ada");
            // }                        
            else if ($minAge>=$maxAge)            
            {
                echo $res=json_api(0," Min Umur Harus lebih kecil dari max umur");
            }
            else
            {

                $this->db->trans_begin();

                $this->passanger->update_data($this->_table,$data, " id={$id} ");

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
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/passanger_type/action_edit';
        $logMethod   = 'EDIT';
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
            echo $res=json_api(0, 'Gagal update data');            
        }
        else{

            $d = explode('|', $p);

            /* data */
            $data = array(
                'status' => $d[1],
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('id'), // masih bawaan poc
            );


            $this->db->trans_begin();
            $this->passanger->update_data($this->_table,$data,"id=".$d[0]);

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
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/passanger_type/action_change';
        $d[1]==1?$logMethod='DISABLE':$logMethod='ENABLE';
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
                'updated_by'=>$this->session->userdata('id'), // masih bawaan poc pake id
                );


            $this->db->trans_begin();
            $this->passanger->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'pelabuhan/port_branch/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    /*
    Document   : Pelabuhan
    Created on : 28 juli, 2023
    Author     : soma
    Description: end Enhancement pasca angleb 2023
    */ 


}
