<?php
defined('BASEPATH') or exit('No direct script access allowed');
class TransactionStatus extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('transactionStatusModel', 'status');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_booking';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/transactionStatus';

        // $this->dbView = $this->load->database("dbView", TRUE);
        $this->dbView = checkReplication();
        $this->dbAction = $this->load->database("dbAction", TRUE);
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->status->dataList();
            echo json_encode($rows);
            exit;
        }


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Transaksi Status',
            'content'  => 'transactionStatus/index',
        );

        $this->load->view('default', $data);
    }

}
