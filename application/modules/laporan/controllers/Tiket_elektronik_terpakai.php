<?php
/**
 * Module   : Reports
 * Author   : ttg <blekedeg@gmail.com>
 */
class Tiket_elektronik_terpakai extends MY_Controller {

  public function __construct(){
    parent::__construct();
    // $this->load->model('sales_passengers_model');

    $this->load->library('Html2pdf');

    $this->_module   = 'laporan/tiket_elektronik_terpakai';

  }

  public function index(){
    checkUrlAccess(uri_string(),'view');
    if($this->input->is_ajax_request()){
      // $rows = $this->ship_income_model->shipIncomeList();
      // echo json_encode($rows);
      // exit;
    }

   $data = array(
      'home' => 'Beranda',
      'url_home' => site_url('home'),
      'title' => 'Serah terima tiket elektronik terpakai',
      'content' => 'tiket_elektronik_terpakai/index',
      'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
      'download_excel' => checkBtnAccess($this->_module,'download_excel'),
    );

    $this->load->view ('default', $data);
  }

    function download_pdf()
  {
    $this->load->view('laporan/tiket_elektronik_terpakai/pdf');
  }
}