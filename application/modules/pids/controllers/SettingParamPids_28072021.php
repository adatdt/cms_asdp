<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class SettingParamPids extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('SettingParamPidsModel','param');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_custom_param_pids';
        $this->_username = $this->session->userdata('username');
        $this->_module   = $this->_module = 'pids/settingParamPids';
        $this->_maxsize = 5120; //max size value for upload refund (param_name max_file_size_refund)
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->param->dataList();
            echo json_encode($rows);
            exit;
        }

        $port=$this->getPort(); 

        $portData[""]="Pilih";
        foreach ($port as $key => $value) 
        {
            $portData[$this->enc->encode($value->id)]=strtoupper($value->name);
        }



        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Seting Parameter',
            'kategori' => $this->param->select_data("app.t_mtr_custom_param_category", "where status = '1' and category_name <> 'secret' order by category_name asc")->result(),
            'content'  => 'settingParamPids/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'port' => $portData,

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=$this->getPort(); 

        $portData[""]="Pilih";
        foreach ($port as $key => $value) 
        {
            $portData[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $data = array (
            'title' => 'Tambah Seting Parameter PIDS',
            'port' => $portData
            );

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add_27072021()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $nama_param=trim($this->input->post('name'));
        $value_param=trim($this->input->post('value_param'));
        $tipe_param=trim($this->input->post('tipe_param'));
        $info=trim($this->input->post('info'));
        $port=trim($this->enc->decode($this->input->post('port')));


        $this->form_validation->set_rules('name', 'Nama', 'required');
        $this->form_validation->set_rules('value_param', 'Value Parameter ', 'required');
        $this->form_validation->set_rules('tipe_param', 'Tipe Parameter', 'required');
        $this->form_validation->set_rules('info', 'Info', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        
        $data=array(
                    'param_name'=>$nama_param,
                    'param_value'=>$value_param,
                    'param_type'=>$tipe_param,
                    'port_id'=>$port,
                    'info'=>$info,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );


        // ceck data jika nama param sudah ada

        $check=$this->param->select_data($this->_table," where upper(param_name)=upper('".$nama_param."') and port_id={$port} and status not in (-5) ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        if(preg_match('/\s/',$nama_param)) // tidak boleh ada spasi
        {
            echo $res=json_api(0,"Nama Tidak Boleh ada Spasi.");   
        }        
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama sudah ada.");
        }
        else
        {

            $this->db->trans_begin();

            $this->param->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'pids/settingParamPids/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $nama_param=trim($this->input->post('name'));
        $info=trim($this->input->post('info'));
        $tipe_param=trim($this->input->post('tipe_param'));        
        $port=trim($this->enc->decode($this->input->post('port')));

        $typeInputValueParam=trim($this->input->post('typeInputValueParam'));
        $typeInputInfo=trim($this->input->post('typeInputInfo'));


        $value_param="";
        if(strtoupper($typeInputValueParam)==strtoupper("text"))
        {
            $value_param=trim($this->input->post('value_param'));
            $this->form_validation->set_rules('value_param', 'Value Parameter ', 'required');
        }
        else
        {

            // input file upload manual php native, karena issue menggunakan library ci
            $filename = $_FILES['value_param']['name'];       
            
            $checking_format_file[]=0;
            if(!empty($filename))
            {
                $lokasi = $_FILES['value_param']['tmp_name'];
                $extensi = pathinfo($filename, PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
                $nama_baru=str_replace(" ","_",$nama_param)."_".date("YmdHis").".".$extensi;
                $value_param="./uploads/".$nama_baru;


                if(strtoupper($extensi)=="JPG" or strtoupper($extensi)=="PNG" or strtoupper($extensi)=="JPEG")
                {
                    $checking_format_file[]=0;                
                }
                else
                {
                    $checking_format_file[]=1;
                }

            }
        }

        // $value_param=trim($this->input->post('value_param'));

        $this->form_validation->set_rules('name', 'Nama', 'required');
        $this->form_validation->set_rules('tipe_param', 'Tipe Parameter', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');

        // $this->form_validation->set_rules('value_param', 'Value Parameter ', 'required');
        $this->form_validation->set_rules('info', 'Info', 'required');
        $this->form_validation->set_message('required','%s harus diisi!');

        
        $data=array(
                    'param_name'=>$nama_param,
                    'param_value'=>$value_param,
                    'param_type'=>$tipe_param,
                    'param_type_value'=>$typeInputValueParam,
                    'param_type_value_info'=>$typeInputInfo,
                    'port_id'=>$port,
                    'info'=>$info,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );


        // ceck data jika nama param sudah ada

        $check=$this->param->select_data($this->_table," where upper(param_name)=upper('".$nama_param."') and port_id={$port} and status not in (-5) ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if(preg_match('/\s/',$nama_param)) // tidak boleh ada spasi
        {
            echo $res=json_api(0,"Nama Tidak Boleh ada Spasi.");   
        }        
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama sudah ada.");
        }
        else if(empty($value_param))
        {
            echo $res=json_api(0,"File Tidak Boleh Kosong.");   
        }
        else if(array_sum($checking_format_file)>0)
        {
            echo $res=json_api(0,"Format gambar harus jpg, png atau jpeg.");     
        }
        else
        {

            $this->db->trans_begin();

            $this->param->insert_data($this->_table,$data);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();

                if(strtoupper($typeInputValueParam)==strtoupper("file"))
                {
                    move_uploaded_file($lokasi,$value_param);    
                }                
                echo $res=json_api(1, 'Berhasil tambah data');
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/settingParamPids/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    

    public function edit_27072021($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);
        $detail=$this->param->select_data($this->_table,"where id=$id_decode")->row();
        $port=$this->getPort(); 

        $portData[""]="Pilih";
        $portDataSelected="";
        foreach ($port as $key => $value) 
        {
            if($detail->port_id==$value->id)
            {
                $portDataSelected=$this->enc->encode($value->id);
                $portData[$portDataSelected]=strtoupper($value->name);

            }
            else
            {
                $portData[$this->enc->encode($value->id)]=strtoupper($value->name);
            }
        }


        $data['title'] = 'Edit Seting Parameter PIDS';
        $data['detail']=$detail;
        $data['id']=$id;
        $data['port'] = $portData;
        $data['portDataSelected'] = $portDataSelected;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit_27072021()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        $id=$this->enc->decode($this->input->post('id'));

        $value_param=trim($this->input->post('value_param'));
        $tipe_param=trim($this->input->post('tipe_param'));
        $info=trim($this->input->post('info'));
        $port=trim($this->enc->decode($this->input->post('port')));


        $this->form_validation->set_rules('value_param', 'Value Parameter ', 'required');
        $this->form_validation->set_rules('tipe_param', 'Tipe Parameter', 'required');
        $this->form_validation->set_rules('info', 'Info', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $data=array(
                    
                    // 'param_name'=>$nama_param,
                    'param_value'=>$value_param,
                    'param_type'=>$tipe_param,
                    'port_id'=>$port,
                    'info'=>$info,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        // print_r($data);
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {

            $this->db->trans_begin();

            $this->param->update_data($this->_table,$data,"id=$id");

            if ($this->db->trans_status() === FALSE)
            {   
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data');
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/settingParameterPids/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);
        $detail=$this->param->select_data($this->_table,"where id=$id_decode")->row();
        $port=$this->getPort(); 

        $portData[""]="Pilih";
        $portDataSelected="";
        foreach ($port as $key => $value) 
        {
            if($detail->port_id==$value->id)
            {
                $portDataSelected=$this->enc->encode($value->id);
                $portData[$portDataSelected]=strtoupper($value->name);

            }
            else
            {
                $portData[$this->enc->encode($value->id)]=strtoupper($value->name);
            }
        }

        if($detail->param_type_value=='file')
        {
            $paramTypeValue='                            
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <label>Pilih File gambar <span class="wajib">*</span></label>
                                <div class="input-group ">

                                    <div class="form-control uneditable-input   input-fix" data-trigger="fileinput">
                                        <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                        <span class="fileinput-filename"> </span>
                                    </div>
                                    <span class="input-group-addon btn default btn-file">
                                        <span class="fileinput-new"> Pilih File </span>
                                        <span class="fileinput-exists"> Pilih File</span>
                                        <input type="hidden"><input type="hidden"><input type="file" name="file"  ></span>
                                    <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput" title="hapus"><i class="fa fa-trash "></i> </a>
                                </div>
                            </div>
                            <img src="'.base_url($detail->param_value).'" width="100px" />
                            <input type="hidden" name="value_param"  value="'.$detail->param_value.'">';
        }
        else
        {

            $paramTypeValue='
                            <label>Value Param <span class="wajib">*</span></label>
                            <input type="text" name="value_param" class="form-control" placeholder="Value Param" value="'.$detail->param_value.'" required>';            

        }


        if($detail->param_type_value_info=='html')
        {
            $paramTypeValuInfo='
                            <div class="form-group">
                                <label>Info<span class="wajib">*</span></label>
                                <textarea class="wysihtml5 form-control" name="info" id="info" placeholder="Info" required  rows="20">'.$detail->info.'</textarea>
                            </div>

                            ';
        }
        else
        {
            $paramTypeValuInfo='
                                <label>Info <span class="wajib">*</span></label>
                                <input type="text" name="info" class="form-control" placeholder="Info" required value="'.$detail->info.'" >
                            ';

        }


        $data['title'] = 'Edit Seting Parameter PIDS';
        $data['detail']=$detail;
        $data['paramTypeValue']=$paramTypeValue;
        $data['paramTypeValuInfo']=$paramTypeValuInfo;
        $data['id']=$id;
        $data['port'] = $portData;
        $data['portDataSelected'] = $portDataSelected;

        $this->load->view($this->_module.'/edit',$data);   
    }    

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        $id=$this->enc->decode($this->input->post('id'));

        // $value_param=trim($this->input->post('value_param'));
        $tipe_param=trim($this->input->post('tipe_param'));
        $info=trim($this->input->post('info'));
        $port=trim($this->enc->decode($this->input->post('port')));

        $typeInputValueParam=trim($this->input->post('typeInputValueParam'));
        $typeInputInfo=trim($this->input->post('typeInputInfo'));

        $getData=$this->param->select_data($this->_table," where id={$id} ")->row();
        $value_param="";

        $emptyFile[]=0;
        $checking_format_file[]=0;
        $isInsertFile=0;
        if(strtoupper($typeInputValueParam)==strtoupper("text"))
        {
            $value_param=trim($this->input->post('value_param'));
            $this->form_validation->set_rules('value_param', 'Value Parameter ', 'required');
        }
        else
        {

            // input file upload manual php native, karena issue menggunakan library ci
            $filename = $_FILES['file']['name'];
            $value_param=trim($this->input->post('value_param'));       
                        
            if(!empty($filename)) // jika filenya diisi maka ambil file baru
            {
                $lokasi = $_FILES['file']['tmp_name'];
                $extensi = pathinfo($filename, PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
                $nama_baru=str_replace(" ","_",$getData->param_name)."_".date("YmdHis").".".$extensi;
                $value_param="./uploads/".$nama_baru;
                $unlink=$getData->param_value;
                $isInsertFile=1;


                if(strtoupper($extensi)=="JPG" or strtoupper($extensi)=="PNG")
                {
                    $checking_format_file[]=0;                
                }
                else
                {
                    $checking_format_file[]=1;
                }

            }
            else
            {
                if(!empty($value_param) and empty($filename) ) // jika gambar sudah ada tapi filenya gak diisi
                {
                    $value_param=$getData->param_value;   
                }
                else //jika  benar2 kosong
                {
                    $emptyFile[]=1;   
                }
            }
        }        


        // $this->form_validation->set_rules('value_param', 'Value Parameter ', 'required');
        $this->form_validation->set_rules('tipe_param', 'Tipe Parameter', 'required');
        $this->form_validation->set_rules('info', 'Info', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $data=array(
                    
                    // 'param_name'=>$nama_param,
                    'param_value'=>$value_param,
                    'param_type'=>$tipe_param,
                    'param_type_value'=>$typeInputValueParam,
                    'param_type_value_info'=>$typeInputInfo,
                    'port_id'=>$port,
                    'info'=>$info,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        // print_r($data);
        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if(array_sum($emptyFile)>0)
        {
            echo $res=json_api(0, " Gambar Harus Diisi. ");   
        }
        else if(array_sum($emptyFile)>0)
        {
            echo $res=json_api(0,"File Tidak Boleh Kosong.");   
        }
        else if(array_sum($checking_format_file)>0)
        {
            echo $res=json_api(0,"Format gambar harus jpg, png atau jpeg.");     
        }        
        else
        {

            $this->db->trans_begin();

            $this->param->update_data($this->_table,$data,"id=$id");

            if ($this->db->trans_status() === FALSE)
            {   
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->db->trans_commit();                
                if($isInsertFile==1)
                {
                    move_uploaded_file($lokasi,$value_param);

                    if(file_exists($unlink)) // jika ada file maka unlink
                    {
                        unlink($unlink);    
                    }
                }                
                echo $res=json_api(1, 'Berhasil edit data');
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/settingParameterPids/action_edit';
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
        $this->param->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'master_data/setting_param/action_change';
        $d[1]==1?$logMethod='DISABLE':$logMethod='ENABLE';
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
        $this->param->update_data($this->_table,$data," id='".$id."'");

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
        $logUrl      = site_url().'master_data/setting_param/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function convertPHPSizeToKiloBytes($sSize)
    {
        $sSuffix = strtoupper(substr($sSize, -1));
        if (!in_array($sSuffix, array('P', 'T', 'G', 'M', 'K'))) {
            return (int)$sSize;
        }
        $iValue = substr($sSize, 0, -1);
        switch ($sSuffix) {
            case 'P':
                $iValue *= 1024;
                // Fallthrough intended
            case 'T':
                $iValue *= 1024;
                // Fallthrough intended
            case 'G':
                $iValue *= 1024;
                // Fallthrough intended
            case 'M':
                $iValue *= 1024;
                break;
        }
        return (int)$iValue;
    }

    function getPort()
    {
        $getApp=$this->param->select_data("app.t_mtr_identity_app", " ")->row();


        if($getApp->port_id<>0)
        {
            $port=$this->param->select_data("app.t_mtr_port", " where id={$getApp->port_id}")->result();

        }
        else
        {
            if(!empty($this->session->userdata("port_id")))
            {
                $port=$this->param->select_data("app.t_mtr_port", " where id=".$this->session->userdata("port_id"))->result();
            }
            else
            {
                $port=$this->param->select_data("app.t_mtr_port", " where status=1 order by name asc ")->result();
            }

        }

        return $port;
    }

}
