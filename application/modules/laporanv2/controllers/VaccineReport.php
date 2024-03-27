<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class VaccineReport extends MY_Controller{
  public function __construct(){
    parent::__construct();

        logged_in();
        $this->load->model('VaccineReportModel','vaccine');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('Html2pdf');

        $this->_table    = 'app.t_mtr_ads_display';
        $this->_tableDetail    = 'app.t_mtr_ads_display_detail';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'laporanv2/vaccineReport';

        $this->dbView=checkReplication();       
        $this->dbAction = $this->load->database("dbAction", TRUE);
  }

    public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->vaccine->dataList();
            echo json_encode($rows);
            exit;
        }


        $port=$this->vaccine->select_data("app.t_mtr_port", " where status <>'-5' order by name asc")->result();

        $dataPort[""]="Pilih";
        foreach ($port as $key => $value) 
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);    
        }

        $layanan=$this->vaccine->select_data("app.t_mtr_ship_class", " where status <>'-5' order by name asc")->result();
        $dataLayanan[""]="Pilih";
        foreach ($layanan as $key => $value) 
        {
            $dataLayanan[$this->enc->encode($value->id)]=strtoupper($value->name);    
        }

        $service=$this->vaccine->select_data("app.t_mtr_service", " where status <>'-5' order by name asc")->result();       
        $dataService[""]="Pilih";
        foreach ($service as $key => $value) 
        {
            $dataService[$this->enc->encode($value->id)]=strtoupper($value->name);    
        }

        $vehicleClass=$this->vaccine->select_data("app.t_mtr_vehicle_class", " where status <>'-5' order by name asc")->result();       
        $dataVehicleClass[""]="Pilih";
        foreach ($vehicleClass as $key => $value) 
        {
            $dataVehicleClass[$this->enc->encode($value->id)]=strtoupper($value->name);    
        }        

        $passangerType=$this->vaccine->select_data("app.t_mtr_passanger_type", " where status <>'-5' order by name asc")->result();       
        $dataPassangerType[""]="Pilih";
        foreach ($passangerType as $key => $value) 
        {
            $dataPassangerType[$this->enc->encode($value->id)]=strtoupper($value->name);    
        }                


        $masterStatus=$this->getMasterStatus();  

        $dataMasterStatus[""]="Pilih";
        foreach ($masterStatus as $key => $value) 
        {
            $dataMasterStatus[$this->enc->encode($value)]=strtoupper($value);    
        }                



        $jam[""]="Pilih";
        foreach ($this->jam() as $key => $value) 
        {
            $jam[$value]=$value;
            
        }

        $menit=array();
        foreach ($this->menit() as $key => $value) 
        {
            $menit[$value]=$value;
            
        }        


        $statusValid[""]="Pilih";
        $statusValid[$this->enc->encode("not validated")]="NOT VALIDATED";
        $statusValid[$this->enc->encode("validated")]="VALIDATED";



        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Laporan Vaksin',
            'content'  => 'vaccineReport/index',
            "port" =>$dataPort, 
            "jam"=>$jam,
            "menit"=>$menit,
            "service"=>$dataService,
            "statusValid"=>$statusValid,
            "shipClass"=>$dataLayanan,
            "masterStatus"=>$dataMasterStatus,
            "vehicleClass"=>$dataVehicleClass,
            "passangerType"=>$dataPassangerType,
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'btn_excel'  => generate_button($this->_module,'download_excel', '<button class="btn btn-sm btn-warning" title="excel" id="download_excel">Excel</button>'),
            'btn_pdf'  => generate_button($this->_module, 'download_pdf', '<button class="btn btn-sm btn-warning" title="PDF" id="download_pdf">PDF</button>'),

        );

        $this->load->view('default', $data);
    }

    public function download_excel()
    {
        // $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");

        $data = $this->vaccine->download();
        
        $file_name = "Validasi Vaksin keberangkatan Tanggal  {$dateFrom} s/d {$dateTo} ";
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

        $header = array(
                "NO"=>"string",
                "KODE BOOKING"=>"string",
                "NOMOR TIKET "=>"string",
                "PELABUHAN"=>"string",
                "JENIS PJ"=>"string",
                "LAYANAN"=>"string",
                "GOLONGAN KND"=>"string",
                "NO POLISI"=>"string",
                "JENIS PNP"=>"string",
                "NAMA"=>"string",
                "JENIS ID"=>"string",
                "NOMOR ID"=>"string",
                "USIA"=>"string",
                "JENIS KELAMIN"=>"string",
                "ALAMAT"=>"string",
                "TAMBAH MANIFEST"=>"string",
                "TANGGAL BERANGKAT"=>"string",
                "JAM BERANGKAT"=>"string",
                "STATUS"=>"string",
                "NAMA KAPAL"=>"string",
                "STATUS VALIDASI"=>"string",
                "TES COVID"=>"string",
                "KETERANGAN"=>"string",
        );

        $no=1;

        foreach ($data as $key => $value) {
            $rows[] = array($no,
                            $value->booking_code,
                            $value->ticket_number,
                            $value->port_name,
                            $value->service_name,
                            $value->ship_class_name,
                            $value->vehicle_class_name,
                            $value->plat_no,
                            $value->passanger_type_name,
                            $value->name,
                            $value->type_id_name,
                            $value->id_number,
                            $value->age,
                            $value->gender,
                            $value->city,
                            $value->add_manifest_channel,
                            $value->depart_date,
                            $value->depart_time_start,
                            $value->description,
                            $value->ship_name,
                            $value->vaccine,
                            htmlspecialchars(strip_tags($value->testCovid)),
                            htmlspecialchars(strip_tags($value->reason)),
                        );
            $no++;
        }


        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header,$styles1);

        foreach($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }    

    public function download_pdf()
    {
 
        $getData = $this->vaccine->download();
        $data["data"]=$getData;
        $data["dateFrom"]=trim($this->input->get('dateFrom'));
        $data["dateTo"]=trim($this->input->get('dateTo'));

        // print_r($data['data']); exit;
        $this->load->view($this->_module . '/pdf', $data);

    }    

    public function jam()
    {
        $jam=array();
        for ($i=0; $i <24 ; $i++) { 
            $jam[]=sprintf("%02s", $i);
        }

        return $jam;
    }

    public function menit()
    {
        $menit=array();
        for ($i=1; $i <60 ; $i++) { 
            $menit[]=sprintf("%02s", $i);
        }

        return $menit;
    }   

    public function getMasterStatus()
    {
        $masterStatus=$this->vaccine->select_data("app.t_mtr_status", " where tbl_name='t_trx_booking_passanger' order by description asc")->result();

        $data=array() ;

        foreach ($masterStatus as $key => $value) 
        {
            $data[]=$value->description;
        } 
 
        return array_unique($data);
    } 



}