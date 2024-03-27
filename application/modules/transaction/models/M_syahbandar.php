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

class M_syahbandar extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/syahbandar';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');		
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));		
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

		// checking app ini dari cloude atau lokal
		$app_identity=$this->select_data("app.t_mtr_identity_app","")->row();


		if($app_identity->port_id==0 or empty($app_identity->port_id))
		{
			if(!empty($this->session->userdata('port_id')))
			{
				$port_origin = $this->session->userdata("port_id");
			}
			else
			{
				$port_origin = $this->enc->decode($this->input->post('port_origin'));
			}
		}
		else
		{
			$port_origin = $app_identity->port_id;	
		}

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
			10 =>'ket',
		);

		$order_column = $field[$order_column];


		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));
		$where = " WHERE a.status is not null and e.open_boarding_date >= '". $dateFrom . "' and e.open_boarding_date < '" . $dateToNew . "'";

		$whereSub=$where;

		if(!empty($port_origin))
		{
			$where .="and (e.port_id=".$port_origin.")";
		}

		if(!empty($port_destination))
		{
			$portDestination =$this->getDest(" where tmr.destination = ".$port_destination);						
			if($portDestination)
			{
				$where .="	
							and a.port_id in (". implode(",",array_column($portDestination,"origin")) .")
							and a.ship_class in (". implode(",",array_column($portDestination,"ship_class")) .") ";
			}
		}

		if(!empty($searchData))
		{
			if($searchName=="boardingCode")
			{
				$where .=" and  a.boarding_code = '".$iLike."' ";
			}
			else if($searchName=="shipName")
			{
				$searchDataShip = $this->dbView->query( "select name, id from app.t_mtr_ship  where name ilike '%".$iLike."%' " )->result();

				if($searchDataShip)
				{				
					$where .=" and e.ship_id in (". implode(",",array_column($searchDataShip,"id")).") ";
				}
				else
				{ $where .=" and a.id is null "; }
			}
			else
			{
				$where .="  ";	
			}
		}

		$sql 		   =$this->qry($where, $whereSub);
		$sqlCount  = $this->qryCount($where, $whereSub);

		$queryCount         = $this->dbView->query($sqlCount)->row();
		$records_total = $queryCount->countdata;

		// $query         = $this->db->query($sql);
		// $records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		$dataPort = $this->getMaster("t_mtr_port","id","name");
        $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataDock = $this->getMaster("t_mtr_dock ","id","name");
        $dataShipClass = $this->getMaster("t_mtr_ship_class ","id","name");
        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class ","id","name");

		$columnShipId = array_column($rows_data,"ship_id");
		$dataShip [""]=""; 
		if(!empty($columnShipId))
		{
			$shipIdArray = array_map(function($a){return "'".$a."'"; },$columnShipId);
			$searchDataShip = $this->dbView->query( "select name, id from app.t_mtr_ship  where id in (". implode(",",$shipIdArray) .") " )->result();
			$dataShip += array_combine(array_column($searchDataShip,"id"),array_column($searchDataShip,"name")); 
		}

		$columnShipClass = array_unique(array_column($rows_data,"ship_class"));
		$columnPort = array_unique(array_column($rows_data,"port_id"));
		$dataDestination=array();
		if(!empty($columnShipClass) && !empty($columnPort))
		{
			$qryDest ="
							where tmr.origin in (". implode(",",$columnPort) .")
							and tmrc.ship_class  in (". implode(",",$columnShipClass) .") 			
						";

			$searchDataDest = $this->getDest($qryDest);
			$columnO = array_column($searchDataDest,"origin");
			$columnS = array_column($searchDataDest,"ship_class");
			$columnD = array_column($searchDataDest,"destination");

			for($x = 0; $x< count($columnO); $x++)
			{
				$dataDestination[$columnO[$x]][$columnS[$x]] = $columnD[$x];
			}
		}		

		foreach ($rows_data as $row) {
			$row->number = $i;

			$code=$this->enc->encode($row->boarding_code);
			$detail_url 	= site_url($this->_module."/detail/{$code}");

			$row->actions="";
			$row->actions.= generate_button_new($this->_module, 'detail', $detail_url);

     		if (empty($row->ket))
     		{
     			$row->ket=failed_label("Belum Approve");	
     		}
     		else
     		{
     			$row->ket=success_label("Sudah Approve");
     		}


     		$row->created_on=format_dateTime($row->created_on);


     		$row->created_on=format_dateTime($row->created_on);
     		$row->open_boarding_date=empty($row->open_boarding_date)?"":format_dateTime($row->open_boarding_date);
     		$row->sail_date=empty($row->sail_date)?"":format_dateTime($row->sail_date);
     		$row->schedule_date=format_date($row->schedule_date);

			$row->port_destination = @$dataPort[$dataDestination[$row->port_id][$row->ship_class]];
			$row->ship_name =$dataShip[$row->ship_id];			
			$row->ship_class_name =$dataShipClass[$row->ship_class];
			$row->dock_name =$dataDock[$row->dock_id];
			$row->port_name =$dataPort[$row->port_id];


     		     	
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
			'data'           => $rows,
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
		);
	}

    public function dataApprove(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');		
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));

		$app_identity=$this->select_data("app.t_mtr_identity_app","")->row();

		if($app_identity->port_id==0 or empty($app_identity->port_id))
		{
			if(!empty($this->session->userdata('port_id')))
			{
				$port_origin = $this->session->userdata("port_id");
			}
			else
			{
				$port_origin = $this->enc->decode($this->input->post('port_origin'));
			}
		}
		else
		{
			$port_origin = $app_identity->port_id;	
		}

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
			10 =>'ket',
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status is not null and e.open_boarding_date >= '". $dateFrom . "' and e.open_boarding_date < '" . $dateToNew . "' and h.boarding_code is not null ";

		$whereSub = " ";


		if(!empty($port_origin))
		{
			$where .="and (e.port_id=".$port_origin.")";
		}

		if(!empty($searchData))
		{
			if($searchName=="boardingCode")
			{
				$where .=" and  a.boarding_code = '".$iLike."' ";
			}
			else if($searchName=="shipName")
			{
				$searchDataShip = $this->dbView->query( "select name, id from app.t_mtr_ship  where name ilike '%".$iLike."%' " )->result();

				if($searchDataShip)
				{				
					$where .=" and e.ship_id in (". implode(",",array_column($searchDataShip,"id")).") ";
				}
				else
				{ $where .=" and a.id is null "; }
			}
			else
			{
				$where .="  ";	
			}
		}


		if(!empty($searchData))
		{
			if($searchName=="boardingCode")
			{
				$where .=" and ( a.boarding_code ilike '%".$iLike."%') ";
			}
			else if($searchName=="shipName")
			{
				$where .=" and ( f.name ilike '%".$iLike."%') ";
			}
			else
			{
				$where .=" and ( d.name ilike '%".$iLike."%') ";	
			}
		}

		$sql 		   = $this->qry($where, $whereSub);
		$sqlCount  = $this->qryCount($where, $whereSub);

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

		$dataPort = $this->getMaster("t_mtr_port","id","name");
        $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataDock = $this->getMaster("t_mtr_dock ","id","name");
        $dataShipClass = $this->getMaster("t_mtr_ship_class ","id","name");
        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class ","id","name");

		$columnShipId = array_column($rows_data,"ship_id");
		$dataShip [""]=""; 
		if(!empty($columnShipId))
		{
			$shipIdArray = array_map(function($a){return "'".$a."'"; },$columnShipId);
			$searchDataShip = $this->dbView->query( "select name, id from app.t_mtr_ship  where id in (". implode(",",$shipIdArray) .") " )->result();
			$dataShip += array_combine(array_column($searchDataShip,"id"),array_column($searchDataShip,"name")); 
		}

		$columnShipClass = array_unique(array_column($rows_data,"ship_class"));
		$columnPort = array_unique(array_column($rows_data,"port_id"));
		$dataDestination=array();
		if(!empty($columnShipClass) && !empty($columnPort))
		{
			$qryDest ="
							where tmr.origin in (". implode(",",$columnPort) .")
							and tmrc.ship_class  in (". implode(",",$columnShipClass) .") 			
						";

			$searchDataDest = $this->getDest($qryDest);
			$columnO = array_column($searchDataDest,"origin");
			$columnS = array_column($searchDataDest,"ship_class");
			$columnD = array_column($searchDataDest,"destination");

			for($x = 0; $x< count($columnO); $x++)
			{
				$dataDestination[$columnO[$x]][$columnS[$x]] = $columnD[$x];
			}
		}		

		foreach ($rows_data as $row) {
			$row->number = $i;

			$code=$this->enc->encode($row->boarding_code);
			$detail_url 	= site_url($this->_module."/detail/{$code}");

			$row->actions="";
			$row->actions.= generate_button_new($this->_module, 'detail', $detail_url);

     		if (empty($row->ket))
     		{
     			$row->ket=failed_label("Belum Approve");	
     		}
     		else
     		{
     			$row->ket=success_label("Sudah Approve");
     		}


     		$row->created_on=format_dateTime($row->created_on);


     		$row->created_on=format_dateTime($row->created_on);
     		$row->open_boarding_date=empty($row->open_boarding_date)?"":format_dateTime($row->open_boarding_date);
     		$row->sail_date=empty($row->sail_date)?"":format_dateTime($row->sail_date);
     		$row->schedule_date=format_date($row->schedule_date);

			$row->port_destination = @$dataPort[$dataDestination[$row->port_id][$row->ship_class]];
			$row->ship_name =$dataShip[$row->ship_id];			
			$row->ship_class_name =$dataShipClass[$row->ship_class];
			$row->dock_name =$dataDock[$row->dock_id];
			$row->port_name =$dataPort[$row->port_id];


     		     	
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
			'data'           => $rows,
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
		);
	}


	public function list_detail_passanger($where=""){

		return $this->db->query("
				SELECT 
					l.name as passanger_type_name,
					 b.ticket_number, c.booking_code, 
					 k.sail_date, 
					 k.open_boarding_date, 
					(select gender from app.t_trx_booking_passanger
					where ticket_number=b.ticket_number and gender='L') as pria,
					(select gender from app.t_trx_booking_passanger
					where ticket_number=b.ticket_number and gender='P') as wanita,
					j.name as ship_class_name, i.name as dock_name, h.name as port_destination,g.name as port_origin,
					f.name as service_name,c.id_number, c.name as passanger_name,c.city,c.age, c.gender,
					d.name as ship_name,b.boarding_code,b.ticket_number, 
				(case
				when b.terminal_code is null then 'Manifest Susulan'
				else '' end) as manifest_data_from,
				a.* 
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
				left join app.t_trx_schedule k on a.schedule_code=k.schedule_code
				left join app.t_mtr_passanger_type l on c.passanger_type_id=l.id
				$where
				order by l.name asc
		");
	}

	public function list_detail_passanger_vehicle($where=""){

		return $this->db->query("SELECT 
			m.id_number as plate_number,
			l.name as passanger_type_name,
			b.ticket_number,
			c.booking_code,
			k.sail_date, 
			k.open_boarding_date, 
			j.name as ship_class_name,
			i.name as dock_name,
			h.name as port_destination,
			g.name as port_origin,
 			f.name as service_name,c.id_number, c.name as passanger_name,c.city,c.age, c.gender, d.name as ship_name,b.boarding_code, b.ticket_number,
 			(case
	 			when b.terminal_code is null then 'Manifest Susulan'
	 			else '' end
	 		) as manifest_data_from,
 			  a.* 
 			from app.t_trx_open_boarding a
			left join app.t_trx_boarding_vehicle b on a.boarding_code=b.boarding_code
			left join app.t_trx_booking_vehicle m on b.ticket_number=m.ticket_number
			left join app.t_trx_booking_passanger c on m.booking_code=c.booking_code
			left join app.t_mtr_ship d on a.ship_id=d.id
			left join app.t_trx_booking e on c.booking_code=e.booking_code
			left join app.t_mtr_service f on e.service_id=f.id
			left join app.t_mtr_port g on e.origin=g.id
			left join app.t_mtr_port h on e.destination=h.id
			left join app.t_mtr_dock i on a.dock_id=i.id
			left join app.t_mtr_ship_class j on a.ship_class=j.id
			-- left join app.t_trx_sail k on a.schedule_code=k.schedule_code
			left join app.t_trx_schedule k on a.schedule_code=k.schedule_code
			left join app.t_mtr_passanger_type l on c.passanger_type_id=l.id
			{$where}
			order by l.name asc
			");
	}

	public function list_detail_passanger_vehicle_05082021($where=""){

		return $this->db->query("SELECT 
			m.id_number as plate_number,
			l.name as passanger_type_name,
			b.ticket_number,
			c.booking_code,
			k.created_on as sail_date,
			j.name as ship_class_name,
			i.name as dock_name,
			h.name as port_destination,
			g.name as port_origin,
 			f.name as service_name,c.id_number, c.name as passanger_name,c.city,c.age, c.gender, d.name as ship_name,b.boarding_code, b.ticket_number,
 			(case
	 			when b.terminal_code is null then 'Manifest Susulan'
	 			else '' end
	 		) as manifest_data_from,
 			  a.* 
 			from app.t_trx_open_boarding a
			left join app.t_trx_boarding_vehicle b on a.boarding_code=b.boarding_code
			left join app.t_trx_booking_vehicle m on b.ticket_number=m.ticket_number
			left join app.t_trx_booking_passanger c on m.booking_code=c.booking_code
			left join app.t_mtr_ship d on a.ship_id=d.id
			left join app.t_trx_booking e on c.booking_code=e.booking_code
			left join app.t_mtr_service f on e.service_id=f.id
			left join app.t_mtr_port g on e.origin=g.id
			left join app.t_mtr_port h on e.destination=h.id
			left join app.t_mtr_dock i on a.dock_id=i.id
			left join app.t_mtr_ship_class j on a.ship_class=j.id
			left join app.t_trx_sail k on a.schedule_code=k.schedule_code
			left join app.t_mtr_passanger_type l on c.passanger_type_id=l.id
			{$where}
			order by l.name asc
			");
	}


	public function list_detail_vehicle_15012021($where=""){

		return $this->db->query("
			SELECT 
			(
				select count(ba.booking_code) from app.t_trx_boarding_passanger aa
				join app.t_trx_booking_passanger ba on aa.ticket_number=ba.ticket_number
				where ba.booking_code=c.booking_code
				and aa.status=1
			) as total_manifest_vehicle,
			e.total_passanger,
			c.id_number as plate_number, l.name as golongan, b.ticket_number, c.booking_code, k.created_on as sail_date, j.name as ship_class_name,
	 		i.name as dock_name, h.name as port_destination,g.name as port_origin,
	 		f.name as service_name,d.name as ship_name,b.boarding_code, b.ticket_number, 
			(case when b.terminal_code is null then 'Manifest Susulan'
			else '' end) as manifest_data_from, a.* 
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

	public function list_detail_vehicle_05082021($where=""){

		return $this->db->query("
			SELECT
			(
				select count(ba.booking_code) from app.t_trx_boarding_passanger aa
				join app.t_trx_booking_passanger ba on aa.ticket_number=ba.ticket_number
				where ba.booking_code=c.booking_code
				and aa.status=1
			) as total_manifest_vehicle,
			-- e.total_passanger,
			(select count(id) from app.t_trx_booking_passanger where booking_code=c.booking_code and status<>-5 ) as total_passanger,
			c.id_number as plate_number, l.name as golongan, b.ticket_number, c.booking_code, k.created_on as sail_date, j.name as ship_class_name,
	 		i.name as dock_name, h.name as port_destination,g.name as port_origin,
	 		f.name as service_name,d.name as ship_name,b.boarding_code, b.ticket_number,
	 		(case
				when b.terminal_code is null then 'Manifest Susulan'
			else '' end) as manifest_data_from,
	 		  a.* 
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

	public function list_detail_vehicle($where=""){

		return $this->db->query("
			SELECT
			(
				select count(ba.booking_code) from app.t_trx_boarding_passanger aa
				join app.t_trx_booking_passanger ba on aa.ticket_number=ba.ticket_number
				where ba.booking_code=c.booking_code
				and aa.status=1
			) as total_manifest_vehicle,
			-- e.total_passanger,
			(select count(id) from app.t_trx_booking_passanger where booking_code=c.booking_code and status<>-5 ) as total_passanger,
			c.id_number as plate_number, 
			l.name as golongan,
			b.ticket_number, 
			c.booking_code, 
			k.sail_date, 
			k.open_boarding_date, 
			j.name as ship_class_name,
	 		i.name as dock_name,
	 		h.name as port_destination,
	 		g.name as port_origin,
	 		f.name as service_name,
	 		d.name as ship_name,
	 		b.boarding_code,
	 		b.ticket_number,
	 		(case
				when b.terminal_code is null then 'Manifest Susulan'
			else '' end) as manifest_data_from,
	 		  a.* 
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
			left join app.t_trx_schedule k on a.schedule_code=k.schedule_code
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
		return $this->dbView->query("select * from $table $where");
	}
	public function select_data_field($table, $field, $where="")
	{
		return $this->dbView->query("select $field from $table $where");
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

	public function get_sum_penumpang_25052023($boarding_code,$gender,$type_id)
	{
		/*
		return $this->db->query(
		
			"select count(b.ship_name) as total_penumpang,
			 b.gender,
			  b.ship_name ,
			--a.name as tipe_name,
			b.ship_id, b.boarding_code
			--, a.id
			from app.t_mtr_passanger_type a
			left join(
				select br.boarding_code, br.ship_id, bk.passanger_type_id, sc.schedule_date,bk.gender, sp.name as ship_name 
				from app.t_trx_open_boarding br
				join app.t_trx_boarding_passanger bp on br.boarding_code=bp.boarding_code
				join app.t_trx_booking_passanger bk on bp.ticket_number=bk.ticket_number
				join app.t_trx_booking bo on bk.booking_code=bo.booking_code
				join app.t_trx_schedule sc on br.schedule_code=sc.schedule_code
				join app.t_mtr_ship sp on br.ship_id = sp.id
				where br.boarding_code='".$boarding_code."' 
				and bp.status=1 and bo.service_id=1
				and bk.gender  in ('".$gender."')
			) b on a.id=b.passanger_type_id
			where a.id  in (".$type_id.")  
			group by  b.ship_name , 
			--a.name , 
			b.ship_id, 
			b.boarding_code, 
			--a.id ,
			b.gender"
		);
		*/

		$qery="SELECT
					bp.passanger_type_id,
					bp.gender ,
					brp.boarding_code	
				from 
				from app.t_trx_boarding_passanger brp
				join  app.t_trx_booking_passanger bp brp.ticket_number = on bp.ticket_number
				where brp.boarding_code = '".$boarding_code."'
				and bp.depart_date in (".$type_id.") 
				";

		$data = $this->db->query($qery)->result();

		$pria = 0;
		$wanita = 0;
		if($data)
		{
			foreach ($data as $key => $value) {
				if($value->gender=='L')
				{
					$pria += 1;
				}
				else if($value->gender=='P')
				{
					$wanita += 1;
				}
			}
		}

		return array("L"=>$pria,"P"=>$wanita);

		

	}
	public function get_sum_penumpang($boarding_code,$type_id)
	{
		$query="SELECT
					bp.passanger_type_id,
					bp.gender ,
					brp.boarding_code	
				from 
				app.t_trx_boarding_passanger brp
				join  app.t_trx_booking_passanger bp on brp.ticket_number = bp.ticket_number
				where brp.boarding_code = '".$boarding_code."'
				and bp.passanger_type_id in (".$type_id.") 
				and bp.service_id =1
				";

		$data = $this->db->query($query)->result();

		$pria = 0;
		$wanita = 0;
		if($data)
		{
			foreach ($data as $key => $value) {
				if($value->gender=='L')
				{
					$pria += 1;
				}
				else if($value->gender=='P')
				{
					$wanita += 1;
				}
			}
		}

		$total = $pria + $wanita;
		return array("L"=>$pria,"P"=>$wanita,"total"=>$total);

		

	}

	public function get_sum_anak($boarding_code , $type_id)
	{

		return $this->db->query(
		
			"select count(b.ship_name) as total_penumpang, b.ship_name , a.name as tipe_name 
			,b.ship_id, b.boarding_code, a.id
			from app.t_mtr_passanger_type a
			left join(
				select br.boarding_code, br.ship_id, bk.passanger_type_id, sc.schedule_date,bk.gender, sp.name as ship_name 
				from app.t_trx_open_boarding br
				join app.t_trx_boarding_passanger bp on br.boarding_code=bp.boarding_code
				join app.t_trx_booking_passanger bk on bp.ticket_number=bk.ticket_number
				join app.t_trx_booking bo on bk.booking_code=bo.booking_code
				join app.t_trx_schedule sc on br.schedule_code=sc.schedule_code
				join app.t_mtr_ship sp on br.ship_id = sp.id
				where br.boarding_code='".$boarding_code."' 
				and bp.status=1 and bo.service_id=1
			) b on a.id=b.passanger_type_id
			where a.id  in (".$type_id.")  
			group by  b.ship_name , a.name , b.ship_id, b.boarding_code, a.id "
		);

	}


	public function get_detail($boarding_code)
	{
		return $this->db->query("
				SELECT 
					h.name as approve_name_ship, g.name as approved_name, f.created_on as jam_berangkat, e.created_on as jam_tiba,
					d.name as dock_name, c.name as port_name , b.name as ship_name ,a.* from app.t_trx_open_boarding a
				left join app.t_mtr_ship b on a.ship_id=b.id
				left join app.t_mtr_port c on a.port_id=c.id
				left join app.t_mtr_dock d on a.dock_id=d.id
				left join app.t_trx_docking e on a.schedule_code=e.schedule_code
				left join app.t_trx_sail f on a.schedule_code=f.schedule_code
				left join app.t_trx_approval_port_officer g on a.schedule_code=g.schedule_code
				left join app.t_trx_approval_ship_officer h on a.schedule_code=h.schedule_code
				where a.boarding_code='".$boarding_code."'
			");
	}

	public function get_sum_vehicle_25052023($boarding_code)
	{
		return $this->db->query("
				SELECT sum(b.total_passanger) as total_penumpang, count(b.ship_name) as total_kendaraan, b.ship_name , a.name as tipe_name 
		,b.ship_id, b.boarding_code, a.id
		from app.t_mtr_vehicle_class a
		left join(
			select tkv.total_passanger, br.boarding_code, br.ship_id, bk.vehicle_class_id, sc.schedule_date, sp.name as ship_name 
			from app.t_trx_open_boarding br
			join app.t_trx_boarding_vehicle bp on br.boarding_code=bp.boarding_code
			join(
				select count(bo2.booking_code) as total_passanger,bk.ticket_number from app.t_trx_open_boarding br
				join app.t_trx_boarding_vehicle bp on br.boarding_code=bp.boarding_code
				join app.t_trx_booking_vehicle bk on bp.ticket_number=bk.ticket_number
				join app.t_trx_booking_passanger bo2 on bk.booking_code=bo2.booking_code and bo2.status<>'-5'
				where br.boarding_code='{$boarding_code}' and  bp.status=1
				group by bk.ticket_number
			) tkv on bp.ticket_number=tkv.ticket_number		
			join app.t_trx_booking_vehicle bk on bp.ticket_number=bk.ticket_number
			join app.t_trx_booking bo on bk.booking_code=bo.booking_code
			join app.t_trx_schedule sc on br.schedule_code=sc.schedule_code
			join app.t_mtr_ship sp on br.ship_id = sp.id
			where br.boarding_code='{$boarding_code}' and bo.service_id=2 and bp.status=1

				) b on  a.id=b.vehicle_class_id 
				where a.status not in (-5)
				group by b.ship_name , a.name , b.ship_id, b.boarding_code, a.id
				order by id asc
		");
	}

	public function get_sum_vehicle($boarding_code)
	{
		return $this->db->query("SELECT
				sum(dt.total_pnp) as total_penumpang,
				count(dt.ticket_number)as total_kendaraan,
				tmvc.name as tipe_name,
				dt.ship_name,
				dt.ship_id,
				dt.boarding_code,
				tmvc.id
			from 
			app.t_mtr_vehicle_class tmvc 
			left join(
				select 
					(
						select count(bp.ticket_number) from app.t_trx_booking_passanger bp 
						where bp.booking_code = ttbv2.booking_code 
						and bp.status<>'-5'
					) as total_pnp,
					ttbv.boarding_code ,
					ttbv.ticket_number ,
					ttbv2.vehicle_class_id as id,
					br.ship_id ,		
					tms.name as ship_name
				from app.t_trx_boarding_vehicle ttbv 
				join app.t_trx_booking_vehicle ttbv2 on ttbv.ticket_number = ttbv2 .ticket_number 
				join app.t_trx_open_boarding br on ttbv .boarding_code = br.boarding_code 
				join app.t_mtr_ship tms on br.ship_id = tms.id 
				where ttbv.boarding_code='{$boarding_code}'
			)as dt on tmvc.id = dt.id
			where tmvc.status not in (-5)
			group by tmvc.name , dt.ship_name, dt.ship_id, dt.boarding_code, tmvc.id
			order by tmvc.id asc
		");
	}	


	public function qry_13062023($where, $whereSub="")
	{
		$data="
			SELECT 
				(
					case when h.boarding_code is null then 'Belum Approve'
					else 'Sudah Approve' end 
				) as ket,
				g.name as port_destination, 
				e.sail_date,
				e.open_boarding_date,
				f.name as ship_name,
				d.name as ship_class_name ,
				c.name as dock_name,
				b.name as port_name,
			a.* 
			from app.t_trx_open_boarding a
			join (
				select  min(a.boarding_code) as boarding_code, a.schedule_code  from app.t_trx_open_boarding a
				left join app.t_trx_schedule e on a.schedule_code=e.schedule_code
				{$whereSub}
				group by a.schedule_code
			) min_bc on a.boarding_code=min_bc.boarding_code			
			left join app.t_mtr_port b on a.port_id=b.id
			left join app.t_mtr_dock c on a.dock_id=c.id
			left join app.t_mtr_ship_class d on a.ship_class=d.id
			left join app.t_trx_schedule e on a.schedule_code=e.schedule_code
			left join app.t_mtr_ship f on a.ship_id=f.id
			left join app.t_mtr_port g on e.destination_port_id=g.id
			left join app.t_trx_approval_ship_officer h on a.boarding_code=h.boarding_code
			{$where}
		";

		// die($data); exit; 
		return $data; 
	}

	public function qryCount_13062023($where, $whereSub="")
	{
		return $data="
			SELECT 
				COUNT(a.id) as countdata
			from app.t_trx_open_boarding a
			join (
				select  min(a.boarding_code) as boarding_code, a.schedule_code  from app.t_trx_open_boarding a
				left join app.t_trx_schedule e on a.schedule_code=e.schedule_code
				{$whereSub}
				group by a.schedule_code
			) min_bc on a.boarding_code=min_bc.boarding_code			
			left join app.t_mtr_port b on a.port_id=b.id
			left join app.t_mtr_dock c on a.dock_id=c.id
			left join app.t_mtr_ship_class d on a.ship_class=d.id
			left join app.t_trx_schedule e on a.schedule_code=e.schedule_code
			left join app.t_mtr_ship f on a.ship_id=f.id
			left join app.t_mtr_port g on e.destination_port_id=g.id
			left join app.t_trx_approval_ship_officer h on a.boarding_code=h.boarding_code
			{$where}
		";
	}	

	public function qry($where, $whereSub="")
	{
			$data="SELECT 
						h.boarding_code as ket,			
						e.sail_date,
						a.ship_id,
						e.schedule_date,
						e.open_boarding_date,
						a.port_id ,
						a.dock_id,
						a.ship_class,
						a.schedule_code,
						a.boarding_code,
						a.plot_code,
						a.created_on,
						a.id
					from app.t_trx_open_boarding a
					join app.t_trx_schedule e on a.schedule_code=e.schedule_code
					left join app.t_trx_approval_ship_officer h on a.boarding_code=h.boarding_code
					{$where}			
				";			
		

		return $data;
	}

	public function qryCount($where, $whereSub="")
	{
		return $data="
						SELECT 
							count(a.id) as countdata
						from app.t_trx_open_boarding a
						join app.t_trx_schedule e on a.schedule_code=e.schedule_code
						left join app.t_trx_approval_ship_officer h on a.boarding_code=h.boarding_code
						{$where}

		";		
	}

	public function getDest($where)
	{
		$qry = "
					SELECT 
					tmr.origin , 
					tmr.destination , 
					tmrc.ship_class  
				from app.t_mtr_rute tmr 
				join app.t_mtr_rute_class tmrc on tmr.id = tmrc.rute_id 
				{$where}
			";	
		
		return	$this->dbView->query($qry)->result();

	}
	public function getMaster($table,$id,$name)
	{
		
		$service =  $this->select_data("app.$table"," where status != '-5' ")->result() ;
        $checkSession = $this->session->userdata("app.".$table); 

        if($checkSession)
        {
            $dataReturn = $checkSession;
        }
        else
        {

            $dataReturn=array();    
            foreach ($service as $key => $value) {
                $dataReturn[$value->$id]= $value->$name;
            }

            $this->session->set_userdata(array("app.".$table => $dataReturn));
        }

		return $dataReturn ;

	}    		

	public function get_detail_passanger_vehicle_25052023($boarding_code)
	{
		return $this->db->query("
				SELECT
					(select gender from app.t_trx_booking_passanger
					where ticket_number=bp.ticket_number and gender='L') as pria,
					(select gender from app.t_trx_booking_passanger
					where ticket_number=bp.ticket_number and gender='P') as wanita,
					bp.booking_code,
					-- b.total_passanger,
					( select count(id) from app.t_trx_booking_passanger where booking_code=bvc.booking_code and status<>-5) as total_passanger,
					bp.id_number, bp.name ,bp.city, bp.age, bp.gender, vc.name as vehicle_class_name,
					bh.id_number as plate_number, 
					(case when bv.terminal_code is null then 'Manifest Susulan'
					else '' end) as manifest_data_from,
					op.* from 
				app.t_trx_open_boarding op
				left join app.t_trx_boarding_vehicle bv on op.boarding_code=bv.boarding_code
				left join app.t_trx_booking_vehicle bvc on bv.ticket_number=bvc.ticket_number
				left join app.t_trx_booking_passanger bp on bvc.booking_code=bp.booking_code and bp.status <>'-5'
				left join app.t_trx_booking b on bp.booking_code=b.booking_code
				left join app.t_trx_booking_vehicle bh  on bp.booking_code=bh.booking_code
				left join app.t_mtr_vehicle_class vc on bh.vehicle_class_id=vc.id
				where bh.service_id=2 and op.boarding_code='{$boarding_code}' and bv.status=1
				and bp.id in ( select min(bp2.id) from app.t_trx_booking_passanger bp2 where bp2.booking_code=bp.booking_code )

			");
	}

	public function get_detail_passanger_vehicle($boarding_code)
	{
		$queryBoarding="SELECT 
						( 
							select 
								count(id) 
							from app.t_trx_booking_passanger 
							where booking_code=bp.booking_code and status<>-5
						) as total_passanger,					
						ttbv2.booking_code,
						ttbv2.id_number as plate_number,
						ttbv. boarding_code,	
						tms.name as vehicle_class_name,
						bp.id_number,
						bp.name ,
						bp.city, 
						bp.age, 
						bp.gender,
						(case when ttbv.terminal_code is null then 'Manifest Susulan' else '' end) as manifest_data_from	
					from app.t_trx_boarding_vehicle ttbv 
					join app.t_trx_booking_vehicle ttbv2 on ttbv.ticket_number = ttbv2 .ticket_number 
					join app.t_trx_open_boarding br on ttbv .boarding_code = br.boarding_code 
					join app.t_mtr_vehicle_class  tms on ttbv2.vehicle_class_id = tms.id 
					join app.t_trx_booking_passanger bp on ttbv2.booking_code  = bp.booking_code 
					where ttbv.boarding_code ='{$boarding_code}'
					and bp.status<>-5		
					order by ttbv.boarding_code asc, bp.ticket_number asc
		";

		// data ambil dari data pertama manifet booking passenger
		$dataBoarding = $this->db->query($queryBoarding)->result();
		
		$getBookingCode = array_unique(array_column($dataBoarding,"booking_code"));
		$dataSupir[""]="";
		// cari data nama supir
		if(!empty($getBookingCode))
		{
			$getBookingCodeString = array_map(function($a){ return "'".$a."'";  },$getBookingCode);
			$dataDrivers = $this->select_data_field("app.t_trx_print_manifest_vehicle", " booking_code, name"," where booking_code in (".implode(",",$getBookingCodeString ).")
			and driver = true ")->result();
			$dataSupir += array_combine(array_column($dataDrivers,"booking_code"), array_column($dataDrivers,"name") );
		}

		// print_r($dataBoarding); exit;
		$returnData = array();
		if($dataBoarding)
		{
			$lastBookingCode="";
			foreach ($dataBoarding as $key => $value) {
				$nowBookingCode=$value->booking_code;
				if($lastBookingCode != $nowBookingCode)
				{
					$value->name = empty($dataSupir[$value->booking_code])?$value->name:$dataSupir[$value->booking_code];					
					$returnData[]=(object)array(
						"total_passanger"=>$value->total_passanger,
						"booking_code"=>$value->booking_code,
						"plate_number"=>$value->plate_number,
						"boarding_code"=>$value->boarding_code,	
						"vehicle_class_name"=>$value->vehicle_class_name,
						"id_number"=>$value->id_number,
						"name"=>$value->name ,
						"city"=>$value->city, 
						"age"=>$value->age, 
						"gender"=>$value->gender,
						"manifest_data_from"=>$value->gender,
					);
				}
				$lastBookingCode = $nowBookingCode;
			}

		}

		// shorting by name
		usort($returnData, function ($a, $b) {
			return strcmp($a->name, $b->name);
		});

		return $returnData;
	}

	public function get_detail_passanger_vehicle2($boarding_code)
	{
		return $this->db->query("SELECT 
						( 
							select 
								count(id) 
							from app.t_trx_booking_passanger 
							where booking_code=bp.booking_code and status<>-5
						) as total_passanger,					
						ttbv2.booking_code,
						ttbv2.id_number as plate_number,
						ttbv. boarding_code,	
						tms.name as vehicle_class_name,
						bp.id_number,
						bp.name ,
						bp.city, 
						bp.age, 
						bp.gender,
						(case when ttbv.terminal_code is null then 'Manifest Susulan' else '' end) as manifest_data_from	
					from app.t_trx_boarding_vehicle ttbv 
					join app.t_trx_booking_vehicle ttbv2 on ttbv.ticket_number = ttbv2 .ticket_number 
					join app.t_trx_open_boarding br on ttbv .boarding_code = br.boarding_code 
					join app.t_mtr_vehicle_class  tms on ttbv2.vehicle_class_id = tms.id 
					join app.t_trx_booking_passanger bp on ttbv2.booking_code  = bp.booking_code 
					where ttbv.boarding_code ='{$boarding_code}'
					and bp.status<>-5		
					order by ttbv.boarding_code asc
			")->result();
	}	


	public function get_detail_passanger_vehicle2_25052023($boarding_code)
	{
		return $this->db->query("
			SELECT 
				(select gender from app.t_trx_booking_passanger
				where ticket_number=bp.ticket_number and gender='L') as pria,
				(select gender from app.t_trx_booking_passanger
				where ticket_number=bp.ticket_number and gender='P') as wanita,
				bp.booking_code,
				-- b.total_passanger,
				( select count(id) from app.t_trx_booking_passanger where booking_code=bvc.booking_code and status<>-5) as total_passanger,
				bp.id_number, bp.name ,bp.city, bp.age, bp.gender, vc.name as vehicle_class_name,
				bh.id_number as plate_number, 
				(case when bv.terminal_code is null then 'Manifest Susulan'
				else '' end) as manifest_data_from,
				op.* from 
			app.t_trx_open_boarding op
			left join app.t_trx_boarding_vehicle bv on op.boarding_code=bv.boarding_code
			left join app.t_trx_booking_vehicle bvc on bv.ticket_number=bvc.ticket_number
			left join app.t_trx_booking_passanger bp on bvc.booking_code=bp.booking_code and bp.status <>'-5'
			left join app.t_trx_booking b on bp.booking_code=b.booking_code
			left join app.t_trx_booking_vehicle bh  on bp.booking_code=bh.booking_code
			left join app.t_mtr_vehicle_class vc on bh.vehicle_class_id=vc.id
			where bh.service_id=2 and op.boarding_code='{$boarding_code}' and bv.status=1
			--and bp.id in ( select min(bp2.id) from app.t_trx_booking_passanger bp2 where bp2.booking_code=bp.booking_code )
			");
	}


	public function total_dalam_kendaraan($code)
	{
		return $this->db->query("

				select distinct da.booking_code, aa.boarding_code, da.total_passanger as tot_pass 
				from app.t_trx_open_boarding aa
				join app.t_trx_boarding_vehicle ba on aa.boarding_code=ba.boarding_code
				join app.t_trx_booking_vehicle bv on ba.ticket_number=bv.ticket_number
				join app.t_trx_booking_passanger ca on bv.booking_code=ca.booking_code
				join app.t_trx_booking da on ca.booking_code=da.booking_code
				where aa.boarding_code='".$code."' and da.service_id=2 and ba.status=1	and ca.status <>'-5'		
			");
	}	


	public function download(){

		$dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");       
        $port_destination=$this->enc->decode($this->input->get("port_destination"));
        $searchData=$this->input->get("searchData");
        $searchName=$this->input->get("searchName");
        $iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));

        $app_identity=$this->select_data("app.t_mtr_identity_app","")->row();

        if($app_identity->port_id==0 or empty($app_identity->port_id))
		{
	        if(!empty($this->session->userdata('port_id')))
			{
				$port_origin = $this->session->userdata("port_id");
			}
			else
			{
				$port_origin = $this->enc->decode($this->input->get('port_origin'));
			}
		}
		else
		{
			$port_origin = $app_identity->port_id;	
		}


		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));
		$where = " WHERE a.status is not null and e.open_boarding_date >= '". $dateFrom . "' and e.open_boarding_date < '" . $dateToNew . "'";
		$whereSub=$where;



		if(!empty($port_origin))
		{
			$where .="and (e.port_id=".$port_origin.")";
		}

		if(!empty($port_destination))
		{
			$portDestination =$this->getDest(" where tmr.destination = ".$port_destination);						
			if($portDestination)
			{
				$where .="	
							and a.port_id in (". implode(",",array_column($portDestination,"origin")) .")
							and a.ship_class in (". implode(",",array_column($portDestination,"ship_class")) .") ";
			}
		}

		if(!empty($searchData))
		{
			if($searchName=="boardingCode")
			{
				$where .=" and  a.boarding_code = '".$iLike."' ";
			}
			else if($searchName=="shipName")
			{
				$searchDataShip = $this->dbView->query( "select name, id from app.t_mtr_ship  where name ilike '%".$iLike."%' " )->result();

				if($searchDataShip)
				{				
					$where .=" and e.ship_id in (". implode(",",array_column($searchDataShip,"id")).") ";
				}
				else
				{ $where .=" and a.id is null "; }
			}
			else
			{
				$where .="  ";	
			}
		}

		$sql = $this->qry($where, $whereSub)." order by a.id desc ";

		$rows_data = $this->db->query($sql)->result();

		$dataPort = $this->getMaster("t_mtr_port","id","name");
        $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataDock = $this->getMaster("t_mtr_dock ","id","name");
        $dataShipClass = $this->getMaster("t_mtr_ship_class ","id","name");
        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class ","id","name");

		$columnShipId = array_column($rows_data,"ship_id");
		$dataShip [""]=""; 
		if(!empty($columnShipId))
		{
			$shipIdArray = array_map(function($a){return "'".$a."'"; },$columnShipId);
			$searchDataShip = $this->dbView->query( "select name, id from app.t_mtr_ship  where id in (". implode(",",$shipIdArray) .") " )->result();
			$dataShip += array_combine(array_column($searchDataShip,"id"),array_column($searchDataShip,"name")); 
		}

		$columnShipClass = array_unique(array_column($rows_data,"ship_class"));
		$columnPort = array_unique(array_column($rows_data,"port_id"));
		$dataDestination=array();
		if(!empty($columnShipClass) && !empty($columnPort))
		{
			$qryDest ="
							where tmr.origin in (". implode(",",$columnPort) .")
							and tmrc.ship_class  in (". implode(",",$columnShipClass) .") 			
						";

			$searchDataDest = $this->getDest($qryDest);
			$columnO = array_column($searchDataDest,"origin");
			$columnS = array_column($searchDataDest,"ship_class");
			$columnD = array_column($searchDataDest,"destination");

			for($x = 0; $x< count($columnO); $x++)
			{
				$dataDestination[$columnO[$x]][$columnS[$x]] = $columnD[$x];
			}
		}		

		foreach ($rows_data as $row) {

			$code=$this->enc->encode($row->boarding_code);
			$detail_url 	= site_url($this->_module."/detail/{$code}");

			$row->actions="";
			$row->actions.= generate_button_new($this->_module, 'detail', $detail_url);

     		if (empty($row->ket))
     		{
     			$row->ket="Belum Approve";	
     		}
     		else
     		{
     			$row->ket="Sudah Approve";
     		}


     		$row->created_on=format_dateTime($row->created_on);


     		$row->created_on=format_dateTime($row->created_on);
     		$row->open_boarding_date=empty($row->open_boarding_date)?"":format_dateTime($row->open_boarding_date);
     		$row->sail_date=empty($row->sail_date)?"":format_dateTime($row->sail_date);
     		$row->schedule_date=format_date($row->schedule_date);

			$row->port_destination = @$dataPort[$dataDestination[$row->port_id][$row->ship_class]];
			$row->ship_name =$dataShip[$row->ship_id];			
			$row->ship_class_name =$dataShipClass[$row->ship_class];
			$row->dock_name =$dataDock[$row->dock_id];
			$row->port_name =$dataPort[$row->port_id];

			$rows[] = $row;
			unset($row->id);
		}		

		return (object)$rows;
	}

	public function download2(){

		$dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");
        $port_destination=$this->enc->decode($this->input->get("port_destination"));
        $searchData=$this->input->get("searchData");
         $searchName=$this->input->get("searchName");
        $iLike        = trim($this->db->escape_like_str($searchData));


        if(!empty($this->session->userdata('port_id')))
		{
			$port_origin = $this->session->userdata("port_id");
		}
		else
		{
			$port_origin = $this->enc->decode($this->input->get('port_origin'));
		}


		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status is not null and e.open_boarding_date >= '". $dateFrom . "' and e.open_boarding_date < '" . $dateToNew . "' and (h.boarding_code is not null)";
		$whereSub = " ";
		// validasi jika dia operator kapal maka hanya operator kapal saja yang bisa liat kapal2nya


		if(!empty($port_origin))
		{
			$where .="and (e.port_id=".$port_origin.")";
		}

		if(!empty($port_destination))
		{
			$portDestination =$this->getDest(" where tmr.destination = ".$port_destination);						
			if($portDestination)
			{
				$where .="	
							and a.port_id in (". implode(",",array_column($portDestination,"origin")) .")
							and a.ship_class in (". implode(",",array_column($portDestination,"ship_class")) .") ";
			}
		}

	
		if(!empty($searchData))
		{
			if($searchName == "boardingCode")
			{
				$where .=" and  a.boarding_code = '".$iLike."' ";
			}
			else if($searchName=="shipName")
			{
				$searchDataShip = $this->dbView->query( "select name, id from app.t_mtr_ship  where name ilike '%".$iLike."%' " )->result();

				if($searchDataShip)
				{				
					$where .=" and e.ship_id in (". implode(",",array_column($searchDataShip,"id")).") ";
				}
				else
				{ $where .=" and a.id is null "; }
			}
			else
			{
				$where .="  ";	
			}
		}

		$sql = $this->qry($where, $whereSub)." order by a.id desc ";


		$rows_data = $this->db->query($sql)->result();

		$dataPort = $this->getMaster("t_mtr_port","id","name");
        $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataDock = $this->getMaster("t_mtr_dock ","id","name");
        $dataShipClass = $this->getMaster("t_mtr_ship_class ","id","name");
        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class ","id","name");

		$columnShipId = array_column($rows_data,"ship_id");
		$dataShip [""]=""; 
		if(!empty($columnShipId))
		{
			$shipIdArray = array_map(function($a){return "'".$a."'"; },$columnShipId);
			$searchDataShip = $this->dbView->query( "select name, id from app.t_mtr_ship  where id in (". implode(",",$shipIdArray) .") " )->result();
			$dataShip += array_combine(array_column($searchDataShip,"id"),array_column($searchDataShip,"name")); 
		}

		$columnShipClass = array_unique(array_column($rows_data,"ship_class"));
		$columnPort = array_unique(array_column($rows_data,"port_id"));
		$dataDestination=array();
		if(!empty($columnShipClass) && !empty($columnPort))
		{
			$qryDest ="
							where tmr.origin in (". implode(",",$columnPort) .")
							and tmrc.ship_class  in (". implode(",",$columnShipClass) .") 			
						";

			$searchDataDest = $this->getDest($qryDest);
			$columnO = array_column($searchDataDest,"origin");
			$columnS = array_column($searchDataDest,"ship_class");
			$columnD = array_column($searchDataDest,"destination");

			for($x = 0; $x< count($columnO); $x++)
			{
				$dataDestination[$columnO[$x]][$columnS[$x]] = $columnD[$x];
			}
		}		

		foreach ($rows_data as $row) {

			$code=$this->enc->encode($row->boarding_code);
			$detail_url 	= site_url($this->_module."/detail/{$code}");

			$row->actions="";
			$row->actions.= generate_button_new($this->_module, 'detail', $detail_url);

     		if (empty($row->ket))
     		{
     			$row->ket="Belum Approve";	
     		}
     		else
     		{
     			$row->ket="Sudah Approve";
     		}


     		$row->created_on=format_dateTime($row->created_on);


     		$row->created_on=format_dateTime($row->created_on);
     		$row->open_boarding_date=empty($row->open_boarding_date)?"":format_dateTime($row->open_boarding_date);
     		$row->sail_date=empty($row->sail_date)?"":format_dateTime($row->sail_date);
     		$row->schedule_date=format_date($row->schedule_date);

			$row->port_destination = @$dataPort[$dataDestination[$row->port_id][$row->ship_class]];
			$row->ship_name =$dataShip[$row->ship_id];			
			$row->ship_class_name =$dataShipClass[$row->ship_class];
			$row->dock_name =$dataDock[$row->dock_id];
			$row->port_name =$dataPort[$row->port_id];

			$rows[] = $row;
			unset($row->id);
		}		

		// print_r($rows); exit;
		return (object)$rows;
	}


}
