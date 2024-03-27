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
		// $dateTo = trim($this->input->post('dateTo'));
		// $dateFrom = trim($this->input->post('dateFrom'));
		
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));


		$field = array(
			0 =>'id',
			1 =>'created_on',
			2 =>'schedule_code',
			3 =>'boarding_code',
			4 =>'schedule_date',
			5 =>'ship_name',
			6 =>'port_name',
			7 =>'dock_name',
			8 =>'port_destination',
			9 =>'ship_class_name',
			10 =>'sail_date',
			11 =>'schedule_code',
		);

		$order_column = $field[$order_column];

		
		
		$where = " WHERE a.status in (0,1) and a.created_on >= '". date('Y-m-d H:i:s',strtotime("-4 hours")) . "' and a.created_on < '" . date('Y-m-d H:i:s') . "'";
		// $where = " WHERE a.status in (0,1) 
		// 		and (to_char(a.created_on,'yyyy-mm-dd hh24:mi:ss') between '".date('Y-m-d H:i:s',strtotime("-4 hours"))."'
		// 		 and '".date('Y-m-d H:i:s')."' )
		// ";

		if($this->get_identity_app()==0)
		{
			if(empty($this->session->userdata('port_id')))
			{
				$port_origin = $this->enc->decode($this->input->post('port_origin'));
			}
			else
			{
				$port_origin = $this->session->userdata('port_id');
			}
		}
		else
		{
			$port_origin = $this->get_identity_app();
		}


		if(!empty($port_origin))
		{
			$where .="and (e.port_id=".$port_origin.")";
		}

		if(!empty($port_destination))
		{
			$where .="and (e.destination_port_id=".$port_destination.")";
		}


		if (!empty($search['value'])){
			$where .="and (a.boarding_code ilike '%".$iLike."%' 
							or d.name ilike '%".$iLike."%'
							or f.name ilike '%".$iLike."%' 
							or a.schedule_code ilike '%".$iLike."%' 

			 )";	
		}

		if(!empty($searchData))
		{
			if($searchName=='boardingCode')
			{
				$where .=" and (a.boarding_code ilike '%".$iLike."%') ";
			}
			else if($searchName=='scheduleCode')
			{
				$where .=" and (a.schedule_code ilike '%".$iLike."%') ";
			}
			else if($searchName=='shipName')
			{
				$where .=" and (f.name ilike '%".$iLike."%') ";	
			}
			else
			{
					$where .="";	
			}			
		}

		$sql 		   = "
							SELECT g.name as port_destination, e.sail_date, f.name as ship_name, d.name as ship_class_name ,
							c.name as dock_name, b.name as port_name, 
							(case 
								when left(upper(e.schedule_code),1)='J' then 'Jadwal Normal'
							 	else 'Jadwal Tiket Manual'
							 end
							 ) as schedule_name,
							a.* from 
							app.t_trx_open_boarding a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_dock c on a.dock_id=c.id
							left join app.t_mtr_ship_class d on a.ship_class=d.id
							left join app.t_trx_schedule e on a.schedule_code=e.schedule_code
							left join app.t_mtr_ship f on a.ship_id=f.id
							left join app.t_mtr_port g on e.destination_port_id=g.id
							$where";


		$sqlCount = "SELECT count(a.id) as countdata from 
							app.t_trx_open_boarding a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_dock c on a.dock_id=c.id
							left join app.t_mtr_ship_class d on a.ship_class=d.id
							left join app.t_trx_schedule e on a.schedule_code=e.schedule_code
							left join app.t_mtr_ship f on a.ship_id=f.id
							left join app.t_mtr_port g on e.destination_port_id=g.id
						$where";
		// die($sqlCount);

		// $query         = $this->db->query($sql);
		// $records_total = $query->num_rows();

		$queryCount         = $this->db->query($sqlCount)->row();
		$records_total = $queryCount->countdata;
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

    public function dataListPassanger(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData =$this->input->post('searchData');
		$searchName =$this->input->post('searchName');
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));


		if($this->get_identity_app()==0)
		{
			if (!empty($this->session->userdata("port_id")))
			{
				$port_id = $this->session->userdata("port_id");
			}
			else
			{
				$port_id = $this->enc->decode($this->input->post('port'));
			}
		}
		else
		{
			$port_id=$this->get_identity_app();
		}



		$field = array(
			0 =>'id',
			1 =>'boarding_date',
			2 =>'schedule_code',
			3 =>'boarding_code',
			4 =>'port_name',
			5 =>'dock_name',
			6 =>'booking_code',
			7 =>'ticket_number',
			8 =>'passanger_name',
			9 =>'age',
			10 =>'gender',
			11 =>'passanger_type_name',
			12 =>'service_name',
			13 =>'ship_name',
			14 =>'ship_class_name',
			15 =>'terminal_name',
			16 =>'terminal_code',
		);

		$order_column = $field[$order_column];

		// $where = " WHERE b.service_id=1 and a.status=1 and (date(a.boarding_date) between '{$dateFrom}' and '{$dateTo}' ) ";

		// $where = " WHERE b.service_id=1 and a.status=1  ";

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status=1 and a.boarding_date >= '". $dateFrom . "' and a.boarding_date < '" . $dateToNew . "'";
		// $where = " WHERE a.status=1 and (date(a.boarding_date) between '{$dateFrom}' and '{$dateTo}' ) ";


		if(!empty($port_id))
		{
			$where .="and (a.port_id=".$port_id.")";
		}


		// if (!empty($search['value'])){
		// 	$where .="and (b.booking_code ilike '%".$iLike."%' 
		// 					or e.name ilike '%".$iLike."%' 
		// 					or f.name ilike '%".$iLike."%' 
		// 					or d.name ilike '%".$iLike."%'
		// 					or b.name ilike '%".$iLike."%' 
		// 					or c.name ilike '%".$iLike."%'
		// 					or a.ticket_number ilike '%".$iLike."%' 
		// 					or a.boarding_code ilike '%".$iLike."%'
		// 					or i.name ilike '%".$iLike."%' 
		// 					or j.terminal_name ilike '%".$iLike."%'
		// 					or h.schedule_code ilike '%".$iLike."%'
		// 					)";	
		// }


		if(!empty($searchData))
		{
			if($searchName=='boardingCode')
			{
				$where .=" and (a.boarding_code ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='scheduleCode')
			{
				$where .=" and (h.schedule_code ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='shipName')
			{
				$where .=" and (i.name ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='ticketNumber')
			{
				$where .=" and (a.ticket_number ilike '%".$iLike."%' ) ";
			}
			else if($searchName='passName')
			{
				$where .=" and (b.name ilike '%".$iLike."%' ) ";	
			}
			else
			{
				$where .=" ";
			}						
		}


		$sql 		   = $this->queryPassanger($where);
		$sqlCount	 = $this->queryPassangerCount($where);

		// $query         = $this->db->query($sql);
		// $records_total = $query->num_rows();
		$queryCount         = $this->db->query($sqlCount)->row();
		$records_total = $queryCount->countdata;

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

    public function dataListVehicle(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		// $port = $this->enc->decode($this->input->post('port'));
		// $port_destination = $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));

		if($this->get_identity_app()==0)
		{
			if (!empty($this->session->userdata("port_id")))
			{
				$port = $this->session->userdata("port_id");
			}
			else
			{
				$port = $this->enc->decode($this->input->post('port'));
			}
		}
		else
		{
			$port = $this->get_identity_app();
		}


		$field = array(
			0 =>'id',
			1 =>'boarding_date',
			2 =>'schedule_code',
			3 =>'boarding_code',
			4 =>'port_name',
			5 =>'dock_name',
			6 =>'booking_code',
			7 =>'ticket_number',
			8 =>'first_passanger',
			9 =>'id_number',
			10 =>'vehicle_class_name',
			11 =>'service_name',
			12 =>'ship_class_name',
			13 =>'ship_name',
			14 =>'terminal_name',
			15 =>'total_passanger',
			16 =>'terminal_code',
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status=1 and a.boarding_date >= '". $dateFrom . "' and a.boarding_date < '" . $dateToNew . "'";
		// $where = " WHERE a.status=1 and (date(a.boarding_date) between '{$dateFrom}' and '{$dateTo}' ) ";

		// if(!empty($service_id))
		// {
		// 	$where .="and (a.service_id=".$service_id.")";
		// }

		if(!empty($port))
		{
			$where .=" and (a.port_id=".$port.")";
		}

		// if (!empty($search['value'])){
		// 	$where .=" and (b.id_number ilike '%".$iLike."%'
		// 					or d.name ilike '%".$iLike."%' 
		// 					or f.name ilike '%".$iLike."%'
		// 					or i.name ilike '%".$iLike."%'
		// 					or e.name ilike '%".$iLike."%'
		// 					or b.booking_code ilike '%".$iLike."%'
		// 					or c.name ilike '%".$iLike."%' 
		// 					or a.boarding_code ilike '%".$iLike."%'
		// 					or a.ticket_number ilike '%".$iLike."%'	
		// 					or a.boarding_code ilike '%".$iLike."%'
		// 					or j.terminal_name ilike '%".$iLike."%'
		// 					or l.name ilike '%".$iLike."%'
		// 					or h.schedule_code ilike '%".$iLike."%'
		// 				)";	
		// }

		if(!empty($searchData))
		{
			if($searchName=='boardingCode')
			{
				$where .=" and (a.boarding_code ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='scheduleCode')
			{
				$where .=" and (h.schedule_code ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='shipName')
			{
				$where .=" and (i.name ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='ticketNumber')
			{
				$where .=" and (a.ticket_number ilike '%".$iLike."%' ) ";
			}
			else if($searchName='passName')
			{
				$where .=" and (l.name ilike '%".$iLike."%' ) ";	
			}
			else
			{
				$where .=" ";
			}						
		}



		$sql 		   = $this->queryVehicle($where);
		$sqlCount  = $this->queryVehicleCount($where);

		$queryCount         = $this->db->query($sqlCount)->row();
		$records_total = $queryCount->countdata;
		// $query         = $this->db->query($sql);
		// $records_total = $query->num_rows();
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

			$id=$this->enc->encode($row->booking_code);
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

     		$row->boarding_date=empty($row->boarding_date)?"":format_dateTime($row->boarding_date);
     		$row->port_origin=strtoupper($row->port_name);

     		// $row->actions.= generate_button_new($this->_module, 'detail', $detail_url);

     		     	
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
				b.status
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

	public function getTerminlaCode($portId)
	{
		// 15 hard cord terminal untuk manifest susulan
		$data=$this->db->query("SELECT a.* from app.t_mtr_device_terminal  a
										 left join app.t_mtr_device_terminal_type b on a.terminal_type=b.terminal_type_id
										where a.terminal_type=14 and a.port_id={$portId} AND a.status=1 and b.status=1 
										")->row();
		return $data->terminal_code;
	}

	Public function downloadPassanger()
	{

		$searchData = $this->input->get('searchData');
		$searchName = $this->input->get('searchName');
		$dateTo = trim($this->input->get('dateTo'));
		$dateFrom = trim($this->input->get('dateFrom'));
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));

		if($this->get_identity_app()==0)
		{
			if (!empty($this->session->userdata("port_id")))
			{
				$port = $this->session->userdata("port_id");
			}
			else
			{
				$port = $this->enc->decode($this->input->get('port'));
			}
		}
		else
		{
			$port = $this->get_identity_app();
		}


		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status=1 and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";
		// $where = " WHERE a.status=1 and (date(a.created_on) between '{$dateFrom}' and '{$dateTo}') ";

		if(!empty($port))
		{
			$where .=" and (a.port_id=".$port.")";
		}

		if (!empty($iLike)){
			$where .=" and (b.id_number ilike '%".$iLike."%'
							or d.name ilike '%".$iLike."%' 
							or f.name ilike '%".$iLike."%'
							or i.name ilike '%".$iLike."%'
							or e.name ilike '%".$iLike."%'
							or b.booking_code ilike '%".$iLike."%'
							or c.name ilike '%".$iLike."%' 
							or a.boarding_code ilike '%".$iLike."%'
							or a.ticket_number ilike '%".$iLike."%'	
							or a.boarding_code ilike '%".$iLike."%'
							or j.terminal_name ilike '%".$iLike."%'
							or l.name ilike '%".$iLike."%'
							or h.schedule_code ilike '%".$iLike."%'
						)";	
		}

		if(!empty($searchData))
		{
			if($searchName=='boardingCode')
			{
				$where .=" and (a.boarding_code ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='scheduleCode')
			{
				$where .=" and (h.schedule_code ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='shipName')
			{
				$where .=" and (i.name ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='ticketNumber')
			{
				$where .=" and (a.ticket_number ilike '%".$iLike."%' ) ";
			}
			else if($searchName='passName')
			{
				$where .=" and (b.name ilike '%".$iLike."%' ) ";	
			}
			else
			{
				$where .=" ";
			}						
		}		

		$qry = $this->queryPassanger($where);		

		$data = $this->db->query($qry)->result();



 	  	$file_name = 'Manifest Susulan Penumpang '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

        $header = array(
                        "NO"=>"string",
                        "TANGGAL BOARDING"=>"string",
                        "KODE JADWAL"=>"string",
                        "KODE BOARDING"=>"string",
                        "PELABUHAN"=>"string",
                        "DERMAGA"=>"string",
                        "KODE BOOKING"=>"string",
                        "NOMER TIKET"=>"string",
                        "NAMA PENUMPANG"=>"string",
                        "UMUR"=>"string",
                        "JENIS KELAMIN"=>"string",
                        "TIPE PENUMPANG"=>"string",
                        "SERVICE"=>"string",
                        "NAMA KAPAL"=>"string",
                        "TIPE KAPAL"=>"string",
                        "PERANGKAT BOARDING"=>"string",
        				);

        $no=1;
        foreach ($data as $key => $value) {

            $rows[] = array($no,
		                    $value->boarding_date,
		                    $value->schedule_code,
		                    $value->boarding_code,
		                    $value->port_name,
		                    $value->dock_name,
		                    $value->booking_code,
		                    $value->ticket_number,
		                    $value->passanger_name,
		                    $value->age,
		                    $value->gender,
		                    $value->passanger_type_name,
		                    $value->service_name,
		                    $value->ship_name,
		                    $value->ship_class_name,
		                    $value->terminal_name,
                        );
            $no++;
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header,$styles1);

        foreach($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();		
	}	

	Public function downloadVehicle()
	{

		$searchData = $this->input->get('searchData');
		$searchName = $this->input->get('searchName');		
		$dateTo = trim($this->input->get('dateTo'));
		$dateFrom = trim($this->input->get('dateFrom'));
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));


		if($this->get_identity_app()==0)
		{
			if (!empty($this->session->userdata("port_id")))
			{
				$port = $this->session->userdata("port_id");
			}
			else
			{
				$port = $this->enc->decode($this->input->post('port'));
			}
		}
		else
		{
			$port = $this->get_identity_app();
		}


		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status=1 and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";
		// $where = " WHERE atv.status=1 and (date(atv.created_on) between '{$dateFrom}' and '{$dateTo}') ";


		if(!empty($port))
		{
			$where .=" and (a.port_id=".$port.")";
		}

		// if (!empty($search['value'])){
		// 	$where .=" and (b.id_number ilike '%".$iLike."%'
		// 					or d.name ilike '%".$iLike."%' 
		// 					or f.name ilike '%".$iLike."%'
		// 					or i.name ilike '%".$iLike."%'
		// 					or e.name ilike '%".$iLike."%'
		// 					or b.booking_code ilike '%".$iLike."%'
		// 					or c.name ilike '%".$iLike."%' 
		// 					or a.boarding_code ilike '%".$iLike."%'
		// 					or a.ticket_number ilike '%".$iLike."%'	
		// 					or a.boarding_code ilike '%".$iLike."%'
		// 					or j.terminal_name ilike '%".$iLike."%'
		// 					or l.name ilike '%".$iLike."%'
		// 					or h.schedule_code ilike '%".$iLike."%'
		// 				)";	
		// }


		if(!empty($searchData))
		{
			if($searchName=='boardingCode')
			{
				$where .=" and (a.boarding_code ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='scheduleCode')
			{
				$where .=" and (h.schedule_code ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='shipName')
			{
				$where .=" and (i.name ilike '%".$iLike."%' ) ";
			}
			else if($searchName=='ticketNumber')
			{
				$where .=" and (a.ticket_number ilike '%".$iLike."%' ) ";
			}
			else if($searchName='passName')
			{
				$where .=" and (l.name ilike '%".$iLike."%' ) ";	
			}
			else
			{
				$where .=" ";
			}						
		}


		$sql 		   = $this->queryVehicle($where);

		$data         = $this->db->query($sql)->result();

 	  	$file_name = 'Manifest Susulan Kendaraan  '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

        $header = array(
                            "NO"=>"string",
                            "TANGGAL BOARDING"=>"string",
                            "KODE JADWAL"=>"string",
                            "KODE BOARDING"=>"string",
                            "PELABUHAN"=>"string",
                            "DERMAGA"=>"string",
                            "KODE BOOKING"=>"string",
                            "NOMER TIKET"=>"string",
                            "NAMA PENGEMUDI"=>"string",
                            "NOMER PLAT"=>"string",
                            "GOLONGAN KENDARAAN"=>"string",
                            "SERVIS"=>"string",
                            "TIPE KAPAL"=>"string",
                            "NAMA KAPAL"=>"string",
                            "PERANGKAT BOARDING"=>"string",
                            "TOTAL PENUMPANG"=>"string",
        				);

        $no=1;
        foreach ($data as $key => $value) {

            $rows[] = array($no,
		                    $value->boarding_date,
		                    $value->schedule_code,
		                    $value->boarding_code,
		                    $value->port_name,
		                    $value->dock_name,
		                    $value->booking_code,
		                    $value->ticket_number,
		                    $value->first_passanger,
		                    $value->id_number,
		                    $value->vehicle_class_name,
		                    $value->service_name,
		                    $value->ship_class_name,
		                    $value->ship_name,
		                    $value->terminal_name,
		                    $value->total_passanger,
                        );
            $no++;
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header,$styles1);

        foreach($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();		
	}	


	public function queryPassanger($where="")
	{
		$data="
			SELECT j.terminal_name, 
			i.name as ship_name, 
			g.name as ship_class_name,
			e.name as port_name , 
			f.name as dock_name, b.booking_code,
			d.name as passanger_type_name,
			b.age,
			b.name as passanger_name, 
			b.gender, 
			c.name as service_name,
			h.schedule_code,
			a.* 
			from
			app.t_trx_apend_ticket at
			left join app.t_trx_boarding_passanger a on at.boarding_code=a.boarding_code
			left join app.t_trx_booking_passanger b  on at.ticket_number=b.ticket_number 
			left join app.t_mtr_service c on b.service_id=c.id
			left join app.t_mtr_passanger_type d on b.passanger_type_id=d.id
			left join  app.t_mtr_port e on a.port_id=e.id
			left join app.t_mtr_dock f on a.dock_id=f.id
			left join app.t_mtr_ship_class g on a.ship_class=g.id
			left join app.t_trx_open_boarding h on at.boarding_code=h.boarding_code
			left join app.t_mtr_ship i on h.ship_id=i.id	
			left join app.t_mtr_device_terminal j on a.terminal_code=j.terminal_code
			{$where}
		";

		return $data;	
	}

	public function queryPassangerCount($where="")
	{
		$data="
			SELECT count(a.id) as countdata
			from
			app.t_trx_apend_ticket at
			left join app.t_trx_boarding_passanger a on at.boarding_code=a.boarding_code
			left join app.t_trx_booking_passanger b  on at.ticket_number=b.ticket_number 
			left join app.t_mtr_service c on b.service_id=c.id
			left join app.t_mtr_passanger_type d on b.passanger_type_id=d.id
			left join  app.t_mtr_port e on a.port_id=e.id
			left join app.t_mtr_dock f on a.dock_id=f.id
			left join app.t_mtr_ship_class g on a.ship_class=g.id
			left join app.t_trx_open_boarding h on at.boarding_code=h.boarding_code
			left join app.t_mtr_ship i on h.ship_id=i.id	
			left join app.t_mtr_device_terminal j on a.terminal_code=j.terminal_code
			{$where}
		";

		return $data;	
	}

	public function queryVehicle($where="")
	{
		$data 	= "
					SELECT
					l.name as first_passanger,
					k.total_passanger, 
					j.terminal_name,
					b.id_number,
					d.name as vehicle_class_name, 
					g.name as ship_class_name,
					e.name as port_name ,
					f.name as dock_name,
					h.schedule_code,
					b.booking_code,
					c.name as service_name,
					a.*, i.name AS ship_name from
					app.t_trx_apend_ticket_vehicle atv
					left join app.t_trx_boarding_vehicle a on atv.boarding_code= a.boarding_code
					left join app.t_trx_booking_vehicle b  on atv.ticket_number=b.ticket_number 
					left join app.t_mtr_service c on b.service_id=c.id
					left join app.t_mtr_vehicle_class d on b.vehicle_class_id=d.id
					left join  app.t_mtr_port e on a.port_id=e.id
					left join app.t_mtr_dock f on a.dock_id=f.id
					left join app.t_mtr_ship_class g on a.ship_class=g.id
					left join app.t_trx_open_boarding h on atv.boarding_code = h.boarding_code
					left join app.t_mtr_ship i on i.id = h.ship_id		
					left join app.t_mtr_device_terminal j on a.terminal_code=j.terminal_code
					left join app.t_trx_booking k on b.booking_code=k.booking_code
					left join
					(
					select distinct on (booking_code) booking_code, name,  min(ticket_number) from app.t_trx_booking_passanger
					where status !='-5'
					group by booking_code, ticket_number, name
					) l on b.booking_code=l.booking_code
					$where
				 ";

		return $data;
	}

	public function queryVehicleCount($where="")
	{
		$data 	= "
					SELECT count(a.id) as countdata
					 from
					app.t_trx_apend_ticket_vehicle atv
					left join app.t_trx_boarding_vehicle a on atv.boarding_code= a.boarding_code
					left join app.t_trx_booking_vehicle b  on atv.ticket_number=b.ticket_number 
					left join app.t_mtr_service c on b.service_id=c.id
					left join app.t_mtr_vehicle_class d on b.vehicle_class_id=d.id
					left join  app.t_mtr_port e on a.port_id=e.id
					left join app.t_mtr_dock f on a.dock_id=f.id
					left join app.t_mtr_ship_class g on a.ship_class=g.id
					left join app.t_trx_open_boarding h on atv.boarding_code = h.boarding_code
					left join app.t_mtr_ship i on i.id = h.ship_id		
					left join app.t_mtr_device_terminal j on a.terminal_code=j.terminal_code
					left join app.t_trx_booking k on b.booking_code=k.booking_code
					left join
					(
					select distinct on (booking_code) booking_code, name,  min(ticket_number) from app.t_trx_booking_passanger
					where status !='-5'
					group by booking_code, ticket_number, name
					) l on b.booking_code=l.booking_code
					$where
				 ";

		return $data;
	}

	public function get_identity_app()
	{
		return $this->db->query("select * from app.t_mtr_identity_app")->row()->port_id;
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
