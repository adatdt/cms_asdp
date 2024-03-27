<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Port extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('port_model');
        $this->load->model('global_model');

        $this->_table    = 'app.t_mtr_port';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'pelabuhan/port';
	}

    /*
    Document   : Pelabuhan
    Created on : 28 juli, 2023
    Author     : soma
    Description: Enhancement pasca angleb 2023
    */ 

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $this->validate_param_datatable($_POST,$this->_module);
            if($this->input->post('searchData')){
                $this->form_validation->set_rules('searchData', 'searchData', 'trim|callback_special_char', array('special_char' => 'search has contain invalid characters'));
            }
            
            if ($this->form_validation->run() == FALSE) 
            {
                echo $res = json_api(0, validation_errors(),[]);
                exit;
            }
            $rows = $this->port_model->portList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Pelabuhan',
            'content'  => 'port/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
        );

		$this->load->view('default', $data);
	}

    public function add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        $ifcs=array(NULL=>"Pilih",'t'=>"IYA",'f'=>"TIDAK");

        $data['title'] = 'Tambah Pelabuhan';
        $data['ifcs'] =$ifcs;
        $this->load->view($this->_module.'/add',$data);
    }

    public function action_add(){
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'add');

        /* validation */
        $this->form_validation->set_rules('name', 'Nama Pelabuhan', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'nama pelabuhan mengandung invalid karakter'));
        $this->form_validation->set_rules('weight_limit', 'MAX Berat', 'trim|required|numeric');
        $this->form_validation->set_rules('city', 'Nama Kota', 'trim|required|max_length[50]|callback_special_char', array('special_char' => 'nama kota mengandung invalid karakter'));
        $this->form_validation->set_rules('cross_class', 'Lintas Kelas', 'trim|required');
        $this->form_validation->set_rules('ifcs', 'IFCS', 'trim|required');
        $this->form_validation->set_rules('timeZone', 'Zona Waktu', 'trim|required');
        $this->form_validation->set_rules('ordering', 'Urutan', 'trim|required');
        $this->form_validation->set_rules('profit_center', 'Kode Profit Center', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('username_siwasops', 'Username Siwasops', 'trim|required');
        $this->form_validation->set_rules('password_siwasops', 'Password Siwasops', 'trim|required');
        $this->form_validation->set_rules('url_login_siwasops', 'URL Login Siwasops', 'trim|required');
        $this->form_validation->set_rules('url_siwasops', 'URL Siwasops', 'trim|required');


        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('numeric', '%s harus angka!')
                            ->set_message('max_length','{field} Maximal  {param} karakter! ');
        /* data post */



        
        $data = null;
        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else
        {
            $post = $this->input->post();
            $name = strtoupper(trim($post['name']));
            $city = strtoupper(trim($post['city']));
            $cross_class = $post['cross_class'];
            $ifcs = $post['ifcs'];
            $weight_limit = trim($post['weight_limit']);
            $profit_center = strtoupper(trim($post['profit_center']));
            $timeZone=strtoupper(trim($this->input->post('timeZone')));
            $ordering = $post['ordering'];
            $username_siwasops=htmlspecialchars_decode(base64_decode($post['username_siwasops']));
            $password_siwasops=htmlspecialchars_decode(base64_decode($post['password_siwasops']));
            $url_login_siwasops=htmlspecialchars_decode(base64_decode($post['url_login_siwasops']));
            $url_siwasops=htmlspecialchars_decode(base64_decode($post['url_siwasops']));


            // $port_id_bmkg = trim($this->input->post("port_id_bmkg"));

            // check timezone
            $checkTimeZone[]=0;
            if($timeZone =='WIB')
            {
                $checkTimeZone[]=1;
                $differentTime=0;
            }
            else if ($timeZone =='WIT') {
                $checkTimeZone[]=1;
                $differentTime=2;
            }
            else if($timeZone=='WITA')
            {
                $checkTimeZone[]=1;
                $differentTime=1;
            }
            else
            {
                $checkTimeZone[]=0;
                $differentTime="";
            }


            $data = array(
                'name' => $name,
                'city' => $city,
                'ifcs' => $ifcs,
                'weight_limit' => $weight_limit,
                'cross_class' => $cross_class,
                'profit_center' => $profit_center,
                'time_zone'=>$timeZone,
                'order'=>$ordering,
                'different_time'=>$differentTime,
                'username_siwasops'=>$username_siwasops,
                'password_siwasops'=>$password_siwasops,
                'url_login_siwasops'=>$url_login_siwasops,
                'url_siwasops'=>$url_siwasops,
                // 'port_id_bmkg'=>$port_id_bmkg,
                'port_code'=>$this->createCode(),
                'created_on'=>date("Y-m-d H:i:s"),
                'created_by'=>$this->session->userdata('username'),
            );


            // $check = $this->global_model->checkData($this->_table,array('UPPER(name)' => $name));

            $check = $this->port_model->select_data($this->_table," where UPPER(name)='{$name}' and status not in (-5) ");

            $checkOrdering = $this->port_model->select_data($this->_table,' where "order"='."'{$ordering}'".' and status not in (-5) ');

            if($check->num_rows()>0)
            {
                echo $res =  json_api(0,'Nama Pelabuhan '.$name.' Sudah Ada'); 
            }
            else if(array_sum($checkTimeZone)<1)
            {
                echo $res =  json_api(0,'Time Zone tidak terdaftar');   
            }
            else if(!is_numeric($ordering))
            {
                echo $res =  json_api(0,'Urutan Harus angka');
            }
            else if($checkOrdering->num_rows()>0)
            {
                echo $res =  json_api(0,'Urutan Sudah ada');   
            }
            else
            {
                $this->db->trans_begin();

                $this->port_model->insert_data($this->_table,$data);

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
        }


         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/port/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function edit($param){
        validate_ajax();
        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        $decode_id=$this->enc->decode($param);

        $ifcs=array(NULL=>"Pilih",'t'=>"IYA",'f'=>"TIDAK");
        $checkData=$this->port_model->select_data($this->_table," where id='{$decode_id}' ")->row();
        $ifcs_selected=$checkData->ifcs;

        $timeZone=array(NULL=>"Pilih",'WIB'=>"WIB",'WIT'=>"WIT",'WITA'=>"WITA");
        $timeZone_selected=$checkData->time_zone;

        $data['id']    = $param;
        $data['ifcs'] =$ifcs;
        $data['ifcs_selected'] =$ifcs_selected;
        $data['timeZone'] =$timeZone;
        $data['timeZone_selected'] =$timeZone_selected;
        $data['row']   = $this->global_model->selectById($this->_table, 'id', $this->enc->decode($param));
        $data['title'] = 'Edit Pelabuhan';
        $this->load->view($this->_module.'/edit',$data);
    }

    public function action_edit(){
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'edit');

        /* validation */
        $this->form_validation
        ->set_rules('id', 'ID Pelabuhan', 'trim|required')
        ->set_rules('name', 'Nama Pelabuhan', 'trim|required|max_length[100]|callback_special_char', array('special_char' => 'nama pelabuhan mengandung invalid karakter'))
        ->set_rules('city', 'Nama Kota', 'trim|required|max_length[50]|callback_special_char', array('special_char' => 'nama kota mengandung invalid karakter'))
        ->set_rules('weight_limit', 'MAX Berat', 'trim|required|numeric')
        ->set_rules('cross_class', 'Lintas Kelas', 'trim|required')
        ->set_rules('ifcs', 'IFCS', 'trim|required')
        ->set_rules('profit_center', 'Kode Profit Center', 'trim|required|max_length[50]')
        ->set_rules('ordering', 'Urutan', 'trim|required')
        ->set_rules('timeZone', 'Zona Waktu', 'trim|required')
        ->set_rules('username_siwasops', 'Username Siwasops', 'trim|required')
        ->set_rules('password_siwasops', 'Password Siwasops', 'trim|required')
        ->set_rules('url_login_siwasops', 'URL Login Siwasops', 'trim|required')
        ->set_rules('url_siwasops', 'URL Siwasops', 'trim|required');

        $this->form_validation->set_message('required','%s harus diisi!')
                            ->set_message('numeric', '%s harus angka!')
                            ->set_message('max_length','{field} Maximal  {param} karakter! ');

        
        $data = null;
        if($this->form_validation->run() == FALSE){
            echo $res = json_api(0,validation_errors());
        }
        else
        {
            $post = $this->input->post();
            $name = strtoupper(trim($post['name']));
            $city = strtoupper(trim($post['city']));
            $cross_class=$post['cross_class'];
            $ifcs=$post['ifcs'];
            $ordering=$post['ordering'];
            $username_siwasops=htmlspecialchars_decode(base64_decode($post['username_siwasops']));
            $password_siwasops=htmlspecialchars_decode(base64_decode($post['password_siwasops']));
            $url_login_siwasops=htmlspecialchars_decode(base64_decode($post['url_login_siwasops']));
            $url_siwasops=htmlspecialchars_decode(base64_decode($post['url_siwasops']));
            $weight_limit=trim($post['weight_limit']);
            $profit_center = strtoupper(trim($post['profit_center']));
            $timeZone=strtoupper(trim($this->input->post('timeZone')));
            // $port_id_bmkg = trim($this->input->post("port_id_bmkg"));

            $id = $this->enc->decode($post['id']);


            // $check = $this->global_model->checkData(
            //     $this->_table,
            //     array('UPPER(name)' => $name),
            //     'id',$id
            // );

            // check timezone
            $checkTimeZone[]=0;
            if($timeZone =='WIB')
            {
                $checkTimeZone[]=1;
                $differentTime=0;
            }
            else if ($timeZone =='WIT') {
                $checkTimeZone[]=1;
                $differentTime=2;
            }
            else if($timeZone=='WITA')
            {
                $checkTimeZone[]=1;
                $differentTime=1;
            }
            else
            {
                $checkTimeZone[]=0;
            }        

            /* data post */
            $data = array(
                'id' => $id,
                'name' => $name,
                'cross_class' =>$cross_class,
                'weight_limit' =>$weight_limit,
                'ifcs' =>$ifcs,
                'city' => $city,
                'profit_center' => $profit_center,
                'time_zone'=>$timeZone,
                'order'=>$ordering,
                'username_siwasops'=>$username_siwasops,
                'password_siwasops'=>$password_siwasops,
                'url_login_siwasops'=>$url_login_siwasops,
                'url_siwasops'=>$url_siwasops,
                // 'port_id_bmkg'=>$port_id_bmkg,
                'different_time'=>$differentTime,
                'updated_on'=>date('Y-m-d H:i:s'),
                'updated_by'=>$this->session->userdata('username'),
            );        

            $check = $this->port_model->select_data($this->_table," where UPPER(name)='{$name}' and status not in (-5) and id !={$id} ");
            $checkOrdering = $this->port_model->select_data($this->_table,' where "order"='."'{$ordering}'".' and status not in (-5) and id<>'.$id);

            if($check->num_rows()>0){
                echo $res =  json_api(0,'Nama Pelabuhan '.$post['name'].' Sudah Ada'); 
            }
            else if(array_sum($checkTimeZone)<1)
            {
                echo $res =  json_api(0,'Time Zone tidak terdaftar');   
            }
            else if(!is_numeric($ordering))
            {
                echo $res =  json_api(0,'Urutan Harus angka');
            }
            else if($checkOrdering->num_rows()>0)
            {
                echo $res =  json_api(0,'Urutan Sudah ada');   
            }                
            else
            {
    
                $this->db->trans_begin();
    
                $this->port_model->update_data($this->_table,$data,"id=$id");
    
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
        $logUrl      = site_url().'pelabuhan/port/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_delete($param){
        validate_ajax();

        // cek aksees
        $this->global_model->checkAccessMenuAction($this->_module,'delete');

        $id   = $this->enc->decode($param);

        $data = [];
        if(!$id || empty($id)){
            echo $res=json_api(0, 'Gagal delete data');            
        }
        else{

            /* data */
            $data = array(
                'id' => $id,
                'status' => -5,
                'updated_by'=>$this->session->userdata('username'),
                'updated_on'=>date("Y-m-d H:i:s"),
            );

            // check apakah masih terpairing di dermaga meskipun status non aktif (-1) 
            $check_dermaga=$this->port_model->select_data("app.t_mtr_dock", " where port_id={$id} and status not in (-5) ");

            // check apakah datanya port terpairingh dengan route
            $check_route=$this->port_model->select_data("app.t_mtr_rute", " where status not in (-5) and (origin={$id} or destination={$id} ) ");


            // if ($check_dermaga->num_rows()>0)
            // {
            //     echo $res=json_api(0, 'Gagal delete, data masih terhubung dengan dermaga');
            // }
            // else if ($check_route->num_rows()>0)
            // {
            //     echo $res=json_api(0, 'Gagal delete, data masih terhubung dengan rute');
            // }
            // else
            // {
            //     if ($this->db->trans_status() === FALSE)
            //     {
            //         $this->db->trans_rollback();
            //         echo $res=json_api(0, 'Gagal delete data');
            //     }
            //     else
            //     {
            //         $this->db->trans_commit();
            //         echo $res=json_api(1, 'Berhasil delete data');
            //     }   
            // }

            $this->db->trans_begin();
            $this->port_model->update_data($this->_table,$data,"id=$id");

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
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('username');
        $logUrl      = site_url().'pelabuhan/port/action_delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
    }

    public function action_change($param){
        validate_ajax();
        $p = $this->enc->decode($param);
        $data = [];

        if(!$p || empty($p)){
            echo $res=json_api(0, 'Gagal update data');
            
        }
        else{
            $d = explode('|', $p);

            /* data */
            $data = array(
                'id' => $d[0],
                'status' => $d[1]
            );

            $query = $this->global_model->updateData($this->_table, $data, 'id');
            if($query){
                $response = json_api(1,'Update Status Berhasil');
            }else{
                $response = json_encode($this->db->error()); 
            }

            $this->log_activitytxt->createLog($this->_username, uri_string(), 'change_status', json_encode($data), $response); 
            echo $response;
        }
    }

    function createCode()
    {

        $max=$this->db->query("select max (port_code) as max_code from app.t_mtr_port")->row();
        $kode=$max->max_code;
        $noUrut = (int) substr($kode, 0, 3);
        $noUrut++;
        $kode = sprintf("%03s", $noUrut);
        return $kode;
    }

    /*
    Document   : Pelabuhan
    Created on : 28 juli, 2023
    Author     : soma
    Description: end Enhancement pasca angleb 2023
    */ 
}
