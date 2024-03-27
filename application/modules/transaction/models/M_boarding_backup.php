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

class M_boarding extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/boarding';
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
			1 =>'created_on',
			2 =>'boarding_code',
			3 =>'schedule_date',
			4 =>'ship_name',
			5 =>'port_name',
			6 =>'dock_name',
			7 =>'port_destination',
			8 =>'ship_class_name',
			9 =>'sail_date',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status is not null ";

		if(!empty($port_origin))
		{
			$where .="and (e.port_id=".$port_origin.")";
		}

		if(!empty($port_destination))
		{
			$where .="and (e.destination_port_id=".$port_destination.")";
		}

		if (!empty($dateTo) and !empty($dateFrom))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		}
		else if(empty($dateFrom) and !empty($dateTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		}
		else if (!empty($dateFrom) and empty($dateTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		}
		else
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		}

		if (!empty($search['value'])){
			$where .="and (a.boarding_code ilike '%".$iLike."%')";	
		}

		$sql 		   = "
							select g.name as port_destination, e.sail_date, f.name as ship_name, d.name as ship_class_name ,
							 c.name as dock_name, b.name as port_name, a.* from app.t_trx_open_boarding a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_dock c on a.dock_id=c.id
							left join app.t_mtr_ship_class d on a.ship_class=d.id
							left join app.t_trx_schedule e on a.schedule_code=e.schedule_code
							left join app.t_mtr_ship f on a.ship_id=f.id
							left join app.t_mtr_port g on e.destination_port_id=g.id
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

			$code=$this->enc->encode($row->boarding_code);
			$detail_url 	= site_url($this->_module."/detail/{$code}");

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

     		$row->created_on=format_dateTime($row->created_on);
     		$row->port_origin=strtoupper($row->port_name);
     		$row->schedule_date=format_date($row->schedule_date);
     		$row->port_destination=strtoupper($row->port_destination);

     		     	
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

	public function list_detail_passanger($where=""){

		return $this->db->query("
		select l.name as passanger_type_name, b.ticket_number, c.booking_code, k.created_on as sail_date, j.name as ship_class_name, i.name as dock_name, h.name as port_destination,g.name as port_origin,
 		f.name as service_name,c.id_number, c.name as passanger_name,c.city,c.age, c.gender, d.name as ship_name,b.boarding_code, b.ticket_number,  a.* 
 		from app.t_trx_open_boarding a
		left join app.t_trx_boarding_passanger b on a.boarding_code=b.boarding_code
		left join app.t_trx_booking_passanger c on b.ticket_number=c.ticket_number
		left join app.t_mtr_ship d on a.ship_id=d.id
		left join app.t_trx_booking e on c.booking_code=e.booking_code
		left join app.t_mtr_service f on e.service_id=f.id
		left join app.t_mtr_port g on e.origin=g.id
		left join app.t_mtr_port h on e.destination=h.id
		left join app.t_mtr_dock i on a.dock_id=i.id
		left join app.t_mtr_ship_class j on a.ship_class=j.id
		left join app.t_trx_sail k on a.schedule_code=k.schedule_code
		left join app.t_mtr_passanger_type l on c.passanger_type_id=l.id
		$where
		order by l.name asc
		");
	}

	public function list_detail_passanger_vehicle($where=""){

		return $this->db->query("select m.id_number as plate_number, l.name as passanger_type_name, b.ticket_number, c.booking_code, k.created_on as sail_date, j.name as ship_class_name, i.name as dock_name, h.name as port_destination,g.name as port_origin,
 			f.name as service_name,c.id_number, c.name as passanger_name,c.city,c.age, c.gender, d.name as ship_name,b.boarding_code, b.ticket_number,  a.* 
 			from app.t_trx_open_boarding a
			left join app.t_trx_boarding_passanger b on a.boarding_code=b.boarding_code
			left join app.t_trx_booking_passanger c on b.ticket_number=c.ticket_number
			left join app.t_mtr_ship d on a.ship_id=d.id
			left join app.t_trx_booking e on c.booking_code=e.booking_code
			left join app.t_mtr_service f on e.service_id=f.id
			left join app.t_mtr_port g on e.origin=g.id
			left join app.t_mtr_port h on e.destination=h.id
			left join app.t_mtr_dock i on a.dock_id=i.id
			left join app.t_mtr_ship_class j on a.ship_class=j.id
			left join app.t_trx_sail k on a.schedule_code=k.schedule_code
			left join app.t_mtr_passanger_type l on c.passanger_type_id=l.id
			left join app.t_trx_booking_vehicle m on c.booking_code=m.booking_code
			$where
			order by l.name asc
			");
	}


	public function list_detail_vehicle($where=""){

		return $this->db->query("
			select c.id_number as plate_number, l.name as golongan, b.ticket_number, c.booking_code, k.created_on as sail_date, j.name as ship_class_name,
	 		i.name as dock_name, h.name as port_destination,g.name as port_origin,
	 		f.name as service_name,d.name as ship_name,b.boarding_code, b.ticket_number,  a.* 
	 		from app.t_trx_open_boarding a
			left join app.t_trx_boarding_vehicle b on a.boarding_code=b.boarding_code
			left join app.t_trx_booking_vehicle c on b.ticket_number=c.ticket_number
			left join app.t_mtr_ship d on a.ship_id=d.id
			left join app.t_trx_booking e on c.booking_code=e.booking_code
			left join app.t_mtr_service f on e.service_id=f.id
			left join app.t_mtr_port g on e.origin=g.id
			left join app.t_mtr_port h on e.destination=h.id
			left join app.t_mtr_dock i on a.dock_id=i.id
			left join app.t_mtr_ship_class j on a.ship_class=j.id
			left join app.t_trx_sail k on a.schedule_code=k.schedule_code
			left join app.t_mtr_vehicle_class l on c.vehicle_class_id=l.id
			$where
			order by l.name asc
							 ");
	}

	public function get_ship_name($boarding_code)
	{
		return $this->db->query("select b.name as ship_name, a.* from app.t_trx_open_boarding a
								left join app.t_mtr_ship b on a.ship_id=b.id where a.boarding_code='".$boarding_code."' ");
	}

	public function select_data($table, $where="")
	{
		return $this->db->query("select * from $table $where");
	}



}
