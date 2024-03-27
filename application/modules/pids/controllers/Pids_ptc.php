<?php

// use function GuzzleHttp\json_encode;

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Pids_ptc extends MY_Controller{

	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_pids_ptc','stc');
        $this->load->model('info/pids_model','pids');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pids/pids_ptc';

        header("Access-Control-Allow-Origin: *");
    }

    function index(){
        checkUrlAccess(uri_string(),'view');
        $identity_app = $this->stc->cek_identity_app();
        $sess_port    = $this->session->userdata('port_id');


        if($identity_app == 0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $port[""]="Pilih";
                $port_name = 'SERVER CLOUD';
                $data_port=$this->stc->select_data("app.t_mtr_port", " where status=1 order by name asc ")->result();
            }
            else
            {
                $row = $this->global_model->selectById('app.t_mtr_port', 'id', $this->session->userdata("port_id"));
                $port_name = 'SERVER CLOUDE PELABUHAN '.$row->name;
                $data_port=$this->stc->select_data("app.t_mtr_port", " where id=".$this->session->userdata("port_id"))->result();
            }

        }
        else
        {
            $row = $this->global_model->selectById('app.t_mtr_port', 'id', $identity_app);
            if ($row) 
            {
                $port_name = 'SERVER '.strtoupper($row->name).' PELABUHAN '.$row->name;
                $data_port=$this->stc->select_data("app.t_mtr_port", " where id={$identity_app}  ")->result();
            }
            else
            {   
                $port[""]="Pilih";
                $port_name = 'Server Tidak di ketahui';
                $data_port="";
            }
        }

        if(!empty($data_port))
        {
            foreach ($data_port as $key => $value) {
                $port[$this->enc->encode($value->id)]=strtoupper($value->name);
            }            
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'INFORMATION DISPLAY',
            'content'  => 'pids_ptc/index',
            // 'port'     => $this->stc->get_list_port(),
            'port'     => $port,
            // 'port_id'  => $this->enc->encode($sess_port),
            'port_name' => $port_name,
            'socket_url' => $_SERVER['HTTP_HOST'].':3000',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            // 'socket_url' => 'http://dev.nutech-integrasi.com:3000',
            'problem' => array(
                array('id' => 'list-anchor','problem' => $this->enc->encode(2),'list' => 'List Anchor', 'title' => 'Anchor'),
                array('id' => 'list-docking','problem' => $this->enc->encode(3),'list' => 'List Docking', 'title' => 'Docking'),
                array('id' => 'list-broken','problem' => $this->enc->encode(4),'list' => 'List Rusak', 'title' => 'Rusak'),
            ),
            'url_problem' => site_url($this->_module.'/problem')
        );

        $this->load->view('default', $data);
    }

    function add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $identity_app=$this->stc->select_data("app.t_mtr_identity_app","")->row();


        $dock[""]="Pilih";
        $ship_pairing[""]="Pilih";

        if($identity_app->port_id==0)
        {
            if(empty($this->session->userdata("port_id")))
            {
                $port[""]="Pilih";

                $data_port=$this->stc->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();
                $data_dock="";
                $data_ship_pairing="";
            }
            else
            {
                $data_port=$this->stc->select_data("app.t_mtr_port"," where id=".$this->session->userdata("port_id"))->result();
                $data_dock=$this->stc->select_data("app.t_mtr_dock"," where status=1 and port_id=".$this->session->userdata("port_id")." order by name asc ")->result();
                $data_ship_pairing=$this->stc->get_ship_pairing(" where a.status=1 and a.port_id=".$this->session->userdata("port_id")." order by c.name asc ")->result();
            }

        }
        else
        {
            $data_port=$this->stc->select_data("app.t_mtr_port"," where id=".$identity_app->port_id)->result();
            $data_dock=$this->stc->select_data("app.t_mtr_dock"," where status=1 and port_id=".$identity_app->port_id." order by name asc")->result();
            $data_ship_pairing=$this->stc->get_ship_pairing(" where a.status=1 and a.port_id=".$identity_app->port_id." order by c.name asc")->result();
        }

        foreach ($data_port as $key => $value) {

            $port[$this->enc->encode($value->id)]=strtoupper($value->name);
        }

        if(!empty($data_dock))
        {
            foreach ($data_dock as $key => $value) {

                $dock[$this->enc->encode($value->id)]=strtoupper($value->name);
            }            
        }

        if(!empty($data_ship_pairing))
        {
            foreach ($data_ship_pairing as $key => $value) {

                $ship_pairing[$this->enc->encode($value->ship_id)]=strtoupper($value->ship_name);
            }            
        }        

        $data['title'] = 'Add Jadwal PIDS';
        $data['port'] = $port;
        $data['dock'] = $dock;
        $data['ship_pairing'] = $ship_pairing;

        $this->load->view($this->_module.'/add',$data);           

    }

    public function action_add()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $dock=trim($this->enc->decode($this->input->post('dock')));
        $port=trim($this->enc->decode($this->input->post('port')));
        $date=trim($this->input->post('date'));
        $ship_id=trim($this->enc->decode($this->input->post('ship_pairing')));

        // echo $ship_id; exit;

        $this->form_validation->set_rules('dock', 'Dermaga', 'required');
        $this->form_validation->set_rules('port', 'Pelabuhan', 'required');
        $this->form_validation->set_rules('date', 'Tanggal', 'required');
        $this->form_validation->set_rules('ship_pairing', 'Nama Kapal', 'required');


        $this->form_validation->set_message('required','%s harus diisi!');

        $pids_ptc_code=$this->createCode($port);

        // echo $pids_ptc_code; exit;

        $data_header=array('port_id'=>$port,
                    'dock_id'=>$dock,
                    'date'=>$date,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date('Y-m-d H:i:s'),
                    );

        $data_detail=array('ship_id'=>$ship_id,
                    'status'=>1,
                    'created_by'=>$this->session->userdata('username'),
                    'created_on'=>date('Y-m-d H:i:s'),
                    );        

        $data=array("data_header"=>$data_header,"data_detail"=>$data_detail);

        if($this->form_validation->run()===false)
        {
            echo $res=json_api(0, validation_errors());
        }
        else
        {
            $this->db->trans_begin();
            
            // checking data, apakah data headernya sudah ada pada tanggal, port dan dermaga yang sama
            $check_data=$this->stc->select_data(" app.t_trx_pids_ptc ", " where dock_id={$dock} and port_id={$port} and to_char(date,'yyyy-mm-dd')='{$date}' and status=1 ");

            // jika data header sudah ada di tanggal, pelabuhan dan dermaga yang sama , maka di create data detailnya saja
            if($check_data->num_rows()>0)
            {
                $data_detail['pids_ptc_code']=$check_data->row()->pids_ptc_code;
                $data_detail['order_data']=$this->stc->max_order_data($check_data->row()->pids_ptc_code);
                $this->stc->insert_data("app.t_trx_pids_ptc_detail",$data_detail);

                // print_r($data_detail); exit;
            }
            else
            {
                $data_header['pids_ptc_code']=$pids_ptc_code;
                $data_detail['pids_ptc_code']=$pids_ptc_code;
                $data_detail['order_data']=$this->stc->max_order_data($pids_ptc_code);

                // print_r($data_detail); exit;

                $this->stc->insert_data("app.t_trx_pids_ptc",$data_header);
                $this->stc->insert_data("app.t_trx_pids_ptc_detail",$data_detail);
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
        $logUrl      = site_url().'pids/pids_ptc/action_add';
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

        $get_detail=$this->stc->get_data_pids_ptc(" where b.id={$id_decode} ")->row();
        unset($get_detail->id);

        $port=array();
        $dock=array();
        $ship_pairing=array();
        $ship_change=array();

        $description=array(""=>"Pilih",$this->enc->encode(5)=>"ANCHOR",$this->enc->encode(3)=>"BROKEN",$this->enc->encode(2)=>"BERLAYAR",$this->enc->encode(4)=>"DOCKING");
        if(!empty($get_detail))
        {
            $data_port=$this->stc->select_data("app.t_mtr_port", " where id={$get_detail->port_id} ")->result();
            $data_dock=$this->stc->select_data("app.t_mtr_dock", " where id={$get_detail->dock_id} ")->result();
            $data_ship_pairing=$this->stc->get_ship_pairing(" where a.ship_id={$get_detail->ship_id} ")->result();
            $data_ship_change=$this->stc->get_ship_pairing(" where a.port_id={$get_detail->port_id} and a.status=1 and b.status=1 order by ship_name asc ")->result();

            foreach ($data_port as $key => $value) {
                $id_encode=$this->enc->encode($value->id);
                $port[$id_encode]=strtoupper($value->name);
            }

            foreach ($data_dock as $key => $value) {
                $id_encode=$this->enc->encode($value->id);
                $dock[$id_encode]=strtoupper($value->name);
            }

            foreach ($data_ship_pairing as $key => $value) {
                $id_encode=$this->enc->encode($value->ship_id);
                $ship_pairing[$id_encode]=strtoupper($value->ship_name);
            }

            $ship_change[""]="Pilih";
            foreach ($data_ship_change as $key => $value) {
                $id_encode=$this->enc->encode($value->ship_id);
                $ship_change[$id_encode]=strtoupper($value->ship_name);
            }

        }

        $data['title'] = 'Edit PIDS';
        $data['id'] = $id;
        $data['dock'] = $dock;
        $data['port'] = $port;
        $data['description']=$description;
        $data['ship_pairing'] = $ship_pairing;
        $data['ship_change'] = $ship_change;
        $data['detail'] = $get_detail;

        $this->load->view($this->_module.'/edit',$data);   
    }

    public function action_edit(){
        // validate_ajax();
        // $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $id=$this->enc->decode($this->input->post('id'));
        $ship_change=$this->enc->decode($this->input->post("ship_change"));
        $description=$this->enc->decode($this->input->post("description"));


        /* validation */
        $this->form_validation
        ->set_rules('id', 'ID PIdS', 'required')
        // ->set_rules('ship_change', 'Kapal Pengganti', 'required')
        ->set_rules('description', 'description', 'required');

        $this->form_validation->set_message('required','%s harus diisi!');

        $detail=$this->stc->select_data("app.t_trx_pids_ptc_detail"," where id={$id}")->row();

        /* data post */
        $update_data = array(
          'status'=>$description,
          'updated_by' =>$this->session->userdata("username"),
          'updated_on' =>date("Y-m-d H:i:s"),
        );

        $insert_data=array();

        if($this->form_validation->run() == FALSE){
          echo $res = json_api(0,validation_errors());
        }
        else
        {
            $this->db->trans_begin();

            if(!empty($ship_change))
            {
                $insert_data=array('ship_id'=>$ship_change,
                                    'pids_ptc_code'=>$detail->pids_ptc_code,
                                    'order_data'=>$detail->order_data,
                                    'status'=>1,
                                    'created_by'=>$this->session->userdata('username'),
                                    'created_on'=>date('Y-m-d H:i:s'),
                                    );        
                
                $this->stc->insert_data("app.t_trx_pids_ptc_detail",$insert_data);

                // menyisipkan update jika ada kapal pengganti
                $update_data['ship_backup_id'] = $ship_change;
            } 
            
            $this->stc->update_data("app.t_trx_pids_ptc_detail",$update_data,"id={$id}");            

            $data=array($update_data, $insert_data);

            // print_r($data); exit;
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
        $logUrl      = site_url().'pids/pids_ptc/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }
    function list_dock(){
        validate_ajax();
        echo $this->stc->get_list_dock();
    }

    public function get_data()
    {
        $port=$this->enc->decode($this->input->post("port"));

        $dock=array();
        $ship_pairing=array();

        if(!empty($port))
        {
            $data_dock=$this->stc->select_data("app.t_mtr_dock"," where status=1 and  port_id=".$port." order by name asc ")->result();
            $data_ship_pairing=$this->stc->get_ship_pairing(" where a.status=1 and a.port_id=".$port." order by c.name asc ")->result();

            if(!empty($data_dock))
            {
                foreach ($data_dock as $key => $value) {

                    $dock[] = array(
                        "id"=>$this->enc->encode($value->id),
                        "name"=>strtoupper($value->name)    
                    );
                }                
            }

            if(!empty($data_ship_pairing))
            {
                foreach ($data_ship_pairing as $key => $value) {

                    $ship_pairing[] = array(
                        "id"=>$this->enc->encode($value->ship_id),
                        "name"=>strtoupper($value->ship_name)    
                    );
                }                
            }            

        }

        $data=array(
            'dock'=>$dock,
            'ship_pairing'=>$ship_pairing,
            );

        echo json_encode($data);
    }

    function createCode($port)
    {
        $front_code="PS".$port.date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_trx_pids_ptc where left(pids_ptc_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $kode=$front_code."0001";
            return $kode;
        }
        else
        {
            $max=$this->db->query("select max (pids_ptc_code) as max_code from app.t_trx_pids_ptc where left(pids_ptc_code,".$total_length.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }    

}