<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Shift_time extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_shift_time','shift');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_shift_time';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'shift_management/shift_time';
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

        $get_identity=$this->shift->get_identity_app();
        if($get_identity==0)
        {
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->shift->select_data("app.t_mtr_port"," where id={$this->session->userdata('port_id')} order by name asc")->result();
                $row_port=1;
            }
            else
            {
                $port=$this->shift->select_data("app.t_mtr_port"," where status !='-5' order by name asc")->result();
                $row_port=0;
            }
        }
        else
        {
            $port=$this->shift->select_data("app.t_mtr_port"," where id={$get_identity} order by name asc")->result();
            $row_port=1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Jam Shift',
            'content'  => 'shift_time/index',
            'port'=>$port,
            'row_port'=>$row_port,
            'shift'=>$this->shift->select_data("app.t_mtr_shift"," where status !='-5' order by shift_name asc")->result(),
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // get port

        $get_identity=$this->shift->get_identity_app();
        if($get_identity==0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $port=$this->shift->select_data("app.t_mtr_port"," where status=1 order by name asc")->result();
            }
            else
            {
                $port=$this->shift->select_data("app.t_mtr_port"," where id={$this->session->userdata('port_id')} order by name asc")->result();
            }
        }
        else
        {
            $port=$this->shift->select_data("app.t_mtr_port"," where id={$get_identity} order by name asc")->result();
        }

        $data['title'] = 'Tambah Jam Shift';
        $data['port'] = $port;
        $data['shift'] = $this->shift->select_data("app.t_mtr_shift"," where status=1 order by shift_name asc")->result();
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');


        $this->form_validation->set_rules('shift', 'shift', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid shift'));
        $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        $this->form_validation->set_rules('login_shift', 'jam awal', 'trim|required');
        $this->form_validation->set_rules('logout_shift', 'jam akhir', 'trim|required');

        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else 
        {
            $shift_id=$this->enc->decode($this->input->post('shift'));
            $port_id=$this->enc->decode($this->input->post('port'));
            $login_shift=trim($this->input->post('login_shift'));
            $logout_shift=trim($this->input->post('logout_shift'));

            $data=array(
                'shift_id'=>$shift_id,
                'port_id'=>$port_id,
                'shift_login'=>$login_shift,
                'shift_logout'=>$logout_shift,
                'status'=>1,
                'night'=>$login_shift>$logout_shift?'true':'false',
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata('username'),
            );
    
            $check_nama=$this->shift->select_data("app.t_mtr_shift_time","where shift_id={$shift_id} and port_id={$port_id} and status not in (-5)");
            
            if($check_nama->num_rows()>0)
            {
                echo $res=json_api(0, 'Jam shift sudah ada');
            }
            else
            {
                $this->db->trans_begin();
                $this->shift->insert_data('app.t_mtr_shift_time',$data);

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
        $logUrl      = site_url().'shift_management/shift_time/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $shift_time_id=$this->enc->decode($id);

        $get_identity=$this->shift->get_identity_app();
        if($get_identity==0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $port=$this->shift->select_data("app.t_mtr_port"," where status=1 order by name asc")->result();
            }
            else
            {
                $port=$this->shift->select_data("app.t_mtr_port"," where id={$this->session->userdata('port_id')} order by name asc")->result();
            }
        }
        else
        {
            $port=$this->shift->select_data("app.t_mtr_port"," where id={$get_identity} order by name asc")->result();
        }


        $data['title'] = 'Edit Jam Shift';
        $data['detail']= $this->shift->select_data($this->_table,"where id={$shift_time_id} ")->row();
        $data['shift'] = $this->shift->select_data("app.t_mtr_shift"," where status=1 order by shift_name asc")->result();
        $data['port']=$port;
        $data['id']=$id;
        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $this->form_validation->set_rules('shift', 'shift', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid shift'));
        $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        $this->form_validation->set_rules('login_shift', 'jam awal', 'trim|required');
        $this->form_validation->set_rules('logout_shift', 'jam akhir', 'trim|required');
        $this->form_validation->set_rules('shift_time', 'Jam Shift', 'required');

        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {
            $shift_id=$this->enc->decode($this->input->post('shift'));
            $port_id=$this->enc->decode($this->input->post('port'));
            $login_shift=trim($this->input->post('login_shift'));
            $logout_shift=trim($this->input->post('logout_shift'));
            $shift_time_id=$this->enc->decode(trim($this->input->post('shift_time')));

            $data=array(
                'shift_id'=>$shift_id,
                'port_id'=>$port_id,
                'shift_login'=>$login_shift,
                'shift_logout'=>$logout_shift,
                'night'=>$login_shift>$logout_shift?'true':'false',
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );
    
    
            $check_nama=$this->shift->select_data("app.t_mtr_shift_time","where shift_id={$shift_id} and port_id={$port_id} and status !='-5' and id!={$shift_time_id} ");

            if($check_nama->num_rows()>0)
            {
                echo $res=json_api(0, 'Jam shift sudah ada');
            }
            else
            {
                // print_r($data); exit;

                $this->db->trans_begin();
                $this->shift->update_data($this->_table,$data,"id=$shift_time_id");

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
        $logUrl      = site_url().'shift_management/shift_time/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');
        $shift_time_id = $this->enc->decode($id);

        $data = [];
        if(!$shift_time_id || empty($shift_time_id)){
            echo $res=json_api(0, 'Gagal delete data');            
        }
        else{

        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );


            $this->db->trans_begin();
            $this->shift->update_data($this->_table,$data,"id={$shift_time_id}");

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
        $logUrl      = site_url().'shift_management/shift_time/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);

        $data = [];
        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal update status');            
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
            $this->shift_model->update_data($this->_table,$data,"id=".$d[0]);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal update status');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil update status');
            }   
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/shift/action_delete';
        $logMethod   = 'DELETE';
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
        $logUrl      = site_url().'shift_management/shift_time/enable';
        $logMethod   = 'Enable';
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
        $logUrl      = site_url().'shift_management/shift_time/disable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }



}
