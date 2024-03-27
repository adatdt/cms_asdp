<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Pcm_trx_global extends MY_Controller{
    public function __construct(){
        parent::__construct();

        logged_in();
        $this->load->model('m_pcm_trx_global','pcm');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_quota_pcm_vehicle';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pelabuhan/pcm_trx_global';

        // $this->load->library('PHPExcel');

        // $this->dbView = $this->load->database('dbView', TRUE);
        $this->dbView = checkReplication();
        $this->dbAction = $this->load->database('dbAction', TRUE);
    }

    public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('dateFrom')){
                $this->form_validation->set_rules('dateFrom', 'date from', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid format tanggal tanggal mulai'));
            }
            if($this->input->post('dateTo')){
                $this->form_validation->set_rules('dateTo', 'date from', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid format tanggal akhir'));
            }
            if($this->input->post('port')){
                $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
            }
            if($this->input->post('shipClass')){
                $this->form_validation->set_rules('shipClass', 'Kelas kapal', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            }
            if($this->input->post('time')){
                $this->form_validation->set_rules('time', 'time', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid jam'));
            }
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->pcm->dataList();
            $rows["tokenHash"] = $this->security->get_csrf_hash();
            $rows["csrfName"] = $this->security->get_csrf_token_name();
            echo json_encode($rows);
            exit;
        }

        $port = $this->pcm->select_data("app.t_mtr_port", "where status<>'-5' order by name asc")->result();
        $shipClass = $this->pcm->select_data("app.t_mtr_ship_class", "where status<>'-5' order by name asc")->result();
        $vehicleClass = $this->pcm->select_data("app.t_mtr_vehicle_class", "where status<>'-5' order by name asc")->result();

        $dataPort[""]="Pilih";
        $dataShipClass[""]="Pilih";
        $dataVehicleClass[""]="Pilih";
        $dataTime[""]="Pilih";
            
        for($i=0;$i<24;$i++)
        {
            strlen($i)<2?$his="0".$i.":00":$his=$i.":00";

            $dataTime[$this->enc->encode($his)]=$his;

        }        

        foreach ($port as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataPort[$idEncode]=strtoupper($value->name);
        }

        foreach ($shipClass as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataShipClass[$idEncode]=strtoupper($value->name);
        } 

        foreach ($vehicleClass as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataVehicleClass[$idEncode]=strtoupper($value->name);
        }

        $btn_excel='<button onclick=showModal("'.site_url("pelabuhan/pcm_trx_global/edit_import_excel").'") class="btn btn-sm btn-warning" title="Edit Import Data"><i class="fa fa-pencil"></i> Edit Import Data</button>            
        ';

        $btn_excel .=' <a href="'.base_url().'template_excel/edit_excel_pcm_global.xlsx" class="btn btn-sm btn-warning" title="Download Format" >Download Format</a>';

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'PCM Quota Global Kendaraan',
            'content'  => 'pcm_trx_global/index',
            'time'     => $dataTime,
            'port'=>$dataPort,
            'shipClass'=>$dataShipClass,
            'btn_excel'=> $btn_excel,
            'vehicleClass'=>$dataVehicleClass,
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'downloadExcel'=>checkBtnAccess($this->_module,'download_excel'),
            'editExcel'=>checkBtnAccess($this->_module,'edit'),

        );

        $this->load->view('default', $data);
    }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port = $this->pcm->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
        $shipClass = $this->pcm->select_data("app.t_mtr_ship_class", "where status=1 order by name asc")->result();
        $vehicleClass = $this->pcm->select_data("app.t_mtr_vehicle_class", "where status=1 order by name asc")->result();

        $dataPort[""]="Pilih";
        $dataShipClass[""]="Pilih";
        $dataTime[""]="Pilih";
        $dataVehicleClass[""]="Pilih";
            
        for($i=0;$i<24;$i++)
        {
            strlen($i)<2?$his="0".$i.":00":$his=$i.":00";

            $dataTime[$this->enc->encode($his)]=$his;

        }            

        foreach ($port as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataPort[$idEncode]=strtoupper($value->name);
        }

        foreach ($shipClass as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataShipClass[$idEncode]=strtoupper($value->name);
        }   

        foreach ($vehicleClass as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataVehicleClass[$idEncode]=strtoupper($value->name);
        }                

        $data['title'] = 'Tambah PCM Quota Khusus Kendaraan';
        $data['port']=$dataPort;
        $data['time']=$dataTime;
        $data['shipClass']=$dataShipClass;
        $data['vehicleClass']=$dataVehicleClass;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add() // module gak di pake karan di create di scheduler
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=trim($this->enc->decode($this->input->post('port')));
        $shipClass=trim($this->enc->decode($this->input->post('shipClass')));
        $vehicleClass=trim($this->enc->decode($this->input->post('vehicleClass')));
        $time=trim($this->enc->decode($this->input->post('time')));
        $departDate=trim($this->input->post('departDate'));
        $quota=trim($this->input->post('quota'));

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('shipClass', 'Kelas layanan ', 'required');
        $this->form_validation->set_rules('vehicleClass', 'Golongan Kendaraan ', 'required');
        $this->form_validation->set_rules('time', 'Jam Keberangkatan', 'required');
        $this->form_validation->set_rules('departDate', 'Tanggal ', 'required');
        $this->form_validation->set_rules('quota', 'Quota ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!')
                              ->set_message('numeric','%s Harus Berupa angka!');

        
        $insertPcmReseved=array(
                    'port_id'=>$port,
                    'ship_class'=>$shipClass,
                    'vehicle_class_id'=>$vehicleClass,
                    'depart_date'=>$departDate,
                    'depart_time'=>$time,
                    'quota'=>$quota,
                    'used_quota'=>0,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        $dataLog1=$insertPcmReseved;
        $dataLog2=array();

        // ceck master quota pcm vehicle
        $masterQuota[]=0;
        $checkInput[]=0;
        if(!empty($port) and !empty($shipClass))
        {

            $checkQuotaMaster=$this->pcm->select_data("app.t_mtr_quota_pcm_vehicle"," where port_id={$port} and ship_class={$shipClass} and status=1 ");

            if($checkQuotaMaster->num_rows()>0)
            {
                // jika ada master quota yang di temukan
                $masterQuota[]=1;
                
                // Jika yang diinput lebih besar dari master quotanya maka ditolak
                $quota>$checkQuotaMaster->row()->quota?$checkInput[]=0:$checkInput[]=1;
            }
            else
            {                
                $masterQuota[]=0;
                $checkInput[]=1;
            }
        }


        // check apakah datanya sudah ada 
        $checkExistData[]=0;
        if(!empty($port) and !empty($shipClass) and !empty($departDate) and !empty($time) and !empty($vehicleClass) )
        {
            $check=$this->pcm->select_data($this->_table," where status<>'-5' and port_id='{$port}' and ship_class='{$shipClass}' and depart_date='{$departDate}' and depart_time='{$time}' and vehicle_class_id='{$vehicleClass}' ");

            $check->num_rows()>0?$checkExistData[]=1:$checkExistData[]=0;
        }

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if(array_sum($checkInput)<1)
        {
            echo $res=json_api(0,"Quota yang diinput melebihi Quota Master.");
        }
        else if(array_sum($checkExistData)>0)
        {
            echo $res=json_api(0,"Data sudah ada.");
        }        
        else if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$departDate))
        {
            echo $res=json_api(0,"Format Harus Berupa Tanggal.");
        }
        else if(empty($this->checkTime($time)))
        {
            echo $res=json_api(0,"Jam Tidak ditemukan.");   
        }
        else if(array_sum($masterQuota)<1)
        {
            echo $res=json_api(0,"Master Quota Tidak ada, silahkan input terlebih dahulu.");   
        }
        else
        {

            $this->dbAction->trans_begin();

            // GET id from quota pcm vehicle
            $getQuotaVehicleTrx=$this->pcm->select_data("app.t_trx_quota_pcm_vehicle"," where status=1 and port_id='{$port}' and ship_class='{$shipClass}' and depart_date='{$departDate}' and depart_time='{$time}'");

            // check apakah ada quota masternya di trx
            if($getQuotaVehicleTrx->num_rows()>0)
            {
                $param=array(
                    "id"=>$getQuotaVehicleTrx->row()->id,
                    "portId"=>$port,
                    "shipClass"=>$shipClass,
                    "vehicleClassId"=>$vehicleClass,
                    "departDate"=>$departDate,
                    "departTime"=>$time,
                    "quotaInput"=>$quota,
                );
                // jika diinput lebih kecil dari trx global
                if($getQuotaVehicleTrx->row()->total_quota<$quota)
                {
                    // jika input quotanya lebih kecil dari global quota trx
                    $totalQuota=$getQuotaVehicleTrx->row()->total_quota;
                    $totalQuotaTrx=0;

                    $param['type']='a';
                }
                else
                {
                    $totalQuota=$quota;
                    $totalQuotaTrx=$getQuotaVehicleTrx->row()->total_quota-$quota;
                    $param['type']='b';                       
                }

                $this->pcm->updateInsert($param);
            }
            else
            {
                $totalQuota=$quota;
                $updateQuotaTrx=array();

                $insertPcmReseved['total_quota']=$quota;
                $insertPcmReseved['quota_limit']=32767-$quota; // max small int - total quota

                $this->pcm->insert_data($this->_table,$insertPcmReseved);
            }
            

            if ($this->dbAction->trans_status() === FALSE)
            {
                $this->dbAction->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->dbAction->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');
            }

            
        }

        $data=$insertPcmReseved;
        // print_r($data); exit;

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/pcm_trx/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function pembatasanQuotaDetail()
    {
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->pcm->pembatasanQuotaDetail();
            echo json_encode($rows);
            exit;
        }
    }    
    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $idDecode=$this->enc->decode($id);

        $detail=$this->pcm->select_data($this->_table, "where id='{$idDecode}'")->row();
        $port = $this->pcm->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
        $shipClass = $this->pcm->select_data("app.t_mtr_ship_class", "where status=1 order by name asc")->result();

        $dataPort[""]="Pilih";
        $dataShipClass[""]="Pilih";
        $dataTime[""]="Pilih";
        
        $selectedPort="";
        $selectedShipClass="";
        $selectedTime="";

        for($i=0;$i<24;$i++)
        {
            strlen($i)<2?$his="0".$i.":00":$his=$i.":00";

            $hisDecode=$this->enc->encode($his);
            $dataTime[$hisDecode]=$his;

            format_dateTimeHis($his)==format_dateTimeHis($detail->depart_time)?$selectedTime=$hisDecode:"";

        }            

        foreach ($port as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataPort[$idEncode]=strtoupper($value->name);

            $value->id==$detail->port_id?$selectedPort=$idEncode:"";
        }

        foreach ($shipClass as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataShipClass[$idEncode]=strtoupper($value->name);

            $value->id==$detail->ship_class?$selectedShipClass=$idEncode:"";
        }   

        $action=array(  
                        ""=>"Pilih",
                        1=>"Tambah (+)",
                        2=>"Kurang (-)");

        // $action=array( 1=>"Tambah (+)");

        $data['title'] = 'Edit PCM Quota Khusus Kendaraan';
        $data['id'] = $id;
        $data['time'] = $dataTime;
        $data['selectedTime'] = $selectedTime;
        $data['port']=$dataPort;
        $data['selectedPort']=$selectedPort;
        $data['shipClass']=$dataShipClass;
        $data['selectedShipClass']=$selectedShipClass;
        $data['detail']=$detail;
        $data['action']=$action;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $this->form_validation->set_rules('id', 'id', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid  id'));
        $this->form_validation->set_rules('lineMeter', 'lineMeter', 'trim|required|numeric');
         $this->form_validation->set_rules('departDate', 'tanggal keberangkatan', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid format tanggal keberangkatan'));
        $this->form_validation->set_rules('quota', 'quota', 'trim|required|numeric');
        $this->form_validation->set_rules('action', 'action', 'trim|required|numeric');
        $this->form_validation->set_rules('estimation', 'estimation', 'trim|required|numeric');

        $this->form_validation->set_message('required', '%s harus diisi!')
            ->set_message('numeric', '%s harus angka!');

        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
            exit;
        }

        $id=trim($this->enc->decode($this->input->post('id')));
        $quota=trim($this->input->post('quota'));
        $lineMeter=trim($this->input->post('lineMeter'));
        $action=trim($this->input->post('action')); 


        $checkData[]=0;
        $checkValueMin[]=0; // jika kurang tapi estimasinya -

        if(!empty($id))
        {
            $checkId=$this->pcm->select_data($this->_table," where id='{$id}' ");
            
            if($checkId->num_rows()>0)
            {
                if($action==2)
                {
                    $quotaMin=$checkId->row()->total_quota-$quota;

                    $quotaMin<0?$checkValueMin[]=1:"";
                }

            }
            else
            {
                $checkData[]=1;
            }
        }                              
    

        $data=array("id"=>$id,
                    "quota"=>$quota,
                    "action"=>$action,
                    "lineMeter"=>$lineMeter,
                    "updated_on"=>date("Y-m-d H:i:s"),
                    "updated_by"=>$this->session->userdata("username"),
                    "portId"=>$checkId->row()->port_id
                    );

        if(array_sum($checkData)>0)
        {
            echo $res=json_api(0,"Data Tidak Ditemukan");
        }
        else if(array_sum($checkValueMin)>0)
        {
            echo $res=json_api(0,"Estimasi tidak boleh Minus ");   
        }
        else
        {
            // print_r($data); exit;
            $this->dbAction->trans_begin();

            $this->pcm->updateData($data);

            if ($this->dbAction->trans_status() === FALSE)
            {   
                $this->dbAction->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->dbAction->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data');
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/pcm_trx_global/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
    public function edit_restrict($param, $idTable)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $paramDecode=$this->enc->decode($param);
        $explode=explode("|",$paramDecode);

        $where = "
            where rd.depart_date='{$explode[0]}' 
            and rd.depart_time='$explode[3]'
            and r.port_id='$explode[4]'
            and r.ship_class='$explode[1]'
            and r.vehicle_class_id='$explode[2]'
            and rd.status = 1
            and r.status =1
        ";

        // echo $where; exit;
        $qrySearch=" SELECT rd.* from app.t_mtr_quota_pcm_vehicle_restrictions r
                    join app.t_trx_quota_pcm_vehicle_restrictions rd on  r.restriction_quota_code = rd.restriction_quota_code 
                    $where ";

        $dariDataTrx=$this->db->query($qrySearch)->row();

        // $dariDataTrx=$this->pcm->select_data("app.t_trx_quota_pcm_vehicle_restrictions", $where." and vehicle_class_id='$explode[2]' ")->row();

        // $dariDataTrxGlobal=$this->pcm->select_data("app.t_trx_quota_pcm_vehicle", $where)->row();

        if($dariDataTrx)
        {
            $detail['departDate'] = $dariDataTrx->depart_date;
            $detail['departTime'] = $dariDataTrx->depart_time;
            $detail['shipClass'] = $dariDataTrx->ship_class;
            $detail['vehicleClass'] = $dariDataTrx->vehicle_class_id;
            $detail['portId'] = $dariDataTrx->port_id;
            $detail['quota'] = $dariDataTrx->quota;
            $detail['totalQuota'] = $dariDataTrx->total_quota;
            $detail['usedQuota'] = $dariDataTrx->used_quota;
            $detail['totalLm'] = $dariDataTrx->total_lm;
            $detail['id'] = $this->enc->encode($dariDataTrx->id);

        }
        else
        {
            $detail['departDate'] = $explode[0];
            $detail['shipClass'] = $explode [1];
            $detail['vehicleClass'] = $explode[2];
            $detail['departTime'] = $explode[3];
            $detail['portId'] = $explode[4];
            $detail['quota'] = $explode[5];
            $detail['totalQuota'] = $explode[6];
            $detail['usedQuota'] = $explode[7];
            $detail['totalLm'] = $explode[8];
            $detail['id'] = $this->enc->encode("");

        }


        $vehicleClass = $this->pcm->select_data("app.t_mtr_vehicle_class", "where status=1 order by name asc")->result();
        $selectedVehicleClass = "";
        foreach ($vehicleClass as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            if($value->id == $detail['vehicleClass'])
            {
                $selectedVehicleClass = $idEncode;
            }
            $dataVehicleClass[$idEncode]=strtoupper($value->name);
        }
        // print_r($detail); exit;

        $action=array(""=>"Pilih",1=>"Tambah",2=>"Kurang");

        $data['title'] = 'Edit PCM Quota Restrict';
        $data['action']=$action;
        $data['detail']=$detail;
        $data['vehicleClass']=$dataVehicleClass;
        $data['selectedVehicleClass']=$selectedVehicleClass;
        $data['idTable'] = $idTable;

        $this->load->view($this->_module.'/edit_restrict',$data);
    }
    public function action_edit_restrict()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        // print_r($this->input->post()); exit;

        $id=trim($this->enc->decode($this->input->post('id')));
        $shipClass=trim($this->input->post('shipClass'));
        $vehicleClass=trim($this->input->post('vehicleClass'));
        $portId=trim($this->input->post('portId'));
        $departTime=trim($this->input->post('departTime'));
        $departDate=trim($this->input->post('departDate'));
        $totalLm=trim($this->input->post('totalLm'));
        $totalQuota=trim($this->input->post('totalQuota'));
        $usedQuota=trim($this->input->post('usedQuota'));
        $quota=trim($this->input->post('quota'));
        $actions=trim($this->input->post('actions'));
        $estimation=trim($this->input->post('estimation'));


        $this->form_validation->set_rules('id',"id ",'required');
        $this->form_validation->set_rules('shipClass'," Layanan ",'required');
        $this->form_validation->set_rules('vehicleClass'," Kelas Kendaraan ",'required');
        $this->form_validation->set_rules('portId'," Pelabuhan",'required');
        $this->form_validation->set_rules('departTime'," Jam Keberangkatan ",'required');
        $this->form_validation->set_rules('departDate'," Tanggal Keberangkatan",'required');
        $this->form_validation->set_rules('totalLm'," Total Line Meter",'required|numeric');
        $this->form_validation->set_rules('totalQuota'," Total Quota",'required|numeric');
        $this->form_validation->set_rules('usedQuota'," Quota Digunakan",'required|numeric');
        $this->form_validation->set_rules('quota'," Quota",'required|numeric');
        $this->form_validation->set_rules('actions'," Aksi",'required');
        $this->form_validation->set_rules('estimation'," Estimasi",'required|numeric');

 
        $this->form_validation->set_message('required','%s harus diisi!')
                              ->set_message('numeric','%s Harus Berupa angka!');
        $where = "
            where depart_date='$departDate' 
            and depart_time='$departTime'
            and port_id='$portId'
            and ship_class='$shipClass'
            and status <> '-5'
        ";
                              
        $where2 = "
            where rd.depart_date='$departDate'
            and rd.depart_time='$departTime'
            and r.port_id='$portId'
            and r.ship_class='$shipClass'
            and rd.status = 1
            and r.status = 1
        ";



        // check data di trx pcm sebagai parameter quota yang tidak boleh diinput melebihi dari quota nya
        $dariDataTrx=$this->pcm->select_data("app.t_trx_quota_pcm_vehicle", $where)->row();

        
        if($actions==1) // action 1 tambah
        {
            $fixQuota = $totalQuota + $quota;
        }
        else
        {
            $fixQuota = $totalQuota - $quota;
        }

        $data=array(
            "ship_class"=>$shipClass,
            "vehicle_class_id"=>$vehicleClass,
            "port_id"=>$portId,
            "depart_time"=>$departTime,
            "depart_date"=>$departDate,
            "total_lm"=>$totalLm,
            "total_quota"=>$totalQuota,
            "used_quota"=>$usedQuota,
            "quota"=>$quota,
            "created_on"=>date("Y-m-d H:i:s"),
            "created_by"=>$this->session->userdata('username')
        );

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($fixQuota < 0)
        {
            echo $res=json_api(0,"Quota Tidak Boleh Min ");
        }
        else if($dariDataTrx->quota < $fixQuota )
        {
            echo $res=json_api(0,"Quota Restrict Tidak Boleh Kurang dari Quota Master ");
        }        
        else
        {
            $where2 .=" and r.vehicle_class_id='{$vehicleClass}' ";
            
            // check data restriction apaah ada atau tidak
            // $dariDataTrxRestrict=$this->pcm->select_data("app.t_trx_quota_pcm_vehicle_restrictions ", $where)->row();

            $qrySearch=" SELECT rd.* from app.t_mtr_quota_pcm_vehicle_restrictions r
            join app.t_trx_quota_pcm_vehicle_restrictions rd on  r.restriction_quota_code = rd.restriction_quota_code 
            $where2 ";

            $dariDataTrxRestrict=$this->db->query($qrySearch)->row();


            // echo $dariDataTrxRestrict->id; exit;
            // print_r($data); exit;
            $this->db->trans_begin();
            // jika belum ada data quota restiriction maka datanya di insert, tapi jika sudah ada maka datanya di update
            if($dariDataTrxRestrict)
            {
                if($actions==2) // action 2 kurang
                {
                    $this->db->query("
                                update app.t_trx_quota_pcm_vehicle_restrictions
                                    set quota=quota-{$quota},
                                    total_quota=total_quota-{$quota},
                                    quota_limit=quota_limit+{$quota},
                                    total_lm={$totalLm},
                                    updated_on='".date("Y-m-d H:i:s")."',
                                    updated_by='".$this->session->userdata('username')."'
                                    where id='{$dariDataTrxRestrict->id}'
                                ");
                }
                else
                {

                    
                    $this->db->query("
                        update app.t_trx_quota_pcm_vehicle_restrictions
                        set quota=quota + {$quota},
                        total_quota=total_quota + {$quota},
                        quota_limit=quota_limit - {$quota},
                        total_lm={$totalLm},
                        updated_on='".date("Y-m-d H:i:s")."',
                        updated_by='".$this->session->userdata('username')."'
                        where id='{$dariDataTrxRestrict->id}'
                    ");
                    
                }
            }
            else
            {
                // checking di master pcm restrict
                $checkMasterRestrict =$this->pcm->checkMasterRestrict($departDate, $departTime, $portId, $shipClass, $vehicleClass );

                if($actions==2) // action 2 kurang
                {
                    $insertTrxRestrict=array(
                        "restriction_quota_code"=>$checkMasterRestrict->restriction_quota_code,
                        "port_id"=>$checkMasterRestrict->port_id,
                        "ship_class"=>$checkMasterRestrict->ship_class,
                        "vehicle_class_id"=>$checkMasterRestrict->vehicle_class_id,
                        "depart_time"=>$checkMasterRestrict->depart_time,
                        "depart_date"=>$departDate,
                        "quota"=>$checkMasterRestrict->quota - $quota ,
                        "quota_limit "=> (32767 - $checkMasterRestrict->quota) + $quota ,
                        "used_quota"=>0,
                        "total_quota"=>$checkMasterRestrict->quota - $quota ,
                        "total_lm"=>$checkMasterRestrict->total_lm,
                        "used_lm"=>0,
                        "status"=>1,
                        "created_on"=>date("Y-m-d H:i:s"),
                        "created_by"=>$this->session->userdata("username")
                    );
                }
                else
                {
                    $insertTrxRestrict=array(
                        "restriction_quota_code"=>$checkMasterRestrict->restriction_quota_code,
                        "port_id"=>$checkMasterRestrict->port_id,
                        "ship_class"=>$checkMasterRestrict->ship_class,
                        "vehicle_class_id"=>$checkMasterRestrict->vehicle_class_id,
                        "depart_time"=>$checkMasterRestrict->depart_time,
                        "depart_date"=>$departDate,
                        "quota"=>$checkMasterRestrict->quota + $quota ,
                        "quota_limit "=> 32767 - ($checkMasterRestrict->quota + $quota) ,
                        "used_quota"=>0,
                        "total_quota"=>$checkMasterRestrict->quota + $quota ,
                        "total_lm"=>$checkMasterRestrict->total_lm,
                        "used_lm"=>0,
                        "status"=>1,
                        "created_on"=>date("Y-m-d H:i:s"),
                        "created_by"=>$this->session->userdata("username")
                    );
                }
                
                $this->pcm->insert_data("app.t_trx_quota_pcm_vehicle_restrictions",$insertTrxRestrict);
                
            }


            if ($this->db->trans_status() === FALSE)
            {   
                $this->dbAction->trans_rollback();
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
        $logUrl      = site_url().'pelabuhan/pcm_trx_global/action_edit_restrict';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    
    public function edit_import_excel(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $port = $this->pcm->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
        $dataPort[""]="Pilih";

        foreach ($port as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataPort[$idEncode]=strtoupper($value->name);
        }

        $action=array(  
                ""=>"Pilih",
                1=>"Tambah (+)",
                2=>"Kurang (-)"
            );
    
        $data['title'] = 'Edit Import Data';
        $data['port'] = $dataPort;
        $data['action']=$action;

        $this->load->view($this->_module.'/edit_import_excel',$data);
    }    


    public function action_edit_import_excel(){
    
        validate_ajax();

        $portId=$this->enc->decode($this->input->post('port'));
        $action=trim($this->input->post('action'));

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('action', 'Aksi ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');        


        $getData=array();

        $emptyExcel[]=0; // error ketika file excel tgidak dipilih
        $emptyRow[]=0; // error ketika row semua sheet tidak di pilih
        $errEmpt[]=0; // error ketika sell tidak diisi

        $errShipClass[]=0;

        $errPortId[]=0;
        $errNumericQuota[]=0; // error ketika yang di input quota selain numeric data
        $errNumericLineMeter[]=0; // error ketika yang di input Linemeter selain numeric data     

        $errDate[]=0;
        $errTime[]=0;  

        $getAllData=array();
        $checkDuplicateData=array();    

        $errEmptMess=array(); // info Messege ketika sell tidak diisi

        $badSymbols = array(",", ".");
        try{
            // load excel
            $file = $_FILES['excel']['tmp_name'];
            $load = PHPExcel_IOFactory::load($file);  

            $sheets = $load->getActiveSheet()->toArray(null,true,true,true);
            $i2 = 1;
            foreach ($sheets as $value)
            {
                // dimulai dari sheet 8

                if($i2>7)
                {
                    $getDataRow['departDate']=trim($value['A']);
                    $getDataRow['departTime']=substr(trim($value['B']),0,5);
                    $getDataRow['quotaInput']=str_replace($badSymbols, "", trim($value['C']));
                    $getDataRow['lineMeter']=str_replace($badSymbols, "", trim($value['D']));
                    $getDataRow['shipClass']=trim($value['E']);
                    // $getDataRow['portId']=trim($value['F']);    
                    $getDataRow['rowData']=$i2;

                    $myData[]=$getDataRow;
                }
                
                $i2++;
            }
        }
        catch(Exception $e) 
        {
            $emptyExcel[]=1;            
        }


        // Checkin apakah data ada yang kosong
        if(!empty($myData))
        {

            foreach ($myData as  $key=>$value) {

                // checking per data 
                if($value['departDate']=='')
                {
                    $errEmpty[]=1;

                    $errEmptMess[]=" Kolom Tanggal Berangkat di Baris {$value['rowData']} ";
                }
                else
                {
                    if($this->validateDate($value['departDate'],'Y-m-d')==false)
                    {
                        $errDate[]=1;
                        $errDateMess[]=" Kolom Tanggal di Baris {$value['rowData']} ";
                    }
                }
 
                if($value['departTime']=='')
                {
                    $errEmpt[]=1;

                    $errEmptMess[]=" Kolom Tanggal Berangkat di Baris {$value['rowData']} ";
                }
                else
                {
                    if($this->validateDate($value['departTime'],'H:i')==false)
                    {
                        $errTime[]=1;
                        $errTimeMess[]=" Kolom Jam di Baris {$value['rowData']} ";
                    }                    
                }

                if($value['quotaInput']=='')
                {
                    $errEmpt[]=1;

                    $errEmptMess[]=" Kolom Quota di Baris {$value['rowData']} ";
                }
                else
                {
                    // check apakah data yang di input numeric
                    if(!is_numeric($value['quotaInput']))
                    {
                        $errNumericQuota[]=1;
                        $errNumericQuotaMess[]=" Kolom Quota  di Baris {$value['rowData']} ";
                    }
                }

                if($value['lineMeter']=='')
                {
                    $errEmpt[]=1;
                    $errEmptMess[]=" Kolom Line Meter di Baris {$value['rowData']} ";
                }
                else
                {
                                        // check apakah data yang di input numeric
                    if(!is_numeric($value['lineMeter']))
                    {
                        $errNumericLineMeter[]=1;
                        $errNumericLineMeterMess[]=" Kolom Line Meter  di Baris {$value['rowData']} ";

                    }  

                }

                if($value['shipClass']=='')
                {
                    $errEmpt[]=1;

                    $errEmptMess[]=" Kolom Kelas Layanan di Baris {$value['rowData']} ";

                }
                else
                {
                    //get ship class

                    $checkShipClass=$this->pcm->select_data("app.t_mtr_ship_class"," where status=1 and upper(name)=upper('".$value['shipClass']."') " );

                    if($checkShipClass->num_rows()<1)
                    {
                        $errShipClass[]=1;
                        $errShipMess[]=$value['shipClass'];

                        $value['shipClass']="";
                    }
                    else
                    {
                        $value['shipClass']=$checkShipClass->row()->id;   
                    }

                }

                // if($value['portId']=='')
                // {
                //     $errEmpt[]=1;

                //     $errEmptMess[]=" Kolom Pelabuhan di Baris {$value['rowData']} ";
                // }
                // else
                // {
                //     //get ship class

                //     $checkPortId=$this->pcm->select_data("app.t_mtr_port"," where status=1 and upper(name)=upper('".$value['portId']."') " );

                //     if($checkPortId->num_rows()<1)
                //     {
                //         $errPortId[]=1;
                //         $errPortIdMess[]=$value['portId'];
                //         $value['portId']="";
                //     }
                //     else
                //     {
                //         $value['portId']=$checkPortId->row()->id;
                //     }

                // }

                $value['portId']=$portId;

                $checkDuplicateData[]=array(
                    "port_id"=>$value['portId'],
                    "ship_class"=>$value['shipClass'],
                    "depart_date"=>$value['departDate'],
                    "depart_time"=>$value['departTime'],
                ); 

                $getAllData[]=$value;
                                                                                                                                      
            }  

            // print_r($getAllData); exit;

            $errDuplicateData[]=0;

            //check apakah ada sata yang duplicate
            if(count($checkDuplicateData)!=count(array_unique($checkDuplicateData,SORT_REGULAR)))
            {
                $errDuplicateData[]=1;

                $withoutOutDuplicates= array_unique($checkDuplicateData,SORT_REGULAR);

                foreach($checkDuplicateData as $aV){
                    $aTmp1[] = $aV['port_id'].'|'.$aV['ship_class'].'|'.$aV['depart_date'].'|'.$aV['depart_time'];
                }

                foreach($withoutOutDuplicates as $aV){
                    $aTmp2[] = $aV['port_id'].'|'.$aV['ship_class'].'|'.$aV['depart_date'].'|'.$aV['depart_time'];
                }

                $newArray = array_diff_assoc($aTmp1,$aTmp2);
                // UNTUK MENDAPATKAN ROW YANG DUPLICATE
                foreach ($getAllData as $value) {

                    foreach ($newArray as $dataArr) 
                    {
                        if($value['portId'].'|'.$value['shipClass'].'|'.$value['departDate'].'|'.$value['departTime']==$dataArr)
                        {
                            $errDuplicateDataMess[]=$value['rowData'];                            
                        }
                    }
                }

            }
        }
        else
        {
            $emptyRow[]=1;
        }

        // checking jika input data tidak di temukan
        $dataNotFound[]=0;
        $getDataToInsert=array();
        $checkBackDate[]=0;
        foreach ($getAllData as $key => $value) {

            if(!empty($value['departDate']) and !empty($value['departTime']) and !empty($value['shipClass']) and !empty($value['portId']) )
            {

                $where=" where depart_date='".$value['departDate']."'  and depart_time='".$value['departTime']."' and ship_class='".$value['shipClass']."' and port_id='".$value['portId']."' and status=1 ";

                $check=$this->pcm->select_data("app.t_trx_quota_pcm_vehicle", $where);

                if($check->num_rows()<1)
                {
                    $dataNotFound[]=1;
                    $dataNotFoundMess[]=$value['rowData'];
                }
                else
                {
                    $row = array(
                        'id'=>$check->row()->id,
                        'updatedOn'=>date('Y-m-d H:i:s'),
                        'updatedBy'=>$this->session->userdata('username'),
                        'quotaInput'=>$value['quotaInput'],
                        'lineMeter'=>$value['lineMeter']
                    );

                    $getDataToInsert[]=$row;
                }

                // checking jika yang di edit back date maka tidak bisa
                if($value['departDate']." ".$value['departTime']<date('Y-m-d H').":00" )
                {
                    $checkBackDate[]=1;
                    $checkBackDateMess[]=$value['rowData'];
                }
            }
            

        }


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if(array_sum($emptyExcel)>0)
        {
            echo $res=json_api(0, 'Tidak Ada File Excel yang dipilih');
        }
        else if(array_sum($emptyRow)>0)
        {
            echo $res=json_api(0, 'File Excel Kosong');   
        }
        else if(array_sum($errEmpt)>0)
        {
            echo $res=json_api(0, 'Data masih ada yang  kosong di '. implode(",", $errEmptMess));   
        }
        else if(array_sum($errShipClass)>0)
        {
            echo $res=json_api(0, 'Nama Kelas Layanan di '. implode(",", $errShipMess).' Tidak ada');   
        }
        else if(array_sum($errPortId)>0)
        {
            echo $res=json_api(0, 'Nama Pelabuhan di '. implode(",", $errPortIdMess).' Tidak ada');   
        }
        else if(array_sum($dataNotFound)>0)
        {
            echo $res=json_api(0, 'Data Tidak di temukan di baris '. implode(", ", $dataNotFoundMess));   
        }
        else if(array_sum($errDuplicateData)>0)
        {
            echo $res=json_api(0, 'Duplikat data baris '. implode(", ", $errDuplicateDataMess));   
        }                
        else if(array_sum($errNumericQuota)>0)
        {
            echo $res=json_api(0, 'Format Salah di '. implode(",", $errNumericQuotaMess) );   
        }
        else if(array_sum($errNumericLineMeter)>0)
        {
            echo $res=json_api(0, 'Format Salah di '. implode(",", $errNumericLineMeterMess) );   
        }
        else if (array_sum($errDate)) {
            echo $res=json_api(0, 'Format Salah di '. implode(",", $errDateMess) );   
        }
        else if (array_sum($errTime)) {
            echo $res=json_api(0, 'Format Salah di '. implode(",", $errTimeMess) );   
        }
        else if (array_sum($checkBackDate)>0) {
            echo $res=json_api(0, 'Tanggal dan jam Berangkat Tidak boleh kurang dari sekarang di baris '. implode(",", $checkBackDateMess) );   
        }                                                                
        else
        {
            
            $this->dbAction->trans_begin();

            $returnData=$this->pcm->action_edit_import_excel($getDataToInsert,$action,$portId);            

            if ($this->dbAction->trans_status() === FALSE)
            {   
                $this->dbAction->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->dbAction->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data',$returnData);
            }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/pcm_trx_global/action_edit_impor_data';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($getAllData);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }

    public function listErr(){
        validate_ajax();

        $idNotUpdated=$this->session->userdata('notUpdated');

        $implode=implode(",",$idNotUpdated);
        $where=" where a.id in ({$implode})";

        $qry=$this->pcm->qry($where);
        $getData=$this->dbView->query($qry)->result();
        $getListNotUpdated=array();

        foreach ($getData as $key => $value) {
            $getListNotUpdated[]=$value;
        }

        $data['title'] = 'List Data Pcm Global yang tidak terupdate';
        $data['getListNotUpdated']=$getListNotUpdated;


        $this->load->view($this->_module.'/list',$data);
    }


    public function downloadExcel()
    {
        // $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");

        $data = $this->pcm->download();

        // print_r($data); exit;
        $file_name = 'Pcm Kuota Global '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');


            $header = array(
                "NO"=>"string",
                "PELABUHAN"=>"string",
                "KELAS LAYANAN"=>"string",
                "TANGGAL KEBERANGKATAN"=>"string",
                "JAM KEBERANGKATAN"=>"string",
                "QUOTA DIINPUT"=>"string",
                "TOTAL QUOTA TERSEDIA"=>"string",
                "QUOTA YANG DI GUNAKAN"=>"string",
                "QUOTA KHUSUS DI RESERVE"=>"string",
                "TOTAL LINEMETER"=>"string",
                "LINEMETER TERSEDIA"=>"string",
                "LINEMETER DIGUNAKAN"=>"string"
            );

            $no=1;

            foreach ($data as $key => $value) {
                $rows[] = array($no,
                                $value->port_name,
                                $value->ship_class_name,
                                $value->depart_date,
                                $value->depart_time,
                                $value->quota,
                                $value->total_quota,
                                $value->used_quota,
                                $value->quota_reserved,
                                $value->total_lm,
                                $value->lmTersedia,
                                $value->lmDigunakan                                
                            );
                $no++;
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


        $this->dbAction->trans_begin();
        $this->pcm->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->dbAction->trans_status() === FALSE)
        {
            $this->dbAction->trans_rollback();
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
            $this->dbAction->trans_commit();
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
        $logUrl      = site_url().'master_data/pcm/action_change';
        $d[1]==1?$logMethod='ENABLE':$logMethod='DISABLE';
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

        $this->dbAction->trans_begin();
        $this->pcm->update_data($this->_table,$data," id='".$id."'");

        if ($this->dbAction->trans_status() === FALSE)
        {
            $this->dbAction->trans_rollback();
            echo $res=json_api(0, 'Gagal delete data');
        }
        else
        {
            $this->dbAction->trans_commit();
            echo $res=json_api(1, 'Berhasil delete data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/master_pcm/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function checkTime($param="")
    {
        for($i=0;$i<24;$i++)
        {
            strlen($i)<2?$his="0".$i.":00":$his=$i.":00";
            $dataTime[$his]=$his;
        }

        $result=array_search($param,$dataTime);

        return empty($result)?"":$result;
    }

    function validateDate($date, $format)
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }    

}
