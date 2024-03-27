<?php

error_reporting(0);

class Rekonsiliasi extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Html2pdf');
		$this->load->model('m_rekonsiliasi');
		$this->_module   = 'laporan/rekonsiliasi';

		// $this->dbView = $this->load->database("dbView", TRUE);
		$this->dbView = checkReplication();
        $this->dbAction = $this->load->database("dbAction", TRUE);
	}

	public function index()
	{
		checkUrlAccess(uri_string(), 'view');
		if ($this->input->is_ajax_request()) {
			$port 		= $this->enc->decode($this->input->post('port'));
			$datefrom 	= $this->input->post('datefrom');
			$dateto 	= $this->input->post('dateto');
			$cek_sc   	= $this->m_rekonsiliasi->getClassBySession();

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

			$lintasan 	= $this->m_rekonsiliasi->get_lintasan($port, $datefrom, $dateto, $ship_class);
			$tunai 		= $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'tunai');
			$cashless 	= $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'cashless');
			$online 	= $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'online');
			$ifcs 		= $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'ifcs');
			$b2b 		= $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'b2b');

			if (!$tunai && !$cashless && !$online && !$b2b) {
				$data =  array(
					'code' => 101,
					'message' => "Tidak ada data",
				);

				echo json_encode($data);
				exit;
			} else {
				$input = array(
					'code' 		=> 200,
					'tunai'	 	=> $tunai,
					'cashless' 	=> $cashless,
					'online' 	=> $online,
					'ifcs' 		=> $ifcs,
					'b2b' 		=> $b2b,
				);

				echo json_encode($input);
				exit;
			}
		}

		$data = array(
			'home' 			 => 'Beranda',
			'url_home' 		 => site_url('home'),
			'title' 		 => 'Rekonsiliasi Revenue',
			'content' 		 => 'rekonsiliasi/index',
			'class' 		 => $this->option_shift_class(),
			'port' 			 => $this->m_rekonsiliasi->getport(),
			'download_pdf' 	 => checkBtnAccess($this->_module, 'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module, 'download_excel'),
		);

		$this->load->view('default', $data);
	}

	private function option_shift_class()
	{
		$shift_class = $this->m_rekonsiliasi->getClassBySession('option');
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

	function get_regu($port_id = "")
	{
		validate_ajax();
		$port_id = $this->enc->decode($port_id);

		if (!$port_id) {
			$option = '<option value="" selected>Semua</option>';
			echo $option;
		} else {
			$data = $this->m_rekonsiliasi->get_team($port_id);
			$option = '<option value="" selected>Semua</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="' . $this->enc->encode($value->team_code) . '">' . $value->team_name . '</option>';
			}
			echo $option;
		}
	}

	function get_pdf()
	{
		$port 		= $this->enc->decode($this->input->get("port"));
		$cek_sc 	= $this->m_rekonsiliasi->getClassBySession();
		if ($cek_sc == false) {
			$ship_class   = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class   = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}

		$datefrom 	= $this->input->get("datefrom");
		$dateto 	= $this->input->get("dateto");
		$tunai 		= $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'tunai');
		$cashless 	= $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'cashless');
		$online 	= $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'online');
		$ifcs 		= $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'ifcs');
		$b2b 		= $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'b2b');

		$data['port_name'] 		 = $this->input->get("port_name");
		$data['ship_classku']    = $ship_classku;
		$data['print_date'] 	 = format_date($datefrom) . " - " . format_date($dateto);
		$data['tunai']  		 = $tunai;
		$data['cashless'] 		 = $cashless;
		$data['online'] 		 = $online;
		$data['ifcs']			 = $ifcs;
		$data['b2b']			 = $b2b;

		$this->load->view('laporan/rekonsiliasi/pdf', $data);
	}

	function get_excel()
	{
		$excel_name = "Rekonsiliasi";

		$port 		= $this->enc->decode($this->input->get("port"));
		$cek_sc 	= $this->m_rekonsiliasi->getClassBySession();
		if ($cek_sc == false) {
			$ship_class   = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class   = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}
		$datefrom 	= $this->input->get("datefrom");
		$dateto		= $this->input->get("dateto");
		$port_name 	= $this->input->get("port_name");

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Rekon_" . $port_name . ".xlsx");

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
			"JENIS",
			"Tarif Pas Pelabuhan (KD88 Tahun 2017)",
			"PRODUKSI (Lbr.)",
			"PENDAPATAN (Rp.)",
		);

		$tunai 	  = $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'tunai');
		$cashless = $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'cashless');
		$online   = $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'online');
		$ifcs	  = $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'ifcs');
		$b2b	  = $this->m_rekonsiliasi->list_data($port, $datefrom, $dateto, $ship_class, 'b2b');

		$produksi_tunai = 0;
		$pendapatan_tunai = 0;

		foreach ($tunai as $key => $value) {
			$produksi_tunai += $value->produksi;
			$pendapatan_tunai += $value->pendapatan;

			$tunais[] = array(
				$key + 1,
				$value->golongan,
				$value->entrance_fee,
				$value->produksi,
				$value->pendapatan,
			);
		}

		$produksi_cashless = 0;
		$pendapatan_cashless = 0;

		foreach ($cashless as $key => $value) {
			$produksi_cashless += $value->produksi;
			$pendapatan_cashless += $value->pendapatan;

			$cashlessis[] = array(
				$key + 1,
				$value->golongan,
				$value->entrance_fee,
				$value->produksi,
				$value->pendapatan,
			);
		}

		$produksi_online = 0;
		$pendapatan_online = 0;

		foreach ($online as $key => $value) {
			$produksi_online += $value->produksi;
			$pendapatan_online += $value->pendapatan;

			$onlineis[] = array(
				$key + 1,
				$value->golongan,
				$value->entrance_fee,
				$value->produksi,
				$value->pendapatan,
			);
		}

		$produksi_ifcs = 0;
		$pendapatan_ifcs = 0;

		foreach ($ifcs as $key => $value) {
			$produksi_ifcs += $value->produksi;
			$pendapatan_ifcs += $value->pendapatan;

			$ifcsis[] = array(
				$key + 1,
				$value->golongan,
				$value->entrance_fee,
				$value->produksi,
				$value->pendapatan,
			);
		}

		$produksi_b2b = 0;
		$pendapatan_b2b = 0;

		foreach ($b2b as $key => $value) {
			$produksi_b2b += $value->produksi;
			$pendapatan_b2b += $value->pendapatan;

			$b2bis[] = array(
				$key + 1,
				$value->golongan,
				$value->entrance_fee,
				$value->produksi,
				$value->pendapatan,
			);
		}

		$writer->writeSheetRow($sheet1, array("LAPORAN REKONSILIASI SHARING REVENUE"), $styles_ha);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("PELABUHAN", $port_name, "", "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : TUNAI"));
		$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);

		if ($tunai) {
			foreach ($tunais as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_tunai, $pendapatan_tunai), $style_sub);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : PREPAID CASHLESS"));
		$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);

		if ($cashless) {
			foreach ($cashlessis as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_cashless, $pendapatan_cashless), $style_sub);

		$writer->writeSheetRow($sheet1, array(""));
		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : TIKET ONLINE"));
		$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);

		if ($online) {
			foreach ($onlineis as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_online, $pendapatan_online), $style_sub);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : IFCS"));
		$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);

		if ($ifcs) {
			foreach ($ifcsis as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_ifcs, $pendapatan_ifcs), $style_sub);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : B2B"));
		$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);

		if ($b2b) {
			foreach ($b2bis as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_b2b, $pendapatan_b2b), $style_sub);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (Tunai + Cashless + Online + IFCS + B2B)", "", "", $produksi_tunai + $produksi_cashless + $produksi_online + $produksi_ifcs + $produksi_b2b, $pendapatan_tunai + $pendapatan_cashless + $pendapatan_online + $pendapatan_ifcs + $pendapatan_b2b), $style_sub);
		$writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$writer->writeToStdOut();
	}
}
