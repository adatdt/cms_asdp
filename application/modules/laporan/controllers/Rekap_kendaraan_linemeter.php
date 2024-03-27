<?php

error_reporting(0);

class Rekap_kendaraan_linemeter extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Html2pdf');
        $this->load->model('Rekap_kendaraan_linemeter_model', 'model_');
        $this->_module     = 'laporan/rekap_kendaraan_linemeter';
        $this->report_name = "rekap_kendaraan_linemeter";
        $this->report_code = $this->global_model->get_report_code($this->report_name);
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $port        = $this->enc->decode($this->input->post('port'));
            $time        = $this->enc->decode($this->input->post('time'));
            $datefrom     = $this->input->post('datefrom');
            // $dateto     = $this->input->post('dateto');
            // $shift         = $this->enc->decode($this->input->post('shift'));
            $cek_sc        = $this->model_->getClassBySession();

            // print_r($time); die();

            if ($cek_sc == false) {
                $ship_class = $this->enc->decode($this->input->post('ship_class'));
            } else {
                $ship_class = $cek_sc['id'];
            }

            if ($datefrom == "") {
                $data =  array(
                    'code' => 101,
                    'message' => "Silahkan pilih tanggal mulai!",
                );

                echo json_encode($data);
                exit;
            }

            // print_r($data);die();

            // if ($dateto == "") {
            //     $data =  array(
            //         'code' => 101,
            //         'message' => "Silahkan pilih tanggal akhir!",
            //     );

            //     echo json_encode($data);
            //     exit;
            // }

            // $lintasan     = $this->model_->get_lintasan($port, $datefrom, $ship_class, $shift);
            // $get_regu     = $this->model_->get_team($port, $datefrom, $ship_class, $shift);
            $kendaraan    = $this->model_->list_data($port, $datefrom, $ship_class, $time);
            // $lintasanku = "-";
            // $reguku     = "-";

            // if ($lintasan) {
            //     $data_lintasan = $lintasan->row();
            //     $lintasanku = $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

            //     if ($lintasan->num_rows() > 1) {
            //         $lintasanku = "-";
            //     }
            // }

            // if ($get_regu) {
            //     $reguku = $get_regu->team_name;
            // }

            if (!$kendaraan) {
                $data =  array(
                    'code'         => 101,
                    'message'     => "Tidak ada data",
                );

                echo json_encode($data);
                exit;
            } else {
                $input = array(
                    'code'         => 200,
                    // 'lintasan'     => $lintasanku,
                    // 'regu'         => $reguku,
                    'kendaraan'    => $kendaraan,
                );

                echo json_encode($input);
                exit;
            }
        }
        $dataTime[""] = "Pilih";

        for ($i = 0; $i < 24; $i++) {
            strlen($i) < 2 ? $his = "0" . $i . ":00" : $his = $i . ":00";

            $dataTime[$this->enc->encode($his)] = $his;
        }

        $data = array(
            'home'              => 'Beranda',
            'url_home'          => site_url('home'),
            'title'             => 'Rekap Rincian Golongan Kendaraan Terhadap Linemeter',
            'content'           => 'laporan/rekap_kendaraan_linemeter/index',
            'port'              => $this->model_->getport(),
            'class'             => $this->option_shift_class(),
            'time'              => $dataTime,
            'download_pdf'      => checkBtnAccess($this->_module, 'download_pdf'),
            'download_excel'    => checkBtnAccess($this->_module, 'download_excel'),
        );

        // print_r($data['time']);
        // die();

        $this->load->view('default', $data);
    }

    private function option_shift_class()
    {
        $shift_class = $this->model_->getClassBySession('option');
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

    function get_pdf()
    {
        $port         = $this->enc->decode($this->input->get("port"));
        $time         = $this->enc->decode($this->input->get("time"));
        $datefrom     = $this->input->get("datefrom");
        // $dateto     = $this->input->get("dateto");
        $cek_sc     = $this->model_->getClassBySession();

        if ($cek_sc == false) {
            $ship_class   = $this->enc->decode($this->input->get("ship_class"));
            $ship_classku = $this->input->get("ship_classku");
        } else {
            $ship_class   = $cek_sc['id'];
            $ship_classku = $cek_sc['name'];
        }

        // $shift         = $this->enc->decode($this->input->get("shift"));
        // $lintasan     = $this->model_->get_lintasan($port, $datefrom, $dateto, $ship_class, $shift);
        // $get_regu     = $this->model_->get_team($port, $datefrom, $dateto, $ship_class, $shift);
        // $penumpang     = $this->model_->list_data($port, $datefrom, $dateto, $ship_class, $shift, "penumpang");
        $kendaraan     = $this->model_->list_data($port, $datefrom, $ship_class, $time);
        // $lintasanku = "-";
        // $reguku     = "-";

        // if ($lintasan) {
        //     $data_lintasan = $lintasan->row();
        //     $lintasanku = $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

        //     if ($lintasan->num_rows() > 1) {
        //         $lintasanku = "-";
        //     }
        // }

        // if ($get_regu) {
        //     $reguku = $get_regu->team_name;
        // }

        // $data['cabang']     = $this->input->get("cabangku");
        $data['pelabuhan']      = $this->input->get("pelabuhanku");
        $data['ship_class']     = $ship_classku;
        // $data['shift']          = $this->input->get("shiftku");
        // $data['regu']           = $reguku;
        // $data['petugas']        = $this->input->get("petugasku");
        $data['tanggal']        = format_date($datefrom);
        // $data['lintasan']       = $lintasanku;
        // $data['penumpang']      = $penumpang;
        $data['kendaraan']      = $kendaraan;

        // print_r($data); die();

        $this->load->view('laporan/rekap_kendaraan_linemeter/pdf', $data);
    }

    function get_excel()
    {
        $excel_name = "Tiket_expired_";

        $port         = $this->enc->decode($this->input->get("port"));
        $time         = $this->enc->decode($this->input->get("time"));
        $datefrom     = $this->input->get("datefrom");
        // $dateto     = $this->input->get("dateto");
        $cek_sc     = $this->model_->getClassBySession();

        if ($cek_sc == false) {
            $ship_class   = $this->enc->decode($this->input->get("ship_class"));
            $ship_classku = $this->input->get("ship_classku");
        } else {
            $ship_class   = $cek_sc['id'];
            $ship_classku = $cek_sc['name'];
        }
        // $shift = $this->enc->decode($this->input->get("shift"));

        // $lintasan      = $this->model_->get_lintasan($port, $datefrom, $dateto, $ship_class, $shift);
        // $get_regu      = $this->model_->get_team($port, $datefrom, $dateto, $ship_class, $shift);
        // $penumpang     = $this->model_->list_data($port, $datefrom, $dateto, $ship_class, $shift, "penumpang");
        $kendaraan     = $this->model_->list_data($port, $datefrom, $ship_class, $time);
        // $lintasanku = "-";
        // $reguku     = "-";

        // if ($lintasan) {
        //     $data_lintasan     = $lintasan->row();
        //     $lintasanku     = $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

        //     if ($lintasan->num_rows() > 1) {
        //         $lintasanku = "-";
        //     }
        // }

        // if ($get_regu) {
        //     $reguku = $get_regu->team_name;
        // }

        $this->load->library('XLSExcel');
        $writer = new XLSXWriter();
        $filename = strtoupper("Rekapitulasi_rincian_golongan_kendaraan_terhadap_penggunaan_linemeter" . "_" . $this->input->get('pelabuhanku') . "_" . trim($datefrom, "-") . "_" . $ship_class . ".xlsx");

        $writer->setTitle($excel_name);
        $writer->setSubject($excel_name);
        $writer->setAuthor($excel_name);
        $writer->setCompany('ASDP Indonesia Ferry');
        $writer->setDescription($filename);
        $writer->setTempDir(sys_get_temp_dir());

        $sheet1 = $filename;

        $styles_ha = array(
            'font' => 'Arial',
            'font-size' => 12,
            'font-style' => 'bold',
            'halign' => 'center',
            'valign' => 'center',
        );

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
            'halign' => 'left',
            'valign' => 'left',
            'border' => 'left,right,top,bottom',
            'border-style' => 'thin',
        );

        $header = array("string", "string", "string", "string", "string");

        $judul_tabel = array(
            "NO.",
            "TANGGAL",
            "WAKTU",
            "GOLONGAN",
            "PRODUKSI",
            "LEBAR",
            "PANJANG",
            "LUAS",
            "JUMLAH"
        );

        // $cabang     = $this->input->get("cabangku");
        $pelabuhan     = $this->input->get("pelabuhanku");
        $ship_class    = $ship_classku;
        // $shiftku     = $this->input->get("shiftku");

        // $produksi_penumpang = 0;
        // $pendapatan_penumpang = 0;

        // foreach ($penumpang as $key => $value) {
        //     $produksi_penumpang += $value->produksi;
        //     $pendapatan_penumpang += $value->pendapatan;

        //     $penumpangs[] = array(
        //         $key + 1,
        //         $value->golongan,
        //         $value->produksi,
        //         $value->pendapatan,
        //     );
        // }

        $produksi_kendaraan = 0;
        $pendapatan_kendaraan = 0;

        foreach ($kendaraan as $key => $value) {
            $produksi_kendaraan             += $value->produksi;
            $pendapatan_kendaraan           += $value->pendapatan;
            $total_ketersediaan_linemeter   += $value->total_lm;
            $total_penggunaan_linemeter        += $value->jumlah;
            $persentase                     = ($total_penggunaan_linemeter) / ($total_ketersediaan_linemeter);
            if ($persentase >= 100) {
                $status = "OVER";
            } else if ($persentase <= 100) {
                $status = "UNDER";
            }
            $kendaraans[] = array(
                $key + 1,
                $value->depart_date,
                $value->depart_time,
                $value->golongan,
                $value->produksi,
                $value->lebar,
                $value->panjang,
                $value->luas,
                $value->jumlah
            );
        }

        $writer->writeSheetRow($sheet1, array("LAPORAN REKAPITULASI RINCIAN GOLONGAN KENDARAAN TERHADAP PENGGUNAAN LINEMETER", "", "", "", "", "", "", "", ""), $styles1);
        $writer->writeSheetRow($sheet1, array(""));

        // $writer->writeSheetRow($sheet1, array("CABANG", $cabang, "SHIFT", $shiftku));
        $writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "TANGGAL", format_date($datefrom)));
        // $writer->writeSheetRow($sheet1, array("LINTASAN", $lintasanku, "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, $judul_tabel, $styles1);
        // $writer->writeSheetRow($sheet1, array("1. PENUMPANG"));

        // if ($penumpang) {
        //     foreach ($penumpangs as $row) {
        //         $writer->writeSheetRow($sheet1, $row, $styles2);
        //     }
        // }

        // $writer->writeSheetRow($sheet1, array("Sub Total", "", $produksi_penumpang, $pendapatan_penumpang), $style_sub);
        // $writer->writeSheetRow($sheet1, array(""));

        // $writer->writeSheetRow($sheet1, array("2. KENDARAAN"));

        if ($kendaraan) {
            foreach ($kendaraans as $row) {
                $writer->writeSheetRow($sheet1, $row, $styles2);
            }
        }
        $writer->writeSheetRow($sheet1, array("TOTAL PENGGUNAAN LINEMETER PER JAM", "", "", "", "", "", "", "", $total_penggunaan_linemeter), $style_sub);
        $writer->writeSheetRow($sheet1, array("TOTAL KETERSEDIAAN LINEMETER PER JAM", "", "", "", "", "", "", "", $total_ketersediaan_linemeter), $style_sub);
        $writer->writeSheetRow($sheet1, array("SISA KETERSEDIAAN LINEMETER", "", "", "", "", "", "", "", ($total_ketersediaan_linemeter) - ($total_penggunaan_linemeter)), $style_sub);
        $writer->writeSheetRow($sheet1, array("PERSENTASE", "", "", "", "", "", "", "", $persentase), $style_sub);
        $writer->writeSheetRow($sheet1, array("STATUS", "", "", "", "", "", "", "", $status), $style_sub);
        $writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);
        // $writer->markMergedCell($sheet1, $start_row = 9, $start_col = 0, $end_row = 9, $end_col = 7);
        // $writer->markMergedCell($sheet1, $start_row = 10, $start_col = 0, $end_row = 10, $end_col = 7);
        // $writer->markMergedCell($sheet1, $start_row = 11, $start_col = 0, $end_row = 11, $end_col = 7);
        // $writer->markMergedCell($sheet1, $start_row = 12, $start_col = 0, $end_row = 12, $end_col = 7);
        // $writer->markMergedCell($sheet1, $start_row = 13, $start_col = 0, $end_row = 13, $end_col = 7);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }
}
