<?php

error_reporting(0);

class Muntah_kapal extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('Html2pdf');
		$this->load->model('muntah_kapal_model','m_muntah');
		$this->_module   = 'laporan/sab';

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);		
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$port 	  = $this->enc->decode($this->input->post('port'));
			$datefrom = $this->input->post('datefrom');
			$dateto   = $this->input->post('dateto');
			$shift    = $this->enc->decode($this->input->post('shift'));
			$regu 	  = $this->enc->decode($this->input->post('regu'));
			$cek_sc   = $this->m_muntah->getClassBySession();

			if ($cek_sc == false) {
				$ship_class = $this->enc->decode($this->input->post('ship_class'));
			} else {
				$ship_class = $cek_sc['id'];
			}

			if ($datefrom == "") {
				$data =  array(
					'code'    => 101,
					'message' => "Silahkan pilih tanggal mulai!",
				);

				echo json_encode($data);
				exit;
			}

			if ($dateto == "") {
				$data =  array(
					'code'    => 101,
					'message' => "Silahkan pilih tanggal akhir!",
				);

				echo json_encode($data);
				exit;
			}

			$lintasan 		    = $this->m_muntah->get_lintasan($port, $datefrom, $dateto, $ship_class, $shift);
			$ship_origin 	    = $this->m_muntah->get_ship_origin($port, $datefrom, $dateto, $ship_class, $shift);
			$ship_destination   = $this->m_muntah->get_ship_destination($port, $datefrom, $dateto, $ship_class, $shift);
			$penumpang 		    = $this->m_muntah->list_data($port, $datefrom, $dateto, $ship_class, $shift, $regu, "penumpang");
			$kendaraan   	    = $this->m_muntah->list_data($port, $datefrom, $dateto, $ship_class, $shift, $regu, "kendaraan");
			$lintasanku 		= "-";
			$ship_originku  	= "-";
			$ship_destinationku = "-";

			if ($lintasan) {
				$data_lintasan = $lintasan->row();
				$lintasanku    = $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

				if ($lintasan-> num_rows() > 1) {
					$lintasanku = "Semua";
				}
			}

			if ($ship_origin) {
				$data_ship_origin = $ship_origin->row();
				$ship_originku    = $data_ship_origin->name;

				if ($ship_origin-> num_rows() > 1) {
					$ship_originku = "-";
				}
			}

			if ($ship_destination) {
				$data_ship_destination = $ship_destination->row();
				$ship_destinationku    = $data_ship_destination->name;

				if ($ship_destination-> num_rows() > 1) {
					$ship_destinationku = "-";
				}
			}

			if (!$penumpang && !$kendaraan) {
				$data =  array(
					'code'    => 101,
					'message' => "Tidak ada data",
				);

				echo json_encode($data);
				exit;
			}else{
				$input = array(
					'code' 			   => 200,
					'lintasan' 		   => $lintasanku,
					'ship_origin'      => $ship_originku,
					'ship_destination' => $ship_destinationku,
					'penumpang' 	   => $penumpang,
					'kendaraan' 	   => $kendaraan,
				);

				echo json_encode($input);
				exit;
			}
		}

		$data = array(
			'home' 			 => 'Beranda',
			'url_home' 		 => site_url('home'),
			'title' 		 => 'Laporan Muntah Kapal',
			'content' 		 => 'laporan/muntah_kapal/index',
			'port' 			 => $this->m_muntah->getport(),
			'class' 		 => $this->option_shift_class(),
			'shift' 		 => $this->global_model->select_data("app.t_mtr_shift","where status in (1)")->result(),
			'download_pdf' 	 => checkBtnAccess($this->_module,'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	private function option_shift_class()
	{
		$shift_class = $this->m_muntah->getClassBySession('option');
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

	function get_regu($port_id="")
	{
		validate_ajax();
		$port_id = $this->enc->decode($port_id);

		if (!$port_id) {
			$option = '<option value="" selected>Semua</option>';
			echo $option;
		}else{
			$data   = $this->m_muntah->get_team($port_id);
			$option = '<option value="" selected>Semua</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="'.$this->enc->encode($value->team_code).'">'.$value->team_name.'</option>';
			}
			echo $option;
		}
	}

	function get_pdf()
	{
		$port		= $this->enc->decode($this->input->get('port'));
		$datefrom 	= $this->input->get('datefrom');
		$dateto 	= $this->input->get('dateto');
		$shift 		= $this->enc->decode($this->input->get('shift'));
		$regu 		= $this->enc->decode($this->input->get('regu'));
		$cek_sc 	= $this->m_muntah->getClassBySession();

		if ($cek_sc == false) {
			$ship_class   = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class   = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}

		$lintasan 		  	= $this->m_muntah->get_lintasan($port, $datefrom, $dateto, $ship_class, $shift);
		$ship_origin   	  	= $this->m_muntah->get_ship_origin($port, $datefrom, $dateto, $ship_class, $shift);
		$ship_destination 	= $this->m_muntah->get_ship_destination($port, $datefrom, $dateto, $ship_class, $shift);
		$lintasanku 		= "-";
		$ship_originku 		= "-";
		$ship_destinationku = "-";

		if ($lintasan) {
			$data_lintasan = $lintasan->row();

			$lintasanku = $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

			if ($lintasan-> num_rows() > 1) {
				$lintasanku = "Semua";
			}
		}

		if ($ship_origin) {
			$data_ship_origin = $ship_origin->row();

			$ship_originku = $data_ship_origin->name;

			if ($ship_origin-> num_rows() > 1) {
				$ship_originku = "-";
			}
		}

		if ($ship_destination) {
			$data_ship_destination = $ship_destination->row();
			$ship_destinationku    = $data_ship_destination->name;

			if ($ship_destination-> num_rows() > 1) {
				$ship_destinationku = "-";
			}
		}

		$penumpang = $this->m_muntah->list_data($port, $datefrom, $dateto, $ship_class, $shift, $regu, "penumpang");
		$kendaraan = $this->m_muntah->list_data($port, $datefrom, $dateto, $ship_class, $shift, $regu, "kendaraan");

		$data['cabang'] 	      = $this->input->get("cabangku");
		$data['pelabuhan'] 	      = $this->input->get("pelabuhanku");
		$data['ship_class']       = $ship_classku;
		$data['shift'] 		      = $this->input->get("shiftku");
		$data['regu'] 		      = $this->input->get("reguku");
		$data['tanggal'] 	      = format_date($datefrom) . " - " . format_date($dateto);
		$data['lintasan'] 	      = $lintasanku;
		$data['ship_origin'] 	  = $ship_originku;
		$data['ship_destination'] = $ship_destinationku;
		$data['penumpang'] 		  = $penumpang;
		$data['kendaraan']   	  = $kendaraan;

		$this->load->view('laporan/muntah_kapal/pdf',$data);
	}

	function get_excel()
	{
		$excel_name = "Laporan_muntah_kapal";

		$port 		= $this->enc->decode($this->input->get('port'));
		$datefrom 	= $this->input->get('datefrom');
		$dateto 	= $this->input->get('dateto');
		$shift 		= $this->enc->decode($this->input->get('shift'));
		$regu 		= $this->enc->decode($this->input->get('regu'));
		$pelabuhan  = $this->input->get('pelabuhanku');
		$cek_sc 	= $this->m_muntah->getClassBySession();

		if ($cek_sc == false) {
			$ship_class   = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class   = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}

		$lintasan 		  	= $this->m_muntah->get_lintasan($port, $datefrom, $dateto, $ship_class, $shift);
		$ship_origin   	  	= $this->m_muntah->get_ship_origin($port, $datefrom, $dateto, $ship_class, $shift);
		$ship_destination 	= $this->m_muntah->get_ship_destination($port, $datefrom, $dateto, $ship_class, $shift);
		$lintasanku 	  	= "-";
		$ship_originku 	  	= "-";
		$ship_destinationku = "-";

		if ($lintasan) {
			$data_lintasan = $lintasan->row();
			$lintasanku    = $data_lintasan->port_origin . " - " . $data_lintasan->port_destination;

			if ($lintasan-> num_rows() > 1) {
				$lintasanku = "Semua";
			}
		}

		if ($ship_origin) {
			$data_ship_origin = $ship_origin->row();
			$ship_originku    = $data_ship_origin->name;

			if ($ship_origin-> num_rows() > 1) {
				$ship_originku = "-";
			}
		}

		if ($ship_destination) {
			$data_ship_destination = $ship_destination->row();
			$ship_destinationku    = $data_ship_destination->name;

			if ($ship_destination-> num_rows() > 1) {
				$ship_destinationku = "-";
			}
		}

		$this->load->library('XLSExcel');
		$writer   = new XLSXWriter();
		$filename = strtoupper("Laporan_muntah_kapal_" . $pelabuhan . "_". trim($datefrom,"-") . "-" . trim($datefrom,"-") . "_" . $ship_class . ".xlsx");

		$writer->setTitle($excel_name);
		$writer->setSubject($excel_name);
		$writer->setAuthor($excel_name);
		$writer->setCompany('ASDP Indonesia Ferry');
		$writer->setDescription($filename);
		$writer->setTempDir(sys_get_temp_dir());

		$sheet1 = $filename;

		$styles_ha = array(
			'font'=>'Arial',
			'font-size'=>12,
			'font-style'=>'bold',
			'halign'=>'center',
			'valign'=>'center',
		);

		$styles1 = array(
			'font'=>'Arial',
			'font-size'=>10,
			'font-style'=>'bold',
			'halign'=>'center',
			'valign'=>'center',
			'border'=>'left,right,top,bottom',
			'border-style'=> 'thin',
		);

		$styles2= array(
			'font'=>'Arial',
			'font-size'=>10,
			'valign'=>'center',
			'border'=>'left,right,top,bottom',
			'border-style'=> 'thin',
		);

		$style_header= array(
			'font'=>'Arial',
			'font-size'=>11,
			'font-style'=>'bold',
			'valign'=>'center',
			'border'=>'left,right,top,bottom',
			'border-style'=> 'thin',
		);

		$style_sub= array(
			'font'=>'Arial',
			'font-size'=>10,
			'font-style'=>'bold',
			'halign'=>'right',
			'valign'=>'right',
			'border'=>'left,right,top,bottom',
			'border-style'=> 'thin',
		);

		$header = array("string","string","string","string","string");

		$judul_tabel = array(
			"NO.",
			"JENIS TIKET",
			"PRODUKSI",
			"PENDAPATAN",
		);

		$penumpang = $this->m_muntah->list_data($port, $datefrom, $dateto, $ship_class, $shift, $regu, "penumpang");
		$kendaraan = $this->m_muntah->list_data($port, $datefrom, $dateto, $ship_class, $shift, $regu, "kendaraan");
		$cabang 	= $this->input->get("cabangku");
		$pelabuhan 	= $this->input->get("pelabuhanku");
		$ship_class = $ship_classku;
		$shift 		= $this->input->get("shiftku");
		$regu 		= $this->input->get("reguku");
		
		$produksi_penumpang = 0;

		foreach ($penumpang as $key => $value) {
			$produksi_penumpang += $value->produksi;
			$pendapatan_penumpang += $value->pendapatan;

			$penumpangs[] = array(
				$key+1,
				$value->golongan,
				$value->produksi,
				$value->pendapatan
			);
		}

		$produksi_kendaraan = 0;

		foreach ($kendaraan as $key => $value) {
			$produksi_kendaraan += $value->produksi;
			$pendapatan_kendaraan += $value->pendapatan;

			$kendaraans[] = array(
				$key+1,
				$value->golongan,
				$value->produksi,
				$value->pendapatan
			);
		}

		$writer->writeSheetRow($sheet1, array("LAPORAN REKAPITULASI MUNTAH KAPAL"),$styles1);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("CABANG",$cabang,"SHIFT",$shift));
		$writer->writeSheetRow($sheet1, array("PELABUHAN",$pelabuhan,"REGU",$regu));
		$writer->writeSheetRow($sheet1, array("LINTASAN",$lintasanku,"TANGGAL",format_date($datefrom) . " - " . format_date($dateto)));
		$writer->writeSheetRow($sheet1, array("KAPAL AWAL",$ship_originku,"KAPAL TUJUAN",$ship_destinationku));
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);
		$writer->writeSheetRow($sheet1, array("1. PENUMPANG"));

		if ($penumpang) {
			foreach($penumpangs as $row){
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total","",$produksi_penumpang,$pendapatan_penumpang),$style_sub);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("2. KENDARAAN"));

		if ($kendaraan) {
			foreach($kendaraans as $row){
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}
		$writer->writeSheetRow($sheet1, array("Sub Total","",$produksi_kendaraan,$pendapatan_kendaraan),$style_sub);

		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("Total)","",$produksi_penumpang+$produksi_kendaraan,$pendapatan_penumpang+$pendapatan_kendaraan),$style_sub);
		$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=3);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$writer->writeToStdOut();
	}
}