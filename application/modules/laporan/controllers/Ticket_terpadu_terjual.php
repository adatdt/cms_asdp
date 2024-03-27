<?php

error_reporting(0);

class Ticket_terpadu_terjual extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Html2pdf');
		$this->load->model('Ticket_terjual_model', 'm_terjual');
		$this->_module   = 'laporan/ticket_terpadu_terjual';
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
			$payment_type = $this->input->post('payment_type');
			$ticket_type = $this->enc->decode($this->input->post('ticket_type'));
			$port = $this->enc->decode($this->input->post('port'));
			$datefrom = $this->input->post('datefrom');
			$dateto = $this->input->post('dateto');
			$regu = $this->enc->decode($this->input->post('regu'));
			$petugas = $this->enc->decode($this->input->post('petugas'));
			$shift = $this->enc->decode($this->input->post('shift'));
			$merchant = $this->enc->decode($this->input->post('merchant'));
			// $ship_class = $this->enc->decode($this->input->post('ship_class'));
			$vm = $this->enc->decode($this->input->post('vm'));
			$cek_sc = $this->m_terjual->getClassBySession();
			if ($cek_sc == false) {
				$ship_class = $this->enc->decode($this->input->post('ship_class'));
			} else {
				$ship_class = $cek_sc['id'];
			}

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

			$lintasan = $this->m_terjual->get_lintasan($port, $datefrom, $dateto, $ship_class);


			if ($payment_type === "all") {
				$tunai = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'tunai', $vm, $ticket_type);
				$cashless = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'cashless', $vm, $ticket_type);
				$online = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'online', $vm, $ticket_type);
				$ifcs = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs', $vm, $ticket_type);
				$ifcs_redeem = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs_redeem', $vm, $ticket_type);
				$b2b = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'b2b', $vm, $ticket_type, $merchant);

				$lintasanku = "-";

				if ($lintasan) {
					$data_lintasan = $lintasan->row();

					$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

					if ($lintasan->num_rows() > 1) {
						$lintasanku = "Semua";
					}
				}

				if (!$tunai && !$cashless && !$online) {
					$data =  array(
						'code' => 101,
						'message' => "Tidak ada data",
					);

					echo json_encode($data);
					exit;
				} else {
					$input = array(
						'code' => 200,
						'payment_type' => "all",
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
			} elseif ($payment_type === "cash") {
				$tunai = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'tunai', $vm, $ticket_type);

				$lintasanku = "-";

				if ($lintasan) {
					$data_lintasan = $lintasan->row();

					$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

					if ($lintasan->num_rows() > 1) {
						$lintasanku = "Semua";
					}
				}

				if (!$tunai) {
					$data =  array(
						'code' => 101,
						'message' => "Tidak ada data",
					);

					echo json_encode($data);
					exit;
				} else {
					$input = array(
						'code' => 200,
						'payment_type' => "cash",
						'lintasan' => $lintasanku,
						'status_approve' => $keterangan_report,
						'tunai' => $tunai
					);

					echo json_encode($input);
					exit;
				}
			} elseif ($payment_type === "cashless") {
				$lintasanku = "-";
				$cashless = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'cashless', $vm, $ticket_type);

				if ($lintasan) {
					$data_lintasan = $lintasan->row();

					$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

					if ($lintasan->num_rows() > 1) {
						$lintasanku = "Semua";
					}
				}

				if (!$cashless) {
					$data =  array(
						'code' => 101,
						'message' => "Tidak ada data",
					);

					echo json_encode($data);
					exit;
				} else {
					$input = array(
						'code' => 200,
						'payment_type' => "cashless",
						'lintasan' => $lintasanku,
						'status_approve' => $keterangan_report,
						'cashless' => $cashless,
					);

					echo json_encode($input);
					exit;
				}
			} elseif ($payment_type === "online") {
				$lintasanku = "-";
				$online = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'online', $vm, $ticket_type);

				if ($lintasan) {
					$data_lintasan = $lintasan->row();

					$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

					if ($lintasan->num_rows() > 1) {
						$lintasanku = "Semua";
					}
				}

				if (!$online) {
					$data =  array(
						'code' => 101,
						'message' => "Tidak ada data",
					);

					echo json_encode($data);
					exit;
				} else {
					$input = array(
						'code' => 200,
						'payment_type' => "online",
						'lintasan' => $lintasanku,
						'status_approve' => $keterangan_report,
						'online' => $online,
					);

					echo json_encode($input);
					exit;
				}
			} elseif ($payment_type === "ifcs") {
				$lintasanku = "-";
				$ifcs = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs', $vm, $ticket_type);

				if ($lintasan) {
					$data_lintasan = $lintasan->row();

					$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

					if ($lintasan->num_rows() > 1) {
						$lintasanku = "Semua";
					}
				}

				if (!$ifcs) {
					$data =  array(
						'code' => 101,
						'message' => "Tidak ada data",
					);

					echo json_encode($data);
					exit;
				} else {
					$input = array(
						'code' => 200,
						'payment_type' => "ifcs",
						'lintasan' => $lintasanku,
						'status_approve' => $keterangan_report,
						'ifcs' => $ifcs,
					);

					echo json_encode($input);
					exit;
				}
			} elseif ($payment_type === "ifcs_redeem") {
				$lintasanku = "-";
				$ifcs_redeem = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs_redeem', $vm, $ticket_type);

				if ($lintasan) {
					$data_lintasan = $lintasan->row();

					$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

					if ($lintasan->num_rows() > 1) {
						$lintasanku = "Semua";
					}
				}

				if (!$ifcs_redeem) {
					$data =  array(
						'code' => 101,
						'message' => "Tidak ada data",
					);

					echo json_encode($data);
					exit;
				} else {
					$input = array(
						'code' => 200,
						'payment_type' => "ifcs_redeem",
						'lintasan' => $lintasanku,
						'status_approve' => $keterangan_report,
						'ifcs_redeem' => $ifcs_redeem,
					);

					echo json_encode($input);
					exit;
				}
			} elseif ($payment_type === "b2b") {
				$lintasanku = "-";
				$b2b = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'b2b', $vm, $ticket_type, $merchant);

				if ($lintasan) {
					$data_lintasan = $lintasan->row();

					$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

					if ($lintasan->num_rows() > 1) {
						$lintasanku = "Semua";
					}
				}

				if (!$b2b) {
					$data =  array(
						'code' => 101,
						'message' => "Tidak ada data",
					);

					echo json_encode($data);
					exit;
				} else {
					$input = array(
						'code' => 200,
						'payment_type' => "b2b",
						'lintasan' => $lintasanku,
						'status_approve' => $keterangan_report,
						'b2b' => $b2b,
					);

					echo json_encode($input);
					exit;
				}
			}
		}

		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Penjualan Tiket Terpadu Terjual',
			'content' => 'ticket_terpadu_terjual/index',
			'port' => $this->m_terjual->getport(),
			'regu' => $this->m_terjual->getregu(),
			'petugas' => $this->m_terjual->getpetugas(),
			'vm' => $this->m_terjual->getvm(),
			'shift' => $this->m_terjual->getshift(),
			'class' => $this->option_shift_class(),
			'download_pdf' => checkBtnAccess($this->_module, 'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module, 'download_excel'),
		);

		$this->load->view('default', $data);
	}

	private function option_shift_class()
	{
		$html = '<option value="">Semua</option>';
		$shift_class = $this->m_terjual->getClassBySession('option');
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

	public function get_merchant()
	{
		$channel = $this->input->post('channel');
		$data = array();
		if (strtolower($channel) == 'b2b') {
			$data[] = array("id" => "", "name" => "Semua");
			$get_data = $this->m_terjual->get_merchant();
			foreach ($get_data as $k => $v) {
				$data[] = array(
					'id' => $this->enc->encode($v->merchant_id),
					'name' => strtoupper($v->merchant_name)
				);
			}
		}
		echo json_encode($data);
	}

	function get_regu($port_id = "")
	{
		validate_ajax();
		$port_id = $this->enc->decode($port_id);

		if (!$port_id) {
			$option = '<option value="" selected>Semua</option>';
			echo $option;
		} else {
			$data = $this->m_terjual->get_team($port_id);
			$option = '<option value="" selected>Semua</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="' . $this->enc->encode($value->team_code) . '">' . $value->team_name . '</option>';
			}
			echo $option;
		}
	}

	function get_pdf()
	{
		$payment_type = $this->input->get("payment_type");
		$port = $this->enc->decode($this->input->get("port"));
		$datefrom = $this->input->get("datefrom");
		$dateto = $this->input->get("dateto");
		$regu = $this->enc->decode($this->input->get("regu"));
		$petugas = $this->enc->decode($this->input->get("petugas"));
		$shift = $this->enc->decode($this->input->get("shift"));
		// $ship_class = $this->enc->decode($this->input->get("ship_class"));
		$vm = $this->enc->decode($this->input->get("vm"));
		$ticket_type = $this->enc->decode($this->input->get("ticket_type"));
		$merchant = $this->enc->decode($this->input->get('merchant'));
		$cek_sc = $this->m_terjual->getClassBySession();
		if ($cek_sc == false) {
			$ship_class = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}

		$lintasan = $this->m_terjual->get_lintasan($port, $datefrom, $dateto, $ship_class);

		$keterangan_report = "DRAFT";

		$status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift, $ship_class);

		if ($status_approve) {
			if ($status_approve == 2) {
				$keterangan_report = "APPROVED";
			}
		}

		$lintasanku = "-";

		if ($lintasan) {
			$data_lintasan = $lintasan->row();

			$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

			if ($lintasan->num_rows() > 1) {
				$lintasanku = "Semua";
			}
		}

		if ($payment_type === "all") {
			$tunai = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'tunai', $vm, $ticket_type);
			$tunai_sobek = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'tunai_sobek', $vm, $ticket_type);
			$cashless = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'cashless', $vm, $ticket_type);
			$online = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'online', $vm, $ticket_type);
			$ifcs = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs', $vm, $ticket_type);
			$ifcs_redeem = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs_redeem', $vm, $ticket_type);
			$b2b = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'b2b', $vm, $ticket_type, $merchant);

			$data['cabang'] = $this->input->get("cabangku");
			$data['pelabuhan'] = $this->input->get("pelabuhanku");
			$data['ship_class'] = $ship_classku;
			$data['shift'] = $this->input->get("shiftku");
			$data['regu'] = $this->input->get("reguku");
			$data['petugas'] = $this->input->get("petugasku");
			$data['vmkita'] = $this->input->get("vmku");
			$data['tipe_tiket'] = $this->input->get("ticket_typeku");
			$data['tanggal'] = format_date($datefrom) . " - " . format_date($dateto);
			$data['lintasan'] = $lintasanku;
			$data['status_approve'] = $keterangan_report;
			$data['tunai'] = $tunai;
			$data['tunai_sobek'] = $tunai_sobek;
			$data['cashless'] = $cashless;
			$data['online'] = $online;
			$data['ifcs'] = $ifcs;
			$data['ifcs_redeem'] = $ifcs_redeem;
			$data['b2b'] = $b2b;

			$this->load->view('laporan/ticket_terpadu_terjual/pdf', $data);
		} elseif ($payment_type === "cash") {
			$tunai = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'tunai', $vm, $ticket_type);

			$data['cabang'] = $this->input->get("cabangku");
			$data['pelabuhan'] = $this->input->get("pelabuhanku");
			$data['ship_class'] = $ship_classku;
			$data['shift'] = $this->input->get("shiftku");
			$data['regu'] = $this->input->get("reguku");
			$data['petugas'] = $this->input->get("petugasku");
			$data['vmkita'] = $this->input->get("vmku");
			$data['tipe_tiket'] = $this->input->get("ticket_typeku");
			$data['tanggal'] = format_date($datefrom) . " - " . format_date($dateto);
			$data['lintasan'] = $lintasanku;
			$data['status_approve'] = $keterangan_report;
			$data['tunai'] = $tunai;

			$this->load->view('laporan/ticket_terpadu_terjual/pdf_tunai', $data);
		} elseif ($payment_type === "cash_sobek") {
			$tunai_sobek = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'tunai_sobek', $vm, $ticket_type);

			$data['cabang'] = $this->input->get("cabangku");
			$data['pelabuhan'] = $this->input->get("pelabuhanku");
			$data['ship_class'] = $ship_classku;
			$data['shift'] = $this->input->get("shiftku");
			$data['regu'] = $this->input->get("reguku");
			$data['petugas'] = $this->input->get("petugasku");
			$data['vmkita'] = $this->input->get("vmku");
			$data['tipe_tiket'] = $this->input->get("ticket_typeku");
			$data['tanggal'] = format_date($datefrom) . " - " . format_date($dateto);
			$data['lintasan'] = $lintasanku;
			$data['status_approve'] = $keterangan_report;
			$data['tunai_sobek'] = $tunai_sobek;

			$this->load->view('laporan/ticket_terpadu_terjual/pdf_tunai_sobek', $data);
		} elseif ($payment_type === "cashless") {
			$cashless = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'cashless', $vm, $ticket_type);

			$data['cabang'] = $this->input->get("cabangku");
			$data['pelabuhan'] = $this->input->get("pelabuhanku");
			$data['ship_class'] = $ship_classku;
			$data['shift'] = $this->input->get("shiftku");
			$data['regu'] = $this->input->get("reguku");
			$data['petugas'] = $this->input->get("petugasku");
			$data['tipe_tiket'] = $this->input->get("ticket_typeku");
			$data['tanggal'] = format_date($datefrom) . " - " . format_date($dateto);
			$data['lintasan'] = $lintasanku;
			$data['status_approve'] = $keterangan_report;
			$data['cashless'] = $cashless;

			$this->load->view('laporan/ticket_terpadu_terjual/pdf_cashless', $data);
		} elseif ($payment_type === "online") {
			$online = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'online', $vm, $ticket_type);

			$data['cabang'] = $this->input->get("cabangku");
			$data['pelabuhan'] = $this->input->get("pelabuhanku");
			$data['ship_class'] = $ship_classku;
			$data['shift'] = $this->input->get("shiftku");
			$data['regu'] = $this->input->get("reguku");
			$data['petugas'] = $this->input->get("petugasku");
			$data['vmkita'] = $this->input->get("vmku");
			$data['tipe_tiket'] = $this->input->get("ticket_typeku");
			$data['tanggal'] = format_date($datefrom) . " - " . format_date($dateto);
			$data['lintasan'] = $lintasanku;
			$data['status_approve'] = $keterangan_report;
			$data['online'] = $online;

			$this->load->view('laporan/ticket_terpadu_terjual/pdf_online', $data);
		} elseif ($payment_type === "ifcs") {
			$ifcs = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs', $vm, $ticket_type);

			$data['cabang'] = $this->input->get("cabangku");
			$data['pelabuhan'] = $this->input->get("pelabuhanku");
			$data['ship_class'] = $ship_classku;
			$data['shift'] = $this->input->get("shiftku");
			$data['regu'] = $this->input->get("reguku");
			$data['petugas'] = $this->input->get("petugasku");
			$data['vmkita'] = $this->input->get("vmku");
			$data['tipe_tiket'] = $this->input->get("ticket_typeku");
			$data['tanggal'] = format_date($datefrom) . " - " . format_date($dateto);
			$data['lintasan'] = $lintasanku;
			$data['status_approve'] = $keterangan_report;
			$data['ifcs'] = $ifcs;

			$this->load->view('laporan/ticket_terpadu_terjual/pdf_ifcs', $data);
		} elseif ($payment_type === "ifcs_redeem") {
			$ifcs = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs_redeem', $vm, $ticket_type);

			$data['cabang'] = $this->input->get("cabangku");
			$data['pelabuhan'] = $this->input->get("pelabuhanku");
			$data['ship_class'] = $ship_classku;
			$data['shift'] = $this->input->get("shiftku");
			$data['regu'] = $this->input->get("reguku");
			$data['petugas'] = $this->input->get("petugasku");
			$data['vmkita'] = $this->input->get("vmku");
			$data['tipe_tiket'] = $this->input->get("ticket_typeku");
			$data['tanggal'] = format_date($datefrom) . " - " . format_date($dateto);
			$data['lintasan'] = $lintasanku;
			$data['status_approve'] = $keterangan_report;
			$data['ifcs'] = $ifcs;

			$this->load->view('laporan/ticket_terpadu_terjual/pdf_redeem', $data);
		} elseif ($payment_type === "b2b") {
			$b2b = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'b2b', $vm, $ticket_type, $merchant);

			$data['cabang'] = $this->input->get("cabangku");
			$data['pelabuhan'] = $this->input->get("pelabuhanku");
			$data['ship_class'] = $ship_classku;
			$data['shift'] = $this->input->get("shiftku");
			$data['regu'] = $this->input->get("reguku");
			$data['petugas'] = $this->input->get("petugasku");
			$data['vmkita'] = $this->input->get("vmku");
			$data['tipe_tiket'] = $this->input->get("ticket_typeku");
			$data['tanggal'] = format_date($datefrom) . " - " . format_date($dateto);
			$data['lintasan'] = $lintasanku;
			$data['status_approve'] = $keterangan_report;
			$data['b2b'] = $b2b;
			$data['merchant'] = $this->input->get("merchantKu");

			$this->load->view('laporan/ticket_terpadu_terjual/pdf_b2b', $data);
		}
	}

	function get_excel()
	{
		$excel_name = "Tiket_terpadu_terjual";
		$payment_type = $this->input->get("payment_type");

		$vm = $this->enc->decode($this->input->get("vm"));
		$port = $this->enc->decode($this->input->get("port"));
		$datefrom = $this->input->get("datefrom");
		$dateto = $this->input->get("dateto");
		$regu = $this->enc->decode($this->input->get("regu"));
		$petugas = $this->enc->decode($this->input->get("petugas"));
		// $ship_class = $this->enc->decode($this->input->get("ship_class"));
		$shift = $this->enc->decode($this->input->get("shift"));
		$petugasku = $this->input->get("petugasku");
		$pelabuhan = $this->input->get("pelabuhanku");
		$ticket_type = $this->enc->decode($this->input->get('ticket_type'));
		$merchant = $this->enc->decode($this->input->get('merchant'));
		$cek_sc = $this->m_terjual->getClassBySession();
		if ($cek_sc == false) {
			$ship_class = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}

		$lintasan = $this->m_terjual->get_lintasan($port, $datefrom, $dateto, $ship_class);

		$keterangan_report = "DRAFT";

		$status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift, $ship_class);

		if ($status_approve) {
			if ($status_approve == 2) {
				$keterangan_report = "APPROVED";
			}
		}

		$lintasanku = "-";

		if ($lintasan) {
			$data_lintasan = $lintasan->row();

			$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

			if ($lintasan->num_rows() > 1) {
				$lintasanku = "Semua";
			}
		}

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Tiket_terjual_" . $pelabuhan . "_" . trim($datefrom, "-") . "-" . trim($datefrom, "-") . ".xlsx");

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
			"TARIF (Rp.)",
			"PRODUKSI (Lbr.)",
			"PENDAPATAN (Rp.)",
		);

		if ($payment_type === "all") {
			$tunai = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'tunai', $vm, $ticket_type);
			$cashless = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'cashless', $vm, $ticket_type);
			$online = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'online', $vm, $ticket_type);
			$ifcs = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs', $vm, $ticket_type);
			$ifcs_redeem = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs_redeem', $vm, $ticket_type);
			$b2b = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'b2b', $vm, $ticket_type, $merchant);

			$cabang = $this->input->get("cabangku");
			$pelabuhan = $this->input->get("pelabuhanku");
			$ship_class = $ship_classku;
			$shift = $this->input->get("shiftku");
			$team_name = $this->input->get("reguku");
			$petugas = $this->input->get("petugasku");
			$vmkita = $this->input->get("vmku");
			$tipe_tiket = $this->input->get("ticket_typeku");

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

			$writer->writeSheetRow($sheet1, array("LAPORAN PRODUKSI DAN PENDAPATAN TIKET TERPADU TERJUAL PER-SHIFT"), $styles_ha);
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, array("CABANG", $cabang, "", "REGU", $team_name));
			$writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "", "PETUGAS", $petugas));
			$writer->writeSheetRow($sheet1, array("LINTASAN", $lintasanku, "", "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
			$writer->writeSheetRow($sheet1, array("SHIFT", $shift, "", "VENDING MACHINE", $vmkita));
			$writer->writeSheetRow($sheet1, array("STATUS", $keterangan_report, "", "TIPE TIKET", $tipe_tiket));
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : TUNAI "));
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

			// REMARK IFCS

			$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : IFCS"));
			$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);

			if ($ifcs) {
				foreach ($ifcsis as $row) {
					$writer->writeSheetRow($sheet1, $row, $styles2);
				}
			}

			$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_ifcs, $pendapatan_ifcs), $style_sub);
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : IFCS REDEEM"));
			$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);

			if ($ifcs_redeem) {
				foreach ($data_redeem as $row) {
					$writer->writeSheetRow($sheet1, $row, $styles2);
				}
			}

			$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_ifcs_redeem, $pendapatan_ifcs_redeem), $style_sub);
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

			$writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (Tunai + Cashless + Online + IFCS + IFCS Redeem + B2B)", "", "", $produksi_tunai + $produksi_cashless + $produksi_online + $produksi_ifcs + $produksi_ifcs_redeem + $produksi_b2b, $pendapatan_tunai + $pendapatan_cashless + $pendapatan_online + $pendapatan_ifcs + $pendapatan_ifcs_redeem + $pendapatan_b2b), $style_sub);
			$writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			$writer->writeToStdOut();
		} elseif ($payment_type === "cash") {
			$tunai = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'tunai', $vm, $ticket_type);

			$cabang = $this->input->get("cabangku");
			$pelabuhan = $this->input->get("pelabuhanku");
			$ship_class = $ship_classku;
			$shift = $this->input->get("shiftku");
			$team_name = $this->input->get("reguku");
			$petugas = $this->input->get("petugasku");
			$vmkita = $this->input->get("vmku");
			$tipe_tiket = $this->input->get("ticket_typeku");

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

			$writer->writeSheetRow($sheet1, array("LAPORAN PRODUKSI DAN PENDAPATAN TIKET TERPADU TERJUAL PER-SHIFT"), $styles_ha);
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, array("CABANG", $cabang, "", "REGU", $team_name));
			$writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "", "PETUGAS", $petugas));
			$writer->writeSheetRow($sheet1, array("LINTASAN", $lintasanku, "", "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
			$writer->writeSheetRow($sheet1, array("SHIFT", $shift, "", "VENDING MACHINE", $vmkita));
			$writer->writeSheetRow($sheet1, array("STATUS", $keterangan_report, "", "TIPE TIKET", $tipe_tiket));
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

			$writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (Tunai)", "", "", $produksi_tunai, $pendapatan_tunai), $style_sub);
			$writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			$writer->writeToStdOut();
		} elseif ($payment_type === "cashless") {
			$cashless = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'cashless', $vm, $ticket_type);

			$cabang = $this->input->get("cabangku");
			$pelabuhan = $this->input->get("pelabuhanku");
			$ship_class = $ship_classku;
			$shift = $this->input->get("shiftku");
			$team_name = $this->input->get("reguku");
			$petugas = $this->input->get("petugasku");
			$vmkita = $this->input->get("vmku");
			$tipe_tiket = $this->input->get("ticket_typeku");

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

			$writer->writeSheetRow($sheet1, array("LAPORAN PRODUKSI DAN PENDAPATAN TIKET TERPADU TERJUAL PER-SHIFT"), $styles_ha);
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, array("CABANG", $cabang, "", "REGU", $team_name));
			$writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "", "PETUGAS", $petugas));
			$writer->writeSheetRow($sheet1, array("LINTASAN", $lintasanku, "", "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
			$writer->writeSheetRow($sheet1, array("SHIFT", $shift, "", "VENDING MACHINE", $vmkita));
			$writer->writeSheetRow($sheet1, array("STATUS", $keterangan_report, "", "TIPE TIKET", $tipe_tiket));
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

			$writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (Cashless)", "", "", $produksi_cashless, $pendapatan_cashless), $style_sub);
			$writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			$writer->writeToStdOut();
		} elseif ($payment_type === "online") {
			$online = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'online', $vm, $ticket_type);

			$cabang = $this->input->get("cabangku");
			$pelabuhan = $this->input->get("pelabuhanku");
			$ship_class = $ship_classku;
			$shift = $this->input->get("shiftku");
			$team_name = $this->input->get("reguku");
			$petugas = $this->input->get("petugasku");
			$vmkita = $this->input->get("vmku");
			$tipe_tiket = $this->input->get("ticket_typeku");

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

			$writer->writeSheetRow($sheet1, array("LAPORAN PRODUKSI DAN PENDAPATAN TIKET TERPADU TERJUAL PER-SHIFT"), $styles_ha);
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, array("CABANG", $cabang, "", "REGU", $team_name));
			$writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "", "PETUGAS", $petugas));
			$writer->writeSheetRow($sheet1, array("LINTASAN", $lintasanku, "", "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
			$writer->writeSheetRow($sheet1, array("SHIFT", $shift, "", "VENDING MACHINE", $vmkita));
			$writer->writeSheetRow($sheet1, array("STATUS", $keterangan_report, "", "TIPE TIKET", $tipe_tiket));
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

			$writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (Online)", "", "", $produksi_online, $pendapatan_online), $style_sub);
			$writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			$writer->writeToStdOut();
		} elseif ($payment_type === "ifcs") {
			$ifcs = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs', $vm, $ticket_type);

			$cabang = $this->input->get("cabangku");
			$pelabuhan = $this->input->get("pelabuhanku");
			$ship_class = $ship_classku;
			$shift = $this->input->get("shiftku");
			$team_name = $this->input->get("reguku");
			$petugas = $this->input->get("petugasku");
			$vmkita = $this->input->get("vmku");
			$tipe_tiket = $this->input->get("ticket_typeku");

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

			$writer->writeSheetRow($sheet1, array("LAPORAN PRODUKSI DAN PENDAPATAN TIKET TERPADU TERJUAL PER-SHIFT"), $styles_ha);
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, array("CABANG", $cabang, "", "REGU", $team_name));
			$writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "", "PETUGAS", $petugas));
			$writer->writeSheetRow($sheet1, array("LINTASAN", $lintasanku, "", "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
			$writer->writeSheetRow($sheet1, array("SHIFT", $shift, "", "VENDING MACHINE", $vmkita));
			$writer->writeSheetRow($sheet1, array("STATUS", $keterangan_report, "", "TIPE TIKET", $tipe_tiket));
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

			$writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (IFCS)", "", "", $produksi_ifcs, $pendapatan_ifcs), $style_sub);
			$writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			$writer->writeToStdOut();
		} elseif ($payment_type === "ifcs_redeem") {
			$ifcs_redeem = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'ifcs_redeem', $vm, $ticket_type);

			$cabang = $this->input->get("cabangku");
			$pelabuhan = $this->input->get("pelabuhanku");
			$ship_class = $ship_classku;
			$shift = $this->input->get("shiftku");
			$team_name = $this->input->get("reguku");
			$petugas = $this->input->get("petugasku");
			$vmkita = $this->input->get("vmku");
			$tipe_tiket = $this->input->get("ticket_typeku");

			$produksi_ifcs = 0;
			$pendapatan_ifcs = 0;

			foreach ($ifcs_redeem as $key => $value) {
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

			$writer->writeSheetRow($sheet1, array("LAPORAN PRODUKSI DAN PENDAPATAN TIKET TERPADU TERJUAL PER-SHIFT"), $styles_ha);
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, array("CABANG", $cabang, "", "REGU", $team_name));
			$writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "", "PETUGAS", $petugas));
			$writer->writeSheetRow($sheet1, array("LINTASAN", $lintasanku, "", "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
			$writer->writeSheetRow($sheet1, array("SHIFT", $shift, "", "VENDING MACHINE", $vmkita));
			$writer->writeSheetRow($sheet1, array("STATUS", $keterangan_report, "", "TIPE TIKET", $tipe_tiket));
			$writer->writeSheetRow($sheet1, array(""));
			$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : IFCS Redeem"));
			$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);

			if ($ifcs_redeem) {
				foreach ($ifcsis as $row) {
					$writer->writeSheetRow($sheet1, $row, $styles2);
				}
			}

			$writer->writeSheetRow($sheet1, array("Sub Total", "", "", $produksi_ifcs, $pendapatan_ifcs), $style_sub);
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (IFCS Redeem)", "", "", $produksi_ifcs, $pendapatan_ifcs), $style_sub);
			$writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			$writer->writeToStdOut();
		} elseif ($payment_type === "b2b") {
			$b2b = $this->m_terjual->list_data($port, $datefrom, $dateto, $regu, $petugas, $shift, $ship_class, 'b2b', $vm, $ticket_type, $merchant);

			$cabang = $this->input->get("cabangku");
			$pelabuhan = $this->input->get("pelabuhanku");
			$ship_class = $ship_classku;
			$shift = $this->input->get("shiftku");
			$team_name = $this->input->get("reguku");
			$petugas = $this->input->get("petugasku");
			$vmkita = $this->input->get("vmku");
			$tipe_tiket = $this->input->get("ticket_typeku");
			$merchantku = $this->input->get("merchantKu");

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

			$writer->writeSheetRow($sheet1, array("LAPORAN PRODUKSI DAN PENDAPATAN TIKET TERPADU TERJUAL PER-SHIFT"), $styles_ha);
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, array("CABANG", $cabang, "", "REGU", $team_name));
			$writer->writeSheetRow($sheet1, array("PELABUHAN", $pelabuhan, "", "PETUGAS", $petugas));
			$writer->writeSheetRow($sheet1, array("LINTASAN", $lintasanku, "", "TANGGAL", format_date($datefrom) . " - " . format_date($dateto)));
			$writer->writeSheetRow($sheet1, array("SHIFT", $shift, "", "VENDING MACHINE", $vmkita));
			$writer->writeSheetRow($sheet1, array("STATUS", $keterangan_report, "", "TIPE TIKET", $tipe_tiket));
			$writer->writeSheetRow($sheet1, array("MERCHANT", $merchantku));
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

			$writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (B2B)", "", "", $produksi_b2b, $pendapatan_b2b), $style_sub);
			$writer->markMergedCell($sheet1, $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			$writer->writeToStdOut();
		}
	}
}
