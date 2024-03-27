<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class AdsDisplay extends MY_Controller{
  public function __construct(){
    parent::__construct();

        logged_in();
        $this->load->model('AdsDisplayModel','ads');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_ads_display';
        $this->_tableDetail    = 'app.t_mtr_ads_display_detail';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pids/adsDisplay';
  }

  public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->ads->dataList();
            echo json_encode($rows);
            exit;
        }


        $port=$this->ads->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();

        $dataPort[]="Pilih";
        foreach ($port as $key => $value) 
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);    
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'ADS Display',
            'content'  => 'adsDisplay/index',
            "port" =>$dataPort, 
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

    $this->load->view('default', $data);
  }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=$this->ads->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();

        $dataPort[""]="Pilih";
        foreach ($port as $key => $value) 
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);    
        }

        $data['title'] = 'Tambah ADS Display';
        $data['port']=$dataPort;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=$this->enc->decode($this->input->post('port'));
        $name=trim(strtolower($this->input->post('name')));
        $duration=trim($this->input->post('duration'));
        $ordering=trim($this->input->post('ordering'));
        $fileName = $_FILES['fileName'];
        $checkFileEmpty=$this->input->post("checkFileEmpty[]");

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('name', 'Nama ', 'required');
        $this->form_validation->set_rules('duration', 'Durasi', 'required');
        $this->form_validation->set_rules('ordering', 'Urutan', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $dataFile=array();
        $emptyFile[]=0;

        $getCode=$this->createCode($port);
        $dataHeader=array(
            "ads_display_code"=>$getCode,
            "port_id"=>$port,
            "ads_name"=>$name,
            "order"=>$ordering,
            "duration"=>$duration,
            "created_on"=>date("Y-m-d H:i:s"),
            "created_by"=>$this->session->userdata("username")
        );

        $checkFormatAllowed=$this->ads->select_data("app.t_mtr_custom_param_pids", " where param_name='ads_upload_format' and port_id='{$port}' and status=1 ");

        $orderingDetail=1;
        $dataDetail=array();
        $dataMoveFile=array();        
        $checking_format_file[]=0;

        $isSetParam[]=0;
        foreach ($checkFileEmpty as $key => $value) {
            
            if(empty($fileName['name'][$key]))
            {
                $emptyFile[]=1;
            }
            else
            {
                $lokasi = $fileName['tmp_name'][$key];
                $extensi = pathinfo($fileName['name'][$key], PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
                $nama_baru=str_replace(" ","_",$name)."_".$orderingDetail."_".date("YmdHis").".".$extensi;



                if($checkFormatAllowed->num_rows()>0)
                {
                    $formatAllowed=$this->getFormatFile(explode(",", $checkFormatAllowed->row()->param_value),$extensi);

                    if($formatAllowed=0)
                    {
                        $checking_format_file[]=1;             
                    }
                }
                else
                {
                    $isSetParam[]=1;
                }

                $pathFile="./uploads/adsdisplay/".$nama_baru;
                $dataDetail[]=array(
                    "ads_display_code"=>$getCode,
                    "path_file"=>$pathFile,
                    "order"=>$orderingDetail,                    
                    "created_on"=>date("Y-m-d H:i:s"),
                    "created_by"=>$this->session->userdata("username")
                );

                $dataMoveFile[]=array(
                    "lokasi"=>$lokasi,
                    "path"=>$pathFile
                );

                $orderingDetail++;
            }
        }

        $data=array("Header"=>$dataHeader, "Detail"=>$dataDetail);


        // ceck order
        $check=$this->ads->select_data($this->_table,'where "order"'."='".strtolower($ordering)."' and port_id='{$port}' and status not in (-5) ");

        $checkName=$this->ads->select_data($this->_table,"where ads_name='".strtolower($name)."' and port_id='{$port}' and status not in (-5) ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        if($check->num_rows()>0)
        {
            echo $res=json_api(0," Urutan Sudah Ada. ");
        }
        else if(array_sum($emptyFile)>0) // tidak boleh ada spasi
        {
            echo $res=json_api(0,"File Tidak Boleh Kosong.");   
        }
        else if(array_sum($checking_format_file)>0)
        {
            echo $res=json_api(0,"Format Harus jpg, png dan jpeg.");     
        }
        else if(preg_match('/\s/',$name)) // tidak boleh ada spasi
        {
            echo $res=json_api(0,"Nama Tidak Boleh ada Sepasi.");   
        }
        else if($checkName->num_rows()>0) // tidak boleh ada spasi
        {
            echo $res=json_api(0,"Nama Tidak Boleh Sama Dalam Satu Pelabuhan.");   
        } 
        else if(array_sum($isSetParam)>0)
        {
            echo $res=json_api(0," Parameter ads_upload_format belum ada.");     
        }               
        else
        {

            $this->db->trans_begin();

            $this->ads->insert_data($this->_table,$dataHeader);
            $this->ads->insert_data_batch($this->_tableDetail,$dataDetail);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();

                foreach ($dataMoveFile as $key => $value) {
                    move_uploaded_file($value['lokasi'],$value['path']); 
                }
                $returnData = array("portId"=>$port);
                echo $res=json_api(1, 'Berhasil tambah data', $returnData);
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/adsDisplay/action_add';
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

        $detail=$this->ads->select_data($this->_table, " where id={$id_decode} ")->row();
        $detailImage=$this->ads->select_data($this->_tableDetail, " where ads_display_code='{$detail->ads_display_code}' ")->result();
        
        $port=$this->ads->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();

        $dataPort[]="Pilih";
        $dataPortSelected="";
        foreach ($port as $key => $value) 
        {
            if($value->id==$detail->port_id)
            {
                $dataPortSelected=$this->enc->encode($value->id);
                $dataPort[$dataPortSelected]=strtoupper($value->name);    
            }
            else
            {

                $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);    
            }
        }


        // print_r($detailImage);exit;

        $data['title'] = 'Edit ADS';
        $data['id'] = $id;
        $data['port']=$dataPort;
        $data['detailImage']=$detailImage;
        $data['portSelected']=$dataPortSelected;
        $data['detail']=$detail;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id'));

        $port=$this->enc->decode($this->input->post('port'));
        $name=trim($this->input->post('name'));
        $duration=trim($this->input->post('duration'));
        $ordering=trim($this->input->post('ordering'));
        $editFile=$this->input->post('editFile[]');


        
        $fileName = $_FILES['fileName'];
        $checkFileEmpty=$this->input->post("checkFileEmpty[]");


        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('name', 'Nama ', 'required');
        $this->form_validation->set_rules('duration', 'Durasi', 'required');
        $this->form_validation->set_rules('ordering', 'Urutan', 'required');
        $this->form_validation->set_rules('id', 'Id ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $getAdsDisplayCode=$this->ads->select_data($this->_table," where id={$id}" )->row();


        $orderingDetail=1;
        $dataDetail=array();
        $dataMoveFile=array();
        $unlinkImage=array();
        $checking_format_file[]=0;
        $emptyFile[]=0;
        foreach ($checkFileEmpty as $key => $value) {
            if(empty($fileName['name'][$key]) and empty($editFile[$key]))// jika tidak ada file path dan tidak ada file yang diisi
            {
                $emptyFile[]=1;
            }
            else if(empty($fileName['name'][$key]) and !empty($editFile[$key]))
            {

                $dataDetail[]=array(
                    "ads_display_code"=>$getAdsDisplayCode->ads_display_code,
                    "path_file"=>$editFile[$key],
                    "order"=>$orderingDetail,                    
                    "created_on"=>date("Y-m-d H:i:s"),
                    "created_by"=>$this->session->userdata("username")
                );

                $orderingDetail++;
            }
            else
            {
                $lokasi = $fileName['tmp_name'][$key];
                $extensi = pathinfo($fileName['name'][$key], PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
                $nama_baru=str_replace(" ","_",$name)."_".$orderingDetail."_".date("YmdHis").".".$extensi;

                if(strtoupper($extensi)=="JPG" or strtoupper($extensi)=="PNG" or strtoupper($extensi)=="JPEG")
                {
                    $checking_format_file[]=0;                
                }
                else
                {
                    $checking_format_file[]=1;
                }

                $pathFile="./uploads/adsdisplay/".$nama_baru;
                $dataDetail[]=array(
                    "ads_display_code"=>$getAdsDisplayCode->ads_display_code,
                    "path_file"=>$pathFile,
                    "order"=>$orderingDetail,                    
                    "created_on"=>date("Y-m-d H:i:s"),
                    "created_by"=>$this->session->userdata("username")
                );

                $dataMoveFile[]=array(
                    "lokasi"=>$lokasi,
                    "path"=>$pathFile
                );

                if(!empty($fileName['name'][$key]) and !empty($editFile[$key]))
                {
                    $unlinkImage[]=$editFile[$key];
                }                

                $orderingDetail++;
            }
        }        
        
        $dataHeader=array(
            "port_id"=>$port,
            // "ads_name"=>$name,
            "order"=>$ordering,
            "duration"=>$duration,
            "updated_on"=>date("Y-m-d H:i:s"),
            "updated_by"=>$this->session->userdata("username")
        );

        // ceck order
        $check=$this->ads->select_data($this->_table,'where "order"'."='".strtolower($ordering)."' and port_id='{$port}' and status not in (-5) and id<>{$id} ");


        // print_r($dataHeader); exit;
        $data=array("Header"=>$dataHeader, "Detail"=>$dataDetail);

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        if($check->num_rows()>0)
        {
            echo $res=json_api(0," Urutan Sudah Ada. ");
        }
        else if(array_sum($emptyFile)>0) // tidak boleh ada spasi
        {
            echo $res=json_api(0,"File Tidak Boleh Kosong.");   
        }        
        else if(array_sum($checking_format_file)>0)
        {
            echo $res=json_api(0,"Format Harus jpg, png dan jpeg.");     
        }                
        else
        {

            $this->db->trans_begin();

            $this->ads->update_data($this->_table,$dataHeader,"id=$id");
            
            
            $updateDataDetail=array(
                "status"=>'-5',
                "updated_on"=>date("Y-m-d H:i:s"),
                "updated_by"=>$this->session->userdata("username")
            );
            // update detail
            $this->ads->update_data($this->_tableDetail,$updateDataDetail,"  ads_display_code='{$getAdsDisplayCode->ads_display_code}' and status=1 "); 

            $this->ads->insert_data_batch($this->_tableDetail,$dataDetail);


            if ($this->db->trans_status() === FALSE)
            {   
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->db->trans_commit();

                if($dataMoveFile)
                {
                    foreach ($dataMoveFile as $key => $value) {
                        move_uploaded_file($value['lokasi'],$value['path']); 
                    }

                }

                // delete sourche
                if($unlinkImage)
                {
                    foreach ($unlinkImage as $key => $value) {
                        @unlink($value);
                    }

                }
                
                $returnData = array("portId"=>$port);
                echo $res=json_api(1, 'Berhasil edit data',$returnData);
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/adsDisplay/action_edit';
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
        $this->ads->update_data($this->_table,$data,"id=".$d[0]);

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
            $port = $this->ads->select_data($this->_table, " where id=".$d[0])->row()->port_id;
            $returnData = array("portId"=>$port);
            if ($d[1]==1)
            {
                echo $res=json_api(1, 'Berhasil aktif data', $returnData);
            }
            else
            {
                echo $res=json_api(1, 'Berhasil non aktif data', $returnData);
            }

            

        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/labelPids/action_change';
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
        $this->ads->update_data($this->_table,$data," id='".$id."'");

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal delete data');
        }
        else
        {
            $this->db->trans_commit();
            $port = $this->ads->select_data($this->_table, " where id=".$id)->row()->port_id;
            $returnData = array("portId"=>$port);
            echo $res=json_api(1, 'Berhasil delete data', $returnData);
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/labelPids/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function getDetailFile()
    {
        $adsCode=$this->input->post("adsCode");

        $getData=$this->ads->select_data($this->_tableDetail," where ads_display_code='{$adsCode}' and status=1 order by".'"order" asc')->result();
        echo json_encode($getData);
    }    

    public function getFormatFile($data, $valueData)
    {
        $getData[]=0;
        foreach ($data as $key => $value) {
            if(strtoupper($value)==strtoupper($valueData))
            {
                $getData[]=1;   
            }
        }

        
        $return= array_sum($getData)>0?1:0;

        return $return;

    }


    function createCode($port)
    {
        $front_code="ADS".sprintf("%02s", $port)."".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_ads_display where left(ads_display_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (ads_display_code) as max_code from app.t_mtr_ads_display where left(ads_display_code,".$total_length.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

}
