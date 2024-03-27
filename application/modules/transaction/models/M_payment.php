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

class M_payment extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'transaction/payment';
	}

	public function dataList()
	{
		ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		// $dateFrom = trim($this->input->post('dateFrom'));
		// $dateTo = trim($this->input->post('dateTo'));
		$dateTo = date("Y-m-d", strtotime(trim($this->input->post('dateTo'))));
		$dateFrom = date("Y-m-d", strtotime(trim($this->input->post('dateFrom'))));
		$service_id = $this->enc->decode($this->input->post('service'));
		$shift_id = $this->enc->decode($this->input->post('shift'));
		$channel = $this->enc->decode($this->input->post('channel'));
		$merchant = $this->enc->decode($this->input->post('merchant'));
		$sofId = $this->input->post('sofId');
		$due_date = trim($this->input->post('due_date'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$outletId = $this->enc->decode($this->input->post('outletId'));
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));
		// print_r($outletId);exit;
		if ($this->get_identity_app() == 0) 
		{
			if (!empty($this->session->userdata("port_id")))
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
			0 => 'id',
			1 => 'payment_date',
			2 => 'created_on',
			3 => 'trans_number',
			4 => 'customer_name',
			5 => 'invoice_date',
			6 => 'payment_type',
			7 => 'amount',
			8 => 'service_name',
			9 => 'channel',
			10 => 'shift_name',
			11 => 'origin',
			12 => 'destination',
			13 => 'depart_date',
			14 => 'depart_time_start',
			15 => 'transaction_type',
			16 => 'trans_code',
			17 => 'card_no',
			18 => 'sof_id',
			// 18 => 'payment_source',
			19 => 'discount_code',
			20 => 'description',
			21 => 'ref_no'
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status<>'-6'  and a.payment_date >= ". $this->db->escape($dateFrom) . " and a.payment_date < " . $this->db->escape($dateToNew) . " ";
		$whereShiftId = $where;
		$wherePrepaid = $where;

		if (!empty($due_date)) {
			$where .= " and ( b.invoice_date::date between " . $this->db->escape(date("Y-m-d", strtotime($due_date))). " and " . $this->db->escape(date("Y-m-d", strtotime($due_date))) . ") ";
		}

		if (!empty($service_id)) {
			$where .= " and b.service_id=" . $this->db->escape($service_id) . "   ";
		}

		if (!empty($shift_id)) {

			$qrySearchObCode = "select 
				d.ob_code, 
				ttob.shift_id  
			from app.t_trx_payment a
			JOIN app.t_trx_sell d ON d.trans_number = a.trans_number
			join app.t_trx_opening_balance ttob on d.ob_code = ttob.ob_code 
			{$whereShiftId}
			and ttob.shift_id=".$this->db->escape($shift_id) ;

			$getSearchDataShiftId = $this->dbView->query($qrySearchObCode)->result();
			if(!empty($getSearchDataShiftId))
			{
				$searchShiftObCode = array_unique(array_column($getSearchDataShiftId,"ob_code"));				
				$searchShiftObCodeString = array_map(function($a){ return "'".$a."'"; },$searchShiftObCode );
				$where .= " and   d.ob_code in (".implode(",",$searchShiftObCodeString).") ";
			}
			else
			{
				$where .= " and  a.id is null ";

			}

		}

		if (!empty($sofId)) {
			$where .= " and a.sof_id=" . $this->db->escape($sofId) . " ";
		}		

		if (!empty($port)) {
			$where .= " and b.port_id=" . $this->db->escape($port) . "  ";
		}

		if (!empty($channel)) {
			$where .= " and a.channel=" . $this->db->escape($channel) . " ";
		}

		if (!empty($merchant)) {
			$where .= " and a.created_by = " . $this->db->escape($merchant) . " ";
		}

		if (!empty($outletId)) {
			$where .= " and b.outlet_id =" . $this->db->escape($outletId). "";
		}


		if(!empty($searchData))
		{
			if($searchName=="transNumber")
			{
				$where .= "and a.trans_number ilike '%" . $iLike . "%' ESCAPE '!' ";

			}
			else if($searchName=="bookingCode")
			{
				$searchBooking = $this->select_data_field("app.t_trx_booking"," trans_number, booking_code"," where booking_code ilike '%" . $iLike . "%' ESCAPE '!'  ")->result();
				$searchBooking2 = $this->select_data_field("app.t_trx_under_paid"," trans_number, booking_code"," where booking_code ilike '%" . $iLike . "%' ESCAPE '!' ")->result();

				$dataBookingSearchTransnumber = array();
				if(!empty($searchBooking))
				{
					$n =  array_merge($dataBookingSearchTransnumber,array_column($searchBooking,"trans_number"));
					unset($dataBookingSearchTransnumber);
					$dataBookingSearchTransnumber = $n;
					unset($n);					
				}

				if(!empty($searchBooking2))
				{
					$n =  array_merge($dataBookingSearchTransnumber,array_column($searchBooking2,"trans_number"));
					unset($dataBookingSearchTransnumber);
					$dataBookingSearchTransnumber = $n;
					unset($n);
					
				}				

				if(count($dataBookingSearchTransnumber)>0)
				{					
					$dataBookingSearchTransnumbeStr = array_map(function($a){ return "'".$a."'"; }, array_unique($dataBookingSearchTransnumber));
					$where .= "and a.trans_number in  (".implode(",",$dataBookingSearchTransnumbeStr).") ";
				}
				else
				{
					$where .= "and a.id is null ";
				}

			}			
			else if($searchName=="passName")
			{
				$where .= "and b.customer_name ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
			else if($searchName=="paymentType")
			{
				$where .= "and a.payment_type ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
			else if($searchName=="discountCode")
			{
				$where .= "and b.discount_code ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
			else if($searchName=="transCode")
			{
				$qryPrepaid ="select 
					l.trans_number, 
					l.trans_code, 
					l.card_no
					FROM
						app.t_trx_payment a
					join app.t_trx_prepaid l on a.trans_number=l.trans_number
					{$wherePrepaid}
					and l.trans_code ilike '%" . $iLike . "%' ESCAPE '!'
					";
				$getDataPrepaid = $this->dbView->query($qryPrepaid)->result();
				if(!empty($getDataPrepaid))
				{
					$columnTransNumberString = array_map(function($a){ return "'".$a."'"; },array_unique(array_column($getDataPrepaid,"trans_number")));
					$where .= " and a.trans_number in(". implode(",",$columnTransNumberString) .")";								
				}
				else
				{
					$where .= " and  a.id is null ";
				}
			}
			else if($searchName=="cardNo")
			{
				$qryPrepaid ="select 
					l.trans_number, 
					l.trans_code, 
					l.card_no
					FROM
						app.t_trx_payment a
					join app.t_trx_prepaid l on a.trans_number=l.trans_number
					{$wherePrepaid}
					and l.trans_code ilike '%".$iLike."%'
					";
				$getDataPrepaid = $this->dbView->query($qryPrepaid)->result();
				if(!empty($getDataPrepaid))
				{
					$columnTransNumberString = array_map(function($a){ return "'".$a."'"; },array_unique(array_column($getDataPrepaid,"trans_number")));	
					$where .= " and a.trans_number in(". implode(",",$columnTransNumberString) .")";
				}
				else
				{
					$where .= " and  a.id is null ";
				}
			}
			else if($searchName=="sofId")
			{
				$where .= "and a.sof_id ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
			else if($searchName=="paymentSource")
			{
				$where .= "and a.payment_source ilike '%" . $iLike . "%' ESCAPE '!' ";
			}															
			else
			{
				$where .= "and ref_no ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
		}		

		$sql =$this->qry($where);

		// die($sql); exit;
		$sql_sum = $this->qrySum($where);
		$getSumData = $this->dbView->query($sql_sum)->result();
		$sumDataGetSumData[]=0;
		if($getSumData)
		{
			unset($sumDataGetSumData);
			$sumDataGetSumData = array_column($getSumData,"total_amount");
		}

		// echo array_sum($sumDataGetSumData); exit;
		$records_total = count((array)$getSumData);

		$sql 		  .= " ORDER BY " . $order_column . " {$order_dir}";
		
		if ($length != -1) {
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

        $dataPort = $this->getMaster("t_mtr_port","id","name");
        $dataService = $this->getMaster("t_mtr_service ","id","name");
		$dataTransactionType = $this->getMaster("t_mtr_transaction_type ","id","name");
		$dataMasterShift = $this->getMaster("t_mtr_shift ","id","shift_name");
        
		$getTransNumber = array_unique(array_column($rows_data,"trans_number"));
		
		// get data prepaid and underpaid
		$getTransCode[""] = "";
		$getNoCard[""] = "";

		$getBookingUnderPaid[""] = "";
		$getDestinationUnderPaid[""] = "";
		$getDepartDateUnderPaid[""] = "";
		$getDepartTimeUnderPaid[""] = "";
		$getServiceIdUnderPaid[""] = "";
		if(!empty($getTransNumber))
		{
			$getTransNumberString = array_map(function($a){ return "'".$a."'"; },$getTransNumber);
			$getDataPrepaid = $this->select_data_field("app.t_trx_prepaid"," trans_number, trans_code, card_no"," where trans_number in (".implode(",",$getTransNumberString).") ")->result();
			if($getDataPrepaid)
			{
				$getTransCode += array_combine(array_column($getDataPrepaid,"trans_number"),array_column($getDataPrepaid,"trans_code"));
				$getNoCard += array_combine(array_column($getDataPrepaid,"trans_number"),array_column($getDataPrepaid,"card_no"));
			}

			//getDataUnderpaid
			$qryUnderPaid = "SELECT 
								pd.trans_number ,
								ttb.booking_code ,
								ttb.service_id,
								ttb.destination ,
								ttb.depart_date,
								ttb.depart_time_start as depart_time,
								ttb.booking_code
							from app.t_trx_under_paid pd
							join app.t_trx_booking ttb on pd.booking_code = ttb.booking_code 
							where pd.trans_number in (".implode(",",$getTransNumberString).")
							";

			$getDataUnderpaid = $this->dbView->query($qryUnderPaid)->result();
			if($getDataUnderpaid)
			{
				$getBookingUnderPaid += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"booking_code"));
				$getDestinationUnderPaid += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"destination"));
				$getDepartDateUnderPaid += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"depart_date"));
				$getDepartTimeUnderPaid += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"depart_time"));
				$getServiceIdUnderPaid += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"service_id"));
			}
		}

		// get data discount
		$columnDiscount = array_unique(array_column($rows_data,"discount_code"));
		$masterDiscount[""] = "";
		if(!empty($columnDiscount))
		{
			$columnDiscountString = array_map(function($a){ return "'".$a."'"; },$columnDiscount);
			$getDataDescount = $this->select_data_field("app.t_mtr_discount "," discount_code, description "," where discount_code in (".implode(",",$columnDiscountString).") ")->result();
			if($getDataDescount)
			{
				$masterDiscount += array_combine(array_column($getDataDescount,"trans_number"),array_column($getDataDescount,"trans_code"));
			}
		}

		// getDataShift
		$columnObCode = array_unique(array_column($rows_data,"ob_code"));
		$masterShiftId[""] = "";
		if(!empty($columnObCode))
		{
			$columnObCodeString = array_map(function($a){ return "'".$a."'"; },$columnObCode);
			$qryObCode = "select 
				sll.ob_code, 
				ttob.shift_id  
			from app.t_trx_sell sll
			join app.t_trx_opening_balance ttob on sll.ob_code = ttob.ob_code 
			where sll.ob_code in (".implode(",",$columnObCodeString).") ";
			
			$getDataShiftId = $this->dbView->query($qryObCode)->result();

			if($getDataShiftId)
			{
				$masterShiftId += array_combine(array_column($getDataShiftId,"ob_code"),array_column($getDataShiftId,"shift_id"));
			}
		}


		// getData outlet and merchant
		$columnOutletId = array_unique(array_column($rows_data,"outlet_id"));
		$masterMerchant [""]=""; 
		if(!empty($columnOutletId))
		{
			$columnOutletIdString = array_map(function($a){ return "'".$a."'"; },$columnOutletId);
			$qryOutletId = "SELECT 
								a.outlet_id , 
								a.merchant_id, 
								b.merchant_name 
							from app.t_mtr_outlet_merchant a
							JOIN app.t_mtr_merchant b ON a.merchant_id = b.merchant_id 
							where a.outlet_id in (".implode(",",$columnOutletIdString).") ";

			$outletMerchant = $this->dbView->query($qryOutletId)->result();
			if($outletMerchant)
			{
				$masterMerchant += array_combine(array_column($outletMerchant,"outlet_id"),array_column($outletMerchant,"merchant_name"));
			}
		}

		
		// print_r($rows_data);exit;

		$dataAmountPage[] = 0;
		foreach ($rows_data as $row) {
			$row->number = $i;
			$dataAmountPage[] = $row->amount;

			$row->admin_fee = idr_currency($row->amount - $row->amount_invoice);
			$row->payment_date = format_dateTime($row->payment_date);
			$row->created_on = format_dateTime($row->created_on);
			$row->invoice_date = format_dateTime($row->invoice_date);
			$row->due_date 	   = format_dateTime($row->due_date);
			$row->amount 	   = idr_currency($row->amount);
			$row->amount_invoice 	   = idr_currency($row->amount_invoice);
			$row->depart_time  = format_time($row->depart_time);
			$row->depart_date = format_date($row->depart_date);

			$shiftId = @$masterShiftId[$row->ob_code];
			$row->shift_name = @$dataMasterShift[$shiftId];

			$row->origin = @$dataPort[$row->origin];
			$row->destination = @$dataPort[$row->destination];
			$row->tipe_transaksi = @$dataTransactionType[$row->transaction_type];
			$row->trans_code = @$getTransCode[$row->trans_number];
			$row->card_no = @$getNoCard[$row->trans_number];
			$row->description = @$masterDiscount[$row->discount_code];
			$row->service_name = @$dataService[$row->service_id];
			$row->merchant_name = @$masterMerchant[$row->outlet_id];

			$booking_code=$this->enc->encode($row->booking_code);
			$service_id=$this->enc->encode($row->service_id);
			// print_r($row->booking_code);exit;

			// jika transaksi under paid
			if(empty($row->booking_code))
			{
				$linkTicketSummary = site_url("transaction/ticket_summary/index/".$getBookingUnderPaid[$row->trans_number]);
				$row->booking_code = "<a href='".$linkTicketSummary."'  target ='_blank'  >".$getBookingUnderPaid[$row->trans_number]."</a>";
				$row->destination =  $dataPort[$getDestinationUnderPaid[$row->trans_number]];
				$row->service_name = $dataService[$getServiceIdUnderPaid[$row->trans_number]];
				$row->depart_time  = format_time($getDepartTimeUnderPaid[$row->trans_number]);
				$row->depart_date = format_date($getDepartDateUnderPaid[$row->trans_number]);
				$booking_code = $this->enc->encode($getBookingUnderPaid[$row->trans_number]);
			}
			else
			{
				$linkTicketSummary = site_url("transaction/ticket_summary/index/".$row->booking_code);
				$row->booking_code = "<a href='".$linkTicketSummary."'  target ='_blank'  >".$row->booking_code."</a>";
			}
			
			$download_ticket_url   = site_url($this->_module . "/download_ticket_pdf/{$booking_code}/{$service_id}");
			// $eticket ="<a  target='_blank'  class='btn btn-sm btn-default' id='download_ticket_pdf' onClick =".'"downloadPdf( '."'".$download_ticket_url."'".')"'."  title='Tiket Online'  ><i class='fa fa-file-pdf-o' style='color: #ea5460'></i> </a>";

			$eticket ="<a  target='_blank'  class='btn btn-sm btn-default' id='download_ticket_pdf' href='".$download_ticket_url."' title='Tiket Online'  ><i class='fa fa-file-pdf-o' style='color: #ea5460'></i> </a>";

            $download_receipt_url   = site_url($this->_module . "/download_tiket_receipt/{$booking_code}/{$service_id}");
            $receipt ="<a  target='_blank'  class='btn btn-sm btn-default' href='".$download_receipt_url."'    title='Receipt Online' ><i class='fa fa-file-pdf-o' style='color: #ea5460'></i> </a>";
			// $receipt  	 = site_url($this->_module."/download_tiket_receipt/{$booking_code}/{$service_id}");
			
		
			$download_receipt_goshow_url   = site_url($this->_module . "/download_receipt_goshow_pdf/{$booking_code}/{$service_id}");
            $buton_receipt_goshow_url ="<a  target='_blank'  class='btn btn-sm btn-default' href='$download_receipt_goshow_url'  title='receipt goshow' ><i class='fa fa-file-pdf-o' style='color: #ea5460'></i></a>";

			$row->actions = '';

			$dateCreated = date("Y-m-d", strtotime($row->created_on));
		
            if( $dateCreated >= '2022-01-01') // yang di tampilkan recieve dimulai dari data tanggal 1 januari 2022 sesuai dengan permintaan mockup
            {

				if( strtoupper($row->channel) == 'IFCS' || strtoupper($row->channel) == 'MOBILE' || strtoupper($row->channel) == 'WEB_ADMIN' || strtoupper($row->channel) == 'WEB' )
				{
					$row->actions .= generate_button($this->_module, 'reciept_pdf', $receipt);
					$row->actions .= generate_button($this->_module, 'ticket_pdf', $eticket);
				}
				if( strtoupper($row->channel) == 'POS_VEHICLE' || strtoupper($row->channel) == 'POS_PASSANGER' || strtoupper($row->channel) == 'VM' )
				{
					$row->actions .= generate_button($this->_module, 'reciept_pdf', $buton_receipt_goshow_url);
				}	

			}

			

			$row->no = $i;

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'sumData'		=>	array_sum($sumDataGetSumData),
			'data'           => $rows,
			'dataAmountPage' => idr_currency(array_sum($dataAmountPage)),
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),

		);
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
			if($service)
			{
				$dataReturn = array_combine(array_column($service ,$id),array_column($service ,$name));
			}

            $this->session->set_userdata(array("app.".$table => $dataReturn));
        }

		return $dataReturn ;

	}    	
	public function select_data($table, $where = "")
	{
		return $this->dbView->query("select * from $table $where");
	}

	public function select_data_field($table, $field, $where = "")
	{
		return $this->dbView->query("select {$field} from $table $where");
	}	

	function get_channel_backup()
	{
		$data  = array('' => 'SEMUA CHANNEL');
		$query = $this->dbView->query("SELECT DISTINCT channel FROM app.t_trx_payment ORDER BY channel")->result();

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

	function get_port()
	{
		$data  = array('' => 'SEMUA PELABUHAN');
		$query = $this->dbView->query("SELECT * FROM app.t_mtr_port WHERE status not in (-5) ORDER BY name")->result();

		foreach ($query as $key => $value) {
			$data[$this->enc->encode($value->id)] = strtoupper($value->name);
		}

		return $data;
	}

	public function download()
	{
		$dateFrom = $this->input->get('dateFrom');
		$dateTo = $this->input->get('dateTo');
		$due_date = $this->input->get('due_date');
		$channel = $this->enc->decode($this->input->get('channel'));
		$merchant = $this->enc->decode($this->input->get('merchant'));
		$sofId = $this->input->get('sofId');
		$shift_id = $this->enc->decode($this->input->get('shit'));
		$service_id = $this->enc->decode($this->input->get('service'));
		$outletId = $this->enc->decode($this->input->get('outletId'));
		$searchData = $this->input->get('searchData');
		$searchName = $this->input->get('searchName');
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

		if ($this->get_identity_app() == 0) 
		{
			if (!empty($this->session->userdata("port_id"))) 
			{
				$port = $this->session->userdata("port_id");
			}
			else
			{
				$port = $this->enc->decode($this->input->get('port')); // error di excel karena method belum di ganti get
			}
		} 
		else 
		{
			$port = $this->get_identity_app();
		}

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status<>'-6'  and a.payment_date >= '". $dateFrom . "' and a.payment_date < '" . $dateToNew . "'";
		$whereShiftId = $where;
		$wherePrepaid = $where;

		if (!empty($due_date)) {
			$where .= " and ( b.invoice_date::date between '" . $due_date . "' and '" . $due_date . "') ";
		}

		if (!empty($service_id)) {
			$where .= " and b.service_id=" . $service_id . "   ";
		}

		if (!empty($shift_id)) {

			$qrySearchObCode = "select 
				d.ob_code, 
				ttob.shift_id  
			from app.t_trx_payment a
			JOIN app.t_trx_sell d ON d.trans_number = a.trans_number
			join app.t_trx_opening_balance ttob on d.ob_code = ttob.ob_code 
			{$whereShiftId}
			and ttob.shift_id=".$shift_id ;

			$getSearchDataShiftId = $this->dbView->query($qrySearchObCode)->result();
			if(!empty($getSearchDataShiftId))
			{
				$searchShiftObCode = array_unique(array_column($getSearchDataShiftId,"ob_code"));				
				$searchShiftObCodeString = array_map(function($a){ return "'".$a."'"; },$searchShiftObCode );
				$where .= " and   d.ob_code in (".implode(",",$searchShiftObCodeString).") ";
			}
			else
			{
				$where .= " and  a.id is null ";

			}

		}

		if (!empty($sofId)) {
			$where .= " and a.sof_id='" . $sofId . "'  ";
		}		

		if (!empty($port)) {
			$where .= " and b.port_id=" . $port . "  ";
		}

		if (!empty($channel)) {
			$where .= " and a.channel='" . $channel . "' ";
		}

		if (!empty($merchant)) {
			$where .= " and a.created_by = '" . $merchant . "' ";
		}

		if (!empty($outletId)) {
			$where .= " and b.outlet_id ='" . $outletId . "' ";
		}

		if(!empty($searchData))
		{
			if($searchName=="transNumber")
			{
				$where .= "and a.trans_number ilike '%" . $iLike . "%' ESCAPE '!' ";

			}
			else if($searchName=="bookingCode")
			{
				$searchBooking = $this->select_data_field("app.t_trx_booking"," trans_number, booking_code"," where booking_code ilike '%" . $iLike . "%' ESCAPE '!' ")->result();
				$searchBooking2 = $this->select_data_field("app.t_trx_under_paid"," trans_number, booking_code"," where booking_code ilike '%" . $iLike . "%' ESCAPE '!' ")->result();

				$dataBookingSearchTransnumber = array();
				if(!empty($searchBooking))
				{
					$n =  array_merge($dataBookingSearchTransnumber,array_column($searchBooking,"trans_number"));
					unset($dataBookingSearchTransnumber);
					$dataBookingSearchTransnumber = $n;
					unset($n);					
				}

				if(!empty($searchBooking2))
				{
					$n =  array_merge($dataBookingSearchTransnumber,array_column($searchBooking2,"trans_number"));
					unset($dataBookingSearchTransnumber);
					$dataBookingSearchTransnumber = $n;
					unset($n);
					
				}				

				if(count($dataBookingSearchTransnumber)>0)
				{					
					$dataBookingSearchTransnumbeStr = array_map(function($a){ return "'".$a."'"; }, array_unique($dataBookingSearchTransnumber));
					$where .= "and a.trans_number in  (".implode(",",$dataBookingSearchTransnumbeStr).") ";
				}
				else
				{
					$where .= "and a.id is null ";
				}

			}			
			else if($searchName=="passName")
			{
				$where .= "and b.customer_name ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
			else if($searchName=="paymentType")
			{
				$where .= "and a.payment_type ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
			else if($searchName=="discountCode")
			{
				$where .= "and b.discount_code ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
			else if($searchName=="transCode")
			{
				$qryPrepaid ="select 
					l.trans_number, 
					l.trans_code, 
					l.card_no
					FROM
						app.t_trx_payment a
					join app.t_trx_prepaid l on a.trans_number=l.trans_number
					{$wherePrepaid}
					and l.trans_code ilike '%" . $iLike . "%' ESCAPE '!'
					";
				$getDataPrepaid = $this->dbView->query($qryPrepaid)->result();
				if(!empty($getDataPrepaid))
				{
					$columnTransNumberString = array_map(function($a){ return "'".$a."'"; },array_unique(array_column($getDataPrepaid,"trans_number")));
					$where .= " and a.trans_number in(". implode(",",$columnTransNumberString) .")";								
				}
				else
				{
					$where .= " and  a.id is null ";
				}
			}
			else if($searchName=="cardNo")
			{
				$qryPrepaid ="select 
					l.trans_number, 
					l.trans_code, 
					l.card_no
					FROM
						app.t_trx_payment a
					join app.t_trx_prepaid l on a.trans_number=l.trans_number
					{$wherePrepaid}
					and l.trans_code ilike '%" . $iLike . "%' ESCAPE '!'
					";
				$getDataPrepaid = $this->dbView->query($qryPrepaid)->result();
				if(!empty($getDataPrepaid))
				{
					$columnTransNumberString = array_map(function($a){ return "'".$a."'"; },array_unique(array_column($getDataPrepaid,"trans_number")));	
					$where .= " and a.trans_number in(". implode(",",$columnTransNumberString) .")";
				}
				else
				{
					$where .= " and  a.id is null ";
				}
			}
			else if($searchName=="sofId")
			{
				$where .= "and a.sof_id ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
			else if($searchName=="paymentSource")
			{
				$where .= "and a.payment_source ilike '%" . $iLike . "%' ESCAPE '!' ";
			}															
			else
			{
				$where .= "and ref_no ilike '%" . $iLike . "%' ESCAPE '!' ";
			}
			
		}

		$sql = $this->qry($where)." order by a.payment_date desc";		
		$rows_data   = $this->dbView->query($sql)->result();
		$rows 	= array();

        $dataPort = $this->getMaster("t_mtr_port","id","name");
        $dataService = $this->getMaster("t_mtr_service ","id","name");
		$dataTransactionType = $this->getMaster("t_mtr_transaction_type ","id","name");
		$dataMasterShift = $this->getMaster("t_mtr_shift ","id","shift_name");
        
		$getTransNumber = array_unique(array_column($rows_data,"trans_number"));
		
		// get data prepaid and underpaid
		$getTransCode[""] = "";
		$getNoCard[""] = "";

		$getBookingUnderPaid[""] = "";
		$getDestinationUnderPaid[""] = "";
		$getDepartDateUnderPaid[""] = "";
		$getDepartTimeUnderPaid[""] = "";
		$getServiceIdUnderPaid[""] = "";
		if(!empty($getTransNumber))
		{
			$getTransNumberString = array_map(function($a){ return "'".$a."'"; },$getTransNumber);
			$getDataPrepaid = $this->select_data_field("app.t_trx_prepaid"," trans_number, trans_code, card_no"," where trans_number in (".implode(",",$getTransNumberString).") ")->result();
			if($getDataPrepaid)
			{
				$getTransCode += array_combine(array_column($getDataPrepaid,"trans_number"),array_column($getDataPrepaid,"trans_code"));
				$getNoCard += array_combine(array_column($getDataPrepaid,"trans_number"),array_column($getDataPrepaid,"card_no"));
			}

			//getDataUnderpaid
			$qryUnderPaid = "SELECT 
								pd.trans_number ,
								ttb.booking_code ,
								ttb.service_id,
								ttb.destination ,
								ttb.depart_date,
								ttb.depart_time_start as depart_time,
								ttb.booking_code
							from app.t_trx_under_paid pd
							join app.t_trx_booking ttb on pd.booking_code = ttb.booking_code 
							where pd.trans_number in (".implode(",",$getTransNumberString).")
							";

			$getDataUnderpaid = $this->dbView->query($qryUnderPaid)->result();
			if($getDataUnderpaid)
			{
				$getBookingUnderPaid += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"booking_code"));
				$getDestinationUnderPaid += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"destination"));
				$getDepartDateUnderPaid += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"depart_date"));
				$getDepartTimeUnderPaid += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"depart_time"));
				$getServiceIdUnderPaid += array_combine(array_column($getDataUnderpaid,"trans_number"),array_column($getDataUnderpaid,"service_id"));
			}
		}

		// get data discount
		$columnDiscount = array_unique(array_column($rows_data,"discount_code"));
		$masterDiscount[""] = "";
		if(!empty($columnDiscount))
		{
			$columnDiscountString = array_map(function($a){ return "'".$a."'"; },$columnDiscount);
			$getDataDescount = $this->select_data_field("app.t_mtr_discount "," discount_code, description "," where discount_code in (".implode(",",$columnDiscountString).") ")->result();
			if($getDataDescount)
			{
				$masterDiscount += array_combine(array_column($getDataDescount,"trans_number"),array_column($getDataDescount,"trans_code"));
			}
		}

		// getDataShift
		$columnObCode = array_unique(array_column($rows_data,"ob_code"));
		$masterShiftId[""] = "";
		if(!empty($columnObCode))
		{
			$columnObCodeString = array_map(function($a){ return "'".$a."'"; },$columnObCode);
			$qryObCode = "select 
				sll.ob_code, 
				ttob.shift_id  
			from app.t_trx_sell sll
			join app.t_trx_opening_balance ttob on sll.ob_code = ttob.ob_code 
			where sll.ob_code in (".implode(",",$columnObCodeString).") ";
			
			$getDataShiftId = $this->dbView->query($qryObCode)->result();

			if($getDataShiftId)
			{
				$masterShiftId += array_combine(array_column($getDataShiftId,"ob_code"),array_column($getDataShiftId,"shift_id"));
			}
		}

		// getData outlet and merchant
		$columnOutletId = array_unique(array_column($rows_data,"outlet_id"));
		$masterMerchant [""]=""; 
		if(!empty($columnOutletId))
		{
			$columnOutletIdString = array_map(function($a){ return "'".$a."'"; },$columnOutletId);
			$qryOutletId = "SELECT 
								a.outlet_id , 
								a.merchant_id, 
								b.merchant_name 
							from app.t_mtr_outlet_merchant a
							JOIN app.t_mtr_merchant b ON a.merchant_id = b.merchant_id 
							where a.outlet_id in (".implode(",",$columnOutletIdString).") ";

			$outletMerchant = $this->dbView->query($qryOutletId)->result();
			if($outletMerchant)
			{
				$masterMerchant += array_combine(array_column($outletMerchant,"outlet_id"),array_column($outletMerchant,"merchant_name"));
			}
		}		


		foreach ($rows_data as $row) {

			$row->admin_fee = $row->amount - $row->amount_invoice;


			$shiftId = @$masterShiftId[$row->ob_code];
			$row->shift_name = @$dataMasterShift[$shiftId];


			$row->origin = @$dataPort[$row->origin];
			$row->destination = @$dataPort[$row->destination];
			$row->tipe_transaksi = @$dataTransactionType[$row->transaction_type];
			$row->trans_code = @$getTransCode[$row->trans_number];
			$row->card_no = @$getNoCard[$row->trans_number];
			$row->description = @$masterDiscount[$row->discount_code];
			$row->service_name = @$dataService[$row->service_id];
			$row->merchant_name = @$masterMerchant[$row->outlet_id];

			// jika transaksi under paid
			if(empty($row->booking_code))
			{
				$row->booking_code = $getBookingUnderPaid[$row->trans_number];
				$row->destination =  $dataPort[$getDestinationUnderPaid[$row->trans_number]];
				$row->service_name = $dataService[$getServiceIdUnderPaid[$row->trans_number]];
				$row->depart_time  = $getDepartTimeUnderPaid[$row->trans_number];
				$row->depart_date = $getDepartDateUnderPaid[$row->trans_number];
			}


			$rows[] = $row;
			unset($row->id);
		}

		return (object)$rows;

	}

	public function qry($where)
	{
		$data="SELECT  
				a.id,
				b.discount_code,
				b.invoice_date,
				b.transaction_type,
				a.payment_date,
				a.trans_number,
				a.payment_type,
				a.amount,
				a.channel,
				a.ref_no,
				b.customer_name,
				b.due_date,
				b.outlet_id,
				b.amount as amount_invoice,
				b.port_id as origin,
				b.service_id,
				c.destination ,
				c.depart_date,
				c.booking_code,
				a.payment_source,
				a.sof_id,
				a.created_on,
				d.ob_code,
				c.depart_time_start as depart_time
			FROM
				app.t_trx_payment a
			JOIN app.t_trx_invoice b ON b.trans_number = a.trans_number
			LEFT JOIN app.t_trx_booking c ON c.trans_number = a.trans_number 
			LEFT JOIN app.t_trx_sell d ON d.trans_number = a.trans_number
		{$where}
		";

		return $data;
	}

	public function qrySum($where)
	{
		$data="SELECT  
				a.amount as total_amount
			FROM
				app.t_trx_payment a
			JOIN app.t_trx_invoice b ON b.trans_number = a.trans_number
			LEFT JOIN app.t_trx_booking c ON c.trans_number = a.trans_number 
			LEFT JOIN app.t_trx_sell d ON d.trans_number = a.trans_number
		{$where}
		";

		return $data;
	}		
	public function get_identity_app()
	{
		$data = $this->dbView->query(" select * from app.t_mtr_identity_app ")->row();

		return $data->port_id;
	}

	public function get_merchant()
	{
		$data = $this->dbView->query("SELECT DISTINCT merchant_name, merchant_id FROM app.t_mtr_merchant WHERE status = 1");
		return $data->result();
	}

	public function getSofId(){

		$data['cc']='Credit Card';
		$data['gopay']='Gopay';
		$data['mc']='Mobile Cash';
		$data['klikdanamon']='Danamon Klikpay';
		$data['sakuku']='Sakuku';
		$data['klikpay']='BCA Klikpay';
		$data['permatanet']='Felisa Permatanet Closed';
		$data['tcash']='TCASH';
		$data['mandiriclickpay']='Mandiri Clickpay';
		$data['briva']='Felisa BRI Closed';
		$data['brivast']='Felisa BRI Open';
		$data['finpay021']='Felisa 021 Closed';
		$data['finpay126']='Felisa Garuda';
		$data['finpay195']='Felisa 195 Closed';
		$data['finpayst021']='Felisa 021 Open';
		$data['finpayst195']='Felisa 195 Open';
		$data['finpaytsel']='Felisa Tsel';
		$data['vabni']='Felisa BNI Closed';
		$data['vapermata']='Felisa Permata Closed';
		$data['vastbni']='Felisa BNI Open';
		$data['vastpermata']='Felisa Permata Open';
		$data['vamandiri']='Felisa Mandiri Closed';
		$data['vastmandiri']='Felisa Mandiri Open';

		$dataId['']='Pilih';
		$dataId['cc']='cc';
		$dataId['gopay']='gopay';
		$dataId['mc']='mc';
		$dataId['klikdanamon']='klikdanamon';
		$dataId['sakuku']='sakuku';
		$dataId['klikpay']='klikpay';
		$dataId['permatanet']='permatanet';
		$dataId['tcash']='tcash';
		$dataId['mandiriclickpay']='mandiriclickpay';
		$dataId['briva']='briva';
		$dataId['brivast']='brivast';
		$dataId['finpay021']='finpay021';
		$dataId['finpay126']='finpay126';
		$dataId['finpay195']='finpay195';
		$dataId['finpayst021']='finpayst021';
		$dataId['finpayst195']='finpayst195';
		$dataId['finpaytsel']='finpaytsel';
		$dataId['vabni']='vabni';
		$dataId['vapermata']='vapermata';
		$dataId['vastbni']='vastbni';
		$dataId['vastpermata']='vastpermata';
		$dataId['vamandiri']='vamandiri';
		$dataId['vastmandiri']='vastmandiri';

		return $dataId;		
	}

	public function ticket_passanger($where=""){

		return $this->db->query("SELECT
								BP.name as penumpang, 
								BP.id_number, 
								P.name as origin, 
								p.city as origin_city,	
								P2.name as destination,
								p2.city as destination_city,
								B.depart_date, 
								B.depart_time_start, 
								B.depart_time_end, 
								S.name as service,
								BP.gender,
								BP.age,
								SC.name as ship_class,
								SC.id as ship_class_id, 
								BP.ticket_number,
								I.customer_name,
								I.email,
								I.created_by,
								pt.name as passanger_type,
								I.phone_number,
								B.created_on,
								BP.fare,
								C.amount as amount_payment,
								C.created_on as created_payment,
								b.ticket_type,
                                b.channel,
								d.sof_name as virtual_account
							from app.t_trx_booking B
							left join app.t_trx_invoice I on I.trans_number = B.trans_number
							left join app.t_trx_booking_passanger BP on B.booking_code = BP.booking_code
							left join app.t_mtr_port P on P.id = B.origin
							left join app.t_mtr_port P2 on P2.id = B.destination
							left join app.t_mtr_service S on S.id = B.service_id
							left join app.t_mtr_ship_class SC on SC.id = B.ship_class
							left join app.t_mtr_passanger_type pt on BP.passanger_type_id = pt.id
							left join app.t_trx_payment c on c.trans_number = B.trans_number 
							left join app.t_mtr_sof_id_finnet d on d. sof_id = c.sof_id 
							$where
							order by BP.ticket_number asc
							");
	}

	public function ticket_vehicle($booking_code){
		// die($booking_code);
		$qry = "SELECT
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
							SC.id as ship_class_id,
							BV.ticket_number, 
							BP.ticket_number as ticket_number_passanger, 
							pt.name as passanger_type,
							-- VC.name as vehicle_name, 
							BV.vehicle_class_id,
							I.customer_name,
							I.customer_name as instansi,
							I.email,
							I.created_by,
							pt.name as passanger_type,
							I.phone_number,
							B.created_on ,
							BV.fare,
							C.amount as amount_payment,
							C.created_on as created_payment,
							B.ticket_type,
							B.channel,
							I.amount as amount_invoice,
							D.sof_name as virtual_account
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
						left join app.t_mtr_sof_id_finnet d on d. sof_id = c.sof_id 
						WHERE B.status != -5 AND BV.service_id = 2 
						AND B.booking_code = '{$booking_code}'
						order by bp.ticket_number asc
					";
		$data = $this->db->query($qry)->result();
		$dataVehicleClass = $this->getMaster("t_mtr_vehicle_class","id","name"); 
        $checkUnderPaid = $this->checkUnderpaid($booking_code);

		$return = [];
		foreach ($data as $key => $value) {

            $value->vehicle_name = @$dataVehicleClass[$value->vehicle_class_id];
            if(!empty($checkUnderPaid[$value->booking_code]))
            {
                $value->vehicle_name = @$dataVehicleClass[$checkUnderPaid[$value->booking_code]->old_vehicle_class];
            }

			$return[] = $value;
		}

		return $return;
	}        	

	function checkUnderpaid($bookingCode)
    {
        $qry = "select ttupv.* from app.t_trx_under_paid ttupv 
            join app.t_trx_payment ttp on ttupv.trans_number = ttp.trans_number 
            where ttupv.booking_code ='".$bookingCode."'
        " ;
        $data = $this->dbView->query($qry)->row();
        $return[$bookingCode] = "";
        if(!empty($data))
        {
            $return[$bookingCode] = $data;
        }

        return $return;
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
	public function count_payment($where=""){

		return $this->db->query("SELECT 	
								sum(fare) as count
								from  app.t_trx_booking_passanger 
								$where
								");
	}
	public function getRecieptPassenger($booking_code){
		
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
	public function checkUnderpaidTicket($bookingCode, $dataVehicle)
    {
        $qry = "select 
                    ttp.channel ,
                    ttp.payment_type ,
                    ttp.amount as amount_payment,
                    tti.amount as amount_invoice,
                    ttupv.new_fare,
                    ttupv.old_fare, 
                    ttupv.new_vehicle_class,
                    ttupv.old_vehicle_class,
                    ttupv.created_by
             from app.t_trx_under_paid ttupv 
            join app.t_trx_payment ttp on ttupv.trans_number = ttp.trans_number 
            join app.t_trx_invoice tti on ttupv.trans_number =  tti.trans_number  
            where ttupv.booking_code ='".$bookingCode."'
            order by ttupv.created_on desc
        " ;
        $data = $this->dbView->query($qry)->result();

		$paymentType = $this->select_data("app.t_mtr_payment_type", "  ")->result();
		$dataPaymentType = [];
		foreach ($paymentType as $keyPayment => $valuePayment) {
			$dataPaymentType[$valuePayment->payment_type] =$valuePayment->name; 
		}        

        $index = count((array)$data);        
        $myData = [];

        $firstVehicleClass = $dataVehicle[0]->vehicle_class_id;
        foreach ($data  as $key => $value) {

            if($key == 0)
            {
                $firstVehicleClass = $value->old_vehicle_class;
            }

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

            $myData []=(object)array(
                "fare"=>$value->new_fare,
                "vehicle_class"=>$value->new_vehicle_class,
                "created_by"=>$value->created_by,
                "channel"=>$value->channel,
                "payment_type"=>$value->payment_type,
                "amount_payment"=>$value->amount_payment,
                "amount_invoice"=>$value->amount_invoice,
                "getNamePayment"=> $value->getNamePayment
                
            );
            
        }

        $myData[] = (object)array(
            "fare"=>$dataVehicle[0]->amount_invoice,
            "vehicle_class"=>$firstVehicleClass,
            "created_by"=>$dataVehicle[0]->created_by,
            "channel"=>"",
            "payment_type"=>$dataVehicle[0]->payment_type,
            "amount_payment"=>$dataVehicle[0]->amount_payment,
            "amount_invoice"=>$dataVehicle[0]->amount_invoice,
            "getNamePayment"=>""
        );          

        return $myData;
    }
	public function getRecieptVehicle($booking_code){
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

}
