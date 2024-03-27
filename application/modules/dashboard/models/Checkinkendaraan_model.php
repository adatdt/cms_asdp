<?php

class Checkinkendaraan_model extends CI_Model
{
      public function __construct() {
        parent::__construct();
        $this->_module = 'dashboard/checkinkendaraan';
    }

    function list_data()
    {

        $port_id = $this->input->post('origin') ? $this->enc->decode($this->input->post('origin')) : '';
        // $ship_class = $this->input->post('ship_class') ? $this->enc->decode($this->input->post('ship_class')) : '';

        $array_group = [];

        $data_grup = array();

        $where_port = "";
        $where_port2 = "";
        $where_sc = "";

        if ($port_id) {
            $where_port2 .= " AND tmp.id=$port_id";
            $where_port .= " AND id=$port_id";
        }

        // if ($ship_class) {
        //     $where_sc .= "AND id=$ship_class";
        // }

        $data_port = $this->select_data("app.t_mtr_port", " where status='1' $where_port order by t_mtr_port.order asc")->result();
        $data_sc = $this->select_data("app.t_mtr_ship_class", " where status<>'-5' $where_sc order by id asc")->result();
        
        $qry = "SELECT 
                  tmp.id,
                  tmp.name as port_name,
                  tmsc.id as ship_class_id,
                  tmsc.name as ship_class_name
                from app.t_mtr_port tmp 
                join app.t_mtr_rute tmr on tmp.id = tmr.origin 
                join app.t_mtr_rute_class tmrc on tmr.id = tmrc.rute_id and tmrc.status = 1
                join app.t_mtr_ship_class tmsc on tmrc.ship_class = tmsc.id
                where tmp.status =1 and tmsc.status <> '-5' $where_port2
                order by tmp.id asc ";

        $allData = $this->dbView->query($qry)->result();



        // $data_grup_vehicle_class = $this->list_vehicle_group_type();
        $data_grup_vehicle_class = array();
        $data_grup_vehicle_class[] = (object) array('id' => 'dua', 'name' => 'Roda 2');
        $data_grup_vehicle_class[] = (object) array('id' => 'empat', 'name' => 'Roda 4');
        $data_grup_vehicle_class[] = (object) array('id' => 'empatplus', 'name' => 'Roda 4+');

        
        // foreach ($data_port as $d_port) {
        //     $d_port->name = ucfirst(strtolower($d_port->name));
        //     foreach ($data_sc as $d_sc) {
        //         if (($d_port->name != 'Bakauheni') && ($d_port->name != 'Merak')) {
        //             if ($d_sc->name != 'Express') {
        //                 $array_group[] = array($d_port->id, $d_port->name, $d_sc->id, $d_sc->name);
        //             }
        //         } else {
        //             $array_group[] = array($d_port->id, $d_port->name, $d_sc->id, $d_sc->name);
        //         }
        //     }
        // }
        

        foreach ($allData as $d_port) {
          $array_group[] = array($d_port->id, $d_port->port_name, $d_port->ship_class_id, $d_port->ship_class_name);
        }


        // print_r($array_group);exit;

        foreach ($array_group as $data_array_group) {
            $data = array();


            $quota = $this->quota($data_array_group[0], $data_array_group[2]);
            $checkin = $this->checkin($data_array_group[0], $data_array_group[2]);
            $reservasi = $this->reservasi($data_array_group[0], $data_array_group[2]);
            $boarding = $this->boarding($data_array_group[0], $data_array_group[2]);


            for ($h = 0; $h < 24; $h++) {
                $jam = $h < 10 ? "0" . $h . ":00"  : $h . ':00';
                $data['jam'][] = $jam;
                // $data['quota'][] = $jam === $quota[0]->jam;
                $data['quota'][] = 0;
                $data['sisa_quota'][] = 0;
                $data['checkin'][] = 0;
                $data['reservasi'][] = 0;
                $data['boarding'][] = 0;
                $data['belum_boarding'][] = 0;
                $data['ship_class'][]= $data_array_group[2];
                $data['port'][]=$data_array_group[0];
                foreach ($data_grup_vehicle_class as $data_grup_vehicle) {
                    $data['checkin_grup'][$data_grup_vehicle->id]['name'] = $data_grup_vehicle->name;
                    $data['checkin_grup'][$data_grup_vehicle->id]['data'][] = 0;
                    $data['reservasi_grup'][$data_grup_vehicle->id]['name'] = $data_grup_vehicle->name;
                    $data['reservasi_grup'][$data_grup_vehicle->id]['data'][] = 0;
                    $data['sisa_quota_grup'][$data_grup_vehicle->id]['name'] = $data_grup_vehicle->name;
                    $data['sisa_quota_grup'][$data_grup_vehicle->id]['data'][] = 0;
                    $data['boarding_grup'][$data_grup_vehicle->id]['name'] = $data_grup_vehicle->name;
                    $data['boarding_grup'][$data_grup_vehicle->id]['data'][] = 0;
                    $data['belum_boarding_grup'][$data_grup_vehicle->id]['name'] = $data_grup_vehicle->name;
                    $data['belum_boarding_grup'][$data_grup_vehicle->id]['data'][] = 0;
                }
            }

            foreach ($quota as $data_quota) {
                $key_array = explode(":", $data_quota->depart_time)[0];
                $data['quota'][(int)$key_array] = $data_quota->quota;
                // $data['sisa_quota'][(int)$key_array] = $data_quota->sisa_quota;
            }

            foreach ($checkin as $data_checkin) {
                $key_array = explode(":", $data_checkin->jam)[0];
                $data['checkin'][(int)$key_array] += $data_checkin->jumlah_checkin;
                $data['checkin_grup'][$data_checkin->golongan]['data'][(int)$key_array] += $data_checkin->jumlah_checkin;
            }

            foreach ($reservasi as $data_reservasi) {
                $key_array = explode(":", $data_reservasi->jam)[0];
                $data['reservasi'][(int)$key_array] += $data_reservasi->jumlah_reservasi;
                $data['reservasi_grup'][$data_reservasi->golongan]['data'][(int)$key_array] += $data_reservasi->jumlah_reservasi;
            }

            foreach ($boarding as $data_boarding) {
                $key_array = explode(":", $data_boarding->jam)[0];
                $data['boarding'][(int)$key_array] += $data_boarding->jumlah_boarding;
                $data['boarding_grup'][$data_boarding->golongan]['data'][(int)$key_array] += $data_boarding->jumlah_boarding;
            }


            for ($h = 0; $h < 24; $h++) {
                $data['sisa_quota'][$h] = $data['reservasi'][$h] - $data['checkin'][$h];
                // $data['reservasi_grup'][$data_reservasi->vehicle_group_type_id]['data'][(int)$key_array] = 
                foreach ($data['sisa_quota_grup'] as $k => $v) {
                    $data['sisa_quota_grup'][$k]['data'][$h] =
                        $data['reservasi_grup'][$k]['data'][$h] - $data['checkin_grup'][$k]['data'][$h];
                }
            }

            for ($h = 0; $h < 24; $h++) {
                $data['belum_boarding'][$h] = $data['checkin'][$h] - $data['boarding'][$h];
                // $data['reservasi_grup'][$data_reservasi->vehicle_group_type_id]['data'][(int)$key_array] = 
                foreach ($data['belum_boarding_grup'] as $k => $v) {
                    $data['belum_boarding_grup'][$k]['data'][$h] =
                        $data['checkin_grup'][$k]['data'][$h] - $data['boarding_grup'][$k]['data'][$h];
                }
            }

            // if (($data_array_group[1] != 'Bakauheni') && ($data_array_group[1] != 'Merak') && ($data_array_group[1] != 'Ciwandan')) {
            //     $data_grup[$data_array_group[1]] = $data;
            // } else {
            //     $data_grup[$data_array_group[1] . '_' . $data_array_group[3]] = $data;
            // }

            $data_grup[$data_array_group[1] . '_' . $data_array_group[3]] = $data;            
        }

            // print_r($data_grup);exit;

        return $data_grup;
    }

    function quota($port_id, $ship_class)
    {
        $start_date = $this->input->post('start_date');
        // $end_date = $this->input->post('end_date');
        // $port_id = $this->input->post('origin') ? $this->enc->decode($this->input->post('origin')) : '';
        // $ship_class = $this->input->post('ship_class') ? $this->enc->decode($this->input->post('ship_class')) : '';

        // $where = "WHERE (aa.depart_date BETWEEN '$start_date' AND '$end_date')";
        $where = "WHERE aa.depart_date = '$start_date'";

        if ($port_id) {
            $where .= " AND aa.port_id = $port_id";
        }

        if ($ship_class) {
            $where .= " AND aa.ship_class = $ship_class";
        }


        $sql = "SELECT
        bb.NAME AS port_name,
        -- aa.depart_date,
        to_char(depart_time,'HH24:00') as depart_time,
        aa.quota, 
        aa.total_quota as sisa_quota
        FROM
        app.t_trx_quota_pcm_vehicle aa
        JOIN app.t_mtr_port bb ON aa.port_id = bb.ID 
        $where
        ORDER BY 
        -- depart_date, 
        depart_time 
        ASC";

        $query = $this->dbView->query($sql)->result();
         // print_r($query);exit;
        return $query;
    }

    function reservasi($port_id, $ship_class)
    {

        $start_date = $this->input->post('start_date');
        // $end_date = $this->input->post('end_date');
        // $port_id = $this->input->post('origin') ? $this->enc->decode($this->input->post('origin')) : '';
        // $ship_class = $this->input->post('ship_class') ? $this->enc->decode($this->input->post('ship_class')) : '';

        // $where = " AND (aa.depart_date BETWEEN '$start_date' AND '$end_date')";
        $where = " AND aa.depart_date = '$start_date'";

        if ($port_id) {
            $where .= " AND aa.origin = $port_id";
        }

        if ($ship_class) {
            $where .= " AND aa.ship_class = $ship_class";
        }

        $sql = "SELECT 
        port_name as nama_pelabuhan,
        -- date as tanggal_keberangkatan, 
        hour as jam,  
        SUM(num_reservation) as jumlah_reservasi,
        -- vehicle_group_type_id,
        -- vehicle_group_type_name
        case
            when tipe = 4
            or tipe = 7
            or tipe = 8 then 'dua'
            when tipe = 9
            or tipe = 10 then 'empat'
            when tipe = 11
            or tipe = 12
            or tipe = 13
            or tipe = 14
            or tipe = 15
            or tipe = 16
            or tipe = 17 then 'empatplus'
        end as golongan
        FROM
        (
            SELECT
            bb.name as port_name,
            aa.depart_date as date,
            to_char(aa.depart_time_start,'HH24:00') as hour,
            count(aa.id) as num_reservation,
            dd.id as tipe
            -- dd.vehicle_group_type_id,
            -- ee.name as vehicle_group_type_name
        FROM
          app.t_trx_booking aa
            JOIN app.t_mtr_port bb ON aa.origin = bb.id
            join app.t_trx_booking_vehicle cc on cc.booking_code = aa.booking_code 
            join app.t_mtr_vehicle_class  dd on dd.id = cc.vehicle_class_id 
            -- join app.t_mtr_vehicle_group_type ee on ee.id = dd.vehicle_group_type_id 
        WHERE aa.status = 2
            $where
            AND aa.service_id = 2
            AND (aa.channel = 'web' OR aa.channel = 'mobile' OR aa.channel = 'ifcs' OR aa.channel = 'b2b'  OR aa.channel = 'web_cs')
            GROUP BY (aa.depart_date,aa.id,bb.name,dd.vehicle_group_type_id,dd.id)
        ) a GROUP BY 
        -- date, 
        hour, 
        port_name,
        a.tipe
        -- vehicle_group_type_id,
        -- vehicle_group_type_name
        ORDER BY 
        -- tanggal_keberangkatan, 
        jam ASC";

        $query = $this->dbView->query($sql)->result();
         // print_r($query);exit;
        return $query;
    }

    function checkin($port_id, $ship_class)
    {
        $start_date = $this->input->post('start_date');
        // $end_date = $this->input->post('end_date');
        // $port_id = $this->input->post('origin') ? $this->enc->decode($this->input->post('origin')) : '';
        // $ship_class = $this->input->post('ship_class') ? $this->enc->decode($this->input->post('ship_class')) : '';

        $where = " AND aa.depart_date = '$start_date'";
        // $where = " AND (aa.depart_date BETWEEN '$start_date' AND '$end_date')";

        if ($port_id) {
            $where .= " AND aa.origin = $port_id";
        }

        if ($ship_class) {
            $where .= " AND aa.ship_class = $ship_class";
        }

        $sql = "SELECT 
        port_name as nama_pelabuhan,
        -- date as tanggal_keberangkatan, 
        hour as jam, 
        SUM(num_checkin) as jumlah_checkin,
        case
        when tipe = 4
        or tipe = 7
        or tipe = 8 then 'dua'
        when tipe = 9
        or tipe = 10 then 'empat'
        when tipe = 11
        or tipe = 12
        or tipe = 13
        or tipe = 14
        or tipe = 15
        or tipe = 16
        or tipe = 17 then 'empatplus'
        end as golongan
        -- vehicle_group_type_id,
        -- vehicle_group_type_name
        FROM
        (
            SELECT
            bb.name as port_name,
            aa.depart_date as date,
            to_char(aa.depart_time_start,'HH24:00') as hour,
            count(cc.id) as num_checkin,
            dd.id as tipe
            -- dd.vehicle_group_type_id,
            -- ee.name as vehicle_group_type_name
        FROM
          app.t_trx_booking aa
            JOIN app.t_mtr_port bb ON aa.origin = bb.id
            join app.t_trx_booking_vehicle cc on cc.booking_code = aa.booking_code 
            JOIN app.t_trx_check_in_vehicle ca ON cc.booking_code = ca.booking_code
            join app.t_mtr_vehicle_class  dd on dd.id = cc.vehicle_class_id 
            -- join app.t_mtr_vehicle_group_type ee on ee.id = dd.vehicle_group_type_id 
        WHERE aa.status = 2
            $where
            AND aa.service_id = 2
            AND (aa.channel = 'web' OR aa.channel = 'mobile' OR aa.channel = 'ifcs' OR aa.channel = 'b2b' OR aa.channel = 'web_cs')
            GROUP BY (aa.depart_date, aa.id, bb.name,dd.vehicle_group_type_id,dd.id)
        ) a GROUP BY 
        -- date, 
        hour, 
        port_name,
        a.tipe
        -- vehicle_group_type_id,
        -- vehicle_group_type_name
        ORDER BY 
        -- tanggal_keberangkatan, 
        jam ASC";

        $query = $this->dbView->query($sql)->result();
        
        return $query;
    }

    function boarding($port_id, $ship_class)
    {
        $start_date = $this->input->post('start_date');
        // $end_date = $this->input->post('end_date');
        // $port_id = $this->input->post('origin') ? $this->enc->decode($this->input->post('origin')) : '';
        // $ship_class = $this->input->post('ship_class') ? $this->enc->decode($this->input->post('ship_class')) : '';

        $where = " AND aa.depart_date = '$start_date'";
        // $where = " AND (aa.depart_date BETWEEN '$start_date' AND '$end_date')";

        if ($port_id) {
            $where .= " AND aa.origin = $port_id";
        }

        if ($ship_class) {
            $where .= " AND aa.ship_class = $ship_class";
        }

        $sql = "SELECT 
        port_name as nama_pelabuhan,
        booking as kode_booking, 
        hour as jam, 
        SUM(num_boarding) as jumlah_boarding,
        case
        when tipe = 4
        or tipe = 7
        or tipe = 8 then 'dua'
        when tipe = 9
        or tipe = 10 then 'empat'
        when tipe = 11
        or tipe = 12
        or tipe = 13
        or tipe = 14
        or tipe = 15
        or tipe = 16
        or tipe = 17 then 'empatplus'
        end as golongan
        -- vehicle_group_type_id,
        -- vehicle_group_type_name
        FROM
        (
            SELECT
            bb.name as port_name,
            aa.depart_date as date,
            to_char(aa.depart_time_start,'HH24:00') as hour,
            count(cc.id) as num_boarding,
            dd.id as tipe,
            aa.booking_code as booking
            -- dd.vehicle_group_type_id,
            -- ee.name as vehicle_group_type_name
        FROM
          app.t_trx_booking aa
            JOIN app.t_mtr_port bb ON aa.origin = bb.id
            join app.t_trx_booking_vehicle cc on cc.booking_code = aa.booking_code 
            JOIN app.t_trx_boarding_vehicle ca ON cc.ticket_number = ca.ticket_number
            join app.t_mtr_vehicle_class  dd on dd.id = cc.vehicle_class_id 
            -- join app.t_mtr_vehicle_group_type ee on ee.id = dd.vehicle_group_type_id 
        WHERE aa.status = 2
          $where
            AND aa.service_id = 2
            AND (aa.channel = 'web' OR aa.channel = 'mobile' OR aa.channel = 'ifcs' OR aa.channel = 'b2b' OR aa.channel = 'web_cs')
            GROUP BY (aa.depart_date, aa.id, bb.name,dd.vehicle_group_type_id,dd.id)
        ) a GROUP BY 
        -- date, 
        hour, 
        port_name,
        booking,
        a.tipe
        -- vehicle_group_type_id,
        -- vehicle_group_type_name
        ORDER BY 
        -- tanggal_keberangkatan, 
        jam ASC";

        $query = $this->dbView->query($sql)->result();
        return $query;
    }

    function list_vehicle_group_type()
    {
        $query = $this->dbView->query("SELECT * FROM app.t_mtr_vehicle_group_type WHERE status = 1 ORDER BY id ASC");
        return $query->result();
    }

    function list_port()
    {

        $where = '';

        $data  = array();

        if ($this->get_identity_app() == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $where .= "AND id =" . $this->session->userdata('port_id');
            } else {
                $where .= "";
                $data[''] = "SEMUA PELABUHAN";
            }
        } else {
            $where .= "AND id =" . $this->get_identity_app();
        }

        $query = $this->dbView->query("SELECT id, name FROM app.t_mtr_port port WHERE status = 1 {$where} ORDER BY port.order ASC")->result();

        foreach ($query as $row) {
            $data[$this->enc->encode($row->id)] = $row->name;
        }
        return $data;
    }

    function list_ship_class()
    {
        $dataShipClass[""] = "Pilih";
        $getShipClass = $this->select_data("app.t_mtr_ship_class", " where status<>'-5' order by name asc")->result();
        foreach ($getShipClass as $key => $value) {
            $dataShipClass[$this->enc->encode($value->id)] = strtoupper($value->name);
        }

        return $dataShipClass;
    }

    public function select_data($table, $where = "")
    {
        return $this->dbView->query("select * from $table $where");
    }

    public function get_identity_app()
    {
        $data = $this->dbView->query(" select * from app.t_mtr_identity_app")->row();

        return $data->port_id;
    }

    public function dataDetailVehicle(){
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');
        $order = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir = strtoupper($order[0]['dir']);
       
        $time = trim($this->input->post('time'));
        $date = trim($this->input->post('date'));
        $portId=trim($this->input->post('portId'));
        $shipClassId=trim($this->input->post('shipClassId'));
        $path=trim($this->input->post('path'));
        $searchData=str_replace(array("'",'"'),"",trim($this->input->post('searchData')));
        $searchName=trim($this->input->post('searchName'));
        // $iLike        = trim(strtoupper(str_replace(array("'",'"'),"", $searchData) ));

        // print_r($time);exit;
        $field = array(
            0 =>'id',
            1=>"number", 
            2=>"ticket_number",
            3=>"booking_code",
            4=>"customer_name",
            5=>"phone_number",
            6=>"nama_penumpang",
            7=>"nik",
            8=>"asal",
            9=>"layanan",
            10=>"depart_date",
            11=>"vehicle_class_name",
            12=>"plat_number",
            13=>"tipe_pembayaran",
            14=>"channel",
            15=>"tarif_ticket",
            16=>"biaya_admin",
            17=>"total_bayar",
            18=>"status_ticket",
            19=>"pemesanan",
            20=>"pembayaran",
            21=>"cetak_boarding",
            22=>"validasi"
        );

        $order_column = $field[$order_column];
        
        $hours = date($time);
        $intervalTime = date('H:i', strtotime($hours . '+ 59 minutes'));

        $where = "where aa.channel in ('web', 'mobile', 'ifcs', 'b2b', 'web_cs') ";
        $where .=" and aa.depart_date = '$date'";
        $where .=" and aa.status=2 ";
        // $where .=" and aa.service_id=2 ";
        $where .=" and aa.depart_time_start between '$time' and '$intervalTime'";
       

        if(!empty($portId))
        {
          $where .=" and aa.origin = {$portId} ";
        }

        if(!empty($shipClassId))
        {      
          $where .=" and aa.ship_class = {$shipClassId}  ";
        }

        // if(!empty($time))
        // {      
        //   $where .=" and aa.depart_time_start between '$time' and '$intervalTime'";
        // }

        switch ($path) {
          case '0': //reservations
            $where .="";

          break;
          case '1': //checkin
            $where .=" and ci.id is not null "; // alias id cehckin vehicle

          break;
          case '2': //notCheckin
            $where .=" and ci.id is null "; // id cehckin vehicle
          break;
          case '3': //boarding
            $where .=" and ttbv.id is not null "; // alias id id boarding vehicle
          break;
          case '4': //notBoarding
            $where .=" and ci.id is not null  and ttbv.id is null "; //id boarding vehicle
          break;
          default: 
            $where = "";
            break;
        }

        $whereOut = " ";
     
        if(!empty($searchData))
        {
            switch ($searchName) {
                case 'bookingCode':
                  $where .=" and cc.booking_code ilike '%{$searchData}%' ";
                  break;
                case 'ticketNumber':
                  $where .=" and cc.ticket_number ilike '%{$searchData}%' ";
                  break;
                case 'costName':
                  $where .=" and tti.nama_pemesan ilike '%{$searchData}%' ";
                  break;        
                default: // passName nama penumpang
                  $whereOut .=" where data.nama_penumpang ilike '%{$searchData}%'";
                  break;
            }
        }
            
        $sql           = $this->qry($where,$whereOut);
        $query         = $this->db->query($sql);

        $records_total = $query->num_rows();
        $sql          .= " ORDER BY ".$order_column." {$order_dir}";

        if($length != -1){
            $sql .=" LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->db->query($sql);
        $rows_data = $query->result();
     
        $rows   = array();
        $i      = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number = $i;

            $booking_code = $this->enc->encode($row->booking_code);
            $detail_url     = site_url($this->_module . "/listDetailVehicle/{$booking_code}");

            $row->actions = "";
            $row->actions .= '<button onclick="showModal(\''.$detail_url.'\')" class="btn btn-sm btn-primary" title="detail"><i class="fa fa-search-plus"></i></button> ';

            $row->depart_time_start = format_time($row->depart_time_start); 
            $row->depart_date = format_date($row->depart_date)." ".$row->depart_time_start;
            $row->tarif_ticket = idr_currency($row->tarif_ticket);
            $row->total_bayar = idr_currency($row->total_bayar); 
            $row->biaya_admin = idr_currency($row->biaya_admin); 
      
      
            $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '<i class="fa fa-exclamation" style="color:red">';
            $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '<i class="fa fa-exclamation" style="color:red">';
            $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '<i class="fa fa-exclamation" style="color:red">';
            $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '<i class="fa fa-exclamation" style="color:red">'; 
            $url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
            $row->ticket_number = '<a href="'.$url.'" target="_blank">'.$row->ticket_number.'</a>';           

            $row->no = $i;

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


    public function qry_09122022($where, $whereOut="") {
  
        $qry="SELECT * from (SELECT 
            aa.id,
            aa.channel,
            aa.booking_code,
            bb.name as pelabuhan,
            cc.id_number as no_polisi,
            cc.ticket_number,
            dd.name as golongan,
            gg.name as shift_class_name,
            (select name from app.t_trx_booking_passanger where booking_code = aa.booking_code  order by ticket_number asc limit 1)
                as nama_penumpang,
            (select id_number from app.t_trx_booking_passanger where booking_code = aa.booking_code  order by ticket_number asc limit 1)
            as nik,
            ee.customer_name as nama_pemesan,
            ee.phone_number as no_telepon,
            aa.depart_date+ aa.depart_time_start as tanggal_jam_masuk,
            ee.amount,
            ee.extra_fee,
            ee.total_amount,
            ee.payment_type,
            ck.id as id_check_in,
            bv.id as id_boarding
            
            FROM
            app.t_trx_booking aa
                left JOIN app.t_mtr_port bb ON aa.origin = bb.id
                left join app.t_trx_booking_vehicle cc on cc.booking_code = aa.booking_code 
                left JOIN app.t_trx_check_in_vehicle ck ON cc.ticket_number = ck.ticket_number
                left JOIN app.t_trx_boarding_vehicle bv ON cc.ticket_number = bv.ticket_number
                Left JOIN app.t_trx_invoice ee ON aa.trans_number = ee.trans_number
                left join app.t_mtr_vehicle_class  dd on dd.id = cc.vehicle_class_id
                left join app.t_mtr_ship_class gg on cc.ship_class=gg.id
                {$where}
            ) data 
            {$whereOut}
            ";

        // die($qry); exit;
        return $qry;
    }

    public function qry($where, $whereOut="") {
  
        $qry="SELECT * from (SELECT
                    aa.id,
                    aa.depart_date,
                    cc.ticket_number,
                    aa.booking_code,
                    tti.customer_name,
                    tti.phone_number,
                    (select name from app.t_trx_booking_passanger where booking_code = aa.booking_code  order by ticket_number asc limit 1)
                    as nama_penumpang,
                    (select id_number from app.t_trx_booking_passanger where booking_code = aa.booking_code  order by ticket_number asc limit 1)
                    as nik,
                    bb.name as asal,
                    tms.name as layanan,
                    aa.depart_date,
                    aa.depart_time_start,
                    dd.name as vehicle_class_name,
                    cc.id_number as plat_number,
                    tti.payment_type as tipe_pembayaran,
                    aa.channel,
                    tti.amount as tarif_ticket,
                    tti.extra_fee as biaya_admin,
                    tti.total_amount as total_bayar,
                    tms2.description as status_ticket,
                    aa.created_on as pemesanan_date,
                    aa.status as pemesanan,
                    py.created_on as pembayaran_date,
                    PY.status as pembayaran,
                    ci.status as cetak_boarding,
                    ci.created_on as cetak_boarding_date,
                    ttbv.created_on as validasi_date,
                    ttbv.status as validasi
                    FROM
                    app.t_trx_booking aa
                        LEFT JOIN app.t_trx_payment py ON py.trans_number = aa.trans_number
                        JOIN app.t_mtr_port bb ON aa.origin = bb.id
                        join app.t_trx_booking_vehicle cc on cc.booking_code = aa.booking_code
                        LEFT JOIN app.t_trx_check_in_vehicle ci ON ci.ticket_number = cc.ticket_number
                        join app.t_mtr_vehicle_class  dd on dd.id = cc.vehicle_class_id
                        join app.t_trx_invoice tti on aa.trans_number =tti .trans_number
                        left join app.t_mtr_ship_class tms on aa.ship_class =tms.id
                        left join app.t_trx_check_in_vehicle ttciv on cc.ticket_number = ttciv.ticket_number
                        left join app.t_trx_boarding_vehicle ttbv on cc.ticket_number = ttbv.ticket_number
                        left join app.t_mtr_status tms2 on cc.status= tms2.status and tms2.tbl_name ='t_trx_booking_vehicle'
                {$where}
            ) data 
            {$whereOut}
            ";

        // die($qry); exit;
        return $qry;
    }    

    public function qryListDetailVehicle($bookingCode)
    {
        $query = $this->dbView->query("
          SELECT 
          ttbp.booking_code,
          ttbp.ticket_number ,
          ttbp.name,
          ttbp.id_number ,
          ttbp.phone_number ,
          ttbp.age ,
          ttbp.gender ,
          tmsc.name as layanan ,
          tms.name as service,
          tmpt.name as tipe_penumpang 
          
          from app.t_trx_booking_passanger ttbp 
          left join app.t_mtr_ship_class tmsc on ttbp .ship_class =tmsc.id
          left join app.t_mtr_service tms on ttbp.service_id =tms.id 
          left join app.t_mtr_passanger_type tmpt on ttbp.passanger_type_id = tmpt.id
          where ttbp.booking_code='{$bookingCode}'
          order by ttbp .ticket_number  asc     
             ");
        $rows_data = $query->result();

        $rows   = array();
        $i      = 1;

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

    public function dataDetailPassenger(){
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');
        $order = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir = strtoupper($order[0]['dir']);
       
        $time = trim($this->input->post('time'));
        $date = trim($this->input->post('date'));
        $portId=trim($this->input->post('portId'));
        $shipClassId=trim($this->input->post('shipClassId'));
        $path=trim($this->input->post('path'));
        $searchData=str_replace(array("'",'"'),"",trim($this->input->post('searchData')));
        $searchName=trim($this->input->post('searchName'));

        $field = array(
            0 =>'id',
            1=>"ticket_number",
            2=>"booking_code",
            3=>"customer_name",
            4=>"phone_number",
            5=>"nama_penumpang",
            6=>"nik",
            7=>"asal",
            8=>"layanan",
            9=>"depart_date",
            10=>"passanger_type_name",
            11=>"tipe_pembayaran",
            12=>"channel",
            13=>"tarif_ticket",
            14=>"biaya_admin",
            15=>"total_bayar",
            16=>"status_ticket",
            17=>"pemesanan",
            18=>"pembayaran",
            19=>"cetak_boarding",
            20=>"validasi"
        );

        $order_column = $field[$order_column];
        
        $hours = date($time);
        $intervalTime = date('H:i', strtotime($hours . '+ 59 minutes'));

        $where = "where aa.channel in ('web', 'mobile', 'ifcs', 'b2b', 'web_cs') ";
        $where .=" and aa.depart_date = '$date'";
        $where .=" and aa.status=2 and cc.service_id=1 ";
        $where .=" and aa.depart_time_start between '$time' and '$intervalTime'";
       

        if(!empty($portId))
        {
          $where .=" and aa.origin = {$portId} ";
        }

        if(!empty($shipClassId))
        {      
          $where .=" and aa.ship_class = {$shipClassId}  ";
        }

        switch ($path) {
          case '0': //reservations
            $where .="";

          break;
          case '1': //checkin
            $where .=" and ci.id is not null "; // alias id cehckin vehicle

          break;
          case '2': //notCheckin
            $where .=" and ci.id is null "; // id cehckin vehicle
          break;
          case '3': //boarding
            $where .=" and ttbv.id is not null "; // alias id id boarding vehicle
          break;
          case '4': //notBoarding
            $where .=" and ci.id is not null  and ttbv.id is null  "; //id boarding vehicle
          break;
          default: 
            $where = "";
            break;
        }

        if(!empty($searchData))
        {
          switch ($searchName) {
            case 'bookingCode':
              $where .=" and cc.booking_code ilike '%{$searchData}%' ";
              break;
            case 'ticketNumber':
              $where .=" and cc.ticket_number ilike '%{$searchData}%' ";
              break;
            case 'costName':
              $where .=" and ee.nama_pemesan ilike '%{$searchData}%' ";
              break;        
            default: // passName nama penumpang
              $where .=" where data.nama_penumpang ilike '%{$searchData}%'";
              break;
          }
        }

        $sql           = $this->qryPassenger($where);
        $query         = $this->db->query($sql);

        $records_total = $query->num_rows();
        $sql          .= " ORDER BY ".$order_column." {$order_dir}";

        if($length != -1){
            $sql .=" LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->db->query($sql);
        $rows_data = $query->result();
     
        $rows   = array();
        $i      = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number = $i;

            $booking_code = $this->enc->encode($row->booking_code);
            $detail_url     = site_url($this->_module . "/listDetailPassenger/{$booking_code}");

            $row->actions = "";
            $row->actions .='<button onclick="showModal(\''.$detail_url.'\')" class="btn btn-sm btn-primary" title="detail"><i class="fa fa-search-plus"></i></button> ';

            $row->depart_time_start = format_time($row->depart_time_start); 
            $row->depart_date = format_date($row->depart_date)." ".$row->depart_time_start;
            $row->tarif_ticket = idr_currency($row->tarif_ticket);
            $row->total_bayar = idr_currency($row->total_bayar); 
            $row->biaya_admin = idr_currency($row->biaya_admin);


            $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '<i class="fa fa-exclamation" style="color:red">';
            $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '<i class="fa fa-exclamation" style="color:red">';
            $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '<i class="fa fa-exclamation" style="color:red">';
            $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '<i class="fa fa-exclamation" style="color:red">';
            $url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
            $row->ticket_number = '<a href="'.$url.'" target="_blank">'.$row->ticket_number.'</a>';

            $row->no = $i;

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

    public function qryPassenger($where) {
  
        $qry="SELECT
                aa.id,
                cc.id as id_booking_pass,
                aa.depart_date,
                cc.ticket_number,
                aa.booking_code,
                tti.customer_name,
                tti.phone_number,
                cc.name as nama_penumpang,
                cc.id_number as nik,
                bb.name as asal,
                tms.name as layanan,
                aa.depart_date,
                aa.depart_time_start,
                dd.name as passanger_type_name,
                cc.id_number as id_number,
                tti.payment_channel as tipe_pembayaran,
                aa.channel,
                tti.amount as tarif_ticket,
                tti.extra_fee as biaya_admin,
                tti.total_amount as total_bayar,
                tms2.description as status_ticket,
                aa.created_on as pemesanan_date,
                aa.status as pemesanan,
                py.created_on as pembayaran_date,
                PY.status as pembayaran,
                ci.status as cetak_boarding,
                ci.created_on as cetak_boarding_date,
                ttbv.created_on as validasi_date,
                ttbv.status as validasi              
              FROM
              app.t_trx_booking aa
                  JOIN app.t_mtr_port bb ON aa.origin = bb.id
                  LEFT JOIN app.t_trx_payment py ON py.trans_number = aa.trans_number
                  join app.t_trx_booking_passanger cc on cc.booking_code = aa.booking_code
                  join app.t_mtr_passanger_type  dd on dd.id = cc.passanger_type_id 
                  join app.t_trx_invoice tti on aa.trans_number =tti .trans_number 
                  left join app.t_mtr_ship_class tms on aa.ship_class =tms.id
                  left join app.t_trx_check_in ci on cc.ticket_number = ci.ticket_number
                  left join app.t_trx_boarding_passanger ttbv on cc.ticket_number = ttbv.ticket_number
                  left join app.t_mtr_status tms2 on cc.status= tms2.status and tms2.tbl_name ='t_trx_booking_passanger'
                     {$where}

                ";

        // die($qry); exit;
        return $qry;
    }

    public function qryListDetailPassenger($bookingCode)
    {
        $query = $this->dbView->query("
          SELECT 
          ttbp.booking_code,
          ttbp.ticket_number ,
          ttbp.name,
          ttbp.id_number ,
          ttbp.phone_number ,
          ttbp.age ,
          ttbp.gender ,
          tmsc.name as layanan ,
          tms.name as service,
          tmpt.name as tipe_penumpang 
          
          from app.t_trx_booking_passanger ttbp 
          left join app.t_mtr_ship_class tmsc on ttbp .ship_class =tmsc.id
          left join app.t_mtr_service tms on ttbp.service_id =tms.id 
          left join app.t_mtr_passanger_type tmpt on ttbp.passanger_type_id = tmpt.id
          where ttbp.booking_code='{$bookingCode}'
          order by ttbp .ticket_number  asc     
             ");
        $rows_data = $query->result();

        $rows   = array();
        $i      = 1;

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

    public function download_detail_checkin_vehicle(){

        $time = trim($this->input->get('time'));
        $date = trim($this->input->get('date'));
        $portId=trim($this->input->get('portId'));
        $shipClassId=trim($this->input->get('shipClassId'));
        $path=trim($this->input->get('path'));
        $searchData=str_replace(array("'",'"'),"",trim($this->input->get('searchData')));
        $searchName=trim($this->input->get('searchName'));
        $hours = date($time);
        $intervalTime = date('H:i', strtotime($hours . '+ 59 minutes'));

        $where = "where aa.channel in ('web', 'mobile', 'ifcs', 'b2b', 'web_cs') ";
        $where .=" and aa.depart_date = '$date'";
        $where .=" and aa.status=2 ";
        // $where .=" and aa.service_id=2 ";
        $where .=" and aa.depart_time_start between '$time' and '$intervalTime'";
       

        if(!empty($portId))
        {
          $where .=" and aa.origin = {$portId} ";
        }

        if(!empty($shipClassId))
        {      
          $where .=" and aa.ship_class = {$shipClassId}  ";
        }

        // if(!empty($time))
        // {      
        //   $where .=" and aa.depart_time_start between '$time' and '$intervalTime'";
        // }

        switch ($path) {
          case '0': //reservations
            $where .="";

          break;
          case '1': //checkin
            $where .=" and ci.id is not null "; // alias id cehckin vehicle

          break;
          case '2': //notCheckin
            $where .=" and ci.id is null "; // id cehckin vehicle
          break;
          case '3': //boarding
            $where .=" and ttbv.id is not null "; // alias id id boarding vehicle
          break;
          case '4': //notBoarding
            $where .=" and ci.id is not null  and ttbv.id is null "; //id boarding vehicle
          break;
          default: 
            $where = "";
            break;
        }

        $whereOut = " ";
     
        if(!empty($searchData))
        {
            switch ($searchName) {
                case 'bookingCode':
                  $where .=" and cc.booking_code ilike '%{$searchData}%' ";
                  break;
                case 'ticketNumber':
                  $where .=" and cc.ticket_number ilike '%{$searchData}%' ";
                  break;
                case 'costName':
                  $where .=" and tti.nama_pemesan ilike '%{$searchData}%' ";
                  break;        
                default: // passName nama penumpang
                  $whereOut .=" where data.nama_penumpang ilike '%{$searchData}%'";
                  break;
            }
        }

        $where        .= " ORDER BY aa.depart_date asc ";
        // print_r($where);exit;
        $sql           = $this->qry($where,$whereOut);

        $query     = $this->db->query($sql);
        $rows_data = $query->result();

        $rows   = array();
        $i      = 1;

        foreach ($rows_data as $row) {
            $row->number = $i;

            $booking_code = $this->enc->encode($row->booking_code);
            $detail_url     = site_url($this->_module . "/listDetailVehicle/{$booking_code}");

            $row->actions = "";
            $row->actions .= '<button onclick="showModal(\''.$detail_url.'\')" class="btn btn-sm btn-primary" title="detail"><i class="fa fa-search-plus"></i></button> ';

            $row->depart_time_start = format_time($row->depart_time_start); 
            $row->depart_date = format_date($row->depart_date)." ".$row->depart_time_start;
            $row->tarif_ticket = idr_currency($row->tarif_ticket);
            $row->total_bayar = idr_currency($row->total_bayar); 
            $row->biaya_admin = idr_currency($row->biaya_admin); 
      
            $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '<i class="fa fa-exclamation" style="color:red">';
            $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '<i class="fa fa-exclamation" style="color:red">';
            $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '<i class="fa fa-exclamation" style="color:red">';
            $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '<i class="fa fa-exclamation" style="color:red">'; 

            $row->no = $i;

            $rows[] = $row;
            unset($row->id);

            $i++;
        }

        return $rows;
    }

    public function download_detail_checkin_passenger12(){

        $time = trim($this->input->post('time'));
        $date = trim($this->input->post('date'));
        $portId=trim($this->input->post('portId'));
        $shipClassId=trim($this->input->post('shipClassId'));
        $path=trim($this->input->post('path'));
        $searchData=str_replace(array("'",'"'),"",trim($this->input->post('searchData')));
        $searchName=trim($this->input->post('searchName'));   
        $hours = date($time);
        $intervalTime = date('H:i', strtotime($hours . '+ 59 minutes'));

        $where = "where aa.channel in ('web', 'mobile', 'ifcs', 'b2b', 'web_cs') ";
        $where .=" and aa.depart_date = '$date'";
        $where .=" and aa.status=2 and cc.service_id=1 ";
        $where .=" and aa.depart_time_start between '$time' and '$intervalTime'";
       

        if(!empty($portId))
        {
          $where .=" and aa.origin = {$portId} ";
        }

        if(!empty($shipClassId))
        {      
          $where .=" and aa.ship_class = {$shipClassId}  ";
        }

        switch ($path) {
          case '0': //reservations
            $where .="";

          break;
          case '1': //checkin
            $where .=" and ci.id is not null "; // alias id cehckin vehicle

          break;
          case '2': //notCheckin
            $where .=" and ci.id is null "; // id cehckin vehicle
          break;
          case '3': //boarding
            $where .=" and ttbv.id is not null "; // alias id id boarding vehicle
          break;
          case '4': //notBoarding
            $where .=" and ci.id is not null  and ttbv.id is null  "; //id boarding vehicle
          break;
          default: 
            $where = "";
            break;
        }

        if(!empty($searchData))
        {
          switch ($searchName) {
            case 'bookingCode':
              $where .=" and cc.booking_code ilike '%{$searchData}%' ";
              break;
            case 'ticketNumber':
              $where .=" and cc.ticket_number ilike '%{$searchData}%' ";
              break;
            case 'costName':
              $where .=" and ee.nama_pemesan ilike '%{$searchData}%' ";
              break;        
            default: // passName nama penumpang
              $where .=" where data.nama_penumpang ilike '%{$searchData}%'";
              break;
          }
        }

        $sql           = $this->qryPassenger($where);
        $sql          .= " ORDER BY aa.depart_date asc ";

        $query     = $this->db->query($sql);
        $rows_data = $query->result();
     
        $rows   = array();
        $i      = 1;

        foreach ($rows_data as $row) {
          $row->number = $i;

        
          $row->depart_date = $row->depart_date." ".$row->depart_time_start; 

          $row->depart_date_format = format_date($row->depart_date)." ".$row->depart_time_start;
          $row->tarif_ticket_format = idr_currency($row->tarif_ticket);
          $row->total_bayar_format = idr_currency($row->total_bayar); 
          $row->biaya_admin_format = idr_currency($row->biaya_admin);


          $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '';
          $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '';
          $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '';
          $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '';


          $rows[] = $row;
          unset($row->id);

          $i++;
      }

        return $rows;
    }  

    public function download_detail_checkin_passenger()
    {

        $time = trim($this->input->get('time'));
        $date = trim($this->input->get('date'));
        $portId=trim($this->input->get('portId'));
        $shipClassId=trim($this->input->get('shipClassId'));
        $path=trim($this->input->get('path'));
        $searchData=str_replace(array("'",'"'),"",trim($this->input->get('searchData')));
        $searchName=trim($this->input->get('searchName'));
        $hours = date($time);
        $intervalTime = date('H:i', strtotime($hours . '+ 59 minutes'));

        $where = "where aa.channel in ('web', 'mobile', 'ifcs', 'b2b', 'web_cs') ";
        $where .=" and aa.depart_date = '$date'";
        $where .=" and aa.status=2 and cc.service_id=1 ";
        $where .=" and aa.depart_time_start between '$time' and '$intervalTime'";
       

        if(!empty($portId))
        {
          $where .=" and aa.origin = {$portId} ";
        }

        if(!empty($shipClassId))
        {      
          $where .=" and aa.ship_class = {$shipClassId}  ";
        }

        switch ($path) {
          case '0': //reservations
            $where .="";

          break;
          case '1': //checkin
            $where .=" and ci.id is not null "; // alias id cehckin vehicle

          break;
          case '2': //notCheckin
            $where .=" and ci.id is null "; // id cehckin vehicle
          break;
          case '3': //boarding
            $where .=" and ttbv.id is not null "; // alias id id boarding vehicle
          break;
          case '4': //notBoarding
            $where .=" and ci.id is not null  and ttbv.id is null  "; //id boarding vehicle
          break;
          default: 
            $where = "";
            break;
        }

        if(!empty($searchData))
        {
            switch ($searchName) {
              case 'bookingCode':
                $where .=" and cc.booking_code ilike '%{$searchData}%' ";
                break;
              case 'ticketNumber':
                $where .=" and cc.ticket_number ilike '%{$searchData}%' ";
                break;
              case 'costName':
                $where .=" and tti.cutomer_name ilike '%{$searchData}%' ";
                break;        
              default: // passName nama penumpang
                $where .=" and cc.name ilike '%{$searchData}%' ";
                break;
            }
        }    
    

      $sql           = $this->qryPassenger($where);
      $sql        .= " ORDER BY aa.depart_date asc ";

      $query     = $this->db->query($sql);
      $rows_data = $query->result();

      $rows     = array();
      $i    = 1;

      foreach ($rows_data as $row) {
          $row->number = $i;

        
          $row->depart_date = $row->depart_date." ".$row->depart_time_start; 

          $row->depart_date_format = format_date($row->depart_date)." ".$row->depart_time_start;
          $row->tarif_ticket_format = idr_currency($row->tarif_ticket);
          $row->total_bayar_format = idr_currency($row->total_bayar); 
          $row->biaya_admin_format = idr_currency($row->biaya_admin);


          $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '';
          $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '';
          $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '';
          $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '';


          $rows[] = $row;
          unset($row->id);

          $i++;
      }

      return $rows;
    }  



}
