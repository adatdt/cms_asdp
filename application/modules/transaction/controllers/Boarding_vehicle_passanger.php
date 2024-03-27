<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Boarding_vehicle_passanger extends MY_Controller {
    public function __construct() {
        parent::__construct();

        logged_in();
        $this->load->model('boarding_vehicle_passanger_model', 'boarding');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_boarding';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/boarding_vehicle_passanger';

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView   = checkReplication();
        $this->dbAction = $this->load->database("dbAction", TRUE);
    }

    public function index() {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->boarding->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity = $this->boarding->get_identity_app();

        if ($get_identity == 0) {
            // ambil port berdasarkan user
            if (!empty($this->session->userdata('port_id'))) {
                $port     = $this->boarding->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id'))->result();
                $row_port = 1;
            } else {
                $port     = $this->boarding->select_data("app.t_mtr_port", "where status not in (-5) order by name asc")->result();
                $row_port = 0;
            }
        } else {
            $port     = $this->boarding->select_data("app.t_mtr_port", "where id=" . $get_identity)->result();
            $row_port = 1;
        }

        $getVehicleClass = $this->boarding->getMaster("t_mtr_vehicle_class ","id","name");  
        asort($getVehicleClass);
        $idVehicleClass = array_map(function($a){ return $this->enc->encode($a);},array_keys($getVehicleClass));
        $vehicleClass = array_combine($idVehicleClass,array_values($getVehicleClass));
        $dataVehicleClass[""]="Pilih";
        $dataVehicleClass += $vehicleClass;

        $data = [
            'home'      => 'Home',
            'url_home'  => site_url('home'),
            'title'     => 'Boarding Penumpang dalam Kendaraan',
            'content'   => 'boarding_vehicle_passanger/index',
            'btn_add'   => generate_button_new($this->_module, 'add', site_url($this->_module . '/add')),
            'port'      => $port,
            'vehicleClass'=>$dataVehicleClass,
            'row_port'  => $row_port,
            'gs'        => $this->check_gs() == 12 ? "false" : "true",
            'btn_excel' => checkBtnAccess($this->_module, 'download_excel'),
            'team'      => $this->boarding->select_data("core.t_mtr_team", "where status=1 order by team_name asc")->result(),
        ];

        $this->load->view('default', $data);
    }

    public function detail($boarding_code) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'detail');

        $id = $this->enc->decode($boarding_code);

        $where = " where b.booking_code='" . $id . "' and b.service_id=2 and a.status=1 ";
        $where .= " order by b.ticket_number asc ";
        $row_data = $this->boarding->listDetail($where)->result();

        $getPerangkat = array_unique(array_column($row_data,"terminal_code"));
        $dataPerangkat=[];
        if(!empty($getPerangkat))
        {
            $terminalString = array_map(function($a){ return "'".$a."'"; },$getPerangkat);
            $selectPerangkat = $this->boarding->select_data("app.t_mtr_device_terminal","where terminal_code in (".implode(",",$terminalString).")")->result();              
            $dataPerangkat = array_combine(array_column($selectPerangkat,"terminal_code"),array_column($selectPerangkat,"terminal_name"));            
        }                

        $dataPort = $this->boarding->getMaster("t_mtr_port","id","name");
        $dataService = $this->boarding->getMaster("t_mtr_service ","id","name");
        $dataDock = $this->boarding->getMaster("t_mtr_dock ","id","name");
        $dataShipClass = $this->boarding->getMaster("t_mtr_ship_class ","id","name");  
        $dataPassangerType = $this->boarding->getMaster("t_mtr_passanger_type ","id","name");     

        $rows = array();
        foreach ($row_data as $key => $value) {
            $value->ship_class_name = $dataShipClass[$value->ship_class];
            $value->port_name = $dataPort[$value->port_id];
            $value->dock_name = $dataDock[$value->dock_id];
            $value->service_name = $dataService[$value->service_id];
            $value->passanger_type_name = $dataPassangerType[$value->passanger_type_id];
            $value->boarding_device_terminal = empty($dataPerangkat[$value->terminal_code])?"":$dataPerangkat[$value->terminal_code];

            $rows[]=$value;
        }

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title']    = 'Detail Boarding Kendaraan';
        $data['content']  = 'boarding/detail';
        $data['id']       = $boarding_code;
        $data['gs']       = $this->check_gs() == 12 ? "gs" : "gt";
        $data['detail']   = (object)$rows;

        $this->load->view($this->_module . '/detail_modal', $data);
    }

    function get_dock() {
        $port = $this->enc->decode($this->input->post('port'));

        empty($port) ? $port_id = 'NULL' : $port_id = $port;
        $dock                   = $this->dock->select_data($this->_table, "where port_id=" . $port_id . " and status=1")->result();

        $data = [];
        foreach ($dock as $key => $value) {
            $value->id = $this->enc->encode($value->id);
            $data[]    = $value;
        }

        echo json_encode($data);
    }

    public function download_excel() {

        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("dateFrom");
        $dateTo   = $this->input->get("dateTo");

        $data = $this->boarding->download();

        $file_name = 'Boarding Penumpang dalam Kendaraan tanggal ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = ['height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom'];

        // ada permintaan awal live bahwa gs tidak bisa liat no tiket
        $rows = [];
        if ($this->check_gs() == 12) {
            $header = [
                "NO"=>"string",
                "TANGGAL BOARDING"=>"string",
                "KODE BOARDING"=>"string",
                "PELABUHAN"=>"string",
                "DERMAGA"=>"string",
                "KODE BOOKING"=>"string",
                // "NOMER TIKET"=>"string",
                "NAMA PENUMPANG"=>"string",
                "JENIS PENUMPANG"=>"string",
                "NOMER POLISI"=>"string",
                "GOLONGAN"=>"string",
                "NAMA KAPAL"=>"string",
                "PERANGKAT BOARDING"=>"string",
                "KETERANGAN"=>"string",
            ];

            $no = 1;

            foreach ($data as $key => $value) {
                $rows[] = [$no,
                $value->boarding_date,
                $value->boarding_code,
                $value->port_name,
                $value->dock_name,
                $value->booking_code,
                // $value->ticket_number,
                $value->name,
                $value->passanger_type_name,
                $value->id_number,
                $value->vehicle_class_name,
                $value->ship_name,
                $value->terminal_name,
                $value->manifest_data_from,
                ];
                $no++;
            }
        } else {
            $header = [
                'NO'                   => 'string',
                "TANGGAL BOARDING"=>"string",
                "KODE BOARDING"=>"string",
                "PELABUHAN"=>"string",
                "DERMAGA"=>"string",
                "KODE BOOKING"=>"string",
                "NOMER TIKET"=>"string",
                "NAMA PENUMPANG"=>"string",
                "JENIS PENUMPANG"=>"string",
                "NOMER POLISI"=>"string",
                "GOLONGAN"=>"string",
                "NAMA KAPAL"=>"string",
                "PERANGKAT BOARDING"=>"string",
                "KETERANGAN"=>"string",
            ];

            $no = 1;

            foreach ($data as $key => $value) {
                $rows[] = [$no,
                    $value->boarding_date,
                    $value->boarding_code,
                    $value->port_name,
                    $value->dock_name,
                    $value->booking_code,
                    $value->ticket_number,
                    $value->name,
                    $value->passanger_type_name,
                    $value->id_number,
                    $value->vehicle_class_name,
                    $value->ship_name,
                    $value->terminal_name,
                    $value->manifest_data_from,
                ];
                $no++;
            }
        }

        // print_r($rows); exit;
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

    function check_gs() {
        // checking apakan user gs ,
        $check_gs = $this->boarding->select_data("core.t_mtr_user", " where id=" . $this->session->userdata("id"))->row();

        return $check_gs->user_group_id;
    }
}
