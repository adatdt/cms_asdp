<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Masteroutlet extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('masteroutletmodel', 'outlet');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_outlet_merchant';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/masteroutlet';
    }

    public function index()
    {
        checkUrlAccess($this->_module, 'view');

        if ($this->input->is_ajax_request()) {
            // echo 'hi';
            // exit;
            $rows = $this->outlet->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master Outlet',
            'content'  => 'masteroutlet/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add')),

        );

        $this->load->view('default', $data);
    }

    public function add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module, 'add');
        $getmastermerchant = $this->outlet->select_data('app.t_mtr_merchant', 'where status = 1 order by merchant_name asc')->result();
        $mastermerchant[''] = 'pilih';
        foreach ($getmastermerchant as $key => $value) {
            $mastermerchant[$value->merchant_id] = $value->merchant_name;
        }
        // print_r ($mastermerchant);
        // exit;
        $data['title'] = 'Tambah Outlet';
        $data['mastermerchant'] = $mastermerchant;

        $this->load->view($this->_module . '/add', $data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'add');

        $merchantid = trim($this->input->post('merchant_id'));
        $outletid = trim($this->input->post('outlet_id'));
        $description = trim($this->input->post('description'));
        // |is_unique[.outlet_id]
        $this->form_validation->set_rules('merchant_id', 'Nama Merchant', 'required');
        $this->form_validation->set_rules('outlet_id', 'Nama Outlet', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');


        $data = array(
            'merchant_id' => $merchantid,
            'outlet_id' => $outletid,
            'description' => $description,
            'status' => 1,
            'created_by' => $this->session->userdata('username'),
            'created_on' => date("Y-m-d H:i:s"),
        );


        if ($this->form_validation->run() === false) {
            echo $res = json_api(0, 'Data masih ada yang kosong');
        } else {
            $checkMerchantoutletExist = $this->outlet->checkMerchantdanOutletExist($merchantid, $outletid);
            if (count($checkMerchantoutletExist) > 0) {
                echo $res = json_api(0, 'Data sudah ada');
            } else {
                $this->db->trans_begin();

                $this->outlet->insert_data($this->_table, $data);

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
        $logUrl      = site_url() . 'master_data/masteroutlet/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    // public function edit($id)
    // {
    //     // $id
    //     validate_ajax();
    //     $this->global_model->checkAccessMenuAction($this->_module, 'edit');
    //     // $mastermerchant[''] = 'pilih';
    //     // foreach ($getmastermerchant as $key => $value) 
    //     // {
    //     //     $mastermerchant[$value->merchant_id] = $value->merchant_name;
    //     // }
    //     $id_decode = $this->enc->decode($id);

    //     $data['title'] = 'Edit Outlet';
    //     // $data['mastermerchant'] = $mastermerchant;
    //     $data['mastermerchant'] = $this->outlet->select_data("app.t_mtr_merchant", "where status='1' order by merchant_name asc")->result();
    //     $data['detail'] = $this->outlet->select_data($this->_table, "where id=$id_decode")->row();

    //     $this->load->view($this->_module . '/edit', $data);
    // }

    // public function action_edit()
    // {
    //     validate_ajax();
    //     $this->global_model->checkAccessMenuAction($this->_module, 'edit');

    //     // $merchantid=trim($this->input->post('merchant_id'));
    //     // echo $merchantid;
    //     // exit;
    //     // print_r ($this->input->post());
    //     // exit;
    //     // $outletid=trim($this->input->post('outlet_id'));
    //     $description = trim($this->input->post('description'));
    //     // echo $description;
    //     // exit;
    //     $id = $this->enc->decode($this->input->post('id'));

    //     // $this->form_validation->set_rules('merchant_id','Nama Merchant', 'required');
    //     // $this->form_validation->set_rules('outlet_id','Nama Outlet', 'required');
    //     $this->form_validation->set_rules('description', 'Description', 'required');


    //     $data = array(
    //         // 'merchant_id'=>$merchantid,
    //         // 'outlet_id'=>$outletid,
    //         'description' => $description,
    //         'updated_by' => $this->session->userdata('username'),
    //         'updated_on' => date("Y-m-d H:i:s"),
    //     );
    //     // print_r ($data);
    //     // exit;

    //     if ($this->form_validation->run() === false) {
    //         echo $res = json_api(0, 'Data masih ada yang kosong');
    //     } else {
    //         $this->db->trans_begin();
    //         // ,"id=$id"
    //         $this->outlet->update_data($this->_table, $data, "id=$id");

    //         if ($this->db->trans_status() === FALSE) {
    //             $this->db->trans_rollback();
    //             echo $res = json_api(0, 'Gagal edit data');
    //         } else {
    //             $this->db->trans_commit();
    //             echo $res = json_api(1, 'Berhasil edit data');
    //         }
    //     }


    //     /* Fungsi Create Log */
    //     $createdBy   = $this->session->userdata('username');
    //     $logUrl      = site_url() . 'master_data/masteroutlet/action_edit';
    //     $logMethod   = 'EDIT';
    //     $logParam    = json_encode($data);
    //     $logResponse = $res;

    //     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    // }

    public function action_change($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on' => date("Y-m-d H:i:s"),
            'updated_by' => $this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->outlet->update_data($this->_table, $data, "id=" . $d[0]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            if ($d[1] == 1) {
                echo $res = json_api(0, 'Gagal aktif');
            } else {
                echo $res = json_api(0, 'Gagal non aktif');
            }
        } else {
            $this->db->trans_commit();
            if ($d[1] == 1) {
                echo $res = json_api(1, 'Berhasil aktif data');
            } else {
                echo $res = json_api(1, 'Berhasil non aktif data');
            }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'master_data/masteroutlet/action_change';
        $d[1] == 1 ? $logMethod = 'DISABLE' : $logMethod = 'ENABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    // public function action_delete($id)
    // {
    //     validate_ajax();
    //     $this->global_model->checkAccessMenuAction($this->_module,'delete');

    //     $data=array(
    //         'status'=>-5,
    //         'updated_on'=>date("Y-m-d H:i:s"),
    //         'updated_by'=>$this->session->userdata('username'),
    //         );

    //     $id = $this->enc->decode($id);

    //     $this->db->trans_begin();
    //     $this->outlet->update_data($this->_table,$data," id='".$id."'");

    //     if ($this->db->trans_status() === FALSE)
    //     {
    //         $this->db->trans_rollback();
    //         echo $res=json_api(0, 'Gagal delete data');
    //     }
    //     else
    //     {
    //         $this->db->trans_commit();
    //         echo $res=json_api(1, 'Berhasil delete data');
    //     }    

    //     /* Fungsi Create Log */
    //     $createdBy   = $this->session->userdata('username');
    //     $logUrl      = site_url().'master_data/masteroutlet/action_delete';
    //     $logMethod   = 'DELETE';
    //     $logParam    = json_encode($data);
    //     $logResponse = $res;

    //     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    // // }


}
