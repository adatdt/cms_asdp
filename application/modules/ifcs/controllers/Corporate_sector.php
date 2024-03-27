<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Corporate_sector extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_corporate_sector','corporate');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_business_sector_ifcs';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'ifcs/corporate_sector';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->corporate->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Corporate Sector IFCS',
            'content'  => 'corporate_sector/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Corporate Sector';

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $name=trim($this->input->post('name'));

        $this->form_validation->set_rules('name', 'Nama Corporate Sector', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        
        $data=array(
                    'business_sector_code'=>$this->createCode(),
                    'description'=>$name,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        $check=$this->corporate->select_data($this->_table, " where upper(description)=upper('{$name}') and status !='-5' ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama Corporate sudah ada");
        }
        else
        {

            $this->db->trans_begin();

            $this->corporate->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'ifcs/corporate_sector/action_add';
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

        $data['title'] = 'Edit Corporate Sector';
        $data['id'] = $id;
        $data['detail']=$this->corporate->select_data($this->_table,"where id=$id_decode")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('corporate_sector'));

        $name=trim($this->input->post('name'));

        $this->form_validation->set_rules('name', 'Nama Corporate Sector', 'required');


        $this->form_validation->set_message('required','%s harus diisi!');

        $data=array(
                    
                    'description'=>$name,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        $check=$this->corporate->select_data($this->_table, " where upper(description)=upper('{$name}') and status !='-5' and id !='{$id}' ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama Corporate Sector sudah ada");
        }
        else
        {

            // print_r($data); exit;
            $this->db->trans_begin();

            $this->corporate->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'ifcs/corporate_sector/action_edit';
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
        $this->corporate->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'ifcs/corporate_sector/action_change';
        $d[1]==1?$logMethod='ENABLE':$logMethod='DISABLE';
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
        $this->corporate->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'ifcs/corporate_sector/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function detail(){   

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Corporate IFCS',
            'content'  => 'corporate/detail',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
        );

        $this->load->view('default', $data);
    }    

    function createCode()
    {
        $front_code="CPS".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_business_sector_ifcs where left(business_sector_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $code=$front_code."001";
            return $code;
        }
        else
        {
            $max=$this->db->query("select max (business_sector_code) as max_code from app.t_mtr_business_sector_ifcs where left(business_sector_code,".$total_length.")='".$front_code."' ")->row();

            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%03s", $noUrut);
            return $kode;
        }
    }    

}
