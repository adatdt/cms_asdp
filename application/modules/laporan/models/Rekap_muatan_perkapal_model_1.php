<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Rekap_muatan_perkapal_model
 * -----------------------
 *
 * @author     Arif Rudianto
 * @copyright  2019
 *
 */

class Rekap_muatan_perkapal_model extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'laporan/rekap_muatan_perkapal';
	}

	public function get_kapal_company($user_id)
	{
		return $this->db->query("SELECT
									tship.name,
									tship.id
								FROM
									app.t_mtr_user_ship tuship
									JOIN core.t_mtr_user usere ON usere.id = tuship.user_id
									JOIN app.t_mtr_ship tship ON tship.ship_company_id = tuship.company_id
								WHERE
									usere.id = $user_id")->result();
	}

	public function dataList()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$port = $this->enc->decode($this->input->post('port'));
		$ship = $this->enc->decode($this->input->post('ship'));
		$dock = $this->enc->decode($this->input->post('dock'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		$group_id = $this->session->userdata('group_id');
		$user_id = $this->session->userdata('id');
		$session_shift_class = $this->session->userdata('ship_class_id');

		$field = array(
			1 => 'created_on',
			2 => 'ship_name',
			3 => 'port_name',
			4 => 'ship_class_name',
			5 => 'dock_name',
			6 => 'shift_date',
			7 => 'shift_name'
		);

		$order_column = $field[$order_column];
		$where = " where ttob.shift_id is not null and ttob.status = 0";

		if ($group_id == 11) {
			$where .= " AND ship_id IN (SELECT
										tship.id
									FROM
										app.t_mtr_user_ship tuship
										JOIN core.t_mtr_user usere ON usere.id = tuship.user_id
										JOIN app.t_mtr_ship tship ON tship.ship_company_id = tuship.company_id
									WHERE
										usere.id = $user_id)";
		}

		if ((!empty($dateFrom) and empty($dateTo)) || (empty($dateFrom) and !empty($dateTo))) {
			$where .= " and shift_date ='$dateFrom' or shift_date ='$dateTo'";
		}

		if (!empty($dateFrom) and !empty($dateTo)) {
			$where .= " and shift_date between '$dateFrom' and  '$dateTo'";
		}

		if (!empty($port)) {
			$where .= " and (ttob.port_id=" . $port . ")";
		}

		if ($ship) {
			$where .= " and (ttob.ship_id=" . $ship . ")";
		}

		if (!empty($search['value'])) {
			$where .= " and (tmsh.name ilike '%" . $iLike . "%' or tmp.name ilike '%" . $iLike . "%' or tmd.name ilike '%" . $iLike . "%')";
		}

		if (!empty($session_shift_class)) {
			$where .= " and ttob.ship_class = {$session_shift_class}";
		}

		$sql = "SELECT ttob.schedule_code, ttob.created_on, shift_date, shift_name, ttob.shift_id, ttob.ship_id, tmsh.name as ship_name, ttob.port_id, tmp.name as port_name, tmsc.name as ship_class_name, tmd.name as dock_name
				from app.t_trx_open_boarding ttob
				join app.t_mtr_port tmp on ttob.port_id = tmp.id
				join app.t_mtr_dock tmd on ttob.dock_id = tmd.id
				join app.t_mtr_ship tmsh on ttob.ship_id = tmsh.id
				join app.t_mtr_ship_class tmsc on ttob.ship_class = tmsc.id
				left join app.t_mtr_shift shift on shift.id = ttob.shift_id
				{$where}
				";


		$query          = $this->db->query($sql);
		$records_total  = $query->num_rows();

		$sql .= " order by " . $order_column . " {$order_dir}";

		if ($length != -1) {
			$sql .= " limit {$length} offset {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$code 			= $this->enc->encode($row->schedule_code);
			$detail_url 	= site_url($this->_module . "/detail?id={$code}");
			$pdf_url 		= site_url($this->_module . "/download_pdf?id={$code}");
			$excel_url 		= site_url($this->_module . "/download_excel?id={$code}");

			$row->actions 	= "";
			$row->actions 	.= generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_pdf', $pdf_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_excel', $excel_url);
			$row->created_on = date('d-m-Y H:i:s', strtotime($row->created_on));
			$row->shift_date = date('d-m-Y', strtotime($row->shift_date));
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

	public function detail_trip($where = "")
	{
		$sql = "SELECT ship.name as ship_name, ship_company.name as company_name, port.name as port_name, origin.name||'-'||destination.name as trip, dock.name as dock_name, ship_class.name as ship_class, sail_date
			from 
			app.t_trx_open_boarding ttob
			join app.t_mtr_ship ship on ttob.ship_id = ship.id
			left join app.t_mtr_ship_company ship_company on ship.ship_company_id = ship_company.id
			left join app.t_mtr_port port on ttob.port_id = port.id
			left join app.t_mtr_dock dock on ttob.dock_id = dock.id
			left join app.t_trx_schedule schedule on ttob.schedule_code = schedule.schedule_code
			left join app.t_mtr_ship_class ship_class on ttob.ship_class = ship_class.id
			left join (select id, name from app.t_mtr_port) origin on schedule.port_id = origin.id
			left join (select id, name from app.t_mtr_port) destination on schedule.destination_port_id = destination.id
			{$where}";
		// echo $sql;exit();

		$result = $this->db->query($sql);

		return $result;
	}

	public function list_detail_passanger($schedule_code)
	{ //$date, $shift_id, $ship_id, $port_id

		$sql = "SELECT 
				passanger_type.name as passanger_type_name, 
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
					ttob.schedule_code = '{$schedule_code}'
					group by 
					ttbp.passanger_type_id, 
					ttbp.trip_fee,
					ttbp.adm_fee
				) trx_passenger on passanger_type.id = trx_passenger.passanger_type_id where passanger_type.id in (1, 2)";

		$result = $this->db->query($sql);

		return $result->result();
	}

	public function list_detail_vehicle($schedule_code)
	{ //$date, $shift_id, $ship_id, $port_id

		$sql = "SELECT 
				vehicle_type.name as vehicle_type_name, 
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
					ttob.schedule_code = '{$schedule_code}'					
					group by 
					ttbv.vehicle_class_id, 
					ttbv.trip_fee,
					ttbv.adm_fee
				) trx_vehicle on vehicle_type.id = trx_vehicle.vehicle_class_id where vehicle_type.status = 1 order by vehicle_type.id asc";

		$result = $this->db->query($sql);

		return $result->result();
	}

	public function dock_fare($where = "")
	{

		return $this->db->query("select (schedule.call * schedule.ship_grt * schedule.dock_fare) as dock_service
			from app.t_trx_schedule schedule
			{$where}");
	}

	public function get_kepil()
	{
		$sql = $this->db->query("SELECT param_value FROM app.t_mtr_custom_param WHERE param_name = 'jasa_kepil'")->row();
		return $sql->param_value;
	}

	public function adm_fee($where = "")
	{

		return $this->db->query("select param_value from app.t_mtr_custom_param
			{$where}");
	}

	public function select_data($table, $where = "")
	{
		return $this->db->query("select * from $table $where");
	}
}
