<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pindah_kapal_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function list_data($port, $datefrom, $dateto, $ship_class, $shift, $regu, $type)
	{
		$where_port 	  = "";
		$where_ship_class = "";
		$where_shift 	  = "";
		$where_regu 	  = "";

		if ($port != "") {
			$where_port = " AND boar.port_id = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = "AND boar.ship_class = $ship_class";
		}

		if ($shift != "") {
			$where_shift = "AND boar.shift_id = $shift";
		}

		if ($regu != "") {
			$where_regu = "AND UP.team_code = '$regu'";
		}

		if ($type === "penumpang") {
				$sql = "SELECT
							PTT.id as idku,
							PTT.name as golongan,
							SUB.*
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN (SELECT
											DISTINCT( BP.fare ) AS harga,
											PT.ID,
											COUNT ( DISTINCT ( BP.ticket_number ) ) AS produksi,
											COUNT ( DISTINCT ( BP.ticket_number ) ) * BP.fare AS pendapatan 
										FROM
											app.t_trx_switch_ship_all_passanger SWP
											JOIN app.t_trx_switch_ship_all_passanger_detail SWPD ON SWPD.switch_ship_code = SWP.switch_ship_code
											JOIN app.t_trx_booking_passanger BP ON BP.ticket_number = SWPD.ticket_number
											JOIN app.t_trx_open_boarding boar on boar.boarding_code = SWP.boarding_code AND boar.shift_date BETWEEN '$datefrom' AND '$dateto' $where_port $where_shift $where_ship_class
											JOIN app.t_trx_assignment_regu reg on reg.assignment_date = boar.shift_date and reg.shift_id = boar.shift_id $where_regu								
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
										GROUP BY
											BP.fare,
											PT.ID) SUB ON SUB.id = PTT.id
							WHERE PTT.id IN(1,2,3,4)
							order by  PTT.ordering asc
							
							";

						if ($this->dbView->query($sql)->num_rows() > 0) {
							return $this->dbView->query($sql)->result();
						}else{
							return false;
						}
					}
					
		if ($type === "kendaraan") {		
			$sql = 	"SELECT
						VCC.id as idku,
						VCC.name as golongan,
						SUB.*
					FROM
						app.t_mtr_vehicle_class VCC
						LEFT JOIN (SELECT
											DISTINCT( BV.fare ) AS harga,
											VC.ID,
											COUNT ( DISTINCT ( BV.ticket_number ) ) AS produksi,
											COUNT ( DISTINCT ( BV.ticket_number ) ) * BV.fare AS pendapatan 
										FROM
											app.t_trx_switch_ship_all_vehicle SWV
											JOIN app.t_trx_switch_ship_all_vehicle_detail SWVD ON SWVD.switch_ship_code = SWV.switch_ship_code
											JOIN app.t_trx_booking_vehicle BV ON BV.ticket_number = SWVD.ticket_number
											JOIN app.t_trx_open_boarding boar on boar.boarding_code =SWV.boarding_code AND boar.shift_date BETWEEN '$datefrom' AND '$dateto' $where_port $where_shift $where_ship_class
											JOIN app.t_trx_assignment_regu reg on reg.assignment_date = boar.shift_date and reg.shift_id=boar.shift_id $where_regu								
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										GROUP BY
											BV.fare,
											VC.ID) SUB ON SUB.id = VCC.id
					WHERE
						VCC.status = 1
					ORDER BY
						idku ASC";

				if ($this->dbView->query($sql)->num_rows() > 0) {
					return $this->dbView->query($sql)->result();
				}else{
					return false;
				}
			}
	}

	public function get_team($port_id)
	{
		return $this->dbView->query("SELECT team_code,team_name FROM core.t_mtr_team WHERE port_id = $port_id ORDER BY team_name ASC")->result();
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
		$where_port 	  	   = "";
		$where_ship_class      = "";
		$where_shift_penumpang = "";
		$where_shift_kendaraan = "";

		if ($port != "") {
			$where_port = " AND BO.origin = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		if ($shift != "") {
			$where_shift_penumpang = " AND SWP.shift_id = $shift";
			$where_shift_kendaraan = " AND SWV.shift_id = $shift";
		}

		$sql = "SELECT DISTINCT origin,
								destination,
								ORI.name as port_origin,
								DE.name as port_destination
				FROM (SELECT DISTINCT
							BO.origin,
							BO.destination 
						FROM
							app.t_trx_switch_ship_all_passanger SWP
							JOIN app.t_trx_switch_ship_all_passanger_detail SWPD ON SWPD.switch_ship_code = SWP.switch_ship_code
							JOIN app.t_trx_booking_passanger BP ON BP.ticket_number = SWPD.ticket_number
							JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_port $where_ship_class
						WHERE
							SWP.date BETWEEN '$datefrom' AND '$dateto'
							$where_shift_penumpang
							
						UNION ALL

						SELECT DISTINCT
							BO.origin,
							BO.destination
						FROM
							app.t_trx_switch_ship_all_vehicle SWV
							JOIN app.t_trx_switch_ship_all_vehicle_detail SWVD ON SWVD.switch_ship_code = SWV.switch_ship_code
							JOIN app.t_trx_booking_vehicle BV ON BV.ticket_number = SWVD.ticket_number
							JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_port $where_ship_class
						WHERE
							SWV.date BETWEEN '$datefrom' AND '$dateto'
							$where_shift_kendaraan) A
				JOIN app.t_mtr_port ORI ON ORI.id = A.origin
				JOIN app.t_mtr_port DE ON DE.id = A.destination";

		if ($this->dbView->query($sql)->num_rows() == 1) {
			return $this->dbView->query($sql);
		}else{
			return false;
		}
	}

	public function get_ship_origin($port, $datefrom, $dateto, $ship_class, $shift)
	{
		$where_port 	       = "";
		$where_ship_class      = "";
		$where_shift_penumpang = "";
		$where_shift_kendaraan = "";

		if ($port != "") {
			$where_port = " AND BO.origin = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		if ($shift != "") {
			$where_shift_penumpang = " AND SWP.shift_id = $shift";
			$where_shift_kendaraan = " AND SWV.shift_id = $shift";
		}

		$sql = "SELECT DISTINCT SS.name
				FROM (SELECT DISTINCT
							SWP.ship_id
						FROM
							app.t_trx_switch_ship_all_passanger SWP
							JOIN app.t_trx_switch_ship_all_passanger_detail SWPD ON SWPD.switch_ship_code = SWP.switch_ship_code
							JOIN app.t_trx_booking_passanger BP ON BP.ticket_number = SWPD.ticket_number
							JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_port $where_ship_class
						WHERE
							SWP.date BETWEEN '$datefrom' AND '$dateto'
							$where_shift_penumpang
							
						UNION ALL

						SELECT DISTINCT
							SWV.ship_id
						FROM
							app.t_trx_switch_ship_all_vehicle SWV
							JOIN app.t_trx_switch_ship_all_vehicle_detail SWVD ON SWVD.switch_ship_code = SWV.switch_ship_code
							JOIN app.t_trx_booking_vehicle BV ON BV.ticket_number = SWVD.ticket_number
							JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_port $where_ship_class
						WHERE
							SWV.date BETWEEN '$datefrom' AND '$dateto'
							$where_shift_kendaraan) A
				JOIN app.t_mtr_ship SS ON SS.id = A.ship_id";

		if ($this->dbView->query($sql)->num_rows() == 1) {
			return $this->dbView->query($sql);
		}else{
			return false;
		}
	}

	public function get_ship_destination($port, $datefrom, $dateto, $ship_class, $shift)
	{
		$where_port 	       = "";
		$where_ship_class      = "";
		$where_shift_penumpang = "";
		$where_shift_kendaraan = "";

		if ($port != "") {
			$where_port = " AND BO.origin = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		if ($shift != "") {
			$where_shift_penumpang = " AND SWP.shift_id = $shift";
			$where_shift_kendaraan = " AND SWV.shift_id = $shift";
		}

		$sql = "SELECT DISTINCT SS.name
				FROM (SELECT DISTINCT
							SWP.ship_id
						FROM
							app.t_trx_switch_ship_all_passanger SWP
							JOIN app.t_trx_switch_ship_all_passanger_detail SWPD ON SWPD.switch_ship_code = SWP.switch_ship_code
							JOIN app.t_trx_booking_passanger BP ON BP.ticket_number = SWPD.ticket_number
							JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_port $where_ship_class
						WHERE
							SWP.date BETWEEN '$datefrom' AND '$dateto'
							$where_shift_penumpang
							
						UNION ALL

						SELECT DISTINCT
							SWV.ship_id
						FROM
							app.t_trx_switch_ship_all_vehicle SWV
							JOIN app.t_trx_switch_ship_all_vehicle_detail SWVD ON SWVD.switch_ship_code = SWV.switch_ship_code
							JOIN app.t_trx_booking_vehicle BV ON BV.ticket_number = SWVD.ticket_number
							JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_port $where_ship_class
						WHERE
							SWV.date BETWEEN '$datefrom' AND '$dateto'
							$where_shift_kendaraan) A
				JOIN app.t_mtr_ship SS ON SS.id = A.ship_id";

		if ($this->dbView->query($sql)->num_rows() == 1) {
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