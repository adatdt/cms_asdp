<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Sailing_company extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('M_sailing_company','sailing');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_sailing_company';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/sailing_company';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->sailing->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Pelayaran',
            'content'  => 'sailing_company/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            // 'port'=>$this->sailing->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
            'port'=>$this->sailing->select_data("app.t_mtr_port","where status!='-5' order by name asc")->result(),
            'company'=>$this->sailing->select_data("app.t_mtr_ship_company","where status=1 order by name asc ")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Pelayaran';
        // $data['port']=$this->sailing->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['port']=$this->sailing->select_data("app.t_mtr_port","where status!='-5' order by name asc")->result();
        $data['company']=$this->sailing->select_data("app.t_mtr_ship_company","where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $segment=trim($this->input->post('segment'));
        $segment_code=trim($this->input->post('segment_code'));
        $company=$this->enc->decode($this->input->post('company'));
        $port_id=$this->enc->decode($this->input->post('port'));



        $this->form_validation->set_rules('segment', 'Nama Pelayaran', 'required');
        $this->form_validation->set_rules('segment_code', 'Kode Pelayaran', 'required');
        $this->form_validation->set_rules('company', 'Perusahaan Kapal', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $data=array('port_id'=>$port_id,
                    'segment'=>$segment,
                    'segment_code'=>$segment_code,
                    'company_id'=>$company,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date('Y-m-d H:i:s'),
                    );

        $check_name_segment=$this->sailing->select_data($this->_table," where status not in (-5) and upper(segment)=upper('".$segment."')");

        $check_company=$this->sailing->select_data($this->_table," where status not in (-5) and company_id={$company} and port_id={$port_id} ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check_name_segment->num_rows()>0)
        {
            echo $res=json_api(0, 'Nama Sudah Digunakan');
        }
        else if($check_company->num_rows()>0)
        {
            echo $res=json_api(0, 'Perusahaan sudah ada');
        }
        else
        {
            $this->db->trans_begin();
            
            $this->sailing->insert_data($this->_table,$data);
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
        $logUrl      = site_url().'master_data/sailing_company/action_add';
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
        $data['title'] = 'Edit Dermaga';
        $data['port']=$this->sailing->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        // $data['port']=$this->sailing->select_data("app.t_mtr_port","where status!='-5' order by name asc")->result();
        $data['id']=$id;
        $data['company']=$this->sailing->select_data("app.t_mtr_ship_company","where status=1 order by name asc")->result();
        $data['detail']=$this->sailing->select_data($this->_table," where id=".$id_decode)->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=$this->enc->decode($this->input->post('id'));

        $segment=trim($this->input->post('segment'));
        $segment_code=trim($this->input->post('segment_code'));
        $company=$this->enc->decode($this->input->post('company'));
        $port_id=$this->enc->decode($this->input->post('port'));



        $this->form_validation->set_rules('id', 'Nama Pelayaran', 'required');
        $this->form_validation->set_rules('segment', 'Nama Pelayaran', 'required');
        $this->form_validation->set_rules('segment_code', 'Kode Pelayaran', 'required');
        $this->form_validation->set_rules('company', 'Perusahaan Kapal', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');


        $this->form_validation->set_message('required','%s harus diisi!');

        $data=array(
                    'port_id'=>$port_id,
                    'segment'=>$segment,
                    'segment_code'=>$segment_code,
                    'company_id'=>$company,
                    'updated_by'=>$this->session->userdata("username"),
                    'updated_on'=>date("Y-m-d H:i:s"),
                );

        $check_name_segment=$this->sailing->select_data($this->_table," where status not in (-5) and upper(segment)=upper('".$segment."') and id !={$id} ");

        $check_company=$this->sailing->select_data($this->_table," where status not in (-5) and company_id={$company} and port_id={$port_id} and id !={$id} ");

        // echo print_r($data); exit;

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check_name_segment->num_rows()>0)
        {
            echo $res=json_api(0, 'Nama Sudah Digunakan');
        }
        else if($check_company->num_rows()>0)
        {
            echo $res=json_api(0, 'Perusahaan sudah ada');
        }
        else
        {
            $this->db->trans_begin();
            $this->sailing->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'masterdata/sailing_company/action_edit';
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

        $id_decode = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->sailing->update_data($this->_table,$data,"id='".$id_decode."'");

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
        $logUrl      = site_url().'master_data/sailing_company/action_delete';
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


        $this->db->trans_begin();
        $this->sailing->update_data($this->_table,$data,"id=".$d[0]);

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
        $this->sailing->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'master_data/sailing_company/disable';
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
