<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Assignment_user_pos extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('assignment_user_pos_model');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_assignment_user_pos';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'shift_management/assignment_user_pos';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('port')){
                $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
            }
            if($this->input->post('dateFrom')){
                $this->form_validation->set_rules('dateFrom', 'Tanggal awal', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal awal'));
            }
            if($this->input->post('dateTo')){
                $this->form_validation->set_rules('dateTo', 'Tanggal akhir', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal akhir'));
            }
            

            
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->assignment_user_pos_model->dataList();
            echo json_encode($rows);
            exit;
        }

        if($this->identity_app()==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port=$this->assignment_user_pos_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
                $row_port=0;               
            }
            else
            {
                $pid=$this->session->userdata('port_id');
                $port=$this->assignment_user_pos_model->select_data("app.t_mtr_port","where id={$pid} order by name asc")->result();
                $row_port=1;     
            }
        }
        else
        {
            $port=$this->assignment_user_pos_model->select_data("app.t_mtr_port","where id={$this->identity_app()} order by name asc")->result();
            $row_port=1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Penugasan',
            'content'  => 'assignment_user_pos/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'port'=>$port,
            'row_port'=>$row_port,
            'team'=>$this->assignment_user_pos_model->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // mengambil port berdasarkan app identity / port id user

        if($this->identity_app()==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $data['port']=$this->assignment_user_pos_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();               
            }
            else
            {
                $pid=$this->session->userdata('port_id');
                $data['port']=$this->assignment_user_pos_model->select_data("app.t_mtr_port","where id={$pid} order by name asc")->result();     
            }
        }
        else
        {
            $data['port']=$this->assignment_user_pos_model->select_data("app.t_mtr_port","where id={$this->identity_app()} order by name asc")->result();
        }

        $data['title'] = 'Tambah Penugasan';
        $data['user']=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where status=1 order by username asc")->result();
        $data['team']=$this->assignment_user_pos_model->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result();
        $data['shift']=$this->assignment_user_pos_model->select_data("app.t_mtr_shift","where status=1 order by shift_name asc")->result();
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');


        $this->form_validation->set_rules('username2',"Username POS",'trim|required');
        $this->form_validation->set_rules('usernamecs2',"Username CS",'trim|required');
        $this->form_validation->set_rules('userptcstc2',"Username PTC/ STC",'trim|required');
        $this->form_validation->set_rules('uservertifikator2',"Username Vertifikator",'trim|required');

        $this->form_validation->set_rules('port',"Pelabuhan",'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        $this->form_validation->set_rules('date',"Tanggal penugasan",'trim|required');
        $this->form_validation->set_rules('spv',"Username SPV",'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid username SPV'));
        $this->form_validation->set_rules('team',"Regu",'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid regus'));
        $this->form_validation->set_rules('shift',"Shift",'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid shift'));

        $this->form_validation->set_message('required','%s harus diisi!');

        $data2 = null;
        if($this->form_validation->run()===false)
        {
                echo $res=json_api(0, validation_errors());
        } 
        else 
        {
            $username=trim($this->input->post("username2"));
            $usercs=trim($this->input->post("usernamecs2"));
            $userptcstc=trim($this->input->post("userptcstc2"));
            $uservertifikator=trim($this->input->post("uservertifikator2"));
            $userComandCenter=trim($this->input->post("userComandCenter2"));

            $port=$this->enc->decode($this->input->post("port"));
            $date=trim($this->input->post("date"));
            $spv=$this->enc->decode($this->input->post("spv"));
            $team_code=$this->enc->decode($this->input->post("team"));
            $shift=$this->enc->decode($this->input->post("shift"));

            $arr_username=array();
            $arr_usercs=array();
            $arr_userptcstc=array();
            $arr_uservertifikator=array();

            $check_userpos=array();
            $get_userpos_err=array();
            $check_usercs=array();
            $check_userptcstc=array();
            $check_uservertifikator=array();

            $get_usercs_err=array();
            $get_userptcstc_err=array();
            $get_uservertifikator_err=array();

            $create_code=$this->createCode($port);

            // mengambil data dan mendekode id username Vertivicator
            foreach (explode(",",$uservertifikator) as $key => $value) {

                // $create_code_cs=$this->createCodeCs($port);
                $user_id=$this->enc->decode($value);
                $data_user_vertifikator=array(
                    'user_id'=>$user_id,
                    'team_code'=>$team_code,
                    'assignment_code'=>$create_code,
                    'port_id'=>$port,
                    'assignment_date'=>$date,
                    // 'shift_code'=>$create_code_cs,
                    'shift_id'=>$shift,
                    'status'=>1,
                    'created_on'=>date('Y-m-d'),
                    'created_by'=>$this->session->userdata("username"),
                );

                $arr_uservertifikator[]=$data_user_vertifikator;

                //pengecekan jika username Vertivicator sudah di assignment di tanggal yang sama
                // $check=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_verifier"," where user_id='".$user_id."' and assignment_date='".$date."' and status !='-5' ");
                $check=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_verifier"," where user_id='".$user_id."' and assignment_date='".$date."' and status =1 ");            

                if($check->num_rows()>0)
                {
                    $check_uservertifikator[]=1;

                    $get_name=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where id=".$user_id)->row();
                    $get_uservertifikator_err[]=$get_name->username;
                }
            }

            // mengambil data dan mendekode id username CS
            foreach (explode(",",$usercs) as $key => $value) {

                // $create_code_cs=$this->createCodeCs($port);
                $user_id=$this->enc->decode($value);
                $data_assignment_cs=array(
                    'user_id'=>$user_id,
                    'team_code'=>$team_code,
                    'assignment_code'=>$create_code,
                    'port_id'=>$port,
                    'assignment_date'=>$date,
                    // 'shift_code'=>$create_code_cs,
                    'shift_id'=>$shift,
                    'status'=>1,
                    'created_on'=>date('Y-m-d'),
                    'created_by'=>$this->session->userdata("username"),
                );

                $arr_usercs[]=$data_assignment_cs;

                //pengecekan jika username CS sudah di assignment di tanggal yang sama
                // $check=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_cs"," where user_id='".$user_id."' and assignment_date='".$date."' and status !='-5' ");
                $check=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_cs"," where user_id='".$user_id."' and assignment_date='".$date."' and status =1 ");

                if($check->num_rows()>0)
                {
                    $check_usercs[]=1;

                    $get_name=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where id=".$user_id)->row();
                    $get_usercs_err[]=$get_name->username;
                }
            }

            // mengambil data dan mendekode id username PTCSTC
            foreach (explode(",",$userptcstc) as $key => $value) {

                // $create_code_cs=$this->createCodeCs($port);
                $user_id=$this->enc->decode($value);
                $data_assignment_ptcstc=array(
                    'user_id'=>$user_id,
                    'team_code'=>$team_code,
                    'assignment_code'=>$create_code,
                    'port_id'=>$port,
                    'assignment_date'=>$date,
                    // 'shift_code'=>$create_code_cs,
                    'shift_id'=>$shift,
                    'status'=>1,
                    'created_on'=>date('Y-m-d'),
                    'created_by'=>$this->session->userdata("username"),
                );

                $arr_userptcstc[]=$data_assignment_ptcstc;

                //pengecekan jika username CS sudah di assignment di tanggal yang sama
                // $check=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_ptc_stc"," where user_id='".$user_id."' and assignment_date='".$date."' and status !='-5' ");
                $check=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_ptc_stc"," where user_id='".$user_id."' and assignment_date='".$date."' and status =1 ");

                if($check->num_rows()>0)
                {
                    $check_userptcstc[]=1;

                    $get_name=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where id=".$user_id)->row();
                    $get_userptcstc_err[]=$get_name->username;
                }
            }

            // mengambil data dan mendekode id username
            foreach (explode(",",$username) as $key => $value) {

                $user_id=$this->enc->decode($value);


                $data_assignment_pos=array('user_id'=>$user_id,
                    'team_code'=>$team_code,
                    'port_id'=>$port,
                    'shift_id'=>$shift,
                    'status'=>1,
                    'assignment_code'=>$create_code,
                    'assignment_date'=>$date,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username"),
                    );

                $arr_username[]=$data_assignment_pos;

                //pengecekan jika username sudah di assignment
                // $check=$this->assignment_user_pos_model->select_data($this->_table," where user_id='".$user_id."' and assignment_date='".$date."' and status !='-5' ");
                $check=$this->assignment_user_pos_model->select_data($this->_table," where user_id='".$user_id."' and assignment_date='".$date."' and status =1 ");
                if($check->num_rows()>0)
                {
                    $check_userpos[]=1;

                    $get_name=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where id=".$user_id)->row();
                    $get_userpos_err[]=$get_name->username;
                }
            }

                $data_assignment_pos=array('user_id'=>$spv,
                'team_code'=>$team_code,
                'port_id'=>$port,
                'shift_id'=>$shift,
                'status'=>1,
                'assignment_code'=>$create_code,
                'assignment_date'=>$date,
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata("username"),
            );

            $arr_username[]=$data_assignment_pos;

            //ceck user spv di user_assignment_pos
            $check_spv_assigmnetpos=$this->assignment_user_pos_model->select_data($this->_table," where user_id='".$spv."' and assignment_date='".$date."' and status=1");


            $data_assignment_regu=array(
                'supervisor_id'=>$spv,
                'team_code'=>$team_code,
                'assignment_code'=>$create_code,
                'port_id'=>$port,
                'assignment_date'=>$date,
                'shift_id'=>$shift,
                'status'=>1,
                'created_on'=>date('Y-m-d H:i:s'),
                'created_by'=>$this->session->userdata("username"),
            );

            //ceck user spv di user_assignment_regu
            // $check_spv_assigmnetregu=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_regu"," where supervisor_id='".$spv."' and assignment_date='".$date."' and status !='-5' ");
            $check_spv_assigmnetregu=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_regu"," where supervisor_id='".$spv."' and assignment_date='".$date."' and status =1 ");

            $get_spv_username=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where id=".$spv)->row();

            //ceck user spv di tim , jika nama tim sudah di assignment
            $check_team=$this->assignment_user_pos_model->select_data($this->_table," where team_code='".$team_code."' and assignment_date='".$date."' and status !='-5'");

            //ceck shift , jika shift sudah di digunakan dalm tanggal yang sama
            $check_shift=$this->assignment_user_pos_model->select_data($this->_table," where shift_id='".$shift."' and assignment_date='".$date."' and port_id=".$port." and status !='-5'"); // harus dengan status 2 juga karena status 2 tutup shift

            $get_team_name=$this->assignment_user_pos_model->select_data("core.t_mtr_team","where team_code='".$team_code."'")->row();

            //check kode agar bener2 tidak duplikasi
            $check_code=$this->assignment_user_pos_model->select_data($this->_table," where assignment_code='".$create_code."'");

            $data_comand = array();
            $getTerminalCode = array();
            $checkDevice = array();
            $arr_userComandCenter=array();
            $arrNameDevice = [];

            if (!empty($userComandCenter)){
                // mengambil data dan mendekode id username comand center
                foreach (explode(",",$userComandCenter) as $key => $value) {

                    // $create_code_cs=$this->createCodeCs($port);
                    $user_id=$value;
                    $data_user_comand_center=array(
                        'user_id'=>$user_id,
                        'team_code'=>$team_code,
                        'assignment_code'=>$create_code,
                        'port_id'=>$port,
                        'assignment_date'=>$date,
                        // 'shift_code'=>$create_code_cs,
                        'shift_id'=>$shift,
                        'status'=>1,
                        'created_on'=>date('Y-m-d'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    $arr_userComandCenter[]=$data_user_comand_center;

                    $check=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_command_center"," where user_id='".$user_id."' and assignment_date='".$date."' and status =1 ");            

                    if($check->num_rows()>0)
                    {
                        $check_userComandCenter[]=1;

                        $get_name=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where id=".$user_id)->row();
                        $get_userComandCenter_err[]=$get_name->username;
                    }

                    empty($port)?$kode=0:$kode=$port;
                    $valComandCenter[]=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$kode."'and id='".$user_id."' and status=1 and user_group_id=42  ")->result();

                }

                // print_r($valComandCenter);exit;
                
                foreach ($valComandCenter as $key => $value) {
                    // echo $value[0]->id ;
                    $deviceComandCenter[$value[0]->id]=trim($this->input->post("deviceComandCenter2_{$value[0]->id}"));
                    
                    foreach ($deviceComandCenter as $key2 => $value2) {
                        $explodedComandCenter = explode(',', $value2);
                    }
                    // print_r($explodedComandCenter);exit;

                    $data_comand[$value[0]->id]= $explodedComandCenter;
                    $getTerminalCode[]= $explodedComandCenter;
                
                }

                // print_r($data_comand);exit;

                //check terminal agar tidak duplikasi
                for($i=0; $i<count($getTerminalCode); $i++)
                {
                    foreach($getTerminalCode[$i] as $key => $value){
                        $checkDevice[] = $value;
                    }
                }

                $arrCheckDevice = array_count_values($checkDevice);

                foreach($arrCheckDevice as $keyD => $valueD ){
                    
                    if ($valueD > 1){
                        $getNameDevice= $this->assignment_user_pos_model->select_data("app.t_mtr_device_terminal "," where terminal_code='".$keyD."' ")->row();          
                        $arrNameDevice[]=$getNameDevice->terminal_name;
                    }

                }
            }

            $data2[]=$arr_username;
            $data2[]=$arr_usercs;
            $data2[]=$arr_userptcstc;
            $data2[]=$data_assignment_regu;
            $data2[]=$arr_uservertifikator;
         
            if(array_sum($check_userpos)>0)
            {
                echo $res=json_api(0,"Username ".implode(", ",$get_userpos_err)." sudah diassignment pada tanggal ".format_date($date));   
            }
            else if($check_shift->num_rows()>0)
            {
                echo $res=json_api(0,"Shift sudah diassignment pada tanggal ".format_date($date));   
            }
            else if(array_sum($check_usercs)>0)
            {
                echo $res=json_api(0,"Username ".implode(", ",$get_usercs_err)." sudah diassignment pada tanggal ".format_date($date));   
            }
            else if(array_sum($check_userptcstc)>0)
            {
                echo $res=json_api(0,"Username ".implode(", ",$get_userptcstc_err)." sudah diassignment pada tanggal ".format_date($date));   
            } 
            else if(array_sum($check_uservertifikator)>0)
            {
                echo $res=json_api(0,"Username ".implode(", ",$get_uservertifikator_err)." sudah diassignment pada tanggal ".format_date($date));   
            }                
            else if($check_spv_assigmnetregu->num_rows()>0)
            {
                echo $res=json_api(0,"Spv dengan user ".$get_spv_username->username." sudah diassignment pada tanggal ".format_date($date));   
            }
            else if ($check_team->num_rows()>0)
            {
                echo $res=json_api(0,"Regu ".$get_team_name->team_name." sudah diassignment pada tanggal ".format_date($date));
            }
            else if($check_code->num_rows()>0)
            {
                echo $res=json_api(0,"Gagal silahkan coba lagi");
            }
            else if (!empty(count($arrNameDevice)))
            {
                echo $res=json_api(0, "Perangkat ".implode(", ",$arrNameDevice)." duplikat antara user comand center"); 

            }
            else
            {
                $this->db->trans_begin();
                
                $this->assignment_user_pos_model->insert_batch_data($this->_table,$arr_username);
                $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_regu",$data_assignment_regu);

                $insert_data_device=array();
                $insert_data_comand_center=array();

                if (!empty($userComandCenter)){

                    foreach($arr_userComandCenter as $key=>$value)
                    {
                        $getShiftCode= $this->createCodeComandCenter($port);
                        
                        $insert_data_comand_center=array(
                        'user_id'=>$value['user_id'],
                        'team_code'=>$value['team_code'],
                        'assignment_code'=>$value['assignment_code'],
                        'port_id'=>$value['port_id'],
                        'assignment_date'=>$value['assignment_date'],
                        'shift_code'=>$getShiftCode,
                        'shift_id'=>$value['shift_id'],
                        'status'=>1,
                        'created_on'=>date('Y-m-d H:i:s'),
                        'created_by'=>$this->session->userdata("username"),
                        );
                        // print_r($insert_data_comand_center);exit;

                        $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_command_center",$insert_data_comand_center);
                        
                        foreach($data_comand[$value['user_id']] as $key2=> $value2 ){

                            $insert_data_device[]=array(
                            'terminal_code'=>$value2,
                            'team_code'=>$value['team_code'],
                            'assignment_code'=>$value['assignment_code'],
                            'port_id'=>$value['port_id'],
                            'assignment_date'=>$value['assignment_date'],
                            'shift_code'=>$getShiftCode,
                            'shift_id'=>$value['shift_id'],
                            'status'=>1,
                            'created_on'=>date('Y-m-d H:i:s'),
                            'created_by'=>$this->session->userdata("username"),

                            );
                        }
                    }

                    $this->assignment_user_pos_model->insert_batch_data("app.t_mtr_pairing_device_command_center",$insert_data_device);
                }
                
                // insert ke opening balance
                foreach($arr_username as $key=>$value)
                {
                    $user_ob[]=$value['user_id'];

                    $data=array(
                        'trx_date'=>$date,
                        'shift_id'=>$shift,
                        'user_id'=>$value['user_id'],
                        'ob_code'=>$this->createCodeOpening($port),
                        'assignment_code'=>$create_code,
                        'status'=>1,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata('username'),
                    );

                    $this->assignment_user_pos_model->insert_data("app.t_trx_opening_balance",$data);
                }

                foreach($arr_usercs as $key=>$value)
                {
                    
                    $insert_data_cs=array(
                    'user_id'=>$value['user_id'],
                    'team_code'=>$value['team_code'],
                    'assignment_code'=>$value['assignment_code'],
                    'port_id'=>$value['port_id'],
                    'assignment_date'=>$value['assignment_date'],
                    'shift_code'=>$this->createCodeCs($port),
                    'shift_id'=>$value['shift_id'],
                    'status'=>1,
                    'created_on'=>date('Y-m-d H:i:s'),
                    'created_by'=>$this->session->userdata("username"),
                    );

                    $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_cs",$insert_data_cs);

                }

                foreach($arr_userptcstc as $key=>$value)
                {
                    
                    $insert_data_ptcstc=array(
                    'user_id'=>$value['user_id'],
                    'team_code'=>$value['team_code'],
                    'assignment_code'=>$value['assignment_code'],
                    'port_id'=>$value['port_id'],
                    'assignment_date'=>$value['assignment_date'],
                    'shift_code'=>$this->createCodeptcstc($port),
                    'shift_id'=>$value['shift_id'],
                    'status'=>1,
                    'created_on'=>date('Y-m-d H:i:s'),
                    'created_by'=>$this->session->userdata("username"),
                    );

                    $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_ptc_stc",$insert_data_ptcstc);

                }

                foreach($arr_uservertifikator as $key=>$value)
                {
                    
                    $insert_data_uservertifikator=array(
                    'user_id'=>$value['user_id'],
                    'team_code'=>$value['team_code'],
                    'assignment_code'=>$value['assignment_code'],
                    'port_id'=>$value['port_id'],
                    'assignment_date'=>$value['assignment_date'],
                    'shift_code'=>$this->createCodeVertifikator($port),
                    'shift_id'=>$value['shift_id'],
                    'status'=>1,
                    'created_on'=>date('Y-m-d H:i:s'),
                    'created_by'=>$this->session->userdata("username"),
                    );
        
                    $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_verifier",$insert_data_uservertifikator);
                }


                // print_r($insert_data_ptcstc); exit;            

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
        $logUrl      = site_url().'shift_management/assignment_user_pos/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data2);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);




        // echo print_r($arr_username);
    }


    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $assignment_code=$this->enc->decode($id);

        $get_detail=$this->assignment_user_pos_model->detail($assignment_code)->row();
        $data['title'] = 'Edit Penugasan';
        $data['port']=$this->assignment_user_pos_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['team']=$this->assignment_user_pos_model->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result();

        // menampilkan data user pos
        $data['detail']=$this->assignment_user_pos_model->detail($assignment_code, " and d.user_group_id=4 and a.status not in (-5) ")->result();

        // menampilkan data user cs
        $data['detail_cs']=$this->assignment_user_pos_model->detail_cs($assignment_code, " and d.user_group_id=6 and a.status not in (-5) ")->result();

        // menampilkan data detail ptc/stc
        $data['detail_ptcstc']=$this->assignment_user_pos_model->detail_ptc_stc($assignment_code, " and (d.user_group_id=2 or d.user_group_id=10 ) and a.status not in (-5) ")->result();

        // menampilkan data vertifikator
        $data['detail_vertifikator']=$this->assignment_user_pos_model->detail_vertifikator($assignment_code, " and d.user_group_id=35 and a.status not in (-5) ")->result();


        $data['shift']=$this->assignment_user_pos_model->select_data("app.t_mtr_shift"," order by shift_name asc")->result();
        $data['detail2']=$get_detail;
        // mengambil data spv
        $data['userspv']=$this->assignment_user_pos_model->get_user("where a.user_group_id=3 and port_id=".$get_detail->port_id." order by username asc")->result();
        $data['spv']=$this->assignment_user_pos_model->detail($assignment_code, " and d.user_group_id=3 ")->row();

        // mengambil data regu
        $data['regu']=$this->assignment_user_pos_model->select_data("core.t_mtr_team"," where port_id=".$get_detail->port_id." order by team_name asc")->result();

        $get_port=$this->assignment_user_pos_model->detail($assignment_code)->row();

        // cek data user yang di tammpilkan hanya user selain user id yg sudah ada dan di pelabuhan yang di pilih saja
        $data['user']=$this->assignment_user_pos_model->get_user(" where id not in (select user_id from app.t_trx_assignment_user_pos where assignment_code='".$assignment_code."' and status !='-5' ) and port_id='".$get_detail->port_id."' and status=1 and user_group_id=4 order by username asc")->result();

        // cek data user CS yang di tammpilkan hanya user selain user id yg sudah ada dan di pelabuhan yang di pilih saja
        $data['usercs']=$this->assignment_user_pos_model->get_user(" where id not in (select user_id from app.t_trx_assignment_cs where assignment_code='".$assignment_code."' and status !='-5' ) and port_id='".$get_detail->port_id."' and status=1 and user_group_id=6 order by username asc")->result();

        // cek data user CS yang di tammpilkan hanya user selain user id yg sudah ada dan di pelabuhan yang di pilih saja
        $data['userptcstc']=$this->assignment_user_pos_model->get_user(" where id not in (select user_id from app.t_trx_assignment_ptc_stc where assignment_code='".$assignment_code."' and status !='-5') and port_id='".$get_detail->port_id."' and status=1 and (user_group_id=2 or user_group_id=10) order by username asc")->result();

        // cek data user vertifikator yang di tammpilkan hanya user selain user id yg sudah ada dan di pelabuhan yang di pilih saja
        $data['uservertifikator']=$this->assignment_user_pos_model->get_user(" where id not in (select user_id from app.t_trx_assignment_verifier where assignment_code='".$assignment_code."' and status !='-5' ) and port_id='".$get_detail->port_id."' and status=1 and user_group_id=35  order by username asc")->result();

        // cek data user vertifikator yang di tammpilkan hanya user selain user id yg sudah ada dan di pelabuhan yang di pilih saja
        $data['userComand']=$this->assignment_user_pos_model->get_user(" where id not in (select user_id from app.t_trx_assignment_command_center where assignment_code='".$assignment_code."' and status !='-5' ) and port_id='".$get_detail->port_id."' and status=1 and user_group_id=42  order by username asc")->result();

        $rowdeviceComandCenter=$this->assignment_user_pos_model->select_data("app.t_mtr_device_terminal","where port_id='".$get_detail->port_id."' and status=1 and terminal_type = 21 ")->result();
        
        $detailUserComand=$this->assignment_user_pos_model->detailUserComand($assignment_code, " and d.user_group_id=42 and a.status not in (-5) ")->result();
        // print_r( $detailUserComand);exit;

        $selectedDeviceComand=[];
        $deviceComandCenter=array();
        $selectedUserComand=array();
        $userComand=array();
        $comad_id=array();
        $userComandCenter=array();

        foreach ($rowdeviceComandCenter as $key => $value) {
            $value->full_name=strtoupper($value->terminal_name);
            $deviceComandCenter[]=$value;
        }

        if(!empty($detailUserComand) ) // pencegahan error 
        {
            foreach ($detailUserComand as $key => $value) {
                $code_enc=$value->assignment_code."_".$this->enc->encode($value->user_id);
                $value->shift_code_id=site_url($this->_module."/action_delete_user/{$code_enc}") ;

                $userComand[]=$value;
                $comad_id[]=$value->user_id;
                // $userComandCenter= $this->assignment_user_pos_model->getDropdown(" where id not in (select user_id from app.t_trx_assignment_verifier where assignment_code='".$assignment_code."' and status !='-5' ) and port_id='".$get_detail->port_id."' and status=1 and user_group_id=42  order by username asc");

                $selectedDeviceComand[$value->user_id]=$this->assignment_user_pos_model->detailDeviceComand($assignment_code,$value->shift_code)->result();
        
            }
        }

        if(!empty($comad_id) ) // pencegahan error 
        {
             $userComandCenter = $this->assignment_user_pos_model->getDropdown(" where id not in (select user_id from app.t_trx_assignment_verifier where assignment_code='".$assignment_code."'  and status !='-5' ) and a.id not in (".implode(",",$comad_id).") and port_id='".$get_detail->port_id."' and status=1 and user_group_id=42  order by username asc");
        }

        $selectedUserComand = array_filter(array_map(function($key) use($detailUserComand){
            foreach ($detailUserComand as $keyM => $valueM) {
                if($valueM->user_id == $key)
                {
                    return $key;
                }
            }
        }, array_keys($userComandCenter)), function($x){
            return $x !="";
        });

        $data['userComandCenter']= array_diff($userComandCenter,array(""=>"Pilih"));
        
        $data['deviceComandCenter']= $deviceComandCenter;
        $data['userComand']= $userComand;
        $data['comandId']= $comad_id;

        $data['selectedUserComand']= $selectedUserComand;
        $data['selectedDeviceComand']= $selectedDeviceComand;

        $this->load->view($this->_module.'/edit',$data);   
    }


    public function action_edit_25112021()
    {
        $assignment_code=$this->enc->decode($this->input->post('assignment'));
        $userspv=$this->enc->decode($this->input->post('userspv'));
        $username=$this->input->post('username2');
        $usercs=$this->input->post('usernamecs2');
        $userptcstc=$this->input->post('userptcstc2');
        $uservertifikator=$this->input->post('uservertifikator2');

            

        // $list_user=$this->input->post('list_user[]');
        // $list_usercs=$this->input->post('list_usercs[]');



        $this->form_validation->set_rules("assignment","Kode penugasan","required");

        $this->form_validation->set_message('required','%s harus diisi!');


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/assignment_user_pos/action_edit';
        $logMethod   = 'EDIT';    


        $data_update=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata("Username"),
        );


        // check apakah sudah tutup shift, jika sudah tutup shift maka tidak bisa di edit\
        $check_assignment_pos=$this->assignment_user_pos_model->select_data(" app.t_trx_assignment_user_pos", " where status=2 and assignment_code='{$assignment_code}' ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());

            $logParam    = json_encode(array("data masih ada yang kosong"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;
        }
        else if ($check_assignment_pos->num_rows()>0)
        {
            echo $res=json_api(0, " Gagal, Karena sudah tutup shift"); 
            $logParam    = json_encode(array("Gagal edit"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;

        }
        else
        {

            // mengambil data berdasarkan assignment_code
            $data_assignment=$this->assignment_user_pos_model->select_data($this->_table," where assignment_code='".$assignment_code."'")->row();

            // mengambil data cs berdasarkan assignment_code
            $data_assign_cs=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_cs"," where assignment_code='".$assignment_code."'")->row();
            
            $user_arr=array();
            $error_user=array();
            $name_err=array();

            $usercs_arr=array();
            $error_usercs=array();
            $namecs_err=array();

            $userptcstc_arr=array();
            $error_userptcstc=array();
            $nameptcstc_err=array();

            $uservertifikator_arr=array();
            $error_uservertifikator=array();
            $namevertifikator_err=array();


            if(!empty($username)) // pencegahan error jika username tidak di tambahkan
            {
                // mendecode id username dan mendapatkan data insert
                foreach(explode(",",$username) as $key=>$value)
                {
                    $user_id=$this->enc->decode($value);
                    $data_assignment_pos=array(
                        'user_id'=>$user_id,
                        'team_code'=>$data_assignment->team_code,
                        'port_id'=>$data_assignment->port_id,
                        'shift_id'=>$data_assignment->shift_id,
                        'status'=>1,
                        'assignment_code'=>$data_assignment->assignment_code,
                        'assignment_date'=>$data_assignment->assignment_date,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata("username"),
                        );

                    $user_arr[]=$data_assignment_pos;


                    // check apakah masih dalam opening balance di tanggal yang sama
                    $check_op=$this->assignment_user_pos_model->select_data("app.t_trx_opening_balance"," where status=1 and user_id=".$user_id." and trx_date='".$data_assignment->assignment_date."'");

                    $data_err_name=$this->assignment_user_pos_model->get_user(" where a.id=".$user_id)->row();

                    if($check_op->num_rows()>0)
                    {
                        $error_user[]=1;
                        $name_err[]=$data_err_name->username;
                    }
                }

                // kondisi jika masih ada data yang opening balance langsung 
                if(array_sum($error_user)>0)
                {
                    echo $res=json_api(0," User ".implode(", ", $name_err)." dalam opening balance");
                    $logParam    = json_encode($user_arr);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }

            }

            if(!empty($usercs)) // pencegahan error jika username tidak di tambahkan
            {

                // mengambil data dan mendekode id username CS
                
                foreach (explode(",",$usercs) as $key => $value) {
                    $user_id=$this->enc->decode($value);
                    $data_assignment_cs=array(
                        'user_id'=>$user_id,
                        'team_code'=>$data_assign_cs->team_code,
                        'assignment_code'=>$data_assign_cs->assignment_code,
                        'port_id'=>$data_assign_cs->port_id,
                        'assignment_date'=>$data_assign_cs->assignment_date,
                        'shift_code'=>$data_assign_cs->shift_code,
                        'shift_id'=>$data_assign_cs->shift_id,
                        'status'=>1,
                        'created_on'=>date('Y-m-d'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    $usercs_arr[]=$data_assignment_cs;

                    // check apakah masih dalam opening balance di tanggal yang sama
                    $check_cs=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_cs"," where status=1 and user_id=".$user_id." and assignment_date='".$data_assignment->assignment_date."'");

                    $data_err_namecs=$this->assignment_user_pos_model->get_user(" where a.id=".$user_id)->row();

                    if($check_cs->num_rows()>0)
                    {
                        $error_usercs[]=1;
                        $namecs_err[]=$data_err_namecs->username;
                    }

                }
                // kondisi jika masih ada data yang opening balance langsung 
                if(array_sum($error_usercs)>0)
                {
                    echo $res=json_api(0," User CS ".implode(", ", $namecs_err)." dalam opening balance");
                    $logParam    = json_encode($usercs_arr);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }

            }

            if(!empty($userptcstc)) // pencegahan error jika username tidak di tambahkan
            {

                // mengambil data dan mendekode id username ptcstc                
                foreach (explode(",",$userptcstc) as $key => $value) {
                    $user_id=$this->enc->decode($value);
                    $data_assignment_ptcstc=array(
                        'user_id'=>$user_id,
                        'team_code'=>$data_assign_cs->team_code,
                        'assignment_code'=>$data_assign_cs->assignment_code,
                        'port_id'=>$data_assign_cs->port_id,
                        'assignment_date'=>$data_assign_cs->assignment_date,
                        'shift_code'=>$data_assign_cs->shift_code,
                        'shift_id'=>$data_assign_cs->shift_id,
                        'status'=>1,
                        'created_on'=>date('Y-m-d'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    $userptcstc_arr[]=$data_assignment_ptcstc;

                    // check apakah masih dalam opening balance di tanggal yang sama
                    $check_ptcstc=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_ptc_stc"," where status=1 and user_id=".$user_id." and assignment_date='".$data_assignment->assignment_date."'");

                    $data_err_nameptcstc=$this->assignment_user_pos_model->get_user(" where a.id=".$user_id)->row();

                    if($check_ptcstc->num_rows()>0)
                    {
                        $error_userptcstc[]=1;
                        $nameptcstc_err[]=$data_err_nameptcstc->username;
                    }                    
                }

                // kondisi jika masih ada data yang opening balance langsung 
                if(array_sum($error_userptcstc)>0)
                {
                    echo $res=json_api(0," User PTC/ STC ".implode(", ", $nameptcstc_err)." dalam opening balance");
                    $logParam    = json_encode($userptcstc_arr);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }                
            }
            
            if(!empty($uservertifikator)) // pencegahan error jika username tidak di tambahkan
            {

                // mengambil data dan mendekode id username ptcstc                
                foreach (explode(",",$uservertifikator) as $key => $value) {
                    $user_id=$this->enc->decode($value);
                    $data_assignment_vertifikator=array(
                        'user_id'=>$user_id,
                        'team_code'=>$data_assign_cs->team_code,
                        'assignment_code'=>$data_assign_cs->assignment_code,
                        'port_id'=>$data_assign_cs->port_id,
                        'assignment_date'=>$data_assign_cs->assignment_date,
                        // 'shift_code'=>$data_assign_cs->shift_code,
                        'shift_id'=>$data_assign_cs->shift_id,
                        'status'=>1,
                        'created_on'=>date('Y-m-d'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    $uservertifikator_arr[]=$data_assignment_vertifikator;

                    // check apakah masih dalam opening balance di tanggal yang sama
                    $check_vertifikator=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_verifier"," where status=1 and user_id=".$user_id." and assignment_date='".$data_assignment->assignment_date."'");

                    $data_err_uservertifikator=$this->assignment_user_pos_model->get_user(" where a.id=".$user_id)->row();

                    if($check_vertifikator->num_rows()>0)
                    {
                        $error_uservertifikator[]=1;
                        $namevertifikator_err[]=$data_err_uservertifikator->username;
                    }                    
                }

                // kondisi jika masih ada data yang opening balance langsung 
                if(array_sum($error_uservertifikator)>0)
                {
                    echo $res=json_api(0," User Vertifikator ".implode(", ", $namevertifikator_err)." dalam opening balance");
                    $logParam    = json_encode($userptcstc_arr);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }                
            }            

            // print_r($uservertifikator_arr); exit;

            $data_user_pos=array();
            $data_user_cs=array();
            $data_user_ptcstc=array();

            // echo json_encode(print_r($usercs_arr)); exit;
            $this->db->trans_begin();
            if(!empty($username)) // menambahkan data assignmnet user pos jika ada datanya
            {
                // menambahkan user ke asignment user pos
                $this->assignment_user_pos_model->insert_batch_data($this->_table,$user_arr);

                // insert ke opening balance
                $user_ob=array();

                foreach($user_arr as $key=>$value)
                {
                    $user_ob[]=$value['user_id'];

                    $data=array(
                        'trx_date'=>$data_assignment->assignment_date,
                        'shift_id'=>$data_assignment->shift_id,
                        'user_id'=>$value['user_id'],
                        'ob_code'=>$this->createCodeOpening($data_assignment->port_id),
                        'assignment_code'=>$data_assignment->assignment_code,
                        'status'=>1,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata('username'),
                    );

                    $data_user_pos[]=$data;

                    $this->assignment_user_pos_model->insert_data("app.t_trx_opening_balance",$data);
                }

            }

            if(!empty($usercs))
            {

                foreach ($usercs_arr as $key => $value) {
                    $insert_cs=array(
                        'user_id'=>$value['user_id'],
                        'team_code'=>$value['team_code'],
                        'assignment_code'=>$value['assignment_code'],
                        'port_id'=>$value['port_id'],
                        'assignment_date'=>$value['assignment_date'],
                        'shift_code'=>$this->createCodeCs($value['port_id']),
                        'shift_id'=>$value['shift_id'],
                        'status'=>1,
                        'created_on'=>date('Y-m-d H:i:s'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    $data_user_cs[]=$insert_cs;
                    
                    // menambahkan user ke asignment cs
                    $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_cs",$insert_cs); 
                }   
            }

            if(!empty($userptcstc))
            {

                foreach ($userptcstc_arr as $key => $value) {
                    $insert_ptcstc=array(
                        'user_id'=>$value['user_id'],
                        'team_code'=>$value['team_code'],
                        'assignment_code'=>$value['assignment_code'],
                        'port_id'=>$value['port_id'],
                        'assignment_date'=>$value['assignment_date'],
                        'shift_code'=>$this->createCodeptcstc($value['port_id']),
                        'shift_id'=>$value['shift_id'],
                        'status'=>1,
                        'created_on'=>date('Y-m-d H:i:s'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    // menambahkan user ke asignment cs

                    $data_user_ptcstc[]=$insert_ptcstc;                    
                    $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_ptc_stc",$insert_ptcstc); 
                }

                // print_r($insert_ptcstc); exit;   
            }  

            // echo $uservertifikator; exit;
            $data_user_vertifikator=array();            
            if(!empty($uservertifikator))
            {

                foreach ($uservertifikator_arr as $key => $value) {
                    $insert_vertifikator=array(
                        'user_id'=>$value['user_id'],
                        'team_code'=>$value['team_code'],
                        'assignment_code'=>$value['assignment_code'],
                        'port_id'=>$value['port_id'],
                        'assignment_date'=>$value['assignment_date'],
                        'shift_code'=>$this->createCodeVertifikator($value['port_id']),
                        'shift_id'=>$value['shift_id'],
                        'status'=>1,
                        'created_on'=>date('Y-m-d H:i:s'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    // menambahkan user ke asignment vertifikator

                    $data_user_vertifikator[]=$insert_vertifikator;                    
                    $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_verifier",$insert_vertifikator); 
                }

            }              


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

            $data=array($data_user_pos,$data_user_cs,$data_user_ptcstc, $data_user_vertifikator);

            $logParam    = json_encode($userptcstc_arr);
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);            
        }   
    }

    public function action_edit()
    {
        $assignment_code=$this->enc->decode($this->input->post('assignment'));
        $userspv=$this->enc->decode($this->input->post('userspv'));
        $username=$this->input->post('username2');
        $usercs=$this->input->post('usernamecs2');
        $userptcstc=$this->input->post('userptcstc2');
        $uservertifikator=$this->input->post('uservertifikator2');
        $userComandCenter=$this->input->post("userComandCenter");
        $userComandId=$this->input->post("userComandId");

        $this->form_validation->set_rules("assignment","Kode penugasan", "trim|required|callback_validate_decode_param", array('validate_decode_param' => 'Invalid kode penugasan'));

        $this->form_validation->set_message('required','%s harus diisi!');

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/assignment_user_pos/action_edit';
        $logMethod   = 'EDIT';    


        $data_update=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata("Username"),
        );

        // print_r($userComandCenter);exit;
        // check apakah sudah tutup shift, jika sudah tutup shift maka tidak bisa di edit\
        $check_assignment_pos=$this->assignment_user_pos_model->select_data(" app.t_trx_assignment_user_pos", " where status=2 and assignment_code='{$assignment_code}' ");
        // mengambil data berdasarkan assignment_code
        $data_assignment=$this->assignment_user_pos_model->select_data($this->_table," where assignment_code='".$assignment_code."'")->row();

        //tambah device comandcenter
        $getCodeId = array();
        $getTerminalCode = array();
        $arrTerminal = array();

        $getCode= "";

        if(!empty($userComandCenter)) // pencegahan error jika username tidak di tambahkan
        {
            foreach (explode(",",$userComandCenter) as $key => $value) {

                $valComandCenter[]=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$data_assignment->port_id."'and id='".$value."' and status=1 and user_group_id=42  ")->result();
            }

            foreach ($valComandCenter as $key => $value) {

                $deviceComandCenter[$value[0]->id]=trim($this->input->post("deviceComandCenter2_{$value[0]->id}"));
                
                foreach ($deviceComandCenter as $key2 => $value2) {
                    $explodedComandCenter = explode(',', $value2);
                }
                $getTerminalCode[$value[0]->id]= $explodedComandCenter;
                $arrTerminal[]= $explodedComandCenter;

            
            }
        }

        //update device comandcenter
        if(!empty($userComandId)) // pencegahan error jika username tidak di tambahkan
        {

            foreach (explode(",",$userComandId) as $key => $value) {

                $valComandId[]=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$data_assignment->port_id."'and id='".$value."' and status=1 and user_group_id=42  ")->result();
            }

            foreach ($valComandId as $key => $value) {

                $deviceComandId[$value[0]->id]=$this->input->post("deviceComandId_{$value[0]->id}");
                $shiftCodeComand[$value[0]->id]=$this->input->post("shiftCodeComand_{$value[0]->id}");
                
                foreach ($deviceComandId as $key2 => $value2) {
                    $explodedComandId = explode(',', $value2);
                }

                foreach ($shiftCodeComand as $key3 => $value3) {
                $explodedshiftCodeComand = explode(',', $value3);
            }
            
                $getCodeId[$value[0]->id]= $explodedComandId;
                $getshiftCodeComand[$value[0]->id]= $explodedshiftCodeComand;
                $arrTerminal[]= $explodedComandId;
            
            }
        }

        //check terminal agar tidak duplikasi
        $checkDevice = array();
        $arrNameDevice = [];

        for($i=0; $i<count($arrTerminal); $i++)
        {
            foreach($arrTerminal[$i] as $key => $value){
                $checkDevice[]  = $value;
            }
        }

        $arrCheckDevice = array_count_values($checkDevice);

        foreach($arrCheckDevice as $keyD => $valueD ){
            
            if ($valueD > 1){
                $getNameDevice= $this->assignment_user_pos_model->select_data("app.t_mtr_device_terminal "," where terminal_code='".$keyD."' ")->row();          
                $arrNameDevice[]=$getNameDevice->terminal_name;
            }

        }

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());

            $logParam    = json_encode(array(validation_errors()));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;
        }
        else if ($check_assignment_pos->num_rows()>0)
        {
            echo $res=json_api(0, " Gagal, Karena sudah tutup shift"); 
            $logParam    = json_encode(array("Gagal edit"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;
        }
        else if (!empty(count($arrNameDevice)))
        {
            echo $res=json_api(0, " Terdapat perangkat ".implode(", ",$arrNameDevice)." duplikat antara user comand center"); 
            $logParam    = json_encode(array("Gagal edit"));
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;
        }
        else
        {
            // mengambil data cs berdasarkan assignment_code
            $data_assign_cs=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_cs"," where assignment_code='".$assignment_code."'")->row();
            
            $user_arr=array();
            $error_user=array();
            $name_err=array();

            $usercs_arr=array();
            $error_usercs=array();
            $namecs_err=array();

            $userptcstc_arr=array();
            $error_userptcstc=array();
            $nameptcstc_err=array();

            $uservertifikator_arr=array();
            $error_uservertifikator=array();
            $namevertifikator_err=array();

            
            $insert_user_comand =array();
            $insert_device_comand=array();
            $val_terminal_code= array();

            $insert_device=array();


            if(!empty($username)) // pencegahan error jika username tidak di tambahkan
            {
                // mendecode id username dan mendapatkan data insert
                foreach(explode(",",$username) as $key=>$value)
                {
                    $user_id=$this->enc->decode($value);
                    $data_assignment_pos=array(
                        'user_id'=>$user_id,
                        'team_code'=>$data_assignment->team_code,
                        'port_id'=>$data_assignment->port_id,
                        'shift_id'=>$data_assignment->shift_id,
                        'status'=>1,
                        'assignment_code'=>$data_assignment->assignment_code,
                        'assignment_date'=>$data_assignment->assignment_date,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata("username"),
                        );

                    $user_arr[]=$data_assignment_pos;


                    // check apakah masih dalam opening balance di tanggal yang sama
                    $check_op=$this->assignment_user_pos_model->select_data("app.t_trx_opening_balance"," where status=1 and user_id=".$user_id." and trx_date='".$data_assignment->assignment_date."'");

                    // jika user melakukan closing balance, maka tidak bisa menambahkan user dengan asigment yang sama 
                    $check_op_close=$this->assignment_user_pos_model->select_data("app.t_trx_opening_balance"," where status !='-5' and user_id=".$user_id." 
                    and assignment_code='".$assignment_code."'");


                    $data_err_name=$this->assignment_user_pos_model->get_user(" where a.id=".$user_id)->row();

                    if($check_op->num_rows()>0)
                    {
                        $error_user[]=1;
                        $name_err[]=$data_err_name->username;
                    }

                    if($check_op_close->num_rows()>0)
                    {
                        $error_user[]=1;
                        $name_err[]=$data_err_name->username;
                    }                    
                }

                // kondisi jika masih ada data yang opening balance langsung 
                if(array_sum($error_user)>0)
                {
                    if($check_op->num_rows()>0)
                    {

                        echo $res=json_api(0," User ".implode(", ", $name_err)." dalam kedinasan ");
                    }
                    else
                    {
                        echo $res=json_api(0," User ".implode(", ", $name_err)." sudah tutup dinas di asignment yang sama ");
                    }
                    $logParam    = json_encode($user_arr);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }

            }

            if(!empty($usercs)) // pencegahan error jika username tidak di tambahkan
            {

                // mengambil data dan mendekode id username CS
                
                foreach (explode(",",$usercs) as $key => $value) {
                    $user_id=$this->enc->decode($value);
                    $data_assignment_cs=array(
                        'user_id'=>$user_id,
                        'team_code'=>$data_assign_cs->team_code,
                        'assignment_code'=>$data_assign_cs->assignment_code,
                        'port_id'=>$data_assign_cs->port_id,
                        'assignment_date'=>$data_assign_cs->assignment_date,
                        'shift_code'=>$data_assign_cs->shift_code,
                        'shift_id'=>$data_assign_cs->shift_id,
                        'status'=>1,
                        'created_on'=>date('Y-m-d'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    $usercs_arr[]=$data_assignment_cs;

                    // check apakah masih dalam opening balance di tanggal yang sama
                    $check_cs=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_cs"," where status=1 and user_id=".$user_id." and assignment_date='".$data_assignment->assignment_date."'");

                    // jika user melakukan closing balance, maka tidak bisa menambahkan user dengan asigment yang sama 
                    $check_cs_close=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_cs"," where status !='-5' and user_id=".$user_id." 
                    and assignment_code='".$assignment_code."'");

                    $data_err_namecs=$this->assignment_user_pos_model->get_user(" where a.id=".$user_id)->row();

                    if($check_cs->num_rows()>0)
                    {
                        $error_usercs[]=1;
                        $namecs_err[]=$data_err_namecs->username;
                    }

                    if($check_cs_close->num_rows()>0)
                    {
                        $error_usercs[]=1;
                        $namecs_err[]=$data_err_namecs->username;
                    }                    

                }
                // kondisi jika masih ada data yang opening balance langsung 
                if(array_sum($error_usercs)>0)
                {
                    if($check_cs->num_rows()>0)
                    {
                        echo $res=json_api(0," User CS ".implode(", ", $namecs_err)." dalam kedinasan");
                    }
                    else
                    {
                        echo $res=json_api(0," User CS ".implode(", ", $namecs_err)." sudah tutup dinas di asignment yang sama");
                    }
                    $logParam    = json_encode($usercs_arr);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }

            }

            if(!empty($userptcstc)) // pencegahan error jika username tidak di tambahkan
            {

                // mengambil data dan mendekode id username ptcstc                
                foreach (explode(",",$userptcstc) as $key => $value) {
                    $user_id=$this->enc->decode($value);
                    $data_assignment_ptcstc=array(
                        'user_id'=>$user_id,
                        'team_code'=>$data_assign_cs->team_code,
                        'assignment_code'=>$data_assign_cs->assignment_code,
                        'port_id'=>$data_assign_cs->port_id,
                        'assignment_date'=>$data_assign_cs->assignment_date,
                        'shift_code'=>$data_assign_cs->shift_code,
                        'shift_id'=>$data_assign_cs->shift_id,
                        'status'=>1,
                        'created_on'=>date('Y-m-d'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    $userptcstc_arr[]=$data_assignment_ptcstc;

                    // check apakah masih dalam opening balance di tanggal yang sama
                    $check_ptcstc=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_ptc_stc"," where status=1 and user_id=".$user_id." and assignment_date='".$data_assignment->assignment_date."'");
                   
                    // jika user melakukan closing balance, maka tidak bisa menambahkan user dengan asigment yang sama 
                    $check_ptcstc_close=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_ptc_stc"," where status !='-5' and user_id=".$user_id." 
                    and assignment_code='".$assignment_code."'");


                    $data_err_nameptcstc=$this->assignment_user_pos_model->get_user(" where a.id=".$user_id)->row();

                    if($check_ptcstc->num_rows()>0)
                    {
                        $error_userptcstc[]=1;
                        $nameptcstc_err[]=$data_err_nameptcstc->username;
                    }  
                    
                    if($check_ptcstc_close->num_rows()>0)
                    {
                        $error_userptcstc[]=1;
                        $nameptcstc_err[]=$data_err_nameptcstc->username;
                    }                      
                }

                // kondisi jika masih ada data yang opening balance langsung 
                if(array_sum($error_userptcstc)>0)
                {
                    if($check_ptcstc->num_rows()>0)
                    {
                        echo $res=json_api(0," User PTC/ STC ".implode(", ", $nameptcstc_err)." dalam Kedinasan");
                    }
                    else
                    {
                        echo $res=json_api(0," User PTC/ STC ".implode(", ", $nameptcstc_err)." sudah tutup dinas di asignment yang sama");
                    }
                    $logParam    = json_encode($userptcstc_arr);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }                
            }
            
            if(!empty($uservertifikator)) // pencegahan error jika username tidak di tambahkan
            {

                // mengambil data dan mendekode id username ptcstc                
                foreach (explode(",",$uservertifikator) as $key => $value) {
                    $user_id=$this->enc->decode($value);
                    $data_assignment_vertifikator=array(
                        'user_id'=>$user_id,
                        'team_code'=>$data_assign_cs->team_code,
                        'assignment_code'=>$data_assign_cs->assignment_code,
                        'port_id'=>$data_assign_cs->port_id,
                        'assignment_date'=>$data_assign_cs->assignment_date,
                        // 'shift_code'=>$data_assign_cs->shift_code,
                        'shift_id'=>$data_assign_cs->shift_id,
                        'status'=>1,
                        'created_on'=>date('Y-m-d'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    $uservertifikator_arr[]=$data_assignment_vertifikator;

                    // check apakah masih dalam opening balance di tanggal yang sama
                    $check_vertifikator=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_verifier"," where status=1 and user_id=".$user_id." and assignment_date='".$data_assignment->assignment_date."'");
                    
                    // jika user melakukan closing balance, maka tidak bisa menambahkan user dengan asigment yang sama 
                    $check_vertifikator_close=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_verifier"," where status !='-5' and user_id=".$user_id." 
                                                                                            and assignment_code='".$assignment_code."'");


                    $data_err_uservertifikator=$this->assignment_user_pos_model->get_user(" where a.id=".$user_id)->row();

                    if($check_vertifikator->num_rows()>0)
                    {
                        $error_uservertifikator[]=1;
                        $namevertifikator_err[]=$data_err_uservertifikator->username;
                    }

                    if($check_vertifikator_close->num_rows()>0)
                    {
                        $error_uservertifikator[]=1;
                        $namevertifikator_err[]="<b>".$data_err_uservertifikator->username."</b>";
                    }                                        
                }

                // kondisi jika masih ada data yang opening balance langsung 
                if(array_sum($error_uservertifikator)>0)
                {
                    if ($check_vertifikator->num_rows()>0)
                    {
                        echo $res=json_api(0," User Vertifikator ".implode(", ", $namevertifikator_err)." dalam kedinasan ");
                    }
                    else
                    {
                        echo $res=json_api(0," User Vertifikator ".implode(", ", $namevertifikator_err)." sudah tutup dinas di asignment yang sama");
                    }

                    $logParam    = json_encode($userptcstc_arr);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }                
            }      
            
            $data_user_pos=array();
            $data_user_cs=array();
            $data_user_ptcstc=array();

            // echo json_encode(print_r($usercs_arr)); exit;
            $this->db->trans_begin();

            if(!empty($username)) // menambahkan data assignmnet user pos jika ada datanya
            {
                // menambahkan user ke asignment user pos
                $this->assignment_user_pos_model->insert_batch_data($this->_table,$user_arr);

                // insert ke opening balance
                $user_ob=array();

                foreach($user_arr as $key=>$value)
                {
                    $user_ob[]=$value['user_id'];

                    $data=array(
                        'trx_date'=>$data_assignment->assignment_date,
                        'shift_id'=>$data_assignment->shift_id,
                        'user_id'=>$value['user_id'],
                        'ob_code'=>$this->createCodeOpening($data_assignment->port_id),
                        'assignment_code'=>$data_assignment->assignment_code,
                        'status'=>1,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata('username'),
                    );

                    $data_user_pos[]=$data;

                    $this->assignment_user_pos_model->insert_data("app.t_trx_opening_balance",$data);
                }

            }

            if(!empty($usercs))
            {

                foreach ($usercs_arr as $key => $value) {
                    $insert_cs=array(
                        'user_id'=>$value['user_id'],
                        'team_code'=>$value['team_code'],
                        'assignment_code'=>$value['assignment_code'],
                        'port_id'=>$value['port_id'],
                        'assignment_date'=>$value['assignment_date'],
                        'shift_code'=>$this->createCodeCs($value['port_id']),
                        'shift_id'=>$value['shift_id'],
                        'status'=>1,
                        'created_on'=>date('Y-m-d H:i:s'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    $data_user_cs[]=$insert_cs;
                    
                    // menambahkan user ke asignment cs
                    $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_cs",$insert_cs); 
                }   
            }

            if(!empty($userptcstc))
            {

                foreach ($userptcstc_arr as $key => $value) {
                    $insert_ptcstc=array(
                        'user_id'=>$value['user_id'],
                        'team_code'=>$value['team_code'],
                        'assignment_code'=>$value['assignment_code'],
                        'port_id'=>$value['port_id'],
                        'assignment_date'=>$value['assignment_date'],
                        'shift_code'=>$this->createCodeptcstc($value['port_id']),
                        'shift_id'=>$value['shift_id'],
                        'status'=>1,
                        'created_on'=>date('Y-m-d H:i:s'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    // menambahkan user ke asignment cs

                    $data_user_ptcstc[]=$insert_ptcstc;                    
                    $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_ptc_stc",$insert_ptcstc); 
                }

                // print_r($insert_ptcstc); exit;   
            }  

            // echo $uservertifikator; exit;
            $data_user_vertifikator=array();            
            if(!empty($uservertifikator))
            {

                foreach ($uservertifikator_arr as $key => $value) {
                    $insert_vertifikator=array(
                        'user_id'=>$value['user_id'],
                        'team_code'=>$value['team_code'],
                        'assignment_code'=>$value['assignment_code'],
                        'port_id'=>$value['port_id'],
                        'assignment_date'=>$value['assignment_date'],
                        'shift_code'=>$this->createCodeVertifikator($value['port_id']),
                        'shift_id'=>$value['shift_id'],
                        'status'=>1,
                        'created_on'=>date('Y-m-d H:i:s'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    // menambahkan user ke asignment vertifikator

                    $data_user_vertifikator[]=$insert_vertifikator;                    
                    $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_verifier",$insert_vertifikator); 
                }

            }
             
            if(!empty($userComandId) ) // pencegahan error jika username tidak di tambahkan
            {

                $softDeleteData=array(
                    "status"=>'-5',
                    "updated_on"=>date("Y-m-d H:i:s"),
                    "updated_by"=>$this->session->userdata("username")
                );
                $this->assignment_user_pos_model->update_data("app.t_mtr_pairing_device_command_center",$softDeleteData," assignment_code='".$data_assign_cs->assignment_code."' and status =1 ");

                $userCom = array_filter(array_map(function($x){
                    return $x;
                }, explode(",",$userComandId)), function($f){
                    return $f !="";
                });  
                
        
                // print_r( $getCodeId );exit;
                 foreach($userCom as $keyM => $valueM){
                    // $testing[]= $getCodeId[$valueM] ;
                     foreach($getCodeId[$valueM] as $keyM2=> $valueM2 ){
                        $val_terminal_code=$valueM2;
                         
                         $insert_device[]=array(
                         'terminal_code'=>$val_terminal_code,
                         'team_code'=>$data_assign_cs->team_code,
                         'assignment_code'=>$data_assign_cs->assignment_code,
                         'port_id'=>$data_assign_cs->port_id,
                         'assignment_date'=>$data_assign_cs->assignment_date,
                         'shift_code'=>$getshiftCodeComand[$valueM][0],
                         'shift_id'=>$data_assign_cs->shift_id,
                         'status'=>1,
                         'created_on'=>date('Y-m-d'),
                         'created_by'=>$this->session->userdata("username"),
        
                         );
                     }
        
                 }
                //  print_r($insert_device);exit;  

                $this->assignment_user_pos_model->insert_batch_data("app.t_mtr_pairing_device_command_center",$insert_device);

                //  print_r($insert_device);exit;  
            }

            if(!empty($userComandCenter) ) // pencegahan error jika username tidak di tambahkan
            {
                $userComand = array_filter(array_map(function($x){
                    return $x;
                }, explode(",",$userComandCenter)), function($f){
                    return $f !="";
                });  
                
                foreach ($userComand as $key => $value) {     
                    $getShiftCode= $this->createCodeComandCenter($data_assign_cs->port_id);
                    $insert_user_comand= array(
                        'user_id'=>$value,
                        'team_code'=>$data_assign_cs->team_code,
                        'assignment_code'=>$data_assign_cs->assignment_code,
                        'port_id'=>$data_assign_cs->port_id,
                        'assignment_date'=>$data_assign_cs->assignment_date,
                        'shift_code'=>$getShiftCode,
                        'shift_id'=>$data_assign_cs->shift_id,
                        'status'=>1,
                        'created_on'=>date('Y-m-d'),
                        'created_by'=>$this->session->userdata("username"),
                    );

                    $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_command_center",$insert_user_comand);


                    foreach($getTerminalCode[$value] as $key2=> $value2 ){
                        
                        $val_terminal_code=$value2;
                        
                        $insert_device_comand[]=array(
                        'terminal_code'=>$val_terminal_code,
                        'team_code'=>$data_assign_cs->team_code,
                        'assignment_code'=>$data_assign_cs->assignment_code,
                        'port_id'=>$data_assign_cs->port_id,
                        'assignment_date'=>$data_assign_cs->assignment_date,
                        'shift_code'=>$getShiftCode,
                        'shift_id'=>$data_assign_cs->shift_id,
                        'status'=>1,
                        'created_on'=>date('Y-m-d'),
                        'created_by'=>$this->session->userdata("username"),

                        );
                    }

                }
        
            $this->assignment_user_pos_model->insert_batch_data("app.t_mtr_pairing_device_command_center",$insert_device_comand);
 
            }

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

            $data=array($data_user_pos,$data_user_cs,$data_user_ptcstc, $data_user_vertifikator);

            $logParam    = json_encode($userptcstc_arr);
            $logResponse = $res;
            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);            
        }   
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

        $assignment_code = $this->enc->decode($id);

        // ambil data ob_code dari opening balance
        $get_ob=$this->assignment_user_pos_model->select_data(" app.t_trx_opening_balance"," where assignment_code='{$assignment_code}'  ")->result();

        // check apakah vm sudah melakukan opening balance
        $check_vm_opening=$this->assignment_user_pos_model->select_data("app.t_trx_opening_balance_vm"," where assignment_code='{$assignment_code}'  ");

        // cehecking apakah user pos ini sudah melakukan transaksi
        $check_error[]=0;
        foreach ($get_ob as $key => $value)
        {
            $check_sell=$this->assignment_user_pos_model->select_data(" app.t_trx_sell", " where ob_code='{$value->ob_code}' ");
            $checkCheckin=$this->assignment_user_pos_model->countData("app.t_trx_check_in", " where created_by='{$ob_code}' ");
            $checkCheckinVehicle=$this->assignment_user_pos_model->countData("app.t_trx_check_in_vehicle", " where created_by='{$ob_code}' ");

            if($check_sell->num_rows()>0)
            {
                $check_error[]=1;
            }    

            if($checkCheckin->num_rows()>0)
            {
                $check_error[]=1;
            }    

            if($checkCheckinVehicle->num_rows()>0)
            {
                $check_error[]=1;
            }    
        }


        if(array_sum($check_error)>0)
        {
            echo $res=json_api(0, 'Gagal, user POS sudah melakukan transaksi');
        }
        else if($check_vm_opening->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal, VM sudah melakukan opening balance');   
        }
        else
        {

            $this->db->trans_begin();

            $this->assignment_user_pos_model->update_data("app.t_trx_assignment_user_pos",$data,"assignment_code='".$assignment_code."'");
            $this->assignment_user_pos_model->update_data("app.t_trx_assignment_cs",$data,"assignment_code='".$assignment_code."'");
            $this->assignment_user_pos_model->update_data("app.t_trx_assignment_ptc_stc",$data,"assignment_code='".$assignment_code."'");
            $this->assignment_user_pos_model->update_data("app.t_trx_opening_balance",$data,"assignment_code='".$assignment_code."'");
            $this->assignment_user_pos_model->update_data("app.t_trx_assignment_regu",$data,"assignment_code='".$assignment_code."'"); 
            $this->assignment_user_pos_model->update_data("app.t_trx_assignment_verifier",$data,"assignment_code='".$assignment_code."'");             
            
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

        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/assignment_user_pos/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete_user($param)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $explode=explode("_", $param);

        $assignment_code=$explode[0];
        $user_id=$this->enc->decode($explode[1]);

        
        $check_user_pos=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_user_pos"," where assignment_code='{$assignment_code}' and user_id={$user_id} and status !='-5' ");
        // print_r($check_user_pos->row()); exit;

        $check_user_cs=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_cs"," where assignment_code='{$assignment_code}' and user_id={$user_id} and status=1 ");

        $check_user_ptcstc=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_ptc_stc"," where assignment_code='{$assignment_code}' and user_id={$user_id} and status=1 ");
        $check_user_verifikator=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_verifier"," where assignment_code='{$assignment_code}' and user_id={$user_id} and status=1 ");
       
        $check_user_comand_center=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_command_center"," where assignment_code='{$assignment_code}' and user_id={$user_id} and status=1 ");

        $check_close_shift=$this->assignment_user_pos_model->select_data("app.t_trx_assignment_regu", "  where assignment_code='{$assignment_code}' and status=2 " );

        if($check_close_shift->num_rows()>0)
        {
            $data=array(
                'status'=>-5,
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );
        
            echo $res=json_api(0, 'User sudah tutup shift');

            /* Fungsi Create Log */
            $createdBy   = $this->session->userdata('username');
            $logUrl      = site_url().'shift_management/assignment_user_pos/action_delete_user';
            $logMethod   = 'DELETE';
            $logParam    = json_encode($data);
            $logResponse = $res;

            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);              

        }
        else
        {
            if ($check_user_pos->num_rows()>0) 
            {
                $get_ob=$this->assignment_user_pos_model->select_data("app.t_trx_opening_balance"," where assignment_code='{$assignment_code}' and user_id={$user_id} and status !='-5' ")->row();
                $this->delete_user_pos($get_ob->ob_code, $assignment_code, $user_id);
            }
            else if ($check_user_cs->num_rows()>0) 
            {
                $this->delete_user_cs($check_user_cs->row()->shift_code);
            }
            else if ($check_user_ptcstc->num_rows()>0) 
            {
                $this->delete_user_ptcstc($check_user_ptcstc->row()->shift_code);
            } 
            else if($check_user_verifikator->num_rows()>0)
            {
                $this->delete_user_vertifikator($check_user_verifikator->row()->shift_code);
            }
            else if($check_user_comand_center->num_rows()>0)
            {
                $this->delete_user_comand_center($check_user_comand_center->row()->shift_code);
            }    

        }
             

    }

    public function delete_user_pos($ob_code,$assignment_code,$user_id)
    {
        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );


        $check=$this->assignment_user_pos_model->select_data("app.t_trx_sell", " where ob_code='{$ob_code}' ");
        $checkCheckin=$this->assignment_user_pos_model->countData("app.t_trx_check_in", " where created_by='{$ob_code}' ");
        $checkCheckinVehicle=$this->assignment_user_pos_model->countData("app.t_trx_check_in_vehicle", " where created_by='{$ob_code}' ");

        // echo $ob_code; exit;

        if($check->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal, user POS sudah melakukan transaksi');
        }
        else if($checkCheckin->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal, user POS sudah melakukan transaksi Cetak Boarding Pass');
        }
        else if($checkCheckinVehicle->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal, user POS sudah melakukan transaksi Cetak Boarding Pass');
        }        
        else
        {   

            $this->db->trans_begin();

            $this->assignment_user_pos_model->update_data("app.t_trx_assignment_user_pos",$data,"assignment_code='".$assignment_code."' and user_id={$user_id} ");
            $this->assignment_user_pos_model->update_data("app.t_trx_opening_balance",$data,"ob_code='".$ob_code."'");
            
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

        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/assignment_user_pos/delete_user_pos';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);          

    }    

    public function delete_user_cs($shift_code)
    {
        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $this->db->trans_begin();

        // print_r($data);exit;

        $this->assignment_user_pos_model->update_data("app.t_trx_assignment_cs",$data," shift_code='".$shift_code."' ");
        
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
        $logUrl      = site_url().'shift_management/assignment_user_pos/delete_user_cs';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);          

    }  

    public function delete_user_ptcstc($shift_code)
    {
        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $this->db->trans_begin();

        $this->assignment_user_pos_model->update_data("app.t_trx_assignment_ptc_stc",$data," shift_code='".$shift_code."' ");
        
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
        $logUrl      = site_url().'shift_management/assignment_user_pos/delete_user_ptcstc';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);          

    }

    public function delete_user_vertifikator($shift_code)
    {
        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $this->db->trans_begin();

        $this->assignment_user_pos_model->update_data("app.t_trx_assignment_verifier",$data," shift_code='".$shift_code."' ");
        
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
        $logUrl      = site_url().'shift_management/assignment_user_pos/delete_user_vertifikator';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);          

    }    
     
    public function delete_user_comand_center($shift_code)
    {
        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );
        // print_r($shift_code);exit;
        $this->db->trans_begin();
        
        $this->assignment_user_pos_model->update_data("app.t_trx_assignment_command_center",$data," shift_code='".$shift_code."' and status =1 ");

        $this->assignment_user_pos_model->update_data("app.t_mtr_pairing_device_command_center",$data," shift_code='".$shift_code."' and status =1 ");
        
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
        $logUrl      = site_url().'shift_management/assignment_user_pos/delete_user_vertifikator';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);          

    }
    
    function get_data()
    {
        $port_id=$this->input->post("port");
        $port_decode=$this->enc->decode($port_id);
        $kode=$port_decode;
        // echo "halo"; exit;
        // empty($port_decode)?$kode=0:$kode=$port_decode;
        if(empty($port_decode)) // jika port id nya tidak dipilih
        {
            $data['regu']=array(""=>"Pilih");
            $data['spv']=array(""=>"Pilih");
            $data['user']=[];
            $data['usercs']=[];
            $data['userptcstc']=[];
            $data['userVertifikator']=[];
            $data['userComandCenter']=[];
            $data['shift']=array(""=>"Pilih");
            $data["tokenHash"]=$this->security->get_csrf_hash();
            $data["csrfName"]=$this->security->get_csrf_token_name();
            echo json_encode($data);
            exit;
        }

        $row=$this->assignment_user_pos_model->select_data("core.t_mtr_team","where port_id='".$kode."' and status=1")->result();

        $regu=array();
        $spv=array();
        $user=array();
        $usercs=array();
        $userptcstc=array();
        $userVertifikator=array();
        $userComandCenter=array();
        $shift=array();
        $data=array();

        foreach ($row as $key => $value) {
            $value->team_code=$this->enc->encode($value->team_code); // decript code
            $regu[]=$value;
        }


        // hardcord user group 3 (spv pos)
        $rowspv=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=3 ")->result();

        // hardcord user group 4 (operator pos)
        $rowuser=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=4 ")->result();

        // hardcord user group 6 (CS)
        $rowusercs=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=6 ")->result();

        // hardcord user group 2 (stc) usergroup 10 (ptc)
        $rowuserptcstc=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and ( user_group_id=2 or  user_group_id=10) ")->result();

        // hardcord user group 35 vertivicator
        $rowuserVertifikator=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=35  ")->result();

        // hardcord user group 42 comandcenter
        $rowuserComandCenter=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=42  ")->result();


        // Check shift di shift time
        $check_shift_time=$this->assignment_user_pos_model->get_shift($port_decode)->result();

        foreach ($rowspv as $key => $value) {
            $value->id=$this->enc->encode($value->id); // decript id user
            $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
            $spv[]=$value;
        }

        foreach ($rowuser as $key => $value) {
            $value->id=$this->enc->encode($value->id); // decript id user
            $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
            $user[]=$value;
        }

        foreach ($rowusercs as $key => $value) {
            $value->id=$this->enc->encode($value->id); // decript id user
            $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
            $usercs[]=$value;
        }

        foreach ($rowuserptcstc as $key => $value) {
            $value->id=$this->enc->encode($value->id); // decript id user
            $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
            $userptcstc[]=$value;
        }

        foreach ($rowuserVertifikator as $key => $value) {
            $value->id=$this->enc->encode($value->id); // decript id user
            $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
            $userVertifikator[]=$value;
        } 
        
        foreach ($rowuserComandCenter as $key => $value) {
            $value->id=$value->id;
            $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
            $userComandCenter[]=$value;
        }

        if(count($check_shift_time)>0)
        {
            foreach($check_shift_time as $key=>$value)
            {
                $value->shift_id=$this->enc->encode($value->shift_id);
                $value->shift_name=strtoupper($value->shift_name);
                $shift[]=$value;

                unset($value->id);
                unset($value->port_id);
            }
        }

        $data['regu']=$regu;
        $data['spv']=$spv;
        $data['user']=$user;
        $data['usercs']=$usercs;
        $data['userptcstc']=$userptcstc;
        $data['userVertifikator']=$userVertifikator;
        $data['userComandCenter']=$userComandCenter;
        $data['shift']=$shift;
        $data["tokenHash"]=$this->security->get_csrf_hash();
        $data["csrfName"]=$this->security->get_csrf_token_name();

        echo json_encode($data);

        // echo json_encode("tes data");
    }

    function get_device_comand_center()
    {
        $port_id=$this->input->post("port");
        $port_decode=$this->enc->decode($port_id);

        empty($port_decode)?$kode=0:$kode=$port_decode;

        $deviceComandCenter=array();
        $userComandCenter=array();

        $rowdeviceComandCenter=$this->assignment_user_pos_model->select_data("app.t_mtr_device_terminal","where port_id='".$kode."' and status=1 and terminal_type = 21 ")->result();
        // hardcord user group 42 comandcenter
        $rowuserComandCenter=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=42  ")->result();

        foreach ($rowdeviceComandCenter as $key => $value) {
            $value->id=$value->terminal_code;
            $value->full_name=strtoupper($value->terminal_name);
            $deviceComandCenter[]=$value;
        }

        foreach ($rowuserComandCenter as $key => $value) {
            $value->id=$value->id;
            $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
            $userComandCenter[]=$value;
        }

        $data['deviceComandCenter']=$deviceComandCenter;
        $data['userComandCenter']=$userComandCenter;
        $data["tokenHash"]=$this->security->get_csrf_hash();
        $data["csrfName"]=$this->security->get_csrf_token_name();

        echo json_encode($data);

        // echo json_encode("tes data");
    }

    function get_user()
    {
        
        $port_id=$this->input->post("port");
        $port_decode=$this->enc->decode($port_id);
        empty($port_decode)?$kode=0:$kode=$port_decode;

        // mendapatkan user pos berdasarkan user group 4 (pos) hardcord
        $row=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=4 ")->result();

        $data=array();
        foreach ($row as $key => $value) {
            $value->id=$this->enc->encode($value->id); // decript id user
            $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
            $data[]=$value;
        }

        echo json_encode($data);
    }

    function get_usercs()
    {

        // hard cord supervisor pos yang user group 3
        
        $port_id=$this->input->post("port");
        $port_decode=$this->enc->decode($port_id);
        empty($port_decode)?$kode=0:$kode=$port_decode;
        $row=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where port_id='".$kode."' and status=1 and user_group_id=6 ")->result();

        $data=array();
        foreach ($row as $key => $value) {
            $value->id=$this->enc->encode($value->id); // decript id user
            $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
            $data[]=$value;
        }

        echo json_encode($data);
    }

    function createCode($port)
    {
        $front_code="A".$port."".date('ymd');

        $len=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_trx_assignment_user_pos where left(assignment_code,".$len.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (assignment_code) as max_code from app.t_trx_assignment_user_pos where left(assignment_code,".$len.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $len, 3);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%03s", $noUrut);
            return $kode;
        }
    }


    function createCodeCs($port)
    {
        $front_code="S".$port."".date('ymd');

        $len=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_trx_assignment_cs where left(shift_code,".$len.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (shift_code) as max_code from app.t_trx_assignment_cs where left(shift_code,".$len.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $len, 3);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%03s", $noUrut);
            return $kode;
        }
    }

    function createCodeptcstc($port)
    {
        $front_code="C".$port."".date('ymd');

        $len=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_trx_assignment_ptc_stc where left(shift_code,".$len.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (shift_code) as max_code from app.t_trx_assignment_ptc_stc where left(shift_code,".$len.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $len, 3);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%03s", $noUrut);
            return $kode;
        }
    }

    function createCodeVertifikator($port)
    {
        $front_code="T".$port."".date('ymd');

        $len=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_trx_assignment_verifier where left(shift_code,".$len.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (shift_code) as max_code from app.t_trx_assignment_verifier where left(shift_code,".$len.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $len, 3);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%03s", $noUrut);
            return $kode;
        }
    }
    
    function createCodeComandCenter($port)
    {
        $front_code="T".$port."".date('ymd');

        $len=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_trx_assignment_command_center where left(shift_code,".$len.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (shift_code) as max_code from app.t_trx_assignment_command_center where left(shift_code,".$len.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $len, 3);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%03s", $noUrut);
            return $kode;
        }
    } 

    function createCodeDeviceComand($port)
    {
        $front_code="T".$port."".date('ymd');

        $len=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_pairing_device_command_center where left(shift_code,".$len.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (shift_code) as max_code from app.t_mtr_pairing_device_command_center where left(shift_code,".$len.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $len, 3);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%03s", $noUrut);
            return $kode;
        }
    } 

    // function createCode($port)
    // {
    //     $front_code="T".$port."".date('ymd');

    //     $len=strlen($front_code);

    //     $chekCode=$this->db->query("select * from app.t_trx_assignment_verifier where left(shift_code,".$len.")='".$front_code."' ")->num_rows();

    //     if($chekCode<1)
    //     {
    //         $shelterCode=$front_code."001";
    //         return $shelterCode;
    //     }
    //     else
    //     {
    //         $max=$this->db->query("select max (shift_code) as max_code from app.t_trx_assignment_verifier where left(shift_code,".$len.")='".$front_code."' ")->row();
    //         $kode=$max->max_code;
    //         $noUrut = (int) substr($kode, $len, 3);
    //         $noUrut++;
    //         $char = $front_code;
    //         $kode = $char . sprintf("%03s", $noUrut);
    //         return $kode;
    //     }
    // } 

    function createCodeOpening($port)
    {
        $front_code="O".$port."".date('ymd');

        $len=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_trx_opening_balance where left(ob_code,".$len.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (ob_code) as max_code from app.t_trx_opening_balance where left(ob_code,".$len.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, 8, 3);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%03s", $noUrut);
            return $kode;
        }
    }

    public function identity_app()
    {
        $data=$this->assignment_user_pos_model->select_data("app.t_mtr_identity_app","")->row();

        return $data->port_id;

    }

    public function get_user_detail()
    {

        $assignment_code=$this->enc->decode($this->input->post('assignment'));
        $port_id=$this->enc->decode($this->input->post('port'));
        // menampilkan data user pos

        $data=array();
        $rows1=array();
        $rows2=array();
        $rows3=array();
        $rows4=array();
        $rows5=array();
        $rows6=array();
        $rows7=array();
        $rows8=array();

        $rows9=array();
        $rows10=array();

        if(!empty($assignment_code) and  !empty($port_id) )
        {
            $user_pos=$this->assignment_user_pos_model->detail($assignment_code, " and d.user_group_id=4 and a.status not in (-5) ")->result();
            

            // print_r($user_pos); exit;

            // setting tampilan data
            if(!empty($user_pos))
            {
                foreach ($user_pos as $key => $value) {

                    $get_ob=$this->assignment_user_pos_model->select_data("app.t_trx_opening_balance", " where assignment_code='{$value->assignment_code}' and user_id={$value->user_id} and status !='-5' ")->row();

                    // echo ($get_ob->ob_code); exit;
                    $check_sell=$this->assignment_user_pos_model->select_data("app.t_trx_sell", " where ob_code='{$get_ob->ob_code}' ");
                    // print_r($get_ob); exit;

                    $check_checkin=$this->assignment_user_pos_model->checkTransactionCheckin($get_ob->ob_code)->result(); // checkin apakah dia sudah melakukan pembayaran


                    $code_enc=$value->assignment_code."_".$this->enc->encode($value->user_id);
                    $close_url=site_url($this->_module."/action_delete_user/{$code_enc}");

                    $value->actions="";

                    // validasi yang boleh di hapus
                    if($check_sell->num_rows()<1 and count((array)$check_checkin)<1 and $value->assignment_date>=date('Y-m-d'))
                    {
                        if($value->status ==1 )
                        {
                            $value->actions .='<a class="btn btn-xs btn-danger" onclick="confirmDeletUser(\'Apakah anda yakin ingin menghapus user ini dari assignmnet ?\', \''.$close_url.'\')" title="Hapus" href="#"> <i class="fa fa-trash-o"></i> </a>';
                        }                        
                    }

                    $rows1[]=$value;
                }
            }

           // menampilkan data user cs
            $user_cs=$this->assignment_user_pos_model->detail_cs($assignment_code, " and d.user_group_id=6 and a.status not in (-5) ")->result();            
            // setting tampilan data
            if(!empty($user_cs))
            {
                foreach ($user_cs as $key => $value) {
                    $code_enc=$value->assignment_code."_".$this->enc->encode($value->user_id);
                    $close_url=site_url($this->_module."/action_delete_user/{$code_enc}");

                    $value->actions ="";
                    if($value->status ==1 )
                    {

                        $value->actions ='<a class="btn btn-xs btn-danger" onclick="confirmDeletUser(\'Apakah anda yakin ingin menghapus user ini dari assignmnet ?\', \''.$close_url.'\')" title="Hapus" href="#"> <i class="fa fa-trash-o"></i> </a>';
                    }
                    $rows2[]=$value;
                }
            }


            // menampilkan data detail ptc/stc
            $user_ptcstc=$this->assignment_user_pos_model->detail_ptc_stc($assignment_code, " and (d.user_group_id=2 or d.user_group_id=10 ) and a.status not in (-5) ")->result();

            // setting tampilan data
            if(!empty($user_ptcstc))
            {
                foreach ($user_ptcstc as $key => $value) {
                    $code_enc=$value->assignment_code."_".$this->enc->encode($value->user_id);
                    $close_url=site_url($this->_module."/action_delete_user/{$code_enc}");
                    if($value->status ==1 )
                    {
                        $value->actions ='<a class="btn btn-xs btn-danger" onclick="confirmDeletUser(\'Apakah anda yakin ingin menghapus user ini dari assignmnet ?\', \''.$close_url.'\')" title="Hapus" href="#"> <i class="fa fa-trash-o"></i> </a>';                    
                    }
                    $rows3[]=$value;
                }
            }

            // cek data user Pos yang di tammpilkan hanya user selain user id yg sudah ada dan di pelabuhan yang di pilih saja
            $user=$this->assignment_user_pos_model->get_user(" where id not in (select user_id from app.t_trx_assignment_user_pos where assignment_code='".$assignment_code."' and status !='-5' ) and port_id='".$port_id."' and status=1 and user_group_id=4 order by username asc")->result();

            if(!empty($user))
            {
                foreach ($user as $key => $value) {

                    $value->id=$this->enc->encode($value->id);
                    $rows4[]=$value;
                }                
            }

            // cek data user CS yang di tammpilkan hanya user selain user id yg sudah ada dan di pelabuhan yang di pilih saja
            $usercs=$this->assignment_user_pos_model->get_user(" where id not in (select user_id from app.t_trx_assignment_cs where assignment_code='".$assignment_code."' and status !='-5' ) and port_id='".$port_id."' and status=1 and user_group_id=6 order by username asc")->result();

            if(!empty($usercs))
            {
                foreach ($usercs as $key => $value) {
                    if($value->status ==1 )
                    {
                        $value->id=$this->enc->encode($value->id);
                    }
                    $rows5[]=$value;
                }                
            }            


            // cek data user CS yang di tammpilkan hanya user selain user id yg sudah ada dan di pelabuhan yang di pilih saja
            $userptcstc=$this->assignment_user_pos_model->get_user(" where id not in (select user_id from app.t_trx_assignment_ptc_stc where assignment_code='".$assignment_code."' and status !='-5' ) and port_id='".$port_id."' and status=1 and (user_group_id=2 or user_group_id=10) order by username asc")->result(); 

            if(!empty($userptcstc))
            {
                foreach ($userptcstc as $key => $value) {

                    $value->id=$this->enc->encode($value->id);
                    $rows6[]=$value;
                }                
            }



            // menampilkan data detail vertivikator
            $user_vertifikasi=$this->assignment_user_pos_model->detail_vertifikator($assignment_code, " and  d.user_group_id=35 and a.status not in (-5) ")->result();

            // setting tampilan data
            if(!empty($user_vertifikasi))
            {
                foreach ($user_vertifikasi as $key => $value) {
                    $code_enc=$value->assignment_code."_".$this->enc->encode($value->user_id);
                    $close_url=site_url($this->_module."/action_delete_user/{$code_enc}");
                    $value->actions ="";
                    if($value->status ==1 )
                    {
                        $value->actions ='<a class="btn btn-xs btn-danger" onclick="confirmDeletUser(\'Apakah anda yakin ingin menghapus user ini dari assignmnet ?\', \''.$close_url.'\')" title="Hapus" href="#"> <i class="fa fa-trash-o"></i> </a>';                    
                    }
                    $rows8[]=$value;
                }
            }
          
            // cek data user vertivikator yang di tammpilkan hanya user selain user id yg sudah ada dan di pelabuhan yang di pilih saja
            $uservertifikasi=$this->assignment_user_pos_model->get_user(" where id not in (select user_id from app.t_trx_assignment_verifier where assignment_code='".$assignment_code."' and status !='-5' ) and port_id='".$port_id."' and status=1 and user_group_id=35 order by username asc")->result(); 

            if(!empty($uservertifikasi))
            {
                foreach ($uservertifikasi as $key => $value) {

                    $value->id=$this->enc->encode($value->id);
                    $rows7[]=$value;
                }                
            } 

            
            // cek data user userComandCenter yang di tammpilkan hanya user selain user id yg sudah ada dan di pelabuhan yang di pilih saja
            $userComandCenter=$this->assignment_user_pos_model->get_user(" where id not in (select user_id from app.t_trx_assignment_command_center where assignment_code='".$assignment_code."' and status !='-5' ) and port_id='".$port_id."' and status=1 and user_group_id=42 order by username asc")->result(); 

            if(!empty($userComandCenter))
            {
                foreach ($userComandCenter as $key => $value) {

                    // $value->id=$this->enc->encode($value->id);
                    $rows10[]=$value;
                }                
            } 

            $data['code']=1;
            $data['user_pos']=$rows1;
            $data['user_cs']=$rows2;
            $data['user_ptcstc']=$rows3;
            
            $data['user']=$rows4;
            $data['usercs']=$rows5;
            $data['userptcstc']=$rows6;
            $data['uservertifikator']=$rows7;
            $data['user_vertifikator']=$rows8;

            $data['userComandCenter']=$rows10;

            $data["tokenHash"]=$this->security->get_csrf_hash();
            $data["csrfName"]=$this->security->get_csrf_token_name();

        }
        else
        {
            $data['code']=0;
            $data["tokenHash"]=$this->security->get_csrf_hash();
            $data["csrfName"]=$this->security->get_csrf_token_name();
        }

        echo json_encode($data);


    }

    public function get_device_detail()
    {

        $assignment_code=$this->enc->decode($this->input->post('assignment'));
        $port_id=$this->enc->decode($this->input->post('port'));

        $rowdeviceComandCenter=$this->assignment_user_pos_model->select_data("app.t_mtr_device_terminal","where port_id='".$port_id."' and status=1 and terminal_type = 21 ")->result();
            
        $detailUserComand=$this->assignment_user_pos_model->detailUserComand($assignment_code, " and d.user_group_id=42 and a.status not in (-5) ")->result();

        $selectedDeviceComand=[];
        $deviceComandCenter=array();
        $selectedUserComand=array();
        $userComand=array();
        $comad_id=array();
        $userComandCenter=array();

        foreach ($rowdeviceComandCenter as $key => $value) {
            // $value->full_name=strtoupper($value->terminal_name[$key]);
            $value->full_name=strtoupper($value->terminal_name);
            $deviceComandCenter[]=$value;
        }

        if(!empty($detailUserComand) ) // pencegahan error 
        {
            foreach ($detailUserComand as $key => $value) {
                $code_enc=$value->assignment_code."_".$this->enc->encode($value->user_id);
                $value->shift_code_id=site_url($this->_module."/action_delete_user/{$code_enc}") ;

                $userComand[]=$value;
                $comad_id[]=$value->user_id;

                $selectedDeviceComand[$value->user_id]=$this->assignment_user_pos_model->detailDeviceComand($assignment_code,$value->shift_code)->result();
        
            }
        }

        if(!empty($comad_id) ) // pencegahan error 
        {
             $userComandCenter = $this->assignment_user_pos_model->getDropdown(" where id not in (select user_id from app.t_trx_assignment_verifier where assignment_code='".$assignment_code."'  and status !='-5' ) and a.id not in (".implode(",",$comad_id).") and port_id='".$port_id."' and status=1 and user_group_id=42  order by username asc");
        }
        $data['userComandCenter']= array_diff($userComandCenter,array(""=>"Pilih"));
        // print_r($userComand);exit;
        
        $data['deviceComandCenter']= $deviceComandCenter;
        $data['userComand']= $userComand;
        $data['comandId']= $comad_id;

        $data['selectedDeviceComand']= $selectedDeviceComand;
        $data["tokenHash"]=$this->security->get_csrf_hash();
        $data["csrfName"]=$this->security->get_csrf_token_name();

        echo json_encode($data);

    }




}
