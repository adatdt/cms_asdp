<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Ticket_sobek extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_ticket_sobek','ticket');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('PHPExcel');

        $this->_table    = 'app.t_trx_ticket_sobek';
        $this->_table2    = 'app.t_trx_ticket_sobek_vehicle';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/ticket_sobek';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->ticket->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->ticket->get_identity_app();

        if($get_identity==0)
        {
            // filter jika menggunkan port yang ada di user
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->ticket->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')." ")->result();
                $row_port=1;
            }
            else
            {
                $port=$this->ticket->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result();
                $row_port=0;
            }
        }
        else
        {
                $port=$this->ticket->select_data("app.t_mtr_port","where id=".$get_identity." ")->result();
                $row_port=1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Tiket Manual Gate In',
            'content'  => 'ticket_sobek/index',
            'port'  => $port,
            'row_port'  => $row_port,
            'destination'=>$this->ticket->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result(),
            'shift'=>$this->ticket->select_data("app.t_mtr_shift","where status not in (-5) order by shift_name asc")->result(),
            'ship_class'=>$this->ticket->select_data("app.t_mtr_ship_class","where status not in (-5) order by name asc")->result(),
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),

        );

		$this->load->view('default', $data);
	}

    public function get_vehicle(){   
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request())
        {
            $rows = $this->ticket->get_data_vehicle();
            echo json_encode($rows);
            exit;
        }
    }        

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $get_identity=$this->ticket->get_identity_app();

        if($get_identity==0)
        {
            // mengambil filter port berdasarkan port user
            if(!empty($this->session->userdata("port_id")))
            {
                $data['port'] = $this->ticket->select_data("app.t_mtr_port","where id=".$this->session->userdata("port_id")." ")->result();

                $data['route'] = $this->ticket->get_route(" where origin={$this->session->userdata("port_id")} order by concat(b.name,'-',c.name) asc ")->result();
            }
            else
            {
                $data['port'] = $this->ticket->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
                $data['route'] = $this->ticket->get_route(" where a.status=1 order by concat(b.name,'-',c.name) asc ")->result();                    
            }
        }
        else
        {
            $data['port'] = $this->ticket->select_data("app.t_mtr_port","where id=".$get_identity." ")->result();
            $data['route'] = $this->ticket->get_route(" where origin={$get_identity} order by concat(b.name,'-',c.name) asc ")->result();
        }

        $getMinInputDateParam=$this->ticket->select_data("app.t_mtr_custom_param", " where status=1 and  upper(param_name)=upper('min_days_ticket_manual_gatein') ")->row();

        $dateParameter=date('d-m-Y',strtotime("-{$getMinInputDateParam->param_value} {$getMinInputDateParam->value_type} "));


        $data['title'] = 'Tambah Tiket Manual';
        $data['service'] = $this->ticket->select_data("app.t_mtr_service"," where status=1 order by name asc")->result();        
        $data['passanger_type'] = $this->ticket->select_data("app.t_mtr_passanger_type"," where status=1 order by name asc")->result();
        $data['ship_class'] = $this->ticket->select_data("app.t_mtr_ship_class"," where status=1 order by name asc")->result();
        $data['vehicle_class'] = $this->ticket->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc")->result(); 
        $data['dateParameter'] = $dateParameter;       

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $service_id=$this->enc->decode($this->input->post("service"));



        if(empty($service_id))
        {
            echo $res=json_api(0, 'Data masih ada yang kosong'); 
            exit;  
        }

        // hardcord service penumpang adalah 1
        if($service_id==1)
        {
            $this->action_add_passanger();
        }
        else
        {
            $this->action_add_vehicle();   
        }

       
    }

    public function action_add_passanger()
    {

        $port_id=$this->enc->decode($this->input->post("port"));
        $trx_date2=$this->input->post("trx_date");
        $shift_id=$this->enc->decode($this->input->post("shift"));
        $ob_code=$this->input->post("ob_code");
        $service_id=$this->enc->decode($this->input->post("service"));
        $route_id=$this->enc->decode($this->input->post("route"));


        $trx_date=date("Y-m-d", strtotime($trx_date2));

        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('trx_date', 'Tanggal Transaksi', 'required');
        $this->form_validation->set_rules('shift', 'Shift', 'required');
        $this->form_validation->set_rules('ob_code', 'Nama Penjual', 'required');
        $this->form_validation->set_rules('service', 'Jenis Pengguna Jasa', 'required');
        $this->form_validation->set_rules('route', 'Rute', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');        

        $empty_err = array();
        $empty_name_err = array();

        $ship_class_err = array();
        $ship_class_err_name = array();

        $pass_type_err = array();
        $pass_type_name_err = array();

        $gender_err = array();
        $gender_err_name = array();

        $ticket_mn_err = array();
        $ticket_mn_err_name = array();        

        $ticket_not_found_err[]=0 ;
        $ticket_not_found_err_name = array();        

        $data_excel=array();

        if(!empty($_FILES['excel']['name']))
        {
            $file = $_FILES['excel']['tmp_name'];
            $load = PHPExcel_IOFactory::load($file);
            $sheets = $load->getActiveSheet()->toArray(null,true,true,true);

            $errDuplicateBeforeInput[]=0;
            $errDuplicateBeforeInputMessage=array();


            if(!empty($sheets))
            {   
                $row_cell=1;
                $index=0;            
                foreach ($sheets as $value) {
                    // dimulai dari baris 8
                    if($row_cell>7)
                    {

                        // validasi jika cell kosong;
                        if(empty($value['A']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Nomer Tiket Manual Baris ".$row_cell." kolom A ";
                        }  

                        if(empty($value['B']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Nama Penumpang Baris ".$row_cell." kolom B ";
                        }  

                        if(empty($value['C']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Jenis Kelamin  Baris ".$row_cell." kolom C ";
                        }  

                        if(empty($value['D']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Tipe Penumpang Baris ".$row_cell." kolom D ";
                        }                                                              

                        if(empty($value['E']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Tipe Kapal Baris ".$row_cell." kolom E ";
                        }

                        if(empty($value['F']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Alamat Baris ".$row_cell." kolom F ";
                        }                    

                    

                        // check validasi... apakah ship classnya ada
                        $check_ship_class=$this->ticket->select_data(" app.t_mtr_ship_class"," where upper(name)=upper('".trim($value['E'])."')  and status=1 ");

                        if($check_ship_class->num_rows()<1)
                        {
                            $ship_class_err[]=1;
                            $ship_class_err_name[]=" Tipe Kapal {$value['E']} Baris ".$row_cell." kolom E";
                            $get_ship_class_id="";
                        }
                        else
                        {

                            $get_ship_class_id=$check_ship_class->row()->id;                        
                        }

                        // check apakah Jenis kelaminya di tulis selain P (perempuan) atau L (Laki - laki)
                        if(strtoupper(trim($value['C'])) !='L' and strtoupper(trim($value['C'])) !='P' )
                        {
                            $gender_err[]=1;
                            $c_upper=strtoupper($value['C']);
                            $gender_err_name[]=" Jenis Kelamin {$c_upper} Baris ".$row_cell." kolom C ";
                        }

                        // ccheck ticket manualnya apakah tiketnya dsudah di input di trxnya
                        $check_ticket_manual=$this->ticket->select_data("app.t_trx_ticket_manual"," where upper(ticket_number_manual)='".strtoupper($value['A'])."' " );

                        if($check_ticket_manual->num_rows()>0)
                        {
                            $ticket_mn_err[]=1;
                            $ticket_mn_err_name[]=" {$value['A']} Baris ".$row_cell." kolom A ";
                        }


                        $check_pass_type=$this->ticket->select_data(" app.t_mtr_passanger_type"," where upper(name)=upper('".trim($value['D'])."')  and status=1 ");

                        if($check_pass_type->num_rows()<1)
                        {
                            $pass_type_err[]=1;
                            $pass_type_err_name[]=" Tipe Penumpang {$value['D']} Baris ".$row_cell." kolom D";
                            $get_pass_type_id="";

                        }
                        else
                        {

                            $get_pass_type_id=$check_pass_type->row()->id;                        
                        }  



                        // check apakah tiket manual tersebut tersedia
                        if($check_pass_type->num_rows()>0 and !empty($value['A']) and !empty($port_id) and !empty($get_ship_class_id) )
                        {
                            $passangerTypeId=$check_pass_type->row()->id;

                            $checkMaterTicket=$this->ticket->select_data("app.t_mtr_ticket_manual_passanger"," where passanger_type_id={$passangerTypeId}  and upper(ticket_number)=upper('".$value['A']."') and ship_class='".$get_ship_class_id."' and status=1 and  port_id=".$port_id )->row();

                            if(count((array)$checkMaterTicket)<1) // jika tidak ada di master
                            {
                                $ticket_not_found_err[]=1 ;
                                $ticket_not_found_err_name[] = "No.Tiket {$value['A']} Layanan {$value['E']} Golongan {$value['D']} Baris ".$row_cell." kolom A ";       
                            }

                            // check duplicat dalam inputan dalam satu ticket
                            $gol=$value['D'];
                            $tikMan=$value['A'];
                            $layanan=$value['E']; // ship class
                            
                            // check duplicat dalam inputan dalam satu ticket
                            $checkDuplicate=$this->checkDuplicateTicket($sheets,$gol,$tikMan,$layanan,'D');

                            // $checkDuplicate=$this->checkDuplicateTicket($sheets,$value['D'],$value['A'],'D');

                            if($checkDuplicate['code']==1)
                            {
                                $errDuplicateBeforeInput[]=1;
                                // $errDuplicateBeforeInputMessage[]="No.Tiket {$value['A']} golongan {$value['D']} Baris ".$row_cell." kolom A ";
                                $errDuplicateBeforeInputMessage[]="No.Tiket {$value['A']} golongan {$value['D']} Baris ".implode(" dan ", $checkDuplicate['data']);
                            }


                        }                  


                        //re temp data
                        $data_excel[$index]=array("ticket_number_manual"=>strtoupper($value['A']),
                                            "passanger_name"=>$value['B'],
                                            "gender"=>strtoupper($value['C']),
                                            "passanger_type"=>$get_pass_type_id,
                                            "ship_class"=>$get_ship_class_id,
                                            "address"=>$value['F']
                                        );

                        $index++;

                    }

                    $row_cell++;
                }
            }

        }            

        // print_r(array_unique($errDuplicateBeforeInputMessage)); exit;

        $data=array();

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if(array_sum($empty_err)>0)            
        {
            echo $res=json_api(0, implode(", ",$empty_name_err)." Harus di isi" );
        }
        else if(empty($_FILES['excel']['name']))            
        {
            echo $res=json_api(0," File xlsx Harus di isi" );
        }  
        else if(array_sum($ship_class_err)>0)            
        {
            echo $res=json_api(0, " Kelas Kapal <br>".implode(",<br> ",$ship_class_err_name)." <br> Tidak ada" );
        }
        else if(array_sum($pass_type_err)>0)            
        {
            echo $res=json_api(0,"Tipe Penumpang <br>".implode(",<br> ",$pass_type_err_name)." <br> Tidak ada" );
        }        
        else if(array_sum($gender_err)>0)            
        {
            echo $res=json_api(0, "Jenis Kelamin <br>".implode(",<br> ",$gender_err_name)." <br> Tidak ada" );
        }   
        // else if(array_sum($ticket_mn_err)>0)  // di takeout karen validasi berdasarkan master yang aktif          
        // {
        //     echo $res=json_api(0, "Tiket Manual <br>".implode(",<br> ",$ticket_mn_err_name)." <br> Sudah ada" );
        // }  
        else if(array_sum($ticket_not_found_err)>0)
        {
            echo $res=json_api(0, "Tiket Manual <br>".implode(",<br> ",$ticket_not_found_err_name)." <br> Tidak Terdaftar" );
        }
        else if(array_sum($errDuplicateBeforeInput)>0)
        {
            echo $res=json_api(0, "Duplikasi  Input <br>".implode(",<br> ",array_unique($errDuplicateBeforeInputMessage))." " );   
        }                               
        else
        {

            $this->db->trans_begin();

            $getAmount[]=0;
            $totalCash[]=0;
            foreach ($data_excel as $key=>$my_data_excel) {

                $get_route=$this->ticket->select_data("app.t_mtr_rute", " where id={$route_id} and status=1 ")->row();

                $getBranchCode = $this->ticket->select_data("app.t_mtr_branch"," where port_id='{$port_id}' and ship_class='{$get_ship_class_id}' and status=1")->row();

                //trx_booking_passanger
                $service_id== 2?$i=2:$i=1;

                $identity=strlen($port_id)>1?'S'.$port_id:'S0'.$port_id;

                $id_origin=strlen($get_route->origin)>1?$get_route->origin:'0'.$get_route->origin;
                $id_destination=strlen($get_route->destination)>1?$get_route->destination:'0'.$get_route->destination;

                $trans_number=$this->ticket->get_trans_number($identity);
                $booking_code=$this->generateBookingCode();
                $new_ticket='MN'.$id_origin.$id_destination.$booking_code . sprintf('%02d', $i); // jika loping jadiin i++

                $get_branch=$this->ticket->select_data("app.t_mtr_branch", " where port_id={$port_id} and ship_class={$my_data_excel['ship_class']} and status=1 "  )->row();
                $get_fare=$this->ticket->select_data("app.t_mtr_fare_passanger", " where rute_id={$route_id} and ship_class={$my_data_excel['ship_class']} and passanger_type={$my_data_excel['passanger_type']} and status=1 " )->row();

                $getAdmFee=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('adm_fee') and status=1  " )->row();

                $get_fare->adm_fee=$getAdmFee->param_value;

                if($my_data_excel['ship_class']==1)
                {
                    $checkin_param='checkin_expired_goshow';
                    $gatein_param='gatein_expired_goshow';

                }
                else
                {
                    $checkin_param='checkin_expired_goshow_eksekutif';
                    $gatein_param='gatein_expired_goshow_eksekutif';
                }

                $boarding_param='boarding_expired';

                $get_expire_checkin=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('$checkin_param') and status=1 " )->row();

                $get_expire_gatein=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$gatein_param}')  and status=1 " )->row();
                $get_expire_boarding=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$boarding_param}') and status=1 " )->row();

                // mengambil data expire yang diambil trxdate

                $dateNow=date("Y-m-d H:i:s");
                $checkin_expired=date("Y-m-d H:i:s",strtotime($get_expire_checkin->param_value." ".$get_expire_checkin->value_type,strtotime(date("Y-m-d H:i:s"))));

                $gatein_expired=date("Y-m-d H:i:s",strtotime($get_expire_gatein->param_value." ".$get_expire_gatein->value_type,strtotime(date("Y-m-d H:i:s"))));

                $boarding_expired=date("Y-m-d H:i:s",strtotime($get_expire_boarding->param_value." ".$get_expire_boarding->value_type,strtotime(date("Y-m-d H:i:s"))));

                $get_extra_fee=$this->ticket->select_data("app.t_mtr_payment_type"," where upper(payment_type)=upper('cash') ")->row();

                if($get_fare->fare<1)
                {
                    $total_amount=$get_fare->fare;
                }
                else
                {
                    $total_amount=$get_fare->fare+$get_extra_fee->extra_fee;
                }

                $getTerminalCode=$this->ticket->getTerminalCode($port_id);

                $data_ticket_manual=array(
                    'trans_number'=>strtoupper(trim($trans_number->trans_number)),
                    'ticket_number_manual'=>$my_data_excel['ticket_number_manual'],
                    'ticket_number'=>$new_ticket,
                    'name'=>$my_data_excel['passanger_name'],
                    'gender'=>$my_data_excel['gender'],
                    'address'=>$my_data_excel['address'],
                    'passanger_type'=>$my_data_excel['passanger_type'],
                    'status'=>1,
                    'trx_date'=>$trx_date,
                    'shift_id'=>$shift_id,
                    'port_id'=>$port_id,
                    'ob_code'=>$ob_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username"),
                    'ship_class'=>$my_data_excel['ship_class']
                );

                //trx_invoice
                $data_invoice=array(
                  'amount'=>$get_fare->fare,
                  'terminal_code'=>$getTerminalCode,
                  'trans_number'=>$trans_number->trans_number,
                  'extra_fee'=>$get_extra_fee->extra_fee,
                  'total_amount'=>$total_amount,
                  'customer_name'=>$my_data_excel['passanger_name'],
                  'phone_number'=>"0",
                  'email'=>"",
                  'booking_channel'=>'web_admin',
                  'channel'=>'web_admin',
                  'service_id'=>$service_id,
                  'ticket_type'=>3,         // tipe tiket 1 normal, 2 sab, 3 tiket manual
                  'status'=>2,
                  'created_on'=>date("Y-m-d H:i:s"),
                  'port_id'=>$port_id,
                  'due_date'=>$trx_date,
                  'invoice_date'=>$trx_date,
                  'payment_type'=>'cash',
                  'branch_code'=>$get_branch->branch_code,
                  'created_by'=>$this->session->userdata('username'),
                  );

                //trx_payment
                $data_payment=array(
                  'payment_type'=>"cash",
                  'amount'=>$total_amount,
                  'booking_code'=>$booking_code,
                  'payment_date'=>$trx_date,
                  'trans_number'=>$trans_number->trans_number,
                  'channel'=>"web_admin",
                  'status'=>1,
                  'created_on'=>date("Y-m-d H:i:s"),
                  'created_by'=>$this->session->userdata('username'),
                  );


                //trx_booking
                $data_booking=array(
                    'service_id'=>$service_id,
                    'booking_code'=>$booking_code,
                    'trans_number'=> $trans_number->trans_number,
                    'total_passanger'=>1,
                    'origin'=>$get_route->origin,
                    'destination'=>$get_route->destination,
                    'ship_class'=>$my_data_excel['ship_class'],
                    'amount'=>$get_fare->fare,
                    'depart_date'=>$trx_date,
                    'depart_time_start'=>"00:00",
                    'depart_time_end'=>"00:00",
                    'channel'=>'web_admin',
                    'checkin_expired'=>$checkin_expired,
                    'gatein_expired'=>$gatein_expired,
                    'ticket_type'=>3,
                    'status'=>2,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    'track_code'=>$get_route->track_code,
                    'branch_code'=>$getBranchCode->branch_code,
                  );


                // trx booking passanger
                $data_booking_passanger = array(
                    'id_number'=>0,
                    'name'=>$my_data_excel['passanger_name'],
                    'age'=>0,
                    'gender'=>$my_data_excel['gender'],
                    'city'=>$my_data_excel['address'],
                    // 'id_type'=>$manifest['id_type'],
                    'passanger_type_id'=>$my_data_excel['passanger_type'],
                    'fare'=>$get_fare->fare,
                    'entry_fee'=>$get_fare->entry_fee,
                    'dock_fee'=>$get_fare->dock_fee,
                    'trip_fee'=>$get_fare->trip_fee,
                    'adm_fee'=>$get_fare->adm_fee, // misss di UAT
                    'responsibility_fee'=>$get_fare->responsibility_fee,
                    'insurance_fee'=>$get_fare->insurance_fee,
                    'ifpro_fee'=>$get_fare->ifpro_fee,
                    'booking_code'=>$booking_code,
                    'ticket_number'=>$new_ticket,
                    'origin'=>$get_route->origin,
                    'destination'=>$get_route->destination,
                    'ship_class'=>$my_data_excel['ship_class'],
                    'depart_date'=>$trx_date,
                    'depart_time_start'=>"00:00",
                    'depart_time_end'=>"00:00",
                    'service_id'=>$service_id,
                    'checkin_expired'=>$checkin_expired,
                    'gatein_expired'=>$gatein_expired,
                    'boarding_expired'=>$boarding_expired,
                    'channel'=>'web_admin',
                    'ticket_type'=>3,
                    'status'=>4,
                    'track_code'=>$get_route->track_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                );  

                // app.t_trx_sell
                $data_sell=array(
                    'trans_number'=>$trans_number->trans_number,
                    'terminal_code'=>$getTerminalCode,
                    'amount'=>$total_amount,
                    'status'=>1,
                    'booking_channel'=>'web_admin',
                    'created_by'=>$this->session->userdata("username"),
                    'created_on'=>date("Y-m-d H:i:s"),
                    'payment_type'=>'cash',
                    'ob_code'=>$ob_code,
                    'payment_date'=>$trx_date,
                );       

                // trx_checkin
                $data_checkin=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$new_ticket,
                    'booking_code'=>$booking_code,
                    'checkin_web'=>1,
                    'checkin_pos'=>0,
                    'reprint'=>0,
                    'channel'=>"web_admin",
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$ob_code,

                );      

                // data gatein
                $data_gatein=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$new_ticket,
                    'booking_code'=>$booking_code,
                    'boarding_expired'=>$boarding_expired,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                );      

                $updateMaster=array(
                    "status"=>"0", // update status master tiket manual jadi 0
                    "updated_on"=>date("Y-m-d H:i:s"),
                    "used_on"=>date("Y-m-d H:i:s"),
                    "updated_by"=>$this->session->userdata('username'),
                );
             

                $data[]=array($data_ticket_manual,
                    $data_invoice,
                    $data_payment,
                    $data_booking,
                    $data_booking_passanger,
                    $data_sell,
                    $data_checkin,
                    $data_gatein
                );           

                $this->ticket->insert_data("app.t_trx_ticket_manual",$data_ticket_manual);
                $this->ticket->insert_data("app.t_trx_invoice",$data_invoice);
                $this->ticket->insert_data("app.t_trx_payment",$data_payment);
                $this->ticket->insert_data("app.t_trx_booking",$data_booking);
                $this->ticket->insert_data("app.t_trx_booking_passanger",$data_booking_passanger);
                $this->ticket->insert_data("app.t_trx_check_in",$data_checkin);
                $this->ticket->insert_data("app.t_trx_gate_in",$data_gatein);
                $this->ticket->insert_data("app.t_trx_sell",$data_sell);
                $this->ticket->edit_total_cash($total_amount,$ob_code);


                $this->ticket->update_data("app.t_mtr_ticket_manual_passanger",$updateMaster," status=1 and upper(ticket_number)=upper('".$my_data_excel['ticket_number_manual']."')
                    and ship_class=".$my_data_excel['ship_class']."
                    and passanger_type_id=".$my_data_excel['passanger_type']."
                    and port_id=".$port_id."
                 ");

                // mendapatkan total amount
                $getAmount[]=$total_amount;

                // total cash penjualan 
                $totalCash[]=1;                 

            }

            // jika penjual sudah melakukan opening balance 
            $checkOpening=$this->ticket->select_data("app.t_trx_opening_balance"," where ob_code='{$ob_code}' and status=2 ");

            if($checkOpening->num_rows()>0)
            {

                $thisTable='app.t_trx_closing_balance_pos';

                $this->ticket->query_update_data($thisTable, array_sum($getAmount) , $ob_code, array_sum($totalCash));
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
        $logUrl      = site_url().'transaction/ticket_sobek/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }

    public function action_add_passanger_24062021()
    {

        $port_id=$this->enc->decode($this->input->post("port"));
        $trx_date=$this->input->post("trx_date");
        $shift_id=$this->enc->decode($this->input->post("shift"));
        $ob_code=$this->input->post("ob_code");
        $service_id=$this->enc->decode($this->input->post("service"));
        $route_id=$this->enc->decode($this->input->post("route"));



        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('trx_date', 'Tanggal Transaksi', 'required');
        $this->form_validation->set_rules('shift', 'Shift', 'required');
        $this->form_validation->set_rules('ob_code', 'Nama Penjual', 'required');
        $this->form_validation->set_rules('service', 'Servis', 'required');
        $this->form_validation->set_rules('route', 'Rute', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');        

        $empty_err = array();
        $empty_name_err = array();

        $ship_class_err = array();
        $ship_class_err_name = array();

        $pass_type_err = array();
        $pass_type_name_err = array();

        $gender_err = array();
        $gender_err_name = array();

        $ticket_mn_err = array();
        $ticket_mn_err_name = array();        

        $data_excel=array();

        if(!empty($_FILES['excel']['name']))
        {
            $file = $_FILES['excel']['tmp_name'];
            $load = PHPExcel_IOFactory::load($file);
            $sheets = $load->getActiveSheet()->toArray(null,true,true,true);

            if(!empty($sheets))
            {   
                $row_cell=1;
                $index=0;            
                foreach ($sheets as $value) {
                    // dimulai dari baris 8
                    if($row_cell>7)
                    {

                        // validasi jika cell kosong;
                        if(empty($value['A']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Nomer Tiket Manual Baris ".$row_cell." kolom A ";
                        }  

                        if(empty($value['B']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Nama Penumpang Baris ".$row_cell." kolom B ";
                        }  

                        if(empty($value['C']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Jenis Kelamin  Baris ".$row_cell." kolom C ";
                        }  

                        if(empty($value['D']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Tipe Penumpang Baris ".$row_cell." kolom D ";
                        }                                                              

                        if(empty($value['E']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Tipe Kapal Baris ".$row_cell." kolom E ";
                        }

                        if(empty($value['F']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Alamat Baris ".$row_cell." kolom F ";
                        }                    

                        // check validasi... apakah ship classnya ada
                        $check_ship_class=$this->ticket->select_data(" app.t_mtr_ship_class"," where upper(name)=upper('".trim($value['E'])."')  and status=1 ");

                        if($check_ship_class->num_rows()<1)
                        {
                            $ship_class_err[]=1;
                            $ship_class_err_name[]=" Tipe Kapal {$value['E']} Baris ".$row_cell." kolom E";
                            $get_ship_class_id="";
                        }
                        else
                        {

                            $get_ship_class_id=$check_ship_class->row()->id;                        
                        }

                        // check apakah Jenis kelaminya di tulis selain P (perempuan) atau L (Laki - laki)
                        if(strtoupper(trim($value['C'])) !='L' and strtoupper(trim($value['C'])) !='P' )
                        {
                            $gender_err[]=1;
                            $c_upper=strtoupper($value['C']);
                            $gender_err_name[]=" Jenis Kelamin {$c_upper} Baris ".$row_cell." kolom C ";
                        }

                        // ccheck ticket manualnya apakah tiketnya dsudah di input
                        $check_ticket_manual=$this->ticket->select_data("app.t_trx_ticket_manual"," where upper(ticket_number_manual)='".strtoupper($value['A'])."' " );

                        if($check_ticket_manual->num_rows()>0)
                        {
                            $ticket_mn_err[]=1;
                            $ticket_mn_err_name[]=" {$value['A']} Baris ".$row_cell." kolom A ";
                        }


                        $check_pass_type=$this->ticket->select_data(" app.t_mtr_passanger_type"," where upper(name)=upper('".trim($value['D'])."')  and status=1 ");

                        if($check_pass_type->num_rows()<1)
                        {
                            $pass_type_err[]=1;
                            $pass_type_err_name[]=" Tipe Penumpang {$value['D']} Baris ".$row_cell." kolom D";
                            $get_pass_type_id="";
                        }
                        else
                        {

                            $get_pass_type_id=$check_pass_type->row()->id;                        
                        }                    


                        //re temp data
                        $data_excel[$index]=array("ticket_number_manual"=>$value['A'],
                                            "passanger_name"=>$value['B'],
                                            "gender"=>strtoupper($value['C']),
                                            "passanger_type"=>$get_pass_type_id,
                                            "ship_class"=>$get_ship_class_id,
                                            "address"=>$value['F']
                                        );

                        $index++;

                    }

                    $row_cell++;
                }
            }

        }            

        // print_r($data_excel); exit;

        $data=array();

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if(array_sum($empty_err)>0)            
        {
            echo $res=json_api(0, implode(", ",$empty_name_err)." Harus di isi" );
        }
        else if(empty($_FILES['excel']['name']))            
        {
            echo $res=json_api(0," File xlsx Harus di isi" );
        }  
        else if(array_sum($ship_class_err)>0)            
        {
            echo $res=json_api(0, " Kelas Kapal ".implode(", ",$ship_class_err_name)." Tidak ada" );
        }
        else if(array_sum($pass_type_err)>0)            
        {
            echo $res=json_api(0,"Tipe Penumpang ".implode(", ",$pass_type_err_name)." Tidak ada" );
        }        
        else if(array_sum($gender_err)>0)            
        {
            echo $res=json_api(0, "Jenis Kelamin ".implode(", ",$gender_err_name)." Tidak ada" );
        }   
        else if(array_sum($ticket_mn_err)>0)            
        {
            echo $res=json_api(0, "Tiket manual ".implode(", ",$ticket_mn_err_name)." Sudah ada" );
        }                         
        else
        {

            $this->db->trans_begin();
            $getAmount[]=0;
            $totalCash[]=0;
            foreach ($data_excel as $key=>$my_data_excel) {

                $get_route=$this->ticket->select_data("app.t_mtr_rute", " where id={$route_id} and status=1 ")->row();

                $getBranchCode = $this->ticket->select_data("app.t_mtr_branch"," where port_id='{$port_id}' and ship_class='{$get_ship_class_id}' and status=1")->row();

                //trx_booking_passanger
                $service_id== 2?$i=2:$i=1;

                $identity=strlen($port_id)>1?'S'.$port_id:'S0'.$port_id;

                $id_origin=strlen($get_route->origin)>1?$get_route->origin:'0'.$get_route->origin;
                $id_destination=strlen($get_route->destination)>1?$get_route->destination:'0'.$get_route->destination;

                $trans_number=$this->ticket->get_trans_number($identity);
                $booking_code=$this->generateBookingCode();
                $new_ticket='MN'.$id_origin.$id_destination.$booking_code . sprintf('%02d', $i); // jika loping jadiin i++

                $get_branch=$this->ticket->select_data("app.t_mtr_branch", " where port_id={$port_id} and ship_class={$my_data_excel['ship_class']}" )->row();
                $get_fare=$this->ticket->select_data("app.t_mtr_fare_passanger", " where rute_id={$route_id} and ship_class={$my_data_excel['ship_class']} and passanger_type={$my_data_excel['passanger_type']}" )->row();

                if($my_data_excel['ship_class']==1)
                {
                    $checkin_param='checkin_expired_goshow';
                    $gatein_param='gatein_expired_goshow';

                }
                else
                {
                    $checkin_param='checkin_expired_goshow_eksekutif';
                    $gatein_param='gatein_expired_goshow_eksekutif';
                }

                $boarding_param='boarding_expired';

                $get_expire_checkin=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('$checkin_param') and status=1 " )->row();

                $get_expire_gatein=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$gatein_param}') and status=1 " )->row();
                $get_expire_boarding=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$boarding_param}') and status=1 " )->row();

                // mengambil data expire yang diambil trxdate

                $dateNow=date("Y-m-d H:i:s");
                $checkin_expired=date("Y-m-d H:i:s",strtotime($get_expire_checkin->param_value." ".$get_expire_checkin->value_type,strtotime(date("Y-m-d H:i:s"))));

                $gatein_expired=date("Y-m-d H:i:s",strtotime($get_expire_gatein->param_value." ".$get_expire_gatein->value_type,strtotime(date("Y-m-d H:i:s"))));

                $boarding_expired=date("Y-m-d H:i:s",strtotime($get_expire_boarding->param_value." ".$get_expire_boarding->value_type,strtotime(date("Y-m-d H:i:s"))));

                $get_extra_fee=$this->ticket->select_data("app.t_mtr_payment_type"," where upper(payment_type)=upper('cash') ")->row();

                if($get_fare->fare<1)
                {
                    $total_amount=$get_fare->fare;
                }
                else
                {
                    $total_amount=$get_fare->fare+$get_extra_fee->extra_fee;
                }

                $getTerminalCode=$this->ticket->getTerminalCode($port_id);

                $data_ticket_manual=array(
                    'trans_number'=>$trans_number->trans_number,
                    'ticket_number_manual'=>$my_data_excel['ticket_number_manual'],
                    'ticket_number'=>$new_ticket,
                    'name'=>$my_data_excel['passanger_name'],
                    'gender'=>$my_data_excel['gender'],
                    'address'=>$my_data_excel['address'],
                    'passanger_type'=>$my_data_excel['passanger_type'],
                    'status'=>1,
                    'trx_date'=>$trx_date,
                    'shift_id'=>$shift_id,
                    'port_id'=>$port_id,
                    'ob_code'=>$ob_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username"),
                    'ship_class'=>$my_data_excel['ship_class']
                );

                //trx_invoice
                $data_invoice=array(
                  'amount'=>$get_fare->fare,
                  'terminal_code'=>$getTerminalCode,
                  'trans_number'=>$trans_number->trans_number,
                  'extra_fee'=>$get_extra_fee->extra_fee,
                  'total_amount'=>$total_amount,
                  'customer_name'=>$my_data_excel['passanger_name'],
                  'phone_number'=>"0",
                  'email'=>"",
                  'booking_channel'=>'web_admin',
                  'channel'=>'web_admin',
                  'service_id'=>$service_id,
                  'ticket_type'=>3,         // tipe tiket 1 normal, 2 sab, 3 tiket manual
                  'status'=>2,
                  'created_on'=>date("Y-m-d H:i:s"),
                  'port_id'=>$port_id,
                  'due_date'=>$trx_date,
                  'invoice_date'=>$trx_date,
                  'payment_type'=>'cash',
                  'branch_code'=>$get_branch->branch_code,
                  'created_by'=>$this->session->userdata('username'),
                  );

                //trx_payment
                $data_payment=array(
                  'payment_type'=>"cash",
                  'amount'=>$total_amount,
                  'booking_code'=>$booking_code,
                  'payment_date'=>$trx_date,
                  'trans_number'=>$trans_number->trans_number,
                  'channel'=>"web_admin",
                  'status'=>1,
                  'created_on'=>date("Y-m-d H:i:s"),
                  'created_by'=>$this->session->userdata('username'),
                  );


                //trx_booking
                $data_booking=array(
                    'service_id'=>$service_id,
                    'booking_code'=>$booking_code,
                    'trans_number'=> $trans_number->trans_number,
                    'total_passanger'=>1,
                    'origin'=>$get_route->origin,
                    'destination'=>$get_route->destination,
                    'ship_class'=>$my_data_excel['ship_class'],
                    'amount'=>$get_fare->fare,
                    'depart_date'=>$trx_date,
                    'depart_time_start'=>"00:00",
                    'depart_time_end'=>"00:00",
                    'channel'=>'web_admin',
                    'checkin_expired'=>$checkin_expired,
                    'gatein_expired'=>$gatein_expired,
                    'ticket_type'=>3,
                    'status'=>2,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    'track_code'=>$get_route->track_code,
                    'branch_code'=>$getBranchCode->branch_code,
                  );


                // trx booking passanger
                $data_booking_passanger = array(
                    'id_number'=>0,
                    'name'=>$my_data_excel['passanger_name'],
                    'age'=>0,
                    'gender'=>$my_data_excel['gender'],
                    'city'=>$my_data_excel['address'],
                    // 'id_type'=>$manifest['id_type'],
                    'passanger_type_id'=>$my_data_excel['passanger_type'],
                    'fare'=>$get_fare->fare,
                    'entry_fee'=>$get_fare->entry_fee,
                    'dock_fee'=>$get_fare->dock_fee,
                    'trip_fee'=>$get_fare->trip_fee,
                    'adm_fee'=>$get_fare->adm_fee, // misss di UAT
                    'responsibility_fee'=>$get_fare->responsibility_fee,
                    'insurance_fee'=>$get_fare->insurance_fee,
                    'ifpro_fee'=>$get_fare->ifpro_fee,
                    'booking_code'=>$booking_code,
                    'ticket_number'=>$new_ticket,
                    'origin'=>$get_route->origin,
                    'destination'=>$get_route->destination,
                    'ship_class'=>$my_data_excel['ship_class'],
                    'depart_date'=>$trx_date,
                    'depart_time_start'=>"00:00",
                    'depart_time_end'=>"00:00",
                    'service_id'=>$service_id,
                    'checkin_expired'=>$checkin_expired,
                    'gatein_expired'=>$gatein_expired,
                    'boarding_expired'=>$boarding_expired,
                    'channel'=>'web_admin',
                    'ticket_type'=>3,
                    'status'=>4,
                    'track_code'=>$get_route->track_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                );  

                // app.t_trx_sell
                $data_sell=array(
                    'trans_number'=>$trans_number->trans_number,
                    'terminal_code'=>$getTerminalCode,
                    'amount'=>$total_amount,
                    'booking_channel'=>'web_admin',
                    'created_by'=>$this->session->userdata("username"),
                    'created_on'=>date("Y-m-d H:i:s"),
                    'payment_type'=>'cash',
                    'ob_code'=>$ob_code,
                    'payment_date'=>$trx_date,
                );       

                // trx_checkin
                $data_checkin=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$new_ticket,
                    'booking_code'=>$booking_code,
                    'checkin_web'=>1,
                    'checkin_pos'=>0,
                    'reprint'=>0,
                    'channel'=>"web_admin",
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$ob_code,

                );      

                // data gatein
                $data_gatein=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$new_ticket,
                    'booking_code'=>$booking_code,
                    'boarding_expired'=>$boarding_expired,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                );      
             

                $data[]=array($data_ticket_manual,
                    $data_invoice,
                    $data_payment,
                    $data_booking,
                    $data_booking_passanger,
                    $data_sell,
                    $data_checkin,
                    $data_gatein
                );           

                $this->ticket->insert_data("app.t_trx_ticket_manual",$data_ticket_manual);
                $this->ticket->insert_data("app.t_trx_invoice",$data_invoice);
                $this->ticket->insert_data("app.t_trx_payment",$data_payment);
                $this->ticket->insert_data("app.t_trx_booking",$data_booking);
                $this->ticket->insert_data("app.t_trx_booking_passanger",$data_booking_passanger);
                $this->ticket->insert_data("app.t_trx_check_in",$data_checkin);
                $this->ticket->insert_data("app.t_trx_gate_in",$data_gatein);
                $this->ticket->insert_data("app.t_trx_sell",$data_sell);
                $this->ticket->edit_total_cash($total_amount,$ob_code);
                
            
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
        $logUrl      = site_url().'transaction/ticket_sobek/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }    

    public function action_add_vehicle()
    {
        $port_id=$this->enc->decode($this->input->post("port"));
        $trx_date2=$this->input->post("trx_date");
        $shift_id=$this->enc->decode($this->input->post("shift"));
        $ob_code=$this->input->post("ob_code");
        $service_id=$this->enc->decode($this->input->post("service"));
        $route_id=$this->enc->decode($this->input->post("route"));

        $trx_date=date("Y-m-d", strtotime($trx_date2));

        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('trx_date', 'Tanggal Transaksi', 'required');
        $this->form_validation->set_rules('shift', 'Shift', 'required');
        $this->form_validation->set_rules('ob_code', 'Nama Penjual', 'required');
        $this->form_validation->set_rules('service', 'Servis', 'required');
        $this->form_validation->set_rules('route', 'Rute', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');


        $empty_err = array();
        $empty_name_err = array();

        $ship_class_err = array();
        $ship_class_err_name = array();

        $vehicle_class_err = array();
        $vehicle_class_name_err = array();

        $gender_err = array();
        $gender_err_name = array();

        $ticket_mn_err = array();
        $ticket_mn_err_name = array(); 

        $ticket_not_found_err[]=0 ;
        $ticket_not_found_err_name=array();      

        $data_excel=array();

        if(!empty($_FILES['excel']['name']))
        {            
            $file = $_FILES['excel']['tmp_name'];
            $load = PHPExcel_IOFactory::load($file);
            $sheets = $load->getActiveSheet()->toArray(null,true,true,true);


            $errDuplicateBeforeInput[]=0;
            $errDuplicateBeforeInputMessage=array();


            if(!empty($sheets))
            {   
                $row_cell=1;
                $index=0;            
                foreach ($sheets as $value) {
                    // dimulai dari baris 8
                    if($row_cell>7)
                    {

                        // validasi jika cell kosong;
                        if(empty($value['A']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Nomer Tiket Manual  Baris ".$row_cell." kolom A ";
                        }  

                        if(empty($value['B']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Nama Penumpang Baris ".$row_cell." kolom B ";
                        }  

                        if(empty($value['C']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Jenis Kelamin Baris ".$row_cell." kolom C ";
                        }  

                        if(empty($value['D']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Plat Nomer Baris ".$row_cell." kolom D ";
                        }                                                              

                        if(empty($value['E']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Tipe Kapal Baris ".$row_cell." kolom E ";
                        }

                        if(empty($value['F']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Golongan Kendaraan Baris ".$row_cell." kolom F ";
                        }


                        if(empty($value['G']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Total Penumpang Baris ".$row_cell." kolom G ";
                        }

                        if(empty($value['H']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Alamat Baris ".$row_cell." kolom H ";
                        }                                                                                                        

                        // check validasi... apakah ship classnya ada
                        $check_ship_class=$this->ticket->select_data(" app.t_mtr_ship_class"," where upper(name)=upper('".trim($value['E'])."')  and status=1 ");

                        if($check_ship_class->num_rows()<1)
                        {
                            $ship_class_err[]=1;
                            $ship_class_err_name[]="{$value['E']} Baris ".$row_cell." kolom E ";
                            $get_ship_class_id="";
                        }
                        else
                        {

                            $get_ship_class_id=$check_ship_class->row()->id;                        
                        }

                        // check validasi... vehicle class
                        $check_vehicle_class=$this->ticket->select_data(" app.t_mtr_vehicle_class"," where upper(name)=upper('".trim($value['F'])."')  and status=1 ");

                        if($check_vehicle_class->num_rows()<1)
                        {
                            $vehicle_class_err[]=1;
                            $vehicle_class_name_err[]=" {$value['F']} Baris ".$row_cell." kolom F";
                            $get_vehicle_class_id="";
                        }
                        else
                        {

                            $get_vehicle_class_id=$check_vehicle_class->row()->id;                        
                        }


                        // check apakah Jenis kelaminya di tulis selain P (perempuan) atau L (Laki - laki)
                        if(strtoupper(trim($value['C'])) !='L' and strtoupper(trim($value['C'])) !='P' )
                        {
                            $gender_err[]=1;
                            $c_upper=strtoupper($value['C']);
                            $gender_err_name[]="{$c_upper} Baris ".$row_cell." kolom C ";
                        }

                        // ccheck ticket manualnya apakah tiketnya dsudah di input
                        $check_ticket_manual=$this->ticket->select_data("app.t_trx_ticket_vehicle_manual"," where upper(ticket_number_manual)='".strtoupper($value['A'])."' and status=1 " );

                        if($check_ticket_manual->num_rows()>0)
                        {
                            $ticket_mn_err[]=1;
                            $ticket_mn_err_name[]=" {$value['A']} Baris ".$row_cell." kolom A ";
                        }

                        // check apakah tiket manual tersebut tersedia
                        if($check_vehicle_class->num_rows()>0 and !empty($value['A']) and  !empty($port_id) and !empty($get_ship_class_id))
                        {
                            $vehicleClassId=$check_vehicle_class->row()->id;

                            $checkMaterTicket=$this->ticket->select_data("app.t_mtr_ticket_manual_vehicle","
                                                                         where vehicle_class_id={$vehicleClassId}  
                                                                         and status=1
                                                                         and ship_class='{$get_ship_class_id}'
                                                                         and upper(ticket_number)=upper('".$value['A']."') 
                                                                         and vehicle_class_id='".$check_vehicle_class->row()->id."'  and  port_id=".$port_id )->row();

                            if(count((array)$checkMaterTicket)<1) // jika tidak ada di master
                            {
                                $ticket_not_found_err[]=1 ;
                                $ticket_not_found_err_name[] = " {$value['A']} Baris ".$row_cell." kolom A ";       
                            }

                            // check duplicat dalam inputan dalam satu ticket

                            $gol=$value['F'];
                            $tikMan=$value['A'];
                            $layanan=$value['E']; // ship class

                            $checkDuplicate=$this->checkDuplicateTicket($sheets,$gol,$tikMan,$layanan,'F');



                            if($checkDuplicate['code']==1)
                            {
                                $errDuplicateBeforeInput[]=1;
                                // $errDuplicateBeforeInputMessage[]="No.Tiket {$value['A']} golongan {$value['F']} Baris ".$row_cell." kolom A ";

                                $errDuplicateBeforeInputMessage[]="No.Tiket {$value['A']} golongan {$value['F']} Baris ".implode(" dan ", $checkDuplicate['data']);
                            }                            


                        }                  


                        //re temp data
                        $data_excel[$index]=array("ticket_number_manual"=>strtoupper($value['A']),
                                            "passanger_name"=>$value['B'],
                                            "gender"=>strtoupper($value['C']),
                                            "plat_no"=>strtoupper($value['D']),
                                            "ship_class"=>$get_ship_class_id,
                                            "vehicle_class"=>$get_vehicle_class_id,
                                            "total_passanger"=>$value['G'],
                                            "address"=>$value['H'],
                                        );

                        $index++;

                    }

                    $row_cell++;
                }
            }

        }

        // echo array_sum($errDuplicateBeforeInput); exit;
        // print_r($data_excel); exit;

        $data=array();
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        } 
        else if(array_sum($empty_err)>0)            
        {
            echo $res=json_api(0, implode(", ",$empty_name_err)." Harus di isi" );
        }
        else if(empty($_FILES['excel']['name']))            
        {
            echo $res=json_api(0," File xlsx Harus di isi" );
        }   
        else if(array_sum($ship_class_err)>0)            
        {
            echo $res=json_api(0, "Tipe Kapal  <br>".implode("<br> ",$ship_class_err_name)." Tidak ada" );
        }
        else if(array_sum($vehicle_class_err)>0)            
        {
            echo $res=json_api(0, "Golongan  <br>".implode("<br> ",$vehicle_class_name_err)." Tidak ada" );
        }        
        else if(array_sum($gender_err)>0)            
        {
            echo $res=json_api(0, " Jenis Kelamin <br>".implode("<br> ",$gender_err_name)." Tidak ada" );
        }   
        // else if(array_sum($ticket_mn_err)>0)            
        // {
        //     echo $res=json_api(0, "Tiket Manual  <br>".implode("<br> ",$ticket_mn_err_name)." Sudah ada" );
        // }
        else if(array_sum($ticket_not_found_err)>0)
        {
            echo $res=json_api(0, "Tiket Manual  <br>".implode("<br> ",$ticket_not_found_err_name)." Tidak Terdaftar" );   
        }
        else if(array_sum($errDuplicateBeforeInput)>0)
        {
            echo $res=json_api(0, "Duplikasi  Input <br>".implode(",<br> ",array_unique($errDuplicateBeforeInputMessage))." " );   
        }           
        else
        {

            // print_r($data); exit;

            // print_r($data_excel); exit;
            $this->db->trans_begin();

              // mendapatkan total amount
            $getAmount[]=0;

            // total cash penjualan 
            $totalCash[]=0;     

            foreach ($data_excel as $key => $my_data_excel) {

                $get_route=$this->ticket->select_data("app.t_mtr_rute", " where id={$route_id} ")->row();

                $getBranchCode = $this->ticket->select_data("app.t_mtr_branch"," where port_id='{$port_id}' and ship_class='{$get_ship_class_id}' and status=1")->row();

                $identity=strlen($port_id)>1?'S'.$port_id:'S0'.$port_id;

                $id_origin=strlen($get_route->origin)>1?$get_route->origin:'0'.$get_route->origin;
                $id_destination=strlen($get_route->destination)>1?$get_route->destination:'0'.$get_route->destination;


                $trans_number=$this->ticket->get_trans_number($identity);
                $booking_code=$this->generateBookingCode();
                $new_ticket='MN'.$id_origin.$id_destination.$booking_code; // jika loping jadiin i++
                $ticket_vehicle=$new_ticket."01";
                // $ticket_passanger=$new_ticket."02";

                $get_branch=$this->ticket->select_data("app.t_mtr_branch", " where port_id={$port_id} and ship_class={$my_data_excel['ship_class']} and status=1 " )->row();
                $get_fare=$this->ticket->select_data("app.t_mtr_fare_vehicle", " where rute_id={$route_id} and ship_class={$my_data_excel['ship_class']} and vehicle_class_id={$my_data_excel['vehicle_class']}and status=1 " )->row();

                $getAdmFee=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('adm_fee') and status=1 " )->row();

                $get_fare->adm_fee=$getAdmFee->param_value;                

                if($my_data_excel['ship_class']==1)
                {
                    $checkin_param='checkin_expired_goshow';
                    $gatein_param='gatein_expired_goshow';

                }
                else
                {
                    $checkin_param='checkin_expired_goshow_eksekutif';
                    $gatein_param='gatein_expired_goshow_eksekutif';
                }

                $boarding_param='boarding_expired';

                $get_expire_checkin=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('$checkin_param') and status=1 " )->row();
                $get_expire_gatein=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$gatein_param}')and status=1  " )->row();
                $get_expire_boarding=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$boarding_param}') and status=1 " )->row();

                // mengambil data expire yang diambil trxdate
                $checkin_expired=date("Y-m-d H:i:s",strtotime($get_expire_checkin->param_value." ".$get_expire_checkin->value_type,strtotime(date("Y-m-d H:i:s"))));
                $gatein_expired=date("Y-m-d H:i:s",strtotime($get_expire_gatein->param_value." ".$get_expire_gatein->value_type,strtotime(date("Y-m-d H:i:s"))));
                $boarding_expired=date("Y-m-d H:i:s",strtotime($get_expire_boarding->param_value." ".$get_expire_boarding->value_type,strtotime(date("Y-m-d H:i:s"))));

                $get_extra_fee=$this->ticket->select_data("app.t_mtr_payment_type"," where upper(payment_type)=upper('cash') ")->row();

                if($get_fare->fare<1)
                {
                    $total_amount=$get_fare->fare;
                }
                else
                {
                    $total_amount=$get_fare->fare+$get_extra_fee->extra_fee;
                }

                $getTerminalCode=$this->ticket->getTerminalCode($port_id);

                $data_ticket_manual=array(
                    'trans_number'=>$trans_number->trans_number,
                    'ticket_number_manual'=>strtoupper(trim($my_data_excel['ticket_number_manual'])),
                    'ticket_number'=>$ticket_vehicle,
                    'name'=>$my_data_excel['passanger_name'],
                    'id_number'=>$my_data_excel['plat_no'],
                    'total_passanger'=>$my_data_excel['total_passanger'],
                    'status'=>1,
                    'vehicle_class'=>$my_data_excel['vehicle_class'],
                    'trx_date'=>$trx_date,
                    'shift_id'=>$shift_id,
                    'port_id'=>$port_id,
                    'ob_code'=>$ob_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username"),
                    'ship_class'=>$my_data_excel['ship_class']
                );

                //trx_invoice
                $data_invoice=array(
                  'terminal_code'=>$getTerminalCode,
                  'amount'=>$get_fare->fare,
                  'trans_number'=>$trans_number->trans_number,
                  'extra_fee'=>$get_extra_fee->extra_fee,
                  'total_amount'=>$total_amount,
                  'customer_name'=>$my_data_excel['passanger_name'],
                  'phone_number'=>"0",
                  'email'=>"",
                  'booking_channel'=>'web_admin',
                  'channel'=>'web_admin',
                  'service_id'=>$service_id,
                  'ticket_type'=>3,         // tipe tiket 1 normal, 2 sab, 3 tiket manual
                  'status'=>2,
                  'created_on'=>date("Y-m-d H:i:s"),
                  'port_id'=>$port_id,
                  'due_date'=>$trx_date,
                  'invoice_date'=>$trx_date,
                  'payment_type'=>'cash',
                  'branch_code'=>$get_branch->branch_code,
                  'created_by'=>$this->session->userdata('username'),
                  );



                //trx_payment
                $data_payment=array(
                  'payment_type'=>"cash",
                  'amount'=>$total_amount,
                  'booking_code'=>$booking_code,
                  'payment_date'=>$trx_date,
                  'trans_number'=>$trans_number->trans_number,
                  'channel'=>"web_admin",
                  'status'=>1,
                  'created_on'=>date("Y-m-d H:i:s"),
                  'created_by'=>$this->session->userdata('username'),
                  );


                //trx_booking
                $data_booking=array(
                    'service_id'=>$service_id,
                    'booking_code'=>$booking_code,
                    'trans_number'=> $trans_number->trans_number,
                    'total_passanger'=>$my_data_excel['total_passanger'],
                    'origin'=>$get_route->origin,
                    'destination'=>$get_route->destination,
                    'ship_class'=>$my_data_excel['ship_class'],
                    'amount'=>$get_fare->fare,
                    'depart_date'=>$trx_date,
                    'depart_time_start'=>"00:00",
                    'depart_time_end'=>"00:00",
                    'channel'=>'web_admin',
                    'checkin_expired'=>$checkin_expired,
                    'gatein_expired'=>$gatein_expired,
                    'ticket_type'=>3,
                    'status'=>2,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    'branch_code'=>$getBranchCode->branch_code,
                    'track_code'=>$get_route->track_code
                  );


                $data_booking_passanger_res=array();

                $prefixTicket=2;
                
                for($i=0; $i<$my_data_excel['total_passanger']; $i++)
                {   
                    $ticket_passanger=strlen($prefixTicket)>1?$new_ticket.$prefixTicket:$new_ticket."0".$prefixTicket;

                    // trx booking passanger                    
                    $data_booking_passanger = array(
                        'id_number'=>0,
                        'name'=>$my_data_excel['passanger_name'],
                        'age'=>0,
                        'gender'=>$my_data_excel['gender'],
                        'passanger_type_id'=>1, // hardcord 1 dewasa
                        'city'=>$my_data_excel['address'],
                        'fare'=>0,
                        'entry_fee'=>0,
                        'dock_fee'=>0,
                        'trip_fee'=>0,
                        'adm_fee'=>0,
                        'responsibility_fee'=>0,
                        'insurance_fee'=>0,
                        'ifpro_fee'=>0,
                        'booking_code'=>$booking_code,
                        'ticket_number'=>$ticket_passanger,
                        'origin'=>$get_route->origin,
                        'destination'=>$get_route->destination,
                        'ship_class'=>$my_data_excel['ship_class'],
                        'depart_date'=>$trx_date,
                        'depart_time_start'=>"00:00",
                        'depart_time_end'=>"00:00",
                        'service_id'=>$service_id,
                        'checkin_expired'=>$checkin_expired,
                        'gatein_expired'=>$gatein_expired,
                        'boarding_expired'=>$boarding_expired,
                        'channel'=>'web_admin',
                        'ticket_type'=>3,
                        'status'=>4,
                        'track_code'=>$get_route->track_code,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata('username'),
                    ); 

                    $data_booking_passanger_res[]=$data_booking_passanger;

                    // insert booking passanger
                    $this->ticket->insert_data("app.t_trx_booking_passanger",$data_booking_passanger);

                    $prefixTicket++;

                }

                $data_booking_vehicle=array
                (
                    "id_number"=>$my_data_excel['plat_no'],
                    "status"=>4,
                    "created_by"=>$this->session->userdata("username"),
                    "created_on"=>date("Y-m-d H:i:s"),
                    "vehicle_class_id"=>$my_data_excel['vehicle_class'],
                    "ticket_number"=>$ticket_vehicle,
                    'fare'=>$get_fare->fare,
                    'trip_fee'=>$get_fare->trip_fee,
                    'adm_fee'=>$get_fare->adm_fee,

                    'entry_fee'=>$get_fare->entry_fee,
                    'dock_fee'=>$get_fare->dock_fee,
                    'responsibility_fee'=>$get_fare->responsibility_fee,
                    'insurance_fee'=>$get_fare->insurance_fee,
                    'ifpro_fee'=>$get_fare->ifpro_fee,

                    'booking_code'=>$booking_code,                    
                    'length'=>0,
                    'height'=>0,
                    'weight'=>0,
                    'origin'=>$get_route->origin,
                    'destination'=>$get_route->destination,
                    'depart_date'=>$trx_date,
                    'ship_class'=>$my_data_excel['ship_class'],
                    'depart_time_start'=>"00:00",
                    'depart_time_end'=>"00:00",
                    'service_id'=>$service_id,
                    'checkin_expired'=>$checkin_expired,
                    'gatein_expired'=>$gatein_expired,
                    'boarding_expired'=>$boarding_expired,
                    'channel'=>'web_admin',
                    'ticket_type'=>3,
                    'track_code'=>$get_route->track_code,
                ); 

                // app.t_trx_sell
                $data_sell=array(
                    'terminal_code'=>$getTerminalCode,
                    'trans_number'=>$trans_number->trans_number,
                    'amount'=>$total_amount,
                    'booking_channel'=>'web_admin',
                    'created_by'=>$this->session->userdata("username"),
                    'created_on'=>date("Y-m-d H:i:s"),
                    'payment_type'=>'cash',
                    'ob_code'=>$ob_code,
                    'payment_date'=>$trx_date,
                    'status'=>1
                );       

                // trx_checkin
                $data_checkin=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_passanger,
                    'booking_code'=>$booking_code,
                    'checkin_web'=>1,
                    'checkin_pos'=>0,
                    'reprint'=>0,
                    'channel'=>"web_admin",
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$ob_code,

                );

                $data_checkin_vehicle=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_vehicle,
                    'booking_code'=>$booking_code,
                    'checkin_web'=>1,
                    'checkin_pos'=>0,
                    'reprint'=>0,
                    'channel'=>"web_admin",
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$ob_code,

                );                    

                // data gatein
                $data_gatein=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_passanger,
                    'booking_code'=>$booking_code,
                    'boarding_expired'=>$boarding_expired,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                );      

                // data gatein
                $data_gatein_vehicle=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_vehicle,
                    'booking_code'=>$booking_code,
                    'boarding_expired'=>$boarding_expired,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                );

                $updateMaster=array(
                    "status"=>"0", // update status master tiket manual jadi 0
                    "updated_on"=>date("Y-m-d H:i:s"),
                    "used_on"=>date("Y-m-d H:i:s"),
                    "updated_by"=>$this->session->userdata('username'),
                );                      
                
                $data[]=array($data_ticket_manual,
                    $data_invoice,
                    $data_payment,
                    $data_booking,
                    $data_booking_passanger_res,
                    $data_booking_vehicle,
                    $data_sell,
                    $data_checkin,
                    $data_checkin_vehicle,
                    $data_gatein,
                    $data_gatein_vehicle,
                );

                $this->ticket->insert_data("app.t_trx_ticket_vehicle_manual",$data_ticket_manual);
                $this->ticket->insert_data("app.t_trx_invoice",$data_invoice);
                $this->ticket->insert_data("app.t_trx_payment",$data_payment);
                $this->ticket->insert_data("app.t_trx_booking",$data_booking);
                // $this->ticket->insert_data("app.t_trx_booking_passanger",$data_booking_passanger);
                $this->ticket->insert_data("app.t_trx_booking_vehicle",$data_booking_vehicle);
                $this->ticket->insert_data("app.t_trx_check_in",$data_checkin);
                $this->ticket->insert_data("app.t_trx_check_in_vehicle",$data_checkin_vehicle);
                $this->ticket->insert_data("app.t_trx_gate_in",$data_gatein);
                $this->ticket->insert_data("app.t_trx_gate_in_vehicle",$data_gatein_vehicle);
                $this->ticket->insert_data("app.t_trx_sell",$data_sell);

                $this->ticket->edit_total_cash($total_amount,$ob_code);            

                $this->ticket->update_data("app.t_mtr_ticket_manual_vehicle", $updateMaster," status=1 and ticket_number='".$my_data_excel['ticket_number_manual']."' 
                    and vehicle_class_id='".$my_data_excel['vehicle_class']."'
                    and ship_class='".$my_data_excel['ship_class']."'
                    and port_id='".$port_id."'
                    ");

        
                // mendapatkan total amount
                $getAmount[]=$total_amount;

                // total cash penjualan 
                $totalCash[]=1;                 

            }

            // print_r($getAmount); exit;

            // jika penjual sudah melakukan opening balance 
            $checkOpening=$this->ticket->select_data("app.t_trx_opening_balance"," where ob_code='{$ob_code}' and status=2 ");

            if($checkOpening->num_rows()>0)
            {

                $thisTable='app.t_trx_closing_balance_pos';

                $this->ticket->query_update_data($thisTable, array_sum($getAmount) , $ob_code, array_sum($totalCash));
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

        // print_r($data); exit;

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/ticket_sobek/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }    

    public function action_add_vehicle_24062021()
    {
        $port_id=$this->enc->decode($this->input->post("port"));
        $trx_date=$this->input->post("trx_date");
        $shift_id=$this->enc->decode($this->input->post("shift"));
        $ob_code=$this->input->post("ob_code");
        $service_id=$this->enc->decode($this->input->post("service"));
        $route_id=$this->enc->decode($this->input->post("route"));

        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('trx_date', 'Tanggal Transaksi', 'required');
        $this->form_validation->set_rules('shift', 'Shift', 'required');
        $this->form_validation->set_rules('ob_code', 'Nama Penjual', 'required');
        $this->form_validation->set_rules('service', 'Servis', 'required');
        $this->form_validation->set_rules('route', 'Rute', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');


        $empty_err = array();
        $empty_name_err = array();

        $ship_class_err = array();
        $ship_class_err_name = array();

        $vehicle_class_err = array();
        $vehicle_class_name_err = array();

        $gender_err = array();
        $gender_err_name = array();

        $ticket_mn_err = array();
        $ticket_mn_err_name = array(); 
        if(!empty($_FILES['excel']['name']))
        {            
            $file = $_FILES['excel']['tmp_name'];
            $load = PHPExcel_IOFactory::load($file);
            $sheets = $load->getActiveSheet()->toArray(null,true,true,true);

            if(!empty($sheets))
            {   
                $row_cell=1;
                $index=0;            
                foreach ($sheets as $value) {
                    // dimulai dari baris 8
                    if($row_cell>7)
                    {

                        // validasi jika cell kosong;
                        if(empty($value['A']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Nomer Tiket Manual  Baris ".$row_cell." kolom A ";
                        }  

                        if(empty($value['B']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Nama Penumpang Baris ".$row_cell." kolom B ";
                        }  

                        if(empty($value['C']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Jenis Kelamin Baris ".$row_cell." kolom C ";
                        }  

                        if(empty($value['D']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Plat Nomer Baris ".$row_cell." kolom D ";
                        }                                                              

                        if(empty($value['E']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Tipe Kapal Baris ".$row_cell." kolom E ";
                        }

                        if(empty($value['F']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Golongan Kendaraan Baris ".$row_cell." kolom F ";
                        }


                        if(empty($value['G']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Total Penumpang Baris ".$row_cell." kolom G ";
                        }                                        

                        // check validasi... apakah ship classnya ada
                        $check_ship_class=$this->ticket->select_data(" app.t_mtr_ship_class"," where upper(name)=upper('".trim($value['E'])."')  and status=1 ");

                        if($check_ship_class->num_rows()<1)
                        {
                            $ship_class_err[]=1;
                            $ship_class_err_name[]="{$value['E']} Baris ".$row_cell." kolom E ";
                            $get_ship_class_id="";
                        }
                        else
                        {

                            $get_ship_class_id=$check_ship_class->row()->id;                        
                        }

                        // check validasi... vehicle class
                        $check_vehicle_class=$this->ticket->select_data(" app.t_mtr_vehicle_class"," where upper(name)=upper('".trim($value['F'])."')  and status=1 ");

                        if($check_vehicle_class->num_rows()<1)
                        {
                            $vehicle_class_err[]=1;
                            $vehicle_class_name_err[]=" {$value['F']} Baris ".$row_cell." kolom F";
                            $get_vehicle_class_id="";
                        }
                        else
                        {

                            $get_vehicle_class_id=$check_vehicle_class->row()->id;                        
                        }


                        // check apakah Jenis kelaminya di tulis selain P (perempuan) atau L (Laki - laki)
                        if(strtoupper(trim($value['C'])) !='L' and strtoupper(trim($value['C'])) !='P' )
                        {
                            $gender_err[]=1;
                            $c_upper=strtoupper($value['C']);
                            $gender_err_name[]="{$c_upper} Baris ".$row_cell." kolom C ";
                        }

                        // ccheck ticket manualnya apakah tiketnya dsudah di input
                        $check_ticket_manual=$this->ticket->select_data("app.t_trx_ticket_vehicle_manual"," where upper(ticket_number_manual)='".strtoupper($value['A'])."' and status=1 " );

                        if($check_ticket_manual->num_rows()>0)
                        {
                            $ticket_mn_err[]=1;
                            $ticket_mn_err_name[]=" {$value['A']} Baris ".$row_cell." kolom A ";
                        }

                        //re temp data
                        $data_excel[$index]=array("ticket_number_manual"=>$value['A'],
                                            "passanger_name"=>$value['B'],
                                            "gender"=>strtoupper($value['C']),
                                            "plat_no"=>strtoupper($value['D']),
                                            "ship_class"=>$get_ship_class_id,
                                            "vehicle_class"=>$get_vehicle_class_id,
                                            "total_passanger"=>$value['G'],
                                        );

                        $index++;

                    }

                    $row_cell++;
                }
            }

        }

        // print_r($data_excel); exit;

        $data=array();
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        } 
        else if(array_sum($empty_err)>0)            
        {
            echo $res=json_api(0, implode(", ",$empty_name_err)." Harus di isi" );
        }
        else if(empty($_FILES['excel']['name']))            
        {
            echo $res=json_api(0," File xlsx Harus di isi" );
        }   
        else if(array_sum($ship_class_err)>0)            
        {
            echo $res=json_api(0, "Tipe Kapal ".implode(", ",$ship_class_err_name)." Tidak ada" );
        }
        else if(array_sum($vehicle_class_err)>0)            
        {
            echo $res=json_api(0, "Golongan ".implode(", ",$vehicle_class_name_err)." Tidak ada" );
        }        
        else if(array_sum($gender_err)>0)            
        {
            echo $res=json_api(0, " Jenis Kelamin".implode(", ",$gender_err_name)." Tidak ada" );
        }   
        else if(array_sum($ticket_mn_err)>0)            
        {
            echo $res=json_api(0, "Tiket manual ".implode(", ",$ticket_mn_err_name)." Sudah ada" );
        }        
        else
        {

            // print_r($data); exit;

            $this->db->trans_begin();

            // print_r($data_excel); exit;
            foreach ($data_excel as $key => $my_data_excel) {

                $get_route=$this->ticket->select_data("app.t_mtr_rute", " where id={$route_id} ")->row();

                $getBranchCode = $this->ticket->select_data("app.t_mtr_branch"," where port_id='{$port_id}' and ship_class='{$get_ship_class_id}' and status=1")->row();

                $identity=strlen($port_id)>1?'S'.$port_id:'S0'.$port_id;

                $id_origin=strlen($get_route->origin)>1?$get_route->origin:'0'.$get_route->origin;
                $id_destination=strlen($get_route->destination)>1?$get_route->destination:'0'.$get_route->destination;


                $trans_number=$this->ticket->get_trans_number($identity);
                $booking_code=$this->generateBookingCode();
                $new_ticket='MN'.$id_origin.$id_destination.$booking_code; // jika loping jadiin i++
                $ticket_vehicle=$new_ticket."01";
                // $ticket_passanger=$new_ticket."02";

                $get_branch=$this->ticket->select_data("app.t_mtr_branch", " where port_id={$port_id} and ship_class={$my_data_excel['ship_class']} and status=1" )->row();
                $get_fare=$this->ticket->select_data("app.t_mtr_fare_vehicle", " where rute_id={$route_id} and ship_class={$my_data_excel['ship_class']} and vehicle_class_id={$my_data_excel['vehicle_class']} and status=1" )->row();

                if($my_data_excel['ship_class']==1)
                {
                    $checkin_param='checkin_expired_goshow';
                    $gatein_param='gatein_expired_goshow';

                }
                else
                {
                    $checkin_param='checkin_expired_goshow_eksekutif';
                    $gatein_param='gatein_expired_goshow_eksekutif';
                }

                $boarding_param='boarding_expired';

                $get_expire_checkin=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('$checkin_param') " )->row();
                $get_expire_gatein=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$gatein_param}') " )->row();
                $get_expire_boarding=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$boarding_param}') " )->row();

                // mengambil data expire yang diambil trxdate
                $checkin_expired=date("Y-m-d H:i:s",strtotime($get_expire_checkin->param_value." ".$get_expire_checkin->value_type,strtotime(date("Y-m-d H:i:s"))));
                $gatein_expired=date("Y-m-d H:i:s",strtotime($get_expire_gatein->param_value." ".$get_expire_gatein->value_type,strtotime(date("Y-m-d H:i:s"))));
                $boarding_expired=date("Y-m-d H:i:s",strtotime($get_expire_boarding->param_value." ".$get_expire_boarding->value_type,strtotime(date("Y-m-d H:i:s"))));

                $get_extra_fee=$this->ticket->select_data("app.t_mtr_payment_type"," where upper(payment_type)=upper('cash') ")->row();

                if($get_fare->fare<1)
                {
                    $total_amount=$get_fare->fare;
                }
                else
                {
                    $total_amount=$get_fare->fare+$get_extra_fee->extra_fee;
                }

                $getTerminalCode=$this->ticket->getTerminalCode($port_id);

                $data_ticket_manual=array(
                    'trans_number'=>$trans_number->trans_number,
                    'ticket_number_manual'=>strtoupper(trim($my_data_excel['ticket_number_manual'])),
                    'ticket_number'=>$ticket_vehicle,
                    'name'=>$my_data_excel['passanger_name'],
                    'id_number'=>$my_data_excel['plat_no'],
                    'total_passanger'=>$my_data_excel['total_passanger'],
                    'status'=>1,
                    'vehicle_class'=>$my_data_excel['vehicle_class'],
                    'trx_date'=>$trx_date,
                    'shift_id'=>$shift_id,
                    'port_id'=>$port_id,
                    'ob_code'=>$ob_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username"),
                    'ship_class'=>$my_data_excel['ship_class']
                );

                //trx_invoice
                $data_invoice=array(
                  'terminal_code'=>$getTerminalCode,
                  'amount'=>$get_fare->fare,
                  'trans_number'=>$trans_number->trans_number,
                  'extra_fee'=>$get_extra_fee->extra_fee,
                  'total_amount'=>$total_amount,
                  'customer_name'=>$my_data_excel['passanger_name'],
                  'phone_number'=>"0",
                  'email'=>"",
                  'booking_channel'=>'web_admin',
                  'channel'=>'web_admin',
                  'service_id'=>$service_id,
                  'ticket_type'=>3,         // tipe tiket 1 normal, 2 sab, 3 tiket manual
                  'status'=>2,
                  'created_on'=>date("Y-m-d H:i:s"),
                  'port_id'=>$port_id,
                  'due_date'=>$trx_date,
                  'invoice_date'=>$trx_date,
                  'payment_type'=>'cash',
                  'branch_code'=>$get_branch->branch_code,
                  'created_by'=>$this->session->userdata('username'),
                  );



                //trx_payment
                $data_payment=array(
                  'payment_type'=>"cash",
                  'amount'=>$total_amount,
                  'booking_code'=>$booking_code,
                  'payment_date'=>$trx_date,
                  'trans_number'=>$trans_number->trans_number,
                  'channel'=>"web_admin",
                  'status'=>1,
                  'created_on'=>date("Y-m-d H:i:s"),
                  'created_by'=>$this->session->userdata('username'),
                  );


                //trx_booking
                $data_booking=array(
                    'service_id'=>$service_id,
                    'booking_code'=>$booking_code,
                    'trans_number'=> $trans_number->trans_number,
                    'total_passanger'=>$my_data_excel['total_passanger'],
                    'origin'=>$get_route->origin,
                    'destination'=>$get_route->destination,
                    'ship_class'=>$my_data_excel['ship_class'],
                    'amount'=>$get_fare->fare,
                    'depart_date'=>$trx_date,
                    'depart_time_start'=>"00:00",
                    'depart_time_end'=>"00:00",
                    'channel'=>'web_admin',
                    'checkin_expired'=>$checkin_expired,
                    'gatein_expired'=>$gatein_expired,
                    'ticket_type'=>3,
                    'status'=>2,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    'branch_code'=>$getBranchCode->branch_code,
                    'track_code'=>$get_route->track_code
                  );


                $data_booking_passanger_res=array();

                $prefixTicket=2;
                
                for($i=0; $i<$my_data_excel['total_passanger']; $i++)
                {   
                    $ticket_passanger=strlen($prefixTicket)>1?$new_ticket.$prefixTicket:$new_ticket."0".$prefixTicket;

                    // trx booking passanger                    
                    $data_booking_passanger = array(
                        'id_number'=>0,
                        'name'=>$my_data_excel['passanger_name'],
                        'age'=>0,
                        'gender'=>$my_data_excel['gender'],
                        'passanger_type_id'=>1, // hardcord 1 dewasa
                        'fare'=>0,
                        'entry_fee'=>0,
                        'dock_fee'=>0,
                        'trip_fee'=>0,
                        'adm_fee'=>0,
                        'responsibility_fee'=>0,
                        'insurance_fee'=>0,
                        'ifpro_fee'=>0,
                        'booking_code'=>$booking_code,
                        'ticket_number'=>$ticket_passanger,
                        'origin'=>$get_route->origin,
                        'destination'=>$get_route->destination,
                        'ship_class'=>$my_data_excel['ship_class'],
                        'depart_date'=>$trx_date,
                        'depart_time_start'=>"00:00",
                        'depart_time_end'=>"00:00",
                        'service_id'=>$service_id,
                        'checkin_expired'=>$checkin_expired,
                        'gatein_expired'=>$gatein_expired,
                        'boarding_expired'=>$boarding_expired,
                        'channel'=>'web_admin',
                        'ticket_type'=>3,
                        'status'=>4,
                        'track_code'=>$get_route->track_code,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata('username'),
                    ); 

                    $data_booking_passanger_res[]=$data_booking_passanger;

                    // insert booking passanger
                    $this->ticket->insert_data("app.t_trx_booking_passanger",$data_booking_passanger);

                    $prefixTicket++;

                }

                $data_booking_vehicle=array
                (
                    "id_number"=>$my_data_excel['plat_no'],
                    "status"=>4,
                    "created_by"=>$this->session->userdata("username"),
                    "created_on"=>date("Y-m-d H:i:s"),
                    "vehicle_class_id"=>$my_data_excel['vehicle_class'],
                    "ticket_number"=>$ticket_vehicle,
                    'fare'=>$get_fare->fare,
                    'trip_fee'=>$get_fare->trip_fee,
                    'adm_fee'=>$get_fare->adm_fee,

                    'entry_fee'=>$get_fare->entry_fee,
                    'dock_fee'=>$get_fare->dock_fee,
                    'responsibility_fee'=>$get_fare->responsibility_fee,
                    'insurance_fee'=>$get_fare->insurance_fee,
                    'ifpro_fee'=>$get_fare->ifpro_fee,

                    'booking_code'=>$booking_code,                    
                    'length'=>0,
                    'height'=>0,
                    'weight'=>0,
                    'origin'=>$get_route->origin,
                    'destination'=>$get_route->destination,
                    'depart_date'=>$trx_date,
                    'ship_class'=>$my_data_excel['ship_class'],
                    'depart_time_start'=>"00:00",
                    'depart_time_end'=>"00:00",
                    'service_id'=>$service_id,
                    'checkin_expired'=>$checkin_expired,
                    'gatein_expired'=>$gatein_expired,
                    'boarding_expired'=>$boarding_expired,
                    'channel'=>'web_admin',
                    'ticket_type'=>3,
                    'track_code'=>$get_route->track_code,
                ); 

                // app.t_trx_sell
                $data_sell=array(
                    'terminal_code'=>$getTerminalCode,
                    'trans_number'=>$trans_number->trans_number,
                    'amount'=>$total_amount,
                    'booking_channel'=>'web_admin',
                    'created_by'=>$this->session->userdata("username"),
                    'created_on'=>date("Y-m-d H:i:s"),
                    'payment_type'=>'cash',
                    'ob_code'=>$ob_code,
                    'payment_date'=>$trx_date,
                );       

                // trx_checkin
                $data_checkin=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_passanger,
                    'booking_code'=>$booking_code,
                    'checkin_web'=>1,
                    'checkin_pos'=>0,
                    'reprint'=>0,
                    'channel'=>"web_admin",
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$ob_code,

                );

                $data_checkin_vehicle=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_vehicle,
                    'booking_code'=>$booking_code,
                    'checkin_web'=>1,
                    'checkin_pos'=>0,
                    'reprint'=>0,
                    'channel'=>"web_admin",
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$ob_code,

                );                    

                // data gatein
                $data_gatein=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_passanger,
                    'booking_code'=>$booking_code,
                    'boarding_expired'=>$boarding_expired,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                );      

                // data gatein
                $data_gatein_vehicle=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_vehicle,
                    'booking_code'=>$booking_code,
                    'boarding_expired'=>$boarding_expired,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                );      
                
                $data[]=array($data_ticket_manual,
                    $data_invoice,
                    $data_payment,
                    $data_booking,
                    $data_booking_passanger_res,
                    $data_booking_vehicle,
                    $data_sell,
                    $data_checkin,
                    $data_checkin_vehicle,
                    $data_gatein,
                    $data_gatein_vehicle,
                );

                $this->ticket->insert_data("app.t_trx_ticket_vehicle_manual",$data_ticket_manual);
                $this->ticket->insert_data("app.t_trx_invoice",$data_invoice);
                $this->ticket->insert_data("app.t_trx_payment",$data_payment);
                $this->ticket->insert_data("app.t_trx_booking",$data_booking);
                // $this->ticket->insert_data("app.t_trx_booking_passanger",$data_booking_passanger);
                $this->ticket->insert_data("app.t_trx_booking_vehicle",$data_booking_vehicle);
                $this->ticket->insert_data("app.t_trx_check_in",$data_checkin);
                $this->ticket->insert_data("app.t_trx_check_in_vehicle",$data_checkin_vehicle);
                $this->ticket->insert_data("app.t_trx_gate_in",$data_gatein);
                $this->ticket->insert_data("app.t_trx_gate_in_vehicle",$data_gatein_vehicle);
                $this->ticket->insert_data("app.t_trx_sell",$data_sell);

                $this->ticket->edit_total_cash($total_amount,$ob_code);

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

        // print_r($data); exit;

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/ticket_sobek/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }        

    public function action_add_vehicle_22062021()
    {
        $port_id=$this->enc->decode($this->input->post("port"));
        $trx_date=$this->input->post("trx_date");
        $shift_id=$this->enc->decode($this->input->post("shift"));
        $ob_code=$this->input->post("ob_code");
        $service_id=$this->enc->decode($this->input->post("service"));
        $route_id=$this->enc->decode($this->input->post("route"));

        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('trx_date', 'Tanggal Transaksi', 'required');
        $this->form_validation->set_rules('shift', 'Shift', 'required');
        $this->form_validation->set_rules('ob_code', 'Nama Penjual', 'required');
        $this->form_validation->set_rules('service', 'Servis', 'required');
        $this->form_validation->set_rules('route', 'Rute', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');


        $empty_err = array();
        $empty_name_err = array();

        $ship_class_err = array();
        $ship_class_err_name = array();

        $vehicle_class_err = array();
        $vehicle_class_name_err = array();

        $gender_err = array();
        $gender_err_name = array();

        $ticket_mn_err = array();
        $ticket_mn_err_name = array(); 
        if(!empty($_FILES['excel']['name']))
        {            
            $file = $_FILES['excel']['tmp_name'];
            $load = PHPExcel_IOFactory::load($file);
            $sheets = $load->getActiveSheet()->toArray(null,true,true,true);

            if(!empty($sheets))
            {   
                $row_cell=1;
                $index=0;            
                foreach ($sheets as $value) {
                    // dimulai dari baris 8
                    if($row_cell>7)
                    {

                        // validasi jika cell kosong;
                        if(empty($value['A']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Nomer Tiket Manual  Baris ".$row_cell." kolom A ";
                        }  

                        if(empty($value['B']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Nama Penumpang Baris ".$row_cell." kolom B ";
                        }  

                        if(empty($value['C']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Jenis Kelamin Baris ".$row_cell." kolom C ";
                        }  

                        if(empty($value['D']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Plat Nomer Baris ".$row_cell." kolom D ";
                        }                                                              

                        if(empty($value['E']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Tipe Kapal Baris ".$row_cell." kolom E ";
                        }

                        if(empty($value['F']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Golongan Kendaraan Baris ".$row_cell." kolom F ";
                        }


                        if(empty($value['G']))
                        {
                            $empty_err[]=1;
                            $empty_name_err[]=" Total Penumpang Baris ".$row_cell." kolom G ";
                        }                                        

                        // check validasi... apakah ship classnya ada
                        $check_ship_class=$this->ticket->select_data(" app.t_mtr_ship_class"," where upper(name)=upper('".trim($value['E'])."')  and status=1 ");

                        if($check_ship_class->num_rows()<1)
                        {
                            $ship_class_err[]=1;
                            $ship_class_err_name[]="{$value['E']}";
                            $get_ship_class_id="";
                        }
                        else
                        {

                            $get_ship_class_id=$check_ship_class->row()->id;                        
                        }

                        // check validasi... vehicle class
                        $check_vehicle_class=$this->ticket->select_data(" app.t_mtr_vehicle_class"," where upper(name)=upper('".trim($value['F'])."')  and status=1 ");

                        if($check_vehicle_class->num_rows()<1)
                        {
                            $vehicle_class_err[]=1;
                            $vehicle_class_name_err[]=" {$value['F']}";
                            $get_vehicle_class_id="";
                        }
                        else
                        {

                            $get_vehicle_class_id=$check_vehicle_class->row()->id;                        
                        }


                        // check apakah Jenis kelaminya di tulis selain P (perempuan) atau L (Laki - laki)
                        if(strtoupper(trim($value['C'])) !='L' and strtoupper(trim($value['C'])) !='P' )
                        {
                            $gender_err[]=1;
                            $c_upper=strtoupper($value['C']);
                            $gender_err_name[]="{$c_upper} ";
                        }

                        // ccheck ticket manualnya apakah tiketnya dsudah di input
                        $check_ticket_manual=$this->ticket->select_data("app.t_trx_ticket_vehicle_manual"," where upper(ticket_number_manual)='".strtoupper($value['A'])."' " );

                        if($check_ticket_manual->num_rows()>0)
                        {
                            $ticket_mn_err[]=1;
                            $ticket_mn_err_name[]=" {$value['A']}";
                        }

                        //re temp data
                        $data_excel[$index]=array("ticket_number_manual"=>$value['A'],
                                            "passanger_name"=>$value['B'],
                                            "gender"=>strtoupper($value['C']),
                                            "plat_no"=>strtoupper($value['D']),
                                            "ship_class"=>$get_ship_class_id,
                                            "vehicle_class"=>$get_vehicle_class_id,
                                            "total_passanger"=>$value['G'],
                                        );

                        $index++;

                    }

                    $row_cell++;
                }
            }

        }

        // print_r($data_excel); exit;

        $data=array();
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        } 
        else if(array_sum($empty_err)>0)            
        {
            echo $res=json_api(0, implode(", ",$empty_name_err)." Harus di isi" );
        }
        else if(empty($_FILES['excel']['name']))            
        {
            echo $res=json_api(0," File xlsx Harus di isi" );
        }   
        else if(array_sum($ship_class_err)>0)            
        {
            echo $res=json_api(0, "Tipe Kapal ".implode(", ",$ship_class_err_name)." Tidak ada" );
        }
        else if(array_sum($vehicle_class_err)>0)            
        {
            echo $res=json_api(0, "Golongan ".implode(", ",$vehicle_class_name_err)." Tidak ada" );
        }        
        else if(array_sum($gender_err)>0)            
        {
            echo $res=json_api(0, " Jenis Kelamin".implode(", ",$gender_err_name)." Tidak ada" );
        }   
        else if(array_sum($ticket_mn_err)>0)            
        {
            echo $res=json_api(0, "Tiket Manual ".implode(", ",$ticket_mn_err_name)." Sudah ada" );
        }        
        else
        {

            // print_r($data); exit;

            $this->db->trans_begin();

            // print_r($data_excel); exit;
            foreach ($data_excel as $key => $my_data_excel) {

                $get_route=$this->ticket->select_data("app.t_mtr_rute", " where id={$route_id} ")->row();

                $getBranchCode = $this->ticket->select_data("app.t_mtr_branch"," where port_id='{$port_id}' and ship_class='{$get_ship_class_id}' and status=1")->row();

                $identity=strlen($port_id)>1?'S'.$port_id:'S0'.$port_id;

                $id_origin=strlen($get_route->origin)>1?$get_route->origin:'0'.$get_route->origin;
                $id_destination=strlen($get_route->destination)>1?$get_route->destination:'0'.$get_route->destination;


                $trans_number=$this->ticket->get_trans_number($identity);
                $booking_code=$this->generateBookingCode();
                $new_ticket='MN'.$id_origin.$id_destination.$booking_code; // jika loping jadiin i++
                $ticket_vehicle=$new_ticket."01";
                $ticket_passanger=$new_ticket."02";

                $get_branch=$this->ticket->select_data("app.t_mtr_branch", " where port_id={$port_id} and ship_class={$my_data_excel['ship_class']}" )->row();
                $get_fare=$this->ticket->select_data("app.t_mtr_fare_vehicle", " where rute_id={$route_id} and ship_class={$my_data_excel['ship_class']} and vehicle_class_id={$my_data_excel['vehicle_class']}" )->row();

                if($my_data_excel['ship_class']==1)
                {
                    $checkin_param='checkin_expired_goshow';
                    $gatein_param='gatein_expired_goshow';

                }
                else
                {
                    $checkin_param='checkin_expired_goshow_eksekutif';
                    $gatein_param='gatein_expired_goshow_eksekutif';
                }

                $boarding_param='boarding_expired';

                $get_expire_checkin=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('$checkin_param') " )->row();
                $get_expire_gatein=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$gatein_param}') " )->row();
                $get_expire_boarding=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$boarding_param}') " )->row();

                // mengambil data expire yang diambil trxdate
                $checkin_expired=date("Y-m-d H:i:s",strtotime($get_expire_checkin->param_value." ".$get_expire_checkin->value_type,strtotime(date("Y-m-d H:i:s"))));
                $gatein_expired=date("Y-m-d H:i:s",strtotime($get_expire_gatein->param_value." ".$get_expire_gatein->value_type,strtotime(date("Y-m-d H:i:s"))));
                $boarding_expired=date("Y-m-d H:i:s",strtotime($get_expire_boarding->param_value." ".$get_expire_boarding->value_type,strtotime(date("Y-m-d H:i:s"))));

                $get_extra_fee=$this->ticket->select_data("app.t_mtr_payment_type"," where upper(payment_type)=upper('cash') ")->row();

                if($get_fare->fare<1)
                {
                    $total_amount=$get_fare->fare;
                }
                else
                {
                    $total_amount=$get_fare->fare+$get_extra_fee->extra_fee;
                }

                $getTerminalCode=$this->ticket->getTerminalCode($port_id);

                $data_ticket_manual=array(
                    'trans_number'=>$trans_number->trans_number,
                    'ticket_number_manual'=>strtoupper(trim($my_data_excel['ticket_number_manual'])),
                    'ticket_number'=>$ticket_vehicle,
                    'name'=>$my_data_excel['passanger_name'],
                    'id_number'=>$my_data_excel['plat_no'],
                    'total_passanger'=>$my_data_excel['total_passanger'],
                    'status'=>1,
                    'vehicle_class'=>$my_data_excel['vehicle_class'],
                    'trx_date'=>$trx_date,
                    'shift_id'=>$shift_id,
                    'port_id'=>$port_id,
                    'ob_code'=>$ob_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username"),
                    'ship_class'=>$my_data_excel['ship_class']
                );

                //trx_invoice
                $data_invoice=array(
                  'terminal_code'=>$getTerminalCode,
                  'amount'=>$get_fare->fare,
                  'trans_number'=>$trans_number->trans_number,
                  'extra_fee'=>$get_extra_fee->extra_fee,
                  'total_amount'=>$total_amount,
                  'customer_name'=>$my_data_excel['passanger_name'],
                  'phone_number'=>"0",
                  'email'=>"",
                  'booking_channel'=>'web_admin',
                  'channel'=>'web_admin',
                  'service_id'=>$service_id,
                  'ticket_type'=>3,         // tipe tiket 1 normal, 2 sab, 3 tiket manual
                  'status'=>2,
                  'created_on'=>date("Y-m-d H:i:s"),
                  'port_id'=>$port_id,
                  'due_date'=>$trx_date,
                  'invoice_date'=>$trx_date,
                  'payment_type'=>'cash',
                  'branch_code'=>$get_branch->branch_code,
                  'created_by'=>$this->session->userdata('username'),
                  );



                //trx_payment
                $data_payment=array(
                  'payment_type'=>"cash",
                  'amount'=>$total_amount,
                  'booking_code'=>$booking_code,
                  'payment_date'=>$trx_date,
                  'trans_number'=>$trans_number->trans_number,
                  'channel'=>"web_admin",
                  'status'=>1,
                  'created_on'=>date("Y-m-d H:i:s"),
                  'created_by'=>$this->session->userdata('username'),
                  );


                //trx_booking
                $data_booking=array(
                    'service_id'=>$service_id,
                    'booking_code'=>$booking_code,
                    'trans_number'=> $trans_number->trans_number,
                    'total_passanger'=>$my_data_excel['total_passanger'],
                    'origin'=>$get_route->origin,
                    'destination'=>$get_route->destination,
                    'ship_class'=>$my_data_excel['ship_class'],
                    'amount'=>$get_fare->fare,
                    'depart_date'=>$trx_date,
                    'depart_time_start'=>"00:00",
                    'depart_time_end'=>"00:00",
                    'channel'=>'web_admin',
                    'checkin_expired'=>$checkin_expired,
                    'gatein_expired'=>$gatein_expired,
                    'ticket_type'=>3,
                    'status'=>2,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    'branch_code'=>$getBranchCode->branch_code,
                    'track_code'=>$get_route->track_code
                  );


                // trx booking passanger
                $data_booking_passanger = array(
                    'id_number'=>0,
                    'name'=>$my_data_excel['passanger_name'],
                    'age'=>0,
                    'gender'=>$my_data_excel['gender'],
                    'passanger_type_id'=>1, // hardcord 1 dewasa
                    'fare'=>0,
                    'entry_fee'=>0,
                    'dock_fee'=>0,
                    'trip_fee'=>0,
                    'adm_fee'=>0,
                    'responsibility_fee'=>0,
                    'insurance_fee'=>0,
                    'ifpro_fee'=>0,
                    'booking_code'=>$booking_code,
                    'ticket_number'=>$ticket_passanger,
                    'origin'=>$get_route->origin,
                    'destination'=>$get_route->destination,
                    'ship_class'=>$my_data_excel['ship_class'],
                    'depart_date'=>$trx_date,
                    'depart_time_start'=>"00:00",
                    'depart_time_end'=>"00:00",
                    'service_id'=>$service_id,
                    'checkin_expired'=>$checkin_expired,
                    'gatein_expired'=>$gatein_expired,
                    'boarding_expired'=>$boarding_expired,
                    'channel'=>'web_admin',
                    'ticket_type'=>3,
                    'status'=>4,
                    'track_code'=>$get_route->track_code,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                ); 

                $data_booking_vehicle=array
                (
                    "id_number"=>$my_data_excel['plat_no'],
                    "status"=>4,
                    "created_by"=>$this->session->userdata("username"),
                    "created_on"=>date("Y-m-d H:i:s"),
                    "vehicle_class_id"=>$my_data_excel['vehicle_class'],
                    "ticket_number"=>$ticket_vehicle,
                    'fare'=>$get_fare->fare,
                    'trip_fee'=>$get_fare->trip_fee,
                    'adm_fee'=>$get_fare->adm_fee,

                    'entry_fee'=>$get_fare->entry_fee,
                    'dock_fee'=>$get_fare->dock_fee,
                    'responsibility_fee'=>$get_fare->responsibility_fee,
                    'insurance_fee'=>$get_fare->insurance_fee,
                    'ifpro_fee'=>$get_fare->ifpro_fee,

                    'booking_code'=>$booking_code,                    
                    'length'=>0,
                    'height'=>0,
                    'weight'=>0,
                    'origin'=>$get_route->origin,
                    'destination'=>$get_route->destination,
                    'depart_date'=>$trx_date,
                    'ship_class'=>$my_data_excel['ship_class'],
                    'depart_time_start'=>"00:00",
                    'depart_time_end'=>"00:00",
                    'service_id'=>$service_id,
                    'checkin_expired'=>$checkin_expired,
                    'gatein_expired'=>$gatein_expired,
                    'boarding_expired'=>$boarding_expired,
                    'channel'=>'web_admin',
                    'ticket_type'=>3,
                    'track_code'=>$get_route->track_code,
                ); 

                // app.t_trx_sell
                $data_sell=array(
                    'terminal_code'=>$getTerminalCode,
                    'trans_number'=>$trans_number->trans_number,
                    'amount'=>$total_amount,
                    'booking_channel'=>'web_admin',
                    'created_by'=>$this->session->userdata("username"),
                    'created_on'=>date("Y-m-d H:i:s"),
                    'payment_type'=>'cash',
                    'ob_code'=>$ob_code,
                    'payment_date'=>$trx_date,
                );       

                // trx_checkin
                $data_checkin=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_passanger,
                    'booking_code'=>$booking_code,
                    'checkin_web'=>1,
                    'checkin_pos'=>0,
                    'reprint'=>0,
                    'channel'=>"web_admin",
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$ob_code,

                );

                $data_checkin_vehicle=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_vehicle,
                    'booking_code'=>$booking_code,
                    'checkin_web'=>1,
                    'checkin_pos'=>0,
                    'reprint'=>0,
                    'channel'=>"web_admin",
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$ob_code,

                );                    

                // data gatein
                $data_gatein=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_passanger,
                    'booking_code'=>$booking_code,
                    'boarding_expired'=>$boarding_expired,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                );      

                // data gatein
                $data_gatein_vehicle=array(
                    'terminal_code'=>$getTerminalCode,
                    'ticket_number'=>$ticket_vehicle,
                    'booking_code'=>$booking_code,
                    'boarding_expired'=>$boarding_expired,
                    'status'=>1,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                );      
                
                $data[]=array($data_ticket_manual,
                    $data_invoice,
                    $data_payment,
                    $data_booking,
                    $data_booking_passanger,
                    $data_booking_vehicle,
                    $data_sell,
                    $data_checkin,
                    $data_checkin_vehicle,
                    $data_gatein,
                    $data_gatein_vehicle,
                );

                $this->ticket->insert_data("app.t_trx_ticket_vehicle_manual",$data_ticket_manual);
                $this->ticket->insert_data("app.t_trx_invoice",$data_invoice);
                $this->ticket->insert_data("app.t_trx_payment",$data_payment);
                $this->ticket->insert_data("app.t_trx_booking",$data_booking);
                $this->ticket->insert_data("app.t_trx_booking_passanger",$data_booking_passanger);
                $this->ticket->insert_data("app.t_trx_booking_vehicle",$data_booking_vehicle);
                $this->ticket->insert_data("app.t_trx_check_in",$data_checkin);
                $this->ticket->insert_data("app.t_trx_check_in_vehicle",$data_checkin_vehicle);
                $this->ticket->insert_data("app.t_trx_gate_in",$data_gatein);
                $this->ticket->insert_data("app.t_trx_gate_in_vehicle",$data_gatein_vehicle);
                $this->ticket->insert_data("app.t_trx_sell",$data_sell);

                $this->ticket->edit_total_cash($total_amount,$ob_code);

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

        // print_r($data); exit;

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/ticket_sobek/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    }    

    public function checkDuplicateTicket($data, $gol,$ticketNumber,$shipClass, $hurufGolongan)
    {
        $count =0;
        $index=1;

        // print_r($data); exit;

        $checkDuplication=array();
        foreach ($data as $key => $value) {
            
            if($index>7)
            {
                if(strtoupper(trim($ticketNumber))==strtoupper(trim($value['A'])) 
                    and strtoupper(trim($gol))==strtoupper(trim($value[$hurufGolongan]))
                    and strtoupper(trim($shipClass))==strtoupper(trim($value['E']))
                )
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



    // public function action_add_vehicle()
    // {
    //     $port_id=$this->enc->decode($this->input->post("port"));
    //     $trx_date=$this->input->post("trx_date");
    //     $shift_id=$this->enc->decode($this->input->post("shift"));
    //     $ob_code=$this->input->post("ob_code");
    //     $service_id=$this->enc->decode($this->input->post("service"));
    //     $ticket_manual=$this->input->post("ticket_manual");
    //     $passanger_name=$this->input->post("passanger_name");
    //     $gender=$this->input->post("gender");
    //     // $passanger_type_id=$this->enc->decode($this->input->post("passanger_type"));
    //     $ship_class=$this->enc->decode($this->input->post("ship_class"));
    //     $route_id=$this->enc->decode($this->input->post("route"));
    //     $vehicle_class=$this->enc->decode($this->input->post("vehicle_type"));
    //     $plat_no=trim($this->input->post("plat_no"));
    //     $total_passanger=trim($this->input->post("total_passanger"));

    //     $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
    //     $this->form_validation->set_rules('trx_date', 'Tanggal Transaksi', 'required');
    //     $this->form_validation->set_rules('shift', 'Shift', 'required');
    //     $this->form_validation->set_rules('ob_code', 'Nama Penjual', 'required');
    //     $this->form_validation->set_rules('service', 'Servis', 'required');
    //     $this->form_validation->set_rules('ticket_manual', 'Nomer Ticket Manual', 'required');
    //     $this->form_validation->set_rules('passanger_name', 'Nama Penumpang', 'required');
    //     $this->form_validation->set_rules('gender', 'Jenis Kelamin', 'required');
    //     // $this->form_validation->set_rules('passanger_type', 'Tipe Penumpang', 'required');
    //     $this->form_validation->set_rules('ship_class', 'Tipe', 'required');
    //     $this->form_validation->set_rules('plat_no', ' Nomer Plat ', 'required');
    //     $this->form_validation->set_rules('total_passanger', ' Total Penumpang ', 'required');
    //     $this->form_validation->set_rules('vehicle_type', ' Golongan Kendaraan ', 'required');

    //     $this->form_validation->set_message('required','%s harus diisi!');

    //     $get_route=$this->ticket->select_data("app.t_mtr_rute", " where id={$route_id} ")->row();

    //     $identity='S'.$this->identity_app();
    //     $trans_number=$this->ticket->get_trans_number($identity);
    //     $booking_code=$this->generateBookingCode();
    //     $new_ticket='MN'.$get_route->origin.$get_route->destination.$booking_code; // jika loping jadiin i++
    //     $ticket_vehicle=$new_ticket."01";
    //     $ticket_passanger=$new_ticket."02";

    //     $get_branch=$this->ticket->select_data("app.t_mtr_branch", " where port_id={$port_id} and ship_class={$ship_class}" )->row();
    //     $get_fare=$this->ticket->select_data("app.t_mtr_fare_vehicle", " where rute_id={$route_id} and ship_class={$ship_class} and vehicle_class_id={$vehicle_class}" )->row();

    //     if($ship_class==1)
    //     {
    //         $checkin_param='checkin_expired_goshow';
    //         $gatein_param='gatein_expired_goshow';

    //     }
    //     else
    //     {
    //         $checkin_param='checkin_expired_goshow_eksekutif';
    //         $gatein_param='gatein_expired_goshow_eksekutif';
    //     }

    //     $boarding_param='boarding_expired';

    //     $get_expire_checkin=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('$checkin_param') " )->row();
    //     $get_expire_gatein=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$gatein_param}') " )->row();
    //     $get_expire_boarding=$this->ticket->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('{$boarding_param}') " )->row();

    //     // mengambil data expire yang diambil trxdate
    //     $checkin_expired=date("Y-m-d H:i:s",strtotime($get_expire_checkin->param_value." ".$get_expire_checkin->value_type,strtotime($trx_date)));
    //     $gatein_expired=date("Y-m-d H:i:s",strtotime($get_expire_gatein->param_value." ".$get_expire_gatein->value_type,strtotime($trx_date)));
    //     $boarding_expired=date("Y-m-d H:i:s",strtotime($get_expire_boarding->param_value." ".$get_expire_boarding->value_type,strtotime($trx_date)));

    //     $get_extra_fee=$this->ticket->select_data("app.t_mtr_payment_type"," where upper(payment_type)=upper('cash') ")->row();

    //     if($get_fare->fare<1)
    //     {
    //         $total_amount=$get_fare->fare;
    //     }
    //     else
    //     {
    //         $total_amount=$get_fare->fare+$get_extra_fee->extra_fee;
    //     }


    //     $data_ticket_manual=array(
    //         'trans_number'=>$trans_number->trans_number,
    //         'ticket_number_manual'=>$ticket_manual,
    //         'ticket_number'=>$ticket_vehicle,
    //         'name'=>$passanger_name,
    //         'id_number'=>$plat_no,
    //         'total_passanger'=>$total_passanger,
    //         // 'gender'=>$gender,
    //         // 'address'=>$address,
    //         // 'passanger_type'=>$passanger_type_id,
    //         'status'=>1,
    //         'vehicle_class'=>$vehicle_class,
    //         'trx_date'=>$trx_date,
    //         'shift_id'=>$shift_id,
    //         'port_id'=>$port_id,
    //         'ob_code'=>$ob_code,
    //         'created_on'=>date("Y-m-d H:i:s"),
    //         'created_by'=>$this->session->userdata("username"),
    //         'ship_class'=>$ship_class
    //     );

    //     //trx_invoice
    //     $data_invoice=array(
    //       'amount'=>$get_fare->fare,
    //       'trans_number'=>$trans_number->trans_number,
    //       'extra_fee'=>$get_extra_fee->extra_fee,
    //       'total_amount'=>$total_amount,
    //       'customer_name'=>$passanger_name,
    //       'phone_number'=>"0",
    //       'email'=>"",
    //       'booking_channel'=>'web_admin',
    //       'channel'=>'web_admin',
    //       'service_id'=>$service_id,
    //       'ticket_type'=>3,         // tipe tiket 1 normal, 2 sab, 3 tiket manual
    //       'status'=>2,
    //       'created_on'=>date("Y-m-d H:i:s"),
    //       'port_id'=>$port_id,
    //       'due_date'=>$trx_date,
    //       'invoice_date'=>$trx_date,
    //       'payment_type'=>'cash',
    //       'branch_code'=>$get_branch->branch_code,
    //       'created_by'=>$this->session->userdata('username'),
    //       );



    //     //trx_payment
    //     $data_payment=array(
    //       'payment_type'=>"cash",
    //       'amount'=>$total_amount,
    //       'booking_code'=>$booking_code,
    //       'payment_date'=>$trx_date,
    //       'trans_number'=>$trans_number->trans_number,
    //       'channel'=>"web_admin",
    //       'status'=>1,
    //       'created_on'=>date("Y-m-d H:i:s"),
    //       'created_by'=>$this->session->userdata('username'),
    //       );


    //     //trx_booking
    //     $data_booking=array(
    //         'service_id'=>$service_id,
    //         'booking_code'=>$booking_code,
    //         'trans_number'=> $trans_number->trans_number,
    //         'total_passanger'=>$total_passanger,
    //         'origin'=>$get_route->origin,
    //         'destination'=>$get_route->destination,
    //         'ship_class'=>$ship_class,
    //         'amount'=>$get_fare->fare,
    //         'depart_date'=>$trx_date,
    //         'depart_time_start'=>"00:00",
    //         'depart_time_end'=>"00:00",
    //         'channel'=>'web_admin',
    //         'checkin_expired'=>$checkin_expired,
    //         'gatein_expired'=>$gatein_expired,
    //         'ticket_type'=>3,
    //         'status'=>2,
    //         'created_on'=>date("Y-m-d H:i:s"),
    //         'created_by'=>$this->session->userdata('username'),
    //       );


    //     // trx booking passanger
    //     $data_booking_passanger = array(
    //         'id_number'=>0,
    //         'name'=>$passanger_name,
    //         'age'=>0,
    //         'gender'=>$gender,
    //         // 'city'=>$address,
    //         // 'id_type'=>$manifest['id_type'],
    //         'passanger_type_id'=>1, // hardcord 1 dewas
    //         'fare'=>0,
    //         'entry_fee'=>0,
    //         'dock_fee'=>0,
    //         'trip_fee'=>0,
    //         'responsibility_fee'=>0,
    //         'insurance_fee'=>0,
    //         'booking_code'=>$booking_code,
    //         'ticket_number'=>$ticket_passanger,
    //         'origin'=>$get_route->origin,
    //         'destination'=>$get_route->destination,
    //         'ship_class'=>$ship_class,
    //         'depart_date'=>$trx_date,
    //         'depart_time_start'=>"00:00",
    //         'depart_time_end'=>"00:00",
    //         'service_id'=>$service_id,
    //         'checkin_expired'=>$checkin_expired,
    //         'gatein_expired'=>$gatein_expired,
    //         'boarding_expired'=>$boarding_expired,
    //         'channel'=>'web_admin',
    //         'ticket_type'=>3,
    //         'status'=>4,
    //         'track_code'=>$get_route->track_code,
    //         'created_on'=>date("Y-m-d H:i:s"),
    //         'created_by'=>$this->session->userdata('username'),
    //     ); 

    //     $data_booking_vehicle=array
    //     (
    //         "id_number"=>$plat_no,
    //         "status"=>4,
    //         "created_by"=>$this->session->userdata("username"),
    //         "created_on"=>date("Y-m-d H:i:s"),
    //         "vehicle_class_id"=>$vehicle_class,
    //         "ticket_number"=>$ticket_vehicle,
    //         'fare'=>$get_fare->fare,
    //         'booking_code'=>$booking_code,
    //         'length'=>0,
    //         'height'=>0,
    //         'weight'=>0,
    //         'origin'=>$get_route->origin,
    //         'destination'=>$get_route->destination,
    //         'depart_date'=>$trx_date,
    //         'ship_class'=>$ship_class,
    //         'depart_time_start'=>"00:00",
    //         'depart_time_end'=>"00:00",
    //         'service_id'=>$service_id,
    //         'checkin_expired'=>$checkin_expired,
    //         'gatein_expired'=>$gatein_expired,
    //         'boarding_expired'=>$boarding_expired,
    //         'channel'=>'web_admin',
    //         'ticket_type'=>3,
    //         'track_code'=>$get_route->track_code,
    //     ); 

    //     // app.t_trx_sell
    //     $data_sell=array(
    //         'trans_number'=>$trans_number->trans_number,
    //         'amount'=>$total_amount,
    //         'booking_channel'=>'web_admin',
    //         'created_by'=>$this->session->userdata("username"),
    //         'created_on'=>date("Y-m-d H:i:s"),
    //         'payment_type'=>'cash',
    //         'ob_code'=>$ob_code,
    //         'payment_date'=>$trx_date,
    //     );       

    //     // trx_checkin
    //     $data_checkin=array(
    //         'ticket_number'=>$ticket_passanger,
    //         'booking_code'=>$booking_code,
    //         'checkin_web'=>1,
    //         'checkin_pos'=>0,
    //         'reprint'=>0,
    //         'channel'=>"web_admin",
    //         'status'=>1,
    //         'created_on'=>date("Y-m-d H:i:s"),
    //         'created_by'=>$this->session->userdata('username'),

    //     );

    //     $data_checkin_vehicle=array(
    //         'ticket_number'=>$ticket_vehicle,
    //         'booking_code'=>$booking_code,
    //         'checkin_web'=>1,
    //         'checkin_pos'=>0,
    //         'reprint'=>0,
    //         'channel'=>"web_admin",
    //         'status'=>1,
    //         'created_on'=>date("Y-m-d H:i:s"),
    //         'created_by'=>$this->session->userdata('username'),

    //     );                    

    //     // data gatein
    //     $data_gatein=array(
    //         'ticket_number'=>$ticket_passanger,
    //         'booking_code'=>$booking_code,
    //         'boarding_expired'=>$boarding_expired,
    //         'status'=>1,
    //         'created_on'=>date("Y-m-d H:i:s"),
    //         'created_by'=>$this->session->userdata('username'),
    //     );      

    //     // data gatein
    //     $data_gatein_vehicle=array(
    //         'ticket_number'=>$ticket_vehicle,
    //         'booking_code'=>$booking_code,
    //         'boarding_expired'=>$boarding_expired,
    //         'status'=>1,
    //         'created_on'=>date("Y-m-d H:i:s"),
    //         'created_by'=>$this->session->userdata('username'),
    //     );      

    //     $check_ticket_number=$this->ticket->select_data("app.t_trx_ticket_vehicle_manual"," where upper(ticket_number_manual)=upper('{$ticket_manual}')");

    //     $data=array();
    //     if($this->form_validation->run()===false)
    //     {
    //         echo $res=json_api(0, validation_errors());
    //     } 
    //     else if($check_ticket_number->num_rows()>0)
    //     {
    //         echo $res=json_api(0," nomer tiket manual sudah ada");

    //     }
    //     else
    //     {

    //         $data=array($data_ticket_manual,
    //             $data_invoice,
    //             $data_payment,
    //             $data_booking,
    //             $data_booking_passanger,
    //             $data_booking_vehicle,
    //             $data_sell,
    //             $data_checkin,
    //             $data_checkin_vehicle,
    //             $data_gatein,
    //             $data_gatein_vehicle,
    //         );                       

    //         // print_r($data); exit;

    //         $this->db->trans_begin();

    //         $this->ticket->insert_data("app.t_trx_ticket_vehicle_manual",$data_ticket_manual);
    //         $this->ticket->insert_data("app.t_trx_invoice",$data_invoice);
    //         $this->ticket->insert_data("app.t_trx_payment",$data_payment);
    //         $this->ticket->insert_data("app.t_trx_booking",$data_booking);
    //         $this->ticket->insert_data("app.t_trx_booking_passanger",$data_booking_passanger);
    //         $this->ticket->insert_data("app.t_trx_booking_vehicle",$data_booking_vehicle);
    //         $this->ticket->insert_data("app.t_trx_check_in",$data_checkin);
    //         $this->ticket->insert_data("app.t_trx_check_in_vehicle",$data_checkin_vehicle);
    //         $this->ticket->insert_data("app.t_trx_gate_in",$data_gatein);
    //         $this->ticket->insert_data("app.t_trx_gate_in_vehicle",$data_gatein_vehicle);
    //         $this->ticket->insert_data("app.t_trx_sell",$data_sell);

    //         $this->ticket->edit_total_cash($total_amount,$ob_code);


    //         if ($this->db->trans_status() === FALSE)
    //         {
    //             $this->db->trans_rollback();
    //             echo $res=json_api(0, 'Gagal tambah data');
    //         }
    //         else
    //         {
    //             $this->db->trans_commit();
    //             echo $res=json_api(1, 'Berhasil tambah data');
    //         }

    //     }       

    //      /* Fungsi Create Log */
    //     $createdBy   = $this->session->userdata('username');
    //     $logUrl      = site_url().'transaction/ticket_sobek/action_add';
    //     $logMethod   = 'ADD';
    //     $logParam    = json_encode($data);
    //     $logResponse = $res;

    //     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

    // }    


    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

                // mengambil filter port berdasarkan port user
        if(!empty($this->session->userdata("port_id")))
        {
            $data['port'] = $this->route->select_data("app.t_mtr_port","where id=".$this->session->userdata("port_id")." ")->result();
        }
        else
        {
            $data['port'] = $this->route->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        }

        $id_decode=$this->enc->decode($id);

        $data['title'] = 'Edit Rute';
        $data['destination'] = $this->route->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['detail'] = $this->route->select_data("app.t_mtr_rute","where id=$id_decode")->row();


        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $origin=$this->enc->decode($this->input->post('origin'));
        $destination=$this->enc->decode($this->input->post('destination'));
        $id=$this->enc->decode($this->input->post('id'));

        $this->form_validation->set_rules('destination', 'Tujuan', 'required');
        $this->form_validation->set_rules('origin', 'Keberangakatn', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');

        $data=array(
                    'origin'=>$origin,
                    'destination'=>$destination,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        $check_route=$this->route->select_data($this->_table,"where origin=$origin and destination=$destination and status=1 and id!=$id ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if ($check_route->num_rows()>0)
        {
            echo $res=json_api(0, 'Data sudah ada');      
        }
        else
        {

            $this->db->trans_begin();

            $this->route->update_data($this->_table,$data,"id=$id");

            if ($this->db->trans_status() === FALSE)
            {   
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data');
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'fare/routee/action_edit';
        $logMethod   = 'EDIT';
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
        $this->route->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal '.$d[2]);
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil '.$d[2].' data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'fare/route/action_change';
        $d[1]==1?$logMethod='ENABLED':$logMethod='DISABLED';
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

        $id = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->route->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'fare/route/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function get_shift()
    {
        $port_id=$this->enc->decode($this->input->post('port'));

        $data=array();
        if(empty($port_id))
        {
            echo json_encode($data);
            exit;
        }

        $row=$this->ticket->get_shift($port_id);

        
        foreach ($row->result() as $key => $value) {
            $value->shift_id=$this->enc->encode($value->shift_id);
            unset($value->id);
            unset($value->port_id);
            $data[]=$value;
        }

        echo json_encode($data);
    }

    public function get_port()
    {
        $route_id=$this->enc->decode($this->input->post('route'));

        $data=array();
        if(empty($route_id))
        {
            echo json_encode($data);
            exit;
        } 

        $get_route=$this->ticket->get_route(" where a.id=$route_id ")->row();

        $port_id=$get_route->origin;

        $get_route->origin=$this->enc->encode($get_route->origin);

        unset($get_route->id);
        unset($get_route->destination);

        
        $row=$this->ticket->get_shift($port_id);
        $data_shift=array();

        if(!empty($row->result()))
        {
            foreach ($row->result() as $key => $value) {
                $value->shift_id=$this->enc->encode($value->shift_id);
                unset($value->id);
                unset($value->port_id);
                $data_shift[]=$value;
            }        
        }

        $data['route']=$get_route;
        $data['shift']=$data_shift;

        echo json_encode($data);
    }


    public function get_ob()
    {
        $shift_id=$this->enc->decode($this->input->post('shift'));
        $port_id=$this->enc->decode($this->input->post('port'));
        $trx_date2=$this->input->post('trx_date');

        $trx_date=date("Y-m-d", strtotime($trx_date2));

        $data=array();
        if(empty($port_id) or empty($shift_id) or empty($trx_date))
        {
            echo json_encode($data);
            exit;
        }

        $row=$this->ticket->get_ob($port_id,$trx_date,$shift_id);
        
        foreach ($row->result() as $key => $value) {
            $value->full_name=strtoupper($value->full_name);
            $value->all_name=$value->username." - ".$value->full_name;

            unset($value->id);
            unset($value->user_id);
            $data[]=$value;
        }

        echo json_encode($data);
    }    

    public function get_service()
    {
        $service_id=$this->enc->decode($this->input->post('service'));

        switch ($service_id) {
            case 1:
                $data='p';
                break;
            case 2:
                $data='v';
                break;                
            
            default:
                $data='not';
                break;
        }

        echo json_encode($data);
    }

    public function generateBookingCode($length = 6) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $bookCode = sprintf('%02d', $this->identity_app()).$randomString;
        return $bookCode;
        // return $randomString;
    } 

    function identity_app()
    {
        $data=$this->ticket->select_data("app.t_mtr_identity_app","")->row();

        return $data->port_id;

    }



    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");

        $data = $this->ticket->download()->result();

        $file_name = 'Ticket Manual Penumpang'.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

        $header = array(
            'NO' =>'string',
            'TANGGAL INPUT' =>'string',
            'TANGGAL TRANSAKSI' =>'string',
            'NAMA PENUMPANG' =>'string',
            'NO TICKET MANUAL'=>'string',
            'NO TICKET BARU' =>'string',
            'NO INVOICE' =>'string',
            'JENIS KELAMIN' =>'string',
            'ALAMAT' =>'string',
            'PENJUAL' =>'string',
            'SHIFT' =>'string',
            'TIPE KAPAL' =>'string',
            'PELABUHAN' =>'string',
        );


        $no=1;
        foreach ($data as $key => $value) {

            $rows[] = array($no,
                            $value->created_on,
                            $value->trx_date,
                            $value->name,
                            $value->ticket_number_manual,
                            $value->ticket_number,
                            $value->trans_number,
                            $value->gender,
                            $value->address,
                            $value->username,
                            $value->shift_name,
                            $value->ship_class_name,
                            $value->port_name,
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

    public function download_excel2()
    {
        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");

        $data = $this->ticket->download2()->result();

        // print_r($data); exit;
 
        $file_name = 'Ticket Manual Kendaraan'.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');



        $header = array(
            'NO' =>'string',
            'TANGGAL INPUT' =>'string',
            'TANGGAL TRANSAKSI' =>'string',
            'NAMA PENUMPANG' =>'string',
            'NO TICKET MANUAL'=>'string',
            'NO TICKET BARU' =>'string',
            'NO INVOICE' =>'string',
            'NO PLAT' =>'string',
            'GOLONGAN' =>'string',
            'PENJUAL' =>'string',
            'SHIFT' =>'string',
            'TIPE KAPAL' =>'string',
            'PELABUHAN' =>'string',
        );


        $no=1;
        foreach ($data as $key => $value) {

            $rows[] = array($no,
                            $value->created_on,
                            $value->trx_date,
                            $value->name,
                            $value->ticket_number_manual,
                            $value->ticket_number,
                            $value->trans_number,
                            $value->id_number,
                            $value->vehicle_class_name,
                            $value->username,
                            $value->shift_name,
                            $value->ship_class_name,
                            $value->port_name,
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





}
