<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
// error_reporting(0);

class DueDateExtends extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('DueDateExtendsModel','dueDate');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbAction=$this->load->database("dbAction",TRUE);
        $this->_table    = 'app.t_trx_invoice';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction2/dueDateExtends';
        $this->load->library('Html2pdf');
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->dueDate->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->dueDate->get_identity_app();
        // port berdasarkan user

        $getRoute=array();
        if($get_identity==0)
        {
            if(!empty($this->session->userdata('port_id')) )
            {
                $port=$this->dueDate->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')."");
                $getRoute=$this->dueDate->getRoute($this->session->userdata('port_id'))->result();
            }
            else
            {
                $dataPort[""]="Pilih";
                $dataRoute[""]="Pilih";
                $port=$this->dueDate->select_data("app.t_mtr_port","where status not in (-5) order by name asc");
                
            }

        }
        else
        {
            $port=$this->dueDate->select_data("app.t_mtr_port","where id=".$get_identity."");
            $getRoute=$this->dueDate->getRoute($get_identity)->result();
        }


        $dataShipClass[""]="Pilih";
        $getShipClass=$this->dueDate->select_data("app.t_mtr_ship_class", " where status<>'-5' order by name asc")->result();


        foreach ($port->result() as $key => $value) {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        foreach ($getRoute as $key => $value) {
            $dataRoute[$this->enc->encode($value->id)]=strtoupper($value->route_name);
        }        


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Perpanjang Due Date',
            'content'  => 'dueDateExtends/index',
            'port'=>$dataPort,
            'route'=>$dataRoute,
            'destination'=>$port=$this->dueDate->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result(),
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),
            'btn_pdf'=>checkBtnAccess($this->_module,'download_pdf'),
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Perpanjangan DueDate';

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        $transNumber=trim($this->input->post("transNumber"));
        $extends=trim($this->input->post("extends"));

        $this->form_validation->set_rules("transNumber", "Nomer Invoice", "required");
        $this->form_validation->set_rules("extends", "Jam Perpanjangan", "required|numeric");

        $this->form_validation->set_message("number", " %s Harus Berupa Angka");
        $this->form_validation->set_message("extends", " %s Tidak Boleh Kosong");



        $insertData=array(
            "extends_time"=>$extends,
            "trans_number"=>$transNumber,
            "created_on"=>date("Y-m-d H:i:s"),
            "created_by"=>$this->session->userdata("username"),
        );

        $updateData=array(
            "updated_on"=>date("Y-m-d H:i:s"),
            "updated_by"=>$this->session->userdata("username"),
        );        
        

        $checkInvoice=$this->dueDate->select_data("app.t_trx_invoice"," where trans_number='{$transNumber}' and status=1 ");

        if($this->form_validation->run()==false)
        {
             echo $res=json_api(0, validation_errors());
        }
        else if($checkInvoice->num_rows()<1)
        {
            echo $res=json_api(0,"Invalid Trans Number");   
        }
        else
        {
            $oldDueDate=$checkInvoice->row()->due_date;

            $newDueDate=date('Y-m-d H:i:s',strtotime("+".$extends."hour",strtotime($oldDueDate)));            
            $insertData['old_due_date']=$oldDueDate;
            $insertData['new_due_date']=$newDueDate;

            $updateData["due_date"]=$newDueDate;

            
            $this->dbAction->trans_begin();

            $this->dueDate->insert_data("app.t_trx_duedate_extends",$insertData);
            $this->dueDate->update_data("app.t_trx_invoice",$updateData," trans_number='{$transNumber}' ");

            if ($this->dbAction->trans_status() === FALSE)
            {
                $this->dbAction->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->dbAction->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');
            }
        }

        $data["inserData"]=$insertData;
        $data["updateData"]=$updateData;

        // print_r($data); exit;


        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction2/dueDateExtends/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }




    public function searchData()
    {
        $number=strtoupper(trim($this->input->post("number")));

        $getData=$this->dueDate->searchData($number);
        

        if(empty($number))
        {
            $data=array("code"=>0,"message"=>"Nomer Invoice/ Booking Kosong");                
        }
        else if($getData->num_rows()<1)
        {
            $data=array("code"=>0,"message"=>"Data tidak ditemukan");
        }
        else
        {
            $row=$getData->row();

            $myData["bookingCode"]=$row->booking_code;
            $myData["transNumber"]=$row->trans_number;
            $myData["shipClassName"]=$row->ship_class_name;
            $myData["serviceName"]=$row->service_name;
            $myData["customerName"]=$row->customer_name;
            $myData["dueDate"]=$row->due_date;
            $myData["routeName"]=strtoupper($row->route_name);


            $data=array("code"=>1,"data"=>$myData);
        }
        echo json_encode($data);
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

        print_r($data); exit;

        $file_name = 'Golongan Kendaraan Kurang Bayar '.$dateFrom.' s/d '.$dateTo;
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
        $this->load->view('vehicleUnderPaid/pdf',$data);

    }    

}