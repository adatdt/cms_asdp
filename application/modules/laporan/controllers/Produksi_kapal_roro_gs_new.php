<?php
error_reporting(0);
class Produksi_kapal_roro_gs_new extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('produksi_kapal_roro_gs_new_model', 'produksi_roro');
        $this->load->model('global_model');
		$this->_module   = 'laporan/produksi_kapal_roro_gs_new';
		$this->load->library('Html2pdf');

		$this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);      

	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->produksi_roro->dataList();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' 			=> 'Beranda',
			'url_home' 		=> site_url('home'),
			'title' 		=> 'Produksi Kapal RO-RO GS',
			'content' 		=> 'produksi_kapal_roro_gs_new/index',
			'port'			=> $this->global_model->select_data("app.t_mtr_port","where status in (1) order by name asc")->result(),
			'shift'         => $this->global_model->select_data("app.t_mtr_shift","where status in (1) order by shift_name asc")->result(),
			'download_pdf' 	=> checkBtnAccess($this->_module,'download_pdf'),
			'download_excel'=> checkBtnAccess($this->_module,'download_excel'),
		);

		$this->load->view ('default', $data);
	}

	public function detail(){
		validate_ajax();

		$port           = $this->enc->decode($this->input->post('port'));
		$datefrom       = trim($this->input->post('datefrom'));
		$dateto         = trim($this->input->post('dateto'));
		$shift          = $this->enc->decode($this->input->post('shift'));
		// $ship_class     = $this->enc->decode($this->input->post('ship_class'));

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

		$all_data = $this->produksi_roro->get_all_data($datefrom, $dateto, $port, $shift);
		$data['all_data']  = $all_data;

		$shift_time = $this->produksi_roro->get_shift_time($shift);
		$regu_list = $this->produksi_roro->get_regu($shift, $port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$cabang = "Semua";
        $pelabuhan = "Semua";
        $lintasan = "Semua";
        $shift = "Semua";
		$regu = "Semua";

		$dataLength = count((array)$regu_list);
		$i=1;
		if($regu_list){
			$regu = "";
			foreach ($regu_list as $list) {
				$i == $dataLength ? $regu .= $list->team_name : $regu .= $list->team_name . ", ";
				$i++;
			}
		}

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
    	// echo " 1 ".(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
		$port           = $this->enc->decode($this->input->get('port'));
		$datefrom       = trim($this->input->get('datefrom'));
		$dateto         = trim($this->input->get('dateto'));
		$shift          = $this->enc->decode($this->input->get('shift'));

		$all_data = $this->produksi_roro->get_all_data($datefrom, $dateto, $port, $shift);
		$data['all_data']  = $all_data;

		$shift_time = $this->produksi_roro->get_shift_time($shift);
		$regu_list = $this->produksi_roro->get_regu($shift, $port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$cabang = "Semua";
        $pelabuhan = $this->input->get('pelabuhanku');
        $lintasan = "Semua";
        $shift = $this->input->get('shiftku');
		$regu = "Semua";

		$dataLength = count((array)$regu_list);
		$i=1;
		if($regu_list){
			$regu = "";
			foreach ($regu_list as $list) {
				$i == $dataLength ? $regu .= $list->team_name : $regu .= $list->team_name . ", ";
				$i++;
			}
		}

        if ($header) {
            $cabang = $header->branch_name;
            $pelabuhan = $header->port;
            $lintasan = $header->origin . " - " . $header->destination;
            $shift = $header->shift_name;
            $regu = $header->team_name;
		}

		if($datefrom == $dateto){
			$tanggal = format_date($datefrom);
		} else {
			$tanggal = format_date($datefrom) . " - " . format_date($dateto);
		}

		$output =  array(
			'code' => 200,
			'title' => "LAPORAN PRODUKSI KAPAL RO-RO GS",
			'data' => $data,
			'pelabuhan' => $pelabuhan,
			'tanggal' => $tanggal,
			'shift' => $shift,
			'regu' => $regu,
			'jam' => $jam_shift,
		);
		// echo " 1 ".(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
		// exit();
		$this->load->view($this->_module.'/pdf', $output);
	}

	function get_excel()
	{
		$port       = $this->enc->decode($this->input->get('port'));
		$datefrom   = trim($this->input->get('datefrom'));
		$dateto     = trim($this->input->get('dateto'));
		$shift      = $this->enc->decode($this->input->get('shift'));

		$all_data = $this->produksi_roro->get_all_data($datefrom, $dateto, $port, $shift);
		$shift_time = $this->produksi_roro->get_shift_time($shift);
		$regu_list = $this->produksi_roro->get_regu($shift, $port);

		$jam_shift = "-";

		if ($shift_time) {
			$jam_shift = format_time($shift_time->shift_login) . " - " . format_time($shift_time->shift_logout);
		}

		$cabang = "Semua";
        $pelabuhan = $this->input->get('pelabuhanku');
        $lintasan = "Semua";
        $shift = $this->input->get('shiftku');
		$regu = "Semua";

		$dataLength = count((array)$regu_list);
		$i=1;
		if($regu_list){
			$regu = "";
			foreach ($regu_list as $list) {
				$i == $dataLength ? $regu .= $list->team_name : $regu .= $list->team_name . ", ";
				$i++;
			}
		}

        if ($header) {
            $cabang = $header->branch_name;
            $pelabuhan = $header->port;
            $lintasan = $header->origin . " - " . $header->destination;
            $shift = $header->shift_name;
            $regu = $header->team_name;
		}

		$excel_name = "Produksi_kapal_roro_gs";

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();

		if($datefrom == $dateto){
			$filename = strtoupper("Produksi_kapal_roro_gs_" . $pelabuhan . "_" . $datefrom . ".xlsx");
		} else {
			$filename = strtoupper("Produksi_kapal_roro_gs_" . $pelabuhan . "_" . $datefrom . "_" . $dateto . ".xlsx");
		}

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
			'halign'=>'center',
			'valign'=>'right',
			'border'=>'left,right,top,bottom',
			'border-style'=> 'thin',
			'border-style'=> 'thin',
		);

		$style_foot= array(
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
			"TIBA",
			"BERANGKAT",
			"DURASI",
			"DERMAGA",
			"CALL",
			"PENUMPANG",
			"",
			"JUMLAH PENUMPANG",
			"KENDARAAN GOLONGAN",
			"","","","","","","","","","","",
			"JUMLAH KENDARAAN",
			"TOTAL (Rp)"
		);

		$total_amount = 0;
		$total_dewasa = $total_anak = $total_pnp = $total_knd = 0;
		$total_gol1 = $total_gol2 = $total_gol3 = $total_gol4A = $total_gol4B = $total_gol5A = $total_gol5B = $total_gol6A = $total_gol6B = $total_gol7 = $total_gol8 = $total_gol9 = 0;
		foreach ($all_data as $key => $value) {
			$total_dewasa += $value->dewasa;
			$total_anak += $value->anak;
			$total_pnp += $value->totalP;

			$total_gol1 += $value->gol1;
			$total_gol2 += $value->gol2;
			$total_gol3 += $value->gol3;
			$total_gol4A += $value->gol4A;
			$total_gol4B += $value->gol4B;
			$total_gol5A += $value->gol5A;
			$total_gol5B += $value->gol5B;
			$total_gol6A += $value->gol6A;
			$total_gol6B += $value->gol6B;
			$total_gol7 += $value->gol7;
			$total_gol8 += $value->gol8;
			$total_gol9 += $value->gol9;
			$total_knd += $value->knd;
			$total_amount += $value->amount;

			$dpis[] = array(
				$key+1,
				$value->company,
				$value->ship,
				$value->ship_grt,
				$value->docking_date.' '.$value->docking_time,
				$value->sail_date.' '.$value->sail_time,
				$value->duration,
				$value->dermaga,
				$value->call,

				$value->dewasa,
				$value->anak,
				$value->totalP,

				$value->gol1,
				$value->gol2,
				$value->gol3,
				$value->gol4A,
				$value->gol4B,
				$value->gol5A,
				$value->gol5B,
				$value->gol6A,
				$value->gol6B,
				$value->gol7,
				$value->gol8,
				$value->gol9,
				$value->totalK,
				$value->amount,
			);
		}

		$foot = array(
			'JUMLAH','','','','','','','','',
			$total_dewasa,
			$total_anak,
			$total_pnp,
			$total_gol1,
			$total_gol2,
			$total_gol3,
			$total_gol4A,
			$total_gol4B,
			$total_gol5A,
			$total_gol5B,
			$total_gol6A,
			$total_gol6B,
			$total_gol7,
			$total_gol8,
			$total_gol9,
			$total_knd,
			$total_amount,
		);

		$sub_head = array('','','','','','','','','','DEWASA','ANAK','','I','II','III','IVA','IVB','VA','VB','VIA','VIB','VII','VIII','IX','','');

		$writer->writeSheetRow($sheet1, array("LAPORAN PRODUKSI KAPAL RO-RO GS"),$style_header);
		$writer->writeSheetRow($sheet1, array(""));

		if($datefrom == $dateto){
			$writer->writeSheetRow($sheet1, array("","PELABUHAN",$pelabuhan,"","TANGGAL",format_date($datefrom), "", "SHIFT",$shift));
		} else {
			$writer->writeSheetRow($sheet1, array("","PELABUHAN",$pelabuhan,"","TANGGAL",format_date($datefrom) . " - " . format_date($dateto), "", "SHIFT",$shift));
		}
		$writer->writeSheetRow($sheet1, array("","REGU",$regu,"","JAM",$jam_shift));
		$writer->writeSheetRow($sheet1, array(""));
		$writer->writeSheetRow($sheet1, $judul_penumpang, $styles1);

		$writer->writeSheetRow($sheet1, $sub_head, $style_sub);

		if ($all_data) {
			foreach($dpis as $row){
				$writer->writeSheetRow($sheet1, $row, $styles2);
			}
		}

		$writer->writeSheetRow($sheet1, $foot, $style_foot);

		$writer->markMergedCell($sheet1, $start_row=5, $start_col=0, $end_row=6, $end_col=0);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=1, $end_row=6, $end_col=1);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=2, $end_row=6, $end_col=2);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=3, $end_row=6, $end_col=3);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=4, $end_row=6, $end_col=4);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=5, $end_row=6, $end_col=5);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=6, $end_row=6, $end_col=6);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=7, $end_row=6, $end_col=7);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=8, $end_row=6, $end_col=8);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=11, $end_row=6, $end_col=11);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=24, $end_row=6, $end_col=24);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=25, $end_row=6, $end_col=25);

		$writer->markMergedCell($sheet1, $start_row=5, $start_col=9, $end_row=5, $end_col=10);
		$writer->markMergedCell($sheet1, $start_row=5, $start_col=12, $end_row=5, $end_col=23);
		$writer->markMergedCell($sheet1, $start_row=69, $start_col=0, $end_row=69, $end_col=8);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$writer->writeToStdOut();
	}
}