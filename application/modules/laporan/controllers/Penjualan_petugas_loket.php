<?php

error_reporting(E_ALL ^ E_WARNING);

class Penjualan_petugas_loket extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('Html2pdf');
		$this->load->model('Penjualan_petugas_loket_model','m_penjualan_petugas_loket');
		$this->_module   = 'laporan/penjualan_petugas_loket';
		$this->_username  = $this->session->userdata('username');

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);
	}

	public function index(){
        checkUrlAccess(uri_string(),'view');
		$data = array(
			'home' => 'Beranda',
			'url_home' => site_url('home'),
			'title' => 'Laporan Penjualan Petugas Loket',
			'content' => 'penjualan_petugas_loket/index',
			'port'  => $this->m_penjualan_petugas_loket->select_data("app.t_mtr_port", "where status = 1")->result(),
			'petugas' => '',
			'loket' => '',
            'regu' => '',
			// 'petugas'  => $this->m_penjualan_petugas_loket->select_data("core.t_mtr_user", "where status = 1 and user_group_id = 4 order by first_name asc")->result(),
			'shift'  => $this->m_penjualan_petugas_loket->select_data("app.t_mtr_shift","where status=1 order by shift_name asc")->result(),
			// 'loket'  => $this->m_penjualan_petugas_loket->select_data("app.t_mtr_device_terminal","where status=1 and terminal_type = 1 order by terminal_name asc")->result(),
			// 'regu'  => $this->m_penjualan_petugas_loket->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
			'btnExcel' => generate_button_download($this->_module, 'download_excel',  site_url($this->_module.'/download_excel')),
            'btnExcelVehicle' => generate_button_download($this->_module, 'download_excel',  site_url($this->_module.'/download_excel_kendaraan')),
            'btnPdf' => generate_button_download($this->_module, 'download_pdf',  site_url($this->_module.'/download_pdf')),
            'btnPdfVehicle' => generate_button_download($this->_module, 'download_pdf',  site_url($this->_module.'/download_pdf_kendaraan'))
		);

		$this->load->view ('default', $data);
	}

	function get_data()
    {
        $port_id=$this->input->post("port");
        $port_decode=$this->enc->decode($port_id);

        empty($port_decode)?$kode=0:$kode=$port_decode;

        $petugas=array();
        $loket=array();
        $regu=array();

        $rowpetugas=$this->m_penjualan_petugas_loket->select_data("core.t_mtr_user", "where port_id='".$kode."' and status = 1 and user_group_id = 4 order by first_name asc")->result();
       	$rowloket=$this->m_penjualan_petugas_loket->select_data("app.t_mtr_device_terminal","where port_id='".$kode."' and status=1 and terminal_type = 1 order by terminal_name asc")->result();
        $rowregu=$this->m_penjualan_petugas_loket->select_data("core.t_mtr_team","where port_id='".$kode."' and status=1 order by team_name ")->result();
        
        foreach ($rowpetugas as $key => $value) {
            $value->id=$this->enc->encode($value->username); // decript id user
            $value->full_name=strtoupper($value->username." - ".$value->first_name." ".$value->last_name);
            $petugas[]=$value;
        }

        foreach ($rowloket as $key => $value) {
            $value->id=$this->enc->encode($value->terminal_code); // decript id user
            $value->terminal_name;
            $loket[]=$value;
        }

        foreach ($rowregu as $key => $value) {
            $value->id=$this->enc->encode($value->team_code);
            $value->team_name;
            $regu[]=$value;
        }

        $data['petugas']=$petugas;
        $data['loket']=$loket;
        $data['regu']=$regu;

        echo json_encode($data);

    }

	public function penumpang(){
		if($this->input->is_ajax_request()){
			$rows = $this->m_penjualan_petugas_loket->listPenumpang();
			echo json_encode($rows);
			exit;

		}
	}

	public function kendaraan(){
		if($this->input->is_ajax_request()){
			$rows = $this->m_penjualan_petugas_loket->listKendaraan();
			echo json_encode($rows);
			exit;

		}
	}

	public function loket_type(){
		$type = $this->input->post('type');
		$data['loket']=$this->m_penjualan_petugas_loket->get_loket($type)->result();
		echo json_encode(array(
			'code' => 1,
			'data' => $data['loket']
		));
	}

    function download($data, $service){
        if(!is_array($data)){
            show_404();
            return false;
        }

		$excel_name = "Penjualan_petugas_loket";

		$this->load->library('XLSExcel');
		$writer = new XLSXWriter();

        $post = (object) $this->input->post();
        $sheet1 = 'Sheet 1'; 
        $fill = '#f1f4f7';
        $color = '#000000';
        if($service == 'pnp'){
            $setTitleFile = 'Penjualan petugas loket - Pejalan Kaki_'.$post->dateFrom.'_'.$post->dateTo.'.xlsx';
        } else if($service == 'knd'){
            $setTitleFile = 'Penjualan petugas loket - Kendaraan_'.$post->dateFrom.'_'.$post->dateTo.'.xlsx';
        }

		$writer->setTitle($excel_name);
		$writer->setSubject($excel_name);
		$writer->setAuthor($excel_name);
		$writer->setCompany('ASDP Indonesia Ferry');
		$writer->setDescription($setTitleFile);
		$writer->setTempDir(sys_get_temp_dir());

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

		$styles_noborder= array(
			'font'=>'Arial',
			'font-size'=>10,
			'valign'=>'center',
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
		);

		$style_foot= array(
			'font'=>'Arial',
			'font-size'=>10,
			'font-style'=>'bold',
			'halign'=>'right',
			'valign'=>'right',
			'border'=>'left,right,top,bottom',
			'border-style'=> 'thin',
		);

        if($service == 'pnp'){
            $judul_penumpang = array(
                "NO.",
                "NAMA PETUGAS",
                "USERNAME",
                "LOKET",
                "KODE BOOKING",
                "NOMOR TIKET",
                "GOLONGAN",
                "METODE BAYAR",
                "KELAS",
                "TANGGAL SHIFT",
                "SHIFT",
                "REGU",
                "NAMA PENGGUNA JASA",
                "NO IDENTITAS",
                "NAMA KAPAL",
                "TANGGAL KLAIM",
                "TARIF (Rp)"
            );
        } else if($service == 'knd'){
            $judul_penumpang = array(
                "NO.",
                "NAMA PETUGAS",
                "USERNAME",
                "LOKET",
                "KODE BOOKING",
                "NOMOR TIKET",
                "GOLONGAN",
                "METODE BAYAR",
                "KELAS",
                "TANGGAL SHIFT",
                "SHIFT",
                "REGU",
                "NO POLISI",
                "NAMA PENGGUNA JASA",
                "NO IDENTITAS",
                "NAMA KAPAL",
                "TANGGAL KLAIM",
                "TARIF (Rp)"
            );
        }

		$excel = array();
        $total = 0;

        foreach ($data as $key => $row) {
            if($service == 'pnp'){
                $excel[] = array(
                    $row->number,
                    $row->first_name,
                    $row->username,
                    $row->loket,
                    $row->booking_code,
                    $row->ticket_number,
                    $row->golongan,
                    $row->payment_type,
                    $row->kelas,
                    $row->trans_date,
                    $row->shift,
                    $row->regu,
                    $row->customer_name,
                    "'".$row->id_number,
                    $row->ship,
                    $row->naik_kapal,
                    $row->tarif
                );
                $total += $row->tarif;
            } else if($service == 'knd'){
                $excel[] = array(
                    $row->number,
                    $row->first_name,
                    $row->username,
                    $row->loket,
                    $row->booking_code,
                    $row->ticket_number,
                    $row->golongan,
                    $row->payment_type,
                    $row->kelas,
                    $row->trans_date,
                    $row->shift,
                    $row->regu,
                    $row->plat,
                    $row->customer_name,
                    "'".$row->id_number,
                    $row->ship,
                    $row->naik_kapal,
                    $row->tarif
                );
                $total += $row->tarif;
            }

        }

        $writer->writeSheetRow($sheet1, array('PT. ASDP Indonesia Ferry'), $style_header);
        $writer->writeSheetRow($sheet1, array('LAPORAN PENJUALAN PETUGAS LOKET'), $style_header);        
        $writer->writeSheetRow($sheet1, array('Cabang : '.$post->port_name), $style_header);
        $writer->writeSheetRow($sheet1, array('Periode : '.format_date($post->dateFrom).' s/d '.format_date($post->dateTo)), $style_header);
        $writer->writeSheetRow($sheet1, array(''), $styles_noborder);

		$writer->writeSheetRow($sheet1, $judul_penumpang, $styles1);
		foreach($excel as $row){
			$writer->writeSheetRow($sheet1, $row, $styles2);
		}

        if($service == 'pnp'){
            $row_total = array(
                "","","","","","","","","","","","","","","Total","",$total
            );
        } else if($service == 'knd'){
            $row_total = array(
                "","","","","","","","","","","","","","","","Total","",$total
            );
        }

        $writer->writeSheetRow($sheet1, $row_total, $style_foot);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$setTitleFile.'"');
		header('Cache-Control: max-age=0');

		$writer->writeToStdOut();
	}

    function download_pdf(){
        ini_set('memory_limit', '10240M');
        $post = $this->input->post();

        if(!isset($post['dateFrom']) || 
            !isset($post['dateTo'])
        ){
            show_404();
            return false;
        }

        $post = (object) $this->input->post();
        $data['title'] = 'LAPORAN PENJUALAN PETUGAS LOKET';
        $data['data']  = $this->m_penjualan_petugas_loket->downloadPenumpang();
        $data['service'] = 'pnp';
        $data['date'] = $post;
        // print_r($data['data']);exit;
        $this->load->view($this->_module.'/pdf',$data);
    }

    function download_pdf_kendaraan(){
        ini_set('memory_limit', '10240M');
        $post = $this->input->post();

        if(!isset($post['dateFrom']) || 
            !isset($post['dateTo'])
        ){
            show_404();
            return false;
        }

        $post = (object) $this->input->post();
        $data['title'] = 'LAPORAN PENJUALAN PETUGAS LOKET';
        $data['data']  = $this->m_penjualan_petugas_loket->downloadKendaraan();
        $data['service'] = 'knd';
        $data['date'] = $post;
        // print_r($data['data']);exit;
        $this->load->view($this->_module.'/pdf',$data);
    }

    function download_excel(){
        ini_set('memory_limit', '-1');
        $post = $this->input->post();

        if(!isset($post['dateFrom']) || 
            !isset($post['dateTo'])
        ){
            show_404();
            return false;
        }

        $this->log_activitytxt->createLog($this->_username, $this->uri->uri_string(), 'Download report excel penjualan petugas loket', json_encode($post),'-');
        checkUrlAccess($this->_module,'download_excel');
        $post = (object) $this->input->post();

        $data = $this->m_penjualan_petugas_loket->downloadPenumpang();

        $this->download($data, 'pnp');
    }

    function download_excel_kendaraan(){
        ini_set('memory_limit', '-1');
        $post = $this->input->post();

        if(!isset($post['dateFrom']) || 
            !isset($post['dateTo'])
        ){
            show_404();
            return false;
        }

        $this->log_activitytxt->createLog($this->_username, $this->uri->uri_string(), 'Download report excel penjualan petugas loket', json_encode($post),'-');
        checkUrlAccess($this->_module,'download_excel');
        $post = (object) $this->input->post();

        $data = $this->m_penjualan_petugas_loket->downloadKendaraan();

        $this->download($data, 'knd');
    }

}
