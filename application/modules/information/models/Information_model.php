<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Information_model extends CI_Model {


  public function informationList() 
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
        
        1 => 'depart_date',
		2 =>'departure',
		3 =>'ship_name',
    );

    $order_column = $field[$order_column];
 /*
    $sql = "

		select distinct d.departure , to_char(a.created_on,'yyyy-mm-dd') as boarding_date,  c.name as ship_name, b.depart_date, a.ship_id from app.t_trx_boarding a
		join app.t_trx_booking b on a.booking_id=b.id
		join  app.t_mtr_ship c on a.ship_id=c.id
		join app.t_mtr_schedule_time d on b.schedule_time_id=d.id
		
		";
*/
	$sql="
		select distinct b.depart_date, d.departure,c.name as ship_name,b.id as booking_id from app.t_trx_boarding a
		join app.t_trx_booking b on a.booking_id=b.id
		join  app.t_mtr_ship c on a.ship_id=c.id
		join app.t_mtr_schedule_time d on b.schedule_time_id=d.id
		where d.id not in (select schedule_time_id from app.t_trx_sail)
		or to_char(b.depart_date,'yyyy-mm-dd') not in (select to_char(depart_date,'yyyy-mm-dd') from app.t_trx_sail)
		or a.ship_id not in (select ship_id from app.t_trx_sail)
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
	
	$totalpasanger=$this->db->query("select * from app.t_trx_booking_passanger where booking_id=".$row->booking_id."  ")->num_rows();
	$totalvehicle=$this->db->query("select * from app.t_trx_booking_vehicle where booking_id=".$row->booking_id." and status >=3 ")->num_rows();

	$row->number=$number;
	$row->departure=format_time($row->departure);
	$row->depart_date=format_date($row->depart_date);
	$row->total_passanger=$totalpasanger;
	$row->total_vehicle=$totalvehicle; 
	$row->status='Belum Berangkat'; 
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
  

}
