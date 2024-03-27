<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Batch
 * -----------------------
 *
 * @author     Arief Darmawan <ariefdwn.nutech@gmail.com>
 * @copyright  2021
 *
 */

class Batch extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('m_batch', 'batch');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table = 'app.t_mtr_batch';
        $this->_username = $this->session->userdata('username');
        $this->_module = 'master_data/batch';
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->batch->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity = $this->batch->get_identity_app();
        // port berdasarkan user

        if ($get_identity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->batch->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id') . "")->result();
                $row_port = 1;
            } else {
                $port = $this->batch->select_data("app.t_mtr_port", "where status = 1 order by name asc")->result();
                $row_port = 0;
            }
        } else {
            $port = $this->batch->select_data("app.t_mtr_port", "where id=" . $get_identity . "")->result();
            $row_port = 1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master Batch',
            'content'  => 'batch/index',
            'port'     => $port,
            'row_port' => $row_port,
            'ship_class'  => $this->batch->select_data("app.t_mtr_ship_class", "where status=1 order by name asc")->result(),
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
        );

        $this->load->view('default', $data);
    }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'add');

        $data['title'] = 'Tambah Batch';
        $data['port'] = $this->batch->select_data("app.t_mtr_port","where status='1' order by name asc")->result();
        $data['ship_class'] = $this->batch->select_data("app.t_mtr_ship_class","where status='1' order by name asc")->result();

        $this->load->view($this->_module.'/add', $data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $batch_name = trim($this->input->post('batch_name', true));
        $port_id = $this->enc->decode($this->input->post('port', true));
        $ship_class_id = $this->enc->decode($this->input->post('ship_class', true));
        $get_portcode   = $this->batch->select_data("app.t_mtr_port"," where id=".$this->db->escape($port_id))->row();
        $batch_code = $this->createCode($ship_class_id, $get_portcode->port_code);

        $_POST['port'] = $port_id;
        $_POST['ship_class'] = $ship_class_id;

        $this->form_validation->set_rules('batch_name', 'Nama Batch ', 'required|max_length[100]');
        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('ship_class', 'Tipe Kapal ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus berupa angka!');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');

        $data = array(
            'batch_code'    => $batch_code,
            'batch_name'    => $batch_name,
            'port_id'       => $port_id,
            'ship_class'    => $ship_class_id,
            'status'        => 1,
            'created_by'    => $this->session->userdata('username'),
        );

        // check data jika batch name sudah ada
        $check  = $this->batch->select_data($this->_table, " where upper(batch_name) = upper('".$batch_name."') and port_id = ".$this->db->escape($port_id)." and status not in (-5) ");

        if ($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
        }
        else if ($check->num_rows() > 0) {
            echo $res = json_api(0, "Nama batch '".$batch_name."' untuk pelabuhan '".$get_portcode->name."' sudah ada.");
        }
        else {
            $this->db->trans_begin();

            $this->batch->insert_data($this->_table, $data);

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
        $logUrl      = site_url().'master_data/batch/action_add';
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

        $data['title'] = 'Edit Batch';
        $data['id'] = $id;
        $data['port'] = $this->batch->select_data("app.t_mtr_port","where status='1' order by name asc")->result();
        $data['ship_class'] = $this->batch->select_data("app.t_mtr_ship_class","where status='1' order by name asc")->result();
        $data['detail']=$this->batch->select_data($this->_table,"where id=".$this->db->escape($id_decode))->row();

        $this->load->view($this->_module.'/edit',$data);
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $batch_name = trim($this->input->post('batch_name', true));
        $id = $this->enc->decode($this->input->post('id', true));
        $port_id = $this->enc->decode($this->input->post('port', true));
        $get_portcode   = $this->batch->select_data("app.t_mtr_port"," where id=".$this->db->escape($port_id))->row();

        $_POST["id"] = $id; 

        $this->form_validation->set_rules('batch_name', 'Nama Batch', 'required|max_length[100]');
        $this->form_validation->set_rules('id', 'Id', 'required|is_natural');


        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus berupa angka!');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');
        $this->form_validation->set_message('is_natural','%s harus angka!');

        $data = array(
            'batch_name'    => $batch_name,
            'updated_by'    => $this->session->userdata('username'),
            'updated_on'    => date("Y-m-d H:i:s"),
        );

        // check data jika batch name sudah ada
        $check  = $this->batch->select_data($this->_table, " where upper(batch_name) = upper(".$this->db->escape($batch_name).") and port_id = ".$this->db->escape($port_id)." and status not in (-5) and id != ".$this->db->escape($id));

        if ($this->form_validation->run()===false) {
            echo $res = json_api(0, validation_errors());
        }
        else if ($check->num_rows() > 0) {
            echo $res = json_api(0, "Nama batch '".$batch_name."' untuk pelabuhan '".$get_portcode->name."' sudah ada.");
        }
        else {
            $this->db->trans_begin();

            $this->batch->update_data($this->_table, $data, "id=".$this->db->escape($id));

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res = json_api(0, 'Gagal edit data');
            }
            else {
                $this->db->trans_commit();
                echo $res = json_api(1, 'Berhasil edit data');
            }
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/batch/action_edit';
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
            'id'            => $d[0],
            'status'        => $d[1],
            'updated_by'    => $this->session->userdata('username'),
            'updated_on'    => date("Y-m-d H:i:s"),
        );

        $this->db->trans_begin();

        $this->batch->update_data($this->_table, $data, "id=".$d[0]);

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
        $logUrl      = site_url().'master_data/batch/action_change';
        $logMethod   = $d[1] == 1 ?  'ENABLE' : $logMethod = 'DISABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'delete');

        $id = $this->enc->decode($id);

        $data = array(
            'id'            => $id,
            'status'        => -5,
            'updated_by'    => $this->session->userdata('username'),
            'updated_on'    => date("Y-m-d H:i:s"),
        );

        $this->db->trans_begin();

        $this->batch->update_data($this->_table, $data, " id='".$id."'");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $res = json_api(0, 'Gagal delete data');
        }
        else {
            $this->db->trans_commit();
            echo $res = json_api(1, 'Berhasil delete data');
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/batch/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function createCode($ship_class_id, $port_code) {
        $front_code = $ship_class_id."".$port_code;
        $total      = strlen($front_code);

        $chekCode   = $this->db->query("select * from app.t_mtr_batch where status not in ('-5') and left(batch_code,".$total.")='".$front_code."' ")->num_rows();

        if($chekCode < 1) {
            $shelterCode = $front_code."0001";
            return $shelterCode;
        }
        else {
            $max    = $this->db->query("select max (batch_code) as max_code from app.t_mtr_batch where status not in ('-5') and left(batch_code,".$total.")='".$front_code."' ")->row();
            $kode   = $max->max_code;
            $noUrut = (int) substr($kode, strlen($front_code), 4);
            $noUrut++;
            $char   = $front_code;
            $kode   = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

}
