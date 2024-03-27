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
            'title'    => 'Coporate IFCS',
            'content'  => 'corporate/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
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


        $this->form_validation->set_rules('name', 'Nama Coporate ', 'required');
        $this->form_validation->set_rules('code', 'Kode Coporate ', 'required');
        $this->form_validation->set_rules('telphone', 'NO Telpon ', 'required');
        $this->form_validation->set_rules('sector', ' Bidang Perusahaan ', 'required');
        $this->form_validation->set_rules('email', 'Email ', 'required|valid_email');
        $this->form_validation->set_rules('address', 'Alamat ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('valid_email','%s Tidak sesuai format email');

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


    // public function action_add()
    // {
    //     validate_ajax();

    //     $this->global_model->checkAccessMenuAction($this->_module,'add');

    //     $name=trim($this->input->post('name'));
    //     $telpon=trim($this->input->post('telphone'));
    //     $email=trim($this->input->post('email'));
    //     $address=trim($this->input->post('address'));
    //     $sector=trim($this->input->post('sector'));

    //     $branch=$this->input->post('branch[]');

    //     $this->form_validation->set_rules('name', 'Nama Coporate ', 'required');
    //     $this->form_validation->set_rules('telphone', 'NO Telpon ', 'required');
    //     $this->form_validation->set_rules('sector', ' Bidang Perusahaan ', 'required');
    //     $this->form_validation->set_rules('email', 'Email ', 'required|valid_email');
    //     $this->form_validation->set_rules('address', 'Alamat ', 'required');

    //     $this->form_validation->set_message('required','%s harus diisi!')
    //                         ->set_message('valid_email','%s Tidak sesuai format email');

    //     $is_ok=array();
    //     if(empty($branch))
    //     {
    //         // ketika tidak ada kiriman maka dianggap sukses
    //         $err_branch=0;
    //     }
    //     else
    //     {
    //         $check_arr_value[]=0;
    //         foreach ($branch as $key => $value) {
    //             // check datanya apakah ada yang salam
    //             if(empty($value))
    //             {
    //                 $check_arr_value[]=1;
    //             }
    //             else
    //             {

    //                 $is_ok[]=trim($value); // jika branch diisi , dan baranch tersebut tidak ada yang kosong
    //             }

    //         }

    //         // jika data dalam array nya ada yang kososng
    //         if(array_sum($check_arr_value)>0)
    //         {
    //            $err_branch=1; 
    //         }
    //         else
    //         {
    //             $err_branch=0;
    //         }
    //     }

    //     // print_r($branch); exit;
        
    //     $corporate_code=$this->createCode();
    //     $data=array(
    //                 'corporate_code'=>$corporate_code,
    //                 'corporate_name'=>$name,
    //                 'email'=>$email,
    //                 'phone'=>$telpon,
    //                 'business_sector_code'=>$sector,
    //                 'corporate_address'=>$address,
    //                 'status'=>1,
    //                 'created_by'=>$this->session->userdata('username'),
    //                 'created_on'=>date("Y-m-d H:i:s"),
    //                 );

    //     $check=$this->corporate->select_data($this->_table, " where upper(corporate_name)=upper('{$name}') and status !='-5' ");

    //     if($this->form_validation->run()===false)
    //     {
    //         echo $res=json_api(0,validation_errors());
    //     }
    //     else if($check->num_rows()>0)
    //     {
    //         echo $res=json_api(0,"Nama Corporate sudah ada");
    //     }
    //     else if ($err_branch>0)
    //     {
    //         echo $res=json_api(0,"Nama Cabang Corporate masih ada yang kosong");
    //     }
    //     else
    //     {

    //         $this->db->trans_begin();

    //         $this->corporate->insert_data($this->_table,$data);

    //         // jika data branch diisi dan datanya sudah ok maka di save
    //         if(count($is_ok)>0)
    //         {
    //             foreach (array_unique($is_ok) as $key => $value) {
    //                 $data_branch=array(
    //                     'corporate_code'=>$corporate_code,
    //                     'branch_code'=>$this->createCodeBranch(),
    //                     'description'=>$value,
    //                     'status'=>1,
    //                     'created_on'=>date("Y-m-d H:i:s"),
    //                     'created_by'=>$this->session->userdata("username"),
    //                 );
    //                 $this->corporate->insert_data("app.t_mtr_branch_ifcs",$data_branch);
    //             }
    //         }

    //         if ($this->db->trans_status() === FALSE)
    //         {
    //             $this->db->trans_rollback();
    //             echo $res=json_api(0, 'Gagal tambah data');
    //         }
    //         else
    //         {
    //             $this->db->trans_commit();
    //             echo $res=json_api(1, 'Berhasil tambah data');
    //         }
    //     }


    //      /* Fungsi Create Log */
    //     $createdBy   = $this->session->userdata('username');
    //     $logUrl      = site_url().'ifcs/corporate/action_add';
    //     $logMethod   = 'ADD';
    //     $logParam    = json_encode($data);
    //     $logResponse = $res;

    //     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    // }

    public function action_add_contract()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $corporate_code=trim($this->input->post('corporate_code'));
        $corporate=trim($this->input->post('corporate'));
        $start_date=trim($this->input->post('start_date'));
        $end_date=trim($this->input->post('end_date'));


        $this->form_validation->set_rules('corporate_code', 'Kode Coporate ', 'required');
        $this->form_validation->set_rules('corporate', 'Nama Corporate', 'required');
        $this->form_validation->set_rules('start_date', 'Awal Kontrak ', 'required');
        $this->form_validation->set_rules('end_date', 'Akhir Kontrak ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $check_data=$this->corporate->select_data("app.t_mtr_corporate_ifcs_detail", " where corporate_code='{$corporate_code}' and status !='-5' ");

        // jika corporate detailnya lebih dari satu yang aktif, kemudian ambil data max , sebelum ke insert data

        $count_overlab[]=0;
        $last_reward_code="";
        if($check_data->num_rows()>0)
        {
            $get_max=$this->corporate->get_max_contract(" where corporate_code='{$corporate_code}' and status!='-5'")->row();

            $last_reward_code .=$get_max->reward_code;

            //mencari data agar waktu tidak bentrok
            foreach ($check_data->result() as $key => $value) {

                if(($value->start_date<=$start_date and $value->end_date>=$start_date ) || ($value->start_date<=$end_date and $value->end_date>=$end_date ) )
                {
                    $count_overlab[]=1;   
                }
            }

        }

        // echo array_sum($count_overlab); exit;

        $data=array(
            "reward_code"=>$this->createCodeReward(),
            "corporate_code"=>$corporate_code,
            "start_date"=>$start_date,
            "end_date"=>$end_date,
            "adjustment_date"=>date('Y-m-d',strtotime("+1 days",strtotime($end_date))),
            "created_by"=>$this->session->userdata("username"),
            "created_on"=>date("Y-m-d H:i:s"),
            "last_reward_code"=>$last_reward_code,
            "status"=>1,
        );

        $update_data=array(
            "start_date_reward"=>$start_date,
            "end_date_reward"=>$end_date,
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
        else
        {

            $this->db->trans_begin();

            $this->corporate->insert_data("app.t_mtr_corporate_ifcs_detail",$data);

            // jika bukan kontrak pertama maka update data kontrak yang sebelumnya
            if($check_data->num_rows()>0)
            {
                $this->corporate->update_data("app.t_mtr_corporate_ifcs_detail",$update_data," reward_code='{$get_max->reward_code}' ");
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


    // public function edit($id)
    // {
    //     validate_ajax();
    //     $this->global_model->checkAccessMenuAction($this->_module,'edit');

    //     $id_decode=$this->enc->decode($id);

    //     // mengambik data corporate
    //     $detail=$this->corporate->select_data($this->_table,"where id=$id_decode");

    //     // get ranch data 
    //     $get_branch=$this->corporate->select_data("app.t_mtr_branch_ifcs", " where corporate_code='{$detail->row()->corporate_code}' and status=1 ")->result();

    //     $detail_branch="";
    //     $key_branch=0;

    //     if(!empty($get_branch))
    //     {
    //         foreach ($get_branch as $key => $value) {
                
    //             $detail_branch .="<div class='row ' id='branch_b".$key."'>
    //                 <div class='col-md-6 form-group' >
    //                     <label>Cabang</label>
    //                     <input type='text' name='branch_b[".$key."]' class='form-control'  placeholder='Cabang Perusahaan' value='".$value->description."' required>
    //                     <input type='hidden' name='code_b[".$key."]' class='form-control' value='".$value->branch_code."' required>
    //                 </div>
    //             </div>";

    //             $key_branch++;
    //         }

    //     }

    //     // mengambil datra sector
    //     $data_sector=$this->corporate->select_data("app.t_mtr_business_sector_ifcs"," where status=1 order by description asc ")->result();
    //     $sector_company[null]="Pilih";

    //     foreach ($data_sector as $key => $value) {
    //         $sector_company[$value->business_sector_code]=$value->description;
    //     }

    //     $data['title'] = 'Edit Corporate';
    //     $data['id'] = $id;
    //     $data['detail_branch'] = $detail_branch;
    //     $data['key'] = $key_branch;
    //     $data['sector_company']=$sector_company;
    //     $data['detail']=$detail->row();

    //     $this->load->view($this->_module.'/edit',$data);   
    // }


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
        $detail=$this->corporate->select_data("app.t_mtr_corporate_ifcs_detail","where id=$id_decode");
        $data_corporate=$this->corporate->select_data("app.t_mtr_corporate_ifcs","where corporate_code='{$detail->row()->corporate_code}' ")->row();

        $data['title'] = 'Edit Kontrak Corporate';
        $data['id'] = $id;
        $data['data_corporate']=$data_corporate;
        $data['detail']=$detail->row();

        $this->load->view($this->_module.'/edit_contract',$data);   
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


        $this->form_validation->set_rules('corporate', 'Nama Coporate ', 'required');
        $this->form_validation->set_rules('sector', 'Nama Bidang Perusahaan ', 'required');
        $this->form_validation->set_rules('name', 'Nama Coporate ', 'required');
        $this->form_validation->set_rules('telphone', 'NO Telpon ', 'required');
        $this->form_validation->set_rules('email', 'Email ', 'required|valid_email');
        $this->form_validation->set_rules('address', 'Alamat ', 'required');

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('valid_email','%s Tidak sesuai format email');

        $data_corporate=$this->corporate->select_data($this->_table," where id={$id}")->row();                            

        $is_ok=array();
        $get_input_duplicate[]=0;
        $get_input_duplicate2[]=0;
        if(empty($branch))
        {
            // ketika tidak ada kiriman maka dianggap sukses
            $err_branch=0;
        }
        else
        {
            $check_arr_value[]=0;
            foreach ($branch as $key => $value) {
                // check datanya apakah ada yang kosong
                if(empty($value))
                {
                    $check_arr_value[]=1;
                }
                else
                {
                    $is_ok[]=trim($value); // jika branch diisi , dan baranch tersebut tidak ada yang kosong
                }

            }

            // jika data dalam array nya ada yang kososng
            if(array_sum($check_arr_value)>0)
            {
               $err_branch=1; 
            }
            else
            {
                $err_branch=0;
            }

            // pengecekan apakah inputnya duplikat/ sama persis, checkin in input data
            foreach(array_count_values($is_ok) as $key=>$value )
            {
                // checkin in input data
                if($value>1)
                {
                    $get_input_duplicate[]=1;
                }
            }

            // pengecekan apakah inputnya duplikat/ sama persis, checkin data in db
            foreach($is_ok as $key=>$value )
            {
                //checkin data in db
                $check_data=$this->corporate->select_data("app.t_mtr_branch_ifcs", " where upper(description)=upper('{$value}') and corporate_code='{$data_corporate->corporate_code}' and status<>'-5' ");

                if($check_data->num_rows()>0)
                {
                    $get_input_duplicate2[]=1;   
                }
            }            

        }

        if(!empty($branch_b))
        {
            foreach ($branch_b as $key => $value) {
                
                // pengecekan apakah inputnya duplikat/ sama persis antara input branch_b dan branch
                // cheking in input data
                if(in_array($value, $is_ok))
                {
                    $get_input_duplicate[]=1;
                }

                //checkin data in db
                $check_data=$this->corporate->select_data("app.t_mtr_branch_ifcs", " where upper(description)=upper('".$value."') and corporate_code='{$data_corporate->corporate_code}' and status<>'-5' and branch_code<>'{$code_b[$key]}' ");

                if($check_data->num_rows()>0)
                {
                    $get_input_duplicate2[]=1;   
                }
            }
        }

        $data=array(
                    
                    'corporate_name'=>$name,
                    'phone'=>$telpon,
                    'email'=>$email,
                    'business_sector_code'=>$sector,
                    'corporate_address'=>$address,
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
        else if ($err_branch>0)
        {
            echo $res=json_api(0,"Nama Cabang Corporate masih ada yang kosong");
        }
        elseif (array_sum($get_input_duplicate)>0) 
        {
            echo $res=json_api(0,"Input Cabang tidak boleh sama");
        }
        elseif (array_sum($get_input_duplicate2)>0) 
        {
            echo $res=json_api(0,"Nama Cabang sudah ada");
        }                
        else
        {

            // print_r($data); exit;
            $this->db->trans_begin();            

            // update corporate 
            $this->corporate->update_data($this->_table,$data,"id=$id");

            // update data branch
            if(count($code_b)>0)
            {
                foreach ($code_b as $key => $value) {

                    $update_branch=array(
                            'description'=>$branch_b[$key],
                            'status'=>1,
                            'updated_on'=>date("Y-m-d H:i:s"),
                            'updated_by'=>$this->session->userdata("username"),
                    );

                    $this->corporate->update_data("app.t_mtr_branch_ifcs",$update_branch,"branch_code='{$value}' ");   
                    
                }
            }

            // jika data branch diisi dan datanya sudah ok maka di save
            if(count($is_ok)>0)
            {

                foreach (array_unique($is_ok) as $key => $value) {

                    $data_branch=array(
                        'corporate_code'=>$data_corporate->corporate_code,
                        'branch_code'=>$this->createCodeBranch(),
                        'description'=>$value,
                        'status'=>1,
                        'created_on'=>date("Y-m-d H:i:s"),
                        'created_by'=>$this->session->userdata("username"),
                    );                 

                    // jika data sudah ada maka datanya akan di di update ke 1 kembali
                    // insert data jika data nya belum ada
                    $this->corporate->insert_data("app.t_mtr_branch_ifcs",$data_branch);
                }
            }            

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
    // public function action_edit()
    // {
    //     validate_ajax();
    //     $this->global_model->checkAccessMenuAction($this->_module,'edit');


    //     $id=$this->enc->decode($this->input->post('corporate'));

    //     $name=trim($this->input->post('name'));
    //     $telpon=trim($this->input->post('telphone'));
    //     $email=trim($this->input->post('email'));
    //     $address=trim($this->input->post('address'));
    //     $sector=trim($this->input->post('sector'));

    //     $branch=$this->input->post('branch[]');
    //     $branch_b=$this->input->post('branch_b[]');
    //     $code_b=$this->input->post('code_b[]');

    //     $this->form_validation->set_rules('corporate', 'Nama Coporate ', 'required');
    //     $this->form_validation->set_rules('sector', 'Nama Bidang Perusahaan ', 'required');
    //     $this->form_validation->set_rules('name', 'Nama Coporate ', 'required');
    //     $this->form_validation->set_rules('telphone', 'NO Telpon ', 'required');
    //     $this->form_validation->set_rules('email', 'Email ', 'required|valid_email');
    //     $this->form_validation->set_rules('address', 'Alamat ', 'required');

    //     $this->form_validation->set_message('required','%s harus diisi!')
    //                         ->set_message('valid_email','%s Tidak sesuai format email');

    //     $data_corporate=$this->corporate->select_data($this->_table," where id={$id}")->row();                            

    //     $is_ok=array();
    //     $get_input_duplicate[]=0;
    //     $get_input_duplicate2[]=0;
    //     if(empty($branch))
    //     {
    //         // ketika tidak ada kiriman maka dianggap sukses
    //         $err_branch=0;
    //     }
    //     else
    //     {
    //         $check_arr_value[]=0;
    //         foreach ($branch as $key => $value) {
    //             // check datanya apakah ada yang kosong
    //             if(empty($value))
    //             {
    //                 $check_arr_value[]=1;
    //             }
    //             else
    //             {
    //                 $is_ok[]=trim($value); // jika branch diisi , dan baranch tersebut tidak ada yang kosong
    //             }

    //         }

    //         // jika data dalam array nya ada yang kososng
    //         if(array_sum($check_arr_value)>0)
    //         {
    //            $err_branch=1; 
    //         }
    //         else
    //         {
    //             $err_branch=0;
    //         }

    //         // pengecekan apakah inputnya duplikat/ sama persis, checkin in input data
    //         foreach(array_count_values($is_ok) as $key=>$value )
    //         {
    //             // checkin in input data
    //             if($value>1)
    //             {
    //                 $get_input_duplicate[]=1;
    //             }
    //         }

    //         // pengecekan apakah inputnya duplikat/ sama persis, checkin data in db
    //         foreach($is_ok as $key=>$value )
    //         {
    //             //checkin data in db
    //             $check_data=$this->corporate->select_data("app.t_mtr_branch_ifcs", " where upper(description)=upper('{$value}') and corporate_code='{$data_corporate->corporate_code}' and status<>'-5' ");

    //             if($check_data->num_rows()>0)
    //             {
    //                 $get_input_duplicate2[]=1;   
    //             }
    //         }            

    //     }

    //     if(!empty($branch_b))
    //     {
    //         foreach ($branch_b as $key => $value) {
                
    //             // pengecekan apakah inputnya duplikat/ sama persis antara input branch_b dan branch
    //             // cheking in input data
    //             if(in_array($value, $is_ok))
    //             {
    //                 $get_input_duplicate[]=1;
    //             }

    //             //checkin data in db
    //             $check_data=$this->corporate->select_data("app.t_mtr_branch_ifcs", " where upper(description)=upper('".$value."') and corporate_code='{$data_corporate->corporate_code}' and status<>'-5' and branch_code<>'{$code_b[$key]}' ");

    //             if($check_data->num_rows()>0)
    //             {
    //                 $get_input_duplicate2[]=1;   
    //             }
    //         }
    //     }

    //     $data=array(
                    
    //                 'corporate_name'=>$name,
    //                 'phone'=>$telpon,
    //                 'email'=>$email,
    //                 'business_sector_code'=>$sector,
    //                 'corporate_address'=>$address,
    //                 'updated_by'=>$this->session->userdata('username'),
    //                 'updated_on'=>date("Y-m-d H:i:s"),
    //                 );

    //     $check=$this->corporate->select_data($this->_table, " where upper(corporate_name)=upper('{$name}') and status !='-5' and id !='{$id}' ");

    //     if($this->form_validation->run()===false)
    //     {
    //         echo $res=json_api(0,validation_errors());
    //     }
    //     else if($check->num_rows()>0)
    //     {
    //         echo $res=json_api(0,"Nama Corporate sudah ada");
    //     }
    //     else if ($err_branch>0)
    //     {
    //         echo $res=json_api(0,"Nama Cabang Corporate masih ada yang kosong");
    //     }
    //     elseif (array_sum($get_input_duplicate)>0) 
    //     {
    //         echo $res=json_api(0,"Input Cabang tidak boleh sama");
    //     }
    //     elseif (array_sum($get_input_duplicate2)>0) 
    //     {
    //         echo $res=json_api(0,"Nama Cabang sudah ada");
    //     }                
    //     else
    //     {

    //         // print_r($data); exit;
    //         $this->db->trans_begin();            

    //         // update corporate 
    //         $this->corporate->update_data($this->_table,$data,"id=$id");

    //         // update data branch
    //         if(count($code_b)>0)
    //         {
    //             foreach ($code_b as $key => $value) {

    //                 $update_branch=array(
    //                         'description'=>$branch_b[$key],
    //                         'status'=>1,
    //                         'updated_on'=>date("Y-m-d H:i:s"),
    //                         'updated_by'=>$this->session->userdata("username"),
    //                 );

    //                 $this->corporate->update_data("app.t_mtr_branch_ifcs",$update_branch,"branch_code='{$value}' ");   
                    
    //             }
    //         }

    //         // jika data branch diisi dan datanya sudah ok maka di save
    //         if(count($is_ok)>0)
    //         {

    //             foreach (array_unique($is_ok) as $key => $value) {

    //                 $data_branch=array(
    //                     'corporate_code'=>$data_corporate->corporate_code,
    //                     'branch_code'=>$this->createCodeBranch(),
    //                     'description'=>$value,
    //                     'status'=>1,
    //                     'created_on'=>date("Y-m-d H:i:s"),
    //                     'created_by'=>$this->session->userdata("username"),
    //                 );                 

    //                 // jika data sudah ada maka datanya akan di di update ke 1 kembali
    //                 // insert data jika data nya belum ada
    //                 $this->corporate->insert_data("app.t_mtr_branch_ifcs",$data_branch);
    //             }
    //         }            

    //         if ($this->db->trans_status() === FALSE)
    //         {   
    //             $this->db->trans_rollback();
    //             echo $res=json_api(0, 'Gagal edit data');
    //         }
    //         else
    //         {
    //             $this->db->trans_commit();
    //             echo $res=json_api(1, 'Berhasil edit data');
    //         }
    //     }


    //      /* Fungsi Create Log */
    //     $createdBy   = $this->session->userdata('username');
    //     $logUrl      = site_url().'ifcs/corporate/action_edit';
    //     $logMethod   = 'EDIT';
    //     $logParam    = json_encode($data);
    //     $logResponse = $res;

    //     $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    // }

    public function action_edit_contract()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $id=$this->enc->decode($this->input->post('id'));
        $corporate_code=trim($this->input->post('corporate_code'));
        $corporate=trim($this->input->post('corporate'));
        $start_date=trim($this->input->post('start_date'));
        $end_date=trim($this->input->post('end_date'));
        $last_reward_code=trim($this->input->post('last_reward_code'));


        $this->form_validation->set_rules('id', 'id ', 'required');
        $this->form_validation->set_rules('corporate', 'Nama Corporate', 'required');
        $this->form_validation->set_rules('start_date', 'Awal Kontrak ', 'required');
        $this->form_validation->set_rules('end_date', 'Akhir Kontrak ', 'required');


        $this->form_validation->set_message('required','%s harus diisi!');

        $check_data=$this->corporate->select_data("app.t_mtr_corporate_ifcs_detail", " where corporate_code='{$corporate_code}' and status !='-5' and id!={$id} ");

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

        // echo array_sum($count_overlab); exit;

        $data=array(
            "start_date"=>$start_date,
            "end_date"=>$end_date,
            "adjustment_date"=>date('Y-m-d',strtotime("+1 days",strtotime($end_date))),
            "updated_by"=>$this->session->userdata("username"),
            "updated_on"=>date("Y-m-d H:i:s"),
        );

        // update reward sebelumnya
        $update_data=array(
            "start_date_reward"=>$start_date,
            "end_date_reward"=>$end_date,
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
        else
        {

            $this->db->trans_begin();

            // update data
            $this->corporate->update_data("app.t_mtr_corporate_ifcs_detail",$data," id={$id}");

            // update data reward sebelumnya
            $this->corporate->update_data("app.t_mtr_corporate_ifcs_detail",$update_data," reward_code='{$last_reward_code}'");

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
        $logUrl      = site_url().'ifcs/corporate/action_add_contract';
        $logMethod   = 'ADD';
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
            'parent' => 'Coporate IFCS',
            'url_parent' => site_url($this->_module),
            'title'    => 'Detail Coporate IFCS',
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

}
