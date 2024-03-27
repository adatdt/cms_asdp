<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * -----------------------
 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  2021
 *
 */

class PassangerQuotaTrxModel extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module   = 'transaction2/vehicleTiketPP';
	}

	public function dataList()
	{
		$start 				= $this->input->post('start');
		$length 			= $this->input->post('length');
		$draw 				= $this->input->post('draw');
		$search 			= $this->input->post('search');

		$dateTo 			= trim($this->input->post('dateTo'));
		$dateFrom 			= trim($this->input->post('dateFrom'));
		$dataJam 			= trim($this->input->post('dataJam'));
		$shipClass			=$this->enc->decode($this->input->post("shipClass"));
		$port				=$this->enc->decode($this->input->post("port"));

		$order 				= $this->input->post('order');
		$order_column 		= $order[0]['column'];
		$order_dir 			= strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

		$field = array(
			0=> 'id',
			1=>"depart_date",
			2=>"depart_time",
			3=>"port_id",
			4=>"ship_class_id",
			5=>"quota",
			7=>"used_quota",
			8=>"total_quota",			
		);

		$order_column = $field[$order_column];

		$where = " WHERE ttqp.status != '-5'  and (date(ttqp.depart_date) between '" . $dateFrom . "' and '" . $dateTo . "' ) ";

		if(!empty($dataJam))
		{
			$where .= " and ttqp.depart_time between  '{$dataJam}:00' and '{$dataJam}:59' ";
		}


		if(!empty($port))
		{
			$where .= " and ttqp.port_id=".$port;
		}

		if(!empty($shipClass))
		{
			$where .= " and ttqp.ship_class=".$shipClass;
		}		

		if(!empty($searchData))
		{
			if($searchName=="bookingCode")
			{
				$where .= "and (bk.booking_code ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="ticketNumber")
			{
				$where .= "and (bvc.ticket_number ilike  '%" . $iLike . "%' )";
			}
			else if($searchName=="bookingCodePulang")
			{
				$where .= "and (bk.booking_code ilike  '%" . $iLike . "%' )";	
			}									
			else
			{
				$where .= "and (bk.booking_code ilike '%" . $iLike . "%')";
			}
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
			$row->number 		= $i;

			$row->depart_date= format_date($row->depart_date);

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

	public function qry($where)
	{
		return $data = 
			"
				SELECT 
					tmsc .name as ship_class_name,
					tmp.name port_name,
					ttqp .* 
				from app.t_trx_quota_passanger ttqp 
				left join app.t_mtr_ship_class tmsc on ttqp .ship_class =tmsc.id 
				left join app.t_mtr_port tmp on ttqp.port_id = tmp.id 
				{$where}
				";
	}

	public function get_identity_app()
	{
		$data = $this->dbView->query("select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}
}
