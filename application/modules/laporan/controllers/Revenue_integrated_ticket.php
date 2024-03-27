<?php
/**
 * Module   : Reports
 * Author   : ttg <blekedeg@gmail.com>
 */
class Revenue_integrated_ticket extends MY_Controller {

  public function __construct(){
    parent::__construct();
    // $this->load->model('sales_passengers_model');
    $this->_module   = 'laporan/revenue_integrated_ticket';

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
      'title' => 'Produksi dan Pendapatan Tiket Terpadu Terjual',
      'content' => 'revenue_integrated_ticket/index',
      'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
      'download_excel' => checkBtnAccess($this->_module,'download_excel'),
    );

    $this->load->view ('default', $data);
  }

}
