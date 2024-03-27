<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jasa_tambat_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'laporan/jasa_tambat';
	}

    public function dataList(){
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

		$field = array(
			0 => 'assignment_date',
			1 => 'port.name',
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

		if(!empty($port)){
			$where .=" and (up.port_id = '".$port."')";
		}

		// if(!empty($shift)){
		// 	$where .=" and (ar.shift_id = ".$shift.")";
		// }

		if (!empty($search['value'])){
			$where .=" and ( branch_name ilike '%".$iLike."%' or shift_name ilike '%".$iLike."%' or team_name ilike '%".$iLike."%')";	
		}

			$sql = "SELECT
				up.assignment_date,
				PORT.name as pelabuhan,
				-- SC.name as ship_class,
				up.port_id,
				-- bok.ship_class as ship_class_id,
				-- branch.branch_name,
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
				-- SC.name,
				-- bok.ship_class,
				-- bok.branch_code,
				-- branch.branch_name,
				up.assignment_code,
				ar.shift_id,
				shift.shift_name,
				ar.team_code,
				shift.shift_login,
				shift.shift_logout,
				mt.team_name";

		$query          = $this->dbView->query($sql);
		$records_total  = $query->num_rows();

		$sql .=" order by " . $order_column . " {$order_dir}";

		if($length != -1){
			$sql .=" limit {$length} offset {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$shiftTime = date('H:i', strtotime($row->shift_login))." s.d ".date('H:i', strtotime($row->shift_logout));

			$ship_class_id 	= $this->enc->encode($row->ship_class_id);
			$code 			= $this->enc->encode($row->assignment_code);
			$pdf_url 		= 	site_url($this->_module."/download_pdf?shift={$row->shift_id}&port={$row->port_id}&date={$row->assignment_date}&port_name={$row->pelabuhan}&shift_time={$shiftTime}&team_name={$row->team_name}");
			$excel_url 		= 	site_url($this->_module."/download_excel?shift={$row->shift_id}&port={$row->port_id}&date={$row->assignment_date}&port_name={$row->pelabuhan}&shift_time={$shiftTime}&team_name={$row->team_name}");

     		$row->actions 	= "";
			$row->actions 	.= generate_button_new($this->_module, 'download_pdf', $pdf_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_excel', $excel_url);
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

	public function dataTrip($date, $port, $shift)
	{
		$sql = "SELECT tms.id,tms.name as ship_name,COALESCE(trip.qty,0) as qty FROM app.t_mtr_ship tms 
				LEFT JOIN(
					SELECT
						DISTINCT ON (subtrip.ship_id)
						subtrip.ship_id,
						COUNT ( boarding_code ) AS qty 
					FROM
						(
					SELECT DISTINCT ON
						( ttob.boarding_code ) ttob.ship_id,
						ttob.boarding_code 
					FROM
						app.t_trx_open_boarding ttob
						JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code
						JOIN app.t_trx_boarding_passanger ttbp ON ttbp.boarding_code = ttob.boarding_code
						JOIN app.t_trx_booking_passanger ttbop ON ttbop.ticket_number = ttbp.ticket_number
						JOIN app.t_trx_booking ttb ON ttb.booking_code = ttbop.booking_code
						JOIN app.t_trx_sell tts ON tts.trans_number = ttb.trans_number
						JOIN app.t_trx_opening_balance ttoba ON ttoba.ob_code = tts.ob_code
						JOIN app.t_trx_assignment_user_pos ttaup ON ttaup.assignment_code = ttoba.assignment_code 
					WHERE
						ttob.port_id = $port
						AND ttsc.status = 1 
						AND ttoba.shift_id = $shift
						AND ttaup.assignment_date = '$date' 
					GROUP BY
						ttob.ship_id,
						ttob.boarding_code
						) AS subtrip 
					GROUP BY
						subtrip.ship_id
					) trip ON trip.ship_id = tms.id";

		return $this->dbView->query($sql)->result();
	}

	public function old_get_all_data($date, $port, $shift)
	{
		$sql = "SELECT UPPER(name) ship_name, count trip, dock FROM (SELECT
					ship_id,
					json_object_agg ( dock_id, total ORDER BY dock_id ) AS dock 
				FROM
					(
				SELECT 
					A.id  ship_id,
					COALESCE ( dock_id, 0 ) dock_id,
					COALESCE ( trip, 0 ) :: VARCHAR || '-' || COALESCE ( trip * tambat_fare, 0 ) :: VARCHAR total 
				FROM
					app.t_mtr_ship
					A LEFT JOIN (
				SELECT
					b.ID,
					d.ID dock_id,
					COUNT ( 0 ) trip,
					C.tambat_fare 
				FROM
					app.t_trx_open_boarding
					A JOIN app.t_mtr_ship b ON b.ID = A.ship_id
					JOIN app.t_trx_schedule C ON C.schedule_code = A.schedule_code 
					AND C.status = 1
					JOIN app.t_mtr_dock d ON d.ID = A.dock_id 
				WHERE
					A.shift_id = $shift 
					AND A.shift_date = '$date' 
					AND A.port_id = $port
					AND A.status = 0 
				GROUP BY
					b.ID,
					d.ID,
					CALL,
					C.tambat_fare 
					) b ON b.ID = A.ID 
				ORDER BY
					A.NAME 
					) A 
				GROUP BY
					ship_id) dock
				LEFT JOIN 
				(SELECT
				tms.name,
				tms.id,
				COALESCE(count,0) count
			FROM
				app.t_mtr_ship tms
				LEFT JOIN (SELECT ttob.ship_id,count(0) FROM app.t_trx_open_boarding ttob
				JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code AND ttsc.status = 1 WHERE
				ttob.port_id = $port
				AND ttsc.status = 1 
				AND ttob.shift_id = $shift 
				AND ttob.shift_date = '$date'
				GROUP BY ttob.ship_id) A ON A.ship_id = tms.id) trx ON trx.id = dock.ship_id";
		return $this->dbView->query($sql)->result();
	}

	public function headerku($port_id,$datefrom,$dateto,$ship_class_id,$shift_id)
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

	public function get_shift_time($shift_id,$port_id)
	{
		if ($shift_id != "" && $port_id != "") {
			$sql = $this->dbView->query("SELECT shift_login,shift_logout FROM app.t_mtr_shift_time WHERE shift_id = $shift_id AND port_id = $port_id");

			if ($sql->num_rows() > 0) {
				return $sql->row();
			}else{
				return false;
			}
		}else{
			return false;
		}		
	}

	public function get_all_data($datefrom, $dateto, $port, $ship_class, $shift)
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

		$sql = "SELECT UPPER(company) company, UPPER(name) ship_name, count trip, dock FROM (SELECT
					ship_id,
					json_object_agg ( dock_id, total ORDER BY dock_id ) AS dock 
				FROM
					(
				SELECT 
					A.id  ship_id,
					COALESCE ( dock_id, 0 ) dock_id,
					COALESCE ( trip, 0 ) :: VARCHAR || '-' || COALESCE ( trip * tambat_fare, 0 ) :: VARCHAR total 
				FROM
					app.t_mtr_ship
					A LEFT JOIN (
				SELECT
					b.ID,
					d.ID dock_id,
					COUNT ( 0 ) trip,
					C.tambat_fare 
				FROM
					app.t_trx_open_boarding
					A JOIN app.t_mtr_ship b ON b.ID = A.ship_id
					JOIN app.t_trx_schedule C ON C.schedule_code = A.schedule_code 
					AND C.status = 1
					JOIN app.t_mtr_dock d ON d.ID = A.dock_id 
				WHERE
					A.shift_date BETWEEN '$datefrom' AND '$dateto'
					AND b.status NOT IN(-5)
					AND A.status = 0 
					$where_shift
					$where_port
				GROUP BY
					b.ID,
					d.ID,
					CALL,
					C.tambat_fare 
					) b ON b.ID = A.ID 
					WHERE A.status NOT IN(-5)
				ORDER BY
					A.NAME 
					) A 
				GROUP BY
					ship_id) dock
				LEFT JOIN 
				(SELECT
				shc.name as company,
				tms.name,
				tms.id,
				COALESCE(count,0) count
			FROM
				app.t_mtr_ship tms
				LEFT JOIN app.t_mtr_ship_company shc ON shc.ID = tms.ship_company_id
				LEFT JOIN (SELECT ttob.ship_id,count(0) FROM app.t_trx_open_boarding ttob
				JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code AND ttsc.status = 1
				WHERE ttsc.status = 1 
				$where_port_sub
				$where_shift_sub
				AND ttob.shift_date BETWEEN '$datefrom' AND '$dateto'
				GROUP BY ttob.ship_id) A ON A.ship_id = tms.id
				WHERE tms.status NOT IN(-5) 
			) trx ON trx.id = dock.ship_id ORDER BY company ASC";
		return $this->dbView->query($sql)->result();
	}

	public function dataTambat($date, $port, $shift)
	{
		$sql = "SELECT
					subtrip.ship_id,
					subtrip.dock_id,
					subtrip.tambat_fare
				FROM
					(
				SELECT DISTINCT
					( ttob.boarding_code ),
					ttob.ship_id,
					ttob.dock_id,
					ttsc.tambat_fare
				FROM
					app.t_trx_open_boarding ttob
					JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code
					JOIN app.t_trx_boarding_passanger ttbp ON ttbp.boarding_code = ttob.boarding_code
					JOIN app.t_trx_booking_passanger ttbop ON ttbop.ticket_number = ttbp.ticket_number
					JOIN app.t_trx_booking ttb ON ttb.booking_code = ttbop.booking_code
					JOIN app.t_trx_sell tts ON tts.trans_number = ttb.trans_number
					JOIN app.t_trx_opening_balance ttoba ON ttoba.ob_code = tts.ob_code
					JOIN app.t_trx_assignment_user_pos ttaup ON ttaup.assignment_code = ttoba.assignment_code 
				WHERE
					ttob.port_id = $port 
					AND ttsc.status = 1 
					AND ttoba.shift_id = $shift
					AND ttaup.assignment_date = '$date' 
				GROUP BY
					ttob.ship_id,
					ttob.dock_id,
					ttob.boarding_code,
					ttsc.tambat_fare
					) AS subtrip 
				GROUP BY
					subtrip.ship_id,
					subtrip.dock_id,
					subtrip.tambat_fare";

		return $this->dbView->query($sql)->result();
	}

	public function detail_pdf ($date, $port, $shift) {

		$sql = "
			SELECT 
				tms.id,
				tms.name as ship_name,
				trip.dock_id,
				tambat.tambat_fare,
				COALESCE(trip.qty, 0) AS qty
			FROM app.t_mtr_ship tms
			LEFT JOIN (
				SELECT 
				tms.id,
				tms.name as ship_name,
				tambat.dock_id,
				COALESCE(tambat.tambat_fare,0) AS tambat_fare
			FROM app.t_mtr_ship tms 
			LEFT JOIN (
				SELECT 
					distinct on (ttob.boarding_code)
					ttob.ship_id,
					ttob.dock_id,
					(ttsc.tambat_fare) as tambat_fare
				FROM app.t_trx_open_boarding ttob
				JOIN app.t_trx_boarding_passanger ttbp ON ttbp.boarding_code = ttob.boarding_code
				JOIN app.t_trx_booking_passanger ttbop ON ttbop.ticket_number = ttbp.ticket_number
				JOIN app.t_trx_booking ttb ON ttb.booking_code = ttbop.booking_code
				JOIN app.t_trx_sell tts ON tts.trans_number = ttb.trans_number
				JOIN app.t_trx_opening_balance ttoba ON ttoba.ob_code = tts.ob_code
				JOIN app.t_trx_assignment_user_pos ttaup ON ttaup.assignment_code = ttoba.assignment_code
				JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code
				WHERE ttob.port_id = {$port} AND ttsc.status = 1
				AND ttoba.shift_id = {$shift}
				AND ttaup.assignment_date = '{$date}'
			) AS tambat ON tambat.ship_id = tms.id
			ORDER BY tms.name ASC
			) as tambat on tambat.id = tms.id
			
			LEFT JOIN (
				SELECT 
					subtrip.ship_id,
					subtrip.dock_id,
					subtrip.tambat_fare,
					count(boarding_code) as qty
				FROM (
					SELECT 
						distinct on (ttob.boarding_code)
						ttob.ship_id,
						ttsc.tambat_fare,
						ttob.dock_id,
						ttob.boarding_code
					FROM app.t_trx_open_boarding ttob
					JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code
					JOIN app.t_trx_boarding_passanger ttbp ON ttbp.boarding_code = ttob.boarding_code
					JOIN app.t_trx_booking_passanger ttbop ON ttbop.ticket_number = ttbp.ticket_number
					JOIN app.t_trx_booking ttb ON ttb.booking_code = ttbop.booking_code
					JOIN app.t_trx_sell tts ON tts.trans_number = ttb.trans_number
					JOIN app.t_trx_opening_balance ttoba ON ttoba.ob_code = tts.ob_code
					JOIN app.t_trx_assignment_user_pos ttaup ON ttaup.assignment_code = ttoba.assignment_code
					WHERE ttob.port_id = {$port} AND ttsc.status = 1
					AND ttoba.shift_id = {$shift}
					AND ttaup.assignment_date = '{$date}'
					GROUP BY 
						ttob.ship_id,
						ttob.boarding_code,
						ttsc.tambat_fare,
						ttob.dock_id
				) AS subtrip
				GROUP BY
					subtrip.ship_id,
					subtrip.tambat_fare,
					subtrip.dock_id
			) AS trip ON trip.ship_id = tms.id
			WHERE tms.status = 1
			ORDER BY 
				tms.name ASC;
		";

		$result = $this->dbView->query($sql);
		// die($sql);exit;
				
		return $result->result();
	}

	public function select_data($table, $where="")
	{
		return $this->dbView->query("select * from $table $where");
	}

}