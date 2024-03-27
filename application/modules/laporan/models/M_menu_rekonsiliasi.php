<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_menu_rekonsiliasi extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'laporan/menu_rekonsiliasi';

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
	}

  public function alfa_dataList(){
			$start = $this->input->post('start');
			$length = $this->input->post('length');
			$draw = $this->input->post('draw');
			// $search = $this->input->post('search');
			$dateFrom = $this->input->post('dateFrom');
			$dateTo = $this->input->post('dateTo');
			$dateFrom2 = $this->input->post('dateFrom2');
			$dateTo2 = $this->input->post('dateTo2');
			$dateFrom3 = $this->input->post('dateFrom3');
			$dateTo3 = $this->input->post('dateTo3');
			$service = $this->enc->decode($this->input->post('service'));
			$merchant = $this->enc->decode($this->input->post('merchant'));

			$status_type = $this->enc->decode($this->input->post('status_type'));
			$channel = $this->enc->decode($this->input->post('channel'));
			$order = $this->input->post('order');
			$order_column = $order[0]['column'];
			$order_dir = strtoupper($order[0]['dir']);
			$searchName=$this->input->post('searchName');
			$searchData=trim($this->input->post('searchData'));
			$ilike= str_replace(array('"',"'"), "", $searchData);

			if($this->get_identity_app()==0)
			{
				// mengambil port berdasarkan port di user menggunakan session
						if(!empty($this->session->userdata('port_id')))
						{
								$port_origin=$this->session->userdata('port_id');
						}
						else
						{
								$port_origin = $this->enc->decode($this->input->post('port_origin'));
						}
			}
			else
			{
				$port_origin=$this->get_identity_app();
			}

			$field = array(
				0 =>'id',
				1 =>'id_trans',
				2 =>'payment_code',
				3 =>'booking_code',
				4 =>'ticket_number',
				5 =>'merchant_id',
				6 => 'waktu_trans',
				7 => 'depart_date',
				8 => 'waktu_settle',
				9 => 'asal',
				10 => 'tujuan',
				11 => 'ship_class',
				12 =>'service',
				13 =>'golongan',
				14 =>'shop_code',
				15 =>'shop_name',
				16 =>'reconn_status',
				17 =>'tarif_per_jenis',
				18 => 'admin_fee',
				19 => 'diskon',
				20 => 'transfer_asdp',
				21 => 'code_promo',
				22 => 'updated_settlement',
			);

			$order_column = $field[$order_column];

			// $where = " WHERE a.status is not null  and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";

			$where = " WHERE b.channel='b2b' and a.status = '1'  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )  ";		

			if(!empty($service))
			{
				$where .= " and (j.id = ".$service.")";
			}

			if($status_type != "")
			{
				if($status_type == "0"){
					$where .= " and k.status is null";
				}
				else{
					$where .= " and k.status = '2'";
				}

			}

			if(!empty($dateFrom3))
			{
				$where .= " and (to_char(b.depart_date,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			}

			if(!empty($dateTo3))
			{
				$where .= " and (to_char(b.depart_date,'yyyy-mm-dd')  <='".$dateTo3."' )";
			}

			if(!empty($dateFrom3) && !empty($dateTo3))
			{
				$where .= " and (to_char(b.depart_date,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			}

			if(!empty($dateFrom2))
			{
				$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			}

			if(!empty($dateTo2))
			{
				$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
			}

			if(!empty($dateFrom2) && !empty($dateTo2))
			{
				$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
			}

			if(!empty($merchant))
			{
				$where .= " and (upper(a.merchant_id)  =upper('".$merchant."') )";
			}

			// if (!empty($search['value'])){
			// 	$where .= "and (a.trans_number ilike '%".$iLike."%' 
			// 					or b.booking_code ilike '%".$iLike."%'
			// 					or a.invoice_number ilike '%".$iLike."%'
			// 					or f.name ilike '%".$iLike."%' 
			// 					or e.name ilike '%".$iLike."%' 
			// 					or a.merchant_id ilike '%".$iLike."%'
			// 				)";	
			// }

			if(!empty($searchData))
			{
				if($searchName=='bookingCode')
				{
					$where .=" and b.booking_code ilike '%{$ilike}%' ";
				}
				else if($searchName=='ticketNumber')
				{
					$where .=" and (c.ticket_number ilike '%{$ilike}%' or d.ticket_number ilike '%{$ilike}%')";
				}
				else if($searchName=='transNumber')
				{
					$where .=" and a.trans_number ilike '%{$ilike}%' ";
				}
				else
				{
					$where .=" and a.ref_no ilike '%{$ilike}%' ";
				}
			}

			$sql = $this->alfa_qry($where);

			$query         = $this->dbView->query($sql);
			$records_total = $query->num_rows();
			$sql 		  .= " ORDER BY ".$order_column." {$order_dir}, id_trans, ticket_number";

			if($length != -1){
				$sql .=" LIMIT {$length} OFFSET {$start}";
			}

			$query     = $this->dbView->query($sql);
			$rows_data = $query->result();

			$rows 	= array();
			$i  	= ($start + 1);

			foreach ($rows_data as $row) {
				$row->number = $i;
				$row->tarif_per_jenis=idr_currency($row->tarif_per_jenis);
				if ($row->reconn_status == "paid"){
					$row->reconn_status = success_label("Paid");
				}
				else {
					$row->reconn_status = warning_label($row->reconn_status);
				}

				$nonaktif = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
					$aktif = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

				$id=$this->enc->encode($row->id);
				$edit_url 	 = site_url($this->_module."/edit/{$id}");
					$delete_url  = site_url($this->_module."/action_delete/{$id}");

					
					// $row->total_amount=idr_currency($row->total_amount);
					$row->created_on=format_dateTimeHis($row->created_on);
					$row->no=$i;

				$rows[] = $row;
				// unset($row->id);

				$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	public function brilink_dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$dateFrom2 = $this->input->post('dateFrom2');
		$dateTo2 = $this->input->post('dateTo2');
		$dateFrom3 = $this->input->post('dateFrom3');
		$dateTo3 = $this->input->post('dateTo3');
		$service = $this->enc->decode($this->input->post('service'));
		$merchant = $this->enc->decode($this->input->post('merchant'));

		$status_type = $this->enc->decode($this->input->post('status_type'));
		$channel = $this->enc->decode($this->input->post('channel'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchName=$this->input->post('searchName');
		$searchData=trim($this->input->post('searchData'));
		$ilike= str_replace(array('"',"'"), "", $searchData);

		if($this->get_identity_app()==0)
		{
			// mengambil port berdasarkan port di user menggunakan session
					if(!empty($this->session->userdata('port_id')))
					{
							$port_origin=$this->session->userdata('port_id');
					}
					else
					{
							$port_origin = $this->enc->decode($this->input->post('port_origin'));
					}
		}
		else
		{
			$port_origin=$this->get_identity_app();
		}

		$field = array(
			0 =>'id',
			1 =>'id_trans',
			2 =>'payment_code',
			3 =>'booking_code',
			4 =>'ticket_number',
			5 =>'merchant_id',
			6 => 'waktu_trans',
			7 => 'depart_date',
			8 => 'waktu_settle',
			9 => 'asal',
			10 => 'tujuan',
			11 => 'ship_class',
			12 =>'service',
			13 =>'golongan',
			14 =>'shop_code',
			15 =>'shop_name',
			16 =>'reconn_status',
			17 =>'tarif_per_jenis',
			18 => 'admin_fee',
			19 => 'diskon',
			20 => 'transfer_asdp',
			21 => 'code_promo',
			22 => 'updated_settlement',
		);

		$order_column = $field[$order_column];

		// $where = " WHERE a.status is not null  and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";

		$where = " WHERE b.channel='b2b' and a.status = '1'  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )  ";
		$where2 = " WHERE a.status = '3'  and (to_char(a.time_trx,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )  ";

		if(!empty($service))
		{
			$where .= " and (j.id = ".$service.")";
			$where2 .= " and (a.service_id = ".$service.")";
		}

		if($status_type != "")
		{
			if($status_type == "0"){
				$where .= " and (k.status is null or k.status = '0')";
				$where2 .= " and (a.status is null or a.status = '0')";
			}
			else if ($status_type == 2) {
				$where .= " and k.status = '2'";
				$where2 .= " and a.status = '2'";
			}
			else {
				$where .= " and k.status = '3'";
				$where2 .= " and a.status = '3'";
			}

		}

		if(!empty($dateFrom3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			$where2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  >='".$dateFrom3."' )";
		}

		if(!empty($dateTo3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd')  <='".$dateTo3."' )";
			$where2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  <='".$dateTo3."' )";
		}

		if(!empty($dateFrom3) && !empty($dateTo3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			$where2 .= " and (to_char(a.depart_time,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
		}

		if(!empty($dateFrom2))
		{
			$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			$where2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
		}

		if(!empty($dateTo2))
		{
			$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
			$where2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
		}

		if(!empty($dateFrom2) && !empty($dateTo2))
		{
			$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
			$where2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
		}

		if(!empty($merchant))
		{
			$where .= " and (upper(a.merchant_id)  =upper('".$merchant."') )";
		}

		if(!empty($searchData))
		{
			if($searchName=='bookingCode')
			{
				$where .=" and b.booking_code ilike '%{$ilike}%' ";
				$where2 .=" and a.booking_code ilike '%{$ilike}%' ";
			}
			else if($searchName=='ticketNumber')
			{
				$where .=" and (c.ticket_number ilike '%{$ilike}%' or d.ticket_number ilike '%{$ilike}%')";
				$where2 .=" and (b.ticket_number ilike '%{$ilike}%' or c.ticket_number ilike '%{$ilike}%')";
			}
			else if($searchName=='transNumber')
			{
				$where .=" and a.trans_number ilike '%{$ilike}%' ";
				$where2 .=" and a.trans_number ilike '%{$ilike}%' ";
			}
			else
			{
				$where .=" and a.ref_no ilike '%{$ilike}%' ";
				$where2 .=" and a.payment_code ilike '%{$ilike}%' ";
			}
		}

		$sql = $this->brilink_qry($where, $where2);

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}, id_trans, ticket_number";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);
		$previous_trans = "";
		$previous_transfer = "";
		$previous_adm_fee = "";

		foreach ($rows_data as $row) {
			$row->number = $i;

			$trans_number 	= $row->id_trans;
			$transfer_asdp 	= $row->transfer_asdp;
			$adm_fee				= $row->admin_fee;

			$row->tarif_per_jenis=idr_currency($row->tarif_per_jenis);

			if ($row->reconn_status == "Paid"){
				$row->reconn_status = success_label("Paid");
			}
			else if($row->reconn_status == "Investigasi") {
				$row->reconn_status = failed_label($row->reconn_status);
			}
			else {
				$row->reconn_status = warning_label($row->reconn_status);
			}

			if ($row->id_trans == $previous_trans && $row->transfer_asdp == $previous_transfer) {
				$row->transfer_asdp = "-";
			}

			if ($row->id_trans == $previous_trans && $row->admin_fee == $previous_adm_fee) {
				$row->admin_fee = "-";
			}

			$nonaktif = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
				$aktif = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

			$id=$this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$id}");
				$delete_url  = site_url($this->_module."/action_delete/{$id}");

				
				// $row->total_amount=idr_currency($row->total_amount);
				$row->created_on=format_dateTimeHis($row->created_on);
				// $row->updated_settlement=format_dateTimeHis($row->updated_settlement);
				$row->no=$i;

			$rows[] = $row;
			// unset($row->id);
			$previous_trans = $trans_number;
			$previous_transfer = $transfer_asdp;
			$previous_adm_fee = $adm_fee;
			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	public function alfa_total()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		// $search = $this->input->post('search');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$dateFrom2 = $this->input->post('dateFrom2');
		$dateTo2 = $this->input->post('dateTo2');
		$dateFrom3 = $this->input->post('dateFrom3');
		$dateTo3 = $this->input->post('dateTo3');
		$service = $this->enc->decode($this->input->post('service'));
		$merchant = $this->enc->decode($this->input->post('merchant'));
		$status_type = $this->enc->decode($this->input->post('status_type'));
		$channel = $this->enc->decode($this->input->post('channel'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchName=$this->input->post('searchName');
		$searchData=trim($this->input->post('searchData'));
		$ilike= str_replace(array('"',"'"), "", $searchData);

		if($this->get_identity_app()==0)
		{
			// mengambil port berdasarkan port di user menggunakan session
	        if(!empty($this->session->userdata('port_id')))
	        {
	            $port_origin=$this->session->userdata('port_id');
	        }
	        else
	        {
	            $port_origin = $this->enc->decode($this->input->post('port_origin'));
	        }
		}
		else
		{
			$port_origin=$this->get_identity_app();
		}

		$where = " WHERE b.channel='b2b' and a.status = '1'  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";
		$where2 = " WHERE b.channel='b2b' and a.status = '1' and k.status = '2'  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";
		$where3 = " WHERE b.channel='b2b' and a.status = '1' and k.status is null  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )  ";		

		if(!empty($service))
		{
			$where .= " and (j.id = ".$service.")";
			$where2 .= " and (j.id = ".$service.")";
			$where3 .= " and (j.id = ".$service.")";
		}
		
		if($status_type != "")
		{
			if($status_type == "0"){
				$where .= " and k.status is null"; //jumlah trans sama dengan jumlah belum dibayar
				$where2 .= "and a.recon_status = '1'"; //jumlah dibayar tidak ada
				// $where3 .= "and a.recon_status = '1'";
			}
			else{
				$where .= " and k.status = '2'"; //jumlah trans sama dengan jumlah dibayar
				$where3 .= "and a.recon_status = '1'"; //jumlah belum dibayar tidak ada
			}
		}

		if(!empty($dateFrom2))
		{
			$where .= " and k.status = '2' and (to_char(k.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			$where2 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			$where3 .= "  and a.recon_status = '1' and (to_char(k.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
		}

		if(!empty($dateTo2))
		{
			$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' ) and k.status = '2'";
			$where2 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
			$where3 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' ) and a.recon_status = '1'";
		}

		if(!empty($dateFrom2) && !empty($dateTo2))
		{
			$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' ) and k.status = '2'";
			$where2 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' ) ";
			$where3 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' ) and a.recon_status = '1'";
		}

		if(!empty($dateFrom3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			$where2 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			$where3 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  >='".$dateFrom3."' )";
		}

		if(!empty($dateTo3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd')  <='".$dateTo3."' )";
			$where2 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  <='".$dateTo3."' )";
			$where3 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  <='".$dateTo3."' )";
		}

		if(!empty($dateFrom3) && !empty($dateTo3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			$where2 .= " and (to_char(b.depart_date,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			$where3 .= " and (to_char(b.depart_date,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
		}

		if(!empty($merchant))
		{
			$where .= " and (upper(a.merchant_id)  =upper('".$merchant."') )";
			$where2 .= " and (upper(a.merchant_id)  =upper('".$merchant."') )";
			$where3 .= " and (upper(a.merchant_id)  =upper('".$merchant."') )";
		}

		// if (!empty($search)){
		// 	$where .= "and (a.trans_number ilike '%".$iLike."%' 
		// 					or b.booking_code ilike '%".$iLike."%'
		// 					or a.invoice_number ilike '%".$iLike."%'
		// 					or f.name ilike '%".$iLike."%' 
		// 					or e.name ilike '%".$iLike."%' 
		// 					or a.merchant_id ilike '%".$iLike."%'
		// 				)";
		// 	$where2 .= "and (a.trans_number ilike '%".$iLike."%' 
		// 					or b.booking_code ilike '%".$iLike."%'
		// 					or a.invoice_number ilike '%".$iLike."%'
		// 					or f.name ilike '%".$iLike."%' 
		// 					or e.name ilike '%".$iLike."%' 
		// 					or a.merchant_id ilike '%".$iLike."%'
		// 				)";	
		// 	$where3 .= "and (a.trans_number ilike '%".$iLike."%' 
		// 					or b.booking_code ilike '%".$iLike."%'
		// 					or a.invoice_number ilike '%".$iLike."%'
		// 					or f.name ilike '%".$iLike."%' 
		// 					or e.name ilike '%".$iLike."%' 
		// 					or a.merchant_id ilike '%".$iLike."%'
		// 				)";		
		// }

		if(!empty($searchData))
		{
			if($searchName=='bookingCode')
			{
				$where .=" and b.booking_code ilike '%{$ilike}%' ";
				$where2 .=" and b.booking_code ilike '%{$ilike}%' ";
				$where3 .=" and b.booking_code ilike '%{$ilike}%' ";
			}
			else if($searchName=='ticketNumber')
			{
				$where .=" and (c.ticket_number ilike '%{$ilike}%' or d.ticket_number ilike '%{$ilike}%')";
				$where2 .=" and (c.ticket_number ilike '%{$ilike}%' or d.ticket_number ilike '%{$ilike}%')";
				$where3 .=" and (c.ticket_number ilike '%{$ilike}%' or d.ticket_number ilike '%{$ilike}%')";
			}
			else if($searchName=='transNumber')
			{
				$where .=" and a.trans_number ilike '%{$ilike}%' ";
				$where2 .=" and a.trans_number ilike '%{$ilike}%' ";
				$where3 .=" and a.trans_number ilike '%{$ilike}%' ";
			}
			else
			{
				$where .=" and a.ref_no ilike '%{$ilike}%' ";
				$where2 .=" and a.ref_no ilike '%{$ilike}%' ";
				$where3 .=" and a.ref_no ilike '%{$ilike}%' ";
			}
		}

		$sql = "SELECT
							count(distinct id_trans) as jumlah_transaksi,
							sum(tarif_per_jenis) as total_transaksi
						from(
							". $this->alfa_qry($where) ."
								) as rekon";
					
		$sql2 = "SELECT
							count(distinct id_trans) as jumlah_dibayar,
							sum(tarif_per_jenis) as total_dibayar
						from(
							". $this->alfa_qry($where2) ."
								) as rekon";

		$sql3 = "SELECT
							count(distinct id_trans) as jumlah_belum,
							sum(tarif_per_jenis) as total_belum
						from(
							". $this->alfa_qry($where3) ."
								) as rekon";


		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();
		$query2     = $this->dbView->query($sql2);
		$rows_data2 = $query2->result();
		$query3     = $this->dbView->query($sql3);
		$rows_data3 = $query3->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
     		$row->total_transaksi=idr_currency($row->total_transaksi);
				$rows1[] = $row;

			$i++;
		}
		foreach ($rows_data2 as $row) {
			$row->total_dibayar=idr_currency($row->total_dibayar);
			$rows2[] = $row;
		}
		foreach ($rows_data3 as $row) {
			$row->total_belum=idr_currency($row->total_belum);
			$rows3[] = $row;
		}
		$rows4[] = array(
				"jumlah_inves"	=> "0",
				"total_inves"		=> idr_currency(0)
		);
		$rows[] = array_merge($rows1,$rows2,$rows3, $rows4);
		return array(
			'data'           => $rows
		);
	}

	public function brilink_total()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		// $search = $this->input->post('search');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$dateFrom2 = $this->input->post('dateFrom2');
		$dateTo2 = $this->input->post('dateTo2');
		$dateFrom3 = $this->input->post('dateFrom3');
		$dateTo3 = $this->input->post('dateTo3');
		$service = $this->enc->decode($this->input->post('service'));
		$merchant = $this->enc->decode($this->input->post('merchant'));
		$status_type = $this->enc->decode($this->input->post('status_type'));
		$channel = $this->enc->decode($this->input->post('channel'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchName=$this->input->post('searchName');
		$searchData=trim($this->input->post('searchData'));
		$ilike= str_replace(array('"',"'"), "", $searchData);

		if($this->get_identity_app()==0)
		{
			// mengambil port berdasarkan port di user menggunakan session
	        if(!empty($this->session->userdata('port_id')))
	        {
	            $port_origin=$this->session->userdata('port_id');
	        }
	        else
	        {
	            $port_origin = $this->enc->decode($this->input->post('port_origin'));
	        }
		}
		else
		{
			$port_origin=$this->get_identity_app();
		}

		$wheretot1 = " WHERE b.channel='b2b' and a.status = '1'  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";
		$wheretot2 = " WHERE a.status = '3'  and (to_char(a.time_trx,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )  ";
		$wherepaid1 = " WHERE b.channel='b2b' and a.status = '1' and k.status = '2'  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";
		$wherepaid2 = " WHERE a.status = '3' and a.status = '2'  and (to_char(a.time_trx,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";
		$whereunpaid1 = " WHERE b.channel='b2b' and a.status = '1' and (k.status is null or k.status = '0')  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )  ";
		$whereunpaid2 = " WHERE a.status = '3' and (a.status is null or a.status = '0')  and (to_char(a.time_trx,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )  ";
		$whereinves1 = " WHERE b.channel='b2b' and a.status = '1' and k.status = '3'  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";
		$whereinves2 = " WHERE a.status = '3'  and (to_char(a.time_trx,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";

		if(!empty($service))
		{
			$wheretot1 .= " and (j.id = ".$service.")";
			$wheretot2 .= " and (a.service_id = ".$service.")";
			$wherepaid1 .= " and (j.id = ".$service.")";
			$wherepaid2 .= " and (a.service_id = ".$service.")";
			$whereunpaid1 .= " and (j.id = ".$service.")";
			$whereunpaid2 .= " and (a.service_id = ".$service.")";
			$whereinves1 .= " and (j.id = ".$service.")";
			$whereinves2 .= " and (a.service_id = ".$service.")";
		}

		if($status_type != "")
		{
			if($status_type == "0"){
				$wheretot1 .= " and (k.status is null or k.status = '0')"; //jumlah trans sama dengan jumlah belum dibayar
				$wheretot2 .= " and (a.status is null or a.status = '0')"; //jumlah trans sama dengan jumlah belum dibayar
				$wherepaid1 .= "and (k.status is null or k.status = '0')"; //jumlah dibayar tidak ada
				$wherepaid2 .= "and (a.status is null or a.status = '0')"; //jumlah dibayar tidak ada
				$whereinves1 .= "and (k.status is null or k.status = '0')"; //jumlah dibayar tidak ada
				$whereinves2 .= "and (a.status is null or a.status = '0')"; //jumlah dibayar tidak ada
				// $where3 .= "and a.recon_status = '1'";
			}
			else if ($status_type == 2){
				$wheretot1 .= " and k.status = '2'"; //jumlah trans sama dengan jumlah dibayar
				$wheretot2 .= " and a.status = '2'"; //jumlah trans sama dengan jumlah dibayar
				$whereunpaid1 .= "and a.recon_status = '1'"; //jumlah belum dibayar tidak ada
				$whereunpaid2 .= " and a.status = '1'"; //jumlah belum dibayar tidak ada
				$whereinves1 .= "and k.status is null"; //jumlah dibayar tidak ada
				$whereinves2 .= "and a.status is null"; //jumlah dibayar tidak ada
			}
			else {
				$wheretot1 .= " and k.status = '3'"; //jumlah trans sama dengan jumlah dibayar
				$wheretot2 .= " and a.status = '3'"; //jumlah trans sama dengan jumlah dibayar
				$whereunpaid1 .= "and k.status = '3'"; //jumlah belum dibayar tidak ada
				$whereunpaid2 .= " and a.status = '3'"; //jumlah belum dibayar tidak ada
				$wherepaid1 .= "and k.status = 3"; //jumlah dibayar tidak ada
				$wherepaid2 .= "and a.status = 3"; //jumlah dibayar tidak ada
			}
		}

		if(!empty($dateFrom2))
		{
			$wheretot1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			$wheretot2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			$wherepaid1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			$wherepaid2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			$whereunpaid1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			$whereunpaid2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			$whereinves1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			$whereinves2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
		}

		if(!empty($dateTo2))
		{
			$wheretot1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
			$wheretot2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
			$wherepaid1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
			$wherepaid2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
			$whereunpaid1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
			$whereunpaid2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
			$whereinves1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
			$whereinves2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
		}

		if(!empty($dateFrom2) && !empty($dateTo2))
		{
			$wheretot1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
			$wheretot2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
			$wherepaid1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' ) ";
			$wherepaid2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' ) ";
			$whereunpaid1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
			$whereunpaid2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
			$whereinves1 .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
			$whereinves2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
		}

		if(!empty($dateFrom3))
		{
			$wheretot1 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			$wheretot2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			$wherepaid1 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			$wherepaid2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			$whereunpaid1 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			$whereunpaid2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			$whereinves1 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			$whereinves2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  >='".$dateFrom3."' )";
		}

		if(!empty($dateTo3))
		{
			$wheretot1 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  <='".$dateTo3."' )";
			$wheretot2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  <='".$dateTo3."' )";
			$wherepaid1 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  <='".$dateTo3."' )";
			$wherepaid2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  <='".$dateTo3."' )";
			$whereunpaid1 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  <='".$dateTo3."' )";
			$whereunpaid2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  <='".$dateTo3."' )";
			$whereinves1 .= " and (to_char(b.depart_date,'yyyy-mm-dd')  <='".$dateTo3."' )";
			$whereinves2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  <='".$dateTo3."' )";
		}

		if(!empty($dateFrom3) && !empty($dateTo3))
		{
			$wheretot1 .= " and (to_char(b.depart_date,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			$wheretot2 .= " and (to_char(a.depart_time,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			$wherepaid1 .= " and (to_char(b.depart_date,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			$wherepaid2 .= " and (to_char(a.depart_time,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			$whereunpaid1 .= " and (to_char(b.depart_date,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			$whereunpaid2 .= " and (to_char(a.depart_time,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			$whereinves1 .= " and (to_char(b.depart_date,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			$whereinves2 .= " and (to_char(a.depart_time,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
		}

		if(!empty($merchant))
		{
			$wheretot1 .= " and (upper(a.merchant_id)  =upper('".$merchant."') )";
			$wherepaid1 .= " and (upper(a.merchant_id)  =upper('".$merchant."') )";
			$whereunpaid1 .= " and (upper(a.merchant_id)  =upper('".$merchant."') )";
			$whereinves1 .= " and (upper(a.merchant_id)  =upper('".$merchant."') )";
		}

		if(!empty($searchData))
		{
			if($searchName=='bookingCode')
			{
				$wheretot1 .=" and b.booking_code ilike '%{$ilike}%' ";
				$wheretot2 .=" and a.booking_code ilike '%{$ilike}%' ";
				$wherepaid1 .=" and b.booking_code ilike '%{$ilike}%' ";
				$wherepaid2 .=" and a.booking_code ilike '%{$ilike}%' ";
				$whereunpaid1 .=" and b.booking_code ilike '%{$ilike}%' ";
				$whereunpaid2 .=" and a.booking_code ilike '%{$ilike}%' ";
				$whereinves1 .=" and b.booking_code ilike '%{$ilike}%' ";
				$whereinves2 .=" and a.booking_code ilike '%{$ilike}%' ";
			}
			else if($searchName=='ticketNumber')
			{
				$wheretot1 .=" and (c.ticket_number ilike '%{$ilike}%' or d.ticket_number ilike '%{$ilike}%')";
				$wheretot2 .=" and (b.ticket_number ilike '%{$ilike}%' or c.ticket_number ilike '%{$ilike}%')";
				$wherepaid1 .=" and (c.ticket_number ilike '%{$ilike}%' or d.ticket_number ilike '%{$ilike}%')";
				$wherepaid2 .=" and (c.ticket_number ilike '%{$ilike}%' or b.ticket_number ilike '%{$ilike}%')";
				$whereunpaid1 .=" and (c.ticket_number ilike '%{$ilike}%' or d.ticket_number ilike '%{$ilike}%')";
				$whereunpaid2 .=" and (c.ticket_number ilike '%{$ilike}%' or b.ticket_number ilike '%{$ilike}%')";
				$whereinves1 .=" and (c.ticket_number ilike '%{$ilike}%' or d.ticket_number ilike '%{$ilike}%')";
				$whereinves2 .=" and (c.ticket_number ilike '%{$ilike}%' or b.ticket_number ilike '%{$ilike}%')";
			}
			else if($searchName=='transNumber')
			{
				$wheretot1 .=" and a.trans_number ilike '%{$ilike}%' ";
				$wheretot2 .=" and a.trans_number ilike '%{$ilike}%' ";
				$wherepaid1 .=" and a.trans_number ilike '%{$ilike}%' ";
				$wherepaid2 .=" and a.trans_number ilike '%{$ilike}%' ";
				$whereunpaid1 .=" and a.trans_number ilike '%{$ilike}%' ";
				$whereunpaid2 .=" and a.trans_number ilike '%{$ilike}%' ";
				$whereinves1 .=" and a.trans_number ilike '%{$ilike}%' ";
				$whereinves2 .=" and a.trans_number ilike '%{$ilike}%' ";
			}
			else
			{
				$wheretot1 .=" and a.ref_no ilike '%{$ilike}%' ";
				$wheretot2 .=" and a.payment_code ilike '%{$ilike}%' ";
				$wherepaid1 .=" and a.ref_no ilike '%{$ilike}%' ";
				$wherepaid2 .=" and a.payment_code ilike '%{$ilike}%' ";
				$whereunpaid1 .=" and a.ref_no ilike '%{$ilike}%' ";
				$whereunpaid2 .=" and a.payment_code ilike '%{$ilike}%' ";
				$whereinves1 .=" and a.ref_no ilike '%{$ilike}%' ";
				$whereinves2 .=" and a.payment_code ilike '%{$ilike}%' ";
			}
		}

		$sql = "SELECT
							count(distinct id_trans) as jumlah_transaksi,
							sum(tarif_per_jenis) as total_transaksi
						from(
							". $this->brilink_qry($wheretot1, $wheretot2) ."
								) as rekon";
					
		$sql2 = "SELECT
							count(distinct id_trans) as jumlah_dibayar,
							sum(tarif_per_jenis) as total_dibayar
						from(
							". $this->brilink_qry($wherepaid1, $wherepaid2) ."
								) as rekon";

		$sql3 = "SELECT
							count(distinct id_trans) as jumlah_belum,
							sum(tarif_per_jenis) as total_belum
						from(
							". $this->brilink_qry($whereunpaid1, $whereunpaid2) ."
								) as rekon";

		$sql4 = "SELECT
							count(distinct id_trans) as jumlah_inves,
							sum(tarif_per_jenis) as total_inves
						from(
							". $this->brilink_qry($whereinves1, $whereinves2) ."
								) as rekon";


		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();
		$query2     = $this->dbView->query($sql2);
		$rows_data2 = $query2->result();
		$query3     = $this->dbView->query($sql3);
		$rows_data3 = $query3->result();
		$query4     = $this->dbView->query($sql4);
		$rows_data4 = $query4->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
     		$row->total_transaksi=idr_currency($row->total_transaksi);
     		// $row->total_transaksi=idr_currency('10000000');
				$rows1[] = $row;
		}
		foreach ($rows_data2 as $row) {
			$row->total_dibayar=idr_currency($row->total_dibayar);
			// $row->total_dibayar=idr_currency('10000000');
			$rows2[] = $row;
		}
		foreach ($rows_data3 as $row) {
			$row->total_belum=idr_currency($row->total_belum);
			// $row->total_belum=idr_currency('10000000000');
			$rows3[] = $row;
		}
		foreach ($rows_data4 as $row) {
			$row->total_inves=idr_currency($row->total_inves);
			// $row->total_inves=idr_currency('10000000');
			$rows4[] = $row;
		}
		$rows[] = array_merge($rows1,$rows2,$rows3,$rows4);
		return array(
			'data'           => $rows
		);
	}

	public function alfa_download(){
		$dateFrom = $this->input->get("dateFrom");
		$dateTo = $this->input->get("dateTo");
		$dateFrom2 = $this->input->get("dateFrom2");
		$dateTo2 = $this->input->get("dateTo2");
		$dateFrom3 = $this->input->get("dateFrom3");
		$dateTo3 = $this->input->get("dateTo3");
		$service = $this->enc->decode($this->input->get("service"));
		$merchant = $this->enc->decode($this->input->get("merchant"));
		$status_type = $this->enc->decode($this->input->get('status_type'));

		$searchName=$this->input->get('searchName');
		$searchData=trim($this->input->get('searchData'));
		$ilike= str_replace(array('"',"'"), "", $searchData);

        // mengambil port berdasarkan port di user menggunakan session
		if($this->get_identity_app()==0)
		{
			// mengambil port berdasarkan port di user menggunakan session
	        if(!empty($this->session->userdata('port_id')))
	        {
	            $port_origin=$this->session->userdata('port_id');
	        }
	        else
	        {
	            $port_origin = $this->enc->decode($this->input->get('port_origin'));
	        }
		}
		else
		{
			$port_origin=$this->get_identity_app();
		}

		// $where = " WHERE a.status is not null  and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";

		// $where = " WHERE b.channel='b2b'  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";		

		$where = " WHERE b.channel='b2b' and a.status = '1'  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )  ";		

		if(!empty($service))
		{
			$where .= " and (j.id = ".$service.")";
		}
		
		if($status_type != "")
		{
			if($status_type == "0"){
				$where .= " and k.status is null";
			}
			else{
				$where .= " and k.status = '2'";
			}
			
		}

		if(!empty($dateFrom3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd')  >='".$dateFrom3."' )";
		}

		if(!empty($dateTo3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd')  <='".$dateTo3."' )";
		}

		if(!empty($dateFrom3) && !empty($dateTo3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
		}

		if(!empty($dateFrom2))
		{
			$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
		}

		if(!empty($dateTo2))
		{
			$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
		}

		if(!empty($dateFrom2) && !empty($dateTo2))
		{
			$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
		}

		if(!empty($merchant))
		{
			$where .= " and (upper(a.merchant_id)  =upper('".$merchant."') )";
		}

		if(!empty($searchData))
		{
			if($searchName=='bookingCode')
			{
				$where .=" and b.booking_code ilike '%{$ilike}%' ";
			}
			else if($searchName=='ticketNumber')
			{
				$where .=" and (c.ticket_number ilike '%{$ilike}%' or d.ticket_number ilike '%{$ilike}%'";
			}
			else if($searchName=='transNumber')
			{
				$where .=" and a.trans_number ilike '%{$ilike}%' ";
			}
			else
			{
				$where .=" and a.ref_no ilike '%{$ilike}%' ";
			}
		}



		$sql = $this->alfa_qry($where) . " ORDER BY waktu_trans DESC";

		$query     = $this->dbView->query($sql);
		// die($sql);
		return $query;
	}

	public function brilink_download(){
		$dateFrom = $this->input->get("dateFrom");
		$dateTo = $this->input->get("dateTo");
		$dateFrom2 = $this->input->get("dateFrom2");
		$dateTo2 = $this->input->get("dateTo2");
		$dateFrom3 = $this->input->get("dateFrom3");
		$dateTo3 = $this->input->get("dateTo3");
		$service = $this->enc->decode($this->input->get("service"));
		$merchant = $this->enc->decode($this->input->get("merchant"));
		$status_type = $this->enc->decode($this->input->get('status_type'));

		$searchName=$this->input->get('searchName');
		$searchData=trim($this->input->get('searchData'));
		$ilike= str_replace(array('"',"'"), "", $searchData);

				// mengambil port berdasarkan port di user menggunakan session
		if($this->get_identity_app()==0)
		{
			// mengambil port berdasarkan port di user menggunakan session
					if(!empty($this->session->userdata('port_id')))
					{
							$port_origin=$this->session->userdata('port_id');
					}
					else
					{
							$port_origin = $this->enc->decode($this->input->get('port_origin'));
					}
		}
		else
		{
			$port_origin=$this->get_identity_app();
		}

		// $where = " WHERE a.status is not null  and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";

		// $where = " WHERE b.channel='b2b'  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";

		$where = " WHERE b.channel='b2b' and a.status = '1'  and (to_char(b.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )  ";
		$where2 = " WHERE a.status = '3'  and (to_char(a.time_trx,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )  ";

		if(!empty($service))
		{
			$where .= " and (j.id = ".$service.")";
			$where2 .= " and (a.service_id = ".$service.")";
		}

		if($status_type != "")
		{
			if($status_type == "0"){
				$where .= " and (k.status is null or k.status = '0')";
				$where2 .= " and (a.status is null or a.status = '0')";
			}
			else if ($status_type == 2) {
				$where .= " and k.status = '2'";
				$where2 .= " and a.status = '2'";
			}
			else {
				$where .= " and k.status = '3'";
				$where2 .= " and a.status = '3'";
			}

		}

		if(!empty($dateFrom3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd')  >='".$dateFrom3."' )";
			$where2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  >='".$dateFrom3."' )";
		}

		if(!empty($dateTo3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd')  <='".$dateTo3."' )";
			$where2 .= " and (to_char(a.depart_time,'yyyy-mm-dd')  <='".$dateTo3."' )";
		}

		if(!empty($dateFrom3) && !empty($dateTo3))
		{
			$where .= " and (to_char(b.depart_date,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
			$where2 .= " and (to_char(a.depart_time,'yyyy-mm-dd') between '".$dateFrom3."' and '".$dateTo3."' )";
		}

		if(!empty($dateFrom2))
		{
			$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
			$where2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  >='".$dateFrom2."' )";
		}

		if(!empty($dateTo2))
		{
			$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
			$where2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  <='".$dateTo2."' )";
		}

		if(!empty($dateFrom2) && !empty($dateTo2))
		{
			$where .= " and (to_char(k.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
			$where2 .= " and (to_char(a.settlement_date,'yyyy-mm-dd')  between '".$dateFrom2."' and '".$dateTo2."' )";
		}

		if(!empty($merchant))
		{
			$where .= " and (upper(a.merchant_id)  =upper('".$merchant."') )";
		}

		if(!empty($searchData))
		{
			if($searchName=='bookingCode')
			{
				$where .=" and b.booking_code ilike '%{$ilike}%' ";
				$where2 .=" and a.booking_code ilike '%{$ilike}%' ";
			}
			else if($searchName=='ticketNumber')
			{
				$where .=" and (c.ticket_number ilike '%{$ilike}%' or d.ticket_number ilike '%{$ilike}%')";
				$where2 .=" and (c.ticket_number ilike '%{$ilike}%' or b.ticket_number ilike '%{$ilike}%')";
			}
			else if($searchName=='transNumber')
			{
				$where .=" and a.trans_number ilike '%{$ilike}%' ";
				$where2 .=" and a.trans_number ilike '%{$ilike}%' ";
			}
			else
			{
				$where .=" and a.ref_no ilike '%{$ilike}%' ";
				$where2 .=" and a.payment_code ilike '%{$ilike}%' ";
			}
		}

		$sql = $this->brilink_qry($where, $where2) . " ORDER BY waktu_trans DESC";

		$query     = $this->dbView->query($sql);
		// die($sql);
		return $query;
	}

	public function select_data($table, $where="")
	{
		return $this->dbView->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->dbView->insert($table, $data);
	}

	public function update_data($table,$data,$where)
	{
		$this->dbView->where($where);
		$this->dbView->update($table, $data);
	}

	public function delete_data($table,$data,$where)
	{
		$this->dbView->where($where);
		$this->dbView->delete($table, $data);
	}

	public function get_identity_app()
	{
		$data=$this->dbView->query(" select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}

	function get_channel(){
		$data  = array(''=>'Pilih');
		$query = $this->dbView->query(" SELECT DISTINCT channel FROM app.t_trx_invoice where channel<>'' ORDER BY channel asc ")->result();

		foreach ($query as $key => $value) {
		 	$data[$this->enc->encode($value->channel)] = strtoupper(str_replace('_', ' ', $value->channel));
		 } 

		return array_unique($data);
	}

	public function alfa_qry($where)
	{
		$data="SELECT a.trans_number as id_trans, a.ref_no as payment_code, b.booking_code, 
							(
								case
								when b.service_id = 1
								then c.ticket_number
								else d.ticket_number end 
							) as ticket_number, 
							a.merchant_id, b.created_on as waktu_trans, b.depart_date, k.settlement_date as waktu_settle,
							e.name as asal, f.name as tujuan, g.name as ship_class, j.name as service,
							k.shop_code, k.shop_name, 
							(
								case 
								when k.status_name is null
								then p.description
								else k.status_name end
							) as reconn_status,
							(
								case
								when b.service_id = 1
								then h.name
								else i.name end
							) as golongan,
							(
								case
								when b.service_id = 1
								then m.fare
								else n.fare end
							) as tarif_per_jenis,
							k.status_name,
							k.admin_fee, k.diskon, k.transfer_asdp, k.code_promo, k.created_on as updated_settlement, a.*
						from app.t_trx_payment_b2b a
						left join app.t_trx_booking b on a.trans_number = b.trans_number
						left join app.t_trx_booking_passanger c on b.booking_code = c.booking_code and c.service_id = 1
						left join app.t_trx_booking_vehicle d on b.booking_code = d.booking_code
						left join app.t_mtr_port e on b.origin = e.id
						left join app.t_mtr_port f on b.destination = f.id
						left join app.t_mtr_ship_class g on g.id = b.ship_class
						left join app.t_mtr_passanger_type h on h.id = c.passanger_type_id 
						left join app.t_mtr_vehicle_class i on i.id = d.vehicle_class_id
						left join app.t_mtr_service j on j.id = b.service_id
						full outer join app.t_trx_recon_alfamart k on k.trans_number = a.trans_number
						left join app.t_mtr_rute l on l.origin = e.id and l.destination = f.id
						left join app.t_mtr_fare_passanger m on m.rute_id = l.id and m.passanger_type = c.passanger_type_id and c.service_id = 1 and m.ship_class = b.ship_class and m.status <> -5
						left join app.t_mtr_fare_vehicle n on n.rute_id = l.id and n.vehicle_class_id = d.vehicle_class_id and n.ship_class = b.ship_class and n.status <> -5
						left join app.t_mtr_merchant o on a.merchant_id = o.merchant_id
						left join app.t_mtr_status p on a.recon_status = p.status and tbl_name = 't_trx_payment_b2b'
						$where";

		return $data;
	}

	public function brilink_qry($where, $where2)
	{
		$data="SELECT a.id, a.created_on,
							a.trans_number as id_trans, a.ref_no as payment_code, b.booking_code, 
							(
								case
								when b.service_id = 1
								then c.ticket_number
								else d.ticket_number end 
							) as ticket_number, 
							a.merchant_id, b.created_on as waktu_trans, concat(b.depart_date, ' ', b.depart_time_start) as depart_date, k.settlement_date as waktu_settle,
							e.name as asal, f.name as tujuan, g.name as ship_class, j.name as service,
							k.shop_code, k.shop_name, 
							(
								case 
								when k.status_name is null
								then p.description
								else k.status_name end
							) as reconn_status,
							(
								case
								when b.service_id = 1
								then h.name
								else i.name end
							) as golongan,
							(
								case
								when b.service_id = 1
								then m.fare
								else n.fare end
							) as tarif_per_jenis,
							k.status_name,
							k.admin_fee, k.diskon, k.transfer_asdp, k.code_promo, k.created_on as updated_settlement
						from app.t_trx_payment_b2b a
						left join app.t_trx_booking b on a.trans_number = b.trans_number
						left join app.t_trx_booking_passanger c on b.booking_code = c.booking_code and c.service_id = 1
						left join app.t_trx_booking_vehicle d on b.booking_code = d.booking_code
						left join app.t_mtr_port e on b.origin = e.id
						left join app.t_mtr_port f on b.destination = f.id
						left join app.t_mtr_ship_class g on g.id = b.ship_class
						left join app.t_mtr_passanger_type h on h.id = c.passanger_type_id 
						left join app.t_mtr_vehicle_class i on i.id = d.vehicle_class_id
						left join app.t_mtr_service j on j.id = b.service_id
						left join app.t_trx_recon_brilink k on k.trans_number = a.trans_number
						left join app.t_mtr_rute l on l.origin = e.id and l.destination = f.id
						left join app.t_mtr_fare_passanger m on m.rute_id = l.id and m.passanger_type = c.passanger_type_id and c.service_id = 1 and m.ship_class = b.ship_class and m.status <> -5
						left join app.t_mtr_fare_vehicle n on n.rute_id = l.id and n.vehicle_class_id = d.vehicle_class_id and n.ship_class = b.ship_class and n.status <> -5
						left join app.t_mtr_merchant o on a.merchant_id = o.merchant_id
						left join app.t_mtr_status p on a.recon_status = p.status and tbl_name = 't_trx_recon_brilink'
						$where
						union all
						select 
							null as id, null as created_on,
							a.trans_number as id_trans, a.payment_code, a.booking_code, 
							(
								case
								when a.service_id = 1
								then b.ticket_number
								else c.ticket_number end 
							) as ticket_number, 
							a.merchant_id, a.time_trx as waktu_trans, a.depart_time::varchar as depart_date , a.settlement_date as waktu_settle,
							a.origin_name as asal, a.destination_name as tujuan, a.ship_class_name as ship_class, a.service_name as service,
							a.shop_code, a.shop_name, 
							a.status_name as reconn_status,
							null as golongan,
							a.total_invoice as tarif_per_jenis,
							a.status_name,
							a.admin_fee, a.diskon, a.transfer_asdp, a.code_promo, a.created_on as updated_settlement
						from
							app.t_trx_recon_brilink a
							left join app.t_trx_booking_passanger b on b.booking_code = a.booking_code
							left join app.t_trx_booking_vehicle c on c.booking_code = a.booking_code
						$where2";

		return $data;
	}

}