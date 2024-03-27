<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Sab extends MY_Controller{
  public function __construct(){
    parent::__construct();

    logged_in();
    $this->load->model('sab_model');
    $this->load->model('sab_validasi_model', 'validasi');
    $this->load->model('quota_model', 'quota');
    $this->load->model('global_model');
    $this->load->library('nu_pdf');
    $this->load->library('dompdfadaptor');
    $this->load->library('new_pdf');    


    $this->_table    = 'app.t_trx_booking';
    $this->_username = $this->session->userdata('username');
    $this->_module   = 'sab';
  }

  public function index(){
    checkUrlAccess(uri_string(),'view');
    if($this->input->is_ajax_request()){
      $this->validate_param_datatable($_POST,$this->_module);
      $rows = $this->sab_model->dataList();
      echo json_encode($rows);
      exit;
    }

    $data = array(
      'home'     => 'Home',
      'url_home' => site_url('home'),
      'title'    => 'Surat Keterangan Penyeberangan Terbatas',
      // 'title'    => 'Surat Angkutan Bebas',
      'content'  => 'sab/index',
      // 'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
      'url'      => site_url($this->_module.'/add'),
      'service'  => $this->sab_model->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
      'port'     => $this->sab_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
    );

    $this->load->view('default', $data);
  }

  public function add()
  {
    validate_ajax();
    $this->global_model->checkAccessMenuAction($this->_module,'add');

    $getCity= file_get_contents(base_url("assets/jsonFile/city.json"));

    $dataJson = json_decode($getCity,true);
    // print_r($dataJson); exit;

    $dataCity[""]="Pilih";
    foreach ($dataJson['results'] as $key => $value) {
      $typeCity = str_replace("Kabupaten","Kab.",$value['type']);
      $city = $typeCity." ".$value['city_name'];
      $dataCity[$city]= $city;
    }


    $data['port']=$this->sab_model->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();
    $data['service']=$this->sab_model->select_data("app.t_mtr_service"," where status=1 order by name asc ")->result();
    $data['ship_class']=$this->sab_model->select_data("app.t_mtr_ship_class"," where status=1 order by name asc ")->result();
    $data['id_type']=$this->sab_model->select_data("app.t_mtr_passanger_type_id"," where status=1 order by id asc ")->result();
    $data['vehicle_class']=$this->sab_model->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc ")->result();
    $data['param_reservation'] = get_config_param('reservation');
    $data['passanger_type'] = $this->sab_model->get_passanger_type();
    $data['min_booking'] = date('Y-m-d', strtotime(date('Y-m-d')."-".$data['param_reservation']['min_booking']." day"));
    $data['title'] = 'Tambah SKPT';
    // $data['title'] = 'Tambah SAB';
    $data['city'] = $dataCity;
    $this->load->view($this->_module.'/add',$data);
  }

  public function validasi_jadwal(){
      validate_ajax();
      $this->global_model->checkAccessMenuAction($this->_module,'add');

      $post = $this->input->post();

      $param_reservation = get_config_param('reservation');

      $this->validasi->validate_booking_date($post, 'sab', $param_reservation);

      $this->validasi->validate_schedule($post, 'sab', $param_reservation);

      // sementara di comment karena tabele activeted dan inactiveted untuk field web_admin belum ada
      // $this->validasi->validate_pembatasan_golongan($post, 'sab', $param_reservation);

      $response = array(
        "code" => 1,
        "message" => 'Success Validate',
        "data" => null,
			  "csrfName"        =>$this->security->get_csrf_token_name(),
        "tokenHash"        =>$this->security->get_csrf_hash(),
        
    );
    echo json_encode($response);
  }

  public function action_add(){
    validate_ajax();
    $this->global_model->checkAccessMenuAction($this->_module,'add');

    $post = $this->input->post();
    // print_r($post);exit;
    /* validation */
    $this->form_validation->set_rules('name', 'Nama', 'trim|required|max_length[150]|callback_special_char', array('special_char' => 'Nama Mengandung invalid karakter'));
    $this->form_validation->set_rules('phone', 'Phone', 'trim|min_length[10]|max_length[14]|numeric');
    $this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[100]|valid_email');
    $this->form_validation->set_rules('origin', 'Origin', 'trim|required|max_length[2]|numeric');
    $this->form_validation->set_rules('destination', 'Destination', 'trim|required|max_length[2]|numeric');
    $this->form_validation->set_rules('depart_date', 'Departure Date', 'trim|required');
    $this->form_validation->set_rules('depart_time_start', 'Depart Time start', 'trim|required|callback_special_char', array('special_char' => 'Depart time start Mengandung invalid karakter'));
    $this->form_validation->set_rules('depart_time_end', 'Depart Time end', 'trim|required|callback_special_char', array('special_char' => 'Depart time end Mengandung invalid karakter'));
    $this->form_validation->set_rules('service', 'service ', 'trim|required|max_length[2]');
    $this->form_validation->set_rules('ship_class', 'ship class', 'trim|required|numeric');
   
    $this->form_validation->set_message('required','%s harus diisi!');
    /* data post */

    $data_booking = null;
    $data_invoice = null;
    $data_booking_passanger = null;
    $data_payment = null;
    $data_booking_vehicle = null;

    //cek service
    $service = $post['service'];

    if($this->form_validation->run() == FALSE){
        echo $response = json_api(0,validation_errors());
    }
    else 
    {
      // print_r($post); exit;
      $post['total_booking_quota'] = $post['dewasa'] + $post['anak'] + $post['bayi'] + $post['lansia'];

      //manifest
      $customer_name = $post['name'];
      $phone_number = $post['phone'];
      $email = $post['email'];
      $manifests = $post['manifest'];

      //jadwal
      $origin = $post['origin'];
      $destination = $post['destination'];
      $depart_date = $post['depart_date'];
      $ship_class = $post['ship_class'];
      $total_passanger = $post['dewasa'] + $post['anak'] + $post['bayi'] + $post['lansia'];
      $time = explode("-", $post['depart_time']);
      $depart_time_start = $time[0];
      $depart_time_end = $time[1];

      $param_reservation = get_config_param('reservation');

      $valid = $this->validasi->validate_booking_date($post, 'sab', $param_reservation);

      $this->validasi->validate_schedule($post, 'sab', $param_reservation);
      
      // take out ini
      // $this->validasi->validate_pembatasan_golongan($post, 'sab', $param_reservation);

      /*
      if($ship_class == 1)
      {
        $gatein_expired    = strtotime(date('Y-m-d H:i:s', strtotime($depart_date." ".$depart_time_end."+".$param_reservation['gatein_expired_web']." hours")));
        $checkin_expired   = strtotime(date('Y-m-d H:i:s', strtotime($depart_date." ".$depart_time_end."+".$param_reservation['checkin_expired_web']." hours")));
      }
      else if()
      {
        $gatein_expired    = strtotime(date('Y-m-d H:i:s', strtotime($depart_date." ".$depart_time_end."+".$param_reservation['gatein_expired_web_eksekutif']." hours")));
        $checkin_expired   = strtotime(date('Y-m-d H:i:s', strtotime($depart_date." ".$depart_time_end."+".$param_reservation['checkin_expired_web_eksekutif']." hours")));
      }
      */

      
      if($ship_class == 2)
      {
        $gatein_expired    = strtotime(date('Y-m-d H:i:s', strtotime($depart_date." ".$depart_time_end."+".$param_reservation['gatein_expired_web_eksekutif']." hours")));
        $checkin_expired   = strtotime(date('Y-m-d H:i:s', strtotime($depart_date." ".$depart_time_end."+".$param_reservation['checkin_expired_web_eksekutif']." hours")));
      }
      else
      {
        $gatein_expired    = strtotime(date('Y-m-d H:i:s', strtotime($depart_date." ".$depart_time_end."+".$param_reservation['gatein_expired_web']." hours")));
        $checkin_expired   = strtotime(date('Y-m-d H:i:s', strtotime($depart_date." ".$depart_time_end."+".$param_reservation['checkin_expired_web']." hours")));
      }    

      $gatein_expired     = date('Y-m-d H:i:s',$gatein_expired);
      $checkin_expired    = date('Y-m-d H:i:s',$checkin_expired);

      $param_gatein      = get_config_param('gatein');
      $boarding_expired  = strtotime(date('Y-m-d H:i:s', strtotime($gatein_expired."+".$param_gatein['boarding_expired']." hours")));
      $boarding_expired  = date('Y-m-d H:i:s',$boarding_expired);

      $due_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')."+3 hours"));

      $this->db->trans_begin();

      $quota = $this->validasi->validate_booking_quota_pcm($post, 'sab');


      if($quota == null){
        $response = array(
          "code"    => 134,
          "message" => 'KUOTA TIKET SUDAH HABIS',
          "data"    => null,
          "csrfName"        =>$this->security->get_csrf_token_name(),
          "tokenHash"        =>$this->security->get_csrf_hash(),

        );
        echo json_encode($response);
        exit();
      }

      $quota_type = $this->validasi->booking_quota_pcm($post,$quota);

      if ($quota_type) {
          //trx_invoice
          $data_invoice=array(
            'amount'=>0,
            'total_amount'=>0,
            'customer_name'=>$customer_name,
            'phone_number'=>$phone_number,
            'email'=>$email,
            'booking_channel'=>'web_admin',
            'channel'=>'web_admin',
            'service_id'=>$service,
            'ticket_type'=>2,
            'status'=>2,
            'created_on'=>date("Y-m-d H:i:s"),
            'created_by'=>$this->session->userdata('username'),
            'due_date'=> $due_date,
            'invoice_date'=>date("Y-m-d H:i:s"),
            'payment_type'=>'SKPT',
            // 'payment_type'=>'SAB',
            'port_id'=> $origin
            );


          $identity_app=$this->sab_model->select_data("app.t_mtr_identity_app","")->row();
          $insertTransaction = $this->insertTransaction($data_invoice, 'S'.$identity_app->port_id, $origin);

          $booking_code = $this->generateRandomString($identity_app->port_id);
          $check = $this->global_model->checkData('app.t_trx_booking',array('booking_code' => $booking_code));
          while($check){
            $booking_code = $this->generateRandomString($identity_app->port_id);
            $check = $this->global_model->checkData('app.t_trx_booking',array('booking_code' => $booking_code));
          }        

          //trx_booking
          $data_booking=array(
            'service_id'=>$service,
            'booking_code'=>$booking_code,
            'trans_number'=> $insertTransaction->trans_number,
            'total_passanger'=>$total_passanger,
            'origin'=>$origin,
            'destination'=>$destination,
            'ship_class'=>$ship_class,
            'amount'=>0,
            'depart_date'=>$depart_date,
            'depart_time_start'=>$depart_time_start,
            'depart_time_end'=>$depart_time_end,
            'channel'=>'web_admin',
            'ticket_type'=>2,
            'checkin_expired'=>$checkin_expired,
            'gatein_expired'=>$gatein_expired,
            'status'=>2,
            'quota_type' => $quota_type,
            'created_on'=>date("Y-m-d H:i:s"),
            'created_by'=>$this->session->userdata('username'),
            );

          //trx_payment
          $data_payment=array(
            'payment_type'=>'SKPT',
            // 'payment_type'=>'SAB',
            'amount'=>0,
            'trans_number'=> $insertTransaction->trans_number,
            'channel'=>'web_admin',
            'status'=>1,
            'payment_date'=>date("Y-m-d H:i:s"),
            'created_on'=>date("Y-m-d H:i:s"),
            'created_by'=>$this->session->userdata('username'),
          );

          //trx_booking_passanger
          if($service == 2)
          {
            $i = 2;
          }
          else
          {
            $i = 1;
          }

          $tempTIcketPass=array();
          // print_r($manifests);exit;
          foreach ($manifests as $id => $manifest) {
            $id_type=$manifest['id_type'];
            $city=$manifest['city'];
            // 'name'=>$manifest['name'];
            // 'age'=>$manifest['age'];
            // 'birth_date'=>$manifest['birthdate'];
            // 'gender'=>$manifest['gender'];
            // 'city'=>$manifest['city'];
            // 'id_type'=>$manifest['id_type'];
            // 'passanger_type_id'=>$manifest['passenger_type'];          
            $this->form_validation->set_rules(" $id_type", 'id type', 'required|max_length[2]|numeric');
            // $this->form_validation->set_rules("$manifest['name']", 'name', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'Name  contains invalid characters'));
            // $this->form_validation->set_rules("$manifest['id_number']", 'id number', 'trim|required|max_length[16]|callback_special_char', array('special_char' => 'Id number  contains invalid characters'));
            // $this->form_validation->set_rules("$manifest['age']", 'age', 'required|max_length[3]|numeric');
            // $this->form_validation->set_rules("$manifest['gender']", 'gender', 'required|max_length[1]|callback_special_char', array('special_char' => 'Gender  contains invalid characters'));
            $this->form_validation->set_rules("$city", 'city', 'required|max_length[100]|callback_special_char_city', array('special_char_city' => 'City  contains invalid characters'));
            // $this->form_validation->set_rules("$manifest['passenger_type']", 'title', 'max_length[12]|callback_special_char', array('special_char' => 'title  contains invalid characters'));
            
            $ticket_number   = 'SA'.$origin.$destination.$booking_code . sprintf('%02d', $i++);
            $tempTIcketPass[]=$ticket_number  ; 
            $data_booking_passanger[] = array(
              'id_number'=>$manifest['id_number'],
              'name'=>$manifest['name'],
              'age'=>$manifest['age'],
              'birth_date'=>$manifest['birthdate'],
              'gender'=>$manifest['gender'],
              'city'=> $city,
              'id_type'=> $id_type,
              'passanger_type_id'=>$manifest['passenger_type'],
              'fare'=>0,
              'booking_code'=>$booking_code,
              'ticket_number'=>$ticket_number,
              'origin'=>$origin,
              'destination'=>$destination,
              'ship_class'=>$ship_class,
              'depart_date'=>$depart_date,
              'depart_time_start'=>$depart_time_start,
              'depart_time_end'=>$depart_time_end,
              'service_id'=>$service,
              'channel'=>'web_admin',
              'ticket_type'=>2,
              'checkin_expired'=>$checkin_expired,
              'gatein_expired'=>$gatein_expired,
              'boarding_expired'=>$boarding_expired,
              // 'status'=>4, // status ticket siap boarding
              'status'=>2, // status ticket siap check in
              'created_on'=>date("Y-m-d H:i:s"),
              'created_by'=>$this->session->userdata('username'),
              'entry_fee'=>0,
              'dock_fee'=>0,
              'trip_fee'=>0,
              'responsibility_fee'=>0,
              'insurance_fee'=>0,
              'ifpro_fee'=>0,
              'adm_fee'=>0,
            );

            // print_r($data_booking_passanger);exit;
            //trx_check_in
            $data_checkin_passanger[]=array(
              'booking_code'=>$booking_code,
              'ticket_number'=>$ticket_number,
              'channel'=>'web_admin',
              'checkin_web'=>1,
              'status'=>1,
              'created_on'=>date("Y-m-d H:i:s"),
              'created_by'=>$this->session->userdata('username'),
            );

            //trx_gate_in
            $data_gatein_passanger[]=array(
              'booking_code'=>$booking_code,
              'ticket_number'=>$ticket_number,
              'boarding_expired'=>$boarding_expired,
              'status'=>1,
              'created_on'=>date("Y-m-d H:i:s"),
              'created_by'=>$this->session->userdata('username'),
            );
          }

          //trx_booking_vehicle
          $query4 = $query7 = $query8 = true;
          $tempTIcketVehicle="";
          if($service == 2){
            $no_police = $post['no_police'];
            $vehicle = $post['vehicle_type'];
            $ticket_number = 'SA'.$origin.$destination.$booking_code.'01';
            $tempTIcketVehicle= $ticket_number;
            $data_booking_vehicle=array(
              'id_number'=>$no_police,
              'vehicle_class_id'=>$vehicle,
              'fare'=>0,
              'booking_code'=>$booking_code,
              'ticket_number'=>$ticket_number,
              'origin'=>$origin,
              'destination'=>$destination,
              'ship_class'=>$ship_class,
              'depart_date'=>$depart_date,
              'depart_time_start'=>$depart_time_start,
              'depart_time_end'=>$depart_time_end,
              'service_id'=>$service,
              'channel'=>'web_admin',
              'ticket_type'=>2,
              'checkin_expired'=>$checkin_expired,
              'gatein_expired'=>$gatein_expired,
              'boarding_expired'=>$boarding_expired,
              // 'status'=>4, // status ticket siap boarding
              'status'=>2, // status ticket siap check in
              'created_on'=>date("Y-m-d H:i:s"),
              'created_by'=>$this->session->userdata('username'),
              'length'=>0,
              'height'=>0,
              'weight'=>0,
              'entry_fee'=>0,
              'dock_fee'=>0,
              'trip_fee'=>0,
              'responsibility_fee'=>0,
              'insurance_fee'=>0,
              'ifpro_fee'=>0,
              'adm_fee'=>0,
            );

            //trx_check_in
            $data_checkin_vehicle=array(
              'booking_code'=>$booking_code,
              'ticket_number'=>$ticket_number,
              'channel'=>'web_admin',
              'checkin_web'=>1,
              'vehicle_class_booking'=>$vehicle,
              'vehicle_class_checkin'=>$vehicle,
              'id_number_booking'=>$no_police,
              'id_number_checkin'=>$no_police,
              'status'=>1,
              'created_on'=>date("Y-m-d H:i:s"),
              'created_by'=>$this->session->userdata('username'),
            );

            //trx_gate_in
            $data_gatein_vehicle=array(
              'booking_code'=>$booking_code,
              'ticket_number'=>$ticket_number,
              'boarding_expired'=>$boarding_expired,
              'status'=>1,
              'created_on'=>date("Y-m-d H:i:s"),
              'created_by'=>$this->session->userdata('username'),
            );
          }

          // print_r($data_booking);exit;
            $query1 = $this->sab_model->saveData('app.t_trx_booking', $data_booking);
            $query2 = $this->sab_model->saveData('app.t_trx_payment', $data_payment);
            $query3 = $this->global_model->insertBatch('app.t_trx_booking_passanger', $data_booking_passanger);
            
            // ini nanti di take out nantinya yang hanya ke insert yang servicenya penumpang pejalan kaki saja
            // $query5 = $this->global_model->insertBatch('app.t_trx_check_in', $data_checkin_passanger);
            // $query6 = $this->global_model->insertBatch('app.t_trx_gate_in', $data_gatein_passanger);
            if($service == 2)
            {

              $query4 = $this->sab_model->saveData('app.t_trx_booking_vehicle', $data_booking_vehicle);
              // $query7 = $this->sab_model->saveData('app.t_trx_check_in_vehicle', $data_checkin_vehicle);
              // $query8 = $this->sab_model->saveData('app.t_trx_gate_in_vehicle', $data_gatein_vehicle);

            }
      }

        if($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback() ;
            $response = json_encode($this->db->error());          
        }
        else
        {
            $this->db->trans_commit();

            $response = json_api(1,'Simpan Data Berhasil');

            $config_param = get_config_param('reservation');

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
            $checkinStart = $this->sab_model->select_data("app.t_mtr_custom_param", " where status=1 and param_name='time_checkin_start_information' ")->row();
            $checkinEnd = $this->sab_model->select_data("app.t_mtr_custom_param", " where status=1 and param_name='time_checkin_end_information' ")->row();
            
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

            $data['booking_code'] = $booking_code;

            if($service == 1)
            {
              $dataPassenger=$this->sab_model->ticket("where B.booking_code='".$booking_code."' and BP.status not in ('-5','-6')")->result();
 
              $contentQr="";
              foreach ($dataPassenger as $key => $value) {
                  // $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
                  $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$booking_code);

              }
              
              $data['booking_qr'] = $this->generateQr($contentQr);
              $data['passenger']=$dataPassenger;  
            }
            else
            {
              $dataVehicle = $this->sab_model->ticket_vehicle($booking_code)->result();
              $config_param = get_config_param('reservation');
            
              $contentQr="";
              foreach ($dataVehicle as $key => $value) {
                  // $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
                  $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$booking_code);

              }
              $data['booking_qr'] = $this->generateQr($contentQr);
              $data['vehicle']=$dataVehicle;    

            }
            // print_r($data);exit;
            $ticket_data = $this->ticket($data, $service, 1);
// print_r($ticket_data);exit;
            $ticket_data = str_replace('/', "-", $ticket_data);
            $this->send_email($booking_code,$ticket_data);          
        }
    }


    $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data_booking), $response);
    $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data_invoice), $response);
    $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data_booking_passanger), $response);
    $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data_payment), $response);
    // $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data_checkin_passanger), $response);
    // $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data_gatein_passanger), $response);
    if($service == 2){
      $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data_booking_vehicle), $response);
      // $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data_checkin_vehicle), $response);
      // $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data_gatein_vehicle), $response);
    }
    echo $response;
  }

  public function detail($booking_code)
  {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $id=$this->enc->decode($booking_code);

        $data['title'] = 'Detail Booking';
        $data['id']       = $booking_code;
        $data['port']=$this->sab_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['booking']=$this->sab_model->select_data('app.t_trx_booking',"where booking_code ='".$id."' ")->row();

        $this->load->view($this->_module.'/detail',$data);
  }

  public function download_pdf__before($booking_code, $service_id){
        $this->global_model->checkAccessMenuAction($this->_module,'download_pdf');
        $this->load->library('ciqrcode');
        $this->load->library('Html2pdf');

        $config_param = get_config_param('reservation');

        $id=$this->enc->decode($booking_code);
        $service_id=$this->enc->decode($service_id);

        $date = date('ymdhis');
        $path = generate_ymd_dir('./assets/img/qrcode');
        $save_path = "{$path}/";

        $config['cacheable']    = true;
        $config['cachedir']     = './assets/';
        $config['errorlog']     = './assets/';
        $config['imagedir']     = $save_path;
        $config['quality']      = true;
        $config['size']         = '1024';
        $config['black']        = array(224,255,255);
        $config['white']        = array(70,130,180);
        $this->ciqrcode->initialize($config);

        $data['title'] = 'Print';
        $data['id']       = $id;
        $data['path']       = $save_path;
        if($service_id == 1){
          $data['passanger']=$this->sab_model->ticket("where B.booking_code='".$id."' ")->result();

          foreach ($data['passanger'] as $key => $value) {
            $image_name = 'QR_'.$value->ticket_number.'.png';

            $params['data'] = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
            $params['level'] = 'H';
            $params['size'] = 10;
            $params['savename'] = FCPATH.$config['imagedir'].$image_name;
            $value->qrcode = $this->ciqrcode->generate($params);
          }
          ob_start();
          $this->load->view($this->_module.'/print',$data);
        }else{
          $data['vehicle']=$this->sab_model->ticket_vehicle($id)->result();

          foreach ($data['vehicle'] as $key => $value) {
            $image_name = 'QR_'.$value->ticket_number.'.png';

            $params['data'] = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
            $params['level'] = 'H';
            $params['size'] = 10;
            $params['savename'] = FCPATH.$config['imagedir'].$image_name;
            $value->qrcode = $this->ciqrcode->generate($params);
          }
          ob_start();
          $this->load->view($this->_module.'/print_vehicle',$data);
        }

  }
  public function download_pdf_21112022($booking_code, $service_id){
    $this->global_model->checkAccessMenuAction($this->_module,'download_pdf');

    $config_param = get_config_param('reservation');

    $booking_code=$this->enc->decode($booking_code);
    $service_id=$this->enc->decode($service_id);

    $data['title'] = 'Print';
    $data['booking_code'] = $booking_code;
    if($service_id == 1){
      $data['passanger']=$this->sab_model->ticket("where B.booking_code='".$booking_code."' ")->result();

      foreach ($data['passanger'] as $key => $value) {
        $value->ticket_number_qr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
      }
      $this->ticket($data, $service_id);
      // ob_start();
      // $this->load->view($this->_module.'/print',$data);
      // $content = ob_get_clean();
    }else{
      $data['vehicle']=$this->sab_model->ticket_vehicle($booking_code)->result();

      foreach ($data['vehicle'] as $key => $value) {
        $value->ticket_number_qr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
      }
      $this->ticket($data, $service_id);
      // ob_start();
      // $this->load->view($this->_module.'/print_vehicle',$data);
      // $content = ob_get_clean();
    }

}

  public function download_pdf_17102023($booking_code, $service_id){
      $this->global_model->checkAccessMenuAction($this->_module,'download_pdf');

      $config_param = get_config_param('reservation');

      $booking_code=$this->enc->decode($booking_code);
      $service_id=$this->enc->decode($service_id);

      $data['title'] = 'Print';
      $data['booking_code'] = $booking_code;
      $data['booking_qr'] = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$booking_code);

      $data['eticket_alert_1'] = $this->sab_model->selectData("app.t_mtr_info"," where name='eticket_alert_1' ")->row();

      $data['eticket_alert_2'] = $this->sab_model->selectData("app.t_mtr_info"," where name='eticket_alert_2' ")->row();
      $data['eticket_alert_3'] = $this->sab_model->selectData("app.t_mtr_info"," where name='eticket_alert_3' ")->row();
      
      if($service_id == 1){
        $data['passanger']=$this->sab_model->ticket("where B.booking_code='".$booking_code."' and BP.status not in ('-5','-6')")->result();

        foreach ($data['passanger'] as $key => $value) {
          $value->ticket_number_qr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
        }

      }else{
        $data['vehicle']=$this->sab_model->ticket_vehicle($booking_code)->result();

        foreach ($data['vehicle'] as $key => $value) {
          $value->ticket_number_qr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
        }

      }
      $this->ticket($data, $service_id);

  }

  public function listDetail(){

      $booking_code=$this->enc->decode($this->input->post('id'));

      $rows = $this->sab_model->listDetail("where a.booking_code='".$booking_code."' ")->result();
      echo json_encode($rows);

  }

  public function listVehicle(){

      $booking_code=$this->enc->decode($this->input->post('id'));

      $rows = $this->sab_model->listVehicle("where a.booking_code='".$booking_code."' ")->result();
      echo json_encode($rows);

  }

  function get_dock()
  {
      $port=$this->enc->decode($this->input->post('port'));

      empty($port)?$port_id='NULL':$port_id=$port;
      $dock=$this->dock->select_data('app.t_trx_booking',"where port_id=".$port_id." and status=1")->result();

      $data=array();
      foreach($dock as $key=>$value)
      {
          $value->id=$this->enc->encode($value->id);
          $data[]=$value;
      }

      echo json_encode($data);
  }

  public function rute(){
    $origin = $this->input->post('origin');
    $data['port']=$this->sab_model->get_route($origin)->result();
    echo json_encode(array(
      'code' => 1,
      'data' => $data['port']
    ));
  }

  public function schedule_time(){
    $data['schedule_time']=$this->sab_model->select_data("app.t_mtr_schedule_time"," where status=1 order by schedule_id asc, departure asc limit 25")->result();
    echo json_encode(array(
      'code' => 1,
      'data' => $data['schedule_time']
    ));
  }

  public function generateRandomString($origin, $length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    $identity_app=$this->sab_model->select_data("app.t_mtr_identity_app","")->row();
    $bookCode = sprintf('%02d', $origin).$randomString;
    return $bookCode;
  }

  public function insertTransaction($data,$booking_channel,$port) {
    $fields = '';
    $values = '';

    foreach($data as $x => $value) {
        $fields .= $x.","; 
        // $values .= "'".$value."',";
        $values .= "'".str_replace("'", " ", $value)."',";
    }

    $fields  = rtrim($fields, ',');
    $values = rtrim($values, ',');

    $query = "
    INSERT INTO app.t_trx_invoice  
    (trans_number,".$fields.") 
    VALUES
    (
    (   
    SELECT 
    '".$booking_channel."'||SUBSTRING(EXTRACT(YEAR FROM now())::varchar, 3,2)||to_char(EXTRACT(DOY FROM now()), 'fm000')||to_char(EXTRACT(HOURS FROM now()), 'fm000')||(to_char(nextval('app.trans_number_seq'), 'fm0000') ) as trans_number 
    ),
    ".$values."
    ) 
    RETURNING id, trans_number
    ";

    $result = $this->db->query($query, false, true);

    return $result->row();
  }

  function getInit(){   
    validate_ajax();
    $init        = $this->sab_model->initialize();
    // $date        = '';
    $max_booking = array('adult' => 1,'child'=> 0, 'infant'=> 0, 'elder'=> 0);
    $arr         = array();
    $arrInitPort[]      = array('id' => '', 'text' => '', "city" => "");
    $serv[]      = array('id' => '', 'text' => '');
    $serv2       = array();
    $arr2[]      = array('id' => '', 'text' => '');
    $ship        = array();
    // $portOther[] = array('text' => 'LAINNYA', 'children' => array(array('id' => '99', 'text' => 'Pilih Pelabuhan lainnya')));

    if($init){
        if($init->code == 1){
            // $min_booking_date   = date('d-m-Y', strtotime($init->data->config_param->min_booking_date));
            // $max_booking_date   = date('d-m-Y', strtotime($init->data->config_param->max_booking_date));
            $portOrigin         = $init->data->origin_port;
            $service            = $init->data->service;
            $ship_class         = $init->data->ship_class;
            $max_booking        = $init->data->config_param;
            $max_booking_pass        = $init->data->max_booking_pass;

            // $app_id=$this->sab_model->select_data("app.t_mtr_identity_app","")->row();

            // foreach ($portOrigin as $key => $value) {
            //   if($app_id->port_id != 0){
            //     if($app_id->port_id == $value->id){
            //       $arr[$key]['id']   = $value->id;
            //       $arr[$key]['text'] = $value->name;
            //     }
            //   } else {
            //     $arr[$key]['id']   = $value->id;
            //     $arr[$key]['text'] = $value->name;
            //   }
            // }

            // foreach ($portOrigin as $k => $v) {
            //   if($app_id->port_id != 0){
            //     if($app_id->port_id == $v->id){
            //       $arr2[] = array(
            //           'text' => $v->city,
            //           'children' => array($arr[$k])
            //       );
            //     }
            //   } else {
            //     $arr2[] = array(
            //         'text' => $v->city,
            //         'children' => array($arr[$k])
            //     );
            //   }
            // }

            foreach ($portOrigin as $key => $value) {
                // $arr[$key]['id']   = $value->id;
                // $arr[$key]['text'] = $value->name;
                $arr[$key]['id']   = $value->id;
                $arr[$key]['text'] = ucwords(strtolower($value->name)) . ", " . ucwords(strtolower($value->city));
                $arr[$key]['city'] = $value->city;
            }

            foreach ($portOrigin as $k => $v) {
                $arr2[] = array(
                    'text' => $v->city,
                    'children' => array($arr[$k])
                );
            }

            foreach ($service as $key => $value) {
                $serv2[$key]['id']   = $value->id;
                $serv2[$key]['text'] = $value->name;
            }

            foreach ($ship_class as $key => $value) {
                $ship[$key]['id']   = $value->id;
                $ship[$key]['text'] = $value->name;
            }
        }
    }

    echo json_encode(array(
        'code'              => $init->code,
        'message'           => $init->message,
        'origin'            => array_merge($arrInitPort, $arr),
        // 'start_booking_date'=> $min_booking_date,
        // 'end_booking_date'  => $max_booking_date,
        'service'           => array_merge($serv,$serv2),
        'ship_class'        => $ship,
        // 'service'           => $serv2,
        'config'            => $max_booking,
        'max_booking_pass'            => $max_booking_pass,
        'csrfName'         =>$this->security->get_csrf_token_name(),
        'tokenHash'        =>$this->security->get_csrf_hash(),

    ));
  }

  function getSchedule(){
    validate_ajax();
    $port  = $this->sab_model->getPortListDestination($this->input->post('origin'));
    $arr   = array();
    $arr2[]= array('id' => '', 'text' => '');
    if($port){
        if($port->code == 1){
            $destination = $port->data->schedule;

            // foreach ($destination as $key => $value) {
            //     $arr[$key]['id']   = $value->destination_port_id;
            //     $arr[$key]['text'] = $value->destination_port_name;
            // }

            // foreach ($destination as $k => $v) {
            //     $arr2[] = array(
            //         'text' => $v->destination_city,
            //         'children' => array($arr[$k])
            //     );
            // }
            foreach ($destination as $key => $value) {
              $arr[$key]['id']   = $value->destination_port_id;
              $arr[$key]['text'] = ucwords(strtolower($value->destination_port_name)) . ", " . ucwords(strtolower($value->destination_city));
              $arr[$key]['city'] = $value->destination_city;
          }
        }
    }

    echo json_encode(array(
        'code'             => $port->code,
        'message'          => $port->message,
        'port_destination' => $arr,
        'csrfName'         =>$this->security->get_csrf_token_name(),
        'tokenHash'        =>$this->security->get_csrf_hash(),

    ));
  }

  function getShipClass()
  {
      validate_ajax();
      $result = $this->sab_model->get_ship_class_rute($this->security->xss_clean($this->input->get('origin')), $this->security->xss_clean($this->input->get('destination')));
      $arr    = array();
      $arr2[] = array('id' => '', 'text' => '');


      if ($result) {
              $data = $result;
              foreach ($data as $i => $value) {
                  $arr[$i]['id']          = $value->id;
                  $arr[$i]['text']        = $value->ship_class_name;
              }
      }

      // print_r($data);exit;

      if (count($data)>1){
        echo json_encode(
            array(
                'code'      => 1,
                'message'   => "Success",
                'data'      => array_merge($arr2, $arr),
  		        	$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),

            )
        );
     } else {
        echo json_encode(
            array(
                'code'      => 1,
                'message'   => "Success",
                'data'      => $arr,
  		        	$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
            )
        );
     }
  }

  function getHours__use__table(){
    validate_ajax();
    // $port  = $this->sab_model->getPortHours($this->input->post());
    $time_start=$this->sab_model->select_data("app.t_mtr_schedule_time"," where status=1 order by schedule_id asc, departure asc limit 24")->result();
    $time_end=$this->sab_model->select_data("app.t_mtr_schedule_time"," where status=1 order by schedule_id asc, departure asc limit 24 offset 1")->result();
    $arr[] = array('id' => '', 'text' => '');
    $arr2  = array();

    // if($port){
    //     if($port->code == 1){
    //         $data = $port->data->times;
            foreach ($time_start as $i => $value) {
                $arr2[$i]['id']   = $value->departure."-".$time_end[$i]->departure;
                $arr2[$i]['text'] = date('H:i', strtotime($value->departure))." - ".date('H:i', strtotime($time_end[$i]->departure));
            }
    //     }
    // }

    echo json_encode(array(
        'code'    => 1,
        'message' => 'Sukses',
        'times'   => array_merge($arr,$arr2),
        'csrfName'         =>$this->security->get_csrf_token_name(),
        'tokenHash'        =>$this->security->get_csrf_hash(),
        // 'times'   => $arr2,
    ));
  }

  function getHours() {
    validate_ajax();
    // $times = array();
    $port  = $this->get_times($this->security->xss_clean($this->input->post()));
    $arr[] = array('id' => '', 'text' => '');
    $arr2  = array();

    if ($port) {
        if ($port['code'] == 1) {
            $data = $port['data']['times'];
            foreach ($data as $i => $value) {
                $arr2[$i]['id']   = $value['depart_time_start'] . "-" . $value['depart_time_end'];
                $arr2[$i]['text'] = date('H:i', strtotime($value['depart_time_start'])) . " - " . date('H:i', strtotime($value['depart_time_end']));
            }
        }
    }

    echo json_encode(array(
        'code'    => $port['code'],
        'message' => $port['message'],
        'times'   => array_merge($arr, $arr2),
  			'csrfName'         =>$this->security->get_csrf_token_name(),
        'tokenHash'        =>$this->security->get_csrf_hash(),

        // 'times'   => $arr2,
    ));
  }

  function get_times() {
    $times          = array();
    $depart_date    = $this->input->post('depart_date');
    $origin         = $this->input->post('origin');
    $destination    = $this->input->post('destination');
    $ship_class     = $this->input->post('ship_class');
    $config_param   = get_config_param('reservation');
    $booking_time   = isset($config_param['booking_time']) ? $config_param['booking_time'] : 0;
    $book_start_on  = date('Y-m-d H:00', strtotime("+ ".$booking_time." hours"));
    if ($depart_date == date('Y-m-d')) {
        if ($ship_class == 2) {
                $schedule_time_eks = $this->sab_model->get_time($origin,$destination,$depart_date,$ship_class,$book_start_on);
                foreach ($schedule_time_eks as $key => $value) {
                  $time = array('depart_time_start' => date('H:i:s',strtotime($value->docking_on)), 'depart_time_end' => date('H:i:s',strtotime($value->docking_on)), 'schedule_code' => $value->schedule_code, 'ship_name' => $value->ship_name);
                  array_push($times, $time);
                }
        }
        else{
                  $book_start_on = date('H:i', strtotime("+ ".$booking_time." hours"));
                  $time_start = explode(':', $book_start_on);
                  if ($book_start_on < date('H:i')) {
                      $time_start = 24;
                  }
                  else{
                    $time_start = intval($time_start[0]);
                  }
                  for($i = $time_start; $i < 24; $i++){
                      $depart_time_start = date('H:i:s',strtotime($i.':00:00'));
                      $depart_time_end   = date('H:i:s',strtotime(($i+1).':00:00'));
                      if($depart_time_start > $depart_time_end){
                          $depart_time_end = "24:00:00";
                    }
                    $time = array('depart_time_start' => $depart_time_start, 'depart_time_end' => $depart_time_end,'schedule_code' => "","ship_name" => "");
                    array_push($times, $time);
                  }
        }
    }
    else if($depart_date > date('Y-m-d')){
        if ($ship_class == 2) {
                // bug fixing cross date schedule express
                $schedule_time_eks = $this->sab_model->get_time($origin,$destination,$depart_date,$ship_class,$book_start_on);
                // end  bug fixing cross date schedule express
                foreach ($schedule_time_eks as $key => $value) {
                  $time = array('depart_time_start' => date('H:i:s',strtotime($value->docking_on)), 'depart_time_end' => date('H:i:s',strtotime($value->docking_on)), 'schedule_code' => $value->schedule_code, 'ship_name' => $value->ship_name);
                  array_push($times, $time);
                }
        }
        else{
                // for($i = 0; $i < 24; $i++){
                //     $depart_time_start = date('H:i:s',strtotime($i.':00:00'));
                //     $depart_time_end   = date('H:i:s',strtotime(($i+1).':00:00'));
                //     if($depart_time_start > $depart_time_end){
                //         $depart_time_end = "24:00:00";
                //   }
                //   $time = array('depart_time_start' => $depart_time_start, 'depart_time_end' => $depart_time_end,'schedule_code' => "","ship_name" => "");
                //   array_push($times, $time);
                // }

                // bug fixing cross date reguler
                $book_start_on      = date('Y-m-d', strtotime("+ " . $booking_time . " hours"));
                $book_start_time_on = date('H:i', strtotime("+ " . $booking_time . " hours"));
                $time_start         = explode(':', $book_start_time_on);
                $time_start         = ($book_start_on == $depart_date) ? intval($time_start[0]) : 0;

                for ($i = $time_start; $i < 24; $i++) {
                    $depart_time_start = date('H:i:s', strtotime($i . ':00:00'));
                    $depart_time_end   = date('H:i:s', strtotime(($i + 1) . ':00:00'));
                    if ($depart_time_start > $depart_time_end) {
                        $depart_time_end = "24:00:00";
                    }
                    $time = ['depart_time_start' => $depart_time_start, 'depart_time_end' => $depart_time_end];
                    array_push($times, $time);
                }
                // end bug fixing cross date reguler
        }
    }
    else{
        $response = array(
            "code"    => 130,
            "message" => 'Invalid tanggal keberangkatan',
            "data"    => null,
        );
        echo json_encode($response);
        exit();
    }

    if (count($times) == 0) {
        $response = array(
          "code"    => 130,
          "message" => 'No data found',
          "data"    => null,
      );
    }
    else{
      $data = array('times' => $times);
      $response = array(
            "code"    => 1,
            "message" => 'Success',
            "data"    => $data,
        );
    }
  return ($response);
  }

  function getVehicle(){
    validate_ajax();
    $portOrigin = $this->input->get('portOrigin');
    $ship_class = $this->input->get('ship_class');

    $result = $this->sab_model->getVehicleClass($portOrigin,  $ship_class);
    // $arr    = array();

    if($result){
        if($result->code == 1){
            $data = $result->data->vehicle_class;

            foreach ($data as $i => $value) {
                $arr[$i]['id']          = $value->id;
                $arr[$i]['text']        = $value->name;
                $arr[$i]['capacity']    = $value->max_capacity;
                $arr[$i]['description'] = $value->description;

            }
        }
    }
    echo json_encode(array(
        'code'      => $result->code,
        'message'   => $result->message,
        'data'      => $arr,
        'csrfName'         =>$this->security->get_csrf_token_name(),
        'tokenHash'        =>$this->security->get_csrf_hash(),
			// $this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
    )
    );
  }

  public function penumpang(){
		if($this->input->is_ajax_request()){
      $this->validate_param_datatable($_POST,$this->_module);
      if($this->input->post('dateFrom')){
        $this->form_validation->set_rules('dateFrom', 'Tanggal awal', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal awal'));
      }
      if($this->input->post('dateTo')){
        $this->form_validation->set_rules('dateTo', 'Tanggal akhir', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal akhir'));
      }
      if($this->input->post('payment_type')){
        $this->form_validation->set_rules('payment_type', 'Tipe pembayaran', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid Tipe pembayaran'));
      }
      if($this->input->post('port_origin')){
        $this->form_validation->set_rules('port_origin', 'Pelabuhan keberangkatan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid Pelabuhan keberangkatan'));
      }
      if($this->input->post('port_destination')){
        $this->form_validation->set_rules('port_destination', 'Pelabuhan Tujuan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid Pelabuhan Tujuan'));
      }
      if($this->input->post('searchData')){
        $this->form_validation->set_rules('searchData', 'searchData', 'trim|callback_special_char', array('special_char' => 'search has contain invalid characters'));
      }
      if($this->input->post('searchName')){
        $this->form_validation->set_rules('searchName', 'searchName', 'trim|callback_special_char', array('special_char' => 'searchName has contain invalid characters'));
      }
      
      if ($this->form_validation->run() == FALSE) 
      {
        echo $res = json_api(0, validation_errors(),[]);
        exit;
      }
      
			$rows = $this->sab_model->listPenumpang();
			echo json_encode($rows);
		}
	}

	public function kendaraan(){
		if($this->input->is_ajax_request()){
      $this->validate_param_datatable($_POST,$this->_module);
      if($this->input->post('dateFrom')){
        $this->form_validation->set_rules('dateFrom', 'Tanggal awal', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal awal'));
      }
      if($this->input->post('dateTo')){
        $this->form_validation->set_rules('dateTo', 'Tanggal akhir', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal akhir'));
      }
      if($this->input->post('payment_type')){
        $this->form_validation->set_rules('payment_type', 'Tipe pembayaran', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid Tipe pembayaran'));
      }
      if($this->input->post('port_origin')){
        $this->form_validation->set_rules('port_origin', 'Pelabuhan keberangkatan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid Pelabuhan keberangkatan'));
      }
      if($this->input->post('port_destination')){
        $this->form_validation->set_rules('port_destination', 'Pelabuhan Tujuan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid Pelabuhan Tujuan'));
      }
      if($this->input->post('searchData')){
        $this->form_validation->set_rules('searchData', 'searchData', 'trim|callback_special_char', array('special_char' => 'search has contain invalid characters'));
      }
      if($this->input->post('searchName')){
        $this->form_validation->set_rules('searchName', 'searchName', 'trim|callback_special_char', array('special_char' => 'searchName has contain invalid characters'));
      }
      
      if ($this->form_validation->run() == FALSE) 
      {
        echo $res = json_api(0, validation_errors(),[]);
        exit;
      }
			$rows = $this->sab_model->listKendaraan();
			echo json_encode($rows);
		}
  }

  function ticket($data, $service_id, $email=0) {
    ob_start();
    $date = date('ymdhis');
    $data['email'] = $email;
    $data['title_pdf'] = 'TIKET_SKPT_ASDP_'.$data['booking_code'].'_'.$date.'.pdf';
 
    if($service_id == 1){
      $this->load->view($this->_module.'/print',$data);
    }else {
      $this->load->view($this->_module.'/print_vehicle',$data);
    }

    if($email == 1){
      $content = ob_get_clean();
      $path = generate_ymd_dir('file/ticket');
      $save_path = "{$path}/TIKET_ASDP_{$data['booking_code']}_{$date}.pdf";
      $real_path = APPPATH . "../{$save_path}";
      $this->new_pdf->save($content , $real_path);
      
      return $save_path;
    }
  }

  function send_email($booking_code,$ticket_data) {
    $ticket = str_replace('-', '/', $ticket_data);
    $booking = $this->sab_model->get_info_by_booking_code($booking_code);
    $data = array(
      'trans_number' => $booking->trans_number,
      'booking' => $booking,
      'title' => 'ASDP - Boarding Pass #' . $booking->trans_number,
    );
    $content = $this->load->view('sab/email', $data, TRUE);
    $data_email = array(
      'recipient' => $booking->email,
      'subject' => $data['title'],
      'body' => $content,
      'created_by' => '1',
    );
    $subject        = "ASDP - Boarding Pass #";
    $sendEmail     = mailSend($subject, $booking->email, $content,$ticket);
    if ($sendEmail){
      $data_email['status'] = 1;
    }
    else{
      $data_email['status'] = 0;
    }
    $this->sab_model->add($data_email, '', '', array($ticket));
    $this->start($booking->email);
  }

  public function start($email_addr) {
    $res = $this->sab_model->get_pending($email_addr);
    $outbox = count($res);

    if ($outbox > 0) {
      // echo "\n:) Get {$outbox} outbox.\n\n";
      foreach ($res as $key => $value) {
        $value->files = $this->sab_model->get_attachment($value->id);
        // echo date('Y-m-d H:i:s') . " Sending Email {$value->id} to {$value->recipient}\n";
        $send = $this->sab_model->send($value);
        if ($send) {
          $this->sab_model->set_status($value->id, 1);
          // echo date('Y-m-d H:i:s') . " Email {$value->id} sent!\n\n";
        } else {
          // show_error($this->email->print_debugger());
          $this->sab_model->set_status($value->id, -1);
          // echo date('Y-m-d H:i:s') . " Email {$value->id} not sent!\n\n";
        }
      }
    }
    // else {

    //   echo date('Y-m-d H:i:s');
    //   echo " No outbox :(\n";
    // }
  }

  public function kirim2($booking_code,$service){
    $config_param = get_config_param('reservation');
    $data['booking_code'] = $booking_code;
    if($service == 1){
      $data['passanger']=$this->sab_model->ticket("where B.booking_code='".$booking_code."' ")->result();
      foreach ($data['passanger'] as $key => $value) {
        $value->ticket_number_qr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
      }
      $ticket_data = $this->ticket($data, $service, 1);
    }else{
      $data['vehicle']=$this->sab_model->ticket_vehicle($booking_code)->result();
      foreach ($data['vehicle'] as $key => $value) {
        $value->ticket_number_qr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
      }
      $ticket_data = $this->ticket($data, $service, 1);
    }
    $ticket_data = str_replace('/', "-", $ticket_data);
    $this->send_email($booking_code,$ticket_data);
    // $command = 'php '.FCPATH.'index.php sab/send_email/'.$booking_code.'/'.$ticket_data.' > /dev/null 2>/dev/null &';
    // exec($command);
  }

  public function getPassangerType()
  {
    $origin=$this->input->post('origin');
    $service=$this->input->post('service');
    $shipClass=$this->input->post('shipClass');


    $getPassengerType=array();

    if($service==1 && !empty($shipClass) ) // service penumpang dan shipclass nya tdak kosong
    {
      $getData = $this->sab_model->getPassangerType($origin, $shipClass);
    }
    else if ($service==2 && !empty($shipClass)) // service kendaraan dan shipclass nya tdak kosong
    {
      $getData = $this->sab_model->select_data("app.t_mtr_passanger_type", " where status =1 order by ordering asc ")->result();

    }
    else
    {
      $getData=array();
    }

    foreach ($getData as $key => $value) {

      $explode = explode(" ", $value->name);

      $getPassengerType []= array(
        "name"=>$explode[0],
        "description"=>$value->description,
        "min_age"=>$value->min_age,
        "max_age"=>$value->max_age,
        "code"=>$value->code,

        
      );
    }

    $data=array("code"=>1,
      "data"=>$getPassengerType,
      'csrfName'         =>$this->security->get_csrf_token_name(),
        'tokenHash'        =>$this->security->get_csrf_hash(),
  );

  echo json_encode($data);

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
    $params['data'] =$this->encriptAes($content); //data yang akan di jadikan QR CODE
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
      $aesKey=$this->sab_model->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_key_login') ")->row();
      $aesIv=$this->sab_model->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_iv_login') ")->row();

      return PHP_AES_Cipher::encrypt2($aesKey->param_value,$aesIv->param_value,$obCode);

  }  

  function getInfo($where)
  {
      $whereIn = array_map(function($x){ return "'".$x."'"; },$where);
      $data = $this->sab_model->select_data("app.t_mtr_info"," where name in (".implode(", ", $whereIn).")  ")->result();   

      $returnData = array();
      foreach ($data as $key => $value) {
          $returnData[$value->name]= (object)array("info" => $value->info); 
      }

      return $returnData;
  }

  public function download_pdf($booking_code, $service_id){
    $this->global_model->checkAccessMenuAction($this->_module,'download_pdf');

    $config_param = get_config_param('reservation');

    $bookingCode = $this->enc->decode($booking_code);
    $serviceId = $this->enc->decode($service_id);
  // print_r( $serviceId);exit;
    ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
    $this->load->library('pdfgenerator');
    $date = date('ymdhis');

    
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
    $checkinStart = $this->sab_model->select_data("app.t_mtr_custom_param", " where status=1 and param_name='time_checkin_start_information' ")->row();
    $checkinEnd = $this->sab_model->select_data("app.t_mtr_custom_param", " where status=1 and param_name='time_checkin_end_information' ")->row();
    
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
    $data['title_pdf'] = 'TIKET_SKPT_ASDP_'.$bookingCode.'_'.$date.'.pdf';
    
    $data['booking_code'] = $bookingCode;

    if($serviceId == 1){
      $dataPassenger=$this->sab_model->ticket("where B.booking_code='".$bookingCode."' and BP.status not in ('-5','-6')")->result();

      $contentQr="";
      foreach ($dataPassenger as $key => $value) {
          $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
      }
      
      $data['booking_qr'] = $this->generateQr($contentQr);
      $data['passenger']=$dataPassenger; 
      $html = $this->load->view($this->_module.'/print',$data, true);    


    }elseif($serviceId == 2){
      $dataVehicle = $this->sab_model->ticket_vehicle($bookingCode)->result();
      $config_param = get_config_param('reservation');
    
      $contentQr="";
      foreach ($dataVehicle as $key => $value) {
          $contentQr = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$value->ticket_number);
      }
      $data['booking_qr'] = $this->generateQr($contentQr);
      $data['vehicle']=$dataVehicle;
      $html = $this->load->view($this->_module.'/print_vehicle',$data, true );          
  
  }else{
      $this->load->view('error_404');
  }

    // setting paper
    $filename = 'TIKET_SKPT_ASDP_'.$bookingCode.'_'.$date;
    $paper = 'A4';
    $orientation = "portrait";

    // run dompdf
    $this->pdfgenerator->generate($html, $filename,$paper,$orientation);


}

}
