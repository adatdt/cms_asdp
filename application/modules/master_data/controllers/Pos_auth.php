<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Pos_auth extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_pos_auth','auth');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_action_auth';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/pos_auth';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->auth->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'POS Auth',
            'content'  => 'pos_auth/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $identity_app=$this->auth->select_data("app.t_mtr_identity_app","")->row();

        if($identity_app->port_id==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port=$this->auth->select_data("app.t_mtr_port", " where status=1 order by name asc ")->result();
            }
            else
            {
                $port=$this->auth->select_data("app.t_mtr_port", " where id=".$this->session->userdata('port_id'))->result();
            }
        }        
        else
        {
            $port=$this->auth->select_data("app.t_mtr_port", " where id=".$identity_app->port_id )->result();
        }

        $action_pos=$this->auth->select_data("app.t_mtr_action", " where status=1 order by action_code asc ")->result();
        $group=$this->auth->select_data("core.t_mtr_user_group", " where status=1 order by name asc ")->result();

        $data_port[""]="Pilih";
        $data_action_pos[""]="Pilih";
        $data_group[""]="Pilih";

        if(!empty($port))
        {
            foreach ($port as $key => $value) {
                $data_port[$this->enc->encode($value->id)]=strtoupper($value->name);
            }
        }

        if(!empty($action_pos))
        {
            foreach ($action_pos as $key => $value) {
                $data_action_pos[$this->enc->encode($value->action_code)]=$value->action_code;
            }
        }

        if(!empty($group))
        {
            foreach ($group as $key => $value) {
                $data_group[$this->enc->encode($value->id)]=strtoupper($value->name);
            }            
        }        

        $data['title'] = 'Tambah Aksi POS AUTH';
        // $data['port']     = $data_port;
        $data['port']     = $port;
        $data['action_pos']  = $data_action_pos;
        $data['group']     = $data_group;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $action_code=$this->enc->decode($this->input->post('action_code'));
        $port=$this->input->post('port[]');
        $group=$this->enc->decode($this->input->post('group'));


        $this->form_validation->set_rules('action_code', 'Nama Kode Aksi POS ', 'required');
        $this->form_validation->set_rules('group', 'Group ', 'required');
        $this->form_validation->set_message('required','%s harus diisi!');

        $grab_data1=array(
                    'action_code'=>$action_code,
                    'user_group_id'=>$group,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        $data=$grab_data1;

        // count data yang sudah ada
        $check_exist=array();
        $port_exists=array();

        // checking apakah datanya sudah pernah ada
        if(!empty($port))
        {
            foreach ($port as $value) {
                //ceck data jika username sudah ada
                $check=$this->auth->select_data($this->_table," where action_code='".$action_code."' and port_id='".$this->enc->decode($value)."' and user_group_id='".$group."' and status<>'-5' ");

                if($check->num_rows()>0)
                {
                    $check_exist[]=1;
                    $port_exists[]=strtoupper($this->auth->select_data("app.t_mtr_port", " where id=".$this->enc->decode($value))->row()->name);
                }
            }
        }

        // print_r($port_exists); exit();

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if (empty($port)) 
        {
            echo $res=json_api(0,"Pelabuhan harus di pilih.");    
        }
        else if (array_sum($check_exist)>0)
        {
            echo $res=json_api(0,"Group ini di Pelabuhan ".implode(", ",$port_exists)." sudah ada Aksi POS AUTH.");    
        }
        // else if($check->num_rows()>0)
        // {
        //     echo $res=json_api(0,"Aksi POS AUTH sudah ada.");
        // }
        else
        {
            foreach ($port as $value) {
                
                $grab_data2[]=array(
                    'action_code'=>$action_code,
                    'port_id'=>$this->enc->decode($value),
                    'user_group_id'=>$group,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );
            }

            $data=$grab_data2;

            $this->db->trans_begin();

            $this->auth->insert_data_batch($this->_table,$data);

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
        $logUrl      = site_url().'master_data/pos_auth/action_add';
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

        $detail= $this->auth->select_data($this->_table,"where id=$id_decode")->row();

        $get_action_pos=$this->auth->select_data("app.t_mtr_action"," where action_code='".$detail->action_code."'")->row();

        $identity_app=$this->auth->select_data("app.t_mtr_identity_app","")->row();

        if($identity_app->port_id==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port=$this->auth->select_data("app.t_mtr_port", " where status=1 order by name asc ")->result();
            }
            else
            {
                $port=$this->auth->select_data("app.t_mtr_port", " where id=".$this->session->userdata('port_id'))->result();
            }
        }        
        else
        {
            $port=$this->auth->select_data("app.t_mtr_port", " where id=".$identity_app->port_id )->result();
        }

        $action_pos=$this->auth->select_data("app.t_mtr_action", " where status=1 order by action_code asc ")->result();
        $group=$this->auth->select_data("core.t_mtr_user_group", " where status=1 order by name asc ")->result();

        $data_port[""]="Pilih";
        $data_action_pos[""]="Pilih";
        $data_group[""]="Pilih";

        $selected_port="";
        $selected_group="";
        $selected_action_pos="";

        if(!empty($port))
        {
            foreach ($port as $key => $value) {

                $id_encode=$this->enc->encode($value->id);
                
                if($value->id==$detail->port_id)
                {
                    $selected_port=$id_encode;
                }

                $data_port[$id_encode]=strtoupper($value->name);
            }
        }

        if(!empty($action_pos))
        {
            foreach ($action_pos as $key => $value) {

                $id_encode=$this->enc->encode($value->action_code);
                
                if($value->action_code==$detail->action_code)
                {
                    $selected_action_pos=$id_encode;
                }

                $data_action_pos[$id_encode]=$value->action_code;
            }
        }

        if(!empty($group))
        {

            foreach ($group as $key => $value) {

                $id_encode=$this->enc->encode($value->id);
                
                if($value->id==$detail->user_group_id)
                {
                    $selected_group=$id_encode;
                }

                $data_group[$id_encode]=strtoupper($value->name);
            }            
        }        


        $data['title'] = 'Edit Aksi POS AUTH';
        $data['port']     = $data_port;
        $data['selected_port']     = $selected_port;
        $data['action_pos']  = $data_action_pos;
        $data['selected_action_pos']  = $selected_action_pos;
        $data['group']  = $data_group;
        $data['selected_group']  = $selected_group;
        $data['id'] = $id;        
        $data['detail']=$detail;
        $data['description']=empty($get_action_pos->description)?"":$get_action_pos->description;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=$this->enc->decode($this->input->post('id'));


        $action_code=$this->enc->decode($this->input->post('action_code'));
        $port=$this->enc->decode($this->input->post('port'));
        $group=$this->enc->decode($this->input->post('group'));


        $this->form_validation->set_rules('action_code', 'Nama Kode Aksi POS ', 'required');
        $this->form_validation->set_rules('group', 'Group ', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');


        $data=array(                    
                    'action_code'=>$action_code,
                    'port_id'=>$port,
                    'user_group_id'=>$group,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );
        
        $check=$this->auth->select_data($this->_table," where action_code='".$action_code."' and port_id='".$port."' and user_group_id='".$group."' and status<>'-5' and id<>".$id);        

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Group ini di Pelabuhan ini sudah ada Aksi POS AUTH.");
        }
        else
        {
            $this->db->trans_begin();

            $this->auth->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'master_data/pos_auth/action_edit';
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
        $this->auth->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'master_data/pos_auth/action_change';
        $d[1]==1?$logMethod='ENABLE':$logMethod='DISABLE';
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
        $this->auth->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'master_data/pos_action/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function get_description()
    {
        $action_code=trim($this->enc->decode($this->input->post("action_code")));

        $data="";
        if(!empty($action_code))
        {
            $get_data=$this->auth->select_data(" app.t_mtr_action "," where action_code='".$action_code."'")->row();

            $data .=$get_data->description;
        }

        echo json_encode($data);
    }

}
