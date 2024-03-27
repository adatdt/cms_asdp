<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sab_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function list_data($port, $datefrom, $dateto, $ship_class, $type)
	{
		$where_port_passanger = "";
		$where_port_vehicle = "";
		$where_regu = "";
		$where_petugas = "";
		$where_shift = "";
		$where_ship_class = "";

		if ($port != "") {
			$where_port_passanger = " AND BP.origin = $port";
			$where_port_vehicle = " AND BV.origin = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		if ($type === "penumpang") {
			$sql = "SELECT
							PTT.id as idku,
							PTT.name as golongan,
							SUB.*
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											PT.ID,
											COUNT ( DISTINCT(BP.ticket_number) ) AS produksi
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code AND BO.channel IN ( 'web_admin' ) $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.depart_date BETWEEN '$datefrom' AND '$dateto'
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
						WHERE PTT.id IN(1,2,3,4)
						order by PTT.ordering asc
						
						";

			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			} else {
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
										VC.ID,
										COUNT ( DISTINCT(BV.ticket_number) ) AS produksi
									FROM
										app.t_trx_booking_vehicle BV
										JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'web_admin' ) $where_ship_class
										JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
										JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
									WHERE
										BV.depart_date BETWEEN '$datefrom' AND '$dateto'
										$where_port_vehicle
									GROUP BY
										BV.fare,
										VC.NAME,
										VC.ID
									ORDER BY
										id ASC) SUB ON SUB.id = VCC.id
					WHERE VCC.status = 1 ORDER BY idku ASC";

			if ($this->dbView->query($sql)->num_rows() > 0) {
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

	public function get_lintasan($port, $datefrom, $dateto, $ship_class)
	{
		$where_port = "";
		$where_ship_class = "";

		if ($port != "") {
			$where_port = " AND BO.origin = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		$sql = "SELECT
					DISTINCT (BO.origin),
					ORI.name as port_origin,
					DE.name as port_destination
				FROM
					app.t_trx_booking BO
					JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
					JOIN app.t_mtr_port ORI ON ORI.id = BO.origin
					JOIN app.t_mtr_port DE ON DE.id = BO.destination
				WHERE
					BO.channel IN ( 'web_admin' ) 
					AND BO.depart_date BETWEEN '$datefrom' AND '$dateto' 
					$where_port
					$where_ship_class";

		if ($this->dbView->query($sql)->num_rows() == 1) {
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