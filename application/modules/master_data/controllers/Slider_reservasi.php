<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Slider_reservasi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('M_slider_reservasi', 'slider');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_banner';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/slider_reservasi';
        $this->_uri_array = array(";" , "/" , "?" , ":" , "@" , "&" , "=" , "+" , "$" , ",", "(", ")");
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        $this->global_model->checkAccessMenuAction($this->_module, 'import_excel');
        if ($this->input->is_ajax_request()) {
            $rows = $this->slider->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Slider Web Reservasi',
            'content'  => 'slider_reservasi/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add')),

        );

        $this->load->view('default', $data);
    }

    public function add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'add');

        $data['title'] = 'Tambah Gambar';
        $data['typeModule'] = $this->typeModule();
        
        $this->load->view($this->_module . '/add', $data);
    }


    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'add');

        $this->form_validation->set_rules('desc', 'Description', 'required');
        $this->form_validation->set_rules('typeModule', 'Tipe', 'required');
        $this->form_validation->set_message('required', '%s harus diisi!');
        $this->form_validation->set_message('numeric', '%s Harus Berupa angka!');

        $tipe=trim($this->input->post('typeModule'));
        $desc = trim($this->input->post('desc'));
        $url = trim($this->input->post('url'));
        if (empty($url)) {
            $url = '#';
        }
        $tipe=='slider_web_reservasi'?$getType=$tipe:$getType='slider_popup';
    
        $config['upload_path']          = './uploads/banner/'.$getType;
        $config['allowed_types']        = 'gif|jpg|png|jpeg|webp';
        $config['max_size']             = 300;

        $this->load->library('upload');
        $this->upload->initialize($config);

        $nama = $_FILES["berkas"]["name"];
        $fileName = str_replace(" ", "_", $nama);
        $nameOnly = str_replace(" ", "_", pathinfo($fileName, PATHINFO_FILENAME));

        // $module = "slider_web_reservasi";
        $module = $getType;

        // $desc = "Slider Web Reservasi";
        $dir = "./uploads/banner/".$getType."/";
        $path = $dir . $fileName;
        $active = "false";

        //jika gif dan webp maka nama akan seperti awal
        switch($_FILES["berkas"]["type"]) {
            case "image/webp":
                $newPath = $path;
                $newName = $fileName;
                break;
            default:
                $newName = $nameOnly . '.webp';
                $newPath = $dir . $newName;
        }

        $sql = "select a.order from app.t_mtr_banner a where a.module = '{$module}' and a.active = 't' and status = 1 order by a.order desc limit 1";
        $query     = $this->db->query($sql);
        $rows_data = $query->result();
        if (!empty($rows_data)) {
            $order = $rows_data[0]->order;
        } else {
            $order = 0;
        }

        $data = array(
            'name' => $fileName,
            'module' => $module,
            'desc' => $desc,
            'path' => $path,
            'order' => $order + 1,
            'url_target' => $url,
            'active' => $active,
            'created_by' => $this->session->userdata('username'),
            'created_on' => date("Y-m-d H:i:s"),
        );

        // print_r($data); exit;

        $check = $this->slider->select_data($this->_table, " where module = '".$module."' and upper(name)=upper('" . $fileName . "') and status = 1");
        $check3 = $this->contains($nama,$this->_uri_array);

        if ($check->num_rows() > 0) {
            echo $res = json_api(0, "Nama file sudah ada.");
        }  else if ($check3 == true) {
            echo $res = json_api(0, "Nama file gambar tidak boleh mengandung (" . implode(', ', $this->_uri_array).")");
        } else {
            if (!$this->upload->do_upload('berkas')) {
                $error = array('error' => $this->upload->display_errors());
                echo $res = json_api(0, $error['error']);
            } else {
                $this->upload->data();

                if($tipe=='slider_web_reservasi')
                {
                    //Resize Image
                    $compress['image_library'] = 'gd2';
                    $compress['source_image'] = $path;
                    $compress['create_thumb'] = FALSE;
                    $compress['maintain_ratio'] = FALSE;
                    // $compress['quality'] = '50%';
                    $compress['width'] = 1366;
                    $compress['height'] = 437;
                    $compress['new_image'] = $path;
                    $this->load->library('image_lib', $compress);
                    $this->image_lib->resize();
                    //end Resize Image
                }

                if ($_FILES["berkas"]["type"] != "image/webp") {
                    // To convert a image to Webp, we can do this:
                    // Create and save
                        convertToWebp($dir, $_FILES["berkas"]["tmp_name"], $fileName, $newName);
                    // end convert a image to Webp

                    // Remove file old ext
                    // @unlink($path);
                }

                $this->db->trans_begin();

                $this->slider->insert_data($this->_table, $data);

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
        $logUrl      = site_url() . 'master_data/slider_reservasi/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);
        $module=$d[2];

        /* data */
        $data = array(
            'active' => $d[1],
            'updated_on' => date("Y-m-d H:i:s"),
            'updated_by' => $this->session->userdata('username'),
        );

        $sql = "select a.* from app.t_mtr_banner a where a.module = '".$module."' and a.id != " . $d[0] . " and active = 't' and status = 1";
        $query     = $this->db->query($sql);

        $order = $this->slider->select_data($this->_table, " where module = '".$module."' and id = " . $d[0] . "")->result();
       
        $checkorder = "select a.* from app.t_mtr_banner a where a.module = '".$module."' and a.order = " . $order[0]->order . " and a.active = 't' and a.id != " . $d[0] . " and status !='-5'  ";
        $query2     = $this->db->query($checkorder);

        if ($d[1] == 'false' && $query->num_rows() < 1) {
            echo $res = json_api(0, 'Harus ada 1 data yang aktif');
        } else if ($query2->num_rows() > 0) {
            echo $res = json_api(0, "Nomer order yang aktif sudah ada.");
        } else {
            $this->db->trans_begin();
            $this->slider->update_data($this->_table, $data, "id=" . $d[0]);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                if ($d[1] == 'true') {
                    echo $res = json_api(0, 'Gagal aktif');
                } else {
                    echo $res = json_api(0, 'Gagal non aktif');
                }
            } else {
                $this->db->trans_commit();
                if ($d[1] == 'true') {
                    echo $res = json_api(1, 'Berhasil aktif data');
                } else {
                    echo $res = json_api(1, 'Berhasil non aktif data');
                }
            }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'master_data/slider_reservasi/action_change';
        $d[1] == 'true' ? $logMethod = 'ENABLE' : $logMethod = 'DISABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');

        $id_decode = $this->enc->decode($id);

        $data['title'] = 'Edit Data Slider';
        $data['id'] = $id;
        $data['typeModule'] = $this->typeModule();
        $data['detail'] = $this->slider->select_data($this->_table, "where id=$id_decode")->row();

        $this->load->view($this->_module . '/edit', $data);
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');

        $order = trim($this->input->post('order'));
        $desc = trim($this->input->post('desc'));
        $url = trim($this->input->post('url'));
        if (empty($url)) {
            $url = '#';
        }
        // $transfer_fee=trim($this->input->post('transfer_fee'));

        $id = $this->enc->decode($this->input->post('id'));

        $this->form_validation->set_rules('order', 'Order', 'required');
        $this->form_validation->set_rules('desc', 'Description', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');
        // $this->form_validation->set_rules('transfer_fee', 'Biaya Transfer ', 'required|numeric');

        $this->form_validation->set_message('required', '%s harus diisi!');
        $this->form_validation->set_message('numeric', '%s Harus Berupa angka!');


        $data = array(

            'order' => $order,
            'desc' => $desc,
            'url_target' => $url,
            // 'transfer_fee'=>$transfer_fee,
            'updated_by' => $this->session->userdata('username'),
            'updated_on' => date("Y-m-d H:i:s"),
        );

        $check=$this->slider->select_data($this->_table," where id = " . $id);

        $sql = "select a.* from app.t_mtr_banner a where a.module = '".$check->row()->module."' and a.order = " . $order . " and a.active = 't' and a.id != " . $id . " and status != '-5' ";
        $query     = $this->db->query($sql);

        if ($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
        } else if ($query->num_rows() > 0) {
            echo $res = json_api(0, "Nomer order yang aktif sudah ada.");
        } else {

            $this->db->trans_begin();

            $this->slider->update_data($this->_table, $data, "id=$id");

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
        $logUrl      = site_url() . 'master_data/slider_reservasi/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'delete');

        $data = array(
            'status' => -5,
            'updated_on' => date("Y-m-d H:i:s"),
            'updated_by' => $this->session->userdata('username'),
        );

        $id = $this->enc->decode($id);

        // $sql = "select a.* from app.t_mtr_banner a where a.module = 'slider_web_reservasi' and a.active = 't' and a.id = " . $id . "";
        $sql = "select a.* from app.t_mtr_banner a where  a.active = 't' and a.id = " . $id . "";
        $query     = $this->db->query($sql);

        //get path for delete
        // $sql2 = "select a.path from app.t_mtr_banner a where a.module = 'slider_web_reservasi' and a.id = " . $id . "";

        $sql2 = "select a.path from app.t_mtr_banner a where  a.id = " . $id . "";
        $query2     = $this->db->query($sql2);
        $path = $query2->result();
        $path = $path[0]->path;


        if ($query->num_rows() > 0) {
            echo $res = json_api(0, 'Data masih aktif');
        } else {

            $this->db->trans_begin();
            $this->slider->update_data($this->_table, $data, " id='" . $id . "'");


            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                @unlink($path);
                echo $res = json_api(1, 'Berhasil hapus data');
            } else {
                $this->db->trans_rollback();
                echo $res = json_api(0, 'Gagal hapus data');
            }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'master_data/slider_reservasi/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function contains($str, array $arr)
    {
        foreach($arr as $a) {
            if (stripos($str,$a) !== false) return true;
        }
        return false;
    }

    public function typeModule()
    {
        return array(

            ""=>"Pilih",
            "slider_web_reservasi"=>"slider_web_reservasi",
            "slider_popup"=>"slider_popup",
        );
    }
}
