<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Rama <ramaleksana@gmail.com>
 * @copyright  2020
 *
 */

class VehicleTicketRescheduleModel extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'transaction2/vehicleTicketReschedule';
	}

	public function dataList()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$statusReschedule = $this->input->post('statusReschedule');
		$shipClass = $this->enc->decode($this->input->post('shipClass'));
		$route = $this->enc->decode($this->input->post('route'));
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$paymentDateFrom = trim($this->input->post('paymentDateFrom'));
		$paymentDateTo = trim($this->input->post('paymentDateTo'));
		$route = $this->enc->decode($this->input->post('route'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

		// cek app get_identity_app
		if ($this->get_identity_app() == 0) {
			if (!empty($this->session->userdata("port_id"))) {
				$port_origin = $this->session->userdata("port_id");
			} else {
				$port_origin = $this->enc->decode($this->input->post('port_origin'));
			}
		} else {
			$port_origin = $this->get_identity_app();
		}

		$field = array(
			0	=> 'rs.id',
			1	=> "booking_code",
			2	=> "ticket_number",
			3	=> "length_vehicle",
			4	=> "vehicle_class_name",
			5	=> "ship_class_name",
			6	=> "fare",
			7	=> "payment_date",
			8	=> "keberangkatan",
			9	=> "route_name",
			10	=> "desc_status_reschedule",
			11	=> "new_booking_code",
			12	=> "reschedule_invoice",
			13  => "reschedule_channel",
			14	=> "charge_amount",
			15	=> "adm_fee",
			16	=> "reschedule_fee"
		);

		$order_column = $field[$order_column];

		$where = " WHERE rs.status = 1 AND bk.service_id = 2  and (date(bk.depart_date) between '" . $dateFrom . "' and '" . $dateTo . "' ) ";


		if (!empty($port_origin)) {
			$where .= " and (bk.origin ='{$port_origin}') ";
		}

		if (!empty($shipClass)) {
			$where .= " and (bk.ship_class ='{$shipClass}') ";
		}

		// if (!empty($channel)) {
		// 	$where .= " and (upper(bk.channel) =upper('{$channel}')) ";
		// }

		if (!empty($route)) {
			$getRoute = $this->select_data("app.t_mtr_rute", " where id={$route} ")->row();

			$where .= " and ( bk.origin ='{$getRoute->origin}'  and bk.destination='{$getRoute->destination}' ) ";
		}

		if (!empty($paymentDateFrom) and empty($paymentDateTo)) {
			$where .= " and (date(pay.payment_date)='{$paymentDateFrom}' ) ";
		}

		if (empty($paymentDateFrom) and !empty($paymentDateTo)) {
			$where .= " and (date(pay.payment_date)='{$paymentDateTo}' ) ";
		}

		if (!empty($paymentDateFrom) and !empty($paymentDateTo)) {
			$where .= " and (date(pay.payment_date) between '{$paymentDateFrom}' and  '{$paymentDateTo}'  ) ";
		}


		if (!empty($search['value'])) {
			$where .= " and (
							bk.trans_number ilike '%" . $iLike . "%'
							or bk.booking_code ilike '%" . $iLike . "%'
							or bps.ticket_number ilike '%" . $iLike . "%'
				 		) ";
		}

		if (!empty($statusReschedule)) {
			if ($statusReschedule == 1) {
				$where .= " and inv2.status = 2";
			} elseif ($statusReschedule == 2) {
				$where .= " and inv2.status IN(0,1)";
			} else {
				$where .= "";
			}
		}

		$sql = $this->qry($where);

		// die($sql);

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY  " . $order_column . " {$order_dir}";

		if ($length != -1) {
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			// $bar = (25 / 100) * $row->transfer_dana;
			// $br = ($row->transfer_dana - $bar) * (50 / 100);
			// $pr = $row->transfer_dana - $bar - $br;

			$row->fare = idr_currency($row->fare);
			$row->payment_date = empty($row->payment_date) ? "" : format_date($row->payment_date) . " " . format_time($row->payment_date);
			$row->keberangkatan = empty($row->keberangkatan) ? "" : format_date($row->keberangkatan);
			$row->adm_fee = idr_currency($row->adm_fee);
			$row->reschedule_fee = idr_currency($row->reschedule_fee);
			$row->charge_amount = idr_currency($row->charge_amount);


			$row->no = $i;

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'data'           => $rows
		);
	}


	public function select_data($table, $where = "")
	{
		return $this->dbView->query("select * from $table $where");
	}

	public function getRoute($portId)
	{
		return $this->dbView->query("

								SELECT concat(b.name,'-',c.name) as route_name, a.* from app.t_mtr_rute a
								left join app.t_mtr_port b on a.origin=b.id and b.status<>'-5'
 								left join app.t_mtr_port c on a.destination=c.id and c.status<>'-5'
								 where a.origin ='{$portId}' and a.status <>'-5' 

								 ");
	}

	public function qry($where = "", $order = "")
	{
		return $data = "SELECT
		bk.booking_code,
		bps.ticket_number,
		bps.length_cam as length_vehicle,
		ty.name AS vehicle_class_name,
		sc.name AS ship_class_name,
		bps.fare,
		pay.payment_date,
		concat(bk.depart_date,' ',bk.depart_time) as keberangkatan,
		concat(port.name,'-',port2.name) as route_name,
		bk.depart_date,
		bk.depart_time_start,
		bk.depart_time_end,
		port.name AS origin,
		port2.name AS destination,
		bps.status AS ticket_status,
		pay.trans_number AS invoice_number,
		inv.channel,
		inv.payment_type,
		rs.charge_amount,
		rs.new_booking_code,
		rs.adm_fee,
		rs.reschedule_fee,
		inv2.channel AS reschedule_channel, 
		inv2.trans_number AS reschedule_invoice,
		inv2.payment_type AS payment_new_type,
		inv2.status AS reschedule_status,
		(
			CASE
				WHEN inv2.status = 2 THEN 'SUDAH DIBAYAR'
				WHEN inv2.status IN(0,1) THEN 'BELUM DIBAYAR'
				ELSE ''
			END
		) AS desc_status_reschedule
	FROM
		app.t_trx_reschedule rs
	JOIN app.t_trx_booking bk ON rs.booking_code = bk.booking_code
	JOIN app.t_trx_invoice inv ON inv.trans_number = bk.trans_number
	JOIN app.t_trx_payment pay ON pay.trans_number = bk.trans_number
	JOIN app.t_trx_booking_vehicle bps ON bps.booking_code = bk.booking_code
	JOIN app.t_mtr_vehicle_class ty ON ty.id = bps.vehicle_class_id
	JOIN app.t_mtr_ship_class sc ON sc.id = bk.ship_class
	JOIN app.t_mtr_port port ON port.id = bps.origin
	JOIN app.t_mtr_port port2 ON port2.id = bps.destination
	JOIN app.t_trx_invoice inv2 ON inv2.trans_number = rs.trans_number
	
	{$where}
	{$order}
	";
	}

	public function download()
	{


		$shipClass = $this->enc->decode($this->input->get('shipClass'));
		$route = $this->enc->decode($this->input->get('route'));
		$dateTo = trim($this->input->get('dateTo'));
		$dateFrom = trim($this->input->get('dateFrom'));
		$paymentDateFrom = trim($this->input->get('paymentDateFrom'));
		$paymentDateTo = trim($this->input->get('paymentDateTo'));
		$route = $this->enc->decode($this->input->get('route'));
		$statusReschedule = trim($this->input->get('statusReschedule'));


		// cek app get_identity_app
		if ($this->get_identity_app() == 0) {
			if (!empty($this->session->userdata("port_id"))) {
				$port_origin = $this->session->userdata("port_id");
			} else {
				$port_origin = $this->enc->decode($this->input->get('port'));
			}
		} else {
			$port_origin = $this->get_identity_app();
		}


		$where = " WHERE bk.service_id = 2 and (date(bk.depart_date) between '" . $dateFrom . "' and '" . $dateTo . "' ) ";


		if (!empty($port_origin)) {
			$where .= " and (bk.origin ='{$port_origin}') ";
		}

		if (!empty($shipClass)) {
			$where .= " and (bk.ship_class ='{$shipClass}') ";
		}

		if (!empty($route)) {
			$getRoute = $this->select_data("app.t_mtr_rute", " where id={$route} ")->row();

			$where .= " and ( bk.origin ='{$getRoute->origin}'  and bk.destination='{$getRoute->destination}' ) ";
		}

		if (!empty($paymentDateFrom) and empty($paymentDateTo)) {
			$where .= " and (date(pay.payment_date)='{$paymentDateFrom}' ) ";
		}

		if (empty($paymentDateFrom) and !empty($paymentDateTo)) {
			$where .= " and (date(pay.payment_date)='{$paymentDateTo}' ) ";
		}

		if (!empty($paymentDateFrom) and !empty($paymentDateTo)) {
			$where .= " and (date(pay.payment_date) between '{$paymentDateFrom}' and '{$paymentDateTo}'  ) ";
		}

		if (!empty($statusReschedule)) {
			if ($statusReschedule == 1) {
				$where .= " and inv2.status = 2";
			} elseif ($statusReschedule == 2) {
				$where .= " and inv2.status IN(0,1)";
			} else {
				$where .= "";
			}
		}


		$order = " order by rs.created_on";

		$sql = $this->qry($where, $order);

		// die($sql);
		$query = $this->dbView->query($sql);
		return $query;
	}

	function get_channel()
	{
		$data  = array('' => 'SEMUA CHANNEL');
		$query = $this->dbView->query(" SELECT DISTINCT channel FROM app.t_trx_invoice where channel<>'' ORDER BY channel asc ")->result();

		foreach ($query as $key => $value) {
			$data[$this->enc->encode($value->channel)] = strtoupper(str_replace('_', ' ', $value->channel));
		}

		return array_unique($data);
	}

	public function get_identity_app()
	{
		$data = $this->dbView->query("select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}
}
