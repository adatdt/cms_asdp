<?php
defined('BASEPATH') or exit('No direct script access allowed');
// error_reporting(0);

class VehicleTicketRefund extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('vehicleTicketRefundModel', 'vehicle');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_booking';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction2/vehicleTicketRefund';
        $this->load->library('Html2pdf');

        $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbAction=$this->load->database("dbAction",TRUE); 
        
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->vehicle->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity = $this->vehicle->get_identity_app();
        // port berdasarkan user

        $getRoute = array();
        if ($get_identity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->vehicle->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id') . "");
                $getRoute = $this->vehicle->getRoute($this->session->userdata('port_id'))->result();
            } else {
                $dataPort[""] = "Pilih";
                $dataRoute[""] = "Pilih";
                $port = $this->vehicle->select_data("app.t_mtr_port", "where status not in (-5) order by name asc");
            }
        } else {
            $port = $this->vehicle->select_data("app.t_mtr_port", "where id=" . $get_identity . "");
            $getRoute = $this->vehicle->getRoute($get_identity)->result();
        }


        $dataShipClass[""] = "Pilih";
        $getShipClass = $this->vehicle->select_data("app.t_mtr_ship_class", " where status<>'-5' order by name asc")->result();


        foreach ($port->result() as $key => $value) {
            $dataPort[$this->enc->encode($value->id)] = strtoupper($value->name);
        }

        foreach ($getRoute as $key => $value) {
            $dataRoute[$this->enc->encode($value->id)] = strtoupper($value->route_name);
        }

        foreach ($getShipClass as $key => $value) {
            $dataShipClass[$this->enc->encode($value->id)] = strtoupper($value->name);
        }

        $statusRefund[""] = "Pilih";
        $statusRefund[1] = "PROSES MANUS";
        $statusRefund[2] = "PROSES MANKEU";
        $statusRefund[3] = "NEED TO RESUBMIT";
        $statusRefund[4] = "REFUNDED";

        $bank[""] = "Pilih";
        $getBank = $this->vehicle->select_data("core.t_mtr_bank", " where status<>'-5' order by bank_name asc")->result();
        foreach ($getBank as $key => $value) {
            $bank[strtoupper($value->bank_name)] = strtoupper($value->bank_name);
        }


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Refund Tiket Kendaraan',
            'content'  => 'vehicleTicketRefund/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add')),
            'service'  => $this->vehicle->select_data("app.t_mtr_service", "where status=1 order by name asc")->result(),
            'port' => $dataPort,
            'route' => $dataRoute,
            'shipClass' => $dataShipClass,
            'channel' => $this->vehicle->get_channel(),
            'statusRefund' => $statusRefund,
            'bank' => $bank,
            'destination' => $port = $this->vehicle->select_data("app.t_mtr_port", "where status not in (-5) order by name asc")->result(),
            'team' => $this->vehicle->select_data("core.t_mtr_team", "where status=1 order by team_name asc")->result(),
            'btn_excel' => checkBtnAccess($this->_module, 'download_excel'),
            'btn_pdf' => checkBtnAccess($this->_module, 'download_pdf'),
        );

        $this->load->view('default', $data);
    }


    public function getRoute()
    {
        $port = $this->enc->decode($this->input->post("port"));


        $dataRoute = array();

        if (!empty($port)) {

            $route = $this->vehicle->getRoute($port)->result();

            foreach ($route as $key => $value) {
                $value->id = $this->enc->encode($value->id);
                $value->route_name = strtoupper($value->route_name);

                $dataRoute[] = $value;
            }
        }

        echo json_encode($dataRoute);
    }

    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("dateFrom");
        $dateTo = $this->input->get("dateTo");

        $data = $this->vehicle->download()->result();

        // print_r($data); exit;

        $file_name = 'Tiket Refund Kendaraan ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 60], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $header = array(
            "NO" => "string",
            "KODE BOOKING" => "string",
            "NO TIKET" => "string",
            "PANJANG PADA PEMESANAN (METER)" => "string",
            "GOLONGAN" => "string",
            "KELAS LAYANAN" => "string",
            "TARIF GOLONGAN (Rp.)" => "string",
            "WAKTU PEMBAYARAN" => "string",
            "JADWAL KEBERANGKATAN" => "string",
            "LINTASAN DIPESAN" => "string",
            "STATUS TIKET REFUND" => "string",
            "TOTAL BIAYA ADMINISTRASI" => "string",
            "BIAYA ADMINISTRASI REFUND" => "string",
            "BIAYA REFUND" => "string",
            "NO REKENING" => "string",
            "BANK TUJUAN" => "string",
            "PENGEMBALIAN REFUND (Tidak dikurangi Charge Transfer Bank)" => "string",
        );
        $rows = [];

        $no = 1;
        foreach ($data as $key => $value) {

            $rows[] = array(
                $no,
                $value->booking_code,
                $value->ticket_number,
                $value->length_vehicle,
                $value->vehicle_class_name,
                $value->ship_class_name,
                $value->fare,
                $value->payment_date,
                $value->keberangkatan,
                $value->route_name,
                $value->status_refund,
                $value->charge_amount,
                $value->adm_fee,
                $value->refund_fee,
                $value->account_number,
                $value->bank,
                $value->total_amount,
            );
            $no++;
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header, $styles1);

        foreach ($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }

    public function download_pdf()
    {
        // $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("dateFrom");
        $dateTo = $this->input->get("dateTo");
        $port = $this->enc->decode($this->input->get("port"));

        $portName = "";
        if (!empty($port)) {
            $getDataPort = $this->vehicle->select_data("app.t_mtr_port", " where id='{$port}' ")->row();

            $portName = strtoupper($getDataPort->name);
        }

        $data['data'] = $this->vehicle->download()->result();
        $data['port'] = $portName;
        $data['departDateFrom'] = $dateFrom;
        $data['departDateTo'] = $dateTo;

        // print_r($data); exit;

        // echo "hai";
        $this->load->view('vehicleTicketRefund/pdf', $data);
    }
}
