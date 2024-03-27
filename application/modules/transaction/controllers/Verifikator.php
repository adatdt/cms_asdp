<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Verifikator extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('VerifikatorModel', 'verifikator');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('Html2pdf');

        $this->_table    = 'app.t_trx_booking';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/verifikator';

        $this->dbView=checkReplication();
        $this->dbAction = $this->load->database("dbAction", TRUE);
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->verifikator->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity = $this->verifikator->get_identity_app();
        // port berdasarkan user

        $dataPort=array();
        if ($get_identity == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->verifikator->select_data("app.t_mtr_port", "where id=" . $this->session->userdata('port_id') . "")->result();
                $row_port = 1;
                
            } else {
                $port = $this->verifikator->select_data("app.t_mtr_port", "where status not in (-5) order by name asc")->result();
                $row_port = 0;
                $dataPort[""]="Pilih";
            }
        } else {
            $port = $this->verifikator->select_data("app.t_mtr_port", "where id=" . $get_identity . "")->result();
            $row_port = 1;

        }
        
        foreach ($port as $key => $value) {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        // layanan
        $shipClass=$this->verifikator->select_data("app.t_mtr_ship_class"," where status=1 order by name asc")->result();
        
        $dataShipClass[""]="Pilih";
        foreach ($shipClass as $key => $value) {
            $dataShipClass[$this->enc->encode($value->id)]=strtoupper($value->name);
        }


        // jenis pj
        $service=$this->verifikator->select_data("app.t_mtr_service"," where status=1 order by name asc")->result();
        
        $dataService[""]="Pilih";
        foreach ($service as $key => $value) {
            $dataService[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        // golongan kendaraan
        $vehicleClass=$this->verifikator->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc")->result();
        
        $dataVehicleClass[""]="Pilih";
        foreach ($vehicleClass as $key => $value) {
            $dataVehicleClass[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        // golongan Penumpang
        $passengerType=$this->verifikator->select_data("app.t_mtr_passanger_type"," where status=1 order by name asc")->result();
                
        $dataPassangerType[""]="Pilih";
        foreach ($passengerType as $key => $value) {
            $dataPassangerType[$this->enc->encode($value->id)]=strtoupper($value->name);
        }
                
        // status ticket
        // $statusTicket=$this->verifikator->select_data("app.t_mtr_status"," where tbl_name='t_trx_booking_passanger' order by description asc")->result();
        $statusTicket=$this->verifikator->statusTicket();
                
        $dataStatusTicket[""]="Pilih";
        foreach ($statusTicket as $key => $value) {
            // $dataStatusTicket[$this->enc->encode($value->id)]=strtoupper($value->description);
            $dataStatusTicket[$this->enc->encode($value->description)]=strtoupper($value->description);
        }
        
        // status Validasi
        $dataValidasi[""]="Pilih";
        $dataValidasi[$this->enc->encode("t")]="Approved";
        $dataValidasi[$this->enc->encode("f")]="Not Validated";

        // Jam
        $dataJam[""]="Pilih";
        for ($i=0; $i < 24 ; $i++) 
        { 
            $sprintf= sprintf("%02s", $i);
            $dataJam[$sprintf]=$sprintf.":00:00";
        }


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Verifikator',
            'content'  => 'verifikator/index',
            'port' => $dataPort,
            'shipClass'=>$dataShipClass,
            'service'=>$dataService,
            'vehicleClass'=>$dataVehicleClass,
            'passangerType'=>$dataPassangerType,
            'statusTicket'=>$dataStatusTicket,
            'dataJam'=>$dataJam,
            'dataValidasi'=>$dataValidasi,
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

        $data = $this->verifikator->download();
        
        $file_name = " Verifikator Tanggal Keberangkatan  {$dateFrom} s/d {$dateTo} ";
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

        $header = array(
            "NO"=>"string",
            "KODE BOOKING"=>"string",
            "NOMOR TIKET"=>"string",
            "PELABUHAN ASAL"=>"string",
            "JENIS PJ"=>"string",
            "LAYANAN"=>"string",
            "GOLONGAN KND"=>"string",
            "GOLONGAN PNP"=>"string",
            "NO POLISI"=>"string",
            "NAMA PENUMPANG"=>"string",
            "JENIS IDENTITAS"=>"string",
            "NO IDENTITAS"=>"string",
            "UMUR"=>"string",
            "JENIS KELAMIN"=>"string",
            "DOMISILI"=>"string",
            "TANGGAL MASUK PELABUHAN"=>"string",
            "JAM MASUK PELABUHAN"=>"string",
            "STATUS TIKET"=>"string",
            "WAKTU CHECKIN"=>"string",
            "WAKTU GATEIN"=>"string",
            "WAKTU BOARDING"=>"string",
            "STATUS VERIFIKATOR"=>"string",
            "USER VERIFIKATOR"=>"string",
            "WAKTU VERIFIKASI"=>"string",
            "PERANGKAT VERIFIKASI"=>"string",
            "ID PERANGKAT "=>"string",
        );

        $no=1;

        foreach ($data as $key => $value) {
            $rows[] = array($no,
                        $value->booking_code,
                        $value->ticket_number,
                        $value->origin_name,
                        $value->service_name,
                        $value->ship_class_name,
                        $value->golongan_knd,
                        $value->golongan_pnp,                        
                        $value->plat_no,
                        $value->passanger_name,
                        $value->id_type_name,
                        $value->no_identitas,
                        $value->age,
                        $value->gender,
                        $value->city,
                        $value->tanggal_masuk_pelabuhan,
                        $value->depart_time_start,
                        $value->status_ticket,
                        $value->checkin_date,
                        $value->gatein_date,
                        $value->boarding_date,
                        $value->approved_status,
                        $value->user_verified, 
                        $value->approved_date, 
                        $value->terminal_name, 
                        $value->terminal_code, 
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
 
        $getData = $this->verifikator->download();
        $data["data"]=$getData;
        $data["dateFrom"]=trim($this->input->get('dateFrom'));
        $data["dateTo"]=trim($this->input->get('dateTo'));

        // print_r($data['data']); exit;
        $this->load->view($this->_module . '/pdf', $data);

    }    

}
