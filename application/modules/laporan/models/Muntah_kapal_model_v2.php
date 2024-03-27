<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Muntah_kapal_model_v2 extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'laporan/muntah_kapal_v2';
	}

	public function get_kapal_company($user_id)
	{
		return $this->dbView->query("SELECT
			tship.name,
			tship.id
			FROM
			app.t_mtr_user_ship tuship
			JOIN core.t_mtr_user usere ON usere.id = tuship.user_id
			JOIN app.t_mtr_ship tship ON tship.ship_company_id = tuship.company_id
			WHERE
			usere.id = $user_id")->result();
	}

    public function dataList(){
		$start 				= $this->input->post('start');
		$length 			= $this->input->post('length');
		$draw 				= $this->input->post('draw');
		$search 			= $this->input->post('search');
		$dateTo 			= trim($this->input->post('dateTo'));
		$dateFrom 			= trim($this->input->post('dateFrom'));
		$port 				= $this->enc->decode($this->input->post('port'));
		$ship 				= $this->enc->decode($this->input->post('ship'));
		$dock 				= $this->enc->decode($this->input->post('dock'));
		$order 				= $this->input->post('order');
		$order_column 		= $order[0]['column'];
		$order_dir 			= strtoupper($order[0]['dir']);
		$iLike        		= trim(strtoupper($this->dbView->escape_like_str($search['value'])));
		$group_id 			= $this->session->userdata('group_id');
		$user_id 			= $this->session->userdata('id');
		$session_shift_class = $this->session->userdata('ship_class_id');

		$field = array(
			1 => 'shift_date',
			2 => 'ship_name',
			3 => 'port_name',
			4 => 'ship_class',
			5 => 'dock_name',
			6 => 'shift_name',
		);

		$order_column = $field[$order_column];
		$where = " WHERE switch_ship_code IS NOT NULL";

		if(!empty($dateFrom) and !empty($dateTo)){
			$where .=" AND shift_date between '$dateFrom' AND '$dateTo'";   
		}

		if(!empty($port)){
			$where .=" AND PO.id = ".$port."";
		}

		if($ship){
			$where .=" AND SWP.ship_id=".$ship."";
		}

		if (!empty($search['value'])){
			$where .=" AND TMS.name ilike '%".$iLike."%'";	
		}

		if (!empty($session_shift_class)) {
			$where .= " and TMS.ship_class = {$session_shift_class}";
		}

		$sql = "SELECT
					DISTINCT(shift_date),
					ship_name,
					port_name,
					ship_class,
					dock_name,
					shift_name,
					switch_ship_code
				FROM(SELECT
						DISTINCT(TMS.id),
						SWP.shift_date,
						TMS.name AS ship_name,
						PO.name AS port_name,
						SHC.name AS ship_class,
						DOC.name AS dock_name,
						SH.shift_name,
						SWP.switch_ship_code
					FROM
						app.t_trx_switch_ship_passanger SWP
						JOIN app.t_mtr_ship TMS ON TMS.id = SWP.ship_id
						JOIN app.t_mtr_port PO ON PO.port_code = SWP.port_code
						JOIN app.t_mtr_shift SH ON SH.id = SWP.shift_id
						JOIN app.t_mtr_ship_class SHC ON SHC.id = TMS.ship_class
						JOIN app.t_trx_schedule TSC ON TSC.schedule_code = SWP.schedule_code
						JOIN app.t_mtr_dock DOC ON DOC.id = TSC.dock_id
					{$where}

					UNION ALL

					SELECT
						DISTINCT(TMS.id),
						SWP.shift_date,
						TMS.name AS ship_name,
						PO.name AS port_name,
						SHC.name AS ship_class,
						DOC.name AS dock_name,
						SH.shift_name,
						SWP.switch_ship_code
					FROM
						app.t_trx_switch_ship_vehicle SWP
						JOIN app.t_mtr_ship TMS ON TMS.id = SWP.ship_id
						JOIN app.t_mtr_port PO ON PO.port_code = SWP.port_code
						JOIN app.t_mtr_shift SH ON SH.id = SWP.shift_id
						JOIN app.t_mtr_ship_class SHC ON SHC.id = TMS.ship_class
						JOIN app.t_trx_schedule TSC ON TSC.schedule_code = SWP.schedule_code
						JOIN app.t_mtr_dock DOC ON DOC.id = TSC.dock_id
					{$where}) AA";

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

			$code 			= $this->enc->encode($row->switch_ship_code);
			$detail_url 	= site_url($this->_module."/detail/{$code}");
			$pdf_url 		= site_url($this->_module."/download_pdf/{$code}");
			$excel_url 		= site_url($this->_module."/download_excel/{$code}");

			$row->actions 	= "";
			$row->actions 	.= generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_pdf', $pdf_url);
			$row->actions 	.= generate_button_new($this->_module, 'download_excel', $excel_url);
			$row->created_on = date('d-m-Y H:i:s', strtotime($row->created_on));
			$row->shift_date = date('d-m-Y', strtotime($row->shift_date));
			$row->no=$i;

			$row->ship_name = $row->ship_name;
			$row->waktu_muntah = date("d M Y",strtotime($row->shift_date));

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

	public function get_lintasan($switch_ship_code)
	{

		$sql = "SELECT DISTINCT origin,
								destination,
								ORI.name as port_origin,
								DE.name as port_destination
				FROM (SELECT DISTINCT
							BO.origin,
							BO.destination 
						FROM
							app.t_trx_switch_ship_passanger SWP
							JOIN app.t_trx_switch_ship_passanger_detail SWPD ON SWPD.switch_ship_code = SWP.switch_ship_code
							JOIN app.t_trx_booking_passanger BP ON BP.ticket_number = SWPD.ticket_number
							JOIN app.t_trx_booking BO ON BO.booking_code = BP.booking_code
						WHERE
							SWP.switch_ship_code = '$switch_ship_code'
							
						UNION ALL

						SELECT DISTINCT
							BO.origin,
							BO.destination
						FROM
							app.t_trx_switch_ship_vehicle SWV
							JOIN app.t_trx_switch_ship_vehicle_detail SWVD ON SWVD.switch_ship_code = SWV.switch_ship_code
							JOIN app.t_trx_booking_vehicle BV ON BV.ticket_number = SWVD.ticket_number
							JOIN app.t_trx_booking BO ON BO.booking_code = BV.booking_code $where_port $where_ship_class
						WHERE
							SWV.switch_ship_code = '$switch_ship_code') A
				JOIN app.t_mtr_port ORI ON ORI.id = A.origin
				JOIN app.t_mtr_port DE ON DE.id = A.destination";

		if ($this->dbView->query($sql)->num_rows() == 1) {
			return $this->dbView->query($sql)->row();
		}else{
			return false;
		}
	}

	public function detail_trip($switch_ship_code){
		$sql = "SELECT
					DISTINCT(TMS.id),
					SWP.shift_date,
					TMS.name AS ship_name,
					PO.name AS port_name,
					SHC.name AS ship_class,
					DOC.name AS dock_name,
					SH.shift_name,
					SWP.switch_ship_code
				FROM
					app.t_trx_switch_ship_passanger SWP
					JOIN app.t_mtr_ship TMS ON TMS.id = SWP.ship_id
					JOIN app.t_mtr_port PO ON PO.port_code = SWP.port_code
					JOIN app.t_mtr_shift SH ON SH.id = SWP.shift_id
					JOIN app.t_mtr_ship_class SHC ON SHC.id = TMS.ship_class
					JOIN app.t_trx_schedule TSC ON TSC.schedule_code = SWP.schedule_code
					JOIN app.t_mtr_dock DOC ON DOC.id = TSC.dock_id
				WHERE
					SWP.switch_ship_code = '$switch_ship_code'";

		if ($this->dbView->query($sql)->num_rows() == 1) {
			return $this->dbView->query($sql)->row();
		}else{
			return false;
		}
	}

	public function list_detail_passanger($switch_ship_code){

		$sql = "SELECT
					PTT.id as idku,
					PTT.name as golongan,
					SUB.*
				FROM
					app.t_mtr_passanger_type PTT
					LEFT JOIN (SELECT
									DISTINCT( BP.fare ) AS harga,
									PT.ID,
									COUNT ( DISTINCT ( BP.ticket_number ) ) AS produksi,
									COUNT ( DISTINCT ( BP.ticket_number ) ) * BP.fare AS pendapatan 
								FROM
									app.t_trx_switch_ship_passanger SWP
									JOIN app.t_trx_switch_ship_passanger_detail SWPD ON SWPD.switch_ship_code = SWP.switch_ship_code
									JOIN app.t_trx_booking_passanger BP ON BP.ticket_number = SWPD.ticket_number
									JOIN app.t_trx_open_boarding boar on boar.boarding_code = SWP.boarding_code
									JOIN app.t_trx_assignment_regu reg on reg.assignment_date = boar.shift_date and reg.shift_id = boar.shift_id
									JOIN app.t_mtr_passanger_type PT ON PT.ID = BP.passanger_type_id
								WHERE
									BP.service_id = 1
									AND SWP.switch_ship_code = '{$switch_ship_code}'
								GROUP BY
									BP.fare,
									PT.ID) SUB ON SUB.id = PTT.id
					WHERE PTT.id IN(1,2,3)";

		$result = $this->dbView->query($sql);
				
		return $result->result();
	}

	public function list_detail_vehicle($switch_ship_code){

		$sql = "SELECT
					VCC.id as idku,
					VCC.name as golongan,
					SUB.*
				FROM
					app.t_mtr_vehicle_class VCC
					LEFT JOIN (SELECT
									DISTINCT( BV.fare ) AS harga,
									VC.ID,
									COUNT ( DISTINCT ( BV.ticket_number ) ) AS produksi,
									COUNT ( DISTINCT ( BV.ticket_number ) ) * BV.fare AS pendapatan 
								FROM
									app.t_trx_switch_ship_vehicle SWV
									JOIN app.t_trx_switch_ship_vehicle_detail SWVD ON SWVD.switch_ship_code = SWV.switch_ship_code
									JOIN app.t_trx_booking_vehicle BV ON BV.ticket_number = SWVD.ticket_number
									JOIN app.t_trx_open_boarding boar on boar.boarding_code = SWV.boarding_code
									JOIN app.t_trx_assignment_regu reg on reg.assignment_date = boar.shift_date and reg.shift_id = boar.shift_id
									JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
								WHERE
									SWV.switch_ship_code = '{$switch_ship_code}'
								GROUP BY
									BV.fare,
									VC.ID) SUB ON SUB.id = VCC.id
				WHERE
					VCC.status = 1
				ORDER BY
					idku ASC";

				$result = $this->dbView->query($sql);
				
				return $result->result();
	}

	public function select_data($table, $where="")
	{
		return $this->dbView->query("select * from $table $where");
	}
}