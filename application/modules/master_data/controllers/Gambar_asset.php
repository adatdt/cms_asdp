<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gambar_asset extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('M_gambar_asset', 'gambar');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_banner';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/gambar_asset';
        $this->_uri_array = array(";" , "/" , "?" , ":" , "@" , "&" , "=" , "+" , "$" , ",", "(", ")");
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        $this->global_model->checkAccessMenuAction($this->_module, 'import_excel');
        if ($this->input->is_ajax_request()) {
            $rows = $this->gambar->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Upload Gambar Asset',
            'content'  => 'gambar_asset/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add')),

        );

        $this->load->view('default', $data);
    }

    public function add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'add');

        $data['title'] = 'Tambah Gambar';
        // $data = array(
        //     'title'    => 'Tambah Vehicle Group',
        //     'merchant'  => $this->vehiclegroup->select_data("app.t_mtr_merchant","where status=1 order by merchant_name asc")->result(),

        // );

        $this->load->view($this->_module . '/add', $data);
    }


    public function action_import_excel()
    {
        validate_ajax();
        // $nama=trim($this->input->post('name'));
        $this->global_model->checkAccessMenuAction($this->_module, 'add');

        $this->form_validation->set_rules('desc', 'Description', 'required');
        $this->form_validation->set_rules('module', 'Module', 'required');
        // $this->form_validation->set_rules('id', 'Id', 'required');
        // $this->form_validation->set_rules('transfer_fee', 'Biaya Transfer ', 'required|numeric');

        $this->form_validation->set_message('required', '%s harus diisi!');
        $this->form_validation->set_message('numeric', '%s Harus Berupa angka!');

        $desc = trim($this->input->post('desc'));
        $module = trim($this->input->post('module'));
        $url = trim($this->input->post('url'));
        if (empty($url)) {
            $url = '#';
        }

        $config['upload_path']          = './uploads/banner/gambar_asset';
        $config['allowed_types']        = 'gif|jpg|png|jpeg|webp';
        $config['max_size']             = 10000;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);


        $nama = $_FILES["berkas"]["name"];
        $fileName = str_replace(" ", "_", $nama);
        $nameOnly = str_replace(" ", "_", pathinfo($fileName, PATHINFO_FILENAME));
        $module = str_replace(" ", "_", $module);
        $dir = "./uploads/banner/gambar_asset/";
        $path = $dir . $fileName;
        $active = "false";
        
        //jika gif dan webp maka nama akan seperti awal
        switch($_FILES["berkas"]["type"]) {
            case "image/webp":
                $newPath = $path;
                $newName = $fileName;
                break;
            case "image/gif";
                $newPath = $path;
                $newName = $fileName;
                break;
            default:
                $newName = $nameOnly . '.webp';
                $newPath = $dir . $newName;
        }

        // $sql = "select a.order from app.t_mtr_banner a where a.module = 'cara_pengukuran_kendaraan' and a.active = 't' order by a.order desc limit 1";
        // $query     = $this->db->query($sql);
        // $rows_data = $query->result();
        // if (!empty($rows_data)){
        //     $order = $rows_data[0]->order;
        // }
        // else {
        $order = 0;
        // }
        // $json = json_decode($rows_data, true);
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

        $check1 = $this->gambar->select_data($this->_table, " where upper(module)=upper('" . $module . "') and status = 1");
        $check2 = $this->gambar->select_data($this->_table, " where upper(name)=upper('" . $newName . "') and module != 'slider_web_reservasi' and status = 1");
        $check3 = $this->contains($nama,$this->_uri_array);

        // print_r($data); exit;
        if ($check1->num_rows() > 0) {
            echo $res = json_api(0, "Nama module sudah ada.");
        } else if ($check2->num_rows() > 0) {
            echo $res = json_api(0, "Nama file gambar sudah ada.");
        } else if ($check3 == true) {
            echo $res = json_api(0, "Nama file gambar tidak boleh mengandung (" . implode(', ', $this->_uri_array).")");
        } else {
            if (!$this->upload->do_upload('berkas')) {
                $error = array('error' => $this->upload->display_errors());
                // print_r($error);
                echo $res = json_api(0, $error['error']);
            } else {
                $dataupload = array('upload_data' => $this->upload->data());

                if ($_FILES["berkas"]["type"] != "image/webp" && $_FILES["berkas"]["type"] != "image/gif") {
                    // To convert a image to Webp, we can do this:
                    // Create and save
                        convertToWebp($dir, $_FILES["berkas"]["tmp_name"], $fileName, $newName);
                    // end convert a image to Webp

                    // Remove file old ext
                    // @unlink($path);
                }

                $this->db->trans_begin();

                $this->gambar->insert_data($this->_table, $data);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo $res = json_api(0, 'Gagal tambah data');
                } else {
                    $this->db->trans_commit();
                    echo $res = json_api(1, 'Berhasil tambah data');
                }
                // echo $res=json_api(1, $data);
                // echo $order+1;
            }
        }


        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'master_data/gambar_asset/action_add';
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

        /* data */
        $data = array(
            'active' => $d[1],
            'updated_on' => date("Y-m-d H:i:s"),
            'updated_by' => $this->session->userdata('username'),
        );

        $sql = "select a.* from app.t_mtr_banner a where a.module = '" . $d[2] . "' and a.id != " . $d[0] . " and active = 't' and status = 1";
        $query     = $this->db->query($sql);
        if ($d[1] == 'true' && $query->num_rows() > 0) {
            echo $res = json_api(0, 'Sudah ada 1 data module ' . $d[2] . ' yang aktif');
        } else {
            $this->db->trans_begin();
            $this->gambar->update_data($this->_table, $data, "id=" . $d[0]);

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
        $logUrl      = site_url() . 'master_data/gambar_asset/action_change';
        $d[1] == 1 ? $logMethod = 'ENABLE' : $logMethod = 'DISABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');
        $this->global_model->checkAccessMenuAction($this->_module, 'import_excel');

        $id_decode = $this->enc->decode($id);

        $data['title'] = 'Edit Order';
        $data['id'] = $id;
        $data['detail'] = $this->gambar->select_data($this->_table, "where id=$id_decode")->row();

        $this->load->view($this->_module . '/edit', $data);
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');
        $this->global_model->checkAccessMenuAction($this->_module, 'import_excel');

        $order = trim($this->input->post('order'));
        $desc = trim($this->input->post('desc'));
        $module = trim($this->input->post('module'));
        $name = trim($this->input->post('name'));
        $path = trim($this->input->post('path'));
        $url = trim($this->input->post('url'));
        if (empty($url)) {
            $url = '#';
        }
        // $transfer_fee=trim($this->input->post('transfer_fee'));

        $id = $this->enc->decode($this->input->post('id'));

        $this->form_validation->set_rules('order', 'Order', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');
        // $this->form_validation->set_rules('transfer_fee', 'Biaya Transfer ', 'required|numeric');

        $this->form_validation->set_message('required', '%s harus diisi!');
        $this->form_validation->set_message('numeric', '%s Harus Berupa angka!');

        $config['upload_path']          = './uploads/banner/gambar_asset';
        $config['allowed_types']        = 'gif|jpg|png|jpeg|webp';
        $config['max_size']             = 10000;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);
        $nama ="";
        if ($_FILES["berkas"]["error"] == 4) {
            //means there is no file uploaded
            $fileName = str_replace(" ", "_", $name);
            $namafix = $fileName;
            $num = 0;
        } else {
            $nama = $_FILES["berkas"]["name"];
            $fileName = str_replace(" ", "_", $nama);
            $nameOnly = str_replace(" ", "_", pathinfo($fileName, PATHINFO_FILENAME));
            $dir = "./uploads/banner/gambar_asset/";
            $path = $dir . $fileName;

            //jika gif dan webp maka nama akan seperti awal
            switch($_FILES["berkas"]["type"]) {
                case "image/webp":
                    $newPath = $path;
                    $newName = $fileName;
                    break;
                case "image/gif";
                    $newPath = $path;
                    $newName = $fileName;
                    break;
                default:
                    $newName = $nameOnly . '.webp';
                    $newPath = $dir . $newName;
            }

            $check = $this->gambar->select_data($this->_table, " where upper(name)=upper('" . $fileName . "') and module != 'slider_web_reservasi' and status = 1");
            $num = $check->num_rows();
        }

        


        $data = array(
            'path' => $path,
            'name' => $fileName,
            // 'module'=>$module,
            'order' => $order,
            'desc' => $desc,
            'url_target' => $url,
            'updated_by' => $this->session->userdata('username'),
            'updated_on' => date("Y-m-d H:i:s"),
        );
        // print_r($data);exit;
        // $check=$this->layanantarif->select_data($this->_table," where order='".$order."' and active = 't' ");
        // $sql = "select a.* from app.t_mtr_banner a where a.module = '".$module."' and a.order = ".$order." and active = 't' and a.id != ".$id."";
        $sql = "select a.* from app.t_mtr_banner a where a.module = '" . $module . "' and a.id != " . $id . " and status = 1";
        $query     = $this->db->query($sql);

        // $check=$this->gambar->select_data($this->_table," where upper(name)=upper('".$fileName."')");
        
        $check3 = false;
        if(!empty($nama))
        {
            $check3 = $this->contains($nama,$this->_uri_array);
        }

        if ($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
        } else if ($query->num_rows() > 0) {
            echo $res = json_api(0, "Nama module sudah ada.");
        } else if ($num > 0) {
            echo $res = json_api(0, "Nama file gambar sudah ada.");
        } else if ($check3 == true) {
            echo $res = json_api(0, "Nama file gambar tidak boleh mengandung (" . implode(', ', $this->_uri_array).")");
        } else {
            if (!($_FILES["berkas"]["error"] == 4))  //ada uploaded file
            {
                if (!$this->upload->do_upload('berkas')) {
                    $error = array('error' => $this->upload->display_errors());
                    // print_r($error);
                    echo $res = json_api(0, $error['error']);
                } else {
                    $dataupload = array('upload_data' => $this->upload->data());
                    // convertToWebp($dir, $_FILES["berkas"]["tmp_name"], $fileName, $newName, 80);

                    if ($_FILES["berkas"]["type"] != "image/webp" && $_FILES["berkas"]["type"] != "image/gif") {
                        // To convert a image to Webp, we can do this:
                        // Create and save
                            convertToWebp($dir, $_FILES["berkas"]["tmp_name"], $fileName, $newName, 80);
                        // end convert a image to Webp
    
                        // Remove file old ext
                        // @unlink($path);
                    }

                    $this->db->trans_begin();

                    $this->gambar->update_data($this->_table, $data, "id=$id");

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        echo $res = json_api(0, 'Gagal edit data');
                    } else {
                        $this->db->trans_commit();
                        echo $res = json_api(1, 'Berhasil edit data');
                    }
                }
            } else {
                $this->db->trans_begin();

                $this->gambar->update_data($this->_table, $data, "id=$id");

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo $res = json_api(0, 'Gagal edit data');
                } else {
                    $this->db->trans_commit();
                    echo $res = json_api(1, 'Berhasil edit data');
                }
            }
        }


        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'master_data/gambar_asset/action_edit';
        $logMethod   = 'EDIT';
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
}
