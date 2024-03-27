<?php

error_reporting(0);

class Ticket_expired_v2 extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Html2pdf');
        $this->load->model('Ticket_expired_v2_model', 'model_');
        $this->_module     = 'laporan/ticket_expired_v2';
        $this->report_name = "tiket_expired_v2";
        $this->report_code = $this->global_model->get_report_code($this->report_name);

        $this->dbView = $this->load->database("dbView", TRUE);
        $this->dbAction = $this->load->database("dbAction", TRUE);        
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $this->input->get('s') == 'kendaraan' ? $statusData = true : $statusData = false;
            $get_data = $this->model_->dataList($statusData);
            $rows_data = $get_data->result();

            $rows     = array();
            $i      =  1;

            foreach ($rows_data as $row) {
                $row->ss = $statusData;
                $row->number = $i;

                $row->fare               = idr_currency($row->fare);
                $row->transfer_dana      = idr_currency($row->transfer_dana);
                $row->payment_date       = empty($row->payment_date) ? "" : format_date($row->payment_date) . " " . format_time($row->payment_date);
                $row->keberangkatan      = empty($row->keberangkatan) ? "" : format_date($row->keberangkatan);
                $row->tanggal_pengakuan  = empty($row->tanggal_pengakuan) ? "" : format_date($row->tanggal_pengakuan);
                $row->pendapatan_expired = $row->pendapatan_expired == 0 ? "" : idr_currency($row->pendapatan_expired);

                $rows[] = $row;
                unset($row->id);

                $i++;
            }

            echo json_encode($rows);
        } else {

            $get_identity = $this->model_->get_identity_app();

            $getRoute = array();
            if ($get_identity == 0) {
                if (!empty($this->session->userdata('port_id'))) {
                    $port     = $this->model_->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id') . "");
                    $getRoute = $this->model_->getRoute($this->session->userdata('port_id'))->result();
                } else {
                    $dataPort[""]  = "Pilih";
                    $dataRoute[""] = "Pilih";
                    $port          = $this->model_->select_data("app.t_mtr_port", "where status not in (-5) order by name asc");
                }
            } else {
                $port       = $this->model_->select_data("app.t_mtr_port", "where id=" . $get_identity . "");
                $getRoute   = $this->model_->getRoute($get_identity)->result();
            }

            foreach ($port->result() as $key => $value) {
                $dataPort[$this->enc->encode($value->id)] = strtoupper($value->name);
            }

            foreach ($getRoute as $key => $value) {
                $dataRoute[$this->enc->encode($value->id)] = strtoupper($value->route_name);
            }

            $statusExpired = array(
                "" => "Pilih",
                $this->enc->encode(10) => "Check In Expired",
                $this->enc->encode(11) => "Gate In Expired",
                $this->enc->encode(12) => "Boarding Expired",

            );

            $data = array(
                'home'           => 'Beranda',
                'url_home'       => site_url('home'),
                'title'          => 'Tiket Expired',
                'content'        => 'ticket_expired_v2/index',
                'port'           => $dataPort,
                'route'          => $dataRoute,
                'shipClass'      => $this->option_shift_class(),
                'statusExpired'  => $statusExpired,
                'channel'        => $this->model_->get_channel(),
                'destination'    => $this->model_->select_data("app.t_mtr_port", "where status not in (-5) order by name asc")->result(),
                'download_pdf'   => checkBtnAccess($this->_module, 'download_pdf'),
                'download_excel' => checkBtnAccess($this->_module, 'download_excel'),
            );

            $this->load->view('default', $data);
        }
    }

    private function option_shift_class()
    {
        $shift_class = $this->model_->getClassBySession('option');
        foreach ($shift_class as $row) {
            if ($row['id'] != '') {
                $id = $this->enc->encode($row['id']);
            } else {
                $id = '';
            }
            $html .= '<option value="' . $id . '">' . $row['name'] . '</option>';
        }
        return $html;
    }

    public function getRoute()
    {
        $port = $this->enc->decode($this->input->post("port"));

        $dataRoute = array();

        if (!empty($port)) {

            $route = $this->model_->getRoute($port)->result();

            foreach ($route as $key => $value) {
                $value->id = $this->enc->encode($value->id);
                $value->route_name = strtoupper($value->route_name);

                $dataRoute[] = $value;
            }
        }

        echo json_encode($dataRoute);
    }

    public function download_pdf()
    {
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $route           = $this->enc->decode($this->input->get('route'));
        $dateTo          = trim($this->input->get('dateTo'));
        $dateFrom        = trim($this->input->get('dateFrom'));
        $paymentDateFrom = trim($this->input->get('paymentDateFrom'));
        $paymentDateTo   = trim($this->input->get('paymentDateTo'));
        $statusExpired   = $this->enc->decode($this->input->get('statusExpired'));
        $port            = $this->enc->decode($this->input->get('port_origin'));
        $status_data     = $this->input->get('statusData');
        $cek_sc 	     = $this->model_->getClassBySession();

		if ($cek_sc == false) {
			$shipClass    = $this->enc->decode($this->input->get("shipClass"));
			$shipClassName = $this->input->get("shipClass");
		} else {
			$shipClass    = $cek_sc['id'];
			$shipClassName = $cek_sc['name'];
		}

        if ($paymentDateFrom == '' && $paymentDateTo == '') {
            $waktu_pembayaran = '-';
        } else if ($paymentDateFrom == '' && $paymentDateTo != '') {
            $waktu_pembayaran = format_date($paymentDateTo);
        } else if ($paymentDateTo == '' && $paymentDateFrom != '') {
            $waktu_pembayaran = format_date($paymentDateFrom);
        } else {
            $waktu_pembayaran = format_date($paymentDateFrom) . " - " . format_date($paymentDateTo);
        }

        $portName = "";
        if (!empty($port)) {
            $getDataPort = $this->model_->select_data("app.t_mtr_port", " where id='{$port}' ")->row();

            $portName = strtoupper($getDataPort->name);
        }

        $routeName = "";
        if (!empty($route)) {
            $getRoute = $this->model_->getSingleRoute($route)->row();

            $routeName = strtoupper($getRoute->route_name);
        }

        $shipClassName = "";
        if (!empty($shipClass)) {
            $getShipClass = $this->model_->select_data("app.t_mtr_ship_class", " where id='{$shipClass}'")->row();
            $shipClassName = strtoupper($getShipClass->name);
        }

        if ($statusExpired == 10) {
            $statusExpiredName = "Check In Expired";
        } elseif ($statusExpired == 11) {
            $statusExpiredName = "Gate In Expired";
        } elseif ($statusExpired == 12) {
            $statusExpiredName = "Boarding Expired";
        } else {
            $statusExpiredName = "Semua";
        }

        $status_data == 'kendaraan' ? $statusData = true : $statusData = false;

        $data['data'] = $this->model_->dataList($statusData)->result();

        $data['waktu_keberangkatan'] = format_date($dateFrom) . ' - ' . format_date($dateTo);
        $data['waktu_pembayaran'] = $waktu_pembayaran;
        $data['port'] = $portName == '' ? 'SEMUA' : $portName;
        $data['ship_class_name'] = $shipClassName;
        $data['route_name'] = $routeName == '' ? 'SEMUA' : $routeName;
        $data['status_expired_name'] = strtoupper($statusExpiredName);
        $data['status_data'] = $status_data;
        // echo"<pre>";
        // print_r($data); die();

        $this->load->view($this->_module . '/pdf', $data);
        // $this->load->view('laporan/tiket_refund/pdf', $data);
    }

    public function download_excel()
    {
        $shipClass = $this->enc->decode($this->input->get('shipClass'));
        $route = $this->enc->decode($this->input->get('route'));
        $dateTo = trim($this->input->get('dateTo'));
        $dateFrom = trim($this->input->get('dateFrom'));
        $paymentDateFrom = trim($this->input->get('paymentDateFrom'));
        $paymentDateTo = trim($this->input->get('paymentDateTo'));
        $statusExpired = $this->enc->decode($this->input->get('statusExpired'));
        $port = $this->enc->decode($this->input->get('port_origin'));
        $status_data = $this->input->get('statusData');



        if ($paymentDateFrom == '' && $paymentDateTo == '') {
            $waktu_pembayaran = '-';
        } else if ($paymentDateFrom == '' && $paymentDateTo != '') {
            $waktu_pembayaran = format_date($paymentDateTo);
        } else if ($paymentDateTo == '' && $paymentDateFrom != '') {
            $waktu_pembayaran = format_date($paymentDateFrom);
        } else {
            $waktu_pembayaran = format_date($paymentDateFrom) . " - " . format_date($paymentDateTo);
        }

        $portName = "";
        if (!empty($port)) {
            $getDataPort = $this->model_->select_data("app.t_mtr_port", " where id='{$port}' ")->row();

            $portName = strtoupper($getDataPort->name);
        }

        $routeName = "";
        if (!empty($route)) {
            $getRoute = $this->model_->getSingleRoute($route)->row();

            $routeName = strtoupper($getRoute->route_name);
        }

        $shipClassName = "";
        if (!empty($shipClass)) {
            $getShipClass = $this->model_->select_data("app.t_mtr_ship_class", " where id='{$shipClass}'")->row();
            $shipClassName = strtoupper($getShipClass->name);
        }

        if ($statusExpired == 10) {
            $statusExpiredName = "Check In Expired";
        } elseif ($statusExpired == 11) {
            $statusExpiredName = "Gate In Expired";
        } elseif ($statusExpired == 12) {
            $statusExpiredName = "Boarding Expired";
        } else {
            $statusExpiredName = "Semua";
        }

        if ($status_data == 'kendaraan') {
            $statusData = true;
            $ds = 'KENDARAAN';
            $length_column = 12;
        } else {
            $statusData = false;
            $ds = 'PENUMPANG';
            $length_column = 11;
        }


        $data = $this->model_->dataList($statusData)->result();

        $Swaktu_keberangkatan = format_date($dateFrom) . ' - ' . format_date($dateTo);
        $Swaktu_pembayaran = $waktu_pembayaran;
        $Sport = $portName == '' ? 'SEMUA' : $portName;
        $Sship_class_name = $shipClassName == '' ? 'SEMUA' : $shipClassName;
        $Sroute_name = $routeName == '' ? 'SEMUA' : $routeName;
        $Sstatus_expired_name = strtoupper($statusExpiredName);


        $file_name = strtoupper("Laporan_tiket_expired_" . $ds . "_" . $Sport . "_" . $Swaktu_keberangkatan . "_" . $Sship_class_name);
        $sheetsName = 'Laporan EXpired';
        $this->load->library('XLSExcel');
        $styleHeader = array(
            'height' => 50,
            'font' => 'Arial',
            'font-size' => 14,
            'font-style' => 'bold',
            'valign' => 'center',
            'halign' => 'center'
        );
        $styleSearch = array(
            'font' => 'arial'
        );

        // $styles1 = array('height' => 50, 'widths' => [2, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 60], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');
        $styleHeader = array(
            'height' => 30,
            'font' => 'Arial',
            'widths' => [60, 40, 40, 40, 40, 40, 40, 40, 40, 60, 60, 60, 60, 60],
            'suppress_row' => 1,
            'font-size' => 10,
            'font-style' => 'bold',
            'valign' => 'center',
            'halign' => 'center',
        );
        $styleHeaderTitle = array(
            'height' => 60,
            'font' => 'Arial',
            'font-size' => 10,
            'font-style' => 'bold',
            'valign' => 'center',
            'halign' => 'center',
            'border' => 'left,right,top,bottom',
            'border-style' => 'thin'
        );
        $styles1 = array(
            'widths' => [160, 160, 160, 160, 160, 160, 160, 160],
            'border' => 'left,right,top,bottom',
            'font' => 'Arial',
            'border-style' => 'thin'
        );

        $arraySearch = array(
            array("Waktu Keberangkatan", "", $Swaktu_keberangkatan),
            array("Waktu Pembayaran", "", $Swaktu_pembayaran),
            array("Pelabuhan", "", strtoupper($Sport)),
            array("Lintasan", "", strtoupper($Sroute_name)),
            array("Kelas Layanan", "", strtoupper($Sship_class_name)),
            array("Status Expired", "", strtoupper($Sstatus_expired_name))
        );


        $writer = new XLSXWriter();

        $writer->markMergedCell($sheetsName, 0, 0, 0, $length_column);
        $writer->markMergedCell($sheetsName, 1, 0, 1, $length_column);

        // Header Top
        $writer->writeSheetRow($sheetsName, array("LAPORAN TIKET EXPIRED " . $ds), $styleHeader);

        // Search Form
        $writer->writeSheetRow($sheetsName, array());

        $noSearchRow = 0;
        foreach ($arraySearch as $row) {
            $writer->markMergedCell($sheetsName, $noSearchRow + 2, 0, $noSearchRow + 2, 1);
            $writer->markMergedCell($sheetsName, $noSearchRow + 2, 2, $noSearchRow + 2, $length_column);
            $writer->writeSheetRow($sheetsName, $row, $styleSearch);
            $noSearchRow++;
        }

        $writer->writeSheetRow($sheetsName, array());
        $arrayHeaderTable = array(
            "KODE BOOKING",
            "NO TIKET",
            "PANJANG PADA PEMESANAN (METER)",
            "GOLONGAN",
            "KELAS LAYANAN",
            "TARIF GOLONGAN (Rp.)",
            "WAKTU PEMBAYARAN",
            "JADWAL KEBERANGKATAN",
            "LINTASAN DIPESAN",
            "STATUS TIKET",
            "STATUS EXPIRED",
            "WAKTU PENGAKUAN PENDAPATAN",
            "JUMLAH PENDAPATAN EXPIRED"
        );

        if ($status_data != 'kendaraan') {
            unset($arrayHeaderTable[2]);
        }

        $writer->writeSheetRow($sheetsName, $arrayHeaderTable, $styleHeaderTitle);


        foreach ($data as $row) {
            $array_include = array(
                $row->booking_code,
                $row->ticket_number,
                $row->length_vehicle,
                $row->golongan,
                $row->ship_class_name,
                $row->fare,
                $row->payment_date,
                $row->keberangkatan,
                $row->route_name,
                $row->description,
                $row->description_expired,
                $row->tanggal_pengakuan,
                $row->pendapatan_expired,
            );
            if ($status_data != 'kendaraan') {
                unset($array_include[2]);
            }
            $writer->writeSheetRow($sheetsName, $array_include, $styles1);
        }



        // foreach ($rows as $row)
        //     $writer->writeSheetRow($sheetsName, $row);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }
}
