<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pemeliharaan_dermaga_model extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'laporan/pemeliharaan_dermaga';
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
		$port = $this->enc->decode($this->input->post('port'));
		$shift = $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

		$field = array(
			1 => 'shift_date',
			2 => 'port_name',
			3 => 'class_name',
			4 => 'shift_name',
			5 => 'team_name'
		);

		$order_column = $field[$order_column];
		$where = " WHERE bor.shift_date IS NOT NULL";

		if ((!empty($dateFrom) and empty($dateTo)) || (empty($dateFrom) and !empty($dateTo))) {
			$where .= " and bor.shift_date ='$dateFrom' or bor.shift_date ='$dateTo'";
		}

		if (!empty($dateFrom) and !empty($dateTo)) {
			$where .= " and bor.shift_date between '$dateFrom' and  '$dateTo'";
		}

		if (!empty($port)) {
			$where .= " and (bor.port_id = " . $port . ")";
		}

		if (!empty($shift)) {
			$where .= " and (ar.shift_id = " . $shift . ")";
		}

		if (!empty($search['value'])) {
			$where .= " and ( port_name ilike '%" . $iLike . "%' or shift_name ilike '%" . $iLike . "%' or team_name ilike '%" . $iLike . "%')";
		}

		$sql = "SELECT DISTINCT
					shift_date,
					bor.shift_id,
					bor.ship_class,
					bor.port_id,
					sh.shift_name,
					sc.name class_name,
					port.name as port_name,
					T.team_name
				FROM
					app.t_trx_open_boarding bor
					JOIN app.t_trx_assignment_regu ar ON ar.assignment_date = bor.shift_date AND ar.shift_id = bor.shift_id AND ar.status = 2
					JOIN app.t_mtr_shift sh ON sh.id = bor.shift_id
					JOIN app.t_mtr_ship_class sc ON sc.id = bor.ship_class
					JOIN app.t_mtr_port port ON port.id = bor.port_id
					JOIN core.t_mtr_team T ON T.team_code = ar.team_code
				$where";

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

			$shift_date	= $this->enc->encode($row->shift_date);
			$ship_class_id 	= $this->enc->encode($row->ship_class);
			$shift_id 	= $this->enc->encode($row->shift_id);

			$detail_url 	= site_url($this->_module . "/new_detail/{$shift_date}/{$ship_class_id}/{$shift_id}");
			$pdf_url 		= site_url($this->_module . "/download_pdf/{$shift_date}/{$ship_class_id}/{$shift_id}");
			$excel_url 		= site_url($this->_module . "/download_excel/{$shift_date}/{$ship_class_id}/{$shift_id}");

			$row->actions 	= "";
			$row->actions 	.= generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_pdf', $pdf_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_excel', $excel_url);
			$row->date = format_date($row->shift_date);
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

	public function old_penumpangku($shift_date, $ship_class_id, $shift_id)
	{
		return $this->dbView->query("SELECT
					DISTINCT(PT.id),
					bok.dock_fee as harga,
					PT.name as golongan,
					COUNT(DISTINCT(bok.id)) as produksi,
					SUM(bok.dock_fee) as dock_fee
				FROM
					app.t_trx_open_boarding bor
					JOIN app.t_trx_boarding_passanger bop ON bop.boarding_code = bor.boarding_code AND bop.status = 1
					JOIN app.t_trx_booking_passanger bok ON bok.ticket_number = bop.ticket_number AND bok.passanger_type_id IN (1,2) --AND bok.status = 5 
					JOIN app.t_trx_booking bok2 ON bok2.booking_code = bok.booking_code AND bok2.service_id = 1
					JOIN app.t_mtr_passanger_type PT ON PT.id = bok.passanger_type_id
				WHERE
					shift_date = '$shift_date' 
					AND bop.ship_class = $ship_class_id
					AND shift_id = $shift_id
				GROUP BY
					PT.id,
					PT.name,
					bok.dock_fee
				ORDER BY
					PT.id ASC")->result();
	}

	public function penumpangku($port_id, $datefrom, $dateto, $ship_class_id, $shift_id, $dock_id, $ticketType)
	{
		$where_port = "";
		$where_ship_class = "";
		$where_shift = "";
		$where_dock = "";
		$whereManual = " ";

		if(!empty($ticketType)) // tipe tiket 3 adalah tiket manual
		{
			if($ticketType==3)
			{

				$whereManual = " and bok.ticket_type='3' ";
			}
			else
			{
				$whereManual = " and bok.ticket_type !='3' ";
			}
		}		

		if ($port_id != "") {
			$where_port = " AND bor.port_id = $port_id";
		}

		if ($ship_class_id != "") {
			$where_ship_class = " AND bor.ship_class = $ship_class_id";
		}

		if ($shift_id != "") {
			$where_shift = " AND shift_id = $shift_id";
		}

		if ($dock_id != "") {
			$where_dock = " AND bor.dock_id = $dock_id";
		}

		$sql = "SELECT
					PTT.name as golongan,
					SUB.*
				FROM
					app.t_mtr_passanger_type PTT
					LEFT JOIN (SELECT
								DISTINCT(PT.id),
								bok.dock_fee as harga,
								COUNT(DISTINCT(bok.id)) as produksi,
								SUM(bok.dock_fee) as dock_fee
							FROM
								app.t_trx_open_boarding bor
								JOIN app.t_trx_boarding_passanger bop ON bop.boarding_code = bor.boarding_code AND bop.status = 1
								JOIN app.t_trx_booking_passanger bok ON bok.ticket_number = bop.ticket_number
								JOIN app.t_trx_booking bok2 ON bok2.booking_code = bok.booking_code AND bok2.service_id = 1
								JOIN app.t_mtr_passanger_type PT ON PT.id = bok.passanger_type_id
							WHERE
								shift_date BETWEEN '$datefrom' AND '$dateto'
								AND bop.status = 1
								$where_dock
								$where_port
								$where_ship_class
								$where_shift
								$whereManual
							GROUP BY
								PT.id,
								PT.name,
								bok.dock_fee
							ORDER BY
								PT.id ASC) SUB ON SUB.id = PTT.id
				WHERE PTT.id IN(1,2,3,4)
				ORDER BY PTT.ordering ASC
				-- ORDER BY PTT.id ASC
				
				";
		// die($sql); exit;
		return $this->dbView->query($sql)->result();
	}

	public function get_dock($port_id)
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_dock WHERE port_id = $port_id AND status = 1 ORDER BY name ASC")->result();
	}

	public function old_kendaraanku($shift_date, $ship_class_id, $shift_id)
	{
		return $this->dbView->query("SELECT
							DISTINCT(VC.id),
							bv.dock_fee as harga,
							VC.name as golongan,
							COUNT(VC.id) as produksi,
							SUM(bv.dock_fee) as dock_fee
						FROM
							app.t_trx_open_boarding bor
							JOIN app.t_trx_boarding_vehicle bov ON bov.boarding_code = bor.boarding_code AND bov.status = 1
							JOIN app.t_trx_booking_vehicle bv ON bv.ticket_number = bov.ticket_number -- AND bv.status = 5
							JOIN app.t_mtr_vehicle_class VC ON VC.id = bv.vehicle_class_id
							JOIN app.t_trx_booking bok2 ON bok2.booking_code = bv.booking_code
						WHERE
							shift_date = '$shift_date' 
							AND bov.ship_class = $ship_class_id
							AND shift_id = $shift_id
						GROUP BY
							VC.id,
							bv.dock_fee,
							VC.name
						ORDER BY
							VC.id ASC")->result();
	}

	public function kendaraanku($port_id, $datefrom, $dateto, $ship_class_id, $shift_id, $dock_id, $ticketType)
	{
		$where_port = "";
		$where_ship_class = "";
		$where_shift = "";
		$where_dock = "";
		$whereManual = " ";

		if(!empty($ticketType)) // tipe tiket 3 adalah tiket manual
		{
			if($ticketType==3)
			{
				$whereManual = " and bv.ticket_type='3' ";
			}
			else
			{
				$whereManual = " and bv.ticket_type !='3' ";
			}
		}

		if ($port_id != "") {
			$where_port = " AND bor.port_id = $port_id";
		}

		if ($ship_class_id != "") {
			$where_ship_class = " AND bor.ship_class = $ship_class_id";
		}

		if ($shift_id != "") {
			$where_shift = " AND shift_id = $shift_id";
		}

		if ($dock_id != "") {
			$where_dock = " AND bor.dock_id = $dock_id";
		}

		$sql = "SELECT
					VCC.name as golongan,
					SUB.*
				FROM
					app.t_mtr_vehicle_class VCC
					LEFT JOIN (SELECT
									DISTINCT(VC.id),
									bv.dock_fee as harga,
									COUNT(VC.id) as produksi,
									SUM(bv.dock_fee) as dock_fee
								FROM
									app.t_trx_open_boarding bor
									JOIN app.t_trx_boarding_vehicle bov ON bov.boarding_code = bor.boarding_code AND bov.status = 1
									JOIN app.t_trx_booking_vehicle bv ON bv.ticket_number = bov.ticket_number -- AND bv.status = 5
									JOIN app.t_mtr_vehicle_class VC ON VC.id = bv.vehicle_class_id
									JOIN app.t_trx_booking bok2 ON bok2.booking_code = bv.booking_code
								WHERE
									shift_date BETWEEN '$datefrom' AND '$dateto'
									-- AND dock_fee != 0
									AND bov.status = 1
									$where_dock
									$where_port
									$where_ship_class
									$where_shift
									$whereManual
								GROUP BY
									VC.id,
									bv.dock_fee,
									VC.name
								ORDER BY
									VC.id ASC) SUB ON SUB.id = VCC.id
				WHERE VCC.status = 1 ORDER BY VCC.id ASC";

		return $this->dbView->query($sql)->result();
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

	public function old_headerku($shift_date, $ship_class_id, $shift_id)
	{
		return $this->dbView->query("SELECT
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
									JOIN app.t_trx_assignment_regu ar ON ar.assignment_date = '$shift_date' AND ar.shift_id = $shift_id
									JOIN core.t_mtr_team t ON t.team_code = ar.team_code
									JOIN app.t_mtr_branch br ON br.branch_code = bok.branch_code
									JOIN app.t_mtr_shift sh ON sh.id = bor.shift_id
									JOIN app.t_mtr_port port ON port.id = bor.port_id
								WHERE
									shift_date = '$shift_date'
									AND bov.ship_class = $ship_class_id
									AND bor.shift_id = $shift_id")->row();
	}

	public function select_data($table, $where = "")
	{
		return $this->dbView->query("select * from $table $where");
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