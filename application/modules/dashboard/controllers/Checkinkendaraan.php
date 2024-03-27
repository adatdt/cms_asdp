<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Checkinkendaraan extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        
        $this->load->library('Html2pdf');   
        $this->load->model('checkinkendaraan_model', 'checkinkendaraan');
        $this->dbView = checkReplication();
        $this->_module   = 'dashboard/checkinkendaraan';      
        $this->dbAction = $this->load->database("dbAction", TRUE);
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');

         $detail_url=checkBtnAccessDetailDashboard(uri_string(), 'detail');

         // print_r($detail_url);exit;
         
        $url=site_url($this->_module);
        $data = array(
            'home'      => 'Home',
            'url_home'  => site_url('home'),
            'title'     => 'Dashboard Checkin Kendaraan',
            'content'   => 'checkinkendaraan',
            'port'      => $this->checkinkendaraan->list_port(),
            'shipClass' => form_dropdown('', $this->checkinkendaraan->list_ship_class(), '', 'id="ship_class" class="form-control select2"'),
            'detail_url'    => $detail_url,
        );

        $this->load->view('default', $data);
    }

    public function list_grafik()
    {
        // $detail_url=checkUrlAccess(uri_string(), 'detail');
        // print_r($detail_url);exit;
        echo json_api(1, 'List Grafik', $this->checkinkendaraan->list_data());
    }

    public function detail(){   
        checkUrlAccess($this->_module, 'detail');

        $get = $this->input->get();
        
        // $this->global_model->checkAccessMenuAction($this->_module,'detail');
        // print_r($get);exit;

        $btnExcel = '<button class="btn btn-sm btn-warning download" id="download_excel">Excel</button>';
        $excel = generate_button($this->_module, "download_excel", $btnExcel);

        $btnPdf = '<a class="btn btn-sm btn-warning download" id="download_pdf" target="_blank" href="#" >Pdf</a>';
        $pdf = generate_button($this->_module, "download_pdf", $btnPdf);

        $btnCsv = '<a class="btn btn-sm btn-warning download" id="download_csv" target="_blank" href="#" >Csv</a>';
        $csv = generate_button($this->_module, "download_csv", $btnCsv);

    
        $btnExcel2 = '<button class="btn btn-sm btn-warning download" id="download_excel2">Excel</button>';
        $excel2 = generate_button($this->_module, "download_excel", $btnExcel2);

        $btnPdf2 = '<a class="btn btn-sm btn-warning download" id="download_pdf2" target="_blank" href="#" >Pdf</a>';
        $pdf2 = generate_button($this->_module, "download_pdf", $btnPdf2);

        $btnCsv2 = '<a class="btn btn-sm btn-warning download" id="download_csv2" target="_blank" href="#" >Csv</a>';
        $csv2 = generate_button($this->_module, "download_csv", $btnCsv2);


        $getPort=$this->checkinkendaraan->select_data('app.t_mtr_port', "where status !='-5' order by name asc");
        $port[""]="pilih";
        $getShipClass=$this->checkinkendaraan->select_data('app.t_mtr_ship_class', "where status !='-5' order by name asc");
        // $shipClass[""]="";
        foreach ($getShipClass->result() as $key => $value) {
            $shipClass[$value->id]=$value->name;
        }
        foreach ($getPort->result() as $key => $value) {
            $port[$value->id]=$value->name;
        }

        $path =  $get['path'];

        switch ($path) {
          case '0': //reservations
            $title ="Reservasi";
          break;
          case '1': //checkin
            $title ="Checkin"; 
          break;
          case '2': //notCheckin
            $title ="Belum Checkin";
          break;
          case '3': //boarding
            $title ="Boarding";
          break;
          case '4': //notBoarding
            $title ="Belum Boarding"; //id boarding vehicle
          break;
          default: 
            $where = "";
            break;
        }

        $data = array(
            'home'           => 'Home',
            'url_home'       => site_url('home'),
            'departdate'     => $get['departdate'],
            'portId'         => $get['portId'],
            'time'           => $get['time'],
            'shipClassId'    => $get['shipClassId'],
            'path'           => $get['path'],
            'port'           => $port,
            'shipClass'      => $shipClass,
            'btn_excel'      => $excel,
            'btn_pdf'        => $pdf,
            'btn_csv'        => $csv,
            'btn_excel2'     => $excel2,
            'btn_pdf2'       => $pdf2,
            'btn_csv2'       => $csv2,
           
            'parent'     => 'Dashboard Checkin Kendaraan',
            'url_parent' => site_url($this->_module),
            'title'      =>'Dashboard Checkin Kendaraan',
            'url_title'  =>site_url('dashboard/checkinkendaraan'),
            'title2'     => 'Detail Checkin Kendaraan '.$title,
            'content'    => 'checkinkendaraan/detail',
        );
        $data['getDataTime']=$this->getDataTime();

        $this->load->view('default', $data);
    }

    public function detailVehicle(){   

        if($this->input->is_ajax_request()){
            $rows = $this->checkinkendaraan->dataDetailVehicle();
            echo json_encode($rows);
            exit;
        }
    }

    public function listDetailVehicle($booking_code)
    {
        validate_ajax();

        $id = $this->enc->decode($booking_code);

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title']    = 'Detail Checkin Kendaraan';
        $data['title']    = 'List Detail Checkin Kendaraan';
        $data['content']  = 'dashboard/checkinkendaraan/list_detail';
        $data['detail'] = $this->checkinkendaraan->qryListDetailVehicle($id);

        $this->load->view($this->_module . '/detail_modal_vehicle', $data);
        
    }

    public function detailPassenger(){   

        if($this->input->is_ajax_request()){
            $rows = $this->checkinkendaraan->dataDetailPassenger();
            echo json_encode($rows);
            exit;
        }
    }

    public function listDetailPassenger($booking_code)
    {
        validate_ajax();

        $id = $this->enc->decode($booking_code);

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title']    = 'Detail Checkin Kendaraan';
        $data['title']    = 'List Detail Checkin Kendaraan';
        $data['content']  = 'dashboard/checkinkendaraan/list_detail';
        $data['detail'] = $this->checkinkendaraan->qryListDetailPassenger($id);

        $this->load->view($this->_module . '/detail_modal_passanger', $data);
        
    }

    public function getDataTime(){

        $start =00;
        $end = 23;
        for ($i = $start; $i <= $end; $i++) {
         $data[date("H:00", mktime($i+1))] = date("H:00", mktime($i+1));
        }
        asort($data); 

        return $data;
    }

    public function download_excel_vehicle()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $date = $this->input->get("date");

        $data = $this->checkinkendaraan->download_detail_checkin_vehicle();

        $file_name = 'Detail Dashboard Checkin Kendaraan VS Reservasi Kendaraan Tanggal ' . $date;

        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $header = array(

            "NO"=>"string",
            "NO TIKET"=>"string",
            "KODE BOOKING"=>"string",
            "NAMA PEMESAN"=>"string",
            "NO TELEPON"=>"string",
            "NAMA PENUMPANG"=>"string",
            "NIK"=>"string",
            "ASAL"=>"string",
            "LAYANAN"=>"string",
            "TANGGAL DAN JAM MASUK PELABUHAN"=>"string",
            "GOLONGAN"=>"string",
            "NO POLISI"=>"string",
            "TIPE PEMBAYARAN"=>"string",
            "CHANNEL"=>"string",
            "TARIF TICKET"=>"string",
            "BIAYA ADMIN"=>"string",
            "TOTAL BAYAR"=>"string",
            "STATUS"=>"string",
            "PEMESANAN"=>"string",
            "PEMBAYARAN"=>"string",
            "CETAK BOARDING PASS"=>"string",
            "VALIDASI"=>"string",
        );



        $no = 1;
        foreach ($data as $key => $value) {
            

            $rows[] = array(
                $value->number,
                $value->ticket_number,
                $value->booking_code,
                $value->customer_name,
                $value->phone_number,
                $value->nama_penumpang,
                $value->nik,
                $value->asal,
                $value->layanan,
                $value->depart_date,
                $value->vehicle_class_name,
                $value->plat_number,
                $value->tipe_pembayaran,
                $value->channel,
                $value->tarif_ticket,
                $value->biaya_admin,
                $value->total_bayar,
                $value->status_ticket,
                $value->pemesanan_date,
                $value->pembayaran_date,
                $value->cetak_boarding_date,
                $value->validasi_date,
            );
            $no++;
        }

        // print_r($rows); exit;
        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header, $styles1);

        foreach ($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }
    public function download_csv_vehicle()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_csv');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $date = $this->input->get("date");
        
        $data = $this->checkinkendaraan->download_detail_checkin_vehicle();
        $file_name = 'Detail Dashboard Checkin Kendaraan VS Reservasi Kendaraan Tanggal ' . $date;

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=".$file_name.".csv");
        $fp = fopen('php://output', 'w');

        $dataArray[] = array(
            "NO",
            "NO TIKET",
            "KODE BOOKING",
            "NAMA PEMESAN",
            "NO TELEPON",
            "NAMA PENUMPANG",
            "NIK",
            "ASAL",
            "LAYANAN",
            "TANGGAL DAN JAM MASUK PELABUHAN",
            "GOLONGAN",
            "NO POLISI",
            "TIPE PEMBAYARAN",
            "CHANNEL",
            "TARIF TICKET",
            "BIAYA ADMIN",
            "TOTAL BAYAR",
            "STATUS",
            "PEMESANAN",
            "PEMBAYARAN",
            "CETAK BOARDING PASS",
            "VALIDASI",
        );

        foreach ($data as $key => $value) {
            

            $dataArray[] = array(
                $value->number,
                $value->ticket_number,
                $value->booking_code,
                $value->customer_name,
                $value->phone_number,
                $value->nama_penumpang,
                $value->nik,
                $value->asal,
                $value->layanan,
                $value->depart_date,
                $value->vehicle_class_name,
                $value->plat_number,
                $value->tipe_pembayaran,
                $value->channel,
                $value->tarif_ticket,
                $value->biaya_admin,
                $value->total_bayar,
                $value->status_ticket,
                $value->pemesanan_date,
                $value->pembayaran_date,
                $value->cetak_boarding_date,
                $value->validasi_date,
            );

        }

        foreach ($dataArray as $dataArray) {
            fputcsv($fp, $dataArray);
        }

       fclose($fp);
    }
    public function download_pdf_vehicle()
    {
        $get = $this->input->get();
        $date = $this->input->get("date");
        // print_r($get);exit;
                
        $getData = $this->checkinkendaraan->download_detail_checkin_vehicle();;
        $data["data"]=$getData;
        $data["date"]=$date;
        
        // print_r($data); exit;
        $this->load->view($this->_module. '/pdf_vehicle', $data);

    }
    public function download_excel_passenger()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $date = $this->input->get("date");
        $data = $this->checkinkendaraan->download_detail_checkin_passenger();

        $file_name = 'Detail Dashboard Checkin Kendaraan VS Reservasi Kendaraan Tanggal ' . $date;
        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $header = array(
            "NO"=>"string",
            "NO TIKET"=>"string",
            "KODE BOOKING"=>"string",
            "NAMA PEMESAN"=>"string",
            "NO TELEPON"=>"string",
            "NAMA PENUMPANG"=>"string",
            "NIK"=>"string",
            "ASAL"=>"string",
            "LAYANAN"=>"string",
            "TANGGAL DAN JAM MASUK PELABUHAN"=>"string",
            "GOLONGAN"=>"string",
            "TIPE PEMBAYARAN"=>"string",
            "CHANNEL"=>"string",
            "TARIF TICKET"=>"string",
            "BIAYA ADMIN"=>"string",
            "TOTAL BAYAR"=>"string",
            "STATUS"=>"string",
            "PEMESANAN"=>"string",
            "PEMBAYARAN"=>"string",
            "CETAK BOARDING PASS"=>"string",
            "VALIDASI"=>"string",
        );


        $no = 1;
        foreach ($data as $key => $value) {
            
            $rows[] = array(
                $value->number, 
                $value->ticket_number,
                $value->booking_code,
                $value->customer_name,
                $value->phone_number,
                $value->nama_penumpang,
                $value->nik,
                $value->asal,
                $value->layanan,
                $value->depart_date,
                $value->passanger_type_name,
                $value->tipe_pembayaran,
                $value->channel,
                $value->tarif_ticket,
                $value->biaya_admin,
                $value->total_bayar,
                $value->status_ticket,
                $value->pemesanan_date,
                $value->pembayaran_date,
                $value->cetak_boarding_date,
                $value->validasi_date,
            );
            $no++;
        }

        // print_r($rows); exit;
        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header, $styles1);

        foreach ($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }
    public function download_csv_passenger()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_csv');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $date = $this->input->get("date");
        
        $data = $this->checkinkendaraan->download_detail_checkin_passenger();
        $file_name = 'Detail Dashboard Checkin Kendaraan VS Reservasi Kendaraan Tanggal ' . $date;

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=".$file_name.".csv");
        $fp = fopen('php://output', 'w');

        $dataArray[] = array(
            "NO",
            "NO TIKET",
            "KODE BOOKING",
            "NAMA PEMESAN",
            "NO TELEPON",
            "NAMA PENUMPANG",
            "NIK",
            "ASAL",
            "LAYANAN",
            "TANGGAL DAN JAM MASUK PELABUHAN",
            "GOLONGAN",
            "TIPE PEMBAYARAN",
            "CHANNEL",
            "TARIF TICKET",
            "BIAYA ADMIN",
            "TOTAL BAYAR",
            "STATUS",
            "PEMESANAN",
            "PEMBAYARAN",
            "CETAK BOARDING PASS",
            "VALIDASI",
        );

        foreach ($data as $key => $value) {
            

            $dataArray[] = array(
                $value->number, 
                $value->ticket_number,
                $value->booking_code,
                $value->customer_name,
                $value->phone_number,
                $value->nama_penumpang,
                $value->nik,
                $value->asal,
                $value->layanan,
                $value->depart_date,
                $value->passanger_type_name,
                $value->tipe_pembayaran,
                $value->channel,
                $value->tarif_ticket,
                $value->biaya_admin,
                $value->total_bayar,
                $value->status_ticket,
                $value->pemesanan_date,
                $value->pembayaran_date,
                $value->cetak_boarding_date,
                $value->validasi_date,
            );

        }

        foreach ($dataArray as $dataArray) {
            fputcsv($fp, $dataArray);
        }

       fclose($fp);
    }
    public function download_pdf_passenger()
    {
        $get = $this->input->get();
        $date = $this->input->get("date");
                
        $getData = $this->checkinkendaraan->download_detail_checkin_passenger();;
        $data["data"]=$getData;
        $data["date"]=$date;
           // print_r($data); exit;
     
        $this->load->view($this->_module. '/pdf_passenger', $data);
    }


}
