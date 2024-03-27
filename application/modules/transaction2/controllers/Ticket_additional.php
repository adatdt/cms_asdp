<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Ticket_additional extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('M_ticket_additional','ticket');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        // $this->_table    = 'app.t_mtr_trx_bank';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction2/ticket_additional';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->ticket->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Perpanjangan Ticket Boarding',
            'content'  => 'ticket_additional/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Perpanjang Waktu';
        $data['btnSave'] =createBtnForm('Simpan');

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $abbr=trim($this->input->post('abbr'));
        $bank=trim($this->input->post('bank'));
        $transfer_fee=trim($this->input->post('transfer_fee'));

        $this->form_validation->set_rules('abbr', 'Bank ABBR ', 'required');
        $this->form_validation->set_rules('bank', 'Nama Bank ', 'required');
        $this->form_validation->set_rules('transfer_fee', 'Biaya Transfer ', 'required|numeric');

        $this->form_validation->set_message('required','%s harus diisi!')
                              ->set_message('numeric','%s Harus Berupa angka!');

        
        $data=array(
                    'bank_name'=>$bank,
                    'bank_abbr'=>strtolower($abbr),
                    'transfer_fee'=>$transfer_fee,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );


        // ceck data jika username sudah ada
        $check=$this->bank->select_data($this->_table," where upper(bank_abbr)=upper('".$abbr."') and status not in (-5) ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"ABBR sudah ada.");
        }
        else
        {

            $this->db->trans_begin();

            // $this->bank->insert_data($this->_table,$data);

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
        $logUrl      = site_url().'master_data/bank/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function getData()
    {
        $ticketNumber=trim($this->input->post("ticketNumber"));

        $checkService= $this->ticket->getService(" where b.ticket_number='{$ticketNumber}' or c.ticket_number='{$ticketNumber}' ");

        if(empty($ticketNumber))
        {
            $res=array("code"=>0, "message"=>"Nomer Ticket Kosong");
        }
        else if($checkService->num_rows()<1)
        {
            $res=array("code"=>0, "message"=>"Nomer Ticket Tidak ditemukan");
        }
        else
        {
            $serviceId= $checkService->row()->service_id;
            if($serviceId==1)
            {
                $dataPassenger=$this->ticket->getDataPassanger($serviceId,$ticketNumber)->row();
                $data=array($dataPassenger);
                $dataService="pnp";
            }
            else
            {
                $getBooking = $this->ticket->getBookingCode($ticketNumber)->row();

                $dataPassenger=$this->ticket->getDataPassanger($serviceId,"",$getBooking->booking_code)->result();


                $dataVehicle=$this->ticket->getDataVehicle($getBooking->booking_code)->row();  
                $data=array($dataPassenger, $dataVehicle); 
                $dataService="knd";
            }

            $res=array("code"=>1, "message"=>"Data di temukan","data"=>$data,"service"=>$dataService);
        }        

        echo json_encode($res);

    }



}
