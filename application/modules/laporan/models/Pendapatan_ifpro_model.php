<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ----------------------------
 * CLASS NAME : Pendapatan_ifpro
 * ----------------------------
 *
 * @author     Robai
 * @copyright  2019
 *
 */

class Pendapatan_ifpro_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'laporan/pendapatan_ifpro';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$branch = $this->enc->decode($this->input->post('branch'));
		$shift = $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

		$field = array(
			0 => 'shift_date',
			1 => 'port_name',
			2 => 'ship_name',
			3 => 'branch_name',
			4 => 'shift_name',
			5 => 'team_name'
		);

		$where = " WHERE ar.status = 2";

		if ((!empty($dateFrom) and empty($dateTo))||(empty($dateFrom) and !empty($dateTo))){
			$where .=" AND ob.shift_date ='$dateFrom' or ob.shift_date ='$dateTo'";
		}

		if(!empty($dateFrom) and !empty($dateTo)){
			$where .=" AND ob.shift_date BETWEEN '$dateFrom' and  '$dateTo'";   
		}

		if(!empty($branch)){
			$where .=" AND (branch_code = '".$branch."')";
		}

		if(!empty($shift)){
			$where .=" AND (ob.shift_id = ".$shift.")";
		}

		if (!empty($search['value'])){
			$where .=" AND ( branch_name ilike '%".$iLike."%' or shift_name ilike '%".$iLike."%' or team_name ilike '%".$iLike."%')";	
		}

		$sql = "SELECT DISTINCT
					ob.shift_date,
					p.name AS port_name,
					ob.port_id,
					sc.name AS ship_name,
					ob.ship_class,
					shift_name,
					ob.shift_id,
					branch_name,
					team_name,
					port_origin.name AS origin,
					port_destination.name AS destination,
					coalesce(users.first_name, '')||' '||coalesce(users.last_name, '') as spv
				FROM
					app.t_trx_open_boarding ob
				JOIN app.t_trx_assignment_regu ar ON ar.shift_id = ob.shift_id AND ar.assignment_date = ob.shift_date AND ar.status = 2
				JOIN app.t_mtr_ship_class sc ON sc.id = ob.ship_class AND ob.ship_class = 2
				JOIN app.t_mtr_port p ON p.id = ob.port_id
				JOIN app.t_mtr_shift s ON s.id = ob.shift_id
				JOIN app.t_mtr_branch b ON b.port_id = ob.port_id AND b.ship_class = ob.ship_class
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

		$order_by = rtrim($order_by,",");
		$sql .= " ORDER BY {$order_by}";

		if($length != -1){
			$sql .=" limit {$length} offset {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$param 	= $this->enc->encode($row->shift_date.'|'.$row->ship_class.'|'.$row->shift_id.'|'.$row->branch_name.'|'.$row->port_name.'|'.$row->origin.'|'.$row->destination.'|'.$row->shift_name.'|'.$row->team_name.'|'.$row->spv);

			$detail_url = site_url($this->_module."/detail/{$param}");
			$pdf_url    = site_url($this->_module."/download_pdf/{$param}");
			$excel_url  = site_url($this->_module."/download_excel/{$param}");

			$row->actions  = '';
			$row->actions .= generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions .= generate_button_new($this->_module, 'download_pdf', $pdf_url);
			$row->actions .= generate_button_new($this->_module, 'download_excel', $excel_url);

     		$row->shift_date = format_date($row->shift_date);
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

	public function old_list_detail_passanger($get){
		$sql = "SELECT name, ticket_count, ifpro_fee, total_amount 
				FROM app.t_mtr_passanger_type pt
				LEFT JOIN (
				SELECT
					b.passanger_type_id,
					COUNT ( DISTINCT b.ID ) AS ticket_count,
					b.ifpro_fee,
					COUNT ( DISTINCT b.ID ) * b.ifpro_fee AS total_amount 
				FROM
					app.t_trx_booking bok
					JOIN app.t_trx_booking_passanger b ON b.booking_code = bok.booking_code and passanger_type_id IN (1,2) -- AND b.status = 5 AND passanger_type_id IN (1,2)
					JOIN app.t_trx_boarding_passanger bo ON bo.ticket_number = b.ticket_number
					JOIN app.t_trx_open_boarding bb ON bb.boarding_code = bo.boarding_code 
				WHERE
					bok.service_id = 1
					AND bb.status = 0 
					AND bb.shift_date = '{$get[0]}' 
					AND bb.ship_class = {$get[1]} 
					AND bb.shift_id = {$get[2]}
				GROUP BY
					b.passanger_type_id,
					b.ifpro_fee) trx ON trx.passanger_type_id = pt.id WHERE id IN (1,2) ORDER BY pt.id";
					
		$data = array();
		$prod = 0;
		$pend = 0;

		foreach ($this->dbView->query($sql)->result() as $row) {
			$prod += $row->ticket_count;
			$pend += $row->total_amount;

			$data[] = $row;
		}


		return array(
			'data' => $data,
			'produksi' => $prod,
			'pendapatan' => $pend
		);
	}

	public function old_list_detail_vehicle($get){
		$sql = "SELECT name, ticket_count, ifpro_fee, total_amount 
				FROM app.t_mtr_vehicle_class vc
				LEFT JOIN (
				SELECT
					b.vehicle_class_id,
					COUNT ( DISTINCT b.ID ) AS ticket_count,
					b.ifpro_fee,
					COUNT ( DISTINCT b.ID ) * b.ifpro_fee AS total_amount 
				FROM
					app.t_trx_booking bok
					JOIN app.t_trx_booking_vehicle b ON b.booking_code = bok.booking_code -- AND b.status = 5
					JOIN app.t_trx_boarding_vehicle bo ON bo.ticket_number = b.ticket_number
					JOIN app.t_trx_open_boarding bb ON bb.boarding_code = bo.boarding_code 
				WHERE
					bok.service_id = 2
					AND bb.status = 0 
					AND bb.shift_date = '{$get[0]}' 
					AND bb.ship_class = {$get[1]} 
					AND bb.shift_id = {$get[2]}
				GROUP BY
					b.vehicle_class_id,
					b.ifpro_fee) trx ON trx.vehicle_class_id = vc.id ORDER BY vc.id ASC";

		$data = array();
		$prod = 0;
		$pend = 0;

		foreach ($this->dbView->query($sql)->result() as $row) {
			$prod += $row->ticket_count;
			$pend += $row->total_amount;

			$data[] = $row;
		}


		return array(
			'data' => $data,
			'produksi' => $prod,
			'pendapatan' => $pend
		);
	}

	public function list_detail_passanger($port_id,$datefrom,$dateto,$ship_class_id,$shift_id, $ticketType){
		$where_port = "";
		$where_ship_class = "";
		$where_shift = "";
		$whereManual = " ";

		if(!empty($ticketType)) // tipe tiket 3 adalah tiket manual
		{
			if($ticketType==3)
			{

				$whereManual = " and b.ticket_type='3' ";
			}
			else
			{
				$whereManual = " and b.ticket_type !='3' ";
			}
		}

		if ($port_id != "") {
			$where_port = " AND bb.port_id = $port_id";
		}

		if ($ship_class_id != "") {
			$where_ship_class = " AND bb.ship_class = $ship_class_id";
		}

		if ($shift_id != "") {
			$where_shift = " AND bb.shift_id = $shift_id";
		}

		$sql = "SELECT name, COALESCE(ticket_count,0) AS ticket_count, COALESCE(ifpro_fee,0) AS ifpro_fee, COALESCE(total_amount,0) AS  total_amount
				FROM app.t_mtr_passanger_type pt
				LEFT JOIN (
				SELECT
					b.passanger_type_id,
					COUNT ( DISTINCT b.ID ) AS ticket_count,
					b.ifpro_fee,
					COUNT ( DISTINCT b.ID ) * b.ifpro_fee AS total_amount 
				FROM
					app.t_trx_booking bok
					JOIN app.t_trx_booking_passanger b ON b.booking_code = bok.booking_code
					JOIN app.t_trx_boarding_passanger bo ON bo.ticket_number = b.ticket_number
					JOIN app.t_trx_open_boarding bb ON bb.boarding_code = bo.boarding_code 
				WHERE
					bok.service_id = 1
					AND bb.status = 0 
					AND bb.shift_date BETWEEN '$datefrom' AND '$dateto'
					AND bo.status = 1
					$where_port
					$where_ship_class 
					$where_shift
					$whereManual
				GROUP BY
					b.passanger_type_id,
					b.ifpro_fee) trx ON trx.passanger_type_id = pt.id 
					WHERE pt.id IN (1,2,3,4)
					ORDER BY pt.ordering ASC";
					
		$data = array();
		$prod = 0;
		$pend = 0;

		foreach ($this->dbView->query($sql)->result() as $row) {
			$prod += $row->ticket_count;
			$pend += $row->total_amount;

			$data[] = $row;
		}


		return array(
			'data' => $data,
			'produksi' => $prod,
			'pendapatan' => $pend
		);
	}

	public function list_detail_vehicle($port_id,$datefrom,$dateto,$ship_class_id,$shift_id,$ticketType ){
		$where_port = "";
		$where_ship_class = "";
		$where_shift = "";
		$whereManual = " ";

		if(!empty($ticketType)) // tipe tiket 3 adalah tiket manual
		{
			if($ticketType==3)
			{

				$whereManual = " and b.ticket_type='3' ";
			}
			else
			{
				$whereManual = " and b.ticket_type !='3' ";
			}
		}		

		if ($port_id != "") {
			$where_port = " AND bb.port_id = $port_id";
		}

		if ($ship_class_id != "") {
			$where_ship_class = " AND bb.ship_class = $ship_class_id";
		}

		if ($shift_id != "") {
			$where_shift = " AND bb.shift_id = $shift_id";
		}

		$sql = "SELECT name, COALESCE(ticket_count,0) AS ticket_count, COALESCE(ifpro_fee,0) AS ifpro_fee, COALESCE(total_amount,0) AS total_amount 
				FROM app.t_mtr_vehicle_class vc
				LEFT JOIN (
				SELECT
					b.vehicle_class_id,
					COUNT ( DISTINCT b.ID ) AS ticket_count,
					b.ifpro_fee,
					COUNT ( DISTINCT b.ID ) * b.ifpro_fee AS total_amount 
				FROM
					app.t_trx_booking bok
					JOIN app.t_trx_booking_vehicle b ON b.booking_code = bok.booking_code -- AND b.status = 5
					JOIN app.t_trx_boarding_vehicle bo ON bo.ticket_number = b.ticket_number
					JOIN app.t_trx_open_boarding bb ON bb.boarding_code = bo.boarding_code 
				WHERE
					bok.service_id = 2
					AND bb.status = 0 
					AND bb.shift_date BETWEEN '$datefrom' AND '$dateto'
					AND bo.status = 1
					$where_port 
					$where_ship_class
					$where_shift
				GROUP BY
					b.vehicle_class_id,
					b.ifpro_fee) trx ON trx.vehicle_class_id = vc.id ORDER BY vc.id ASC";

		$data = array();
		$prod = 0;
		$pend = 0;

		foreach ($this->dbView->query($sql)->result() as $row) {
			$prod += $row->ticket_count;
			$pend += $row->total_amount;

			$data[] = $row;
		}


		return array(
			'data' => $data,
			'produksi' => $prod,
			'pendapatan' => $pend
		);
	}

	public function headerku($port_id,$datefrom,$dateto,$ship_class_id,$shift_id)
	{
		$where_port = "";
		$where_ship_class = "";
		$where_shift_1 = "";
		$where_shift_2 = "";

		if ($port_id != "") {
			$where_port = " AND bor.port_id = $port_id";
		}

		if ($ship_class_id != "") {
			$where_ship_class = " AND bov.ship_class = $ship_class_id";
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
					$where_ship_class
					$where_shift_2";

		if ($this->dbView->query($sql)->num_rows() == 1) {
			return $this->dbView->query($sql)->row();
		}
	}

	public function getclass()
    {
        return $this->dbView->query("SELECT * FROM app.t_mtr_ship_class WHERE id=2")->result();
    }

}