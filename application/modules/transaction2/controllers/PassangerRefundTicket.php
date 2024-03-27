<?php
defined('BASEPATH') || exit('No direct script access allowed');

class PassangerRefundTicket extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('passangerRefundTicketModel', 'passanger');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_booking';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction2/passangerRefundTicket';
        $this->load->library('Html2pdf');
        
        $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbAction=$this->load->database("dbAction",TRUE); 

    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->passanger->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity = $this->passanger->get_identity_app();
        // port berdasarkan user

        $getRoute = array();
        if ($get_identity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->passanger->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id') . "");
                $getRoute = $this->passanger->getRoute($this->session->userdata('port_id'))->result();
            } else {
                $dataPort[""]   = "Pilih";
                $dataRoute[""]  = "Pilih";
                $port = $this->passanger->select_data("app.t_mtr_port", "where status not in (-5) order by name asc");
            }
        } else {
            $port = $this->passanger->select_data("app.t_mtr_port", "where id=" . $get_identity . "");
            $getRoute = $this->passanger->getRoute($get_identity)->result();
        }

        $statusRefund[""] = "Pilih";
        $statusRefund[1] = "PROSES MANUS";
        $statusRefund[2] = "PROSES MANKEU";
        $statusRefund[3] = "NEED TO RESUBMIT";
        $statusRefund[4] = "REFUNDED";
        

        $dataShipClass[""] = "Pilih";

        $getShipClass = $this->passanger->select_data("app.t_mtr_ship_class", " where status<>'-5' order by name asc")->result();

        foreach ($port->result() as $key => $value) {
            $dataPort[$this->enc->encode($value->id)] = strtoupper($value->name);
        }

        foreach ($getRoute as $key => $value) {
            $dataRoute[$this->enc->encode($value->id)] = strtoupper($value->route_name);
        }

        foreach ($getShipClass as $key => $value) {
            $dataShipClass[$this->enc->encode($value->id)] = strtoupper($value->name);
        }

        $data = array(
            'home'          => 'Home',
            'url_home'      => site_url('home'),
            'title'         => 'Refund Tiket PNP',
            'content'       => 'passangerRefundTicket/index',
            'service'       => $this->passanger->select_data("app.t_mtr_service", "where status=1 order by name asc")->result(),
            'port'          => $dataPort,
            'route'         => $dataRoute,
            'shipClass'     => $dataShipClass,
            'bank'          => $this->passanger->get_bank(),
            'statusRefund'  => $statusRefund,
            'destination'   => $port = $this->passanger->select_data("app.t_mtr_port", "where status not in (-5) order by name asc")->result(),
            'team'          => $this->passanger->select_data("core.t_mtr_team", "where status=1 order by team_name asc")->result(),
            'btn_excel'     => checkBtnAccess($this->_module, 'download_excel'),
            'btn_pdf'       => checkBtnAccess($this->_module, 'download_pdf'),
        );
        $this->load->view('default', $data);
    }


    public function getRoute()
    {
        $port = $this->enc->decode($this->input->post("port"));


        $dataRoute = array();

        if (!empty($port)) {

            $route = $this->passanger->getRoute($port)->result();

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

        $dateFrom   = $this->input->get("dateFrom");
        $dateTo     = $this->input->get("dateTo");
        $data       = $this->passanger->download()->result();
        $file_name  = 'Refund Tiket PNP ' . $dateFrom . ' s/d ' . $dateTo;

        $this->load->library('XLSExcel');

        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $header = array(
            "NO"                        => "string",
            "KODE BOOKING"              => "string",
            "NO TIKET"                  => "string",
            "GOLONGAN"                  => "string",
            "KELAS LAYANAN"             => "string",
            "TARIF GOLONGAN (Rp)"       => "string",
            "WAKTU PEMBAYARAN"          => "string",
            "JADWAL KEBERANGKATAN"      => "string",
            "LINTASAN DIPESAN"          => "string",
            "STATUS TIKET"              => "string",
            "STATUS TIKET REFUND"       => "string",
            "WAKTU APPROVAL"            => "string",
            "TOTAL BIAYA ADMINISTRASI"  => "string",
            "BIAYA ADMINISTRASI REFUND" => "string",
            "BIAYA REFUND"              => "string",
            "BANK TUJUAN"               => "string",
            "NOMOR REKENING"            => "string",
            "PENGEMBALIAN REFUND/REROUTE/SELISIH GOL" => "string",
        );

        $no = 1;
        foreach ($data as $key => $value) {

            $rows[] = array(
                $no,
                $value->booking_code,
                $value->ticket_number,
                $value->passanger_type_name,
                $value->ship_class_name,
                $value->fare,
                $value->payment_date,
                $value->depart_date,
                $value->route_name,
                $value->status_booking,
                $value->status_refund,
                $value->tanggal_approve,
                $value->total_biaya,
                $value->biaya_admin,
                $value->biaya_refund,
                $value->bank_tujuan,
                $value->no_rekening,
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
            $getDataPort = $this->passanger->select_data("app.t_mtr_port", " where id='{$port}' ")->row();

            $portName = strtoupper($getDataPort->name);
        }

        $data['data'] = $this->passanger->download()->result();
        $data['port'] = $portName;
        $data['departDateFrom'] = $dateFrom;
        $data['departDateTo'] = $dateTo;

        // echo"<pre>";
        // print_r($data); exit;
        $this->load->view('passangerRefundTicket/pdf', $data);
    }
}
