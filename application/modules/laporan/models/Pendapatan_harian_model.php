<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pendapatan_harian_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function getvm()
	{
		$port_id = $this->session->userdata('port_id');

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

	public function list_data($port, $datefrom, $dateto, $shift, $ship_class, $type, $vm = "")
	{
		$where_port = "";
		$where_shift = "";
		$where_ship_class = "";
		$where_vm = "";

		if ($port != "") {
			$where_port = "AND UP.port_id = $port";
		}

		if ($shift != "") {
			$where_shift = "AND UP.shift_id = $shift";
			$where_shift_vm = "AND OB.shift_id = $shift";
		}

		if ($ship_class != "") {
			$where_ship_class = "AND BO.ship_class = $ship_class";
		}

		if ($vm != "") {
			$where_vm = "AND OB.terminal_code = '$vm'";
		}

		if ($type === 'penumpang') {
			$sql = "SELECT
						-- DISTINCT(PTT.id) as idku,
						DISTINCT(PTT.ordering) as idku,
						PTT.name as golongan,
						SUB.harga,
						SUM(SUB.produksi) AS produksi,
						SUM(SUB.pendapatan) AS pendapatan
					FROM
						app.t_mtr_passanger_type PTT
						LEFT JOIN 	(SELECT
										DISTINCT(BP.fare) as harga,
										PT.id,
										COUNT(DISTINCT(BP.id)) as produksi,
										COUNT(DISTINCT(BP.id)) * BP.fare as pendapatan
									FROM
										app.t_trx_assignment_user_pos UP
										JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
										JOIN app.t_trx_check_in S ON S.created_by = OB.ob_code
										JOIN app.t_trx_booking_passanger BP ON BP.ticket_number = S.ticket_number AND BP.service_id = 1
										JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
										JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1
										JOIN core.t_mtr_team T ON T.team_code = UP.team_code
									WHERE
										UP.assignment_date BETWEEN '$datefrom' AND '$dateto'
										$where_port
										$where_shift
									GROUP BY
										BP.fare,
										PT.name,
										PT.id

									UNION ALL

									SELECT
										DISTINCT(BP.fare) as harga,
										PT.id,
										COUNT(DISTINCT(BP.id)) as produksi,
										COUNT(DISTINCT(BP.id)) * BP.fare as pendapatan
									FROM
										app.t_trx_assignment_user_pos UP
										JOIN app.t_trx_opening_balance_vm OB ON OB.assignment_code = UP.assignment_code $where_vm $where_shift
										JOIN app.t_trx_check_in S ON S.created_by = OB.ob_code
										JOIN app.t_trx_booking_passanger BP ON BP.ticket_number = S.ticket_number AND BP.service_id = 1
										JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
										JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1
										JOIN core.t_mtr_team T ON T.team_code = UP.team_code
									WHERE
										UP.assignment_date BETWEEN '$datefrom' AND '$dateto'
										$where_port
										$where_shift
									GROUP BY
										BP.fare,
										PT.name,
										PT.id
									ORDER BY
										id ASC) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3,4)
					GROUP BY
						idku,
						golongan,
						harga

					order by idku asc
					";
			
			// echo $sql; exit;

			if ($vm != "") {
				$sql = "SELECT
						-- DISTINCT(PTT.id) as idku,
						DISTINCT(PTT.ordering) as idku,
						PTT.name as golongan,
						SUB.harga,
						SUM(SUB.produksi) AS produksi,
						SUM(SUB.pendapatan) AS pendapatan
					FROM
						app.t_mtr_passanger_type PTT
						LEFT JOIN 	(SELECT
										DISTINCT(BP.fare) as harga,
										PT.id,
										COUNT(DISTINCT(BP.id)) as produksi,
										COUNT(DISTINCT(BP.id)) * BP.fare as pendapatan
									FROM
										app.t_trx_assignment_user_pos UP
										JOIN app.t_trx_opening_balance_vm OB ON OB.assignment_code = UP.assignment_code $where_vm $where_shift
										JOIN app.t_trx_check_in S ON S.created_by = OB.ob_code
										JOIN app.t_trx_booking_passanger BP ON BP.ticket_number = S.ticket_number AND BP.service_id = 1
										JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
										JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1
										JOIN core.t_mtr_team T ON T.team_code = UP.team_code
									WHERE
										UP.assignment_date BETWEEN '$datefrom' AND '$dateto'
										$where_port
										$where_shift
									GROUP BY
										BP.fare,
										PT.name,
										PT.id
									ORDER BY
										id ASC) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3,4)
					GROUP BY
						idku,
						golongan,
						harga

						order by idku asc
					";

			}

			$cek_ada = $this->dbView->query($sql)->num_rows();

			if ($cek_ada > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		}

		if ($type === 'kendaraan') {
			$sql = "SELECT
						VCC.name as golongan,
						SUB.*
					FROM
						app.t_mtr_vehicle_class VCC
						LEFT JOIN 	(SELECT
										DISTINCT(BV.fare) as harga,
										VC.id,
										COUNT(DISTINCT(BV.id)) as produksi,
										COUNT(DISTINCT(BV.id)) * BV.fare as pendapatan
									FROM
										app.t_trx_assignment_user_pos UP
										JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
										JOIN app.t_trx_check_in_vehicle S ON S.created_by = OB.ob_code
										JOIN app.t_trx_booking_vehicle BV ON BV.ticket_number = S.ticket_number
										JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
										JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
										JOIN core.t_mtr_team T ON T.team_code = UP.team_code
									WHERE
										UP.assignment_date BETWEEN '$datefrom' AND '$dateto'
										$where_port
										$where_shift
									GROUP BY
										BV.fare,
										VC.name,
										VC.id

									UNION ALL

									SELECT
										DISTINCT(BV.fare) as harga,
										VC.id,
										COUNT(DISTINCT(BV.id)) as produksi,
										COUNT(DISTINCT(BV.id)) * BV.fare as pendapatan
									FROM
										app.t_trx_assignment_user_pos UP
										JOIN app.t_trx_opening_balance_vm OB ON OB.assignment_code = UP.assignment_code $where_vm $where_shift
										JOIN app.t_trx_check_in_vehicle S ON S.created_by = OB.ob_code
										JOIN app.t_trx_booking_vehicle BV ON BV.ticket_number = S.ticket_number
										JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
										JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
										JOIN core.t_mtr_team T ON T.team_code = UP.team_code
									WHERE
										UP.assignment_date BETWEEN '$datefrom' AND '$dateto'
										$where_port
										$where_shift
									GROUP BY
										BV.fare,
										VC.name,
										VC.id
									ORDER BY
										id ASC
									) SUB ON SUB.id = VCC.id
					WHERE VCC.status = 1 ORDER BY VCC.id ASC";

			if ($vm != "") {
				$sql = "SELECT
						VCC.name as golongan,
						SUB.*
					FROM
						app.t_mtr_vehicle_class VCC
						LEFT JOIN 	(SELECT
										DISTINCT(BV.fare) as harga,
										VC.id,
										COUNT(DISTINCT(BV.id)) as produksi,
										COUNT(DISTINCT(BV.id)) * BV.fare as pendapatan
									FROM
										app.t_trx_assignment_user_pos UP
										JOIN app.t_trx_opening_balance_vm OB ON OB.assignment_code = UP.assignment_code $where_vm $where_shift
										JOIN app.t_trx_check_in_vehicle S ON S.created_by = OB.ob_code
										JOIN app.t_trx_booking_vehicle BV ON BV.ticket_number = S.ticket_number
										JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
										JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
										JOIN core.t_mtr_team T ON T.team_code = UP.team_code
									WHERE
										UP.assignment_date BETWEEN '$datefrom' AND '$dateto'
										$where_port
										$where_shift
									GROUP BY
										BV.fare,
										VC.name,
										VC.id
									ORDER BY
										id ASC
									) SUB ON SUB.id = VCC.id
					WHERE VCC.status = 1 ORDER BY VCC.id ASC";
			}

			$cek_ada = $this->dbView->query($sql)->num_rows();

			if ($cek_ada > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		}
	}

	public function get_lintasan($port, $datefrom, $dateto, $ship_class)
	{
		$where_port = "";
		$where_ship_class = "";

		if ($port != "") {
			$where_port = "AND UP.port_id = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = "AND BO.ship_class = $ship_class";
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
			return $this->dbView->query($sql)->row();
		} else {
			return false;
		}
	}

	public function get_team($port, $datefrom, $dateto, $ship_class, $shift)
	{
		$where_port = "";
		$where_shift = "";
		$where_ship_class = "";

		if ($port != "") {
			$where_port = "AND UP.port_id = $port";
		}

		if ($shift != "") {
			$where_shift = "AND UP.shift_id = $shift";
		}

		if ($ship_class != "") {
			$where_ship_class = "AND BO.ship_class = $ship_class";
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
		} else {
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