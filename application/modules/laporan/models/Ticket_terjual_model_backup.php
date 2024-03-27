<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ticket_terjual_model extends CI_Model
{

	public $variable;

	public function __construct()
	{
		parent::__construct();
	}

	public function list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, $type, $vm = "", $merchant = "")
	{
		$where_port_passanger = "";
		$where_port_vehicle = "";
		$where_regu = "";
		$where_petugas = "";
		$where_shift = "";
		$where_ship_class = "";
		$where_vm = "";
		$where_merchant = "";

		if ($port != "") {
			$where_port_passanger = " AND BP.origin = $port";
			$where_port_vehicle = " AND BV.origin = $port";
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

		if ($merchant != "") {
			$where_merchant = " AND (UPPER(BO.created_by) = UPPER('" . $merchant . "'))";
		}

		if ($type === 'tunai') {
			// FILTER SEMUA
			$sql = "SELECT
							PTT.id as idku,
							PTT.name as golongan,
							SUB.*
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN (SELECT
											BP.fare AS harga,
											PT.ID,
											COUNT ( BP.ticket_number ) AS produksi,
											COUNT ( BP.ticket_number ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type = 'cash'
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
						WHERE PTT.id IN(1,2,3)

						UNION ALL

						SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.*
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN (SELECT
											BV.fare AS harga,
											VC.ID,
											COUNT ( BV.ticket_number ) AS produksi,
											COUNT ( BV.ticket_number ) * BV.fare AS pendapatan
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type = 'cash'
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
						WHERE VCC.status = 1 ORDER BY idku ASC";

			// FILTER PETUGAS ISI
			if ($petugas != "") {
				$sql = "SELECT
							PTT.id as idku,
							PTT.name as golongan,
							SUB.*
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN (SELECT
											BP.fare AS harga,
											PT.ID,
											COUNT ( BP.ticket_number ) AS produksi,
											COUNT ( BP.ticket_number ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type = 'cash'
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
						WHERE PTT.id IN(1,2,3)

						UNION ALL

						SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.*
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN (SELECT
											BV.fare AS harga,
											VC.ID,
											COUNT ( BV.ticket_number ) AS produksi,
											COUNT ( BV.ticket_number ) * BV.fare AS pendapatan
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type = 'cash'
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
						WHERE VCC.status = 1 ORDER BY idku ASC";
			}

			// KETIKA FILTER VM ADA DATA
			if ($vm != "") {
				$sql = "SELECT
							PTT.id as idku,
							PTT.name as golongan,
							SUB.*
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN (SELECT
											BP.fare AS harga,
											PT.ID,
											COUNT ( BP.ticket_number ) AS produksi,
											COUNT ( BP.ticket_number ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type = 'cash'
											JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
						WHERE PTT.id IN(1,2,3)

						UNION ALL

						SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.*
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN (SELECT
											BV.fare AS harga,
											VC.ID,
											COUNT ( BV.ticket_number ) AS produksi,
											COUNT ( BV.ticket_number ) * BV.fare AS pendapatan
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type = 'cash'
											JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
						WHERE VCC.status = 1 ORDER BY idku ASC";
			}
			// die($sql);

			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		} elseif ($type === 'cashless') {
			if ($petugas != "" && $vm == "") {
				$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											BP.fare AS harga,
											PT.ID,
											COUNT ( BP.ticket_number ) AS produksi,
											COUNT ( BP.ticket_number ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
							idku,
							golongan,
							harga

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											BV.fare AS harga,
											VC.ID,
											COUNT ( BV.ticket_number ) AS produksi,
											COUNT ( BV.ticket_number ) * BV.fare AS pendapatan
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
						WHERE VCC.status = 1 ORDER BY idku ASC";
			} elseif ($petugas == "" && $vm != "") {
				// FILTER VM ONLY
				$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
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
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
							idku,
							golongan,
							harga

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											BV.fare AS harga,
											VC.ID,
											COUNT ( BV.ticket_number ) AS produksi,
											COUNT ( BV.ticket_number ) * BV.fare AS pendapatan
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
						WHERE VCC.status = 1 ORDER BY idku ASC";
			} elseif ($petugas != "" && $vm != "") {
				// FILTER 22 NYA, YANG DIAMBIL VM AJA
				$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
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
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
							idku,
							golongan,
							harga

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											BV.fare AS harga,
											VC.ID,
											COUNT ( BV.ticket_number ) AS produksi,
											COUNT ( BV.ticket_number ) * BV.fare AS pendapatan
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
						WHERE VCC.status = 1 ORDER BY idku ASC";
			} elseif ($petugas == "" && $vm == "") {
				// FILTER KOSONG SEMUA, DIAMBIL 22 NYA
				$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											BP.fare AS harga,
											PT.ID,
											COUNT ( BP.ticket_number ) AS produksi,
											COUNT ( BP.ticket_number ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID

										UNION ALL

										SELECT
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
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
							idku,
							golongan,
							harga

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											BV.fare AS harga,
											VC.ID,
											COUNT ( BV.ticket_number ) AS produksi,
											COUNT ( BV.ticket_number ) * BV.fare AS pendapatan
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
						WHERE VCC.status = 1 ORDER BY idku ASC";
			}

			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		} elseif ($type === 'online') {
			$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											DISTINCT(BP.fare) AS harga,
											PT.ID,
											COUNT ( DISTINCT(BP.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BP.ticket_number) ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code AND BO.channel IN ( 'web', 'mobile' ) $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID

										UNION ALL

										SELECT
											DISTINCT(BP.fare) AS harga,
											PT.ID,
											COUNT ( DISTINCT(BP.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BP.ticket_number) ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code AND BO.channel IN ( 'web', 'mobile' ) $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
						SUB.harga,
						PTT.id

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											DISTINCT(BV.fare) AS harga,
											VC.ID,
											COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'web', 'mobile' ) $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
					WHERE VCC.status = 1 ORDER BY idku ASC";

			// filter petugas saja
			if ($petugas != "" && $vm == "") {
				$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											DISTINCT(BP.fare) AS harga,
											PT.ID,
											COUNT ( DISTINCT(BP.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BP.ticket_number) ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code AND BO.channel IN ( 'web', 'mobile' ) $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
						SUB.harga,
						PTT.id

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											DISTINCT(BV.fare) AS harga,
											VC.ID,
											COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'web', 'mobile' ) $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
					WHERE VCC.status = 1 ORDER BY idku ASC";
			}

			// filter petugas & vm, diambil vm saja
			if ($petugas != "" && $vm != "") {
				$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											DISTINCT(BP.fare) AS harga,
											PT.ID,
											COUNT ( DISTINCT(BP.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BP.ticket_number) ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code AND BO.channel IN ( 'web', 'mobile' ) $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
						SUB.harga,
						PTT.id

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											DISTINCT(BV.fare) AS harga,
											VC.ID,
											COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'web', 'mobile' ) $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
					WHERE VCC.status = 1 ORDER BY idku ASC";
			}

			// filter vm saja
			if ($vm != "") {
				$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											DISTINCT(BP.fare) AS harga,
											PT.ID,
											COUNT ( DISTINCT(BP.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BP.ticket_number) ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code AND BO.channel IN ( 'web', 'mobile' ) $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
						SUB.harga,
						PTT.id

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											DISTINCT(BV.fare) AS harga,
											VC.ID,
											COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'web', 'mobile' ) $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
					WHERE VCC.status = 1 ORDER BY idku ASC";
			}
			// die($sql);

			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		} elseif ($type === 'ifcs') {
			$sql = "SELECT
						VCC.id as idku,
						VCC.name as golongan,
						SUB.harga,
						SUM(SUB.produksi) AS produksi,
						SUM(SUB.pendapatan) AS pendapatan
					FROM
						app.t_mtr_vehicle_class VCC
						LEFT JOIN 	(SELECT
										DISTINCT(BV.fare) AS harga,
										VC.ID,
										COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
										COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
									FROM
										app.t_trx_booking_vehicle BV
										JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'ifcs' ) $where_ship_class
										JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type NOT IN ('reedem')
										JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
										JOIN app.t_trx_opening_balance OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
										JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
										JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
									WHERE
										BV.status NOT IN (-6)
										$where_port_vehicle
									GROUP BY
										BV.fare,
										VC.NAME,
										VC.ID) SUB ON SUB.id = VCC.id
				WHERE VCC.status = 1
				GROUP BY
						VCC.id,
						golongan,
						SUB.harga
				ORDER BY
						idku ASC";

			if ($vm != "") {
				$sql = "SELECT
						VCC.id as idku,
						VCC.name as golongan,
						SUB.harga,
						SUM(SUB.produksi) AS produksi,
						SUM(SUB.pendapatan) AS pendapatan
					FROM
						app.t_mtr_vehicle_class VCC
						LEFT JOIN 	(SELECT
										DISTINCT(BV.fare) AS harga,
										VC.ID,
										COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
										COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
									FROM
										app.t_trx_booking_vehicle BV
										JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'ifcs' ) $where_ship_class
										JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type NOT IN ('reedem')
										JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
										JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_vm
										JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
										JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
									WHERE
										BV.status NOT IN (-6)
										$where_port_vehicle
									GROUP BY
										BV.fare,
										VC.NAME,
										VC.ID) SUB ON SUB.id = VCC.id
				WHERE VCC.status = 1
				GROUP BY
						VCC.id,
						golongan,
						SUB.harga
				ORDER BY
						idku ASC";
			}

			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		} elseif ($type === 'ifcs_reedem') {
			$sql = "SELECT
						VCC.id as idku,
						VCC.name as golongan,
						SUB.harga,
						SUM(SUB.produksi) AS produksi,
						SUM(SUB.pendapatan) AS pendapatan
					FROM
						app.t_mtr_vehicle_class VCC
						LEFT JOIN 	(SELECT
										DISTINCT(BV.fare) AS harga,
										VC.ID,
										COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
										COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
									FROM
										app.t_trx_booking_vehicle BV
										JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'ifcs' ) $where_ship_class
										JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('reedem')
										JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
										JOIN app.t_trx_opening_balance OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
										JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
										JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
									WHERE
										BV.status NOT IN (-6)
										$where_port_vehicle
									GROUP BY
										BV.fare,
										VC.NAME,
										VC.ID) SUB ON SUB.id = VCC.id
				WHERE VCC.status = 1
				GROUP BY
						VCC.id,
						golongan,
						SUB.harga
				ORDER BY
						idku ASC";

			if ($vm != "") {
				$sql = "SELECT
						VCC.id as idku,
						VCC.name as golongan,
						SUB.harga,
						SUM(SUB.produksi) AS produksi,
						SUM(SUB.pendapatan) AS pendapatan
					FROM
						app.t_mtr_vehicle_class VCC
						LEFT JOIN 	(SELECT
										DISTINCT(BV.fare) AS harga,
										VC.ID,
										COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
										COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
									FROM
										app.t_trx_booking_vehicle BV
										JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'ifcs' ) $where_ship_class
										JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('reedem')
										JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
										JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_vm
										JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
										JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
									WHERE
										BV.status NOT IN (-6)
										$where_port_vehicle
									GROUP BY
										BV.fare,
										VC.NAME,
										VC.ID) SUB ON SUB.id = VCC.id
				WHERE VCC.status = 1
				GROUP BY
						VCC.id,
						golongan,
						SUB.harga
				ORDER BY
						idku ASC";
			}

			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		} elseif ($type === 'b2b') {
			$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											DISTINCT(BP.fare) AS harga,
											PT.ID,
											COUNT ( DISTINCT(BP.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BP.ticket_number) ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code AND BO.channel IN ( 'b2b' ) $where_ship_class $where_merchant
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID

										UNION ALL

										SELECT
											DISTINCT(BP.fare) AS harga,
											PT.ID,
											COUNT ( DISTINCT(BP.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BP.ticket_number) ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code AND BO.channel IN ( 'b2b' ) $where_ship_class $where_merchant
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
						SUB.harga,
						PTT.id

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											DISTINCT(BV.fare) AS harga,
											VC.ID,
											COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'b2b' ) $where_ship_class $where_merchant
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
					WHERE VCC.status = 1 ORDER BY idku ASC";

			// filter petugas saja
			if ($petugas != "" && $vm == "") {
				$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											DISTINCT(BP.fare) AS harga,
											PT.ID,
											COUNT ( DISTINCT(BP.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BP.ticket_number) ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code AND BO.channel IN ( 'b2b' ) $where_ship_class $where_merchant
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
						SUB.harga,
						PTT.id

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											DISTINCT(BV.fare) AS harga,
											VC.ID,
											COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'b2b' ) $where_ship_class $where_merchant
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
					WHERE VCC.status = 1 ORDER BY idku ASC";
			}

			// filter petugas & vm, diambil vm saja
			if ($petugas != "" && $vm != "") {
				$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											DISTINCT(BP.fare) AS harga,
											PT.ID,
											COUNT ( DISTINCT(BP.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BP.ticket_number) ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code AND BO.channel IN ( 'b2b' ) $where_ship_class $where_merchant
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
						SUB.harga,
						PTT.id

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											DISTINCT(BV.fare) AS harga,
											VC.ID,
											COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'b2b' ) $where_ship_class $where_merchant
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
					WHERE VCC.status = 1 ORDER BY idku ASC";
			}

			// filter vm saja
			if ($vm != "") {
				$sql = "SELECT
							DISTINCT(PTT.id) as idku,
							PTT.name as golongan,
							SUB.harga,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											DISTINCT(BP.fare) AS harga,
											PT.ID,
											COUNT ( DISTINCT(BP.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BP.ticket_number) ) * BP.fare AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code AND BO.channel IN ( 'b2b' ) $where_ship_class $where_merchant
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.fare,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)
					GROUP BY
						SUB.harga,
						PTT.id

					UNION ALL
						
					SELECT
							VCC.id as idku,
							VCC.name as golongan,
							SUB.harga,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											DISTINCT(BV.fare) AS harga,
											VC.ID,
											COUNT ( DISTINCT(BV.ticket_number) ) AS produksi,
											COUNT ( DISTINCT(BV.ticket_number) ) * BV.fare AS pendapatan 
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code AND BO.channel IN ( 'b2b' ) $where_ship_class $where_merchant
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
											JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = CI.created_by AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_vm $where_shift
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.fare,
											VC.NAME,
											VC.ID
										ORDER BY
											id ASC) SUB ON SUB.id = VCC.id
					WHERE VCC.status = 1 ORDER BY idku ASC";
			}
			// die($sql);

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

	public function get_lintasan($port, $datefrom, $dateto, $ship_class)
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

	public function get_merchant()
	{
		$data = $this->dbView->query("SELECT DISTINCT merchant_name, merchant_id FROM app.t_mtr_merchant WHERE status = 1");
		return $data->result();
	}
}
