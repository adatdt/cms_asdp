<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class GenerateQr extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('GenerateQrModel','qr');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('Html2pdf');

        $this->_table    = 'core.t_mtr_team';
        // $this->_username = $this->session->userdata('username');
        $this->_module   = 'shift_management/generateQr';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->qr->dataList();
            echo json_encode($rows);
            exit;
        }

        $dataPort[""]="Pilih";
        if($this->identity_app()==0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $port=$this->qr->select_data("app.t_mtr_port","where status='1' order by name asc")->result();
                $row_port=0;   
            }
            else
            {
                $port=$this->qr->select_data("app.t_mtr_port","where id=".$this->session->userdata("port_id"))->result();
                $row_port=1;   
            }
        }
        else
        {
                $port=$this->qr->select_data("app.t_mtr_port","where id=".$this->identity_app())->result();
                $row_port=1;
        }

        foreach ($port as $key => $value) 
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        $dataShift[""]="Pilih&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        $dataUserGroup[""]="Pilih&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $dataUserGroup[$this->enc->encode(4)]="POS";
        $dataUserGroup[$this->enc->encode(3)]="SPV POS";
        $dataUserGroup[$this->enc->encode(33)]="Verifikator";

        $buttonPdf='
        
            <button type="button" class="btn btn-warning mt-ladda-btn ladda-button" data-style="zoom-in" title="PDF" id="download_pdf">
            <span class="ladda-label">PDF</span>
            <span class="ladda-spinner"></span>
            </button>        
        
        ';

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Generate QR',
            'content'  => 'generateQr/index',
            // 'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'port'      =>$dataPort,
            'shift'      =>$dataShift,
            'userGroup'      =>$dataUserGroup,
            'row_port'=>$row_port,
            // 'btn_pdf'  => generate_button($this->_module, 'download_pdf', '<button class="btn  btn-warning" title="PDF" id="download_pdf">PDF</button>'),
            'btn_pdf'  => generate_button($this->_module, 'download_pdf', $buttonPdf),
        );

		$this->load->view('default', $data);
	}

    public function getShift()
    {
    
        $port=$this->enc->decode($this->input->post("port"));

        $dataGetShiftTime=array();
        if(!empty($port))
        {
            $getShiftTime=$this->qr->getShift($port);
            foreach ($getShiftTime as $key => $value) {
                $dataGetShiftTime[]=array(
                    "id"=>$this->enc->encode($value->id),
                    "name"=>strtoupper($value->name),
                    "tokenHash" => $this->security->get_csrf_hash()
                );
            }
        }

        echo json_encode($dataGetShiftTime);
    }


    public function getQr()
    {

        if($this->input->post('port')){
            $this->form_validation->set_rules('port', 'Pelabuhan', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid pelabuhan'));
        }
        if($this->input->post('userGroup')){
            $this->form_validation->set_rules('userGroup', 'User Grup', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid user grup'));
        }
        if($this->input->post('shift')){
            $this->form_validation->set_rules('shift', 'Shift', 'trim|callback_validate_decode_param', array('validate_decode_param' => 'Invalid shift'));
        }
        if($this->input->post('dateFrom')){
            $this->form_validation->set_rules('dateFrom', 'Tanggal penugasan', 'trim|required|callback_validate_date', array('validate_date' => 'Invalid tanggal penugasan'));
        }
        
        
        if ($this->form_validation->run() == FALSE) 
        {
            echo $res = json_api(0, validation_errors(),[]);
            exit;
        }

        $port=$this->enc->decode($this->input->post("port"));
        $userGroup=$this->enc->decode($this->input->post("userGroup"));
        $shift=$this->enc->decode($this->input->post("shift"));
        $dateFrom=$this->input->post("dateFrom");

        if(!empty($port) and !empty($userGroup) and !empty($shift) and !empty($dateFrom))
        {


            $param =array(
                "port"=>$port,
                "userGroup"=>$userGroup,
                "shift"=>$shift,
                "dateFrom"=>$dateFrom,
            );        
    
            $getData=$this->qr->getQr($param);

            // print_r($getData); exit;


            if(count((array)$getData)>0)
            {
                $this->load->library('ciqrcode'); //pemanggilan library QR CODE

                $config['cacheable']    = true; //boolean, the default is true
                $config['cachedir']     = './assets/'; //string, the default is application/cache/
                $config['errorlog']     = './assets/'; //string, the default is application/logs/
                $config['imagedir']     = './assets/img/'; //direktori penyimpanan qr code
                $config['quality']      = true; //boolean, the default is true
                $config['size']         = '1024'; //interger, the default is 1024
                $config['black']        = array(224,255,255); // array, default is array(255,255,255)
                $config['white']        = array(70,130,180); // array, default is array(0,0,0)
                $this->ciqrcode->initialize($config);
                $rowData=array();
                foreach ($getData as $key => $value) {
                    
                    $image_name=$value->shift_code."_".date("YmdHis").'.png'; //buat name dari qr code sesuai dengan nim
             
                    // $params['data'] = $value->shift_code; //data yang akan di jadikan QR CODE
                    $params['data'] =$this->encriptAes($value->shift_code); //data yang akan di jadikan QR CODE
                    $params['level'] = 'H'; //H=High
                    $params['size'] = 10;
                    $params['savename'] = FCPATH.$config['imagedir'].$image_name; ////simpan image QR CODE ke folder assets/images/
                    $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE
                    

                    $value->qrCode=$image_name;
                    $value->baseCode=base64_encode(file_get_contents($params['savename']));
                    $value->tokenHash = $this->security->get_csrf_hash();


                    unlink($config['imagedir']."/".$image_name); // delete derectory
                    $rowData[]=$value;
                }
         
                                
                $data=array(
                    "code"=>1,
                    "data"=>$rowData,
                    "tokenHash" => $this->security->get_csrf_hash()
                );
            }
            else
            {
                $data=array(
                    "code"=>0,
                    "message"=>" Tidak Ada Data ",
                    "tokenHash" => $this->security->get_csrf_hash()
                ) ;  
            }
        }
        else
        {
            if(empty($port))
            {
                $data=array(
                    "code"=>0,
                    "message"=>" Pelabuhan Tidak Boleh Kosong ",
                    "tokenHash" => $this->security->get_csrf_hash()
                );
            }
            else if(empty($userGroup))
            {
                $data=array(
                    "code"=>0,
                    "message"=>" User Group Tidak Boleh Kosong ",
                    "tokenHash" => $this->security->get_csrf_hash()
                );
            }
            else if(empty($shift))
            {
                $data=array(
                    "code"=>0,
                    "message"=>" Shift Tidak Boleh Kosong ",
                    "tokenHash" => $this->security->get_csrf_hash()
                );
            }
            else
            {
                $data=array(
                    "code"=>0,
                    "message"=>" Tanggal Tidak Boleh Kosong ",
                    "tokenHash" => $this->security->get_csrf_hash()
                );
            }
        }


        echo json_encode($data);
    }    
  
    public function identity_app()
    {
        $data=$this->qr->select_data("app.t_mtr_identity_app","")->row();

        return $data->port_id;
    }

    public function encriptAes($obCode)
    {
        // hard cord 
        $aesKey=$this->qr->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_key_login') ")->row();
        $aesIv=$this->qr->select_data("app.t_mtr_custom_param", " where upper(param_name)=upper('aes_iv_login') ")->row();

        return PHP_AES_Cipher::encrypt2($aesKey->param_value,$aesIv->param_value,$obCode);

    }    

    public function download_pdf()
    {
        
        $name=$this->input->post('name[]');
        $group=$this->input->post('group[]');
        $portName=$this->input->post('portName[]');    
        $shift=$this->input->post('shift[]');
        $assignmentDate=$this->input->post('assignmentDate[]');
        $baseCode=$this->input->post('baseCode[]');

        // echo $name[0]; exit;
        $detail = array();
        foreach ($name as $key => $value) {
            $detail []= array(
                "name"=>$name[$key],
                "group"=>$group[$key],
                "portName"=>$portName[$key],    
                "shift"=>$shift[$key],
                "assignmentDate"=>$assignmentDate[$key],
                "baseCode"=>$baseCode[$key],
            );
        }

        // print_r($detail); exit;
        $data["data"]=$detail;
        // $getData = $this->vaccine->download();
        // $data["data"]=$getData;
        // $data["dateFrom"]=trim($this->input->get('dateFrom'));
        // $data["dateTo"]=trim($this->input->get('dateTo'));

        // // print_r($data['data']); exit;
        $this->load->view($this->_module . '/pdf', $data);

    }        


}