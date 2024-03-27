<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class ApprovalParam extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('ApprovalParamModel','approval');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_approval_param_vm';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/approvalParam';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->approval->dataList();
            echo json_encode($rows);
            exit;
        }

        $getPort=$this->approval->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();

        $dataPort[""]="Pilih";
        foreach ($getPort as $key => $value) 
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);

        }

        $getShipClass=$this->approval->select_data("app.t_mtr_ship_class"," where status=1 order by name asc ")->result();
        
        $dataShipClass[""]="Pilih";
        foreach ($getShipClass as $key => $value) 
        {
            $dataShipClass[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master Approval Parameter VM',
            'content'  => 'approvalParam/index',
            'port'=> $dataPort,
            'shipClass'=>$dataShipClass,
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $getPort=$this->approval->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();

        $dataPort[""]="Pilih";
        foreach ($getPort as $key => $value) 
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);

        }

        $getShipClass=$this->approval->select_data("app.t_mtr_ship_class"," where status=1 order by name asc ")->result();
        
        $dataShipClass[""]="Pilih";
        foreach ($getShipClass as $key => $value) 
        {
            $dataShipClass[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $data['title'] = 'Tambah Approval Parameter VM';
        $data['port']= $dataPort;
        $data['shipClass']=$dataShipClass;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=$this->enc->decode($this->input->post('port'));
        $shipClass=$this->enc->decode($this->input->post('shipClass'));

        $_POST['port'] = $port;
        $_POST['shipClass'] = $shipClass;

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required|numeric');
        $this->form_validation->set_rules('shipClass', 'Layanan ', 'required|numeric');


        $this->form_validation->set_message('required','%s harus diisi!')
                              ->set_message('numeric','%s Harus Berupa angka!');

        $data=array(
                    'port_id'=>$port,
                    'ship_class'=>$shipClass,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );


        // ceck data jika username sudah ada
        $check=$this->approval->select_data($this->_table," where port_id={$port}  and ship_class={$shipClass} and status not in (-5) ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Data sudah ada.");
        }
        else
        {

            $this->db->trans_begin();

            $this->approval->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/approvalParam/action_add';
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
        $detail=$this->approval->select_data($this->_table,"where id=$id_decode")->row();     
        
        $getPort=$this->approval->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();

        $dataPort[""]="Pilih";
        $selectedPort="";
        foreach ($getPort as $key => $value) 
        {
            if($value->id==$detail->port_id)
            {
                $selectedPort=$this->enc->encode($value->id);
                $dataPort[$selectedPort]=strtoupper($value->name);
            }
            else
            {

                $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
            }

        }

        $getShipClass=$this->approval->select_data("app.t_mtr_ship_class"," where status=1 order by name asc ")->result();
        
        $dataShipClass[""]="Pilih";
        $selectedShipClass="";
        foreach ($getShipClass as $key => $value) 
        {
            if($value->id==$detail->ship_class)
            {
                $selectedShipClass=$this->enc->encode($value->id);
                $dataShipClass[$selectedShipClass]=strtoupper($value->name);
            }
            else
            {
                $dataShipClass[$this->enc->encode($value->id)]=strtoupper($value->name);
            }
            
        }        

        $data['title'] = 'Edit Approval Parameter VM';
        $data['port']=$dataPort;
        $data['selectedPort']=$selectedPort;
        $data['shipClass']=$dataShipClass;
        $data['selectedShipClass']=$selectedShipClass;
        $data['id'] = $id;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id'));
        $shipClass=$this->enc->decode($this->input->post('shipClass'));

        $_POST['id'] = $id;
        $_POST['shipClass'] = $shipClass;
        // $_POST['shipClass'] = "aaa";



        $getDetail=$this->approval->select_data($this->_table, " where id={$id} ")->row();


        $this->form_validation->set_rules('id', 'id', 'required|numeric');
        $this->form_validation->set_rules('shipClass', 'Layanan ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!')
                              ->set_message('numeric','%s Harus Berupa angka!');


        $data=array(
            // 'port_id'=>$port,
            'ship_class'=>$shipClass,
            'status'=>1,
            'updated_by'=>$this->session->userdata('username'),
            'updated_on'=>date("Y-m-d H:i:s"),
        );

        // print_r($data); exit;

        // ceck data jika username sudah ada
        $check=$this->approval->select_data($this->_table," where port_id={$getDetail->port_id}  and ship_class={$shipClass} and status not in (-5) and id !={$id} ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Data sudah ada.");
        }        
        else
        {

            $this->db->trans_begin();

            $this->approval->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'master_data/approvalParam/action_edit';
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

        // print_r($d); exit;

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->approval->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'master_data/approvalParam/action_change';
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
        $this->approval->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'master_data/bank/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
