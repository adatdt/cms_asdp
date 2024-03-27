<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('login_model', 'login');
        $this->load->library(array('bcrypt', 'curl', 'form_validation', 'session'));
        $this->load->helper('captcha');
    }

	public function index(){
        if ($this->session->userdata('logged_in')){
            redirect('home');
            // return flase;
            // return false;
        }
		$this->load->view('login');
	}

    public function capcay(){
        $this->load->view('ratcha_image');
    }

    public function do_login()
    {
        //$username = strtolower(trim($this->input->post('username')));

        $username = trim($this->input->post('username'));
		$password = trim($this->input->post('password'));
        $ratcha   = strtolower(trim($this->input->post('ratcha')));

        $user=$this->login->check_user($username)->row();
        $app_id=$this->login->identity_app();

        // echo count($user); exit;

        $data=array("username"=>$username);

        // $check_login_cms=$this->login->select_data("core.t_mtr_user"," where username='".$username."' and status=1")->row();

        // pengecekan chapcha
        if ($this->session->userdata('ratcha') <> $ratcha) {
            $message['error'] = 'Captcha tidak cocok.';
            echo json_encode($message);
            $res=array("code"=>"0","message"=>$message['error']);
        }
        // pengecekan user
        // else if(is_array($user) && count($user) < 1)
        else if(count($user) < 1)
        {
            $message['error'] = 'Username Atau Password  Tidak Cocok. ';
            echo json_encode($message);
            $res=array("code"=>"0","message"=>$message['error']);
        }
        else if($user->admin_pannel_login == false)
        {
            $message['error'] = 'Anda tidak punya akses. ';
            echo json_encode($message);
            $res=array("code"=>"0","message"=>$message['error']);   
        }
        // cek identity app
        else if(!$app_id)
        {
            $message['error'] = 'Aplikasi tidak teridentifikasi';
            echo json_encode($message);
            $res=array("code"=>"0","message"=>$message['error']);
        }

        // cek port
        else if($app_id->port_id AND !$this->global_model->selectById('app.t_mtr_port', 'id', $app_id->port_id))
        {
            $message['error'] = 'Pelabuhan tidak ditemukan';
            echo json_encode($message);
            $res=array("code"=>"0","message"=>$message['error']);
        }

        // pengecekan port && identity app
        elseif($user->port_id AND $app_id->port_id AND $user->port_id != $app_id->port_id)
        {   
            $message['error'] = 'Anda tidak punya akses di server ';
            if ($app_id->port_id) 
            {
                $message['error'] .= $this->global_model->selectById('app.t_mtr_port', 'id', $app_id->port_id)->name;
            } 
            else 
            {
                $message['error'] .= 'ini';
            }

            echo json_encode($message);
            $res=array("code"=>"0","message"=>$message['error']);
        }        

        else
        {
            $check_user_ptc=$this->login->check_user_ptc($user->id);

            $check_pass=$this->bcrypt->check_password(strtoupper(md5($password)),$user->password);
            if(!$check_pass)
            {
                $message['error'] = 'Username Atau Password Tidak Cocok. ';
                echo json_encode($message);
                $res=array("code"=>"0","message"=>$message['error']);

                $created_by   = $username; // karna belum ada session maka di ambil dari inputanuya
                $log_url      = site_url().'login';
                $log_method   = 'login';
                $log_param    = json_encode($data);
                $log_response = json_encode($res); 

                $this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);                
                exit;   
            }

            // jika loginya login ptc
            if(is_array($check_user_ptc) && count($check_user_ptc) > 0)
            {
                $check_assignment_ptc=$this->login->check_assignment_ptc($user->port_id,$user->id);

                if(empty($check_assignment_ptc))
                {
                    $message['error'] = 'User PTC/ STC belum di assignment. ';
                    echo json_encode($message);
                    $res=array("code"=>"0","message"=>$message['error']);
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
                        'stc_ship_date'=> "",
                    );

                    $this->session->set_userdata($session);

                    $message['success'] = 'Login success. ';
                    echo json_encode($message);
                    $res=array("code"=>"1","message"=>$message['success']);            
                } 
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
                    'stc_ship_date'=> "",
                );

                $this->session->set_userdata($session);

                $message['success'] = 'Login success. ';
                echo json_encode($message);
                $res=array("code"=>"1","message"=>$message['success']);                   
            }
            
        }

        $created_by   = $username; // karna belum ada session maka di ambil dari inputanuya
        $log_url      = site_url().'login';
        $log_method   = 'login';
        $log_param    = json_encode($data);
        $log_response = json_encode($res); 

        $this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
    }

    public function do_logout(){
        $created_by   = $this->session->userdata('username');
        $log_url      = site_url().'logout';
        $log_method   = 'logout';
        $log_param    = '';
        $log_response = json_encode(array("code"=>"1","message"=>"Berhasil logout"));    
        $this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
        $this->session->unset_userdata('logged_in');
        $this->session->sess_destroy();
        redirect('login');
    }

}
