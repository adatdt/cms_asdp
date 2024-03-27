<?php

class Jasa_pelabuhan extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->_module   = 'laporan/jasa_pelabuhan';

	}

	public function index(){
		checkUrlAccess(uri_string(),'view');

		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Lapooran Jasa Pelabuhan Per-Shift (COA)',
			'content' => 'jasa_pelabuhan/index',
			'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

}