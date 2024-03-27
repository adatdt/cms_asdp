<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : portConfigLogModel
 * -----------------------
 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  2023
 *
 */

class PortConfigLogModel extends MY_Model
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

		$port =$this->enc->decode($this->input->post('port'));
		$shipClass =$this->enc->decode($this->input->post('shipClass'));
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');	
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));



		$field = array(
			0 =>'id',
			1 =>"created_on",
			2 =>"pcis_code",
			3 =>"port_name",
			4 =>"ship_class_name",
			5 =>"request",
			6 =>"response",
			7 =>"description",
			8 =>"status",
		);

		$order_column = $field[$order_column];

		$where = " WHERE pc.created_on::DATE between '{$dateFrom}' and '{$dateTo}'  ";


		if(!empty($port))
		{
			$where .=" and pc.port_id='{$port}' ";
		}

		if(!empty($shipClass))
		{
			$where .=" and pc.ship_class='{$shipClass}' ";
		}

		if(!empty($searchData))
		{
			if($searchName=='pcisCode')
			{
				$where .=" and pc.pcis_code ilike '%{$iLike}%' ";
			}
			else if($searchName=='request')
			{
				$where .=" and pc.request ilike '%{$iLike}%' ";
			}
			else if($searchName=='response')
			{
				$where .=" and pc.response ilike '%{$iLike}%' ";
			}
			else
			{
				$where .="  ";
			}

		}


		$sql 		   = " 	
							select 
								tmp.name as port_name,
								tmsc.name as ship_class_name,
								pc.pcis_code,
								pc.request,
								pc.response,
								pc.type,
								pc.id,
								pc.description ,
								pc.status ,
								pc.created_on 
							from app.t_log_pcis pc
							join app.t_mtr_port tmp on pc.port_id = tmp.id 
							left join app.t_mtr_ship_class tmsc on pc.ship_class = tmsc.id
							$where
						 ";

		// die($sql); exit;

		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {

     		$row->no=$i;

			$row->request = "<div style='width:600px; font-size:12px; word-wrap: break-word ' >".$row->request."</div> ";
			$row->response = "<div style='width:600px; font-size:12px; word-wrap: break-word ' >".$row->response."</div> ";

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
		$qry=$this->db->query("			
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
		return $this->db->query("select * from $table $where");
	}


}
