<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/**
 * ------------------
 * CLASS NAME : Users
 * ------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Users extends MY_Controller {
	public function __construct() {
		parent::__construct();
        logged_in();
        $this->load->model('users_model');
        $this->load->library(array('bcrypt'));

        $this->_table     = 'core.t_mtr_user';
        $this->_username  = $this->session->userdata('username');
        $this->_module    = 'configuration/users';
	}

    public function index(){
        checkUrlAccess(uri_string(),'view');
        
        $getWhereGroup=$this->session->userdata("group_id")==1?"":" and id<>1 ";
        $data = array(
            'home'       => 'Home',
            'url_home'   => site_url('home'),
            'parent'     => 'Konfigurasi Sistem',
            'url_parent' => '#',
            'title'      => 'User',
            'content'    => 'users/index',
            'user_group' => $this->users_model->select_data("core.t_mtr_user_group","where status not in (-5) ".$getWhereGroup." order by name asc")->result(),
            'port' => $this->users_model->select_data("app.t_mtr_port","where status not in (-5)  order by name asc")->result(),
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),
            'btn_add'    => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
        );

        $this->load->view ('default', $data);
    }

    public function ajaxlist_user() {
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('user_group')){
                $this->form_validation->set_rules('user_group', 'user_group', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid user group'));
            }
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->users_model->userList();
            
            $rows["tokenHash"] = $this->security->get_csrf_hash();
            $rows["csrfName"] = $this->security->get_csrf_token_name();
            echo json_encode($rows);
            exit;
        }
    }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['user_group'] = $this->dropdown_user_group();
        $data['title']      = 'Tambah User';
        $data['port']=$this->users_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $this->load->view($this->_module.'/add',$data);
    }

    public function edit($param){
        validate_ajax();
        $getUserGroup=$this->users_model->select_data("core.t_mtr_user_group", " where status <> '-5' order by id asc ")->result();
        $getUser=$this->global_model->selectById($this->_table, 'id', $this->enc->decode($param));


        $userGroup['']='';
        $selected="";
        foreach ($getUserGroup as $key => $value) {
            if($value->id==$getUser->user_group_id)
            {

                $userGroupEnc=$this->enc->encode($getUser->user_group_id);
                $selected =$userGroupEnc;
                $userGroup[$userGroupEnc]=strtoupper($value->name);
            }
            else
            {
                $userGroup[$this->enc->encode($value->id)]=strtoupper($value->name);   
            }
        }

        $data['id']         = $param;
        $data['selectedGroup']         = $selected;
        $data['user_group'] = $userGroup;
        $data['row']        = $this->global_model->selectById($this->_table, 'id', $this->enc->decode($param));
        $data['port']=$this->users_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['title']      = 'Edit User';
        $this->load->view($this->_module.'/edit',$data);
    }

    public function reset_password($param){
        validate_ajax();
        $data['id']    = $param;
        $data['title'] = 'Ganti Password';

        $this->load->view($this->_module.'/reset_password',$data);
    }

    public function action_add(){
        validate_ajax();
        
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $this->form_validation->set_rules('username', 'username', 'trim|required|min_length[3]|max_length[100]|callback_special_char', array('special_char' => 'Username memuat invalid karakter'));
        $this->form_validation->set_rules('user_group', 'user_group', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid user group'));
        $this->form_validation->set_rules('first_name', 'Nama depan', 'required|min_length[3]|max_length[100]|callback_special_char', array('special_char' => 'Nama depan memuat invalid karakter'));
        $this->form_validation->set_rules('last_name', 'Nama belakang', 'min_length[3]|max_length[100]|callback_special_char', array('special_char' => 'Nama belakang memuat invalid karakter'));

        $this->form_validation->set_rules('port', 'port', 'required');
        $this->form_validation->set_rules('phone', 'Nomor telepon', 'min_length[10]|max_length[17]|numeric');
        $this->form_validation->set_rules('email', 'email', 'min_length[5]|max_length[254]|valid_email');
        $this->form_validation->set_rules('login_cms', 'login_cms', 'max_length[1]|numeric');
        $this->form_validation->set_rules('login_pos', 'login_pos', 'max_length[1]|numeric');
        $this->form_validation->set_rules('login_validator', 'login_validator', 'max_length[1]|numeric');
        $this->form_validation->set_rules('login_ektp_reader', 'login_ektp_reader', 'max_length[1]|numeric');
        $this->form_validation->set_rules('login_cs', 'login_cs', 'max_length[1]|numeric');
        $this->form_validation->set_rules('vertifikator', 'vertifikator', 'max_length[1]|numeric');

        
        if($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
            exit;
        }
        
        $post = $this->input->post();

        
        $set_pass="admin123";

        /* data post */
        $post['port']=='all'?$dataport=null:$dataport=$this->enc->decode($post['port']);

        $first_name=$this->input->post('first_name');
        $last_name=$this->input->post('last_name'); 
        $username =trim($this->input->post('username'));
        $user_group_id = $this->enc->decode($this->input->post('user_group'));
        $phone = $this->input->post('phone');
        $email =$this->input->post('email'); 
        $admin_pannel_login=$this->input->post('login_cms');
        $pos_login=$this->input->post('login_pos');
        $validator_login=$this->input->post('login_validator');
        $e_ktp_reader_login=$this->input->post('login_ektp_reader');
        $cs_login=$this->input->post('login_cs'); 
        $vertifikator=$this->input->post('vertifikator'); 
        $command_center=$this->input->post('command_center'); 

        $userPhoneselfService =""; 
        $passwordPhoneSelfservice = ""; 
        $extPhoneSelfservice= "";

        $userPhoneselfServiceId = $this->enc->decode($this->input->post('userPhoneselfService'));

        $checkAllComandCenter =0;
        if(!empty($userPhoneselfServiceId))
        {
            // check apakah sudah di pakai 
            $checkUserPhone = $this->users_model->select_data("app.t_mtr_phone_extension", "where is_used =0 and id =".$userPhoneselfServiceId)->row();
            if(count($checkUserPhone) > 0)
            {
                $checkAllComandCenter = 0;
                $userPhoneselfService = $checkUserPhone->username_phone; 
                $passwordPhoneSelfservice = $checkUserPhone->password_phone; 
                $extPhoneSelfservice= $checkUserPhone->extension_phone;
            }
            else
            {
                $checkAllComandCenter = 1;
            }
        }

        // $password= $this->bcrypt->hash_password(strtoupper(md5($this->input->post('$password'))));
        $password= $this->bcrypt->hash_password(strtoupper(md5($set_pass)));

        $data = array(
            'first_name' => $first_name, 
            'last_name' => $last_name, 
            'username' => strtolower($username), 
            'user_group_id' =>$user_group_id,
            'phone' => $phone, 
            'email' => $email, 
            'admin_pannel_login'=>$admin_pannel_login,
            'pos_login'=>$pos_login,
            'validator_login'=>$validator_login,
            'e_ktp_reader_login'=>$e_ktp_reader_login,
            'verifier_login'=>$vertifikator,
            'cs_login'=>$cs_login,
            'port_id' => $dataport, 
            'password'  => $password,
            'username_phone'=>$userPhoneselfService,
            'password_phone'=>$passwordPhoneSelfservice,
            'extension_phone'=>$extPhoneSelfservice,
            'command_center_login'=>$command_center,

        );

        // ceck username selain -5
        $check = $this->users_model->select_data($this->_table," where upper(username)=upper('".$post['username']."')  and status not in (-5)" );

        if($check->num_rows()>0)
        {
            $response =  json_api(0,'Username Sudah Ada'); 
        }
        else if($checkAllComandCenter == 1)
        {
            $response =  json_api(0,' Username ext, password ext dan ext Sudah terpakai'); 
        }
        else{  
            $query = $this->global_model->saveData($this->_table, $data);
            if(!empty($userPhoneselfServiceId))
            {
                $upadatedata = array("is_used"=> 1,
                                                    "used_by"=>strtolower($username),
                                                    "updated_by"=> $this->session->userdata("username"),
                                                    "updated_on"=>date("Y-m-d H:i:s")                  
                                                );
                $this->users_model->update_data("app.t_mtr_phone_extension", $upadatedata, " id=".$userPhoneselfServiceId);
            }

            if($query){
                $response = json_api(1,'Simpan Data Berhasil');
            }else{
                $response = json_encode($this->db->error()); 
            }
        }

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'ADD', json_encode($data), $response); 
        echo $response;
    }

    public function action_edit(){
        validate_ajax();        
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $post = $this->input->post();
        $id = $this->enc->decode($post['id']);

        $_POST['id']=$id;

        $this->form_validation->set_rules('id', 'id', 'trim|required');
        $this->form_validation->set_rules('username', 'username', 'trim|required|min_length[3]|max_length[100]|callback_special_char', array('special_char' => 'Username memuat invalid karakter'));
        $this->form_validation->set_rules('user_group', 'user_group', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid user group'));
        $this->form_validation->set_rules('first_name', 'Nama depan', 'required|min_length[3]|max_length[100]|callback_special_char', array('special_char' => 'Nama depan memuat invalid karakter'));
        $this->form_validation->set_rules('last_name', 'Nama belakang', 'min_length[3]|max_length[100]|callback_special_char', array('special_char' => 'Nama belakang memuat invalid karakter'));

        /* validation */        
        $this->form_validation->set_rules('port', 'port', 'required');
        $this->form_validation->set_rules('phone', 'Nomor telepon', 'min_length[10]|max_length[17]|numeric');
        $this->form_validation->set_rules('email', 'email', 'min_length[5]|max_length[254]|valid_email');
        $this->form_validation->set_rules('login_cms', 'login_cms', 'max_length[1]|numeric');
        $this->form_validation->set_rules('login_pos', 'login_pos', 'max_length[1]|numeric');
        $this->form_validation->set_rules('login_validator', 'login_validator', 'max_length[1]|numeric');
        $this->form_validation->set_rules('login_ektp_reader', 'login_ektp_reader', 'max_length[1]|numeric');
        $this->form_validation->set_rules('login_cs', 'login_cs', 'max_length[1]|numeric');
        $this->form_validation->set_rules('vertifikator', 'vertifikator', 'max_length[1]|numeric');

        
        if($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
            exit;
        }
        
        
        $post['port']=='all'?$dataport=null:$dataport=$this->enc->decode($post['port']);
        /* data post */

        $first_name=$this->input->post('first_name');
        $last_name=$this->input->post('last_name'); 
        $phone = $this->input->post('phone');
        $email =$this->input->post('email'); 
        $admin_pannel_login=$this->input->post('login_cms');
        // $user_group=$this->input->post('user_group');
        $user_group=$this->enc->decode($this->input->post('user_group'));
        $pos_login=$this->input->post('login_pos');
        $validator_login=$this->input->post('login_validator');
        $e_ktp_reader_login=$this->input->post('login_ektp_reader');
        $cs_login=$this->input->post('login_cs'); 
        $vertifikator=$this->input->post('vertifikator'); 
        $command_center=$this->input->post('command_center'); 
        

        $userPhoneselfService =""; 
        $passwordPhoneSelfservice = ""; 
        $extPhoneSelfservice= "";

        $userPhoneselfServiceId = $this->enc->decode($this->input->post('userPhoneselfService'));

        $getDetailUser = $this->users_model->select_data("core.t_mtr_user", "where id=".$id)->row();

        $checkAllComandCenter =0;

        // master ext tidak ada perubahan
        $idtUpdateExt = 0;
        if(!empty($userPhoneselfServiceId) && !empty($getDetailUser->username_phone))
        {
            // mencari id master yang terpairing
            $getIdMasterExt = $this->users_model->select_data("app.t_mtr_phone_extension", "where  username_phone='$getDetailUser->username_phone' and password_phone='$getDetailUser->password_phone' and extension_phone='$getDetailUser->extension_phone' and status=1")->row();
            $idExtMaster = @$getIdMasterExt->id;
            // check apakah sudah di pakai 
            $checkUserPhone = $this->users_model->select_data("app.t_mtr_phone_extension", "where  id =".$userPhoneselfServiceId)->row();

            if(count($checkUserPhone) > 0)
            {
                // check jika datanya sama seperti existing
                if($checkUserPhone->username_phone == $getDetailUser->username_phone && $checkUserPhone->password_phone == $getDetailUser->password_phone && $checkUserPhone->extension_phone == $getDetailUser->extension_phone )
                {
                    $userPhoneselfService = $checkUserPhone->username_phone; 
                    $passwordPhoneSelfservice = $checkUserPhone->password_phone; 
                    $extPhoneSelfservice = $checkUserPhone->extension_phone;

                    // master ext tidak di update
                    $idtUpdateExt = 1;

                    // echo 1; exit;
    
                }
                else if($checkUserPhone->is_used == 0)
                {
                    // kondisi jika  data extention  berbeda dengan  existing
                    // mencari id master yang terpairing
                    $getIdMasterExt = $this->users_model->select_data("app.t_mtr_phone_extension", "where  username_phone='$getDetailUser->username_phone' and password_phone='$getDetailUser->password_phone' and extension_phone='$getDetailUser->extension_phone' and status=1")->row();
                    
                    $userPhoneselfService = $checkUserPhone->username_phone; 
                    $passwordPhoneSelfservice = $checkUserPhone->password_phone; 
                    $extPhoneSelfservice = $checkUserPhone->extension_phone;

                    // master ext di update
                    $idtUpdateExt = 2;
                }
                else
                {
                    $checkAllComandCenter  = 1; // sudah terpakai
                }
            }
        }
        else if(empty($userPhoneselfServiceId) && !empty($getDetailUser->username_phone)) // kondisi jika data ext yang tadinya ada jadi tidak ada
        {            
            // mencari id master yang terpairing
            $getIdMasterExt = $this->users_model->select_data("app.t_mtr_phone_extension", "where  username_phone='$getDetailUser->username_phone' and password_phone='$getDetailUser->password_phone' and extension_phone='$getDetailUser->extension_phone' and status=1")->row();
            $idExtMaster = @$getIdMasterExt->id;

            // master ext di update kembali ke tidak pairing
            $idtUpdateExt = 3;
        }
        else if(!empty($userPhoneselfServiceId) && empty($getDetailUser->username_phone))
        { // kondisi ketika update extension tapi user belum terpairing sama sekali
            $checkUserPhone = $this->users_model->select_data("app.t_mtr_phone_extension", "where  is_used=0 and id =".$userPhoneselfServiceId)->row();

            if(count($checkUserPhone)>0)
            {
                $userPhoneselfService = $checkUserPhone->username_phone; 
                $passwordPhoneSelfservice = $checkUserPhone->password_phone; 
                $extPhoneSelfservice = $checkUserPhone->extension_phone;
    
                $idtUpdateExt = 4;
            }
            else
            {
                $checkAllComandCenter  = 1; // sudah terpakai
            }
        }

        $data = array(
            'first_name' => $first_name, 
            'last_name' => $last_name, 
            'port_id' => $dataport,
            'user_group_id' => $user_group,
            'phone' => $phone, 
            'email' => $email,
            'admin_pannel_login'=>$admin_pannel_login,
            'pos_login'=>$pos_login,
            'validator_login'=>$validator_login,
            'e_ktp_reader_login'=>$e_ktp_reader_login,
            'cs_login'=>$cs_login,
            'verifier_login'=>$vertifikator,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            'username_phone'=>$userPhoneselfService,
            'password_phone'=>$passwordPhoneSelfservice,
            'extension_phone'=>$extPhoneSelfservice,
            'command_center_login'=>$command_center,
        );
        
        // echo array_sum($checkAllComandCenter); exit;

        $check = $this->global_model->checkData(
            $this->_table,
            array('username' => $post['username']),
            'id',$id
        );

        if ($check)
        {
            $response =  json_api(0,'Username '.$post['username'].' Sudah Ada'); 
        }
        else if($checkAllComandCenter == 1)
        {
            $response =  json_api(0,' Username ext, password ext dan ext Sudah terpakai'); 
        }        
        else
        {
            $this->db->trans_begin();
            // update untuk ext yang di pakai
            // is used 1 digunakan oleh username, 2 di gumakan oleh device, 0 tidak ada yang menggunakan
            $upadatedata = array("is_used"=> 1, 
                "used_by"=>strtolower($getDetailUser->username),
                "updated_by"=> $this->session->userdata("username"),
                "updated_on"=>date("Y-m-d H:i:s")                  
            );

            // update untuk mengembalikan data , unpairing data
            $upadatedata2 = array("is_used"=> 0,
                                                "used_by"=>"",
                                                "updated_by"=> $this->session->userdata("username"),
                                                "updated_on"=>date("Y-m-d H:i:s")                  
                                            );            

            // update jika ada perubahaan ext
            if($idtUpdateExt==2)
            {
                $this->users_model->update_data("app.t_mtr_phone_extension", $upadatedata, " id=".$userPhoneselfServiceId);
                
                $this->users_model->update_data("app.t_mtr_phone_extension", $upadatedata2, " id=".$idExtMaster);                
            }
            else if($idtUpdateExt==3)
            {
                $this->users_model->update_data("app.t_mtr_phone_extension", $upadatedata2, " id=".$idExtMaster);              
            }
            else if($idtUpdateExt==4)
            {
                $this->users_model->update_data("app.t_mtr_phone_extension", $upadatedata, " id=".$userPhoneselfServiceId);
            }

            $query = $this->users_model->update_data($this->_table, $data, "id=$id");

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                $response=json_api(0, 'Gagal update data');
            }
            else
            {
                $this->db->trans_commit();
                $response=json_api(1, 'Berhasil update data');
            }   
        }

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'update', json_encode($data), $response); 
        echo $response;
    }

    public function action_reset_password(){
        validate_ajax();
        $post = $this->input->post();
        $this->form_validation->set_rules('id', 'id', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid id'));
        $this->form_validation->set_rules('password', 'password', 'required');

        
        if($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
            exit;
        }
        /* validation */
        $this->form_validation->set_rules('password', 'Password Baru', 'trim|required');
        $this->form_validation->set_message('required','%s harus diisi!');

        $id = $this->enc->decode($post['id']);
        /* data post */
        $data = array(
            'id' => $id, 
            'password' => $this->bcrypt->hash_password(strtoupper(md5($post['password'])))
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

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'UPDATE', json_encode($data), $response); 
        echo $response;
    }

    public function action_delete($param){
        validate_ajax();
        $id = $this->enc->decode($param);

        /* data */
        $data = array(
            'id' => $id,
            'status' => -5
        );

        $query = $this->global_model->updateData($this->_table, $data, 'id');
        if($query){
            $response = json_api(1,'Delete Data Berhasil');
        }else{
            $response = json_encode($this->db->error()); 
        }

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'DELETE', json_encode($data), $response); 
        echo $response;
    }

    function dropdown_user_group(){

        $getWhereGroup=$this->session->userdata("group_id")==1?"":" and id<>1 ";
        $datas = $this->users_model->select_data("core.t_mtr_user_group","where status=1 ".$getWhereGroup." order by name asc")->result();

        // $datas    = $this->global_model->selectAll('core.t_mtr_user_group');
        $data[''] = '';

        if($datas){
            foreach($datas as $row){
                $data[$this->enc->encode($row->id)] = $row->name;
            }
        }
        
        return $data;
    }


    function select_port(){
        $datas    = $this->global_model->selectAll('app.t_mtr_port');
        $data[''] = '';

        if($datas){
            foreach($datas as $row){
                $data[$row->id] = $row->name;
            }
        }
        
        return $data;
    }

    public function enable($param)
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
        $this->users_model->update_data($this->_table,$data,"id=".$this->enc->decode($d[0]));

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal aktifkan data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil aktifkan data');
        }


        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'configuration/user/enable';
        $logMethod   = 'ENABLED';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function disable($param)
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
        $this->users_model->update_data($this->_table,$data,"id=".$this->enc->decode($d[0]));

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal dinonaktifkan data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Data berhasil dinonaktifkan ');
        } 

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'configuration/user/enable';
        $logMethod   = 'DISABLED';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function download()
    {

        $this->load->library('exceldownload');

        // $code=$this->enc->decode($encode);
        $data=$this->users_model->download()->result();
        $excel = new Exceldownload();
        // Send Header


        $excel->setHeader('Data_User.xls');
        $excel->BOF();



        $excel->writeLabel(0, 0, "NO");
        $excel->writeLabel(0, 1, "NAMA DEPAN");
        $excel->writeLabel(0, 2, "NAMA BELAKANG");
        $excel->writeLabel(0, 3, "USERNAME");
        $excel->writeLabel(0, 4, "GROUP");
        $excel->writeLabel(0, 5, "PELABUHAN");
        $excel->writeLabel(0, 6, "USERNAME PHONE");
        $excel->writeLabel(0, 7, "EXTENSION PHONE	");
        $excel->writeLabel(0, 8, "LOGIN CMS");
        $excel->writeLabel(0, 9, "LOGIN VALIDATOR");
        $excel->writeLabel(0, 10, "LOGIN E-KTP READER");
        $excel->writeLabel(0, 11, "LOGIN CS");
        $excel->writeLabel(0, 12, "LOGIN POS");
        $excel->writeLabel(0, 13, "STATUS");


        $index=1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index,0, $index);
            $excel->writeLabel($index,1, $value->first_name);
            $excel->writeLabel($index,2, $value->last_name);
            $excel->writeLabel($index,3, $value->username);
            $excel->writeLabel($index,4, $value->group_name);
            $excel->writeLabel($index,5, $value->port_name);
            $excel->writeLabel($index,6, $value->username_phone);
            $excel->writeLabel($index,7, $value->extension_phone);
            $excel->writeLabel($index,8, $value->admin_pannel_login);
            $excel->writeLabel($index,9, $value->validator_login);
            $excel->writeLabel($index,10, $value->e_ktp_reader_login);
            $excel->writeLabel($index,11, $value->cs_login);
            $excel->writeLabel($index,12, $value->pos_login);
            $excel->writeLabel($index,13, $value->status);



            $index++;
        }
         
        $excel->EOF();
        exit();
    }

    public function download_excel()
    {

        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');

        $data = $this->users_model->download_excel();

        $user_group=$this->enc->decode($this->input->get("user_group"));


        $file_name = 'DATA USER';
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');


        $header = array(
            'NO' =>'string',
            'NAMA DEPAN' =>'string',
            'NAMA BELAKANG' =>'string',
            'USERNAME' =>'string',
            'GROUP' =>'string',
            'PELABUHAN' =>'string',
            'USERNAME PHONE' =>'string',
            'EXTENSION PHONE	' =>'string',
            'LOGIN CMS' =>'string',
            'LOGIN VALIDATOR' =>'string',
            'LOGIN E-KTP READER' =>'string',
            'LOGIN CS' =>'string',
            'LOGIN POS' =>'string',
            'VERIFIKATOR' =>'string',
            'COMMAND CENTER	' =>'string',
            'STATUS' =>'string',
        );

        $no=1;


        foreach ($data as $key => $value) {
            $rows[] = array($no,
                    $value->first_name,
                    $value->last_name,
                    $value->username,
                    $value->group_name,
                    empty($value->port_name)?"Semua Pelabuhan":$value->port_name,
                    $value->username_phone,
                    $value->extension_phone,
                    $value->admin_pannel_login,
                    $value->validator_login,
                    $value->e_ktp_reader_login,
                    $value->cs_login,
                    $value->pos_login,
                    $value->verifier_login,
                    $value->command_center_login,
                    $value->status,
                );
            $no++;
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header,$styles1);

        foreach($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }
    function getextension()
    {
        $portId = $this->input->post('portId');
        $usernameExt = $this->input->post('usernameExt');

        if($portId == 'all')
        {
            $dataReturn = $this->users_model->getDataExt("|".$usernameExt);
            $data = $dataReturn['data'];
            $selectData = $dataReturn['selectData'];
        }
        else
        {
            $portDecode = $this->enc->decode($portId);
            if( $portDecode != "")
            {
                $dataReturn = $this->users_model->getDataExt($portDecode."|".$usernameExt);
                $data = $dataReturn['data'];
                $selectData = $dataReturn['selectData'];
            }
            else
            {
                $data = [];
                $selectData ="";
            }
        }
        $return = array("data" => $data,
                                    "selectData"=> $selectData,
                                    "tokenHash" => $this->security->get_csrf_hash(),
                                    "csrfName" => $this->security->get_csrf_token_name(),                                    
                                );
        echo json_encode($return);
    }        
    function getextension_backup()
    {
        $portId = $this->input->post('portId');

        if($portId == 'all')
        {
            $data = $this->users_model->getDataExt();
        }
        else
        {
            $portDecode = $this->enc->decode($portId);
            if( $portDecode != "")
            {
                $data = $this->users_model->getDataExt($portDecode);
            }
            else
            {
                $data = [];
            }
        }
        $return = array("data" => $data,
                                    "tokenHash" => $this->security->get_csrf_hash(),
                                    "csrfName" => $this->security->get_csrf_token_name(),                                    
                                );
        echo json_encode($return);
    }    


}
