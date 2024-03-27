<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/*
 * @author      <dayungjaya.nutech@gmail.com>
 * @copyright  2024
 *
*/

class Self_service_extension extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_self_service_extension','service_extension');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_phone_extension';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'device_management/self_service_extension';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->service_extension->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Self Service Extension',
            'content'  => 'self_service_extension/index',
            'port' => $this->service_extension->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data = array(
            'title' => 'Tambah Self Service Extension',
            'port'  => $this->global_model->select_data("app.t_mtr_port", "WHERE status = '1' ORDER BY name ASC")->result(),
        );

        $this->load->view($this->_module.'/add',$data);
    }


    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');


        $this->form_validation->set_rules('usernamePhone', 'usernamePhone', 'trim|required|min_length[1]|max_length[100]|callback_special_char', array('special_char' => 'Username phone memuat invalid karakter'));
        $this->form_validation->set_rules('extensionPhone', 'extensionPhone', 'trim|required|min_length[1]|max_length[100]|callback_special_char', array('special_char' => 'Extension phone memuat invalid karakter'));
        $this->form_validation->set_rules('passwordPhone', 'passwordPhone', 'trim|required|min_length[1]|max_length[100]|callback_special_char', array('special_char' => 'Password phone memuat invalid karakter'));

        $this->form_validation->set_message('required','%s harus diisi!');


        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {
            $usernamePhone =trim($this->input->post('usernamePhone'));
            $extensionPhone =trim($this->input->post('extensionPhone'));
            $passwordPhone =trim($this->input->post('passwordPhone'));
            $portId = $this->enc->decode($this->input->post('port'));

            $data=array('username_phone'=>$usernamePhone,
                    'extension_phone'=>$extensionPhone,
                    'password_phone'=>$passwordPhone,
                    'port_id'=> $portId,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
            );

            //check validation username and extentions
            $check_username_phone=$this->service_extension->select_data($this->_table,"where username_phone='$usernamePhone' and port_id='$portId' and status not in (-5)");
            $check_extension_phone=$this->service_extension->select_data($this->_table,"where extension_phone='$extensionPhone' and port_id='$portId' and status not in (-5)");
        
            if($check_username_phone->num_rows()>0)
            {
                echo $res=json_api(0, 'User phone sudah ada');
            } else if($check_extension_phone->num_rows()>0)
            {
                echo $res=json_api(0, 'Extention phone sudah ada');
            }
            else
            {

                $this->db->trans_begin();

                $this->service_extension->insert_data($this->_table,$data);
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
            
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'device_management/self_service_extension/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $self_service_id=$this->enc->decode($id);

        $data['title'] = 'Edit Self Service Extension';
        $data['detail']= $this->service_extension->select_data($this->_table,"where id={$self_service_id} ")->row();
        $data['port']=$this->service_extension->select_data("app.t_mtr_port","where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id = $this->enc->decode($this->input->post('id'));
        $portId = $this->enc->decode($this->input->post('port'));
        $_POST['id'] = $id;
        $_POST['port'] = $portId;

        $this->form_validation->set_rules('id', 'id', 'required');
        $this->form_validation->set_rules('port', 'pelabuhan', 'required');
        $this->form_validation->set_rules('usernamePhone', 'usernamePhone', 'trim|required|min_length[1]|max_length[100]|callback_special_char', array('special_char' => 'Username phone memuat invalid karakter'));
        $this->form_validation->set_rules('extensionPhone', 'extensionPhone', 'trim|required|min_length[1]|max_length[100]|callback_special_char', array('special_char' => 'Extension phone memuat invalid karakter'));
        $this->form_validation->set_rules('passwordPhone', 'passwordPhone', 'trim|required|min_length[1]|max_length[100]|callback_special_char', array('special_char' => 'Password phone memuat invalid karakter'));

        $this->form_validation->set_message('required','%s harus diisi!');
        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
            exit;
        }
        else
        {
         
            $usernamePhone =trim($this->input->post('usernamePhone'));
            $extensionPhone =trim($this->input->post('extensionPhone'));
            $passwordPhone =trim($this->input->post('passwordPhone'));            

            $data=array('username_phone'=>$usernamePhone,
                    'extension_phone'=>$extensionPhone,
                    'password_phone'=>$passwordPhone,
                    'port_id'=> $portId,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
            );

            //check validation username and extentions
            $check_username_phone=$this->service_extension->select_data($this->_table,"where username_phone='$usernamePhone' and port_id='$portId' and status not in (-5) and id !=".$id);            
            $check_extension_phone=$this->service_extension->select_data($this->_table,"where extension_phone='$extensionPhone' and port_id='$portId' and status not in (-5) and id !=".$id);

            // pakah sudah terpakai
            $checkIsUsed = $this->service_extension->select_data($this->_table,"where id =".$id)->row();        
        
            if($check_username_phone->num_rows()>0)
            {
                echo $res=json_api(0, 'User phone sudah ada');
            } else if($check_extension_phone->num_rows()>0)
            {
                echo $res=json_api(0, 'Extention phone sudah ada');
            }
            else if($checkIsUsed->is_used != 0 )
            {
                echo $res=json_api(0, 'Tidak Bisa diedit karena sudah di pakai, silahkan reload halaman');
            }
            else
            {

                $this->db->trans_begin();
                $this->service_extension->update_data($this->_table,$data,"id='".$id."'");

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
            
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'device_management/self_service_extension/action_edit';
        $logMethod   = 'EDIT';
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

        // pakah sudah terpakai
        $checkIsUsed = $this->service_extension->select_data($this->_table,"where id =".$id)->row();        

        if($checkIsUsed->is_used != 0 )
        {
            echo $res=json_api(0, 'Tidak Bisa dihapus karena sudah di pakai, silahkan reload halaman');
        }
        else
        {
            $this->db->trans_begin();
            $this->service_extension->update_data($this->_table, $data, " id='".$id."'");
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $res = json_api(0, 'Gagal delete data');
            }
            else {
                $this->db->trans_commit();
                echo $res = json_api(1, 'Berhasil delete data');
            }
        }        

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'device_management/self_service_extension/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    


}