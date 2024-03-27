<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Pcm_trx extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_pcm_trx','pcm');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_quota_pcm_vehicle_reserved';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pelabuhan/pcm_trx';

        // $this->dbView = $this->load->database('dbView', TRUE);
        $this->dbView = checkReplication();
        $this->dbAction = $this->load->database('dbAction', TRUE);
	}

     /*
    Document   : Pelabuhan
    Created on : 28 juli, 2023
    Author     : soma
    Description: Enhancement pasca angleb 2023
    */  

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('port')){
                $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
            }
            if($this->input->post('time')){
                $this->form_validation->set_rules('time', 'Jam keberangkatan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid jam keberangkatan'));
            }
            if($this->input->post('shipClass')){
                $this->form_validation->set_rules('shipClass', 'Kelas kapal', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
            }
            if($this->input->post('vehicleClass')){
                $this->form_validation->set_rules('vehicleClass', 'Golongan kendaraan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid golongan kendaraan'));
            }
            if($this->input->post('dateFrom')){
                $this->form_validation->set_rules('dateFrom', 'Tanggal awal', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal awal'));
            }
            if($this->input->post('dateTo')){
                $this->form_validation->set_rules('dateTo', 'Tanggal akhir', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal akhir'));
            }
            
            
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->pcm->dataList();
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


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'PCM Quota Khusus Kendaraan',
            'content'  => 'pcm_trx/index',
            'port'=>$dataPort,
            'time'=>$dataTime,
            'shipClass'=>$dataShipClass,
            'vehicleClass'=>$dataVehicleClass,
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

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
        // $data['time']=$dataTime;
        $data['time']=array(''=>"Pilih");
        $data['shipClass']=$dataShipClass;
        $data['vehicleClass']=$dataVehicleClass;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        $this->form_validation->set_rules('shipClass', 'Kelas layanan ', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas layanan'));
        $this->form_validation->set_rules('vehicleClass', 'Golongan Kendaraan ', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid golongan kendaraan'));
        $this->form_validation->set_rules('time', 'Jam Keberangkatan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid jam keberangkatan'));
        $this->form_validation->set_rules('departDate', 'Tanggal ', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal'));
        $this->form_validation->set_rules('quota', 'Quota ', 'trim|required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!')
                              ->set_message('numeric','%s Harus Berupa angka!');

        
        
        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else 
        {
            $port=$this->enc->decode($this->input->post('port'));
            $shipClass=$this->enc->decode($this->input->post('shipClass'));
            $vehicleClass=$this->enc->decode($this->input->post('vehicleClass'));
            $time=$this->enc->decode($this->input->post('time'));
            $departDate=$this->input->post('departDate');
            $quota=$this->input->post('quota');

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
                // if(!empty($port) and !empty($shipClass))
                // {

                //     $checkQuotaMaster=$this->pcm->select_data("app.t_mtr_quota_pcm_vehicle"," where port_id={$port} and ship_class={$shipClass} and status=1 ");

                //     if($checkQuotaMaster->num_rows()>0)
                //     {
                //         // jika ada master quota yang di temukan
                //         $masterQuota[]=1;
                        
                //         // Jika yang diinput lebih besar dari master quotanya maka ditolak
                //         $quota>$checkQuotaMaster->row()->quota?$checkInput[]=0:$checkInput[]=1;
                //     }
                //     else
                //     {                
                //         $masterQuota[]=0;
                //         $checkInput[]=1;
                //     }
                // }


                // check apakah datanya sudah ada 
                $checkExistData[]=0;
                if(!empty($port) and !empty($shipClass) and !empty($departDate) and !empty($time) and !empty($vehicleClass) )
                {
                    $check=$this->pcm->select_data($this->_table," where status<>'-5' and port_id='{$port}' and ship_class='{$shipClass}' and depart_date='{$departDate}' and depart_time='{$time}' and vehicle_class_id='{$vehicleClass}' ");

                    $check->num_rows()>0?$checkExistData[]=1:$checkExistData[]=0;

                    // GET id from quota pcm vehicle
                    $getQuotaVehicleTrx=$this->pcm->select_data("app.t_trx_quota_pcm_vehicle"," where status=1 and port_id='{$port}' and ship_class='{$shipClass}' and depart_date='{$departDate}' and depart_time='{$time}'");            
                }
                
            if($getQuotaVehicleTrx->num_rows()<1)
                {
                    
                    echo $res=json_api(0,"Quota Global Tidak ada.");
                }
                // else if(array_sum($checkInput)<1)
                // {
                //     echo $res=json_api(0,"Quota yang diinput melebihi Quota Master.");
                // }        
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
                // else if(array_sum($masterQuota)<1)
                // {
                //     echo $res=json_api(0,"Master Quota Tidak ada, silahkan input terlebih dahulu.");   
                // }
                else
                {

                    $this->dbAction->trans_begin();


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
                        // jika input quotanya lebih besar dari global quota trx
                        // $totalQuota=$getQuotaVehicleTrx->row()->total_quota;
                        // $totalQuotaTrx=0;

                        // $param['type']='a';

                        echo $res=json_api(0, 'Quota yang diinput melebihi Quota Global');
                    }
                    else
                    {
                        $totalQuota=$quota;
                        $totalQuotaTrx=$getQuotaVehicleTrx->row()->total_quota-$quota;
                        $param['type']='b';

                        $this->pcm->updateInsert($param);

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

                    // $this->pcm->updateInsert($param);                
                    
                }

                $data=$insertPcmReseved;
                // print_r($data); exit;

        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/pcm_trx/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $idDecode=$this->enc->decode($id);

        $detail=$this->pcm->select_data($this->_table, "where id='{$idDecode}'")->row();
        $port = $this->pcm->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
        $shipClass = $this->pcm->select_data("app.t_mtr_ship_class", "where status=1 order by name asc")->result();
        $vehicleClass = $this->pcm->select_data("app.t_mtr_vehicle_class", "where status=1 order by name asc")->result();

        $dataPort[""]="Pilih";
        $dataShipClass[""]="Pilih";
        $dataTime[""]="Pilih";
        $dataVehicleClass[""]="Pilih";
        
        $selectedPort="";
        $selectedShipClass="";
        $selectedVehicleClass="";
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

        foreach ($vehicleClass as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataVehicleClass[$idEncode]=strtoupper($value->name);

            $value->id==$detail->vehicle_class_id?$selectedVehicleClass=$idEncode:"";
        }                

        $action=array(  
                        ""=>"Pilih",
                        1=>"Tambah (+)",
                        2=>"Kurang (-)");
        // $action=array( 1=>"Tambah (+)");

        $data['title'] = 'Edit PCM Quota Khusus Kendaraan';
        $data['id'] = $id;
        $data['vehicleClass'] = $dataVehicleClass;
        $data['selectedVehicleClass'] = $selectedVehicleClass;
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
       
 
        $this->form_validation->set_rules('id', 'Id tiak Ditemukan ', 'trim|required');
        $this->form_validation->set_rules('quota', 'Quota ', 'trim|required|numeric');
        $this->form_validation->set_rules('action', 'Aksi ', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!')
                              ->set_message('numeric','%s Harus Berupa angka!');


        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else 
        {
            $id=trim($this->enc->decode($this->input->post('id')));
            $quota=trim($this->input->post('quota'));
            $action=trim($this->input->post('action'));

            $data=array(
                'updated_by'=>$this->session->userdata('username'),
                'updated_on'=>date("Y-m-d H:i:s"),
                );
    
            $checkExistData[]=0;
            $checkQuotaTrx[]=0;
            $checkQuotaTrxReseved[]=0;
            $checkQuotaMaster[]=0;
    
            if(!empty($id))
            {
                $dataReserved=$this->pcm->select_data($this->_table," where id='{$id}' ");
    
                // $getQuotaMaster=$this->pcm->select_data("app.t_mtr_quota_pcm_vehicle"," where status=1 and port_id='{$dataReserved->row()->port_id}' and ship_class='{$dataReserved->row()->ship_class}' ")->row();
    
                // $getQuotaMaster<$quota?$checkQuotaMaster[]=1:"";
    
                $getQuotaVehicleTrx=$this->pcm->select_data("app.t_trx_quota_pcm_vehicle"," where status=1 and port_id='{$dataReserved->row()->port_id}' and ship_class='{$dataReserved->row()->ship_class}' and depart_date='{$dataReserved->row()->depart_date}' and depart_time='{$dataReserved->row()->depart_time}'");
    
                if($getQuotaVehicleTrx->num_rows()>0)
                {
                    // jika actionya tambah
                    if($action==1)
                    {
                        // jika total quota  i t_trx_quota_pcm <1 maka akan di ntolak 
                        if($getQuotaVehicleTrx->row()->total_quota<1)
                        {
                            $checkQuotaTrx[]=1;
                        }                    
                    }
                    else
                    {
                        // jika total quota  i t_trx_quota_pcm_reserved <= 0 maka akan di ntolak 
                        if(($dataReserved->row()->total_quota-$quota)<0)
                        {
                            $checkQuotaTrxReseved[]=1;
                        }   
                    }
    
                }
    
                // check apakah data ini sudah ada, kondisi jika diperbolehkan bisa ganti golongan
                $check=$this->pcm->select_data($this->_table," where status<>'-5' and port_id='{$dataReserved->row()->port_id}' and ship_class='{$dataReserved->row()->ship_class}' and depart_date='{$dataReserved->row()->depart_date}' and depart_time='{$dataReserved->row()->depart_time}' and vehicle_class_id='{$dataReserved->row()->vehicle_class_id}' and id <>{$id} ");
    
                $check->num_rows()>0?$checkExistData[]=1:$checkExistData[]=0;     
    
                $data['vehcile_class_id']=$dataReserved->row()->vehicle_class_id;
                $data['quota']=$quota;                       
                
            }                              
        
    
                $data=array("id"=>$id,
                        "quota"=>$quota,
                        "action"=>$action,
                        "updated_on"=>date("Y-m-d H:i:s"),
                        "updated_by"=>$this->session->userdata("username")
                        );
            
                if(array_sum($checkExistData)>0)
                {
                    echo $res=json_api(0,"Data sudah ada");
                }
                // else if(array_sum($checkQuotaMaster)>0)
                // {
                //     echo $res=json_api(0,"Quota yang diinput lebih besar dari quota master");   
                // }
                else if(array_sum($checkQuotaTrxReseved)>0)
                {
                    echo $res=json_api(0,"estimasi tidak boleh minus");
                }
                else if(array_sum($checkQuotaTrx)>0)
                {
                    echo $res=json_api(0,"Quota Global Transaksi kosong");   
                }
                else if($getQuotaVehicleTrx->num_rows()<1)
                {
                    echo $res=json_api(0,"Data global tidak di temukan");   
                }        
                else
                {
                
                            // print_r($data); exit;
                            $this->dbAction->trans_begin();
                
                            if($action==1) //aksi tambah
                            {                
                                // jika diinput lebih kecil dari trx global
                                if($getQuotaVehicleTrx->row()->total_quota<$quota)
                                {
                
                                    // $dataUpdate=array(
                                    //                 'quotaInput'=>$quota,
                                    //                 'updated_on'=>date("Y-m-d H:i:s"),
                                    //                 'updated_by'=>$this->session->userdata("username"),
                                    //                 'type'=>'a',
                                    //                 'id_pcm'=>$getQuotaVehicleTrx->row()->id,
                                    //                 'id_pcm_reserved'=>$id,
                                    //                 'portId'=>$getQuotaVehicleTrx->row()->port_id
                                    //                 );
                                    echo $res=json_api(0, 'Total Quota Global Minus');
                                        
                                }
                                else
                                {
                                    $dataUpdate=array(
                                                    'quotaInput'=>$quota,
                                                    'updated_on'=>date("Y-m-d H:i:s"),
                                                    'updated_by'=>$this->session->userdata("username"),
                                                    'type'=>'b',
                                                    'id_pcm'=>$getQuotaVehicleTrx->row()->id,
                                                    'id_pcm_reserved'=>$id,
                                                    'portId'=>$getQuotaVehicleTrx->row()->port_id                                        
                                                    );
                
                                    $this->pcm->updateUpdatePlus($dataUpdate);
                
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
                
                            }
                            else
                            {
                
                                $dataUpdate=array(
                                                'quotaInput'=>$quota,
                                                'updated_on'=>date("Y-m-d H:i:s"),
                                                'updated_by'=>$this->session->userdata("username"),
                                                'id_pcm'=>$getQuotaVehicleTrx->row()->id,
                                                'id_pcm_reserved'=>$id,
                                                'portId'=>$getQuotaVehicleTrx->row()->port_id                                        
                                                );
                
                            $this->pcm->updateUpdateMin($dataUpdate);
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
                
                            // // $this->pcm->update_data($this->_table,$data,"id=$id");
                
                }

        }

         /* Fungsi Create Log */
         $createdBy   = $this->session->userdata('username');
         $logUrl      = site_url().'pelabuhan/pcm_trx/action_edit';
         $logMethod   = 'EDIT';
         $logParam    = json_encode($data);
         $logResponse = $res;
                         
         $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $data = [];
        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal update data');            
        }
        else{
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
        $id = $this->enc->decode($id);

        $data = [];
        if(!$id || empty($id)){
            echo $res=json_api(0, 'Gagal delete data');            
        }
        else{

            $data=array(
                'status'=>-5,
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
                );


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

    function getTime(){

        $datePick=trim($this->input->post("datePick"));

        $nowDate=date('Y-m-d H');
        $returnData=array();

        for($i=0;$i<24;$i++)
        {
            strlen($i)<2?$his="0".$i.":00":$his=$i.":00";

            // checking date apakah harinya yang di pilih saat ini_alter
            if($datePick." ".$his<$nowDate.":00")
            {
                $dataTime['statusData']='disabled';
            }
            else
            {
                $dataTime['statusData']='enabled';
            }

            $dataTime['valData']=$his;
            $dataTime['idData']=$this->enc->encode($his);
            $dataTime["tokenHash"] = $this->security->get_csrf_hash();



            $returnData[]=$dataTime;
        } 

        echo json_encode($returnData);       
    }   
    
     /*
    Document   : Pelabuhan
    Created on : 28 juli, 2023
    Author     : soma
    Description: end Enhancement pasca angleb 2023
    */  


}