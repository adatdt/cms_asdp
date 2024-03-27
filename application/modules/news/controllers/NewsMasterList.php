<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class NewsMasterList extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('newsMasterListModel','newsMasterList');
        $this->load->model('global_model');

        $this->_table    = '';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'news/newsMasterList';
        $this->_pathFile  ="uploads/news/berita/";
	}

	public function index__12092022(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->newsMasterList->getDataList();
            $rows["tokenHash"] = $this->security->get_csrf_hash();
            $rows["csrfName"] = $this->security->get_csrf_token_name();
            echo json_encode($rows);
            exit;
        }

        $url=site_url($this->_module.'/add');
        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master Daftar Berita',
            'content'  => 'NewsMasterList/index',
            'btn_add'  => generate_button($this->_module, 'add', '<button onclick=" showModalNew2(\''.$url.'\')" class="btn btn-sm btn-warning" title="Tambah"><i class="fa fa-plus"></i> Tambah</button> ')
        );

		$this->load->view('default', $data);
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->newsMasterList->getDataList();
            echo json_encode($rows);
            exit;
        }

        $url=site_url($this->_module.'/add');
        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master Daftar Berita',
            'content'  => 'newsMasterList/index',
            'btn_add'  => generate_button($this->_module, 'add', '<button onclick=" showModalNew2(\''.$url.'\')" class="btn btn-sm btn-warning" title="Tambah"><i class="fa fa-plus"></i> Tambah</button> ')
        );

		$this->load->view('default', $data);
	}
    

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $dataType = $this->newsMasterList->getDataType();
        $type[""] = "Pilih";
        foreach ($dataType as $key => $value) {
            $type[$this->enc->encode($key)] = $value ;
        }

        $data['title'] = 'Tambah Master Daftar Berita';
        $data['getDataType']=$type;
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $type=trim($this->enc->decode($this->input->post('type')));
        $title=trim($this->input->post('title'));
        $contentData=trim(base64_decode($this->input->post('contentData')));

        // echo $filename; exit;
        
        /* validation */
        // $this->form_validation->set_rules('type', 'Tipe', 'trim|required');
        $this->form_validation->set_rules('title', 'Judul', 'trim|required');
        $this->form_validation->set_rules('type', 'Tipe', 'trim|required');
        $this->form_validation->set_rules('contentData', 'Sub Judul', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        /* data post */


        // echo $path_file; exit;

        $data = array(
            "type"=> (int)$type,
            "title"=>$title,
            "is_redirect"=>true,
            "created_by"=>$this->session->userdata('username'),
            "sub_title"=>$contentData
        );

        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else
        {
            
            $urlApi="master_notification/transaction/create_notification";            
            $sendData=$this->newsMasterList->postData($urlApi,$data);

            if($sendData->status==1)
            {
                echo $res=json_api(1, 'Berhasil tambah data');        
            }
            else
            {
                echo $res=json_api(0, $sendData->message,$sendData);        
            }
            
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'news/newsMasterList/action_add';
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
            "transaction_id"=> $id
        );

        // print_r(json_encode($data)); exit;     
        $urlApi="master_notification/transaction/detail_notification";            
        $sendData=$this->newsMasterList->postData($urlApi,$data);

        $getDataType=$this->newsMasterList->getDataType();
        $detail = $sendData->data;

        // print_r($detail); exit;
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

        $data['title'] = 'Edit Berita';
        $data['detail'] = $detail;
        $data['getDataType']=$getDataType2;
        $data['getDataTypeSelected']=$getDataTypeSelected;
        $this->load->view($this->_module.'/edit',$data);
    }

    public function action_edit(){
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=trim($this->enc->decode($this->input->post('id')));
        $type=trim($this->enc->decode($this->input->post('type')));
        $title=trim($this->input->post('title'));
        $contentData=trim(base64_decode($this->input->post('contentData')));



        // /* validation */
        // $this->form_validation->set_rules('type', 'Tipe', 'trim|required');
        $this->form_validation->set_rules('title', 'Judul', 'trim|required');
        $this->form_validation->set_rules('type', 'Tipe', 'trim|required');
        $this->form_validation->set_rules('id', 'Id', 'trim|required');
        $this->form_validation->set_rules('contentData', 'Sub Judul', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        /* data post */

        $data = array(
            "type"=> $type,
            "transaction_id"=> $id,
            "title"=>$title,
            "updated_by"=> $this->session->userdata("username"),
            "is_redirect"=> true,
            "sub_title"=> $contentData
        );        

        // echo json_encode($data); exit;
        
        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        } 
        else
        {
            $urlApi="master_notification/transaction/update_notification";            
            $sendData=$this->newsMasterList->postData($urlApi,$data);

            if($sendData->status==1)
            {
                echo $res=json_api(1, 'Berhasil edit data');      
            
            }
            else
            {
                echo $res=json_api(0, $sendData->message,$sendData);        
            }
            
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'news/newsMasterList/action_edit';
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
            'transaction_id' => $id,
            'type' => (int)$type,
            'status' => -5,
            'updated_by'=>$this->session->userdata('username'),
        );
        // print_r(json_encode($data)); exit;     
        $urlApi="master_notification/transaction/update_status_notification";            
        $sendData=$this->newsMasterList->postData($urlApi,$data);

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
        $logUrl      = site_url().'news/newsMasterList/action_delete';
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
            'transaction_id' => $id,
            'status' => (int)$status,
            'type'=>(int)$type,
            'updated_by'=>$this->session->userdata('username'),
        );
        // print_r(json_encode($data)); exit;     
        $urlApi="master_notification/transaction/update_status_notification";            
        $sendData=$this->newsMasterList->postData($urlApi,$data);

        if($sendData->status==1){
            $response = json_api(1,'Update Status Berhasil');
        }else{
            $response = json_api(0,$sendData->message,$sendData->data); 
        }

        /* Fungsi Create Log */
        $this->log_activitytxt->createLog($this->_username, uri_string().'news/newsMasterList/change_status', 'change_status', json_encode($data), $response); 

        echo $response;
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

    
}
