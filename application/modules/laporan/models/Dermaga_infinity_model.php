<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dermaga_infinity_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'laporan/dermaga_infinity';
	}

    public function dataList($dock_id){
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
			0 => 'shift_date',
			1 => 'port_name',
			2 => 'branch_name',
			3 => 'shift_name',
			4 => 'team_name'
		);

		$where = " WHERE ar.status = 2 and ob.dock_id = $dock_id";

		if ((!empty($dateFrom) and empty($dateTo))||(empty($dateFrom) and !empty($dateTo))){
			$where .=" AND ob.shift_date ='$dateFrom' or ob.shift_date ='$dateTo'";
		}

		if(!empty($dateFrom) and !empty($dateTo)){
			$where .=" AND ob.shift_date BETWEEN '$dateFrom' and  '$dateTo'";   
		}

		if(!empty($branch)){
			$where .=" AND (branch_code = '".$branch."')";
		}

		if(!empty($shift)){
			$where .=" AND (shift_name = ".$shift.")";
		}

		if (!empty($search['value'])){
			$where .=" AND ( branch_name ilike '%".$iLike."%' or shift_name ilike '%".$iLike."%' or team_name ilike '%".$iLike."%')";	
		}

		$sql = "SELECT DISTINCT
					ob.shift_date,
					p.name AS port_name,
					ob.port_id,
					sc.name AS ship_name,
					ob.ship_class,
					shift_name,
					ob.shift_id,
					ob.dock_id,
					branch_name,
					team_name,
					port_origin.name AS origin,
					port_destination.name AS destination,
					coalesce(users.first_name, '')||' '||coalesce(users.last_name, '') as spv
				FROM
					app.t_trx_open_boarding ob
				JOIN app.t_trx_assignment_regu ar ON ar.shift_id = ob.shift_id AND ar.assignment_date = ob.shift_date AND ar.status = 2
				JOIN app.t_mtr_ship_class sc ON sc.id = ob.ship_class
				JOIN app.t_mtr_port p ON p.id = ob.port_id
				JOIN app.t_mtr_shift s ON s.id = ob.shift_id
				JOIN app.t_mtr_branch b ON b.port_id = ob.port_id AND b.ship_class = ob.ship_class
				JOIN core.t_mtr_team t ON t.team_code = ar.team_code
				JOIN app.t_trx_schedule ss ON ss.schedule_code = ob.schedule_code
				JOIN app.t_mtr_port port_origin ON ss.port_id = port_origin.id
				JOIN app.t_mtr_port port_destination ON ss.destination_port_id = port_destination.id
				JOIN core.t_mtr_user users on ar.supervisor_id = users.id
				{$where}";

		$query          = $this->db->query($sql);
		$records_total  = $query->num_rows();

		$order_by = '';
		foreach ($order as $key => $value) {
			$order_column = $order[$key]['column'];
			$order_dir    = strtoupper($order[$key]['dir']);
			$order_column = $field[$order_column];


			$order_by .= "{$order_column} {$order_dir},";
		}

		$order_by = rtrim($order_by,",");
		$sql .= " ORDER BY {$order_by}";

		if($length != -1){
			$sql .=" limit {$length} offset {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$param 	= $this->enc->encode($row->shift_date.'|'.$row->ship_class.'|'.$row->shift_id.'|'.$row->port_id.'|'.$row->dock_id.'|'.$row->branch_name.'|'.$row->port_name.'|'.$row->origin.'|'.$row->destination.'|'.$row->shift_name.'|'.$row->team_name.'|'.$row->spv);

			$detail_url = site_url($this->_module."/detail/{$param}");
			$pdf_url    = site_url($this->_module."/download_pdf/{$param}");
			$excel_url    = site_url($this->_module."/download_excel/{$param}");

			$row->actions .= generate_button_new($this->_module, 'download_pdf', $pdf_url);
			$row->actions .= generate_button_new($this->_module, 'download_excel', $excel_url);
     		$row->shift_date = format_date($row->shift_date);
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
	// public function detail_trip($where="",$ship_class_id){

	// 	return $this->db->query("SELECT up.assignment_date, branch.branch_name, port.name as port_name, (port_origin.name||'-'||port_destination.name) as trip, up.assignment_code, shift.shift_name, mt.team_name, coalesce(users.first_name, '')||' '||coalesce(users.last_name, '') as spv
	// 		from app.t_trx_assignment_user_pos up 
	// 		join app.t_trx_assignment_regu ar on up.assignment_code = ar.assignment_code 
	// 		join app.t_trx_opening_balance ob on ob.assignment_code = up.assignment_code
	// 		join app.t_trx_sell sel on sel.ob_code = ob.ob_code
	// 		join app.t_trx_invoice inv on inv.trans_number = sel.trans_number
	// 		join app.t_trx_booking bok on bok.trans_number = inv.trans_number AND bok.ship_class = $ship_class_id
	// 		join app.t_mtr_branch branch on bok.branch_code = branch.branch_code
	// 		join core.t_mtr_team mt on ar.team_code = mt.team_code
	// 		join app.t_mtr_shift shift on ar.shift_id = shift.id
	// 		join app.t_mtr_port port on branch.port_id = port.id
	// 		join app.t_mtr_port port_origin on bok.origin = port_origin.id
	// 		join app.t_mtr_port port_destination on bok.destination = port_destination.id
	// 		join core.t_mtr_user users on ar.supervisor_id = users.id
	// 		{$where}
	// 		group by up.assignment_date,up.assignment_code, branch.branch_name,  port.name, port_origin.name, port_destination.name, up.assignment_code, shift.shift_name, mt.team_name, users.id");
	// }

	public function old_list_detail_passanger($get){

		$sql = "SELECT name, ticket_count, dock_fee, total_amount 
				FROM app.t_mtr_passanger_type pt
				LEFT JOIN (
				SELECT
					b.passanger_type_id,
					COUNT ( DISTINCT b.ID ) AS ticket_count,
					(b.dock_fee*0.9) as dock_fee,
					COUNT ( DISTINCT b.id ) * (b.dock_fee*0.9) AS total_amount 
				FROM
					app.t_trx_booking bok
					JOIN app.t_trx_booking_passanger b ON b.booking_code = bok.booking_code AND b.status = 5 AND bok.service_id = 1 
					JOIN app.t_trx_boarding_passanger bo ON bo.ticket_number = b.ticket_number and bo.dock_id = 4
					JOIN app.t_trx_open_boarding bb ON bb.boarding_code = bo.boarding_code 
				WHERE
					bb.status = 0 
					AND bb.shift_date = '{$get[0]}' 
					AND bb.ship_class = {$get[1]}
					AND bb.shift_id = {$get[2]}
					AND bb.port_id = {$get[3]}
				GROUP BY
					b.passanger_type_id,
					b.dock_fee) trx ON trx.passanger_type_id = pt.id WHERE id IN (1,2) ORDER BY pt.id";

		$result = $this->db->query($sql);				
		return $result->result();
	}

	public function list_detail_passanger($port, $datefrom, $dateto, $regu, $ship_class, $shift, $petugas)
	{
		$where_port = "";
		$where_regu = "";
		$where_ship_class = "";
		$where_shift = "";
		$where_petugas = "";

		if ($port != "") {
			$where_port = " AND bb.port_id = $port";
		}

		if ($regu != "") {
			$where_regu = " AND UP.team_code = '$regu'";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND bb.ship_class = $ship_class";
		}

		if ($shift != "") {
			$where_shift = " AND bb.shift_id = $shift";
		}

		if ($petugas != "") {
			$where_petugas = " AND OB.user_id = $petugas";
		}

		$sql = "SELECT name, coalesce(ticket_count,0) AS produksi, coalesce(dock_fee,0) AS harga, coalesce(total_amount,0) AS pendapatan
				FROM app.t_mtr_passanger_type pt
				LEFT JOIN (
				SELECT
					b.passanger_type_id,
					COUNT ( DISTINCT b.ID ) AS ticket_count,
					(b.dock_fee*0.9) as dock_fee,
					COUNT ( DISTINCT b.id ) * (b.dock_fee*0.9) AS total_amount 
				FROM
					app.t_trx_booking bok
					JOIN app.t_trx_booking_passanger b ON b.booking_code = bok.booking_code AND b.status = 5 AND bok.service_id = 1 
					JOIN app.t_trx_boarding_passanger bo ON bo.ticket_number = b.ticket_number and bo.dock_id = 4
					JOIN app.t_trx_open_boarding bb ON bb.boarding_code = bo.boarding_code
					JOIN app.t_trx_sell S ON S.trans_number = bok.trans_number
					JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code $where_petugas
					JOIN app.t_trx_assignment_user_pos UP ON UP.assignment_code = OB.assignment_code $where_regu
				WHERE
					bb.status = 0 
					AND bb.shift_date BETWEEN '$datefrom' AND '$dateto'
					$where_ship_class
					$where_shift
					$where_port
				GROUP BY
					b.passanger_type_id,
					b.dock_fee) trx ON trx.passanger_type_id = pt.id WHERE status = 1 ORDER BY pt.id";

		if ($this->db->query($sql)->num_rows() > 0) {
			return $this->db->query($sql)->result();
		}else{
			return false;
		}
	}

	public function list_detail_vehicle($port, $datefrom, $dateto, $regu, $ship_class, $shift, $petugas)
	{
		$where_port = "";
		$where_regu = "";
		$where_ship_class = "";
		$where_shift = "";
		$where_petugas = "";

		if ($port != "") {
			$where_port = " AND ttob.port_id = $port";
		}

		if ($regu != "") {
			$where_regu = " AND UP.team_code = '$regu'";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND ttob.ship_class = $ship_class";
		}

		if ($shift != "") {
			$where_shift = " AND ttob.shift_id = $shift";
		}

		if ($petugas != "") {
			$where_petugas = " AND OB.user_id = $petugas";
		}

		$sql = "SELECT name, 
				coalesce(ticket_count, 0) as produksi, 
				coalesce(dock_fee, 0) as harga, 
				coalesce(total_amount, 0) as pendapatan
					FROM app.t_mtr_vehicle_class tmvc
					LEFT JOIN (
					SELECT
						ttbv.vehicle_class_id,
						COUNT ( DISTINCT ttbv.id ) AS ticket_count,
						(ttbv.dock_fee*0.9) as dock_fee,
						COUNT ( DISTINCT ttbv.id ) * (ttbv.dock_fee*0.9) AS total_amount 
					FROM
						app.t_trx_booking ttb
						join app.t_trx_booking_vehicle ttbv on ttbv.booking_code = ttb.booking_code and ttbv.status = 5
						and ttb.service_id = 2 
						join app.t_trx_boarding_vehicle ttov on ttov.ticket_number = ttbv.ticket_number and ttov.dock_id = 4
						join app.t_trx_open_boarding ttob on ttob.boarding_code = ttov.boarding_code
						JOIN app.t_trx_sell S ON S.trans_number = ttb.trans_number
						JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code $where_petugas
						JOIN app.t_trx_assignment_user_pos UP ON UP.assignment_code = OB.assignment_code $where_regu
					WHERE
						ttob.status = 0 
						AND ttob.shift_date BETWEEN '$datefrom' AND '$dateto' 
						$where_ship_class
						$where_shift
						$where_port
					GROUP BY
						ttbv.vehicle_class_id,
						ttbv.dock_fee) trx ON trx.vehicle_class_id = tmvc.id 
						WHERE tmvc.status=1 ORDER BY tmvc.id ASC";

		if ($this->db->query($sql)->num_rows() > 0) {
			return $this->db->query($sql)->result();
		}else{
			return false;
		}
	}

	public function get_lintasan($port,$datefrom,$dateto,$ship_class)
	{
		$where_port = "";
		$where_ship_class = "";

		if ($port != "") {
			$where_port = " AND UP.port_id = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		$sql = "SELECT DISTINCT
					PO.name as origin,
					PD.name as destination
				FROM
					app.t_trx_assignment_user_pos UP
					JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
					JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number $where_ship_class
					JOIN app.t_mtr_port PO ON PO.id = BO.origin
					JOIN app.t_mtr_port PD ON PD.id = BO.destination
				WHERE
					UP.assignment_date BETWEEN '$datefrom' AND '$dateto'
				$where_port";

		if ($this->db->query($sql)->num_rows() > 0) {
			return $this->db->query($sql);
		}else{
			return false;
		}
	}

	public function old_list_detail_vehicle($get){

		$sql = "SELECT name, 
			coalesce(ticket_count, 0) as ticket_count, 
			coalesce(dock_fee, 0) as dock_fee, 
			coalesce(total_amount, 0) as total_amount
				FROM app.t_mtr_vehicle_class tmvc
				LEFT JOIN (
				SELECT
					ttbv.vehicle_class_id,
					COUNT ( DISTINCT ttbv.id ) AS ticket_count,
					(ttbv.dock_fee*0.9) as dock_fee,
					COUNT ( DISTINCT ttbv.id ) * (ttbv.dock_fee*0.9) AS total_amount 
				FROM
					app.t_trx_booking ttb
					join app.t_trx_booking_vehicle ttbv on ttbv.booking_code = ttb.booking_code and ttbv.status = 5
					and ttb.service_id = 2 
					join app.t_trx_boarding_vehicle ttov on ttov.ticket_number = ttbv.ticket_number and ttov.dock_id = 4--{$get[3]}
					join app.t_trx_open_boarding ttob on ttob.boarding_code = ttov.boarding_code 					
				WHERE
					ttob.status = 0 
					AND ttob.shift_date = '{$get[0]}' 
					AND ttob.ship_class = {$get[1]} 
					AND ttob.shift_id = {$get[2]}
					AND ttob.port_id = {$get[3]}
				GROUP BY
					ttbv.vehicle_class_id,
					ttbv.dock_fee) trx ON trx.vehicle_class_id = tmvc.id 
					WHERE tmvc.status=1 ORDER BY tmvc.id ASC";

		$result = $this->db->query($sql);				
		return $result->result();
	}

	public function get_team($port_id)
	{
		return $this->db->query("SELECT team_code,team_name FROM core.t_mtr_team WHERE port_id = $port_id ORDER BY team_name ASC")->result();
	}

}