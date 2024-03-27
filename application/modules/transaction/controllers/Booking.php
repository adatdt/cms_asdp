<?php
defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(0);

class Booking extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('m_booking', 'booking');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_booking';
        $this->_username = $this->session->userdata('username');
        
        $this->_module   = 'transaction/booking';
        // $this->_module   = 'module_testing/booking';

        $this->dbView=checkReplication();
        // $this->dbView = $this->load->database("dbView", TRUE);        
        $this->dbAction = $this->load->database("dbAction", TRUE);
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');

        $service =  $this->booking->select_data("app.t_mtr_service", "where status=1 order by name asc")->result() ;

        if ($this->input->is_ajax_request()) {
            $rows = $this->booking->dataList();

            // print_r($rows);exit;
            

            echo json_encode($rows);
            exit;
        }

        $get_identity = $this->booking->get_identity_app();
        // port berdasarkan user

        if ($get_identity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->booking->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id') . "")->result();
                $row_port = 1;
            } else {
                $port = $this->booking->select_data("app.t_mtr_port", "where status not in (-5) order by name asc")->result();
                $row_port = 0;
            }
        } else {
            $port = $this->booking->select_data("app.t_mtr_port", "where id=" . $get_identity . "")->result();
            $row_port = 1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Booking',
            'content'  => 'booking/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add')),
            'service'  =>  $service,
            'status'  => $this->booking->select_data("app.t_mtr_status", "where tbl_name='t_trx_booking' order by description asc")->result(),
            'port' => $port,
            'channel' => $this->booking->get_channel(),
            'keterangan'  => $this->booking->getUnderOverPaid(),
            'row_port' => $row_port,
            'destination' => $port = $this->booking->select_data("app.t_mtr_port", "where status not in (-5) order by name asc")->result(),
            'team' => $this->booking->select_data("core.t_mtr_team", "where status=1 order by team_name asc")->result(),
            'btn_excel' => checkBtnAccess($this->_module, 'download_excel'),
        );

        $this->load->view('default', $data);
    }

    public function get_merchant()
    {
        $channel = $this->enc->decode($this->input->post('channel'));
        $data = array();
        if (strtolower($channel) == 'b2b') {
            $data[] = array("id" => "", "name" => "Pilih");
            $get_data = $this->booking->get_merchant();
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
        $merchantId=$this->enc->decode($this->input->post("merchantId"));
        checkUrlAccess($this->_module, 'view');
        $rows = array();
        if ($this->input->is_ajax_request()) {
            $dataRows = $this->booking->select_data("app.t_mtr_outlet_merchant"," where merchant_id='{$merchantId}' and status <> '-5' order by outlet_id asc ")->result(); 
            foreach ($dataRows as $key => $value) {
                $rows[]=$value;
            }                       
        }
        echo json_encode($rows);
    }

    public function detail($booking_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'detail');

        $id = $this->enc->decode($booking_code);

        $get_service = $this->booking->select_data("app.t_trx_booking", " where booking_code='" . $id . "' ")->row();

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title']    = 'detail booking';
        $data['title'] = 'Detail Booking';
        $data['content']  = 'booking/detail';
        $data['id']       = $booking_code;
        $data['booking_code']       = $id;
        $data['port'] = $this->booking->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
        $data['booking'] = $this->booking->select_data($this->_table, "where booking_code ='" . $id . "' ")->row();

        // ambil data sesuai kebutuhan , penumpang atau kendaraan
        if ($get_service->service_id == 1) {
            // $data['passanger'] = $this->booking->listDetail("where a.booking_code='" . $id . "' and a.status !='-5' ")->result();
            // $data['header_data'] = $this->booking->listDetail("where a.booking_code='" . $id . "' ")->row();
            $data['passanger'] = $this->booking->listDetail("where a.booking_code='" . $id . "' and a.status !='-5' ");
            $data['header_data'] = $this->booking->listDetail("where a.booking_code='" . $id . "' ");

            $this->load->view($this->_module . '/detail_modal_passanger', $data);
        } else {
            // $data['vehicle'] = $this->booking->listVehicle("where a.booking_code='" . $id . "' ")->result();
            // $data['vehicle_passanger'] = $this->booking->listDetail("where a.booking_code='" . $id . "' and a.status !='-5' ")->result();
            // $header_data = $this->booking->listVehicle("where a.booking_code='" . $id . "' ")->row();
            $data['vehicle'] = $this->booking->listVehicle("where a.booking_code='" . $id . "' ");
            
            $data['vehicle_passanger'] = $this->booking->listDetail("where a.booking_code='" . $id . "' and a.status !='-5' ");
            $header_data = $this->booking->listVehicle("where a.booking_code='" . $id . "' ");
            $data['header_data'] = $header_data;
            $data['count_vehicle'] = count((array)$header_data);
            $this->load->view($this->_module . '/detail_modal_vehicle', $data);
        }
    }

    public function listDetail()
    {

        $booking_code = $this->enc->decode($this->input->post('id'));

        // $rows = $this->booking->listDetail("where a.booking_code='" . $booking_code . "' ")->result();
        $rows = $this->booking->listDetail("where a.booking_code='" . $booking_code . "' ");

        $r = array();
        foreach ($rows as $key => $value) {
            $value->fare = idr_currency($value->fare);
            $r[] = $value;
        }

        echo json_encode($r);
    }

    public function listDetail2()
    {

        $rows = $this->booking->listDetail2();
        echo json_encode($rows);
    }

    public function listVehicle()
    {

        $booking_code = $this->enc->decode($this->input->post('id'));

        // $rows = $this->booking->listVehicle("where a.booking_code='" . $booking_code . "' ")->result();
        $rows = $this->booking->listVehicle("where a.booking_code='" . $booking_code . "' ");
        echo json_encode($rows);
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

        $data = $this->booking->download();

        // print_r($data); exit;
        $file_name = 'Booking tanggal ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');


        $header = array(
            'NO' => 'string',
            'NAMA PENUMPANG BOOKING' => 'string',
            'SERVICE' => 'string',
            'NOMER INVOICE' => 'string',
            'KODE BOOKING' => 'string',
            'KEBERANGKATAN' => 'string',
            'TUJUAN' => 'string',
            'TANGGAL BERANGKAT' => 'string',
            'TANGGAL BOOKING' => 'string',
            'TOTAL PENUMPANG' => 'string',
            'HARGA' => 'string',
            'CHANNEL' => 'string',
            'EMAIL' => 'string',
            'NOMER TELPON' => 'string',
            'NO. PREPAID' => 'string',
            'TERMINAL CODE' => 'string',
            'NAMA LOKET' => 'string',
            'STATUS' => 'string',
            'KETERANGAN' => 'string',            
            'REF NO' => 'string',
            'NAMA MERCHANT'=>'string',
            'OUTLET ID'=>'string'
        );     
        
        // print_r($header); exit;

        $no = 1;
        foreach ($data as $key => $value) {
            
            $rows[] = array(
                $no,
                $value->customer_name,
                $value->service_name,
                $value->trans_number,
                $value->booking_code,
                $value->port_origin,
                $value->port_destination,
                $value->depart_date,
                $value->created_on,
                $value->total_passanger,
                $value->amount,
                $value->channel,
                $value->email,
                $value->phone_number,
                $value->card_no,
                $value->terminal_code,
                $value->terminal_name,
                $value->status,
                $value->keterangan,
                $value->ref_no,
                $value->merchant_name,
                $value->outlet_id,
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
}
