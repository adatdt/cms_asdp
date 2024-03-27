<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Boarding extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('m_boarding', 'boarding');
        $this->load->model('global_model');
        $this->load->library('Html2pdf');
        $this->load->library('log_activitytxt');
        $this->load->library('Restcurl');

        $this->_table    = 'app.t_trx_boarding';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/boarding';
        $this->dbView=checkReplication();
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        if ($this->input->is_ajax_request()) {
            $rows = $this->boarding->dataList();
            echo json_encode($rows);
            exit;
        }

        // $get_port=$this->boarding->select_data("core.t_mtr_user","where id=".$this->session->userdata('id')."
        //  and  status not in (-5)")->row();

        if ($this->boarding->get_identity_app() == 0) {
            if (!empty($this->session->userdata('port_id'))) {
                $port = $this->boarding->select_data("app.t_mtr_port", "where id=" . $this->session->userdata("port_id") . " ")->result();
                $row_port = 1;
            } else {
                $port = $this->boarding->select_data("app.t_mtr_port", "where  status not in (-5) order by name asc")->result();
                $row_port = 0;
            }
        } else {
            $port = $this->boarding->select_data("app.t_mtr_port", "where id=" . $this->boarding->get_identity_app() . " ")->result();
            $row_port = 1;
        }


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Boarding Kapal',
            'content'  => 'boarding/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module . '/add')),
            'service'  => $this->boarding->select_data("app.t_mtr_service", "where status=1 order by name asc")->result(),
            'port' => $port,
            'row_port' => $row_port,
            'dock' => $this->boarding->select_data("app.t_mtr_dock", "where status not in (-5) order by name asc")->result(),
            'team' => $this->boarding->select_data("core.t_mtr_team", "where status=1 order by team_name asc")->result(),
            'btn_excel' => checkBtnAccess($this->_module, 'download_excel'),
            'port_destination' => $this->boarding->select_data("app.t_mtr_port", "where  status not in (-5) order by name asc")->result(),
            'socket_protocol' => $this->config->item('socket_protocol'),
            'socket_transport' => $this->config->item('socket_transport'),
            'socket_url' => $this->config->item('socket_url'),
            'dashboard_socket_key' => $this->config->item('dashboard_socket_key')

        );

        $this->load->view('default', $data);
    }


    public function detail($param)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'detail');

        $d = explode("_", $param);

        $code = $this->enc->decode($d['0']);
        $plot = $this->enc->decode($d['1']);


        // check status apakah datanya sudah ada dxi 
        $check_status = $this->boarding->select_data("app.t_trx_approval_ship_officer", "where boarding_code='" . $code . "' ")->num_rows();

        // check status apakah datanya sudah ada dxi 
        $check_status_syahbandar = $this->boarding->select_data("app.t_trx_approval_port_officer", "where boarding_code='" . $code . "' ")->num_rows();

        // check apakah sudah close boarding 
        $check_status_ramdoor = $this->boarding->select_data("app.t_trx_close_ramp_door", "where plot_code='" . $plot . "' ")->num_rows();

        $check_status_syahbandar > 0 ? $status_syahbandar = 'Sudah Approve' : $status_syahbandar = 'Belum Approve';
        $check_status > 0 ? $status = 'Sudah Approve' : $status = 'Belum Approve';
        $check_status > 0 ? $btn = 1 : $btn = 0;

        $buttonApproved='<button class="btn btn-sm btn-warning" onclick="confirmApprove('."'"."Apakah anda ingin menyetujui keberangkatan ?'".')" title="Nonaktifkan" id="btnapprove">Approve</button>';        

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title'] = 'Detail Boarding';
        $data['content']  = 'boarding/detail_modal';
        $data['detail_passanger'] = $this->boarding->list_detail_passanger("where a.boarding_code='" . $code . "'  and  e.service_id=1 and b.status=1 ")->result();
        $data['passanger_count'] = $this->boarding->list_detail_passanger("where a.boarding_code='" . $code . "'  and  e.service_id=1 and b.status=1 ")->num_rows();
        $data['approval']=generate_button($this->_module, "change_status", $buttonApproved);

        $data['detail_passanger_vehicle'] = $this->boarding->list_detail_passanger_vehicle("where a.boarding_code='" . $code . "'  and  e.service_id=2 and b.status=1 and c.status>=5 ")->result();

        // $data['detail_passanger_vehicle'] = $this->boarding->list_detail_passanger_vehicle("where a.boarding_code='" . $code . "'  and  e.service_id=2 and b.status=1 and c.status<>'-5' ")->result();

        // $data['passanger_vehicle_count']=$this->boarding->total_dalam_kendaraan($code)->result();

        // $data['passanger_vehicle_count']=$this->boarding->list_detail_passanger_vehicle("where a.boarding_code='".$code."'  and  e.service_id=2 and b.status=1  and c.status<>'-5' ")->num_rows();
        $data['detail_vehicle'] = $this->boarding->list_detail_vehicle("where a.boarding_code='" . $code . "'  and  e.service_id=2 and b.status=1")->result();
        $data['vehicle_count'] = $this->boarding->list_detail_vehicle("where a.boarding_code='" . $code . "'  and  e.service_id=2 and b.status=1")->num_rows();
        $data['code']   = $d['0'];
        $data['get_ship_name'] = $this->boarding->get_ship_name($code)->row();
        $data['port'] = $this->boarding->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
        $data['status'] = $status;
        $data['status_syahbandar'] = $status_syahbandar;
        $data['status_close_rampdoor'] = $check_status_ramdoor;
        $data['btn'] = $btn;
        $data['btn_akses'] = checkBtnAccess($this->_module, 'edit');
        $data['btn_pdf'] = checkBtnAccess($this->_module, 'download_pdf');
        $data['btn_excel'] = checkBtnAccess($this->_module, 'download_excel');

        $this->load->view($this->_module . '/detail_modal', $data);

        // $this->load->view('default',$data);   
    }

    public function listDetail()
    {

        $booking_code = $this->enc->decode($this->input->post('id'));

        $rows = $this->booking->listDetail("where a.booking_code='" . $booking_code . "' ")->result();
        echo json_encode($rows);
    }

    public function listVehicle()
    {

        $booking_code = $this->enc->decode($this->input->post('id'));

        $rows = $this->booking->listVehicle("where a.booking_code='" . $booking_code . "' ")->result();
        echo json_encode($rows);
    }

    function get_dock()
    {
        $port = $this->enc->decode($this->input->post('port'));

        empty($port) ? $port_id = 'NULL' : $port_id = $port;
        $dock = $this->dock->select_data($this->_table, "where port_id=" . $port_id . " and status=1")->result();

        $data = array();
        foreach ($dock as $key => $value) {
            $value->id = $this->enc->encode($value->id);
            $data[] = $value;
        }

        echo json_encode($data);
    }

    public function download($encode)
    {

        $this->load->library('exceldownload');

        $code = $this->enc->decode($encode);
        $data = $this->boarding->list_detail_passanger("where a.boarding_code='" . $code . "'  and  e.service_id=1 and b.status=1 ")->result();
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
        $excel->writeLabel(0, 14, "TANGGAL SCAN");
        $excel->writeLabel(0, 15, "TANGGAL KEBERANGKAN");


        $index = 1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index, 0, $index);
            $excel->writeLabel($index, 1, $value->booking_code);
            $excel->writeLabel($index, 2, $value->ticket_number);
            $excel->writeLabel($index, 3, $value->id_number);
            $excel->writeLabel($index, 4, $value->passanger_name);
            $excel->writeLabel($index, 5, $value->city);
            $excel->writeLabel($index, 6, $value->gender);
            $excel->writeLabel($index, 7, $value->age);
            $excel->writeLabel($index, 8, $value->service_name);
            $excel->writeLabel($index, 9, $value->passanger_type_name);
            $excel->writeLabel($index, 10, $value->ship_class_name);
            $excel->writeLabel($index, 11, $value->port_origin);
            $excel->writeLabel($index, 12, $value->port_destination);
            $excel->writeLabel($index, 13, $value->open_boarding_on);
            $excel->writeLabel($index, 14, $value->boarding_date);
            $excel->writeLabel($index, 15, $value->sail_date);

            $index++;
        }

        $excel->EOF();
        exit();
    }


    public function download_vehicle_05082021($encode)
    {

        $this->load->library('exceldownload');

        $code = $this->enc->decode($encode);
        $data = $this->boarding->list_detail_vehicle("where a.boarding_code='" . $code . "'  and  e.service_id=2 and b.status=1")->result();
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


        $index = 1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index, 0, $index);
            $excel->writeLabel($index, 1, $value->booking_code);
            $excel->writeLabel($index, 2, $value->ticket_number);
            $excel->writeLabel($index, 3, $value->plate_number);
            $excel->writeLabel($index, 4, $value->service_name);
            $excel->writeLabel($index, 5, $value->golongan);
            $excel->writeLabel($index, 6, $value->ship_class_name);
            $excel->writeLabel($index, 7, $value->port_origin);
            $excel->writeLabel($index, 8, $value->port_destination);
            $excel->writeLabel($index, 9, $value->created_on);
            $excel->writeLabel($index, 10, $value->sail_date);

            $index++;
        }

        $excel->EOF();
        exit();
    }

    public function download_vehicle_passanger_05082021($encode)
    {

        $this->load->library('exceldownload');

        $code = $this->enc->decode($encode);
        $data = $this->boarding->download_vehicle_passanger("where a.boarding_code='" . $code . "'  and  e.service_id=2 and b.status=1  and status<>'-5' ")->result();
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


        $index = 1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index, 0, $index);
            $excel->writeLabel($index, 1, $value->booking_code);
            $excel->writeLabel($index, 2, $value->ticket_number);
            $excel->writeLabel($index, 3, $value->plate_number);
            $excel->writeLabel($index, 4, $value->id_number);
            $excel->writeLabel($index, 5, $value->passanger_name);
            $excel->writeLabel($index, 6, $value->city);
            $excel->writeLabel($index, 7, $value->gender);
            $excel->writeLabel($index, 8, $value->age);
            $excel->writeLabel($index, 9, $value->service_name);
            $excel->writeLabel($index, 10, $value->passanger_type_name);
            $excel->writeLabel($index, 11, $value->ship_class_name);
            $excel->writeLabel($index, 12, $value->port_origin);
            $excel->writeLabel($index, 13, $value->port_destination);
            $excel->writeLabel($index, 14, $value->created_on);
            $excel->writeLabel($index, 15, $value->sail_date);

            $index++;
        }

        $excel->EOF();
        exit();
    }

    public function download_vehicle($encode)
    {

        $this->load->library('exceldownload');

        $code = $this->enc->decode($encode);
        $data = $this->boarding->list_detail_vehicle("where a.boarding_code='" . $code . "'  and  e.service_id=2 and b.status=1")->result();
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
        $excel->writeLabel(0, 10, "TANGGAL SCAN");
        $excel->writeLabel(0, 11, "TANGGAL KEBERANGKATAN");


        $index = 1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index, 0, $index);
            $excel->writeLabel($index, 1, $value->booking_code);
            $excel->writeLabel($index, 2, $value->ticket_number);
            $excel->writeLabel($index, 3, $value->plate_number);
            $excel->writeLabel($index, 4, $value->service_name);
            $excel->writeLabel($index, 5, $value->golongan);
            $excel->writeLabel($index, 6, $value->ship_class_name);
            $excel->writeLabel($index, 7, $value->port_origin);
            $excel->writeLabel($index, 8, $value->port_destination);
            $excel->writeLabel($index, 9, $value->open_boarding_date);
            $excel->writeLabel($index, 10, $value->boarding_date);
            $excel->writeLabel($index, 11, $value->sail_date);

            $index++;
        }

        $excel->EOF();
        exit();
    }

    public function download_vehicle_passanger($encode)
    {

        $this->load->library('exceldownload');

        $code = $this->enc->decode($encode);
        $data = $this->boarding->list_detail_passanger_vehicle("where a.boarding_code='" . $code . "'  and  e.service_id=2 and b.status=1 and c.status>=5  ")->result();
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
        $excel->writeLabel(0, 15, "TANGGAL SCAN");
        $excel->writeLabel(0, 16, "TANGGAL KEBERANGKAN");


        $index = 1;
        foreach ($data as $key => $value) {
            $excel->writeLabel($index, 0, $index);
            $excel->writeLabel($index, 1, $value->booking_code);
            $excel->writeLabel($index, 2, $value->ticket_number);
            $excel->writeLabel($index, 3, $value->plate_number);
            $excel->writeLabel($index, 4, $value->id_number);
            $excel->writeLabel($index, 5, $value->passanger_name);
            $excel->writeLabel($index, 6, $value->city);
            $excel->writeLabel($index, 7, $value->gender);
            $excel->writeLabel($index, 8, $value->age);
            $excel->writeLabel($index, 9, $value->service_name);
            $excel->writeLabel($index, 10, $value->passanger_type_name);
            $excel->writeLabel($index, 11, $value->ship_class_name);
            $excel->writeLabel($index, 12, $value->port_origin);
            $excel->writeLabel($index, 13, $value->port_destination);
            $excel->writeLabel($index, 14, $value->created_on);
            $excel->writeLabel($index, 15, $value->boarding_date);
            $excel->writeLabel($index, 16, $value->sail_date);

            $index++;
        }

        $excel->EOF();
        exit();
    }    

    function download_pdf_26052023()
    {
        $code = $this->enc->decode($this->input->get('boarding_code'));

        // $data['dewasa_l']=$this->boarding->get_sum_penumpang($code,"L","1")->row();
        // $data['dewasa_p']=$this->boarding->get_sum_penumpang($code,"P","1")->row();
        // $data['anak']=$this->boarding->get_sum_penumpang($code,"null","2")->row();

        // 1 dan 4 id dewasa dan lansia
        $data['dewasa_l'] = $this->boarding->get_sum_penumpang($code, "L", "1,4")->row();
        $data['dewasa_p'] = $this->boarding->get_sum_penumpang($code, "P", "1,4")->row();
        // $data['anak']=$this->syahbandar->get_sum_penumpang($code,"null","2")->row();
        $data['anak'] = $this->boarding->get_sum_anak($code, "2")->row();
        $data['bayi'] = $this->boarding->get_sum_anak($code, "3")->row();
        $data['a'] = $this->boarding->get_sum_vehicle($code)->result();


        $data['detail'] = $this->boarding->get_detail($code)->row();

        // mengihutung sum data pada total_passanger di trx_booking
        // $data['hitung_passanger_vehicle']=$this->boarding->total_dalam_kendaraan($code)->result();

        $data['hitung_passanger_vehicle'] = $this->boarding->get_detail_passanger_vehicle2($code)->result();

        $data['detail_passanger'] = $this->boarding->list_detail_passanger("where a.boarding_code='" . $code . "'  and  e.service_id=1 and b.status=1 ")->result();
        $data['detail_passanger_vehicle'] = $this->boarding->get_detail_passanger_vehicle($code)->result();


        $this->load->view('transaction/boarding/pdf', $data);
    }

    function download_pdf()
    {
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        $code=$this->enc->decode($this->input->get('boarding_code'));
        $n=$this->input->get('n');

        // 1 dan 4 id dewasa dan lansia
        $gender = $this->boarding->get_sum_penumpang($code,"1,4");
        $anak = $this->boarding->get_sum_penumpang($code,"2");
        $bayi = $this->boarding->get_sum_penumpang($code,"3");

        $data['dewasa_l']=(object)array("total_penumpang"=>$gender["L"]);
        $data['dewasa_p']=(object)array("total_penumpang"=>$gender["P"]);
        $data['anak']=(object)array("total_penumpang"=>$anak["total"]);
        $data['bayi']=(object)array("total_penumpang"=>$bayi["total"]);

        $data['a']=$this->boarding->get_sum_vehicle($code)->result();
        $data['detail']=$this->boarding->get_detail($code)->row();

        // mengihutung sum data pada total_passanger di trx_booking
        // $data['hitung_passanger_vehicle']=$this->boarding->total_dalam_kendaraan($code)->result();

        // menghitung mengambil data sesuai row booking passanger
        $data['hitung_passanger_vehicle']=$this->boarding->get_detail_passanger_vehicle2($code);
        $data['detail_passanger']=$this->boarding->list_detail_passanger("where a.boarding_code='".$code."'  and  e.service_id=1 and b.status=1" )->result();

        if ($n=='summary')
        {
            $data['detail_passanger_vehicle']=$this->boarding->get_detail_passanger_vehicle($code);

        }
        else
        {
            $data['detail_passanger_vehicle']=$data['hitung_passanger_vehicle'] ;           
        }

        // print_r($data['detail_passanger_vehicle']); exit;
        $this->load->view($this->_module.'/pdf',$data);
    }    

    public function download_excel()
    {

        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');

        $dateFrom = $this->input->get("dateFrom");
        $dateTo = $this->input->get("dateTo");

        $data = $this->boarding->download();

        // print_r($data); exit;
        $file_name = 'Kapal boarding tanggal ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $header = array(
            'NO' => 'string',
            'TANGGAL BOARDING' => 'string',
            'KODE BOARDING' => 'string',
            'TANGGAL JADWAL' => 'string',
            'KAPAL' => 'string',
            'PELABUHAN' => 'string',
            'DERMAGA' => 'string',
            'TUJUAN' => 'string',
            'TIPE KAPAL' => 'string',
            'JAM BERANGKAT' => 'string',
        );

        $no = 1;


        foreach ($data as $key => $value) {
            $rows[] = array(
                $no,
                $value->open_boarding_date,
                $value->boarding_code,
                $value->schedule_date,
                $value->ship_name,
                $value->port_name,
                $value->dock_name,
                $value->port_destination,
                $value->ship_class_name,
                empty($value->sail_date) ? "" : date("H:i:s", strtotime($value->sail_date)),
            );
            $no++;
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header, $styles1);

        foreach ($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }



    function approve()
    {
        $boarding_code = $this->enc->decode($this->input->post('code'));

        $data_boarding = $this->boarding->select_data("app.t_trx_open_boarding", "where boarding_code='" . $boarding_code . "' ")->row();

        $getname = $this->boarding->select_data("core.t_mtr_user", "where id='" . $this->session->userdata('id') . "' ")->row();

        $data = array(
            'name' => $getname->first_name . " " . $getname->last_name,
            'schedule_code' => $data_boarding->schedule_code,
            'boarding_code' => $data_boarding->boarding_code,
            'ship_id' => $data_boarding->ship_id,
            'status' => 1,
            'created_by' => $this->session->userdata("username"),
            'created_on' => date("Y-m-d H:i:s"),
        );

        // pengecekan apakah data sudah di lakukan close ramdoor
        // $check=$this->boarding->select_data("app.t_trx_close_ramp_door"," where schedule_code='".$data_boarding->schedule_code."'");

        // if($check->num_rows()<1)
        // {
        //     echo $res=json_api(0, 'Kapal belum tutup ramdor');
        // }
        // else
        // {
        //     $this->db->trans_begin();

        //     $this->boarding->insert_data("app.t_trx_approval_ship_officer",$data);

        //     // echo json_encode($data);

        //     if ($this->db->trans_status() === FALSE)
        //     {
        //         //gagal
        //         $this->db->trans_rollback();
        //         echo $res=json_api(0, 'gagal approve');
        //     }
        //     else
        //     {
        //         // berhasil
        //         $this->db->trans_commit();
        //         echo $res=json_api(1, 'Berhasil approve Kapal');
        //     }

        // }

        // ceck apalah data sudag di approve
        $ceck_approve = $this->boarding->select_data("app.t_trx_approval_ship_officer", " where boarding_code='{$boarding_code}' ");

        // jika sudah di approve pada saat modal di buka 2 tab bersamaan
        if ($ceck_approve->num_rows() > 0) {
            echo $res = json_api(1, 'Berhasil approve Kapal');
            exit;
        }

        $this->db->trans_begin();

        $this->boarding->insert_data("app.t_trx_approval_ship_officer", $data);

        // echo json_encode($data);

        if ($this->db->trans_status() === FALSE) {
            //gagal
            $this->db->trans_rollback();
            echo $res = json_api(0, 'gagal approve');
        } else {
            // berhasil
            $this->db->trans_commit();
            echo $res = json_api(1, 'Berhasil approve Kapal');
        }

    }

    public function send_manifest($type) {
        $boarding_code = $type == 1 ? $this->enc->decode($this->input->post('code')) : $this->input->post('code');
        $description = $this->input->post('desc');

         //Get data manifest for siwasops
         $manifest = $this->boarding->get_data_boarding($boarding_code);

         $identity = $this->boarding->identityApp();
         $url_send_manifest    = $identity['url_siwasops'];

         $user = $this->_login($manifest, $description);
         
         //Check status login true/false
         if($user->code == 200) {
             $token = $user->data->token;

             $custHeader = array(
                 "Authorization: Bearer $token"
             );
            
            //Retry hit to asdp max 2x
            for ($i=0; $i <= 2 ; $i++) { 
                $res = $this->restcurl->postSiwasops($url_send_manifest, $manifest, $custHeader);
                if ($res->code == 200) {
                    $this->boarding->saveLogSendSiwasops($manifest, $res, $description, 1);  
                    break;
                }
                $this->boarding->saveLogSendSiwasops($manifest, $res, $description, 0);
            }

            if ($res->code == 200) {
                echo $res = json_api(1, 'Berhasil send data manifest');
            } else {
                echo $res = json_api(0, 'Gagal send data manifest');
            }

        } else {
           echo $res = json_api(0, 'Gagal send data manifest');
        }
    }



    function _login($data, $desc)
    {
        $identity = $this->boarding->identityApp();
        $url_login    = $identity['url_login_siwasops'];

        $req = array(
            'email' => $identity['username_siwasops'],
            'password' => $identity['password_siwasops']
        );
        $created_by = 'system';

        $res = $this->restcurl->postLogin($url_login, $req);

        if ($res->code == 200) {
            $this->db->insert(
                'app.t_log_siwasops',
                array(
                    'boarding_code' => $data['params']['boarding_id'],
				    'boarding_date_start' => $data['params']['boarding_start'],
                    'ship_id' => $data['params']['kapal_id'],
                    'port_id' => $data['params']['pelabuhan_id'],
                    'dock_id' => $data['params']['dermaga_id'],
                    'request'    => json_encode($req),
                    'response'   => json_encode($res),
                    'type' => 1,
                    'status' => 1,
                    'created_by' => $created_by,
                    'description' => $desc,
                )
            );
        } else if ($res->code != 200) {
            $this->db->insert(
                'app.t_log_siwasops',
                array(
                    'boarding_code' => $data['params']['boarding_id'],
				    'boarding_date_start' => $data['params']['boarding_start'],
                    'ship_id' => $data['params']['kapal_id'],
                    'port_id' => $data['params']['pelabuhan_id'],
                    'dock_id' => $data['params']['dermaga_id'],
                    'request'    => json_encode($req),
                    'response'   => json_encode($res),
                    'type' => 1,
                    'status' => 0,
                    'created_by' => $created_by,
                    'description' => $desc,
                )
            );
        } else {
            $this->db->insert(
                'app.t_log_siwasops',
                array(
                    'boarding_code' => $data['params']['boarding_id'],
				    'boarding_date_start' => $data['params']['boarding_start'],
                    'ship_id' => $data['params']['kapal_id'],
                    'port_id' => $data['params']['pelabuhan_id'],
                    'dock_id' => $data['params']['dermaga_id'],
                    'request'    => json_encode($req),
                    'response'   => json_encode($res),
                    'type' => 1,
                    'status' => 0,
                    'created_by' => $created_by,
                    'description' => $desc,
                )
            );
        }

        return $res;
        exit;
    }

}
