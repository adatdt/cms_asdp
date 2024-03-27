<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Trx_mtr_schedule extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_trx_mtr_schedule','schedule');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_schedule';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/trx_schedule';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->schedule->dataList();
            echo json_encode($rows);
            exit;
        }

        // mengambil port yang ada di user
        if($this->identity_app()==0)
        {
            if(!empty($this->session->userdata('port_id')))
            {

                $port=$this->schedule->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')." ")->result();
                $dock=$this->schedule->select_data("app.t_mtr_dock","where port_id=".$this->session->userdata('port_id')." and status not in (-5) ")->result();
                $select="select";   
            }
            else
            {
                $port=$this->schedule->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result();
                $dock="";
                $select="";
            }
        }
        else
        {
            $port=$this->schedule->select_data("app.t_mtr_port","where id=".$this->identity_app()." ")->result();
            $dock=$this->schedule->select_data("app.t_mtr_dock","where port_id=".$this->identity_app()." and status not in (-5) ")->result();
            $select="select";   
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Jadwal Planing VS Realisasi',
            'content'  => 'trx_mtr_schedule/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'download_excel'=> checkBtnAccess($this->_module,'download_excel'),
            'service'  => $this->schedule->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'port'=>$port,
            'select'=>$select,
            'dock'=>$dock,
            'team'=>$this->schedule->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
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

    function get_data()
    {
        $port_id=$this->enc->decode($this->input->post('port'));

        empty($port_id)?$id='NULL':$id=$port_id;

        $cari=$this->schedule->select_data("app.t_mtr_dock"," where status not in (-5) and port_id=".$id)->result();

        $data=array();
        foreach ($cari as $key => $value) {
            
            $value->id=$this->enc->encode($value->id);
            $data[]=$value;

        }

        echo json_encode($data);
    }

    function identity_app()
    {
        $data=$this->schedule->select_data("app.t_mtr_identity_app","")->row();

        return $data->port_id;
    }

    public function download()
    {

        $this->load->library('exceldownload');
        $data = $this->schedule->download()->result();
        $excel = new Exceldownload();
        // Send Header
        $excel->setHeader('Jadwal_planing_vs_realisasi.xls');
        $excel->BOF();

        $excel->writeLabel(0, 0, "NO");
        $excel->writeLabel(0, 1, "KODE JADWAL");
        $excel->writeLabel(0, 2, "TANGGAL JADWAL");
        $excel->writeLabel(0, 3, "MASUK ALUR (REALISASI)");
        $excel->writeLabel(0, 4, "NAMA KAPAL (RENCANA)");
        $excel->writeLabel(0, 5, "NAMA KAPAL (REALISASI)");
        $excel->writeLabel(0, 6, "PELABUHAN");
        $excel->writeLabel(0, 7, "DERMAGA");
        $excel->writeLabel(0, 8, "TUJUAN");
        $excel->writeLabel(0, 9, "JAM SANDAR (RENCANA)");
        $excel->writeLabel(0, 10, "JAM SANDAR (REALISASI)");
        $excel->writeLabel(0, 11, "JAM BUKA LAYANAN (RENCANA)");
        $excel->writeLabel(0, 12, "JAM BUKA LAYANAN (REALISASI)");
        $excel->writeLabel(0, 13, "JAM TUTUP LAYANAN (RENCANA)");
        $excel->writeLabel(0, 14, "JAM TUTUP LAYANAN (REALISASI)");
        $excel->writeLabel(0, 15, "JAM TUTUP RAMDOR (RENCANA)");
        $excel->writeLabel(0, 16, "JAM TUTUP RAMDOR (REALISASI)");
        $excel->writeLabel(0, 17, "JAM BERANGKAT (RENCANA)");
        $excel->writeLabel(0, 18, "JAM BERANGKAT (REALISASI)");
        $excel->writeLabel(0, 19, "TRIP");
        $excel->writeLabel(0, 20, "CALL");



        $index=1;
        foreach ($data as $key => $value) {

            $excel->writeLabel($index,0, $index);
            $excel->writeLabel($index,1, $value->trx_schedule_code);
            $excel->writeLabel($index,2, $value->schedule_date);
            $excel->writeLabel($index,3, $value->ploting_date);
            $excel->writeLabel($index,4, $value->pl_ship_name);
            $excel->writeLabel($index,5, $value->rl_ship_name);
            $excel->writeLabel($index,6, $value->port_name);
            $excel->writeLabel($index,7, $value->dock_name);
            $excel->writeLabel($index,8, $value->port_destination);
            $excel->writeLabel($index,9, $value->docking_on);
            $excel->writeLabel($index,10, $value->rl_docking);
            $excel->writeLabel($index,11, $value->open_boarding_on);
            $excel->writeLabel($index,12, $value->rl_open_boarding);
            $excel->writeLabel($index,13, $value->close_boarding_on);
            $excel->writeLabel($index,14, $value->rl_close_boarding);
            $excel->writeLabel($index,15, $value->close_boarding_on);
            $excel->writeLabel($index,16, $value->rl_close_ramp_door);
            $excel->writeLabel($index,17, $value->sail_time);
            $excel->writeLabel($index,18, $value->rl_sail);
            $excel->writeLabel($index,19, $value->trip);
            $excel->writeLabel($index,20, $value->call);



        $index++;
        }
         
        $excel->EOF();
        exit();
    }


    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");

        $data = $this->schedule->download()->result();

        $file_name = 'jadwal realisasi VS planing tanggal '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');


                $header = array(
                    'NO'=>'string',
                    'KODE JADWAL'=>'string',
                    'TANGGAL JADWAL'=>'string',
                    'PELABUHAN'=>'string',
                    'DERMAGA'=>'string',
                    'TUJUAN'=>'string',
                    'NAMA KAPAL (RENCANA)'=>'string',
                    'NAMA KAPAL (REALISASI)'=>'string',
                    'MASUK ALUR (REALISASI)'=>'string',
                    'JAM SANDAR (RENCANA)'=>'string',
                    'JAM SANDAR (REALISASI)'=>'string',
                    'JAM BUKA LAYANAN (RENCANA)'=>'string',
                    'JAM BUKA LAYANAN (REALISASI)'=>'string',
                    'JAM TUTUP LAYANAN (RENCANA)'=>'string',
                    'JAM TUTUP LAYANAN (REALISASI)'=>'string',
                    'JAM TUTUP RAMDOR (RENCANA)'=>'string',
                    'JAM TUTUP RAMDOR (REALISASI)'=>'string',
                    'JAM BERANGKAT (RENCANA)'=>'string',
                    'JAM BERANGKAT (REALISASI)'=>'string',
                    'TRIP' =>'string',
                    'CALL' =>'string',
                    );

        $no=1;


        foreach ($data as $key => $value) {
            $rows[] = array($no,
                            $value->trx_schedule_code,
                            $value->schedule_date,
                            $value->port_name,
                            $value->dock_name,
                            $value->port_destination,
                            $value->pl_ship_name,
                            $value->rl_ship_name,
                            $value->ploting_date,
                            $value->docking_on,
                            $value->rl_docking,
                            $value->open_boarding_on,
                            $value->rl_open_boarding,
                            $value->close_boarding_on,
                            $value->rl_close_boarding,
                            $value->close_rampdoor_on,
                            $value->rl_close_ramp_door,
                            $value->sail_time,
                            $value->rl_sail,
                            $value->trip,
                            $value->call,
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
