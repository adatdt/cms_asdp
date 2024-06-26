<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Sap_summary extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_sap_summary','summary');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_summary_sap';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'monitoring/sap_summary';
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

            $rows = $this->summary->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->summary->get_identity_app();
        
        if($get_identity==0)
        {
            // ambil port berdasarkan user
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->summary->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id'))->result();
                $row_port=1;
            }
            else
            {
                $port=$this->summary->select_data("app.t_mtr_port","where status !='-5' order by name asc")->result();
                $row_port=0;
            }
        }
        else
        {
            $port=$this->summary->select_data("app.t_mtr_port","where id=".$get_identity)->result();
            $row_port=1;
        }        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Log Summary Local',
            'content'  => 'sap_summary/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'ship_class'  => $this->summary->select_data("app.t_mtr_ship_class","where status=1 order by name asc")->result(),
            'port'=>$port,
            'row_port'=>$row_port,
            'gs'=>$this->check_gs()==12?"false":"true",
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),
            'team'=>$this->summary->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function generate($param)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        

        $d=$this->enc->decode($param);
        $p= explode('|', $d);

        // echo $p[1];

        if ($p[1] == "TERJUAL NORMAL" or $p[1] == "TERJUAL MANUAL" ){
            $data['title'] = 'Generate SAP Terjual';
            $data['id'] = $p[0];
            $data['type'] = $p[1];
            $data['detail']=$this->summary->select_data($this->_table,"where id=$p[0]")->row();

            $this->load->view($this->_module.'/generate_jual',$data);
        }
        else if ($p[1] == "TERTAGIH NORMAL" or $p[1] == "TERTAGIH MANUAL"){
            $data['title'] = 'Generate SAP Tertagih';
            $data['id'] = $p[0];
            $data['type'] = $p[1];
            $data['detail']=$this->summary->select_data($this->_table,"where id=$p[0]")->row();

            $this->load->view($this->_module.'/generate_tagih',$data);
        }

        // $data['title'] = 'Generate SAP';
        // $data['id'] = $p[0];
        // $data['type'] = $p[1];
        // $data['detail']=$this->summary->select_data($this->_table,"where id=$p[0]")->row();

        // $this->load->view($this->_module.'/generate_tagih',$data);   
    }

    public function summary_terjual () {     //blm edit
        // if (is_null($date)) {
        //     $date = date('Y-m-d', strtotime(date('Y-m-d')."-1 days"));
        // }     
        // $check   = $this->dob_check($date,$port, $class);
        $shift_date=trim($this->input->post('shift_date'));
        $port_id=trim($this->input->post('port_id'));
        $shift_id=trim($this->input->post('shift_id'));
        $ship_class=trim($this->input->post('ship_class'));
        $type=trim($this->input->post('type'));
        $id=trim($this->input->post('id'));

        $date    = date('Y-m-d', strtotime($shift_date));
        // $check_exist = $this->summary->check_summary_sap($port_id, $ship_class, $shift_id, $date, 1);        

        $result  = $this->summary->getSummaryTerjual($date, $port_id, $ship_class, $shift_id);
        if ($result) {
            if (!is_null($result->produksi)) {                       
                    $text = "TERJUAL".strtoupper(" ".$date." ".$result->shift_name);
                    $data = array(
                                    'port_id'    => $port_id, 
                                    'shift_date' => $date,
                                    'shift_id'   => $shift_id,
                                    'produksi'   => $result->produksi,
                                    'pendapatan' => $result->pendapatan,
                                    'text'       => $text,
                                    'created_by' =>'system',
                                    'type'       => $type,
                                    'ship_class' => $ship_class
                                );
                    $insert = $this->summary->insert_summary_sap($data);
                    if ($insert) {
                        // echo "success insert data";
                        echo $res=json_api(1, "success insert data");
                    }
                    else{
                        // echo "failed insert data";
                        echo $res=json_api(0, "failed insert data");
                    }                       
                    // echo $res=json_api(1, $data);
            }
            else{
                echo $res=json_api(0, "no data found");
            }                    
        }
        else{
                //  echo "no data found";
                    echo $res=json_api(0, "no data found");
            }
    }

    public function summary_tertagih () {
        // if (is_null($date)) {
        //     $date = date('Y-m-d', strtotime(date('Y-m-d')."-1 days"));
        // }     
        // $check   = $this->dob_check($date,$port, $class);
        // $date    = date('Y-m-d', strtotime($date));
        $shift_date=trim($this->input->post('shift_date'));
        $port_id=trim($this->input->post('port_id'));
        $shift_id=trim($this->input->post('shift_id'));
        $ship_class=trim($this->input->post('ship_class'));
        $type=trim($this->input->post('type'));
        $id=trim($this->input->post('id'));

        $date    = date('Y-m-d', strtotime($shift_date));
        // $check_exist = $this->sap->check_summary_sap($port, $class, $shift, $date, 2);
        // $check_exist = $this->summary->check_summary_sap($port_id, $ship_class, $shift_id, $date, 2);
        $result  = $this->summary->getSummaryTertagih($date, $port_id, $ship_class, $shift_id);
        // echo $res=json_api(1, $result);
        if ($result) {
                $produksi   = 0;
                $pendapatan =0;
                $shift_name       = "";
                foreach ($result as $key => $value) {
                    $produksi   = $produksi + $value->produksi;
                    $pendapatan = $pendapatan + $value->pendapatan;
                    $shift_name = $value->shift;
                }
                // $check_exist = $this->summary->check_summary_sap($port, $class, $shift, $date, 2);
                    $text = "TERTAGIH".strtoupper(" ".$date." ".$shift_name);
                    $data = array(
                                    'port_id'    => $port_id, 
                                    'shift_date' => $date,
                                    'shift_id'   => $shift_id,
                                    'produksi'   => $produksi,
                                    'pendapatan' => $pendapatan,
                                    'text'       => $text,
                                    'created_by' =>'system',
                                    'type'       => $type,
                                    'ship_class' => $ship_class
                                );
                    $insert = $this->summary->insert_summary_sap($data);
                    if ($insert) {
                        // echo "success insert data";
                        echo $res=json_api(1, "success insert data");
                    }
                    else{
                        // echo "failed insert data";
                        echo $res=json_api(0, "failed insert data");
                    }                                                   
            }
            else
            {
                echo $res=json_api(0, "no data found");
            }         
    }



    function check_gs()
    {
        // checking apakan user gs ,
        $check_gs=$this->summary->select_data("core.t_mtr_user", " where id=".$this->session->userdata("id"))->row();

        return $check_gs->user_group_id;
    }

    /*
    Document   : Monitoring
    Created on : 21 juli, 2023
    Author     : soma
    Description: end Enhancement cms penerapan validasi dan token csrf 
    */

}
