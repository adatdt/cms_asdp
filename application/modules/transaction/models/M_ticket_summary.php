<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class M_ticket_summary extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function listPenumpang() {
        $start        = $this->input->post('start');
        $length       = $this->input->post('length');
        $draw         = $this->input->post('draw');
        $search       = $this->input->post('search');
        $order        = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir    = strtoupper($order[0]['dir']);
        $dateFrom     = trim($this->input->post('dateFrom'));
        $dateTo       = trim($this->input->post('dateTo'));

        $payment_type = $this->enc->decode($this->input->post('payment_type'));
        $channel      = $this->enc->decode($this->input->post('channel'));
        $cari         = $this->input->post('cari');
        $searchName   = $this->input->post('searchName');
        $searchNameDate   = $this->input->post('searchNameDate');
        $ilike        = trim(strtoupper($this->dbView->escape_like_str($cari)));
        $like         = trim(strtoupper($this->dbView->escape_like_str($cari)));
        $iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

        $merchant = $this->enc->decode($this->input->post('merchant'));
        $outletId = $this->input->post('outletId');

        $appIndentity = $this->appIdentity();

        if ($appIdentity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->session->userdata('port_id');
            } else {
                $port = $this->enc->decode($this->input->post('port'));
            }
        } else {
            $port = $port = $appIndentity;
        }

        // print_r($searchNameDate);exit;

        $field = [
            0  => 'B.id',
            1  => 'ticket_number',
            2  => 'booking_code',
            3  => 'depart_date',
            4  => 'customer',
            5  => 'id_number',
            6  => 'origin',
            7  => 'ship_class',
            8  => 'payment_type',
            9  => 'channel',
            10 => 'fare',
            11 => 'pemesanan',
            12 => 'merchant_name',
            13 => 'merchant_id',
            14 => 'pembayaran',
            15 => 'cetak_boarding',
            16 => 'gate_in',
            17 => 'validasi',
        ];

        $order_column = $field[$order_column];

        $newDateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));

        


        if ($searchNameDate == 'createdBooking') {
            $where .= " WHERE B.status != -5 AND BP.service_id = 1 and B.created_on>='" . $dateFrom . "' and B.created_on<'" . $newDateTo . "' ";
        } else if($searchNameDate == 'createdPayment') {
            $where .= " WHERE B.status != -5 AND BP.service_id = 1 and PY.created_on>='" . $dateFrom . "' and PY.created_on<'" . $newDateTo . "' ";
        } else if($searchNameDate == 'createdCheckin') {
            $where .= " WHERE B.status != -5 AND BP.service_id = 1 and CI.created_on>='" . $dateFrom . "' and CI.created_on<'" . $newDateTo . "' ";
        } else if($searchNameDate == 'createdBoarding') {
            $where .= " WHERE B.status != -5 AND BP.service_id = 1 and BRP.created_on>='" . $dateFrom . "' and BRP.created_on<'" . $newDateTo . "' ";
        } else {
            $where .= " WHERE B.status != -5 AND BP.service_id = 1 and B.created_on>='" . $dateFrom . "' and B.created_on<'" . $newDateTo . "' ";
        }

        if (!empty($port)) {
            $where .= " and (B.origin='" . $port . "')";
        }

        if (!empty($payment_type)) {
            $where .= " and (PY.payment_type='" . $payment_type . "')";
        }

        if (!empty($channel)) {
            $where .= " and (tti.channel='" . $channel . "')";
        }

        if(!empty($merchant))
        {
            $where .= " and b.created_by='" . $merchant . "'";
        }

        if(!empty($outletId))
        {
            $where .= " and tti.outlet_id='" . $outletId . "'";
        }        

        if (!empty($cari)) {

            if ($searchName == 'bookingCode') {
                $where .= " and (B.booking_code = '" . $like . "') ";
            } else if ($searchName == 'noIdentitas') {
                $where .= " and ( BP.id_number ilike '%" . $like . "%') ";
            } else if ($searchName == 'ticketNumber') {
                $where .= " and ( BP.ticket_number ilike '%" . $like . "%') ";
            } else if ($searchName == 'customerName') {
                $where .= " and ( BP.name ilike '%" . $like . "%') ";
            } else if ($searchName == 'noInvoice') {
                $where .= " and ( tti.trans_number = '" . $like . "') ";
            } else if ($searchName == 'createdBooking') {
                $where .= " and ( B.created_on = '" . $like . "') ";
            // } else if ($searchName == 'noInvoice') {
            //     $where .= " and ( tti.trans_number = '" . $like . "') ";
            // } else if ($searchName == 'noInvoice') {
            //     $where .= " and ( tti.trans_number = '" . $like . "') ";
            // } else if ($searchName == 'noInvoice') {
                $where .= " and ( tti.trans_number = '" . $like . "') ";
            } else {
                $where .= "";
            }
        }



        $sql = "SELECT
                    B.id,
                    B.origin,
                    B.ship_class,
                    BP.ticket_number,
                    BP.booking_code,
                    BP.depart_date,
                    BP.depart_time_start,
                    BP.name as customer,
                    BP.id_number,
                    PY.payment_type,
                    tti.channel,
                    BP.fare,
                    B.status as pemesanan,
                    PY.status as pembayaran,
                    GI.status as gate_in,
                    BRP.status as validasi,
                    CI.status as cetak_boarding,
                    B.created_on as pemesanan_date,
                    PY.created_on as pembayaran_date,
                    GI.created_on as gate_in_date,
                    BRP.created_on as validasi_date,
                    CI.created_on as cetak_boarding_date,
                    tti.trans_number,
                    tti.created_by as created_by_invoice,
                    tti.outlet_id 
                FROM app.t_trx_booking B
                JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
                LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
                LEFT JOIN app.t_trx_gate_in GI ON GI.ticket_number = BP.ticket_number
                LEFT JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
                LEFT JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
                left join app.t_trx_invoice tti on B.trans_number = tti.trans_number
                -- left join app.t_mtr_outlet_merchant tmom on tti.outlet_id = tmom.outlet_id 

		    {$where}";

        $queryCount = $this->dbView->query(

            "SELECT  count(B.id) as countdata
							FROM app.t_trx_booking B
                        JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
                        LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
                        LEFT JOIN app.t_trx_gate_in GI ON GI.ticket_number = BP.ticket_number
                        LEFT JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
                        LEFT JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
                        left join app.t_trx_invoice tti on B.trans_number = tti.trans_number
                        -- left join app.t_mtr_outlet_merchant tmom on tti.outlet_id = tmom.outlet_id 
					{$where}
								"
        )->row();

        $records_total = (int) $queryCount->countdata;

        $sql .= " ORDER BY " . $order_column . " {$order_dir}";
        if ($length != -1) {
            $sql .= " LIMIT {$length} OFFSET {$start}";
        }

        $query = $this->dbView->query($sql);

        $rows_data = $query->result();


        $outletId="";
        if($rows_data)
        {
            $unique = array_unique(array_filter(array_column($rows_data, 'outlet_id')));

            $getDataOutlet =[];
            foreach ($unique as $unique2) {
                $getDataOutlet[]="'".$unique2."'";
            }
            $outletId = implode(", ",$getDataOutlet) ;
        }
        
        $dataOutlet = array();
        if(!empty($outletId))
        {
            $dataOutlet = $this->db->query("
                                        select a.merchant_id, a.outlet_id, b.merchant_name
                                        from app.t_mtr_outlet_merchant a 
                                        join app.t_mtr_merchant b on a.merchant_id = b.merchant_id
                                        where outlet_id in (".$outletId.")
                                    ")->result();
        }
        

        $getColumn = array_column($dataOutlet, 'outlet_id');
        // print_r($getColumn); exit;
        $rows = [];
        $i    = ($start + 1);


        $masterPort = $this->getDataMaster("app.t_mtr_port","id","name");
        $masterShipClass = $this->getDataMaster("app.t_mtr_ship_class","id","name");
        $masterMerchant = $this->getDataMaster("app.t_mtr_merchant","merchant_id","merchant_name");

        // print_r($masterMerchant); exit;
        foreach ($rows_data as $row) {
            $row->number       = $i;
            $row->depart_date  = format_date($row->depart_date) . ' ' . format_time($row->depart_time_start);
            $row->fare         = idr_currency($row->fare);
            $row->ship_class   = strtoupper($row->ship_class);
            $row->payment_type = strtoupper(str_replace('-', ' ', $row->payment_type));
            $row->channel      = strtoupper(str_replace('_', ' ', $row->channel));

            $url                = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
            $row->ticket_number = '<a href="' . $url . '" target="_blank">' . $row->ticket_number . '</a>';

            $row->id_number != 0 ? $row->id_number = $row->id_number : $row->id_number = '-';

            $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '<i class="fa fa-exclamation" style="color:red">';
            $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '<i class="fa fa-exclamation" style="color:red">';
            $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '<i class="fa fa-exclamation" style="color:red">';
            $row->gate_in == 1 ? $row->gate_in               = format_dateTimeHis($row->gate_in_date) : $row->gate_in               = '<i class="fa fa-exclamation" style="color:red">';
            $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '<i class="fa fa-exclamation" style="color:red">';

            $row->origin = $masterPort[$row->origin];
            $row->ship_class = $masterShipClass[$row->ship_class];
            // $row->merchant_name = $masterMerchant[$row->merchant_id];

            // $key = array_search($row->outlet_id , $getColumn);             
            // $row->merchant_name =empty($row->outlet_id)?"":$dataOutlet[$key]->merchant_name ;
            
            $row->merchant_name = "";
            If($row->channel=="B2B" || $row->channel=="b2b"  )
            {
                $row->merchant_name = $masterMerchant[$row->created_by_invoice];
            }

            $rows[] = $row;
            // unset($row->id);

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

    public function listKendaraan() {
        $start        = $this->input->post('start');
        $length       = $this->input->post('length');
        $draw         = $this->input->post('draw');
        $search       = $this->input->post('search');
        $order        = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir    = strtoupper($order[0]['dir']);
        $dateFrom     = trim($this->input->post('dateFrom'));
        $dateTo       = trim($this->input->post('dateTo'));
        // $port = $this->enc->decode($this->input->post('port'));
        $payment_type = $this->enc->decode($this->input->post('payment_type'));
        $channel      = $this->enc->decode($this->input->post('channel'));
        $cari         = $this->input->post('cari');
        $like         = trim(strtoupper($this->dbView->escape_like_str($cari)));
        $searchName   = $this->input->post('searchName');
        $searchNameDate   = $this->input->post('searchNameDate');
        $ilike        = trim(strtoupper($this->dbView->escape_like_str($cari)));
        // $iLike = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

        $merchant = $this->enc->decode($this->input->post('merchant'));
        $outletId = $this->input->post('outletId');

        $appIndentity = $this->appIdentity();

        // $searchName

        if ($appIdentity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->session->userdata('port_id');
            } else {
                $port = $this->enc->decode($this->input->post('port'));
            }
        } else {
            $port = $port = $appIndentity;
        }

        $field = [
            0  => 'id',
            1  => 'ticket_number',
            2  => 'booking_code',
            3  => 'depart_date',
            4  => 'customer',
            5  => 'nik',
            6  => 'plat',
            7  => 'origin',
            8  => 'ship_class',
            9  => 'vehicle_class',
            10 => 'payment_type',
            11 => 'channel',
            12 => 'fare',
            13 => 'pemesanan',
            14 =>'merchant_name',
            15 =>'outlet_id',
            16 => 'pembayaran',
            17 => 'cetak_boarding',
            18 => 'validasi',
            19 => 'height',
            20 => 'length',
            21 => 'width',
            22 => 'weight',
            23 => 'height_cam',
            24 => 'length_cam',
            25 => 'width_cam',
            26 => 'weighbridge',
        ];

        $order_column = $field[$order_column];
        $newDateTo    = date('Y-m-d', strtotime($dateTo . ' +1 day'));

       

        if ($searchNameDate == 'createdBooking') {
            $where = " WHERE B.status != -5 AND BV.service_id = 2 and B.created_on>='" . $dateFrom . "' and B.created_on<'" . $newDateTo . "' ";
        } else if($searchNameDate == 'createdPayment') {
            $where .= " WHERE B.status != -5 AND BV.service_id = 2 and PY.created_on>='" . $dateFrom . "' and PY.created_on<'" . $newDateTo . "' ";
        } else if($searchNameDate == 'createdCheckin') {
            $where .= " WHERE B.status != -5 AND BV.service_id = 2 and CI.created_on>='" . $dateFrom . "' and CI.created_on<'" . $newDateTo . "' ";
        } else if($searchNameDate == 'createdBoarding') {
            $where .= " WHERE B.status != -5 AND BV.service_id = 2 and BRV.created_on>='" . $dateFrom . "' and BRV.created_on<'" . $newDateTo . "' ";
        } else {
            $where = " WHERE B.status != -5 AND BV.service_id = 2 and B.created_on>='" . $dateFrom . "' and B.created_on<'" . $newDateTo . "' ";
        }

        $whereData = " where data.id is not null ";

        if (!empty($port)) {
            $where .= " and (B.origin='" . $port . "')";
        }

        if (!empty($payment_type)) {
            $where .= " and (PY.payment_type='" . $payment_type . "')";
        }

        if (!empty($channel)) {
            $where .= " and (tti.channel='" . $channel . "')";
        }

        if(!empty($merchant))
        {
            $where .= " and b.created_by='" . $merchant . "'";
        }

        if(!empty($outletId))
        {
            $where .= " and tti.outlet_id='" . $outletId . "'";
        }                

        if (!empty($cari)) {
            if ($searchName == 'bookingCode') {
                $where .= " and (B.booking_code ilike '" . $like . "') ";
            } else if ($searchName == 'noIdentitas') {
                $whereData .= " and ( data.nik ilike '%" . $like . "%') ";
            } else if ($searchName == 'platNo') {
                $where .= " and ( BV.id_number ilike '%" . $like . "%') ";
            } else if ($searchName == 'ticketNumber') {
                $where .= " and ( BV.ticket_number ilike '%" . $like . "%') ";
            } else if ($searchName == 'customerName') {
                $whereData .= " and ( data.customer ilike '%" . $like . "%') ";
            }else if ($searchName == 'noInvoice') {
                $where .= " and ( tti.trans_number = '" . $like . "') ";
            } else if ($searchName == 'createdBooking') {
                $where .= " and ( B.created_on = '" . $like . "') ";
            }else {
                $where .= "";
            }
        }

        if (!empty($search['value'])) {
            $where .= " and (B.booking_code ilike '%" . $iLike . "%')";
        }

        $sql = "
				SELECT * from  (
					select
					B.id,
                    B.origin,
                    B.ship_class,
					BV.ticket_number,
					BV.booking_code,
					BV.depart_date,
					BV.depart_time_start,
					(
						select name from app.t_trx_booking_passanger
						where
							booking_code=B.booking_code and status<>'-5' and service_id=2
						order by
						ticket_number asc limit 1) as customer
					,
					(
						select id_number from app.t_trx_booking_passanger
						where
							booking_code=B.booking_code and status<>'-5' and service_id=2
						order by
						ticket_number asc limit 1) as nik,
					BV.id_number as plat,
                    BV.vehicle_class_id,
					PY.payment_type,
					tti.channel,
					BV.fare,
					B.status as pemesanan,
					PY.status as pembayaran,
					BRV.status as validasi,
					CI.status as cetak_boarding,
					B.created_on as pemesanan_date,
					PY.created_on as pembayaran_date,
					BRV.created_on as validasi_date,
					CI.created_on as cetak_boarding_date,
					CI.height, CI.length, CI.weight, CI.width, CI.height_cam, CI.length_cam, CI.weighbridge, CI.width_cam,
                    tti.trans_number,
                    tti.created_by as created_by_invoice,
                    tti.outlet_id 
				FROM app.t_trx_booking B
				JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
				LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
				LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
				LEFT JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
                left join app.t_trx_invoice tti on B.trans_number = tti.trans_number 
				{$where}
				) data {$whereData}
			";

        // die($sql); exit;

        $queryCount = $this->dbView->query("
					SELECT count(data.id) AS countdata
					from  (
						select
						B.id,
						(
							select name from app.t_trx_booking_passanger
							where
								booking_code=B.booking_code and status<>'-5' and service_id=2
							order by
							ticket_number asc limit 1) as customer
						,
						(
							select id_number from app.t_trx_booking_passanger
							where
								booking_code=B.booking_code and status<>'-5' and service_id=2
							order by
							ticket_number asc limit 1) as nik
                        FROM app.t_trx_booking B
                        JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
                        LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
                        LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
                        LEFT JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
                        left join app.t_trx_invoice tti on B.trans_number = tti.trans_number 

					{$where}
				) data
				{$whereData} ")->row();
        // print_r($queryCount); exit;

        $records_total = (int) $queryCount->countdata;

        $sql .= " ORDER BY " . $order_column . " {$order_dir}";
        if ($length != -1) {
            $sql .= " LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->dbView->query($sql);
        $rows_data = $query->result();

        $outletId="";
        if($rows_data)
        {
            $unique = array_unique(array_filter(array_column($rows_data, 'outlet_id')));

            $getDataOutlet =[];
            foreach ($unique as $unique2) {
                $getDataOutlet[]="'".$unique2."'";
            }
            $outletId = implode(", ",$getDataOutlet) ;
        }
        
        $dataOutlet = array();
        if(!empty($outletId))
        {
            $dataOutlet = $this->db->query("
                                        select a.merchant_id, a.outlet_id, b.merchant_name
                                        from app.t_mtr_outlet_merchant a 
                                        join app.t_mtr_merchant b on a.merchant_id = b.merchant_id
                                        where outlet_id in (".$outletId.")
                                    ")->result();
        }        

        $getColumn = array_column($dataOutlet, 'outlet_id');

        $rows = [];
        $i    = ($start + 1);

        $masterPort = $this->getDataMaster("app.t_mtr_port","id","name");
        $masterShipClass = $this->getDataMaster("app.t_mtr_ship_class","id","name");
        $masterMerchant = $this->getDataMaster("app.t_mtr_merchant","merchant_id","merchant_name");
        $masterVehicleClass = $this->getDataMaster("app.t_mtr_vehicle_class","id","name");

        // print_r($masterMerchant); exit;

        foreach ($rows_data as $row) {
            $row->number       = $i;
            $row->depart_date  = format_date($row->depart_date) . ' ' . format_time($row->depart_time_start);
            $row->fare         = idr_currency($row->fare);
            $row->ship_class   = strtoupper($row->ship_class);
            $row->payment_type = strtoupper(str_replace('-', ' ', $row->payment_type));
            $row->channel      = strtoupper(str_replace('_', ' ', $row->channel));

            $url                = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
            $row->ticket_number = '<a href="' . $url . '" target="_blank">' . $row->ticket_number . '</a>';

            $row->id_number != 0 ? $row->id_number = $row->id_number : $row->id_number = '-';

            $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '<i class="fa fa-exclamation" style="color:red">';
            $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '<i class="fa fa-exclamation" style="color:red">';
            $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '<i class="fa fa-exclamation" style="color:red">';
            $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '<i class="fa fa-exclamation" style="color:red">';

            $row->origin = $masterPort[$row->origin];
            $row->ship_class = $masterShipClass[$row->ship_class];
            // $row->merchant_name = $masterMerchant[$row->merchant_id];
            $row->vehicle_class =$masterVehicleClass[$row->vehicle_class_id];

            // $key = array_search($row->outlet_id , $getColumn); 
            // $row->merchant_name =empty($row->outlet_id)?"":$dataOutlet[$key]->merchant_name ;

            $row->merchant_name = " ";
            If($row->channel=="B2B" || $row->channel=="b2b"  )
            {
                $row->merchant_name = $masterMerchant[$row->created_by_invoice];
                // $row->merchant_name = "ini b2b";
            }            

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

    public function listPenumpang_28032023() {
        $start        = $this->input->post('start');
        $length       = $this->input->post('length');
        $draw         = $this->input->post('draw');
        $search       = $this->input->post('search');
        $order        = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir    = strtoupper($order[0]['dir']);
        $dateFrom     = trim($this->input->post('dateFrom'));
        $dateTo       = trim($this->input->post('dateTo'));

        $payment_type = $this->enc->decode($this->input->post('payment_type'));
        $channel      = $this->enc->decode($this->input->post('channel'));
        $cari         = $this->input->post('cari');
        $searchName   = $this->input->post('searchName');
        $ilike        = trim(strtoupper($this->dbView->escape_like_str($cari)));
        $like         = trim(strtoupper($this->dbView->escape_like_str($cari)));
        $iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

        $merchant = $this->enc->decode($this->input->post('merchant'));
        $outletId = $this->input->post('outletId');

        $appIndentity = $this->appIdentity();

        if ($appIdentity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->session->userdata('port_id');
            } else {
                $port = $this->enc->decode($this->input->post('port'));
            }
        } else {
            $port = $port = $appIndentity;
        }

        $field = [
            0  => 'B.id',
            1  => 'ticket_number',
            2  => 'booking_code',
            3  => 'depart_date',
            4  => 'customer',
            5  => 'id_number',
            6  => 'origin',
            7  => 'ship_class',
            8  => 'payment_type',
            9  => 'channel',
            10 => 'fare',
            11 => 'pemesanan',
            12 =>'merchant_name',
            13 =>'merchant_id',
            14 => 'pembayaran',
            15 => 'cetak_boarding',
            16 => 'gate_in',
            17 => 'validasi',
        ];

        $order_column = $field[$order_column];

        $newDateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
        $where = " WHERE B.status != -5 AND BP.service_id = 1 and B.created_on>='" . $dateFrom . "' and B.created_on<'" . $newDateTo . "' ";

        if (!empty($port)) {
            $where .= " and (B.origin='" . $port . "')";
        }

        if (!empty($payment_type)) {
            $where .= " and (PY.payment_type='" . $payment_type . "')";
        }

        if (!empty($channel)) {
            $where .= " and (tti.channel='" . $channel . "')";
        }

        if(!empty($merchant))
        {
            $where .= " and tmm.merchant_id='" . $merchant . "'";
        }

        if(!empty($outletId))
        {
            $where .= " and tti.outlet_id='" . $outletId . "'";
        }        

        if (!empty($cari)) {

            if ($searchName == 'bookingCode') {
                $where .= " and (B.booking_code ilike '%" . $like . "%') ";
            } else if ($searchName == 'noIdentitas') {
                $where .= " and ( BP.id_number ilike '%" . $like . "%') ";
            } else if ($searchName == 'ticketNumber') {
                $where .= " and ( BP.ticket_number ilike '%" . $like . "%') ";
            } else if ($searchName == 'customerName') {
                $where .= " and ( BP.name ilike '%" . $like . "%') ";
            } else {
                $where .= "";
            }
        }



        $sql = "SELECT
                    B.id,
                    BP.ticket_number,
                    BP.booking_code,
                    BP.depart_date,
                    BP.depart_time_start,
                    BP.name as customer,
                    BP.id_number,
                    P.name as origin,
                    SC.name as ship_class,
                    PY.payment_type,
                    tti.channel,
                    BP.fare,
                    B.status as pemesanan,
                    PY.status as pembayaran,
                    GI.status as gate_in,
                    BRP.status as validasi,
                    CI.status as cetak_boarding,
                    B.created_on as pemesanan_date,
                    PY.created_on as pembayaran_date,
                    GI.created_on as gate_in_date,
                    BRP.created_on as validasi_date,
                    CI.created_on as cetak_boarding_date,
                    tti.outlet_id ,
                    tmm.merchant_name 
                FROM app.t_trx_booking B
                JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
                JOIN app.t_mtr_port P ON P.id = B.origin
                JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
                LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
                LEFT JOIN app.t_trx_gate_in GI ON GI.ticket_number = BP.ticket_number
                LEFT JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
                LEFT JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
                left join app.t_trx_invoice tti on B.trans_number = tti.trans_number 
                left join app.t_mtr_outlet_merchant tmom on tti.outlet_id = tmom.outlet_id 
                left join app.t_mtr_merchant tmm on tmom.merchant_id =tmm.merchant_id 
		    {$where}";

     
        $queryCount = $this->dbView->query(

            "SELECT  count(B.id) as countdata
							FROM app.t_trx_booking B
							JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
							JOIN app.t_mtr_port P ON P.id = B.origin
							JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
							LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
							LEFT JOIN app.t_trx_gate_in GI ON GI.ticket_number = BP.ticket_number
							LEFT JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
							LEFT JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
                            left join app.t_trx_invoice tti on B.trans_number = tti.trans_number 
                            left join app.t_mtr_outlet_merchant tmom on tti.outlet_id = tmom.outlet_id 
                            left join app.t_mtr_merchant tmm on tmom.merchant_id =tmm.merchant_id 
					{$where}
								"
        )->row();

        $records_total = (int) $queryCount->countdata;

        $sql .= " ORDER BY " . $order_column . " {$order_dir}";
        if ($length != -1) {
            $sql .= " LIMIT {$length} OFFSET {$start}";
        }

        $query = $this->dbView->query($sql);

        $rows_data = $query->result();

        $rows = [];
        $i    = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number       = $i;
            $row->depart_date  = format_date($row->depart_date) . ' ' . format_time($row->depart_time_start);
            $row->fare         = idr_currency($row->fare);
            $row->ship_class   = strtoupper($row->ship_class);
            $row->payment_type = strtoupper(str_replace('-', ' ', $row->payment_type));
            $row->channel      = strtoupper(str_replace('_', ' ', $row->channel));

            $url                = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
            $row->ticket_number = '<a href="' . $url . '" target="_blank">' . $row->ticket_number . '</a>';

            $row->id_number != 0 ? $row->id_number = $row->id_number : $row->id_number = '-';

            $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '<i class="fa fa-exclamation" style="color:red">';
            $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '<i class="fa fa-exclamation" style="color:red">';
            $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '<i class="fa fa-exclamation" style="color:red">';
            $row->gate_in == 1 ? $row->gate_in               = format_dateTimeHis($row->gate_in_date) : $row->gate_in               = '<i class="fa fa-exclamation" style="color:red">';
            $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '<i class="fa fa-exclamation" style="color:red">';

            $rows[] = $row;
            // unset($row->id);

            $i++;
        }

        return [
            'draw'            => $draw,
            'recordsTotal'    => $records_total,
            'recordsFiltered' => $records_total,
            'data'            => $rows,
        ];
    }

    public function listKendaraan_02122020() {
        $start        = $this->input->post('start');
        $length       = $this->input->post('length');
        $draw         = $this->input->post('draw');
        $search       = $this->input->post('search');
        $order        = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir    = strtoupper($order[0]['dir']);
        $dateFrom     = trim($this->input->post('dateFrom'));
        $dateTo       = trim($this->input->post('dateTo'));
        // $port = $this->enc->decode($this->input->post('port'));
        $payment_type = $this->enc->decode($this->input->post('payment_type'));
        $channel      = $this->enc->decode($this->input->post('channel'));
        $cari         = $this->input->post('cari');
        $like         = trim(strtoupper($this->dbView->escape_like_str($cari)));
        $searchName   = $this->input->post('searchName');
        $ilike        = trim(strtoupper($this->dbView->escape_like_str($cari)));
        // $iLike = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

        $appIndentity = $this->appIdentity();

        if ($appIdentity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->session->userdata('port_id');
            } else {
                $port = $this->enc->decode($this->input->post('port'));
            }
        } else {
            $port = $port = $appIndentity;
        }

        $field = [
            0  => 'B.id',
            1  => 'ticket_number',
            2  => 'booking_code',
            3  => 'depart_date',
            4  => 'customer',
            5  => 'nik',
            6  => 'plat',
            7  => 'origin',
            8  => 'ship_class',
            9  => 'vehicle_class',
            10 => 'payment_type',
            11 => 'channel',
            12 => 'fare',
            13 => 'pemesanan',
            14 => 'pembayaran',
            15 => 'cetak_boarding',
            16 => 'validasi',
        ];

        $order_column = $field[$order_column];
        $newDateTo    = date('Y-m-d', strtotime($dateTo . ' +1 day'));

        // $where = " WHERE B.status != -5 AND BV.service_id = 2 AND BP.id IN(SELECT MIN(BP2.id) FROM app.t_trx_booking_passanger BP2 WHERE BP2.booking_code=BP.booking_code)";

        // 13/10/2020
        // $where = " WHERE B.status != -5 AND BV.service_id = 2 and (B.created_on::date between '".$dateFrom."' and '".$dateTo."' )";

        $where = " WHERE B.status != -5 AND BV.service_id = 2 and B.created_on>='" . $dateFrom . "' and B.created_on<'" . $newDateTo . "' ";

        $whereSub = $where;

        if (!empty($port)) {
            $where .= " and (B.origin='" . $port . "')";
        }

        if (!empty($payment_type)) {
            $where .= " and (PY.payment_type='" . $payment_type . "')";
        }

        if (!empty($channel)) {
            $where .= " and (tti.channel='" . $channel . "')";
        }

        // if (!empty($dateTo) and !empty($dateFrom))
        // {
        //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
        // }
        // else if(empty($dateFrom) and !empty($dateTo))
        // {
        //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";
        // }
        // else if (!empty($dateFrom) and empty($dateTo))
        // {
        //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";
        // }
        // else
        // {
        //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";
        // }

        // if (!empty($dateTo) and !empty($dateFrom))
        // {
        //     $where .=" and (B.created_on::date between '".$dateFrom."' and '".$dateTo."' )";
        // }
        // else if(empty($dateFrom) and !empty($dateTo))
        // {
        //     $where .=" and (B.created_on::date between '".$dateTo."' and '".$dateTo."' )";
        // }
        // else if (!empty($dateFrom) and empty($dateTo))
        // {
        //     $where .=" and (B.created_on::date between '".$dateFrom."' and '".$dateFrom."' )";
        // }
        // else
        // {
        //     $where .=" and (B.created_on::date '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";
        // }

        if (!empty($cari)) {
            // $where .=" and (UPPER(B.booking_code) ilike '%".$like."%' or UPPER(BP.name) ilike '%".$like."%' or BP.id_number ilike '%".$like."%' or BV.id_number ilike '%".$like."%' or UPPER(BV.ticket_number) ilike '%".$like."%')";

            if ($searchName == 'bookingCode') {
                $where .= " and (B.booking_code ilike '%" . $like . "%') ";
            } else if ($searchName == 'noIdentitas') {
                $where .= " and ( BP.id_number ilike '%" . $like . "%') ";
            } else if ($searchName == 'platNo') {
                $where .= " and ( BV.id_number ilike '%" . $like . "%') ";
            } else if ($searchName == 'ticketNumber') {
                $where .= " and ( BP.ticket_number ilike '%" . $like . "%') ";
            } else if ($searchName == 'customerName') {
                $where .= " and ( BP.name ilike '%" . $like . "%') ";
            } else {
                $where .= "";
            }
        }

        if (!empty($search['value'])) {
            $where .= " and (B.booking_code ilike '%" . $iLike . "%')";
        }

        // $sql = "SELECT DISTINCT B.id, BV.ticket_number, BV.booking_code, BV.depart_date, BV.depart_time_start, BP.name as customer, BP.id_number as nik, BV.id_number as plat, P.name as origin, SC.name as ship_class, VC.name as vehicle_class, PY.payment_type, tti.channel, BV.fare, B.status as pemesanan, PY.status as pembayaran, BRV.status as validasi, CI.status as cetak_boarding, B.created_on as pemesanan_date, PY.created_on as pembayaran_date, BRV.created_on as validasi_date, CI.created_on as cetak_boarding_date
        //         FROM app.t_trx_booking B
        //         JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
        //         JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
        //         JOIN app.t_mtr_port P ON P.id = B.origin
        //         JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
        //         LEFT JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
        //         LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
        //         LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
        //         LEFT JOIN app.t_trx_check_in_vehicle CI ON CI.booking_code = B.booking_code
        // {$where}";

        $sql = "SELECT
					B.id,
					BV.ticket_number,
					BV.booking_code,
					BV.depart_date,
					BV.depart_time_start,
					BP.name as customer,
					BP.id_number as nik,
					BV.id_number as plat,
					P.name as origin,
					SC.name as ship_class,
					VC.name as vehicle_class,
					PY.payment_type,
					tti.channel,
					BV.fare,
					B.status as pemesanan,
					PY.status as pembayaran,
					BRV.status as validasi,
					CI.status as cetak_boarding,
					B.created_on as pemesanan_date,
					PY.created_on as pembayaran_date,
					BRV.created_on as validasi_date,
					CI.created_on as cetak_boarding_date
				FROM app.t_trx_booking B
				JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
				JOIN app.t_trx_booking_passanger BP ON  BP.booking_code = B.booking_code
				JOIN (
					select min(BP.ticket_number) as ticket_number, B.booking_code
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
					{$whereSub}
					group by B.booking_code
				) PS  on PS.ticket_number = BP.ticket_number
				JOIN app.t_mtr_port P ON P.id = B.origin
				JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
				LEFT JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
				LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
				LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
				LEFT JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
			{$where}";

        // die($sql); exit;
        // $query         = $this->dbView->query($sql);
        // $records_total = $query->num_rows();

        $queryCount = $this->dbView->query(

            "SELECT  COUNT(B.id) AS countdata
								FROM app.t_trx_booking B
								JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
								JOIN app.t_trx_booking_passanger BP ON  BP.booking_code = B.booking_code
								JOIN (
									select min(BP.ticket_number) as ticket_number, B.booking_code
									FROM app.t_trx_booking B
									JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
									JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
									{$whereSub}
									group by B.booking_code
								) PS  on PS.ticket_number = BP.ticket_number
								JOIN app.t_mtr_port P ON P.id = B.origin
								JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
								LEFT JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
								LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
								LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
								LEFT JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
							{$where}"
        )->row();

        $records_total = (int) $queryCount->countdata;

        $sql .= " ORDER BY " . $order_column . " {$order_dir}";
        if ($length != -1) {
            $sql .= " LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->dbView->query($sql);
        $rows_data = $query->result();

        $rows = [];
        $i    = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number       = $i;
            $row->depart_date  = format_date($row->depart_date) . ' ' . format_time($row->depart_time_start);
            $row->fare         = idr_currency($row->fare);
            $row->ship_class   = strtoupper($row->ship_class);
            $row->payment_type = strtoupper(str_replace('-', ' ', $row->payment_type));
            $row->channel      = strtoupper(str_replace('_', ' ', $row->channel));

            $url                = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
            $row->ticket_number = '<a href="' . $url . '" target="_blank">' . $row->ticket_number . '</a>';

            $row->id_number != 0 ? $row->id_number = $row->id_number : $row->id_number = '-';

            $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '<i class="fa fa-exclamation" style="color:red">';
            $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '<i class="fa fa-exclamation" style="color:red">';
            $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '<i class="fa fa-exclamation" style="color:red">';
            $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '<i class="fa fa-exclamation" style="color:red">';

            $rows[] = $row;
            unset($row->id);

            $i++;
        }

        return [
            'draw'            => $draw,
            'recordsTotal'    => $records_total,
            'recordsFiltered' => $records_total,
            'data'            => $rows,
        ];
    }

    public function listKendaraan_28032023() {
        $start        = $this->input->post('start');
        $length       = $this->input->post('length');
        $draw         = $this->input->post('draw');
        $search       = $this->input->post('search');
        $order        = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir    = strtoupper($order[0]['dir']);
        $dateFrom     = trim($this->input->post('dateFrom'));
        $dateTo       = trim($this->input->post('dateTo'));
        // $port = $this->enc->decode($this->input->post('port'));
        $payment_type = $this->enc->decode($this->input->post('payment_type'));
        $channel      = $this->enc->decode($this->input->post('channel'));
        $cari         = $this->input->post('cari');
        $like         = trim(strtoupper($this->dbView->escape_like_str($cari)));
        $searchName   = $this->input->post('searchName');
        $ilike        = trim(strtoupper($this->dbView->escape_like_str($cari)));
        // $iLike = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

        $merchant = $this->enc->decode($this->input->post('merchant'));
        $outletId = $this->input->post('outletId');

        $appIndentity = $this->appIdentity();

        // $searchName

        if ($appIdentity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->session->userdata('port_id');
            } else {
                $port = $this->enc->decode($this->input->post('port'));
            }
        } else {
            $port = $port = $appIndentity;
        }

        $field = [
            0  => 'id',
            1  => 'ticket_number',
            2  => 'booking_code',
            3  => 'depart_date',
            4  => 'customer',
            5  => 'nik',
            6  => 'plat',
            7  => 'origin',
            8  => 'ship_class',
            9  => 'vehicle_class',
            10 => 'payment_type',
            11 => 'channel',
            12 => 'fare',
            13 => 'pemesanan',
            14=>'merchant_name',
            15=>'outlet_id',
            16 => 'pembayaran',
            17 => 'cetak_boarding',
            18 => 'validasi',
            19 => 'height',
            20 => 'length',
            21 => 'width',
            22 => 'weight',
            23 => 'height_cam',
            24 => 'length_cam',
            25 => 'width_cam',
            26 => 'weighbridge',
        ];

        $order_column = $field[$order_column];
        $newDateTo    = date('Y-m-d', strtotime($dateTo . ' +1 day'));

        $where = " WHERE B.status != -5 AND BV.service_id = 2 and B.created_on>='" . $dateFrom . "' and B.created_on<'" . $newDateTo . "' ";

        $whereData = " where data.id is not null ";

        if (!empty($port)) {
            $where .= " and (B.origin='" . $port . "')";
        }

        if (!empty($payment_type)) {
            $where .= " and (PY.payment_type='" . $payment_type . "')";
        }

        if (!empty($channel)) {
            $where .= " and (tti.channel='" . $channel . "')";
        }

        if(!empty($merchant))
        {
            $where .= " and tmm.merchant_id='" . $merchant . "'";
        }

        if(!empty($outletId))
        {
            $where .= " and tti.outlet_id='" . $outletId . "'";
        }                

        if (!empty($cari)) {
            if ($searchName == 'bookingCode') {
                $where .= " and (B.booking_code ilike '%" . $like . "%') ";
            } else if ($searchName == 'noIdentitas') {
                $whereData .= " and ( data.nik ilike '%" . $like . "%') ";
            } else if ($searchName == 'platNo') {
                $where .= " and ( BV.id_number ilike '%" . $like . "%') ";
            } else if ($searchName == 'ticketNumber') {
                $where .= " and ( BV.ticket_number ilike '%" . $like . "%') ";
            } else if ($searchName == 'customerName') {
                $whereData .= " and ( data.customer ilike '%" . $like . "%') ";
            } else {
                $where .= "";
            }
        }

        if (!empty($search['value'])) {
            $where .= " and (B.booking_code ilike '%" . $iLike . "%')";
        }

        $sql = "
				SELECT * from  (
					select
					B.id,
					BV.ticket_number,
					BV.booking_code,
					BV.depart_date,
					BV.depart_time_start,
					(
						select name from app.t_trx_booking_passanger
						where
							booking_code=B.booking_code and status<>'-5' and service_id=2
						order by
						ticket_number asc limit 1) as customer
					,
					(
						select id_number from app.t_trx_booking_passanger
						where
							booking_code=B.booking_code and status<>'-5' and service_id=2
						order by
						ticket_number asc limit 1) as nik,
					BV.id_number as plat,
					P.name as origin,
					SC.name as ship_class,
					VC.name as vehicle_class,
					PY.payment_type,
					tti.channel,
					BV.fare,
					B.status as pemesanan,
					PY.status as pembayaran,
					BRV.status as validasi,
					CI.status as cetak_boarding,
					B.created_on as pemesanan_date,
					PY.created_on as pembayaran_date,
					BRV.created_on as validasi_date,
					CI.created_on as cetak_boarding_date,
					CI.height, CI.length, CI.weight, CI.width, CI.height_cam, CI.length_cam, CI.weighbridge, CI.width_cam,
                    tti.outlet_id ,
                    tmm.merchant_name
				FROM app.t_trx_booking B
				JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
				JOIN app.t_mtr_port P ON P.id = B.origin
				JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
				LEFT JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
				LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
				LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
				LEFT JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
                left join app.t_trx_invoice tti on B.trans_number = tti.trans_number 
                left join app.t_mtr_outlet_merchant tmom on tti.outlet_id = tmom.outlet_id 
                left join app.t_mtr_merchant tmm on tmom.merchant_id =tmm.merchant_id 
				{$where}
				) data {$whereData}
			";

        // die($sql); exit;

        $queryCount = $this->dbView->query("
					SELECT count(data.id) AS countdata
					from  (
						select
						B.id,
						(
							select name from app.t_trx_booking_passanger
							where
								booking_code=B.booking_code and status<>'-5' and service_id=2
							order by
							ticket_number asc limit 1) as customer
						,
						(
							select id_number from app.t_trx_booking_passanger
							where
								booking_code=B.booking_code and status<>'-5' and service_id=2
							order by
							ticket_number asc limit 1) as nik
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
					JOIN app.t_mtr_port P ON P.id = B.origin
					JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
					LEFT JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
					LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
					LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
					LEFT JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
                    left join app.t_trx_invoice tti on B.trans_number = tti.trans_number 
                    left join app.t_mtr_outlet_merchant tmom on tti.outlet_id = tmom.outlet_id 
                    left join app.t_mtr_merchant tmm on tmom.merchant_id =tmm.merchant_id 
					{$where}
				) data
				{$whereData} ")->row();

        $records_total = (int) $queryCount->countdata;

        $sql .= " ORDER BY " . $order_column . " {$order_dir}";
        if ($length != -1) {
            $sql .= " LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->dbView->query($sql);
        $rows_data = $query->result();

        $rows = [];
        $i    = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number       = $i;
            $row->depart_date  = format_date($row->depart_date) . ' ' . format_time($row->depart_time_start);
            $row->fare         = idr_currency($row->fare);
            $row->ship_class   = strtoupper($row->ship_class);
            $row->payment_type = strtoupper(str_replace('-', ' ', $row->payment_type));
            $row->channel      = strtoupper(str_replace('_', ' ', $row->channel));

            $url                = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
            $row->ticket_number = '<a href="' . $url . '" target="_blank">' . $row->ticket_number . '</a>';

            $row->id_number != 0 ? $row->id_number = $row->id_number : $row->id_number = '-';

            $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '<i class="fa fa-exclamation" style="color:red">';
            $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '<i class="fa fa-exclamation" style="color:red">';
            $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '<i class="fa fa-exclamation" style="color:red">';
            $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '<i class="fa fa-exclamation" style="color:red">';

            $rows[] = $row;
            unset($row->id);

            $i++;
        }

        return [
            'draw'            => $draw,
            'recordsTotal'    => $records_total,
            'recordsFiltered' => $records_total,
            'data'            => $rows,
        ];
    }

    function get_channel_old() {
        $data  = ['' => 'All'];
        $query = $this->dbView->query("SELECT DISTINCT channel FROM app.t_trx_payment ORDER BY channel")->result();

        foreach ($query as $key => $value) {
            $data[$this->enc->encode($value->channel)] = strtoupper(str_replace('_', ' ', $value->channel));
        }

        return $data;
    }

    function get_channel() {
        $data[""]                                  = 'All';
        $data[$this->enc->encode('b2b')]           = 'B2B';
        $data[$this->enc->encode('ifcs')]          = 'IFCS';
        $data[$this->enc->encode('mobile')]        = 'MOBILE';
        $data[$this->enc->encode('pos_passanger')] = 'POS PASSANGER';
        $data[$this->enc->encode('pos_vehicle')]   = 'POS VEHICLE';
        $data[$this->enc->encode('vm')]            = 'VM';
        $data[$this->enc->encode('web')]           = 'WEB';

        return $data;
    }

    function get_payment_type_old() {
        $data  = ['' => 'All'];
        $query = $this->dbView->query("SELECT DISTINCT payment_type FROM app.t_trx_payment ORDER BY payment_type")->result();

        foreach ($query as $key => $value) {
            $data[$this->enc->encode($value->payment_type)] = strtoupper(str_replace('-', ' ', $value->payment_type));
        }

        return $data;
    }

    function get_payment_type() {
        $data[""]                                    = 'All';
        $data[$this->enc->encode('b2b')]             = 'B2B';
        $data[$this->enc->encode('cash')]            = 'CASH';
        $data[$this->enc->encode('finpay')]          = 'FINPAY';
        $data[$this->enc->encode('prepaid-bni')]     = 'PREPAID BNI';
        $data[$this->enc->encode('prepaid-bri')]     = 'PREPAID BRI';
        $data[$this->enc->encode('prepaid-mandiri')] = 'PREPAID MANDIRI';
        $data[$this->enc->encode('reedem')]          = 'REEDEM';
        $data[$this->enc->encode('sab')]             = 'SAB';

        return $data;
    }

    public function list_data_02122020($port, $payment_type, $channel, $cari, $dateFrom, $dateTo, $type, $searchName) {
        $newDateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
        $like      = trim(strtoupper($this->dbView->escape_like_str($cari)));
        if ($type === 'penumpang') {
            // $where = " WHERE B.status != -5 AND BP.service_id = 1  and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";

            $where = " WHERE B.status != -5 AND BP.service_id = 1  and B.created_on >='" . $dateFrom . "' and B.created_on<'" . $newDateTo . "' ";

            if (!empty($port)) {
                $where .= " and (B.origin='" . $port . "')";
            }
            if (!empty($payment_type)) {
                $where .= " and (PY.payment_type='" . $payment_type . "')";
            }
            if (!empty($channel)) {
                $where .= " and (tti.channel='" . $channel . "')";
            }
            // if (!empty($dateTo) and !empty($dateFrom))
            // {
            //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
            // }
            // else if(empty($dateFrom) and !empty($dateTo))
            // {
            //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";
            // }
            // else if (!empty($dateFrom) and empty($dateTo))
            // {
            //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";
            // }
            // else
            // {
            //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";
            // }
            if (!empty($cari)) {
                // $where .=" and (UPPER(B.booking_code) ilike '%".$like."%' or UPPER(BP.name) ilike '%".$like."%' or BP.id_number ilike '%".$like."%' or UPPER(BP.ticket_number) ilike '%".$like."%') ";

                if ($searchName == 'bookingCode') {
                    $where .= " and (B.booking_code ilike '%" . $like . "%') ";
                } else if ($searchName == 'noIdentitas') {
                    $where .= " and ( BP.id_number ilike '%" . $like . "%') ";
                } else if ($searchName == 'ticketNumber') {
                    $where .= " and ( BP.ticket_number ilike '%" . $like . "%') ";
                } else if ($searchName == 'customerName') {
                    $where .= " and ( BP.name ilike '%" . $like . "%') ";
                } else {
                    $where .= "";
                }
            }

            // $sql = "SELECT DISTINCT B.id, BP.ticket_number, BP.booking_code, BP.depart_date, BP.depart_time_start, BP.name as customer, BP.id_number, P.name as origin, SC.name as ship_class, PY.payment_type, tti.channel, BP.fare, B.status as pemesanan, PY.status as pembayaran, GI.status as gate_in, BRP.status as validasi, CI.status as cetak_boarding, B.created_on as pemesanan_date, PY.created_on as pembayaran_date, GI.created_on as gate_in_date, BRP.created_on as validasi_date, CI.created_on as cetak_boarding_date
            //         FROM app.t_trx_booking B
            //         JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
            //         JOIN app.t_mtr_port P ON P.id = B.origin
            //         JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
            //         LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
            //         LEFT JOIN app.t_trx_gate_in GI ON GI.ticket_number = BP.ticket_number
            //         LEFT JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
            //         LEFT JOIN app.t_trx_check_in CI ON CI.booking_code = B.booking_code
            //         {$where}
            //         ORDER BY B.ID ASC";

            $sql = "SELECT
					B.id,
					BP.ticket_number,
					BP.booking_code,
					BP.depart_date,
					BP.depart_time_start,
					BP.name as customer,
					BP.id_number,
					P.name as origin,
					SC.name as ship_class,
					PY.payment_type,
					tti.channel,
					BP.fare,
					B.status as pemesanan,
					PY.status as pembayaran,
					GI.status as gate_in,
					BRP.status as validasi,
					CI.status as cetak_boarding,
					B.created_on as pemesanan_date,
					PY.created_on as pembayaran_date,
					GI.created_on as gate_in_date,
					BRP.created_on as validasi_date,
					CI.created_on as cetak_boarding_date
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
					JOIN app.t_mtr_port P ON P.id = B.origin
					JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
					LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
					LEFT JOIN app.t_trx_gate_in GI ON GI.ticket_number = BP.ticket_number
					LEFT JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
					LEFT JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
			{$where}";

            $check = $this->dbView->query($sql)->num_rows();

            if ($check > 0) {
                return $this->dbView->query($sql)->result();
            } else {
                return false;
            }
        }

        if ($type === 'kendaraan') {
            // $where = " WHERE B.status != -5 AND BV.service_id = 2 AND BP.id IN(SELECT MIN(BP2.id) FROM app.t_trx_booking_passanger BP2 WHERE BP2.booking_code=BP.booking_code)";

            // 13/10/2020
            // $where = " WHERE B.status != -5 AND BV.service_id = 2 and (B.created_on::date between '".$dateFrom."' and '".$dateTo."' ) ";

            $where = " WHERE B.status != -5 AND BV.service_id = 2 and B.created_on>= '" . $dateFrom . "' and B.created_on<'" . $newDateTo . "'  ";

            $whereSub = $where;

            if (!empty($port)) {
                $where .= " and (B.origin='" . $port . "')";
            }
            if (!empty($payment_type)) {
                $where .= " and (PY.payment_type='" . $payment_type . "')";
            }
            if (!empty($channel)) {
                $where .= " and (tti.channel='" . $channel . "')";
            }
            // if (!empty($dateTo) and !empty($dateFrom))
            // {
            //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
            // }
            // else if(empty($dateFrom) and !empty($dateTo))
            // {
            //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";
            // }
            // else if (!empty($dateFrom) and empty($dateTo))
            // {
            //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";
            // }
            // else
            // {
            //     $where .=" and (to_char(B.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";
            // }
            if (!empty($cari)) {
                // $where .=" and (UPPER(B.booking_code) ilike '%".$like."%' or UPPER(BP.name) ilike '%".$like."%' or BP.id_number ilike '%".$like."%' or BV.id_number ilike '%".$like."%' or UPPER(BV.ticket_number) ilike '%".$like."%')";

                if ($searchName == 'bookingCode') {
                    $where .= " and (B.booking_code ilike '%" . $like . "%') ";
                } else if ($searchName == 'noIdentitas') {
                    $where .= " and ( BP.id_number ilike '%" . $like . "%') ";
                } else if ($searchName == 'platNo') {
                    $where .= " and ( BV.id_number ilike '%" . $like . "%') ";
                } else if ($searchName == 'ticketNumber') {
                    $where .= " and ( BP.ticket_number ilike '%" . $like . "%') ";
                } else if ($searchName == 'customerName') {
                    $where .= " and ( BP.name ilike '%" . $like . "%') ";
                } else {
                    $where .= "";
                }
            }
            // $sql = "SELECT DISTINCT B.id, BV.ticket_number, BV.booking_code, BV.depart_date, BV.depart_time_start, BP.name as customer, BP.id_number as nik, BV.id_number as plat, P.name as origin, SC.name as ship_class, VC.name as vehicle_class, PY.payment_type, tti.channel, BV.fare, B.status as pemesanan, PY.status as pembayaran, BRV.status as validasi, CI.status as cetak_boarding, B.created_on as pemesanan_date, PY.created_on as pembayaran_date, BRV.created_on as validasi_date, CI.created_on as cetak_boarding_date
            //         FROM app.t_trx_booking B
            //         JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
            //         JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
            //         JOIN app.t_mtr_port P ON P.id = B.origin
            //         JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
            //         LEFT JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
            //         LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
            //         LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
            //         LEFT JOIN app.t_trx_check_in_vehicle CI ON CI.booking_code = B.booking_code
            //         {$where}
            //         ORDER BY B.ID ASC";

            $sql = "SELECT
						B.id,
						BV.ticket_number,
						BV.booking_code,
						BV.depart_date,
						BV.depart_time_start,
						BP.name as customer,
						BP.id_number as nik,
						BV.id_number as plat,
						P.name as origin,
						SC.name as ship_class,
						VC.name as vehicle_class,
						PY.payment_type,
						tti.channel,
						BV.fare,
						B.status as pemesanan,
						PY.status as pembayaran,
						BRV.status as validasi,
						CI.status as cetak_boarding,
						B.created_on as pemesanan_date,
						PY.created_on as pembayaran_date,
						BRV.created_on as validasi_date,
						CI.created_on as cetak_boarding_date
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
					JOIN app.t_trx_booking_passanger BP ON  BP.booking_code = B.booking_code
					-- JOIN (
					-- 	select min(BP.ticket_number) as ticket_number, B.booking_code
					-- 	FROM app.t_trx_booking B
					-- 	JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
					-- 	JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
					-- 	{$whereSub}
					-- 	group by B.booking_code
					-- ) PS  on PS.ticket_number = BP.ticket_number
					JOIN app.t_mtr_port P ON P.id = B.origin
					JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
					LEFT JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
					LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
					LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
					LEFT JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
				{$where} ORDER BY B.ID ASC";

            $check = $this->dbView->query($sql)->num_rows();

            if ($check > 0) {
                return $this->dbView->query($sql)->result();
            } else {
                return false;
            }
        }
    }

    public function list_data($port, $payment_type, $channel, $cari, $dateFrom, $dateTo, $type, $searchName, $merchant, $outletId) {
        $newDateTo = date('Y-m-d', strtotime($dateTo . ' +1 day'));
        $like      = trim(strtoupper($this->dbView->escape_like_str($cari)));


        if ($type === 'penumpang') {
            $where = " WHERE B.status != -5 AND BP.service_id = 1  and B.created_on >='" . $dateFrom . "' and B.created_on<'" . $newDateTo . "' ";

            if (!empty($port)) {
                $where .= " and (B.origin='" . $port . "')";
            }
            if (!empty($payment_type)) {
                $where .= " and (PY.payment_type='" . $payment_type . "')";
            }
            if (!empty($channel)) {
                $where .= " and (tti.channel='" . $channel . "')";
            }
            
            if(!empty($merchant))
            {
                // $where .= " and tmom.merchant_id='" . $merchant . "'";
                $where .= " and b.created_by='" . $merchant . "'";
            }
    
            if(!empty($outletId) and $outletId !="undefined" )
            {
                $where .= " and tti.outlet_id='" . $outletId . "'";
            }                            

            if (!empty($cari)) {
                if ($searchName == 'bookingCode') {
                    $where .= " and (B.booking_code ='" . $like . "') ";
                } else if ($searchName == 'noIdentitas') {
                    $where .= " and ( BP.id_number ilike '%" . $like . "%') ";
                } else if ($searchName == 'ticketNumber') {
                    $where .= " and ( BP.ticket_number ilike '%" . $like . "%') ";
                } else if ($searchName == 'customerName') {
                    $where .= " and ( BP.name ilike '%" . $like . "%') ";
                } else if ($searchName == 'noInvoice') {
                    $where .= " and ( tti.trans_number = '" . $like . "') ";
                } else {
                    $where .= "";
                }
            }

            $sql_old_04052023 = "SELECT
                        B.id,
                        B.origin,
                        B.ship_class,
                        BP.ticket_number,
                        BP.booking_code,
                        BP.depart_date,
                        BP.depart_time_start,
                        BP.name as customer,
                        BP.id_number,
                        PY.payment_type,
                        tti.channel,
                        BP.fare,
                        B.status as pemesanan,
                        PY.status as pembayaran,
                        GI.status as gate_in,
                        BRP.status as validasi,
                        CI.status as cetak_boarding,
                        B.created_on as pemesanan_date,
                        PY.created_on as pembayaran_date,
                        GI.created_on as gate_in_date,
                        BRP.created_on as validasi_date,
                        CI.created_on as cetak_boarding_date,
                        tti.trans_number,
                        tti.outlet_id ,
                        tmom.merchant_id
                    FROM app.t_trx_booking B
                    JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
                    LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
                    LEFT JOIN app.t_trx_gate_in GI ON GI.ticket_number = BP.ticket_number
                    LEFT JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
                    LEFT JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
                    left join app.t_trx_invoice tti on B.trans_number = tti.trans_number
                    left join app.t_mtr_outlet_merchant tmom on tti.outlet_id = tmom.outlet_id 
			{$where}";

            $sql = "SELECT
            B.id,
            B.origin,
            B.ship_class,
            BP.ticket_number,
            BP.booking_code,
            BP.depart_date,
            BP.depart_time_start,
            BP.name as customer,
            BP.id_number,
            PY.payment_type,
            tti.channel,
            BP.fare,
            B.status as pemesanan,
            PY.status as pembayaran,
            GI.status as gate_in,
            BRP.status as validasi,
            CI.status as cetak_boarding,
            B.created_on as pemesanan_date,
            PY.created_on as pembayaran_date,
            GI.created_on as gate_in_date,
            BRP.created_on as validasi_date,
            CI.created_on as cetak_boarding_date,
            tti.trans_number,
            tti.created_by as created_by_invoice,
            tti.outlet_id 
            FROM app.t_trx_booking B
            JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
            LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
            LEFT JOIN app.t_trx_gate_in GI ON GI.ticket_number = BP.ticket_number
            LEFT JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
            LEFT JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
            left join app.t_trx_invoice tti on B.trans_number = tti.trans_number
            -- left join app.t_mtr_outlet_merchant tmom on tti.outlet_id = tmom.outlet_id 

            {$where}";            

            // echo $sql; exit;
            $rows_data = $this->dbView->query($sql)->result();

            if (count($rows_data) > 0) {
                $masterPort = $this->getDataMaster("app.t_mtr_port","id","name");
                $masterShipClass = $this->getDataMaster("app.t_mtr_ship_class","id","name");
                $masterMerchant = $this->getDataMaster("app.t_mtr_merchant","merchant_id","merchant_name");
                $rows = []; 
                foreach ($rows_data as $row) {

                    $row->origin = $masterPort[$row->origin];
                    $row->ship_class = $masterShipClass[$row->ship_class];
                    // $row->merchant_name = $masterMerchant[$row->merchant_id];;   
                    $row->merchant_name = "";
                    If($row->channel=="B2B" || $row->channel=="b2b"  )
                    {
                        $row->merchant_name = $masterMerchant[$row->created_by_invoice];
                    }

                    $rows[] = $row;
    
                }

                // print_r($rows); exit;
                return $rows;

            } else {
                return false;
            }
        }

        if ($type === 'kendaraan') {
            $where = " WHERE B.status != -5 AND BV.service_id = 2 and B.created_on>= '" . $dateFrom . "' and B.created_on<'" . $newDateTo . "'  ";

            $whereSub = $where;

            $whereData = " where data.id is not null ";

            if (!empty($port)) {
                $where .= " and (B.origin='" . $port . "')";
            }
            if (!empty($payment_type)) {
                $where .= " and (PY.payment_type='" . $payment_type . "')";
            }
            if (!empty($channel)) {
                $where .= " and (tti.channel='" . $channel . "')";
            }

            if(!empty($merchant))
            {
                // $where .= " and tmom.merchant_id='" . $merchant . "'";
                $where .= " and b.created_by='" . $merchant . "'";
            }
    
            if(!empty($outletId) and $outletId !="undefined" )
            {
                $where .= " and tti.outlet_id='" . $outletId . "'";
            }                         

            if (!empty($cari)) {
                if ($searchName == 'bookingCode') {
                    $where .= " and (B.booking_code = '" . $like . "') ";
                } else if ($searchName == 'noIdentitas') {
                    $whereData .= " and ( data.id_number ilike '%" . $like . "%') ";
                } else if ($searchName == 'platNo') {
                    $where .= " and ( BV.id_number ilike '%" . $like . "%') ";
                } else if ($searchName == 'ticketNumber') {
                    $where .= " and ( BV.ticket_number ilike '%" . $like . "%') ";
                } else if ($searchName == 'customerName') {
                    $whereData .= " and ( data.name ilike '%" . $like . "%') ";
                } else if ($searchName == 'noInvoice') {
                    $where .= " and ( tti.trans_number = '" . $like . "') ";
                } else {
                    $where .= "";
                }
            }

            $sql_old_04052023 = "
                SELECT * from  (
                    select
                    B.id,
                    B.origin,
                    B.ship_class,
                    tmom.merchant_id,
                    BV.ticket_number,
                    BV.booking_code,
                    BV.depart_date,
                    BV.depart_time_start,
                    (
                        select name from app.t_trx_booking_passanger
                        where
                            booking_code=B.booking_code and status<>'-5' and service_id=2
                        order by
                        ticket_number asc limit 1) as customer
                    ,
                    (
                        select id_number from app.t_trx_booking_passanger
                        where
                            booking_code=B.booking_code and status<>'-5' and service_id=2
                        order by
                        ticket_number asc limit 1) as nik,
                    BV.id_number as plat,
                    BV.vehicle_class_id,
                    PY.payment_type,
                    tti.channel,
                    BV.fare,
                    B.status as pemesanan,
                    PY.status as pembayaran,
                    BRV.status as validasi,
                    CI.status as cetak_boarding,
                    B.created_on as pemesanan_date,
                    PY.created_on as pembayaran_date,
                    BRV.created_on as validasi_date,
                    CI.created_on as cetak_boarding_date,
                    CI.height, CI.length, CI.weight, CI.width, CI.height_cam, CI.length_cam, CI.weighbridge, CI.width_cam,
                    tti.trans_number,
                    tti.outlet_id ,
                    tmom.merchant_id 
                FROM app.t_trx_booking B
                JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
                LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
                LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
                LEFT JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
                left join app.t_trx_invoice tti on B.trans_number = tti.trans_number 
                left join app.t_mtr_outlet_merchant tmom on tti.outlet_id = tmom.outlet_id

				{$where}
				) data {$whereData}

			ORDER BY id ASC";

            $sql = "
				SELECT * from  (
					select
					B.id,
                    B.origin,
                    B.ship_class,
					BV.ticket_number,
					BV.booking_code,
					BV.depart_date,
					BV.depart_time_start,
					(
						select name from app.t_trx_booking_passanger
						where
							booking_code=B.booking_code and status<>'-5' and service_id=2
						order by
						ticket_number asc limit 1) as customer
					,
					(
						select id_number from app.t_trx_booking_passanger
						where
							booking_code=B.booking_code and status<>'-5' and service_id=2
						order by
						ticket_number asc limit 1) as nik,
					BV.id_number as plat,
                    BV.vehicle_class_id,
					PY.payment_type,
					tti.channel,
					BV.fare,
					B.status as pemesanan,
					PY.status as pembayaran,
					BRV.status as validasi,
					CI.status as cetak_boarding,
					B.created_on as pemesanan_date,
					PY.created_on as pembayaran_date,
					BRV.created_on as validasi_date,
					CI.created_on as cetak_boarding_date,
					CI.height, CI.length, CI.weight, CI.width, CI.height_cam, CI.length_cam, CI.weighbridge, CI.width_cam,
                    tti.trans_number,
                    tti.created_by as created_by_invoice,
                    tti.outlet_id 
				FROM app.t_trx_booking B
				JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
				LEFT JOIN app.t_trx_payment PY ON PY.trans_number = B.trans_number
				LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
				LEFT JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
                left join app.t_trx_invoice tti on B.trans_number = tti.trans_number 
				{$where}
				) data {$whereData}

                ORDER BY id ASC";

            $masterPort = $this->getDataMaster("app.t_mtr_port","id","name");
            $masterShipClass = $this->getDataMaster("app.t_mtr_ship_class","id","name");
            $masterMerchant = $this->getDataMaster("app.t_mtr_merchant","merchant_id","merchant_name");
            $masterVehicleClass = $this->getDataMaster("app.t_mtr_vehicle_class","id","name");            

            $rows_data = $this->dbView->query($sql)->result();

            if (count((array) $rows_data) > 0) {
                $rows = [];
                foreach ($rows_data as $row) {

                    $row->origin = $masterPort[$row->origin];
                    $row->ship_class = $masterShipClass[$row->ship_class];
                    $row->merchant_name = $masterMerchant[$row->merchant_id];
                    $row->vehicle_class =$masterVehicleClass[$row->vehicle_class_id];
                    
                    If($row->channel=="B2B" || $row->channel=="b2b"  )
                    {
                        $row->merchant_name = $masterMerchant[$row->created_by_invoice];
                    }

                    $rows[] = $row;
                }
                return $rows;
            } else {
                return false;
            }
        }
    }

    public function select_data($table, $where) {
        return $this->dbView->query("select * from $table $where");
    }

    public function appIdentity() {
        $data = $this->dbView->query("select * from app.t_mtr_identity_app")->row();
        return $data->port_id;
    }
}