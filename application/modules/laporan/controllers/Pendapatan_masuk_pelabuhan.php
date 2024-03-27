<?php

error_reporting(0);
class Pendapatan_masuk_pelabuhan extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pendapatan_masuk_pelabuhan_model', 'pendapatan_masuk');
        $this->load->model('global_model');
        $this->_module   = 'laporan/pendapatan_masuk_pelabuhan';
        $this->load->library('Html2pdf');
        $this->report_name = "tiket_terjual";
        $this->report_code = $this->global_model->get_report_code($this->report_name);

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);
    }

    function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->pendapatan_masuk->dataList();
            echo json_encode($rows);
            exit;
        }

        $ticketType[""]="SEMUA";
		$ticketType[$this->enc->encode(1)]="NORMAL";
		$ticketType[$this->enc->encode(3)]="MANUAL";      

        $data = array(
            'home'       => 'Beranda',
            'url_home' => site_url('home'),
            'title'   => 'Pendapatan Pas Masuk Pelabuhan Per-shift',
            'content' => 'pendapatan_masuk_pelabuhan/index2',
            'branch'  => $this->global_model->select_data("app.t_mtr_branch", "where status in (1) order by branch_name asc")->result(),
            'port'    => $this->global_model->select_data("app.t_mtr_port", "where status=1 order by id asc")->result(),
            'shift'   => $this->global_model->select_data("app.t_mtr_shift", "where status in (1) order by shift_name asc")->result(),
            'class'   => $this->option_shift_class(),
            'ticketType'=>$ticketType,
        );

        $this->load->view('default', $data);
    }

    private function option_shift_class()
    {
        $shift_class = $this->pendapatan_masuk->getClassBySession('option');
        foreach ($shift_class as $row) {
            if ($row['id'] != '') {
                $id = $this->enc->encode($row['id']);
            } else {
                $id = '';
            }
            $html .= '<option value="' . $id . '">' . $row['name'] . '</option>';
        }
        return $html;
    }

    function old_detail($param)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'detail');

        $param = $this->enc->decode($param);

        if (!$param) {
            redirect('/');
        } else {
            $param = explode('|', $param);
            $data['title'] = 'FORMULIR LAPORAN PENDAPATAN PAS MASUK PELABUHAN PER-SHIFT';
            $data['pass']   = $this->pendapatan_masuk->list_detail_passanger($param);
            $data['veh']    = $this->pendapatan_masuk->list_detail_vehicle($param);
            $data['param']  = $param;

            $this->load->view($this->_module . '/detail_modal', $data);
        }
    }

    function detail()
    {
        validate_ajax();
        $port = $this->enc->decode($this->input->post('port'));
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $ticketType     = $this->enc->decode($this->input->post('ticketType'));
		$ticketTypeku     = $this->input->post('ticketTypeku');
        // $ship_class_id = $this->enc->decode($this->input->post('ship_class'));
        $shift_id = $this->enc->decode($this->input->post('shift'));
        $cek_sc = $this->pendapatan_masuk->getClassBySession();
        if ($cek_sc == false) {
            $ship_class_id = $this->enc->decode($this->input->post('ship_class'));
        } else {
            $ship_class_id = $cek_sc['id'];
        }

        $pelabuhan = ($this->input->post('pelabuhan') != "") ? $this->input->post('pelabuhan') : "-";
        $shift_name = ($this->input->post('shift_name') != "") ? $this->input->post('shift_name') : "-";

        $penumpang = $this->pendapatan_masuk->list_detail_passanger($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);
        $kendaraan = $this->pendapatan_masuk->list_detail_vehicle($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);

        $lintasan = "Semua";
        $regu = "Semua";
        $cabang = "Semua";

        $keterangan_report = "DRAFT";

        $status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift_id, $ship_class_id);

        if ($status_approve) {
            $keterangan_report = "APPROVED";
        }

        if ($port != "") {
            $get_rute = $this->global_model->get_rute($port);
            $get_cabang = $this->global_model->get_branch($port, $ship_class_id);
            $get_regu = $this->global_model->get_team($port, $datefrom, $dateto, $shift_id);

            $implode_cabang = implode(', ', json_decode($get_cabang->branch_name));
            $cabang = ($implode_cabang != null) ? $implode_cabang : "-";

            $implode_regu = implode(', ', json_decode($get_regu->team_name));
            $regu = ($implode_regu != null) ? $implode_regu : "-";
            $lintasan = $get_rute->origin . " - " . $get_rute->destination;
        }

        if (!$penumpang && !$kendaraan) {
            $data =  array(
                'code' => 101,
                'message' => "Tidak ada data",
            );

            echo json_encode($data);
            exit;
        } else {
            $input = array(
                'code' => 200,
                'status_approve' => $keterangan_report,
                'penumpang' => $penumpang,
                'kendaraan' => $kendaraan,
                'report_title' => "FORMULIR LAPORAN PENDAPATAN PAS MASUK PELABUHAN PER-SHIFT",
                'cabang' => $cabang,
                'pelabuhan' => $pelabuhan,
                'lintasan' => $lintasan,
                'shift' => $shift_name,
                'regu' => $regu,
                'ticketTypeku'=>empty($ticketTypeku)?"Semua":$ticketTypeku,
            );

            echo json_encode($input);
            exit;
        }
    }

    function get_pdf()
    {
        $port = $this->enc->decode($this->input->get('port'));
        $datefrom = $this->input->get('datefrom');
        $dateto = $this->input->get('dateto');
        // $ship_class_id = $this->enc->decode($this->input->get('ship_class'));
        $shift_id = $this->enc->decode($this->input->get('shift'));
        $cek_sc = $this->pendapatan_masuk->getClassBySession();
        $ticketType     = $this->enc->decode($this->input->get('ticketType'));
		$ticketTypeku 	= $this->input->get('ticketTypeku');

        if ($cek_sc == false) {
            $ship_class_id = $this->enc->decode($this->input->get("ship_class"));
            $ship_classku = $this->input->get("ship_classku");
        } else {
            $ship_class_id = $cek_sc['id'];
            $ship_classku = $cek_sc['name'];
        }

        $pelabuhan = ($this->input->get('pelabuhan') != "") ? $this->input->get('pelabuhan') : "-";
        $shift_name = ($this->input->get('shift_name') != "") ? $this->input->get('shift_name') : "-";

        $penumpang = $this->pendapatan_masuk->list_detail_passanger($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);
        $kendaraan = $this->pendapatan_masuk->list_detail_vehicle($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);

        $keterangan_report = "DRAFT";

        $status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift_id, $ship_class_id);

        if ($status_approve) {
            $keterangan_report = "APPROVED";
        }

        $lintasan = "Semua";
        $regu = "Semua";
        $cabang = "Semua";

        if ($port != "") {
            $get_rute = $this->global_model->get_rute($port);
            $get_cabang = $this->global_model->get_branch($port, $ship_class_id);
            $get_regu = $this->global_model->get_team($port, $datefrom, $dateto, $shift_id);

            $implode_cabang = implode(', ', json_decode($get_cabang->branch_name));
            $cabang = ($implode_cabang != null) ? $implode_cabang : "-";

            $implode_regu = implode(', ', json_decode($get_regu->team_name));
            $regu = ($implode_regu != null) ? $implode_regu : "-";

            $lintasan = $get_rute->origin . " - " . $get_rute->destination;
        }

        $data['status_approve'] = $keterangan_report;
        $data['cabang'] = $cabang;
        $data['pelabuhan'] = $pelabuhan;
        $data['lintasan'] = $lintasan;
        $data['shift'] = $shift_name;
        $data['regu'] = $regu;

        $data['report_title'] = "FORMULIR LAPORAN PENDAPATAN PAS MASUK PELABUHAN PER-SHIFT";
        $data['penumpang'] = $penumpang;
        $data['kendaraan'] = $kendaraan;
        $data['ticketTypeku']=empty($ticketTypeku)?"SEMUA":$ticketTypeku;

        $this->load->view($this->_module . '/pdf2', $data);
    }

    function get_excel()
    {
        $port = $this->enc->decode($this->input->get('port'));
        $datefrom = $this->input->get('datefrom');
        $dateto = $this->input->get('dateto');
        // $ship_class_id = $this->enc->decode($this->input->get('ship_class'));
        $shift_id = $this->enc->decode($this->input->get('shift'));
        $cek_sc = $this->pendapatan_masuk->getClassBySession();

        $ticketType     = $this->enc->decode($this->input->get('ticketType'));
		$ticketTypeku 	= $this->input->get('ticketTypeku');

        if ($cek_sc == false) {
            $ship_class_id = $this->enc->decode($this->input->get("ship_class"));
            $ship_classku = $this->input->get("ship_classku");
        } else {
            $ship_class_id = $cek_sc['id'];
            $ship_classku = $cek_sc['name'];
        }

        $pelabuhan = ($this->input->get('pelabuhan') != "") ? $this->input->get('pelabuhan') : "-";
        $shift_name = ($this->input->get('shift_name') != "") ? $this->input->get('shift_name') : "-";

        $penumpang = $this->pendapatan_masuk->list_detail_passanger($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);
        $kendaraan = $this->pendapatan_masuk->list_detail_vehicle($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);

        $keterangan_report = "DRAFT";

        $status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift_id, $ship_class_id);

        if ($status_approve) {
            $keterangan_report = "APPROVED";
        }

        $lintasan = "Semua";
        $regu = "Semua";
        $cabang = "Semua";

        if ($port != "") {
            $get_rute = $this->global_model->get_rute($port);
            $get_cabang = $this->global_model->get_branch($port, $ship_class_id);
            $get_regu = $this->global_model->get_team($port, $datefrom, $dateto, $shift_id);

            $implode_cabang = implode(', ', json_decode($get_cabang->branch_name));
            $cabang = ($implode_cabang != null) ? $implode_cabang : "-";

            $implode_regu = implode(', ', json_decode($get_regu->team_name));
            $regu = ($implode_regu != null) ? $implode_regu : "-";

            $lintasan = $get_rute->origin . " - " . $get_rute->destination;
        }

        $this->load->library('XLSExcel');
        $writer = new XLSXWriter();
        $filename = strtoupper("pendapatan_pas_masuk_" . $pelabuhan . "_" . $datefrom . "_" . $dateto . ".xlsx");

        $writer->setTitle($excel_name);
        $writer->setSubject($excel_name);
        $writer->setAuthor($excel_name);
        $writer->setCompany('ASDP Indonesia Ferry');
        $writer->setDescription($filename);
        $writer->setTempDir(sys_get_temp_dir());

        $sheet1 = $filename;

        $styles1 = array(
            'font' => 'Arial',
            'font-size' => 10,
            'font-style' => 'bold',
            'halign' => 'center',
            'valign' => 'center',
            'border' => 'left,right,top,bottom',
            'border-style' => 'thin',
        );

        $styles2 = array(
            'font' => 'Arial',
            'font-size' => 10,
            'valign' => 'center',
            'border' => 'left,right,top,bottom',
            'border-style' => 'thin',
        );

        $style_header = array(
            'font' => 'Arial',
            'font-size' => 11,
            'font-style' => 'bold',
            'valign' => 'center',
            'border' => 'left,right,top,bottom',
            'border-style' => 'thin',
        );

        $style_sub = array(
            'font' => 'Arial',
            'font-size' => 10,
            'font-style' => 'bold',
            'halign' => 'right',
            'valign' => 'right',
            'border' => 'left,right,top,bottom',
            'border-style' => 'thin',
        );

        $header = array("string", "string", "string", "string", "string");

        $judul_tunai = array(
            "NO.",
            "JENIS TIKET",
            "TARIF (PAS)",
            "PRODUKSI (Lbr)",
            "PENDAPATAN (Rp)",
            "KETERANGAN",
        );

        $produksi_penumpang = 0;
        $pendapatan_penumpang = 0;

        foreach ($penumpang as $key => $value) {
            $produksi_penumpang += $value->ticket_count;
            $pendapatan_penumpang += $value->total_amount;

            $tunais[] = array(
                $key + 1,
                $value->name,
                $value->entry_fee,
                $value->ticket_count,
                $value->total_amount,
                "",
            );
        }

        $produksi_kendaraan = 0;
        $pendapatan_kendaraan = 0;

        foreach ($kendaraan as $key => $value) {
            $produksi_kendaraan += $value->ticket_count;
            $pendapatan_kendaraan += $value->total_amount;

            $cashlessis[] = array(
                $key + 1,
                $value->name,
                $value->entry_fee,
                $value->ticket_count,
                $value->total_amount,
                "",
            );
        }

        $writer->writeSheetRow($sheet1, array("FORMULIR LAPORAN PENDAPATAN PAS MASUK PELABUHAN PER-SHIFT"), $style_header);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("CABANG", $cabang, "", "SHIFT", $shift_name));
        $writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "", "REGU", $regu));
        $writer->writeSheetRow($sheet1, array("LINTASAN", $lintasan, "", "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
        $writer->writeSheetRow($sheet1, array("STATUS", $keterangan_report,"","TIPE TIKET",empty($ticketTypeku)?"Semua":$ticketTypeku));
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("1. PENUMPANG"));
        $writer->writeSheetRow($sheet1, $judul_tunai, $styles1);

        if ($penumpang) {
            foreach ($tunais as $row) {
                $writer->writeSheetRow($sheet1, $row, $styles2);
            }
        }

        $writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_penumpang, $pendapatan_penumpang, ""), $style_sub);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("2. KENDARAAN"));

        if ($kendaraan) {
            foreach ($cashlessis as $row) {
                $writer->writeSheetRow($sheet1, $row, $styles2);
            }
        }

        $writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_kendaraan, $pendapatan_kendaraan, ""), $style_sub);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (Penumpang + Kendaraan)", "", "", $produksi_penumpang + $produksi_kendaraan, $pendapatan_penumpang + $pendapatan_kendaraan, ""), $style_sub);
        $writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 5);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }

    function download_pdf($param)
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_pdf');
        $param = $this->enc->decode($param);

        if (!$param) {
            redirect('/');
        } else {
            $param = explode('|', $param);
            $data['title'] = 'FORMULIR LAPORAN PENDAPATAN PAS MASUK PELABUHAN PER-SHIFT';
            $data['pass']   = $this->pendapatan_masuk->list_detail_passanger($param);
            $data['veh']    = $this->pendapatan_masuk->list_detail_vehicle($param);
            $data['param']  = $param;

            $this->load->view($this->_module . '/pdf', $data);
        }
    }

    function download_excel($param)
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        $param = $this->enc->decode($param);

        if (!$param) {
            redirect('/');
        } else {
            $param = explode('|', $param);
            $judul = 'FORMULIR LAPORAN PENDAPATAN PAS MASUK PELABUHAN PER-SHIFT';
            $pass  = $this->pendapatan_masuk->list_detail_passanger($param);
            $veh   = $this->pendapatan_masuk->list_detail_vehicle($param);

            $excel_name = "PENDAPATAN PAS MASUK PELABUHAN PER-SHIFT";

            $this->load->library('XLSXWriter');
            $writer = new XLSXWriter();
            $filename = $excel_name . '_' . $param[0] . '.xlsx';

            $writer->setTitle($excel_name);
            $writer->setSubject($excel_name);
            $writer->setAuthor($excel_name);
            $writer->setCompany('ASDP Indonesia Ferry');
            $writer->setDescription($excel_name);
            $writer->setTempDir(sys_get_temp_dir());

            $fill  = '#f1f4f7';
            $color = '#000000';

            $style = array(
                'color'       => $color,
                'halign'      => 'center',
                'font'        => 'Arial',
                'border'      => 'left,right,top,bottom',
                'border-style' => 'thin',
                'font-style'  => 'bold',
                'font-size'      => 10,
            );

            $null =  array('', '', '', '', '', '', '', '');
            $writer->writeSheetRow($excel_name, array($judul, '', '', '', '', '', '', ''), $style);

            unset($style['font-style']);
            unset($style['halign']);

            $style['halign'] = 'left';

            $writer->writeSheetRow($excel_name, $null, $style);
            $writer->writeSheetRow($excel_name, array("CABANG", "", strtoupper($param[3]), "", "", "SHIFT", strtoupper($param[7]), ''), $style);
            $writer->writeSheetRow($excel_name, array("PELABUHAN", "", strtoupper($param[4]), "", "", "REGU", strtoupper($param[8]), ''), $style);
            $writer->writeSheetRow($excel_name, array("LINTASAN", "", strtoupper($param[5]) . " - " . strtoupper($param[6]), "", "", "TANGGAL", format_date($param[0]), ''), $style);
            $writer->writeSheetRow($excel_name, $null, $style);

            // start_row, start_col, end_row, end_col
            for ($i = 2; $i < 5; $i++) {
                $writer->markMergedCell($excel_name, $i, 0, $i, 1);
                $writer->markMergedCell($excel_name, $i, 2, $i, 3);
                $writer->markMergedCell($excel_name, $i, 6, $i, 7);
            }

            $writer->markMergedCell($excel_name, 0, 0, 0, 7);
            $writer->markMergedCell($excel_name, 1, 0, 1, 7);
            $writer->markMergedCell($excel_name, 2, 4, 4, 4);
            $writer->markMergedCell($excel_name, 5, 0, 5, 7);

            $header = array(
                "NO.",
                "JENIS TIKET",
                "",
                "TARIF (PAS)",
                "PRODUKSI (Lbr)",
                "PENDAPATAN (Rp)",
                "KETERANGAN",
                ""
            );

            $style['halign'] = 'center';

            $col1 = 1;
            $col2 = 2;
            $col6 = 6;
            $col7 = 7;

            $writer->writeSheetRow($excel_name, $header, $style);
            $writer->markMergedCell($excel_name, 6, $col1, 6, $col2);
            $writer->markMergedCell($excel_name, 6, $col6, 6, $col7);

            // print_r($pass['data']);exit;

            // exit;
            $writer->writeSheetRow($excel_name, array(1, 2, '', 3, 4, 5, 6, ''), $style);
            $writer->markMergedCell($excel_name, 7, $col1, 7, $col2);
            $writer->markMergedCell($excel_name, 7, $col6, 7, $col7);

            $writer->writeSheetRow($excel_name, array(1, 'Penumpang', '', '', '', '', '', ''), $style);
            $writer->markMergedCell($excel_name, 8, 1, 8, 7);


            $penumpang = array();
            foreach ($pass['data'] as $key => $val) {
                $penumpang[] = array(
                    '',
                    $val->name,
                    '',
                    $val->entry_fee ? $val->entry_fee : 0,
                    $val->ticket_count ? $val->ticket_count : 0,
                    $val->total_amount ? $val->total_amount : 0,
                    '',
                    ''
                );
            }

            $kendaraan = array();
            foreach ($veh['data'] as $key => $val) {
                $kendaraan[] = array(
                    '',
                    $val->name,
                    '',
                    $val->entry_fee ? $val->entry_fee : 0,
                    $val->ticket_count ? $val->ticket_count : 0,
                    $val->total_amount ? $val->total_amount : 0,
                    '',
                    ''
                );
            }

            unset($style['halign']);
            $m = 9;
            foreach ($penumpang as $row) {
                $writer->writeSheetRow($excel_name, $row, $style);
                $writer->markMergedCell($excel_name, $m, $col1, $m, $col2);
                $writer->markMergedCell($excel_name, $m, $col6, $m, $col7);
                $m++;
            }

            $style['font-style'] = 'bold';
            $writer->writeSheetRow($excel_name, array('', 'Sub Jumlah', '', '', $pass['produksi'], $pass['pendapatan'], '', ''), $style);
            $writer->markMergedCell($excel_name, $m, 1, $m, 3);
            $writer->markMergedCell($excel_name, $m, $col6, $m, $col7);

            $m = $m + 1;
            unset($style['font-style']);
            $style['halign'] = 'center';

            $writer->writeSheetRow($excel_name, array(2, 'Kendaraan', '', '', '', '', '', ''), $style);
            $writer->markMergedCell($excel_name, $m, 1, $m, 7);

            unset($style['halign']);
            $m = $m + 1;
            foreach ($kendaraan as $row) {
                $writer->writeSheetRow($excel_name, $row, $style);
                $writer->markMergedCell($excel_name, $m, $col1, $m, $col2);
                $writer->markMergedCell($excel_name, $m, $col6, $m, $col7);
                $m++;
            }

            $style['font-style'] = 'bold';
            $writer->writeSheetRow($excel_name, array('', 'Sub Jumlah', '', '', $veh['produksi'], $veh['pendapatan'], '', ''), $style);
            $writer->markMergedCell($excel_name, $m, 1, $m, 3);
            $writer->markMergedCell($excel_name, $m, $col6, $m, $col7);

            $writer->writeSheetRow($excel_name, array('Jumlah', '', '', '', $pass['produksi'] + $veh['produksi'], $pass['pendapatan'] + $veh['pendapatan'], '', ''), $style);
            $m = $m + 1;
            $writer->markMergedCell($excel_name, $m, 0, $m, 3);
            $writer->markMergedCell($excel_name, $m, $col6, $m, $col7);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            $writer->writeToStdOut();
        }
    }
}