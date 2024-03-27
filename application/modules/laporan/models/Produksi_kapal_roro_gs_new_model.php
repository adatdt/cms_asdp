<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Produksi_kapal_roro_gs_model
 * -----------------------
 *
 * @author     Arif Rudianto
 * @copyright  2019
 *
 */

class Produksi_kapal_roro_gs_new_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'laporan/produksi_kapal_roro_gs';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		// $dateTo = trim($this->input->post('dateTo'));
		// $dateFrom = trim($this->input->post('dateFrom'));
		// $port = $this->enc->decode($this->input->post('port'));
		// $dock = $this->enc->decode($this->input->post('dock'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));


		$field = array(
			1 => 'ship',
			2 => 'company',
			3 => 'ship_grt',
			4 => 'docking_date',
			5 => 'open_boarding_date',
			6 => 'duration',
			7 => 'dermaga',
			8 => 'call',
			9 => 'dewasa',
			10 => 'anak',
			11 => 'totalP',
			12 => 'gol1',
			13 => 'gol2',
			14 => 'gol3',
			15 => 'gol4A',
			16 => 'gol4B',
			17 => 'gol5A',
			18 => 'gol5B',
			19 => 'gol6A',
			20 => 'gol6B',
			21 => 'gol7',
			22 => 'gol8',
			23 => 'gol9',
			24 => 'totalK',
		);

		$order_column = $field[$order_column];
		$where = " where SH.status != -5";

		// if ((!empty($dateFrom) and empty($dateTo))||(empty($dateFrom) and !empty($dateTo))){
		// 	$where .=" and shift_date ='$dateFrom' or shift_date ='$dateTo'";
		// }

		// if(!empty($dateFrom) and !empty($dateTo)){
		// 	$where .=" and shift_date between '$dateFrom' and  '$dateTo'";
		// }

		// if(!empty($port)){
		// 	$where .=" and (ttob.port_id=".$port.")";
		// }

		// if (!empty($search['value'])){
		// 	$where .=" and (tmsh.name ilike '%".$iLike."%' or tmp.name ilike '%".$iLike."%' or tmd.name ilike '%".$iLike."%')";	
		// }

		$sql = "SELECT SH.schedule_code, S.name as ship, SC.name as company, ship_grt, docking_date, open_boarding_date, (open_boarding_date - docking_date) as duration, D.name as dermaga, SH.call
						-- (SELECT count(BP.id)
						-- FROM app.t_trx_booking_passanger BP
						-- JOIN app.t_trx_boarding_passanger BRP ON BP.ticket_number = BRP.ticket_number
						-- JOIN app.t_trx_open_boarding OB ON OB.boarding_code = BRP.boarding_code
						-- WHERE BP.service_id = 2 AND BP.passanger_type_id = 1 AND OB.schedule_code = SH.schedule_code) AS dewasa,
						-- (SELECT count(BP.id)
						-- FROM app.t_trx_booking_passanger BP
						-- JOIN app.t_trx_boarding_passanger BRP ON BP.ticket_number = BRP.ticket_number
						-- JOIN app.t_trx_open_boarding OB ON OB.boarding_code = BRP.boarding_code
						-- WHERE BP.service_id = 2 AND BP.passanger_type_id = 2 AND OB.schedule_code = SH.schedule_code) AS anak,
						-- (SELECT count(BP.id)
						-- FROM app.t_trx_booking_passanger BP
						-- JOIN app.t_trx_boarding_passanger BRP ON BP.ticket_number = BRP.ticket_number
						-- JOIN app.t_trx_open_boarding OB ON OB.boarding_code = BRP.boarding_code
						-- WHERE BP.service_id = 2 AND OB.schedule_code = SH.schedule_code) AS penumpang
				FROM app.t_trx_schedule SH
				JOIN app.t_mtr_dock D on D.id = SH.dock_id
				JOIN app.t_mtr_ship S on S.id = SH.ship_id
				JOIN app.t_mtr_ship_company SC ON SC.id = S.ship_company_id
				{$where}
				";

		$query          = $this->dbView->query($sql);
		$records_total  = $query->num_rows();

		$sql .=" order by " . $order_column . " {$order_dir}";

		if($length != -1){
			$sql .=" limit {$length} offset {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			//pejalan kaki
			$sql2 = "select
					passanger_type.name as passanger_type_name,
					trx_passenger.passanger_type_id as passanger_id,
					coalesce(trip_fee, 0) as trip_fee,
					coalesce(adm_fee, 0) as adm_fee,
					coalesce(ticket_count, 0) as ticket_count,
					coalesce(total_amount, 0) as total_amount
				from
					app.t_mtr_passanger_type passanger_type
					left join (
						select
						ttbp.passanger_type_id,
						count (distinct ttbp.id) as ticket_count,
						ttbp.trip_fee,
						sum(ttbp.adm_fee) as adm_fee,
						count (distinct ttbp.id) * ttbp.trip_fee as total_amount
						from
						app.t_trx_booking ttb
						join app.t_trx_booking_passanger ttbp on ttbp.booking_code = ttb.booking_code and ttbp.status = 5
						and ttb.service_id = 1
						join app.t_trx_boarding_passanger ttop on ttop.ticket_number = ttbp.ticket_number
						join app.t_trx_open_boarding ttob on ttob.boarding_code = ttop.boarding_code
						and ttob.status = 0
						where
						ttob.schedule_code = '{$row->schedule_code}'
						group by
						ttbp.passanger_type_id,
						ttbp.trip_fee,
						ttbp.adm_fee
					) trx_passenger on passanger_type.id = trx_passenger.passanger_type_id where passanger_type.id in (1, 2)";

			$result = $this->dbView->query($sql2);
			$penumpangs = $result->result();
			$row->dewasa = $row->anak = 0;
			foreach ($penumpangs as $penumpang) {
				if ($penumpang->passanger_id == 1) {
					$row->dewasa += $penumpang->ticket_count;
					// $amountD = $penumpang->ticket_amount;
				} else if ($penumpang->passanger_id == 2) {
					$row->anak += $penumpang->ticket_count;
					// $amountA = $penumpang->ticket_amount;
				}
			}
			$row->totalP = $row->dewasa + $row->anak;

			//kendaraan
			$sql3 = "select
					vehicle_type.name as vehicle_type_name,
					trx_vehicle.vehicle_class_id as vehicle_id,
					coalesce(trip_fee, 0) as trip_fee,
					coalesce(adm_fee, 0) as adm_fee,
					coalesce(ticket_count, 0) as ticket_count,
					coalesce(total_amount, 0) as total_amount
				from
					app.t_mtr_vehicle_class vehicle_type
					left join (
						select
						ttbv.vehicle_class_id,
						count (distinct ttbv.id) as ticket_count,
						ttbv.trip_fee,
						sum(ttbv.adm_fee) as adm_fee,
						count (distinct ttbv.id) * ttbv.trip_fee as total_amount
						from
						app.t_trx_booking ttb
						join app.t_trx_booking_vehicle ttbv on ttbv.booking_code = ttb.booking_code and ttbv.status = 5
						and ttb.service_id = 2
						join app.t_trx_boarding_vehicle ttop on ttop.ticket_number = ttbv.ticket_number
						join app.t_trx_open_boarding ttob on ttob.boarding_code = ttop.boarding_code
						and ttob.status = 0
						where
						ttob.schedule_code = '{$row->schedule_code}'
						group by
						ttbv.vehicle_class_id,
						ttbv.trip_fee,
						ttbv.adm_fee
					) trx_vehicle on vehicle_type.id = trx_vehicle.vehicle_class_id where vehicle_type.status = 1 order by vehicle_type.id asc";

			$result = $this->dbView->query($sql3);
			$kendaraans = $result->result();
			$row->gol1 = $row->gol2 = $row->gol3 = $row->gol4A = $row->gol4B = $row->gol5A = $row->gol5B = $row->gol6A = $row->gol6B = $row->gol7 = $row->gol8 = $row->gol9 = 0;
			foreach ($kendaraans as $kendaraan) {
				switch ($kendaraan->vehicle_id) {
					case 4:
						$row->gol1 += $kendaraan->ticket_count;
						break;
					case 7:
						$row->gol2 += $kendaraan->ticket_count;
						break;
					case 8:
						$row->gol3 += $kendaraan->ticket_count;
						break;
					case 9:
						$row->gol4A += $kendaraan->ticket_count;
						break;
					case 10:
						$row->gol4B += $kendaraan->ticket_count;
						break;
					case 11:
						$row->gol5A += $kendaraan->ticket_count;
						break;
					case 12:
						$row->gol5B += $kendaraan->ticket_count;
						break;
					case 13:
						$row->gol6A += $kendaraan->ticket_count;
						break;
					case 14:
						$row->gol6B += $kendaraan->ticket_count;
						break;
					case 15:
						$row->gol7 += $kendaraan->ticket_count;
						break;
					case 16:
						$row->gol8 += $kendaraan->ticket_count;
						break;
					case 17:
						$row->gol9 += $kendaraan->ticket_count;
						break;
					default:
						break;
				}
			}
			$row->totalK = $row->gol1 + $row->gol2 + $row->gol3 + $row->gol4A + $row->gol4B + $row->gol5A + $row->gol5B + $row->gol6A + $row->gol6B + $row->gol7 + $row->gol8 + $row->gol9;

			$code 			= $this->enc->encode($row->schedule_code);
			$detail_url 	= site_url($this->_module."/detail?id={$code}");
			$pdf_url 		= site_url($this->_module."/download_pdf?id={$code}");
			$excel_url 		= site_url($this->_module."/download_excel?id={$code}");

			$row->actions 	= "";
			$row->actions 	.= generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_pdf', $pdf_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_excel', $excel_url);
			$row->docking_date = date('d-m-Y H:i:s', strtotime($row->docking_date));
			$row->open_boarding_date = date('d-m-Y H:i:s', strtotime($row->open_boarding_date));
			$row->no=$i;

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	public function dock_fare($where=""){

		return $this->dbView->query("select (schedule.call * schedule.ship_grt * schedule.dock_fare) as dock_service
			from app.t_trx_schedule schedule
			{$where}");
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

	public function select_data($table, $where="")
	{
		return $this->dbView->query("select * from $table $where");
	}

	public function get_shift_time($shift_id)
	{
		if ($shift_id != "") {
			$sql = $this->dbView->query("SELECT shift_login,shift_logout FROM app.t_mtr_shift WHERE id = $shift_id");

			if ($sql->num_rows() > 0) {
				return $sql->row();
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function get_regu($shift_id, $port_id){
		if ($shift_id != "") {
			$sql = $this->dbView->query("SELECT DISTINCT team_name
								FROM app.t_trx_assignment_regu AR
								JOIN core.t_mtr_team T ON T.team_code = AR.team_code
								WHERE AR.shift_id =  $shift_id AND T.port_id = $port_id 
								ORDER BY team_name ASC");
			if ($sql->num_rows() > 0) {
				return $sql->result();
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function get_all_data($datefrom, $dateto, $port, $shift)
	{
		$where_port = "";
		$where_port_sub = "";
		// $where_ship_class = "";
		$where_shift = "";
		$where_shift_sub = "";

		if ($port != "") {
			$where_port = " AND SH.port_id = $port";
			$where_port2 = " AND ttob.port_id = $port";
		}

		if ($shift != "") {
			$where_shift = " AND OB.shift_id = $shift";
			$where_shift2 = " AND ttob.shift_id = $shift";
		}

		
		$sql = "SELECT
					SH.schedule_code,
					S.NAME AS ship,
					SC.NAME AS company,
					COALESCE ( ship_grt, 0 ) AS ship_grt,
					docking_date,
					sail_date,
					( sail_date - docking_date ) AS duration,
					D.NAME AS dermaga,
					COALESCE ( SH.CALL, 0 ) AS CALL 
				FROM
					app.t_trx_schedule SH
					JOIN app.t_trx_open_boarding OB ON OB.schedule_code = SH.schedule_code
					JOIN app.t_mtr_dock D ON D.ID = SH.dock_id
					JOIN app.t_mtr_ship S ON S.ID = SH.ship_id
					JOIN app.t_mtr_ship_company SC ON SC.ID = S.ship_company_id 				
					WHERE OB.status = 0
				AND OB.shift_date BETWEEN '$datefrom' AND '$dateto'
				$where_port
				$where_shift
				ORDER BY sail_date ASC
				";

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);
		$where_schedule_code = "";

		foreach ($rows_data as $key => $value) {
			$where_schedule_code .= "'".$value->schedule_code."',";
		}


		$kendaraans = null;
		$penumpangs = null;

		$where_schedule_code = rtrim($where_schedule_code, ", ");

		if ($rows_data) {
			// $sql2 = "SELECT
			// 			passanger_type.NAME AS passanger_type_name,
			// 			trx_passenger.passanger_type_id AS passanger_id,
			// 			COALESCE ( trip_fee, 0 ) AS trip_fee,
			// 			COALESCE ( adm_fee, 0 ) AS adm_fee,
			// 			COALESCE ( ticket_count, 0 ) AS ticket_count,
			// 			COALESCE ( total_amount, 0 ) AS total_amount,
			// 			schedule_code 
			// 		FROM
			// 			app.t_mtr_passanger_type passanger_type
			// 			LEFT JOIN (
			// 		SELECT
			// 			ttbp.passanger_type_id,
			// 			COUNT ( DISTINCT ttbp.ID ) AS ticket_count,
			// 			ttbp.trip_fee,
			// 			SUM ( ttbp.adm_fee ) AS adm_fee,
			// 			COUNT ( DISTINCT ttbp.ID ) * ttbp.trip_fee AS total_amount,
			// 			ttob.schedule_code 
			// 		FROM
			// 			app.t_trx_booking ttb
			// 			JOIN app.t_trx_booking_passanger ttbp ON ttbp.booking_code = ttb.booking_code 
			// 			AND ttbp.status = 5 
			// 			AND ttb.service_id = 1
			// 			JOIN app.t_trx_boarding_passanger ttop ON ttop.ticket_number = ttbp.ticket_number
			// 			JOIN app.t_trx_open_boarding ttob ON ttob.boarding_code = ttop.boarding_code 
			// 			AND ttob.status = 0 
			// 		WHERE
			// 			ttob.schedule_code in (".$where_schedule_code.")
			// 			GROUP BY
			// 			ttbp.passanger_type_id,
			// 			ttbp.trip_fee,
			// 			ttbp.adm_fee
			// 			,ttob.schedule_code
			// 		) trx_passenger on passanger_type.id = trx_passenger.passanger_type_id where passanger_type.id in (1, 2)";
			$sql2 = "SELECT
						passanger_type.NAME AS passanger_type_name,
						trx_passenger.passanger_type_id AS passanger_id,
						COALESCE ( trip_fee, 0 ) AS trip_fee,
						COALESCE ( adm_fee, 0 ) AS adm_fee,
						COALESCE ( ticket_count, 0 ) AS ticket_count,
						COALESCE ( total_amount, 0 ) AS total_amount,
						schedule_code 
					FROM
						app.t_mtr_passanger_type passanger_type
						LEFT JOIN (
					SELECT
						ttbp.passanger_type_id,
						COUNT ( DISTINCT ttbp.ID ) AS ticket_count,
						ttbp.trip_fee,
						SUM ( ttbp.adm_fee ) AS adm_fee,
						COUNT ( DISTINCT ttbp.ID ) * ttbp.trip_fee AS total_amount,
						ttob.schedule_code 
					FROM
						app.t_trx_booking ttb
						JOIN app.t_trx_booking_passanger ttbp ON ttbp.booking_code = ttb.booking_code 
						AND ttbp.status = 5 
						AND ttb.service_id = 1
						JOIN app.t_trx_boarding_passanger ttop ON ttop.ticket_number = ttbp.ticket_number
						JOIN app.t_trx_open_boarding ttob ON ttob.boarding_code = ttop.boarding_code 
						AND ttob.status = 0 
					WHERE
						ttob.shift_date BETWEEN '$datefrom' AND '$dateto'
						$where_port2
						$where_shift2 
						GROUP BY
						ttbp.passanger_type_id,
						ttbp.trip_fee,
						ttbp.adm_fee
						,ttob.schedule_code
					) trx_passenger on passanger_type.id = trx_passenger.passanger_type_id where passanger_type.id in (1, 2)";
			$result = $this->dbView->query($sql2);
			$penumpangs = $result->result();

			//kendaraan
			// $sql3 = "SELECT
			// 			vehicle_type.NAME AS vehicle_type_name,
			// 			trx_vehicle.vehicle_class_id AS vehicle_id,
			// 			COALESCE ( trip_fee, 0 ) AS trip_fee,
			// 			COALESCE ( adm_fee, 0 ) AS adm_fee,
			// 			COALESCE ( ticket_count, 0 ) AS ticket_count,
			// 			COALESCE ( total_amount, 0 ) AS total_amount,
			// 			schedule_code 
			// 		FROM
			// 			app.t_mtr_vehicle_class vehicle_type
			// 			LEFT JOIN (
			// 		SELECT
			// 			ttbv.vehicle_class_id,
			// 			COUNT ( DISTINCT ttbv.ID ) AS ticket_count,
			// 			ttbv.trip_fee,
			// 			SUM ( ttbv.adm_fee ) AS adm_fee,
			// 			COUNT ( DISTINCT ttbv.ID ) * ttbv.trip_fee AS total_amount,
			// 			ttob.schedule_code 
			// 		FROM
			// 			app.t_trx_booking ttb
			// 			JOIN app.t_trx_booking_vehicle ttbv ON ttbv.booking_code = ttb.booking_code 
			// 			AND ttbv.status = 5 
			// 			AND ttb.service_id = 2
			// 			JOIN app.t_trx_boarding_vehicle ttop ON ttop.ticket_number = ttbv.ticket_number
			// 			JOIN app.t_trx_open_boarding ttob ON ttob.boarding_code = ttop.boarding_code 
			// 			AND ttob.status = 0 
			// 		WHERE
			// 			ttob.schedule_code in (".$where_schedule_code.")
			// 			group by
			// 			ttbv.vehicle_class_id,
			// 			ttbv.trip_fee,
			// 			ttbv.adm_fee
			// 			,ttob.schedule_code
			// 		) trx_vehicle on vehicle_type.id = trx_vehicle.vehicle_class_id where vehicle_type.status = 1 order by vehicle_type.id asc";

			$sql3 = "SELECT
						vehicle_type.NAME AS vehicle_type_name,
						trx_vehicle.vehicle_class_id AS vehicle_id,
						COALESCE ( trip_fee, 0 ) AS trip_fee,
						COALESCE ( adm_fee, 0 ) AS adm_fee,
						COALESCE ( ticket_count, 0 ) AS ticket_count,
						COALESCE ( total_amount, 0 ) AS total_amount,
						schedule_code 
					FROM
						app.t_mtr_vehicle_class vehicle_type
						LEFT JOIN (
					SELECT
						ttbv.vehicle_class_id,
						COUNT ( DISTINCT ttbv.ID ) AS ticket_count,
						ttbv.trip_fee,
						SUM ( ttbv.adm_fee ) AS adm_fee,
						COUNT ( DISTINCT ttbv.ID ) * ttbv.trip_fee AS total_amount,
						ttob.schedule_code 
					FROM
						app.t_trx_booking ttb
						JOIN app.t_trx_booking_vehicle ttbv ON ttbv.booking_code = ttb.booking_code 
						AND ttbv.status = 5 
						AND ttb.service_id = 2
						JOIN app.t_trx_boarding_vehicle ttop ON ttop.ticket_number = ttbv.ticket_number
						JOIN app.t_trx_open_boarding ttob ON ttob.boarding_code = ttop.boarding_code 
						AND ttob.status = 0 
					WHERE 
						ttob.shift_date BETWEEN '$datefrom' AND '$dateto'
						$where_port2
						$where_shift2 
						group by
						ttbv.vehicle_class_id,
						ttbv.trip_fee,
						ttbv.adm_fee
						,ttob.schedule_code
					) trx_vehicle on vehicle_type.id = trx_vehicle.vehicle_class_id where vehicle_type.status = 1 order by vehicle_type.id asc";
			$result = $this->dbView->query($sql3);
			$kendaraans = $result->result();
		}

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->knd = 0;
			$row->dewasa = $row->anak = $row->amount = 0;
			$row->gol1 = $row->gol2 = $row->gol3 = $row->gol4A = $row->gol4B = $row->gol5A = $row->gol5B = $row->gol6A = $row->gol6B = $row->gol7 = $row->gol8 = $row->gol9 = 0;
			if (!$row->duration) {
				$row->duration = "-";
			}

			foreach ($penumpangs as $key_penumangs => $value_penumpangs) {
				
				if ($value_penumpangs->schedule_code == $row->schedule_code) {
					$row->amount += $value_penumpangs->total_amount;
					if ($value_penumpangs->passanger_id == 1) {
						$row->dewasa += $value_penumpangs->ticket_count;
					} else if ($value_penumpangs->passanger_id == 2) {
						$row->anak += $value_penumpangs->ticket_count;
					}
				}
				$row->totalP = $row->dewasa + $row->anak;
			}

			foreach ($kendaraans as $kendaraan => $value_kendaraan) {
				if ($value_kendaraan->schedule_code == $row->schedule_code) {
					$row->amount += $value_kendaraan->total_amount;
					
					switch ($value_kendaraan->vehicle_id) {
						case 4:
							$row->gol1 += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						case 7:
							$row->gol2 += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						case 8:
							$row->gol3 += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						case 9:
							$row->gol4A += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						case 10:
							$row->gol4B += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						case 11:
							$row->gol5A += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						case 12:
							$row->gol5B += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						case 13:
							$row->gol6A += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						case 14:
							$row->gol6B += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						case 15:
							$row->gol7 += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						case 16:
							$row->gol8 += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						case 17:
							$row->gol9 += $value_kendaraan->ticket_count;
							$row->knd += $value_kendaraan->ticket_count;
							break;
						default:
							break;
					}
				}
								
			}
			$row->totalK = $row->gol1 + $row->gol2 + $row->gol3 + $row->gol4A + $row->gol4B + $row->gol5A + $row->gol5B + $row->gol6A + $row->gol6B + $row->gol7 + $row->gol8 + $row->gol9;
			if ($row->sail_date) {
				$temp_sail         = $row->sail_date;
				$row->sail_date    = date('Y-m-d', strtotime($temp_sail));
				$row->sail_time    = date('H:i:s', strtotime($temp_sail));
			}
			else{
				$row->sail_date    = '-';
				$row->sail_time    = '';
			}
			if ($row->docking_date) {
				$temp_dock         = $row->docking_date;
				$row->docking_date = date('Y-m-d', strtotime($temp_dock));
				$row->docking_time = date('H:i:s', strtotime($temp_dock));
			}
			else{
				$row->docking_date = '-';
				$row->docking_time = '';
			}
			
			$row->dermaga      = substr($row->dermaga, 8);
			$row->no = $i;

			$rows[] = $row;
			unset($row->id);

			$i++;
		}
		return $rows;
	}

}
