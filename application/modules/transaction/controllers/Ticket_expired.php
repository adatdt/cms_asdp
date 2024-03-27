<?php

error_reporting(0);

class Ticket_expired extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('Html2pdf');
		$this->load->model('m_ticket_expired');
		$this->_module   = 'laporan/ticket_expired';

		// $this->dbView=$this->load->database("dbView",TRUE);
		$this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);
	}

	public function index(){
		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Ticket Expired',
			'content' => 'ticket_expired/index',
			'port'  => $this->m_ticket_expired->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
            'channel'=>$this->m_ticket_expired->get_channel(),
            'payment_type'=>$this->m_ticket_expired->get_payment_type(),
			'download_excel' => checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	public function penumpang(){
		if($this->input->is_ajax_request()){
			$rows = $this->m_ticket_expired->listPenumpang();
			echo json_encode($rows);
			exit;
		}
	}

	public function kendaraan(){
		if($this->input->is_ajax_request()){
			$rows = $this->m_ticket_expired->listKendaraan();
			echo json_encode($rows);
			exit;
		}
	}

	function excel_penumpang(){
		ini_set('memory_limit','1024M');
		$excel_name = "Ticket Expired - Pejalan Kaki";
		$port = $this->enc->decode($this->input->get("port"));
		$payment_type = $this->enc->decode($this->input->get("payment_type"));
		$channel = $this->enc->decode($this->input->get("channel"));
		$cari = $this->input->get("cari");
		$dateFrom = $this->input->get("dateFrom");
		$dateTo = $this->input->get("dateTo");
		$searchName = $this->input->get("searchName");
		$pelabuhan = '';
		if($port){
			$pelabuhan .= '_'.$this->input->get("pelabuhan");
		}

		$penumpang = $this->m_ticket_expired->list_data($port,$payment_type,$channel,$cari,$dateFrom,$dateTo,'penumpang',$searchName);

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Ticket expired - Pejalan Kaki" . $pelabuhan . "_". $dateFrom . "_" . $dateTo .".xlsx");
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

		$header = array(
				"NO."=>"integer",
                "NOMOR TIKET"=>"string",
                "NAMA"=>"string",
                "GOLONGAN"=>"string",
                "SERVIS"=>"string",
                "PELABUHAN"=>"string",
                "TIPE PEMBAYARAN"=>"string",
                "PEMBAYARAN"=>"string",
                "CETAK BOARDING PASS"=>"string",
                "GATE IN"=>"string",
                "CETAK BOARDING PASS EXPIRED"=>"string",
                "GATE IN EXPIRED"=>"string",
                "BOARDING EXPIRED"=>"string"
		);

		foreach ($penumpang as $key => $value) {
			$value->pembayaran ? $value->pembayaran = format_dateTime($value->pembayaran) : $value->pembayaran = '-';
			$value->cetak_boarding ? $value->cetak_boarding = format_dateTime($value->cetak_boarding) : $value->cetak_boarding = '-';
            $value->gate_in ? $value->gate_in = format_dateTime($value->gate_in) : $value->gate_in = '-';
			$value->checkin_expired ? $value->checkin_expired = format_dateTime($value->checkin_expired) : $value->checkin_expired = '-';
			$value->gatein_expired ? $value->gatein_expired = format_dateTime($value->gatein_expired) : $value->gatein_expired = '-';
			$value->boarding_expired ? $value->boarding_expired = format_dateTime($value->boarding_expired) : $value->boarding_expired = '-';
			$penumpangs[] = array(
				$key+1,
				$value->ticket_number,
				$value->nama,
				$value->golongan,
				$value->servis,
				$value->pelabuhan,
				$value->payment_type,
				$value->channel,
				$value->pembayaran,
				$value->cetak_boarding,
				$value->gate_in,
				$value->checkin_expired,
				$value->gatein_expired,
				$value->boarding_expired,
			);
		}
		$writer->writeSheetHeader($sheet1, $header, $styles1 );
		foreach($penumpangs as $row){
			$writer->writeSheetRow($sheet1, $row, $styles2);
		}
		$writer->writeToStdOut();
	}

	function excel_kendaraan(){
		ini_set('memory_limit','1024M');
		$excel_name = "Ticket expired - Kendaraan";
		$port = $this->enc->decode($this->input->get("port"));
		$payment_type = $this->enc->decode($this->input->get("payment_type"));
		$channel = $this->enc->decode($this->input->get("channel"));
		$cari = $this->input->get("cari");
		$dateFrom = $this->input->get("dateFrom");
		$dateTo = $this->input->get("dateTo");
		$searchName = $this->input->get("searchName");
		$pelabuhan = '';
		if($port){
			$pelabuhan .= '_'.$this->input->get("pelabuhan");
		}

		$kendaraan = $this->m_ticket_expired->list_data($port,$payment_type,$channel,$cari,$dateFrom,$dateTo,'kendaraan',$searchName);

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Ticket expired - Kendaraan" . $pelabuhan . "_". $dateFrom . "_" . $dateTo .".xlsx");
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

		$header = array(
                "NO."=>"integer",
                "NOMOR TIKET"=>"string",
                "PLAT"=>"string",
                "GOLONGAN"=>"string",
                "SERVIS"=>"string",
                "PELABUHAN"=>"string",
                "TIPE PEMBAYARAN"=>"string",
                "PEMBAYARAN"=>"string",
                "CETAK BOARDING PASS"=>"string",
                "GATE IN"=>"string",
                "CETAK BOARDING PASS EXPIRED"=>"string",
                "GATE IN EXPIRED"=>"string",
                "BOARDING EXPIRED"=>"string"
		);

		foreach ($kendaraan as $key => $value) {
			$value->pembayaran ? $value->pembayaran = format_dateTime($value->pembayaran) : $value->pembayaran = '-';
			$value->cetak_boarding ? $value->cetak_boarding = format_dateTime($value->cetak_boarding) : $value->cetak_boarding = '-';
            $value->gate_in ? $value->gate_in = format_dateTime($value->gate_in) : $value->gate_in = '-';
			$value->checkin_expired ? $value->checkin_expired = format_dateTime($value->checkin_expired) : $value->checkin_expired = '-';
			$value->gatein_expired ? $value->gatein_expired = format_dateTime($value->gatein_expired) : $value->gatein_expired = '-';
			$value->boarding_expired ? $value->boarding_expired = format_dateTime($value->boarding_expired) : $value->boarding_expired = '-';
			$kendaraans[] = array(
				$key+1,
				$value->ticket_number,
				$value->plat,
				$value->golongan,
				$value->servis,
				$value->pelabuhan,
				$value->payment_type,
				$value->channel,
				$value->pembayaran,
				$value->cetak_boarding,
				$value->gate_in,
				$value->checkin_expired,
				$value->gatein_expired,
				$value->boarding_expired,
			);
		}

		$writer->writeSheetHeader($sheet1, $header, $styles1 );
		foreach($kendaraans as $row){
			$writer->writeSheetRow($sheet1, $row, $styles2);
		}

		$writer->writeToStdOut();
	}

}