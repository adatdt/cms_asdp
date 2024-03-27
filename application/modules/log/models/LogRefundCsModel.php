<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class LogRefundCsModel extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'transaction/booking';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');		
		$order = $this->input->post('order');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);

		$lintasan=$this->enc->decode($this->input->post('lintasan'));
		$service=$this->enc->decode($this->input->post('service'));
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');	
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));


		
		$field = array(
			0 =>'id',
			1=>'booking_code',
			2=>'refund_code',
			3=>'name',
			4=>'lintasan',
			5=>'bk.service_id',
			6=>'total_false'
		);

		$order_column = $field[$order_column];

		$where = " WHERE lr.status <> (-5) and lr.created_on::date between '{$dateFrom}' and '{$dateTo}' ";

		if(!empty($lintasan))
		{
			$explode=explode("|", $lintasan);
			$getOrigin=$explode[0];
			$getDestination=$explode[1];

			$where .=" and (bk.origin='{$getOrigin}' and bk.destination='{$getDestination}' ) ";
		}

		if(!empty($service))
		{
			$where .=" and (rf.service_id='{$service}'  ) ";	
		}

		if(!empty($searchData))
		{
			if($searchName=='bookingCode')
			{
				$where .=" and (lr.booking_code ilike '%{$iLike}%' ) ";
			}
			else if($searchName=='refundCode')
			{
				$where .=" and (rf.refund_code ilike '%{$iLike}%' ) ";
			}
			else
			{
				$where .=" and (rf.name ilike '%{$iLike}%' ) ";
			}

		}


		$sql 		   = " 	
							SELECT 	
								lr.booking_code,
								rf.refund_code,
								rf.name,
								concat(org.name,'-',ds.name) as lintasan,
								sv.name as service_name,
								x.total_false,
								lr.id
								FROM app.t_log_request_refund lr
								left join app.t_trx_refund rf on lr.booking_code=rf.booking_code
								left join app.t_trx_booking bk on lr.booking_code=bk.booking_code
								left join (
								SELECT 	
									(	10-
										((lr.indikator::json->>'r_bo_code')::int+
										(lr.indikator::json->>'r_cust_name')::int+
										(lr.indikator::json->>'r_vehic_class')::int+
										(lr.indikator::json->>'r_id_vehic')::int+
										(lr.indikator::json->>'r_fare')::int+
										(lr.indikator::json->>'r_date_bo')::int+
										(lr.indikator::json->>'r_date_depart')::int+
										(lr.indikator::json->>'r_port_origin')::int+
										(lr.indikator::json->>'r_phone_number')::int+
										(lr.indikator::json->>'r_email')::int)
									) as total_false,
									lr.booking_code,
									lr.id
									FROM app.t_log_request_refund lr
									WHERE lr.status <> (-5) and lr.created_on::date between '{$dateFrom}' and '{$dateTo}'
								)  x on lr.id=x.id
								join app.t_mtr_port org on bk.origin=org.id
								join app.t_mtr_port ds on bk.destination=ds.id
								join app.t_mtr_service sv on bk.service_id=sv.id
							$where
						 ";

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {

     		$row->no=$i;

			$rows[] = $row;

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	function getLintasan()
	{
		$qry=$this->dbView->query("			
			SELECT
				pt1.name as origin_name,
				pt2.name as destination_name,
				rt.origin,
				rt.destination
			from app.t_mtr_rute rt
			join app.t_mtr_port pt1 on rt.origin=pt1.id
			join app.t_mtr_port pt2 on rt.destination=pt2.id
			where rt.status <>'-5' order by pt1.name asc
		 ")->result();

		$data[]="Pilih";
		foreach ($qry as $key => $value) {
			
			$data[$this->enc->encode($value->origin."|".$value->destination)]=strtoupper($value->origin_name."-".$value->destination_name);
		}

		return $data;
	}

	public function select_data($table, $where="")
	{
		return $this->dbView->query("select * from $table $where");
	}


}
