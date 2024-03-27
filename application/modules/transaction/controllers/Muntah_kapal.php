<?php

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Muntah_kapal extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('m_muntah_kapal','munpal');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/muntah_kapal';
    }

    public function index()
    {
        checkUrlAccess(uri_string(),'view');

        $button='<button class="btn btn-primary" type="submit" id="saveBtn" title="Simpan"><i class="fa fa-check"></i>Simpan</button>';

        $btnSave = generate_button($this->_module, 'add', $button);


        $data = array(
            'home'      => 'Home',
            'url_home'  => site_url('home'),
            'title'     => 'Muntah Kapal',
            'content'   => 'muntah_kapal/index',
            'btn_add'   => $btnSave,
        );
        $this->load->view('default', $data);
    }

    public function checkTicketNumberPassenger()
    {
        $rows = $this->munpal->dataTicketNumberPassenger();
        echo json_encode($rows);
    }

    public function checkTicketNumberVehicle()
    {
        $rows = $this->munpal->dataTicketNumberVehicle();
        echo json_encode($rows);
    }

    public function save_muntah_29012021()
    {
        $type = $this->input->post('type');
        $ticketNumber = array_map('strtoupper', $this->input->post('ticketNumber'));

        $timestamp_log = date('Y-m-d H:i:s');
        $passenger_boarding_ids = array();
        $passenger_booking_ids = array();
        $passenger_ticket_numbers = array();
        $data_switch_ship_header_passenger = array();
        $data_switch_ship_detail_passenger = array();
        $countVehicles = 0;
        $vehicle_boarding_ids = array();
        $vehicle_booking_ids = array();
        $vehicle_ticket_numbers = array();
        $data_switch_ship_header_vehicle = array();
        $data_switch_ship_detail_vehicle = array();
        $countPassengers = 0;


        $boarding_status = $this->munpal->cekBoardingStatus($type, $ticketNumber);
        if ($boarding_status === false)
        {
            $message = 'Tiket belum melakukan boarding.';
            echo json_encode(array('code' => 0, 'message'=>$message));
            exit();
        }

        // cek schedule code udah pernah pindah kapal atau belum
        $manifest_status = $this->munpal->cekManifestStatus($type, $ticketNumber);
        if ($manifest_status === false) 
        {
            $message = 'Manifest kapal belum di-approve.';
            echo json_encode(array('code' => 0, 'message'=>$message));
            exit();
        }        

        // ambil data passenger yang terkait dengan scheduleCode nya
        if ($type == 'kendaraan')
        {
            $passengers = $this->munpal->getBoardedPassengerByVehicleTicketNumber($ticketNumber);
        }
        else
        {
            $passengers = $this->munpal->getBoardedPassengerByTicketNumber($ticketNumber);
        }

        if ($passengers !== false)
        {
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

            // print_r($passenger_ticket_numbers); exit;
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

            $save_switch_ship1 = $this->munpal->setSwitchShipData($data_switch_ship_header_passenger, $data_switch_ship_detail_passenger, 'passenger');

            if ($save_switch_ship1 === true) 
            {
                $unboardUnbookPassenger = $this->munpal->unboardAndUnbook($passenger_ticket_numbers, 'penumpang');
                if ($unboardUnbookPassenger === false) {
                    $message = 'Gagal memindahkan penumpang.';
                    echo json_encode(array('code' => 0, 'message'=>$message));
                    exit();
                }
            }
            else
            {
                $message = 'Gagal memindahkan penumpang.';
                echo json_encode(array('code' => 0, 'message'=>$message));
                exit();
            }
        }

        if ($type == 'kendaraan') {
            // ambil data vehicle yang terkait dengan scheduleCode nya
            $vehicles = $this->munpal->getBoardedVehicleByTicketNumber($ticketNumber);
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
                    $shift_id = $row->shift_id;
                    $shift_date = $row->shift_date;
                    $switch_ship_code = $boarding_code.$port_code;
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
                $save_switch_ship2 = $this->munpal->setSwitchShipData($data_switch_ship_header_vehicle, $data_switch_ship_detail_vehicle, 'vehicle');
                if ($save_switch_ship2 === true) {
                    $unboardUnbookVehicle = $this->munpal->unboardAndUnbook($vehicle_ticket_numbers, 'kendaraan');
                    if ($unboardUnbookVehicle === false) {
                        $message = 'Gagal memindahkan kendaraan.';
                        echo json_encode(array('code' => 0, 'message'=>$message));
                        exit();
                    }
                } else {
                    $message = 'Gagal memindahkan kendaraan.';
                    echo json_encode(array('code' => 0, 'message'=>$message));
                    exit();
                }
            }
        }

        $message = 'Berhasil memindahkan '.$countPassengers.' penumpang & '.$countVehicles.' kendaraan.';
        echo json_encode(array('code' => 1, 'message'=>$message));
    }

    public function save_muntah()
    {
        $type = $this->input->post('type');
        $ticketNumber = array_map('strtoupper', $this->input->post('ticketNumber'));

        $timestamp_log = date('Y-m-d H:i:s');
        $passenger_boarding_ids = array();
        $passenger_booking_ids = array();
        $passenger_ticket_numbers = array();
        $data_switch_ship_header_passenger = array();
        $data_switch_ship_detail_passenger = array();
        $countVehicles = 0;
        $vehicle_boarding_ids = array();
        $vehicle_booking_ids = array();
        $vehicle_booking_code = array();
        $vehicle_ticket_numbers = array();
        $data_switch_ship_header_vehicle = array();
        $data_switch_ship_detail_vehicle = array();
        $countPassengers = 0;


        $boarding_status = $this->munpal->cekBoardingStatus($type, $ticketNumber);
        if ($boarding_status === false)
        {
            $message = 'Tiket belum melakukan boarding.';
            echo json_encode(array('code' => 0, 'message'=>$message));
            exit();
        }

        // cek schedule code udah pernah pindah kapal atau belum
        $manifest_status = $this->munpal->cekManifestStatus($type, $ticketNumber);
        if ($manifest_status === false) 
        {
            $message = 'Manifest kapal belum di-approve.';
            echo json_encode(array('code' => 0, 'message'=>$message));
            exit();
        }        

        // ambil data passenger yang terkait dengan scheduleCode nya
        if ($type == 'kendaraan')
        {
            $passengers = $this->munpal->getBoardedPassengerByVehicleTicketNumber($ticketNumber);
        }
        else
        {
            $passengers = $this->munpal->getBoardedPassengerByTicketNumber($ticketNumber);
        }

        if ($passengers !== false)
        {
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

            // print_r($passenger_ticket_numbers); exit;
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
            
            // $save_switch_ship1 = $this->munpal->setSwitchShipData($data_switch_ship_header_passenger, $data_switch_ship_detail_passenger, 'passenger');

            // if ($save_switch_ship1 === true) 
            // {
            //     $unboardUnbookPassenger = $this->munpal->unboardAndUnbook($passenger_ticket_numbers, 'penumpang');
            //     if ($unboardUnbookPassenger === false) {
            //         $message = 'Gagal memindahkan penumpang.';
            //         echo json_encode(array('code' => 0, 'message'=>$message));
            //         exit();
            //     }
            // }
            // else
            // {
            //     $message = 'Gagal memindahkan penumpang.';
            //     echo json_encode(array('code' => 0, 'message'=>$message));
            //     exit();
            // }

            $unboardUnbookPassenger = $this->munpal->unboardAndUnbook(
                                                                        $passenger_ticket_numbers,
                                                                        'penumpang',
                                                                        $data_switch_ship_header_passenger, 
                                                                        $data_switch_ship_detail_passenger, 
                                                                        'passenger');
            if ($unboardUnbookPassenger === false) {
                $message = 'Gagal memindahkan penumpang.';
                echo json_encode(array('code' => 0, 'message'=>$message));
                exit();
            }            
        }

        if ($type == 'kendaraan') {
            // ambil data vehicle yang terkait dengan scheduleCode nya
            $vehicles = $this->munpal->getBoardedVehicleByTicketNumber($ticketNumber);
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
                    $shift_id = $row->shift_id;
                    $shift_date = $row->shift_date;
                    $switch_ship_code = $boarding_code.$port_code;
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

                // $save_switch_ship2 = $this->munpal->setSwitchShipData($data_switch_ship_header_vehicle, $data_switch_ship_detail_vehicle, 'vehicle');
                // if ($save_switch_ship2 === true) {
                //     $unboardUnbookVehicle = $this->munpal->unboardAndUnbook($vehicle_ticket_numbers, 'kendaraan');
                //     if ($unboardUnbookVehicle === false) {
                //         $message = 'Gagal memindahkan kendaraan.';
                //         echo json_encode(array('code' => 0, 'message'=>$message));
                //         exit();
                //     }
                // } else {
                //     $message = 'Gagal memindahkan kendaraan.';
                //     echo json_encode(array('code' => 0, 'message'=>$message));
                //     exit();
                // }

                $unboardUnbookVehicle = $this->munpal->unboardAndUnbook($vehicle_ticket_numbers, 
                                                                        'kendaraan',
                                                                        $data_switch_ship_header_vehicle, 
                                                                        $data_switch_ship_detail_vehicle, 
                                                                        'vehicle',
                                                                        $vehicle_booking_code
                                                                    );
                if ($unboardUnbookVehicle === false) {
                    $message = 'Gagal memindahkan kendaraan.';
                    echo json_encode(array('code' => 0, 'message'=>$message));
                    exit();
                }                
            }
        }

        $message = 'Berhasil memindahkan '.$countPassengers.' penumpang & '.$countVehicles.' kendaraan.';
        echo json_encode(array('code' => 1, 'message'=>$message));
    }


    public function history()
    {
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->munpal->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'History Muntah Kapal',
            'content'  => 'transaction/muntah_kapal/history',
            'ship'  => $this->munpal->select_data("app.t_mtr_ship","where status=1 order by name asc")->result(),
            'port'=>$this->munpal->select_data("app.t_mtr_port","where status=1 order by name asc")->result()
        );

        $this->load->view('default', $data);
    }

    public function detail($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction('transaction/muntah_kapal/history','detail');
        $id=$this->enc->decode($id);


        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title']    = 'Detail Muntah Kapal';
        $data['content']  = 'muntah_kapal/detail';
        $data['id']       = $id;

        $this->load->view($this->_module.'/detail',$data);
    }

    public function listDetail()
    {
        $id=$this->input->post('id');
        if($this->input->is_ajax_request()){
            $rows = $this->munpal->getDetailSwitchShip($id);
            echo json_encode($rows);
            exit;
        }
    }

    public function checkTicketNumberBoardingStatus()
    {
        $type = $this->input->post('type');
        // $ticketNumber = $this->input->post('ticketNumber');
        // $ticketNumber = array_map('strtoupper', $this->input->post('ticketNumber'));
        $ticketNumber = $this->input->post('ticketNumber[]');
        $check_tickets = array();
        $boarded_tickets = array();
        $unboarded_tickets = array();



        if (!empty($ticketNumber)) 
        {
            foreach ($ticketNumber as $row1) {
                $check_tickets[] = strtoupper($row1);
            }
            $boarding_data = $this->munpal->getTicketNumberBoardingData($type, $check_tickets);
            foreach ($boarding_data as $row) {
                $boarded_tickets[] = $row->ticket_number;
            }
            $unboarded_tickets = array_diff($check_tickets, $boarded_tickets);
        } else {
            $message = 'Parameter pencarian kosong.';
            echo json_encode(array('code' => 0, 'message'=>$message));
            exit();
        }

        if (!empty($unboarded_tickets))
         {
            $imploded_tickets = implode(', ', $unboarded_tickets);
            $message = 'Tiket ['.$imploded_tickets.'] belum melakukan boarding.';
            echo json_encode(array('code' => 0, 'message'=>$message));
            exit();
        } 
        else 
        {

            $manifest_status = $this->munpal->cekManifestStatus($type, $ticketNumber);
            if ($manifest_status === false) {
                $message = 'Manifest kapal belum di-approve.';
                echo json_encode(array('code' => 0, 'message'=>$message));
                exit();
            }
            else
            {                
                echo json_encode(array('code' => 1, 'message'=>'success'));
                exit();
            }
        }
    }
}
