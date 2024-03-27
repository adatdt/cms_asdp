<?php
/**
 * Module   : Reports
 * Author   : ttg <blekedeg@gmail.com>
 */
class Penyerahan_hasil_penjualan extends MY_Controller {

  public function __construct(){
    parent::__construct();
    // $this->load->model('sales_passengers_model');

    $this->load->library('Html2pdf');

    $this->_module   = 'laporan/penyerahan_hasil_penjualan';

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
      'title' => 'Penyerahan Hasil Penjualan',
      'content' => 'penyerahan_hasil_penjualan/index',
      'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
      'download_excel' => checkBtnAccess($this->_module,'download_excel'),
    );

    $this->load->view ('default', $data);
  }

    function download_pdf()
  {
    $this->load->view('laporan/penyerahan_hasil_penjualan/pdf');
  }
}