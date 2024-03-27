<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_ticket_expired extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function listPenumpang(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$dateFrom = trim($this->input->post('dateFrom'));
		$dateTo = trim($this->input->post('dateTo'));
		$port = $this->enc->decode($this->input->post('port'));
		$payment_type = $this->enc->decode($this->input->post('payment_type'));
		$channel = $this->enc->decode($this->input->post('channel'));
		$searchData=$this->input->post('searchData');
		$searchName=$this->input->post('searchName');
		$iLike  = trim(strtoupper($this->db->escape_like_str($searchData)));

		$field = array(
			0 =>'BP.id',
			1 =>'ticket_number',
			2 =>'nama',
			3 =>'golongan',
			4 =>'servis',
			5 =>'plebuhan',
			6 =>'payment_type',
			7 =>'pembayaran',
			8 =>'cetak_boarding',
			9 =>'gate_in',
			10 =>'checkin_expired',
			11 =>'gatein_expired',
			12 =>'boarding_expired'
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE B.status != -5 and B.created_on >= '". $dateFrom . "' and B.created_on < '" . $dateToNew . "'";
		// $where = " WHERE B.status != -5";

		if(!empty($port))
		{
			$where .=" and (B.origin='".$port."')";
		}

		if (!empty($payment_type))
		{
			$where .= " and (P.payment_type='".$payment_type."')";
		}

		if (!empty($channel))
		{
			$where .= " and (P.channel='".$channel."')";
		}

		if(!empty($searchData))
        {
            if($searchName=='bookingCode')
            {
                $where .=" and (B.booking_code ilike '%".$iLike."%' ) ";
            }
            else if($searchName=='ticketNumber')
            {
                $where .=" and (BP.ticket_number ilike '%".$iLike."%' ) ";
            }
            else
            {
                $where .=" and (BP.name ilike '%".$iLike."%' ) ";
            }
        }

		$getParamCheckIn = $this->select_data("app.t_mtr_custom_param", " where param_name in('checkin_expired_web_eksekutif','checkin_expired_web') ")->result();
		$masterCheckinExpired = array_combine(array_column($getParamCheckIn,"param_name"),array_column($getParamCheckIn,"param_value") );
				
		$sql = "SELECT  BP.id,
                        BP.ticket_number,
                        BP.name AS nama,
                        PT.name as golongan,
                        S.name AS servis,
                        PO.name AS pelabuhan,
                        P.payment_type,
                        P.created_on AS pembayaran,
                        C.created_on AS cetak_boarding,
                        G.created_on AS gate_in,
                        BP.service_id,
                        BP.depart_date,
						BP.depart_time_start,
						BP.gatein_expired,
                        BP.boarding_expired
                FROM app.t_trx_booking B
                JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
				LEFT JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
				LEFT JOIN app.t_trx_check_in C ON C.ticket_number = BP.ticket_number
				LEFT JOIN app.t_trx_gate_in G ON G.ticket_number = BP.ticket_number
                JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id
                JOIN app.t_mtr_port PO ON PO.id = BP.origin
                JOIN app.t_mtr_service S ON S.id = BP.service_id AND BP.service_id = 1
                {$where}
                AND (
					((BP.status = 2 AND concat(bp.depart_date,' ', bp.depart_time_start)::TIMESTAMP +interval'".$masterCheckinExpired['checkin_expired_web_eksekutif']." hour' < CURRENT_TIMESTAMP and bp.ship_class=2)
					OR
					(BP.status = 2 AND concat(bp.depart_date,' ', bp.depart_time_start)::TIMESTAMP +interval'".$masterCheckinExpired['checkin_expired_web']." hour' < CURRENT_TIMESTAMP and bp.ship_class != 2))
                OR (BP.status = 3 AND BP.gatein_expired::timestamp < CURRENT_TIMESTAMP)
                OR ((BP.status = 4 OR BP.status = 7) AND BP.boarding_expired::timestamp < CURRENT_TIMESTAMP)
								OR BP.status = 10 OR BP.status = 11 OR BP.status = 12)";
		// die($sql); exit;

		$sqlCount = "SELECT  
										count(BP.id) as countdata
								FROM app.t_trx_booking B
								JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
								LEFT JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
								LEFT JOIN app.t_trx_check_in C ON C.ticket_number = BP.ticket_number
								LEFT JOIN app.t_trx_gate_in G ON G.ticket_number = BP.ticket_number
								JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id
								JOIN app.t_mtr_port PO ON PO.id = BP.origin
								JOIN app.t_mtr_service S ON S.id = BP.service_id AND BP.service_id = 1
								{$where}
								AND (
									((BP.status = 2 AND concat(bp.depart_date,' ', bp.depart_time_start)::TIMESTAMP +interval'".$masterCheckinExpired['checkin_expired_web_eksekutif']." hour' < CURRENT_TIMESTAMP and bp.ship_class=2)
										OR
										(BP.status = 2 AND concat(bp.depart_date,' ', bp.depart_time_start)::TIMESTAMP +interval'".$masterCheckinExpired['checkin_expired_web']." hour' < CURRENT_TIMESTAMP and bp.ship_class != 2))
								OR (BP.status = 3 AND BP.gatein_expired::timestamp < CURRENT_TIMESTAMP)
								OR ((BP.status = 4 OR BP.status = 7) AND BP.boarding_expired::timestamp < CURRENT_TIMESTAMP)
								OR BP.status = 10 OR BP.status = 11 OR BP.status = 12)";


		$queryCount         = $this->db->query($sqlCount)->row();
    	$records_total 			= $queryCount->countdata;
		// $query         = $this->dbView->query($sql);
		// $records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);

		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->nama = strtoupper($row->nama);
			$row->golongan = strtoupper($row->golongan);
			$row->servis = strtoupper($row->servis);
			$row->pelabuhan = strtoupper($row->pelabuhan);
			$row->payment_type = strtoupper(str_replace('-', ' ', $row->payment_type));

			$url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
			$row->ticket_number = '<a href="'.$url.'" target="_blank">'.$row->ticket_number.'</a>';

			// $row->id_number != 0 ? $row->id_number = $row->id_number : $row->id_number = '-';

			$row->pembayaran ? $row->pembayaran = format_dateTime($row->pembayaran) : $row->pembayaran = '-';
			$row->cetak_boarding ? $row->cetak_boarding = format_dateTime($row->cetak_boarding) : $row->cetak_boarding = '-';
            $row->gate_in ? $row->gate_in = format_dateTime($row->gate_in) : $row->gate_in = '-';

			// expired checkin
			$paramCheckinExpired = $row->service_id==2?$masterCheckinExpired['checkin_expired_web_eksekutif']:$masterCheckinExpired['checkin_expired_web'];
			$checkin_expired_date = $row->depart_date." ".$row->depart_time_start;
			$row->checkin_expired = 	 date('Y-m-d H:i:s ', strtotime($checkin_expired_date . ' +'.$paramCheckinExpired .' hours'));			

			$row->checkin_expired ? $row->checkin_expired = format_dateTime($row->checkin_expired) : $row->checkin_expired = '-';
			$row->gatein_expired ? $row->gatein_expired = format_dateTime($row->gatein_expired) : $row->gatein_expired = '-';
			$row->boarding_expired ? $row->boarding_expired = format_dateTime($row->boarding_expired) : $row->boarding_expired = '-';

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

	public function listKendaraan(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$dateFrom = trim($this->input->post('dateFrom'));
		$dateTo = trim($this->input->post('dateTo'));
		$port = $this->enc->decode($this->input->post('port'));
		$payment_type = $this->enc->decode($this->input->post('payment_type'));
		$channel = $this->enc->decode($this->input->post('channel'));
		$searchData=$this->input->post('searchData');
		$searchName=$this->input->post('searchName');
		$iLike  = trim(strtoupper($this->db->escape_like_str($searchData)));

		$field = array(
			0 =>'BV.id',
			1 =>'ticket_number',
			2 =>'nama',
			3 =>'golongan',
			4 =>'servis',
			5 =>'plebuhan',
			6 =>'payment_type',
			7 =>'pembayaran',
			8 =>'cetak_boarding',
			9 =>'gate_in',
			10 =>'checkin_expired',
			11 =>'gatein_expired',
			12 =>'boarding_expired'
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE B.status != -5 and B.created_on >= '". $dateFrom . "' and B.created_on < '" . $dateToNew . "'";
		// $where = " WHERE B.status != -5";

		if(!empty($port))
		{
			$where .=" and (B.origin='".$port."')";
		}

		if (!empty($payment_type))
		{
			$where .= " and (P.payment_type='".$payment_type."')";
		}

		if (!empty($channel))
		{
			$where .= " and (P.channel='".$channel."')";
		}
	

		if(!empty($searchData))
        {
            if($searchName=='bookingCode')
            {
                $where .=" and (B.booking_code ilike '%".$iLike."%' ) ";
            }
            else if($searchName=='ticketNumber')
            {
                $where .=" and (BV.ticket_number ilike '%".$iLike."%' ) ";
            }
            else
            {
                $where .=" and (BV.id_number ilike '%".$iLike."%' ) ";
            }
        }

		$getParamCheckIn = $this->select_data("app.t_mtr_custom_param", " where param_name in('checkin_expired_web_eksekutif','checkin_expired_web') ")->result();
		$masterCheckinExpired = array_combine(array_column($getParamCheckIn,"param_name"),array_column($getParamCheckIn,"param_value") );

		$sql = "SELECT  BV.id,
                        BV.ticket_number,
                        BV.id_number AS plat,	
                        VC.name as golongan,
                        S.name AS servis,
                        PO.name AS pelabuhan,
                        P.payment_type,
                        P.created_on AS pembayaran,
                        C.created_on AS cetak_boarding,
                        G.created_on AS gate_in,
						BV.service_id,
                        BV.depart_date,
						BV.depart_time_start,
                        BV.gatein_expired,
                        BV.boarding_expired
                FROM app.t_trx_booking B
                JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
				LEFT JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
				LEFT JOIN app.t_trx_check_in_vehicle C ON C.ticket_number = BV.ticket_number
				LEFT JOIN app.t_trx_gate_in_vehicle G ON G.ticket_number = BV.ticket_number
                JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
                JOIN app.t_mtr_port PO ON PO.id = BV.origin
                JOIN app.t_mtr_service S ON S.id = BV.service_id AND BV.service_id = 2
                {$where}
                AND (
					((BV.status = 2 AND concat(BV.depart_date,' ', BV.depart_time_start)::TIMESTAMP +interval'".$masterCheckinExpired['checkin_expired_web_eksekutif']." hour' < CURRENT_TIMESTAMP and BV.ship_class=2)
					OR
					(BV.status = 2 AND concat(BV.depart_date,' ', BV.depart_time_start)::TIMESTAMP +interval'".$masterCheckinExpired['checkin_expired_web']." hour' < CURRENT_TIMESTAMP and BV.ship_class != 2))
                OR (BV.status = 3 AND BV.gatein_expired::timestamp < CURRENT_TIMESTAMP)
                OR ((BV.status = 4 OR BV.status = 7) AND BV.boarding_expired::timestamp < CURRENT_TIMESTAMP)
								OR BV.status = 10 OR BV.status = 11 OR BV.status = 12)";


		$sqlCount = "SELECT  
										count(BV.id) as countdata
								FROM app.t_trx_booking B
								JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
								LEFT JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
								LEFT JOIN app.t_trx_check_in_vehicle C ON C.ticket_number = BV.ticket_number
								LEFT JOIN app.t_trx_gate_in_vehicle G ON G.ticket_number = BV.ticket_number
								JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
								JOIN app.t_mtr_port PO ON PO.id = BV.origin
								JOIN app.t_mtr_service S ON S.id = BV.service_id AND BV.service_id = 2
								{$where}
								AND (
									((BV.status = 2 AND concat(BV.depart_date,' ', BV.depart_time_start)::TIMESTAMP +interval'".$masterCheckinExpired['checkin_expired_web_eksekutif']." hour' < CURRENT_TIMESTAMP and BV.ship_class=2)
					OR
					(BV.status = 2 AND concat(BV.depart_date,' ', BV.depart_time_start)::TIMESTAMP +interval'".$masterCheckinExpired['checkin_expired_web']." hour' < CURRENT_TIMESTAMP and BV.ship_class != 2))
								OR (BV.status = 3 AND BV.gatein_expired::timestamp < CURRENT_TIMESTAMP)
								OR ((BV.status = 4 OR BV.status = 7) AND BV.boarding_expired::timestamp < CURRENT_TIMESTAMP)
								OR BV.status = 10 OR BV.status = 11 OR BV.status = 12)";

		$queryCount         = $this->db->query($sqlCount)->row();
		$records_total 			= $queryCount->countdata;
		// $query         = $this->dbView->query($sql);
		// $records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->plat = strtoupper($row->plat);
			$row->golongan = strtoupper($row->golongan);
			$row->servis = strtoupper($row->servis);
			$row->pelabuhan = strtoupper($row->pelabuhan);
			$row->payment_type = strtoupper(str_replace('-', ' ', $row->payment_type));

			$url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
			$row->ticket_number = '<a href="'.$url.'" target="_blank">'.$row->ticket_number.'</a>';

			// $row->id_number != 0 ? $row->id_number = $row->id_number : $row->id_number = '-';

			$row->pembayaran ? $row->pembayaran = format_dateTime($row->pembayaran) : $row->pembayaran = '-';
			$row->cetak_boarding ? $row->cetak_boarding = format_dateTime($row->cetak_boarding) : $row->cetak_boarding = '-';
            $row->gate_in ? $row->gate_in = format_dateTime($row->gate_in) : $row->gate_in = '-';

			// expired checkin
			$paramCheckinExpired = $row->service_id==2?$masterCheckinExpired['checkin_expired_web_eksekutif']:$masterCheckinExpired['checkin_expired_web'];
			$checkin_expired_date = $row->depart_date." ".$row->depart_time_start;
			$row->checkin_expired = 	 date('Y-m-d H:i:s ', strtotime($checkin_expired_date . ' +'.$paramCheckinExpired .' hours'));			

			$row->checkin_expired ? $row->checkin_expired = format_dateTime($row->checkin_expired) : $row->checkin_expired = '-';
			$row->gatein_expired ? $row->gatein_expired = format_dateTime($row->gatein_expired) : $row->gatein_expired = '-';
			$row->boarding_expired ? $row->boarding_expired = format_dateTime($row->boarding_expired) : $row->boarding_expired = '-';

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}	
	public function listPenumpang_27062023(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$dateFrom = trim($this->input->post('dateFrom'));
		$dateTo = trim($this->input->post('dateTo'));
		$port = $this->enc->decode($this->input->post('port'));
		$payment_type = $this->enc->decode($this->input->post('payment_type'));
		$channel = $this->enc->decode($this->input->post('channel'));
		$searchData=$this->input->post('searchData');
		$searchName=$this->input->post('searchName');
		$iLike  = trim(strtoupper($this->db->escape_like_str($searchData)));

		$field = array(
			0 =>'BP.id',
			1 =>'ticket_number',
			2 =>'nama',
			3 =>'golongan',
			4 =>'servis',
			5 =>'plebuhan',
			6 =>'payment_type',
			7 =>'pembayaran',
			8 =>'cetak_boarding',
			9 =>'gate_in',
			10 =>'checkin_expired',
			11 =>'gatein_expired',
			12 =>'boarding_expired'
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE B.status != -5 and B.created_on >= '". $dateFrom . "' and B.created_on < '" . $dateToNew . "'";
		// $where = " WHERE B.status != -5";

		if(!empty($port))
		{
			$where .=" and (B.origin='".$port."')";
		}

		if (!empty($payment_type))
		{
			$where .= " and (P.payment_type='".$payment_type."')";
		}

		if (!empty($channel))
		{
			$where .= " and (P.channel='".$channel."')";
		}

		// if (!empty($dateTo) and !empty($dateFrom))
		// {
		// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		// }
		// else if(empty($dateFrom) and !empty($dateTo))
		// {
		// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";
		// }
		// else if (!empty($dateFrom) and empty($dateTo))
		// {
		// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";
		// }
		// else
		// {
		// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";
		// }

		// if (!empty($cari)){
		// 	$where .=" and (UPPER(B.booking_code) ilike '%".$like."%' or UPPER(BP.name) ilike '%".$like."%' or UPPER(BP.ticket_number) ilike '%".$like."%') ";
		// }

		// if (!empty($search['value'])){
		// 	$where .=" and (B.booking_code ilike '%".$iLike."%')";
		// }

		if(!empty($searchData))
        {
            if($searchName=='bookingCode')
            {
                $where .=" and (B.booking_code ilike '%".$iLike."%' ) ";
            }
            else if($searchName=='ticketNumber')
            {
                $where .=" and (BP.ticket_number ilike '%".$iLike."%' ) ";
            }
            else
            {
                $where .=" and (BP.name ilike '%".$iLike."%' ) ";
            }
        }

		$sql = "SELECT  BP.id,
                        BP.ticket_number,
                        BP.name AS nama,
                        PT.name as golongan,
                        S.name AS servis,
                        PO.name AS pelabuhan,
                        P.payment_type,
                        P.created_on AS pembayaran,
                        C.created_on AS cetak_boarding,
                        G.created_on AS gate_in,
                        BP.checkin_expired,
                        BP.gatein_expired,
                        BP.boarding_expired
                FROM app.t_trx_booking B
                JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
				LEFT JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
				LEFT JOIN app.t_trx_check_in C ON C.ticket_number = BP.ticket_number
				LEFT JOIN app.t_trx_gate_in G ON G.ticket_number = BP.ticket_number
                JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id
                JOIN app.t_mtr_port PO ON PO.id = BP.origin
                JOIN app.t_mtr_service S ON S.id = BP.service_id AND BP.service_id = 1
                {$where}
                AND ((BP.status = 2 AND BP.checkin_expired::timestamp < CURRENT_TIMESTAMP)
                OR (BP.status = 3 AND BP.gatein_expired::timestamp < CURRENT_TIMESTAMP)
                OR ((BP.status = 4 OR BP.status = 7) AND BP.boarding_expired::timestamp < CURRENT_TIMESTAMP)
								OR BP.status = 10 OR BP.status = 11 OR BP.status = 12)";
		// die($sql); exit;


		$sqlCount = "SELECT  
										count(BP.id) as countdata
								FROM app.t_trx_booking B
								JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
								LEFT JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
								LEFT JOIN app.t_trx_check_in C ON C.ticket_number = BP.ticket_number
								LEFT JOIN app.t_trx_gate_in G ON G.ticket_number = BP.ticket_number
								JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id
								JOIN app.t_mtr_port PO ON PO.id = BP.origin
								JOIN app.t_mtr_service S ON S.id = BP.service_id AND BP.service_id = 1
								{$where}
								AND ((BP.status = 2 AND BP.checkin_expired::timestamp < CURRENT_TIMESTAMP)
								OR (BP.status = 3 AND BP.gatein_expired::timestamp < CURRENT_TIMESTAMP)
								OR ((BP.status = 4 OR BP.status = 7) AND BP.boarding_expired::timestamp < CURRENT_TIMESTAMP)
								OR BP.status = 10 OR BP.status = 11 OR BP.status = 12)";


		$queryCount         = $this->db->query($sqlCount)->row();
    	$records_total 			= $queryCount->countdata;
		// $query         = $this->dbView->query($sql);
		// $records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);

		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->nama = strtoupper($row->nama);
			$row->golongan = strtoupper($row->golongan);
			$row->servis = strtoupper($row->servis);
			$row->pelabuhan = strtoupper($row->pelabuhan);
			$row->payment_type = strtoupper(str_replace('-', ' ', $row->payment_type));

			$url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
			$row->ticket_number = '<a href="'.$url.'" target="_blank">'.$row->ticket_number.'</a>';

			// $row->id_number != 0 ? $row->id_number = $row->id_number : $row->id_number = '-';

			$row->pembayaran ? $row->pembayaran = format_dateTime($row->pembayaran) : $row->pembayaran = '-';
			$row->cetak_boarding ? $row->cetak_boarding = format_dateTime($row->cetak_boarding) : $row->cetak_boarding = '-';
            $row->gate_in ? $row->gate_in = format_dateTime($row->gate_in) : $row->gate_in = '-';

			$row->checkin_expired ? $row->checkin_expired = format_dateTime($row->checkin_expired) : $row->checkin_expired = '-';
			$row->gatein_expired ? $row->gatein_expired = format_dateTime($row->gatein_expired) : $row->gatein_expired = '-';
			$row->boarding_expired ? $row->boarding_expired = format_dateTime($row->boarding_expired) : $row->boarding_expired = '-';

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

	public function listKendaraan_27062023(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$dateFrom = trim($this->input->post('dateFrom'));
		$dateTo = trim($this->input->post('dateTo'));
		$port = $this->enc->decode($this->input->post('port'));
		$payment_type = $this->enc->decode($this->input->post('payment_type'));
		$channel = $this->enc->decode($this->input->post('channel'));
		$searchData=$this->input->post('searchData');
		$searchName=$this->input->post('searchName');
		$iLike  = trim(strtoupper($this->db->escape_like_str($searchData)));

		$field = array(
			0 =>'BV.id',
			1 =>'ticket_number',
			2 =>'nama',
			3 =>'golongan',
			4 =>'servis',
			5 =>'plebuhan',
			6 =>'payment_type',
			7 =>'pembayaran',
			8 =>'cetak_boarding',
			9 =>'gate_in',
			10 =>'checkin_expired',
			11 =>'gatein_expired',
			12 =>'boarding_expired'
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE B.status != -5 and B.created_on >= '". $dateFrom . "' and B.created_on < '" . $dateToNew . "'";
		// $where = " WHERE B.status != -5";

		if(!empty($port))
		{
			$where .=" and (B.origin='".$port."')";
		}

		if (!empty($payment_type))
		{
			$where .= " and (P.payment_type='".$payment_type."')";
		}

		if (!empty($channel))
		{
			$where .= " and (P.channel='".$channel."')";
		}

		// if (!empty($dateTo) and !empty($dateFrom))
		// {
		// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		// }
		// else if(empty($dateFrom) and !empty($dateTo))
		// {
		// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";
		// }
		// else if (!empty($dateFrom) and empty($dateTo))
		// {
		// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";
		// }
		// else
		// {
		// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";
		// }

		if(!empty($searchData))
        {
            if($searchName=='bookingCode')
            {
                $where .=" and (B.booking_code ilike '%".$iLike."%' ) ";
            }
            else if($searchName=='ticketNumber')
            {
                $where .=" and (BV.ticket_number ilike '%".$iLike."%' ) ";
            }
            else
            {
                $where .=" and (BV.id_number ilike '%".$iLike."%' ) ";
            }
        }

		$sql = "SELECT  BV.id,
                        BV.ticket_number,
                        BV.id_number AS plat,	
                        VC.name as golongan,
                        S.name AS servis,
                        PO.name AS pelabuhan,
                        P.payment_type,
                        P.created_on AS pembayaran,
                        C.created_on AS cetak_boarding,
                        G.created_on AS gate_in,
                        BV.checkin_expired,
                        BV.gatein_expired,
                        BV.boarding_expired
                FROM app.t_trx_booking B
                JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
				LEFT JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
				LEFT JOIN app.t_trx_check_in_vehicle C ON C.ticket_number = BV.ticket_number
				LEFT JOIN app.t_trx_gate_in_vehicle G ON G.ticket_number = BV.ticket_number
                JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
                JOIN app.t_mtr_port PO ON PO.id = BV.origin
                JOIN app.t_mtr_service S ON S.id = BV.service_id AND BV.service_id = 2
                {$where}
                AND ((BV.status = 2 AND BV.checkin_expired::timestamp < CURRENT_TIMESTAMP)
                OR (BV.status = 3 AND BV.gatein_expired::timestamp < CURRENT_TIMESTAMP)
                OR ((BV.status = 4 OR BV.status = 7) AND BV.boarding_expired::timestamp < CURRENT_TIMESTAMP)
								OR BV.status = 10 OR BV.status = 11 OR BV.status = 12)";


		$sqlCount = "SELECT  
										count(BV.id) as countdata
								FROM app.t_trx_booking B
								JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
								LEFT JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
								LEFT JOIN app.t_trx_check_in_vehicle C ON C.ticket_number = BV.ticket_number
								LEFT JOIN app.t_trx_gate_in_vehicle G ON G.ticket_number = BV.ticket_number
								JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
								JOIN app.t_mtr_port PO ON PO.id = BV.origin
								JOIN app.t_mtr_service S ON S.id = BV.service_id AND BV.service_id = 2
								{$where}
								AND ((BV.status = 2 AND BV.checkin_expired::timestamp < CURRENT_TIMESTAMP)
								OR (BV.status = 3 AND BV.gatein_expired::timestamp < CURRENT_TIMESTAMP)
								OR ((BV.status = 4 OR BV.status = 7) AND BV.boarding_expired::timestamp < CURRENT_TIMESTAMP)
								OR BV.status = 10 OR BV.status = 11 OR BV.status = 12)";

		$queryCount         = $this->db->query($sqlCount)->row();
		$records_total 			= $queryCount->countdata;
		// $query         = $this->dbView->query($sql);
		// $records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->plat = strtoupper($row->plat);
			$row->golongan = strtoupper($row->golongan);
			$row->servis = strtoupper($row->servis);
			$row->pelabuhan = strtoupper($row->pelabuhan);
			$row->payment_type = strtoupper(str_replace('-', ' ', $row->payment_type));

			$url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
			$row->ticket_number = '<a href="'.$url.'" target="_blank">'.$row->ticket_number.'</a>';

			// $row->id_number != 0 ? $row->id_number = $row->id_number : $row->id_number = '-';

			$row->pembayaran ? $row->pembayaran = format_dateTime($row->pembayaran) : $row->pembayaran = '-';
			$row->cetak_boarding ? $row->cetak_boarding = format_dateTime($row->cetak_boarding) : $row->cetak_boarding = '-';
            $row->gate_in ? $row->gate_in = format_dateTime($row->gate_in) : $row->gate_in = '-';

			$row->checkin_expired ? $row->checkin_expired = format_dateTime($row->checkin_expired) : $row->checkin_expired = '-';
			$row->gatein_expired ? $row->gatein_expired = format_dateTime($row->gatein_expired) : $row->gatein_expired = '-';
			$row->boarding_expired ? $row->boarding_expired = format_dateTime($row->boarding_expired) : $row->boarding_expired = '-';

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	function get_channel_backup(){
		$data  = array(''=>'All');
		$query = $this->dbView->query("SELECT DISTINCT channel FROM app.t_trx_payment ORDER BY channel")->result();

		foreach ($query as $key => $value) {
			$data[$this->enc->encode($value->channel)] = strtoupper(str_replace('_', ' ', $value->channel));
		}

		return $data;
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

	function get_payment_type(){
		$data  = array(''=>'All');
		$query = $this->dbView->query("SELECT DISTINCT payment_type FROM app.t_trx_payment ORDER BY payment_type")->result();

		foreach ($query as $key => $value) {
			$data[$this->enc->encode($value->payment_type)] = strtoupper(str_replace('-', ' ', $value->payment_type));
		}

		return $data;
	}

	public function list_data($port,$payment_type,$channel,$cari,$dateFrom,$dateTo,$type,$searchName)
	{
		$iLike  = trim(strtoupper($this->db->escape_like_str($cari)));
		
		if ($type === 'penumpang') {
			$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

			$where = " WHERE B.status != -5 and B.created_on >= '". $dateFrom . "' and B.created_on < '" . $dateToNew . "'";
			// $where = " WHERE B.status != -5";
			if(!empty($port))
			{
				$where .=" and (B.origin='".$port."')";
			}
			if (!empty($payment_type))
			{
				$where .= " and (P.payment_type='".$payment_type."')";
			}
			if (!empty($channel))
			{
				$where .= " and (P.channel='".$channel."')";
			}
			// if (!empty($dateTo) and !empty($dateFrom))
			// {
			// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
			// }
			// else if(empty($dateFrom) and !empty($dateTo))
			// {
			// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
			// }
			// else if (!empty($dateFrom) and empty($dateTo))
			// {
			// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
			// }
			// else
			// {
			// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";	
			// }
			// if (!empty($cari)){
			// 	$where .=" and (UPPER(B.booking_code) ilike '%".$like."%' or UPPER(BP.name) ilike '%".$like."%' or BP.id_number ilike '%".$like."%' or UPPER(BP.ticket_number) ilike '%".$like."%') ";
			// }

			if(!empty($cari))
        {
            if($searchName=='bookingCode')
            {
                $where .=" and (B.booking_code ilike '%".$iLike."%' ) ";
            }
            else if($searchName=='ticketNumber')
            {
                $where .=" and (BP.ticket_number ilike '%".$iLike."%' ) ";
            }
            else
            {
                $where .=" and (BP.name ilike '%".$iLike."%' ) ";
            }
				}
				
				
			$sql = "SELECT  BP.id,
                            BP.ticket_number,
                            BP.name AS nama,
                            PT.name as golongan,
                            S.name AS servis,
                            PO.name AS pelabuhan,
                            P.payment_type,
                            P.created_on AS pembayaran,
                            C.created_on AS cetak_boarding,
                            G.created_on AS gate_in,
                            BP.checkin_expired,
                            BP.gatein_expired,
                            BP.boarding_expired
                    FROM app.t_trx_booking B
                    JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
                    LEFT JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
                    LEFT JOIN app.t_trx_check_in C ON C.ticket_number = BP.ticket_number
                    LEFT JOIN app.t_trx_gate_in G ON G.ticket_number = BP.ticket_number
                    JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id
                    JOIN app.t_mtr_port PO ON PO.id = BP.origin
                    JOIN app.t_mtr_service S ON S.id = BP.service_id AND BP.service_id = 1
					{$where}
                AND ((BP.status = 2 AND BP.checkin_expired::timestamp < CURRENT_TIMESTAMP)
                OR (BP.status = 3 AND BP.gatein_expired::timestamp < CURRENT_TIMESTAMP)
                OR ((BP.status = 4 OR BP.status = 7) AND BP.boarding_expired::timestamp < CURRENT_TIMESTAMP)
								OR BP.status = 10 OR BP.status = 11 OR BP.status = 12)
					ORDER BY B.ID ASC";

			$check = $this->dbView->query($sql)->num_rows();

			if ($check > 0) {
				return $this->dbView->query($sql)->result();
			}else{
				return false;
			}
		}

		if ($type === 'kendaraan') {
			$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

			$where = " WHERE B.status != -5 and B.created_on >= '". $dateFrom . "' and B.created_on < '" . $dateToNew . "'";
			// $where = " WHERE B.status != -5";
			if(!empty($port))
			{
				$where .=" and (B.origin='".$port."')";
			}
			if (!empty($payment_type))
			{
				$where .= " and (P.payment_type='".$payment_type."')";
			}
			if (!empty($channel))
			{
				$where .= " and (P.channel='".$channel."')";
			}
			// if (!empty($dateTo) and !empty($dateFrom))
			// {
			// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
			// }
			// else if(empty($dateFrom) and !empty($dateTo))
			// {
			// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
			// }
			// else if (!empty($dateFrom) and empty($dateTo))
			// {
			// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
			// }
			// else
			// {
			// 	$where .=" and (to_char(B.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";	
			// }

			if(!empty($cari))
        {
            if($searchName=='bookingCode')
            {
                $where .=" and (B.booking_code ilike '%".$iLike."%' ) ";
            }
            else if($searchName=='ticketNumber')
            {
                $where .=" and (BV.ticket_number ilike '%".$iLike."%' ) ";
            }
            else
            {
                $where .=" and (BV.id_number ilike '%".$iLike."%' ) ";
            }
				}
				
			$sql = "SELECT  BV.id,
                            BV.ticket_number,
                            BV.id_number AS plat,
                            VC.name as golongan,
                            S.name AS servis,
                            PO.name AS pelabuhan,
                            P.payment_type,
                            P.created_on AS pembayaran,
                            C.created_on AS cetak_boarding_pass,
                            G.created_on AS gate_in,
                            BV.checkin_expired,
                            BV.gatein_expired,
                            BV.boarding_expired
                    FROM app.t_trx_booking B
                    JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
                    LEFT JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
                    LEFT JOIN app.t_trx_check_in_vehicle C ON C.ticket_number = BV.ticket_number
                    LEFT JOIN app.t_trx_gate_in_vehicle G ON G.ticket_number = BV.ticket_number
                    JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
                    JOIN app.t_mtr_port PO ON PO.id = BV.origin
                    JOIN app.t_mtr_service S ON S.id = BV.service_id AND BV.service_id = 2
					{$where}
                AND ((BV.status = 2 AND BV.checkin_expired::timestamp < CURRENT_TIMESTAMP)
                OR (BV.status = 3 AND BV.gatein_expired::timestamp < CURRENT_TIMESTAMP)
                OR ((BV.status = 4 OR BV.status = 7) AND BV.boarding_expired::timestamp < CURRENT_TIMESTAMP)
								OR BV.status = 10 OR BV.status = 11 OR BV.status = 12)
					ORDER BY B.ID ASC";

			$check = $this->dbView->query($sql)->num_rows();

			if ($check > 0) {
				return $this->dbView->query($sql)->result();
			}else{
				return false;
			}
		}
	}

	public function select_data($table, $where)
	{
		return $this->dbView->query("select * from $table $where");
	}

}