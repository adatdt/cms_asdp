<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Gs extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_gs','gs');
        $this->load->model('global_model');
        $this->load->library('Html2pdf');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_approval_gs_officer';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/gs';

        // $this->dbAction = $this->load->database("dbAction", TRUE);
        $this->dbView = checkReplication();
        $this->dbView = $this->load->database("dbView", TRUE);
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->gs->dataList();
            echo json_encode($rows);
            exit;
        }



        if($this->gs->get_identity_app()==0)
        {
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->gs->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')." ")->result();
                $row_port=1;
            }
            else
            {
                $port=$this->gs->select_data("app.t_mtr_port","where  status not in (-5) order by name asc")->result();
                $row_port=0;   
            }

        }
        else
        {
            $port=$this->gs->select_data("app.t_mtr_port","where id=".$this->gs->get_identity_app()." ")->result();
            $row_port=1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Gedung Central',
            'content'  => 'gs/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'service'  => $this->gs->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'port'=>$port,
            'row_port'=>$row_port,
            'dock'=>$this->gs->select_data("app.t_mtr_dock","where status not in (-5) order by name asc")->result(),
            'team'=>$this->gs->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
            'port_destination'=>$this->gs->select_data("app.t_mtr_port","where  status not in (-5) order by name asc")->result(),
            'btn_excel'=>checkBtnAccess($this->_module,'download_excel'),
        );

		$this->load->view('default', $data);
	}


    public function detail($boarding_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $code=$this->enc->decode($boarding_code);

        // check status apakah datanya sudah ada dxi 
        // $check_status=$this->gs->select_data("app.t_trx_approval_gs_officer","where boarding_code='".$code."' ")->num_rows();

        $check_status=$this->gs->select_data("app.t_trx_approval_ship_officer","where boarding_code='".$code."' ")->num_rows();


        $check_status_syahbandar=$this->gs->select_data("app.t_trx_approval_port_officer","where boarding_code='".$code."' ")->num_rows();

        $check_status_syahbandar>0?$status_syahbandar='Sudah Approve':$status_syahbandar='Belum Approve';

        $check_status>0?$status='Sudah Approve':$status='Belum Approve';
        $check_status>0?$btn=1:$btn=0;

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title'] = 'Detail ';
        $data['content']  = 'boarding/detail_modal';
        $data['detail_passanger']=$this->gs->list_detail_passanger("where a.boarding_code='".$code."'  and  e.service_id=1 and b.status=1 ")->result();
        $data['passanger_count']=$this->gs->list_detail_passanger("where a.boarding_code='".$code."'  and  e.service_id=1 and b.status=1")->num_rows();
        $data['detail_passanger_vehicle']=$this->gs->list_detail_passanger_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1 ")->result();
        $data['passanger_vehicle_count']=$this->gs->list_detail_passanger_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1")->num_rows();


        $data['passanger_vehicle_count']=$this->gs->total_dalam_kendaraan($code)->result();


        // $data['detail_vehicle']=$this->gs->list_detail_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1 ")->result();

        $data['detail_vehicle']=$this->gs->list_detail_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1 ")->result();
        $data['vehicle_count']=$this->gs->list_detail_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1 ")->num_rows();
        $data['code']   = $boarding_code;
        $data['get_ship_name']=$this->gs->get_ship_name($code)->row();
        $data['port']=$this->gs->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['status']=$status;
        $data['status_syahbandar']=$status;
        $data['btn']=$btn;
        $data['btn_akses']=checkBtnAccess($this->_module,'edit');
        $data['btn_pdf']=checkBtnAccess($this->_module,'download_pdf');
        $data['btn_excel']=checkBtnAccess($this->_module,'download_excel');
        $data['gs']=$this->check_gs()==12?"gs":"gt";

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

    public function download($encode)
    {

        $this->load->library('exceldownload');

        $code=$this->enc->decode($encode);
        $data=$this->gs->list_detail_passanger("where a.boarding_code='".$code."'  and  e.service_id=1 and b.status=1")->result();
        $excel = new Exceldownload();
        // Send Header


        $excel->setHeader('data_penumpang.xls');
        $excel->BOF();


        if ($this->check_gs()==12)
        {
            $excel->writeLabel(0, 0, "NO");
            $excel->writeLabel(0, 1, "NOMER BOOKING");
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
            $excel->writeLabel(0, 14, "TANGGAL KJEBERANGKAN"); 


            $index=1;
            foreach ($data as $key => $value) {
                $excel->writeLabel($index,0, $index);
                $excel->writeLabel($index,1, $value->booking_code);
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

                $index++;
            }

        }
        else
        {
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
            $excel->writeLabel(0, 14, "TANGGAL KJEBERANGKAN"); 


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

                $index++;
            }

        }
         
        $excel->EOF();
        exit();
    }


    public function download_vehicle($encode)
    {

        $this->load->library('exceldownload');

        $code=$this->enc->decode($encode);
        $data=$this->gs->list_detail_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1")->result();
        $excel = new Exceldownload();
        // Send Header



        $excel->setHeader('data_kendaraan.xls');
        $excel->BOF(); 


        if($this->check_gs()==12)
        {
            $excel->writeLabel(0, 0, "NO");
            $excel->writeLabel(0, 1, "NOMER BOOKING");
            $excel->writeLabel(0, 3, "NOMER PLAT");
            $excel->writeLabel(0, 4, "SERVIS");
            $excel->writeLabel(0, 5, "TIPE PENUMPANG"); 
            $excel->writeLabel(0, 6, "TIPE KAPAL");
            $excel->writeLabel(0, 7, "KEBERANGKATAN");
            $excel->writeLabel(0, 8, "TUJUAN");
            $excel->writeLabel(0, 9, "TANGGAL BOARDING");
            $excel->writeLabel(0, 10, "TANGGAL KEBERANGKATAN");


            $index=1;
            foreach ($data as $key => $value) {
                $excel->writeLabel($index,0, $index);
                $excel->writeLabel($index,1, $value->booking_code);
                $excel->writeLabel($index,3, $value->plate_number);
                $excel->writeLabel($index,4, $value->service_name);
                $excel->writeLabel($index,5, $value->golongan);
                $excel->writeLabel($index,6, $value->ship_class_name);
                $excel->writeLabel($index,7, $value->port_origin);
                $excel->writeLabel($index,8, $value->port_destination);
                $excel->writeLabel($index,9, $value->created_on);
                $excel->writeLabel($index,10, $value->sail_date);

                $index++;
            }

        }
        else
        {
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

                $index++;
            }

        }

         
        $excel->EOF();
        exit();
    }

    public function download_vehicle_passanger($encode)
    {

        $this->load->library('exceldownload');

        $code=$this->enc->decode($encode);
        $data=$this->gs->list_detail_passanger_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1 ")->result();
        $excel = new Exceldownload();
        // Send Header

        $excel->setHeader('data_penumpang_kendaraan.xls');
        $excel->BOF();

        if($this->check_gs()==12)
        {

            $excel->writeLabel(0, 0, "NO");
            $excel->writeLabel(0, 1, "NOMER BOOKING");
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


            $index=1;
            foreach ($data as $key => $value) {
                $excel->writeLabel($index,0, $index);
                $excel->writeLabel($index,1, $value->booking_code);
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

                $index++;
            }

        }
        else
        {

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

                $index++;
            }

        }
         
        $excel->EOF();
        exit();
    }

    function download_pdf()
    {
        $code=$this->enc->decode($this->input->get('boarding_code'));

        $data['dewasa_l']=$this->gs->get_sum_penumpang($code,"L","1")->row();
        $data['dewasa_p']=$this->gs->get_sum_penumpang($code,"P","1")->row();
        // $data['anak']=$this->gs->get_sum_penumpang($code,"null","2")->row();
        $data['anak']=$this->gs->get_sum_anak($code,"2")->row();
        $data['bayi']=$this->gs->get_sum_anak($code,"3")->row();
        $data['a']=$this->gs->get_sum_vehicle($code)->result();
        $data['detail']=$this->gs->get_detail($code)->row();

        // mengihutung sum data pada total_passanger di trx_booking
        $data['hitung_passanger_vehicle']=$this->gs->total_dalam_kendaraan($code)->result();

        $data['detail_passanger']=$this->gs->list_detail_passanger("where a.boarding_code='".$code."'  and  e.service_id=1 and b.status=1 ")->result();
        $data['detail_passanger_vehicle']=$this->gs->get_detail_passanger_vehicle($code)->result();


        $this->load->view('transaction/gs/pdf',$data);
    }

    public function download_excel()
    {

        $this->global_model->checkAccessMenuAction($this->_module,'download_excel');

        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");

        $data = $this->gs->download()->result();

        $file_name = 'Kapal boarding (GS) tanggal '.$dateFrom.' s/d '.$dateTo;
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
        );

        $no=1;


        foreach ($data as $key => $value) {
            $rows[] = array($no,
                            $value->created_on,
                            $value->boarding_code,
                            $value->schedule_date,
                            $value->ship_name,
                            $value->port_name,
                            $value->dock_name,
                            $value->port_destination,
                            $value->ship_class_name,
                            empty($value->sail_date)?"":date("H:i:s",strtotime ($value->sail_date))
                            ,
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

        $data_boarding=$this->gs->select_data("app.t_trx_open_boarding","where boarding_code='".$boarding_code."' ")->row();

        $getname=$this->gs->select_data("core.t_mtr_user","where id='".$this->session->userdata('id')."' ")->row();

        // Mencari apakah ada gs yang aktif
        $check=$this->gs->select_data("app.t_trx_open_shift_gs"," where status=1");

        $gs=$check->row();


        if($check->num_rows()<1)
        {
            echo $res=json_api(0, 'Gagal approve, gs tidak ada yang open shift');
        }
        else
        {

            $data=array(
                'name'=>$getname->first_name." ".$getname->last_name,
                'schedule_code'=>$data_boarding->schedule_code,
                'boarding_code'=>$data_boarding->boarding_code,
                'shift_gs_code'=>$gs->shift_gs_code,
                'status'=>1,
                'created_by'=>$this->session->userdata("username"),
                'created_on'=>date("Y-m-d"),
            );

            $this->dbAction->trans_begin();

            $this->gs->insert_data("app.t_trx_approval_gs_officer",$data);

            // echo json_encode($data);

            if ($this->dbAction->trans_status() === FALSE)
            {
                //gagal
                $this->dbAction->trans_rollback();
                echo $res=json_api(0, 'Gagal approve');
            }
            else
            {
                // berhasil
                $this->dbAction->trans_commit();
                echo $res=json_api(1, 'Berhasil approve Kapal');
            }
        }
    }

    function check_gs()
    {
        // checking apakan user gs ,
        $check_gs=$this->gs->select_data("core.t_mtr_user", " where id=".$this->session->userdata("id"))->row();

        return $check_gs->user_group_id;
    }

}
