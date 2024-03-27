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

class Ticket_expired_v2_model extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'laporan/ticket_expired_v2';
	}

	public function dataList($ket = "kendaraan")
	{
		if ($this->input->post()) {
			$dateTo 		 = trim($this->input->post('dateTo'));
			$dateFrom 	     = trim($this->input->post('dateFrom'));
			$statusExpired   = $this->enc->decode($this->input->post('statusExpired'));
			$route 		     = $this->enc->decode($this->input->post('route'));
			$paymentDateFrom = trim($this->input->post('paymentDateFrom'));
			$paymentDateTo   = trim($this->input->post('paymentDateTo'));
			$param_port      = $this->enc->decode($this->input->post('port_origin'));
			$cek_sc     	 = $this->getClassBySession();
			if ($cek_sc == false) {
				$shipClass = $this->enc->decode($this->input->post('shipClass'));
			} else {
				$shipClass = $cek_sc['id'];
			}
		} else {
			$dateTo 		 = trim($this->input->get('dateTo'));
			$dateFrom 		 = trim($this->input->get('dateFrom'));
			$statusExpired 	 = $this->enc->decode($this->input->get('statusExpired'));
			$route 			 = $this->enc->decode($this->input->get('route'));
			$paymentDateFrom = trim($this->input->get('paymentDateFrom'));
			$paymentDateTo 	 = trim($this->input->get('paymentDateTo'));
			$param_port 	 = $this->enc->decode($this->input->get('port'));
			$cek_sc     	 = $this->getClassBySession();
			if ($cek_sc == false) {
				$shipClass = $this->enc->decode($this->input->get('shipClass'));
			} else {
				$shipClass = $cek_sc['id'];
			}
		}

		// cek app get_identity_app
		if ($this->get_identity_app() == 0) {
			if (!empty($this->session->userdata("port_id"))) {
				$port_origin = $this->session->userdata("port_id");
			} else {
				$port_origin = $param_port;
			}
		} else {
			$port_origin = $this->get_identity_app();
		}

		$where = " WHERE a.status <>'-6' and (date(a.depart_date) between '" . $dateFrom . "' and '" . $dateTo . "' ) ";

		$expired = " 	and (
				(b.status=2 and b.checkin_expired<=now())
				or
				(b.status=3 and b.gatein_expired<=now())
				or
				(b.status in (4,7) and b.boarding_expired<=now())
				or b.status in (10,11,12) ) ";

		if (!empty($statusExpired)) {
			if ($statusExpired == 10) {
				$expired = " and ((b.status=2 and b.checkin_expired<=now()) or b.status=10 ) ";
			} else if ($statusExpired == 11) {
				$expired = " and ((b.status=3 and b.gatein_expired<=now()) or b.status=11 ) ";
			} else {
				$expired = " and ((b.status in (4,7) and b.boarding_expired<=now()) or b.status=12 ) ";
			}
		}

		if (!empty($port_origin)) {
			$where .= " and (a.origin ='{$port_origin}') ";
		}

		if (!empty($shipClass)) {
			$where .= " and (a.ship_class ='{$shipClass}') ";
		}

		if (!empty($route)) {
			$getRoute = $this->select_data("app.t_mtr_rute", " where id={$route} ")->row();

			$where .= " and ( a.origin ='{$getRoute->origin}'  and a.destination='{$getRoute->destination}' ) ";
		}

		if (!empty($paymentDateFrom) and empty($paymentDateTo)) {
			$where .= " and (date(e.payment_date)='{$paymentDateFrom}' ) ";
		}

		if (empty($paymentDateFrom) and !empty($paymentDateTo)) {
			$where .= " and (date(e.payment_date)='{$paymentDateTo}' ) ";
		}

		if (!empty($paymentDateFrom) and !empty($paymentDateTo)) {
			$where .= " and (date(e.payment_date) between '{$paymentDateFrom}' and  '{$paymentDateTo}'  ) ";
		}

		$order = " order by a.depart_date , a.depart_time";

		if ($ket == 'kendaraan') {
			$sql = $this->qry_kendaraan($where . " " . $expired, $order);
		} else {
			$sql = $this->qry_penumpang($where . " " . $expired, $order);
		}


		$query     = $this->dbView->query($sql);
		return $query;
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

	public function getSingleRoute($routeId)
	{
		return $this->dbView->query("
			SELECT concat(b.name,' - ',c.name) as route_name, a.* from app.t_mtr_rute a
			left join app.t_mtr_port b on a.origin=b.id and b.status<>'-5'
			left join app.t_mtr_port c on a.destination=c.id and c.status<>'-5'
			where a.id ='{$routeId}' and a.status <>'-5' 
			");
	}

	public function qry_kendaraan($where = "", $order = "")
	{
		return $data = "SELECT 
				a.booking_code, 
				b.ticket_number,
				c.name as golongan,
				d.name as service_name,
				b.fare,
				(
					case
					when b.status=2 and b.checkin_expired<=now() then 'Check in Expired'
					when b.status=10 then 'Check in Expired'
					when b.status=3 and b.gatein_expired<=now() then 'Gate in Expired'
					when b.status=11 then 'Gate in Expired'
					when b.status=4 and b.boarding_expired<=now() then 'Boarding Expired'
					when b.status=12 then 'Boarding Expired'
					else 'Boarding Expired' end
				) as description_expired,
				(
					case
					when py2.payment_date is not null then py2.payment_date
					when py3.payment_date is not null then py3.payment_date
					else e.payment_date end
				) as payment_date,
				e.amount as transfer_dana,
				a.channel as sales_channel,
				a.trans_number,
				up.trans_number,
				op.trans_number,
				iv.trans_number,
				(
					case
					when up.trans_number is not null then up.trans_number
					when op.trans_number is not null then op.trans_number
					else iv.trans_number end
				) as invoice_number,
				concat(a.depart_date,' ',a.depart_time) as keberangkatan,
				concat(g.name,'-',h.name) as route_name,
				f.description,
				i.name as ship_class_name,
				b.length_cam as length_vehicle,
				j.created_on as tanggal_pengakuan,
				(
					case 
						when j.created_on is null then 0
						else b.fare
					end
				) as pendapatan_expired

			from app.t_trx_booking a

			left join app.t_trx_under_paid up on a.booking_code=up.booking_code and up.status=1
			left join app.t_trx_over_paid op on a.booking_code=op.booking_code and op.status=1
			left join app.t_trx_invoice iv on a.trans_number=iv.trans_number 
			left join app.t_trx_booking_vehicle b on a.booking_code=b.booking_code
			left join app.t_mtr_vehicle_class c on b.vehicle_class_id=c.id
			left join app.t_mtr_service d on a.service_id=d.id
			left join app.t_trx_payment e on a.trans_number=e.trans_number
			left join app.t_trx_payment py2 on up.trans_number=py2.trans_number
			left join app.t_trx_payment py3 on op.trans_number=py3.trans_number
			LEFT JOIN app.t_mtr_status f on a.status = f.status AND tbl_name = 't_trx_booking'
			left join app.t_mtr_port g on a.origin =g.id
			left join app.t_mtr_port h on a.destination =h.id
			left join app.t_mtr_ship_class i on a.ship_class =i.id
			left join app.t_trx_check_in_vehicle j on b.ticket_number = j.ticket_number
			{$where}{$order} ";
	}

	public function qry_penumpang($where = "", $order = "")
	{
		return "SELECT 
			b.status,
			(
				case
				when b.status=2 and b.checkin_expired<=now() then 'Check in Expired'
				when b.status=10 then 'Check in Expired'
				when b.status=3 and b.gatein_expired<=now() then 'Gate in Expired'
				when b.status=11 then 'Gate in Expired'
				when b.status=4 and b.boarding_expired<=now() then 'Boarding Expired'
				when b.status=12 then 'Boarding Expired'
				else 'Boarding Expired' end
			) as description_expired,
			b.checkin_expired,
			b.gatein_expired,
			b.boarding_expired,
			a.booking_code,
			b.ticket_number,
			c.name as golongan,
			d.name as service_name,
			b.fare,
			e.payment_date,
			e.amount as transfer_dana,
			a.channel as sales_channel,
			a.trans_number,
			concat(a.depart_date,' ',a.depart_time) as keberangkatan,
			concat(g.name,'-',h.name) as route_name,
			f.description,
			i.name as ship_class_name,
			j.created_on as tanggal_pengakuan,
			(
				case 
					when j.created_on is null then 0
					else b.fare
				end
			) as pendapatan_expired
			from app.t_trx_booking a 
			left join app.t_trx_booking_passanger b on a.booking_code=b.booking_code
			left join app.t_mtr_passanger_type c on b.passanger_type_id=c.id
			left join app.t_mtr_service d on a.service_id=d.id
			left join app.t_trx_payment e on a.trans_number=e.trans_number
			LEFT JOIN app.t_mtr_status f on a.status = f.status AND tbl_name = 't_trx_booking'
			left join app.t_mtr_port g on a.origin =g.id
			left join app.t_mtr_port h on a.destination =h.id
			left join app.t_mtr_ship_class i on a.ship_class =i.id
			left join app.t_trx_check_in j on b.ticket_number = j.ticket_number
			{$where} and a.service_id = 1 {$order} ";
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
