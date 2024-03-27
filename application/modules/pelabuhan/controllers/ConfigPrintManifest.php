<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------
 * CLASS NAME : ConfigPrintManifest
 * -----------------
 *
 * @author     Adat <adatdt@gmail.com>
 * @copyright  2023
 *
 */

class ConfigPrintManifest extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model("ConfigPrintManifestModel",'configModel');
        $this->load->model('global_model');
        $this->_table    = 'app.t_mtr_config_print_manifest';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pelabuhan/configPrintManifest';
  }

  /*
    Document   : Pelabuhan
    Created on : 10 juli, 2023
    Author     : adi
    Description: Enhancement pasca angleb 2023
    */

    public function index()
    {
        checkUrlAccess($this->_module, 'view');
        if ($this->input->is_ajax_request()) {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->configModel->shipList();
            echo json_encode($rows);
            exit;
            
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Konfigurasi Print Penumpang Dalam Kendaraan',
            'content'  => 'configPrintManifest/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add')),

        );

        $this->load->view('default', $data);
    }

    public function add()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'add');
        validate_ajax();

        $getPort = $this->configModel->select_data("app.t_mtr_port"," where status = 1 order by id desc ")->result();
        $dataPort = $this->getDataMaster($getPort);
        
        $getShipClass = $this->configModel->select_data("app.t_mtr_ship_class"," where status = 1 order by id desc ")->result();
        $dataShipClass = $this->getDataMaster($getShipClass);

        $data['title'] = 'Tambah Konfigurasi Print Penumpang Dalam Kendaraan';
        $data['port'] = $dataPort['data'];
        $data['shipClass'] = $dataShipClass['data'];

        $this->load->view($this->_module . '/add', $data);
    }

    
    private function getDataMaster($data, $paramSelected="")
    {
        $dataReturn[""]="Pilih";
        $selectedData = "";
        foreach ($data as $key => $value) {

            $idEncode = $this->enc->encode($value->id);
            if( $paramSelected == $value->id )
            {
                $selectedData = $idEncode ;
            }
            $dataReturn[$idEncode] = $value->name; 
        }
        
        $return["data"] = $dataReturn;
        $return["selected"] = $selectedData;
        return $return;
    }

    

    public function action_add()
    {
        validate_ajax();
        $data = [];
        $this->global_model->checkAccessMenuAction($this->_module, 'add');

        
        /* validation */
        $this->form_validation->set_rules('checkbox_param', 'status', 'trim|numeric');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        $this->form_validation->set_rules('shipClass', 'Kelas kapal', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
        $this->form_validation->set_message('required', '%s harus diisi!')
            ->set_message('numeric', '%s harus angka!');
        /* data post */
        
       
        if ($this->form_validation->run() == FALSE) 
        {
            echo $res = json_api(0, validation_errors());
        } 
        else 
        {   
            $port = $this->enc->decode($this->input->post("port"));
            $shipClass = $this->enc->decode($this->input->post("shipClass"));
            $configurationStatus = $this->input->post("checkbox_param");
            $data = array(
                'port_id' => $port,
                'ship_class' => $shipClass,
                'configuration_status' => $configurationStatus,
                'status' => 1,
                'created_on' => date('Y-m-d H:i:s'),
                'created_by' => $this->session->userdata("username"),
            );

            $check = $this->configModel->select_data($this->_table, " where  port_id='".$port."' and ship_class = '".$shipClass."' and status != '-5'   ");
            if ($check->num_rows() > 0) {
                echo $res =  json_api(0, 'Konfigurasi sudah ada');
            }
            else{
                $this->db->trans_begin();
                $this->configModel->insert_data($this->_table, $data);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo $res = json_api(0, 'Gagal tambah data');
                } else {
                    $this->db->trans_commit();
                    echo $res = json_api(1, 'Berhasil tambah data');
                }
            } 
            
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'pelabuhan/configPrintManifest/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

   

    public function edit($param)
    {
        validate_ajax();

        if ($this->validate_decode_param($param) == FALSE) 
        {
            echo $res = json_api(0, "Invalid id");
            exit;
        } 
        $detail = $this->global_model->selectById($this->_table, 'id', $this->enc->decode($param));
        $getPort = $this->configModel->select_data("app.t_mtr_port"," where status = 1 order by id desc ")->result();
        $dataPort = $this->getDataMaster($getPort, $detail->port_id);
        
        $getShipClass = $this->configModel->select_data("app.t_mtr_ship_class"," where status = 1 order by id desc ")->result();
        $dataShipClass = $this->getDataMaster($getShipClass, $detail->ship_class);
        
        
        $data['title'] =  'Edit Konfigurasi Print Penumpang Dalam Kendaraan';
        $data['id']    = $param;
        $data['detail']   = $detail;
        $data['port'] = $dataPort['data'];
        $data['portSelected'] = $dataPort['selected'];
        $data['shipClass'] = $dataShipClass['data'];
        $data['shipClassSelected'] = $dataShipClass['selected'];
        $this->load->view($this->_module . '/edit', $data);
    }
    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');
        $data = [];
    
        /* validation */
        $this->form_validation->set_rules('checkbox_param', 'status', 'trim|numeric');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        $this->form_validation->set_rules('shipClass', 'Kelas kapal', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas kapal'));
        $this->form_validation->set_rules('id', 'id', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid id'));
        $this->form_validation->set_message('required', '%s harus diisi!')
            ->set_message('numeric', '%s harus angka!');
        /* data post */

        if ($this->form_validation->run() == FALSE) 
        {
            echo $res = json_api(0, validation_errors());
        } 
        else
        {   

            $id = $this->enc->decode($this->input->post("id"));
            $port = $this->enc->decode($this->input->post("port"));
            $shipClass = $this->enc->decode($this->input->post("shipClass"));
            $configurationStatus = $this->input->post("checkbox_param");
            $check = $this->configModel->select_data($this->_table, " where  port_id='".$port."' and ship_class = '".$shipClass."'  and id !={$id} and status !='-5' ");
            if ($check->num_rows() > 0) {
                echo $res =  json_api(0, 'Konfigurasi sudah ada');
            } 
            else{
                /* data post */
                $data = array(
                    'port_id' => $port,
                    'ship_class' => $shipClass,
                    'configuration_status' => $configurationStatus,
                    'updated_on' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->session->userdata("username"),
                );

                
                $this->db->trans_begin();
                $this->configModel->update_data($this->_table, $data, "id={$id}");

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo $res = json_api(0, 'Gagal edit data');
                } else {
                    $this->db->trans_commit();
                    echo $res = json_api(1, 'Berhasil edit data');
                }
            }

            
        }
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'pelabuhan/configPrintManifest/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function action_delete($param)
    {   

        
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'delete');
        $id   = $this->enc->decode($param);
        $data = [];
        if(!$id || empty($id)){
            echo $res=json_api(0, 'Gagal hapus data');            
        }
        else{
            /* data */
            $data = array(
                'status' => -5,
                'updated_by' => $this->session->userdata("username"),
                'updated_on' => date("Y-m-d H:i:s "),
            );

            $this->db->trans_begin();

            $this->configModel->update_data($this->_table, $data, "id={$id}");

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res = json_api(0, 'Gagal delete data');
            } else {
                $this->db->trans_commit();
                echo $res = json_api(1, 'Berhasil delete data');
            }
        }
        
        

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'pelabuhan/configPrintManifest/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


     /*
    Document   : Pelabuhan
    Created on : 10 juli, 2023
    Author     : adi
    Description: end Enhancement pasca angleb 2023
    */

}
