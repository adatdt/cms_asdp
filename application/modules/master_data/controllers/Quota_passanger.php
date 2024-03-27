<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Quota_passanger extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_quota_passanger','quota');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_booking_quota_passanger';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/quota_passanger';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->quota->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Quota Penumpang',
            'content'  => 'quota_passanger/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'port' =>$this->quota->select_data("app.t_mtr_port"," where status not in (-5) order by name asc ")->result(),
            'ship_class' =>$this->quota->select_data("app.t_mtr_ship_class"," where status not in (-5) order by name asc ")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Quota Penumpang';
        $data['port'] = $this->quota->select_data("app.t_mtr_port"," where status=1 order by name asc")->result();
        $data['ship_class'] = $this->quota->select_data("app.t_mtr_ship_class"," where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $value=trim($this->input->post('value'));
        $port=$this->enc->decode($this->input->post('port'));
        $ship_class=$this->enc->decode($this->input->post('ship_class'));

        $this->form_validation->set_rules('value', 'Value', 'required|integer');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('ship_class', 'ship_class', 'required');

        $this->form_validation->set_message('required','%s harus diisi!')
        ->set_message('integer','%s Harus angka!');
        
        $data=array(
                    'param_value'=>$value,
                    'port_id'=>$port,
                    'ship_class'=>$ship_class,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // ceck data jika port dan class kapal sudah ada 
        $check=$this->quota->select_data($this->_table," where port_id=$port and ship_class=$ship_class and status not in (-5) ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Parameter sudah ada.");
        }
        else
        {

            $this->db->trans_begin();

            $this->quota->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/quota_passanger/action_add';
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

        $data['title'] = 'Edit Quota Penumpang';
        $data['detail']=$this->quota->select_data($this->_table,"where id=$id_decode")->row();
        $data['port'] = $this->quota->select_data("app.t_mtr_port"," where status=1 order by name asc")->result();
        $data['ship_class'] = $this->quota->select_data("app.t_mtr_ship_class"," where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $value=trim($this->input->post('value'));
        $port=$this->enc->decode($this->input->post('port'));
        $ship_class=$this->enc->decode($this->input->post('ship_class'));
        $id=$this->enc->decode($this->input->post('id'));

        $this->form_validation->set_rules('value', 'Value', 'required|integer');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('ship_class', 'ship_class', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');


        $this->form_validation->set_message('required','%s harus diisi!');

        // ceck data jika port dan class kapal sudah ada 
        $check=$this->quota->select_data($this->_table," where port_id=$port and ship_class=$ship_class and status not in (-5) and id !=$id");

        $data=array(
                    'param_value'=>$value,
                    // 'port_id'=>$port,
                    'ship_class'=>$ship_class,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0, 'Parameter sudah ada.');
        }
        else
        {

            $this->db->trans_begin();

            $this->quota->update_data($this->_table,$data,"id=$id");

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


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/quota_passanger/action_edit';
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
        $this->quota->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'master_data/quota_passanger/action_change';
        $d[1]==1?$logMethod='DISABLE':$logMethod='ENABLE';
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
        $this->quota->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'master_data/quota_passanger/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


}
