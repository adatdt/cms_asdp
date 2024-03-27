<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class M_boarding_passanger extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/boarding_passanger';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$port_origin = $this->enc->decode($this->input->post('port_origin'));
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$field = array(
			0 =>'id',
			1 =>'boarding_date',
			2 =>'port_name',
			3 =>'dock_name',
			4 =>'booking_code',
			5 =>'ticket_number',
			6 =>'passanger_name',
			7 =>'age',
			8 =>'gender',
			9 =>'passanger_type_name',
			10 =>'service_name',
			11 =>'ship_class_name',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status is not null and b.service_id=1 ";

		// if(!empty($service_id))
		// {
		// 	$where .="and (a.service_id=".$service_id.")";
		// }

		// if(!empty($port_origin))
		// {
		// 	$where .="and (a.origin=".$port_origin.")";
		// }

		// if(!empty($port_destination))
		// {
		// 	$where .="and (a.destination=".$port_destination.")";
		// }

		if (!empty($dateTo) and !empty($dateFrom))
		{
			$where .="and (to_char(a.boarding_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		}
		else if(empty($dateFrom) and !empty($dateTo))
		{
			$where .="and (to_char(a.boarding_date,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		}
		else if (!empty($dateFrom) and empty($dateTo))
		{
			$where .="and (to_char(a.boarding_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		}
		else
		{
			$where .="and (to_char(a.boarding_date,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		}

		if (!empty($search['value'])){
			$where .="and (b.booking_code ilike '%".$iLike."%' or e.name ilike '%".$iLike."%' or
							f.name ilike '%".$iLike."%' or d.name ilike '%".$iLike."%'
							or b.name ilike '%".$iLike."%' or c.name ilike '%".$iLike."%'
							or a.ticket_number ilike '%".$iLike."%'
							)";	
		}

		$sql 		   = "
							select g.name as ship_class_name, e.name as port_name , f.name as dock_name, b.booking_code, d.name as passanger_type_name,b.age,  b.name as passanger_name,
							 b.gender, c.name as service_name, a.* from app.t_trx_boarding_passanger a
							left join app.t_trx_booking_passanger b  on a.ticket_number=b.ticket_number 
							left join app.t_mtr_service c on b.service_id=c.id
							left join app.t_mtr_passanger_type d on b.passanger_type_id=d.id
							left join  app.t_mtr_port e on a.port_id=e.id
							left join app.t_mtr_dock f on a.dock_id=f.id
							left join app.t_mtr_ship_class g on a.ship_class=g.id							 
							$where
						 ";

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
			$row->number = $i;

			$id=$this->enc->encode($row->id);
			$detail_url 	= site_url($this->_module."/detail/{$id}");

     		$row->actions="";

     		if ($row->status==1)
     		{
     			$row->status=success_label("Aktif");
     		}
     		else
     		{
     			$row->status=failed_label("Tidak Aktif");	
     		}

     		$row->actions.= generate_button_new($this->_module, 'detail', $detail_url);

     		$row->boarding_date=format_dateTime($row->boarding_date);
     		$row->port_origin=strtoupper($row->port_name);

     		     	
     		// $row->created_on=format_dateTimeHis($row->created_on);
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

	public function listDetail($where=""){

		return $this->db->query("
							select g.name as service_name,f.name as shift_class_name, e.name as destination_name, d.name as origin_name, 
							c.name as special_service_name, b.name as passenger_type_name, a.* from app.t_trx_booking_passanger a
							left join  app.t_mtr_passanger_type b on a.passanger_type_id=b.id
							left join app.t_mtr_special_service c on a.special_service_id=c.id
							left join app.t_mtr_port d on a.origin=d.id
							left join app.t_mtr_port e on a.destination=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_mtr_service g on a.service_id=g.id	
							$where
							 ");
	}

	public function listVehicle($where=""){

		return $this->db->query("
							select g.name as service_name,f.name as shift_class_name, e.name as destination_name, d.name as origin_name,
							 b.name as vehicle_class_name, a.* from app.t_trx_booking_vehicle a
							left join  app.t_mtr_vehicle_class b on a.vehicle_class_id=b.id
							left join app.t_mtr_port d on a.origin=d.id
							left join app.t_mtr_port e on a.destination=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_mtr_service g on a.service_id=g.id	
							$where
							 ");
	}

	public function select_data($table, $where="")
	{
		return $this->db->query("select * from $table $where");
	}



}
