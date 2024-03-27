<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class PembatasanMemberB2b extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('PembatasanMemberB2bModel','pembatasan');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_limit_transaction_b2b';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/pembatasanMemberB2b';
        $this->dbView=checkReplication();
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->pembatasan->dataList();
            echo json_encode($rows);
            exit;
        }

        $linkAdd=site_url($this->_module.'/add');
        $btnAdd = '<button onclick=showModal("'.$linkAdd.'") class="btn btn-sm btn-warning" title="Tambah" id="btnTmbh" ><i class="fa fa-plus"></i> Tambah</button>';
        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Pembatasan Transaksi B2B',
            'content'  => 'pembatasanMemberB2b/index',
            // 'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'btn_add'  =>generate_button($this->_module, 'add',$btnAdd),
            'btn_add_vehicle'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	
    }

	public function getDetailMember(){   
        checkUrlAccess($this->_module,'view');

        if($this->input->is_ajax_request()){
            $rows = $this->pembatasan->getDetailMember();
            echo json_encode($rows);
            exit;
        }
	
    }
    
	public function getDetailMember2(){   
        checkUrlAccess($this->_module,'view');

        if($this->input->is_ajax_request()){
            $rows = $this->pembatasan->getDetailMember();
            echo json_encode($rows);
            exit;
        }
	
    }    

    public function getDetailMemberExcept(){   
        checkUrlAccess($this->_module,'view');

        if($this->input->is_ajax_request()){
            $rows = $this->pembatasan->getDetailMemberExcept();
            echo json_encode($rows);
            exit;
        }
	
    }    


    public function add_with_detail(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $getUser= $this->pembatasan->getUser();

        /*
            1 = per jam
            2 = per hari
            3 = per bulan
            4 = per tahun
        */

        $limitType=array(
                            ""=>'Pilih',
                            $this->enc->encode(1)=>'Per Jam',
                            $this->enc->encode(2)=>'Per Hari',
                            $this->enc->encode(3)=>'Per Bulan',
                            $this->enc->encode(4)=>'Per Tahun',
    
                        );



        $data['title'] = 'Tambah Pembatasan Transaksi B2B';
        $data['limitType'] = $limitType;
        $data['getUser']  = $getUser;



        $this->load->view($this->_module.'/add',$data);
    }    

    public function action_add_with_detail()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $startDate=$this->input->post('startDate');
        $endDate=$this->input->post('endDate');
        $value=$this->input->post('value');
        $limitType=$this->enc->decode($this->input->post('limitType'));
        $isCustom=$this->input->post('isCustom');
        $customValue=$this->input->post('customValue');

        
        // yang di ambil hanya user yang except untu pencarian data not in 
        $idMemberExcept=$this->input->post('idMemberExcept[]');

        // print_r(count($idMemberExcept)); exit;
        

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
        $dataHeader=array(
                    'limit_transaction_code'=> $createCode,      
                    'start_date'=>$startDate,
                    'end_date'=>$endDate,             
                    'value'=>$value,
                    'limit_type'=>$limitType,
                    'custom_value'=>$customValue,
                    'custom_type'=>$isCustom,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );
        
        // print_r($dataHeader); exit;

        $data[]=$dataHeader;

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else
        {
            $this->db->trans_begin();

            // $idMemberLimit;

            // insert header
            $this->pembatasan->insert_data('app.t_mtr_limit_transaction',$dataHeader);

            $where =" ";
            if(!empty($idMemberExcept))
            {
                $getIdUserEsxcept=array();
                foreach ($idMemberExcept as $key => $value) {
                    if(!empty($value))
                    {
                        $getIdUserEsxcept[]="'".$value."'";
                    }
                }

                $where =" where id not in (".implode(',',$getIdUserEsxcept).") ";                 
                $whereExcept =" where id in (".implode(',',$getIdUserEsxcept).") "; 

                // insert exception
                $this->pembatasan->insert_detail($dataHeader, $whereExcept, '-5');
            }
            
            // insert limit
            $this->pembatasan->insert_detail($dataHeader, $where,'1');
            
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
        $logUrl      = site_url().'vaccine_parameter/vaccineParam/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // $getUser= $this->pembatasan->getUser();

        $userMerchant=$this->pembatasan->select_data("app.t_mtr_merchant"," where status=1 order by merchant_name asc")->result();

        $getUserMerchant[""]="Pilih";
        foreach ($userMerchant as $key => $value) {
            $getUserMerchant[$this->enc->encode($value->merchant_id)]=strtoupper($value->merchant_name);
        }

        /*
            1 = per jam
            2 = per hari
            3 = per bulan
            4 = per tahun
        */

        $limitType=array(
                            ""=>'Pilih',
                            $this->enc->encode(1)=>'Per Jam',
                            $this->enc->encode(2)=>'Per Hari',
                            $this->enc->encode(3)=>'Per Bulan',
                            $this->enc->encode(4)=>'Per Tahun',
    
                        );



        $data['title'] = 'Tambah Pembatasan Transaksi B2B';
        $data['limitType'] = $limitType;
        $data['merchant']  = $getUserMerchant;


        $this->load->view($this->_module.'/add',$data);
    }    

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $startDate=$this->input->post('startDate');
        $endDate=$this->input->post('endDate');
        $value=$this->input->post('value');
        $limitType=$this->enc->decode($this->input->post('limitType'));
        $isCustom=$this->input->post('isCustom');
        $customValue=$this->input->post('customValue');
        $merchant=$this->enc->decode($this->input->post('merchant'));
        // echo $value; exit;

        $idData=$this->input->post("idData");
        $idMember=$this->input->post("idMemberExcept[]");

        // echo $idData; exit;
        // print_r($idMember); exit;

        $getIdMember= array();
        if(count((array)$idMember)>0)
        {
            foreach ($idMember as $key => $idMember) {
                $getIdMember[]="'".$idMember."'";
            }
        }


        $this->form_validation->set_rules('startDate', 'Tanggal Awal ', 'required');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir ', 'required');
        $this->form_validation->set_rules('value', 'Batas Jumlah Trx ', 'required|numeric');
        $this->form_validation->set_rules('limitType', 'Range Waktu Pembatasan ', 'required');
        $this->form_validation->set_rules('merchant', 'Merchant ', 'required');
        $this->form_validation->set_rules('idData', 'Id Data ', 'required');

        if(!empty($isCustom))
        {
            $this->form_validation->set_rules('customValue', ' Custom Nominal Jenis Pembatasan ', 'required|numeric');
        }



        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');

        
        $createCode=$this->createCode();
        $dataHeader=array(
                    'limit_transaction_code'=> $createCode,   
                    'merchant_id'=> $merchant,      
                    'start_date'=>$startDate,
                    'end_date'=>$endDate,             
                    'value'=>$value,
                    'limit_type'=>$limitType,
                    'custom_value'=>$customValue,
                    'custom_type'=>$isCustom,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        $dataDetail=array(
            'limit_transaction_code'=> $createCode,                 
            'value'=>$value,
            );
        
        // print_r($dataDetail); exit;

        $data[]=$dataHeader;
        $data[]=$dataDetail;

        $checkOverlaps= $this->pembatasan->checkOverlaps($startDate.":00",$endDate.":00",$merchant,"");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($endDate <= $startDate)
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

            // insert header
            $this->pembatasan->insert_data('app.t_mtr_limit_transaction_b2b',$dataHeader);

            $where =" where merchant_id='{$merchant}' ";
            if($idData==1)
            {
                if(count((array)$getIdMember)>0)
                {
                    $where .=" and id not in  (".implode(",",$getIdMember).") and status =1 ";
                }

                // di insert hanya data pengecualian saja
                $this->pembatasan->insert_detail($dataDetail, $where, 1);
            }
            else            
            {
                if(count((array)$getIdMember)>0)
                {
                    $where .=" and  id  in  (".implode(",",$getIdMember).") and status =1 ";
                    // di insert hanya data pengecualian saja
                    $this->pembatasan->insert_detail($dataDetail, $where, 1);
                }
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
        $logUrl      = site_url().'master_data/pembatasanMemberB2b/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    function createCode()
    {
        // tidak ada prefix port karena member berlaku untuk semua pelabuhan
        $front_code="LB".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_limit_transaction_b2b where left(limit_transaction_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (limit_transaction_code) as max_code from app.t_mtr_limit_transaction_b2b where left(limit_transaction_code,".$total_length.")='".$front_code."' ")->row();
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

        $getDetail= $this->pembatasan->select_data($this->_table,"where id=$id_decode")->row();
        $userMerchant=$this->pembatasan->select_data("app.t_mtr_merchant"," where status=1 order by merchant_name asc")->result();

// print_r($userMerchant);exit;

        $getUserMerchant[""]="Pilih";
        $selectedMerchant="";
        foreach ($userMerchant as $key => $value) {
            
            if($getDetail->merchant_id==$value->merchant_id)
            {   
                $selectedMerchant=$this->enc->encode($value->merchant_id);
                $getUserMerchant[$selectedMerchant]=strtoupper($value->merchant_name);
            } 
            else
            {
                $getUserMerchant[$this->enc->encode($value->merchant_id)]=strtoupper($value->merchant_name);
            }
        }


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
 // print_r($limitTypeSelected);exit;
        $data['title'] = 'Edit Pembatasan Transaksi B2B';
        $data['id'] = $id;
        $data['limitType'] = $limitType;
        $data['limitTypeSelected'] = $limitTypeSelected;
        $data['detail'] = $getDetail;
        $data['merchant']  = $getUserMerchant;
        $data['selectedMerchant']  = $selectedMerchant;
//  print_r($data);exit;

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
        $startDate=$this->input->post('startDate');
        $endDate=$this->input->post('endDate');
        

        
        $this->form_validation->set_rules('idDetail', 'Id', 'required');
        $this->form_validation->set_rules('startDate', 'Tanggal Mulai', 'required');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir', 'required');
        $this->form_validation->set_rules('limitTransactionCode', 'Kode Pebatasan ', 'required');
        $this->form_validation->set_rules('value', 'Batas Jumlah Trx ', 'required|numeric');
        $this->form_validation->set_rules('limitType', 'Range Waktu Pembatasan ', 'required');

        if(!empty($isCustom))
        {
            $this->form_validation->set_rules('customValue', 'Custom Nominal Jenis Pembatasan ', 'required|numeric');
        }


        // $this->form_validation->set_rules('timer','Timer','required|numeric');

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

        $dataDetail=array(
            'value'=>$value,
            'limit_type'=>$limitType,
            'custom_value'=>$customValue,
            'custom_type'=>$isCustom,
            'updated_by'=>$this->session->userdata('username'),
            'updated_on'=>date("Y-m-d H:i:s"),
            );        

        $getDetail=$this->pembatasan->select_data($this->_table," where id=".$id)->row();
        $checkOverlaps= $this->pembatasan->checkOverlaps($startDate,$endDate,$getDetail->merchant_id,$id);

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($endDate <= $startDate)
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

            $getHeader=$this->pembatasan->select_data($this->_table ," where id={$id} " )->row();
            $this->pembatasan->update_data($this->_table ,$data,"id=$id");
            $this->pembatasan->update_data("app.t_mtr_limit_transaction_detail_b2b" ,$dataDetail," 
            limit_transaction_code='".$getHeader->limit_transaction_code."' and value = '".$getHeader->value."' ");

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
        $logUrl      = site_url().'master_data/pembatasanTransactionB2b/action_edit_detail_pembatasan';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    
    
    public function edit_with_detail($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);

        $getDetail= $this->pembatasan->select_data($this->_table,"where id=$id_decode")->row();


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
        $data['detail'] = $getDetail;

        $this->load->view($this->_module.'/edit',$data);   
    }


    public function action_edit_with_detail()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=$this->enc->decode($this->input->post('idDetail'));

        $limitTransactionCode=$this->input->post('limitTransactionCode');    
        $value=$this->input->post('value'); 
        $limitType=$this->enc->decode($this->input->post('limitType'));
        $isCustom=$this->input->post('isCustom');
        $customValue=$this->input->post('customValue');
        

        
        $this->form_validation->set_rules('idDetail', 'Id', 'required');
        $this->form_validation->set_rules('limitTransactionCode', 'Kode Pebatasan ', 'required');
        $this->form_validation->set_rules('value', 'Nominal Pembatasan Transaksi ', 'required|numeric');
        $this->form_validation->set_rules('limitType', 'Tipe Pembatasa ', 'required');

        if(!empty($isCustom))
        {
            $this->form_validation->set_rules('customValue', 'Custom Nominal Pembatasan ', 'required|numeric');
        }


        // $this->form_validation->set_rules('timer','Timer','required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');



        $data=array(
                    'value'=>$value,
                    'limit_type'=>$limitType,
                    'custom_value'=>$customValue,
                    'custom_type'=>$isCustom,
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
            $this->pembatasan->update_data($this->_table ,$data,"id=$id");

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
        $logUrl      = site_url().'master_data/pembatasanTransaction/action_edit_detail_pembatasan';
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

        $getDetail= $this->pembatasan->select_data("app.t_mtr_limit_transaction_detail_b2b","where id=$id_decode")->row();
        $getHeader= $this->pembatasan->select_data("app.t_mtr_limit_transaction_b2b","where limit_transaction_code='".$getDetail->limit_transaction_code."' ")->row();


        $limitType[""]='Pilih';
        $limitTypeSelected="";
        
        $limitTypeData[1]='Per Jam';
        $limitTypeData[2]='Per Hari';
        $limitTypeData[3]='Per Bulan';
        $limitTypeData[4]='Per Tahun';


        foreach ($limitTypeData as $key => $value) {

            if($getHeader->limit_type==$key)
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
        $data['limitTransactionCode']= $getDetail->limit_transaction_code;
        $data['detail'] = $getDetail;

        $this->load->view($this->_module.'/edit_detail_pembatasan',$data);   
    }
    
    public function action_edit_detail_pembatasan()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=$this->enc->decode($this->input->post('idDetail'));

        $limitTransactionCode=$this->input->post('limitTransactionCode');    
        $value=$this->input->post('value'); 
        
        
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
            $this->pembatasan->update_data("app.t_mtr_limit_transaction_detail_b2b" ,$data,"id=$id");

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
        $logUrl      = site_url().'master_data/pembatasanTransactionB2b/action_edit_detail_pembatasan';
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
        $this->pembatasan->update_data("app.t_mtr_limit_transaction_b2b",$data," id=".$d[0]);

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
        $logUrl      = site_url().'master_data/pembatasanMemberB2b/action_change';
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

        $getDetail=$this->pembatasan->select_data($this->_table," where id=".$id)->row();
        $checkOverlaps= $this->pembatasan->checkOverlaps($getDetail->start_date,$getDetail->end_date,$getDetail->merchant_id,$id);

        if($checkOverlaps->num_rows()>0)
        {
            echo $res=json_api(0," Ada Waktu yang Aktif dan Bersinggungan ");
        }
        else
        {

            $this->db->trans_begin();
            $this->pembatasan->update_data("app.t_mtr_limit_transaction_b2b",$data," id=".$id);
            
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
        $logUrl      = site_url().'master_data/pembatasanMemberB2b/action_change';
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
        $d[2]= outlet_id t_mtr_outlet_merchant
        */

        // get heder data
        $getHeader = $this->pembatasan->select_data("app.t_mtr_limit_transaction_b2b", " where limit_transaction_code='".$d[0]."'")->row();
        

        $data=array(
            "merchant_id"=>$getHeader->merchant_id,
            "limit_transaction_code"=>$d[0],
            "outlet_id"=>$d[2],
            "limit_type"=>$getHeader->limit_type,
            "value"=>$getHeader->value,
            "custom_type"=>$getHeader->custom_type,
            "custom_value"=>$getHeader->custom_value,
            "status"=>$d[1],
            "created_on"=>date("Y-m-d H:i:s"),
            "created_by"=>$this->session->userdata("username")
        );


        $this->db->trans_begin();
        $this->pembatasan->insert_data("app.t_mtr_limit_transaction_detail_b2b",$data);
    
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
        $logUrl      = site_url().'master_data/pembatasanMemberB2b/action_change_detail_limit_member';
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
        $d[0]= id detail
        $d[1]= status
        */

        $data=array(
            "status"=>$d[1],
            "updated_on"=>date("Y-m-d H:i:s"),
            "updated_by"=>$this->session->userdata("username")
        );
        $this->db->trans_begin();
        $this->pembatasan->update_data("app.t_mtr_limit_transaction_detail_b2b",$data," id ='".$d[0]."' ");
    
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
        $logUrl      = site_url().'master_data/pembatasanMember/action_change_detail_limit_member_except';
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
            'status'=>'-5',
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
        $logUrl      = site_url().'master_data/pembatasanMemberB2b/action_delete';
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

        $getDetailVehicle=$this->pembatasan->select_data("app.t_mtr_vaccine_param_detail_vehicle"," where id=".$id)->row();
        $getParam=$this->pembatasan->select_data("app.t_mtr_vaccine_param", " where id=".$getDetailVehicle->vaccine_param_id)->row();

        $vaccineActive= $getParam->vaccine_active;
        $testVaccineActive= $getParam->test_covid_active;

        $startDate=$getParam->start_date;
        $endDate=$getParam->end_date;        
        
        $dataVehicleClass=array();
        $dataPort=array();

        $selectDetailVehicle=$this->pembatasan->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where status !='-5' and  vehicle_class_id !=".$getDetailVehicle->vehicle_class_id." and vaccine_param_id=".$getDetailVehicle->vaccine_param_id)->result();
        $selectDetailPort=$this->pembatasan->select_data("app.t_mtr_vaccine_param_detail_port", " where status !='-5' and vaccine_param_id=".$getDetailVehicle->vaccine_param_id)->result();
        
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
        $getByRange=$this->pembatasan->select_data("app.t_mtr_vaccine_param",$where);    
        
        $getError[]=0;
        $getErrorName=array();
        if($getByRange->num_rows()>0)
        {
            foreach ($getByRange->result() as $key => $value) {
                
                $getVacinePortDetail=$this->pembatasan->getPortDetailVaccine($value->id);
                $getVacineVehicleDetail=$this->pembatasan->getVehicleDetailVaccine($value->id);

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
            $this->pembatasan->update_data("app.t_mtr_vaccine_param_detail_vehicle",$data," id='".$id."'");
        
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

    public function getUser()
    {
        echo json_encode($this->pembatasan->getUser());
    }

    public function getMemberLimit()
    {
     
        if($this->input->is_ajax_request())
        {
            $rows = $this->pembatasan->getMemberLimit();
            echo json_encode($rows);
            exit;
        }
    }

    public function getMemberExcept()
    {
     
        if($this->input->is_ajax_request())
        {
            $rows = $this->pembatasan->getMemberExcept();
            echo json_encode($rows);
            exit;
        }
    }    

}