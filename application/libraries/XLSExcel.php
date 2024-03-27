<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/libraries/xlsxwriter.class.php";
class XLSExcel extends Xlsxwriter {
   public function __construct() {
      parent::__construct();
   }
}