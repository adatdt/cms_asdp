<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Opening_balance extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_opening_balance','balance');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_opening_balance';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/opening_balance';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->balance->dataList();
            echo json_encode($rows);
            exit;
        }


        // get identity or port from user

        if ($this->balance->get_identity_app()->port_id==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port=$this->balance->select_data("app.t_mtr_port"," where status not in (-5) order by name asc")->result();
                $row_port=0;
            }
            else
            {
                $port=$this->balance->select_data("app.t_mtr_port"," where id={$this->session->userdata('port_id')} order by name asc")->result();
                $row_port=1;
            }
        }
        else
        {
            $port=$this->balance->select_data("app.t_mtr_port"," where id={$this->balance->get_identity_app()->port_id} order by name asc")->result();
            $row_port=1;
        }


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Dinas',
            'content'  => 'opening_balance/index',
            'port'  => $port,
            'row_port'  => $row_port,
            'shift'  => $this->balance->select_data("app.t_mtr_shift"," where status not in (-5) order by shift_name asc")->result(),
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            

        );

		$this->load->view('default', $data);
	}

    public function data_cs()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request())
        {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->balance->data_cs();
            echo json_encode($rows);
        }

    }

    public function data_ptcstc()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request())
        {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->balance->data_ptcstc();
            echo json_encode($rows);
        }

    }
    
    public function data_verifikator()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request())
        {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->balance->data_verifikator();
            echo json_encode($rows);
        }

    }    

    public function data_comand_center()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request())
        {
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->balance->data_comand_center();
            echo json_encode($rows);
        }

    } 
 

    public function edit($ob_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($ob_code);

        $data['title'] = 'Edit Opening Balance';
        $data['detail']= $this->balance->get_detail("where ob_code='".$id_decode."'")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $total_cash=trim($this->input->post('total_cash'));
        $id=$this->enc->decode($this->input->post('id'));

        $this->form_validation->set_rules('total_cash', 'cash', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');

        $data=array(
                    // 'total_cash'=>$total_cash,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else
        {

            $this->db->trans_begin();

            $this->balance->update_data($this->_table,$data,"ob_code='".$id."'");

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
        $logUrl      = site_url().'transaction/opening_balance/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_force_logoff($id)
    {
        validate_ajax();
        // $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $ob_code=$this->enc->decode($id);

        $data=array(
                    'terminal_code'=>NULL,
                    'is_logged_in'=>0,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        $check_validate=$this->balance->select_data($this->_table," where ob_code=".$this->db->escape($ob_code)." and status =1 and terminal_code is not null ");

        if ($check_validate->num_rows()<1)
        {
            echo $res=json_api(0, 'Gagal Force Logoff, User sudah logout atau tutup dinas');
        }
        else
        {

            $this->db->trans_begin();


            $this->balance->update_data($this->_table,$data,"ob_code='".$ob_code."'");

            if ($this->db->trans_status() === FALSE)
            {   
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal Force Logoff ');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil Force Logoff ' );
            }
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/opening_balance/action_force_logoff';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function action_close_balance($ob_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'close_balance');

        $data=array(
            'status'=>2,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $id = $this->enc->decode($ob_code);

        $get_summary=$this->balance->get_summary($id)->result();
        
        $data_summary=array();
        if(!empty($get_summary))
        {
            $data_summary=array();
            foreach($get_summary as $key=>$value)
            {
                    $value->created_by=$this->session->userdata("username");
                    $value->created_on=date("Y-m-d H:i:s");

                    $data_summary[]=$value;
            }
        }

        // mencegah terjadi closing 2 x / duplikasi summary
        $check=$this->balance->select_data($this->_table," where ob_code=".$this->db->escape($id)." and status=2");

        if($check->num_rows()>0)
        {
            echo $res=json_api(1, 'Berhasil tutup dinas');
        }
        else
        {
            $this->db->trans_begin();
            $this->balance->update_data($this->_table,$data," ob_code=".$this->db->escape($id)." ");

            if(!empty($get_summary))
            {
                $this->balance->insert_data_batch("app.t_trx_closing_balance_pos",$data_summary);
            }

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tutup dinas');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil tutup dinas');
            }   
        }

        /* Fungsi Create Log */
        $data2=array($data);
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/opening_balance/action_close_balance';
        $logMethod   = 'CLOSE BALANCE';
        $logParam    = json_encode($data2);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_close_balance2($shift_code,$param)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'close_balance');

        if($param=='cs')
        {
            $table="app.t_trx_assignment_cs";
        }
        else if($param=='verifikator')
        {
            $table="app.t_trx_assignment_verifier";
        }
        else if($param=='comand_center')
        {
            $table="app.t_trx_assignment_command_center";
        }
        else
        {
            $table="app.t_trx_assignment_ptc_stc";
        }

        $data=array(
            'status'=>2,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $id = $this->enc->decode($shift_code);

        $this->db->trans_begin();
        $this->balance->update_data($table ,$data," shift_code='".$id."'");


        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal tutup dinas');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil tutup dinas');
        }   
    

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/opening_balance/action_close_balance2';
        $logMethod   = 'CLOSE BALANCE';
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
            echo $res=json_api(0, 'Gagal non aktif');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil non aktif data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'fare/route/action_change';
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

    public function assignment_code()
    {
        $date=$this->input->post("date");
        $data=$this->balance->get_assignment_code("where a.status=1 and assignment_date='".$date."' order by assignment_code asc")->result();

        $row=array();
        foreach($data as $r)
        {
            $r->team_name=strtoupper($r->team_name);
            $row[]=$r;
        }

        echo json_encode($row); 
    }

    public function get_shift()
    {
        $assignment_code=$this->input->post("assignment_code");
        $data=$this->balance->get_assignment_code("where a.status=1 and assignment_code='".$assignment_code."' ")->row();


        $data->shift_id=$this->enc->encode($data->shift_id);
        $row=$data;

        echo json_encode($row); 
    }

    public function user_list()
    {
        $assignment_code=$this->input->post("assignment_code");
        $data=$this->balance->get_user_list("where a.status=1 and assignment_code='".$assignment_code."' ")->result();

        echo json_encode($data); 
    }

    public function get_port()
    {
        $assignment_code=$this->input->post("assignment_code");

        $row=$this->balance->get_assignment_code("where a.status=1 and assignment_code='".$assignment_code."' ")->row();

        //encode port id
        $row->port_id=$this->enc->encode($row->port_id);

        $data=$row;

        echo json_encode($data); 
    }


    function createCode($port)
    {

        $front_code="O".$port."".date('ymd');

        $chekCode=$this->db->query("select * from app.t_trx_opening_balance where left(ob_code,".strlen($front_code).")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (ob_code) as max_code from app.t_trx_opening_balance where left(ob_code,".strlen($front_code).")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, strlen($front_code), 3);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%03s", $noUrut);
            return $kode;
        }
    }

}
