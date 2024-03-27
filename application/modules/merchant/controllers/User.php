<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('user_model');
        $this->load->model('global_model');

        $this->_table    = 'app.t_mtr_merchant';
        $this->_user_table = 'core.t_mtr_user';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'merchant/user';
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->user_model->listData();
            $rows["tokenHash"] = $this->security->get_csrf_hash();
            $rows["csrfName"] = $this->security->get_csrf_token_name();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'User Merchant',
            'content'  => 'user/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add'))
        );

        $this->load->view('default', $data);
    }

    public function add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'add');

        $data['title'] = 'Tambah User Merchant';
        $data['option'] = $this->user_model->option_user('');
        $this->load->view($this->_module . '/add', $data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'add');
        /* validation */
        $this->form_validation->set_rules('username', 'username', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'Username memuat invalid karakter'));
        $this->form_validation->set_rules('mitraCode', 'Kode Mitra', 'trim|required|max_length[30]|callback_special_char', array('special_char' => 'kode mitra memuat invalid karakter'));
        $this->form_validation->set_rules('merchant_name', 'Merchant Name', 'trim|required|min_length[8]|max_length[100]|callback_special_char', array('special_char' => 'Nama merchant memuat invalid karakter'));
        $this->form_validation->set_rules('merchant_id', 'Merchant ID', 'trim|required|max_length[30]|callback_special_char', array('special_char' => 'merchant id memuat invalid karakter'));
        $this->form_validation->set_rules('merchant_key', 'Merchant Key', 'trim|required|min_length[8]|max_length[12]|callback_special_char', array('special_char' => 'Username memuat invalid karakter'));
        // $this->form_validation->set_rules('merchantPrefix', 'Merchant Prefix', 'trim|required|min_length[2]|max_length[2]');

        $this->form_validation->set_message('required', '%s harus diisi!');
        /* data post */
        if ($this->form_validation->run() == FALSE) {
            echo $res = json_api(0, validation_errors());
            exit;
        } 
        $post = $this->input->post();
        $username = trim($post['username']);
        $merchant_name = strtoupper(trim($post['merchant_name']));
        $merchant_key = strtoupper(trim($post['merchant_key']));
        $merchant_id = strtoupper(trim($post['merchant_id']));
        // $merchantPrefix = strtoupper(trim($post['merchantPrefix']));
        $mitraCode = trim($post['mitraCode']);



        $data = array(
            'username' => $username,
            'merchant_name' => $merchant_name,
            'status' => 1,
            // 'merchant_prefix' => $merchantPrefix,
            'merchant_id' => $merchant_id,
            'mitra_code' => $mitraCode,
            'created_on' => date("Y-m-d H:i:s"),
            'created_by' => $this->session->userdata('username'),
            'merchant_key' => $merchant_key,
        );
        $cekusername = strtoupper($username);
        $cekmerchantid = strtoupper($merchant_id);


        $check = $this->user_model->select_data($this->_user_table, " where UPPER(username)='{$cekusername}' and status not in (-5) and user_group_id=29");
        $check2 = $this->user_model->select_data($this->_table, " where UPPER(username)='{$cekusername}' and status<>'-5' ");
        $check3 = $this->user_model->select_data($this->_table, " where UPPER(merchant_id)='{$cekmerchantid}' and status<>'-5' ");
        // $check4 = $this->user_model->select_data($this->_table, " where UPPER(merchant_prefix)=upper('{$merchantPrefix}') and status<>'-5' ");


        if ($check->num_rows() == 0) {
            echo $res =  json_api(0, 'Username ' . $username . ' Tidak Tersedia');
        } elseif ($check2->num_rows() > 0) {
            echo $res =  json_api(0, 'Username ' . $username . ' Sudah Ada');
        } elseif ($check3->num_rows() > 0) {
            echo $res =  json_api(0, 'Merchant Id ' . $merchant_id . ' Sudah Ada');
        } 
        // else if($check4->num_rows() > 0){
        //     echo $res =  json_api(0, 'Merchant Prefix ' . $merchantPrefix . ' Sudah Ada');
        // }
        else 
        {
            $this->db->trans_begin();

            $this->user_model->insert_data($this->_table, $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res = json_api(0, 'Gagal tambah data');
            } else {
                $this->db->trans_commit();
                echo $res = json_api(1, 'Berhasil tambah data');
            }
            // echo $res = json_api(1, 'Berhasil tambah data');
        }


        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'merchant/user/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($param)
    {
        validate_ajax();
        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');

        $decode_id = $this->enc->decode($param);

        $data['id']    = $param;
        $data['option'] = $this->user_model->option_user(' OR um.id = ' . $decode_id);
        $data['row']   = $this->global_model->selectById($this->_table, 'id', $decode_id);
        $data['title'] = 'Edit User Merchant';
        $this->load->view($this->_module . '/edit', $data);
    }

    public function action_edit()
    {
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');
        /* validation */
        $this->form_validation->set_rules('id', 'id', 'required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid id'));
        $this->form_validation->set_rules('username', 'username', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'Username memuat invalid karakter'));
        $this->form_validation->set_rules('mitraCode', 'Kode Mitra', 'trim|required|max_length[30]|callback_special_char', array('special_char' => 'kode mitra memuat invalid karakter'));
        $this->form_validation->set_rules('merchant_name', 'Merchant Name', 'trim|required|min_length[8]|max_length[100]|callback_special_char', array('special_char' => 'Nama merchant memuat invalid karakter'));
        $this->form_validation->set_rules('merchant_id', 'Merchant ID', 'trim|required|max_length[30]|callback_special_char', array('special_char' => 'merchant id memuat invalid karakter'));
        $this->form_validation->set_rules('merchant_key', 'Merchant Key', 'trim|required|min_length[8]|max_length[12]|callback_special_char', array('special_char' => 'Username memuat invalid karakter'));
        // $this->form_validation->set_rules('merchantPrefix', 'Merchant Prefix', 'trim|required|min_length[2]|max_length[2]');

        $this->form_validation->set_message('required', '%s harus diisi!');
        /* data post */
        if ($this->form_validation->run() == FALSE) {
            echo $res = json_api(0, validation_errors());
            exit;
        } 


        $post = $this->input->post();
        $username = trim($post['username']);
        $mitraCode = trim($post['mitraCode']);
        $merchant_name = strtoupper(trim($post['merchant_name']));
        $merchant_key = strtoupper(trim($post['merchant_key']));
        $old_username = strtoupper($post['old_username']);
        $merchant_id = strtoupper(trim($post['merchant_id']));
        $old_merchant_id = strtoupper($post['old_merchant_id']);
        // $merchantPrefix = strtoupper($post['merchantPrefix']);


        $id = $this->enc->decode($post['id']);

        /* data post */
        $data = array(
            // 'id' => $id,
            'username' => $username,
            'merchant_name' => $merchant_name,
            'mitra_code' => $mitraCode,
            // 'merchant_id' => $merchant_id,
            // 'merchant_prefix'=>$merchantPrefix,
            'updated_on' => date('Y-m-d H:i:s'),
            'updated_by' => $this->session->userdata('username'),
            'merchant_key' => $merchant_key,
        );
        $cekusername = strtoupper($username);
        $cekmerchantid = strtoupper($merchant_id);


        $check = $this->user_model->select_data($this->_user_table, " where UPPER(username)='{$cekusername}' and status not in (-5) and user_group_id=29");
        $check2 = $this->user_model->select_data($this->_table, " where UPPER(username)='{$cekusername}' and UPPER(username) not in('{$old_username}')");
        $check3 = $this->user_model->select_data($this->_table, " where UPPER(merchant_id)='{$cekmerchantid}' and UPPER(merchant_id) not in('{$old_merchant_id}') and status<>'-5' ");
        // $check4 = $this->user_model->select_data($this->_table, " where UPPER(merchant_prefix)=upper('{$merchantPrefix}') and UPPER(merchant_id) not in('{$old_merchant_id}') and status<>'-5' ");

        if ($this->form_validation->run() == FALSE) {
            echo $res = json_api(0, validation_errors());
        } elseif ($check->num_rows() == 0) {
            echo $res =  json_api(0, 'Username ' . $username . ' Tidak Tersedia');
        } elseif ($check2->num_rows() > 0) {
            echo $res =  json_api(0, 'Username ' . $username . ' Sudah Ada');
        } elseif ($check3->num_rows() > 0) {
            echo $res =  json_api(0, 'Merchant Id ' . $merchant_id . ' Sudah Ada');
        }
        // elseif ($check4->num_rows() > 0) {
        //     echo $res =  json_api(0, 'Merchant Prefix ' . $merchantPrefix . ' Sudah Ada');
        // } 
        else {

            $this->db->trans_begin();

            $this->user_model->update_data($this->_table, $data, "id=$id");

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res = json_api(0, 'Gagal edit data');
            } else {
                $this->db->trans_commit();
                echo $res = json_api(1, 'Berhasil edit data');
            }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'merchant/user/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($param)
    {
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module, 'delete');

        $id   = $this->enc->decode($param);

        /* data */
        $data = array(
            'id' => $id,
            'status' => -5,
            'updated_by' => $this->session->userdata('username'),
            'updated_on' => date("Y-m-d H:i:s"),
        );

        $this->db->trans_begin();
        $this->user_model->update_data($this->_table, $data, "id=$id");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $res = json_api(0, 'Gagal delete data');
        } else {
            $this->db->trans_commit();
            echo $res = json_api(1, 'Berhasil delete data');
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'merchant/user/action_delete';
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
            // 'id' => $d[0],
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            "updated_by"=>$this->session->userdata("username")
        );


        $this->db->trans_begin();
        $this->user_model->update_data($this->_table, $data, "id=$d[0]");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $response = json_api(0, 'Update Status Berhasil');
        } else {
            $this->db->trans_commit();
            $response = json_api(1, 'Update Status Berhasil');
        }

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'change_status', json_encode($data), $response);
        echo $response;
    }
}
