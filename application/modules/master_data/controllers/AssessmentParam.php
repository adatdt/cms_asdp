<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class AssessmentParam extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('AssessmentParamModel','assessment');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_assessment_param';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'master_data/assessmentParam';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->assessment->dataList();
            // secirty validate param database
            $this->validate_param_datatable($_POST,$this->_module);
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Assessment Parameter',
            'content'  => 'assessmentParam/index',
            'btn_add'  => generate_button($this->_module, 'add', '<button onclick="showModal2(\''.site_url($this->_module.'/add').'\')" class="btn btn-sm btn-warning" title="Tambah"><i class="fa fa-plus"></i> Tambah</button>'),
            // 'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $data['title'] = 'Tambah Assessment Parameter';

        $this->load->view($this->_module.'/add',$data);
    }
    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');


        $type=strtolower(trim($this->input->post("type",true)));
        $titleText=trim($this->input->post("titleText",true));
        $info=trim($this->input->post("info",true));

        $groupType = trim($this->enc->decode($this->input->post("groupType",true)));
        $instructionText=trim($this->input->post("instructionText",true));

        $_POST['groupType'] = $groupType;
        $_POST['instructionText'] = $instructionText;

        $question=$this->input->post("question[]", true);
        $ordering=$this->input->post("ordering[]", true);

        $totalArrayOrdering=count($ordering);


        $dataQuestion=array();
        $dataOrdering=array();

        $dataQuestionError[]=0;
        $dataOrderingError[]=0;

        $checkDuplicationError[]=0;
        $checkDuplicationErrorMessage=array();        

        $indexOrdering=0;
        if($totalArrayOrdering>0);
        {
            foreach ($ordering as $key => $value) {
                $dataQuestion[]=trim($question[$key]);
                $dataOrdering[]=$value;

                if(empty($value))
                {
                    
                    $dataOrderingError[]=1;
                }

                if(empty($question[$key]))
                {
                    $dataQuestionError[]=1;
                }

                $indexOrdering=0;


                // check apakah ada order yang sama dalam satu inputan
                $checkDuplication=$this->checkDuplication($ordering, $value);

                if($checkDuplication>0)
                {

                    $checkDuplicationError[]=1;
                    $checkDuplicationErrorMessage[]=$value;
                }

                // $this->form_validation->set_rules("question[{$key}]"," Pertanyaan ",'required');
                $this->form_validation->set_rules("ordering[{$key}]"," Urutan ",'is_natural_no_zero');

            }

        }


        $this->form_validation->set_rules("type"," Tipe",'required|max_length[100]|alpha_dash');
        $this->form_validation->set_rules("titleText"," Judul Teks",'required|alpha_dash');
        $this->form_validation->set_rules("instructionText"," Intsruksi",'required');
        $this->form_validation->set_rules("groupType"," Grup tipe",'required|max_length[100]|alpha_dash');
        // $this->form_validation->set_rules("info"," Info Teks",'required');    

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');
        $this->form_validation->set_message('numeric','%s harus angka!');
        $this->form_validation->set_message('is_natural_no_zero','%s harus angka!');
        $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah !');
        $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');
        

        
        $data=array(
                    'type'=>$type,
                    'title_text'=>$titleText,
                    'instructions_text'=>htmlspecialchars_decode(base64_decode($instructionText)),
                    'info_text'=>htmlspecialchars_decode(base64_decode($info)),
                    'group_type'=>$groupType,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // ceck data jika nama tabel dengan status sama sudah ada
        $check=$this->assessment->select_data($this->_table," where upper(type)=upper(".$this->db->escape($type).")  and status <>'-5' ");
        

        if($this->form_validation->run()===false)
        {
            $messError = array_unique(explode("\n",validation_errors()));
            echo $res=json_api(0, implode(" ", $messError));

        }       
        else if(preg_match('/\s/',$type)) // tidak boleh ada spasi
        {
            echo $res=json_api(0,"Tipe Tidak Boleh ada Spasi.");   
        }                
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Tipe Sudah ada .");
        }
        // else if(array_sum($dataQuestionError)>0)
        // {
        //     echo $res=json_api(0,"Pertanyaan Tidak Boleh Kosong .");
        // }
        // else if(array_sum($dataOrderingError)>0)
        // {
        //     echo $res=json_api(0,"Urutan Tidak Boleh Kosong .");
        // }
        else if(array_sum($checkDuplicationError)>0)
        {
            $unique=array_unique($checkDuplicationErrorMessage);

            // echo $res=json_api(0,"Urutan ".implode(", ", $unique)." Tidak Boleh Sama");
            echo $res=json_api(0,"Urutan Parameter Tidak Boleh Sama");
        }        
        else
        {
            // echo $res=json_api(0, 'Gagal tambah data');
            // print_r($data); exit;

            $this->db->trans_begin();

            $id=$this->assessment->insertGetId($this->_table,$data);

            foreach ($dataOrdering as $key => $value) {
                $dataDetail=array(
                        'assessment_param_id'=>$id,
                        'question_text'=>htmlspecialchars_decode(base64_decode($dataQuestion[$key])),
                        'ordering'=>empty($value)?NULL:$value,
                        'status'=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                    );

                if(empty($value) and empty($dataQuestion[$key]) )
                {
                    "";
                }
                else
                {
                    $this->assessment->insert_data("app.t_mtr_assessment_param_detail",$dataDetail);
                }
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
        $logUrl      = site_url().'master_data/assessmentParam/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_add_24052021()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');


        $type=strtolower(trim($this->input->post("type")));
        $titleText=trim($this->input->post("titleText"));
        $info=trim($this->input->post("info"));
        $groupType=trim($this->enc->decode($this->input->post("groupType")));
        $instructionText=trim($this->input->post("instructionText"));
        $question=$this->input->post("question[]");
        $ordering=$this->input->post("ordering[]");



        $totalArrayOrdering=count($ordering);


        $dataQuestion=array();
        $dataOrdering=array();

        $dataQuestionError[]=0;
        $dataOrderingError[]=0;

        $checkDuplicationError[]=0;
        $checkDuplicationErrorMessage=array();        

        $indexOrdering=0;
        if($totalArrayOrdering>0);
        {
            foreach ($ordering as $key => $value) {
                $dataQuestion[]=$question[$key];
                $dataOrdering[]=$value;

                if(empty($value))
                {
                    
                    $dataOrderingError[]=1;
                }

                if(empty($question[$key]))
                {
                    $dataQuestionError[]=1;
                }

                $indexOrdering=0;


                // check apakah ada order yang sama dalam satu inputan
                $checkDuplication=$this->checkDuplication($ordering, $value);

                if($checkDuplication>0)
                {

                    $checkDuplicationError[]=1;
                    $checkDuplicationErrorMessage[]=$value;
                }

            }

        }



        $this->form_validation->set_rules("type"," Tipe",'required');
        $this->form_validation->set_rules("titleText"," Judul Teks",'required');
        $this->form_validation->set_rules("instructionText"," Intsruksi",'required');
        $this->form_validation->set_rules("groupType"," Grup tipe",'required');
        // $this->form_validation->set_rules("info"," Info Teks",'required');    

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');

        
        $data=array(
                    'type'=>$type,
                    'title_text'=>$titleText,
                    'instructions_text'=>htmlspecialchars_decode(str_replace("monkey-1777","style",$instructionText)),
                    'info_text'=>htmlspecialchars_decode(str_replace("monkey-1777","style",$info)),
                    'group_type'=>$groupType,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );

        // ceck data jika nama tabel dengan status sama sudah ada
        $check=$this->assessment->select_data($this->_table," where upper(type)=upper('".$type."')  and status <>'-5' ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }       
        else if(preg_match('/\s/',$type)) // tidak boleh ada spasi
        {
            echo $res=json_api(0,"Tipe Tidak Boleh ada Spasi.");   
        }                
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Tipe Sudah ada .");
        }
        // else if(array_sum($dataQuestionError)>0)
        // {
        //     echo $res=json_api(0,"Pertanyaan Tidak Boleh Kosong .");
        // }
        // else if(array_sum($dataOrderingError)>0)
        // {
        //     echo $res=json_api(0,"Urutan Tidak Boleh Kosong .");
        // }
        else if(array_sum($checkDuplicationError)>0)
        {
            $unique=array_unique($checkDuplicationErrorMessage);


            // echo $res=json_api(0,"Urutan ".implode(", ", $unique)." Tidak Boleh Sama");
            echo $res=json_api(0,"Urutan Parameter Tidak Boleh Sama");
        }        
        else
        {

            // print_r($data); exit;

            $this->db->trans_begin();

            $id=$this->assessment->insertGetId($this->_table,$data);

            foreach ($dataOrdering as $key => $value) {
                $dataDetail=array(
                        'assessment_param_id'=>$id,
                        'question_text'=>htmlspecialchars_decode(str_replace("monkey-1777","style",$dataQuestion[$key])),
                        'ordering'=>empty($value)?NULL:$value,
                        'status'=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                    );

                if(empty($value) and empty($dataQuestion[$key]) )
                {
                    "";
                }
                else
                {
                    $this->assessment->insert_data("app.t_mtr_assessment_param_detail",$dataDetail);
                }
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
        $logUrl      = site_url().'master_data/assessmentParam/action_add';
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

        $data['title'] = 'Edit Assessment Parameter';
        $data['id'] = $id;
        $data['detail']=$this->assessment->select_data($this->_table,"where id=$id_decode")->row();

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=$this->enc->decode($this->input->post('id')); 
   
        // $type=trim($this->input->post("type"));
        $titleText=trim($this->input->post("titleText",true));
        $info=trim($this->input->post("info",true));
        $instructionText=trim($this->input->post("instructionText",true));
        $groupType=trim($this->enc->decode($this->input->post("groupType",true)));

        $_POST['groupType'] = $groupType;
        $_POST['instructionText'] = $instructionText;

        // print_r($_POST);

        $question=$this->input->post("question[]",true);
        $ordering=$this->input->post("ordering[]",true);

        $totalArrayOrdering=count($ordering);

        $dataQuestion=array();
        $dataOrdering=array();

        $dataQuestionError[]=0;
        $dataOrderingError[]=0;

        $checkDuplicationError[]=0;
        $checkDuplicationErrorMessage=array();                

        $indexOrdering=0;
        if($totalArrayOrdering>0);
        {
            foreach ($ordering as $key => $value) {
                $dataQuestion[]=$question[$key];
                $dataOrdering[]=$value;

                if(empty($value))
                {
                    
                    $dataOrderingError[]=1;
                }

                if(empty($question[$key]))
                {
                    $dataQuestionError[]=1;
                }

                $indexOrdering=0;

                // check apakah ada order yang sama dalam satu inputan
                $checkDuplication=$this->checkDuplication($ordering, $value);

                if($checkDuplication>0)
                {

                    $checkDuplicationError[]=1;
                    $checkDuplicationErrorMessage[]=$value;
                }
                
                 // $this->form_validation->set_rules("question[{$key}]"," Pertanyaan ",'required');
                $this->form_validation->set_rules("ordering[{$key}]"," Urutan ",'is_natural_no_zero');

            }

        }

        $this->form_validation->set_rules("type"," Tipe",'required|max_length[100]|alpha_dash');
       $this->form_validation->set_rules("titleText","Judul Teks",'required');
        $this->form_validation->set_rules("instructionText"," Intsruksi",'required');
        // $this->form_validation->set_rules("info"," Info Teks",'required');
         $this->form_validation->set_rules("groupType"," Grup tipe",'required|max_length[100]|alpha_dash');  

         $this->form_validation->set_message('required','%s harus diisi!');
         $this->form_validation->set_message('integer','%s harus angka!');
         $this->form_validation->set_message('numeric','%s harus angka!');
         $this->form_validation->set_message('is_natural_no_zero','%s harus angka!');
         $this->form_validation->set_message('alpha_dash','%s tidak boleh karakter khusus kecuali strip dan garis bawah!');
         $this->form_validation->set_message('max_length','{field} Maximal  {param} karakter! ');

        
        $data=array(
                    // 'type'=>$type,
                    'title_text'=>$titleText,
                    'instructions_text'=>htmlspecialchars_decode(base64_decode($instructionText)),
                    'info_text'=>htmlspecialchars_decode(base64_decode($info)),
                    'updated_by'=>$this->session->userdata('username'),
                    'group_type'=>$groupType,
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        // print_r($data); exit;
        
        // ceck data jika nama tabel dengan status sama sudah ada
        // $check=$this->assessment->select_data($this->_table," where upper(tbl_name)=upper('".$tbl_name."') and status={$status} and id !={$id} ");


        if($this->form_validation->run()===false)
        {
            $messError = array_unique(explode("\n",validation_errors()));
            echo $res=json_api(0, implode(" ", $messError));
        }
        // else if(preg_match('/\s/',$type)) // tidak boleh ada spasi
        // {
        //     echo $res=json_api(0,"Tipe Tidak Boleh ada Spasi.");   
        // }                
        // else if($check->num_rows()>0)
        // {
        //     echo $res=json_api(0,"Tipe Sudah ada .");
        // }
        // else if(array_sum($dataQuestionError)>0)
        // {
        //     echo $res=json_api(0,"Pertanyaan Tidak Boleh Kosong .");
        // }
        // else if(array_sum($dataOrderingError)>0)
        // {
        //     echo $res=json_api(0,"Urutan Tidak Boleh Kosong .");
        // }
        else if(array_sum($checkDuplicationError)>0)
        {
            $unique=array_unique($checkDuplicationErrorMessage);

            // echo $res=json_api(0,"Urutan ".implode(", ", $unique)." Tidak Boleh Sama");
            echo $res=json_api(0,"Urutan Parameter Tidak Boleh Sama");
        }        
        else
        {

        $dataSoftdelete=array(
            'status'=>"-5",
            'updated_by'=>$this->session->userdata('username'),
            'updated_on'=>date("Y-m-d H:i:s"),
            );            
            $this->db->trans_begin();

            // echo $id; exit;
            $this->assessment->update_data($this->_table,$data,"id=".$this->db->escape($id));
            $this->assessment->update_data("app.t_mtr_assessment_param_detail",$dataSoftdelete,"assessment_param_id=$id");

            foreach ($dataOrdering as $key => $value) {
                $dataDetail=array(
                        'assessment_param_id'=>$id,
                        'question_text'=>htmlspecialchars_decode(base64_decode($dataQuestion[$key])),
                        'ordering'=>empty($value)?NULL:$value,
                        'status'=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                    );

                // $this->assessment->insert_data("app.t_mtr_assessment_param_detail",$dataDetail);

                if(empty($value) and empty($dataQuestion[$key]) )
                {
                    "";
                }
                else
                {
                    $this->assessment->insert_data("app.t_mtr_assessment_param_detail",$dataDetail);
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
        $logUrl      = site_url().'master_data/master_status/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function action_edit_24052022()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id')); 
   
        // $type=trim($this->input->post("type"));
        $titleText=trim($this->input->post("titleText"));
        $info=trim($this->input->post("info"));
        $instructionText=trim($this->input->post("instructionText"));
        $groupType=trim($this->enc->decode($this->input->post("groupType")));
        $question=$this->input->post("question[]");
        $ordering=$this->input->post("ordering[]");

        $totalArrayOrdering=count($ordering);

        $dataQuestion=array();
        $dataOrdering=array();

        $dataQuestionError[]=0;
        $dataOrderingError[]=0;

        $checkDuplicationError[]=0;
        $checkDuplicationErrorMessage=array();                

        $indexOrdering=0;
        if($totalArrayOrdering>0);
        {
            foreach ($ordering as $key => $value) {
                $dataQuestion[]=$question[$key];
                $dataOrdering[]=$value;

                if(empty($value))
                {
                    
                    $dataOrderingError[]=1;
                }

                if(empty($question[$key]))
                {
                    $dataQuestionError[]=1;
                }

                $indexOrdering=0;

                // check apakah ada order yang sama dalam satu inputan
                $checkDuplication=$this->checkDuplication($ordering, $value);

                if($checkDuplication>0)
                {

                    $checkDuplicationError[]=1;
                    $checkDuplicationErrorMessage[]=$value;
                }                

            }

        }

        $this->form_validation->set_rules("type"," Tipe",'required');
        $this->form_validation->set_rules("titleText"," Judul Teks",'required');
        $this->form_validation->set_rules("instructionText"," Intsruksi",'required');
        // $this->form_validation->set_rules("info"," Info Teks",'required');
        $this->form_validation->set_rules("groupType"," Grup tipe",'required');    

        $this->form_validation->set_message('required','%s harus diisi!');
        $this->form_validation->set_message('integer','%s harus angka!');

        
        $data=array(
                    // 'type'=>$type,
                    'title_text'=>$titleText,
                    'instructions_text'=>htmlspecialchars_decode(str_replace("monkey-1777","style",$instructionText)),
                    'info_text'=>htmlspecialchars_decode(str_replace("monkey-1777","style",$info)),
                    'updated_by'=>$this->session->userdata('username'),
                    'group_type'=>$groupType,
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );

        // print_r($data); exit;


        // ceck data jika nama tabel dengan status sama sudah ada
        // $check=$this->assessment->select_data($this->_table," where upper(tbl_name)=upper('".$tbl_name."') and status={$status} and id !={$id} ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        // else if(preg_match('/\s/',$type)) // tidak boleh ada spasi
        // {
        //     echo $res=json_api(0,"Tipe Tidak Boleh ada Spasi.");   
        // }                
        // else if($check->num_rows()>0)
        // {
        //     echo $res=json_api(0,"Tipe Sudah ada .");
        // }
        // else if(array_sum($dataQuestionError)>0)
        // {
        //     echo $res=json_api(0,"Pertanyaan Tidak Boleh Kosong .");
        // }
        // else if(array_sum($dataOrderingError)>0)
        // {
        //     echo $res=json_api(0,"Urutan Tidak Boleh Kosong .");
        // }
        else if(array_sum($checkDuplicationError)>0)
        {
            $unique=array_unique($checkDuplicationErrorMessage);


            // echo $res=json_api(0,"Urutan ".implode(", ", $unique)." Tidak Boleh Sama");
            echo $res=json_api(0,"Urutan Parameter Tidak Boleh Sama");
        }        
        else
        {

        $dataSoftdelete=array(
            'status'=>"-5",
            'updated_by'=>$this->session->userdata('username'),
            'updated_on'=>date("Y-m-d H:i:s"),
            );            
            $this->db->trans_begin();

            // echo $id; exit;
            $this->assessment->update_data($this->_table,$data,"id=$id");
            $this->assessment->update_data("app.t_mtr_assessment_param_detail",$dataSoftdelete,"assessment_param_id=$id");

            foreach ($dataOrdering as $key => $value) {
                $dataDetail=array(
                        'assessment_param_id'=>$id,
                        'question_text'=>htmlspecialchars_decode(str_replace("monkey-1777","style",$dataQuestion[$key])),
                        'ordering'=>empty($value)?NULL:$value,
                        'status'=>1,
                        'created_by'=>$this->session->userdata('username'),
                        'created_on'=>date("Y-m-d H:i:s"),
                    );

                // $this->assessment->insert_data("app.t_mtr_assessment_param_detail",$dataDetail);

                if(empty($value) and empty($dataQuestion[$key]) )
                {
                    "";
                }
                else
                {
                    $this->assessment->insert_data("app.t_mtr_assessment_param_detail",$dataDetail);
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
        $logUrl      = site_url().'master_data/master_status/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }



    public function action_delete_29112021($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        // full delete permanent tidak ganti flag

        $id = $this->enc->decode($id);

        $data=array('id'=>$id);

        $this->db->trans_begin();
        $this->assessment->delete_data($this->_table," id='".$id."'");

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
        $logUrl      = site_url().'master_data/master_status/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($id)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        // full delete permanent tidak ganti flag

        $id = $this->enc->decode($id);

        /* data */
        $data = array(
            'status' => '-5',
            'updated_on'=>date("Y-m-d H:i:s"),
            'updated_by'=>$this->session->userdata('username'),
        );


        $this->db->trans_begin();
        // $this->assessment->delete_data($this->_table," id='".$id."'");
        $this->assessment->update_data($this->_table,$data,"id=".$id);

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
        $logUrl      = site_url().'master_data/master_status/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }


    public function getDetail()
    {
        $id=$this->enc->decode($this->input->post("id"));

        $getDetail=$this->assessment->select_data("app.t_mtr_assessment_param_detail", " where assessment_param_id=$id and status=1 order by ordering asc")->result();

        $data=array();
        foreach ($getDetail as $key => $value) {
            $value->id=$this->enc->encode($value->id);
            $value->assessment_param_id=$this->enc->encode($value->assessment_param_id);

            $data[]=$value;
        }

        $data = array("data"=> $data,
                            "tokenHash" => $this->security->get_csrf_hash() ,
    );


        echo json_encode($data);
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
        $this->assessment->update_data($this->_table,$data,"id=".$d[0]);

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
        $logUrl      = site_url().'device_management/mobileVersion/action_delete';
        $logMethod   = $d[1] == 1 ? $logMethod = 'ENABLE' : $logMethod = 'DISABLE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function checkDuplication($data, $keyParam)
    {
        $return=0;
        foreach ($data as $key => $value) {
            if($value==$keyParam)
            {
                $return = $return +1 ;
            }
        }

        $returnData=$return>1?1:0;

        return $returnData;

    }


}
