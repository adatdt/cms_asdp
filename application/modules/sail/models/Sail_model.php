<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Sail_model extends CI_Model {


  public function sailList() 
  {
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
    $departdate= $this->input->post('departdate');
    //$dateFrom = $this->input->post('dateFrom');
    //$dateTo = $this->input->post('dateTo');

    $field = array(
        
        1 => 'departure',
		2 =>'ship_name',
		3 =>'depart_date',
    );

    $order_column = $field[$order_column];

/*
    $where = "WHERE a.status= 1";

    if (!empty($search['value'])) {
      $where .= "
				AND (b.code  ilike '%" . $search['value']."%' or  a.ticket_number ilike  '%" . $search['value']."%'  
				or c.name ilike '%" . $search['value']."%' or c.id_number ilike '%" . $search['value']."%'
				)
			";
    }
 */
 	 $where="";
 	 if(!empty($departdate))
 	 {
 	 	if (!empty($search['value'])) {
     		$where .= "where (c.name ilike '%" . $search['value']."%')
     			 			and (b.depart_date='$departdate')
							";
		}
		else
		{
			$where .= "WHERE  b.depart_date='$departdate'";
		}
        
 	 }
 	 else
 	 {
 	 	$where .= "where c.name ilike '%" . $search['value']."%'";
 	 }
 
    $sql = "

		select distinct d.departure , to_char(a.created_on,'yyyy-mm-dd') as boarding_date,  c.name as ship_name, b.depart_date, a.ship_id 
		from app.t_trx_boarding a
		join app.t_trx_booking b on a.booking_id=b.id
		join  app.t_mtr_ship c on a.ship_id=c.id
		join app.t_mtr_schedule_time d on b.schedule_time_id=d.id
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
/*    
	foreach ($rows_data as $row) {
	
	 $departdate=$row->depart_date;
	 $departure=$row->departure;
	 
	  $x=$this->db->query("select b.departure,a.* from app.t_trx_sail a
							left join app.t_mtr_schedule_time b on a.schedule_time_id=b.id
							where to_char(a.depart_date,'yyyy-mm-dd')='$departdate'
							and b.departure='$departure'
							")->num_rows();
	  if($x>0)
	  {
	  	$status='Sudah Approve';
	  }
	  else
	  {
	  	$status='Belum Approve';
	  }
	  
      $row->number = $i;
	  $row->status = $status;
	  $row->action='
	  	<a class="btn-warning btn-sm" style=" color:black" href="'.site_url('sail/detail/'.$row->ticket_number).'"> <i class="fa fa-search-plus"></i> </a>
	  ';
	    $row->departure = format_time($row->departure);
		 $row->depart_date = format_date($row->depart_date);
      $rows[] = $row;

      $i++;
    }
	*/
$number=1;
foreach ($rows_data as $row) {
		
	 $departure=$row->departure;
	 $depart_date=$row->depart_date;
	 $ship_id=$row->ship_id;
	 
	 $x=$this->db->query("
	 	select b.departure , a.* from app.t_trx_sail a
left join app.t_mtr_schedule_time b on a.schedule_time_id=b.id
where departure='$departure' and to_char(depart_date,'yyyy-mm-dd')='$depart_date' and ship_id=$ship_id
	 ");
	 
	 if ($x->num_rows()>0)
	 {
	 	$status=success_color ('Sudah Berangkat');
	 }
	 /*
	 else if($depart_date<date('YYYY-mm-dd'))
	 {
	 	$status='gagal berangkat';
	 }
	 */
	 else
	 {
	 	$status='Belum Berangkat';
	 }
	 	
		
	  $row->number=$number;
	  $row->departure=format_time($row->departure);
	  $row->boarding_date=format_date($row->boarding_date);
	  $row->depart_date=format_date($row->depart_date);
	  $row->status=$status;
	  $row->action='
	  	<a style=" color:black"  class="btn btn-warning btn-sm" href="'.site_url('sail/detail/'.$departure."/".$depart_date."/".$ship_id).'"> <i class="fa fa-search-plus"></i> </a>
	  ';
	  
      $rows[] = $row;
      $number++;
    }
	
    return array(
        'draw' => $draw,
        'recordsTotal' => $records_total,
        'recordsFiltered' => $records_total,
        'data' => $rows
    );
  }
  
  public function getPassanger($depature, $depart_date, $ship_id)
  {
  /*
  	return $this->db->query("
		select g.id as booking_passanger_id, a.ship_id as shipid ,d.id as schedule_time_id, f.name as ship_name, c.depart_date, d.departure, g.* from app.t_trx_boarding a
		left join app.t_trx_booking c on a.booking_id=c.id
		left join app.t_mtr_schedule_time d on c.schedule_time_id=d.id
		left join app.t_mtr_schedule e on d.schedule_id=e.id
		left join app.t_mtr_ship f on e.ship_id=f.id
		right join app.t_trx_booking_passanger g on g.booking_id=c.id
		where departure='$depature' and to_char(depart_date,'yyyy-mm-dd')='$depart_date' and a.ship_id=$ship_id
	");
  */
  	return $this->db->query("
		select b.id as booking_passanger_id, a.ship_id as shipid ,d.id as schedule_time_id, f.name as ship_name, c.depart_date, d.departure, b.* from app.t_trx_boarding a
		left join app.t_trx_booking_passanger b on a.ticket_number=b.ticket_number
		left join app.t_trx_booking c on a.booking_id=c.id
		left join app.t_mtr_schedule_time d on c.schedule_time_id=d.id
		left join app.t_mtr_schedule e on d.schedule_id=e.id
		left join app.t_mtr_ship f on e.ship_id=f.id
		where service_id=1 and departure='$depature' and to_char(depart_date,'yyyy-mm-dd')='$depart_date' and a.ship_id=$ship_id

	");
	
  }
  
  public function getVehicle($depature, $depart_date, $ship_id)
  {

  	  	return $this->db->query("
		select h.id as booking_vehicle_id, h.name as vehicle_name, g.id_number as nopol, g.ticket_number as ticket, c.id as booking_passanger_id, a.ship_id as shipid,d.id as schedule_time_id, f.name as ship_name,
		 b.depart_date, d.departure, c.*
		from app.t_trx_boarding a
		left join app.t_trx_booking b on a.booking_id=b.id
		left join app.t_trx_booking_passanger c on c.booking_id=b.id
		left join app.t_mtr_schedule_time d on b.schedule_time_id=d.id
		left join app.t_mtr_schedule e on d.schedule_id=e.id
		left join app.t_mtr_ship f on e.ship_id=f.id
		left join app.t_trx_booking_vehicle g on a.ticket_number=g.ticket_number
		left join app.t_mtr_vehicle_class h on g.vehicle_class_id=h.id
		where b.service_id=2 and departure='$depature' and to_char(depart_date,'yyyy-mm-dd')='$depart_date' and a.ship_id=$ship_id
	");

	
  /*
  	  	return $this->db->query("
		select b.id as booking_vehicle_id, a.ship_id as shipid , d.id as schedule_time_id, c.depart_date, d.departure,  g.name as ship_name, c.customer_name, e.name as vehicle_name, a.* from app.t_trx_boarding a
		left join app.t_trx_booking_vehicle b on a.ticket_number=b.ticket_number
		left join app.t_trx_booking c on a.booking_id=c.id
		left join app.t_mtr_schedule_time d on c.schedule_time_id=d.id
		left join app.t_mtr_vehicle_class e on b.vehicle_class_id=e.id
		left join app.t_mtr_schedule f on d.schedule_id=f.id
		left join app.t_mtr_ship g on f.ship_id=g.id
		where service_id=2 and departure='$depature' and to_char(depart_date,'yyyy-mm-dd')='$depart_date' and a.ship_id=$ship_id

	");
	*/
  }
  
   public function getVehicle2($depature, $depart_date, $ship_id)
  {
  	  	return $this->db->query("
		select b.id_number, b.id as booking_vehicle_id, a.ship_id as shipid , d.id as schedule_time_id, c.depart_date, d.departure,  g.name as ship_name, c.customer_name, e.name as vehicle_name, a.* from app.t_trx_boarding a
		left join app.t_trx_booking_vehicle b on a.ticket_number=b.ticket_number
		left join app.t_trx_booking c on a.booking_id=c.id
		left join app.t_mtr_schedule_time d on c.schedule_time_id=d.id
		left join app.t_mtr_vehicle_class e on b.vehicle_class_id=e.id
		left join app.t_mtr_schedule f on d.schedule_id=f.id
		left join app.t_mtr_ship g on f.ship_id=g.id
		where service_id=2 and departure='$depature' and to_char(depart_date,'yyyy-mm-dd')='$depart_date' and a.ship_id=$ship_id

	");
	
  }
  
  public function update($where,$table,$data)
  {
  	$this->db->where($where);
	$this->db->update($table,$data);
  }
}
