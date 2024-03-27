<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penjualan_vm_model extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'transaction/booking';
	}

	public function getvm($port_id = "")
	{
		if ($port_id == "") {
			$port_id = $this->session->userdata('port_id');
		}

		if ($port_id == "") {
			$identity_app = $this->dbView->query("SELECT port_id FROM app.t_mtr_identity_app")->row();
			$port_id = $identity_app->port_id;

			if ($port_id == 0) {
				$where_port = "";
			} else {
				$where_port = "AND port_id = $port_id";
			}
		} else {
			$where_port = " AND port_id = $port_id";
		}

		return $this->dbView->query("SELECT
										port_id,
										terminal_name,
										terminal_code
									FROM
										app.t_mtr_device_terminal
									WHERE
										terminal_type = 3
										AND status = 1
										$where_port")->result();
	}

	public function get_team($port_id)
	{
		return $this->dbView->query("SELECT team_code,team_name FROM core.t_mtr_team WHERE port_id = $port_id ORDER BY team_name ASC")->result();
	}

	public function listPenumpang()
	{
		$start 				= $this->input->post('start');
		$length 			= $this->input->post('length');
		$draw 				= $this->input->post('draw');
		$search 			= $this->input->post('search');
		$order 				= $this->input->post('order');
		$order_column 		= $order[0]['column'];
		$order_dir 			= strtoupper($order[0]['dir']);
		$shift 				= $this->enc->decode($this->input->post('shift'));
		$regu 				= $this->enc->decode($this->input->post('regu'));
		$port 				= $this->enc->decode($this->input->post('port'));
		$vm 				= $this->enc->decode($this->input->post('vm'));
		$dateFrom 			= trim($this->input->post('dateFrom'));
		$dateTo 			= trim($this->input->post('dateTo'));
		$iLike 				= trim(strtoupper($this->dbView->escape_like_str($search['value'])));
		$session_shift_class = $this->session->userdata('ship_class_id');

		$field = array(
			0 => 'S.id',
			1 => 'terminal_name',
			2 => 'booking_code',
			3 => 'ticket_number',
			4 => 'golongan',
			5 => 'tarif',
			6 => 'payment_type',
			7 => 'kelas',
			8 => 'trans_date',
			9 => 'shift',
			10 => 'regu',
			11 => 'customer_name',
			12 => 'id_number',
			13 => 'ship',
			14 => 'naik_kapal',
		);

		$order_column = $field[$order_column];

		$where = " WHERE S.status = 1";

		if ($vm != "") {
			$where .= " AND OB.terminal_code = '$vm'";
		}

		if (!empty($shift)) {
			$where .= " and (OB.shift_id=" . $shift . ")";
		}

		if (!empty($port)) {
			$where .= " and (I.port_id=" . $port . ")";
		}

		if (!empty($regu)) {
			$where .= " and (AR.team_code='" . $regu . "')";
		}

		if (!empty($dateTo) and !empty($dateFrom)) {
			$where .= " and (date(OB.trx_date) between '" . $dateFrom . "' and '" . $dateTo . "' )";
		} else if (empty($dateFrom) and !empty($dateTo)) {
			$where .= " and (date(OB.trx_date) between '" . $dateTo . "' and '" . $dateTo . "' )";
		} else if (!empty($dateFrom) and empty($dateTo)) {
			$where .= " and (date(OB.trx_date) between '" . $dateFrom . "' and '" . $dateFrom . "' )";
		} else {
			$where .= " and (date(OB.trx_date) between '" . date("Y-m-d") . "' and '" . date('Y-m-d', strtotime("-7 days")) . "' )";
		}

		if (!empty($search['value'])) {
			$where .= " and (BO.booking_code ilike '%" . $iLike . "%')";
		}

		if (!empty($session_shift_class)) {
			$where .= " and BO.ship_class = {$session_shift_class}";
		}

		$sql = "SELECT DISTINCT
					S.ID,
					BV.ticket_number,
					DT.terminal_name,
					BO.booking_code AS booking_code,
					VC.NAME AS golongan,
					BV.fare AS tarif,
					S.payment_type AS payment_type,
					SC.NAME AS kelas,
					OB.trx_date AS trans_date,
					SF.shift_name AS shift,
					T.team_name AS regu,
					BV.id_number AS id_number,
					BV.NAME AS customer_name,
					K.NAME AS ship,
					brv.created_on :: TIMESTAMP ( 0 ) AS naik_kapal
				FROM
					app.t_trx_sell_vm S
					JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code
					JOIN app.t_mtr_shift SF ON SF.ID = OB.shift_id
					JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = S.terminal_code AND DT.status = 1
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
					JOIN app.t_mtr_ship_class SC ON SC.ID = BO.ship_class
					JOIN app.t_trx_booking_passanger BV ON BV.booking_code = BO.booking_code AND BV.service_id = 1
					JOIN app.t_mtr_passanger_type VC ON VC.ID = BV.passanger_type_id AND VC.status = 1
					LEFT JOIN app.t_trx_invoice I ON I.trans_number = BO.trans_number
					LEFT JOIN app.t_trx_boarding_passanger brv ON brv.ticket_number = BV.ticket_number AND brv.status = 1
					LEFT JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BRV.boarding_code
					LEFT JOIN app.t_mtr_ship K ON K.ID = OBO.ship_id
					JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
						{$where}";

		$sql2 = "SELECT
					SUM(BV.fare) as total
				FROM
					app.t_trx_sell_vm S
					JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code
					JOIN app.t_mtr_shift SF ON SF.id = OB.shift_id
					JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = S.terminal_code AND DT.status = 1
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
					JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class
					JOIN app.t_trx_booking_passanger BV ON BV.booking_code = BO.booking_code AND BV.service_id = 1
					JOIN app.t_mtr_passanger_type VC ON VC.id = BV.passanger_type_id AND VC.status=1
					LEFT JOIN app.t_trx_invoice I ON I.trans_number = BO.trans_number
					LEFT JOIN app.t_trx_boarding_passanger brv ON brv.ticket_number = BV.ticket_number AND brv.status = 1
					LEFT JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BRV.boarding_code
					LEFT JOIN app.t_mtr_ship K ON K.id = OBO.ship_id
					JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
					{$where}";

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY " . $order_column . " {$order_dir}";
		if ($length != -1) {
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		$total_amount = $this->dbView->query($sql2)->row()->total;

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->trans_date = format_date($row->trans_date);
			$row->tarif = idr_currency($row->tarif);

			$url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
			$row->ticket_number = '<a href="' . $url . '" target="_blank">' . $row->ticket_number . '</a>';

			if ($row->id_number != 0) {
				$row->id_number = $row->id_number;
			} else {
				$row->id_number = '-';
			}
			if ($row->ship) {
				$row->ship = $row->ship;
			} else {
				$row->ship = '-';
			}
			if ($row->naik_kapal != null) {
				$row->naik_kapal = format_dateTime($row->naik_kapal);
			} else {
				$row->naik_kapal = '-';
			}

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'data'           => $rows,
			'total' 		 => $total_amount ? idr_currency($total_amount) : 0
		);
	}

	public function download()
	{
		$shift 					= $this->enc->decode($this->input->get('shift'));
		$regu 					= $this->enc->decode($this->input->get('regu'));
		$port 					= $this->enc->decode($this->input->get('port'));
		$vm 					= $this->enc->decode($this->input->get('vm'));
		$dateFrom 				= trim($this->input->get('dateFrom'));
		$dateTo 				= trim($this->input->get('dateTo'));
		$search					= $this->input->get("search");
		$iLike 					= trim($this->dbView->escape_like_str($search));
		$session_shift_class    = $this->session->userdata('ship_class_id');

		$where = " WHERE S.status = 1";

		if ($vm != "") {
			$where .= " AND OB.terminal_code = '$vm'";
		}

		if (!empty($shift)) {
			$where .= " and (OB.shift_id=" . $shift . ")";
		}

		if (!empty($port)) {
			$where .= " and (I.port_id=" . $port . ")";
		}

		if (!empty($regu)) {
			$where .= " and (AR.team_code='" . $regu . "')";
		}

		if (!empty($dateTo) and !empty($dateFrom)) {
			$where .= " and (date(OB.trx_date) between '" . $dateFrom . "' and '" . $dateTo . "' )";
		} else if (empty($dateFrom) and !empty($dateTo)) {
			$where .= " and (date(OB.trx_date) between '" . $dateFrom . "' and '" . $dateTo . "' )";
		} else if (!empty($dateFrom) and empty($dateTo)) {
			$where .= " and (date(OB.trx_date) between '" . $dateFrom . "' and '" . $dateFrom . "' )";
		} else {
			$where .= " and (date(OB.trx_date) between '" . date("Y-m-d") . "' and '" . date('Y-m-d', strtotime("-7 days")) . "' )";
		}

		if (!empty($search['value'])) {
			$where .= " and (BO.booking_code ilike '%" . $iLike . "%')";
		}

		if (!empty($session_shift_class)) {
			$where .= " and BO.ship_class = {$session_shift_class}";
		}

		$sql = "SELECT DISTINCT
					S.ID,
					BV.ticket_number,
					DT.terminal_name,
					BO.booking_code AS booking_code,
					VC.NAME AS golongan,
					BV.fare AS tarif,
					S.payment_type AS payment_type,
					SC.NAME AS kelas,
					OB.trx_date AS trans_date,
					SF.shift_name AS shift,
					T.team_name AS regu,
					BV.id_number AS id_number,
					BV.NAME AS customer_name,
					K.NAME AS ship,
					brv.created_on :: TIMESTAMP ( 0 ) AS naik_kapal
				FROM
					app.t_trx_sell_vm S
					JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code
					JOIN app.t_mtr_shift SF ON SF.ID = OB.shift_id
					JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = S.terminal_code AND DT.status = 1
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
					JOIN app.t_mtr_ship_class SC ON SC.ID = BO.ship_class
					JOIN app.t_trx_booking_passanger BV ON BV.booking_code = BO.booking_code AND BV.service_id = 1
					JOIN app.t_mtr_passanger_type VC ON VC.ID = BV.passanger_type_id AND VC.status = 1
					LEFT JOIN app.t_trx_invoice I ON I.trans_number = BO.trans_number
					LEFT JOIN app.t_trx_boarding_passanger brv ON brv.ticket_number = BV.ticket_number AND brv.status = 1
					LEFT JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BRV.boarding_code
					LEFT JOIN app.t_mtr_ship K ON K.ID = OBO.ship_id
					JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
						{$where}";

		$sql2 = "SELECT
					SUM(BV.fare) as total
				FROM app.t_trx_sell_vm S
					JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code
					JOIN app.t_mtr_shift SF ON SF.id = OB.shift_id
					JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = S.terminal_code AND DT.status = 1
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
					JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class
					JOIN app.t_trx_booking_passanger BV ON BV.booking_code = BO.booking_code AND BV.service_id = 1
					JOIN app.t_mtr_passanger_type VC ON VC.id = BV.passanger_type_id AND VC.status=1
					LEFT JOIN app.t_trx_invoice I ON I.trans_number = BO.trans_number
					LEFT JOIN app.t_trx_boarding_passanger brv ON brv.ticket_number = BV.ticket_number AND brv.status = 1
					LEFT JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BRV.boarding_code
					LEFT JOIN app.t_mtr_ship K ON K.id = OBO.ship_id
					JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
					{$where}";

		$query = $this->dbView->query($sql);
		return $query;
	}

	public function get_identity_app()
	{
		$data = $this->dbView->query("select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}
}
