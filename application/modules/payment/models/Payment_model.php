<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Payment_model extends CI_Model {


  public function invoiceList() 
  {
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
	$paymentDate=$this->input->post('paymentDate');
	$bookingDate=$this->input->post('bookingDate');
    //$dateFrom = $this->input->post('dateFrom');
    //$dateTo = $this->input->post('dateTo');
	$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
	
    $field = array(
		0=>'a.id',
        1 => 'booking_code',
		2 => 'booking_date',
        3 => 'invoice_number',
		4 => 'ref_no',
		5 =>'amount',
		6 =>'name',
		7 =>'payment_type',
		8 =>'payment_date',
    );

    $order_column = $field[$order_column];

    $where = "";

	if (!empty($bookingDate) and !empty($paymentDate) )
	{
		if (!empty($search['value']))
		{
		   $where .= "
				where (a.booking_code  ilike '%".$iLike."%'
				or  a.invoice_number ilike '%".$iLike."%'
				or a.ref_no ilike '%".$iLike."%'
				or b.name ilike  '%".$iLike."%')
				and to_char(c.created_on,'yyyy-mm-dd')='$bookingDate'
				and to_char(a.payment_date,'yyyy-mm-dd')='$paymentDate'
			";
		}
		else
		{
				$where .= "
				where to_char(c.created_on,'yyyy-mm-dd')='$bookingDate'
				and to_char(a.payment_date,'yyyy-mm-dd')='$paymentDate'
			";
		}
	}
	
	else if (!empty($bookingDate) and empty($paymentDate) )
	{
		if (!empty($search['value']))
		{
		   $where .= "
				where (a.booking_code  ilike '%".$iLike."%'
				or  a.invoice_number ilike '%".$iLike."%'
				or a.ref_no ilike '%".$iLike."%'
				or b.name ilike  '%".$iLike."%')
				and to_char(c.created_on,'yyyy-mm-dd')='$bookingDate'
				
			";
		}
		else
		{
				$where .= "
				where to_char(c.created_on,'yyyy-mm-dd')='$bookingDate'
				
			";
		}
	}
	
	else if (empty($bookingDate) and !empty($paymentDate) )
	{
		if (!empty($search['value']))
		{
		   $where .= "
				where (a.booking_code  ilike '%".$iLike."%'
				or  a.invoice_number ilike '%".$iLike."%'
				or a.ref_no ilike '%".$iLike."%'
				or b.name ilike  '%".$iLike."%')
				and to_char(a.payment_date,'yyyy-mm-dd')='$paymentDate'
				
			";
		}
		else
		{
				$where .= "
				where to_char(a.payment_date,'yyyy-mm-dd')='$paymentDate'
				
			";
		}
	}
	else 
	{
		if (!empty($search['value']))
		{
		   $where .= "
				where a.booking_code  ilike '%".$iLike."%'
				or  a.invoice_number ilike '%".$iLike."%'
				or a.ref_no ilike '%".$iLike."%'
				or b.name ilike  '%".$iLike."%'
			";
		}
	}
/*
    if (!empty($search['value'])) {
      $where .= "
				where a.booking_code  ilike '%".trim($search['value'])."%'
				or  a.invoice_number ilike '%".trim($search['value'])."%'
				or a.ref_no ilike '%".trim($search['value'])."%'
				or b.name ilike  '%".trim($search['value'])."%'
			";
    }
*/

    $sql = "
			select c.created_on as booking_date,b.*,a.* from app.t_trx_payment a
			left join app.t_mtr_payment_method b on a.payment_method= b.id
			left join app.t_trx_booking c on a.booking_id=c.id
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
	  $row->booking_date = format_dateTime($row->booking_date);
	  $row->payment_date = format_dateTime($row->payment_date);
	  $row->amount = idr_currency($row->amount);
	  $row->payment_type = $row->payment_type==1?success_color('Normal'):failed_color('Kurang Bayar');

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
