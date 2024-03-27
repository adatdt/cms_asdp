<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Bank_refund extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_bank_refund','bank');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_refund_bank';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/bank_refund';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->bank->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master Bank Refund',
            'content'  => 'bank_refund/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Bank Refund';

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $abbr=$this->db->escape(trim($this->input->post('abbr',true)));
        $bank=$this->db->escape(trim($this->input->post('bank',true)));
        $transfer_fee=$this->db->escape(trim($this->input->post('transfer_fee',true)));

        $this->form_validation->set_rules('abbr', 'Bank ABBR ', 'required|max_length[10]');
        $this->form_validation->set_rules('bank', 'Nama Bank ', 'required|max_length[50]');
        $this->form_validation->set_rules('transfer_fee', 'Biaya Transfer ', 'required|is_natural');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('is_natural','%s harus angka!');
        $this->form_validation->set_message('is_natural_no_zero','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');

        
        $data=array(
                    'bank_name'=>$bank,
                    'bank_abbr'=>$abbr,
                    'transfer_fee'=>$transfer_fee,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s")
        );

        // ceck data jika username sudah ada
        $check=$this->bank->select_data($this->_table," where upper(bank_abbr)=upper(".$abbr.") and status not in (-5) ");

        if($this->form_validation->run() === false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows() > 0)
        {
            echo $res=json_api(0,"ABBR sudah ada.");
        }
        else
        {

            $this->db->trans_begin();
            $this->bank->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/bank_refund/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->db->escape($this->enc->decode($id));

        $data['title'] = 'Edit Bank Refund';
        $data['id'] = $id;
        $data['detail']=$this->bank->select_data($this->_table,"where id=$id_decode")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $bank_name=$this->db->escape(trim($this->input->post('bank', true)));
        $transfer_fee=$this->db->escape(trim($this->input->post('transfer_fee', true)));
        $id = $this->enc->decode($this->input->post('id', true));
        $_POST['id'] = $id; 

        $this->form_validation->set_rules('bank', 'Nama Bank', 'required|max_length[50]');
        $this->form_validation->set_rules('id', 'Id', 'required|is_natural');
        $this->form_validation->set_rules('transfer_fee', 'Biaya Transfer ', 'required|is_natural');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('is_natural','%s harus angka!');
        $this->form_validation->set_message('is_natural_no_zero','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');

        $data=array(
                    
                    'bank_name'=>$bank_name,
                    'transfer_fee'=>$transfer_fee,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {

            $this->db->trans_begin();

            $this->bank->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'master_data/bank_refund/action_edit';
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
        $this->bank->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'master_data/bank_refund/action_change';
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
        $this->bank->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'master_data/bank_refund/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
