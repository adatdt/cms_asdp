<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class force_majeure_model extends MY_Model{

	public function forceList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$dateFrom=$this->input->post('dateFrom');
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$field = array(
			0=>'id',
			1=>'date',
			2=>'remark'
		);

		$order_column = $field[$order_column];

		//$where = " where a.status = 1 ";
		
		$where ="where status is not null ";
	
		if (!empty($search['value'])){
			$where .="and (remark ilike '%".$iLike."%')";
		}
		
		if (!empty($dateFrom))
		{
			$where .="and (to_char(date,'yyyy-mm-dd') = '".$dateFrom."')
					";
		}

		$sql = "SELECT * FROM app.t_trx_force_major {$where}";
		
		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();

		$sql .=" ORDER BY ".$order_column." {$order_dir}";

		if($length != -1)
		{
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();
							
		$rows = array();
		$i = ($start + 1);
		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->date = format_date($row->date);
			$row->id = $this->enc->encode($row->id);

			$row->actions = generate_button_new('force_majeure', 'detail', site_url('force_majeure/detail/'.$row->id));
			
			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}
	
	public function getDetail($id)
	{
		return $this->db->query("
			SELECT * FROM app.t_trx_force_major
			WHERE id = $id
		");
	}

	public function getDetailForce($id)
	{
		return $this->db->query("SELECT vc.name AS vehicle, bp.name, f.ticket_number, bp.fare, bv.fare AS fare_vehicle 
			FROM app.t_trx_force_major_detail f
			LEFT JOIN app.t_trx_booking_passanger bp ON bp.ticket_number =f.ticket_number
			LEFT JOIN app.t_trx_booking_vehicle bv ON bv.ticket_number =f.ticket_number
			LEFT JOIN app.t_mtr_vehicle_class vc ON vc.id = bv.vehicle_class_id WHERE force_major_id = $id");
	}

	public function checkBooking($table,$date)
	{
		return $this->db->query("
			SELECT ticket_number, bp.booking_id FROM app.t_trx_booking b
			LEFT JOIN app.{$table} bp ON bp.booking_id = b.id
			WHERE to_char(depart_date,'yyyy-mm-dd') = '{$date}' AND bp.status IN (1,2,3) AND b.status IN (3,4) AND ticket_number IS NOT null
		");
	}	

	public function checkBoarding($date)
	{
		return $this->db->query("
			SELECT ticket_number FROM app.t_trx_boarding_detail bd
			LEFT JOIN app.t_trx_booking b ON b.id = bd.booking_id
			WHERE to_char(depart_date,'yyyy-mm-dd') = '{$date}'");
	}	

	public function checkForce($date){
		return $this->db->query("
			SELECT id FROM app.t_trx_force_major 
			WHERE to_char(date,'yyyy-mm-dd') = '{$date}'");
	}

	public function checkForceDetail($ticket){
		return $this->db->query("
			SELECT id FROM app.t_trx_force_major_detail
			WHERE ticket_number = '{$ticket}'");
	}
}
