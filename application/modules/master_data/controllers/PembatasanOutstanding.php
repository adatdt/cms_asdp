<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class PembatasanOutstanding extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('pembatasanOutstandingModel','pembatasan');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_limit_outstanding_transaction';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/pembatasanOutstanding';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->pembatasan->dataList();
            echo json_encode($rows);
            exit;
        }

        $getShipClass=$this->pembatasan->select_data("app.t_mtr_ship_class"," where status !='-5' order by name asc")->result();
        
        $dataShipClass[""]="Pilih";
        $dataShipClass[$this->enc->encode(0)]="Semua";
        foreach ($getShipClass as $key => $value) {
            $dataShipClass[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Pembatasan Outsanding',
            'content'  => 'pembatasanOutstanding/index',
            'shipClass'=>$dataShipClass,
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $getShipClass=$this->pembatasan->select_data("app.t_mtr_ship_class"," where status=1 order by name asc")->result();
        
        $dataShipClass[$this->enc->encode(0)]="SEMUA";
        foreach ($getShipClass as $key => $value) {
            $dataShipClass[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $data['title'] = 'Tambah Pembatasan Outsanding';
        $data['shipClass']=$dataShipClass;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');


        $startDate=$this->input->post('startDate');
        $endDate=$this->input->post('endDate');
        $value=$this->input->post('value');
        $shipClass=$this->enc->decode($this->input->post('shipClass'));

        // $transfer_fee=trim($this->input->post('transfer_fee'));

        $this->form_validation->set_rules('startDate', 'Tanggal Mulai ', 'required');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir ', 'required');
        $this->form_validation->set_rules('value', 'Nominal Pembatasan ', 'required|numeric');
        // $this->form_validation->set_rules('shipClass', ' Layanan ', 'required');

        // $this->form_validation->set_rules('transfer_fee', 'Biaya Transfer ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!')
                              ->set_message('numeric','%s Harus Berupa angka!');

        
        $data = array(
                    'ship_class'=>$shipClass,
                    'start_date'=>$startDate,
                    'end_date'=>$endDate,
                    'value'=>$value,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // print_r($data); exit;

        $checkOverlaps= $this->pembatasan->checkOverlaps($startDate, $endDate, $shipClass,"");
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
            echo $res=json_api(0," Waktu Tidak Boleh Bersinggungan ");
        }
        else
        {

            $this->db->trans_begin();

            $this->pembatasan->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/pembatasanMasterOutsanding/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);
        $detail= $this->pembatasan->select_data($this->_table,"where id=$id_decode")->row();

        $getShipClass=$this->pembatasan->select_data("app.t_mtr_ship_class"," where status=1 order by name asc")->result();        
        
        $selectedShipClass=$this->enc->encode(0);
        $dataShipClass[$selectedShipClass]="Semua";
        foreach ($getShipClass as $key => $value) {

            if($value->id == $detail->ship_class)
            {
                $selectedShipClass=$this->enc->encode($value->id);
                $dataShipClass[$selectedShipClass]=strtoupper($value->name);
            }
            else
            {                
                $dataShipClass[$this->enc->encode($value->id)]=strtoupper($value->name);
            }
        }



        $data['title'] = 'Edit Pembatasan Outsanding';
        $data['id'] = $id;
        $data['detail']=$detail;
        $data['selectedShipClass']=$selectedShipClass;
        $data['shipClass']=$dataShipClass;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id'));
        $startDate=$this->input->post('startDate');
        $endDate=$this->input->post('endDate');
        $value=$this->input->post('value');
        // $shipClass=$this->enc->decode($this->input->post('shipClass'));

        // $transfer_fee=trim($this->input->post('transfer_fee'));

        $this->form_validation->set_rules('startDate', 'Tanggal Mulai ', 'required');
        $this->form_validation->set_rules('endDate', 'Tanggal Akhir ', 'required');
        $this->form_validation->set_rules('value', 'Nominal Pembatasan ', 'required|numeric');
        // $this->form_validation->set_rules('shipClass', ' Layanan ', 'required');
        $this->form_validation->set_rules('id', ' id ', 'required');

        // $this->form_validation->set_rules('transfer_fee', 'Biaya Transfer ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!')
                              ->set_message('numeric','%s Harus Berupa angka!');

        
        $data = array(
                    // 'ship_class'=>$shipClass,
                    'start_date'=>$startDate,
                    'end_date'=>$endDate,
                    'value'=>$value,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        $getDetail=$this->pembatasan->select_data($this->_table," where id=".$id)->row();
        $checkOverlaps= $this->pembatasan->checkOverlaps($startDate, $endDate, $getDetail->ship_class, $id);                    

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
            echo $res=json_api(0," Waktu Tidak Boleh Bersinggungan ");
        }        
        else
        {

            $this->db->trans_begin();

            $this->pembatasan->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'master_data/pembatasanOutstanding/action_edit';
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

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/pembatasanOutstanding/action_change';
        $d[1]==1?$logMethod='ENABLE':$logMethod='DISABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change_active($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        $id=$d[0];

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );

        $getDetail=$this->pembatasan->select_data($this->_table," where id=".$id)->row();
        $checkOverlaps= $this->pembatasan->checkOverlaps($getDetail->start_date, $getDetail->end_date, $getDetail->ship_class, $id);       

        if($checkOverlaps->num_rows()>0)
        {
            echo $res=json_api(0," Ada Waktu yang Aktif dan Bersinggungan ");
        }
        else
        {

            $this->db->trans_begin();
            $this->pembatasan->update_data($this->_table,$data,"id=".$id);
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal aktif');            
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil aktif data');
            }   
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/pembatasanOutstanding/action_change_active';
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
        $logUrl      = site_url().'master_data/pembatasanOutstanding/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
