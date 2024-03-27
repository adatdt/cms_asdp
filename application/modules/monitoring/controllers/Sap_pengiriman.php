<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Sap_pengiriman extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_sap_pengiriman','pengiriman');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_log_sync_sap';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'monitoring/sap_pengiriman';
	}

    /*
    Document   : Monitoring
    Created on : 21 juli, 2023
    Author     : soma
    Description: Enhancement cms penerapan validasi dan token csrf 
    */

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('port')){
                $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
            }
            if($this->input->post('ship_class')){
                $this->form_validation->set_rules('ship_class', 'Kelas layanan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid kelas layanan'));
            }
            if($this->input->post('type')){
                $this->form_validation->set_rules('type', 'Tipe laporan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid tipe laporan'));
            }
            if($this->input->post('dateFrom')){
                $this->form_validation->set_rules('dateFrom', 'Tanggal awal', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal awal'));
            }
            if($this->input->post('dateTo')){
                $this->form_validation->set_rules('dateTo', 'Tanggal akhir', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal akhir'));
            }
            
            
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->pengiriman->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->pengiriman->get_identity_app();
        
        if($get_identity==0)
        {
            // ambil port berdasarkan user
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->pengiriman->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id'))->result();
                $row_port=1;
            }
            else
            {
                $port=$this->pengiriman->select_data("app.t_mtr_port","where status !='-5' order by name asc")->result();
                $row_port=0;
            }
        }
        else
        {
            $port=$this->pengiriman->select_data("app.t_mtr_port","where id=".$get_identity)->result();
            $row_port=1;
        }        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Log Pengiriman SAP',
            'content'  => 'sap_pengiriman/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'ship_class'  => $this->pengiriman->select_data("app.t_mtr_ship_class","where status=1 order by name asc")->result(),
            'port'=>$port,
            'row_port'=>$row_port,
            'gs'=>$this->check_gs()==12?"false":"true",
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),
            'team'=>$this->pengiriman->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
    }
    
    public function resend($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);

        $data['title'] = 'Resend SAP';
        $data['id'] = $id;
        $data['detail']=$this->pengiriman->select_data($this->_table,"where id=$id_decode")->row();

        $this->load->view($this->_module.'/resend',$data);   
    }

    public function action_resend(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $shift_date=trim($this->input->post('shift_date'));
        $port_id=trim($this->input->post('port_id'));
        $shift_id=trim($this->input->post('shift_id'));
        $ship_class=trim($this->input->post('ship_class'));
        $type=trim($this->input->post('type'));
        $id=trim($this->input->post('id'));
        
        $terjual = $this->pengiriman->select_data("app.t_mtr_custom_param", "where param_name='url_api_sap_terjual'")->row();
        $tertagih = $this->pengiriman->select_data("app.t_mtr_custom_param", "where param_name='url_api_sap_tertagih'")->row();



        $data = array(
            'id'=> $id,
            'shift_date' => $shift_date,
            'port_id' => $port_id,
            'shift_id' => $shift_id,
            'ship_class' => $ship_class,
            'type' => $type,

        );
        // // echo json_encode($data);
        // $portSocket = $this->config->item('port_socket_server');
        // $urlSocket  = $this->config->item('url_socket_server').":".$this->config->item('url_socket_port')."/pids";

        /*
        ihab
        $terjualurl = $terjual->param_value."/{$port_id}/{$ship_class}/{$shift_id}/{$shift_date}";
        $tertagihurl = $tertagih->param_value."/{$port_id}/{$ship_class}/{$shift_id}/{$shift_date}";
        */

        $terjualurl = $terjual->param_value."/{$port_id}/{$ship_class}/{$shift_id}/{$type}/{$shift_date}";
        $tertagihurl = $tertagih->param_value."/{$port_id}/{$ship_class}/{$shift_id}/{$type}/{$shift_date}";

        // echo $tertagihurl. 
        // "<p> scheduler/sap/tertagihV5/3/1/1/2/2021-10-04";
        // exit;



	    $curl = curl_init();

        if ($type == 1 OR $type == 3 )  {
            curl_setopt_array($curl, array(
                // CURLOPT_PORT => $portSocket,
                CURLOPT_URL => $terjualurl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
                // CURLOPT_HTTPHEADER => array(
                //     "Postman-Token: caa08c67-a11c-4785-ae78-0c47f4b4c851",
                //                     "cache-control: no-cache"
                // ),
            ));
            // echo ($terjualurl);
            // echo "";
        }
        else if ($type == 2 or $type == 4  ){
            curl_setopt_array($curl, array(
                // CURLOPT_PORT => $portSocket,
                CURLOPT_URL => $tertagihurl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
                // CURLOPT_HTTPHEADER => array(
                //     "Postman-Token: caa08c67-a11c-4785-ae78-0c47f4b4c851",
                //                     "cache-control: no-cache"
                // ),
            ));
            // echo $tertagihurl;
            // echo "";
        }

        $response = curl_exec($curl);
        $tes = json_decode($response, true);
		$err = curl_error($curl);


        curl_close($curl);

        if ($response) {
            echo $res=json_api(1, $tes['message']);
            // print_r( $tes);
            // echo $tes['message'];
        }
        else {
            echo $res=json_api(0, 'Gagal hit api  '.$err);
            // echo $err;
        }
        // echo "</br> ";
        // echo $response;
        // echo "</br> ";
        // echo $err;

    }

    // public function detail($booking_code)
    // {
    //     validate_ajax();
    //     $this->global_model->checkAccessMenuAction($this->_module,'detail');

    //     $id=$this->enc->decode($booking_code);

    //     $data['home']     = 'Home';
    //     $data['url_home'] = site_url('home');
    //     $data['title']    = 'detail booking';
    //     $data['title'] = 'Detail Booking';
    //     $data['content']  = 'booking/detail';
    //     $data['id']       = $booking_code;
    //     $data['port']=$this->booking->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
    //     $data['booking']=$this->booking->select_data("$this->_table","where booking_code ='".$id."' ")->row();

    //     $this->load->view($this->_module.'/detail_modal',$data); 

    //     // $this->load->view('default',$data);   
    // }

    // public function listDetail(){   

    //     $booking_code=$this->enc->decode($this->input->post('id'));

    //     $rows = $this->booking->listDetail("where a.booking_code='".$booking_code."' ")->result();
    //     echo json_encode($rows);

    // }

    // public function listVehicle(){   

    //     $booking_code=$this->enc->decode($this->input->post('id'));

    //     $rows = $this->booking->listVehicle("where a.booking_code='".$booking_code."' ")->result();
    //     echo json_encode($rows);

    // }

    // function get_dock()
    // {
    //     $port=$this->enc->decode($this->input->post('port'));

    //     empty($port)?$port_id='NULL':$port_id=$port;
    //     $dock=$this->dock->select_data($this->_table,"where port_id=".$port_id." and status=1")->result();

    //     $data=array();
    //     foreach($dock as $key=>$value)
    //     {
    //         $value->id=$this->enc->encode($value->id);
    //         $data[]=$value;            
    //     }

    //      echo json_encode($data);
    // }



    function check_gs()
    {
        // checking apakan user gs ,
        $check_gs=$this->pengiriman->select_data("core.t_mtr_user", " where id=".$this->session->userdata("id"))->row();

        return $check_gs->user_group_id;
    }

    /*
    Document   : Monitoring
    Created on : 21 juli, 2023
    Author     : soma
    Description: end Enhancement cms penerapan validasi dan token csrf 
    */

}
