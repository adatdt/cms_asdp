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
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$dateTo = trim($this->input->post('dateTo'));
		$service_id = $this->enc->decode($this->input->post('service'));
		$shift_id = $this->enc->decode($this->input->post('shift'));
		$channel = $this->enc->decode($this->input->post('channel'));
		$merchant = $this->enc->decode($this->input->post('merchant'));
		$outletId = $this->enc->decode($this->input->post('outletId'));
		$sofId = $this->input->post('sofId');
		$dateFrom = trim($this->input->post('dateFrom'));
		$due_date = trim($this->input->post('due_date'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

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
			10 => 'merchant_name',
			11 => 'outlet_id',
			12 => 'shift_name',
			13 => 'origin',
			14 => 'destination',
			15 => 'depart_date',
			16 => 'depart_time_start',
			17 => 'transaction_type',
			18 => 'trans_code',
			19 => 'card_no',
			20 => 'sof_id',
			// 18 => 'payment_source',
			21 => 'discount_code',
			22 => 'description',
			23 => 'ref_no'
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status<>'-6'  and a.payment_date >= '". $dateFrom . "' and a.payment_date < '" . $dateToNew . "'";
		// $where = " WHERE a.status <>'-6' and ( date(a.payment_date) between '" . $dateFrom . "' and '" . $dateTo . "')";

		if (!empty($due_date)) {
			$where .= " and (date(b.invoice_date) between '" . $due_date . "' and '" . $due_date . "') ";
		}

		if (!empty($service_id)) {
			$where .= " and (c.service_id=" . $service_id . " ) ";
		}

		if (!empty($shift_id)) {
			$where .= " and (e.shift_id=" . $shift_id . " ) ";
		}

		if (!empty($sofId)) {
			$where .= " and (a.sof_id='" . $sofId . "' ) ";
		}		

		if (!empty($port)) {
			$where .= " and (c.origin=" . $port . " ) ";
		}

		if (!empty($channel)) {
			$where .= " and (a.channel='" . $channel . "')";
		}

		if (!empty($merchant)) {
			$where .= " and (UPPER(a.created_by) = UPPER('" . $merchant . "'))";
		}
		
		if (!empty($outletId)) {
			$where .= " and b.outlet_id ='" . $outletId . "' ";
		}


		if(!empty($searchData))
		{
			if($searchName=="transNumber")
			{
				$where .= "and (a.trans_number ilike '%" . $iLike . "%')";

			}
			else if($searchName=="passName")
			{
				$where .= "and (b.customer_name ilike '%" . $iLike . "%')";
			}
			else if($searchName=="paymentType")
			{
				$where .= "and (a.payment_type ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="discountCode")
			{
				$where .= "and (b.discount_code ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="transCode")
			{
				$where .= "and (l.trans_code ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="cardNo")
			{
				$where .= "and (l.card_no ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="sofId")
			{
				$where .= "and (a.sof_id ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="paymentSource")
			{
				$where .= "and (a.payment_source ilike '%" . $iLike . "%' )";
			}															
			else
			{
				$where .= "and (ref_no ilike '%" . $iLike . "%')";
			}
		}		

		$sql =$this->qry($where);


		$sql_sum = "
			select 
sum(total_amount) as total_amount ,
		sum(total_record) as total_record 
		from (
	SELECT 
		sum(a.amount) as total_amount,
		count(a.id) as total_record 
		FROM
		app.t_trx_payment a
		JOIN app.t_trx_invoice b ON b.trans_number = a.trans_number and b.transaction_type not in (2)
		LEFT JOIN app.t_trx_booking c ON c.trans_number = a.trans_number
		LEFT JOIN app.t_trx_sell d ON d.trans_number = a.trans_number
		LEFT JOIN app.t_trx_opening_balance e ON e.ob_code = d.ob_code
		left join app.t_trx_prepaid l on a.trans_number=l.trans_number
		 {$where}
	union all 
	SELECT 
		sum(a.amount) as total_amount,
		count(a.id) as total_record
		FROM
		app.t_trx_payment a
		JOIN app.t_trx_invoice b ON b.trans_number = a.trans_number and b.transaction_type  in (2)
		JOIN app.t_trx_under_paid tup ON tup.trans_number = b.trans_number
		LEFT JOIN app.t_trx_booking c ON 
		c.booking_code = tup.booking_code
		LEFT JOIN app.t_trx_sell d ON d.trans_number = a.trans_number
		LEFT JOIN app.t_trx_opening_balance e ON e.ob_code = d.ob_code
		left join app.t_trx_prepaid l on a.trans_number=l.trans_number
		 {$where}
) as total_amount
			";		

		$getSumData=$this->dbView->query($sql_sum)->row();

			

		// $query         = $this->dbView->query($sql);
		// $records_total = $this->dbView->query($sql)->num_rows();

		// $records_total = $getSumData->countdata;
		$sumDataGetSumData[]=$getSumData->total_amount;
		// $sumDataGetSumData[]=0;
		// foreach ($getSumData as $key => $value) {
		// 	$sumDataGetSumData[]=$value->total_amount;
		// }
		$records_total = $getSumData->total_record;

		

		$sql 		  .= " ORDER BY " . $order_column . " {$order_dir}";
		
		if ($length != -1) {
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}
		
		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		$dataAmountPage[] = 0;

		$shift = $this->db->query("select * from app.t_mtr_shift;")->result_array();
        $shift_column = array_column($shift,"id");
		$port = $this->db->query("select * from app.t_mtr_port;")->result_array();
        $port_column = array_column($port,"id");
		$service = $this->db->query("select * from app.t_mtr_service;")->result_array();
        $service_column = array_column($service,"id");
		$outlet_id_key = "'" . implode("', '", array_values(array_unique(array_column(json_decode(json_encode($rows_data), true),"outlet_id")))) . "'"; 
		
		if($outlet_id_key != ""){
			$outlet = $this->db->query("select a.outlet_id , a.merchant_id, b.merchant_name from app.t_mtr_outlet_merchant a
									JOIN app.t_mtr_merchant b ON a.merchant_id = b.merchant_id 
									where a.outlet_id in ($outlet_id_key) 
									;")->result_array();
		}
		else{
			$outlet = [];
		}
        
        $outlet_column = array_column($outlet,"outlet_id");

		$transaction_type = $this->db->query("select * from app.t_mtr_transaction_type;")->result_array();
        $transaction_type_column = array_column($transaction_type,"id");

		$discount = $this->db->query("select * from app.t_mtr_discount;")->result_array();
        $discount_column = array_column($discount,"discount_code");

		foreach ($rows_data as $row) {
			$row->number = $i;
			$dataAmountPage[] = $row->amount;
			$row->payment_date = format_dateTime($row->payment_date);
			$row->created_on = format_dateTime($row->created_on);
			$row->invoice_date = format_dateTime($row->invoice_date);
			$row->due_date 	   = format_dateTime($row->due_date);
			$row->amount 	   = idr_currency($row->amount);
			$row->depart_time  = format_time($row->depart_time);
			$row->depart_date = format_date($row->depart_date);


			$key_shift = array_search($row->shift_id, $shift_column);  
			$row->shift_name = (is_numeric($key_shift)) ?  $shift[$key_shift]["shift_name"]:null;

			$key_service = array_search($row->service_id, $service_column);  
			$row->service_name = (is_numeric($key_service)) ?  $service[$key_service]["name"]:null;

			$key_port = array_search($row->origin_id, $port_column);  
			$row->origin = (is_numeric($key_port)) ?  $port[$key_port]["name"]:null;

			$key_port2 = array_search($row->destination_id, $port_column);  
			$row->destination = (is_numeric($key_port2)) ?  $port[$key_port2]["name"]:null;

			$key_outlet = array_search($row->outlet_id, $outlet_column);  
			$row->merchant_name = (is_numeric($key_outlet)) ?  $outlet[$key_outlet]["merchant_name"]:null;


			$key_transaction_type = array_search($row->transaction_type, $transaction_type_column);  
			$row->tipe_transaksi = (is_numeric($key_transaction_type)) ?  $transaction_type[$key_transaction_type]["name"]:null;

			$key_discount = array_search($row->discount_code, $discount_column);  
			$row->description = (is_numeric($key_discount)) ?  $discount[$key_discount]["description"]:null;

			$row->no = $i;

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			// 'sumData'		=> $getSumData->total_amount,
				'sumData'		=>	array_sum($sumDataGetSumData),
			'data'           => $rows,
			'dataAmountPage' => idr_currency(array_sum($dataAmountPage))
		);
	}

	public function select_data($table, $where = "")
	{
		return $this->dbView->query("select * from $table $where");
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
		$outletId = $this->enc->decode($this->input->get('outletId'));

		$sofId = $this->input->get('sofId');

		$shift_id = $this->enc->decode($this->input->get('shit'));
		$service_id = $this->enc->decode($this->input->get('service'));
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
		// $where = " WHERE a.status <>'-6' and ( date(a.payment_date) between '" . $dateFrom . "' and '" . $dateTo . "')";

		if (!empty($due_date)) {
			$where .= " and date(b.invoice_date) between '" . $due_date . "' and '" . $due_date . "'";
		}

		if (!empty($service_id)) {
			$where .= " and c.service_id=" . $service_id . "";
		}

		if (!empty($sofId)) {
			$where .= " and a.sof_id='" . $sofId . "'";
		}		

		if (!empty($shift_id)) {
			$where .= " and g.id=" . $shift_id . "";
		}

		if (!empty($port)) {
			$where .= " and c.origin=" . $port . "";
		}

		if (!empty($channel)) {
			$where .= " and a.channel='" . $channel . "'";
		}

		if (!empty($merchant)) {
			$where .= " and (UPPER(a.created_by) = UPPER('" . $merchant . "'))";
		}

		if (!empty($outletId)) {
			$where .= " and b.outlet_id ='" . $outletId . "' ";
		}		

		if(!empty($searchData))
		{
			if($searchName=="transNumber")
			{
				$where .= "and (a.trans_number ilike '%" . $iLike . "%')";

			}
			else if($searchName=="passName")
			{
				$where .= "and (b.customer_name ilike '%" . $iLike . "%')";
			}
			else if($searchName=="paymentType")
			{
				$where .= "and (a.payment_type ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="discountCode")
			{
				$where .= "and (b.discount_code ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="transCode")
			{
				$where .= "and (l.trans_code ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="cardNo")
			{
				$where .= "and (l.card_no ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="sofId")
			{
				$where .= "and (a.sof_id ilike '%" . $iLike . "%' )";
			}
			else if($searchName=="paymentSource")
			{
				$where .= "and (a.payment_source ilike '%" . $iLike . "%' )";
			}												
			else
			{
				$where .= "and (ref_no ilike '%" . $iLike . "%')";
			}
		}		

		$sql = $this->qry($where)." order by a.payment_date desc";
		
		$query   = $this->dbView->query($sql);
		return $query;
	}

	public function qry($where)
	{
		$data="select 
*
		from (
	SELECT 
a.id,
			l.trans_code,
			l.card_no,
			b.discount_code,
			b.invoice_date,
			a.payment_date,
			a.trans_number,
			a.payment_type,
			a.amount,
			a.channel,
			a.ref_no,
			b.customer_name,
			b.due_date,
			c.service_id,
			c.depart_date,
			a.payment_source,
			a.sof_id,
			a.created_on,
			b.outlet_id,
			c.depart_time_start as depart_time,
			c.origin as origin_id,
			c.destination as destination_id,
			e.shift_id,
			b.transaction_type
		FROM
		app.t_trx_payment a
		JOIN app.t_trx_invoice b ON b.trans_number = a.trans_number and b.transaction_type not in (2)
		LEFT JOIN app.t_trx_booking c ON c.trans_number = a.trans_number
		LEFT JOIN app.t_trx_sell d ON d.trans_number = a.trans_number
		LEFT JOIN app.t_trx_opening_balance e ON e.ob_code = d.ob_code
		left join app.t_trx_prepaid l on a.trans_number=l.trans_number
		 {$where}
	union all 
	SELECT 
			a.id,
			l.trans_code,
			l.card_no,
			b.discount_code,
			b.invoice_date,
			a.payment_date,
			a.trans_number,
			a.payment_type,
			a.amount,
			a.channel,
			a.ref_no,
			b.customer_name,
			b.due_date,
			c.service_id,
			c.depart_date,
			a.payment_source,
			a.sof_id,
			a.created_on,
			b.outlet_id,
			c.depart_time_start as depart_time,
			c.origin as origin_id,
			c.destination as destination_id,
			e.shift_id,
			b.transaction_type
		FROM
		app.t_trx_payment a
		JOIN app.t_trx_invoice b ON b.trans_number = a.trans_number  and b.transaction_type  in (2)
		JOIN app.t_trx_under_paid tup ON tup.trans_number = b.trans_number
		LEFT JOIN app.t_trx_booking c ON 
		c.booking_code = tup.booking_code
		LEFT JOIN app.t_trx_sell d ON d.trans_number = a.trans_number
		LEFT JOIN app.t_trx_opening_balance e ON e.ob_code = d.ob_code
		left join app.t_trx_prepaid l on a.trans_number=l.trans_number
		{$where}
		) as data_list 
		
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
}
