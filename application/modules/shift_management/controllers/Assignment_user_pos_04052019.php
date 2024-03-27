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
            $rows = $this->assignment_user_pos_model->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Penugasan',
            'content'  => 'assignment_user_pos/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'port'=>$this->assignment_user_pos_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
            'team'=>$this->assignment_user_pos_model->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Penugasan';
        $data['port']=$this->assignment_user_pos_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['user']=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where status=1 order by username asc")->result();
        $data['team']=$this->assignment_user_pos_model->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result();
        $data['shift']=$this->assignment_user_pos_model->select_data("app.t_mtr_shift","where status=1 order by shift_name asc")->result();
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $team_code=$this->enc->decode($this->input->post('team'));
        $port_id=$this->enc->decode($this->input->post('port'));
        $spv_id=$this->enc->decode($this->input->post('spv'));
        $shift_id=$this->enc->decode($this->input->post('shift'));
        $user_id=$this->input->post('user[]');
        $assignment_date=$this->input->post('assignment_date');
        $cs_id=$this->enc->decode($this->input->post('cs'));


        $this->form_validation->set_rules('team', 'Regu', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('shift', 'Shift', 'required');
        $this->form_validation->set_rules('spv', 'Shift', 'required');
        $this->form_validation->set_rules('cs', 'Shift', 'required');
        $this->form_validation->set_rules('user[]', 'port', 'required');

        // mencari nilai yang sudah di decrycpt
        $user_decode=array();

        foreach ($user_id as $user_id) {
            $user_decode[]=$this->enc->decode($user_id);
        }


        // pengecekan agar data user id yang masuk tidak ada yang sama dalam satu inputan
        $array_unique=array_unique($user_decode);

        $total=count($array_unique);


        $create_code=$this->createCode($port_id);

        // pengecekn jika regu sudah ada di tanggal assignment yang sama
        $check_regu=$this->assignment_user_pos_model->select_data($this->_table," where team_code='".$team_code."' and assignment_date='".$assignment_date."' and status=1");

        // pengecekn jika spv sudah ada di tanggal assignment yang sama
        $check_user_spv=$this->assignment_user_pos_model->select_data($this->_table," where user_id='".$spv_id."' and assignment_date='".$assignment_date."' and status=1");
        

        $data2=array();

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if($check_regu->num_rows()>0)
        {
            echo $res=json_api(0, 'Regu sudah ditugaskan tanggal '.$assignment_date);   
        }
        else if($check_user_spv->num_rows()>0)
        {
            echo $res=json_api(0, 'Spv sudah ditugaskan tanggal '.$assignment_date);   
        }
        else
        {
            $this->db->trans_begin();
            
            $total=array();
            $username=array();
            foreach ($array_unique as $arr)
            {
                //check user jika user sudah ada dalam transaksi tersebut
                $check_user=$this->assignment_user_pos_model->select_data($this->_table,"where user_id=$arr and assignment_date='".$assignment_date."' and status=1");

                $datauser=$this->assignment_user_pos_model->select_data("core.t_mtr_user","where id=".$arr)->row();

                if ($check_user->num_rows()>0)
                {
                    $total[]=1;
                    $getusername[]=$datauser->username;

                }
                else
                {
                    $data=array('user_id'=>$arr,
                            'team_code'=>$team_code,
                            'port_id'=>$port_id,
                            'shift_id'=>$shift_id,
                            'status'=>1,
                            'assignment_code'=>$create_code,
                            'assignment_date'=>$assignment_date,
                            'created_on'=>date("Y-m-d H:i:s"),
                            'created_by'=>$this->session->userdata("username"),
                            );

                    $this->assignment_user_pos_model->insert_data($this->_table,$data);
                    $data2[]=$data;
                }
            }


            if(array_sum($total)>0)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Username '.implode(", ",$getusername).' sudah ada pada pada tanggal asignment '.$assignment_date);
            }
            else
            {

                // insert data spv assignmnet user pos
                $dataSpv=array('user_id'=>$spv_id,
                    'team_code'=>$team_code,
                    'port_id'=>$port_id,
                    'shift_id'=>$shift_id,
                    'status'=>1,
                    'assignment_code'=>$create_code,
                    'assignment_date'=>$assignment_date,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username"),
                );    

                $data_assignment_regu=array(
                    'supervisor_id'=>$spv_id,
                    'team_code'=>$team_code,
                    'assignment_code'=>$create_code,
                    'port_id'=>$port_id,
                    'assignment_date'=>$assignment_date,
                    'shift_id'=>$shift_id,
                    'status'=>1,
                    'created_on'=>date('Y-m-d'),
                    'created_by'=>$this->session->userdata("username"),
                );


                $data_assignment_cs=array(
                    'user_id'=>$cs_id,
                    'team_code'=>$team_code,
                    'assignment_code'=>$create_code,
                    'port_id'=>$port_id,
                    'assignment_date'=>$assignment_date,
                    'shift_code'=>$this->createCodeCs($port_id),
                    'shift_id'=>$shift_id,
                    'status'=>1,
                    'created_on'=>date('Y-m-d'),
                    'created_by'=>$this->session->userdata("username"),
                );

                $this->assignment_user_pos_model->insert_data($this->_table,$dataSpv);
                $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_regu",$data_assignment_regu);
                $this->assignment_user_pos_model->insert_data("app.t_trx_assignment_cs",$data_assignment_cs);

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
    }
    

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $assignment_code=$this->enc->decode($id);
        $data['title'] = 'Edit Penugasan';
        $data['port']=$this->assignment_user_pos_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['team']=$this->assignment_user_pos_model->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result();
        $data['detail']=$this->assignment_user_pos_model->detail_user($assignment_code)->result();
        $data['shift']=$this->assignment_user_pos_model->select_data("app.t_mtr_shift"," order by shift_name asc")->result();

        $data['detail2']=$this->assignment_user_pos_model->detail($assignment_code)->row();

        $get_port=$this->assignment_user_pos_model->detail($assignment_code)->row();

        // cek data user yang di tammpilkan hanya user selain user id yg sudah ada dan di pelahun yang di pilih saja
        $data['user']=$this->assignment_user_pos_model->select_data("core.t_mtr_user"," where id not in (select user_id from app.t_trx_assignment_user_pos where assignment_code='".$assignment_code."' and status=1 ) and port_id='".$get_port->port_id."' and status=1 and user_group_id=4 order by username asc")->result();


        $this->load->view($this->_module.'/edit',$data);   
    }


    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $assignment_code=$this->enc->decode($this->input->post('assignment_code'));
        $user_id=$this->input->post('user');
        $team_code=$this->enc->decode($this->input->post('team'));
        $shift_id=$this->enc->decode($this->input->post('shift'));
        $port_id=$this->enc->decode($this->input->post('port'));
        $assignment_date=$this->input->post('assignment_date');
        $list_user=$this->input->post('list_user[]');



        // mendecode data check box
        $user_id_decode=array();

        if (empty($list_user))
        {
            $list_id="assignment_code='".$assignment_code."' and status=1";
        }
        else
        {
            foreach($list_user as $key=>$value)
            {
                $user_id_decode[]=$this->enc->decode($value);
            }

            $list_id="assignment_code='".$assignment_code."' and user_id not in (".implode(",",$user_id_decode).") and status=1";
        }


        $this->form_validation->set_rules('assignment_code', 'Kode assignment', 'required');
        $this->form_validation->set_rules('user[]', 'User', 'required');
        $this->form_validation->set_rules('shift', 'shift', 'required');
        // remove user
            // mengambil data yang tidak kosong
            $user_decode=array();
            for($y=0;$y<=max(array_keys($user_id));$y++)
            {
                if (!empty($user_id[$y]))
                {
                    $user_decode[]=$this->enc->decode($user_id[$y]);
                }
            }

            // mencegah data array duplikat
            $user_unique=array_unique($user_decode);

        
            $this->db->trans_begin();

            $data_update=array(
                'status'=>-5,
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata("username"),
            );

            // jika tidak data check box di uncentang maka update
            if(!empty($user_id_decode))
            {// update data yang di uncentang di chekbox
                $this->assignment_user_pos_model->update_data($this->_table, $data_update,$list_id);
            }

            // script lain
            $err_user=array();
            $mydata=array();
            if(empty($user_unique))
            {
                $err_user[]=0;
            }
            else
            {
                foreach($user_unique as $key=>$value)
                {
                    // pengecekan data jika user belum ad di code assigment yang sama 
                    $check=$this->assignment_user_pos_model->select_data($this->_table,"where user_id='".$value."' and assignment_code='".$assignment_code."' and status=1 ")->num_rows();

                    $chek_other_user=$this->assignment_user_pos_model->select_data($this->_table,"where user_id='".$value."' and assignment_code !='".$assignment_code."' and assignment_date='".$assignment_date."' and status=1 ")->row();
                    
                    $data=array('user_id'=>$value,
                        'team_code'=>$team_code,
                        'port_id'=>$port_id,
                        'shift_id'=>$shift_id,
                        'status'=>1,
                        'assignment_code'=>$assignment_code,
                        'assignment_date'=>$assignment_date,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata("username"),
                        );

                    if($check>0)
                    {
                        $err_user[]=1;
                    }
                    else if(empty($chek_other_user))
                    {
                        $err_user[]=0;
                        $this->assignment_user_pos_model->insert_data($this->_table,$data);   
                    }
                    else
                    {
                        $where=$chek_other_user->id;
                        $this->assignment_user_pos_model->update_data($this->_table,$data_update,"id=".$where);
                        $this->assignment_user_pos_model->insert_data($this->_table,$data); 
                        $err_user[]=0;
                    }
                }
            }



            if (array_sum($err_user)>0)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'User sudah ada di regu ini');
            }
            else
            {
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

        // /* Fungsi Create Log */
        // $createdBy   = $this->session->userdata('username');
        // $logUrl      = site_url().'shift_management/team/action_edit';
        // $logMethod   = 'EDIT';
        // $logParam    = json_encode($data);
        // $logResponse = $res;

        // $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
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

        $this->db->trans_begin();
        $this->assignment_user_pos_model->update_data($this->_table,$data,"assignment_code='".$assignment_code."'");

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
        $logUrl      = site_url().'shift_management/team/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    function get_data()
    {
        $port_id=$this->input->post("port");
        $port_decode=$this->enc->decode($port_id);

        empty($port_decode)?$kode=0:$kode=$port_decode;

        $row=$this->assignment_user_pos_model->select_data("core.t_mtr_team","where port_id='".$kode."' and status=1")->result();

        $regu=array();
        $spv=array();
        $user=array();
        $usercs=array();
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

        $data['regu']=$regu;
        $data['spv']=$spv;
        $data['user']=$user;
        $data['usercs']=$usercs;


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

    function create_code()
    {
        $data=$this->db->query("SELECT 
                    SUBSTRING(EXTRACT(YEAR FROM now())::varchar, 3,2)||
                     to_char(EXTRACT(DAY FROM now()), 'fm000')|| 
                    (to_char(nextval('app.t_mtr_assigment_user_pos_code_seq'), 'fm0000')) as code ")->row();

        return $data->code;

    }

    function createCode($port)
    {
        $front_code="A".$port."".date('ymd');

        $chekCode=$this->db->query("select * from app.t_trx_assignment_user_pos where left(assignment_code,8)='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (assignment_code) as max_code from app.t_trx_assignment_user_pos where left(assignment_code,8)='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, 8, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }


    function createCodeCs($port)
    {
        $front_code="S".$port."".date('ymd');

        $chekCode=$this->db->query("select * from app.t_trx_assignment_user_pos where left(assignment_code,8)='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (assignment_code) as max_code from app.t_trx_assignment_user_pos where left(assignment_code,8)='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, 8, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

}
