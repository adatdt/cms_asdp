<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap_muatan_perkapal_new_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'laporan/rekap_muatan_perkapal';
	}

	public function get_passanger($port_id, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift)
	{
		$where_port = "";
		$where_ship_company = "";
		$where_ship_name = "";
		$where_ship_class = "";
		$where_dock_id = "";
		$where_shift = "";

		if ($port_id != "") {
			$where_port = " AND ttob.port_id = $port_id";
		}

		if ($ship_company != "") {
			$where_ship_company = " AND tsc.id = $ship_company";
		}

		if ($ship_name != "") {
			$where_ship_name = " AND ttob.ship_id = $ship_name";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND ttob.ship_class = $ship_class";
		}

		if ($dock_id != "") {
			$where_dock_id = " AND ttob.dock_id = $dock_id";
		}

		if ($shift != "") {
			$where_shift = " AND ttob.shift_id = $shift";
		}

		$sql = "SELECT
					passanger_type.NAME AS golongan,
					COALESCE ( trip_fee, 0 ) AS harga,
					COALESCE ( adm_fee, 0 ) AS adm_fee,
					COALESCE ( ticket_count, 0 ) AS produksi,
					COALESCE ( total_amount, 0 ) AS pendapatan
				FROM
					app.t_mtr_passanger_type passanger_type
					LEFT JOIN (
						SELECT
							ttbp.passanger_type_id,
							COUNT ( DISTINCT ttbp.ID ) AS ticket_count,
							ttbp.trip_fee,
							SUM ( ttbp.adm_fee ) AS adm_fee,
							COUNT ( DISTINCT ttbp.ID ) * ttbp.trip_fee AS total_amount 
						FROM
							app.t_trx_booking ttb
							JOIN app.t_trx_booking_passanger ttbp ON ttbp.booking_code = ttb.booking_code AND ttbp.status = 5 AND ttb.service_id = 1
							JOIN app.t_trx_boarding_passanger ttop ON ttop.ticket_number = ttbp.ticket_number
							JOIN app.t_trx_open_boarding ttob ON ttob.boarding_code = ttop.boarding_code AND ttob.status = 0 
							JOIN app.t_mtr_ship tmsh ON ttob.ship_id = tmsh.ID
							JOIN app.t_mtr_ship_company tsc ON tsc.id = tmsh.ship_company_id $where_ship_company
						WHERE 
						shift_date BETWEEN '$datefrom' 
						AND '$dateto'
						$where_port
						$where_ship_name
						$where_ship_class
						$where_dock_id
						$where_shift 
						GROUP BY
						ttbp.passanger_type_id,
						ttbp.trip_fee,
						ttbp.adm_fee) 
				trx_passenger ON passanger_type.ID = trx_passenger.passanger_type_id 
				WHERE
					passanger_type.ID IN (1,2)";
		

		if ($this->dbView->query($sql)->num_rows() > 0) {
			return $this->dbView->query($sql)->result();
		}		
	}

	public function get_vehicle($port_id, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift)
	{
		$where_port = "";
		$where_ship_company = "";
		$where_ship_name = "";
		$where_ship_class = "";
		$where_dock_id = "";
		$where_shift = "";

		if ($port_id != "") {
			$where_port = " AND ttob.port_id = $port_id";
		}

		if ($ship_company != "") {
			$where_ship_company = " AND tsc.id = $ship_company";
		}

		if ($ship_name != "") {
			$where_ship_name = " AND ttob.ship_id = $ship_name";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND ttob.ship_class = $ship_class";
		}

		if ($dock_id != "") {
			$where_dock_id = " AND ttob.dock_id = $dock_id";
		}

		if ($shift != "") {
			$where_shift = " AND ttob.shift_id = $shift";
		}

		$sql = "SELECT
					vehicle_type.NAME AS golongan,
					COALESCE ( trip_fee, 0 ) AS harga,
					COALESCE ( adm_fee, 0 ) AS adm_fee,
					COALESCE ( ticket_count, 0 ) AS produksi,
					COALESCE ( total_amount, 0 ) AS pendapatan 
				FROM
					app.t_mtr_vehicle_class vehicle_type
					LEFT JOIN (
				SELECT
					ttbv.vehicle_class_id,
					COUNT ( DISTINCT ttbv.ID ) AS ticket_count,
					ttbv.trip_fee,
					SUM ( ttbv.adm_fee ) AS adm_fee,
					COUNT ( DISTINCT ttbv.ID ) * ttbv.trip_fee AS total_amount 
				FROM
					app.t_trx_booking ttb
					JOIN app.t_trx_booking_vehicle ttbv ON ttbv.booking_code = ttb.booking_code AND ttbv.status = 5 AND ttb.service_id = 2
					JOIN app.t_trx_boarding_vehicle ttop ON ttop.ticket_number = ttbv.ticket_number
					JOIN app.t_trx_open_boarding ttob ON ttob.boarding_code = ttop.boarding_code 
					AND ttob.status = 0 
					JOIN app.t_mtr_ship tmsh ON ttob.ship_id = tmsh.ID
					JOIN app.t_mtr_ship_company tsc ON tsc.id = tmsh.ship_company_id $where_ship_company

					WHERE 
					shift_date BETWEEN '$datefrom' AND '$dateto'
					$where_port
					$where_ship_name
					$where_ship_class
					$where_dock_id
					$where_shift 	
				GROUP BY
					ttbv.vehicle_class_id,
					ttbv.trip_fee,
					ttbv.adm_fee 
					) trx_vehicle ON vehicle_type.ID = trx_vehicle.vehicle_class_id 
				WHERE
					vehicle_type.status = 1 
				ORDER BY
					vehicle_type.ID ASC";
		

		if ($this->dbView->query($sql)->num_rows() > 0) {
			return $this->dbView->query($sql)->result();
		}
	}

	public function jumlah_trip($port_id, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift)
	{
		$where_port = "";
		$where_ship_company = "";
		$where_ship_name = "";
		$where_ship_class = "";
		$where_dock_id = "";
		$where_shift = "";

		if ($port_id != "") {
			$where_port = " AND ttob.port_id = $port_id";
		}

		if ($ship_company != "") {
			$where_ship_company = " AND tsc.id = $ship_company";
		}

		if ($ship_name != "") {
			$where_ship_name = " AND ttob.ship_id = $ship_name";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND ttob.ship_class = $ship_class";
		}

		if ($dock_id != "") {
			$where_dock_id = " AND ttob.dock_id = $dock_id";
		}

		if ($shift != "") {
			$where_shift = " AND ttob.shift_id = $shift";
		}

		$sql = "SELECT
					COUNT(DISTINCT(ttob.schedule_code)) AS jml_trip
				FROM
					app.t_trx_open_boarding ttob
					JOIN app.t_trx_schedule ttsc ON ttsc.schedule_code = ttob.schedule_code
					JOIN app.t_mtr_port tmp ON ttob.port_id = tmp.ID
					JOIN app.t_mtr_dock tmd ON ttob.dock_id = tmd.ID
					JOIN app.t_mtr_ship tmsh ON ttob.ship_id = tmsh.ID
					JOIN app.t_mtr_ship_class tmsc ON ttob.ship_class = tmsc.ID
					JOIN app.t_mtr_ship_company tsc ON tsc.id = tmsh.ship_company_id $where_ship_company
					LEFT JOIN app.t_mtr_shift shift ON shift.ID = ttob.shift_id
				WHERE
					shift_date BETWEEN '$datefrom' AND '$dateto'
					AND ttsc.sail_date IS NOT NULL
					$where_port
					$where_ship_name
					$where_ship_class
					$where_dock_id
					$where_shift";

		$hasil = $this->dbView->query($sql)->row();
		return $hasil->jml_trip;
	}

	public function headerku($port_id, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift)
	{
		$where_port = "";
		$where_ship_company = "";
		$where_ship_name = "";
		$where_ship_class = "";
		$where_dock_id = "";
		$where_shift = "";

		if ($port_id != "") {
			$where_port = " AND ttob.port_id = $port_id";
		}

		if ($ship_company != "") {
			$where_ship_company = " AND tsc.id = $ship_company";
		}

		if ($ship_name != "") {
			$where_ship_name = " AND ttob.ship_id = $ship_name";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND ttob.ship_class = $ship_class";
		}

		if ($dock_id != "") {
			$where_dock_id = " AND ttob.dock_id = $dock_id";
		}

		if ($shift != "") {
			$where_shift = " AND ttob.shift_id = $shift";
		}

		$sql = "SELECT
					ttob.schedule_code,
					shift_date,
					shift_name,
					tsc.NAME AS company,
					tmsh.NAME AS ship_name,
					tmp.NAME AS port_name,
					tmsc.NAME AS ship_class_name,
					tmd.NAME AS dock_name
				FROM
					app.t_trx_open_boarding ttob
					JOIN app.t_mtr_port tmp ON ttob.port_id = tmp.ID
					JOIN app.t_mtr_dock tmd ON ttob.dock_id = tmd.ID
					JOIN app.t_mtr_ship tmsh ON ttob.ship_id = tmsh.ID
					JOIN app.t_mtr_ship_class tmsc ON ttob.ship_class = tmsc.ID
					JOIN app.t_mtr_ship_company tsc ON tsc.id = tmsh.ship_company_id $where_ship_company
					LEFT JOIN app.t_mtr_shift shift ON shift.ID = ttob.shift_id
				WHERE
					shift_date BETWEEN '$datefrom' AND '$dateto'
					$where_port
					$where_ship_name
					$where_ship_class
					$where_dock_id
					$where_shift";

		if ($this->dbView->query($sql)->num_rows() == 1) {
			return $this->dbView->query($sql)->row();
		}
	}

	public function get_trip($port_id, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift)
	{
		$where_port = "";
		$where_ship_company = "";
		$where_ship_name = "";
		$where_ship_class = "";
		$where_dock_id = "";
		$where_shift = "";

		if ($port_id != "") {
			$where_port = " AND ttob.port_id = $port_id";
		}

		if ($ship_company != "") {
			$where_ship_company = " AND tsc.id = $ship_company";
		}

		if ($ship_name != "") {
			$where_ship_name = " AND ttob.ship_id = $ship_name";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND ttob.ship_class = $ship_class";
		}

		if ($dock_id != "") {
			$where_dock_id = " AND ttob.dock_id = $dock_id";
		}

		if ($shift != "") {
			$where_shift = " AND ttob.shift_id = $shift";
		}

		$sql = "SELECT
					COUNT(0) as trip
				FROM
					app.t_trx_schedule schedule 
					JOIN app.t_trx_open_boarding ttob on ttob.schedule_code = schedule.schedule_code 
					JOIN app.t_mtr_ship_class tmsc ON ttob.ship_class = tmsc.ID 
					JOIN app.t_mtr_ship tmsh ON ttob.ship_id = tmsh.ID
					JOIN app.t_mtr_ship_company tsc ON tsc.id = tmsh.ship_company_id 
					$where_ship_company
				WHERE
				shift_date BETWEEN '$datefrom' AND '$dateto'
				$where_port
				$where_ship_name
				$where_ship_class
				$where_dock_id
				$where_shift";

		return $this->dbView->query($sql)->row()->trip;
	}

	public function dock_fare($port_id, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift)
	{
		$where_port = "";
		$where_ship_company = "";
		$where_ship_name = "";
		$where_ship_class = "";
		$where_dock_id = "";
		$where_shift = "";

		if ($port_id != "") {
			$where_port = " AND ttob.port_id = $port_id";
		}

		if ($ship_company != "") {
			$where_ship_company = " AND tsc.id = $ship_company";
		}

		if ($ship_name != "") {
			$where_ship_name = " AND ttob.ship_id = $ship_name";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND ttob.ship_class = $ship_class";
		}

		if ($dock_id != "") {
			$where_dock_id = " AND ttob.dock_id = $dock_id";
		}

		if ($shift != "") {
			$where_shift = " AND ttob.shift_id = $shift";
		}

		$sql = "SELECT
					SUM(( schedule.CALL * schedule.ship_grt * schedule.dock_fare )) AS dock_service 
				FROM
					app.t_trx_schedule schedule 
				WHERE
					schedule.schedule_code IN (SELECT
													ttob.schedule_code
												FROM
													app.t_trx_open_boarding ttob
													JOIN app.t_mtr_port tmp ON ttob.port_id = tmp.ID
													JOIN app.t_mtr_dock tmd ON ttob.dock_id = tmd.ID
													JOIN app.t_mtr_ship tmsh ON ttob.ship_id = tmsh.ID
													JOIN app.t_mtr_ship_class tmsc ON ttob.ship_class = tmsc.ID
													JOIN app.t_mtr_ship_company tsc ON tsc.id = tmsh.ship_company_id $where_ship_company
													LEFT JOIN app.t_mtr_shift shift ON shift.ID = ttob.shift_id
												WHERE
													shift_date BETWEEN '$datefrom' AND '$dateto'
													$where_port
													$where_ship_name
													$where_ship_class
													$where_dock_id
													$where_shift)";

		return $this->dbView->query($sql)->row()->dock_service;
	}

	public function get_kepil()
	{
		$sql = $this->dbView->query("SELECT param_value FROM app.t_mtr_custom_param WHERE param_name = 'jasa_kepil'")->row();
		return $sql->param_value;
	}

	public function adm_fee($where=""){

		return $this->dbView->query("select param_value from app.t_mtr_custom_param
			{$where}");
	}

	public function get_dock($port_id)
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_dock WHERE port_id = $port_id AND status = 1")->result();
	}

	public function get_ship($company_id)
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_ship WHERE ship_company_id = $company_id AND status = 1")->result();
	}

}