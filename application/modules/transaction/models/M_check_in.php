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

class M_check_in extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/check_in';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		// $search = $this->input->post('search');
		
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

		// mengambil port di port id user

		if($this->get_identity_app()==0)
		{
			if(!empty($this->session->userdata("port_id")))
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
			1 =>'created_on',
			2 =>'booking_code',
			3 =>'ticket_number',
			4 =>'id_number',
			5 =>'passanger_name',
			6 =>'trans_number',
			7 =>'channel',
			8 =>'service_name',
			9 =>'port_origin',
			10 =>'terminal_name',
			11 =>'channel',
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.id is not null and a.status<>'-6' and b.service_id='1' and a.created_on >= ". $this->db->escape($dateFrom) . " and a.created_on < " . $this->db->escape($dateToNew) . " "; 

		if (!empty($port))
		{
			$where .="and b.origin=".$this->db->escape($port)." ";
		}

		if(!empty($searchData))
		{
			if($searchName=="transNumber")
			{
				$where .= "and b.trans_number = '" . $iLike . "'  ";

			}
			else if($searchName=="bookingCode")
			{
				$where .= "and b.booking_code = '" . $iLike . "' ";

			}			
			else if($searchName=="passName")
			{
				$where .= "and  c.name ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
			else if($searchName=="ticketNumber")
			{
				$where .= "and  a.ticket_number  ='" . $iLike . "' ";
			}
			else if($searchName=="deviceName")
			{
				$searchDevice = $getTerminal = $this ->select_data_field(" app.t_mtr_device_terminal "," terminal_code ", " where terminal_name ilike '%".$iLike."%'  ESCAPE '!' ")->result();

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
				$where .= "and (c.id_number ilike '%" . $iLike . "%' ESCAPE '!' )";
			}
		}		

		$sql 		   = $this->qry($where);
		$sqlCount 		   = $this->qryCount($where);

		// $query         = $this->dbView->query($sql);
		// $records_total = $query->num_rows();

		$countData         = $this->dbView->query($sqlCount)->row();
		$records_total = $countData->countdata;



		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

        //getterminalCode
        $columnTerminal = array_unique(array_column($rows_data,"terminal_code"));
        $masterTerminal[""]="";
        if(!empty($columnTerminal))
        {
            $columnTerminalString = array_map(function($a){ return "'".$a."'"; } , $columnTerminal );
            $getTerminal = $this ->select_data_field(" app.t_mtr_device_terminal "," terminal_code, terminal_name ", " where terminal_code in (".implode(",",$columnTerminalString).") ")->result();
            $masterTerminal = array_combine(array_column($getTerminal,"terminal_code"),array_column($getTerminal,"terminal_name"));
        }      		

        $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataPort = $this->getMaster("t_mtr_port","id","name"); 

		foreach ($rows_data as $row) {
			$row->number = $i;
			$booking_code = $this->enc->encode($row->booking_code);
            $ticket_number_code = $this->enc->encode($row->ticket_number);
			$service_id=$this->enc->encode($row->service_id);

			$row->terminal_name = @$masterTerminal[$row->terminal_code];
			$row->port_origin = @$dataPort[$row->origin];
			$row->service_name = @$dataService[$row->service_id];

			$download_ticket_url   = site_url("transaction/payment" . "/download_ticket_pdf/{$booking_code}/{$service_id}");
            $buton_tiket ="<a  target='_blank'  class='btn btn-sm btn-default' id='download_ticket_pdf' href='$download_ticket_url'  title='Tiket Online'  ><i class='fa fa-file-pdf-o' style='color: #ea5460'></i> </a>";

            $download_receipt_online_url   = site_url("transaction/payment" . "/download_tiket_receipt/{$booking_code}/{$service_id}");
            $buton_receipt_online_url ="<a  target='_blank'  class='btn btn-sm btn-default' href='$download_receipt_online_url'   title='receipt online' ><i class='fa fa-file-pdf-o' style='color: #ea5460'></i> </a>";	
			
			// $download_receipt_goshow_url   = site_url($this->_module . "/download_receipt_goshow_pdf/{$booking_code}");
			$download_receipt_goshow_url   = site_url("transaction/payment" . "/download_receipt_goshow_pdf/{$booking_code}/{$service_id}");
            
			$buton_receipt_goshow_url ="<a  target='_blank'  class='btn btn-sm btn-default' href='$download_receipt_goshow_url'  title='receipt goshow' ><i class='fa fa-file-pdf-o' style='color: #ea5460'></i></a>";

            $download_boarding_url   = site_url($this->_module . "/download_boarding_pdf/{$ticket_number_code}");
            $buton_boarding ="<a  target='_blank'  class='btn btn-sm btn-default' href='$download_boarding_url'  title='boarding pass' ><i class='fa fa-file-pdf-o' style='color: #ea5460'></i></a>"; 			

			$row->actions = "";

     		if($row->channel=='mobile' || $row->channel=='web' )
     		{	
     			$row->method_checkin="ONLINE";	
     		}
     		else
     		{
     			$row->method_checkin="OFFLINE";
     		}

			/*
			 $row->actions .= generate_button($this->_module, 'view', $buton_tiket);
			 $row->actions .= generate_button($this->_module, 'view', $buton_receipt_online_url);
			 $row->actions .= generate_button($this->_module, 'view', $buton_receipt_goshow_url);
			 $row->actions .= generate_button($this->_module, 'view', $buton_boarding);
			 */
			// $row->actions .= generate_button($this->_module, 'ticket_pdf', $buton_tiket);
            $dateCreated = date("Y-m-d", strtotime($row->created_on));
            if( $dateCreated >= '2022-01-01') // yang di tampilkan recieve dimulai dari data tanggal 1 januari 2022 sesuai dengan permintaan mockup
            {
				if( strtoupper($row->booking_channel) == 'IFCS' || strtoupper($row->booking_channel) == 'MOBILE' || strtoupper($row->booking_channel) == 'WEB_ADMIN' || strtoupper($row->booking_channel) == 'WEB' )
				{
					$row->actions .= generate_button($this->_module, 'ticket_pdf', $buton_tiket);
					$row->actions .= generate_button($this->_module, 'reciept_pdf', $buton_receipt_online_url);
				}
				else
				{
					$row->actions .= generate_button($this->_module, 'reciept_pdf', $buton_receipt_goshow_url);
				}                       
				$row->actions .= generate_button($this->_module, 'boarding_pdf', $buton_boarding);			
			}

			 
     		$row->created_on=format_dateTimeHis($row->created_on);
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

	public function download(){
		
		$dateFrom=$this->input->get("dateFrom", true);
		$dateTo=$this->input->get("dateTo", true);
		$search=$this->input->get("search", true);
		$searchData = $this->input->get('searchData', true);
		$searchName = $this->input->get('searchName', true);
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

		if($this->get_identity_app()==0)
		{
			if(!empty($this->session->userdata("port_id")))
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

		$where = " WHERE a.id is not null and a.status<>'-6' and b.service_id='1' and a.created_on >= ".$this->db->escape( $dateFrom) . " and a.created_on < " . $this->db->escape($dateToNew) . " "; 

		if (!empty($port))
		{
			$where .="and  b.origin=".$this->db->eascape($port)." ";
		}

		if(!empty($searchData))
		{
			if($searchName=="transNumber")
			{
				$where .= "and b.trans_number = '" . $iLike . "'  ";

			}
			else if($searchName=="bookingCode")
			{
				$where .= "and b.booking_code = '" . $iLike . "' ";

			}			
			else if($searchName=="passName")
			{
				$where .= "and  c.name ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
			else if($searchName=="ticketNumber")
			{
				$where .= "and  a.ticket_number  ='" . $iLike . "' ";
			}
			else if($searchName=="deviceName")
			{
				$searchDevice = $getTerminal = $this ->select_data_field(" app.t_mtr_device_terminal "," terminal_code ", " where terminal_name ilike '%".$iLike."%'  ESCAPE '!' ")->result();

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
				$where .= "and (c.id_number ilike '%" . $iLike . "%' ESCAPE '!' )";
			}
		}				
		
		$sql 		   = $this->qry($where)." order by a.id desc";
		
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

		$dataService = $this->getMaster("t_mtr_service ","id","name");
		$dataPort = $this->getMaster("t_mtr_port","id","name"); 
		$dataVehicleClass = $this->getMaster("t_mtr_vehicle_class","id","name"); 	
		
		$rows = [];

		foreach ($rows_data as $row) {
            $booking_code = $this->enc->encode($row->booking_code);
            $detail_url   = site_url($this->_module . "/detail/{$booking_code}");

            $row->actions = "";

            if ($row->status == 1) {
                $row->status = success_label("Aktif");
            } else {
                $row->status = failed_label("Tidak Aktif");
            }

            if ($row->channel == 'web' || $row->channel == 'mobile') {
                $row->method_checkin = "ONLINE";
            } else {
                $row->method_checkin = "OFFLINE";
            }

            $row->actions .= generate_button_new($this->_module, 'detail', $detail_url);

            $row->vehicle_class_name = @$dataVehicleClass[$row->vehicle_class_checkin];
            $row->terminal_name = @$masterTerminal[$row->terminal_code];
            $row->port_origin = @$dataPort[$row->origin];
            $row->service_name = @$dataService[$row->service_id];

            $row->created_on = format_dateTimeHis($row->created_on);

            $rows[] = $row;
            unset($row->id);

        }		

		return (object)$rows;

	}

	public function get_identity_app()
	{
		$data=$this->dbView->query(" select * from app.t_mtr_identity_app ")->row();

		return $data->port_id;
	}

	public function qry($where)
	{
		$data=" 			
				SELECT
					a.terminal_code, 
					b.origin,
					b.service_id ,
					c.name as passanger_name, 
					c.id_number, 
					b.trans_number, 
					d.customer_name,
					a.id,
					a.ticket_number,
					a.booking_code,
					a.channel,
					b.channel as booking_channel,
					a.status,
					a.created_on
				from app.t_trx_check_in a
				join app.t_trx_booking b on a.booking_code=b.booking_code
				join app.t_trx_booking_passanger c on a.ticket_number=c.ticket_number
				join app.t_trx_invoice d on b.trans_number=d.trans_number
				{$where}
			"; 
		// die($data); exit;
		return $data;
	}

	public function qryCount($where)
	{
		$data=" 			
				SELECT
					count(a.id) as countdata
				from app.t_trx_check_in a
				join app.t_trx_booking b on a.booking_code=b.booking_code
				join app.t_trx_booking_passanger c on a.ticket_number=c.ticket_number
				join app.t_trx_invoice d on b.trans_number=d.trans_number
				{$where}
			"; 

		return $data;
	}	
	public function getMaster($table,$id,$name)
	{		
		$service =  $this->select_data("app.$table"," where status != '-5' ")->result() ;
        $checkSession = $this->session->userdata("app.".$table.$name); 

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
	public function ticket_passanger($booking_code){
		// die($booking_code);
		$qry = "SELECT
											BP.name as penumpang,
											BP.id_number as identity_number,
											BP.depart_time_start ,
											BP.depart_time_end ,
											BP.id_number, 
											B.depart_date,
											B.booking_code,
											BP.ticket_number, 
											BP.passanger_type_id, 
											I.customer_name ,
											I.email,
											I.created_by,
											b.ticket_type,
											b.channel,
											b.origin,
											b.ship_class,
											b.destination,
											I.phone_number
										from app.t_trx_booking B
										left join app.t_trx_invoice I on I.trans_number = B.trans_number
										left join app.t_trx_booking_passanger BP on B.booking_code = BP.booking_code
										WHERE B.status != -5 AND BP.service_id = 1 
										AND B.booking_code = '$booking_code'
										order by bp.ticket_number asc
							";
		$data = $this->db->query($qry)->result();
		
		$dataService = $this->getMaster("t_mtr_service ","id","name");
		$dataShipClass = $this->getMaster("t_mtr_ship_class ","id","name");
        $dataPort = $this->getMaster("t_mtr_port","id","name"); 
		$dataCityPort = $this->getMaster("t_mtr_port","id","city"); 
		$dataPassangerType = $this->getMaster("t_mtr_passanger_type","id","name"); 

		$returnData = [];
		foreach ($data as $key => $value) {

			$origin = @$dataPort[$value->origin];
			$origin_city = @$dataCityPort[$value->origin];
			$destination = @$dataPort[$value->destination];
			$destination_city = @$dataCityPort[$value->destination];

			$value->ship_class_id = $value->ship_class;

			$value->passanger_type = @$dataPassangerType[$value->passanger_type_id];
			$value->ship_class = $dataShipClass[$value->ship_class];
			$value->origin = $origin;
			$value->origin_city = $origin_city;
			$value->destination = $destination;
			$value->destination_city = $destination_city;

			$returnData[]=$value;
		}

		return  $returnData;
	}	 
	public function getBoardingPass($ticketNumber)
    {
        $qry="SELECT 
					ttciv.booking_code,
					ttciv.ticket_number,
					ttciv.terminal_code,
					ttb.origin,
					ttb.destination,
					ttb.service_id ,
					ttb.ship_class ,	
					ttb.depart_date ,
					ttbv.gatein_expired ,
					ttbv.passanger_type_id,
					ttbv.fare,
					ttbv.name,
					ttbv .id_number
				from app.t_trx_check_in ttciv 
				left join app.t_trx_booking ttb on ttciv.booking_code = ttb .booking_code 
				left join app.t_trx_booking_passanger ttbv on ttciv.ticket_number  = ttbv .ticket_number 
				where ttciv.ticket_number='$ticketNumber' ";

        $data = $this->db->query($qry)->row();

        // $dataService = $this->getMaster("t_mtr_service ","id","name");
        $dataPort = $this->getMaster("t_mtr_port","id","name"); 
        $dataVehicleClass = $this->getMaster("t_mtr_vehicle_class","id","name"); 
        $dataPassangerType = $this->getMaster("t_mtr_passanger_type","id","name"); 
		$dataShipClass = $this->getMaster("t_mtr_ship_class","id","name"); 
        $dataTerminal = $this->select_data("app.t_mtr_device_terminal", " where terminal_code='".$data->terminal_code."' ")->row();
        
        $data->terminal_code_name = @$dataTerminal->terminal_name;
        $data->passanger_type_id = @$dataPassangerType[$data->passanger_type_id];
        $data->origin = @$dataPort[$data->origin];
        $data->destination = @$dataPort[$data->destination];
        $data->ship_class = @$dataShipClass[$data->ship_class];
        // $data->service_id = @$dataService[$data->service_id];

        return $data;

    }	
	function get_vaccine_status_manifest($ticketNumber)
    {
		$whereTicket = array_map(function($x){ return "'".$x."'";}, $ticketNumber);
        $sql = "SELECT 
            v.id,
            v.ticket_number,
            v.under_age,
            v.vaccine,
            v.vaccine_status_pl,
            v.reason as reason_id,
            det.question_text as reason,
            v.test,
            v.reason_test as reason_test_id,
            det2.question_text as reason_test,
            mv.under_age_reason,
            v.config_vaccine,
            v.config_test
            FROM 
            app.t_trx_vaccine v 
            LEFT JOIN app.t_mtr_vaccine_param mv ON mv.id = v.vaccine_param_id
            LEFT JOIN app.t_mtr_assessment_param_detail det ON det.id = v.reason
            LEFT JOIN app.t_mtr_assessment_param_detail det2 ON det2.id = v.reason_test
            WHERE v.ticket_number in (".implode(", ",$whereTicket).")               
            ORDER BY v.id";
        $data = $this->db->query($sql)->result();

		$returnData = [];
		foreach ($data as $key => $value) {

            $value->vaccineReason = "-";
            $value->testReason = "-";
            if(empty($value->vaccine_status_pl))
            {
                $value->vaccineStatus ="Belum Vaksin";                
                if(!empty($value->reason_id))
                {
                    $value->vaccineReason = $value->reason;
                    $value->vaccineStatus ="<span style='color:red'>Belum Vaksin</span>";
                }

                if(!empty(!empty($value->reason_test_id)))
                {
                    $value->testReason = $value->reason_test;
                }
            }
            else{
                $value->vaccineStatus = "Dosis ke-".$value->vaccine_status_pl;
                if(!empty($value->reason_id))
                {
                    $value->vaccineReason = $value->reason;
                    $value->vaccineStatus ="<span style='color:red'>Dosis ke-".$value->vaccine_status_pl."</span>";
                }

                if(!empty($value->reason_test_id))
                {
                    $value->testReason = $value->reason_test;
                }                
            }

            if($value->under_age=='t')
            {
                $value->vaccineStatus ="<span style='color:red'>-</span>";  
                $value->vaccineReason = $value->under_age_reason;
                $value->testReason = $value->under_age_reason;
            }     
            
            $value->isValidCovidTest = $value->test == 't'?"valid":"";


			$returnData[$value->ticket_number] = $value;
		}

		return $returnData;
    }
	function get_test_status($ticketNumber)	
	{
		$whereTicket = array_map(function($x){ return "'".$x."'";}, $ticketNumber);
		$sql ="
		WITH summary AS (
				SELECT 
					v.test,
					t.* , 
					ROW_NUMBER() OVER(
										PARTITION BY v.ticket_number 
									ORDER BY t.date desc,t.id ASC
									) AS rk
					FROM app.t_trx_vaccine v 
									JOIN app.t_trx_test_covid t ON t.ticket_number = v.ticket_number 
				WHERE   v.ticket_number in (".implode(", ",$whereTicket).")  AND t.status = 1 )  
		SELECT s.*
		FROM summary s
		WHERE s.rk = 1 order by id";
		$data = $this->db->query($sql)->result();
		
		$returnData = [];
		foreach ($data as $key => $value) {
			$returnData[$value->ticket_number] = $value;
		}

		return $returnData;
	}
	public function getReciept($booking_code){
		
		$qry ="SELECT
						BP.name as penumpang,
						BP.id_number as identity_number,
						BP.depart_time_start ,
						BP.depart_time_end ,
						BP.id_number, 
						BP.fare ,
						b.origin, 
						b.destination, 
						B.depart_date,
						B.booking_code,
						B.service_id,
						B.ship_class ,
						B.ship_class as ship_class_id ,
						BP.ticket_number, 
						BP.passanger_type_id,
						I.customer_name ,
						I.email,
						I.created_by,
						I.phone_number,
						C.created_on ,
						B.created_by ,
						B.channel as booking_channel  ,
						I.amount as amount_invoice,
						C.balance as total_cash ,
	                    C.change as change_cash ,
						C.payment_type  ,
						si.sof_name,
						C.amount as amount_payment
					from app.t_trx_booking B
					left join app.t_trx_invoice I on I.trans_number = B.trans_number
					left join app.t_trx_booking_passanger BP on B.booking_code = BP.booking_code
					left join app.t_trx_payment c on c.trans_number = B.trans_number 
					left join app.t_mtr_sof_id_finnet si on c.sof_id =si.sof_id
					WHERE B.status != -5 AND BP.service_id = 1 
					AND B.booking_code = '$booking_code'
					order by bp.ticket_number asc";

		$dataService = $this->getMaster("t_mtr_service ","id","name");
		$dataShipClass = $this->getMaster("t_mtr_ship_class ","id","name");
		$dataPort = $this->getMaster("t_mtr_port","id","name"); 
		$dataCityPort = $this->getMaster("t_mtr_port","id","city"); 
		$dataPassangerType = $this->getMaster("t_mtr_passanger_type","id","name");

		$paymentType = $this->select_data("app.t_mtr_payment_type", "  ")->result();
		$dataPaymentType = [];
		foreach ($paymentType as $keyPayment => $valuePayment) {
			$dataPaymentType[$valuePayment->payment_type] =$valuePayment->name; 
		}

		$data = $this->db->query($qry)->result();		
		foreach ($data as $key => $value) {

			$origin = @$dataPort[$value->origin];
			$origin_city = @$dataCityPort[$value->origin];
			$destination = @$dataPort[$value->destination];
			$destination_city = @$dataCityPort[$value->destination];

			$value->passanger_type = @$dataPassangerType[$value->passanger_type_id];
			$value->ship_class = $dataShipClass[$value->ship_class];
			$value->origin = $origin;
			$value->origin_city = $origin_city;
			$value->destination = $destination;
			$value->destination_city = $destination_city;
			
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

			$returnData[]=$value;
		}
		 return $returnData;
	}   
	public function getNamaPetugas($obcode, $bookingChannel)
    {

        $qry ="SELECT
						concat(tmu.first_name,' ',tmu.last_name) as username
                    from app.t_trx_opening_balance ttob 
                    join core.t_mtr_user tmu on ttob.user_id = tmu.id 
                    where ob_code ='$obcode' ";
		if(strtoupper($bookingChannel)=="VM")
		{
			$qry ="SELECT  
							tmdt.terminal_name  as username
						from 
							app.t_trx_opening_balance_vm ttobv 
						join app.t_mtr_device_terminal tmdt on ttobv .terminal_code = tmdt.terminal_code 
						where ob_code ='$obcode' ";
		}

        return $this->db->query($qry)->row();
    } 	 		  		

}
