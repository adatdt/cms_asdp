<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Route extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_route','route');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_rute';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'fare/route';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->route->dataList();
            echo json_encode($rows);
            exit;
        }

        // filter jika menggunkan port yang ada di user
        if(!empty($this->session->userdata('port_id')))
        {
            $port=$this->route->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')." ")->result();
        }
        else
        {
            $port=$this->route->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result();
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Rute',
            'content'  => 'route/index',
            'port'  => $port,
            'destination'=>$this->route->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result(),
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');


        // mengambil filter port berdasarkan port user
        if(!empty($this->session->userdata("port_id")))
        {
            $data['port'] = $this->route->select_data("app.t_mtr_port","where id=".$this->session->userdata("port_id")." ")->result();
        }
        else
        {
            $data['port'] = $this->route->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        }

        $data['title'] = 'Tambah Rute';
        $data['destination'] = $this->route->select_data("app.t_mtr_port","where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $origin=$this->enc->decode($this->input->post('origin'));
        $destination=$this->enc->decode($this->input->post('destination'));

        $_POST['origin'] = $origin;
        $_POST['destination'] = $destination;

        $this->form_validation->set_rules('origin', 'Keberangakatn', 'required');
        $this->form_validation->set_rules('destination', 'Tujuan', 'required');

        
        $data=array(
                    'origin'=>$origin,
                    'destination'=>$destination,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        $check_route=$this->route->select_data($this->_table,"where origin=$origin and destination=$destination and status=1");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if($check_route->num_rows()>0)
        {
            echo $res=json_api(0, 'Data sudah ada');
        }
        else
        {

            $this->db->trans_begin();

            $this->route->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'fare/route/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

                // mengambil filter port berdasarkan port user
        if(!empty($this->session->userdata("port_id")))
        {
            $data['port'] = $this->route->select_data("app.t_mtr_port","where id=".$this->session->userdata("port_id")." ")->result();
        }
        else
        {
            $data['port'] = $this->route->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        }

        $id_decode=$this->enc->decode($id);

        $data['title'] = 'Edit Rute';
        $data['destination'] = $this->route->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['detail'] = $this->route->select_data("app.t_mtr_rute","where id=$id_decode")->row();


        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $origin=$this->enc->decode($this->input->post('origin'));
        $destination=$this->enc->decode($this->input->post('destination'));
        $id=$this->enc->decode($this->input->post('id'));

        $_POST['origin'] = $origin;
        $_POST['destination'] = $destination;
        $_POST['id'] = $id;

        $this->form_validation->set_rules('destination', 'Tujuan', 'required');
        $this->form_validation->set_rules('origin', 'Keberangakatn', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');

        $data=array(
                    'origin'=>$origin,
                    'destination'=>$destination,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        $check_route=$this->route->select_data($this->_table,"where origin=$origin and destination=$destination and status=1 and id!=$id ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if ($check_route->num_rows()>0)
        {
            echo $res=json_api(0, 'Data sudah ada');      
        }
        else
        {

            $this->db->trans_begin();

            $this->route->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'fare/routee/action_edit';
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
        $this->route->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal '.$d[2]);
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil '.$d[2].' data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'fare/route/action_change';
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
        $this->route->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'fare/route/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


}
