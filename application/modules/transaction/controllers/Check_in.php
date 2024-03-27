<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/*
    enchance validation and add csrf token 11-08-2023 by adat adat.nutech@gmail.com
*/

class Check_in extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_check_in','check_in');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('dompdfadaptor');    

        $this->_table    = 'app.t_trx_check_in';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/check_in';

        $this->dbAction = $this->load->database("dbAction", TRUE);
        $this->dbView=checkReplication();
        // $this->dbView = $this->load->database("dbView", TRUE);
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->check_in->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->check_in->get_identity_app();

        if($get_identity==0)
        {
            // pengambilan id port yang ada di usernya
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->check_in->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id') )->result();
                $row_port=1;
            }
            else
            {
                $port=$this->check_in->select_data("app.t_mtr_port","where status!='-5' order by name asc")->result();
                $row_port=0;
            }


        }
        else
        {
            $port=$this->check_in->select_data("app.t_mtr_port","where id=".$get_identity )->result();
            $row_port=1;
        }


        // hardcord 12 user group gs 
        // kodisi false true dikirim ke columnDefs index, untuk hidding ticket
        $this->check_gs() ==12?$gs="false":$gs="true";

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Check In Penumpang',
            'content'  => 'check_in/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'service'  => $this->check_in->select_data("app.t_mtr_service","where status not in (-5) order by name asc")->result(),
            'port'=>$port,
            'row_port'=>$row_port,
            'gs'=>$gs,
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),
            'team'=>$this->check_in->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
	}


    public function detail($booking_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $id=$this->enc->decode($booking_code);

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title']    = 'detail booking';
        $data['title'] = 'Detail Booking';
        $data['content']  = 'booking/detail';
        $data['id']       = $booking_code;
        $data['booking']=$this->booking->select_data("$this->_table","where booking_code ='".$id."' ")->row();
        $data['port']=$this->booking->select_data("app.t_mtr_port","where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/detail_modal',$data); 

        // $this->load->view('default',$data);   
    }

    public function listDetail(){   

        $booking_code=$this->enc->decode($this->input->post('id'));

        $rows = $this->booking->listDetail("where a.booking_code='".$booking_code."' ")->result();
        echo json_encode($rows);

    }

    public function listVehicle(){   

        $booking_code=$this->enc->decode($this->input->post('id'));

        $rows = $this->booking->listVehicle("where a.booking_code='".$booking_code."' ")->result();
        echo json_encode($rows);

    }

    function get_dock()
    {
        $port=$this->enc->decode($this->input->post('port'));

        empty($port)?$port_id='NULL':$port_id=$port;
        $dock=$this->dock->select_data($this->_table,"where port_id=".$port_id." and status=1")->result();

        $data=array();
        foreach($dock as $key=>$value)
        {
            $value->id=$this->enc->encode($value->id);
            $data[]=$value;            
        }

         echo json_encode($data);
    }

    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");

        $data = $this->check_in->download();

        

        $file_name = 'Check in penumpang tanggal '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');


        // apakah dia user gs
        if($this->check_gs()==12)
        {
            $header = array(
                'NO' =>'string',
                'TANGGAL CHECKIN' =>'string',
                'KODE BOOKING' =>'string',
                'NOMER IDENTITAS' =>'string',
                'NAMA PENUMPANG' =>'string',
                'NOMER INVOICE' =>'string',
                'CHANNEL' =>'string',
                'SERVIS' =>'string',
                'PELABUHAN' =>'string',
                'PERANGKAT' =>'string',
            );

            $no=1;

            foreach ($data as $key => $value) {
                $rows[] = array($no,
                                $value->created_on,
                                $value->booking_code,
                                $value->id_number,
                                $value->passanger_name,
                                $value->trans_number,
                                $value->channel,
                                $value->service_name,
                                $value->port_origin,
                                $value->terminal_name,
                            );
                $no++;
            }
        }
        else
        {
            $header = array(
                'NO' =>'string',
                'TANGGAL CHECKIN' =>'string',
                'KODE BOOKING' =>'string',
                'NOMER TICKET' =>'string',
                'NOMER IDENTITAS' =>'string',
                'NAMA PENUMPANG' =>'string',
                'NOMER INVOICE' =>'string',
                'CHANNEL' =>'string',
                'SERVIS' =>'string',
                'PELABUHAN' =>'string',
                'PERANGKAT' =>'string',
            );

            $no=1;

            foreach ($data as $key => $value) {
                $rows[] = array($no,
                                $value->created_on,
                                $value->booking_code,
                                $value->ticket_number,
                                $value->id_number,
                                $value->passanger_name,
                                $value->trans_number,
                                $value->channel,
                                $value->service_name,
                                $value->port_origin,
                                $value->terminal_name,
                            );
                $no++;
            }

        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header,$styles1);

        foreach($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }


    function check_gs()
    {
        // checking apakan user gs ,
        $check_gs=$this->check_in->select_data("core.t_mtr_user", " where id=".$this->session->userdata("id"))->row();

        return $check_gs->user_group_id;
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
        $data['dataPassangerType'] = $this->check_in->select_data("app.t_mtr_passanger_type"," where status != -5 order by ordering asc ")->result(); 

        

        // $data['vehicle']=$this->check_in->getReciept("02EAHZHE")->result();
        $data['passanger']=$this->check_in->getReciept($booking_code);
        $this->load->view($this->_module.'/receipt_online_pdf',$data);	    

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

        $this->load->view('check_in/boarding_pdf',$data);        

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

        $dataPassanger = $this->check_in->getReciept($booking_code);

        $data['logo']= base64_encode($dataImage);

        $data["eticket_npwp"]= $dataInfo["eticket_npwp"];
        $data["eticket_address"]= $dataInfo["eticket_address"];
        $data["company_name"]= $dataInfo["company_name"];
        $data['mailFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/mail-boarding.png')));
        $data['waFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/wa-boarding.png')));
        $data['phoneFooter'] = base64_encode(file_get_contents(base_url('assets/img/img/phone-boarding.png')));
        $data['imgPaid'] = $imgPaid = base64_encode(file_get_contents("assets/img/img/paid.png"));
        $data['passanger']=$dataPassanger;
        $data['ppnReceipt']=$this->getPpnReceipt("ppn_e_receipt")->param_value;     
        $data['ppnText']=$this->getPpnReceipt("narasi_ppn_e_receipt")->param_value;   
        $data['namaPetugas'] = @$this->check_in->getNamaPetugas($dataPassanger[0]->created_by, $dataPassanger[0]->booking_channel );
        $data['dataPassangerType'] = $this->check_in->select_data("app.t_mtr_passanger_type"," where status != -5 order by ordering asc ")->result(); 

        // exit;
        $this->load->view('check_in/receipt_goshow_pdf',$data);        

    } 

    public function download_ticket_pdf($encodeBooking)
    
    {
        $this->global_model->checkAccessMenuAction($this->_module,'ticket_pdf');
        $bookingCode = $this->enc->decode($encodeBooking);
        if(empty($bookingCode))
        {
            redirect('error_401');
            exit;
        }
        
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
        $dataPassanger = $this->check_in->ticket_passanger($bookingCode);
        // $dataPassanger = $this->check_in->ticket_passanger("00QNR3TB");
        // $dataPassanger = $this->check_in->ticket_passanger("0032FY7G");

        // 0032FY7G

        $path = base_url('assets/img/img/ferizy-logo.png');
        $dataImage = file_get_contents($path);

        $config_param = get_config_param('reservation');
        // $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$dataPassanger[0]->ticket_number);
        $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$bookingCode);


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
            "time_checkin_end_information",
            "vaccine_covid_pl",
        );
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

        $data["dataVaccine"]= $this->check_in->get_vaccine_status_manifest(array_column($dataPassanger,"ticket_number"));   
        $data["dataTestStatus"]= $this->check_in->get_test_status(array_column($dataPassanger,"ticket_number"));            

        $data['passanger']=$dataPassanger;
        
        $this->load->view('check_in/tiket_new_pdf_pnp',$data);       
        
        // $html = $this->load->view('check_in/tiket_new_pdf_pnp',$data, true);        
        // $dompdf = new \Dompdf\Dompdf();
        // $dompdf->load_html($html);
        // $dompdf->render();
        // $output = $dompdf->output();
        // file_put_contents(__DIR__ .'/Brochure.pdf', $output);
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
    public function encriptAes($obCode)
    {
        // hard cord 
        $aesKey=$this->check_in->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_key_login') ")->row();
        $aesIv=$this->check_in->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_iv_login') ")->row();

        return PHP_AES_Cipher::encrypt2($aesKey->param_value,$aesIv->param_value,$obCode);

    }   
    function getPpnReceipt($param)
    {
        $getParameter=$this->check_in->select_data("app.t_mtr_custom_param"," where status=1 and param_name='".$param."' ")->row();

        return $getParameter;
    }           

}
