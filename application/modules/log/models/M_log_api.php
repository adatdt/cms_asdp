<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_log_api extends MY_Model{

	public function __construct() {
		parent::__construct();
		$this->_module = 'log_api';
		$this->_db = $this->load->database('default', TRUE);
		// $this->_dbmerak = $this->load->database('dbmerak', TRUE);
	}

}