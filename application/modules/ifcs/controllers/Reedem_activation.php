<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Reedem_activation extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('M_reedem_activation','activation');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_vehicle_class_reedem_activated';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'ifcs/reedem_activation';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->activation->dataList();
            echo json_encode($rows);
            exit;
        }
        // check identity app
        $data_port=array();
        $identity_app=$this->activation->select_data("app.t_mtr_identity_app"," ")->row();
        $ship_class=$this->activation->select_data("app.t_mtr_ship_class"," where status<>'-5' order by name asc ")->result();
        $vehicle_class=$this->activation->select_data("app.t_mtr_vehicle_class"," where status<>'-5' order by name asc ")->result();

        if($identity_app->port_id==0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $port_id=$this->activation->select_data("app.t_mtr_port"," where status<>'-5' order by name asc ")->result();
                $data_port[""]="Pilih";
            }
            else
            {
                $port_id=$this->activation->select_data("app.t_mtr_port"," where id=".$this->session->userdata("port_id"))->result();
            }
        }
        else
        {
            $port_id=$this->activation->select_data("app.t_mtr_port"," where id=".$identity_app->port_id)->result();
        }

        foreach ($port_id as $key => $value) {
            $data_port[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $data_ship_class[""]="Pilih";
        foreach ($ship_class as $key => $value) {
            $data_ship_class[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        


        $data_vehicle_class[""]="Pilih";
        foreach ($vehicle_class as $key => $value) {
            $data_vehicle_class[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'port'=>$data_port,
            'ship_class'=>$data_ship_class,
            'vehicle_class'=>$data_vehicle_class,
            'title'    => 'Aktivasi Reedem Gol KND',
            'content'  => 'reedem_activation/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // check identity app
        $data_port=array();
        $identity_app=$this->activation->select_data("app.t_mtr_identity_app"," ")->row();
        $ship_class=$this->activation->select_data("app.t_mtr_ship_class"," where status<>'-5' order by name asc ")->result();
        $vehicle_class=$this->activation->select_data("app.t_mtr_vehicle_class"," where status<>'-5' order by name asc ")->result();

        if($identity_app->port_id==0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $port_id=$this->activation->select_data("app.t_mtr_port"," where status<>'-5' order by name asc ")->result();
                $data_port[""]="Pilih";
            }
            else
            {
                $port_id=$this->activation->select_data("app.t_mtr_port"," where id=".$this->session->userdata("port_id"))->result();
            }
        }
        else
        {
            $port_id=$this->activation->select_data("app.t_mtr_port"," where id=".$identity_app->port_id)->result();
        }

        foreach ($port_id as $key => $value) {
            $data_port[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $data_ship_class[""]="Pilih";
        foreach ($ship_class as $key => $value) {
            $data_ship_class[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        


        $data_vehicle_class[""]="Pilih";
        foreach ($vehicle_class as $key => $value) {
            $data_vehicle_class[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        $reedem=array(""=>"Pilih",$this->enc->encode('true')=>"IYA",$this->enc->encode('false')=>"TIDAK");

        $data['title'] = 'Tambah Aktivasi Reedem Gol KND';
        $data['port'] = $data_port;
        $data['ship_class'] = $data_ship_class;
        $data['vehicle_class'] = $data_vehicle_class;
        $data['reedem'] = $reedem;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=$this->enc->decode($this->input->post('port'));
        $vehicle_class=$this->enc->decode($this->input->post('vehicle_class'));
        $ship_class=$this->enc->decode($this->input->post('ship_class'));
        $reedem=$this->enc->decode($this->input->post('reedem'));

        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('vehicle_class', 'Golongan', 'required');
        $this->form_validation->set_rules('ship_class', 'Tipe Kapal', 'required');
        $this->form_validation->set_rules('reedem', 'Reedem', 'required');
        $this->form_validation->set_message('required','%s harus diisi!');

        
        $data=array(
                    'vehicle_class'=>$vehicle_class,
                    'ship_class'=>$ship_class,
                    'is_redeem'=>$reedem,
                    'port_id'=>$port,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        $check=$this->activation->select_data($this->_table, " where vehicle_class={$vehicle_class} and ship_class={$ship_class} and port_id={$port} and status !='-5' ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Data sudah ada");
        }
        else
        {

            $this->db->trans_begin();

            $this->activation->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'ifcs/Reedem_activation/action_add';
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

        $all_data=$this->activation->select_data($this->_table," where id={$id_decode} ")->row();

        // check identity app
        $data_port=array();
        $port_selected="";
        $vehicle_class_selected="";
        $reedem_selected="";
        $ship_class_selected="";

        $identity_app=$this->activation->select_data("app.t_mtr_identity_app"," ")->row();
        $ship_class=$this->activation->select_data("app.t_mtr_ship_class"," where status<>'-5' order by name asc ")->result();
        $vehicle_class=$this->activation->select_data("app.t_mtr_vehicle_class"," where status<>'-5' order by name asc ")->result();

        if($identity_app->port_id==0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $port_id=$this->activation->select_data("app.t_mtr_port"," where status<>'-5' order by name asc ")->result();
                $data_port[""]="Pilih";
            }
            else
            {
                $port_id=$this->activation->select_data("app.t_mtr_port"," where id=".$this->session->userdata("port_id"))->result();
            }
        }
        else
        {
            $port_id=$this->activation->select_data("app.t_mtr_port"," where id=".$identity_app->port_id)->result();
        }

        foreach ($port_id as $key => $value) {

            $encode=$this->enc->encode($value->id);
            if($all_data->port_id==$value->id)
            {
                $port_selected .=$encode;
            }
            $data_port[$encode]=strtoupper($value->name);
        }

        $data_ship_class[""]="Pilih";
        foreach ($ship_class as $key => $value) {
            $encode=$this->enc->encode($value->id);
            if($all_data->ship_class==$value->id)
            {
                $ship_class_selected .=$encode;
            }

            $data_ship_class[$encode]=strtoupper($value->name);
        }        


        $data_vehicle_class[""]="Pilih";
        foreach ($vehicle_class as $key => $value) {

            $encode=$this->enc->encode($value->id);
            if($all_data->vehicle_class==$value->id)
            {
                $vehicle_class_selected .=$encode;
            }
            $data_vehicle_class[$encode]=strtoupper($value->name);
        }        

        $true_selected=$this->enc->encode('true');
        $false_selected=$this->enc->encode('false');
        $reedem=array(""=>"Pilih",$true_selected=>"IYA",$false_selected=>"TIDAK");

        $all_data->is_redeem=='t'?$reedem_selected .=$true_selected:$reedem_selected .=$false_selected;

        $data['title'] = 'Edit Aktivasi Reedem';
        $data['port_selected']=$port_selected;
        $data['vehicle_class_selected']=$vehicle_class_selected;
        $data['reedem_selected']=$reedem_selected;
        $data['ship_class_selected']=$ship_class_selected;
        $data['port'] = $data_port;
        $data['ship_class'] = $data_ship_class;
        $data['vehicle_class'] = $data_vehicle_class;
        $data['reedem'] = $reedem;
        $data['id'] = $id;
        $data['detail']=$this->activation->select_data($this->_table,"where id=$id_decode")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id'));

        $port=$this->enc->decode($this->input->post('port'));
        $vehicle_class=$this->enc->decode($this->input->post('vehicle_class'));
        $ship_class=$this->enc->decode($this->input->post('ship_class'));
        $reedem=$this->enc->decode($this->input->post('reedem'));

        $this->form_validation->set_rules('id', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('vehicle_class', 'Golongan', 'required');
        $this->form_validation->set_rules('ship_class', 'Tipe Kapal', 'required');
        $this->form_validation->set_rules('reedem', 'Reedem', 'required');
        $this->form_validation->set_message('required','%s harus diisi!');

        $data=array(                    
                    'vehicle_class'=>$vehicle_class,
                    'ship_class'=>$ship_class,
                    'is_redeem'=>$reedem,
                    'port_id'=>$port,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        $check=$this->activation->select_data($this->_table, " where vehicle_class={$vehicle_class} and ship_class={$ship_class} and port_id={$port} and status !='-5' and  id !={$id} ");

                // $check=$this->activation->select_data($this->_table, " where vehicle_class={$vehicle_class} ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Data sudah ada");
        }
        else
        {
            // print_r($data); exit;
            $this->db->trans_begin();

            $this->activation->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'ifcs/reedem_activation/action_edit';
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
        $this->activation->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'ifcs/reedem_activation/action_change';
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
        $this->activation->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'ifcs/reedem_activation/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
