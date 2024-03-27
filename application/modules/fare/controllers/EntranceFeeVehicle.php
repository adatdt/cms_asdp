<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class EntranceFeeVehicle extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('EntranceFeeVehicleModel','fare');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_entrance_fee_vehicle';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'fare/entranceFeeVehicle';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->enc->decode($this->input->post('port'))){
                $this->form_validation->set_rules('port', 'port', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid Pelabuhan Keberangkatan'));
            }
            if($this->enc->decode($this->input->post('vehicleClass'))){
                $this->form_validation->set_rules('vehicleClass', 'Golongan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid Golongan '));
            }
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->fare->dataList();
            echo json_encode($rows);
            exit;
        }

        $getPort = $this->fare->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();
        $getVehicleClass = $this->fare->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc ")->result();

        $dataPort[""]="Pilih";
        foreach ($getPort as $key => $value) {
            $dataPort[$this->enc->encode($value->id)] = strtoupper($value->name);
        }

        $dataVehicleClass[""]="Pilih";
        foreach ($getVehicleClass as $key => $value) {
            $dataVehicleClass[$this->enc->encode($value->id)] = strtoupper($value->name);
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master Tarif Entrance Kendaraan',
            'content'  => 'fare/entranceFeeVehicle/index',
            'port' => $dataPort,
            'vehicleClass' => $dataVehicleClass,
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $getPort = $this->fare->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();
        $getVehicleClass = $this->fare->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc ")->result();

        $dataPort[""]="Pilih";
        foreach ($getPort as $key => $value) {
            $dataPort[$this->enc->encode($value->id)] = strtoupper($value->name);
        }

        $dataVehicleClass[""]="Pilih";
        foreach ($getVehicleClass as $key => $value) {
            $dataVehicleClass[$this->enc->encode($value->id)] = strtoupper($value->name);
        }


        $data['title'] = 'Tambah Tarif Entrance Kendaraan';
        $data['port'] = $dataPort;
        $data['vehicleClass'] = $dataVehicleClass;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        /* validation */
        $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        $this->form_validation->set_rules('vehicleClass', 'Golongan PNP ', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid Golongan PNP '));
        $this->form_validation->set_rules('entranceFee', 'Tarif Entrance  ', 'trim|required|numeric');
    
        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('numeric','%s Harus Berupa angka!');
        
        /* data post */
        $data = null;
        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else 
        {

            $port = trim($this->enc->decode($this->input->post('port')));
            $vehicleClass = trim($this->enc->decode($this->input->post('vehicleClass')));
            $entranceFee = trim($this->input->post('entranceFee'));
            
            $data=array(
                        'port_id'=>$port,
                        'vehicle_class_id'=>$vehicleClass,
                        'entrance_fee'=>$entranceFee,
                        'status'=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                        );

            // ceck data jika username sudah ada
            $check=$this->fare->select_data($this->_table," where port_id={$port} and vehicle_class_id={$vehicleClass} and status not in (-5) ");

            if($check->num_rows()>0)
            {
                echo $res=json_api(0,"Tarif Entrance sudah ada");
            }
            else
            {
                $this->db->trans_begin();

                $this->fare->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'fare/entranceFeeVehicle/action_add';
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

        $getDetail = $this->fare->select_data($this->_table," where id={$id_decode} ")->row();
        $getPort = $this->fare->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();
        $getVehicleClass = $this->fare->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc ")->result();

        $dataPort[""]="Pilih";
        $portSelected="";
        foreach ($getPort as $key => $value) {
            $idkey=$this->enc->encode($value->id);
            if($value->id == $getDetail->port_id)
            {
                $portSelected = $idkey;
            }
            $dataPort[$idkey] = strtoupper($value->name);
        }

        $dataVehicleClass[""]="Pilih";
        $dataVehicleClassSelected="";
        foreach ($getVehicleClass as $key => $value) {
            $idkey=$this->enc->encode($value->id);
            if($value->id == $getDetail->vehicle_class_id)
            {
                $dataVehicleClassSelected = $idkey;
            }
            $dataVehicleClass[$idkey] = strtoupper($value->name);
        }

        $data['title'] = 'Edit Tarif Entrance Penumpang';
        $data['detail'] = $getDetail;
        $data['id'] = $id;
        $data['port'] = $dataPort;
        $data['portSelected'] = $portSelected;
        $data['vehicleClass'] = $dataVehicleClass;
        $data['vehicleClassSelected'] = $dataVehicleClassSelected;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        /* validation */
        $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        $this->form_validation->set_rules('vehicleClass', 'Golongan PNP ', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid Golongan PNP '));
        $this->form_validation->set_rules('entranceFee', 'Tarif Entrance  ', 'trim|required|numeric');
        $this->form_validation->set_rules('id', 'id', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid id'));
        
        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('numeric','%s Harus Berupa angka!');
        
        /* data post */
        $data = null;
        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else 
        {
        
            $id=$this->enc->decode($this->input->post('id'));
            $port = trim($this->enc->decode($this->input->post('port')));
            $vehicleClass = trim($this->enc->decode($this->input->post('vehicleClass')));
            $entranceFee = trim($this->input->post('entranceFee'));

            $data=array(                    
                        'port_id'=>$port,
                        'vehicle_class_id'=>$vehicleClass,
                        'entrance_fee'=>$entranceFee,
                        'updated_by'=>$this->session->userdata('username'),
                        'updated_on'=>date("Y-m-d H:i:s"),
                        );

            // ceck data jika username sudah ada
            $check=$this->fare->select_data($this->_table," where port_id={$port} and vehicle_class_id={$vehicleClass} and status not in (-5) and id<>{$id} ");

            if($check->num_rows()>0)
            {
                echo $res=json_api(0,"Tarif Entrance sudah ada");
            }        
            else
            {

                $this->db->trans_begin();

                $this->fare->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'fare/entranceFee/action_edit';
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
        $this->fare->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'fare/entranceFee/action_change';
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
        $this->fare->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'fare/entranceFee/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
