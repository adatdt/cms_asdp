<?php

error_reporting(0);

class Tiket_expired extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Html2pdf');
		$this->load->model('tiket_expired_model', 'm_expired');
		$this->_module   = 'laporan/tiket_expired';

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);		
	}

	public function index()
	{
		checkUrlAccess(uri_string(), 'view');
		if ($this->input->is_ajax_request()) {
			$port		= $this->enc->decode($this->input->post('port'));
			$datefrom 	= $this->input->post('datefrom');
			$dateto 	= $this->input->post('dateto');
			$shift 		= $this->enc->decode($this->input->post('shift'));
			$cek_sc		= $this->m_expired->getClassBySession();

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

			$lintasan 	= $this->m_expired->get_lintasan($port, $datefrom, $dateto, $ship_class, $shift);
			$get_regu 	= $this->m_expired->get_team($port, $datefrom, $dateto, $ship_class, $shift);
			$penumpang 	= $this->m_expired->list_data($port, $datefrom, $dateto, $ship_class, $shift, "penumpang");
			$kendaraan 	= $this->m_expired->list_data($port, $datefrom, $dateto, $ship_class, $shift, "kendaraan");
			$lintasanku = "-";
			$reguku 	= "-";

			if ($lintasan) {
				$data_lintasan = $lintasan->row();
				$lintasanku = $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

				if ($lintasan->num_rows() > 1) {
					$lintasanku = "-";
				}
			}

			if ($get_regu) {
				$reguku = $get_regu->team_name;
			}

			if (!$penumpang && !$kendaraan) {
				$data =  array(
					'code' 		=> 101,
					'message' 	=> "Tidak ada data",
				);

				echo json_encode($data);
				exit;
			} else {
				$input = array(
					'code' 		=> 200,
					'lintasan' 	=> $lintasanku,
					'regu' 		=> $reguku,
					'penumpang' => $penumpang,
					'kendaraan' => $kendaraan,
				);

				echo json_encode($input);
				exit;
			}
		}

		$data = array(
			'home' 			 => 'Beranda',
			'url_home' 		 => site_url('home'),
			'title' 		 => 'Laporan Tiket Expired',
			'content' 		 => 'laporan/tiket_expired/index',
			'port' 			 => $this->m_expired->getport(),
			'class' 		 => $this->option_shift_class(),
			'shift' 		 => $this->m_expired->getshift(),
			'download_pdf'   => checkBtnAccess($this->_module, 'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module, 'download_excel'),
		);

		$this->load->view('default', $data);
	}

	private function option_shift_class()
	{
		$shift_class = $this->m_expired->getClassBySession('option');
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
		$port 		= $this->enc->decode($this->input->get("port"));
		$datefrom 	= $this->input->get("datefrom");
		$dateto 	= $this->input->get("dateto");
		$cek_sc 	= $this->m_expired->getClassBySession();

		if ($cek_sc == false) {
			$ship_class   = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class   = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}

		$shift 		= $this->enc->decode($this->input->get("shift"));
		$lintasan 	= $this->m_expired->get_lintasan($port, $datefrom, $dateto, $ship_class, $shift);
		$get_regu 	= $this->m_expired->get_team($port, $datefrom, $dateto, $ship_class, $shift);
		$penumpang 	= $this->m_expired->list_data($port, $datefrom, $dateto, $ship_class, $shift, "penumpang");
		$kendaraan 	= $this->m_expired->list_data($port, $datefrom, $dateto, $ship_class, $shift, "kendaraan");
		$lintasanku = "-";
		$reguku 	= "-";

		if ($lintasan) {
			$data_lintasan = $lintasan->row();
			$lintasanku = $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

			if ($lintasan->num_rows() > 1) {
				$lintasanku = "-";
			}
		}

		if ($get_regu) {
			$reguku = $get_regu->team_name;
		}

		$data['cabang'] 	= $this->input->get("cabangku");
		$data['pelabuhan'] 	= $this->input->get("pelabuhanku");
		$data['ship_class'] = $ship_classku;
		$data['shift'] 		= $this->input->get("shiftku");
		$data['regu'] 		= $reguku;
		$data['petugas'] 	= $this->input->get("petugasku");
		$data['tanggal'] 	= format_date($datefrom) . " - " . format_date($dateto);
		$data['lintasan'] 	= $lintasanku;
		$data['penumpang'] 	= $penumpang;
		$data['kendaraan'] 	= $kendaraan;

		$this->load->view('laporan/tiket_expired/pdf', $data);
	}

	function get_excel()
	{
		$excel_name = "Tiket_expired_";

		$port 		= $this->enc->decode($this->input->get("port"));
		$datefrom 	= $this->input->get("datefrom");
		$dateto 	= $this->input->get("dateto");
		$cek_sc 	= $this->m_expired->getClassBySession();

		if ($cek_sc == false) {
			$ship_class   = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class   = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}		$shift = $this->enc->decode($this->input->get("shift"));

		$lintasan  	= $this->m_expired->get_lintasan($port, $datefrom, $dateto, $ship_class, $shift);
		$get_regu  	= $this->m_expired->get_team($port, $datefrom, $dateto, $ship_class, $shift);
		$penumpang 	= $this->m_expired->list_data($port, $datefrom, $dateto, $ship_class, $shift, "penumpang");
		$kendaraan 	= $this->m_expired->list_data($port, $datefrom, $dateto, $ship_class, $shift, "kendaraan");
		$lintasanku = "-";
		$reguku 	= "-";

		if ($lintasan) {
			$data_lintasan 	= $lintasan->row();
			$lintasanku 	= $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

			if ($lintasan->num_rows() > 1) {
				$lintasanku = "-";
			}
		}

		if ($get_regu) {
			$reguku = $get_regu->team_name;
		}

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Tiket_expired_" . $this->input->get('pelabuhanku') . "_" . trim($datefrom, "-") . "-" . trim($datefrom, "-") . "_" . $ship_class . ".xlsx");

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
			'halign' => 'right',
			'valign' => 'right',
			'border' => 'left,right,top,bottom',
			'border-style' => 'thin',
		);

		$header = array("string", "string", "string", "string", "string");

		$judul_tabel = array(
			"NO.",
			"JENIS TIKET",
			"PRODUKSI",
			"PENDAPATAN",
		);

		$cabang 	= $this->input->get("cabangku");
		$pelabuhan 	= $this->input->get("pelabuhanku");
		$ship_class = $ship_classku;
		$shiftku 	= $this->input->get("shiftku");

		$produksi_penumpang = 0;
		$pendapatan_penumpang = 0;

		foreach ($penumpang as $key => $value) {
			$produksi_penumpang += $value->produksi;
			$pendapatan_penumpang += $value->pendapatan;

			$penumpangs[] = array(
				$key + 1,
				$value->golongan,
				$value->produksi,
				$value->pendapatan,
			);
		}

		$produksi_kendaraan = 0;
		$pendapatan_kendaraan = 0;

		foreach ($kendaraan as $key => $value) {
			$produksi_kendaraan += $value->produksi;
			$pendapatan_kendaraan += $value->pendapatan;

			$kendaraans[] = array(
				$key + 1,
				$value->golongan,
				$value->produksi,
				$value->pendapatan,
			);
		}

		$writer->writeSheetRow($sheet1, array("LAPORAN REKAPITULASI EXPIRED"), $styles1);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("CABANG", $cabang, "SHIFT", $shiftku));
		$writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "REGU", $reguku));
		$writer->writeSheetRow($sheet1, array("LINTASAN", $lintasanku, "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);
		$writer->writeSheetRow($sheet1, array("1. PENUMPANG"));

		if ($penumpang) {
			foreach ($penumpangs as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", $produksi_penumpang, $pendapatan_penumpang), $style_sub);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("2. KENDARAAN"));

		if ($kendaraan) {
			foreach ($kendaraans as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}
		$writer->writeSheetRow($sheet1, array("Sub Total", "", $produksi_kendaraan, $pendapatan_kendaraan), $style_sub);

		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("Total (Penumpang + Kendaraan)", "", $produksi_penumpang + $produksi_kendaraan, $pendapatan_penumpang + $pendapatan_kendaraan), $style_sub);
		$writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 3);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$writer->writeToStdOut();
	}
}
