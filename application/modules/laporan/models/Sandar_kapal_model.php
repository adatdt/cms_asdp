<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sandar_kapal_model extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'laporan/sandar_kapal';
	}

	public function dataList()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$port = $this->enc->decode($this->input->post('port'));
		$shift = $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));
		$session_shift_class = $this->session->userdata('ship_class_id');

		$field = array(
			0 => 'assignment_date',
			1 => 'port.name',
			2 => 'shift_name',
			3 => 'team_name'
		);

		$order_column = $field[$order_column];
		$where = " where up.status = 2 ";

		if ((!empty($dateFrom) and empty($dateTo)) || (empty($dateFrom) and !empty($dateTo))) {
			$where .= " and up.assignment_date ='$dateFrom' or up.assignment_date ='$dateTo'";
		}

		if (!empty($dateFrom) and !empty($dateTo)) {
			$where .= " and up.assignment_date between '$dateFrom' and  '$dateTo'";
		}

		if (!empty($port)) {
			$where .= " and (up.port_id = '" . $port . "')";
		}

		if (!empty($search['value'])) {
			$where .= " and ( branch_name ilike '%" . $iLike . "%' or shift_name ilike '%" . $iLike . "%' or team_name ilike '%" . $iLike . "%')";
		}

		if (!empty($session_shift_class)) {
			$where .= " AND bok.ship_class = '" . $session_shift_class . "'";
		}

		$sql = "SELECT
				up.assignment_date,
				PORT.name as pelabuhan,
				up.port_id,
				up.assignment_code,
				shift.shift_name,
				shift.shift_login,
				shift.shift_logout,
				mt.team_name, 
				ar.shift_id
			FROM
				app.t_trx_assignment_user_pos up
				JOIN app.t_trx_assignment_regu ar ON up.assignment_code = ar.assignment_code
				JOIN app.t_trx_opening_balance ob ON ob.assignment_code = up.assignment_code
				JOIN app.t_trx_sell sel ON sel.ob_code = ob.ob_code
				JOIN app.t_trx_invoice inv ON inv.trans_number = sel.trans_number
				JOIN app.t_trx_booking bok ON bok.trans_number = inv.trans_number
				JOIN app.t_trx_booking_passanger bp ON bp.booking_code = bok.booking_code
				JOIN app.t_mtr_branch branch ON bok.branch_code = branch.branch_code
				JOIN core.t_mtr_team mt ON ar.team_code = mt.team_code
				JOIN app.t_mtr_shift shift ON ar.shift_id = shift.ID
				JOIN app.t_mtr_port PORT ON PORT.id = UP.port_id
				JOIn app.t_mtr_ship_class SC ON SC.id = bok.ship_class
			{$where}
			GROUP BY
				up.assignment_date,
				up.port_id,
				PORT.name,
				up.assignment_code,
				ar.shift_id,
				shift.shift_name,
				ar.team_code,
				shift.shift_login,
				shift.shift_logout,
				mt.team_name";

		$query          = $this->dbView->query($sql);
		$records_total  = $query->num_rows();

		$sql .= " order by " . $order_column . " {$order_dir}";

		if ($length != -1) {
			$sql .= " limit {$length} offset {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$shiftTime = date('H:i', strtotime($row->shift_login)) . " s.d " . date('H:i', strtotime($row->shift_logout));

			$ship_class_id 	= $this->enc->encode($row->ship_class_id);
			$code 			= $this->enc->encode($row->assignment_code);
			$detail_url 	= site_url($this->_module . "/detail/{$code}/{$ship_class_id}");
			$pdf_url 		= site_url($this->_module . "/download_pdf?shift={$row->shift_id}&port={$row->port_id}&date={$row->assignment_date}&port_name={$row->pelabuhan}&shift_time={$shiftTime}&team_name={$row->team_name}");
			$excel_url 		= site_url($this->_module . "/download_excel?shift={$row->shift_id}&port={$row->port_id}&date={$row->assignment_date}&port_name={$row->pelabuhan}&shift_time={$shiftTime}&team_name={$row->team_name}");

			$row->actions 	= "";
			$row->actions 	.= generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_pdf', $pdf_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_excel', $excel_url);
			$row->assignment_date = format_date($row->assignment_date);
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

	public function old_detail_pdf($date, $port, $shift)
	{

		$sql = "
			SELECT 
				tms.id,
				tms.name as ship_name,
				string_agg(sandar.dock_id::TEXT,',') dock_id,
				COALESCE(sandar.total_sandar,0) AS total_sandar
			FROM app.t_mtr_ship tms 
			LEFT JOIN (
				SELECT 
					distinct (ttob.boarding_code),
					ttob.ship_id,
					ttob.dock_id,
					(ttsc.dock_fare * ttsc.ship_grt * ttsc.call) as total_sandar
				FROM app.t_trx_open_boarding ttob
				JOIN app.t_trx_boarding_passanger ttbp ON ttbp.boarding_code = ttob.boarding_code
				JOIN app.t_trx_booking_passanger ttbop ON ttbop.ticket_number = ttbp.ticket_number
				JOIN app.t_trx_booking ttb ON ttb.booking_code = ttbop.booking_code
				JOIN app.t_trx_sell tts ON tts.trans_number = ttb.trans_number
				JOIN app.t_trx_opening_balance ttoba ON ttoba.ob_code = tts.ob_code
				JOIN app.t_trx_assignment_user_pos ttaup ON ttaup.assignment_code = ttoba.assignment_code
				JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code
				WHERE ttob.port_id = {$port}
				AND ttoba.shift_id = {$shift}
				AND ttaup.assignment_date = '{$date}'
			) AS sandar ON sandar.ship_id = tms.id
			WHERE
				tms.status = 1
			OR (
				sandar.boarding_code is not null AND tms.status != 1
			)
			GROUP BY
				tms.id,
				tms.name,
				sandar.total_sandar
			ORDER BY tms.name ASC
		";

		$result = $this->dbView->query($sql);

		return $result->result();
	}

	public function get_shift_time($shift_id, $port_id)
	{
		if ($shift_id != "" && $port_id != "") {
			$sql = $this->dbView->query("SELECT shift_login,shift_logout FROM app.t_mtr_shift_time WHERE shift_id = $shift_id AND port_id = $port_id");

			if ($sql->num_rows() > 0) {
				return $sql->row();
			} else {
				return false;
			}
		} else {
			return false;
		}
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

	public function old_new_sandar_kapal($datefrom, $dateto, $port, $shift)
	{
		$where_port = "";
		$where_port_sub = "";
		$where_ship_class = "";
		$where_shift = "";
		$where_shift_sub = "";

		if ($port != "") {
			$where_port = " AND A.port_id = $port";
			$where_port_sub = " AND ttob.port_id = $port";
		}

		if ($shift != "") {
			$where_shift = " AND A.shift_id = $shift";
			$where_shift_sub = " AND ttob.shift_id = $shift ";
		}

		$sql = "SELECT UPPER
					( company ) company,
					UPPER ( NAME ) ship_name,
					ship_grt,
					dock_fare,
					COUNT trip,
					dock 
				FROM
					(
				SELECT
					ship_id,
					ship_grt,
					dock_fare,
					json_object_agg ( dock_id, total ORDER BY dock_id ) AS dock 
				FROM
					(
				SELECT 
					A.ID ship_id,
					b.dock_fare,
					b.ship_grt,
					COALESCE ( dock_id, 0 ) dock_id,
					COALESCE ( trip, 0 ) :: VARCHAR || '-' || COALESCE ( (ship_grt * call * dock_fare) + ( trip * tambat_fare), 0 ) :: VARCHAR total 
				FROM
					app.t_mtr_ship A LEFT JOIN (
				SELECT
					b.ID,
					d.ID dock_id,
					COUNT ( 0 ) trip,
					C.dock_fare,
					C.tambat_fare,
					C.ship_grt,
					C.call
				FROM
					app.t_trx_open_boarding
					A JOIN app.t_mtr_ship b ON b.ID = A.ship_id
					JOIN app.t_trx_schedule C ON C.schedule_code = A.schedule_code 
					AND C.status = 1
					JOIN app.t_mtr_dock d ON d.ID = A.dock_id 
				WHERE
					A.shift_date BETWEEN '$datefrom' AND '$dateto' 
					AND A.status = 0 
					$where_shift
					$where_port
				GROUP BY
					b.ID,
					d.ID,
					CALL,
					C.dock_fare,
					C.tambat_fare,
					C.ship_grt,
					C.call
					) b ON b.ID = A.ID 
				ORDER BY
					A.NAME 
					) A 
				GROUP BY
					ship_id,
					ship_grt,
					dock_fare
					) dock
					LEFT JOIN (
				SELECT
					shc.NAME AS company,
					tms.NAME,
					tms.ID,
					COALESCE ( COUNT, 0 ) COUNT 
				FROM
					app.t_mtr_ship tms
					LEFT JOIN app.t_mtr_ship_company shc ON shc.ID = tms.ship_company_id
					LEFT JOIN (
				SELECT
					DISTINCT(ttsc.dock_fare),
					ttob.ship_id,
					COUNT ( 0 ) 
				FROM
					app.t_trx_open_boarding ttob
					JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code 
					AND ttsc.status = 1 
				WHERE
					ttsc.status = 1 
					AND ttob.shift_date BETWEEN '$datefrom' AND '$dateto' 
					$where_port_sub
					$where_shift_sub
				GROUP BY
					ttob.ship_id,
					ttsc.dock_fare
					) A ON A.ship_id = tms.ID 
					) trx ON trx.ID = dock.ship_id 
				ORDER BY
					company ASC,
					ship_name ASC";
		return $this->dbView->query($sql)->result();
	}

	public function latest_sandar($datefrom, $dateto, $port, $shift)
	{
		$where_port = "";
		$where_shift = "";
		$where_sc = "";
		$session_shift_class = $this->session->userdata('ship_class_id');

		if ($port != "") {
			$where_port = " AND ttob.port_id = $port";
		}

		if ($shift != "") {
			$where_shift = " AND ttob.shift_id = $shift";
		}

		if ($session_shift_class != "") {
			$where_sc = " AND ttob.ship_class = $session_shift_class";
		}

		$sql = "SELECT 
					company_name,
					ship_name, 
					dock_fare,
					ship_grt,
					SUM(trip) trip,
					COALESCE(call,0) as call,
					json_object_agg ( dock_id, total ORDER BY dock_id ) AS dock
					FROM
					(SELECT DISTINCT

									(tms.ID),
									COALESCE(dock_id,0) dock_id,
									COALESCE(tms.ship_company_id,0) company,
									tss.name as company_name,
									tms.NAME ship_name,
									COALESCE(dock_fare,0) dock_fare,
									COALESCE(tambat_fare,0) tambat_fare,
									COALESCE(ship_grt,0) ship_grt,
									call,
									COALESCE ( ( ship_grt * CALL * dock_fare * COALESCE ( COUNT, 0 )) + ( COALESCE ( COUNT, 0 ) * tambat_fare ), 0 ) :: VARCHAR total,
									COALESCE ( COUNT, 0 ) trip 
								FROM
									app.t_mtr_ship tms
									LEFT JOIN app.t_mtr_ship_company tss ON tss.id = tms.ship_company_id
									LEFT JOIN (SELECT
																DISTINCT( ttob.ship_id ),
																ttob.dock_id,
																ttsc.dock_fare,
																ttsc.ship_grt,
																call,
																ttsc.tambat_fare,
																COUNT ( 0 ) 
															FROM
																app.t_trx_open_boarding ttob
																JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code AND ttsc.status = 1 
															WHERE
																ttsc.status = 1 
																AND ttob.shift_date BETWEEN '$datefrom' AND '$dateto' 
																AND ttob.status != -5
																$where_port
																$where_shift
																$where_sc
															GROUP BY
																ttob.ship_id,
																ttob.dock_id,
																ttsc.ship_grt,
																call,
																ttsc.tambat_fare,
																ttsc.dock_fare 
															ORDER BY
																ship_id ASC)A ON A.ship_id = tms.ID
																WHERE tms.status NOT IN (-5)
																ORDER BY id ASC) trx 
																GROUP BY
				company_name,
				call,
				ship_name,
				ship_grt,
				dock_fare";

		return $this->dbView->query($sql)->result();
	}

	public function new_sandar_kapal($datefrom, $dateto, $port, $shift)
	{
		$where_port = "";
		$where_port_sub = "";
		$where_ship_class = "";
		$where_shift = "";
		$where_shift_sub = "";

		if ($port != "") {
			$where_port = " AND A.port_id = $port";
			$where_port_sub = " AND ttob.port_id = $port";
		}

		if ($shift != "") {
			$where_shift = " AND A.shift_id = $shift";
			$where_shift_sub = " AND ttob.shift_id = $shift ";
		}

		$sql = "SELECT 
									company,
									ship_name,
									COALESCE(ship_grt,0) ship_grt,
									COALESCE(dock_fare,0) dock_fare,
									SUM(trip) trip,
									json_object_agg ( dock_id, total ORDER BY dock_id ) AS dock 
				FROM (SELECT 
									UPPER ( company ) company,
									UPPER ( NAME ) ship_name,
									adock.ship_grt,
									trx.dock_fare,
									dock_id,
									trip,
									COALESCE ( trip, 0 ) :: VARCHAR || '-' || COALESCE ( ( ship_grt * CALL * dock_fare ) + ( trip * tambat_fare ), 0 ) :: VARCHAR total
								FROM
									(SELECT
											ship_id,
											company,
											ship_name,
											ship_grt,
											CALL,
											dock_id,
											tambat_fare
										FROM(SELECT
														A.ID ship_id,
														shc.name as company,
														A.name as ship_name,
														b.ship_grt,
														b.tambat_fare,
														CALL,
														COALESCE ( dock_id, 0 ) dock_id
													FROM
														app.t_mtr_ship A
														LEFT JOIN app.t_mtr_ship_company shc ON shc.id = A.ship_company_id
														LEFT JOIN (SELECT
																					b.ID,
																					d.ID dock_id,
																					COUNT ( 0 ) trip,
																					C.tambat_fare,
																					C.ship_grt,
																					C.CALL 
																				FROM
																					app.t_trx_open_boarding A
																					JOIN app.t_mtr_ship b ON b.ID = A.ship_id
																					JOIN app.t_trx_schedule C ON C.schedule_code = A.schedule_code AND C.status = 1
																					JOIN app.t_mtr_dock d ON d.ID = A.dock_id 
																				WHERE
																					A.shift_date BETWEEN '$datefrom' AND '$dateto' 
																					AND A.status = 0 
																					$where_port
																					$where_shift
																				GROUP BY
																					b.ID,
																					d.ID,
																					CALL,
																					C.tambat_fare,
																					C.ship_grt,
																					C.CALL
																				ORDER BY
																					id ASC) b ON b.ID = A.ID
													ORDER BY
														ship_id ASC) A 
										GROUP BY
											ship_id,
											company,
											ship_name,
											call,
											tambat_fare,
											dock_id,
											ship_grt
									) adock
									JOIN (SELECT DISTINCT
																(tms.ID),
																tms.NAME,
																dock_fare,
																COALESCE ( COUNT, 0 ) trip 
															FROM
																app.t_mtr_ship tms
																LEFT JOIN (SELECT
																							DISTINCT( ttob.ship_id ),
																							ttsc.dock_fare,
																							COUNT ( 0 ) 
																						FROM
																							app.t_trx_open_boarding ttob
																							JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code AND ttsc.status = 1 
																						WHERE
																							ttsc.status = 1 
																							AND ttob.shift_date BETWEEN '$datefrom' AND '$dateto' 
																							$where_port_sub
																							$where_shift_sub
																						GROUP BY
																							ttob.ship_id,
																							ttsc.dock_fare 
																						ORDER BY
																							ship_id ASC)A ON A.ship_id = tms.ID ORDER BY id ASC
									) trx ON trx.ID = adock.ship_id
								ORDER BY
									company ASC,
									ship_name ASC) akhir
								GROUP BY
									company,
									ship_name,
									ship_grt,
									dock_fare";
		// die($sql);exit;
		return $this->dbView->query($sql)->result();
	}

	public function detail_pdf($datefrom, $dateto, $port, $shift)
	{
		$where_port = "";
		$where_shift = "";
		$session_shift_class = $this->session->userdata('ship_class_id');

		if ($port != "") {
			$where_port = " AND ttob.port_id = $port";
		}

		if ($shift != "") {
			$where_shift = " AND ttoba.shift_id = $shift";
		}

		if ($session_shift_class != "") {
			$where_sc = " AND ttb.ship_class = '" . $session_shift_class . "'";
		} else {
			$where_sc = "";
		}

		$sql = "SELECT 
					tms.id,
					shc.name as company,
					tms.name as ship_name,
					string_agg(sandar.dock_id::TEXT,',') dock_id,
					COALESCE(sandar.total_sandar,0) AS total_sandar
				FROM app.t_mtr_ship tms
				LEFT JOIN app.t_mtr_ship_company shc ON shc.id = tms.ship_company_id
				LEFT JOIN (
					SELECT 
						distinct (ttob.boarding_code),
						ttob.ship_id,
						ttob.dock_id,
						(ttsc.dock_fare * ttsc.ship_grt * ttsc.call) as total_sandar
					FROM app.t_trx_open_boarding ttob
					JOIN app.t_trx_boarding_passanger ttbp ON ttbp.boarding_code = ttob.boarding_code
					JOIN app.t_trx_booking_passanger ttbop ON ttbop.ticket_number = ttbp.ticket_number
					JOIN app.t_trx_booking ttb ON ttb.booking_code = ttbop.booking_code {$where_sc}
					JOIN app.t_trx_sell tts ON tts.trans_number = ttb.trans_number
					JOIN app.t_trx_opening_balance ttoba ON ttoba.ob_code = tts.ob_code
					JOIN app.t_trx_assignment_user_pos ttaup ON ttaup.assignment_code = ttoba.assignment_code
					JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code
				WHERE
					ttaup.assignment_date BETWEEN '$datefrom' AND '$dateto'
					$where_port
					$where_shift
				) AS sandar ON sandar.ship_id = tms.id
				WHERE
					tms.status = 1
				OR (
					sandar.boarding_code is not null AND tms.status != 1
				)
				GROUP BY
					tms.id,
					shc.name,
					tms.name,
					sandar.total_sandar
				ORDER BY shc.name ASC";

		$result = $this->dbView->query($sql);

		return $result->result();
	}
}
