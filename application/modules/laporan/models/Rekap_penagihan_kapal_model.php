<?php
defined('BASEPATH') or exit('No direct script access allowed');
ini_set("memory_limit", "512M");
class Rekap_penagihan_kapal_model extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'laporan/rekap_penagihan_kapal';
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

		$field = array(
			0 => 'assignment_date',
			1 => 'pelabuhan',
			2 => 'shift_name',
			3 => 'team_name'
		);

		$order_column = $field[$order_column];
		$where = " where ar.status = 2 ";

		if ((!empty($dateFrom) and empty($dateTo)) || (empty($dateFrom) and !empty($dateTo))) {
			$where .= " and assignment_date ='$dateFrom' or assignment_date ='$dateTo'";
		}

		if (!empty($dateFrom) and !empty($dateTo)) {
			$where .= " and assignment_date between '$dateFrom' and  '$dateTo'";
		}

		if (!empty($port)) {
			$where .= " and (ob.port_id = '" . $port . "')";
		}

		if (!empty($search['value'])) {
			$where .= " and ( p.name ilike '%" . $iLike . "%' or shift_name ilike '%" . $iLike . "%' )";
		}

		$sql = "SELECT DISTINCT
					assignment_date,
					p.name AS pelabuhan,
					ob.port_id,
					shift_name,
					shift_login,
					shift_logout,
					ob.shift_id,
					team_name,
					coalesce(users.first_name, '')||' '||coalesce(users.last_name, '') as spv
				FROM
					app.t_trx_open_boarding ob
				JOIN app.t_trx_assignment_regu ar ON ar.shift_id = ob.shift_id AND ar.assignment_date = ob.shift_date AND ar.status = 2
				JOIN app.t_mtr_port p ON p.id = ob.port_id
				JOIN app.t_mtr_shift s ON s.id = ob.shift_id		
				JOIN core.t_mtr_team t ON t.team_code = ar.team_code
				JOIN app.t_trx_schedule ss ON ss.schedule_code = ob.schedule_code
				JOIN app.t_mtr_port port_origin ON ss.port_id = port_origin.id
				JOIN app.t_mtr_port port_destination ON ss.destination_port_id = port_destination.id
				JOIN core.t_mtr_user users on ar.supervisor_id = users.id
				{$where}";

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
			$detail_url 	= site_url($this->_module . "/detail?shift={$row->shift_id}&port={$row->port_id}&date={$row->assignment_date}&port_name={$row->pelabuhan}&shift_time={$shiftTime}&shift_name={$row->shift_name}&spv={$row->spv}");
			$pdf_url 		= 	site_url($this->_module . "/download_pdf?shift={$row->shift_id}&port={$row->port_id}&date={$row->assignment_date}&port_name={$row->pelabuhan}&shift_time={$shiftTime}&shift_name={$row->shift_name}&spv={$row->spv}");
			$excel_url 		= 	site_url($this->_module . "/download_excel?shift={$row->shift_id}&port={$row->port_id}&date={$row->assignment_date}&port_name={$row->pelabuhan}&shift_time={$shiftTime}&shift_name={$row->shift_name}&spv={$row->spv}");

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

	public function detail_pdf($date, $port, $shift)
	{

		$sql = "
			SELECT 
				tms.id,
				tms.name as ship_name,
				COALESCE(trip.qty, 0) AS qty,
				COALESCE(penumpang.total, 0) as penumpang,
				COALESCE(vehicle.total, 0) as vehicle
			FROM app.t_mtr_ship tms 
			LEFT JOIN (
				SELECT 
					subtrip.ship_id,
					count(boarding_code) as qty
				FROM (
					SELECT 
						distinct on (ttob.boarding_code)
						ttob.ship_id,
						ttob.boarding_code
					FROM app.t_trx_open_boarding ttob
					JOIN app.t_trx_boarding_passanger ttbp ON ttbp.boarding_code = ttob.boarding_code AND ttbp.status = 1
					JOIN app.t_trx_booking_passanger ttbop ON ttbop.ticket_number = ttbp.ticket_number
					WHERE ttob.port_id = {$port}
					AND ttob.shift_id = {$shift}
					AND ttob.shift_date = '{$date}'
					GROUP BY 
						ttob.ship_id,
						ttob.boarding_code
				) AS subtrip
				GROUP BY
					subtrip.ship_id
			) AS trip ON trip.ship_id = tms.id
			LEFT JOIN (
				SELECT 
					subpenumpang.ship_id,
					sum(subpenumpang.trip_fee) as total
				FROM (
					SELECT 
						DISTINCT ON (ttbop.ticket_number)
						ttbop.ticket_number,
						ttob.ship_id,
						ttbop.trip_fee
					FROM app.t_trx_open_boarding ttob
					JOIN app.t_trx_boarding_passanger ttbp ON ttbp.boarding_code = ttob.boarding_code AND ttbp.status = 1
					JOIN app.t_trx_booking_passanger ttbop ON ttbop.ticket_number = ttbp.ticket_number
					WHERE ttob.port_id = {$port}
					AND ttob.shift_id = {$shift}
					AND ttob.shift_date = '{$date}'
				) AS subpenumpang
				GROUP BY
					subpenumpang.ship_id
			) AS penumpang ON penumpang.ship_id = tms.id
			LEFT JOIN (
				SELECT 
					subvehicle.ship_id,
					sum(subvehicle.trip_fee) as total
				FROM (
					SELECT 
						DISTINCT ON (ttbov.ticket_number)
						ttbov.ticket_number,
						ttob.ship_id,
						ttbov.trip_fee
					FROM app.t_trx_open_boarding ttob
					JOIN app.t_trx_boarding_vehicle ttbv ON ttbv.boarding_code = ttob.boarding_code AND ttbv.status = 1
					JOIN app.t_trx_booking_vehicle ttbov ON ttbov.ticket_number = ttbv.ticket_number
					WHERE ttob.port_id = {$port}
					AND ttob.shift_id = {$shift}
					AND ttob.shift_date = '{$date}'
				) AS subvehicle
				GROUP BY
					subvehicle.ship_id
			) AS vehicle ON vehicle.ship_id = tms.id
			ORDER BY 
				tms.name ASC
		";

		$result = $this->dbView->query($sql);

		return $result->result();
	}

	public function detail_pdf_new($datefrom, $dateto, $port, $shift)
	{
		$where_port = "";
		$where_shift = "";
		$where_sc = "";
		$session_shift_class = $this->session->userdata('ship_class_id');

		if ($port != "") {
			$where_port = " AND ttob.port_id = {$port}";
		}

		if ($shift != "") {
			$where_shift = " AND ttob.shift_id = {$shift}";
		}

		if ($session_shift_class != "") {
			$where_sc = " AND ttob.ship_class = {$session_shift_class}";
		}

		$sql = "
			SELECT 
				tms.id,
				shc.name as company,
				tms.name as ship_name,
				COALESCE(trip.qty, 0) AS qty,
				COALESCE(penumpang.total, 0) as penumpang,
				COALESCE(vehicle.total, 0) as vehicle
			FROM app.t_mtr_ship tms
			LEFT JOIN app.t_mtr_ship_company shc ON shc.id = tms.ship_company_id
			LEFT JOIN (
				SELECT 
					subtrip.ship_id,
					count(boarding_code) as qty
				FROM (
					SELECT 
						distinct on (ttob.boarding_code)
						ttob.ship_id,
						ttob.boarding_code
					FROM app.t_trx_open_boarding ttob
					JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code AND ttsc.status = 1
					WHERE ttob.shift_date BETWEEN '{$datefrom}' AND '{$dateto}' AND ttob.status != -5
					$where_port
					$where_shift
					$where_sc
					GROUP BY 
						ttob.ship_id,
						ttob.boarding_code
				) AS subtrip
				GROUP BY
					subtrip.ship_id
			) AS trip ON trip.ship_id = tms.id
			LEFT JOIN (
				SELECT 
					subpenumpang.ship_id,
					sum(subpenumpang.trip_fee) as total
				FROM (
					SELECT 
						DISTINCT ON (ttbop.ticket_number)
						ttbop.ticket_number,
						ttob.ship_id,
						ttbop.trip_fee
					FROM app.t_trx_open_boarding ttob
					JOIN app.t_trx_boarding_passanger ttbp ON ttbp.boarding_code = ttob.boarding_code AND ttbp.status = 1
					JOIN app.t_trx_booking_passanger ttbop ON ttbop.ticket_number = ttbp.ticket_number
					WHERE ttob.shift_date BETWEEN '{$datefrom}' AND '{$dateto}' AND ttob.status != -5
					$where_port
					$where_shift
					$where_sc
				) AS subpenumpang
				GROUP BY
					subpenumpang.ship_id
			) AS penumpang ON penumpang.ship_id = tms.id
			LEFT JOIN (
				SELECT 
					subvehicle.ship_id,
					sum(subvehicle.trip_fee) as total
				FROM (
					SELECT 
						DISTINCT ON (ttbov.ticket_number)
						ttbov.ticket_number,
						ttob.ship_id,
						ttbov.trip_fee
					FROM app.t_trx_open_boarding ttob
					JOIN app.t_trx_boarding_vehicle ttbv ON ttbv.boarding_code = ttob.boarding_code AND ttbv.status = 1
					JOIN app.t_trx_booking_vehicle ttbov ON ttbov.ticket_number = ttbv.ticket_number
					WHERE ttob.shift_date BETWEEN '{$datefrom}' AND '{$dateto}' AND ttob.status != -5
					$where_port
					$where_shift
					$where_sc
				) AS subvehicle
				GROUP BY
					subvehicle.ship_id
			) AS vehicle ON vehicle.ship_id = tms.id
			WHERE tms.status NOT IN(-5)
			ORDER BY 
				shc.name ASC, tms.name ASC";
		$result = $this->dbView->query($sql);

		return $result->result();
	}

	public function get_shift_time($shift_id, $port_id)
	{
		if ($shift_id != "") {
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

	public function get_port($port_id)
	{
		$sql = $this->dbView->query("SELECT name FROM app.t_mtr_port WHERE id = $port_id")->row();
		return $sql->name;
	}

	public function get_shift($shift_id)
	{
		$sql = $this->dbView->query("SELECT shift_name FROM app.t_mtr_shift WHERE id = $shift_id")->row();
		return $sql->shift_name;
	}

	public function select_data($table, $where = "")
	{
		return $this->dbView->query("select * from $table $where");
	}
}
