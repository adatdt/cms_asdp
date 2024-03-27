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

class M_gate_in_vehicle extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_module = 'transaction/gate_in_vehicle';
    }

    public function dataList() {
        $start  = $this->input->post('start');
        $length = $this->input->post('length');
        $draw   = $this->input->post('draw');
        $dateTo   = $this->inputDate(trim($this->input->post('dateTo')));
        $dateFrom = $this->inputDate(trim($this->input->post('dateFrom')));
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
            // 1  => 'created_on',
            // 2  => 'booking_code',
            // 3  => 'ticket_number',
            // 4  => 'first_passanger',
            // 5  => 'plate_number',
            // 6  => 'vehicle_class_name',
            // 7  => 'service_name',
            // 8  => 'port_name',
            // 9  => 'boarding_expired',
            // 10 => 'terminal_name',
            // 11 => 'total_passanger',
            // 12 => 'height',
            // 13 => 'length',
            // 14 => 'width',
            // 15 => 'weight',
            // 16 => 'height_cam',
            // 17 => 'length_cam',
            // 18 => 'width_cam',
            // 19 => 'weighbridge',
        ];

        $order_column = $field[$order_column];

        $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));
        $where = " WHERE  a.status<>'-6' and a.created_on >= '" . $dateFrom . "' and a.created_on < '" . $dateToNew . "'";
        $whereSearch = $where;

        if (!empty($port_id)) {
            $where .= "and (b.origin=" . $port_id . ")";
        }

        if (!empty($searchData)) {
            if ($searchName == "bookingCode") {
                $where .= " and a.booking_code = '" . $iLike . "'  ";
            } else if ($searchName == "ticketNumber") {
                $where .= " and  a.ticket_number = '" . $iLike . "' ";
            } else if ($searchName == "deviceName") {

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
            } else if ($searchName == "passName") {

                $qrySearchDrivers = "SELECT 
                                                    a.booking_code  
                                                from app.t_trx_gate_in_vehicle a
                                                join app.t_trx_booking_passanger b on a.booking_code = b.booking_code  $whereSearch and b.name ilike '%".$iLike."%' "; 
                $searchDrivers = $this->dbView->query($qrySearchDrivers)->result();
                if(!empty($searchDrivers))
                {
                    $searchDriversString = array_map(function($a){ return "'".$a."'"; }, array_column($searchDrivers,"booking_code"));
                    $where  .= " and  a.booking_code in (".implode(", ",$searchDriversString ).") ";
                }
                else
                {
                    $where .= " and a.id is null ";
                }
            } else {
                $where .= " and  g.id_number ilike '%" . $iLike . "%'  ";
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

        // getDrivers
        $columnBooking = array_unique(array_column($rows_data,"booking_code"));
        
        $masterDriver1 = array();
        $masterDriver2 = array();
        if(!empty($columnBooking))
        {
            $columnBookingString = array_map(function($a){ return "'".$a."'"; } , $columnBooking );

            // check from trx_booking_passanger
            $getDriverName = $this ->select_data_field(" app.t_trx_booking_passanger "," booking_code, name ", " where booking_code in (".implode(",",$columnBookingString)." ) order by ticket_number desc ")->result();

            // when key have some duplicate then automatical fill last value
            $masterDriver1 = array_combine(array_column($getDriverName,"booking_code"),array_column($getDriverName,"name") );

            // check from t_trx_print_manifest_vehicle
            $getDriverName2 = $this ->select_data_field(" app.t_trx_print_manifest_vehicle "," booking_code, name ", " where booking_code in (".implode(",",$columnBookingString).")  and driver = true and name !=''  ")->result();                  
            if(!empty($getDriverName2))
            {
                // when key have some duplicate then automatical fill last value
                $masterDriver2 = array_combine(array_column($getDriverName2,"booking_code"),array_column($getDriverName2,"name") );
            }
        }
        
        $masterDriver  = array_merge($masterDriver1,$masterDriver2);

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
            $row->first_passanger = @$masterDriver[$row->booking_code];


            $row->created_on       = empty($row->created_on) ? "" : format_dateTimeHis($row->created_on);
            $row->boarding_expired = empty($row->boarding_expired) ? "" : format_dateTimeHis($row->boarding_expired);
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

    public function listDetail_04022024($booking_code = "") {

        return $this->dbView->query("SELECT
                                    e.name as service_name,
                                    c.ticket_number as ticket_passanger,
                                    c.name as passanger_name, 
                                    c.age, 
                                    c.gender, 
                                    b.trans_number, 
                                    d.customer_name,
                                    b.total_passanger,
                                    b.booking_code,
                                    mv.terminal_code as terminal_code_passenger,
                                    mv.created_on as gatein_date,
                                    a.terminal_code,
                                    a.created_on,
                                    a.id 
                                from app.t_trx_gate_in_vehicle a
                                left join app.t_trx_booking b on a.booking_code=b.booking_code
                                left join app.t_trx_booking_passanger c on a.booking_code=c.booking_code
                                left join app.t_trx_invoice d on b.trans_number=d.trans_number
                                join app.t_trx_gate_in_manifest_vehicle mv on c.ticket_number = mv.ticket_number  
                                left join app.t_mtr_service e on b.service_id=e.id
                                where b.booking_code='" . $booking_code . "' and c.status !='-5'
							 ");
    }
    public function listDetail($booking_code = "") {

        return $this->dbView->query("SELECT
                                    e.name as service_name,
                                    c.ticket_number as ticket_passanger,
                                    c.name as passanger_name, 
                                    c.age, 
                                    c.gender, 
                                    b.trans_number, 
                                    d.customer_name,
                                    b.total_passanger,
                                    b.booking_code,
                                    a.terminal_code as terminal_code_passenger,
                                    a.created_on as gatein_date,
                                    a.terminal_code,
                                    a.created_on,
                                    a.id 
                                from app.t_trx_gate_in a
                                left join app.t_trx_booking b on a.booking_code=b.booking_code
                                 join app.t_trx_booking_passanger c on a.ticket_number=c.ticket_number
                                left join app.t_trx_invoice d on b.trans_number=d.trans_number
                                left join app.t_mtr_service e on b.service_id=e.id
                                where b.booking_code='" . $booking_code . "' and c.status !='-5'
							 ");
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

        $dateFrom = $this->inputDate($this->input->get("dateFrom"));
        $dateTo   = $this->inputDate($this->input->get("dateTo"));
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
        $where = " WHERE  a.status<>'-6' and a.created_on >= '" . $dateFrom . "' and a.created_on < '" . $dateToNew . "'";
        $whereSearch = $where;

        $wherename = "";
        if (!empty($port_id)) {
            $where .= "and (b.origin=" . $port_id . ")";
        }

        if (!empty($searchData)) {
            if ($searchName == "bookingCode") {
                $where .= " and a.booking_code = '" . $iLike . "'  ";
            } else if ($searchName == "ticketNumber") {
                $where .= " and  a.ticket_number = '" . $iLike . "' ";
            } else if ($searchName == "deviceName") {

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
            } else if ($searchName == "passName") {

                $qrySearchDrivers = "SELECT 
                                                    a.booking_code  
                                                from app.t_trx_gate_in_vehicle a
                                                join app.t_trx_booking_passanger b on a.booking_code = b.booking_code  $whereSearch and b.name ilike '%".$iLike."%' "; 
                $searchDrivers = $this->dbView->query($qrySearchDrivers)->result();
                if(!empty($searchDrivers))
                {
                    $searchDriversString = array_map(function($a){ return "'".$a."'"; }, array_column($searchDrivers,"booking_code"));
                    $where  .= " and  a.booking_code in (".implode(", ",$searchDriversString ).") ";
                }
                else
                {
                    $where .= " and a.id is null ";
                }
            } else {
                $where .= " and  g.id_number ilike '%" . $iLike . "%'  ";
            }
        }

        $sql = $this->qry($where) . " order by a.id desc ";
        $query = $this->dbView->query($sql);
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

        // getDrivers
        $columnBooking = array_unique(array_column($rows_data,"booking_code"));
        $masterDriver[""]="";
        if(!empty($columnBooking))
        {
            $columnBookingString = array_map(function($a){ return "'".$a."'"; } , $columnBooking );

            // check from trx_booking_passanger
            $getDriverName = $this ->select_data_field(" app.t_trx_booking_passanger "," booking_code, name ", " where booking_code in (".implode(",",$columnBookingString)." ) order by ticket_number desc ")->result();

            // when key have some duplicate then automatical fill last value
            $masterDriver += array_combine(array_column($getDriverName,"booking_code"),array_column($getDriverName,"name") );

            // check from t_trx_print_manifest_vehicle
            $getDriverName2 = $this ->select_data_field(" app.t_trx_print_manifest_vehicle "," booking_code, name ", " where booking_code in (".implode(",",$columnBookingString).")  and driver = true and name !=''  ")->result();                  
            if(!empty($getDriverName2))
            {
                // when key have some duplicate then automatical fill last value
                $masterDriver += array_combine(array_column($getDriverName2,"booking_code"),array_column($getDriverName2,"name") );
            }
        }


        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class ","id","name");       
        $dataPort = $this->getMaster("t_mtr_port","id","name"); 
        $dataService = $this->getMaster("t_mtr_service ","id","name");

        $rows = [];
        foreach ($rows_data as $row) {


            $row->vehicle_class_name = @$dataVehicleClass[$row->vehicle_class_id];
            $row->port_name = @$dataPort[$row->origin];
            $row->service_name = @$dataService[$row->service_id];
            $row->terminal_name = @$masterTerminal[$row->terminal_code];
            $row->first_passanger = @$masterDriver[$row->booking_code];

            $rows[] = $row;
            unset($row->id);

        }                
        return (object)$rows;
    }

    public function qry($where) {
        $data = "SELECT
                            a.id,
                            a.created_on ,
                            a.boarding_expired,
                            a.terminal_code,
                            a.ticket_number ,
                            b.total_passanger, 
                            b.service_id,
                            b.booking_code,
                            b.origin ,
                            g.vehicle_class_id ,
                            g.id_number as plate_number, 
                            a1.height, 
                            a1.length, 
                            a1.weight, 
                            a1.width, a1.height_cam, 
                            a1.length_cam, a1.weighbridge, 
                            a1.width_cam
                        from app.t_trx_gate_in_vehicle a
                        left join app.t_trx_check_in_vehicle a1 on a.booking_code=a1.booking_code
                        left join app.t_trx_booking b on a.booking_code=b.booking_code
                        left join app.t_trx_booking_vehicle g on a.booking_code=g.booking_code
                        $where
                        ";
        return $data;
    }

    public function qryCount($where) {

        $data = "SELECT
		                    a.id AS countdata 
                        from app.t_trx_gate_in_vehicle a
                        left join app.t_trx_check_in_vehicle a1 on a.booking_code=a1.booking_code
                        left join app.t_trx_booking b on a.booking_code=b.booking_code
                        left join app.t_trx_booking_vehicle g on a.booking_code=g.booking_code
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
