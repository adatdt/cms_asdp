<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting_param extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('m_setting_param', 'param');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_custom_param';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/setting_param';
        $this->_maxsize = 5120; //max size value for upload refund (param_name max_file_size_refund)
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->param->dataList();
            echo json_encode($rows);
            exit;
        }
        // $sql = "SELECT distinct a.category_name from app.t_mtr_custom_param a where a.status <> -5
        //         order by a.category_name";
        // $query     = $this->db->query($sql);
        // $rows_data = $query->result();
        // $kategori = array();

        // foreach ($rows_data as $row) {
        // 	if ($row->category_name == null) {
        // 		$row->category_name = 'Lainnya';
        // 	}
        // 	$kategori[] = $row;
        // }


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Seting Parameter',
            // 'kategori' => $this->param->select_data("app.t_mtr_custom_param_category", "where status = '1' and category_name <> 'secret' order by category_name asc")->result(),
            'content'  => 'setting_param/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add')),

        );

        $this->load->view('default', $data);
    }

    public function add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'add');

        $data = array(
            'title' => 'Tambah Setting Parameter',
            'kategori' => $this->param->select_data("app.t_mtr_custom_param_category", "where status = '1' order by category_name asc")->result(),
        );

        $this->load->view($this->_module . '/add', $data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module, 'add');

        $nama_param = trim(htmlspecialchars_decode(base64_decode($this->input->post('name'))));
        $value_param = trim(htmlspecialchars_decode(base64_decode($this->input->post('value_param'))));
        $tipe_param = trim(htmlspecialchars_decode(base64_decode($this->input->post('tipe_param'))));
        $tipe_value = trim(htmlspecialchars_decode(base64_decode($this->input->post('tipe_value'))));
        $info = trim(htmlspecialchars_decode(base64_decode($this->input->post('info'))));
        // $category = trim($this->input->post('category'));

        // if ($category == 'lainnya') {
        //     $category = null;
        // }

        $this->form_validation->set_rules('name', 'Nama', 'required');
        $this->form_validation->set_rules('value_param', 'Value Parameter ', 'required');
        $this->form_validation->set_rules('tipe_param', 'Tipe Parameter', 'required');
        $this->form_validation->set_rules('tipe_value', 'Tipe Value', 'required');
        $this->form_validation->set_rules('info', 'Info', 'required');
        // $this->form_validation->set_rules('category', 'Category', 'required');

        $this->form_validation->set_message('required', '%s harus diisi!');


        $data = array(
            'param_name' => $nama_param,
            'param_value' => $value_param,
            'type' => $tipe_param,
            'value_type' => $tipe_value,
            'info' => $info,
            'status' => 1,
            // 'category_name' => $category,
            'created_by' => $this->session->userdata('username'),
            'created_on' => date("Y-m-d H:i:s"),
        );

        // ceck data jika nama param sudah ada

        $check = $this->param->select_data($this->_table, " where upper(param_name)=upper('" .$nama_param. "') and status not in (-5) ");

        if ($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
        } else if ($check->num_rows() > 0) {
            echo $res = json_api(0, "Nama sudah ada.");
        } else {

            $this->db->trans_begin();

            $this->param->insert_data($this->_table, $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res = json_api(0, 'Gagal tambah data');
            } else {
                $this->db->trans_commit();
                echo $res = json_api(1, 'Berhasil tambah data');
            }
        }


        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'master_data/settin_param/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');

        $id_decode = $this->enc->decode($id);
        $max_upload = $this->convertPHPSizeToKiloBytes(get_cfg_var('upload_max_filesize'));   //get value from php.ini not .htaccess and convert to KB
        $max_post = $this->convertPHPSizeToKiloBytes(get_cfg_var('post_max_size'));
        $memory_limit = $this->convertPHPSizeToKiloBytes(get_cfg_var('memory_limit'));


        // $data['getMaxResize'] = min($max_upload, $max_post, $memory_limit) / 2;

        //ambil nilai max size agar max resize tidak melebihi max size
        $max_resize = $this->param->select_data("app.t_mtr_custom_param", "where param_name = 'max_file_size_refund'")->row();

        $data['getMaxSize'] = $this->_maxsize;
        $data['getMaxResize'] = $max_resize->param_value;
        $data['title'] = 'Edit Setting Parameter';
        $data['detail'] = $this->param->select_data($this->_table, "where param_id=$id_decode")->row();
        // $data['kategori'] = $this->param->select_data("app.t_mtr_custom_param_category", "where status = '1' order by category_name asc")->result();

        $this->load->view($this->_module . '/edit', $data);
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');

        $nama_param = trim(htmlspecialchars_decode(base64_decode($this->input->post('name'))));
        $value_param = trim(htmlspecialchars_decode(base64_decode($this->input->post('value_param'))));
        $checkbox_param = trim($this->input->post('checkbox_param'));
        $tipe_param = trim(htmlspecialchars_decode(base64_decode($this->input->post('tipe_param'))));
        $tipe_value = trim(htmlspecialchars_decode(base64_decode($this->input->post('tipe_value'))));
        $info = trim(htmlspecialchars_decode(base64_decode($this->input->post('info'))));
        $id = $this->enc->decode($this->input->post('id'));

        // $value_param = htmlspecialchars_decode(base64_decode($val_param));

        // print_r($value_param);exit;
        // $category = trim($this->input->post('category'));

        // if ($category == 'lainnya' || $category == null) {
        //     $category = null;
        // }

        if (stripos($tipe_value, 'bool') !== FALSE) {
            // $value_param = $checkbox_param;
            if ($value_param == 'true' || $value_param == 'false') {
                switch ($checkbox_param) {
                    case '1':
                        $value_param = 'true';
                        break;

                    default:
                        $value_param = 'false';
                        break;
                }
            } else {
                $value_param = $checkbox_param;
            }
        }

        // $this->form_validation->set_rules('name', 'Nama', 'required');
        $this->form_validation->set_rules('value_param', 'Value Parameter ', 'required');
        $this->form_validation->set_rules('tipe_param', 'Tipe Parameter', 'required');
        $this->form_validation->set_rules('tipe_value', 'Tipe Value', 'required');
        $this->form_validation->set_rules('info', 'Info', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');

        $this->form_validation->set_message('required', '%s harus diisi!');

        // $check=$this->param->select_data($this->_table," where upper(param_name)=upper('".$nama_param."') and param_id !=$id and status not in (-5)");
        $max_resize = $this->param->select_data("app.t_mtr_custom_param", "where param_name = 'max_file_size_refund'")->row();

        $data = array(

            // 'param_name'=>$nama_param,
            'param_value' => $value_param,
            'type' => $tipe_param,
            'value_type' => $tipe_value,
            'info' => $info,
            // 'category_name' => $category,
            'updated_by' => $this->session->userdata('username'),
            'updated_on' => date("Y-m-d H:i:s"),
        );

        if ($this->form_validation->run() === false) {
            echo $res = json_api(0, validation_errors());
        } else if ($nama_param == 'max_file_size_refund' && $value_param > $this->_maxsize) {
            echo $res = json_api(0, 'Nilai parameter tidak boleh melebihi ' . $this->_maxsize . ' KB');
        } else if ($nama_param == 'max_file_resize_refund' && $value_param > $max_resize->param_value) {
            echo $res = json_api(0, 'Nilai parameter tidak boleh melebihi nilai max_file_size_refund');
        } else if ($nama_param == 'limit_same_char_nik' && $value_param > 17) {
            echo $res = json_api(0, 'Nilai parameter tidak boleh melebihi 17');
        } else if ($nama_param == 'limit_same_char_nik' && $value_param < 3) {
            echo $res = json_api(0, 'Nilai parameter tidak boleh kurang dari 3');
        } else if ($nama_param == 'booking_expired' && $value_param < 15) {
            echo $res = json_api(0, 'Nilai parameter tidak boleh kurang dari 15');
        } else {

            $this->db->trans_begin();

            $this->param->update_data($this->_table, $data, "param_id=$id");

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
        $logUrl      = site_url() . 'master_data/payment_method/action_edit';
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
            'updated_on' => date("Y-m-d H:i:s"),
            'updated_by' => $this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->param->update_data($this->_table, $data, "param_id=" . $d[0]);

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
        $logUrl      = site_url() . 'master_data/setting_param/action_change';
        $d[1] == 1 ? $logMethod = 'DISABLE' : $logMethod = 'ENABLE';
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

        $this->db->trans_begin();
        $this->param->update_data($this->_table, $data, " param_id='" . $id . "'");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $res = json_api(0, 'Gagal delete data');
        } else {
            $this->db->trans_commit();
            echo $res = json_api(1, 'Berhasil delete data');
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url() . 'master_data/setting_param/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function convertPHPSizeToKiloBytes($sSize)
    {
        $sSuffix = strtoupper(substr($sSize, -1));
        if (!in_array($sSuffix, array('P', 'T', 'G', 'M', 'K'))) {
            return (int)$sSize;
        }
        $iValue = substr($sSize, 0, -1);
        switch ($sSuffix) {
            case 'P':
                $iValue *= 1024;
                // Fallthrough intended
            case 'T':
                $iValue *= 1024;
                // Fallthrough intended
            case 'G':
                $iValue *= 1024;
                // Fallthrough intended
            case 'M':
                $iValue *= 1024;
                break;
        }
        return (int)$iValue;
    }
}
