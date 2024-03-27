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

class Gate_in_vehicle_passanger_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_module = 'transaction/gate_in_vehicle';
    }

    public function dataList() {
        $start  = $this->input->post('start');
        $length = $this->input->post('length');
        $draw   = $this->input->post('draw');
        $dateTo   = trim($this->input->post('dateTo'));
        $dateFrom = trim($this->input->post('dateFrom'));
        $order        = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir    = strtoupper($order[0]['dir']);

        $searchData = $this->input->post('searchData');
        $searchName = $this->input->post('searchName');
        $iLike      = trim(strtoupper($this->dbView->escape_like_str($searchData)));

        if ($this->get_identity_app() == 0) {
            if (!empty($this->session->userdata("port_id"))) {
                $port_id = $this->session->userdata("port_id");
            } else {
                $port_id = $this->enc->decode($this->input->post('port'));
            }
        } else {
            $port_id = $this->get_identity_app();
        }

        $field = [
            0  => 'id',
        ];

        $order_column = $field[$order_column];

        $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));
        $where = " WHERE  b.service_id=2 and a.status<>'-6' and a.created_on >= '" . $dateFrom . "' and a.created_on < '" . $dateToNew . "'";
        $whereSearch = $where;

        if (!empty($port_id)) {
            $where .= "and (b.origin=" . $port_id . ")";
        }

        if (!empty($searchData)) {
            if ($searchName == "bookingCode") {
                $where .= " and a.booking_code = '" . $iLike . "'  ";
            } else if ($searchName == "ticketNumber") {
                $where .= " and  a.ticket_number = '" . $iLike . "' ";
            }
            else if ($searchName == "passName") {
                $where .= " and  b.name ilike  '%" . $iLike . "%' ESCAPE '!' ";
            } else {
                $where .= " and  g.id_number ilike '%" . $iLike . "%'  ESCAPE '!' ";
            }
        }

        $sql      = $this->qry($where);
        $sqlCount = $this->qryCount($where);

        $countData     = $this->dbView->query($sqlCount)->result();
        $records_total = count($countData);

        $sql .= " ORDER BY " . $order_column . " {$order_dir}";

        if ($length != -1) {
            $sql .= " LIMIT {$length} OFFSET {$start}";
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

       // get ship name
       $columnTicketNumber = array_unique(array_column($rows_data,"ticket_number"));
       $masterShip[""]="";
       if(!empty($columnTicketNumber))
       {
           $columnTicketNumberString = array_map(function($a){ return "'".$a."'"; } , $columnTicketNumber );
           $qryGetShip ="SELECT 
                                       tms.name,
                                       ttbp.ticket_number
                                   from app.t_trx_boarding_passanger ttbp 
                                   join app.t_trx_open_boarding ttob on ttbp.boarding_code  = ttob.boarding_code 
                                   join app.t_mtr_ship tms on ttob.ship_id = tms.id 
                                   where ttbp.ticket_number in  (".implode(",",$columnTicketNumberString).") ";
           $getShip = $this->dbView->query($qryGetShip)->result();
           if($getShip)
           {
               $masterShip = array_combine(array_column($getShip,"ticket_number"),array_column($getShip,"name"));
           }
       }


        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class ","id","name");       
        $dataPort = $this->getMaster("t_mtr_port","id","name"); 
        $dataService = $this->getMaster("t_mtr_service ","id","name");

        $rows = [];
        $i    = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number = $i;

            $booking_code = $this->enc->encode($row->booking_code);
            $detail_url   = site_url($this->_module . "/detail/{$booking_code}");

            $row->actions = "";

            $row->actions .= generate_button_new($this->_module, 'detail', $detail_url);

            $row->vehicle_class_name = @$dataVehicleClass[$row->vehicle_class_id];
            $row->port_name = @$dataPort[$row->origin];
            $row->service_name = @$dataService[$row->service_id];
            $row->terminal_name = @$masterTerminal[$row->terminal_code];
            $row->ship_name = @$masterShip[$row->ticket_number];


            $row->created_on       = empty($row->created_on) ? "" : format_dateTimeHis($row->created_on);
            $row->boarding_expired = empty($row->boarding_expired) ? "" : format_dateTimeHis($row->boarding_expired);
            $row->ticket_number = "<a href='".site_url("transaction/ticket_tracking/index/".$row->ticket_number)."' target='_blank' >".$row->ticket_number."</a>";

            $row->no               = $i;

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


    public function select_data($table, $where = "") {
        return $this->dbView->query("select * from $table $where");
    }
    public function select_data_field($table, $field , $where = "") {
        return $this->dbView->query("select $field from $table $where");
    }    
    public function download() {

        $dateFrom = $this->input->get("dateFrom");
        $dateTo   = $this->input->get("dateTo");
        $searchData = $this->input->get("searchData");
        $searchName = $this->input->get("searchName");
        $iLike      = trim($this->dbView->escape_like_str($searchData));

        if ($this->get_identity_app() == 0) {
            if (!empty($this->session->userdata("port_id"))) {
                $port_id = $this->session->userdata("port_id");
            } else {
                $port_id = $this->enc->decode($this->input->get('port'));
            }
        } else {
            $port_id = $this->get_identity_app();
        }

        $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));
        $where = " WHERE  b.service_id=2 and a.status<>'-6' and a.created_on >= '" . $dateFrom . "' and a.created_on < '" . $dateToNew . "'";
        $whereSearch = $where;

        if (!empty($port_id)) {
            $where .= "and (b.origin=" . $port_id . ")";
        }

        if (!empty($searchData)) {
            if ($searchName == "bookingCode") {
                $where .= " and a.booking_code = '" . $iLike . "'  ";
            } else if ($searchName == "ticketNumber") {
                $where .= " and  a.ticket_number = '" . $iLike . "' ";
            }
            else if ($searchName == "passName") {
                $where .= " and  b.name ilike  '%" . $iLike . "%' ESCAPE '!' ";
            } else {
                $where .= " and  g.id_number ilike '%" . $iLike . "%'  ESCAPE '!' ";
            }
        }

        $sql      = $this->qry($where);
        $sql .= " ORDER BY  a.id desc ";

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

       // get ship name
       $columnTicketNumber = array_unique(array_column($rows_data,"ticket_number"));
       $masterShip[""]="";
       if(!empty($columnTicketNumber))
       {
           $columnTicketNumberString = array_map(function($a){ return "'".$a."'"; } , $columnTicketNumber );
           $qryGetShip ="SELECT 
                                       tms.name,
                                       ttbp.ticket_number
                                   from app.t_trx_boarding_passanger ttbp 
                                   join app.t_trx_open_boarding ttob on ttbp.boarding_code  = ttob.boarding_code 
                                   join app.t_mtr_ship tms on ttob.ship_id = tms.id 
                                   where ttbp.ticket_number in  (".implode(",",$columnTicketNumberString).") ";
           $getShip = $this->dbView->query($qryGetShip)->result();
           if($getShip)
           {
               $masterShip = array_combine(array_column($getShip,"ticket_number"),array_column($getShip,"name"));
           }
       }


        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class ","id","name");       
        $dataPort = $this->getMaster("t_mtr_port","id","name"); 
        $dataService = $this->getMaster("t_mtr_service ","id","name");

        $rows = [];

        foreach ($rows_data as $row) {
            $booking_code = $this->enc->encode($row->booking_code);

            $row->vehicle_class_name = @$dataVehicleClass[$row->vehicle_class_id];
            $row->port_name = @$dataPort[$row->origin];
            $row->service_name = @$dataService[$row->service_id];
            $row->terminal_name = @$masterTerminal[$row->terminal_code];
            $row->ship_name = @$masterShip[$row->ticket_number];

            $rows[] = $row;
            unset($row->id);
        }          

        // print_r($rows);
        return (object)$rows;
    }

    public function qry($where) {
        $data = "SELECT
                                a.id,
                                a.terminal_code ,
                                a.created_on ,
                                a.boarding_expired,
                                a.terminal_code,
                                a.ticket_number ,
                                b.service_id,
                                b.booking_code,
                                b.origin ,
                                b.name,
                                g.vehicle_class_id ,
                                g.id_number as plate_number
                            from app.t_trx_gate_in a
                            left join app.t_trx_booking_passanger  b on a.ticket_number =b.ticket_number
                            join app.t_trx_booking_vehicle g on a.booking_code=g.booking_code
                        $where
                        ";
            // die($data); exit;
        return $data;
    }

    public function qryCount($where) {

        $data = "SELECT
		                    a.id AS countdata 
                            from app.t_trx_gate_in a
                            left join app.t_trx_booking_passanger  b on a.ticket_number =b.ticket_number
                            join app.t_trx_booking_vehicle g on a.booking_code=g.booking_code
                        $where
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
