<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class PembatasanQuota extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('PembatasanQuotaModel','pembatasan');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('Html2pdf');

        $this->_table    = 'app.t_mtr_quota_pcm_vehicle_restrictions';
        $this->_table_detail    = 'app.t_mtr_quota_pcm_vehicle_restrictions_detail';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data2/pembatasanQuota';

        $this->dbView=checkReplication();
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->pembatasan->dataList();
            echo json_encode($rows);
            exit;
        }

        $btnExcel = '<button class="btn btn-sm btn-warning download" id="download_excel">Excel</button>';
        $excel = generate_button($this->_module, "download_excel", $btnExcel);

        $btnPdf = '<a class="btn btn-sm btn-warning download" id="download_pdf" target="_blank" href="#" >Pdf</a>';
        $pdf = generate_button($this->_module, "download_excel", $btnPdf);

        $getVehicleClass['']="Pilih";
        foreach ($this->pembatasan->getVehicleClass() as $key => $value) {
            $getVehicleClass[$this->enc->encode($value->id)]=$value->name;
        }
        $getShipClass['']="Pilih";
        foreach ($this->pembatasan->getShipClass() as $key => $value) {
            $getShipClass[$this->enc->encode($value->id)]=$value->name;
        }

        $getPort['']="Pilih";
        foreach ($this->pembatasan->getPort() as $key => $value) {
            $getPort[$this->enc->encode($value->id)]=$value->name;
        }
        
        
        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Pembatasan Quota Restrict',
            'content'  => 'pembatasanQuota/index',
            'btn_add'  => generate_button_new($this->_module, 'add', site_url($this->_module.'/add')),
            'btn_excel' =>$excel,
            'btn_pdf' =>$pdf,
            'port'=>$getPort,
            'shipClass'=>$getShipClass,
            'vehicleClassId'=>$getVehicleClass,
        );

		$this->load->view('default', $data);
	}

    public function pembatasanQuotaDetail()
    {
        if($this->input->is_ajax_request()){
            $rows = $this->pembatasan->pembatasanQuotaDetail();
            echo json_encode($rows);
            exit;
        }
    }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $getPort =$this->pembatasan->select_data("app.t_mtr_port"," where status=1 order by name asc");
        $dataPort[""]="Pilih";
        foreach ($getPort->result() as $key => $value) {
            $dataPort[$this->enc->encode($value->id)] = $value->name;
        }

        $getShipClass =$this->pembatasan->select_data("app.t_mtr_ship_class"," where status=1 order by name asc");
        $dataShipClass[""]="Pilih";
        foreach ($getShipClass->result() as $key => $value) {
            $dataShipClass[$this->enc->encode($value->id)] = $value->name;
        }

        $getVehicleClass =$this->pembatasan->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc");
        $dataVehicleClass=array();
        foreach ($getVehicleClass->result() as $key => $value) {
            $dataVehicleClass[$this->enc->encode($value->id)] = $value->name;
        }

        $jam=array();
        for ($i=0; $i <24 ; $i++) { 
            $value = sprintf("%02s", $i).":00";
            $jam []= $value;
        }

        $data['title'] = 'Tambah Pembatasan Quota Restrict';
        $data['port'] = $dataPort;
        $data['vehicleClass'] = $dataVehicleClass;
        $data['shipClass'] = $dataShipClass;
        $data['jam'] = $jam;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // print_r($this->input->post()); exit;
        $port=trim($this->enc->decode($this->input->post('port')));
        $shipClass=trim($this->enc->decode($this->input->post('shipClass')));
        $dateFrom=trim($this->input->post('dateFrom'));
        $dateTo=trim($this->input->post('dateTo'));
        $quota=trim($this->input->post('quota'));
        $vehicleClass=trim($this->input->post('vehicleClass2'));
        $lineMeter=trim($this->input->post('lineMeter'));
        $jam=trim($this->input->post('jam2'));
        // $transfer_fee=trim($this->input->post('transfer_fee'));


        $this->form_validation->set_rules('port'," Pelabuhan",'required');
        $this->form_validation->set_rules('dateFrom'," Tanggal Mulai ",'required');
        $this->form_validation->set_rules('dateTo'," Tanggal Akhir",'required');
        $this->form_validation->set_rules('quota'," Quota ",'required|numeric');
        $this->form_validation->set_rules('vehicleClass'," Golongan ",'required');
        $this->form_validation->set_rules('lineMeter'," Line Meter",'required|numeric');
        $this->form_validation->set_rules('jam'," Jam ",'required');

        $this->form_validation->set_message('required','%s harus diisi!')
                              ->set_message('numeric','%s Harus Berupa angka!');


        // data header app.t_mtr_quota_pcm_vehicle_restrictions
        $dataBatchHeader=array();        
        $dataHeader['port_id']= $port;
        $dataHeader['ship_class']= $shipClass;
        // $dataHeader['vehicle_class_id']= $vehicleClass;
        $dataHeader['start_date']= $dateFrom." 00:00";
        $dataHeader['end_date']= $dateTo." 23:59";
        $dataHeader['quota']= $quota;
        $dataHeader['total_lm']= $lineMeter;
        $dataHeader['status']= 1;
        $dataHeader['created_on']= date('Y-m-d H:i:s');
        $dataHeader['created_by']= $this->session->userdata('username');

        $whereJam=array();
        foreach (explode(",",$jam) as $key => $value) {
            $whereJam[]="'".$value."'";
        }
        
        $dataBatchDetail=array();
        $vehicleClassExplode= explode(",",$vehicleClass);
        
        $dataRange = $this->dateRange($dateFrom, $dateTo);
        
        $dataBatchTrx=array();
        
        $getCode=$this->createCode($port);
        $total_length =strlen($getCode)-4;
        $noUrut = (int) substr($getCode, $total_length, 4);  
        $char = substr($getCode,0,$total_length);
        
        $vehicleClassParam=array();
        foreach ($vehicleClassExplode as $valueVehicleClassExplode) {
            $restrictionCode = $char . sprintf("%04s", $noUrut);
            
            $vehicleClassId= $this->enc->decode($valueVehicleClassExplode);
            $vehicleClassParam[]=$vehicleClassId;
            foreach (explode(",",$jam) as $valueJamExplode) {
                
                // data app.t_mtr_quota_pcm_vehicle_restrictions_detail
                $dataDetail['restriction_quota_code']= $restrictionCode;
                $dataDetail['depart_time']=$valueJamExplode ;
                $dataDetail['status']= 1;
                $dataDetail['created_on']= date('Y-m-d H:i:s');
                $dataDetail['created_by']= $this->session->userdata('username');
                $dataBatchDetail[]=$dataDetail;
                
                /* // trx di insert di API sebagai gantinya tricki di tampiloan kuota knd global
                foreach ($dataRange as $valueDataRange) {
                    // data transaksi app.t_trx_quota_pcm_vehicle_restrictions 
                    $dataTrx = array( 'restriction_quota_code'=> $restrictionCode,
                    'port_id'=> $port,
                    'ship_class'=> $shipClass,
                    'vehicle_class_id'=> $vehicleClassId,
                    'depart_date'=> $valueDataRange,
                    'depart_time'=> $valueJamExplode,
                    'quota'=> $quota,
                    'total_lm'=> $lineMeter,
                    'status'=> 1,
                    'created_on'=> date('Y-m-d H:i:s'),
                    'created_by'=> $this->session->userdata('username'));

                    $dataBatchTrx[] = $dataTrx;
                
                }
                */
            }
            $dataHeader['restriction_quota_code']= $restrictionCode;
            $dataHeader['vehicle_class_id']= $vehicleClassId;
            $dataBatchHeader[]=$dataHeader;

            $noUrut++;
        }

        // checking overlap
        $checkOverlaps = $this->pembatasan->checkOverlaps($dateFrom,$dateTo,$port,$shipClass,implode(",",$vehicleClassParam), implode(",",$whereJam));
        $checkQuotaGlobal=$this->pembatasan->checkQuotaGlobal($dateFrom, $dateTo, $port, $shipClass);

        // print_r($checkOverlaps); exit;
        // print_r($dataBatchHeader); exit;
        // print_r($dataBatchDetail); exit;
        // print_r($dataBatchTrx); exit;

        // checkin data quota dan lime meter berdasarkan master data yang berlaku
        $errQuota[]=0;
        $errQuotaMessege=array();
        $errLm[]=0;
        $errLmMessege=array();
        if($checkQuotaGlobal)
        {
            foreach ($checkQuotaGlobal as $key => $value) {
                if($quota > $value->quota)
                {
                    $errQuota[]=1;
                    $errQuotaMessege[]=$value->depart_date;
                }
                else if($lineMeter > $value->total_lm)
                {
                    $errLm[]=1;
                    $errLmMessege[]=$value->depart_date;
                }
                // echo $quota."|".$value->quota."<br>";
                // echo $lineMeter."|".$value->total_lm."<br>";
            }
        }
        // print_r($errLm);
        // print_r($errQuota);
        // exit;

        $data=array();
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if(count((array)$checkOverlaps)>0)
        {
            $restrictionQuotaCode=array();
            $restrictionDepartDate=array();
            $restrictionDepartTime=array();
            foreach ($checkOverlaps as $key => $value) {
                $restrictionQuotaCode[]= $value->restriction_quota_code;
                $restrictionDepartDate[]= date("Y-m-d",strtotime($value->start_date))." s/d ".date("Y-m-d",strtotime($value->end_date))." Jam : ".$value->depart_time;
                $restrictionDepartTime[]= $value->depart_time;
            }
            $implodeRestrictionQuotaCode=implode(", ",$restrictionQuotaCode);
            $implodeRestrictionDepartDate=implode(", ",$restrictionDepartDate);
            $implodeRestrictionDepartTime=implode(", ",$restrictionDepartTime);

            echo $res=json_api(0,"Data Tidak Boleh Overlaps pada tanggal dan  jam yang sama dengan restriction Tanggal Berlaku dan Akhir Berlaku ".$implodeRestrictionDepartDate);
        }
        else if(array_sum($errQuota)>0)
        {
            echo $res=json_api(0,"Input Quota tidak boleh lebih besar dari master quota tanggal berlaku : ".implode(", ",$errQuotaMessege));
        }
        else if(array_sum($errLm)>0)
        {
            echo $res=json_api(0,"Input Line Meter tidak boleh lebih besar dari master quota PCM tanggal berlaku : ".implode(", ",$errLmMessege));
        }
        // else if($dateFrom > date("Y-m-d") && $dateTo < date("Y-m-d") )
        // {
        //     echo $res=json_api(0,"Awal berlaku atau akhir berlaku tidak boleh lewat dari tanggal sekarang" );
        // }
        else if($dateFrom > $dateTo)
        {
            echo $res=json_api(0,"Jam akhir berlaku tidak boleh lebih besar dari awal berlaku" );
        }
        else
        {

            // echo "berhasil";
            // exit;
            $this->db->trans_begin();

            $this->pembatasan->insert_data_batch("app.t_mtr_quota_pcm_vehicle_restrictions",$dataBatchHeader);
            $this->pembatasan->insert_data_batch("app.t_mtr_quota_pcm_vehicle_restrictions_detail",$dataBatchDetail);

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
        $logUrl      = site_url().'master_data2/pembatasan_quota/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("dateFrom");
        $dateTo = $this->input->get("dateTo");

        $data = $this->pembatasan->download();

        $file_name = 'Pembatasan Kuota Restrict Tanggal Berlaku dan Akhir Berlaku' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $header = array(
            'NO' => 'string',
            'PELABUHAN' => 'string',
            'LAYANAN' => 'string',
            'JENIS PJ' => 'string',
            'GOLONGAN' => 'string',
            'BATAS QUOTA' => 'string',
            'BATAS LINEMETER' => 'string',
            'TANGGAL BERLAKU' => 'string',
            'AKHIR BERLAKU' => 'string',
            'JAM' => 'string',
            'STATUS' => 'string',
        );



        $no = 1;
        foreach ($data as $key => $value) {
            

            $rows[] = array(
                $no,
                $value->port_name,
                $value->ship_class_name,
                $value->jenis_pj,
                $value->golongan,
                $value->quota,
                $value->total_lm,
                $value->start_date,
                $value->end_date,
                $value->depart_time,
                $value->status,
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
    public function download_pdf()
    {
 
        $getData = $this->pembatasan->download();
        $data["data"]=$getData;
        $data["dateFrom"]=trim($this->input->get('dateFrom'));
        $data["dateTo"]=trim($this->input->get('dateTo'));

        // print_r($data['data']); exit;
        $this->load->view($this->_module . '/pdf', $data);

    }
    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);
        $detail = $this->pembatasan->select_data($this->_table,"where id=$id_decode")->row();

        $getPort =$this->pembatasan->select_data("app.t_mtr_port"," where status=1 order by name asc");
        $dataPort[""]="Pilih";
        $selectedDataPort = "";
        foreach ($getPort->result() as $key => $value) {

            $valueKey = $this->enc->encode($value->id);
            if($value->id == $detail->port_id)
            {
                $selectedDataPort = $valueKey;
            }
            $dataPort[$valueKey] = $value->name;
        }

        $getShipClass =$this->pembatasan->select_data("app.t_mtr_ship_class"," where status=1 order by name asc");
        $dataShipClass[""]="Pilih";
        $selectedDataShipClass="";
        foreach ($getShipClass->result() as $key => $value) {
            $valueKey = $this->enc->encode($value->id);
            if($value->id == $detail->ship_class)
            {
                $selectedDataShipClass = $valueKey;
            }
            $dataShipClass[$valueKey] = $value->name;
        }

        $getVehicleClass =$this->pembatasan->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc");
        $dataVehicleClass['']="Pilih";
        $selectedDataVehicleClass="";
        foreach ($getVehicleClass->result() as $key => $value) {
            $valueKey = $this->enc->encode($value->id);
            if($value->id == $detail->vehicle_class_id)
            {
                $selectedDataVehicleClass = $valueKey;
            }
            $dataVehicleClass[$valueKey] = $value->name;
        }

        $jam=array();
        for ($i=0; $i <24 ; $i++) {
            $value = sprintf("%02s", $i).":00";
            $jam []= $value;
        }

        $detailJam = $this->pembatasan->select_data(" app.t_mtr_quota_pcm_vehicle_restrictions_detail "," where restriction_quota_code='{$detail->restriction_quota_code}' and status=1 ")->result();

        $getJam=array();
        foreach ($detailJam as $key => $value) {
            $getJam[]=date("H:i",strtotime($value->depart_time));
        }
        $selectedJam = implode(",",$getJam);

        $action=array(""=>"Pilih",1=>"Tambah",2=>"Kurang");        

        $data['title'] = 'Edit Pembatasan Quota Restrict';
        $data['id'] = $id;
        $data['port'] = $dataPort;
        $data['selectedPort'] = $selectedDataPort;
        $data['vehicleClass'] = $dataVehicleClass;
        $data['selectedVehicleClass'] = $selectedDataVehicleClass;
        $data['shipClass'] = $dataShipClass;
        $data['selectedShipClass'] = $selectedDataShipClass;
        $data['jam'] = $jam;
        $data['detailJam'] = $detailJam;
        $data['selectedJam'] = $selectedJam;
        $data['action'] = $action;
        $data['detail']=$detail;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id'));
        $quota=trim($this->input->post('quota'));
        $restrictionQuotaCode = $this->enc->decode($this->input->post('restrictionQuotaCode'));
        $action = trim($this->input->post('actions'));
        // $shipClass=trim($this->enc->decode($this->input->post('shipClass')));
        // $dateFrom=trim($this->input->post('dateFrom'));
        $dateTo=trim($this->input->post('dateTo'));
        // $vehicleClass=trim($this->input->post('vehicleClass2'));
        $lineMeter=trim($this->input->post('lineMeter'));
        $jam=trim($this->input->post('jam2'));
        // $jamLama=trim($this->input->post('jamLama'));


        // $transfer_fee=trim($this->input->post('transfer_fee'));

        $this->form_validation->set_rules('id'," Id",'required');
        $this->form_validation->set_rules('restrictionQuotaCode'," Kode Restriction",'required');
        // $this->form_validation->set_rules('port'," Pelabuhan",'required');
        // $this->form_validation->set_rules('dateFrom'," Tanggal Mulai ",'required');
        $this->form_validation->set_rules('dateTo'," Tanggal Akhir",'required');
        // $this->form_validation->set_rules('vehicleClass'," Golongan ",'required');
        $this->form_validation->set_rules('lineMeter'," Line Meter",'required|numeric');
        $this->form_validation->set_rules('jam2'," Jam ",'required');

        $this->form_validation->set_rules('quota'," Quota ",'required|numeric');
        $this->form_validation->set_rules('actions'," Aksi ",'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!')
                              ->set_message('numeric','%s Harus Berupa angka!');
                        
        $checkData =  $this->pembatasan->select_data("app.t_mtr_quota_pcm_vehicle_restrictions"," where id='{$id}' ")->row();

        $getDataDetail = $this->pembatasan->select_data("app.t_mtr_quota_pcm_vehicle_restrictions_detail"," where restriction_quota_code='{$checkData->restriction_quota_code}' ")->result();
        
        // mencari id yang tidak di update
        $timeWithoutUpdate=array();
        $whereTimeWitoutUpdate=array();
        $explodeJam=explode(",",$jam);
        foreach ($getDataDetail as $key => $value) {
            foreach ( $explodeJam as $key2 => $value2 ) {
                if(date("H:i",strtotime($value->depart_time)) == $value2)
                {
                    $timeWithoutUpdate[]=$value2;
                    $whereTimeWitoutUpdate[]="'".$value->depart_time."'";
                }
            }
        }

        
        if($action==1)
        {
            $getQuota= $checkData->quota + $quota;
        }
        else
        {
            $getQuota= $checkData->quota - $quota;
        }


        $whereJam=array();
        foreach (explode(",",$jam) as $key => $value) {
            $whereJam[]="'".$value."'";
        }

        // checking overlap
        $checkOverlaps = $this->pembatasan->checkOverlaps($checkData->start_date,$dateTo,$checkData->port_id,$checkData->ship_class,$checkData->vehicle_class_id, implode(",",$whereJam),$checkData->restriction_quota_code);

        $checkQuotaGlobal=$this->pembatasan->checkQuotaGlobal($checkData->start_date, $dateTo, $checkData->port_id, $checkData->ship_class);

        $errQuota[]=0;
        $errQuotaMessege=array();
        $errLm[]=0;
        $errLmMessege=array();
        if($checkQuotaGlobal)
        {
            foreach ($checkQuotaGlobal as $key => $value) {
                if($getQuota > $value->quota)
                {
                    $errQuota[]=1;
                    $errQuotaMessege[]=$value->depart_date;
                }
                else if($lineMeter > $value->total_lm)
                {
                    $errLm[]=1;
                    $errLmMessege[]=$value->depart_date;
                }
                // echo $quota."|".$value->quota."<br>";
                // echo $lineMeter."|".$value->total_lm."<br>";
            }
        }

        $data=array(
            "quota"=>$getQuota,
            "total_lm"=>$lineMeter,
            "end_date"=>$dateTo,
            "updated_on"=>date("Y-m-d H:i:s"),
            "updated_by"=>$this->session->userdata("username")
        );

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        if($getQuota<0)
        {
            echo $res=json_api(0, "Quota Tidak Boleh Min ");
        }
        else if(array_sum($errQuota)>0)
        {
            echo $res=json_api(0,"Input Quota tidak boleh lebih besar dari master quota tanggal berlaku : ".implode(", ",$errQuotaMessege));
        }
        else if(array_sum($errLm)>0)
        {
            echo $res=json_api(0,"Input Line Meter tidak boleh lebih besar dari master quota PCM tanggal berlaku : ".implode(", ",$errLmMessege));
        }        
        else if(count((array)$checkOverlaps)>0)
        {
            $restrictionQuotaCode=array();
            $restrictionDepartDate=array();
            $restrictionDepartTime=array();
            foreach ($checkOverlaps as $key => $value) {
                $restrictionQuotaCode[]= $value->restriction_quota_code;
                $restrictionDepartDate[]= date("Y-m-d",strtotime($value->start_date))." s/d ".date("Y-m-d",strtotime($value->end_date))." Jam : ".$value->depart_time;
                $restrictionDepartTime[]= $value->depart_time;
            }
            $implodeRestrictionQuotaCode=implode(", ",$restrictionQuotaCode);
            $implodeRestrictionDepartDate=implode(", ",$restrictionDepartDate);
            $implodeRestrictionDepartTime=implode(", ",$restrictionDepartTime);

            echo $res=json_api(0,"Data Tidak Boleh Overlaps pada tanggal dan  jam yang sama dengan restriction Tanggal Berlaku dan Akhir Berlaku ".$implodeRestrictionDepartDate);
        }        
        else if($lineMeter<0)
        {
            echo $res=json_api(0, "Line Meter Tidak Boleh Min ");
        }        
        else
        {

            $this->db->trans_begin();
            // update header
            $this->pembatasan->update_data('app.t_mtr_quota_pcm_vehicle_restrictions',$data," id={$id} ");

            $param=array(
                "quota"=>$quota,
                "action"=>$action,
                "total_lm"=>$lineMeter,
                "restrictionQuotaCode"=>$restrictionQuotaCode,
                "updated_on"=>date("Y-m-d H:i:s"),
                "updated_by"=>$this->session->userdata("username")
            );
            // update trx yang sudah ke generate
            $this->pembatasan->insertEdit($param);

            // update data detail master quota restriction
            $whereUpdate="   status <> '-5'
                            and restriction_quota_code='{$checkData->restriction_quota_code}' ";
            if(!empty($timeWithoutUpdate))
            {
                $whereUpdate .=" and depart_time not in (".implode(", ",$whereTimeWitoutUpdate)." ) ";
            }

            $updateDataDetail=array(
                "updated_on"=>date("Y-m-d H:i:s"),
                "updated_by"=>$this->session->userdata("username"),
                "status"=>'-5'
            );

            // update di detail quota restrict
            $this->pembatasan->update_data("app.t_mtr_quota_pcm_vehicle_restrictions_detail",$updateDataDetail,$whereUpdate);


            // update di trx  restrict
            $this->pembatasan->update_data("app.t_trx_quota_pcm_vehicle_restrictions",$updateDataDetail,$whereUpdate);

            // dapetin jam  data yang di insert 
            $array_diff = array_diff($explodeJam,$timeWithoutUpdate);

            // insert data jika ada data di master restriction detail yang baru
            if($array_diff)
            {
                $insertDataDetail=array();
                foreach ($array_diff as $key => $value) {
                    
                    $insertDataDetail[]=array(
                        "restriction_quota_code"=>$checkData->restriction_quota_code,
                        "depart_time"=>$value,
                        "created_on"=>date("Y-m-d H:i:s"),
                        "created_by"=>$this->session->userdata("username"),
                        "status"=>1
                    );
                }
                $this->pembatasan->insert_data_batch("app.t_mtr_quota_pcm_vehicle_restrictions_detail",$insertDataDetail);
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
        $logUrl      = site_url().'master_data2/pembatasanQuota/action_edit';
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

        $isAktive =0; 
        if($d[1]==1) // jika dia ingin aktifkan data maka akan di check apakah ada data yang sudah di set
        {
            $checkDataMaster =$this->pembatasan->select_data("app.t_mtr_quota_pcm_vehicle_restrictions"," where id=".$d[0])->row();
            $checkDataMasterDetail = $this->pembatasan->select_data("app.t_mtr_quota_pcm_vehicle_restrictions_detail"," where restriction_quota_code='$checkDataMaster->restriction_quota_code' ")->result();

            $whereJam=array();
            foreach ($checkDataMasterDetail as $key => $value) {
                $whereJam[]="'".$value->depart_time."'";
            }

            $checkOverlaps = $this->pembatasan->checkOverlaps($checkDataMaster->start_date,$checkDataMaster->end_date,$checkDataMaster->port_id,$checkDataMaster->ship_class,$checkDataMaster->vehicle_class_id, implode(",",$whereJam));

            $isAktive = count((array)$checkOverlaps)>0?1:0;
        }

        if($isAktive>0)
        {
            $restrictionQuotaCode=array();
            $restrictionDepartDate=array();
            $restrictionDepartTime=array();
            foreach ($checkOverlaps as $key => $value) {
                $restrictionQuotaCode[]= $value->restriction_quota_code;
                $restrictionDepartDate[]= date("Y-m-d",strtotime($value->start_date))." s/d ".date("Y-m-d",strtotime($value->end_date))." Jam : ".$value->depart_time;
                $restrictionDepartTime[]= $value->depart_time;
            }
            $implodeRestrictionQuotaCode=implode(", ",$restrictionQuotaCode);
            $implodeRestrictionDepartDate=implode(", ",$restrictionDepartDate);
            $implodeRestrictionDepartTime=implode(", ",$restrictionDepartTime);

            echo $res=json_api(0,"Ada Data yang masih aktif Tanggal Berlaku dan Akhir Berlaku ".$implodeRestrictionDepartDate);
        }
        else
        {
            $this->db->trans_begin();
            $this->pembatasan->update_data($this->_table,$data,"id=".$d[0]);
        
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
        $logUrl      = site_url().'master_data2/pembatasanQuota/action_change';
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

        $this->db->trans_begin();
        $this->pembatasan->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'master_data2/pembatasanQuota/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
    function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
        $dates = [];
        $current = strtotime( $first );
        $last = strtotime( $last );
    
        while( $current <= $last ) {
    
            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }
    
        return $dates;
    }    
    function createCode($portId)
    {
        $front_code="RQ".$portId.date('ymd'); // RQ restrict quota

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select restriction_quota_code from app.t_mtr_quota_pcm_vehicle_restrictions where left(restriction_quota_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $kode=$front_code."0001";
            return $kode;
        }
        else
        {
            $max=$this->db->query("select max (restriction_quota_code) as max_code from app.t_mtr_quota_pcm_vehicle_restrictions where left(restriction_quota_code,".$total_length.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }
}
