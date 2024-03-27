<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Reward_sector extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_reward_sector','sector');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library(array('bcrypt'));

        $this->_table    = 'app.t_mtr_ifcs_reward';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'ifcs/reward_sector';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->sector->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Reward Sector IFCS',
            'content'  => 'reward_sector/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $sector=$this->sector->select_data("app.t_mtr_business_sector_ifcs", " where status=1 order by description asc")->result();

        $data_sector['']="Pilih";
        foreach ($sector as $key => $value) {

            $code_enc=$this->enc->encode($value->business_sector_code);
            $data_sector[$code_enc]=strtoupper($value->description);
        }

        $data['title'] = 'Tambah Reward Sector IFCS';
        $data['sector']=$data_sector;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $tier=trim($this->input->post('tier'));
        $sector=$this->enc->decode($this->input->post('sector'));
        $min=trim($this->input->post('min'));
        $max=trim($this->input->post('max'));
        $persen=trim($this->input->post('persen'));

        $this->form_validation->set_rules('tier', 'Nama Tier ', 'required');
        $this->form_validation->set_rules('sector', 'Sector ', 'required');
        $this->form_validation->set_rules('min', 'Min ', 'required');
        $this->form_validation->set_rules('max', 'max ', 'required');
        $this->form_validation->set_rules('persen', 'Persen ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        
        $data=array(
                    'tier'=>$tier,
                    'business_sector_code'=>$sector,
                    'min'=>$min,
                    'max'=>$max,
                    'reward_value'=>$persen,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // print_r($data); exit;

        $check=$this->sector->select_data($this->_table, " where upper(tier)=upper('{$tier}') and business_sector_code='{$sector}' and status !='-5' ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama Tier sudah ada");
        }
        else if($min>$max)
        {
            echo $res=json_api(0,"Min Tier tidak boleh besar dari Max Tier");
        }
        else
        {

            // print_r($data); exit;
            $this->db->trans_begin();

            $this->sector->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'ifcs/reward_sector/action_add';
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

        $sector=$this->sector->select_data("app.t_mtr_business_sector_ifcs", " where status=1 order by description asc")->result();
        $detail=$this->sector->select_data($this->_table, " where id={$id_decode} ");

        $data_sector['']="Pilih";
        $selected_sector="";
        foreach ($sector as $key => $value) {

            $code_enc=$this->enc->encode($value->business_sector_code);

            $detail->row()->business_sector_code==$value->business_sector_code?$selected_sector.=$code_enc:"";
            $data_sector[$code_enc]=strtoupper($value->description);
        }        

        $data['title'] = 'Edit Reward Sector IFCS';
        $data['detail']=$detail->row();
        $data['sector']=$data_sector;
        $data['selected_sector']=$selected_sector;
        $data['id'] = $id;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id'));

        $tier=trim($this->input->post('tier'));
        $sector=$this->enc->decode($this->input->post('sector'));
        $min=trim($this->input->post('min'));
        $max=trim($this->input->post('max'));
        $persen=trim($this->input->post('persen'));

        $this->form_validation->set_rules('tier', 'Nama Tier ', 'required');
        $this->form_validation->set_rules('sector', 'Sector ', 'required');
        $this->form_validation->set_rules('min', 'Min ', 'required');
        $this->form_validation->set_rules('max', 'max ', 'required');
        $this->form_validation->set_rules('persen', 'Persen ', 'required');

        
        $data=array(
                    'tier'=>$tier,
                    'business_sector_code'=>$sector,
                    'min'=>$min,
                    'max'=>$max,
                    'reward_value'=>$persen,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        // print_r($data); exit;

        $check=$this->sector->select_data($this->_table, " where upper(tier)=upper('{$tier}') and business_sector_code='{$sector}' and status !='-5' and id !={$id} ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama Tier sudah ada");
        }
        else if($min>$max)
        {
            echo $res=json_api(0,"Min Tier tidak boleh besar dari Max Tier");
        }
        else
        {
            // print_r($data); exit;
            $this->db->trans_begin();

            $this->sector->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'ifcs/reward_sector/action_edit';
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
        $this->sector->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'ifcs/reward_sector/action_change';
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
        $this->sector->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'ifcs/reward_sector/action_delete';
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
