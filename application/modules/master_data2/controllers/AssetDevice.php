<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class AssetDevice extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('assetDeviceModel','asset');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_assets_device';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data2/assetDevice';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->asset->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Gambar Aset Perangkat',
            'content'  => 'assetDevice/index',
            'port' => $this->asset->getPortIdIndex()["data"],
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Gambar Aset Perangkat';
        $data['fileType'] = $this->asset->getFilleType();
        $data['getModule'] = $this->asset->getModule()["data"];
        $data['port'] = $this->asset->getPortId()["data"];

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $module = $this->enc->decode(trim($this->input->post("module")));
        $port = $this->enc->decode(trim($this->input->post("port")));
        $start_date = trim($this->input->post("start_date"));
        $desc = $this->input->post("desc[]");
        // $name= $this->input->post("name[]");
        $fileType = $this->enc->decode($this->input->post("fileType"));
        $upload = $_FILES['fileUpload']['name'];

        $_POST['fileType']=$fileType;
        $_POST['module']=$module;
        $_POST['port']=$port;

        // validation array name and desc
        // foreach ($name as $key => $value) {
        //     $this->form_validation->set_rules('name['.$key.']', 'nama ', 'required|max_length[150]');
        //     $this->form_validation->set_rules('desc['.$key.']', 'Keterangan ', 'required|max_length[250]');
        // }

        foreach ($desc as $key => $value) {
            $this->form_validation->set_rules('desc['.$key.']', 'Keterangan ', 'required|max_length[250]');
        }        

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('module', 'Module ', 'required|max_length[150]|callback_letter_number_val');
        $this->form_validation->set_rules('fileType', 'Tipe File ', 'required');
        $this->form_validation->set_rules('start_date', 'Tanggal Berlaku ', 'required|callback_validate_date_time_minutes');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('is_natural','%s harus angka!');
        $this->form_validation->set_message('is_natural_no_zero','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');
        $this->form_validation->set_message('letter_number_val','%s Tidak Boleh ada Karakter Khusus!');    
        $this->form_validation->set_message('validate_date_time_minutes',' Format  %s tidak valid!');    

        if($this->form_validation->run()===false)
        {
            $errors = array_unique($this->form_validation->error_array());
            echo $res=json_api(0,implode("<br>",$errors));
            exit;
        }

        if(count($upload)<1)
        {
            echo $res=json_api(0,"Gambar/ Video tidak boleh kosong");
            exit;
        }

        // set config file
        $setPath = './uploads/assets_device/';

        $config['upload_path']          = $setPath;
        $config['overwrite']            = true;
        $config['allowed_types']        = 'jpeg|jpg|png';
        $config['max_size']             = 2000;

        // jika tipe filenya video
        if($fileType == 2)
        {
            $config['upload_path']          = $setPath;
            $config['overwrite']            = true;
            $config['allowed_types']        = 'mp4';
            $config['max_size']             = 3000;            
        }

        foreach ($upload as $keyUpload => $valueUpload) {

            $_FILES['file']['name'] = $_FILES['fileUpload']['name'][$keyUpload];
            $_FILES['file']['type'] = $_FILES['fileUpload']['type'][$keyUpload];
            $_FILES['file']['tmp_name'] = $_FILES['fileUpload']['tmp_name'][$keyUpload];
            $_FILES['file']['error'] = $_FILES['fileUpload']['error'][$keyUpload];
            $_FILES['file']['size'] = $_FILES['fileUpload']['size'][$keyUpload];
            
            $this->load->library('upload', $config);     
            if (!$this->upload->do_upload('file')) {

                $errType = '<p>The filetype you are attempting to upload is not allowed.</p>';
                $errSize = '<p>The file you are attempting to upload is larger than the permitted size.</p>';
                $uploadMsg = $this->upload->display_errors();
                if($errSize == $uploadMsg)
                {
                    $getSize = $config['max_size'] /1000;
                    echo $res=json_api(0, " Ukuran Maximal ".$getSize." MB ");
                }
                else if( $errType == $uploadMsg)
                {
                    echo $res =json_api(0, " Format diperbolehkan ".str_replace("|",",",$config['allowed_types']));
                }
                else
                {
                    echo $res =json_api(0, $uploadMsg);
                }
                exit;
            }
            else
            {
                // ketika lolos validasi maka hapus file aslinya
                @unlink($setPath.$_FILES['file']['name']);
            }        
        }
        $createCode = $this->createCode();        
        $data = array(
                    'module'=>$module,
                    'port_id'=>$port,
                    'start_date'=>$start_date,
                    'file_type'=>$fileType,
                    'group_code_assets'=>$createCode,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    "ip_local" => $this->config->item('ip_local')
                    );

        
        // ceck data jika username sudah ada
        $check=$this->asset->select_data($this->_table," where upper(module)=upper('".$this->db->escape_str($module)."') and start_date='".$start_date."' and port_id=".$port." and status not in (-5) ");

        if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Tidak diperbolehkan tanggal berlaku dengan module yang sama dalam satu pelabuhan");
        }
        else
        {
            $this->db->trans_begin();
            $dataUpload=[];
            $idx=1;
            foreach ($upload as $keyUpload2 => $valueUpload2) {

                $_FILES['file']['name'] = $_FILES['fileUpload']['name'][$keyUpload2];
                $_FILES['file']['type'] = $_FILES['fileUpload']['type'][$keyUpload2];
                $_FILES['file']['tmp_name'] = $_FILES['fileUpload']['tmp_name'][$keyUpload2];
                $_FILES['file']['error'] = $_FILES['fileUpload']['error'][$keyUpload2];
                $_FILES['file']['size'] = $_FILES['fileUpload']['size'][$keyUpload2];

                $newName = str_replace(array(" ","'",'"',"-"),"_",pathinfo($_FILES['fileUpload']['name'][$keyUpload2],PATHINFO_FILENAME))."_".date("YmdHis");

                $getPath= $setPath.$newName.".".pathinfo($_FILES['fileUpload']['name'][$keyUpload2],PATHINFO_EXTENSION);                               
                
                $this->load->library('upload'); 
                $config['file_name']            = $newName;
                $this->upload->initialize($config);    
                //   aksi upload data
                $this->upload->do_upload('file');

                $data['path'] = $getPath;
                $data['order'] = $idx;
                // $data['name']=trim($name[$keyUpload2]);
                $data['name']=$newName.".".pathinfo($_FILES['fileUpload']['name'][$keyUpload2],PATHINFO_EXTENSION);
                $data['desc']=trim($desc[$keyUpload2]);
                $dataUpload[] = $data;
                $idx++;
            }
            // print_r($dataUpload); exit;
            $this->asset->insert_data_batch($this->_table,$dataUpload);
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

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data2/assetDevice/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);
        $detail = $this->asset->select_data($this->_table,"where group_code_assets='".$id_decode."' 
        and status=1 ")->result();
        $getModule = $this->asset->getModule($detail[0]->module);
        $getPort = $this->asset->getPortId($detail[0]->port_id);

        $data['title'] = 'Edit Gambar Aset Perangkat';
        $data['id'] = $id;
        $data['fileType'] = $this->asset->getFilleType();
        $data['detail'] = $detail;
        $data['getModule'] = $getModule["data"];
        $data['getModuleSelected'] = $getModule["selected"];
        $data['port'] = $getPort["data"];
        $data['portSelected'] = $getPort["selected"];

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $code = $this->enc->decode($this->input->post("code"));
        $module = $this->enc->decode(trim($this->input->post("module")));
        $port = $this->enc->decode(trim($this->input->post("port")));
        $start_date = trim($this->enc->decode($this->input->post("start_date")));
        $desc = $this->input->post("desc[]");
        $name= $this->input->post("name[]");
        $id= $this->input->post("id[]");
        $path= $this->input->post("path[]");
        $uploadUpdate = @$_FILES['fileUploadUpdate']['name'];
        $upload = @$_FILES['fileUpload']['name'];
        $fileType = $this->enc->decode($this->input->post("fileType"));

        // print_r($name); exit;
        $_POST['code']=$code;
        $_POST['start_date']=$start_date;
        $_POST['fileType']=$fileType;
        $_POST['module']=$module;
        $_POST['port']=$port;

        // validation array name and desc
        // foreach ($name as $key => $value) {
        //     $this->form_validation->set_rules('name['.$key.']', 'nama ', 'required|max_length[150]');
        //     $this->form_validation->set_rules('desc['.$key.']', 'Keterangan ', 'required|max_length[250]');
        // }
        foreach ($desc as $key => $value) {
            $this->form_validation->set_rules('desc['.$key.']', 'Keterangan ', 'required|max_length[250]');
        }                

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('module', 'Module ', 'required|max_length[150]|callback_letter_number_val');
        $this->form_validation->set_rules('fileType', 'Tipe File ', 'required');
        $this->form_validation->set_rules('start_date', 'Tanggal Berlaku ', 'required');


        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('is_natural','%s harus angka!');
        $this->form_validation->set_message('is_natural_no_zero','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');
        $this->form_validation->set_message('letter_number_val','%s Tidak Boleh ada Karakter Khusus!');    

        if($this->form_validation->run()===false)
        {
            $errors = array_unique($this->form_validation->error_array());
            echo $res=json_api(0,implode("<br>",$errors));
            exit;
        }
        // set config file
        $setPath = './uploads/assets_device/';

        $config['upload_path']          = $setPath;
        $config['overwrite']            = true;
        $config['allowed_types']        = 'jpeg|jpg|png';
        $config['max_size']             = 2000;

        // jika tipe filenya video
        if($fileType == 2)
        {
            $config['upload_path']          = $setPath;
            $config['overwrite']            = true;
            $config['allowed_types']        = 'mp4';
            $config['max_size']             = 3000;            
        }        

        // check jika ada gambar yang di update maka gambar akan mengecek validasinya
        $dataArr = [];
        $dataUnlink =[];
        if(!empty($id))
        {
            foreach ($id as $idKey => $idValue) {
                if(!empty($_FILES['fileUploadUpdate']['name'][$idKey]))
                {
                    $dataUnlink[]=$path[$idKey];
                    $_FILES['file']['name'] = $_FILES['fileUploadUpdate']['name'][$idKey];
                    $_FILES['file']['type'] = $_FILES['fileUploadUpdate']['type'][$idKey];
                    $_FILES['file']['tmp_name'] = $_FILES['fileUploadUpdate']['tmp_name'][$idKey];
                    $_FILES['file']['error'] = $_FILES['fileUploadUpdate']['error'][$idKey];
                    $_FILES['file']['size'] = $_FILES['fileUploadUpdate']['size'][$idKey];

                    $_FILES['file']['nameFile'] = $name[$idKey];
                    $_FILES['file']['description'] = $desc[$idKey];
                    
                    $this->load->library('upload', $config);     
                    if (!$this->upload->do_upload('file')) {
                        // echo $res=json_api(0, $this->upload->display_errors());
                        $errType = '<p>The filetype you are attempting to upload is not allowed.</p>';
                        $errSize = '<p>The file you are attempting to upload is larger than the permitted size.</p>';
                        $uploadMsg = $this->upload->display_errors();
                        if($errSize == $uploadMsg)
                        {
                            $getSize = $config['max_size'] /1000;
                            echo $res=json_api(0, " Ukuran Maximal ".$getSize." MB ");
                        }
                        else if( $errType == $uploadMsg)
                        {
                            echo $res =json_api(0, " Format diperbolehkan ".str_replace("|",",",$config['allowed_types']));
                        }
                        else
                        {
                            echo $res =json_api(0, $uploadMsg);
                        }
                        exit; 
                    }
                    else
                    {
                        // ketika lolos validasi maka hapus file aslinya
                        @unlink($setPath.$_FILES['file']['name']);                        
                    }    
                    // name di set kosong agar bisa di set otomatis nanti sesui nama field yang di rubah
                    $_FILES['file']['nameFile'] ="";
                    $dataArr[]=$_FILES['file'];              
                }
                else
                {
                    $dataArr[] = array("path"=>$path[$idKey],
                                                'nameFile'=> $name[$idKey],
                                                'description' => $desc[$idKey],
                    );
                }
                
            }
        }
        
        // check jika ada gambar yang di tambahkan
        if(!empty($upload))
        {
            foreach ($upload as $keyUpload => $valueUpload) {

                $_FILES['file2']['name'] = $_FILES['fileUpload']['name'][$keyUpload];
                $_FILES['file2']['type'] = $_FILES['fileUpload']['type'][$keyUpload];
                $_FILES['file2']['tmp_name'] = $_FILES['fileUpload']['tmp_name'][$keyUpload];
                $_FILES['file2']['error'] = $_FILES['fileUpload']['error'][$keyUpload];
                $_FILES['file2']['size'] = $_FILES['fileUpload']['size'][$keyUpload];

                // $_FILES['file2']['nameFile'] = $name[$keyUpload];
                // name di set kosong agar bisa di set otomatis nanti sesui nama field yang di rubah
                $_FILES['file2']['nameFile'] = "";
                $_FILES['file2']['description'] = $desc[$keyUpload];
                
                $this->load->library('upload', $config);     
                if (!$this->upload->do_upload('file2')) {
                    echo $res=json_api(0, $this->upload->display_errors());
                    exit;
                }
                else
                { // ketika lolos validasi maka hapus file aslinya
                    @unlink($setPath.$_FILES['file2']['name']);
                }

                $dataArr[]=$_FILES['file2'];    
            }            
        }

        $data = array(
                'module'=>$module,
                'start_date'=>$start_date,
                'file_type'=>$fileType,
                'port_id'=>$port,
                'group_code_assets'=>$code,
                'status'=>1,
                'created_by'=>$this->session->userdata('username'),
                'created_on'=>date("Y-m-d H:i:s"),
                "ip_local" =>$this->config->item('ip_local'),
            );
        
        // print_r($dataArr); exit;
        $check=$this->asset->select_data($this->_table," where upper(module)=upper('".$this->db->escape_str($module)."') and start_date='".$start_date."' and port_id=".$port." and status not in (-5)  and group_code_assets !='".$code."' ");
        
        // print_r($dataUnlink); exit;
        if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Module sudah ada");
        }
        else
        {
            $idx=1;
            foreach ($dataArr as $dataArrKey => $dataArrValue) {
                if(empty($dataArrValue['path']))
                {
                    $_FILES['file3']['name'] = $dataArrValue['name'];
                    $_FILES['file3']['type'] = $dataArrValue['type'];
                    $_FILES['file3']['tmp_name'] = $dataArrValue['tmp_name'];
                    $_FILES['file3']['error'] = $dataArrValue['error'];
                    $_FILES['file3']['size'] = $dataArrValue['size'];
    
                    $newName = str_replace(array(" ","'",'"',"-"),"_",pathinfo($dataArrValue['name'],PATHINFO_FILENAME))."_".date("YmdHis");
    
                    $getPath= $setPath.$newName.".".pathinfo($dataArrValue['name'],PATHINFO_EXTENSION);                               
                    
                    $this->load->library('upload'); 
                    $config['file_name']            = $newName;
                    $this->upload->initialize($config);    
                    //   aksi upload data
                    $this->upload->do_upload('file3');
    
                    $data['path'] = $getPath;
                    $data['name'] = $newName.".".pathinfo($dataArrValue['name'],PATHINFO_EXTENSION);    

                }
                else
                {
                    $data['path'] = $dataArrValue['path'];     
                    $data['name'] = trim($dataArrValue['nameFile']);
                }

                $data['order'] = $idx;
                $data['desc']=trim($dataArrValue['description']);
                $dataUpload[] = $data;
                $idx++;
            }

            // print_r($dataUpload); exit;
            $getPathDb = $this->asset->select_data($this->_table," where group_code_assets='".$code."' and status=1 ");

            $dataSoftDelete = array( 
                    "status"=>'-5',
                    "updated_on"=>date("Y-m-d H:i:s"),
                    "updated_by"=>$this->session->userdata("username"),
                );
            $this->db->trans_begin();

            // soft delete data
            $this->asset->update_data($this->_table,$dataSoftDelete ,"group_code_assets='".$code."'");

            // insert data baru
            $this->asset->insert_data_batch($this->_table,$dataUpload);

            if ($this->db->trans_status() === FALSE)
            {   
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data');
                // jika data yang telah ada sudah tidak sama dengan total yang di edit (data yg sudah ada di db)
                if(count((array)$path) <> $getPathDb->num_rows())
                {
                    $getPath = $getPathDb->result();
                    $arrayDiff = array_diff(array_column($getPath,"path"),(array)$path);

                    // print_r($arrayDiff); exit;
                    foreach ($arrayDiff as $pathKey2 => $pathValue2) {
                         @unlink($pathValue2);
                    }
                }

                // jika ada salah satu data yang terkena edit maka hapus data source yang di isi
                if(count($dataUnlink)>0)
                {
                    foreach ($dataUnlink as $pathKey2 => $pathValue2) {
                        @unlink($pathValue2);
                   }
                }
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data2/assetDevice/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /* data */
        $data = array(
            'status' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->asset->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            if ($d[1]==1)
            {
                echo $res=json_api(0, 'Gagal aktif');
            }
            else
            {
                echo $res=json_api(0, 'Gagal non aktif');
            }
            
        }
        else
        {
            $this->db->trans_commit();
            if ($d[1]==1)
            {
                echo $res=json_api(1, 'Berhasil aktif data');
            }
            else
            {
                echo $res=json_api(1, 'Berhasil non aktif data');
            }
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/bank/action_change';
        $d[1]==1?$logMethod='ENABLE':$logMethod='DISABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data=array(
            'status'=>-5,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $id = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->asset->update_data($this->_table,$data," id='".$id."'");

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal delete data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil delete data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/bank/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function createCode()
    {
        $getIdentity = $this->asset->select_data("app.t_mtr_identity_app", "")->row();
        $front_code="ADC".sprintf("%02s", $getIdentity->port_id).date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_assets_device where left(group_code_assets,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $code=$front_code."0001";
            return $code;
        }
        else
        {
            $max=$this->db->query("select max (group_code_assets) as max_code from app.t_mtr_assets_device where left(group_code_assets,".$total_length.")='".$front_code."' ")->row();

            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }    



}
