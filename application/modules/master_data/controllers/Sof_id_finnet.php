<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Sof_id_finnet extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_sof_id_finnet','sofid');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_sof_id_finnet';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/sof_id_finnet';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->sofid->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Sof ID Finnet',
            'content'  => 'sof_id_finnet/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Sof ID Finnet';
        // $data = array(
        //     'title'    => 'Tambah Vehicle Group',
        //     'merchant'  => $this->vehiclegrouptype->select_data("app.t_mtr_merchant","where status=1 order by merchant_name asc")->result(),

        // );

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $sof_id=trim($this->input->post('sof_id'));
        $sof_name=trim($this->input->post('sof_name'));
        $category=trim($this->input->post('category'));
        $mitraCode=trim($this->input->post('mitraCode'));

        $this->form_validation->set_rules('sof_id', 'Sof ID ', 'required|callback_sof_id');
        $this->form_validation->set_rules('sof_name', 'Sof Name ', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required');
        $this->form_validation->set_rules('mitraCode', 'Kode Mitra', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $data=array(
                    'sof_id'=>$sof_id,
                    'sof_name'=>$sof_name,
                    'category'=>$category,
                    'mitra_code'=>$mitraCode,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );


        // ceck data jika username sudah ada
        $check=$this->sofid->select_data($this->_table," where upper(sof_id)=upper('".$sof_id."') ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Sof ID sudah ada.");
        }
        else
        {

            $this->db->trans_begin();

            $this->sofid->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/sof_id_finnet/action_add';
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

        $data['title'] = 'Edit Vehicle Group';
        $data['id'] = $id;
        $data['detail']=$this->sofid->select_data($this->_table,"where id=$id_decode")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $sof_id=trim($this->input->post('sof_id'));
        $sof_name=trim($this->input->post('sof_name'));
        $category=trim($this->input->post('category'));
        $mitraCode=trim($this->input->post('mitraCode'));

		$id=$this->enc->decode($this->input->post('id'));
				
        $this->form_validation->set_rules('sof_id', 'Sof ID ', 'required|callback_sof_id');
        $this->form_validation->set_rules('sof_name', 'Sof Name ', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required');
        $this->form_validation->set_rules('mitraCode', 'Kode Mitra', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        
        $data=array(
                    'sof_id'=>$sof_id,
                    'sof_name'=>$sof_name,
                    'category'=>$category,
                    'mitra_code'=>$mitraCode,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        
        $check=$this->sofid->select_data($this->_table," where upper(sof_id)=upper('".$sof_id."') and id != '".$id."'");
        
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama sudah ada.");
        }
        else
        {

            $this->db->trans_begin();

            $this->sofid->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'master_data/sof_id_finnet/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public  function sof_id($str)
    {

        $pattern = '/ /';
        $result = preg_match($pattern, $str);

        if ($result)
        {
            $this->form_validation->set_message('sof_id', 'Sof ID tidak boleh mengandung spasi');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    // public function action_change($param)
    // {
    //     validate_ajax();
    //     $p = $this->enc->decode($param);
    //     $d = explode('|', $p);

    //     /* data */
    //     $data = array(
    //         'status' => $d[1],
    //         'updated_on'=>date("Y-m-d H:i:s"),
    //         'updated_by'=>$this->session->userdata('username'),
    //     );


    //     $this->db->trans_begin();
    //     $this->vehiclegrouptype->update_data($this->_table,$data,"id=".$d[0]);

    //     if ($this->db->trans_status() === FALSE)
    //     {
    //         $this->db->trans_rollback();
    //         if ($d[1]==1)
    //         {
    //             echo $res=json_api(0, 'Gagal aktif');
    //         }
    //         else
    //         {
    //             echo $res=json_api(0, 'Gagal non aktif');
    //         }
            
    //     }
    //     else
    //     {
    //         $this->db->trans_commit();
    //         if ($d[1]==1)
    //         {
    //             echo $res=json_api(1, 'Berhasil aktif data');
    //         }
    //         else
    //         {
    //             echo $res=json_api(1, 'Berhasil non aktif data');
    //         }
    //     }   

    //     /* Fungsi Create Log */
    //     $createdBy   = $this->session->userdata('username');
    //     $logUrl      = site_url().'master_data/vehicle_group_type/action_change';
    //     $d[1]==1?$logMethod='ENABLE':$logMethod='DISABLE';
    //     $logParam    = json_encode($data);
    //     $logResponse = $res;

    //     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    // }

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
    //     $this->vehiclegrouptype->update_data($this->_table,$data," id='".$id."'");

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
    //     $logUrl      = site_url().'master_data/vehicle_group_type/action_delete';
    //     $logMethod   = 'DELETE';
    //     $logParam    = json_encode($data);
    //     $logResponse = $res;

    //     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    // }

}
