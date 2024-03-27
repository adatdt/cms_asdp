<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_rekonsiliasi extends CI_Model
{

	
	public function list_data($port, $datefrom, $dateto, $ship_class, $type)
	{
		$where_port_passanger = "";
		$where_port_vehicle = "";
		$where_ship_class = "";

		if ($port != "") {
			$where_port_passanger = " AND BP.origin = $port";
			$where_port_vehicle = " AND BV.origin = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		if ($type === 'tunai') {
			$sql = "SELECT
							-- PTT.id as idku,
							PTT.ordering as idku,
							PTT.name as golongan,
							SUB.*
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN (SELECT
											BP.entry_fee,
											PT.ID,
											COUNT ( BP.ticket_number ) AS produksi,
											COUNT ( BP.ticket_number ) * BP.entry_fee AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type = 'cash'
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto'
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
											-- LEFT JOIN app.t_mtr_entrance_fee_passanger EFP ON EFP.port_id = BP.origin AND EFP.passanger_type_id = BP.passanger_type_id AND EFP.status = 1
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.entry_fee,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
						WHERE PTT.id IN(1,2,3,4)

						UNION ALL

						SELECT
							VCC.id+10 as idku,
							VCC.name as golongan,
							SUB.*
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN (SELECT
											BV.entry_fee,
											VC.ID,
											COUNT ( BV.ticket_number ) AS produksi,
											COUNT ( BV.ticket_number ) * BV.entry_fee AS pendapatan
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type = 'cash'
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto'
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
											-- LEFT JOIN app.t_mtr_entrance_fee_vehicle EFV ON EFV.port_id = BV.origin AND EFV.vehicle_class_id = BV.vehicle_class_id AND EFV.status = 1
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.entry_fee,
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
		} elseif ($type === 'cashless') {
			$sql = "SELECT
							-- DISTINCT(PTT.id) as idku,
							DISTINCT(PTT.ordering) as idku,
							PTT.name as golongan,
							SUB.entry_fee,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											BP.entry_fee,
											PT.ID,
											COUNT ( BP.ticket_number ) AS produksi,
											COUNT ( BP.ticket_number ) * BP.entry_fee AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
											-- LEFT JOIN app.t_mtr_entrance_fee_passanger EFP ON EFP.port_id = BP.origin AND EFP.passanger_type_id = BP.passanger_type_id AND EFP.status = 1
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.entry_fee,
											PT.NAME,
											PT.ID

										UNION ALL

										SELECT
											BP.entry_fee,
											PT.ID,
											COUNT ( BP.ticket_number ) AS produksi,
											COUNT ( BP.ticket_number ) * BP.entry_fee AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto'
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
											-- LEFT JOIN app.t_mtr_entrance_fee_passanger EFP ON EFP.port_id = BP.origin AND EFP.passanger_type_id = BP.passanger_type_id AND EFP.status = 1
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											BP.entry_fee,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3,4)
					GROUP BY
							idku,
							golongan,
							entry_fee

					UNION ALL
						
					SELECT
							VCC.id+10 as idku,
							VCC.name as golongan,
							SUB.entry_fee,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											BV.entry_fee,
											VC.ID,
											COUNT ( BV.ticket_number ) AS produksi,
											COUNT ( BV.ticket_number ) * BV.entry_fee AS pendapatan
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto'
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
											-- LEFT JOIN app.t_mtr_entrance_fee_vehicle EFV ON EFV.port_id = BV.origin AND EFV.vehicle_class_id = BV.vehicle_class_id AND EFV.status = 1
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											BV.entry_fee,
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
		} elseif ($type === 'online') {
			$sql = "SELECT 
					-- PTT.id,
					PTT.ordering as id,
					PTT.name AS golongan, SUB.* FROM app.t_mtr_passanger_type PTT LEFT JOIN(
					SELECT
						PT.id AS sub_id,
						COALESCE(BP.entry_fee,0) AS entrance_fee,
						COUNT(BP.id) AS produksi,
						COALESCE(COUNT(BP.id) * entry_fee,0) AS pendapatan
					FROM
						app.t_trx_payment PP
						JOIN app.t_trx_invoice INV ON INV.trans_number = PP.trans_number
						JOIN app.t_trx_booking BO ON BO.trans_number = PP.trans_number $where_ship_class
						JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
						JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
						-- LEFT JOIN app.t_mtr_entrance_fee_passanger EFP ON EFP.port_id = BP.origin AND EFP.passanger_type_id = BP.passanger_type_id AND EFP.status = 1
					WHERE
						PP.created_on::date BETWEEN '$datefrom' AND '$dateto'
						AND BO.channel IN ( 'web', 'mobile' )
						$where_port_passanger
					GROUP BY
						PT.id,
						BP.entry_fee) SUB ON SUB.sub_id = PTT.id
						
					UNION ALL
						
					SELECT 
						-- VCC.id,
						VCC.id+10,
						VCC.name AS golongan,SUB.* FROM app.t_mtr_vehicle_class VCC LEFT JOIN(SELECT
						VC.id AS sub_id,
						COALESCE(BV.entry_fee,0) AS entrance_fee,
						COUNT(BV.id) AS produksi,
						COALESCE(COUNT(BV.id) * entry_fee,0) AS pendapatan
					FROM
						app.t_trx_payment PP
						JOIN app.t_trx_invoice INV ON INV.trans_number = PP.trans_number
						JOIN app.t_trx_booking BO ON BO.trans_number = PP.trans_number $where_ship_class
						JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code
						JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
						-- LEFT JOIN app.t_mtr_entrance_fee_vehicle EFV ON EFV.port_id = BV.origin AND EFV.vehicle_class_id = BV.vehicle_class_id AND EFV.status = 1
					WHERE
						PP.created_on::date BETWEEN '$datefrom' AND '$dateto'
						AND BO.channel IN ( 'web', 'mobile' )
						$where_port_vehicle
					GROUP BY
						VC.id,
						BV.entry_fee) SUB ON SUB.sub_id = VCC.id AND VCC.status = 1 ORDER BY id ASC";

			// die($sql); exit;
			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		} elseif ($type === 'ifcs') {

			$sql = "SELECT VCC.id,VCC.name AS golongan,SUB.* FROM app.t_mtr_vehicle_class VCC LEFT JOIN(
					SELECT
						VC.id AS sub_id,
						COALESCE(BV.entry_fee,0) AS entrance_fee,
						COUNT(BV.id) AS produksi,
						COALESCE(COUNT(BV.id) * entry_fee,0) AS pendapatan
					FROM
						app.t_trx_payment PP
						JOIN app.t_trx_invoice INV ON INV.trans_number = PP.trans_number
						JOIN app.t_trx_booking BO ON BO.trans_number = PP.trans_number $where_ship_class
						JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code
						JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
						-- LEFT JOIN app.t_mtr_entrance_fee_vehicle EFV ON EFV.port_id = BV.origin AND EFV.vehicle_class_id = BV.vehicle_class_id AND EFV.status = 1
					WHERE
						PP.created_on :: DATE BETWEEN '$datefrom' AND '$dateto' 
						AND BO.channel IN ( 'ifcs' )
						$where_port_vehicle
					GROUP BY
						VC.ID,
						BV.entry_fee) SUB ON SUB.sub_id = VCC.id AND VCC.status = 1 ORDER BY id ASC";


			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		} elseif ($type === 'b2b') {
			$sql = "SELECT 
					-- PTT.id,
					PTT.ordering as id,
					PTT.name AS golongan, SUB.* FROM app.t_mtr_passanger_type PTT LEFT JOIN(
					SELECT
						PT.id AS sub_id,
						COALESCE(BP.entry_fee,0) AS entrance_fee,
						COUNT(BP.id) AS produksi,
						COALESCE(COUNT(BP.id) * entry_fee,0) AS pendapatan
					FROM
						app.t_trx_payment PP
						JOIN app.t_trx_invoice INV ON INV.trans_number = PP.trans_number
						JOIN app.t_trx_booking BO ON BO.trans_number = PP.trans_number $where_ship_class
						JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
						JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
						-- LEFT JOIN app.t_mtr_entrance_fee_passanger EFP ON EFP.port_id = BP.origin AND EFP.passanger_type_id = BP.passanger_type_id AND EFP.status = 1
					WHERE
						PP.created_on::date BETWEEN '$datefrom' AND '$dateto'
						AND BO.channel IN ( 'b2b' )
						$where_port_passanger
					GROUP BY
						PT.id,
						BP.entry_fee) SUB ON SUB.sub_id = PTT.id
						
					UNION ALL
						
					SELECT 
						-- VCC.id,
						VCC.id+10,
						VCC.name AS golongan,SUB.* FROM app.t_mtr_vehicle_class VCC LEFT JOIN(SELECT
						VC.id AS sub_id,
						COALESCE(BV.entry_fee,0) AS entrance_fee,
						COUNT(BV.id) AS produksi,
						COALESCE(COUNT(BV.id) * entry_fee,0) AS pendapatan
					FROM
						app.t_trx_payment PP
						JOIN app.t_trx_invoice INV ON INV.trans_number = PP.trans_number
						JOIN app.t_trx_booking BO ON BO.trans_number = PP.trans_number $where_ship_class
						JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code
						JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
						-- LEFT JOIN app.t_mtr_entrance_fee_vehicle EFV ON EFV.port_id = BV.origin AND EFV.vehicle_class_id = BV.vehicle_class_id AND EFV.status = 1
					WHERE
						PP.created_on::date BETWEEN '$datefrom' AND '$dateto'
						AND BO.channel IN ( 'b2b' )
						$where_port_vehicle
					GROUP BY
						VC.id,
						BV.entry_fee) SUB ON SUB.sub_id = VCC.id AND VCC.status = 1 ORDER BY id ASC";

			// print_r($sql);exit;

			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		}
	}


	public function list_data_13022023($port, $datefrom, $dateto, $ship_class, $type)
	{
		$where_port_passanger = "";
		$where_port_vehicle = "";
		$where_ship_class = "";

		if ($port != "") {
			$where_port_passanger = " AND BP.origin = $port";
			$where_port_vehicle = " AND BV.origin = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		if ($type === 'tunai') {
			$sql = "SELECT
							-- PTT.id as idku,
							PTT.ordering as idku,
							PTT.name as golongan,
							SUB.*
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN (SELECT
											EFP.entrance_fee,
											PT.ID,
											COUNT ( BP.ticket_number ) AS produksi,
											COUNT ( BP.ticket_number ) * EFP.entrance_fee AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type = 'cash'
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto'
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
											LEFT JOIN app.t_mtr_entrance_fee_passanger EFP ON EFP.port_id = BP.origin AND EFP.passanger_type_id = BP.passanger_type_id AND EFP.status = 1
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											EFP.entrance_fee,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
						WHERE PTT.id IN(1,2,3,4)

						UNION ALL

						SELECT
							VCC.id+10 as idku,
							VCC.name as golongan,
							SUB.*
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN (SELECT
											EFV.entrance_fee,
											VC.ID,
											COUNT ( BV.ticket_number ) AS produksi,
											COUNT ( BV.ticket_number ) * EFV.entrance_fee AS pendapatan
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type = 'cash'
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto'
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
											LEFT JOIN app.t_mtr_entrance_fee_vehicle EFV ON EFV.port_id = BV.origin AND EFV.vehicle_class_id = BV.vehicle_class_id AND EFV.status = 1
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											EFV.entrance_fee,
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
		} elseif ($type === 'cashless') {
			$sql = "SELECT
							-- DISTINCT(PTT.id) as idku,
							DISTINCT(PTT.ordering) as idku,
							PTT.name as golongan,
							SUB.entrance_fee,
							SUM(SUB.produksi) AS produksi,
							SUM(SUB.pendapatan) AS pendapatan
						FROM
							app.t_mtr_passanger_type PTT
							LEFT JOIN 	(SELECT
											EFP.entrance_fee,
											PT.ID,
											COUNT ( BP.ticket_number ) AS produksi,
											COUNT ( BP.ticket_number ) * EFP.entrance_fee AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_petugas
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
											LEFT JOIN app.t_mtr_entrance_fee_passanger EFP ON EFP.port_id = BP.origin AND EFP.passanger_type_id = BP.passanger_type_id AND EFP.status = 1
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											EFP.entrance_fee,
											PT.NAME,
											PT.ID

										UNION ALL

										SELECT
											EFP.entrance_fee,
											PT.ID,
											COUNT ( BP.ticket_number ) AS produksi,
											COUNT ( BP.ticket_number ) * EFP.entrance_fee AS pendapatan
										FROM
											app.t_trx_booking_passanger BP
											JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell_vm S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance_vm OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto'
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2
											JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
											LEFT JOIN app.t_mtr_entrance_fee_passanger EFP ON EFP.port_id = BP.origin AND EFP.passanger_type_id = BP.passanger_type_id AND EFP.status = 1
										WHERE
											BP.service_id = 1
											AND BP.status NOT IN (-6)
											$where_port_passanger
										GROUP BY
											EFP.entrance_fee,
											PT.NAME,
											PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3,4)
					GROUP BY
							idku,
							golongan,
							entrance_fee

					UNION ALL
						
					SELECT
							VCC.id+10 as idku,
							VCC.name as golongan,
							SUB.entrance_fee,
							SUB.produksi,
							SUB.pendapatan
						FROM
							app.t_mtr_vehicle_class VCC
							LEFT JOIN 	(SELECT
											EFV.entrance_fee,
											VC.ID,
											COUNT ( BV.ticket_number ) AS produksi,
											COUNT ( BV.ticket_number ) * EFV.entrance_fee AS pendapatan
										FROM
											app.t_trx_booking_vehicle BV
											JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_ship_class
											JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number AND PY.payment_type IN ('prepaid-bni','prepaid-bri','prepaid-mandiri')
											JOIN app.t_trx_sell S ON S.trans_number = BO.trans_number
											JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code AND OB.trx_date BETWEEN '$datefrom' AND '$dateto'
											JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code AND AR.status = 2 $where_regu
											JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
											LEFT JOIN app.t_mtr_entrance_fee_vehicle EFV ON EFV.port_id = BV.origin AND EFV.vehicle_class_id = BV.vehicle_class_id AND EFV.status = 1
										WHERE
											BV.status NOT IN (-6)
											$where_port_vehicle
										GROUP BY
											EFV.entrance_fee,
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
		} elseif ($type === 'online') {
			$sql = "SELECT 
					-- PTT.id,
					PTT.ordering as id,
					PTT.name AS golongan, SUB.* FROM app.t_mtr_passanger_type PTT LEFT JOIN(
					SELECT
						PT.id AS sub_id,
						COALESCE(EFP.entrance_fee,0) AS entrance_fee,
						COUNT(BP.id) AS produksi,
						COALESCE(COUNT(BP.id) * entrance_fee,0) AS pendapatan
					FROM
						app.t_trx_payment PP
						JOIN app.t_trx_invoice INV ON INV.trans_number = PP.trans_number
						JOIN app.t_trx_booking BO ON BO.trans_number = PP.trans_number $where_ship_class
						JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
						JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
						LEFT JOIN app.t_mtr_entrance_fee_passanger EFP ON EFP.port_id = BP.origin AND EFP.passanger_type_id = BP.passanger_type_id AND EFP.status = 1
					WHERE
						PP.created_on::date BETWEEN '$datefrom' AND '$dateto'
						AND BO.channel IN ( 'web', 'mobile' )
						$where_port_passanger
					GROUP BY
						PT.id,
						EFP.entrance_fee) SUB ON SUB.sub_id = PTT.id
						
					UNION ALL
						
					SELECT 
						-- VCC.id,
						VCC.id+10,
						VCC.name AS golongan,SUB.* FROM app.t_mtr_vehicle_class VCC LEFT JOIN(SELECT
						VC.id AS sub_id,
						COALESCE(EFV.entrance_fee,0) AS entrance_fee,
						COUNT(BV.id) AS produksi,
						COALESCE(COUNT(BV.id) * entrance_fee,0) AS pendapatan
					FROM
						app.t_trx_payment PP
						JOIN app.t_trx_invoice INV ON INV.trans_number = PP.trans_number
						JOIN app.t_trx_booking BO ON BO.trans_number = PP.trans_number $where_ship_class
						JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code
						JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
						LEFT JOIN app.t_mtr_entrance_fee_vehicle EFV ON EFV.port_id = BV.origin AND EFV.vehicle_class_id = BV.vehicle_class_id AND EFV.status = 1
					WHERE
						PP.created_on::date BETWEEN '$datefrom' AND '$dateto'
						AND BO.channel IN ( 'web', 'mobile' )
						$where_port_vehicle
					GROUP BY
						VC.id,
						EFV.entrance_fee) SUB ON SUB.sub_id = VCC.id AND VCC.status = 1 ORDER BY id ASC";

			// die($sql); exit;
			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		} elseif ($type === 'ifcs') {

			$sql = "SELECT VCC.id,VCC.name AS golongan,SUB.* FROM app.t_mtr_vehicle_class VCC LEFT JOIN(
					SELECT
						VC.id AS sub_id,
						COALESCE(EFV.entrance_fee,0) AS entrance_fee,
						COUNT(BV.id) AS produksi,
						COALESCE(COUNT(BV.id) * entrance_fee,0) AS pendapatan
					FROM
						app.t_trx_payment PP
						JOIN app.t_trx_invoice INV ON INV.trans_number = PP.trans_number
						JOIN app.t_trx_booking BO ON BO.trans_number = PP.trans_number $where_ship_class
						JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code
						JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
						LEFT JOIN app.t_mtr_entrance_fee_vehicle EFV ON EFV.port_id = BV.origin AND EFV.vehicle_class_id = BV.vehicle_class_id AND EFV.status = 1
					WHERE
						PP.created_on :: DATE BETWEEN '$datefrom' AND '$dateto' 
						AND BO.channel IN ( 'ifcs' )
						$where_port_vehicle
					GROUP BY
						VC.ID,
						EFV.entrance_fee) SUB ON SUB.sub_id = VCC.id AND VCC.status = 1 ORDER BY id ASC";


			if ($this->dbView->query($sql)->num_rows() > 0) {
				return $this->dbView->query($sql)->result();
			} else {
				return false;
			}
		} elseif ($type === 'b2b') {
			$sql = "SELECT 
					-- PTT.id,
					PTT.ordering as id,
					PTT.name AS golongan, SUB.* FROM app.t_mtr_passanger_type PTT LEFT JOIN(
					SELECT
						PT.id AS sub_id,
						COALESCE(EFP.entrance_fee,0) AS entrance_fee,
						COUNT(BP.id) AS produksi,
						COALESCE(COUNT(BP.id) * entrance_fee,0) AS pendapatan
					FROM
						app.t_trx_payment PP
						JOIN app.t_trx_invoice INV ON INV.trans_number = PP.trans_number
						JOIN app.t_trx_booking BO ON BO.trans_number = PP.trans_number $where_ship_class
						JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
						JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
						LEFT JOIN app.t_mtr_entrance_fee_passanger EFP ON EFP.port_id = BP.origin AND EFP.passanger_type_id = BP.passanger_type_id AND EFP.status = 1
					WHERE
						PP.created_on::date BETWEEN '$datefrom' AND '$dateto'
						AND BO.channel IN ( 'b2b' )
						$where_port_passanger
					GROUP BY
						PT.id,
						EFP.entrance_fee) SUB ON SUB.sub_id = PTT.id
						
					UNION ALL
						
					SELECT 
						-- VCC.id,
						VCC.id+10,
						VCC.name AS golongan,SUB.* FROM app.t_mtr_vehicle_class VCC LEFT JOIN(SELECT
						VC.id AS sub_id,
						COALESCE(EFV.entrance_fee,0) AS entrance_fee,
						COUNT(BV.id) AS produksi,
						COALESCE(COUNT(BV.id) * entrance_fee,0) AS pendapatan
					FROM
						app.t_trx_payment PP
						JOIN app.t_trx_invoice INV ON INV.trans_number = PP.trans_number
						JOIN app.t_trx_booking BO ON BO.trans_number = PP.trans_number $where_ship_class
						JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code
						JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
						LEFT JOIN app.t_mtr_entrance_fee_vehicle EFV ON EFV.port_id = BV.origin AND EFV.vehicle_class_id = BV.vehicle_class_id AND EFV.status = 1
					WHERE
						PP.created_on::date BETWEEN '$datefrom' AND '$dateto'
						AND BO.channel IN ( 'b2b' )
						$where_port_vehicle
					GROUP BY
						VC.id,
						EFV.entrance_fee) SUB ON SUB.sub_id = VCC.id AND VCC.status = 1 ORDER BY id ASC";

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
