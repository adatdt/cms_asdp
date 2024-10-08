<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pemeliharaan_dermaga_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'laporan/pemeliharaan_dermaga';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$branch = $this->enc->decode($this->input->post('branch'));
		$shift = $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$field = array(
			0 => 'assignment_date',
			1 => 'branch_name',
			2 => 'shift_name',
			3 => 'team_name'
		);

		$order_column = $field[$order_column];
		$where = " where up.status = 2 ";

		if ((!empty($dateFrom) and empty($dateTo))||(empty($dateFrom) and !empty($dateTo))){
			$where .=" and up.assignment_date ='$dateFrom' or up.assignment_date ='$dateTo'";
		}

		if(!empty($dateFrom) and !empty($dateTo)){
			$where .=" and up.assignment_date between '$dateFrom' and  '$dateTo'";   
		}

		if(!empty($branch)){
			$where .=" and (bok.branch_code = '".$branch."')";
		}

		if(!empty($shift)){
			$where .=" and (ar.shift_id = ".$shift.")";
		}


		if (!empty($search['value'])){
			$where .=" and ( branch_name ilike '%".$iLike."%' or shift_name ilike '%".$iLike."%' or team_name ilike '%".$iLike."%')";	
		}

		$sql = "SELECT up.assignment_date, branch.branch_name, up.assignment_code, shift.shift_name, mt.team_name
			from app.t_trx_assignment_user_pos up 
			join app.t_trx_assignment_regu ar on up.assignment_code = ar.assignment_code 
			join app.t_trx_opening_balance ob on ob.assignment_code = up.assignment_code
			join app.t_trx_sell sel on sel.ob_code = ob.ob_code
			join app.t_trx_invoice inv on inv.trans_number = sel.trans_number
			join app.t_trx_booking bok on bok.trans_number = inv.trans_number 
			join app.t_trx_booking_passanger bp on bp.booking_code = bok.booking_code
			join app.t_mtr_branch branch on bok.branch_code = branch.branch_code
			join core.t_mtr_team mt on ar.team_code = mt.team_code
			join app.t_mtr_shift shift on ar.shift_id = shift.id
			{$where}
			group by up.assignment_date, bok.branch_code, branch.branch_name, up.assignment_code, ar.shift_id,shift.shift_name, ar.team_code, mt.team_name";

		$query          = $this->db->query($sql);
		$records_total  = $query->num_rows();

		$sql .=" order by " . $order_column . " {$order_dir}";

		if($length != -1){
			$sql .=" limit {$length} offset {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$code 			= $this->enc->encode($row->assignment_code);
			$detail_url 	= site_url($this->_module."/detail/{$code}");
			$pdf_url 		= site_url($this->_module."/download_pdf/{$code}");

     		$row->actions 	= "";
			$row->actions 	.= generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_pdf', $pdf_url);
     		$row->assignment_date = format_date($row->assignment_date);
     		$row->no=$i;

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

	public function detail_trip($where=""){

		return $this->db->query("SELECT up.assignment_date, branch.branch_name, port.name as port_name, (port_origin.name||'-'||port_destination.name) as trip, up.assignment_code, shift.shift_name, mt.team_name, coalesce(users.first_name, '')||' '||coalesce(users.last_name, '') as spv
			from app.t_trx_assignment_user_pos up 
			join app.t_trx_assignment_regu ar on up.assignment_code = ar.assignment_code 
			join app.t_trx_opening_balance ob on ob.assignment_code = up.assignment_code
			join app.t_trx_sell sel on sel.ob_code = ob.ob_code
			join app.t_trx_invoice inv on inv.trans_number = sel.trans_number
			join app.t_trx_booking bok on bok.trans_number = inv.trans_number
			join app.t_mtr_branch branch on bok.branch_code = branch.branch_code
			join core.t_mtr_team mt on ar.team_code = mt.team_code
			join app.t_mtr_shift shift on ar.shift_id = shift.id
			join app.t_mtr_port port on branch.port_id = port.id
			join app.t_mtr_port port_origin on bok.origin = port_origin.id
			join app.t_mtr_port port_destination on bok.destination = port_destination.id
			join core.t_mtr_user users on ar.supervisor_id = users.id
			{$where}
			group by up.assignment_date,up.assignment_code, branch.branch_name,  port.name, port_origin.name, port_destination.name, up.assignment_code, shift.shift_name, mt.team_name, users.id");
	}

	public function list_detail_passanger($where=""){

		return $this->db->query("SELECT passanger.name as passanger_type_name, ticket_count, dock_fee, total_amount
		from app.t_mtr_passanger_type passanger 
		left join (SELECT bp.passanger_type_id, count( distinct bp.id) as ticket_count, bp.dock_fee, count( distinct bp.id) * bp.dock_fee as total_amount
			from app.t_trx_opening_balance ob 
			join app.t_trx_sell sel on sel.ob_code = ob.ob_code
			join app.t_trx_invoice inv on inv.trans_number = sel.trans_number
			join app.t_trx_booking bok on bok.trans_number = inv.trans_number and bok.service_id = 1 
			join app.t_trx_booking_passanger bp on bp.booking_code = bok.booking_code and bp.status = 5
			{$where}
			group by bp.passanger_type_id, bp.dock_fee) trx on passanger.id = trx.passanger_type_id
			where passanger.id not in (3)");
	}

	public function sub_total_passanger($where=""){

		return $this->db->query("SELECT count( DISTINCT bp.id) AS ticket_count, sum(bp.dock_fee) AS sub_total_amount
		from app.t_trx_opening_balance ob 
		join app.t_trx_sell sell on ob.ob_code = sell.ob_code
		join app.t_trx_invoice inv on sell.trans_number = inv.trans_number 
		join app.t_trx_booking bok on inv.trans_number = bok.trans_number and bok.service_id = 1 
		join app.t_trx_booking_passanger bp on bok.booking_code = bp.booking_code and bp.status = 5
		{$where}");
	}

	public function list_detail_vehicle($where=""){

		return $this->db->query("SELECT vehicle.name as vehicle_class_name, ticket_count, dock_fee, total_amount 
			from app.t_mtr_vehicle_class vehicle 
			left join
			(select bv.vehicle_class_id, count( distinct bv.id) as ticket_count, bv.dock_fee, count( distinct bv.id) * bv.dock_fee as total_amount
			from app.t_trx_opening_balance ob 
			join app.t_trx_sell sel on sel.ob_code = ob.ob_code
			join app.t_trx_invoice inv on inv.trans_number = sel.trans_number
			join app.t_trx_booking bok on bok.trans_number = inv.trans_number and bok.service_id = 2
			join app.t_trx_booking_vehicle bv on bv.booking_code = bok.booking_code and bv.status = 5
			{$where}
			group by bv.vehicle_class_id, bv.dock_fee) trx on vehicle.id = trx.vehicle_class_id
			where status = 1
			order by vehicle.id");
	}

	public function sub_total_vehicle($where=""){

		return $this->db->query("SELECT count( distinct bv.id) as ticket_count, sum(bv.dock_fee) as total_amount
			from app.t_trx_opening_balance ob 
			join app.t_trx_sell sell on ob.ob_code = sell.ob_code
			join app.t_trx_invoice inv on sell.trans_number = inv.trans_number 
			join app.t_trx_booking bok on inv.trans_number = bok.trans_number and bok.service_id = 2 
			join app.t_trx_booking_vehicle bv on bv.booking_code = bok.booking_code and bv.status = 5
			{$where}");
	}

	public function select_data($table, $where="")
	{
		return $this->db->query("select * from $table $where");
	}



}
