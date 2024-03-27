<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Shift_vm extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_shift_vm','shift');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_shift_vending';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'shift_management/shift_vm';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('port')){
                $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
            }
            if($this->input->post('shift')){
                $this->form_validation->set_rules('shift', 'Shift', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid shift'));
            }
            
            
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->shift->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Shift Vending',
            'content'  => 'shift_vm/index',
            'port' => $this->shift->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
            'shift' => $this->shift->select_data("app.t_mtr_shift","where status=1 order by shift_name asc")->result(),
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function getTime($type)
    {
        $data=array();
        if($type=='j')
        {

            for ($i=0; $i <24 ; $i++) { 
                $getTime = sprintf("%02d", $i);
                $data[$getTime ]=$getTime;
            }
        }
        else
        {
            for ($i=0; $i <60 ; $i++) { 
                $getTime = sprintf("%02d", $i);
                $data[$getTime ]=$getTime;
            }
        }

        return $data;
    }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data=array(

        'title'=> 'Tambah Shift Vending',
        'shift'=> $this->shift->select_data("app.t_mtr_shift","where status=1 order by shift_name asc")->result(),
        'port'=> $this->shift->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
        'jam'=> $this->getTime('j'),
        'menit'=> $this->getTime('m'),
        // 'port'=> $this->shift->select_data("app.t_mtr_port","where status !='-5' order by name asc")->result(),
        );

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');


        $this->form_validation->set_rules('shift', 'shift', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid shift'));
        $this->form_validation->set_rules('device_code', 'device code', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kode perangkat'));
        $this->form_validation->set_rules('shift_logout', 'shift logout', 'trim|required');
        $this->form_validation->set_rules('shift_login', 'shift login', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!');


        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {
            $shift_id=$this->enc->decode($this->input->post('shift'));
            $device_code=$this->enc->decode($this->input->post('device_code'));
            $shift_logout=trim($this->input->post('shift_logout'));
            $shift_login=trim($this->input->post('shift_login'));

            $data=array('terminal_code'=>$device_code,
                    'shift_id'=>$shift_id,
                    'shift_login'=>$shift_login,
                    'shift_logout'=>$shift_logout,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
            );

            $check_shift=$this->shift->select_data($this->_table,"where terminal_code='$device_code' and shift_id='$shift_id' and status not in (-5)");
                
            if($check_shift->num_rows()>0)
            {
                echo $res=json_api(0, 'Data shift vending sudah ada');
            }
            else
            {

                $this->db->trans_begin();

                $this->shift->insert_data($this->_table,$data);
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
        $logUrl      = site_url().'shift_management/shift_vm/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $shift_vending_id=$this->enc->decode($id);
        $get_port_id=$this->shift->get_detail(" where a.id=".$shift_vending_id)->row();

        $detail = $this->shift->get_detail(" where a.id=".$shift_vending_id)->row();
        $data=array(
            'title'=> 'Edit Shift Vending',
            'shift'=> $this->shift->select_data("app.t_mtr_shift","where status=1 order by shift_name asc")->result(),
            'port'=> $this->shift->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
            // 'device_code'=>$this->shift->select_data("app.t_mtr_device_terminal","where status=1 and terminal_type=3 and port_id=".$get_port_id->port_id)->result(),
            'device_code'=>$this->shift->get_device_code($get_port_id->port_id),
            'detail'=>$detail,
            'jam'=> $this->getTime('j'),
            'menit'=> $this->getTime('m'),
            'jamLogin'=>date("H", strtotime($detail->shift_login)),
            'menitLogin'=>date("i", strtotime($detail->shift_login)),
            'jamLogout'=>date("H", strtotime($detail->shift_logout)),
            'menitLogout'=>date("i", strtotime($detail->shift_logout)),
        );

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $this->form_validation->set_rules('shift', 'shift', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid shift'));
        $this->form_validation->set_rules('device_code', 'device code', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kode perangkat'));
        $this->form_validation->set_rules('shift_logout', 'shift logout', 'trim|required');
        $this->form_validation->set_rules('shift_login', 'shift login', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {

            $shift_vending_id=$this->enc->decode($this->input->post('shift_vending'));

            $shift_id=$this->enc->decode($this->input->post('shift'));
            $device_code=$this->enc->decode($this->input->post('device_code'));
            $shift_logout=trim($this->input->post('shift_logout'));
            $shift_login=trim($this->input->post('shift_login'));

            $data=array(
                'terminal_code'=>$device_code,
                'shift_id'=>$shift_id,
                'shift_login'=>$shift_login,
                'shift_logout'=>$shift_logout,
                'updated_by'=>$this->session->userdata('username'),
                'updated_on'=>date("Y-m-d H:i:s"),
                );
            
            $check_shift=$this->shift->select_data($this->_table,"where terminal_code='$device_code' and shift_id='$shift_id' and status not in (-5) and id !=$shift_vending_id");
                
            if($check_shift->num_rows()>0)
            {
                echo $res=json_api(0, 'Data shift vending sudah ada');
            }
            else
            {

                $this->db->trans_begin();

                $this->shift->update_data($this->_table,$data,"id='".$shift_vending_id."'");

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
        $logUrl      = site_url().'transaction/opening_balance/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_close_balance($ob_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data=array(
            'status'=>2,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $id = $this->enc->decode($ob_code);

        $this->db->trans_begin();
        $this->balance->update_data($this->_table,$data," ob_code='".$id."'");

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
        $logUrl      = site_url().'transaction/opening_balance/action_opening_balance';
        $logMethod   = 'CLOSE BALANCE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function disable($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        
        $data = [];
        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal dinonaktifkan data');            
        }
        else{
            $d = explode('|', $p);

            /* data */
            $data = array(
                'status' => $d[1],
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );


            $this->db->trans_begin();
            $this->shift->update_data($this->_table,$data,"id=".$d[0]);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal dinonaktifkan data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil dinonaktifkan data');
            } 
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/enable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function enable($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);

        $data = [];
        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal aktifkan data');            
        }
        else{
            $d = explode('|', $p);

            /* data */
            $data = array(
                'status' => $d[1],
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );



            $this->db->trans_begin();
            $this->shift->update_data($this->_table,$data,"id=".$d[0]);

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
        }
  

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/enable';
        $logMethod   = 'Enable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');
        $id = $this->enc->decode($id);

        $data = [];
        if(!$id || empty($id)){
            echo $res=json_api(0, 'Gagal delete data');            
        }
        else{
            $data=array(
                'status'=>-5,
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
                );


            $this->db->trans_begin();
            $this->route->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'fare/route/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function get_device_code()
    {
        $port_id=$this->enc->decode($this->input->post("port"));

        // $data=array();
        // if(!empty($port_id))
        // {            
        //     $row=$this->shift->select_data("app.t_mtr_device_terminal"," where terminal_type in (3,21) and status=1 and port_id=".$port_id)->result();
        //     foreach ($row as $key => $value) {
        //         $value->full_code=$value->terminal_code." - ".$value->terminal_name;
        //         $value->terminal_code=$this->enc->encode($value->terminal_code);
        //         $value->terminal_type =
        //         $data[]=$value;
        //     }
        // }
        $data = $this->shift->get_device_code($port_id);

        echo json_encode($data); 
    }


}