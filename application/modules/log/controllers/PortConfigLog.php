<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PortConfigLog extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('PortConfigLogModel','logPortConfig');
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
            $data=$this->logPortConfig->dataList();
            echo json_encode($data);
            exit;
        }        

        
        $getPort = $this->logPortConfig->select_data("app.t_mtr_port"," where status<>'-5' order by name asc ")->result();
        $port['']='Pilih';
        foreach ($getPort as $key => $value) {
            $port[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $getShipClass = $this->logPortConfig->select_data("app.t_mtr_ship_class"," where status<>'-5' order by name asc ")->result();
        $shipClass['']='Pilih';
        foreach ($getShipClass as $key => $value) {
            $shipClass[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'btn_excel'=>"",
            'title'    => 'Log Port Config',
            'port' => $port,
            'shipClass'=>$shipClass,
            'content'  => 'portConfigLog/index',
        );

        $this->load->view('default', $data);
    }

}
