<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Checkin_model extends CI_Model {


  public function checkInList() 
  {
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
    $checkin =$this->input->post('checkin');
    $departdate =$this->input->post('departdate');
    $order_dir = strtoupper($order[0]['dir']);
    //$dateFrom = $this->input->post('dateFrom');
    //$dateTo = $this->input->post('dateTo');
    $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

    $field = array(
      /*
      1 => 'customer_name',
      2 => 'booking_code',
		  3 =>'booking_date',
		  4 =>'checkin_date',
		  5 =>'phone',
		  6 =>'email',
		  7 =>'date_time'
      */
      1 => 'customer_name',
      2 => 'booking_code',
      3 =>'email',
      4 =>'phone',
      5 =>'checkin_date',
      6 =>'date_time'
    );

    $order_column = $field[$order_column];

    $where = "WHERE a.status= 1";

    if (!empty($search['value'])) {
      $where .= "
				AND (
        b.customer_name  ilike '%".$iLike."%'
        or b.code ilike '%".$iLike."%'
        or b.phone ilike '%".$iLike."%'
        or b.email ilike '%".$iLike."%'
        )
			";
    }

    if (!empty($checkin))
    {
      $where .="
        and (to_char(a.created_on,'yyyy-mm-dd')='".$checkin."')
      ";
    }

    if (!empty($departdate))
    {
      $where .="
        and (depart_date='".$departdate."')
      ";
    }

    $sql = "
			select c.departure+b.depart_date as date_time, c.departure, b.depart_date, b.customer_name,b.phone, b.email,b.created_on as booking_date,a.created_on as checkin_date, a.* 
      from app.t_trx_check_in a
			left join app.t_trx_booking b on a.booking_id=b.id 
			left join app.t_mtr_schedule_time c on b.schedule_time_id=c.id
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
	  $row->checkin_date = format_date($row->checkin_date);
	  $row->booking_date = format_date($row->booking_date);
	   $row->date_time = format_date($row->depart_date)." ".format_time($row->departure);
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
