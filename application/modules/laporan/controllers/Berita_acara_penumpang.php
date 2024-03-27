<?php
error_reporting(E_ALL ^ E_WARNING);

class Berita_acara_penumpang extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('berita_acara_penumpang_model','berita_acara');
		$this->load->library('Html2pdf');
		$this->_module = 'laporan/berita_acara_penumpang';
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->berita_acara->list_data();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Berita Acara Penjualan Tiket Loket Penumpang',
			'content' => 'berita_acara_penumpang/index',
			'pelabuhan' => $this->berita_acara->port_list(),
			'regu' => $this->berita_acara->team_list(),
			'shift' => $this->berita_acara->shift_list(),
			'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	function detail($assignment_code="")
	{
		checkUrlAccess('laporan/berita_acara_penumpang','download_pdf');
		if ($this->enc->decode($assignment_code)) {
			$assignment_code = $this->enc->decode($assignment_code);
			// echo $assignment_code;exit;
			$tgl = $this->berita_acara->get_data_waktu($assignment_code);
			$hari = date('D',strtotime($tgl));
			$tanggal = date('d M Y',strtotime($tgl));

			$spv_name = $this->berita_acara->get_spv_name($assignment_code);
			$team_name = $this->berita_acara->get_team_name($assignment_code);
			$shift_name = $this->berita_acara->get_shift_name($assignment_code);

			$loket_tunai_dewasa = $this->berita_acara->get_detail($assignment_code,'loket_tunai_dewasa')->jumlah;
			$loket_tunai_anak = $this->berita_acara->get_detail($assignment_code,'loket_tunai_anak')->jumlah;

			$loket_non_tunai_dewasa = $this->berita_acara->get_detail($assignment_code,'loket_non_tunai_dewasa')->jumlah;
			$loket_non_tunai_anak = $this->berita_acara->get_detail($assignment_code,'loket_non_tunai_anak')->jumlah;

			$loket_online_dewasa = $this->berita_acara->get_detail($assignment_code,'loket_online_dewasa')->jumlah;
			$loket_online_anak = $this->berita_acara->get_detail($assignment_code,'loket_online_anak')->jumlah;

			$tarif_dewasa = ($this->berita_acara->get_detail($assignment_code,'tarif_dewasa')) ? $this->berita_acara->get_detail($assignment_code,'tarif_dewasa')->fare : 0;
			$tarif_anak = ($this->berita_acara->get_detail($assignment_code,'tarif_anak')) ? $this->berita_acara->get_detail($assignment_code,'tarif_anak')->fare : 0;

			$tunai_dewasa = $this->berita_acara->get_detail($assignment_code,'tunai_dewasa')->jumlah;
			$tunai_anak = $this->berita_acara->get_detail($assignment_code,'tunai_anak')->jumlah;

			$online_dewasa = $this->berita_acara->get_detail($assignment_code,'online_dewasa')->jumlah;
			$online_anak = $this->berita_acara->get_detail($assignment_code,'online_anak')->jumlah;

			$saldo_akhir_loket_dewasa = $loket_tunai_dewasa + $loket_non_tunai_dewasa + $loket_online_dewasa;
			$saldo_akhir_loket_anak = $loket_tunai_anak + $loket_non_tunai_anak + $loket_online_anak;

			$total_dewasa = $tunai_dewasa + $online_dewasa;
			$total_anak = $tunai_anak + $online_anak;

			$total_terbilang = $total_dewasa + $total_anak;
			$angka_terbilang = $total_dewasa + $total_anak;

			// echo $tanggal;

			// $hari = $this->berita_acara->get_data_waktu($assignment_code);
			// $detail = $this->berita_acara->get_detail($assignment_code);

			$data = array(
				'hari' => $hari,
				'tanggal' => $tanggal,
				'spv_name' => $spv_name,
				'team_name' => $team_name,
				'shift_name' => $shift_name,

				'tarif_dewasa' => $tarif_dewasa,
				'tarif_anak' => $tarif_anak,

				'loket_tunai_dewasa' => $loket_tunai_dewasa,
				'loket_tunai_anak' => $loket_tunai_anak,

				'loket_non_tunai_dewasa' => $loket_non_tunai_dewasa,
				'loket_non_tunai_anak' => $loket_non_tunai_anak,

				'loket_online_dewasa' => $loket_online_dewasa,
				'loket_online_anak' => $loket_online_anak,

				'saldo_akhir_loket_dewasa' => $saldo_akhir_loket_dewasa,
				'saldo_akhir_loket_anak' => $saldo_akhir_loket_anak,

				'tunai_dewasa' => $tunai_dewasa,
				'tunai_anak' => $tunai_anak,

				'online_dewasa' => $online_dewasa,
				'online_anak' => $online_anak,

				'total_dewasa' => $total_dewasa,
				'total_anak' => $total_anak,

				'total_terbilang' => $total_terbilang,
				'angka_terbilang' => $angka_terbilang,
			);

			// echo json_encode($data);

			$this->load->view('berita_acara_penumpang/pdf', $data);
		}else{
			show_404();
		}
	}

	function old_detail($param)
	{
		checkUrlAccess('laporan/berita_acara_penumpang','download_pdf');
		// $this->load->view('laporan/berita_acara_penumpang/pdf');
		echo $this->enc->decode($param);exit;

		exit;

		if ($this->enc->decode($param)) {
			$param = $this->enc->decode($param);
			$param = explode("|", $param);

			$detail = $this->berita_acara->get_detail($param);

			$data = array(
				'hari' => date('D',strtotime($param[1])),
				'tanggal' => date('d M Y',strtotime($param[1])),
				'data' => $detail,
				// 'tahun' => date('Y',strtotime($param[1])),
			);

			// echo json_encode($detail);

			$this->load->view('berita_acara_penumpang/pdf', $data);

			// explode(|, string)
			// echo $this->enc->decode($param);
			// echo date('D d M Y',strtotime($param[1]));
			// echo json_encode($detail['data']);
			// $y = $data['data'];
			// foreach ($y as $key => $value) {
			// 	echo $value->team_code."<br>";
			// }
			// echo $param[0];
			// echo date("Y-m-d", strtotime($waktu));
			// echo $this->enc->decode($param);
		}else{
			show_404();
		}
	}

	function download_pdf()
	{
		$this->load->view('laporan/berita_acara_penumpang/pdf');
	}
}