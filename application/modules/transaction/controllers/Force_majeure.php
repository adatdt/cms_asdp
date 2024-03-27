<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Force_majeure extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_force_majeure','force');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_force_majeure';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/force_majeure';
	}

	public function index(){

        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->force->dataList();
            echo json_encode($rows);
            exit;
        }

        $app_identity=$this->force->select_data("app.t_mtr_identity_app","")->row();

        if($app_identity->port_id==0)
        {
            if(!empty($this->session->userdata("port_id")))
            {
                $port=$this->force->select_data("app.t_mtr_port"," where id=".$this->session->userdata("port_id")." order by name asc ")->result();
                $row_port=1;
            }
            else
            {
                $port=$this->force->select_data("app.t_mtr_port"," where status !='-5' order by name asc ")->result();
                $row_port=0;
            }

        }
        else
        {
            $port=$this->force->select_data("app.t_mtr_port"," where id=".$app_identity->port_id." order by name asc ")->result();
            $row_port=1;            
        }


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Force Majeure',
            'content'  => 'force_majeure/index',
            'port'  => $port,
            'row_port'=>$row_port,
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();


        $app_identity=$this->force->select_data("app.t_mtr_identity_app","")->row();

        if($app_identity->port_id==0)
        {
            if(!empty($this->session->userdata("port_id")))
            {
                $port=$this->force->select_data("app.t_mtr_port"," where id=".$this->session->userdata("port_id")." order by name asc ")->result();
                $row_port=1;
            }
            else
            {
                $port=$this->force->select_data("app.t_mtr_port"," where status =1 order by name asc ")->result();
                $row_port=0;
            }

        }
        else
        {
            $port=$this->force->select_data("app.t_mtr_port"," where id=".$app_identity->port_id." order by name asc ")->result();
            $row_port=1;            
        }


        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Force Majeure';
        // hardcord user group user bank
        $data['port'] = $port;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add_29122020()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port_id=$this->enc->decode($this->input->post('port'));
        $date=$this->input->post('date');
        $force_type=$this->enc->decode($this->input->post('force_type'));
        $remark=trim($this->input->post('remark'));
        $extend_param=trim($this->input->post('extend'));

        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('date', 'Tanggal ', 'required');
        $this->form_validation->set_rules('force_type', 'Tipe', 'required');
        $this->form_validation->set_rules('remark', 'Keterangan ', 'required');
        $this->form_validation->set_rules('extend', 'Waktu Perpanjangan/Jam ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $force_code=$this->createCode($port_id);
        
        $data=array(
                    'date'=>$date,
                    'remark'=>$remark,
                    'extend_param'=>$extend_param,
                    'port_id'=>$port_id,
                    'force_majeure_type'=>$force_type,
                    'force_majeure_code'=>$force_code,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('id'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        $checkin_pass=$this->force->get_checkin_pass_general($port_id)->result();

        $gatein_pass=$this->force->get_gatein_pass_general($port_id)->result();

        $boarding_pass=$this->force->get_boarding_pass_general($port_id)->result();

        $checkin_vehicle=$this->force->get_checkin_vehicle_general($port_id)->result();

        $gatein_vehicle=$this->force->get_gatein_vehicle_general($port_id)->result();

        $boarding_vehicle=$this->force->get_boarding_vehicle_general($port_id)->result();


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else
        {
            $this->db->trans_begin();

            if($force_type==2)
            {
                if (!empty($checkin_pass))
                {

                    foreach ($checkin_pass as $key => $value) {

                        $new_expired_gatein_pass=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($value->gatein_expired)));

                        $data_checkin_pass=array(
                            'ticket_number'=>$value->ticket_number,
                            'status'=>1,
                            'channel'=>$value->channel,
                            'booking_code'=>$value->booking_code,
                            'old_ticket_status'=>$value->status,
                            'old_gatein_expired'=>$value->gatein_expired,
                            'new_gatein_expired'=>$new_expired_gatein_pass,
                            'force_majeure_code'=>$force_code,
                            'created_on'=>date("Y-m-d H:i:s"),
                            'created_by'=>$this->session->userdata("username")
                        );

                        $update_data_checkin_pass=array('gatein_expired'=>$new_expired_gatein_pass,
                                            'updated_on'=>date("Y-m-d H:i:s"),
                                            'updated_by'=>$this->session->userdata("username"),
                        );

                        // update dan insert
                        $this->force->insert_data("app.t_trx_force_majeure_extend_passanger",$data_checkin_pass);
                        $this->force->update_data("app.t_trx_booking_passanger",$update_data_checkin_pass, " ticket_number='{$value->ticket_number}' ");
                    }

                }

                if(!empty($gatein_pass))
                {
                    $data_gatein_pass=array();
                    foreach ($gatein_pass as $key => $value) {
                        $new_expired_boarding_pass=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($value->boarding_expired)));

                        $data_gatein_pass=array(
                            'ticket_number'=>$value->ticket_number,
                            'status'=>1,
                            'channel'=>$value->channel,
                            'booking_code'=>$value->booking_code,
                            'old_ticket_status'=>$value->status,
                            'old_boarding_expired'=>$value->boarding_expired,
                            'new_boarding_expired'=>$new_expired_boarding_pass,
                            'force_majeure_code'=>$force_code,
                            'created_on'=>date("Y-m-d H:i:s"),
                            'created_by'=>$this->session->userdata("username")
                        );

                        $update_data_gatein_pass=array('boarding_expired'=>$new_expired_boarding_pass,
                                            'updated_on'=>date("Y-m-d H:i:s"),
                                            'updated_by'=>$this->session->userdata("username"),
                        );

                        $this->force->insert_data("app.t_trx_force_majeure_extend_passanger",$data_gatein_pass);
                        $this->force->update_data("app.t_trx_booking_passanger",$update_data_gatein_pass, " ticket_number='{$value->ticket_number}' ");
                    }
                }

                if(!empty($boarding_pass))
                {
                    $data_boarding_pass=array();
                    foreach ($boarding_pass as $key => $value) {
                        $new_expired_boarding_pass=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($value->boarding_expired)));
                        $data_boarding_pass=array(
                            'ticket_number'=>$value->ticket_number,
                            'status'=>1,
                            'channel'=>$value->channel,
                            'booking_code'=>$value->booking_code,
                            'old_ticket_status'=>$value->status,
                            'old_boarding_expired'=>$value->boarding_expired,
                            'new_boarding_expired'=>$new_expired_boarding_pass,
                            'force_majeure_code'=>$force_code,
                            'created_on'=>date("Y-m-d H:i:s"),
                            'created_by'=>$this->session->userdata("username")
                        );

                        $update_data_boarding_pass=array('boarding_expired'=>$new_expired_boarding_pass,
                                            'updated_on'=>date("Y-m-d H:i:s"),
                                            'updated_by'=>$this->session->userdata("username"),
                        );

                        $this->force->insert_data("app.t_trx_force_majeure_extend_passanger",$data_boarding_pass);
                        $this->force->update_data("app.t_trx_booking_passanger",$update_data_boarding_pass, " ticket_number='{$value->ticket_number}' ");                        
                    }
                }

                if(!empty($checkin_vehicle))
                {
                    $data_checkin_vehicle=array();
                    foreach ($checkin_vehicle as $key => $value) {

                        $new_expired_gatein_vehicle=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($value->gatein_expired)));

                        $data_checkin_vehicle=array(
                            'ticket_number'=>$value->ticket_number,
                            'status'=>1,
                            'channel'=>$value->channel,
                            'booking_code'=>$value->booking_code,
                            'old_ticket_status'=>$value->status,
                            'old_gatein_expired'=>$value->gatein_expired,
                            'new_gatein_expired'=>$new_expired_gatein_vehicle,
                            'force_majeure_code'=>$force_code,
                            'created_on'=>date("Y-m-d H:i:s"),
                            'created_by'=>$this->session->userdata("username")
                        );

                        $update_data_checkin_vehicle=array('gatein_expired'=>$new_expired_gatein_vehicle,
                                            'updated_on'=>date("Y-m-d H:i:s"),
                                            'updated_by'=>$this->session->userdata("username"),
                        );

                        $this->force->insert_data("app.t_trx_force_majeure_extend_vehicle",$data_checkin_vehicle);
                        $this->force->update_data("app.t_trx_booking_vehicle",$update_data_checkin_vehicle, " ticket_number='{$value->ticket_number}' ");     

                        // mencari data manifest kendaraan
                        $get_checkin_manifest=$this->force->select_data("app.t_trx_booking_passanger"," where booking_code='{$value->booking_code}' and status !='-5' ")->result();
                        
                        foreach ($get_checkin_manifest as $key => $value_manifest) {
                            $data_checkin_manifest=array(
                                'ticket_number'=>$value_manifest->ticket_number,
                                'status'=>1,
                                'channel'=>$value_manifest->channel,
                                'booking_code'=>$value_manifest->booking_code,
                                'old_ticket_status'=>$value_manifest->status,
                                'old_gatein_expired'=>$value_manifest->gatein_expired,
                                'new_gatein_expired'=>$new_expired_gatein_vehicle,
                                'force_majeure_code'=>$force_code,
                                'created_on'=>date("Y-m-d H:i:s"),
                                'created_by'=>$this->session->userdata("username")
                            );

                            $update_data_checkin_manifest=array('gatein_expired'=>$new_expired_gatein_vehicle,
                                                'updated_on'=>date("Y-m-d H:i:s"),
                                                'updated_by'=>$this->session->userdata("username"),
                            );

                            $this->force->insert_data("app.t_trx_force_majeure_extend_passanger",$data_checkin_manifest);
                            $this->force->update_data("app.t_trx_booking_passanger",$update_data_checkin_manifest, " ticket_number='{$value_manifest->ticket_number}' ");       
                        }

                    }

                    // print_r($data_checkin_vehicle);
                }

                if(!empty($gatein_vehicle))
                {
                    $data_gatein_vehicle=array();
                    $tes=array();
                    foreach ($gatein_vehicle as $key => $value) {

                        $new_expired_boarding_vehicle=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($value->boarding_expired)));

                        $data_gatein_vehicle=array(
                            'ticket_number'=>$value->ticket_number,
                            'status'=>1,
                            'channel'=>$value->channel,
                            'booking_code'=>$value->booking_code,
                            'old_ticket_status'=>$value->status,
                            'old_boarding_expired'=>$value->boarding_expired,
                            'new_boarding_expired'=>$new_expired_boarding_vehicle,
                            'force_majeure_code'=>$force_code,
                            'created_on'=>date("Y-m-d H:i:s"),
                            'created_by'=>$this->session->userdata("username")
                        );

                        $update_data_gatein_vehicle=array('boarding_expired'=>$new_expired_boarding_vehicle,
                                            'updated_on'=>date("Y-m-d H:i:s"),
                                            'updated_by'=>$this->session->userdata("username"),
                        );

                        $this->force->insert_data("app.t_trx_force_majeure_extend_vehicle",$data_gatein_vehicle);
                        $this->force->update_data("app.t_trx_booking_vehicle",$update_data_gatein_vehicle, " ticket_number='{$value->ticket_number}' ");     

                        // mencari data manifest kendaraan
                        $get_manifest=$this->force->select_data("app.t_trx_booking_passanger"," where booking_code='{$value->booking_code}' and status !='-5' ")->result();
                        
                        foreach ($get_manifest as $key => $value_manifest) {
                            $data_gatein_manifest=array(
                                'ticket_number'=>$value_manifest->ticket_number,
                                'status'=>1,
                                'channel'=>$value_manifest->channel,
                                'booking_code'=>$value_manifest->booking_code,
                                'old_ticket_status'=>$value_manifest->status,
                                'old_boarding_expired'=>$value_manifest->boarding_expired,
                                'new_boarding_expired'=>$new_expired_boarding_vehicle,
                                'force_majeure_code'=>$force_code,
                                'created_on'=>date("Y-m-d H:i:s"),
                                'created_by'=>$this->session->userdata("username")
                            );

                            $update_data_gatein_manifest=array(
                                                'boarding_expired'=>$new_expired_boarding_vehicle,
                                                'updated_on'=>date("Y-m-d H:i:s"),
                                                'updated_by'=>$this->session->userdata("username"),
                            );

                            $this->force->insert_data("app.t_trx_force_majeure_extend_passanger",$data_gatein_manifest);
                            $this->force->update_data("app.t_trx_booking_passanger",$update_data_gatein_manifest, " ticket_number='{$value_manifest->ticket_number}' ");     
                        }

                    }

                }

                if(!empty($boarding_vehicle))
                {
                    $data_boarding_vehicle=array();
                    foreach ($boarding_vehicle as $key => $value) {

                        $new_expired_boarding_vehicle=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($value->boarding_expired)));

                        $data_boarding_vehicle=array(
                            'ticket_number'=>$value->ticket_number,
                            'status'=>1,
                            'channel'=>$value->channel,
                            'booking_code'=>$value->booking_code,
                            'old_ticket_status'=>$value->status,
                            'old_boarding_expired'=>$value->boarding_expired,
                            'new_boarding_expired'=>$new_expired_boarding_vehicle,
                            'force_majeure_code'=>$force_code,
                            'created_on'=>date("Y-m-d H:i:s"),
                            'created_by'=>$this->session->userdata("username")
                        );

                        $update_data_boarding_vehicle=array('boarding_expired'=>$new_expired_boarding_vehicle,
                                            'updated_on'=>date("Y-m-d H:i:s"),
                                            'updated_by'=>$this->session->userdata("username"),
                        );

                        $this->force->insert_data("app.t_trx_force_majeure_extend_vehicle",$data_boarding_vehicle);
                        $this->force->update_data("app.t_trx_booking_vehicle",$update_data_boarding_vehicle," ticket_number='{$value->ticket_number}' "); 

                        $get_manifest_boarding=$this->force->select_data("app.t_trx_booking_passanger"," where booking_code='{$value->booking_code}' and status !='-5' ")->result();

                        foreach ($get_manifest_boarding as $key => $value_manifest) {
                            $data_boarding_manifest=array(
                                'ticket_number'=>$value_manifest->ticket_number,
                                'status'=>1,
                                'channel'=>$value_manifest->channel,
                                'booking_code'=>$value_manifest->booking_code,
                                'old_ticket_status'=>$value_manifest->status,
                                'old_boarding_expired'=>$value_manifest->gatein_expired,
                                'new_boarding_expired'=>$new_expired_boarding_vehicle,
                                'force_majeure_code'=>$force_code,
                                'created_on'=>date("Y-m-d H:i:s"),
                                'created_by'=>$this->session->userdata("username")
                            );

                            $update_data_boarding_manifest=array('gatein_expired'=>$new_expired_boarding_vehicle,
                                                'updated_on'=>date("Y-m-d H:i:s"),
                                                'updated_by'=>$this->session->userdata("username"),
                            );

                            $this->force->insert_data("app.t_trx_force_majeure_extend_passanger",$data_boarding_manifest);
                            $this->force->update_data("app.t_trx_booking_passanger",$update_data_boarding_manifest," ticket_number='{$value_manifest->ticket_number}' "); 
                        }    
                    }

                }

            }

            $this->force->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/force_majeure/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port_id=$this->enc->decode($this->input->post('port'));
        $date=$this->input->post('date');
        $force_type=$this->enc->decode($this->input->post('force_type'));
        $remark=trim($this->input->post('remark'));
        $extend_param=trim($this->input->post('extend'));

        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('date', 'Tanggal ', 'required');
        $this->form_validation->set_rules('force_type', 'Tipe', 'required');
        $this->form_validation->set_rules('remark', 'Keterangan ', 'required');
        $this->form_validation->set_rules('extend', 'Waktu Perpanjangan/Jam ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $force_code=$this->createCode($port_id);
        
        $data=array(
                    'date'=>$date,
                    'remark'=>$remark,
                    'extend_param'=>$extend_param,
                    'port_id'=>$port_id,
                    'force_majeure_type'=>$force_type,
                    'force_majeure_code'=>$force_code,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('id'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else
        {
            $this->db->trans_begin();

            $this->force->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/force_majeure/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function add_ticket_eks(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Ticket Eksekutif Force Majeure';
        // hardcord user group user bank
        // $data['port'] = $this->force->select_data("app.t_mtr_port"," where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/add_ticket',$data);
    }


    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);

        $data['title'] = 'Edit User Bank';
        $data['id'] = $id;
        $data['detail']=$this->user->select_data($this->_table,"where id=$id_decode")->row();
        // hardcord user group user bank
        $data['username'] = $this->user->select_data("core.t_mtr_user"," where user_group_id=26 and status=1 order by username asc")->result();
        $data['bank'] = $this->user->select_data("core.t_mtr_bank"," where status=1 order by bank_name asc")->result();

        $this->load->view($this->_module.'/edit',$data);   
    }


    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $bank_abbr=$this->enc->decode($this->input->post('bank'));
        // $user_id=$this->enc->decode($this->input->post('username'));
        $id=$this->enc->decode($this->input->post('id'));

        $this->form_validation->set_rules('bank', 'Nama Bank', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');


        $data=array(
                    
                    'bank_abbr'=>$bank_abbr,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {

            $this->db->trans_begin();

            $this->user->update_data($this->_table,$data,"id=$id");

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
        $logUrl      = site_url().'master_data/user_bank/action_edit';
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
            'updated_by'=>$this->session->userdata('id'),
        );


        $this->db->trans_begin();
        $this->force->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'transaction/force_majeure/action_change';
        $d[1]==1?$logMethod='DISABLE':$logMethod='ENABLE';
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
        $this->user->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'master_data/user_bank/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function list_manifest($enc_code)
    {
        // check hak akses
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $check_force=$this->force->select_data($this->_table," where force_majeure_code='".$this->enc->decode("$enc_code")."' ");

        if($check_force->num_rows() <1 )
        {
            redirect('error_404');
            exit;
        }

        $get_data_force=$check_force->row();

        $force_type=$this->force->select_data("app.t_mtr_force_majeure_type"," where id='".$get_data_force->force_majeure_type."' ")->row();

        $code=$this->enc->decode("$enc_code");
        $get_force=$check_force->row();

        $implode=implode("_",array($code,$get_force->extend_param));
        

        $data['title'] = 'Daftar Manifest Force Majeure';
        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title']    = 'Detail Daftar Manifest Force Majeure Eksekutif';
        $data['url_parent']=site_url('transaction/force_majeure');
        $data['parent']='Force Majeure';
        $data['extend_param']=$get_data_force->extend_param;
        $data['force_majeure_date']=empty($get_data_force->date)?"":format_date($get_data_force->date);
        $data['force_type']=$force_type->label;
        $data['content']  = 'force_majeure/list_manifest';
        $data['force_code']=$this->enc->decode($enc_code);
        $data['port'] = $this->force->select_data("app.t_mtr_port"," where status=1 order by name asc")->result();
        $data['btn_add']  = $get_data_force->status==1?generate_button_new($this->_module, 'add',  site_url($this->_module.'/add_force_majeure_eks/'.$implode)):"";

        $this->load->view('default',$data);
    }

    public function list_manifest_general($enc_code)
    {
        // check hak akses
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $check_force=$this->force->select_data($this->_table," where force_majeure_code='".$this->enc->decode("$enc_code")."' ");

        $get_data_force=$check_force->row();

        if($check_force->num_rows() <1 )
        {
            redirect('error_404');
            exit;
        }


        $code=$this->enc->decode("$enc_code");
        $get_force=$check_force->row();

        $implode=implode("_",array($code,$get_force->extend_param));

        $force=site_url($this->_module."/action_add_force_majeure_reguler/".$this->enc->decode("$enc_code")."_".$get_data_force->extend_param);
        $btn_add=generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-warning" onclick="confirmationAction(\'Apakah Anda yakin akan force majeure general ?\', \''.$force.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> Tambah</button> ');

        $get_data_force=$check_force->row();

        $force_type=$this->force->select_data("app.t_mtr_force_majeure_type"," where id='".$get_data_force->force_majeure_type."' ")->row();

        $data['title'] = 'Daftar Manifest Force Majeure Reguler';
        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['url_parent']=site_url('transaction/force_majeure');
        $data['parent']='Force Majeure';
        $data['content']  = 'force_majeure/list_manifest_general';
        $data['extend_param']=$get_data_force->extend_param;
        $data['force_majeure_date']=empty($get_data_force->date)?"":format_date($get_data_force->date);
        $data['force_type']=$force_type->label;
        $data['force_code']=$this->enc->decode($enc_code);
        $data['port'] = $this->force->select_data("app.t_mtr_port"," where status=1 order by name asc")->result();
        $data['btn_add']  = $btn_add;

        $this->load->view('default',$data);
    }

    public function manifest_passanger(){   
        checkUrlAccess($this->_module,'detail');
        if($this->input->is_ajax_request()){
            $rows = $this->force->dataManifestPassanger();
            echo json_encode($rows);
            exit;
        }

        $port=$this->force->select_data("app.t_mtr_port"," where status !='-5' order by name asc ")->result();

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Force Majeure Eksekutif',
            'content'  => 'Force_majeure/index',
            'port'  => $port,
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
        );

        $this->load->view('default', $data);
    }

    public function manifest_vehicle(){   
        checkUrlAccess($this->_module,'detail');
        $rows = $this->force->dataManifestVehicle();
        echo json_encode($rows);

    }

    public function add_force_majeure_eks($code){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $explode=explode("_",$code);

        $data['title'] = 'Tambah Tiket Force Majeure Eksekutif';
        $data['force_code']=$explode[0];
        $data['extend_param']=$explode[1];

        $this->load->view($this->_module.'/add_force_majeure_eks',$data);
    }

    public function action_add_force_majeure_eks()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $td_ticket=$this->input->post('td_ticket[]');
        $cek_service=$this->input->post('service');


        
        if(count($td_ticket)==0)
        {
            // validasi jika data diinput tanpa ditampung dahulu   
            if(strtoupper($cek_service)=="PENUMPANG")
            {
                echo $this->action_add_force_majeure_passanger();
            }
            else
            {
                echo $this->action_add_force_majeure_vehicle();
            }

        }
        else
        {

            $re_array=array();
            $re_ticket_vehicle=array();
            $re_ticket_passanger=array();
            foreach($td_ticket as $key=>$value)
            {
                // memisahkan tiket penumpang dan kendaraan
                $ticket_vehicle=$this->force->select_data("app.t_trx_booking_vehicle", " where ticket_number='".$value."' ")->row();

                if(count($ticket_vehicle)>0)
                {
                    $re_ticket_vehicle[]=$ticket_vehicle->ticket_number;
                }
                else
                {
                    $re_ticket_passanger[]=$value;
                }

            }

            $this->action_add_force_majeure_tampung($re_ticket_vehicle,$re_ticket_passanger);

        }

    }

    public function action_add_force_majeure_passanger()
    {

        $ticket_number=trim($this->input->post("ticket_number"));
        $service=trim($this->input->post("service"));
        $force_code=trim($this->input->post("fcode"));
        $extend_param=trim($this->input->post("extend_param"));
        $booking_code=trim($this->input->post("booking_code"));

        $this->form_validation->set_rules('ticket_number', 'nomer_ticket', 'required');
        $this->form_validation->set_rules('service', 'Servis ', 'required');
        $this->form_validation->set_rules('fcode', 'Kode', 'required');
        $this->form_validation->set_rules('extend_param', 'Keterangan ', 'required');
        $this->form_validation->set_rules('booking_code', 'Kode Booking ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/force_majeure/action_add_force_majeure_passanger';
        $logMethod   = 'ADD';


        // checking ticket_number status
        $check_ticket=$this->force->select_data("app.t_trx_booking_passanger"," where ticket_number='{$ticket_number}'");
        $check_expired=$check_ticket->row();

        $now=date("Y-m-d H:i:s");

        // print_r($check_expired);
        // $res=json_api(0,"ini error");
        // return $res;
        // exit;

        $app_identity=$this->force->select_data("app.t_mtr_identity_app","")->row();
        $get_port_name=$this->force->select_data("app.t_mtr_port"," where id=".$check_ticket->origin)->row();


        // check apakah  ini di aplikasi selain cloude
        if($app_identity->port_id !=0)
        {
            if($app_identity->port_id != $check_ticket->origin )
            {
                $res=json_api(0,"Tiket Tidak ditemukan Di Pelabuhan ".strtoupper($get_port_name->name));
                return $res;

                $logParam    = json_encode(array("data"=>"Tiket  Pelabuhan ".strtoupper($get_port_name->name)." tidak bisa di input "));
                $logResponse = $res;
                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                exit;
            }
        }
        else
        {
            if(!empty($this->session->userdata("port_id")))
            {
                if($this->session->userdata("port_id") != $check_ticket->origin )
                {
                    $res=json_api(0,"Tiket Tidak ditemukan Di Pelabuhan ".strtoupper($get_port_name->name));
                    return $res;

                    $logParam    = json_encode(array("data"=>"Tiket  Pelabuhan ".strtoupper($get_port_name->name)." tidak bisa di input "));
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }

        }


        if($this->form_validation->run()===false)
        {
            $res=json_api(0,validation_errors());
            return $res;

            $logParam    = json_encode(array("data"=>"data kosong"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;
        }
        else if($check_ticket->num_rows()<1)
        {
            $res=json_api(0,"Tiket Tidak ditemukan");
            return $res;

            $logParam    = json_encode(array("data"=>"Tiket Tidak ditemukan"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;
        }
        else
        {
            // pengechekan jika tiket sudah expired
            if($check_expired->status==3)
            {
                $expired=date('Y-m-d H:i:s',strtotime($check_expired->gatein_expired));
                if(strtotime($expired)<$now)
                {
                    $res=json_api(0,"Tiket Sudah Gatein Expired");
                    $logParam    = json_encode(array("data"=>"gatein_expired"));
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }
            else if($check_expired->status==4 )
            {
                if(date('Y-m-d H:i:s',strtotime($check_expired->boarding_expired))<=$now)
                {
                    $res=json_api(0,"Tiket Sudah Boarding Expired");
                    return $res;

                    $logParam    = json_encode(array("data"=>"Boarding Expire"));
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }
            else if($check_expired->status==5 || $check_expired->status==7 )
            {
                if(date('Y-m-d H:i:s',strtotime($check_expired->boarding_expired))<=$now)
                {
                    $res=json_api(0,"Tiket Sudah Boarding Expired");

                    return $res;

                    $logParam    = json_encode(array("data"=>"Tiket Boarding Expire"));
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }

                // checking apakah sudah berlayar atau belum
                $get_sail=$this->force->get_sail_date($ticket_number);

                if(!empty($get_sail->sail_date))
                {
                    $res=json_api(0,"Tiket Sudah Berlayar");

                    return $res;

                    $logParam    = json_encode(array("data"=>"Tiket Sudah Berlayar"));
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }

            $this->db->trans_begin();

            if($check_expired->status==3)
            {
                $new_expired=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($check_expired->gatein_expired)));
                $data=array(
                    'ticket_number'=>$ticket_number,
                    'status'=>1,
                    'channel'=>$check_expired->channel,
                    'booking_code'=>$check_expired->booking_code,
                    'old_ticket_status'=>$check_expired->status,
                    'old_gatein_expired'=>$check_expired->gatein_expired,
                    'new_gatein_expired'=>$new_expired,
                    'force_majeure_code'=>$force_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username")
                );

                $update_data=array('gatein_expired'=>$new_expired,
                                    'updated_on'=>date("Y-m-d H:i:s"),
                                    'updated_by'=>$this->session->userdata("username"),
                                    );

                // update gatein expired diboarding passanger
                $this->force->update_data("app.t_trx_booking_passanger",$update_data," ticket_number='{$ticket_number}' ");
            }
            else
            {
                $new_expired=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($check_expired->boarding_expired)));
                $data=array(
                    'ticket_number'=>$ticket_number,
                    'status'=>1,
                    'channel'=>$check_expired->channel,
                    'booking_code'=>$check_expired->booking_code,
                    'old_ticket_status'=>$check_expired->status,
                    'old_boarding_expired'=>$check_expired->boarding_expired,
                    'new_boarding_expired'=>$new_expired,
                    'force_majeure_code'=>$force_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username")
                );

                $update_data=array('boarding_expired'=>$new_expired,
                                    'updated_on'=>date("Y-m-d H:i:s"),
                                    'updated_by'=>$this->session->userdata("username"),
                                    );

                // update gatein expired diboarding passanger
                $this->force->update_data("app.t_trx_booking_passanger",$update_data," ticket_number='{$ticket_number}' ");
            }

            $this->force->insert_data("app.t_trx_force_majeure_extend_passanger",$data);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                $res=json_api(0, 'Gagal tambah data');
                return $res;
            }
            else
            {
                $this->db->trans_commit();
                $res=json_api(1, 'Berhasil tambah data');
                return $res;
            }

            $logParam    = json_encode(array($data,$update_data));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;
        }

    }

    public function action_add_force_majeure_vehicle()
    {

        $ticket_number=trim($this->input->post("ticket_number"));
        $service=trim($this->input->post("service"));
        $force_code=trim($this->input->post("fcode"));
        $extend_param=trim($this->input->post("extend_param"));
        $booking_code=trim($this->input->post("booking_code"));

        $this->form_validation->set_rules('ticket_number', 'nomer_ticket', 'required');
        $this->form_validation->set_rules('service', 'Servis ', 'required');
        $this->form_validation->set_rules('fcode', 'Kode', 'required');
        $this->form_validation->set_rules('extend_param', 'Keterangan ', 'required');
        $this->form_validation->set_rules('booking_code', 'Kode Booking ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/force_majeure/action_add_force_majeure_vehicle';
        $logMethod   = 'ADD';


        // checking ticket_number status
        $check_ticket=$this->force->select_data("app.t_trx_booking_vehicle"," where ticket_number='{$ticket_number}'");
        $check_expired=$check_ticket->row();

        $now=date("Y-m-d H:i:s");

        // print_r($check_expired);
        // $res=json_api(0,"ini error");
        // return $res;
        // exit;
        $app_identity=$this->force->select_data("app.t_mtr_identity_app","")->row();
        $get_port_name=$this->force->select_data("app.t_mtr_port"," where id=".$check_ticket->origin)->row();


        // check apakah  ini di aplikasi selain cloude
        if($app_identity->port_id !=0)
        {
            if($app_identity->port_id != $check_ticket->origin )
            {
                $res=json_api(0,"Tiket Tidak ditemukan Di Pelabuhan ".strtoupper($get_port_name->name));
                return $res;

                $logParam    = json_encode(array("data"=>"Tiket  Pelabuhan ".strtoupper($get_port_name->name)." tidak bisa di input "));
                $logResponse = $res;
                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                exit;
            }
        }
        else
        {
            if(!empty($this->session->userdata("port_id")))
            {
                if($this->session->userdata("port_id") != $check_ticket->origin )
                {
                    $res=json_api(0,"Tiket Tidak ditemukan Di Pelabuhan ".strtoupper($get_port_name->name));
                    return $res;

                    $logParam    = json_encode(array("data"=>"Tiket  Pelabuhan ".strtoupper($get_port_name->name)." tidak bisa di input "));
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }

        }



        if($this->form_validation->run()===false)
        {
            $res=json_api(0,validation_errors());
            return $res;

            $logParam    = json_encode(array("data"=>"data kosong"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;
        }
        else if($check_ticket->num_rows()<1)
        {
            $res=json_api(0,"Tiket Tidak ditemukan");
            return $res;

            $logParam    = json_encode(array("data"=>"Tiket Tidak ditemukan"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;
        }
        else
        {
            // pengechekan jika tiket sudah expired
            if($check_expired->status==3)
            {
                $expired=date('Y-m-d H:i:s',strtotime($check_expired->gatein_expired));
                if(strtotime($expired)<$now)
                {
                    $res=json_api(0,"Tiket Sudah Gatein Expired");
                    $logParam    = json_encode(array("data"=>"gatein_expired"));
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }
            else if($check_expired->status==5 || $check_expired->status==7 || $check_expired->status==4)
            {
                if(date('Y-m-d H:i:s',strtotime($check_expired->boarding_expired))<=$now)
                {
                    $res=json_api(0,"Tiket Sudah Boarding Expired");

                    return $res;

                    $logParam    = json_encode(array("data"=>"Tiket Boarding Expire"));
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }

                // checking apakah sudah berlayar atau belum
                $get_sail=$this->force->get_sail_date($ticket_number);

                if(!empty($get_sail->sail_date))
                {
                    $res=json_api(0,"Tiket Sudah Berlayar");

                    return $res;

                    $logParam    = json_encode(array("data"=>"Tiket Sudah Berlayar"));
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }
            else
            {
                $res=json_api(0,"Tiket Tidak Memenuhi Syarat");
                $logParam    = json_encode(array("data"=>"tidak memenuhi syarat"));
                $logResponse = $res;
                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                exit;
            }

            $this->db->trans_begin();

            // mengambil row data pada penumpang kendaraan berdasarkan booking_code
            $search_data=$this->force->select_data("app.t_trx_booking_passanger"," where booking_code='{$check_expired->booking_code}' and status !='-5' ")->result();
            $data_passanger=array();

            if($check_expired->status==3)
            {
                $new_expired=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($check_expired->gatein_expired)));

                $data=array(
                    'ticket_number'=>$ticket_number,
                    'status'=>1,
                    'channel'=>$check_expired->channel,
                    'booking_code'=>$check_expired->booking_code,
                    'old_ticket_status'=>$check_expired->status,
                    'old_gatein_expired'=>$check_expired->gatein_expired,
                    'new_gatein_expired'=>$new_expired,
                    'force_majeure_code'=>$force_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username")
                );

                
                foreach ($search_data as $key => $value) {

                    $data_passanger[]=array(
                        'ticket_number'=>$value->ticket_number,
                        'status'=>1,
                        'channel'=>$check_expired->channel,
                        'booking_code'=>$check_expired->booking_code,
                        'old_ticket_status'=>$check_expired->status,
                        'old_gatein_expired'=>$check_expired->gatein_expired,
                        'new_gatein_expired'=>$new_expired,
                        'force_majeure_code'=>$force_code,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata("username")
                    );
                }


                $update_data=array('gatein_expired'=>$new_expired,
                                    'updated_on'=>date("Y-m-d H:i:s"),
                                    'updated_by'=>$this->session->userdata("username"),
                                    );

                // update gatein expired diboarding vehicle
                $this->force->update_data("app.t_trx_booking_vehicle",$update_data," ticket_number='{$ticket_number}' ");

                // update ticket di boarding passanger, tiket penumpang kendaraan
                $this->force->update_data("app.t_trx_booking_passanger",$update_data," booking_code='{$check_expired->booking_code}' and status !='-5' ");
            }
            else
            {
                $new_expired=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($check_expired->boarding_expired)));
                $data=array(
                    'ticket_number'=>$ticket_number,
                    'channel'=>$check_expired->channel,
                    'status'=>1,
                    'booking_code'=>$check_expired->booking_code,
                    'old_ticket_status'=>$check_expired->status,
                    'old_boarding_expired'=>$check_expired->boarding_expired,
                    'new_boarding_expired'=>$new_expired,
                    'force_majeure_code'=>$force_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username")
                );

                foreach ($search_data as $key => $value) {

                    $data_passanger[]=array(
                        'ticket_number'=>$value->ticket_number,
                        'channel'=>$check_expired->channel,
                        'status'=>1,
                        'booking_code'=>$check_expired->booking_code,
                        'old_ticket_status'=>$check_expired->status,
                        'old_boarding_expired'=>$check_expired->boarding_expired,
                        'new_boarding_expired'=>$new_expired,
                        'force_majeure_code'=>$force_code,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata("username")
                    );
                }

                $update_data=array('boarding_expired'=>$new_expired,
                                    'updated_on'=>date("Y-m-d H:i:s"),
                                    'updated_by'=>$this->session->userdata("username"),
                                    );

                // update boarding expired diboarding vehicle
                $this->force->update_data("app.t_trx_booking_vehicle",$update_data," ticket_number='{$ticket_number}' ");

                // update ticket di boarding passanger, tiket penumpang kendaraan
                $this->force->update_data("app.t_trx_booking_passanger",$update_data," booking_code='{$check_expired->booking_code}' and status !='-5' ");
            }

            $this->force->insert_data("app.t_trx_force_majeure_extend_vehicle",$data);
            $this->force->insert_data_batch("app.t_trx_force_majeure_extend_passanger",$data_passanger);

            // print_r($data_passanger); exit;


            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                $res=json_api(0, 'Gagal tambah data');
                return $res;
            }
            else
            {
                $this->db->trans_commit();
                $res=json_api(1, 'Berhasil tambah data');
                return $res;
            }

            $logParam    = json_encode(array($data,$update_data));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;
        }

    }

    public function action_add_force_majeure_tampung($re_ticket_vehicle,$re_ticket_passanger)
    {
        $force_code=trim($this->input->post("fcode"));
        $extend_param=trim($this->input->post("extend_param"));

        $this->form_validation->set_rules('fcode', 'Kode', 'required');
        $this->form_validation->set_rules('extend_param', 'Keterangan ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $err_gatein_exp_pass=array();
        $err_gatein_exp_vehicle=array();
        $err_sail_vehicle=array();
        $err_boarding_exp_pass=array();
        $err_sail_pass=array();
        $err_boarding_exp_vehicle=array();
        $err_status=array();

        $now=date("Y-m-d H:i:s");

        if(array_sum($re_ticket_vehicle)>0)
        {

            foreach ($re_ticket_vehicle as $key => $value) {

                // checking ticket_number status
                $check_ticket=$this->force->select_data("app.t_trx_booking_vehicle"," where ticket_number='{$value}'");
                $check_expired=$check_ticket->row();

                // pengechekan jika tiket sudah expired
                if($check_expired->status==3)
                {
                    $expired=date('Y-m-d H:i:s',strtotime($check_expired->gatein_expired));
                    if(strtotime($expired)<$now)
                    {
                        // tampung error jika sudah gate in expired
                        $err_gatein_exp_vehicle[]=1;
                    }
                }
                // jika statusnya sudah gatein
                else if($check_expired->status==4 || $check_expired->status==7)
                {
                    if(date('Y-m-d H:i:s',strtotime($check_expired->boarding_expired))<=$now)
                    {
                        // tampung error boarding expire
                        $err_boarding_exp_vehicle[]=1;
                    }
                }
                else if($check_expired->status==5 || $check_expired->status==6)
                {
                    if(date('Y-m-d H:i:s',strtotime($check_expired->boarding_expired))<=$now)
                    {
                        // tampung error boarding expire
                        $err_boarding_exp_vehicle[]=1;
                    }

                    // checking apakah sudah berlayar atau belum
                    $get_sail=$this->force->get_sail_date_vehicle($value);

                    if(!empty($get_sail->sail_date))
                    {
                        // tampung error jika sudah boarding dan sudah sudah berangkat
                        $err_sail_vehicle[]=1;
                    }
                }
                else
                {
                    // tampung error jika status ticket tidak memenuhi syarat
                    $err_status[]=1;
                }
                
            }
        }


        if(array_sum($re_ticket_passanger)>0)
        {

            foreach ($re_ticket_passanger as $key => $value) {

                // checking ticket_number status
                $check_ticket=$this->force->select_data("app.t_trx_booking_passanger"," where ticket_number='{$value}'");
                $check_expired=$check_ticket->row();

                // pengechekan jika tiket sudah expired
                if($check_expired->status==3)
                {
                    $expired=date('Y-m-d H:i:s',strtotime($check_expired->gatein_expired));
                    if(strtotime($expired)<$now)
                    {
                        // tampung error jika sudah gate in expired
                        $err_gatein_exp_passanger[]=1;
                    }
                }
                else if( $check_expired->status==7 || $check_expired->status==4)
                {
                    if(date('Y-m-d H:i:s',strtotime($check_expired->boarding_expired))<=$now)
                    {
                        // tampung error boarding expire
                        $err_boarding_exp_passanger[]=1;
                    }

                }
                else if($check_expired->status==5 || $check_expired->status==6 )
                {
                    if(date('Y-m-d H:i:s',strtotime($check_expired->boarding_expired))<=$now)
                    {
                        // tampung error boarding expire
                        $err_boarding_exp_passanger[]=1;
                    }

                    // checking apakah sudah berlayar atau belum
                    $get_sail=$this->force->get_sail_date($value);

                    if(!empty($get_sail->sail_date))
                    {
                        // tampung error jika sudah boarding dan sudah sudah berangkat
                        $err_sail_passanger[]=1;
                    }
                }
                else
                {
                    // tampung error jika status ticket tidak memenuhi syarat
                    $err_status[]=1;
                }
                
            }
        }


        //checking before insert

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/force_majeure/action_add_force_majeure_vehicle';
        $logMethod   = 'ADD';

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());

            $logParam    = json_encode(array("data"=>"Data Kosong"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;              
        }
        else if(array_sum($err_gatein_exp_pass)>0)
        {
            echo $res=json_api(0,"Gatein expired");

            $logParam    = json_encode(array("data"=>"Gatein expired pass"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;   
        }
        else if (array_sum($err_boarding_exp_pass)>0)
        {
            echo $res=json_api(0,"boarding expired");
            $logParam    = json_encode(array("data"=>"Boarding expired pass"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;     
        }
        else if (array_sum($err_sail_pass)>0)
        {
            echo $res=json_api(0,"Kapal sudah berlayar");   

            $logParam    = json_encode(array("data"=>"Kapar berlayar pass"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;  
        }

        else if(array_sum($err_gatein_exp_vehicle)>0)
        {
            echo $res=json_api(0,"Gatein expired");

            $logParam    = json_encode(array("data"=>"Gatein expired vehicle"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;   
        }
        else if (array_sum($err_boarding_exp_vehicle)>0)
        {
            echo $res=json_api(0,"boarding expired"); 

            $logParam    = json_encode(array("data"=>"Boarding expired vehicle"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;    
        }
        else if (array_sum($err_sail_vehicle)>0)
        {
            echo $res=json_api(0,"Kapal sudah berlayar"); 

            $logParam    = json_encode(array("data"=>"Kapal berlayar vehicle"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;    
        }
        else
        {
            $this->db->trans_begin();

            $data=array();
            $update_data=array();
            // jika yang ditampung ada tiket penumpang
            if(array_sum($re_ticket_passanger)>0)
            {


                foreach ($re_ticket_passanger as $key => $value) {
                    // checking ticket_number status
                    $check_ticket=$this->force->select_data("app.t_trx_booking_passanger"," where ticket_number='{$value}'");
                    $check_expired=$check_ticket->row();

                    // pengechekan jika tiket status 3 /checkin
                    if($check_expired->status==3)
                    {
                        //expired baru
                        $new_expired=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($check_expired->gatein_expired)));
                        $data_passanger=array(
                            'ticket_number'=>$value,
                            'status'=>1,
                            'channel'=>$check_expired->channel,
                            'booking_code'=>$check_expired->booking_code,
                            'old_ticket_status'=>$check_expired->status,
                            'old_gatein_expired'=>$check_expired->gatein_expired,
                            'new_gatein_expired'=>$new_expired,
                            'force_majeure_code'=>$force_code,
                            'created_on'=>date("Y-m-d H:i:s"),
                            'created_by'=>$this->session->userdata("username")
                        );

                        $update_data=array('gatein_expired'=>$new_expired,
                            'updated_on'=>date("Y-m-d H:i:s"),
                            'updated_by'=>$this->session->userdata("username"),
                        );

                        // update ticket di gate passanger
                        $this->force->update_data("app.t_trx_booking_passanger",$update_data," ticket_number='{$check_expired->booking_code}' ");

                        // insert table extend force majeure
                        $this->force->insert_data("app.t_trx_force_majeure_extend_passanger",$data_passanger);
                    }
                    else
                    {
                            //expired baru
                        $new_expired=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($check_expired->boarding_expired)));
                        $data_passanger=array(
                            'ticket_number'=>$value,
                            'status'=>1,
                            'channel'=>$check_expired->channel,
                            'booking_code'=>$check_expired->booking_code,
                            'old_ticket_status'=>$check_expired->status,
                            'old_boarding_expired'=>$check_expired->boarding_expired,
                            'new_boarding_expired'=>$new_expired,
                            'force_majeure_code'=>$force_code,
                            'created_on'=>date("Y-m-d H:i:s"),
                            'created_by'=>$this->session->userdata("username")
                        );

                        $update_data=array('boarding_expired'=>$new_expired,
                            'updated_on'=>date("Y-m-d H:i:s"),
                            'updated_by'=>$this->session->userdata("username"),
                        );

                        // update ticket di gate passanger
                        $this->force->update_data("app.t_trx_booking_passanger",$update_data," ticket_number='{$check_expired->booking_code}' ");

                        // insert table extend force majeure
                        $this->force->insert_data("app.t_trx_force_majeure_extend_passanger",$data_passanger);
                    }
                }
            }

            // print_r($data_passanger); exit;

            if(array_sum($re_ticket_vehicle)>0)
            {

                foreach ($re_ticket_vehicle as $key => $value) {
                    // checking ticket_number status
                    $check_ticket_vehicle=$this->force->select_data("app.t_trx_booking_vehicle"," where ticket_number='{$value}'");
                    $check_expired_vehicle=$check_ticket_vehicle->row();


                    // print_r($check_expired_vehicle); exit;

                    // pengechekan jika tiket status 3 /checkin
                    if($check_expired_vehicle->status==3)
                    {
                        //expired baru
                        $new_expired=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($check_expired_vehicle->gatein_expired)));
                        $data_vehicle=array(
                            'ticket_number'=>$value,
                            'status'=>1,
                            'channel'=>$check_expired_vehicle->channel,
                            'booking_code'=>$check_expired_vehicle->booking_code,
                            'old_ticket_status'=>$check_expired_vehicle->status,
                            'old_gatein_expired'=>$check_expired_vehicle->gatein_expired,
                            'new_gatein_expired'=>$new_expired,
                            'force_majeure_code'=>$force_code,
                            'created_on'=>date("Y-m-d H:i:s"),
                            'created_by'=>$this->session->userdata("username")
                        );

                        $update_data_vehicle=array('gatein_expired'=>$new_expired,
                            'updated_on'=>date("Y-m-d H:i:s"),
                            'updated_by'=>$this->session->userdata("username"),
                        );

                        // cari data passangernya berdasarkan booking code yang ada pada vehice
                        $get_passanger_vehicle=$this->force->select_data("app.t_trx_booking_passanger"," where booking_code='{$check_expired_vehicle->booking_code}' and status!='-5' ")->result();

                        // mendapatkan ticket penumpang kendaraan
                        foreach ($get_passanger_vehicle as $key_pass => $value_pass) {
                            // update data
                            $data_passanger_vehicle=array(
                                'ticket_number'=>$value_pass->ticket_number,
                                'status'=>1,
                                'channel'=>$value_pass->channel,
                                'booking_code'=>$value_pass->booking_code,
                                'old_ticket_status'=>$value_pass->status,
                                'old_gatein_expired'=>$value_pass->gatein_expired,
                                'new_gatein_expired'=>$new_expired,
                                'force_majeure_code'=>$force_code,
                                'created_on'=>date("Y-m-d H:i:s"),
                                'created_by'=>$this->session->userdata("username")
                            );

                            $update_data_pass=array('gatein_expired'=>$new_expired,
                                'updated_on'=>date("Y-m-d H:i:s"),
                                'updated_by'=>$this->session->userdata("username"),
                            );

                            // echo $value_pass->ticket_number; exit;
                            // update ticket di gate vehicle
                            $this->force->update_data("app.t_trx_booking_passanger",$update_data_pass," ticket_number='{$value_pass->ticket_number}' ");

                            // insert table extend force majeure
                            $this->force->insert_data("app.t_trx_force_majeure_extend_passanger",$data_passanger_vehicle);

                        }

                        // update ticket di gate vehicle
                        $this->force->update_data("app.t_trx_booking_vehicle",$update_data_vehicle," ticket_number='{$check_expired_vehicle->ticket_number}' ");

                        // insert table extend force majeure
                        $this->force->insert_data("app.t_trx_force_majeure_extend_vehicle",$data_vehicle);
                    }
                    else
                    {
                        //expired baru

                        $new_expired=date('Y-m-d H:i:s',strtotime("+".$extend_param." hour",strtotime($check_expired_vehicle->boarding_expired)));

                        $data_vehicle=array(
                            'ticket_number'=>$value,
                            'status'=>1,
                            'channel'=>$check_expired_vehicle->channel,
                            'booking_code'=>$check_expired_vehicle->booking_code,
                            'old_ticket_status'=>$check_expired_vehicle->status,
                            'old_boarding_expired'=>$check_expired_vehicle->boarding_expired,
                            'new_boarding_expired'=>$new_expired,
                            'force_majeure_code'=>$force_code,
                            'created_on'=>date("Y-m-d H:i:s"),
                            'created_by'=>$this->session->userdata("username")
                        );

                        $update_data=array('boarding_expired'=>$new_expired,
                            'updated_on'=>date("Y-m-d H:i:s"),
                            'updated_by'=>$this->session->userdata("username"),
                        );

                        // echo $check_expired_vehicle->booking_code; exit;
                        // cari data passangernya berdasarkan booking code yang ada pada vehice
                        $get_passanger_vehicle=$this->force->select_data("app.t_trx_booking_passanger"," where booking_code='{$check_expired_vehicle->booking_code}' and status!='-5' ")->result();

                        foreach ($get_passanger_vehicle as $key_pass => $value_pass) {
                            // update data
                            $data_passanger_vehicle=array(
                                'ticket_number'=>$value_pass->ticket_number,
                                'status'=>1,
                                'channel'=>$value_pass->channel,
                                'booking_code'=>$value_pass->booking_code,
                                'old_ticket_status'=>$value_pass->status,
                                'old_boarding_expired'=>$value_pass->boarding_expired,
                                'new_boarding_expired'=>$new_expired,
                                'force_majeure_code'=>$force_code,
                                'created_on'=>date("Y-m-d H:i:s"),
                                'created_by'=>$this->session->userdata("username")
                            );

                            $update_data_pass=array('boarding_expired'=>$new_expired,
                                'updated_on'=>date("Y-m-d H:i:s"),
                                'updated_by'=>$this->session->userdata("username"),
                            );

                            // update ticket di gate vehicle
                            $this->force->update_data("app.t_trx_booking_passanger",$update_data_pass," ticket_number='{$value_pass->ticket_number}' ");

                            // insert table extend force majeure
                            $this->force->insert_data("app.t_trx_force_majeure_extend_passanger",$data_passanger_vehicle);

                        }

                        // update ticket di gate vehicle
                        $this->force->update_data("app.t_trx_booking_vehicle",$update_data," ticket_number='{$check_expired_vehicle->ticket_number}' ");

                        // insert table extend force majeure
                        $this->force->insert_data("app.t_trx_force_majeure_extend_vehicle",$data_vehicle);
                    }
                }

            }

            // print_r($data_passanger);
            // echo "<p></p>";
            // print_r($data_passanger_vehicle);
            //  exit;

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
                // return $res;
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');
                // return $res;
            }

            $logParam    = json_encode(array("data"=>"data berhasil"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;              
        }

    }

    public function get_data_ticket()
    {
        $ticket_number=strtoupper(trim($this->input->post("search")));

        // validasi jika input nya kosong
        if(empty($ticket_number))
        {
            $code=array("code"=>0,"message"=>"Silahakan ketik nomer tiket");
            $data=array(array(), $code);
            echo json_encode($data);
            exit;
        }

        // pengechekan tiket apakah tiket ini adalah tiket kendaraan atau penumpang
        $check_ticket=$this->force->check_passanger_type($ticket_number)->row();

        // apakah tiket ini  tidak ada
        if(empty($check_ticket))
        {
            $code=array("code"=>0,"message"=>"Tiket tidak ditemukan ");
            $data=array(array(), $code);
            echo json_encode($data);
            exit;
        }

        // Jika tiket bukanlah tiket exsekutif harcord 1 (reguler) 2.(eksekutif)
        if($check_ticket->ship_class==1)
        {
            $code=array("code"=>0,"message"=>"Tiket Bukan eksekutif ");
            $data=array(array(), $code);
            echo json_encode($data);
            exit;
        }

        $now=date('Y-m-d H:i:s');

        // service id 1 penumpang pejalan kaki
        if($check_ticket->service_id==1)
        {
            $get_passanger=$this->force->get_passanger(" where a.ticket_number='{$ticket_number}' ")->row();


        $app_identity=$this->force->select_data("app.t_mtr_identity_app","")->row();
        $get_port_name=$this->force->select_data("app.t_mtr_port"," where id=".$get_passanger->origin)->row();


            // check apakah  ini di aplikasi selain cloude
            if($app_identity->port_id !=0)
            {
                if($app_identity->port_id != $get_passanger->origin )
                {
                        $code=array("code"=>0,"message"=>"Tiket  Pelabuhan ".strtoupper($get_port_name->name)." tidak bisa di input ");
                        $data=array(array(), $code);
                        echo json_encode($data);
                        exit;
                }
            }
            else
            {
                if(!empty($this->session->userdata("port_id")))
                {
                    if($this->session->userdata("port_id") != $get_passanger->origin )
                    {

                        $code=array("code"=>0,"message"=>"Tiket  Pelabuhan ".strtoupper($get_port_name->name)." tidak bisa di input ");
                        $data=array(array(), $code);
                        echo json_encode($data);
                        exit;
                    }
                }

            }
            


            // check ticket apakah memenuhi syarat untuk force majeure
            if($get_passanger->status!=3 and $get_passanger->status!=4 and $get_passanger->status!=5 and $get_passanger->status!=7)
            {
                $code=array("code"=>0,"message"=>"Tiket Tidak Memenuhi Syarat");
                $data=array(array(), $code);
                echo json_encode($data);
                exit;
            }

            // mengubah status 
            if($get_passanger->status==3)
            {
                $status="Check In";

                // Jika sudah expire maka tidak bisa di force_majeure, check gatein expire
                if(date("Y-m-d H:i:s",strtotime($get_passanger->gatein_expired))<= $now)
                {
                    $code=array("code"=>0,"message"=>"Tiket Sudah Gatein Expired  ");
                    $data=array(array(), $code);
                    echo json_encode($data);
                    exit;
                }
            }
            else if($get_passanger->status==4 or $get_passanger->status==5 or $get_passanger->status==7)
            {
                if($get_passanger->status==4)
                {
                    $status="Gate In";
                }
                else if($get_passanger->status==5)
                {
                    $status="Boarding";
                }
                else
                {
                    $status="Muntah Kapal";
                }

                // Jika sudah expire maka tidak bisa di force_majeure
                if(date($get_passanger->boarding_expired)<= $now)
                {
                    $code=array("code"=>0,"message"=>"Tiket Sudah Boarding Expired ");
                    $data=array(array(), $code);
                    echo json_encode($data);
                    exit;
                }
            }
            else
            {
                $status="";
            }

            $row_data=array("ticket_number"=>$get_passanger->ticket_number,
                "booking_code"=>$get_passanger->booking_code,
                "name"=>$get_passanger->name,
                "age"=>$get_passanger->age,
                "gender"=>$get_passanger->gender,
                "type"=>$get_passanger->passanger_type_name,
                "service" =>"Penumpang",
                "plat_no"=>"",
                "status"=>$status,
            );
        }
        else
        {
            // cari berdasarkan booking code saja untuk mendapatkan data vehicle,
            $get_vehicle=$this->force->get_data_vehicle(" where a.booking_code='{$check_ticket->booking_code}' ")->row();


            $app_identity=$this->force->select_data("app.t_mtr_identity_app","")->row();
            $get_port_name=$this->force->select_data("app.t_mtr_port"," where id=".$get_vehicle->origin)->row();


            // check apakah  ini di aplikasi selain cloude
            if($app_identity->port_id !=0)
            {
                if($app_identity->port_id != $get_vehicle->origin )
                {
                        $code=array("code"=>0,"message"=>"Tiket  Pelabuhan ".strtoupper($get_port_name->name)." tidak bisa di input ");
                        $data=array(array(), $code);
                        echo json_encode($data);
                        exit;
                }
            }
            else
            {
                if(!empty($this->session->userdata("port_id")))
                {
                    if($this->session->userdata("port_id") != $get_vehicle->origin )
                    {

                        $code=array("code"=>0,"message"=>"Tiket  Pelabuhan ".strtoupper($get_port_name->name)." tidak bisa di input ");
                        $data=array(array(), $code);
                        echo json_encode($data);
                        exit;
                    }
                }

            }

            // check ticket apakah memenuhi syarat untuk force majeure
            if($get_vehicle->status!=3 and $get_vehicle->status!=4 and $get_vehicle->status!=5 and $get_vehicle->status!=7)
            {
                $code=array("code"=>0,"message"=>"Tiket Tidak Memenuhi Syarat");
                $data=array(array(), $code);
                echo json_encode($data);
                exit;
            }

            // mengubah status dan pengechekan di setiap table
            if($get_vehicle->status==3)
            {
                $status="Check In";

                // Jika sudah expire maka tidak bisa di force_majeure
                if(date($get_vehicle->gatein_expired)<= $now)
                {
                    $code=array("code"=>0,"message"=>"Tiket Sudah Gatein Expired ");
                    $data=array(array(), $code);
                    echo json_encode($data);
                    exit;
                }

            }
            else if($get_vehicle->status==4 or $get_vehicle->status==5 or $get_passanger->status==7)
            {
                if($get_vehicle->status==4)
                {
                    $status="Gate In";    
                }
                else if($get_vehicle->status==5)
                {
                    $status="Boarding"; 
                }
                else
                {
                    $status="Muntah Kapal";
                }
                
                // Jika sudah expire maka tidak bisa di force_majeure
                if(date($get_vehicle->boarding_expired)<= $now)
                {
                    $code=array("code"=>0,"message"=>"Tiket Sudah Boarding Expired ");
                    $data=array(array(), $code);
                    echo json_encode($data);
                    exit;
                }
            }
            else
            {
                $status="";
            }

            $row_data=array("ticket_number"=>$get_vehicle->ticket_number,
                    "booking_code"=>$get_vehicle->booking_code,
                    "name"=>$get_vehicle->name,
                    "age"=>$get_vehicle->age,
                    "gender"=>$get_vehicle->gender,
                    "type"=>$get_vehicle->vehicle_class_name,
                    "plat_no"=>$get_vehicle->id_number,
                    "service" =>"Kendaraan",
                    "status" =>$status,
            );
            // print_r($get_vehicle); exit;

        }

        // kode 
        $code=array("code"=>1);
        $data=array($row_data, $code);
        echo json_encode($data);

    }


    public function createCode($port)
    {
        $front_code="F".$port."".date('ymd');

        $len=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_trx_force_majeure where left(force_majeure_code,".$len.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (force_majeure_code) as max_code from app.t_trx_force_majeure where left(force_majeure_code,".$len.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $len, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

    public function get_custom_param($param_name,$type)
    {
        $where =" where status !='-5' and param_name='{$param_name}' and type='{$type}' ";
        return $this->force->select_data("app.t_mtr_custom_param", $where)->row();
    }


}
