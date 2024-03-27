<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ticket_terjual_vm_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	public function list_data_07022024($port,$datefrom,$dateto,$regu,$petugas,$shift,$ship_class,$type,$vm = "")
	{
		$where_port = "";
		$where_regu = "";
		$where_shift = "";
		$where_ship_class = "";
		$where_vm = "";

		if ($port != "") {
			$where_port = " AND BP.origin = $port";
		}

		if ($regu != "") {
			$where_regu = " AND AR.team_code = '$regu'";
		}

		if ($petugas != "") {
			$where_petugas = " AND OB.user_id = $petugas";
		}

		if ($shift != "") {
			$where_shift = " AND OB.shift_id = $shift";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		if ($vm != "") {
			$where_vm = " AND OB.terminal_code = '$vm'";
		}

		if ($type === 'vm') {
			$sql = "SELECT
						DTT.terminal_name,
						PT.name AS golongan,
						BP.fare AS harga,
						PT.ID,
						COUNT ( BP.ticket_number ) AS produksi,
						COUNT ( BP.ticket_number ) * BP.fare AS pendapatan
					FROM
						app.t_trx_booking_passanger BP
						JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
						JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
						JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
						JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
						JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
						JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
						JOIN app.t_mtr_device_terminal DTT ON DTT.terminal_code = OB.terminal_code
					WHERE
						BP.service_id = 1
						$where_port
					GROUP BY
						DTT.terminal_name,
						golongan,
						BP.fare,
						PT.NAME,
						PT.ID
					ORDER BY
						terminal_name ASC,
						id ASC";

			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			}else{
				return false;
			}
		}
	}
	public function list_data($port,$datefrom,$dateto,$regu,$petugas,$shift,$ship_class,$type,$vm = "")
	{
		$where_port = "";
		$where_regu = "";
		$where_shift = "";
		$where_ship_class = "";
		$where_vm = "";

		if ($port != "") {
			$where_port = " AND BP.origin = $port";
		}

		if ($regu != "") {
			$where_regu = " AND AR.team_code = '$regu'";
		}

		if ($petugas != "") {
			$where_petugas = " AND OB.user_id = $petugas";
		}

		if ($shift != "") {
			$where_shift = " AND OB.shift_id = $shift";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		if ($vm != "") {
			$where_vm = " AND OB.terminal_code = '$vm'";
		}

		if ($type === 'vm') {
			$sql = "SELECT * from (
				SELECT
					1 as type,
					DTT.terminal_name,
					PT.name AS golongan,
					BP.fare AS harga,
					PT.ID,
					COUNT ( BP.ticket_number ) AS produksi,
					COUNT ( BP.ticket_number ) * BP.fare AS pendapatan
				FROM
					app.t_trx_booking_passanger BP
					JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
					JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
					JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
					JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
					JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
					JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
					JOIN app.t_mtr_device_terminal DTT ON DTT.terminal_code = OB.terminal_code
				WHERE
					BP.service_id = 1
					$where_port
				GROUP BY
					DTT.terminal_name,
					golongan,
					BP.fare,
					PT.NAME,
					PT.ID

					UNION

					SELECT
					2 as type,
					DTT.terminal_name,
					PT.name AS golongan,
					BP.fare AS harga,
					PT.ID,
					COUNT ( BP.ticket_number ) AS produksi,
					COUNT ( BP.ticket_number ) * BP.fare AS pendapatan
				FROM
					app.t_trx_booking_vehicle BP
					JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
					JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
					JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
					JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
					JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
					JOIN app.t_mtr_vehicle_class PT ON PT.ID = BP.vehicle_class_id 
					JOIN app.t_mtr_device_terminal DTT ON DTT.terminal_code = OB.terminal_code
				WHERE
					BP.service_id = 2
					$where_port
				GROUP BY
					DTT.terminal_name,
					golongan,
					BP.fare,
					PT.NAME,
					PT.ID
				) as dt
				ORDER BY
					dt.type ASC,
					dt.terminal_name ASC,
					dt.id ASC	";
			die($sql); exit;
			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			}else{
				return false;
			}
		}
	}

	public function getport()
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_port WHERE status=1")->result();
	}

	public function getregu()
	{
		return $this->dbView->query("SELECT * FROM core.t_mtr_team WHERE status=1")->result();
	}

	public function getpetugas()
	{
		return $this->dbView->query("SELECT * FROM core.t_mtr_user WHERE user_group_id = 4 AND status=1")->result();
	}

	public function getshift()
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_shift WHERE status=1")->result();
	}

	public function getclass()
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_ship_class WHERE status=1")->result();
	}

	public function get_team($port_id)
	{
		return $this->dbView->query("SELECT team_code,team_name FROM core.t_mtr_team WHERE port_id = $port_id ORDER BY team_name ASC")->result();
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
			}else{
				$where_port = "AND port_id = $port_id";
			}
		}else{
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

		if ($this->dbView->query($sql)->num_rows() > 0) {
			return $this->dbView->query($sql);
		}else{
			return false;
		}
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
					$getData  = $data->row();
					$result[] = array('id' => $getData->id, 'name' => $getData->name);
				}
			} else {
				$data     = $this->dbView->query($sql)->result();
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