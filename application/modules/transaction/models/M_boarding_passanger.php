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
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateTo = $this->inputDate(trim($this->input->post('dateTo')));
		$dateFrom = $this->inputDate(trim($this->input->post('dateFrom')));
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData= $this->input->post("searchData");
		$searchName= $this->input->post("searchName");
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));


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
			2 =>'boarding_code',
			3 =>'port_name',
			4 =>'dock_name',
			5 =>'booking_code',
			6 =>'ticket_number',
			7 =>'passanger_name',
			8 =>'age',
			9 =>'gender',
			10 =>'passanger_type_name',
			11 =>'service_name',
			12 =>'ship_name',
			13 =>'ship_class_name',
			14 =>'terminal_name',
			15 =>'terminal_code',
		);

		$order_column = $field[$order_column];


		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE b.service_id=1 and a.status=1 and a.boarding_date >= '". $dateFrom . "' and a.boarding_date < '" . $dateToNew . "'";
		// $where = " WHERE b.service_id=1 and a.status=1 and (date(a.boarding_date) between '{$dateFrom}' and '{$dateTo}' ) ";


		if(!empty($port_id))
		{
			$where .="and (a.port_id=".$port_id.")";
		}


		if(!empty($searchData))
		{
			if($searchName=='bookingCode')
			{
				$where .=" and ( b.booking_code ilike '%".$iLike."%'  )";
			}
			else if($searchName=='boardingCode')
			{
				$where .=" and ( a.boarding_code ilike '%".$iLike."%'  )";
			}
			else if($searchName=='passName')
			{
				$where .=" and ( b.name ilike '%".$iLike."%'  )";
			}
			else if ($searchName=='ticketNumber')
			{
				$where .=" and (a.ticket_number ilike '%".$iLike."%' ) ";
			}

			else if ($searchName=='shipName')
			{
                $searchShip = $this->select_data("app.t_mtr_ship", " where name ilike '%". $iLike ."%' ")->result();
                if($searchShip)
                {
                    $getSearchShip = array_unique(array_column($searchShip,"id"));
                    $where .= " and h.ship_id in (".implode(",",$getSearchShip).") ";
                }
                else{
                    // default when there is not data 
                    $where .= " and a.id is null ";
                }
			}
			else
			{
				$searchTerminalCode = $this->select_data("app.t_mtr_device_terminal", " where terminal_name ilike '%". $iLike ."%' ")->result();
                if($searchTerminalCode)
                {
                    $getSearchTerminalCode = array_unique(array_column($searchTerminalCode,"terminal_code"));
                    $getSearchTerminalCode2 = array_map(function($a){ return "'".$a."'"; },$getSearchTerminalCode);
                    $where .= " and a.terminal_code in (".implode(",",$getSearchTerminalCode2).") ";
                }
                else{
                    // default when there is not data 
                    $where .= " and a.id is null ";
                }      
			}			
		}

		$sql 		   =$this->qry($where);



		$sqlCount 		   =$this->qryCount($where);
		$queryCount         = $this->dbView->query($sqlCount)->row();
		$records_total = $queryCount->countdata;		

		
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

        $getShipId = array_unique(array_column($rows_data,"ship_id"));        
        $dataShip=[];
        if(!empty($getShipId))
        {
            $selectShip = $this->select_data("app.t_mtr_ship","where id in (".implode(",",$getShipId).")")->result();   
            $dataShip = array_combine(array_column($selectShip,"id"),array_column($selectShip,"name"));
            
        }		

        $getPerangkat = array_unique(array_column($rows_data,"terminal_code"));
        $dataPerangkat=[];
        if(!empty($getPerangkat))
        {
            $terminalString = array_map(function($a){ return "'".$a."'"; },$getPerangkat);
            $selectPerangkat = $this->select_data("app.t_mtr_device_terminal","where terminal_code in (".implode(",",$terminalString).")")->result();              
            $dataPerangkat = array_combine(array_column($selectPerangkat,"terminal_code"),array_column($selectPerangkat,"terminal_name"));            
        }      		

		$dataPort = $this->getMaster("t_mtr_port","id","name");
        $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataDock = $this->getMaster("t_mtr_dock ","id","name");
        $dataShipClass = $this->getMaster("t_mtr_ship_class ","id","name");
        $dataPassangerType = $this->getMaster("t_mtr_passanger_type ","id","name");

		foreach ($rows_data as $row) {
			$row->number = $i;

			$row->port_name = @$dataPort[$row->port_id];
			$row->dock_name =  @$dataDock[$row->dock_id];
			$row->service_name = @$dataShipClass[$row->service_id];
			$row->ship_name = @$dataShip[$row->ship_id];
			$row->ship_class_name = @$dataService[$row->ship_class];
			$row->terminal_name = @$dataPerangkat[$row->terminal_code];
			$row->passanger_type_name = @$dataPassangerType[$row->passanger_type_id];


     		$row->boarding_date=format_dateTime($row->boarding_date);
     		$row->port_origin=strtoupper($row->port_name);

     		
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

	public function listDetail($where=""){

		return $this->dbView->query("
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

		return $this->dbView->query("
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
		return $this->dbView->query("select * from $table $where");
	}

    public function download(){

    	$dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");
        $searchData=$this->input->get("searchData");
        $searchName=$this->input->get("searchName");
        $iLike = trim($this->dbView->escape_like_str($searchData));

		if($this->get_identity_app()==0)
		{
			if (!empty($this->session->userdata("port_id")))
			{
				$port_id = $this->session->userdata("port_id");
			}
			else
			{
				$port_id = $this->enc->decode($this->input->get('port'));
			}
		}
		else
		{
			$port_id=$this->get_identity_app();
		}
		

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE b.service_id=1 and a.status=1 and a.boarding_date >= '". $dateFrom . "' and a.boarding_date < '" . $dateToNew . "'";

		if(!empty($port_id))
		{
			$where .="and (a.port_id=".$port_id.")";
		}


        if(!empty($searchData))
		{
			if($searchName=='bookingCode')
			{
				$where .=" and ( b.booking_code ilike '%".$iLike."%'  )";
			}
			else if($searchName=='boardingCode')
			{
				$where .=" and ( a.boarding_code ilike '%".$iLike."%'  )";
			}
			else if($searchName=='passName')
			{
				$where .=" and ( b.name ilike '%".$iLike."%'  )";
			}
			else if ($searchName=='ticketNumber')
			{
				$where .=" and (a.ticket_number ilike '%".$iLike."%' ) ";
			}

			else if ($searchName=='shipName')
			{
                $searchShip = $this->select_data("app.t_mtr_ship", " where name ilike '%". $iLike ."%' ")->result();
                if($searchShip)
                {
                    $getSearchShip = array_unique(array_column($searchShip,"id"));
                    $where .= " and h.ship_id in (".implode(",",$getSearchShip).") ";
                }
                else{
                    // default when there is not data 
                    $where .= " and a.id is null ";
                }
			}
			else
			{
				$searchTerminalCode = $this->select_data("app.t_mtr_device_terminal", " where terminal_name ilike '%". $iLike ."%' ")->result();
                if($searchTerminalCode)
                {
                    $getSearchTerminalCode = array_unique(array_column($searchTerminalCode,"terminal_code"));
                    $getSearchTerminalCode2 = array_map(function($a){ return "'".$a."'"; },$getSearchTerminalCode);
                    $where .= " and a.terminal_code in (".implode(",",$getSearchTerminalCode2).") ";
                }
                else{
                    // default when there is not data 
                    $where .= " and a.id is null ";
                }      
			}			
		}

		$sql 		   = $this->qry($where)." order by a.id desc";		
		$rows_data = $this->dbView->query($sql)->result();

  		$getShipId = array_unique(array_column($rows_data,"ship_id"));        
        $dataShip=[];
        if(!empty($getShipId))
        {
            $selectShip = $this->select_data("app.t_mtr_ship","where id in (".implode(",",$getShipId).")")->result();   
            $dataShip = array_combine(array_column($selectShip,"id"),array_column($selectShip,"name"));
            
        }		

        $getPerangkat = array_unique(array_column($rows_data,"terminal_code"));
        $dataPerangkat=[];
        if(!empty($getPerangkat))
        {
            $terminalString = array_map(function($a){ return "'".$a."'"; },$getPerangkat);
            $selectPerangkat = $this->select_data("app.t_mtr_device_terminal","where terminal_code in (".implode(",",$terminalString).")")->result();              
            $dataPerangkat = array_combine(array_column($selectPerangkat,"terminal_code"),array_column($selectPerangkat,"terminal_name"));            
        }      		

		$dataPort = $this->getMaster("t_mtr_port","id","name");
        $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataDock = $this->getMaster("t_mtr_dock ","id","name");
        $dataShipClass = $this->getMaster("t_mtr_ship_class ","id","name");
        $dataPassangerType = $this->getMaster("t_mtr_passanger_type ","id","name");	
		
		$rows = [];
		foreach ($rows_data as $row) {

			$row->port_name = @$dataPort[$row->port_id];
			$row->dock_name =  @$dataDock[$row->dock_id];
			$row->service_name = @$dataShipClass[$row->service_id];
			$row->ship_name = @$dataShip[$row->ship_id];
			$row->ship_class_name = @$dataService[$row->ship_class];
			$row->terminal_name = @$dataPerangkat[$row->terminal_code];
			$row->passanger_type_name = @$dataPassangerType[$row->passanger_type_id];

     		$rows[] = $row;
			unset($row->id);

		}		

		return (object)$rows;
	
	}

	public function qry($where)
	{
		$data="SELECT 
						a.terminal_code ,
						a.id,
						a.boarding_date,
						a.ticket_number,
						a.boarding_code,
						h.ship_id ,
						a.ship_class ,
						a.port_id ,
						a.dock_id,
						b.booking_code, 
						b.passanger_type_id,
						b.age,
						b.name as passanger_name, 
						b.gender, 
						b.service_id,
						(case when a.terminal_code is null then 'Manifest Susulan' else '' end) as manifest_data_from
					from app.t_trx_boarding_passanger a
					left join app.t_trx_booking_passanger b  on a.ticket_number=b.ticket_number 
					left join app.t_trx_open_boarding h on a.boarding_code=h.boarding_code
					{$where}
				";
		// die($data); exit;
		return $data;
	}

	public function qryCount($where)
	{
		$data="
					SELECT 
						count(a.id) countdata 
					from app.t_trx_boarding_passanger a
					left join app.t_trx_booking_passanger b  on a.ticket_number=b.ticket_number 
					left join app.t_trx_open_boarding h on a.boarding_code=h.boarding_code
					{$where}
				";

		return $data;
	}

	public function qry_06072023($where)
	{
		$data="
					SELECT j.terminal_name, i.name as ship_name, g.name as ship_class_name, e.name as port_name , f.name as dock_name, b.booking_code, d.name as passanger_type_name,b.age,
					b.name as passanger_name, b.gender, c.name as service_name, 
					(case when a.terminal_code is null then 'Manifest Susulan' else '' end) as manifest_data_from,
					a.* from app.t_trx_boarding_passanger a
					left join app.t_trx_booking_passanger b  on a.ticket_number=b.ticket_number 
					left join app.t_mtr_service c on b.service_id=c.id
					left join app.t_mtr_passanger_type d on b.passanger_type_id=d.id
					left join  app.t_mtr_port e on a.port_id=e.id
					left join app.t_mtr_dock f on a.dock_id=f.id
					left join app.t_mtr_ship_class g on a.ship_class=g.id
					left join app.t_trx_open_boarding h on a.boarding_code=h.boarding_code
					left join app.t_mtr_ship i on h.ship_id=i.id	
					left join app.t_mtr_device_terminal j on a.terminal_code=j.terminal_code	
					{$where}
				";
		die($data); exit;
		return $data;
	}

	public function qryCount_06072023($where)
	{
		$data="
					SELECT 
						count(a.id) countdata 
					from app.t_trx_boarding_passanger a
					left join app.t_trx_booking_passanger b  on a.ticket_number=b.ticket_number 
					left join app.t_mtr_service c on b.service_id=c.id
					left join app.t_mtr_passanger_type d on b.passanger_type_id=d.id
					left join  app.t_mtr_port e on a.port_id=e.id
					left join app.t_mtr_dock f on a.dock_id=f.id
					left join app.t_mtr_ship_class g on a.ship_class=g.id
					left join app.t_trx_open_boarding h on a.boarding_code=h.boarding_code
					left join app.t_mtr_ship i on h.ship_id=i.id	
					left join app.t_mtr_device_terminal j on a.terminal_code=j.terminal_code	
					{$where}
				";

		return $data;
	}



	public function get_identity_app()
	{
		$data=$this->dbView->query("select * from app.t_mtr_identity_app")->row();
		return $data->port_id;
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
    public function inputDate($date)
    {
        $dateNew = date("Y-m-d", strtotime($date));

        $data = $dateNew;
        if($dateNew != $date)
        {
            $data = date("Y-m-d");
        }
        return $data;
    }	


}
