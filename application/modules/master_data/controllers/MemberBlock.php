<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );


class MemberBlock extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('MemberBlockModel','member');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('Html2pdf');

        $this->_table    = 'app.t_mtr_member';
        $this->_username = $this->session->userdata('username');
        $this->_module = 'master_data/memberBlock';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->member->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Block Member',
            'content'  => 'memberBlock/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'method'=> $this->member->select_data("app.t_mtr_payment_method","where status='1' order by name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function change_status($id){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'change_status');
        
        $status=$this->status();

        $decodeId=$this->enc->decode($id);
        $getMember =$this->member->select_data("app.t_mtr_member", " where id={$decodeId} ")->row();

        $getStatus=array();

        $selected="";
        foreach ($status as $key => $value) 
        {
            
            if($key==$getMember->status)
            {
                $selected =$this->enc->encode($key);    
                $getStatus[$selected] = $value;
            }
            else
            {
                $getStatus[$this->enc->encode($key)] = $value;
            }

        }
        

        $data['title'] = 'Ubah Status';
        $data['status'] = $getStatus;
        $data['id'] = $id;
        $data['selected'] = $selected;



        $this->load->view($this->_module.'/change_status',$data);
    }

    public function action_change_status()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'change_status');

        $status=trim($this->enc->decode($this->input->post('status')));
        $id=trim($this->enc->decode($this->input->post('id')));


        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');

        $getParam=$this->member->select_data("app.t_mtr_custom_param" ," where param_name='limit_temp_block_member' ")->row();

        $getLimit=date('Y-m-d H:i:s',strtotime("+{$getParam->param_value} {$getParam->value_type}"));
        
        $blockingExpiered=$status=='-1'?$getLimit:NULL;

        $data=array(
                    'status'=>$status,
                    'blocking_expired'=>$blockingExpiered,
                    'updated_on'=>date("Y-m-d H:i:s"),
                    'updated_by'=>$this->session->userdata('username'),
                    );

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else
        {
            $this->db->trans_begin();
            $this->member->update_data($this->_table,$data, " id='$id' ");

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal update status');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil update status');
            }
            
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/memberBlock/action_change_status';
        $logMethod   = 'CHANGE_STATUS';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function status()
    {

        $data["0"]="Tidak Aktif";
        $data["1"]="Aktif";
        $data["-1"]="Temp Banned";
        $data["-2"]="Permanent Banned";


        return $data;
    }


}
