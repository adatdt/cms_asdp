<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Shift extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('shift_model');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_shift';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'shift_management/shift';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->shift_model->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Shift',
            'content'  => 'shift/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Shift';
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        

        $this->form_validation->set_rules('shift', 'shift', 'trim|required|max_length[50]|callback_special_char', array('special_char' => 'shift mengandung invalid karakter'));
        $this->form_validation->set_rules('login_shift', 'jam awal', 'trim|required');
        $this->form_validation->set_rules('logout_shift', 'jam akhir', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('max_length','{field} Maximal  {param} karakter! ');


        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {
            $shift=trim($this->input->post('shift'));
            $login_shift=trim($this->input->post('login_shift'));
            $logout_shift=trim($this->input->post('logout_shift'));

            $data=array(
                'shift_name'=>$shift,
                'shift_login'=>$login_shift,
                'shift_logout'=>$logout_shift,
                'status'=>1,
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata('username'),
            );
    
            $night = $this->input->post('night');
            if(isset($night)){
                $data['night'] = true;
            }
    
            $check_nama=$this->shift_model->select_data("app.t_mtr_shift","where upper(shift_name)=upper('$shift') and status not in (-5)");

            if($check_nama->num_rows()>0)
            {
                echo $res=json_api(0, 'Nama sudah ada');
            }
            else
            {
                $this->db->trans_begin();
                $this->shift_model->insert_data('app.t_mtr_shift',$data);

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
        $logUrl      = site_url().'shift_management/shift/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $shift_id=$this->enc->decode($id);

        $data['title'] = 'Edit Shift';
        $data['detail']= $this->shift_model->select_data($this->_table,"where id=$shift_id and status not in (-5)")->row();
        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $this->form_validation->set_rules('shift', 'shift', 'trim|required|max_length[50]|callback_special_char', array('special_char' => 'shift mengandung invalid karakter'));
        $this->form_validation->set_rules('login_shift', 'jam awal', 'trim|required');
        $this->form_validation->set_rules('logout_shift', 'jam akhir', 'trim|required');
        $this->form_validation->set_rules('id', 'id', 'required');

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('max_length','{field} Maximal  {param} karakter! ');


        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {
            $shift=trim($this->input->post('shift'));
            $login_shift=trim($this->input->post('login_shift'));
            $logout_shift=trim($this->input->post('logout_shift'));
            $shift_id=$this->enc->decode(trim($this->input->post('id')));

            $data=array(
                'shift_name'=>$shift,
                'shift_login'=>$login_shift,
                'shift_logout'=>$logout_shift,
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );
    
            $night = $this->input->post('night');
            if(isset($night)){
                $data['night'] = true;
            }else{
                $data['night'] = false;
            }
    
            $check_nama=$this->shift_model->select_data($this->_table,"where upper(shift_name)=upper('$shift') and status not in (-5) and id !=$shift_id");

            if($check_nama->num_rows()>0)
            {
                echo $res=json_api(0, 'Nama sudah ada');
            }
            else
            {
                $this->db->trans_begin();
                $this->shift_model->update_data($this->_table,$data,"id=$shift_id");

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
        $logUrl      = site_url().'shift_management/shift/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');
        $shift_id = $this->enc->decode($id);

        $data = [];
        if(!$shift_id || empty($shift_id)){
            echo $res=json_api(0, 'Gagal delete data');            
        }
        else{

        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );


            $this->db->trans_begin();
            $this->shift_model->update_data($this->_table,$data,"id=$shift_id");

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
        $logUrl      = site_url().'shift_management/shift/action_delete';
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

            $check_enable=$this->shift_model->select_data($this->_table,"where upper(shift_name)=upper('".$d[2]."') and status=1");

            if($check_enable->num_rows()>0)
            {
                echo $res=json_api(0, 'Gagal aktifkan data, Sudah ada nama yang aktif');
            }
            else
            {
                $this->db->trans_begin();
                $this->shift_model->update_data($this->_table,$data,"id=".$d[0]);

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
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/shift/enable';
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
            $this->shift_model->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'shift_management/shift/enable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
