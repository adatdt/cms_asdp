<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
/**
 * --------------------------
 * CLASS NAME : Vehicle_class
 * --------------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Vehicle_class extends MY_Controller{

	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('vehicle_class_model');
        $this->_table    = 'app.t_mtr_vehicle_class';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pelabuhan/vehicle_class';
    }

    /*
    Document   : Pelabuhan
    Created on : 28 juli, 2023
    Author     : soma
    Description: Enhancement pasca angleb 2023
    */

    public function index(){
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->vehicle_class_model->vehicleClassList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Golongan Kendaraan',
            'content'  => 'vehicle_class/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
        );

        $this->load->view('default', $data);
    }

    public function add(){
        validate_ajax();
        $data['title'] = 'Tambah Golongan Kendaraan';
        $data['class_type'] = $this->vehicle_class_model->select_data("app.t_mtr_vehicle_type"," where status=1 order by id desc")->result();
        $this->load->view($this->_module.'/add',$data);
    }

    public function edit($param){
        validate_ajax();
        $data['id']    = $param;
        $data['row']   = $this->global_model->selectById($this->_table, 'id', $this->enc->decode($param));
        $data['class_type'] = $this->vehicle_class_model->select_data("app.t_mtr_vehicle_type"," where status=1 order by id desc")->result();
        $data['title'] = 'Edit Golongan Kendaraan';
        $this->load->view($this->_module.'/edit',$data);
    }

    public function action_add(){
        validate_ajax();
        
        /* validation */
        $this->form_validation->set_rules('name', 'Nama Golongan', 'trim|required|callback_special_char', array('special_char' => 'nama golongan mengandung invalid karakter'));
        $this->form_validation->set_rules('tipe', 'Tipe Class', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid tipe class'));
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|required');
        $this->form_validation->set_rules('min', 'Panjang Minimum', 'trim|required|numeric|min_length[1]');
        $this->form_validation->set_rules('max', 'Panjang Maksimal', 'trim|required|numeric|min_length[1]');
        $this->form_validation->set_rules('weight_maximum', 'Berat Maksimal', 'trim|required|numeric|min_length[1]');

        $this->form_validation->set_message('required','%s harus diisi!')
        ->set_message('numeric','%s harus angka!')
        ->set_message('min_length','{field} Minimal  {param} karakter! ');


        $data = null;
        if($this->form_validation->run() == FALSE){
            $response = json_api(0,validation_errors());
        }
        else
        {
            $post = $this->input->post();
            // $code = strtoupper($post['code']);
            // $name = strtoupper(trim($post['name']));
            $name = trim($post['name']);
            $capacity_maximum=$post['capacity_maximum'];
            $weight_maximum=$post['weight_maximum'];
            $tipe=$this->enc->decode($this->input->post('tipe'));
            $description=$this->input->post('description');
            $idNumberRequired=$this->input->post('checkbox_param');

            /* data post */
            $data = array(
                'code' => NULL,
                'name' => $name,
                'adult_capacity' =>NULL,
                'child_capacity' =>NULL,
                'infant_capacity' => NULL,
                'id_number_required'=>$idNumberRequired,
                'max_capacity'=>$capacity_maximum,
                'type'=>$tipe,
                'min_length' => $post['min'],
                'max_length' => $post['max'],
                'default_weight' => $weight_maximum,
                'description' =>$description, 
            );

            // check nama nya tidak boleh sama 
            $check_name=$this->vehicle_class_model->select_data($this->_table," where upper(name)='".$name."' and status =1");

            if($check_name->num_rows()>0)
            {
                $response = json_api(0,'Nama sudah ada');
            }
            else
            {

                $query = $this->global_model->saveData($this->_table, $data);
                if($query)
                {
                    $response = json_api(1,'Berhasil Tambah Data');
                }
                else
                {
                    // $response = json_encode($this->db->error());
                    $response = json_api(0,'Gagal Tambah Data');
                }
            }
        }
            $this->log_activitytxt->createLog($this->_username, uri_string(), 'insert', json_encode($data), $response); 
            echo $response;
    }

    public function action_edit(){
        validate_ajax();
        
        /* validation */
        $this->form_validation->set_rules('name', 'Nama Golongan', 'trim|required|callback_special_char', array('special_char' => 'nama golongan mengandung invalid karakter'));
        $this->form_validation->set_rules('tipe', 'Tipe Class', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid tipe class'));
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|required');
        $this->form_validation->set_rules('min', 'Panjang Minimum', 'trim|required|numeric|min_length[1]');
        $this->form_validation->set_rules('max', 'Panjang Maksimal', 'trim|required|numeric|min_length[1]');
        $this->form_validation->set_rules('weight_maximum', 'Berat Maksimal', 'trim|required|numeric|min_length[1]');

        $this->form_validation->set_message('required','%s harus diisi!')
        ->set_message('numeric','%s harus angka!')
        ->set_message('min_length','{field} Minimal  {param} karakter! ');

        $data = null;
        if($this->form_validation->run() == FALSE){
            $response = json_api(0,validation_errors());
        }
        else
        {
            $post = $this->input->post();

            // $name = strtoupper(trim($post['name']));
            $name = trim($post['name']);
            $capacity_maximum=$post['capacity_maximum'];
            $tipe=$this->enc->decode($this->input->post('tipe'));
            $description=$this->input->post('description');
            $id=$this->enc->decode($this->input->post('vehicle_class'));
            $idNumberRequired=$this->input->post('checkbox_param');

            /* data post */
            $data = array(
                'name' => $name,
                'max_capacity' => $capacity_maximum,
                'type' =>$tipe,
                'description' => $description,
                'min_length' => $post['min'],
                'max_length' => $post['max'],
                'id_number_required'=>$idNumberRequired,
                'default_weight' => $post['weight_maximum'],
                'updated_on'=>date('Y-m-d H:i:s'),
                'updated_by'=>$this->session->userdata('username'),
            );

            $check_name=$this->vehicle_class_model->select_data($this->_table," where upper(name)='".$name."' and status =1 and id!=".$id);
            
            if($check_name->num_rows()>0)
            {
                $response = json_api(0,'Nama sudah ada');   
            }
            else
            {

                $this->db->trans_begin();
                $this->vehicle_class_model->update_data($this->_table, $data, "id=$id");

                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                    $response=json_api(0, 'Gagal edit data');
                }
                else
                {
                    $this->db->trans_commit();
                    $response=json_api(1, 'Berhasil edit data');
                }
                
            }
        }

        $this->log_activitytxt->createLog($this->_username, uri_string(), 'update', json_encode($data), $response); 
        echo $response;
    }

    public function action_delete($param){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $id   = $this->enc->decode($param);
        $data = [];
        if(!$id || empty($id)){
            echo $response=json_api(0, 'Gagal hapus data');            
        }
        else{

            /* data */
            $data = array(
            'id' => $id,
            'status' => -5
            );

            $query = $this->global_model->updateData($this->_table, $data, 'id');
            if($query){
            $response = json_api(1,'Berhasil hapus data');
            }else{
            $response = json_encode($this->db->error()); 
            }

            $this->log_activitytxt->createLog($this->_username, uri_string(), 'delete', json_encode($data), $response); 
            echo $response;
        }
    }

    public function enable($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $data = [];

        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal aktifkan data');
            
        }
        else{

            $d = explode('|', $p);

            /* data */
            $data = array(
                'status' => $d[1],
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );

            //mencari data nama dengan status yang sama
            $cari_data=$this->vehicle_class_model->select_data($this->_table," where id=".$this->enc->decode($d[0]))->row();


            $check_name=$this->vehicle_class_model->select_data($this->_table," where upper(name)=upper('".trim($cari_data->name)."') and status =1");

            $this->db->trans_begin();
            $this->vehicle_class_model->update_data($this->_table,$data,"id=".$this->enc->decode($d[0]));

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal aktifkan data');
            }
            else if($check_name->num_rows()>0)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal aktifkan data, karena nama sudah ada yang aktif');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil aktifkan data');
            }
        }


        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/vehicle_class/enable';
        $logMethod   = 'Enable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function disable($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $data = [];

        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal aktifkan data');
            
        }
        else{

            $d = explode('|', $p);

            /* data */
            $data = array(
                'status' => $d[1],
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );

            // check jika ada status=1 dengan nama yang sama

            $this->db->trans_begin();
            $this->vehicle_class_model->update_data($this->_table,$data,"id=".$this->enc->decode($d[0]));

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal dinonaktifkan data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Data berhasil dinonaktifkan ');
            } 
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/vehicle_class/enable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    /*
    Document   : Pelabuhan
    Created on : 28 juli, 2023
    Author     : soma
    Description: end Enhancement pasca angleb 2023
    */

}
