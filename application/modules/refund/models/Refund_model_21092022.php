<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Refund_model extends MY_Model{

	public function refundList(){
		$start 			= $this->input->post('start');
		$length 		= $this->input->post('length');
		$draw 			= $this->input->post('draw');
		$order 			= $this->input->post('order');
		$order_column 	= $order[0]['column'];
		$order_dir 		= strtoupper($order[0]['dir']);
		$dateFrom		= $this->input->post('dateFrom');
		$dateTo			= $this->input->post('dateTo');
		$status			= $this->enc->decode($this->input->post('status'));
		$refund_type	= $this->enc->decode($this->input->post('refund_type'));
		$ship_class		= $this->enc->decode($this->input->post('ship_class'));
		$approvedBy		= $this->enc->decode($this->input->post('approvedBy'));
		$sla			= $this->input->post('sla');
		$searchData 	= $this->input->post('searchData');
		$searchName 	= $this->input->post('searchName');
		$iLike        	= trim(strtoupper($this->db->escape_like_str($searchData)));

		
		$field = array(
			0 =>'a.id',
			1 =>'a.id',
			2 =>'a.id',
			3 => 'booking_code',
			4 => 'name',
			5 => 'phone',
			6 => 'created_on',
			7 => 'refund_code',
			8 => 'refund_type',
			9 => 'asal',
			10 => 'tujuan',
			11 => 'layanan',
			12 => 'id_number',
			13 => 'golongan',
			14 => 'account_number',
			15 => 'account_name',
			16 => 'bank',
			17 => 'amount',
			18 => 'adm_fee',
			19 => 'refund_fee',
			20 => 'bank_transfer_fee',
			21 => 'jumlah_potongan',
			22 => 'dana_pengembalian',
			23 => 'status_refund',
			24 => 'approved_status_cs',
			25 => 'approved_by_cs',
			26 => 'approved_on_cs',
			27 => 'sla_cs',
			28 => 'keterangan_cs',
			29 => 'catatan_cs',
			30 => 'status_approved',
			31 => 'approved_by',
			32 => 'approved_on',
			33 => 'sla_usaha',
			34 => 'keterangan_usaha',
			35 => 'catatan_usaha',
			36 => 'transfer_status',
			37 => 'approved_by_keuangan',
			38 => 'approved_on_keuangan',
			39 => 'sla_keuangan',
			40 => 'keterangan_keuangan',
			41 => 'catatan_keuangan',
			42 => 'durasi',
			43 => 'keterangan',
		);

        $appIdentity=$this->select_data("app.t_mtr_identity_app","")->row();		

        $dataPort=array();
        if($appIdentity->port_id==0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $portId = $this->enc->decode($this->input->post('port'));
            }
            else
            {
                $portId=$this->session->userdata("port_id");
            }
        }
        else
        {
            $portId=$appIdentity->port_id;
        }		

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		// status 4 dan 5 proses upload di cs
		// $where = "WHERE a.status not in (4,5,6) and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";
		$where = "WHERE a.status is not null and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";
		// $where = "WHERE a.status is not null and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."') ";
		
		if(!empty($portId))
		{
			$where .=" and (b.origin='{$portId}')";
		}

		if(!empty($refund_type))
		{
			$where .=" and (a.refund_type='{$refund_type}')";
		}

		if(!empty($ship_class))
		{
			$where .=" and (b.ship_class='{$ship_class}')";
		}

		if(!empty($status))
		{
			//Verification Proses 
			if($status == 1) {
				$where .=" and (a.status=4 OR a.status = 5)";
			}
			//Validation Process
			else if ($status == 2) {
				$where .=" and (a.status=1 AND a.is_approved is not true)";
			}
			//Transfer Process
			else if ($status == 3) {
				$where .=" and (a.status=1 AND a.is_approved is true)";
			}
			//Customer Revision Process
			else if($status == 4)
			{
				$where .=" and (a.status=3 OR a.status=6 OR a.status = 7)";
			}
			//Rejected
			else if($status == 5)
			{
				$where .=" and (a.status = 8)";
			}
			//Transferred
			else
			{
				$where .=" and (a.status = 2)";
			}
		}

		if(!empty($approvedBy))
		{
			//by CC/CS 
			if ($approvedBy == 1) {
				$where .=" and (a.approved_on_cs is null AND a.channel = 'web_cs')";
			}
			//by Usaha
			else if ($approvedBy == 2) {
				$where .=" and ((a.approved_on is null AND a.channel != 'web_cs') OR (a.approved_on is null AND (a.approved_on_cs is not null AND a.channel = 'web_cs')))";
			}
			//by Keuangan
			else if ($approvedBy == 3) {
				$where .=" and (a.status != 2 AND a.approved_on is not null AND (a.approved_on_cs is not null AND a.channel = 'web_cs'))";
			}
		}

		if(!empty($sla))
		{
			$where .=" and a.status != 2 and (EXTRACT(DAY FROM now()-a.created_on) >='{$sla}')";
		}


		if(!empty($searchData))
		{
			if($searchName=='passName')
			{
				$where .=" and (a.name ilike '%".$iLike."%' )";
			}
			else if($searchName=='bookingCode')
			{
				$where .=" and (a.booking_code ilike '%".$iLike."%' )";
			}
			else if($searchName=='refundCode')
			{
				$where .=" and (a.refund_code ilike '%".$iLike."%' )";
			}
			else if($searchName=='accountName')
			{
				$where .=" and (a.account_name ilike '%".$iLike."%' )";
			}
			else if($searchName=='accountNumber')
			{
				$where .=" and (a.account_number ilike '%".$iLike."%' )";
			}						
			else
			{
				$where .=" and (a.phone ilike '%".$iLike."%' )";	
			}			
		}

		$sql 			= $this->qry($where);
		$sqlCount		= $this->qryCount($where);

		$queryCount     = $this->db->query($sqlCount)->row();
		$records_total 	= $queryCount->countdata;

		$sql .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();
							
		$rows = array();
		$idThPosesCs=array();
		$i = ($start + 1);
		foreach ($rows_data as $row) {
			$row->number 		  = $i;
			$id= $this->enc->encode($row->id);
			// $row->date_collection = format_date($row->date_collection);

			if(empty($row->approved_by))
			{
				$row->approved_by="-";
			}
			if(empty($row->approved_by_keuangan))
			{
				$row->approved_by_keuangan="-";
			}			
			//approved_cs
			if ($row->approved_by_cs == null) {
				$row->approved_status_cs = "-";
				if ($row->status == 6) {
					$row->approved_status_cs = "Revised";
				}
				if ($row->channel == 'web_cs') {
					$row->approved_by_cs = "-";
					$row->approved_on_cs = "-";
				}
				
				$date1 = $row->created_on;
				$date2 = date('Y-m-d H:i:s');

				//jika ada revisi di cs
				if ($row->revisi_cs != null) {
					$date1 = $row->updated_on;
				}

				//sla_cs
				// $row->sla_cs = ceil(abs($date2 - $date1) / 86400);
				$row->sla_cs = $this->getWorkingDays($date1, $date2);

				if (strtoupper($row->channel) != strtoupper('web_cs')) {
					$row->sla_cs = '-';
				}
			}
			else {
				$row->approved_status_cs = "Approved";

				$date1 = $row->created_on;
				$date2 = $row->approved_on_cs;

				//jika ada revisi di cs
				if ($row->revisi_cs != null) {
					$date1 = $row->query_1;
				}

				//sla_cs
				// $row->sla_cs = ceil(abs($date2 - $date1) / 86400);
				$row->sla_cs = $this->getWorkingDays($date1, $date2);
			}


			//sla_usaha	
			$row->status_approved = "-";
			$row->sla_usaha = '-';
			if (($row->channel == 'web_cs' && $row->approved_by_cs != "-") || $row->channel != 'web_cs') {
				$row->status_approved = "-";
				$date_usaha1 = $row->approved_on_cs != null ? $row->approved_on_cs : $row->created_on;
				$date_usaha2 = date('Y-m-d H:i:s');

				if ($row->status == 7) {
					$row->status_approved = "Revised";
				}

				if ($row->status_approved_usaha !== null) {
					$row->status_approved = "Approved";
					$date_usaha2 = $row->approved_on;
				}

				// $row->sla_usaha = ceil(abs($date_usaha2 - $date_usaha1) / 86400);
				$row->sla_usaha = $this->getWorkingDays($date_usaha1, $date_usaha2);
			}
			

			//sla keuangan
			$row->sla_keuangan 		= '-';
			$row->status_keuangan 	= '-';
			if ($row->status_approved_usaha != null) {
				//belum approve dan (sudah approve tapi belum transfer)
				$date_keuangan1 = $row->approved_on;
				$date_keuangan2 = date('Y-m-d H:i:s');

				//sudah approve dan terdapat revisi
				if ($row->approved_on_keuangan !== null && $row->revisi_keuangan !== null && $row->transfer_status == 3) {
					$date_keuangan1 = $row->query_2;
					$row->status_keuangan 	= 'Revised';
				}

				//sudah approve dan sudah transfer
				if ($row->transfer_status == 2) {
					$date_keuangan2 = $row->query_3;
					$row->status_keuangan 	= 'Approved';
				}

				// $row->sla_keuangan = ceil(abs($date_keuangan2 - $date_keuangan1) / 86400);
				$row->sla_keuangan = $this->getWorkingDays($date_keuangan1, $date_keuangan2);
			}


			
			//sla keseluruhan
			$date_durasi1 = strtotime($row->created_on);
			$date_durasi2 = strtotime('now');

			//jika terdapat revisi di cs
			if ($row->approved_by_cs == null && $row->revisi_cs != null) {
				$date_durasi1 = strtotime($row->updated_on);
			}

			//jika terdapat revisi di keuangan
			if ($row->revisi_keuangan != null) {
				$date_durasi1 = strtotime($row->query_2);
			}

			if ($row->transfer_status == 2) {
				$date_durasi2 = strtotime($row->query_3);
			}
			$row->durasi = ceil(abs($date_durasi2 - $date_durasi1) / 86400);



			$row->actions		= "";
			$row->created_on 	= format_date($row->created_on);
			$booking_code 		= $this->enc->encode($row->booking_code);
			$transfer       	= site_url($this->_module."/action_change/".$this->enc->encode($row->id));
			$resend	 			= site_url($this->_module."/resendEmailCS/".$this->enc->encode($row->id));
			$edit_url 	 		= site_url($this->_module."/edit/{$id}");
			$revisi_usaha_url	= site_url($this->_module."/komentarUsaha/{$id}");
			$row->icon_sla 		= "";



			//catatan
			if ($row->catatan_cs == null) {
				$row->catatan_cs = '-';
			}
			if ($row->catatan_usaha == null) {
				$row->catatan_usaha = '-';
			}
			if ($row->catatan_keuangan == null) {
				$row->catatan_keuangan = '-';
			}


			//keterangan SLA dan icon SLA
			//keterangan SLA dan icon SLA
			if ($row->sla_cs > 1) {
				$row->keterangan_cs = 'Over SLA';
				if ($row->approved_by_cs == null || $row->approved_by_cs == '-') {
					$row->icon_sla = '<div class="bg-icon"><i class="fa fa-exclamation" style="color: yellow; font-size: 25px; padding: 15px; text-shadow: 3px 4px 2px #383a3e; "></i></div>';
				}
			}
			else if ($row->sla_cs <= 1 && $row->sla_cs != '-') {
				$row->keterangan_cs = 'Under SLA';
			}
			else {
				$row->keterangan_cs = '-';
			}

			if ($row->sla_usaha > 2) {
				$row->keterangan_usaha = 'Over SLA';
				if ($row->approved_by == null || $row->approved_by == '-') {
					$row->icon_sla = '<div class="bg-icon"><i class="fa fa-exclamation" style="color: orange; font-size: 25px; padding: 15px;  text-shadow: 3px 4px 2px #383a3e;"></i></div>';
				}
			}
			else if ($row->sla_usaha <= 2 && $row->sla_usaha != '-') {
				$row->keterangan_usaha = 'Under SLA';
			}
			else {
				$row->keterangan_usaha = '-';
			}

			if ($row->sla_keuangan > 7) {
				$row->keterangan_keuangan = 'Over SLA';
				if ($row->approved_by_keuangan == null || $row->approved_by_keuangan == '-') {
					$row->icon_sla = '<div class="bg-icon"><i class="fa fa-exclamation" style="color: chocolate; font-size: 25px; padding: 15px; text-shadow: 3px 4px 2px #383a3e;"></i></div>';
				}
			}
			else if ($row->sla_keuangan <= 7 && $row->sla_keuangan != '-') {
				$row->keterangan_keuangan = 'Under SLA';
			}
			else {
				$row->keterangan_keuangan = '-';
			}

			if ($row->durasi > 30) {
				$row->keterangan = 'Over SLA';
			}
			else if ($row->durasi <= 30 && $row->durasi != '-') {
				$row->keterangan = 'Under SLA';
			}
			else {
				$row->keterangan = '-';
			}



			if ($row->status == 4 || $row->status == 5) {
				$row->status_refund = warning_label(wordwrap("Verification Process", 15, "<br>\n"));
			}
			else if ($row->status == 1 && $row->is_approved == false) {
				$row->status_refund = warning_label(wordwrap("Validation Process", 15, "<br>\n"));
			}
			else if (($row->is_approved == true && $row->status == 1 ) || $row->transfer_status == 11 ) {
				$row->status_refund = warning_label(wordwrap("Transfer Process", 15, "<br>\n"));
			}
			else if ($row->is_approved == true && $row->transfer_status == 2) {
				$row->status_refund = success_label(wordwrap("Transferred", 15, "<br>\n"));
				$row->icon_sla = "";
			}
			else if ($row->status == 6 || $row->status == 7 || ($row->is_approved == true && $row->transfer_status == 3)) {
				$row->status_refund =failed_label(wordwrap("Customer Revision Process", 15, "<br>\n"));
			}
			else if ($row->status == 8) {
				$row->status_refund = failed_label(wordwrap("Rejected", 15, "<br>\n"));
			}
			



			$row->checkBox ="";
			// $row->status_approved ="";

			$row->DT_RowId="th_".$i; // penambahan id untuk tr datatable

			$idCk="ck_".$i;
			if($row->status==4 or $row->status==5 or $row->status==6 ) // proses refund ketika masih di proses cs
			{
				$row->status_approved="-";
				$idThPosesCs[]=$row->DT_RowId;

				if($row->status==4)
				{
					$row->status=failed_label(wordwrap("Proses CS Belum Upload",15,"<br>\n"));
				} 
				else if ($row->status==5)
				{
					$row->status=failed_label(wordwrap("Proses CS Menunggu Verifikasi",15,"<br>\n"));
				}
				else
				{
					$row->status=failed_label(wordwrap("Proses CS Revisi Upload",15,"<br>\n"));
				}

			}
			else // proses refund normal
			{
				if($row->is_approved == false && $row->status != 8 )
				{
					if ($row->status != 7) {
						$row->checkBox .='	<div class="checkbox checkcheck">
												<label>
													<input type="checkbox" value="'.$this->enc->encode($row->id).'" name="check" class="myCheck" id="'.$idCk.'"  >
													<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
												</label>
											</div>' ;
					}

					if ($row->status == 1) {
						$row->actions .= generate_button_new($this->_module, 'edit', $revisi_usaha_url);
					}

				}
				else
				{
					if (!in_array($row->transfer_status, [2, 3, 7, 8])) {
						$row->actions.= generate_button_new($this->_module, 'edit', $edit_url);	
					}				
				}


			}

			//button resend email for refund from web cs
			if (strtoupper($row->channel) == "WEB_CS" && ($row->transfer_status == 3 || $row->transfer_status == 7)) {
				$row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengirim email kembali ?\', \''.$resend.'\')" title="Resend Email"> <i class="fa fa-send"></i> </button> ');
			}

			// $row->total_amount= idr_currency($row->total_amount);

			$row->actions .= generate_button_new('refund', 'detail', site_url('refund/detail/'.$booking_code));

			$row->approved_on=empty($row->approved_on)?"-":format_date($row->approved_on)." ".format_time($row->approved_on);
			$row->approved_on_keuangan=empty($row->approved_on_keuangan)?"-":$row->approved_on_keuangan;

			// $row->route_name=strtoupper($row->route_name);
			$row->name=strtoupper($row->name);

						
			$rows[] = $row;
			unset($row->id);
			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows,
			'idThProsesCs'	=>$idThPosesCs,
		);

	}
	
	public function get_detail($booking_code){
		return $this->db->query("
				SELECT 
					e.id_image,
					e.account_number,
					e.account_name,
					e.bank,
					e.bukti_nodin,
					d.name as port_name,
					c.invoice_date,
					c.customer_name,
					c.email,
					e.email as email_from_refund,
					c.phone_number,
					b.id_number as plat_no,
					a.booking_code,
					a.depart_date ,
					a.service_id ,
					a.depart_time_start,
					a.depart_time_end,
					a.ship_class,
					e.refund_code,
					e.stnk_image from app.t_trx_booking a
				left join app.t_trx_booking_vehicle b on a.booking_code=b.booking_code
				left join app.t_trx_invoice c on a.trans_number= c.trans_number
				left join  app.t_mtr_port d on a.origin= d.id
				left join app.t_trx_refund e on a.booking_code= e.booking_code
				where a.booking_code='{$booking_code}'
		");
	}
	
	public function getDetailRefund($id){
		return $this->db->query("
			SELECT vc.name AS vehicle, bp.name, a.ticket_number, a.fare, fee FROM app.t_trx_refund_detail r
			LEFT JOIN app.t_trx_booking_passanger bp ON bp.ticket_number =a.ticket_number
			LEFT JOIN app.t_trx_booking_vehicle bv ON bv.ticket_number =a.ticket_number
			LEFT JOIN app.t_mtr_vehicle_class vc ON vc.id = bv.vehicle_class_id WHERE refund_id = $id
		");
	}

	public function getDetailEmail($bookingCode)
	{
		return $this->db->query("SELECT c.phone_number, c.extra_fee, c.amount as tarif, c.total_amount, c.customer_name,c.email,b.channel, a.* from app.t_trx_refund a
			left join app.t_trx_booking b on a.booking_code=b.booking_code
			left join app.t_trx_invoice c on b.trans_number=c.trans_number
			where a.booking_code='{$bookingCode}' ");
	}

	public function getDetailEmail_21_09_2022($bookingCode)
	{
		return $this->db->query("SELECT c.phone_number, c.extra_fee, c.amount as tarif, c.total_amount, c.customer_name,c.email,b.channel, a.* from app.t_trx_refund a
			left join app.t_trx_booking b on a.booking_code=b.booking_code
			left join app.t_trx_invoice c on b.trans_number=c.trans_number
			left join app.t_trx_payment d on c.trans_number= d.invoice_number where a.booking_code='{$bookingCode}' ");
	}

	public function historyTransfer___ ($bookingCode)
	{
		$rows_data =  $this->db->query("SELECT a.*, c.description from app.t_log_refund_transfer a
									left join app.t_trx_refund b on a.refund_code=b.refund_code  and b.status<>'-5'
									left join app.t_mtr_status c on a.transfer_status = c.status and c.tbl_name = 't_trx_refund'
									where a.status<>'-5' and a.booking_code='{$bookingCode}' --and a.transfer_status not in (4,5,6,1)
									order by a.created_on desc 
							   ")->result();


		$data = array();
		foreach($rows_data as $row) {
			if ($row->transfer_status == 4 || $row->transfer_status == 5) {
				$row->status_refund = wordwrap("Verification Process", 20, "<br>\n");
				$row->status_tgl_edit = "Diproses";
			}
			else if ($row->transfer_status == 1 && $row->is_approved == 'f' ) {
				$row->status_refund = wordwrap("Validation Process", 20, "<br>\n");
				$row->status_tgl_edit = "Diproses";
			}
			/*
			else if ($row->is_approved == true && $row->status == 1) {
				$row->status_refund = wordwrap("Transfer Process", 20, "<br>\n");
				$row->status_tgl_edit = "Diproses";
			} */
			else if ($row->is_approved == 't' && $row->status == 1) {
				$row->status_refund = wordwrap("Transfer Process", 20, "<br>\n");
				$row->status_tgl_edit = "Diproses";
			}
			else if ($row->is_approved == 't' && $row->transfer_status == 2) {
				$row->status_refund = wordwrap("Transferred", 20, "<br>\n");
				$row->status_tgl_edit = "Ditransfer";
			}
			else if ($row->transfer_status == 6 || $row->transfer_status == 7 || ($row->is_approved == 't' && $row->transfer_status == 3)) {
				$row->status_refund = wordwrap("Customer Revision Process", 20, "<br>\n");
				$row->status_tgl_edit = "Direvisi";
			}
			else if ($row->transfer_status == 8) {
				$row->status_refund = wordwrap("Rejected", 20, "<br>\n");
				$row->status_tgl_edit = "Ditolak";
			}
			else if($row->transfer_status == 11)			
			{
				$row->status_refund = wordwrap("Approved Usaha", 20, "<br>\n");
				$row->status_tgl_edit = "Diapproved";
			}

			$data[] = $row;
		}
		return $data;
	}	
	public function historyTransfer($bookingCode)
	{
		$rows_data =  $this->db->query("SELECT a.*, c.description, b.is_approved, b.approved_on from app.t_log_refund_transfer a
									left join app.t_trx_refund b on a.refund_code=b.refund_code  and b.status<>'-5'
									left join app.t_mtr_status c on a.transfer_status = c.status and c.tbl_name = 't_trx_refund'
									where a.status<>'-5' and a.booking_code='{$bookingCode}' --and a.transfer_status not in (4,5,6,1)
									order by a.created_on desc 
							   ")->result();


		$data = array();
		foreach($rows_data as $row) {
			if ($row->transfer_status == 4 || $row->transfer_status == 5) {
				$row->status_refund = wordwrap("Verification Process", 20, "<br>\n");
				$row->status_tgl_edit = "Diproses";
			}
			// else if ($row->transfer_status == 1 && $row->is_approved == 'f' ) {
			// 	$row->status_refund = wordwrap("Validation Process", 20, "<br>\n");
			// 	$row->status_tgl_edit = "Diproses";
			// }

			/*
			else if ($row->transfer_status == 1 && ($row->approved_on != "" && $row->created_on < $row->approved_on)) {
				$row->status_refund = wordwrap("Validation Process", 20, "<br>\n");
				$row->status_tgl_edit = "Diproses";
			}			
			*/
			else if ($row->transfer_status == 1 && ( $row->approved_on == null or  ($row->approved_on !="" && $row->created_on < $row->approved_on))) {
				$row->status_refund = wordwrap("Validation Process", 20, "<br>\n");
				$row->status_tgl_edit = "Diproses";
			}						
			/*
			
			else if ($row->is_approved == true && $row->status == 1) {
				$row->status_refund = wordwrap("Transfer Process", 20, "<br>\n");
				$row->status_tgl_edit = "Diproses";
			} */
			else if (($row->is_approved == 't' && $row->transfer_status == 1 )  || $row->transfer_status == 11 ) {
				$row->status_refund = wordwrap("Transfer Process", 20, "<br>\n");
				$row->status_tgl_edit = "Diproses";
			}
			else if ($row->is_approved == 't' && $row->transfer_status == 2) {
				$row->status_refund = wordwrap("Transferred", 20, "<br>\n");
				$row->status_tgl_edit = "Ditransfer";
			}
			else if ($row->transfer_status == 6 || $row->transfer_status == 7 || ($row->is_approved == 't' && $row->transfer_status == 3)) {
				$row->status_refund = wordwrap("Customer Revision Process", 20, "<br>\n");
				$row->status_tgl_edit = "Direvisi";
			}
			else if ($row->transfer_status == 8) {
				$row->status_refund = wordwrap("Rejected", 20, "<br>\n");
				$row->status_tgl_edit = "Ditolak";
			}


			$data[] = $row;
		}
		return $data;
	}

	public function buktiTransfer($refund_code)
	{
		return $this->db->query("SELECT bukti_transfer from app.t_trx_refund_bukti_transfer a
									where a.status<>'-5' and a.refund_code='{$refund_code}'
							  ");
	}

	public function buktiNodin($refund_code)
	{
		return $this->db->query("SELECT bukti_nodin from app.t_trx_refund_bukti_nodin a
									where a.status<>'-5' and a.refund_code='{$refund_code}'
							  ");
	}

	public function historyTransfer_07052021($bookingCode)
	{
		return $this->db->query("
				SELECT a.* from app.t_log_refund_transfer a
				left join app.t_trx_refund b on a.refund_code=b.refund_code  and b.status<>'-5'
				where a.status<>'-5' and a.booking_code='{$bookingCode}'
				order by a.created_on desc 
			");
	}	

	public function qry($where)
	{

		return $data="SELECT 
						a.id,
						a.is_approved,
						a.status,
						a.booking_code,
						a.refund_code,
						a.name,
						a.phone,
						a.created_on,
						a.updated_on,
						a.channel,
						tmrt.name as refund_type,
						d.name as asal,
						e.name as tujuan,
						tmsc.name as layanan,
						c.name as jenis_pj,
						ttbv.id_number ,
						a.account_number ,
						(
							case 
							when a.service_id = 2
							then vc.name
							else 'Dewasa' end
						) as golongan ,
						a.account_name ,
						a.bank ,
						b.amount ,
						tti.extra_fee ,
						a.adm_fee,
						a.refund_fee ,
						a.bank_transfer_fee ,
						a.charge_amount as jumlah_potongan,
						a.total_amount as dana_pengembalian,
						tmst.description as status_description,
						a.approved_by_cs as approved_by_cs ,
						a.approved_on_cs ,
						'sla cs ' as sla_cs,					
						'keterangan' as keterangan_cs,
						a.is_approved as status_approved_usaha,
						a.approved_by,
						a.approved_on,
						'sla cs ' as sla_usaha,
						'keterangan' as keterangan_usaha,
						(
							SELECT a2.transfer_description from app.t_log_refund_transfer a2
							left join app.t_trx_refund b2 on a2.refund_code=b2.refund_code  and b2.status<>'-5'
							where a2.status<>'-5' 
							and a2.booking_code=a.booking_code 
							and a2.transfer_status 
							in (7)
							order by a2.id desc limit 1
						) as catatan_usaha,
						(
							SELECT a2.created_by from app.t_log_refund_transfer a2
							left join app.t_trx_refund b2 on a2.refund_code=b2.refund_code  and b2.status<>'-5'
							where a2.status<>'-5' 
							and a2.booking_code=a.booking_code 
							and a2.transfer_status 
							not in (1, 4, 5, 6, 7, 8, 11)
							order by a2.id desc limit 1
						) as approved_by_keuangan,
						(
							SELECT a2.transfer_status from app.t_log_refund_transfer a2
							left join app.t_trx_refund b2 on a2.refund_code=b2.refund_code  and b2.status<>'-5'
							where a2.status<>'-5' 
							and a2.booking_code=a.booking_code 
							and a2.transfer_status 
							not in (4,5,6)
							order by a2.id desc limit 1
						) as transfer_status,	
						(
							SELECT a2.transfer_description from app.t_log_refund_transfer a2
							left join app.t_trx_refund b2 on a2.refund_code=b2.refund_code  and b2.status<>'-5'
							where a2.status<>'-5' 
							and a2.booking_code=a.booking_code 
							and a2.transfer_status 
							not in (4, 5, 6, 7)
							order by a2.id desc limit 1
						) as transfer_description,										
						(
							SELECT a2.created_on from app.t_log_refund_transfer a2
							left join app.t_trx_refund b2 on a2.refund_code=b2.refund_code  and b2.status<>'-5'
							where a2.status<>'-5' 
							and a2.booking_code=a.booking_code 
							and a2.transfer_status 
							not in (1, 4, 5, 6, 7, 8, 11)
							order by a2.id desc limit 1
						) as approved_on_keuangan,
						(
							SELECT a2.created_on from app.t_log_refund_transfer a2
							left join app.t_trx_refund b2 on a2.refund_code=b2.refund_code  and b2.status<>'-5'
							where a2.status<>'-5' 
							and a2.booking_code=a.booking_code 
							and a2.transfer_status = '6'
							order by a2.id desc limit 1
						) as revisi_cs,
						(
							SELECT a2.created_on from app.t_log_refund_transfer a2
							left join app.t_trx_refund b2 on a2.refund_code=b2.refund_code  and b2.status<>'-5'
							where a2.status<>'-5' 
							and a2.booking_code= a.booking_code 
							and a2.transfer_status = '3'
							order by a2.id desc limit 1
						) as revisi_keuangan,
						(
							SELECT a2.created_on from app.t_log_refund_transfer a2
							left join app.t_trx_refund b2 on a2.refund_code=b2.refund_code  and b2.status<>'-5'
							where a2.status<>'-5' 
							and a2.booking_code=a.booking_code 
							and a2.transfer_status in (4, 5, 6)
							order by a2.id desc limit 1		
						) as query_1,
						(
							SELECT a2.created_on from app.t_log_refund_transfer a2
							left join app.t_trx_refund b2 on a2.refund_code=b2.refund_code  and b2.status<>'-5'
							where a2.status<>'-5' 
							and a2.booking_code=a.booking_code 
							and a2.transfer_status = 1
							order by a2.id desc limit 1		
						) as query_2,
						(
							SELECT a2.created_on from app.t_log_refund_transfer a2
							left join app.t_trx_refund b2 on a2.refund_code=b2.refund_code  and b2.status<>'-5'
							where a2.status<>'-5' 
							and a2.booking_code=a.booking_code 
							and a2.transfer_status = 2
							order by a2.id desc limit 1		
						) as query_3,
						(
							SELECT a2.komentar_cs from app.t_log_refund_transfer a2
							left join app.t_trx_refund b2 on a2.refund_code=b2.refund_code  and b2.status<>'-5'
							where a2.status<>'-5' 
							and a2.booking_code=a.booking_code 
							and a2.transfer_status = 6
							order by a2.id desc limit 1		
						) as catatan_cs,
						'-' as sla_keuangan,
						a.transfer_description as catatan_keuangan,
						-- EXTRACT(DAY FROM now()-a.created_on) as durasi,
						'keterangan' as keterangan					
						from app.t_trx_refund a
					left join app.t_trx_booking b on a.booking_code=b.booking_code
					left join app.t_mtr_service c on a.service_id=c.id 
					left join app.t_mtr_port d on b.origin = d.id and d.status=1
					left join app.t_mtr_port e on b.destination = e.id and e.status=1
					left join app.t_mtr_refund_type tmrt  on a.refund_type = tmrt.id 
					left join app.t_mtr_ship_class tmsc on b.ship_class =tmsc.id
					left join app.t_trx_booking_vehicle ttbv on a.booking_code = ttbv.booking_code 
					left join app.t_trx_invoice tti on b.trans_number =tti.trans_number
					left join app.t_mtr_status tmst on tmst.status = a.status and tmst.tbl_name = 't_trx_refund'
					left join app.t_mtr_vehicle_class vc on vc.id = ttbv.vehicle_class_id
					{$where}
		";
	}

	public function qryCount($where)
	{
		return $data="
				SELECT 
					count(a.id) as countdata from
				app.t_trx_refund a
				left join app.t_trx_booking b on a.booking_code=b.booking_code
				left join app.t_mtr_service c on a.service_id=c.id 
				left join app.t_mtr_port d on b.origin = d.id and d.status=1
				left join app.t_mtr_port e on b.destination = e.id and e.status=1
				left join app.t_mtr_refund_type tmrt  on a.refund_type = tmrt.id 
				left join app.t_mtr_ship_class tmsc on b.ship_class =tmsc.id
				left join app.t_trx_booking_vehicle ttbv on a.booking_code = ttbv.booking_code 
				left join app.t_trx_invoice tti on b.trans_number =tti.trans_number 
				{$where}
		";
	}

	public function qry_old($where)
	{

		return $data="
				SELECT 
					concat(d.name,' - ',e.name) as route_name , 
					d.name as port_name ,
					c.name as service_name,
					a.id,
					a.booking_code,
					a.name,
					a.phone,
					a.created_on,
					a.refund_code,
					a.account_number,
					a.account_name,
					a.bank,
					a.status,
					a.approved_by,
					a.approved_on,
					a.channel,
					a.total_amount,
					a.is_approved
					from app.t_trx_refund a
				left join app.t_trx_booking b on a.booking_code=b.booking_code
				left join app.t_mtr_service c on a.service_id=c.id 
				left join app.t_mtr_port d on b.origin = d.id and d.status=1
				left join app.t_mtr_port e on b.destination = e.id and e.status=1
				{$where}
		";
	}

	public function download()
	{

		$dateFrom			= $this->input->get('dateFrom');
		$dateTo				= $this->input->get('dateTo');
		$status				= $this->enc->decode($this->input->get('status'));
		$refund_type		= $this->enc->decode($this->input->get('refund_type'));
		$searchData 		= $this->input->get('searchData');
		$searchName 		= $this->input->get('searchName');
		$iLike        	= trim(strtoupper($this->db->escape_like_str($searchData)));
		

        $appIdentity=$this->select_data("app.t_mtr_identity_app","")->row();		

        $dataPort=array();
        if($appIdentity->port_id==0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $portId = $this->enc->decode($this->input->post('port'));
            }
            else
            {
                $portId=$this->session->userdata("port_id");
            }
        }
        else
        {
            $portId=$appIdentity->port_id;
        }		


		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = "WHERE a.status is not null and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";
		
		if(!empty($portId))
		{
			$where .=" and (b.origin='{$portId}')";
		}

		if(!empty($refund_type))
		{
			$where .=" and (a.refund_type='{$refund_type}')";
		}

		if(!empty($status))
		{
			if($status=='1_b')
			{
				$where .=" and (a.status=1 and is_approved is not true )";
			}
			else
			{
				$where .=" and (a.status='{$status}')";
			}
		}


		if(!empty($searchData))
		{
			if($searchName=='passName')
			{
				$where .=" and (a.name ilike '%".$iLike."%' )";
			}
			else if($searchName=='bookingCode')
			{
				$where .=" and (a.booking_code ilike '%".$iLike."%' )";
			}
			else if($searchName=='refundCode')
			{
				$where .=" and (a.refund_code ilike '%".$iLike."%' )";
			}
			else if($searchName=='accountName')
			{
				$where .=" and (a.account_name ilike '%".$iLike."%' )";
			}
			else if($searchName=='accountNumber')
			{
				$where .=" and (a.account_number ilike '%".$iLike."%' )";
			}						
			else
			{
				$where .=" and (a.phone ilike '%".$iLike."%' )";	
			}			
		}

		$qry=$this->qry($where)." order by a.id desc ";
		// $qry=$this->qry_old($where)." order by a.id desc ";

		$getData=$this->db->query($qry)->result();
		$data=array();

		foreach ($getData as $key => $row) {

			//approved_cs
			if ($row->approved_by_cs == null) {
				$row->approved_status_cs = "-";
				if ($row->status == 6) {
					$row->approved_status_cs = "Revised";
				}
				if ($row->channel == 'web_cs') {
					$row->approved_by_cs = "-";
					$row->approved_on_cs = "-";
				}
				
				$date1 = $row->created_on;
				$date2 = date('Y-m-d H:i:s');
		
				//jika ada revisi di cs
				if ($row->revisi_cs != null) {
					$date1 = $row->updated_on;
				}
		
				//sla_cs
				// $row->sla_cs = ceil(abs($date2 - $date1) / 86400);
				$row->sla_cs = $this->getWorkingDays($date1, $date2);
		
				if (strtoupper($row->channel) != strtoupper('web_cs')) {
					$row->sla_cs = '-';
				}
			}
			else {
				$row->approved_status_cs = "Approved";
		
				$date1 = $row->created_on;
				$date2 = $row->approved_on_cs;
		
				//jika ada revisi di cs
				if ($row->revisi_cs != null) {
					$date1 = $row->query_1;
				}
		
				//sla_cs
				// $row->sla_cs = ceil(abs($date2 - $date1) / 86400);
				$row->sla_cs = $this->getWorkingDays($date1, $date2);
			}
		
		
			//sla_usaha	
			$row->status_approved = "-";
			$row->sla_usaha = '-';
			if (($row->channel == 'web_cs' && $row->approved_by_cs != "-") || $row->channel != 'web_cs') {
				$row->status_approved = "-";
				$date_usaha1 = $row->approved_on_cs != null ? $row->approved_on_cs : $row->created_on;
				$date_usaha2 = date('Y-m-d H:i:s');
		
				if ($row->status == 7) {
					$row->status_approved = "Revised";
				}
		
				if ($row->status_approved_usaha !== null) {
					$row->status_approved = "Approved";
					$date_usaha2 = $row->approved_on;
				}
		
				// $row->sla_usaha = ceil(abs($date_usaha2 - $date_usaha1) / 86400);
				$row->sla_usaha = $this->getWorkingDays($date_usaha1, $date_usaha2);
			}
			
		
			//sla keuangan
			$row->sla_keuangan 		= '-';
			$row->status_keuangan 	= '-';
			if ($row->status_approved_usaha != null) {
				//belum approve dan (sudah approve tapi belum transfer)
				$date_keuangan1 = $row->approved_on;
				$date_keuangan2 = date('Y-m-d H:i:s');
		
				//sudah approve dan terdapat revisi
				if ($row->approved_on_keuangan !== null && $row->revisi_keuangan !== null) {
					$date_keuangan1 = $row->query_2;
					$row->status_keuangan 	= 'Revised';
				}
		
				//sudah approve dan sudah transfer
				if ($row->transfer_status == 2) {
					$date_keuangan2 = $row->query_3;
					$row->status_keuangan 	= 'Approved';
				}
		
				// $row->sla_keuangan = ceil(abs($date_keuangan2 - $date_keuangan1) / 86400);
				$row->sla_keuangan = $this->getWorkingDays($date_keuangan1, $date_keuangan2);
			}
		
		
			//sla keseluruhan
			$date_durasi1 = strtotime($row->created_on);
			$date_durasi2 = strtotime('now');
			
			// empty($row->approved_on_keuangan)?"-":$row->approved_on_keuangan;

			//jika terdapat revisi di cs
			if ($row->approved_by_cs == null && $row->revisi_cs != null) {
				$date_durasi1 = strtotime($row->updated_on);
			}
		
			//jika terdapat revisi di keuangan
			if ($row->revisi_keuangan != null) {
				$date_durasi1 = strtotime($row->query_2);
			}
		
			if ($row->transfer_status == 2) {
				$date_durasi2 = strtotime($row->query_3);
			}
			$row->durasi = ceil(abs($date_durasi2 - $date_durasi1) / 86400);


			//catatan
			if ($row->catatan_cs == null) {
				$row->catatan_cs = '-';
			}
			if ($row->catatan_usaha == null) {
				$row->catatan_usaha = '-';
			}
			if ($row->catatan_keuangan == null) {
				$row->catatan_keuangan = '-';
			}

			//keterangan SLA
			//sla cs
			if ($row->sla_cs > 1) {
				$row->keterangan_cs = 'Over SLA';
			}
			else if ($row->sla_cs <= 1 && $row->sla_cs != '-') {
				$row->keterangan_cs = 'Under SLA';
			}
			else {
				$row->keterangan_cs = '-';
			}
		
			//sla usaha
			if ($row->sla_usaha > 2) {
				$row->keterangan_usaha = 'Over SLA';
			}
			else if ($row->sla_usaha <= 2 && $row->sla_usaha != '-') {
				$row->keterangan_usaha = 'Under SLA';
			}
			else {
				$row->keterangan_usaha = '-';
			}
		
			//sla keuangan
			if ($row->sla_keuangan > 7) {
				$row->keterangan_keuangan = 'Over SLA';
			}
			else if ($row->sla_keuangan <= 7 && $row->sla_keuangan != '-') {
				$row->keterangan_keuangan = 'Under SLA';
			}
			else {
				$row->keterangan_keuangan = '-';
			}
		
			//sla keseluruhan
			if ($row->durasi > 30) {
				$row->keterangan = 'Over SLA';
			}
			else if ($row->durasi <= 30 && $row->durasi != '-') {
				$row->keterangan = 'Under SLA';
			}
			else {
				$row->keterangan = '-';
			}


			if ($row->status == 4 || $row->status == 5) {
				$row->status_refund = "Verification Process";
			}
			else if ($row->status == 1 && $row->is_approved == false) {
				$row->status_refund = "Validation Process";
			}
			else if ($row->is_approved == true && $row->status == 1) {
				$row->status_refund = "Transfer Process";
			}
			else if ($row->is_approved == true && $row->transfer_status == 2) {
				$row->status_refund = "Transferred";
				$row->icon_sla = "";
			}
			else if ($row->status == 6 || $row->status == 7 || ($row->is_approved == true && $row->transfer_status == 3)) {
				$row->status_refund = "Customer Revision Process";
			}
			else if ($row->status == 7) {
				$row->status_refund = "Rejected";
			}

			$data[]=$row;
		}	

		return $data;
	}

	public function select_data($table, $where)
	{
		return $this->db->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->db->insert($table, $data);
	}

	public function update_data($table,$data,$where)
	{
		$this->db->where($where);
		$this->db->update($table, $data);
	}

	public function delete_data($table,$data,$where)
	{
		$this->db->where($where);
		$this->db->delete($table, $data);
	}

	public function number_of_working_days($from, $to) {
		$workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
		$holidayDays = ['*-12-25', '*-01-01', '2013-12-23']; # variable and fixed holidays
	
		$from = new DateTime($from);
		$to = new DateTime($to);
		$to->modify('+1 day');
		$interval = new DateInterval('P1D');
		$periods = new DatePeriod($from, $interval, $to);
	
		$days = 0;
		foreach ($periods as $period) {
			if (!in_array($period->format('N'), $workingDays)) continue;
			if (in_array($period->format('Y-m-d'), $holidayDays)) continue;
			if (in_array($period->format('*-m-d'), $holidayDays)) continue;
			$days++;
		}
		return $days;
	}

	public function getWorkingDays($startDate, $endDate)
	{
		$begin = strtotime($startDate);
		$end   = strtotime($endDate);
		if ($begin > $end) {
	
			return 0;
		} else {
			$no_days  = 0;
			while ($begin <= $end) {
				$what_day = date("N", $begin);
				if (!in_array($what_day, [6,7]) ) // 6 and 7 are weekend
					$no_days++;
				$begin += 86400; // +1 day
			};
	
			return $no_days;
		}
	}


}
