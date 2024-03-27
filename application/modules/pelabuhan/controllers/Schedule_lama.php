<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Schedule extends MY_Controller {
  public function __construct() {
    parent::__construct();
    logged_in();

    $this->load->model('pelabuhan/schedule_model', 'schedule');
    $this->load->model('pelabuhan/port_model', 'port');
    $this->_table     = 'app.t_mtr_schedule';
    $this->_username  = $this->session->userdata('username');
    $this->_module    = 'pelabuhan/schedule';
  }

  public function index() {
    checkUrlAccess(uri_string(),'view');
    if ($this->input->is_ajax_request()) {
      $rows = $this->schedule->scheduleList();
      echo json_encode($rows);
      exit;
    } 

    $data = array(
      'home'     => 'Home',
      'url_home' => site_url('home'),
      'title'    => 'Jadwal',
      'content'  => 'schedule/index',
      'port'	   => $this->port_origin(),
      'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
    );

    $this->load->view('default', $data);
  }

  public function add() {
    validate_ajax();

    $data  = array(
      'title'         => 'Tambah Jadwal',
      'vehicle_class' => $this->schedule->select_all('app.t_mtr_vehicle_class'),
      'passenger'     => $this->schedule->select_all('app.t_mtr_passanger_type'),
      'origin'        => $this->port_origin(),      
    );

    $this->load->view($this->_module.'/add', $data);
  }

  public function edit($param='') {
    validate_ajax();
    $id        = $this->enc->decode($param);
    $schedule  = $this->schedule->getById($id)->row();

    $data  = array(
      'title'       => 'Detail Jadwal',
      'id'          => $param,
      'vehicle_class'=> $this->schedule->select_all('app.t_mtr_vehicle_class'),
      'passenger'   => $this->schedule->select_all('app.t_mtr_passanger_type'),
      'origin'      => $this->port_origin(),  
      'schedule'    => $schedule,
      'destination' => $this->port_destination($schedule->origin_port_id),
      'vehicle_fare'=> $this->schedule->getVehiclefare($id)->result(),
      'get_time'    => $this->schedule->get_time_edit($id)->result(),
    );

    $this->load->view($this->_module.'/edit', $data);
  }

  public function detail($param='') {
    checkUrlAccess($this->_module,'detail');
    $id    = $this->enc->decode($param);

    $schedule=$this->schedule->getById($id);
    $vehicle_fare=$this->schedule->getVehiclefare($id);
    $get_time=$this->schedule->getTime($id);

    // redirect halaman 401 jika query error
    if(!$schedule || !$vehicle_fare || !$get_time)
    {
      redirect('error_401');
      exit;
    }

    $data  = array(
      'home'        => 'Home',
      'url_home'    => site_url('home'),
      'parent'      => 'Jadwal',
      'url_parent'  => '#',
      'parent1'     => 'Jadwal keberangkatan',
      'url_parent1' => site_url('pelabuhan/schedule'),
      'title'       => 'Detail Jadwal keberangkatan',
      'schedule'    => $schedule->row(),
      'vehicle_fare'=> $vehicle_fare->result(),
      'get_time'    => $get_time->result(),
      'content'     => 'schedule/detail',
    );
    

    $this->load->view('default', $data);
  }

  public function action_add(){
    validate_ajax();
    $this->global_model->checkAccessMenuAction($this->_module,'add');

    $post = $this->input->post();

    /* validation */
    $this->form_validation
      ->set_rules('origin', 'Pelabuhan Asal', 'trim|required')
      ->set_rules('destination', 'Pelabuhan Tujuan', 'trim|required')
      ->set_rules('dewasa', 'Tarif Dewasa', 'trim|required')
      ->set_rules('anak', 'Tarif Anak', 'trim|required')
      ->set_rules('bayi', 'Tarif Bayi', 'trim|required');

    foreach($post['vehicle'] as $key => $vehicle){
      $row = $this->global_model->selectById('app.t_mtr_vehicle_class', 'id', $key);
      $this->form_validation->set_rules('vehicle['.$key.']', 'Tarif kendaraan #'.$row->name.' tidak boleh Kosong', 'trim|required');
    }

    $this->form_validation->set_message('required','%s harus diisi!');
    $origin = $post['origin'];
    $destination = $post['destination'];
    $data_vehicle = array();
    $data_schedule = array(
      'ship_id' => 0,
      'origin_port_id' => $origin,
      'destination_port_id' => $destination,
      'adult_fare' => str_replace('.', '', $post['dewasa']),
      'child_fare' => str_replace('.', '', $post['anak']),
      'infant_fare' => str_replace('.', '', $post['bayi'])
    );
    
    if($this->form_validation->run() == FALSE){
      $response = json_api(0,validation_errors());
    }else{
      $where = array(
        'origin_port_id' => $origin,
        'destination_port_id' => $destination
      );

      $check = $this->global_model->checkData($this->_table,$where);

      if(empty($post['hours'])){
        $response = json_api(0,'Silahkan pilih salah satu waktu keberangkatan');
      }elseif($check){
        $response =  json_api(0,'Jadwal Sudah Ada'); 
      }else{
        $schedule = $this->global_model->saveData($this->_table, $data_schedule);
        if($schedule){
          foreach($post['hours'] as $key => $hours){
            $data_hours[] = array(
              'schedule_id' => $schedule,
              'departure' => $hours
            );
          }
          $this->global_model->insertBatch('app.t_mtr_schedule_time', $data_hours);

          foreach($post['vehicle'] as $key => $vehicle){
            $data_vehicle[] = array(
              'schedule_id' => $schedule,
              'vehicle_class_id' => $key,
              'fare' => str_replace('.', '', $vehicle)
            );
          }

          if($data_vehicle){
            $this->global_model->insertBatch('app.t_mtr_fare_vehicle', $data_vehicle);
          }

          $response = json_api(1,'Simpan Data Berhasil');
        }else{
          $response = json_encode($this->db->error()); 
        }
      }
    }

    $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($post), $response); 
    echo $response;
  }

  public function action_edit(){
    validate_ajax();
    $this->global_model->checkAccessMenuAction($this->_module,'edit');

    $post = $this->input->post();
    $id = $this->enc->decode($post['id']);

    /* validation */
    $this->form_validation
      ->set_rules('id', 'ID', 'trim|required')
      ->set_rules('origin', 'Pelabuhan Asal', 'trim|required')
      ->set_rules('destination', 'Pelabuhan Tujuan', 'trim|required')
      ->set_rules('dewasa', 'Tarif Dewasa', 'trim|required')
      ->set_rules('anak', 'Tarif Anak', 'trim|required')
      ->set_rules('bayi', 'Tarif Bayi', 'trim|required');

    foreach($post['vehicle'] as $key => $vehicle){
      $row = $this->global_model->selectById('app.t_mtr_vehicle_class', 'id', $key);
      $this->form_validation->set_rules('vehicle['.$key.']', 'Tarif kendaraan #'.$row->name.' tidak boleh Kosong', 'trim|required');
    }

    $this->form_validation->set_message('required','%s harus diisi!');

    $origin = $post['origin'];
    $destination = $post['destination'];
    $data_vehicle = array();
    $data_schedule = array(
      'id' => $id,
      'ship_id' => 0,
      'origin_port_id' => $origin,
      'destination_port_id' => $destination,
      'adult_fare' => str_replace('.', '', $post['dewasa']),
      'child_fare' => str_replace('.', '', $post['anak']),
      'infant_fare' => str_replace('.', '', $post['bayi'])
    );

    if($this->form_validation->run() == FALSE){
      $response = json_api(0,validation_errors());
    }elseif(empty($post['hours'])){
      $response = json_api(0,'Silahkan pilih salah satu waktu keberangkatan');
    }else{
      $where = array(
        'origin_port_id' => $origin,
        'destination_port_id' => $destination
      );
      $check = $this->global_model->checkData($this->_table,$where,'id',$id);

      if($check){
        $response =  json_api(0,'Jadwal Sudah Ada'); 
      }else{
        $schedule = $this->global_model->updateData($this->_table, $data_schedule, 'id');
        if($schedule){
          $time = $this->schedule->getTime($id)->result();
            
          $dataTime = array();
          if($time){
            foreach($time as $row){
              $dataTime[] = $row->departure;
            }
          }

          $diffTime = array_diff($dataTime,$post['hours']);
          if($diffTime){
            foreach ($diffTime as $diff) {
              $this->schedule->update_schedule_time($id,$diff,0);
            }
          }

          $data_hours = array();
          foreach($post['hours'] as $hr){
            $checkTime = $this->schedule->get_time($id,$hr);
            if($checkTime){
              $this->schedule->update_schedule_time($id,$hr,1);
            }else{
              $data_hours[] = array(
                'schedule_id' => $id,
                'departure' => $hr
              );
            }
          }

          if($data_hours){
            $this->global_model->insertBatch('app.t_mtr_schedule_time', $data_hours);
          }

          foreach($post['vehicle'] as $key => $vehicle){
            $this->schedule->update_fare_vehicle($id,$key,str_replace('.', '', $vehicle));
          }

          $response = json_api(1,'Update Data Berhasil');
        }else{
          $response = json_encode($this->db->error()); 
        }
      }
    }

    $this->log_activitytxt->createLog($this->_username, uri_string(), 'update', json_encode($data), $response); 
    echo $response;
  }

  public function action_delete($param){
    validate_ajax();
    $this->global_model->checkAccessMenuAction($this->_module,'delete');

    $id = $this->enc->decode($param);

    /* data */
    $data = array(
      'id' => $id,
      'status' => -5
    );

    $data_ = array(
      'schedule_id' => $id,
      'status' => -5
    );

    $query = $this->global_model->updateData($this->_table, $data, 'id');
    if($query){
      $this->global_model->updateData('app.t_mtr_schedule_time', $data_, 'schedule_id');
      $this->global_model->updateData('app.t_mtr_fare_vehicle', $data_, 'schedule_id');

      $response = json_api(1,'Delete Data Berhasil');
    }else{
      $response = json_encode($this->db->error()); 
    }

    $this->log_activitytxt->createLog($this->_username, uri_string(), 'delete', json_encode($data), $response); 
    echo $response;
  }

  public function port_origin(){
    $datas  = $this->global_model->selectAll('app.t_mtr_port');

    $data[''] = '';
    if($datas){
      foreach($datas as $row){
        $data[$row->id] = strtoupper($row->name);
      }
    }
    
    return $data;
  }

  public function port_destination($id=''){
    if($id != ''){
      $datas  = $this->schedule->port_list($id);;

      $data[''] = '';
      if($datas){
        foreach($datas as $row){
          $data[$row->id] = strtoupper($row->name);
        }
      }
      
      return $data;
    }else{
      validate_ajax();
      $id    = $this->input->post('id');
      $arr[] = array('id' => '', 'text' => '');
      $arr2  = array();
      $port  = $this->schedule->port_list($id);

      if($port){
        foreach ($port as $i => $row) {
          $arr2[$i]['id']   = $row->id;
          $arr2[$i]['text'] = strtoupper($row->name);
        }
      }

      echo json_api(1,'List port',array_merge($arr,$arr2));
    }
  }
}
