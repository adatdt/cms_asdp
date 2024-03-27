<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );


class Payment_type extends MY_Controller{
    public function __construct(){
        parent::__construct();

        logged_in();
        $this->load->model('M_paymenttype','payment');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_payment_type';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/payment_type';
    }

    public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->payment->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Tipe Pembayaran',
            'content'  => 'payment_type/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'method'=> $this->payment->select_data("app.t_mtr_payment_method","where status='1' order by name asc")->result(),
        );

        $this->load->view('default', $data);
    }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Tipe Pembayaran';
        $data['method']=$this->payment->select_data("app.t_mtr_payment_method","where status='1' order by name asc")->result();
        $data['bank']=$this->payment->select_data("core.t_mtr_bank","where status='1' order by bank_name asc")->result();
        $data['pay_type']=$this->payment->select_data("app.t_mtr_pay_type","where status='1' order by name asc")->result();
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $name=trim($this->input->post('name'));
        $payment_type=trim($this->input->post('payment_type'));
        $payment_method=$this->enc->decode($this->input->post('payment_method'));
        $pay_type=$this->enc->decode($this->input->post('pay_type'));
        $bank_id=$this->enc->decode($this->input->post('bank'));
        $extra_fee=trim($this->input->post('extra_fee'));
        $status_web=trim($this->input->post('status_web'));
        $status_mobile=trim($this->input->post('status_mobile'));
        $status_mpos=trim($this->input->post('status_mpos'));
        $status_vm=trim($this->input->post('status_vm'));
        $order=trim($this->input->post('order'));
        $status_pos_passanger=trim($this->input->post('status_pos_passanger'));
        $status_pos_vehicle=trim($this->input->post('status_pos_vehicle'));
        $status_ifcs=trim($this->input->post('status_ifcs'));
        $status_b2b=trim($this->input->post('status_b2b'));
        $status_web_cs=trim($this->input->post('status_web_cs'));


        $this->form_validation->set_rules('name', 'Nama Payment', 'required');
        $this->form_validation->set_rules('payment_type', 'Tipe Pembayaran', 'required');
        $this->form_validation->set_rules('payment_method', 'Metode Pembayaran', 'required');
        $this->form_validation->set_rules('pay_type', 'Metode Pembayaran', 'required');
        $this->form_validation->set_rules('extra_fee', 'Biaya Tambahan', 'required');
         $this->form_validation->set_rules('order', 'Biaya Tambahan', 'required');
        // $this->form_validation->set_rules('status_web', 'status_web', 'required');
        // $this->form_validation->set_rules('status_mpos', 'status_mpos', 'required');
        // $this->form_validation->set_rules('status_vm', 'Status Mesin Vending', 'required');
        // $this->form_validation->set_rules('status_pos_passanger', 'Status POS Penumpang', 'required');
        // $this->form_validation->set_rules('status_pos_vehicle', 'Status POS Kendaraan', 'required');

        !empty($status_web)?$web=1:$web=0;
        !empty($status_mpos)?$mpos=1:$mpos=0;
        !empty($status_vm)?$vm=1:$vm=0;
        !empty($status_pos_passanger)?$passanger=1:$passanger=0;
        !empty($status_pos_vehicle)?$vehicle=1:$vehicle=0;
        !empty($status_ifcs)?$ifcs=1:$ifcs=0;
        !empty($status_mobile)?$mobile=1:$mobile=0;
        !empty($status_b2b)?$b2b=1:$b2b=0;
        !empty($status_web_cs)?$web_cs=1:$web_cs=0;

        $check_order=$this->payment->select_data($this->_table,' where payment_method_id='.$payment_method.' and "order"='.$order);

        $data=array(
                    'name'=>$name,
                    'payment_type'=>$payment_type,
                    'extra_fee'=>$extra_fee,
                    'payment_method_id'=>$payment_method,
                    'bank_id'=>empty($bank_id)?NULL:$bank_id,
                    'type_id'=>$pay_type,
                    'status_web'=>$web,
                    'order'=>$order,
                    'status_mpos'=>$mpos,
                    'status_vm'=>$vm,
                    'status_pos_passanger'=>$passanger,
                    'status_pos_vehicle'=>$vehicle,
                    'status_mobile'=>$mobile,
                    'status_ifcs'=>$ifcs,
                    'status_b2b'=>$b2b,
                    'status_web_cs'=>$web_cs,
                    'created_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata('username'),
                    );

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }
        else if(!is_numeric($extra_fee))
        {
            echo $res=json_api(0, 'Biaya tambahan tidak valid');   
        }
        else if($check_order->num_rows()>0)
        {
            echo $res=json_api(0, 'Order sudah digunakan'); 
        }
        else
        {
            $this->db->trans_begin();
            $this->payment->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/payment_type/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $payment_type_id=$this->enc->decode($id);

        $data['title'] = 'Edit Tipe Pembayaran';
        $data['bank']=$this->payment->select_data("core.t_mtr_bank","where status='1' order by bank_name asc")->result();
        $data['method']=$this->payment->select_data("app.t_mtr_payment_method","where status='1' order by name asc")->result();
        $data['detail']=$this->payment->select_data($this->_table,"where id='".$payment_type_id."' ")->row();
        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $payment_type_id=$this->enc->decode($this->input->post('payment_type_id'));


        $name=trim($this->input->post('name'));
        $payment_type=trim($this->input->post('payment_type'));
        $payment_method=$this->enc->decode($this->input->post('payment_method'));
        $bank_id=$this->enc->decode($this->input->post('bank'));
        $extra_fee=trim($this->input->post('extra_fee'));
        $status_web=trim($this->input->post('status_web'));
        $status_mobile=trim($this->input->post('status_mobile'));
        $status_mpos=trim($this->input->post('status_mpos'));
        $status_vm=trim($this->input->post('status_vm'));
        $order=trim($this->input->post('order'));
        $status_pos_passanger=trim($this->input->post('status_pos_passanger'));
        $status_pos_vehicle=trim($this->input->post('status_pos_vehicle'));
        $status_ifcs=trim($this->input->post('status_ifcs'));
        $status_b2b=trim($this->input->post('status_b2b'));
        $status_web_cs=trim($this->input->post('status_web_cs'));


        $this->form_validation->set_rules('name', 'Nama Payment', 'required');
        $this->form_validation->set_rules('payment_type', 'Tipe Pembayaran', 'required');
        $this->form_validation->set_rules('payment_method', 'Metode Pembayaran', 'required');
        $this->form_validation->set_rules('extra_fee', 'Biaya Tambahan', 'required');
        $this->form_validation->set_rules('order', 'Order', 'required');
        // $this->form_validation->set_rules('status_web', 'status_web', 'required');
        // $this->form_validation->set_rules('status_mpos', 'status_mpos', 'required');
        // $this->form_validation->set_rules('status_vm', 'Status Mesin Vending', 'required');
        // $this->form_validation->set_rules('status_pos_passanger', 'Status POS Penumpang', 'required');
        // $this->form_validation->set_rules('status_pos_vehicle', 'Status POS Kendaraan', 'required');

        !empty($status_web)?$web=1:$web=0;
        !empty($status_mpos)?$mpos=1:$mpos=0;
        !empty($status_vm)?$vm=1:$vm=0;
        !empty($status_pos_passanger)?$passanger=1:$passanger=0;
        !empty($status_pos_vehicle)?$vehicle=1:$vehicle=0;
        !empty($status_ifcs)?$ifcs=1:$ifcs=0;
        !empty($status_mobile)?$mobile=1:$mobile=0;
        !empty($status_b2b)?$b2b=1:$b2b=0;
        !empty($status_web_cs)?$web_cs=1:$web_cs=0;

        $data=array(
            'name'=>$name,
            'payment_type'=>$payment_type,
            'extra_fee'=>$extra_fee,
            'payment_method_id'=>$payment_method,
            'status_web'=>$web,
            'bank_id'=>empty($bank_id)?NULL:$bank_id,
            'order'=>$order,
            'status_mpos'=>$mpos,
            'status_vm'=>$vm,
            'status_pos_passanger'=>$passanger,
            'status_mobile'=>$mobile,
            'status_ifcs'=>$ifcs,
            'status_b2b'=>$b2b,
            'status_web_cs'=>$web_cs,
            'status_pos_vehicle'=>$vehicle,
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $check_order=$this->payment->select_data($this->_table,' where payment_method_id='.$payment_method.' and "order"='.$order.' and id !='.$payment_type_id);

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, 'Data masih ada yang kosong');
        }

        if($check_order->num_rows()>0)
        {
            echo $res=json_api(0, 'Order sudah digunakan');
        }
        else
        {
            $this->db->trans_begin();
            $this->payment->update_data($this->_table,$data,"id=$payment_type_id");

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
        $logUrl      = site_url().'master_data/payment_type/action_edit';
        $logMethod   = 'EDIT';
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

    function createCode($port)
    {
        $front_code="J".$port."".date('ymd');

        $chekCode=$this->db->query("select * from app.t_mtr_schedule where left(schedule_code,8)='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (schedule_code) as max_code from app.t_mtr_schedule where left(schedule_code,8)='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, 8, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

}
