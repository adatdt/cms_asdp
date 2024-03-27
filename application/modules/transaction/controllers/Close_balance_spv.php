<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
/*
 enchance validatioan and add csrf toket by adat 11-08-2023 adat.nutech@gmail.com
 */
class Close_balance_spv extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_close_balance_spv','balance');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_assignment_regu';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/close_balance_spv';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->balance->dataList();
            echo json_encode($rows);
            exit;
        }

        if($this->balance->get_identity_app()==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port=$this->balance->select_data("app.t_mtr_port", " where status !='-5' order by name asc ")->result();
                $row_port=0;
            }
            else
            {
                $port=$this->balance->select_data("app.t_mtr_port", " where id={$this->session->userdata('port_id')} order by name asc ")->result();
                $row_port=1;
            }
        }
        else
        {
            $port=$this->balance->select_data("app.t_mtr_port", " where id={$this->balance->get_identity_app()} order by name asc ")->result();
            $row_port=1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Tutup Shift',
            'content'  => 'close_balance_spv/index',
            'port'  => $port,
            'row_port'  => $row_port,
            'shift'  => $this->balance->select_data("app.t_mtr_shift", " where status !='-5' order by shift_name asc ")->result(),
            // 'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            

        );

		$this->load->view('default', $data);
	}



    public function action_close_balance($assignmnet_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'close_balance');
        $decode_code=$this->enc->decode("$assignmnet_code");

        $data=array(
            'status'=>2,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $data_user_pos=array(
            'status'=>2,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );

        if(!empty($decode_code))
        {
            // check apakah masih ada status yang berdinas
            $ceck=$this->balance->select_data("app.t_trx_opening_balance"," where assignment_code=".$this->db->escape($decode_code)." and status=1");
    
            // check user cs apakah masih ada status yang berdinas
            $ceck_cs=$this->balance->select_data("app.t_trx_assignment_cs"," where assignment_code=".$this->db->escape($decode_code)." and status=1");  
    
            // check user cs apakah masih ada status yang berdinas
            $ceck_ptc_stc=$this->balance->select_data("app.t_trx_assignment_ptc_stc"," where assignment_code=".$this->db->escape($decode_code)." and status=1");  
            
            // check user verifikator apakah masih ada status yang berdinas
            $ceck_verifikator=$this->balance->select_data("app.t_trx_assignment_verifier"," where assignment_code=".$this->db->escape($decode_code)." and status=1");  
            
            // check user comand center apakah masih ada status yang berdinas
            $ceck_comand_center=$this->balance->select_data("app.t_trx_assignment_command_center"," where assignment_code='".$decode_code."' and status=1");  

        }

        if(empty($decode_code))
        {
            echo $res=json_api(0, 'Kode Penugasan Tidak dikenal');
        }
        else if($ceck->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal tutup shift, masih ada user POS yang berdinas');
        }
        else if($ceck_cs->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal tutup shift, masih ada user CS yang berdinas');
        }
        else if($ceck_ptc_stc->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal tutup shift, masih ada user PTC/ STC yang berdinas');
        }
        else if($ceck_verifikator->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal tutup shift, masih ada user Verifikator yang berdinas');
        }
        else if($ceck_comand_center->num_rows()>0)
        {
            echo $res=json_api(0, 'Gagal tutup shift, masih ada user Comand Center yang berdinas');
        }         
        else
        {
            $this->db->trans_begin();
            $this->balance->update_data($this->_table,$data," assignment_code=".$this->db->escape($decode_code)." ");
            $this->balance->update_data("app.t_trx_assignment_user_pos",$data_user_pos," assignment_code='".$decode_code."' and status=1 ");

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal closing balance');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil closing balance');
            }   

        }



        /* Fungsi Create Log */
        $data2=array($data);
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/opening_balance/action_opening_balance';
        $logMethod   = 'CLOSE BALANCE';
        $logParam    = json_encode($data2);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
