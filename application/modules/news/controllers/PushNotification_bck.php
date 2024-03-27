<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class PushNotification extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('pushNotificationModel','pushNotification');
        $this->load->model('global_model');

        $this->_table    = '';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'news/pushNotification';
        $this->_pathFile  ="uploads/news/";
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->pushNotification->getDataList();
            echo json_encode($rows);
            exit;
        }

        $url=site_url($this->_module.'/add');
        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Push Notification',
            'content'  => 'pushNotification/index',
            'btn_add'  => generate_button($this->_module, 'add', '<button onclick=" showModalNew2(\''.$url.'\')" class="btn btn-sm btn-warning" title="Tambah"><i class="fa fa-plus"></i> Tambah</button> ')
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        // $grup = $this->pushNotification->getDataGrup();

        $oneByteToKbyte=1024;
        $paramMaxSize=$this->getParam()->param_value; // hitung per kb
        $paramMaxFrekuensi=$this->getDataTime(); // hitung frekuensi
        $paramAllowedSize= $paramMaxSize * $oneByteToKbyte ;

        $data['title'] = 'Tambah Push Notification';
        $data['paramMaxFrekuensi'] = count($paramMaxFrekuensi);
        $data['getDataType']=$this->getDataType();
        $data['getDataTime']=$this->getDataTime();
        $data['parameter'] = $paramAllowedSize;

        // $data['getDataUraian']=$this->getDataUraian();
        $this->load->view($this->_module.'/add',$data);
    
    }

    public function action_add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $oldPath=trim($this->input->post('oldPath'));
        $id=trim($this->input->post('id'));
        $type=trim($this->input->post('type'));
        $title=trim($this->input->post('title'));
        $isDirect=true;
        $subTitle=trim($this->input->post('subTitle'));
        $ordering=trim($this->input->post('ordering'));
        $startDate=trim($this->input->post('startDate'));
        $endDate=trim($this->input->post('endDate'));
        $time=$this->input->post('time[]');

        $contentData=trim(base64_decode($this->input->post('contentData')));

        $filenameWithExt= @$_FILES['thumbnail']['name'];
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $fileHide = trim($this->input->post('fileHide'));
        /* validation */

        $this->form_validation->set_rules('title', 'Judul Berita/ Promo', 'trim|required');
        $this->form_validation->set_rules('subTitle', 'Judul Berita/ Promo', 'trim|required');
        $this->form_validation->set_rules('startDate', 'Priode Awal', 'trim|required');
        $this->form_validation->set_rules('endDate', 'Priode Akhir', 'trim|required');
        $this->form_validation->set_rules('contentData', 'Konten', 'trim|required');
        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        /* data post */

        //validasi tanggal 
        if ($startDate > $endDate){
            
            $checkingDate[]=1; 
        }
        else{
            
            $checkingDate[]=0; 
        }

        //validasi jam
        $check_time = $time;

        if ($check_time > 0){
            // $keys = array_keys($check_time);
            $checkingFormatTime[]=0;
            $checkingTombolFrekuensi[]=0;

            foreach ($check_time as $key) {

                $check = 0 ;
                foreach ($check_time as $k => $v) {
                   
                    if ($v == $key) {
                        
                        $check +=1; 
                    
                    }
                }

                if ($check>=2){
                 $checkingFormatTime[]=1;
                }
            }
        
        }else{
           
            $checkingTombolFrekuensi[]=1;
        
        }

        // print_r($checkingFormatTime);exit;

        if ($type == 1) {

            $type_grup ='info/';

        }else if ($type == 2){

            $type_grup ='promo/';

        }else if($type == 3){

            $type_grup ='berita/';

        }

        $path_grup=$this->_pathFile.$type_grup;

        $checkingFormatFile[]=0;
        $nama_baru="";
        $nama_baru2="";
        $getSizeFile=0;
        $path_file_wpg=$oldPath;
        $baseUrl="";
        if(!empty($filenameWithExt))
        {
            $lokasi = $_FILES['thumbnail']['tmp_name'];
            $extensi = pathinfo($filenameWithExt, PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
            $nama_baru="Push_notif_type_".$type."_".str_replace(" ","_",$filename)."_".date("YmdHis").".".$extensi;
            $nama_baru2="Push_notif_type_".$type."_".str_replace(" ","_",$filename)."_".date("YmdHis");

            if(strtoupper($extensi)=="JPG" or strtoupper($extensi)=="PNG")
            {
                $checkingFormatFile[]=0;                
            }
            else
            {
                $checkingFormatFile[]=1;
            }

            $baseUrl=base_url();
            $getSizeFile += filesize($lokasi);

            $path_file=$path_grup.$nama_baru;
            $path_file_wpg=$path_grup.$nama_baru2.".webp";

        }

        // print_r($path_file_wpg);exit;

        if($path_file_wpg == null){
            
            $data["notification_id"]= $id;
            $data["type"]=  (int)$type;
            $data["title"]= $title;
            $data["sub_title"]= $subTitle;        
            $data["is_redirect"] = $isDirect==1?true:false;
            $data["created_by"]= $this->session->userdata('username');
            $data["time_published"]= $time;
            // $data["image"] = $path_file_wpg;
            $data["start_published"]= $startDate;
            $data["end_published"]= $endDate;
            $data["content"]= $contentData;
             // echo json_encode($data); exit;

        }else{

            $data = array(
                "notification_id"    => $id,
                "type"  => (int)$type,
                "title" => $title,
                "sub_title"=>$subTitle,
                "image"=>$baseUrl.$path_file_wpg,
                "is_redirect"=>true,
                // "order"=>(int)$ordering,
                "time_published"=>$time,
                "created_by"=>$this->session->userdata('username'),
                "start_published"=>$startDate,
                "end_published"=>$endDate,
                "content"=>$contentData

            );

          
        }

        // print_r($data);exit;

        $oneByteToKbyte=1024;
        $paramMaxSize=$this->getParam()->param_value; // hitung per kb
        $paramAllowedSize= $paramMaxSize * $oneByteToKbyte ;

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else if(array_sum($checkingFormatFile)>0)
        {
            echo $res=json_api(0," Format File Harus jpg atau png");     
        }
        else if($paramAllowedSize <= $getSizeFile)
        {
            echo $res=json_api(0," File Tidak boleh lebih dari ".$paramMaxSize." kb");  
        }
        else if(array_sum($checkingTombolFrekuensi)>0)
        {
            echo $res=json_api(0," Silakan Tekan tombol frekuensi");     
        }
        else if(array_sum($checkingFormatTime)>0)
        {
            echo $res=json_api(0," Jam Tidak boleh ada yang sama");     
        }
        else if(array_sum($checkingDate)>0)
        {
            echo $res=json_api(0," Tanggal mulai Anda harus lebih awal dari tanggal akhir Anda");     
        }
       
        else
        {

            $urlApi="master_notification/push/create_notification";            
            $sendData=$this->pushNotification->postData($urlApi,$data);

            if($sendData->status==1)
            {
                echo $res=json_api(1, 'Berhasil tambah data');      
                
                if(!empty($filename))
                {
                    // Menyimpan path 
                    // move_uploaded_file($lokasi,$path_file); 
                    file_put_contents($path_file,  file_get_contents($fileHide));

                    // convert to webp
                    $this->convertTowebp($nama_baru,$path_grup,$nama_baru2); 

                    // delete old image
                    // $explode= explode("/",$oldPath);
                    // $count=count((array)$explode);
                    // @unlink($path_grup.$explode[$count - 1]);        
                }
            }
            else
            {
                echo $res=json_api(0, $sendData->message,$sendData);        
            }
            
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'news/pushNotification/action_add';
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
            "push_notification_id"=> $id
        );
        // print_r(json_encode($data)); exit;   

        $urlApi="master_notification/push/detail_notification";            
        $sendData=$this->pushNotification->postData($urlApi,$data);
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

        $notifType[""]='Pilih';
        $notifTypeSelected="";        
        $notifTypeData=$detail->time_published;

        foreach ($notifTypeData as $key => $value) {

            if($detail->type==$key)
            {
                $notifTypeSelected=$this->enc->encode($key);
                $notifType[$notifTypeSelected]=$value;
            }
            else
            {
                $notifType[$this->enc->encode($key)]=$value;
            }            
        }

        if ($type == 1) {

            $type_grup ='Info';

        }else if ($type == 2){

            $type_grup ='Promo';

        }else if($type == 3){

            $type_grup ='Berita';

        }

        $oneByteToKbyte=1024;
        $paramMaxSize=$this->getParam()->param_value; // hitung per kb
        $paramAllowedSize= $paramMaxSize * $oneByteToKbyte ;
        $paramMaxFrekuensi=$this->getParamMax()->param_value;  

        $data['title'] = 'Edit Push Notification';
        $data['parameter'] = $paramAllowedSize;
        $data['detail'] = $detail;
        $data['type_grup'] = $type_grup;
        $data['getDataTime']=$this->getDataTime();
        $data['notifType'] = $notifType;
        $data['notifTypeSelected'] = $notifTypeSelected;
        $data['getDataType']=$getDataType2;
        $data['getDataTypeSelected']=$getDataTypeSelected;
        $data['paramMaxFrekuensi'] = $paramMaxFrekuensi;
        $this->load->view($this->_module.'/edit',$data);
    }

    public function action_edit(){
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        $post = $this->input->post();
        // print_r($post); exit;
        
        $oldPath=trim($this->input->post('oldPath'));
        $push_notification_id=trim($this->input->post('push_notification_id'));
        $notification_id=trim($this->input->post('notification_id'));
        $type=trim($this->input->post('type'));
        $title=trim($this->input->post('title'));
        $subTitle=trim($this->input->post('subTitle'));
        $startDate=trim($this->input->post('startDate'));
        $endDate=trim($this->input->post('endDate'));
        $time=$this->input->post('time[]');
        $contentData=trim(base64_decode($this->input->post('contentData')));
        $isDirect=true;
        $fileHide = trim($this->input->post('fileHide'));
        $filenameWithExt = @$_FILES['thumbnail']['name'];
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        
        // /* validation */
        $this->form_validation->set_rules('title', 'Judul Berita/ Promo', 'trim|required');
        $this->form_validation->set_rules('subTitle', 'Judul Berita/ Promo', 'trim|required');
        $this->form_validation->set_rules('startDate', 'Priode Awal', 'trim|required');
        $this->form_validation->set_rules('endDate', 'Priode Akhir', 'trim|required');
        $this->form_validation->set_rules('contentData', 'Konten', 'trim|required');
        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');

        /* data post */

        //validasi tanggal 
        if ($startDate > $endDate){
            
            $checkingDate[]=1; 
        }
        else{
            
            $checkingDate[]=0; 
        }
        
        //validasi jam
        $check_time = $time;

        if ($check_time > 0){
            // $keys = array_keys($check_time);
            $checkingFormatTime[]=0;
            $checkingTombolFrekuensi[]=0;

            foreach ($check_time as $key) {

                $check = 0 ;
                foreach ($check_time as $k => $v) {
                   
                    if ($v == $key) {
                        
                        $check +=1; 
                    
                    }
                }

                if ($check>=2){
                 $checkingFormatTime[]=1;
                }
            }
        
        }else{
           
            $checkingTombolFrekuensi[]=1;
        
        }

        if ($type == 1) {

            $type_grup ='info/';

        }else if ($type == 2){

            $type_grup ='promo/';

        }else if($type == 3){

            $type_grup ='berita/';

        }

        $path_grup=$this->_pathFile.$type_grup;

        $checkingFormatFile[]=0;
        $nama_baru="";
        $nama_baru2="";
        $getSizeFile=0;
        $path_file_wpg=$oldPath;
        $baseUrl="";

         // jika image sebelumnya ada datanya
        if(!empty($path_file_wpg))
        {
            $data["image"]=$path_file_wpg;
        }

        // jika di input image baru
        if(!empty($filenameWithExt))
        {
            $lokasi = $_FILES['thumbnail']['tmp_name'];
            $extensi = pathinfo($filenameWithExt, PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
            $nama_baru="Push_notif_type_".$type."_".str_replace(" ","_",$filename)."_".date("YmdHis").".".$extensi;
            $nama_baru2="Push_notif_type_".$type."_".str_replace(" ","_",$filename)."_".date("YmdHis");

            if(strtoupper($extensi)=="JPG" or strtoupper($extensi)=="PNG")
            {
                $checkingFormatFile[]=0;                
            }
            else
            {
                $checkingFormatFile[]=1;
            }

            $path_file=$path_grup.$nama_baru;
            $path_file_wpg=$path_grup.$nama_baru2.".webp";
            $baseUrl=base_url();
            $getSizeFile += filesize($lokasi);
            $data["image"]=$baseUrl.$path_file_wpg;
        } 

        // kiriman data
        $data["push_notification_id"]=$push_notification_id;
        $data["notification_id"]=$notification_id;
        $data["type"]=  (int)$type;
        $data["title"]= $title;
        $data["sub_title"]= $subTitle;        
        $data["is_redirect"] = $isDirect==1?true:false;
        $data["start_published"]= $startDate;
        $data["end_published"]= $endDate;
        $data["content"]= $contentData;
        $data["time_published"]= $time;
        $data["updated_by"]=$this->session->userdata('username');
       

       // print_r($data);exit;  

        $oneByteToKbyte=1024;
        $paramMaxSize=500; // hitung per kb
        $paramAllowedSize= 500 * $oneByteToKbyte ;

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else if(array_sum($checkingFormatFile)>0)
        {
            echo $res=json_api(0," Format File Harus jpg atau png");     
        }
        else if($paramAllowedSize <= $getSizeFile)
        {
            
            echo $res=json_api(0," File Tidak boleh lebih dari ".$paramMaxSize." kb");  
        }
        else if(array_sum($checkingFormatTime)>0)
        {
            echo $res=json_api(0," Jam Tidak boleh ada yang sama");     
        }
        else if(array_sum($checkingDate)>0)
        {
            echo $res=json_api(0," Tanggal mulai Anda harus lebih awal dari tanggal akhir Anda");     
        }        
        else
        {
            $urlApi="master_notification/push/update_notification";            
            $sendData=$this->pushNotification->postData($urlApi,$data);

            if($sendData->status==1)
            {
                echo $res=json_api(1, 'Berhasil tambah data');      
                
                if(!empty($filename))
                {
                    // Menyimpan path 
                    // move_uploaded_file($lokasi,$path_file); 
                    file_put_contents($path_file,  file_get_contents($fileHide));

                    // convert to webp
                    $this->convertTowebp($nama_baru,$path_grup,$nama_baru2); 

                    // delete old image
                    $explode= explode("/",$oldPath);
                    $count=count((array)$explode);
                    @unlink($path_grup.$explode[$count - 1]);      
                }
            }
            else
            {
                echo $res=json_api(0, $sendData->message,$sendData);        
            }
            
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'news/pushNotification/action_edit';
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
            'push_notification_id' => $id,
            'type' => (int)$type,
            'status' => -5,
            'updated_by'=>$this->session->userdata('username'),
        );
        // print_r(json_encode($data)); exit;     
        $urlApi="master_notification/push/update_status_notification";            
        $sendData=$this->pushNotification->postData($urlApi,$data);

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
        $logUrl      = site_url().'news/pushNotification/action_delete';
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
            'push_notification_id' => $id,
            'status' => (int)$status,
            'type'=>(int)$type,
            'updated_by'=>$this->session->userdata('username'),
        );
        // print_r(json_encode($data)); exit;     
        $urlApi="master_notification/push/update_status_notification";            
        $sendData=$this->pushNotification->postData($urlApi,$data);

        if($sendData->status==1){
            $response = json_api(1,'Update Status Berhasil');
        }else{
            $response = json_api(0,$sendData->message,$sendData->data); 
        }

        /* Fungsi Create Log */
        $this->log_activitytxt->createLog($this->_username, uri_string().'news/pushNotification/change_status', 'change_status', json_encode($data), $response); 

        echo $response;
    }

    public function getDataUraian(){
        validate_ajax();

        $post = $this->input->post();
        $type=trim($this->input->post('type'));
        $data["type"]=(int)$type;

        $url="master_notification/push/list_description";
        $getList = $this->pushNotification->postData($url,$data);

        echo json_encode($getList); 

    }


    public function getDataType(){

        $data[""]="Pilih";
        $data["1"]="Info";
        $data["2"]="Promo";
        $data["3"]="Berita";

        return $data;
    }

    public function getDataTimebck(){

        $start =00;
        $end = 23;

        for ($i = $start; $i <= $end; $i++) {
         $data[date("H:00", mktime($i+1))] = date("H:00", mktime($i+1));
        }
        asort($data); 

        return $data;
    }

    public function getDataTime(){

        $start =strtotime(date("00:00"));
        $end = strtotime('23:59');//
        $data = array();
        $timeMinutes = $this->getTimeFrekuensi()->param_value;

        while ($start <= $end)
        {
            $data[date('H:i',$start )] = date('H:i',$start );
            $start = strtotime('+'.$timeMinutes.' minutes',$start);

        }
        asort($data);

        return $data;
    }



    function convertTowebp($new_name, $path_grup, $new_name2, $count=0, $quality = 100)
    {
        $dir=$path_grup;
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
        $getParameter=$this->pushNotification->select_data("app.t_mtr_custom_param"," where status=1 and param_name='max_img_news' ")->row();

        return $getParameter;
    }

    // function getParamMax()
    // {
    //     $getParameter=$this->pushNotification->select_data("app.t_mtr_custom_param"," where status=1 and param_name='paramMaxFrekuensi' ")->row();

    //     return $getParameter;
    // }

    // function getParamMin()
    // {
    //     $getParameter=$this->pushNotification->select_data("app.t_mtr_custom_param"," where status=1 and param_name='min_frekuensi' ")->row();

    //     return $getParameter;
    // } 

    function getTimeFrekuensi()
    {
        $getParameter=$this->pushNotification->select_data("app.t_mtr_custom_param"," where status=1 and param_name='time_frekuensi' ")->row();

        return $getParameter;
    } 



    
}
