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

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Jadwal',
            'content'  => 'schedule/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'btn_excel'=> generate_button_new($this->_module, 'import_excel',  site_url($this->_module.'/import_excel')),
            'port' =>$this->schedule->select_data("app.t_mtr_port","where status='1' order by name asc")->result(),
            'ship' =>$this->schedule->select_data("app.t_mtr_ship","where status='1' order by name asc")->result(),
            'ship_class' =>$this->schedule->select_data("app.t_mtr_ship_class","where status='1' order by name asc")->result(),
            'dock' =>$this->schedule->select_data("app.t_mtr_dock","where status='1' order by name asc")->result(),
            'import'=>checkBtnAccess($this->_module,'import_excel'),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Jadwal';
        $data['port']=$this->schedule->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['ship']=$this->schedule->select_data("app.t_mtr_ship","where status='1' order by name asc")->result();
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
        // $this->form_validation->set_rules('ship', 'Kapal', 'required');
        $this->form_validation->set_rules('schedule', 'jadwal tanggal', 'required');
        $this->form_validation->set_rules('docking_on', 'waktu sandar', 'required');
        // $this->form_validation->set_rules('open_boarding', 'waktu boarding', 'required');
        // $this->form_validation->set_rules('close_boarding', 'tutup boarding', 'required');
        // $this->form_validation->set_rules('sail_time', 'waktu berlayar', 'required');
        // $this->form_validation->set_rules('close_ramdoor', 'waktu tutup ramdoor', 'required');
        $this->form_validation->set_rules('trip', 'waktu tutup ramdoor', 'required');
        $this->form_validation->set_rules('class', 'ship_class', 'required');

        // $min_docking_on=date( "Y-m-d H:i", strtotime( $schedule." ".$docking_on." 0 minutes" ) );
        // $min_open_boarding=date( "Y-m-d H:i", strtotime( $schedule." ".$open_boarding." 0 minutes" ) );
        // $min_close_boarding=date( "Y-m-d H:i", strtotime( $schedule." ".$close_boarding." 0 minutes" ) );
        // $min_close_ramdoor=date( "Y-m-d H:i", strtotime( $schedule." ".$close_ramdoor." 0 minutes" ) );
        // $min_sail_time=date( "Y-m-d H:i", strtotime( $schedule." ".$sail_time." 0 minutes" ) );

        $max=$this->db->query('select max("order") as max_order from app.t_mtr_schedule where schedule_date='."'".$schedule."'".' and port_id='.$port_id.' and dock_id='.$dock_id.' and status=1')->row();


        // check data agar tidak bentrok waktunya
        // $check_overlaps=$this->schedule->select_data("app.t_mtr_schedule","where (to_char(docking_on,'yyyy-mm-dd HH:MM')>'".$docking_on."' and to_char(docking_on,'yyyy-mm-dd HH:MM')<'".$sail_time."') and port_id=$port_id  and dock_id=$dock_id");

        empty($ship_id)?$shipId="NULL":$shipId=$ship_id;


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

        // echo print_r($data_trx);
        // exit;

        // pengecekan apakah waktunya open boarding dan close boarding
        // $check_data=$this->db->query("
        //                             select d.name as ship_name, c.name as dock_name, b.name as port_name, a.* from app.t_mtr_schedule a
        //                             left join app.t_mtr_port b on a.port_id=b.id
        //                             left join app.t_mtr_dock c on a.dock_id=c.id
        //                             left join app.t_mtr_ship d on a.ship_id=d.id
        //                             where (a.open_boarding_on between '".$open_boarding."' and '".$close_boarding."') and (a.close_boarding_on between '".$open_boarding."' and '".$close_boarding."')
        //                             ");

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
        // else if($check_overlaps->num_rows()>0)
        // {
        //     echo $res=json_api(0,"Waktu Tidak boleh bentrok dengan Kapal");
        // }
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

          $empty_data=array();
          $data=array();

            $invalid_ship=array();
            $invalid_port=array();
            $invalid_dock=array();
            $invalid_ship_class=array();

            $err_ship=array();
            $err_port=array();
            $err_dock=array();
            $err_ship_class=array();

            foreach ($sheets as $sheet) {

            // karena data yang di excel di mulai dari baris ke 2
            // maka jika $i lebih dari 1 data akan di masukan ke database
            $order_data=1;
            if ($i > 7) {

                $check_ship=$this->schedule->select_data("app.t_mtr_ship","where status=1 and upper(name)='".strtoupper(trim($sheet['H']))."'");
                $check_port=$this->schedule->select_data("app.t_mtr_port","where status=1 and  upper(name)='".strtoupper($sheet['A'])."'");
                $check_ship_class=$this->schedule->select_data("app.t_mtr_ship_class","where status=1 and  upper(name)='".strtoupper($sheet['K'])."'");

                $check_dock=$this->schedule->val_dock("where a.status=1 and upper(b.name)='".strtoupper(trim($sheet['A']))."' and upper(a.name)='".strtoupper(trim($sheet['B']))."'");

                $port_id=$check_port->row();
                $ship_id=$check_ship->row();
                $dock_id=$check_dock->row();
                $ship_class_id=$check_ship_class->row();


                $data[]=array(
                    'port_id'=>empty($port_id->id)?"":$port_id->id,
                    'dock_id'=>empty($dock_id->id)?"":$dock_id->id,
                    'docking_on'=>$sheet['C'],
                    'ship_class'=>empty($ship_class_id->id)?"":$ship_class_id->id,
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

                //check apakah sheet data yang kosong 
                if(empty($sheet['A']) || empty($sheet['B']) || empty($sheet['C']) || empty($sheet['D']) || empty($sheet['E']) || empty($sheet['F']) || empty($sheet['G'])  || empty($sheet['I']) || empty($sheet['J']) || empty($sheet['K']))
                {
                    $empty_data[]=1;   
                }
                // pengecekan jika nama ship
                // else if($check_ship->num_rows()<1)
                // {
                //     $invalid_ship[]=$sheet['H'];
                //     $err_ship[]=1;
                // }
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
                
                // jika nama kelas tidak terdaftar harcord
                if(strtoupper(trim($sheet['K'])) =="REGULER" or strtoupper(trim($sheet['K'])) =="EKSEKUTIF")
                {
                    $invalid_ship_class[]="";

                    $err_ship_class[]=0;
                }
                else
                {
                    $invalid_ship_class[]=$sheet['K'];
                    $err_ship_class[]=1;   
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
        // else if(array_sum($err_ship)>0)
        // {
        //     echo $res=json_api(0, 'Nama kapal '.implode(", ",array_unique($invalid_ship)).' tidak ada');
        // }
        else if(array_sum($err_port)>0)
        {
            echo $res=json_api(0, 'Nama pelabuhan '.implode(", ",array_unique($invalid_port)).' tidak ada');
        }
        else if(array_sum($err_dock)>0)
        {
            echo $res=json_api(0, 'Nama dermaga '.implode(", ",array_unique($invalid_dock)).' tidak ada');
        }
        else if (array_sum($err_ship_class) >0)
        {
            echo $res=json_api(0, 'Tipe Kapal '.implode(", ",array_unique($invalid_ship_class)).' tidak ada');   
        }
        else
        {
            $this->db->trans_begin();


             foreach ($data as $key => $value) {
                 
                $schedule=$value['schedule_date'];

                $max=$this->db->query("select max(".'"order"'.") as max_order from app.t_mtr_schedule where schedule_date='".$schedule."' and port_id=".$value['port_id']." and dock_id=".$value['dock_id']." and status=1")->row();

                $schedule_code=$this->createCode($value['port_id']);

                empty($value['ship_id'])?$shipId=NULL:$shipId=$value['ship_id'];

                $data_row=array(
                'port_id'=>$value['port_id'],
                'dock_id'=>$value['dock_id'],
                'docking_on'=>$value['docking_on'],
                'ship_class'=>$value['ship_class'],
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
        $row->docking_on=date("Y-m-d H:i", strtotime($row->docking_on));
        $row->open_boarding_on=date("Y-m-d H:i", strtotime($row->open_boarding_on));
        $row->close_boarding_on=date("Y-m-d H:i", strtotime($row->close_boarding_on));
        $row->close_rampdoor_on=date("Y-m-d H:i", strtotime($row->close_rampdoor_on));
        $row->sail_time=date("Y-m-d H:i", strtotime($row->sail_time));
        $detail=$row;

        $data['title'] = 'Edit Team';
        $data['port']=$this->schedule->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['ship']=$this->schedule->select_data("app.t_mtr_ship","where status='1' order by name asc")->result();
        $data['dock']=$this->schedule->select_data("app.t_mtr_dock","where status='1' order by name asc")->result();
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
        $this->form_validation->set_rules('ship', 'Kapal', 'required');
        $this->form_validation->set_rules('schedule', 'jadwal tanggal', 'required');
        $this->form_validation->set_rules('docking_on', 'waktu sandar', 'required');
        $this->form_validation->set_rules('open_boarding', 'waktu boarding', 'required');
        $this->form_validation->set_rules('close_boarding', 'tutup boarding', 'required');
        $this->form_validation->set_rules('sail_time', 'waktu berlayar', 'required');
        $this->form_validation->set_rules('close_ramdoor', 'waktu tutup ramdoor', 'required');
        $this->form_validation->set_rules('trip', 'waktu tutup ramdoor', 'required');
        $this->form_validation->set_rules('class', 'ship_class', 'required');
                
        $data=array(
                    'port_id'=>$port_id,
                    'dock_id'=>$dock_id,
                    'docking_on'=>$docking_on,
                    'ship_class'=>$ship_class,
                    'open_boarding_on'=>$open_boarding,
                    'close_boarding_on'=>$close_boarding,
                    'close_rampdoor_on'=>$close_ramdoor,
                    'sail_time'=>$sail_time,
                    'ship_id'=>$ship_id,
                    'schedule_date'=>$schedule,
                    'trip'=>$trip,
                    'updated_on'=>date("Y-m-d H:i:s"),
                    'updated_by'=>$this->session->userdata('username'),
                    );

        // check schedule 
        $trx_schedule=$this->schedule->select_data("app.t_trx_schedule"," where schedule_code='".$schedule_id."'")->row();
        
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if($docking_on > $open_boarding)
        {
            echo $res=json_api(0, 'Waktu buka layanan tidak boleh diatas jam sandar');
        }
        else if($open_boarding > $close_boarding)
        {
            echo $res=json_api(0, 'Waktu tutup layanan tidak boleh diatas jam buka boarding');
        }
        else if($close_boarding > $close_ramdoor)
        {
            echo $res=json_api(0, 'Waktu tutup rampdoor tidak boleh diatas jam tutup boarding');
        }
        else if($close_ramdoor > $sail_time)
        {
            echo $res=json_api(0, 'Waktu keberangkatan tidak boleh diatas jam Waktu tutup rampdoor');
        }


        else if(!empty($trx_schedule->ploting_date) || !empty($trx_schedule->docking_date) || !empty($trx_schedule->open_boarding_date) || !empty($trx_schedule->close_boarding_date) || !empty($trx_schedule->close_rampdoor_date) ||!empty($trx_schedule->sail_date) )
        {
            echo $res=json_api(0, 'Gagal edit, jadwal sudah melakukan transaksi');   
        }
        else
        {
            $this->db->trans_begin();
            $this->schedule->update_data($this->_table,$data,"schedule_code='".$schedule_id."'");

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

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/action_edit';
        $logMethod   = 'EDIT';
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

        $row=$this->schedule->select_data("app.t_mtr_dock","where status=1 and port_id=".$port_id." order by name asc")->result();

        $data=array();
        foreach ($row as $key => $value) {
            $value->id=$this->enc->encode($value->id);
            $value->name=strtoupper($value->name);

            $data[]=$value;
        }

        echo json_encode($data);
    }

    function createCode($port)
    {
        $front_code="J".$port."".date('ymd');

        $chekCode=$this->db->query("select * from app.t_mtr_schedule where left(schedule_code,8)='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (schedule_code) as max_code from app.t_mtr_schedule where left(schedule_code,8)='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, 8, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

}
