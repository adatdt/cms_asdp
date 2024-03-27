<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Reschedule extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_reschedule','schedule');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_reschedule';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/reschedule';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->schedule->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Reschedule',
            'content'  => 'reschedule/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'service'  => $this->schedule->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'port'=>$this->schedule->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
            'team'=>$this->schedule->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Dermaga';
        $data['port']=$this->dock->select_data("app.t_mtr_port","where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $dock=trim($this->input->post('dock'));
        $port_id=$this->enc->decode($this->input->post('port'));


        $this->form_validation->set_rules('dock', 'dock', 'required');
        $this->form_validation->set_rules('port', 'port', 'required');

        $data=array('port_id'=>$port_id,
                    'name'=>$dock,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date('Y-m-d'),
                    );

        $check_dock=$this->dock->select_data($this->_table," where status=1 and upper(name)=upper('".$dock."') and port_id=".$port_id);

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if($check_dock->num_rows()>0)
        {
            echo $res=json_api(0, 'Nama Sudah Digunakan');
        }
        else
        {
            $this->db->trans_begin();
            
            $this->dock->insert_data($this->_table,$data);
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
        $logUrl      = site_url().'pelabuhan/dock/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
    

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $dock_id=$this->enc->decode($id);
        $data['title'] = 'Edit Assignment user pos';
        $data['port']=$this->dock->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['detail']=$this->dock->select_data($this->_table," where id=".$dock_id)->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $dock_id=$this->enc->decode($this->input->post('dock'));
        $dock_name=trim($this->input->post('dock_name'));

        $this->form_validation->set_rules('dock', 'Dermaga', 'required');
        $this->form_validation->set_rules('dock_name', 'Dermaga', 'required');

        $data=array(
                    'name'=>$dock_name,
                    'updated_by'=>$this->session->userdata("username"),
                    'updated_on'=>date("Y-m-d"),
                );



        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Gagal edit data');
        }
        else
        {
            $this->db->trans_begin();
            $this->dock->update_data($this->_table,$data,"id=$dock_id");

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

        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $dock_id = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->dock->update_data($this->_table,$data,"id='".$dock_id."'");

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal hapus data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil hapus data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
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

        $check_enable=$this->dock->select_data($this->_table,"where upper(name)=upper('".$d[2]."') and port_id=".$d[3]." and status=1");

        if($check_enable->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal aktifkan data, Sudah ada nama yang aktif');
        }
        else
        {
            $this->db->trans_begin();
            $this->dock->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'pelabuhan/dock/enable';
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
        $this->dock->update_data($this->_table,$data,"id=".$d[0]);

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

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/dock/enable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function get_dock()
    {
        $port=$this->enc->decode($this->input->post('port'));

        empty($port)?$port_id='NULL':$port_id=$port;
        $dock=$this->dock->select_data($this->_table,"where port_id=".$port_id." and status=1")->result();

        $data=array();
        foreach($dock as $key=>$value)
        {
            $value->id=$this->enc->encode($value->id);
            $data[]=$value;            
        }

         echo json_encode($data);
    }

}