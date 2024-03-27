<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class PembatasanTransaksi extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('PembatasanTransaksiModel','pembatasantransaksi');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->load->library('Html2pdf');

        $this->_table    = 'app.t_mtr_limit_certain_group_transaction';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data2/pembatasanTransaksi';
        $this->dbView=checkReplication();
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->pembatasantransaksi->dataList();
            echo json_encode($rows);
            exit;
        }

        $linkAdd=site_url($this->_module.'/add');
        $btnAdd = '<button onclick=showModal("'.$linkAdd.'") class="btn btn-sm btn-warning" title="Tambah" id="btnTmbh" ><i class="fa fa-plus"></i> Tambah</button>';

        $btnExcel = '<button class="btn btn-sm btn-warning download" id="download_excel">Excel</button>';
        $excel = generate_button($this->_module, "download_excel", $btnExcel);

        $btnPdf = '<a class="btn btn-sm btn-warning download" id="download_pdf" target="_blank" href="#" >Pdf</a>';
        $pdf = generate_button($this->_module, "download_excel", $btnPdf);

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Pembatasan Transaksi',
            'content'  => 'pembatasanTransaksi/index',
            'btn_excel'=>$excel,
            'btn_pdf'  =>$pdf,
            'btn_add'  =>generate_button($this->_module, 'add',$btnAdd),
            'btn_add_vehicle'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );
		$this->load->view('default', $data);
	
    }

    public function getDetailTransaksi(){   
        checkUrlAccess($this->_module,'view');

        if($this->input->is_ajax_request()){
            $rows = $this->pembatasantransaksi->getDetailTransaksi();
            echo json_encode($rows);
            exit;
        }
    
    }    
    
    public function getDetailTransaksiExcept(){   
        checkUrlAccess($this->_module,'view');

        if($this->input->is_ajax_request()){
            $rows = $this->pembatasantransaksi->getDetailTransaksiExcept();
            echo json_encode($rows);
            exit;
        }
    } 

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $limitType=array(
                            ""=>'Pilih',
                            $this->enc->encode(1)=>'Per Jam',
                            $this->enc->encode(2)=>'Per Hari',
                            $this->enc->encode(3)=>'Per Bulan',
                            $this->enc->encode(4)=>'Per Tahun',
    
                        );
        
        $getGolongan=$this->pembatasantransaksi->select_data('app.t_mtr_vehicle_class', "where status !='-5' order by name asc");

        foreach ($getGolongan->result() as $key => $value) {
            $golongan[$value->id]=$value->name;
        }


        $data['title'] = 'Tambah Pembatasan Transaksi';
        $data['limitType'] = $limitType;
        $data['golongan'] = $golongan;
        $this->load->view($this->_module.'/add',$data);
    }

    public function getUser()
    {
        if($this->input->is_ajax_request()){
            $rows = $this->pembatasantransaksi->getUser();
            echo json_encode($rows);
            exit;
        }
    }

    public function getUserExcept()
    {
        if($this->input->is_ajax_request()){
            $rows = $this->pembatasantransaksi->getUserExcept();
            echo json_encode($rows);
            exit;
        }
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $startDate=$this->input->post('startDate');
        $endDate=$this->input->post('endDate');
        $totalTrx=$this->input->post('value');
        $limitType=$this->enc->decode($this->input->post('limitType'));
        $isCustom=$this->input->post('isCustom');
        $customValue=$this->input->post('customValue');
        $idData=$this->input->post('idData');
        $golongan = explode("," ,$this->input->post('valGolongan'));
        $pejalanKaki = $this->input->post('pejalanKaki');
        $kendaraan=$this->input->post('kendaraan');
        // yang di ambil hanya user yang except untu pencarian data not in 
        $idQuotaExcept=$this->input->post('idQuotaExcept[]');
        
        $this->form_validation->set_rules('startDate', 'Tanggal Awal ', 'required');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir ', 'required');
        $this->form_validation->set_rules('value', 'Nominal Pembatasan Transaksi ', 'required|numeric');
        $this->form_validation->set_rules('limitType', 'Tipe Pembatasan ', 'required');

        if(!empty($isCustom))
        {
            $this->form_validation->set_rules('customValue', 'Custom Nominal Jenis Pembatasan ', 'required|numeric');
        }

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');

        $createCode=$this->createCode();

        $total_length =strlen($createCode)-4;
        $no = (int) substr($createCode, $total_length, 4);  
        $char = substr($createCode,0,$total_length);

        $getEmail=array();  
        $where =" ";
        if(!empty($idQuotaExcept))
        {
            $getIdUserEsxcept=array();
            foreach ($idQuotaExcept as $key => $value) {
                if(!empty($value))
                {
                    $getIdUserEsxcept[]="'".$value."'";
                }
            }
            $where =" where id in (".implode(',',$getIdUserEsxcept).") "; 
            $getEmail=$this->pembatasantransaksi->select_data('app.t_mtr_member', "$where")->result();

        }

// print_r($getEmail);exit;
        foreach ($getEmail as $key => $mail) {
             $email[$mail->id]=$mail->email;
        }

        $vehicleClassParam=array();

        // print_r($golongan);exit;

        if($kendaraan == 2) {        

        foreach ($golongan as $key => $val) {
            $restrictionCode = $char . sprintf("%04s", $no);
            
            $vehicleClassParam[]=$val;

            $dataHeader=array(
                        'limit_transaction_code'=> $restrictionCode,
                        'service_id'=>$kendaraan ,      
                        'start_date'=>$startDate,
                        'end_date'=>$endDate,             
                        'value'=>$totalTrx,
                        'limit_type'=>$limitType,
                        'custom_value'=>$customValue,
                        'custom_type'=>$isCustom,
                        'status'=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                        'vehicle_class_id'=> $val,
                        );

            $data[]=$dataHeader;
        
            //jika email tidak
        if(!empty($getEmail)){    
        foreach ($email as $key => $mail) {
            $dataHeaderDetail=array(
                    'limit_transaction_code'=> $restrictionCode,
                    'email'=>$mail,       
                    'value'=>$totalTrx,             
                    'limit_type'=>$limitType,
                    'custom_value'=>$customValue,
                    'custom_type'=>$isCustom,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );


            $dataDetail[]=$dataHeaderDetail;
            }
        }
            $noPj= substr($restrictionCode,11)+1 ;
            $codePj= substr($restrictionCode,0,11) ;
            $createCodePj= sprintf("$codePj%u",$noPj) ;
            $no++;
        
        }

        if($pejalanKaki == 1){
            $dataHeader=array(
                        'limit_transaction_code'=> $createCodePj,
                        'service_id'=>$pejalanKaki ,       
                        'start_date'=>$startDate,
                        'end_date'=>$endDate,             
                        'value'=>$totalTrx,
                        'limit_type'=>$limitType,
                        'custom_value'=>$customValue,
                        'custom_type'=>$isCustom,
                        'status'=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                        'vehicle_class_id'=>null,
                        );

            $data[]=$dataHeader;
            
            if(!empty($getEmail)){  
                foreach ($email as $key => $mail) {
                    $dataHeaderDetail=array(
                        'limit_transaction_code'=> $createCodePj,
                        'email'=>$mail,       
                        'value'=>$totalTrx,             
                        'limit_type'=>$limitType,
                        'custom_value'=>$customValue,
                        'custom_type'=>$isCustom,
                        'status'=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                        );

                $dataDetail[]=$dataHeaderDetail;
                }
            }
        
        }

        }else{
          
            $dataHeader=array(
                    'limit_transaction_code'=> $createCode,
                    'service_id'=>$pejalanKaki ,       
                    'start_date'=>$startDate,
                    'end_date'=>$endDate,             
                    'value'=>$totalTrx,
                    'limit_type'=>$limitType,
                    'custom_value'=>$customValue,
                    'custom_type'=>$isCustom,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    'vehicle_class_id'=>null,
                    );

            $data[]=$dataHeader;

            if(!empty($getEmail)){  
                foreach ($email as $key => $mail) {
                    $dataHeaderDetail=array(
                        'limit_transaction_code'=> $createCode,
                        'email'=>$mail,       
                        'value'=>$totalTrx,             
                        'limit_type'=>$limitType,
                        'custom_value'=>$customValue,
                        'custom_type'=>$isCustom,
                        'status'=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                        );

                $dataDetail[]=$dataHeaderDetail;
                }
            }

        }

        $checkOverlaps= $this->pembatasantransaksi->checkOverlaps($startDate,$endDate,implode(",",$vehicleClassParam),$kendaraan,$pejalanKaki,"");

        // print_r($checkOverlaps);exit;

        $checkTransaksi=$this->pembatasantransaksi->select_data('app.t_mtr_limit_transaction', " where start_date='$startDate' and end_date='$endDate'    
            and status = '1'");
        
        $checkingTrx[]=0;

        foreach ($checkTransaksi->result() as $key => $trx) {
             $check=$trx->value;

               if ($totalTrx > $check){
            
                    $checkingTrx[]=1; 
                }
                else{
                    
                    $checkingTrx[]=0; 
                }
        }

 
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if( $endDate <= $startDate)
        {
            echo $res=json_api(0," Tanggal akhir tidak boleh berada sebelum tanggal awal ");
        }        
        else if($checkOverlaps->num_rows()>0)
        {
            echo $res=json_api(0," Waktu tidak boleh bersinggungan");
        }
        else if(array_sum($checkingTrx)>0)
        {
            echo $res=json_api(0," Nilai boleh lebih besar dari batas transaksi global ");    
        }        
        else
        {

            // print_r($data);exit;
            $this->db->trans_begin();
            // insert header
            $this->pembatasantransaksi->insert_data_batch('app.t_mtr_limit_certain_group_transaction',$data);

             // insert detail
            if(!empty($getEmail)){  
            $this->pembatasantransaksi->insert_data_batch('app.t_mtr_limit_certain_group_transaction_detail',$dataDetail);
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
        $logUrl      = site_url().'master_data2/pembatasanTransaksi/action_add';;
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function createCode()
    {
        // tidak ada prefix port karena member berlaku untuk semua pelabuhan
        $front_code="LT".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_limit_certain_group_transaction where left(limit_transaction_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (limit_transaction_code) as max_code from app.t_mtr_limit_certain_group_transaction where left(limit_transaction_code,".$total_length.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);

        $getDetail= $this->pembatasantransaksi->select_data($this->_table,"where id=$id_decode")->row();
        // print_r($getDetail);exit;

        $limitType[""]='Pilih';
        $limitTypeSelected="";
        
        $limitTypeData[1]='Per Jam';
        $limitTypeData[2]='Per Hari';
        $limitTypeData[3]='Per Bulan';
        $limitTypeData[4]='Per Tahun';


        foreach ($limitTypeData as $key => $value) {

            if($getDetail->limit_type==$key)
            {
                $limitTypeSelected=$this->enc->encode($key);
                $limitType[$limitTypeSelected]=$value;
            }
            else
            {
                $limitType[$this->enc->encode($key)]=$value;
            }            

        }

        $getVehicleClass =$this->pembatasantransaksi->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc");
        $dataVehicleClass['']="Pilih";
        $selectedDataVehicleClass="";
        foreach ($getVehicleClass->result() as $key => $value) {
            $valueKey = $this->enc->encode($value->id);
            if($value->id == $getDetail->vehicle_class_id)
            {
                $selectedDataVehicleClass = $valueKey;
            }
            $dataVehicleClass[$valueKey] = $value->name;
        }


        // $limitType[""]='Pilih';
        // $limitTypeSelected="";
        
        // $limitTypeData[1]='Per Jam';
        // $limitTypeData[2]='Per Hari';
        // $limitTypeData[3]='Per Bulan';
        // $limitTypeData[4]='Per Tahun';

        $data['title'] = 'Edit Pembatasan Transaksi';
        $data['id'] = $id;
        $data['limitType'] = $limitType;
        $data['limitTypeSelected'] = $limitTypeSelected;
        $data['detail'] = $getDetail;
        $data['vehicleClass'] = $dataVehicleClass;
        $data['selectedVehicleClass'] = $selectedDataVehicleClass;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=$this->enc->decode($this->input->post('idDetail'));

        $limitTransactionCode=$this->input->post('limitTransactionCode');    
        $value=$this->input->post('value'); 

        $limitType=$this->enc->decode($this->input->post('limitType'));
        $isCustom=$this->input->post('isCustom');
        $customValue=$this->input->post('customValue');
        $service_id=$this->input->post('service_id');
        $vehicleClass=$this->input->post('vehicle');

        $startDate=$this->input->post('startDate');
        $endDate=$this->input->post('endDate');
                
        $this->form_validation->set_rules('idDetail', 'Id', 'required');
        $this->form_validation->set_rules('startDate', 'Awal Berlaku ', 'required');
        $this->form_validation->set_rules('endDate', 'Akhir Berlaku ', 'required');
        $this->form_validation->set_rules('limitTransactionCode', 'Kode Pebatasan ', 'required');
        $this->form_validation->set_rules('value', 'Nominal Pembatasan Transaksi ', 'required|numeric');
        $this->form_validation->set_rules('limitType', 'Tipe Pembatasan ', 'required');

        if(!empty($isCustom))
        {
            $this->form_validation->set_rules('customValue', 'Custom Nominal Jenis Pembatasan ', 'required|numeric');
        }

       // print_r($vehicleClass);exit;

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');

        $data=array(
                    'value'=>$value,
                    'start_date'=>$startDate,
                    'end_date'=>$endDate,
                    'limit_type'=>$limitType,
                    'custom_value'=>$customValue,
                    'custom_type'=>$isCustom,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        $updateDetail=array(
            'value'=>$value,
            'updated_by'=>$this->session->userdata('username'),
            'updated_on'=>date("Y-m-d H:i:s"),
            );        

        $checkOverlaps= $this->pembatasantransaksi->checkOverlapsEdit($startDate,$endDate,$vehicleClass,$service_id,$id);

        $getDetail=$this->pembatasantransaksi->select_data($this->_table , " where id=$id")->row();
        

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if( $endDate <= $startDate)
        {
            echo $res=json_api(0," Tanggal Awal berlaku tidak boleh melebihi tanggal akhir berlaku ");
        }        
        else if($checkOverlaps->num_rows()>0)
        {
            echo $res=json_api(0," Waktu tidak boleh bersinggungan ");
        }                                  
        else
        {
            
            $this->db->trans_begin();
            $this->pembatasantransaksi->update_data($this->_table ,$data,"id=$id");
            $this->pembatasantransaksi->update_data("app.t_mtr_limit_certain_group_transaction_detail" ,$updateDetail,"limit_transaction_code='".$getDetail->limit_transaction_code."' 
                                            and status=1 and value = ".$getDetail->value );

            // jika diisi baru pelabuhannya                    
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
        $logUrl      = site_url().'master_data2/pembatasanTransaksi/action_edit_detail_pembatasan';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit_detail_pembatasan($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);

        $getDetail= $this->pembatasantransaksi->getUserDEtailPembatasan($id_decode)->row();


        $limitType[""]='Pilih';
        $limitTypeSelected="";
        
        $limitTypeData[1]='Per Jam';
        $limitTypeData[2]='Per Hari';
        $limitTypeData[3]='Per Bulan';
        $limitTypeData[4]='Per Tahun';

        foreach ($limitTypeData as $key => $value) {

            if($getDetail->limit_type==$key)
            {
                $limitTypeSelected=$this->enc->encode($key);
                $limitType[$limitTypeSelected]=$value;
            }
            else
            {
                $limitType[$this->enc->encode($key)]=$value;
            }            

        }        

        $data['title'] = 'Edit Pembatasan Transaksi';
        $data['id'] = $id;
        $data['limitType'] = $limitType;
        $data['limitTypeSelected'] = $limitTypeSelected;
        $data['idTable']= "detailDataTables_".$getDetail->limit_transaction_code;
        $data['transactionCode']= $getDetail->limit_transaction_code;
        $data['detail'] = $getDetail;

        $this->load->view($this->_module.'/edit_detail_pembatasan',$data);   
    }
    
    public function action_edit_detail_pembatasan()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=$this->enc->decode($this->input->post('idDetail'));

        $limitTransactionCode=$this->input->post('limitTransactionCode');    
        $email=$this->input->post('email');
        $value=$this->input->post('value'); 
        $limitType=$this->enc->decode($this->input->post('limitType'));
        $isCustom=$this->input->post('isCustom');
        $customValue=$this->input->post('customValue');
                
        $this->form_validation->set_rules('idDetail', 'Id', 'required');
        $this->form_validation->set_rules('value', 'Batas Jumlah Trx ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('email',' format %s salah!');

        $data=array(
                    'value'=>$value,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }                       
        else
        {
            $this->db->trans_begin();
            $this->pembatasantransaksi->update_data("app.t_mtr_limit_certain_group_transaction_detail" ,$data,"id=$id");

            // jika diisi baru pelabuhannya                    
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
        $logUrl      = site_url().'master_data2/pembatasanTransaction/action_edit_detail_pembatasan';
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
        */

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->pembatasantransaksi->update_data("app.t_mtr_limit_certain_group_transaction",$data," id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal update data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil update data');
        } 
            
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data2/pembatasanTransaksi/action_change';
        $d[1]==1?$logMethod='DISABLE':$logMethod='ENABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change_active($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /*
        $d[0]= id
        $d[1]= status
        */

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );

        $id=$d[0];
        $getDetail=$this->pembatasantransaksi->select_data($this->_table," where id=".$id)->row();
        // $checkOverlaps= $this->pembatasantransaksi->checkOverlaps($getDetail->start_date, $getDetail->end_date,$id);  
        $checkOverlaps= $this->pembatasantransaksi->checkOverlapsEdit($getDetail->start_date, $getDetail->end_date,$getDetail->vehicle_class_id,$getDetail->service_id, $id); 
        
        if($checkOverlaps->num_rows()>0)
        {
            echo $res=json_api(0," Ada Waktu yang Aktif dan Bersinggungan ");
        }
        else
        {
            $this->db->trans_begin();
            $this->pembatasantransaksi->update_data("app.t_mtr_limit_certain_group_transaction",$data," id=".$id);
    
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal update data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil update data');
            } 
        }

            
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data2/pembatasanTransaksi/action_change';
        $d[1]==1?$logMethod='DISABLE':$logMethod='ENABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    


    public function action_change_detail_limit_member($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /*
        $d[0]= limit transaction code
        $d[1]= status
        $d[2]= email
        */

        // get heder data
        $getHeader = $this->pembatasantransaksi->select_data("app.t_mtr_limit_certain_group_transaction", " where limit_transaction_code='".$d[0]."'")->row();

        $data=array(
            "limit_transaction_code"=>$d[0],
            "email"=>$d[2],
            "limit_type"=>$getHeader->limit_type,
            "value"=>$getHeader->value,
            "custom_type"=>$getHeader->custom_type,
            "custom_value"=>$getHeader->custom_value,
            "status"=>$d[1],
            "created_on"=>date("Y-m-d H:i:s"),
            "created_by"=>$this->session->userdata("username")
        );

        $this->db->trans_begin();
        $this->pembatasantransaksi->insert_data("app.t_mtr_limit_certain_group_transaction_detail",$data);
    
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal Pembatasan User');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil Pembatasan User');
        } 
        

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data2/pembatasanTransaksi/action_change_detail_limit_member';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
    
    public function action_change_detail_limit_member_except($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /*
        $d[0]= limit transaction code
        $d[1]= status
        $d[2]= email
        */

        $data=array(
            "status"=>$d[1],
            "updated_on"=>date("Y-m-d H:i:s"),
            "updated_by"=>$this->session->userdata("username")
        );
        $this->db->trans_begin();
        $this->pembatasantransaksi->update_data("app.t_mtr_limit_certain_group_transaction_detail",$data," email ='".$d[2]."' and limit_transaction_code='".$d[0]."' ");
    
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal pengecualian pembatasan user');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil pengecualian pembatasan user');
        } 


        

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data2/pembatasanTransaksi/action_change_detail_limit_member_except';
        $logMethod   = 'DELETE';
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
        $this->pembatasantransaksi->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'master_data2/pembatasanTransaksi/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $data = $this->pembatasantransaksi->download();

        $file_name = 'Pembatasan Transaksi Golongan';
        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $header = array(
            'NO' => 'string',
            'KODE' => 'string',
            'TANGGAL MULAI' => 'string',
            'TANGGAL AKHIR' => 'string',
            'JENIS PJ' => 'string',
            'GOLONGAN' => 'string',
            'RANGE WAKTU' => 'string',
            'BATAS JUMLAH' => 'string',
            'CUSTOM RANGE WAKTU' => 'string',
            'NILAI CUSTOM RANGE WAKTU' => 'string',
            'STATUS' => 'string',
        );


        $no = 1;
        foreach ($data as $key => $value) {
            

            $rows[] = array(
                $no,
                $value->limit_transaction_code,
                $value->start_date,
                $value->end_date,
                $value->jenis_pj,
                $value->golongan,
                $value->limit_type,
                $value->value,
                $value->custom_type,
                $value->custom_value,
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
 
        $getData = $this->pembatasantransaksi->download();
        $data["data"]=$getData;
        $data["dateFrom"]=trim($this->input->get('dateFrom'));
        $data["dateTo"]=trim($this->input->get('dateTo'));

        // print_r($data['data']); exit;
        $this->load->view($this->_module . '/pdf', $data);

    }




}