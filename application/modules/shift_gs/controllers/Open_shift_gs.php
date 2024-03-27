<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Open_shift_gs extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('open_shift_gs_model');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_open_shift_gs';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'shift_gs/open_shift_gs';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->open_shift_gs_model->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Management Shift Gedung Sentral',
            'content'  => 'open_shift_gs/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'port'     => $this->global_model->select_data("app.t_mtr_port","where status in (1) order by name asc")->result(),
            'shift'    => $this->global_model->select_data("app.t_mtr_shift","where status in (1) order by shift_name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Penugasan';
        $data['username'] = $this->session->userdata("username");
        $data['port_id'] = $this->session->userdata("port_id");
        $data['port']=$this->global_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['shift']=$this->global_model->select_data("app.t_mtr_shift","where status=1 order by shift_name asc")->result();
        
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $username=trim($this->input->post("username"));
        $port=$this->enc->decode($this->input->post("port"));
        $date=trim($this->input->post("date"));
        $shift=$this->enc->decode($this->input->post("shift"));


        $this->form_validation->set_rules('username',"Username",'required');
        $this->form_validation->set_rules('port',"Pelabuhan",'required');
        $this->form_validation->set_rules('date',"Tanggal penugasan",'required');
        $this->form_validation->set_rules('shift',"Shift",'required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $create_code=$this->createCode($port);
      
        $data_assignment_gs=array(
            'username'=>$username,
            'port_id'=>$port,
            'shift_id'=>$shift,
            'status'=>1,
            'shift_gs_code'=>$create_code,
            'shift_gs_date'=>$date,
            'created_on'=>date("Y-m-d H:i:s"),
            'created_by'=>$this->session->userdata("username"),
        );

      
        $check_user_assignmnet=$this->open_shift_gs_model->select_data($this->_table," where username='".$username."' and shift_gs_date='".$date."' and shift_id='".$shift."' and status=1");
        //ceck shift , jika shift sudah di digunakan dalm tanggal yang sama
        $check_shift=$this->open_shift_gs_model->select_data($this->_table," where shift_id='".$shift."' and shift_gs_date='".$date."' and port_id=".$port." and status in (1,2)"); 
        //check kode agar bener2 tidak duplikasi
        $check_code=$this->open_shift_gs_model->select_data($this->_table," where shift_gs_code='".$create_code."'");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check_user_assignmnet->num_rows()>0)
        {
            echo $res=json_api(0,"Username ".$username." sudah assignment pada tanggal ".format_date($date));   
        }
        else if($check_shift->num_rows()>0)
        {
            echo $res=json_api(0,"Shift sudah diassignment pada tanggal ".format_date($date));   
        }        
        else if($check_code->num_rows()>0)
        {
            echo $res=json_api(0,"Gagal silahkan coba lagi");
        }
        else
        {
            $this->db->trans_begin();
            
            $this->open_shift_gs_model->insert_data($this->_table, $data_assignment_gs);

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
        $logUrl      = site_url().'shift_gs/open_shift_gs/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data_assignment_gs);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }


    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $shift_gs_code=$this->enc->decode($id);

        $get_detail=$this->open_shift_gs_model->detail($shift_gs_code)->row();
        $data['title'] = 'Edit Penugasan';
        $data['username'] = $this->session->userdata("username");
        $data['port']=$this->global_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $shift=$this->global_model->select_data("app.t_mtr_shift","where status=1 order by shift_name asc")->result();  
        
        $get_data=$this->open_shift_gs_model->detail($shift_gs_code)->row();
        $data['detail']=$get_data;
        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_close_shift($shift_gs_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'close_balance');
        $decode_code=$this->enc->decode("$shift_gs_code");
        $get_detail=$this->open_shift_gs_model->detail($decode_code)->row();

        $data=array(
            'status'=>2,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );
        
        $data_close=array(
            'username'=>$get_detail->username,
            'port_id'=>$get_detail->port_id,
            'shift_id'=>$get_detail->shift_id,
            'status'=>2,
            'shift_gs_code'=>$get_detail->shift_gs_code,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            'created_by'=>$this->session->userdata("username"),
        );



        // check apakah masih ada status yang berdinas
        // $check=$this->balance->select_data("app.t_trx_opening_balance"," where shift_gs_code='".$decode_code."' and status=1");

        // if($check->num_rows()>0)
        // {
        //     echo $res=json_api(0, 'Gagal closing balance, masih ada user yang berdinas');
        // }

        // else
        // {
            $this->db->trans_begin();
            $this->open_shift_gs_model->update_data($this->_table, $data," shift_gs_code='".$decode_code."'");
            $this->open_shift_gs_model->insert_data("app.t_trx_close_shift_gs", $data_close);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal close shift');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil close shift');
            }   

        // }



        /* Fungsi Create Log */
        $data2=array($data);
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/opening_balance/action_opening_balance';
        $logMethod   = 'CLOSE BALANCE';
        $logParam    = json_encode($data2);
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

        $shift_gs_code = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->open_shift_gs_model->update_data($this->_table,$data,"shift_gs_code='".$shift_gs_code."'");

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
        $logUrl      = site_url().'shift_gs/open_shift_gs/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
    
    function createCode($port)
    {
        $front_code="GS".$port."".date('ymd');

        $chekCode=$this->db->query("select * from app.t_trx_open_shift_gs where left(shift_gs_code,9)='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (shift_gs_code) as max_code from app.t_trx_open_shift_gs where left(shift_gs_code,9)='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, 9, 3);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%03s", $noUrut);
            return $kode;
        }
    }


    // public function action_edit()
    // {
    //     $shift_gs_code=$this->enc->decode($this->input->post('assignment'));
    //     $userspv=$this->enc->decode($this->input->post('userspv'));
    //     $username=$this->input->post('username2');
    //     $usercs=$this->input->post('usernamecs2');
    //     $list_user=$this->input->post('list_user[]');
    //     $list_usercs=$this->input->post('list_usercs[]');


    //     $this->form_validation->set_rules("assignment","Kode penugasan","required");

    //     $this->form_validation->set_message('required','%s harus diisi!');


    //     $data_update=array(
    //         'status'=>-5,
    //         'updated_on'=>date("Y-m-d H:i:s"),
    //         'updated_by'=>$this->session->userdata("Username"),
    //     );

    //     // mendecode id list user
    //     $list_user_id=array();
    //     if(!empty($list_user)) // mencegah terjadinya error jika tidak ada list yang di centang
    //     {
    //         foreach($list_user as $key=>$value)
    //         {
    //             $list_user_id[]=$this->enc->decode($value);
    //         }
    //     }

    //     // $list_user_id[]=$userspv;

    //     // echo print_r($list_user_id); exit;


    //     // mendecode id list user cs
    //     $list_usercs_id=array();
    //     foreach($list_usercs as $key=>$value)
    //     {
    //         $list_usercs_id[]=$this->enc->decode($value);
    //     }



    //     if($this->form_validation->run()===false)
    //     {
    //         echo $res=json_api(0, validation_errors());
    //     }
    //     else
    //     {

    //         // mengambil data berdasarkan shift_gs_code
    //         $data_assignment=$this->open_shift_gs_model->select_data($this->_table," where shift_gs_code='".$shift_gs_code."'")->row();

    //         // mengambil data cs berdasarkan shift_gs_code
    //         $data_assign_cs=$this->open_shift_gs_model->select_data("app.t_trx_assignment_cs"," where shift_gs_code='".$shift_gs_code."'")->row();
            

    //         if(!empty($username)) // pencegahan error jika username tidak di tambahkan
    //         {
    //             // mendecode id username dan mendapatkan data insert
    //             $user_arr=array();
    //             $error_user=array();
    //             $name_err=array();
    //             foreach(explode(",",$username) as $key=>$value)
    //             {
    //                 $user_id=$this->enc->decode($value);
    //                 $data_assignment_pos=array(
    //                     'user_id'=>$user_id,
    //                     'team_code'=>$data_assignment->team_code,
    //                     'port_id'=>$data_assignment->port_id,
    //                     'shift_id'=>$data_assignment->shift_id,
    //                     'status'=>1,
    //                     'shift_gs_code'=>$data_assignment->shift_gs_code,
    //                     'assignment_date'=>$data_assignment->assignment_date,
    //                     'created_on'=>date("Y-m-d H:i:s"),
    //                     'created_by'=>$this->session->userdata("username"),
    //                     );

    //                 $user_arr[]=$data_assignment_pos;


    //                 // check apakah masih dalam opening balance di tanggal yang sama
    //                 $check_op=$this->open_shift_gs_model->select_data("app.t_trx_opening_balance"," where status=1 and user_id=".$user_id." and trx_date='".$data_assignment->assignment_date."'");
    //                 $data_err_name=$this->open_shift_gs_model->get_user(" where a.id=".$user_id)->row();

    //                 if($check_op->num_rows()>0)
    //                 {
    //                     $error_user[]=1;
    //                     $name_err[]=$data_err_name->username;
    //                 }
    //             }

    //             // kondisi jika masih ada data yang opening balance langsung 
    //             if(array_sum($error_user)>0)
    //             {
    //                 echo $res=json_api(0," User ".implode(", ", $name_err)." dalam opening balance");
    //                 exit;
    //             }

    //         }

    //         if(!empty($usercs)) // pencegahan error jika username tidak di tambahkan
    //         {

    //             // mengambil data dan mendekode id username CS
    //             $usercs_arr=array();
    //             foreach (explode(",",$usercs) as $key => $value) {
    //                 $user_id=$this->enc->decode($value);
    //                 $data_assignment_cs=array(
    //                     'user_id'=>$user_id,
    //                     'team_code'=>$data_assign_cs->team_code,
    //                     'shift_gs_code'=>$data_assign_cs->shift_gs_code,
    //                     'port_id'=>$data_assign_cs->port_id,
    //                     'assignment_date'=>$data_assign_cs->assignment_date,
    //                     'shift_code'=>$data_assign_cs->shift_code,
    //                     'shift_id'=>$data_assign_cs->shift_id,
    //                     'status'=>1,
    //                     'created_on'=>date('Y-m-d'),
    //                     'created_by'=>$this->session->userdata("username"),
    //                 );

    //                 $usercs_arr[]=$data_assignment_cs;
    //             }
    //         }

    //         // $error_user2=array();
    //         // $name_err2=array();
    //         // if(!empty($list_user))
    //         // {
    //         //     // check apakah masih dalam opening balance dengan code yang sama
    //         //     $check_op=$this->open_shift_gs_model->select_data("app.t_trx_opening_balance"," where status=1 and user_id in (".implode(",", $list_user_id).") and shift_gs_code='".$shift_gs_code."'");

    //         //     foreach ($check_op->result() as $key => $value) {
    //         //         $data_err_name=$this->open_shift_gs_model->get_user(" where a.id=".$value->user_id)->row();
    //         //         $error_user2[]=1;
    //         //         $name_err2[]=$data_err_name->username;
                    
    //         //     }

    //         //     if(array_sum($error_user2)>0)
    //         //     {
    //         //         echo $res=json_api(0," User ".implode(", ", $name_err2)." dalam opening balance");
    //         //         exit;
    //         //     }

    //         // }

    //         // echo json_encode(print_r($usercs_arr)); exit;
    //         $this->db->trans_begin();
    //         if(!empty($username)) // menambahkan data assignmnet user pos jika ada datanya
    //         {
    //             // menambahkan user ke asignment user pos
    //             $this->open_shift_gs_model->insert_batch_data($this->_table,$user_arr);

    //             // insert ke opening balance
    //             $user_ob=array();
    //             foreach($user_arr as $key=>$value)
    //             {
    //                 $user_ob[]=$value['user_id'];

    //                 $data=array(
    //                     'trx_date'=>$data_assignment->assignment_date,
    //                     'shift_id'=>$data_assignment->shift_id,
    //                     'user_id'=>$value['user_id'],
    //                     'ob_code'=>$this->createCodeOpening($data_assignment->port_id),
    //                     'shift_gs_code'=>$data_assignment->shift_gs_code,
    //                     'status'=>1,
    //                     'created_on'=>date("Y-m-d H:i:s"),
    //                     'created_by'=>$this->session->userdata('username'),
    //                 );

    //                 $this->open_shift_gs_model->insert_data("app.t_trx_opening_balance",$data);
    //             }

    //         }

    //         if(!empty($usercs))
    //         {
    //             // menambahkan user ke asignment cs
    //             $this->open_shift_gs_model->insert_batch_data("app.t_trx_assignment_cs",$usercs_arr);    
    //         }

    //         // update data untuk list user yang tidak di centang
    //         // if(!empty($list_user))
    //         // {
    //         //     $this->open_shift_gs_model->update_data($this->_table,$data_update," user_id not in (".implode(",",$list_user_id).") and shift_gs_code='".$shift_gs_code."'");   
    //         // }


    //         if ($this->db->trans_status() === FALSE)
    //         {
    //             $this->db->trans_rollback();
    //             echo $res=json_api(0, 'Gagal tambah data');
    //         }
    //         else
    //         {
    //             $this->db->trans_commit();
    //             echo $res=json_api(1, 'Berhasil tambah data');
    //         }
    //     }   
    // }
    // public function action_edit()
    // {
    //     validate_ajax();
    //     $this->global_model->checkAccessMenuAction($this->_module,'edit');

    //     $shift_gs_code=$this->enc->decode($this->input->post('shift_gs_code'));
    //     $user_id=$this->input->post('user');
    //     $team_code=$this->enc->decode($this->input->post('team'));
    //     $shift_id=$this->enc->decode($this->input->post('shift'));
    //     $port_id=$this->enc->decode($this->input->post('port'));
    //     $assignment_date=$this->input->post('assignment_date');
    //     $list_user=$this->input->post('list_user[]');



    //     // mendecode data check box
    //     $user_id_decode=array();

    //     if (empty($list_user))
    //     {
    //         $list_id="shift_gs_code='".$shift_gs_code."' and status=1";
    //     }
    //     else
    //     {
    //         foreach($list_user as $key=>$value)
    //         {
    //             $user_id_decode[]=$this->enc->decode($value);
    //         }

    //         $list_id="shift_gs_code='".$shift_gs_code."' and user_id not in (".implode(",",$user_id_decode).") and status=1";
    //     }


    //     $this->form_validation->set_rules('shift_gs_code', 'Kode assignment', 'required');
    //     $this->form_validation->set_rules('user[]', 'User', 'required');
    //     $this->form_validation->set_rules('shift', 'shift', 'required');
    //     // remove user
    //         // mengambil data yang tidak kosong
    //         $user_decode=array();
    //         for($y=0;$y<=max(array_keys($user_id));$y++)
    //         {
    //             if (!empty($user_id[$y]))
    //             {
    //                 $user_decode[]=$this->enc->decode($user_id[$y]);
    //             }
    //         }

    //         // mencegah data array duplikat
    //         $user_unique=array_unique($user_decode);

        
    //         $this->db->trans_begin();

    //         $data_update=array(
    //             'status'=>-5,
    //             'updated_on'=>date("Y-m-d H:i:s"),
    //             'updated_by'=>$this->session->userdata("username"),
    //         );

    //         // jika tidak data check box di uncentang maka update
    //         if(!empty($user_id_decode))
    //         {// update data yang di uncentang di chekbox
    //             $this->open_shift_gs_model->update_data($this->_table, $data_update,$list_id);
    //         }

    //         // script lain
    //         $err_user=array();
    //         $mydata=array();
    //         if(empty($user_unique))
    //         {
    //             $err_user[]=0;
    //         }
    //         else
    //         {
    //             foreach($user_unique as $key=>$value)
    //             {
    //                 // pengecekan data jika user belum ad di code assigment yang sama 
    //                 $check=$this->open_shift_gs_model->select_data($this->_table,"where user_id='".$value."' and shift_gs_code='".$shift_gs_code."' and status=1 ")->num_rows();

    //                 $chek_other_user=$this->open_shift_gs_model->select_data($this->_table,"where user_id='".$value."' and shift_gs_code !='".$shift_gs_code."' and assignment_date='".$assignment_date."' and status=1 ")->row();
                    
    //                 $data=array('user_id'=>$value,
    //                     'team_code'=>$team_code,
    //                     'port_id'=>$port_id,
    //                     'shift_id'=>$shift_id,
    //                     'status'=>1,
    //                     'shift_gs_code'=>$shift_gs_code,
    //                     'assignment_date'=>$assignment_date,
    //                     'created_on'=>date("Y-m-d H:i:s"),
    //                     'created_by'=>$this->session->userdata("username"),
    //                     );

    //                 if($check>0)
    //                 {
    //                     $err_user[]=1;
    //                 }
    //                 else if(empty($chek_other_user))
    //                 {
    //                     $err_user[]=0;
    //                     $this->open_shift_gs_model->insert_data($this->_table,$data);   
    //                 }
    //                 else
    //                 {
    //                     $where=$chek_other_user->id;
    //                     $this->open_shift_gs_model->update_data($this->_table,$data_update,"id=".$where);
    //                     $this->open_shift_gs_model->insert_data($this->_table,$data); 
    //                     $err_user[]=0;
    //                 }
    //             }
    //         }



    //         if (array_sum($err_user)>0)
    //         {
    //             $this->db->trans_rollback();
    //             echo $res=json_api(0, 'User sudah ada di regu ini');
    //         }
    //         else
    //         {
    //             if ($this->db->trans_status() === FALSE)
    //             {
    //                 $this->db->trans_rollback();
    //                 echo $res=json_api(0, 'Gagal tambah data');
    //             }
    //             else
    //             {
    //                 $this->db->trans_commit();
    //                 echo $res=json_api(1, 'Berhasil tambah data');
    //             }
    //         }

    //     // /* Fungsi Create Log */
    //     // $createdBy   = $this->session->userdata('username');
    //     // $logUrl      = site_url().'shift_gs/team/action_edit';
    //     // $logMethod   = 'EDIT';
    //     // $logParam    = json_encode($data);
    //     // $logResponse = $res;

    //     // $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    // }

    


    // function get_data()
    // {
    //     $port_id=$this->input->post("port");
    //     $port_decode=$this->enc->decode($port_id);

    //     empty($port_decode)?$kode=0:$kode=$port_decode;

    //     $row=$this->open_shift_gs_model->select_data("core.t_mtr_team","where port_id='".$kode."' and status=1")->result();

    //     $regu=array();
    //     $spv=array();
    //     $user=array();
    //     $usercs=array();
    //     $data=array();
    //     foreach ($row as $key => $value) {
    //         $value->team_code=$this->enc->encode($value->team_code); // decript code
    //         $regu[]=$value;
    //     }


    //     // hardcord user group 3 (spv pos)
    //     $rowspv=$this->open_shift_gs_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=3 ")->result();

    //     // hardcord user group 4 (operator pos)
    //     $rowuser=$this->open_shift_gs_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=4 ")->result();

    //     // hardcord user group 6 (CS)
    //     $rowusercs=$this->open_shift_gs_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=6 ")->result();

    //     foreach ($rowspv as $key => $value) {
    //         $value->id=$this->enc->encode($value->id); // decript id user
    //         $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
    //         $spv[]=$value;
    //     }

    //     foreach ($rowuser as $key => $value) {
    //         $value->id=$this->enc->encode($value->id); // decript id user
    //         $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
    //         $user[]=$value;
    //     }

    //     foreach ($rowusercs as $key => $value) {
    //         $value->id=$this->enc->encode($value->id); // decript id user
    //         $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
    //         $usercs[]=$value;
    //     }

    //     $data['regu']=$regu;
    //     $data['spv']=$spv;
    //     $data['user']=$user;
    //     $data['usercs']=$usercs;


    //     echo json_encode($data);


    //     // echo json_encode("tes data");
    // }

    // function get_user()
    // {
        
    //     $port_id=$this->input->post("port");
    //     $port_decode=$this->enc->decode($port_id);
    //     empty($port_decode)?$kode=0:$kode=$port_decode;

    //     // mendapatkan user pos berdasarkan user group 4 (pos) hardcord
    //     $row=$this->open_shift_gs_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=4 ")->result();

    //     $data=array();
    //     foreach ($row as $key => $value) {
    //         $value->id=$this->enc->encode($value->id); // decript id user
    //         $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
    //         $data[]=$value;
    //     }

    //     echo json_encode($data);
    // }


    // function get_usercs()
    // {

    //     // hard cord supervisor pos yang user group 3
        
    //     $port_id=$this->input->post("port");
    //     $port_decode=$this->enc->decode($port_id);
    //     empty($port_decode)?$kode=0:$kode=$port_decode;
    //     $row=$this->open_shift_gs_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=6 ")->result();

    //     $data=array();
    //     foreach ($row as $key => $value) {
    //         $value->id=$this->enc->encode($value->id); // decript id user
    //         $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
    //         $data[]=$value;
    //     }

    //     echo json_encode($data);
    // }




    // function createCodeCs($port)
    // {
    //     $front_code="S".$port."".date('ymd');

    //     $chekCode=$this->db->query("select * from app.t_trx_assignment_user_pos where left(shift_gs_code,8)='".$front_code."' ")->num_rows();

    //     if($chekCode<1)
    //     {
    //         $shelterCode=$front_code."0001";
    //         return $shelterCode;
    //     }
    //     else
    //     {
    //         $max=$this->db->query("select max (shift_gs_code) as max_code from app.t_trx_assignment_user_pos where left(shift_gs_code,8)='".$front_code."' ")->row();
    //         $kode=$max->max_code;
    //         $noUrut = (int) substr($kode, 8, 4);
    //         $noUrut++;
    //         $char = $front_code;
    //         $kode = $char . sprintf("%04s", $noUrut);
    //         return $kode;
    //     }
    // }

    // function createCodeOpening($port)
    // {
    //     $front_code="O".$port."".date('ymd');

    //     $chekCode=$this->db->query("select * from app.t_trx_opening_balance where left(ob_code,8)='".$front_code."' ")->num_rows();

    //     if($chekCode<1)
    //     {
    //         $shelterCode=$front_code."0001";
    //         return $shelterCode;
    //     }
    //     else
    //     {
    //         $max=$this->db->query("select max (ob_code) as max_code from app.t_trx_opening_balance where left(ob_code,8)='".$front_code."' ")->row();
    //         $kode=$max->max_code;
    //         $noUrut = (int) substr($kode, 8, 4);
    //         $noUrut++;
    //         $char = $front_code;
    //         $kode = $char . sprintf("%04s", $noUrut);
    //         return $kode;
    //     }
    // }

}
