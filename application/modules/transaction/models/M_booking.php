<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class M_booking extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function dataList()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$depart_date = trim($this->input->post('depart_date'));
		$channel = strtolower($this->enc->decode($this->input->post('channel')));
		$merchant = $this->enc->decode($this->input->post('merchant'));
		$status = $this->enc->decode($this->input->post('status'));
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$outletId = $this->enc->decode($this->input->post('outletId'));
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));
		$device = [];
		$keterangan = $this->enc->decode($this->input->post('keterangan'));
		// cek app get_identity_app
		if ($this->get_identity_app() == 0) 
		{
			if (!empty($this->session->userdata("port_id"))) 
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
			$port_origin = $this->get_identity_app();
		}

		$field = array(
			0 => 'created_on',
			1 => 'customer_name',
			2 => 'service_name',
			3 => 'trans_number',
			4 => 'booking_code',
			5 => 'port_origin',
			6 => 'port_destination',
			7 => 'depart_date',
			8 => 'created_on',
			9 => 'total_passanger',
			10 => 'amount',
			11 => 'booking_channel',
			12 => 'email',
			13 => 'phone_number',
			14 => 'card_no',
			15 => 'terminal_code',
			16 => 'terminal_name',
			17 => 'outlet_id',
			18 => 'outlet_id',
			19 => 'status',
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		// $where = " WHERE a.status <>'-6' and (date(a.created_on) between '" . $dateFrom . "' and '" . $dateTo . "' ) ";
		$where = " WHERE a.status<>'-6'  and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";

		if (!empty($service_id)) {
			$where .= "and (a.service_id=" . $service_id . ")";
		}

		if (!empty($depart_date)) {
			$where .= "and (a.depart_date='" . $depart_date . "')";
		}

		if (!empty($port_origin)) {
			$where .= "and (a.origin=" . $port_origin . ")";
		}

		if (!empty($port_destination)) {
			$where .= "and (a.destination=" . $port_destination . ")";
		}

		if (!empty($channel)) {
			$where .= "and (d.channel  ='" . $channel . "' )";
		}

		if (!empty($merchant)) {
			$where .= " and (d.created_by ='" . $merchant . "' )";
		}

		if (!empty($keterangan)) {
			if ($keterangan == 'overpaid'){
			 	$where .= "and top.status = 1 ";
			}
			elseif ($keterangan == 'underpaid') {
				$where .= "and tup.status = 1";
			}
			else
			{
				$where .= " ";
			}
		}

		//use != null because value 0 treated as empty
		if ($status != null) {
			$where .= " and (a.status = " . $status . ")";
		}

		if ($outletId != null) {
			$where .= " and (d.outlet_id = '" . $outletId . "')";
		}


		if(!empty($searchData))
		{
			if($searchName=="transNumber")
			{
				$where .= "and (a.trans_number  ='" . $iLike . "')";

			}
			else if($searchName=="passName")
			{
				$where .= "and (d.customer_name ilike '%" . $iLike . "%')";
			}
			else if($searchName=="phone")
			{
				$where .= "and (d.phone_number ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="email")
			{
				$where .= "and (d.email ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="bookingCode")
			{
				$where .= "and (a.booking_code ='" . $iLike . "' )";
			}
			else if($searchName=="cardNo")
			{
				$where .= "and (ttp.card_no ilike  '%" . $iLike . "%' )";
			}
			else if($searchName=="terminalCode")
			{
				$where .= "and (d.terminal_code ilike  '%" . $iLike . "%' )";	
			}
			else if($searchName=="refNo")
			{
				$where .= "and (ref_no ilike  '%" . $iLike . "%' )";	
			}	

			if($searchName=="device"){
				$sql_device = "select terminal_name, terminal_code from app.t_mtr_device_terminal where terminal_name ilike '%" . $iLike . "%';";
				$device = $this->dbView->query($sql_device)->result_array();

			}
		
		}

		if (!empty($keterangan)) 
		{
			$sql = $this->qryUnderOverPaid($where,$device);
		}
		else
		{
			$sql = $this->qry($where,$device);
		}

		// echo $sql; exit;

		// $query         = $this->dbView->query($sql);
		// $records_total = $query->num_rows();

		if (!empty($keterangan)) 
		{
			$sqlCount = $this->countQryUnderOverPaid($where,$device);
		}
		else
		{
			$sqlCount = $this->countQry($where,$device);
		}
		
		// echo $sqlCount; exit;

		// $recordCount        = $this->dbView->query($sqlCount)->row();
		// $records_total = (int)$recordCount->countdata;
		$recordCount        = $this->dbView->query($sqlCount)->row();
		$records_total = $recordCount->total;

	
		$sql 		  .= " ORDER BY  " . $order_column . " {$order_dir}";

		// echo $sql; exit;

		if ($length != -1) {
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}
		
		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();
		// print_r($rows_data);exit;
		$rows 	= array();
		$i  	= ($start + 1);

		$getOutletId = array_filter(array_unique(array_column($rows_data, "outlet_id")));

		$dataMerchant=[];
		if(count($getOutletId)>0)
		{	
			$outletId2=array();
			foreach ($getOutletId as $getOutletId2) {
				$outletId2[]="'".$getOutletId2."'";				
			}
			// print_r($outletId2); 
			// exit;
			$getDataMerchant = $this->dbView->query("select 
							mco.merchant_id, 
							mco.outlet_id, 
							mc.merchant_name 
						from app.t_mtr_outlet_merchant mco 
						join app.t_mtr_merchant mc on mco.merchant_id=mc.merchant_id
						where mco.outlet_id in (".implode(", ",$outletId2).")")->result();

			// print_r($getDataMerchant); exit;

			foreach ($getDataMerchant as  $key => $getDataMerchant2) 
			{
				$dataMerchant[$getDataMerchant2->outlet_id]=array(
					"merchant_id"=>$getDataMerchant2->merchant_id,
					"merchant_name"=>$getDataMerchant2->merchant_name,
				);
			} 	
			
			// print_r($dataMerchant); exit;
		}
		$status = $this->db->query("select * from app.t_mtr_status where tbl_name='t_trx_booking';")->result_array();
		$status_column = array_column($status,"status");
		$dataService = $this->getMaster('t_mtr_service');
		$dataPort = $this->getMaster('t_mtr_port');

		$booking_code_key = "'" . implode("', '", array_values(array_unique(array_column(json_decode(json_encode($rows_data), true),"booking_code")))) . "'"; 
		
		if($booking_code_key != ""){
			$overpaid = $this->db->query("select id, booking_code, a.created_on
										from app.t_trx_over_paid a
									where a.booking_code in ($booking_code_key) 
									;")->result_array();
			$underpaid = $this->db->query("select a.id, a.booking_code, a.created_on
										from app.t_trx_under_paid a 
										JOIN  app.t_trx_payment b ON b.trans_number = a.trans_number
									where a.booking_code in ($booking_code_key) 
									;")->result_array();
		}
		else{
			$overpaid = [];
			$underpaid = [];
		}
	
		$overpaid_column = array_column($overpaid,"booking_code");
		$underpaid_column = array_column($underpaid,"booking_code");
		if(count($device)< 1){
			$device_terminal_key = "'" . implode("', '", array_values(array_unique(array_column(json_decode(json_encode($rows_data), true),"terminal_code")))) . "'"; 
			$sql_device = "select terminal_name, terminal_code from app.t_mtr_device_terminal where terminal_code in (".$device_terminal_key.");";
			$device = $this->dbView->query($sql_device)->result_array();
		}
		$device_column = array_column($device,"terminal_code");
		foreach ($rows_data as $row) {
			$row->number = $i;
			$key_status = array_search($row->status, $status_column);  
			$row->description = (is_numeric($key_status)) ?  $status[$key_status]["description"]:null;
			$booking_code = $this->enc->encode($row->booking_code);
			$detail_url 	= site_url($this->_module . "/detail/{$booking_code}");

			$row->actions = "";
			$row->keterangan = "";
			$row->terminal_name = "";

			$row->service_name= $dataService[$row->service_id];
			$row->port_origin = $dataPort[$row->origin];
			$row->port_destination = $dataPort[$row->destination];

			$row->status= $row->description;

			$row->merchant_name=empty($row->outlet_id)?"":$dataMerchant[$row->outlet_id]["merchant_name"];
			

			$row->actions .= generate_button_new($this->_module, 'detail', $detail_url);

			$row->created_on = format_dateTime($row->created_on);
			$row->depart_date = format_date($row->depart_date);
			$row->port_origin = strtoupper($row->port_origin);
			$row->port_destination = strtoupper($row->port_destination);

			$row->description = success_label($row->description);

			// $row->status=success_label($row->description);

			$key_overpaid = array_search($row->booking_code, $overpaid_column);  
			$key_underpaid = array_search($row->booking_code, $underpaid_column); 
			
			$row->keterangan = (is_numeric($key_overpaid)) ?  $row->keterangan."Lebih Bayar ":"";
			$row->keterangan = (is_numeric($key_underpaid)) ?  $row->keterangan."Kurang Bayar":$row->keterangan;

			$row->amount = idr_currency($row->amount);
			// $row->created_on=format_dateTimeHis($row->created_on);
			
			$key_device= array_search($row->terminal_code, $device_column); 
			$row->terminal_name = (is_numeric($key_device)) ? $device[$key_device]["terminal_name"]:null;
			
			$row->no = $i;

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'data'           => $rows
		);
	}

	public function getMaster($table)
	{
		
		$service =  $this->select_data("app.$table")->result() ;

		$dataReturn=array();

		foreach ($service as $key => $value) {
			$dataReturn[$value->id]= $value->name;
		}

		return $dataReturn ;

	}

	public function listDetail_backup($where = "")
	{

		return $this->dbView->query("
								select i.name as identity_name, g.name as service_name,f.name as shift_class_name, 
								e.name as destination_name, d.name as origin_name,
								h.total_passanger, h.amount as grand_total,
								c.name as special_service_name, b.name as passenger_type_name, a.* 
								from app.t_trx_booking_passanger a
								left join  app.t_mtr_passanger_type b on a.passanger_type_id=b.id
								left join app.t_mtr_special_service c on a.special_service_id=c.id
								left join app.t_mtr_port d on a.origin=d.id
								left join app.t_mtr_port e on a.destination=e.id
								left join app.t_mtr_ship_class f on a.ship_class=f.id
								left join app.t_mtr_service g on a.service_id=g.id
								left join app.t_trx_booking h on a.booking_code=h.booking_code
								left join app.t_mtr_passanger_type_id i on a.id_type=i.id
								$where
							 ");
	}

	public function listDetail($where = "")
	{
		$query = $this->dbView->query("
								select i.name as identity_name, g.name as service_name,f.name as shift_class_name, 
								e.name as destination_name, d.name as origin_name,
								h.total_passanger, h.amount as grand_total,
								c.name as special_service_name, b.name as passenger_type_name, a.* 
								from app.t_trx_booking_passanger a
								left join  app.t_mtr_passanger_type b on a.passanger_type_id=b.id
								left join app.t_mtr_special_service c on a.special_service_id=c.id
								left join app.t_mtr_port d on a.origin=d.id
								left join app.t_mtr_port e on a.destination=e.id
								left join app.t_mtr_ship_class f on a.ship_class=f.id
								left join app.t_mtr_service g on a.service_id=g.id
								left join app.t_trx_booking h on a.booking_code=h.booking_code
								left join app.t_mtr_passanger_type_id i on a.id_type=i.id
								$where
							 ");
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
			$row->ticket_number = '<a href="'.$url.'" target="_blank">'.$row->ticket_number.'</a>';

			$row->no = $i;

			$rows[] = $row;
			unset($row->id);

			$i++;

			
		}
		return $rows;
	}

	public function listVehicle_backup($where = "")
	{

		return $this->dbView->query("
							select g.name as service_name,f.name as shift_class_name, e.name as destination_name, d.name as origin_name,
							h.total_passanger, h.amount,
							b.name as vehicle_class_name, a.* from app.t_trx_booking_vehicle a
							left join  app.t_mtr_vehicle_class b on a.vehicle_class_id=b.id
							left join app.t_mtr_port d on a.origin=d.id
							left join app.t_mtr_port e on a.destination=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_mtr_service g on a.service_id=g.id
							left join app.t_trx_booking h on a.booking_code=h.booking_code	
							$where
							 ");
	}

	public function listVehicle($where = "")
	{

		$query = $this->dbView->query("
							select g.name as service_name,f.name as shift_class_name, e.name as destination_name, d.name as origin_name,
							h.total_passanger, h.amount,
							b.name as vehicle_class_name, a.* from app.t_trx_booking_vehicle a
							left join  app.t_mtr_vehicle_class b on a.vehicle_class_id=b.id
							left join app.t_mtr_port d on a.origin=d.id
							left join app.t_mtr_port e on a.destination=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_mtr_service g on a.service_id=g.id
							left join app.t_trx_booking h on a.booking_code=h.booking_code	
							$where
							 ");

		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
			$row->ticket_number = '<a href="'.$url.'" target="_blank">'.$row->ticket_number.'</a>';

			$row->no = $i;

			$rows[] = $row;
			unset($row->id);

			$i++;

		}
		return $rows;
	}

	public function select_data($table, $where = "")
	{
		return $this->dbView->query("select * from $table $where");
	}

	public function download()
	{
		// echo "tes"; exit;
		$dateFrom = $this->input->get("dateFrom");
		$dateTo = $this->input->get("dateTo");
		$service_id = $this->enc->decode($this->input->get("service"));
		$port_destination = $this->enc->decode($this->input->get("port_destination"));
		$search = $this->input->get("search");
		$depart_date = trim($this->input->get('depart_date'));
		$channel = $this->enc->decode($this->input->get('channel'));
		$merchant = strtolower($this->enc->decode($this->input->get('merchant')));
		$status = $this->enc->decode($this->input->get('status'));
		$searchData = $this->input->get('searchData');
		$searchName = $this->input->get('searchName');
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));
		$outletId = $this->enc->decode($this->input->get('outletId'));

		// echo "tes"; exit;

		// cek app get_identity_app
		if ($this->get_identity_app() == 0)
		{
			if (!empty($this->session->userdata("port_id")))
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
			$port_origin = $this->get_identity_app();
		}
		// echo "tes"; exit;

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status<>'-6'  and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";
		// $where = " WHERE a.status <>'-6' and (date(a.created_on) between '" . $dateFrom . "' and '" . $dateTo . "' ) ";

		// echo "tes"; exit;
		if (!empty($service_id)) {
			$where .= "and (a.service_id=" . $service_id . ")";
		}

		if (!empty($depart_date)) {
			$where .= "and (a.depart_date='" . $depart_date . "')";
		}

		if (!empty($port_origin)) {
			$where .= "and (a.origin=" . $port_origin . ")";
		}

		if (!empty($port_destination)) {
			$where .= "and (a.destination=" . $port_destination . ")";
		}

		if (!empty($channel)) {
			$where .= "and (d.channel  ='" . $channel . "' )";
		}

		if (!empty($merchant)) {
			$where .= " and (d.created_by = '" . $merchant . "')";
		}

		// echo "tes"; exit;

		//use != null because value 0 treated as empty
		if ($status != null) {
			$where .= " and (a.status = " . $status . ")";
		}
		if ($outletId != null) {
			$where .= " and (d.outlet_id = '" . $outletId . "')";
		}
		// echo $searchName;
		if(!empty($searchData))
		{
			if($searchName=="transNumber")
			{
				$where .= "and (a.trans_number ilike '%" . $iLike . "%')";

			}
			else if($searchName=="passName")
			{
				$where .= "and (d.customer_name ilike '%" . $iLike . "%')";
			}
			else if($searchName=="phone")
			{
				$where .= "and (d.phone_number ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="email")
			{
				$where .= "and (d.email ilike '%" . $iLike . "%' )";
			}
			else if($searchName == "bookingCode")
			{
				$where .= "and (a.booking_code ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="cardNo")
			{
				$where .= "and (ttp.card_no ilike  '%" . $iLike . "%' )";
			}
			else if($searchName=="terminalCode")
			{
				$where .= "and (d.terminal_code ilike  '%" . $iLike . "%' )";	
			}
			else if($searchName=="refNo")
			{
				$where .= "and (ref_no ilike  '%" . $iLike . "%' )";	
			}									
			// else
			// {
			// 	$where .= "and (G.terminal_name ilike '%" . $iLike . "%')";
			// }
				
			if($searchName=="device"){
				$sql_device = "select terminal_name, terminal_code from app.t_mtr_device_terminal where terminal_name ilike '%" . $iLike . "%';";
				$device = $this->dbView->query($sql_device)->result();

			}
		}		

		

		$sql = $this->qry($where)." ORDER BY created_on DESC";
		
		$rows_data = $this->dbView->query($sql)->result();

		$getOutletId = array_filter(array_unique(array_column($rows_data, "outlet_id")));
		// echo "tes"; exit;
		$dataMerchant=[];
		if(count($getOutletId)>0)
		{	
			$outletId2=array();
			foreach ($getOutletId as $getOutletId2) {
				$outletId2[]="'".$getOutletId2."'";				
			}
			// print_r($outletId2); 
			// exit;
			$getDataMerchant = $this->dbView->query("select 
							mco.merchant_id, 
							mco.outlet_id, 
							mc.merchant_name 
						from app.t_mtr_outlet_merchant mco 
						join app.t_mtr_merchant mc on mco.merchant_id=mc.merchant_id
						where mco.outlet_id in (".implode(", ",$outletId2).")")->result();

			// print_r($getDataMerchant); exit;

			foreach ($getDataMerchant as  $key => $getDataMerchant2) 
			{
				$dataMerchant[$getDataMerchant2->outlet_id]=array(
					"merchant_id"=>$getDataMerchant2->merchant_id,
					"merchant_name"=>$getDataMerchant2->merchant_name,
				);
			} 	

		}
		// echo "tes"; exit;
		$status = $this->db->query("select * from app.t_mtr_status where tbl_name='t_trx_booking';")->result_array();
		$status_column = array_column($status,"status");		
		$dataService = $this->getMaster('t_mtr_service');
		$dataPort = $this->getMaster('t_mtr_port');

		$booking_code_key = "'" . implode("', '", array_values(array_unique(array_column(json_decode(json_encode($rows_data), true),"booking_code")))) . "'"; 
		
		if($booking_code_key != ""){
			$overpaid = $this->db->query("select id, booking_code, a.created_on
										from app.t_trx_over_paid a
									where a.booking_code in ($booking_code_key) 
									;")->result_array();
			$underpaid = $this->db->query("select a.id, a.booking_code, a.created_on
										from app.t_trx_under_paid a 
										JOIN  app.t_trx_payment b ON b.trans_number = a.trans_number
									where a.booking_code in ($booking_code_key) 
									;")->result_array();
		}
		else{
			$overpaid = [];
			$underpaid = [];
		}
		
		// echo "tes"; exit;
		$overpaid_column = array_column($overpaid,"booking_code");
		$underpaid_column = array_column($underpaid,"booking_code");

		if(count($device)< 1){
			$device_terminal_key = "'" . implode("', '", array_values(array_unique(array_column(json_decode(json_encode($rows_data), true),"terminal_code")))) . "'"; 
			$sql_device = "select terminal_name, terminal_code from app.t_mtr_device_terminal where terminal_code in (".$device_terminal_key.");";
			$device = $this->dbView->query($sql_device)->result_array();
		}
		$device_column = array_column($device,"terminal_code");		
		
		
		$rows = array();
		foreach ($rows_data as $row) {
			$row->number = $i;
			$key_status = array_search($row->status, $status_column);  
			$row->description = (is_numeric($key_status)) ?  $status[$key_status]["description"]:null;
			$booking_code = $this->enc->encode($row->booking_code);
			$detail_url 	= site_url($this->_module . "/detail/{$booking_code}");

			$row->actions = "";
			$row->keterangan = "";
			$row->terminal_name = '';

			$row->service_name= $dataService[$row->service_id];
			$row->port_origin = $dataPort[$row->origin];
			$row->port_destination = $dataPort[$row->destination];


			$row->status= $row->description;
			$row->merchant_name=empty($row->outlet_id)?"":$dataMerchant[$row->outlet_id]["merchant_name"];
			
			$row->channel = $row->booking_channel;
			$row->actions .= generate_button_new($this->_module, 'detail', $detail_url);

			$row->created_on = format_dateTime($row->created_on);
			$row->depart_date = format_date($row->depart_date);
			$row->port_origin = strtoupper($row->port_origin);
			$row->port_destination = strtoupper($row->port_destination);

			$row->description = success_label($row->description);

			$key_overpaid = array_search($row->booking_code, $overpaid_column);  
			$key_underpaid = array_search($row->booking_code, $underpaid_column); 
						
			$row->keterangan = (is_numeric($key_overpaid)) ?  $row->keterangan."Lebih Bayar ":"";
			$row->keterangan = (is_numeric($key_underpaid)) ?  $row->keterangan."Kurang Bayar":$row->keterangan;

			$key_device= array_search($row->terminal_code, $device_column); 
			$row->terminal_name = (is_numeric($key_device)) ? $device[$key_device]["terminal_name"]:null;
			
			// $row->status=success_label($row->description);

			$row->amount = idr_currency($row->amount);
			// $row->created_on=format_dateTimeHis($row->created_on);
			$row->no = $i;

			$rows[] = $row;
			unset($row->id);

			$i++;
		}		

		// print_r($rows_data); exit;
		return $rows;
	}

	function get_channel_backup()
	{
		$data  = array('' => 'SEMUA CHANNEL');
		$query = $this->dbView->query(" SELECT DISTINCT channel FROM app.t_trx_invoice where channel<>'' AND upper(channel) not in ('WEB1','ALFAMARTID')  ORDER BY channel asc ")->result();

		foreach ($query as $key => $value) {
			$data[$this->enc->encode($value->channel)] = strtoupper(str_replace('_', ' ', $value->channel));
		}

		return array_unique($data);
	}


	function get_channel()
	{
		
						$data[''] = 'SEMUA CHANNEL';
						$data[$this->enc->encode('b2b')] = 'B2B';
						$data[$this->enc->encode('ifcs')]='IFCS';
						$data[$this->enc->encode('mobile')]='MOBILE';
						$data[$this->enc->encode('pos_passanger')]='POS PASSANGER';
						$data[$this->enc->encode('pos_vehicle')]='POS VEHICLE';
						$data[$this->enc->encode('vm')]='VM';
						$data[$this->enc->encode('web')]='WEB';
						$data[$this->enc->encode('web_admin')]='WEB ADMIN';


		return array_unique($data);
	}
	public function get_identity_app()
	{
		$data = $this->dbView->query("select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}

	public function get_merchant()
	{
		$data = $this->dbView->query("SELECT DISTINCT merchant_name, merchant_id FROM app.t_mtr_merchant WHERE status = 1");
		return $data->result();
	}

	public function qry_15042023($where)
	{

		$data ="
			SELECT
					top.status as status_overpaid,
					tup.status as status_underpaid,
					d.channel as booking_channel,
					f.description,
					-- e.NAME AS service_name,
					A.origin,
					A.destination,
					A.service_id,
					d.customer_name,
					-- C.NAME AS port_destination,
					-- b.NAME AS port_origin,
					d.email,
					d.phone_number,
					A.booking_code,
					A.trans_number,
					A.depart_date,
					A.depart_time ,
					A.created_on ,
					A.created_by,
					A.id,
					A.amount ,
					A.total_passanger ,
					ttp.card_no,
					d.terminal_code,
					G.terminal_name,
					d.outlet_id,
					mc.merchant_name, 
					h.ref_no
				FROM
					app.t_trx_booking A
					LEFT JOIN app.t_trx_prepaid ttp ON ttp.trans_number = A.trans_number
					-- LEFT JOIN app.t_mtr_port b ON A.origin = b.ID
					-- LEFT JOIN app.t_mtr_port C ON A.destination = C.ID
					LEFT JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number
					-- LEFT JOIN app.t_mtr_service e ON A.service_id = e.ID
					LEFT JOIN app.t_mtr_status f ON A.status = f.status AND tbl_name = 't_trx_booking'
					LEFT JOIN app.t_mtr_device_terminal G ON d.terminal_code = G.terminal_code
					LEFT JOIN app.t_trx_under_paid tup ON 
						A.booking_code = tup.booking_code AND tup.status = 1
					LEFT JOIN app.t_trx_over_paid top ON top.booking_code = A.booking_code
					LEFT JOIN app.t_trx_payment h ON h.trans_number = d.trans_number
					left join app.t_mtr_outlet_merchant mco on d.outlet_id=mco.outlet_id
					left join app.t_mtr_merchant mc on mco.merchant_id=mc.merchant_id
				$where
		";	



		return $data;
	}

	public function qry($where,$array_device_code = null)
	{
		$field = "
				d.channel as booking_channel,
				-- f.description,
				A.origin,
				A.destination,
				A.service_id,
				d.customer_name,
				d.email,
				d.phone_number,
				A.booking_code,
				A.trans_number,
				A.depart_date,
				A.depart_time ,
				A.created_on ,
				A.created_by,
				A.id,
				A.amount ,
				A.total_passanger ,
				ttp.card_no,
				d.terminal_code,
				d.outlet_id,
				h.ref_no	,
				A.status
		";
		
	
		if($array_device_code){
			$array_device_code = "'" . implode("', '", array_values(array_unique(array_column($array_device_code,"terminal_code")))) . "'"; 
			$where .= " AND  d.terminal_code in (".$array_device_code.")";
		}
		$data ="SELECT
											
						{$field}
					FROM
						app.t_trx_booking A
						LEFT JOIN app.t_trx_prepaid ttp ON ttp.trans_number = A.trans_number and ttp.status in (0,1)
						JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number 
						LEFT JOIN app.t_trx_payment h ON h.trans_number = d.trans_number
					
					$where
		";	
		
		return $data;
	}	

	public function countQry($where,$array_device_code = null)
	{	
		if($array_device_code){
			$array_device_code = "'" . implode("', '", array_values(array_unique(array_column($array_device_code,"terminal_code")))) . "'"; 
			$where .= " AND  d.terminal_code in (".$array_device_code.")";
		}
		$data ="SELECT
					count(a.id) as total
				FROM
					app.t_trx_booking A
					LEFT JOIN app.t_trx_prepaid ttp ON ttp.trans_number = A.trans_number  and ttp.status in (0,1)
					JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number
					LEFT JOIN app.t_trx_payment h ON h.trans_number = d.trans_number
					$where
		";	
		// die($data); exit;
		return $data;
	}	


	public function countQry_15042023($where)
	{
		$data="
			SELECT
				count (A.id) as countdata
				FROM
					app.t_trx_booking A
					LEFT JOIN app.t_trx_prepaid ttp ON ttp.trans_number = A.trans_number
					LEFT JOIN app.t_mtr_port b ON A.origin = b.ID
					LEFT JOIN app.t_mtr_port C ON A.destination = C.ID
					LEFT JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number
					LEFT JOIN app.t_mtr_service e ON A.service_id = e.ID
					LEFT JOIN app.t_mtr_status f ON A.status = f.status AND tbl_name = 't_trx_booking'
					LEFT JOIN app.t_mtr_device_terminal G ON d.terminal_code = G.terminal_code
					LEFT JOIN app.t_trx_under_paid tup ON A.booking_code = tup.booking_code AND tup.status = 1					
					LEFT JOIN app.t_trx_payment h ON h.trans_number = d.trans_number
					left join app.t_mtr_outlet_merchant mco on d.outlet_id=mco.outlet_id
					left join app.t_mtr_merchant mc on mco.merchant_id=mc.merchant_id
				$where
		";

		return $data;
	}	

	public function qry_22062022($where)
	{
		$data_20042022="
			SELECT
					top.status as status_overpaid,
					tup.status as status_underpaid,
					d.channel as booking_channel,
					f.description,
					e.NAME AS service_name,
					d.customer_name,
					C.NAME AS port_destination,
					b.NAME AS port_origin,
					d.email,
					d.phone_number,
					A.*,
					ttp.card_no,
					d.terminal_code,
					G.terminal_name,
					d.outlet_id,
					mc.merchant_name,
					h.ref_no
				FROM
					app.t_trx_booking A
					LEFT JOIN app.t_trx_prepaid ttp ON ttp.trans_number = A.trans_number
					LEFT JOIN app.t_mtr_port b ON A.origin = b.ID
					LEFT JOIN app.t_mtr_port C ON A.destination = C.ID
					LEFT JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number
					LEFT JOIN app.t_mtr_service e ON A.service_id = e.ID
					LEFT JOIN app.t_mtr_status f ON A.status = f.status AND tbl_name = 't_trx_booking'
					LEFT JOIN app.t_mtr_device_terminal G ON d.terminal_code = G.terminal_code
					LEFT JOIN app.t_trx_under_paid tup ON tup.trans_number = d.trans_number OR A.booking_code = tup.booking_code AND tup.status = 1
					LEFT JOIN app.t_trx_over_paid top ON top.booking_code = A.booking_code
					LEFT JOIN app.t_trx_payment h ON h.trans_number = d.trans_number
					left join app.t_mtr_outlet_merchant mco on d.outlet_id=mco.outlet_id
					left join app.t_mtr_merchant mc on mco.merchant_id=mc.merchant_id
				$where
		";

		$data ="
			SELECT
					top.status as status_overpaid,
					tup.status as status_underpaid,
					d.channel as booking_channel,
					f.description,
					e.NAME AS service_name,
					d.customer_name,
					C.NAME AS port_destination,
					b.NAME AS port_origin,
					d.email,
					d.phone_number,
					A.booking_code,
					A.trans_number,
					A.depart_date,
					A.depart_time ,
					A.created_on ,
					A.created_by,
					A.id,
					A.amount ,
					A.total_passanger ,
					ttp.card_no,
					d.terminal_code,
					G.terminal_name,
					d.outlet_id,
					mc.merchant_name, 
					h.ref_no
				FROM
					app.t_trx_booking A
					LEFT JOIN app.t_trx_prepaid ttp ON ttp.trans_number = A.trans_number
					LEFT JOIN app.t_mtr_port b ON A.origin = b.ID
					LEFT JOIN app.t_mtr_port C ON A.destination = C.ID
					LEFT JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number
					LEFT JOIN app.t_mtr_service e ON A.service_id = e.ID
					LEFT JOIN app.t_mtr_status f ON A.status = f.status AND tbl_name = 't_trx_booking'
					LEFT JOIN app.t_mtr_device_terminal G ON d.terminal_code = G.terminal_code
					LEFT JOIN app.t_trx_under_paid tup ON 
						-- tup.trans_number = d.trans_number OR 
						A.booking_code = tup.booking_code AND tup.status = 1
					LEFT JOIN app.t_trx_over_paid top ON top.booking_code = A.booking_code
					LEFT JOIN app.t_trx_payment h ON h.trans_number = d.trans_number
					left join app.t_mtr_outlet_merchant mco on d.outlet_id=mco.outlet_id
					left join app.t_mtr_merchant mc on mco.merchant_id=mc.merchant_id
				$where
		";		

		return $data;
	}

	public function countQry_22062022($where)
	{
		$data="
			SELECT
				count (A.id) as countdata
				FROM
					app.t_trx_booking A
					LEFT JOIN app.t_trx_prepaid ttp ON ttp.trans_number = A.trans_number
					LEFT JOIN app.t_mtr_port b ON A.origin = b.ID
					LEFT JOIN app.t_mtr_port C ON A.destination = C.ID
					LEFT JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number
					LEFT JOIN app.t_mtr_service e ON A.service_id = e.ID
					LEFT JOIN app.t_mtr_status f ON A.status = f.status AND tbl_name = 't_trx_booking'
					LEFT JOIN app.t_mtr_device_terminal G ON d.terminal_code = G.terminal_code
					LEFT JOIN app.t_trx_under_paid tup ON tup.trans_number = d.trans_number OR A.booking_code = tup.booking_code AND tup.status = 1
					LEFT JOIN app.t_trx_over_paid top ON top.booking_code = A.booking_code
					LEFT JOIN app.t_trx_payment h ON h.trans_number = d.trans_number
					join app.t_mtr_outlet_merchant mco on d.outlet_id=mco.outlet_id
					join app.t_mtr_merchant mc on mco.merchant_id=mc.merchant_id
				$where
		";

		return $data;
	}
	
	function getUnderOverPaid()
	{
		
		$data[''] = 'Pilih';
		$data[$this->enc->encode('overpaid')] = 'LEBIH BAYAR';
		$data[$this->enc->encode('underpaid')]='KURANG BAYAR';
		

		return array_unique($data);
	}

	public function qryUnderOverPaid($where,$array_device_code = null)
	{
		$field = "
				d.channel as booking_channel,
				-- f.description,
				A.origin,
				A.destination,
				A.service_id,
				d.customer_name,
				d.email,
				d.phone_number,
				A.booking_code,
				A.trans_number,
				A.depart_date,
				A.depart_time ,
				A.created_on ,
				A.created_by,
				A.id,
				A.amount ,
				A.total_passanger ,
				ttp.card_no,
				d.terminal_code,
				d.outlet_id,
				h.ref_no	,
				top.status as keterangan_overpaid,
				tup.status as keterangan_underpaid,
				A.status
		";
		
	
		if($array_device_code){
			$array_device_code = "'" . implode("', '", array_values(array_unique(array_column($array_device_code,"terminal_code")))) . "'"; 
			$where .= " AND  d.terminal_code in (".$array_device_code.")";
		}
		$data ="SELECT
											
						{$field}
					FROM
						app.t_trx_booking A
						LEFT JOIN app.t_trx_prepaid ttp ON ttp.trans_number = A.trans_number and ttp.status in (0,1)
						JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number 
						LEFT JOIN app.t_trx_payment h ON h.trans_number = d.trans_number
						LEFT JOIN app.t_trx_under_paid tup ON 
						A.booking_code = tup.booking_code AND tup.status = 1
						LEFT JOIN app.t_trx_over_paid top ON top.booking_code = A.booking_code
					
					$where
		";	

		// echo($data);exit;
		
		return $data;
	}

	public function countQryUnderOverPaid($where,$array_device_code = null)
	{	
		if($array_device_code){
			$array_device_code = "'" . implode("', '", array_values(array_unique(array_column($array_device_code,"terminal_code")))) . "'"; 
			$where .= " AND  d.terminal_code in (".$array_device_code.")";
		}
		$data ="SELECT
					count(a.id) as total
				FROM
					app.t_trx_booking A
					LEFT JOIN app.t_trx_prepaid ttp ON ttp.trans_number = A.trans_number  and ttp.status in (0,1)
					JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number
					LEFT JOIN app.t_trx_payment h ON h.trans_number = d.trans_number
					LEFT JOIN app.t_trx_under_paid tup ON 
						A.booking_code = tup.booking_code AND tup.status = 1
					LEFT JOIN app.t_trx_over_paid top ON top.booking_code = A.booking_code
					$where
		";	
		// die($data); exit;
		return $data;
	}	
}

