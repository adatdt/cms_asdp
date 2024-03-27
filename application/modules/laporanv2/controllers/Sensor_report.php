<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

// error_reporting(0);

class Sensor_report extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->library('Html2pdf');
        $this->load->model('sensor_report_model', 'model');
        $this->_module   = 'laporanv2/sensor_report';
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('PHPExcel');


        $this->_table    = 'app.t_trx_invoice';
        $this->_username = $this->session->userdata('username');
        // $this->_module   = 'laporan/menu_rekonsiliasi';

        // $this->dbView = $this->load->database("dbView", TRUE);
        $this->dbView = checkReplication();
        $this->dbAction = $this->load->database("dbAction", TRUE);        
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->model->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->model->get_identity_app();

        if($get_identity==0)
        {
            // mengambil port berdasarkan port di user menggunakan session
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->model->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')." ")->result();
                $row_port=1;
            }
            else
            {
                $port=$this->model->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result();
                $row_port=0;
            }
        }
        else
        {
            $port=$this->model->select_data("app.t_mtr_port","where id=".$get_identity." ")->result();
            $row_port=1;
        }

        // $data = array(
        //     'home'     => 'Home',
        //     'url_home' => site_url('home'),
        //     'title'    => 'Rekonsiliasi B2B',
        //     'content'  => 'menu_rekonsiliasi/index',
        //     'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
        //     'service'  => $this->rekon->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
        //     'merchant'  => $this->rekon->select_data("app.t_mtr_merchant","where status=1 order by merchant_name asc")->result(),
        //     'import_excel' => generate_button_new($this->_module, 'import_excel',  site_url($this->_module.'/import_excel')),
        //     'status_type' => $this->rekon->select_data("app.t_mtr_status","where tbl_name = 't_trx_payment_b2b' AND status not in (-5) order by id ASC")->result(),
        //     'team' => $this->rekon->select_data("core.t_mtr_team","where status = 1")->result(),
        //     'btn_excel' => checkBtnAccess($this->_module,'download_excel'),
        //     // 'import'=>checkBtnAccess($this->_module,'import_excel'),
				// );
				
				$approve= generate_button($this->_module, 'change_status', '<button type="button"id="btn-approve" class="btn btn-warning mt-ladda-btn ladda-button btn-sm download" data-style="zoom-in">
                            <span class="ladda-label">APPROVED</span>
                            <span class="ladda-spinner"></span>
												</button> ');

				$data = array(
					'home' => 'Beranda',
					'url_home' => site_url('home'),
					'title' => 'Laporan Pembacaan Sensor',
					'content' => 'sensor_report/index',
					'port' => $this->model->getport(),
					'regu' => $this->model->getregu(),
					'petugas' => $this->model->getpetugas(),
					'shift' => $this->model->getshift(),
					'class' => $this->model->getclass(),
					'approve' => $approve,
					'download_pdf' => checkBtnAccess($this->_module, 'download_pdf'),
					'download_excel' => checkBtnAccess($this->_module, 'download_excel'),
			);

		$this->load->view('default', $data);
	}


	function get_regu($port_id = "")
	{
		validate_ajax();
		$port_id = $this->enc->decode($port_id);

		if (!$port_id) {
			$option = '<option value="" selected>Semua</option>';
			echo $option;
		} else {
			$data = $this->model->get_team($port_id);
			$option = '<option value="" selected>Semua</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="' . $this->enc->encode($value->team_code) . '">' . $value->team_name . '</option>';
			}
			echo $option;
		}
	}

	function get_loket($port_id = "")
	{
		validate_ajax();
		$port_id = $this->enc->decode($port_id);

		if (!$port_id) {
			$option = '<option value="" selected>Semua</option>';
			echo $option;
		} else {
			$data = $this->model->get_loket($port_id);
			$option = '<option value="" selected>Semua</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="' . $this->enc->encode($value->terminal_code) . '">' . $value->terminal_name . '</option>';
			}
			echo $option;
		}
	}

    function get_total()
    {
        $rows = $this->rekon->total();
        echo json_encode($rows);
    }
    function get_dock()
    {
        $port=$this->enc->decode($this->input->post('port'));

        empty($port)?$port_id='NULL':$port_id=$port;
        $dock=$this->dock->select_data($this->_table,"where port_id=".$port_id." and status=1")->result();

        $data=array();
        foreach($dock as $key=>$value)
        {
            $value->id=$this->enc->encode($value->id);
            $data[]=$value;            
        }

         echo json_encode($data);
    }

    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
				
				// $port ="Merak";
				// $lintasan = "Merak - Bakauheni";
				// $petugas = "petugas 1";
				// $regu = "regu 1";
				// $shift = "malam";
				// $status = "boarding";
				// $loket = "loket 1";
				// $keter = "Overpaid";

        $dateFrom = $this->input->get('dateFrom');
				$dateTo = $this->input->get('dateTo');
				$port = $this->enc->decode($this->input->get('port'));
				$shipclass = $this->enc->decode($this->input->get('shipclass'));
				$status = $this->enc->decode($this->input->get('status'));
				$shift = $this->enc->decode($this->input->get('shift'));
				$regu = $this->enc->decode($this->input->get('regu'));
				$petugas = $this->enc->decode($this->input->get('petugas'));
				$loket = $this->enc->decode($this->input->get('loket'));
				$keter = $this->enc->decode($this->input->get('keter'));

				if ($keter) {
					if ($keter == 1) {
						$keter_name = 'Over Paid';
					}
					else {
						$keter_name = 'Under Paid';
					}
				}
				else {
					$keter_name = "Semua";
				}

				// die($port);
				if (($port)) {
					$port_name = $this->dbView->query("SELECT name FROM app.t_mtr_port WHERE id = {$port} and status=1")->row()->name;
				}
				if (($shipclass)) {
					$shipclass_name = $this->dbView->query("SELECT name FROM app.t_mtr_ship_class WHERE id = {$shipclass} and status=1")->row()->name;
				}
				if (($status)) {
					$status_name = $this->dbView->query("SELECT description FROM app.t_mtr_status WHERE status = {$status} and tbl_name='t_trx_booking_vehicle'")->row()->description;
				}
				if (($shift)) {
					$shift_name = $this->dbView->query("SELECT shift_name FROM app.t_mtr_shift WHERE id = {$shift} and status=1")->row()->shift_name;
				}
				if (($regu)) {
					$regu_name = $this->dbView->query("SELECT team_name FROM core.t_mtr_team WHERE team_code = '{$regu}' and status=1")->row()->team_name;
				}
				if (($petugas)) {
					$petugas_name = $this->dbView->query("SELECT username FROM core.t_mtr_user WHERE id = '{$petugas}' and status=1")->row()->username;
				}
				if (($loket)) {
					$loket_name = $this->dbView->query("SELECT terminal_name FROM app.t_mtr_device_terminal WHERE terminal_code = '{$loket}' and status=1")->row()->terminal_name;
				}

				$lintasan = $this->model->get_lintasan($port, $dateFrom, $dateTo, $shipclass);

				if ($lintasan) {
					$data_lintasan = $lintasan->row();

					$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

					if ($lintasan->num_rows() > 1) {
						$lintasanku = "Semua";
					}
				}

				$data = $this->model->download();
				// print_r($data) ;
				// exit;

        $file_name = 'Laporan Pembacaan Sensor tanggal '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles_ha = array('font'=>'Arial','font-size'=>12,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center');
        $styles1 = array( 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20], 'height' => 30,
													'font' => 'Arial',
													'font-size' => 10,
													'font-style' => 'bold',
													'valign' => 'center',
													'halign' => 'center',
													'border' => 'bottom',
													'border-style' => 'thin'
												);
        $styles2 = array('font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');
        $style_sub = array(
			'font' => 'Arial',
			'font-size' => 10,
			'font-style' => 'bold',
			'halign' => 'left',
			'valign' => 'left',
			);

			$styleHeader = array(
					'height' => 30,
					'font' => 'Arial',
					'font-size' => 10,
					'font-style' => 'bold',
					'valign' => 'center',
					'halign' => 'center',
					'border' => 'left,right,top,bottom',
					'border-style' => 'thin'
			);

				$headertbl1 = array(
					"NO",
					"KODE BOOKING",
					"NOMOR TIKET",
					"LAYANAN",
					"NOMOR POLISI",
					"",
					"",
					"",
					"HASIL PENGUKURAN",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"IDENTITAS PETUGAS",
					"",
					"",
					"INFORMASI TIKET",
					"",
					"",
					"",
					"",
					"STATUS APPROVAL NAIK/TURUN GOLONGAN",
					"",
					"",
					""
				);

				$headertbl2 = array(
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"PANJANG",
					"",
					"",
					"",
					"LEBAR",
					"",
					"",
					"",
					"TINGGI",
					"",
					"",
					"",
					"BERAT",
					"",
					"",
					"GOLONGAN",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
				);


				$headertbl3 = array(
					"",
					"",
					"",
					"",
					"RESERVASI",
					"SENSOR",
					"MANUAL",
					"KOMPARASI MANUAL DAN SENSOR",
					"RESERVASI",
					"SENSOR",
					"MANUAL",
					"MANUAL - SENSOR",
					"RESERVASI",
					"SENSOR",
					"MANUAL",
					"MANUAL - SENSOR",
					"RESERVASI",
					"SENSOR",
					"MANUAL",
					"MANUAL - SENSOR",
					"BATASAN",
					"HASIL TIMBANG",
					"STATUS",
					"RESERVASI",
					"SENSOR",
					"MANUAL",
					"MANUAL - SENSOR",
					"USER PETUGAS LOKET",
					"NAMA LOKET",
					"USER SUPERVISI",
					"STATUS",
					"DERMAGA",
					"KAPAL",
					"WAKTU",
					"KETERANGAN",
					"STATUS",
					"USER",
					"TANGGAL",
					"AKSI",
				);

        $header = array("LAPORAN BERITA ACARA HASIL PENGUKURAN NOMOR POLISI, DIMENSI KENDARAAN, DAN BERAT KENDARAAN" => "string","" => "string","" => "string","" => "string","" => "string","" => "string","" => "string",
                        "" => "string","" => "string","" => "string","" => "string","" => "string","" => "string","" => "string",
                        "" => "string","" => "string","" => "string","" => "string","" => "string","" => "string","" => "string",
                        "" => "string","" => "string" );
        $no=1;

        foreach ($data as $key => $value) {
            $rows[] = array($no,
                            $value->booking_code,
                            $value->ticket_number,
                            $value->ship_class,
                            $value->nopol_bok,
                            $value->nopol_cek,
                            $value->nopol_man,
                            $value->nopol_comp,
                            $value->panjang_bok,
                            $value->panjang_cek,
                            $value->panjang_man,
                            $value->panjang,
                            $value->lebar_bok,
                            $value->lebar_cek,
                            $value->lebar_man,
                            $value->lebar,
                            $value->tinggi_bok,
                            $value->tinggi_cek,
                            $value->tinggi_man,
                            $value->tinggi,
                            $value->batasan,
														$value->hasil_timbang,
														$value->berat_status,
                            $value->gol_bok,
                            $value->gol_cek,
                            $value->gol_man,
                            $value->gol_comp,
                            $value->user_petugas_loket,
                            $value->nama_loket,
                            $value->nama_spv,
                            $value->status,
                            $value->dermaga,
                            $value->nama_kapal,
                            $value->waktu,
                            $value->keterangan,
                            $value->appr_status,
                            $value->appr_user,
                            $value->appr_tanggal,
                            $value->appr_aksi,
                        );
            $no++;
				}

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header,$styles1);
        // $writer->writeSheetRow('Sheet1', array("", "LAPORAN BERITA ACARA HASIL PENGUKURAN NOMOR POLISI, DIMENSI KENDARAAN, DAN BERAT KENDARAAN"), $styles_ha);
        $writer->writeSheetRow('Sheet1', array(""));
				$writer->writeSheetRow('Sheet1', array("", "Cabang:", isset($port_name)?$port_name:"Semua", "", "", "Tanggal", $dateFrom, "S/D", $dateTo), $style_sub);
				$writer->writeSheetRow('Sheet1', array("", "Pelabuhan:", isset($port_name)?$port_name:"Semua", "", "", "Petugas", isset($petugas_name)?$petugas_name:"Semua"), $style_sub);
				$writer->writeSheetRow('Sheet1', array("", "Lintasan:", $lintasanku, "", "", "Nama Loket", isset($loket_name)?$loket_name:"Semua"), $style_sub);
				$writer->writeSheetRow('Sheet1', array("", "Shift:", isset($shift_name)?$shift_name:"Semua", "", "", "Status", isset($status_name)?$status_name:"Semua"), $style_sub);
				$writer->writeSheetRow('Sheet1', array("", "Regu:", isset($regu_name)?$regu_name:"Semua", "", "", "Keterangan", $keter_name), $style_sub);
				$writer->writeSheetRow('Sheet1', array("", "Layanan:", isset($shipclass_name)?$shipclass_name:"Semua", "", ""), $style_sub);
        $writer->writeSheetRow('Sheet1', array(""));
        $writer->writeSheetRow('Sheet1', $headertbl1,$styleHeader);
        $writer->writeSheetRow('Sheet1', $headertbl2,$styleHeader);
				$writer->writeSheetRow('Sheet1', $headertbl3,$styleHeader);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 1, $end_col = 38);

				$writer->markMergedCell('Sheet1', $start_row = 9, $start_col = 0, $end_row = 11, $end_col = 0);
				$writer->markMergedCell('Sheet1', $start_row = 9, $start_col = 1, $end_row = 11, $end_col = 1);
				$writer->markMergedCell('Sheet1', $start_row = 9, $start_col = 2, $end_row = 11, $end_col = 2);
				$writer->markMergedCell('Sheet1', $start_row = 9, $start_col = 3, $end_row = 11, $end_col = 3);
				$writer->markMergedCell('Sheet1', $start_row = 9, $start_col = 4, $end_row = 10, $end_col = 7);
				$writer->markMergedCell('Sheet1', $start_row = 9, $start_col = 8, $end_row = 9, $end_col = 26);
				$writer->markMergedCell('Sheet1', $start_row = 9, $start_col = 27, $end_row = 10, $end_col = 29);
				$writer->markMergedCell('Sheet1', $start_row = 9, $start_col = 30, $end_row = 10, $end_col = 34);
				$writer->markMergedCell('Sheet1', $start_row = 9, $start_col = 35, $end_row = 10, $end_col = 38);


				$writer->markMergedCell('Sheet1', $start_row = 10, $start_col = 8, $end_row = 10, $end_col = 11);
				$writer->markMergedCell('Sheet1', $start_row = 10, $start_col = 12, $end_row = 10, $end_col = 15);
				$writer->markMergedCell('Sheet1', $start_row = 10, $start_col = 16, $end_row = 10, $end_col = 19);
				$writer->markMergedCell('Sheet1', $start_row = 10, $start_col = 20, $end_row = 10, $end_col = 22);
				$writer->markMergedCell('Sheet1', $start_row = 10, $start_col = 23, $end_row = 10, $end_col = 26);
        
        foreach($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
		}
		
		public function download_pdf()
    {
			error_reporting(0);
			$dateFrom = $this->input->get('dateFrom');
			$dateTo = $this->input->get('dateTo');
			$port = $this->enc->decode($this->input->get('port'));
			$shipclass = $this->enc->decode($this->input->get('shipclass'));
			$status = $this->enc->decode($this->input->get('status'));
			$shift = $this->enc->decode($this->input->get('shift'));
			$regu = $this->enc->decode($this->input->get('regu'));
			$petugas = $this->enc->decode($this->input->get('petugas'));
			$loket = $this->enc->decode($this->input->get('loket'));
			$keter = $this->enc->decode($this->input->get('keter'));

			if ($keter) {
				if ($keter == 1) {
					$data['keter'] = 'Over Paid';
				}
				else {
					$data['keter'] = 'Under Paid';
				}
			}

			$data['dateFrom'] = $dateFrom;
			$data['dateTo'] = $dateTo;
			if (($port)) {
				$data['port_name'] = $this->dbView->query("SELECT name FROM app.t_mtr_port WHERE id = {$port} and status=1")->row()->name;
			}
			if (($shipclass)) {
				$data['shipclass_name'] = $this->dbView->query("SELECT name FROM app.t_mtr_ship_class WHERE id = {$shipclass} and status=1")->row()->name;
			}
			if (($status)) {
				$data['status_name'] = $this->dbView->query("SELECT description FROM app.t_mtr_status WHERE status = {$status} and tbl_name='t_trx_booking_vehicle'")->row()->description;
			}
			if (($shift)) {
				$data['shift_name'] = $this->dbView->query("SELECT shift_name FROM app.t_mtr_shift WHERE id = {$shift} and status=1")->row()->shift_name;
			}
			if (($regu)) {
				$data['regu_name'] = $this->dbView->query("SELECT team_name FROM core.t_mtr_team WHERE team_code = '{$regu}' and status=1")->row()->team_name;
			}
			if (($petugas)) {
				$data['petugas_name'] = $this->dbView->query("SELECT username FROM core.t_mtr_user WHERE id = '{$petugas}' and status=1")->row()->username;
			}
			if (($loket)) {
				$data['loket_name'] = $this->dbView->query("SELECT terminal_name FROM app.t_mtr_device_terminal WHERE terminal_code = '{$loket}' and status=1")->row()->terminal_name;
			}
			

			$lintasan = $this->model->get_lintasan($port, $dateFrom, $dateTo, $shipclass);

			if ($lintasan) {
				$data_lintasan = $lintasan->row();

				$data['lintasanku'] = $data_lintasan->origin . " - " . $data_lintasan->destination;

				if ($lintasan->num_rows() > 1) {
					$data['lintasanku'] = "Semua";
				}
			}

			$data['data'] = $this->model->download();

        $this->load->view($this->_module . '/pdf', $data);
        // $this->load->view('laporan/tiket_refund/pdf', $data);
		}
		


}