<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Boarding_model extends CI_Model {


  public function boardingList() 
  {
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
	$caridata = $this->input->post('caridata1');
	$boardingDate = $this->input->post('boardingDate1');
	$departDate= $this->input->post('departDate1');
    //$dateFrom = $this->input->post('dateFrom');
    //$dateTo = $this->input->post('dateTo');

    $field = array(

               	1=>"code",
				2=>"ticket_number",
				3=>"name_checkin",
				4=>"id_number",
                5=>"birth_date",
				6=>"gender",
				7=>"boarding_date",
				8=>"departure_date"
    );

    $order_column = $field[$order_column];

    $where = "WHERE  b.service_id=1 and c.status>=3 and  a.booking_id in (select booking_id from app.t_trx_boarding_detail) ";
/*
    if (!empty($search['value'])) {
      $where .= "
				AND (b.code  ilike '%" . $search['value']."%' or  a.ticket_number ilike  '%" . $search['value']."%'  
				or c.name ilike '%" . $search['value']."%' or c.id_number ilike '%" . $search['value']."%'
				)
			";
    }
*/

	if (!empty($caridata))
	{
		$where .=" and (b.code ilike '%".trim($caridata)."%' or c.name ilike '%".trim($caridata)."%' or c.id_number ilike '%".trim($caridata)."%' or a.ticket_number ilike '%".trim($caridata)."%')
		";
	}
	/*
	if (!empty($search['value']))
	{
		$where .=" and (b.code ilike '%".trim($search['value'])."%')
		
		";
	}
	*/
	if (!empty($boardingDate))
	{
		$where .="and (to_char(a.created_on,'yyyy-mm-dd') ='".$boardingDate."')
		";
	}
 	
	if (!empty($departDate))
	{
		$where .="and (to_char(b.depart_date,'yyyy-mm-dd') ='".$departDate."')
		";
	}
	
    $sql = "
		select d.departure+b.depart_date as date_time, d.departure, b.depart_date,b.id, b.code,c.name as name_checkin,c.id_number, c.birth_date, c.gender,a.created_on as boarding_date,  a.* 
		from app.t_trx_boarding_detail a
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
	  $row->boarding_date=date("d F Y H:i", strtotime($row->boarding_date));
	   $row->departure_date=format_date($row->depart_date)." ".format_time($row->departure);
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

 public function passangerVehicleList() 
  {
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
	$caridata = $this->input->post('caridata3');
	$departdate = $this->input->post('departdate3');
	$boardingdate = $this->input->post('boardingdate3');
    //$dateFrom = $this->input->post('dateFrom');
    //$dateTo = $this->input->post('dateTo');

    $field = array(
        1 => 'code',
        2 => 'ticket_number',
		3 =>'name_checkin',
		 4 =>'id_number',
		 5 =>'birth_date',
		 6 =>'gender',
		 7 =>'boarding_date',
		 8=>'date_time',
    );

    $order_column = $field[$order_column];

    $where = "WHERE  b.service_id=2 and c.status>=3 and ( a.booking_id in (select booking_id from app.t_trx_boarding_detail) ) " ;
/*
    if (!empty($search['value'])) {
      $where .= "
				AND (b.code  ilike '%" . $search['value']."%' or  a.ticket_number ilike  '%" . $search['value']."%'  
				or c.name ilike '%" . $search['value']."%' or c.id_number ilike '%" . $search['value']."%'
				)
			";
    }
 */
/* 
	 if (!empty($caridata))
	 {
		$where .=" and (b.code ilike '%".trim($caridata)."%')";
	 }
*/
	if (!empty($caridata))
	{
		$where .=" and (b.code ilike '%".trim($caridata)."%' or c.name ilike '%".trim($caridata)."%' or c.id_number ilike '%".trim($caridata)."%' or a.ticket_number ilike '%".trim($caridata)."%')
		";
	}	 
	 	
	if (!empty($boardingdate))
	{
		$where .="and (to_char(a.created_on,'yyyy-mm-dd') ='".$boardingdate."')
		";
	}
 	
	if (!empty($departdate))
	{
		$where .="and (to_char(b.depart_date,'yyyy-mm-dd') ='".$departdate."')
		";
	}
	
    $sql = "
		select  d.departure+b.depart_date as date_time, d.departure, b.depart_date,b.id, b.code,c.name as name_checkin,c.id_number, c.birth_date, c.gender,a.created_on as boarding_date,
		a.* from app.t_trx_boarding_detail a
		left join app.t_trx_booking b on a.booking_id = b.id 
		left join app.t_trx_booking_passanger c on c.booking_id=b.id 
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
	  $row->boarding_date=date("d F Y H:i", strtotime($row->boarding_date));
	   $row->departure_date=format_date($row->depart_date)." ".format_time($row->departure);
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


  public function boardingVehicleList() 
  {
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
	$caridata =$this->input->post('caridata2');
	$boardingdate=$this->input->post('boardingdate2');
	$departdate=$this->input->post('departdate2');
    //$dateFrom = $this->input->post('dateFrom');
    //$dateTo = $this->input->post('dateTo');

    $field = array(
        1 => 'booking_code',
        2 => 'ticket_number',
		3 =>'vehicle_name',
		 4 =>'id_number',
		 5 =>'boarding_date',
		 6=>'date_time',
    );

    $order_column = $field[$order_column];

    $where = "WHERE  b.service_id=2 and  c.status>=3 and  a.booking_id in (select booking_id from app.t_trx_boarding_detail) ";
/*
   if (!empty($search['value'])) {
      $where .= "
				AND (b.code  ilike '%" . $search['value']."%' or  a.ticket_number ilike  '%" . $search['value']."%'  
				or c.id_number ilike '%" . $search['value']."%'
				)
			";
    } 
*/
	if (!empty($caridata))
	{
		$where .="
			and (b.code ilike '%".trim($caridata)."%' or a.ticket_number ilike '%".trim($caridata)."%' 
			or c.id_number ilike '%".trim($caridata)."%' or e.name ilike '%".trim($caridata)."%')
		";	
	}
	
	if (!empty($departdate))
	{
		$where .="
			and (depart_date='".$departdate."')
		";
	}
	
	if (!empty($boardingdate))
	{
		$where .="
			and (to_char(a.created_on,'yyyy-mm-dd') ='".$boardingdate."')
		";
	}
	
    $sql = "
		select a.created_on as boarding_date, b.code as booking_code, e.name as vehicle_name, d.departure+b.depart_date as date_time, d.departure, b.depart_date,b.id,c.id_number,  a.* from app.t_trx_boarding_detail a
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
	  $row->boarding_date=format_date($row->boarding_date);
	   $row->depart_date=format_date($row->depart_date);

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
