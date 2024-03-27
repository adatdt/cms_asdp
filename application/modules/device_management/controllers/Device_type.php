<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );


class Device_type extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('M_devicetype','device');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_device_terminal_type';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'device_management/device_type';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('service')){
                $this->form_validation->set_rules('service', 'service', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid service'));
            }
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->device->dataList();
            $rows["tokenHash"] = $this->security->get_csrf_hash();
            $rows["csrfName"] = $this->security->get_csrf_token_name();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Tipe Perangkat',
            'content'  => 'device_type/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'btn_excel'=> generate_button_new($this->_module, 'import_excel',  site_url($this->_module.'/import_excel')),
            'port' =>$this->device->select_data("app.t_mtr_port","where status='1' order by name asc")->result(),
            'ship' =>$this->device->select_data("app.t_mtr_ship","where status='1' order by name asc")->result(),
            'dock' =>$this->device->select_data("app.t_mtr_dock","where status='1' order by name asc")->result(),
            'service'=> $this->device->select_data("app.t_mtr_service","where status='1' order by name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Tipe Perangkat';
        $data['service']=$this->device->select_data("app.t_mtr_service","where status='1' order by name asc")->result();
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $this->form_validation->set_rules('device_type', 'device type', 'required|max_length[50]|callback_special_char', array('special_char' => 'nama device memuat invalid karakter'));
        $this->form_validation->set_rules('service', 'service', 'required');
        $this->form_validation->set_rules('channel', 'channel', 'required|max_length[50]|callback_special_char', array('special_char' => 'nama device memuat invalid karakter'));
        $this->form_validation->set_message('required','%s harus diisi!');
        
        if($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
            exit;
        }

        $device_type=trim($this->input->post('device_type'));
        $channel=trim($this->input->post('channel'));
        $service_id=$this->enc->decode($this->input->post('service'));


        $check_channel =$this->device->select_data($this->_table," where upper(channel)=upper('".$channel."')");

        $data=array(
                    'terminal_type_name'=>$device_type,
                    'channel'=>$channel,
                    'service_id'=>$service_id,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    );


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        // else if($check_channel->num_rows()>0)
        // {
        //     echo $res=json_api(0, 'Nama channel sudah ada');   
        // }
        else
        {
            $this->db->trans_begin();
            $this->device->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'device_management/device_type/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $terminal_id=$this->enc->decode($id);

        $data['title'] = 'Edit Tipe Perangkat';
        $data['service']=$this->device->select_data("app.t_mtr_service","where status='1' order by name asc")->result();
        $data['detail']=$this->device->select_data($this->_table,"where terminal_type_id='".$terminal_id."' ")->row();
        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        $this->form_validation->set_rules('device_type_id', 'device_type_id', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid id'));
        $this->form_validation->set_rules('device_type', 'device type', 'required|max_length[50]|callback_special_char', array('special_char' => 'nama device memuat invalid karakter'));
        $this->form_validation->set_rules('service', 'service', 'required');
        $this->form_validation->set_message('required','%s harus diisi!');
        
        if($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
            exit;
        }

        $device_type_id=$this->enc->decode($this->input->post('device_type_id'));

        $device_type=trim($this->input->post('device_type'));
        $channel=trim($this->input->post('channel'));
        $service_id=$this->enc->decode($this->input->post('service'));

                
        $data=array(
                    'terminal_type_name'=>$device_type,
                    'channel'=>$channel,
                    'service_id'=>$service_id,
                    'updated_on'=>date("Y-m-d H:i:s"),
                    'updated_by'=>$this->session->userdata('username'),
                    );

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else
        {
            $this->db->trans_begin();
            $this->device->update_data($this->_table,$data,"terminal_type_id=$device_type_id");

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data ');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data');
            }   
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'device_management/device_type/action_edit';
        $logMethod   = 'EDIT';
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

        $terminal_type_id = $this->enc->decode($id);

        $check_terminal=$this->device->select_data("app.t_mtr_device_terminal"," where status not in (-5) and terminal_type=".$terminal_type_id." ");

        if($check_terminal->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal delete data, data sudah terpairing ');
        }
        else
        {
            $this->db->trans_begin();
            $this->device->update_data($this->_table,$data,"terminal_type_id=".$terminal_type_id);

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
        $logUrl      = site_url().'pelabuhan/schedule/action_delete';
        $logMethod   = 'DELETE';
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
        $this->team_model->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal non aktif');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil non aktif data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    private function create_code()
    {
        $data=$this->db->query("SELECT 
                    SUBSTRING(EXTRACT(YEAR FROM now())::varchar, 3,2)||
                     to_char(EXTRACT(DAY FROM now()), 'fm000')|| 
                    (to_char(nextval('core.t_mtr_team_code_seq'), 'fm0000')) as code ")->row();

        return $data->code;

    }

    public function enable($param)
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
            $this->device->update_data($this->_table,$data,"terminal_type_id=".$d[0]);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal aktifkan data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil aktifkan data');
            }


        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/enable';
        $logMethod   = 'Enable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function disable($param)
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
        $this->device->update_data($this->_table,$data,"terminal_type_id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal dinonaktifkan data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Data berhasil dinonaktifkan ');
        } 

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/disable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function get_dock()
    {
        $port_id=$this->enc->decode($this->input->post('port'));

        $row=$this->schedule->select_data("app.t_mtr_dock","where status=1 and port_id=".$port_id." order by name asc")->result();

        $data=array();
        foreach ($row as $key => $value) {
            $value->id=$this->enc->encode($value->id);
            $value->name=strtoupper($value->name);

            $data[]=$value;
        }

        echo json_encode($data);
    }

    private function createCode($port)
    {
        $front_code="J".$port."".date('ymd');

        $chekCode=$this->db->query("select * from app.t_mtr_schedule where left(schedule_code,8)='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (schedule_code) as max_code from app.t_mtr_schedule where left(schedule_code,8)='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, 8, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

}
