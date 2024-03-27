<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class NewsInfo extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('newsInfoModel','newsInfo');
        $this->load->model('global_model');

        $this->_table    = '';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'news/newsInfo';
        $this->_pathFile  ="uploads/news/info/";
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->newsInfo->getDataList();
            $rows["tokenHash"] = $this->security->get_csrf_hash();
            $rows["csrfName"] = $this->security->get_csrf_token_name();
            echo json_encode($rows);
            exit;
        }

        $url=site_url($this->_module.'/add');
        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Berita Info',
            'content'  => 'newsInfo/index',
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
        
        $data['title'] = 'Tambah Berita Info';
        $data['parameter'] = $paramAllowedSize;
        $data['getDataType']=$this->getDataType();
        $data['paramMaxSize']=$paramMaxSize;

        //parameter range minutes frekuensi
        if(!empty($rangeMinutesFrekuensi)){
            $data['rangeMinutesFrekuensi']=$this->getMinutesFrekuensi()->param_value;
        }else{
            $data['rangeMinutesFrekuensi']=0;
        }

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // $type=trim($this->enc->decode($this->input->post('type')));
        $type=1;
        $title=trim($this->input->post('title'));
        $subTitle=trim($this->input->post('subTitle'));
        // $ordering=trim($this->input->post('ordering'));
        $startDate=trim($this->input->post('startDate'));
        $endDate=trim($this->input->post('endDate'));
        // $isDirect=trim($this->input->post('is_direct'));
        // $fileHide = trim($this->input->post('fileHide'));
        $fileHide = trim(base64_decode($this->input->post('fileHide')));
        $isDirect=true;
        $contentData=trim(base64_decode($this->input->post('contentData')));
        $filenameWithExt= @$_FILES['thumbnail']['name'];
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $linkImages=$this->input->post('linkImages[]');

        // echo $filename; exit;
        
        /* validation */
        // $this->form_validation->set_rules('type', 'Tipe', 'trim|required');
        $this->form_validation->set_rules('title', 'Judul Berita/ Promo', 'trim|required|max_length[50]|callback_special_char_news');
        $this->form_validation->set_rules('subTitle', 'Sub Judul Berita/ Promo', 'trim|required|callback_special_char_news');
        // $this->form_validation->set_rules('ordering', 'Urutan', 'trim|required|numeric');
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
        // } else {
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

            $path_file=$this->_pathFile.$nama_baru;
            $path_file_wpg=base_url().$this->_pathFile.$nama_baru2.".webp";

            $data["image"] = array("detail"=>$path_file_wpg);
        }  
        
        // kiriman data
        $data["type"]=  (int)$type;
        $data["title"]= $title;
        $data["sub_title"]= $subTitle;        
        $data["is_redirect"] = $isDirect==1?true:false;
        $data["created_by"]= $this->session->userdata('username');
        $data["start_published"]= $startDate;
        $data["end_published"]= $endDate;
        $data["content"]= $contentData;
        
        if(!empty($linkImages)){
            $data['content_images']=$linkImages;
        }

        // echo json_encode($data); exit;

        $oneByteToKbyte=1024;
        $paramMaxSize= $this->getParam()->param_value;
        $paramAllowedSize= $paramMaxSize * $oneByteToKbyte ;

      
        // if(empty($filename))
        // {
        //     $res=json_api(0,"File Gambar Harus diisi");     
        // }  

        if(array_sum($checkingFormatFile)>0)
        {
            echo $res=json_api(0," Format File Harus jpg/jpeg atau png");     
        }
        else if($paramAllowedSize <= $getSizeFile)
        {
            echo $res=json_api(0," File Tidak boleh lebih dari ".$paramMaxSize." kb");  
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
            $sendData=$this->newsInfo->postData($urlApi,$data);

            if($sendData->status==1)
            {
                echo $res=json_api(1, 'Berhasil tambah data');   
                
                if(!empty($filename)){
                    // Menyimpan path 
                    // move_uploaded_file($lokasi,$path_file); 
                    file_put_contents($path_file,  file_get_contents($fileHide));
                    $this->convertTowebp($nama_baru, $nama_baru2);    
                    
                    // save png
                    imagepng(imagecreatefromstring(file_get_contents($lokasi)), $this->_pathFile.$nama_baru2.".jpeg");
                    
                }
            }
            else
            {
                echo $res=json_api(0, $sendData->message,$sendData);        
            }
            


        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'news/newsInfo/action_add';
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
        $sendData=$this->newsInfo->postData($urlApi,$data);

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

        if (empty($detail->image))
        {
            $detail->image = (object)["detail"=>""];
        }

        // print_r($detail); exit;

        $oneByteToKbyte=1024;
        $paramMaxSize=$this->getParam()->param_value; // hitung per kb
        $paramAllowedSize= $paramMaxSize * $oneByteToKbyte ;
        $rangeMinutesFrekuensi=$this->getMinutesFrekuensi(); // minutes frekuensi


        $data['title'] = 'Edit Berita Info';
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

        $this->load->view($this->_module.'/edit',$data);
    }

    public function action_edit(){
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        $post = $this->input->post();
        
        $oldPath=trim($this->input->post('oldPath'));
        $id=trim($this->enc->decode($this->input->post('id')));

        $type=1;
        $title=trim($this->input->post('title'));
        $subTitle=trim($this->input->post('subTitle'));
        $startDate=trim($this->input->post('startDate'));
        $typeStartDate=trim($this->input->post('typeStartDate'));
        $endDate=trim($this->input->post('endDate'));
        // $isDirect=trim($this->input->post('is_direct'));
        $isDirect=true;
        $fileHide = trim(base64_decode($this->input->post('fileHide')));
        // $fileHide = trim($this->input->post('fileHide'));
        $linkImages=$this->input->post('linkImages[]');

        $contentData=trim(base64_decode($this->input->post('contentData')));
        $filenameWithExt = @$_FILES['thumbnail']['name'];
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

        // /* validation */
        $this->form_validation->set_rules('title', 'Judul Berita/ Promo', 'trim|required|max_length[50]|callback_special_char_news');
        $this->form_validation->set_rules('subTitle', 'Sub Judul Berita/ Promo', 'trim|required|callback_special_char_news');
        $this->form_validation->set_rules('startDate', 'Priode Awal', 'trim|required|callback_validate_date_time');
        $this->form_validation->set_rules('typeStartDate', 'tipe awal mulai', 'trim|required|numeric');
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
        $path_file_wpg=$oldPath;
        $baseUrl="";

        // jika image sebelumnya ada datanya
        if(!empty($path_file_wpg))
        {
            $data["image"]=array("detail"=>$path_file_wpg);
        }

        // jika di input image baru
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
            $path_file_wpg=$this->_pathFile.$nama_baru2.".webp";
            $baseUrl=base_url();
            $getSizeFile += filesize($lokasi);

            $data["image"]=array("detail"=>$baseUrl.$path_file_wpg);
        }  

        // kiriman data
        $data["notification_id"]=$id;
        $data["type"]=  (int)$type;
        $data["title"]= $title;
        $data["sub_title"]= $subTitle;        
        $data["is_redirect"] = $isDirect==1?true:false;
        $data["start_published"]= $startDate;
        $data["end_published"]= $endDate;
        $data["content"]= $contentData;
        $data["updated_by"]=$this->session->userdata('username');

        if(!empty($linkImages)){
            $data['content_images']=$linkImages;
        }      
        
        // print_r(json_encode($data)); exit;       

        $oneByteToKbyte=1024;
        $paramMaxSize=$this->getParam()->param_value; // hitung per kb
        $paramAllowedSize= $paramMaxSize * $oneByteToKbyte ;

        if(array_sum($checkingFormatFile)>0)
        {
            echo $res=json_api(0," Format File Harus jpg/jpeg atau png");     
        }
        else if($paramAllowedSize <= $getSizeFile)
        {
            
            echo $res=json_api(0," File Tidak boleh lebih dari ".$paramMaxSize." kb");  
        }
        else if(array_sum($checkingDateTime)>0 && $typeStartDate==1  )
        {
            echo $res=json_api(0,"Awal Publikasi kurang dari waktu ".$timeNow." ");     
        }         
        else if(array_sum($checkingDateTimeEnd)>0)
        {
            echo $res=json_api(0,"Akhir Publikasi kurang dari waktu ".$timeNow." ");     
        }
        else if(array_sum($checkingImages)>0)
        {
            echo $res=json_api(0,"Link URL Gambar tidak boleh ada yang kosong");     
        }           
        else
        {
            $urlApi="master_notification/update_notification";            
            $sendData=$this->newsInfo->postData($urlApi,$data);

            if($sendData->status==1)
            {
                echo $res=json_api(1, 'Berhasil tambah data');      
                
                if(!empty($filename))
                {
                    // Menyimpan path 
                    // move_uploaded_file($lokasi,$path_file); 
                    file_put_contents($path_file,  file_get_contents($fileHide));
                    $this->convertTowebp($nama_baru, $nama_baru2); 

                    imagepng(imagecreatefromstring(file_get_contents($lokasi)), $this->_pathFile.$nama_baru2.".jpeg");
                    
                    $ext = ".".pathinfo($oldPath, PATHINFO_EXTENSION);                    
                    // delete old image
                    $explode= explode("/",$oldPath);
                    $count=count((array)$explode);
                    @unlink($this->_pathFile.$explode[$count - 1]);   
                    
                    // unlink png                    
                    $replace = str_replace($ext,".jpeg",$explode[$count - 1]);
                    @unlink($this->_pathFile.$replace);  
                }
            }
            else
            {
                echo $res=json_api(0, $sendData->message,$sendData);        
            }
            
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'news/newsInfo/action_edit';
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
        $sendData=$this->newsInfo->postData($urlApi,$data);

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
        $sendData=$this->newsInfo->postData($urlApi,$data);

        if($sendData->status==1){
            $response = json_api(1,'Update Status Berhasil');
        }else{
            $response = json_api(0,$sendData->message,$sendData->data); 
        }

        /* Fungsi Create Log */
        $this->log_activitytxt->createLog($this->_username, uri_string().'news/newsInfo/change_status', 'change_status', json_encode($data), $response); 

        echo $response;
    }

    public function getDataType(){

        $data[""]="Pilih";
        $data["1"]="Info";
        $data["2"]="Promo";
        $data["3"]="Berita";

        return $data;
    }

    function convertTowebp($new_name, $new_name2, $count=0, $quality = 100)
    {
        $dir=$this->_pathFile;
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
        $getParameter=$this->newsInfo->select_data("app.t_mtr_custom_param"," where status=1 and param_name='max_img_news' ")->row();

        return $getParameter;
    }   
    
    function getMinutesFrekuensi()
    {
        $getParameter=$this->newsInfo->select_data("app.t_mtr_custom_param"," where status=1 and param_name='range_minutes_frekuensi' ")->row();

        return $getParameter;
    } 

    
}
