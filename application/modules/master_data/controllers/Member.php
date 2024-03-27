<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );


class Member extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('M_member','member');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->load->library('Html2pdf');

        $this->_table    = 'app.t_mtr_member';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/member';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->member->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Member',
            'content'  => 'member/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'method'=> $this->member->select_data("app.t_mtr_payment_method","where status='1' order by name asc")->result(),
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');
        $data['title'] = 'Tambah Perangkat';
        $data['method']=$this->member->select_data("app.t_mtr_payment_method","where status='1' order by name asc")->result();
        $data['bank']=$this->member->select_data("core.t_mtr_bank","where status='1' order by bank_name asc")->result();
        $data['pay_type']=$this->member->select_data("app.t_mtr_pay_type","where status='1' order by name asc")->result();
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
            $this->member->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'shift_management/member/enable';
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
        $this->member->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'pelabuhan/member/enable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }




    public function enable_15042021($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);

        $d = explode('|', $p);

        /* data */
        $data = array(
            'is_activation' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


            $this->db->trans_begin();
            $this->member->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'shift_management/member/enable';
        $logMethod   = 'Enable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function disable_15042021($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);

        $d = explode('|', $p);

        /* data */
        $data = array(
            'is_activation' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->member->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'pelabuhan/member/enable';
        $logMethod   = 'Disable';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function downloadExcel()
    {
        $this->global_model->checkAccessMenuAction($this->_module, 'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB

        $dateFrom = $this->input->get("dateFrom");
        $dateTo = $this->input->get("dateTo");

        $data = $this->member->download();
        // echo json_encode($data); exit;

        $file_name = 'Member Ferizy Tanggal Daftar' . $dateFrom . ' s/d ' . $dateTo;
        $this->load->library('XLSExcel');
        $styles1 = array('height' => 50, 'widths' => [5, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20], 'font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');


        $header = array(
            'NO' => 'string',
            "NIK"=>"string",
            "NAMA"=>"string",
            "TANGGAL LAHIR"=>"string",
            "ALAMAT"=>"string",
            "NO. TELEPON"=>"string",
            "EMAIL"=>"string",
            "TANGGAL PENDAFTARAN"=>"string",
            "STATUS"=>"string",
        );        

        $no = 1;
        foreach ($data as $key => $value) {
            
            $rows[] = array(
                $no,
                $value->nik,
                $value->full_name,
                $value->date_of_birth,
                $value->address,
                $value->phone_number,
                $value->email,
                $value->created_on,
                $value->status,
            );
            $no++;
        }

        $writer = new XLSXWriter();

        $writer->writeSheetHeader('Sheet1', $header, $styles1);

        foreach ($rows as $row)
            $writer->writeSheetRow('Sheet1', $row);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->writeToStdOut();
    }

    public function downloadPdf()
    {
        // $this->global_model->checkAccessMenuAction($this->_module,'download_excel');
        ini_set('memory_limit', '4095M'); // 4 GBs minus 1 MB
        
        $dateFrom=$this->input->get("dateFrom");
        $dateTo=$this->input->get("dateTo");
        

        $data['data'] = $this->member->download();
        $data['departDateFrom'] = $dateFrom;
        $data['departDateTo'] = $dateTo;

        // print_r($data); exit;

        // echo "hai";
        $this->load->view('member/pdf',$data);

    }        


}
