<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Income_model extends CI_Model {


  public function invoiceList() 
  {
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
	$dateFrom=$this->input->post('dateFrom');
    //$dateFrom = $this->input->post('dateFrom');
    //$dateTo = $this->input->post('dateTo');

    $field = array(
		0=>'id',
        1 => 'customer_name',
        2 => 'booking_code',
		3 => 'booking_date',
		4 =>'service_name',
		 5 =>'payment_name',
		 6 =>'trx_number',
		 7 =>'amount',
		 8 =>'created_on'
    );

    $order_column = $field[$order_column];

    $where = "";

    if (!empty($search['value']) and !empty($dateFrom)) {
      $where .= "
				where (b.customer_name  ilike '%".trim($search['value'])."%'
				or  a.booking_code ilike '%".trim($search['value'])."%'
				or a.number ilike '%".trim($search['value'])."%')
				and to_char(b.created_on,'yyyy-mm-dd')='$dateFrom'
			";
    }
	else if(!empty($search['value']) and empty($dateFrom))
	{
		      $where .= "
				where b.customer_name  ilike '%".trim($search['value'])."%'
				or  a.booking_code ilike '%".trim($search['value'])."%'
				or a.number ilike '%".trim($search['value'])."%'
			";
	}
	else if(empty($search['value']) and !empty($dateFrom))
	{
		      $where .= "where to_char(a.created_on,'yyyy-mm-dd')='$dateFrom' ";
	}
	else
	{
		$where .="";
	}

    $sql = "
			select a.created_on as invoice_date, d.name as service_name, b.created_on as booking_date, c.name as payment_name, b.customer_name ,a.number as invoice_number ,a.*  from app.t_trx_invoice a
			left join app.t_trx_booking b on a.booking_id=b.id
			left join app.t_mtr_payment_method c on a.payment_method_id=c.id
			left join app.t_mtr_service d on b.service_id=d.id
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
	  $row->amount=idr_currency($row->amount);
	   $row->created_on=format_date( $row->created_on);
	   $row->invoice_date=format_date( $row->invoice_date);
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
