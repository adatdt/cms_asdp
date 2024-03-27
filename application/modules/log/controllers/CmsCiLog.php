<?php
defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(0);

class CmsCiLog extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('cmsCiLogModel', 'logCi');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_booking';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/booking';

        $this->dbView=checkReplication();
        // $this->dbView = $this->load->database("dbView", TRUE);        
        $this->dbAction = $this->load->database("dbAction", TRUE);
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $data=$this->logCi->dataList();
            echo json_encode($data);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Log Error CI di CMS',
            'content'  => 'cmsCiLog/index',
        );

        $this->load->view('default', $data);
    }

}
