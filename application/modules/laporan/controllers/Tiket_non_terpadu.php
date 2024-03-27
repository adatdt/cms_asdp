<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tiket_non_terpadu extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->_module   = 'laporan/tiket_non_terpadu';
	}

	public function index()
	{
		checkUrlAccess(uri_string(),'view');

    	$data = array(
    		'home' => 'Beranda',
    		'url_home' => site_url('home'),
    		'title' => 'Penjualan Tiket Non Terpadu',
    		'content' => 'tiket_non_terpadu/index',
            'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
            'download_excel' => checkBtnAccess($this->_module,'download_excel'),
    	);

    	$this->load->view ('default', $data);
	}

}

/* End of file Tiket_non_terpadu.php */
/* Location: ./application/controllers/Tiket_non_terpadu.php */