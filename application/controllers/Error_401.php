<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Error_401 extends CI_Controller {
	public function index()
	{
		$this->load->view('unauthorized_401');
	}
}
