<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Accessbility extends MY_Controller {

    public function __construct() {
        parent::__construct ();
        logged_in ();
        $this->load->model('accessbilityModel', 'accessbility');
        $this->_module   = 'configuration/accessbility';
    }

    public function index(){    
        checkUrlAccess(uri_string(),'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->accessbility->privilegeList();
            echo json_encode ( $rows );
            exit ();
        }

        $data = array(
            'home'       => 'Home',
            'url_home'   => site_url('home'),
            'parent'     => 'Konfigurasi Sistem',
            'url_parent' => '#',
            'title'      => 'Akses',
            'content'    => 'accessbility/index',
            // 'usergroup'  => $this->list_user_group(),
        );

        $this->load->view ( 'default', $data );
    }

    function get_list(){
        validate_ajax();
        $rows = $this->accessbility->get_list();
        $rows["tokenHash"] = $this->security->get_csrf_hash();
        $rows["csrfName"] = $this->security->get_csrf_token_name();
        echo json_encode($rows);
    }


    function action_privilege(){

        // validate_ajax();
        // $this->global_model->checkAccessMenuAction($this->_module,'add');

        $actions=$this->input->post('actions[]');

        // print_r($actions);
        // exit;

        $cloudActionId=array();
        $localActionId=array();
        $detail_id = "detail_id";
        $status_checked ="status_checked";

        foreach ($actions as $key => $value) {
            $this->form_validation->set_rules("actions[" . $key . "][" . $detail_id . "]", 'detail_id', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'Name  contains invalid characters'));
            $this->form_validation->set_rules("actions[" . $key . "][" . $status_checked . "]", 'status_checked', 'trim|required|numeric');
            // pemilihan id untuk clode atau id untuk local
            if(!empty($value['detail_id']))
            {
                // $explod[0] : inisial c (clode), l(local)
                // $explod[1] : id menu web detail

                $explode=explode("_", $value['detail_id']);
                if($explode[0]=="c")
                {
                    $cloudActionId[]=$explode[1];
                    $status_checked_cloud[]=$value['status_checked']==1?'true':'false';
                }
                else
                {
                    $localActionId[]=$explode[1];
                    $status_checked_local[]=$value['status_checked']==1?'true':'false';
                }
            }
        }

        if ($this->form_validation->run() == FALSE) 
        {
            echo $res = json_api(0, validation_errors(),[]);
            exit;
        }


        $data=array();
        $this->db->trans_begin();

        // update action cloude
        if(count((array)$cloudActionId)>0)
        {
            foreach ($cloudActionId as $key => $value) {

                $dataCloud=array(
                                    "access_cloud"=>$status_checked_cloud[$key],
                                    "updated_on"=>date('Y-m-d H:i:s'),
                                    "updated_by"=>$this->session->userdata("username"),

                                );
                
                $this->accessbility->update_data("core.t_mtr_menu_detail_web",$dataCloud," id={$value} ");
                $data[]=$dataCloud;
            }
        }

        // update action local
        if(count((array)$localActionId)>0)
        {
            foreach ($localActionId as $key => $value) {
                
                $dataLocal=array(
                    "access_local"=>$status_checked_local[$key],
                    "updated_on"=>date('Y-m-d H:i:s'),
                    "updated_by"=>$this->session->userdata("username"),

                );
                $this->accessbility->update_data("core.t_mtr_menu_detail_web",$dataLocal," id={$value} ");
                $data[]=$dataLocal;
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


        // print_r($cloudActionId);
        // exit;

        /* log-file */
        $created_by = $this->session->userdata('username');   
        $this->log_activitytxt->createLog($created_by, uri_string(), 'set_privilege', json_encode($data), $res); 
    }


}
