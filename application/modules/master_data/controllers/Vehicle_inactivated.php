<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Vehicle_inactivated extends MY_Controller{
	public function __construct() {
		parent::__construct();

        logged_in();
        $this->load->model('M_vehicle_inactivated', 'vehicleInactivatedModel');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_vehicle_class_inactivated';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/vehicle_inactivated';
	}

	public function index() { 
        checkUrlAccess(uri_string(),'view');

        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->vehicleInactivatedModel->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'          => 'Home',
            'url_home'      => site_url('home'),
            'title'         => 'Inaktivasi Kelas Layanan',
            'content'       => 'vehicle_inactivated/index',
            'btn_add'       => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'port' 	        => $this->vehicleInactivatedModel->select_data("app.t_mtr_port","WHERE status NOT IN (-5) ORDER BY name ASC")->result(),
            'ship_class'    => $this->vehicleInactivatedModel->select_data("app.t_mtr_ship_class","WHERE status NOT IN (-5) ORDER BY name ASC")->result(),
            'vehicle_class' => $this->vehicleInactivatedModel->select_data("app.t_mtr_vehicle_class","WHERE status NOT IN (-5) ORDER BY name ASC")->result(),
        );
		$this->load->view('default', $data);
	}

    public function add() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title']      = 'Tambah Kelas Layanan Inaktif';
        $data['port']       = $this->global_model->select_data("app.t_mtr_port", "WHERE status = '1' ORDER BY name ASC")->result();
        $data['class']      = $this->global_model->select_data("app.t_mtr_vehicle_class", "WHERE status = '1' ORDER BY name ASC")->result();
        $data['ship_class'] = $this->global_model->select_data("app.t_mtr_ship_class", "WHERE status = '1' ORDER BY name ASC")->result();
        $data['merchant'] = $this->global_model->select_data("app.t_mtr_merchant", "WHERE status = '1' ORDER BY merchant_name ASC")->result();

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add() {
       
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        /* data post */
        $port_id            = trim($this->enc->decode($this->input->post("port_id")));
        $vehicle_class_id   = trim($this->enc->decode($this->input->post('vehicle_class_id')));
        $ship_class_id      = trim($this->enc->decode($this->input->post('ship_class_id')));
        $start_date = $this->input->post('start_date');
        $end_date   = $this->input->post('end_date');
        $merchant   = $this->input->post('merchant');
        
        $_POST['port_id'] = $port_id; 
        $_POST['vehicle_class_id'] = $vehicle_class_id; 
        $_POST['ship_class_id'] = $ship_class_id; 
         
        $web                = $this->input->post('web');
        $mobile             = $this->input->post('mobile');
        $b2b                = $this->input->post('b2b');
        $ifcs               = $this->input->post('ifcs');
        $web_cs             = $this->input->post('web_cs');
        $web_admin          = $this->input->post('web_admin');
        // $mpos_motor_bike    = $this->input->post('mpos_motor_bike');
        // $mpos_vehicle       = $this->input->post('mpos_vehicle');
        // $pos_motor_bike     = $this->input->post('pos_motor_bike');
        // $pos_vehicle        = $this->input->post('pos_vehicle');
        
        // $pos_motor_bike === "yes" ? $pos_motor_bike = "t" : $pos_motor_bike = "f";
        // $pos_vehicle  === "yes" ? $pos_vehicle = "t" : $pos_vehicle = "f";
        $web === "yes" ? $web = "t" : $web = "f";
        $mobile === "yes" ? $mobile = "t" : $mobile = "f";
        $b2b === "yes" ? $b2b = "t" : $b2b = "f";
        $ifcs === "yes" ? $ifcs = "t" : $ifcs = "f";
        $web_cs === "yes" ? $web_cs = "t" : $web_cs = "f";
        $web_admin === "yes" ? $web_admin = "t" : $web_admin = "f";
        // $mpos_motor_bike === "yes" ? $mpos_motor_bike = "t" : $mpos_motor_bike = "f";
        // $mpos_vehicle === "yes" ? $mpos_vehicle = "t" : $mpos_vehicle = "f";

        /* validation */
        $this->form_validation->set_rules('start_date', 'Tanggal Awal Berlaku', 'required|callback_validate_date_time');
        $this->form_validation->set_rules('end_date', 'Tanggal Akhir Berlaku', 'required|callback_validate_date_time');
        $this->form_validation->set_rules('port_id', 'Pelabuhan', 'required|callback_special_char');
        $this->form_validation->set_rules('vehicle_class_id', 'Golongan', 'required|callback_special_char');
        $this->form_validation->set_rules('ship_class_id', 'Kelas Layanan', 'required|callback_special_char');

        if($b2b == "t")
        {
            $this->form_validation->set_rules('merchant', 'Merchant', 'required');
        }

        $this->form_validation->set_message('letter_number_val','%s tidak boleh ada karakter khusus !');
        $this->form_validation->set_message('validate_date_time','%s Format Tanggal tidak sesuai !');
        $this->form_validation->set_message('required', '%s harus diisi!'); 

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
            exit;
        }        

        $data = array(
            'port_id'           => $port_id,
            'ship_class'        => $ship_class_id,
            'vehicle_class_id'  => $vehicle_class_id,
            'start_date'        => $start_date,
            'end_date'          => $end_date,
            // 'pos_motor_bike'    => $pos_motor_bike,
            // 'pos_vehicle'       => $pos_vehicle,
            'web'               => $web,
            'mobile'            => $mobile,
            'b2b'               => $b2b,
            'ifcs'              => $ifcs,
            'web_cs'            => $web_cs,
            'web_admin'         => $web_admin,
            // 'mpos_motor_bike'   => $mpos_motor_bike,
            // 'mpos_vehicle'      => $mpos_vehicle,

            'created_on'        => date("Y-m-d H:i:s"),
            'created_by'        => $this->session->userdata('username'),
        );
        
        // $check = $this->vehicleInactivatedModel->check_data($vehicle_class_id, $port_id, $ship_class_id);
        $check = FALSE;

        if ($check) {
            echo $res = json_api(0, 'Data sudah ada');
        }
        else {
            // print_r(explode(",",$merchant)); exit;
            // echo $merchant; exit;
            // print_r($getMerchant); exit;

            $this->db->trans_begin();
            $getId = $this->vehicleInactivatedModel->insert_data_id($this->_table, $data);
            if($b2b == 't')
            {
                $getMerchant = array_filter(array_map(function($x){
                    return $this->enc->decode(trim($x));
                }, explode(",",$merchant)), function($f){
                    return $f !="";
                });  

                $dataB2b=array();
                foreach ($getMerchant as $key => $value) {
                    $dataB2b[]=array(
                        "merchant_id"=>$value,
                        "vehicle_class_inactivated_id"=>$getId,
                        "ship_class"=>$ship_class_id,
                        "status"=> 1,
                        "created_on"=> date("Y-m-d H:i:s"),
                        "created_by"=>$this->session->userdata("username"),
                    );
                }
    
                $this->vehicleInactivatedModel->insert_data_batch("app.t_mtr_vehicle_class_inactivated_b2b", $dataB2b);
            }
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');
            }
        }

        

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/vehicle_inactivated/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $data['title']      = 'Edit Inaktivasi Kelas Kendaraan';
        $data['detail']     = $this->vehicleInactivatedModel->get_edit($this->enc->decode($id));
        $data['port']       = $this->global_model->select_data("app.t_mtr_port", "WHERE status = '1' ORDER BY name ASC")->result();
        $data['class']      = $this->global_model->select_data("app.t_mtr_vehicle_class","WHERE status = '1' ORDER BY name ASC")->result();
        $data['ship_class'] = $this->global_model->select_data("app.t_mtr_ship_class","WHERE status = '1' ORDER BY name ASC")->result();
        $data['merchant'] = $this->global_model->select_data("app.t_mtr_merchant", "WHERE status = '1' ORDER BY merchant_name ASC")->result();
        $data['detailMerchant'] = $this->vehicleInactivatedModel->getDetailMerchant(" where a.vehicle_class_inactivated_id=".$this->enc->decode($id)." and a.status=1");

        $merchatId = array_column($data['detailMerchant'],"merchant_id");
        $data["dataSelected"] = array_combine($merchatId, $merchatId);
        $data["merchantString"] = array_map(function($x){ return $this->enc->encode($x); },$merchatId);        

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        /* data post */
        $id                 = trim($this->enc->decode($this->input->post("id")));
        $port_id            = trim($this->enc->decode($this->input->post("port_id")));
        $vehicle_class_id   = trim($this->enc->decode($this->input->post('vehicle_class_id')));
        $ship_class_id      = trim($this->enc->decode($this->input->post('ship_class_id')));
        $start_date         = $this->input->post('start_date');
        $end_date           = $this->input->post('end_date');
        $merchant   = $this->input->post('merchant');

        $_POST['id'] = $id; 
        $_POST['port_id'] = $port_id; 
        $_POST['vehicle_class_id'] = $vehicle_class_id; 
        $_POST['ship_class_id'] = $ship_class_id; 

        // $pos_motor_bike     = $this->input->post('pos_motor_bike');
        // $pos_vehicle        = $this->input->post('pos_vehicle');
        $web                = $this->input->post('web');
        $mobile             = $this->input->post('mobile');
        $b2b                = $this->input->post('b2b');
        $ifcs               = $this->input->post('ifcs');
        $web_cs             = $this->input->post('web_cs');
        $web_admin          = $this->input->post('web_admin');
        // $mpos_motor_bike    = $this->input->post('mpos_motor_bike');
        // $mpos_vehicle       = $this->input->post('mpos_vehicle');

        // $pos_motor_bike === "yes" ? $pos_motor_bike = "t" : $pos_motor_bike = "f";
        // $pos_vehicle  === "yes" ? $pos_vehicle = "t" : $pos_vehicle = "f";
        $web === "yes" ? $web = "t" : $web = "f";
        $mobile === "yes" ? $mobile = "t" : $mobile = "f";
        $b2b === "yes" ? $b2b = "t" : $b2b = "f";
        $ifcs === "yes" ? $ifcs = "t" : $ifcs = "f";
        $web_cs === "yes" ? $web_cs = "t" : $web_cs = "f";
        $web_admin === "yes" ? $web_admin = "t" : $web_admin = "f";
        // $mpos_motor_bike === "yes" ? $mpos_motor_bike = "t" : $mpos_motor_bike = "f";
        // $mpos_vehicle === "yes" ? $mpos_vehicle = "t" : $mpos_vehicle = "f";

        /* validation */
        $this->form_validation->set_rules('start_date', 'Tanggal Awal Berlaku', 'required|callback_validate_date_time');
        $this->form_validation->set_rules('end_date', 'Tanggal Akhir Berlaku', 'required|callback_validate_date_time');
        $this->form_validation->set_rules('port_id', 'Pelabuhan', 'required|callback_special_char');
        $this->form_validation->set_rules('vehicle_class_id', 'Golongan', 'required|callback_special_char');
        $this->form_validation->set_rules('ship_class_id', 'Kelas Layanan', 'required|callback_special_char');

        if($b2b == "t")
        {
            $this->form_validation->set_rules('merchant', 'Merchant', 'required');
        }        

        $this->form_validation->set_message('letter_number_val','%s tidak boleh ada karakter khusus !');
        $this->form_validation->set_message('validate_date_time','%s Format Tanggal tidak sesuai !');
        $this->form_validation->set_message('required', '%s harus diisi!');         

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
            exit;
        }        

        $data = array(
            'port_id'           => $port_id,
            'ship_class'        => $ship_class_id,
            'vehicle_class_id'  => $vehicle_class_id,
            'start_date'        => $start_date,
            'end_date'          => $end_date,
            // 'pos_motor_bike'    => $pos_motor_bike,
            // 'pos_vehicle'       => $pos_vehicle,
            'web'               => $web,
            'mobile'            => $mobile,
            'b2b'               => $b2b,
            'ifcs'              => $ifcs,
            'web_cs'            => $web_cs,
            'web_admin'            => $web_admin,
            // 'mpos_motor_bike'   => $mpos_motor_bike,
            // 'mpos_vehicle'      => $mpos_vehicle,

            'updated_on'        => date("Y-m-d H:i:s"),
            'updated_by'        => $this->session->userdata('username'),
        );

        // $check = $this->vehicleInactivatedModel->check_data($vehicle_class_id, $port_id, $ship_class_id, $id);
        $check = FALSE;


        if ($check) {
            echo $res = json_api(0, 'Data sudah ada');
        }
        else {
            $this->db->trans_begin();
            $this->vehicleInactivatedModel->update_data($this->_table, $data, "id = $id");

            $dataSoftDelete=array(
                "status"=>"-5",
                "updated_on"=>date("Y-m-d H:i:s"),
                "updated_by"=>$this->session->userdata("username")
            );
            $this->vehicleInactivatedModel->update_data("app.t_mtr_vehicle_class_inactivated_b2b", $dataSoftDelete, "vehicle_class_inactivated_id = $id and status=1");            

            if($b2b == 't')
            {
                $getMerchant = array_filter(array_map(function($x){
                    return $this->enc->decode(trim($x));
                }, explode(",",$merchant)), function($f){
                    return $f !="";
                });  
    
                $dataB2b=array();
                foreach ($getMerchant as $key => $value) {
                    $dataB2b[]=array(
                        "merchant_id"=>$value,
                        "vehicle_class_inactivated_id"=>$id,
                        "ship_class"=>$ship_class_id,
                        "status"=> 1,
                        "created_on"=> date("Y-m-d H:i:s"),
                        "created_by"=>$this->session->userdata("username"),
                    );
                }
    
                $this->vehicleInactivatedModel->insert_data_batch("app.t_mtr_vehicle_class_inactivated_b2b", $dataB2b);            
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
        $logUrl      = site_url().'master_data/vehicle_inactivated/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data = array(
            'status'        => -5,
            'updated_on'    => date("Y-m-d H:i:s"),
            'updated_by'    => $this->session->userdata('username'),
        );

        $this->db->trans_begin();
        $this->vehicleInactivatedModel->update_data($this->_table, $data, "id = ".$this->enc->ddecode($id)."");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal delete data');
        }
        else {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil delete data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/vehicle_inactivated/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
}