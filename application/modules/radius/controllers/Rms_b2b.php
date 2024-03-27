<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Rms_b2b extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('rms_b2b_model','rms');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_rms';
        $this->_table_rms_detail_channel    = 'app.t_mtr_rms_channel';
        $this->_table_rms_detail_class    = 'app.t_mtr_rms_detail_class';
        $this->_table_rms_exp_web  = "app.t_mtr_exception_rms";
        $this->_table_rms_exp_ifcs  = "app.t_mtr_exception_rms_ifcs";
        $this->_table_rms_merchant_b2b  = "app.t_mtr_rms_merchant_b2b";
        $this->_table_rms_outlet_b2b  = "app.t_mtr_rms_outlet_b2b";

        $this->_username = $this->session->userdata('username');
        $this->_module = 'radius/rms_b2b';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->rms->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'port' => $this->rms->getDropdown2("app.t_mtr_port","id","name"),
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master RMS B2B',
            'content'  => 'rms_b2b/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            

        );

		$this->load->view('default', $data);
	}

    public function detailOutlet()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->rms->detailOutlet();
            echo json_encode($rows);
            exit;
        }   
    }

    public function detailOutletExcept()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->rms->detailOutletExcept();
            echo json_encode($rows);
            exit;
        }   
    }

    public function detailMerchant()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->rms->detailMerchant();
            echo json_encode($rows);
            exit;
        }   
    }    

    public function getOutletImpact()
    {
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->rms->getOutletImpact();
            echo json_encode($rows);
            exit;
        }
    }    

    public function getOutletExcept()
    {
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->rms->getOutletExcept();
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

        $merchant = $this->rms->getDropdown("app.t_mtr_merchant","merchant_id","merchant_name");

        $data['title'] = 'Tambah Radius B2B';
        $data['port'] = $this->rms->getDropdown("app.t_mtr_port","id","name");
        $data['service'] = $this->rms->select_data("app.t_mtr_service"," where status =1 order by id asc")->result();
        $data['vehicleClass'] = $this->rms->getDropdown("app.t_mtr_vehicle_class","id","name");
        $data['merchant'] = array_diff($merchant,array(""=>"Pilih"));


        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // $data = $this->input->post();
        // print_r($data); exit;
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
        $getMerchant = $this->input->post("merchant", true);
        $getIsOutlet = $this->input->post("isOutlet", true);
        $channel = "b2b";

        $_POST["port"] = $port;
        $_POST["radiusType"] = $radiusType;

        // print_r($this->input->post()); exit;
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

        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('activeDate', 'Tanggal Aktif', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('startDate', 'Tanggal Mulai', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('longitude', 'Longitude', 'required|callback_longLatFormat');
        $this->form_validation->set_rules('latitude', 'Latitude', 'required|callback_longLatFormat');
        $this->form_validation->set_rules('radius', 'Radius', 'required|numeric');
        $this->form_validation->set_rules('radiusType', 'Tipe Radius', 'required');
        $this->form_validation->set_rules('merchant', 'Merchant', 'required');

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
        $this->form_validation->set_message('validate_date_time_minutes','%s Format tidak sesuai! ');

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
            exit;
        }       
    
        $dataHeader = array(
                                        "rms_code"=>$rmsCode,
                                        "type"=>2, // typw 1 channel mobile dan web dan ifcs, type 2 b2b channel ifcs
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

        $dataChannel [] = array(
            "rms_code"=>$rmsCode,
            "channel"=>"b2b",
            "status"=>1,
            "created_on"=>date("Y-m-d H:i:s"),
            "created_by"=>$this->session->userdata("username"),
        );

        $merchant = array_filter(array_map(function($x){
            return $this->enc->decode(trim($x));
        }, explode(",",$getMerchant)), function($f){
            return $f !="";
        });  
        
        $isOutlet = array_filter(array_map(function($x){
            return $this->enc->decode(trim($x));
        }, explode(",",$getIsOutlet)), function($f){
            return $f !="";
        });

        // print_r($getIsOutlet); exit;

        $dataIsOutlet = array_combine($isOutlet,$isOutlet);
        
        $dataMerchant =[];
        foreach ($merchant as $keyMerchant => $valueMerchant) {     
            $dataMerchant[]= array(
                "rms_code"=>$rmsCode,
                "merchant_id"=>$valueMerchant,
                "is_custom_outlet"=>!empty($dataIsOutlet[$valueMerchant])?true:false,
                "status"=>1,
                "created_on"=>date("Y-m-d H:i:s"),
                "created_by"=>$this->session->userdata("username") );       
        }


        $data =  array(
                                    $this->_table => $dataHeader,
                                    $this->_table_rms_detail_channel => $dataChannel,
                                    $this->_table_rms_detail_class => $dataVehicle,
                                    $this->_table_rms_merchant_b2b => $dataMerchant,
                                );                               
        
        $checkOverlaps = $this->rms->checkOverlaps($startDate, $endDate, $port);

        if($servicePnp != 't' && $serviceKnd != 't' )
        {
            echo $res=json_api(0,"Layanan harus di checklist minimal 1 pilihan");
        }
        else if(array_sum($checkVehicledEmpty)>0)
        {
            echo $res=json_api(0,"Golongan Kendaraan harus di checklist minimal 1 pilihan");
        }
        else if(count($dataMerchant) < 1 )
        {
            echo $res=json_api(0,"Merchnat harus di pilih minimal 1 ");
        }
        else if(array_sum($checkVehicledEmptyId)>0)
        {
            echo $res=json_api(0,"Id golongan tidak sesuai");
        }   
        else if($checkOverlaps->num_rows()>0)
        {
            echo $res=json_api(0," Waktu tidak boleh bersinggungan dalam satu pelabuhan yang sama ");
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
            $this->rms->insert_data_batch($this->_table_rms_merchant_b2b ,$dataMerchant);
            if(!empty($dataVehicle))
            {
                $this->rms->insert_data_batch($this->_table_rms_detail_class,$dataVehicle);
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
        $logUrl      = site_url().'radius/rms_b2b/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
    public function add_detail_outlet($rmsCode){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $code = $this->enc->decode($rmsCode);

        $getDetail = $this->rms->select_data("app.t_mtr_rms_merchant_b2b", " where rms_code='".$code."' and is_custom_outlet ='t' and status=1")->result();
        $whereMerchantId = array_map(function($x){return "'".$x."'"; },array_column($getDetail,"merchant_id"));        
        $getWhereMerchantId = empty($whereMerchantId)?"''": implode(",", $whereMerchantId);

        // print_r($getDetail); exit;
        $getMerchant = $this->rms->select_data("app.t_mtr_merchant", " where merchant_id in (".$getWhereMerchantId.") and status=1")->result();
        
        $dataMerchant[""] = "Pilih";
        foreach ($getMerchant as $key => $value) {
            $dataMerchant[$this->enc->encode($value->merchant_id)] = $value->merchant_name;
        }
        // $merchant = $this->rms->getDropdown("app.t_mtr_merchant","merchant_id","merchant_name");

        $data['title'] = 'Tambah Radius B2B Detail Outlet';
        $data['merchant'] = $dataMerchant;
        $data['rmsCode'] = $rmsCode;

        $this->load->view($this->_module.'/add_detail_outlet',$data);
    }    
    public function action_add_detail_outlet()
    {
        $rmsCode = $this->enc->decode($this->input->post("rmsCode", true));
        $idData = $this->input->post("idData", true);
        $idMemberImpact  = $this->input->post("idMemberImpact[]"); 
        $inputRemoveUser  = $this->input->post("inputRemoveUser[]"); 
        // $outletId = $this->input->post("outletId", true);

        $_POST["rmsCode"] = $rmsCode;

        // $this->form_validation->set_rules('outletId', 'Outlet', 'required');        
        $this->form_validation->set_rules('rmsCode', 'Kode Rms', 'required');
        $this->form_validation->set_rules('idData', 'idData', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
            exit;
        }

        $implodeOutlet = "";
        if(!empty($idMemberImpact))
        {
            $outlet = array_map( function($x){ return "'".$this->db->escape_str($x)."'"; },$idMemberImpact);
            $implodeOutlet = "(".implode(",", $outlet).")";
        }

        $getMechant = $this->rms->select_data("app.t_mtr_rms_merchant_b2b"," where status =1 and rms_code='".$rmsCode."'  and is_custom_outlet is true")->result();

        $data= array(
            "idData" =>$idData,
            "rmsCode" =>$rmsCode,
            "id_outlet" =>$idMemberImpact,
            "created_by"=>$this->session->userdata("username"),
            "created_on"=>date("Y-m-d H:i:s"),
        );

        // $dataUpdate= array(
        //     "status"=>'-5',
        //     "updated_by"=>$this->session->userdata("username"),
        //     "updated_on"=>date("Y-m-d H:i:s"),
        // );        

        if(empty($getMechant))
        {
            echo $res=json_api(0, 'Tidak ada nerchant sampe pembatasan outlet');
        }
        else
        {
            $this->db->trans_begin();
            $getOutletMechant = $this->rms->select_data("app.t_mtr_rms_outlet_b2b"," where status =1 and rms_code='".$rmsCode."'  ")->result();

            $getMechantString = array_map(function($x){ return "'".$x."'"; } , array_column($getMechant,"merchant_id"));
            if($idData == 1)
            {                            
                if(!empty($implodeOutlet))
                {
                    $whereInsert =" where status = 1 and id in ".$implodeOutlet." and merchant_id in (".implode(",",$getMechantString).") ";
                    $this->rms->insert_detail($data, $whereInsert);
                }

            }
            else
            {
                $whereOtutlet = empty($implodeOutlet)?"":" and id not in ".$implodeOutlet;
                $whereInsert =" where status = 1 ".$whereOtutlet." and merchant_id in (".implode(",",$getMechantString).") ";

                if($getOutletMechant)
                {
                    $whereInsert .= " 
                        and concat(merchant_id,outlet_id) not in (
                            select concat(merchant_id,outlet_id) from  app.t_mtr_rms_outlet_b2b
                            where rms_code='".$rmsCode."' and status = 1 )
                    ";
                }
                // die($whereInsert); exit;
                $this->rms->insert_detail($data, $whereInsert);
            }
    
        
    
            
            // soft delete data berdasarkan kode rmsnya
            // $this->rms->update_data("$this->_table_rms_outlet_b2b",$dataUpdate, "  rms_code='".$rmsCode."' and status=1");
            // insert ulang data di sesuaikan dengan kode  rms dan datanya
            // $this->rms->insert_detail($data, $whereInsert);
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
         $logUrl      = site_url().'radius/rms_b2b/add_detail_outlet';
         $logMethod   = 'ADD';
         $logParam    = json_encode($data);
         $logResponse = $res;
 
         $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }        
    public function action_add_detail_outlet_07122023()
    {
        $rmsCode = $this->enc->decode($this->input->post("rmsCode", true));
        $merchant = $this->enc->decode($this->input->post("merchant", true));
        $outletId = $this->input->post("outletId", true);

        $_POST["rmsCode"] = $rmsCode;
        $_POST["merchant"] = $merchant;
        $this->form_validation->set_rules('outletId', 'Outlet', 'required');        
        $this->form_validation->set_rules('rmsCode', 'Kode Rms', 'required');
        $this->form_validation->set_rules('merchant', 'Merchant', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
            exit;
        }

        // check apakah datanya ada di merchant outlet
        $explodeOutletId = explode(",",$outletId);
        $outlet = array_map(function($x){ return $this->db->escape($x); }, $explodeOutletId);

        $searchDataOutlet =  $this->rms->select_data("app.t_mtr_outlet_merchant", " where outlet_id in (".implode(",",$outlet).") and merchant_id='".$merchant."'  and status=1 ")->result();
        $getDiffOutlet = array_diff($explodeOutletId, array_column($searchDataOutlet, "outlet_id"));

        //check apakah outletId sudah masuk ke app.t_mtr_rms_outlet_b2b
        $check =  $this->rms->select_data("app.t_mtr_rms_outlet_b2b ", " where outlet_id in (".implode(",",$outlet).") and merchant_id='".$merchant."' and rms_code='".$rmsCode."'  and status=1 ")->result();
        
        $data=[];
        foreach ($explodeOutletId as $key => $value) {
            $data[] = array(
                "rms_code"=>$rmsCode,
                "merchant_id"=>$merchant,
                "outlet_id"=>$value,
                "status"=>1,
                "created_by"=>$this->session->userdata("username"),
                "created_on"=>date("Y-m-d H:i:s"),
            );
        }

        if (!empty($getDiffOutlet)){
            echo $res=json_api(0,"Outlet<br> ".implode("<br>",$getDiffOutlet)."<br>Tidak ditemukan di dalam merchant ini");
        }
        else if(!empty($check))
        {
            echo $res=json_api(0,"Data Outlet<br> ".implode("<br>",array_column($check,"outlet_id"))."<br> Sudah ada");
        }
        else
        {
            $this->rms->insert_data_batch($this->_table_rms_outlet_b2b, $data);
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
         $logUrl      = site_url().'radius/rms_b2b/add_detail_outlet';
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
                "account_id"=>$this->enc->decode(trim($x)),
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
        $detailVehicle = $this->rms->getRmsVehicleDetail($codeDecode);
        $detailMerchant = $this->rms->getRmsMerchantDetail($codeDecode);
        $vehicleClass = $this->rms->getDropdown("app.t_mtr_vehicle_class","id","name");
        $selectedVehicle = $this->getSelectedVehicle($detailVehicle, $vehicleClass);
        $merchant = $this->rms->getDropdown("app.t_mtr_merchant","merchant_id","merchant_name");  
        
        // get selectedMerchant
        $selectedMerchant = array_filter(array_map(function($key) use($detailMerchant){
            foreach ($detailMerchant as $keyM => $valueM) {
                if($valueM->merchant_id == $this->enc->decode(trim($key)) )
                {
                    return $key;
                }
            }
        }, array_keys($merchant)), function($x){
            return $x !="";
        });

        // get is outlet
        $isOutlet = array_filter(array_map(function($key) use($detailMerchant){
            foreach ($detailMerchant as $keyM => $valueM) {
                if($valueM->merchant_id == $this->enc->decode($key) and  $valueM->is_custom_outlet =='t')
                {
                    return $key;
                }
            }
        }, array_keys($merchant)), function($x){
            return $x !="";
        });


        $data['title'] = 'Edit RMS B2B';
        $data['code'] = $code;
        $data['port'] = $this->rms->getDropdown2("app.t_mtr_port","id","name",$detailHeader->port_id);
        $data['service'] = $this->rms->select_data("app.t_mtr_service"," where status =1 order by id asc")->result();
        $data['vehicleClass'] = $vehicleClass;
        $data['detailHeader'] = $detailHeader;
        $data['selectedVehicle'] = $selectedVehicle;
        $data['merchant'] = array_diff($merchant,array(""=>"Pilih"));
        $data['selectedMerchant'] = $selectedMerchant;
        $data['isOutlet'] = array_values($isOutlet);

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        // print_r($this->input->post()); exit;
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
        $getMerchant = $this->input->post("merchant", true);
        $getIsOutlet = $this->input->post("isOutlet", true);
        // $webExp = $this->input->post("webExp[]", true);
        // $ifcsExp = $this->input->post("ifcsExp[]", true);
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

  
        // print_r($checkChannelEmpt); exit;
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('activeDate', 'Tanggal Aktif', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('startDate', 'Tanggal Mulai', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir', 'required|callback_validate_date_time_minutes');
        $this->form_validation->set_rules('longitude', 'Longitude', 'required|callback_longLatFormat');
        $this->form_validation->set_rules('latitude', 'Latitude', 'required|callback_longLatFormat');
        $this->form_validation->set_rules('radius', 'Radius', 'required|numeric');
        $this->form_validation->set_rules('radiusType', 'Tipe Radius', 'required');
        $this->form_validation->set_rules('merchant', 'Merchant', 'required');
        $this->form_validation->set_rules('rmsCode', 'Kode RMS', 'required');

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
        $this->form_validation->set_message('validate_date_time_minutes','%s Format tidak sesuai! ');



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

        $merchant = array_filter(array_map(function($x){
            return $this->enc->decode(trim($x));
        }, explode(",",$getMerchant)), function($f){
            return $f !="";
        });  

        // print_r($merchant); exit;        
        $isOutlet = array_filter(array_map(function($x){
            return $this->enc->decode(trim($x));
        }, explode(",",$getIsOutlet)), function($f){
            return $f !="";
        });
        // print_r($isOutlet); exit;
        $dataIsOutlet = array_combine($isOutlet,$isOutlet);
        
        $dataMerchant =[];
        foreach ($merchant as $keyMerchant => $valueMerchant) {     
            $dataMerchant[]= array(
                "rms_code"=>$rmsCode,
                "merchant_id"=>$valueMerchant,
                "is_custom_outlet"=>!empty($dataIsOutlet[$valueMerchant])?true:false,
                "status"=>1,
                "created_on"=>date("Y-m-d H:i:s"),
                "created_by"=>$this->session->userdata("username") );       
        }

        $data =  array(
                $this->_table => $dataHeader,
                $this->_table_rms_detail_class => $dataVehicle,
                $this->_table_rms_merchant_b2b => $dataMerchant,
            );        


        $checkOverlaps = $this->rms->checkOverlaps($startDate, $endDate, $port, $rmsCode);
        if($servicePnp != 't' && $serviceKnd != 't' )
        {
            echo $res=json_api(0,"Layanan harus di checklist minimal 1 pilihan");
        }        
        else if(array_sum($checkVehicledEmpty)>0)
        {
            echo $res=json_api(0,"Golongan Kendaraan harus di checklist minimal 1 pilihan");
        }
        else if(count($dataMerchant) < 1 )
        {
            echo $res=json_api(0,"Merchnat harus di pilih minimal 1 ");
        }        
        else if(array_sum($checkVehicledEmptyId)>0)
        {
            echo $res=json_api(0,"Id golongan tidak sesuai");
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
            $this->rms->update_data($this->_table_rms_merchant_b2b,$softDeleteData, " rms_code='".$rmsCode."' and status =1 ");
            $this->rms->update_data($this->_table_rms_detail_class,$softDeleteData, " rms_code='".$rmsCode."' and status =1 ");
            
            // soft delelete outlet2 yang tidak terkena pembatasan radius/ di uncheck
            $mapIsOutlet = array_map(function($x){return "'".trim($x)."'"; },$isOutlet);
            $implodeIsOutlet =  implode (",",$mapIsOutlet);            
            $whereUpdateOutlet = empty($implodeIsOutlet)?"rms_code='".$rmsCode."' ":"rms_code='".$rmsCode."'  and merchant_id not in (".$implodeIsOutlet.") ";

            $this->rms->update_data($this->_table_rms_outlet_b2b,$softDeleteData, $whereUpdateOutlet." and status =1 ");


            $this->rms->insert_data_batch($this->_table_rms_merchant_b2b ,$dataMerchant);

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
        $message = @$explode[3];
        

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
            if(!empty($message))
            {
                echo $res=json_api(0, 'Gagal '.$message);
            }
            else
            {
                echo $res=json_api(0, 'Gagal delete data');
            }
        }
        else
        {
            $this->db->trans_commit();
            $data=array("rms_code"=>$rms_code);

            if(!empty($message))
            {
                echo $res=json_api(1, 'Berhasil '.$message, $data);
            }
            else
            {
                echo $res=json_api(1, 'Berhasil delete data', $data);
            }

            
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/bank/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_limit($getId)
    {

        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');
        $idDecode = $this->enc->decode($getId);
        if(empty($idDecode))
        {
            echo $res=json_api(0, 'Gagal membatasi outlet');
        }

        $explode = explode("|",$idDecode); 
        $table = $explode[1];
        $id = $explode[0];
        $rms_code = @$explode[2];

        $getDataOutlet = $this->rms->select_data("app.t_mtr_outlet_merchant", " where id = ".$id)->row();

        $data=array(
            'rms_code'=>$rms_code,
            'merchant_id'=>$getDataOutlet->merchant_id,
            'outlet_id'=>$getDataOutlet->outlet_id,
            'status'=>1,
            'created_on'=>date("Y-m-d H:i:s"),
            'created_by'=>$this->session->userdata('username'),
            );

        $check = $this->rms->select_data("app.t_mtr_rms_outlet_b2b", " where rms_code ='".$rms_code."' and status=1 and outlet_id='".$getDataOutlet->outlet_id."' and merchant_id='".$getDataOutlet->merchant_id."' ");

        if($check->num_rows>0)
        {
            echo $res=json_api(0, 'Outlet Sudah berdampak');
        }
        else
        {
            $this->db->trans_begin();
            // $this->rms->update_data($table,$data," id='".$id."'");
            $this->rms->insert_data("app.t_mtr_rms_outlet_b2b", $data);
    
            if ($this->db->trans_status() === FALSE)
            {            
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal membatasi outlet');
            }
            else
            {
                $this->db->trans_commit();
                $data=array("rms_code"=>$rms_code);
                echo $res=json_api(1, 'Berhasil membatasi outlet', $data);
            }   
        }


        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/bank/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    

    public function getUser()
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
        $tagEmailStr = array_map(function($x){ return "'".$this->db->escape_str($x)."'";}, $tagEmailArr);
        $qryUser =$this->rms->select_data("app.t_mtr_member", " where email in (".implode(", ", $tagEmailStr).")  and status=1")->result();

        $arrDiff = array_diff($tagEmailArr, array_column($qryUser,"email"));
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
                $value->email = $value->email."|".$this->enc->encode($value->email);
                $sendData[] = $value;
            }            
            echo  json_api(1, 'Berhasil Tambah email', array("email"=>array_column($sendData,"email")));
        }
        
    }

    public function getUserIfcs()
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
        $tagEmailStr = array_map(function($x){ return "'".$this->db->escape_str($x)."'";}, $tagEmailArr);
        $qryUser =$this->rms->select_data("app.t_mtr_member_ifcs", " where email in (".implode(", ", $tagEmailStr).") and status=1 ")->result();

        $arrDiff = array_diff($tagEmailArr, array_column($qryUser,"email"));
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
                $value->email = $value->email."|".$this->enc->encode($value->email);
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

        $getCode ="RB".$codePort.date('ymdhis');
        return $getCode;
    }    

}
