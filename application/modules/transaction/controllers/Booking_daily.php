<?php

error_reporting(0);

class Booking_daily extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('Html2pdf');
		$this->load->model('m_booking_daily');
		$this->_module   = 'transaction/booking_daily';
	}

	public function index(){

		$identity=$this->m_booking_daily->select_data("app.t_mtr_identity_app","")->row();

		$dataPort=array();

		if($identity->port_id==0)
		{
			if(empty($this->session->userdata('port_id')))
			{	
				$dataPort[""]="Pilih";
				$port=$this->m_booking_daily->select_data("app.t_mtr_port", " where status<>'-5' order by name asc")->result();	
			}
			else
			{
				$port=$this->m_booking_daily->select_data("app.t_mtr_port", " where id=".$this->session->userdata('port_id'))->result();
			}
		}
		else
		{
			$port=$this->m_booking_daily->select_data("app.t_mtr_port", " where id=".$identity->port_id)->result();
		}

		foreach ($port as $key => $value) {
			$dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
		}

		$shift = $this->m_booking_daily->select_data("app.t_mtr_shift"," where status<>'-5' order by shift_name asc ")->result();

		$dataShift[""]='Pilih';
		foreach ($shift as $key => $value) {
			$dataShift[$this->enc->encode($value->shift_name)]=strtoupper($value->shift_name);
		}


		$data = array(
			'home'			=> 'Beranda',
			'url_home' 		=> site_url('home'),
			'title' 		=> 'Booking Daily',
			'content' 		=> 'booking_daily/index',
			// 'port'  		=> $this->m_booking_daily->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
			'port'  		=> $dataPort,
			'shift'			=> $dataShift,
            'kelas'			=> $this->m_booking_daily->get_kelas(),
            'payment_type'	=> $this->m_booking_daily->get_payment_type(),
			'download_excel'=> checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	public function penumpang(){
		if($this->input->is_ajax_request()){
			$rows = $this->m_booking_daily->listPenumpang();
			echo json_encode($rows);
			exit;
		}
	}

	public function kendaraan(){
		if($this->input->is_ajax_request()){
			$rows = $this->m_booking_daily->listKendaraan();
			echo json_encode($rows);
			exit;
		}
	}

	function excel_penumpang(){

        // $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
		$port 		= $this->enc->decode($this->input->get("port"));
		$kelas 		= $this->enc->decode($this->input->get("kelas"));
		$shift 		= $this->enc->decode($this->input->get("shift"));
		$dateFrom 	= $this->input->get("dateFrom");
		$dateTo 	= $this->input->get("dateTo");
		$cari = $this->input->get("cari");
		$searchName = $this->input->get("searchName");

		$data = $this->m_booking_daily->list_data($port,$kelas,$dateFrom,$dateTo,'penumpang',$shift,$cari,$searchName);


        $file_name = 'Booking Daily '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

		$header = array(
				"NO."=>"string",
				"KODE BOKING"=>"string",
				"TANGGAL BOOKING"=>"string",
				"TANGGAL BERANGKAT"=>"string",
				"NAMA PEMESAN"=>"string",
				"NIK"=>"string",
				"JENIS IDENTITAS"=>"string",
				"JENIS KELAMIN"=>"string",
				"TIPE PENUMPANG"=>"string",
				"KELAS"=>"string",
				"PELABUHAN"=>"string",
				"SERVICE"=>"string",
				"HARGA"=>"string",
		);



        $no=1;
        foreach ($data as $key => $value) {

            $rows[] = array($no,
							$value->booking_code,
							$value->created_on,
							$value->depart_date.' '.$value->depart_time_start,
							$value->customer_name,
							$value->nik,
							$value->jenis_identitas,
							$value->gender,
							$value->tipe_penumpang,
							$value->kelas,
							$value->port_origin,
							$value->service_name,
							$value->fare,
                        );
            $no++;
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header,$styles1);
        // var_dump($rows);
        foreach($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
	}


	function excel_kendaraan(){
		ini_set('memory_limit','1024M');
		$excel_name = "Booking Daily - Kendaraan";
		$port 		= $this->enc->decode($this->input->get("port"));
		$kelas 		= $this->enc->decode($this->input->get("kelas"));
		$shift 		= $this->enc->decode($this->input->get("shift"));
		$dateFrom 	= $this->input->get("dateFrom");
		$dateTo 	= $this->input->get("dateTo");
		$cari = $this->input->get("cari");
		$searchName = $this->input->get("searchName");
		$pelabuhan 	= '';
		$kel 		= '';

		if($port){
			$pelabuhan .= '_'.$this->enc->decode($this->input->get("pelabuhan"));
		}
		if($kelas){
			$kel .= '_'.$this->enc->decode($this->input->get("kelas"));
		}

		$kendaraan = $this->m_booking_daily->list_data($port,$kelas,$dateFrom,$dateTo,'kendaraan',$shift,$cari,$searchName);


		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Booking Daily - Kendaraan ". $dateFrom . "_" . $dateTo .".xlsx");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer->setTitle($excel_name);
		$writer->setSubject($excel_name);
		$writer->setAuthor($excel_name);
		$writer->setCompany('ASDP Indonesia Ferry');
		$writer->setDescription($filename);
		$writer->setTempDir(sys_get_temp_dir());

		$sheet1 = "sheet1";

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
				"KODE BOKING"=>"string",
				"TANGGAL BOOKING"=>"string",
				"TANGGAL BERANGKAT"=>"string",
				"PLAT NOMOR"=>"string",
				"GOLONGAN"=>"string",
				"TARIF"=>"integer",
				"KELAS"=>"string",
				"DRIVER"=>"string",
				"BERAT KENDARAAN (JEMBATAN TIMBANG)"=>"string",
				"PELABUHAN"=>"string",
				"SHIFT"=>"string",
		);

		foreach ($kendaraan as $key => $value) {
			if($value->pemesanan >= 0){$value->pemesanan = format_dateTimeHis($value->pemesanan_date);}else{$value->pemesanan = '-';}
			if($value->pembayaran == 1){$value->pembayaran = format_dateTimeHis($value->pembayaran_date);}else{$value->pembayaran = '-';}
			if($value->cetak_boarding == 1){$value->cetak_boarding = format_dateTimeHis($value->cetak_boarding_date);}else{$value->cetak_boarding = '-';}
			if($value->validasi == 1){$value->validasi = format_dateTimeHis($value->validasi_date);}else{$value->validasi = '-';}
			$kendaraans[] = array(
				$key+1,
				$value->booking_code,
				$value->created_on,
				$value->depart_date.' '.$value->depart_time_start,
				$value->plat,
				$value->vehicle_class,
				$value->fare,
				$value->vehicle_class,
				$value->customer,
				empty($value->weighbridge)?0:$value->weighbridge,
				$value->origin,
				$value->shift,
			);
		}

		$writer->writeSheetHeader($sheet1, $header, $styles1 );
		foreach($kendaraans as $row){
			$writer->writeSheetRow($sheet1, $row, $styles2);
		}

		$writer->writeToStdOut();
	}

}