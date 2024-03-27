<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );


class Schedule extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('M_schedule','schedule');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('PHPExcel');

        $this->_table    = 'app.t_mtr_schedule';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pelabuhan/schedule';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->schedule->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->schedule->get_identity_app();

        if($get_identity==0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $port=$this->schedule->select_data("app.t_mtr_port","where status!='-5' order by name asc")->result();
                $dock="";
                $row_port=0;
            }
            else
            {
                $port=$this->schedule->select_data("app.t_mtr_port","where id={$this->session->userdata("port_id")} ")->result();
                $dock=$this->schedule->select_data("app.t_mtr_dock","where port_id={$this->session->userdata("port_id")} ")->result();
                $row_port=1;
            }
        }
        else
        {
            $port=$this->schedule->select_data("app.t_mtr_port","where id={$get_identity} ")->result();
            $dock=$this->schedule->select_data("app.t_mtr_dock","where port_id={$get_identity} ")->result();            
            $row_port=1;
        }        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Jadwal',
            'content'  => 'schedule/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'btn_excel'=> generate_button_new($this->_module, 'import_excel',  site_url($this->_module.'/import_excel')),
            'port' =>$port,
            'row_port' =>$row_port,
            'ship' =>$this->schedule->select_data("app.t_mtr_ship","where status='1' order by name asc")->result(),
            'ship_class' =>$this->schedule->select_data("app.t_mtr_ship_class","where status='1' order by name asc")->result(),
            'dock' =>$dock,
            'import'=>checkBtnAccess($this->_module,'import_excel'),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Jadwal';
        $data['port']=$this->schedule->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['ship']=$this->schedule->ship()->result();
        $data['dock']=$this->schedule->select_data("app.t_mtr_dock","where status='1' order by name asc")->result();
        $data['tipe_kapal']=$this->schedule->select_data("app.t_mtr_ship_class","where status='1' order by name asc")->result();

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port_id=$this->enc->decode($this->input->post('port'));
        $dock_id=$this->enc->decode($this->input->post('dock'));
        $ship_id=$this->enc->decode($this->input->post('ship'));
        $ship_class=$this->enc->decode($this->input->post('class'));
        $schedule=trim($this->input->post('schedule'));
        $trip=trim($this->input->post('trip'));
        $docking_on=trim($this->input->post('docking_on'));
        $open_boarding=trim($this->input->post('open_boarding'));
        $close_boarding=trim($this->input->post('close_boarding'));
        $sail_time=trim($this->input->post('sail_time'));
        $close_ramdoor=trim($this->input->post('close_ramdoor'));

        $this->form_validation->set_rules('dock', 'dermaga', 'required');
        $this->form_validation->set_rules('port', 'pelabuhan', 'required');
        $this->form_validation->set_rules('schedule', 'jadwal tanggal', 'required');
        $this->form_validation->set_rules('docking_on', 'waktu sandar', 'required');
        $this->form_validation->set_rules('trip', 'waktu tutup ramdoor', 'required');
        $this->form_validation->set_rules('class', 'ship_class', 'required');

        $max=$this->db->query('select max("order") as max_order from app.t_mtr_schedule where schedule_date='."'".$schedule."'".' and port_id='.$port_id.' and dock_id='.$dock_id.' and status=1')->row();

        // empty($ship_id)?$shipId=NULL:$shipId=$ship_id;

        $checkShipSailing[]=0;

        if(!empty($ship_id))
        {
            $shipId=$ship_id;
            if(!empty($port_id))
            {
                $checkShipSailing[]= $this->schedule->checkShipSailing($ship_id, $port_id); // check apakah kapal ini punya sailing code
            }
        }
        else
        {
            $shipId=NULL;
        }
        
        // print_r($checkShipSailing); exit;

        $schedule_code=$this->createCode($port_id);

        $data=array(
                    'port_id'=>$port_id,
                    'dock_id'=>$dock_id,
                    'docking_on'=>$docking_on,
                    'ship_class'=>$ship_class,
                    'open_boarding_on'=>empty($open_boarding)?NULL:$open_boarding,
                    'close_boarding_on'=>empty($close_boarding)?NULL:$close_boarding,
                    'close_rampdoor_on'=>empty($close_ramdoor)?NULL:$close_ramdoor,
                    'sail_time'=>empty($sail_time)?NULL:$sail_time,
                    'ship_id'=>$shipId,
                    'schedule_code'=>$schedule_code,
                    'schedule_date'=>$schedule,
                    'trip'=>$trip,
                    'status'=>1,
                    'order'=>$max->max_order+1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    );

        // mencari rute pelabuhan tujuan berdasarkan origin port 
        $get_destiny=$this->schedule->select_data("app.t_mtr_rute"," where origin=$port_id ")->row();
        $data_trx=array(
                    'schedule_date'=>$schedule,
                    'port_id'=>$port_id,
                    'dock_id'=>$dock_id,
                    'destination_port_id'=> $get_destiny->destination,
                    'ship_id'=>$shipId,
                    'schedule_code'=>$schedule_code,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username"),
        );

        $checkWaktu[]=0; // check data agar tidak bentrok waktunya
        $errorWaktu=array();        

        if(!empty($dock_id))
        {

            $checkDocking= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id} and docking_on='{$docking_on}' and status<>'-5' ");

            if($checkDocking->num_rows()>0)
            {
                $checkWaktu[]=1;
                $errorWaktu[]="SANDAR";
            } 
        }

        // jika open boarding tidak kosong
        if (!empty($open_boarding))
        {

            if(!empty($dock_id))
            {

                $checkOpenBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id} and open_boarding_on='{$open_boarding}' and status<>'-5' ");

                if($checkOpenBoarding->num_rows()>0)
                {
                    $checkWaktu[]=1;
                    $errorWaktu[]="BUKA LAYANAN";
                } 
            }

            // checking waktu open boarding tidk boleh lebih besar dari docking
            if($docking_on >= $open_boarding)
            {
                echo $res=json_api(0, 'Waktu buka boarding tidak boleh diatas jam sandar');
                $createdBy   = $this->session->userdata('username');
                $logUrl      = site_url().'pelabuhan/schedule/action_add';
                $logMethod   = 'ADD';
                $logParam    = json_encode($data);
                $logResponse = $res;

                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);                   
                exit;
            }
        }

        // jika close boarding tidak kosong
        if (!empty($close_boarding))
        {

            if(!empty($dock_id))
            {

                $checkCloseBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id} and close_boarding_on='{$close_boarding}' and status<>'-5' ");

                if($checkCloseBoarding->num_rows()>0)
                {
                    $checkWaktu[]=1;
                    $errorWaktu[]="TUTUP LAYANAN";
                } 
            }


            if($docking_on >= $close_boarding)
            {
                echo $res=json_api(0, 'Waktu tutup boarding tidak boleh diatas jam sandar');
                $createdBy   = $this->session->userdata('username');
                $logUrl      = site_url().'pelabuhan/schedule/action_add';
                $logMethod   = 'ADD';
                $logParam    = json_encode($data);
                $logResponse = $res;

                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);                   
                exit;
            }

            if(!empty($open_boarding))
            {
                if($open_boarding>= $close_boarding)
                {
                    echo $res=json_api(0, 'Waktu tutup boarding tidak boleh diatas jam buka boarding');
                    $createdBy   = $this->session->userdata('username');
                    $logUrl      = site_url().'pelabuhan/schedule/action_add';
                    $logMethod   = 'ADD';
                    $logParam    = json_encode($data);
                    $logResponse = $res;

                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);                       
                    exit;
                }
            }

        }


        // validasi jika tutup rampdor diisi
        if(!empty($close_ramdoor))
        {

            if(!empty($dock_id))
            {
                $checkCloseRamdoor= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id} and close_rampdoor_on  ='$close_ramdoor' and status<>'-5' ");
                // echo "tes"; exit;

                if($checkCloseRamdoor->num_rows()>0)
                {
                    $checkWaktu[]=1;
                    $errorWaktu[]="TUTUP RAMDOOR";
                } 
            }

            if($docking_on >= $close_ramdoor)
            {
                echo $res=json_api(0, 'Waktu tutup ramdor tidak boleh diatas jam sandar');
                $createdBy   = $this->session->userdata('username');
                $logUrl      = site_url().'pelabuhan/schedule/action_add';
                $logMethod   = 'ADD';
                $logParam    = json_encode($data);
                $logResponse = $res;

                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);                   
                exit;
            }

            if(!empty($open_boarding))
            {
                if($open_boarding>=$close_ramdoor)
                {
                    echo $res=json_api(0, 'Waktu tutup ramdor tidak boleh diatas jam buka boarding');
                    $createdBy   = $this->session->userdata('username');
                    $logUrl      = site_url().'pelabuhan/schedule/action_add';
                    $logMethod   = 'ADD';
                    $logParam    = json_encode($data);
                    $logResponse = $res;

                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);                       
                    exit;
                }
            }

            if(!empty($close_boarding))
            {
                if($close_boarding>=$close_ramdoor)
                {
                    echo $res=json_api(0, 'Waktu tutup ramdor tidak boleh diatas jam tutup boarding');

                    $createdBy   = $this->session->userdata('username');
                    $logUrl      = site_url().'pelabuhan/schedule/action_add';
                    $logMethod   = 'ADD';
                    $logParam    = json_encode($data);
                    $logResponse = $res;

                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);                       
                    exit;
                }
            }
        }


        // validasi jika tutup rampdor diisi
        if(!empty($sail_time))
        {

            if(!empty($dock_id))
            {

                $checkSailTime= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id} and sail_time='{$sail_time}' and status<>'-5' ");

                if($checkSailTime->num_rows()>0)
                {
                    $checkWaktu[]=1;
                    $errorWaktu[]="KEBERANGKATAN";
                } 
            }

            if($docking_on >= $sail_time)
            {
                echo $res=json_api(0, 'Waktu keberangkatan tidak boleh diatas jam sandar');
                $createdBy   = $this->session->userdata('username');
                $logUrl      = site_url().'pelabuhan/schedule/action_add';
                $logMethod   = 'ADD';
                $logParam    = json_encode($data);
                $logResponse = $res;

                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);                   
                exit;
            }

            if(!empty($open_boarding))
            {
                if($open_boarding>=$sail_time)
                {
                    echo $res=json_api(0, 'Waktu keberangkatan ramdor tidak boleh diatas jam buka boarding');
                    $createdBy   = $this->session->userdata('username');
                    $logUrl      = site_url().'pelabuhan/schedule/action_add';
                    $logMethod   = 'ADD';
                    $logParam    = json_encode($data);
                    $logResponse = $res;

                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);                       
                    exit;
                }
            }

            if(!empty($close_boarding))
            {
                if($close_boarding>=$sail_time)
                {
                    echo $res=json_api(0, 'Waktu keberangkatan ramdor tidak boleh diatas jam tutup boarding');
                    $createdBy   = $this->session->userdata('username');
                    $logUrl      = site_url().'pelabuhan/schedule/action_add';
                    $logMethod   = 'ADD';
                    $logParam    = json_encode($data);
                    $logResponse = $res;

                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);                       
                    exit;
                }
            }


            if(!empty($close_ramdoor))
            {
                if($close_ramdoor>=$sail_time)
                {
                    echo $res=json_api(0, 'Waktu keberangkatan  tidak boleh diatas jam buka tutup ramdor');
                    $createdBy   = $this->session->userdata('username');
                    $logUrl      = site_url().'pelabuhan/schedule/action_add';
                    $logMethod   = 'ADD';
                    $logParam    = json_encode($data);
                    $logResponse = $res;

                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);                    
                    exit;
                }
            }
        }


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if (array_sum($checkShipSailing)>0)
        {
            echo $res=json_api(0, 'Kapal Belum Mempunyai Kode Pelayaran di pelabuhan  ');
        }
        else if(array_sum($checkWaktu)>0)
        {
            $implode =implode(", ", $errorWaktu);
            echo $res=json_api(0, 'Tanggal dan Waktu '.$implode.' tidak boleh sama dalam satu dermaga');
        }
        else
        {
            $this->db->trans_begin();
            $this->schedule->insert_data($this->_table,$data);

            $this->schedule->insert_data("app.t_trx_schedule",$data_trx);
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
        $logUrl      = site_url().'pelabuhan/schedule/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function import_excel()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'import_excel');
        $data['title'] = 'Tambah Jadwal';
        $this->load->view($this->_module.'/import_excel',$data);
    }
    public function action_import_excel(){
    
        // validate_ajax();

        /*
            keterangan
            $value['A'] = Nama pelabuhan
            $value['B'] = Nama dermaga
            $value['C'] = Tanggal dan jam sandar
            $value['D'] = Tanggal dan jam buka layanan
            $value['E'] = Tanggal dan jam tutup layanan
            $value['F'] = Tanggal dan jam tutup rampdoor
            $value['G'] = Tanggal dan jam Keberangkatan
            $value['H'] = Nama Kapal
            $value['I'] = Tanggal Jadwal
            $value['J'] = Trip
        */
          // load excel
        $file = $_FILES['excel']['tmp_name'];

        $load = PHPExcel_IOFactory::load($file);

        /*
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $load = $reader->load($file); 
        */
          
          $max_row = $load->getActiveSheet(0)->getHighestRow()-7;

          $true=array();
          for ($i=0; $i < $max_row ; $i++) { 
                $true[]=true;
            }

          $sheets = $load->getActiveSheet()->toArray(null,true,true,true);



          $i = 1;
          $i2 = 1;

          $empty_data=array();
          $data=array();
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/action_import_excel';
        $logMethod   = 'ADD';

        $invalid_ship=array();
        $invalid_port=array();
        $invalid_dock=array();

        $err_ship=array();
        $err_port=array();
        $err_dock=array();
        $err_ship_class=array();

        $checkUniquePortId=array();


        // check apakah proses waktunya lebih besar dari sebelumnya
        foreach ($sheets as $value)
        {
            // dimulai dari sheet 8
            if($i2>7)
            {
                // jika open boarding tidak kosong
                if (!empty($value['D']))
                {
                    // checking waktu open boarding tidk boleh lebih besar dari docking
                    if($value['C'] >= $value['D'])
                    {
                        echo $res=json_api(0, "Waktu buka boarding tidak boleh diatas jam sandar, baris ke {$i2} kolom D");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }
                }

                // jika close boarding tidak kosong
                if (!empty($value['E']))
                {
                    // tidak boleh di atas docking
                    if($value['C'] >= $value['E'])
                    {
                        echo $res=json_api(0, "Waktu tutup boarding tidak boleh diatas jam sandar, baris ke {$i2} kolom E");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    // jika open boarding keiisi
                    if(!empty($value['D']))
                    {
                        if($value['D']>= $value['E'])
                        {
                            echo $res=json_api(0, "Waktu tutup boarding tidak boleh diatas jam buka boarding baris ke {$i2} kolom E" );
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                }

                // validasi jika tutup rampdor diisi
                if(!empty($value['F']))
                {
                    if($value['C'] >= $value['F'])
                    {
                        echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam sandar, baris ke {$i2} kolom F");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    if(!empty($value['D']))
                    {
                        if($value['D']>=$value['F'])
                        {
                            echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam buka boarding, , baris ke {$i2} kolom F");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                    if(!empty($value['E']))
                    {
                        if($value['E']>=$value['F'])
                        {
                            echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam tutup boarding, , baris ke {$i2} kolom F");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }
                }


                // validasi jika tutup rampdor diisi
                if(!empty($value['G']))
                {
                    if($value['C'] >= $value['G'])
                    {
                        echo $res=json_api(0, "Waktu keberangkatan tidak boleh diatas jam sandar, baris ke {$i2} kolom G");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    if(!empty($value['D']))
                    {
                        if($value['D']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan ramdor tidak boleh diatas jam buka boarding, baris ke {$i2} kolom G");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                    if(!empty($value['E']))
                    {
                        if($value['E']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan ramdor tidak boleh diatas jam tutup boarding, baris ke {$i2} kolom G" );
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }


                    if(!empty($value['F']))
                    {
                        if($value['F']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan  tidak boleh diatas jam buka tutup ramdor, baris ke {$i2} kolom G");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }                        
                }

                // check jika nama kapal diisi dan dan nama kapal tidak sama di db maka akan di tolak
                if(!empty($value['H']))
                {
                    $check_ship_data=$this->schedule->select_data("app.t_mtr_ship","where status=1 and upper(name)='".strtoupper(trim($value['H']))."'");
                    if($check_ship_data->num_rows()<1)
                    {
                        echo $res=json_api(0, "Nama Kapal {$value['H']} tidak ada, baris ke {$i2} kolom H");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }
                }
            }
            
            $i2++;
        }

        // check identity app nya 
        $identity=$this->schedule->get_identity_app();
        if($identity==0)
        {
            if(!empty($this->session->userdata("port_id")))
            {
                $identity_app=$this->schedule->select_data(" app.t_mtr_port" ," where id=".$this->session->userdata("port_id"))->row();

                $get_port_identity_name=$identity_app->name;

            }
            else
            {
                $identity_app="";
                $get_port_identity_name="";
            }
        }
        else
        {
            $identity_app=$identity_app=$this->schedule->select_data(" app.t_mtr_port" ," where id=".$identity)->row();
            $get_port_identity_name=$identity_app->name;
        }


        $check_identity[]=0;

        $checkWaktu[]=0;
        $errorWaktu=array();

        $errorDuplicateInput=array();
        $errDuplicate[]=0;

        $checkShipSailing[]=0; 
        $checkShipSailingMessege=array();

        $getDockId= array();

        foreach ($sheets as $sheet) {

            // karena data yang di excel di mulai dari baris ke 8
            // maka jika $i lebih dari 1 data akan di masukan ke database
            $order_data=1;
            if ($i > 7) {

                $check_ship=$this->schedule->select_data("app.t_mtr_ship","where status=1 and upper(name)='".strtoupper(trim($sheet['H']))."'");

                // checking nama portnya apakah benar ada
                $check_port=$this->schedule->select_data("app.t_mtr_port","where status=1 and  upper(name)='".trim(strtoupper($sheet['A']))."'");

                // ketika mempunyai port id maka di check apakah sesuai pelabuhanya dengan pelabuhan yang di miliki user
                if(!empty($identity_app))
                {
                    if( strtoupper($check_port->row()->name) != strtoupper($identity_app->name))
                    {
                        $check_identity[]=1;   
                    }
                }

                $check_dock=$this->schedule->val_dock("where a.status=1 and upper(b.name)='".strtoupper(trim($sheet['A']))."' and upper(a.name)='".strtoupper(trim($sheet['B']))."'");

                $port_id=$check_port->row();
                $ship_id=$check_ship->row();
                $dock_id=$check_dock->row();
                
                // echo ; exit;

                if($check_dock->num_rows()>0 && $check_port->num_rows()>0 )
                {   
                    $getDockId[]= array(
                                    "dockId"=>$dock_id->dock_id,
                                    "portId"=>$port_id->id,
                                    "scheduleDate"=>$sheet['I']
                                );
                    if($check_ship->num_rows()>0)
                    {
                        $getCheckShipSailing = $this->schedule->checkShipSailing($ship_id->id, $port_id->id); // check apakah kapal ini punya sailing code
                        if($getCheckShipSailing==1) // 1 artinya data tidak ada atau data error di model
                        {
                            $checkShipSailing [] =  $getCheckShipSailing;
                            $checkShipSailingMessege[]=  "- ".$ship_id->name." baris ke ".$i." Kolom H Pelabuhan ".$port_id->name;
                        }
                    }                    
                    
                    // check docking
                    $checkDocking= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id}  and docking_on='{$sheet['C']}' and status<>'-5' ");
                    

                    if($checkDocking->num_rows()>0)
                    {
                        $checkWaktu[]=1;
                        $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Sandar Baris ke {$i} kolom C ";
                    }
                
                    // $getCheckQry[]=" select * from app.t_mtr_schedule where dock_id={$dock_id->id} and port_id={$port_id->id}  and docking_on='{$sheet['C']}' and status<>'-5'  ";


                    // check duplikat didalam excel 
                    $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['C'],"docking");
                    if($checkDuplicateTime["code"]>0)
                    {
                        $errDuplicate[]=1;
                        $errorDuplicateInput[]= "Duplikat Jam Sandar Baris ".implode(" dan ", $checkDuplicateTime['data']);
                    }

                    if(!empty($sheet['D'])) // open boarding
                    {

                        $checkOpenBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id} and open_boarding_on='{$sheet['D']}' and status<>'-5' ");

                        if($checkOpenBoarding->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Buka Layanan Baris ke {$i} kolom D ";
                        }
    
                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['D'],"openBoarding");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Buka Layanan  Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                        
                    }

                    if(!empty($sheet['E'])) // close boarding
                    {
                        $checkCloseBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id}  and close_boarding_on='{$sheet['E']}' and status<>'-5' ");

                        if($checkCloseBoarding->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Tutup Layanan Baris ke {$i} kolom E ";
                        } 

                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['E'],"closeBoarding");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Tutup Layanan  Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                                                
                    }

                    if(!empty($sheet['F'])) // close rampdoor
                    {
                        $checkCloseRamdoor= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id} and close_rampdoor_on='{$sheet['F']}' and status<>'-5' ");

                        if($checkCloseRamdoor->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Tutup Rampdoor Baris ke {$i} kolom C ";
                        } 

                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['F'],"closeRampdoor");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Tutup Rampdoor  Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                                                          
                    }     

                    if(!empty($sheet['G'])) // sail
                    {
                        $checkSailTime= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id} and sail_time='{$sheet['G']}' and status<>'-5' ");

                        if($checkSailTime->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Keberangkatan Baris ke {$i} kolom G ";
                        } 

                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['G'],"sail");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Berangkat Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                           
                    }                                                            

                }
                // $ship_class_id=$check_ship_class->row();

                

                $data[]=array(
                    'port_id'=>empty($port_id->id)?"":$port_id->id,
                    'dock_id'=>empty($dock_id->id)?"":$dock_id->id,
                    'docking_on'=>$sheet['C'],
                    // 'ship_class'=>empty($ship_class_id->id)?"":$ship_class_id->id,
                    'open_boarding_on'=>$sheet['D'],
                    'close_boarding_on'=>$sheet['E'],
                    'close_rampdoor_on'=>$sheet['F'],
                    'sail_time'=>$sheet['G'],
                    'ship_id'=>empty($ship_id->id)?"":$ship_id->id,
                    'schedule_code'=>$this->createCode(empty($port_id->id)?"":$port_id->id),
                    'schedule_date'=>$sheet['I'],
                    'trip'=>$sheet['J'],
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    );

                    if(!empty($port_id->id))
                    {
                        $checkUniquePortId[]=$port_id->id;
                    }

                    
                if(empty($sheet['A']) || empty($sheet['B']) || empty($sheet['C']) ||  empty($sheet['I']) || empty($sheet['J']) )
                {
                    $empty_data[]=1;   
                }

                else if($check_port->num_rows()<1)
                {
                    $invalid_port[]=$sheet['A'];
                    $err_port[]=1;
                    
                }
                else if($check_dock->num_rows()<1)
                {
                    $invalid_dock[]=$sheet['B']." di pelabuhan ".$sheet['A'];
                    $invalid_dock_port[]=$sheet['A'];
                    $err_dock[]=1;
                }
                
                $order_data++;
            }

            $i++;
        }

        // print_r($checkShipSailing); exit;
          // echo array_sum($err_ship_class);
          // exit;
        //   print_r($getCheckQry);
        //   exit;

        // check agar port id tidak diinput berbeda dalam satu form        
        // print_r(array_unique($checkUniquePortId)); exit;

        asort($getDockId);         
        $orderingData=array();
        $max =1;
        $lastDockId = "";
        $lastScheduleDate = "";

        // print_r($getDockId); exit;
        foreach ($getDockId as $key => $value) {
            $nowDock = $value['dockId'];        
            $nowScheduleDate = $value['scheduleDate'];            

            if($nowDock != $lastDockId || $nowScheduleDate !=$lastScheduleDate )
            {
                $getMax = $this->db->query("select max(".'"order"'.") as max_order from app.t_mtr_schedule where schedule_date='".$value['scheduleDate']."' and port_id=".$value['portId']." and dock_id=".$value['dockId']." and status=1")->row()->max_order;
                
                $max = empty($getMax)?1:$getMax+1;
                $orderingData[]=$max;
  
            }
            else 
            {
                $max ++;
                $orderingData[]=$max;
            }            

            $lastDockId = $value['dockId'];
            $lastScheduleDate = $value['scheduleDate'];            
        }



        if(array_sum($empty_data)>0)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if (array_sum($check_identity)>0) 
        {
            echo $res=json_api(0, ' Tidak bisa menambahkan jadwal lain, selain pelabuhan '.$get_port_identity_name);            
        }
        else if(array_sum($err_port)>0)
        {
            echo $res=json_api(0, 'Nama pelabuhan '.implode(", ",array_unique($invalid_port)).' tidak ada');
        }
        else if(array_sum($err_dock)>0)
        {
            echo $res=json_api(0, 'Nama dermaga '.implode(", ",array_unique($invalid_dock)).' tidak ada');
        }
        else if(array_sum($checkShipSailing)>0)
        {
            echo $res=json_api(0, 'Kapal <br>'.implode(", <br>",array_unique($checkShipSailingMessege)).' <br> tidak ada Kode Pelayaran');
        }        
        else if(array_sum($checkWaktu)>0)
        {
            // $unique= array_unique($errorWaktu);
            // $implode =implode(", ",$unique);

            $implode =implode("<br> ",$errorWaktu);
            echo $res=json_api(0, 'Tanggal dan Waktu tidak boleh sama dalam satu dermaga <br> '.$implode);
        }
        else if(count(array_unique($checkUniquePortId))>1) // mencegah port id diinput berbeda dalam satu form
        {
            echo $res=json_api(0,"Dalam Satu Form Hanya boleh satu pelabuhan");
        }
        else if(array_sum($errDuplicate)>0)
        {
            echo $res=json_api(0, "Duplikasi  Input <br>".implode(",<br> ",array_unique($errorDuplicateInput))." " );   
        }           
        else
        {
            

            
            $schedule_code= $this->createCode($checkUniquePortId[0]);
            $indexCode =(int)substr($schedule_code, -4);
            $frontCode =substr($schedule_code,0, -4);

            
            // echo $frontCode; exit;

            // shorting asc data berdasarkan dock id , agar mendapatkan ordering yang sesuai
            usort($data, function($a, $b) {
                return $a['dock_id'] <=> $b['dock_id'];
            });
    
            $nilIndex=0;            
             foreach ($data as $key => $value) {
                $schedule=$value['schedule_date'];
                $maxCode = $orderingData[$nilIndex];
                $nilIndex++;


                // $max = $this->db->query("select max(".'"order"'.") as max_order from app.t_mtr_schedule where schedule_date='".$schedule."' and port_id=".$value['port_id']." and dock_id=".$value['dock_id']." and status=1")->row();
      

                // ambil ship class berdasarkan docknya
                $get_ship_class=$this->schedule->select_data("app.t_mtr_dock"," where id=".$value['dock_id']."")->row();

                empty($value['ship_id'])?$shipId=NULL:$shipId=$value['ship_id'];

                $data_row[]=array(
                'port_id'=>$value['port_id'],
                'dock_id'=>$value['dock_id'],
                'docking_on'=>$value['docking_on'],
                'ship_class'=>$get_ship_class->ship_class_id,
                'open_boarding_on'=>$value['open_boarding_on'],
                'close_boarding_on'=>$value['close_boarding_on'],
                'close_rampdoor_on'=>$value['close_rampdoor_on'],
                'sail_time'=>$value['sail_time'],
                'ship_id'=>$shipId,
                'schedule_code'=>$schedule_code,
                'schedule_date'=>$value['schedule_date'],
                'trip'=>$value['trip'],
                'status'=>1,
                // 'order'=>$max->max_order+1,
                'order'=>$maxCode,
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata('username'),
                );

                // mencari rute pelabuhan tujuan berdasarkan origin port 
                $get_destiny=$this->schedule->select_data("app.t_mtr_rute"," where origin='".$value['port_id']."' ")->row();

                $data_trx[]=array(
                        'schedule_date'=>$value['schedule_date'],
                        'port_id'=>$value['port_id'],
                        'dock_id'=>$value['dock_id'],
                        'destination_port_id'=> $get_destiny->destination,
                        'ship_id'=>$shipId,
                        'schedule_code'=>$schedule_code,
                        'status'=>1,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata("username"),
                );

                $indexCode++;
                $schedule_code = $frontCode. sprintf("%04s", $indexCode);


             }

            //  print_r($data_row);
            //  exit;

             $this->db->trans_begin();

              $this->schedule->insert_data_batch($this->_table,$data_row);
              $this->schedule->insert_data_batch("app.t_trx_schedule",$data_trx);
              

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



        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }    
    public function action_import_excel_31052023(){
    
        // validate_ajax();

        /*
            keterangan
            $value['A'] = Nama pelabuhan
            $value['B'] = Nama dermaga
            $value['C'] = Tanggal dan jam sandar
            $value['D'] = Tanggal dan jam buka layanan
            $value['E'] = Tanggal dan jam tutup layanan
            $value['F'] = Tanggal dan jam tutup rampdoor
            $value['G'] = Tanggal dan jam Keberangkatan
            $value['H'] = Nama Kapal
            $value['I'] = Tanggal Jadwal
            $value['J'] = Trip
        */
          // load excel
        $file = $_FILES['excel']['tmp_name'];

        $load = PHPExcel_IOFactory::load($file);

        /*
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $load = $reader->load($file); 
        */
          
          $max_row = $load->getActiveSheet(0)->getHighestRow()-7;

          $true=array();
          for ($i=0; $i < $max_row ; $i++) { 
                $true[]=true;
            }

          $sheets = $load->getActiveSheet()->toArray(null,true,true,true);



          $i = 1;
          $i2 = 1;

          $empty_data=array();
          $data=array();
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/action_import_excel';
        $logMethod   = 'ADD';

        $invalid_ship=array();
        $invalid_port=array();
        $invalid_dock=array();

        $err_ship=array();
        $err_port=array();
        $err_dock=array();
        $err_ship_class=array();

        $checkUniquePortId=array();


        // check apakah proses waktunya lebih besar dari sebelumnya
        foreach ($sheets as $value)
        {
            // dimulai dari sheet 8
            if($i2>7)
            {
                // jika open boarding tidak kosong
                if (!empty($value['D']))
                {
                    // checking waktu open boarding tidk boleh lebih besar dari docking
                    if($value['C'] >= $value['D'])
                    {
                        echo $res=json_api(0, "Waktu buka boarding tidak boleh diatas jam sandar, baris ke {$i2} kolom D");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }
                }

                // jika close boarding tidak kosong
                if (!empty($value['E']))
                {
                    // tidak boleh di atas docking
                    if($value['C'] >= $value['E'])
                    {
                        echo $res=json_api(0, "Waktu tutup boarding tidak boleh diatas jam sandar, baris ke {$i2} kolom E");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    // jika open boarding keiisi
                    if(!empty($value['D']))
                    {
                        if($value['D']>= $value['E'])
                        {
                            echo $res=json_api(0, "Waktu tutup boarding tidak boleh diatas jam buka boarding baris ke {$i2} kolom E" );
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                }

                // validasi jika tutup rampdor diisi
                if(!empty($value['F']))
                {
                    if($value['C'] >= $value['F'])
                    {
                        echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam sandar, baris ke {$i2} kolom F");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    if(!empty($value['D']))
                    {
                        if($value['D']>=$value['F'])
                        {
                            echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam buka boarding, , baris ke {$i2} kolom F");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                    if(!empty($value['E']))
                    {
                        if($value['E']>=$value['F'])
                        {
                            echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam tutup boarding, , baris ke {$i2} kolom F");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }
                }


                // validasi jika tutup rampdor diisi
                if(!empty($value['G']))
                {
                    if($value['C'] >= $value['G'])
                    {
                        echo $res=json_api(0, "Waktu keberangkatan tidak boleh diatas jam sandar, baris ke {$i2} kolom G");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    if(!empty($value['D']))
                    {
                        if($value['D']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan ramdor tidak boleh diatas jam buka boarding, baris ke {$i2} kolom G");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                    if(!empty($value['E']))
                    {
                        if($value['E']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan ramdor tidak boleh diatas jam tutup boarding, baris ke {$i2} kolom G" );
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }


                    if(!empty($value['F']))
                    {
                        if($value['F']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan  tidak boleh diatas jam buka tutup ramdor, baris ke {$i2} kolom G");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }                        
                }

                // check jika nama kapal diisi dan dan nama kapal tidak sama di db maka akan di tolak
                if(!empty($value['H']))
                {
                    $check_ship_data=$this->schedule->select_data("app.t_mtr_ship","where status=1 and upper(name)='".strtoupper(trim($value['H']))."'");
                    if($check_ship_data->num_rows()<1)
                    {
                        echo $res=json_api(0, "Nama Kapal {$value['H']} tidak ada, baris ke {$i2} kolom H");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }
                }
            }
            
            $i2++;
        }

        // check identity app nya 
        $identity=$this->schedule->get_identity_app();
        if($identity==0)
        {
            if(!empty($this->session->userdata("port_id")))
            {
                $identity_app=$this->schedule->select_data(" app.t_mtr_port" ," where id=".$this->session->userdata("port_id"))->row();

                $get_port_identity_name=$identity_app->name;

            }
            else
            {
                $identity_app="";
                $get_port_identity_name="";
            }
        }
        else
        {
            $identity_app=$identity_app=$this->schedule->select_data(" app.t_mtr_port" ," where id=".$identity)->row();
            $get_port_identity_name=$identity_app->name;
        }


        $check_identity[]=0;

        $checkWaktu[]=0;
        $errorWaktu=array();

        $errorDuplicateInput=array();
        $errDuplicate[]=0;

        $checkShipSailing[]=0; 
        $checkShipSailingMessege=array();

        $getDockId= array();

        foreach ($sheets as $sheet) {

            // karena data yang di excel di mulai dari baris ke 8
            // maka jika $i lebih dari 1 data akan di masukan ke database
            $order_data=1;
            if ($i > 7) {

                $check_ship=$this->schedule->select_data("app.t_mtr_ship","where status=1 and upper(name)='".strtoupper(trim($sheet['H']))."'");

                // checking nama portnya apakah benar ada
                $check_port=$this->schedule->select_data("app.t_mtr_port","where status=1 and  upper(name)='".trim(strtoupper($sheet['A']))."'");

                // ketika mempunyai port id maka di check apakah sesuai pelabuhanya dengan pelabuhan yang di miliki user
                if(!empty($identity_app))
                {
                    if( strtoupper($check_port->row()->name) != strtoupper($identity_app->name))
                    {
                        $check_identity[]=1;   
                    }
                }

                $check_dock=$this->schedule->val_dock("where a.status=1 and upper(b.name)='".strtoupper(trim($sheet['A']))."' and upper(a.name)='".strtoupper(trim($sheet['B']))."'");

                $port_id=$check_port->row();
                $ship_id=$check_ship->row();
                $dock_id=$check_dock->row();
                
                // echo ; exit;

                if($check_dock->num_rows()>0 && $check_ship->num_rows()>0 && $check_port->num_rows()>0 )
                {   
                    $getDockId[]= array(
                                    "dockId"=>$dock_id->dock_id,
                                    "portId"=>$port_id->id,
                                    "scheduleDate"=>$sheet['I']
                                );
                    
                    $getCheckShipSailing = $this->schedule->checkShipSailing($ship_id->id, $port_id->id); // check apakah kapal ini punya sailing code
                    if($getCheckShipSailing==1) // 1 artinya data tidak ada atau data error di model
                    {
                        $checkShipSailing [] =  $getCheckShipSailing;
                        $checkShipSailingMessege[]=  "- ".$ship_id->name." baris ke ".$i." Kolom H Pelabuhan ".$port_id->name;
                    }
                    
                    // check docking
                    $checkDocking= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id}  and docking_on='{$sheet['C']}' and status<>'-5' ");
                    

                    if($checkDocking->num_rows()>0)
                    {
                        $checkWaktu[]=1;
                        $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Sandar Baris ke {$i} kolom C ";
                    }
                
                    // $getCheckQry[]=" select * from app.t_mtr_schedule where dock_id={$dock_id->id} and port_id={$port_id->id}  and docking_on='{$sheet['C']}' and status<>'-5'  ";


                    // check duplikat didalam excel 
                    $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['C'],"docking");
                    if($checkDuplicateTime["code"]>0)
                    {
                        $errDuplicate[]=1;
                        $errorDuplicateInput[]= "Duplikat Jam Sandar Baris ".implode(" dan ", $checkDuplicateTime['data']);
                    }

                    if(!empty($sheet['D'])) // open boarding
                    {

                        $checkOpenBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id} and open_boarding_on='{$sheet['D']}' and status<>'-5' ");

                        if($checkOpenBoarding->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Buka Layanan Baris ke {$i} kolom D ";
                        }
    
                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['D'],"openBoarding");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Buka Layanan  Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                        
                    }

                    if(!empty($sheet['E'])) // close boarding
                    {
                        $checkCloseBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id}  and close_boarding_on='{$sheet['E']}' and status<>'-5' ");

                        if($checkCloseBoarding->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Tutup Layanan Baris ke {$i} kolom E ";
                        } 

                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['E'],"closeBoarding");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Tutup Layanan  Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                                                
                    }

                    if(!empty($sheet['F'])) // close rampdoor
                    {
                        $checkCloseRamdoor= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id} and close_rampdoor_on='{$sheet['F']}' and status<>'-5' ");

                        if($checkCloseRamdoor->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Tutup Rampdoor Baris ke {$i} kolom C ";
                        } 

                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['F'],"closeRampdoor");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Tutup Rampdoor  Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                                                          
                    }     

                    if(!empty($sheet['G'])) // sail
                    {
                        $checkSailTime= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id} and sail_time='{$sheet['G']}' and status<>'-5' ");

                        if($checkSailTime->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Keberangkatan Baris ke {$i} kolom G ";
                        } 

                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['G'],"sail");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Berangkat Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                           
                    }                                                            

                }
                // $ship_class_id=$check_ship_class->row();

                

                $data[]=array(
                    'port_id'=>empty($port_id->id)?"":$port_id->id,
                    'dock_id'=>empty($dock_id->id)?"":$dock_id->id,
                    'docking_on'=>$sheet['C'],
                    // 'ship_class'=>empty($ship_class_id->id)?"":$ship_class_id->id,
                    'open_boarding_on'=>$sheet['D'],
                    'close_boarding_on'=>$sheet['E'],
                    'close_rampdoor_on'=>$sheet['F'],
                    'sail_time'=>$sheet['G'],
                    'ship_id'=>empty($ship_id->id)?"":$ship_id->id,
                    'schedule_code'=>$this->createCode(empty($port_id->id)?"":$port_id->id),
                    'schedule_date'=>$sheet['I'],
                    'trip'=>$sheet['J'],
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    );

                    if(!empty($port_id->id))
                    {
                        $checkUniquePortId[]=$port_id->id;
                    }

                    
                if(empty($sheet['A']) || empty($sheet['B']) || empty($sheet['C']) ||  empty($sheet['I']) || empty($sheet['J']) )
                {
                    $empty_data[]=1;   
                }

                else if($check_port->num_rows()<1)
                {
                    $invalid_port[]=$sheet['A'];
                    $err_port[]=1;
                    
                }
                else if($check_dock->num_rows()<1)
                {
                    $invalid_dock[]=$sheet['B']." di pelabuhan ".$sheet['A'];
                    $invalid_dock_port[]=$sheet['A'];
                    $err_dock[]=1;
                }
                
                $order_data++;
            }

            $i++;
        }

        // print_r($checkShipSailing); exit;
          // echo array_sum($err_ship_class);
          // exit;
        //   print_r($getCheckQry);
        //   exit;

        // check agar port id tidak diinput berbeda dalam satu form        
        // print_r(array_unique($checkUniquePortId)); exit;

        asort($getDockId);         
        $orderingData=array();
        $max =1;
        $lastDockId = "";
        $lastScheduleDate = "";

        // print_r($getDockId); exit;
        foreach ($getDockId as $key => $value) {
            $nowDock = $value['dockId'];        
            $nowScheduleDate = $value['scheduleDate'];            

            if($nowDock != $lastDockId || $nowScheduleDate !=$lastScheduleDate )
            {
                $getMax = $this->db->query("select max(".'"order"'.") as max_order from app.t_mtr_schedule where schedule_date='".$value['scheduleDate']."' and port_id=".$value['portId']." and dock_id=".$value['dockId']." and status=1")->row()->max_order;
                
                $max = empty($getMax)?1:$getMax+1;
                $orderingData[]=$max;
  
            }
            else 
            {
                $max ++;
                $orderingData[]=$max;
            }            

            $lastDockId = $value['dockId'];
            $lastScheduleDate = $value['scheduleDate'];            
        }



        if(array_sum($empty_data)>0)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if (array_sum($check_identity)>0) 
        {
            echo $res=json_api(0, ' Tidak bisa menambahkan jadwal lain, selain pelabuhan '.$get_port_identity_name);            
        }
        else if(array_sum($err_port)>0)
        {
            echo $res=json_api(0, 'Nama pelabuhan '.implode(", ",array_unique($invalid_port)).' tidak ada');
        }
        else if(array_sum($err_dock)>0)
        {
            echo $res=json_api(0, 'Nama dermaga '.implode(", ",array_unique($invalid_dock)).' tidak ada');
        }
        else if(array_sum($checkShipSailing)>0)
        {
            echo $res=json_api(0, 'Kapal <br>'.implode(", <br>",array_unique($checkShipSailingMessege)).' <br> tidak ada Kode Pelayaran');
        }        
        else if(array_sum($checkWaktu)>0)
        {
            // $unique= array_unique($errorWaktu);
            // $implode =implode(", ",$unique);

            $implode =implode("<br> ",$errorWaktu);
            echo $res=json_api(0, 'Tanggal dan Waktu tidak boleh sama dalam satu dermaga <br> '.$implode);
        }
        else if(count(array_unique($checkUniquePortId))>1) // mencegah port id diinput berbeda dalam satu form
        {
            echo $res=json_api(0,"Dalam Satu Form Hanya boleh satu pelabuhan");
        }
        else if(array_sum($errDuplicate)>0)
        {
            echo $res=json_api(0, "Duplikasi  Input <br>".implode(",<br> ",array_unique($errorDuplicateInput))." " );   
        }           
        else
        {
            

            
            $schedule_code= $this->createCode($checkUniquePortId[0]);
            $indexCode =(int)substr($schedule_code, -4);
            $frontCode =substr($schedule_code,0, -4);

            
            // echo $frontCode; exit;

            // shorting asc data berdasarkan dock id , agar mendapatkan ordering yang sesuai
            usort($data, function($a, $b) {
                return $a['dock_id'] <=> $b['dock_id'];
            });
    
            $nilIndex=0;            
             foreach ($data as $key => $value) {
                $schedule=$value['schedule_date'];
                $maxCode = $orderingData[$nilIndex];
                $nilIndex++;


                // $max = $this->db->query("select max(".'"order"'.") as max_order from app.t_mtr_schedule where schedule_date='".$schedule."' and port_id=".$value['port_id']." and dock_id=".$value['dock_id']." and status=1")->row();
      

                // ambil ship class berdasarkan docknya
                $get_ship_class=$this->schedule->select_data("app.t_mtr_dock"," where id=".$value['dock_id']."")->row();

                empty($value['ship_id'])?$shipId=NULL:$shipId=$value['ship_id'];

                $data_row[]=array(
                'port_id'=>$value['port_id'],
                'dock_id'=>$value['dock_id'],
                'docking_on'=>$value['docking_on'],
                'ship_class'=>$get_ship_class->ship_class_id,
                'open_boarding_on'=>$value['open_boarding_on'],
                'close_boarding_on'=>$value['close_boarding_on'],
                'close_rampdoor_on'=>$value['close_rampdoor_on'],
                'sail_time'=>$value['sail_time'],
                'ship_id'=>$shipId,
                'schedule_code'=>$schedule_code,
                'schedule_date'=>$value['schedule_date'],
                'trip'=>$value['trip'],
                'status'=>1,
                // 'order'=>$max->max_order+1,
                'order'=>$maxCode,
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata('username'),
                );

                // mencari rute pelabuhan tujuan berdasarkan origin port 
                $get_destiny=$this->schedule->select_data("app.t_mtr_rute"," where origin='".$value['port_id']."' ")->row();

                $data_trx[]=array(
                        'schedule_date'=>$value['schedule_date'],
                        'port_id'=>$value['port_id'],
                        'dock_id'=>$value['dock_id'],
                        'destination_port_id'=> $get_destiny->destination,
                        'ship_id'=>$shipId,
                        'schedule_code'=>$schedule_code,
                        'status'=>1,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata("username"),
                );

                $indexCode++;
                $schedule_code = $frontCode. sprintf("%04s", $indexCode);


             }

            //  print_r($data_row);
            //  exit;

             $this->db->trans_begin();

              $this->schedule->insert_data_batch($this->_table,$data_row);
              $this->schedule->insert_data_batch("app.t_trx_schedule",$data_trx);
              

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



        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }
    public function action_import_excel_tanpa_vendor(){
    
        // validate_ajax();

        /*
            keterangan
            $value['A'] = Nama pelabuhan
            $value['B'] = Nama dermaga
            $value['C'] = Tanggal dan jam sandar
            $value['D'] = Tanggal dan jam buka layanan
            $value['E'] = Tanggal dan jam tutup layanan
            $value['F'] = Tanggal dan jam tutup rampdoor
            $value['G'] = Tanggal dan jam Keberangkatan
            $value['H'] = Nama Kapal
            $value['I'] = Tanggal Jadwal
            $value['J'] = Trip
        */
          // load excel
        $file = $_FILES['excel']['tmp_name'];

          $load = PHPExcel_IOFactory::load($file);
          
          $max_row = $load->getActiveSheet(0)->getHighestRow()-7;

          $true=array();
          for ($i=0; $i < $max_row ; $i++) { 
                $true[]=true;
            }

          $sheets = $load->getActiveSheet()->toArray(null,true,true,true);


          $i = 1;
          $i2 = 1;

          $empty_data=array();
          $data=array();
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/action_import_excel';
        $logMethod   = 'ADD';

        $invalid_ship=array();
        $invalid_port=array();
        $invalid_dock=array();

        $err_ship=array();
        $err_port=array();
        $err_dock=array();
        $err_ship_class=array();


        // check apakah proses waktunya lebih besar dari sebelumnya
        foreach ($sheets as $value)
        {
            // dimulai dari sheet 8
            if($i2>7)
            {
                // jika open boarding tidak kosong
                if (!empty($value['D']))
                {
                    // checking waktu open boarding tidk boleh lebih besar dari docking
                    if($value['C'] >= $value['D'])
                    {
                        echo $res=json_api(0, "Waktu buka boarding tidak boleh diatas jam sandar, baris ke {$i2} kolom D");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }
                }

                // jika close boarding tidak kosong
                if (!empty($value['E']))
                {
                    // tidak boleh di atas docking
                    if($value['C'] >= $value['E'])
                    {
                        echo $res=json_api(0, "Waktu tutup boarding tidak boleh diatas jam sandar, baris ke {$i2} kolom E");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    // jika open boarding keiisi
                    if(!empty($value['D']))
                    {
                        if($value['D']>= $value['E'])
                        {
                            echo $res=json_api(0, "Waktu tutup boarding tidak boleh diatas jam buka boarding baris ke {$i2} kolom E" );
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                }

                // validasi jika tutup rampdor diisi
                if(!empty($value['F']))
                {
                    if($value['C'] >= $value['F'])
                    {
                        echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam sandar, baris ke {$i2} kolom F");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    if(!empty($value['D']))
                    {
                        if($value['D']>=$value['F'])
                        {
                            echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam buka boarding, , baris ke {$i2} kolom F");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                    if(!empty($value['E']))
                    {
                        if($value['E']>=$value['F'])
                        {
                            echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam tutup boarding, , baris ke {$i2} kolom F");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }
                }


                // validasi jika tutup rampdor diisi
                if(!empty($value['G']))
                {
                    if($value['C'] >= $value['G'])
                    {
                        echo $res=json_api(0, "Waktu keberangkatan tidak boleh diatas jam sandar, baris ke {$i2} kolom G");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    if(!empty($value['D']))
                    {
                        if($value['D']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan ramdor tidak boleh diatas jam buka boarding, baris ke {$i2} kolom G");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                    if(!empty($value['E']))
                    {
                        if($value['E']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan ramdor tidak boleh diatas jam tutup boarding, baris ke {$i2} kolom G" );
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }


                    if(!empty($value['F']))
                    {
                        if($value['F']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan  tidak boleh diatas jam buka tutup ramdor, baris ke {$i2} kolom G");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }                        
                }

                // check jika nama kapal diisi dan dan nama kapal tidak sama di db maka akan di tolak
                if(!empty($value['H']))
                {
                    $check_ship_data=$this->schedule->select_data("app.t_mtr_ship","where status=1 and upper(name)='".strtoupper(trim($value['H']))."'");
                    if($check_ship_data->num_rows()<1)
                    {
                        echo $res=json_api(0, "Nama Kapal {$value['H']} tidak ada, baris ke {$i2} kolom H");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }
                }
            }
            
            $i2++;
        }

        // check identity app nya 
        $identity=$this->schedule->get_identity_app();
        if($identity==0)
        {
            if(!empty($this->session->userdata("port_id")))
            {
                $identity_app=$this->schedule->select_data(" app.t_mtr_port" ," where id=".$this->session->userdata("port_id"))->row();

                $get_port_identity_name=$identity_app->name;

            }
            else
            {
                $identity_app="";
                $get_port_identity_name="";
            }
        }
        else
        {
            $identity_app=$identity_app=$this->schedule->select_data(" app.t_mtr_port" ," where id=".$identity)->row();
            $get_port_identity_name=$identity_app->name;
        }


        $check_identity[]=0;

        $checkWaktu[]=0;
        $errorWaktu=array();

        $errorDuplicateInput=array();
        $errDuplicate[]=0;

        $checkShipSailing[]=0;
        $checkShipSailingMessege=array();
        
        foreach ($sheets as $sheet) {

            // karena data yang di excel di mulai dari baris ke 8
            // maka jika $i lebih dari 1 data akan di masukan ke database
            $order_data=1;
            if ($i > 7) {

                $check_ship=$this->schedule->select_data("app.t_mtr_ship","where status=1 and upper(name)='".strtoupper(trim($sheet['H']))."'");

                // checking nama portnya apakah benar ada
                $check_port=$this->schedule->select_data("app.t_mtr_port","where status=1 and  upper(name)='".trim(strtoupper($sheet['A']))."'");

                // ketika mempunyai port id maka di check apakah sesuai pelabuhanya dengan pelabuhan yang di miliki user
                if(!empty($identity_app))
                {
                    if( strtoupper($check_port->row()->name) != strtoupper($identity_app->name))
                    {
                        $check_identity[]=1;   
                    }
                }

                $check_dock=$this->schedule->val_dock("where a.status=1 and upper(b.name)='".strtoupper(trim($sheet['A']))."' and upper(a.name)='".strtoupper(trim($sheet['B']))."'");

                $port_id=$check_port->row();
                $ship_id=$check_ship->row();
                $dock_id=$check_dock->row();
                
                // echo ; exit;

                if($check_dock->num_rows()>0 && $check_ship->num_rows()>0 && $check_port->num_rows()>0 )
                {
                    
                    // check docking
                    $checkDocking= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id}  and docking_on='{$sheet['C']}' and status<>'-5' ");
                    

                    if($checkDocking->num_rows()>0)
                    {
                        $checkWaktu[]=1;
                        $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Sandar Baris ke {$i} kolom C ";
                    }
                
                    // $getCheckQry[]=" select * from app.t_mtr_schedule where dock_id={$dock_id->id} and port_id={$port_id->id}  and docking_on='{$sheet['C']}' and status<>'-5'  ";


                    // check duplikat didalam excel 
                    $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['C'],"docking");
                    if($checkDuplicateTime["code"]>0)
                    {
                        $errDuplicate[]=1;
                        $errorDuplicateInput[]= "Duplikat Jam Sandar Baris ".implode(" dan ", $checkDuplicateTime['data']);
                    }

                    if(!empty($sheet['D'])) // open boarding
                    {

                        $checkOpenBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id} and open_boarding_on='{$sheet['D']}' and status<>'-5' ");

                        if($checkOpenBoarding->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Buka Layanan Baris ke {$i} kolom D ";
                        }
    
                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['D'],"openBoarding");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Buka Layanan  Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                        
                    }

                    if(!empty($sheet['E'])) // close boarding
                    {
                        $checkCloseBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id}  and close_boarding_on='{$sheet['E']}' and status<>'-5' ");

                        if($checkCloseBoarding->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Tutup Layanan Baris ke {$i} kolom E ";
                        } 

                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['E'],"closeBoarding");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Tutup Layanan  Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                                                
                    }

                    if(!empty($sheet['F'])) // close rampdoor
                    {
                        $checkCloseRamdoor= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id} and close_rampdoor_on='{$sheet['F']}' and status<>'-5' ");

                        if($checkCloseRamdoor->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Tutup Rampdoor Baris ke {$i} kolom C ";
                        } 

                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['F'],"closeRampdoor");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Tutup Rampdoor  Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                                                          
                    }     

                    if(!empty($sheet['G'])) // sail
                    {
                        $checkSailTime= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id->id} and sail_time='{$sheet['G']}' and status<>'-5' ");

                        if($checkSailTime->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Keberangkatan Baris ke {$i} kolom G ";
                        } 

                        // check duplikat didalam excel 
                        $checkDuplicateTime=$this->checkDuplicateTime($sheets,$sheet['B'],$sheet['G'],"sail");
                        if($checkDuplicateTime["code"]>0)
                        {
                            $errDuplicate[]=1;
                            $errorDuplicateInput[]= "Duplikat Jam Berangkat Baris ".implode(" dan ", $checkDuplicateTime['data']);
                        }                           
                    }                                                            

                }
                // $ship_class_id=$check_ship_class->row();


                $data[]=array(
                    'port_id'=>empty($port_id->id)?"":$port_id->id,
                    'dock_id'=>empty($dock_id->id)?"":$dock_id->id,
                    'docking_on'=>$sheet['C'],
                    // 'ship_class'=>empty($ship_class_id->id)?"":$ship_class_id->id,
                    'open_boarding_on'=>$sheet['D'],
                    'close_boarding_on'=>$sheet['E'],
                    'close_rampdoor_on'=>$sheet['F'],
                    'sail_time'=>$sheet['G'],
                    'ship_id'=>empty($ship_id->id)?"":$ship_id->id,
                    'schedule_code'=>$this->createCode(empty($port_id->id)?"":$port_id->id),
                    'schedule_date'=>$sheet['I'],
                    'trip'=>$sheet['J'],
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    );


                if(empty($sheet['A']) || empty($sheet['B']) || empty($sheet['C']) ||  empty($sheet['I']) || empty($sheet['J']) )
                {
                    $empty_data[]=1;   
                }

                else if($check_port->num_rows()<1)
                {
                    $invalid_port[]=$sheet['A'];
                    $err_port[]=1;
                }
                else if($check_dock->num_rows()<1)
                {
                    $invalid_dock[]=$sheet['B']." di pelabuhan ".$sheet['A'];;
                    $invalid_dock_port[]=$sheet['A'];
                    $err_dock[]=1;
                }
                
                $order_data++;
            }

            $i++;
        }

          // echo array_sum($err_ship_class);
          // exit;
        //   print_r($getCheckQry);
        //   exit;


        if(array_sum($empty_data)>0)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if (array_sum($check_identity)>0) 
        {
            echo $res=json_api(0, ' Tidak bisa menambahkan jadwal lain, selain pelabuhan '.$get_port_identity_name);            
        }
        else if(array_sum($err_port)>0)
        {
            echo $res=json_api(0, 'Nama pelabuhan '.implode(", ",array_unique($invalid_port)).' tidak ada');
        }
        else if(array_sum($err_dock)>0)
        {
            echo $res=json_api(0, 'Nama dermaga '.implode(", ",array_unique($invalid_dock)).' tidak ada');
        }
        else if(array_sum($checkWaktu)>0)
        {
            // $unique= array_unique($errorWaktu);
            // $implode =implode(", ",$unique);

            $implode =implode("<br> ",$errorWaktu);
            echo $res=json_api(0, 'Tanggal dan Waktu tidak boleh sama dalam satu dermaga <br> '.$implode);
        }
        else if(array_sum($errDuplicate)>0)
        {
            echo $res=json_api(0, "Duplikasi  Input <br>".implode(",<br> ",array_unique($errorDuplicateInput))." " );   
        }           
        else
        {
            $this->db->trans_begin();


             foreach ($data as $key => $value) {
                 
                $schedule=$value['schedule_date'];

                $max=$this->db->query("select max(".'"order"'.") as max_order from app.t_mtr_schedule where schedule_date='".$schedule."' and port_id=".$value['port_id']." and dock_id=".$value['dock_id']." and status=1")->row();

                $schedule_code=$this->createCode($value['port_id']);

                // ambil ship class berdasarkan docknya
                $get_ship_class=$this->schedule->select_data("app.t_mtr_dock"," where id=".$value['dock_id']."")->row();

                empty($value['ship_id'])?$shipId=NULL:$shipId=$value['ship_id'];

                $data_row=array(
                'port_id'=>$value['port_id'],
                'dock_id'=>$value['dock_id'],
                'docking_on'=>$value['docking_on'],
                'ship_class'=>$get_ship_class->ship_class_id,
                'open_boarding_on'=>$value['open_boarding_on'],
                'close_boarding_on'=>$value['close_boarding_on'],
                'close_rampdoor_on'=>$value['close_rampdoor_on'],
                'sail_time'=>$value['sail_time'],
                'ship_id'=>$shipId,
                'schedule_code'=>$schedule_code,
                'schedule_date'=>$value['schedule_date'],
                'trip'=>$value['trip'],
                'status'=>1,
                'order'=>$max->max_order+1,
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata('username'),
                );

                // mencari rute pelabuhan tujuan berdasarkan origin port 
                $get_destiny=$this->schedule->select_data("app.t_mtr_rute"," where origin='".$value['port_id']."' ")->row();

                $data_trx=array(
                        'schedule_date'=>$value['schedule_date'],
                        'port_id'=>$value['port_id'],
                        'dock_id'=>$value['dock_id'],
                        'destination_port_id'=> $get_destiny->destination,
                        'ship_id'=>$shipId,
                        'schedule_code'=>$schedule_code,
                        'status'=>1,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata("username"),
                );


                $this->db->insert($this->_table,$data_row);
                $this->db->insert("app.t_trx_schedule",$data_trx);
             }

             // print_r($data_row);
             // exit;

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



        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }
    public function action_import_excel_27092021(){
    
        // validate_ajax();

        /*
            keterangan
            $value['A'] = Nama pelabuhan
            $value['B'] = Nama dermaga
            $value['C'] = Tanggal dan jam sandar
            $value['D'] = Tanggal dan jam buka layanan
            $value['E'] = Tanggal dan jam tutup layanan
            $value['F'] = Tanggal dan jam tutup rampdoor
            $value['G'] = Tanggal dan jam Keberangkatan
            $value['H'] = Nama Kapal
            $value['I'] = Tanggal Jadwal
            $value['J'] = Trip
        */
          // load excel
        $file = $_FILES['excel']['tmp_name'];

          $load = PHPExcel_IOFactory::load($file);
          
          $max_row = $load->getActiveSheet(0)->getHighestRow()-7;

          $true=array();
          for ($i=0; $i < $max_row ; $i++) { 
                $true[]=true;
            }

          $sheets = $load->getActiveSheet()->toArray(null,true,true,true);


          $i = 1;
          $i2 = 1;

          $empty_data=array();
          $data=array();
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/action_import_excel';
        $logMethod   = 'ADD';

        $invalid_ship=array();
        $invalid_port=array();
        $invalid_dock=array();

        $err_ship=array();
        $err_port=array();
        $err_dock=array();
        $err_ship_class=array();


        // check apakah proses waktunya lebih besar dari sebelumnya
        foreach ($sheets as $value)
        {
            // dimulai dari sheet 8
            if($i2>7)
            {
                // jika open boarding tidak kosong
                if (!empty($value['D']))
                {
                    // checking waktu open boarding tidk boleh lebih besar dari docking
                    if($value['C'] >= $value['D'])
                    {
                        echo $res=json_api(0, "Waktu buka boarding tidak boleh diatas jam sandar, baris ke {$i2} kolom D");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }
                }

                // jika close boarding tidak kosong
                if (!empty($value['E']))
                {
                    // tidak boleh di atas docking
                    if($value['C'] >= $value['E'])
                    {
                        echo $res=json_api(0, "Waktu tutup boarding tidak boleh diatas jam sandar, baris ke {$i2} kolom E");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    // jika open boarding keiisi
                    if(!empty($value['D']))
                    {
                        if($value['D']>= $value['E'])
                        {
                            echo $res=json_api(0, "Waktu tutup boarding tidak boleh diatas jam buka boarding baris ke {$i2} kolom E" );
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                }

                // validasi jika tutup rampdor diisi
                if(!empty($value['F']))
                {
                    if($value['C'] >= $value['F'])
                    {
                        echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam sandar, baris ke {$i2} kolom F");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    if(!empty($value['D']))
                    {
                        if($value['D']>=$value['F'])
                        {
                            echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam buka boarding, , baris ke {$i2} kolom F");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                    if(!empty($value['E']))
                    {
                        if($value['E']>=$value['F'])
                        {
                            echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam tutup boarding, , baris ke {$i2} kolom F");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }
                }


                // validasi jika tutup rampdor diisi
                if(!empty($value['G']))
                {
                    if($value['C'] >= $value['G'])
                    {
                        echo $res=json_api(0, "Waktu keberangkatan tidak boleh diatas jam sandar, baris ke {$i2} kolom G");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }

                    if(!empty($value['D']))
                    {
                        if($value['D']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan ramdor tidak boleh diatas jam buka boarding, baris ke {$i2} kolom G");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }

                    if(!empty($value['E']))
                    {
                        if($value['E']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan ramdor tidak boleh diatas jam tutup boarding, baris ke {$i2} kolom G" );
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }


                    if(!empty($value['F']))
                    {
                        if($value['F']>=$value['G'])
                        {
                            echo $res=json_api(0, "Waktu keberangkatan  tidak boleh diatas jam buka tutup ramdor, baris ke {$i2} kolom G");
                            $logParam    = json_encode($data);
                            $logResponse = $res;
                            $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                            exit;
                        }
                    }                        
                }

                // check jika nama kapal diisi dan dan nama kapal tidak sama di db maka akan di tolak
                if(!empty($value['H']))
                {
                    $check_ship_data=$this->schedule->select_data("app.t_mtr_ship","where status=1 and upper(name)='".strtoupper(trim($value['H']))."'");
                    if($check_ship_data->num_rows()<1)
                    {
                        echo $res=json_api(0, "Nama Kapal {$value['H']} tidak ada, baris ke {$i2} kolom H");
                        $logParam    = json_encode($data);
                        $logResponse = $res;
                        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                        exit;
                    }
                }
            }
            
            $i2++;
        }

        // check identity app nya 
        $identity=$this->schedule->get_identity_app();
        if($identity==0)
        {
            if(!empty($this->session->userdata("port_id")))
            {
                $identity_app=$this->schedule->select_data(" app.t_mtr_port" ," where id=".$this->session->userdata("port_id"))->row();

                $get_port_identity_name=$identity_app->name;

            }
            else
            {
                $identity_app="";
                $get_port_identity_name="";
            }
        }
        else
        {
            $identity_app=$identity_app=$this->schedule->select_data(" app.t_mtr_port" ," where id=".$identity)->row();
            $get_port_identity_name=$identity_app->name;
        }


        $check_identity[]=0;

        $checkWaktu[]=0;
        $errorWaktu=array();
        foreach ($sheets as $sheet) {

            // karena data yang di excel di mulai dari baris ke 8
            // maka jika $i lebih dari 1 data akan di masukan ke database
            $order_data=1;
            if ($i > 7) {

                $check_ship=$this->schedule->select_data("app.t_mtr_ship","where status=1 and upper(name)='".strtoupper(trim($sheet['H']))."'");

                // checking nama portnya apakah benar ada
                $check_port=$this->schedule->select_data("app.t_mtr_port","where status=1 and  upper(name)='".trim(strtoupper($sheet['A']))."'");

                // ketika mempunyai port id maka di check apakah sesuai pelabuhanya dengan pelabuhan yang di miliki user
                if(!empty($identity_app))
                {
                    if( strtoupper($check_port->row()->name) != strtoupper($identity_app->name))
                    {
                        $check_identity[]=1;   
                    }
                }

                $check_dock=$this->schedule->val_dock("where a.status=1 and upper(b.name)='".strtoupper(trim($sheet['A']))."' and upper(a.name)='".strtoupper(trim($sheet['B']))."'");

                $port_id=$check_port->row();
                $ship_id=$check_ship->row();
                $dock_id=$check_dock->row();

                if($check_dock->num_rows()>0 && $check_ship->num_rows()>0 && $check_port->num_rows()>0 )
                {
                    
                    // check docking
                    $checkDocking= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id} and docking_on='{$sheet['C']}' and status<>'-5' ");

                    if($checkDocking->num_rows()>0)
                    {
                        $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Sandar Baris ke {$i} kolom C ";
                    } 

                    if(!empty($sheet['D'])) // open boarding
                    {
                        $checkOpenBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id} and  open_boarding_on='{$sheet['D']}' and status<>'-5' ");

                        if($checkOpenBoarding->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Buka Layanan Baris ke {$i} kolom D ";
                        } 
                    }

                    if(!empty($sheet['E'])) // close boarding
                    {
                        $checkCloseBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id} and close_boarding_on='{$sheet['E']}' and status<>'-5' ");

                        if($checkCloseBoarding->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Tutup Layanan Baris ke {$i} kolom E ";
                        } 
                    }

                    if(!empty($sheet['F'])) // close rampdoor
                    {
                        $checkCloseRamdoor= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id} and close_rampdoor_on='{$sheet['F']}' and status<>'-5' ");

                        if($checkCloseRamdoor->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Tutup Rampdoor Baris ke {$i} kolom C ";
                        } 
                    }     

                    if(!empty($sheet['G'])) // close rampdoor
                    {
                        $checkSailTime= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id->id} and port_id={$port_id} and sail_time='{$sheet['G']}' and status<>'-5' ");

                        if($checkSailTime->num_rows()>0)
                        {
                            $checkWaktu[]=1;
                            $errorWaktu[]="<i class='fa fa-circle' ></i> Jam Keberangkatan Baris ke {$i} kolom G ";
                        } 
                    }                                                            

                }
                // $ship_class_id=$check_ship_class->row();


                $data[]=array(
                    'port_id'=>empty($port_id->id)?"":$port_id->id,
                    'dock_id'=>empty($dock_id->id)?"":$dock_id->id,
                    'docking_on'=>$sheet['C'],
                    // 'ship_class'=>empty($ship_class_id->id)?"":$ship_class_id->id,
                    'open_boarding_on'=>$sheet['D'],
                    'close_boarding_on'=>$sheet['E'],
                    'close_rampdoor_on'=>$sheet['F'],
                    'sail_time'=>$sheet['G'],
                    'ship_id'=>empty($ship_id->id)?"":$ship_id->id,
                    'schedule_code'=>$this->createCode(empty($port_id->id)?"":$port_id->id),
                    'schedule_date'=>$sheet['I'],
                    'trip'=>$sheet['J'],
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    );


                if(empty($sheet['A']) || empty($sheet['B']) || empty($sheet['C']) ||  empty($sheet['I']) || empty($sheet['J']) )
                {
                    $empty_data[]=1;   
                }

                else if($check_port->num_rows()<1)
                {
                    $invalid_port[]=$sheet['A'];
                    $err_port[]=1;
                }
                else if($check_dock->num_rows()<1)
                {
                    $invalid_dock[]=$sheet['B']." di pelabuhan ".$sheet['A'];;
                    $invalid_dock_port[]=$sheet['A'];
                    $err_dock[]=1;
                }
                
                $order_data++;
            }

            $i++;
        }

          // echo array_sum($err_ship_class);
          // exit;
          // print_r($data);
          // exit;

        if(array_sum($empty_data)>0)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if (array_sum($check_identity)>0) 
        {
            echo $res=json_api(0, ' Tidak bisa menambahkan jadwal lain, selain pelabuhan '.$get_port_identity_name);            
        }
        else if(array_sum($err_port)>0)
        {
            echo $res=json_api(0, 'Nama pelabuhan '.implode(", ",array_unique($invalid_port)).' tidak ada');
        }
        else if(array_sum($err_dock)>0)
        {
            echo $res=json_api(0, 'Nama dermaga '.implode(", ",array_unique($invalid_dock)).' tidak ada');
        }
        else if(array_sum($checkWaktu)>0)
        {
            // $unique= array_unique($errorWaktu);
            // $implode =implode(", ",$unique);

            $implode =implode("<br> ",$errorWaktu);
            echo $res=json_api(0, 'Tanggal dan Waktu tidak boleh sama dalam satu dermaga <br> '.$implode);
        }
        else
        {
            $this->db->trans_begin();


             foreach ($data as $key => $value) {
                 
                $schedule=$value['schedule_date'];

                $max=$this->db->query("select max(".'"order"'.") as max_order from app.t_mtr_schedule where schedule_date='".$schedule."' and port_id=".$value['port_id']." and dock_id=".$value['dock_id']." and status=1")->row();

                $schedule_code=$this->createCode($value['port_id']);

                // ambil ship class berdasarkan docknya
                $get_ship_class=$this->schedule->select_data("app.t_mtr_dock"," where id=".$value['dock_id']."")->row();

                empty($value['ship_id'])?$shipId=NULL:$shipId=$value['ship_id'];

                $data_row=array(
                'port_id'=>$value['port_id'],
                'dock_id'=>$value['dock_id'],
                'docking_on'=>$value['docking_on'],
                'ship_class'=>$get_ship_class->ship_class_id,
                'open_boarding_on'=>$value['open_boarding_on'],
                'close_boarding_on'=>$value['close_boarding_on'],
                'close_rampdoor_on'=>$value['close_rampdoor_on'],
                'sail_time'=>$value['sail_time'],
                'ship_id'=>$shipId,
                'schedule_code'=>$schedule_code,
                'schedule_date'=>$value['schedule_date'],
                'trip'=>$value['trip'],
                'status'=>1,
                'order'=>$max->max_order+1,
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata('username'),
                );

                // mencari rute pelabuhan tujuan berdasarkan origin port 
                $get_destiny=$this->schedule->select_data("app.t_mtr_rute"," where origin='".$value['port_id']."' ")->row();

                $data_trx=array(
                        'schedule_date'=>$value['schedule_date'],
                        'port_id'=>$value['port_id'],
                        'dock_id'=>$value['dock_id'],
                        'destination_port_id'=> $get_destiny->destination,
                        'ship_id'=>$shipId,
                        'schedule_code'=>$schedule_code,
                        'status'=>1,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata("username"),
                );


                $this->db->insert($this->_table,$data_row);
                $this->db->insert("app.t_trx_schedule",$data_trx);
             }

             // print_r($data_row);
             // exit;

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



        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }

    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $schedule_id=$this->enc->decode($id);
        $row=$this->schedule->select_data($this->_table,"where id=".$schedule_id)->row();
        $row->docking_on=empty($row->docking_on)?"":date("Y-m-d H:i", strtotime($row->docking_on));
        $row->open_boarding_on=empty($row->open_boarding_on)?"":date("Y-m-d H:i", strtotime($row->open_boarding_on));
        $row->close_boarding_on=empty($row->close_boarding_on)?"":date("Y-m-d H:i", strtotime($row->close_boarding_on));
        $row->close_rampdoor_on=empty($row->close_rampdoor_on)?"":date("Y-m-d H:i", strtotime($row->close_rampdoor_on));
        $row->sail_time=empty($row->sail_time)?"":date("Y-m-d H:i", strtotime($row->sail_time));
        $detail=$row;

        $data['title'] = 'Edit Jadwal';
        $data['port']=$this->schedule->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['ship']=$this->schedule->ship()->result();
        $data['dock']=$this->schedule->select_data("app.t_mtr_dock","where status='1' and port_id=".$row->port_id." order by name asc")->result();
        $data['detail']=$detail;
        $data['tipe_kapal']=$this->schedule->select_data("app.t_mtr_ship_class","where status='1' order by name asc")->result();
        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $schedule_id=$this->enc->decode($this->input->post('schedule_code'));

        $port_id=$this->enc->decode($this->input->post('port'));
        $dock_id=$this->enc->decode($this->input->post('dock'));
        $ship_id=$this->enc->decode($this->input->post('ship'));
        $ship_class=$this->enc->decode($this->input->post('class'));
        $schedule=trim($this->input->post('schedule'));
        $trip=trim($this->input->post('trip'));
        $docking_on=trim($this->input->post('docking_on'));
        $open_boarding=trim($this->input->post('open_boarding'));
        $close_boarding=trim($this->input->post('close_boarding'));
        $sail_time=trim($this->input->post('sail_time'));
        $close_ramdoor=trim($this->input->post('close_ramdoor'));

        $this->form_validation->set_rules('dock', 'dermaga', 'required');
        $this->form_validation->set_rules('port', 'pelabuhan', 'required');
        // $this->form_validation->set_rules('ship', 'Kapal', 'required');
        $this->form_validation->set_rules('schedule', 'jadwal tanggal', 'required');
        $this->form_validation->set_rules('docking_on', 'waktu sandar', 'required');
        // $this->form_validation->set_rules('open_boarding', 'waktu boarding', 'required');
        // $this->form_validation->set_rules('close_boarding', 'tutup boarding', 'required');
        // $this->form_validation->set_rules('sail_time', 'waktu berlayar', 'required');
        // $this->form_validation->set_rules('close_ramdoor', 'waktu tutup ramdoor', 'required');
        $this->form_validation->set_rules('trip', 'waktu tutup ramdoor', 'required');
        $this->form_validation->set_rules('class', 'ship_class', 'required');
                
        $data=array(
                    'port_id'=>$port_id,
                    'dock_id'=>$dock_id,
                    'docking_on'=>$docking_on,
                    'ship_class'=>$ship_class,
                    'open_boarding_on'=>empty($open_boarding)?NULL:$open_boarding,
                    'close_boarding_on'=>empty($close_boarding)?NULL:$close_boarding,
                    'close_rampdoor_on'=>empty($close_ramdoor)?NULL:$close_ramdoor,
                    'sail_time'=>empty($sail_time)?NULL:$sail_time,
                    'ship_id'=>empty($ship_id)?NULL:$ship_id,
                    'schedule_date'=>$schedule,
                    'trip'=>$trip,
                    'updated_on'=>date("Y-m-d H:i:s"),
                    'updated_by'=>$this->session->userdata('username'),
                    );

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/action_edit';
        $logMethod   = 'EDIT';
        
        $checkShipSailing[]=0;

        if(!empty($ship_id))
        {
            $shipId=$ship_id;
            if(!empty($port_id))
            {
                $checkShipSailing[]= $this->schedule->checkShipSailing($ship_id, $port_id); // check apakah kapal ini punya sailing code
            }
        }
        else
        {
            $shipId=NULL;
        }

        $data_trx=array(
            'port_id'=>$port_id,
            'dock_id'=>$dock_id,
            'ship_id'=>$shipId,
            'schedule_date'=>$schedule,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
                    );

                            


        $checkWaktu[]=0;
        $errorWaktu=array();        

        if(!empty($dock_id))
        {
            

            $checkDocking= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id} and docking_on='{$docking_on}' and status<>'-5' and schedule_code !='{$schedule_id}' ");
            if($checkDocking->num_rows()>0)
            {
                $checkWaktu[]=1;
                $errorWaktu[]="SANDAR";
            } 
        }                    


        if (!empty($open_boarding))
        {

            if(!empty($dock_id))
            {

                $checkOpenBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id} and open_boarding_on='{$open_boarding}' and status<>'-5' and schedule_code !='{$schedule_id}' ");

                if($checkOpenBoarding->num_rows()>0)
                {
                    $checkWaktu[]=1;
                    $errorWaktu[]="BUKA LAYANAN";
                } 
            }            
            // checking waktu open boarding tidk boleh lebih besar dari docking
            if($docking_on >= $open_boarding)
            {
                echo $res=json_api(0, 'Waktu buka boarding tidak boleh diatas jam sandar');
                $logParam    = json_encode($data);
                $logResponse = $res;
                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                exit;
            }
        }

        // jika close boarding tidak kosong
        if (!empty($close_boarding))
        {
            if(!empty($dock_id))
            {

                $checkCloseBoarding= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id} and close_boarding_on='{$close_boarding}' and status<>'-5' and schedule_code !='{$schedule_id}' ");

                if($checkCloseBoarding->num_rows()>0)
                {
                    $checkWaktu[]=1;
                    $errorWaktu[]="TUTUP LAYANAN";
                } 
            }

            if($docking_on >= $close_boarding)
            {
                echo $res=json_api(0, 'Waktu tutup boarding tidak boleh diatas jam sandar');
                $logParam    = json_encode($data);
                $logResponse = $res;
                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                exit;
            }

            if(!empty($open_boarding))
            {
                if($open_boarding>= $close_boarding)
                {
                    echo $res=json_api(0, 'Waktu tutup boarding tidak boleh diatas jam buka boarding');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }

        }


        // validasi jika tutup rampdor diisi
        if(!empty($close_ramdoor))
        {

            if(!empty($dock_id))
            {

                $checkCloseRamdoor= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id} and close_rampdoor_on='{$close_ramdoor}' and status<>'-5' and schedule_code !='{$schedule_id}' ");

                if($checkCloseRamdoor->num_rows()>0)
                {
                    $checkWaktu[]=1;
                    $errorWaktu[]="TUTUP RAMDOOR";
                } 
            }

            if($docking_on >= $close_ramdoor)
            {
                echo $res=json_api(0, 'Waktu tutup ramdor tidak boleh diatas jam sandar');
                $logParam    = json_encode($data);
                $logResponse = $res;
                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                exit;
            }

            if(!empty($open_boarding))
            {
                if($open_boarding>=$close_ramdoor)
                {
                    echo $res=json_api(0, 'Waktu tutup ramdor tidak boleh diatas jam buka boarding');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }

            if(!empty($close_boarding))
            {
                if($close_boarding>=$close_ramdoor)
                {
                    echo $res=json_api(0, 'Waktu tutup ramdor tidak boleh diatas jam tutup boarding');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }
        }


        // validasi jika tutup rampdor diisi
        if(!empty($sail_time))
        {
            if(!empty($dock_id))
            {

                $checkSailTime= $this->schedule->select_data("app.t_mtr_schedule"," where dock_id={$dock_id} and sail_time='{$sail_time}' and status<>'-5' and schedule_code !='{$schedule_id}' ");

                if($checkSailTime->num_rows()>0)
                {
                    $checkWaktu[]=1;
                    $errorWaktu[]="KEBERANGKATAN";
                } 
            }

            if($docking_on >= $sail_time)
            {
                echo $res=json_api(0, 'Waktu keberangkatan tidak boleh diatas jam sandar');
                $logParam    = json_encode($data);
                $logResponse = $res;
                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                exit;
            }

            if(!empty($open_boarding))
            {
                if($open_boarding>=$sail_time)
                {
                    echo $res=json_api(0, 'Waktu keberangkatan ramdor tidak boleh diatas jam buka boarding');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }

            if(!empty($close_boarding))
            {
                if($close_boarding>=$sail_time)
                {
                    echo $res=json_api(0, 'Waktu keberangkatan ramdor tidak boleh diatas jam tutup boarding');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }


            if(!empty($close_ramdoor))
            {
                if($close_ramdoor>=$sail_time)
                {
                    echo $res=json_api(0, 'Waktu keberangkatan  tidak boleh diatas jam buka tutup ramdor');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }


        }

        // print_r($checkWaktu); exit;


        // check schedule 
        $trx_schedule=$this->schedule->select_data("app.t_trx_schedule"," where schedule_code='".$schedule_id."'")->row();
        
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if (array_sum($checkShipSailing)>0)
        {
            echo $res=json_api(0, 'Kapal Belum Mempunyai Kode Pelayaran di pelabuhan  ');
        }        
        else if(array_sum($checkWaktu)>0)
        {
            $implode =implode(", ", $errorWaktu);
            echo $res=json_api(0, 'Tanggal dan Waktu '.$implode.' tidak boleh sama dalam satu dermaga');
        }        
        // else if($docking_on > $open_boarding)
        // {
        //     echo $res=json_api(0, 'Waktu buka layanan tidak boleh diatas jam sandar');
        // }
        // else if($open_boarding > $close_boarding)
        // {
        //     echo $res=json_api(0, 'Waktu tutup layanan tidak boleh diatas jam buka boarding');
        // }
        // else if($close_boarding > $close_ramdoor)
        // {
        //     echo $res=json_api(0, 'Waktu tutup rampdoor tidak boleh diatas jam tutup boarding');
        // }
        // else if($close_ramdoor > $sail_time)
        // {
        //     echo $res=json_api(0, 'Waktu keberangkatan tidak boleh diatas jam Waktu tutup rampdoor');
        // }

        else if(!empty($trx_schedule->ploting_date) || !empty($trx_schedule->docking_date) || !empty($trx_schedule->open_boarding_date) || !empty($trx_schedule->close_boarding_date) || !empty($trx_schedule->close_rampdoor_date) ||!empty($trx_schedule->sail_date) )
        {
            echo $res=json_api(0, 'Gagal edit, jadwal sudah melakukan transaksi');   
        }
        else
        {
            $this->db->trans_begin();
            $this->schedule->update_data($this->_table,$data,"schedule_code='".$schedule_id."'");
            $this->schedule->update_data("app.t_trx_schedule",$data_trx,"schedule_code='".$schedule_id."'");

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data ');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data');
            }   
        }


        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_edit_27092021()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $schedule_id=$this->enc->decode($this->input->post('schedule_code'));

        $port_id=$this->enc->decode($this->input->post('port'));
        $dock_id=$this->enc->decode($this->input->post('dock'));
        $ship_id=$this->enc->decode($this->input->post('ship'));
        $ship_class=$this->enc->decode($this->input->post('class'));
        $schedule=trim($this->input->post('schedule'));
        $trip=trim($this->input->post('trip'));
        $docking_on=trim($this->input->post('docking_on'));
        $open_boarding=trim($this->input->post('open_boarding'));
        $close_boarding=trim($this->input->post('close_boarding'));
        $sail_time=trim($this->input->post('sail_time'));
        $close_ramdoor=trim($this->input->post('close_ramdoor'));

        $this->form_validation->set_rules('dock', 'dermaga', 'required');
        $this->form_validation->set_rules('port', 'pelabuhan', 'required');
        // $this->form_validation->set_rules('ship', 'Kapal', 'required');
        $this->form_validation->set_rules('schedule', 'jadwal tanggal', 'required');
        $this->form_validation->set_rules('docking_on', 'waktu sandar', 'required');
        // $this->form_validation->set_rules('open_boarding', 'waktu boarding', 'required');
        // $this->form_validation->set_rules('close_boarding', 'tutup boarding', 'required');
        // $this->form_validation->set_rules('sail_time', 'waktu berlayar', 'required');
        // $this->form_validation->set_rules('close_ramdoor', 'waktu tutup ramdoor', 'required');
        $this->form_validation->set_rules('trip', 'waktu tutup ramdoor', 'required');
        $this->form_validation->set_rules('class', 'ship_class', 'required');
                
        $data=array(
                    'port_id'=>$port_id,
                    'dock_id'=>$dock_id,
                    'docking_on'=>$docking_on,
                    'ship_class'=>$ship_class,
                    'open_boarding_on'=>empty($open_boarding)?NULL:$open_boarding,
                    'close_boarding_on'=>empty($close_boarding)?NULL:$close_boarding,
                    'close_rampdoor_on'=>empty($close_ramdoor)?NULL:$close_ramdoor,
                    'sail_time'=>empty($sail_time)?NULL:$sail_time,
                    'ship_id'=>empty($ship_id)?NULL:$ship_id,
                    'schedule_date'=>$schedule,
                    'trip'=>$trip,
                    'updated_on'=>date("Y-m-d H:i:s"),
                    'updated_by'=>$this->session->userdata('username'),
                    );

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/action_edit';
        $logMethod   = 'EDIT';

        $data_trx=array(
            'port_id'=>$port_id,
            'dock_id'=>$dock_id,
            'ship_id'=>empty($ship_id)?NULL:$ship_id,
            'schedule_date'=>$schedule,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
                    );


        if (!empty($open_boarding))
        {
            // checking waktu open boarding tidk boleh lebih besar dari docking
            if($docking_on >= $open_boarding)
            {
                echo $res=json_api(0, 'Waktu buka boarding tidak boleh diatas jam sandar');
                $logParam    = json_encode($data);
                $logResponse = $res;
                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                exit;
            }
        }

        // jika close boarding tidak kosong
        if (!empty($close_boarding))
        {

            if($docking_on >= $close_boarding)
            {
                echo $res=json_api(0, 'Waktu tutup boarding tidak boleh diatas jam sandar');
                $logParam    = json_encode($data);
                $logResponse = $res;
                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                exit;
            }

            if(!empty($open_boarding))
            {
                if($open_boarding>= $close_boarding)
                {
                    echo $res=json_api(0, 'Waktu tutup boarding tidak boleh diatas jam buka boarding');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }

        }


        // validasi jika tutup rampdor diisi
        if(!empty($close_ramdoor))
        {
            if($docking_on >= $close_ramdoor)
            {
                echo $res=json_api(0, 'Waktu tutup ramdor tidak boleh diatas jam sandar');
                $logParam    = json_encode($data);
                $logResponse = $res;
                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                exit;
            }

            if(!empty($open_boarding))
            {
                if($open_boarding>=$close_ramdoor)
                {
                    echo $res=json_api(0, 'Waktu tutup ramdor tidak boleh diatas jam buka boarding');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }

            if(!empty($close_boarding))
            {
                if($close_boarding>=$close_ramdoor)
                {
                    echo $res=json_api(0, 'Waktu tutup ramdor tidak boleh diatas jam tutup boarding');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }
        }


        // validasi jika tutup rampdor diisi
        if(!empty($sail_time))
        {
            if($docking_on >= $sail_time)
            {
                echo $res=json_api(0, 'Waktu keberangkatan tidak boleh diatas jam sandar');
                $logParam    = json_encode($data);
                $logResponse = $res;
                $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                exit;
            }

            if(!empty($open_boarding))
            {
                if($open_boarding>=$sail_time)
                {
                    echo $res=json_api(0, 'Waktu keberangkatan ramdor tidak boleh diatas jam buka boarding');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }

            if(!empty($close_boarding))
            {
                if($close_boarding>=$sail_time)
                {
                    echo $res=json_api(0, 'Waktu keberangkatan ramdor tidak boleh diatas jam tutup boarding');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }


            if(!empty($close_ramdoor))
            {
                if($close_ramdoor>=$sail_time)
                {
                    echo $res=json_api(0, 'Waktu keberangkatan  tidak boleh diatas jam buka tutup ramdor');
                    $logParam    = json_encode($data);
                    $logResponse = $res;
                    $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
                    exit;
                }
            }


        }




        // check schedule 
        $trx_schedule=$this->schedule->select_data("app.t_trx_schedule"," where schedule_code='".$schedule_id."'")->row();
        
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        // else if($docking_on > $open_boarding)
        // {
        //     echo $res=json_api(0, 'Waktu buka layanan tidak boleh diatas jam sandar');
        // }
        // else if($open_boarding > $close_boarding)
        // {
        //     echo $res=json_api(0, 'Waktu tutup layanan tidak boleh diatas jam buka boarding');
        // }
        // else if($close_boarding > $close_ramdoor)
        // {
        //     echo $res=json_api(0, 'Waktu tutup rampdoor tidak boleh diatas jam tutup boarding');
        // }
        // else if($close_ramdoor > $sail_time)
        // {
        //     echo $res=json_api(0, 'Waktu keberangkatan tidak boleh diatas jam Waktu tutup rampdoor');
        // }

        else if(!empty($trx_schedule->ploting_date) || !empty($trx_schedule->docking_date) || !empty($trx_schedule->open_boarding_date) || !empty($trx_schedule->close_boarding_date) || !empty($trx_schedule->close_rampdoor_date) ||!empty($trx_schedule->sail_date) )
        {
            echo $res=json_api(0, 'Gagal edit, jadwal sudah melakukan transaksi');   
        }
        else
        {
            $this->db->trans_begin();
            $this->schedule->update_data($this->_table,$data,"schedule_code='".$schedule_id."'");
            $this->schedule->update_data("app.t_trx_schedule",$data_trx,"schedule_code='".$schedule_id."'");

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data ');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data');
            }   
        }


        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $team_id = $this->enc->decode($id);

            $this->db->trans_begin();
            $this->schedule->update_data($this->_table,$data,"id=$team_id");

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal delete data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil delete data');
            }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->team_model->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal non aktif');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil non aktif data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function create_code()
    {
        $data=$this->db->query("SELECT 
                    SUBSTRING(EXTRACT(YEAR FROM now())::varchar, 3,2)||
                     to_char(EXTRACT(DAY FROM now()), 'fm000')|| 
                    (to_char(nextval('core.t_mtr_team_code_seq'), 'fm0000')) as code ")->row();

        return $data->code;

    }

    public function enable($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);

        $d = explode('|', $p);

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


            $this->db->trans_begin();
            $this->schedule->update_data($this->_table,$data,"id=".$d[0]);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal aktifkan data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil aktifkan data');
            }


        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/enable';
        $logMethod   = 'Enable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function disable($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);

        $d = explode('|', $p);

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->schedule->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal dinonaktifkan data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Data berhasil dinonaktifkan ');
        } 

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/enable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function get_dock()
    {
        $port_id=$this->enc->decode($this->input->post('port'));

        empty($port_id)?$id='null':$id=$port_id;

        $row=$this->schedule->select_data("app.t_mtr_dock","where status=1 and port_id=".$id." order by name asc")->result();

        $data=array();
        foreach ($row as $key => $value) {
            $value->id=$this->enc->encode($value->id);
            $value->name=strtoupper($value->name);

            $data[]=$value;
        }

        echo json_encode($data);
    }

    function get_ship_class()
    {
        $dock_id=$this->enc->decode($this->input->post('dock'));

        empty($dock_id)?$id='null':$id=$dock_id;

        $row=$this->schedule->select_data("app.t_mtr_dock","where id={$id} ")->row();

        if(empty($row->ship_class_id))
        {
            $data=array('id'=>"",'name'=>"Pilih");
            echo json_encode($data);
            exit;
        }

        if(!empty($dock_id))
        {
            // ambil data ship class
            $data=$this->schedule->select_data("app.t_mtr_ship_class","where id=".$row->ship_class_id." ")->row();
            $data->id=$this->enc->encode($data->id);
            echo json_encode($data);
            exit;
        }
        else
        {
            $data=array('id'=>"",'name'=>"Pilih");
            echo json_encode($data);
            exit;
        }

        
    }

    function createCode_21032023($port)
    {
        $front_code="J".$port."".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_schedule where left(schedule_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (schedule_code) as max_code from app.t_mtr_schedule where left(schedule_code,".$total_length.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

    function createCode($port)
    {
        $front_code="J".$port."".date('ymd');
        $date = date("Y-m-d");
        // $dateNow = $date." 00:00"; 
        $dateToNew = date('Y-m-d', strtotime($date . ' +1 day'));

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_schedule where created_on >='{$date}' and created_on<'{$dateToNew}' and port_id='{$port}' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (schedule_code) as max_code from app.t_mtr_schedule where created_on >='{$date}' and created_on<'{$dateToNew}' and port_id='{$port}' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }    

    public function checkDuplicateTime($data,$dock, $time, $typeTime)
    {

        /*
        $value['A'] = Nama pelabuhan
        $value['B'] = Nama dermaga
        $value['C'] = Tanggal dan jam sandar
        $value['D'] = Tanggal dan jam buka layanan
        $value['E'] = Tanggal dan jam tutup layanan
        $value['F'] = Tanggal dan jam tutup rampdoor
        $value['G'] = Tanggal dan jam Keberangkatan
        $value['H'] = Nama Kapal
        $value['I'] = Tanggal Jadwal
        $value['J'] = Trip

        */

        switch ($typeTime) {
            case 'sail':
                $getValue= 'G' ;

                break;
            case 'openBoarding':
                $getValue= 'D' ;
                    break;      
            case 'closeBoarding':
                $getValue= 'E' ;
                break; 
            case 'closeRampdoor':
                $getValue= 'F' ;
                break;                                                                       
            default:        // docking
                $getValue= 'C' ;
                break;
        }

        $count =0;
        $index=1;        

        // print_r($data); exit;

        $checkDuplication=array();
        foreach ($data as $key => $value) {
            
            if($index>7)
            {
                if((trim($time)==trim($value[$getValue])) and (strtoupper(trim($value['B']))==strtoupper(trim($dock))))
                {
                    $count ++;
                    $checkDuplication[]=$index;  
                }                
            }

            $index++;

        }

        // print_r($checkDuplication); exit;

        if($count>1)
        {
            return array ("code"=>"1",
                          "data"=>$checkDuplication
                        );
        }
        else
        {
            return array ("code"=>"0",
              "data"=>$checkDuplication
            );
        }
    }

    // function createCode($port)
    // {
    //     $front_code="J".$port."".date('ymd');

    //     $chekCode=$this->db->query("select * from app.t_mtr_schedule where left(schedule_code,8)='".$front_code."' ")->num_rows();

    //     if($chekCode<1)
    //     {
    //         $shelterCode=$front_code."0001";
    //         return $shelterCode;
    //     }
    //     else
    //     {
    //         $max=$this->db->query("select max (schedule_code) as max_code from app.t_mtr_schedule where left(schedule_code,8)='".$front_code."' ")->row();
    //         $kode=$max->max_code;
    //         $noUrut = (int) substr($kode, 8, 4);
    //         $noUrut++;
    //         $char = $front_code;
    //         $kode = $char . sprintf("%04s", $noUrut);
    //         return $kode;
    //     }
    // }

}
