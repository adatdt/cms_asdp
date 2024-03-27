<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
/*
    enchance penambahan validasi dan penambahan token csrf
     by adat 25-07-2023
*/

class Ip_whitelist_b2b extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_ip_whitelist_b2b','whitelistb2b');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_ip_whitelist_b2b';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/ip_whitelist_b2b';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->whitelistb2b->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'IP Whitelist B2B',
            'content'  => 'ip_whitelist_b2b/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // $data['title'] = 'Tambah IP Whitelist B2B';
        $data = array(
            'title'    => 'Tambah IP Whitelist B2B',
            'merchant'  => $this->whitelistb2b->select_data("app.t_mtr_merchant","where status=1 order by merchant_name asc")->result(),

        );

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $ip=trim($this->input->post('ip', true));
        $merchant_id=$this->enc->decode(trim($this->input->post('merchant',true)));
        // $transfer_fee=trim($this->input->post('transfer_fee'));

        $_POST['merchant'] = $merchant_id;

        $this->form_validation->set_rules('ip', 'IP ', 'required|max_length[15]|callback_numeric_dot');
        $this->form_validation->set_rules('merchant', 'ID Merchant ', 'required');
        // $this->form_validation->set_rules('transfer_fee', 'Biaya Transfer ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('numeric_dot','%s harus angka atau titik!');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');
        
        $data=array(
                    'ip'=>$ip,
                    'merchant_id'=>$merchant_id,
                    // 'transfer_fee'=>$transfer_fee,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // ceck data jika username sudah ada
        $check = $this->whitelistb2b->select_data($this->_table," where upper(ip)=upper(".$this->db->escape($ip).") and upper(merchant_id)=(".$this->db->escape($merchant_id).")  and status not in (-5) ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"IP dan Merchant sudah ada.");
        }
        else
        {

            $this->db->trans_begin();

            $this->whitelistb2b->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/ip_whitelist_b2b/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function numeric_dot ($num) {
        return ( ! preg_match("/^([0-9.\s])+$/D", $num)) ? FALSE : TRUE;
    }
    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);

        $data['title'] = 'Edit IP Whitelist B2B';
        $data['id'] = $id;
        $data['detail']=$this->whitelistb2b->select_join($this->_table,"where a.id=$id_decode")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $ip=trim($this->input->post('ip', true));
        $merchant_id=$this->enc->decode(trim($this->input->post('merchant_id', true)));
        // $transfer_fee=trim($this->input->post('transfer_fee'));
        $id=$this->enc->decode($this->input->post('id', true));

        $_POST['merchant'] = $merchant_id;
        $_POST['id'] = $id;

        $this->form_validation->set_rules('ip', 'IP ', 'required|max_length[15]|callback_numeric_dot');
        $this->form_validation->set_rules('merchant', 'ID Merchant ', 'required');
        // $this->form_validation->set_rules('transfer_fee', 'Biaya Transfer ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric_dot','%s harus angka atau titik!');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');


        $data=array(                    
                    'ip'=>$ip,
                    // 'transfer_fee'=>$transfer_fee,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );
        
        $check = "";
        if(!empty($id) )
        {                    
            $check=$this->whitelistb2b->select_data($this->_table," where upper(ip)=upper(".$this->db->escape($ip).")  and upper(merchant_id)=upper(".$this->db->escape($merchant_id).") and id != ".$this->db->escape($id)." and status not in (-5) ");
        }

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"IP sudah ada.");
        }
        else
        {

            $this->db->trans_begin();

            $this->whitelistb2b->update_data($this->_table,$data,"id=".$this->db->escape($id));

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
        $logUrl      = site_url().'master_data/ip_whitelist_b2b/action_edit';
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
        $this->whitelistb2b->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'master_data/ip_whitelist_b2b/action_change';
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
        $this->whitelistb2b->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'master_data/ip_whitelist_b2b/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
