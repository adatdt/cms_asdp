<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Refund_force_majeure extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_refund_force_majeure','force');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_force_majeure';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/refund_force_majeure';
	}

	public function index(){

        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->force->dataList();
            echo json_encode($rows);
            exit;
        }

        if(!empty($this->session->userdata("port_id")))
        {
            $port=$this->force->select_data("app.t_mtr_port"," where id=".$this->session->userdata("port_id")." order by name asc ")->result();
        }
        else
        {
            $port=$this->force->select_data("app.t_mtr_port"," where status !='-5' order by name asc ")->result();
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Refund Force Majeure',
            'content'  => 'refund_force_majeure/index',
            'port'  => $port,
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Refund Force Majeure';
        // hardcord user group user bank
        $data['port'] = $this->force->select_data("app.t_mtr_port"," where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/add',$data);
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

        $now=date('Y-m-d H:i:s');

        // service id 1 penumpang pejalan kaki
        if($check_ticket->service_id==1)
        {
            $get_passanger=$this->force->get_passanger(" where a.ticket_number='{$ticket_number}' ")->row();

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


}
