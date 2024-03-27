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

    $query = $this->dbView->query("SELECT id, name FROM app.t_mtr_port WHERE status = 1 {$where} ORDER BY name ASC")->result();

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
  public function get_identity_app()
  {
    $data = $this->dbView->query(" select * from app.t_mtr_identity_app")->row();

    return $data->port_id;
  }

  function getReservasi()
  {
    $origin = $this->enc->decode($this->input->post('origin'));
    $date   = $this->input->post('date');
    $dates   = $this->input->post('date2');

    $where_origin = '';

    $where_kelas = '';

    $data_sc = $this->select_data("app.t_mtr_ship_class", " where status<>'-5' order by name asc")->result();
    $where_port = '';
    if ($origin) {
      $where_port .= "AND id=$origin";
    }
    $data_port = $this->select_data("app.t_mtr_port", " where status='1' $where_port order by id asc")->result();
    $ha = array();
    $hs = array();

    foreach ($data_port as $port) {

      $where_origin = "and aa.origin = $port->id";

      foreach ($data_sc as $classship) {
        $arr = array();
        unset($arr);
        $where_kelas = "and aa.ship_class = $classship->id";
        $data = $this->getData($date, $dates, $where_origin, $where_kelas);
        $dataclass = $this->db->query("select name  from app.t_mtr_vehicle_class where status = 1 order by name asc")->result();
        $akhir = array();

        if ($data) {
          $s = 0;
          $cl = array();
          $tes = $port->name . $classship->name;

          $tanggalbaru = '';
          foreach ($dataclass as $class) {
            $arr[$class->name][] = $class->name;
            array_push($cl, $class->name);
          }
          $a = 1;
          $h = count((array)$data);
          foreach ($data as $row) {
            $arr['quota'][0]='kuota';
            $arr['date'][0] = 'product';
            $tanggal = $row->tanggal;
            if ($a < $h) {
              $tanggalbaru = $data[$a]->tanggal;
            }
            $quota = $this->quota($port->id,$classship->id,$tanggal)->sisa_quota;

            array_push($arr['date'], $tanggal);
            array_push($arr['quota'],$quota);
            if ($tanggal == $tanggalbaru) {
              if ($a < $h) {
                if (array_key_exists($row->kendaraan, $arr)) {
                  array_push($arr[$row->kendaraan], $row->jumlah);
                  for ($i = 0; $i < count($cl); $i++) {
                    if ($row->kendaraan != $cl[$i]) {
                      array_push($arr[$cl[$i]], 0);
                    }
                  }
                }
              }
            } else {
              if (array_key_exists($row->kendaraan, $arr)) {

                array_push($arr[$row->kendaraan], $row->jumlah);

                for ($i = 0; $i < count($cl); $i++) {
                  if ($row->kendaraan != $cl[$i]) {
                    array_push($arr[$cl[$i]], 0);
                  }
                }
              }
            }
            $a++;
            $s++;
          }
          $akhir[0] = $arr['date'];
          unset($arr['date']);
          foreach ($arr as $b) {
            $akhir[] = $b;
          }
        }
        $ha[$classship->name] = $akhir;
      }
      $hs[$port->name] = $ha;
    }

    return $hs;
  }
  public function select_data($table, $where = "")
  {
    return $this->dbView->query("select * from $table $where");
  }

  public function getData($date, $dates, $where_origin, $where_kelas)
  {

    $sql = "SELECT 
    port_name as pelabuhan,
    vehicle_class as kendaraan,
    date as tanggal,   
    SUM(num_reservation) as jumlah
    FROM
    (
      SELECT
      bb.name as port_name,
      aa.depart_date as date,
      dd.name as vehicle_class,
        count(aa.id) as num_reservation
    FROM
      app.t_trx_booking aa
      JOIN app.t_mtr_port bb ON aa.origin = bb.id
      JOIN app.t_trx_booking_vehicle cc ON aa.booking_code = cc.booking_code
      JOIN app.t_mtr_vehicle_class dd ON cc.vehicle_class_id = dd.id
    WHERE
      aa.depart_date BETWEEN '{$date} 00:00:00' AND '{$dates} 23:59:59' 
    	AND 
      aa.status = 2
      $where_origin
      $where_kelas
      AND (aa.channel = 'web' OR aa.channel = 'mobile' OR aa.channel = 'ifcs' OR aa.channel = 'b2b')
      GROUP BY (aa.depart_date,aa.id,bb.name,dd.name)
    ) a GROUP BY date, port_name, vehicle_class";
    $data = $this->db->query($sql)->result();
    return $data;
  }

  function quota($port_id, $ship_class,$tanggal)
  {
    
      $where = "WHERE aa.depart_date ='$tanggal'";

      if ($port_id) {
          $where .= " AND aa.port_id = $port_id";
      }

      if ($ship_class) {
          $where .= " AND aa.ship_class = $ship_class";
      }


      $sql = "SELECT
      bb.NAME AS port_name,
      aa.depart_date,
      to_char(depart_time,'HH24:00') as depart_time,
      aa.quota, 
      aa.total_quota as sisa_quota
  FROM
      app.t_trx_quota_pcm_vehicle aa
      JOIN app.t_mtr_port bb ON aa.port_id = bb.ID 
      $where
      ORDER BY depart_date, depart_time ASC";

      $query = $this->dbView->query($sql)->row();
      return $query;
  }
}
