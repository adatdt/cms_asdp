<?php

error_reporting(0);
class Pendapatan_cetak_tiket extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('pendapatan_cetak_tiket_model', 'pendapatan_ctk_tiket');
        $this->load->model('global_model');
		$this->_module   = 'laporan/pendapatan_cetak_tiket';
		$this->load->library('Html2pdf');

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);        
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->pendapatan_ctk_tiket->dataList();
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' 	  => 'Beranda',
			'url_home'=> site_url('home'),
			'title'   => 'Pendapatan Jasa Administrasi Operasional Cetak Tiket Per-shift',
			'content' => 'pendapatan_cetak_tiket/index2',
			'port'    => $this->global_model->select_data("app.t_mtr_port","where status in (1) order by name asc")->result(),
            'branch'  => $this->global_model->select_data("app.t_mtr_branch","where status in (1) order by branch_name asc")->result(),
            'shift'   => $this->global_model->select_data("app.t_mtr_shift","where status in (1) order by shift_name asc")->result(),
		);

		$this->load->view ('default', $data);
	}

	function download_pdf($param){
        $this->global_model->checkAccessMenuAction($this->_module,'download_pdf');
        $param = $this->enc->decode($param);

        if (!$param) {
            redirect('/');
        } else {
            $param = explode('|', $param);
            $data['title'] = 'LAPORAN PENDAPATAN JASA ADMINISTRASI OPERASIONAL CETAK TIKET PER-SHIFT';
            $data['data']   = $this->pendapatan_ctk_tiket->list_detail($param);
            $data['param']  = $param;

            $this->load->view($this->_module.'/pdf',$data);
        }
    }

	public function detail() {
       validate_ajax();

        $port = $this->enc->decode($this->input->post('port'));
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $shift_id = $this->enc->decode($this->input->post('shift'));
        $pelabuhanku = $this->input->post('pelabuhanku');
        $shiftku = $this->input->post('shiftku');

        $header = $this->pendapatan_ctk_tiket->headerku($port,$datefrom,$dateto,$shift_id);
        $all_data = $this->pendapatan_ctk_tiket->list_detail($port,$datefrom,$dateto,$shift_id);

        $cabang = "Semua";
        $pelabuhan = $pelabuhanku;
        $lintasan = "Semua";
        $shift = $shiftku;
        $regu = "Semua";

        if ($header) {
            $cabang = $header->branch_name;
            $pelabuhan = $header->port;
            $lintasan = $header->origin . " - " . $header->destination;
            $shift = $header->shift_name;
            $regu = $header->team_name;
        }

        if (!$all_data) {
            $data =  array(
                'code' => 101,
                'message' => "Tidak ada data",
            );

            echo json_encode($data);
            exit;
        }else{
            $input = array(
                'code' => 200,
                'data' => $all_data,
                'report_title' => "LAPORAN PENDAPATAN JASA ADMINISTRASI OPERASIONAL CETAK TIKET PER-SHIFT",
                'cabang' => $cabang,
                'pelabuhan' => $pelabuhan,
                'lintasan' => $lintasan,
                'shift' => $shift,
                'regu' => $regu,
            );

            echo json_encode($input);
            exit;
        }
    }

    function get_pdf(){
        $port = $this->enc->decode($this->input->get('port'));
        $datefrom = $this->input->get('datefrom');
        $dateto = $this->input->get('dateto');
        $shift_id = $this->enc->decode($this->input->get('shift'));

        $header = $this->pendapatan_ctk_tiket->headerku($port,$datefrom,$dateto,$shift_id);
        $all_data = $this->pendapatan_ctk_tiket->list_detail($port,$datefrom,$dateto,$shift_id);

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

        $data['cabang'] = $cabang;
        $data['pelabuhan'] = $pelabuhan;
        $data['lintasan'] = $lintasan;
        $data['shift'] = $shift;
        $data['regu'] = $regu;

        $data['report_title'] = "LAPORAN PENDAPATAN JASA ADMINISTRASI OPERASIONAL CETAK TIKET PER-SHIFT";
        $data['data'] = $all_data;

        $this->load->view($this->_module.'/pdf2',$data);
    }

    function get_excel(){
        $port = $this->enc->decode($this->input->get('port'));
        $datefrom = $this->input->get('datefrom');
        $dateto = $this->input->get('dateto');
        $shift_id = $this->enc->decode($this->input->get('shift'));

        $header = $this->pendapatan_ctk_tiket->headerku($port,$datefrom,$dateto,$shift_id);
        $all_data = $this->pendapatan_ctk_tiket->list_detail($port,$datefrom,$dateto,$shift_id);

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

        $this->load->library('XLSExcel');
        $writer = new XLSXWriter();
        $filename = strtoupper("Pendapatan_cetak_tiket_" . $pelabuhan . "_" . $datefrom . "_" . $dateto . ".xlsx");

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

        $judul_tunai = array(
            "NO.",
            "NAMA PERUSAHAAN",
            "NAMA KAPAL",
            "TARIF",
            "PRODUKSI (Lbr)",
            "PENDAPATAN (Rp)",
            "KET",
        );

        $produksi = 0;
        $pendapatan = 0;

        foreach ($all_data['data'] as $key => $value) {
            $produksi += $value->produksi;
            $pendapatan += $value->pendapatan;

            $tunais[] = array(
                $key+1,
                $value->company,
                $value->ship_name,
                $value->harga,
                $value->produksi,
                $value->pendapatan,
                "",
            );
        }

        $writer->writeSheetRow($sheet1, array("FORMULIR LAPORAN PENDAPATAN ASURANSI IURAN WAJIB PER-SHIFT"),$style_header);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("PELABUHAN",$pelabuhan,"","SHIFT",$shift));
        $writer->writeSheetRow($sheet1, array("REGU",$regu,"","TANGGAL",format_date($datefrom) . " - " . format_date($dateto)));
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, $judul_tunai, $styles1);

        if ($all_data) {
            foreach($tunais as $row){
                $writer->writeSheetRow($sheet1, $row, $styles2);
            }
        }

        $writer->writeSheetRow($sheet1, array("JUMLAH","","","",$produksi,$pendapatan,""),$style_sub);
        $writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=6);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }

    function download_excel($param)
    {
        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        $param = $this->enc->decode($param);

        if (!$param) {
            redirect('/');
        } else {
        	$excel_name = "Jasa_tiket";
        	$param = explode('|', $param);

	        $data_detail = $this->pendapatan_ctk_tiket->list_detail($param);

	        $this->load->library('XLSExcel');
	        $writer = new XLSXWriter();
	        $filename = strtoupper("Jasa_tiket_" . $param[0] . ".xlsx");
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

	        $juduls = array(
	            array(
	                "NO.",
	                "NAMA KAPAL",
	                "PRODUKSI (Lbr)",
	                "PENDAPATAN (Rp)",
	                "KET",
	            ),
	        );

	        foreach ($data_detail['data'] as $key => $value) {

	            $datas[] = array(
	                $key+1,
	                $value->ship_name,
	                $value->ticket_count,
	                $value->adm_fee,
	                $value->dock_fee,
	            );
	        }

	        foreach($juduls as $title){
	            $writer->writeSheetRow($sheet1, array("LAPORAN PENDAPATAN JASA ADMINISTRASI OPERASIONAL CETAK TIKET PER-SHIFT"),$style_header);
	            $writer->writeSheetRow($sheet1, array(""));

	            $writer->writeSheetRow($sheet1, array("PELABUHAN",$param[3],"","SHIFT",$param[4]));
	            $writer->writeSheetRow($sheet1, array("REGU",$param[3],"","TANGGAL",format_date($param[0])));
	            $writer->writeSheetRow($sheet1, array(""));

	            $writer->writeSheetRow($sheet1, $title, $styles1);
	        }

	        if ($data_detail) {
	            foreach($datas as $row){
	                $writer->writeSheetRow($sheet1, $row, $styles2);
	            }
	        }

	        $writer->writeSheetRow($sheet1, array("JUMLAH","",$data_detail['totalTicket'],$data_detail['totalAdmfee'],"",""),$style_sub);
	        $writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=4);

	        $writer->writeToStdOut();
	    }
    }
}