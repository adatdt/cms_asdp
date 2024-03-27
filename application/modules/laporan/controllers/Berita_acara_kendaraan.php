<?php
error_reporting(E_ALL ^ E_WARNING);

class Berita_acara_kendaraan extends MY_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library('Html2pdf');
		$this->load->model('berita_acara_kendaraan_model','m_kendaraan');
		$this->_module   = 'laporan/berita_acara_kendaraan';
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->m_kendaraan->list_data();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Berita Acara Penjualan Tiket Loket Kendaraan',
			'content' => 'berita_acara_kendaraan/index',
			'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	public function detail($assignment_code="")
	{
		checkUrlAccess('laporan/berita_acara_kendaraan','download_pdf');
		if ($this->enc->decode($assignment_code)) {
			$assignment_code = $this->enc->decode($assignment_code);
			// echo $assignment_code;exit;

			$tgl = $this->m_kendaraan->get_data_waktu($assignment_code);
			$hari = date('D',strtotime($tgl));
			$tanggal = date('d M Y',strtotime($tgl));

			$spv_name = $this->m_kendaraan->get_spv_name($assignment_code);
			$team_name = $this->m_kendaraan->get_team_name($assignment_code);
			$shift_name = $this->m_kendaraan->get_shift_name($assignment_code);
			$detail = $this->m_kendaraan->detail($assignment_code);

			$data = array(
				'hari' => $hari,
				'tanggal' => $tanggal,
				'spv_name' => $spv_name,
				'team_name' => $team_name,
				'shift_name' => $shift_name,

				'data' => $detail,

				// 'tarif_dewasa' => $tarif_dewasa,
				// 'tarif_anak' => $tarif_anak,

				// 'loket_tunai_dewasa' => $loket_tunai_dewasa,
				// 'loket_tunai_anak' => $loket_tunai_anak,

				// 'loket_non_tunai_dewasa' => $loket_non_tunai_dewasa,
				// 'loket_non_tunai_anak' => $loket_non_tunai_anak,

				// 'loket_online_dewasa' => $loket_online_dewasa,
				// 'loket_online_anak' => $loket_online_anak,

				// 'saldo_akhir_loket_dewasa' => $saldo_akhir_loket_dewasa,
				// 'saldo_akhir_loket_anak' => $saldo_akhir_loket_anak,

				// 'tunai_dewasa' => $tunai_dewasa,
				// 'tunai_anak' => $tunai_anak,

				// 'online_dewasa' => $online_dewasa,
				// 'online_anak' => $online_anak,

				// 'total_dewasa' => $total_dewasa,
				// 'total_anak' => $total_anak,

				// 'total_terbilang' => $total_terbilang,
				// 'angka_terbilang' => $angka_terbilang,
			);

			// echo json_encode($data);

			$this->load->view('berita_acara_kendaraan/pdf', $data);
			// echo $shift_name;
			
		}else{
			show_404();
		}
	}

	function download_pdf()
	{
		$this->load->view('laporan/berita_acara_kendaraan/pdf');
	}
}