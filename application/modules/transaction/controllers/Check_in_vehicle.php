<?php
/*
    Document   : transaction
    Created on : 01 september, 2023
    Author     : dayung
    Description: Enhancement pasca angleb 2023
*/ 
defined('BASEPATH') or exit('No direct script access allowed');

class Check_in_vehicle extends MY_Controller {
    public function __construct() {
        parent::__construct();

        logged_in();
        $this->load->model('m_check_in_vehicle', 'check_in');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');        
        $this->load->library('dompdfadaptor');    

        $this->_table    = 'app.t_trx_check_in';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/check_in_vehicle';

        $this->dbAction = $this->load->database("dbAction", TRUE);
        $this->dbView   = checkReplication();
        // $this->dbView = $this->load->database("dbView", TRUE);
    }

    public function index() {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->check_in->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity = $this->check_in->get_identity_app();
        // ambil port berdasarkan user

        if ($get_identity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port     = $this->check_in->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id'))->result();
                $row_port = 1;
            } else {
                $port     = $this->check_in->select_data("app.t_mtr_port", "where status !='-5' order by name asc")->result();
                $row_port = 0;
            }
        } else {
            $port     = $this->check_in->select_data("app.t_mtr_port", "where id=" . $get_identity)->result();
            $row_port = 1;
        }

        // hardcord user group gs 12 jika ya maka diisikan false untuk dikirim di datatable
        $this->check_gs() == 12 ? $notGs = "false" : $notGs = "true";

        $data = [
            'home'      => 'Home',
            'url_home'  => site_url('home'),
            'title'     => 'Check In Kendaraan',
            'content'   => 'check_in_vehicle/index',
            'btn_add'   => generate_button_new($this->_module, 'add', site_url($this->_module . '/add')),
            'service'   => $this->check_in->select_data("app.t_mtr_service", "where status=1 order by name asc")->result(),
            'port'      => $port,
            'row_port'  => $row_port,
            'notGs'     => $notGs,
            'team'      => $this->check_in->select_data("core.t_mtr_team", "where status=1 order by team_name asc")->result(),
            'btn_excel' => checkBtnAccess($this->_module, 'download_excel'),
        ];

        $this->load->view('default', $data);
    }

    public function detail($booking_code) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'detail');

        $id = $this->enc->decode($booking_code);

        $get_total = $this->check_in->select_data("app.t_trx_booking", " where booking_code='{$id}' ")->row();

        $data['home']            = 'Home';
        $data['url_home']        = site_url('home');
        $data['title']           = 'Detail Check In Penumpang Kendaraan';
        $data['id']              = $booking_code;
        $data['gs']              = $this->check_gs() == 12 ? "false" : "true";
        $data['total_passanger'] = $get_total->total_passanger;
        $data['port']            = $this->check_in->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
        $data['booking']         = $this->check_in->select_data("$this->_table", "where booking_code ='" . $id . "' ")->row();

        $this->load->view($this->_module . '/detail_modal', $data);

        // $this->load->view('default',$data);
    }

    public function listDetail() {

        $booking_code = $this->enc->decode($this->input->post('id'));

        $data = $this->check_in->listDetail($booking_code)->result();
        $columnTerminalVehicle = array_column($data, "terminal_code");
        $columnTerminalPassanger = array_column($data, "terminal_code_passenger");
        $mergeTerminal = array_unique(array_merge($columnTerminalVehicle, $columnTerminalPassanger));
        $masterTerminal[""] = "";
        if(!empty($mergeTerminal ))
        {
            $getTerminalString = array_map(function($a){ return "'".$a."'"; }, $mergeTerminal );

            $dataTerminal = $this->check_in->select_data_field("app.t_mtr_device_terminal"," terminal_code, terminal_name ", " where terminal_code in (".implode(",",$getTerminalString).") ")->result();
            $masterTerminal += array_combine(array_column($dataTerminal,"terminal_code"), array_column($dataTerminal,"terminal_name"));
        }        

        $dataService = $this->check_in->getMaster("t_mtr_service ","id","name");

        $rows = [];
        foreach ($data as $key => $value) 
        {
            $value->no = "";
            $value->created_on = format_date($value->checkin_vehicle )." ".format_time($value->checkin_vehicle );
            if(!empty($value->checkin_passenger))
            {
                $value->created_on = format_date($value->checkin_passenger )." ".format_time($value->checkin_passenger );
            }


            $value->terminal_name = @$masterTerminal[$value->terminal_code];
            if(!empty($value->terminal_code_passenger))
            {
                $value->terminal_name = @$masterTerminal[$value->terminal_code_passenger];
            }

            $value->checkin_status = $value->checkin_pos;
            $value->service_name = $dataService[$value->service_id];
            $rows []= $value; 
        }

        echo json_encode(array("data" => $rows, 
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

        $dateFrom     = date("Y-m-d", strtotime(trim($this->input->post('dateFrom'))));
		$dateTo       = date("Y-m-d", strtotime(trim($this->input->post('dateTo'))));

        $data = $this->check_in->download();

        $file_name = 'check in kendaraan tanggal ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = ['height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom'];

        $rows = [];
        if ($this->check_gs() == 12) {
            $header = [
                'NO'                   => 'string',
                'TANGGAL CHECKIN'      => 'string',
                'KODE BOOKING'         => 'string',
                'NOMER PLAT'           => 'string',
                'GOLONGAN'             => 'string',
                'NOMER INVOICE'        => 'string',
                'CHANNEL'              => 'string',
                'SERVIS'              => 'string',
                'PELABUHAN'            => 'string',
                'PERANGKAT'            => 'string',
                'TOTAL PENUMPANG'      => 'string',
                // 'PANJANG'             => 'string',
                // 'TINGGI'              => 'string',
                // 'LEBAR'               => 'string',
                // 'BERAT'               => 'string',
                'PANJANG DARI SENSOR'  => 'string',
                'TINGGI DARI SENSOR'   => 'string',
                'LEBAR DARI SENSOR'    => 'string',
                'BERAT DARI TIMBANGAN' => 'string',
                'METODE CHECKIN' => 'string',
            ];

            $no = 1;

            foreach ($data as $key => $value) {
                $rows[] = [$no,
                    $value->created_on,
                    $value->booking_code,
                    $value->id_number,
                    $value->vehicle_class_name,
                    $value->trans_number,
                    $value->channel,
                    $value->service_name,
                    $value->port_origin,
                    $value->terminal_name,
                    $value->total_passanger,
                    $value->total_passanger,
                    // $value->length,
                    // $value->height,
                    // $value->width,
                    // $value->weight,
                    $value->length_cam,
                    $value->height_cam,
                    $value->width_cam,
                    $value->weighbridge,
                    $value->method_checkin,
                ];
                $no++;
            }
        } else {
            $header = [
                'NO'                   => 'string',
                'TANGGAL CHECKIN'      => 'string',
                'KODE BOOKING'         => 'string',
                'NOMER TICKET'         => 'string',
                'NOMER PLAT'           => 'string',
                'GOLONGAN'             => 'string',
                'NOMER INVOICE'        => 'string',
                'CHANNEL'              => 'string',
                'SERVIS'              => 'string',
                'PELABUHAN'            => 'string',
                'PERANGKAT'            => 'string',
                'TOTAL PENUMPANG'      => 'string',
                // 'PANJANG'             => 'string',
                // 'TINGGI'              => 'string',
                // 'LEBAR'               => 'string',
                // 'BERAT'               => 'string',
                'PANJANG DARI SENSOR'  => 'string',
                'TINGGI DARI SENSOR'   => 'string',
                'LEBAR DARI SENSOR'    => 'string',
                'BERAT DARI TIMBANGAN' => 'string',
                'METODE CHECKIN' => 'string',
            ];

            $no = 1;

            foreach ($data as $key => $value) {
                $rows[] = [$no,
                    $value->created_on,
                    $value->booking_code,
                    $value->ticket_number,
                    $value->id_number,
                    $value->vehicle_class_name,
                    $value->trans_number,
                    $value->channel,
                    $value->service_name,
                    $value->port_origin,
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
                    $value->method_checkin,
                ];
                $no++;
            }
        }

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
        $check_gs = $this->check_in->select_data("core.t_mtr_user", " where id=" . $this->session->userdata("id"))->row();

        return $check_gs->user_group_id;
    }
    public function download_boarding_pdf($ticketNumberCode)
    {
        $this->global_model->checkAccessMenuAction($this->_module,'boarding_pdf');
        $ticketNumber = $this->enc->decode($ticketNumberCode);
        if(empty($ticketNumber))
        {
            redirect('error_401');
            exit;
        }
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        

        $path = base_url('assets/img/img/ferizy-logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataImage = file_get_contents($path);

        $whereInfo = array(
            "eticket_npwp",
            "eticket_address",
            "company_name",
            "eticket_description",
        );

        $dataInfo = $this->getInfo($whereInfo);        

        $config_param = get_config_param('reservation');
        $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$ticketNumber);

        $data['booking_qr'] = $this->generateQr($contentQr);
        $data['logo']= base64_encode($dataImage);

        $data["eticket_npwp"]= $dataInfo["eticket_npwp"];
        $data["eticket_address"]= $dataInfo["eticket_address"];
        $data["company_name"]= $dataInfo["company_name"];
        $data["eticket_description"]= $dataInfo["eticket_description"];

        $data['mailFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/mail-boarding.png')));
        $data['waFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/wa-boarding.png')));
        $data['phoneFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/phone-boarding.png')));

        // define data
        $data['data'] = $this->check_in->getBoardingPass($ticketNumber);


        $this->load->view('check_in_vehicle/boarding_pdf',$data);        

    }    
    public function download_receipt_goshow_pdf($booking_code)
    {
        $this->global_model->checkAccessMenuAction($this->_module,'reciept_pdf');
        $booking_code=$this->enc->decode($booking_code);
        
        if(empty($booking_code))
        {
            redirect('error_401');
            exit;
        }

        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB


        $path = base_url('assets/img/img/ferizy-logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataImage = file_get_contents($path);


        $whereInfo = array(
            "eticket_npwp",
            "eticket_address",
            "company_name",
        );

        $dataInfo = $this->getInfo($whereInfo);        
        $dataVehicle = $this->check_in->getReciept($booking_code);


        $checkUnderpaid = $this->check_in->checkUnderpaid($booking_code, $dataVehicle );

        $namaPetugas = @$this->check_in->getNamaPetugas($dataVehicle[0]->created_by , $dataPassanger[0]->booking_channel);

        // echo $namaPetugas->username; exit;

        $data['logo']= base64_encode($dataImage);

        $data["eticket_npwp"]= $dataInfo["eticket_npwp"];
        $data["eticket_address"]= $dataInfo["eticket_address"];
        $data["company_name"]= $dataInfo["company_name"];
        $data['mailFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/mail-boarding.png')));
        $data['waFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/wa-boarding.png')));
        $data['phoneFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/phone-boarding.png')));
        $data['imgPaid'] = $imgPaid = base64_encode(file_get_contents("assets/img/img/paid.png"));
        $data['vehicle']=$dataVehicle;
        $data['ppnReceipt']=$this->getPpnReceipt("ppn_e_receipt")->param_value;     
        $data['ppnText']=$this->getPpnReceipt("narasi_ppn_e_receipt")->param_value;   
        $data['masterVehicle'] = $this->check_in->getMaster("t_mtr_vehicle_class","id","name"); 
        // print_r($checkUnderpaid[0]); exit;

        $detailRincianVehicle = $checkUnderpaid; // disini di set semua data  lama dan underpaid
        if(count($checkUnderpaid) > 1)
        {
            $namaPetugas = @$this->check_in->getNamaPetugas($checkUnderpaid[0]->created_by , $checkUnderpaid[0]->channel);                        
            $data['namaPetugas'] = $namaPetugas;        
            $data['detailRincianVehicle'] = $detailRincianVehicle;                        
            $this->load->view('check_in_vehicle/receipt_goshow_underpaid_pdf',$data);   
        }
        else
        {
            $data['namaPetugas'] = $namaPetugas;        
            $data['detailRincianVehicle'] = $detailRincianVehicle;                    
            $this->load->view('check_in_vehicle/receipt_goshow_pdf',$data);   
        }
             
    } 

    public function download_receipt_online_pdf($booking_code)
    {
        $this->global_model->checkAccessMenuAction($this->_module,'reciept_pdf');

        $booking_code=$this->enc->decode($booking_code);
        if(empty($booking_code))
        {
            redirect('error_401');
            exit;
        }

        $dataVehicle = $this->check_in->getReciept($booking_code);
        $getVehicleClass = $this->check_in->checkUnderpaid($booking_code, $dataVehicle);
        $dataVehicleClass = $this->check_in->getMaster("t_mtr_vehicle_class","id","name"); 
        $data["vehicleClassName"] = @$dataVehicleClass[$getVehicleClass[(count((array)$getVehicleClass) - 1 )]->vehicle_class];
        
        $this->load->library('pdfgenerator');
        $imgFeryzi = base64_encode(file_get_contents("assets/img/img/ferizy-logo.png"));
        $imgLogoPrimary = base64_encode(file_get_contents("assets/img/img/LOGO_ASDP_Primary.png"));
        $imgMail = base64_encode(file_get_contents("assets/img/img/mail.png"));
        $imgCallCenter = base64_encode(file_get_contents("assets/img/img/call-center.png"));
        $imgWhatsapp = base64_encode(file_get_contents("assets/img/img/whatsapp.png"));
        $imgGooglePlayIos = base64_encode(file_get_contents("assets/img/img/google-play-ios.png"));
        $imgFacebook = base64_encode(file_get_contents("assets/img/img/facebook.png"));
        $imgInstagram = base64_encode(file_get_contents("assets/img/img/instagram.png"));
        $imgTwitter = base64_encode(file_get_contents("assets/img/img/twitter.png"));
        $imgPaid = base64_encode(file_get_contents("assets/img/img/paid.png"));
        
        $whereInfo = array(
            "eticket_npwp",
            "eticket_address",
            "company_name",
        );
        $dataInfo = $this->getInfo($whereInfo);    

        $data["eticket_npwp"] = $dataInfo["eticket_npwp"];
        $data["eticket_address"] = $dataInfo["eticket_address"];
        $data["company_name"] = $dataInfo["company_name"];

        $data['title_pdf'] = 'E-Receipt';
        $data['booking_code'] = $booking_code;
        $data['imgFeryzi'] = $imgFeryzi;
        $data['imgLogoPrimary'] = $imgLogoPrimary;
        $data['imgMail'] = $imgMail;
        $data['imgCallCenter'] = $imgCallCenter;
        $data['imgWhatsapp'] = $imgWhatsapp;
        $data['imgGooglePlayIos'] = $imgGooglePlayIos;
        $data['imgFacebook'] = $imgFacebook;
        $data['imgInstagram'] = $imgInstagram;
        $data['imgTwitter'] = $imgTwitter;
        $data['imgPaid'] = $imgPaid;
        $data['ppnReceipt']=$this->getPpnReceipt("ppn_e_receipt")->param_value;
        $data['ppnText']=$this->getPpnReceipt("narasi_ppn_e_receipt")->param_value;   
        
        $data['vehicle']=$dataVehicle;
        $this->load->view($this->_module.'/receipt_online_pdf',$data);	    

    }

    public function download_ticket_pdf($encodeBooking)
    {
        
        $this->global_model->checkAccessMenuAction($this->_module,'ticket_pdf');
        $bookingCode = $this->enc->decode($encodeBooking);
        // echo $bookingCode; exit;
        if(empty($bookingCode))
        {
            redirect('error_401');
            exit;
        }
        
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
        $dataVehicle = $this->check_in->ticket_vehicle($bookingCode)->result();
        $getVehicleClassUnderpaid =  $this->check_in->getVehicleClassUnderpaid("'".$bookingCode."'");
        $dataVehicleClass = $this->check_in->getMaster("t_mtr_vehicle_class","id","name"); 

        // print_r($getVehicleClassUnderpaid); exit;

        $vehicleClassName = @$dataVehicleClass[$dataVehicle[0]->vehicle_class_id];
        if($getVehicleClassUnderpaid)
        {
            $vehicleClassName = @$dataVehicleClass[$getVehicleClassUnderpaid[0]->old_vehicle_class];
        }
        
        $data["vehicleClassName"] = $vehicleClassName;

        $path = base_url('assets/img/img/ferizy-logo.png');
        $dataImage = file_get_contents($path);

        $config_param = get_config_param('reservation');
        // $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$dataVehicle[0]->ticket_number);
        $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$bookingCode);
        // $contentQr = $bookingCode;



        $whereInfo = array(
            "eticket_alert_1",
            "eticket_alert_2",
            "eticket_alert_3",
            "eticket_npwp",
            "eticket_address",
            "company_name",
        );
        $dataInfo = $this->getInfo($whereInfo);

        $whereParameter = array(
            "time_checkin_start_information",
            "time_checkin_end_information");
            $dataParameterize = $this->getParameterize($whereParameter);
        
        // define image
        $data['imageIFCS'] = base64_encode(file_get_contents(base_url('assets/img/img/IFCS_Logo_PNG_primary.png')));
        $data['imageShip'] = base64_encode(file_get_contents(base_url('assets/img/img/cruise-with-arrow.png')));
        $data['imageInfo1'] = base64_encode(file_get_contents(base_url('assets/img/img/id_card.png')));
        $data['imageInfo2'] = base64_encode(file_get_contents(base_url('assets/img/img/printer2.png')));
        $data['imageInfo3'] = base64_encode(file_get_contents(base_url('assets/img/img/expired_new.png')));
        
        $data['imageFooter1'] = base64_encode(file_get_contents(base_url('assets/img/img/LOGO_ASDP_Primary.png')));
        $data['imageFooter2'] = base64_encode(file_get_contents(base_url('assets/img/img/mail.png')));
        $data['imageFooter3'] = base64_encode(file_get_contents(base_url('assets/img/img/call-center.png')));
        $data['imageFooter4'] = base64_encode(file_get_contents(base_url('assets/img/img/whatsapp.png')));
        $data['imageFooter5'] = base64_encode(file_get_contents(base_url('assets/img/img/google-play-ios.png')));
        $data['imageFooter6'] = base64_encode(file_get_contents(base_url('assets/img/img/facebook.png')));
        $data['imageFooter7'] = base64_encode(file_get_contents(base_url('assets/img/img/instagram.png')));
        $data['imageFooter8'] = base64_encode(file_get_contents(base_url('assets/img/img/twitter.png')));        
        $data['booking_qr'] = $this->generateQr($contentQr);
        $data['logo']= base64_encode($dataImage);

        // define dat
        $data['eticket_alert_1'] = $dataInfo['eticket_alert_1'];
        $data['eticket_alert_2'] = $dataInfo['eticket_alert_2'];
        $data['eticket_alert_3'] = $dataInfo['eticket_alert_3'];
        $data["eticket_npwp"]= $dataInfo["eticket_npwp"];
        $data["eticket_address"]= $dataInfo["eticket_address"];
        $data["company_name"]= $dataInfo["company_name"];

        $data['time_checkin_start_information'] = $dataParameterize['time_checkin_start_information']->value." ".$dataParameterize['time_checkin_start_information']->value_type;
        $data['time_checkin_end_information'] = $dataParameterize['time_checkin_end_information']->value." ".$dataParameterize['time_checkin_end_information']->value_type;   
        
        $data["dataVaccine"]= $this->check_in->get_vaccine_status_manifest(array_column($dataVehicle,"ticket_number_passanger"));   
        // print_r($data["dataVaccine"]); exit;
        // print_r($data["dataVaccine"]); exit;
        $data["dataTestStatus"]= $this->check_in->get_test_status(array_column($dataVehicle,"ticket_number_passanger"));            

        $data['vehicle']=$dataVehicle;

        $this->load->view('check_in_vehicle/tiket_new_pdf_knd',$data);        
    }
    function getInfo($where)
    {
        $whereIn = array_map(function($x){ return "'".$x."'"; },$where);
        $data = $this->check_in->select_data("app.t_mtr_info"," where name in (".implode(", ", $whereIn).")  ")->result();   

        $returnData = array();
        foreach ($data as $key => $value) {
            $returnData[$value->name]= (object)array("info" => $value->info); 
        }

        return $returnData;
    }
    function generateQr($content){

        $this->load->library('ciqrcode'); //pemanggilan library QR CODE

        $config['cacheable']    = true; //boolean, the default is true
        $config['cachedir']     = './assets/'; //string, the default is application/cache/
        $config['errorlog']     = './assets/'; //string, the default is application/logs/
        $config['imagedir']     = './assets/img/'; //direktori penyimpanan qr code
        $config['quality']      = true; //boolean, the default is true
        $config['size']         = '1024'; //interger, the default is 1024
        $config['black']        = array(224,255,255); // array, default is array(255,255,255)
        $config['white']        = array(70,130,180); // array, default is array(0,0,0)
        $this->ciqrcode->initialize($config);
        $rowData=array();

        $image_name="boarding_qr_".date("YmdHis").'.png'; //buat name dari qr code sesuai dengan nim
        //$params['data'] =$this->encriptAes($content); //data yang akan di jadikan QR CODE
        $params['data'] =$content; //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 10;
        $params['savename'] = FCPATH.$config['imagedir'].$image_name; ////simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE
        

        $qrCode=$image_name;
        $baseCode=base64_encode(file_get_contents($params['savename']));

        unlink($config['imagedir']."/".$image_name); // delete derectory
        return $baseCode;
                                
    }       
    function getPpnReceipt($param)
    {
        $getParameter=$this->check_in->select_data("app.t_mtr_custom_param"," where status=1 and param_name='".$param."' ")->row();

        return $getParameter;
    }             
    public function encriptAes($obCode)
    {
        // hard cord 
        $aesKey=$this->check_in->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_key_login') ")->row();
        $aesIv=$this->check_in->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_iv_login') ")->row();

        return PHP_AES_Cipher::encrypt2($aesKey->param_value,$aesIv->param_value,$obCode);

    }       
    function getParameterize($where)
    {
        $whereIn = array_map(function($x){ return "'".$x."'"; },$where);
        $data = $this->check_in->select_data("app.t_mtr_custom_param"," where param_name in (".implode(", ", $whereIn).")  ")->result();   

        $returnData = array();
        foreach ($data as $key => $value) {
            $returnData[$value->param_name]= (object)array("value" => $value->param_value, "value_type"=>$value->value_type); 
        }

        return $returnData;
    }             
}
