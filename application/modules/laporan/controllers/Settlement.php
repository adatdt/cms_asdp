<?php
/**
 * -----------------------
 * CLASS NAME : Settlement
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Settlement extends MY_Controller {
 
    function __construct() {
        parent::__construct();
        $this->load->model('settlement_model','model');
        $this->load->model('global_model', 'global');
        $this->_module    = 'laporan/settlement';
        $this->_urlAccess = 'laporan/settlement';
        $this->_username  = $this->session->userdata('username');
        
    }

 	function index() {
        checkUrlAccess(uri_string(),'view');
        $data['title']         = "Settlement";
        $data['home']           = 'Home';
        $data['url_home']       = site_url('home');
        $data['urlDatatables'] = site_url('laporan/settlement/listDetailAll');
        $data['list_status']   = $this->model->list_status();
        $data['list_status_rf']   = $this->model->list_status_rf();
        $data['dropdown_status']= $this->listStatus();
        $data['port']          = $this->global->select_data("app.t_mtr_port", "where status = 1")->result();
        $data['status']        = $this->model->dropdown_status();
        $data['bank']          = $this->model->dropdown_bank();
        $data['shift']         = $this->global->select_data("app.t_mtr_shift", "where status = 1")->result();
        $data['dropdownBank']  = $this->model->checkUserBank()->num_rows();
        $data['btnDownload']   = createBtnDownloadByClass(uri_string());
        $data['tab']           = array('Summary Settlement','Detail Settlement','Detail Settlement Bank','Detail All','Summary Settlement Perbulan');
        $data['fill_tab']      = array('summary','detail','detail_bank','detail_all','detail_bank_perbulan');
        $data['content']       = "settlement/index";

        $this->load->view ('default', $data);
    }

    function listDetailAll(){
        validate_ajax();
        echo json_encode($this->model->getList());
    }

    function listSettlement(){
        validate_ajax();

        $data = array(
            'post' => $this->input->post(),
            'summary' => $this->model->listSummarySettlement(),
            'detail' => $this->model->listDetailSettlement(),
            'detail_bank' => $this->model->listDetailBankSettlement(),
            // 'detail_perbulan' => $this->model->listDetailSettlementPerbulan(),
            'detail_bank_perbulan' => $this->model->listDetailBankSettlementPerbulan(),
            // 'rekap_fs' => $this->model->listRekapFS(),
        );

        echo $res=json_api(1,'List settlement',$data);
         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'laporan/settlement';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        // $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        
    }

    function download_summary($data){

        if(!is_array($data)){
            show_404();
            return false;
        }

        $this->load->library('XLSXWriter');
        $writer = new XLSXWriter();

        $post = (object) $this->input->post();
        $sheet_name = 'Sheet 1'; 
        $fill = '#f1f4f7';
        $color = '#000000';
        $setTitleFile = 'Settlement_('.$post->tab_name.')_'.$post->start_date.'_'.$post->end_date;

        $writer->setTitle($setTitleFile);
        $writer->setSubject('Settlement');
        $writer->setAuthor($this->_username);
        $writer->setCompany('Nutech Integrasi');
        $writer->setDescription($setTitleFile);
        $writer->setKeywords('');
        $col_options = array(
            'widths'      => array(25,25,25),
            'fill'        => $fill,
            'color'       => $color,
            'halign'      => 'center',
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        $writer->writeSheetHeader(
            $sheet_name,
            $rowdata = array('Status' => 'string', 'Total Transaction' => 'integer', 'Nominal' => '#,##0.00'), 
            $col_options
        );

        $excel = array();
        foreach ($data['data'] as $key => $val) {
            $excel[] = array(
                $val->status_name,
                $val->count,
                $val->sum
            );
        }

        $style = array(
            'font'        => 'Arial',
            'font-size'   => 10,
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        // print_r($data['data']);exit;

        foreach($excel as $row){
            $writer->writeSheetRow($sheet_name, $row, $style);
        }

        unset($col_options['halign']);
        $writer->writeSheetRow($sheet_name, array('Total',$data['total']['total_volume'],$data['total']['total_revenue']), $col_options);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$setTitleFile.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
        exit;
    }

    function download_detail($data){
        if(!is_array($data)){
            show_404();
            return false;
        }

        $this->load->library('XLSXWriter');
        $writer = new XLSXWriter();

        $post = (object) $this->input->post();
        $sheet_name = 'Sheet 1'; 
        $fill = '#f1f4f7';
        $color = '#000000';
        $setTitleFile = 'Settlement_('.$post->tab_name.')_'.$post->start_date.'_'.$post->end_date;

        $writer->setTitle($setTitleFile);
        $writer->setSubject('Settlement');
        $writer->setAuthor($this->_username);
        $writer->setCompany('Nutech Integrasi');
        $writer->setDescription($setTitleFile);
        $writer->setKeywords('');

        $style = array(
            'fill'        => $fill,
            'color'       => $color,
            'halign'      => 'center',
            'valign'      => 'center',
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        $dataStatus = $data['status'];

        $arr = array('Date');
        $arr2 = array('');

        $total = array('Total');
        $trx = $data['data']['total']['trx'];
        $nom = $data['data']['total']['nominal'];

        $a = 0;
        $n = 0;

        for ($i=0; $i < count($dataStatus) * 2; $i++) { 
            if($i % 2 == 0){
                $arr2[] = 'TRX';
                $arr[]  = $dataStatus[$a]->status_name;
                $total[]= $trx[$dataStatus[$a]->status_code];
                $a++;
            }else{
                $arr[] = '';
                $arr2[] = '(Rp)';
                $total[] = $nom[$dataStatus[$n]->status_code];
                $n++;
            }
        }

        $arr[] = 'Total';
        $arr[] = '';

        $arr2[] = 'TRX';
        $arr2[] = '(Rp)';

        $total[] = $trx['total'];
        $total[] = $nom['total'];

        $writer->writeSheetRow($sheet_name, $arr, $style);
        $writer->writeSheetRow($sheet_name, $arr2, $style);

        // merger date
        $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = 0, $end_row = 1, $end_col = 0);  //date

        // merger status
        for ($i=0; $i < count($arr); $i++) { 
            if($i % 2 == 0){
                $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = $i+1, $end_row = 0, $end_col = $i+2);
            }
        }

        $excel = array();
        foreach ($data['data']['data'] as $key => $val) {
            $excel[$key] = array(
                $val->dates
            );

            for ($i=0; $i < count($dataStatus); $i++){
                array_push($excel[$key], $val->status[$dataStatus[$i]->status_code]['trx']);
                array_push($excel[$key], $val->status[$dataStatus[$i]->status_code]['nominal']);
            }

            array_push($excel[$key], $val->total_trx, $val->total_nom);
        }

        $style2 = array(
            'font'        => 'Arial',
            'font-size'   => 10,
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        foreach($excel as $row){
            $writer->writeSheetRow($sheet_name, $row, $style2);
        }


        // totals bottom
        unset($style['halign']);
        unset($style['valign']);
        $writer->writeSheetRow($sheet_name, $total, $style);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$setTitleFile.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
        exit;
    }

    function download_detail_perbulan($data){
        if(!is_array($data)){
            show_404();
            return false;
        }

        $this->load->library('XLSXWriter');
        $writer = new XLSXWriter();

        $post = (object) $this->input->post();
        $sheet_name = 'Sheet 1'; 
        $fill = '#f1f4f7';
        $color = '#000000';
        $setTitleFile = 'Settlement_('.$post->tab_name.')_'.$post->start_date.'_'.$post->end_date;

        $writer->setTitle($setTitleFile);
        $writer->setSubject('Settlement');
        $writer->setAuthor($this->_username);
        $writer->setCompany('Nutech Integrasi');
        $writer->setDescription($setTitleFile);
        $writer->setKeywords('');

        $style = array(
            'fill'        => $fill,
            'color'       => $color,
            'halign'      => 'center',
            'valign'      => 'center',
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        $dataStatus = $data['status'];

        $arr = array('Bulan');
        $arr2 = array('');

        $total = array('Total');
        $trx = $data['data']['total']['trx'];
        $nom = $data['data']['total']['nominal'];

        $a = 0;
        $n = 0;

        for ($i=0; $i < count($dataStatus) * 2; $i++) { 
            if($i % 2 == 0){
                $arr2[] = 'TRX';
                $arr[]  = $dataStatus[$a]->status_name;
                $total[]= $trx[$dataStatus[$a]->status_code];
                $a++;
            }else{
                $arr[] = '';
                $arr2[] = '(Rp)';
                $total[] = $nom[$dataStatus[$n]->status_code];
                $n++;
            }
        }

        $arr[] = 'Total';
        $arr[] = '';

        $arr2[] = 'TRX';
        $arr2[] = '(Rp)';

        $total[] = $trx['total'];
        $total[] = $nom['total'];

        $writer->writeSheetRow($sheet_name, $arr, $style);
        $writer->writeSheetRow($sheet_name, $arr2, $style);

        // merger date
        $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = 0, $end_row = 1, $end_col = 0);  //date

        // merger status
        for ($i=0; $i < count($arr); $i++) { 
            if($i % 2 == 0){
                $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = $i+1, $end_row = 0, $end_col = $i+2);
            }
        }

        $excel = array();
        foreach ($data['data']['data'] as $key => $val) {
            $excel[$key] = array(
                $val->dates
            );

            for ($i=0; $i < count($dataStatus); $i++){
                array_push($excel[$key], $val->status[$dataStatus[$i]->status_code]['trx']);
                array_push($excel[$key], $val->status[$dataStatus[$i]->status_code]['nominal']);
            }

            array_push($excel[$key], $val->total_trx, $val->total_nom);
        }

        $style2 = array(
            'font'        => 'Arial',
            'font-size'   => 10,
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        foreach($excel as $row){
            $writer->writeSheetRow($sheet_name, $row, $style2);
        }


        // totals bottom
        unset($style['halign']);
        unset($style['valign']);
        $writer->writeSheetRow($sheet_name, $total, $style);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$setTitleFile.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
        exit;
    }

    function download_detail_bank($data){
        if(!is_array($data)){
            show_404();
            return false;
        }

        $this->load->library('XLSXWriter');
        $writer = new XLSXWriter();

        $post = (object) $this->input->post();
        $sheet_name = 'Sheet 1'; 
        $fill = '#f1f4f7';
        $color = '#000000';
        $setTitleFile = 'Settlement_('.$post->tab_name.')_'.$post->start_date.'_'.$post->end_date;

        $writer->setTitle($setTitleFile);
        $writer->setSubject('Settlement');
        $writer->setAuthor($this->_username);
        $writer->setCompany('Nutech Integrasi');
        $writer->setDescription($setTitleFile);
        $writer->setKeywords('');

        $style = array(
            'fill'        => $fill,
            'color'       => $color,
            'halign'      => 'center',
            'valign'      => 'center',
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        $dataStatus = $data['status'];

        $arr = array('Date','Bank');
        $arr2 = array('','');

        $total = array('','Total');
        $trx = $data['data']['total']['trx'];
        $nom = $data['data']['total']['nominal'];

        $a = 0;
        $n = 0;

        for ($i=0; $i < count($dataStatus) * 2; $i++) { 
            if($i % 2 == 0){
                $arr2[] = 'TRX';
                $arr[]  = $dataStatus[$a]->status_name;
                $total[]= $trx[$dataStatus[$a]->status_code];
                $a++;
            }else{
                $arr[] = '';
                $arr2[] = '(Rp)';
                $total[] = $nom[$dataStatus[$n]->status_code];
                $n++;
            }
        }

        $arr[] = 'Total';
        $arr[] = '';

        $arr2[] = 'TRX';
        $arr2[] = '(Rp)';

        $total[] = $trx['total'];
        $total[] = $nom['total'];

        $writer->writeSheetRow($sheet_name, $arr, $style);
        $writer->writeSheetRow($sheet_name, $arr2, $style);

        // merger date
        $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = 0, $end_row = 1, $end_col = 0);  //date
        $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = 1, $end_row = 1, $end_col = 1);  //date

        // merger status
        for ($i=0; $i < count($arr); $i++) { 
            if($i % 2 == 0){
                $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = $i+2, $end_row = 0, $end_col = $i+3);
            }
        }

        $excel = array();
        foreach ($data['data']['data'] as $key => $val) {
            $excel[$key] = array(
                $val->dates,
                $val->bank_name,
            );

            for ($i=0; $i < count($dataStatus); $i++){
                array_push($excel[$key], $val->status[$dataStatus[$i]->status_code]['trx']);
                array_push($excel[$key], $val->status[$dataStatus[$i]->status_code]['nominal']);
            }

            array_push($excel[$key], $val->total_trx, $val->total_nom);
        }

        $style2 = array(
            'font'        => 'Arial',
            'font-size'   => 10,
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        foreach($excel as $row){
            $writer->writeSheetRow($sheet_name, $row, $style2);
        }


        // totals bottom
        unset($style['halign']);
        unset($style['valign']);
        $writer->writeSheetRow($sheet_name, $total, $style);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$setTitleFile.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
        exit;
    }

    function download_detail_bank_perbulan($data){
        if(!is_array($data)){
            show_404();
            return false;
        }

        $this->load->library('XLSXWriter');
        $writer = new XLSXWriter();

        $post = (object) $this->input->post();
        $sheet_name = 'Sheet 1'; 
        $fill = '#f1f4f7';
        $color = '#000000';
        $setTitleFile = 'Settlement_('.$post->tab_name.')_'.$post->start_date.'_'.$post->end_date;

        $writer->setTitle($setTitleFile);
        $writer->setSubject('Settlement');
        $writer->setAuthor($this->_username);
        $writer->setCompany('Nutech Integrasi');
        $writer->setDescription($setTitleFile);
        $writer->setKeywords('');

        $style = array(
            'fill'        => $fill,
            'color'       => $color,
            'halign'      => 'center',
            'valign'      => 'center',
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        $dataStatus = $data['status'];

        $arr = array('Bulan','Bank');
        $arr2 = array('','');

        $total = array('','Total');
        $trx = $data['data']['total']['trx'];
        $nom = $data['data']['total']['nominal'];

        $a = 0;
        $n = 0;

        for ($i=0; $i < count($dataStatus) * 2; $i++) { 
            if($i % 2 == 0){
                $arr2[] = 'TRX';
                $arr[]  = $dataStatus[$a]->status_name;
                $total[]= $trx[$dataStatus[$a]->status_code];
                $a++;
            }else{
                $arr[] = '';
                $arr2[] = '(Rp)';
                $total[] = $nom[$dataStatus[$n]->status_code];
                $n++;
            }
        }

        $arr[] = 'Total';
        $arr[] = '';

        $arr2[] = 'TRX';
        $arr2[] = '(Rp)';

        $total[] = $trx['total'];
        $total[] = $nom['total'];

        $writer->writeSheetRow($sheet_name, $arr, $style);
        $writer->writeSheetRow($sheet_name, $arr2, $style);

        // merger date
        $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = 0, $end_row = 1, $end_col = 0);  //date
        $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = 1, $end_row = 1, $end_col = 1);  //date

        // merger status
        for ($i=0; $i < count($arr); $i++) { 
            if($i % 2 == 0){
                $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = $i+2, $end_row = 0, $end_col = $i+3);
            }
        }

        $excel = array();
        foreach ($data['data']['data'] as $key => $val) {
            $excel[$key] = array(
                $val->dates,
                $val->bank_name,
            );

            for ($i=0; $i < count($dataStatus); $i++){
                array_push($excel[$key], $val->status[$dataStatus[$i]->status_code]['trx']);
                array_push($excel[$key], $val->status[$dataStatus[$i]->status_code]['nominal']);
            }

            array_push($excel[$key], $val->total_trx, $val->total_nom);
        }

        $style2 = array(
            'font'        => 'Arial',
            'font-size'   => 10,
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        foreach($excel as $row){
            $writer->writeSheetRow($sheet_name, $row, $style2);
        }


        // totals bottom
        unset($style['halign']);
        unset($style['valign']);
        $writer->writeSheetRow($sheet_name, $total, $style);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$setTitleFile.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
        exit;
    }

    function download_detail_all($data){
        if(!is_array($data)){
            show_404();
            return false;
        }

        $this->load->library('XLSXWriter');
        $writer = new XLSXWriter();

        $post = (object) $this->input->post();
        $sheet_name = 'Sheet 1'; 
        $fill = '#f1f4f7';
        $color = '#000000';
        $setTitleFile = 'Settlement_('.$post->tab_name.')_'.$post->start_date.'_'.$post->end_date;

        $writer->setTitle($setTitleFile);
        $writer->setSubject('Settlement');
        $writer->setAuthor($this->_username);
        $writer->setCompany('Nutech Integrasi');
        $writer->setDescription($setTitleFile);
        $writer->setKeywords('');

        $writer->writeSheetHeader(
            $sheet_name,
            $rowdata = array('Transaction Date' => 'string',
                'Settlement Date' => 'string',
                'TID' => 'string',
                'MID' => 'string',
                'Bank' => 'string',
                'Status' => 'string',
                'Transaction Code' => 'string',
                'Filename' => 'string', 
                'Return Filename' => 'string', 
                'Nominal' => '#,##0.00'), 
            $col_options = array(
                'widths'      => array(20,20,20,20,20,20,20,24,20,20),
                'fill'        => $fill,
                'color'       => $color,
                'halign'      => 'center',
                'border'      => 'left,right,top,bottom',
                'border-style'=> 'thin'
            )
        );

        $style = array(
            'font'        => 'Arial',
            'font-size'   => 10,
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );
        
       

        $excel = array();
        $total = 0;
        foreach ($data as $key => $row) {
            $excel[] = array(
                $row->transaction_date,
                $row->settlement_date,
                $row->terminal_id,
                $row->merchant_id,
                $row->bank_name,
                $row->status_name,
                $row->transaction_code,
                $row->filename,
                $row->return_file_name,
                $row->amount
            );

            $total += $row->amount;
        }

        $style2 = array(
            'font'        => 'Arial',
            'font-size'   => 10,
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        foreach($excel as $row){
            $writer->writeSheetRow($sheet_name, $row, $style2);
        }

        $row_total = array(
            "","","","","","","","","Total",$total
        );
        
        $writer->writeSheetRow($sheet_name, $row_total, $style);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$setTitleFile.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
        exit;
    }

    function download_rekap_fs($data){
        if(!is_array($data)){
            show_404();
            return false;
        }

        $this->load->library('XLSXWriter');
        $writer = new XLSXWriter();

        $post = (object) $this->input->post();
        $sheet_name = 'Sheet 1'; 
        $fill = '#f1f4f7';
        $color = '#000000';
        $setTitleFile = 'Settlement_('.$post->tab_name.')_'.$post->start_date.'_'.$post->end_date;

        $writer->setTitle($setTitleFile);
        $writer->setSubject('Settlement');
        $writer->setAuthor($this->_username);
        $writer->setCompany('Nutech Integrasi');
        $writer->setDescription($setTitleFile);
        $writer->setKeywords('');

        $style = array(
            'fill'        => $fill,
            'color'       => $color,
            'halign'      => 'center',
            'valign'      => 'center',
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        $dataStatus = $data['status'];

        $arr = array('Date','Bank','Filename');
        $arr2 = array('','','');

        $total = array('','','Total');
        $trx = $data['data']['total']['trx'];
        $nom = $data['data']['total']['nominal'];

        $a = 0;
        $n = 0;

        for ($i=0; $i < count($dataStatus) * 2; $i++) { 
            if($i % 2 == 0){
                $arr2[] = 'TRX';
                $arr[]  = $dataStatus[$a]->status_name;
                $total[]= $trx[$dataStatus[$a]->status_code];
                $a++;
            }else{
                $arr[] = '';
                $arr2[] = '(Rp)';
                $total[] = $nom[$dataStatus[$n]->status_code];
                $n++;
            }
        }

        // $arr[] = 'Total';
        // $arr[] = '';

        // $arr2[] = 'TRX';
        // $arr2[] = '(Rp)';

        // $total[] = $trx['total'];
        // $total[] = $nom['total'];

        $writer->writeSheetRow($sheet_name, $arr, $style);
        $writer->writeSheetRow($sheet_name, $arr2, $style);

        // merger date
        $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = 0, $end_row = 1, $end_col = 0);  //date
        // merger filename
        $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = 1, $end_row = 1, $end_col = 1);  //bank
        // merger bank
        $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = 2, $end_row = 1, $end_col = 2);  //filename

        // merger status
        for ($i=0; $i < count($arr); $i++) { 
            if($i % 2 == 0){
                $writer->markMergedCell($sheet_name, $start_row = 0, $start_col = $i+3, $end_row = 0, $end_col = $i+4);
            }
        }

        $excel = array();
        foreach ($data['data']['data'] as $key => $val) {
            $excel[$key] = array(
                $val->dates,
                $val->bank_name,
                $val->filename,
            );

            for ($i=0; $i < count($dataStatus); $i++){
                array_push($excel[$key], $val->status[$dataStatus[$i]->status_code]['trx']);
                array_push($excel[$key], $val->status[$dataStatus[$i]->status_code]['nominal']);
            }

            // array_push($excel[$key], $val->total_trx, $val->total_nom);
        }

        $style2 = array(
            'font'        => 'Arial',
            'font-size'   => 10,
            'border'      => 'left,right,top,bottom',
            'border-style'=> 'thin'
        );

        foreach($excel as $row){
            $writer->writeSheetRow($sheet_name, $row, $style2);
        }


        // totals bottom
        unset($style['halign']);
        unset($style['valign']);
        $writer->writeSheetRow($sheet_name, $total, $style);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$setTitleFile.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
        exit;
    }

    function download_excel(){
        ini_set('memory_limit', '-1');
        $post = $this->input->post();
        // print_r($post);exit();

        if(!isset($post['start_date']) || 
            !isset($post['end_date']) || 
            !isset($post['bank']) || 
            !isset($post['date_type']) || 
            !isset($post['date_type_name']) || 
            !isset($post['tab_name']) || 
            !isset($post['type'])
        ){
            show_404();
            return false;
        }

        $this->log_activitytxt->createLog($this->_username, $this->uri->uri_string(), 'Download report excel settlement', json_encode($post),'-');
        checkUrlAccess($this->_urlAccess,'download_excel');
        $post = (object) $this->input->post();

        if($post->type == 1){
            $data = $this->model->listSummarySettlement();
            // print_r($data);exit();
            $this->download_summary($data);
        }elseif($post->type == 2){
            $data = $this->model->listDetailSettlement();
            $this->download_detail(array(
                'data' => $data,
                'status' => $this->model->list_status()
            ));
        }elseif($post->type == 3){
            $data = $this->model->listDetailBankSettlement();
            $this->download_detail_bank(array(
                'data' => $data,
                'status' => $this->model->list_status()
            ));
        }elseif($post->type == 4){
            $data = $this->model->listDownloadDetails();
            $this->download_detail_all($data);
        }

        // elseif($post->type == 5){
        //     $data = $this->model->listDetailSettlementPerbulan();
        //     $this->download_detail_perbulan(array(
        //         'data' => $data,
        //         'status' => $this->model->list_status()
        //     ));
        // }

        elseif($post->type == 5){
            $data = $this->model->listDetailBankSettlementPerbulan();
            $this->download_detail_bank_perbulan(array(
                'data' => $data,
                'status' => $this->model->list_status()
            ));
        }

        // elseif($post->type == 6){
        //     $data = $this->model->listRekapFS();
        //     $this->download_rekap_fs(array(
        //         'data' => $data,
        //         'status' => $this->model->list_status_rf()
        //     ));
        // }
    }

    function download_pdf(){
        $this->load->library('Html2pdf');
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $post = $this->input->post();

        if(!isset($post['start_date']) || 
            !isset($post['end_date']) ||
            !isset($post['type'])
        ){
            show_404();
            return false;
        }

        checkUrlAccess($this->_urlAccess,'download_pdf');
        $post = (object) $this->input->post();

        if($post->type == 1){
            $row = $this->model->listSummarySettlement();
        }
        elseif($post->type == 2){
            $row = $this->model->listDetailSettlement();
        }elseif($post->type == 3){
            $row = $this->model->listDetailBankSettlement();
        }elseif($post->type == 4){
            $row = $this->model->listDownloadDetails();
        }

        // elseif($post->type == 5){
        //     $row = $this->model->listDetailSettlementPerbulan();
        // }

        elseif($post->type == 5){
            $row = $this->model->listDetailBankSettlementPerbulan();
        }elseif($post->type == 6){
            $row = $this->model->listRekapFS();
        }
        
        $data['data'] = array(          
            'post'      => $post,
            'status'    => $this->model->list_status(),
            'data'      => $row,
            'author'    => $this->_username
        );

        // check port name
        $port_id = $this->session->userdata('port_id') != '' ? $this->session->userdata('port_id') : $this->enc->decode($post->port);
        if ($port_id) {
            $portName = $this->global->select_data("app.t_mtr_port", "where status = 1 and id = {$port_id}")->row();
            $data['port_name'] = $portName->name;
        } else {
            $data['port_name'] = 'Semua Cabang';
        }

        // check bank name
        if ($this->model->checkUserBank()->num_rows() > 0)
        {
            $bank_user=$this->model->checkUserBank()->row();            
        } 

        $bank_id = $this->enc->decode($post->bank) != '' ? $this->enc->decode($post->bank) : $bank_user->id; 
        if ($bank_id) {
            $bankName = $this->global->select_data("core.t_mtr_bank", "where status = 1 and id = {$bank_id}")->row();
            $data['bank_name'] = $bankName->bank_name;
        } else {
            $data['bank_name'] = 'Semua Bank';
        }
        // print_r($data);exit();

        $this->log_activitytxt->createLog($this->_username, $this->uri->uri_string(), 'Download report pdf settlement', json_encode($post),'-');
        $this->load->view($this->_module.'/pdf', $data);
    }

    function listStatus(){
        $query = $this->model->list_status();

        $data = array('' => 'All Status');
        foreach ($query as $key => $value) {
            $data[$value->status_code] = $value->status_name;
        }

        return $data;
    }
}