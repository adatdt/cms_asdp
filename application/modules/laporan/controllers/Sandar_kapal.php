<?php

error_reporting(0);
class Sandar_kapal extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('sandar_kapal_model', 'sandar_kapal');
		$this->load->model('global_model');
		$this->_module   = 'laporan/sandar_kapal';
		$this->load->library('Html2pdf');

		// $this->dbView=$this->load->database("dbView",TRUE);
		$this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);

	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->sandar_kapal->dataList();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home'          => 'Beranda',
			'url_home'      => site_url('home'),
			'title'         => 'Jasa Sandar Kapal',
			'content'       => 'sandar_kapal/index2',
			'port'          => $this->global_model->select_data("app.t_mtr_port","where status in (1) order by name asc")->result(),
			'branch'        => $this->global_model->select_data("app.t_mtr_branch","where status in (1) order by branch_name asc")->result(),
			'shift'         => $this->global_model->select_data("app.t_mtr_shift","where status in (1) order by shift_name asc")->result(),
			// 'download_pdf'   => checkBtnAccess($this->_module,'download_pdf'),
			// 'download_excel'=> checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	public function detail()
	{
		validate_ajax();

		$port           = $this->enc->decode($this->input->post('port'));
		$datefrom       = trim($this->input->post('datefrom'));
		$dateto         = trim($this->input->post('dateto'));
		$shift          = $this->enc->decode($this->input->post('shift'));
		$ship_class     = $this->enc->decode($this->input->post('ship_class'));

		if ($port == "") {
			$data =  array(
				'code' => 101,
				'message' => "Silahkan pilih pelabuhan!",
			);

			echo json_encode($data);
			exit;
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

		$all_data = $this->sandar_kapal->latest_sandar($datefrom, $dateto, $port, $shift);
		$data['all_data']  = $all_data;
		$data_dock = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		$data['dock']   = $data_dock;

		$shift_time = $this->sandar_kapal->get_shift_time($shift,$port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$pelabuhan = ($this->input->post('pelabuhan') != "") ? $this->input->post('pelabuhan') : "-";
        $shift_name = ($this->input->post('shift_name') != "") ? $this->input->post('shift_name') : "-";

        $regu = "Semua";

		if ($port != "") {
			$get_regu = $this->global_model->get_team($port, $datefrom, $dateto, $shift);

			$implode_regu = implode(', ',json_decode($get_regu->team_name));
			$regu = ($implode_regu != null) ? $implode_regu : "-" ;
		}

		$output =  array(
			'code' => 200,
			'data' => $data,
			'pelabuhan' => $pelabuhan,
			'shift' => $shift_name,
			'regu' => $regu,
			'jam' => $jam_shift,
		);

		echo json_encode($output);

	}

	function get_pdf(){
		$port           = $this->enc->decode($this->input->get('port'));
		$datefrom       = trim($this->input->get('datefrom'));
		$dateto         = trim($this->input->get('dateto'));
		$shift          = $this->enc->decode($this->input->get('shift'));
		$ship_class     = $this->enc->decode($this->input->get('ship_class'));

		$all_data = $this->sandar_kapal->latest_sandar($datefrom, $dateto, $port, $shift);
		$data['all_data']  = $all_data;
		$data_dock = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		$data['dock']   = $data_dock;

		$shift_time = $this->sandar_kapal->get_shift_time($shift,$port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$pelabuhan = ($this->input->get('pelabuhan') != "") ? $this->input->get('pelabuhan') : "-";
        $shift_name = ($this->input->get('shift_name') != "") ? $this->input->get('shift_name') : "-";

        $regu = "Semua";

		if ($port != "") {
			$get_regu = $this->global_model->get_team($port, $datefrom, $dateto, $shift);

			$implode_regu = implode(', ',json_decode($get_regu->team_name));
			$regu = ($implode_regu != null) ? $implode_regu : "-" ;
		}

		$output =  array(
			'code' => 200,
			'title' => "LAPORAN PENDAPATAN SANDAR KAPAL PER - SHIFT",
			'data' => $data,
			'pelabuhan' => $pelabuhan,
			'tanggal' => format_date($datefrom) . " - " . format_date($dateto),
			'shift' => $shift_name,
			'regu' => $regu,
			'jam' => $jam_shift,
		);

		$this->load->view($this->_module.'/pdf2', $output);
	}

	function get_excel()
	{
		$port       = $this->enc->decode($this->input->get('port'));
		$datefrom   = trim($this->input->get('datefrom'));
		$dateto     = trim($this->input->get('dateto'));
		$shift      = $this->enc->decode($this->input->get('shift'));
		$ship_class = $this->enc->decode($this->input->get('ship_class'));

		$all_data = $this->sandar_kapal->latest_sandar($datefrom, $dateto, $port, $shift);
		$dock = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();

		$shift_time = $this->sandar_kapal->get_shift_time($shift,$port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$pelabuhan = ($this->input->get('pelabuhan') != "") ? $this->input->get('pelabuhan') : "-";
        $shift_name = ($this->input->get('shift_name') != "") ? $this->input->get('shift_name') : "-";

        $regu = "Semua";

		if ($port != "") {
			$get_regu = $this->global_model->get_team($port, $datefrom, $dateto, $shift);

			$implode_regu = implode(', ',json_decode($get_regu->team_name));
			$regu = ($implode_regu != null) ? $implode_regu : "-" ;
		}

		$excel_name = "Jasa_sandar";

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Jasa_sandar_" . $pelabuhan . "_" . $datefrom . "_" . $dateto . ".xlsx");

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
			'border-style'=> 'thin',
		);

		$styles2= array(
			'font'=>'Arial',
			'font-size'=>10,
			'valign'=>'center',
			'border'=>'left,right,top,bottom',
			'border-style'=> 'thin',
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
			'border-style'=> 'thin',
		);

		$header = array("string","string","string","string","string");

		$judul_penumpang = array(
			"NO.",
			"NAMA PERUSAHAAN",
			"NAMA KAPAL",
			"GRT",
			"TARIF",
			"CALL",
			"TRIP",
			"JASA SANDAR NON PPN 10%",
		);

		$total_trip = 0;
        $total_duit_bawah =0;

		foreach ($all_data as $key => $value) {
			$total_trip += $value->trip;

			$dpis[] = array(
				$key+1,
				$value->company_name,
				$value->ship_name,
				$value->ship_grt,
				$value->dock_fare,
				$value->call,
				$value->trip,
			);

			$data_dock = json_decode($value->dock, true);
			$jumlah_kanan = 0;
			foreach ($dock as $dd => $vv) {
				if(array_key_exists($vv->id, $data_dock)){
					$exp = $data_dock[$vv->id];
					$dock[$dd]->total += $exp;
					$jumlah_kanan += $exp;
					$a = $exp;
				}else{
					$a = "-";
				}

				array_push($dpis[$key], $a);
			}
				$total_duit_bawah += $jumlah_kanan;
				// array_push($dpis[$key], $jumlah_kanan);
		}

		$all_dock = array('','','','','','','');

		foreach ($dock as $key => $value) {
			$all_dock[] = $value->name;
			// $judul_penumpang[] = "";
		}

		// $judul_penumpang[$key+5] = "JUMLAH";

		$writer->writeSheetRow($sheet1, array("LAPORAN PENDAPATAN SANDAR KAPAL PER - SHIFT"),$style_header);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("","PELABUHAN",$pelabuhan,"","TANGGAL",format_date($datefrom) . " - " . format_date($dateto), "", "SHIFT",$shift_name));
		$writer->writeSheetRow($sheet1, array("","REGU",$regu,"","JAM",$jam_shift));
		$writer->writeSheetRow($sheet1, array(""));
		$writer->writeSheetRow($sheet1, $judul_penumpang, $styles1);

		$writer->writeSheetRow($sheet1, $all_dock, $style_sub);

		if ($all_data) {
			foreach($dpis as $row){
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$footer = array("","","","","JUMLAH",$total_trip);

		foreach ($dock as $key => $value) {
			array_push($footer, $value->total);
		}

		// array_push($footer, $total_duit_bawah);

		$writer->writeSheetRow($sheet1, $footer, $style_sub);

		$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=count($all_dock)-1);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=0, $end_row=6, $end_col=0);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=1, $end_row=6, $end_col=1);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=2, $end_row=6, $end_col=2);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=3, $end_row=6, $end_col=3);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=4, $end_row=6, $end_col=4);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=5, $end_row=6, $end_col=5);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=6, $end_row=6, $end_col=6);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=7, $end_row=6, $end_col=count($all_dock)-1);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer->writeToStdOut();
	}

	public function yyy_detail(){
		validate_ajax();

		$port     	 = $this->enc->decode($this->input->post('port'));
		$datefrom    = trim($this->input->post('datefrom'));
		$dateto  	 = trim($this->input->post('dateto'));
		$shift       = $this->enc->decode($this->input->post('shift'));
		$pelabuhanku = $this->input->post('pelabuhanku');
		$shiftku     = $this->input->post('shiftku');

		if ($port == "") {
			$data =  array(
				'code' => 101,
				'message' => "Silahkan pilih pelabuhan!",
			);

			echo json_encode($data);
			exit;
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

		$all_data = $this->sandar_kapal->latest_sandar($datefrom, $dateto, $port, $shift);

		$shift_time = $this->sandar_kapal->get_shift_time($shift,$port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$regu = "Semua";

        if ($header) {
            $cabang = $header->branch_name;
            $pelabuhan = $header->port;
            $lintasan = $header->origin . " - " . $header->destination;
            $shift = $header->shift_name;
            $regu = $header->team_name;
        }

		$dock = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		$title = "LAPORAN HARIAN SANDAR KAPAL PER - SHIFT";

		$output =  array(
			'code' => 200,
			'data' => $all_data,
			'dock' => $dock,
			'pelabuhan' => $pelabuhanku,
			'shift' => $shiftku,
			'regu' => $regu,
			'jam' => $jam_shift,
		);

		echo json_encode($output);
	}

	function download_pdf(){
		$date           = trim($this->input->get('date'));
		$port           = $this->input->get('port');
		$shift          = $this->input->get('shift');
		$portName       = $this->input->get('port_name');
		$shiftTime      = $this->input->get('shift_time');
		$teamName       = $this->input->get('team_name');
		$data['data']   = $this->sandar_kapal->detail_pdf($date, $port, $shift);
		$data['dock']   = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		$data['title']  = "LAPORAN HARIAN SANDAR KAPAL PER - SHIFT";
		$data['date']   = $date;
		$data['port_name']  = $portName;
		$data['shift_time'] = $shiftTime;
		$data['team_name'] = $teamName;

		$this->load->view($this->_module.'/pdf', $data);
	}

	function download_excel()
	{
		$date      = trim($this->input->get('date'));
		$port      = $this->input->get('port');
		$shift     = $this->input->get('shift');
		$portName  = $this->input->get('port_name');
		$shiftTime = $this->input->get('shift_time');
		$teamName  = $this->input->get('team_name');

		$all_data       = $this->sandar_kapal->detail_pdf($date, $port, $shift);
		// echo json_encode($all_data);exit;
		$dock = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();

		$excel_name = "Jasa_sandar";

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Jasa_sandar_" . $date . "_" . $portName . ".xlsx");

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

		$judul_penumpang = array(
			"NO.",
			"NAMA KAPAL",
			"JASA SANDAR NON PPN 10%",
		);

		$total_duit_bawah = 0;

		foreach ($all_data as $key => $value) {
			$docks = explode(',', $value->dock_id);

			$dpis[] = array(
				$key+1,
				$value->ship_name,
			);

			$totalSandar = 0;
			foreach ($dock as $dd => $vv) {

				if (in_array($vv->id, $docks) && $value->dock_id == $vv->id) {
					$totalSandar += $value->total_sandar;
					$jasaSandar = ($value->total_sandar != 0) ? $value->total_sandar : '-';
					$dock[$dd]->total += $value->total_sandar;

					$a = $jasaSandar;
				} else {
					$a = "-";
				}

				array_push($dpis[$key], $a);
			}
				$total_duit_bawah += $totalSandar;
				array_push($dpis[$key], $totalSandar);
				array_push($dpis[$key], "-");
				array_push($dpis[$key], $totalSandar);
		}
		
		$all_dock = array('','');

		foreach ($dock as $key => $value) {
			$all_dock[] = $value->name;
			$judul_penumpang[] = "";
		}

		array_push($all_dock, "JUMLAH");

		$judul_penumpang[$key+4] = "JASA SANDAR KSO DRG IV";
		$judul_penumpang[$key+5] = "TOTAL SANDAR - KSO";

		$writer->writeSheetRow($sheet1, array("LAPORAN HARIAN SANDAR KAPAL PER - SHIFT"),$styles1);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("","PELABUHAN",strtoupper($portName),"","TANGGAL",format_date($date)));
		$writer->writeSheetRow($sheet1, array("","REGU",$teamName,"","JAM",$shiftTime));
		$writer->writeSheetRow($sheet1, array(""));
		$writer->writeSheetRow($sheet1, $judul_penumpang, $styles1);

		$writer->writeSheetRow($sheet1, $all_dock, $style_sub);

		if ($all_data) {
			foreach($dpis as $row){
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$footer = array("","JUMLAH");

		foreach ($dock as $key => $value) {
			// array_push($footer, $value->total);
			array_push($footer, ($value->total != "") ? $value->total : '0');
		}

		array_push($footer, $total_duit_bawah);
		array_push($footer, "0");
		array_push($footer, $total_duit_bawah);

		$writer->writeSheetRow($sheet1, $footer, $style_sub);

		$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=count($all_dock)+1);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=0, $end_row=6, $end_col=0);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=1, $end_row=6, $end_col=1);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=2, $end_row=5, $end_col=count($all_dock)-1);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=count($all_dock), $end_row=6, $end_col=count($all_dock));
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=count($all_dock)+1, $end_row=6, $end_col=count($all_dock)+1);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer->writeToStdOut();
	}
}