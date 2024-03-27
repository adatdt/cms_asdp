<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule_model extends MY_Model {

  public function __construct() {
    parent::__construct();
    $this->_module = 'pelabuhan/schedule';
  }

  public function scheduleList() {
    $start      = $this->input->post('start');
    $length     = $this->input->post('length');
    $draw       = $this->input->post('draw');
    $search     = $this->input->post('search');
    $order      = $this->input->post('order');
    $origin     = $this->input->post('origin');
    $destination= $this->input->post('destination');
    $order_column = $order[0]['column'];
    $order_dir  = strtoupper($order[0]['dir']);
    // $iLike      = trim(strtoupper($this->db->escape_like_str($search)));

    $field = array(
      1 => 'origin_name',
      2 => 'destination_name',
      3 => 'ship_name'
    );

    $order_column = $field[$order_column];
    $where = "WHERE a.status=1 ";

    // if (!empty($search)){
    //   $where .= " AND (b.name ilike '%".$iLike."%'
    //     or c.name ilike '%".$iLike."%'
    //     or d.name ilike '%".$iLike."%')";
    // }

    if (!empty($origin) && !empty($destination)){
      $where .= " AND c.id = ".$origin." AND d.id = ".$destination."";
    }

    if (!empty($origin) && empty($destination)){
      $where .= " AND c.id = ".$origin."";
    }

    if (empty($origin) && !empty($destination)){
      $where .= " AND d.id = ".$destination."";
    }

    $sql = "SELECT a.id, d.name AS origin_name,c.name AS destination_name, b.name AS ship_name FROM app.t_mtr_schedule a 
        		LEFT JOIN app.t_mtr_ship b ON a.ship_id = b.id
        		LEFT JOIN app.t_mtr_port c ON a.origin_port_id = c.id
        		LEFT JOIN app.t_mtr_port d ON a.destination_port_id = d.id {$where}";

    $query = $this->db->query($sql);
    $records_total = $query->num_rows();

    $sql .=" ORDER BY " . $order_column . " {$order_dir}";

    if ($length != -1) {
      $sql .=" LIMIT {$length} OFFSET {$start}";
    }

    $query = $this->db->query($sql);
    $rows_data = $query->result();

    $rows = array();
    $i = ($start + 1);

    foreach ($rows_data as $row) {
      $row->number = $i;
      $row->id = $this->enc->encode($row->id);
      $edit_url   = site_url($this->_module."/edit/{$row->id}");
      $delete_url = site_url($this->_module."/action_delete/{$row->id}");
      $detail_url = site_url($this->_module."/detail/{$row->id}");

      $row->actions  = generate_button_new($this->_module, 'edit', $edit_url);
      $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
      $row->actions .= generate_button_new($this->_module, 'detail', $detail_url);
      
      $rows[] = $row;
      unset($row->id);
      
      $i++;
    }

    return array(
      'draw' => $draw,
      'recordsTotal' => $records_total,
      'recordsFiltered' => $records_total,
      'data' => $rows
    );
  }

  public function port_list($id) {
    $this->db->where('status', 1);
    $this->db->where("id != {$id}");
    return $this->db->get('app.t_mtr_port')->result();
  }

  public function getById($id)
  {
  	return  $this->db->query("
  		SELECT b.name as ship_name, b.vehicle_capacity, b.people_capacity, c.name AS origin_name, d.name AS dest_name, a.*  FROM app.t_mtr_schedule a 
  		LEFT JOIN app.t_mtr_ship b ON a.ship_id = b.id
  		LEFT JOIN app.t_mtr_port c ON a.origin_port_id = c.id
  		LEFT JOIN app.t_mtr_port d ON a.destination_port_id = d.id WHERE a.id = $id");
  }
  
  public function getVehiclefare($id){
  	return $this->db->query("
  		SELECT a.*, b.id AS vehicle_id, b.name AS vehicle_name FROM app.t_mtr_fare_vehicle a
  		LEFT JOIN app.t_mtr_vehicle_class b ON a.vehicle_class_id = b.id WHERE a.schedule_id = $id ORDER BY b.id ASC");
  }
  
  public function getTime($id){
  	return $this->db->query("SELECT * FROM app.t_mtr_schedule_time WHERE schedule_id = $id AND status = 1 ORDER BY departure ASC");
  }

  public function get_time_edit($id){
    return $this->db->query("SELECT * FROM app.t_mtr_schedule_time WHERE schedule_id = $id");
  }

  public function get_time($id,$time){
    return $this->db->query("SELECT * FROM app.t_mtr_schedule_time WHERE schedule_id = $id AND departure = '{$time}'")->row();
  }

  public function select_all($table) {
    $this->db->where('status', 1);
    $this->db->order_by('id', 'ASC');
    return $this->db->get($table)->result();
  }

  public function update_schedule_time($schedule_id,$time,$status){
    if ($this->input->is_cli_request()){
      $user = 0;
    }else{
      $user = $this->session->userdata('id');
    }

    $data = array(
      'status' => $status,
      'updated_by' => $user,
      'updated_on' => date('Y-m-d H:i:s')
    );

    $this->db->where('schedule_id',$schedule_id);
    $this->db->where('departure',$time);
    $this->db->update('app.t_mtr_schedule_time', $data);
    if($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return false;
    }else{
      return true;
    }
  } 

  public function update_fare_vehicle($schedule_id,$class_id,$fare){
    if ($this->input->is_cli_request()){
      $user = 0;
    }else{
      $user = $this->session->userdata('id');
    }

    $data = array(
      'fare' => $fare,
      'updated_by' => $user,
      'updated_on' => date('Y-m-d H:i:s')
    );

    $this->db->where('schedule_id',$schedule_id);
    $this->db->where('vehicle_class_id',$class_id);
    $this->db->update('app.t_mtr_fare_vehicle', $data);
    if($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return false;
    }else{
      return true;
    }
  } 
}
