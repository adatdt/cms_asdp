<?php

error_reporting(0);

class Ticket_summary extends MY_Controller {

    public function __construct() {
        parent::__construct();
        // $this->load->library('Html2pdf');
        $this->load->model('m_ticket_summary');
        $this->_module = 'laporan/ticket_summary';

        $this->dbAction = $this->load->database("dbAction", TRUE);
        // $this->dbView = $this->load->database("dbView", TRUE);
        $this->dbView = checkReplication();
    }

    public function index($booking_code = '') {

        $appIdentity = $this->m_ticket_summary->appIdentity();

        $portData = [];
        if ($appIdentity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->m_ticket_summary->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id') . "")->result();
            } else {
                $portData[''] = "All";
                $port         = $this->m_ticket_summary->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
            }
        } else {
            $port = $this->m_ticket_summary->select_data("app.t_mtr_port", "where id=" . $appIdentity . " and status=1 ")->result();
        }

        $selectedPort="";
        foreach ($port as $key => $value) {
            $encode = $this->enc->encode($value->id);
            $portData[$encode] = strtoupper($value->name);

            if ($booking_code){
                
                $selectedPort="";
            
            }else{
                
                if($value->id == 7)
                {
                    $selectedPort=$encode;
                }
           }
        }

        $data = array(
            'home'           => 'Beranda',
            'url_home'       => site_url('home'),
            'title'          => 'Ticket Summary',
            'content'        => 'ticket_summary/index',
            'port'           => $portData,
            'selectedPort'   => $selectedPort,
            'channel'        => $this->m_ticket_summary->get_channel(),
            'payment_type'   => $this->m_ticket_summary->get_payment_type(),
            'download_excel' => checkBtnAccess($this->_module, 'download_excel'),
        );
        
        if($booking_code){
            $getData= $this->m_ticket_summary->select_data("app.t_trx_booking", "where booking_code ='". $booking_code ."' ")->result();

            $created_on ="";
            $shipClass     ="";
            foreach ($getData as $key => $value){
                $booking_date = $value->created_on;
                $service    = $value->service_id;
            }


			$data['cari']         = $booking_code;
            $data['booking_date'] = $booking_date;
            $data['service']    = $service;
		}

        // print_r($data);exit;

        $this->load->view('default', $data);
    }

    public function penumpang() {
        if ($this->input->is_ajax_request()) {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->m_ticket_summary->listPenumpang();
            echo json_encode($rows);
            exit;
        }
    }

    public function kendaraan() {
        if ($this->input->is_ajax_request()) {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->m_ticket_summary->listKendaraan();
            echo json_encode($rows);
            exit;
        }
    }

    function excel_penumpang() {
        // ini_set('memory_limit','1024M');
        ini_set('memory_limit', '8190M'); // 8Giga
        $excel_name   = "Ticket Summary - Pejalan Kaki";
        $port         = $this->enc->decode($this->input->get("port"));
        $payment_type = $this->enc->decode($this->input->get("payment_type"));
        $channel      = $this->enc->decode($this->input->get("channel"));
        $cari         = $this->input->get("cari");
        $searchName   = $this->input->get("searchName");
        $dateFrom     = $this->input->get("dateFrom");
        $dateTo       = $this->input->get("dateTo");
        $merchant       = $this->enc->decode($this->input->get("merchant"));
        $outletId       = $this->input->get("outletId");
        $pelabuhan    = '';
        if ($port) {
            $pelabuhan .= '_' . $this->input->get("pelabuhan");
        }

        $penumpang = $this->m_ticket_summary->list_data($port, $payment_type, $channel, $cari, $dateFrom, $dateTo, 'penumpang', $searchName, $merchant,$outletId);

        // print_r($penumpang); exit;
        $this->load->library('XLSExcel');
        $writer   = new XLSXWriter();
        $filename = strtoupper("Ticket Summary - Pejalan Kaki" . $pelabuhan . "_" . $dateFrom . "_" . $dateTo . ".xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->setTitle($excel_name);
        $writer->setSubject($excel_name);
        $writer->setAuthor($excel_name);
        $writer->setCompany('ASDP Indonesia Ferry');
        $writer->setDescription($filename);
        $writer->setTempDir(sys_get_temp_dir());

        $sheet1 = $filename;

        $styles1 = [
            'font'       => 'Arial',
            'font-size'  => 10,
            'font-style' => 'bold',
            'halign'     => 'center',
            'valign'     => 'center',
            'border'     => 'left,right,top,bottom',
        ];

        $styles2 = [
            'font'      => 'Arial',
            'font-size' => 10,
            'valign'    => 'center',
            'border'    => 'left,right,top,bottom',
        ];

        $style_header = [
            'font'       => 'Arial',
            'font-size'  => 11,
            'font-style' => 'bold',
            'valign'     => 'center',
            'border'     => 'left,right,top,bottom',
        ];

        $header = [
            "NO."                 => "integer",
            "NOMOR TIKET"         => "string",
            "KODE BOKING"         => "string",
            "NOMOR INVOICE"         => "string",
            "TANGGAL BERANGKAT"   => "string",
            "NAMA PEMESAN"        => "string",
            "NIK"                 => "string",
            "ASAL"                => "string",
            "KELAS"               => "string",
            "TIPE PEMBAYARAN"     => "string",
            "CHANNEL"             => "string",
            "MERCHANT"           => "string",
            "TARIF"               => "integer",
            "PEMESANAN"           => "string",
            "OUTLET ID"           => "string",
            "PEMBAYARAN"          => "string",
            "CETAK BOARDING PASS" => "string",
            "GATE IN"             => "string",
            "VALIDASI"            => "string",
        ];

        foreach ($penumpang as $key => $value) {
            if ($value->pemesanan >= 0) {$value->pemesanan = format_dateTimeHis($value->pemesanan_date);} else { $value->pemesanan = '-';}
            if ($value->pembayaran == 1) {$value->pembayaran = format_dateTimeHis($value->pembayaran_date);} else { $value->pembayaran = '-';}
            if ($value->cetak_boarding == 1) {$value->cetak_boarding = format_dateTimeHis($value->cetak_boarding_date);} else { $value->cetak_boarding = '-';}
            if ($value->gate_in == 1) {$value->gate_in = format_dateTimeHis($value->gate_in_date);} else { $value->gate_in = '-';}
            if ($value->validasi == 1) {$value->validasi = format_dateTimeHis($value->validasi_date);} else { $value->validasi = '-';}
            $penumpangs[] = [
                $key + 1,
                $value->ticket_number,
                $value->booking_code,
                $value->trans_number,
                format_date($value->depart_date) . ' ' . format_time($value->depart_time_start),
                $value->customer,
                $value->id_number,
                $value->origin,
                $value->ship_class,
                $value->payment_type,
                $value->channel,
                $value->merchant_name,
                $value->fare,
                $value->pemesanan,
                $value->outlet_id,
                $value->pembayaran,
                $value->cetak_boarding,
                $value->gate_in,
                $value->validasi,
            ];
        }

        // foreach($judul_penumpang as $title){
        // $writer->writeSheetRow($sheet1, array("TICKET SUMMARY - PENUMPANG"),$style_header);
        // $writer->writeSheetRow($sheet1, array(""));
        // $writer->writeSheetRow($sheet1, $title, $styles1);
        // }

        $writer->writeSheetHeader($sheet1, $header, $styles1);
        foreach ($penumpangs as $row) {
            $writer->writeSheetRow($sheet1, $row, $styles2);
        }

        // $writer->writeSheetRow($sheet1, array("Sub Total","","",$produksi_penumpang,$pendapatan_penumpang),$style_sub);
        // $writer->writeSheetRow($sheet1, array(""));

        // $writer->writeSheetRow($sheet1, array("TOTAL JUMLAH","","",$produksi_penumpang+$produksi_kendaraan,$pendapatan_penumpang+$pendapatan_kendaraan),$style_sub);
        // $writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=4);

        $writer->writeToStdOut();
    }

    function excel_kendaraan_15122020() {
        // ini_set('memory_limit','1024M');
        ini_set('memory_limit', '8190M'); // 8Giga

        // ini_set('memory_limit', '8190M+4085'); // 8Giga
        // ini_set('memory_limit', '12275M'); // 12Giga
        // ini_set('memory_limit', '16360M'); // 16Giga

        $excel_name   = "Ticket Summary - Kendaraan";
        $port         = $this->enc->decode($this->input->get("port"));
        $payment_type = $this->enc->decode($this->input->get("payment_type"));
        $channel      = $this->enc->decode($this->input->get("channel"));
        $cari         = $this->input->get("cari");
        $searchName   = $this->input->get("searchName");
        $dateFrom     = $this->input->get("dateFrom");
        $dateTo       = $this->input->get("dateTo");
        $pelabuhan    = '';
        if ($port) {
            $pelabuhan .= '_' . $this->input->get("pelabuhan");
        }

        $kendaraan = $this->m_ticket_summary->list_data($port, $payment_type, $channel, $cari, $dateFrom, $dateTo, 'kendaraan', $searchName);

        // print_r($kendaraan); exit;

        $this->load->library('XLSExcel');
        $writer   = new XLSXWriter();
        $filename = strtoupper("Ticket Summary - Kendaraan" . $pelabuhan . "_" . $dateFrom . "_" . $dateTo . ".xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->setTitle($excel_name);
        $writer->setSubject($excel_name);
        $writer->setAuthor($excel_name);
        $writer->setCompany('ASDP Indonesia Ferry');
        $writer->setDescription($filename);
        $writer->setTempDir(sys_get_temp_dir());

        $sheet1 = $filename;

        $styles1 = [
            'font'       => 'Arial',
            'font-size'  => 10,
            'font-style' => 'bold',
            'halign'     => 'center',
            'valign'     => 'center',
            'border'     => 'left,right,top,bottom',
        ];

        $styles2 = [
            'font'      => 'Arial',
            'font-size' => 10,
            'valign'    => 'center',
            'border'    => 'left,right,top,bottom',
        ];

        $style_header = [
            'font'       => 'Arial',
            'font-size'  => 11,
            'font-style' => 'bold',
            'valign'     => 'center',
            'border'     => 'left,right,top,bottom',
        ];

        $header = [
            "NO."                 => "integer",
            "NOMOR TIKET"         => "string",
            "KODE BOKING"         => "string",
            "TANGGAL BERANGKAT"   => "string",
            "NAMA PEMESAN"        => "string",
            "NIK"                 => "string",
            "PLAT NOMOR"          => "string",
            "ASAL"                => "string",
            "KELAS"               => "string",
            "TIPE PEMBAYARAN"     => "string",
            "CHANNEL"             => "string",
            "TARIF"               => "integer",
            "PEMESANAN"           => "string",
            "PEMBAYARAN"          => "string",
            "CETAK BOARDING PASS" => "string",
            "VALIDASI"            => "string",
        ];

        foreach ($kendaraan as $key => $value) {
            if ($value->pemesanan >= 0) {$value->pemesanan = format_dateTimeHis($value->pemesanan_date);} else { $value->pemesanan = '-';}
            if ($value->pembayaran == 1) {$value->pembayaran = format_dateTimeHis($value->pembayaran_date);} else { $value->pembayaran = '-';}
            if ($value->cetak_boarding == 1) {$value->cetak_boarding = format_dateTimeHis($value->cetak_boarding_date);} else { $value->cetak_boarding = '-';}
            if ($value->validasi == 1) {$value->validasi = format_dateTimeHis($value->validasi_date);} else { $value->validasi = '-';}
            $kendaraans[] = [
                $key + 1,
                $value->ticket_number,
                $value->booking_code,
                format_date($value->depart_date) . ' ' . format_time($value->depart_time_start),
                $value->customer,
                $value->nik,
                $value->plat,
                $value->origin,
                $value->ship_class,
                $value->payment_type,
                $value->channel,
                $value->fare,
                $value->pemesanan,
                $value->pembayaran,
                $value->cetak_boarding,
                $value->validasi,
            ];
        }

        // foreach($judul_kendaraan as $title){
        // $writer->writeSheetRow($sheet1, array("TICKET SUMMARY - kendaraan"),$style_header);
        // $writer->writeSheetRow($sheet1, array(""));
        // $writer->writeSheetRow($sheet1, $title, $styles1);
        // }

        $writer->writeSheetHeader($sheet1, $header, $styles1);
        foreach ($kendaraans as $row) {
            $writer->writeSheetRow($sheet1, $row, $styles2);
        }

        // $writer->writeSheetRow($sheet1, array("Sub Total","","",$produksi_kendaraan,$pendapatan_kendaraan),$style_sub);
        // $writer->writeSheetRow($sheet1, array(""));

        // $writer->writeSheetRow($sheet1, array("TOTAL JUMLAH","","",$produksi_kendaraan+$produksi_kendaraan,$pendapatan_kendaraan+$pendapatan_kendaraan),$style_sub);
        // $writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=4);

        $writer->writeToStdOut();
    }

    function getMerchant()
    {
        $data=array();
        if ($this->input->is_ajax_request()) {
            $rows = $this->m_ticket_summary->select_data("app.t_mtr_merchant"," where status<>'-5' order by merchant_name asc ")->result();
            foreach ($rows as $key => $value) {
                $value->merchant_id = $this->enc->encode($value->merchant_id);
                $data[]=$value;
            }            
        }
        // echo json_encode($data);        
        echo json_encode(array(
            'data'             => $data,
            'csrfName'         =>$this->security->get_csrf_token_name(),
            'tokenHash'        =>$this->security->get_csrf_hash(),
        )
        );
    }
    function getOutletId()
    {
        // echo $this->input->post('merchantId'); exit;
        $merchantId=$this->enc->decode($this->input->post('merchantId'));
        $data=array();
        if ($this->input->is_ajax_request()) {
            $rows = $this->m_ticket_summary->select_data("app.t_mtr_outlet_merchant"," where status<>'-5' and merchant_id='".$merchantId."' order by outlet_id asc ")->result();
            foreach ($rows as $key => $value) {
                $data[]=$value;
            }            
        }
        // echo json_encode($data);        
        echo json_encode(array(
            'data'             => $data,
            'csrfName'         =>$this->security->get_csrf_token_name(),
            'tokenHash'        =>$this->security->get_csrf_hash(),
        )
        );

    }    
    public function excel_kendaraan() {
        // $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $excel_name   = "Ticket Summary - Kendaraan";
        $port         = $this->enc->decode($this->input->get("port"));
        $payment_type = $this->enc->decode($this->input->get("payment_type"));
        $channel      = $this->enc->decode($this->input->get("channel"));
        $cari         = $this->input->get("cari");
        $searchName   = $this->input->get("searchName");
        $dateFrom     = $this->input->get("dateFrom");
        $dateTo       = $this->input->get("dateTo");
        $merchant       = $this->enc->decode($this->input->get("merchant"));
        $outletId       = $this->input->get("outletId");
        $pelabuhan    = '';
        if ($port) {
            $pelabuhan .= '_' . $this->input->get("pelabuhan");
        }

        $kendaraan = $this->m_ticket_summary->list_data($port, $payment_type, $channel, $cari, $dateFrom, $dateTo, 'kendaraan', $searchName, $merchant, $outletId);

        // print_r($kendaraan); exit;

        $file_name = 'Ticket Summary - Kendaraan  tanggal ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = ['height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom'];

        $header = [
            "NO."                  => "integer",
            "NOMOR TIKET"          => "string",
            "KODE BOKING"          => "string",
            "NOMOR INVOICE"          => "string",
            "TANGGAL BERANGKAT"    => "string",
            "NAMA PEMESAN"         => "string",
            "NIK"                  => "string",
            "PLAT NOMOR"           => "string",
            "ASAL"                 => "string",
            "KELAS"                => "string",
            "GOLONGAN"             => "string",
            "TIPE PEMBAYARAN"      => "string",
            "CHANNEL"              => "string",
            "MERCHANT"            => "string",
            "TARIF"                => "integer",
            "PEMESANAN"            => "string",
            "OUTLET ID"            => "string",
            "PEMBAYARAN"           => "string",
            "CETAK BOARDING PASS"  => "string",
            "VALIDASI"             => "string",
            // 'PANJANG'             => 'string',
            // 'TINGGI'              => 'string',
            // 'LEBAR'               => 'string',
            // 'BERAT'               => 'string',
            'PANJANG DARI SENSOR'  => 'string',
            'TINGGI DARI SENSOR'   => 'string',
            'LEBAR DARI SENSOR'    => 'string',
            'BERAT DARI TIMBANGAN' => 'string',

        ];

        $no = 1;
        foreach ($kendaraan as $key => $value) {
            if ($value->pemesanan >= 0) {$value->pemesanan = format_dateTimeHis($value->pemesanan_date);} else { $value->pemesanan = '-';}
            if ($value->pembayaran == 1) {$value->pembayaran = format_dateTimeHis($value->pembayaran_date);} else { $value->pembayaran = '-';}
            if ($value->cetak_boarding == 1) {$value->cetak_boarding = format_dateTimeHis($value->cetak_boarding_date);} else { $value->cetak_boarding = '-';}
            if ($value->validasi == 1) {$value->validasi = format_dateTimeHis($value->validasi_date);} else { $value->validasi = '-';}

            $rows[] = [
                $key + 1,
                $value->ticket_number,
                $value->booking_code,
                $value->trans_number,
                format_date($value->depart_date) . ' ' . format_time($value->depart_time_start),
                $value->customer,
                $value->nik,
                $value->plat,
                $value->origin,
                $value->ship_class,
                $value->vehicle_class,
                $value->payment_type,
                $value->channel,
                $value->merchant_name,
                $value->fare,
                $value->pemesanan,
                $value->outlet_id,
                $value->pembayaran,
                $value->cetak_boarding,
                $value->validasi,
                // $value->length,
                // $value->height,
                // $value->width,
                // $value->weight,
                $value->length_cam,
                $value->height_cam,
                $value->width_cam,
                $value->weighbridge,
            ];
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header, $styles1);

        foreach ($rows as $row) {
            $writer->writeSheetRow('Sheet1', $row);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }

    function excel_kendaraan_csv() {
        $port         = $this->enc->decode($this->input->get("port"));
        $payment_type = $this->enc->decode($this->input->get("payment_type"));
        $channel      = $this->enc->decode($this->input->get("channel"));
        $cari         = $this->input->get("cari");
        $searchName   = $this->input->get("searchName");
        $dateFrom     = $this->input->get("dateFrom");
        $dateTo       = $this->input->get("dateTo");
        $pelabuhan    = '';
        if ($port) {
            $pelabuhan .= '_' . $this->input->get("pelabuhan");
        }

        $kendaraan = $this->m_ticket_summary->list_data($port, $payment_type, $channel, $cari, $dateFrom, $dateTo, 'kendaraan', $searchName);

        // print_r($kendaraan); exit;
        $file_name = 'Ticket Summary - Kendaraan  tanggal ' . $dateFrom . ' s/d ' . $dateTo;

        $this->load->library('PHPExcel');

        // Panggil class PHPExcel nya
        $csv = new PHPExcel();
        // Settingan awal fil excel
        $csv->getProperties()->setCreator('My Notes Code')
            ->setLastModifiedBy('My Notes Code')
            ->setTitle("Data Summary Kendaraan")
            ->setSubject("Data Summary Kendaraan")
            ->setDescription($file_name)
            ->setKeywords($file_name);
        // Buat header tabel nya pada baris ke 1

            $csv->setActiveSheetIndex(0)->setCellValue("A1","NO.");
            $csv->setActiveSheetIndex(0)->setCellValue("B1","NOMOR TIKET");
            $csv->setActiveSheetIndex(0)->setCellValue("C1","KODE BOKING");
            $csv->setActiveSheetIndex(0)->setCellValue("D1","NOMOR INVOICE");
            $csv->setActiveSheetIndex(0)->setCellValue("E1","TANGGAL BERANGKAT");
            $csv->setActiveSheetIndex(0)->setCellValue("F1","NAMA PEMESAN");
            $csv->setActiveSheetIndex(0)->setCellValue("G1","NIK");
            $csv->setActiveSheetIndex(0)->setCellValue("H1","PLAT NOMOR");
            $csv->setActiveSheetIndex(0)->setCellValue("I1","ASAL");
            $csv->setActiveSheetIndex(0)->setCellValue("J1","KELAS");
            $csv->setActiveSheetIndex(0)->setCellValue("K1","TIPE PEMBAYARAN");
            $csv->setActiveSheetIndex(0)->setCellValue("L1","CHANNEL");
            $csv->setActiveSheetIndex(0)->setCellValue("M1","MERCHANT");
            $csv->setActiveSheetIndex(0)->setCellValue("N1","TARIF");
            $csv->setActiveSheetIndex(0)->setCellValue("O1","PEMESANAN");
            $csv->setActiveSheetIndex(0)->setCellValue("P1","OUTLET ID");
            $csv->setActiveSheetIndex(0)->setCellValue("Q1","PEMBAYARAN");
            $csv->setActiveSheetIndex(0)->setCellValue("R1","CETAK BOARDING PASS");
            $csv->setActiveSheetIndex(0)->setCellValue("S1","VALIDASI");
            $csv->setActiveSheetIndex(0)->setCellValue("T1",'PANJANG DARI SENSOR');
            $csv->setActiveSheetIndex(0)->setCellValue("U1",'TINGGI DARI SENSOR');
            $csv->setActiveSheetIndex(0)->setCellValue("V1",'LEBAR DARI SENSOR');
            $csv->setActiveSheetIndex(0)->setCellValue("W1",'BERAT DARI TIMBANGAN');

        $no     = 1; // Untuk penomoran tabel, di awal set dengan 1
        $numrow = 2; // Set baris pertama untuk isi tabel adalah baris ke 2

        foreach ($kendaraan as $key => $value) {
            # code...
            $csv->setActiveSheetIndex(0)->setCellValue('A' . $numrow, $no);
            $csv->setActiveSheetIndex(0)->setCellValue('B' . $numrow, $value->ticket_number);
            $csv->setActiveSheetIndex(0)->setCellValue('C' . $numrow, $value->booking_code);
            $csv->setActiveSheetIndex(0)->setCellValue('D' . $numrow, $value->trans_number);
            $csv->setActiveSheetIndex(0)->setCellValue('E' . $numrow, format_date($value->depart_date) . ' ' . format_time($value->depart_time_start));
            $csv->setActiveSheetIndex(0)->setCellValue('F' . $numrow, $value->customer);
            $csv->setActiveSheetIndex(0)->setCellValue('G' . $numrow, $value->nik);
           $csv->setActiveSheetIndex(0)->setCellValue('H' . $numrow, $value->plat);
            $csv->setActiveSheetIndex(0)->setCellValue('I' . $numrow, $value->origin);
            $csv->setActiveSheetIndex(0)->setCellValue('J' . $numrow, $value->ship_class);
            $csv->setActiveSheetIndex(0)->setCellValue('K' . $numrow, $value->payment_type);
            $csv->setActiveSheetIndex(0)->setCellValue('L' . $numrow, $value->channel);
            $csv->setActiveSheetIndex(0)->setCellValue('M' . $numrow, $value->merchant_name);
            $csv->setActiveSheetIndex(0)->setCellValue('N' . $numrow, $value->fare);
            $csv->setActiveSheetIndex(0)->setCellValue('O' . $numrow, $value->pemesanan);
            $csv->setActiveSheetIndex(0)->setCellValue('P' . $numrow, $value->outlet_id);
            $csv->setActiveSheetIndex(0)->setCellValue('Q' . $numrow, $value->pembayaran);
            $csv->setActiveSheetIndex(0)->setCellValue('R' . $numrow, $value->cetak_boarding);
            $csv->setActiveSheetIndex(0)->setCellValue('S' . $numrow, $value->validasi);
            $csv->setActiveSheetIndex(0)->setCellValue('T' . $numrow, $value->length_cam);
            $csv->setActiveSheetIndex(0)->setCellValue('U' . $numrow, $value->height_cam);
            $csv->setActiveSheetIndex(0)->setCellValue('V' . $numrow, $value->width_cam);
            $csv->setActiveSheetIndex(0)->setCellValue('W' . $numrow, $value->weighbridge);

            $no++;
            $numrow++;
        }

        // Set orientasi kertas jadi LANDSCAPE
        $csv->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        // Set judul file excel nya
        $csv->getActiveSheet(0)->setTitle("Laporan Data Transaksi");
        $csv->setActiveSheetIndex(0);
        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Data Sumary.csv"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $write = new PHPExcel_Writer_CSV($csv);
        $write->save('php://output');
    }
}