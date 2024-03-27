<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Device_class extends MY_Controller{
	public function __construct() {
		parent::__construct();

        logged_in();
        $this->load->model('M_deviceclass','device');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library(array('bcrypt'));

        $this->_table    = 'app.t_mtr_device_terminal';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'device_management/device_class';
	}

	public function index() {
        checkUrlAccess(uri_string(),'view');

        if($this->input->is_ajax_request()) {
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('service')){
                $this->form_validation->set_rules('service', 'service', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid service'));
            }
            if($this->input->post('port')){
                $this->form_validation->set_rules('port', 'port', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
            }
            if($this->input->post('device_type')){
                $this->form_validation->set_rules('device_type', 'device_type', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid tipe device'));
            }
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->device->dataList();
            $rows["tokenHash"] = $this->security->get_csrf_hash();
            $rows["csrfName"] = $this->security->get_csrf_token_name();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'          => 'Home',
            'url_home'      => site_url('home'),
            'title'         => 'Perangkat',
            'content'       => 'device_class/index',
            'btn_add'       => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            // 'btn_excel'     => generate_button_new($this->_module, 'download_excel',  site_url($this->_module.'/download_excel')),
            'port'          => $this->device->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result(),
            'ship'          => $this->device->select_data("app.t_mtr_ship","where status='1' order by name asc")->result(),
            'dock'          => $this->device->select_data("app.t_mtr_dock","where status='1' order by name asc")->result(),
            'device_type'   => $this->device->select_data("app.t_mtr_device_terminal_type","where status not in (-5) order by terminal_type_name asc")->result(),
            'service'       => $this->device->select_data("app.t_mtr_service","where status not in (-5) order by name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title']          = 'Tambah Perangkat';
        $data['service']        = $this->device->select_data("app.t_mtr_service","where status='1' order by name asc")->result();
        $data['port']           = $this->device->select_data("app.t_mtr_port","where status='1' order by name asc")->result();
        $data['ship_class']     = $this->device->select_data("app.t_mtr_ship_class","where status='1' order by name asc")->result();
        $data['vehicle_class']     = $this->device->select_data("app.t_mtr_vehicle_class","where status='1' order by name asc")->result();        
        $data['terminal_type']  = $this->device->select_data("app.t_mtr_device_terminal_type","where status='1' order by terminal_type_name asc")->result();
        $data['license_number'] = $this->device->select_data("app.t_mtr_license","WHERE status = '1' AND imei IS NOT NULL AND imei != '' ORDER BY id DESC")->result();
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        $this->form_validation->set_rules('device_name', 'Nama device', 'required|max_length[100]|callback_special_char', array('special_char' => 'nama device memuat invalid karakter'));
        if($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
            exit;
        }

        $device_type_id = $this->input->post('device_type_terminal');
        $device_name    = trim($this->input->post('device_name'));
        $port           = $this->enc->decode($this->input->post('port'));
        $password       = $this->bcrypt->hash_password(strtoupper(md5($this->input->post('password'))));
        $get_portcode   = $this->device->select_data("app.t_mtr_port"," where id=".$port)->row();
        $code           = $this->createCode($get_portcode->port_code, $device_type_id);
        $crossClass     = trim($this->input->post('crossClass'));

        $checkAllComandCenter  = 0;

        if ( $device_type_id == 4 || $device_type_id == 5   ) {
            $ship_class = $this->enc->decode($this->input->post('ship_class'));

            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('password', 'password', 'required');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));

            $this->form_validation->set_message('required','%s harus diisi!');
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }

            $data = array(
                'terminal_code'=>$code,
                'terminal_name'=>$device_name,
                'terminal_type'=>$device_type_id,
                'port_id'=>$port,
                'password'=>$password,
                'ship_class'=>$ship_class,
            );
        }
        else if ($device_type_id == 1 || $device_type_id == 2 || $device_type_id == 3 || $device_type_id == 12  ) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('password', 'password', 'required');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_rules('crossClass','Antar Pelabuhan', 'required');

            $this->form_validation->set_message('required','%s harus diisi!');
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }

            $ship_class = $this->enc->decode($this->input->post('ship_class'));


            $data = array(
                'terminal_code'=>$code,
                'terminal_name'=>$device_name,
                'terminal_type'=>$device_type_id,
                'port_id'=>$port,
                'password'=>$password,
                'ship_class'=>$ship_class,
                'cross_class'=>$crossClass,
            );
        }


        // else if($device_type_id == 3) {
        //     $this->form_validation->set_rules('device_type_terminal', 'Pelabuhan', 'required');
        //     $this->form_validation->set_rules('device_name', 'Nama device', 'required');
        //     $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        //     $this->form_validation->set_rules('password', 'password', 'required');
        //     $this->form_validation->set_message('required','%s harus diisi!');

        //     $data = array(
        //         'terminal_code' => $code,
        //         'terminal_name' => $device_name,
        //         'terminal_type' => $device_type_id,
        //         'port_id'       => $port,
        //         'password'      => $password,
        //     );
        // }

        else if($device_type_id == 7 || $device_type_id == 8) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('password', 'password', 'required');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_rules('dock', 'dock', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_message('required','%s harus diisi!');
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }

            $dock_id    = $this->enc->decode($this->input->post('dock'));
            $ship_class = $this->enc->decode($this->input->post('ship_class'));

            

            $data = array(
                'terminal_code'=>$code,
                'terminal_name'=>$device_name,
                'terminal_type'=>$device_type_id,
                'port_id'=>$port,
                'dock_id'=>$dock_id,
                'ship_class'=>$ship_class,
                'password'=>$password,
            );   
        }

        else if ($device_type_id == 6) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('password', 'password', 'required');
            $this->form_validation->set_rules('imei', 'imei', 'required|max_length[50]|callback_special_char', array('special_char' => 'Imei memuat invalid karakter'));
            $this->form_validation->set_rules('dock', 'dock', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }


            $dock_id    = $this->enc->decode($this->input->post('dock'));
            $imei       = trim($this->input->post('imei'));
            // $ship_class = $this->enc->decode($this->input->post('ship_class'));

            $check_imei = $this->device->select_data($this->_table," where upper(imei)=upper('".$imei."') and status not in (-5) ");

            if($check_imei->num_rows()>0) {
                echo $res = json_api(0, "Imei sudah terdaftar");
                exit;
            }

           
            $data = array(
                'terminal_code' => $code,
                'terminal_name' => $device_name,
                'terminal_type' => $device_type_id,
                'port_id'       => $port,
                'dock_id'       => $dock_id,
                // 'ship_class'    => $ship_class,
                'imei'          => strtoupper($imei),
                'password'      => $password,
            );
        }

        else if ($device_type_id == 11 || $device_type_id == 20 ) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('password', 'password', 'required');
            $this->form_validation->set_rules('imei', 'imei', 'required|max_length[50]|callback_special_char', array('special_char' => 'Imei memuat invalid karakter'));
            $this->form_validation->set_message('required','%s harus diisi!');

            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }


            $imei = trim($this->input->post('imei'));
            // $ship_class = $this->enc->decode($this->input->post('ship_class'));

            $check_imei = $this->device->select_data($this->_table," where upper(imei)=upper('".$imei."') and status not in (-5) ");

            if($check_imei->num_rows() > 0) {
                echo $res = json_api(0, "Imei sudah terdaftar");
                exit;
            }


            $data = array(
                'terminal_code' => $code,
                'terminal_name' => $device_name,
                'terminal_type' => $device_type_id,
                'port_id'       => $port,
                'imei'          => strtoupper($imei), // tadinya imei di ganti diisi serial numbar
                'password'      => $password,
            );   
        }

        else if($device_type_id == 16 || $device_type_id == 17 || $device_type_id == 15) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('password', 'password', 'required');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_rules('imei', 'imei', 'required|max_length[50]|callback_special_char', array('special_char' => 'Imei memuat invalid karakter'));
            $this->form_validation->set_rules('crossClass','Antar Pelabuhan', 'required');
            $this->form_validation->set_message('required','%s harus diisi!');

            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }


            $ship_class = $this->enc->decode($this->input->post('ship_class'));
            $imei       = trim($this->input->post('imei'));
            $check_imei = $this->device->select_data($this->_table," where upper(imei)=upper('".$imei."') and status not in (-5) ");

            if($check_imei->num_rows() > 0) {
                echo $res = json_api(0, "Imei sudah terdaftar");
                exit;
            }

            

            $data = array(
                'terminal_code'     => $code,
                'terminal_name'     => $device_name,
                'terminal_type'     => $device_type_id,
                'port_id'           => $port,
                'password'          => $password,
                'imei'              => $imei,
                'ship_class'        => $ship_class,
                'cross_class'        => $crossClass,
            );
        }

        else if($device_type_id == 18 ) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('posPairing', 'Pos Pairing', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pairing pos'));
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_message('required','%s harus diisi!');
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }


            $posPairing = $this->enc->decode($this->input->post('posPairing'));
            $shipClass = $this->enc->decode($this->input->post('ship_class'));

            $check_posPairing = $this->device->select_data($this->_table," where upper(pairing_pos)=upper('".$posPairing."') and status not in (-5) and port_id='{$port}' ");

            if($check_posPairing->num_rows() > 0) {
                echo $res = json_api(0, "Perangkat sudah terpairing di pelabuhan yang sama");
                exit;
            }
            

            $data = array(
                'terminal_code'     => $code,
                'terminal_name'     => $device_name,
                'terminal_type'     => $device_type_id,
                'port_id'           => $port,
                'password'          => $this->bcrypt->hash_password(strtoupper(md5('admin123'))),
                'pairing_pos'       =>$posPairing,
                'ship_class'        =>$shipClass,

            );
        }    
        else if ($device_type_id == 19) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_message('required','%s harus diisi!');

            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }



            $ship_class = $this->enc->decode($this->input->post('ship_class'));
           

            $data = array(
                'terminal_code'=>$code,
                'terminal_name'=>$device_name,
                'terminal_type'=>$device_type_id,
                'port_id'=>$port,
                'password'=>$this->bcrypt->hash_password(strtoupper(md5('admin123'))),
                'ship_class'=>$ship_class,
            );
        }
        // self service
        else if ($device_type_id == 21) {

            $userPhone = $this->enc->decode(trim($this->input->post("userPhone")));

            $cctvpath = $this->input->post("cctvpath[]");
            $vehicle_class_multiple_value = $this->input->post('vehicle_class_multiple_value'); 
            $vehicle_class = $this->enc->decode($this->input->post('vehicle_class')); 
            $isSpesific = $this->input->post('isSpesific'); 
            $ip = trim($this->input->post('ip')); 

            $overpaid = $this->input->post('overpaid'); 
            $sensor = $this->input->post('sensor'); 

            $overpaid = $overpaid==1?"true":"false";
            $sensor= $sensor==1?"true":"false";

            $_POST['vehicle_class']=$vehicle_class;
            $_POST['userPhone']=$userPhone;
            
            $userPhoneselfService = ""; 
            $passwordPhoneSelfservice = ""; 
            $extPhoneSelfservice= "";            

            $checkAllComandCenter  = 0;
            $userPhoneselfServiceId = $userPhone;
            if(!empty($userPhoneselfServiceId))
            {
                // check apakah sudah di pakai 
                $checkUserPhone = $this->device->select_data("app.t_mtr_phone_extension", "where is_used =0 and id =".$userPhoneselfServiceId)->row();
                if(count($checkUserPhone) > 0)
                {
                    $checkAllComandCenter  = 1;
                    $userPhoneselfService = $checkUserPhone->username_phone; 
                    $passwordPhoneSelfservice = $checkUserPhone->password_phone; 
                    $extPhoneSelfservice= $checkUserPhone->extension_phone;
                }
            }            
            
            $dataCctvPath = [];
            if(!empty($cctvpath))
            {
                foreach ($cctvpath as $keyCctvpath => $valueCctvpath) {
                    if(!empty($valueCctvpath))
                    {
                        $dataCctvPath[]=$valueCctvpath;                        
                    }
                    $this->form_validation->set_rules('cctvpath['.$keyCctvpath.']', 'CCTV PATH', 'required|callback_special_char_url');                                       
                }
            }
            
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');            
            $this->form_validation->set_rules('password', 'password', 'required');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_rules('crossClass','Antar Pelabuhan', 'required');
            $this->form_validation->set_rules('userPhone','User Phone', 'required');
            $this->form_validation->set_rules('ip', 'ip', 'required|callback_special_char_url');

            
            if($isSpesific == 't')
            {                
                $this->form_validation->set_rules('vehicle_class', ' golongan kendaraan', 'required');
                $dataVehicle[]=$vehicle_class;
            }
            else
            {
                $this->form_validation->set_rules('vehicle_class_multiple_value', ' golongan kendaraan', 'required');
                $mapVehicleClass = array_map(function($x){return (int)$this->enc->decode($x);},explode(",",$vehicle_class_multiple_value) );
                // print_r($mapVehicleClass ); exit;
    
                $dataVehicle = array_filter($mapVehicleClass,function($f){ return $f != "";});
            }
            

            $this->form_validation->set_message('numeric','%s harus angka!');
            $this->form_validation->set_message('numeric_dot','%s harus angka atau titik!');
            $this->form_validation->set_message('required','%s harus diisi!');
            $this->form_validation->set_message('special_char_url','%s Invalid  Karakter!');
            

            if($this->form_validation->run() === false) {
                $errMess = array_map(function($x){ return "<p>".$x."</p>"; }, array_unique($this->form_validation->error_array()));
                echo $res = json_api(0, implode("",$errMess));
                exit;
            }

            if($isSpesific != 't' && count($dataVehicle) <2 )
            {
                echo $res = json_api(0, " Jika multiple , minimal harus 2 data ");
                exit;
            }

            // check comand center apa sudah terbapaki
            if($checkAllComandCenter < 1)
            {
                echo $res =  json_api(0,' Username ext, password ext dan ext Sudah terpakai'); 
                exit;
            }

            $ship_class = $this->enc->decode($this->input->post('ship_class'));

            $data = array(
                'terminal_code'=>$code,
                'terminal_name'=>$device_name,
                'terminal_type'=>$device_type_id,
                'port_id'=>$port,
                'password'=>$password,
                'ship_class'=>$ship_class,
                'cross_class'=>$crossClass,
                'username_phone' => $userPhoneselfService,
                'extension_phone' => $extPhoneSelfservice,
                'password_phone' => $passwordPhoneSelfservice,
                'cctv_path'=>json_encode($dataCctvPath),
                'is_specific'=>$isSpesific=='t'?"true":"false",
                'vehicle_class_id'=>json_encode($dataVehicle),
                'enable_overpaid_underpaid'=>$overpaid,
                'enable_sensor'=>$sensor,
                'ip_device'=>$ip,
            );
            // print_r($data); exit;
        }        

        else {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('password', 'password', 'required');
            $this->form_validation->set_message('required','%s harus diisi!');
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }
           

            $data = array(
                'terminal_code' => $code,
                'terminal_name' => $device_name,
                'terminal_type' => $device_type_id,
                'port_id'       => $port,
                'password'      => $password,
            );
        }

        $check_name = $this->device->select_data($this->_table," where upper(terminal_name)=upper('".$device_name."') and status=1 ");

        if($check_name->num_rows() > 0) {
            echo $res = json_api(0, "Nama sudah ada");   
        }
        else {
            $this->db->trans_begin();
            $data['status']     = 1;
            $data['created_on'] = date('Y-m-d H:i:s');
            $data['created_by'] = $this->session->userdata('username');
            $this->device->insert_data($this->_table,$data);

            if($device_type_id  == 21)
            {
                // is used 1 user , 2 perangkat/ device
                $upadatedata = array("is_used"=> 2,
                "used_by"=>$data['terminal_code'] ,
                "updated_by"=> $this->session->userdata("username"),
                "updated_on"=>date("Y-m-d H:i:s")                  
                );
                $this->device->update_data("app.t_mtr_phone_extension", $upadatedata, " id=".$userPhoneselfServiceId);   
            }


            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res = json_api(0, 'Gagal tambah data');
            }
            else {
                $this->db->trans_commit();
                echo $res = json_api(1, 'Berhasil tambah device');
            }
        }
    }

    public function edit($id) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $terminal_id = $this->enc->decode($id);
        $get_port_id = $this->device->select_data($this->_table," where device_terminal_id=".$terminal_id)->row();

        $getPairingPos=$this->device->getPosVehicle($get_port_id->port_id);

        $dataPairingPos[""]="Pilih";
        $selectedDataPairingPos="";
        foreach ($getPairingPos as $key => $value) {
            if($get_port_id->pairing_pos==$value->terminal_code)
            {
                $selectedDataPairingPos =$this->enc->encode($value->terminal_code);
                $dataPairingPos[$selectedDataPairingPos]=$value->terminal_name;
            }
            else
            {
                $dataPairingPos[$this->enc->encode($value->terminal_code)]=$value->terminal_name;   
            }
        }

        $data['title']          = 'Edit Tipe Perangkat';
        $data['port']           = $this->device->select_data("app.t_mtr_port","where status='1' order by name asc")->result();

        $data['terminal_type']  = $this->device->select_data("app.t_mtr_device_terminal_type","where status='1' order by terminal_type_id asc")->result();
        $data['dataPairingPos'] = $dataPairingPos;
        $data['selectedDataPairingPos'] = $selectedDataPairingPos;

        $data['ship_class']     = $this->device->select_data("app.t_mtr_ship_class"," where status=1 order by name asc")->result();
        $data['detail']         = $this->device->select_data($this->_table," where device_terminal_id=".$terminal_id)->row();
        $data['dock']           = $this->device->select_data("app.t_mtr_dock","where port_id=".$get_port_id->port_id." and status=1 order by name asc")->result();
        $data['license_number'] = $this->device->select_data("app.t_mtr_license","WHERE status = '1' AND imei IS NOT NULL AND imei != '' ORDER BY id DESC")->result();

        $data['countCctv'] = count(json_decode($data['detail']->cctv_path))>0?count(json_decode($data['detail']->cctv_path)):0;
        $data['vehicle_class']     = $this->device->select_data("app.t_mtr_vehicle_class","where status='1' order by name asc")->result();    
        
        $dataVehicleId = json_decode($data['detail']->vehicle_class_id);
        $data['vehicle_class_selected']     = @array_combine($dataVehicleId,$dataVehicleId);
        
        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $device_terminal_id = $this->enc->decode($this->input->post('device_terminal_id'));
        $_POST["device_terminal_id"] = $device_terminal_id;         

        $this->form_validation->set_rules('device_terminal_id', 'device terminal id', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
            $this->form_validation->set_rules('device_name', 'Nama device', 'required|max_length[100]|callback_special_char', array('special_char' => 'nama device memuat invalid karakter'));
        
        if($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
            exit;
        }
        
        $device_type_id     = $this->input->post('device_type_terminal');
        $device_name        = trim($this->input->post('device_name'));
        $port               = $this->enc->decode($this->input->post('port'));
        $get_portcode       = $this->device->select_data("app.t_mtr_port"," where id=".$port)->row();
        $code               = $this->createCode($get_portcode->port_code, $device_type_id);
        $crossClass     = trim($this->input->post('crossClass'));

        if ($device_type_id == 4 || $device_type_id == 5) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_message('required','%s harus diisi!');
            
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }


            $ship_class = $this->enc->decode($this->input->post('ship_class'));

            

            $data = array(
                // 'terminal_code' => $code,
                'terminal_name' => $device_name,
                'terminal_type' => $device_type_id,
                'port_id'       => $port,
                'ship_class'    => $ship_class,
            );
        }
        else if ($device_type_id == 1 || $device_type_id == 2 || $device_type_id == 3 || $device_type_id == 12 ) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_rules('crossClass','Antar Pelabuhan', 'required');
            $this->form_validation->set_message('required','%s harus diisi!');
            
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }


            $ship_class = $this->enc->decode($this->input->post('ship_class'));

            

            $data = array(
                // 'terminal_code' => $code,
                'terminal_name' => $device_name,
                'terminal_type' => $device_type_id,
                'port_id'       => $port,
                'ship_class'    => $ship_class,
                'cross_class'=>$crossClass,
            );
        }        

        // else if($device_type_id == 3) {
        //     $this->form_validation->set_rules('device_type_terminal', 'Pelabuhan', 'required');
        //     $this->form_validation->set_rules('device_name', 'Nama device', 'required');
        //     $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        //     $this->form_validation->set_message('required','%s harus diisi!');

        //     $data = array(
        //         'terminal_code' => $code,
        //         'terminal_name' => $device_name,
        //         'terminal_type' => $device_type_id,
        //         'port_id'       => $port,
        //     );   
        // }

        else if($device_type_id == 7 || $device_type_id == 8) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_rules('dock', 'dock', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_message('required','%s harus diisi!');
            
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }


            $dock_id    = $this->enc->decode($this->input->post('dock'));
            $ship_class = $this->enc->decode($this->input->post('ship_class'));

            

            $data = array(
                // 'terminal_code' => $code,
                'terminal_name' => $device_name,
                'terminal_type' => $device_type_id,
                'port_id'       => $port,
                'dock_id'       => $dock_id,
                'ship_class'    => $ship_class,
            );
        }

        else if ($device_type_id == 6) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('dock', 'dock', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_rules('imei', 'imei', 'required|max_length[50]|callback_special_char', array('special_char' => 'Imei memuat invalid karakter'));
            $this->form_validation->set_message('required','%s harus diisi!');
            
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }


            $dock_id    = $this->enc->decode($this->input->post('dock'));
            $imei       = $this->input->post('imei');
            // $ship_class = $this->enc->decode($this->input->post('ship_class'));

            $check_imei = $this->device->select_data($this->_table," where upper(imei)=upper('".$imei."') and status not in (-5) and device_terminal_id != '".$device_terminal_id ."'");

            if($check_imei->num_rows() > 0) {
                echo $res = json_api(0, "Imei sudah terdaftar");
                exit;
            }
            

            $data = array(
                // 'terminal_code' => $code,
                'terminal_name' => $device_name,
                'terminal_type' => $device_type_id,
                'port_id'       => $port,
                'dock_id'       => $dock_id,
                // 'ship_class'    => $ship_class,
                'imei'          => strtoupper($imei),
            );
   
        }

        // ktp reader
        else if ($device_type_id == 11 || $device_type_id == 20 ) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('imei', 'imei', 'required|max_length[50]|callback_special_char', array('special_char' => 'Imei memuat invalid karakter'));
            $this->form_validation->set_message('required','%s harus diisi!');
            
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }


            $imei       = $this->input->post('imei');
            // $ship_class = $this->enc->decode($this->input->post('ship_class'));

            $check_imei = $this->device->select_data($this->_table," where upper(imei)=upper('".$imei."') and status not in (-5) and device_terminal_id != '".$device_terminal_id ."'");

            if($check_imei->num_rows() > 0) {
                echo $res = json_api(0, "Imei sudah terdaftar");
                exit;
            }

            

            $data = array(
                // 'terminal_code' => $code,
                'terminal_name' => $device_name,
                'terminal_type' => $device_type_id,
                'port_id'       => $port,
                'imei'          => strtoupper($imei),
            );   
        }        

        else if($device_type_id == 16 || $device_type_id == 17 || $device_type_id == 15) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_rules('crossClass','Antar Pelabuhan', 'required');
            $this->form_validation->set_message('required','%s harus diisi!');
            
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }


            $device_type_id = $this->input->post('detail');
            $ship_class     = $this->enc->decode($this->input->post('ship_class'));
            $imei           = $this->input->post('imei');
            $check_imei     = $this->device->select_data($this->_table," where upper(imei)=upper('".$imei."') and status not in (-5) and device_terminal_id != '".$device_terminal_id ."'");

            if($check_imei->num_rows() > 0) {
                echo $res = json_api(0, "Imei sudah terdaftar");
                exit;
            }

            

            $data = array(
                // 'terminal_code'     => $code,
                'terminal_name'     => $device_name,
                'terminal_type'     => $device_type_id,
                'port_id'           => $port,
                // 'password'          => $password,
                'ship_class'        => $ship_class,
                // 'imei'              => $imei,
                'cross_class'       =>$crossClass,
                'cross_class'        => $crossClass,
            );
        }

        else if($device_type_id == 18) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_rules('posPairing', 'Pos Pairing', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pairing pos'));
            $this->form_validation->set_message('required','%s harus diisi!');
            
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }



            $posPairing = $this->enc->decode($this->input->post('posPairing'));
            $shipClass = $this->enc->decode($this->input->post('ship_class'));

            $check_posPairing = $this->device->select_data($this->_table," where upper(pairing_pos)=upper('".$posPairing."') and status not in (-5) and port_id='{$port}' and device_terminal_id<>$device_terminal_id ");

            if($check_posPairing->num_rows() > 0) {
                echo $res = json_api(0, "Perangkat sudah terpairing di pelabuhan yang sama");
                exit;
            }           
            

            $data = array(
                // 'terminal_code'     => $code,
                'terminal_name'     => $device_name,
                'terminal_type'     => $device_type_id,
                'port_id'           => $port,
                'pairing_pos'       =>$posPairing,
                'ship_class'       =>$shipClass,
                

            );
        }
        else if ($device_type_id == 19) {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_message('required','%s harus diisi!');
            
            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }
            
            $ship_class = $this->enc->decode($this->input->post('ship_class'));
            

            $data = array(
                'terminal_name'=>$device_name,
                'terminal_type'=>$device_type_id,
                'port_id'=>$port,
                'ship_class'=>$ship_class,
            );
        }        
        else if ($device_type_id == 21) {

            $userPhone = $this->enc->decode(trim($this->input->post("userPhone")));

            $cctvpath = $this->input->post("cctvpath[]");
            $vehicle_class_multiple_value = $this->input->post('vehicle_class_multiple_value'); 
            $vehicle_class = $this->enc->decode($this->input->post('vehicle_class')); 
            $isMultiple = $this->input->post('isMultiple'); 
            $isSpesific = $this->input->post('isSpesific'); 

            $ip = trim($this->input->post('ip')); 

            $overpaid = $this->input->post('overpaid'); 
            $sensor = $this->input->post('sensor'); 

            $overpaid = $overpaid==1?"true":"false";
            $sensor= $sensor==1?"true":"false";            

            $_POST['vehicle_class']=$vehicle_class;      
            $_POST['userPhone']=$userPhone;

            $userPhoneselfService = ""; 
            $passwordPhoneSelfservice = ""; 
            $extPhoneSelfservice= "";          

            $checkAllComandCenter  = 0;

            $getDetailDevice = $this->device->select_data("app.t_mtr_device_terminal", "where device_terminal_id=".$device_terminal_id)->row();                  
            $userPhoneselfServiceId = $userPhone;
            if(!empty($userPhoneselfServiceId))
            {          
                // check apakah sudah di pakai 
                $checkUserPhone = $this->device->select_data("app.t_mtr_phone_extension", "where  id =".$userPhoneselfServiceId)->row();
                if(count($checkUserPhone) > 0)
                {
                    //  keadaan jika tidak ada perubahan pada username extensionya
                    if($getDetailDevice->username_phone == $checkUserPhone->username_phone && $getDetailDevice->password_phone == $checkUserPhone->password_phone &&
                    $getDetailDevice->extension_phone == $checkUserPhone->extension_phone)
                    {
                        $idt = 1;
                        $checkAllComandCenter  = 1;
                        $userPhoneselfService = $checkUserPhone->username_phone; 
                        $passwordPhoneSelfservice = $checkUserPhone->password_phone; 
                        $extPhoneSelfservice= $checkUserPhone->extension_phone;                           
                    }
                    else if($checkUserPhone->is_used == 0)
                    {
                        $idt = 2;
                        $checkAllComandCenter  = 1;
                        $userPhoneselfService = $checkUserPhone->username_phone; 
                        $passwordPhoneSelfservice = $checkUserPhone->password_phone; 
                        $extPhoneSelfservice= $checkUserPhone->extension_phone;                             
                    }
                    else
                    {
                        $checkAllComandCenter  = 0;
                    }

                }
            }                                      
            
            $dataCctvPath = [];
            if(!empty($cctvpath))
            {
                foreach ($cctvpath as $keyCctvpath => $valueCctvpath) {
                    if(!empty($valueCctvpath))
                    {
                        $dataCctvPath[]=$valueCctvpath;                        
                    }
                    $this->form_validation->set_rules('cctvpath['.$keyCctvpath.']', 'CCTV PATH', 'required|callback_special_char_url');                   
                }
            }
            
            $this->form_validation->set_rules('ship_class', 'kelas kapal', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            $this->form_validation->set_rules('crossClass','Antar Pelabuhan', 'required');

            $this->form_validation->set_rules('userPhone','User Phone', 'required');
            $this->form_validation->set_rules('ip', 'ip', 'required|callback_special_char_url');

            if($isSpesific == 't')
            {
                $this->form_validation->set_rules('vehicle_class', ' golongan kendaraan', 'required');
                $dataVehicle[]=$vehicle_class;                

            }
            else
            {
                $this->form_validation->set_rules('vehicle_class_multiple_value', ' golongan kendaraan', 'required');
                $mapVehicleClass = array_map(function($x){return (int)$this->enc->decode($x);},explode(",",$vehicle_class_multiple_value) );
                // print_r($mapVehicleClass ); exit;

                $dataVehicle = array_filter($mapVehicleClass,function($f){ return $f != "";});
            } 
            
            $this->form_validation->set_message('numeric','%s harus angka!');
            $this->form_validation->set_message('numeric_dot','%s harus angka atau titik!');
            $this->form_validation->set_message('required','%s harus diisi!');
            $this->form_validation->set_message('special_char_url','%s Invalid  karakter!');            
            

            if($this->form_validation->run() === false) {
                $errMess = array_map(function($x){ return "<p>".$x."</p>"; }, array_unique($this->form_validation->error_array()));
                echo $res = json_api(0, implode("",$errMess));
                exit;
            }

            if($isSpesific != 't' && count($dataVehicle) <2 )
            {
                echo $res = json_api(0, " Jika multiple , minimal harus 2 data ");
                exit;
            }            

            if($checkAllComandCenter < 1)
            {
                echo $res =  json_api(0,' Username ext, password ext dan ext Sudah terpakai'); 
                exit;
            }            

            $ship_class = $this->enc->decode($this->input->post('ship_class'));

            $data = array(
                'terminal_name'=>$device_name,
                'port_id'=>$port,
                'ship_class'=>$ship_class,
                'cross_class'=>$crossClass,
                'username_phone' => $userPhoneselfService,
                'extension_phone' => $extPhoneSelfservice,
                'password_phone' => $passwordPhoneSelfservice,
                'cctv_path'=>json_encode($dataCctvPath),
                'is_specific'=>$isSpesific=='t'?"true":"false",
                'vehicle_class_id'=>json_encode($dataVehicle),
                'enable_overpaid_underpaid'=>$overpaid,
                'enable_sensor'=>$sensor,
                'ip_device'=>$ip,                
            );

        }   

        else {
            $this->form_validation->set_rules('device_type_terminal', 'device type', 'required|numeric');
            $this->form_validation->set_message('required','%s harus diisi!');

            if($this->form_validation->run() === false) {
                echo $res = json_api(0, validation_errors());
                exit;
            }

            $data = array(
                // 'terminal_code' => $code,
                'terminal_name' => $device_name,
                'terminal_type' => $device_type_id,
                'port_id' => $port,
            );
        }

        $check_name = $this->device->select_data($this->_table," where upper(terminal_name)=upper('".$device_name."') and device_terminal_id !=$device_terminal_id and status !='-5' " );

        if($check_name->num_rows() > 0) {
            echo $res = json_api(0, "Nama sudah ada");
        }
        else {
            $this->db->trans_begin();
            $data['updated_on'] = date('Y-m-d H:i:s');
            $data['updated_by'] = $this->session->userdata('username');
            $this->device->update_data($this->_table,$data,"device_terminal_id=$device_terminal_id");

            if($device_type_id  == 21 && $idt == 2)
            {
                // is used 1 user , 2 perangkat/ device
                $upadatedata = array("is_used"=> 2,
                "used_by"=>$getDetailDevice->terminal_code ,
                "updated_by"=> $this->session->userdata("username"),
                "updated_on"=>date("Y-m-d H:i:s")                  
                );

            // update untuk mengembalikan data , unpairing data
            $upadatedata2 = array("is_used"=> 0,
                                                "used_by"=>"",
                                                "updated_by"=> $this->session->userdata("username"),
                                                "updated_on"=>date("Y-m-d H:i:s")                  
                                            );            

                $this->device->update_data("app.t_mtr_phone_extension", $upadatedata, " id=".$userPhoneselfServiceId);   

                $this->device->update_data("app.t_mtr_phone_extension", $upadatedata2, " username_phone='".$getDetailDevice->username_phone."' and port_id=".$data['port_id']." and used_by='".$getDetailDevice->terminal_code."' and status=1 ");                   
            }            

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res = json_api(0,validation_errors());
            }
            else {
                $this->db->trans_commit();
                echo $res = json_api(1, 'Berhasil edit data');
            }   
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'device_management/device_type/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit_pass($id) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        
        $idDecode = $this->enc->decode($id);

        $data['title']  = 'Reset Password';
        $data['detail'] = $this->device->select_data($this->_table," where device_terminal_id='{$idDecode}' ")->row();

        $this->load->view($this->_module.'/edit_pass',$data);
    }   

    function numeric_dot ($num) {
        return ( ! preg_match("/^([0-9.\s])+$/D", $num)) ? FALSE : TRUE;
    }    

    public function action_edit_password() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        $this->form_validation->set_rules('device_terminal_id', 'id', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid id'));
        $this->form_validation->set_rules('old_pass'," Password Lama","required" );
        $this->form_validation->set_rules('new_pass'," Password Baru ","required" );
        $this->form_validation->set_rules('repeat_pass'," Ulangi Password Baru ","required" );
        $this->form_validation->set_message('required','%s harus diisi!');
        
        if($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
            exit;
        }



        $device_terminal_id = $this->enc->decode($this->input->post('device_terminal_id'));
        $old_pass           = trim($this->input->post('old_pass'));
        $new_pass           = trim($this->input->post('new_pass'));
        $repeat_pass        = trim($this->input->post('repeat_pass'));


        //ambil data device
        $data_device = $this->device->select_data($this->_table,"where device_terminal_id='".$device_terminal_id."' ")->row();

        $data = array(
            'password'      => $this->bcrypt->hash_password(strtoupper(md5($new_pass))),
            'updated_by'    => $this->session->userdata('username'),
            'updated_on'    => date("Y-m-d H:i:s"),
        );        

        if($this->form_validation->run() === false) {
            echo $res = json_api(0, 'Data masih ada yang kosong');
        }
        else if(!$this->bcrypt->check_password(strtoupper(md5($old_pass)), $data_device->password)) { // pengecekan password
           echo $res = json_api(0, 'Password lama yang anda masukan salah');
        }        
        else if($new_pass != $repeat_pass) {
            echo $res = json_api(0, 'Password Tidak sama');   
        }
        else {
            $this->device->update_data($this->_table,$data," device_terminal_id=".$device_terminal_id);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res = json_api(0,validation_errors());
            }
            else {
                $this->db->trans_commit();
                echo $res = json_api(1, 'Berhasil ubah password');
            }                 
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'device_management/device_type/action_edit_pass';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }     

    // public function action_edit()
    // {
    //     validate_ajax();
    //     $this->global_model->checkAccessMenuAction($this->_module,'edit');


    //     $device_terminal_id=$this->enc->decode($this->input->post('device_terminal_id'));

    //     $port_id=$this->enc->decode($this->input->post('port'));
    //     $dock_id=$this->enc->decode($this->input->post('dock'));
    //     $ship_class_id=$this->enc->decode($this->input->post('ship_class'));
    //     $device_type_terminal_id=$this->enc->decode($this->input->post('device_type_terminal'));
    //     $device_name=trim($this->input->post('device_name'));
    //     $imei=trim($this->input->post('imei'));
    //     $device_code=trim($this->input->post('device_code'));


    //     $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
    //     $this->form_validation->set_rules('dock', 'Dermaga', 'required');
    //     $this->form_validation->set_rules('ship_class', 'Kelas kapal', 'required');
    //     $this->form_validation->set_rules('device_type_terminal', 'Tipe Perangkat', 'required');
    //     $this->form_validation->set_rules('device_name', 'Nama Perangkat', 'required');
    //     $this->form_validation->set_rules('imei', 'Imei', 'required');
    //     $this->form_validation->set_rules('device_code', 'Kode Perangkat', 'required');
                
    //     $data=array(
    //                 'port_id'=>$port_id,
    //                 'dock_id'=>$dock_id,
    //                 'ship_class'=>$ship_class_id,
    //                 'terminal_type'=>$device_type_terminal_id,
    //                 'terminal_name'=>$device_name,
    //                 'imei'=>$imei,
    //                 'terminal_code'=>$device_code,
    //                 'updated_on'=>date("Y-m-d H:i:s"),
    //                 'updated_by'=>$this->session->userdata('username'),
    //                 );

    //     if($this->form_validation->run()===false)
    //     {
    //         echo $res=json_api(0, 'Data masih ada yang kosong');
    //     }
    //     else
    //     {
    //         $this->db->trans_begin();
    //         $this->device->update_data($this->_table,$data,"device_terminal_id=$device_terminal_id");

    //         if ($this->db->trans_status() === FALSE)
    //         {
    //             $this->db->trans_rollback();
    //             echo $res=json_api(0, 'Gagal edit data ');
    //         }
    //         else
    //         {
    //             $this->db->trans_commit();
    //             echo $res=json_api(1, 'Berhasil edit data');
    //         }   
    //     }

    //     /* Fungsi Create Log */
    //     $createdBy   = $this->session->userdata('username');
    //     $logUrl      = site_url().'device_management/device_type/action_edit';
    //     $logMethod   = 'EDIT';
    //     $logParam    = json_encode($data);
    //     $logResponse = $res;

    //     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    // }

    public function action_delete($id) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data = array(
            'status'        => -5,
            'updated_on'    => date("Y-m-d H:i:s"),
            'updated_by'    => $this->session->userdata('username'),
        );

        $team_id = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->schedule->update_data($this->_table,$data,"id=$team_id");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $res = json_api(0, 'Gagal delete data');
        }
        else {
            $this->db->trans_commit();
            echo $res = json_api(1, 'Berhasil delete data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param) {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /* data */
        $data = array(
            'status'        => $d[1],
            'updated_on'    => date("Y-m-d H:i:s"),
            'updated_by'    => $this->session->userdata('username'),
        );

        $this->db->trans_begin();
        $this->team_model->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $res = json_api(0, 'Gagal non aktif');
        }
        else {
            $this->db->trans_commit();
            echo $res = json_api(1, 'Berhasil non aktif data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    private function create_code() {
        $data = $this->db->query("SELECT 
                    SUBSTRING(EXTRACT(YEAR FROM now())::varchar, 3,2)||
                     to_char(EXTRACT(DAY FROM now()), 'fm000')|| 
                    (to_char(nextval('core.t_mtr_team_code_seq'), 'fm0000')) as code ")->row();
        return $data->code;
    }

    public function enable($param) {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /* data */
        $data = array(
            'status'        => $d[1],
            'updated_on'    => date("Y-m-d H:i:s"),
            'updated_by'    => $this->session->userdata('username'),
        );

        $this->db->trans_begin();
        $this->device->update_data($this->_table,$data,"device_terminal_id=".$d[0]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $res = json_api(0, 'Gagal aktifkan data');
        }
        else {
            $this->db->trans_commit();
            echo $res = json_api(1, 'Berhasil aktifkan data');
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/enable';
        $logMethod   = 'Enable';
        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function disable($param) {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /* data */
        $data = array(
            'status'        => $d[1],
            'updated_on'    => date("Y-m-d H:i:s"),
            'updated_by'    => $this->session->userdata('username'),
        );

        $this->db->trans_begin();
        $this->device->update_data($this->_table,$data,"device_terminal_id=".$d[0]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $res = json_api(0, 'Gagal dinonaktifkan data');
        }
        else {
            $this->db->trans_commit();
            echo $res = json_api(1, 'Data berhasil dinonaktifkan ');
        } 

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/enable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function get_dock() {
        $port_id = $this->enc->decode($this->input->post('port'));
        empty($port_id) ? $id = 'NULL' : $id = $port_id;

        $row = $this->device->select_data("app.t_mtr_dock","where status=1 and port_id=".$id." order by name asc")->result();
        $data = array();
        foreach ($row as $key => $value) {
            $value->id      = $this->enc->encode($value->id);
            $value->name    = strtoupper($value->name);
            $data[]         = $value;
        }

        $data_response = array(
            "data" => $data,
            "tokenHash" => $this->security->get_csrf_hash(),
            "csrfName" => $this->security->get_csrf_token_name()
        );
        echo json_encode($data_response);
    }

    function getPosVehicle()
    {
        $port=$this->enc->decode($this->input->post("port"));
        $getData=$this->device->getPosVehicle($port);

        $data=array();
        foreach ($getData as $key => $value) {
            $value->terminal_code=$this->enc->encode($value->terminal_code);
            $data[]=$value;
        }

         $data_response = array(
            "data" => $data,
            "tokenHash" => $this->security->get_csrf_hash(),
            "csrfName" => $this->security->get_csrf_token_name()
        );
        echo json_encode($data_response);
    }

    // function get_service() {
    //     $device_type_terminal_id = $this->enc->decode($this->input->post('device_type_terminal_id'));
    //     empty($device_type_terminal_id) ? $id = 'NULL' : $id = $device_type_terminal_id;

    //     $data = $this->device->select_data("app.t_mtr_device_terminal_type","where terminal_type_id=".$id."")->row();

    //     if(!empty($data)) {
    //         $data->service_id == 1 ? $tipe = "REGULER" : $tipe = "EKSEKUTIF";
    //         echo json_encode($tipe);
    //     }
    //     else {
    //         echo json_encode("");
    //     }        
    // }

    private function createCode($port_code, $device_type_id) {
        if(strlen($device_type_id) > 1) {
            $front_code = $device_type_id."".$port_code;
        }
        else {
            $front_code = "0".$device_type_id."".$port_code;
        }

        $total      = strlen($front_code);
        
        $chekCode   = $this->db->query("select * from app.t_mtr_device_terminal where left(terminal_code,".$total.")='".$front_code."' ")->num_rows();

        if($chekCode < 1) {
            $shelterCode = $front_code."001";
            return $shelterCode;
        }
        else {
            $max    = $this->db->query("select max (terminal_code) as max_code from app.t_mtr_device_terminal where left(terminal_code,".$total.")='".$front_code."' ")->row();
            $kode   = $max->max_code;
            $noUrut = (int) substr($kode, strlen($front_code), 3);
            $noUrut++;
            $char   = $front_code;
            $kode   = $char . sprintf("%03s", $noUrut);
            return $kode;
        }
    }
    function getextension()
    {
        $portId = $this->input->post('portId');
        $usernameExt = $this->input->post('usernameExt');

        $portDecode = $this->enc->decode($portId);
        if( $portDecode != "")
        {
            $dataReturn = $this->device->getDataExt($portDecode."|".$usernameExt);
            $data = $dataReturn['data'];
            $selectData = $dataReturn['selectData'];
        }
        else
        {
            $data = [];
            $selectData ="";
        }
        
        $return = array("data" => $data,
                                    "selectData"=> $selectData,
                                    "tokenHash" => $this->security->get_csrf_hash(),
                                    "csrfName" => $this->security->get_csrf_token_name(),                                    
                                );
        echo json_encode($return);
    }        
}
