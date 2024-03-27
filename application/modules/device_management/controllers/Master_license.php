<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Master_license extends MY_Controller{
	public function __construct() {
		parent::__construct();

        logged_in();
        $this->load->model('m_master_license','modelMasterLicense');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_license';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'device_management/master_license';
	}

	public function index() {
        checkUrlAccess(uri_string(),'view');

        if($this->input->is_ajax_request()){
            $rows = $this->modelMasterLicense->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master Lisensi',
            'content'  => 'master_license/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
        );
        $this->load->view('default', $data);
	}

    public function add() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data = array(
            'title' => 'Generate Nomor Lisensi tes',
            'port'  => $this->global_model->select_data("app.t_mtr_port", "WHERE status = '1' ORDER BY name ASC")->result(),
        );
        // print_r($data);exit;
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port_id = $this->enc->decode($this->input->post('port_id'));
        $license_number = $this->generate_license_number();

        $dataInsert = array(
            'license_number'    => $license_number,
            'imei'              => NULL,
            'status'            => 1,
            'created_by'        => $this->session->userdata('username'),
            'created_on'        => date("Y-m-d H:i:s"),
            'port_id'           => $port_id,
        );

        $check = $this->modelMasterLicense->select_data($this->_table," where license_number = '".$license_number."' and status not in (-5)");

        if($check->num_rows() > 0) {
            echo $res = json_api(0,"Nomor Lisensi sudah ada. Silahkan Generate lagi.");
        }
        else {
            $this->db->trans_begin();
            $this->modelMasterLicense->insert_data($this->_table, $dataInsert);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res = json_api(0, 'Gagal tambah data');
            }
            else {
                $this->db->trans_commit();
                echo $res = json_api(1, 'Berhasil tambah data');
            }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'device_management/master_license/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($dataInsert);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param) {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        $dtLicense = $this->modelMasterLicense->select_data($this->_table," WHERE id = ".$d[0])->row_array();
        $check = $this->modelMasterLicense->select_data($this->_table," WHERE imei = '".$dtLicense['imei']."' AND status IN (1)");

        if($d[1] == 1 && $check->num_rows() > 0) {
            echo $res = json_api(0,"IMEI sudah digunakan.");
        }
        else {
            $dataUpdate = array(
                'status'        => $d[1],
                'updated_on'    => date("Y-m-d H:i:s"),
                'updated_by'    => $this->session->userdata('username'),
            );

            $this->db->trans_begin();
            $this->modelMasterLicense->update_data($this->_table, $dataUpdate, "id = ".$d[0]);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                if ($d[1] == 1) {
                    echo $res = json_api(0, 'Gagal aktif');
                }
                else {
                    echo $res = json_api(0, 'Gagal non aktif');
                }            
            }
            else {
                $this->db->trans_commit();
                if ($d[1] == 1) {
                    echo $res = json_api(1, 'Berhasil aktif data');
                }
                else {
                    echo $res = json_api(1, 'Berhasil non aktif data');
                }
            }   

            /* Fungsi Create Log */
            $createdBy   = $this->session->userdata('username');
            $logUrl      = site_url().'device_management/master_license/action_change';
            $d[1] == 1 ? $logMethod = 'ENABLE' : $logMethod = 'DISABLE';
            $logParam    = json_encode($dataUpdate);
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        }
    }

    public function generate_license_number() {
        $karakter           = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $panjang            = 4;
        $panjangKarakter    = strlen($karakter);
        $segment1           = '';
        $segment2           = '';
        $segment3           = '';
        $segment4           = '';

        for ($i = 0; $i < $panjang; $i++) {
            $segment1 .= $karakter[rand(0, $panjangKarakter - 1)];
            $segment2 .= $karakter[rand(0, $panjangKarakter - 1)];
            $segment3 .= $karakter[rand(0, $panjangKarakter - 1)];
            $segment4 .= $karakter[rand(0, $panjangKarakter - 1)];
        }
        return $segment1.'-'.$segment2.'-'.$segment3.'-'.$segment4;
    }
}
