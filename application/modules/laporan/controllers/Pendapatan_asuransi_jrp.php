<?php

error_reporting(0);
class Pendapatan_asuransi_jrp extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pendapatan_asuransi_jrp_model', 'model');
        $this->load->model('global_model');
        $this->_module   = 'laporan/pendapatan_asuransi_jrp';
        $this->load->library('Html2pdf');
        $this->report_name = "tiket_terjual";
        $this->report_code = $this->global_model->get_report_code($this->report_name);

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);        
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->model->dataList();
            echo json_encode($rows);
            exit;
        }

		$ticketType[""]="SEMUA";
		$ticketType[$this->enc->encode(1)]="NORMAL";
		$ticketType[$this->enc->encode(3)]="MANUAL";  


        $data = array(
            'home'             => 'Beranda',
            'url_home'         => site_url('home'),
            'title'         => 'Pendapatan Asuransi JRP (Jasa Raharja Putra)',
            'content'         => $this->_module . '/index2',
            'url_datatables' => current_url(),
            'port'          => $this->global_model->select_data("app.t_mtr_port", "where status = 1 order by id asc")->result(),
            'shift'         => $this->global_model->select_data("app.t_mtr_shift", "where status not in (-5) order by shift_name asc")->result(),
            'class' => $this->option_shift_class(),
            'ticketType'=>$ticketType,
        );

        $this->load->view('default', $data);
    }

    private function option_shift_class()
    {
        $shift_class = $this->model->getClassBySession('option');
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

    public function old_detail($shift_date, $ship_class_id, $shift_id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'detail');

        $shift_date = $this->enc->decode($shift_date);
        $ship_class_id = $this->enc->decode($ship_class_id);
        $shift_id = $this->enc->decode($shift_id);

        if (!$shift_date) {
            $this->load->view('error_404');
            return false;
        }

        $header = $this->model->headerku($shift_date, $ship_class_id, $shift_id);
        $penumpang = $this->model->penumpangku($shift_date, $ship_class_id, $shift_id);
        $kendaraan = $this->model->kendaraanku($shift_date, $ship_class_id, $shift_id);

        $data['header'] = $header;
        $data['report_title'] = "FORMULIR LAPORAN PENDAPATAN ASURANSI TG. JAWAB PENGANGKUT PER-SHIFT";
        $data['penumpang'] = $penumpang;
        $data['kendaraan'] = $kendaraan;

        $this->load->view($this->_module . '/detail_modal', $data);
    }

    public function detail()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'detail');

        $port = $this->enc->decode($this->input->post('port'));
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $ticketType     = $this->enc->decode($this->input->post('ticketType'));
		$ticketTypeku     = $this->input->post('ticketTypeku');

        // $ship_class_id = $this->enc->decode($this->input->post('ship_class'));
        $shift_id = $this->enc->decode($this->input->post('shift'));
        $cek_sc = $this->model->getClassBySession();
        if ($cek_sc == false) {
            $ship_class_id = $this->enc->decode($this->input->post('ship_class'));
        } else {
            $ship_class_id = $cek_sc['id'];
        }

        $pelabuhan = ($this->input->post('pelabuhan') != "") ? $this->input->post('pelabuhan') : "-";
        $shift_name = ($this->input->post('shift_name') != "") ? $this->input->post('shift_name') : "-";

        $penumpang = $this->model->penumpangku($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);
        $kendaraan = $this->model->kendaraanku($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);

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
                'report_title' => "Pendapatan Asuransi JRP (Jasa Raharja Putra)",
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
        $ticketType     = $this->enc->decode($this->input->get('ticketType'));
		$ticketTypeku 	= $this->input->get('ticketTypeku');

        // $ship_class_id = $this->enc->decode($this->input->get('ship_class'));
        $shift_id = $this->enc->decode($this->input->get('shift'));
        $cek_sc = $this->model->getClassBySession();
        if ($cek_sc == false) {
            $ship_class_id = $this->enc->decode($this->input->get("ship_class"));
            $ship_classku = $this->input->get("ship_classku");
        } else {
            $ship_class_id = $cek_sc['id'];
            $ship_classku = $cek_sc['name'];
        }

        $pelabuhan = ($this->input->get('pelabuhan') != "") ? $this->input->get('pelabuhan') : "-";
        $shift_name = ($this->input->get('shift_name') != "") ? $this->input->get('shift_name') : "-";

        $penumpang = $this->model->penumpangku($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);
        $kendaraan = $this->model->kendaraanku($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);

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

        $data['status_approve'] = $keterangan_report;
        $data['cabang'] = $cabang;
        $data['pelabuhan'] = $pelabuhan;
        $data['lintasan'] = $lintasan;
        $data['shift'] = $shift_name;
        $data['regu'] = $regu;

        $data['report_title'] = "FORMULIR LAPORAN PENDAPATAN ASURANSI TG. JAWAB PENGANGKUT PER-SHIFT";
        $data['penumpang'] = $penumpang;
        $data['kendaraan'] = $kendaraan;
        $data['persen_jrp'] = $this->model->persen_jrp();

        $data['ticketTypeku']=empty($ticketTypeku)?"SEMUA":$ticketTypeku;

        $this->load->view($this->_module . '/pdf2', $data);
    }

    function get_excel()
    {
        $port = $this->enc->decode($this->input->get('port'));
        $datefrom = $this->input->get('datefrom');
        $dateto = $this->input->get('dateto');
        $ticketType     = $this->enc->decode($this->input->get('ticketType'));
		$ticketTypeku 	= $this->input->get('ticketTypeku');

        // $ship_class_id = $this->enc->decode($this->input->get('ship_class'));
        $shift_id = $this->enc->decode($this->input->get('shift'));
        $cek_sc = $this->model->getClassBySession();
        if ($cek_sc == false) {
            $ship_class_id = $this->enc->decode($this->input->get("ship_class"));
            $ship_classku = $this->input->get("ship_classku");
        } else {
            $ship_class_id = $cek_sc['id'];
            $ship_classku = $cek_sc['name'];
        }

        $pelabuhan = ($this->input->get('pelabuhan') != "") ? $this->input->get('pelabuhan') : "-";
        $shift_name = ($this->input->get('shift_name') != "") ? $this->input->get('shift_name') : "-";

        $penumpang = $this->model->penumpangku($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);
        $kendaraan = $this->model->kendaraanku($port, $datefrom, $dateto, $ship_class_id, $shift_id, $ticketType);

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

        $this->load->library('XLSExcel');
        $writer = new XLSXWriter();
        $filename = strtoupper("Asuransi_jrp_" . $pelabuhan . "_" . $datefrom . "_" . $dateto . ".xlsx");

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
            "TARIF (JR)",
            "PRODUKSI (Lbr)",
            "PENDAPATAN (Rp)",
            "KETERANGAN",
        );

        $produksi_penumpang = 0;
        $pendapatan_penumpang = 0;

        foreach ($penumpang as $key => $value) {
            $produksi_penumpang += $value->produksi;
            $pendapatan_penumpang += $value->responsibility_fee;

            $tunais[] = array(
                $key + 1,
                $value->golongan,
                $value->harga,
                $value->produksi,
                $value->responsibility_fee,
                "",
            );
        }

        $produksi_kendaraan = 0;
        $pendapatan_kendaraan = 0;

        foreach ($kendaraan as $key => $value) {
            $produksi_kendaraan += $value->produksi;
            $pendapatan_kendaraan += $value->responsibility_fee;

            $cashlessis[] = array(
                $key + 1,
                $value->golongan,
                $value->harga,
                $value->produksi,
                $value->responsibility_fee,
                "",
            );
        }

        $writer->writeSheetRow($sheet1, array("FORMULIR LAPORAN PENDAPATAN ASURANSI TG. JAWAB PENGANGKUT PER-SHIFT"), $style_header);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("CABANG", $cabang, "", "SHIFT", $shift_name));
        $writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "", "REGU", $regu));
        $writer->writeSheetRow($sheet1, array("LINTASAN", $lintasan, "", "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
        $writer->writeSheetRow($sheet1, array("STATUS", $keterangan_report, "", "TIPE TIKET", empty($ticketTypeku)?"Semua":$ticketTypeku));
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

    function download_pdf($shift_date, $ship_class_id, $shift_id)
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_pdf');

        $shift_date = $this->enc->decode($shift_date);
        $ship_class_id = $this->enc->decode($ship_class_id);
        $shift_id = $this->enc->decode($shift_id);

        if (!$shift_date) {
            $this->load->view('error_404');
            return false;
        }

        $header = $this->model->headerku($shift_date, $ship_class_id, $shift_id);
        $penumpang = $this->model->penumpangku($shift_date, $ship_class_id, $shift_id);
        $kendaraan = $this->model->kendaraanku($shift_date, $ship_class_id, $shift_id);

        $data['header'] = $header;
        $data['report_title'] = "FORMULIR LAPORAN PENDAPATAN ASURANSI TG. JAWAB PENGANGKUT PER-SHIFT";
        $data['penumpang'] = $penumpang;
        $data['kendaraan'] = $kendaraan;

        $this->load->view($this->_module . '/pdf', $data);
    }

    function download_excel($shift_date, $ship_class_id, $shift_id)
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        $excel_name = "Asuransi_jrp";

        $shift_date = $this->enc->decode($shift_date);
        $ship_class_id = $this->enc->decode($ship_class_id);
        $shift_id = $this->enc->decode($shift_id);

        $headerkita = $this->model->headerku($shift_date, $ship_class_id, $shift_id);
        $penumpang = $this->model->penumpangku($shift_date, $ship_class_id, $shift_id);
        $kendaraan = $this->model->kendaraanku($shift_date, $ship_class_id, $shift_id);

        $this->load->library('XLSExcel');
        $writer = new XLSXWriter();
        $filename = strtoupper("Asuransi_jrp_" . $shift_date . ".xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

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
        );

        $styles2 = array(
            'font' => 'Arial',
            'font-size' => 10,
            'valign' => 'center',
            'border' => 'left,right,top,bottom',
        );

        $style_header = array(
            'font' => 'Arial',
            'font-size' => 11,
            'font-style' => 'bold',
            'valign' => 'center',
            'border' => 'left,right,top,bottom',
        );

        $style_sub = array(
            'font' => 'Arial',
            'font-size' => 10,
            'font-style' => 'bold',
            'halign' => 'right',
            'valign' => 'right',
            'border' => 'left,right,top,bottom',
        );

        $header = array("string", "string", "string", "string", "string");

        $judul_tunai = array(
            array(
                "NO.",
                "JENIS TIKET",
                "TARIF (JRP)",
                "PRODUKSI (Lbr)",
                "PENDAPATAN (Rp)",
                "KETERANGAN",
            ),
        );

        $produksi_penumpang = 0;
        $pendapatan_penumpang = 0;

        foreach ($penumpang as $key => $value) {
            $produksi_penumpang += $value->produksi;
            $pendapatan_penumpang += $value->responsibility_fee;

            $tunais[] = array(
                $key + 1,
                $value->golongan,
                $value->harga,
                $value->produksi,
                $value->responsibility_fee,
                "",
            );
        }

        $produksi_kendaraan = 0;
        $pendapatan_kendaraan = 0;

        foreach ($kendaraan as $key => $value) {
            $produksi_kendaraan += $value->produksi;
            $pendapatan_kendaraan += $value->responsibility_fee;

            $cashlessis[] = array(
                $key + 1,
                $value->golongan,
                $value->harga,
                $value->produksi,
                $value->responsibility_fee,
                "",
            );
        }

        foreach ($judul_tunai as $title) {
            $writer->writeSheetRow($sheet1, array("FORMULIR LAPORAN PENDAPATAN ASURANSI TG. JAWAB PENGANGKUT PER-SHIFT"), $style_header);
            $writer->writeSheetRow($sheet1, array(""));

            $writer->writeSheetRow($sheet1, array("CABANG", strtoupper($headerkita->branch_name), "", "SHIFT", $headerkita->shift_name));
            $writer->writeSheetRow($sheet1, array("PELABUHAN", $headerkita->port, "", "REGU", $headerkita->team_name));
            $writer->writeSheetRow($sheet1, array("LINTASAN", $headerkita->origin . " - " . $headerkita->destination, "", "TANGGAL", $headerkita->shift_date));
            $writer->writeSheetRow($sheet1, array(""));

            $writer->writeSheetRow($sheet1, array("1. PENUMPANG"));
            $writer->writeSheetRow($sheet1, $title, $styles1);
        }

        if ($penumpang) {
            foreach ($tunais as $row) {
                $writer->writeSheetRow($sheet1, $row, $styles2);
            }
        }

        $writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_penumpang, $pendapatan_penumpang, ""), $style_sub);
        $writer->writeSheetRow($sheet1, array(""));

        foreach ($judul_tunai as $title) {
            $writer->writeSheetRow($sheet1, array("2. KENDARAAN"));
        }

        if ($kendaraan) {
            foreach ($cashlessis as $row) {
                $writer->writeSheetRow($sheet1, $row, $styles2);
            }
        }

        $writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_kendaraan, $pendapatan_kendaraan, ""), $style_sub);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("TOTAL JUMLAH", "", "", $produksi_penumpang + $produksi_kendaraan, $pendapatan_penumpang + $pendapatan_kendaraan, ""), $style_sub);
        $writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 5);

        $writer->writeToStdOut();
    }
}
