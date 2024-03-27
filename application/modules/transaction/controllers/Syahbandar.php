<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Syahbandar extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_syahbandar','syahbandar');
        $this->load->model('global_model');
        $this->load->library('Html2pdf');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_boarding';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/syahbandar';
        $this->dbView=checkReplication();
	}

	public function index(){   
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request()){
            $rows = $this->syahbandar->dataList();
            echo json_encode($rows);
            exit;
        }


        if($this->idendity_app()==0)
        {
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->syahbandar->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')." ")->result();
                $ket="1";   
            }
            else
            {
                $port=$this->syahbandar->select_data("app.t_mtr_port","where  status not in (-5) order by name asc")->result(); 
                $ket="";  
            }

        }
        else
        {
            $port=$this->syahbandar->select_data("app.t_mtr_port","where id=".$this->idendity_app()." ")->result();
            $ket="1";
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Syahbandar',
            'content'  => 'syahbandar/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'service'  => $this->syahbandar->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'port'=>$port,
            'ket'=>$ket,
            'port_destination'=>$this->syahbandar->select_data("app.t_mtr_port","where  status not in (-5) order by name asc")->result(),
            'dock'=>$this->syahbandar->select_data("app.t_mtr_dock","where status not in (-5) order by name asc")->result(),
            'team'=>$this->syahbandar->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),
            
        );

		$this->load->view('default', $data);
	}


    public function data_approve()
    {
        checkUrlAccess($this->_module,'view');
        if($this->input->is_ajax_request()){
            $rows = $this->syahbandar->dataApprove();
            echo json_encode($rows);
            exit;
        }

    }


    public function detail($boarding_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $code=$this->enc->decode($boarding_code);

        // check status apakah datanya sudah ada approval syahbandar (tidak dipakai)
        $check_status=$this->syahbandar->select_data("app.t_trx_approval_port_officer","where boarding_code='".$code."' ")->num_rows();

        // check status apakah datanya sudah ada approval operator kapal
        $check_status_kapal=$this->syahbandar->select_data("app.t_trx_approval_ship_officer","where boarding_code='".$code."' ")->num_rows();

        $check_status>0?$status='Sudah Approve':$status='Belum Approve';
        $check_status>0?$btn=1:$btn=0;

        $check_status_kapal>0?$status_kapal='Sudah Approve':$status_kapal='Belum Approve';      
        $check_status_kapal>0?$btn_kapal=1:$btn_kapal=0;


        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title'] = 'Syahbandar';
        $data['content']  = 'boarding/detail_modal';
        $data['detail_passanger']=$this->syahbandar->list_detail_passanger("where a.boarding_code='".$code."'  and  e.service_id=1 and b.status=1 ")->result();
        $data['passanger_count']=$this->syahbandar->list_detail_passanger("where a.boarding_code='".$code."'  and  e.service_id=1 and b.status=1 ")->num_rows();
        $data['detail_passanger_vehicle']=$this->syahbandar->list_detail_passanger_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1 and c.status >= 5 ")->result();

        
        // $data['passanger_vehicle_count']=$this->syahbandar->list_detail_passanger_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1")->num_rows();


        // $data['passanger_vehicle_count']=$this->syahbandar->total_dalam_kendaraan($code)->result();

        $data['detail_vehicle']=$this->syahbandar->list_detail_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1 ")->result();
        $data['vehicle_count']=$this->syahbandar->list_detail_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1")->num_rows();
        $data['code']   = $boarding_code;
        $data['get_ship_name']=$this->syahbandar->get_ship_name($code)->row();
        $data['port']=$this->syahbandar->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['status']=$status;
        $data['btn']=$btn;
        $data['btn_kapal']=$btn_kapal;
        $data['btn_akses']=checkBtnAccess($this->_module,'edit');
        $data['btn_pdf']=checkBtnAccess($this->_module,'download_pdf');
        $data['btn_excel']=checkBtnAccess($this->_module,'download_excel');
        $data['status_kapal']=$status_kapal;

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

    public function download_05082021($encode)
    {

        $this->load->library('exceldownload');

        $code=$this->enc->decode($encode);
        $data=$this->syahbandar->list_detail_passanger("where a.boarding_code='".$code."'  and  e.service_id=1 and b.status=1 ")->result();
        $excel = new Exceldownload();
        // Send Header


        $excel->setHeader('data_penumpang.xls');
        $excel->BOF();


        $excel->writeLabel(0, 0, "NO");
        $excel->writeLabel(0, 1, "NOMER BOOKING");
        $excel->writeLabel(0, 2, "NOMER TIKET");
        $excel->writeLabel(0, 3, "NOMER ID");
        $excel->writeLabel(0, 4, "NAMA");
        $excel->writeLabel(0, 5, "KOTA"); 
        $excel->writeLabel(0, 6, "JENIS KELAMIN");
        $excel->writeLabel(0, 7, "USIA");
        $excel->writeLabel(0, 8, "SERVIS");
        $excel->writeLabel(0, 9, "TIPE PENUMPANG");
        $excel->writeLabel(0, 10, "TIPE KAPAL");
        $excel->writeLabel(0, 11, "KEBERANGKATAN");
        $excel->writeLabel(0, 12, "TUJUAN");
        $excel->writeLabel(0, 13, "TANGGAL BOARDING");
        $excel->writeLabel(0, 14, "TANGGAL KEBERANGKAN"); 
        $excel->writeLabel(0, 14, "KETERANGAN"); 


        $index=1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index,0, $index);
            $excel->writeLabel($index,1, $value->booking_code);
            $excel->writeLabel($index,2, $value->ticket_number);
            $excel->writeLabel($index,3, $value->id_number);
            $excel->writeLabel($index,4, $value->passanger_name);
            $excel->writeLabel($index,5, $value->city);
            $excel->writeLabel($index,6, $value->gender);
            $excel->writeLabel($index,7, $value->age);
            $excel->writeLabel($index,8, $value->service_name);
            $excel->writeLabel($index,9, $value->passanger_type_name);
            $excel->writeLabel($index,10, $value->ship_class_name);
            $excel->writeLabel($index,11, $value->port_origin);
            $excel->writeLabel($index,12, $value->port_destination);
            $excel->writeLabel($index,13, $value->created_on);
            $excel->writeLabel($index,14, $value->sail_date);
            $excel->writeLabel($index,15, $value->manifest_data_from);

            $index++;
        }
         
        $excel->EOF();
        exit();
    }


    public function download_vehicle_05082021($encode)
    {

        $this->load->library('exceldownload');

        $code=$this->enc->decode($encode);
        $data=$this->syahbandar->list_detail_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1 ")->result();
        $excel = new Exceldownload();
        // Send Header



        $excel->setHeader('data_kendaraan.xls');
        $excel->BOF(); 

        $excel->writeLabel(0, 0, "NO");
        $excel->writeLabel(0, 1, "NOMER BOOKING");
        $excel->writeLabel(0, 2, "NOMER TIKET");
        $excel->writeLabel(0, 3, "NOMER PLAT");
        $excel->writeLabel(0, 4, "SERVIS");
        $excel->writeLabel(0, 5, "TIPE PENUMPANG"); 
        $excel->writeLabel(0, 6, "TIPE KAPAL");
        $excel->writeLabel(0, 7, "KEBERANGKATAN");
        $excel->writeLabel(0, 8, "TUJUAN");
        $excel->writeLabel(0, 9, "TANGGAL BOARDING");
        $excel->writeLabel(0, 10, "TANGGAL KEBERANGKATAN");
        $excel->writeLabel(0, 11, "TANGGAL KEBERANGKATAN");


        $index=1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index,0, $index);
            $excel->writeLabel($index,1, $value->booking_code);
            $excel->writeLabel($index,2, $value->ticket_number);
            $excel->writeLabel($index,3, $value->plate_number);
            $excel->writeLabel($index,4, $value->service_name);
            $excel->writeLabel($index,5, $value->golongan);
            $excel->writeLabel($index,6, $value->ship_class_name);
            $excel->writeLabel($index,7, $value->port_origin);
            $excel->writeLabel($index,8, $value->port_destination);
            $excel->writeLabel($index,9, $value->created_on);
            $excel->writeLabel($index,10, $value->sail_date);
            $excel->writeLabel($index,11, $value->manifest_data_from);

            $index++;
        }
         
        $excel->EOF();
        exit();
    }

    public function download_vehicle_passanger_05082021($encode)
    {

        $this->load->library('exceldownload');

        $code=$this->enc->decode($encode);
        $data=$this->syahbandar->list_detail_passanger_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1 and c.status >= 5  ")->result();
        $excel = new Exceldownload();
        // Send Header



        $excel->setHeader('data_penumpang_kendaraan.xls');
        $excel->BOF();


        $excel->writeLabel(0, 0, "NO");
        $excel->writeLabel(0, 1, "NOMER BOOKING");
        $excel->writeLabel(0, 2, "NOMER TIKET");
        $excel->writeLabel(0, 3, "NOMER PLAT");
        $excel->writeLabel(0, 4, "NOMER ID");
        $excel->writeLabel(0, 5, "NAMA");
        $excel->writeLabel(0, 6, "KOTA"); 
        $excel->writeLabel(0, 7, "JENIS KELAMIN");
        $excel->writeLabel(0, 8, "USIA");
        $excel->writeLabel(0, 9, "SERVIS");
        $excel->writeLabel(0, 10, "TIPE PENUMPANG");
        $excel->writeLabel(0, 11, "TIPE KAPAL");
        $excel->writeLabel(0, 12, "KEBERANGKATAN");
        $excel->writeLabel(0, 13, "TUJUAN");
        $excel->writeLabel(0, 14, "TANGGAL BOARDING");
        $excel->writeLabel(0, 15, "TANGGAL KEBERANGKAN"); 
        $excel->writeLabel(0, 15, "KETERANGAN"); 


        $index=1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index,0, $index);
            $excel->writeLabel($index,1, $value->booking_code);
            $excel->writeLabel($index,2, $value->ticket_number);
            $excel->writeLabel($index,3, $value->plate_number);
            $excel->writeLabel($index,4, $value->id_number);
            $excel->writeLabel($index,5, $value->passanger_name);
            $excel->writeLabel($index,6, $value->city);
            $excel->writeLabel($index,7, $value->gender);
            $excel->writeLabel($index,8, $value->age);
            $excel->writeLabel($index,9, $value->service_name);
            $excel->writeLabel($index,10, $value->passanger_type_name);
            $excel->writeLabel($index,11, $value->ship_class_name);
            $excel->writeLabel($index,12, $value->port_origin);
            $excel->writeLabel($index,13, $value->port_destination);
            $excel->writeLabel($index,14, $value->created_on);
            $excel->writeLabel($index,15, $value->sail_date);
            $excel->writeLabel($index,16, $value->manifest_data_from);

            $index++;
        }
         
        $excel->EOF();
        exit();
    }

    public function download($encode)
    {

        $this->load->library('exceldownload');

        $code=$this->enc->decode($encode);
        $data=$this->syahbandar->list_detail_passanger("where a.boarding_code='".$code."'  and  e.service_id=1 and b.status=1 ")->result();
        $excel = new Exceldownload();
        // Send Header


        $excel->setHeader('data_penumpang.xls');
        $excel->BOF();


        $excel->writeLabel(0, 0, "NO");
        $excel->writeLabel(0, 1, "NOMER BOOKING");
        $excel->writeLabel(0, 2, "NOMER TIKET");
        $excel->writeLabel(0, 3, "NOMER ID");
        $excel->writeLabel(0, 4, "NAMA");
        $excel->writeLabel(0, 5, "KOTA"); 
        $excel->writeLabel(0, 6, "JENIS KELAMIN");
        $excel->writeLabel(0, 7, "USIA");
        $excel->writeLabel(0, 8, "SERVIS");
        $excel->writeLabel(0, 9, "TIPE PENUMPANG");
        $excel->writeLabel(0, 10, "TIPE KAPAL");
        $excel->writeLabel(0, 11, "KEBERANGKATAN");
        $excel->writeLabel(0, 12, "TUJUAN");
        $excel->writeLabel(0, 13, "TANGGAL BOARDING");
        $excel->writeLabel(0, 14, "TANGGAL KEBERANGKAN"); 
        $excel->writeLabel(0, 14, "KETERANGAN"); 


        $index=1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index,0, $index);
            $excel->writeLabel($index,1, $value->booking_code);
            $excel->writeLabel($index,2, $value->ticket_number);
            $excel->writeLabel($index,3, $value->id_number);
            $excel->writeLabel($index,4, $value->passanger_name);
            $excel->writeLabel($index,5, $value->city);
            $excel->writeLabel($index,6, $value->gender);
            $excel->writeLabel($index,7, $value->age);
            $excel->writeLabel($index,8, $value->service_name);
            $excel->writeLabel($index,9, $value->passanger_type_name);
            $excel->writeLabel($index,10, $value->ship_class_name);
            $excel->writeLabel($index,11, $value->port_origin);
            $excel->writeLabel($index,12, $value->port_destination);
            $excel->writeLabel($index,13, $value->open_boarding_date);
            $excel->writeLabel($index,14, $value->sail_date);
            $excel->writeLabel($index,15, $value->manifest_data_from);

            $index++;
        }
         
        $excel->EOF();
        exit();
    }


    public function download_vehicle($encode)
    {

        $this->load->library('exceldownload');

        $code=$this->enc->decode($encode);
        $data=$this->syahbandar->list_detail_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1 ")->result();
        $excel = new Exceldownload();
        // Send Header



        $excel->setHeader('data_kendaraan.xls');
        $excel->BOF(); 

        $excel->writeLabel(0, 0, "NO");
        $excel->writeLabel(0, 1, "NOMER BOOKING");
        $excel->writeLabel(0, 2, "NOMER TIKET");
        $excel->writeLabel(0, 3, "NOMER PLAT");
        $excel->writeLabel(0, 4, "SERVIS");
        $excel->writeLabel(0, 5, "TIPE PENUMPANG"); 
        $excel->writeLabel(0, 6, "TIPE KAPAL");
        $excel->writeLabel(0, 7, "KEBERANGKATAN");
        $excel->writeLabel(0, 8, "TUJUAN");
        $excel->writeLabel(0, 9, "TANGGAL BOARDING");
        $excel->writeLabel(0, 10, "TANGGAL KEBERANGKATAN");
        $excel->writeLabel(0, 11, "TANGGAL KEBERANGKATAN");


        $index=1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index,0, $index);
            $excel->writeLabel($index,1, $value->booking_code);
            $excel->writeLabel($index,2, $value->ticket_number);
            $excel->writeLabel($index,3, $value->plate_number);
            $excel->writeLabel($index,4, $value->service_name);
            $excel->writeLabel($index,5, $value->golongan);
            $excel->writeLabel($index,6, $value->ship_class_name);
            $excel->writeLabel($index,7, $value->port_origin);
            $excel->writeLabel($index,8, $value->port_destination);
            $excel->writeLabel($index,9, $value->open_boarding_date);
            $excel->writeLabel($index,10, $value->sail_date);
            $excel->writeLabel($index,11, $value->manifest_data_from);

            $index++;
        }
         
        $excel->EOF();
        exit();
    }

    public function download_vehicle_passanger($encode)
    {

        $this->load->library('exceldownload');

        $code=$this->enc->decode($encode);
        $data=$this->syahbandar->list_detail_passanger_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1 and c.status >= 5 ")->result();
        $excel = new Exceldownload();
        // Send Header



        $excel->setHeader('data_penumpang_kendaraan.xls');
        $excel->BOF();


        $excel->writeLabel(0, 0, "NO");
        $excel->writeLabel(0, 1, "NOMER BOOKING");
        $excel->writeLabel(0, 2, "NOMER TIKET");
        $excel->writeLabel(0, 3, "NOMER PLAT");
        $excel->writeLabel(0, 4, "NOMER ID");
        $excel->writeLabel(0, 5, "NAMA");
        $excel->writeLabel(0, 6, "KOTA"); 
        $excel->writeLabel(0, 7, "JENIS KELAMIN");
        $excel->writeLabel(0, 8, "USIA");
        $excel->writeLabel(0, 9, "SERVIS");
        $excel->writeLabel(0, 10, "TIPE PENUMPANG");
        $excel->writeLabel(0, 11, "TIPE KAPAL");
        $excel->writeLabel(0, 12, "KEBERANGKATAN");
        $excel->writeLabel(0, 13, "TUJUAN");
        $excel->writeLabel(0, 14, "TANGGAL BOARDING");
        $excel->writeLabel(0, 15, "TANGGAL KEBERANGKAN"); 
        $excel->writeLabel(0, 15, "KETERANGAN"); 


        $index=1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index,0, $index);
            $excel->writeLabel($index,1, $value->booking_code);
            $excel->writeLabel($index,2, $value->ticket_number);
            $excel->writeLabel($index,3, $value->plate_number);
            $excel->writeLabel($index,4, $value->id_number);
            $excel->writeLabel($index,5, $value->passanger_name);
            $excel->writeLabel($index,6, $value->city);
            $excel->writeLabel($index,7, $value->gender);
            $excel->writeLabel($index,8, $value->age);
            $excel->writeLabel($index,9, $value->service_name);
            $excel->writeLabel($index,10, $value->passanger_type_name);
            $excel->writeLabel($index,11, $value->ship_class_name);
            $excel->writeLabel($index,12, $value->port_origin);
            $excel->writeLabel($index,13, $value->port_destination);
            $excel->writeLabel($index,14, $value->open_boarding_date);
            $excel->writeLabel($index,15, $value->sail_date);
            $excel->writeLabel($index,16, $value->manifest_data_from);

            $index++;
        }
         
        $excel->EOF();
        exit();
    }

    function download_pdf()
    {
        $code=$this->enc->decode($this->input->get('boarding_code'));
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $n=$this->input->get('n');       

        // 1 dan 4 id dewasa dan lansia
        $gender = $this->syahbandar->get_sum_penumpang($code,"1,4");
        $anak = $this->syahbandar->get_sum_penumpang($code,"2");
        $bayi = $this->syahbandar->get_sum_penumpang($code,"3");

        $data['dewasa_l']=(object)array("total_penumpang"=>$gender["L"]);
        $data['dewasa_p']=(object)array("total_penumpang"=>$gender["P"]);
        $data['anak']=(object)array("total_penumpang"=>$anak["total"]);
        $data['bayi']=(object)array("total_penumpang"=>$bayi["total"]);
        $data['a']=$this->syahbandar->get_sum_vehicle($code)->result();
        $data['detail']=$this->syahbandar->get_detail($code)->row();


        // menghitung mengambil data sesuai row booking passanger
        $data['hitung_passanger_vehicle']=$this->syahbandar->get_detail_passanger_vehicle2($code);
        $data['detail_passanger']=$this->syahbandar->list_detail_passanger("where a.boarding_code='".$code."'  and  e.service_id=1 and b.status=1")->result();

        if ($n=='summary')
        {
            $data['detail_passanger_vehicle']=$this->syahbandar->get_detail_passanger_vehicle($code);

        }
        else
        {
            $data['detail_passanger_vehicle']=$data['hitung_passanger_vehicle'] ;           
        }



        $this->load->view('transaction/syahbandar/pdf',$data);
    }


    public function download_excel()
    {
        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");
        $status=$this->input->get("status");

        // jika status 1 approve jika selain itu data belum approve
        if ($status==1)
        {
            $data = $this->syahbandar->download2();
        }
        else
        {
            $data = $this->syahbandar->download();
        }


        $file_name = 'Kapal boarding (Syahbandar) tanggal '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height'=>50, 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20,20],'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

        $header = array(
            'NO' =>'string',
            'TANGGAL BOARDING' =>'string',
            'KODE BOARDING' =>'string',
            'TANGGAL JADWAL' =>'string',
            'KAPAL' =>'string',
            'PELABUHAN' =>'string',
            'DERMAGA' =>'string',
            'TUJUAN' =>'string',
            'TIPE KAPAL' =>'string',
            'JAM BERANGKAT' =>'string',
            'KETERANGAN' =>'string',
        );

        $no=1;


        foreach ($data as $key => $value) {
            $rows[] = array($no,
                            $value->open_boarding_date,
                            $value->boarding_code,
                            $value->schedule_date,
                            $value->ship_name,
                            $value->port_name,
                            $value->dock_name,
                            $value->port_destination,
                            $value->ship_class_name,
                            empty($value->sail_date)?"":date("H:i:s",strtotime ($value->sail_date))
                            ,
                            $value->ket,
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

    function approve()
    {
        $boarding_code=$this->enc->decode($this->input->post('code'));

        $data_boarding=$this->syahbandar->select_data("app.t_trx_open_boarding","where boarding_code='".$boarding_code."' ")->row();

        $getname=$this->syahbandar->select_data("core.t_mtr_user","where id='".$this->session->userdata('id')."' ")->row();

        // check data apakah operator kapal sudah approve
        $check_operator_kapal=$this->syahbandar->select_data("app.t_trx_approval_ship_officer","where boarding_code='".$boarding_code."' ");

        $data=array(
            'name'=>$getname->first_name." ".$getname->last_name,
            'schedule_code'=>$data_boarding->schedule_code,
            'boarding_code'=>$data_boarding->boarding_code,
            // 'ship_id'=>$data_boarding->ship_id,
            'status'=>1,
            'created_by'=>$this->session->userdata("username"),
            'created_on'=>date("Y-m-d"),
        );

        if ($check_operator_kapal->num_rows()<1)
        {

            echo $res=json_api(0, 'Gagal approve, data belum di approve oleh operator kapal');

        }
        else
        {
            $this->db->trans_begin();

             $this->syahbandar->insert_data("app.t_trx_approval_port_officer",$data);

                // echo json_encode($data);

                if ($this->db->trans_status() === FALSE)
                {
                    //gagal
                    $this->db->trans_rollback();
                    echo $res=json_api(0, 'gagal approve');
                }
                else
                {
                    // berhasil
                    $this->db->trans_commit();
                    echo $res=json_api(1, 'Berhasil approve Kapal');
                }

        }

    }

    function idendity_app()
    {
        $data=$this->syahbandar->select_data("app.t_mtr_identity_app", "")->row();
        return $data->port_id;
    }
}
