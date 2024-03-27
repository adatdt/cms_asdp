<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Password extends CI_Controller {
	
	public function encryptData($str) {
		$this->load->library('bcrypt');

		$str1 = md5($str);
		echo $this->bcrypt->hash_password($str1);
	}
}
