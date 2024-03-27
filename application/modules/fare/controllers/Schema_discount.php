<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Schema_discount extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_schema_discount','schema');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_discount_schema';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'fare/schema_discount';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->schema->dataList();
            echo json_encode($rows);
            exit;
        }


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Schema Diskon',
            'content'  => 'schema_discount/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'destination'=>$this->schema->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
            'team'=>$this->schema->select_data("core.t_mtr_team","where status not in (-5) order by team_name asc")->result(),
            'ship_class'=>$this->schema->select_data("app.t_mtr_ship_class","where status=1 order by name asc")->result(),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Schema Diskon';
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
      
        /* validation */
        $this->form_validation->set_rules('name', 'Nama Schema', 'trim|required|callback_special_char', array('special_char' => 'Nama Schemaa mengandung invalid karakter'));
        $this->form_validation->set_rules('slug', 'Slug', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'Slug mengandung invalid karakter'));

        $this->form_validation->set_message('required','%s harus diisi!')
                                ->set_message('max_length','{field} Maximal  {param} karakter! ');


        $data = null;
        if($this->form_validation->run() == FALSE)
        {
            echo $res=json_api(0,validation_errors());
        }
        else 
        {

            $schema_name=trim($this->input->post('name'));
            $slug=trim($this->input->post('slug'));


            $this->form_validation->set_rules('name', 'Nama Schema', 'required');
            $this->form_validation->set_rules('slug', 'Slug', 'required');

            $this->form_validation->set_message('required','%s harus diisi!');
            
            $data=array(
                        'description'=>$schema_name,
                        'schema_code'=>$this->createCode(),
                        'slug'=>$slug,
                        'status'=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                        );

            // check harga route jika ada
            $check_fare=$this->schema->select_data($this->_table,"where upper(description)='".strtoupper($schema_name)."' and status not in (-5) ");

            if ($check_fare->num_rows()>0)
            {
                echo $res=json_api(0, 'Nama schema sudah ada');   
            }
            else
            {

                $this->db->trans_begin();

                $this->schema->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'fare/schema_discount/action_add';
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
        $data['id']=$id;
        $data['title'] = 'Edit Schema Diskon';
        $data['detail']=$this->schema->select_data($this->_table,"where id=$id_decode")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        /* validation */
        $this->form_validation->set_rules('name', 'Nama Schema', 'trim|required|callback_special_char', array('special_char' => 'Nama Schemaa mengandung invalid karakter'));
        $this->form_validation->set_rules('slug', 'Slug', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'Slug mengandung invalid karakter'));
        $this->form_validation->set_rules('id', 'id', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid id'));
        
        $this->form_validation->set_message('required','%s harus diisi!')
                                ->set_message('max_length','{field} Maximal  {param} karakter! ');


        $data = null;
        if($this->form_validation->run() == FALSE)
        {
            echo $res=json_api(0,validation_errors());
        }
        else 
        {

            $slug=trim($this->input->post('slug'));
            $schema_name=trim($this->input->post('name'));
            $schema_id=$this->enc->decode($this->input->post('id'));
            
            $data=array(
                        'description'=>$schema_name,
                        'slug'=>$slug,
                        'updated_by'=>$this->session->userdata('username'),
                        'updated_on'=>date("Y-m-d H:i:s"),
                        );

            $check=$this->schema->select_data($this->_table,"where upper(description)='".strtoupper($schema_name)."' and status not in (-5) and id !={$schema_id}");


            if ($check->num_rows()>0)
            {
                echo $res=json_api(0, 'Data sudah ada');      
            }
            else
            {

                $this->db->trans_begin();

                $this->schema->update_data($this->_table,$data,"id=$schema_id");

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
        $logUrl      = site_url().'fare/schema_discount/action_add';
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
        $this->schema->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'fare/schema_discount/action_change';
        $d[1]==1?$logMethod='ENABLED':$logMethod='DISABLED';
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
        $this->schema->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'fare/schema_discount/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function createCode()
    {
        $front_code="DS".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_discount_schema where left(schema_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $kode=$front_code."01";
            return $kode;
        }
        else
        {
            $max=$this->db->query("select max (schema_code) as max_code from app.t_mtr_discount_schema where left(schema_code,".$total_length.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 2);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

}
