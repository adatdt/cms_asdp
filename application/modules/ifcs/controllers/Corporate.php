<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Corporate extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_corporate','corporate');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_corporate_ifcs';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'ifcs/corporate';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->corporate->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Corporate IFCS',
            'content'  => 'corporate/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function listDetailContract(){   
        checkUrlAccess($this->_module,'detail');

        $rows = $this->corporate->detailContract();
        echo json_encode($rows);
    }    

    public function listDetailContract2(){   
        checkUrlAccess($this->_module,'detail');

        $rows = $this->corporate->detailContract2();
        echo json_encode($rows);
    }        

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data_sector=$this->corporate->select_data("app.t_mtr_business_sector_ifcs"," where status=1 order by description asc ")->result();
        $sector_company[null]="Pilih";

        foreach ($data_sector as $key => $value) {
            $sector_company[$value->business_sector_code]=$value->description;
        }

        $data['title'] = 'Tambah Corporate';
        $data['corporate_code']=$this->createCode();
        $data['sector_company'] =$sector_company;
        $data['key'] =0;

        $this->load->view($this->_module.'/add',$data);
    }

    public function add_contract($corporate_code){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $code=$this->enc->decode($corporate_code);

        $data_corporate=$this->corporate->select_data($this->_table, " where corporate_code='{$code}' ")->row();

        $data['title'] = 'Tambah Kontrak Corporate';
        $data['agreement_code'] =$this->createCodeAgreement();
        $data['data_corporate']=$data_corporate;

        $this->load->view($this->_module.'/add_contract',$data);
    }    

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $name=trim($this->input->post('name'));
        $telpon=trim($this->input->post('telphone'));
        $email=trim($this->input->post('email'));
        $address=trim($this->input->post('address'));
        $sector=trim($this->input->post('sector'));
        $code=trim($this->input->post('code'));
        $pic=trim($this->input->post('pic'));
        $position=trim($this->input->post('position'));
        $pic_email=trim($this->input->post('pic_email'));
        $pic_phone=trim($this->input->post('pic_phone'));


        $this->form_validation->set_rules('name', 'Nama Coporate ', 'required');
        $this->form_validation->set_rules('code', 'Kode Coporate ', 'required');
        $this->form_validation->set_rules('telphone', 'NO Telpon ', 'required|max_length[16]|numeric');
        $this->form_validation->set_rules('sector', ' Bidang Perusahaan ', 'required');
        $this->form_validation->set_rules('email', 'Email ', 'required|valid_email');
        $this->form_validation->set_rules('address', 'Alamat ', 'required');
        $this->form_validation->set_rules('pic', 'Nama PIC ', 'required');
        $this->form_validation->set_rules('position', 'Jabatan PIC ', 'required');
        $this->form_validation->set_rules('pic_email', 'Email PIC ', 'required');
        $this->form_validation->set_rules('pic_phone', 'Nomer Telpon PIC ', 'required|max_length[16]|numeric');

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('valid_email','%s Tidak sesuai format email')
                            ->set_message('max_length','%s Min 16 Karakter')
                            ->set_message('numeric','%s Harus angka');

        // print_r($branch); exit;
        
        $corporate_code=$this->createCode();
        $data=array(
                    'corporate_code'=>$corporate_code,
                    'corporate_name'=>$name,
                    'corporate_code'=>$code,
                    'email'=>$email,
                    'phone'=>$telpon,
                    'business_sector_code'=>$sector,
                    'corporate_address'=>$address,
                    'pic_name'=>$pic,
                    'pic_position'=>$position,
                    'pic_phone'=>$pic_phone,
                    'pic_email'=>$pic_email,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // create Cabang Pusat
        $data_branch=array(
            'corporate_code'=>$corporate_code,
            'branch_code'=>$this->createCodeBranch(),
            'description'=>"Pusat",
            'branch_ifcs_type'=>1, //1 untuk pusat 2 untuk cabang
            'status'=>1,
            'created_on'=>date("Y-m-d H:i:s"),
            'created_by'=>$this->session->userdata("username"),
        );

        // pic
        $data_pic=array(
                    'corporate_code'=>$corporate_code,
                    // 'pic_address'=>$pic_address,
                    'pic_name'=>$pic,
                    'pic_position'=>$position,
                    'pic_phone'=>$pic_phone,
                    'pic_email'=>$pic_email,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );



        $check=$this->corporate->select_data($this->_table, " where upper(corporate_name)=upper('{$name}') and status !='-5' ");
        $check_corporate_code=$this->corporate->select_data($this->_table, " where upper(corporate_code)=upper('{$code}') and status !='-5' ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama Corporate sudah ada");
        }
        else if($check_corporate_code->num_rows()>0)
        {
            echo $res=json_api(0,"Kode Corporate sudah ada");
        }        
        else
        {

            $this->db->trans_begin();

            $this->corporate->insert_data($this->_table,$data);
            $this->corporate->insert_data("app.t_mtr_branch_ifcs",$data_branch);
            $this->corporate->insert_data("app.t_mtr_pic_corporate_ifcs",$data_pic);


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
        $logUrl      = site_url().'ifcs/corporate/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function detail_contract($agreement_code){

        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $data['title'] = 'Detail Contract';
        $data['agreement_code'] = $agreement_code;

        $this->load->view($this->_module.'/detail_contract',$data);
    }

    public function action_add_contract()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $corporate_code=trim($this->input->post('corporate_code'));
        $corporate=trim($this->input->post('corporate'));
        $contract_number=trim($this->input->post('contract_number'));
        $start_date=trim($this->input->post('start_date'));
        $end_date=trim($this->input->post('end_date'));
        // $activation=trim($this->input->post('activation'));
        $order_number=trim($this->input->post('order_number'));

        $agreement_code=$this->createCodeAgreement();

        // input file upload manual php native, karena issue menggunakan library ci
        $filename = $_FILES['input_gambar']['name']; 

        $checking_format_file[]=0;

        if(!empty($filename))
        {
            $lokasi = $_FILES['input_gambar']['tmp_name'];
            $extensi = pathinfo($filename, PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
            $nama_baru=$agreement_code."_".date("YmdHis").".".$extensi;
            // move_uploaded_file($lokasi, "./uploads/".$nama_baru);

            strtoupper($extensi)<>"PDF"?$checking_format_file[]=1:"";                  
        }

        // echo $filename; exit;

        $this->form_validation->set_rules('corporate_code', 'Kode Coporate ', 'required');
        $this->form_validation->set_rules('corporate', 'Nama Corporate', 'required');
        $this->form_validation->set_rules('contract_number', 'Kode Kontrak', 'required');
        $this->form_validation->set_rules('start_date', 'Awal Kontrak ', 'required');
        $this->form_validation->set_rules('end_date', 'Akhir Kontrak ', 'required');
        // $this->form_validation->set_rules('activation', 'Activation ', 'required');
        $this->form_validation->set_rules('order_number', 'Urutan Kontrak ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $check_data=$this->corporate->select_data("app.t_mtr_corporate_agreement", " where corporate_code='{$corporate_code}' and status !='-5' ");

        // check order
        $check_data_order=$this->corporate->select_data("app.t_mtr_corporate_agreement", " where corporate_code='{$corporate_code}' and status !='-5' and order_number={$order_number} ");

        $check_data_detail=$this->corporate->select_data("app.t_mtr_corporate_agreement_detail", " where corporate_code='{$corporate_code}' and status !='-5' ");

        $check_agreement=$this->corporate->select_data("app.t_mtr_corporate_agreement", " where agreement_number='{$contract_number}' and status !='-5' ");

        // jika corporate detailnya lebih dari satu yang aktif, kemudian ambil data max , sebelum ke insert data

        $count_overlab[]=0;
        $get_last_agreement="";

        if($check_data->num_rows()>0)
        {
            // mengambil data max sebelum di insert detail nanti
            $get_last_agreement .=$this->corporate->get_last_agreement($corporate_code)->agreement_code;

            $get_data_max =$this->corporate->get_max_contract2( " where corporate_code='{$corporate_code}' and status !='-5' ")->row();
   
            //mencari data agar waktu tidak bentrok
            foreach ($check_data->result() as $key => $value) {

                if(($value->start_date<=$start_date and $value->end_date>=$start_date ) || ($value->start_date<=$end_date and $value->end_date>=$end_date ) )
                {
                    $count_overlab[]=1;   
                }
            }

        }

        // echo $get_data_max->reward_code; exit;

        // mendapatkan 6 bulan pertama
        $first_month=date('Y-m-d',strtotime("+0 month",strtotime($start_date)));
        $end_first_month=date('Y-m-d',strtotime("+6 month -1 day",strtotime($start_date)));
        $second_month=date('Y-m-d',strtotime("+1 days",strtotime($end_first_month)));
        $end_second_month=date('Y-m-d',strtotime("+0 days",strtotime($end_date)));

        $data=array(
                "agreement_code"=>$agreement_code,
                "agreement_number"=>$contract_number,
                "corporate_code"=>$corporate_code,
                "start_date"=>$start_date,
                "end_date"=>$end_date,
                "is_active"=>0,
                "order_number"=>$order_number,
                "last_agreement_code"=>$get_last_agreement,
                // "adjustment_date"=>date('Y-m-d',strtotime("+1 days",strtotime($end_date))),
                "created_by"=>$this->session->userdata("username"),
                "created_on"=>date("Y-m-d H:i:s"),
                "status"=>1,
            );            

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if(array_sum($count_overlab)>0)
        {
            echo $res=json_api(0," Tanggal Tidak Boleh bersinggungan");
        } 
        else if($check_data_order->num_rows()>0)
        {
            echo $res=json_api(0," Urutan Kontrak Sudah Ada");
        }         
        else if ($check_agreement->num_rows()>0)
        {
            echo $res=json_api(0," Nomer Kontrak Sudah Ada");  
        }
        else if(array_sum($checking_format_file)>0)
        {
            echo $res=json_api(0," Format File Harus PDF");     
        }
        else
        {
            // print_r($data_detail2); exit;

            $this->db->trans_begin();

            // update data sebelumnya jika sudah ada kontrak sebelumnya

            $last_reward="";

            if($check_data->num_rows()>0)
            {

                $update_data=array(
                    'start_date_reward'=>$first_month,
                    'end_date_reward'=>$end_first_month,
                    'updated_on'=>date("Y-m-d H:i:s"),
                    'created_by'=>$this->session->userdata("username"),
                );

                $this->corporate->update_data("app.t_mtr_corporate_agreement_detail",$update_data, " reward_code='{$get_data_max->reward_code}' ");

                $last_reward=$get_data_max->reward_code;
            }

            //  ketika file upload keiisi maka memindahkan file

            $path_file="";
            if(!empty($filename))
            {
                $folder_year=date('Y');

                $folder_month=date('m');

                // echo (base_url("uploads/".$folder_year)); exit;
                if(is_dir("./uploads/".$folder_year))
                {
                    if(is_dir("./uploads/".$folder_year."/".$folder_month))
                    {
                        // jika folder bulan dan tahun sudah terbentuk
                        // move_uploaded_file($lokasi, "./uploads/".$folder_year."/".$folder_month."/".$nama_baru);                        
                        $path_file="./uploads/".$folder_year."/".$folder_month."/".$nama_baru;   
                    }
                    else
                    {
                        // jika  bulan belum terbentuk
                         mkdir("./uploads/".$folder_year."/".$folder_month);
                         // move_uploaded_file($lokasi, "./uploads/".$folder_year."/".$folder_month."/".$nama_baru);
                         $path_file="./uploads/".$folder_year."/".$folder_month."/".$nama_baru;
                    }
                }
                else
                {
                    // jika folder tahun  dan bulan belum terbentuk
                        mkdir("./uploads/".$folder_year);
                        mkdir("./uploads/".$folder_year."/".$folder_month);
                        // move_uploaded_file($lokasi, "./uploads/".$folder_year."/".$folder_month."/".$nama_baru); 
                        $path_file="./uploads/".$folder_year."/".$folder_month."/".$nama_baru;  
                }
            }            

            $data['file_upload']=$path_file;
            $this->corporate->insert_data("app.t_mtr_corporate_agreement",$data);

            $reward_detail1=$this->createCodeReward2();

            $data_detail1=array(
                "agreement_code"=>$agreement_code,
                "reward_code"=>$reward_detail1,
                "corporate_code"=>$corporate_code,
                "start_date"=>$first_month,
                "end_date"=>$end_first_month,
                "adjustment_date"=>date('Y-m-d',strtotime("+1 days",strtotime($end_first_month))),
                "start_date_reward"=>$second_month,
                "end_date_reward"=>$end_second_month,
                "last_reward_code"=>$last_reward,
                // "adjustment_date"=>date('Y-m-d',strtotime("+1 days",strtotime($end_date))),
                "created_by"=>$this->session->userdata("username"),
                "created_on"=>date("Y-m-d H:i:s"),
                "status"=>1,
            );

            $this->corporate->insert_data("app.t_mtr_corporate_agreement_detail",$data_detail1);


            $data_detail2=array(
                "agreement_code"=>$agreement_code,
                "last_reward_code"=>$reward_detail1,
                "reward_code"=>$this->createCodeReward2(),
                "corporate_code"=>$corporate_code,
                "start_date"=>$second_month,
                "end_date"=>$end_second_month,
                "adjustment_date"=>date('Y-m-d',strtotime("+1 days",strtotime($end_second_month))),
                "start_date_reward"=>null,
                "end_date_reward"=>null,
                // "adjustment_date"=>date('Y-m-d',strtotime("+1 days",strtotime($end_date))),
                "created_by"=>$this->session->userdata("username"),
                "created_on"=>date("Y-m-d H:i:s"),
                "status"=>1,
            );

            $this->corporate->insert_data("app.t_mtr_corporate_agreement_detail",$data_detail2);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                if(!empty($filename))
                {
                    move_uploaded_file($lokasi,$path_file); 
                }
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');

            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'ifcs/corporate/action_add_contract';
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

        // mengambik data corporate
        $detail=$this->corporate->select_data($this->_table,"where id=$id_decode");

        // get ranch data 
        $get_branch=$this->corporate->select_data("app.t_mtr_branch_ifcs", " where corporate_code='{$detail->row()->corporate_code}' and status=1 ")->result();


        // mengambil datra sector
        $data_sector=$this->corporate->select_data("app.t_mtr_business_sector_ifcs"," where status=1 order by description asc ")->result();
        $sector_company[null]="Pilih";

        foreach ($data_sector as $key => $value) {
            $sector_company[$value->business_sector_code]=$value->description;
        }

        $data['title'] = 'Edit Corporate';
        $data['id'] = $id;
        $data['sector_company']=$sector_company;
        $data['detail']=$detail->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function edit_contract($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');
        $id_decode=$this->enc->decode($id);

        // mengambik data corporate
        $detail=$this->corporate->select_data("app.t_mtr_corporate_agreement","where id=$id_decode");
        $data_corporate=$this->corporate->select_data("app.t_mtr_corporate_ifcs","where corporate_code='{$detail->row()->corporate_code}' ");

        if(empty($detail->row()->file_upload))
        {
            $link="";
        }
        else
        {

            $file_array=explode("/",$detail->row()->file_upload);
            $file_key=max(array_keys($file_array));
            $link="<a href='".base_url($detail->row()->file_upload)."' target='_blank'><i class='fa fa-file-pdf-o' aria-hidden='true'></i> {$file_array[$file_key]}</a>";
        }

        $data['title'] = 'Edit Kontrak Corporate';
        $data['id'] = $id;
        $data['detail']=$detail->row();
        $data['filename']=$link;
        $data['data_corporate']=$data_corporate->row();

        $this->load->view($this->_module.'/edit_contract',$data);   
    }   

    public function edit_reward($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id_decode=$this->enc->decode($id);

        $detail=$this->corporate->get_corporate_detail($id_decode)->row();

        $action=array(NULL=>"Pilih","1"=>"TAMBAH (+) ","2"=>"KURANG (-)");

        $data['title'] = 'Edit Reward';
        $data['action']=$action;
        $data['id'] = $id;
        $data['detail']=$detail;

        $this->load->view($this->_module.'/edit_reward',$data);   
    }     

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('corporate'));

        $name=trim($this->input->post('name'));
        $telpon=trim($this->input->post('telphone'));
        $email=trim($this->input->post('email'));
        $address=trim($this->input->post('address'));
        $sector=trim($this->input->post('sector'));
        $pic=trim($this->input->post('pic'));
        $position=trim($this->input->post('position'));
        $pic_phone=trim($this->input->post('pic_phone'));
        $pic_email=trim($this->input->post('pic_email'));



        $this->form_validation->set_rules('corporate', 'Nama Coporate ', 'required');
        $this->form_validation->set_rules('sector', 'Nama Bidang Perusahaan ', 'required');
        $this->form_validation->set_rules('name', 'Nama Coporate ', 'required');
        $this->form_validation->set_rules('telphone', 'NO Telpon ', 'required');
        $this->form_validation->set_rules('email', 'Email ', 'required|valid_email');
        $this->form_validation->set_rules('address', 'Alamat ', 'required');
        $this->form_validation->set_rules('pic', 'Nama PIC ', 'required');
        $this->form_validation->set_rules('position', 'Jabatan PIC ', 'required');
        $this->form_validation->set_rules('pic_phone', 'Nomer Telepon PIC ', 'required');
        $this->form_validation->set_rules('pic_email', 'Email PIC ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('valid_email','%s Tidak sesuai format email');

        $data_corporate=$this->corporate->select_data($this->_table," where id={$id}")->row();                            


        $data=array(
                    
                    'corporate_name'=>$name,
                    'phone'=>$telpon,
                    'email'=>$email,
                    'business_sector_code'=>$sector,
                    'corporate_address'=>$address,
                    'pic_name'=>$pic,
                    'pic_position'=>$position,
                    'pic_phone'=>$pic_phone,
                    'pic_email'=>$pic_email,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

                $data_pic=array(
                    'pic_name'=>$pic,
                    'pic_position'=>$position,
                    'pic_phone'=>$pic_phone,
                    'pic_email'=>$pic_email,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        $check=$this->corporate->select_data($this->_table, " where upper(corporate_name)=upper('{$name}') and status !='-5' and id !='{$id}' ");

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama Corporate sudah ada");
        }                
        else
        {

            // echo "berhasil"; exit;
            // print_r($data); exit;
            $this->db->trans_begin();            

            // update corporate 
            $this->corporate->update_data($this->_table,$data,"id=$id");

            // update corporate 
            $this->corporate->update_data("app.t_mtr_pic_corporate_ifcs",$data_pic,"corporate_code='{$data_corporate->corporate_code}'");


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
        $logUrl      = site_url().'ifcs/corporate/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
  
    public function action_edit_contract()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $id=$this->enc->decode($this->input->post('id'));
        $corporate_code=trim($this->input->post('corporate_code'));
        $contract_number=trim($this->input->post('contract_number'));
        $start_date=trim($this->input->post('start_date'));
        $end_date=trim($this->input->post('end_date'));
        $activation=trim($this->input->post('activation'));
        $last_agreement_code=trim($this->input->post('last_agreement_code'));
        $agreement_code=trim($this->input->post('agreement_code'));
        $order_number=trim($this->input->post('order_number'));

        // input file upload manual php native, karena issue menggunakan library ci
        $filename =$_FILES['input_gambar']['name']; 

        $checking_format_file[]=0;

        if(!empty($filename))
        {
            $lokasi = $_FILES['input_gambar']['tmp_name'];
            $extensi = pathinfo($filename, PATHINFO_EXTENSION); // mengambil extensi file yg di upload 
            $nama_baru=$agreement_code."_".date("YmdHis").".".$extensi;
            // move_uploaded_file($lokasi, "./uploads/".$nama_baru);

            strtoupper($extensi)<>"PDF"?$checking_format_file[]=1:"";                  
        }

        $this->form_validation->set_rules('id', 'id ', 'required');
        $this->form_validation->set_rules('corporate', 'Nama Corporate', 'required');
        $this->form_validation->set_rules('start_date', 'Awal Kontrak ', 'required');
        $this->form_validation->set_rules('end_date', 'Akhir Kontrak ', 'required');
        $this->form_validation->set_rules('contract_number', 'Nomer Kontrak ', 'required');
        // $this->form_validation->set_rules('activation', 'Aktivasi ', 'required');
        $this->form_validation->set_rules('contract_number', 'Urutan Kontrak ', 'required');


        $this->form_validation->set_message('required','%s harus diisi!');

        $check_data=$this->corporate->select_data("app.t_mtr_corporate_agreement", " where corporate_code='{$corporate_code}' and status !='-5' and id!={$id} ");

        $count_overlab[]=0;
        if(!empty($check_data))
        {
            foreach ($check_data->result() as $key => $value) 
            {
                if(($value->start_date<=$start_date and $value->end_date>=$start_date ) || ($value->start_date<=$end_date and $value->end_date>=$end_date ) )
                {
                    $count_overlab[]=1;   
                }            
            }
        }

        // mendapatkan 6 bulan pertama
        $first_month=date('Y-m-d',strtotime("+0 month",strtotime($start_date)));
        $end_first_month=date('Y-m-d',strtotime("+6 month -1 day",strtotime($start_date)));
        $second_month=date('Y-m-d',strtotime("+1 days",strtotime($end_first_month)));
        $end_second_month=date('Y-m-d',strtotime("+0 days",strtotime($end_date)));        


        $get_min_agreement_detail=$this->corporate->get_min_contract2( " where corporate_code='{$corporate_code}' and status !='-5' and agreement_code='{$agreement_code}' ")->row();

        $get_max_agreement_detail =$this->corporate->get_max_contract2( " where corporate_code='{$corporate_code}' and status !='-5' and agreement_code='{$agreement_code}' ")->row();

        $get_max_last_agreement_detail =$this->corporate->get_max_contract2( " where corporate_code='{$corporate_code}' and status !='-5' and agreement_code='{$last_agreement_code}' ")->row();

        $get_data_agreement=$this->corporate->select_data("app.t_mtr_corporate_agreement", " where agreement_code='{$agreement_code}'")->row();

        $check_data_order=$this->corporate->select_data("app.t_mtr_corporate_agreement", " where corporate_code='{$corporate_code}' and status!='-5' and order_number={$order_number} and agreement_code<>'{$agreement_code}' ")->num_rows();

        $last_path_file=$get_data_agreement->file_upload; // mengambil path file 

        // echo $last_path_file; exit;

        // print_r($get_min_agreement_detail); exit;
        // echo $get_min_agreement_detail->reward_code; exit;

        // echo $get_max_last_agreement_detail->reward_code ; exit;

        $data=array(
            "start_date"=>$start_date,
            "end_date"=>$end_date,
            "order_number"=>$order_number,
            "updated_by"=>$this->session->userdata("username"),
            "updated_on"=>date("Y-m-d H:i:s"),
        );

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if(array_sum($count_overlab)>0)
        {
            echo $res=json_api(0," Tanggal Tidak Boleh bersinggungan");
        }
        else if($check_data_order>0)
        {
            echo $res=json_api(0,"Urutan Kontrak Sudah Ada");
        }        
        else if(array_sum($checking_format_file)>0)
        {
            echo $res=json_api(0," Format File Harus PDF");     
        }        
        else
        {
            // echo $last_path_file; exit;

            $update_data1=array(
                "start_date"=>$first_month,
                "end_date"=>$end_first_month,
                "adjustment_date"=>date('Y-m-d',strtotime("+1 days",strtotime($end_first_month))),
                "start_date_reward"=>$second_month,
                "end_date_reward"=>$end_second_month,
                "updated_by"=>$this->session->userdata("username"),
                "updated_on"=>date("Y-m-d H:i:s"),
            );        

            $update_data2=array(

                "start_date"=>$second_month,
                "end_date"=>$end_second_month,
                "adjustment_date"=>date('Y-m-d',strtotime("+1 days",strtotime($end_second_month))),
                "updated_by"=>$this->session->userdata("username"),
                "updated_on"=>date("Y-m-d H:i:s"),
            );

            $update_data3=array(
                "start_date_reward"=>$first_month,
                "end_date_reward"=>$end_first_month,
                "updated_by"=>$this->session->userdata("username"),
                "updated_on"=>date("Y-m-d H:i:s"),
            );

            $this->db->trans_begin();

            $path_file="";
            if(!empty($filename))
            {
                $folder_year=date('Y');

                $folder_month=date('m');

                // echo (base_url("uploads/".$folder_year)); exit;
                if(is_dir("./uploads/".$folder_year))
                {
                    if(is_dir("./uploads/".$folder_year."/".$folder_month))
                    {
                        // jika folder bulan dan tahun sudah terbentuk
                        // move_uploaded_file($lokasi, "./uploads/".$folder_year."/".$folder_month."/".$nama_baru);                        
                        $path_file="./uploads/".$folder_year."/".$folder_month."/".$nama_baru;   
                    }
                    else
                    {
                        // jika  bulan belum terbentuk
                         mkdir("./uploads/".$folder_year."/".$folder_month);
                         // move_uploaded_file($lokasi, "./uploads/".$folder_year."/".$folder_month."/".$nama_baru);
                         $path_file="./uploads/".$folder_year."/".$folder_month."/".$nama_baru;
                    }
                }
                else
                {
                    // jika folder tahun  dan bulan belum terbentuk
                        mkdir("./uploads/".$folder_year);
                        mkdir("./uploads/".$folder_year."/".$folder_month);
                        // move_uploaded_file($lokasi, "./uploads/".$folder_year."/".$folder_month."/".$nama_baru); 
                        $path_file="./uploads/".$folder_year."/".$folder_month."/".$nama_baru;  
                }

                $data['file_upload']=$path_file;                   
            }            
         
            // update data
            $this->corporate->update_data("app.t_mtr_corporate_agreement",$data," id={$id}");

            // update data reward data yang pertama...dalam detail agreement code
            $this->corporate->update_data("app.t_mtr_corporate_agreement_detail",$update_data1," reward_code='{$get_min_agreement_detail->reward_code}'");

            // update data reward data yang akhir...dalam detail agreement code
            $this->corporate->update_data("app.t_mtr_corporate_agreement_detail",$update_data2," reward_code='{$get_max_agreement_detail->reward_code}'");

            if(!empty($get_max_last_agreement_detail))
            {

                // update data last reward sebelumnya kontrak 
                $this->corporate->update_data("app.t_mtr_corporate_agreement_detail",$update_data3," reward_code='{$get_max_last_agreement_detail->reward_code}'");            
            }

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();
                echo $res=json_api(1, 'Berhasil tambah data');

                if(!empty($filename))// check apakah filenya diisi/ tidak kosong
                {
                    if(!empty($last_path_file)) // check apakah ada last pathnya
                    {
                        if (file_exists($last_path_file)) { // check apakah path tersedia
                            unlink($last_path_file); // menghapus path file data sebelumnya
                        }
                    }

                    move_uploaded_file($lokasi,$path_file); // memindahkan file upload ke lokal 
                }
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'ifcs/corporate/action_edit_contract';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    

    public function action_edit_reward()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id'));

        $add_reward=trim($this->input->post('add_reward'));
        $action=$this->input->post('action_select');


        $this->form_validation->set_rules('add_reward', 'Total Reward ', 'required|numeric');
        $this->form_validation->set_rules('action_select', 'Aksi ', 'required');
        $this->form_validation->set_rules('name', 'Nama Coporate ', 'required');;

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('numeric','%s Harus angka');

        $check_data=$this->corporate->select_data("app.t_mtr_corporate_agreement_detail", " where id={$id} ")->row();

        $data_corporate=$this->corporate->select_data($this->_table," where id={$id}")->row();


        $this->db->trans_begin();

        $check_data=$this->corporate->select_data("app.t_mtr_corporate_agreement_detail", " where id={$id} for update ")->row();                            

        $data=array(                    
            'updated_by'=>$this->session->userdata('username'),
            'updated_on'=>date("Y-m-d H:i:s"),
        );

        $data_log=array("reward_code"=>$check_data->reward_code,
                        "last_total_reward"=>$check_data->total_reward,
                        "value"=>$add_reward,
                        "status"=>1,
                        "created_on"=>date("Y-m-d H:i:s"),
                        "created_by"=>$this->session->userdata("username"),            
                        );

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        elseif ($check_data->total_reward<0) 
        {
            echo $res=json_api(0, 'Estimasi Reward di bawah 0');  
        }                
        else
        {
            // jika di ambil
            if($action==2)
            {
                if(($check_data->total_reward-$add_reward)<0)
                {
                    echo $res=json_api(0, 'Estimasi Reward di bawah 0');  
                }
                else
                {
                    $total_reward=$check_data->total_reward-$add_reward;
                    $data["total_reward"]=$total_reward;
                    $data_log["total_reward"]=$total_reward;
                    $data_log["action"]="Mengurangi";
                    // update corporate 
                    $this->corporate->update_data("app.t_mtr_corporate_agreement_detail",$data,"id=$id");
                    $this->corporate->insert_data("app.t_trx_update_reward_ifcs",$data_log);


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
            }
            else
            {
                $total_reward=$check_data->total_reward+$add_reward;
                $data["total_reward"]=$total_reward;
                $data_log["total_reward"]=$total_reward;
                $data_log["action"]="Menambahkan";

                // update corporate 
                $this->corporate->update_data("app.t_mtr_corporate_agreement_detail",$data,"id=$id");
                $this->corporate->insert_data("app.t_trx_update_reward_ifcs",$data_log);


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

        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'ifcs/corporate/action_edit_reward';
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
        $this->corporate->update_data($d[2],$data,"id=".$d[0]);

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
        $logUrl      = site_url().'ifcs/corporate/action_change';
        $d[1]==1?$logMethod='ENABLE':$logMethod='DISABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function activation_contract($param)
    {
        validate_ajax();
        $p = $this->enc->decode($param);
        $d = explode('|', $p);

        /* data */
        $data = array(
            'is_active' => $d[1],
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        $this->db->trans_begin();
        $this->corporate->update_data($d[2],$data,"id=".$d[0]);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            if ($d[1]==1)
            {
                echo $res=json_api(0, 'Kontrak Gagal aktif');
            }
            else
            {
                echo $res=json_api(0, 'Kontrak Gagal non aktif');
            }
            
        }
        else
        {
            $this->db->trans_commit();
            if ($d[1]==1)
            {
                echo $res=json_api(1, 'Kontrak Berhasil aktif');
            }
            else
            {
                echo $res=json_api(1, 'Kontrak Berhasil non aktif');
            }
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'ifcs/corporate/activation_contract';
        $d[1]==1?$logMethod='ACTIVATION':$logMethod='NON ACTIVE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }    

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $data=array(
            'status'=>'-5',
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
            );

        $id = $this->enc->decode($id);

        $this->db->trans_begin();
        $this->corporate->update_data($this->_table,$data," id='".$id."'");
        $get_corporate_code=$this->corporate->select_data($this->_table," where id='".$id."'")->row();
        $this->corporate->update_data("app.t_mtr_branch_ifcs", $data, " corporate_code='{$get_corporate_code->corporate_code}' ");

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
        $logUrl      = site_url().'ifcs/corporate/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function detail($code){

        $corporate_code=$this->enc->decode($code);

        if (empty($corporate_code)) {
               redirect('error_404');
               exit;
           }   

        $btn_add=generate_button($this->_module,'add', '<button onclick="showModal(\''.site_url($this->_module.'/add_contract/'.$code).'\')" class="btn btn-sm btn-warning" title="Tambah"><i class="fa fa-plus"></i> Tambah Kontrak</button> ');   

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'parent' => 'Corporate IFCS',
            'url_parent' => site_url($this->_module),
            'title'    => 'Detail Corporate IFCS',
            'corporate_code'=>$corporate_code,
            'content'  => 'corporate/detail',
            'btn_add'  => $btn_add,
        );

        $this->load->view('default', $data);
    }

    public function get_branch(){   
        checkUrlAccess($this->_module,'detail');
            $rows = $this->corporate->dataListBranch();
            echo json_encode($rows);
    }

    public function get_detail_contract(){   
        checkUrlAccess($this->_module,'detail');
            $rows = $this->corporate->dataListContract();
            echo json_encode($rows);
    }    


    function createCode()
    {
        $front_code="CP".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_corporate_ifcs where left(corporate_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $code=$front_code."0001";
            return $code;
        }
        else
        {
            $max=$this->db->query("select max (corporate_code) as max_code from app.t_mtr_corporate_ifcs where left(corporate_code,".$total_length.")='".$front_code."' ")->row();

            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

    function createCodeBranch()
    {
        $front_code="CPB".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_branch_ifcs where left(branch_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $code=$front_code."0001";
            return $code;
        }
        else
        {
            $max=$this->db->query("select max (branch_code) as max_code from app.t_mtr_branch_ifcs where left(branch_code,".$total_length.")='".$front_code."' ")->row();

            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }

    function createCodeReward()
    {
        $front_code="CPR".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_corporate_ifcs_detail where left(reward_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $code=$front_code."0001";
            return $code;
        }
        else
        {
            $max=$this->db->query("select max (reward_code) as max_code from app.t_mtr_corporate_ifcs_detail where left(reward_code,".$total_length.")='".$front_code."' ")->row();

            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }


    function createCodeReward2()
    {
        $front_code="CPR".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_corporate_agreement_detail where left(reward_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $code=$front_code."0001";
            return $code;
        }
        else
        {
            $max=$this->db->query("select max (reward_code) as max_code from app.t_mtr_corporate_agreement_detail where left(reward_code,".$total_length.")='".$front_code."' ")->row();

            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }


    function createCodeAgreement()
    {
        $front_code="CA".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_corporate_agreement where left(agreement_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $code=$front_code."0001";
            return $code;
        }
        else
        {
            $max=$this->db->query("select max (agreement_code) as max_code from app.t_mtr_corporate_agreement where left(agreement_code,".$total_length.")='".$front_code."' ")->row();

            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }                

}
