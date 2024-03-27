<?php 
if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        logged_in();
    }

    public function check_access($slug, $action) {
        $g_id       = $this->session->userdata('group_id');
        $access     = array('slug' => $slug,'g_id'=> $g_id, 'action' => $action );
        $getAccess  = $this->global_model->getAccess($access);
        if($getAccess==false){
         redirect('error_401');
        } 
    }

}