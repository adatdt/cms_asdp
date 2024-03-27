<?php

error_reporting(0);
class Jasa_tambat extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('jasa_tambat_model', 'm_tambat');
		$this->load->model('global_model');
		$this->_module   = 'laporan/jasa_tambat';
		$this->load->library('Html2pdf');

		// $this->dbView=$this->load->database("dbView",TRUE);
		$this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);		
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->m_tambat->dataList();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' 			=> 'Beranda',
			'url_home' 		=> site_url('home'),
			'title' 		=> 'Laporan Jasa Tambat',
			'content' 		=> 'jasa_tambat/index2',
			'port'			=> $this->global_model->select_data("app.t_mtr_port","where status in (1) order by name asc")->result(),
			'branch'		=> $this->global_model->select_data("app.t_mtr_branch","where status in (1) order by branch_name asc")->result(),
			'shift'         => $this->global_model->select_data("app.t_mtr_shift","where status in (1) order by shift_name asc")->result(),
			'class' => $this->global_model->getclass(),
		);

		$this->load->view ('default', $data);
	}

	public function old_detail($assignment_code,$ship_class_id){
		validate_ajax();
		$date           = trim($this->input->get('date'));
		$port           = $this->input->get('port');
		$shift          = $this->input->get('shift');
		$portName       = $this->input->get('port_name');
		$shiftTime      = $this->input->get('shift_time');
		$teamName       = $this->input->get('team_name');
		$all_data       = $this->m_tambat->get_all_data($date, $port, $shift);
		$data['title']  = "LAPORAN PENDAPATAN JASA TAMBAT KAPAL PER - SHIFT";
		$data['all_data']  = $all_data;
		$data['dock']   = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		$dockku = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		// echo json_encode($dockku);exit;
		$data['date']   = $date;
		$data['port_name']  = $portName;
		$data['shift_time'] = $shiftTime;
		$data['team_name'] = $teamName;
	}

	public function detail(){
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

		$headerku = $this->m_tambat->headerku($port,$datefrom,$dateto,$ship_class,$shift);

		$all_data = $this->m_tambat->get_all_data($datefrom, $dateto, $port, $ship_class, $shift);
		$data['all_data']  = $all_data;
		$data_dock = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		$data['dock']   = $data_dock;

		$shift_time = $this->m_tambat->get_shift_time($shift,$port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$cabang = "Semua";
        $pelabuhan = "Semua";
        $lintasan = "Semua";
        $shift = "Semua";
        $regu = "Semua";

        if ($header) {
            $cabang = $header->branch_name;
            $pelabuhan = $header->port;
            $lintasan = $header->origin . " - " . $header->destination;
            $shift = $header->shift_name;
            $regu = $header->team_name;
        }

		$output =  array(
			'code' => 200,
			'data' => $data,
			'pelabuhan' => $pelabuhan,
			'shift' => $shift,
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
		
		$headerku = $this->m_tambat->headerku($port,$datefrom,$dateto,$ship_class,$shift);

		$all_data = $this->m_tambat->get_all_data($datefrom, $dateto, $port, $ship_class, $shift);
		$data['all_data']  = $all_data;
		$data_dock = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		$data['dock']   = $data_dock;

		$shift_time = $this->m_tambat->get_shift_time($shift,$port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$cabang = "Semua";
        $pelabuhan = $this->input->get('pelabuhanku');
        $lintasan = "Semua";
        $shift = $this->input->get('shiftku');
        $regu = "Semua";

        if ($header) {
            $cabang = $header->branch_name;
            $pelabuhan = $header->port;
            $lintasan = $header->origin . " - " . $header->destination;
            $shift = $header->shift_name;
            $regu = $header->team_name;
        }

		$output =  array(
			'code' => 200,
			'title' => "LAPORAN PENDAPATAN JASA TAMBAT KAPAL PER - SHIFT",
			'data' => $data,
			'pelabuhan' => $pelabuhan,
			'tanggal' => format_date($datefrom) . " - " . format_date($dateto),
			'shift' => $shift,
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

		$headerku = $this->m_tambat->headerku($port,$datefrom,$dateto,$ship_class,$shift);
		$all_data = $this->m_tambat->get_all_data($datefrom, $dateto, $port, $ship_class, $shift);
		$dock = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();

		$shift_time = $this->m_tambat->get_shift_time($shift,$port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$cabang = "Semua";
        $pelabuhan = $this->input->get('pelabuhanku');
        $lintasan = "Semua";
        $shift = $this->input->get('shiftku');
        $regu = "Semua";

        if ($header) {
            $cabang = $header->branch_name;
            $pelabuhan = $header->port;
            $lintasan = $header->origin . " - " . $header->destination;
            $shift = $header->shift_name;
            $regu = $header->team_name;
        }

		$excel_name = "Jasa_tambat";

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Jasa_tambat_" . $pelabuhan . "_" . $datefrom . "_" . $dateto . ".xlsx");

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
			"TRIP",
			"PENDAPATAN JASA TAMBAT PER DERMAGA",
		);

		$total_trip = 0;
        $total_duit_bawah =0;

		foreach ($all_data as $key => $value) {
			$total_trip += $value->trip;

			$dpis[] = array(
				$key+1,
				$value->company,
				$value->ship_name,
				$value->trip,
			);

			$data_dock = json_decode($value->dock, true);
			$jumlah_kanan = 0;
			foreach ($dock as $dd => $vv) {
				if(array_key_exists($vv->id, $data_dock)){
					$exp = explode("-", $data_dock[$vv->id]);
					$dock[$dd]->total += $exp[1];
					$jumlah_kanan += $exp[1];
					$a = $exp[1];
				}else{
					$a = "-";
				}

				array_push($dpis[$key], $a);
			}
				$total_duit_bawah += $jumlah_kanan;
				array_push($dpis[$key], $jumlah_kanan);
		}

		$all_dock = array('','','','');

		foreach ($dock as $key => $value) {
			$all_dock[] = $value->name;
			$judul_penumpang[] = "";
		}

		$judul_penumpang[$key+5] = "JUMLAH";

		$writer->writeSheetRow($sheet1, array("LAPORAN PENDAPATAN JASA TAMBAT KAPAL PER - SHIFT"),$style_header);
		$writer->writeSheetRow($sheet1, array(""));

		$writer->writeSheetRow($sheet1, array("","PELABUHAN",$pelabuhan,"","TANGGAL",format_date($datefrom) . " - " . format_date($dateto), "", "SHIFT",$shift));
		$writer->writeSheetRow($sheet1, array("","REGU",$regu,"","JAM",$jam_shift));
		$writer->writeSheetRow($sheet1, array(""));
		$writer->writeSheetRow($sheet1, $judul_penumpang, $styles1);

		$writer->writeSheetRow($sheet1, $all_dock, $style_sub);

		if ($all_data) {
			foreach($dpis as $row){
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$footer = array("","","JUMLAH",$total_trip);

		foreach ($dock as $key => $value) {
			array_push($footer, $value->total);
		}

		array_push($footer, $total_duit_bawah);

		$writer->writeSheetRow($sheet1, $footer, $style_sub);

		$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=count($all_dock));
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=0, $end_row=6, $end_col=0);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=1, $end_row=6, $end_col=1);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=2, $end_row=6, $end_col=2);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=3, $end_row=6, $end_col=3);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=4, $end_row=5, $end_col=count($all_dock)-1);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=count($all_dock), $end_row=6, $end_col=count($all_dock));

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer->writeToStdOut();
	}

	function old_download_pdf(){
		$date           = trim($this->input->get('date'));
		$port           = $this->input->get('port');
		$shift          = $this->input->get('shift');
		$portName       = $this->input->get('port_name');
		$shiftTime      = $this->input->get('shift_time');
		$teamName       = $this->input->get('team_name');
		// $dataTrip       = $this->m_tambat->dataTrip($date, $port, $shift);
		// $dataTambat     = $this->m_tambat->dataTambat($date, $port, $shift);
		$all_data       = $this->m_tambat->get_all_data($date, $port, $shift);
		// echo json_encode($data);exit;
		// echo json_encode($dataTambat);exit;
		// $data['dataTrip']   = $dataTrip;
		// $data['dataTambat'] = $dataTambat;
		// echo json_encode($this->m_tambat->detail_pdf($date, $port, $shift));exit;
		$data['title']  = "LAPORAN PENDAPATAN JASA TAMBAT KAPAL PER - SHIFT";
		$data['all_data']  = $all_data;
		$data['dock']   = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		$dockku = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		// echo json_encode($dockku);exit;
		$data['date']   = $date;
		$data['port_name']  = $portName;
		$data['shift_time'] = $shiftTime;
		$data['team_name'] = $teamName;
		// echo json_encode($data);exit;

		$this->load->view($this->_module.'/pdf', $data);
	}

	function download_pdf(){
		$date           = trim($this->input->get('date'));
		$port           = $this->input->get('port');
		$shift          = $this->input->get('shift');
		$portName       = $this->input->get('port_name');
		$shiftTime      = $this->input->get('shift_time');
		$teamName       = $this->input->get('team_name');
		$all_data       = $this->m_tambat->get_all_data($date, $port, $shift);
		$data['title']  = "LAPORAN PENDAPATAN JASA TAMBAT KAPAL PER - SHIFT";
		$data['all_data']  = $all_data;
		$data['dock']   = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		$dockku = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		$data['date']   = $date;
		$data['port_name']  = $portName;
		$data['shift_time'] = $shiftTime;
		$data['team_name'] = $teamName;

		$this->load->view($this->_module.'/pdf', $data);
	}

	function download_excel()
	{
		$date           = trim($this->input->get('date'));
		$port           = $this->input->get('port');
		$shift          = $this->input->get('shift');
		$portName       = $this->input->get('port_name');
		$shiftTime      = $this->input->get('shift_time');
		$teamName       = $this->input->get('team_name');

		$all_data       = $this->m_tambat->get_all_data($date, $port, $shift);
		$dock = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();
		$dockku = $this->global_model->select_data("app.t_mtr_dock", "where status in (1) and port_id={$port} order by name asc")->result();

		$excel_name = "Jasa_tambat";

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();
		$filename = strtoupper("Jasa_tambat_" . $date . "_" . $portName . ".xlsx");

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
			"TRIP",
			"PENDAPATAN JASA TAMBAT PER DERMAGA",
		);

		$total_trip = 0;
        $total_duit_bawah =0;

		foreach ($all_data as $key => $value) {
			$total_trip += $value->trip;

			$dpis[] = array(
				$key+1,
				$value->ship_name,
				$value->trip,
			);

			$data_dock = json_decode($value->dock, true);
			// print_r($data_dock);
			$jumlah_kanan = 0;
			foreach ($dock as $dd => $vv) {
				if(array_key_exists($vv->id, $data_dock)){
					$dock[$dd]->total += $exp[1];
					$exp = explode("-", $data_dock[$vv->id]);
					$jumlah_kanan += $exp[1];
					$a = $exp[1];
				}else{
					$a = "-";
				}

				array_push($dpis[$key], $a);
			}
				$total_duit_bawah += $jumlah_kanan;
				array_push($dpis[$key], $jumlah_kanan);
		}

		// print_r($dpis);exit;

		$all_dock = array('','','');

		foreach ($dock as $key => $value) {
			$all_dock[] = $value->name;
			$judul_penumpang[] = "";
		}

		$judul_penumpang[$key+4] = "JUMLAH";

		$writer->writeSheetRow($sheet1, array("LAPORAN PENDAPATAN JASA TAMBAT KAPAL PER - SHIFT"),$style_header);
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

		$footer = array("","JUMLAH",$total_trip);
		// $total_trip,$total_duit_bawah,"1","2",""

		foreach ($dock as $key => $value) {
			array_push($footer, $value->total);
		}

		array_push($footer, $total_duit_bawah);

		$writer->writeSheetRow($sheet1, $footer, $style_sub);

		$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=count($all_dock));
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=0, $end_row=6, $end_col=0);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=1, $end_row=6, $end_col=1);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=2, $end_row=6, $end_col=2);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=3, $end_row=5, $end_col=count($all_dock)-1);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=count($all_dock), $end_row=6, $end_col=count($all_dock));

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer->writeToStdOut();
	}
}