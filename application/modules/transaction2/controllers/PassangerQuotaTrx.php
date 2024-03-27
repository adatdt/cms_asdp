<?php
defined('BASEPATH') or exit('No direct script access allowed');
// error_reporting(0);

class PassangerQuotaTrx extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('PassangerQuotaTrxModel', 'quota');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_booking';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction2/passangerQuotaTrx';
        $this->load->library('Html2pdf');

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->quota->dataList();
            echo json_encode($rows);
            exit;

        }


        $getApp=$this->quota->get_identity_app();        


        if($getApp ==0)
        {
            if(!empty($this->session->userdata("port_id")))
            {
                $port=$this->quota->select_data("app.t_mtr_port", " where id=".$this->session->userdata("port_id"))->result();
            }
            else
            {
                $dataPort[""]="Pilih";
                $port=$this->quota->select_data("app.t_mtr_port", " where status != '-5' order by name asc ")->result();
            }
        }
        else
        {
            $port=$this->quota->select_data("app.t_mtr_port", " where id=".$getApp )->result();
        }

        foreach ($port as $key => $value) {
            $dataPort[$this->enc->encode($value->id)]= strtoupper($value->name);
        }

        $getShipClass=$this->quota->select_data("app.t_mtr_ship_class", " where status != '-5' order by name asc ")->result();

        $dataShipClass[""]="Pilih";
        foreach ($getShipClass as $key => $value) {
            $dataShipClass[$this->enc->encode($value->id)]= strtoupper($value->name);
        }

        $dataJam[""]="Pilih";
        for ($i=0; $i <24 ; $i++) { 
            $dataJam[sprintf("%02s",$i)]=sprintf("%02s",$i).":00";
        }



        $data = array(
            'home'              => 'Home',
            'url_home'          => site_url('home'),
            'title'             => 'Transaksi kuota Penumpang',
            'content'           => 'passangerQuotaTrx/index',
            'dataJam'           => $dataJam,
            'port'              =>$dataPort,
            'shipClass'         =>$dataShipClass,
            'btn_excel'         => checkBtnAccess($this->_module, 'download_excel'),
            'btn_pdf'           => checkBtnAccess($this->_module, 'download_pdf'),
        );

        $this->load->view('default', $data);
    }


  
}
