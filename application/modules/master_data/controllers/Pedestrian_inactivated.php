<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Pedestrian_inactivated extends MY_Controller{
	public function __construct() {
		parent::__construct();

        logged_in();
        $this->load->model('M_pedestrian_inactivated', 'pedestrian');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_pedestrian_inactivated';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/pedestrian_inactivated';
	}

	public function index() { 
        checkUrlAccess(uri_string(),'view');

        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->pedestrian->dataList();            
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'          => 'Home',
            'url_home'      => site_url('home'),
            'title'         => 'Inaktivasi Pejalan Kaki',
            'content'       => 'pedestrian_inactivated/index',
            'btn_add'       => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'port' 	        => $this->pedestrian->select_data("app.t_mtr_port","WHERE status NOT IN (-5) ORDER BY name ASC")->result(),
            'ship_class'    => $this->pedestrian->select_data("app.t_mtr_ship_class","WHERE status NOT IN (-5) ORDER BY name ASC")->result(),
        );
		$this->load->view('default', $data);
	}

    public function add() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title']      = 'Tambah Kelas Layanan Inaktif';
        $data['port']       = $this->global_model->select_data("app.t_mtr_port", "WHERE status = '1' ORDER BY name ASC")->result();
        $data['ship_class'] = $this->global_model->select_data("app.t_mtr_ship_class", "WHERE status = '1' ORDER BY name ASC")->result();
        $data['merchant'] = $this->global_model->select_data("app.t_mtr_merchant", "WHERE status = '1' ORDER BY merchant_name ASC")->result();

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port_id            = $this->enc->decode($this->input->post('port_id', true));
        $ship_class_id      = $this->enc->decode($this->input->post('ship_class_id', true));
        $merchant   = $this->input->post('merchant', true);

        $start_date = $this->input->post('start_date', true);
        $end_date   = $this->input->post('end_date', true);
        $web                = $this->input->post('web', true);
        $mobile             = $this->input->post('mobile', true);
        $b2b                = $this->input->post('b2b', true);
        $ifcs               = $this->input->post('ifcs', true);
        $web_cs             = $this->input->post('web_cs', true);

        $_POST['port_id'] = $port_id;
        $_POST['ship_class_id'] = $ship_class_id;                

        $web === "yes" ? $web = "t" : $web = "f";
        $mobile === "yes" ? $mobile = "t" : $mobile = "f";
        $b2b === "yes" ? $b2b = "t" : $b2b = "f";
        $ifcs === "yes" ? $ifcs = "t" : $ifcs = "f";
        $web_cs === "yes" ? $web_cs = "t" : $web_cs = "f";

        $this->form_validation->set_rules('port_id', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('ship_class_id', 'Kelas Layanan ', 'required');
        $this->form_validation->set_rules('start_date', 'Tanggal Mulai ', 'required|callback_validate_date_time');
        $this->form_validation->set_rules('end_date', 'Tanggal Selesai ', 'required|callback_validate_date_time');
        if($b2b == "t")
        {
            $this->form_validation->set_rules('merchant', 'Merchant', 'required');
        }

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('validate_date_time','%s Format Tanggal tidak sesuai !');      
        
        if($this->form_validation->run()===false) 
        {
        	echo $res=json_api(0,validation_errors());
            exit;
        }        

        $data = array(
        	'port_id'           => $port_id,
        	'ship_class'        => $ship_class_id,
			'start_date'        => $start_date,
			'end_date'          => $end_date,
        	'web'               => $web,
        	'mobile'            => $mobile,
        	'b2b'               => $b2b,
			'ifcs'              => $ifcs,
			'web_cs'            => $web_cs,
			'created_on'        => date("Y-m-d H:i:s"),
        	'created_by'        => $this->session->userdata('username'),
        );
        
        // $check = $this->pedestrian->check_data($vehicle_class_id, $port_id, $ship_class_id);
        // $check = FALSE;

        // if($this->form_validation->run()===false) 
        // {
        // 	echo $res=json_api(0,validation_errors());
        // }
        // else {
            $this->db->trans_begin();
        	// $this->pedestrian->insert_data($this->_table, $data);
            $getId = $this->pedestrian->insert_data_id($this->_table, $data);
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
                        "pedestrian_inactivated_id"=>$getId,
                        "status"=> 1,
                        "created_on"=> date("Y-m-d H:i:s"),
                        "created_by"=>$this->session->userdata("username"),
                    );
                }

                $this->pedestrian->insert_data_batch("app.t_mtr_pedestrian_inactivated_b2b", $dataB2b);
            }

        	if ($this->db->trans_status() === FALSE) {
        		$this->db->trans_rollback();
        		echo $res=json_api(0, 'Gagal tambah data');
        	}
        	else {
        		$this->db->trans_commit();
        		echo $res=json_api(1, 'Berhasil tambah data');
        	}
        // }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/pedestrian_inactivated/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id = $this->enc->decode($id);

        $data['title']      = 'Edit Kelas Layanan Inaktif';
        $data['detail']     = $this->pedestrian->get_edit($id);
        $data['port']       = $this->global_model->select_data("app.t_mtr_port", "WHERE status = '1' ORDER BY name ASC")->result();
        $data['ship_class'] = $this->global_model->select_data("app.t_mtr_ship_class","WHERE status = '1' ORDER BY name ASC")->result();

        $data['merchant'] = $this->global_model->select_data("app.t_mtr_merchant", "WHERE status = '1' ORDER BY merchant_name ASC")->result();
        $data['detailMerchant'] = $this->pedestrian->getDetailMerchant(" where a.pedestrian_inactivated_id=".$id." and a.status=1");

        $merchatId = array_column($data['detailMerchant'],"merchant_id");
        $data["dataSelected"] = array_combine($merchatId, $merchatId);
        $data["merchantString"] = array_map(function($x){ return $this->enc->encode($x); },$merchatId);        

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id = $this->enc->decode($this->input->post('id', true));

        $port_id            = $this->enc->decode(trim($this->input->post('port_id', true)));
        $ship_class_id      = $this->enc->decode(trim($this->input->post('ship_class_id', true)));
        $merchant   = $this->input->post('merchant');

        $_POST['id'] = $id;
        $_POST['port_id'] = $port_id;
        $_POST['ship_class_id'] = $ship_class_id;

        $start_date         = trim($this->input->post('start_date', true));
        $end_date           = trim($this->input->post('end_date', true));
        $web                = trim($this->input->post('web', true));
        $mobile             = trim($this->input->post('mobile', true));
        $b2b                = trim($this->input->post('b2b', true));
        $ifcs               =trim($this->input->post('ifcs', true));
		$web_cs             = trim($this->input->post('web_cs', true));

        $web === "yes" ? $web = "t" : $web = "f";
        $mobile === "yes" ? $mobile = "t" : $mobile = "f";
        $b2b === "yes" ? $b2b = "t" : $b2b = "f";
        $ifcs === "yes" ? $ifcs = "t" : $ifcs = "f";
        $web_cs === "yes" ? $web_cs = "t" : $web_cs = "f";        

        $this->form_validation->set_rules('port_id', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('id', 'Id ', 'required');
        $this->form_validation->set_rules('ship_class_id', 'Kelas Layanan ', 'required');
        $this->form_validation->set_rules('start_date', 'Kelas Layanan ', 'required|callback_validate_date_time');
        $this->form_validation->set_rules('end_date', 'Kelas Layanan ', 'required|callback_validate_date_time');
        if($b2b == "t")
        {
            $this->form_validation->set_rules('merchant', 'Merchant', 'required');
        }       

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('validate_date_time','%s Format Tanggal tidak sesuai !');        

        if($this->form_validation->run()===false)
        {
        	echo $res=json_api(0,validation_errors());
            exit;
        }        

        $data = array(
        	'port_id'           => $port_id,
        	'ship_class'        => $ship_class_id,
			'start_date'        => $start_date,
			'end_date'          => $end_date,
        	'web'               => $web,
        	'mobile'            => $mobile,
        	'b2b'               => $b2b,
			'ifcs'              => $ifcs,
			'web_cs'            => $web_cs,
			'updated_on'        => date("Y-m-d H:i:s"),
        	'updated_by'        => $this->session->userdata('username'),
        );

        // $check = $this->pedestrian->check_data($vehicle_class_id, $port_id, $ship_class_id, $id);
        // $check = FALSE;

        // if($this->form_validation->run()===false)
        // {
        // 	echo $res=json_api(0,validation_errors());
        // }
        // else {
            $this->db->trans_begin();
        	$this->pedestrian->update_data($this->_table, $data, "id = $id");
            $dataSoftDelete=array(
                "status"=>"-5",
                "updated_on"=>date("Y-m-d H:i:s"),
                "updated_by"=>$this->session->userdata("username")
            );  
            $this->pedestrian->update_data("app.t_mtr_pedestrian_inactivated_b2b", $dataSoftDelete, "pedestrian_inactivated_id = $id and status=1");      
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
                        "pedestrian_inactivated_id"=>$id,
                        "status"=> 1,
                        "created_on"=> date("Y-m-d H:i:s"),
                        "created_by"=>$this->session->userdata("username"),
                    );
                }

                $this->pedestrian->insert_data_batch("app.t_mtr_pedestrian_inactivated_b2b", $dataB2b);
            }                                     

        	if ($this->db->trans_status() === FALSE) {
        		$this->db->trans_rollback();
        		echo $res=json_api(0, 'Gagal edit data ');
        	}
        	else {
        		$this->db->trans_commit();
        		echo $res=json_api(1, 'Berhasil edit data');
        	}   
        // }

        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/pedestrian_inactivated/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

				$id = $this->enc->decode($id);

        $data = array(
            'status'        => -5,
            'updated_on'    => date("Y-m-d H:i:s"),
            'updated_by'    => $this->session->userdata('username'),
        );

        $this->db->trans_begin();
        $this->pedestrian->update_data($this->_table, $data, "id = $id");

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
        $logUrl      = site_url().'master_data/pedestrian_inactivated/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
}