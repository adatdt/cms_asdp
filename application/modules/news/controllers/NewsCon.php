<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class NewsCon extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('newsConModel','newsCon');
        $this->load->model('global_model');

        $this->_table    = '';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'news/newsCon';
        $this->_pathFile  ="uploads/news/berita/";
        $this->_pathFileThumbnail  ="uploads/news_thumbnail/berita/";
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            // $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->newsCon->getDataList();
            $rows["tokenHash"] = $this->security->get_csrf_hash();
            $rows["csrfName"] = $this->security->get_csrf_token_name();
            echo json_encode($rows);
            exit;
        }

        $url=site_url($this->_module.'/add');
        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Berita',
            'content'  => 'newsCon/index',
            'btn_add'  => generate_button($this->_module, 'add', '<button onclick=" showModalNew2(\''.$url.'\')" class="btn btn-sm btn-warning" title="Tambah"><i class="fa fa-plus"></i> Tambah</button> ')
        );
        

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $oneByteToKbyte=1024;
        $paramMaxSize=$this->getParam()->param_value; // hitung per kb
        $paramAllowedSize= $paramMaxSize * $oneByteToKbyte ;
        $rangeMinutesFrekuensi=$this->getMinutesFrekuensi(); // minutes frekuensi
        // $maxLinkPicture=$this->getMaxLinkPicture(); //max limit input gambar
        
        $data['title'] = 'Tambah Berita';
        $data['parameter'] = $paramAllowedSize;
        $data['getDataType']=$this->getDataType();
        $data['paramMaxSize']=$paramMaxSize;

        //parameter range minutes frekuensi
        if(!empty($rangeMinutesFrekuensi)){
            $data['rangeMinutesFrekuensi']=$this->getMinutesFrekuensi()->param_value;
        }else{
            $data['rangeMinutesFrekuensi']=0;
        }

        // if(!empty($maxLinkPicture)){
        //     $data['maxLinkPicture']=$this->getMaxLinkPicture()->param_value;
        // }else{
        //     $data['maxLinkPicture']=5;
        // }

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // $size = (int) $_SERVER['CONTENT_LENGTH'];
        // echo $size;
        // exit;


        // print_r($this->input->post()); exit;

        // $type=trim($this->enc->decode($this->input->post('type')));
        $type=3;
        $title=trim($this->input->post('title'));
        $subTitle=trim($this->input->post('subTitle'));
        $ordering=trim($this->input->post('ordering'));
        $startDate=trim($this->input->post('startDate'));
        $endDate=trim($this->input->post('endDate'));
        $fileHide = trim(base64_decode($this->input->post('fileHide')));
        $fileHideThumbnail = trim(base64_decode($this->input->post('fileHideThumbnail')));
        $videoUrl = trim($this->input->post('videoUrl'));
        $contentData=trim(base64_decode($this->input->post('contentData')));
        $linkImages=$this->input->post('linkImages[]');

        // $isDirect=trim($this->input->post('is_direct'));
        $filenameWithExt= @$_FILES['thumbnail']['name'];
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

        // print_r($this->input->post()); exit;
        // echo $filename; exit;
        
        /* validation */
        // $this->form_validation->set_rules('type', 'Tipe', 'trim|required');
        $this->form_validation->set_rules('title', 'Judul Berita/ Promo', 'trim|required|max_length[50]|callback_special_char_news');
        $this->form_validation->set_rules('subTitle', 'Sub Judul Berita/ Promo', 'trim|required|callback_special_char_news');
        $this->form_validation->set_rules('ordering', 'Urutan', 'trim|required|numeric');
        $this->form_validation->set_rules('startDate', 'Priode Awal', 'trim|required|callback_validate_date_time');
        $this->form_validation->set_rules('endDate', 'Priode Akhir', 'trim|required|callback_validate_date_time');
        $this->form_validation->set_rules('contentData', 'Konten', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('validate_date_time','%s Format Tanggal tidak sesuai !');
        $this->form_validation->set_message('special_char_news','%s Mengandung Invalid Karakter!'); 
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
            exit;
        }
        /* data post */
        if(empty($linkImages)){
            $checkingImages[]=0;
        }

        if(!empty($linkImages)){
            $countImages = count(array_filter($linkImages, function($x) { return empty($x); }));
            
            if ($countImages > 0){   
                $checkingImages[]=1; 
            }else{
                $checkingImages[]=0; 
            }
        }

        $getMinutesFrekuensi=$this->getMinutesFrekuensi()->param_value;
        $strtTime = strtotime("+".$getMinutesFrekuensi." minutes", strtotime('now'));
        $timeNow = date('Y-m-d H:i',$strtTime);

        //validasi tanggal 
        if ($startDate > $endDate){   
            $checkingDate[]=1; 
        }
        else
        {       
            $checkingDate[]=0; 
        }

        // //validasi tanggal jam backdate
        // if ($startDate < date('Y-m-d H:i')) {
        //    $checkingDateTime[]=1; 
        // } 
        // else 
        // {
        //    $checkingDateTime[]=0; 
        // }

        //validasi tanggal jam awal backdate
        if ($startDate < $timeNow) {
            $checkingDateTime[]=1; 
         } else {
            $checkingDateTime[]=0; 
         }
 
         //validasi tanggal jam akhir backdate
         if ($endDate < $timeNow) {
             $checkingDateTimeEnd[]=1; 
          } else {
             $checkingDateTimeEnd[]=0; 
          }
        
        //validasi jam required
        $time_start = strlen($startDate);
        $time_end   = strlen($endDate);

        if($time_start < 16 or $time_end < 16 ){
            $checkingTime[]=1; 
        }
        else
        {
           
            $checkingTime[]=0; 
        }

        $checkingFormatFile[]=0;
        $nama_baru="";
        $nama_baru2="";
        $getSizeFile=0;
        if(!empty($filename))
        {
            $lokasi = $_FILES['thumbnail']['tmp_name'];
            $extensi = pathinfo($filenameWithExt, PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
            $nama_baru="type_".$type."_".str_replace(" ","_",$filename)."_".date("YmdHis").".".$extensi;
            $nama_baru2="type_".$type."_".str_replace(" ","_",$filename)."_".date("YmdHis");

            if(strtoupper($extensi)=="JPG" or strtoupper($extensi)=="PNG" or strtoupper($extensi)=="JPEG")
            {
                $checkingFormatFile[]=0;                
            }
            else
            {
                $checkingFormatFile[]=1;
            }
            $getSizeFile += filesize($lokasi);

        }  
        
        $path_file=$this->_pathFile.$nama_baru;
        $path_file_wpg=$this->_pathFile.$nama_baru2.".webp";

        $path_file_thumbnail=$this->_pathFileThumbnail.$nama_baru;
        $path_file_wpg_thumbnail=$this->_pathFileThumbnail.$nama_baru2.".webp";

        $data = array(
            "type"=> (int)$type,
            "title"=>$title,
            "sub_title"=>$subTitle,
            "image"=>array(
                    "detail"=>base_url().$path_file_wpg,
                    "thumbnail"=>base_url().$path_file_wpg_thumbnail,
            ),
            // "status"=>1,
            // "is_redirect"=>$isDirect==1?true:false,
            "is_redirect"=>true,
            "order"=>(int)$ordering,
            // "created_on"=>
            "created_by"=>$this->session->userdata('username'),
            "start_published"=>$startDate,
            "end_published"=>$endDate,
            "content"=>$contentData

        );

        if(!empty($linkImages)){
            $data['content_images']=$linkImages;
        }

        // print_r($data);exit;
        $oneByteToKbyte=1024;
        $paramMaxSize= $this->getParam()->param_value;
        $paramAllowedSize= $paramMaxSize * $oneByteToKbyte ;

        // check videoUrl
        $checkVideoUrl=1;
        if(!empty($videoUrl))
        {
            
            $checkVideoUrlData = $this->sourceVideoAllowed($videoUrl);
            $checkVideoUrl = $checkVideoUrlData['code'];

            $data['video']=$videoUrl;
            if($checkVideoUrlData['isYoutube']==1)
            {
                $data['is_video_youtube']=true;
            }
        }

        if(array_sum($checkingFormatFile)>0)
        {
            echo $res=json_api(0," Format File Harus jpg/jpeg atau png");     
        }
        else if($paramAllowedSize <= $getSizeFile) // tidak di pake karen pake resize
        {
            echo $res=json_api(0," File Tidak boleh lebih dari ".$paramMaxSize." kb");  
        }
        else if(empty($filename))
        {
            echo $res=json_api(0,"File Gambar Harus diisi");     
        }
        else if($checkVideoUrl==0)
        {
            echo $res=json_api(0," Sumber URL Video Tidak diizinkan ");    
        }
        else if(array_sum($checkingDate)>0)
        {
            echo $res=json_api(0," Tanggal mulai Anda harus lebih awal dari tanggal akhir Anda");     
        }
        else if(array_sum($checkingDateTime)>0)
        {
            echo $res=json_api(0,"Awal Publikasi kurang dari waktu ".$timeNow." ");     
        }
        else if(array_sum($checkingDateTimeEnd)>0)
        {
            echo $res=json_api(0,"Akhir Publikasi kurang dari waktu ".$timeNow." ");     
        }
        else if(array_sum($checkingTime)>0)
        {
            echo $res=json_api(0,"Jam harus di isi");     
        } 
        else if(array_sum($checkingImages)>0)
        {
            echo $res=json_api(0,"Link URL Gambar tidak boleh ada yang kosong");     
        }       
        else
        {
            
            
            $urlApi="master_notification/create_notification";            
            $sendData=$this->newsCon->postData($urlApi,$data);

            if($sendData->status==1)
            {
                echo $res=json_api(1, 'Berhasil tambah data');  

                // Menyimpan path 
                // move_uploaded_file($lokasi,$path_file); 
                file_put_contents($path_file,  file_get_contents($fileHide));
                file_put_contents($path_file_thumbnail,  file_get_contents($fileHideThumbnail));
                
                // convert to webp
                $this->convertTowebp($nama_baru, $nama_baru2, $this->_pathFile);
                $this->convertTowebp($nama_baru, $nama_baru2, $this->_pathFileThumbnail);   
                
                // convert to PNG for mobile consume
                imagepng(imagecreatefromstring(file_get_contents($lokasi)), $this->_pathFile.$nama_baru2.".jpeg");
                imagepng(imagecreatefromstring(file_get_contents($fileHideThumbnail)), $this->_pathFileThumbnail.$nama_baru2.".jpeg");
                
            
            }
            else
            {
                echo $res=json_api(0, $sendData->message,$sendData);        
            }


        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'news/newsCon/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($param){
        validate_ajax();
        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $p=$this->enc->decode($param);
        $d = explode('|', $p);        
        $id=$d[0];
        $type=$d[1];

        $data=array(
            "type"=> (int)$type,
            "notification_id"=> $id
        );

        // print_r(json_encode($data)); exit;     
        $urlApi="master_notification/detail_notification";            
        $sendData=$this->newsCon->postData($urlApi,$data);

        $getDataType=$this->getDataType();
        $detail = $sendData->data;
        $getDataTypeSelected="";
        $getDataType2[""]="Pilih";

        foreach ($getDataType as $key => $value) {

            if($key==$detail->type)
            {
                $encKey=$this->enc->encode($detail->type);
                $getDataType2[$encKey]=$value;
                $getDataTypeSelected=$encKey;
            }
            else
            {
                $getDataType2[$key]=$value;
            }
        }

        $oneByteToKbyte=1024;
        $paramMaxSize=$this->getParam()->param_value; // hitung per kb
        $paramAllowedSize= $paramMaxSize * $oneByteToKbyte ;
        $rangeMinutesFrekuensi=$this->getMinutesFrekuensi(); // minutes frekuensi
        // $maxLinkPicture=$this->getMaxLinkPicture(); //max limit input gambar
        
        // print_r(json_encode($detail)); exit;     

        $data['title'] = 'Edit Berita';
        $data['parameter'] = $paramAllowedSize;
        $data['detail'] = $detail;
        $data['getDataType']=$getDataType2;
        $data['getDataTypeSelected']=$getDataTypeSelected;
        $data['paramMaxSize']=$paramMaxSize;

        //parameter range minutes frekuensi
        if(!empty($rangeMinutesFrekuensi)){
            $data['rangeMinutesFrekuensi']=$this->getMinutesFrekuensi()->param_value;
        }else{
            $data['rangeMinutesFrekuensi']=0;
        }

        // if(!empty($maxLinkPicture)){
        //     $data['maxLinkPicture']=$this->getMaxLinkPicture()->param_value;
        // }else{
        //     $data['maxLinkPicture']=5;
        // }
        
        $this->load->view($this->_module.'/edit',$data);
    }


    public function action_edit(){
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        $post = $this->input->post();
        // print_r($post); exit;
        
        $oldPath=trim($this->input->post('oldPath'));
        $id=trim($this->enc->decode($this->input->post('id')));

        // $type=trim($this->enc->decode($this->input->post('type')));
        $type=3;
        $title=trim($this->input->post('title'));
        $subTitle=trim($this->input->post('subTitle'));
        $ordering=trim($this->input->post('ordering'));
        $startDate=trim($this->input->post('startDate'));
        $typeStartDate=trim($this->input->post('typeStartDate'));
        $endDate=trim($this->input->post('endDate'));
        // $fileHide = trim($this->input->post('fileHide'));
        // $fileHideThumbnail = trim($this->input->post('fileHideThumbnail'));
        $fileHide = trim(base64_decode($this->input->post('fileHide')));
        $fileHideThumbnail = trim(base64_decode($this->input->post('fileHideThumbnail')));
        $videoUrl = trim($this->input->post('videoUrl'));
        $linkImages=$this->input->post('linkImages[]');

        // $isDirect=trim($this->input->post('is_direct'));
        $contentData=trim(base64_decode($this->input->post('contentData')));
        $filenameWithExt = @$_FILES['thumbnail']['name'];
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        
        // /* validation */
        // $this->form_validation->set_rules('type', 'Tipe', 'trim|required');
        $this->form_validation->set_rules('title', 'Judul Berita/ Promo', 'trim|required|max_length[50]|callback_special_char_news');
        $this->form_validation->set_rules('subTitle', 'Sub Judul Berita/ Promo', 'trim|required|callback_special_char_news');
        $this->form_validation->set_rules('ordering', 'Urutan', 'trim|required|numeric');
        $this->form_validation->set_rules('typeStartDate', 'tipe awal mulai', 'trim|required|numeric');

        $this->form_validation->set_rules('startDate', 'Priode Awal', 'trim|required|callback_validate_date_time');
        $this->form_validation->set_rules('endDate', 'Priode Akhir', 'trim|required|callback_validate_date_time');
        $this->form_validation->set_rules('contentData', 'Konten', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('validate_date_time','%s Format Tanggal tidak sesuai !');
        $this->form_validation->set_message('special_char_news','%s Mengandung Invalid Karakter!'); 
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
            exit;
        }

        /* data post */

        if(empty($linkImages)){
            $checkingImages[]=0;
        }

        if(!empty($linkImages)){
            $countImages = count(array_filter($linkImages, function($x) { return empty($x); }));
            
            if ($countImages > 0){   
                $checkingImages[]=1; 
            }else{
                $checkingImages[]=0; 
            }
        }

        $getMinutesFrekuensi=$this->getMinutesFrekuensi()->param_value;
        $strtTime = strtotime("+".$getMinutesFrekuensi." minutes", strtotime('now'));
        $timeNow = date('Y-m-d H:i',$strtTime);
        
        //validasi tanggal jam awal backdate
        if ($startDate < $timeNow) {
            $checkingDateTime[]=1; 
         } else {
            $checkingDateTime[]=0; 
         }

        //validasi tanggal jam akhir backdate
        if ($endDate < $timeNow) {
            $checkingDateTimeEnd[]=1; 
        } else {
            $checkingDateTimeEnd[]=0; 
        }
        
        $checkingFormatFile[]=0;
        $nama_baru="";
        $nama_baru2="";
        $getSizeFile=0;

        $explode = explode("/",$oldPath);        
        $indexName = count((array)$explode) - 1;

        $path_file_wpg=$oldPath;
        $path_file_wpg_thumbnail= base_url().$this->_pathFileThumbnail.$explode[$indexName];
        
        if(!empty($filenameWithExt))
        {
            $lokasi = $_FILES['thumbnail']['tmp_name'];
            $extensi = pathinfo($filenameWithExt, PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
            $nama_baru="type_".$type."_".str_replace(" ","_",$filename)."_".date("YmdHis").".".$extensi;
            $nama_baru2="type_".$type."_".str_replace(" ","_",$filename)."_".date("YmdHis");

            if(strtoupper($extensi)=="JPG" or strtoupper($extensi)=="PNG" or strtoupper($extensi)=="JPEG")
            {
                $checkingFormatFile[]=0;                
            }
            else
            {
                $checkingFormatFile[]=1;
            }

            $path_file=$this->_pathFile.$nama_baru;
            $path_file_wpg=base_url().$this->_pathFile.$nama_baru2.".webp";
            
            $path_file_thumbnail=$this->_pathFileThumbnail.$nama_baru;
            $path_file_wpg_thumbnail=base_url().$this->_pathFileThumbnail.$nama_baru2.".webp";        
            $getSizeFile += filesize($lokasi);
        }  

                
        $data = array(
            "notification_id"=>$id,
            "type"=> (int)$type,
            "title"=>$title,
            "sub_title"=>$subTitle,
            "image"=>array(
                "detail"=>$path_file_wpg,
                "thumbnail"=>$path_file_wpg_thumbnail,
                ),            
            // "is_redirect"=>$isDirect==1?true:false,
            "is_redirect"=>true,
            "order"=>(int)$ordering,
            "updated_by"=>$this->session->userdata('username'),
            "start_published"=>$startDate,
            "end_published"=>$endDate,
            "content"=>$contentData
            
            
        );                
        // print_r(json_encode($data)); exit;       

        if(!empty($linkImages)){
            $data['content_images']=$linkImages;
        }

        $oneByteToKbyte=1024;
        $paramMaxSize=$this->getParam()->param_value; // hitung per kb
        $paramAllowedSize= $paramMaxSize * $oneByteToKbyte ;

        // check videoUrl
        $checkVideoUrl=1;
        if(!empty($videoUrl))
        {
            $checkVideoUrlData = $this->sourceVideoAllowed($videoUrl);
            $checkVideoUrl = $checkVideoUrlData['code'];

            $data['video']=$videoUrl;
            if($checkVideoUrlData['isYoutube']==1)
            {
                $data['is_video_youtube']=true;
            }
        }

       
        if(array_sum($checkingFormatFile)>0)
        {
            echo $res=json_api(0," Format File Harus jpg/jpeg atau png");     
        }
        else if($checkVideoUrl==0)
        {
            echo $res=json_api(0," Sumber Tidak diizinkan ");    
        }
        else if(array_sum($checkingDateTime)>0 && $typeStartDate==1 )
        {
            echo $res=json_api(0,"Awal Publikasi kurang dari waktu ".$timeNow." ");     
        }        
        else if(array_sum($checkingDateTimeEnd)>0)
        {
            echo $res=json_api(0,"Akhir Publikasi kurang dari waktu ".$timeNow." ");     
        }  
        else if($paramAllowedSize <= $getSizeFile) //tidak di pake karen pake resize
        {
            
            echo $res=json_api(0," File Tidak boleh lebih dari ".$paramMaxSize." kb");  
        }
        else if(array_sum($checkingImages)>0)
        {
            echo $res=json_api(0,"Link URL Gambar tidak boleh ada yang kosong");     
        }         
        else
        {
            $urlApi="master_notification/update_notification";            
            $sendData=$this->newsCon->postData($urlApi,$data);
            // print_r($sendData);exit;
            if($sendData->status==1)
            {
                echo $res=json_api(1, 'Berhasil tambah data');      
                
                if(!empty($filename))
                {
                    // Menyimpan path 
                    // move_uploaded_file($lokasi,$path_file); 
                    file_put_contents($path_file,  file_get_contents($fileHide));
                    file_put_contents($path_file_thumbnail,  file_get_contents($fileHideThumbnail));

                    // convert to webp
                    $this->convertTowebp($nama_baru, $nama_baru2, $this->_pathFile); 
                    $this->convertTowebp($nama_baru, $nama_baru2, $this->_pathFileThumbnail);  
                    
                    imagepng(imagecreatefromstring(file_get_contents($lokasi)), $this->_pathFile.$nama_baru2.".jpeg");
                    imagepng(imagecreatefromstring(file_get_contents($fileHideThumbnail)), $this->_pathFileThumbnail.$nama_baru2.".jpeg");
                    

                    $ext = ".".pathinfo($oldPath, PATHINFO_EXTENSION);

                    // delete old image
                    $explode= explode("/",$oldPath);
                    $count=count((array)$explode);
                    @unlink($this->_pathFile.$explode[$count - 1]);  
                    @unlink($this->_pathFileThumbnail.$explode[$count - 1]);   
                    
                    // unlink png                    
                    $replace = str_replace($ext,".jpeg",$explode[$count - 1]);
                    @unlink($this->_pathFile.$replace);  
                    @unlink($this->_pathFileThumbnail.$replace);  
                }
            }
            else
            {
                echo $res=json_api(0, $sendData->message,$sendData);        
            }
            
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'news/newsCon/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($param){
        validate_ajax();
        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $p = $this->enc->decode($param);
        $d = explode('|', $p);
        // print_r($d); exit;
        $id=$d[0];
        $type=$d[1];

        /* data */
        $data = array(
            'notification_id' => $id,
            'type' => (int)$type,
            'status' => -5,
            'updated_by'=>$this->session->userdata('username'),
        );
        // print_r(json_encode($data)); exit;     
        $urlApi="master_notification/update_status_notification";            
        $sendData=$this->newsCon->postData($urlApi,$data);

        if ($sendData->status==1)
        {
            echo $res=json_api(1, 'Berhasil delete data');
        }
        else
        {
            echo $res=json_api(0, $sendData->message,$sendData->data);
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'news/newsCon/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param){
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        $id=$d[0];
        $status=$d[1];
        $type=$d[2];
        /* data */
        $data = array(
            'notification_id' => $id,
            'status' => (int)$status,
            'type'=>(int)$type,
            'updated_by'=>$this->session->userdata('username'),
        );
        // print_r(json_encode($data)); exit;     
        $urlApi="master_notification/update_status_notification";            
        $sendData=$this->newsCon->postData($urlApi,$data);

        if($sendData->status==1){
            $response = json_api(1,'Update Status Berhasil');
        }else{
            $response = json_api(0,$sendData->message,$sendData->data); 
        }

        /* Fungsi Create Log */
        $this->log_activitytxt->createLog($this->_username, uri_string().'news/newsCon/change_status', 'change_status', json_encode($data), $response); 

        echo $response;
    }

    public function getDataType(){

        $data[""]="Pilih";
        $data["1"]="Info";
        $data["2"]="Promo";
        $data["3"]="Berita";

        return $data;
    }

    function convertTowebp($new_name, $new_name2, $dir, $count=0, $quality = 100)
    {
        // $dir=$this->_pathFile;

        $mime_type = mime_content_type($dir.$new_name);
        if ($mime_type == 'image/jpeg') {
            $img = imagecreatefromjpeg($dir.$new_name);
        }
        else if ($mime_type == 'image/png') {
            $img = imagecreatefrompng($dir.$new_name);
        }
        else if ($mime_type == 'image/gif') {
            $img = imagecreatefromgif($dir.$new_name);
        }
        else if ($mime_type == 'image/bmp') {
            $img = imagecreatefrombmp($dir.$new_name);
        }
        else
        {
            $img = imagecreatefromwebp($dir.$new_name);
        }

        // echo $new_name." ".$new_name2;        

        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
        imagewebp($img, $dir . $new_name2.".webp", $quality);        
        imagedestroy($img);

        // deleting image befor to webpeg
        if ($mime_type != 'image/webp') 
        {
            @unlink($dir.$new_name);
        }

    }

    function getParam()
    {
        $getParameter=$this->newsCon->select_data("app.t_mtr_custom_param"," where status=1 and param_name='max_img_news' ")->row();
// print_r($getParameter);exit;
        return $getParameter;
    }

    function sourceVideoAllowed($url)
    {
        $dataParam = $this->newsCon->select_data("app.t_mtr_custom_param"," where param_name='source_news_video'  and status=1 ")->row();

        $explode = explode("/",$url);
        $getDomain = $explode[2];

        $sourceVideo = explode(",", $dataParam->param_value);

        $checkingSource = 0;
        foreach ($sourceVideo as $key => $value) {
            if($value==$getDomain)
            {   
                $checkingSource +=1;
            }
        }
        
        if($checkingSource>0)
        {
            $return["code"] =1;
            $return["domail"] =$getDomain;

            switch ($getDomain) {
                case 'www.youtube.com':
                        $return["isYoutube"]=1;
                break;
                case 'youtu.be':
                    $return["isYoutube"]=1;
                break;                
                
                default:
                    $return["isYoutube"]=0;
                break;
            }
     
        }
        else
        {
            $return["code"] =0;
            $return["domail"] =$getDomain;
            $return["isYoutube"]=0;
        }

        return $return;
    }
    
    function getMinutesFrekuensi()
    {
        $getParameter=$this->newsCon->select_data("app.t_mtr_custom_param"," where status=1 and param_name='range_minutes_frekuensi' ")->row();

        return $getParameter;
    } 

    // function getMaxLinkPicture()
    // {
    //     $getParameter=$this->newsCon->select_data("app.t_mtr_custom_param"," where status=1 and param_name='limit_max_link_gambar' ")->row();
        
    //     return $getParameter;
    // } 
    
}
