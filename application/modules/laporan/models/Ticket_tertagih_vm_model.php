<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ticket_tertagih_vm_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();		
	}

	public function get_regu($port_id)
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

	public function list_data_07022024($port,$datefrom,$dateto,$petugas,$shift,$ship_class,$type,$vm = "")
	{
		$where_port = "";
		$where_petugas = "";
		$where_shift = "";
		$where_ship_class = "";
		$where_vm = "";

		if ($port != "") {
			$where_port = " AND OBO.port_id = $port";
		}

		if ($petugas != "") {
			$where_petugas = " AND OB.user_id = $petugas";
		}

		if ($vm != "") {
			$where_vm = " AND OB.terminal_code = '$vm'";
		}

		if ($shift != "") {
			$where_shift = " AND OBO.shift_id = $shift";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BKP.ship_class = $ship_class";
		}

		if ($type === 'vm') {
			$sql = "SELECT
						DTT.terminal_name,
						PT.ID,
						PT.name AS golongan,
						( BKP.fare ) AS harga,
						COUNT ( BOP.ticket_number ) AS produksi,
						COUNT ( BOP.ticket_number ) * BKP.fare AS pendapatan
					FROM
						app.t_trx_open_boarding OBO
						JOIN app.t_trx_boarding_passanger BOP ON OBO.boarding_code = BOP.boarding_code
						JOIN app.t_trx_booking_passanger BKP ON BKP.ticket_number = BOP.ticket_number AND BKP.service_id = 1 $where_ship_class
						JOIN app.t_trx_booking BO ON BO.booking_code = BKP.booking_code
						JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
						JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code $where_vm
						JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ( 'prepaid-bni', 'prepaid-bri', 'prepaid-mandiri' )
						JOIN app.t_mtr_passanger_type PT ON PT.ID = BKP.passanger_type_id AND PT.status = 1
						JOIN app.t_mtr_device_terminal DTT ON DTT.terminal_code = OB.terminal_code
					WHERE
						OBO.status = 0
						AND OBO.shift_date BETWEEN '$datefrom' AND '$dateto'
						$where_port
						$where_shift
					GROUP BY
						PT.ID,
						terminal_name,
						BKP.fare,
						PT.NAME
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

	public function list_data($port,$datefrom,$dateto,$petugas,$shift,$ship_class,$type,$vm = "")
	{
		$where_port = "";
		$where_petugas = "";
		$where_shift = "";
		$where_ship_class = "";
		$where_vm = "";

		if ($port != "") {
			$where_port = " AND OBO.port_id = $port";
		}

		if ($petugas != "") {
			$where_petugas = " AND OB.user_id = $petugas";
		}

		if ($vm != "") {
			$where_vm = " AND OB.terminal_code = '$vm'";
		}

		if ($shift != "") {
			$where_shift = " AND OBO.shift_id = $shift";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BKP.ship_class = $ship_class";
		}

		if ($type === 'vm') {
			$sql = "SELECT * FROM (
					SELECT
						BO.service_id,
						DTT.terminal_name,
						PT.ID,
						PT.name AS golongan,
						( BKP.fare ) AS harga,
						COUNT ( BOP.ticket_number ) AS produksi,
						COUNT ( BOP.ticket_number ) * BKP.fare AS pendapatan
					FROM
						app.t_trx_open_boarding OBO
						JOIN app.t_trx_boarding_passanger BOP ON OBO.boarding_code = BOP.boarding_code
						JOIN app.t_trx_booking_passanger BKP ON BKP.ticket_number = BOP.ticket_number AND BKP.service_id = 1 $where_ship_class
						JOIN app.t_trx_booking BO ON BO.booking_code = BKP.booking_code
						JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
						JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code $where_vm
						JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ( 'prepaid-bni', 'prepaid-bri', 'prepaid-mandiri' )
						JOIN app.t_mtr_passanger_type PT ON PT.ID = BKP.passanger_type_id AND PT.status = 1
						JOIN app.t_mtr_device_terminal DTT ON DTT.terminal_code = OB.terminal_code
					WHERE
						OBO.status = 0
						AND OBO.shift_date BETWEEN '$datefrom' AND '$dateto'
						$where_port
						$where_shift
					GROUP BY
						BO.service_id,
						PT.ID,
						terminal_name,
						BKP.fare,
						PT.NAME
					UNION
						SELECT
						BO.service_id,
						DTT.terminal_name,
						PT.ID,
						PT.name AS golongan,
						( BKP.fare ) AS harga,
						COUNT ( BOP.ticket_number ) AS produksi,
						COUNT ( BOP.ticket_number ) * BKP.fare AS pendapatan
					FROM
						app.t_trx_open_boarding OBO
						JOIN app.t_trx_boarding_vehicle BOP ON OBO.boarding_code = BOP.boarding_code
						JOIN app.t_trx_booking_vehicle BKP ON BKP.ticket_number = BOP.ticket_number  $where_ship_class
						JOIN app.t_trx_booking BO ON BO.booking_code = BKP.booking_code
						JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
						JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code $where_vm
						JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ( 'prepaid-bni', 'prepaid-bri', 'prepaid-mandiri' )
						JOIN app.t_mtr_vehicle_class PT ON PT.ID = BKP.vehicle_class_id AND PT.status = 1
						JOIN app.t_mtr_device_terminal DTT ON DTT.terminal_code = OB.terminal_code
					WHERE
						OBO.status = 0
						AND OBO.shift_date BETWEEN '$datefrom' AND '$dateto'
						$where_port
						$where_shift
					GROUP BY
						BO.service_id,
						PT.ID,
						terminal_name,
						BKP.fare,
						PT.NAME
					) as dt
					ORDER BY
						dt.service_id ASC,
						dt.terminal_name ASC,
						dt.id ASC";

			// die($sql); exit;

			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			}else{
				return false;
			}
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

		if ($this->dbView->query($sql)->num_rows() > 0) {
			return $this->dbView->query($sql);
		}else{
			return false;
		}
	}

	public function get_team($port,$datefrom,$dateto,$ship_class,$shift)
	{
		$where_port = "";
		$where_ship_class = "";
		$where_shift = "";

		if ($port != "") {
			$where_port = " AND UP.port_id = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		if ($shift != "") {
			$where_shift = " AND UP.shift_id = $shift";
		}

		$sql = "SELECT DISTINCT
			PO.name as origin,
			T.team_name,
			B.branch_name
		FROM
			app.t_trx_assignment_user_pos UP
			JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
			JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
			JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number $where_ship_class
			JOIN app.t_mtr_port PO ON PO.id = BO.origin
			JOIN app.t_mtr_port PD ON PD.id = BO.destination
			JOIN core.t_mtr_team T ON T.team_code = UP.team_code
			JOIN app.t_mtr_branch B ON B.branch_code = BO.branch_code
		WHERE
			UP.assignment_date BETWEEN '$datefrom' AND '$dateto'
			$where_port
			$where_shift";

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