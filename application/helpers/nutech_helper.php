<?php

/*
  Document   : nutech_helper
  Created on : Jul 25, 2018 5:18:36 PM
  Author     : Andedi
  Description: Purpose of the PHP File follows.
 */

function function_terbilang($x)
{
  $x = abs($x);
  $angka = array(
    "", "satu", "dua", "tiga", "empat", "lima",
    "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"
  );
  $temp = "";
  if ($x < 12) {
    $temp = " " . $angka[$x];
  } else if ($x < 20) {
    $temp = function_terbilang($x - 10) . " belas";
  } else if ($x < 100) {
    $temp = function_terbilang($x / 10) . " puluh" . function_terbilang($x % 10);
  } else if ($x < 200) {
    $temp = " Seratus" . function_terbilang($x - 100);
  } else if ($x < 1000) {
    $temp = function_terbilang($x / 100) . " ratus" . function_terbilang($x % 100);
  } else if ($x < 2000) {
    $temp = " Seribu" . function_terbilang($x - 1000);
  } else if ($x < 1000000) {
    $temp = function_terbilang($x / 1000) . " ribu" . function_terbilang($x % 1000);
  } else if ($x < 1000000000) {
    $temp = function_terbilang($x / 1000000) . " juta" . function_terbilang($x % 1000000);
  } else if ($x < 1000000000000) {
    $temp = function_terbilang($x / 1000000000) . " milyar" . function_terbilang(fmod($x, 1000000000));
  } else if ($x < 1000000000000000) {
    $temp = function_terbilang($x / 1000000000000) . " trilyun" . function_terbilang(fmod($x, 1000000000000));
  }
  return $temp;
}

function hari_ini($hariku)
{
  $hari = $hariku;

  switch ($hari) {
    case 'Sun':
      $hari_ini = "Minggu";
      break;

    case 'Mon':
      $hari_ini = "Senin";
      break;

    case 'Tue':
      $hari_ini = "Selasa";
      break;

    case 'Wed':
      $hari_ini = "Rabu";
      break;

    case 'Thu':
      $hari_ini = "Kamis";
      break;

    case 'Fri':
      $hari_ini = "Jumat";
      break;

    case 'Sat':
      $hari_ini = "Sabtu";
      break;

    default:
      $hari_ini = "Tidak di ketahui";
      break;
  }

  return "<b>" . $hari_ini . "</b>";
}
function idtyApp_14102021()
{
  $ci = &get_instance();
  $ci->load->database();
  $portId = $ci->db->query("select * from app.t_mtr_identity_app")->row()->port_id;
  $getPort = $ci->db->query("select * from app.t_mtr_port where id='{$portId}'");

  if ($getPort->num_rows() > 0) {

    return strtoupper($getPort->row()->name);
  } else {
    return "CLOUD";
  }
}

function idtyApp()
{
  // $ci = &get_instance();
  // $ci->load->database();
  $dbView=checkReplication();

  $portId = $dbView->query("select * from app.t_mtr_identity_app")->row()->port_id;
  $getPort = $dbView->query("select * from app.t_mtr_port where id='{$portId}'");

  if ($getPort->num_rows() > 0) {

    return strtoupper($getPort->row()->name);
  } else {
    return "CLOUD";
  }
}

function idr_currency($nominal)
{
  return number_format($nominal, 0, ',', '.');
}

function format_time($time)
{
  return date("H:i", strtotime($time));
}

function format_date_old($date)
{
  return date("d F Y ", strtotime($date));
}

function format_date($date)
{
  $date = date("Y-m-d ", strtotime($date));
  $data = explode('-', $date);

  $month = array(
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember',
  );

  return (int)$data[2] . " {$month[$data[1]]} {$data[0]}";
}

function format_dateTime($date)
{
  return date("d F Y H:i", strtotime($date));
}

function format_dateTimeHis($date)
{
  return date("d F Y H:i:s", strtotime($date));
}

function success_color($text)
{
  return "<font color='#00FF00'><b>" . $text . "</font></b>";
}

function failed_color($text)
{
  return "<font color='red'><b>" . $text . "</font></b>";
}

function pending_color($text)
{
  return "<font color='orange'><b>" . $text . "</font></b>";
}

function success_label($text)
{
  return "<span class='label label-success'>" . $text . "<span>";
}

function failed_label($text)
{
  return "<span class='label label-danger'>" . $text . "<span>";
}

function warning_label($text)
{
  return "<span class='label label-warning'>" . $text . "<span>";
}

function is_phone_number($phone)
{
  $prefix = '+628';
  if (substr($phone, 0, 4) == $prefix) {
    return TRUE;
  } else {
    return FALSE;
  }
}

function save_base64img($base64_string, $save_path)
{
  $imageData = base64_decode($base64_string);
  $source = imagecreatefromstring($imageData);
  $save = imagejpeg($source, $save_path, 86);
  imagedestroy($source);

  return $save;
}

function generate_ymd_dir($path, $y = '', $m = '', $d = '')
{
  $THIS = &get_instance();
  $THIS->load->helper('file');


  if (!file_exists($path . '/index.html')) {
    write_file($path . '/index.html', '');
  }

  $y = ($y == '') ? date('Y') : $y;
  $y = $path . '/' . $y;
  if (!is_dir($y)) {
    if (mkdir($y, 0755, TRUE))
      write_file($y . '/index.html', '');
  }

  $m = ($m == '') ? date('m') : $m;
  $ym = $y . '/' . $m;
  if (!is_dir($ym)) {
    if (mkdir($ym, 0755, TRUE))
      write_file($ym . '/index.html', '');
  }

  $d = ($d == '') ? date('d') : $d;
  $ymd = $y . '/' . $m . '/' . $d;
  if (!is_dir($ymd)) {
    if (mkdir($ymd, 0755, TRUE))
      write_file($ymd . '/index.html', '');
  }

  return $ymd;
}

if (!function_exists('validate_ajax')) {
  function validate_ajax()
  {
    $CI   = &get_instance();
    if (!$CI->input->is_ajax_request()) {
      redirect('error_401');
    }
  }
}

if (!function_exists('headerForm')) {
  function headerForm($title)
  {
    $html  = '<div class="portlet-title">';
    $html .= '<div class="caption">';
    $html .= '' . $title . '</div><div class="tools">';
    $html .= '<button type="button" class="btn btn-box-tool btn-xs btn-primary" onclick="closeModal()"><i class="fa fa-times"></i></button></div></div>';

    return $html;
  }
}

if (!function_exists('createBtnForm')) {
  function createBtnForm($type)
  {
    $html  = '<div class="box-footer text-right">';
    $html .= '<button type="button" class="btn btn-sm btn-default" onclick="closeModal()"><i class="fa fa-close"></i> Batal</button> ';
    $html .= '<button type="submit" class="btn btn-sm btn-primary" id="saveBtn"><i class="fa fa-check"></i> ' . $type . '</button>';
    $html .= '</div>';

    return $html;
  }
}

if (!function_exists('json_api')) {
  function json_api($code, $message, $data = '')
  { 
    $THIS = &get_instance();
    if ($data == '') {
      $array = array(
        'code'  => $code,
        'message' => $message,
        "tokenHash"=>$THIS->security->get_csrf_hash(),
        "csrfName"=>$THIS->security->get_csrf_token_name()
      );
    } else {
      $array = array(
        'code'  => $code,
        'message' => $message,
        'data'   => $data,
        "tokenHash"=>$THIS->security->get_csrf_hash(),
        "csrfName"=>$THIS->security->get_csrf_token_name()
      );
    }
    return json_encode($array);
  }
}

function get_config_param($type)
{
  $THIS = &get_instance();
  $THIS->load->model('global_model');
  $config_param = $THIS->global_model->getconfigParam_byType($type);
  $arrayName = array();
  foreach ($config_param as $key => $value) {
    $arrayName[$value->param_name] = $value->param_value;
  }
  return $arrayName;
}

function checkDbView()
{
  // check conecttion note* ' autoinit harus false dan db_debug harus fals di config db '
  $ci = &get_instance();

  $dbObj = $ci->load->database('dbView2', TRUE);
  $connected = $dbObj->initialize();
  if (!$connected) {
    return $ci->load->database("dbView", TRUE);
  } else {
    return $ci->load->database("dbAction", TRUE);
  }
}

function checkReplication_16122020()
{
  $ci = &get_instance();
  $getRow = $ci->db->query("select * from pg_stat_replication")->num_rows();

  if ($getRow > 0) {
    return $ci->load->database("dbView", TRUE);
  } else {
    return $ci->load->database("dbAction", TRUE);
  }
}


function checkReplication_15062022()
{
    $file_path = APPPATH.'config/'.ENVIRONMENT.'/database.php';

    if(file_exists($file_path))
    {
        include($file_path); //load file setting database

        $configDb = $db['dbView']; // inisialisasi db view


        $ci = &get_instance();    
        $connStr = "host=".$configDb['hostname']."  dbname=".$configDb['database']." user=".$configDb['username']." password=".$configDb['password']." ";

        // tanda @ untuk menghilangkan messege error php
        @$conn=pg_connect($connStr);

        if($conn)
        {
            /*
            $getRow = $ci->db->query("select * from pg_stat_replication")->num_rows(); // check apakah ada replikasi
            if ($getRow > 0)
            {
                return $ci->load->database("dbView", TRUE);
            } 
            else 
            {
                return $ci->load->database("dbAction", TRUE);
            }   
            */     

            return $ci->load->database("dbView", TRUE);
        }
        else
        {
            return $ci->load->database("dbAction", TRUE);
        }
    }

}

function checkReplication()
{
    $file_path = APPPATH.'config/'.ENVIRONMENT.'/database.php';

    if(file_exists($file_path))
    {
        include($file_path); //load file setting database

        $configDb = $db['dbView']; // inisialisasi db view

        $ci = &get_instance();
        $getRow = $ci->db->query("select * from pg_stat_replication where client_addr='{$configDb['hostname']}' ")->num_rows();

        if ($getRow > 0) 
        {
          return $ci->load->database("dbView", TRUE);
        } else {
          return $ci->load->database("dbAction", TRUE);
        }
    }

}

function checkReplication_14102021()
{
    

        $ci = &get_instance();    

        return $ci->load->database("dbView", TRUE);


}


function convertToWebp($dir, $tmp_name, $file_name, $new_name, $quality = 100)
{
  $mime_type = mime_content_type($tmp_name);
  if ($mime_type == 'image/jpeg') {
    $img = imagecreatefromjpeg($dir . $file_name);
  }
  if ($mime_type == 'image/png') {
    $img = imagecreatefrompng($dir . $file_name);
  }
  if ($mime_type == 'image/gif') {
    $img = imagecreatefromgif($dir . $file_name);
  }

  if ($mime_type == 'image/bmp') {
    $img = imagecreatefrombmp($dir . $file_name);
  }

  imagepalettetotruecolor($img);
  imagealphablending($img, true);
  imagesavealpha($img, true);
  imagewebp($img, $dir . $new_name, $quality);
  imagedestroy($img);
}

if (!function_exists('checkReplicationSettlement')) {
  function checkReplicationSettlement() {
    $file_path = APPPATH.'config/'.ENVIRONMENT.'/database.php';

    if(file_exists($file_path)) {
      include($file_path);
      $configDb = $db['stmView'];
      $ci = &get_instance();    
      $connStr = "host=".$configDb['hostname']."  dbname=".$configDb['database']." user=".$configDb['username']." password=".$configDb['password']." ";
      @$conn=pg_connect($connStr);

      if($conn) {
        $getRow = $ci->db->query("select * from pg_stat_replication")->num_rows();
        if ($getRow > 0) {
          return $ci->load->database("stmView", TRUE);
        } 
        else {
          return $ci->load->database("stm", TRUE);
        }        
      }
      else {
        echo "stm";
        return $ci->load->database("stm", TRUE);
      }
    }
  }
}
