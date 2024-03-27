<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
/*
    echance penambahan validasi dan penambahan token csrf 
    21-07-2023 by adat
*/

class CountryCode extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('CountryCodeModel','country');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_iso_3166_1';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/countryCode';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->country->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Kode Negara',
            'content'  => 'countryCode/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Kode Negara';

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $name=trim($this->input->post("name", true));
        $officialName=trim($this->input->post("officialName", true));
        // $continentAlpha2=trim($this->input->post("continentAlpha2"));
        $alpha2=trim($this->input->post("alpha2", true));
        $alpha3=trim($this->input->post("alpha3", true));
        $numericCountry=trim($this->input->post("numericCountry", true));
        $codeDial=trim($this->input->post("codeDial", true));        

        $this->form_validation->set_rules("name"," Nama ","required|max_length[255]");
        $this->form_validation->set_rules("officialName"," Nama Resmi","required|max_length[255]");
        // $this->form_validation->set_rules("continentAlpha2","","required");
        $this->form_validation->set_rules("alpha2"," Alpha 2 ","required|max_length[2]");
        $this->form_validation->set_rules("alpha3"," Alpha 3 ","required|max_length[3]");
        $this->form_validation->set_rules("numericCountry","Nomor Negara","required|is_natural|max_length[3]");

        if(!empty($codeDial))
        {
            $this->form_validation->set_rules("codeDial","Kode panggilan Negara","max_length[255]|callback_numeric_dash");
        } 

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('is_natural','%s harus angka!');
        $this->form_validation->set_message('is_natural_no_zero','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');

        // set messege angka
        $this->form_validation->set_message('numeric_dash','%s harus angka!');

        
        $data=array(
                    'name'=>$name,
                    'official_name'=>$officialName,
                    'alpha_2'=>$alpha2,
                    'alpha_3'=>$alpha3,
                    'numeric_code'=>$numericCountry,
                    'dial_code'=>$codeDial,
                    // 'continent_alpha_2'=>$continentAlpha2,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        $checkName = $this->country->select_data($this->_table, " where upper(name) = upper(".$this->db->escape($name).")");
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($checkName->num_rows() > 0)
        {
            echo $res=json_api(0,"Nama  sudah ada");
        }
        else
        {
            $this->db->trans_begin();

            $this->country->insert_data($this->_table,$data);

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


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/countryCode/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
    function numeric_dash ($num) {
        return ( ! preg_match("/^([0-9-\s])+$/D", $num)) ? FALSE : TRUE;
      }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);

        $data['title'] = 'Edit Kode Negara';
        $data['id'] = $id;
        $data['detail']=$this->country->select_data($this->_table,"where id=".$this->db->escape($id_decode))->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=$this->enc->decode($this->input->post("id", true));

        $name=trim($this->input->post("name", true));
        $officialName=trim($this->input->post("officialName", true));
        // $continentAlpha2=trim($this->input->post("continentAlpha2"));
        $alpha2=trim($this->input->post("alpha2", true));
        $alpha3=trim($this->input->post("alpha3", true));
        $numericCountry=trim($this->input->post("numericCountry", true));
        $codeDial=trim($this->input->post("codeDial", true));        

        $_POST['id'] = $id;

        $this->form_validation->set_rules("id"," Id ","required|is_natural");
        $this->form_validation->set_rules("name"," Nama ","required|max_length[255]");
        $this->form_validation->set_rules("officialName"," Nama Resmi","required|max_length[255]");
        // $this->form_validation->set_rules("continentAlpha2","","required");
        $this->form_validation->set_rules("alpha2"," Alpha 2 ","required|max_length[2]");
        $this->form_validation->set_rules("alpha3"," Alpha 3 ","required|max_length[3]");
        $this->form_validation->set_rules("numericCountry","Nomor Negara","required|is_natural|max_length[3]");
        if(!empty($codeDial))
        {
            $this->form_validation->set_rules("codeDial","Kode panggilan Negara","max_length[255]|callback_numeric_dash");
        } 

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('is_natural','%s harus angka!');
        $this->form_validation->set_message('is_natural_no_zero','%s harus angka!');
        $this->form_validation->set_message('alpha_numeric','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');

        // set messege angka
        $this->form_validation->set_message('numeric_dash','%s harus angka!');
        

        $data=array(
                        'name'=>$name,
                        'official_name'=>$officialName,
                        'alpha_2'=>$alpha2,
                        'alpha_3'=>$alpha3,
                        'numeric_code'=>$numericCountry,
                        'dial_code'=>$codeDial,
                        // 'continent_alpha_2'=>$continentAlpha2,
                        'updated_by'=>$this->session->userdata('username'),
                        'updated_on'=>date("Y-m-d H:i:s"),
                    );                   

        $checkName = $this->country->select_data($this->_table, " where upper(name) = upper(".$this->db->escape($name).") and id != ".$this->db->escape($id));

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($checkName->num_rows() > 0)
        {
            echo $res=json_api(0,"Nama  sudah ada");
        }        
        else
        {
            $this->db->trans_begin();

            $this->country->update_data($this->_table,$data,"id=".$this->db->escape($id));

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


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/countryCode/action_edit';
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
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->country->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            if ($d[1]==1)
            {
                echo $res=json_api(0, 'Gagal aktif');
            }
            else
            {
                echo $res=json_api(0, 'Gagal non aktif');
            }
            
        }
        else
        {
            $this->db->trans_commit();
            if ($d[1]==1)
            {
                echo $res=json_api(1, 'Berhasil aktif data');
            }
            else
            {
                echo $res=json_api(1, 'Berhasil non aktif data');
            }
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/bank/action_change';
        $d[1]==1?$logMethod='ENABLE':$logMethod='DISABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $id = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->country->update_data($this->_table,$data," id='".$id."'");

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal delete data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil delete data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/bank/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
