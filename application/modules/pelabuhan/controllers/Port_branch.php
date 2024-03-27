<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class port_branch extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_port_branch','port');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_branch';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pelabuhan/port_branch';
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
            if($this->input->post('port')){
                $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
            }
    
            
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->port->dataList();
            echo json_encode($rows);
            exit;
        }

        if(!empty($this->session->userdata("port_id")))
        {
            $port=$this->port->select_data("app.t_mtr_port"," where id=".$this->session->userdata("port_id")."")->result();
        }
        else
        {
            $port=$this->port->select_data("app.t_mtr_port"," where status not in (-5) order by name asc ")->result();
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Cabang Pelabuhan',
            'content'  => 'port_branch/index',
            'port'     =>$port,
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        if(!empty($this->session->userdata('port_id')))
        {
            $data['port']=$this->port->select_data("app.t_mtr_port"," where id=".$this->session->userdata("port_id")." ")->result();
        }
        else
        {
            $data['port']=$this->port->select_data("app.t_mtr_port"," where status=1 order by name asc")->result();
            // $data['port']=$this->port->select_data("app.t_mtr_port"," where status !='-5' order by name asc")->result();

        }

        $data['title'] = 'Tambah Cabang Pelabuhan';
        $data['ship_class']=$this->port->select_data("app.t_mtr_ship_class"," where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');


        $this->form_validation->set_rules('branch_name', 'Nama Cabang ', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'nama cabang mengandung invalid karakter'));
        $this->form_validation->set_rules('branch_code', 'Kode Cabang ', 'trim|required|max_length[12]|callback_special_char', array('special_char' => 'kode cabang mengandung invalid karakter'));
        $this->form_validation->set_rules('ship_class', 'Tipe', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid tipe'));
        $this->form_validation->set_rules('port', 'Pelabuhan ', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('max_length','{field} Maximal  {param} karakter! ');

        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());   
        }
        else
        {
            $branch_name=trim($this->input->post('branch_name'));
            $branch_code=trim($this->input->post('branch_code'));
            $ship_class_id=$this->enc->decode($this->input->post('ship_class'));
            $port_id=$this->enc->decode($this->input->post('port'));

            $data=array(
                'port_id'=>$port_id,
                'branch_name'=>$branch_name,
                'ship_class'=>$ship_class_id,
                'branch_code'=>$branch_code,
                'status'=>1,
                'created_on'=>date('Y-m-d H:i:s'),
                'created_by'=>$this->session->userdata("username"),


                );


            $check_data=$this->port->select_data($this->_table," where port_id=$port_id  and ship_class=$ship_class_id and status not in (-5) ");

            $check_code=$this->port->select_data($this->_table," where upper(branch_code)=upper('".$branch_code."') and status not in (-5) ");

            if($check_data->num_rows()>0)
            {
                echo $res=json_api(0,"Data sudah ada");     
            }
            else if($check_code->num_rows()>0)
            {
                echo $res=json_api(0,"Kode sudah ada");     
            }
            else
            {
                $this->db->trans_begin();

                $this->port->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/user_kapal/action_add';
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

        if(!empty($this->session->userdata('port_id')))
        {
            $data['port']=$this->port->select_data("app.t_mtr_port"," where id=".$this->session->userdata('port_id')." ")->result();
        }
        else
        {
            $data['port']=$this->port->select_data("app.t_mtr_port"," where status=1 order by name asc")->result();
        }

        $data['title'] = 'Tambah Cabang Pelabuhan';
        $data['id']=$id;
        $data['detail']=$this->port->select_data("app.t_mtr_branch"," where id=".$id_decode." ")->row();
        $data['ship_class']=$this->port->select_data("app.t_mtr_ship_class"," where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $this->form_validation->set_rules('branch_name', 'Nama Cabang ', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'nama cabang mengandung invalid karakter'));
        $this->form_validation->set_rules('branch_code', 'Kode Cabang ', 'trim|required|max_length[12]|callback_special_char', array('special_char' => 'kode cabang mengandung invalid karakter'));
        $this->form_validation->set_rules('ship_class', 'Tipe', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid tipe'));
        $this->form_validation->set_rules('port', 'Pelabuhan ', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        $this->form_validation->set_rules('id', 'Pelabuhan ', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('max_length','{field} Maximal  {param} karakter! ');

        
        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {
            $id=$this->enc->decode($this->input->post('id'));
            $branch_name=trim($this->input->post('branch_name'));
            $branch_code=trim($this->input->post('branch_code'));
            $ship_class_id=$this->enc->decode($this->input->post('ship_class'));
            $port_id=$this->enc->decode($this->input->post('port'));

            $data=array(
                'port_id'=>$port_id,
                'branch_name'=>$branch_name,
                'ship_class'=>$ship_class_id,
                'branch_code'=>$branch_code,
                'status'=>1,
                'updated_on'=>date('Y-m-d H:i:s'),
                'updated_by'=>$this->session->userdata("username"),
    
            );
    
    
            $check_data=$this->port->select_data($this->_table," where port_id=$port_id  and ship_class=$ship_class_id and status not in (-5) and id !=$id ");
    
            $check_code=$this->port->select_data($this->_table," where upper(branch_code)=upper('".$branch_code."') and status not in (-5) and id !=$id ");
            
            if($check_data->num_rows()>0)
            {
                echo $res=json_api(0,"Data sudah ada");     
            }
            else if($check_code->num_rows()>0)
            {
                echo $res=json_api(0,"Kode sudah ada");     
            }
            else
            {

                $this->db->trans_begin();

                $this->port->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'pelabuhan/port_branch/action_edit';
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
            echo $res=json_api(0, 'Gagal delete data');            
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
            $this->port->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'master_data/setting_param/action_change';
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
                'updated_by'=>$this->session->userdata('username'),
                );


            $this->db->trans_begin();
            $this->port->update_data($this->_table,$data," id='".$id."'");

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
