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

class M_gate_in extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/gate_in';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$dateTo = $this->inputDate(trim($this->input->post('dateTo')));
		$dateFrom = $this->inputDate(trim($this->input->post('dateFrom')));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

		if($this->get_identity_app()==0)
		{
			if(!empty($this->session->userdata("port_id")))
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
			$port_id = $this->get_identity_app();
		}

		$field = array(
			0 =>'id',
			// 1 =>'created_on',
			// 2 =>'booking_code',
			// 3 =>'ticket_number',
			// 4 =>'id_number',
			// 5 =>'passanger_name',
			// 6 =>'service_name',
			// 7 =>'terminal_name',
			// 8 =>'port_name',
			// 9 =>'c.boarding_expired',
		);

		$order_column = $field[$order_column];


		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.id is not null and a.status<>'-6' and b.service_id='1' and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";

		if(!empty($port_id))
		{
			$where .="and (b.origin=".$port_id.")";	
		}

		if(!empty($searchData))
		{

			if($searchName=="bookingCode")
			{
				$where .= "and (b.booking_code ilike '%" . $iLike . "%')";

			}			
			else if($searchName=="passName")
			{
				$where .= "and (c.name ilike '%" . $iLike . "%')";
			}
			else if($searchName=="ticketNumber")
			{
				$where .= "and (a.ticket_number ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="deviceName")
			{
				$searchDevice = $getTerminal = $this ->select_data_field(" app.t_mtr_device_terminal "," terminal_code ", " where terminal_name ilike '%".$iLike."%'  ")->result();

                if(!empty($searchDevice))
                {
                    $searchDeviceString = array_map(function($a){ return "'".$a."'"; }, array_column($searchDevice,"terminal_code"));

                    $where .= " and a.terminal_code  in (".implode(",",$searchDeviceString).")";
                }
                else
                {
                    $where .= " and a.id is null ";
                }
			}
			else
			{
				$where .= "and (c.id_number ilike '%" . $iLike . "%')";
			}
		}				

		$sql 		   = $this->qry($where);
		$sqlCount = $this->qryCount($where);

		$countData         = $this->dbView->query($sqlCount)->row();
		$records_total = $countData->countdata;

		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

        //getterminalCode
        $columnTerminal = array_unique(array_column($rows_data,"terminal_code"));
        $masterTerminal[""]="";
        if(!empty($columnTerminal))
        {
            $columnTerminalString = array_map(function($a){ return "'".$a."'"; } , $columnTerminal );
            $getTerminal = $this ->select_data_field(" app.t_mtr_device_terminal "," terminal_code, terminal_name ", " where terminal_code in (".implode(",",$columnTerminalString).") ")->result();
            $masterTerminal = array_combine(array_column($getTerminal,"terminal_code"),array_column($getTerminal,"terminal_name"));
        }

        $dataPort = $this->getMaster("t_mtr_port","id","name"); 
        $dataService = $this->getMaster("t_mtr_service ","id","name");		

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$row->port_name = @$dataPort[$row->origin];
			$row->service_name = @$dataService[$row->service_id];
			$row->terminal_name = @$masterTerminal[$row->terminal_code];

     		// $row->actions.= generate_button_new($this->_module, 'detail', $detail_url);
     		$row->created_on=empty($row->created_on)?"":format_dateTimeHis($row->created_on);
     		$row->boarding_expired=empty($row->boarding_expired)?"":format_dateTimeHis($row->boarding_expired);
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

    public function select_data_field($table, $field , $where = "") {
        return $this->dbView->query("select $field from $table $where");
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
	public function download(){
	
		$dateFrom=$this->inputDate($this->input->get("dateFrom"));
        $dateTo=$this->inputDate($this->input->get("dateTo"));
        $search=$this->input->get("search");
		$searchData = $this->input->get('searchData');
		$searchName = $this->input->get('searchName');
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

		if($this->get_identity_app()==0)
		{
			if(!empty($this->session->userdata("port_id")))
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
			$port_id = $this->get_identity_app();
		}


		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.id is not null and a.status<>'-6' and b.service_id='1' and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";

		if(!empty($port_id))
		{
			$where .="and (b.origin=".$port_id.")";	
		}

		if(!empty($searchData))
		{

			if($searchName=="bookingCode")
			{
				$where .= "and (b.booking_code ilike '%" . $iLike . "%')";

			}			
			else if($searchName=="passName")
			{
				$where .= "and (c.name ilike '%" . $iLike . "%')";
			}
			else if($searchName=="ticketNumber")
			{
				$where .= "and (a.ticket_number ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="deviceName")
			{
				$searchDevice = $getTerminal = $this ->select_data_field(" app.t_mtr_device_terminal "," terminal_code ", " where terminal_name ilike '%".$iLike."%'  ")->result();

                if(!empty($searchDevice))
                {
                    $searchDeviceString = array_map(function($a){ return "'".$a."'"; }, array_column($searchDevice,"terminal_code"));

                    $where .= " and a.terminal_code  in (".implode(",",$searchDeviceString).")";
                }
                else
                {
                    $where .= " and a.id is null ";
                }
			}
			else
			{
				$where .= "and (c.id_number ilike '%" . $iLike . "%')";
			}
		}				

		$sql 		   = $this->qry($where)." order by a.id desc"; 
		$query         = $this->db->query($sql);
		$rows_data = $query->result();
        //getterminalCode
        $columnTerminal = array_unique(array_column($rows_data,"terminal_code"));
        $masterTerminal[""]="";
        if(!empty($columnTerminal))
        {
            $columnTerminalString = array_map(function($a){ return "'".$a."'"; } , $columnTerminal );
            $getTerminal = $this ->select_data_field(" app.t_mtr_device_terminal "," terminal_code, terminal_name ", " where terminal_code in (".implode(",",$columnTerminalString).") ")->result();
            $masterTerminal = array_combine(array_column($getTerminal,"terminal_code"),array_column($getTerminal,"terminal_name"));
        }

        $dataPort = $this->getMaster("t_mtr_port","id","name"); 
        $dataService = $this->getMaster("t_mtr_service ","id","name");	
		
		$rows = [];
		foreach ($rows_data as $row) {

			$row->port_name = @$dataPort[$row->origin];
			$row->service_name = @$dataService[$row->service_id];
			$row->terminal_name = @$masterTerminal[$row->terminal_code];

			$rows[] = $row;
			unset($row->id);

		}		

		return (object)$rows;

	}

	public function get_identity_app()
	{
		$data=$this->dbView->query("select * from app.t_mtr_identity_app")->row();
		return $data->port_id;
	}

	public function qry($where)
	{
		$data="SELECT 
							a.id,
							a.status,
							a.created_on,
							a.booking_code,
							a.ticket_number,
							c.boarding_expired, 
							b.origin,
							a.terminal_code ,
							b.service_id ,
							c.name as passanger_name, 
							c.id_number
						from app.t_trx_gate_in a
						left join app.t_trx_booking b on a.booking_code=b.booking_code
						left join app.t_trx_booking_passanger c on a.ticket_number=c.ticket_number
						{$where} ";

		// die($data); exit;
		return $data;
	} 

	public function qryCount($where)
	{
		$data="SELECT
						count(a.id) as countdata
						from app.t_trx_gate_in a
						left join app.t_trx_booking b on a.booking_code=b.booking_code
						left join app.t_trx_booking_passanger c on a.ticket_number=c.ticket_number
						{$where}
						";
		return $data;
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
