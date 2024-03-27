<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Menu_rekonsiliasi extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('M_menu_rekonsiliasi','rekon');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('PHPExcel');


        $this->_table    = 'app.t_trx_invoice';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'laporan/menu_rekonsiliasi';

        // $this->dbView = $this->load->database("dbView", TRUE);
        $this->dbView = checkReplication();
        $this->dbAction = $this->load->database("dbAction", TRUE);        
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $merchant = $this->enc->decode($this->input->post('merchant'));
            $merchant_uname=$this->rekon->select_data("app.t_mtr_merchant","where merchant_id='".$merchant."'")->row();
            // print_r($merchant_uname->username);exit;
            if ($merchant_uname->username == 'alfamart'){
                $rows = $this->rekon->alfa_dataList();
            }
            else if ($merchant_uname->username == 'brilink') {
                $rows = $this->rekon->brilink_dataList();
            }
            else {
                echo "oii"; exit;
            }
            echo json_encode($rows);
            exit;
        }

        $get_identity=$this->rekon->get_identity_app();

        if($get_identity==0)
        {
            // mengambil port berdasarkan port di user menggunakan session
            if(!empty($this->session->userdata('port_id')))
            {
                $port=$this->rekon->select_data("app.t_mtr_port","where id=".$this->session->userdata('port_id')." ")->result();
                $row_port=1;
            }
            else
            {
                $port=$this->rekon->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result();
                $row_port=0;
            }
        }
        else
        {
            $port=$this->rekon->select_data("app.t_mtr_port","where id=".$get_identity." ")->result();
            $row_port=1;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Rekonsiliasi B2B',
            'content'  => 'menu_rekonsiliasi/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'service'  => $this->rekon->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'merchant'  => $this->rekon->select_data("app.t_mtr_merchant","where status=1 order by merchant_name asc")->result(),
            'import_excel' => generate_button_new($this->_module, 'import_excel',  site_url($this->_module.'/import_excel')),
            'status_type' => $this->rekon->select_data("app.t_mtr_status","where tbl_name = 't_trx_payment_b2b' AND status not in (-5) order by id ASC")->result(),
            'team' => $this->rekon->select_data("core.t_mtr_team","where status = 1")->result(),
            'btn_excel' => checkBtnAccess($this->_module,'download_excel'),
            // 'import'=>checkBtnAccess($this->_module,'import_excel'),
        );

		$this->load->view('default', $data);
	}

    function get_total()
    {
        $merchant = $this->enc->decode($this->input->post('merchant'));
        $merchant_uname=$this->rekon->select_data("app.t_mtr_merchant","where merchant_id='".$merchant."'")->row();
        // print_r($merchant_uname->username);exit;
        if ($merchant_uname->username == 'alfamart'){
            $rows = $this->rekon->alfa_total();
        }
        else if ($merchant_uname->username == 'brilink') {
            $rows = $this->rekon->brilink_total();
        }
        else {
            echo "oii"; exit;
        }
        echo json_encode($rows);
    }

    function get_status($merchant = "")
	{
		validate_ajax();
        $merchant = $this->enc->decode($merchant);
        $merchant_uname=$this->rekon->select_data("app.t_mtr_merchant","where merchant_id='".$merchant."'")->row();

		if (!$merchant) {
			$option = '<option value="" selected>Semua (Pilih Merchant terlebih dahulu)</option>';
			echo $option;
        }
        else if ($merchant_uname->username == 'brilink'){
            $data = $this->rekon->select_data("app.t_mtr_status","where tbl_name = 't_trx_recon_brilink' AND status not in (-5) order by id ASC")->result();
			$option = '<option value="" selected>Semua</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="' . $this->enc->encode($value->status) . '">' . $value->description . '</option>';
			}
			echo $option;
        }
        else{
			$data = $this->rekon->select_data("app.t_mtr_status","where tbl_name = 't_trx_recon_alfamart' AND status not in (-5) order by id ASC")->result();
			$option = '<option value="" selected>Semua</option>';
			foreach ($data as $key => $value) {
				$option .= '<option value="' . $this->enc->encode($value->status) . '">' . $value->description . '</option>';
			}
			echo $option;
        }
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
        
        $dateFrom = $this->input->get("dateFrom");
        $dateTo = $this->input->get("dateTo");
        $dateFrom2 = $this->input->get("dateFrom2");
        $dateTo2 = $this->input->get("dateTo2");
        $dateFrom3 = $this->input->get("dateFrom3");
        $dateTo3 = $this->input->get("dateTo3");
        $total = $this->input->get("total");
        $jumlah = $this->input->get("jumlah");
        $totaldibayar = $this->input->get("totaldibayar");
        $jumlahdibayar = $this->input->get("jumlahdibayar");
        $totalbelum = $this->input->get("totalbelum");
        $jumlahbelum = $this->input->get("jumlahbelum");
        $totalinves = $this->input->get("totalinves");
        $jumlahinves = $this->input->get("jumlahinves");

        $merchant = $this->enc->decode($this->input->get('merchant'));
        $merchant_uname=$this->rekon->select_data("app.t_mtr_merchant","where merchant_id='".$merchant."'")->row();

        if ($merchant_uname->username == 'alfamart'){
            $data = $this->rekon->alfa_download()->result();
        }
        else if ($merchant_uname->username == 'brilink') {
            $data = $this->rekon->brilink_download()->result();
        }

        $file_name = 'Rekonsiliasi '. $merchant_uname->merchant_name .' tanggal '.$dateFrom.' s/d '.$dateTo;
        $this->load->library('XLSExcel');
        $styles_ha = array('widths' => 50,'font'=>'Arial','font-size'=>12,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center');
        $styles1 = array( 'widths' => [5,20,20,20,20,20,20,20,20,20,20,20,20]);
        $styles2 = array('font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');
        $style_sub = array(
			'font' => 'Arial',
			'font-size' => 10,
			'font-style' => 'bold',
			'halign' => 'left',
			'valign' => 'left',
			// 'border' => 'left,right,top,bottom',
			// 'border-style' => 'thin',
		);
        $headertbl = array(
            'NO',
            'INVOICE NUMBER',
            'REF NO',
            'KODE BOOKING',
            'NO TIKET',
            'MITRA ID',
            'WAKTU TRANSAKSI',
            'WAKTU KEBERANGKATAN',
            'WAKTU SETTLEMENT',
            'ASAL',
            'TUJUAN',
            'LAYANAN',
            'JENIS PENGGUNA JASA',
            'GOLONGAN',
            'KODE TOKO',
            'NAMA TOKO',
            'STATUS',
            'TARIF PER JENIS',
            'ADMIN FEE',
            'DISKON',
            'TRANSFER ASDP',
            'KODE PROMO',
            'UPDATED SETTLEMENT',
        );
        $header = array("" => "string","" => "string","" => "string","" => "string","" => "string","" => "string","" => "string",
                        "" => "string","" => "string","" => "string","" => "string","" => "string","" => "string","" => "string",
                        "" => "string","" => "string","" => "string","" => "string","" => "string","" => "string","" => "string",
                        "" => "string","" => "string" );
        $no=1;

        foreach ($data as $key => $value) {
            $rows[] = array($no,
                            $value->id_trans,
                            $value->payment_code,
                            $value->booking_code,
                            $value->ticket_number,
                            $value->merchant_id,
                            $value->waktu_trans,
                            $value->depart_date,
                            $value->waktu_settle,
                            $value->asal,
                            $value->tujuan,
                            $value->ship_class,
                            $value->service,
                            $value->golongan,
                            $value->shop_code,
                            $value->shop_name,
                            $value->reconn_status,
                            $value->tarif_per_jenis,
                            $value->admin_fee,
                            $value->diskon,
                            $value->transfer_asdp,
                            $value->code_promo,
                            $value->updated_settlement,
                        );
            $no++;
        }

        $writer = new XLSXWriter();

        
        // $writer->writeSheetRow('Sheet1', array("LAPORAN PRODUKSI DAN PENDAPATAN TIKET TERPADU TERJUAL PER-SHIFT"), $styles_ha);
        // $writer->writeSheetRow('Sheet1', array(""));
        $writer->writeSheetHeader('Sheet1', $header,$styles1);
        $writer->writeSheetRow('Sheet1', array("", "TANGGAL SETTLEMENT", "", $dateFrom2, "S/D", $dateTo2), $style_sub);
        $writer->writeSheetRow('Sheet1', array("", "TANGGAL KEBERANGKATAN", "", $dateFrom3, "S/D", $dateTo3), $style_sub);
        if ($merchant_uname->username == 'brilink') {
            $writer->writeSheetRow('Sheet1', array("", "JUMLAH TRANSAKSI", $jumlah, "JUMLAH DIBAYAR", $jumlahdibayar, "JUMLAH BELUM DIBAYAR", "", $jumlahbelum, "JUMLAH INVESTIGASI", "", $jumlahinves), $style_sub);
            $writer->writeSheetRow('Sheet1', array("", "TOTAL TRANSAKSI", $total, "TOTAL DIBAYAR", $totaldibayar, "TOTAL BELUM DIBAYAR","", $totalbelum, "TOTAL INVESTIGASI","", $totalinves), $style_sub);
        }
        else {
            $writer->writeSheetRow('Sheet1', array("", "JUMLAH TRANSAKSI", $jumlah, "JUMLAH DIBAYAR", $jumlahdibayar, "JUMLAH BELUM DIBAYAR", "", $jumlahbelum), $style_sub);
            $writer->writeSheetRow('Sheet1', array("", "TOTAL TRANSAKSI", $total, "TOTAL DIBAYAR", $totaldibayar, "TOTAL BELUM DIBAYAR","", $totalbelum), $style_sub);
        }
        $writer->writeSheetRow('Sheet1', array(""));
        $writer->writeSheetRow('Sheet1', $headertbl,$styles2);
        
        foreach($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }


    function import_excel()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'import_excel');
        $data['title'] = 'Tambah Excel';
        $this->load->view($this->_module.'/import_excel',$data);
    }

    public function action_import_excel(){
    
        // validate_ajax();

          // load excel
        $file = $_FILES['excel']['tmp_name'];

          $load = PHPExcel_IOFactory::load($file);
          
          $max_row = $load->getActiveSheet(0)->getHighestRow()-7;

          $true=array();
          for ($i=0; $i < $max_row ; $i++) { 
                $true[]=true;
            }

          $sheets = $load->getActiveSheet()->toArray(null,true,true,true);

          $i = 1;
          $i2 = 1;

          $empty_data=array();
          $data=array();
        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'laporan/menu_rekonsiliasi/action_import_excel';
        $logMethod   = 'ADD';

        // $invalid_ship=array();
        // $invalid_port=array();
        // $invalid_dock=array();
        $invalid_trans=array();
        $invalid_trans_recon=array();
        $invalid_booking=array();
        $invalid_booking_trans=array();

        // $err_ship=array();
        // $err_port=array();
        // $err_dock=array();
        // $err_ship_class=array();
        $err_trans=array();
        $err_booking=array();
        $err_trans_recon=array();


        // check apakah proses waktunya lebih besar dari sebelumnya
        // foreach ($sheets as $value)
        // {
        //     // dimulai dari sheet 8
        //     if($i2>7)
        //     {
        //         // jika open boarding tidak kosong
        //         if (!empty($value['D']))
        //         {
        //             // checking waktu open boarding tidk boleh lebih besar dari docking
        //             if($value['C'] >= $value['D'])
        //             {
        //                 echo $res=json_api(0, "Waktu buka boarding tidak boleh diatas jam sandar, baris ke {$i2} kolom D");
        //                 $logParam    = json_encode($data);
        //                 $logResponse = $res;
        //                 $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        //                 exit;
        //             }
        //         }

        //         // jika close boarding tidak kosong
        //         if (!empty($value['E']))
        //         {
        //             // tidak boleh di atas docking
        //             if($value['C'] >= $value['E'])
        //             {
        //                 echo $res=json_api(0, "Waktu tutup boarding tidak boleh diatas jam sandar, baris ke {$i2} kolom E");
        //                 $logParam    = json_encode($data);
        //                 $logResponse = $res;
        //                 $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        //                 exit;
        //             }

        //             // jika open boarding keiisi
        //             if(!empty($value['D']))
        //             {
        //                 if($value['D']>= $value['E'])
        //                 {
        //                     echo $res=json_api(0, "Waktu tutup boarding tidak boleh diatas jam buka boarding baris ke {$i2} kolom E" );
        //                     $logParam    = json_encode($data);
        //                     $logResponse = $res;
        //                     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        //                     exit;
        //                 }
        //             }

        //         }

        //         // validasi jika tutup rampdor diisi
        //         if(!empty($value['F']))
        //         {
        //             if($value['C'] >= $value['F'])
        //             {
        //                 echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam sandar, baris ke {$i2} kolom F");
        //                 $logParam    = json_encode($data);
        //                 $logResponse = $res;
        //                 $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        //                 exit;
        //             }

        //             if(!empty($value['D']))
        //             {
        //                 if($value['D']>=$value['F'])
        //                 {
        //                     echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam buka boarding, , baris ke {$i2} kolom F");
        //                     $logParam    = json_encode($data);
        //                     $logResponse = $res;
        //                     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        //                     exit;
        //                 }
        //             }

        //             if(!empty($value['E']))
        //             {
        //                 if($value['E']>=$value['F'])
        //                 {
        //                     echo $res=json_api(0, "Waktu tutup ramdor tidak boleh diatas jam tutup boarding, , baris ke {$i2} kolom F");
        //                     $logParam    = json_encode($data);
        //                     $logResponse = $res;
        //                     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        //                     exit;
        //                 }
        //             }
        //         }


        //         // validasi jika tutup rampdor diisi
        //         if(!empty($value['G']))
        //         {
        //             if($value['C'] >= $value['G'])
        //             {
        //                 echo $res=json_api(0, "Waktu keberangkatan tidak boleh diatas jam sandar, baris ke {$i2} kolom G");
        //                 $logParam    = json_encode($data);
        //                 $logResponse = $res;
        //                 $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        //                 exit;
        //             }

        //             if(!empty($value['D']))
        //             {
        //                 if($value['D']>=$value['G'])
        //                 {
        //                     echo $res=json_api(0, "Waktu keberangkatan ramdor tidak boleh diatas jam buka boarding, baris ke {$i2} kolom G");
        //                     $logParam    = json_encode($data);
        //                     $logResponse = $res;
        //                     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        //                     exit;
        //                 }
        //             }

        //             if(!empty($value['E']))
        //             {
        //                 if($value['E']>=$value['G'])
        //                 {
        //                     echo $res=json_api(0, "Waktu keberangkatan ramdor tidak boleh diatas jam tutup boarding, baris ke {$i2} kolom G" );
        //                     $logParam    = json_encode($data);
        //                     $logResponse = $res;
        //                     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        //                     exit;
        //                 }
        //             }


        //             if(!empty($value['F']))
        //             {
        //                 if($value['F']>=$value['G'])
        //                 {
        //                     echo $res=json_api(0, "Waktu keberangkatan  tidak boleh diatas jam buka tutup ramdor, baris ke {$i2} kolom G");
        //                     $logParam    = json_encode($data);
        //                     $logResponse = $res;
        //                     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        //                     exit;
        //                 }
        //             }                        
        //         }

        //         // check jika nama kapal diisi dan dan nama kapal tidak sama di db maka akan di tolak
        //         if(!empty($value['H']))
        //         {
        //             $check_ship_data=$this->schedule->select_data("app.t_mtr_ship","where status=1 and upper(name)='".strtoupper(trim($value['H']))."'");
        //             if($check_ship_data->num_rows()<1)
        //             {
        //                 echo $res=json_api(0, "Nama Kapal {$value['H']} tidak ada, baris ke {$i2} kolom H");
        //                 $logParam    = json_encode($data);
        //                 $logResponse = $res;
        //                 $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        //                 exit;
        //             }
        //         }
        //     }
            
        //     $i2++;
        // }

        // check identity app nya 
        $identity=$this->rekon->get_identity_app();
        if($identity==0)
        {
            if(!empty($this->session->userdata("port_id")))
            {
                $identity_app=$this->rekon->select_data(" app.t_mtr_port" ," where id=".$this->session->userdata("port_id"))->row();

                $get_port_identity_name=$identity_app->name;

            }
            else
            {
                $identity_app="";
                $get_port_identity_name="";
            }
        }
        else
        {
            $identity_app=$identity_app=$this->rekon->select_data(" app.t_mtr_port" ," where id=".$identity)->row();
            $get_port_identity_name=$identity_app->name;
        }


        $check_identity[]=0;

        foreach ($sheets as $sheet) {

            // karena data yang di excel di mulai dari baris ke 8
            // maka jika $i lebih dari 1 data akan di masukan ke database
            $order_data=1;
            if ($i > 1) {

                // $check_ship=$this->schedule->select_data("app.t_mtr_ship","where status=1 and upper(name)='".strtoupper(trim($sheet['H']))."'");
                $check_trans=$this->rekon->select_data("app.t_trx_payment_b2b","where status=1 and upper(trans_number)='".strtoupper(trim($sheet['A']))."'");
                $check_trans_recon=$this->rekon->select_data("app.t_trx_recon_alfamart","where upper(trans_number)='".strtoupper(trim($sheet['A']))."'");
                $check_booking = $this->rekon->select_data("app.t_trx_booking","where upper(trans_number)='".strtoupper(trim($sheet['A']))."' and upper(booking_code)='".strtoupper(trim($sheet['C']))."'");

                // checking nama portnya apakah benar ada
                // $check_port=$this->schedule->select_data("app.t_mtr_port","where status=1 and  upper(name)='".trim(strtoupper($sheet['A']))."'");
                // $check_trans=$this->rekon->select_data("app.t_trx_payment_b2b","where status=1 and upper(trans_number)='".strtoupper(trim($sheet['H']))."'");

                // ketika mempunyai port id maka di check apakah sesuai pelabuhanya dengan pelabuhan yang di miliki user
                // if(!empty($identity_app))
                // {
                //     if( strtoupper($check_port->row()->name) != strtoupper($identity_app->name))
                //     {
                //         $check_identity[]=1;   
                //     }
                // }

                // $check_dock=$this->schedule->val_dock("where a.status=1 and upper(b.name)='".strtoupper(trim($sheet['A']))."' and upper(a.name)='".strtoupper(trim($sheet['B']))."'");

                // $port_id=$check_port->row();
                // $ship_id=$check_ship->row();
                // $dock_id=$check_dock->row();

                // $ship_class_id=$check_ship_class->row();


                // $data[]=array(
                //     'port_id'=>empty($port_id->id)?"":$port_id->id,
                //     'dock_id'=>empty($dock_id->id)?"":$dock_id->id,
                //     'docking_on'=>$sheet['C'],
                //     // 'ship_class'=>empty($ship_class_id->id)?"":$ship_class_id->id,
                //     'open_boarding_on'=>$sheet['D'],
                //     'close_boarding_on'=>$sheet['E'],
                //     'close_rampdoor_on'=>$sheet['F'],
                //     'sail_time'=>$sheet['G'],
                //     'ship_id'=>empty($ship_id->id)?"":$ship_id->id,
                //     'schedule_code'=>$this->createCode(empty($port_id->id)?"":$port_id->id),
                //     'schedule_date'=>$sheet['I'],
                //     'trip'=>$sheet['J'],
                //     'status'=>1,
                //     'created_on'=>date("Y-m-d H:i:s"),
                //     'created_by'=>$this->session->userdata('username'),
                //     );

                $data[]=array(
                    'trans_number'=>$sheet['A'],
                    'payment_code'=>$sheet['B'],
                    'kode_booking'=>$sheet['C'],
                    // 'ship_class'=>empty($ship_class_id->id)?"":$ship_class_id->id,
                    'jenis_pengguna_jasa'=>$sheet['D'],
                    'layanan'=>$sheet['E'],
                    'mitra_id'=>$sheet['F'],
                    'kode_toko'=>$sheet['G'],
                    'nama_toko'=>$sheet['H'],
                    'status'=>$sheet['I'],
                    'tanggal_transaksi'=>$sheet['J'],
                    'asal'=>$sheet['K'],
                    'tujuan'=>$sheet['L'],
                    'tanggal_keberangkatan'=>$sheet['M'],
                    'kode_promo'=>$sheet['N'],
                    'total_perinvoice'=>$sheet['O'],
                    'admin_fee'=>$sheet['P'],
                    'diskon'=>$sheet['Q'],
                    'fee_mitra'=>$sheet['R'],
                    'total_trans'=>$sheet['S'],
                    'transfer_asdp'=>$sheet['T'],
                    'settlement_date'=>$sheet['U'],
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    );


                // if(empty($sheet['A']) || empty($sheet['B']) || empty($sheet['C']) ||  empty($sheet['I']) || empty($sheet['J']) )
                // {
                //     $empty_data[]=1;   
                // }

                // else if($check_port->num_rows()<1)
                // {
                //     $invalid_port[]=$sheet['A'];
                //     $err_port[]=1;
                // }
                // else if($check_dock->num_rows()<1)
                // {
                //     $invalid_dock[]=$sheet['B']." di pelabuhan ".$sheet['A'];;
                //     $invalid_dock_port[]=$sheet['A'];
                //     $err_dock[]=1;
                // }
                if($check_trans->num_rows()<1)
                {
                    $invalid_trans[]=$sheet['A'];
                    $err_trans[]=1;
                }
                if($check_trans_recon->num_rows()>0){
                    $invalid_trans_recon[]=$sheet['A'];
                    $err_trans_recon[]=1;
                }
                if($check_booking->num_rows()<1){
                    $invalid_booking[]=$sheet['C'];
                    $invalid_booking_trans[]=$sheet['A'];
                    $err_booking[]=1;
                }
                $order_data++;
            }

            $i++;
        }

          // echo array_sum($err_ship_class);
          // exit;
          // print_r($data);
          // exit;

        // if(array_sum($empty_data)>0)
        // {
        //     echo $res=json_api(0, 'Data masih ada yang kosong');
        // }
        // else if (array_sum($check_identity)>0) 
        // {
        //     echo $res=json_api(0, ' Tidak bisa menambahkan jadwal lain, selain pelabuhan '.$get_port_identity_name);            
        // }
        // else if(array_sum($err_port)>0)
        // {
        //     echo $res=json_api(0, 'Nama pelabuhan '.implode(", ",array_unique($invalid_port)).' tidak ada');
        // }
        // else if(array_sum($err_dock)>0)
        // {
        //     echo $res=json_api(0, 'Nama dermaga '.implode(", ",array_unique($invalid_dock)).' tidak ada');
        // }
        if(array_sum($err_trans)>0)
        {
            echo $res=json_api(0, 'Nomer Transaksi '.implode(", ",array_unique($invalid_trans)).' tidak ada');
        }
        else if(array_sum($err_trans_recon)>0)
        {
            echo $res=json_api(0, 'Nomer Transaksi '.implode(", ",array_unique($invalid_trans_recon)).' sudah pernah rekonsiliasi');
        }
        else if(array_sum($err_booking)>0)
        {
            echo $res=json_api(0, 'Nomer Transaksi '.implode(", ",array_unique($invalid_booking_trans)).' dan Nomer Booking '.implode(", ",array_unique($invalid_booking)).' tidak sesuai');
        }
        else
        {
                $this->db->trans_begin();


             foreach ($data as $key => $value) 
             {
                 
                // $schedule=$value['schedule_date'];

                // $max=$this->db->query("select max(".'"order"'.") as max_order from app.t_mtr_schedule where schedule_date='".$schedule."' and port_id=".$value['port_id']." and dock_id=".$value['dock_id']." and status=1")->row();

                // $schedule_code=$this->createCode($value['port_id']);

                // ambil ship class berdasarkan docknya
                $status = "paid";
                $get_ship_class=$this->rekon->select_data("app.t_mtr_ship_class"," where (upper(name)  =upper('".$value['layanan']."') )")->row();
                $get_service=$this->rekon->select_data("app.t_mtr_service"," where (upper(name)  =upper('".$value['jenis_pengguna_jasa']."') )")->row();
                $get_origin=$this->rekon->select_data("app.t_mtr_port"," where (upper(name)  =upper('".$value['asal']."') )")->row();
                $get_destination=$this->rekon->select_data("app.t_mtr_port"," where (upper(name)  =upper('".$value['tujuan']."') )")->row();
                $get_status = $this->rekon->select_data("app.t_mtr_status"," where (upper(description)  =upper('".$status."')) and tbl_name = 't_trx_recon_alfamart'")->row();

                // empty($value['ship_id'])?$shipId=NULL:$shipId=$value['ship_id'];

                $data_row=array(
                'trans_number'=>$value['trans_number'],
                'payment_code'=>$value['payment_code'],
                'booking_code'=>$value['kode_booking'],
                'service_id'=>$get_service->id,
                'service_name'=>$value['jenis_pengguna_jasa'],
                'ship_class'=>$get_ship_class->id,
                'ship_class_name'=>$value['layanan'],
                'merchant_id'=>$value['mitra_id'],
                'shop_code'=>$value['kode_toko'],
                'shop_name'=>$value['nama_toko'],
                'status'=>$get_status->status,
                'status_name'=>$value['status'],
                'created_by'=>$this->session->userdata('username'),
                'created_on'=>date("Y-m-d H:i:s"),
                'time_trx'=>$value['tanggal_transaksi'],
                'origin'=>$get_origin->id,
                'origin_name'=>$value['asal'],
                'destination'=>$get_destination->id,
                'destination_name'=>$value['tujuan'],
                'depart_time'=>$value['tanggal_keberangkatan'],
                'code_promo'=>$value['kode_promo'],
                'total_invoice'=>$value['total_perinvoice'],
                'total_trans'=>$value['total_trans'],
                'admin_fee'=>$value['admin_fee'],
                'mitra_fee'=>$value['fee_mitra'],
                'diskon'=>$value['diskon'],
                'transfer_asdp'=>$value['transfer_asdp'],
                'settlement_date'=>$value['settlement_date'],
                );

                // mencari rute pelabuhan tujuan berdasarkan origin port 
                // $get_destiny=$this->schedule->select_data("app.t_mtr_rute"," where origin='".$value['port_id']."' ")->row();

                // $data_trx=array(
                //         'schedule_date'=>$value['schedule_date'],
                //         'port_id'=>$value['port_id'],
                //         'dock_id'=>$value['dock_id'],
                //         'destination_port_id'=> $get_destiny->destination,
                //         'ship_id'=>$shipId,
                //         'schedule_code'=>$schedule_code,
                //         'status'=>1,
                //         'created_on'=>date("Y-m-d H:i:s"),
                //         'created_by'=>$this->session->userdata("username"),
                // );


                // $this->db->insert($this->_table,$data_row);

                $this->db->insert("app.t_trx_recon_alfamart",$data_row);
                
                // echo json_encode($data_row);
             }

             // print_r($data_row);
             // exit;

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');
            }
        }

        // echo json_encode(
        //     array(
        //         'code'      => $res->code,
        //         'message'   => $res,
        //         'data'      => $data_row
        //     )
        // );

        $logParam    = json_encode($data);
        $logResponse = $res;
        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}