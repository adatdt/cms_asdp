<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Dock extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('Dock_model','dock');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_dock';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pelabuhan/dock';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->dock->dataList();
            echo json_encode($rows);
            exit;
        }

        // port akan tampil sesuai dengan port id di user
        if(!empty($this->session->userdata('port_id')))
        {
            $port=$this->dock->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')." ")->row();
            $dock=$this->dock->select_data("app.t_mtr_dock","where port_id=".$this->session->userdata('port_id')." and status not in (-5) order by name asc ")->result();
        }
        else
        {
            $port=$this->dock->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result();
            $dock="";
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Dermaga',
            'content'  => 'dock/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'port'=>$port,
            'dock'=>$dock,
            'team'=>$this->dock->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        if(!empty($this->session->userdata("port_id")))
        {
            $data['port']=$this->dock->select_data("app.t_mtr_port","where id=".$this->session->userdata("port_id")."")->result();
        }
        else
        {
            $data['port']=$this->dock->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        }

        $data['title'] = 'Tambah Dermaga';
        $data['ship_class']=$this->dock->select_data("app.t_mtr_ship_class","where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $dock=trim($this->input->post('dock'));
        $dock_fee=trim($this->input->post('dock_fee'));
        $tambat_fee=trim($this->input->post('tambat_fee'));
        $port_id=$this->enc->decode($this->input->post('port'));
        $ship_class_id=$this->enc->decode($this->input->post('ship_class'));


        $this->form_validation->set_rules('dock', 'Nama Dermaga', 'required');
        $this->form_validation->set_rules('port', 'Nama Pelabuhan', 'required');
        $this->form_validation->set_rules('dock_fee', 'Tarif', 'required');
        $this->form_validation->set_rules('tambat_fee', 'Tarif tambat', 'required');
        $this->form_validation->set_rules('ship_class', 'Tipe', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $data=array('port_id'=>$port_id,
                    'name'=>$dock,
                    'tambat_fare'=>$tambat_fee,
                    'fare'=>$dock_fee,
                    'ship_class_id'=>$ship_class_id,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date('Y-m-d'),
                    );

        $check_dock=$this->dock->select_data($this->_table," where status=1 and upper(name)=upper('".$dock."') and port_id=".$port_id);

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check_dock->num_rows()>0)
        {
            echo $res=json_api(0, 'Nama Sudah Digunakan');
        }
        else
        {
            $this->db->trans_begin();
            
            $this->dock->insert_data($this->_table,$data);
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
        $logUrl      = site_url().'pelabuhan/dock/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
    

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        if(!empty($this->session->userdata("port_id")))
        {
            $data['port']=$this->dock->select_data("app.t_mtr_port","where id=".$this->session->userdata("port_id")."")->result();
        }
        else
        {
            $data['port']=$this->dock->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        }

        $dock_id=$this->enc->decode($id);
        $data['title'] = 'Edit Dermaga';
        // $data['port']=$this->dock->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['ship_class']=$this->dock->select_data("app.t_mtr_ship_class","where status=1 order by name asc")->result();
        $data['detail']=$this->dock->select_data($this->_table," where id=".$dock_id)->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $dock_id=$this->enc->decode($this->input->post('dock'));
        $port_id=$this->enc->decode($this->input->post('port'));
        $ship_class_id=$this->enc->decode($this->input->post('ship_class'));
        $dock_name=trim($this->input->post('dock_name'));
        $dock_fare=trim($this->input->post('dock_fee'));
        $tambat_fare=trim($this->input->post('tambat_fee'));

        $this->form_validation->set_rules('dock', 'Dermaga', 'required');
        $this->form_validation->set_rules('ship_class', 'Tipe', 'required');
        $this->form_validation->set_rules('dock_name', 'Nama Dermaga', 'required');
        $this->form_validation->set_rules('dock_fee', 'Tarif Dermaga', 'required');
        $this->form_validation->set_rules('tambat_fee', 'Trif Tambat', 'required');
        $this->form_validation->set_rules('port', 'Nama Pelabuhan', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $data=array(
                    'name'=>$dock_name,
                    'port_id'=>$port_id,
                    'fare'=>$dock_fare,
                    'ship_class_id'=>$ship_class_id,
                    'tambat_fare'=>$tambat_fare,
                    'updated_by'=>$this->session->userdata("username"),
                    'updated_on'=>date("Y-m-d"),
                );


        $check_dock=$this->dock->select_data($this->_table," where upper(name)=upper('".$dock_name."') and id!='".$dock_id."' and port_id=".$port_id." and status not in (-5) ");

        // echo print_r($data); exit;

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check_dock->num_rows()>0)
        {
            echo $res=json_api(0, 'Nama sudah ada');   
        }
        else
        {
            $this->db->trans_begin();
            $this->dock->update_data($this->_table,$data,"id=$dock_id");

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
        $logUrl      = site_url().'pelabuhan/dock/action_edit';
        $logMethod   = 'EDIT';
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

        $dock_id = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->dock->update_data($this->_table,$data,"id='".$dock_id."'");

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal hapus data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil hapus data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function enable($param)
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

        $check_enable=$this->dock->select_data($this->_table,"where upper(name)=upper('".$d[2]."') and port_id=".$d[3]." and status=1");

        if($check_enable->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal aktifkan data, Sudah ada nama yang aktif');
        }
        else
        {
            $this->db->trans_begin();
            $this->dock->update_data($this->_table,$data,"id=".$d[0]);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal aktifkan data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil aktifkan data');
            }
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/dock/enable';
        $logMethod   = 'Enable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function disable($param)
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
        $this->dock->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal dinonaktifkan data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil dinonaktifkan data');
        } 

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/dock/disable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function get_dock()
    {
        $port=$this->enc->decode($this->input->post('port'));

        empty($port)?$port_id='NULL':$port_id=$port;
        $dock=$this->dock->select_data($this->_table,"where port_id=".$port_id." and status=1")->result();

        $data=array();
        foreach($dock as $key=>$value)
        {
            $value->id=$this->enc->encode($value->id);
            $data[]=$value;            
        }

         echo json_encode($data);
    }

}
