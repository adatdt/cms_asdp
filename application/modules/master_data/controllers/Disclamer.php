<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
/*
    echnace validation and csrf tocken 24-07-2023
    by adat
*/
class Disclamer extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_disclamer','disclamer');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_info';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/disclamer';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->disclamer->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Info',
            'content'  => 'disclamer/index',
            // 'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'btn_add'  => generate_button($this->_module, 'add', '<button onclick="showModal_new('."'".site_url($this->_module.'/add')."'".')" class="btn btn-sm btn-warning" title="Tambah"><i class="fa fa-plus"></i> Tambah</button> '),
            'url'      => site_url($this->_module.'/add'),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Info';

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $name=trim($this->input->post('name', true));
        // $info=trim($this->input->post('info', true));

        $valinfo=trim($this->input->post('info', true));
        $decinfo=htmlspecialchars_decode((base64_decode($valinfo)));
        $info=rtrim($decinfo,"\n");

        $this->form_validation->set_rules('name', 'Nama Info', 'required|max_length[255]');
        $this->form_validation->set_rules('info', 'Konten Info', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');        

        
        $data=array(
                    'name'=>$name,
                    'info'=>$info,
                    // 'info'=>htmlspecialchars_decode(str_replace("monkey-1777","style",$info)),
                    // 'info'=>htmlspecialchars_decode((base64_decode($info))),
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // check nama jika ada yang sama 
        $check_name=$this->disclamer->select_data($this->_table," where upper(name)=upper(".$this->db->escape($name).") and status not in (-5)");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,  validation_errors());
        }
        else if($check_name->num_rows()>0)
        {
            echo $res=json_api(0, 'Nama sudah ada');   
        }
        else
        {

            $this->db->trans_begin();

            $this->disclamer->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/disclamer/action_add';
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



        $data['title'] = 'Edit Info';
        $data['detail']=$this->disclamer->select_data($this->_table,"where id=$id_decode")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $name=trim($this->input->post('name', true));
        // $info=trim($this->input->post('info', true));

        $valinfo=trim($this->input->post('info', true));
        $decinfo=htmlspecialchars_decode((base64_decode($valinfo)));
        $info=rtrim($decinfo,"\n");

        // print_r($info);exit;

        $id=$this->enc->decode($this->input->post('id', true));
        $_POST['id'] = $id;

        $this->form_validation->set_rules('name', 'Nama Info', 'required|max_length[255]');
        $this->form_validation->set_rules('info', 'Konten Info', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required|is_natural');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');   
        $this->form_validation->set_message('is_natural','%s harus angka!'); 

        $data=array(
                    'name'=>$name,
                    'info'=>$info,
                    // 'info'=>htmlspecialchars_decode(str_replace("monkey-1777","style",$info)),
                    // 'info'=>htmlspecialchars_decode((base64_decode($info))),
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        // check nama jika ada yang sama 
        $check_name = 0;
        if(!empty($id))
        {
            $check_name=$this->disclamer->select_data($this->_table," where upper(name)=upper('') and status not in (-5) and id !=".$this->db->escape($id) );
        }


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,  validation_errors());
        }
        else if($check_name->num_rows()>0)
        {
            echo $res=json_api(0, 'Nama sudah ada');   
        }
        else
        {

            $this->db->trans_begin();

            $this->disclamer->update_data($this->_table,$data,"id=".$this->db->escape($id) );

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
        $logUrl      = site_url().'master_data/disclamer/action_edit';
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
            'updated_by'=>$this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->disclamer->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'master_data/disclamer/action_change';
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
            'updated_by'=>$this->session->userdata('username'),
            );

        $id = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->disclamer->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'master_data/disclamer/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


}