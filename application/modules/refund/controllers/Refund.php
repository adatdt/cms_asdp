<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Refund extends MY_Controller
{

	public function __construct(){
		parent::__construct();
        $this->load->library('Html2pdf');

		$this->load->model('refund_model');
        $this->_module   = 'refund';
        $this->_table   = 'app.t_trx_refund';
        $this->dbView=checkReplication();
	}

	public function index(){
		checkUrlAccess(uri_string(),'view');
		if($this->input->is_ajax_request()) {
			$rows = $this->refund_model->refundList();
			echo json_encode($rows);
			exit;
		}

        $appIdentity=$this->refund_model->select_data("app.t_mtr_identity_app","")->row();

        $dataPort=array();
        if($appIdentity->port_id==0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $dataPort[""]="Pilih";
                $port=$this->refund_model->select_data("app.t_mtr_port"," where status=1 order by name asc")->result();
            }
            else
            {
                $port=$this->refund_model->select_data("app.t_mtr_port"," where id=".$this->session->userdata("port_id"))->result();
            }
        }
        else
        {
            $port=$this->refund_model->select_data("app.t_mtr_port"," where id=".$appIdentity->port_id)->result();
        }


        foreach ($port as $key => $value) {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $approve                = generate_button($this->_module, 'change_status', '<button type="button" id="btn" class="btn btn-warning mt-ladda-btn ladda-button btn-sm download" data-style="zoom-in" id="btn">
                            <span class="ladda-label">APPROVED</span>
                            <span class="ladda-spinner"></span>
                        </button> ');        
        $downloadExcel          = generate_button($this->_module,'download_excel','<button id="downloadExcel" class="btn btn-warning btn-sm download" >EXCEL</button>');
        $downloadPdf            = generate_button($this->_module,'download_pdf','<button id="downloadPdf" class="btn btn-warning btn-sm download" >PDF</button>' );
        $refund_type            = $this->refund_model->select_data("app.t_mtr_refund_type"," where status=1 order by name asc")->result();
        $data_refund_type       = array();
        $data_refund_type[""]   = "Pilih";
        foreach($refund_type as $key => $value) {
            $data_refund_type[$this->enc->encode($value->id)] = strtoupper($value->name);
        }
        $ship_class             = $this->refund_model->select_data("app.t_mtr_ship_class"," where status = 1 order by name asc")->result();
        $data_ship_class       = array();
        $data_ship_class[""]   = "Pilih";
        foreach($ship_class as $key => $value) {
            $data_ship_class[$this->enc->encode($value->id)] = strtoupper($value->name);
        }


		$data = array(
			'home'          => 'Home',
            'url_home'      => site_url('home'),
            'parent'        => 'Booking Management',
            'url_parent'    => '#',
            'approve'       => $approve,
            'port'          => $dataPort,
            'status'        => $this->getStatus(),
            'approvedBy'    => $this->getApprovedBy(),
            'refund_type'   => $data_refund_type,
            'ship_class'    => $data_ship_class,
            'downloadExcel' => $downloadExcel,
            'downloadPdf'   => $downloadPdf,
			'title'         => 'Refund',
			'content'       => 'index',
		);

		$this->load->view('default', $data);
	}

	public function action_change($id_enc) {

		validate_ajax();
		$id=$this->enc->decode($id_enc);

		// jika statusnya 2 maka statusnya sudah transfer  
		$data= array( 'status'=>2,
					  'updated_on'=>date('Y-m-d H:i:s'),
					  'updated_by'=>$this->session->userdata('username')
					 );

        $this->db->trans_begin();

        $this->refund_model->update_data($this->_table,$data,"id=$id");

        if ($this->db->trans_status() === FALSE)
        {   
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal ubah status');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil ubah status');
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'refund/refund/action_change';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);					 		
	}

    public function detail($booking_code){
        checkUrlAccess($this->_module,'detail'); 

        $booking_code_decode = $this->enc->decode($booking_code);

        $get_detail         = $this->refund_model->get_detail($booking_code_decode)->row();
        $get_bukti_transfer = $this->refund_model->buktiTransfer($get_detail->refund_code)->row();

        $text="Tidak Ada Gambar";
        $img="<span class='label label-danger'>".$text."</span>";
        $stnk="<span class='label label-danger'>".$text."</span>";

        $decode=base64_decode($get_detail->id_image);
        $encode=base64_encode($decode);        

        if($get_detail->id_image==$encode)
        {
            if(!empty($get_detail->id_image))
            {
                $img='<img src="data:image/png;base64,'.$get_detail->id_image.'" alt="" width="500" height="auto" \ >';
            }
        }

        $imgHtml= '<div class="col-md-12 form-group"></div>
                
                <div class="col-md-12 form-group"><legend></legend></div>
                <div class="col-md-12 form-group my-content">
                    <p>KTP</p>
                    <center>
                        '.$img.'
                        <p id="errKtp"></p>
                    </center>

                </div>';

        if($get_detail->service_id==2)
        {

            $decode=base64_decode($get_detail->stnk_image);
            $encode=base64_encode($decode);
            if($get_detail->stnk_image==$encode)
            {
                if(!empty($get_detail->stnk_image))
                {
                    $stnk='<img src="data:image/png;base64,'.$get_detail->stnk_image.'" alt="" width="500" height="auto" \ >';
                }
            }

            $imgHtml .='<div class="col-md-12 form-group"></div>
                    
                    <div class="col-md-12 form-group"><legend></legend></div>
                    <div class="col-md-12 form-group my-content">
                        <p>STNK</p>
                        <center>
                            '.$stnk.'
                        </center>

                    </div>'  ;
        }

        if($get_bukti_transfer != null) {
            $decode_bukti=base64_decode($get_bukti_transfer->bukti_transfer);
            $encode_bukti=base64_encode($decode_bukti); 

            $encoded_string = ($encode_bukti);
            $imgdata = base64_decode($encoded_string);
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);       
    
            if($get_bukti_transfer->bukti_transfer==$encode_bukti)
            {
                if ($mime_type == 'application/pdf'){
                    
                    if(!empty($get_bukti_transfer->bukti_transfer))
                    {
                        $imgBukti='<embed src="data:application/pdf;base64,'.$get_bukti_transfer->bukti_transfer.'" width="800px" height="2100px" \ >';

                    }
                }else{
                     
                    if(!empty($get_bukti_transfer->bukti_transfer))
                    {
                        $imgBukti='<img src="data:image/png;base64,'.$get_bukti_transfer->bukti_transfer.'"alt="" width="500" height="auto" \ >';

                    }
                }

            }
        
        }else {
            $imgBukti= '<p>Tidak ada gambar</p>';
        }

        $imgBuktiTrf= '<div class="col-md-12 form-group"></div>
                
                <div class="col-md-12 form-group"><legend></legend></div>
                <div class="col-md-12 form-group my-content">
                    <p>Bukti Transfer</p>
                    <center>
                        '.$imgBukti.'
                        <p id="errKtp"></p>
                    </center>

                </div>';

        if($get_detail->bukti_nodin != null) {
            $ext = pathinfo($get_detail->bukti_nodin, PATHINFO_EXTENSION);
            if (strtoupper($ext) == 'PDF') {
                $imgBuktiNod='<embed src="'.$get_detail->bukti_nodin.'" width="800px" height="2100px" />';
            }
            else {
                $imgBuktiNod= '<a href="'.$get_detail->bukti_nodin.'"> Download File <br>'.$get_detail->bukti_nodin.'</a>';
            }
        }
        else {
            $imgBuktiNod= '<p>Tidak ada bukti nodin</p>';
        }

        // print_r($imgBuktiNod);exit;

        $imgBuktiNodin= '<div class="col-md-12 form-group"></div>
                
                <div class="col-md-12 form-group"><legend></legend></div>
                <div class="col-md-12 form-group my-content">
                    <p>Bukti Nodin</p>
                    <center>
                        '.$imgBuktiNod.'
                        <p id="errKtp"></p>
                    </center>

                </div>';

        $dataHistory=array();
        $getHistory=$this->refund_model->historyTransfer($booking_code_decode);

        if($getHistory)
        {
            $dataHistory=$getHistory;
        }

        $data = array(
            'home'              => 'Home',
            'url_home'          => site_url('home'),
            'parent1'           => 'Booking Management',
            'url_parent1'       => '#',
            'parent2'           => 'Refund',
            'url_parent2'       => site_url('refund'),
            'title'             => 'Detail Refund',
            // 'title'             => 'Form Pengajuan',
            'content'           => 'detail',
            'img'               => $img ,
            'stnk'              => $stnk ,
            'imgHtml'           => $imgHtml ,
            'booking_enc'       => $booking_code,
            'historyTransfer'   => $dataHistory,
            'imgBuktiTrf'       => $imgBuktiTrf,
            'imgBuktiNodin'     => $imgBuktiNodin,
            'detail'            => $get_detail
        );

        // print_r($dataHistory); exit;

        $this->load->view('detail',$data);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $idDecode=$this->enc->decode($id);
        $transfered=$this->enc->encode(2);
        $failedTransfered=$this->enc->encode(3);
        $transfer=array(""=>"Pilih",$transfered=>"Transfer",$failedTransfered=>"Gagal Transfer");

        $getDetail=$this->refund_model->select_data($this->_table," where id='{$idDecode}' ")->row();

        // memilih transfered
        if($getDetail->status==3)
        {
            $selectedStatusTransfer=$failedTransfered;   
        }
        else if($getDetail->status==2)
        {
            $selectedStatusTransfer=$transfered;
        }
        else
        {
            $selectedStatusTransfer="";   
        }

        $config_param = get_config_param('reservation');

        $dock_id=$this->enc->decode($id);
        $data['title'] = 'Edit Refund';
        $data['id'] = $id;
        $data['selectedTransfer'] = $selectedStatusTransfer;
        $data['detail'] = $getDetail;
        $data['transfer'] = $transfer;
        $data['max_size'] = $config_param['max_file_size_refund'] * 1024;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $this->load->helper('phpmailer');

        // print_r($this->input->post());exit;
        $bookingCode            = trim($this->input->post('bookingCode'));
        $refundCode             = trim($this->input->post('refundCode'));
        $name                   = trim($this->input->post('name'));
        $accountNumber          = trim($this->input->post('accountNumber'));
        $bankName               = trim($this->input->post('bankName'));
        $transfer               = $this->enc->decode(trim($this->input->post('transfer')));
        $transferDescription    = trim($this->input->post('transferDescription'));
        $id                     = trim($this->enc->decode($this->input->post('id')));
       
        $path= $_FILES['buktiTransfer']['name'];
        $type_file = pathinfo($path, PATHINFO_EXTENSION);
        $size_file=$_FILES['buktiTransfer']['size'];
        // print_r($path);exit;


        $this->form_validation->set_rules('bookingCode', 'Nomer Booking', 'required');
        $this->form_validation->set_rules('refundCode', 'Nomer Refund', 'required');
        $this->form_validation->set_rules('name', 'Nama', 'required');
        $this->form_validation->set_rules('accountNumber', 'Nomer Rekening', 'required');
        $this->form_validation->set_rules('transfer', 'Transfer', 'required');
        $this->form_validation->set_rules('transferDescription', 'Keterangan Transfer', 'required');

        $erorr_size_pdf[]=0;

        if ($transfer == 2) {
            // $this->form_validation->set_rules('buktiTransfer', 'Bukti Transfer', 'required');
            // $bukti_trf_image        = $this->validate_image($req, $buktiTrf, 'buktitrf'); //convert image to base64
            if($type_file == 'pdf')
            {
                if($size_file > 200000  ){
                    
                $erorr_size_pdf[]=1;
                }
                
                $image= base64_encode(file_get_contents($_FILES["buktiTransfer"]["tmp_name"]));

            }else{ //untuk upload type gambar
                $nama = str_replace(".", "(dot)", $refundCode) . '_bukti_transfer.jpeg';
                $fileName = str_replace(" ", "_", $nama);
                $location = 'assets/img/tmp/' . $fileName;
                $img_tmp = base_url($location);
                $image = base64_encode(file_get_contents($img_tmp));
                }
        }

          // print_r($image);exit;

        $this->form_validation->set_message('required','%s harus diisi!');

        $getBooking=$this->refund_model->select_data("app.t_trx_booking", "where booking_code='{$bookingCode}'")->row();
        // $getBooking->refund_code=$refundCode;

        // $dataDetail['booking']=$getBooking;
        
        $getEmailCust=$this->refund_model->select_data("app.t_trx_invoice"," where trans_number='{$getBooking->trans_number}'")->row();
        
        $getRefundDetail=$this->refund_model->getDetailEmail($bookingCode)->row();
        $getRefundDetail->amount=idr_currency($getRefundDetail->amount);
        $getRefundDetail->extra_fee=idr_currency($getRefundDetail->extra_fee);
        $getRefundDetail->total_amount=idr_currency($getRefundDetail->total_amount);
        $getRefundDetail->charge_amount=idr_currency($getRefundDetail->charge_amount);
        $getRefundDetail->transfer_description=$transferDescription;

        if($transfer==2)
        {
            $getRefundDetail->statusRefund=success_label("<b>Refund Berhasil</b>");
            $getRefundDetail->myStatus="Refund Berhasil";
            $getRefundDetail->linkFailed="";
        }
        else
        {

            $getCustomParam=$this->refund_model->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('refund_url_failed') ")->row();
            $getRefundDetail->statusRefund=failed_label("<b>Refund Gagal</b>");
            $getRefundDetail->myStatus="Refund Gagal";
            $getRefundDetail->linkFailed="
                                            <tr>
                                                <td>Link</td>
                                                <td>: <a href='".$getCustomParam->param_value."".$bookingCode."' >Klik Disini</a> </td>
                                            </tr>";
        }

        // print_r($getRefundDetail); exit;

        $disclaimer=$this->refund_model->select_data("app.t_mtr_info", " where name='termsandconditions_success_payment' ")->row(); // wher hardcord

        $dataDetailEmail['detailRefund']=$getRefundDetail;
        $dataDetailEmail['disclaimer']=$disclaimer->info;


        $checkBokingRefund=$this->refund_model->select_data($this->_table," where refund_code='{$refundCode}' and booking_code='{$bookingCode}' ");

        $getDataRefund=$this->refund_model->select_data($this->_table," where id={$id} ")->row();

        if($transfer==2)
        {

            $subject="Refund Berhasil";
            // $sendto="adatdt@gmail.com";
            // $sendto="riandi.nutech@gmail.com";
            // $sendto=strtoupper($getDataRefund->channel)=='WEB_CS'?$getDataRefund->email:$getEmailCust->email;
            $sendto=!empty($getDataRefund->email)?$getDataRefund->email:$getEmailCust->email;
            $content=$this->load->view($this->_module.'/emailSuccess',$dataDetailEmail,TRUE);
        }
        else
        {
            

            if(strtoupper($getDataRefund->channel)=='WEB_CS') //jika gagal refund email lewat cs
            {

                $subject="Refund Gagal";
                // $sendto="adatdt@gmail.com";
                // $sendto="riandi.nutech@gmail.com";

                $sendto=$getDataRefund->email; 
                $linkUpload=$this->generatePolaLinkUpload($getDataRefund->refund_code, $getDataRefund->booking_code);

                $getUrl=$this->refund_model->select_data("app.t_mtr_custom_param"," where  param_name='link_update_file_refund' and status<>'-5'")->row();

            

                $dataRevisi=array(
                    'cust_name'     =>$getDataRefund->name,
                    'booking_code'  =>$getDataRefund->booking_code,
                    'refund_code'   =>$getDataRefund->refund_code,
                    'linkUpload'    =>$getUrl->param_value.$linkUpload,
                    'komentar'      => $transferDescription,

                );

                $content=$this->load->view($this->_module.'/emailRevisiCs',$dataRevisi,TRUE);

                // echo $linkUpload;
                // exit;
            }
            else // jika gagal refund email kepada email member
            {            
                $subject="Refund Gagal";
                // $sendto="adatdt@gmail.com";
                // $sendto="riandi.nutech@gmail.com";
                $sendto=!empty($getDataRefund->email)?$getDataRefund->email:$getEmailCust->email;
                // $content=$this->load->view($this->_module.'/emailSuccess',$dataDetailEmail,TRUE);


                $dataRevisi=array(
                    'cust_name'     =>$getDataRefund->name,
                    'booking_code'  =>$getDataRefund->booking_code,
                    'refund_code'   =>$getDataRefund->refund_code,
                    'linkUpload'    =>$getCustomParam->param_value.$bookingCode,
                    'komentar'      => $transferDescription,
                );

                $content=$this->load->view($this->_module.'/emailRevisiCs',$dataRevisi,TRUE);
            }
        }



        $validationApproveKeuangan = $this->refund_model->select_data('app.t_log_refund_transfer', "WHERE refund_code = '" . $refundCode . "' and transfer_status = 3");

        $updateData=array(
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata("username"),
            'transfer_status'=>$transfer,
            'status'=>$transfer, 
            'transfer_description'=>$transferDescription,
        );

        if ($validationApproveKeuangan->num_rows() < 1) {
            $updateData=array(
                'updated_on'=>date("Y-m-d H:i:s"),
                'updated_by'=>$this->session->userdata("username"),
                'approved_by_keuangan'=>$this->session->userdata("username"),
                'transfer_status'=>$transfer,
                'status'=>$transfer, 
                'transfer_description'=>$transferDescription,
            );
        }

        $insertLog=array(
            'refund_code'=>$refundCode,
            'booking_code'=>$bookingCode,
            'transfer_status'=>$transfer,
            'transfer_description'=>$transferDescription,
            'status'=>1,
            'created_on'=>date("Y-m-d H:i:s"),
            'created_by'=>$this->session->userdata("username"),
        );


        if ($transfer == 2) {
            $insertBuktiTransfer=array(
                'refund_code'=> $refundCode,
                'bukti_transfer'=> $image,
                'status'=>1,
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata("username"),
            );
        }

        $data=array("Update_refund"=>$updateData,"insert_log"=>$insertLog,);


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($checkBokingRefund->num_rows()<1 )
        {
            echo $res=json_api(0,"Kode Booking dan Refund tidak ditemukan");      
        }
        else if(array_sum($erorr_size_pdf)>0)
        {
             echo $res=json_api(0,"File harus berupa Gambar atau PDF!"); 
        }
        else
        {

            $dataEmail=array("recipient"=>$sendto,
                            "subject"=>$subject,
                            "body"=>$content,
                            );

            $this->db->trans_begin();

            $this->refund_model->update_data($this->_table,$updateData,"id=$id");
            $this->refund_model->insert_data("app.t_log_refund_transfer",$insertLog);
            if ($transfer == 2) {
                $this->refund_model->insert_data("app.t_trx_refund_bukti_transfer",$insertBuktiTransfer);
            }

            $sendEmail=mailSend($subject, $sendto, $content);

            $sendEmail?$dataEmail['status']=1:$dataEmail['status']=0;
            $this->refund_model->insert_data("core.t_trx_email",$dataEmail);   


            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();
                if ($transfer == 2) {
                    @unlink($location);
                }
                echo $res=json_api(1, 'Berhasil Edit data');                
            }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'refund/refund/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function download_pdf_detail($booking_enc)
    {
        $booking_dec=$this->enc->decode($booking_enc);

        $get_detail=$this->refund_model->get_detail($booking_dec)->row();

        $imgHead ="";        
        $imgKtp ="";
        $imgStnk="";

        // echo $get_detail->id_image; exit; 

        $decode=base64_decode($get_detail->id_image);
        $encode=base64_encode($decode);

        if ($get_detail->id_image==$encode) // pengecehkan base 64
        {
            if(!empty($get_detail->id_image)) // pengecheckan jika datanya tidak kosong
            {
              $imgKtp .= '                   
                <p><hr />KTP</p>                          
                <p style="text-align: center;">
                    <img src="data:image/png;base64,'.$get_detail->id_image.'" alt="" width="400" height="auto" >
                </p>

                ';
            }
        }


        if($get_detail->service_id==2) // jika servinya kendaraan
        {

            // if($this->is_base64($get_detail->stnk_image))
            // {

                $decode=base64_decode($get_detail->stnk_image);
                $encode=base64_encode($decode);

                if ($get_detail->stnk_image==$encode) // pengecehkan base 64
                {
                    if(!empty($get_detail->stnk_image)) // pengecheckan jika datanya tidak kosong
                    {
                        $imgStnk .= '                   
                            <div style="page-break-after:always; clear:both"></div>
                            <p><hr />STNK</p>                          
                            <p style="text-align: center;">
                                <img src="data:image/png;base64,'.$get_detail->stnk_image.'" alt="" width="400" height="auto"  >
                            </p>

                            ';    
                    }
                
                }
            // }
        }

        if(!empty($imgStnk) or !empty($imgKtp))
        {
             $imgHead .= '<div style="page-break-after:always; clear:both"></div>';
        }

        $img =$imgHead." ".$imgKtp." ".$imgStnk;

        $data['detail']=$get_detail;
        $data['img']=$img;



        $this->load->view('pdf',$data);
    }

    function downloadExcelGrid()
    {
        // $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '8190M'); // 8 GBs minus 1 MB

        $dateTo=$this->input->get("dateTo");
        $dateFrom=$this->input->get("dateFrom");

        $data=$this->refund_model->download();

        $file_name = 'Booking Refund ' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $styleHeader = array(
            'height' => 50,
            'widths' => [5, 40, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20],
            'font' => 'Arial',
            'font-size' => 10,
            'font-style' => 'bold',
            'valign' => 'center',
            'halign' => 'center',
            'border' => 'left,right,top,bottom',
            'border-style' => 'thin'
        );
        
        $headertbl1= array(
            "NO",
            "KODE BOOKING",
            "NAMA",
            "NO HP",
            "TANGGAL REFUND",
            "KODE REFUND",
            "JENIS REFUND",
            "ASAL",
            "TUJUAN",
            "LAYANAN",
            "JENIS PENGGUNA JASA",
            "NO POLISI KENDARAAN",
            "GOLONGAN",
            "NO. REKENING",
            "NAMA PEMILIK REKENING",
            "BANK",
            "HARGA TIKET",
            "BIAYA ADMINISTRASI",
            "BIAYA REFUND",
            "BIAYA TRANSFER",
            "JUMLAH PEMOTONGAN BIAYA",
            "PENGEMBALIAN DANA",
            "STATUS REFUND",
            "PROSES APROVAL CONTACT CENTER/CUSTOMER SERVICE",
            "",
            "",
            "",
            "",
            "",
            "PROSES APROVAL DIVISI USAHA",
            "",
            "",
            "",
            "",
            "",
            "PROSES APROVAL DIVISI KEUANGAN",
            "",
            "",
            "",
            "",
            "",
            "SLA PENYELESAIAN",
            "",
        );

        $headertbl2 = array(
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "STATUS",
            "USER",
            "TANGGAL",
            "SLA (HARI KERJA)",
            "KETERANGAN",
            "CATATAN",
            "STATUS",
            "USER",
            "TANGGAL",
            "SLA (HARI KERJA)",
            "KETERANGAN",
            "CATATAN",
            "STATUS",
            "USER",
            "TANGGAL",
            "SLA (HARI KERJA)",
            "KETERANGAN",
            "CATATAN",
            "DURASI SLA (HARI KALENDER)",
            "KETERANGAN"
        );


        $header = array("LAPORAN REFUND TANGGAL ".  $dateFrom . " s/d ". $dateTo => "string","" => "string","" => "string","" => "string","" => "string","" => "string","" => "string",
                        "" => "string","" => "string","" => "string","" => "string","" => "string","" => "string","" => "string",
                        "" => "string","" => "string","" => "string","" => "string","" => "string","" => "string","" => "string",
                        "" => "string","" => "string" );

        $no = 1;
        foreach ($data as $key => $value) {


            $rows[] = array(
                $no,
                $value->booking_code,
                $value->name,
                $value->phone,
                $value->created_on,
                $value->refund_code,
                $value->refund_type,
                $value->asal,
                $value->tujuan,
                $value->layanan,
                $value->jenis_pj,
                $value->id_number,
                $value->golongan,
                $value->account_number,
                $value->account_name,
                $value->bank,
                $value->amount,
                $value->adm_fee,
                $value->refund_fee,
                $value->bank_transfer_fee,
                $value->jumlah_potongan,
                $value->dana_pengembalian,
                $value->status_refund,
                $value->approved_status_cs,
                $value->approved_by_cs,
                $value->approved_on_cs,
                $value->sla_cs,
                $value->keterangan_cs,
                $value->catatan_cs,
                $value->status_approved,
                $value->approved_by,
                $value->approved_on,
                $value->sla_usaha,
                $value->keterangan_usaha,
                $value->catatan_usaha,
                $value->status_keuangan,
                $value->approved_by_keuangan,
                $value->approved_on_keuangan,
                $value->sla_keuangan,
                $value->keterangan_keuangan,
                $value->catatan_keuangan,
                $value->durasi,
                $value->keterangan,
            );
            $no++;
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header, $styles1);
        $writer->writeSheetRow('Sheet1', $headertbl1,$styleHeader);
        $writer->writeSheetRow('Sheet1', $headertbl2,$styleHeader);
        $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 42);
        for ($i = 0; $i<23; $i++) {
            $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = $i, $end_row = 2, $end_col = $i);
        }
        $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 23, $end_row = 1, $end_col = 28);
        $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 29, $end_row = 1, $end_col = 34);
        $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 35, $end_row = 1, $end_col = 40);
        $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 41, $end_row = 1, $end_col = 42);

        foreach ($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();        
    }    

    function downloadPdf()
    {
        // $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '8190M'); // 8 GBs minus 1 MB

        $dateTo=$this->input->get("dateTo");
        $dateFrom=$this->input->get("dateFrom");

        $data['data']=$this->refund_model->download();
        $data['dateTo']=$dateTo;
        $data['dateFrom']=$dateFrom;

        $this->load->view('pdfGrid',$data);   
    }        


    function is_base64($string)  //check base 64 encode 
    {
          // Check if there is no invalid character in string
          if (!preg_match('/^(?:[data]{4}:(text|image|application)\/[a-z]*)/', $string)){
            return false;
          }else{
            return true;
          }


    }


    function is_base642($string)  //check base 64 encode 
    {
          // Check if there is no invalid character in string
          if (!preg_match('/^(?:[data]{4}:(image)\/[a-z]*)/', $string)){
            return 0;
          }else{
            return 1;
          }

    }

    public function approve()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        $id=$this->input->post("idApprove");
        // print_r($id);exit;

        // $idDecode=$this->enc->decode($id);

        // $getDetail=$this->refund_model->select_data($this->_table," where id='{$idDecode}' ")->row();


        $data['title'] = 'Approve Refund';
        $data['id'] = $id;

        $this->load->view($this->_module.'/approve',$data);   
    }

    function actionApprove()
    {
        // print_r($_FILES);
        // print_r($this->input->post());exit;
        $id=$this->input->post("id[]");
        $newName = time().'_'.$_FILES['buktiNodin']['name'];
        $dir     = './uploads/bukti_nodin';

        $config['upload_path']          = $dir;
        $config['allowed_types']        = 'pdf|xlsx|xls';
        $config['file_name']            = $newName;

        $this->load->library('upload', $config);

        // $fileName = str_replace(" ", "_", $newName);
        // $path = $dir .'/'. $fileName;
        $baseName = str_replace(" ", "_", pathinfo($newName, PATHINFO_FILENAME));
        $fileName = str_replace(".", "_", $baseName);
        $path = $dir .'/'. $fileName .'.'. pathinfo($newName, PATHINFO_EXTENSION);

        $dataUpdate=array(
            "updated_by"=> $this->session->userdata("username"),
            "updated_on"=> date("Y-m-d H:i:s"),
            "approved_on"=> date("Y-m-d H:i:s"),
            "is_approved"=> true,
            "bukti_nodin"=> $path
        );
        
        
        $data=$dataUpdate;
        
        if(empty($id))
        {
            $res=array("code"=>0,"message"=>"Tidak ada Data yang Di pilih");
        }
        else
        {
            
            if (!$this->upload->do_upload('buktiNodin')) {
                $error = array('error' => $this->upload->display_errors());
                // print_r($error);
                echo $res = json_api(0, $error['error']);
            } else {
                $this->db->trans_begin();
                $data=array();
                foreach ($id as $key => $value) {
                    
                    $idDecode = $this->enc->decode($value);
                    $dataUpdate['approved_by']=$this->session->userdata("username");
                    $this->refund_model->update_data("app.t_trx_refund",$dataUpdate,"id=".$this->enc->decode($value));

                    $data_refund = $this->refund_model->select_data($this->_table," where id='{$idDecode}' ")->row();
                    $insertLog=array(
                        'refund_code'=> $data_refund->refund_code,
                        'booking_code'=> $data_refund->booking_code,
                        'transfer_status'=> 11,
                        'transfer_description'=> '-',
                        'status'=> 1,
                        'created_on'=> date("Y-m-d H:i:s"),
                        'created_by'=> $this->session->userdata("username"),                        
                    );
                    
                    $this->refund_model->insert_data("app.t_log_refund_transfer",$insertLog);
                    // print_r($insertLog);
                    $data[]=$dataUpdate;

                }

                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                    $res=array("code"=>0,"message"=>"Gagal tambah data");
                }
                else
                {
                    $this->db->trans_commit();
                    $res=array("code"=>1,"message"=>"Berhasil tambah data");
                }
            }
        }

        // print_r($data); exit;

        echo json_encode($res);

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'refund/refund/action_approve';
        $logMethod   = 'APROVED';
        $logParam    = json_encode($data);
        $logResponse = json_encode($res);

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    function komentarUsaha($id) {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $idDecode=$this->enc->decode($id);

        $getDetail=$this->refund_model->select_data($this->_table," where id='{$idDecode}' ")->row();


        $data['title'] = 'Revisi Manager Usaha';
        $data['id'] = $id;
        $data['detail'] = $getDetail;

        $this->load->view($this->_module.'/revisi_usaha',$data);   
    }

    function actionKomentarUsaha() {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $this->load->helper('phpmailer');

        $bookingCode            = trim($this->input->post('booking_code'));
        $refundCode             = trim($this->input->post('refund_code'));
        $komentarUsaha          = trim($this->input->post('komentar_usaha'));
        $id                     = trim($this->enc->decode($this->input->post('id')));

        $this->form_validation->set_rules('booking_code', 'Nomer Booking', 'required');
        $this->form_validation->set_rules('refund_code', 'Nomer Refund', 'required');
        $this->form_validation->set_rules('komentar_usaha', 'Revisi', 'required');



        $this->form_validation->set_message('required','%s harus diisi!');

        $getBooking=$this->refund_model->select_data("app.t_trx_booking", "where booking_code='{$bookingCode}'")->row();
        
        $getEmailCust=$this->refund_model->select_data("app.t_trx_invoice"," where trans_number='{$getBooking->trans_number}'")->row();
        
        $getRefundDetail                = $this->refund_model->getDetailEmail($bookingCode)->row();
        $getRefundDetail->amount        = idr_currency($getRefundDetail->amount);
        $getRefundDetail->extra_fee     = idr_currency($getRefundDetail->extra_fee);
        $getRefundDetail->total_amount  = idr_currency($getRefundDetail->total_amount);
        $getRefundDetail->charge_amount = idr_currency($getRefundDetail->charge_amount);


        $getCustomParam                 = $this->refund_model->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('refund_url_failed') ")->row();
        $getRefundDetail->statusRefund  = failed_label("<b>Refund Gagal</b>");
        $getRefundDetail->myStatus      = "Refund Gagal";
        $getRefundDetail->linkFailed    = "
                                        <tr>
                                            <td>Link</td>
                                            <td>: <a href='".$getCustomParam->param_value."".$bookingCode."' >Klik Disini</a> </td>
                                        </tr>";

        // print_r($getRefundDetail); exit;

        $disclaimer                     = $this->refund_model->select_data("app.t_mtr_info", " where name='termsandconditions_success_payment' ")->row(); // wher hardcord

        $dataDetailEmail['detailRefund']= $getRefundDetail;
        $dataDetailEmail['disclaimer']  = $disclaimer->info;


        $checkBokingRefund              =$this->refund_model->select_data($this->_table," where refund_code='{$refundCode}' and booking_code='{$bookingCode}' ");

        $getDataRefund=$this->refund_model->select_data($this->_table," where id={$id} ")->row();      

        if(strtoupper($getDataRefund->channel)=='WEB_CS') //jika gagal refund email lewat cs
        {

            $subject        = "Refund Gagal";
            // $sendto="adatdt@gmail.com";
            // $sendto="riandi.nutech@gmail.com";

            $sendto         = $getDataRefund->email; 
            $linkUpload     = $this->generatePolaLinkUpload($getDataRefund->refund_code, $getDataRefund->booking_code);

            $getUrl=$this->refund_model->select_data("app.t_mtr_custom_param"," where  param_name='link_update_file_refund' and status<>'-5'")->row();

        

            $dataRevisi=array(
                'cust_name'     => $getDataRefund->name,
                'booking_code'  => $getDataRefund->booking_code,
                'refund_code'   => $getDataRefund->refund_code,
                'linkUpload'    => $getUrl->param_value.$linkUpload,
                'komentar'      => $komentarUsaha,
            );

            $content=$this->load->view($this->_module.'/emailRevisiCs',$dataRevisi,TRUE);

            // echo $linkUpload;
            // exit;
        }
        else // jika gagal refund email kepada email member
        {            
            $subject    = "Refund Gagal";
            // $sendto="adatdt@gmail.com";
            // $sendto     = "ihaab.nutech@gmail.com";
            $sendto     = !empty($getDataRefund->email)?$getDataRefund->email:$getEmailCust->email;
            // $content    = $this->load->view($this->_module.'/emailSuccess',$dataDetailEmail,TRUE);
            $dataRevisi=array(
                'cust_name'     => $getDataRefund->name,
                'booking_code'  => $getDataRefund->booking_code,
                'refund_code'   => $getDataRefund->refund_code,
                'linkUpload'    => $getCustomParam->param_value . $bookingCode,
                'komentar'      => $komentarUsaha,
            );

            $content=$this->load->view($this->_module.'/emailRevisiCs',$dataRevisi,TRUE);
        }

        $updateData=array(
            'updated_on'=> date("Y-m-d H:i:s"),
            'updated_by'=> $this->session->userdata("username"),
            'status'=> 7
        );

        $insertLog=array(
            'refund_code'=> $refundCode,
            'booking_code'=> $bookingCode,
            'transfer_status'=> 7,
            'transfer_description'=> $komentarUsaha,
            'status'=> 1,
            'created_on'=> date("Y-m-d H:i:s"),
            'created_by'=> $this->session->userdata("username"),
        );



        $data=array("Update_refund"=>$updateData,"insert_log"=>$insertLog,);
        // print_r($data);exit;

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($checkBokingRefund->num_rows()<1 )
        {
            echo $res=json_api(0,"Kode Booking dan Refund tidak ditemukan");      
        }
        else
        {

            $dataEmail=array("recipient"=>$sendto,
                            "subject"=>$subject,
                            "body"=>$content,
                            );

            $this->db->trans_begin();

            $this->refund_model->update_data($this->_table,$updateData,"id=$id");
            $this->refund_model->insert_data("app.t_log_refund_transfer",$insertLog);

            $sendEmail=mailSend($subject, $sendto, $content);

            $sendEmail ? $dataEmail['status'] = 1 : $dataEmail['status'] = 0;
            $this->refund_model->insert_data("core.t_trx_email",$dataEmail);   

            // print_r($dataEmail);exit;
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil Edit data');                
            }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'refund/refund/action_revisi_usaha';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    // function getImg()
    // {

    //     $url = "";
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //         // CURLOPT_PORT => $portSocket,
    //         CURLOPT_URL => $terjualurl,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 30,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => "GET",
    //         CURLOPT_POSTFIELDS => "",
    //         // CURLOPT_HTTPHEADER => array(
    //         //     "Postman-Token: caa08c67-a11c-4785-ae78-0c47f4b4c851",
    //         //                     "cache-control: no-cache"
    //         // ),
    //     ));

    //     $response = curl_exec($curl);
    //     $tes = json_decode($response, true);
    //     $err = curl_error($curl);


    //     curl_close($curl);

    //     if ($response) {
    //         echo $res=json_api(1, $tes['message']);
    //         // print_r( $tes);
    //         // echo $tes['message'];
    //     }
    //     else {
    //         echo $res=json_api(0, 'Gagal hit api  '.$err);
    //         // echo $err;
    //     }                
    // }   


    public function getStatusOld(){

        $data['']="Pilih";
        $data[$this->enc->encode(1)]="Proses";
        $data[$this->enc->encode(2)]="Sukses Transfer";
        $data[$this->enc->encode(3)]="Gagal Transfer";
        $data[$this->enc->encode("1_b")]="Menuggu Approve"; // menunggu approve sttatusnya sama dengan proses 1, hanya is approvenya belum diisi
        $data[$this->enc->encode(4)]="Proses CS Belum Upload ";
        $data[$this->enc->encode(5)]="Proses CS Menunggu Verifikasi";
        $data[$this->enc->encode(4)]="Proses CS Revisi Upload";

        return $data;
    }

    public function getStatus(){

        $data['']="Pilih";
        $data[$this->enc->encode(1)]="Verification Proses";
        $data[$this->enc->encode(2)]="Validation Process";
        $data[$this->enc->encode(3)]="Transfer Process";
        $data[$this->enc->encode(4)]="Customer Revision Process";
        $data[$this->enc->encode(5)]="Rejected";
        $data[$this->enc->encode(6)]="Transferred";

        return $data;
    }
    public function getApprovedBy(){

        $data['']="Pilih";
        $data[$this->enc->encode(1)]="CC/CS";
        $data[$this->enc->encode(2)]="Usaha";
        $data[$this->enc->encode(3)]="Keuangan";

        return $data;
    }


    function url_encode($data)
    {
        return strtr($data,
            array(
                '+' => '.',
                '=' => '-',
                '/' => '_'
            )
        );
    }

    function url_decode($url)
    {
        return strtr($url, array('.' => '+', '-' => '=', '_' => '/'));
    }

    public function generatePolaLinkUpload($refund_code, $booking_code)
    {

        $aes_key = $this->refund_model->select_data("app.t_mtr_custom_param", "WHERE status=1 AND param_name='aes_key' LIMIT 1")->row();
        $aes_iv = $this->refund_model->select_data("app.t_mtr_custom_param", "WHERE status=1 AND param_name='aes_iv' LIMIT 1")->row();
        $expired_link = $this->refund_model->select_data("app.t_mtr_custom_param", "WHERE status=1 AND param_name='expired_link_upload_refund' LIMIT 1")->row();

        // $link_upload_refund = $this->modelListUploadedDocument->select_data("app.t_mtr_custom_param", "WHERE status=1 AND param_name='link_upload_refund' LIMIT 1")->row();


        // pola link enkripsi
        $dateAdd2Hours  = strtotime('+' . $expired_link->param_value . ' hours', strtotime(date("Y-m-d H:i:s")));
        $dateExpired    = date('Y-m-d H:i:s', $dateAdd2Hours);
        $before         = $refund_code . '|' . $booking_code . '|' . $dateExpired;

        $val = PHP_AES_Cipher::encrypt2($aes_key->param_value, $aes_iv->param_value, $before);
        return $this->url_encode($val);
    }

    function validate_image($req, $file, $file_group) {
        $allowed             = ['jpg', 'jpeg', 'png'];
        $filename            = $file['name'];
        $size                = $file['size'] / 1024;
        $ext                 = pathinfo($filename, PATHINFO_EXTENSION);
        $config_param_refund = get_config_param('reservation');
        $max_upload          = 5 * 1024;
        // $max_post            = (int)(ini_get('post_max_size'));
        // $memory_limit        = (int)(ini_get('memory_limit'));
        $max_upload_param = $config_param_refund['max_file_resize_refund'] / 1024;
        $file_data        = ['req_data' => $req, 'filename' => $filename, 'size' => $size, 'ext' => $ext];
        $created_by       = 'system';
        $log_url          = site_url() . 'refund/edit/' . $file_group;
        $log_method       = 'POST';
        $log_param        = json_encode($file_data);
        $log_response     = null;

        $this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);

        if (!in_array(strtolower($ext), $allowed)) {
            $response = [
                "code"    => 0,
                "message" => 'INVALID TIPE FILE.',
                "data"    => null,
            ];
            echo json_encode($response);
            exit();
        }
        if (empty($file['tmp_name'])) {
            $response = [
                "code"    => 0,
                "message" => 'UPLOAD FILE GAGAL.',
                "data"    => null,
            ];
            echo json_encode($response);
            exit();
        }
        if ($size == 0) {
            $response = [
                "code"    => 0,
                "message" => 'UPLOAD FILE GAGAL.',
                "data"    => null,
            ];
            echo json_encode($response);
            exit();
        }
        if ($size > $max_upload) {
            $response = [
                "code"    => 0,
                // "message" => 'GAGAL. MAKSIMAL UKURAN FILE ' . $config_param_refund['max_file_resize_refund'] . ' KB.',
                "message" => 'GAGAL. MAKSIMAL UKURAN FILE 5 MB.',
                "data"    => null,
            ];
            echo json_encode($response);
            exit();
        }
        $image = base64_encode(file_get_contents($file['tmp_name']));
        return $image;
    }


    function process2()
    {
        // $max_size = $this->get_max_upload(getConfigParams($this->init)->max_file_resize_refund);
        $data = array();
        $refund_code = $this->input->post('refund-code');
        $nama_file = $this->input->post('bukti-transfer');

        // print_r($nama_file);exit;
        
        if (isset($_FILES['bukti-transfer']['tmp_name'])) {
            $nama = str_replace(".", "(dot)", $refund_code) . '_bukti_transfer.jpeg';
            $fileName = str_replace(" ", "_", $nama);
            $location = 'assets/img/tmp/' . $fileName;
            $url = base_url($location);

            if (@move_uploaded_file($_FILES["bukti-transfer"]["tmp_name"], $location) && @chmod($location, 0777)) {
                $data['code'] = 1;
                $data['type'] = "bukti_transfer";
                $data['message'] = "success";
            } else {
                $data['code'] = 0;
                $data['type'] = "bukti_transfer";
                $data['message'] = "upload failed";
            }
        }
        echo json_encode($data);
    }


    function resendEmailCS($id_enc) {
        validate_ajax();
		$id=$this->enc->decode($id_enc);

        $getDataRefund      = $this->refund_model->select_data($this->_table," where id={$id} ")->row();
        $getDataLogRefund   = $this->refund_model->select_data("app.t_log_refund_transfer"," where refund_code='". $getDataRefund->refund_code ."' order by id desc")->row();
        
        $subject        = "Refund Gagal";

        $sendto         = $getDataRefund->email; 
        $linkUpload     = $this->generatePolaLinkUpload($getDataRefund->refund_code, $getDataRefund->booking_code);

        $getUrl=$this->refund_model->select_data("app.t_mtr_custom_param"," where  param_name='link_update_file_refund' and status<>'-5'")->row();

    

        $dataRevisi=array(
            'cust_name'     => $getDataRefund->name,
            'booking_code'  => $getDataRefund->booking_code,
            'refund_code'   => $getDataRefund->refund_code,
            'linkUpload'    => $getUrl->param_value.$linkUpload,
            'komentar'      => $getDataLogRefund->transfer_description,
        );

        $content=$this->load->view($this->_module.'/emailRevisiCs',$dataRevisi,TRUE);


        $dataEmail=array("recipient"=>$sendto,
                        "subject"=>$subject,
                        "body"=>$content,
                        );

        $this->db->trans_begin();
        $sendEmail=mailSend($subject, $sendto, $content);

        $sendEmail ? $dataEmail['status'] = 1 : $dataEmail['status'] = 0;
        $this->refund_model->insert_data("core.t_trx_email",$dataEmail);   

        // print_r($dataEmail);exit;
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal resend email');
        }
        else
        {
            $this->db->trans_commit();
            if ($sendEmail) {
                echo $res=json_api(1, 'Berhasil resend email');               
            }
            else {
                echo $res=json_api(0, 'Gagal resend email');
            }
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'refund/resendEmailCS';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($dataRevisi);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
