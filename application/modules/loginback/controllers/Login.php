<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model', 'login');
        $this->load->library(array('bcrypt', 'curl', 'form_validation', 'session'));
        $this->load->helper('captcha');
    }

    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('home');
            // return flase;
            // return false;
        }

        $chaptchaUrl=site_url().'login/capcay';
        $chaptcha="<img src='{$chaptchaUrl}'' id='ratcha_image' width='100%' height='35ppx'>";
        $data['chaptcha']=$chaptcha;

        $this->load->view('login', $data);
    }

    public function getChaptcha()
    {
        $dateTime=date('YmdHis');
        $chaptchaUrl=site_url().'login/capcay?='.$dateTime;
        $chaptcha="<img src='{$chaptchaUrl}'' id='ratcha_image'  width='100%' height='35px'>";

        echo  json_encode($chaptcha);
    }

    public function capcay()
    {
        $this->load->view('ratcha_image');
    }
    
    public function do_login()
    {
    
        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));
        $ratcha   = strtolower(trim($this->input->post('ratcha')));

        $getUser = $this->login->check_user($username);
        $user = $getUser->row();
        $getAppId = $this->login->identity_app();
        $appId = $getAppId->row();        
        $data = array("username" => $username);

        $checkAksesCms[]=0;
        $checkAssigmentPtcStc[]=0;
        $checkPasswordUser[]=0;
        $getPortUser="";
        if($getUser->num_rows()>0)
        {
            if($user->admin_pannel_login == false)
            {
                // jika dia tidak punya akses cms centang maka dia tidak bisa masuk
                $checkAksesCms[]=1;
            }

            $getPortUser=$user->port_id;

            $checkUserPtc = $this->login->check_user_ptc($user->id);

            // jika login ptc
            if($checkUserPtc->num_rows()>0)
            {
                $check_assignment_ptc = $this->login->check_assignment_ptc($user->port_id, $user->id); 
                // check apakah sudah di assignment
                if(empty($check_assignment_ptc))
                {
                    $checkAssigmentPtcStc[]=1;
                }  
            }

            $checkPass = $this->bcrypt->check_password(strtoupper(md5($password)), $user->password);

            if(!$checkPass)
            {
                $checkPasswordUser[]=1;
            }            
        }


        $emptyIdentity[]=0; 
        $checkAksesServer[]=0; 

        if ($getAppId->num_rows()>0)
        {
            if($appId->port_id==null || $appId->port_id=="")
            {
                $emptyIdentity[]=1; // jika identity appnya tidak diisi maka tidak bisa login 
            }
            else
            {
                // ceck apakah ini aplikasi cloude atau bukan
                if($appId->port_id<>0)
                {
                    // checking apakah user dengan applikasinya portnya sama kecuali semua pelabuhan atau aplikasi cloude  

                    if($getPortUser != null or $getPortUser != '' )
                    {
                        if($getPortUser != $appId->port_id)
                        {
                            $checkAksesServer[]=1;
                        }
                    }
                }
            }
        }      
        
        // pengecekan chapcha
        if ($this->session->userdata('ratcha') <> $ratcha) {
            $message['error'] = 'Captcha tidak cocok.';
            $res = array("code" => "0", "message" => $message['error']);
        }
        // pengecekan username apakah di ada usernamenya
        else if ($getUser->num_rows()<1) 
        {
            $message['error'] = 'Username Tidak ditemukan. ';
            $res = array("code" => "0", "message" => $message['error']);
        }
        else if (array_sum($checkAksesCms)>0) 
        {
            $message['error'] = 'Anda tidak punya akses. ';
            $res = array("code" => "0", "message" => $message['error']);
        }
        // cek identity app
        else if ($getAppId->num_rows()<1) 
        {
            $message['error'] = 'Aplikasi tidak teridentifikasi';
            $res = array("code" => "0", "message" => $message['error']);
        }
        else if(array_sum($emptyIdentity)>0)
        {
            $message['error'] = 'Aplikasi tidak teridentifikasi';
            $res = array("code" => "0", "message" => $message['error']);
        }

        // pengecekan port && identity app
        else if (array_sum($checkAksesServer)>0) {
            $message['error'] = 'Anda tidak punya akses di server ';
            $res = array("code" => "0", "message" => $message['error']);
        }
        else if(array_sum($checkAssigmentPtcStc)>0)
        {
            $message['error'] = 'User PTC/ STC belum di assignment. ';
            $res = array("code" => "0", "message" => $message['error']);
        }
        else if(array_sum($checkPasswordUser)>0)
        {
            $message['error'] = 'Username Atau Password Tidak Cocok. ';
            $res = array("code" => "0", "message" => $message['error']);
        }
        else
        {
            $session = array(
                'logged_in'    => 1,
                'id'           => $user->id,
                'group_id'     => $user->group_id,
                'firstname'    => $user->first_name,
                'lastname'     => $user->last_name,
                'username'     => $user->username,
                'port_id'      => $user->port_id,
                'ship_class_id'      => $user->ship_class_id,
                'stc_ship_date' => "",
            );

            $this->session->set_userdata($session);
            $this->session->set_userdata(array("listMenu" => listMenu()));

            $message['success'] = 'Login success. ';
            $res = array("code" => "1", "message" => $message['success']);
        }

        $created_by   = $username; // karna belum ada session maka di ambil dari inputanuya
        $log_url      = site_url() . 'login';
        $log_method   = 'login';
        $log_param    = json_encode($data);
        $log_response = json_encode($res);

        $this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);

        echo json_encode($message);
    }    

    public function do_logout()
    {
        $created_by   = $this->session->userdata('username');
        $log_url      = site_url() . 'logout';
        $log_method   = 'logout';
        $log_param    = '';
        $log_response = json_encode(array("code" => "1", "message" => "Berhasil logout"));
        $this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
        $this->session->unset_userdata('logged_in');
        $this->session->sess_destroy();
        redirect('login');
    }
}
