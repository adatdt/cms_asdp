<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class VideoPids extends MY_Controller{
  public function __construct(){
    parent::__construct();

        logged_in();
        $this->load->model('VideoPidsModel','video');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_pids_video';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pids/videoPids';
  }

  public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->video->dataList();
            echo json_encode($rows);
            exit;
        }


        $port=$this->video->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();

        $dataPort[]="Pilih";
        foreach ($port as $key => $value) 
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);    
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Video PIDS',
            'content'  => 'videoPids/index',
            "port" =>$dataPort, 
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

    $this->load->view('default', $data);
  }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=$this->video->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();

        $dataPort[""]="Pilih";
        foreach ($port as $key => $value) 
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);    
        }

        $data['title'] = 'Tambah Video PIDS ';
        $data['port']=$dataPort;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=$this->enc->decode($this->input->post('port'));
        $ordering=trim($this->input->post('ordering'));
        $filename = $_FILES['fileName']['name'];      

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('ordering', 'Urutan ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s Harus angka!');

        $checking_format_file[]=0;
        $nama_baru="";
        if(!empty($filename))
        {
            $lokasi = $_FILES['fileName']['tmp_name'];
            $extensi = pathinfo($filename, PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
            $nama_baru=str_replace(" ","_",$port)."_".date("YmdHis").".".$extensi;

            $getParamFormat=$this->video->select_data ("app.t_mtr_custom_param_pids"," where param_name='videos_upload_format' ")->row();

            $explodeFormat=explode(",", $getParamFormat->param_value);
            foreach ($explodeFormat as $key => $value) {
                if($extensi==$value)
                {
                    $checking_format_file[]=1; // jika ada format yang terdaftar maka akan ada satu                
                }
                else
                {
                    $checking_format_file[]=0;
                }
            }

        }

        // path kirim data
        $path_file="./uploads/video_pids/".$nama_baru;

        $data=array(
            "port_id"=>$port,
            "order"=>$ordering,
            "path"=>$path_file,
            "created_on"=>date("Y-m-d H:i:s"),
            "created_by"=>$this->session->userdata("username"),
        );

        // check order
        $check=$this->video->select_data($this->_table, " where port_id={$port} and ".'"order"='."'{$ordering}' and status<>'-5' ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if (empty($filename))
        {
            echo $res=json_api(0," File tidak boleh kosong. ");
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0," Urutan Sudah Ada. ");
        }
        else if(array_sum($checking_format_file)<1 and !empty($filename))
        {
            echo $res=json_api(0," Format Harus ".$getParamFormat->param_value);   
        }
        else
        {

            $this->db->trans_begin();

            $this->video->insert_data($this->_table,$data);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();
                $returnData = array("portId"=>$port);
                move_uploaded_file($lokasi,$path_file); 
                echo $res=json_api(1, 'Berhasil tambah data', $returnData);
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/videoPids/action_add';
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

        $detail=$this->video->select_data($this->_table, " where id={$id_decode} ")->row();

        $port=$this->video->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();

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

        $data['title'] = 'Edit Video PIDS';
        $data['id'] = $id;
        $data['port']=$dataPort;
        $data['portSelected']=$dataPortSelected;
        $data['detail']=$detail;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id'));

         // $port=$this->enc->decode($this->input->post('port'));
        $ordering=trim($this->input->post('ordering'));
        $filename = empty($_FILES['fileName']['name'])?"":$_FILES['fileName']['name'];      

        // $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('ordering', 'Urutan ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('numeric','%s Harus angka!');

        $getDetail=$this->video->select_data($this->_table, " where id=".$id)->row();
        $port=$getDetail->port_id;

        $checking_format_file[]=0;
        $nama_baru="";
        if(!empty($filename))
        {
            $lokasi = $_FILES['fileName']['tmp_name'];
            $extensi = pathinfo($filename, PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
            $nama_baru=str_replace(" ","_",$port)."_".date("YmdHis").".".$extensi;


            $getParamFormat=$this->video->select_data ("app.t_mtr_custom_param_pids"," where param_name='videos_upload_format' ")->row();

            $explodeFormat=explode(",", $getParamFormat->param_value);
            foreach ($explodeFormat as $key => $value) {
                if($extensi==$value)
                {
                    $checking_format_file[]=1; // jika ada format yang terdaftar maka akan ada satu                
                }
                else
                {
                    $checking_format_file[]=0;
                }
            }

            // path kirim data
            $path_file="./uploads/video_pids/".$nama_baru;

            $data["path"]=$path_file;
        }


        // $data["port_id"]=$port;
        $data["order"]=$ordering;            
        $data["updated_on"]=date("Y-m-d H:i:s");
        $data["updated_by"]=$this->session->userdata("username");


        // check order
        $check=$this->video->select_data($this->_table, " where port_id={$port} and ".'"order"='."'{$ordering}' and status<>'-5' and id<>{$id} ");        

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0," Urutan Sudah Ada. ");
        }
        else if(array_sum($checking_format_file)<1 and !empty($filename))
        {
            echo $res=json_api(0," Format Harus ".$getParamFormat->param_value);   
        }

        else
        {

            $this->db->trans_begin();

            $this->video->update_data($this->_table,$data,"id=$id");

            if ($this->db->trans_status() === FALSE)
            {   
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->db->trans_commit();

                if(!empty($filename))
                {
                    // Menyimpan path 
                    move_uploaded_file($lokasi,$path_file); 

                    if(!empty($getDetail->path))
                    {
                        unlink($getDetail->path);
                    }
                }

                $port = $this->video->select_data($this->_table, " where id=".$id)->row()->port_id;
                $returnData = array("portId"=>$port);
                echo $res=json_api(1, 'Berhasil edit data', $returnData);
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/videoDisplay/action_edit';
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
        $this->video->update_data($this->_table,$data,"id=".$d[0]);
        $port = $this->video->select_data($this->_table, " where id=".$d[0])->row()->port_id;
        $returnData = array("portId"=>$port);           

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            if ($d[1]==1)
            {
                echo $res=json_api(0, 'Gagal aktif',$returnData);
            }
            else
            {
                echo $res=json_api(0, 'Gagal non aktif',$returnData);
            }
            
        }
        else
        {
            $this->db->trans_commit();
            $port = $this->video->select_data($this->_table, " where id=".$d[0])->row()->port_id;
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
        $this->video->update_data($this->_table,$data," id='".$id."'");

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal delete data');
        }
        else
        {
            $this->db->trans_commit();
            $port = $this->video->select_data($this->_table, " where id=".$id)->row()->port_id;
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

        $getData=$this->video->select_data($this->_tableDetail," where ads_display_code='{$adsCode}' and status=1 order by".'"order" asc')->result();
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
