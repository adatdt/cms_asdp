<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Trx_schedule extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_trx_schedule','schedule');
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
                $dock=$this->schedule->select_data("app.t_mtr_dock","where port_id=".$this->session->userdata('port_id') )->result();   
                $row_port=1;
            }
            else
            {
                $port=$this->schedule->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result();
                $dock="";
                $row_port=0;
            }
        }
        else
        {
            $port=$this->schedule->select_data("app.t_mtr_port","where id=".$this->identity_app()." ")->result();
            $dock=$this->schedule->select_data("app.t_mtr_dock","where port_id=".$this->identity_app())->result();   
            $row_port=1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Jadwal Realisasi',
            'content'  => 'trx_schedule/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'download_excel'=> checkBtnAccess($this->_module,'download_excel'),
            'service'  => $this->schedule->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'port'=>$port,
            'dock'=>$dock,
            'row_port'=>$row_port,
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
        $excel->setHeader('Realisasi_jadwal.xls');
        $excel->BOF();



        $excel->writeLabel(0, 0, "No");
        $excel->writeLabel(0, 1, "KODE JADWAL");
        $excel->writeLabel(0, 2, "NAMA KAPAL");
        $excel->writeLabel(0, 3, "TANGGAL JADWAL");
        $excel->writeLabel(0, 4, "PELABUHAN");
        $excel->writeLabel(0, 5, "DERMAGA");
        $excel->writeLabel(0, 6, "TUJUAN");
        $excel->writeLabel(0, 7, "JAM MASUK ALUR");
        $excel->writeLabel(0, 8, "JAM SANDAR");
        $excel->writeLabel(0, 9, "JAM BUKA LAYANAN");
        $excel->writeLabel(0, 10, "JAM TUTUP LAYANAN");
        $excel->writeLabel(0, 11, "JAM TUTUP RAMDOR");
        $excel->writeLabel(0, 12, "JAM BERANGKAT");


        $index=1;
        foreach ($data as $key => $value) {

            $excel->writeLabel($index,0, $index);
            $excel->writeLabel($index,1, $value->schedule_code);
            $excel->writeLabel($index,2, $value->ship_name);
            $excel->writeLabel($index,3, $value->schedule_date);
            $excel->writeLabel($index,4, $value->port_name);
            $excel->writeLabel($index,5, $value->dock_name);
            $excel->writeLabel($index,6, $value->port_destination);
            $excel->writeLabel($index,7, $value->ploting_date);
            $excel->writeLabel($index,8, $value->docking_date);
            $excel->writeLabel($index,9, $value->open_boarding_date);
            $excel->writeLabel($index,10, $value->close_boarding_date);
            $excel->writeLabel($index,11, $value->close_ramp_door_date);
            $excel->writeLabel($index,12, $value->sail_date);


        $index++;
        }
         
        $excel->EOF();
        exit();
    }

}
