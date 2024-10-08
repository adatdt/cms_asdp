<?php
error_reporting(0);
class Rekap_muatan_perkapal_v2 extends MY_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('rekap_muatan_perkapal_v2_model', 'rekap_muatan');
        $this->load->model('global_model');
        $this->_module   = 'laporan/rekap_muatan_perkapal_v2';
        $this->load->library('Html2pdf');
        $this->report_name = "rekap_muatan_perkapal";
        $this->report_code = $this->global_model->get_report_code($this->report_name);


        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);      
    }

    public function index(){
        checkUrlAccess(uri_string(),'view');

        $ticketType[""]="SEMUA";
		$ticketType[$this->enc->encode(1)]="NORMAL";
		$ticketType[$this->enc->encode(3)]="MANUAL";
        
        
        $data = array(
            'home'          => 'Beranda',
            'url_home'      => site_url('home'),
            'title'         => 'Rekap Muatan Per-kapal dan Per-Trip V2',
            'content'       => 'rekap_muatan_perkapal_v2/index',
            // 'port'          => $this->global_model->select_data("app.t_mtr_port","where status in (1) order by name asc")->result(),
            'port'          => $this->getPort(),
            'ship_company'  => $this->rekap_muatan->getCompany(),            
            // 'ship'          => $this->global_model->select_data("app.t_mtr_ship","where status in (1) order by name asc")->result(),
            'ship'          => $this->rekap_muatan->getDefaultShip(),
            'shift'         => $this->global_model->select_data("app.t_mtr_shift","where status in (1)")->result(),
            'class'         => $this->global_model->select_data("app.t_mtr_ship_class","where status in (1)")->result(),
            'download_pdf'  => checkBtnAccess($this->_module,'download_pdf'),
            'download_excel'=> checkBtnAccess($this->_module,'download_excel'),
            'ticketType'=>$ticketType,
        );

        $this->load->view ('default', $data);
    }

    public function getPort()
    {
        $getApp = $this->global_model->select_data("app.t_mtr_identity_app"," ")->row();

        if($getApp->port_id==0 || $getApp->port_id== "" || $getApp->port_id == null )
        {
            $getUser =$this->session->userdata("username");
            $dataPortUser = $this->global_model->select_data("core.t_mtr_user","where status in (1)  and username='$getUser'  ")->row();
            if(!empty($dataPortUser->port_id))
            {           
                $pilih="";  
                $dataPort = $this->global_model->select_data("app.t_mtr_port","where id = ".$dataPortUser->port_id)->result();
            }
            else
            {
                $pilih="Semua";
                $dataPort = $this->global_model->select_data("app.t_mtr_port","where status in (1) order by name asc")->result();
            }
        }
        else
        {

                $pilih="";
                $dataPort = $this->global_model->select_data("app.t_mtr_port","where id =".$getApp->port_id." order by name asc")->result();

        }

        if($pilih == 'Semua')
        {
            $returnData[""] =$pilih;
        }

        foreach ($dataPort as $key => $value) {
            $returnData[$this->enc->encode($value->id)] = strtoupper($value->name)  ;
        }

        return $returnData;
    }    	

    public function detail(){
        validate_ajax();

        $port = $this->enc->decode($this->input->post('port'));
        $ship_company = $this->enc->decode($this->input->post('ship_company'));
        $ship_name = $this->enc->decode($this->input->post('ship'));
        $ship_class = $this->enc->decode($this->input->post('ship_class'));
        $dock_id = $this->enc->decode($this->input->post('dock'));
        $shift = $this->enc->decode($this->input->post('shift'));
        $ticketType = $this->enc->decode($this->input->post('ticketType'));


        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');

        $keterangan_report = "DRAFT";

        $status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift, $ship_class);

        if ($status_approve) {
            if ($status_approve == 2) {
                $keterangan_report = "APPROVED";
            }
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

        $dock_fareku = 0;

        $headerku = $this->rekap_muatan->headerku($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);
        $penumpang = $this->rekap_muatan->get_passanger($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift, $ticketType);
        $kendaraan = $this->rekap_muatan->get_vehicle($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift, $ticketType);
        $dock_fare = $this->rekap_muatan->dock_fare($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);
        $jumlah_trip = $this->rekap_muatan->jumlah_trip($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);

        $trip = $this->rekap_muatan->get_trip($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);
        $kepil = $this->rekap_muatan->get_kepil();

        if ($dock_fare) {
            $dock_fareku = $dock_fare;
        }

        $lintasan = "-";
        $jam = "-";

        if ($port != "") {
            $get_rute = $this->global_model->get_rute($port);
            $get_cabang = $this->global_model->get_branch($port, $ship_class_id);
            $get_regu = $this->global_model->get_team($port, $datefrom, $dateto, $shift_id);

            $implode_cabang = implode(', ', json_decode($get_cabang->branch_name));
            $cabang = ($implode_cabang != null) ? $implode_cabang : "-";

            $implode_regu = implode(', ', json_decode($get_regu->team_name));
            $regu = ($implode_regu != null) ? $implode_regu : "-";
            $lintasan = $get_rute->origin . " - " . $get_rute->destination;

            if(!empty($shift))
            {
                $getShiftTime=$this->dbView->query(" select * from app.t_mtr_shift_time where port_id='{$port}' and shift_id='{$shift}' and status=1 ")->row();

                $jam = $getShiftTime->shift_login." - ".$getShiftTime->shift_logout;
            }
        }


        if (!$penumpang && !$kendaraan) {

            $data =  array(
                'code' => 101,
                'message' => "Tidak ada data!",
            );

            echo json_encode($data);
            exit;
        }
        else{

            $data =  array(
                'code' => 200,
                'title' => "FORMULIR LAPORAN REKAPITULASI MUATAN PER-KAPAL DAN PER-TRIP",
                'status_approve' => $keterangan_report,
                'lintasan' => $lintasan,
                'jam' => $jam,
                'penumpang' => $penumpang,
                'kendaraan' => $kendaraan,
                'jumlah_trip' => $jumlah_trip,
                'jasa_kepil' => $trip * $kepil,
                'dock_fare' => $dock_fareku,
                'adm_fee' => $this->rekap_muatan->adm_fee(" where param_name = 'adm_fee' and status = 1 ")->row(),
            );

            echo json_encode($data);
            exit;
        }
    }

    function get_pdf()
    {
        $port = $this->enc->decode($this->input->get('port'));
        $ship_company = $this->enc->decode($this->input->get('ship_company'));
        $ship_name = $this->enc->decode($this->input->get('ship'));
        $ship_class = $this->enc->decode($this->input->get('ship_class'));
        $dock_id = $this->enc->decode($this->input->get('dock'));
        $shift = $this->enc->decode($this->input->get('shift'));

        $ticketType = $this->enc->decode($this->input->get('ticketType'));

        $datefrom = $this->input->get('datefrom');
        $dateto = $this->input->get('dateto');

        $keterangan_report = "DRAFT";

        $status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift, $ship_class);

        if ($status_approve) {
            if ($status_approve == 2) {
                $keterangan_report = "APPROVED";
            }
        }

        $kapal = $this->input->get('shipku');
        $pelabuhanku = $this->input->get('pelabuhanku');
        $perusahaan = $this->input->get('ship_companyku');
        $dermaga = $this->input->get('dockku');
        $shiftku = $this->input->get('shiftku');
        $ticketTypeku = $this->input->get('ticketTypeku');

        $dock_fareku = 0;

        $headerku = $this->rekap_muatan->headerku($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);
        $penumpang = $this->rekap_muatan->get_passanger($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift,$ticketType);
        $kendaraan = $this->rekap_muatan->get_vehicle($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift, $ticketType);
        $dock_fare = $this->rekap_muatan->dock_fare($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);
        $jumlah_trip = $this->rekap_muatan->jumlah_trip($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);

        $trip = $this->rekap_muatan->get_trip($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);
        $kepil = $this->rekap_muatan->get_kepil();

        if ($dock_fare) {
            $dock_fareku = $dock_fare;
        }

        $lintasan = "-";
        $jam = "-";

        if ($port != "") {
            $get_rute = $this->global_model->get_rute($port);
            $get_cabang = $this->global_model->get_branch($port, $ship_class_id);
            $get_regu = $this->global_model->get_team($port, $datefrom, $dateto, $shift_id);

            $implode_cabang = implode(', ', json_decode($get_cabang->branch_name));
            $cabang = ($implode_cabang != null) ? $implode_cabang : "-";

            $implode_regu = implode(', ', json_decode($get_regu->team_name));
            $regu = ($implode_regu != null) ? $implode_regu : "-";
            $lintasan = $get_rute->origin . " - " . $get_rute->destination;

            if(!empty($shift))
            {
                $getShiftTime=$this->dbView->query(" select * from app.t_mtr_shift_time where port_id='{$port}' and shift_id='{$shift}' and status=1 ")->row();

                $jam = $getShiftTime->shift_login." - ".$getShiftTime->shift_logout;
            }
        }

        $data['title'] = "FORMULIR LAPORAN REKAPITULASI MUATAN PER-KAPAL DAN PER-TRIP";
        $data['status_approve'] = $keterangan_report;

        $data['kapal'] = $kapal;
        $data['pelabuhan'] = $pelabuhanku;
        $data['lintasan'] = $lintasan;
        $data['dermaga'] = $dermaga;
        $data['shiftku'] = $shiftku;
        $data['ticketTypeku'] = $ticketTypeku;

        $data['lintasan'] = $lintasan;

        $data['perusahaan'] = $perusahaan;
        $data['lintasan'] = $lintasan;

        $data['jam'] = $jam;
        $data['penumpang'] = $penumpang;
        $data['kendaraan'] = $kendaraan;
        $data['jumlah_trip'] = $jumlah_trip;
        $data['jasa_kepil'] = $trip * $kepil;
        $data['dock_fare'] = $dock_fareku;
        $data['adm_fee'] = $this->rekap_muatan->adm_fee(" where param_name = 'adm_fee' and status = 1 ")->row();

        $this->load->view($this->_module.'/pdf',$data);
    }

    function get_excel()
    {
        $port = $this->enc->decode($this->input->get('port'));
        $ship_company = $this->enc->decode($this->input->get('ship_company'));
        $ship_name = $this->enc->decode($this->input->get('ship'));
        $ship_class = $this->enc->decode($this->input->get('ship_class'));
        $dock_id = $this->enc->decode($this->input->get('dock'));
        $shift = $this->enc->decode($this->input->get('shift'));
        $ticketType = $this->enc->decode($this->input->get('ticketType'));

        $datefrom = $this->input->get('datefrom');
        $dateto = $this->input->get('dateto');

        $keterangan_report = "DRAFT";

        $status_approve = $this->global_model->status_approve($this->report_code, $port, $datefrom, $dateto, $shift, $ship_class);

        if ($status_approve) {
            if ($status_approve == 2) {
                $keterangan_report = "APPROVED";
            }
        }

        $kapal = $this->input->get('shipku');
        $pelabuhanku = $this->input->get('pelabuhanku');
        $perusahaan = $this->input->get('ship_companyku');
        $dermaga = $this->input->get('dockku');
        $shiftku = $this->input->get('shiftku');
        $ticketTypeku = $this->input->get('ticketTypeku');

        $dock_fareku = 0;

        $headerku = $this->rekap_muatan->headerku($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);
        $penumpang = $this->rekap_muatan->get_passanger($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift,$ticketType);
        $kendaraan = $this->rekap_muatan->get_vehicle($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift,$ticketType);
        $dock_fare = $this->rekap_muatan->dock_fare($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);
        $jumlah_trip = $this->rekap_muatan->jumlah_trip($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);

        $trip = $this->rekap_muatan->get_trip($port, $ship_company, $ship_name, $ship_class, $datefrom, $dateto, $dock_id, $shift);
        $kepil = $this->rekap_muatan->get_kepil();

        if ($dock_fare) {
            $dock_fareku = $dock_fare;
        }

        $lintasan = "-";
        $jam = "-";


         if ($port != "") {
            $get_rute = $this->global_model->get_rute($port);
            $get_cabang = $this->global_model->get_branch($port, $ship_class_id);
            $get_regu = $this->global_model->get_team($port, $datefrom, $dateto, $shift_id);

            $implode_cabang = implode(', ', json_decode($get_cabang->branch_name));
            $cabang = ($implode_cabang != null) ? $implode_cabang : "-";

            $implode_regu = implode(', ', json_decode($get_regu->team_name));
            $regu = ($implode_regu != null) ? $implode_regu : "-";
            $lintasan = $get_rute->origin . " - " . $get_rute->destination;

            if(!empty($shift))
            {
                $getShiftTime=$this->dbView->query(" select * from app.t_mtr_shift_time where port_id='{$port}' and shift_id='{$shift}' and status=1 ")->row();

                $jam = $getShiftTime->shift_login." - ".$getShiftTime->shift_logout;
            }
        }


        $this->load->library('XLSExcel');
        $writer = new XLSXWriter();
        $filename = strtoupper("Rekap_muatan_perkapal_v2_" . $datefrom . "" . ".xlsx");

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

        $judul_penumpang = array(
            "NO.",
            "JENIS TIKET",
            "TARIF",
            "PRODUKSI",
            "PENDAPATAN",
            "KETERANGAN",
        );

        $produksi_penumpang = 0;
        $pendapatan_penumpang = 0;
        $totalAdmFee = 0;
        $totalAmount = 0;

        foreach ($penumpang as $key => $value) {
            $produksi_penumpang += $value->produksi;
            $pendapatan_penumpang += $value->pendapatan;
            $totalAdmFee += $value->adm_fee;
            $totalAmount += $value->pendapatan;

            $dpis[] = array(
                $key+1,
                $value->golongan,
                $value->harga,
                $value->produksi,
                $value->pendapatan,
                "",
            );
        }

        $produksi_kendaraan = 0;
        $pendapatan_kendaraan = 0;
        $totalAdmFeeVehicle = 0;
        $totalAmountVehicle = 0;

        foreach ($kendaraan as $key => $value) {
            $produksi_kendaraan += $value->produksi;
            $pendapatan_kendaraan += $value->pendapatan;
            $totalAdmFeeVehicle += $value->adm_fee;
            $totalAmountVehicle += $value->pendapatan;

            $dvis[] = array(
                $key+1,
                $value->golongan,
                $value->trip_fee,
                $value->produksi,
                $value->pendapatan,
                "",
            );
        }

        $writer->writeSheetRow($sheet1, array("FORMULIR LAPORAN REKAPITULASI MUATAN PER-KAPAL DAN PER-TRIP"),$style_header);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("NAMA KAPAL",$kapal,"","LINTASAN",$lintasan));
        $writer->writeSheetRow($sheet1, array("PERUSAHAAN",$perusahaan,"","DERMAGA",$dermaga));
        $writer->writeSheetRow($sheet1, array("CABANG",$pelabuhanku,"","TANGGAL",format_date($datefrom) . " - " . format_date($dateto)));
        $writer->writeSheetRow($sheet1, array("PELABUHAN",$pelabuhanku,"","JAM",$jam));
        $writer->writeSheetRow($sheet1, array("JUMLAH TRIP",$jumlah_trip,"","STATUS",$keterangan_report));
        // $writer->writeSheetRow($sheet1, array("TIPE TIKET",$ticketTypeku,"","","")); // belom deploy filter tipe tiket
        $writer->writeSheetRow($sheet1, array("","","","",""));
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("1. PENUMPANG"));
        $writer->writeSheetRow($sheet1, $judul_penumpang, $styles1);

        foreach($dpis as $row){
            $writer->writeSheetRow($sheet1, $row, $styles2);
        }

        $writer->writeSheetRow($sheet1, array("Sub Total","","",$produksi_penumpang,$pendapatan_penumpang,""),$style_sub);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->writeSheetRow($sheet1, array("2. KENDARAAN"));

        foreach($dvis as $row){
            $writer->writeSheetRow($sheet1, $row, $styles2);
        }

        $writer->writeSheetRow($sheet1, array("Sub Total","","",$produksi_kendaraan,$pendapatan_kendaraan,""),$style_sub);

        $writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (Penumpang + Kendaraan)","","",$produksi_penumpang+$produksi_kendaraan,$pendapatan_penumpang+$pendapatan_kendaraan,""),$style_sub);

        $writer->writeSheetRow($sheet1, array(""));
        $writer->writeSheetRow($sheet1, array("3. BEA JASA PELABUHAN"));
        $writer->writeSheetRow($sheet1, array("a. Jasa Adm. Tiket","","","",$adm_tiket = $totalAdmFee+$totalAdmFeeVehicle,""),$styles2);
        $writer->writeSheetRow($sheet1, array("b. Jasa Sandar","","","",$dock_fare,""),$styles2);
        $writer->writeSheetRow($sheet1, array("c. Jasa Kepil","","","",$trip * $kepil,""),$styles2);

        $writer->writeSheetRow($sheet1, array("Jumlah","","","",$bea = ($dock_fare + $adm_tiket+$trip*$kepil),""),$style_sub);
        $writer->writeSheetRow($sheet1, array(""));

        $writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=5);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }

    function get_dock($port_id="")
    {
        validate_ajax();
        $port_id = $this->enc->decode($port_id);

        if (!$port_id) {
            $option = '<option value="" selected>Semua</option>';
            echo $option;
        }else{
            $data = $this->rekap_muatan->get_dock($port_id);
            $option = '<option value="" selected>Semua</option>';
            foreach ($data as $key => $value) {
                $option .= '<option value="'.$this->enc->encode($value->id).'">'.$value->name.'</option>';
            }
            echo $option;
        }
    }

    function get_ship($company_id="")
    {
        validate_ajax();
        $company_id = $this->enc->decode($company_id);

        if (!$company_id) {
            $data = $this->global_model->select_data("app.t_mtr_ship","where status in (1) order by name asc")->result();
            $option = '<option value="" selected>Semua</option>';
            foreach ($data as $key => $value) {
                $option .= '<option value="'.$this->enc->encode($value->id).'">'.$value->name.'</option>';
            }
            echo $option;
        }else{
            $data = $this->rekap_muatan->get_ship($company_id);
            $option = '<option value="" selected>Semua</option>';
            foreach ($data as $key => $value) {
                $option .= '<option value="'.$this->enc->encode($value->id).'">'.$value->name.'</option>';
            }
            echo $option;
        }
    }

    function download_excel()
    {
        $code  = $this->enc->decode($this->input->get('id'));
        if (!$this->enc->decode($this->input->get('id'))) {
            redirect('/');
        }else{
            if ($this->rekap_muatan->detail_trip(" where ttob.schedule_code = '$code' ")->num_rows() > 0) {

                $excel_name = "Rekap_muatan_perkapal";

                $detail_trip = $this->rekap_muatan->detail_trip(" where ttob.schedule_code = '$code' ")->row();
                $detail_passenger = $this->rekap_muatan->list_detail_passanger($code);           
                $detail_vehicle = $this->rekap_muatan->list_detail_vehicle($code);                
                $dock_fare = $this->rekap_muatan->dock_fare(" where schedule.schedule_code = '$code' ")->row();
                $jasa_kepil = $this->rekap_muatan->get_kepil();
                $adm_fee = $this->rekap_muatan->adm_fee(" where param_name = 'adm_fee' and status = 1 ")->row();

                $this->load->library('XLSExcel');
                $writer = new XLSXWriter();
                $filename = strtoupper("Rekap_muatan_perkapal_" . $detail_trip->sail_date . ".xlsx");

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

                $judul_penumpang = array(
                    "NO.",
                    "JENIS TIKET",
                    "TARIF",
                    "PRODUKSI",
                    "PENDAPATAN",
                    "KETERANGAN",
                );

                $produksi_penumpang = 0;
                $pendapatan_penumpang = 0;
                $totalAdmFee = 0;
                $totalAmount = 0;

                foreach ($detail_passenger as $key => $value) {
                    $produksi_penumpang += $value->ticket_count;
                    $pendapatan_penumpang += $value->total_amount;
                    $totalAdmFee += $value->adm_fee;
                    $totalAmount += $value->total_amount;

                    $dpis[] = array(
                        $key+1,
                        $value->passanger_type_name,
                        $value->trip_fee,
                        $value->ticket_count,
                        $value->total_amount,
                        "",
                    );
                }

                $produksi_kendaraan = 0;
                $pendapatan_kendaraan = 0;
                $totalAdmFeeVehicle = 0;
                $totalAmountVehicle = 0;

                foreach ($detail_vehicle as $key => $value) {
                    $produksi_kendaraan += $value->ticket_count;
                    $pendapatan_kendaraan += $value->total_amount;
                    $totalAdmFeeVehicle += $value->adm_fee;
                    $totalAmountVehicle += $value->total_amount;

                    $dvis[] = array(
                        $key+1,
                        $value->vehicle_type_name,
                        $value->trip_fee,
                        $value->ticket_count,
                        $value->total_amount,
                        "",
                    );
                }

                $writer->writeSheetRow($sheet1, array("FORMULIR LAPORAN REKAPITULASI MUATAN PER-KAPAL DAN PER-TRIP"),$style_header);
                $writer->writeSheetRow($sheet1, array(""));

                $writer->writeSheetRow($sheet1, array("NAMA KAPAL",strtoupper($detail_trip->ship_name),"","LINTASAN",$detail_trip->trip." (".$detail_trip->ship_class.")"));
                $writer->writeSheetRow($sheet1, array("PERUSAHAAN",$detail_trip->company_name,"","DERMAGA",$detail_trip->dock_name));
                $writer->writeSheetRow($sheet1, array("CABANG",$detail_trip->port_name." (".$detail_trip->ship_class.")","","TANGGAL",format_date(($detail_trip->sail_date == '') ? date('Y-m-d H:i:s'):$detail_trip->sail_date) ));
                $writer->writeSheetRow($sheet1, array("PELABUHAN",$detail_trip->port_name,"","JAM",format_time(($detail_trip->sail_date == '') ? date('Y-m-d H:i:s'):$detail_trip->sail_date)));
                $writer->writeSheetRow($sheet1, array(""));

                $writer->writeSheetRow($sheet1, array("1. PENUMPANG"));
                $writer->writeSheetRow($sheet1, $judul_penumpang, $styles1);

                if ($detail_passenger) {
                    foreach($dpis as $row){
                        $writer->writeSheetRow($sheet1, $row, $styles2);
                    }
                }
                
                $writer->writeSheetRow($sheet1, array("Sub Total","","",$produksi_penumpang,$pendapatan_penumpang,""),$style_sub);
                $writer->writeSheetRow($sheet1, array(""));

                $writer->writeSheetRow($sheet1, array("2. KENDARAAN"));

                if ($detail_vehicle) {
                    foreach($dvis as $row){
                        $writer->writeSheetRow($sheet1, $row, $styles2);
                    }
                }

                $writer->writeSheetRow($sheet1, array("Sub Total","","",$produksi_kendaraan,$pendapatan_kendaraan,""),$style_sub);

                $writer->writeSheetRow($sheet1, array("TOTAL JUMLAH (Penumpang + Kendaraan)","","",$produksi_penumpang+$produksi_kendaraan,$pendapatan_penumpang+$pendapatan_kendaraan,""),$style_sub);

                $writer->writeSheetRow($sheet1, array(""));
                $writer->writeSheetRow($sheet1, array("3. BEA JASA PELABUHAN"));
                $writer->writeSheetRow($sheet1, array("a. Jasa Adm. Tiket","","","",$adm_tiket = $totalAdmFee+$totalAdmFeeVehicle,""),$styles2);
                $writer->writeSheetRow($sheet1, array("b. Jasa Sandar","","","",$dock_fare->dock_service,""),$styles2);
                $writer->writeSheetRow($sheet1, array("c. Jasa Kepil","","","",$jasa_kepil,""),$styles2);

                $writer->writeSheetRow($sheet1, array("Jumlah","","","",$bea = ($dock_fare->dock_service + $adm_tiket+$jasa_kepil),""),$style_sub);
                $writer->writeSheetRow($sheet1, array(""));

                $writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=5);

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'.$filename.'"');
                header('Cache-Control: max-age=0');
                $writer->writeToStdOut();
            }
        }
    }
}