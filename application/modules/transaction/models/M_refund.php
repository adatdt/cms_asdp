<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Refund_model extends MY_Model{

	public function refundList(){
		$start 			= $this->input->post('start');
		$length 		= $this->input->post('length');
		$draw 			= $this->input->post('draw');
		$search 		= $this->input->post('search');
		$order 			= $this->input->post('order');
		$order_column 	= $order[0]['column'];
		$order_dir 		= strtoupper($order[0]['dir']);
		$dateFrom		= $this->input->post('dateFrom');
		$iLike        	= trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$field = array(
			1 => 'booking_code',
			2 => 'name',
			3 => 'phone',
			4 => 'port',
			5 => 'created_on',
			6 => 'date_collection',
		);

		$order_column = $field[$order_column];		
		$where = "WHERE r.status is not null ";
	
		if(!empty($search['value'])){
			$where .="and (
				r.name ilike '%".$iLike."%' OR 
				r.booking_code ilike '%".$iLike."%')";
		}
		
		if(!empty($dateFrom)){
			$where .="and (to_char(r.created_on,'yyyy-mm-dd')='".$dateFrom."')
					";
		}

		$sql = "SELECT 
			r.id,
			booking_code, 
			r.name, phone, 
			p.name as port, 
			date_collection,
			r.created_on,
			r.service_id,
			r.*
		FROM app.t_trx_refund r
		LEFT JOIN app.t_mtr_port p ON p.id = r.port_id_collection {$where}";
		
		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();

		$sql .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();
							
		$rows = array();
		$i = ($start + 1);
		foreach ($rows_data as $row) {
			$row->number 		  = $i;
			$row->date_collection = format_date($row->date_collection);
			$row->created_on 	  = format_date($row->created_on);
			$param 			      = $this->enc->encode("{$row->id}|{$row->service_id}");
			$row->actions 		  = generate_button_new('refund', 'detail', site_url('refund/detail/'.$param));
						
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
	
	public function getDetail($id){
		return $this->db->query("
			SELECT 
				r.id,
				booking_code, 
				r.name, phone, 
				p.name as port, 
				date_collection,
				r.created_on
			FROM app.t_trx_refund r
			LEFT JOIN app.t_mtr_port p ON p.id = r.port_id_collection 
			WHERE r.id = $id
		");
	}
	
	public function getDetailRefund($id){
		return $this->db->query("
			SELECT vc.name AS vehicle, bp.name, r.ticket_number, r.fare, fee FROM app.t_trx_refund_detail r
			LEFT JOIN app.t_trx_booking_passanger bp ON bp.ticket_number =r.ticket_number
			LEFT JOIN app.t_trx_booking_vehicle bv ON bv.ticket_number =r.ticket_number
			LEFT JOIN app.t_mtr_vehicle_class vc ON vc.id = bv.vehicle_class_id WHERE refund_id = $id
		");
	}
}
