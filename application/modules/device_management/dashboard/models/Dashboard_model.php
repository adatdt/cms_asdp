<?php
class Dashboard_model extends CI_Model
{

  function get_total_trx_booking($vehicle = false)
  {
    $date         = $this->input->post('date');
    $date2        = $this->input->post('date2');

    $origin       = $this->enc->decode($this->input->post('origin'));
    $service      = 1;
    $type_booking = 'passanger';

    if ($vehicle) {
      $service = 2;
      $type_booking = 'vehicle';
    }

    // check apakah ini di pelabuhan
    $where_origin = "";
    if ($origin != 0) {
      $where_origin = " AND c.origin ={$origin}";
    }

    $sql = "SELECT COUNT(0)
            FROM
              app.t_trx_payment a
              JOIN app.t_trx_booking b ON a.trans_number = b.trans_number
              JOIN app.t_trx_booking_{$type_booking} c ON b.booking_code = c.booking_code 
            WHERE
              b.service_id = {$service} AND a.payment_date BETWEEN '{$date} 00:00:00' AND '{$date2} 23:59:59' $where_origin ";

    return $this->dbView->query($sql)->row()->count;
  }

  function get_total_trx_boarding()
  {
    $date   = $this->input->post('date');
    $date2  = $this->input->post('date2');

    $origin = $this->enc->decode($this->input->post('origin'));

    $where_origin = "";
    if ($origin != 0) {
      $where_origin = " AND c.origin ={$origin}";
    }

    $sql = "SELECT COUNT(0) 
            FROM
              app.t_trx_boarding_passanger a
              JOIN app.t_trx_booking_passanger b ON a.ticket_number = b.ticket_number
              JOIN app.t_trx_booking c ON b.booking_code = c.booking_code 
            WHERE
              b.service_id = 1 AND a.boarding_date BETWEEN '{$date} 00:00:00' AND '{$date2} 23:59:59' {$where_origin} ";

    return $this->dbView->query($sql)->row()->count;
  }

  function get_total_trx_boarding_vehicle()
  {
    $date   = $this->input->post('date');
    $date2  = $this->input->post('date2');

    $origin = $this->enc->decode($this->input->post('origin'));

    $where_origin = "";
    if ($origin != 0) {
      $where_origin = " AND c.origin ={$origin}";
    }

    $sql = "SELECT COUNT(0) 
            FROM
              app.t_trx_boarding_vehicle a
              JOIN app.t_trx_booking_vehicle b ON a.ticket_number = b.ticket_number
              JOIN app.t_trx_booking c ON b.booking_code = c.booking_code 
            WHERE
              b.service_id = 2 AND a.boarding_date BETWEEN '{$date} 00:00:00' AND '{$date2} 23:59:59' {$where_origin} ";

    return $this->dbView->query($sql)->row()->count;
  }

  function get_ticket_volume($in)
  {
    $date = $this->input->post('date');
    $date2 = $this->input->post('date2');

    $origin = $this->enc->decode($this->input->post('origin'));

    if ($in) {
      $not = '';
    } else {
      $not = 'NOT';
    }

    $where_origin = "";
    if ($origin != 0) {
      $where_origin = " AND b.origin ={$origin}";
    }

    $where = "AND a.payment_date BETWEEN '{$date} 00:00:00' AND '{$date2} 23:59:59' AND LOWER(a.channel) {$not} IN ('web','mobile') {$where_origin} ";

    $sql = "SELECT count1+count2 AS count FROM (SELECT 
            (SELECT COUNT(0)
            FROM
              app.t_trx_payment a
              JOIN app.t_trx_booking b ON a.trans_number = b.trans_number
              JOIN app.t_trx_booking_passanger c ON b.booking_code = c.booking_code 
            WHERE b.service_id = 1 {$where}) AS count1,

            (SELECT COUNT(0)
            FROM
              app.t_trx_payment a
              JOIN app.t_trx_booking b ON a.trans_number = b.trans_number
              JOIN app.t_trx_booking_vehicle c ON b.booking_code = c.booking_code 
            WHERE b.service_id = 2 {$where}) AS count2) as trx";

    return $this->dbView->query($sql)->row()->count;
  }

  function get_ticket_revenue($in)
  {
    $date = $this->input->post('date');
    $date2 = $this->input->post('date2');

    $origin = $this->enc->decode($this->input->post('origin'));

    if ($in) {
      $not = '';
    } else {
      $not = 'NOT';
    }

    $where_origin = "";
    if ($origin != 0) {
      $where_origin = " AND b.origin ={$origin}";
    }

    $where = "AND a.payment_date BETWEEN '{$date} 00:00:00' AND '{$date2} 23:59:59' AND LOWER(a.channel) {$not} IN ('web','mobile') {$where_origin}";

    $sql = "SELECT COALESCE(sum1, 0) + COALESCE(sum2, 0) AS sum
            FROM (SELECT (SELECT SUM(amount) FROM (SELECT
              DISTINCT a.id, a.amount
            FROM
              app.t_trx_payment a
            JOIN app.t_trx_booking b ON a.trans_number = b.trans_number
            JOIN app.t_trx_booking_passanger c ON b.booking_code = c.booking_code
            WHERE b.service_id = 1 {$where}) trx1) AS sum1,

            (SELECT SUM(amount) FROM (SELECT
              DISTINCT a.id, a.amount
            FROM
              app.t_trx_payment a
            JOIN app.t_trx_booking b ON a.trans_number = b.trans_number
            JOIN app.t_trx_booking_vehicle c ON b.booking_code = c.booking_code
            WHERE b.service_id = 2 {$where}) trx2) AS sum2) a";

    return $this->dbView->query($sql)->row()->sum;
  }

  function get_trx_days($day)
  {
    $origin = $this->enc->decode($this->input->post('origin'));
    $date   = $this->input->post('date');
    $date2  = $this->input->post('date2');

    $where_origin = "";
    if ($origin != 0) {
      $where_origin = " AND b.origin ={$origin}";
    }

    $sql = "SELECT date_trunc('day', trx)::date AS date,
            (SELECT COUNT(0)
              FROM
                app.t_trx_payment a
                JOIN app.t_trx_booking b ON a.trans_number = b.trans_number
                JOIN app.t_trx_booking_passanger c ON b.booking_code = c.booking_code 
              WHERE b.service_id = 1 AND a.payment_date::date = date_trunc('day', trx)::date {$where_origin} ) AS total_penumpang,

            (SELECT COUNT(0)
              FROM
                app.t_trx_payment a
                JOIN app.t_trx_booking b ON a.trans_number = b.trans_number
                JOIN app.t_trx_booking_vehicle c ON b.booking_code = c.booking_code 
              WHERE b.service_id = 2 AND a.payment_date::date = date_trunc('day', trx)::date {$where_origin} ) AS total_kendaraan
            FROM generate_series( '{$date2}'::TIMESTAMP - INTERVAL '{$day} DAY', '{$date2}'::TIMESTAMP, '1 day'::interval) trx";

    $arr = array();
    $data = $this->dbView->query($sql)->result();

    foreach ($data as $row) {
      $arr['date'][]  = date('d M Y', strtotime($row->date));
      $arr['total'][] = $row->total_penumpang + $row->total_kendaraan;
    }

    return $arr;
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
        $data[$this->enc->encode(0)] = "SEMUA PELABUHAN";
      }
    } else {
      $where .= "AND id =" . $this->get_identity_app();
    }

    $query = $this->dbView->query("SELECT id, name FROM app.t_mtr_port p WHERE status = 1 {$where} ORDER BY p.order ASC")->result();

    foreach ($query as $row) {
      $data[$this->enc->encode($row->id)] = $row->name;
    }
    return $data;
  }
  public function list_kelas()
  {
    $data  = array();
    $data[$this->enc->encode(0)] = "SEMUA Kelas";
    $query = $this->dbView->query(" select id,name from app.t_mtr_ship_class")->result();
    foreach ($query as $row) {
      $data[$this->enc->encode($row->id)] = $row->name;
    }
    return $data;
  }
  function getReservasi_23112022()
  {
    $origin = $this->enc->decode($this->input->post('origin'));
    $date   = $this->input->post('date');
    $dates   = $this->input->post('date2');

    $getVehicleReservation = $this->dataReservation($date, $dates);

    $where_origin = '';

    $where_kelas = '';

    // $data_sc = $this->select_data("app.t_mtr_ship_class", " where status<>'-5' order by id asc")->result();
    $data_sc = $this->dbCloudSurabaya->query(" select * from app.t_mtr_ship_class  where status<>'-5' order by id asc")->result();
    $where_port = '';
    if ($origin) {
      $where_port .= "AND id=$origin";
    }

    // $data_port = $this->select_data("app.t_mtr_port", " where status='1' $where_port order by id asc")->result();
    $data_port = $this->dbCloudSurabaya->query("select * from app.t_mtr_port where status='1' $where_port order by id asc")->result();
    $ha = array();
    $hs = array();
    $golongan = array();
    $golongan = ['dua', 'empat', 'empatplus'];
    $arr = array();

    $getBoardingData=$this->getTotalBoarding($date, $dates, $origin);

    // print_r($getBoardingData); exit;
    
    foreach ($data_port as $port) {

      $where_origin = "and aa.origin = $port->id";

      foreach ($data_sc as $classship) {
        $tgl1 = new DateTime($date);
        $tgl2 = new DateTime($dates);
        $d = $tgl2->diff($tgl1)->days + 1;
        unset($arr);
        $besok = $date;
        $akumulasi = 0;
        for ($s = 0; $s < $d; $s++) {
        $akumulasi = 0;

          if ($s > 0) {
            $besok = date('Y-m-d', strtotime("+$s day", strtotime($date)));
          }
          $arr['date'][] = $besok;
          $where_kelas = "and aa.ship_class = $classship->id";
          $quota = $this->quota($port->id, $classship->id, $besok);
          $akhir = array();
          $gol = array();
          $data = $this->getData($besok, $where_origin, $where_kelas);

          // $totalBoarding = $this->getTotalBoarding($besok, $where_origin, $where_kelas);
          // var_dump($besok, $where_origin, $where_kelas,$data);
          $totalBelumBoarding = 0;
          if ($data) {
            $c = 0;
            foreach ($data as $row) {
              if (isset($arr[$row->golongan][$s])) {
                $b = $arr[$row->golongan][$s];
                $arr[$row->golongan][$s] = $b + $row->jumlah;
              } else {
                $arr[$row->golongan][$s] = $row->jumlah;
                $gol[] = $row->golongan;
              }
              $akumulasi += $row->jumlah;
            }
            $arrdiff = array_diff($golongan, $gol);
            foreach ($arrdiff as $gs) {
              $arr[$gs][$s] = 0;
            }

            //akumulasi
            $arr['akumulasi'][$s]  = $akumulasi;
            $totalBelumBoarding += $akumulasi;
          } else {
            for ($i = 0; $i < count($golongan); $i++) {
              $arr[$golongan[$i]][$s] = 0;
            }
            $arr['akumulasi'][$s] = 0;
          }

          // quota 
          if ($quota) {
            foreach ($quota as $kuota) {
              if ($kuota->quota == null || $kuota->quota === '') {
                $kuota->quota = 0;
              }
              $arr['kuota'][$s] = $kuota->quota;
            }
          } else {
            $arr['kuota'][$s] = 0;
          }
          
          $total_boarding =0;
          if($getBoardingData)
          {
            foreach ($getBoardingData as $valueTotalBoarding) {
              if($valueTotalBoarding->depart_date == $besok  && $valueTotalBoarding->origin == $port->id  && $valueTotalBoarding->ship_class == $classship->id)
              {
                $total_boarding = $valueTotalBoarding->total_boarding;

              }
            }
          }

          // total reservasi
          $total_reservation=0;
          $total_checkin=0;
          $total_reservation_not_checkin=0;
          if($getVehicleReservation)
          {
            foreach ($getVehicleReservation as $key => $value) 
            {
              if($value->depart_date == $besok  && $value->port_id==$port->id && $value->ship_class == $classship->id )
              {
                $total_reservation += $value->num_reservation;
                $total_checkin += $value->total_checkin;
                $total_reservation_not_checkin += ($total_reservation - $total_checkin);
              }
            }
          }


          $arr['total_boarding'][$s] = $total_boarding;
          $arr['total_belum_boarding'][$s] = $totalBelumBoarding - $total_boarding;
          $arr['total_reservasi'][$s]=$total_reservation;
          $arr['total_checkin'][$s]=$total_checkin;
          $arr['total_reservation_not_checkin'][$s]=$total_reservation_not_checkin;

          $akhir = $arr;
        }
        $j = $port->name . '-' . $classship->name;
              

        if ($j != "GILIMANUK-Express" && $j != "KETAPANG-Express") {
          $ha[$classship->name] = $akhir;
        } else {
          $ha[$classship->name] = [];
          unset($ha[$classship->name]);
        }
      }

      $hs[$port->name] = $ha;
    }
    // print_r($hs);
    // exit;
    return $hs;
  }
  function getReservasi_23022023()
  {
    $origin = $this->enc->decode($this->input->post('origin'));
    $date   = $this->input->post('date');
    $dates   = $this->input->post('date2');

    $getVehicleReservation = $this->dataReservation($date, $dates);

    $where_origin = '';

    $where_kelas = '';

    $data_sc = $this->dbCloudSurabaya->query(" select * from app.t_mtr_ship_class  where status<>'-5' order by id asc")->result();
    $where_port = '';
    if ($origin) {
      $where_port .= "AND id=$origin";
    }

    $data_port = $this->dbCloudSurabaya->query("select * from app.t_mtr_port where status='1' $where_port order by id asc")->result();
    $ha = array();
    $hs = array();
    $golongan = array();
    $golongan = ['dua', 'empat', 'empatplus'];
    $arr = array();

    // $getBoardingData=$this->getTotalBoarding($date, $dates, $origin);

    // print_r($getBoardingData); exit;
    
    foreach ($data_port as $port) {

      $where_origin = "and aa.origin = $port->id";

      foreach ($data_sc as $classship) {
        $tgl1 = new DateTime($date);
        $tgl2 = new DateTime($dates);
        $d = $tgl2->diff($tgl1)->days + 1;
        unset($arr);
        $besok = $date;
        $akumulasi = 0;
        for ($s = 0; $s < $d; $s++) {
        $akumulasi = 0;

          if ($s > 0) {
            $besok = date('Y-m-d', strtotime("+$s day", strtotime($date)));
          }
          $arr['date'][] = $besok;
          $where_kelas = "and aa.ship_class = $classship->id";
          $quota = $this->quota($port->id, $classship->id, $besok);
          $akhir = array();
          $gol = array();
          $data = $this->getData($besok, $where_origin, $where_kelas);

          $totalReservasi =0;
          $totalBoarding = 0;
          $totalBelumBoarding = 0;
          $totalCheckin = 0;
          $totalBelumCheckin = 0;
                    
          if ($data) {

            $duaReservasi=0;
            $empatReservasi=0;
            $empatplusReservasi=0;

            $duaCheckin=0;
            $empatCheckin=0;
            $empatplusCheckin=0;

            $duaNotCheckin=0;
            $empatNotCheckin=0;
            $empatplusNotCheckin=0;            

            $duaBoarding=0;
            $empatBoarding=0;
            $empatplusBoarding=0;        
            

            $duaNotBoarding=0;
            $empatNotBoarding=0;
            $empatplusNotBoarding=0;                    

            foreach ($data as $detail_type) 
            {
              if($detail_type->pelabuhan == $port->id && $detail_type->ship_class == $classship->id && $detail_type->golongan=='dua' ){
                $duaReservasi += $detail_type->jumlah;
                $duaCheckin += $detail_type->jumlah_total_checkin;
                $duaBoarding += $detail_type->jumlah_total_boarding;
                $duaNotCheckin+= $detail_type->total_not_checkin;
                $duaNotBoarding +=$detail_type->total_not_boarding;
             
              }
              else if($detail_type->pelabuhan == $port->id && $detail_type->ship_class == $classship->id && $detail_type->golongan=='empat' )
              {
                $empatReservasi += $detail_type->jumlah;
                $empatCheckin += $detail_type->jumlah_total_checkin;
                $empatBoarding += $detail_type->jumlah_total_boarding;
                $empatNotCheckin +=$detail_type->total_not_checkin;
                $empatNotBoarding +=$detail_type->total_not_boarding;

              }
              else if($detail_type->pelabuhan == $port->id && $detail_type->ship_class == $classship->id && $detail_type->golongan=='empatplus' )
              {
                $empatplusReservasi += $detail_type->jumlah;
                $empatplusCheckin += $detail_type->jumlah_total_checkin;
                $empatplusBoarding += $detail_type->jumlah_total_boarding;
                $empatplusNotCheckin +=$detail_type->total_not_checkin;
                $empatplusNotBoarding +=$detail_type->total_not_boarding;
              }

              $totalBelumBoarding += $detail_type->total_not_boarding;
              $totalBelumCheckin += $detail_type->total_not_checkin;
              $totalBoarding += $detail_type->jumlah_total_boarding;
              $totalCheckin += $detail_type->jumlah_total_checkin;
              $totalReservasi += $detail_type->jumlah;
            }            

            $detailReservasi=array("dua"=>$duaReservasi,"empat"=>$empatReservasi,"empatplus"=>$empatplusReservasi);

            $detailCheckin=array("dua"=>$duaCheckin,"empat"=>$empatCheckin,"empatplus"=>$empatplusCheckin);

            $detailNotCheckin=array("dua"=>$duaNotCheckin,"empat"=>$empatNotCheckin,"empatplus"=>$empatplusNotCheckin);

            $detailBoarding=array("dua"=>$duaBoarding,"empat"=>$empatBoarding,"empatplus"=>$empatplusBoarding);

            $detailNotBoarding=array("dua"=>$duaNotBoarding,"empat"=>$empatNotBoarding,"empatplus"=>$empatplusNotBoarding);            

            $dataDetailGolongan=array(
              "detailReservasi"=>$detailReservasi,
              "detailCheckin"=>$detailCheckin,
              "detailNotCheckin"=>$detailNotCheckin,
              "detailBoarding"=>$detailBoarding,
              "detailNotBoarding"=>$detailNotBoarding,
              "portId"=>$port->id,
              "shipClass"=>$classship->id
            );
          }
          else{
            $dataDetailGolongan=array(
              "detailReservasi"=>array("dua"=>0,"empat"=>0,"empatplus"=>0, "portId"=>$port->id,"shipClass"=>$classship->id),
              "detailCheckin"=>array("dua"=>0,"empat"=>0,"empatplus"=>0, "portId"=>$port->id,"shipClass"=>$classship->id),
              "detailNotCheckin"=>array("dua"=>0,"empat"=>0,"empatplus"=>0, "portId"=>$port->id,"shipClass"=>$classship->id),
              "detailBoarding"=>array("dua"=>0,"empat"=>0,"empatplus"=>0, "portId"=>$port->id,"shipClass"=>$classship->id),
              "detailNotBoarding"=>array("dua"=>0,"empat"=>0,"empatplus"=>0, "portId"=>$port->id,"shipClass"=>$classship->id),
            );
          }

          // quota 
          if ($quota) {
            foreach ($quota as $kuota) {
              if ($kuota->quota == null || $kuota->quota === '') {
                $kuota->quota = 0;
              }
              $arr['kuota'][$s] = $kuota->quota;
            }
          } else {
            $arr['kuota'][$s] = 0;
          }

          $arr['total_boarding'][$s] = $totalBoarding;
          $arr['total_belum_boarding'][$s] = $totalBelumBoarding;
          $arr['total_reservasi'][$s]=$totalReservasi;
          $arr['total_checkin'][$s]=$totalCheckin;
          $arr['total_reservation_not_checkin'][$s]=$totalBelumCheckin;
          $arr['detail'][$s]=$dataDetailGolongan;

          $akhir = $arr;
        }
        $j = $port->name . '-' . $classship->name;
              

        if ($j != "GILIMANUK-Express" && $j != "KETAPANG-Express") {
          $ha[$classship->name] = $akhir;
        } else {
          $ha[$classship->name] = [];
          unset($ha[$classship->name]);
        }
      }

      $hs[$port->name] = $ha;
    }
    // print_r($hs);
    // exit;
    return $hs;
  }

  function getReservasi()
  {
    $origin = $this->enc->decode($this->input->post('origin'));
    $date   = $this->input->post('date');
    $dates   = $this->input->post('date2');

    $getVehicleReservation = $this->dataReservation($date, $dates);

    $where_origin = '';

    $where_kelas = '';

    $data_sc = $this->dbCloudSurabaya->query(" select * from app.t_mtr_ship_class  where status<>'-5' order by id asc")->result();
    $where_port = '';
    $where_port2 ="";
    if ($origin) {
      $where_port .= "AND id=$origin";
      $where_port2 .= " AND tmp.id=$origin";
    }

    $data_port = $this->dbCloudSurabaya->query("select * from app.t_mtr_port where status='1' $where_port order by id asc")->result();
    $ha = array();
    $hs = array();
    $golongan = array();
    $golongan = ['dua', 'empat', 'empatplus'];
    $arr = array();

    // $getBoardingData=$this->getTotalBoarding($date, $dates, $origin);

    // print_r($getBoardingData); exit;
    
    foreach ($data_port as $port) {

      $where_origin = "and aa.origin = $port->id";

      foreach ($data_sc as $classship) {
        $tgl1 = new DateTime($date);
        $tgl2 = new DateTime($dates);
        $d = $tgl2->diff($tgl1)->days + 1;
        unset($arr);
        $besok = $date;
        $akumulasi = 0;
        for ($s = 0; $s < $d; $s++) {
        $akumulasi = 0;

          if ($s > 0) {
            $besok = date('Y-m-d', strtotime("+$s day", strtotime($date)));
          }
          $arr['date'][] = $besok;
          $where_kelas = "and aa.ship_class = $classship->id";
          $quota = $this->quota($port->id, $classship->id, $besok);
          $akhir = array();
          $gol = array();
          $data = $this->getData($besok, $where_origin, $where_kelas);

          $totalReservasi =0;
          $totalBoarding = 0;
          $totalBelumBoarding = 0;
          $totalCheckin = 0;
          $totalBelumCheckin = 0;
                    
          if ($data) {

            $duaReservasi=0;
            $empatReservasi=0;
            $empatplusReservasi=0;

            $duaCheckin=0;
            $empatCheckin=0;
            $empatplusCheckin=0;

            $duaNotCheckin=0;
            $empatNotCheckin=0;
            $empatplusNotCheckin=0;            

            $duaBoarding=0;
            $empatBoarding=0;
            $empatplusBoarding=0;        
            

            $duaNotBoarding=0;
            $empatNotBoarding=0;
            $empatplusNotBoarding=0;                    

            foreach ($data as $detail_type) 
            {
              if($detail_type->pelabuhan == $port->id && $detail_type->ship_class == $classship->id && $detail_type->golongan=='dua' ){
                $duaReservasi += $detail_type->jumlah;
                $duaCheckin += $detail_type->jumlah_total_checkin;
                $duaBoarding += $detail_type->jumlah_total_boarding;
                $duaNotCheckin+= $detail_type->total_not_checkin;
                $duaNotBoarding +=$detail_type->total_not_boarding;
             
              }
              else if($detail_type->pelabuhan == $port->id && $detail_type->ship_class == $classship->id && $detail_type->golongan=='empat' )
              {
                $empatReservasi += $detail_type->jumlah;
                $empatCheckin += $detail_type->jumlah_total_checkin;
                $empatBoarding += $detail_type->jumlah_total_boarding;
                $empatNotCheckin +=$detail_type->total_not_checkin;
                $empatNotBoarding +=$detail_type->total_not_boarding;

              }
              else if($detail_type->pelabuhan == $port->id && $detail_type->ship_class == $classship->id && $detail_type->golongan=='empatplus' )
              {
                $empatplusReservasi += $detail_type->jumlah;
                $empatplusCheckin += $detail_type->jumlah_total_checkin;
                $empatplusBoarding += $detail_type->jumlah_total_boarding;
                $empatplusNotCheckin +=$detail_type->total_not_checkin;
                $empatplusNotBoarding +=$detail_type->total_not_boarding;
              }

              $totalBelumBoarding += $detail_type->total_not_boarding;
              $totalBelumCheckin += $detail_type->total_not_checkin;
              $totalBoarding += $detail_type->jumlah_total_boarding;
              $totalCheckin += $detail_type->jumlah_total_checkin;
              $totalReservasi += $detail_type->jumlah;
            }            

            $detailReservasi=array("dua"=>$duaReservasi,"empat"=>$empatReservasi,"empatplus"=>$empatplusReservasi);

            $detailCheckin=array("dua"=>$duaCheckin,"empat"=>$empatCheckin,"empatplus"=>$empatplusCheckin);

            $detailNotCheckin=array("dua"=>$duaNotCheckin,"empat"=>$empatNotCheckin,"empatplus"=>$empatplusNotCheckin);

            $detailBoarding=array("dua"=>$duaBoarding,"empat"=>$empatBoarding,"empatplus"=>$empatplusBoarding);

            $detailNotBoarding=array("dua"=>$duaNotBoarding,"empat"=>$empatNotBoarding,"empatplus"=>$empatplusNotBoarding);            

            $dataDetailGolongan=array(
              "detailReservasi"=>$detailReservasi,
              "detailCheckin"=>$detailCheckin,
              "detailNotCheckin"=>$detailNotCheckin,
              "detailBoarding"=>$detailBoarding,
              "detailNotBoarding"=>$detailNotBoarding,
              "portId"=>$port->id,
              "shipClass"=>$classship->id
            );
          }
          else{
            $dataDetailGolongan=array(
              "detailReservasi"=>array("dua"=>0,"empat"=>0,"empatplus"=>0, "portId"=>$port->id,"shipClass"=>$classship->id),
              "detailCheckin"=>array("dua"=>0,"empat"=>0,"empatplus"=>0, "portId"=>$port->id,"shipClass"=>$classship->id),
              "detailNotCheckin"=>array("dua"=>0,"empat"=>0,"empatplus"=>0, "portId"=>$port->id,"shipClass"=>$classship->id),
              "detailBoarding"=>array("dua"=>0,"empat"=>0,"empatplus"=>0, "portId"=>$port->id,"shipClass"=>$classship->id),
              "detailNotBoarding"=>array("dua"=>0,"empat"=>0,"empatplus"=>0, "portId"=>$port->id,"shipClass"=>$classship->id),
            );
          }

          // quota 
          if ($quota) {
            foreach ($quota as $kuota) {
              if ($kuota->quota == null || $kuota->quota === '') {
                $kuota->quota = 0;
              }
              $arr['kuota'][$s] = $kuota->quota;
            }
          } else {
            $arr['kuota'][$s] = 0;
          }

          $arr['total_boarding'][$s] = $totalBoarding;
          $arr['total_belum_boarding'][$s] = $totalBelumBoarding;
          $arr['total_reservasi'][$s]=$totalReservasi;
          $arr['total_checkin'][$s]=$totalCheckin;
          $arr['total_reservation_not_checkin'][$s]=$totalBelumCheckin;
          $arr['detail'][$s]=$dataDetailGolongan;

          $akhir = $arr;
        }
        $j = $port->name . '-' . $classship->name;
              

        if ($j != "GILIMANUK-Express" && $j != "KETAPANG-Express") {
          $ha[$classship->name] = $akhir;
        } else {
          $ha[$classship->name] = [];
          unset($ha[$classship->name]);
        }
      }

      $hs[$port->name] = $ha;
    }
    // print_r($hs);
    // exit;

    $returnData = array();
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
      foreach($allData as $key => $allData2)
      {
        foreach($hs as $key2 => $hs2)
        { 
           foreach($hs2 as $key3 => $hs3)
           {  
              
              if($allData2->port_name == $key2 && $allData2->ship_class_name == $key3 )
              {
                $returnData [$key2][$key3]=$hs3;
              }
           }
        }
      }

      // print_r($this->sortingData($returnData));
      // exit;

    // return $hs;
    return $this->sortingData($returnData);
  }  

  public function sortingData($array)
  {
    $data=array();
    foreach($array as $key => $array2)
    {
        asort($array2);
        $data[$key]=$array2;
    }

    return $data;
  }

  public function dataReservation($departDateStart, $departDateEnd)
  {
    $sql = "SELECT
              bb.id as port_id,
              aa.ship_class ,
              aa.depart_date,
              sum (
                
                case
                when 
                  ttciv.id is null
                then 0
                else 1
                end
              ) as total_checkin,
              count(aa.id) as num_reservation
          FROM
            app.t_trx_booking aa
              JOIN app.t_mtr_port bb ON aa.origin = bb.id
              join app.t_trx_booking_vehicle cc on cc.booking_code = aa.booking_code 
              join app.t_mtr_vehicle_class  dd on dd.id = cc.vehicle_class_id 
              left join app.t_trx_check_in_vehicle ttciv on cc.ticket_number = ttciv.ticket_number 

          WHERE aa.status = 2
              and aa.depart_date between '{$departDateStart}' and '{$departDateEnd}' 
              AND aa.service_id = 2
              AND (aa.channel = 'web' OR aa.channel = 'mobile' OR aa.channel = 'ifcs' OR aa.channel = 'b2b'  OR aa.channel = 'web_cs')
          GROUP BY aa.depart_date,bb.id, aa.ship_class";

    return $this->dbView->query($sql)->result();
  }
  public function select_data($table, $where = "")
  {
    return $this->dbView->query("select * from $table $where");
  }

  public function getData_23112022($date, $where_origin, $where_kelas)
  {

    $sql = "SELECT 
    port_name as pelabuhan,
    vehicle_class as kendaraan,
    tipe,
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
    end as golongan,
    date as tanggal,   
    SUM(num_reservation) as jumlah
    FROM
    (
      SELECT
      bb.name as port_name,
      aa.depart_date as date,
      dd.name as vehicle_class,
      dd.id as tipe,
        count(aa.id) as num_reservation
    FROM
      app.t_trx_booking aa
      JOIN app.t_mtr_port bb ON aa.origin = bb.id
      JOIN app.t_trx_booking_vehicle cc ON aa.booking_code = cc.booking_code
      JOIN app.t_mtr_vehicle_class dd ON cc.vehicle_class_id = dd.id
    WHERE
      aa.depart_date = '{$date}' 
    	AND 
      aa.status = 2
      $where_origin
      $where_kelas
      AND (aa.channel = 'web' OR aa.channel = 'mobile' OR aa.channel = 'ifcs' OR aa.channel = 'b2b' OR aa.channel = 'web_cs')
      GROUP BY (aa.depart_date,aa.id,bb.name,dd.name,dd.id)
    ) a GROUP BY date, port_name, vehicle_class,a.tipe";
    // var_dump($sql); exit;
    $data = $this->dbCloudSurabaya->query($sql)->result();
    return $data;

  }
  public function getData($date, $where_origin, $where_kelas)
  {

    $sql = "SELECT 
    port_name as pelabuhan,
    port_id,
    vehicle_class as kendaraan,
    ship_class,
    tipe,
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
    end as golongan,
    date as tanggal,   
    SUM(num_reservation) as jumlah,
    SUM(total_boarding) as jumlah_total_boarding,
    SUM(total_checkin) - SUM(total_boarding) as total_not_boarding,
    SUM(num_reservation) - SUM(total_checkin) as total_not_checkin,
    SUM(total_checkin) as jumlah_total_checkin
    FROM
    (
      SELECT
          bb.id as port_id,
          bb.id as port_name,
          dd.name as vehicle_class,
      	  dd.id as tipe,
          aa.ship_class ,
          aa.depart_date as date,
          sum (
            case
            when
              ttciv.id is null
            then 0
            else 1
            end
          ) as total_checkin,
          sum (
            case
            when
              ttbv.id is null
            then 0
            else 1
            end
          ) as total_boarding,
          count(aa.id) as num_reservation
      FROM
        app.t_trx_booking aa
          JOIN app.t_mtr_port bb ON aa.origin = bb.id
          join app.t_trx_booking_vehicle cc on cc.booking_code = aa.booking_code
          join app.t_mtr_vehicle_class  dd on dd.id = cc.vehicle_class_id
          left join app.t_trx_check_in_vehicle ttciv on cc.ticket_number = ttciv.ticket_number
          left join app.t_trx_boarding_vehicle ttbv on cc.ticket_number = ttbv.ticket_number
    WHERE
      aa.depart_date = '{$date}'
    	AND 
      aa.status = 2
      $where_origin
      $where_kelas
      AND (aa.channel = 'web' OR aa.channel = 'mobile' OR aa.channel = 'ifcs' OR aa.channel = 'b2b' OR aa.channel = 'web_cs')
      GROUP BY aa.depart_date,bb.id, aa.ship_class,dd.name, dd.id
    ) a GROUP BY date, port_name, vehicle_class,a.tipe, a.ship_class,port_id";
    // die($sql); exit;
    $data = $this->dbCloudSurabaya->query($sql)->result();
    return $data;

  }


  public function getTotalBoarding($startDate, $endDate, $origin)
  {
    $and ="";
    if(!empty($origin))
    {
      $and = " and aa.origin='{$origin}' ";
    }
    $sql = "SELECT
    count(aa.id) as total_boarding,
    aa.depart_date,
    aa.origin,
    aa.ship_class
      FROM
        app.t_trx_booking aa
        JOIN app.t_mtr_port bb ON aa.origin = bb.id
        JOIN app.t_trx_booking_vehicle cc ON aa.booking_code = cc.booking_code
        JOIN app.t_mtr_vehicle_class dd ON cc.vehicle_class_id = dd.id
        JOIN app.t_trx_boarding_vehicle ttbv on cc.ticket_number = ttbv.ticket_number 
      WHERE
        aa.depart_date between '{$startDate}' and '{$endDate}'
        AND
        aa.status = 2
        $and
        AND (aa.channel = 'web' OR aa.channel = 'mobile' OR aa.channel = 'ifcs' OR aa.channel = 'b2b' OR aa.channel = 'web_cs')
        GROUP BY 
          aa.depart_date, 
          aa.origin,
          aa.ship_class";
    
    // die($sql); exit;
    $data = $this->dbCloudSurabaya->query($sql)->result();
    
    return $data;

  }


  function quota($port_id, $ship_class, $tanggal)
  {

    $where = "WHERE aa.depart_date = '$tanggal' ";

    if ($port_id) {
      $where .= " AND aa.port_id = $port_id";
    }

    if ($ship_class) {
      $where .= " AND aa.ship_class = $ship_class";
    }


    $sql = "SELECT
     sum(aa.quota) as quota
    FROM
      app.t_trx_quota_pcm_vehicle aa
      JOIN app.t_mtr_port bb ON aa.port_id = bb.ID 
      $where";

    $query = $this->dbCloudSurabaya->query($sql)->result();
    return $query;
  }
  public function list_ship()
  {

    $query = $this->dbView->query("SELECT id, name FROM app.t_mtr_ship_class WHERE status = 1 ORDER BY name ASC")->result();

    foreach ($query as $row) {
      $data[$this->enc->encode($row->id)] = $row->name;
    }
    return $data;
  }

  public function get_identity_app()
  {
    $data = $this->dbView->query(" select * from app.t_mtr_identity_app")->row();

    return $data->port_id;
  }


  function getLinemeter()
  {
    $origin = $this->enc->decode($this->input->post('origins'));
    $date   = $this->input->post('dates');
    $time = $this->input->post('time');
    $ship = $this->enc->decode($this->input->post('ship'));

    $where_origin = '';
    $where_bookorigin = '';
    $where_pcmorigin = '';
    if ($origin != 0) {
      $where_origin = "and q.port_id = $origin";
      $where_bookorigin = "and book.origin = $origin";
      $where_pcmorigin = "and pcm.port_id = $origin";
    }

    $sql = "SELECT
    q.depart_date,
    q.depart_time,
    q.total_lm,q,ship_class,
    EXTRACT ( HOUR FROM q.depart_time ) AS hours,
    (
    COALESCE ( used_quota.used_lm, 0 ) - COALESCE ( expired.used_lm, 0 )) AS sudahdigunain,
    q.total_lm - (
    COALESCE ( used_quota.used_lm, 0 ) - COALESCE ( expired.used_lm, 0 )) AS ketersediaan
    FROM
      app.t_trx_quota_pcm_vehicle q
      LEFT JOIN (
      SELECT
        book.depart_date,
        EXTRACT ( HOUR FROM pass.depart_time_start ) AS depart_time,
        COALESCE ( COUNT ( book.ID ), 0 ) AS total,
        COALESCE ( SUM ( vclass.total_lm ), 0 ) AS used_lm,
        book.origin 
      FROM
        app.t_trx_booking book
        JOIN app.t_trx_booking_vehicle pass ON pass.booking_code = book.booking_code
        JOIN app.t_mtr_vehicle_class vclass ON vclass.ID = pass.vehicle_class_id 
      WHERE
        book.depart_date = '$date' 
        $where_bookorigin
        AND book.status IN ( 0, 1, 2 ) 
        AND book.ship_class = $ship 
      GROUP BY
        book.depart_date,
        book.origin,
        EXTRACT ( HOUR FROM pass.depart_time_start ) 
      ) AS used_quota ON EXTRACT ( HOUR FROM q.depart_time ) = used_quota.depart_time 
      AND q.depart_date = used_quota.depart_date
      LEFT JOIN (
      SELECT
        book.depart_date,
        EXTRACT ( HOUR FROM pass.depart_time_start ) AS depart_time,
        COALESCE ( COUNT ( book.ID ), 0 ) AS total,
        COALESCE ( SUM ( vclass.total_lm ), 0 ) AS used_lm,
        book.origin 
      FROM
        app.t_trx_booking book
        JOIN app.t_trx_invoice inv ON inv.trans_number = book.trans_number
        JOIN app.t_trx_booking_vehicle pass ON pass.booking_code = book.booking_code
        JOIN app.t_mtr_vehicle_class vclass ON vclass.ID = pass.vehicle_class_id 
      WHERE
        inv.status NOT IN ( 2, 7 ) 
        AND inv.due_date :: TIMESTAMP < now() 
        AND book.depart_date = '$date' 
        $where_bookorigin
        AND book.ship_class = $ship 
      GROUP BY
        book.depart_date,
        book.origin,
        EXTRACT ( HOUR FROM pass.depart_time_start ) 
      ) AS expired ON EXTRACT ( HOUR FROM q.depart_time ) = expired.depart_time 
      AND q.depart_date = expired.depart_date 
    WHERE
      q.depart_date = '$date' 
      AND q.ship_class = $ship
      $where_origin
      
      order by q.depart_time asc";
      //   $sql="SELECT
      //  pcm.depart_date,
      //  pcm.depart_time,
      //  pcm.port_id,pcm.ship_class,
      //  A.used_lms as sudahdigunain,pcm.total_lm,   (COALESCE ( pcm.total_lm, 0 ) - COALESCE ( a.used_lms, 0 )) AS ketersediaan 
      // FROM
      //  app.t_trx_quota_pcm_vehicle pcm
      //  LEFT JOIN (
      //  SELECT
      //    book.depart_date,
      //    book.depart_time_start,
      //  book.ship_class,
      //    COALESCE ( COUNT ( book.ID ), 0 ) AS total,
      //    COALESCE ( SUM ( vclass.total_lm ), 0 ) AS used_lms,
      //    book.origin 
      //  FROM
      //    app.t_trx_booking book
      //    JOIN app.t_trx_invoice inv ON inv.trans_number = book.trans_number
      //    JOIN app.t_trx_booking_vehicle pass ON pass.booking_code = book.booking_code
      //    JOIN app.t_mtr_vehicle_class vclass ON vclass.ID = pass.vehicle_class_id 
      //  WHERE
      //    book.trans_number NOT IN (
      //    SELECT
      //      trans_number 
      //    FROM
      //      app.t_trx_invoice inc 
      //    WHERE
      //      inc.status NOT IN ( 2 ) 
      //      AND inc.due_date :: TIMESTAMP < now() 
      //      AND inc.trans_number = book.trans_number 
      //    ) 
      //  GROUP BY
      //    book.origin,
      //    book.depart_date,
      //    book.depart_time_start ,book.ship_class
      //  ) AS A ON A.origin = pcm.port_id 
      //  AND A.depart_date = pcm.depart_date 
      //  AND A.depart_time_start = pcm.depart_time 
      //  AND A.ship_class = pcm.ship_class 
      // WHERE
      //  pcm.depart_date = '$date' 
      //  AND pcm.depart_time BETWEEN '00:00:00' 
      //  AND '23:59:59' 
      //  AND pcm.ship_class = $ship 
      //   $where_pcmorigin 

      //   order by pcm.depart_time";
      $arr = array();
      // var_dump($sql);exit;
      $data = $this->dbView->query($sql)->result();
      if ($data) {
        foreach ($data as $row) {
          $tanggal = $row->depart_time;
          $arr['date'][]  = $tanggal;
          $arr['ship'][] = $row->ship_class;
          if ($row->total_lm == null) {
            $row->total_lm = 0;
          }
          $arr['total_lm'][] = $row->total_lm;
          if ($row->sudahdigunain == null) {
            $row->sudahdigunain = 0;
          }
          $arr['sudahdigunain'][] = $row->sudahdigunain;
          if ($row->ketersediaan == null) {
            $row->ketersediaan = 0;
          }
          $arr['ketersediaan'][] = $row->ketersediaan;
        }
      }


      // var_dump($arr);exit;
      return $arr;
  }

  public function data_detail_reservasi_kendaraan(){


		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');

		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$departDate=trim($this->input->post('departDate'));
    $detail=trim($this->input->post('detail'));
    $portId=trim($this->input->post('portId'));
    $shipClassId=trim($this->input->post('shipClassId'));
    $searchData=str_replace(array("'",'"'),"",trim($this->input->post('searchData')));
    $searchName=trim($this->input->post('searchName'));
		// $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));


	
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

		$where = "where aa.channel in ('web', 'mobile', 'ifcs', 'b2b', 'web_cs') ";

    $where .=" and aa.depart_date='{$departDate}' ";
    $where .=" and aa.status=2 ";

    if(!empty($portId))
    {
      $where .=" and aa.origin = {$portId} ";
    }

    if(!empty($shipClassId))
    {      
      $where .=" and aa.ship_class = {$shipClassId}  ";
    }

    switch ($detail) {
      case 'checkin':
        $where .=" and ttciv.id is not null "; // alias ttciv.id cehckin vehicle

      break;
      case 'notCheckin':
        $where .=" and ttciv.id is  null "; // alias  cehckin vehicle
      break;
      case 'boarding':
        $where .=" and ttbv.id is not null "; // alias ttbv.id id boarding vehicle
      break;
      case 'notBoarding':
        $where .=" and ttciv.id is not null and ttbv.id is null "; // ttbv.id boarding vehicle
      break;
      default: // default reservasi
        $where .= "";
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
          $where .=" and tti.cutomer_name ilike '%{$searchData}%' ";
          break;        
        default: // passName nama penumpang
          $whereOut .=" where data.nama_penumpang ilike '%{$searchData}%'";
          break;
      }
    }

		$sql 		   = $this->qry_data_detail_reservasi_kendaraan($where,$whereOut);

    // die($sql); exit;    
		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$bookingCodeEnc 	 = $this->enc->encode($row->booking_code);
      $detailUrl  = site_url($this->_module."/detail_knd_modal/{$bookingCodeEnc}");

      $row->actions="";
      // $row->actions .= generate_button_new($this->_module, 'detail', $detailUrl);
      $row->actions .='<button onclick="showModal(\''.$detailUrl.'\')" class="btn btn-sm btn-primary" title="detail"><i class="fa fa-search-plus"></i></button> ';

      $row->depart_time_start = format_time($row->depart_time_start); 
      $row->depart_date = format_date($row->depart_date)." ".$row->depart_time_start;
      $row->tarif_ticket = idr_currency($row->tarif_ticket);
      $row->total_bayar = idr_currency($row->total_bayar); 
      $row->biaya_admin = idr_currency($row->biaya_admin); 


      $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '<i class="fa fa-exclamation" style="color:red">';
      $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '<i class="fa fa-exclamation" style="color:red">';
      $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '<i class="fa fa-exclamation" style="color:red">';
      $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '<i class="fa fa-exclamation" style="color:red">';

      $row->ticket_number ='<a href="'.site_url("transaction/ticket_tracking/index/".$row->ticket_number).'" target="_blank">'.$row->ticket_number.'</a>';

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
  public function qry_data_detail_reservasi_kendaraan($where, $whereOut=""){
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

  public function download_detail_reservasi_kendaraan(){

		$departDate=trim($this->input->get('departDate'));
    $detail=trim($this->input->get('detail'));
    $portId=trim($this->input->get('portId'));
    $shipClassId=trim($this->input->get('shipClassId'));
    $searchData=str_replace(array("'",'"'),"",trim($this->input->get('searchData')));
    $searchName=trim($this->input->get('searchName'));
		// $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));


		$where = "where aa.channel in ('web', 'mobile', 'ifcs', 'b2b', 'web_cs') ";
    $where .=" and aa.depart_date='{$departDate}' ";
    $where .=" and aa.status=2 ";

    if(!empty($portId))
    {
      $where .=" and aa.origin = {$portId} ";
    }

    if(!empty($shipClassId))
    {      
      $where .=" and aa.ship_class = {$shipClassId}  ";
    }

    switch ($detail) {
      case 'checkin':
        $where .=" and ttciv.id is not null "; // alias ttciv.id cehckin vehicle

      break;
      case 'notCheckin':
        $where .=" and ttciv.id is  null "; // alias  cehckin vehicle
      break;
      case 'boarding':
        $where .=" and ttbv.id is not null "; // alias ttbv.id id boarding vehicle
      break;
      case 'notBoarding':
        $where .=" and ttciv.id is not null and ttbv.id is null "; // ttbv.id boarding vehicle
      break;
      default: // default reservasi
        $where .= "";
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
          $where .=" and tti.cutomer_name ilike '%{$searchData}%' ";
          break;        
        default: // passName nama penumpang
          $whereOut .=" where data.nama_penumpang ilike '%{$searchData}%'";
          break;
      }
    }

    $where 		  .= " ORDER BY aa.depart_date asc ";
		$sql 		   = $this->qry_data_detail_reservasi_kendaraan($where,$whereOut);

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= 1;

		foreach ($rows_data as $row) {
			$row->number = $i;

			$bookingCodeEnc 	 = $this->enc->encode($row->booking_code);
      $detailUrl  = site_url($this->_module."/detail_knd_modal/{$bookingCodeEnc}");

      $row->depart_date = $row->depart_date." ".$row->depart_time_start;

      $row->depart_time_start_format = format_time($row->depart_time_start); 
      $row->depart_date_format = format_date($row->depart_date)." ".$row->depart_time_start;
      $row->tarif_ticket_format = idr_currency($row->tarif_ticket);
      $row->total_bayar_format = idr_currency($row->total_bayar); 
      $row->biaya_admin_format = idr_currency($row->biaya_admin); 


      $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '<i class="fa fa-exclamation" style="color:red">';
      $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '<i class="fa fa-exclamation" style="color:red">';
      $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '<i class="fa fa-exclamation" style="color:red">';
      $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '<i class="fa fa-exclamation" style="color:red">';


			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return $rows;
	}  


  public function data_detail_reservasi_penumpang_backup(){


		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');

		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$departDate=trim($this->input->post('departDate'));
    $detail=trim($this->input->post('detail'));
    $portId=trim($this->input->post('portId'));
    $shipClassId=trim($this->input->post('shipClassId'));
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$field = array(
			0 =>'id',

		);

		
		$order_column = $field[$order_column];

		$where = "where aa.channel in ('web', 'mobile', 'ifcs', 'b2b', 'web_cs') ";

    $where .=" and aa.depart_date='{$departDate}' ";
    // $where .=" and aa.depart_date='2022-06-23' ";
    $where .=" and aa.status=2 and cc.service_id=1"; // service id 1 penumpang

    if(!empty($portId))
    {
      $where .=" and aa.origin = {$portId} ";
    }

    if(!empty($shipClassId))
    {      
      $where .=" and aa.ship_class = {$shipClassId}  ";
    }

    switch ($detail) {
      case 'checkin':
        $where .=" and ttciv.id is not null "; // alias ttciv.id cehckin vehicle

      break;
      case 'notCheckin':
        $where .=" and ttciv.id is null "; // alias  not cehckin vehicle
      break;
      case 'boarding':
        $where .=" and ttbv.id is not null "; // alias ttbv.id id boarding vehicle
      break;
      case 'notBoarding':
        $where .=" and ttciv.id is not null  and ttbv.id is null "; // ttbv.id boarding vehicle
      break;
      default: // default reservasi
        $where .= "";
        break;
    }

		$sql 		   = $this->qry_data_detail_reservasi_penumpang($where);

		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;


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
  public function data_detail_reservasi_penumpang(){


		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');

		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$departDate=trim($this->input->post('departDate'));
    $detail=trim($this->input->post('detail'));
    $portId=trim($this->input->post('portId'));
    $shipClassId=trim($this->input->post('shipClassId'));
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));


		
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

		$where = "where aa.channel in ('web', 'mobile', 'ifcs', 'b2b', 'web_cs') ";

    $where .=" and aa.depart_date='{$departDate}' ";
    // $where .=" and aa.depart_date='2022-06-23' ";
    $where .=" and aa.status=2 and cc.service_id=1"; // service id 1 penumpang

    if(!empty($portId))
    {
      $where .=" and aa.origin = {$portId} ";
    }

    if(!empty($shipClassId))
    {      
      $where .=" and aa.ship_class = {$shipClassId}  ";
    }

    switch ($detail) {
      case 'checkin':
        $where .=" and ttciv.id is not null "; // alias ttciv.id cehckin vehicle

      break;
      case 'notCheckin':
        $where .=" and ttciv.id is  null "; // alias  cehckin vehicle
      break;
      case 'boarding':
        $where .=" and ttbv.id is not null "; // alias ttbv.id id boarding vehicle
      break;
      case 'notBoarding':
        $where .=" and ttciv.id is not null and ttbv.id is null "; // ttbv.id boarding vehicle
      break;
      default: // default reservasi
        $where .= "";
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
    

		$sql 		   = $this->qry_data_detail_reservasi_penumpang($where);

    // die($sql); exit;

		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$bookingCodeEnc 	 = $this->enc->encode($row->booking_code);
      $idCodeEnc 	 = $this->enc->encode($row->id_booking_pass);
      $detailUrl  = site_url($this->_module."/detail_pnp_modal/{$idCodeEnc}");

      $row->actions="";
      // $row->actions .= generate_button_new($this->_module, 'detail', $detailUrl);

      $row->actions .='<button onclick="showModal(\''.$detailUrl.'\')" class="btn btn-sm btn-primary" title="detail"><i class="fa fa-search-plus"></i></button> ';

      $row->depart_time_start = format_time($row->depart_time_start); 
      $row->depart_date = format_date($row->depart_date)." ".$row->depart_time_start;
      $row->tarif_ticket = idr_currency($row->tarif_ticket);
      $row->total_bayar = idr_currency($row->total_bayar); 
      $row->biaya_admin = idr_currency($row->biaya_admin);


      $row->pemesanan >= 0 ? $row->pemesanan           = format_dateTimeHis($row->pemesanan_date) : $row->pemesanan           = '<i class="fa fa-exclamation" style="color:red">';
      $row->pembayaran == 1 ? $row->pembayaran         = format_dateTimeHis($row->pembayaran_date) : $row->pembayaran         = '<i class="fa fa-exclamation" style="color:red">';
      $row->cetak_boarding == 1 ? $row->cetak_boarding = format_dateTimeHis($row->cetak_boarding_date) : $row->cetak_boarding = '<i class="fa fa-exclamation" style="color:red">';
      $row->validasi == 1 ? $row->validasi             = format_dateTimeHis($row->validasi_date) : $row->validasi             = '<i class="fa fa-exclamation" style="color:red">';

      $row->ticket_number ='<a href="'.site_url("transaction/ticket_tracking/index/".$row->ticket_number).'" target="_blank">'.$row->ticket_number.'</a>';
      
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
  
  public function qry_data_detail_reservasi_penumpang($where){
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
            ttciv.status as cetak_boarding,
            ttciv.created_on as cetak_boarding_date,
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
              left join app.t_trx_check_in ttciv on cc.ticket_number = ttciv.ticket_number
              left join app.t_trx_boarding_passanger ttbv on cc.ticket_number = ttbv.ticket_number
              left join app.t_mtr_status tms2 on cc.status= tms2.status and tms2.tbl_name ='t_trx_booking_passanger'
          {$where}
    ";

    return $qry;
  }
  public function download_detail_reservasi_penumpang(){


      $departDate=trim($this->input->get('departDate'));
      $detail=trim($this->input->get('detail'));
      $portId=trim($this->input->get('portId'));
      $shipClassId=trim($this->input->get('shipClassId'));

      $where = "where aa.channel in ('web', 'mobile', 'ifcs', 'b2b', 'web_cs') ";
      $where .=" and aa.depart_date='{$departDate}' ";
      $where .=" and aa.status=2 and cc.service_id=1"; // service id 1 penumpang

      if(!empty($portId))
      {
        $where .=" and aa.origin = {$portId} ";
      }

      if(!empty($shipClassId))
      {      
        $where .=" and aa.ship_class = {$shipClassId}  ";
      }

      switch ($detail) {
        case 'checkin':
          $where .=" and ttciv.id is not null "; // alias ttciv.id cehckin vehicle

        break;
        case 'notCheckin':
          $where .=" and ttciv.id is  null "; // alias  cehckin vehicle
        break;
        case 'boarding':
          $where .=" and ttbv.id is not null "; // alias ttbv.id id boarding vehicle
        break;
        case 'notBoarding':
          $where .=" and ttciv.id is not null and ttbv.id is null "; // ttbv.id boarding vehicle
        break;
        default: // default reservasi
          $where .= "";
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
    

      $sql 		   = $this->qry_data_detail_reservasi_penumpang($where);
      $sql 		  .= " ORDER BY aa.depart_date asc ";

      $query     = $this->db->query($sql);
      $rows_data = $query->result();

      $rows 	= array();
      $i  	= 1;

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

  public function detailPenumpangKnd($where)
  {
    $qry="
      select 
      ttbp.booking_code,
      ttbp.ticket_number ,
      ttbp .name,
      ttbp.id_number ,
      ttbp .phone_number ,
      ttbp.age ,
      ttbp.gender ,
      tmsc.name as layanan ,
      tms.name as service,
      tmpt .name as tipe_penumpang 
      
      from app.t_trx_booking_passanger ttbp 
      left join app.t_mtr_ship_class tmsc on ttbp .ship_class =tmsc.id
      left join app.t_mtr_service tms on ttbp.service_id =tms.id 
      left join app.t_mtr_passanger_type tmpt on ttbp.passanger_type_id = tmpt.id
      {$where}
      order by ttbp .ticket_number  asc     
    ";
    return $this->dbView->query($qry)->result();
  }
}
