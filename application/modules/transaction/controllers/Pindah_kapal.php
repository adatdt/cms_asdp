<?php

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Pindah_kapal extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('m_pindah_kapal','pinpal');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/pindah_kapal';
    }

    public function index()
    {
        checkUrlAccess(uri_string(),'view');

        $button='<button class="btn btn-primary" type="submit" id="saveBtn" title="Simpan"><i class="fa fa-check"></i>Simpan</button>';

        $btnSave = generate_button($this->_module, 'add', $button);
                
        $data = array(
            'home'      => 'Home',
            'url_home'  => site_url('home'),
            'title'     => 'Pindah Kapal',
            'content'   => 'pindah_kapal/index',
            'btn_add'   => $btnSave,
        );
        $this->load->view('default', $data);
    }

    public function checkScheduleCodePassenger()
    {
        $rows = $this->pinpal->dataScheduleCodePassenger();
        echo json_encode($rows);
    }

    public function checkScheduleCodeVehicle()
    {
        $rows = $this->pinpal->dataScheduleCodeVehicle();
        echo json_encode($rows);
    }

    public function save_pindah()
    {
        $ticketNumber   = trim(strtoupper($this->input->post('ticketNumber')));

        $scheduleCode = $this->pinpal->getScheduleCodeByTicketNumber($ticketNumber);


        $timestamp_log = date('Y-m-d H:i:s');
        $passenger_boarding_ids = array();
        $passenger_booking_ids = array();
        $passenger_ticket_numbers = array();
        $data_switch_ship_header_passenger = array();
        $data_switch_ship_detail_passenger = array();
        $countPassengers = 0;
        $vehicle_boarding_ids = array();
        $vehicle_booking_ids = array();
        $vehicle_booking_code = array();
        $vehicle_ticket_numbers = array();
        $data_switch_ship_header_vehicle = array();
        $data_switch_ship_detail_vehicle = array();
        $countVehicles = 0;

        // cek schedule code udah pernah pindah kapal atau belum        
        if (empty($ticketNumber)) {
            $message = 'Nomer Tiket Harus diisi';
            echo json_encode(array('code' => 0, 'message'=>$message));
            exit();
        }


        // cek schedule code udah pernah pindah kapal atau belum
        $switch_ship_availability = $this->pinpal->cekSwitchShipAllAvailability($scheduleCode);
        if ($switch_ship_availability === false) {
            $message = 'Kode jadwal sudah pernah pindah kapal.';
            echo json_encode(array('code' => 0, 'message'=>$message));
            exit();
        }

        // ambil data passenger yang terkait dengan scheduleCode nya
        $passengers = $this->pinpal->getBoardedPassengerByScheduleCode($scheduleCode);
        if ($passengers !== false) {
            foreach ($passengers as $row) {
                $passenger_boarding_ids[] = $row->boarding_id;
                $passenger_booking_ids[] = $row->booking_id;
                $passenger_ticket_numbers[] = $row->ticket_number;
                $ship_id = $row->ship_id;
                $port_code = $row->port_code;
                $boarding_code = $row->boarding_code;
                $boarding_date = $row->boarding_date;
                $schedule_code = $row->schedule_code;
                $switch_ship_code = $boarding_code.$port_code;
                $shift_id = $row->shift_id;
                $shift_date = $row->shift_date;
                $data_switch_ship_detail_passenger[] = array(
                    'switch_ship_code' => $switch_ship_code,
                    'ticket_number' => $row->ticket_number,
                    'status'        => 1,
                    'created_on'    => $timestamp_log,
                    'created_by'    => $this->_username
                );
                $countPassengers++;
            }
            // save unboarded passengers data to switch table
            $data_switch_ship_header_passenger = array(
                'switch_ship_code' => $switch_ship_code,
                'ship_id'       => $ship_id,
                'date'          => date('Y-m-d', strtotime($boarding_date)),
                'time'          => date('H:i:s', strtotime($boarding_date)),
                'port_code'     => $port_code,
                'branch_code'   => '',
                'boarding_code' => $boarding_code,
                'schedule_code' => $schedule_code,
                'status'        => 1,
                'created_on'    => $timestamp_log,
                'created_by'    => $this->_username,
                'shift_id'      => $shift_id,
                'shift_date'    => $shift_date
            );
            $save_switch_ship_passenger = $this->pinpal->setSwitchShipData($data_switch_ship_header_passenger, $data_switch_ship_detail_passenger, 'passenger');
            if ($save_switch_ship_passenger === true) {
                $unboardUnbookPassenger = $this->pinpal->unboardAndUnbook($passenger_ticket_numbers, 'penumpang');
                if ($unboardUnbookPassenger === false) {
                    $message = 'Gagal memindahkan penumpang.';
                    echo json_encode(array('code' => 0, 'message'=>$message));
                    exit();
                }
            } else {
                $message = 'Gagal memindahkan penumpang.';
                echo json_encode(array('code' => 0, 'message'=>$message));
                exit();
            }
        }

        // ambil data vehicle yang terkait dengan scheduleCode nya
        $vehicles = $this->pinpal->getBoardedVehicleByScheduleCode($scheduleCode);
        if ($vehicles !== false) {
            foreach ($vehicles as $row) {
                $vehicle_boarding_ids[] = $row->boarding_id;
                $vehicle_booking_ids[] = $row->booking_id;
                $vehicle_booking_code[] = $row->booking_code;
                $vehicle_ticket_numbers[] = $row->ticket_number;
                $ship_id = $row->ship_id;
                $port_code = $row->port_code;
                $boarding_code = $row->boarding_code;
                $boarding_date = $row->boarding_date;
                $schedule_code = $row->schedule_code;
                $switch_ship_code = $boarding_code.$port_code;
                $shift_id = $row->shift_id;
                $shift_date = $row->shift_date;
                $data_switch_ship_detail_vehicle[] = array(
                    'switch_ship_code' => $switch_ship_code,
                    'ticket_number' => $row->ticket_number,
                    'status'        => 1,
                    'created_on'    => $timestamp_log,
                    'created_by'    => $this->_username
                );
                $countVehicles++;
            }
            // save unboarded passengers data to switch table
            $data_switch_ship_header_vehicle = array(
                'switch_ship_code' => $switch_ship_code,
                'ship_id'       => $ship_id,
                'date'          => date('Y-m-d', strtotime($boarding_date)),
                'time'          => date('H:i:s', strtotime($boarding_date)),
                'port_code'     => $port_code,
                'branch_code'   => '',
                'boarding_code' => $boarding_code,
                'schedule_code' => $schedule_code,
                'status'        => 1,
                'created_on'    => $timestamp_log,
                'created_by'    => $this->_username,
                'shift_id'      => $shift_id,
                'shift_date'    => $shift_date
            );
            $save_switch_ship_vehicle = $this->pinpal->setSwitchShipData($data_switch_ship_header_vehicle, $data_switch_ship_detail_vehicle, 'vehicle');
            if ($save_switch_ship_vehicle === true) {
                $unboardUnbookVehicle = $this->pinpal->unboardAndUnbook($vehicle_ticket_numbers, 'kendaraan',$boarding_code);
            } else {
                $message = 'Gagal memindahkan kendaraan.';
                echo json_encode(array('code' => 0, 'message'=>$message));
                exit();
            }
        }

        $message = 'Berhasil memindahkan '.$countPassengers.' penumpang & '.$countVehicles.' kendaraan.';
        echo json_encode(array('code' => 1, 'message'=>$message));
    }
}
