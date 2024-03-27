<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Boarding_passanger extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_boarding_passanger','boarding');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_boarding';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/boarding_passanger';
        
        // $this->dbView   = $this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction   = $this->load->database("dbAction",TRUE);
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->boarding->dataList();
            echo json_encode($rows);
            exit;
        }

        $get_app=$this->boarding->get_identity_app();

        if($get_app==0)
        {
            // ambil port berdasarkan user
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->boarding->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id'))->result();
                $row_port=1;
            }
            else
            {
                $port=$this->boarding->select_data("app.t_mtr_port","where status !='-5' order by name asc")->result();
                $row_port=0;
            }
        }
        else
        {
            $port=$this->boarding->select_data("app.t_mtr_port","where id=".$get_app)->result();
            $row_port=1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Penumpang Boarding',
            'content'  => 'boarding_passanger/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'service'  => $this->boarding->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'gs'=>$this->check_gs()==12?"false":"true",
            'port'=>$port,
            'row_port'=>$row_port,
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),
            'team'=>$this->boarding->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
	}


    public function download_excel()
    {

        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");

        $data = $this->boarding->download();

        $file_name = 'Boarding penumpang tanggal '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

        if($this->check_gs()==12)
        {
            $header = array(
                'NO' =>'string',
                'TANGGAL BOARDING' =>'string',
                'KODE BOARDING' =>'string',
                'PELABUHAN' =>'string',
                'DERMAGA' =>'string',
                'KODE BOOKING' =>'string',
                'NAMA PENUMPANG' =>'string',
                'UMUR' =>'string',
                'JENIS KELAMIN' =>'string',
                'TIPE PENUMPANG' =>'string',
                'SERVICE' =>'string',
                'NAMA KAPAL' =>'string',
                'TIPE KAPAL' =>'string',
                'PERANGKAT BOARDING' =>'string',
                'Keterangan' =>'string',
            );

            $no=1;

            foreach ($data as $key => $value) {
                $rows[] = array($no,
                                $value->boarding_date,
                                $value->boarding_code,
                                $value->port_name,
                                $value->dock_name,
                                $value->booking_code,
                                $value->passanger_name,
                                $value->age,
                                $value->gender,
                                $value->passanger_type_name,
                                $value->service_name,
                                $value->ship_name,
                                $value->ship_class_name,
                                $value->manifest_data_from,
                            );
                $no++;
            }

        }
        else
        {

            $header = array(
                'NO' =>'string',
                'TANGGAL BOARDING' =>'string',
                'KODE BOARDING' =>'string',
                'PELABUHAN' =>'string',
                'DERMAGA' =>'string',
                'KODE BOOKING' =>'string',
                'NOMER TIKET' =>'string',
                'NAMA PENUMPANG' =>'string',
                'UMUR' =>'string',
                'JENIS KELAMIN' =>'string',
                'TIPE PENUMPANG' =>'string',
                'SERVICE' =>'string',
                'NAMA KAPAL' =>'string',
                'TIPE KAPAL' =>'string',
                'PERANGKAT BOARDING' =>'string',
                'KETERANGAN' =>'string',
            );

            $no=1;

            foreach ($data as $key => $value) {
                $rows[] = array($no,
                                $value->boarding_date,
                                $value->boarding_code,
                                $value->port_name,
                                $value->dock_name,
                                $value->booking_code,
                                $value->ticket_number,
                                $value->passanger_name,
                                $value->age,
                                $value->gender,
                                $value->passanger_type_name,
                                $value->service_name,
                                $value->ship_name,
                                $value->ship_class_name,
                                $value->terminal_name,
                                $value->manifest_data_from,
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
        $check_gs=$this->boarding->select_data("core.t_mtr_user", " where id=".$this->session->userdata("id"))->row();

        return $check_gs->user_group_id;
    }

}
