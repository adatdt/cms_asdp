<?php

error_reporting(0);

class Perbandingan_linemeter extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Html2pdf');
        $this->load->model('Perbandingan_linemeter_model', 'model_');
        $this->_module     = 'laporan/perbandingan_linemeter';
        $this->report_name = "perbandingan_linemeter";
        $this->report_code = $this->global_model->get_report_code($this->report_name);
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $port        = $this->enc->decode($this->input->post('port'));
            $golongan    = $this->enc->decode($this->input->post('golongan'));
            $time        = $this->enc->decode($this->input->post('time'));
            $ket         = $this->input->post('ket');
            $datefrom    = $this->input->post('datefrom');
            $dateto      = $this->input->post('dateto');
            $cek_sc      = $this->model_->getClassBySession();

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

            if ($dateto == "") {
                $data =  array(
                    'code' => 101,
                    'message' => "Silahkan pilih tanggal akhir!",
                );

                echo json_encode($data);
                exit;
            }

            // $lintasan     = $this->model_->get_lintasan($port, $datefrom, $ship_class, $shift);
            // $get_regu     = $this->model_->get_team($port, $datefrom, $ship_class, $shift);
            $perbandingan    = $this->model_->list_data($port, $golongan, $ket, $datefrom, $dateto, $ship_class, $time);
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

            if (!$perbandingan) {
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
                    'perbandingan'    => $perbandingan,
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
            'title'             => 'Report Perbandingan linemeter',
            'content'           => 'laporan/perbandingan_linemeter/index',
            'port'              => $this->model_->getport(),
            'golongan'          => $this->model_->getgolongan(),
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
        $golongan     = $this->enc->decode($this->input->get('golongan'));
        $time         = $this->enc->decode($this->input->get('time'));
        $ket          = $this->input->get('ket');
        $datefrom     = $this->input->get("datefrom");
        $dateto       = $this->input->get("dateto");
        $cek_sc       = $this->model_->getClassBySession();

        if ($cek_sc == false) {
            $ship_class   = $this->enc->decode($this->input->get("ship_class"));
            $ship_classku = $this->input->get("ship_classku");
        } else {
            $ship_class   = $cek_sc['id'];
            $ship_classku = $cek_sc['name'];
        }

        // print_r($ket); die();

        // $shift         = $this->enc->decode($this->input->get("shift"));
        // $lintasan     = $this->model_->get_lintasan($port, $datefrom, $dateto, $ship_class, $shift);
        // $get_regu     = $this->model_->get_team($port, $datefrom, $dateto, $ship_class, $shift);
        // $penumpang     = $this->model_->list_data($port, $datefrom, $dateto, $ship_class, $shift, "penumpang");
        $perbandingan    = $this->model_->list_data($port, $golongan, $ket, $datefrom, $dateto, $ship_class, $time );
        
        $data['pelabuhan']      = $this->input->get("pelabuhanku");
        $data['keterangan']     = $this->input->get("keteranganku");
        $data['ship_class']     = $this->input->get("ship_classku");
        $data['golongan']       = $this->input->get("golonganku");
        $data['tanggal']        = format_date($datefrom) . " - " . format_date($dateto);
        $data['perbandingan']   = $perbandingan;

        // echo"<pre>";
        // print_r($data); die();

        $this->load->view('laporan/perbandingan_linemeter/pdf', $data);
    }

    function get_excel()
    {
        $excel_name = "Perbandingan_linemeter_";

        $port         = $this->enc->decode($this->input->get("port"));
        $golongan     = $this->enc->decode($this->input->get('golongan'));
        $time         = $this->enc->decode($this->input->get('time'));
        $ket          = $this->input->get('ket');
        $datefrom     = $this->input->get("datefrom");
        $dateto       = $this->input->get("dateto");
        $cek_sc       = $this->model_->getClassBySession();

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
        $perbandingan    = $this->model_->list_data($port, $golongan, $ket, $datefrom, $dateto, $ship_class, $time );
        $pelabuhan       = $this->input->get('pelabuhanku');
        $golongan        = $this->input->get('golonganku');
        $keterangan      = $this->input->get('keteranganku');
        $ship_class      = $ship_classku;
        // echo"<pre>";
        // print_r($golongan, $pelabuhan, $ship_class); die();
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
        $filename = strtoupper("Report_perbandingan_linemeter" . "_" . $pelabuhan . "_" . trim($datefrom, "-") . "_" . trim($dateto, "-") . "_" . $ship_class . ".xlsx");

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

        $styles3 = array(
            'font' => 'Arial',
            'font-size' => 10,
            'font-style' => 'bold',
            'halign' => 'justify',
            'valign' => 'justify',
            'border' => 'left,right,top,bottom',
            'border-style' => 'thin',
        );

        $style_header = array(
            'font' => 'Arial',
            'font-size' => 11,
            'font-style' => 'bold',
            'halign' => 'center',
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
            "KODE BOOKING",
            "DEFAULT LINEMETER",
            "",
            "PENGISIAN LINEMETER",
            "",
            "REALISASI LINEMETER",
            "",
            "KETERANGAN"
        );

        $panjangP   = 0;
        $lebarR     = 0;
        $panjangR   = 0;

        foreach ($perbandingan as $key => $value) {
            // $produksi_kendaraan             += $value->produksi;
            // $pendapatan_kendaraan           += $value->pendapatan;
            // $total_ketersediaan_linemeter   += $value->total_lm;
            // $total_penggunaan_linemeter        += $value->jumlah;
            // $persentase                     = ($total_penggunaan_linemeter) / ($total_ketersediaan_linemeter);
            if ($value->keterangan == '1') {
				$keterangan = "Overpaid";
			} else if ($value->keterangan == '2') {
				$keterangan = "Underpaid";
			} else (
				$keterangan = "Normal"
            );

            if ($value->panjang_pengisian != ''){
                $panjangP = $value->panjang_pengisian;
            }

            if ($value->lebar_real != ''){
                $lebarR = $value->lebar_real;
            }

            if ($value->panjang_real != ''){
                $panjangR = $value->panjang_real;
            }
            
            $perbandingans[] = array(
                $key + 1,
                $value->depart_date,
                $value->depart_time_start,
                $value->name,
                $value->kode_booking,
                $value->panjang_default,
                $value->lebar_default,
                $panjangP,
                $value->lebar_pengisian,
                $panjangR,
                $lebarR,
                $keterangan                
            );
        }

        $sub_head = array('','','','','','Panjang (m)','Lebar (m)','Panjang (m)','Lebar (m)','Panjang (m)','Lebar (m)','');

        $writer->writeSheetRow($sheet1, array("LAPORAN PERBANDINGAN LINEMETER", "", "", "", "", "", "", "", "", "", "", ""), $style_header);
        $writer->writeSheetRow($sheet1, array(""));

        // $writer->writeSheetRow($sheet1, array("CABANG", $cabang, "SHIFT", $shiftku));
        $writer->writeSheetRow($sheet1, array("PELABUHAN", "", "", "", $pelabuhan, "", "TANGGAL", "", "", "", "", format_date($datefrom) . " - " . format_date($dateto)), $style_sub);
        $writer->writeSheetRow($sheet1, array("KETERANGAN", "", "", "", $keterangan, "", "GOLONGAN", "", "", "", "", $golongan), $style_sub);
        $writer->writeSheetRow($sheet1, array("KELAS", "", "", "", $ship_classku, "", "", "", "", "", "", ""), $style_sub);
        // $writer->writeSheetRow($sheet1, array("LINTASAN", $lintasanku, "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, $judul_tabel, $styles1);

        $writer->writeSheetRow($sheet1, $sub_head, $style_sub);
        

        if ($perbandingan) {
            foreach ($perbandingans as $row) {
                $writer->writeSheetRow($sheet1, $row, $styles2);
            }
        }
        $writer->writeSheetRow($sheet1, array("", "", "", "", "", "", "", "", "", "", "", ""), $styles2);
        $writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 11);
        $writer->markMergedCell($sheet1, $start_row = 6, $start_col = 0, $end_row = 7, $end_col = 0);
        $writer->markMergedCell($sheet1, $start_row = 6, $start_col = 1, $end_row = 7, $end_col = 1);
        $writer->markMergedCell($sheet1, $start_row = 6, $start_col = 2, $end_row = 7, $end_col = 2);
        $writer->markMergedCell($sheet1, $start_row = 6, $start_col = 3, $end_row = 7, $end_col = 3);
        $writer->markMergedCell($sheet1, $start_row = 6, $start_col = 4, $end_row = 7, $end_col = 4);
        $writer->markMergedCell($sheet1, $start_row = 6, $start_col = 5, $end_row = 6, $end_col = 6);
        $writer->markMergedCell($sheet1, $start_row = 6, $start_col = 7, $end_row = 6, $end_col = 8);
        $writer->markMergedCell($sheet1, $start_row = 6, $start_col = 9, $end_row = 6, $end_col = 10);
        $writer->markMergedCell($sheet1, $start_row = 6, $start_col = 11, $end_row = 7, $end_col = 11);
        $writer->markMergedCell($sheet1, $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 3);
        $writer->markMergedCell($sheet1, $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 3);
        $writer->markMergedCell($sheet1, $start_row = 4, $start_col = 0, $end_row = 4, $end_col = 3);
        $writer->markMergedCell($sheet1, $start_row = 2, $start_col = 6, $end_row = 2, $end_col = 10);
        $writer->markMergedCell($sheet1, $start_row = 3, $start_col = 6, $end_row = 3, $end_col = 10);
        $writer->markMergedCell($sheet1, $start_row = 4, $start_col = 6, $end_row = 4, $end_col = 11);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }
}
