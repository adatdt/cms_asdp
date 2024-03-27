<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Port extends MY_Controller
{

	public function __construct()
    {
		parent::__construct();

        logged_in();
        $this->load->model('global_model', 'global');
        $this->load->model('port_model');
	}

	public function index()
    {   $this->check_access('port', 'view');
        if ( $this->input->is_ajax_request() ) {
            $rows = $this->port_model->portList();

            echo json_encode($rows);
            exit;
        }

        $data = array(
        'home'          => 'Home',
        'url_home'      => site_url('home'),
        'title'         => 'Port',
        'content'       => 'index',
        );

		$this->load->view('default', $data);
	}

    public function add()
    {   $this->check_access('port', 'add');
        $data = array(
        'home'        => 'Home',
        'url_home'    => site_url('home'),
        'parent1'     => 'Port',
        'url_parent1' => site_url('port'),
        'title'       => 'Add port',
        'content'     => 'add',
		'province'	 =>$this->get_prov()	
        );

		$this->load->view('default', $data);
	}

    public function save()
    {   $this->check_access('port', 'add');
        
        $this->form_validation->set_rules('port_name', 'Port Name', 'required|max_length[100]');
        $this->form_validation->set_rules('prov', 'Port Province', 'required');
		$this->form_validation->set_rules('city', 'Port City', 'required');
		$this->form_validation->set_rules('district', 'Port District', 'required');
		//$this->form_validation->set_rules('port_city', 'Port City', 'required');
		
        $port_code = $this->generete_no();
        $port_name = $this->security->xss_clean($this->input->post('port_name')); 
        //$port_city = $this->input->post('port_city');
		$province_id=$this->input->post('prov');
		$city_id=$this->input->post('city'); 
		$district_id=$this->input->post('district');
		
        $res           = "failed";
        $data          = array(
                'port_code'   => $port_code,
                'port_name'   => strtoupper($port_name),
                //'port_city'   => strtoupper("data_dummy"),
				'province_id' => $province_id,
				'city_id'	=> $city_id,
				'district_id'=>$district_id,
                'created_by'      => $this->session->userdata('username'),
               
            );

        if ($this->form_validation->run() == FALSE){
            $this->session->set_flashdata('status', '<b><i class="fa fa-times-circle"></i> Failed! </b>');
                  
        }
        else if($this->port_model->get_byCode($port_code)){
            $this->session->set_flashdata('status', '<b><i class="fa fa-times-circle"></i> Failed! </b>');
           
        }
        else{
            
            $insert=$this->global_model->insert('t_mtr_port',$data);
            if ($insert){
                $this->session->set_flashdata('status', '<b> <i class="fa fa-check-circle"></i> Success! </b>');
                 $this->session->set_flashdata('message', 'Data Saved.');
                 $res = "success";
            }
            else{
                $this->session->set_flashdata('status', '<b><i class="fa fa-times-circle"></i> Failed! </b>');
                $this->session->set_flashdata('message', 'Data Failed.');
            }
           
        }

        $created_by   = $this->session->userdata('username');
        $log_url      = site_url().'port';
        $log_method   = 'insert';
        $log_param    = json_encode($data);
        $log_response = json_encode($res);    
        $this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
        redirect('port');
        
    }

    public function edit($enc_id)
    {   $this->check_access('port', 'edit');
        $id = $this->enc->decode($enc_id);
		// ambil data city id dan district id
		$dataid=$this->db->query("select * from t_mtr_port where id=$id")->row();
		
		$province_id=$dataid->province_id;
		$city_id=$dataid->city_id;
		
	
        $data = array(
            'home'          => 'Home',
            'url_home'      => site_url('home'),
            'parent1'       => 'port',
            'url_parent1'   => site_url('port'),
            'title'         => 'Edit Port',
            'content'       => 'edit',
            'details'       => $this->port_model->get_by(array('id' => $id)),
			'data_province' => $this->get_prov2(),
			'data_city' =>$this->port_model->get_area($province_id),
			'data_district'=>$this->port_model->get_district($city_id)
        );

		$this->load->view('default', $data);
	}

    public function update()
    {   $this->check_access('port', 'edit');
        $this->form_validation->set_rules('id', 'Group', 'required');
        $this->form_validation->set_rules('port_name', 'port name', 'required|max_length[100]');
		$this->form_validation->set_rules('prov'.'Province','required');
		$this->form_validation->set_rules('city'.'Province','required');
		$this->form_validation->set_rules('district'.'Province','required');
		
		$province_id=$this->input->post("prov");
		$city_id=$this->input->post("city");
		$district_id=$this->input->post("district");
		
        //$this->form_validation->set_rules('port_city', 'City', 'required|max_length[100]');
        $enc_id         = $this->input->post('id');
        $id             = $this->enc->decode($enc_id);
        $port_name = $this->security->xss_clean($this->input->post('port_name'));
        $port_code = $this->security->xss_clean($this->input->post('port_code'));
        $port_city = $this->input->post('port_city');
        $res = 'failed';
        $data = array(
                'id'  => $id,
                'port_name'   => strtoupper($port_name),
                //'port_city'   => strtoupper($port_city),
				'city_id'=>$city_id,
				'province_id'=>$province_id,
				'district_id'=>$district_id,
                'updated_by' => $this->session->userdata('username'),
                'updated_on' => date('Y-m-d H:i:s')
                
            );
        if ($this->form_validation->run() == FALSE){
            $this->session->set_flashdata('status', '<b><i class="fa fa-times-circle"></i> Faailed! </b>');
           }
        else{
            $update=$this->port_model->update($id, $data);
            
            if ($update){
                $this->session->set_flashdata('status', '<b> <i class="fa fa-check-circle"></i> success! </b>');
                 $this->session->set_flashdata('message', 'Update Success.');
                 $res = "success";
            }
            else{
                $this->session->set_flashdata('status', '<b><i class="fa fa-times-circle"></i> Failed! </b>');
                $this->session->set_flashdata('message', 'Update Failed.');
            }
           
        }

        $created_by   = $this->session->userdata('username');
        $log_url      = site_url().'port';
        $log_method   = 'update';
        $log_param    = json_encode($data);
        $log_response = json_encode($res);    
        $this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
        redirect('port');
    }

    public function delete($enc_id)
    {   
        $this->check_access('port', 'delete');
        $id = $this->enc->decode($enc_id);   
		
		// mengubah status jadi 0          
       // $delete=$this->global->delete2('t_mtr_port',$id);
	   
	   // menghapus data full
	   $delete=$this->port_model->delete($id);
	   
        $res = 'failed';
        if ($delete){
                $this->session->set_flashdata('status', '<b> <i class="fa fa-check-circle"></i> Sukses! </b>');
                 $this->session->set_flashdata('message', 'Delete Success.');
                 $res = 'success';
            }
            else{
                $this->session->set_flashdata('status', '<b><i class="fa fa-times-circle"></i> Gagal! </b>');
                $this->session->set_flashdata('message', 'Delete failed.');
            }
           
        $created_by   = $this->session->userdata('username');
        $log_url      = site_url().'port';
        $log_method   = 'delete';
        $log_param    = json_encode($id);
        $log_response = json_encode($res);    
        $this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
        redirect('port');
    }

    public function unique_code() 
    {
        $code = strtoupper($this->input->post('port_code'));
        $data = $this->port_model->get_byCode($code);
        if ($data) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    public function generete_no() {
 
          $string   = '';
          $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
          for ($a = 0; $a < 5; $a++) {
            $pos = rand(0, strlen($karakter)-1);
            $string .= $karakter{$pos};
          }
          $year = substr(date('Y'), 2, 3);
          $month = date('m');       
          $generate_no = $string.''.$month.''.$year.'';
                 
          return $generate_no;
	}
	
	public function get_prov()
	{
		$data = $this->port_model->get_prov();
		$result['']  = '';
		
		if($data)
		{
			foreach($data as $row)
			{
				$result[$row->id]=$row->name;
				
			}
		}
		
		return $result;
		
		/*if($data){
			foreach($data as $key => $value){
				$arr[$key]['id'] = $value->id;
				$arr[$key]['text'] = $value->name;
			}
		}
		
		$result['results'] = $arr;
		$result['paginate'] = array('more' => true);
		echo json_encode($result); */
	}
	
	public function get_prov2()
	{
		return $data= $this->port_model->get_prov();
	}
	
	public function get_area()
	{
		$province_id=$this->input->post("province_id");
		$data=$this->port_model->get_area($province_id);
		echo json_encode($data);
	}
	
	public function get_district()
	{
		$district_id=$this->input->post("district_id");
		$data=$this->port_model->get_district($district_id);
		echo json_encode($data);
	}

}
