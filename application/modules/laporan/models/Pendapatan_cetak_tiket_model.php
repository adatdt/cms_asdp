<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pendapatan_cetak_tiket_model extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'laporan/pendapatan_cetak_tiket';
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
		$shift = $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

		$field = array(
			0 => 'shift_date',
			1 => 'port_name',
			2 => 'ship_name',
			3 => 'port_name',
			4 => 'shift_name',
			5 => 'team_name'
		);

		$where = " WHERE ar.status = 2";

		if ((!empty($dateFrom) and empty($dateTo)) || (empty($dateFrom) and !empty($dateTo))) {
			$where .= " AND ob.shift_date ='$dateFrom' or ob.shift_date ='$dateTo'";
		}

		if (!empty($dateFrom) and !empty($dateTo)) {
			$where .= " AND ob.shift_date BETWEEN '$dateFrom' and  '$dateTo'";
		}

		if (!empty($port)) {
			$where .= " AND (ob.port_id = '" . $port . "')";
		}

		if (!empty($shift)) {
			$where .= " AND (ob.shift_id = " . $shift . ")";
		}

		if (!empty($search['value'])) {
			$where .= " AND ( p.name ilike '%" . $iLike . "%' or shift_name ilike '%" . $iLike . "%' or team_name ilike '%" . $iLike . "%')";
		}

		$sql = "SELECT DISTINCT
					ob.shift_date,
					p.name AS port_name,
					ob.port_id,
					shift_name,
					ob.shift_id,
					team_name,
					port_origin.name AS origin,
					port_destination.name AS destination,
					coalesce(users.first_name, '')||' '||coalesce(users.last_name, '') as spv
				FROM
					app.t_trx_open_boarding ob
				JOIN app.t_trx_assignment_regu ar ON ar.shift_id = ob.shift_id AND ar.assignment_date = ob.shift_date AND ar.status = 2
				JOIN app.t_mtr_port p ON p.id = ob.port_id
				JOIN app.t_mtr_shift s ON s.id = ob.shift_id
				JOIN core.t_mtr_team t ON t.team_code = ar.team_code
				JOIN app.t_trx_schedule ss ON ss.schedule_code = ob.schedule_code
				JOIN app.t_mtr_port port_origin ON ss.port_id = port_origin.id
				JOIN app.t_mtr_port port_destination ON ss.destination_port_id = port_destination.id
				JOIN core.t_mtr_user users on ar.supervisor_id = users.id
				{$where}";

		$query          = $this->dbView->query($sql);
		$records_total  = $query->num_rows();

		$order_by = '';
		foreach ($order as $key => $value) {
			$order_column = $order[$key]['column'];
			$order_dir    = strtoupper($order[$key]['dir']);
			$order_column = $field[$order_column];


			$order_by .= "{$order_column} {$order_dir},";
		}

		$order_by = rtrim($order_by, ",");
		$sql .= " ORDER BY {$order_by}";

		if ($length != -1) {
			$sql .= " limit {$length} offset {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$param 	= $this->enc->encode($row->shift_date . '|' . $row->port_id . '|' . $row->shift_id . '|' . $row->port_name . '|' . $row->shift_name . '|' . $row->team_name . '|' . $row->spv);

			$detail_url = site_url($this->_module . "/detail/{$param}");
			$pdf_url    = site_url($this->_module . "/download_pdf/{$param}");
			$excel_url  = site_url($this->_module . "/download_excel/{$param}");

			$row->actions .= generate_button_new($this->_module, 'download_pdf', $pdf_url);
			$row->actions .= generate_button_new($this->_module, 'download_excel', $excel_url);
			$row->shift_date = format_date($row->shift_date);
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

	public function old_list_detail($get)
	{
		$sql = "SELECT 
				tms.id,
				tms.name as ship_name,
				COALESCE(trip.ticket_count, 0) AS ticket_count,
				COALESCE(trip.adm_fee, 0) as adm_fee
			FROM app.t_mtr_ship tms 
			LEFT JOIN (			
				SELECT 
					subtrip.ship_id,
					count(ticket_number) as ticket_count,
					sum(adm_fee) as adm_fee
				FROM (
					SELECT 
						distinct on (ttbop.ticket_number)
						ttob.ship_id,
						ttbop.ticket_number,
						ttbop.adm_fee
					FROM app.t_trx_open_boarding ttob
					JOIN app.t_trx_boarding_passanger ttbp ON ttbp.boarding_code = ttob.boarding_code AND ttbp.status = 1
					JOIN app.t_trx_booking_passanger ttbop ON ttbop.ticket_number = ttbp.ticket_number 
					WHERE ttob.port_id = {$get[1]}
					AND ttob.shift_id = {$get[2]}
					AND ttob.shift_date = '{$get[0]}'
					GROUP BY 
						ttob.ship_id,
						ttbop.ticket_number,
						ttbop.adm_fee
				) AS subtrip
				GROUP BY
					subtrip.ship_id					
			) AS trip ON trip.ship_id = tms.id
			ORDER BY 
				tms.name ASC";

		$data = array();
		$totalTicket = 0;
		$totalAdmfee = 0;

		foreach ($this->dbView->query($sql)->result() as $row) {
			$totalTicket += $row->ticket_count;
			$totalAdmfee += $row->adm_fee;

			$data[] = $row;
		}

		return array(
			'data' => $data,
			'totalTicket' => $totalTicket,
			'totalAdmfee' => $totalAdmfee
		);
	}

	public function list_detail($port, $datefrom, $dateto, $shift_id)
	{
		$where_port = "";
		$where_shift = "";

		if ($port != "") {
			$where_port = "AND obor.port_id = $port";
		}

		if ($shift_id != "") {
			$where_shift = "AND obor.shift_id = $shift_id";
		}

		$session_shift_class = $this->session->userdata('ship_class_id');
		if ($session_shift_class != '') {
			$where_sc = " AND obor.ship_class = $session_shift_class";
		} else {
			$where_sc = "";
		}

		// $sql = "SELECT 
		// 			tms.id,
		// 			tsc.name as company,
		// 			tms.name as ship_name,
		// 			COALESCE(harga,0) AS harga,
		// 			COALESCE(trip.ticket_count, 0) AS produksi,
		// 			COALESCE(trip.adm_fee, 0) as pendapatan
		// 		FROM app.t_mtr_ship tms
		// 		LEFT JOIN app.t_mtr_ship_company tsc ON tsc.id = tms.ship_company_id
		// 		LEFT JOIN (			
		// 			SELECT 
		// 				subtrip.ship_id,
		// 				adm_fee as harga,
		// 				count(ticket_number) as ticket_count,
		// 				sum(adm_fee) as adm_fee
		// 			FROM (
		// 				SELECT 
		// 					distinct on (ttbop.ticket_number)
		// 					ttob.ship_id,
		// 					ttbop.ticket_number,
		// 					ttbop.adm_fee
		// 				FROM app.t_trx_open_boarding ttob
		// 					JOIN app.t_trx_boarding_passanger ttbp ON ttbp.boarding_code = ttob.boarding_code AND ttbp.status = 1
		// 					JOIN app.t_trx_booking_passanger ttbop ON ttbop.ticket_number = ttbp.ticket_number 
		// 				WHERE
		// 					ttob.shift_date BETWEEN '$datefrom' AND '$dateto'
		// 					$where_port
		// 					$where_shift
		// 				GROUP BY 
		// 					ttob.ship_id,
		// 					adm_fee,
		// 					ttbop.ticket_number
		// 			) AS subtrip
		// 			GROUP BY
		// 				subtrip.ship_id,
		// 				adm_fee
		// 		) AS trip ON trip.ship_id = tms.id
		// 		WHERE tms.status NOT IN(-5)
		// 		ORDER BY 
		// 			company ASC,
		// 			ship_name ASC";
		$sql = "SELECT ship.id,com.name as company,ship.name as ship_name,coalesce(passanger.adm_fee,0) as harga, 
					coalesce(passanger.total,0) as produksi,  coalesce((adm_fee*total),0) as pendapatan
					from app.t_mtr_ship ship 
					join app.t_mtr_ship_company com on com.id = ship.ship_company_id 
					left join(
					select id,adm_fee,sum(total) as total from (
								select ship.id,bps.adm_fee,count(bps.ticket_number) as total from app.t_mtr_ship ship 
								join app.t_trx_open_boarding obor on obor.ship_id = ship.id 
								join app.t_trx_boarding_passanger boar on boar.boarding_code = obor.boarding_code
								join app.t_trx_booking_passanger bps on bps.ticket_number = boar.ticket_number and bps.service_id = 1 
									where obor.shift_date BETWEEN '$datefrom' AND '$dateto' $where_shift  $where_port $where_sc group by ship.id,bps.adm_fee 
								union all 
								select ship.id,bps.adm_fee,count(bps.ticket_number)as total from app.t_mtr_ship ship 
								join app.t_trx_open_boarding obor on obor.ship_id = ship.id 
								join app.t_trx_boarding_vehicle boar on boar.boarding_code = obor.boarding_code
								join app.t_trx_booking_vehicle bps on bps.ticket_number = boar.ticket_number 
								where obor.shift_date BETWEEN '$datefrom' AND '$dateto' $where_shift $where_port $where_sc group by ship.id,bps.adm_fee 			
								) as detail group by id,adm_fee
					)
					as passanger on passanger.id = ship.id order by com.name asc,ship.name asc";

		$data = array();
		$totalTicket = 0;
		$totalAdmfee = 0;

		foreach ($this->dbView->query($sql)->result() as $row) {
			$totalTicket += $row->produksi;
			$totalAdmfee += $row->pendapatan;

			$data[] = $row;
		}

		return array(
			'data' => $data,
			'totalTicket' => $totalTicket,
			'totalAdmfee' => $totalAdmfee
		);
	}

	public function headerku($port_id, $datefrom, $dateto, $shift_id)
	{
		$where_port = "";
		$where_shift_1 = "";
		$where_shift_2 = "";

		if ($port_id != "") {
			$where_port = " AND bor.port_id = $port_id";
		}

		if ($shift_id != "") {
			$where_shift_1 = " AND ar.shift_id = $shift_id";
			$where_shift_2 = " AND bor.shift_id = $shift_id";
		}

		$sql = "SELECT
					DISTINCT(bor.port_id),
					port.name as port,
					bor.shift_date,
					ori.name as origin,
					desti.name as destination,
					sh.shift_name,
					t.team_name,
					br.branch_name
				FROM
					app.t_trx_open_boarding bor
					JOIN app.t_trx_boarding_vehicle bov ON bov.boarding_code = bor.boarding_code AND bov.status = 1
					JOIN app.t_trx_booking_vehicle bv ON bv.ticket_number = bov.ticket_number AND bv.status = 5
					JOIN app.t_mtr_vehicle_class VC ON VC.id = bv.vehicle_class_id
					JOIN app.t_trx_booking bok ON bok.booking_code = bv.booking_code
					JOIN app.t_mtr_port ori ON ori.id = bok.origin
					JOIN app.t_mtr_port desti ON desti.id = bok.destination
					JOIN app.t_trx_assignment_regu ar ON ar.assignment_date BETWEEN '$datefrom' AND '$dateto' $where_shift_1
					JOIN core.t_mtr_team t ON t.team_code = ar.team_code
					JOIN app.t_mtr_branch br ON br.branch_code = bok.branch_code
					JOIN app.t_mtr_shift sh ON sh.id = bor.shift_id
					JOIN app.t_mtr_port port ON port.id = bor.port_id
				WHERE
					shift_date BETWEEN '$datefrom' AND '$dateto'
					$where_port
					$where_shift_2";

		if ($this->dbView->query($sql)->num_rows() == 1) {
			return $this->dbView->query($sql)->row();
		}
	}
}
