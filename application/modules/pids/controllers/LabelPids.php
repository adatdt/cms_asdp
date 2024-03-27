<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class LabelPids extends MY_Controller{
  public function __construct(){
    parent::__construct();

        logged_in();
        $this->load->model('LabelPidsModel','label');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_label_pids';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pids/labelPids';
  }

  public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            // $this->validate_param_datatable($_POST,$this->_module);
            $rows = $this->label->dataList();
            echo json_encode($rows);
            exit;
        }


        $port=$this->label->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();

        $dataPort[]="Pilih";
        foreach ($port as $key => $value) 
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);    
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Label PIDS',
            'content'  => 'labelPids/index',
            "port" =>$dataPort, 
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),

        );

    $this->load->view('default', $data);
  }

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=$this->label->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();

        $dataPort[]="Pilih";
        foreach ($port as $key => $value) 
        {
            $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);    
        }

        $data['title'] = 'Tambah Label PIDS';
        $data['port']=$dataPort;

        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add()
    {
        validate_ajax();

        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $port=$this->enc->decode($this->input->post('port'));
        $name=trim($this->input->post('name'));
        $labelName=trim($this->input->post('label_name'));
        $labelNameEn=trim($this->input->post('label_name_en'));

        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('name', 'Nama ', 'required');
        $this->form_validation->set_rules('label_name', 'Nama Label (ID)', 'required');
        $this->form_validation->set_rules('label_name_en', 'Nama Label (EN)', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        // echo ; exit;
        
        $data=array(
                    'name'=>strtolower($name),
                    'in_label'=>$labelName,
                    'en_label'=>$labelNameEn,
                    'port_id'=>$port,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date("Y-m-d H:i:s"),
                    );


        // ceck data jika username sudah ada
        $check=$this->label->select_data($this->_table," where name='".strtolower($name)."' and port_id='{$port}' and status not in (-5) ");


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0,validation_errors());
        }
        else if(preg_match('/\s/',$name)) // tidak boleh ada spasi
        {
            echo $res=json_api(0,"Nama Tidak Boleh ada Spasi.");   
        }
        else if($check->num_rows()>0)
        {
            echo $res=json_api(0,"Nama Sudah Ada.");
        }
        else
        {

            $this->db->trans_begin();

            $this->label->insert_data($this->_table,$data);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal tambah data');
            }
            else
            {
                $this->db->trans_commit();
                $returnData = array("portId"=>$port);
                echo $res=json_api(1, 'Berhasil tambah data');
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/labelPids/action_add';
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

        $detail=$this->label->select_data($this->_table, " where id={$id_decode} ")->row();
        $port=$this->label->select_data("app.t_mtr_port", " where status=1 order by name asc")->result();

        $dataPort[]="Pilih";
        $dataPortSelected="";
        foreach ($port as $key => $value) 
        {
            if($value->id==$detail->port_id)
            {
                $dataPortSelected=$this->enc->encode($value->id);
                $dataPort[$dataPortSelected]=strtoupper($value->name);    
            }
            else
            {

                $dataPort[$this->enc->encode($value->id)]=strtoupper($value->name);    
            }
        }

        $data['title'] = 'Edit Label PIDS';
        $data['id'] = $id;
        $data['port']=$dataPort;
        $data['portSelected']=$dataPortSelected;
        $data['detail']=$detail;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'edit');


        $id=$this->enc->decode($this->input->post('id'));

        $port=$this->enc->decode($this->input->post('port'));
        $labelName=trim($this->input->post('label_name'));
        $labelNameEn=trim($this->input->post('label_name_en'));


        $this->form_validation->set_rules('port', 'Pelabuhan ', 'required');
        $this->form_validation->set_rules('label_name', 'Nama Label (ID)', 'required');
        $this->form_validation->set_rules('label_name_en', 'Nama Label (EN)', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        
        $data=array(
                    'in_label'=>$labelName,
                    'en_label'=>$labelNameEn,
                    'port_id'=>$port,
                    'updated_by'=>$this->session->userdata('username'),
                    'updated_on'=>date("Y-m-d H:i:s"),
                    );


        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {

            $this->db->trans_begin();

            $this->label->update_data($this->_table,$data,"id=$id");

            if ($this->db->trans_status() === FALSE)
            {   
                $this->db->trans_rollback();
                echo $res=json_api(0, 'Gagal edit data');
            }
            else
            {
                $this->db->trans_commit();
                $returnData = array("portId"=>$port);
                echo $res=json_api(1, 'Berhasil edit data', $returnData );
            }
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/labelPids/action_edit';
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
        $this->label->update_data($this->_table,$data,"id=".$d[0]);

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
            $port = $this->label->select_data($this->_table, " where id=".$d[0])->row()->port_id;
            $returnData = array("portId"=>$port);

            if ($d[1]==1)
            {                
                echo $res=json_api(1, 'Berhasil aktif data', $returnData);
            }
            else
            {
                echo $res=json_api(1, 'Berhasil non aktif data', $returnData);
            }
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/labelPids/action_change';
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

        $this->db->trans_begin();
        $this->label->update_data($this->_table,$data," id='".$id."'");

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=json_api(0, 'Gagal delete data');
        }
        else
        {
            $this->db->trans_commit();
            $port = $this->label->select_data($this->_table, " where id=".$id)->row()->port_id;
            $returnData = array("portId"=>$port);
            echo $res=json_api(1, 'Berhasil delete data', $returnData);
        }   

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pids/labelPids/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

}
