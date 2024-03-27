<?php
error_reporting(0);
class Rekap_muatan_perkapal extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('rekap_muatan_perkapal_model', 'rekap_muatan');
        $this->load->model('global_model');
		$this->_module   = 'laporan/rekap_muatan_perkapal';
		$this->load->library('Html2pdf');

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);        

	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()){
			$rows = $this->rekap_muatan->dataList();
			echo json_encode($rows);
			exit;
		}

        $ship = $this->global_model->select_data("app.t_mtr_ship","where status in (1) order by name asc")->result();
        $group_id = $this->session->userdata('group_id');

        if ($group_id == 11 or $group_id == 39) {
            $ship = $this->rekap_muatan->get_kapal_company($this->session->userdata('id'));
        }

        $ticketType[""]="SEMUA";
		$ticketType[$this->enc->encode(1)]="NORMAL";
		$ticketType[$this->enc->encode(3)]="MANUAL";        

		$data = array(
			'home' 			=> 'Beranda',
			'url_home' 		=> site_url('home'),
			'title' 		=> 'Rekap Muatan Per-kapal dan Per-Trip',
			'content' 		=> 'rekap_muatan_perkapal/index',
			// 'port'			=> $this->global_model->select_data("app.t_mtr_port","where status in (1) order by name asc")->result(),
			'port'			=> $this->getPort(),
            'ship'          => $ship,
			'download_pdf' 	=> checkBtnAccess($this->_module,'download_pdf'),
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
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $code  = $this->enc->decode($this->input->get('id'));

        $detail_trip= $this->rekap_muatan->detail_trip(" where ttob.schedule_code = '$code' ")->row();

        $substr= substr($detail_trip->schedule_code,0,1);
        $ticketTypeku= $substr=='M'?"MANUAL":"NORMAL";

       
        $data['home']     	= 'Home';
        $data['url_home'] 	= site_url('home');
        $data['title'] = 'FORMULIR LAPORAN REKAPITULASI MUATAN PER-KAPAL DAN PER-TRIP';

        // $data['detail_trip']   = $this->rekap_muatan->detail_trip(" where ttob.schedule_code = '$code' ")->row();
        $data['detail_trip']   = $detail_trip;
        $data['ticketTypeku']   = $ticketTypeku; 


        $data['detail_passenger']   = $this->rekap_muatan->list_detail_passanger($code);
   
        $data['detail_vehicle']  = $this->rekap_muatan->list_detail_vehicle($code);
        
        $data['dock_fare']   = $this->rekap_muatan->dock_fare(" where schedule.schedule_code = '$code' ")->row();
        $data['jasa_kepil']   = $this->rekap_muatan->get_kepil();
        $data['adm_fee']   = $this->rekap_muatan->adm_fee(" where param_name = 'adm_fee' and status = 1 ")->row();



        $this->load->view($this->_module.'/detail_modal',$data); 
    }

	function download_pdf(){
        if (!$this->input->get('id')) {
            redirect('/');
        } else {
            $code  = $this->enc->decode($this->input->get('id'));
            
            if ($this->rekap_muatan->detail_trip(" where ttob.schedule_code = '$code' ")->num_rows() > 0) {
                
                $detail_trip= $this->rekap_muatan->detail_trip(" where ttob.schedule_code = '$code' ")->row();
        
                $substr= substr($detail_trip->schedule_code,0,1);
                $ticketTypeku= $substr=='M'?"MANUAL":"NORMAL";

                $data['title'] = 'FORMULIR LAPORAN REKAPITULASI MUATAN PER-KAPAL DAN PER-TRIP';
                // $data['detail_trip']   = $this->rekap_muatan->detail_trip(" where ttob.schedule_code = '$code' ")->row();
                $data['detail_trip']   = $detail_trip;
                $data['ticketTypeku']   = $ticketTypeku; 

                $data['detail_passenger']   = $this->rekap_muatan->list_detail_passanger($code);           
                $data['detail_vehicle']  = $this->rekap_muatan->list_detail_vehicle($code);                
                $data['dock_fare']   = $this->rekap_muatan->dock_fare(" where schedule.schedule_code = '$code' ")->row();
                $data['jasa_kepil']   = $this->rekap_muatan->get_kepil();
                $data['adm_fee']   = $this->rekap_muatan->adm_fee(" where param_name = 'adm_fee' and status = 1 ")->row();
                $this->load->view($this->_module.'/pdf',$data);
            } else {
                redirect('/');
            }
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