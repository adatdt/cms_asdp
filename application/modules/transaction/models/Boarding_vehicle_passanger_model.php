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

 /*
    enchance 14-7-2023
    - penambahan crsf token 
    - validasi datatable
    - penambahan db->escape


 */

class Boarding_vehicle_passanger_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_module = 'transaction/boarding_vehicle';
    }

    public function dataList() {
        $start        = $this->input->post('start');
        $length       = $this->input->post('length');
        $draw         = $this->input->post('draw');
        $service_id   = $this->enc->decode($this->input->post('service'));
        $dateTo       = trim($this->input->post('dateTo'));
        $dateFrom     = trim($this->input->post('dateFrom'));
        $order        = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir    = strtoupper($order[0]['dir']);
        $searchData   = $this->input->post('searchData');
        $searchName   = $this->input->post('searchName');
        $iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

        // $vehicleClass = $this->enc->decode($this->input->post('vehicleClass'));

        if ($this->get_identity_app() == 0) {
            if (!empty($this->session->userdata("port_id"))) {
                $port = $this->session->userdata("port_id");
            } else {
                $port = $this->enc->decode($this->input->post('port'));
            }
        } else {
            $port = $this->get_identity_app();
        }

        $field = [
            0  => 'id',
        ];

        $order_column = $field[$order_column];

        $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

        $where    = " WHERE b.service_id=2 and a.status=1 and a.boarding_date >= ".$this->db->escape($dateFrom) . " and a.boarding_date < " . $this->db->escape($dateToNew) ;
        $whereSearch = $where ;


        if (!empty($port)) {
            $where .= " and a.port_id=" . $this->db->escape($port) ;        
        }
        /*
            if (!empty($vehicleClass)) {
                $where .= " and b.vehicle_class_id=" . $this->db->escape($vehicleClass) ;  
            } 
        */                     

        if (!empty($searchData)) {
            if ($searchName == 'bookingCode') 
            {
                $where .= " and  b.booking_code  =" .$this->db->escape($iLike);
            } 
            else if ($searchName == 'boardingCode') 
            {
                $where .= " and  a.boarding_code = " .$this->db->escape($iLike);
            } 
            else if ($searchName == 'passName') 
            {
                $where .= " and  bp.name  ilike '%".$iLike."%'  ESCAPE '!'  ";
            } 
            else if ($searchName == 'ticketNumber') {
                $where .= " and  a.ticket_number = '" . $iLike . "'   ";
            } 
            else if ($searchName == 'shipName') 
            {
                $searchShip = $this->select_data("app.t_mtr_ship", " where name ilike '%". $iLike ."%'  ESCAPE '!'  ")->result();
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
            else if ($searchName == 'deviceName') 
            {
                $searchTerminalCode = $this->select_data("app.t_mtr_device_terminal", " where terminal_name ilike '%". $iLike ."%'  ESCAPE '!'  ")->result();
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
            else {
                $where .= " and ( b.id_number ilike '%" . $iLike . "%'   ESCAPE '!'  ) ";
            }
        }
        $sql = $this->qry($where);
        // die($sql); exit;
        $sqlCount = $this->qryCount($where);


        $queryCount    = $this->dbView->query($sqlCount)->row();
        $records_total = $queryCount->countdata;

        $sql .= " ORDER BY " . $order_column . " {$order_dir}";

        if ($length != -1) {
            $sql .= " LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->dbView->query($sql);
        $rows_data = $query->result();
        
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
        
        // getDrivers
        $columnBooking = array_unique(array_column($rows_data,"booking_code"));
                
        $rows = [];
        $i    = ($start + 1);

        $dataPort = $this->getMaster("t_mtr_port","id","name");
        $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataDock = $this->getMaster("t_mtr_dock ","id","name");
        $dataShipClass = $this->getMaster("t_mtr_ship_class ","id","name");
        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class ","id","name");
        $dataPasserType = $this->getMaster("t_mtr_passanger_type ","id","name");

        foreach ($rows_data as $row) {
            $row->number = $i;

            $id         = $this->enc->encode($row->booking_code);
            $row->actions = "";

            $row->boarding_date = empty($row->boarding_date) ? "" : format_dateTime($row->boarding_date);

            $row->port_name = $dataPort[$row->port_id];
            $row->service_name = $dataService[$row->service_id];
            $row->vehicle_class_name = $dataVehicleClass[$row->vehicle_class_id];
            $row->ship_class_name = $dataShipClass[$row->ship_class];
            $row->dock_name = $dataDock[$row->dock_id];
            $row->ship_name = $dataShip[$row->ship_id];
            $row->terminal_name = empty($dataPerangkat[$row->terminal_code])?"":$dataPerangkat[$row->terminal_code];
            $row->passanger_type_name = @$dataPasserType[$row->passanger_type_id];

            $row->ticket_number = "<a href='".site_url("transaction/ticket_tracking/index/".$row->ticket_number)."' target='_blank' >".$row->ticket_number."</a>";


            $row->no = $i;

            $rows[] = $row;
            unset($row->id);

            $i++;
        }

        return [
            'draw'            => $draw,
            'recordsTotal'    => $records_total,
            'recordsFiltered' => $records_total,
            'data'            => $rows,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
        ];
    }

    public function listVehicle($where = "") {

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

    public function select_data($table, $where = "") {
        return $this->dbView->query("select * from $table $where");
    }
    public function select_data_field($table, $field , $where = "") {
        return $this->dbView->query("select $field from $table $where");
    }    
    public function download() {

        $dateFrom   = $this->input->get("dateFrom");
        $dateTo     = $this->input->get("dateTo");
        $searchData = $this->input->get("searchData");
        $searchName = $this->input->get("searchName");
        $iLike      = trim($this->dbView->escape_like_str($searchData));

        // $vehicleClass = $this->enc->decode($this->input->get('vehicleClass'));

        if ($this->get_identity_app() == 0) {
            if (!empty($this->session->userdata("port_id"))) {
                $port = $this->session->userdata("port_id");
            } else {
                $port = $this->enc->decode($this->input->get('port'));
            }
        } else {
            $port = $this->get_identity_app();
        }

        $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

        $where    = " WHERE b.service_id=2 and a.status=1 and a.boarding_date >= ".$this->db->escape($dateFrom) . " and a.boarding_date < " . $this->db->escape($dateToNew) ;
        $whereSearch = $where ;


        if (!empty($port)) {
            $where .= " and a.port_id=" . $this->db->escape($port) ;        
        }
        /*
            if (!empty($vehicleClass)) {
                $where .= " and b.vehicle_class_id=" . $this->db->escape($vehicleClass) ;  
            } 
        */                     

        if (!empty($searchData)) {
            if ($searchName == 'bookingCode') 
            {
                $where .= " and  b.booking_code  =" .$this->db->escape($iLike);
            } 
            else if ($searchName == 'boardingCode') 
            {
                $where .= " and  a.boarding_code = " .$this->db->escape($iLike);
            } 
            else if ($searchName == 'passName') 
            {
                $where .= " and  bp.name  ilike '%".$iLike."%'  ESCAPE '!'  ";
            } 
            else if ($searchName == 'ticketNumber') {
                $where .= " and  a.ticket_number = '" . $iLike . "'   ";
            } 
            else if ($searchName == 'shipName') 
            {
                $searchShip = $this->select_data("app.t_mtr_ship", " where name ilike '%". $iLike ."%'  ESCAPE '!'  ")->result();
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
            else if ($searchName == 'deviceName') 
            {
                $searchTerminalCode = $this->select_data("app.t_mtr_device_terminal", " where terminal_name ilike '%". $iLike ."%'  ESCAPE '!'  ")->result();
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
            else {
                $where .= " and ( b.id_number ilike '%" . $iLike . "%'   ESCAPE '!'  ) ";
            }
        }
        $sql = $this->qry($where);
        $sql .= " ORDER BY  a.id desc" ;
        $query     = $this->dbView->query($sql);
        $rows_data = $query->result();
        
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
        
        // getDrivers
        $columnBooking = array_unique(array_column($rows_data,"booking_code"));
                
        $rows = [];

        $dataPort = $this->getMaster("t_mtr_port","id","name");
        $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataDock = $this->getMaster("t_mtr_dock ","id","name");
        $dataShipClass = $this->getMaster("t_mtr_ship_class ","id","name");
        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class ","id","name");
        $dataPasserType = $this->getMaster("t_mtr_passanger_type ","id","name");

        foreach ($rows_data as $row) {
            $id         = $this->enc->encode($row->booking_code);
            $row->actions = "";

            $row->boarding_date = empty($row->boarding_date) ? "" : format_dateTime($row->boarding_date);

            $row->port_name = $dataPort[$row->port_id];
            $row->service_name = $dataService[$row->service_id];
            $row->vehicle_class_name = $dataVehicleClass[$row->vehicle_class_id];
            $row->ship_class_name = $dataShipClass[$row->ship_class];
            $row->dock_name = $dataDock[$row->dock_id];
            $row->ship_name = $dataShip[$row->ship_id];
            $row->terminal_name = empty($dataPerangkat[$row->terminal_code])?"":$dataPerangkat[$row->terminal_code];
            $row->passanger_type_name = @$dataPasserType[$row->passanger_type_id];

            $rows[] = $row;
            unset($row->id);
        }
        
        return (object)$rows;
    }

    public function qry($where) {
  
        $data = " SELECT
                            bp.name,
                            bp.passanger_type_id, 
                            b.id_number, 
                            b.booking_code,                
                            (
                                case 
                                when a.terminal_code is null then 'Manifest Susulan' else '' end
                            ) as manifest_data_from,
                            a.id,
                            a.status,
                            a.boarding_date,
                            a.boarding_code,
                            a.ticket_number,
                            b.service_id ,
                            a.port_id,
                            a.terminal_code,
                            b.vehicle_class_id,
                            a.ship_class,
                            a.dock_id,
                            h.ship_id 
                        from app.t_trx_boarding_passanger a
                        left join app.t_trx_booking_passanger bp  on a.ticket_number=bp.ticket_number
                        left join app.t_trx_booking_vehicle b  on bp.booking_code =b.booking_code
                        left join app.t_trx_open_boarding h on a.boarding_code = h.boarding_code
                    {$where}
                    ";
            // die($data); exit;
        return $data;
    }

    public function qryCount($where) {

        $data = "SELECT count(a.id) as countdata 
                        from app.t_trx_boarding_passanger a
                        left join app.t_trx_booking_passanger bp  on a.ticket_number=bp.ticket_number
                        left join app.t_trx_booking_vehicle b  on bp.booking_code =b.booking_code
                        left join app.t_trx_open_boarding h on a.boarding_code = h.boarding_code
                {$where}
				";
        return $data;
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

    public function get_identity_app() {
        $data = $this->dbView->query("select * from app.t_mtr_identity_app")->row();

        return $data->port_id;
    }
}
