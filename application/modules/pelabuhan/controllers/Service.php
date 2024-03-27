<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/**
 * --------------------
 * CLASS NAME : Service
 * --------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Service extends MY_Controller{
	public function __construct(){
		parent::__construct();

    logged_in();
    $this->load->model('service_model');
    $this->load->model('global_model');

    $this->_table    = 'app.t_mtr_service';
    $this->_username = $this->session->userdata('username');
    $this->_module   = 'pelabuhan/service';
  }
  
  public function index(){
    checkUrlAccess(uri_string(),'view');
    if ($this->input->is_ajax_request()) {
      $this->validate_param_datatable($_POST,$this->_module);
      $rows = $this->service_model->serviceList();
      $rows["tokenHash"] = $this->security->get_csrf_hash();
      $rows["csrfName"] = $this->security->get_csrf_token_name();

      echo json_encode($rows);
      exit;
    }

    $data = array(
      'home'     => 'Home',
      'url_home' => site_url('home'),
      'title'    => 'Layanan',
      'content'  => 'service/index',
      'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
    );

    $this->load->view('default', $data);
  }

  public function add(){
    validate_ajax();
    $data['title'] = 'Tambah Layanan';
    $this->load->view($this->_module.'/add',$data);
  }

  public function edit($param){
    validate_ajax();
    $data['id']    = $param;
    $data['row']   = $this->global_model->selectById($this->_table, 'id', $this->enc->decode($param));
    $data['title'] = 'Edit Layanan';
    $this->load->view($this->_module.'/edit',$data);
  }

  public function action_add(){
    validate_ajax();
    $this->global_model->checkAccessMenuAction($this->_module,'add');

    $this->form_validation->set_rules('name', 'Nama Layanan', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'nama layanan memuat invalid karakter'));

    $this->form_validation->set_message('required','%s harus diisi!');
    $data = [];
    if($this->form_validation->run() == FALSE){
      echo $response = json_api(0,validation_errors());
      exit;
    }

    $post = $this->input->post();
    $name = strtoupper($post['name']);

    

    /* data post */
    $data = array(
      'name' => $post['name'],
    );

    $check = $this->global_model->checkData($this->_table,array('UPPER(name)' => $name));

    if($check){
      $response =  json_api(0,'Nama Layanan '.$post['name'].' Sudah Ada'); 
    }else{
      $query = $this->global_model->saveData($this->_table, $data);
      if($query){
        $response = json_api(1,'Simpan Data Berhasil');
      }else{
        $response = json_encode($this->db->error()); 
      }
    }

    $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data), $response); 
    echo $response;
  }

  public function action_edit(){
    validate_ajax();
    $this->global_model->checkAccessMenuAction($this->_module,'edit');

    /* validation */
    $this->form_validation->set_rules('id', 'ID Kapal', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid id'));
    $this->form_validation->set_rules('name', 'Nama Layanan', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'nama layanan memuat invalid karakter'));

    $this->form_validation->set_message('required','%s harus diisi!');
    $data =[];
    if($this->form_validation->run() == FALSE){
      echo $response = json_api(0,validation_errors());
      exit;
    }

    $post = $this->input->post();
    $name = strtoupper($post['name']);

    /* validation */
    $this->form_validation
    ->set_rules('id', 'ID Kapal', 'trim|required')
    ->set_rules('name', 'Nama Kapal', 'trim|required');

    $this->form_validation->set_message('required','%s harus diisi!');

    $id = $this->enc->decode($post['id']);

    /* data post */
    $data = array(
      'id' => $id,
      'name' => $post['name']
    );

    $check = $this->global_model->checkData(
      $this->_table,
      array('UPPER(name)' => $name),
      'id',$id
    );

    if($check){
      $response =  json_api(0,'Nama Layanan '.$post['name'].' Sudah Ada'); 
    }else{
      $query = $this->global_model->updateData($this->_table, $data, 'id');
      if($query){
        $response = json_api(1,'Update Data Berhasil');
      }else{
        $response = json_encode($this->db->error()); 
      }
    }

    $this->log_activitytxt->createLog($this->_username, uri_string(), 'update', json_encode($data), $response); 
    echo $response;
  }

  public function action_delete($param){
    validate_ajax();
    $this->global_model->checkAccessMenuAction($this->_module,'delete');

    $id   = $this->enc->decode($param);

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

    $this->log_activitytxt->createLog($this->_username, uri_string(), 'delete', json_encode($data), $response); 
    echo $response;
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
          $this->service_model->update_data($this->_table,$data,"id=".$this->enc->decode($d[0]));

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
      $logUrl      = site_url().'pelabuhan/service/enable';
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
      $this->service_model->update_data($this->_table,$data,"id=".$this->enc->decode($d[0]));

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
      $logUrl      = site_url().'pelabuhan/service/enable';
      $logMethod   = 'Disable';
      $logParam    = json_encode($data);
      $logResponse = $res;

      $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
  }

}
