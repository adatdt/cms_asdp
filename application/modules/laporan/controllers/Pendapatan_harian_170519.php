<?php

error_reporting(E_ALL ^ E_WARNING);

class Pendapatan_harian extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('Html2pdf');
		$this->load->model('Pendapatan_harian_model','m_harian');
		$this->_module   = 'laporan/pendapatan_harian';
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$port = $this->enc->decode($this->input->post('port'));
			$tanggal = $this->input->post('tanggal');
			$regu = $this->enc->decode($this->input->post('regu'));
			$petugas = $this->enc->decode($this->input->post('petugas'));
			$shift = $this->enc->decode($this->input->post('shift'));
			$ship_class = $this->enc->decode($this->input->post('ship_class'));

			if ($port == "") {
				$data =  array(
					'code' => 101,
					'message' => "Silahkan pilih pelabuhan!",
				);

				echo json_encode($data);
				exit;
			}

			if ($tanggal == "") {
				$data =  array(
					'code' => 101,
					'message' => "Silahkan pilih tanggal!",
				);

				echo json_encode($data);
				exit;
			}

			if ($ship_class == "") {
				$data =  array(
					'code' => 101,
					'message' => "Silahkan pilih kelas layanan!",
				);

				echo json_encode($data);
				exit;
			}

			if ($shift == "") {
				$data =  array(
					'code' => 101,
					'message' => "Silahkan pilih shift!",
				);

				echo json_encode($data);
				exit;
			}

			$lintasan = $this->m_harian->get_lintasan($port,$tanggal,$ship_class);
			$penumpang = $this->m_harian->list_data($port,$tanggal,$regu,$petugas,$shift,$ship_class,'penumpang');
			$kendaraan = $this->m_harian->list_data($port,$tanggal,$regu,$petugas,$shift,$ship_class,'kendaraan');

			if ($lintasan) {
				if (!$penumpang && !$kendaraan) {
					$data =  array(
						'code' => 101,
						'message' => "Tidak ada data!",
					);

					echo json_encode($data);
					exit;
				}else{
					$input = array(
						'code' => 200,
						'lintasan' => $lintasan,
						'penumpang' => $penumpang,
						'kendaraan' => $kendaraan,
					);

					echo json_encode($input);
					exit;
				}
			}else{
				$data =  array(
					'code' => 101,
					'message' => "Tidak ada data!",
				);

				echo json_encode($data);
				exit;
			}

		}

		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Laporan Harian Pendapatan Tiket Terpadu Per Shift',
			'content' => 'pendapatan_harian/index',
			'port' => $this->global_model->getport(),
			'regu' => $this->global_model->getregu(),
			'petugas' => $this->global_model->getpetugas(),
			'shift' => $this->global_model->getshift(),
			'class' => $this->global_model->getclass(),
			'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	function download_pdf()
	{
		$this->load->view('laporan/ticket_terpadu_terjual/pdf');
	}
}