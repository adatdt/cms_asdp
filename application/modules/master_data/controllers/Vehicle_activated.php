<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Vehicle_activated extends MY_Controller{
	public function __construct() {
		parent::__construct();

        logged_in();
        $this->load->model('M_vehicle_activated','m_activated');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_vehicle_class_activated';
        $this->_table_activated_b2b  = "app.t_mtr_vehicle_class_activated_b2b";
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/vehicle_activated';
	}

	public function index() { 
        checkUrlAccess(uri_string(),'view');

        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->m_activated->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Aktivasi Kelas Layanan',
            'content'  => 'vehicle_activated/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'port' 	   => $this->m_activated->select_data("app.t_mtr_port"," where status not in (-5) order by name asc ")->result(),
            'ship_class' => $this->global_model->select_data("app.t_mtr_ship_class"," where status not in (-5) order by name asc ")->result(),
            'vehicle_class' => $this->m_activated->select_data("app.t_mtr_vehicle_class"," where status not in (-5) order by name asc ")->result(),
        );
		$this->load->view('default', $data);
	}

    public function add() {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $merchant = $this->m_activated->getDropdown("app.t_mtr_merchant","merchant_id","merchant_name");

        $data['title'] = 'Tambah Kelas Kendaraan Aktif';

        $data['port'] = $this->global_model->select_data("app.t_mtr_port","where status='1' order by name asc")->result();
        $data['class'] = $this->global_model->select_data("app.t_mtr_vehicle_class","where status='1' order by name asc")->result();
        $data['ship_class'] = $this->global_model->select_data("app.t_mtr_ship_class","where status='1' order by name asc")->result();
        $data['vehicleClass'] = $this->m_activated->getDropdown("app.t_mtr_vehicle_class","id","name");
        $data['merchant'] = array_diff($merchant,array(""=>"Pilih"));


        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        /* data post */
        $port_id         = trim($this->enc->decode($this->input->post('port')));
        $vehicle_class   = trim($this->enc->decode($this->input->post('vehicle_class')));
        $ship_class      = trim($this->enc->decode($this->input->post('ship_class')));
        $get_merchant    = $this->input->post("merchant");
        // print_r($this->input->post()); exit;
        $_POST['port_id'] = $port_id; 
        $_POST['vehicle_class'] = $vehicle_class; 
        $_POST['ship_class'] = $ship_class; 

        /* validation */
        $this->form_validation->set_rules('port_id', 'Pelabuhan', 'required|callback_special_char');
        $this->form_validation->set_rules('vehicle_class', 'Golongan', 'required|callback_special_char');
        $this->form_validation->set_rules('ship_class', 'Kelas Layanan', 'required|callback_special_char');

        $this->form_validation->set_message('letter_number_val','%s tidak boleh ada karakter khusus !');
        $this->form_validation->set_message('required', '%s harus diisi!'); 

        $check           = $this->m_activated->check_data($vehicle_class, $port_id, $ship_class);
       
        $pos_motor_bike  = $this->input->post('pos_motor_bike');
        $pos_vehicle     = $this->input->post('pos_vehicle');
        $web             = $this->input->post('web');
        $mobile          = $this->input->post('mobile');
        $b2b             = $this->input->post('b2b');
        $ifcs            = $this->input->post('ifcs');
        $web_cs          = $this->input->post('web_cs');
        $mpos_motor_bike = $this->input->post('mpos_motor_bike');
        $mpos_vehicle    = $this->input->post('mpos_vehicle');

        $pos_motor_bike === "yes" ? $pos_motor_bike = "t" : $pos_motor_bike = "f";
        $pos_vehicle  === "yes" ? $pos_vehicle = "t" : $pos_vehicle = "f";
        $web === "yes" ? $web = "t" : $web = "f";
        $mobile === "yes" ? $mobile = "t" : $mobile = "f";
        $b2b === "yes" ? $b2b = "t" : $b2b = "f";
        $ifcs === "yes" ? $ifcs = "t" : $ifcs = "f";
        $mpos_motor_bike === "yes" ? $mpos_motor_bike = "t" : $mpos_motor_bike = "f";
        $mpos_vehicle === "yes" ? $mpos_vehicle = "t" : $mpos_vehicle = "f";

        $merchant = array_filter(array_map(function($x){
            return $this->enc->decode(trim($x));
        }, explode(",",$get_merchant)), function($f){
            return $f !="";
        }); 
        // $this->db->insert('table', $data);

        $data = array(
            'vehicle_class' => $vehicle_class,
            'port_id' => $port_id,
            'ship_class' => $ship_class,
            'pos_motor_bike' => $pos_motor_bike,
            'pos_vehicle' => $pos_vehicle,
            'web' => $web,
            'mobile' => $mobile,
            'b2b' => $b2b,
            'ifcs' => $ifcs,
            'web_cs' => $web_cs,
            'mpos_motor_bike' => $mpos_motor_bike,
            'mpos_vehicle' => $mpos_vehicle,
            'created_on'=>date("Y-m-d H:i:s"),
            'created_by'=>$this->session->userdata('username'),
        );

        $dataMerchant =[];

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else if ($check) {
            echo $res = json_api(0, 'Data sudah ada');
        }
        else {
            $this->db->trans_begin();
            $this->m_activated->insert_data($this->_table,$data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else {
                $insert_id = $this->db->insert_id();

                foreach ($merchant as $keyMerchant => $valueMerchant) {     
                    $dataMerchant[]= array(
                        "merchant_id"=>$valueMerchant,
                        "vehicle_class_activated_id"=> $insert_id,
                        "status"=>1,
                        'created_by'=>$this->session->userdata('username'),
                        "created_on"=>date("Y-m-d H:i:s"), 
                        "ship_class" => $ship_class, );       
                }

                $this->m_activated->insert_data_batch($this->_table_activated_b2b,$dataMerchant);

                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');
            }
        }
    

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/vehicle_activated/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id = $this->enc->decode($id);
        $data['merchant'] = $this->global_model->select_data("app.t_mtr_merchant", "WHERE status = '1' ORDER BY merchant_name ASC")->result();
        $data['detailMerchant'] = $this->m_activated->getMerchantDetail($id);

        $merchatId = array_column($data['detailMerchant'],"merchant_id");
        $data["dataSelected"] = array_combine($merchatId, $merchatId);
        $data["selectMerchant"] = array_map(function($x){ return $this->enc->encode($x); },$merchatId);        

        $data['title'] = 'Edit Aktivasi Kelas Kendaraan';
        $data['detail'] = $this->m_activated->get_edit($id);
        $data['port'] = $this->global_model->select_data("app.t_mtr_port","where status='1' order by name asc")->result();
        $data['class'] = $this->global_model->select_data("app.t_mtr_vehicle_class","where status='1' order by name asc")->result();
        $data['ship_class'] = $this->global_model->select_data("app.t_mtr_ship_class","where status='1' order by name asc")->result();
   
        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        
        $id = $this->enc->decode($this->input->post('id'));
        $port_id         = trim($this->enc->decode($this->input->post('port')));
        $vehicle_class   = trim($this->enc->decode($this->input->post('vehicle_class')));
        $ship_class      = trim($this->enc->decode($this->input->post('ship_class')));
        $merchant        = $this->input->post('merchant');

        $_POST['id'] = $id; 
        $_POST['port_id'] = $port_id; 
        $_POST['vehicle_class'] = $vehicle_class; 
        $_POST['ship_class'] = $ship_class; 

        $pos_motor_bike  = $this->input->post('pos_motor_bike');
        $pos_vehicle     = $this->input->post('pos_vehicle');
        $web             = $this->input->post('web');
        $mobile          = $this->input->post('mobile');
        $b2b             = $this->input->post('b2b');
        $ifcs            = $this->input->post('ifcs');
        $web_cs          = $this->input->post('web_cs');
        $mpos_motor_bike = $this->input->post('mpos_motor_bike');
        $mpos_vehicle    = $this->input->post('mpos_vehicle');

        /* validation */
        $this->form_validation->set_rules('id', 'id', 'required');
        $this->form_validation->set_rules('port_id', 'Pelabuhan', 'required|callback_special_char');
        $this->form_validation->set_rules('vehicle_class', 'Golongan', 'required|callback_special_char');
        $this->form_validation->set_rules('ship_class', 'Kelas Layanan', 'required|callback_special_char');
        if($b2b == "t")
        {
            $this->form_validation->set_rules('merchant', 'Merchant', 'required');
        }   

        $this->form_validation->set_message('letter_number_val','%s tidak boleh ada karakter khusus !');
        $this->form_validation->set_message('required', '%s harus diisi!'); 
       
        $check           = $this->m_activated->check_data($vehicle_class, $port_id, $ship_class, $id);

        $pos_motor_bike === "yes" ? $pos_motor_bike = "t" : $pos_motor_bike = "f";
        $pos_vehicle  === "yes" ? $pos_vehicle = "t" : $pos_vehicle = "f";
        $web === "yes" ? $web = "t" : $web = "f";
        $web_cs === "yes" ? $web_cs = "t" : $web_cs = "f";
        $mobile === "yes" ? $mobile = "t" : $mobile = "f";
        $b2b === "yes" ? $b2b = "t" : $b2b = "f";
        $ifcs === "yes" ? $ifcs = "t" : $ifcs = "f";
        $mpos_motor_bike === "yes" ? $mpos_motor_bike = "t" : $mpos_motor_bike = "f";
        $mpos_vehicle === "yes" ? $mpos_vehicle = "t" : $mpos_vehicle = "f";

        $data = array(
            'vehicle_class' => $vehicle_class,
            'port_id' => $port_id,
            'ship_class' => $ship_class,
            'pos_motor_bike' => $pos_motor_bike,
            'pos_vehicle' => $pos_vehicle,
            'web' => $web,
            'web_cs' => $web_cs,
            'mobile' => $mobile,
            'b2b' => $b2b,
            'ifcs' => $ifcs,
            'mpos_motor_bike' => $mpos_motor_bike,
            'mpos_vehicle' => $mpos_vehicle,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else if ($check) {
            echo $res = json_api(0, 'Data sudah ada');
        }
        else {
            $this->db->trans_begin();
            $this->m_activated->update_data($this->_table, $data, "id=$id");
            $dataSoftDelete=array(
                "status"=>"-5",
                "updated_on"=>date("Y-m-d H:i:s"),
                "updated_by"=>$this->session->userdata("username")
            );
            $this->m_activated->update_data($this->_table_activated_b2b, $dataSoftDelete, "vehicle_class_activated_id = $id and status=1");            

            if($b2b == 't')
            {
                $getMerchant = array_filter(array_map(function($x){
                    return $this->enc->decode(trim($x));
                }, explode(",",$merchant)), function($f){
                    return $f !="";
                });  
    
                $dataMerchant=array();
                foreach ($getMerchant as $key => $value) {
                    $dataMerchant[]=array(
                        "merchant_id"=>$value,
                        "vehicle_class_activated_id"=>$id,
                        "ship_class"=>$ship_class,
                        "status"=> 1,
                        "created_on"=> date("Y-m-d H:i:s"),
                        "created_by"=>$this->session->userdata("username"),
                    );
                }
    
                $this->m_activated->insert_data_batch($this->_table_activated_b2b,$dataMerchant);            
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data ');
            }
            else {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data');
            }   
        }

        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/vehicle_activated/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $team_id = $this->enc->decode($id);

            $this->db->trans_begin();
            $this->schedule->update_data($this->_table,$data,"id=$team_id");

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
        $logUrl      = site_url().'pelabuhan/schedule/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
}