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

class Check_in_vehicle_passanger_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_module = 'transaction/check_in_vehicle_vehicle';
    }

    public function dataList() {
        $start  = $this->input->post('start');
        $length = $this->input->post('length');
        $draw   = $this->input->post('draw');
        // $search = $this->input->post('search');
        $dateFrom     = date("Y-m-d", strtotime(trim($this->input->post('dateFrom'))));
		$dateTo       = date("Y-m-d", strtotime(trim($this->input->post('dateTo'))));
        $order        = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir    = strtoupper($order[0]['dir']);
        $searchData   = $this->input->post('searchData');
        $searchName   = $this->input->post('searchName');
        $iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

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

        $where = " WHERE a.id is not null and a.status<>'-6' and c.service_id='2' and a.created_on >= '" . $dateFrom . "' and a.created_on < '" . $dateToNew . "'";


        if (!empty($port)) {
            $where .="and b.origin=".$this->db->escape($port)." ";
        }

        if (!empty($searchData)) {
             if ($searchName == "ticketNumber") {
                $where .= "and a.ticket_number = '" . $iLike . "' ";
            } else if ($searchName == "bookingCode") {
                $where .= "and b.booking_code = '" . $iLike . "' ";
            } 
            else if ($searchName == "passName") {
                $where .= "and c.name ilike '%" . $iLike . "%' ESCAPE '!' ";
            }             
            else {
                $where .= "and b.id_number ilike '%" . $iLike . "%' ESCAPE '!' ";
            }
        }

        $sql      = $this->qry($where);
        $sqlCount = $this->qryCount($where);

        $countdata     = $this->dbView->query($sqlCount)->row();
        $records_total = $countdata->countdata;

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

        $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataPort = $this->getMaster("t_mtr_port","id","name"); 
        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class","id","name"); 
        $dataPassangerTypeId = $this->getMaster("t_mtr_passanger_type","id","name"); 


        $rows = [];
        $i    = ($start + 1);
        foreach ($rows_data as $row) {
            $row->number = $i;

            $row->actions = "";

            $dateCreated = date("Y-m-d", strtotime($row->created_on));
            // die($dateCreated); exit;

            $row->vehicle_class_name = @$dataVehicleClass[$row->vehicle_class_checkin];
            $row->terminal_name = @$masterTerminal[$row->terminal_code];
            $row->port_origin = @$dataPort[$row->origin];
            $row->service_name = @$dataService[$row->service_id];
            $row->passanger_type_name = @$dataPassangerTypeId[$row->passanger_type_id];
            $row->ship_name = @$masterShip[$row->ticket_number];
            $row->ticket_number = "<a href='".site_url("transaction/ticket_tracking/index/".$row->ticket_number)."' target='_blank' >".$row->ticket_number."</a>";


            $row->created_on = format_dateTimeHis($row->created_on);
            $row->no         = $i;

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

        $dateFrom   = $this->input->get('dateFrom');
        $dateTo     = $this->input->get('dateTo');
        $searchData = $this->input->get('searchData');
        $searchName = $this->input->get('searchName');
        $iLike      = trim(strtoupper($this->dbView->escape_like_str($searchData)));

        if ($this->get_identity_app() == 0) {
            if (!empty($this->session->userdata("port_id"))) {
                $port = $this->session->userdata("port_id");
            } else {
                $port = $this->enc->decode($this->input->post('port'));
            }
        } else {
            $port = $this->get_identity_app();
        }

        $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

        $where = " WHERE a.id is not null and a.status<>'-6' and c.service_id='2' and a.created_on >= '" . $dateFrom . "' and a.created_on < '" . $dateToNew . "'";


        if (!empty($port)) {
            $where .="and b.origin=".$this->db->escape($port)." ";
        }

        if (!empty($searchData)) {
             if ($searchName == "ticketNumber") {
                $where .= "and a.ticket_number = '" . $iLike . "' ";
            } else if ($searchName == "bookingCode") {
                $where .= "and b.booking_code = '" . $iLike . "' ";
            } 
            else if ($searchName == "passName") {
                $where .= "and c.name ilike '%" . $iLike . "%' ESCAPE '!' ";
            }             
            else {
                $where .= "and b.id_number ilike '%" . $iLike . "%' ESCAPE '!' ";
            }
        }

        $sql      = $this->qry($where);
        $sql .= " ORDER BY  a.id desc";

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

        $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataPort = $this->getMaster("t_mtr_port","id","name"); 
        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class","id","name"); 
        $dataPassangerTypeId = $this->getMaster("t_mtr_passanger_type","id","name"); 

        $rows = [];

        foreach ($rows_data as $row) {
            $row->actions = "";

            $dateCreated = date("Y-m-d", strtotime($row->created_on));
            // die($dateCreated); exit;

            $row->vehicle_class_name = @$dataVehicleClass[$row->vehicle_class_checkin];
            $row->terminal_name = @$masterTerminal[$row->terminal_code];
            $row->port_origin = @$dataPort[$row->origin];
            $row->service_name = @$dataService[$row->service_id];
            $row->passanger_type_name = @$dataPassangerTypeId[$row->passanger_type_id];
            $row->ship_name = @$masterShip[$row->ticket_number];
            // $row->created_on = format_dateTimeHis($row->created_on);
            $row->created_on = date('Y-m-d H:i' , strtotime($row->created_on));

            $rows[] = $row;
            unset($row->id);
        }    
        return (object)$rows;
    }

    public function qry($where) {
        $data = "SELECT
                        a.id,
                        a.created_on,
                        ttciv.vehicle_class_checkin ,
                        a.booking_code ,
                        b.origin ,
                        a.terminal_code ,
                        a.ticket_number ,
                        b.id_number,	
                        c.name,
                        c.passanger_type_id 	
                    from app.t_trx_check_in a
                        left join app.t_trx_check_in_vehicle ttciv on a.booking_code =ttciv.booking_code 
                        join app.t_trx_booking_vehicle b on a.booking_code =b.booking_code 
                        join app.t_trx_booking_passanger c on a.ticket_number =c.ticket_number
				$where
				";
        return $data;
    }    

    public function qryCount($where) {
        $data = "	SELECT
					count(a.id) as countdata
                    from app.t_trx_check_in a
                        left join app.t_trx_check_in_vehicle ttciv on a.booking_code =ttciv.booking_code 
                        join app.t_trx_booking_vehicle b on a.booking_code =b.booking_code 
                        join app.t_trx_booking_passanger c on a.ticket_number =c.ticket_number
				$where
				";

        return $data;
    }
	public function ticket_vehicle($booking_code){
		// die($booking_code);
		return $this->db->query("SELECT
								BP.name as penumpang,
								BP.id_number as identity_number,
								BP.depart_time_start ,
								BP.depart_time_end ,
                                BP.ticket_number as ticket_number_passanger ,
								BV.id_number, 
                                BV.vehicle_class_id,
								P.name as origin, 
								P.city as origin_city, 
								P2.name as destination, 
								P2.city as destination_city, 
								B.depart_date,
								B.booking_code,
								S.name as service, 
								SC.name as ship_class, 
                                B.ship_class as ship_class_id, 
								BV.ticket_number, 
								pt.name as passanger_type,
								VC.name as vehicle_name, 
								I.customer_name ,
								I.email,
								I.created_by,
                                b.ticket_type,
                                b.channel,
								pt.name as passanger_type,
								I.phone_number
							from app.t_trx_booking B
							left join app.t_trx_invoice I on I.trans_number = B.trans_number
							left join app.t_trx_booking_passanger BP on B.booking_code = BP.booking_code
							left join app.t_trx_booking_vehicle BV on B.booking_code = BV.booking_code
							left join app.t_mtr_port P on P.id = B.origin
							left join app.t_mtr_port P2 on P2.id = B.destination
							left join app.t_mtr_service S on S.id = B.service_id
							left join app.t_mtr_ship_class SC on SC.id = B.ship_class
							left join app.t_mtr_vehicle_class VC on VC.id = BV.vehicle_class_id
							left join app.t_mtr_passanger_type pt on BP.passanger_type_id = pt.id
							WHERE B.status != -5 AND BV.service_id = 2 
							AND B.booking_code = '{$booking_code}'
							order by bp.ticket_number asc
							");
	}	   
    
    public function getBoardingPass($ticketNumber)
    {
        $qry="SELECT 
            ttciv.booking_code,
            ttciv.ticket_number,
            ttciv.vehicle_class_checkin ,
            ttciv .weight ,
            ttciv.terminal_code,
            ttb.origin,
            ttb.destination,
            ttb.service_id ,
            ttb.ship_class ,	
            ttb.depart_date ,
            ttbv.boarding_expired,
            ttbv .id_number,
            ( select 
                name from app.t_trx_booking_passanger 
              where booking_code = ttb.booking_code 
              and status not in ('-5','-6')
              order by id desc limit 1 
              )as name
        from app.t_trx_check_in_vehicle ttciv 
        left join app.t_trx_booking ttb on ttciv.booking_code = ttb .booking_code 
        left join app.t_trx_booking_vehicle ttbv on ttciv.ticket_number  = ttbv .ticket_number 
        where ttciv.ticket_number ='$ticketNumber' ";

        $data = $this->db->query($qry)->row();

        // $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataPort = $this->getMaster("t_mtr_port","id","name"); 
        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class","id","name"); 
        $dataShipClass = $this->getMaster("t_mtr_ship_class","id","name"); 
        $dataTerminal = $this->select_data("app.t_mtr_device_terminal", " where terminal_code='".$data->terminal_code."' ")->row();
        
        $data->terminal_code_name = @$dataTerminal->terminal_name;
        $data->vehicle_class_checkin = @$dataVehicleClass[$data->vehicle_class_checkin];
        $data->origin = @$dataPort[$data->origin];
        $data->destination = @$dataPort[$data->destination];
        $data->ship_class = @$dataShipClass[$data->ship_class];
        // $data->service_id = @$dataService[$data->service_id];

        return $data;

    }
    public function getReciept($booking_code){
		// die($booking_code);
		$data = $this->dbView->query("SELECT
								BP.name as penumpang,
								BP.id_number as identity_number,
								BP.depart_time_start ,
								BP.depart_time_end ,
								BV.id_number, 
								P.name as origin, 
								P.city as origin_city, 
								P2.name as destination, 
								P2.city as destination_city, 
								B.depart_date,
								B.booking_code,
								S.name as service, 
								SC.name as ship_class, 
                                B.ship_class as ship_class_id, 
								BV.ticket_number, 
								pt.name as passanger_type,
								-- VC.name as vehicle_name, 
                                BV.vehicle_class_id, 
								I.customer_name as instansi,
								I.email,
								I.created_by,
								pt.name as passanger_type,
								I.phone_number,
								C.created_on ,
                                B.created_by ,
								I.amount as amount_invoice,
                                C.balance as total_cash ,
	                            C.change as change_cash ,
                                C.payment_type  ,
                                si.sof_name,
								C.amount as amount_payment
							from app.t_trx_booking B
							left join app.t_trx_invoice I on I.trans_number = B.trans_number
							left join app.t_trx_booking_passanger BP on B.booking_code = BP.booking_code
							left join app.t_trx_booking_vehicle BV on B.booking_code = BV.booking_code
							left join app.t_mtr_port P on P.id = B.origin
							left join app.t_mtr_port P2 on P2.id = B.destination
							left join app.t_mtr_service S on S.id = B.service_id
							left join app.t_mtr_ship_class SC on SC.id = B.ship_class
							-- left join app.t_mtr_vehicle_class VC on VC.id = BV.vehicle_class_id
							left join app.t_mtr_passanger_type pt on BP.passanger_type_id = pt.id
							left join app.t_trx_payment c on c.trans_number = B.trans_number 
                            left join app.t_mtr_sof_id_finnet si on c.sof_id =si.sof_id
							left join app.t_mtr_sof_id_finnet d on d. sof_id = c.sof_id 
							WHERE B.status != -5 AND BV.service_id = 2 
							AND B.booking_code = '{$booking_code}'
							order by bp.ticket_number asc
							")->result();
                            
		$paymentType = $this->select_data("app.t_mtr_payment_type", "  ")->result();
		$dataPaymentType = [];
		foreach ($paymentType as $keyPayment => $valuePayment) {
			$dataPaymentType[$valuePayment->payment_type] =$valuePayment->name; 
		}
        
        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class","id","name"); 
        // $checkUnderPaid = $this->checkUnderpaid($booking_code);
                
        $returnData = [];
        foreach ($data as $key => $value) {
            
            $value->getNamePayment ="";
            if($value->payment_type=='finpay')
            {
                $value->getNamePayment = "*".$value->sof_name;
            }else if($value->payment_type=='reedem')
            {
                // $value->getNamePayment = '*redeem';
                $value->getNamePayment = '';
            }	
            else
            {
                $value->getNamePayment = empty($dataPaymentType[$value->payment_type])?"":"*".$dataPaymentType[$value->payment_type];
            }		

            $value->vehicle_name = @$dataVehicleClass[$value->vehicle_class_id];
            // if(!empty($checkUnderPaid[$value->booking_code]))
            // {
            //     $value->vehicle_name = @$dataVehicleClass[$checkUnderPaid[$value->booking_code]->old_vehicle_class];
            // }
            
            $returnData[]=$value;
        }
        return $returnData;                            
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
        $data = $this->dbView->query(" select * from app.t_mtr_identity_app ")->row();

        return $data->port_id;
    }
}
