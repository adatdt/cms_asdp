<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Boarding_ticket_manual extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_boarding_ticket_manual','ticket_manual');
        $this->load->model('global_model');
        $this->load->library('PHPExcel');

        $this->_table    = 'app.t_mtr_port';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/boarding_ticket_manual';

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->ticket_manual->dataList();
            echo json_encode($rows);
            exit;
        }
        $sessionPortId=$this->session->userdata('port_id');
        $pilih="Pilih";

        $app_identity=$this->ticket_manual->select_data("app.t_mtr_identity_app", "")->row();

        $shift=$this->ticket_manual->select_data("app.t_mtr_shift", " where status='1' order by shift_name asc");
        $ship_class=$this->ticket_manual->select_data("app.t_mtr_ship_class", " where status='1' order by name asc");

        $data_shift[""]=$pilih;
        $data_ship_class[""]=$pilih;

        if($app_identity->port_id==0)
        {
            if(empty($sessionPortId))
            {
                $data_port[""]=$pilih;
                $port=$this->ticket_manual->select_data("app.t_mtr_port", " where status=1 order by id asc");
            }
            else
            {
                $port=$this->ticket_manual->select_data("app.t_mtr_port", " where id='".$sessionPortId."' ");
            }   
        }
        else
        {
            $port=$this->ticket_manual->select_data("app.t_mtr_port", " where id=".$app_identity->port_id);
        }


        foreach ($port->result() as $key => $value) {
            $data_port[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        foreach ($shift->result() as $key => $value) {
            $data_shift[$this->enc->encode($value->id)]=strtoupper($value->shift_name);
        }

        foreach ($ship_class->result() as $key => $value) {
            $data_ship_class[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $statusBoarding[""]="Pilih";
        $statusBoarding[$this->enc->encode(1)]="Boarding";
        $statusBoarding[$this->enc->encode(0)]="Belum Boarding";                

        $url=site_url($this->_module.'/add');
        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'port'=>$data_port,
            'shift'=>$data_shift,
            'ship_class'=>$data_ship_class,
            'title'    => 'tiket Manual Boarding',
            'content'  => 'boarding_ticket_manual/index',
            'statusBoarding'=>$statusBoarding,
            // 'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
             'btn_add'=>generate_button($this->_module, 'add','<button onclick="showModal2(\''.$url.'\')" class="btn btn-sm btn-warning" title="Tambah"><i class="fa fa-plus"></i> Tambah</button> ')
        );

		$this->load->view('default', $data);
	}

    public function get_data_vehicle()
    {
        $rows = $this->ticket_manual->get_data_vehicle();
        echo json_encode($rows); 
    }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');


        $app_identity=$this->ticket_manual->select_data("app.t_mtr_identity_app","")->row();
        $data_port[""]="Pilih";
        if($app_identity->port_id==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port=$this->ticket_manual->select_data("app.t_mtr_port", " where status=1 order by name asc");
            }
            else
            {
                $port=$this->ticket_manual->select_data("app.t_mtr_port", " where id=".$this->session->userdata('port_id'));  
            }
        }
        else
        {
            $port=$this->ticket_manual->select_data("app.t_mtr_port", " where id=".$app_identity->port_id);  
        }

        $data_service[""]="Pilih";
        $data_ship_class[""]="Pilih";
        
        $get_service=$this->ticket_manual->select_data(" app.t_mtr_service", " where status=1 order by name asc");
        $get_ship_class=$this->ticket_manual->select_data(" app.t_mtr_ship_class", " where status=1 order by name asc");

        foreach ($port->result() as $key => $value) {
            $enc_id=$this->enc->encode($value->id);
            $data_port[$enc_id]=strtoupper($value->name);
        }

        foreach ($get_service->result() as $key => $value) {
            $enc_id=$this->enc->encode($value->id);
            $data_service[$enc_id]=strtoupper($value->name);
        }

        foreach ($get_ship_class->result() as $key => $value) {
            $enc_id=$this->enc->encode($value->id);
            $data_ship_class[$enc_id]=strtoupper($value->name);
        }        

        // ambil parameter
        $getParam=$this->ticket_manual->select_data("app.t_mtr_custom_param", " where param_name='min_days_ticket_manual' and status=1 ")->row();

        $startDate=date('Y-m-d',strtotime(" -{$getParam->param_value} {$getParam->value_type} "));


        $data['title'] = 'Tambah Boarding Tiket Manual';
        $data['port'] = $data_port;
        $data["startDate"]=$startDate;
        $data['ship_class'] = $data_ship_class;
        $data['service'] = $data_service;
        $this->load->view($this->_module.'/add',$data);
    }


    public function action_add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $service_id=$this->enc->decode($this->input->post("service"));

        if(empty($service_id))
        {
            echo json_api(0,'Nama Pelabuhan '.$name.' Sudah Ada'); 
        }
        else
        {
            if($service_id==1)
            {
                $this->action_add_passanger();
            }
            else
            {
                $this->action_add_vehicle();   
            }
        }        

    }

    public function action_add_passanger(){

        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $date=date('Y-m-d H:i:s');
        $schedule_code=$this->input->post("schedule_code");
        $portSchedule=$this->input->post("portSchedule"); // portname dari form schedule
        $shipClassName=$this->input->post("ship_class"); // portname dari form schedule
        $service_id=$this->enc->decode($this->input->post("service"));
        $port_name=$this->input->post("port_name[]"); // portname dari data tampung
        $ticket_number=$this->input->post("ticket_number[]");
        $ticket_number_manual=$this->input->post("ticket_number_manual[]");

        /* validation */
        $this->form_validation->set_rules('schedule_code', 'Jadwal', 'trim|required');
        $this->form_validation->set_rules('service', 'Servis', 'trim|required');
        $this->form_validation->set_message('required','%s harus diisi!');

        $get_schedule=$this->ticket_manual->select_data("app.t_trx_schedule"," where schedule_code='{$schedule_code}' ");
        $getShipClass=$this->ticket_manual->select_data("app.t_mtr_ship_class", " where upper(name)=upper('{$shipClassName}') and status<>'-5' ")->row();

        $data_port=array();
        $err_data_port=array();
        $name_err_data_port=array();

        $data_ticket_number=array();
        $data_ticket_number_manual=array();

        // variable untuk menampung ticket yang tidak di temukan
        $err_data_ticket_number=array();
        $name_err_data_ticket_number=array();

        $err_ticket_number_status=array();
        $name_err_ticket_number_status=array();

        $errPortSchedule=array();
        $errShipClass=array();

        $port_temp_id="";

        if(!empty($port_name))
        {
            foreach ($port_name as $key => $value) {
                $search_port=$this->ticket_manual->select_data("app.t_mtr_port"," where upper(name)=upper('".trim($value)."') and status=1 ");

                // jika pelabuhan jadwal dengan pelabuhan data tiket yang di tampung berbeda
                if(strtoupper(trim($value))!=strtoupper($portSchedule))
                {
                    $errPortSchedule[]=1;
                }

                if($search_port->num_rows()<1)
                {
                    $err_data_port[]=1;
                    $name_err_data_port[]=$value;
                }
                else
                {
                    $data_port[]=$search_port->row()->id;
                    $port_temp_id=$search_port->row()->id;

                }

            }
        }

        // re key value ticket number
        if(!empty($ticket_number))
        {
            foreach ($ticket_number as $key => $value) {
                $data_ticket_number[]=$value;
            }
        }

        // re key value ticket number
        if(!empty($ticket_number_manual))
        {
            foreach ($ticket_number_manual as $key => $value) {
                $data_ticket_number_manual[]=$value;
            }
        }    

        // ceck ticket di ticket sobek penumpang
        if(!empty($data_ticket_number))
        {
            foreach ($data_ticket_number as $key => $value) {
                $check_ticket=$this->ticket_manual->select_data("app.t_trx_ticket_manual", " where port_id='{$port_temp_id}' and  ticket_number='{$value}'  and ticket_number_manual='".$data_ticket_number_manual[$key]."' ");

                // pengechekan jika ticket tidak ditemukan
                if($check_ticket->num_rows()<1)
                {
                    $err_data_ticket_number[]=1;
                }
                else
                {
                    // cecking ticket yang di perbolehkan hanya statusnya 4 dan 7 mencegah terjadinya expired 
                    $check_ticket_booking=$this->ticket_manual->select_data(" app.t_trx_booking_passanger", " where ticket_number='{$value}' and (status<>4 and status<>7 ) ");
                    if($check_ticket_booking->num_rows()>0)
                    {
                        $err_ticket_number_status[]=1;
                        $name_err_ticket_number_status[]=$value;
                    }
                }

                // pengecekan jika shipclassnya tidak sesuai dengan shipclass schedule
                if($getShipClass->id != $check_ticket->row()->ship_class)
                {
                    $errShipClass[]=1;
                }
                
            }
        }   
        // echo array_sum($err_data_ticket_number); exit        
        /* data post */

        $data=array();

        $get_boarding=$this->ticket_manual->get_pass_array($schedule_code)->row();

        if($get_boarding)
        {
            $getTerminalCode=$this->ticket_manual->getTerminalCode($get_boarding->port_id);
            $data_boarding_pass["terminal_code"]=$getTerminalCode;
        }

        $data_boarding_pass["port_id"]=$get_boarding->port_id;
        $data_boarding_pass["dock_id"]=$get_boarding->dock_id;
        $data_boarding_pass["schedule_date"]=$get_boarding->schedule_date;
        $data_boarding_pass["boarding_code"]=$get_boarding->boarding_code;
        $data_boarding_pass["ship_class"]=$get_boarding->ship_class;
        $data_boarding_pass['status']=1;
        $data_boarding_pass['created_on']=$date;
        $data_boarding_pass['boarding_date']=$get_schedule->row()->open_boarding_date;
        $data_boarding_pass['created_by']=$this->session->userdata("username");
        $data_boarding_pass['service_id']=1;

        $updt_booking_pass['status']=5;
        $updt_booking_pass['updated_on']=date('Y-m-d H:i:s');
        $updt_booking_pass['updated_by']=$this->session->userdata("username");

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        elseif(array_sum($err_data_ticket_number)>0)
        {
            echo $res =  json_api(0,'Ticket tidak ditemukan atau belum ada data yang ditampung'); 
        }

        else if(array_sum($err_data_port)>0)
        {
            echo $res =  json_api(0,'Pelabuhan Tidak ditemukan ');             
        }
        else if(empty($data_port) or empty($data_ticket_number) or empty($data_ticket_number_manual))
        {
            echo $res =  json_api(0,'Tidak ada data yang ditampung! ');      
        }
        else if (array_sum($err_ticket_number_status))
        {
            echo $res =  json_api(0,'Ticket boarding expired! ');     
        }
        else if(array_sum($errPortSchedule)>0)
        {
            echo $res =  json_api(0,'Pelabuhan jadwal dengan Pelabuhan Tiket tidak sama');     
        }
        else if(array_sum($errShipClass)>0)
        {
            echo $res =  json_api(0,'Tipe Kapal tidak sama  dengan jadwal dan data Tiket');     
        }
        else
        {

            $this->dbAction->trans_begin();
            foreach ($data_ticket_number as $key => $value) {
                
                $data_boarding_pass['ticket_number']=$value;
                $this->ticket_manual->insert_data("app.t_trx_boarding_passanger",$data_boarding_pass);

                // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
                $this->ticket_manual->update_data("app.t_trx_booking_passanger",$updt_booking_pass,"ticket_number='".$value."'");

                $temp_data[]=$data_boarding_pass;
            }

            $data=array("data_boarding"=>$temp_data,"update_booking_pass"=>$updt_booking_pass);  

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


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/boarding_ticket_manual/action_add_passanger';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_add_vehicle(){

        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $date=date('Y-m-d H:i:s');
        $schedule_code=$this->input->post("schedule_code");
        $portSchedule=$this->input->post("portSchedule"); // portname dari form schedule
        $shipClassName=$this->input->post("ship_class"); // portname dari form schedule
        $service_id=$this->enc->decode($this->input->post("service"));
        $ship_class_id=$this->enc->decode($this->input->post("ship_class_id"));
        $port_name=$this->input->post("port_name[]");
        $ticket_number=$this->input->post("ticket_number[]");
        $ticket_number_manual=$this->input->post("ticket_number_manual[]");

        /* validation */
        $this->form_validation->set_rules('schedule_code', 'Jadwal', 'trim|required');
        $this->form_validation->set_rules('service', 'Servis', 'trim|required');
        $this->form_validation->set_message('required','%s harus diisi!');

        $get_schedule=$this->ticket_manual->select_data("app.t_trx_schedule"," where schedule_code='{$schedule_code}' ");
        $getShipClass=$this->ticket_manual->select_data("app.t_mtr_ship_class", " where upper(name)=upper('{$shipClassName}') and status<>'-5' ")->row();

        $data_port=array();
        $err_data_port=array();
        $name_err_data_port=array();

        $data_ticket_number=array();
        $data_ticket_number_manual=array();

        // variable untuk menampung ticket yang tidak di temukan
        $err_data_ticket_number=array();
        $name_err_data_ticket_number=array();

        $err_ticket_number_status=array();
        $name_err_ticket_number_status=array();

        $errPortSchedule=array();
        $errShipClass=array();        

        $port_temp_id="";

        if(!empty($port_name))
        {
            foreach ($port_name as $key => $value) {
                $search_port=$this->ticket_manual->select_data("app.t_mtr_port"," where upper(name)=upper('".trim($value)."') and status=1 ");

                // jika pelabuhan jadwal dengan pelabuhan data tiket yang di tampung berbeda
                if(strtoupper(trim($value))!=strtoupper($portSchedule))
                {
                    $errPortSchedule[]=1;
                }                

                if($search_port->num_rows()<1)
                {
                    $err_data_port[]=1;
                    $name_err_data_port[]=$value;
                }
                else
                {
                    $data_port[]=$search_port->row()->id;
                    $port_temp_id=$search_port->row()->id;

                }

            }
        }

        // re key value ticket number
        if(!empty($ticket_number))
        {
            foreach ($ticket_number as $key => $value) {
                $data_ticket_number[]=$value;
            }
        }

        // re key value ticket number
        if(!empty($ticket_number_manual))
        {
            foreach ($ticket_number_manual as $key => $value) {
                $data_ticket_number_manual[]=$value;
            }
        }    

        // ceck ticket di ticket sobek penumpang
        if(!empty($data_ticket_number))
        {
            foreach ($data_ticket_number as $key => $value) {
                $check_ticket=$this->ticket_manual->select_data("app.t_trx_ticket_vehicle_manual", " where port_id='{$port_temp_id}' and  ticket_number='{$value}'  and ticket_number_manual='".$data_ticket_number_manual[$key]."' ");

                if($check_ticket->num_rows()<1)
                {
                    $err_data_ticket_number[]=1;
                }
                else
                {
                    // cecking ticket yang di perbolehkan hanya statusnya 4 dan 7 mencegah terjadinya expired 
                    $check_ticket_booking=$this->ticket_manual->select_data(" app.t_trx_booking_vehicle", " where ticket_number='{$value}' and (status<>4 and status<>7 ) ");
                    if($check_ticket_booking->num_rows()>0)
                    {
                        $err_ticket_number_status[]=1;
                        $name_err_ticket_number_status[]=$value;
                    }
                }

                // pengecekan jika shipclassnya tidak sesuai dengan shipclass schedule
                if($getShipClass->id != $check_ticket->row()->ship_class)
                {
                    $errShipClass[]=1;
                }                
            }
        }   
        // echo array_sum($err_data_ticket_number); exit        
        /* data post */

        $data=array();

        $get_boarding=$this->ticket_manual->get_pass_array($schedule_code)->row();

        if($get_boarding)
        {
            $getTerminalCode=$this->ticket_manual->getTerminalCode($get_boarding->port_id);
            $data_boarding_pass["terminal_code"]=$getTerminalCode;
            $data_boarding_vehicle["terminal_code"]=$getTerminalCode;   
        }

        $data_boarding_pass["port_id"]=$get_boarding->port_id;
        $data_boarding_pass["dock_id"]=$get_boarding->dock_id;
        $data_boarding_pass["schedule_date"]=$get_boarding->schedule_date;
        $data_boarding_pass["boarding_code"]=$get_boarding->boarding_code;
        $data_boarding_pass["ship_class"]=$get_boarding->ship_class;
        $data_boarding_pass['status']=1;
        $data_boarding_pass['created_on']=$date;
        $data_boarding_pass['boarding_date']=$get_schedule->row()->open_boarding_date;
        $data_boarding_pass['created_by']=$this->session->userdata("username");
        $data_boarding_pass['service_id']=2;// service 2 kendaraan

        $data_boarding_vehicle["port_id"]=$get_boarding->port_id;
        $data_boarding_vehicle["dock_id"]=$get_boarding->dock_id;
        $data_boarding_vehicle["schedule_date"]=$get_boarding->schedule_date;
        $data_boarding_vehicle["boarding_code"]=$get_boarding->boarding_code;
        $data_boarding_vehicle["ship_class"]=$get_boarding->ship_class;
        $data_boarding_vehicle['status']=1;
        $data_boarding_vehicle['created_on']=$date;
        $data_boarding_vehicle['boarding_date']=$get_schedule->row()->open_boarding_date;
        $data_boarding_vehicle['created_by']=$this->session->userdata("username");

        $updt_booking['status']=5;
        $updt_booking['updated_on']=date('Y-m-d H:i:s');
        $updt_booking['updated_by']=$this->session->userdata("username");

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        elseif(array_sum($err_data_ticket_number)>0)
        {
            echo $res =  json_api(0,'Ticket tidak ditemukan atau belum ada data yang ditampung'); 
        }

        else if(array_sum($err_data_port)>0)
        {
            echo $res =  json_api(0,'Pelabuhan Tidak ditemukan ');             
        }
        else if(empty($data_port) or empty($data_ticket_number) or empty($data_ticket_number_manual))
        {
            echo $res =  json_api(0,'Tidak ada data yang ditampung! ');      
        }
        else if (array_sum($err_ticket_number_status))
        {
            echo $res =  json_api(0,'Ticket boarding expired! ');     
        }
        else if(array_sum($errPortSchedule)>0)
        {
            echo $res =  json_api(0,'Pelabuhan jadwal dengan Pelabuhan Tiket tidak sama');     
        }
        else if(array_sum($errShipClass)>0)
        {
            echo $res =  json_api(0,'Tipe Kapal tidak sama  dengan jadwal dan data Tiket');     
        }        
        else
        {

            $this->dbAction->trans_begin();
            $tempDataPassanger=array();
            foreach ($data_ticket_number as $key => $value) 
            {
                
                $data_boarding_vehicle['ticket_number']=$value;

                $getBookingVehicle=$this->ticket_manual->getBookingVehicle($value)->result();

                // insert data passanger
                foreach ($getBookingVehicle as $ticketPassanger) {

                    $data_boarding_pass['ticket_number']=$ticketPassanger->ticket_number_passanger;

                    $this->ticket_manual->insert_data("app.t_trx_boarding_passanger",$data_boarding_pass);

                    // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
                    $this->ticket_manual->update_data("app.t_trx_booking_passanger",$updt_booking,"ticket_number='".$ticketPassanger->ticket_number_passanger."'"); 

                    $tempDataPassanger[]= $data_boarding_pass;                     
                }


                $this->ticket_manual->insert_data("app.t_trx_boarding_vehicle",$data_boarding_vehicle);

                // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
                $this->ticket_manual->update_data("app.t_trx_booking_vehicle",$updt_booking,"ticket_number='".$value."'");

                $temp_data[]=array("boarding_vehicle"=>$data_boarding_vehicle,"boarding_passanger"=>$tempDataPassanger);
            }

            $data=array("data_boarding"=>$temp_data,"update_booking_pass"=>$updt_booking);  

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


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/boarding_ticket_manual/action_add_vehicle';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    

    public function upload_excel()
    {
        validate_ajax();
        // $this->global_model->checkAccessMenuAction($this->_module,'import_excel');



        $service=$this->ticket_manual->select_data("app.t_mtr_service"," where status=1 order by name asc ")->result();
        $app_identity=$this->ticket_manual->select_data("app.t_mtr_identity_app","")->row();
        $data_port[""]="Pilih";
        if($app_identity->port_id==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port=$this->ticket_manual->select_data("app.t_mtr_port", " where status=1 order by name asc");
            }
            else
            {
                $port=$this->ticket_manual->select_data("app.t_mtr_port", " where id=".$this->session->userdata('port_id'));  
            }
        }
        else
        {
            $port=$this->ticket_manual->select_data("app.t_mtr_port", " where id=".$app_identity->port_id);  
        }

        foreach ($port->result() as $key => $value) {
            $data_port[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $dataService[""]="Pilih";
        foreach ($service as $key => $value) {
            $dataService[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $data['title'] = 'Tambah Tiket Manual Boarding ';
        $data['service'] = $dataService;
        $data['port'] =$data_port;
        $this->load->view($this->_module.'/upload_excel',$data);
    }

    public function action_import_excel(){
    
        // validate_ajax();
        // load excel
        $service=$this->enc->decode($this->input->post("service"));
        $port=$this->enc->decode($this->input->post("port"));

        $this->form_validation->set_rules('service', 'Servis', 'trim|required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|required');
        $this->form_validation->set_message('required','%s harus diisi!');

        $dataExcel=array();

        $emptyError[]=0;
        $emptyErrorName=array();

        $fileError[]=0; // untuk error file yang kosong atau bukan file xlsx

        try{

            $file = $_FILES['excel']['tmp_name'];
            $load = PHPExcel_IOFactory::load($file);         
            $max_row = $load->getActiveSheet(0)->getHighestRow()-7;
            $sheets = $load->getActiveSheet()->toArray(null,true,true,true);


            $i = 1;
            foreach ($sheets as $sheet) {

                // karena data yang di excel di mulai dari baris ke 8
                // maka jika $i lebih dari 1 data akan di masukan ke database
                if ($i > 7) {

                    $ticketBonggol= trim($sheet['A']);
                    $ticketBaru= trim($sheet['B']);
                    $layanan= trim($sheet['C']);
                    $namaKapal= trim($sheet['D']);
                    $scheduleCode= trim($sheet['E']);

                    if(empty($ticketBonggol))
                    {
                        $emptyError[]=1;
                        $emptyErrorName[]=" Tiket Manual Kolom A baris {$i} ";
                    }

                    if(empty($ticketBaru))
                    {
                        $emptyError[]=1;
                        $emptyErrorName[]=" Tiket Baru Kolom B baris {$i} ";   
                        $getService="";
                    }

                    if(empty($layanan))
                    {
                        $emptyError[]=1;
                        $emptyErrorName[]=" Kode Layanan C baris {$i} ";
                        
                    }
                    
                    if(empty($namaKapal))
                    {
                        $emptyError[]=1;
                        $emptyErrorName[]=" Kode Nama Kapal C baris {$i} ";
                        
                    }                    

                    if(empty($scheduleCode))
                    {
                        $emptyError[]=1;
                        $emptyErrorName[]=" Kode Jadwal Kolom E baris {$i} ";
                        
                    }


                    $dataExcel[]=array(
                        "ticketBonggol"=>strtoupper(trim($ticketBonggol)),
                        "ticketBaru"=>strtoupper(trim($ticketBaru)),
                        "scheduleCode"=>strtoupper(trim($scheduleCode)),
                        "service"=>$service,
                        "layanan"=>$layanan,
                        "shipName"=>$namaKapal,
                        "port"=>$port,
                        "baris"=>$i,
                    );
                                                                                                              
                    
                }

                $i++;

            }

            $allEmptyError[]=0;
            if($i<=8)
            {
                $allEmptyError[]=1;                
            }

            // echo $i; exit;
        }
        catch (Exception $e){
            $fileError[]=1;
        }

        $getErrorGatein[]=0;
        $getErrorGateinMessege=array();

        $scheduleError[]=0;
        $scheduleErrorMessege=array();

        $boardingError[]=0;
        $boardingErrorMessege=array();

        $duplicateInputError[]=0;
        $duplicateInputErrorMessege=array();

        $shipClassNotMatchErr[]=0;
        $shipClassNotMatchErrMessege=array();

        $scheduleParamError[]=0;
        $scheduleParamErrorMessege=array();    
        
        $errorShipName[]=0;
        $errorShipNameMessege=array();

        $errorShipAndSchedule[]=0;
        $errorShipAndScheduleMessege=array();

        $errorLayananName[]=0;
        $errorLayananNameMessege=array();

        $errorLayananAndSchedule[]=0;
        $errorLayananAndScheduleMessege=array();

                // ambil parameter
        $getParam=$this->ticket_manual->select_data("app.t_mtr_custom_param", " where param_name='min_days_ticket_manual' and status=1 ")->row();

        if(!empty($dataExcel))
        {
            foreach ($dataExcel as $key => $value) {
                
                if(!empty($value['ticketBonggol']) and 
                    !empty($value['ticketBaru']) and 
                    !empty($value['port']) and 
                    !empty($value['service']) and 
                    !empty($value['layanan']) and
                    !empty($value['shipName']))
                {

                    // $cariDataTicketMaster=$this->cariDataTicketMasterTicket($value['ticketBonggol'],$value['ticketBaru'],$value['port'],$value['service']);

                    // $explodeMaster=explode("|", $cariDataTicketMaster);
                    
                    /*
                    $explodeMaster[0]: kode ok 1/ kode tida ada data 0
                    $explodeMaster[1]: kode ship_class
                    */

                    // if($explodeMaster[0]<1) // jika tidak ada dtanya di trx tiket manual/ gatein
                    // {
                    //     $getErrorMaster[]=1;
                    //     $getErrorMasterMessege[]=" Nomer Bonggol {$value['ticketBonggol']} Nomor Tiket {$value['ticketBaru']} baris {$value['baris']} ";
                    // }


                    $cariDataTicketGateIn=$this->cariDataTicketGateIn($value['ticketBonggol'],$value['ticketBaru'],$value['port'],$value['service']);

                    $explode=explode("|", $cariDataTicketGateIn);
                    
                    /*
                    $explode[0]: kode ok 1/ kode tida ada data 0
                    $explode[1]: kode ship_class
                    */

                    if($explode[0]<1) // jika tidak ada dtanya di trx tiket manual/ gatein
                    {
                        $getErrorGatein[]=1;
                        $getErrorGateinMessege[]=" Nomer Bonggol {$value['ticketBonggol']} Nomor Tiket {$value['ticketBaru']} baris {$value['baris']} ";


                        $getShipClassTicket="";
                    }
                    else
                    {
                        $getShipClassTicket=$explode[1];
                    }

                    // check schedule
                    // $checkSchedule=$this->ticket_manual->select_data("app.t_trx_schedule", " where schedule_code='{$value['scheduleCode']}' and  sail_date is not null and port_id={$value['port']} ");
                    $checkSchedule=$this->ticket_manual->checkSheduleShipClass($value['scheduleCode'], $value['port']);
                    // echo $checkSchedule->num_rows(); exit;
                    if($checkSchedule->num_rows()<1)
                    {


                        $scheduleError[]=1;
                        $scheduleErrorMessege[]=" Kode Jadwal {$value['scheduleCode']} baris {$value['baris']} Kolom E ";

                                        
                    }
                    else
                    {
                        // check nama kapal
                        $checkNamaKapal= $this->ticket_manual->select_data('app.t_mtr_ship'," where upper(name)=upper('".($value['shipName'])."') ");

                        if($checkNamaKapal->num_rows()<1)
                        {
                            $errorShipName[]=1;
                            $errorShipNameMessege[]=" Nama Kapal {$value['shipName']} baris {$value['baris']} Kolom D "; // Nama Layanan Tidak ada
                        }
                        else
                        {
                            if($checkSchedule->row()->ship_id != $checkNamaKapal->row()->id)
                            {

                                $errorShipAndSchedule[]=1;
                                $errorShipAndScheduleMessege[]="  baris  {$value['baris']} Kolom D  "; // Nama Kapal Tidak sesuai dengan jadwal
                            }
                        }


                        $getScheduleShipClass=$checkSchedule->row()->ship_class;

                        if($getShipClassTicket != $getScheduleShipClass)
                        {
                            $shipClassNotMatchErr[]=1;
                            $shipClassNotMatchErrMessege[]=" baris {$value['baris']}  ";


                        }

                        // check layanan

                        $checkLayanan=$this->ticket_manual->select_data("app.t_mtr_ship_class"," where upper(name)=upper('".$value['layanan']."') ");

                        if($checkLayanan->num_rows()<1)
                        {
                            $errorLayananName[]=1;
                            $errorLayananNameMessege[]=" Nama Kapal {$value['shipName']} baris {$value['baris']} Kolom C "; // Nama Layanan Tidak ada
                        }
                        else
                        {
                            if($getScheduleShipClass != $checkLayanan->row()->id)
                            {

                                $errorLayananAndSchedule[]=1;
                                $errorLayananAndScheduleMessege[]="  baris  {$value['baris']} Kolom C  "; // Nama Layanan Tidak sesuai dengan jadwal
                            }
                        }

                        $startDate=date('Y-m-d',strtotime(" -{$getParam->param_value} {$getParam->value_type} "));

                        if($checkSchedule->row()->schedule_date< $startDate or $checkSchedule->row()->schedule_date > date("Y-m-d") )
                        {

                            $scheduleParamError[]=1;
                            $scheduleParamErrorMessege[]=" Kode Jadwal {$value['scheduleCode']} baris {$value['baris']} Kolom E ";

                        }

                    }

                    $checkDuplicationInput=$this->checkDuplicationInput($dataExcel,$value['ticketBonggol'],$value['ticketBaru']);
                    
                    if($checkDuplicationInput>0)
                    {
                        $duplicateInputError[]=1;
                        $duplicateInputErrorMessege[]=" No. bonggol {$value['ticketBonggol']} dan No. Tiket {$value['ticketBaru']} bari {$value['baris'] }" ;                        
                    }
                    
                    // check apakah tiketnya sudah melakukan boarding
                    if($value['service']==1)
                    {
                        $checkBoarding=$this->ticket_manual->select_data("app.t_trx_boarding_passanger"," where ticket_number='".$value['ticketBaru']."' and port_id={$port} and status=1 ");

                        if($checkBoarding->num_rows()>0)
                        {
                            $boardingError[]=1;
                            $boardingErrorMessege[]=" Tiket {$value['ticketBaru']} baris {$value['baris']} Kolom B ";
                        }

                        // print_r($checkBoarding->result()); exit;

                    }
                    else
                    {
                        $checkBoarding=$this->ticket_manual->select_data("app.t_trx_boarding_vehicle"," where ticket_number='".$value['ticketBaru']."' and port_id={$port} and status=1 ");

                        if($checkBoarding->num_rows()>0)
                        {
                            $boardingError[]=1;
                            $boardingErrorMessege[]=" Tiket {$value['ticketBaru']} baris {$value['baris']} Kolom B ";
                        }
                    }

                }

            }

        }


        // print_r($allEmptyError); exit;      

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else if(array_sum($allEmptyError)>0)
        {
            echo $res = json_api(0," File excel Kosong");   
        }
        else if(array_sum($fileError)>0)
        {
            echo $res=json_api(0, 'File Kosong Kosong atau format file salah');
        }
        else if(array_sum($emptyError)>0)
        {
            echo $res=json_api(0, implode(",<br> ", $emptyErrorName).' <br> Masih Ada yang Kosong');
        }
        else if(array_sum($duplicateInputError)>0)
        {
            echo $res=json_api(0, "Duplikasi Input <br>". implode(",<br> ", $duplicateInputErrorMessege).'');                 
        }
        else if(array_sum($boardingError)>0)
        {
            echo $res=json_api(0, implode(",<br> ", $boardingErrorMessege).' <br>Sudah Melakukan Boarding');
        }
        // else if(array_sum(getErrorMaster)>0)
        // {
        //     echo $res=json_api(0, implode(",<br> ", $getErrorGateinMessege).'<br>Tidak ada di master tiket');   
        // }        
        else if(array_sum($getErrorGatein)>0)
        {
            echo $res=json_api(0, implode(",<br> ", $getErrorGateinMessege).'<br>Data Tiket tidak sesuai dengan master atau ticket Belum Melakukan Gate in');
        }
        else if(array_sum($scheduleError)>0)
        {
            echo $res=json_api(0, "Jadwal Kapal Belum Melakukan Keberangkatan atau Kode jadwal kapal tidak ada <br>".implode(",<br> ", $scheduleErrorMessege).'<br> ');
        }    
        else if(array_sum($scheduleParamError)>0)
        {
            echo $res=json_api(0, "Jadwal harus {$getParam->param_value} Hari Kebelakang <br>".implode(",<br> ", $scheduleErrorMessege).'<br> ');
        }            
        else if(array_sum($shipClassNotMatchErr)>0)
        {
            echo $res=json_api(0, "Layanan Tiket tidak sesuai dengan layanan Jadwal Kapal ".implode(", <br> ", $shipClassNotMatchErrMessege).'<br>');
        } 
        else if(array_sum($errorShipName)>0)
        {
            echo $res=json_api(0, "Nama Kapal  ".implode(", <br> ", $errorShipNameMessege).'<br> Tidak ada');
        }         
        
        else if(array_sum($errorShipAndSchedule)>0)
        {
            echo $res=json_api(0, "Nama Kapal  ".implode(", <br> ", $errorShipAndScheduleMessege).'<br> Tidak sesuai dengan jadwal');
        }                 
        else if(array_sum($errorLayananName)>0)
        {
            echo $res=json_api(0, "Nama Layanan  ".implode(", <br> ", $errorLayananNameMessege).'<br> Tidak ada');
        }   
        
        else if(array_sum($errorLayananAndSchedule)>0)
        {
            echo $res=json_api(0, "Nama Layanan  ".implode(", <br> ", $errorLayananAndScheduleMessege).'<br> Tidak sesuai dengan jadwal');
        }                 
        else
        {


            $this->db->trans_begin();


            if($service==1)// jika service pnp
            {

                $data=array();

                foreach ($dataExcel as $key => $value) {

                    $getSchedule=$this->ticket_manual->checkSheduleShipClass($value['scheduleCode'], $value['port'])->row();

                    $get_boarding=$this->ticket_manual->get_pass_array($value['scheduleCode'])->row();

                    $getTerminalCode=$this->ticket_manual->getTerminalCode($get_boarding->port_id);
                    $data_boarding_pass["terminal_code"]=$getTerminalCode;
                    
                    $data_boarding_pass["port_id"]=$get_boarding->port_id;
                    $data_boarding_pass["dock_id"]=$get_boarding->dock_id;
                    $data_boarding_pass["schedule_date"]=$get_boarding->schedule_date;
                    $data_boarding_pass["boarding_code"]=$get_boarding->boarding_code;
                    $data_boarding_pass["ship_class"]=$get_boarding->ship_class;
                    $data_boarding_pass['status']=1;
                    $data_boarding_pass['created_on']=date("Y-m-d H:i:s");
                    $data_boarding_pass['boarding_date']=$getSchedule->open_boarding_date;
                    $data_boarding_pass['created_by']=$this->session->userdata("username");
                    $data_boarding_pass['service_id']=1;
                    $data_boarding_pass['ticket_number']=$value['ticketBaru'];

                    $updt_booking_pass['status']=5;
                    $updt_booking_pass['updated_on']=date('Y-m-d H:i:s');
                    $updt_booking_pass['updated_by']=$this->session->userdata("username");  

                    $data[]=$data_boarding_pass;

                    
                    $this->ticket_manual->insert_data("app.t_trx_boarding_passanger",$data_boarding_pass);

                    // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
                    $this->ticket_manual->update_data("app.t_trx_booking_passanger",$updt_booking_pass,"ticket_number='".$value['ticketBaru']."'");          
                }

            }
            else
            {
                foreach ($dataExcel as $key => $value) 
                {
                    $getSchedule=$this->ticket_manual->checkSheduleShipClass($value['scheduleCode'], $value['port'])->row();
                    
                    $get_boarding=$this->ticket_manual->get_pass_array($value['scheduleCode'])->row();

                    $getTerminalCode=$this->ticket_manual->getTerminalCode($get_boarding->port_id);
                    $data_boarding_pass["terminal_code"]=$getTerminalCode;

                    $data_boarding_pass["port_id"]=$get_boarding->port_id;
                    $data_boarding_pass["dock_id"]=$get_boarding->dock_id;
                    $data_boarding_pass["schedule_date"]=$get_boarding->schedule_date;
                    $data_boarding_pass["boarding_code"]=$get_boarding->boarding_code;
                    $data_boarding_pass["ship_class"]=$get_boarding->ship_class;
                    $data_boarding_pass['status']=1;
                    $data_boarding_pass['created_on']=date("Y-m-d H:i:s");
                    $data_boarding_pass['boarding_date']=$getSchedule->open_boarding_date;
                    $data_boarding_pass['created_by']=$this->session->userdata("username");
                    $data_boarding_pass['service_id']=2;// service 2 kendaraan
                    

                    $data_boarding_vehicle["terminal_code"]=$getTerminalCode;
                    $data_boarding_vehicle["port_id"]=$get_boarding->port_id;
                    $data_boarding_vehicle["dock_id"]=$get_boarding->dock_id;
                    $data_boarding_vehicle["schedule_date"]=$get_boarding->schedule_date;
                    $data_boarding_vehicle["boarding_code"]=$get_boarding->boarding_code;
                    $data_boarding_vehicle["ship_class"]=$get_boarding->ship_class;
                    $data_boarding_vehicle['status']=1;
                    $data_boarding_vehicle['created_on']=date("Y-m-d H:i:s");
                    $data_boarding_vehicle['boarding_date']=$getSchedule->open_boarding_date;
                    $data_boarding_vehicle['created_by']=$this->session->userdata("username");
                    $data_boarding_vehicle['ticket_number']=$value['ticketBaru'];

                    $updt_booking['status']=5;
                    $updt_booking['updated_on']=date('Y-m-d H:i:s');
                    $updt_booking['updated_by']=$this->session->userdata("username");
                    

                    $getBookingVehicle=$this->ticket_manual->getBookingVehicle($value['ticketBaru'])->result();

                    // insert data passanger
                    foreach ($getBookingVehicle as $ticketPassanger) {

                        $data_boarding_pass['ticket_number']=$ticketPassanger->ticket_number_passanger;

                        $this->ticket_manual->insert_data("app.t_trx_boarding_passanger",$data_boarding_pass);

                        // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
                        $this->ticket_manual->update_data("app.t_trx_booking_passanger",$updt_booking,"ticket_number='".$ticketPassanger->ticket_number_passanger."'"); 
           
                    }

                    $this->ticket_manual->insert_data("app.t_trx_boarding_vehicle",$data_boarding_vehicle);

                    // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
                    $this->ticket_manual->update_data("app.t_trx_booking_vehicle",$updt_booking,"ticket_number='".$value['ticketBaru']."'");                                    
                }
            }

            // print_r($data);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');
            }
        }


                /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/MasterTicketSobek/action_import_excel';
        $logMethod   = 'ADD';
        $logParam    = json_encode($dataExcel);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }

    public function action_import_excel_22092021(){
    
        // validate_ajax();
          // load excel
        $service=$this->enc->decode($this->input->post("service"));
        $port=$this->enc->decode($this->input->post("port"));

        $this->form_validation->set_rules('service', 'Servis', 'trim|required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|required');
        $this->form_validation->set_message('required','%s harus diisi!');



        $dataExcel=array();

        $emptyError[]=0;
        $emptyErrorName=array();

        $fileError[]=0; // untuk error file yang kosong atau bukan file xlsx

        try{

            $file = $_FILES['excel']['tmp_name'];
            $load = PHPExcel_IOFactory::load($file);         
            $max_row = $load->getActiveSheet(0)->getHighestRow()-7;
            $sheets = $load->getActiveSheet()->toArray(null,true,true,true);


            $i = 1;
            foreach ($sheets as $sheet) {

                // karena data yang di excel di mulai dari baris ke 8
                // maka jika $i lebih dari 1 data akan di masukan ke database
                if ($i > 7) {

                    $ticketBonggol= trim($sheet['A']);
                    $ticketBaru= trim($sheet['B']);
                    $scheduleCode= trim($sheet['C']);

                    if(empty($ticketBonggol))
                    {
                        $emptyError[]=1;
                        $emptyErrorName[]=" Tiket Manual Kolom A baris {$i} ";
                    }

                    if(empty($ticketBaru))
                    {
                        $emptyError[]=1;
                        $emptyErrorName[]=" Tiket Baru Kolom B baris {$i} ";   
                        $getService="";
                    }


                    if(empty($scheduleCode))
                    {
                        $emptyError[]=1;
                        $emptyErrorName[]=" Kode Jadwal Kolom C baris {$i} ";
                        
                    }

                    $dataExcel[]=array(
                        "ticketBonggol"=>strtoupper(trim($ticketBonggol)),
                        "ticketBaru"=>strtoupper(trim($ticketBaru)),
                        "scheduleCode"=>strtoupper(trim($scheduleCode)),
                        "service"=>$service,
                        "port"=>$port,
                        "baris"=>$i,
                    );
                                                                                                              
                    
                }

                $i++;

            }

            $allEmptyError[]=0;
            if($i<=8)
            {
                $allEmptyError[]=1;                
            }

            // echo $i; exit;
        }
        catch (Exception $e){
            $fileError[]=1;
        }

        $getErrorGatein[]=0;
        $getErrorGateinMessege=array();

        $scheduleError[]=0;
        $scheduleErrorMessege=array();

        $boardingError[]=0;
        $boardingErrorMessege=array();

        $duplicateInputError[]=0;
        $duplicateInputErrorMessege=array();

        $shipClassNotMatchErr[]=0;
        $shipClassNotMatchErrMessege=array();

        $scheduleParamError[]=0;
        $scheduleParamErrorMessege=array();        

                // ambil parameter
        $getParam=$this->ticket_manual->select_data("app.t_mtr_custom_param", " where param_name='min_days_ticket_manual' and status=1 ")->row();

        if(!empty($dataExcel))
        {
            foreach ($dataExcel as $key => $value) {
                
                if(!empty($value['ticketBonggol']) and !empty($value['ticketBaru']) and !empty($value['port']) and !empty($value['service']) )
                {

                    // $cariDataTicketMaster=$this->cariDataTicketMasterTicket($value['ticketBonggol'],$value['ticketBaru'],$value['port'],$value['service']);

                    // $explodeMaster=explode("|", $cariDataTicketMaster);
                    
                    /*
                    $explodeMaster[0]: kode ok 1/ kode tida ada data 0
                    $explodeMaster[1]: kode ship_class
                    */

                    // if($explodeMaster[0]<1) // jika tidak ada dtanya di trx tiket manual/ gatein
                    // {
                    //     $getErrorMaster[]=1;
                    //     $getErrorMasterMessege[]=" Nomer Bonggol {$value['ticketBonggol']} Nomor Tiket {$value['ticketBaru']} baris {$value['baris']} ";
                    // }




                    $cariDataTicketGateIn=$this->cariDataTicketGateIn($value['ticketBonggol'],$value['ticketBaru'],$value['port'],$value['service']);

                    $explode=explode("|", $cariDataTicketGateIn);
                    
                    /*
                    $explode[0]: kode ok 1/ kode tida ada data 0
                    $explode[1]: kode ship_class
                    */

                    if($explode[0]<1) // jika tidak ada dtanya di trx tiket manual/ gatein
                    {
                        $getErrorGatein[]=1;
                        $getErrorGateinMessege[]=" Nomer Bonggol {$value['ticketBonggol']} Nomor Tiket {$value['ticketBaru']} baris {$value['baris']} ";


                        $getShipClassTicket="";
                    }
                    else
                    {
                        $getShipClassTicket=$explode[1];
                    }

                    // check schedule
                    // $checkSchedule=$this->ticket_manual->select_data("app.t_trx_schedule", " where schedule_code='{$value['scheduleCode']}' and  sail_date is not null and port_id={$value['port']} ");
                    $checkSchedule=$this->ticket_manual->checkSheduleShipClass($value['scheduleCode'], $value['port']);
                    
                    if($checkSchedule->num_rows()<1)
                    {


                        $scheduleError[]=1;
                        $scheduleErrorMessege[]=" Kode Jadwal {$value['scheduleCode']} baris {$value['baris']} Kolom C ";

                                        
                    }
                    else
                    {

                        $getScheduleShipClass=$checkSchedule->row()->ship_class;

                        if($getShipClassTicket != $getScheduleShipClass)
                        {
                            $shipClassNotMatchErr[]=1;
                            $shipClassNotMatchErrMessege[]=" baris {$value['baris']}  ";


                        }

                        $startDate=date('Y-m-d',strtotime(" -{$getParam->param_value} {$getParam->value_type} "));

                        if($checkSchedule->row()->schedule_date< $startDate or $checkSchedule->row()->schedule_date > date("Y-m-d") )
                        {

                            $scheduleParamError[]=1;
                            $scheduleParamErrorMessege[]=" Kode Jadwal {$value['scheduleCode']} baris {$value['baris']} Kolom C ";

                        }

                    }

                    $checkDuplicationInput=$this->checkDuplicationInput($dataExcel,$value['ticketBonggol'],$value['ticketBaru']);
                    
                    if($checkDuplicationInput>0)
                    {
                        $duplicateInputError[]=1;
                        $duplicateInputErrorMessege[]=" No. bonggol {$value['ticketBonggol']} dan No. Tiket {$value['ticketBaru']} bari {$value['baris'] }" ;                        
                    }
                    
                    // check apakah tiketnya sudah melakukan boarding
                    if($value['service']==1)
                    {
                        $checkBoarding=$this->ticket_manual->select_data("app.t_trx_boarding_passanger"," where ticket_number='".$value['ticketBaru']."' and port_id={$port} and status=1 ");

                        if($checkBoarding->num_rows()>0)
                        {
                            $boardingError[]=1;
                            $boardingErrorMessege[]=" Tiket {$value['ticketBaru']} baris {$value['baris']} Kolom B ";
                        }

                        // print_r($checkBoarding->result()); exit;

                    }
                    else
                    {
                        $checkBoarding=$this->ticket_manual->select_data("app.t_trx_boarding_vehicle"," where ticket_number='".$value['ticketBaru']."' and port_id={$port} and status=1 ");

                        if($checkBoarding->num_rows()>0)
                        {
                            $boardingError[]=1;
                            $boardingErrorMessege[]=" Tiket {$value['ticketBaru']} baris {$value['baris']} Kolom B ";
                        }
                    }

                }
            }

        }


        // print_r($allEmptyError); exit;      

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else if(array_sum($allEmptyError)>0)
        {
            echo $res = json_api(0," File excel Kosong");   
        }
        else if(array_sum($fileError)>0)
        {
            echo $res=json_api(0, 'File Kosong Kosong atau format file salah');
        }
        else if(array_sum($emptyError)>0)
        {
            echo $res=json_api(0, implode(",<br> ", $emptyErrorName).' <br> Masih Ada yang Kosong');
        }
        else if(array_sum($duplicateInputError)>0)
        {
            echo $res=json_api(0, "Duplikasi Input <br>". implode(",<br> ", $duplicateInputErrorMessege).'');                 
        }
        else if(array_sum($boardingError)>0)
        {
            echo $res=json_api(0, implode(",<br> ", $boardingErrorMessege).' <br>Sudah Melakukan Boarding');
        }
        // else if(array_sum(getErrorMaster)>0)
        // {
        //     echo $res=json_api(0, implode(",<br> ", $getErrorGateinMessege).'<br>Tidak ada di master tiket');   
        // }        
        else if(array_sum($getErrorGatein)>0)
        {
            echo $res=json_api(0, implode(",<br> ", $getErrorGateinMessege).'<br>Data Tiket tidak sesuai dengan master atau ticket Belum Melakukan Gate in');
        }
        else if(array_sum($scheduleError)>0)
        {
            echo $res=json_api(0, "Jadwal Kapal Belum Melakukan Keberangkatan atau Kode jadwal kapal tidak ada <br>".implode(",<br> ", $scheduleErrorMessege).'<br> ');
        }    
        else if(array_sum($scheduleParamError)>0)
        {
            echo $res=json_api(0, "Jadwal harus {$getParam->param_value} Hari Kebelakang <br>".implode(",<br> ", $scheduleErrorMessege).'<br> ');
        }            
        else if(array_sum($shipClassNotMatchErr)>0)
        {
            echo $res=json_api(0, "Layanan Tiket tidak sesuai dengan layanan Jadwal Kapal ".implode(", <br> ", $shipClassNotMatchErrMessege).'<br>');
        }                            
        else
        {


            $this->db->trans_begin();


            if($service==1)// jika service pnp
            {

                $data=array();

                foreach ($dataExcel as $key => $value) {

                    $get_boarding=$this->ticket_manual->get_pass_array($value['scheduleCode'])->row();

                    $getTerminalCode=$this->ticket_manual->getTerminalCode($get_boarding->port_id);
                    $data_boarding_pass["terminal_code"]=$getTerminalCode;
                    
                    $data_boarding_pass["port_id"]=$get_boarding->port_id;
                    $data_boarding_pass["dock_id"]=$get_boarding->dock_id;
                    $data_boarding_pass["schedule_date"]=$get_boarding->schedule_date;
                    $data_boarding_pass["boarding_code"]=$get_boarding->boarding_code;
                    $data_boarding_pass["ship_class"]=$get_boarding->ship_class;
                    $data_boarding_pass['status']=1;
                    $data_boarding_pass['created_on']=date("Y-m-d H:i:s");
                    $data_boarding_pass['boarding_date']=$checkSchedule->row()->open_boarding_date;
                    $data_boarding_pass['created_by']=$this->session->userdata("username");
                    $data_boarding_pass['service_id']=1;
                    $data_boarding_pass['ticket_number']=$value['ticketBaru'];

                    $updt_booking_pass['status']=5;
                    $updt_booking_pass['updated_on']=date('Y-m-d H:i:s');
                    $updt_booking_pass['updated_by']=$this->session->userdata("username");  

                    $data[]=$data_boarding_pass;

                    
                    $this->ticket_manual->insert_data("app.t_trx_boarding_passanger",$data_boarding_pass);

                    // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
                    $this->ticket_manual->update_data("app.t_trx_booking_passanger",$updt_booking_pass,"ticket_number='".$value['ticketBaru']."'");          
                }

            }
            else
            {
                foreach ($dataExcel as $key => $value) 
                {
                    
                    $get_boarding=$this->ticket_manual->get_pass_array($value['scheduleCode'])->row();

                    $getTerminalCode=$this->ticket_manual->getTerminalCode($get_boarding->port_id);
                    $data_boarding_pass["terminal_code"]=$getTerminalCode;

                    $data_boarding_pass["port_id"]=$get_boarding->port_id;
                    $data_boarding_pass["dock_id"]=$get_boarding->dock_id;
                    $data_boarding_pass["schedule_date"]=$get_boarding->schedule_date;
                    $data_boarding_pass["boarding_code"]=$get_boarding->boarding_code;
                    $data_boarding_pass["ship_class"]=$get_boarding->ship_class;
                    $data_boarding_pass['status']=1;
                    $data_boarding_pass['created_on']=date("Y-m-d H:i:s");
                    $data_boarding_pass['boarding_date']=$checkSchedule->row()->open_boarding_date;
                    $data_boarding_pass['created_by']=$this->session->userdata("username");
                    $data_boarding_pass['service_id']=2;// service 2 kendaraan
                    

                    $data_boarding_vehicle["terminal_code"]=$getTerminalCode;
                    $data_boarding_vehicle["port_id"]=$get_boarding->port_id;
                    $data_boarding_vehicle["dock_id"]=$get_boarding->dock_id;
                    $data_boarding_vehicle["schedule_date"]=$get_boarding->schedule_date;
                    $data_boarding_vehicle["boarding_code"]=$get_boarding->boarding_code;
                    $data_boarding_vehicle["ship_class"]=$get_boarding->ship_class;
                    $data_boarding_vehicle['status']=1;
                    $data_boarding_vehicle['created_on']=date("Y-m-d H:i:s");
                    $data_boarding_vehicle['boarding_date']=$checkSchedule->row()->open_boarding_date;
                    $data_boarding_vehicle['created_by']=$this->session->userdata("username");
                    $data_boarding_vehicle['ticket_number']=$value['ticketBaru'];

                    $updt_booking['status']=5;
                    $updt_booking['updated_on']=date('Y-m-d H:i:s');
                    $updt_booking['updated_by']=$this->session->userdata("username");
                    

                    $getBookingVehicle=$this->ticket_manual->getBookingVehicle($value['ticketBaru'])->result();

                    // insert data passanger
                    foreach ($getBookingVehicle as $ticketPassanger) {

                        $data_boarding_pass['ticket_number']=$ticketPassanger->ticket_number_passanger;

                        $this->ticket_manual->insert_data("app.t_trx_boarding_passanger",$data_boarding_pass);

                        // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
                        $this->ticket_manual->update_data("app.t_trx_booking_passanger",$updt_booking,"ticket_number='".$ticketPassanger->ticket_number_passanger."'"); 
           
                    }

                    $this->ticket_manual->insert_data("app.t_trx_boarding_vehicle",$data_boarding_vehicle);

                    // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
                    $this->ticket_manual->update_data("app.t_trx_booking_vehicle",$updt_booking,"ticket_number='".$value['ticketBaru']."'");                                    
                }
            }

            // print_r($data);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');
            }
        }


                /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/MasterTicketSobek/action_import_excel';
        $logMethod   = 'ADD';
        $logParam    = json_encode($dataExcel);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }

    public function data_ticket()
    {
        $port=$this->enc->decode($this->input->post('port'));
        $service=$this->enc->decode($this->input->post('service'));
        $ship_class=$this->enc->decode($this->input->post('ship_class'));
        $trx_date=trim($this->input->post('trx_date'));
        $trx_date2=trim($this->input->post('trx_date2'));

        if (empty($service))
        {
            $res = array('code'=>0,'massege'=>'Servis Harus diisi');
        }        
        else if (empty($trx_date))
        {
            $res = array('code'=>0,'massege'=>'Data transaksi Harus diisi');
        }
        else if (empty($trx_date2))
        {
            $res = array('code'=>0,'massege'=>'Data transaksi Harus diisi');
        }        
        else if (empty($ship_class))
        {
            $res = array('code'=>0,'massege'=>'Tipe Kapal Harus diisi');
        }        
        else if(empty($port))
        {
            $res = array('code'=>0,'massege'=>'Pelabuhan Harus diisi');
        }
        else
        {
            // mencari data di ticket manual
            $data=array();
            $data_schedule=array();

            $get_schedule=$this->ticket_manual->get_schedule($trx_date, $ship_class, $port);

            if ($service==1)
                {
                    $search = $this->ticket_manual->manual_passanger(" where ( a.trx_date between '{$trx_date}' and '{$trx_date2}' )  and a.port_id='{$port}' and g.boarding_code is null and a.ship_class='{$ship_class}' ");
                    $code_service="pnp";
                }
            else
            {
                $search = $this->ticket_manual->manual_vehicle(" where ( a.trx_date between '{$trx_date}' and '{$trx_date2}' ) and a.port_id='{$port}'  and h.boarding_code is null and a.ship_class='{$ship_class}' ");
                $code_service="knd";
            }

                $data=$search->result();
                $data_schedule=$get_schedule->result();
                $res =array("code"=>1, 
                            "massage"=>"Berhasil",
                            "service"=>$code_service,
                            "data"=>$data,
                            // "schedule"=>$data_schedule,
                            "schedule"=>array(),// default saat awal serchjadi nol
                            "port"=>$this->enc->encode($port),
                            "trx_date"=>$trx_date,
                            ); 

        }

        echo json_encode($res);
    }

    public function getDock()
    {
        $portId=$this->enc->decode($this->input->post("id"));
        $getDock=$this->ticket_manual->select_data(" app.t_mtr_dock", " where port_id={$portId} and status=1 order by name asc")->result();

        foreach ($getDock as $key => $value) {
            
            $value->id=$this->enc->encode($value->id);   
        }

        echo json_encode($getDock);


    }

    public function get_schedule()
    {
        $schedule_date=$this->input->post("schedule_date");
        $schedule_date2=$this->input->post("schedule_date2");
        $ship_class=$this->enc->decode($this->input->post("ship_class"));
        $port_id=$this->enc->decode($this->input->post("port"));

        $categorySchedule=$this->input->post("categorySchedule");
        $dockSchedule=$this->enc->decode($this->input->post("dockSchedule"));
        
        $data_schedule=array();

        $get_schedule=$this->ticket_manual->get_schedule($schedule_date, $ship_class, $port_id, $categorySchedule, $dockSchedule, $schedule_date2);

        $data_schedule=$get_schedule->result();

        echo json_encode($data_schedule);        
    }

    public function cariDataTicketGateIn($bonggol, $newTicketNumber,$port,$service)
    {
        /*
            NOTE*
            jika sudah masuk ke table t_trx_tiket manual berrti tiket sudah melakukan gatein tiket manual
        */
        if($service==1)// jika servicenya penumpang
        {
            $checkData=$this->ticket_manual->select_data("app.t_trx_ticket_manual"," where upper(ticket_number)=upper('$newTicketNumber') and upper(ticket_number_manual)=upper('{$bonggol}') and  port_id=".$port);


            $return = $checkData->num_rows()>0?"1":"0";
            if($return==1)
            {
                $return .= "|".$checkData->row()->ship_class;
            }
        }
        else
        {
            $checkData=$this->ticket_manual->select_data("app.t_trx_ticket_vehicle_manual"," where upper(ticket_number)=upper('$newTicketNumber') and upper(ticket_number_manual)=upper('{$bonggol}') and  port_id=".$port);

            $return = $checkData->num_rows()>0?"1":"0";
            if($return==1)
            {
                $return .= "|".$checkData->row()->ship_class;
            }
        }

        return $return;

    }

     public function cariDataTicketMasterTicket($bonggol, $layanan,$port,$service)
     {

        /*
            NOTE*
            jika sudah masuk ke table t_trx_tiket manual berrti tiket sudah melakukan gatein tiket manual
        */
        if($service==1)// jika servicenya penumpang
        {
            $checkData=$this->ticket_manual->select_data("app.t_mtr_ticket_manual_passanger"," where upper(ticket_number)=upper('$bonggol') and ship_class='{$layanan}' and  port_id=".$port);


            $return = $checkData->num_rows()>0?"1":"0";
            if($return==1)
            {
                $return .= "|".$checkData->row()->ship_class;
            }
        }
        else
        {
            $checkData=$this->ticket_manual->select_data("app.t_mtr_ticket_manual_passanger_vehicle"," where upper(ticket_number)=upper('$bonggol') and ship_class='{$layanan}' and  port_id=".$port);

            $return = $checkData->num_rows()>0?"1":"0";
            if($return==1)
            {
                $return .= "|".$checkData->row()->ship_class;
            }
        }

        return $return;        

     }

    public function checkDuplicationInput($data,$bonggol,$ticketBaru)
    {

        
        $countData=0;
        foreach ($data as $value) {
            
            if($value['ticketBonggol']==$bonggol and $value['ticketBaru']==$ticketBaru)
            {
                $countData++;
            }

        }

        $return = $countData>1?"1":"0";

        return $return;
    }
}
