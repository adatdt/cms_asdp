<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
// error_reporting(0);

class VehicleOverPaid extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('vehicleOverPaidModel','vehicle');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_booking';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction2/vehicleOverPaid';
        $this->load->library('Html2pdf');
        
        $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbAction=$this->load->database("dbAction",TRUE);         
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->vehicle->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->vehicle->get_identity_app();
        // port berdasarkan user

        $getRoute=array();
        if($get_identity==0)
        {
            if(!empty($this->session->userdata('port_id')) )
            {
                $port=$this->vehicle->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')."");
                $getRoute=$this->vehicle->getRoute($this->session->userdata('port_id'))->result();
            }
            else
            {
                $dataPort[""]="Pilih";
                $dataRoute[""]="Pilih";
                $port=$this->vehicle->select_data("app.t_mtr_port","where status not in (-5) order by name asc");
                
            }

        }
        else
        {
            $port=$this->vehicle->select_data("app.t_mtr_port","where id=".$get_identity."");
            $getRoute=$this->vehicle->getRoute($get_identity)->result();
        }


        $dataShipClass[""]="Pilih";
        $getShipClass=$this->vehicle->select_data("app.t_mtr_ship_class", " where status<>'-5' order by name asc")->result();


        foreach ($port->result() as $key => $value) {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        foreach ($getRoute as $key => $value) {
            $dataRoute[$this->enc->encode($value->id)]=strtoupper($value->route_name);
        }        

        foreach ($getShipClass as $key => $value) {
            $dataShipClass[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Golongan Kendaraan Lebih Bayar',
            'content'  => 'vehicleOverPaid/index',
            'service'  => $this->vehicle->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'port'=>$dataPort,
            'route'=>$dataRoute,
            'shipClass'=>$dataShipClass,
            'channel'=>$this->vehicle->get_channel(),
            'destination'=>$port=$this->vehicle->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result(),
            'team'=>$this->vehicle->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),
            'btn_pdf'=>checkBtnAccess($this->_module,'download_pdf'),
        );

		$this->load->view('default', $data);
	}


    public function getRoute()
    {
        $port=$this->enc->decode($this->input->post("port"));


        $dataRoute=array();

        if(!empty($port))
        {

            $route=$this->vehicle->getRoute($port)->result();

            foreach ($route as $key => $value) {
                $value->id=$this->enc->encode($value->id);
                $value->route_name=strtoupper($value->route_name);

                $dataRoute[]=$value;
            }
        }

        echo json_encode($dataRoute);
    }

    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");

        $data = $this->vehicle->download()->result();

        // print_r($data); exit;

        $file_name = 'Golongan Kendaraan Lebih  Bayar '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

        $header = array(
                            "NO"=>"string",
                            "KODE BOOKING"=>"string",
                            "NO TIKET"=>"string",
                            "GOLONGAN"=>"string",
                            "KELAS LAYANAN"=>"string",
                            "TARIF GOLONGAN (Rp)"=>"string",
                            "WAKTU PEMBAYARAN"=>"string",
                            "JADWAL KEBERANGKATAN"=>"string",
                            "LINTASAN DIPESAN"=>"string",
                            "STATUS TIKET"=>"string",
                            "GOLONGAN PADA PEMESANAN"=>"string",
                            "TARIF PEMESANAN/ LAMA"=>"string",
                            "GOLONGAN CHECKIN"=>"string",
                            "TARIF CHECKIN"=>"string",
                        );

        $no=1;
        foreach ($data as $key => $value) {

            $rows[] = array($no,
                            $value->booking_code,
                            $value->ticket_number,
                            $value->passanger_type_name,
                            $value->ship_class_name,
                            $value->fare,
                            $value->payment_date,
                            $value->keberangkatan,
                            $value->route_name,
                            $value->description,
                            $value->old_vehicle_class_name,
                            $value->old_fare,
                            $value->new_vehicle_class_name,
                            $value->new_fare,
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
        // $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");
        $port=$this->enc->decode($this->input->get("port"));

        $portName="";
        if (!empty($port))
        {
            $getDataPort=$this->vehicle->select_data("app.t_mtr_port", " where id='{$port}' ")->row();

            $portName=strtoupper($getDataPort->name);
        }

        $data['data'] = $this->vehicle->download()->result();
        $data['port'] = $portName;
        $data['departDateFrom'] = $dateFrom;
        $data['departDateTo'] = $dateTo;

        // print_r($data); exit;

        // echo "hai";
        $this->load->view('vehicleOverPaid/pdf',$data);

    }    

}