<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Peak_season extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_peak_season','peak');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library(array('bcrypt'));

        $this->_table    = 'app.t_mtr_peak_season';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'ifcs/peak_season';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->peak->dataList();
            echo json_encode($rows);
            exit;
        }

        $identity_app=$this->peak->select_data("app.t_mtr_identity_app","")->row();

        if ($identity_app->port_id==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port=$this->peak->select_data("app.t_mtr_port", " where status !='-5' order by name asc")->result();
                $data_port['']="Pilih";
            }
            else
            {
                $port=$this->peak->select_data("app.t_mtr_port", " where id=".$this->session->userdata('port_id'))->result();
            }   
        }
        else
        {
            $port=$this->peak->select_data("app.t_mtr_port", " where id=".$identity_app->port_id)->result();

        }

        foreach ($port as $key => $value) {

            $id_enc=$this->enc->encode($value->id);
            $data_port[$id_enc]=strtoupper($value->name);
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Peak Season IFCS',
            'port'     =>$data_port,
            'content'  => 'peak_season/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $identity_app=$this->peak->select_data("app.t_mtr_identity_app","")->row();

        if ($identity_app->port_id==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port=$this->peak->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();
            }
            else
            {
                $port=$this->peak->select_data("app.t_mtr_port", " where id=".$this->session->userdata('port_id'))->result();
            }   
        }
        else
        {
            $port=$this->peak->select_data("app.t_mtr_port", " where id=".$identity_app->port_id)->result();

        }

        $data_port['']="Pilih";
        foreach ($port as $key => $value) {

            $id_enc=$this->enc->encode($value->id);
            $data_port[$id_enc]=strtoupper($value->name);
        }

        $data['title'] = 'Tambah Peak Season';
        $data['port']=$data_port;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=$this->enc->decode($this->input->post('port'));
        $description=trim($this->input->post('description'));
        $start_date=trim($this->input->post('start_date'));
        $end_date=trim($this->input->post('end_date'));

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('description', 'Keterangan ', 'required');
        $this->form_validation->set_rules('start_date', 'Tanggal Awal ', 'required');
        $this->form_validation->set_rules('end_date', 'Tanggal Akhir ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        
        $data=array(
                    'port_id'=>$port,
                    'start_date'=>$start_date,
                    'end_date'=>$end_date,
                    'description'=>$description,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else
        {
            $this->db->trans_begin();

            $this->peak->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'ifcs/peak_season/action_add';
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

        $detail=$this->peak->select_data($this->_table, " where id={$id_decode} ");

        $identity_app=$this->peak->select_data("app.t_mtr_identity_app","")->row();

        if ($identity_app->port_id==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port=$this->peak->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();
            }
            else
            {
                $port=$this->peak->select_data("app.t_mtr_port", " where id=".$this->session->userdata('port_id'))->result();
            }   
        }
        else
        {
            $port=$this->peak->select_data("app.t_mtr_port", " where id=".$identity_app->port_id)->result();

        }

        $data_port['']="Pilih";
        $selected_port="";
        foreach ($port as $key => $value) {

            $id_enc=$this->enc->encode($value->id);
            $value->id==$detail->row()->port_id?$selected_port=$id_enc:"";
            $data_port[$id_enc]=strtoupper($value->name);
        }

        $data['title'] = 'Edit Reward Sector IFCS';
        $data['detail']=$detail->row();
        $data['port']=$data_port;
        $data['selected_port']=$selected_port;
        $data['id'] = $id;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id'));

        $port=$this->enc->decode($this->input->post('port'));
        $description=trim($this->input->post('description'));
        $start_date=trim($this->input->post('start_date'));
        $end_date=trim($this->input->post('end_date'));

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('description', 'Keterangan ', 'required');
        $this->form_validation->set_rules('start_date', 'Tanggal Awal ', 'required');
        $this->form_validation->set_rules('end_date', 'Tanggal Akhir ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        
        $data=array(
                    'port_id'=>$port,
                    'start_date'=>$start_date,
                    'end_date'=>$end_date,
                    'description'=>$description,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else
        {
            // print_r($data); exit;
            $this->db->trans_begin();

            $this->peak->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'ifcs/peak_season/action_edit';
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
        $this->peak->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'ifcs/peak_season/action_change';
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
        $this->peak->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'ifcs/peak_season/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function reset_password($param){
        validate_ajax();
        $data['id']    = $param;
        $data['title'] = 'Ganti Password';

        $this->load->view($this->_module.'/reset_password',$data);
    }

    public function action_reset_password(){
        validate_ajax();
        $post = $this->input->post();

        /* validation */
        $this->form_validation->set_rules('password', 'Password Baru', 'trim|required');
        $this->form_validation->set_message('required','%s harus diisi!');

        $id = $this->enc->decode($post['id']);
        /* data post */


        $data = array(
            'id' => $id, 
            'password' => $this->bcrypt->hash_password(strtoupper(md5(trim($post['password']))))
        );

        if($this->form_validation->run() == FALSE){
            $response = json_api(0,validation_errors());
        }else{
            $query = $this->global_model->updateData($this->_table, $data, 'id');
            if($query){
                $response = json_api(1,'Update Password Berhasil');
            }else{
                $response = json_encode($this->db->error()); 
            }
        }

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'update', json_encode($data), $response); 
        echo $response;
    }

    public function get_branch()
    {
        $corporate_code=$this->input->post('corporate_code');
        $row=$this->corporate->select_data("app.t_mtr_branch_ifcs", " where corporate_code='{$corporate_code}' and status!='-5' order by description asc ")->result();

        $data=array();

        if(!empty($row))
        {
            foreach ($row as $key => $value) {
                   $data[]=$value;
               }   
        }
        echo json_encode($data);

    }    


}
