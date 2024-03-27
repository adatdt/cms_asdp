<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Add_manifest extends MY_Controller{
    public function __construct(){
        parent::__construct();

        logged_in();
        $this->load->model('m_addmanifest','manifest');
        $this->load->model('global_model');
        $this->load->library('Html2pdf');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_open_boarding';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'manifest/add_manifest';
    }

    public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->manifest->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Manifest Tambahan',
            'content'  => 'add_manifest/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            // 'service'  => $this->manifest->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'port'=>$this->manifest->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result(),
            'dock'=>$this->manifest->select_data("app.t_mtr_dock","where status not in (-5) order by name asc")->result(),
            'service'=>$this->manifest->select_data("app.t_mtr_service","where status not in (-5) order by name asc")->result(),
            'team'=>$this->manifest->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

        $this->load->view('default', $data);
    }


    public function add($boarding_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $code=$this->enc->decode($boarding_code);
        $ship_class=$this->manifest->select_data("app.t_trx_open_boarding"," where boarding_code='".$code."'")->row();

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title'] = 'Tambahan Manifest';
        $data['boarding_code'] = $code;
        $data['data_header']=$this->manifest->get_header($code)->row();
        $data['ship_class'] = $this->enc->encode($ship_class->ship_class);
        $data['service']=$this->manifest->select_data("app.t_mtr_service","where status not in (-5) order by name asc")->result();

        $this->load->view($this->_module.'/add',$data); 

        // $this->load->view('default',$data);   
    }


    public function action_add()
    {

        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $boarding_code=trim($this->input->post('boarding_code'));
        $ticket_number=trim($this->input->post('ticket_number'));
        $service_id=$this->enc->decode($this->input->post('service'));
        $ship_class_boarding=$this->enc->decode($this->input->post('ship_class_boarding'));


        $this->form_validation->set_rules('ticket_number', 'Nomer Tiket', 'required');
        $this->form_validation->set_rules('service', 'Service', 'required');
        // $this->form_validation->set_rules('boarding_code', 'Kode Boarding', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
            exit;
        }
        
        if(empty($boarding_code))
        {
            echo $res=json_api(0,"Data Kosong");
            exit;
        }


        // ceck apakah statusnya sudah tutup layanan 
        $ceck_close_boarding=$this->manifest->get_status_boarding($boarding_code)->row();

        if ($ceck_close_boarding->close_boarding_date=="")
        {
            echo $res=json_api(0,"Kapal sedang buka layanan");
            exit;
        }

        //menentukan  ticket reguler / eks
        if($service_id==1)
        {
            $cari=$this->manifest->select_data("app.t_trx_booking_passanger"," where ticket_number='".$ticket_number."'")->row();
            $ship_class=$cari->ship_class;
        }
        else
        {
            $cari=$this->manifest->select_data("app.t_trx_booking_vehicle"," where ticket_number='".$ticket_number."'")->row();
            $ship_class=$cari->ship_class;
        }


        // checking validasi apakah reguler atau eks
        if($ship_class==1)
        {
            // mencegah jika ada tiket yang masuk ke kapal eksekutif
            if($ship_class_boarding!=$ship_class)
            {
                echo $res=json_api(0,"Invalid Ticket");
                exit;
            }

            echo $this->action_add_reguler($ticket_number,$boarding_code,$service_id,$ship_class);
        }
        else
        {
            // mencegah jika ada tiket yang masuk ke kapal eksekutif
            if($ship_class_boarding!=$ship_class)
            {
                echo $res=json_api(0,"Invalid Ticket");
                exit;
            }
            echo $this->action_add_eks($ticket_number,$boarding_code,$service_id,$ship_class);   
        }

    }

    public function decrypt_aes($ticket_number="")
    {
        // hard cord 
        $aes_key=$this->manifest->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_key') ")->row();
        $aes_iv=$this->manifest->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_iv') ")->row();


        // print_r($aes_key); exit;

        $key=$aes_key->param_value;
        $iv=$aes_iv->param_value;

        $decrypt_ticket_number = strtoupper(PHP_AES_Cipher::decrypt2($key,$iv,$ticket_number));
        empty($decrypt_ticket_number)?$data=$ticket_number:$data=$decrypt_ticket_number;

        return $data;
    }    

    // public function decrypt_aes2($ticket_number)
    // {
    //     // hard cord 
    //     // $aes_key=$this->manifest->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_key') ")->row();
    //     // $aes_iv=$this->manifest->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_iv') ")->row();


    //     $aes_key='D3ED68F7315B7E72';
    //     $aes_iv='DAAD68F7315B7E73';


    //     $decrypt_ticket_number = strtoupper(PHP_AES_Cipher::decrypt2($aes_key,$aes_iv,$ticket_number));
    //     empty($decrypt_ticket_number)?$data=$ticket_number:$data=$decrypt_ticket_number;

    //     return $data;
    // }



    public function action_add_reguler($ticket_number,$boarding_code,$service_id,$service_id_ticket)
    {

        // validate_ajax();
        // $this->global_model->checkAccessMenuAction($this->_module,'add');


        $check_tiket_passanger=$this->manifest->select_data("app.t_trx_boarding_passanger"," where ticket_number='".$ticket_number."' and status=1");

        $check_tiket_vehicle=$this->manifest->select_data("app.t_trx_boarding_vehicle"," where ticket_number='".$ticket_number."' and status=1");

        if(empty($boarding_code))
        {
            return $res=json_api(0,"Data Kosong");   
        }
        else if($check_tiket_passanger->num_rows()>0)
        {
            return $res=json_api(0,"Manifest sudah ada");
        }
        else if($check_tiket_vehicle->num_rows()>0)
        {
            return $res=json_api(0,"Manifest sudah ada");
        }
        else
        {
            $this->db->trans_begin();
            $data_boarding_pass=$this->manifest->get_pass_array($boarding_code)->row();
            $data_boarding_vehicle=$this->manifest->get_pass_array($boarding_code)->row();
            

            if($service_id==1)
            {
                // check apakah dia dalam status 4,7 yang reguler
                $check_booking_status=$this->manifest->get_passanger($ticket_number)->row();

                $data_boarding_pass->status=1;
                $data_boarding_pass->ticket_number=$ticket_number;
                $data_boarding_pass->ship_class=$service_id_ticket;
                $data_boarding_pass->created_on=date("Y-m-d H:i:s");
                $data_boarding_pass->boarding_date=date("Y-m-d H:i:s");
                $data_boarding_pass->created_by=$this->session->userdata("username");
                $data_boarding_pass->service_id=1;

                $data_pass=$data_boarding_pass;

                $updt_booking_pass['status']=5;
                $updt_booking_pass['updated_on']=date('Y-m-d H:i:s');
                $updt_booking_pass['updated_by']=$this->session->userdata("username");

                if($check_booking_status->status==3)
                {
                    return $res=json_api(0, 'Silahkan gate in terdahulu');
                    exit;
                }
                else if($check_booking_status->status==5)
                {
                    return $res=json_api(0, 'Tiket sudah boarding');
                    exit;
                }
                else if ($check_booking_status->status==4 or $check_booking_status->status==7 or $check_booking_status->status==12 )
                {

                    $this->manifest->insert_data("app.t_trx_boarding_passanger",$data_pass);

                    // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
                    $this->manifest->update_data("app.t_trx_booking_passanger",$updt_booking_pass,"ticket_number='".$ticket_number."'");
                }
                else
                {
                    return $res=json_api(0, 'Invalid ticket');
                    exit;
                }
            }

            else if($service_id==2)
            {

                $get_booking=$this->manifest->get_vehicle($ticket_number)->row();

                //mencari passanger yang setatus -5 
                $data_boarding_pass_vehicle=$this->manifest->select_data("app.t_trx_booking_passanger",
                    " where booking_code='".$get_booking->booking_code."' and status !='-5' ")->result();

                // check apakah dia dalam status 4,7 yang reguler
                $check_booking_status=$this->manifest->get_vehicle($ticket_number)->row();

                $updt_booking_pass['status']=5;
                $updt_booking_pass['updated_on']=date('Y-m-d H:i:s');
                $updt_booking_pass['updated_by']=$this->session->userdata("username");


                //data boarding kendaraan
                $data_boarding_vehicle->status=1;
                $data_boarding_vehicle->ticket_number=$ticket_number;
                $data_boarding_pass->ship_class=$service_id_ticket;
                $data_boarding_vehicle->created_on=date("Y-m-d H:i:s");
                $data_boarding_vehicle->boarding_date=date("Y-m-d H:i:s");
                $data_boarding_vehicle->created_by=$this->session->userdata("username");

                $data_vehicle=$data_boarding_vehicle;

                if($check_booking_status->status==3)
                {
                    return $res=json_api(0, 'Silahkan gate in terdahulu');
                    exit;
                }
                else if($check_booking_status->status==5)
                {
                    return $res=json_api(0, 'Tiket sudah boarding');
                    exit;
                }
                else if ($check_booking_status->status==4 or $check_booking_status->status==7 or $check_booking_status->status==12 )
                {

                    foreach ( $data_boarding_pass_vehicle as $key => $value) {

                        $data_boarding_pass->status=1;
                        $data_boarding_pass->ship_class=$service_id_ticket;
                        $data_boarding_pass->ticket_number=$value->ticket_number;
                        $data_boarding_pass->created_on=date("Y-m-d H:i:s");
                        $data_boarding_pass->boarding_date=date("Y-m-d H:i:s");
                        $data_boarding_pass->created_by=$this->session->userdata("username");
                        $data_boarding_pass->service_id=2;

                        $data_pass=$data_boarding_pass;

                        $this->manifest->insert_data("app.t_trx_boarding_passanger",$data_pass);

                        // update ke booking penumpang menjadi 5 sudah boarding
                        $this->manifest->update_data("app.t_trx_booking_passanger",$updt_booking_pass,"ticket_number='".$value->ticket_number."'");

                    }

                    $this->manifest->insert_data("app.t_trx_boarding_vehicle",$data_vehicle);

                    // update ke booking kendaraan menjadi 5 sudah boarding
                    $this->manifest->update_data("app.t_trx_booking_vehicle",$updt_booking_pass,"ticket_number='".$ticket_number."'");
                }

                else
                {
                    return $res=json_api(0, 'Invalid ticket');
                    exit;

                }
            }
            else
            {
                return $res=json_api(0,"Gagal tambah data");
            }

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();
                return $res=json_api(1, 'Berhasil tambah data');
            }
        }

    }

    public function action_add_eks($ticket_number,$boarding_code,$service_id,$service_id_ticket)
    {

        // validate_ajax();
        // $this->global_model->checkAccessMenuAction($this->_module,'add');


        $check_tiket_passanger=$this->manifest->select_data("app.t_trx_boarding_passanger"," where ticket_number='".$ticket_number."' and status=1");

        $check_tiket_vehicle=$this->manifest->select_data("app.t_trx_boarding_vehicle"," where ticket_number='".$ticket_number."' and status=1");

        if(empty($boarding_code))
        {
            return $res=json_api(0,"Data Kosong");   
        }
        else if($check_tiket_passanger->num_rows()>0)
        {
            return $res=json_api(0,"Manifest sudah ada");
        }
        else if($check_tiket_vehicle->num_rows()>0)
        {
            return $res=json_api(0,"Manifest sudah ada");
        }
        else
        {
            $this->db->trans_begin();
            $data_boarding_pass=$this->manifest->get_pass_array($boarding_code)->row();
            $data_boarding_vehicle=$this->manifest->get_pass_array($boarding_code)->row();
            

            if($service_id==1)
            {
                // check apakah dia dalam status 4,7 yang reguler
                $check_booking_status=$this->manifest->get_passanger($ticket_number)->row();

                $data_boarding_pass->status=1;
                $data_boarding_pass->ticket_number=$ticket_number;
                $data_boarding_pass->ship_class=$service_id_ticket;
                $data_boarding_pass->created_on=date("Y-m-d H:i:s");
                $data_boarding_pass->boarding_date=date("Y-m-d H:i:s");
                $data_boarding_pass->created_by=$this->session->userdata("username");
                $data_boarding_pass->service_id=1;

                $data_pass=$data_boarding_pass;

                $updt_booking_pass['status']=5;
                $updt_booking_pass['updated_on']=date('Y-m-d H:i:s');
                $updt_booking_pass['updated_by']=$this->session->userdata("username");

                // if($check_booking_status->status==3)
                // {
                //     return $res=json_api(0, 'Silahkan gate in terdahulu');
                //     exit;
                // }
                if($check_booking_status->status==5)
                {
                    return $res=json_api(0, 'Tiket sudah boarding');
                    exit;
                }
                else if ($check_booking_status->status==4 or $check_booking_status->status==7 or $check_booking_status->status==3 or $check_booking_status->status==12)
                {

                    $this->manifest->insert_data("app.t_trx_boarding_passanger",$data_pass);

                    // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
                    $this->manifest->update_data("app.t_trx_booking_passanger",$updt_booking_pass,"ticket_number='".$ticket_number."'");
                }
                else
                {
                    return $res=json_api(0, 'Invalid ticket');
                    exit;
                }
            }

            else if($service_id==2)
            {

                $get_booking=$this->manifest->get_vehicle($ticket_number)->row();

                $data_boarding_pass_vehicle=$this->manifest->select_data("app.t_trx_booking_passanger",
                    " where booking_code='".$get_booking->booking_code."' and status !='-5' ")->result();

                // check apakah dia dalam status 4,7 yang reguler
                $check_booking_status=$this->manifest->get_vehicle($ticket_number)->row();

                $updt_booking_pass['status']=5;
                $updt_booking_pass['updated_on']=date('Y-m-d H:i:s');
                $updt_booking_pass['updated_by']=$this->session->userdata("username");


                //data boarding kendaraan
                $data_boarding_vehicle->status=1;
                $data_boarding_vehicle->ticket_number=$ticket_number;
                $data_boarding_vehicle->ship_class=$service_id_ticket;
                $data_boarding_vehicle->boarding_date=date("Y-m-d H:i:s");
                $data_boarding_vehicle->created_on=date("Y-m-d H:i:s");
                $data_boarding_vehicle->created_by=$this->session->userdata("username");

                $data_vehicle=$data_boarding_vehicle;

                // if($check_booking_status->status==3)
                // {
                //     return $res=json_api(0, 'Silahkan gate in terdahulu');
                //     exit;
                // }
                if($check_booking_status->status==5)
                {
                    return $res=json_api(0, 'Tiket sudah boarding');
                    exit;
                }
                else if ($check_booking_status->status==4 or $check_booking_status->status==7 or $check_booking_status->status==3 or $check_booking_status->status==12 )
                {

                    foreach ( $data_boarding_pass_vehicle as $key => $value) {

                        $data_boarding_pass->status=1;
                        $data_boarding_pass->ticket_number=$value->ticket_number;
                        $data_boarding_pass->ship_class=$service_id_ticket;
                        $data_boarding_pass->created_on=date("Y-m-d H:i:s");
                        $data_boarding_pass->boarding_date=date("Y-m-d H:i:s");
                        $data_boarding_pass->created_by=$this->session->userdata("username");
                        $data_boarding_pass->service_id=2;

                        $data_pass=$data_boarding_pass;

                        $this->manifest->insert_data("app.t_trx_boarding_passanger",$data_pass);

                        // update ke booking penumpang menjadi 5 sudah boarding
                        $this->manifest->update_data("app.t_trx_booking_passanger",$updt_booking_pass,"ticket_number='".$value->ticket_number."'");

                    }

                    $this->manifest->insert_data("app.t_trx_boarding_vehicle",$data_vehicle);

                    // update ke booking kendaraan menjadi 5 sudah boarding
                    $this->manifest->update_data("app.t_trx_booking_vehicle",$updt_booking_pass,"ticket_number='".$ticket_number."'");
                }

                else
                {
                    return $res=json_api(0, 'Invalid ticket');
                    exit;

                }
            }
            else
            {
                return $res=json_api(0,"Gagal tambah data");
            }



            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();
                return $res=json_api(1, 'Berhasil tambah data');
            }
        }

    }

    // public function action_add()
    // {

    //     validate_ajax();
    //     $this->global_model->checkAccessMenuAction($this->_module,'add');

    //     $boarding_code=trim($this->input->post('boarding_code'));
    //     $ticket_number=trim($this->input->post('ticket_number'));
    //     $service_id=$this->enc->decode($this->input->post('service'));


    //     $this->form_validation->set_rules('ticket_number', 'Nomer Tiket', 'required');
    //     $this->form_validation->set_rules('service', 'Service', 'required');
    //     $this->form_validation->set_message('required','%s harus diisi!');


    //     $check_tiket_passanger=$this->manifest->select_data("app.t_trx_boarding_passanger"," where ticket_number='".$ticket_number."'");

    //     $check_tiket_vehicle=$this->manifest->select_data("app.t_trx_boarding_vehicle"," where ticket_number='".$ticket_number."'");


    //     if($this->form_validation->run()===false)
    //     {
    //         echo $res=json_api(0,validation_errors());
    //     }
    //     else if(empty($boarding_code))
    //     {
    //         echo $res=json_api(0,"Data Kosong");   
    //     }
    //     else if($check_tiket_passanger->num_rows()>0)
    //     {
    //         echo $res=json_api(0,"Manifest sudah ada");
    //     }
    //     else if($check_tiket_vehicle->num_rows()>0)
    //     {
    //         echo $res=json_api(0,"Manifest sudah ada");
    //     }
    //     else
    //     {
    //         $this->db->trans_begin();
    //         $data_boarding_pass=$this->manifest->get_pass_array($boarding_code)->row();
    //         $data_boarding_vehicle=$this->manifest->get_pass_array($boarding_code)->row();
            

    //         if($service_id==1)
    //         {
    //             // check apakah dia dalam status 4,7 yang reguler
    //             $check_booking_status=$this->manifest->get_passanger($ticket_number)->row();

    //             $data_boarding_pass->status=1;
    //             $data_boarding_pass->ticket_number=$ticket_number;
    //             $data_boarding_pass->created_on=date("Y-m-d H:i:s");
    //             $data_boarding_pass->created_by=$this->session->userdata("username");
    //             $data_boarding_pass->service_id=1;

    //             $data_pass=$data_boarding_pass;

    //             $updt_booking_pass['status']=5;
    //             $updt_booking_pass['updated_on']=date('Y-m-d H:i:s');
    //             $updt_booking_pass['updated_by']=$this->session->userdata("username");

    //             if($check_booking_status->status==3)
    //             {
    //                 echo $res=json_api(0, 'Silahkan gate in terdahulu');
    //                 exit;
    //             }
    //             else if($check_booking_status->status==5)
    //             {
    //                 echo $res=json_api(0, 'Tiket sudah boarding');
    //                 exit;
    //             }
    //             else if ($check_booking_status->status==4 or $check_booking_status->status==7 )
    //             {

    //                 $this->manifest->insert_data("app.t_trx_boarding_passanger_catch_up",$data_pass);

    //                 // update ke booking penumpang ubah satatus menjadi 5 (sudah boarding)
    //                 $this->manifest->update_data("app.t_trx_booking_passanger",$updt_booking_pass,"ticket_number='".$ticket_number."'");
    //             }
    //             else
    //             {
    //                 echo $res=json_api(0, 'Invalid ticket');
    //                 exit;
    //             }
    //         }

    //         else if($service_id==2)
    //         {

    //             $get_booking=$this->manifest->get_vehicle($ticket_number)->row();

    //             $data_boarding_pass_vehicle=$this->manifest->select_data("app.t_trx_booking_passanger",
    //                 " where booking_code='".$get_booking->booking_code."' ")->result();

    //             // check apakah dia dalam status 4,7 yang reguler
    //             $check_booking_status=$this->manifest->get_vehicle($ticket_number)->row();

    //             $updt_booking_pass['status']=5;
    //             $updt_booking_pass['updated_on']=date('Y-m-d H:i:s');
    //             $updt_booking_pass['updated_by']=$this->session->userdata("username");


    //             //data boarding kendaraan
    //             $data_boarding_vehicle->status=1;
    //             $data_boarding_vehicle->ticket_number=$ticket_number;
    //             $data_boarding_vehicle->created_on=date("Y-m-d H:i:s");
    //             $data_boarding_vehicle->created_by=$this->session->userdata("username");

    //             $data_vehicle=$data_boarding_vehicle;

    //             if($check_booking_status->status==3)
    //             {
    //                 echo $res=json_api(0, 'Silahkan gate in terdahulu');
    //                 exit;
    //             }
    //             else if($check_booking_status->status==5)
    //             {
    //                 echo $res=json_api(0, 'Tiket sudah boarding');
    //                 exit;
    //             }
    //             else if ($check_booking_status->status==4 or $check_booking_status->status==7 )
    //             {

    //                 foreach ( $data_boarding_pass_vehicle as $key => $value) {

    //                     $data_boarding_pass->status=1;
    //                     $data_boarding_pass->ticket_number=$value->ticket_number;
    //                     $data_boarding_pass->created_on=date("Y-m-d H:i:s");
    //                     $data_boarding_pass->created_by=$this->session->userdata("username");
    //                     $data_boarding_pass->service_id=2;

    //                     $data_pass=$data_boarding_pass;

    //                     $this->manifest->insert_data("app.t_trx_boarding_passanger_catch_up",$data_pass);

    //                     // update ke booking penumpang menjadi 5 sudah boarding
    //                     $this->manifest->update_data("app.t_trx_booking_passanger",$updt_booking_pass,"ticket_number='".$value->ticket_number."'");

    //                 }

    //                 $this->manifest->insert_data("app.t_trx_boarding_vehicle_catch_up",$data_vehicle);

    //                 // update ke booking kendaraan menjadi 5 sudah boarding
    //                 $this->manifest->update_data("app.t_trx_booking_vehicle",$updt_booking_pass,"ticket_number='".$ticket_number."'");
    //             }

    //             else
    //             {
    //                 echo $res=json_api(0, 'Invalid ticket');
    //                 exit;

    //             }
    //         }
    //         else
    //         {
    //             echo $res=json_api(0,"Gagal tambah data");
    //         }

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

    // }




    function get_data()
    {

        $service=$this->enc->decode($this->input->post('service'));
        

        if(empty($this->input->post('ticket_number')))
        {
            $ticket_number="";
        }
        else
        {
            $ticket_number=$this->decrypt_aes(trim($this->input->post('ticket_number')));
        }

        // echo $ticket_number; exit;

        // mencegah terjadinya jika diinput kosong
        if(empty($service) or empty($ticket_number)   )
        {
            $data['tipe_penumpang']='kosong';
            echo json_encode($data);
            exit;            
        }

        
        // service penumpang
        if ($service==1)
        {
            $get_passanger=$this->manifest->get_passanger($ticket_number,$service)->row();

            if(empty($get_passanger))
            {
                $data['tipe_penumpang']='kosong';
                echo json_encode($data);
            exit;            
            }

            $get_passanger->tipe_penumpang='penumpang';
            $get_passanger->ticket=$ticket_number;
            $get_passanger->id=$this->enc->encode($get_passanger->id);
            $data=$get_passanger;

            echo json_encode($data);
        }
        // service kendaraan
        else if ($service==2)
        {
            $get_vehicle=$this->manifest->get_vehicle($ticket_number)->row();

            if(empty($get_vehicle))
            {
                $data['tipe_penumpang']='kosong';
                echo json_encode($data);
            exit;            
            }

            $get_vehicle->tipe_penumpang='kendaraan';
            $get_vehicle->ticket=$ticket_number;
            $get_vehicle->id=$this->enc->encode($get_vehicle->id);
            $data=$get_vehicle;

            echo json_encode($data);
        }

    }

}
