<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class User_corporate extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_user_corporate','corporate');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library(array('bcrypt'));

        $this->_table    = 'app.t_mtr_member_ifcs';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'ifcs/user_corporate';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->corporate->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'User IFCS',
            'content'  => 'user_corporate/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah User Corporate';
        $data['corporate']=$this->corporate->select_data("app.t_mtr_corporate_ifcs"," where status=1 order by corporate_name asc")->result();
        $data['member_type']=$this->corporate->select_data("app.t_mtr_member_ifcs_type"," where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $corporate_code=$this->input->post('corporate_name');
        $member_type_id=$this->enc->decode($this->input->post('member_type'));
        $nik=trim($this->input->post('nik'));
        $nip=trim($this->input->post('nip'));
        $name=trim($this->input->post('name'));
        $telpon=trim($this->input->post('telphone'));
        $email=trim($this->input->post('email'));
        $pass=trim($this->input->post('password'));
        $branch=trim($this->input->post('branch'));
        $position=trim($this->input->post('position'));
        $booking=$this->input->post('booking');
        $reedem=$this->input->post('reedem');
        $topup_deposit=$this->input->post('topup_deposit');
        $cash_Out=$this->input->post('cash_Out');
        $purchase_deposit=$this->input->post('purchase_deposit');
        $password= $this->bcrypt->hash_password(strtoupper(md5($pass)));

        $this->form_validation->set_rules('corporate_name', 'Nama Corporate ', 'required');
        $this->form_validation->set_rules('position', 'Jabatan ', 'required');
        $this->form_validation->set_rules('member_type', 'Tipe Member ', 'required');
        $this->form_validation->set_rules('nip', 'NO Kepegawaian ', 'required');
        $this->form_validation->set_rules('nik', 'NO KTP ', 'required');
        $this->form_validation->set_rules('name', 'Nama Coporate ', 'required');
        $this->form_validation->set_rules('telphone', 'NO Telpon ', 'required');
        $this->form_validation->set_rules('branch', 'Cabang ', 'required');
        $this->form_validation->set_rules('email', 'Email ', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('valid_email','%s Tidak sesuai format email');

        
        $data=array(
                    'corporate_code'=>$corporate_code,
                    'member_type'=>$member_type_id,
                    'nik'=>$nik,
                    'nip'=>$nip,
                    'position'=>$position,
                    'name'=>$name,
                    'email'=>$email,
                    'phone'=>$telpon,
                    'topup_deposit'=>empty($topup_deposit)?'false':'true',
                    'deposit'=>empty($purchase_deposit)?'false':'true',
                    'cash_out_deposit'=>empty($cash_Out)?'false':'true',
                    'branch_code'=>$branch=='all'?null:$branch,
                    'password'=>$password,
                    'is_activation'=>1,
                    'booking'=>empty($booking)?'false':'true',
                    'reedem'=>empty($reedem)?'false':'true',
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // print_r($data); exit;

        $check=$this->corporate->select_data($this->_table, " where upper(email)=upper('{$email}') and status !='-5' ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Email sudah ada");
        }
        else
        {

            $this->db->trans_begin();

            $this->corporate->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'ifcs/user_corporate/action_add';
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

        $get_data=$this->corporate->select_data("app.t_mtr_member_ifcs"," where id={$id_decode} ")->row();

        $data['title'] = 'Edit User Corporate';
        $data['id'] = $id;
        $data['corporate']=$this->corporate->select_data("app.t_mtr_corporate_ifcs"," where corporate_code='{$get_data->corporate_code}' and status =1 order by corporate_name asc")->result();
        $data['branch']=$this->corporate->select_data("app.t_mtr_branch_ifcs"," where corporate_code='{$get_data->corporate_code}' and status !='-5' order by description  asc")->result();
        $data['member_type']=$this->corporate->select_data("app.t_mtr_member_ifcs_type"," where status=1 order by name asc")->result();
        $data['detail']=$this->corporate->select_data($this->_table,"where id=$id_decode")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('corporate'));

        $member_type_id=$this->enc->decode($this->input->post('member_type'));
        $nik=trim($this->input->post('nik'));
        $nip=trim($this->input->post('nip'));
        $name=trim($this->input->post('name'));
        $telpon=trim($this->input->post('telphone'));
        $branch=trim($this->input->post('branch'));
        $booking=$this->input->post('booking');
        $reedem=$this->input->post('reedem');
        $position=trim($this->input->post('position'));
        $booking=$this->input->post('booking');
        $reedem=$this->input->post('reedem');
        $topup_deposit=$this->input->post('topup_deposit');
        $cash_Out=$this->input->post('cash_Out');
        $purchase_deposit=$this->input->post('purchase_deposit');
        $is_active=$this->enc->decode($this->input->post('is_active'));
        $pass=trim($this->input->post('password'));


        $this->form_validation->set_rules('member_type', 'Tipe Member ', 'required');
        $this->form_validation->set_rules('position', 'Jabatan ', 'required');
        $this->form_validation->set_rules('branch', 'Cabang ', 'required');
        $this->form_validation->set_rules('nip', 'NO Kepegawaian ', 'required');
        $this->form_validation->set_rules('nik', 'NO KTP ', 'required');
        $this->form_validation->set_rules('name', 'Nama Coporate ', 'required');
        $this->form_validation->set_rules('telphone', 'NO Telpon ', 'required');
        $this->form_validation->set_rules('is_active', 'Aktivasi ', 'required');
        // $this->form_validation->set_rules('email', 'Email ', 'required|valid_email');

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('valid_email','%s Tidak sesuai format email');

        
        $data=array(
                    'member_type'=>$member_type_id,
                    'nik'=>$nik,
                    'nip'=>$nip,
                    'name'=>$name,
                    'topup_deposit'=>empty($topup_deposit)?'false':'true',
                    'cash_out_deposit'=>empty($cash_Out)?'false':'true',
                    'deposit'=>empty($purchase_deposit)?'false':'true',
                    'position'=>$position,
                    'branch_code'=>$branch=='all'?null:$branch,
                    'is_activation'=>$is_active,
                    'booking'=>empty($booking)?'false':'true',
                    'reedem'=>empty($reedem)?'false':'true',
                    'phone'=>$telpon,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        // print_r($data); exit;

        // $check=$this->corporate->select_data($this->_table, " where upper(email)=upper('{$email}') and status !='-5' and id !='-5' ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else
        {

            // print_r($data); exit;
            $this->db->trans_begin();

            $this->corporate->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'ifcs/user_corporate/action_edit';
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
        $this->corporate->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'ifcs/corporate/action_change';
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
        $this->corporate->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'ifcs/corporate/action_delete';
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
            'password' => $this->bcrypt->hash_password(strtoupper(md5(trim($post['password'])))),
            'reset_password'=>0
        );

        if($this->form_validation->run() == FALSE)
        {
            $response = json_api(0,validation_errors());
        }
        else
        {
            $query = $this->global_model->updateData($this->_table, $data, 'id');
            if($query)
            {
                $response = json_api(1,'Update Password Berhasil');
            }
            else
            {
                $response = json_encode($this->db->error()); 
            }
        }

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'update', json_encode($data), $response); 
        echo $response;
    }

    public function get_branch()
    {
        $corporate_code=$this->input->post('corporate_code');
        $row=$this->corporate->select_data("app.t_mtr_branch_ifcs", " where corporate_code='{$corporate_code}' and status!='-5' order by id ASC, description asc ")->result();

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