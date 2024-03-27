<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pendapatan_masuk_pelabuhan_model extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'laporan/pendapatan_masuk_pelabuhan';
	}

	public function dataList()
	{
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
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

		$field = array(
			0 => 'shift_date',
			1 => 'port_name',
			2 => 'ship_name',
			3 => 'branch_name',
			4 => 'shift_name',
			5 => 'team_name'
		);

		$where = " WHERE ar.status = 2";

		if ((!empty($dateFrom) and empty($dateTo)) || (empty($dateFrom) and !empty($dateTo))) {
			$where .= " AND ob.shift_date ='$dateFrom' or ob.shift_date ='$dateTo'";
		}

		if (!empty($dateFrom) and !empty($dateTo)) {
			$where .= " AND ob.shift_date BETWEEN '$dateFrom' and  '$dateTo'";
		}

		if (!empty($branch)) {
			$where .= " AND (branch_code = '" . $branch . "')";
		}

		if (!empty($shift)) {
			$where .= " AND (ob.shift_id = " . $shift . ")";
		}

		if (!empty($search['value'])) {
			$where .= " AND ( branch_name ilike '%" . $iLike . "%' or shift_name ilike '%" . $iLike . "%' or team_name ilike '%" . $iLike . "%')";
		}

		$sql = "SELECT DISTINCT
					ob.shift_date,
					p.name AS port_name,
					ob.port_id,
					sc.name AS ship_name,
					ob.ship_class,
					shift_name,
					ob.shift_id,
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

		$query          = $this->dbView->query($sql);
		$records_total  = $query->num_rows();

		$order_by = '';
		foreach ($order as $key => $value) {
			$order_column = $order[$key]['column'];
			$order_dir    = strtoupper($order[$key]['dir']);
			$order_column = $field[$order_column];


			$order_by .= "{$order_column} {$order_dir},";
		}

		$order_by = rtrim($order_by, ",");
		$sql .= " ORDER BY {$order_by}";

		if ($length != -1) {
			$sql .= " limit {$length} offset {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$param 	= $this->enc->encode($row->shift_date . '|' . $row->ship_class . '|' . $row->shift_id . '|' . $row->branch_name . '|' . $row->port_name . '|' . $row->origin . '|' . $row->destination . '|' . $row->shift_name . '|' . $row->team_name . '|' . $row->spv);

			$detail_url = site_url($this->_module . "/detail/{$param}");
			$pdf_url    = site_url($this->_module . "/download_pdf/{$param}");
			$excel_url    = site_url($this->_module . "/download_excel/{$param}");

			$row->actions = '';
			$row->actions .= generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions .= generate_button_new($this->_module, 'download_pdf', $pdf_url);
			$row->actions .= generate_button_new($this->_module, 'download_excel', $excel_url);

			$row->shift_date = format_date($row->shift_date);
			$row->no = $i;

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'data'           => $rows
		);
	}

	public function old_list_detail_passanger($get)
	{
		$sql = "SELECT name, ticket_count, entry_fee, total_amount 
				FROM app.t_mtr_passanger_type pt
				LEFT JOIN (
				SELECT
					b.passanger_type_id,
					COUNT ( DISTINCT b.ID ) AS ticket_count,
					b.entry_fee,
					COUNT ( DISTINCT b.ID ) * b.entry_fee AS total_amount 
				FROM
					app.t_trx_booking bok
					JOIN app.t_trx_booking_passanger b ON b.booking_code = bok.booking_code and passanger_type_id IN (1,2) -- AND b.status = 5 AND passanger_type_id IN (1,2)
					JOIN app.t_trx_boarding_passanger bo ON bo.ticket_number = b.ticket_number
					JOIN app.t_trx_open_boarding bb ON bb.boarding_code = bo.boarding_code 
				WHERE
					bok.service_id = 1
					AND bb.status = 0 
					AND bb.shift_date = '{$get[0]}' 
					AND bb.ship_class = {$get[1]} 
					AND bb.shift_id = {$get[2]}
				GROUP BY
					b.passanger_type_id,
					b.entry_fee) trx ON trx.passanger_type_id = pt.id WHERE id IN (1,2) ORDER BY pt.id";

		$data = array();
		$prod = 0;
		$pend = 0;

		foreach ($this->dbView->query($sql)->result() as $row) {
			$prod += $row->ticket_count;
			$pend += $row->total_amount;

			$data[] = $row;
		}


		return array(
			'data' => $data,
			'produksi' => $prod,
			'pendapatan' => $pend
		);
	}

	public function list_detail_passanger($port_id, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType)
	{
		$where_port = "";
		$where_ship_class = "";
		$where_shift = "";
		$whereManual = " ";

		if(!empty($ticketType)) // tipe tiket 3 adalah tiket manual
		{
			if($ticketType==3)
			{

				$whereManual = " and b.ticket_type='3' ";
			}
			else
			{
				$whereManual = " and b.ticket_type !='3' ";
			}
		}

		if ($port_id != "") {
			$where_port = " AND bb.port_id = $port_id";
		}

		if ($ship_class_id != "") {
			$where_ship_class = " AND bb.ship_class = $ship_class_id";
		}

		if ($shift_id != "") {
			$where_shift = " AND bb.shift_id = $shift_id";
		}

		$sql = "SELECT name, COALESCE(ticket_count,0) AS ticket_count, COALESCE(entry_fee,0) AS entry_fee, COALESCE(total_amount,0) AS total_amount
				FROM app.t_mtr_passanger_type pt
				LEFT JOIN (
				SELECT
					b.passanger_type_id,
					COUNT ( DISTINCT bo.id ) AS ticket_count,
					b.entry_fee,
					COUNT ( DISTINCT bo.id ) * b.entry_fee AS total_amount 
				FROM
					app.t_trx_booking bok
					JOIN app.t_trx_booking_passanger b ON b.booking_code = bok.booking_code
					JOIN app.t_trx_boarding_passanger bo ON bo.ticket_number = b.ticket_number
					JOIN app.t_trx_open_boarding bb ON bb.boarding_code = bo.boarding_code 
				WHERE
					bok.service_id = 1
					AND bb.status = 0 
					AND bb.shift_date BETWEEN '$datefrom' AND '$dateto'
					AND bo.status = 1
					$where_port
					$where_ship_class 
					$where_shift
					$whereManual
				GROUP BY
					b.passanger_type_id,
					b.entry_fee) trx ON trx.passanger_type_id = pt.id 
					WHERE id IN (1,2,3,4)
					ORDER BY pt.ordering asc
					-- ORDER BY pt.id
					
					";
		return $this->dbView->query($sql)->result();
		// if ($result) {
		// 	return $result;
		// }else{
		// 	return false;
		// }

		// $data = array();
		// $prod = 0;
		// $pend = 0;

		// foreach ($this->dbView->query($sql)->result() as $row) {
		// 	$prod += $row->ticket_count;
		// 	$pend += $row->total_amount;

		// 	$data[] = $row;
		// }

		// return array(
		// 	'data' => $data,
		// 	'produksi' => $prod,
		// 	'pendapatan' => $pend
		// );
	}

	public function old_list_detail_vehicle($get)
	{
		$sql = "SELECT name, ticket_count, entry_fee, total_amount 
				FROM app.t_mtr_vehicle_class vc
				LEFT JOIN (
				SELECT
					b.vehicle_class_id,
					COUNT ( DISTINCT b.ID ) AS ticket_count,
					b.entry_fee,
					COUNT ( DISTINCT b.ID ) * b.entry_fee AS total_amount 
				FROM
					app.t_trx_booking bok
					JOIN app.t_trx_booking_vehicle b ON b.booking_code = bok.booking_code -- AND b.status = 5
					JOIN app.t_trx_boarding_vehicle bo ON bo.ticket_number = b.ticket_number
					JOIN app.t_trx_open_boarding bb ON bb.boarding_code = bo.boarding_code 
				WHERE
					bok.service_id = 2
					AND bb.status = 0 
					AND bb.shift_date = '{$get[0]}' 
					AND bb.ship_class = {$get[1]} 
					AND bb.shift_id = {$get[2]}
				GROUP BY
					b.vehicle_class_id,
					b.entry_fee) trx ON trx.vehicle_class_id = vc.id ORDER BY vc.id ASC";

		$data = array();
		$prod = 0;
		$pend = 0;

		foreach ($this->dbView->query($sql)->result() as $row) {
			$prod += $row->ticket_count;
			$pend += $row->total_amount;

			$data[] = $row;
		}


		return array(
			'data' => $data,
			'produksi' => $prod,
			'pendapatan' => $pend
		);
	}

	public function list_detail_vehicle($port_id, $datefrom, $dateto, $ship_class_id, $shift_id,$ticketType )
	{
		$where_port = "";
		$where_ship_class = "";
		$where_shift = "";
		$whereManual = " ";

		if(!empty($ticketType)) // tipe tiket 3 adalah tiket manual
		{
			if($ticketType==3)
			{

				$whereManual = " and b.ticket_type='3' ";
			}
			else
			{
				$whereManual = " and b.ticket_type !='3' ";
			}
		}	

		if ($port_id != "") {
			$where_port = " AND bb.port_id = $port_id";
		}

		if ($ship_class_id != "") {
			$where_ship_class = " AND bb.ship_class = $ship_class_id";
		}

		if ($shift_id != "") {
			$where_shift = " AND bb.shift_id = $shift_id";
		}

		$sql = "SELECT name, COALESCE(ticket_count,0) AS ticket_count, COALESCE(entry_fee,0) AS entry_fee, COALESCE(total_amount,0) AS total_amount
				FROM app.t_mtr_vehicle_class vc
				LEFT JOIN (
				SELECT
					b.vehicle_class_id,
					COUNT ( DISTINCT bo.id ) AS ticket_count,
					b.entry_fee,
					COUNT ( DISTINCT bo.id ) * b.entry_fee AS total_amount 
				FROM
					app.t_trx_booking bok
					JOIN app.t_trx_booking_vehicle b ON b.booking_code = bok.booking_code
					JOIN app.t_trx_boarding_vehicle bo ON bo.ticket_number = b.ticket_number
					JOIN app.t_trx_open_boarding bb ON bb.boarding_code = bo.boarding_code 
				WHERE
					bok.service_id = 2
					AND bb.status = 0 
					-- AND entry_fee != 0
					AND bb.shift_date BETWEEN '$datefrom' AND '$dateto'
					AND bo.status = 1
					$where_port 
					$where_ship_class
					$where_shift
					$whereManual
				GROUP BY
					b.vehicle_class_id,
					b.entry_fee) trx ON trx.vehicle_class_id = vc.id ORDER BY vc.id ASC";

		return $this->dbView->query($sql)->result();

		// $data = array();
		// $prod = 0;
		// $pend = 0;

		// foreach ($this->dbView->query($sql)->result() as $row) {
		// 	$prod += $row->ticket_count;
		// 	$pend += $row->total_amount;

		// 	$data[] = $row;
		// }


		// return array(
		// 	'data' => $data,
		// 	'produksi' => $prod,
		// 	'pendapatan' => $pend
		// );

		// if ($this->dbView->query($sql)->num_rows() > 0) {
		// 	return $this->dbView->query($sql)->result();
		// }else{
		// 	return false;
		// }
	}

	public function headerku($port_id, $datefrom, $dateto, $ship_class_id, $shift_id)
	{
		$where_port = "";
		$where_ship_class = "";
		$where_shift_1 = "";
		$where_shift_2 = "";

		if ($port_id != "") {
			$where_port = " AND bor.port_id = $port_id";
		}

		if ($ship_class_id != "") {
			$where_ship_class = " AND bov.ship_class = $ship_class_id";
		}

		if ($shift_id != "") {
			$where_shift_1 = " AND ar.shift_id = $shift_id";
			$where_shift_2 = " AND bor.shift_id = $shift_id";
		}

		$sql = "SELECT
					DISTINCT(bor.port_id),
					port.name as port,
					bor.shift_date,
					ori.name as origin,
					desti.name as destination,
					sh.shift_name,
					t.team_name,
					br.branch_name
				FROM
					app.t_trx_open_boarding bor
					JOIN app.t_trx_boarding_vehicle bov ON bov.boarding_code = bor.boarding_code AND bov.status = 1
					JOIN app.t_trx_booking_vehicle bv ON bv.ticket_number = bov.ticket_number AND bv.status = 5
					JOIN app.t_mtr_vehicle_class VC ON VC.id = bv.vehicle_class_id
					JOIN app.t_trx_booking bok ON bok.booking_code = bv.booking_code
					JOIN app.t_mtr_port ori ON ori.id = bok.origin
					JOIN app.t_mtr_port desti ON desti.id = bok.destination
					JOIN app.t_trx_assignment_regu ar ON ar.assignment_date BETWEEN '$datefrom' AND '$dateto' $where_shift_1
					JOIN core.t_mtr_team t ON t.team_code = ar.team_code
					JOIN app.t_mtr_branch br ON br.branch_code = bok.branch_code
					JOIN app.t_mtr_shift sh ON sh.id = bor.shift_id
					JOIN app.t_mtr_port port ON port.id = bor.port_id
				WHERE
					shift_date BETWEEN '$datefrom' AND '$dateto'
					$where_port
					$where_ship_class
					$where_shift_2";

		if ($this->dbView->query($sql)->num_rows() == 1) {
			return $this->dbView->query($sql)->row();
		}
	}

	//old
	public function detail_trip($where = "", $ship_class_id)
	{

		return $this->dbView->query("SELECT up.assignment_date, branch.branch_name, port.name as port_name, (port_origin.name||'-'||port_destination.name) as trip, up.assignment_code, shift.shift_name, mt.team_name, coalesce(users.first_name, '')||' '||coalesce(users.last_name, '') as spv
			from app.t_trx_assignment_user_pos up 
			join app.t_trx_assignment_regu ar on up.assignment_code = ar.assignment_code 
			join app.t_trx_opening_balance ob on ob.assignment_code = up.assignment_code
			join app.t_trx_sell sel on sel.ob_code = ob.ob_code
			join app.t_trx_invoice inv on inv.trans_number = sel.trans_number
			join app.t_trx_booking bok on bok.trans_number = inv.trans_number AND bok.ship_class = $ship_class_id
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

	public function list_detail_passanger_old($where = "", $ship_class_id)
	{

		return $this->dbView->query("SELECT passanger.name as passanger_type_name, ticket_count, entry_fee, total_amount
		from app.t_mtr_passanger_type passanger 
		left join (SELECT bp.passanger_type_id, count( distinct bp.id) as ticket_count, bp.entry_fee, count( distinct bp.id) * bp.entry_fee as total_amount
			from app.t_trx_opening_balance ob 
			join app.t_trx_sell sel on sel.ob_code = ob.ob_code
			join app.t_trx_invoice inv on inv.trans_number = sel.trans_number
			join app.t_trx_booking bok on bok.trans_number = inv.trans_number and bok.service_id = 1 AND bok.ship_class = $ship_class_id
			join app.t_trx_booking_passanger bp on bp.booking_code = bok.booking_code and bp.status = 5
			{$where}
			group by bp.passanger_type_id, bp.entry_fee) trx on passanger.id = trx.passanger_type_id
			where passanger.id not in (3)");
	}

	public function list_detail_vehicle_old($where = "", $ship_class_id)
	{

		return $this->dbView->query("SELECT vehicle.name as vehicle_class_name, ticket_count, entry_fee, total_amount 
			from app.t_mtr_vehicle_class vehicle 
			left join
			(select bv.vehicle_class_id, count( distinct bv.id) as ticket_count, bv.entry_fee, count( distinct bv.id) * bv.entry_fee as total_amount
			from app.t_trx_opening_balance ob 
			join app.t_trx_sell sel on sel.ob_code = ob.ob_code
			join app.t_trx_invoice inv on inv.trans_number = sel.trans_number
			join app.t_trx_booking bok on bok.trans_number = inv.trans_number and bok.service_id = 2 AND bok.ship_class = $ship_class_id
			join app.t_trx_booking_vehicle bv on bv.booking_code = bok.booking_code and bv.status = 5
			{$where}
			group by bv.vehicle_class_id, bv.entry_fee) trx on vehicle.id = trx.vehicle_class_id
			where status = 1
			order by vehicle.id");
	}

	public function sub_total_passanger_old($where = "", $ship_class_id)
	{

		return $this->dbView->query("SELECT count( DISTINCT bp.id) AS ticket_count, sum(bp.entry_fee) AS sub_total_amount
		from app.t_trx_opening_balance ob 
		join app.t_trx_sell sell on ob.ob_code = sell.ob_code
		join app.t_trx_invoice inv on sell.trans_number = inv.trans_number 
		join app.t_trx_booking bok on inv.trans_number = bok.trans_number and bok.service_id = 1 AND bok.ship_class = $ship_class_id
		join app.t_trx_booking_passanger bp on bok.booking_code = bp.booking_code and bp.status = 5 AND bp.passanger_type_id != 3
		{$where}");
	}

	public function sub_total_vehicle_old($where = "", $ship_class_id)
	{
		return $this->dbView->query("SELECT count( distinct bv.id) as ticket_count, sum(bv.entry_fee) as total_amount
			from app.t_trx_opening_balance ob 
			join app.t_trx_sell sell on ob.ob_code = sell.ob_code
			join app.t_trx_invoice inv on sell.trans_number = inv.trans_number 
			join app.t_trx_booking bok on inv.trans_number = bok.trans_number and bok.service_id = 2 AND bok.ship_class = $ship_class_id
			join app.t_trx_booking_vehicle bv on bv.booking_code = bok.booking_code and bv.status = 5
			{$where}");
	}

	public function getClassBySession($type = 'cek')
	{
		$session_shift_class = $this->session->userdata('ship_class_id');
		$sql = "SELECT * FROM app.t_mtr_ship_class WHERE status=1";

		if ($type == 'option') {
			$result = array();
			if ($session_shift_class != '') {
				$data = $this->dbView->query($sql . " and id = {$session_shift_class}");
				if ($data->num_rows() > 0) {
					$getData = $data->row();
					$result[] = array('id' => $getData->id, 'name' => $getData->name);
				}
			} else {
				$data = $this->dbView->query($sql)->result();
				$result[] = array('id' => '', 'name' => 'Semua');
				foreach ($data as $key => $value) {
					$result[] = array('id' => $value->id, 'name' => $value->name);
				}
			}
			return $result;
		} else {
			if ($session_shift_class != '') {
				$data = $this->dbView->query($sql . " and id = {$session_shift_class}");
				if ($data->num_rows() > 0) {
					$getData = $data->row();
					return array('id' => $getData->id, 'name' => $getData->name);
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
}