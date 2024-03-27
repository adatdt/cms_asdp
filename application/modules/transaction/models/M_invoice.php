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

class M_invoice extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		// $this->_module = 'pelabuhan/dock';
	}

	public function dataList()
	{
		ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$service = $this->enc->decode($this->input->post('service'));
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$transaction_type = $this->enc->decode($this->input->post('transaction_type'));
		$status_type = $this->enc->decode($this->input->post('status_type'));
		$channel = $this->enc->decode($this->input->post('channel'));
		$merchant = $this->enc->decode($this->input->post('merchant'));
		$outletId = $this->input->post('outletId');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		// $iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

		if ($this->get_identity_app() == 0) {
			// mengambil port berdasarkan port di user menggunakan session
			if (!empty($this->session->userdata('port_id'))) {
				$port_origin = $this->session->userdata('port_id');
			} else {
				$port_origin = $this->enc->decode($this->input->post('port_origin'));
			}
		} else {
			$port_origin = $this->get_identity_app();
		}

		$field = array(
			// 0 => 'id',
			0 => 'created_on',
			1 => 'created_on',
			2 => 'trans_number',
			3 => 'customer_name',
			4 => 'phone_number',
			5 => 'email',
			6 => 'amount',
			7 => 'service_name',
			8 => 'port_origin',
			9 => 'port_destination',
			10 => 'channel',
			11 => 'terminal_name',
			12 => 'merchant_name',
			13 => 'outlet_id',
			14 => 'transaction_type',
			15 => 'status_invoice',
			16 => 'discount_code',
			17 => 'description',
			18 => 'due_date',
		);
		// die("tes"); exit;

		$order_column = $field[$order_column];
		
		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where2 = $where = "	WHERE a.status<>'-6' 
					and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";


		if (!empty($service)) {
			$where .= " and a.service_id = " . $service . " ";
		}

		if (!empty($port_origin)) {
			
			// serchin in table invoice
			$getOriginInv = $this->dbView->query("SELECT a.trans_number FROM app.t_trx_invoice a 
				JOIN app.t_trx_booking d ON a.trans_number = d.trans_number
			$where2 and  d.origin = $port_origin
			")->result();	

			$whereIdOrigin1 = array();
			if(!empty($getOriginInv))
			{
				$whereIdOrigin1 = array_column($getOriginInv,"trans_number") ;
			}

			// serchin in table underpaid
			$getOriginInv2 = $this->dbView->query("SELECT a.trans_number FROM app.t_trx_under_paid a 
			JOIN app.t_trx_booking d ON a.booking_code = d.booking_code
			$where2 and  d.origin = $port_origin
			")->result();	
			$whereIdOrigin2 = array();
			if(!empty($getOriginInv2))
			{
				// print_r($getOriginInv2); exit;
				$whereIdOrigin2 = array_column($getOriginInv2,"trans_number") ;				
			}			
			
			$whereIdOrigin = array_merge($whereIdOrigin1, $whereIdOrigin2);
			if(count($whereIdOrigin)>0)
			{
				$whereIdOriginString = array_map(function($a){ return "'".$a."'"; } , $whereIdOrigin);
				$where .= " and a.trans_number in (".implode(",", $whereIdOriginString).") ";
			}
			else
			{
				$where .= " and a.id is null ";
			}


		}

		if (!empty($port_destination)) {
			// serchin in table invoice
			$getDestInv = $this->dbView->query("SELECT a.trans_number FROM app.t_trx_invoice a 
				JOIN app.t_trx_booking d ON a.trans_number = d.trans_number
			$where2 and  d.destination = $port_destination
			")->result();	

			$whereIdDest1 = array();
			if(!empty($getDestInv))
			{
				$whereIdDest1 = array_column($getDestInv,"trans_number") ;
			}

			// serchin in table underpaid
			$getDestInv2 = $this->dbView->query("SELECT a.trans_number FROM app.t_trx_under_paid a 
			JOIN app.t_trx_booking d ON a.booking_code = d.booking_code
			$where2 and  d.destination = $port_destination
			")->result();	
			$whereIdDest2 = array();
			if(!empty($getDestInv2))
			{
				// print_r($getOriginInv2); exit;
				$whereIdDest2 = array_column($getDestInv2,"trans_number") ;				
			}			
			
			$whereIdDest = array_merge($whereIdDest1, $whereIdDest2);
			if(count($whereIdDest)>0)
			{
				$whereIdDestString = array_map(function($a){ return "'".$a."'"; } , $whereIdDest);
				$where .= " and a.trans_number in (".implode(",", $whereIdDestString).") ";
			}
			else
			{
				$where .= " and a.id is null ";
			}
		}

		if ($status_type != "") {
			$where .= " and a.status =" . $status_type . "";
		}

		if (!empty($transaction_type)) {
			$where .= " and a.transaction_type = " . $transaction_type . "";
		}

		if (!empty($channel)) {
			$where .= "and a.channel  ='" . $channel . "'";
		}

		if (!empty($merchant)) {
			$where .= " and a.created_by = '" . $merchant . "'";
		}		

		if (!empty($outletId)) {
			$where .= " and a.outlet_id ='" . $outletId . "'";
		}		



		if(!empty($searchData))
		{
			if($searchName=="transNumber")
			{
				$where .= "and a.trans_number = '" . $iLike . "' ";

			}
			else if($searchName=="passName")
			{
				$where .= "and a.customer_name ilike '%" . $iLike . "%' ";
			}
			else if($searchName=="phone")
			{
				$where .= "and a.phone_number ilike '%" . $iLike . "%' ";
			}
			else if($searchName=="email")
			{
				$where .= "and a.email ='" . $iLike . "' ";
			}
			else if($searchName=="device")
			{
				$dataDevice = $this->dbView->query(" select terminal_code,terminal_name from app.t_mtr_device_terminal where terminal_name ilike '%".$iLike."%'  ")->result();
				// print_r($dataDevice); exit;

				$terminal_code_id_key = "'" . implode("', '", array_values(array_unique(array_column(json_decode(json_encode($dataDevice), true),"terminal_code")))) . "'"; 
				// print_r($terminal_code_id_key); exit;
				$where .= " and  a.terminal_code in ($terminal_code_id_key) ";
			}			
			else
			{
				$where .= " ";
			}
		}
				
		$sql = $this->qry($where);

		$sqlCount = $this->countQry($where);
		$recordCount        = $this->dbView->query($sqlCount)->row();


		$records_total = (int)$recordCount->countdata;
		$sql 		  .= " ORDER BY " . $order_column . " {$order_dir}";

		if ($length != -1) {
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		$masterPort = $this->getDataMaster("app.t_mtr_port","id","name");	
		$masterStatus = $this->getDataMaster2("app.t_mtr_status","status","description"," where tbl_name = 't_trx_invoice' ");		
		$masterService = $this->getDataMaster("app.t_mtr_service","id","name");	
		$masterTransactionType = $this->getDataMaster("app.t_mtr_transaction_type","id","name");	

		$outlet_id_key = "'" . implode("', '", array_values(array_unique(array_column(json_decode(json_encode($rows_data), true),"outlet_id")))) . "'"; 

		if($outlet_id_key != ""){
			$outlet = $this->dbView->query("select a.outlet_id , a.merchant_id, b.merchant_name from app.t_mtr_outlet_merchant a
									JOIN app.t_mtr_merchant b ON a.merchant_id = b.merchant_id 
									where a.outlet_id in ($outlet_id_key) 
									;")->result_array();
		}
		else{
			$outlet = [];
		}
        $outlet_column = array_column($outlet,"outlet_id");	
				
		$device_code_key = array_unique(array_column($rows_data,"terminal_code"));		
		$masterTerminalCode[""]="";		
		if(!empty($device_code_key) ){
			$device_code_key_string = array_map(function($a){ return "'".$a."'"; }, $device_code_key);
			$device_code = $this->dbView->query("select terminal_name, terminal_code 
										from app.t_mtr_device_terminal 
										where terminal_code in (".implode(",",$device_code_key_string).") 
									;")->result();

			$masterTerminalCode += array_combine(array_column($device_code,"terminal_code"), array_column($device_code,"terminal_name"));
		}

        // $device_code_column = array_column($device_code,"terminal_code");				
		$discount_key = array_map(function($a){ return "'".$a."'"; },array_unique(array_column($rows_data,"discount_code")));	
		
		$masterDiscount = array();
		if(!empty($discount_key) ){
			
			$discount_code = $this->dbView->query("select 
													description, 
													discount_code 
												from app.t_mtr_discount
										where discount_code in (".implode(",",$discount_key).") 
									;")->result();
			
			$masterDiscount= array_combine(array_column($discount_code,"discount_code"),array_column($discount_code,"description"));					
		}

		// data kurang bayar
		$getTransNumber = array_column($rows_data,"trans_number");
		$masterOriginUp[""]="";
		$masterDestUp[""]="";
		$masterTerminalCodeUp[""]="";
		if(!empty($getTransNumber))
		{
			$getTransNumberString = array_map(function($a){ return "'".$a."'"; },$getTransNumber);	
			$qryUnderPaid = "SELECT
											b.origin, 
											b.destination, 
											a.trans_number ,
											a.terminal_code 
											from 
										app.t_trx_under_paid a 
										join app.t_trx_booking b on a.booking_code = b.booking_code 
										where a.trans_number in (".implode(",",$getTransNumberString).") ";

			$getDataUnderpaid = $this->dbView->query($qryUnderPaid)->result();
			if($getDataUnderpaid)
			{
				$masterOriginUp += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"origin"));
				$masterDestUp += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"destination"));
				$masterTerminalCodeUp += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"terminal_code"));
			}
		}

			// print_r($masterPort); exit;

		foreach ($rows_data as $row) {
			$row->number = $i;

			$id = $this->enc->encode($row->id);

			if(empty($row->origin))
			{
				$row->port_origin = @$masterPort[$masterOriginUp[$row->trans_number]];
				$row->port_destination = @$masterPort[$masterDestUp[$row->trans_number]];
				$row->terminal_name = $masterTerminalCode[$row->terminal_code];
			}
			else
			{				
				$row->port_origin = @$masterPort[$row->origin];
				$row->port_destination = @$masterPort[$row->destination];
				$row->terminal_name = $masterTerminalCode[$row->terminal_code];
			}

			
			$row->status_invoice = $masterStatus[$row->status];
			$row->service_name = $masterService[$row->service_id];
			$row->transaction_type_name = $masterTransactionType[$row->transaction_type];
			

			$key_outlet = array_search($row->outlet_id, $outlet_column);  
			$row->merchant_name = (is_numeric($key_outlet)) ?  $outlet[$key_outlet]["merchant_name"]:null;			

			
			$row->description ="";
			if(!empty($row->discount_code))
			{
				// $row->description = $masterDiscount[0][$row->discount_code];
				$row->description = $masterDiscount[$row->discount_code];
			}

			if ($row->status_invoice == "Dibayar"){
				$row->status_invoice = success_label("Dibayar");
			}
			else {
				$row->status_invoice = warning_label("$row->status_invoice");
			}
			$row->amount = idr_currency($row->amount);
			$row->created_on = format_dateTimeHis($row->created_on);
			$row->due_date = format_dateTimeHis($row->due_date);
			$row->no = $i;
			// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

			$rows[] = $row;
			// unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'data'           => $rows
		);
	}

	function getMap($discount_code)
	{
		return array_map(function ($arr)
		{
			return array($arr['discount_code']=>$arr['description']);
		}, $discount_code);	

	}

	public function select_data($table, $where = "")
	{
		return $this->dbView->query("select * from $table $where");
	}

	public function insert_data($table, $data)
	{
		$this->dbAction->insert($table, $data);
	}

	public function update_data($table, $data, $where)
	{
		$this->dbAction->where($where);
		$this->dbAction->update($table, $data);
	}

	public function delete_data($table, $data, $where)
	{
		$this->dbAction->where($where);
		$this->dbAction->delete($table, $data);
	}

	public function download()
	{
		$dateFrom = $this->input->get("dateFrom");
		$dateTo = $this->input->get("dateTo");
		$service = $this->enc->decode($this->input->get("service"));
		$port_destination = $this->enc->decode($this->input->get("port_destination"));
		$transaction_type = $this->enc->decode($this->input->get('transaction_type'));
		$status_type = $this->enc->decode($this->input->get('status_type'));
		$channel = $this->enc->decode($this->input->get('channel'));
		$merchant = $this->enc->decode($this->input->get('merchant'));
		$outletId = $this->enc->decode($this->input->get('outletId'));
		$searchData = $this->input->get("searchData");
		$searchName = $this->input->get("searchName");
		$iLike = trim($this->dbView->escape_like_str($searchData));

		// mengambil port berdasarkan port di user menggunakan session
		if ($this->get_identity_app() == 0) 
		{
			// mengambil port berdasarkan port di user menggunakan session
			if (!empty($this->session->userdata('port_id')))
			{
				$port_origin = $this->session->userdata('port_id');
			}
			 else 
			 {
				$port_origin = $this->enc->decode($this->input->get('port_origin'));
			}
		} else {
			$port_origin = $this->get_identity_app();
		}


		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where2 = $where = "	WHERE a.status<>'-6' 
					and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";


		if (!empty($service)) {
			$where .= " and a.service_id = " . $service . " ";
		}

		if (!empty($port_origin)) {
			
			// serchin in table invoice
			$getOriginInv = $this->dbView->query("SELECT a.trans_number FROM app.t_trx_invoice a 
				JOIN app.t_trx_booking d ON a.trans_number = d.trans_number
			$where2 and  d.origin = $port_origin
			")->result();	

			$whereIdOrigin1 = array();
			if(!empty($getOriginInv))
			{
				$whereIdOrigin1 = array_column($getOriginInv,"trans_number") ;
			}

			// serchin in table underpaid
			$getOriginInv2 = $this->dbView->query("SELECT a.trans_number FROM app.t_trx_under_paid a 
			JOIN app.t_trx_booking d ON a.booking_code = d.booking_code
			$where2 and  d.origin = $port_origin
			")->result();	
			$whereIdOrigin2 = array();
			if(!empty($getOriginInv2))
			{
				// print_r($getOriginInv2); exit;
				$whereIdOrigin2 = array_column($getOriginInv2,"trans_number") ;				
			}			
			
			$whereIdOrigin = array_merge($whereIdOrigin1, $whereIdOrigin2);
			if(count($whereIdOrigin)>0)
			{
				$whereIdOriginString = array_map(function($a){ return "'".$a."'"; } , $whereIdOrigin);
				$where .= " and a.trans_number in (".implode(",", $whereIdOriginString).") ";
			}
			else
			{
				$where .= " and a.id is null ";
			}


		}

		if (!empty($port_destination)) {
			// serchin in table invoice
			$getDestInv = $this->dbView->query("SELECT a.trans_number FROM app.t_trx_invoice a 
				JOIN app.t_trx_booking d ON a.trans_number = d.trans_number
			$where2 and  d.destination = $port_destination
			")->result();	

			$whereIdDest1 = array();
			if(!empty($getDestInv))
			{
				$whereIdDest1 = array_column($getDestInv,"trans_number") ;
			}

			// serchin in table underpaid
			$getDestInv2 = $this->dbView->query("SELECT a.trans_number FROM app.t_trx_under_paid a 
			JOIN app.t_trx_booking d ON a.booking_code = d.booking_code
			$where2 and  d.destination = $port_destination
			")->result();	
			$whereIdDest2 = array();
			if(!empty($getDestInv2))
			{
				// print_r($getOriginInv2); exit;
				$whereIdDest2 = array_column($getDestInv2,"trans_number") ;				
			}			
			
			$whereIdDest = array_merge($whereIdDest1, $whereIdDest2);
			if(count($whereIdDest)>0)
			{
				$whereIdDestString = array_map(function($a){ return "'".$a."'"; } , $whereIdDest);
				$where .= " and a.trans_number in (".implode(",", $whereIdDestString).") ";
			}
			else
			{
				$where .= " and a.id is null ";
			}
		}

		if ($status_type != "") {
			$where .= " and a.status =" . $status_type . "";
		}

		if (!empty($transaction_type)) {
			$where .= " and a.transaction_type = " . $transaction_type . "";
		}

		if (!empty($channel)) {
			$where .= "and a.channel  ='" . $channel . "'";
		}

		if (!empty($merchant)) {
			$where .= " and a.created_by = '" . $merchant . "'";
		}		

		if (!empty($outletId)) {
			$where .= " and a.outlet_id ='" . $outletId . "'";
		}		

		if(!empty($searchData))
		{
			if($searchName=="transNumber")
			{
				$where .= "and a.trans_number = '" . $iLike . "' ";

			}
			else if($searchName=="passName")
			{
				$where .= "and a.customer_name ilike '%" . $iLike . "%' ";
			}
			else if($searchName=="phone")
			{
				$where .= "and a.phone_number ilike '%" . $iLike . "%' ";
			}
			else if($searchName=="email")
			{
				$where .= "and a.email ='" . $iLike . "' ";
			}
			else if($searchName=="device")
			{
				$dataDevice = $this->dbView->query(" select terminal_code,terminal_name from app.t_mtr_device_terminal where terminal_name ilike '%".$iLike."%'  ")->result();
				// print_r($dataDevice); exit;

				$terminal_code_id_key = "'" . implode("', '", array_values(array_unique(array_column(json_decode(json_encode($dataDevice), true),"terminal_code")))) . "'"; 
				// print_r($terminal_code_id_key); exit;
				$where .= " and  a.terminal_code in ($terminal_code_id_key) ";
			}			
			else
			{
				$where .= " ";
			}
		}

		$sql =$this->qry($where)." ORDER BY a.created_on DESC";			
		// die($sql); exit;		

		$rows_data     = $this->dbView->query($sql)->result();

		$masterPort = $this->getDataMaster("app.t_mtr_port","id","name");	
		$masterStatus = $this->getDataMaster2("app.t_mtr_status","status","description"," where tbl_name = 't_trx_invoice' ");		
		$masterService = $this->getDataMaster("app.t_mtr_service","id","name");	
		$masterTransactionType = $this->getDataMaster("app.t_mtr_transaction_type","id","name");	

		$outlet_id_key = "'" . implode("', '", array_values(array_unique(array_column(json_decode(json_encode($rows_data), true),"outlet_id")))) . "'"; 

		if($outlet_id_key != ""){
			$outlet = $this->dbView->query("select a.outlet_id , a.merchant_id, b.merchant_name from app.t_mtr_outlet_merchant a
									JOIN app.t_mtr_merchant b ON a.merchant_id = b.merchant_id 
									where a.outlet_id in ($outlet_id_key) 
									;")->result_array();
		}
		else{
			$outlet = [];
		}
        $outlet_column = array_column($outlet,"outlet_id");	
				
		$device_code_key = array_unique(array_column($rows_data,"terminal_code"));		
		$masterTerminalCode[""]="";		
		if(!empty($device_code_key) ){
			$device_code_key_string = array_map(function($a){ return "'".$a."'"; }, $device_code_key);
			$device_code = $this->dbView->query("select terminal_name, terminal_code 
										from app.t_mtr_device_terminal 
										where terminal_code in (".implode(",",$device_code_key_string).") 
									;")->result();

			$masterTerminalCode += array_combine(array_column($device_code,"terminal_code"), array_column($device_code,"terminal_name"));
		}

        // $device_code_column = array_column($device_code,"terminal_code");				
		$discount_key = array_map(function($a){ return "'".$a."'"; },array_unique(array_column($rows_data,"discount_code")));	
		
		$masterDiscount = array();
		if(!empty($discount_key) ){
			
			$discount_code = $this->dbView->query("select 
													description, 
													discount_code 
												from app.t_mtr_discount
										where discount_code in (".implode(",",$discount_key).") 
									;")->result();
			
			$masterDiscount= array_combine(array_column($discount_code,"discount_code"),array_column($discount_code,"description"));					
		}

		// data kurang bayar
		$getTransNumber = array_column($rows_data,"trans_number");
		$masterOriginUp[""]="";
		$masterDestUp[""]="";
		$masterTerminalCodeUp[""]="";
		if(!empty($getTransNumber))
		{
			$getTransNumberString = array_map(function($a){ return "'".$a."'"; },$getTransNumber);	
			$qryUnderPaid = "SELECT
											b.origin, 
											b.destination, 
											a.trans_number ,
											a.terminal_code 
											from 
										app.t_trx_under_paid a 
										join app.t_trx_booking b on a.booking_code = b.booking_code 
										where a.trans_number in (".implode(",",$getTransNumberString).") ";

			$getDataUnderpaid = $this->dbView->query($qryUnderPaid)->result();
			if($getDataUnderpaid)
			{
				$masterOriginUp += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"origin"));
				$masterDestUp += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"destination"));
				$masterTerminalCodeUp += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"terminal_code"));
			}
		}

		$query = [];
		foreach ($rows_data as $row) {

			$row->status_invoice = $masterStatus[$row->status];
			$row->service_name = $masterService[$row->service_id];
			$row->transaction_type_name = $masterTransactionType[$row->transaction_type];			

			if(empty($row->origin))
			{
				$row->port_origin = @$masterPort[$masterOriginUp[$row->trans_number]];
				$row->port_destination = @$masterPort[$masterDestUp[$row->trans_number]];
				$row->terminal_name = $masterTerminalCode[$row->terminal_code];
			}
			else
			{				
				$row->port_origin = @$masterPort[$row->origin];
				$row->port_destination = @$masterPort[$row->destination];
				$row->terminal_name = $masterTerminalCode[$row->terminal_code];
			}		

			$row->description ="";
			if(!empty($row->discount_code))
			{
				// $row->description = $masterDiscount[0][$row->discount_code];
				$row->description = $masterDiscount[$row->discount_code];
			}			
			
			$key_outlet = array_search($row->outlet_id, $outlet_column);  
			$row->merchant_name = (is_numeric($key_outlet)) ?  $outlet[$key_outlet]["merchant_name"]:null;
						
			$query[] = $row;
		}		

		// print_r($query); exit;
		return $query;
	}

	public function get_identity_app()
	{
		$data = $this->dbView->query(" select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}


	function get_channel_bacup(){
		$data  = array(''=>'Pilih');
		$query = $this->dbView->query(" SELECT DISTINCT channel FROM app.t_trx_invoice where channel<>'' and upper(channel) not in ('WEB1','ALFAMARTID') ORDER BY channel asc ")->result();

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

	public function get_merchant()
	{
		$data = $this->dbView->query("SELECT DISTINCT merchant_name, merchant_id FROM app.t_mtr_merchant WHERE status = 1");
		return $data->result();
	}


	public function qry($where){

		$qry ="SELECT 
					d.origin,
					d.destination,
					a.created_on,
					a.trans_number,
					a.customer_name,
					a.phone_number,
					a.email,
					a.amount,
					a.service_id,
					a.channel,
					a.terminal_code,
					a.outlet_id,
					a.transaction_type,
					a.status,
					a.discount_code,
					a.due_date,
					a.id
				FROM app.t_trx_invoice a 
				LEFT JOIN app.t_trx_booking d ON a.trans_number = d.trans_number
				$where ";

		return $qry;
	}

	public function countQry($where){

		$qry = "SELECT 
						count(a.id) as countdata
						FROM app.t_trx_invoice a 
				LEFT JOIN app.t_trx_booking d ON a.trans_number = d.trans_number
						 $where
		";	

		return $qry;
	}	

    public function getDataMaster2($table,$id,$name, $where="")
    {

		$getData = $this->select_data($table,$where)->result();

		$data=[];
		foreach ($getData as $key => $value) {
			$data[$value->$id]=$value->$name;
		}

		$returnData = $data; 

        return $returnData;

    }
	
	public function getDataMaster($table,$id,$name)
    {
        $dataSesion = $this->session->userdata($table);
        if($dataSesion)
        {
            $returnData = $dataSesion; 
        } 
        else
        {
            $getData = $this->select_data($table," ")->result();
    
            $data=[];
            foreach ($getData as $key => $value) {
                $data[$value->$id]=$value->$name;
            }

            $returnData = $data; 
            $this->session->set_userdata($table, $data);
        }
		

        return $returnData;

    }	
}
