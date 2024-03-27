<?php

error_reporting(0);
class Kapal_broken extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('kapal_broken_model', 'model');
        $this->load->model('global_model');
        $this->_module   = 'laporan/kapal_broken';
        $this->load->library('Html2pdf');

        $this->dbView = $this->load->database("dbView", TRUE);
        $this->dbAction = $this->load->database("dbAction", TRUE);        
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        $port  = $this->session->userdata('port_id');
        $where = '';

        if ($port) {
            $where = "AND id = {$port}";
        }

        $data = array(
            'home'               => 'Beranda',
            'url_home'           => site_url('home'),
            'title'              => 'Kapal Broken',
            'content'            => $this->_module . '/index',
            'url'                => site_url('laporan/kapal_broken/get_list_data'),
            'urlDownload'        => site_url('laporan/kapal_broken/download_pdf'),
            'urlDownload_excel'  => site_url('laporan/kapal_broken/download_excel'),
            'port'               => $this->global_model->select_data("app.t_mtr_port", "where status = 1 {$where} order by name asc")->result(),
            'ship'               => $this->global_model->select_data("app.t_mtr_ship", "where status = 1 order by name asc")->result(),
            'shift'              => $this->global_model->select_data("app.t_mtr_shift", "where status = 1 order by id asc")->result(),
            'cek_download_pdf'   => checkBtnAccess(uri_string(), 'download_pdf'),
            'cek_download_excel' => checkBtnAccess(uri_string(), 'download_excel'),
        );

        $this->load->view('default', $data);
    }

    function get_list_data()
    {
        validate_ajax();

        $this->form_validation
            ->set_rules('start_date', 'Tanggal Awal', 'trim|required')
            ->set_rules('end_date', 'Tanggal Akhir', 'trim|required')
            ->set_rules('port', 'Nama Pelabuhan', 'trim|required')
            ->set_rules('ship', 'Nama Kapal', 'trim|required');
        $this->form_validation->set_message('required', '%s harus diisi!');

        if ($this->form_validation->run() == FALSE) {
            $response = json_api(0, validation_errors());
        } else {
            $response = json_api(1, 'List Report Broken', $this->model->dataList());
        }

        $this->log_activitytxt->createLog($this->_session->name, uri_string(), 'report broken', json_encode($post), $response);

        echo $response;
        exit;
    }

    function download_pdf()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_pdf');

        $this->form_validation
            ->set_rules('start_date', 'Tanggal Awal', 'trim|required')
            ->set_rules('end_date', 'Tanggal Akhir', 'trim|required')
            ->set_rules('port', 'Nama Pelabuhan', 'trim|required')
            ->set_rules('ship', 'Nama Kapal', 'trim|required');
        $this->form_validation->set_message('required', '%s harus diisi!');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('error_404');
        } else {
            $data['data']         = $this->model->dataList();
            $data['report_title'] = 'LAPORAN KAPAL BROKEN';

            $this->load->view($this->_module . '/pdf', $data);
        }
    }

    function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');

        $this->form_validation
            ->set_rules('start_date', 'Tanggal Awal', 'trim|required')
            ->set_rules('end_date', 'Tanggal Akhir', 'trim|required')
            ->set_rules('port', 'Nama Pelabuhan', 'trim|required')
            ->set_rules('ship', 'Nama Kapal', 'trim|required');
        $this->form_validation->set_message('required', '%s harus diisi!');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('error_404');
        } else {
            $data       = $this->model->dataList();
            $det        = $data['detail'];
            $excel_name = "LAPORAN KAPAL BROKEN";

            $this->load->library('XLSXWriter');
            $writer = new XLSXWriter();
            $filename = strtoupper('Laporan_broken_' . $det->ship_name . "_" . $data['tanggal']) . '.xlsx';

            $writer->setTitle($excel_name);
            $writer->setSubject($excel_name);
            $writer->setAuthor($excel_name);
            $writer->setCompany('ASDP Indonesia Ferry');
            $writer->setDescription($excel_name);
            $writer->setTempDir(sys_get_temp_dir());

            $fill  = '#f1f4f7';
            $color = '#000000';


            $style = array(
                'color'        => $color,
                'halign'       => 'center',
                'font'         => 'Arial',
                'border'       => 'left,right,top,bottom',
                'border-style' => 'thin',
                'font-style'  => 'bold',
                'font-size'   => 10,
            );

            $null =  array('', '', '', '', '', '', '', '', '');
            $writer->writeSheetRow($excel_name, array($excel_name, '', '', '', '', '', '', '', ''), $style);

            unset($style['font-style']);
            unset($style['halign']);

            $style['halign'] = 'left';

            $writer->writeSheetRow($excel_name, $null, $style);
            $writer->writeSheetRow($excel_name, array("CABANG", "", strtoupper($det->origin), "", "", "KAPAL", '', strtoupper($det->ship_name), ''), $style);
            $writer->writeSheetRow($excel_name, array("PELABUHAN", "", strtoupper($det->origin . ' ' . $det->class_name), "", "", "PERUSAHAAN", '', strtoupper($det->company_name), ''), $style);
            $writer->writeSheetRow($excel_name, array("LINTASAN", "", strtoupper($det->origin) . " - " . strtoupper($det->destination), "", "", "GRT", '', $det->ship_grt, ''), $style);
            $writer->writeSheetRow($excel_name, array("TANGGAL SHIFT", "", $data['tanggal'], "", "", "SHIFT", '', $data['shift_name'], ''), $style);

            $writer->writeSheetRow($excel_name, $null, $style);

            // start_row, start_col, end_row, end_col
            for ($i = 2; $i < 5; $i++) {
                $writer->markMergedCell($excel_name, $i, 0, $i, 1);
                $writer->markMergedCell($excel_name, $i, 2, $i, 3);
                $writer->markMergedCell($excel_name, $i, 5, $i, 6);
                $writer->markMergedCell($excel_name, $i, 7, $i, 8);
            }

            $writer->markMergedCell($excel_name, $i, 0, $i, 1);
            $writer->markMergedCell($excel_name, $i, 2, $i, 3);

            $writer->markMergedCell($excel_name, 0, 0, 0, 8);
            $writer->markMergedCell($excel_name, 1, 0, 1, 8);
            $writer->markMergedCell($excel_name, 2, 4, 5, 4);
            $writer->markMergedCell($excel_name, 5, 5, 5, 6);
            $writer->markMergedCell($excel_name, 5, 7, 5, 8);
            $writer->markMergedCell($excel_name, 6, 0, 6, 8);

            $header = array(
                'NO',
                'TANGGAL BROKEN',
                '',
                'DERMAGA',
                'TARIF',
                'PRODUKSI (GRT/CALL)',
                '',
                'PENDAPATAN',
                '',
            );

            $style['halign'] = 'center';

            $col1 = 1;
            $col2 = 2;
            $col5 = 5;
            $col7 = 7;
            $col8 = 8;
            $col6 = 6;
            $style['font-style'] = 'bold';

            $writer->writeSheetRow($excel_name, $header, $style);
            $writer->markMergedCell($excel_name, $col7, $col1, $col7, $col2);
            $writer->markMergedCell($excel_name, $col7, $col7, $col7, $col8);

            $data_excel = array();
            foreach ($data['data'] as $row) {
                $data_excel[] = array(
                    $row->no,
                    $row->date,
                    '',
                    $row->dock_name,
                    $row->dock_fare,
                    $row->ship_grt,
                    '',
                    $row->total,
                    '',
                );
            }

            unset($style['font-style']);
            $m = 8;
            foreach ($data_excel as $row) {
                $writer->writeSheetRow($excel_name, $row, $style);
                $writer->markMergedCell($excel_name, $m, $col1, $m, $col2);
                $writer->markMergedCell($excel_name, $m, $col5, $m, $col6);
                $writer->markMergedCell($excel_name, $m, $col7, $m, $col8);
                $m++;
            }

            $style['font-style'] = 'bold';
            $writer->writeSheetRow($excel_name, array('SUB TOTAL', '', '', '', '', $data['total_broken'], $data['total_grt'], $data['sub_total'], ''), $style);
            $writer->markMergedCell($excel_name, $m, 0, $m, 4);
            $writer->markMergedCell($excel_name, $m, $col5, $m, $col6);
            $writer->markMergedCell($excel_name, $m, $col7, $m, $col8);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $writer->writeToStdOut();
        }
    }
}
