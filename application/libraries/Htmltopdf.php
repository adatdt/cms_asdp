<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH."libraries/Html2pdf/Html2pdf.php"; 
 
class Htmtopdf extends Html2pdf { 
    public function __construct() { 
        parent::__construct(); 
    } 
}
