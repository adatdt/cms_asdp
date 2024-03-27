<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/**
 * -----------------
 * CLASS NAME : Ship
 * -----------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Ship extends MY_Controller{
  public function __construct(){
    parent::__construct();

    logged_in();
    $this->load->model('ship_model');
    $this->load->model('global_model');

    $this->_table    = 'app.t_mtr_ship';
    $this->_username = $this->session->userdata('username');
    $this->_module   = 'pelabuhan/ship';
  }

  public function index(){   
    checkUrlAccess(uri_string(),'view');
    if($this->input->is_ajax_request()){
      $this->validate_param_datatable($_POST,$this->_module);
      $rows = $this->ship_model->shipList();
      $rows["tokenHash"] = $this->security->get_csrf_hash();
      $rows["csrfName"] = $this->security->get_csrf_token_name();
      echo json_encode($rows);
      exit;
    }

    $data = array(
      'home'     => 'Home',
      'url_home' => site_url('home'),
      'title'    => 'Kapal',
      'content'  => 'ship/index',
      'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

    );

    $this->load->view('default', $data);
  }

  public function add(){
    validate_ajax();
    $data['title'] = 'Tambah Kapal';
    $data['ship_class']=$this->ship_model->select_data("app.t_mtr_ship_class"," where status=1 order by name asc ")->result();
    $data['ship_company']=$this->ship_model->select_data("app.t_mtr_ship_company"," where status=1 order by name asc ")->result();
    $this->load->view($this->_module.'/add',$data);
  }

  public function action_add(){
    validate_ajax();
    $this->global_model->checkAccessMenuAction($this->_module,'add');
    $this->form_validation->set_rules('name', 'Nama Kapal', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'Nama kapal memuat invalid karakter'));
    $this->form_validation->set_rules('ship_class', 'Kelas Layanan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas layanan'));
    $this->form_validation->set_rules('passenger_cap', 'Kapasitas Penumpang', 'trim|required|numeric|min_length[1]');
    $this->form_validation->set_rules('grt', 'GRT', 'trim|required|numeric');
    $this->form_validation->set_rules('ship_company', 'Perusahaan Kapal', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas layanan'));

    $this->form_validation->set_rules('ship_code', 'Kode Kapal', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'kode kapal memuat invalid karakter'));
    $this->form_validation->set_rules('vehicle_cap', 'Kapasitas Kendaraan', 'trim|required|numeric|min_length[1]');
    $this->form_validation->set_rules('shipName', 'Gambar Kapal', 'trim');
    $this->form_validation->set_message('required','%s harus diisi!')
          ->set_message('numeric', '%s harus angka!');

     /* validation */
  
    $data = [];
    if($this->form_validation->run() == FALSE){
       echo $res = json_api(0, validation_errors());   
       exit;
    }

    $post = $this->input->post();
    $name = strtoupper(trim($post['name']));
    $ship_class_id= $this->enc->decode($post['ship_class']);
    $ship_company_id= $this->enc->decode($post['ship_company']);
    $ship_code= trim($post['ship_code']);


    

    /* data post */
    $data = array(
      'name' => trim($post['name']),
      'ship_class' => $ship_class_id,
      'ship_code' => $ship_code,
      'ship_company_id' => $ship_company_id,
      'grt' => trim($post['grt']),
      'people_capacity' => trim($post['passenger_cap']),
      'vehicle_capacity' => trim($post['vehicle_cap']),
      'status'=>1,
      'created_on'=>date('Y-m-d H:i:s'),
      'created_by'=>$this->session->userdata("username"),
    );

    // $check = $this->global_model->checkData($this->_table,array('UPPER(name)' => $name));
    $check = $this->ship_model->select_data($this->_table," where UPPER(name)='".$name."' ");

    // echo print_r($check->result());

    if($check->num_rows()>0){
      echo $res =  json_api(0,'Nama kapal sudah ada'); 
    }
    else
    {
            $this->db->trans_begin();
            
            $this->ship_model->insert_data($this->_table,$data);
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
        $logUrl      = site_url().'pelabuhan/ship/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
  }

  public function edit($param){
    validate_ajax();
    $data['id']    = $param;
    $data['row']   = $this->global_model->selectById($this->_table, 'id', $this->enc->decode($param));
    $data['title'] = 'Edit Kapal';
    $data['ship_class']=$this->ship_model->select_data("app.t_mtr_ship_class"," where status=1 order by name asc ")->result();
    $data['ship_company']=$this->ship_model->select_data("app.t_mtr_ship_company"," where status=1 order by name asc ")->result();
    $this->load->view($this->_module.'/edit',$data);
  }

  public function action_edit(){
    validate_ajax();
    $this->global_model->checkAccessMenuAction($this->_module,'edit');
    $this->form_validation->set_rules('id', 'id', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid id'));
    $this->form_validation->set_rules('name', 'Nama Kapal', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'Nama kapal memuat invalid karakter'));
    $this->form_validation->set_rules('ship_class', 'Kelas Layanan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas layanan'));
    $this->form_validation->set_rules('passenger_cap', 'Kapasitas Penumpang', 'trim|required|numeric|min_length[1]');
    $this->form_validation->set_rules('grt', 'GRT', 'trim|required|numeric');
    $this->form_validation->set_rules('ship_company', 'Perusahaan Kapal', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas layanan'));

    $this->form_validation->set_rules('ship_code', 'Kode Kapal', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'kode kapal memuat invalid karakter'));
    $this->form_validation->set_rules('vehicle_cap', 'Kapasitas Kendaraan', 'trim|required|numeric|min_length[1]');
    $this->form_validation->set_rules('shipName', 'Gambar Kapal', 'trim');
    $this->form_validation->set_message('required','%s harus diisi!')
          ->set_message('numeric', '%s harus angka!');
    $data = [];
    if ($this->form_validation->run() == FALSE) {
      echo $res = json_api(0, validation_errors());
      exit;
    }


    $post = $this->input->post();
    $name = strtoupper(trim($post['name']));
    $ship_class_id= $this->enc->decode($post['ship_class']);
    $ship_company= $this->enc->decode($post['ship_company']);
    $grt= trim($post['grt']);
    $ship_code= trim($post['ship_code']);

   
    $id = $this->enc->decode($post['id']);

    /* data post */
    $data = array(
      'name' => trim($post['name']),
      'ship_class'=>$ship_class_id,
      'ship_code'=>$ship_code,
      'people_capacity' => trim($post['passenger_cap']),
      'vehicle_capacity' => trim($post['vehicle_cap']),
      'grt' => $grt,
      'ship_company_id' =>$ship_company,
      'updated_by' =>$this->session->userdata("username"),
      'updated_on' =>date("Y-m-d H:i:s"),
    );

    // $check = $this->global_model->checkData($this->_table,array('UPPER(name)' => $name));
    $check = $this->ship_model->select_data($this->_table," where UPPER(name)='".$name."' and id !={$id} ");

    // echo print_r($check->result());

    if($this->form_validation->run() == FALSE){
      echo $res = json_api(0,validation_errors());
    }
    else if($check->num_rows()>0){
      echo $res =  json_api(0,'Nama kapal sudah ada'); 
    }
    else
    {
            $this->db->trans_begin();
            
            $this->ship_model->update_data($this->_table,$data,"id={$id}");
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
        $logUrl      = site_url().'pelabuhan/ship/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
  }

  public function action_delete($param){
    validate_ajax();
    $this->global_model->checkAccessMenuAction($this->_module,'delete');

    $id   = $this->enc->decode($param);

    /* data */
    $data = array(
      'status' => -5,
      'updated_by'=>$this->session->userdata("username"),
      'updated_on'=>date("Y-m-d H:i:s "),
    );

        $this->db->trans_begin();
        
        $this->ship_model->update_data($this->_table,$data,"id={$id}");
        
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

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/ship/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
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
          $this->ship_model->update_data($this->_table,$data,"id=".$this->enc->decode($d[0]));

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
      $logUrl      = site_url().'pelabuhan/ship/enable';
      $logMethod   = 'Enable';
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
      $this->ship_model->update_data($this->_table,$data,"id=".$this->enc->decode($d[0]));

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
      $logUrl      = site_url().'pelabuhan/member/enable';
      $logMethod   = 'Disable';
      $logParam    = json_encode($data);
      $logResponse = $res;

      $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
  }


}
