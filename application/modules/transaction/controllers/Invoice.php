<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('m_invoice', 'invoice');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_invoice';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/invoice';
        // $this->_module   = 'module_testing/invoice';


        // $this->dbView = $this->load->database("dbView", TRUE);
        $this->dbView=checkReplication();
        $this->dbAction = $this->load->database("dbAction", TRUE);
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->invoice->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity = $this->invoice->get_identity_app();

        if ($get_identity == 0) {
            // mengambil port berdasarkan port di user menggunakan session
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->invoice->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id') . " ")->result();
                $row_port = 1;
            } else {
                $port = $this->invoice->select_data("app.t_mtr_port", "where status not in (-5) order by name asc")->result();
                $row_port = 0;
            }
        } else {
            $port = $this->invoice->select_data("app.t_mtr_port", "where id=" . $get_identity . " ")->result();
            $row_port = 1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Invoice',
            'content'  => 'invoice/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add')),
            'service'  => $this->invoice->select_data("app.t_mtr_service", "where status=1 order by name asc")->result(),
            'port' => $port,
            'channel' => $this->invoice->get_channel(),
            'row_port' => $row_port,
            'destination' => $this->invoice->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result(),
            'transaction_type' => $this->invoice->select_data("app.t_mtr_transaction_type","where status not in (-5) order by id ASC")->result(),
            'status_type' => $this->invoice->select_data("app.t_mtr_status","where tbl_name = 't_trx_invoice' AND status<>'-5' order by id ASC")->result(),
            'team' => $this->invoice->select_data("core.t_mtr_team","where status = 1")->result(),
            'btn_excel' => checkBtnAccess($this->_module,'download_excel'),
        );

        $this->load->view('default', $data);
    }

    public function get_merchant()
    {
        $channel = $this->enc->decode($this->input->post('channel'));
        $data = array();
        if (strtolower($channel) == 'b2b') {
            $data[] = array("id" => "", "name" => "Pilih");
            $get_data = $this->invoice->get_merchant();
            foreach ($get_data as $k => $v) {
                $data[] = array(
                    'id' => $this->enc->encode($v->merchant_id),
                    'name' => strtoupper($v->merchant_name)
                );
            }
        }
        echo json_encode($data);
    }

    public function getOutletId()
    {
        $merchantId = $this->enc->decode($this->input->post('merchantId'));
        $get_data = $this->invoice->selectData("app.t_mtr_outlet_merchant", " where merchant_id='{$merchantId}' and status <> '-5' order by outlet_id asc")->result();

        $data[] = array("id" => "", "name" => "Pilih");
        foreach ($get_data as $k => $v) {
            $data[] = array(
                'id' => $this->enc->encode($v->merchant_id),
                'name' => strtoupper($v->merchant_name)
            );
        }

        echo json_encode($data);
    }    

    function get_dock()
    {
        $port = $this->enc->decode($this->input->post('port'));

        empty($port) ? $port_id = 'NULL' : $port_id = $port;
        $dock = $this->dock->select_data($this->_table, "where port_id=" . $port_id . " and status=1")->result();

        $data = array();
        foreach ($dock as $key => $value) {
            $value->id = $this->enc->encode($value->id);
            $data[] = $value;
        }

        echo json_encode($data);
    }

    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("dateFrom");
        $dateTo = $this->input->get("dateTo");

        $data = $this->invoice->download();

        $file_name = 'Invoice tanggal ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $header = array(
            'NO' => 'string',
            'TANGGAL INVOICE' => 'string',
            'NOMER INVOICE' => 'string',
            'NAMA CUSTOMER' => 'string',
            'NOMER TELPON' => 'string',
            'EMAIL' => 'string',
            'HARGA' => 'string',
            'SERVICE' => 'string',
            'KEBERANGKATAN' => 'string',
            'TUJUAN' => 'string',
            'CHANNEL' => 'string',
            'NAMA LOKET' => 'string',
            'MERCHANT' => 'string',
            'OUTLET ID' => 'string',
            'JENIS TRANSAKSI' => 'string',
            'STATUS' => 'string',
            'KODE DISKON' => 'string',
            'NAMA DISKON' => 'string',
            'DUE DATE' => 'string',
        );

        $no = 1;

        foreach ($data as $key => $value) {
            $rows[] = array(
                $no,
                $value->created_on,
                $value->trans_number,
                $value->customer_name,
                $value->phone_number,
                $value->email,
                $value->amount,
                $value->service_name,
                $value->port_origin,
                $value->port_destination,
                $value->channel,
                $value->terminal_name,
                $value->merchant_name,
                $value->outlet_id,
                $value->transaction_type_name,
                $value->status_invoice,
                $value->discount_code,
                $value->description,
                $value->due_date,
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
