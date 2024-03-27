<?php

/*
  Document   : Vehicle_pos
  Created on : Oct 16, 2018 1:28:36 PM
  Author     : Andedi
  Description: Purpose of the PHP File follows.
 */

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Description of Vehicle_pos
 *
 * @author Andedi
 */
class Vehicle_goshow extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model(array('vehicle_goshow_model'));
    $this->_module   = 'laporan/vehicle_goshow';
  }

  public function index() {
    checkUrlAccess($this->_module,'view');
    $data = array(
        'home' => 'Beranda',
        'url_home' => site_url('home'),
        'title' => 'Laporan Go Show Kendaraan',
        'content' => 'vehicle_goshow/index',
        'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
        'download_excel' => checkBtnAccess($this->_module,'download_excel'),
    );

    $this->load->view('default', $data);
  }
  
  public function get_list() {
    if ($this->input->is_ajax_request()) {
      $post = $this->input->post();
      $result = $this->vehicle_goshow_model->get_list($post);
      echo json_encode($result);
    } else {
      show_404();
    }
  }

}

?>
