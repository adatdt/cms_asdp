<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Approval_report extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_approval_report','report');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_approval_report';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/approval_report';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->report->dataList();
            echo json_encode($rows);
            exit;
        }

        // mengam bil port id berdasarkan app port, jika pp port 0 (cloude) maka app port mengikuti port id yang ada pada user
        $check_port=$this->report->select_data("app.t_mtr_identity_app")->row();

        // jika identity app nya 0 (data cloude), maka usernya mengambil port berdasarkan session yang di ambil dari port user
        if($check_port->port_id==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port_id='';
                $port=$this->report->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();
            }
            else
            {
                $port_id=$this->session->userdata('port_id');
                $port=$this->report->select_data("app.t_mtr_port", " where id={$port_id} order by name asc ")->result();
            }
        }
        else
        {
            $port_id=$check_port->port_id;
            $port=$this->report->select_data("app.t_mtr_port", " where id={$port_id} order by name asc  ")->result();
        }

        $url =site_url($this->_module.'/add');

        // menampilkan tombol approve ketika status user groupnya 13 dan 1 (spv asdp dan admin)
        // if ($this->get_user_group()==1 or $this->get_user_group()==13)
        // {
            $btn_approve=generate_button($this->_module, 'add','<button onclick="showModal(\''.$url.'\')" class="btn btn-sm btn-warning" title="Tambah"><i class="fa fa-plus"></i> Approve SPV</button> ');   
        // }
        // else
        // {
        //     $btn_approve="";
        // }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Approval Laporan',
            'content'  => 'approval_report/index',
            'port'=>$port,
            'port_id'=>$port_id,
            'shift'=>$this->report->select_data("app.t_mtr_shift", " where status=1 order by shift_name asc")->result(),
            'btn_add'  =>$btn_approve,
        );

		$this->load->view('default', $data);
	}


    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // mengam bil port id berdasarkan app port, jika pp port 0 (cloude) maka app port mengikuti port id yang ada pada user
        $check_port=$this->report->select_data("app.t_mtr_identity_app")->row();

        // jika identity app nya 0 (data cloude), maka usernya mengambil port berdasarkan session yang di ambil dari port user
        if($check_port->port_id==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port_id='';
                $port=$this->report->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();
            }
            else
            {
                $port_id=$this->session->userdata('port_id');
                $port=$this->report->select_data("app.t_mtr_port", " where id={$port_id} order by name asc ")->result();
            }
        }
        else
        {
            $port_id=$check_port->port_id;
            $port=$this->report->select_data("app.t_mtr_port", " where id={$port_id} order by name asc  ")->result();
        }

        $data['title'] = 'Approval Laporan';
        $data['master_report'] = $this->report->select_data("app.t_mtr_report", " where status=1 order by report_name asc")->result();
        $data['shift'] = $this->report->select_data("app.t_mtr_shift", " where status=1 order by shift_name asc")->result();
        $data['ship_class'] = $this->report->select_data("app.t_mtr_ship_class", " where status=1 order by name asc")->result();
        $data['port']= $port;
        $data['port_id']= $port_id;       
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $report_code=$this->enc->decode($this->input->post('report_name'));
        $shift_id=$this->enc->decode($this->input->post('shift'));
        $port_id=$this->enc->decode($this->input->post('port'));
        $ship_class_id=$this->enc->decode($this->input->post('ship_class'));
        $report_date=$this->input->post('date');

        $this->form_validation->set_rules('report_name', 'Nama Laporan', 'required');
        $this->form_validation->set_rules('shift', ' Shift', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('ship_class', 'Tipe', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');
        
        $data=array(
                    'report_code'=>$report_code,
                    'port_id'=>$port_id,
                    'shift_id'=>$shift_id,
                    'ship_class'=>$ship_class_id,
                    'report_date'=>$report_date,
                    'approve_spv'=>1,
                    'approve_date_spv'=>date("Y-m-d H:i:s"),
                    'approve_manager'=>0,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // ceck data jika data sudah diapprove
        $check=$this->report->select_data($this->_table," where report_code='".$report_code."' and shift_id={$shift_id} and port_id={$port_id} and report_date='{$report_date}'and ship_class={$ship_class_id} and status not in (-5) ");

        $user_group=$this->get_user_group();
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0," Data Sudah diapprove");
        }
        else
        {
            $this->db->trans_begin();

            $this->report->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'transaction/approve_report/action_add';
        $logMethod   = 'APPROVE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function approve_manager($id_param)
    {
        // validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=$this->enc->decode($id_param);


        $data=array(
                    
                    'approve_manager'=>1,
                    'approve_date_manager'=>date("Y-m-d H:i:s"),
                    'status'=>2,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        // ceck data jika ada yang mengubah status approve spv
        $check=$this->report->select_data($this->_table," where id={$id} ")->row();

        if($check->approve_spv=='')
        {
            echo $res=json_api(0,"SPV Belum approve");   
        }
        else if(!empty($check->approve_date_manager))
        {
            echo $res=json_api(0,"Data sudah di approve");
        }
        else 
        {
            $this->db->trans_begin();

            $this->report->update_data($this->_table,$data,"id=$id");

            if ($this->db->trans_status() === FALSE)
            {   
                $this->db->trans_rollback();
                echo $res=json_api(0,"Gagal approve");
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1,"Berhasil approve");
            }
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/approval_report/approve_manager';
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
        $this->report->update_data($this->_table,$data,"id=".$d[0]);

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
        $this->report->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'master_data/master_report/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function createCode()
    {
        $front_code="RC".date('ymd');

        $len=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_report where left(report_code,".$len.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (report_code) as max_code from app.t_mtr_report where left(report_code,".$len.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $len, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

    public function get_user_group()
    {
        $user_id=$this->session->userdata('id');
        $get_user_group=$this->report->select_data("core.t_mtr_user", " where id={$user_id}")->row();
        return $get_user_group->user_group_id;

    }
}
