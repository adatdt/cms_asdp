<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Penjualan_petugas_loket_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function listPenumpang()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$username = $this->enc->decode($this->input->post('username'));
		$shift = $this->enc->decode($this->input->post('shift'));
		$regu = $this->enc->decode($this->input->post('regu'));
		$port = $this->enc->decode($this->input->post('port'));
		$loket = $this->enc->decode($this->input->post('loket'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$dateTo = trim($this->input->post('dateTo'));
		$iLike = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

		$field = array(
			0 => 'S.id',
			1 => 'first_name',
			2 => 'username',
			3 => 'loket',
			4 => 'booking_code',
			5 => 'ticket_number',
			6 => 'golongan',
			7 => 'tarif',
			8 => 'payment_type',
			9 => 'kelas',
			10 => 'trans_date',
			11 => 'shift',
			12 => 'regu',
			13 => 'customer_name',
			14 => 'id_number',
			15 => 'ship',
			16 => 'naik_kapal',
		);

		$order_column = $field[$order_column];

		$where = " WHERE S.status = 1";

		if (!empty($first_name)) {
			$where .= " and (U.first_name='" . $first_name . "')";
		}

		if (!empty($username)) {
			$where .= " and (U.username='" . $username . "')";
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

		if (!empty($loket)) {
			$where .= " and (S.terminal_code='" . $loket . "')";
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

		$session_shift_class = $this->session->userdata('ship_class_id');
		if ($session_shift_class != '') {
			$where .= " and (OBO.ship_class = " . $session_shift_class . ")";
		}

		$sql = "SELECT DISTINCT S.id,
		BV.ticket_number,
		U.first_name AS first_name,
		U.username AS username,
		DT.terminal_name AS loket,
		BO.booking_code AS booking_code,
		VC.name AS golongan,
		BV.fare AS tarif,
		S.payment_type AS payment_type,
		SC.name AS kelas,
		OB.trx_date AS trans_date,
		SF.shift_name AS shift,
		T.team_name AS regu,
		BV.id_number AS id_number,
		BV.name AS customer_name,
		K.name AS ship,
		brv.created_on::TIMESTAMP(0) AS naik_kapal
		FROM app.t_trx_sell S
		JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
		JOIN app.t_mtr_shift SF ON SF.id = OB.shift_id
		JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = S.terminal_code AND DT.status = 1
		JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
		JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class
		JOIN app.t_trx_booking_passanger BV ON BV.booking_code = BO.booking_code AND BV.service_id = 1
		JOIN app.t_mtr_passanger_type VC ON VC.id = BV.passanger_type_id AND VC.status=1
		LEFT JOIN app.t_trx_invoice I ON I.trans_number = BO.trans_number
		JOIN core.t_mtr_user U ON U.id = OB.user_id
		LEFT JOIN app.t_trx_boarding_passanger brv ON brv.ticket_number = BV.ticket_number AND brv.status = 1
		LEFT JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BRV.boarding_code
		LEFT JOIN app.t_mtr_ship K ON K.id = OBO.ship_id
		JOIN app.t_trx_assignment_user_pos AR ON AR.assignment_code = OB.assignment_code
		JOIN core.t_mtr_team T ON T.team_code = AR.team_code
		{$where}";

		$sql2 = "SELECT sum(BV.fare) as total
		FROM app.t_trx_sell S
		JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
		JOIN app.t_mtr_shift SF ON SF.id = OB.shift_id
		JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = S.terminal_code AND DT.status = 1
		JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
		JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class
		JOIN app.t_trx_booking_passanger BV ON BV.booking_code = BO.booking_code AND BV.service_id = 1
		JOIN app.t_mtr_passanger_type VC ON VC.id = BV.passanger_type_id AND VC.status=1
		LEFT JOIN app.t_trx_invoice I ON I.trans_number = BO.trans_number
		JOIN core.t_mtr_user U ON U.id = OB.user_id
		LEFT JOIN app.t_trx_boarding_passanger brv ON brv.ticket_number = BV.ticket_number AND brv.status = 1
		LEFT JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BRV.boarding_code
		LEFT JOIN app.t_mtr_ship K ON K.id = OBO.ship_id
		JOIN app.t_trx_assignment_user_pos AR ON AR.assignment_code = OB.assignment_code
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
			// $row->naik_kapal=format_dateTime($row->naik_kapal);
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

	public function listKendaraan()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$username = $this->enc->decode($this->input->post('username'));
		$shift = $this->enc->decode($this->input->post('shift'));
		$regu = $this->enc->decode($this->input->post('regu'));
		$port = $this->enc->decode($this->input->post('port'));
		$loket = $this->enc->decode($this->input->post('loket'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$dateTo = trim($this->input->post('dateTo'));
		$iLike = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

		$field = array(
			0 => 'S.id',
			1 => 'first_name',
			2 => 'username',
			3 => 'loket',
			4 => 'booking_code',
			5 => 'ticket_number',
			6 => 'golongan',
			7 => 'tarif',
			8 => 'payment_type',
			9 => 'kelas',
			10 => 'trans_date',
			11 => 'shift',
			12 => 'regu',
			13 => 'plat',
			14 => 'customer_name',
			15 => 'id_number',
			16 => 'ship',
			17 => 'naik_kapal',
		);

		$order_column = $field[$order_column];

		$where = " WHERE S.status = 1 AND BP.id IN(SELECT MIN(BP2.id) FROM app.t_trx_booking_passanger BP2 WHERE BP2.booking_code=BP.booking_code)";

		if (!empty($first_name)) {
			$where .= " and (U.first_name='" . $first_name . "')";
		}

		if (!empty($username)) {
			$where .= " and (U.username='" . $username . "')";
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

		if (!empty($loket)) {
			$where .= " and (S.terminal_code='" . $loket . "')";
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

		$session_shift_class = $this->session->userdata('ship_class_id');
		if ($session_shift_class != '') {
			$where .= " and (OBO.ship_class = " . $session_shift_class . ")";
		}

		$sql = "SELECT DISTINCT S.id,
		BV.ticket_number,
		U.first_name AS first_name,
		U.username AS username,
		DT.terminal_name AS loket,
		VC.name AS golongan,
		BV.fare AS tarif,
		S.payment_type AS payment_type,
		SC.name AS kelas,
		OB.trx_date AS trans_date,
		SF.shift_name AS shift,
		T.team_name AS regu,
		BO.booking_code AS booking_code,
		BV.id_number AS plat,
		I.customer_name AS customer_name,
		BP.id_number,
		K.name AS ship,
		brv.created_on::TIMESTAMP(0) AS naik_kapal
		FROM app.t_trx_sell S
		JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
		JOIN app.t_mtr_shift SF ON SF.id = OB.shift_id
		JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = S.terminal_code AND DT.status = 1
		JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
		JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class
		JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code AND BV.service_id = 2
		JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
		JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
		LEFT JOIN app.t_trx_invoice I ON I.trans_number = BO.trans_number
		JOIN core.t_mtr_user U ON U.id = OB.user_id
		LEFT JOIN app.t_trx_boarding_vehicle brv ON brv.ticket_number = BV.ticket_number AND brv.status = 1
		LEFT JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BRV.boarding_code
		LEFT JOIN app.t_mtr_ship K ON K.id = OBO.ship_id
		JOIN app.t_trx_assignment_user_pos AR ON AR.assignment_code = OB.assignment_code
		JOIN core.t_mtr_team T ON T.team_code = AR.team_code
		{$where}";

		$sql2 = "SELECT sum(BV.fare) as total
		FROM app.t_trx_sell S
		JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
		JOIN app.t_mtr_shift SF ON SF.id = OB.shift_id
		JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = S.terminal_code AND DT.status = 1
		JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
		JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class
		JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code AND BV.service_id = 2
		JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
		JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
		LEFT JOIN app.t_trx_invoice I ON I.trans_number = BO.trans_number
		JOIN core.t_mtr_user U ON U.id = OB.user_id
		LEFT JOIN app.t_trx_boarding_vehicle brv ON brv.ticket_number = BV.ticket_number AND brv.status = 1
		LEFT JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BRV.boarding_code
		LEFT JOIN app.t_mtr_ship K ON K.id = OBO.ship_id
		JOIN app.t_trx_assignment_user_pos AR ON AR.assignment_code = OB.assignment_code
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

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->trans_date = format_date($row->trans_date);
			// $row->naik_kapal=format_dateTime($row->naik_kapal);
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

		$total_amount = $this->dbView->query($sql2)->row()->total;

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'data'           => $rows,
			'total' 		 => $total_amount ? idr_currency($total_amount) : 0
		);
	}

	public function select_data($table, $where)
	{
		return $this->dbView->query("select * from $table $where");
	}

	public function get_loket($type)
	{
		$session_shift_class = $this->session->userdata('ship_class_id');
		if ($session_shift_class != '') {
			$where = " and (ship_class = " . $session_shift_class . ")";
		} else {
			$where = "";
		}

		if ($type == '#kendaraan') {
			return $this->dbView->query("SELECT *
								FROM app.t_mtr_device_terminal
								WHERE (terminal_type = 2 OR terminal_type = 12) {$where}
								ORDER BY terminal_name ASC");
		} else {
			return $this->dbView->query("SELECT *
								FROM app.t_mtr_device_terminal
								WHERE terminal_type = 1 {$where}
								ORDER BY terminal_name ASC");
		}
	}

	public function downloadPenumpang()
	{
		$post = $this->input->post();

		$where = " WHERE S.status = 1";

		if (!empty($post['username'])) {
			$where .= " and (U.username='" . $this->enc->decode($post['username']) . "')";
		}

		if (!empty($post['shift'])) {
			$where .= " and (OB.shift_id=" . $this->enc->decode($post['shift']) . ")";
		}

		if (!empty($post['port'])) {
			$where .= " and (I.port_id=" . $this->enc->decode($post['port']) . ")";
		}

		if (!empty($post['regu'])) {
			$where .= " and (AR.team_code='" . $this->enc->decode($post['regu']) . "')";
		}

		if (!empty($post['loket'])) {
			$where .= " and (S.terminal_code='" . $this->enc->decode($post['loket']) . "')";
		}

		if (!empty($post['dateTo']) and !empty($post['dateFrom'])) {
			$where .= " and (date(OB.trx_date) between '" . $post['dateFrom'] . "' and '" . $post['dateTo'] . "' )";
		} else if (empty($post['dateFrom']) and !empty($post['dateTo'])) {
			$where .= " and (date(OB.trx_date) between '" . $post['dateTo'] . "' and '" . $post['dateTo'] . "' )";
		} else if (!empty($post['dateFrom']) and empty($post['dateTo'])) {
			$where .= " and (date(OB.trx_date) between '" . $post['dateFrom'] . "' and '" . $post['dateFrom'] . "' )";
		} else {
			$where .= " and (date(OB.trx_date) between '" . date("Y-m-d") . "' and '" . date('Y-m-d', strtotime("-7 days")) . "' )";
		}

		$session_shift_class = $this->session->userdata('ship_class_id');
		if ($session_shift_class != '') {
			$where .= " and (OBO.ship_class = " . $session_shift_class . ")";
		}

		// $iLike = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

		// if (!empty($search['value'])){
		// 	$where .=" and (BO.booking_code ilike '%".$iLike."%')";
		// }

		$sql = "SELECT DISTINCT S.id,
		BV.ticket_number,
		U.first_name AS first_name,
		U.username AS username,
		DT.terminal_name AS loket,
		BO.booking_code AS booking_code,
		VC.name AS golongan,
		BV.fare AS tarif,
		S.payment_type AS payment_type,
		SC.name AS kelas,
		OB.trx_date AS trans_date,
		SF.shift_name AS shift,
		T.team_name AS regu,
		BV.id_number AS id_number,
		BV.name AS customer_name,
		K.name AS ship,
		brv.created_on::TIMESTAMP(0) AS naik_kapal
		FROM app.t_trx_sell S
		JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
		JOIN app.t_mtr_shift SF ON SF.id = OB.shift_id
		JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = S.terminal_code AND DT.status = 1
		JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
		JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class
		JOIN app.t_trx_booking_passanger BV ON BV.booking_code = BO.booking_code AND BV.service_id = 1
		JOIN app.t_mtr_passanger_type VC ON VC.id = BV.passanger_type_id AND VC.status=1
		LEFT JOIN app.t_trx_invoice I ON I.trans_number = BO.trans_number
		JOIN core.t_mtr_user U ON U.id = OB.user_id
		LEFT JOIN app.t_trx_boarding_passanger brv ON brv.ticket_number = BV.ticket_number AND brv.status = 1
		LEFT JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BRV.boarding_code
		LEFT JOIN app.t_mtr_ship K ON K.id = OBO.ship_id
		JOIN app.t_trx_assignment_user_pos AR ON AR.assignment_code = OB.assignment_code
		JOIN core.t_mtr_team T ON T.team_code = AR.team_code
		{$where}
		ORDER BY first_name ASC, ticket_number ASC, S.id DESC";

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= 1;

		foreach ($rows_data as $row) {
			$row->number = $i;
			$rows[] = $row;
			$i++;
		}

		return $rows;
	}

	public function downloadKendaraan()
	{
		$post = $this->input->post();

		$where = " WHERE S.status = 1 AND BP.id IN(SELECT MIN(BP2.id) FROM app.t_trx_booking_passanger BP2 WHERE BP2.booking_code=BP.booking_code)";

		if (!empty($post['username'])) {
			$where .= " and (U.username='" . $this->enc->decode($post['username']) . "')";
		}

		if (!empty($post['shift'])) {
			$where .= " and (OB.shift_id=" . $this->enc->decode($post['shift']) . ")";
		}

		if (!empty($post['port'])) {
			$where .= " and (I.port_id=" . $this->enc->decode($post['port']) . ")";
		}

		if (!empty($post['regu'])) {
			$where .= " and (AR.team_code='" . $this->enc->decode($post['regu']) . "')";
		}

		if (!empty($post['loket'])) {
			$where .= " and (S.terminal_code='" . $this->enc->decode($post['loket']) . "')";
		}

		if (!empty($post['dateTo']) and !empty($post['dateFrom'])) {
			$where .= " and (date(OB.trx_date) between '" . $post['dateFrom'] . "' and '" . $post['dateTo'] . "' )";
		} else if (empty($post['dateFrom']) and !empty($post['dateTo'])) {
			$where .= " and (date(OB.trx_date) between '" . $post['dateTo'] . "' and '" . $post['dateTo'] . "' )";
		} else if (!empty($post['dateFrom']) and empty($post['dateTo'])) {
			$where .= " and (date(OB.trx_date) between '" . $post['dateFrom'] . "' and '" . $post['dateFrom'] . "' )";
		} else {
			$where .= " and (date(OB.trx_date) between '" . date("Y-m-d") . "' and '" . date('Y-m-d', strtotime("-7 days")) . "' )";
		}

		$session_shift_class = $this->session->userdata('ship_class_id');
		if ($session_shift_class != '') {
			$where .= " and (OBO.ship_class = " . $session_shift_class . ")";
		}

		// $iLike = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

		// if (!empty($search['value'])){
		// 	$where .=" and (BO.booking_code ilike '%".$iLike."%')";
		// }

		$sql = "SELECT DISTINCT S.id,
		BV.ticket_number,
		U.first_name AS first_name,
		U.username AS username,
		DT.terminal_name AS loket,
		VC.name AS golongan,
		BV.fare AS tarif,
		S.payment_type AS payment_type,
		SC.name AS kelas,
		OB.trx_date AS trans_date,
		SF.shift_name AS shift,
		T.team_name AS regu,
		BO.booking_code AS booking_code,
		BV.id_number AS plat,
		I.customer_name AS customer_name,
		BP.id_number,
		K.name AS ship,
		brv.created_on::TIMESTAMP(0) AS naik_kapal
		FROM app.t_trx_sell S
		JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
		JOIN app.t_mtr_shift SF ON SF.id = OB.shift_id
		JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = S.terminal_code AND DT.status = 1
		JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
		JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class
		JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code AND BV.service_id = 2
		JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
		JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
		LEFT JOIN app.t_trx_invoice I ON I.trans_number = BO.trans_number
		JOIN core.t_mtr_user U ON U.id = OB.user_id
		LEFT JOIN app.t_trx_boarding_vehicle brv ON brv.ticket_number = BV.ticket_number AND brv.status = 1
		LEFT JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BRV.boarding_code
		LEFT JOIN app.t_mtr_ship K ON K.id = OBO.ship_id
		JOIN app.t_trx_assignment_user_pos AR ON AR.assignment_code = OB.assignment_code
		JOIN core.t_mtr_team T ON T.team_code = AR.team_code
		{$where}
		ORDER BY first_name ASC, ticket_number ASC, S.id DESC";

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= 1;

		foreach ($rows_data as $row) {
			$row->number = $i;
			$rows[] = $row;
			$i++;
		}

		return $rows;
	}
}
