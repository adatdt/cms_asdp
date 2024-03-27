<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Team extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('team_model');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'core.t_mtr_team';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'shift_management/team';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('port')){
                $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
            }
            
            
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->team_model->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Team',
            'content'  => 'team/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'port'      =>$this->team_model->select_data("app.t_mtr_port","where status='1' order by name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Team';
        $data['port']=$this->team_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        

        $this->form_validation->set_rules('team', 'regu', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'nama regu memuat invalid karakter'));
        $this->form_validation->set_rules('port', 'pelabuhan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        
        $this->form_validation->set_message('required','%s harus diisi!')
                                ->set_message('max_length','{field} Maksimal  {param} karakter! ');

        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {
            $team=trim($this->input->post('team'));
            $port=$this->enc->decode($this->input->post('port'));

            $data=array(
                'team_name'=>$team,
                'team_code'=>$this->createCode($port),
                'port_id'=>$port,
                'status'=>1,
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata('username'),
                );

            $check_nama=$this->team_model->select_data($this->_table,"where upper(team_name)=upper('$team') and port_id='$port' and status not in (-5)");
                
            if($check_nama->num_rows()>0)
            {
                echo $res=json_api(0, 'Nama sudah ada');
            }
            else
            {
                $this->db->trans_begin();
                $this->team_model->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'shift_management/team/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $team_id=$this->enc->decode($id);
        $data['title'] = 'Edit Team';
        $data['port']=$this->team_model->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['detail']=$this->team_model->select_data($this->_table,"where id=$team_id and status not in (-5)")->row();
        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $this->form_validation->set_rules('id', 'port id', 'required');
        $this->form_validation->set_rules('team', 'regu', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'nama regu memuat invalid karakter'));
        $this->form_validation->set_rules('port', 'pelabuhan', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        
        $this->form_validation->set_message('required','%s harus diisi!')
                                ->set_message('max_length','{field} Maksimal  {param} karakter! ');

        $data = null;
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {
            $team=trim($this->input->post('team'));
            $port=$this->enc->decode($this->input->post('port'));
            $team_id=$this->enc->decode($this->input->post('id'));

            $data=array(
                'team_name'=>$team,
                'port_id'=>$port,
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
                );

            $check_nama=$this->team_model->select_data($this->_table,"where port_id=".$port." and upper(team_name)=upper('$team') 
                and status not in (-5) and id !=$team_id");

            
            if($check_nama->num_rows()>0)
            {
                echo $res=json_api(0, validation_errors());
            }
            else
            {
                $this->db->trans_begin();
                $this->team_model->update_data($this->_table,$data,"id=$team_id");

                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                    echo $res=json_api(0, 'Gagal edit data ');
                }
                else
                {
                    $this->db->trans_commit();
                    echo $res=json_api(1, 'Berhasil edit data');
                }   
            }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');
        $team_id = $this->enc->decode($id);

        $data = [];
        if(!$team_id || empty($team_id)){
            echo $res=json_api(0, 'Gagal delete data');            
        }
        else{
            $data=array(
                'status'=>-5,
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
                );


                $this->db->trans_begin();
                $this->team_model->update_data($this->_table,$data,"id=$team_id");

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
        $logUrl      = site_url().'shift_management/team/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        
        $data = [];
        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal update status');            
        }
        else{
            $d = explode('|', $p);

            /* data */
            $data = array(
                'status' => $d[1],
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );


            $this->db->trans_begin();
            $this->team_model->update_data($this->_table,$data,"id=".$d[0]);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal non aktif');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil non aktif data');
            }   
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function create_code()
    {
        $data=$this->db->query("SELECT 
                    SUBSTRING(EXTRACT(YEAR FROM now())::varchar, 3,2)||
                     to_char(EXTRACT(DAY FROM now()), 'fm000')|| 
                    (to_char(nextval('core.t_mtr_team_code_seq'), 'fm0000')) as code ")->row();

        return $data->code;

    }

    public function enable($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);

        $data = [];
        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal aktifkan data');            
        }
        else{

            $d = explode('|', $p);

            /* data */
            $data = array(
                'status' => $d[1],
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );

            $check_enable=$this->team_model->select_data($this->_table,"where upper(team_name)=upper('".$d[2]."') and port_id=".$d[3]." and status=1");

            if($check_enable->num_rows()>0)
            {
                echo $res=json_api(0, 'Gagal aktifkan data, Sudah ada nama yang aktif');
            }
            else
            {
                $this->db->trans_begin();
                $this->team_model->update_data($this->_table,$data,"id=".$d[0]);

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
        } 

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/enable';
        $logMethod   = 'Enable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function disable($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);

        $data = [];
        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal dinonaktifkan data');            
        }
        else{
            $d = explode('|', $p);

            /* data */
            $data = array(
                'status' => $d[1],
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata('username'),
            );


            $this->db->trans_begin();
            $this->team_model->update_data($this->_table,$data,"id=".$d[0]);

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
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/enable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function createCode($port)
    {
        $front_code="R".$port."".date('ymd');

        $chekCode=$this->db->query("select * from core.t_mtr_team where left(team_code,8)='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (team_code) as max_code from core.t_mtr_team where left(team_code,8)='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, 8, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

}
