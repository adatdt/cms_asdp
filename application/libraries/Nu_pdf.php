<?php

/*
  Document   : Nutech_Pdf
  Created on : Sep 10, 2018 2:40:20 PM
  Author     : Andedi
  Description: Purpose of the PHP File follows.
 */

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Description of Nutech_Pdf
 *
 * @author Andedi
 */
require_once('Html2pdf/Html2pdf.php');

class Nu_pdf {

  function download($content) {
    $html2pdf = new Html2Pdf();
    $html2pdf->writeHTML($content);
    $html2pdf->output();
  }

  function save($content, $save_path) {
    $html2pdf = new HTML2PDF();
    $html2pdf->writeHTML($content);
    $html2pdf->Output($save_path, 'F');
  }

}

?>
