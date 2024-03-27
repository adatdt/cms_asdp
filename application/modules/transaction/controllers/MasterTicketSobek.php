<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class MasterTicketSobek extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('MasterTicketSobekModel','ticket');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('PHPExcel');

        $this->_table    = 'app.t_mtr_dock';
        $this->_username = $this->session->userdata('username');
        $this->_module = 'transaction/masterTicketSobek';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->ticket->dataList();
            echo json_encode($rows);
            exit;
        }

        $port=$this->ticket->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();
        $layanan=$this->ticket->select_data("app.t_mtr_ship_class"," where status=1 order by name asc ")->result();
        $service=$this->ticket->select_data("app.t_mtr_service"," where status=1 order by name asc ")->result();
        $vehicleClass=$this->ticket->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc ")->result();
        $passangerType=$this->ticket->select_data("app.t_mtr_passanger_type"," where status=1 order by name asc ")->result();

        $dataPort[""]="Pilih";
        $dataLayanan[""]="Pilih";
        $dataService[""]="Pilih";
        $dataVehicleClass[""]="Pilih";
        $dataPassangerType[""]="Pilih";

        foreach ($port as $key => $value) {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        foreach ($service as $key => $value) {
            $dataService[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        foreach ($layanan as $key => $value) {
            $dataLayanan[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        foreach ($vehicleClass as $key => $value) {
            // $dataVehicleClass[$this->enc->encode($value->id)]=strtoupper($value->name);
            $dataVehicleClass[strtoupper($value->name)]=strtoupper($value->name);
        }

        foreach ($passangerType as $key => $value) {
            // $dataPassangerType[$this->enc->encode($value->id)]=strtoupper($value->name);
            $dataPassangerType[strtoupper($value->name)]=strtoupper($value->name);
        }             


        $dataStatus[""]="Pilih";
        $dataStatus[$this->enc->encode(1)]="Aktif";   
        $dataStatus[$this->enc->encode(0)]="Tidak Aktif";   

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master Tiket Manual',
            'content'  => 'masterTicketSobek/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'import'=>checkBtnAccess($this->_module,'import_excel'),
            'btn_excel'=> generate_button_new($this->_module, 'import_excel',  site_url($this->_module.'/import_excel')),
            'port'=>$dataPort,
            'layanan'=>$dataLayanan,
            'service'=>$dataService,
            'status'=>$dataStatus,
            'vehicleClass'=>$dataVehicleClass,
            'passangerType'=>$dataPassangerType
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=$this->ticket->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();
        $layanan=$this->ticket->select_data("app.t_mtr_ship_class"," where status=1 order by name asc ")->result();
        $service=$this->ticket->select_data("app.t_mtr_service"," where status=1 order by name asc ")->result();
        $vehicleClass=$this->ticket->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc ")->result();
        $passangerType=$this->ticket->select_data("app.t_mtr_passanger_type"," where status=1 order by name asc ")->result();

        $dataPort[""]="Pilih";
        $dataLayanan[""]="Pilih";
        $dataService[""]="Pilih";
        $dataVehicleClass[""]="Pilih";
        $dataPassangerType[""]="Pilih";
        foreach ($port as $key => $value) {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        foreach ($service as $key => $value) {
            $dataService[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        foreach ($layanan as $key => $value) {
            $dataLayanan[$this->enc->encode($value->id)]=strtoupper($value->name);
        }        

        foreach ($vehicleClass as $key => $value) {
            $dataVehicleClass[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        foreach ($passangerType as $key => $value) {
            $dataPassangerType[$this->enc->encode($value->id)]=strtoupper($value->name);
        }                


        $data['title'] = 'Tambah Tiket Manual';
        $data['port']=$dataPort;
        $data['service']=$dataService;
        $data['layanan']=$dataLayanan;
        $data['vehicleClass']=$dataVehicleClass;
        $data['passangerType']=$dataPassangerType;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $ticket_number=trim(strtoupper($this->input->post('ticket_number')));
        $port=$this->enc->decode($this->input->post('port'));
        $layanan=$this->enc->decode($this->input->post('layanan'));
        $service=$this->enc->decode($this->input->post('service'));
        $golongan=$this->enc->decode($this->input->post('golongan'));


        $this->form_validation->set_rules('ticket_number', 'Nomor Tiket', 'required');
        $this->form_validation->set_rules('port', 'Nama Pelabuhan', 'required');
        $this->form_validation->set_rules('layanan', 'Layanan', 'required');
        $this->form_validation->set_rules('service', 'Jenis PJ', 'required');
        $this->form_validation->set_rules('golongan', 'Golongan', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');


        if($service==1)
        {
            $data=array(
            'ticket_number'=>$ticket_number,
            'ship_class'=>$layanan,
            'passanger_type_id'=>$golongan,
            'service_id'=>$service,
            'port_id'=>$port,     
            'status'=>1,
            'created_by'=>$this->session->userdata('username'),
            'created_on'=>date('Y-m-d H:i:s'),
            );

            $check_Tiket=$this->ticket->select_data("app.t_mtr_ticket_manual_passanger"," where status=1 
                                                                                    and ship_class=$layanan
                                                                                    and passanger_type_id=$golongan
                                                                                    and upper(ticket_number)=upper('$ticket_number') 
                                                                                    and port_id=".$port);


        }
        else
        {

            $data=array(
                'ticket_number'=>$ticket_number,
                'ship_class'=>$layanan,
                'vehicle_class_id'=>$golongan,
                'service_id'=>$service,
                'port_id'=>$port,     
                'status'=>1,
                'created_by'=>$this->session->userdata('username'),
                'created_on'=>date('Y-m-d H:i:s'),
            );

            $check_Tiket=$this->ticket->select_data("app.t_mtr_ticket_manual_vehicle"," where status=1
                                                                                and ship_class=$layanan 
                                                                                and vehicle_class_id=$golongan
                                                                                and upper(ticket_number)=upper('$ticket_number') 
                                                                                and port_id=".$port);

        }


        // print_r($data); exit;
        
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check_Tiket->num_rows()>0)
        {
            echo $res=json_api(0, 'Nomor Tiket Sudah Ada dalam satu Pelabuhan yang sama ');
        }
        else
        {
            $this->db->trans_begin();

            $table=$service==1?"app.t_mtr_ticket_manual_passanger":"app.t_mtr_ticket_manual_vehicle";
            
            $this->ticket->insert_data($table,$data);

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
        $logUrl      = site_url().'transaction/MasterTicketSobek/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
    

    public function edit($id, $service_id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $idEncode=$this->enc->decode($id);

        $serviceIdDecode=$this->enc->decode($service_id);
        

        $table=$serviceIdDecode==1?"app.t_mtr_ticket_manual_passanger":"app.t_mtr_ticket_manual_vehicle";

        $getData=$this->ticket->select_data($table, " where id={$idEncode} ")->row();


        $port=$this->ticket->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();
        $layanan=$this->ticket->select_data("app.t_mtr_ship_class"," where status=1 order by name asc ")->result();
        $service=$this->ticket->select_data("app.t_mtr_service"," where status=1 order by name asc ")->result();
        $vehicleClass=$this->ticket->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc ")->result();
        $passangerType=$this->ticket->select_data("app.t_mtr_passanger_type"," where status=1 order by name asc ")->result();

        $dataPort[""]="Pilih";
        $dataLayanan[""]="Pilih";
        $dataService[""]="Pilih";
        $dataGolongan[""]="Pilih";

        $selectedPort="";
        $selectedLayanan="";
        $selectedService="";
        $selectedGolongan="";

        foreach ($port as $key => $value) 
        {

            if($getData->port_id==$value->id)
            {
                $selectedPort=$this->enc->encode($value->id);
                $dataPort[$selectedPort]=strtoupper($value->name);
            }
            else
            {
                $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);

            }

        }

        foreach ($service as $key => $value) {
            if($getData->service_id==$value->id)
            {
                $selectedService=$this->enc->encode($value->id);
                $dataService[$selectedService]=strtoupper($value->name);
            }
            else
            {
                $dataService[$this->enc->encode($value->id)]=strtoupper($value->name);   
            }
        }

        foreach ($layanan as $key => $value) 
        {
            if($getData->ship_class==$value->id)
            {
                $selectedLayanan=$this->enc->encode($value->id);
                $dataLayanan[$selectedLayanan]=strtoupper($value->name);
            }
            else
            {
                $dataLayanan[$this->enc->encode($value->id)]=strtoupper($value->name);
            }
        }        


        if($serviceIdDecode==1) // penumpang
        {
            foreach ($passangerType as $key => $value) {

                if($getData->passanger_type_id==$value->id)
                {
                    $selectedGolongan=$this->enc->encode($value->id);
                    $dataGolongan[$selectedGolongan]=strtoupper($value->name);
                }
                else
                {
                    $dataGolongan[$this->enc->encode($value->id)]=strtoupper($value->name);
                }
            } 

            $titleGolongan='Pejalan Kaki';               

        }
        else //kendaraan
        {
            foreach ($vehicleClass as $key => $value) {
                if($getData->vehicle_class_id==$value->id)
                {
                    $selectedGolongan=$this->enc->encode($value->id);
                    $dataGolongan[$selectedGolongan]=strtoupper($value->name);
                }
                else
                {
                    $dataGolongan[$this->enc->encode($value->id)]=strtoupper($value->name);   
                }
            }

            $titleGolongan='Kendaraan';

        }


        $data['title'] = 'Tambah Tiket Manual';
        $data['port']=$dataPort;
        $data['id']=$id;
        $data['selectedPort']=$selectedPort;
        $data['service']=$dataService;
        $data['selectedService']=$selectedService;
        $data['layanan']=$dataLayanan;
        $data['selectedLayanan']=$selectedLayanan;
        $data['golongan']=$dataGolongan;
        $data['selectedGolongan']=$selectedGolongan;
        $data['titleGolongan']=$titleGolongan;
        $data['getData']=$getData;


        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $idEncode=$this->enc->decode($this->input->post('id'));
        $ticket_number=trim(strtoupper($this->input->post('ticket_number')));
        $port=$this->enc->decode($this->input->post('port'));
        $layanan=$this->enc->decode($this->input->post('layanan'));
        $service=$this->enc->decode($this->input->post('serviceId'));
        $golongan=$this->enc->decode($this->input->post('golongan'));


        $this->form_validation->set_rules('ticket_number', 'Nomor Tiket', 'required');
        $this->form_validation->set_rules('port', 'Nama Pelabuhan', 'required');
        $this->form_validation->set_rules('layanan', 'Layanan', 'required');
        $this->form_validation->set_rules('serviceId', 'Jenis PJ', 'required');
        $this->form_validation->set_rules('golongan', 'Golongan', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');


        if($service==1)
        {
            $data=array(
            'ticket_number'=>$ticket_number,
            'ship_class'=>$layanan,
            'passanger_type_id'=>$golongan,
            // 'service_id'=>$service,
            'port_id'=>$port,     
            'updated_by'=>$this->session->userdata('username'),
            'updated_on'=>date('Y-m-d H:i:s'),
            );

            $check_tiket=$this->ticket->select_data("app.t_mtr_ticket_manual_passanger"," 
                                                                            where status='1'  
                                                                            and ship_class=$layanan
                                                                            and passanger_type_id=$golongan
                                                                            and upper(ticket_number)=upper('$ticket_number') 
                                                                            and id<>{$idEncode} 
                                                                            and port_id=".$port);


        }
        else
        {

            $data=array(
                'ticket_number'=>$ticket_number,
                'ship_class'=>$layanan,
                'vehicle_class_id'=>$golongan,
                // 'service_id'=>$service,
                'port_id'=>$port,     
                'updated_by'=>$this->session->userdata('username'),
                'updated_on'=>date('Y-m-d H:i:s'),
            );

            $check_tiket=$this->ticket->select_data("app.t_mtr_ticket_manual_vehicle"," where status='1' 
                                                                                        and ship_class=$layanan
                                                                                        and vehicle_class_id=$golongan
                                                                                        and upper(ticket_number)=upper('$ticket_number')
                                                                                        and id<>{$idEncode} 
                                                                                        and port_id=".$port);

        }

        // echo print_r($data); exit;

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check_tiket->num_rows()>0)
        {
            echo $res=json_api(0, 'Nama sudah ada');   
        }
        else
        {
            $this->db->trans_begin();

            $table=$service==1?"app.t_mtr_ticket_manual_passanger":"app.t_mtr_ticket_manual_vehicle";

            $this->ticket->update_data($table,$data,"id={$idEncode}");

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
        $logUrl      = site_url().'transaction/masterTicketSobek/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function import_excel()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'import_excel');
        $data['title'] = 'Tambah Master Tiket Manual';
        $this->load->view($this->_module.'/import_excel',$data);
    }


    public function action_import_excel(){
    
        // validate_ajax();


          // load excel
        $file = $_FILES['excel']['tmp_name'];

        $load = PHPExcel_IOFactory::load($file);         
        $max_row = $load->getActiveSheet(0)->getHighestRow()-7;
        $sheets = $load->getActiveSheet()->toArray(null,true,true,true);



        $data1=array();

        $emptyError[]=0;
        $emptyErrorMessage=array();

        $duplicateError[]=0;
        $duplicateErrorMessage=array();

        $i = 1;
        foreach ($sheets as $sheet) {

            // karena data yang di excel di mulai dari baris ke 8
            // maka jika $i lebih dari 1 data akan di masukan ke database
            if ($i > 7) {

                $pelabuhan= trim($sheet['A']);
                $ticketNumber= trim($sheet['B']);
                $service= trim($sheet['C']);
                $layanan= trim($sheet['D']);
                $golongan= trim($sheet['E']);


                if(!empty($pelabuhan) and !empty($ticketNumber) and !empty($service) and !empty($golongan) and !empty($layanan))
                {
                    $checkDuplicateTicket=$this->checkDuplicateTicket($sheets, $pelabuhan ,$ticketNumber, $service,$golongan,$layanan);
 
                    if($checkDuplicateTicket['code']>0)// jika terjadi duplikasi lebih dari satu dalam satu file exxcel
                    {
                        $duplicateError[]=1;
                        // $duplicateErrorMessage[]= "No.Tiket {$sheet['B']} golongan {$sheet['E']} Baris ".$i." kolom A ";
                        $duplicateErrorMessage[]= "No.Tiket {$sheet['B']} golongan {$sheet['E']} Baris ".implode(" dan ", $checkDuplicateTicket['data']);

                    }
                }

                if(empty($pelabuhan))
                {
                    $emptyError[]=1;
                    $emptyErrorMessage[]=" data Masih Kosong Kolom A Baris {$i} ";
                }

                if(empty($ticketNumber))
                {
                    $emptyError[]=1;
                    $emptyErrorMessage[]=" data Masih Kosong Kolom B Baris {$i} ";
                }

                if(empty($service))
                {
                    $emptyError[]=1;
                    $emptyErrorMessage[]=" data Masih Kosong Kolom C Baris {$i} ";
                }

                if(empty($layanan))
                {
                    $emptyError[]=1;
                    $emptyErrorMessage[]=" data Masih Kosong Kolom D Baris {$i} ";
                }

                if(empty($golongan))
                {
                    $emptyError[]=1;
                    $emptyErrorMessage[]=" data Masih Kosong Kolom B Baris {$i} ";
                }

                $data1[]=array(
                    "pelabuhan"=>$pelabuhan,
                    "ticketNumber"=>$ticketNumber,
                    "service"=>$service,
                    "layanan"=>$layanan,
                    "golongan"=>$golongan
                );                                                                                                              
                
            }

            $i++;

        }

        // print_r($duplicateError); exit;


        if(array_sum($emptyError)>0)
        {

            echo $res=json_api(0,implode(",<br> ",$emptyErrorMessage)); // pesan error koson

            /* Fungsi Create Log */
            $createdBy   = $this->session->userdata('username');
            $logUrl      = site_url().'transaction/masterTicketSobek/action_delete';
            $logMethod   = 'DELETE';
            $logParam    = json_encode($data1);
            $logResponse = $res;

            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
            exit;        
        }

        $data2=array();
        $baris=8;

        $portError[]=0;
        $portErrorMessage=array();

        $serviceError[]=0;
        $serviceErrorMessage=array();

        $layananError[]=0;
        $layananErrorMessage=array();

        $golonganError[]=0;
        $golonganErrorMessage=array();

        $ticketNumberError[]=0;
        $ticketNumberErrorMessage=array();                

        $dataTiketPassanger=array();
        $dataTiketVehicle=array();

        foreach ($data1 as $key=>$value) 
        {

            $checkPort=$this->ticket->select_data("app.t_mtr_port"," where upper(name)='".strtoupper($value['pelabuhan'])."' and status=1 ")->row();

            $checkService=$this->ticket->select_data("app.t_mtr_service"," where upper(name)='".strtoupper($value['service'])."' and status=1 ")->row();

            $checkLayanan=$this->ticket->select_data("app.t_mtr_ship_class"," where upper(name)='".strtoupper($value['layanan'])."' and status=1 ")->row();

            

            if(count((array)$checkPort)<1)
            {
                $portError[]=1;
                $portErrorMessage[]=" {$value['pelabuhan']} Tidak Ada Kolom A Baris {$baris} ";

                $dataPortId="";
            }
            else
            {
                $dataPortId=$checkPort->id;   
            }

            if(count((array)$checkLayanan)<1)
            {
                $layananError[]=1;
                $layananErrorMessage[]=" {$value['layanan']}  Kolom D Baris {$baris} ";

                $dataShipClass="";
            }
            else
            {
                $dataShipClass=$checkLayanan->id;   
            }


            if(count((array)$checkService)<1)
            {
                $serviceError[]=1;
                $serviceErrorMessage[]=" {$value['service']}  Kolom C Baris {$baris} ";

                $dataServiceId="";
            }
            else
            {
                $dataServiceId=$checkService->id;   
                if($checkService->id==1) // penumpang
                {
                    $checkGolongan=$this->ticket->select_data("app.t_mtr_passanger_type"," where upper(name)='".strtoupper($value['golongan'])."' and status=1 ")->row();

                    if(count((array)$checkGolongan)<1)
                    {
                        $golonganError[]=1;
                        $golonganErrorMessage[]=" {$value['golongan']}  Kolom E Baris {$baris} ";

                        $dataPassangerTypeId="";
                    }
                    else
                    {
                        $dataPassangerTypeId=$checkGolongan->id;

                        if(!empty($dataPortId) and !empty($dataShipClass) )// pastikan bahwa portnya tidak kosong
                        {                        
                            $checkTicketNumber=$this->ticket->select_data("app.t_mtr_ticket_manual_passanger", 
                                " 
                                where upper(ticket_number)=upper('".$value['ticketNumber']."') 
                                and status='1' 
                                and ship_class=$dataShipClass
                                and passanger_type_id='{$dataPassangerTypeId}' 
                                and port_id='{$dataPortId}' ");

                            if($checkTicketNumber->num_rows()>0)
                            {
                                $ticketNumberError[]=1;
                                $ticketNumberErrorMessage[]="{$value['ticketNumber']} Kolom B Baris {$baris} ";
                            }
                        }

                    }

                    $dataTiketPassanger[]=array(
                        "ticket_number"=>strtoupper(trim($value['ticketNumber'])),
                        "ship_class"=>$dataShipClass,
                        "passanger_type_id"=>$dataPassangerTypeId,
                        "service_id"=>$dataServiceId,
                        "port_id"=>$dataPortId,
                        "status"=>1,
                        "created_by"=>$this->session->userdata("username"),
                        "created_on"=>date("Y-m-d H:i:s")
                    );
                            

                }
                else // kendaraan
                {
                    $checkGolongan=$this->ticket->select_data("app.t_mtr_vehicle_class"," where upper(name)='".strtoupper($value['golongan'])."' and status=1 ")->row();

                    if(count((array)$checkGolongan)<1)
                    {
                        $golonganError[]=1;
                        $golonganErrorMessage[]=" {$value['golongan']}  Kolom E Baris {$baris} ";

                        $dataVehicleClassId="";
                    }
                    else
                    {
                        $dataVehicleClassId=$checkGolongan->id;

                        if(!empty($dataPortId) and !empty($dataShipClass))// pastikan bahwa portnya tidak kosong
                        {                                                
                            $checkTicketNumber=$this->ticket->select_data("app.t_mtr_ticket_manual_vehicle", " 
                                    where upper(ticket_number)=upper('".$value['ticketNumber']."') 
                                    and status='1' 
                                    and ship_class=$dataShipClass
                                    and vehicle_class_id='{$dataVehicleClassId}'  
                                    and port_id='{$dataPortId}' ");

                            if($checkTicketNumber->num_rows()>0)
                            {
                                $ticketNumberError[]=1;
                                $ticketNumberErrorMessage[]="{$value['ticketNumber']} Kolom B Baris {$baris} ";
                            }
                        }

                    }

                    $dataTiketVehicle[]=array(
                        "ticket_number"=>strtoupper(trim($value['ticketNumber'])),
                        "ship_class"=>$dataShipClass,
                        "vehicle_class_id"=>$dataVehicleClassId,
                        "service_id"=>$dataServiceId,
                        "port_id"=>$dataPortId,
                        "status"=>1,
                        "created_by"=>$this->session->userdata("username"),
                        "created_on"=>date("Y-m-d H:i:s")
                    );                    

                }
            } 

            $baris++;           
        }

        $data=array($dataTiketVehicle,$dataTiketPassanger);

        if(array_sum($portError)>0)
        {
            echo $res=json_api(0, 'Nama pelabuhan <br>'.implode(",<br> ",$portErrorMessage).' <br>tidak ada');
        }
        else if (array_sum($serviceError)>0) 
        {
            echo $res=json_api(0, 'Nama Servis <br>'.implode(",<br> ",$serviceErrorMessage).'<br> tidak ada');          
        }
        else if(array_sum($layananError)>0)
        {

            echo $res=json_api(0, 'Nama Layanan <br>'.implode(",<br>",$layananErrorMessage).' <br> tidak ada'); 
        }
        else if(array_sum($golonganError)>0)
        {
            echo $res=json_api(0, 'Nama Golongan <br>'.implode(",<br> ",$golonganErrorMessage).'<br> tidak ada'); 
        } 

        else if(array_sum($ticketNumberError)>0)
        {
            echo $res=json_api(0, 'No Tiket <br>'.implode(",<br> ",$ticketNumberErrorMessage).'<br> golongan tidak boleh sama  dalam satu pelabuhan '); 
        }
        else if(array_sum($duplicateError)>0)
        {
            
            echo $res=json_api(0, "Duplikasi  Input <br>".implode(",<br> ",array_unique($duplicateErrorMessage))." " );   
        }                           
        else
        {
            $this->db->trans_begin();

            if(count((array)$dataTiketPassanger)>0)
            {
                $this->ticket->insert_data_batch("app.t_mtr_ticket_manual_passanger",$dataTiketPassanger);
            }

            if(count((array)$dataTiketVehicle)>0)
            {
                $this->ticket->insert_data_batch("app.t_mtr_ticket_manual_vehicle",$dataTiketVehicle);
            }            


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
        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }
    public function action_delete($id,$service)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $idDecode = $this->enc->decode($id);
        $serviceIdDecode=$this->enc->decode($service);

        $table=$serviceIdDecode==1?"app.t_mtr_ticket_manual_passanger":"app.t_mtr_ticket_manual_vehicle";

        $this->db->trans_begin();
        $this->ticket->update_data($table,$data,"id='".$idDecode."'");

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal hapus data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil hapus data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/masterTicketSobek/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function get_dock()
    {
        $port=$this->enc->decode($this->input->post('port'));

        empty($port)?$port_id='NULL':$port_id=$port;
        $dock=$this->ticket->select_data($this->_table,"where port_id=".$port_id." and status=1")->result();

        $data=array();
        foreach($dock as $key=>$value)
        {
            $value->id=$this->enc->encode($value->id);
            $data[]=$value;            
        }

         echo json_encode($data);
    }

    public function checkDuplicateTicket($sheets, $pelabuhan ,$ticketNumber, $service,$golongan,$layanan)
    {
        $count =0;
        $index=1;

        // print_r($sheets); exit;

        $dataDuplication=array();
        foreach ($sheets as $key => $value) {
            
            if($index>7)
            {
                if(
                    strtoupper($value['A'])==strtoupper($pelabuhan) 
                    and strtoupper($value['B']) == strtoupper($ticketNumber)
                    and strtoupper($value['C']) == strtoupper($service)
                    and strtoupper($value['E']) == strtoupper($golongan)
                    and strtoupper($value['D']) == strtoupper($layanan) 
                )
                {
                    $count ++;  
                    $dataDuplication[]=$index;
                }                
            }

            $index++;

        }


        if($count>1)
        {
            // return "1";
            return array(
                "code"=>"1",
                "data"=>$dataDuplication,
            );
        }
        else
        {
            // return "0";
            return array(
                "code"=>"0",
                "data"=>$dataDuplication,
            );
        }
    }    


}
