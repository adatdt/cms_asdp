<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Emoney_model extends CI_Model {


  public function emoneyList() 
  {
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
    $order_dir = strtoupper($order[0]['dir']);
    $payment_date = $this->input->post('payment_date');
    $booking_date= $this->input->post('booking_date');
    $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));


    $field = array(

      1 => 'booking_code',
      2 => 'booking_date',
      //3 =>'invoice_number',
      3 =>'amount',
      4 =>'name_method',
      5 =>'payment_type',
      6 =>'payment_date'
    );

    $order_column = $field[$order_column];
/*
    $where = "WHERE a.status= 1";

    if (!empty($search['value'])) {
      $where .= "
				AND (
        b.customer_name  ilike '%".trim($search['value'])."%'
        or b.code ilike '%".trim($search['value'])."%'
        or b.phone ilike '%".trim($search['value'])."%'
        or b.email ilike '%".trim($search['value'])."%'
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
*/
    $where="";

    if(!empty($booking_date) and !empty($payment_date))
    {
      if (!empty($search['value']))
      {
        $where .="
          where (to_char(b.created_on,'yyyy-mm-dd')='$booking_date'
          and to_char(a.payment_date,'yyyy-mm-dd')='$payment_date') and
          (
           a.booking_code ilike '%".$iLike."%'
          or c.number ilike '%".$iLike."%'
          or d.name ilike '%".$iLike."%'
          )
        ";
      } 
      else
      {
        $where .="
          where to_char(b.created_on,'yyyy-mm-dd')='$booking_date'
          and to_char(a.payment_date,'yyyy-mm-dd')='$payment_date'
          ";
      }
    }
    else if (!empty($booking_date) or !empty($payment_date))
    {
      if (!empty($search['value']))
      {
        $where .="
          where (to_char(b.created_on,'yyyy-mm-dd')='$booking_date'
          or to_char(a.payment_date,'yyyy-mm-dd')='$payment_date') and
          (
           a.booking_code ilike '%".$iLike."%'
          or c.number ilike '%".$iLike."%'
          or d.name ilike '%".$iLike."%'
          )
        ";
      } 
      else
      {
        $where .="
          where to_char(b.created_on,'yyyy-mm-dd')='$booking_date'
          or to_char(a.payment_date,'yyyy-mm-dd')='$payment_date'
          ";
      }
    }
    else
    {
      if (!empty($search['value']))
      {
        $where .="
  
          where  a.booking_code ilike '%".$iLike."%'
          or c.number ilike '%".$iLike."%'
          or d.name ilike '%".$iLike."%'
          
        ";
      } 
      else
      {
        $where .=" ";
      } 
    }

    $sql = "
      select d.name as name_method, c.number as invoice_number,b.created_on as booking_date,  a.* from app.t_trx_payment_emoney a
      left join app.t_trx_booking b on a.booking_id=b.id
      left join app.t_trx_invoice c on a.booking_code=c.booking_code
      left join app.t_mtr_payment_method d on a.payment_method=d.id
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
      $row->booking_date=format_datetime($row->booking_date);
      $row->payment_date=format_datetime($row->payment_date);
      $row->amount=idr_currency($row->amount);
      $row->invoice_number=$row->invoice_number==''?'Go Show':$row->invoice_number;
      $row->type=$row->payment_type==1?success_color('Normal'):failed_color('Kurang Bayar');
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
