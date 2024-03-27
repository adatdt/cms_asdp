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

class M_addmanifest extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'manifest/add_manifest';
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

		$where = " WHERE a.status in (0,1) 
				and (to_char(a.created_on,'yyyy-mm-dd hh24:mi:ss') between '".date('Y-m-d H:i:s',strtotime("-4 hours"))."'
				 and '".date('Y-m-d H::s')."' )

		";

		if(!empty($port_origin))
		{
			$where .="and (e.port_id=".$port_origin.")";
		}

		if(!empty($port_destination))
		{
			$where .="and (e.destination_port_id=".$port_destination.")";
		}

		// if (!empty($dateTo) and !empty($dateFrom))
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		// }
		// else if(empty($dateFrom) and !empty($dateTo))
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		// }
		// else if (!empty($dateFrom) and empty($dateTo))
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		// }
		// else
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		// }

		if (!empty($search['value'])){
			$where .="and (a.boarding_code ilike '%".$iLike."%' 
							or d.name ilike '%".$iLike."%'
							or f.name ilike '%".$iLike."%' 

			 )";	
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
			// $detail_url 	= site_url($this->_module."/detail/{$code}");
			$add_url 	= site_url($this->_module."/add/{$code}");

     		$row->actions="";

     		if ($row->status==1)
     		{
     			$row->status=success_label("Aktif");
     		}
     		else
     		{
     			$row->status=failed_label("Tidak Aktif");	
     		}

     		// $row->actions.= generate_button_new($this->_module, 'detail', $detail_url);
     		$row->actions.= generate_button_new($this->_module, 'add', $add_url);

     		$row->created_on=format_dateTime($row->created_on);
     		$row->sail_date=empty($row->sail_date)?"":date("H:i:s",strtotime ($row->sail_date));
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


	public function get_passanger($ticket_number)
	{
		return $this->db->query("

			select e.name as ship_class_name, d.name as service_name, a.booking_code, c.name as passanger_type, b.* from app.t_trx_booking a
			join app.t_trx_booking_passanger b on a.booking_code=b.booking_code 
			join app.t_mtr_passanger_type c on b.passanger_type_id=c.id
			left join app.t_mtr_service d on a.service_id=d.id
			left join app.t_mtr_ship_class e on b.ship_class=e.id
			where b.ticket_number='".$ticket_number."' and a.service_id=1 and b.status !='-5' 

			");
	}

	// public function get_vehicle($ticket_number)
	// {
	// 	return $this->db->query("

	// 			select f.name as ship_class_name,  a.total_passanger, b.ticket_number as ticket_vehicle, d.name as vehicle_class_name,
	// 			b.id_number as plate_number, e.name as tipe_penumpang, c.* from app.t_trx_booking a
	// 			left join app.t_trx_booking_vehicle b on a.booking_code=b.booking_code
	// 			left join app.t_trx_booking_passanger c on a.booking_code=c.booking_code
	// 			left join app.t_mtr_vehicle_class d on b.vehicle_class_id=d.id
	// 			left join app.t_mtr_service e on a.service_id=e.id
	// 			left join app.t_mtr_ship_class f on b.ship_class=f.id
	// 			where b.ticket_number='".$ticket_number."' 
	// 			and a.service_id=2 

	// 		");
	// }


	public function get_vehicle($ticket_number)
	{
		return $this->db->query("

				SELECT 
				f.name as ship_class_name,  
				a.total_passanger, 
				b.ticket_number as ticket_vehicle,
				d.name as vehicle_class_name,
				b.id_number as plate_number,
				e.name as tipe_penumpang,
				c.id,
				c.id_number,
				c.id_image,
				c.name,
				c.birth_date,
				c.gender,
				c.created_by,
				c.created_on,
				c.updated_by,
				c.updated_on,
				c.passanger_type_ID,
				c.fare,
				c.ticket_number,
				c.city,
				c.id_type,
				c.age,
				c.booking_code,
				c.special_service_id,
				c.origin,
				c.destination,
				c.depart_date,
				c.depart_time,
				c.schedule_code,
				c.ship_class,
				c.depart_time_start,
				c.depart_time_end,
				c.service_id,
				c.gatein_expired,
				c.boarding_expired,
				c.checkin_expired,
				c.channel,
				d.status
				from app.t_trx_booking a
				left join app.t_trx_booking_vehicle b on a.booking_code=b.booking_code
				left join app.t_trx_booking_passanger c on a.booking_code=c.booking_code
				left join app.t_mtr_vehicle_class d on b.vehicle_class_id=d.id
				left join app.t_mtr_service e on a.service_id=e.id
				left join app.t_mtr_ship_class f on b.ship_class=f.id
				where b.ticket_number='".$ticket_number."' 
				and a.service_id=2 

			");
	}	

	public function get_pass_array($boarding_code)
	{
		return $this->db->query("
				select dock_id, port_id, schedule_date, boarding_code, ship_class from app.t_trx_open_boarding
				where boarding_code='".$boarding_code."' and status in (1,0);

			");
	}

	public function get_header($boarding_code)
	{
		return $this->db->query("
		select d.name as ship_class_name, c.name as ship_name, b.shift_name, a.* from app.t_trx_open_boarding a
		left join app.t_mtr_shift b on a.shift_id=b.id
		left join app.t_mtr_ship c on a.ship_id=c.id
		left join app.t_mtr_ship_class d on a.ship_class =d.id
		where a.boarding_code='".$boarding_code."' ");
	}

	public function get_status_boarding($boarding_code)
	{
		return $this->db->query("
			select b.* from app.t_trx_open_boarding a
			join app.t_trx_schedule b on a.schedule_code=b.schedule_code
			where a.boarding_code='".$boarding_code."'
		");
	}

	public function select_data($table, $where="")
	{
		return $this->db->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->db->insert($table, $data);
	}

	public function update_data($table,$data,$where)
	{
		$this->db->where($where);
		$this->db->update($table, $data);
	}

	public function delete_data($table,$data,$where)
	{
		$this->db->where($where);
		$this->db->delete($table, $data);
	}


}
