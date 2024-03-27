<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LogRefundCs extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('logRefundCsModel','logRefundCs');
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
            $data=$this->logRefundCs->dataList();
            echo json_encode($data);
            exit;
        }        

        $qryService=$this->logRefundCs->select_data("app.t_mtr_service"," where status<>'-5' ")->result();

        $dataService['']='Pilih';

        foreach ($qryService as $key => $value) {
            $dataService[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'btn_excel'=>"",
            'title'    => 'Log Proses Refund CS',
            'lintasan' => $this->logRefundCs->getLintasan(),
            'service'=>$dataService,
            'content'  => 'logRefundCs/index',
        );

        $this->load->view('default', $data);
    }

}
