<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class PortConfig extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('portConfigModel','port');
        $this->load->model('global_model');

        $this->_table    = 'app.t_mtr_port_config';
        $this->_username = $this->session->userdata('username');
        $this->_module = 'pelabuhan/portConfig';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('port')){
                $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
            }
            
            
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->port->portList();
            echo json_encode($rows);
            exit;
        }

        $getPort = $this->getPort();
        $port[""]="Pilih";
        foreach($getPort as $key => $getPort2 )
        {
            $port[$this->enc->encode($getPort2->id)]=$getPort2->name;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Port Config',
            'content'  => 'portConfig/index',
            'port' => $port,
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $getPort = $this->getPort();
        
        $port[""]="Pilih";
        foreach($getPort as $key => $getPort2 )
        {
            $port[$this->enc->encode($getPort2->id)]=$getPort2->name;
        }

        $data['title'] = 'Tambah Port Config';
        $data['port'] = $port;
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        /* validation */
        $this->form_validation->set_rules('name', 'Nama', 'trim|required|callback_special_char', array('special_char' => 'nama mengandung invalid karakter'));
        $this->form_validation->set_rules('configGroup', 'Config Group', 'trim|required|callback_special_char', array('special_char' => 'config group mengandung invalid karakter'));
        $this->form_validation->set_rules('valueData', 'Value', 'trim|required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        


        $this->form_validation->set_message('required','%s harus diisi!');
        /* data post */

        $data = null;
        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else 
        {
            $name = trim($this->input->post("name"));
            $configGroup = trim($this->input->post("configGroup"));
            $valueData = trim($this->input->post("valueData"));
            $port = trim($this->enc->decode($this->input->post("port")));

                
            $data = array(
                'port_id' => $port,
                'config_group' => $configGroup,
                'config_name' => $name,
                'value' => $valueData,
                'status' => 1,
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata('username'),
            );

            $whereCheck ="
                where status not in (-5)
                and config_group='{$configGroup}'
                and config_name='{$name}'
                and port_id = '{$port}'
            ";
            $check = $this->port->select_data($this->_table, $whereCheck);

            if($check->num_rows()>0)
            {
                echo $res =  json_api(0," Config tidak boleh sama dalam satu Pelabuhan "); 
            }
            else
            {
                $this->db->trans_begin();

                $this->port->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'pelabuhan/portConfig/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($param){
        validate_ajax();
        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $decode_id=$this->enc->decode($param);
        $getPort = $this->getPort();

        $getDetail = $this->port->select_data($this->_table, " where id='{$decode_id}' ")->row();
        
        $port[""]="Pilih";
        $portSelected ="";
        foreach($getPort as $key => $getPort2 )
        {
            $idEncode = $this->enc->encode($getPort2->id);
            if($getPort2->id == $getDetail->port_id)
            {
                $portSelected = $this->enc->encode($getPort2->id);
                $idEncode = $portSelected;
            }
            $port[$idEncode]=$getPort2->name;
        }

 
        $data['title'] = 'Edit Port Config';
        $data['port'] = $port;
        $data['portSelected'] = $portSelected;
        $data['dataDetail'] = $getDetail;

        $this->load->view($this->_module.'/edit',$data);
    }

    public function action_edit(){
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        /* validation */
        $this->form_validation->set_rules('name', 'Nama', 'trim|required|callback_special_char', array('special_char' => 'nama mengandung invalid karakter'));
        $this->form_validation->set_rules('id', 'Id', 'trim|required');
        $this->form_validation->set_rules('configGroup', 'Config Group', 'trim|required|callback_special_char', array('special_char' => 'config group mengandung invalid karakter'));
        $this->form_validation->set_rules('valueData', 'Value', 'trim|required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        

        $this->form_validation->set_message('required','%s harus diisi!');

        $data = null;
        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else 
        {
            $id = $this->enc->decode($this->input->post("id"));
            $name = trim($this->input->post("name"));
            $configGroup = trim($this->input->post("configGroup"));
            $valueData = trim($this->input->post("valueData"));
            $port = trim($this->enc->decode($this->input->post("port")));

            $data = array(
                'port_id' => $port,
                'config_group' => $configGroup,
                'config_name' => $name,
                'value' => $valueData,
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );
    
            /* data post */
            $whereCheck ="
                where status not in (-5)
                and config_group='{$configGroup}'
                and config_name='{$name}'
                and port_id = '{$port}'
                and id <>'{$id}'
            ";        
            $check = $this->port->select_data($this->_table, $whereCheck);

            if($check->num_rows()>0){
                echo $res =  json_api(0,'Config tidak boleh sama dalam satu Pelabuhan '); 
            }
            else
            {
    
                $this->db->trans_begin();
    
                $this->port->update_data($this->_table,$data,"id=$id");
    
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
        $logUrl      = site_url().'pelabuhan/portConfig/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($param){
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $id   = $this->enc->decode($param);

        $data = [];
        if(!$id || empty($id)){
            echo $res=json_api(0, 'Gagal delete data');            
        }
        else{

            /* data */
            $data = array(
                'id' => $id,
                'status' => -5,
                'updated_by'=>$this->session->userdata('username'),
                'updated_on'=>date("Y-m-d H:i:s"),
            );


            $this->db->trans_begin();
            $this->port->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'pelabuhan/portConfig/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param){
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
                'id' => $d[0],
                'status' => $d[1]
            );

            $query = $this->global_model->updateData($this->_table, $data, 'id');
            if($query){
                $response = json_api(1,'Update Status Berhasil');
            }else{
                $response = json_encode($this->db->error()); 
            }

            echo $response;
        }

        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/portConfig/change_status';
        $logMethod   = $d[1]==1?"change_active":"change_non_active";
        $logParam    = json_encode($data);
        $logResponse = $response;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }
    public function getPort()
    {
        $getPort = $this->port->select_data("app.t_mtr_port"," where status=1 order by name asc");
        return $getPort->result();
    }

}
