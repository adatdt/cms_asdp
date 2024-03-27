<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model', 'dashboard');

        // $this->dbCloudSurabaya=$this->load->database("cloudSurabaya", TRUE);
        
        $this->load->library('Html2pdf');        
        $this->_module ='dashboard';
        $this->dbCloudSurabaya=checkReplication();
        $this->dbView = checkReplication();
    }

    function index()
    {
        checkUrlAccess(uri_string(), 'view');
        
        
        $data = array(
            'home'      => 'Home',
            'url_home'  => site_url('home'),
            'title'     => 'Dashboard',
            'content'   => 'index',
            'port'      => form_dropdown('', $this->dashboard->list_port(), '', 'id="origin" class="form-control select2"'),
            'ports'      => form_dropdown('', $this->dashboard->list_port(), '', 'id="origins" class="form-control select2"'),
            'ship'      => form_dropdown('', $this->dashboard->list_ship(), '', 'id="ship" class="form-control select2"')
        );

        $this->load->view('default', $data);
    }

    function listDashboard()
    {
        validate_ajax();
        $post = $this->input->post();

        if ($post['date'] > $post['date2']) {
            $response = json_api(0, 'Start date lebih besar dari end date');
        } else {
            $diff = date_diff(date_create($post['date']), date_create($post['date2']));
            $d    = $diff->format('%a');

            if ($d > 6) {
                $day = $d;
            } else {
                $day = 6;
            }

            $data = array(
                'total_passenger' => $this->dashboard->get_total_trx_booking(),
                'total_vehicle' => $this->dashboard->get_total_trx_booking(true),
                'boarding_passenger' => $this->dashboard->get_total_trx_boarding(),
                'boarding_vehicle' => $this->dashboard->get_total_trx_boarding_vehicle(),
                'volume_ticket' => array(
                    'ticket' => array('Go Show', 'Online'),
                    'total' => array(
                        array('value' => $this->dashboard->get_ticket_volume(false), 'name' => 'Go Show'),
                        array('value' => $this->dashboard->get_ticket_volume(true), 'name' => 'Online'),
                    )
                ),
                'revenue_ticket' => array(
                    'ticket' => array('Go Show', 'Online'),
                    'total' => array(
                        array('value' => $this->dashboard->get_ticket_revenue(false), 'name' => 'Go Show'),
                        array('value' => $this->dashboard->get_ticket_revenue(true), 'name' => 'Online'),
                    )
                ),
                'days' => $this->dashboard->get_trx_days($day)
            );

            $response = json_api(1, 'List Dasboard', $data);
        }

        echo $response;
    }

    function listPCM()
    {
        validate_ajax();
        $post = $this->input->post();
        $data = array(
            'PCM' => $this->dashboard->getLinemeter()
        );
      if($data['PCM']){

          $response = json_api(1, 'List Dasboard', $data);
      } else{
          $data = array('code'=>2,'message'=>'Data Tidak Ditemukan');
          $response = json_encode($data);
      }


        echo $response;
    }

    public function reservasi(){
        checkUrlAccess(uri_string(),'view');

        $detail_url=checkBtnAccessDetailDashboard(uri_string(), 'detail');

        $data = array(
            'home'          => 'Home',
            'url_home'      => site_url('home'),
            'title'         => 'Dashboard',
            'content'       => 'reservasi/index',
            'port'          => form_dropdown('', $this->dashboard->list_port(), '', 'id="origin" class="form-control select2"'),
            'kelas'         => form_dropdown('', $this->dashboard->list_kelas(), '', 'id="kelas" class="form-control select2"'),
            'detail_url'    => $detail_url
        );

        $this->load->view('default', $data);
    }
    public function detail_reservasi(){
        $module = "dashboard/reservasi";

        checkUrlAccess($module, 'detail');

        // buton knd
        $btnExcel = '<button class="btn btn-sm btn-warning download" id="download_excel">Excel</button>';
        $excel = generate_button($module, "download_excel", $btnExcel);

        $btnPdf = '<a class="btn btn-sm btn-warning download" id="download_pdf" target="_blank" href="#" >Pdf</a>';
        $pdf = generate_button($module, "download_pdf", $btnPdf);

        $btnCsv = '<a class="btn btn-sm btn-warning download" id="download_csv" target="_blank" href="#" >Csv</a>';
        $csv = generate_button($module, "download_csv", $btnCsv);

        // buton pnp
        $btnExcel2 = '<button class="btn btn-sm btn-warning download" id="download_excel2">Excel</button>';
        $excel2 = generate_button($module, "download_excel", $btnExcel2);

        $btnPdf2 = '<a class="btn btn-sm btn-warning download" id="download_pdf2" target="_blank" href="#" >Pdf</a>';
        $pdf2 = generate_button($module, "download_pdf", $btnPdf2);

        $btnCsv2 = '<a class="btn btn-sm btn-warning download" id="download_csv2" target="_blank" href="#" >Csv</a>';
        $csv2 = generate_button($module, "download_csv", $btnCsv2);
        
        

        $getPort=$this->dashboard->select_data('app.t_mtr_port', "where status !='-5' order by name asc");
        $port[""]="pilih";
        foreach ($getPort->result() as $key => $value) {
            $port[$value->id]=$value->name;
        }

        $getShipClass=$this->dashboard->select_data('app.t_mtr_ship_class', "where status !='-5' order by name asc");
        $shipClass[""]="pilih";
        foreach ($getShipClass->result() as $key => $value) {
            $shipClass[$value->id]=$value->name;
        }

        $portId=$this->input->get("portId");
        $shipClassId=$this->input->get("shipClass");
        $detail=$this->input->get("detail");
        $departDate=$this->input->get("departDate");

        switch ($detail) {
        case 'checkin':
            $title ="Checkin"; //  cehckin vehicle
    
        break;
        case 'notCheckin':
            $title ="Belum Checkin"; //   belum cehckin vehicle
        break;
        case 'boarding':
            $title ="Boarding"; //  boarding vehicle
        break;
        case 'notBoarding':
            $title ="Belum Boarding"; // belum boarding vehicle
        break;
        default: // default reservasi
            $title ="Reservasi";
            break;
        }        

        $data = array(
            'home'      => 'Home',
            'url_home'  => site_url('home'),
            'title'=>'Dashboard Reservasi',
            'url_title'=>site_url('dashboard/reservasi'),
            'title2'     => 'Detail Dashboard '.$title,
            'content'   => 'reservasi/detail_reservasi',
            'portId'=>$portId,
            'port'=>$port,
            'shipClassId'=>$shipClassId,
            'shipClass'=>$shipClass,
            'detail'=>$detail,
            'departDate'=>$departDate,
            'btn_excel' =>$excel,
            'btn_pdf' =>$pdf,
            'btn_csv' =>$csv,
            'btn_excel2' =>$excel2,
            'btn_pdf2' =>$pdf2,
            'btn_csv2' =>$csv2,
        );

        $this->load->view('default', $data);
    }
    public function data_detail_reservasi_kendaraan(){


        if($this->input->is_ajax_request()){
            $rows = $this->dashboard->data_detail_reservasi_kendaraan();
            echo json_encode($rows);
            exit;
        }
    }    
    public function data_detail_reservasi_penumpang(){


        if($this->input->is_ajax_request()){
            $rows = $this->dashboard->data_detail_reservasi_penumpang();
            echo json_encode($rows);
            exit;
        }
    }        
    public function listReservasi(){
        validate_ajax();
        $post = $this->input->post();

        if($post['date'] > $post['date2']){
            $response = json_api(0,'Start date lebih besar dari end date');
        }else{
            $data = array(
                'reservasi' => $this->dashboard->getReservasi()
            );

            $response = json_api(1,'List Dasboard',$data);
        }

        echo $response;
    }
    public function detail_knd_modal($bookingCode){
        // validate_ajax();
        // $this->global_model->checkAccessMenuAction($this->_module,'add');

        $decode=$this->enc->decode($bookingCode);
        $where =" where ttbp.booking_code='{$decode}' ";

        $data['title'] = 'Detail Penumpang Dalam Kendaraan';
        $data['detail']=$this->dashboard->detailPenumpangKnd($where);

        $this->load->view($this->_module.'/reservasi/detail_knd_modal',$data);
    }
    public function detail_pnp_modal($idBookingPnp){
        // validate_ajax();
        // $this->global_model->checkAccessMenuAction($this->_module,'add');

        $decode=$this->enc->decode($idBookingPnp);
        $where =" where ttbp.id='{$decode}' ";

        $data['title'] = 'Detail Penumpang';
        $data['detail']=$this->dashboard->detailPenumpangKnd($where);

        $this->load->view($this->_module.'/reservasi/detail_pnp_modal',$data);
    }
    public function download_excel_knd()
    {
        $module = "dashboard/reservasi";
        $this->global_model->checkAccessMenuAction($module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("departDate");
        

        $data = $this->dashboard->download_detail_reservasi_kendaraan();

        $file_name = 'Detail Dashboard Reservasi Tanggal Keberangkatan' . $dateFrom ;
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
    public function download_csv_knd()
    {
        $module = "dashboard/reservasi";
        $this->global_model->checkAccessMenuAction($module, 'download_csv');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("departDate");
        
        $data = $this->dashboard->download_detail_reservasi_kendaraan();
        $file_name = 'Detail Dashboard Reservasi Tanggal Keberangkatan' . $dateFrom ;

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
    public function download_pdf_knd()
    {
        $module = "dashboard/reservasi";
        $dateFrom = $this->input->get("departDate");
                
        $getData = $this->dashboard->download_detail_reservasi_kendaraan();;
        $data["data"]=$getData;
        $data["dateFrom"]=$dateFrom;
        
        // print_r($data['data']); exit;
        $this->load->view($module. '/pdf_knd', $data);

    }
    public function download_excel_pnp()
    {
        $module = "dashboard/reservasi";
        $this->global_model->checkAccessMenuAction($module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("departDate");
        

        $data = $this->dashboard->download_detail_reservasi_penumpang();

        $file_name = 'Detail Dashboard Reservasi Tanggal Keberangkatan' . $dateFrom ;
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
    public function download_csv_pnp()
    {
        $module = "dashboard/reservasi";
        $this->global_model->checkAccessMenuAction($module, 'download_csv');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("departDate");
        
        $data = $this->dashboard->download_detail_reservasi_penumpang();
        $file_name = 'Detail Dashboard Reservasi Tanggal Keberangkatan' . $dateFrom ;

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
    public function download_pdf_pnp()
    {
        $module = "dashboard/reservasi";
        $dateFrom = $this->input->get("departDate");
                
        $getData = $this->dashboard->download_detail_reservasi_penumpang();;
        $data["data"]=$getData;
        $data["dateFrom"]=$dateFrom;
        
        // print_r($data['data']); exit;
        $this->load->view($module. '/pdf_pnp', $data);

    }    
}
