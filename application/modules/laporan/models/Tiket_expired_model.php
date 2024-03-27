<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tiket_expired_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function list_data($port, $datefrom, $dateto, $ship_class, $shift, $type)
	{
		$where_port 	  = "";
		$where_shift 	  = "";
		$where_ship_class = "";

		if ($port != "") {
			$where_port = " AND bps.origin = $port";
		}

		if ($shift != "") {
			$where_shift = " AND ob.shift_id = $shift";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND bok.ship_class = $ship_class";
		}

		if ($type === 'penumpang') {

			$sql = "SELECT
						DISTINCT(SUB.harga),
						PTT.id,
						PTT.name as golongan,
						SUM(SUB.produksi) AS produksi,
						SUM(SUB.pendapatan) AS pendapatan
					FROM
						app.t_mtr_passanger_type PTT
						LEFT JOIN 	(SELECT
										DISTINCT(bps.fare) AS harga,
										tp.id,
										COUNT ( bps.ID ) AS produksi,
										COUNT(bps.id) * bps.fare AS pendapatan
									FROM
										app.t_trx_booking_passanger bps
										JOIN app.t_mtr_passanger_type tp ON tp.ID = bps.passanger_type_id
										JOIN app.t_trx_booking bok ON bok.booking_code = bps.booking_code $where_ship_class
										JOIN app.t_trx_invoice inv ON inv.trans_number = bok.trans_number 
										AND inv.status = 2
										JOIN app.t_trx_check_in chk ON chk.ticket_number = bps.ticket_number
										JOIN app.t_trx_opening_balance ob ON ob.ob_code = chk.created_by $where_shift
									WHERE
										ob.trx_date BETWEEN '$datefrom' AND '$dateto'
										AND bps.service_id = 1
										AND (bps.status IN ( 10, 11, 12 ) 
											OR ( bps.status = 2 AND bps.checkin_expired < CURRENT_TIMESTAMP ) 
											OR ( bps.status = 3 AND bps.gatein_expired < CURRENT_TIMESTAMP ) 
											OR ( bps.status IN ( 4, 7 ) AND bps.boarding_expired < CURRENT_TIMESTAMP ))
									GROUP BY
										tp.NAME,
										tp.id,
										bps.fare
									
									UNION ALL
													
									SELECT
											DISTINCT(bps.fare) AS harga,
											tp.id,
											COUNT ( bps.ID ) AS produksi,
											COUNT(bps.id) * bps.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger bps
											JOIN app.t_mtr_passanger_type tp ON tp.ID = bps.passanger_type_id
											JOIN app.t_trx_booking bok ON bok.booking_code = bps.booking_code $where_ship_class
											JOIN app.t_trx_invoice inv ON inv.trans_number = bok.trans_number AND inv.status = 2
											JOIN app.t_trx_check_in chk ON chk.ticket_number = bps.ticket_number
											JOIN app.t_trx_opening_balance_vm ob ON ob.ob_code = chk.created_by $where_shift
										WHERE
											ob.trx_date BETWEEN '$datefrom' AND '$dateto'
											AND bps.service_id = 1
											$where_port
											AND (bps.status IN ( 10, 11, 12 ) 
												OR ( bps.status = 2 AND bps.checkin_expired < CURRENT_TIMESTAMP ) 
												OR ( bps.status = 3 AND bps.gatein_expired < CURRENT_TIMESTAMP ) 
												OR ( bps.status IN ( 4, 7 ) AND bps.boarding_expired < CURRENT_TIMESTAMP )) 
										GROUP BY
											tp.NAME,
											tp.id,
											bps.fare) SUB ON SUB.id = PTT.id
								WHERE PTT.id IN(1,2,3)
								GROUP BY
									PTT.id,
									harga
								ORDER BY
									id ASC,
									harga ASC";

			$cek_ada = $this->dbView->query($sql)->num_rows();

			if ($cek_ada > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		}

		if ($type === 'kendaraan') {

			$sql = "SELECT
						SUB.harga,
						VCC.id,
						VCC.name as golongan,
						SUB.produksi,
						SUB.pendapatan
					FROM
						app.t_mtr_vehicle_class VCC
						LEFT JOIN 	(SELECT
										DISTINCT(bps.fare) AS harga,
										tp.id,
										COUNT ( bps.ID ) AS produksi,
										COUNT(bps.id) * bps.fare AS pendapatan
									FROM
										app.t_trx_booking_vehicle bps
										JOIN app.t_mtr_vehicle_class tp ON tp.ID = bps.vehicle_class_id
										JOIN app.t_trx_booking bok ON bok.booking_code = bps.booking_code $where_ship_class
										JOIN app.t_trx_invoice inv ON inv.trans_number = bok.trans_number AND inv.status = 2
										JOIN app.t_trx_check_in_vehicle chk ON chk.ticket_number = bps.ticket_number
										JOIN app.t_trx_opening_balance ob ON ob.ob_code = chk.created_by $where_shift
									WHERE
										ob.trx_date BETWEEN '$datefrom' AND '$dateto' 
										$where_port
										AND (bps.status IN ( 10, 11, 12 ) 
											OR ( bps.status = 2 AND bps.checkin_expired < CURRENT_TIMESTAMP ) 
											OR ( bps.status = 3 AND bps.gatein_expired < CURRENT_TIMESTAMP ) 
											OR ( bps.status IN ( 4, 7 ) AND bps.boarding_expired < CURRENT_TIMESTAMP )) 
									GROUP BY
										tp.NAME,
										tp.id,
										bps.fare) SUB ON SUB.id = VCC.id
					WHERE
						VCC.status = 1
					ORDER BY
						id ASC";

			$cek_ada = $this->dbView->query($sql)->num_rows();

			if ($cek_ada > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		}
	}

	public function getport()
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_port WHERE status=1")->result();
	}

	public function getshift()
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_shift WHERE status=1")->result();
	}

	public function getclass()
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_ship_class WHERE status=1")->result();
	}

	public function get_lintasan($port, $datefrom, $dateto, $ship_class, $shift)
	{
		$where_port = "";
		$where_ship_class = "";
		$where_shift = "";

		if ($port != "") {
			$where_port = "AND UP.port_id = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = "AND BO.ship_class = $ship_class";
		}

		if ($shift != "") {
			$where_shift = " AND OB.shift_id = $shift";
		}

		$sql = "SELECT DISTINCT
					PO.name as port_origin,
					PD.name as port_destination
				FROM
					app.t_trx_assignment_user_pos UP
					JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code $where_shift
					JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number $where_ship_class
					JOIN app.t_mtr_port PO ON PO.id = BO.origin
					JOIN app.t_mtr_port PD ON PD.id = BO.destination
				WHERE
					UP.assignment_date BETWEEN '$datefrom' AND '$dateto'
					$where_port";

		if ($this->dbView->query($sql)->num_rows() > 0) {
			return $this->dbView->query($sql);
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

		if ($this->dbView->query($sql)->num_rows() == 1) {
			return $this->dbView->query($sql)->row();
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
