<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Description of Nutech_Pdf
 *
 * @author dayungjaya.nutech@gmail.com
 */
require_once APPPATH."libraries/dompdf/autoload.inc.php"; 
use Dompdf\Dompdf;

class New_pdf {

  function save($content, $save_path) {
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->load_html($content);
    $dompdf->render();
    $output = $dompdf->output();
    file_put_contents($save_path, $output);
  }

}

?>
