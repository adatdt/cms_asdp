<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class VaccineParam extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('VaccineParamModel','vaccine');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_vaccine_param';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'vaccine_parameter/vaccineParam';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->vaccine->dataList();
            echo json_encode($rows);
            exit;
        }

        $getMaxVaccine = $this->vaccine->select_data("app.t_mtr_custom_param"," where param_name='max_vaccine' ")->row();
        $getTestCovid = $this->vaccine->select_data("app.t_mtr_test_covid"," where status=1 ")->result();

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Parameter Vaksin',
            'content'  => 'vaccineParam/index',
            'getMaxVaccine'  => $getMaxVaccine->param_value,
            'getTestCovid'  => json_encode($getTestCovid),
            'content'  => 'vaccineParam/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'btn_add_vehicle'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	
    }

	public function detailPort(){   
        checkUrlAccess($this->_module,'view');

        if($this->input->is_ajax_request()){
            $rows = $this->vaccine->getPortDetail();
            echo json_encode($rows);
            exit;
        }
	
    }


	public function detailVehicle(){   
        checkUrlAccess($this->_module,'view');

        if($this->input->is_ajax_request()){
            $rows = $this->vaccine->getVehicleDetail();
            echo json_encode($rows);
            exit;
        }
	
    }    


    public function add_10122021(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // $assessmentType[""]="Pilih";
        // $assessmentType[$this->enc->encode('peduli_lindungi')]=strtoupper('peduli_lindungi');

        // $assessmentTestType[""]="Pilih";
        // $assessmentTestType[$this->enc->encode('peduli_lindungi')]=strtoupper('peduli_lindungi');       
        
        $getAssessmentType= $this->vaccine->getAssesmentType("where status=1");

        // print_r($getAssesmentType[0]->type); exit;
        $assessmentType[""]="Pilih";
        foreach ($getAssessmentType as $key=>$value) 
        {
            $assessmentType[$this->enc->encode($value->type)] =$value->type;
        }
        
        $port=$this->vaccine->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();
        $vehicleClass=$this->vaccine->select_data("app.t_mtr_vehicle_class", " where status=1 order by name asc")->result();

        $dataPort=array();
        foreach($port as $key=>$value)
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $dataVehicle=array();
        foreach($vehicleClass as $key=>$value)
        {
            $dataVehicle[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        $data['title'] = 'Tambah Param Vaksin';
        $data['assessmentType'] = $assessmentType;
        $data['assessmentTestType'] = $assessmentType;
        $data['port'] = $dataPort;
        $data['vehicleClass'] = $dataVehicle;


        $this->load->view($this->_module.'/add',$data);
    }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

    

        // $getAssessmentTypeVaccine= $this->vaccine->getAssesmentType(" where type='vaksin' and status=1 ");
        // $getAssessmentTypeTest= $this->vaccine->getAssesmentType(" where type='test_covid_19' and status=1 ");

        $getAssessmentTypeVaccine= $this->vaccine->getAssesmentType(" where status=1 and group_type='assesment_vaccine_covid_19' ");
        $getAssessmentTypeTest= $this->vaccine->getAssesmentType(" where   status=1 and group_type='assesment_test_covid_19' ");

        $getMaxVaccine = $this->vaccine->select_data("app.t_mtr_custom_param"," where param_name='max_vaccine' ")->row();
        $getTestCovid = $this->vaccine->select_data("app.t_mtr_test_covid"," where status=1 ")->result();

        // print_r($getAssesmentType[0]->type); exit;
        // $assessmentTypeVaccine=array();
        $assessmentTypeVaccine[""]="Pilih";
        foreach ($getAssessmentTypeVaccine as $key=>$value) 
        {
            $assessmentTypeVaccine[$this->enc->encode($value->type)] =$value->type;
        }

        // $assessmentTypeTest=array();
        $assessmentTypeTest[""]="Pilih";
        foreach ($getAssessmentTypeTest as $key=>$value) 
        {
            $assessmentTypeTest[$this->enc->encode($value->type)] =$value->type;
        }

        
        $port=$this->vaccine->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();
        $vehicleClass=$this->vaccine->select_data("app.t_mtr_vehicle_class", " where status=1 order by name asc")->result();

        $dataPort=array();
        foreach($port as $key=>$value)
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $dataVehicle=array();
        foreach($vehicleClass as $key=>$value)
        {
            $dataVehicle[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        $data['title'] = 'Tambah Param Vaksin';
        $data['assessmentType'] = $assessmentTypeVaccine;
        $data['assessmentTestType'] = $assessmentTypeTest;
        $data['port'] = $dataPort;
        $data['vehicleClass'] = $dataVehicle;
        $data['getMaxVaccine'] = $getMaxVaccine->param_value;
        $data['getTestCovid'] = $getTestCovid;


        $this->load->view($this->_module.'/add',$data);
    }    

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $assesmentType=$this->enc->decode($this->input->post('assessmentType'));
        $assessmentTestType=$this->enc->decode($this->input->post('assessmentTestType'));
        
        $startDate=$this->input->post('startDate');
        $endDate=$this->input->post('endDate');
        $port=$this->input->post('port2');
        $vehicleClass=$this->input->post('vehicleClass2');
        // $minAge=$this->input->post('minAge');
        $underAgeReason=$this->input->post('underAgeReason');

        // $timer=$this->input->post('timer');
        $pedestrian=$this->input->post('pedestrian');
        $vehicle=$this->input->post('vehicle');
        $web=$this->input->post('web');
        $mobile=$this->input->post('mobile');
        $ifcs=$this->input->post('ifcs');
        $b2b=$this->input->post('b2b');
        // $vaccineActive= $this->input->post('vaccineActive');
        $vaccineActive= true;
        $testVaccineActive= $this->input->post('testVaccineActive');

        $pos_vehicle=$this->input->post("pos_vehicle");
        $pos_passanger=$this->input->post("pos_passanger");
        $mpos=$this->input->post("mpos");
        $vm=$this->input->post("vm");
        $verifikator=$this->input->post("verifikator");
        $web_cs=$this->input->post("web_cs");


        // get vaccine min age
        $idMinAge = $this->input->post("idMinAge[]");
        $minAge = $this->input->post("minAge[]");


        $errVaksin[]=0;
        $errVaksinMessege=array();

        $errMinAge[]=0;
        $errMinAgeMessage=array();

        $countDataVaccineTest=array();
        foreach($idMinAge as $idMinAge )
        {
            $usia = $this->input->post("minAge[{$idMinAge}]");

            $this->form_validation->set_rules("idMinAge[{$idMinAge}]", 'Id Min Usia', 'required');
            $this->form_validation->set_rules("minAge[{$idMinAge}]", 'Min Usia', 'required');

            $dataVaccineTest = array();
            $vaccineCovid= $this->input->post("vaccineCovid_".$idMinAge."[]");

            foreach($vaccineCovid as $key => $vaccineCovid2)
            {
                
                $this->form_validation->set_rules("vaccineCovid_".$idMinAge."[{$key}]", 'Vaksin Covid', 'required');
                $this->form_validation->set_rules("testCovid_".$idMinAge."[{$key}]", 'Tes Covid', 'required');

                $dataVaccineTest[]=array(
                    "vaccine_status"=>$vaccineCovid2,
                    "test_status"=>$this->input->post("testCovid_".$idMinAge."[{$key}]"),
                );
            }

            $vaccineDataDetail[]=array(
                "min_age"=>$minAge[$idMinAge],
                "detail"=>$dataVaccineTest,
            );

            // pengecekan vaksin dosis jika ada yang di set sama 
            $countDataVaccineTest = $vaccineCovid;
            $countUnique = array_unique($vaccineCovid);
            // check apakah dalam minimal usia terdapat vaksin yang sama
            if(count($countDataVaccineTest) != count($countUnique))
            {
                $errVaksin[]=1;
                $errVaksinMessege[]="<br> - Min Usia ".$usia;
        
            }


            // pengecekan data min usia jika di set usia yang duplicate atau sama
            
            $countUsia = 0;
            foreach($minAge as $minAge2)
            {
                if($minAge2 == $usia)
                {
                    $countUsia += 1;
                }

            }
            

            if($countUsia >= 2 )
            {
                $errMinAge[]=1;
                $errMinAgeMessage[]="<br> - Min Usia ".$usia;
            }
            
        }

        // print_r($errMinAge); exit;


        $this->form_validation->set_rules('assessmentType', 'Tipe Assesmen', 'required');
        $this->form_validation->set_rules('startDate', 'Tanggal Awal ', 'required');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir ', 'required');
        $this->form_validation->set_rules('port2', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('vehicleClass2', 'Kelas Kendaraan ', 'required');
        // $this->form_validation->set_rules('minAge', 'Minimal Usia ', 'required|numeric');
        $this->form_validation->set_rules('assessmentTestType', 'Tipe Assesmen Tes', 'required');
        $this->form_validation->set_rules('underAgeReason', 'Alasan di bawah umur', 'required');

        // $this->form_validation->set_rules('timer','Timer','required|number');
        // $this->form_validation->set_rules('pedestrian','Pedestrian','required');
        // $this->form_validation->set_rules('vehicle','Kendaraan','required');
        // $this->form_validation->set_rules('web','Web','required');
        // $this->form_validation->set_rules('mobile','Mobile','required');
        // $this->form_validation->set_rules('ifcs','IFCS','required');
        // $this->form_validation->set_rules('b2b','B2B','required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');

        $dataPort=array();
        foreach(explode(",",$port) as $value)
        {
            $dataPort[]=$this->enc->decode($value);
        }

        
        $dataVehicleClass=array();
        foreach(explode(",",$vehicleClass) as $value)
        {
            $dataVehicleClass[]=$this->enc->decode($value);
        }
        
        // mengurutkan aray berdasarkan value asc
        sort($dataPort);
        sort($dataVehicleClass);
        
        
        $data=array(
                    'assessment_type'=>$assesmentType,
                    'assessment_type_test'=>$assessmentTestType,
                    'vaccine_active'=>$vaccineActive,
                    'test_covid_active'=>$testVaccineActive,
                    // 'timer'=>$timer,
                    'pos_vehicle'=>$pos_vehicle,
                    'pos_passanger'=>$pos_passanger,
                    'mpos'=>$mpos,
                    'vm'=>$vm,
                    'verifikator'=>$verifikator,
                    'under_age_reason'=>$underAgeReason,
                    'start_date'=>$startDate,
                    'end_date'=>$endDate,
                    'min_age'=>min($minAge),
                    'pedestrian'=>$pedestrian,
                    'vehicle'=>$vehicle,
                    'web'=>$web,
                    'web_cs'=>$web_cs,
                    'mobile'=>$mobile,
                    'ifcs'=>$ifcs,
                    'b2b'=>$b2b,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );
        
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if(array_sum($errMinAge) > 0)
        {
            $errMsg = implode(" ",array_unique($errMinAgeMessage))." <br> tidak boleh sama";
            echo $res =json_api(0,$errMsg);
        }  
        else if(array_sum($errVaksin) > 0)
        {
            $errMsg = "Vaksin ".implode(" ",$errVaksinMessege)." <br> tidak boleh diatur dengan dosis yang sama";
            echo $res =json_api(0,$errMsg);
        }
        else
        {
            $where = "where 
                status=1 
                and start_date='{$startDate}'
                and end_date='{$endDate}'
            ";

            if(empty($vaccineActive))
            {
                $where .="
                    and vaccine_active is null
                ";
            }
            else
            {
                $where .="
                    and vaccine_active is not null
                ";
            }

            if(empty($testVaccineActive))
            {
                $where .="
                    and test_covid_active is null
                ";
            }
            else
            {
                $where .="
                    and test_covid_active is not null
                ";
            }            

            // mencari data sesuai dengan rangenya 
            $getByRange=$this->vaccine->select_data("app.t_mtr_vaccine_param",$where);
            

            $getError[]=0;
            $getErrorName=array();
            if($getByRange->num_rows()>0)
            {
                foreach ($getByRange->result() as $key => $value) {
                    
                    $getVacinePortDetail=$this->vaccine->getPortDetailVaccine($value->id);
                    $getVacineVehicleDetail=$this->vaccine->getVehicleDetailVaccine($value->id);

                    // echo implode("_",$getVacinePortDetail['portId'])." | ".implode("_",$dataPort)."<br>";
                    // echo implode("_",$getVacineVehicleDetail['vehicleClassId'])." | ".implode("_",$dataVehicleClass)."<br>";

                    if(implode("_",$getVacinePortDetail['portId'])==implode("_",$dataPort) && implode("_",$getVacineVehicleDetail['vehicleClassId'])==implode("_",$dataVehicleClass) )
                    {
                        $getError[]=1;
                        $getErrorName[]="Pelabuhan ".implode(",", $getVacinePortDetail['portName']) ." dengan Kelas Kendaraan ".implode(",", $getVacineVehicleDetail['vehicleClassName'])."<br> Sudah Ada";
                    }
                    
                }
            }

            // echo array_sum($getError); exit;
            // $dataVehicleClass

            if(array_sum($getError)>0)
            {
                echo $res=json_api(0,$getErrorName[0]);
            }
            else
            {

                $this->db->trans_begin();
                                
                $getId=$this->vaccine->insert_data_id($this->_table,$data);
                // $getId=0;
                
                foreach($dataPort as $value)
                {
                    $insertPortDetail=array(
                        "vaccine_param_id"=>$getId,
                        "port_id"=>$value,
                        "status"=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                    );
                    
                    //insert app.t_mtr_vaccine_param_detail_port
                    $this->vaccine->insert_data("app.t_mtr_vaccine_param_detail_port",$insertPortDetail);
                }
    
                foreach($dataVehicleClass as $value)
                {
                    $insertvehicleClassDetail=array(
                        "vaccine_param_id"=>$getId,
                        "vehicle_class_id"=>$value,
                        "status"=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                    );

                    // insert app.t_mtr_vaccine_param_detail_vehicle
                    $this->vaccine->insert_data("app.t_mtr_vaccine_param_detail_vehicle",$insertvehicleClassDetail);
                }

                $dataDetail=array();
                $dataDetailVaccineTest=array();

                $kode = $this->createCode();

                foreach($vaccineDataDetail as $key => $value )
                {
                                    
                    $dataDetail[]= array(
                        "vaccine_param_id"=>$getId,
                        "vaccine_param_detail_code"=>$kode,
                        "min_age"=>$value['min_age'],
                        "status"=>1,
                        "created_by"=>$this->session->userdata("username"),
                        "created_on"=>date("Y-m-d H:i:s"),
                    );

                    foreach($value['detail'] as $key2 => $value2)
                    {
                        $dataDetailVaccineTest[]=array(
                            "vaccine_param_detail_code"=>$kode,
                            "vaccine_status"=>$value2["vaccine_status"],
                            "test_status"=>$value2["test_status"],
                            "status"=>1,
                            "created_by"=>$this->session->userdata("username"),
                            "created_on"=>date("Y-m-d H:i:s"),
                        );
                    }

                    $front_code="VPC".date('ymd');
                    $noUrut = (int) substr($kode, strlen($front_code), 4);
                    $noUrut++;
                    $char = $front_code;
                    $kode = $char . sprintf("%04s", $noUrut);
                }
                
                $this->vaccine->insert_data_batch("app.t_mtr_vaccine_param_detail_age",$dataDetail);
                $this->vaccine->insert_data_batch("app.t_mtr_vaccine_param_detail",$dataDetailVaccineTest);

                // print_r($dataDetailVaccineTest);
                // exit;
        
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
    

        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'vaccine_parameter/vaccineParam/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function add_detail_vehicle($idParamVaksin="")
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        if(empty($idParamVaksin))
        {
            $where=" where status=1 order by name asc ";
        }
        else
        {
            $where=" where status=1 
                    and  id not in (
                        select 
                            vehicle_class_id 
                        from app.t_mtr_vaccine_param_detail_vehicle
                        where vaccine_param_id='".$this->enc->decode($idParamVaksin)."'
                        and status !='-5'
                    )
                    order by name asc 
                ";
        }

        $vehicleClass=$this->vaccine->select_data("app.t_mtr_vehicle_class",$where)->result();


        $dataVehicle[""]="Pilih";
        foreach($vehicleClass as $key=>$value)
        {
            $dataVehicle[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        $dataParamVaccine=$this->vaccine->select_data("app.t_mtr_vaccine_param", " where id=".$this->enc->decode($idParamVaksin) )->row();

        $data['title'] = 'Tambah Detail Kelas Kendaraan';
        $data['idParamVaksin'] = $idParamVaksin;
        $data['idTable'] = "vehicleDataTables_".$this->enc->decode($idParamVaksin);
        $data['vehicleClass'] = $dataVehicle;
        $data['paramVaccine'] = $dataParamVaccine;


        $this->load->view($this->_module.'/add_detail_vehicle',$data);
    }


    public function action_add_detail_vehicle()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');
        
        $idParamVaksin=$this->enc->decode($this->input->post('idParamVaksin'));
        $vehicleClass=$this->enc->decode($this->input->post('vehicleClass'));
        $startDate=$this->input->post('startDate');
        $endDate=$this->input->post('endDate');

        $vaccineActive= $this->input->post('vaccineActive');
        $testVaccineActive= $this->input->post('testVaccineActive');

        // echo $startDate; exit;


        $this->form_validation->set_rules('idParamVaksin', 'Id Kosong', 'required');
        $this->form_validation->set_rules('vehicleClass', 'Golongan Kendaraan ', 'required');
        $this->form_validation->set_rules('startDate', 'Tanggal Mulai', 'required');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir ', 'required');


        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');


        $data=array(
        'vaccine_param_id'=>$idParamVaksin,
        'vehicle_class_id'=>$vehicleClass,
        'status'=>1,
        'created_by'=>$this->session->userdata('username'),
        'created_on'=>date("Y-m-d H:i:s"),
        );

        $dataVehicleClass=array();
        $dataPort=array();

        $selectDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where status =1 and vaccine_param_id=".$idParamVaksin)->result();
        $selectDetailPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port", " where status =1 and vaccine_param_id=".$idParamVaksin)->result();

        if ($selectDetailVehicle) {
            foreach ($selectDetailVehicle as $key => $value) {
                $dataVehicleClass[]=$value->vehicle_class_id;
            }
        }   
                            
        if ($selectDetailPort) {
            foreach ($selectDetailPort as $key => $value) {
                $dataPort[]=$value->port_id;
            }
        }       

        // check vehicle class apakah sudah ada datanya
        $checkData= $this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle"," where status =1 and vehicle_class_id={$vehicleClass} and vaccine_param_id={$idParamVaksin}  ");
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($checkData->num_rows()>0)
        {
            echo $res=json_api(0,"Data Sudah Ada ");
        }
        else
        {

            $dataVehicleClass[]=$vehicleClass;
            // ascending data array
            sort($dataVehicleClass);
            sort($dataPort);

            $where = "where 
            id !=$idParamVaksin
            and status=1 
            and start_date='{$startDate}'
            and end_date='{$endDate}'            
            ";

            if(empty($vaccineActive))
            {
                $where .="
                    and vaccine_active is null
                ";
            }
            else
            {
                $where .="
                    and vaccine_active is not null
                ";
            }

            if(empty($testVaccineActive))
            {
                $where .="
                    and test_covid_active is null
                ";
            }
            else
            {
                $where .="
                    and test_covid_active is not null
                ";
            }            

            // mencari data sesuai dengan rangenya 
            $getByRange=$this->vaccine->select_data("app.t_mtr_vaccine_param",$where);    
            
            $getError[]=0;
            $getErrorName=array();
            if($getByRange->num_rows()>0)
            {
                foreach ($getByRange->result() as $key => $value) {
                    
                    $getVacinePortDetail=$this->vaccine->getPortDetailVaccine($value->id);
                    $getVacineVehicleDetail=$this->vaccine->getVehicleDetailVaccine($value->id);

                    // echo implode("_",$getVacinePortDetail['portId'])." | ".implode("_",$dataPort)."<br>";
                    // echo implode("_",$getVacineVehicleDetail['vehicleClassId'])." | ".implode("_",$dataVehicleClass)."<br>";

                    if(implode("_",$getVacinePortDetail['portId'])==implode("_",$dataPort) && implode("_",$getVacineVehicleDetail['vehicleClassId'])==implode("_",$dataVehicleClass) )
                    {
                        $getError[]=1;
                        $getErrorName[]="Pelabuhan ".implode(",", $getVacinePortDetail['portName']) ." dengan Kelas Kendaraan ".implode(",", $getVacineVehicleDetail['vehicleClassName'])." <br>Sudah Ada";
                    }
                    
                }
            }

            // echo array_sum($getError); exit;            

            if(array_sum($getError)>0)
            {
                echo $res=json_api(0,$getErrorName[0]);
            }
            else
            {
            
                $this->db->trans_begin();
    
                $this->vaccine->insert_data_id("app.t_mtr_vaccine_param_detail_vehicle",$data);
    
                // print_r($data); exit;
    
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

            
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'vaccine_parameter/vaccineParam/action_add_detail_vehicle';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit_10122021($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);

        $getDetail= $this->vaccine->select_data($this->_table,"where id=$id_decode")->row();

        // $assessmentType[""]="Pilih";
        // $assessmentType['peduli_lindungi']=strtoupper('peduli_lindungi');

        $getAssessmentType= $this->vaccine->getAssesmentType(" where status =1 ");

        // print_r($getAssesmentType[0]->type); exit;
        $assessmentType[""]="Pilih";
        $assessmentTestType[""]="Pilih";

        $selectedAssessmentType="";
        $selectedAssessmentTestType="";

        foreach ($getAssessmentType as $key=>$value) 
        {
            
            if($getDetail->assessment_type==$value->type)
            {
                $selectedAssessmentType=$this->enc->encode($value->type);
                $assessmentType[$selectedAssessmentType] =$value->type;
            }
            else
            {
                $assessmentType[$this->enc->encode($value->type)] =$value->type;                
            }

            
            if($getDetail->assessment_type_test==$value->type)
            {
                $selectedAssessmentTestType=$this->enc->encode($value->type);
                $assessmentTestType[$selectedAssessmentTestType] =$value->type;
            }
            else
            {
                $assessmentTestType[$this->enc->encode($value->type)] =$value->type;      
            }            
        }  
        
        
        

        $qryDetailPort = $this->vaccine->qryDetailPort()." where dp.vaccine_param_id={$id_decode} and dp.status <>'-5' order by p.name asc ";
        $qryDetailVehicle = $this->vaccine->qryDetailVehicle()." where dp.vaccine_param_id={$id_decode} and dp.status <>'-5' order by p.name asc ";

        $detailVehicle=$this->db->query($qryDetailVehicle)->result();
        $detailPort=$this->db->query($qryDetailPort)->result();     
        
        
        $portId=array();
        $vehicleClassId=array();
        $getDetailPort=array();
        foreach ($detailPort as $key => $value) {
            $portId[]="'".$value->port_id."'";

            if($value->status==1)
            {
                $value->label_status=success_color("Aktif");
            }
            else if ($value->status==0)
            {
                $value->label_status=failed_color("Non Aktif");
            }
            else
            {
                $value->label_status=failed_color("Delete");
            }       
            
            $getDetailPort[]=$value;
            
        }

        $getDetailVehicle=array();
        foreach ($detailVehicle as $key => $value) 
        {
            if($value->status==1)
            {
                $value->label_status=success_color("Aktif");
            }
            else if ($value->status==0)
            {
                $value->label_status=failed_color("Non Aktif");
            }
            else
            {
                $value->label_status=failed_color("Delete");
            }

            $vehicleClassId[]="'".$value->vehicle_class_id."'";
            $getDetailVehicle[]=$value;
            
        }        


        $port=$this->vaccine->select_data("app.t_mtr_port", " where status=1 and id not in (".implode(",",$portId).") order by name asc")->result();
        $vehicleClass=$this->vaccine->select_data("app.t_mtr_vehicle_class", " where status=1 and id not in (".implode(",",$vehicleClassId).") order by name asc")->result();
    
        foreach($port as $key=>$value)
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $dataVehicle=array();
        foreach($vehicleClass as $key=>$value)
        {
            $dataVehicle[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        


        $data['title'] = 'Edit Parameter Vaksin';
        $data['id'] = $id;
        $data['detail']=$getDetail;
        $data['detailVehicle']=$getDetailVehicle;
        $data['detailPort']=$getDetailPort;
        // hardcord user group operator kapal
        $data['assessmentType'] = $assessmentType;
        $data['assessmentTestType'] = $assessmentTestType;
        $data['selectedAssessmentType'] = $selectedAssessmentType;
        $data['selectedAssessmentTestType'] = $selectedAssessmentTestType;        
        $data['port'] = $dataPort;
        $data['vehicleClass'] = $dataVehicle;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);

        $getDetail= $this->vaccine->select_data($this->_table,"where id=$id_decode")->row();
        $getDetailMinAge = $this->vaccine->getDetailMinAge($id_decode);

        $getMaxVaccine = $this->vaccine->select_data("app.t_mtr_custom_param"," where param_name='max_vaccine' ")->row();
        $getTestCovid = $this->vaccine->select_data("app.t_mtr_test_covid"," where status=1 ")->result();


        $minAge=array();
        $vaccineParamDetailCode=array();
        $tempAge=array();

        // ambil data untuk umur nya aja
        foreach($getDetailMinAge as $key => $value)
        {            
            
            $tempAge[]=$value->min_age."-".$value->vaccine_param_detail_code;

        }

        // anbil kode detail min age  dan min usia agar tidak duplicate
        foreach(array_unique($tempAge) as $tempAge2 )
        {
            $explodeMinAge = explode("-",$tempAge2);
            $minAge[]= $explodeMinAge[0];
            $vaccineParamDetailCode []=$explodeMinAge[1];
        }


        // $getAssessmentTypeVaccine= $this->vaccine->getAssesmentType(" where type='vaksin' and status=1 ");
        // $getAssessmentTypeTest= $this->vaccine->getAssesmentType(" where type='test_covid_19' and status=1 ");

        // $getAssessmentTypeVaccine= $this->vaccine->getAssesmentType(" where  status=1 ");
        // $getAssessmentTypeTest= $this->vaccine->getAssesmentType(" where  status=1 ");

        $getAssessmentTypeVaccine= $this->vaccine->getAssesmentType(" where status=1 and group_type='assesment_vaccine_covid_19' ");
        $getAssessmentTypeTest= $this->vaccine->getAssesmentType(" where   status=1 and  group_type='assesment_test_covid_19' ");

    
        // print_r($getAssesmentType[0]->type); exit;
        // $assessmentType=array();
        // $assessmentTestType=array();

        $assessmentType[""]="Pilih";
        $assessmentTestType[""]="Pilih";

        $selectedAssessmentType="";
        $selectedAssessmentTestType="";
        
        foreach ($getAssessmentTypeVaccine as $key => $value) {
            
            if($getDetail->assessment_type==$value->type)
            {
                $selectedAssessmentType=$this->enc->encode($value->type);
                $assessmentType[$selectedAssessmentType] =$value->type;
            }
            else
            {
                $assessmentType[$this->enc->encode($value->type)] =$value->type;
            }

        }

        foreach ($getAssessmentTypeTest as $key => $value) {
     
            
            if($getDetail->assessment_type_test==$value->type)
            {
                $selectedAssessmentTestType=$this->enc->encode($value->type);
                $assessmentTestType[$selectedAssessmentTestType] =$value->type;
            }
            else
            {
                $assessmentTestType[$this->enc->encode($value->type)] =$value->type;      
            }       
        }        

        $qryDetailPort = $this->vaccine->qryDetailPort()." where dp.vaccine_param_id={$id_decode} and dp.status <>'-5' order by p.name asc ";
        $qryDetailVehicle = $this->vaccine->qryDetailVehicle()." where dp.vaccine_param_id={$id_decode} and dp.status <>'-5' order by p.name asc ";

        $detailVehicle=$this->db->query($qryDetailVehicle)->result();
        $detailPort=$this->db->query($qryDetailPort)->result();     
        
        
        $portId=array();
        $vehicleClassId=array();
        $getDetailPort=array();
        foreach ($detailPort as $key => $value) {
            $portId[]="'".$value->port_id."'";

            if($value->status==1)
            {
                $value->label_status=success_color("Aktif");
            }
            else if ($value->status==0)
            {
                $value->label_status=failed_color("Non Aktif");
            }
            else
            {
                $value->label_status=failed_color("Delete");
            }       
            
            $getDetailPort[]=$value;
            
        }

        $getDetailVehicle=array();
        foreach ($detailVehicle as $key => $value) 
        {
            if($value->status==1)
            {
                $value->label_status=success_color("Aktif");
            }
            else if ($value->status==0)
            {
                $value->label_status=failed_color("Non Aktif");
            }
            else
            {
                $value->label_status=failed_color("Delete");
            }

            $vehicleClassId[]="'".$value->vehicle_class_id."'";
            $getDetailVehicle[]=$value;
        }

        $portImplode="";
        if($portId)
        {
            $portImplode="and id not in (".implode(",",$portId).") ";    
        }
        $port=$this->vaccine->select_data("app.t_mtr_port", " where status=1  {$portImplode} order by name asc")->result();

        $vehicleClassImplode="";
        if($vehicleClassId)
        {
            $vehicleClassImplode="and id not in (".implode(",",$vehicleClassId).")";
        }        
        $vehicleClass=$this->vaccine->select_data("app.t_mtr_vehicle_class", " where status=1  {$vehicleClassImplode} order by name asc")->result();
       
        $dataPort=array();
        foreach($port as $key=>$value)
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $dataVehicle=array();
        foreach($vehicleClass as $key=>$value)
        {
            $dataVehicle[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        


        $data['title'] = 'Edit Parameter Vaksin';
        $data['id'] = $id;
        $data['detail']=$getDetail;
        $data['detailVehicle']=$getDetailVehicle;
        $data['detailPort']=$getDetailPort;
        // hardcord user group operator kapal
        $data['assessmentType'] = $assessmentType;
        $data['assessmentTestType'] = $assessmentTestType;
        $data['selectedAssessmentType'] = $selectedAssessmentType;
        $data['selectedAssessmentTestType'] = $selectedAssessmentTestType;
        $data['port'] = $dataPort;
        $data['vehicleClass'] = $dataVehicle;
        $data['getDetailMinAge'] = $getDetailMinAge; 
        $data['minAge'] = $minAge; // menghindari data dulplikat karena multiple data
        $data['vaccineParamDetailCode']=$vaccineParamDetailCode;
        $data['minAgeDetail'] = $getDetailMinAge;
        $data['getMaxVaccine'] = $getMaxVaccine->param_value;
        $data['getTestCovid'] = $getTestCovid;

        $this->load->view($this->_module.'/edit',$data);   
    }


    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=$this->enc->decode($this->input->post('id'));

        // echo $id; exit;

        $assesmentType=$this->enc->decode($this->input->post('assessmentType'));
        $assessmentTestType=$this->enc->decode($this->input->post('assessmentTestType'));
        $startDate=$this->input->post('startDate');
        $endDate=$this->input->post('endDate');
        $port=$this->input->post('port2');
        $vehicleClass=$this->input->post('vehicleClass2');
        // $minAge=$this->input->post('minAge');    
        $underAgeReason=$this->input->post('underAgeReason');
            

        // $timer=$this->input->post('timer');
        $pedestrian=$this->input->post('pedestrian');
        $vehicle=$this->input->post('vehicle');
        $web=$this->input->post('web');
        $mobile=$this->input->post('mobile');
        $ifcs=$this->input->post('ifcs');
        $b2b=$this->input->post('b2b');
        // $vaccineActive= $this->input->post('vaccineActive');
        $vaccineActive= true;
        $testVaccineActive= $this->input->post('testVaccineActive');

        $pos_vehicle=$this->input->post("pos_vehicle");
        $pos_passanger=$this->input->post("pos_passanger");
        $mpos=$this->input->post("mpos");
        $vm=$this->input->post("vm");
        $verifikator=$this->input->post("verifikator");
        $web_cs=$this->input->post("web_cs");

        // get vaccine min age
        $idMinAge = $this->input->post("idMinAge[]");
        $minAge = $this->input->post("minAge[]");


        $errVaksin[]=0;
        $errVaksinMessege=array();

        $errMinAge[]=0;
        $errMinAgeMessage=array();

        $countDataVaccineTest=array();
        foreach($idMinAge as $idMinAge )
        {
            $usia = $this->input->post("minAge[{$idMinAge}]");

            $this->form_validation->set_rules("idMinAge[{$idMinAge}]", 'Id Min Usia', 'required');
            $this->form_validation->set_rules("minAge[{$idMinAge}]", 'Min Usia', 'required');

            $dataVaccineTest = array();
            $vaccineCovid= $this->input->post("vaccineCovid_".$idMinAge."[]");

            foreach($vaccineCovid as $key => $vaccineCovid2)
            {
                
                $this->form_validation->set_rules("vaccineCovid_".$idMinAge."[{$key}]", 'Vaksin Covid', 'required');
                $this->form_validation->set_rules("testCovid_".$idMinAge."[{$key}]", 'Tes Covid', 'required');

                $dataVaccineTest[]=array(
                    "vaccine_status"=>$vaccineCovid2,
                    "test_status"=>$this->input->post("testCovid_".$idMinAge."[{$key}]"),
                );
            }

            $vaccineDataDetail[]=array(
                "min_age"=>$minAge[$idMinAge],
                "detail"=>$dataVaccineTest,
            );

            // pengecekan vaksin dosis jika ada yang di set sama 
            $countDataVaccineTest = $vaccineCovid;
            $countUnique = array_unique($vaccineCovid);
            // check apakah dalam minimal usia terdapat vaksin yang sama
            if(count($countDataVaccineTest) != count($countUnique))
            {
                $errVaksin[]=1;
                $errVaksinMessege[]="<br> - Min Usia ".$usia;

            }


            // pengecekan data min usia jika di set usia yang duplicate atau sama
            
            $countUsia = 0;
            foreach($minAge as $minAge2)
            {
                if($minAge2 == $usia)
                {
                    $countUsia += 1;
                }

            }
            

            if($countUsia >= 2 )
            {
                $errMinAge[]=1;
                $errMinAgeMessage[]="<br> - Min Usia ".$usia;
            }
            
        }

        // print_r($errMinAge); exit;


        
        $this->form_validation->set_rules('id', 'Id', 'required');
        $this->form_validation->set_rules('underAgeReason', 'Alasan Bawah Umur', 'required');
        $this->form_validation->set_rules('assessmentType', 'Tipe Assesmen', 'required');
        $this->form_validation->set_rules('assessmentTestType', 'Tipe Assesmen Tes', 'required');
        $this->form_validation->set_rules('startDate', 'Tanggal Awal ', 'required');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir ', 'required');
        // $this->form_validation->set_rules('port2', 'Pelabuhan ', 'required');
        // $this->form_validation->set_rules('vehicleClass2', 'Kelas Kendaraan ', 'required');
        // $this->form_validation->set_rules('minAge', 'Minimal Usia ', 'required|numeric');

        // $this->form_validation->set_rules('timer','Timer','required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!');


        $data=array(
                    'assessment_type'=>$assesmentType,
                    'assessment_type_test'=>$assessmentTestType,
                    // 'timer'=>$timer,
                    'start_date'=>$startDate,
                    'under_age_reason'=>$underAgeReason,
                    'pos_vehicle'=>$pos_vehicle,
                    'pos_passanger'=>$pos_passanger,
                    'mpos'=>$mpos,
                    'vm'=>$vm,
                    "verifikator"=>$verifikator,
                    'end_date'=>$endDate,
                    'min_age'=>min($minAge),
                    'pedestrian'=>$pedestrian,
                    'vehicle'=>$vehicle,
                    'web'=>$web,
                    'mobile'=>$mobile,
                    'ifcs'=>$ifcs,
                    'web_cs'=>$web_cs,
                    'b2b'=>$b2b,
                    'vaccine_active'=>$vaccineActive,
                    'test_covid_active'=>$testVaccineActive,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        // penampung sementara data port dan vehicleClass
        $dataVehicleClass=array();
        $dataPort=array();

        $selectDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where status =1 and vaccine_param_id=".$id)->result();
        // print_r($selectDetailVehicle); echo $id; exit;
        $selectDetailPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port", " where  status =1 and vaccine_param_id=".$id)->result();

        if ($selectDetailVehicle) {
            foreach ($selectDetailVehicle as $key => $value) {
                $dataVehicleClass[]=$value->vehicle_class_id;
            }
        }

        if ($selectDetailPort) {
            foreach ($selectDetailPort as $key => $value) {
                $dataPort[]=$value->port_id;
            }
        }        

        // check vehicle class apakah datanya sudah ada di detailnya
        $checkVehicleClass[]=0;
        $checkVehicleClassName=array();
        if(!empty($vehicleClass))
        {
            foreach(explode(",",$vehicleClass) as $value)
            {
                $checkDataVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where status =1 and vaccine_param_id=".$id." and vehicle_class_id=".$this->enc->decode($value));
                if($checkDataVehicle->num_rows()>0)
                {
                    $getVehicleClassName=$this->vaccine->select_data("app.t_mtr_vehicle_class"," where id=".$this->enc->decode($value))->row();
                    $checkVehicleClass[]=1;
                    $checkVehicleClassName[]=$getVehicleClassName->name;
                }
                $dataVehicleClass[]=$this->enc->decode($value);
            }
        }    

        // check port apakah datanya sudah ada di detailnya
        $checkPort[]=0;
        $checkPortName=array();
        if(!empty($port))
        {
            foreach(explode(",",$port) as $value)
            {
                $checkDataPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port", " where status =1 and vaccine_param_id=".$id." and port_id=".$this->enc->decode($value));
                if($checkDataPort->num_rows()>0)
                {
                    $getPortName=$this->vaccine->select_data("app.t_mtr_port"," where id=".$this->enc->decode($value))->row();
                    $checkPort[]=1;
                    $checkPortName[]=$getPortName->name;
                }
                $dataPort[]=$this->enc->decode($value);
            }
        }            
        
        // echo array_sum($checkVehicleClass); exit;

        $checkGetDetailPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port"," where vaccine_param_id={$id} and status!='-5'")->result();
        $checkGetDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle"," where vaccine_param_id={$id} and status!='-5'")->result();

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if(array_sum($checkVehicleClass)>0)
        {
            $implode=implode("<br>",$checkVehicleClassName);
            echo $res=json_api(0, "Kelas Kendaraan <br>".$implode."<br> Sudah ada" );
        }
        else if(array_sum($checkPort)>0)
        {
            $implode=implode("<br>",$checkPortName);
            echo $res=json_api(0, "Pelabuhan <br>".$implode."<br> Sudah ada" );
        }
        else if(!$checkGetDetailPort and empty($port))
        {
            echo $res=json_api(0, "Pelabuhan Tidak Boleh Kosong" );
        }        
        else if(!$checkGetDetailVehicle and empty($vehicleClass))
        {
            echo $res=json_api(0, "Golongan Kendaraan Tidak Boleh Kosong" );
        }
        else if(array_sum($errMinAge) > 0)
        {
            $errMsg = implode(" ",array_unique($errMinAgeMessage))." <br> tidak boleh sama";
            echo $res =json_api(0,$errMsg);
        }  
        else if(array_sum($errVaksin) > 0)
        {
            $errMsg = "Vaksin ".implode(" ",$errVaksinMessege)." <br> tidak boleh diatur dengan dosis yang sama";
            echo $res =json_api(0,$errMsg);
        }        
        else
        {
            // ascending data array
            sort($dataVehicleClass);
            sort($dataPort);

            $where = "where 
            id !=$id
            and status=1 
            and start_date='{$startDate}'
            and end_date='{$endDate}'            
            ";

            if(empty($vaccineActive))
            {
                $where .="
                    and vaccine_active is null
                ";
            }
            else
            {
                $where .="
                    and vaccine_active is not null
                ";
            }

            if(empty($testVaccineActive))
            {
                $where .="
                    and test_covid_active is null
                ";
            }
            else
            {
                $where .="
                    and test_covid_active is not null
                ";
            }            

            // mencari data sesuai dengan rangenya 
            $getByRange=$this->vaccine->select_data("app.t_mtr_vaccine_param",$where);    
            
            $getError[]=0;
            $getErrorName=array();
            if($getByRange->num_rows()>0)
            {
                foreach ($getByRange->result() as $key => $value) {
                    
                    $getVacinePortDetail=$this->vaccine->getPortDetailVaccine($value->id);
                    $getVacineVehicleDetail=$this->vaccine->getVehicleDetailVaccine($value->id);

                    // echo implode("_",$getVacinePortDetail['portId'])." | ".implode("_",$dataPort)."<br>";
                    // echo implode("_",$getVacineVehicleDetail['vehicleClassId'])." | ".implode("_",$dataVehicleClass)."<br>";

                    if(implode("_",$getVacinePortDetail['portId'])==implode("_",$dataPort) && implode("_",$getVacineVehicleDetail['vehicleClassId'])==implode("_",$dataVehicleClass) )
                    {
                        $getError[]=1;
                        $getErrorName[]="Pelabuhan ".implode(",", $getVacinePortDetail['portName']) ." dengan Kelas Kendaraan ".implode(",", $getVacineVehicleDetail['vehicleClassName'])." <br>Sudah Ada";
                    }
                    
                }
            }

            // echo array_sum($getError); exit;
            // $dataVehicleClass            
            if(array_sum($getError)>0)
            {
                echo $res=json_api(0,$getErrorName[0]);
            }
            else
            {

                $this->db->trans_begin();

                $this->vaccine->update_data($this->_table,$data,"id=$id");

                // jika diisi baru pelabuhannya
                if(!empty($port))
                {
                    foreach(explode(",",$port) as $value)
                    {
                        
                        $insertPortDetail=array(
                            "vaccine_param_id"=>$id,
                            "port_id"=>$this->enc->decode($value),
                            "status"=>1,
                            'created_by'=>$this->session->userdata('username'),
                            'created_on'=>date("Y-m-d H:i:s"),
                        );
                        
                        $this->vaccine->insert_data("app.t_mtr_vaccine_param_detail_port",$insertPortDetail);     

                    }
                }
        
                // jika diisi baru vehicle classnya
                if(!empty($vehicleClass))
                {
                    foreach(explode(",",$vehicleClass) as $value)
                    {
                        $insertvehicleClassDetail=array(
                            "vaccine_param_id"=>$id,
                            "vehicle_class_id"=>$this->enc->decode($value),
                            "status"=>1,
                            'created_by'=>$this->session->userdata('username'),
                            'created_on'=>date("Y-m-d H:i:s"),
                        );

                        $this->vaccine->insert_data("app.t_mtr_vaccine_param_detail_vehicle",$insertvehicleClassDetail);

                    }
                }
                
                
                // vaksin min age di soft delete terlebih dahulu sebelum di insert kembali
                $checkMinAge=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_age"," where vaccine_param_id=".$id)->result();

                $whereCode=array();

                foreach($checkMinAge as $key => $checkMinAge2)
                {
                    $whereCode[]="'".$checkMinAge2->vaccine_param_detail_code."'";
                }

                $softDelete = array(
                    "status"=>'-5',
                    "updated_by"=>$this->session->userdata("username"),
                    "updated_on"=>date("Y-m-d H:i:s"),
                );


                // soft delete 
                $this->vaccine->update_data("app.t_mtr_vaccine_param_detail_age",$softDelete," vaccine_param_id=".$id);
                $this->vaccine->update_data("app.t_mtr_vaccine_param_detail",$softDelete," vaccine_param_detail_code in (".implode(",",$whereCode).")");


                $dataDetail=array();
                $dataDetailVaccineTest=array();

                $kode = $this->createCode();

                foreach($vaccineDataDetail as $key => $value )
                {
                                    
                    $dataDetail[]= array(
                        "vaccine_param_id"=>$id,
                        "vaccine_param_detail_code"=>$kode,
                        "min_age"=>$value['min_age'],
                        "status"=>1,
                        "created_by"=>$this->session->userdata("username"),
                        "created_on"=>date("Y-m-d H:i:s"),
                    );

                    foreach($value['detail'] as $key2 => $value2)
                    {
                        $dataDetailVaccineTest[]=array(
                            "vaccine_param_detail_code"=>$kode,
                            "vaccine_status"=>$value2["vaccine_status"],
                            "test_status"=>$value2["test_status"],
                            "status"=>1,
                            "created_by"=>$this->session->userdata("username"),
                            "created_on"=>date("Y-m-d H:i:s"),
                        );
                    }

                    $front_code="VPC".date('ymd');
                    $noUrut = (int) substr($kode, strlen($front_code), 4);
                    $noUrut++;
                    $char = $front_code;
                    $kode = $char . sprintf("%04s", $noUrut);
                }
            
                $this->vaccine->insert_data_batch("app.t_mtr_vaccine_param_detail_age",$dataDetail);
                $this->vaccine->insert_data_batch("app.t_mtr_vaccine_param_detail",$dataDetailVaccineTest);
            
                
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
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'vaccine_parameter/vaccineParam/action_edit';
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

        /*
        $d[0]= id
        $d[1]= status
        $d[2]= tabel
        */

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        $getError[]=0;
        $getErrorName=array();
        if( $d['1']==1)
        {
            //$d=1 dalam keadaan non aktive ingin di aktifkkan     
            $getParam=$this->vaccine->select_data("app.t_mtr_vaccine_param", " where id=".$d[0])->row();
        
            $vaccineActive= $getParam->vaccine_active;
            $testVaccineActive= $getParam->test_covid_active;   
                            
            $startDate=$getParam->start_date;
            $endDate=$getParam->end_date;    

            $selectDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where status ='1' and vaccine_param_id=".$d[0])->result();
            $selectDetailPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port", " where status ='1' and vaccine_param_id=".$d[0])->result();
            
            if ($selectDetailVehicle) {
                foreach ($selectDetailVehicle as $key => $value) {
                    $dataVehicleClass[]=$value->vehicle_class_id;
                }
            }

            if ($selectDetailPort) {
                foreach ($selectDetailPort as $key => $value) {
                    $dataPort[]=$value->port_id;
                }
            }     

            // mengurutkan aray berdasarkan value asc
            sort($dataPort);
            sort($dataVehicleClass);
            
            $where = "where 
            id !=".$d[0]."
            and status=1 
            and start_date='{$startDate}'
            and end_date='{$endDate}'
            ";

            if(empty($vaccineActive))
            {
                $where .="
                    and vaccine_active is null
                ";
            }
            else
            {
                $where .="
                    and vaccine_active is not null
                ";
            }

            if(empty($testVaccineActive))
            {
                $where .="
                    and test_covid_active is null
                ";
            }
            else
            {
                $where .="
                    and test_covid_active is not null
                ";
            }            

            // mencari data sesuai dengan rangenya 
            $getByRange=$this->vaccine->select_data("app.t_mtr_vaccine_param",$where);
            
            if($getByRange->num_rows()>0)
            {
                foreach ($getByRange->result() as $key => $value) {
                    
                    $getVacinePortDetail=$this->vaccine->getPortDetailVaccine($value->id);
                    $getVacineVehicleDetail=$this->vaccine->getVehicleDetailVaccine($value->id);

                    // echo implode("_",$getVacinePortDetail['portId'])." | ".implode("_",$dataPort)."<br>";
                    // echo implode("_",$getVacineVehicleDetail['vehicleClassId'])." | ".implode("_",$dataVehicleClass)."<br>";

                    if(implode("_",$getVacinePortDetail['portId'])==implode("_",$dataPort) && implode("_",$getVacineVehicleDetail['vehicleClassId'])==implode("_",$dataVehicleClass) )
                    {
                        $getError[]=1;
                        $getErrorName[]="Pelabuhan ".implode(",", $getVacinePortDetail['portName']) ." dengan Kelas Kendaraan ".implode(",", $getVacineVehicleDetail['vehicleClassName'])."<br> Sudah Ada";
                    }
                    
                }
            }
        }    

        // echo array_sum($getError); exit;        

        if(array_sum($getError)>0)
        {
            echo $res=json_api(0,$getErrorName[0]);
        }
        else
        {

            $this->db->trans_begin();
            $this->vaccine->update_data($d[2],$data," id=".$d[0]);

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
            
        }    
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'vaccine_parameter/vaccineParam/action_change';
        $d[1]==1?$logMethod='DISABLE':$logMethod='ENABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function action_change_detail_vehicle($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /*
        $d[0]= id
        $d[1]= status
        $d[2]= tabel
        */

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        if( $d['1']==1)
        {
            $dataVehicleClass=array();
            $dataPort=array();
            //$d=1 dalam keadaan non aktive ingin di aktifkkan            

            $getDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle"," where id=".$d['0'])->row();
            $getParam=$this->vaccine->select_data("app.t_mtr_vaccine_param", " where id=".$getDetailVehicle->vaccine_param_id)->row();
    
            $vaccineActive= $getParam->vaccine_active;
            $testVaccineActive= $getParam->test_covid_active;   
                            
            $startDate=$getParam->start_date;
            $endDate=$getParam->end_date;    
            
            $selectDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where status ='1' and  id!='".$d['0']."' and vaccine_param_id=".$getDetailVehicle->vaccine_param_id)->result();
            $selectDetailPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port", " where status ='1' and vaccine_param_id=".$getDetailVehicle->vaccine_param_id)->result();
            
            $dataVehicleClass[]=$getDetailVehicle->vehicle_class_id;
            if ($selectDetailVehicle) {
                foreach ($selectDetailVehicle as $key => $value) {
                    $dataVehicleClass[]=$value->vehicle_class_id;
                }
            }
    
            if ($selectDetailPort) {
                foreach ($selectDetailPort as $key => $value) {
                    $dataPort[]=$value->port_id;
                }
            }
            // ascending data array
            sort($dataVehicleClass);
            sort($dataPort);

            $where = "where 
            id !=$getDetailVehicle->vaccine_param_id
            and status=1 
            and start_date='{$startDate}'
            and end_date='{$endDate}'            
            ";

            if(empty($vaccineActive))
            {
                $where .="
                    and vaccine_active is null
                ";
            }
            else
            {
                $where .="
                    and vaccine_active is not null
                ";
            }

            if(empty($testVaccineActive))
            {
                $where .="
                    and test_covid_active is null
                ";
            }
            else
            {
                $where .="
                    and test_covid_active is not null
                ";
            }            

            // mencari data sesuai dengan rangenya 
            $getByRange=$this->vaccine->select_data("app.t_mtr_vaccine_param",$where);    

            $getError[]=0;
            $getErrorName=array();
            if($getByRange->num_rows()>0)
            {
                foreach ($getByRange->result() as $key => $value) {
                    
                    $getVacinePortDetail=$this->vaccine->getPortDetailVaccine($value->id);
                    $getVacineVehicleDetail=$this->vaccine->getVehicleDetailVaccine($value->id);

                    // echo implode("_",$getVacinePortDetail['portId'])." | ".implode("_",$dataPort)."<br>";
                    // echo implode("_",$getVacineVehicleDetail['vehicleClassId'])." | ".implode("_",$dataVehicleClass)."<br>";

                    if(implode("_",$getVacinePortDetail['portId'])==implode("_",$dataPort) && implode("_",$getVacineVehicleDetail['vehicleClassId'])==implode("_",$dataVehicleClass) )
                    {
                        $getError[]=1;
                        $getErrorName[]="Pelabuhan ".implode(",", $getVacinePortDetail['portName']) ." dengan Kelas Kendaraan ".implode(",", $getVacineVehicleDetail['vehicleClassName'])." <br>Sudah Ada";
                    }
                    
                }
            }                      
                        
        }
        else
        {
         
            //$d=0 dalam keadaan aktive ingin di Non aktifkkan            
            $dataVehicleClass=array();
            $dataPort=array();
            $getDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle"," where id=".$d['0'])->row();
            $getParam=$this->vaccine->select_data("app.t_mtr_vaccine_param", " where id=".$getDetailVehicle->vaccine_param_id)->row();

            $vaccineActive= $getParam->vaccine_active;
            $testVaccineActive= $getParam->test_covid_active;   
                            
            $startDate=$getParam->start_date;
            $endDate=$getParam->end_date;    

            $selectDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where status ='1' and  id!='".$d['0']."' and vaccine_param_id=".$getDetailVehicle->vaccine_param_id)->result();
            $selectDetailPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port", " where status ='1' and vaccine_param_id=".$getDetailVehicle->vaccine_param_id)->result();

            if ($selectDetailVehicle) {
                foreach ($selectDetailVehicle as $key => $value) {
                    $dataVehicleClass[]=$value->vehicle_class_id;
                }
            }

            if ($selectDetailPort) {
                foreach ($selectDetailPort as $key => $value) {
                    $dataPort[]=$value->port_id;
                }
            }
            // ascending data array
            sort($dataVehicleClass);
            sort($dataPort);

            $where = "where 
            id !=$getDetailVehicle->vaccine_param_id
            and status=1 
            and start_date='{$startDate}'
            and end_date='{$endDate}'            
            ";

            if(empty($vaccineActive))
            {
                $where .="
                    and vaccine_active is null
                ";
            }
            else
            {
                $where .="
                    and vaccine_active is not null
                ";
            }

            if(empty($testVaccineActive))
            {
                $where .="
                    and test_covid_active is null
                ";
            }
            else
            {
                $where .="
                    and test_covid_active is not null
                ";
            }            

            // mencari data sesuai dengan rangenya 
            $getByRange=$this->vaccine->select_data("app.t_mtr_vaccine_param",$where);    

            $getError[]=0;
            $getErrorName=array();
            if($getByRange->num_rows()>0)
            {
                foreach ($getByRange->result() as $key => $value) {
                    
                    
                    $getVacinePortDetail=$this->vaccine->getPortDetailVaccine($value->id);
                    $getVacineVehicleDetail=$this->vaccine->getVehicleDetailVaccine($value->id);

                    // echo implode("_",$getVacinePortDetail['portId'])." | ".implode("_",$dataPort)."<br>";
                    // echo implode("_",$getVacineVehicleDetail['vehicleClassId'])." | ".implode("_",$dataVehicleClass)."<br>";

                    if(implode("_",$getVacinePortDetail['portId'])==implode("_",$dataPort) && implode("_",$getVacineVehicleDetail['vehicleClassId'])==implode("_",$dataVehicleClass) )
                    {
                        $getError[]=1;
                        $getErrorName[]="Pelabuhan ".implode(",", $getVacinePortDetail['portName']) ." dengan Kelas Kendaraan ".implode(",", $getVacineVehicleDetail['vehicleClassName'])." <br>Sudah Ada";
                    }
                    
                }
            }
                    
        }

        // echo array_sum($getError); exit;        
        if(array_sum($getError)>0)
        {
            echo $res=json_api(0,$getErrorName[0]);
        }
        else
        {
            $this->db->trans_begin();
            $this->vaccine->update_data($d[2],$data," id=".$d[0]);
    
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
        }
        

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'vaccine_parameter/vaccineParam/action_change_detail_vehicle';
        $d[1]==1?$logMethod='DISABLE':$logMethod='ENABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    

    public function action_change_detail_port($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /*
        $d[0]= id
        $d[1]= status
        $d[2]= tabel
        */

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        if( $d['1']==1)
        {
            $dataVehicleClass=array();
            $dataPort=array();
            //$d=1 dalam keadaan non aktive ingin di aktifkkan            

            $getDetailPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port"," where id=".$d['0'])->row();
            $getParam=$this->vaccine->select_data("app.t_mtr_vaccine_param", " where id=".$getDetailPort->vaccine_param_id)->row();
    
            $vaccineActive= $getParam->vaccine_active;
            $testVaccineActive= $getParam->test_covid_active;   
                            
            $startDate=$getParam->start_date;
            $endDate=$getParam->end_date;    
            
            $selectDetailPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port", " where status ='1' and  id!='".$d['0']."' and vaccine_param_id=".$getDetailPort->vaccine_param_id)->result();
            $selectDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where status ='1' and vaccine_param_id=".$getDetailPort->vaccine_param_id)->result();
            
            if ($selectDetailVehicle) {
                foreach ($selectDetailVehicle as $key => $value) {
                    $dataVehicleClass[]=$value->vehicle_class_id;
                }
            }
            
            $dataPort[]=$getDetailPort->port_id;
            if ($selectDetailPort) {
                foreach ($selectDetailPort as $key => $value) {
                    $dataPort[]=$value->port_id;
                }
            }
            // ascending data array
            sort($dataVehicleClass);
            sort($dataPort);

            $where = "where 
            id !=$getDetailPort->vaccine_param_id
            and status=1 
            and start_date='{$startDate}'
            and end_date='{$endDate}'            
            ";

            if(empty($vaccineActive))
            {
                $where .="
                    and vaccine_active is null
                ";
            }
            else
            {
                $where .="
                    and vaccine_active is not null
                ";
            }

            if(empty($testVaccineActive))
            {
                $where .="
                    and test_covid_active is null
                ";
            }
            else
            {
                $where .="
                    and test_covid_active is not null
                ";
            }            

            // mencari data sesuai dengan rangenya 
            $getByRange=$this->vaccine->select_data("app.t_mtr_vaccine_param",$where);    

            $getError[]=0;
            $getErrorName=array();
            if($getByRange->num_rows()>0)
            {
                foreach ($getByRange->result() as $key => $value) {
                    
                    $getVacinePortDetail=$this->vaccine->getPortDetailVaccine($value->id);
                    $getVacineVehicleDetail=$this->vaccine->getVehicleDetailVaccine($value->id);

                    // echo implode("_",$getVacinePortDetail['portId'])." | ".implode("_",$dataPort)."<br>";
                    // echo implode("_",$getVacineVehicleDetail['vehicleClassId'])." | ".implode("_",$dataVehicleClass)."<br>";

                    if(implode("_",$getVacinePortDetail['portId'])==implode("_",$dataPort) && implode("_",$getVacineVehicleDetail['vehicleClassId'])==implode("_",$dataVehicleClass) )
                    {
                        $getError[]=1;
                        $getErrorName[]="Pelabuhan ".implode(",", $getVacinePortDetail['portName']) ." dengan Kelas Kendaraan ".implode(",", $getVacineVehicleDetail['vehicleClassName'])." <br>Sudah Ada";
                    }
                    
                }
            }                      
                        
        }
        else
        {
            $dataVehicleClass=array();
            $dataPort=array();

            //$d=0 dalam keadaan aktive ingin di Non aktifkkan            
            $getDetailPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port"," where id=".$d['0'])->row();
            $getParam=$this->vaccine->select_data("app.t_mtr_vaccine_param", " where id=".$getDetailPort->vaccine_param_id)->row();

            $vaccineActive= $getParam->vaccine_active;
            $testVaccineActive= $getParam->test_covid_active;   
                            
            $startDate=$getParam->start_date;
            $endDate=$getParam->end_date;    

            $selectDetailPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port", " where status ='1' and  id!='".$d['0']."' and vaccine_param_id=".$getDetailPort->vaccine_param_id)->result();
            $selectDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where status ='1' and vaccine_param_id=".$getDetailPort->vaccine_param_id)->result();

            if ($selectDetailVehicle) {
                foreach ($selectDetailVehicle as $key => $value) {
                    $dataVehicleClass[]=$value->vehicle_class_id;
                }
            }

            if ($selectDetailPort) {
                foreach ($selectDetailPort as $key => $value) {
                    $dataPort[]=$value->port_id;
                }
            }
            // ascending data array
            sort($dataVehicleClass);
            sort($dataPort);

            $where = "where 
            id !=$getDetailPort->vaccine_param_id
            and status=1 
            and start_date='{$startDate}'
            and end_date='{$endDate}'            
            ";

            if(empty($vaccineActive))
            {
                $where .="
                    and vaccine_active is null
                ";
            }
            else
            {
                $where .="
                    and vaccine_active is not null
                ";
            }

            if(empty($testVaccineActive))
            {
                $where .="
                    and test_covid_active is null
                ";
            }
            else
            {
                $where .="
                    and test_covid_active is not null
                ";
            }            

            // mencari data sesuai dengan rangenya 
            $getByRange=$this->vaccine->select_data("app.t_mtr_vaccine_param",$where);    

            $getError[]=0;
            $getErrorName=array();
            if($getByRange->num_rows()>0)
            {
                foreach ($getByRange->result() as $key => $value) {
                    
                    
                    $getVacinePortDetail=$this->vaccine->getPortDetailVaccine($value->id);
                    $getVacineVehicleDetail=$this->vaccine->getVehicleDetailVaccine($value->id);

                    // echo implode("_",$getVacinePortDetail['portId'])." | ".implode("_",$dataPort)."<br>";
                    // echo implode("_",$getVacineVehicleDetail['vehicleClassId'])." | ".implode("_",$dataVehicleClass)."<br>";

                    if(implode("_",$getVacinePortDetail['portId'])==implode("_",$dataPort) && implode("_",$getVacineVehicleDetail['vehicleClassId'])==implode("_",$dataVehicleClass) )
                    {
                        $getError[]=1;
                        $getErrorName[]="Pelabuhan ".implode(",", $getVacinePortDetail['portName']) ." dengan Kelas Kendaraan ".implode(",", $getVacineVehicleDetail['vehicleClassName'])." <br>Sudah Ada";
                    }
                    
                }
            }
                    
        }

        // echo array_sum($getError); exit;        

        if(array_sum($getError)>0)
        {
            echo $res=json_api(0,$getErrorName[0]);
        }
        else
        {
            $this->db->trans_begin();
            $this->vaccine->update_data($d[2],$data," id=".$d[0]);
    
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
        }
        
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'vaccine_parameter/vaccineParam/action_change_detail_port';
        $d[1]==1?$logMethod='DISABLE':$logMethod='ENABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }        


    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $id = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->vaccine->update_data($this->_table,$data," id='".$id."'");

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal delete data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil delete data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'vaccine_parameter/vaccineParam/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete_detail_vehicle($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');


        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $id = $this->enc->decode($id);

        $getDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle"," where id=".$id)->row();
        $getParam=$this->vaccine->select_data("app.t_mtr_vaccine_param", " where id=".$getDetailVehicle->vaccine_param_id)->row();

        $vaccineActive= $getParam->vaccine_active;
        $testVaccineActive= $getParam->test_covid_active;

        $startDate=$getParam->start_date;
        $endDate=$getParam->end_date;        
        
        $dataVehicleClass=array();
        $dataPort=array();

        $selectDetailVehicle=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where status !='-5' and  vehicle_class_id !=".$getDetailVehicle->vehicle_class_id." and vaccine_param_id=".$getDetailVehicle->vaccine_param_id)->result();
        $selectDetailPort=$this->vaccine->select_data("app.t_mtr_vaccine_param_detail_port", " where status !='-5' and vaccine_param_id=".$getDetailVehicle->vaccine_param_id)->result();
        
        if ($selectDetailVehicle) {
            foreach ($selectDetailVehicle as $key => $value) {
                $dataVehicleClass[]=$value->vehicle_class_id;
            }
        }

        if ($selectDetailPort) {
            foreach ($selectDetailPort as $key => $value) {
                $dataPort[]=$value->port_id;
            }
        }
        
    // ascending data array
    sort($dataVehicleClass);
    sort($dataPort);

    $where = "where 
    id !=$getDetailVehicle->vaccine_param_id
    and status=1 
    and start_date='{$startDate}'
    and end_date='{$endDate}'            
    ";

    if(empty($vaccineActive))
    {
        $where .="
            and vaccine_active is null
        ";
    }
    else
    {
        $where .="
            and vaccine_active is not null
        ";
    }

    if(empty($testVaccineActive))
    {
        $where .="
            and test_covid_active is null
        ";
    }
    else
    {
        $where .="
            and test_covid_active is not null
        ";
    }            

    // mencari data sesuai dengan rangenya 
    $getByRange=$this->vaccine->select_data("app.t_mtr_vaccine_param",$where);    
    
    $getError[]=0;
    $getErrorName=array();
    if($getByRange->num_rows()>0)
    {
        foreach ($getByRange->result() as $key => $value) {
            
            $getVacinePortDetail=$this->vaccine->getPortDetailVaccine($value->id);
            $getVacineVehicleDetail=$this->vaccine->getVehicleDetailVaccine($value->id);

            // echo implode("_",$getVacinePortDetail['portId'])." | ".implode("_",$dataPort)."<br>";
            // echo implode("_",$getVacineVehicleDetail['vehicleClassId'])." | ".implode("_",$dataVehicleClass)."<br>";

            if(implode("_",$getVacinePortDetail['portId'])==implode("_",$dataPort) && implode("_",$getVacineVehicleDetail['vehicleClassId'])==implode("_",$dataVehicleClass) )
            {
                $getError[]=1;
                $getErrorName[]="Pelabuhan ".implode(",", $getVacinePortDetail['portName']) ." dengan Kelas Kendaraan ".implode(",", $getVacineVehicleDetail['vehicleClassName'])." <br>Sudah Ada";
            }
            
        }
    }
    // echo array_sum($getError); exit;        

    if(array_sum($getError)>0)
    {
        echo $res=json_api(0,$getErrorName[0]);
    }
    else
    {
        $this->db->trans_begin();
        $this->vaccine->update_data("app.t_mtr_vaccine_param_detail_vehicle",$data," id='".$id."'");
    
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal delete data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil delete data');
        } 

    }
        
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'vaccine_parameter/vaccineParam/action_delete_detail_vehicle';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }  
    
    function createCode()
    {

        $front_code="VPC".date('ymd');

        $chekCode=$this->db->query("select * from app.t_mtr_vaccine_param_detail_age where left(vaccine_param_detail_code,".strlen($front_code).")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (vaccine_param_detail_code) as max_code from app.t_mtr_vaccine_param_detail_age where left(vaccine_param_detail_code,".strlen($front_code).")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, strlen($front_code), 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }


}
