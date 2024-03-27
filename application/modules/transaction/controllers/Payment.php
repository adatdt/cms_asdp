<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('m_payment', 'payment');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('dompdfadaptor');    

        $this->_table    = 'app.t_trx_payment';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/payment';

        $this->dbAction = $this->load->database("dbAction", TRUE);
        $this->dbView=checkReplication();
        // $this->dbView = $this->load->database("dbView", TRUE);
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->payment->dataList();
            echo json_encode($rows);
            exit;
        }

        $identity_app = $this->payment->get_identity_app();

        if ($identity_app == 0) {
            // mengambil filter berdasarkan port user melalui user
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->payment->select_data("app.t_mtr_port", " where id=" . $this->session->userdata('port_id') . "  ")->result();
                $row_port = 1;
            } else {
                $port = $this->payment->select_data("app.t_mtr_port", " where status not in (-5) order by name asc ")->result();
                $row_port = 0;
            }
        } else {
            $port = $this->payment->select_data("app.t_mtr_port", " where id=" . $identity_app . "  ")->result();
            $row_port = 1;
        }


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Pembayaran',
            'content'  => 'payment/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add')),
            'service'  => $this->payment->select_data("app.t_mtr_service", "where status=1 order by name asc")->result(),
            // 'port'=>$this->payment->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
            'team' => $this->payment->select_data("core.t_mtr_team", "where status=1 order by team_name asc")->result(),
            'shift' => $this->payment->select_data("app.t_mtr_shift", "where status=1 order by shift_name asc")->result(),
            'channel' => $this->payment->get_channel(),
            'port' => $port,
            'sofId'=>$this->payment->getSofId(),
            'row_port' => $row_port,
            'btn_excel' => checkBtnAccess($this->_module, 'download_excel'),
            // 'service'=>$this->payment->select_data("app.t_mtr_service","where status=1 order by team_name asc")->result(),
            'btnDownload'  => createBtnDownloadByClass(uri_string())
        );


        $this->load->view('default', $data);
    }

    public function get_merchant()
    {
        $channel = $this->enc->decode($this->input->post('channel'));
        $data = array();
        if (strtolower($channel) == 'b2b') {
            $data[] = array("id" => "", "name" => "Pilih");
            $get_data = $this->payment->get_merchant();
            foreach ($get_data as $k => $v) {
                $data[] = array(
                    'id' => $this->enc->encode($v->merchant_id),
                    'name' => strtoupper($v->merchant_name)
                );
            }
        }
        // echo json_encode($data);
        echo json_encode(array(
            'data'      => $data,
            'csrfName'         =>$this->security->get_csrf_token_name(),
            'tokenHash'        =>$this->security->get_csrf_hash(),
        )
        );
    }

    public function detail($booking_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'detail');

        $id = $this->enc->decode($booking_code);

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title']    = 'detail booking';
        $data['title'] = 'Detail Booking';
        $data['content']  = 'booking/detail';
        $data['id']       = $booking_code;
        $data['port'] = $this->booking->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
        $data['booking'] = $this->booking->select_data("$this->_table", "where booking_code ='" . $id . "' ")->row();

        $this->load->view($this->_module . '/detail_modal', $data);

        // $this->load->view('default',$data);   
    }


    public function download_excel()
    {

        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("dateFrom");
        $dateTo = $this->input->get("dateTo");

        $data = $this->payment->download();
        // print_r($data); exit;

        $file_name = 'Pembayaran tanggal ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $header = array(
            "NO"=>"string",
            "TANGGAL PEMBAYARAN"=>"string",
            "TANGGAL TERBENTUK"=>"string",
            "KODE BOOKING"=>"string",
            "NOMER INVOICE"=>"string",
            "NAMA CUSTOMER"=>"string",
            "TANGGAL INVOICE"=>"string",
            "TIPE PEMBAYARAN"=>"string",
            "MERCHANT"=>"string",
            "TOTAL TARIF (Rp.)"=>"string",
            "TOTAL TARIF TANPA BIAYA ADMIN (Rp.)"=>"string",
            "BIAYA ADMIN (Rp.)"=>"string",
            "SERVIS"=>"string",
            "CHANNEL"=>"string",
            "OUTLET ID"=>"string",
            "SHIFT"=>"string",
            "ASAL"=>"string",
            "TUJUAN"=>"string",
            "TGL KEBERANGKATAN"=>"string",
            "JAM KEBERANGKATAN"=>"string",
            "JENIS TRANSAKSI"=>"string",
            "TRANS CODE"=>"string",
            "NOMER KARTU"=>"string",
            "SOF ID"=>"string",
            "KODE DISKON"=>"string",
            "NAMA DISKON"=>"string",
            "REF NO"=>"string",
        );

        $no = 1;
        $rows = array();
        foreach ($data as $key => $value) {
            $rows[] = array(
                $no,
                $value->payment_date,
                $value->created_on,
                $value->booking_code,
                $value->trans_number,
                $value->customer_name,
                $value->invoice_date,
                $value->payment_type,
                $value->merchant_name,
                $value->amount,
                $value->amount_invoice,
                $value->admin_fee,
                $value->service_name,
                $value->channel,
                $value->outlet_id,
                $value->shift_name,
                $value->origin,
                $value->destination,
                $value->depart_date,
                $value->depart_time,
                $value->tipe_transaksi,
                $value->trans_code,
                $value->card_no,
                $value->sof_id,
                $value->discount_code,
                $value->description,
                $value->ref_no,
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
    public function getOutletId()
    {
        $merchantId = $this->enc->decode($this->input->post('merchantId'));
        $data = array();

        $get_data = $this->payment->select_data("app.t_mtr_outlet_merchant", " where merchant_id='{$merchantId}' and status=1 order by outlet_id asc  ")->result();
        $data[] = array("id" => "", "name" => "Pilih");
        foreach ($get_data as $k => $v) {
            $data[] = array(
                'id' => $this->enc->encode($v->outlet_id),
                'name' => strtoupper($v->outlet_id)
            );
        }
        // echo json_encode($data);
        echo json_encode(array(
            'data'             => $data,
            'csrfName'         =>$this->security->get_csrf_token_name(),
            'tokenHash'        =>$this->security->get_csrf_hash(),
        )
        );
    }

    public function download_tiket_receipt($booking_code, $service_id)
    {
        $this->global_model->checkAccessMenuAction($this->_module,'reciept_pdf');
        $this->load->library('pdfgenerator');

        //define image
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

        //parameter info
        $eticketNpwp = $this->payment->select_data("app.t_mtr_info", " where name='eticket_npwp' ")->row();
        $companyName = $this->payment->select_data("app.t_mtr_info", " where name='company_name' ")->row();
        $eticketAddress = $this->payment->select_data("app.t_mtr_info", " where name='eticket_address' ")->row();

        //parameter PPN
        $ppnReceipt = $this->payment->select_data("app.t_mtr_custom_param", " where status=1 and param_name='ppn_e_receipt' ")->row();

        $ppnReceipt = $this->payment->select_data("app.t_mtr_custom_param", " where status=1 and param_name='ppn_e_receipt' ")->row();

        $booking_code=$this->enc->decode($booking_code);
        $service_id=$this->enc->decode($service_id);

        $data['title_pdf'] = 'Tiket Receipt Online '.$booking_code;
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
        $data['eticketNpwp'] = $eticketNpwp->info;
        $data['companyName'] =  $companyName->info;
        $data['eticketAddress'] =  $eticketAddress->info;
        $data['ppnReceipt']= $ppnReceipt->param_value;
        $data['ppnText']=$this->getPpnReceipt("narasi_ppn_e_receipt")->param_value;   
      
        if($service_id == 1){ //pejalan kaki

            $passenger=$this->payment->ticket_passanger("where B.booking_code='".$booking_code."' and BP.status not in ('-5','-6')")->result();
            $count_payment=$this->payment->count_payment("where booking_code='".$booking_code."' and status not in ('-5','-6')")->result();

            $passengerType=array();
            $fare=array();
            foreach ($passenger as $k => $v) {

                $passengerType[] = strtoupper($v->passanger_type);
                $fare[]          = strtoupper($v->fare);
                
            }
          
            $type_count= array_count_values($passengerType);
            $fare_count= array_count_values($fare);

            $data['passengerFare']= $fare_count;
            $data['passengerType']= $type_count;
            $data['countPayment']= $count_payment;
            $data['passenger']= $passenger;

            $html = $this->load->view($this->_module.'/ticket_receipt_passanger',$data, true);	    

        }else if ($service_id == 2) { //kendaraan

            // $data['vehicle']=$this->payment->ticket_vehicle($booking_code)->result();
            $data['vehicle']=$this->payment->ticket_vehicle($booking_code);

		    $html = $this->load->view($this->_module.'/ticket_receipt_vehicle',$data, true);	    

        }else{
            $this->load->view('error_404');
        }

         // setting paper
        $file_pdf = 'Tiket_Receipt_Online_'.$booking_code;
        $paper = 'A4';
        $orientation = "portrait";

        // run dompdf
        $this->pdfgenerator->generate($html, $file_pdf,$paper,$orientation);
    }

    function getPpnReceipt($param)
    {
        $getParameter=$this->payment->select_data("app.t_mtr_custom_param"," where status=1 and param_name='".$param."' ")->row();

        return $getParameter;
    }             

    public function download_ticket_pdf($booking_code, $service_id)
    {
        $this->global_model->checkAccessMenuAction($this->_module,'ticket_pdf');
        $bookingCode = $this->enc->decode($booking_code);
        $serviceId = $this->enc->decode($service_id);
      
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
        $whereInfo = array(
            "eticket_alert_1",
            "eticket_alert_2",
            "eticket_alert_3",
            "eticket_npwp",
            "eticket_address",
            "company_name",
        );
        $dataInfo = $this->getInfo($whereInfo);
        
        //parameter (Check In Start)
        $checkinStart = $this->payment->select_data("app.t_mtr_custom_param", " where status=1 and param_name='time_checkin_start_information' ")->row();
        $checkinEnd = $this->payment->select_data("app.t_mtr_custom_param", " where status=1 and param_name='time_checkin_end_information' ")->row();
        
        // define image
        $data['imageIFCS'] = base64_encode(file_get_contents(base_url('assets/img/img/IFCS_Logo_PNG_primary.png')));
        $data['imageShip'] = base64_encode(file_get_contents(base_url('assets/img/img/cruise-with-arrow.png')));
        $data['imageInfo1'] = base64_encode(file_get_contents(base_url('assets/img/img/id_card.png')));
        $data['imageInfo2'] = base64_encode(file_get_contents(base_url('assets/img/img/printer2.png')));
        $data['imageInfo3'] = base64_encode(file_get_contents(base_url('assets/img/img/expired_new.png')));
        
        $data['imgLogoPrimary'] = base64_encode(file_get_contents(base_url('assets/img/img/LOGO_ASDP_Primary.png')));
        $data['imgMail'] = base64_encode(file_get_contents(base_url('assets/img/img/mail.png')));
        $data['imgCallCenter'] = base64_encode(file_get_contents(base_url('assets/img/img/call-center.png')));
        $data['imgWhatsapp'] = base64_encode(file_get_contents(base_url('assets/img/img/whatsapp.png')));
        $data['imgGooglePlayIos'] = base64_encode(file_get_contents(base_url('assets/img/img/google-play-ios.png')));
        $data['imgFacebook'] = base64_encode(file_get_contents(base_url('assets/img/img/facebook.png')));
        $data['imgInstagram'] = base64_encode(file_get_contents(base_url('assets/img/img/instagram.png')));
        $data['imgTwitter'] = base64_encode(file_get_contents(base_url('assets/img/img/twitter.png')));        
        $data['logo']= base64_encode(file_get_contents(base_url('assets/img/img/ferizy-logo.png')));

        // define dat
        $data['eticket_alert_1'] = $dataInfo['eticket_alert_1'];
        $data['eticket_alert_2'] = $dataInfo['eticket_alert_2'];
        $data['eticket_alert_3'] = $dataInfo['eticket_alert_3'];
        $data['eticketNpwp']    = $dataInfo["eticket_npwp"];
        $data['eticketAddress'] = $dataInfo["eticket_address"];
        $data['companyName']    = $dataInfo["company_name"];

        //define param
        $data['checkinStart']    = $checkinStart->param_value;
        $data['checkinEnd']    = $checkinEnd->param_value;

        // $serviceId =1;
        if($serviceId == 1){
            $dataPassenger = $this->payment->ticket_passanger("where B.booking_code='".$bookingCode."' and BP.status not in ('-5','-6')")->result();
            // $dataPassenger = $this->payment->ticket_passanger("where B.booking_code='006W23XK' and BP.status not in ('-5','-6')")->result();
            $config_param = get_config_param('reservation');
          
            $contentQr="";
            foreach ($dataPassenger as $key => $value) {
                // $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
                $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$bookingCode);

            }
            $data["dataVaccine"]= $this->payment->get_vaccine_status_manifest(array_column($dataPassenger,"ticket_number"));   
            $data["dataTestStatus"]= $this->payment->get_test_status(array_column($dataPassenger,"ticket_number"));    

            $data['booking_code'] = $bookingCode;
            $data['booking_qr'] = $this->generateQr($contentQr);
            $data['passenger']=$dataPassenger;   
            
		    $this->load->view($this->_module.'/eticket_passenger',$data);	  
        
        }elseif($serviceId == 2){
            // $dataVehicle = $this->payment->ticket_vehicle($bookingCode)->result();
            $dataVehicle = $this->payment->ticket_vehicle($bookingCode);
            $config_param = get_config_param('reservation');
          
            $contentQr="";
            foreach ($dataVehicle as $key => $value) {
                // $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
                $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$bookingCode);

            }

            $data["dataVaccine"]= $this->payment->get_vaccine_status_manifest(array_column($dataVehicle,"ticket_number_passanger"));   
            $data["dataTestStatus"]= $this->payment->get_test_status(array_column($dataVehicle,"ticket_number_passanger"));        

            $data['booking_qr'] = $this->generateQr($contentQr);
            $data['vehicle']=$dataVehicle;      
		    $this->load->view($this->_module.'/eticket_vehicle',$data);	    
        
        }else{
            $this->load->view('error_404');
        }
    
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
        // $params['data'] =$this->encriptAes($content); //data yang akan di jadikan QR CODE
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

    public function encriptAes($obCode)
    {
        // hard cord 
        $aesKey=$this->payment->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_key_login') ")->row();
        $aesIv=$this->payment->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_iv_login') ")->row();

        return PHP_AES_Cipher::encrypt2($aesKey->param_value,$aesIv->param_value,$obCode);

    }  

    function getInfo($where)
    {
        $whereIn = array_map(function($x){ return "'".$x."'"; },$where);
        $data = $this->payment->select_data("app.t_mtr_info"," where name in (".implode(", ", $whereIn).")  ")->result();   

        $returnData = array();
        foreach ($data as $key => $value) {
            $returnData[$value->name]= (object)array("info" => $value->info); 
        }

        return $returnData;
    }

    public function download_receipt_goshow_pdf($booking_code, $service_id)
    {
        $this->global_model->checkAccessMenuAction($this->_module,'reciept_pdf');
        $booking_code=$this->enc->decode($booking_code);
        $serviceId = $this->enc->decode($service_id);
        // print_r($booking_code);exit;

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

        $data['logo']= base64_encode($dataImage);

        $data["eticket_npwp"]= $dataInfo["eticket_npwp"];
        $data["eticket_address"]= $dataInfo["eticket_address"];
        $data["company_name"]= $dataInfo["company_name"];
        $data['mailFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/mail-boarding.png')));
        $data['waFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/wa-boarding.png')));
        $data['phoneFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/phone-boarding.png')));
        $data['imgPaid'] = $imgPaid = base64_encode(file_get_contents("assets/img/img/paid.png"));
        $data['ppnReceipt']=$this->getPpnReceipt("ppn_e_receipt")->param_value;     
        $data['ppnText']=$this->getPpnReceipt("narasi_ppn_e_receipt")->param_value;   
        $data['dataPassangerType'] = $this->payment->select_data("app.t_mtr_passanger_type"," where status != -5 order by ordering asc ")->result(); 

        if($serviceId == 1){
            $dataPassanger = $this->payment->getRecieptPassenger($booking_code);
            $data['passanger']=$dataPassanger;
            $data['namaPetugas'] = @$this->payment->getNamaPetugas($dataPassanger[0]->created_by, $dataPassanger[0]->booking_channel );

            
            $this->load->view('payment/receipt_goshow_passenger',$data);
        }
        else
        {
            $dataVehicle = $this->payment->getRecieptVehicle($booking_code);
      
            $namaPetugas = @$this->payment->getNamaPetugas($dataVehicle[0]->created_by , $dataPassanger[0]->booking_channel);

            $data['vehicle']=$dataVehicle;
            $data['masterVehicle'] = $this->payment->getMaster("t_mtr_vehicle_class","id","name"); 

            $checkUnderpaid = $this->payment->checkUnderpaidTicket($booking_code, $dataVehicle );
         
            $detailRincianVehicle = $checkUnderpaid; // disini di set semua data  lama dan underpaid
            if(count($checkUnderpaid) > 1)
            {
                $namaPetugas = @$this->payment->getNamaPetugas($checkUnderpaid[0]->created_by , $checkUnderpaid[0]->channel);                        
                $data['namaPetugas'] = $namaPetugas;        
                $data['detailRincianVehicle'] = $detailRincianVehicle;                        
                $this->load->view('payment/receipt_goshow_underpaid',$data);   
            }
            else
            {
                $data['namaPetugas'] = $namaPetugas;        
                $data['detailRincianVehicle'] = $detailRincianVehicle;                    
                $this->load->view('payment/receipt_goshow_vehicle',$data);   
            }
        }        

    } 

}
