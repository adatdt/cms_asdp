<?php
error_reporting(0);

class Ticket_terpadu_tertagih extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Html2pdf');
		$this->load->model('Ticket_tertagih_model', 'm_tertagih');
		$this->_module   = 'laporan/ticket_terpadu_tertagih';
		$this->report_name = "tiket_tertagih";
		$this->report_code = $this->global_model->get_report_code($this->report_name);

		// $this->dbView=$this->load->database("dbView",TRUE);
		$this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);
	}

	public function index()
	{
		ini_set('memory_limit', '2048M');
		checkUrlAccess(uri_string(), 'view');
		if ($this->input->is_ajax_request()) {
			$port = $this->enc->decode($this->input->post('port'));
			$datefrom = $this->input->post('datefrom');
			$dateto = $this->input->post('dateto');
			$petugas = $this->enc->decode($this->input->post('petugas'));
			$shift = $this->enc->decode($this->input->post('shift'));
			$ticket_type = $this->enc->decode($this->input->post('ticket_type'));
			$merchant = $this->enc->decode($this->input->post('merchant'));
			// $ship_class = $this->enc->decode($this->input->post('ship_class'));
			$vm = $this->enc->decode($this->input->post('vm'));
			$cek_sc = $this->m_tertagih->getClassBySession();
			if ($cek_sc == false) {
				$ship_class = $this->enc->decode($this->input->post('ship_class'));
			} else {
				$ship_class = $cek_sc['id'];
			}
			// echo json_encode($petugas);

			$keterangan_report = "Draft";

			$status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift, $ship_class);

			if ($status_approve) {
				if ($status_approve == 2) {
					$keterangan_report = "Approved";
				}
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

			$lintasan = $this->m_tertagih->get_lintasan($port, $datefrom, $dateto, $ship_class);
			$header = $this->m_tertagih->get_team($port, $datefrom, $dateto, $ship_class, $shift);
			$tunai = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'tunai', $vm, $ticket_type);
			$cashless = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'cashless', $vm, $ticket_type);
			$online = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'online', $vm, $ticket_type);
			$ifcs = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'ifcs', $vm, $ticket_type);
			$ifcs_redeem = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'ifcs_redeem', $vm, $ticket_type);
			$b2b = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'b2b', $vm, $ticket_type, $merchant);

			$lintasanku = "-";
			$team_name = "-";

			if ($lintasan) {
				$data_lintasan = $lintasan->row();

				$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

				if ($lintasan->num_rows() > 1) {
					$lintasanku = "Semua";
				}
			}

			if ($header) {
				$data_header = $header->row();

				$team_name = $data_header->team_name;

				if ($header->num_rows() > 1) {
					$team_name = "-";
				};
			}

			if (!$tunai && !$cashless && !$online && !$ifcs && !$b2b) {
				$data =  array(
					'code' => 101,
					'message' => "Tidak ada data",
				);

				echo json_encode($data);
				exit;
			} else {
				$input = array(
					'code' => 200,
					'regu' => $team_name,
					'lintasan' => $lintasanku,
					'status_approve' => $keterangan_report,
					'tunai' => $tunai,
					'cashless' => $cashless,
					'online' => $online,
					'ifcs' => $ifcs,
					'ifcs_redeem' => $ifcs_redeem,
					'b2b' => $b2b,
				);

				echo json_encode($input);
				exit;
			}
		}

		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Penjualan Tiket Terpadu Tertagih',
			'content' => 'ticket_terpadu_tertagih/index',
			'port' => $this->global_model->getport(),
			'regu' => $this->global_model->getregu(),
			'petugas' => $this->global_model->getpetugas(),
			'shift' => $this->global_model->getshift(),
			'class' => $this->option_shift_class(),
			'merchant' => $this->get_merchant(),
			'vm' => $this->m_tertagih->getvm(),
			'download_pdf' => checkBtnAccess($this->_module, 'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module, 'download_excel'),
		);

		$this->load->view('default', $data);
	}

	public function get_merchant()
	{

		$html = '<option value="">Semua</option>';
		$get_data = $this->m_tertagih->get_merchant();
		foreach ($get_data as $k => $v) {
			$html .= '<option value="' . $this->enc->encode($v->merchant_id) . '">' . strtoupper($v->merchant_name) . '</option>';
		}

		return $html;
	}

	private function option_shift_class()
	{
		$html = '<option value="">Semua</option>';
		$shift_class = $this->m_tertagih->getClassBySession('option');
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
			$data = $this->m_tertagih->get_regu($port_id);
			$option = '<option value="" selected>Semua</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="' . $this->enc->encode($value->team_code) . '">' . $value->team_name . '</option>';
			}
			echo $option;
		}
	}

	function get_pdf()
	{
		$port = $this->enc->decode($this->input->get("port"));
		$vm = $this->enc->decode($this->input->get("vm"));
		$datefrom = $this->input->get("datefrom");
		$dateto = $this->input->get("dateto");
		$petugas = $this->enc->decode($this->input->get("petugas"));
		$shift = $this->enc->decode($this->input->get("shift"));
		$ticket_type = $this->enc->decode($this->input->get("ticket_type"));
		$merchant = $this->enc->decode($this->input->get('merchant'));
		// $ship_class = $this->enc->decode($this->input->get("ship_class"));
		$cek_sc = $this->m_tertagih->getClassBySession();
		if ($cek_sc == false) {
			$ship_class = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}

		$lintasan = $this->m_tertagih->get_lintasan($port, $datefrom, $dateto, $ship_class);
		$header = $this->m_tertagih->get_team($port, $datefrom, $dateto, $ship_class, $shift);
		$tunai = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'tunai', $vm, $ticket_type);
		$tunai_sobek = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'tunai_sobek', $vm, $ticket_type);
		$cashless = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'cashless', $vm, $ticket_type);
		$online = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'online', $vm, $ticket_type);
		$ifcs = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'ifcs', $vm, $ticket_type);
		$ifcs_redeem = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'ifcs_redeem', $vm, $ticket_type);
		$b2b = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'b2b', $vm, $ticket_type, $merchant);

		$keterangan_report = "Draft";

		$status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift, $ship_class);

		if ($status_approve) {
			if ($status_approve == 2) {
				$keterangan_report = "Approved";
			}
		}

		$nama_lintasan = "-";
		$team_name = "-";

		if ($lintasan) {
			$data_lintasan = $lintasan->row();

			$nama_lintasan = $data_lintasan->origin . " - " . $data_lintasan->destination;

			if ($lintasan->num_rows() > 1) {
				$nama_lintasan = "-";
			}
		}

		if ($header) {
			$data_header = $header->row();

			$team_name = $data_header->team_name;

			if ($header->num_rows() > 1) {
				$team_name = "-";
			};
		}

		$data['cabang'] = $this->input->get("cabangku");
		$data['pelabuhan'] = $this->input->get("pelabuhanku");
		$data['ship_class'] = $ship_classku;
		$data['shift'] = $this->input->get("shiftku");
		$data['vmkita'] = $this->input->get("vmku");
		$data['tipe_tiket'] = $this->input->get("ticket_typeku");
		$data['regu'] = $team_name;
		$data['petugas'] = $this->input->get("petugasku");
		$data['tanggal'] = format_date($datefrom) . " - " . format_date($dateto);
		$data['lintasan'] = $nama_lintasan;
		$data['status_approve'] = $keterangan_report;
		$data['tunai'] = $tunai;
		$data['tunai_sobek'] = $tunai_sobek;
		$data['cashless'] = $cashless;
		$data['online'] = $online;
		$data['ifcs'] = $ifcs;
		$data['ifcs_redeem'] = $ifcs_redeem;
		$data['b2b'] = $b2b;
		$data['merchant'] = $this->input->get("merchantKu");

		$this->load->view('laporan/ticket_terpadu_tertagih/pdf', $data);
	}

	function get_excel()
	{
		$excel_name = "Tiket_terpadu_tertagih";
		$port = $this->enc->decode($this->input->get("port"));
		$datefrom = $this->input->get("datefrom");
		$dateto = $this->input->get("dateto");
		$petugas = $this->enc->decode($this->input->get("petugas"));
		$shift = $this->enc->decode($this->input->get("shift"));
		$merchant = $this->enc->decode($this->input->get('merchant'));
		$petugasku = $this->input->get("petugasku");
		$ticket_type = $this->enc->decode($this->input->get('ticket_type'));
		$vm = $this->enc->decode($this->input->get("vm"));
		// $ship_class = $this->enc->decode($this->input->get("ship_class"));
		$cek_sc = $this->m_tertagih->getClassBySession();
		if ($cek_sc == false) {
			$ship_class = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}

		$lintasan = $this->m_tertagih->get_lintasan($port, $datefrom, $dateto, $ship_class);
		$header = $this->m_tertagih->get_team($port, $datefrom, $dateto, $ship_class, $shift);
		$tunai = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'tunai', $vm, $ticket_type);
		$tunai_sobek = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'tunai_sobek', $vm, $ticket_type);
		$cashless = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'cashless', $vm, $ticket_type);
		$online = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'online', $vm, $ticket_type);
		$ifcs = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'ifcs', $vm, $ticket_type);
		$ifcs_redeem = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'ifcs_redeem', $vm, $ticket_type);
		$b2b = $this->m_tertagih->list_data($port, $datefrom, $dateto, $petugas, $shift, $ship_class, 'b2b', $vm, $ticket_type, $merchant);

		$keterangan_report = "Draft";

		$status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift, $ship_class);

		if ($status_approve) {
			if ($status_approve == 2) {
				$keterangan_report = "Approved";
			}
		}

		$nama_lintasan = "-";
		$team_name = "-";

		if ($lintasan) {
			$data_lintasan = $lintasan->row();

			$nama_lintasan = $data_lintasan->origin . " - " . $data_lintasan->destination;

			if ($lintasan->num_rows() > 1) {
				$nama_lintasan = "-";
			}
		}

		if ($header) {
			$data_header = $header->row();

			$team_name = $data_header->team_name;

			if ($header->num_rows() > 1) {
				$team_name = "-";
			};
		}

		$cabang = $this->input->get("cabangku");
		$pelabuhan = $this->input->get("pelabuhanku");
		$ship_class = $ship_classku;
		$shift = $this->input->get("shiftku");
		$petugas = $this->input->get("petugasku");
		$vmkita = $this->input->get("vmku");
		$tipe_tiket = $this->input->get("ticket_typeku");
		$merchantku = $this->input->get("merchantKu");

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Tiket_tertagih_" . $pelabuhan . "_" . $datefrom . "_" . $dateto . "_" . $ship_class . "_" . $shift . ".xlsx");

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

		$judul_kita = array(
			"NO.",
			"JENIS",
			"TARIF (Rp.)",
			"PRODUKSI (Lbr.)",
			"PENDAPATAN (Rp.)",
		);

		$produksi_tunai = 0;
		$pendapatan_tunai = 0;

		foreach ($tunai as $key => $value) {
			$produksi_tunai += $value->produksi;
			$pendapatan_tunai += $value->pendapatan;

			$tunais[] = array(
				$key + 1,
				$value->golongan,
				$value->harga,
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
				$value->harga,
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
				$value->harga,
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
				$value->harga,
				$value->produksi,
				$value->pendapatan,
			);
		}

		$produksi_ifcs_redeem = 0;
		$pendapatan_ifcs_redeem = 0;

		foreach ($ifcs_redeem as $key => $value) {
			$produksi_ifcs_redeem += $value->produksi;
			$pendapatan_ifcs_redeem += $value->pendapatan;

			$data_redeem[] = array(
				$key + 1,
				$value->golongan,
				$value->harga,
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
				$value->harga,
				$value->produksi,
				$value->pendapatan,
			);
		}

		$writer->writeSheetRow($sheet1, array("LAPORAN PRODUKSI DAN PENDAPATAN TIKET TERPADU TERTAGIH PER-SHIFT"), $style_header);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("CABANG", $cabang, "", "REGU", $team_name));
		$writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "", "PETUGAS", $petugas));
		$writer->writeSheetRow($sheet1, array("LINTASAN", $nama_lintasan, "", "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
		$writer->writeSheetRow($sheet1, array("SHIFT", $shift, "", "VENDING MACHINE", $vmkita));
		$writer->writeSheetRow($sheet1, array("STATUS", $keterangan_report, "", "TIPE TIKET", $tipe_tiket));
		$writer->writeSheetRow($sheet1, array("MERCHANT", $merchantku));
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : TUNAI"));
		$writer->writeSheetRow($sheet1, $judul_kita, $styles1);

		if ($tunai) {
			foreach ($tunais as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_tunai, $pendapatan_tunai), $style_sub);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : PREPAID CASHLESS"));
		$writer->writeSheetRow($sheet1, $judul_kita, $styles1);

		if ($cashless) {
			foreach ($cashlessis as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_cashless, $pendapatan_cashless), $style_sub);

		$writer->writeSheetRow($sheet1, array(""));
		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : TIKET ONLINE"));
		$writer->writeSheetRow($sheet1, $judul_kita, $styles1);

		if ($online) {
			foreach ($onlineis as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_online, $pendapatan_online), $style_sub);

		$writer->writeSheetRow($sheet1, array(""));
		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : IFCS"));
		$writer->writeSheetRow($sheet1, $judul_kita, $styles1);

		if ($ifcs) {
			foreach ($ifcsis as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_ifcs, $pendapatan_ifcs), $style_sub);

		$writer->writeSheetRow($sheet1, array(""));
		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : IFCS Redeem"));
		$writer->writeSheetRow($sheet1, $judul_kita, $styles1);

		if ($ifcs_redeem) {
			foreach ($data_redeem as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_ifcs_redeem, $pendapatan_ifcs_redeem), $style_sub);

		$writer->writeSheetRow($sheet1, array(""));
		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : B2B"));
		$writer->writeSheetRow($sheet1, $judul_kita, $styles1);

		if ($b2b) {
			foreach ($b2bis as $row) {
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_b2b, $pendapatan_b2b), $style_sub);

		$writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (Tunai + Cashless + Online + IFCS + IFCS Redeem + B2B)", "", "", $produksi_tunai + $produksi_cashless + $produksi_online + $produksi_ifcs + $produksi_ifcs_redeem + $produksi_b2b, $pendapatan_tunai + $pendapatan_cashless + $pendapatan_online + $pendapatan_ifcs + $pendapatan_ifcs_redeem + $pendapatan_b2b), $style_sub);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$writer->writeToStdOut();
	}
}
