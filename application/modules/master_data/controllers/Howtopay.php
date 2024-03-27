<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
/*
    penambahan validasi dan csrf token  24-07-2023 by adat

*/

class Howtopay extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('M_howtopay','payment');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_payment_howtopay';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/howtopay';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->payment->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Cara Pembayaran',
            'content'  => 'howtopay/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'payment_type'=> $this->payment->select_data("app.t_mtr_payment_type"," order by name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Cara Pembayaran';
        $data['payment_type']= $this->payment->select_data("app.t_mtr_payment_type"," order by name asc")->result();
        $data['method']=$this->payment->select_data("app.t_mtr_payment_method","where status='1' order by name asc")->result();
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $howtopay=$this->input->post('howtopay[]', true);
        $title=trim($this->input->post('title', true));
        $payment_type=$this->enc->decode($this->input->post('payment_type',true));
        $_POST["payment_type"] = $payment_type;

        $this->form_validation->set_rules('title', 'Judul Pembayaran', 'required|max_length[255]');
        $this->form_validation->set_rules('payment_type', 'Tipe Pembayaran', 'required|is_natural');

        // $check_empty=array();
        // $howtopay2=array();
        if(!empty($howtopay))
        {
            foreach ($howtopay as $key => $value) {
                $this->form_validation->set_rules("howtopay[".$key."]", 'Cara Pembayaran', 'required|max_length[255]');

            }
        }

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('is_natural','%s harus angka!');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');

        $kode=$this->createCode();
        $data_howtopay=array('payment_type_id'=>$payment_type,
                            'title'=>$title,
                            'code'=>$kode,
                            'created_by'=>$this->session->userdata("username"),
                            "created_on" => date("Y-m-d H:i:s")
                            );
        $allData [0]=$data_howtopay;
        if($this->form_validation->run()===false)
        {
            $errorUnique =array_unique($this->form_validation->error_array());
            $map = array_map(function($a){ return "<p>".$a."</p>";}, $errorUnique);
            echo $res=json_api(0, implode(" ",$map));
        }
        else if (empty($howtopay) >0)
        {
            echo $res=json_api(0, 'Cara Pembayaran masih ada yang kosong');   
        }
        else
        {
            $this->db->trans_begin();
            $this->db->insert($this->_table,$data_howtopay);
            $get_id=$this->db->insert_id();

            $order=1;
            $data_detail = array();
            foreach($howtopay as $key=>$value)
                {
                 $data=array('howtopay_id'=>$get_id,
                            'order'=>$order,
                            'detail'=>$value,
                            'howtopay_code'=>$kode,
                            'created_by'=>$this->session->userdata("username"),
                            "created_on" => date("Y-m-d H:i:s")
                            );

                $data_detail[]=$data;
                 $this->payment->insert_data("app.t_mtr_payment_howtopay_detail",$data);

                 $order++;   
                }
                $allData [1]=$data_detail;

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
        $logUrl      = site_url().'master_data/howtopay/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($allData );
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $howtopay_id=$this->enc->decode($id);

        $data['title'] = 'Edit Tipe Pembayaran';
        $data['payment_type']= $this->payment->select_data("app.t_mtr_payment_type"," order by name asc")->result();
        $data['method']=$this->payment->select_data("app.t_mtr_payment_method","where status='1' order by name asc")->result();
        $data['detail']=$this->payment->select_data($this->_table," where id=".$howtopay_id)->row();
        $data['detail2']=$this->payment->select_data("app.t_mtr_payment_howtopay_detail"," where howtopay_id=".$howtopay_id.' order by "order" asc')->result();
        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $payment_type_id=$this->enc->decode($this->input->post('payment_type_id', true));
        $howtopay=$this->input->post('howtopay[]', true);
        $howtopay_id=$this->enc->decode($this->input->post('howtopay_id', true));
        $code=trim($this->input->post('code'));
        $howtopay_detail=$this->input->post('howtopay_detail[]', true);
        $id_detail=$this->input->post('id_detail[]', true);
        $title=trim($this->input->post('title', true));
        $payment_type=$this->enc->decode($this->input->post('payment_type'));

        $_POST["payment_type"] = $payment_type;
        $_POST["howtopay_id"] = $howtopay_id;

        $this->form_validation->set_rules('title', 'Judul  Pembayaran', 'required|max_length[255]');
        $this->form_validation->set_rules('payment_type', 'Tipe Pembayaran', 'required|is_natural');
        $this->form_validation->set_rules('howtopay_id', 'Id howtopay', 'required|is_natural');

        if(count((array)$howtopay)>0)
        {
            foreach ($howtopay as $key => $value) {
                $this->form_validation->set_rules("howtopay[".$key."]", 'Cara Pembayaran', 'required|max_length[255]');
            }
        }
        
        if(count((array)$howtopay_detail)>0)
        {
            // echo 2;
            foreach ($howtopay_detail as $key => $value) {
                
                    $this->form_validation->set_rules("howtopay_detail[".$key."]", 'Cara Pembayaran', 'required|max_length[255]');
                
            }
        }         

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('is_natural','%s harus angka!');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');        


        $data_howtopay=array(
                    'title'=>$title,
                    'payment_type_id'=>$payment_type,
                    'updated_by'=>$this->session->userdata("username"),
                    "updated_on" => date("Y-m-d H:i:s")                    
                    );

        $allData[0] = $data_howtopay;

        if($this->form_validation->run()===false)
        {
            $errorUnique =array_unique($this->form_validation->error_array());
            $map = array_map(function($a){ return "<p>".$a."</p>";}, $errorUnique);
            echo $res=json_api(0, implode(" ",$map));
        }
        else if(empty($howtopay) && empty($howtopay_detail)   )        
        {
            echo $res=json_api(0, 'Cara Pembayaran masih ada yang kosong');   
        }
        else
        {
            $this->db->trans_begin();   
            if(empty($howtopay_detail))
            {

                $this->payment->delete_data("app.t_mtr_payment_howtopay_detail","howtopay_id=".$howtopay_id);
            }

            else
            {
                $delete_id=array();
                $order=1;
                foreach ($howtopay_detail as $key => $value) {

                    $data_detail=array('detail'=>$value,'order'=>$order);
                    //update data
                    $id=$this->enc->decode($id_detail[$key]);

                    $this->payment->update_data("app.t_mtr_payment_howtopay_detail",$data_detail,"id=".$id);

                    $delete_id[]=$id;
                    $order++;
                }
                
                //delete data
                $this->payment->delete_data("app.t_mtr_payment_howtopay_detail","id not in (".implode(", ",$delete_id).") and howtopay_id=".$howtopay_id);
            }


            // jika data  how to paynya tidak kosong
            if(!empty($howtopay))
            {
                $new_insert=array();
                foreach ($howtopay as $key => $value) {
                    if(!empty($value))
                    {
                        $new_insert[]=$value;
                    }                
                }

                $count_data=$this->payment->select_data("app.t_mtr_payment_howtopay_detail"," where howtopay_id=".$howtopay_id);

                $maxorder=$count_data->num_rows()+1;

                foreach ($new_insert as $key => $value) {
                    
                    $data_newinsert=array('detail'=>$value,
                                        'order'=>$maxorder,
                                        'howtopay_id'=>$howtopay_id,
                                        'howtopay_code'=>$code,
                                        'created_by'=>$this->session->userdata("username"),
                                        "created_on" => date("Y-m-d H:i:s")
                                       );
                    $this->payment->insert_data("app.t_mtr_payment_howtopay_detail",$data_newinsert);
                    $maxorder++;
                    $allData[1] = $data_newinsert;
                }
            }

            $this->payment->update_data("app.t_mtr_payment_howtopay",$data_howtopay,"id=".$howtopay_id);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data ');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil edit data');
            }   
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'master_data/howtopay/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($allData);
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

        $team_id = $this->enc->decode($id);

            $this->db->trans_begin();
            $this->schedule->update_data($this->_table,$data,"id=$team_id");

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
        $logUrl      = site_url().'pelabuhan/schedule/action_delete';
        $logMethod   = 'DELETE';
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
        $this->team_model->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal non aktif');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Berhasil non aktif data');
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function create_code()
    {
        $data=$this->db->query("SELECT 
                    SUBSTRING(EXTRACT(YEAR FROM now())::varchar, 3,2)||
                     to_char(EXTRACT(DAY FROM now()), 'fm000')|| 
                    (to_char(nextval('core.t_mtr_team_code_seq'), 'fm0000')) as code ")->row();

        return $data->code;

    }

    public function enable($param)
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
            $this->schedule->update_data($this->_table,$data,"id=".$d[0]);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal aktifkan data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil aktifkan data');
            }


        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'shift_management/team/enable';
        $logMethod   = 'Enable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function disable($param)
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
        $this->schedule->update_data($this->_table,$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal dinonaktifkan data');
        }
        else
        {
            $this->db->trans_commit();
            echo $res=json_api(1, 'Data berhasil dinonaktifkan ');
        } 

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/schedule/enable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    function get_dock()
    {
        $port_id=$this->enc->decode($this->input->post('port'));

        empty($port_id)?$id='NULL':$id=$port_id;

        $row=$this->device->select_data("app.t_mtr_dock","where status=1 and port_id=".$id." order by name asc")->result();

        $data=array();
        foreach ($row as $key => $value) {
            $value->id=$this->enc->encode($value->id);
            $value->name=strtoupper($value->name);

            $data[]=$value;
        }

        echo json_encode($data);
    }

    function get_service()
    {
        $device_type_terminal_id=$this->enc->decode($this->input->post('device_type_terminal_id'));

        empty($device_type_terminal_id)?$id='NULL':$id=$device_type_terminal_id;

        $data=$this->device->select_data("app.t_mtr_device_terminal_type","where terminal_type_id=".$id."")->row();

        if(!empty($data))
        {
            $data->service_id==1?$tipe="REGULER":$tipe="EKSEKUTIF";
            echo json_encode($tipe);
        }
        else
        {
            echo json_encode("");
        }
        
    }

    function createCode()
    {
        $front_code=date('ymd');

        $chekCode=$this->db->query("select * from app.t_mtr_payment_howtopay where left(code,6)='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (code) as max_code from app.t_mtr_payment_howtopay where left(code,6)='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, 6, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

}
