<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pendapatan_harian_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function list_data($port,$tanggal,$regu,$petugas,$shift,$ship_class,$type)
	{
		$where_regu = "";
		$where_petugas = "";
		$where_shift = "";

		if ($regu != "") {
			$where_regu = " AND UP.team_code = '$regu'";
		}

		if ($petugas != "") {
			$where_petugas = " AND UP.user_id = $petugas";
		}

		if ($type === 'penumpang') {
			$sql = "SELECT
					DISTINCT(BP.fare) as harga,
					T.team_name,
					PT.name as golongan,
					COUNT(DISTINCT(BP.id)) as produksi,
					COUNT(DISTINCT(BP.id)) * BP.fare as pendapatan
				FROM
					app.t_trx_assignment_user_pos UP
					JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
					JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number AND BO.ship_class = $ship_class
					-- JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code AND BP.service_id = 1
					JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1 AND PT.id != 3
					JOIN core.t_mtr_team T ON T.team_code = UP.team_code
				WHERE
					UP.assignment_date = '$tanggal'
					AND UP.port_id = $port
					AND UP.shift_id = $shift
					$where_regu
					$where_petugas
				GROUP BY
					BP.fare,
					T.team_name,
					PT.name";

			$cek_ada = $this->db->query($sql)->num_rows();

			if ($cek_ada > 0) {
				return $this->db->query($sql)->result();
			}else{
				return false;
			}
		}

		if ($type === 'kendaraan') {
			$sql = "SELECT
						DISTINCT(BV.fare) as harga,
						T.team_name,
						VC.name as golongan,
						COUNT(DISTINCT(BV.id)) as produksi,
						COUNT(DISTINCT(BV.id)) * BV.fare as pendapatan
					FROM
						app.t_trx_assignment_user_pos UP
						JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
						JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
						JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number AND BO.ship_class = $ship_class
						-- JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
						JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code
						JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
						JOIN core.t_mtr_team T ON T.team_code = UP.team_code
					WHERE
						UP.assignment_date = '$tanggal'
						AND UP.port_id = $port
						AND UP.shift_id = $shift
						$where_regu
						$where_petugas
					GROUP BY
						BV.fare,
						T.team_name,
						VC.name";

			$cek_ada = $this->db->query($sql)->num_rows();

			if ($cek_ada > 0) {
				return $this->db->query($sql)->result();
			}else{
				return false;
			}
		}
	}

	public function get_lintasan($port,$tanggal,$ship_class)
	{
		$sql = "SELECT DISTINCT
		PO.name as origin,
		PD.name as destination
		FROM
		app.t_trx_assignment_user_pos UP
		JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
		JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
		JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number AND BO.ship_class = $ship_class
		JOIN app.t_mtr_port PO ON PO.id = BO.origin
		JOIN app.t_mtr_port PD ON PD.id = BO.destination
		WHERE
		UP.assignment_date = '$tanggal'
		AND UP.port_id = $port";

		if ($this->db->query($sql)->num_rows() > 0) {
			return $this->db->query($sql)->row();
		}else{
			return false;
		}
	}
}