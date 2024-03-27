<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller {

  public function __construct() {
    parent::__construct();

    logged_in();

    // $this->load->model('schedule/Schedule_model', 'schedule');
    // $this->load->model('booking/Booking_model', 'booking');
    $this->load->model('home_model', 'home');
    $this->load->helper('nutech_helper');
    $this->load->library('bcrypt');

    $this->_module='home';
    $this->_table='core.t_mtr_user';

  }

  public function index() {
    $data = array(
        'title'   => 'Home',
        'content' => 'home/index',
    );
    $this->load->view('default', $data);
  }

  public function profile() {
    $data = array(
        'title'   => 'Profile',
        'content' => 'home/profile',
        'detail'=>$this->home->get_profile()->row(),
    );
    $this->load->view('default', $data);
  }

  public function edit($id)
  {

      validate_ajax();
      // $this->global_model->checkAccessMenuAction($this->_module,'edit');

      $user_id=$this->enc->decode($id);

      $data['title'] = 'Edit Profil';
      $data['detail']=$this->home->get_profile()->row();
      $this->load->view($this->_module.'/edit',$data);   
  }

  public function action_edit()
  {
    validate_ajax();

    // $user_id=$this->enc->decode($id);

    $first_name=$this->input->post('first_name');
    $last_name=$this->input->post('last_name');
    $no_telpon=$this->input->post('phone');
    $user_id=$this->enc->decode($this->input->post('id'));


    $this->form_validation->set_rules('first_name', 'Nama Depan', 'required');
    // $this->form_validation->set_rules('last_name', 'last_name', 'required');
    $this->form_validation->set_rules('phone', 'phone', 'required');

    $data=array(
            'first_name'=>$first_name,
            'last_name'=>$last_name,
            'phone'=>$no_telpon,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

    if($this->form_validation->run()===false)
    {
        echo $res=json_api(0, 'Data masih ada yang kosong');
    }
    else
    {
      $this->db->trans_begin();
      $this->home->update_data($this->_table,$data,"id=$user_id");

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

  public function change_password($id)
  {

    validate_ajax();
    // $this->global_model->checkAccessMenuAction($this->_module,'edit');

    $user_id=$this->enc->decode($id);

    $data['title'] = 'Ganti Password';
    $data['detail']=$this->home->get_profile()->row();
    $this->load->view($this->_module.'/change_password',$data);   
  }

  public function action_change_password()
  {
    $pass=trim($this->input->post('pass'));
    $newpass=trim($this->input->post('newpass'));
    $repass=trim($this->input->post('repass'));
    $user_id=$this->enc->decode($this->input->post('id'));
    $username=$this->session->userdata('username');

    // encrypt password
    // $this->bcrypt->hash_password(strtoupper(md5($post['password'])));

    $this->form_validation->set_rules('pass', 'Password', 'required');
    $this->form_validation->set_rules('newpass', 'Password Baru', 'required');
    $this->form_validation->set_rules('repass', 'Pasword pengulangan berbeda', 'required');
    $this->form_validation->set_rules('id', 'User Id', 'required');

    $data=array(
                'password'=>$this->bcrypt->hash_password(strtoupper(md5($newpass))),
                'updated_by'=>$this->session->userdata('username'),
                'updated_on'=>date("Y-m-d H:i:s"),
                );

    //ambil data user
    $data_user=$this->home->select_data($this->_table,"where username='".$username."' and status=1")->row();

    if($this->form_validation->run()===false)
    {
        echo $res=json_api(0, 'Data masih ada yang kosong');
    }
    else if(!$this->bcrypt->check_password(strtoupper(md5($pass)), $data_user->password)) // pengecelan password
    {
       echo $res=json_api(0, 'Password yang anda masukan salah');
    }
    else if($newpass!=$repass)
    {
      echo $res=json_api(0, 'Password tidak sama');
    }
    else if ($user_id != $this->session->userdata('id')) // pengecekan jika sudah gantu user tapi masih dalam satu modul
    {
      echo $res=json_api(0, 'Password gagal diubah');
    }
    else
    {
      $this->db->trans_begin();
      $this->home->update_data($this->_table,$data,"id=".$this->session->userdata("id"));

      if ($this->db->trans_status() === FALSE)
      {
          $this->db->trans_rollback();
          echo $res=json_api(0, 'Password gagal diubah');
      }
      else
      {
          $this->db->trans_commit();
          echo $res=json_api(1, 'Password berhasil diubah');
      }   
    }
  }

  public function ambil_data()
  {
     $detail=$this->home->get_profile()->row();
     echo json_encode($detail);
  }



}
