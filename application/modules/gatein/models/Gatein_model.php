<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Gatein_model extends CI_Model {


  public function gateInList() 
  {
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
    $gatein = $this->input->post('gatein');
    $departdate = $this->input->post('departdate');
    //$dateFrom = $this->input->post('dateFrom');
    //$dateTo = $this->input->post('dateTo');
    $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

    $field = array(
        1 => 'code',
        2 => 'ticket_number',
		3 =>'name_checkin',
		 4 =>'id_number',
		 5 =>'birth_date',
		 6 =>'gender',
		 7 =>'gatein_date',
		 8 =>'date_time',
    );

    $order_column = $field[$order_column];

    $where = "where b.service_id=1";

    if (!empty($search['value'])) {
      $where .= "
				AND (b.code  ilike '%".$iLike."%' or  a.ticket_number ilike  '%".$iLike."%'  
				or c.name ilike '%".$iLike."%' or c.id_number ilike '%".$iLike."%'
				)
			";
    }

    if (!empty($departdate))
    {
        $where.="
            and ( b.depart_date='$departdate')
        ";
    }

    if (!empty($gatein))
    {
        $where.="
            and ( to_char(a.created_on,'yyyy-mm-dd')='$gatein')
        ";
    }

    $sql = "
			select d.departure+b.depart_date as date_time, d.departure, b.depart_date, b.id, b.code,c.name as name_checkin,c.id_number, c.birth_date, c.gender,a.created_on as gatein_date,  a.* from app.t_trx_gate_in a
			left join app.t_trx_booking b on a.booking_id=b.id
			left join app.t_trx_booking_passanger c on a.ticket_number=c.ticket_number
			left join app.t_mtr_schedule_time d on b.schedule_time_id=d.id
			 {$where}

		";

    $query = $this->db->query($sql);
    $records_total = $query->num_rows();

    $sql .=" ORDER BY " . $order_column . " {$order_dir}";

    if ($length != -1) {
      $sql .=" LIMIT {$length} OFFSET {$start};";
    }

    $query = $this->db->query($sql);
    $rows_data = $query->result();

    $rows = array();
    $i = ($start + 1);
    foreach ($rows_data as $row) {
      $row->number = $i;
	   $row->birth_date = format_date($row->birth_date);
	  $row->gender= $row->gender=='L'?'Laki-laki':'perempuan';
	  $row->gatein_date=date("d F Y H:i", strtotime($row->gatein_date));
	  $row->depart_date= format_date($row->depart_date)." ".format_time($row->departure);
      $rows[] = $row;

      $i++;
    }

    return array(
        'draw' => $draw,
        'recordsTotal' => $records_total,
        'recordsFiltered' => $records_total,
        'data' => $rows
    );
  }
 
  public function gateInVehicleList() 
  {
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
    //$dateFrom = $this->input->post('dateFrom');
    //$dateTo = $this->input->post('dateTo');

    $field = array(
        1 => 'code',
        2 => 'ticket_number',
		3 =>'vehicle_name',
		 4 =>'id_number',
		 5 =>'gatein_date',
		 6 =>'date_time',
    );

    $order_column = $field[$order_column];

    $where = "where b.service_id=2";


    if (!empty($search['value'])) {
      $where .= "
				AND (b.code  ilike '%" . $search['value']."%' or  a.ticket_number ilike  '%" . $search['value']."%'  
				or e.name ilike '%" . $search['value']."%' or c.id_number ilike '%" . $search['value']."%'
				)
			";
    }

    $sql = "
			select e.name as vehicle_name, d.departure+b.depart_date as date_time, d.departure, b.depart_date, b.id, b.code, c.id_number, a.created_on as gatein_date,a.* from app.t_trx_gate_in a
			left join app.t_trx_booking b on a.booking_id=b.id
			left join app.t_trx_booking_vehicle c on a.ticket_number=c.ticket_number
			left join app.t_mtr_schedule_time d on b.schedule_time_id=d.id
			left join app.t_mtr_vehicle_class e on c.vehicle_class_id=e.id
			 {$where}

		";

    $query = $this->db->query($sql);
    $records_total = $query->num_rows();

    $sql .=" ORDER BY " . $order_column . " {$order_dir}";

    if ($length != -1) {
      $sql .=" LIMIT {$length} OFFSET {$start};";
    }

    $query = $this->db->query($sql);
    $rows_data = $query->result();

    $rows = array();
    $i = ($start + 1);
    foreach ($rows_data as $row) {
      $row->number = $i;
	  $row->gatein_date=date("d F Y H:i", strtotime($row->gatein_date));
	  $row->depart_date= format_date($row->depart_date)." ".format_time($row->departure);
      $rows[] = $row;

      $i++;
    }

    return array(
        'draw' => $draw,
        'recordsTotal' => $records_total,
        'recordsFiltered' => $records_total,
        'data' => $rows
    );
  }
  
}
