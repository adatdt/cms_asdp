<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Master_pcm extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_master_pcm','pcm');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_quota_pcm_vehicle';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/master_pcm';

        $this->dbAction = $this->load->database('dbAction', TRUE);
        $this->dbView = $this->load->database('dbView', TRUE);
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->pcm->dataList();
            echo json_encode($rows);
            exit;
        }

        $port = $this->pcm->select_data("app.t_mtr_port", "where status<>'-5' order by name asc")->result();
        $shipClass = $this->pcm->select_data("app.t_mtr_ship_class", "where status<>'-5' order by name asc")->result();

        $dataPort[""]="Pilih";
        $dataShipClass[""]="Pilih";
        $dataTime[""]="Pilih";
            
        for($i=0;$i<24;$i++)
        {
            strlen($i)<2?$his="0".$i.":00":$his=$i.":00";

            $dataTime[$this->enc->encode($his)]=$his;

        }                            

        foreach ($port as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataPort[$idEncode]=strtoupper($value->name);
        }

        foreach ($shipClass as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataShipClass[$idEncode]=strtoupper($value->name);
        }        


        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Master PCM',
            'port'     =>$dataPort,
            'shipClass'     =>$dataShipClass,
            'time'     =>$dataTime,
            'content'  => 'master_pcm/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port = $this->pcm->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
        $shipClass = $this->pcm->select_data("app.t_mtr_ship_class", "where status=1 order by name asc")->result();

        $dataPort[""]="Pilih";
        $dataShipClass[""]="Pilih";
        $dataTime[""]="Pilih";


        foreach ($port as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataPort[$idEncode]=strtoupper($value->name);
        }

        foreach ($shipClass as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataShipClass[$idEncode]=strtoupper($value->name);
        }        

        $data['title'] = 'Tambah Master PCM';
        $data['port']=$dataPort;
        $data['shipClass']=$dataShipClass;
        $data['time']=$dataTime;

        $this->load->view($this->_module.'/add',$data);
    }

    public function listErr(){
        validate_ajax();

        $idNotUpdated=$this->session->userdata('notUpdated');

        $implode=implode(",",$idNotUpdated);
        $where=" where a.id in ({$implode})";
        $getData=$this->pcm->qryDataTrx($where)->result();
        $getListNotUpdated=array();

        foreach ($getData as $key => $value) {
            $getListNotUpdated[]=$value;
        }

        $data['title'] = 'List Data Pcm Global yang tidak terupdate';
        $data['getListNotUpdated']=$getListNotUpdated;


        $this->load->view($this->_module.'/list',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=trim($this->enc->decode($this->input->post('port', true)));
        $shipClass=trim($this->enc->decode($this->input->post('shipClass', true)));
        $quota=trim($this->input->post('quota', true));
        $lineMeter=trim($this->input->post('lineMeter', true));
        $time=trim($this->enc->decode($this->input->post('time', true)));
        // $action=trim($this->enc->decode($this->input->post('action')));
        $date=trim($this->input->post('date', true));

        $_POST["port"] = $port;
        $_POST["shipClass"] = $shipClass;
        $_POST["time"] = $time;

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('time', 'Jam Berlaku', 'required');
        $this->form_validation->set_rules('date', 'Tanggal Berlaku ', 'required|callback_check_date');
        // $this->form_validation->set_rules('action', 'Aksi', 'required');
        $this->form_validation->set_rules('shipClass', 'Kelas layanan ', 'required');
        $this->form_validation->set_rules('quota', 'Quota ', 'required|numeric|numeric');
        $this->form_validation->set_rules('lineMeter', 'Line Meter ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('check_date','%s Tanggal Tidak Valid!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');

        
        $data=array(
                    'port_id'=>$port,
                    'ship_class'=>$shipClass,
                    'quota'=>$quota,
                    'total_lm'=>$lineMeter,
                    'depart_time'=>$time,
                    'depart_date'=>$date,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );


        // ceck data jika username sudah ada
        $check=$this->pcm->select_data($this->_table," where port_id=".$this->db->escape($port)." and ship_class=".$this->db->escape($shipClass)." and depart_time=".$this->db->escape($time)." and status<>-5 and depart_date=".$this->db->escape($date));


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Quota sudah ada.");
        }
        else if (empty($this->checkTime($time)))
        {
            echo $res=json_api(0,"Format jam salah.");   
        }
        else
        {
                                    
            
            // print_r($returnData); exit;

            $this->dbAction->trans_begin();

            $idData=$this->pcm->actionAddData($data);            
            $this->pcm->insert_data($this->_table,$data); // insert log hanya pada saat edit saja

            if ($this->dbAction->trans_status() === FALSE)
            {
                $this->dbAction->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->dbAction->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data',$idData);
            }

            // print_r($data); exit;
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/master_pcm/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
    function check_date ($date) {

        $explodeDate = explode("-",$date);
        $tahun = $explodeDate[0];
        $bulan = $explodeDate[1];
        $tanggal  = $explodeDate[2]; 

        return checkdate($bulan, $tanggal, $tahun);        
    }
    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $idDecode=$this->enc->decode($id);


        $port = $this->pcm->select_data("app.t_mtr_port", "where status=1 order by name asc")->result();
        $shipClass = $this->pcm->select_data("app.t_mtr_ship_class", "where status=1 order by name asc")->result(); 
        $detail = $this->pcm->select_data($this->_table, "where id={$idDecode} ")->row();       

        $dataPort[""]="Pilih";
        $selectedPort="";
        $dataShipClass[""]="Pilih";
        $dataTime[""]="Pilih";
        $selectedTime="";
        $selectedShipClass="";

        for($i=0;$i<24;$i++)
        {
            strlen($i)<2?$his="0".$i.":00":$his=$i.":00";

            $hisEncode=$this->enc->encode($his);
            $dataTime[$hisEncode]=$his;

            format_dateTimeHis($his)==format_dateTimeHis($detail->depart_time)?$selectedTime=$hisEncode:"";

        }            

        foreach ($port as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataPort[$idEncode]=strtoupper($value->name);
            
            if($value->id==$detail->port_id)
            {
                $selectedPort=$idEncode;
            }
        }

        foreach ($shipClass as $key => $value) {
            $idEncode=$this->enc->encode($value->id);
            $dataShipClass[$idEncode]=strtoupper($value->name);
            
            if($value->id==$detail->ship_class)
            {
                $selectedShipClass=$idEncode;
            }
        }        

        $action=array(""=>"Pilih",1=>"Tambah",2=>"Kurang");

        $data['title'] = 'Edit Mater PCM';
        $data['id'] = $id;
        $data['port']=$dataPort;
        $data['selectedPort']=$selectedPort;
        $data['shipClass']=$dataShipClass;
        $data['time']=$dataTime;
        $data['selectedTime']=$selectedTime;
        $data['selectedShipClass']=$selectedShipClass;
        $data['detail']=$detail;
        $data['action']=$action;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=trim($this->enc->decode($this->input->post('id', true)));
        // $port=trim($this->enc->decode($this->input->post('port')));
        // $shipClass=trim($this->enc->decode($this->input->post('shipClass')));
        $time=trim($this->enc->decode($this->input->post('time', true)));
        $quota=trim($this->input->post('quota', true));
        $action=trim($this->input->post('action', true));
        $lineMeter=trim($this->input->post('lineMeter', true));

        $_POST["id"] = $id;

        $getDetail=$this->pcm->select_data($this->_table, " where id=".$this->db->escape($id))->row();

        $this->form_validation->set_rules('action', 'Aksi ', 'required|numeric');
        $this->form_validation->set_rules('id', 'Id ', 'required');
        $this->form_validation->set_rules('quota', 'Quota ', 'required|numeric');
        $this->form_validation->set_rules('lineMeter', 'Line Meter ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('check_date','%s Tanggal Tidak Valid!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');

        
        $data=array(
                    'port_id'=>$getDetail->port_id,
                    'ship_class'=>$getDetail->ship_class,
                    'depart_time'=>$getDetail->depart_time,
                    'lineMeter'=>$lineMeter,
                    'quota'=>$quota,    
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        $checkMin[]=0;

        if(!empty($getDetail))
        {
            // checking apakah jika di kurang dia min
            if($action!=1)
            {                
                if(($getDetail->quota-$quota)<0)
                {
                    $checkMin[]=1;                
                }
            }

            // ceck data jika data sudah ada
            $check=$this->pcm->select_data($this->_table," where port_id=".$this->db->escape($getDetail->port_id)." and ship_class=".$this->db->escape($getDetail->ship_class) ." and depart_time=".$this->db->escape($getDetail->depart_time) ." and  status<>'-5' and id<> ".$this->db->escape($id));            
        }


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else if(array_sum($checkMin)>0)
        {
            echo $res=json_api(0, "Estimasi tidak boleh minus");   
        }
        else
        {
            $param=array('action'=>$action,
                        'quota'=>$quota,
                        'idMaster'=>$id,
                        'time'=>$time,
                        'lineMeter'=>$lineMeter,
                        'updatedBy'=>$this->session->userdata("username"),
                        'updatedOn'=>date("Y-m-d H:i:s"), 
                        );

            $this->dbAction->trans_begin();

            $getData=$this->pcm->editData($param);


            if ($this->dbAction->trans_status() === FALSE)
            {   
                $this->dbAction->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->dbAction->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data',$getData);
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/master_pcm/action_edit';
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


        $this->dbAction->trans_begin();
        $this->pcm->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->dbAction->trans_status() === FALSE)
        {
            $this->dbAction->trans_rollback();
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
            $this->dbAction->trans_commit();
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
        $logUrl      = site_url().'master_data/pcm/action_change';
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

        $this->dbAction->trans_begin();
        $this->pcm->update_data($this->_table,$data," id='".$id."'");

        if ($this->dbAction->trans_status() === FALSE)
        {
            $this->dbAction->trans_rollback();
            echo $res=json_api(0, 'Gagal delete data');
        }
        else
        {
            $this->dbAction->trans_commit();
            echo $res=json_api(1, 'Berhasil delete data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/master_pcm/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function checkTime($param="")
    {
        for($i=0;$i<24;$i++)
        {
            strlen($i)<2?$his="0".$i.":00":$his=$i.":00";
            $dataTime[$his]=$his;
        }

        $result=array_search($param,$dataTime);

        return empty($result)?"":$result;
    }

    function getTime(){

        $datePick=trim($this->input->post("datePick"));

        $nowDate = date('Y-m-d H');
        $dataDate = array();

        for($i=0;$i<24;$i++)
        {
            strlen($i)<2?$his="0".$i.":00":$his=$i.":00";

            // checking date apakah harinya yang di pilih saat ini_alter
            if($datePick." ".$his<$nowDate.":00")
            {
                $dataTime['statusData']='disabled';
            }
            else
            {
                $dataTime['statusData']='enabled';
            }

            $dataTime['valData']=$his;
            $dataTime['idData']=$this->enc->encode($his);            
            
            $dataDate[] = $dataTime;
        }   

        $returnData = array(
            "tockenHash"=> $this->security->get_csrf_hash(),
            "dataTime" => $dataDate
        );

        echo json_encode($returnData);       
    }    

}
