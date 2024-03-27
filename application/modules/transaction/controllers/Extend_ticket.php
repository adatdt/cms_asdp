<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Extend_ticket extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_extend_ticket','extend');
        $this->load->model('global_model');

        $this->_table    = 'app.t_trx_extend_time_passanger';
        $this->_table2    = 'app.t_trx_extend_time_vehicle';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/extend_ticket';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->extend->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Perpanjang ticket',
            'content'  => 'extend_ticket/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
        );

		$this->load->view('default', $data);
	}

    public function dataListVehicle(){   
        checkUrlAccess($this->_module,'view');
        $rows = $this->extend->dataListVehicle();
        echo json_encode($rows);
    }    

    public function add(){
        validate_ajax();
        $data['title'] = 'Tambah Perpanjang Ticket';
        $this->load->view($this->_module.'/add',$data);

        // perpanjang hanya bisa yang statusnya 4 dan 7 (sudah gate in)
        // khusus pejalan kaki eksekutif, yang statusnya 3 yang sudah checkin (karna bisa langsung boarding dengan status 3)
    }

    public function action_add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $ticket_number = trim($this->input->post('ticket'));
        $extra_time = trim($this->input->post('extra_time'));

        /* validation */
        $this->form_validation->set_rules('ticket', 'Tiket', 'trim|required');
        $this->form_validation->set_rules('extra_time', 'Perpanjangan', 'trim|required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!')
        ->set_message('numeric','%s harus angka!');


        if($this->form_validation->run()===FALSE)
        {
            echo json_api(0,validation_errors());
        }
        else
        {
            $check_ticket=$this->extend->check_ticket($ticket_number)->row();

            switch($check_ticket->service_id){
                case 1 :

                    $this->action_add_passanger($check_ticket->ticket_number, $check_ticket->ship_class, $check_ticket->status,$extra_time);
                break;
                default:
                    $this->action_add_vehicle($check_ticket->booking_code, $check_ticket->ship_class, $check_ticket->status,$extra_time);
                break;
            }



        }

    }

    public function action_add_passanger($ticket_number, $ship_class, $status_ticket,$extra_time)
    {

        $check_expire=$this->extend->select_data("app.t_trx_booking_passanger", " where ticket_number='{$ticket_number}' " )->row();        
        
        switch ($status_ticket) {
            case 3:

                $new_gatein_expired=date('Y-m-d H:i:s',strtotime("+".$extra_time."hour",strtotime($check_expire->gatein_expired)));
                $data=array(
                    'ticket_number'=>$ticket_number,
                    'service_id'=>$check_expire->service_id,
                    'ticket_status'=>$status_ticket,
                    'total_time'=>$extra_time,
                    'old_gatein_expired'=>$check_expire->gatein_expired,
                    'new_gatein_expired'=>$new_gatein_expired,
                    'status'=>1,
                    'created_by'=>$this->session->userdata("username"),
                    'created_on'=>date('Y-m-d H:i:s'),
                );

                $update_data=array(
                    'updated_on'=>date('Y-m-d H:i:s'),
                    'updated_by'=>$this->session->userdata("username"),
                    'gatein_expired'=>$new_gatein_expired,
                );  
            break;
            default:
                $new_boarding_expired=date('Y-m-d H:i:s',strtotime("+".$extra_time." hour ",strtotime($check_expire->boarding_expired)));
                $data=array(
                    'ticket_number'=>$ticket_number,
                    'service_id'=>$check_expire->service_id,
                    'ticket_status'=>$status_ticket,
                    'total_time'=>$extra_time,
                    'old_boarding_expired'=>$check_expire->boarding_expired,
                    'new_boarding_expired'=>$new_boarding_expired,
                    'status'=>1,
                    'created_by'=>$this->session->userdata("username"),
                    'created_on'=>date('Y-m-d H:i:s'),
                );

                $update_data=array(
                    'updated_on'=>date('Y-m-d H:i:s'),
                    'updated_by'=>$this->session->userdata("username"),
                    'boarding_expired'=>$new_boarding_expired,
                );  
            break;
        }

        // print_r($data); exit;        

        // check ship class reguler atau eksekutif 1 reguler 2 eksekutif
        if($ship_class==1)
        {   

            // ang bisa perpoanjang hanya ticket yang statusnya 4 (sudah gate in ) dan 7 ( muntah kapal)
            if($status_ticket==4 or $status_ticket==7)
            {

                // checking apakah sudah expired ticketnya boarding expired
                if($check_expire->boarding_expired<date("Y-m-d H:i:s"))
                {
                    echo $res=json_api(0, 'Tiket sudah expired');
                }
                else 
                {
                    // cheking expire tanggal 
                    $this->db->trans_begin();

                    $this->extend->insert_data($this->_table,$data);

                    $this->extend->update_data("app.t_trx_booking_passanger",$update_data, "ticket_number='{$ticket_number}' ");

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
            else
            {
                    echo $res=json_api(0, 'Invalid Tiket');   
            }


        }
        else
        {

            // ang bisa perpoanjang hanya ticket yang statusnya 3(sudah check in) 4 (sudah gate in ) dan 7 ( muntah kapal) 

            if($status_ticket==4 or $status_ticket==7) 
            {
                // checking apakah sudah expired ticketnya gate in expired karena eksekutif gatein sekaligus boarding
                if($check_expire->boarding_expired<date("Y-m-d H:i:s"))
                {
                    echo $res=json_api(0, 'Tiket sudah expired');
                }
                else
                {
                    // cheking expire tanggal 
                    $this->db->trans_begin();

                    $this->extend->insert_data($this->_table,$data);

                    $this->extend->update_data("app.t_trx_booking_passanger",$update_data, "ticket_number='{$ticket_number}' ");

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
            else if($status_ticket==3)
            {
                // checking apakah sudah expired ticketnya gate in expired karena eksekutif gatein sekaligus boarding
                if($check_expire->gatein_expired<date("Y-m-d H:i:s"))
                {
                    echo $res=json_api(0, 'Tiket sudah expired');
                }
                else
                {
                    // cheking expire tanggal 
                    $this->db->trans_begin();

                    $this->extend->insert_data($this->_table,$data);

                    $this->extend->update_data("app.t_trx_booking_passanger",$update_data, "ticket_number='{$ticket_number}' ");

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
            else
            {
                echo $res=json_api(0, 'Invalid Tiket'); 
            }            

        }  

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'transaction/extend_ticket/action_add_passanger';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);                  

    }

    public function action_add_vehicle($booking_code, $ship_class, $status_ticket, $extra_time)
    {

        // echo  $booking_code; exit;

        $check_expire=$this->extend->select_data("app.t_trx_booking_vehicle", " where booking_code='{$booking_code}' " )->row();

        $get_data_passanger=$this->extend->select_data("app.t_trx_booking_passanger", " where booking_code='".$booking_code."' ");
        $data_passanger=array();

        // print_r($get_data_passanger->result()); exit;


        switch ($status_ticket) {
            case 3:

                $new_gatein_expired=date('Y-m-d H:i:s',strtotime("+".$extra_time."hour",strtotime($check_expire->gatein_expired)));
                $data=array(
                    'ticket_number'=>$$check_expire->ticket_number,
                    'service_id'=>$check_expire->service_id,
                    'ticket_status'=>$status_ticket,
                    'total_time'=>$extra_time,
                    'old_gatein_expired'=>$check_expire->gatein_expired,
                    'new_gatein_expired'=>$new_gatein_expired,
                    'status'=>1,
                    'created_by'=>$this->session->userdata("username"),
                    'created_on'=>date('Y-m-d H:i:s'),
                );

                foreach ($get_data_passanger->result() as $key => $value) {
                        $data_p=array(
                        'ticket_number'=>$value->ticket_number,
                        'service_id'=>$check_expire->service_id,
                        'ticket_status'=>$status_ticket,
                        'total_time'=>$extra_time,
                        'old_gatein_expired'=>$check_expire->gatein_expired,
                        'new_gatein_expired'=>$new_gatein_expired,
                        'status'=>1,
                        'created_by'=>$this->session->userdata("username"),
                        'created_on'=>date('Y-m-d H:i:s'),
                    );

                    $data_passanger[]=$data_p;
                }

                $update_data=array(
                    'updated_on'=>date('Y-m-d H:i:s'),
                    'updated_by'=>$this->session->userdata("username"),
                    'gatein_expired'=>$new_gatein_expired,
                );

            break;
            default:
                $new_boarding_expired=date('Y-m-d H:i:s',strtotime("+".$extra_time."hour",strtotime($check_expire->boarding_expired)));
                $data=array(
                    'ticket_number'=>$check_expire->ticket_number,
                    'service_id'=>$check_expire->service_id,
                    'ticket_status'=>$status_ticket,
                    'total_time'=>$extra_time,
                    'old_boarding_expired'=>$check_expire->boarding_expired,
                    'new_boarding_expired'=>$new_boarding_expired,
                    'status'=>1,
                    'created_by'=>$this->session->userdata("username"),
                    'created_on'=>date('Y-m-d H:i:s'),
                );


                foreach ($get_data_passanger->result() as $key => $value) {
                        $data_p=array(
                        'ticket_number'=>$value->ticket_number,
                        'service_id'=>$check_expire->service_id,
                        'ticket_status'=>$status_ticket,
                        'total_time'=>$extra_time,
                        'old_boarding_expired'=>$check_expire->boarding_expired,
                        'new_boarding_expired'=>$new_boarding_expired,
                        'status'=>1,
                        'created_by'=>$this->session->userdata("username"),
                        'created_on'=>date('Y-m-d H:i:s'),
                    );

                    $data_passanger[]=$data_p;
                }

                $update_data=array(
                    'updated_on'=>date('Y-m-d H:i:s'),
                    'updated_by'=>$this->session->userdata("username"),
                    'boarding_expired'=>$new_boarding_expired,
                );

            break;
        }


        
        // check ship class reguler atau eksekutif 1 reguler 2 eksekutif
        if($ship_class==1)
        {   

            // ang bisa perpoanjang hanya ticket yang statusnya 4 (sudah gate in ) dan 7 ( muntah kapal)
            if($status_ticket==4 or $status_ticket==7)
            {
                // checking apakah sudah expired ticketnya boarding expired
                if($check_expire->boarding_expired<date("Y-m-d H:i:s"))
                {
                    echo $res=json_api(0, 'Tiket sudah expired');   
                }
                else
                {                
                    // cheking expire tanggal 
                    $this->db->trans_begin();

                    // insert ke aapp.t_trx_extend_time_vehicle
                    $this->extend->insert_data($this->_table2,$data);

                    //insert ke aapp.t_trx_extend_time_passanger 
                    $this->extend->insert_batch_data($this->_table,$data_passanger);

                    // update ke a\app.t_trx_booking_passanger
                    $this->extend->update_data("app.t_trx_booking_passanger",$update_data, "booking_code='{$booking_code}' ");

                    // update ke a\app.t_trx_booking_vehicle
                    $this->extend->update_data("app.t_trx_booking_vehicle",$update_data, "booking_code='{$booking_code}' ");

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
            else
            {
                echo $res=json_api(0, 'Invalid Tiket');    
            }

        }
        else
        {
            // ang bisa perpoanjang hanya ticket yang statusnya 3(sudah check in) 4 (sudah gate in ) dan 7 ( muntah kapal) 
            if($status_ticket==4 or $status_ticket==7)
            {

                // checking apakah sudah expired ticketnya gate in expired karena eksekutif gatein sekaligus boarding
                // checking apakah sudah expired ticketnya boarding expired
                if($check_expire->boarding_expired<date("Y-m-d H:i:s"))
                {
                    echo $res=json_api(0, 'Tiket sudah expired');   
                }
                else
                {    
                    // cheking expire tanggal 
                    $this->db->trans_begin();


                    // insert ke aapp.t_trx_extend_time_vehicle
                    $this->extend->insert_data($this->_table2,$data);

                    //insert ke aapp.t_trx_extend_time_passanger 
                    $this->extend->insert_batch_data($this->_table,$data_passanger);

                    // update ke a\app.t_trx_booking_passanger
                    $this->extend->update_data("app.t_trx_booking_passanger",$update_data, "booking_code='{$booking_code}' ");

                    // update ke a\app.t_trx_booking_vehicle
                    $this->extend->update_data("app.t_trx_booking_vehicle",$update_data, "booking_code='{$booking_code}' ");

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
            else
            {
                echo $res=json_api(0, 'Invalid Tiket');   
            }            

        }
        
    }    


    public function search_data()
    {
        $ticket_number=trim($this->input->post('ticket_number'));
        $check_ticket=$this->extend->check_ticket($ticket_number)->row();

        if(!empty($check_ticket))
        {

            if($check_ticket->service_id==1)
            {
                $data=$this->search_passanger($check_ticket->ticket_number, $check_ticket->ship_class, $check_ticket->status);
            }
            else
            {
                $data=$this->search_vehicle($check_ticket->booking_code, $check_ticket->ship_class, $check_ticket->status);
            }
        }
        else
        {

            $data=array("code"=>0,"message"=>"Ticket tidak di temukan");  
        }


        echo json_encode($data);

    }

    public function search_passanger($ticket_number, $ship_class, $status_ticket)
    {

        $check_expire=$this->extend->select_data("app.t_trx_booking_passanger", " where ticket_number='{$ticket_number}' " )->row();
        
        // check ship class reguler atau eksekutif 1 reguler 2 eksekutif
        if($ship_class==1)
        {   

            // yang bisa perpanjang hanya ticket yang statusnya 4 (sudah gate in ) dan 7 ( muntah kapal)
            if($status_ticket==4 or $status_ticket==7)
            {
                // checking apakah sudah expired ticketnya boarding expired
                if($check_expire->boarding_expired<date("Y-m-d H:i:s"))
                {
                    return array("code"=>0,"message"=>"Ticket Sudah Expire");
                }
                else
                {

                    return array("code"=>1,
                                "data"=>$this->extend->get_data_passanger($ticket_number)->row(),
                                "service"=>'penumpang',
                                "status"=>'Boarding Expired',
                                "expiredDate"=>$check_expire->boarding_expired
                                );   
                }   
            }
            else
            {
                return array("code"=>0,"message"=>"Invalid Ticket");   
            }

        }
        else
        {

            // yang bisa perpoanjang hanya ticket yang statusnya 3(sudah check in) 4 (sudah gate in ) dan 7 ( muntah kapal) 
            if( $status_ticket==4 or $status_ticket==7)
            {
                // checking apakah sudah expired ticketnya gate in expired karena eksekutif gatein sekaligus boarding
                if($check_expire->boarding_expired<date("Y-m-d H:i:s"))
                {
                    return array("code"=>0,"message"=>"Ticket Sudah Expire");
                }
                else
                {

                    // cheking expire tanggal 
                    return array("code"=>1,
                                "data"=>$this->extend->get_data_passanger($ticket_number)->row(),
                                "service"=>'penumpang',
                                "status"=>'Boarding Expired',
                                "expiredDate"=>$check_expire->boarding_expired
                            );   
                }
            }
            else if($status_ticket==3 )
            {

                // checking apakah sudah expired ticketnya gate in expired karena eksekutif gatein sekaligus boarding
                if($check_expire->gatein_expired<date("Y-m-d H:i:s"))
                {
                    return array("code"=>0,"message"=>"Ticket Sudah Expire");
                }
                else
                {

                    // cheking expire tanggal 
                    return array("code"=>1,
                                "data"=>$this->extend->get_data_passanger($ticket_number)->row(),
                                "service"=>'penumpang',
                                "status"=>'Boarding Expired',
                                "expiredDate"=>$check_expire->gatein_expired
                            );   
                }
            }
            else
            {
                return array("code"=>0,"message"=>"Invalid Ticket");   
            }            

        }
        
    }

    public function search_vehicle($booking_code, $ship_class, $status_ticket)
    {

        $check_expire=$this->extend->select_data("app.t_trx_booking_vehicle", " where booking_code='{$booking_code}' " )->row();
        
        // check ship class reguler atau eksekutif 1 reguler 2 eksekutif
        if($ship_class==1)
        {   

            // ang bisa perpoanjang hanya ticket yang statusnya 4 (sudah gate in ) dan 7 ( muntah kapal)
            if($status_ticket==4 or $status_ticket==7)
            {
                // checking apakah sudah expired ticketnya boarding expired
                if($check_expire->boarding_expired<date("Y-m-d H:i:s"))
                {
                    return array("code"=>0,"message"=>"Ticket Sudah Expire");
                }
                else
                {

                    return array("code"=>1,
                                "data"=>$this->extend->get_data_vehicle($booking_code)->row(),
                                "service"=>'kendaraan',
                                "status"=>'Boarding Expired',
                                "expiredDate"=>$check_expire->boarding_expired
                            );   
                }
            }
            else
            {
                return array("code"=>0,"message"=>"Invalid Ticket");   
            }

        }
        else
        {
            // ang bisa perpoanjang hanya ticket yang statusnya 4 (sudah gate in ) dan 7 ( muntah kapal)
            if($status_ticket==4 or $status_ticket==7)
            {            
            // checking apakah sudah expired ticketnya boarding expired
                if($check_expire->boarding_expired<date("Y-m-d H:i:s"))
                {
                    return array("code"=>0,"message"=>"Ticket Sudah Expire");
                }
                else
                {

                    return array("code"=>1,
                                "data"=>$this->extend->get_data_vehicle($booking_code)->row(),
                                "service"=>'kendaraan',
                                "status"=>'Boarding Expired',
                                "expiredDate"=>$check_expire->boarding_expired
                            );   
                }
            }
            else if($status_ticket==3 )
            {

                // checking apakah sudah expired ticketnya gate in expired karena eksekutif gatein sekaligus boarding
                if($check_expire->gatein_expired<date("Y-m-d H:i:s"))
                {
                    return array("code"=>0,"message"=>"Ticket Sudah Expire");
                }
                else
                {

                    // cheking expire tanggal 
                    return array("code"=>1,
                                "data"=>$this->extend->get_data_passanger($ticket_number)->row(),
                                "service"=>'kendaraan',
                                "status"=>'Boarding Expired',
                                "expiredDate"=>$check_expire->gatein_expired
                            );   
                }
            }
            else
            {
                return array("code"=>0,"message"=>"Invalid Ticket");   
            }            

        }
        
    }    



}
