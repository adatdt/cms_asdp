<?php
/*
    Document   : laporan
    Created on : 23 agustus, 2023
    Author     : dayung
    Description: Enhancement pasca angleb 2023
*/ 

error_reporting(0);

class Ticket_terpadu_terjual_vm extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('Html2pdf');
		$this->load->model('Ticket_terjual_vm_model','m_terjual');
		$this->_module   = 'laporan/Ticket_terpadu_terjual_vm';
		$this->report_name = "tiket_terjual";
		$this->report_code = $this->global_model->get_report_code($this->report_name);

		$this->dbView = $this->load->database("dbView", TRUE);
        $this->dbAction = $this->load->database("dbAction", TRUE);
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$payment_type 	= $this->input->post('payment_type');
			$port 	      	= $this->enc->decode($this->input->post('port'));
			$datefrom 		= date("Y-m-d", strtotime($this->input->post('datefrom')));
        	$dateto			= date("Y-m-d", strtotime($this->input->post('dateto')));
			$regu 			= $this->enc->decode($this->input->post('regu'));
			$petugas 		= $this->enc->decode($this->input->post('petugas'));
			$shift 			= $this->enc->decode($this->input->post('shift'));
			$vm 			= $this->enc->decode($this->input->post('vm'));
			$cek_sc   		= $this->m_terjual->getClassBySession();

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
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
				);

				echo json_encode($data);
				exit;
			}

			if ($dateto == "") {
				$data =  array(
					'code' => 101,
					'message' => "Silahkan pilih tanggal akhir!",
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
				);

				echo json_encode($data);
				exit;
			}

			$lintasan = $this->m_terjual->get_lintasan($port,$datefrom,$dateto,$ship_class);

			$lintasanku = "-";
			$vm = $this->m_terjual->list_data($port,$datefrom,$dateto,$regu,$petugas,$shift,$ship_class,'vm',$vm);

			if ($lintasan) {
				$data_lintasan = $lintasan->row();

				$lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;

				if ($lintasan-> num_rows() > 1) {
					$lintasanku = "Semua";
				}
			}

			if (!$vm) {
				$data =  array(
					'code' => 101,
					'message' => "Tidak ada data",
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
				);

				echo json_encode($data);
				exit;
			}else{
				$input = array(
					'code' => 200,
					'lintasan' => $lintasanku,
					'status_approve' => $keterangan_report,
					'vm' => $vm,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
				);

				echo json_encode($input);
				exit;
			}
		}

		$data = array(
			'home' 			 => 'Beranda',
			'url_home' 		 => site_url('home'),
			'title' 		 => 'Penjualan Tiket Terpadu Terjual VM',
			'content' 		 => 'ticket_terpadu_terjual_vm/index',
			'port' 			 => $this->m_terjual->getport(),
			'regu' 			 => $this->m_terjual->getregu(),
			'petugas' 		 => $this->m_terjual->getpetugas(),
			'vm' 			 => $this->m_terjual->getvm(),
			'shift' 		 => $this->m_terjual->getshift(),
			'class' 		 => $this->option_shift_class(),			
			'download_pdf' 	 => checkBtnAccess($this->_module,'download_pdf'),
			'download_excel' => checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	private function option_shift_class()
	{
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

	function get_regu($port_id="")
	{
		validate_ajax();
		$port_id = $this->enc->decode($port_id);

		if (!$port_id) {
			$option = '<option value="" selected>Semua</option>';
			echo $option;
		}else{
			$data = $this->m_terjual->get_team($port_id);
			$option = '<option value="" selected>Semua</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="'.$this->enc->encode($value->team_code).'">'.$value->team_name.'</option>';
			}
			echo $option;
		}
	}

	function get_vm($port_id="")
	{
		validate_ajax();
		$port_id = $this->enc->decode($port_id);

		if (!$port_id) {
			$data   = $this->m_terjual->getvm();
			$option = '<option value="" selected>Semua</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="'.$this->enc->encode($value->terminal_code).'">'.$value->terminal_name.'</option>';
			}
			echo $option;
		}else{
			$data   = $this->m_terjual->getvm($port_id);
			$option = '<option value="" selected>Semua</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="'.$this->enc->encode($value->terminal_code).'">'.$value->terminal_name.'</option>';
			}
			echo $option;
		}
	}

	function get_pdf()
	{
		$payment_type 	= $this->input->get("payment_type");
		$port 			= $this->enc->decode($this->input->get("port"));
		$datefrom 		= date("Y-m-d", strtotime($this->input->get('datefrom')));
        $dateto			= date("Y-m-d", strtotime($this->input->get('dateto')));
		$regu 			= $this->enc->decode($this->input->get("regu"));
		$petugas 		= $this->enc->decode($this->input->get("petugas"));
		$shift 			= $this->enc->decode($this->input->get("shift"));
		$cek_sc   		= $this->m_terjual->getClassBySession();

		if ($cek_sc == false) {
			$ship_class   = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class   = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}
		$vm = $this->enc->decode($this->input->get("vm"));
		$vmku = $this->input->get("vmku");

		$lintasan = $this->m_terjual->get_lintasan($port,$datefrom,$dateto,$ship_class);

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

			if ($lintasan-> num_rows() > 1) {
				$lintasanku = "Semua";
			}
		}

		$vm = $this->m_terjual->list_data($port,$datefrom,$dateto,$regu,$petugas,$shift,$ship_class,'vm',$vm);

		$data['cabang'] 		= $this->input->get("cabangku");
		$data['pelabuhan'] 		= $this->input->get("pelabuhanku");
		$data['ship_class']     = $ship_classku;		
		$data['shift'] 			= $this->input->get("shiftku");
		$data['regu'] 			= $this->input->get("reguku");
		$data['tanggal'] 		= format_date($datefrom) . " - " . format_date($dateto);
		$data['lintasan'] 		= $lintasanku;
		$data['status_approve'] = $keterangan_report;
		$data['vm'] 			= $vm;
		$data['vmkita'] 		= $vmku;

		$this->load->view('laporan/ticket_terpadu_terjual_vm/pdf',$data);
	}

	function get_excel()
	{
		$excel_name 	= "Tiket_terpadu_terjual";
		$payment_type 	= $this->input->get("payment_type");
		$vm 			= $this->enc->decode($this->input->get("vm"));
		$port 			= $this->enc->decode($this->input->get("port"));
		$datefrom 		= date("Y-m-d", strtotime($this->input->get('datefrom')));
        $dateto			= date("Y-m-d", strtotime($this->input->get('dateto')));
		$regu 			= $this->enc->decode($this->input->get("regu"));
		$petugas 		= $this->enc->decode($this->input->get("petugas"));
		$cek_sc   		= $this->m_terjual->getClassBySession();

		if ($cek_sc == false) {
			$ship_class   = $this->enc->decode($this->input->get("ship_class"));
			$ship_classku = $this->input->get("ship_classku");
		} else {
			$ship_class   = $cek_sc['id'];
			$ship_classku = $cek_sc['name'];
		}
		$shift = $this->enc->decode($this->input->get("shift"));
		$petugasku = $this->input->get("petugasku");
		$pelabuhan = $this->input->get("pelabuhanku");

		$lintasan = $this->m_terjual->get_lintasan($port,$datefrom,$dateto,$ship_class);

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

			if ($lintasan-> num_rows() > 1) {
				$lintasanku = "Semua";
			}
		}

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Tiket_terjual_vm_" . $pelabuhan . "_". trim($datefrom,"-") . "-" . trim($datefrom,"-") . ".xlsx");

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
			"NAMA PERANGKAT",
			"JENIS",
			"TARIF (Rp.)",
			"PRODUKSI (Lbr.)",
			"PENDAPATAN (Rp.)",
		);

		$vm 		= $this->m_terjual->list_data($port,$datefrom,$dateto,$regu,$petugas,$shift,$ship_class,'vm',$vm);
		$cabang 	= $this->input->get("cabangku");
		$pelabuhan 	= $this->input->get("pelabuhanku");
		$ship_class = $ship_classku;
		$shift 		= $this->input->get("shiftku");
		$team_name 	= $this->input->get("reguku");
		$vmkita 	= $this->input->get("vmku");

		$produksi_vm 	= 0;
		$pendapatan_vm 	= 0;

		foreach ($vm as $key => $value) {
			$produksi_vm += $value->produksi;
			$pendapatan_vm += $value->pendapatan;

			$cashlessis[] = array(
				$key+1,
				$value->terminal_name,
				$value->golongan,
				$value->harga,
				$value->produksi,
				$value->pendapatan,
			);
		}

		$writer->writeSheetRow($sheet1, array("LAPORAN PRODUKSI DAN PENDAPATAN TIKET TERPADU TERJUAL PER-SHIFT"),$styles_ha);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("CABANG",$cabang,"","REGU",$team_name));
		$writer->writeSheetRow($sheet1, array("PELABUHAN",$pelabuhan,"","VENDING MACHINE",$vmkita));
		$writer->writeSheetRow($sheet1, array("LINTASAN",$lintasanku,"","TANGGAL",format_date($datefrom) . " - " . format_date($dateto)));
		$writer->writeSheetRow($sheet1, array("SHIFT",$shift,"","STATUS",$keterangan_report));
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("TIPE PENJUALAN : VENDING MACHINE"));
		$writer->writeSheetRow($sheet1, $judul_tabel, $styles1);

		if ($vm) {
			foreach($cashlessis as $row){
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("Sub Total","","","",$produksi_vm,$pendapatan_vm),$style_sub);

		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (VM)","","","",$produksi_vm,$pendapatan_vm),$style_sub);
		$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=5);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$writer->writeToStdOut();
	}
}