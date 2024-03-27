<?php

error_reporting(0);
class Dermaga_infinity extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Dermaga_infinity_model', 'model');
        $this->load->model('global_model');
		$this->_module   = 'laporan/dermaga_infinity';
		$this->load->library('Html2pdf');
        $this->report_name = "dermaga_infinity";
        $this->report_code = $this->global_model->get_report_code($this->report_name);
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
        
        $dock_id = $this->global_model->select_data("app.t_mtr_custom_param", "where param_name = 'infinity_dock_id' and status = 1 ")->row(); 
        
		if($this->input->is_ajax_request()){
			$rows = $this->model->dataList($dock_id->param_value);
			echo json_encode($rows);
			exit;
		}

		$data = array(
			'home' 			=> 'Beranda',
			'url_home' 		=> site_url('home'),
			'title' 		=> 'Dermaga IV Infinity',
			'content' 		=> $this->_module.'/index2',
            'url_datatables'=> current_url(),

			'branch'		=> $this->global_model->select_data("app.t_mtr_branch","where status not in (-5) order by branch_name asc")->result(),
            'petugas'        => $this->global_model->select_data("core.t_mtr_user","WHERE user_group_id = 4 AND status=1")->result(),
            'port'        => $this->global_model->select_data("app.t_mtr_port","where status = 1")->result(),
            'class'       => $this->global_model->select_data("app.t_mtr_ship_class","where status = 1")->result(),
            'shift'       => $this->global_model->select_data("app.t_mtr_shift","where status not in (-5) order by shift_name asc")->result()
		);

		$this->load->view ('default', $data);
	}

    function get_regu($port_id="")
    {
        validate_ajax();
        $port_id = $this->enc->decode($port_id);

        if (!$port_id) {
            $option = '<option value="" selected>Semua</option>';
            echo $option;
        }else{
            $data = $this->model->get_team($port_id);
            $option = '<option value="" selected>Semua</option>';
            foreach ($data as $key => $value) {
                $option .= '<option value="'.$this->enc->encode($value->team_code).'">'.$value->team_name.'</option>';
            }
            echo $option;
        }
    }

	public function old_detail($assignment_code='',$ship_class_id){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $code=$this->enc->decode($assignment_code);
        $ship_class_id=$this->enc->decode($ship_class_id);
        if(!$code){
        	$this->load->view('error_404');
        	return false;
        }

        $data['home']     	= 'Home';
        $data['url_home'] 	= site_url('home');
        $data['title'] 		= 'Detail';
        $data['content']  	= $this->_module.'/detail_modal';
        $dock_id = $this->global_model->select_data("app.t_mtr_custom_param", "where param_name = 'infinity_dock_id' and status = 1 ")->row();
        $data['detail_trip']   = $this->model->detail_trip(" where up.assignment_code = '$code' ",$ship_class_id)->row();
        $data['detail_passenger']   = $this->model->list_detail_passanger(" where ob.assignment_code = '$code' and brp.dock_id = $dock_id->param_value ",$ship_class_id)->result();
        $data['sub_total_passenger']   = $this->model->sub_total_passanger(" where ob.assignment_code = '$code' and brp.dock_id = $dock_id->param_value",$ship_class_id)->row();
        $data['detail_vehicle']  = $this->model->list_detail_vehicle(" where ob.assignment_code = '$code' and brv.dock_id = $dock_id->param_value ",$ship_class_id)->result();
        $data['sub_total_vehicle']   = $this->model->sub_total_vehicle(" where ob.assignment_code = '$code' and brv.dock_id = $dock_id->param_value",$ship_class_id)->row();
        $data['report_title'] = "FORMULIR LAPORAN PENDAPATAN DERMAGA IV INFINITY PER-SHIFT";

        $this->load->view($this->_module.'/detail_modal',$data); 
    }

    public function detail(){
        validate_ajax();

        $port = $this->enc->decode($this->input->post('port'));
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $regu = $this->enc->decode($this->input->post('regu'));
        $petugas = $this->enc->decode($this->input->post('petugas'));
        $shift = $this->enc->decode($this->input->post('shift'));
        $ship_class = $this->enc->decode($this->input->post('ship_class'));

        $keterangan_report = "DRAFT";

        $status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift, $ship_class);

        if ($status_approve) {
            $keterangan_report = "APPROVED";
        }

        $pelabuhan = $this->enc->decode($this->input->post('pelabuhan'));
        $cabang = $this->enc->decode($this->input->post('cabang'));
        $shiftku = $this->enc->decode($this->input->post('shiftku'));
        $reguku = $this->enc->decode($this->input->post('reguku'));
        $petugasku = $this->enc->decode($this->input->post('petugasku'));

        $lintasan = $this->model->get_lintasan($port,$datefrom,$dateto,$ship_class);

        $lintasanku = "-";

        if ($lintasan) {
            $data_lintasan = $lintasan->row();
            $lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;
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

        $penumpang = $this->model->list_detail_passanger($port, $datefrom, $dateto, $regu, $ship_class, $shift, $petugas);
        $kendaraan = $this->model->list_detail_vehicle($port, $datefrom, $dateto, $regu, $ship_class, $shift, $petugas);

        $output = array(
            'code' => 200,
            'status_approve' => $keterangan_report,
            'lintasan' => $lintasanku,
            'petugasku' => $petugasku,
            'penumpang' => $penumpang,
            'kendaraan' => $kendaraan
        );

        echo json_encode($output);
        exit;
    }

    function get_pdf(){
        $port = $this->enc->decode($this->input->get('port'));
        $datefrom = $this->input->get('datefrom');
        $dateto = $this->input->get('dateto');
        $regu = $this->enc->decode($this->input->get('regu'));
        $petugas = $this->enc->decode($this->input->get('petugas'));
        $shift = $this->enc->decode($this->input->get('shift'));
        $ship_class = $this->enc->decode($this->input->get('ship_class'));

        $keterangan_report = "DRAFT";

        $status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift, $ship_class);

        if ($status_approve) {
            $keterangan_report = "APPROVED";
        }

        $pelabuhan = $this->input->get('pelabuhanku');
        $cabang = $this->input->get('cabang');
        $shiftku = $this->input->get('shiftku');
        $reguku = $this->input->get('reguku');
        $petugasku = $this->input->get('petugasku');

        $lintasan = $this->model->get_lintasan($port,$datefrom,$dateto,$ship_class);

        $lintasanku = "-";

        if ($lintasan) {
            $data_lintasan = $lintasan->row();
            $lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;
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

        $penumpang = $this->model->list_detail_passanger($port, $datefrom, $dateto, $regu, $ship_class, $shift, $petugas);
        $kendaraan = $this->model->list_detail_vehicle($port, $datefrom, $dateto, $regu, $ship_class, $shift, $petugas);

        $data['status_approve'] = $keterangan_report;
        $data['lintasan'] = $lintasanku;
        $data['petugas'] = $petugasku;
        $data['penumpang'] = $penumpang;
        $data['kendaraan'] = $kendaraan;

        $data['cabang'] = $pelabuhan;
        $data['pelabuhan'] = $pelabuhan;
        $data['shift'] = $shiftku;
        $data['regu'] = $reguku;

        $data['report_title'] = "FORMULIR LAPORAN PENDAPATAN DERMAGA IV INFINITY PER-SHIFT";
        $data['penumpang'] = $penumpang;
        $data['kendaraan'] = $kendaraan;

        $this->load->view($this->_module.'/pdf2',$data);
    }

    function get_excel(){
       $port = $this->enc->decode($this->input->get('port'));
        $datefrom = $this->input->get('datefrom');
        $dateto = $this->input->get('dateto');
        $regu = $this->enc->decode($this->input->get('regu'));
        $petugas = $this->enc->decode($this->input->get('petugas'));
        $shift = $this->enc->decode($this->input->get('shift'));
        $ship_class = $this->enc->decode($this->input->get('ship_class'));

        $keterangan_report = "DRAFT";

        $status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift, $ship_class);

        if ($status_approve) {
            $keterangan_report = "APPROVED";
        }

        $pelabuhan = $this->input->get('pelabuhanku');
        $cabang = $this->input->get('cabang');
        $shiftku = $this->input->get('shiftku');
        $reguku = $this->input->get('reguku');
        $petugasku = $this->input->get('petugasku');

        $lintasan = $this->model->get_lintasan($port,$datefrom,$dateto,$ship_class);

        $lintasanku = "-";

        if ($lintasan) {
            $data_lintasan = $lintasan->row();
            $lintasanku = $data_lintasan->origin . " - " . $data_lintasan->destination;
        }

        $penumpang = $this->model->list_detail_passanger($port, $datefrom, $dateto, $regu, $ship_class, $shift, $petugas);
        $kendaraan = $this->model->list_detail_vehicle($port, $datefrom, $dateto, $regu, $ship_class, $shift, $petugas);

        $this->load->library('XLSExcel');
        $writer = new XLSXWriter();
        $filename = strtoupper("Dermaga_infinity_" . $pelabuhan . "_" . $datefrom . "_" . $dateto . ".xlsx");

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
            "No.",
            "JENIS TIKET",
            "TARIF (90% * Dmg)",
            "Produksi (Lbr)",
            "Pendapatan (Rp.)",
        );

        $produksi_penumpang = 0;
        $pendapatan_penumpang = 0;

        foreach ($penumpang as $key => $value) {
            $produksi_penumpang += $value->produksi;
            $pendapatan_penumpang += $value->pendapatan;

            $tunais[] = array(
                $key+1,
                $value->name,
                $value->harga,
                $value->produksi,
                $value->pendapatan,
            );
        }

        $produksi_kendaraan = 0;
        $pendapatan_kendaraan = 0;

        foreach ($kendaraan as $key => $value) {
            $produksi_kendaraan += $value->produksi;
            $pendapatan_kendaraan += $value->pendapatan;

            $cashlessis[] = array(
                $key+1,
                $value->golongan,
                $value->harga,
                $value->produksi,
                $value->pendapatan,
            );
        }

        $writer->writeSheetRow($sheet1, array("FORMULIR LAPORAN PENDAPATAN DERMAGA IV INFINITY PER-SHIFT"),$style_header);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("CABANG",$pelabuhan,"","SHIFT",$shiftku));
        $writer->writeSheetRow($sheet1, array("PELABUHAN",$pelabuhan,"","REGU",$reguku));
        $writer->writeSheetRow($sheet1, array("LINTASAN",$lintasanku,"","TANGGAL",format_date($datefrom) . " - " . format_date($dateto)));
        $writer->writeSheetRow($sheet1, array("PETUGAS",$petugasku,"","STATUS",$keterangan_report));
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, $judul_tunai, $styles1);
        $writer->writeSheetRow($sheet1, array("1. PENUMPANG"));

        if ($penumpang) {
            foreach($tunais as $row){
                $writer->writeSheetRow($sheet1, $row, $styles2);
            }
        }
        
        $writer->writeSheetRow($sheet1, array("Sub Total","","",$produksi_penumpang,$pendapatan_penumpang),$style_sub);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("2. KENDARAAN"));

        if ($kendaraan) {
            foreach($cashlessis as $row){
                $writer->writeSheetRow($sheet1, $row, $styles2);
            }
        }

        $writer->writeSheetRow($sheet1, array("Sub Total","","",$produksi_kendaraan,$pendapatan_kendaraan),$style_sub);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (Penumpang + Kendaraan)","","",$produksi_penumpang+$produksi_kendaraan,$pendapatan_penumpang+$pendapatan_kendaraan),$style_sub);
        $writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=4);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }

	function download_pdf($param){
        $this->global_model->checkAccessMenuAction($this->_module,'download_pdf');
        $param = $this->enc->decode($param);

        if (!$param) {
            redirect('/');
        } else {
            $param = explode('|', $param);
            $data['report_title'] = "FORMULIR LAPORAN PENDAPATAN DERMAGA IV INFINITY PER-SHIFT";
            $dock_id = $this->global_model->select_data("app.t_mtr_custom_param", "where param_name = 'infinity_dock_id' and status = 1 ")->row();
            $data['detail_passenger']   = $this->model->list_detail_passanger($param);
            $data['detail_vehicle']  = $this->model->list_detail_vehicle($param);   
            $data['param']  = $param;     

            $this->load->view($this->_module.'/pdf',$data);
        }
	}

    function download_excel($param)
    {
        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        $param = $this->enc->decode($param);

        if (!$param) {
            redirect('/');
        } else {
            $param = explode('|', $param);
            $excel_name = "Dermaga_infinity";
            $dock_id = $this->global_model->select_data("app.t_mtr_custom_param", "where param_name = 'infinity_dock_id' and status = 1 ")->row();

            $detail_passenger = $this->model->list_detail_passanger($param);
            $detail_vehicle = $this->model->list_detail_vehicle($param);

            $this->load->library('XLSExcel');
            $writer = new XLSXWriter();
            $filename = strtoupper("Dermaga_infinity_" . $param[0] . "_" . $param[9] . "_" . $param[6] . ".xlsx");
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

            $judul_tunai = array(
                    "NO.",
                    "JENIS TIKET",
                    "TARIF (90% * Dmg)",
                    "PRODUKSI (Lbr)",
                    "PENDAPATAN (Rp)",
                    "KETERANGAN",
            );

            $produksi_penumpang = 0;
            $pendapatan_penumpang = 0;

            foreach ($detail_passenger as $key => $value) {
                $produksi_penumpang += $value->ticket_count;
                $pendapatan_penumpang += $value->total_amount;

                $tunais[] = array(
                    $key+1,
                    $value->name,
                    $value->dock_fee,
                    $value->ticket_count,
                    $value->total_amount,
                    "",
                );
            }

            $produksi_kendaraan = 0;
            $pendapatan_kendaraan = 0;

            foreach ($detail_vehicle as $key => $value) {
                $produksi_kendaraan += $value->ticket_count;
                $pendapatan_kendaraan += $value->total_amount;

                $cashlessis[] = array(
                    $key+1,
                    $value->name,
                    $value->dock_fee,
                    $value->ticket_count,
                    $value->total_amount,
                    "",
                );
            }

            $writer->writeSheetRow($sheet1, array("FORMULIR LAPORAN PENDAPATAN DERMAGA IV INFINITY PER-SHIFT"),$styles_ha);
            $writer->writeSheetRow($sheet1, array(""));

            $writer->writeSheetRow($sheet1, array("CABANG",strtoupper($param[5]),"","SHIFT",strtoupper($param[9])));
            $writer->writeSheetRow($sheet1, array("PELABUHAN",strtoupper($param[6]),"","REGU",strtoupper($param[10])));
            $writer->writeSheetRow($sheet1, array("LINTASAN",strtoupper($param[7] . " - ".$param[8]),"","TANGGAL",format_date($param[0])));
            $writer->writeSheetRow($sheet1, array(""));

            $writer->writeSheetRow($sheet1, $judul_tunai, $styles1);

            $writer->writeSheetRow($sheet1, array("1. PENUMPANG"));

            if ($detail_passenger) {
                foreach($tunais as $row){
                    $writer->writeSheetRow($sheet1, $row, $styles2);
                }
            }

            $writer->writeSheetRow($sheet1, array("Sub Total","","",$produksi_penumpang,$pendapatan_penumpang,""),$style_sub);
            $writer->writeSheetRow($sheet1, array(""));

            $writer->writeSheetRow($sheet1, array("2. KENDARAAN"));

            if ($detail_vehicle) {
                foreach($cashlessis as $row){
                    $writer->writeSheetRow($sheet1, $row, $styles2);
                }
            }

            $writer->writeSheetRow($sheet1, array("Sub Total","","",$produksi_kendaraan,$pendapatan_kendaraan,""),$style_sub);
            $writer->writeSheetRow($sheet1, array(""));

            $writer->writeSheetRow($sheet1, array("TOTAL JUMLAH","","",$produksi_penumpang+$produksi_kendaraan,$pendapatan_penumpang+$pendapatan_kendaraan,""),$style_sub);
            $writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=5);

            $writer->writeToStdOut();
        }
    }
}