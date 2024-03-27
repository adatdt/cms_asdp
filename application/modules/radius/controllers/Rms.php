<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Rms extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('rms_model','rms');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_rms';
        $this->_table_rms_detail_channel    = 'app.t_mtr_rms_channel';
        $this->_table_rms_detail_class    = 'app.t_mtr_rms_detail_class';
        $this->_table_rms_exp_web  = "app.t_mtr_exception_rms";
        $this->_table_rms_exp_ifcs  = "app.t_mtr_exception_rms_ifcs";

        $this->_username = $this->session->userdata('username');
        $this->_module = 'radius/rms';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            // print_r($this->input->post('searchData'));exit;
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('dateFrom')){
                $this->form_validation->set_rules('dateFrom', 'Tanggal awal', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal awal'));
            }
            if($this->input->post('reservation_date')){
            $this->form_validation->set_rules('reservation_date', 'Tanggal Aktif', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal akhir'));
            }
            if($this->input->post('dateTo')){
            $this->form_validation->set_rules('dateTo', 'Tanggal akhir', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal akhir'));
            }
            if($this->input->post('searchData')){
            $this->form_validation->set_rules('searchData', 'Search', 'trim|callback_special_char', array('special_char' => 'Search has contain invalid characters'));
            }
            if ($this->form_validation->run() == FALSE) 
            {
              echo $res = json_api(0, validation_errors(),[]);
              exit;
            }
            $rows = $this->rms->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'port' => $this->rms->getDropdown2("app.t_mtr_port","id","name"),
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master RMS',
            'content'  => 'rms/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function detailUserWebExp()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->rms->detailUserWebExp();
            echo json_encode($rows);
            exit;
        }   
    }

    public function detailUserWebLimit()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->rms->detailUserWebLimit();
            echo json_encode($rows);
            exit;
        }   
    }

    public function detailUserIfcsExp()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->rms->detailUserIfcsExp();
            echo json_encode($rows);
            exit;
        }   
    }    

    public function detailUserIfcsLimit()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->rms->detailUserIfcsLimit();
            echo json_encode($rows);
            exit;
        }   
    }   

    public function detailGolongan()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->rms->detailGolongan();
            echo json_encode($rows);
            exit;
        }   
    }        

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Radius';
        $data['port'] = $this->rms->getDropdown("app.t_mtr_port","id","name");
        $data['service'] = $this->rms->select_data("app.t_mtr_service"," where status =1 order by id asc")->result();
        $data['vehicleClass'] = $this->rms->getDropdown("app.t_mtr_vehicle_class","id","name");

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // $data = $this->input->post();
        $port = $this->enc->decode($this->input->post("port", true));
        $activeDate = trim($this->input->post("activeDate", true));
        $startDate = trim($this->input->post("startDate", true));
        $endDate = trim($this->input->post("endDate", true));
        $longitude = trim($this->input->post("longitude", true));
        $latitude = trim($this->input->post("latitude", true));
        $radius = trim($this->input->post("radius", true));
        $radiusType = trim($this->enc->decode($this->input->post("radiusType", true)));
        $servicePnp = trim($this->input->post("servicePnp", true));
        $serviceKnd = trim($this->input->post("serviceKnd", true));
        $vehicleClass = $this->input->post("vehicleClass[]", true);
        $channel = $this->input->post("channel[]", true);
        // $webExp = $this->input->post("webExp[]", true);
        // $ifcsExp = $this->input->post("ifcsExp[]", true);
        $idDataIfcs=$this->input->post('idData[]', true);
        $idDataWeb=$this->input->post('idDataWeb[]', true);
        $emailIfcs=$this->input->post('idMemberExcept[]', true);
        $emailWeb=$this->input->post('idMemberExceptWeb[]', true);

        $webExp=$this->createEmailWeb($idDataWeb,$emailWeb);
        $ifcsExp=$this->createEmailIfcs($idDataWeb,$emailIfcs);

        // print_r($ifcsExp);exit;

        $_POST["port"] = $port;
        $_POST["radiusType"] = $radiusType;

        $rmsCode = $this->createCode($port);

        $dataVehicle = [];
        $checkVehicledEmpty[] = 0;
        $checkVehicledEmptyId[] = 0;
        if($serviceKnd == "t" )
        {   if(!empty($vehicleClass))
            { 
                foreach ($vehicleClass as $key => $value) {
                    $decodeVehicle = $this->enc->decode($value);
                    if(empty($decodeVehicle))
                    {
                        $checkVehicleEmptyId[] = 1;
                    }
                    $dataVehicle [] = array(
                        "rms_code"=>$rmsCode,
                        "class_id"=>$decodeVehicle,
                        "status"=>1,
                        "created_on"=>date("Y-m-d H:i:s"),
                        "created_by"=>$this->session->userdata("username"),
                    );
                }
            }
            else
            {
                $checkVehicledEmpty[] = 1;
            }
        }    

        $dataChannel = [];
        $channelGroupWeb =0 ; // ini termasuk mobile dan web
        $channelGroupIfcs =0 ;
        $checkChannelEmpty[] = 0;
        if(!empty($channel))
        {            
            foreach ($channel as $keychannel => $valuechannel) {
                $decodeChannel = $this->enc->decode($valuechannel);
                if(empty($decodeChannel))
                {
                    $checkChannelEmpty[] = 1;
                }

                // ini untuk validasi jika sudah input email exeption tapi kemudian channelnya di uncheck kembali 
                if($decodeChannel == "web" || $decodeChannel == "mobile"  )
                {
                    $channelGroupWeb = 1;
                }
                else
                {
                    $channelGroupIfcs =1;
                }
                $dataChannel [] = array(
                    "rms_code"=>$rmsCode,
                    "channel"=>$decodeChannel,
                    "status"=>1,
                    "created_on"=>date("Y-m-d H:i:s"),
                    "created_by"=>$this->session->userdata("username"),
                );
            }
        } 

        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('activeDate', 'Tanggal Aktif', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('startDate', 'Tanggal Mulai', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('longitude', 'Longitude', 'required|callback_longLatFormat');
        $this->form_validation->set_rules('latitude', 'Latitude', 'required|callback_longLatFormat');
        $this->form_validation->set_rules('radius', 'Radius', 'required|numeric');
        $this->form_validation->set_rules('radiusType', 'Tipe Radius', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('is_natural','%s harus angka!');
        $this->form_validation->set_message('is_natural_no_zero','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');
        $this->form_validation->set_message('letter_number_val','%s Tidak Boleh ada Karakter Khusus!'); 
        $this->form_validation->set_message('date_time_minutes','%s Format tidak sesuai! ');
        $this->form_validation->set_message('longLatFormat','%s Format tidak sesuai! ');

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
            exit;
        }        

        $dataHeader = array(
                                        "rms_code"=>$rmsCode,
                                        "type"=>1, // typw 1 channel mobile dan web dan ifcs, type 2 b2b channel ifcs
                                        "port_id"=>$port,
                                        "reservation_date"=>$activeDate,
                                        "start_date"=>$startDate,
                                        "end_date"=>$endDate,
                                        "latitude"=>$latitude,
                                        "longitude"=>$longitude,
                                        "radius"=>$radius,
                                        "radius_type"=>$radiusType,
                                        "is_pedestrian"=>$servicePnp != "t" ?false:true,
                                        "is_vehicle"=>$serviceKnd != "t" ?false:true,
                                        "status"=>1,
                                        "created_on"=>date("Y-m-d H:i:s"),
                                        "created_by"=>$this->session->userdata("username"),
                                    );



        $dataWebExp = array();
        $dataIfcsExp = array();
        // data exept di validasi pada saat serach user, disini di filtering jika idnya setelah di decode kosong maka tidak akan di save
        if(!empty($webExp) && $channelGroupWeb==1)
        {            
            $dataWebExp = $this->checkUserExept($webExp, $rmsCode);
        }

        if(!empty($ifcsExp && $channelGroupIfcs==1 ))
        {            
            $dataIfcsExp = $this->checkUserExept($ifcsExp, $rmsCode);
        }        
        // print_r($dataWebExp); exit;
        // print_r($dataIfcsExp); exit;

        $data =  array($this->_table => $dataHeader,
                                $this->_table_rms_detail_channel => $dataChannel,
                                $this->_table_rms_detail_class => $dataVehicle,
                                $this->_table_rms_exp_web => $dataWebExp,
                                $this->_table_rms_exp_ifcs => $dataIfcsExp,
                                    );        
        
        $checkOverlaps= $this->rms->checkOverlaps($startDate,$endDate,$port,"");

        if(empty($channel))
        {
            echo $res=json_api(0,"Channel harus di checklist minimal 1 pilihan");
        }
        else if(array_sum($checkChannelEmpty)>0)
        {
            echo $res=json_api(0,"Id channel tidak sesuai");
        }
        // else if($check->num_rows()>0)
        // {
        //     echo $res=json_api(0,"ABBR sudah ada.");
        // }
        else if($serviceKnd != 't' && $servicePnp != 't' )
        {
            echo $res=json_api(0,"Layanan harus di checklist minimal 1 pilihan");
        }
        else if(array_sum($checkVehicledEmpty)>0)
        {
            echo $res=json_api(0,"Golongan Kendaraan harus di checklist minimal 1 pilihan");
        }
        else if(array_sum($checkVehicledEmptyId)>0)
        {
            echo $res=json_api(0,"Id golongan tidak sesuai");
        }
        else if($checkOverlaps->num_rows()>0)
        {
            echo $res=json_api(0," Waktu tidak boleh bersinggungan dalam satu pelabuhan yang sama");
        }         
        else
        {
            // print_r($data); exit;
            /*
                $this->_table    = 'app.t_mtr_rms';
                $this->_table_rms_detail_channel    = 'app.t_mtr_rms_channel';
                $this->_table_rms_detail_class    = 'app.t_mtr_rms_detail_class';
                $this->_table_rms_exp_web  = "app.t_mtr_exception_rms";
                $this->_table_rms_exp_ifcs  = "app.t_mtr_exception_rms_ifcs";    
             */
            
            $this->db->trans_begin();

            $this->rms->insert_data($this->_table,$dataHeader);
            $this->rms->insert_data_batch($this->_table_rms_detail_channel,$dataChannel);
            if(!empty($dataVehicle))
            {
                $this->rms->insert_data_batch($this->_table_rms_detail_class,$dataVehicle);
            }
            if(!empty($dataWebExp) && $channelGroupWeb==1)
            {
                $this->rms->insert_data_batch($this->_table_rms_exp_web ,$dataWebExp);
            }    
            if(!empty($dataIfcsExp) && $channelGroupIfcs==1)
            {
                $this->rms->insert_data_batch($this->_table_rms_exp_ifcs ,$dataIfcsExp);
            }

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'radius/rms/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_add_user_web_exp()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        // print_r($this->input->post()); exit;
        
        $rmsCode = $this->enc->decode($this->input->post("rmsCode", true));
        $tagEmail = trim($this->input->post("tagEmail", true));
        $type = trim($this->input->post("type", true));
        $_POST['rmsCode'] = $rmsCode;

        // echo $rmsCode; exit;

        $tagEmailArr = array_map(function($x){ return trim($x); },explode(",",$tagEmail));
        $tagEmailStr =  array_map(function($x){
            return "'".$this->db->escape_str(trim($x))."'";
        },$tagEmailArr);


        $this->form_validation->set_rules('tagEmail', 'Email', 'required');
        $this->form_validation->set_rules('rmsCode', 'Kode Rms', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('is_natural','%s harus angka!');
        $this->form_validation->set_message('is_natural_no_zero','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');
        $this->form_validation->set_message('letter_number_val','%s Tidak Boleh ada Karakter Khusus!'); 
        $this->form_validation->set_message('date_time_minutes','%s Format tidak sesuai! ');
        $this->form_validation->set_message('longLatFormat','%s Format tidak sesuai! ');

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
            exit;
        }  
        
        $tableExept = $type=="ifcs"?"app.t_mtr_exception_rms_ifcs":"app.t_mtr_exception_rms";
        $tableMember = $type=="ifcs"?"app.t_mtr_member_ifcs":"app.t_mtr_member";
        
        $qryUser =$this->rms->select_data($tableExept, " where account_id in (".implode(", ", $tagEmailStr).") and rms_code='$rmsCode' and status=1");

        $checkUser =$this->rms->select_data($tableMember, " where email in (".implode(", ", $tagEmailStr).")  and status=1")->result();
        $arrDiff2 = array_diff($tagEmailArr, array_column($checkUser ,"email"));

        $data = array_map(function($email) use ($rmsCode){
            return array(
                "account_id"=>trim($email),
                "rms_code"=>$rmsCode,
                "status"=>1,
                "created_by"=>$this->session->userdata("username"),
                "created_on"=>date("Y-m-d H:i:s"),
            );
        },$tagEmailArr);
        

        if($qryUser->num_rows() > 0)
        {
            $implodeMessage = implode("<br>", array_column($qryUser->result(),"account_id"));
            $data = array(
                "message"=>$implodeMessage." <br> Email sudah di kecualikan",                
            );
            echo  $res= json_api(0, 'Email belum Terdaftar', $data);            
        }
        else if(!empty($arrDiff2))
        {
            $implodeMessage2 = implode("<br>", $arrDiff2);
            $data = array(
                "message"=>$implodeMessage2." <br> Email belum terdaftar",                
            );
            echo  $res= json_api(0, 'Email belum Terdaftar', $data);          

        }
        else
        {
            $this->db->trans_begin();
            $this->rms->insert_data_batch($tableExept, $data);
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');
            }
        }      
        
         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'radius/rms/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    

    function longLatFormat($num)
    {
        return ( ! preg_match("/[0-9,-.]+/", $num)) ? FALSE : TRUE;
    }

    public function checkUserExept($userExept,$code)
    {
        $data = array_filter(array_map(function($x) use ($code) { 
            $dataMap = array(
                "account_id"=>trim($x),
                "rms_code" => $code,
                "status" => 1,
                "created_on"=>date("Y-m-d H:i:s"),
                "created_by"=>$this->session->userdata("username")
            );
            return $dataMap; 
        },$userExept),function($f){
            if($f['account_id'] !="")
            {
                return $f;
            }
        });

        return $data;

    }

    public function checkUserExeptEdit($userExept,$code)
    {
        $data = array_filter(array_map(function($x) use ($code) { 
            $dataMap = array(
                "account_id"=>trim($x),
                "rms_code" => $code,
                "status"=>'1',
                "updated_on"=>date("Y-m-d H:i:s"),
                "updated_by"=>$this->session->userdata("username")
            );
            return $dataMap; 
        },$userExept),function($f){
            if($f['account_id'] !="")
            {
                return $f;
            }
        });

        return $data;

    }

    public function  getSelectedVehicle ($detailVehicle, $vehicleClass)
    {
        $map = array_filter(array_map(function($v, $k) use ($detailVehicle){
                            foreach ($detailVehicle as $key => $value) {
                                if($this->enc->decode($k) == $value->class_id)
                                {
                                    return $k;
                                }                
                            }
                        },$vehicleClass,array_keys($vehicleClass) ),
                        function($x){
                            return $x !="";
                        }
                    );
        return array_values($map);
    }
    public function edit($code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        $codeDecode=$this->enc->decode($code);
        // echo $codeDecode; exit; 
        $detailHeader = $this->rms->getRmsHeader($codeDecode);
        $detailEmailWeb = $this->rms->getEmailweb($codeDecode);
        // print_r($detailEmailWeb);exit;
        $detailVehicle = $this->rms->getRmsVehicleDetail($codeDecode);
        $vehicleClass = $this->rms->getDropdown("app.t_mtr_vehicle_class","id","name");
        $selectedVehicle = $this->getSelectedVehicle($detailVehicle, $vehicleClass);
        $rmsChannel = $this->rms->getRmsChannel($codeDecode);        

        $data['title'] = 'Edit RMS';
        $data['code'] = $code;
        $data['port'] = $this->rms->getDropdown2("app.t_mtr_port","id","name",$detailHeader->port_id);
        $data['service'] = $this->rms->select_data("app.t_mtr_service"," where status =1 order by id asc")->result();
        $data['vehicleClass'] = $vehicleClass;
        $data['detailEmailWeb'] = $detailEmailWeb;
        $data['detailHeader'] = $detailHeader;
        $data['selectedVehicle'] = $selectedVehicle;
        $data['channel'] = array_fill_keys(array_column($rmsChannel,"channel"), 'checked');

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        
        $rmsCode = $this->enc->decode($this->input->post("rmsCode", true));
        $port = $this->enc->decode($this->input->post("port", true));
        $activeDate = trim($this->input->post("activeDate", true));
        $startDate = trim($this->input->post("startDate", true));
        $endDate = trim($this->input->post("endDate", true));
        $longitude = trim($this->input->post("longitude", true));
        $latitude = trim($this->input->post("latitude", true));
        $radius = trim($this->input->post("radius", true));
        $radiusType = trim($this->enc->decode($this->input->post("radiusType", true)));
        $servicePnp = trim($this->input->post("servicePnp", true));
        $serviceKnd = trim($this->input->post("serviceKnd", true));
        $vehicleClass = $this->input->post("vehicleClass[]", true);
        $channel = $this->input->post("channel[]", true);

        $_POST["port"] = $port;
        $_POST["radiusType"] = $radiusType;
        $_POST["rmsCode"] = $rmsCode;

        $dataVehicle = [];
        $checkVehicledEmpty[] = 0;
        $checkVehicledEmptyId[] = 0;
        if($serviceKnd == "t" )
        {   if(!empty($vehicleClass))
            { 
                foreach ($vehicleClass as $key => $value) {
                    $decodeVehicle = $this->enc->decode($value);
                    if(empty($decodeVehicle))
                    {
                        $checkVehicleEmptyId[] = 1;
                    }
                    $dataVehicle [] = array(
                        "rms_code"=>$rmsCode,
                        "class_id"=>$decodeVehicle,
                        "status"=>1,
                        "created_on"=>date("Y-m-d H:i:s"),
                        "created_by"=>$this->session->userdata("username"),
                    );
                }
            }
            else
            {
                $checkVehicledEmpty[] = 1;
            }
        }    

        $dataChannel = [];
        $channelGroupWeb =0 ; // ini termasuk mobile dan web
        $channelGroupIfcs =0 ;
        $checkChannelEmpty[] = 0;
        if(!empty($channel))
        {            
            foreach ($channel as $keychannel => $valuechannel) {
                $decodeChannel = $this->enc->decode($valuechannel);
                if(empty($decodeChannel))
                {
                    $checkChannelEmpty[] = 1;
                }

                // ini untuk validasi jika sudah input email exeption tapi kemudian channelnya di uncheck kembali 
                if($decodeChannel == "web" || $decodeChannel == "mobile"  )
                {
                    $channelGroupWeb = 1;
                }
                else
                {
                    $channelGroupIfcs =1;
                }
                $dataChannel [] = array(
                    "rms_code"=>$rmsCode,
                    "channel"=>$decodeChannel,
                    "status"=>1,
                    "created_on"=>date("Y-m-d H:i:s"),
                    "created_by"=>$this->session->userdata("username"),
                );
            }
        }     
        // print_r($checkChannelEmpt); exit;
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('activeDate', 'Tanggal Aktif', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('startDate', 'Tanggal Mulai', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('longitude', 'Longitude', 'required|callback_longLatFormat');
        $this->form_validation->set_rules('latitude', 'Latitude', 'required|callback_longLatFormat');
        $this->form_validation->set_rules('radius', 'Radius', 'required|numeric');
        $this->form_validation->set_rules('radiusType', 'Tipe Radius', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('is_natural','%s harus angka!');
        $this->form_validation->set_message('is_natural_no_zero','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');
        $this->form_validation->set_message('letter_number_val','%s Tidak Boleh ada Karakter Khusus!'); 
        $this->form_validation->set_message('date_time_minutes','%s Format tidak sesuai! ');
        $this->form_validation->set_message('longLatFormat','%s Format tidak sesuai! ');


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
            exit;
        }

        $dataHeader = array(
            "port_id"=>$port,
            "reservation_date"=>$activeDate,
            "start_date"=>$startDate,
            "end_date"=>$endDate,
            "latitude"=>$latitude,
            "longitude"=>$longitude,
            "radius"=>$radius,
            "radius_type"=>$radiusType,
            "is_pedestrian"=>$servicePnp != "t" ?false:true,
            "is_vehicle"=>$serviceKnd != "t" ?false:true,
            "updated_on"=>date("Y-m-d H:i:s"),
            "updated_by"=>$this->session->userdata("username"),
        );

        $data =  array(
                $this->_table => $dataHeader,
                $this->_table_rms_detail_channel => $dataChannel,
                $this->_table_rms_detail_class => $dataVehicle
            );        
        // print_r($dataHeader); exit;


        //-----------------------------------------------------------------

        $checkOverlaps= $this->rms->checkOverlaps($startDate,$endDate,$port,$rmsCode);
        // print_r($checkOverlaps); exit;                       

        if(empty($channel))
        {
            echo $res=json_api(0,"Channel harus di checklist minimal 1 pilihan");
        }
        else if(array_sum($checkChannelEmpty)>0)
        {
            echo $res=json_api(0,"Id channel tidak sesuai");
        }
        // else if($check->num_rows()>0)
        // {
        //     echo $res=json_api(0,"ABBR sudah ada.");
        // }
        else if($serviceKnd != 't' && $servicePnp != 't' )
        {
            echo $res=json_api(0,"Layanan harus di checklist minimal 1 pilihan");
        }        
        else if(array_sum($checkVehicledEmpty)>0)
        {
            echo $res=json_api(0,"Golongan Kendaraan harus di checklist minimal 1 pilihan");
        }
        else if(array_sum($checkVehicledEmptyId)>0)
        {
            echo $res=json_api(0,"Id golongan tidak sesuai");
        }
        else if($checkOverlaps->num_rows()>0)
        {
            echo $res=json_api(0," Waktu tidak boleh bersinggungan dalam satu pelabuhan yang sama");
        }          
        else
        {
            /*
                $this->_table    = 'app.t_mtr_rms';
                $this->_table_rms_detail_channel    = 'app.t_mtr_rms_channel';
                $this->_table_rms_detail_class    = 'app.t_mtr_rms_detail_class';
                $this->_table_rms_exp_web  = "app.t_mtr_exception_rms";
                $this->_table_rms_exp_ifcs  = "app.t_mtr_exception_rms_ifcs";    
             */
            $this->db->trans_begin();

            $softDeleteData=array(
                                                    "status"=>'-5',
                                                    "updated_on"=>date("Y-m-d H:i:s"),
                                                    "updated_by"=>$this->session->userdata("username")
                                                );


            $this->rms->update_data($this->_table,$dataHeader," rms_code='".$rmsCode."'  ");
            $this->rms->update_data($this->_table_rms_detail_channel,$softDeleteData, " rms_code='".$rmsCode."' and status =1 ");
            $this->rms->update_data($this->_table_rms_detail_class,$softDeleteData, " rms_code='".$rmsCode."' and status =1 ");

            // jika channelnya tidak dipilih maka semua data exeption di soft delete
            if($channelGroupWeb < 1)
            {
                $this->rms->update_data($this->_table_rms_exp_web,$softDeleteData, " rms_code='".$rmsCode."' and status =1 ");
            }

            // jika channelnya tidak dipilih maka semua data exeption di soft delete
            if($channelGroupIfcs < 1)
            {
                $this->rms->update_data($this->_table_rms_exp_ifcs,$softDeleteData, " rms_code='".$rmsCode."' and status =1 ");
            }            


            $this->rms->insert_data_batch($this->_table_rms_detail_channel,$dataChannel);

            if(!empty($dataVehicle))
            {
                $this->rms->insert_data_batch($this->_table_rms_detail_class,$dataVehicle);
            }      

            if ($this->db->trans_status() === FALSE)
            {   
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data');
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'radius/rms/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );

        if($d[1]==1)
        {
            $getDataById = $this->rms->select_data("app.t_mtr_rms", " where id = '".$d[0]."' ")->row();
            // print_r($getDataById);exit;
            $checkOverlaps = $this->rms->checkOverlaps($getDataById->start_date, $getDataById->end_date,$getDataById->port_id, $getDataById->rms_code);       
            
            if($checkOverlaps->num_rows()>0)
            {
                echo $res=json_api(0," Gagal aktif, Ada data aktif yang bersinggungan dalam waktu tanggal mulai dan akhir dalam satu  pelabuhan ");
                exit;
            }
        }

        $this->db->trans_begin();
        $this->rms->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            if ($d[1]==1)
            {
                echo $res=json_api(0, 'Gagal aktif');
            }
            else
            {
                echo $res=json_api(0, 'Gagal non aktif');
            }
            
        }
        else
        {
            $this->db->trans_commit();
            if ($d[1]==1)
            {
                echo $res=json_api(1, 'Berhasil aktif data');
            }
            else
            {
                echo $res=json_api(1, 'Berhasil non aktif data');
            }
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/bank/action_change';
        $d[1]==1?$logMethod='ENABLE':$logMethod='DISABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($getId)
    {

        validate_ajax();
        $idDecode = $this->enc->decode($getId);
        $explode = explode("|",$idDecode); 
        $table = $explode[1];
        $id = $explode[0];
        $rms_code = @$explode[2];
        

        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $this->db->trans_begin();
        $this->rms->update_data($table,$data," id='".$id."'");

        if ($this->db->trans_status() === FALSE)
        {            
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal membatasi user');
        }
        else
        {
            $this->db->trans_commit();
            $data=array("rms_code"=>$rms_code);
            echo $res=json_api(1, 'Berhasil membatasi user', $data);
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/bank/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function getUser_04122023()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $tagEmail = $this->input->post("tagEmail");    

        if(empty($tagEmail))
        {
            $data = array(
                "massage"=>"email Masih Kosong",                
            );
            echo  json_api(0, 'email Masih Kosong', $data);
            exit;
        }
        $tagEmailArr = explode(",",$tagEmail);
        $tagEmailTrim = array_map(function($x){ return $this->db->escape_str(trim($x));}, $tagEmailArr);

        $tagEmailStr = array_map(function($x){ return "'".$this->db->escape_str(trim($x))."'";}, $tagEmailArr);
        
        //   echo  "where email in (".implode(", ", $tagEmailStr).")  and status=1";exit;
        $qryUser =$this->rms->select_data("app.t_mtr_member", " where email in (".implode(", ", $tagEmailStr).")  and status=1")->result();

        $arrDiff = array_diff($tagEmailTrim, array_column($qryUser,"email"));
        // print_r( $arrDiff);exit;
        if(!empty($arrDiff))
        {
            $implodeMessage = implode("<br>", $arrDiff);
            $data = array(
                "massage"=>$implodeMessage." <br> Email belum Terdaftar",                
            );
            echo  json_api(0, 'Email belum Terdaftar', $data);
        }
        else
        {
            $sendData =[]; 
            foreach ($qryUser as $key => $value) {
                // $value->email = $value->email."|".$this->enc->encode($value->id);
                $value->email = $value->email;
                $sendData[] = $value;
            }            
            echo  json_api(1, 'Berhasil Tambah email', array("email"=>array_column($sendData,"email")));
        }
        
    }

    public function getUserIfcs_04122023()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $tagEmail = $this->input->post("tagEmail");        

        if(empty($tagEmail))
        {
            $data = array(
                "massage"=>"email Masih Kosong",                
            );
            echo  json_api(0, 'email Masih Kosong', $data);
            exit;
        }
        $tagEmailArr = explode(",",$tagEmail);
        $tagEmailTrim = array_map(function($x){ return $this->db->escape_str(trim($x));}, $tagEmailArr);
       
        $tagEmailStr = array_map(function($x){ return "'".$this->db->escape_str(trim($x))."'";}, $tagEmailArr);
        $qryUser =$this->rms->select_data("app.t_mtr_member_ifcs", " where email in (".implode(", ", $tagEmailStr).") and status=1 ")->result();

        $arrDiff = array_diff($tagEmailTrim, array_column($qryUser,"email"));
        if(!empty($arrDiff))
        {
            $implodeMessage = implode("<br>", $arrDiff);
            $data = array(
                "massage"=>$implodeMessage." <br> Email belum Terdaftar",                
            );
            echo  json_api(0, 'Email belum Terdaftar', $data);
        }
        else
        {
            $sendData =[]; 
            foreach ($qryUser as $key => $value) {
                // $value->email = $value->email."|".$this->enc->encode($value->id);
                $value->email = $value->email;
                $sendData[] = $value;
            }                        
            echo  json_api(1, 'Berhasil Tambah email', array("email"=>array_column($sendData,"email")));
        }
        
    }    

    function createCode($portId=0)
    {
        $countPort = strlen($portId);
        $codePort =$portId;
        if($countPort<2)
        {
            $codePort ="0".$portId;
        }

        $getCode ="RM".$codePort.date('ymdhis');
        return $getCode;
    }  
    
    public function getUser()
    {
        if($this->input->is_ajax_request()){
            $rows = $this->rms->getUser();
            echo json_encode($rows);
            exit;
        }
    }

    public function getUserExcept()
    {
        if($this->input->is_ajax_request()){
            $rows = $this->rms->getUserExcept();
            echo json_encode($rows);
            exit;
        }
    } 

    public function getUserIfcs()
    {
        if($this->input->is_ajax_request()){
            $rows = $this->rms->getUserIfcs();
            echo json_encode($rows);
            exit;
        }
    }

    public function getUserExceptIfcs()
    {
        if($this->input->is_ajax_request()){
            $rows = $this->rms->getUserExceptIfcs();
            echo json_encode($rows);
            exit;
        }
    } 

    public function detailGetUser()
    {
        if($this->input->is_ajax_request()){
            $rows = $this->rms->detailGetUser();
            echo json_encode($rows);
            exit;
        }
    }

    public function detailGetUserExcept()
    {
        if($this->input->is_ajax_request()){
            $rows = $this->rms->detailGetUserExcept();
            echo json_encode($rows);
            exit;
        }
    } 

    public function createEmailWeb($idData,$webExp){

        $where = " where status <> '-5'  ";

        if($idData==1)
        {
            if(count((array)$webExp)>0)
            {
                $getwebExp=array();
                foreach ($webExp as $value) {
                    $getwebExp[]="'".$value."'";
                }
                $where .= " and id in  (".implode(",",$getwebExp).") ";
            }
            else
            { $where .= " and id is null "; }
        }
        else
        {
            if(count((array)$webExp)>0)
            {
                $getwebExp=array();
                foreach ($webExp as $value) {
                    $getwebExp[]="'".$value."'";
                }
                $where .= " and id not in (".implode(",",$getwebExp).") ";
            }
        }
        $get=" select email from app.t_mtr_member {$where}";
        $query     = $this->db->query($get);
		$rows_data = $query->result();
		$emailWeb 	= array();
        $i  	= 1;

        foreach ($rows_data as $row) {
            $emailWeb[] = $row->email;
            $i++;
        }

        // print_r($emailWeb);exit;
        return $emailWeb;
    }

    public function createEmailIfcs($idData,$ifcsExp){

        $where = " where status <> '-5'  ";

        if($idData==1)
        {
            if(count((array)$ifcsExp)>0)
            {
                $getIfcsExp=array();
                foreach ($ifcsExp as $value) {
                    $getIfcsExp[]="'".$value."'";
                }
                $where .= " and id in  (".implode(",",$getIfcsExp).") ";
            }
            else
            { $where .= " and id is null "; }
        }
        else
        {
            if(count((array)$ifcsExp)>0)
            {
                $getIfcsExp=array();
                foreach ($ifcsExp as $value) {
                    $getIfcsExp[]="'".$value."'";
                }
                $where .= " and id not in (".implode(",",$getIfcsExp).") ";
            }
        }
        $get=" select email from app.t_mtr_member_ifcs {$where}";
        $query     = $this->db->query($get);
		$rows_data = $query->result();
		$emailIfcs 	= array();
        $i  	= 1;

        foreach ($rows_data as $row) {
            $emailIfcs[] = $row->email;
            $i++;
        }

        // print_r($emailIfcs);exit;
        return $emailIfcs;
    }

    public function actionChangeWebLimit($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /*
        $d[0]= Rms code
        $d[1]= status
        $d[2]= email
        */

        // get heder data
        $data = array(
            "account_id"=>trim($d[2]),
            "rms_code" => $d[0],
            "status" => 1,
            "created_on"=>date("Y-m-d H:i:s"),
            "created_by"=>$this->session->userdata("username")
        );
           

        // print_r($data);exit;

        $this->db->trans_begin();
        $this->rms->insert_data("app.t_mtr_exception_rms",$data);
    
        if ($this->db->trans_status() === FALSE)
        {            
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal mengecualikan user');
        }
        else
        {
            $this->db->trans_commit();
            $data=array("rms_code"=>$d[0]);
            echo $res=json_api(1, 'Berhasil mengecualikan user', $data);
        }  
        
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/rms/actionChangeWebLimit';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    
    }

    public function actionChangeIfcsLimit($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /*
        $d[0]= Rms code
        $d[1]= status
        $d[2]= email
        */

        // get heder data
        $data = array(
            "account_id"=>trim($d[2]),
            "rms_code" => $d[0],
            "status" => 1,
            "created_on"=>date("Y-m-d H:i:s"),
            "created_by"=>$this->session->userdata("username")
        );
           

        // print_r($data);exit;

        $this->db->trans_begin();
        $this->rms->insert_data("app.t_mtr_exception_rms_ifcs",$data);
    
        if ($this->db->trans_status() === FALSE)
        {            
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal mengecualikan user');
        }
        else
        {
            $this->db->trans_commit();
            $data=array("rms_code"=>$d[0]);
            echo $res=json_api(1, 'Berhasil mengecualikan user', $data);
        }  
        
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/rms/actionChangeWebLimit';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    
    }

}
