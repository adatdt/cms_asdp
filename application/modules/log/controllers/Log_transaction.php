<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Log_transaction extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_log_transaction','transaction');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_gate_in';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'log/log_transaction';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->transaction->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->transaction->get_identity_app();
        
        if($get_identity==0)
        {
            // ambil port berdasarkan user
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->transaction->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id'))->result();
                $row_port=1;
            }
            else
            {
                $port=$this->transaction->select_data("app.t_mtr_port","where status !='-5' order by name asc")->result();
                $row_port=0;
            }
        }
        else
        {
            $port=$this->transaction->select_data("app.t_mtr_port","where id=".$get_identity)->result();
            $row_port=1;
        }        

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Log Transaksi',
            'content'  => 'log_transaction/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'service'  => $this->transaction->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'port'=>$port,
            'row_port'=>$row_port,
            'gs'=>$this->check_gs()==12?"false":"true",
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),
            'team'=>$this->transaction->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
	}


    public function detail($booking_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $id=$this->enc->decode($booking_code);

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title']    = 'detail booking';
        $data['title'] = 'Detail Booking';
        $data['content']  = 'booking/detail';
        $data['id']       = $booking_code;
        $data['port']=$this->booking->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['booking']=$this->booking->select_data("$this->_table","where booking_code ='".$id."' ")->row();

        $this->load->view($this->_module.'/detail_modal',$data); 

        // $this->load->view('default',$data);   
    }

    public function listDetail(){   

        $booking_code=$this->enc->decode($this->input->post('id'));

        $rows = $this->booking->listDetail("where a.booking_code='".$booking_code."' ")->result();
        echo json_encode($rows);

    }

    public function listVehicle(){   

        $booking_code=$this->enc->decode($this->input->post('id'));

        $rows = $this->booking->listVehicle("where a.booking_code='".$booking_code."' ")->result();
        echo json_encode($rows);

    }

    function get_dock()
    {
        $port=$this->enc->decode($this->input->post('port'));

        empty($port)?$port_id='NULL':$port_id=$port;
        $dock=$this->dock->select_data($this->_table,"where port_id=".$port_id." and status=1")->result();

        $data=array();
        foreach($dock as $key=>$value)
        {
            $value->id=$this->enc->encode($value->id);
            $data[]=$value;            
        }

         echo json_encode($data);
    }

    public function download_excel()
    {

        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");

        $data = $this->gate_in->download()->result();

        $file_name = 'Gate in  tanggal '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

        // checking user gs 12
        if($this->check_gs()==12)
        {

            $header = array(
                'NO' =>'string',
                'TANGGAL GATEIN' =>'string',
                'KODE BOOKING' =>'string',
                'NOMER IDENTITAS' =>'string',
                'NAMA PENUMPANG' =>'string',
                'SERVIS' =>'string',
                'PERANGKAT GATEIN' =>'string',
                'PELABUHAN' =>'string',
                'BOARDING EXPIRED' =>'string',
            );

            $no=1;


            foreach ($data as $key => $value) {
                $rows[] = array($no,
                                $value->created_on,
                                $value->booking_code,
                                $value->id_number,
                                $value->passanger_name,
                                $value->service_name,
                                $value->terminal_name,
                                $value->port_name,
                                $value->boarding_expired,
                            );
                $no++;
            }

        }
        else
        {
            $header = array(
                'NO' =>'string',
                'TANGGAL GATEIN' =>'string',
                'KODE BOOKING' =>'string',
                'NOMER TICKET' =>'string',
                'NOMER IDENTITAS' =>'string',
                'NAMA PENUMPANG' =>'string',
                'SERVIS' =>'string',
                'PERANGKAT GATEIN' =>'string',
                'PELABUHAN' =>'string',
                'BOARDING EXPIRED' =>'string',
            );

            $no=1;


            foreach ($data as $key => $value) {
                $rows[] = array($no,
                                $value->created_on,
                                $value->booking_code,
                                $value->ticket_number,
                                $value->id_number,
                                $value->passanger_name,
                                $value->service_name,
                                $value->terminal_name,
                                $value->port_name,
                                $value->boarding_expired,
                            );
                $no++;
            }

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

    function check_gs()
    {
        // checking apakan user gs ,
        $check_gs=$this->transaction->select_data("core.t_mtr_user", " where id=".$this->session->userdata("id"))->row();

        return $check_gs->user_group_id;
    }

}
