<?php
/**
 * Module   : Reports
 * Author   : ttg <blekedeg@gmail.com>
 */
class Sales_passengers extends MY_Controller {

  public function __construct(){
    parent::__construct();
    // $this->load->model('sales_passengers_model');
    $this->_module   = 'laporan/sales_passengers';

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
      'title' => 'Penjualan dan Posisi Tiket Loket Penumpang',
      'content' => 'sales_passengers/index',
      'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
      'download_excel' => checkBtnAccess($this->_module,'download_excel'),
    );

    $this->load->view ('default', $data);
  }

}
