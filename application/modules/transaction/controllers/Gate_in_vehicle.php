<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gate_in_vehicle extends MY_Controller {
    public function __construct() {
        parent::__construct();

        logged_in();
        $this->load->model('m_gate_in_vehicle', 'gate_in');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_gate_in_vehicle';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/gate_in_vehicle';

        $this->dbAction = $this->load->database("dbAction", TRUE);
        $this->dbView   = checkReplication();
        // $this->dbView = $this->load->database("dbView", TRUE);
    }

    public function index() {
        checkUrlAccess(uri_string(), 'view');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
        if ($this->input->is_ajax_request()) {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->gate_in->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity = $this->gate_in->get_identity_app();

        if ($get_identity == 0) {
            // ambil port berdasarkan user
            if (!empty($this->session->userdata('port_id'))) {
                $port     = $this->gate_in->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id'))->result();
                $row_port = 1;
            } else {
                $port     = $this->gate_in->select_data("app.t_mtr_port", "where status !='-5' order by name asc")->result();
                $row_port = 0;
            }
        } else {
            $port     = $this->gate_in->select_data("app.t_mtr_port", "where id=" . $get_identity)->result();
            $row_port = 1;
        }

        $data = [
            'home'      => 'Home',
            'url_home'  => site_url('home'),
            'title'     => 'Gate in Kendaraan',
            'content'   => 'gate_in_vehicle/index',
            'btn_add'   => generate_button_new($this->_module, 'add', site_url($this->_module . '/add')),
            'service'   => $this->gate_in->select_data("app.t_mtr_service", "where status=1 order by name asc")->result(),
            'port'      => $port,
            'row_port'  => $row_port,
            'gs'        => $this->check_gs() == 12 ? "false" : "true",
            'btn_excel' => checkBtnAccess($this->_module, 'download_excel'),
            'team'      => $this->gate_in->select_data("core.t_mtr_team", "where status=1 order by team_name asc")->result(),
        ];

        $this->load->view('default', $data);
    }

    public function detail($booking_code) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'detail');

        $id = $this->enc->decode($booking_code);

        // get total passanger
        $get_total = $this->gate_in->select_data("app.t_trx_booking", "where booking_code ='" . $id . "' ")->row();

        $data['home']            = 'Home';
        $data['url_home']        = site_url('home');
        $data['title']           = 'detail booking';
        $data['title']           = 'Detail Gate in ';
        $data['content']         = 'booking/detail';
        $data['id']              = $booking_code;
        $data['gs']              = $this->check_gs() == 12 ? "false" : "true";
        $data['total_passanger'] = $get_total->total_passanger;
        $data['port']            = $this->gate_in->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
        $data['booking']         = $this->gate_in->select_data("$this->_table", "where booking_code ='" . $id . "' ")->row();

        $this->load->view($this->_module . '/detail_modal', $data);

        // $this->load->view('default',$data);
    }

    public function listDetail() {

        $booking_code = $this->enc->decode($this->input->post('id'));

        $data = $this->gate_in->listDetail($booking_code)->result();

        $columnTerminalVehicle = array_column($data, "terminal_code");
        $columnTerminalPassanger = array_column($data, "terminal_code_passenger");
        $mergeTerminal = array_unique(array_merge($columnTerminalVehicle, $columnTerminalPassanger));

        $masterTerminal[""] = "";
        if(!empty($mergeTerminal ))
        {
            $getTerminalString = array_map(function($a){ return "'".$a."'"; }, $mergeTerminal );

            $dataTerminal = $this->gate_in->select_data_field("app.t_mtr_device_terminal"," terminal_code, terminal_name ", " where terminal_code in (".implode(",",$getTerminalString).") ")->result();
            $masterTerminal += array_combine(array_column($dataTerminal,"terminal_code"), array_column($dataTerminal,"terminal_name"));
        }

                
        $rows = [];
        foreach ($data as $key => $value) 
        {
            $value->no = "";
            $value->created_on = format_date($value->created_on )." ".format_time($value->created_on );
            if(!empty($value->gatein_date))
            {
                $value->created_on = format_date($value->gatein_date )." ".format_time($value->gatein_date);
            }

            $value->terminal_name = @$masterTerminal[$value->terminal_code];
            if(!empty($value->terminal_code_passenger))
            {
                $value->terminal_name = @$masterTerminal[$value->terminal_code_passenger];
            }
            $rows []= $value; 
        }

        echo json_encode(array("data"=>$rows,
                                            "csrfToken" => $this->security->get_csrf_hash(),
                                            "csrfName" => $this->security->get_csrf_token_name() ));
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

        $data = $this->gate_in->download();

        // print_r($data); exit;

        $file_name = 'Gate in kendaraan tanggal ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = ['height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom'];
        $rows = [];
        if ($this->check_gs() == 12) {
            $header = [
                'NO'                   => 'string',
                'TANGGAL GATEIN'       => 'string',
                'KODE BOOKING'         => 'string',
                'NAMA PENGEMUDI'       => 'string',
                'NOMER PLAT'           => 'string',
                'GOLONGAN'             => 'string',
                'SERVIS'               => 'string',
                'PELABUHAN'            => 'string',
                'BOARDING EXPIRED'     => 'string',
                'PERANGKAT GATEIN'     => 'string',
                'TOTAL PENUMPANG'      => 'string',
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

            foreach ($data as $key => $value) {
                $rows[] = [$no,
                    $value->created_on,
                    $value->booking_code,
                    $value->first_passanger,
                    $value->plate_number,
                    $value->vehicle_class_name,
                    $value->service_name,
                    $value->port_name,
                    $value->boarding_expired,
                    $value->terminal_name,
                    $value->total_passanger,
                    // $value->length,
                    // $value->height,
                    // $value->width,
                    // $value->weight,
                    $value->length_cam,
                    $value->height_cam,
                    $value->width_cam,
                    $value->weighbridge,
                ];
                $no++;
            }
        } else {
            $header = [
                'NO'                   => 'string',
                'TANGGAL GATEIN'       => 'string',
                'KODE BOOKING'         => 'string',
                'NOMER TIKET'         => 'string',
                'NAMA PENGEMUDI'       => 'string',
                'NOMER PLAT'           => 'string',
                'GOLONGAN'             => 'string',
                'SERVIS'               => 'string',
                'PELABUHAN'            => 'string',
                'BOARDING EXPIRED'     => 'string',
                'PERANGKAT GATEIN'     => 'string',
                'TOTAL PENUMPANG'      => 'string',
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

            foreach ($data as $key => $value) {
                $rows[] = [$no,
                    $value->created_on,
                    $value->booking_code,
                    $value->ticket_number,
                    $value->first_passanger,
                    $value->plate_number,
                    $value->vehicle_class_name,
                    $value->service_name,
                    $value->port_name,
                    $value->boarding_expired,
                    $value->terminal_name,
                    $value->total_passanger,
                    // $value->length,
                    // $value->height,
                    // $value->width,
                    // $value->weight,
                    $value->length_cam,
                    $value->height_cam,
                    $value->width_cam,
                    $value->weighbridge,
                ];
                $no++;
            }
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header, $styles1);

        if(!empty($rows))
        {
            foreach ($rows as $row) {
                $writer->writeSheetRow('Sheet1', $row);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }

    function check_gs() {
        // checking apakan user gs ,
        $check_gs = $this->gate_in->select_data("core.t_mtr_user", " where id=" . $this->session->userdata("id"))->row();

        return $check_gs->user_group_id;
    }
}
