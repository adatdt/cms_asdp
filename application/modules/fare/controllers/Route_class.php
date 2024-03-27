<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Route_class extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_routeclass','route');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_rute_class';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'fare/route_class';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->route->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity_app=$this->route->select_data("app.t_mtr_identity_app","")->row();

        if($get_identity_app->port_id==0)
        {
            // filter jika menggunkan port yang ada di user
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->route->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')." ")->result();
            }
            else
            {
                $port=$this->route->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result();
            }
        }
        else
        {
            $port=$this->route->select_data("app.t_mtr_port","where id=".$get_identity_app->port_id." ")->result();
        }        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Tipe Rute',
            'content'  => 'route_class/index',
            'port'  => $port,
            'destination'=>$this->route->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result(),
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $get_identity_app=$this->route->select_data("app.t_mtr_identity_app","")->row();

        // mengambil filter port berdasarkan port user

        if($get_identity_app->port_id==0)
        {
            if(!empty($this->session->userdata("port_id")))
            {
                $port = "and a.origin=".$this->session->userdata("port_id");
            }
            else
            {
                $port = " ";
            }
        }
        else
        {
            $port = "and a.origin=".$get_identity_app->port_id;
        }


        $data['title'] = 'Tambah Tipe Rute';
        $data['route'] = $this->route->get_route($port);
        $data['ship_class'] = $this->route->select_data("app.t_mtr_ship_class"," where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $rute=$this->enc->decode($this->input->post('rute'));
        $ship_class_id=$this->enc->decode($this->input->post('ship_class'));
        $ifcs=$this->enc->decode($this->input->post('ifcs'));

        $_POST['rute'] = $rute;
        $_POST['ship_class'] = $ship_class_id;
        $_POST['ifcs'] = $ifcs;

        $this->form_validation->set_rules('rute', 'Rute Keberangkatan', 'required');
        $this->form_validation->set_rules('ship_class', 'Tipe Rute', 'required');
        $this->form_validation->set_message('required','%s harus diisi!');
        
        $data=array(
                    'rute_id'=>$rute,
                    'ship_class'=>$ship_class_id,
                    'ifcs'=>$ifcs==1?'true':'false',
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // check jika rute sudah ada
        $check_route_class=$this->route->select_data($this->_table," where rute_id={$rute} and ship_class={$ship_class_id} and status not in (-5) ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check_route_class->num_rows()>0)
        {
            echo $res=json_api(0, " Data sudah ada"); 
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
        $logUrl      = site_url().'fare/route_class/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $get_identity_app=$this->route->select_data("app.t_mtr_identity_app","")->row();

        if($get_identity_app->port_id==0)
        {
            // mengambil filter port berdasarkan port user
            if(!empty($this->session->userdata("port_id")))
            {
                $port = "and a.origin=".$this->session->userdata("port_id");
            }
            else
            {
                $port = " ";
            }
        }
        else
        {
            $port = "and a.origin=".$get_identity_app->port_id;
        }

        $id_decode=$this->enc->decode($id);

        $data['title'] = 'Edit Tipe Rute';
        $data['route'] = $this->route->get_route($port);
        $data['ship_class'] = $this->route->select_data("app.t_mtr_ship_class"," where status=1 order by name asc")->result();

        $data['detail'] = $this->route->select_data($this->_table,"where id=$id_decode")->row();


        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $route_id=$this->enc->decode($this->input->post('rute'));
        $ship_class_id=$this->enc->decode($this->input->post('ship_class'));
        $ifcs=$this->enc->decode($this->input->post('ifcs'));
        $id=$this->enc->decode($this->input->post('id'));

        $_POST['rute'] = $route_id;
        $_POST['ship_class'] = $ship_class_id;
        $_POST['ifcs'] = $ifcs;
        $_POST['id'] = $id;

        $this->form_validation->set_rules('rute', 'Rute', 'required');
        $this->form_validation->set_rules('ship_class', 'Tipe Rute', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');
        $this->form_validation->set_message('required','%s harus diisi!');

        $data=array(
                    'rute_id'=>$route_id,
                    'ship_class'=>$ship_class_id,
                    'ifcs'=>$ifcs==1?'true':'false',
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        // check jika rute sudah ada
        $check_route_class=$this->route->select_data($this->_table," where rute_id={$route_id} and ship_class={$ship_class_id} and status not in (-5) and id!=$id ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if ($check_route_class->num_rows()>0)
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
        $logUrl      = site_url().'fare/route_class/action_edit';
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
        $logUrl      = site_url().'fare/route_class/action_change';
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
