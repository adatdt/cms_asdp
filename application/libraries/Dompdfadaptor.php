<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH."libraries/dompdf/autoload.inc.php"; 
use Dompdf\Dompdf;
 
class Dompdfadaptor extends Dompdf { 
    public function __construct() { 
        parent::__construct(); 
    } 
}