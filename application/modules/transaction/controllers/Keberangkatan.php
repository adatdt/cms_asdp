<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Keberangkatan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('m_keberangkatan', 'berangkat');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_booking';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/keberangkatan';

        // $this->dbView = $this->load->database("dbView", TRUE);
        $this->dbView = checkReplication();
        $this->dbAction = $this->load->database("dbAction", TRUE);
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->berangkat->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity = $this->berangkat->get_identity_app();

        if ($get_identity == 0) {
            // mengambil port berdasarkan port di user menggunakan session
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->berangkat->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id') . " ")->result();
                $row_port = 1;
            } else {
                $port = $this->berangkat->select_data("app.t_mtr_port", "where status not in (-5) order by name asc")->result();
                $row_port = 0;
            }
        } else {
            $port = $this->berangkat->select_data("app.t_mtr_port", "where id=" . $get_identity . " ")->result();
            $row_port = 1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Keberangkatan Kendaraan',
            'content'  => 'keberangkatan/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add')),
            'ship_class'  => $this->berangkat->select_data("app.t_mtr_ship_class", "where status=1 order by name asc")->result(),
            'port' => $port,
            'row_port' => $row_port,
            'destination' => $this->berangkat->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result(),
            'transaction_type' => $this->berangkat->select_data("app.t_mtr_transaction_type","where status not in (-5) order by id ASC")->result(),
            'status_type' => $this->berangkat->select_data("app.t_mtr_status","where tbl_name = 't_trx_berangkat' AND status<>'-5' order by id ASC")->result(),
            'team' => $this->berangkat->select_data("core.t_mtr_team","where status = 1")->result(),
            'btn_excel' => checkBtnAccess($this->_module,'download_excel'),
        );

        $this->load->view('default', $data);
    }

    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("dateFrom");
        $dateTo = $this->input->get("dateTo");

        $data = $this->berangkat->download()->result();

        $file_name = 'Keberangkatan Kendaraan tanggal ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $header = array(
            'NO' => 'string',
            'KODE BOOKING' => 'string',
            'PELABUHAN' => 'string',
            'KELAS LAYANAN' => 'string',
            'TANGGAL BERANGKAT' => 'string',
            'JAM BERANGKAT' => 'string',
            'GOLONGAN KENDARAAN' => 'string',
            'TANGGAL RESERVASI' => 'string',
            'JAM RESERVASI' => 'string',
            'TANGGAL CHECKIN' => 'string',
            'JAM CHECKIN' => 'string',
        );

        $no = 1;

        foreach ($data as $key => $value) {
            $rows[] = array(
                $no,
                $value->booking_code,
                $value->port_name,
                $value->ship_class,
                $value->depart_date,
                $value->depart_time_start,
                $value->vehicle_class,
                $value->reservation_date,
                $value->reservation_time,
                $value->checkin_date,
                $value->checkin_time,
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
}
