<?php

error_reporting(0);

class Sab extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Html2pdf');
		$this->load->model('sab_model', 'm_sab');
		$this->_module   = 'laporan/sab';

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);		
	}

	public function index()
	{
		checkUrlAccess(uri_string(), 'view');
		if ($this->input->is_ajax_request()) {
			$port = $this->enc->decode($this->input->post('port'));
			$datefrom = $this->input->post('datefrom');
			$dateto = $this->input->post('dateto');
			// $ship_class = $this->enc->decode($this->input->post('ship_class'));
			$cek_sc = $this->m_sab->getClassBySession();
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

			$lintasan = $this->m_sab->get_lintasan($port, $datefrom, $dateto, $ship_class);

			$penumpang = $this->m_sab->list_data($port, $datefrom, $dateto, $ship_class, "penumpang");
			$kendaraan = $this->m_sab->list_data($port, $datefrom, $dateto, $ship_class, "kendaraan");

			$lintasanku = "-";

			if ($lintasan) {
				$data_lintasan = $lintasan->row();

				$lintasanku = $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

				if ($lintasan->num_rows() > 1) {
					$lintasanku = "Semua";
				}
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
					'lintasan' => $lintasanku,
					'penumpang' => $penumpang,
					'kendaraan' => $kendaraan,
				);

				echo json_encode($input);
				exit;
			}
		}

		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Laporan SAB',
			'content' => 'laporan/sab/index',
			'port' => $this->m_sab->getport(),
			'class' => $this->option_shift_class(),
			'download_pdf' => checkBtnAccess($this->_module, 'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module, 'download_excel'),
		);

		$this->load->view('default', $data);
	}

	private function option_shift_class()
	{
		$shift_class = $this->m_sab->getClassBySession('option');
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
		$port = $this->enc->decode($this->input->get("port"));
		$datefrom = $this->input->get("datefrom");
		$dateto = $this->input->get("dateto");
		// $ship_class = $this->enc->decode($this->input->get("ship_class"));
		$cek_sc = $this->m_sab->getClassBySession();
		if ($cek_sc == false) {
			$ship_class = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}

		$lintasan = $this->m_sab->get_lintasan($port, $datefrom, $dateto, $ship_class);

		$lintasanku = "-";

		if ($lintasan) {
			$data_lintasan = $lintasan->row();

			$lintasanku = $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

			if ($lintasan->num_rows() > 1) {
				$lintasanku = "Semua";
			}
		}

		$penumpang = $this->m_sab->list_data($port, $datefrom, $dateto, $ship_class, 'penumpang');
		$kendaraan = $this->m_sab->list_data($port, $datefrom, $dateto, $ship_class, 'kendaraan');

		$data['cabang'] = $this->input->get("cabangku");
		$data['pelabuhan'] = $this->input->get("pelabuhanku");
		$data['ship_class'] = $ship_classku;
		$data['shift'] = $this->input->get("shiftku");
		$data['regu'] = $this->input->get("reguku");
		$data['petugas'] = $this->input->get("petugasku");
		$data['tanggal'] = format_date($datefrom) . " - " . format_date($dateto);
		$data['lintasan'] = $lintasanku;
		$data['penumpang'] = $penumpang;
		$data['kendaraan'] = $kendaraan;

		$this->load->view('laporan/sab/pdf', $data);
	}

	function get_excel()
	{
		$excel_name = "Tiket_terpadu_terjual";

		$port = $this->enc->decode($this->input->get("port"));
		$datefrom = $this->input->get("datefrom");
		$dateto = $this->input->get("dateto");
		// $ship_class = $this->enc->decode($this->input->get("ship_class"));
		$cek_sc = $this->m_sab->getClassBySession();
		if ($cek_sc == false) {
			$ship_class = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}

		$lintasan = $this->m_sab->get_lintasan($port, $datefrom, $dateto, $ship_class);

		$lintasanku = "-";

		if ($lintasan) {
			$data_lintasan = $lintasan->row();

			$lintasanku = $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

			if ($lintasan->num_rows() > 1) {
				$lintasanku = "Semua";
			}
		}

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Laporan_sab" . $pelabuhan . "_" . trim($datefrom, "-") . "-" . trim($datefrom, "-") . "_" . $ship_class . ".xlsx");

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
			"JENIS TIKET SAB",
			"PRODUKSI",
		);

		$penumpang = $this->m_sab->list_data($port, $datefrom, $dateto, $ship_class, 'penumpang');
		$kendaraan = $this->m_sab->list_data($port, $datefrom, $dateto, $ship_class, 'kendaraan');

		$cabang = $this->input->get("cabangku");
		$pelabuhan = $this->input->get("pelabuhanku");
		$ship_class = $ship_classku;

		$produksi_penumpang = 0;

		foreach ($penumpang as $key => $value) {
			$produksi_penumpang += $value->produksi;

			$penumpangs[] = array(
				$key + 1,
				$value->golongan,
				$value->produksi
			);
		}

		$produksi_kendaraan = 0;

		foreach ($kendaraan as $key => $value) {
			$produksi_kendaraan += $value->produksi;

			$kendaraans[] = array(
				$key + 1,
				$value->golongan,
				$value->produksi
			);
		}

		$writer->writeSheetRow($sheet1, array("LAPORAN REKAPITULASI SURAT ANGKUT BEBAS"), $styles1);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("CABANG", $cabang, "LINTASAN", $lintasanku));
		$writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);
		$writer->writeSheetRow($sheet1, array("1. PENUMPANG"));

		if ($penumpang) {
			foreach ($penumpangs as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", $produksi_penumpang), $style_sub);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("2. KENDARAAN"));

		if ($kendaraan) {
			foreach ($kendaraans as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}
		$writer->writeSheetRow($sheet1, array("Sub Total", "", $produksi_kendaraan), $style_sub);

		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("Total)", "", $produksi_penumpang + $produksi_kendaraan), $style_sub);
		$writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 2);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$writer->writeToStdOut();
	}
}
