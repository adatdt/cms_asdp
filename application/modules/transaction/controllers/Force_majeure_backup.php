<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Force_majeure extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_force_majeure','force');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'core.t_mtr_user_bank';
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

        $btn_add="";
        $url_eks=site_url($this->_module.'/add_force_majeure_eks');
        $url=site_url($this->_module.'/add_force_majeure_eks');
        // menampilkan button force majeur
        if(checkBtnAccess($this->_module,'add'))
        {
            $btn_add.='<button onclick="showModal(\''.$url_eks.'\')" class="btn btn-sm btn-warning" title="Force Majeure Eksekutif"><i class="fa fa-plus"></i> Force Majeure Eksekutif</button> ';

            $btn_add.='<button onclick="showModal(\''.$url.'\')" class="btn btn-sm btn-warning" title="TForce Majeure General"><i class="fa fa-plus"></i> Force Majeure General</button> ';
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Force Majeure',
            'content'  => 'force_majeure/index',
            //'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'btn_add' =>$btn_add
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah User Bank';
        $data['username'] = $this->user->select_data("core.t_mtr_user"," where user_group_id=26 and status=1 order by username asc")->result();
        $data['bank'] = $this->user->select_data("core.t_mtr_bank"," where status=1 order by bank_name asc")->result();


        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $user_id=$this->enc->decode($this->input->post('username'));
        $bank_abbr=$this->enc->decode($this->input->post('bank'));

        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('bank', 'Bank ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        
        $data=array(
                    'user_id'=>$user_id,
                    'bank_abbr'=>$bank_abbr,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // ceck data jika username sudah ada
        $check=$this->user->select_data($this->_table," where user_id='".$user_id."' and status not in (-5) ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"User Bank sudah ada.");
        }
        else
        {
            $this->db->trans_begin();

            $this->user->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/user_bank/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function add_force_majeure_eks(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Force Majeure Eksekutif';

        $this->load->view($this->_module.'/add_force_majeure_eks',$data);
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
        // if($check_ticket->ship_class==1)
        // {
        //     $code=array("code"=>0,"message"=>"Tiket Bukan eksekutif ");
        //     $data=array(array(), $code);
        //     echo json_encode($data);
        //     exit;
        // }


        // service id 1 penumpang pejalan kaki
        if($check_ticket->service_id==1)
        {
            $get_passanger=$this->force->get_passanger(" where a.ticket_number='{$ticket_number}' ")->row();

            $row_data=array("ticket_number"=>$get_passanger->ticket_number,
                "booking_code"=>$get_passanger->booking_code,
                "name"=>$get_passanger->name,
                "age"=>$get_passanger->age,
                "gender"=>$get_passanger->gender,
                "type"=>$get_passanger->passanger_type_name,
                "service" =>"Penumpang",
                "plat_no"=>"",
            );
        }
        else
        {
            // cari berdasarkan booking code saja untuk mendapatkan data vehicle,
            $get_vehicle=$this->force->get_data_vehicle(" where a.booking_code='{$check_ticket->booking_code}' ")->row();

            $row_data=array("ticket_number"=>$get_vehicle->ticket_number,
                    "booking_code"=>$get_vehicle->booking_code,
                    "name"=>$get_vehicle->name,
                    "age"=>$get_vehicle->age,
                    "gender"=>$get_vehicle->gender,
                    "type"=>$get_vehicle->vehicle_class_name,
                    "plat_no"=>$get_vehicle->id_number,
                    "service" =>"Kendaraan",
            );

            // print_r($get_vehicle); exit;

        }


        // kode 
            $code=array("code"=>1);
            $data=array($row_data, $code);
            echo json_encode($data);

    }
    public function create_log($link, $action, $data,$res)
    {
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url()."".$link;
        $logMethod   = $action;
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
            'updated_by'=>$this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->param->update_data($this->_table,$data,"param_id=".$d[0]);

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
        $logUrl      = site_url().'master_data/setting_param/action_change';
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


}
