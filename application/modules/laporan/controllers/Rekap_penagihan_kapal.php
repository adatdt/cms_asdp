<?php

error_reporting(0);
class Rekap_penagihan_kapal extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('rekap_penagihan_kapal_model', 'penagihan_kapal');
		$this->load->model('global_model');
		$this->_module   = 'laporan/rekap_penagihan_kapal';
		$this->load->library('Html2pdf');
		$this->report_name = "tiket_terjual";
        $this->report_code = $this->global_model->get_report_code($this->report_name);

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->penagihan_kapal->dataList();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' 			=> 'Beranda',
			'url_home' 		=> site_url('home'),
			'title' 		=> 'Rekap Penagihan Kapal',
			'content' 		=> 'rekap_penagihan_kapal/index2',
			'port'			=> $this->global_model->select_data("app.t_mtr_port","where status in (1) order by name asc")->result(),
			'branch'		=> $this->global_model->select_data("app.t_mtr_branch","where status in (1) order by branch_name asc")->result(),
			'shift'         => $this->global_model->select_data("app.t_mtr_shift","where status in (1) order by shift_name asc")->result(),
			// 'download_pdf' 	=> checkBtnAccess($this->_module,'download_pdf'),
			// 'download_excel'=> checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	public function old_detail(){
		validate_ajax();
		$this->global_model->checkAccessMenuAction($this->_module,'detail');

		$date = trim($this->input->get('date'));
		$port = $this->input->get('port');
		$shift = $this->input->get('shift');
		$portName = $this->input->get('port_name');
		$shiftTime = $this->input->get('shift_time');
		$shiftName = $this->input->get('shift_name');
		$spv = $this->input->get('spv');

		$data['data']   = $this->penagihan_kapal->detail_pdf($date, $port, $shift);
		$data['title']  = "REKAPITULASI PENAGIHAN PER - KAPAL PER - SHIFT";
		$data['date']   = $date;
		$data['port_name']  = $portName;
		$data['shift_time'] = $shiftTime;
		$data['shift_name'] = $shiftName;
		$data['spv'] = $spv;

		$this->load->view($this->_module.'/detail_modal',$data); 
	}

	public function detail(){
		validate_ajax();

		$datefrom = trim($this->input->get('datefrom'));
		$dateto = trim($this->input->get('dateto'));
		$port = $this->enc->decode($this->input->get('port'));
		$shift = $this->enc->decode($this->input->get('shift'));

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

		$keterangan_report = "DRAFT";

		$status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift);

		if ($status_approve) {
			$keterangan_report = "APPROVED";
		}

		$all_data = $this->penagihan_kapal->detail_pdf_new($datefrom, $dateto, $port, $shift);
		$shift_time = $this->penagihan_kapal->get_shift_time($shift,$port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$data['title']  = "REKAPITULASI PENAGIHAN PER - KAPAL PER - SHIFT";
		$data['date']   = $datefrom . " - " . $dateto;
		$data['shift_time']   = $jam_shift;

		$hasil = array(
			'code' => 200,
			'status_approve' => $keterangan_report,
			'header' => $data,
			'get' => $this->input->get(),
			'data' => $all_data,
		);

		echo json_encode($hasil);
	}

	function get_pdf(){
		// $this->global_model->checkAccessMenuAction($this->_module,'download_pdf');

		$datefrom = trim($this->input->get('datefrom'));
		$dateto = trim($this->input->get('dateto'));
		$port = $this->enc->decode($this->input->get('port'));
		$shift = $this->enc->decode($this->input->get('shift'));

		$port_name = "Semua";
		$shift_name = "Semua";

		if ($port) {
			$port_name = $this->penagihan_kapal->get_port($port);
		}

		if ($shift) {
			$shift_name = $this->penagihan_kapal->get_shift($shift);
		}

		$keterangan_report = "DRAFT";

		$status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift);

		if ($status_approve) {
			$keterangan_report = "APPROVED";
		}

		$all_data = $this->penagihan_kapal->detail_pdf_new($datefrom, $dateto, $port, $shift);
		$shift_time = $this->penagihan_kapal->get_shift_time($shift,$port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$data['title']  = "REKAPITULASI PENAGIHAN PER - KAPAL PER - SHIFT";
		$data['data'] = $all_data;
		$data['status_approve'] = $keterangan_report;
		$data['date']   = format_date($datefrom) . " - " . format_date($dateto);
		$data['shift_time']   = $jam_shift;
		$data['port_name'] = $port_name;
		$data['shift_name'] = $shift_name;
		// $data['tanggal_cetak'] = date("d M Y");
		$data['tanggal_cetak'] = format_dateTime(date("Y-m-d H:i:s"));

		$this->load->view($this->_module.'/pdf2', $data);
	}

	function get_excel(){
		// $this->global_model->checkAccessMenuAction($this->_module,'download_excel');

		$datefrom = trim($this->input->get('datefrom'));
		$dateto = trim($this->input->get('dateto'));
		$port = $this->enc->decode($this->input->get('port'));
		$shift = $this->enc->decode($this->input->get('shift'));

		$portName = "Semua";
		$shift_name = "Semua";

		if ($port) {
			$portName = $this->penagihan_kapal->get_port($port);
		}

		if ($shift) {
			$shift_name = $this->penagihan_kapal->get_shift($shift);
		}

		$keterangan_report = "DRAFT";

		$status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift);

		if ($status_approve) {
			$keterangan_report = "APPROVED";
		}

		$all_data = $this->penagihan_kapal->detail_pdf_new($datefrom, $dateto, $port, $shift);
		$shiftTime = $this->penagihan_kapal->get_shift_time($shift,$port);

		$jam_shift = "-";

		if ($shiftTime) {
			$jam_shift = format_time($shiftTime->shift_login) . " - " . format_time($shiftTime->shift_logout);
		}

		$title  = "REKAPITULASI PENAGIHAN PER - KAPAL PER - SHIFT";
		$tanggalku  = format_date($datefrom) . " - " . format_date($dateto);
		$shiftTime  = $jam_shift;
		$shiftName = $shift_name;

		$excel_name = "Rekap_penagihan_kapal";

		$data_detail = $this->penagihan_kapal->detail_pdf_new($datefrom, $dateto, $port, $shift);

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Rekap_penagihan_kapal_" . $tanggalku . "_". $portName . "_" . $shiftName . ".xlsx");

		$writer->setTitle($excel_name);
		$writer->setSubject($excel_name);
		$writer->setAuthor($excel_name);
		$writer->setCompany('ASDP Indonesia Ferry');
		$writer->setDescription($filename);
		$writer->setTempDir(sys_get_temp_dir());

		$sheet1 = $filename;

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

		$judul_atas = array(
			"NO.",
			"NAMA PERUSAHAAN",
			"NAMA KAPAL",
			"JLH TRIP",
			"Penumpang",
			"Kendaraan",
			"JUMLAH",
		);

		$totalTrip = 0;
        $totalPenumpang = 0;
        $totalVehicle = 0;
        $total = 0;

		foreach ($data_detail as $key => $value) {
			$totalTrip += $value->qty;
           	$totalPenumpang += $value->penumpang;
            $totalVehicle += $value->vehicle;
            $total += ($value->penumpang + $value->vehicle);

			$ddis[] = array(
				$key+1,
				$value->company,
				$value->ship_name,
				$value->qty,
				$value->penumpang,
				$value->vehicle,
				$value->penumpang + $value->vehicle,
			);
		}

		$writer->writeSheetRow($sheet1, array($title),$style_header);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("PELABUHAN",$portName,"","TANGGAL",$tanggalku));
		$writer->writeSheetRow($sheet1, array("SHIFT",$shiftName,"","JAM",$shiftTime));
		$writer->writeSheetRow($sheet1, array("STATUS",$keterangan_report));
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, $judul_atas, $styles1);

		if ($data_detail) {
			foreach($ddis as $row){
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("JUMLAH","","",$totalTrip,$totalPenumpang,$totalVehicle,$total),$style_sub);
		$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=6);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$writer->writeToStdOut();
	}

	function download_pdf(){
		$date           = trim($this->input->get('date'));
		$port           = $this->input->get('port');
		$shift          = $this->input->get('shift');
		$portName       = $this->input->get('port_name');
		$shiftTime      = $this->input->get('shift_time');
		$shiftName      = $this->input->get('shift_name');
		$spv            = $this->input->get('spv');

		$data['data']   = $this->penagihan_kapal->detail_pdf($date, $port, $shift);
		$data['title']  = "REKAPITULASI PENAGIHAN PER - KAPAL PER - SHIFT";
		$data['date']   = $date;
		$data['port_name']  = $portName;
		$data['shift_time'] = $shiftTime;
		$data['shift_name'] = $shiftName;
		$data['spv'] = $spv;

		$this->load->view($this->_module.'/pdf', $data);
	}
	
	function download_excel(){
		$excel_name = "Rekap_penagihan_kapal";

		$date       = trim($this->input->get('date'));
		$port       = $this->input->get('port');
		$shift      = $this->input->get('shift');
		$portName   = $this->input->get('port_name');
		$shiftTime  = $this->input->get('shift_time');
		$shiftName  = $this->input->get('shift_name');
		$spv        = $this->input->get('spv');

		$data_detail = $this->penagihan_kapal->detail_pdf($date, $port, $shift);

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Rekap_penagihan_kapal_" . $date . "_". $portName . "_" . $shiftName . ".xlsx");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer->setTitle($excel_name);
		$writer->setSubject($excel_name);
		$writer->setAuthor($excel_name);
		$writer->setCompany('ASDP Indonesia Ferry');
		$writer->setDescription($filename);
		$writer->setTempDir(sys_get_temp_dir());

		$sheet1 = $filename;

		$styles1 = array(
			'font'=>'Arial',
			'font-size'=>10,
			'font-style'=>'bold',
			'halign'=>'center',
			'valign'=>'center',
			'border'=>'left,right,top,bottom',
		);

		$styles2= array(
			'font'=>'Arial',
			'font-size'=>10,
			'valign'=>'center',
			'border'=>'left,right,top,bottom',
		);

		$style_header= array(
			'font'=>'Arial',
			'font-size'=>11,
			'font-style'=>'bold',
			'valign'=>'center',
			'border'=>'left,right,top,bottom',
		);

		$style_sub= array(
			'font'=>'Arial',
			'font-size'=>10,
			'font-style'=>'bold',
			'halign'=>'right',
			'valign'=>'right',
			'border'=>'left,right,top,bottom',
		);

		$header = array("string","string","string","string","string");

		$judul_atas = array(
			array(
				"NO.",
				"NAMA PERUSAHAAN / NAMA KAPAL",
				"JLH TRIP",
				"Penumpang",
				"Kendaraan",
				"KSO PT. ASDP",
				"JUMLAH",
			),
		);

		$totalTrip = 0;
        $totalPenumpang = 0;
        $totalVehicle = 0;
        $total = 0;

		foreach ($data_detail as $key => $value) {
			$totalTrip += $value->qty;
           	$totalPenumpang += $value->penumpang;
            $totalVehicle += $value->vehicle;
            $total += ($value->penumpang + $value->vehicle);

			$ddis[] = array(
				$key+1,
				$value->ship_name,
				$value->qty,
				$value->penumpang,
				$value->vehicle,
				" - ",
				$value->penumpang + $value->vehicle,
			);
		}

		foreach($judul_atas as $title){
			$writer->writeSheetRow($sheet1, array("REKAPITULASI PENAGIHAN PER - KAPAL PER - SHIFT"),$style_header);
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, array("PELABUHAN",$portName,"","TANGGAL",format_date($date)));
			$writer->writeSheetRow($sheet1, array("SHIFT",$shiftName,"","JAM",$shiftTime));
			$writer->writeSheetRow($sheet1, array(""));

			$writer->writeSheetRow($sheet1, $title, $styles1);

		}

		if ($data_detail) {
			foreach($ddis as $row){
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, array("JUMLAH","",$totalTrip,$totalPenumpang,$totalVehicle,"-",$total),$style_sub);
		$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=6);

		$writer->writeToStdOut();
	}
}