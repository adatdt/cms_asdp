<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Boarding_passanger extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_boarding_passanger','boarding');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_boarding';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/Boarding_passanger';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->boarding->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Penumpang Boarding',
            'content'  => 'boarding_passanger/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'service'  => $this->boarding->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'port'=>$this->boarding->select_data("app.t_mtr_port","where status=1 order by name asc")->result(),
            'team'=>$this->boarding->select_data("core.t_mtr_team","where status=1 order by team_name asc")->result(),
        );

		$this->load->view('default', $data);
	}


    public function detail($booking_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $id=$this->enc->decode($booking_code);

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title']    = 'detail booking';
        $data['title'] = 'Detail Booking';
        $data['content']  = 'booking/detail';
        $data['id']       = $booking_code;
        $data['port']=$this->booking->select_data("app.t_mtr_port","where status=1 order by name asc")->result();
        $data['booking']=$this->booking->select_data("$this->_table","where booking_code ='".$id."' ")->row();

        $this->load->view($this->_module.'/detail_modal',$data); 

        // $this->load->view('default',$data);   
    }

    public function listDetail(){   

        $booking_code=$this->enc->decode($this->input->post('id'));

        $rows = $this->booking->listDetail("where a.booking_code='".$booking_code."' ")->result();
        echo json_encode($rows);

    }

    public function listVehicle(){   

        $booking_code=$this->enc->decode($this->input->post('id'));

        $rows = $this->booking->listVehicle("where a.booking_code='".$booking_code."' ")->result();
        echo json_encode($rows);

    }

    function get_dock()
    {
        $port=$this->enc->decode($this->input->post('port'));

        empty($port)?$port_id='NULL':$port_id=$port;
        $dock=$this->dock->select_data($this->_table,"where port_id=".$port_id." and status=1")->result();

        $data=array();
        foreach($dock as $key=>$value)
        {
            $value->id=$this->enc->encode($value->id);
            $data[]=$value;            
        }

         echo json_encode($data);
    }

}
