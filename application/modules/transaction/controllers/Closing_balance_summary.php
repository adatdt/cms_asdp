<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Closing_balance_summary extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('m_closing_balance_summary','balance');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_trx_closing_balance';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/closing_balance_summary';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
            $rows = $this->balance->dataList();
            echo json_encode($rows);
            exit;
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Summary Tutup Dinas',
            'content'  => 'closing_balance_summary/index',
            'btn_add'  => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add')),
            'service'  => $this->balance->select_data("app.t_mtr_service","where status=1 order by name asc")->result(),
            'port'=>$this->balance->select_data("app.t_mtr_port","where status not in (-5) order by name asc")->result(),

        );

		$this->load->view('default', $data);
	}


    public function detail($ob_code)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module,'detail');

        $ob_code=$this->enc->decode($ob_code);

        $data['home']     = 'Home';
        $data['url_home'] = site_url('home');
        $data['title'] = 'Detail Tutup Dinas';
        $data['content']  = 'closing_balance_summary/detail_modal';
        $data['ob_code']  = $ob_code;
        $data['detail']   = $this->balance->get_detail(" where a.ob_code='".$ob_code."'")->result();
        $data['get_name']   = $this->balance->get_name(" where a.ob_code='".$ob_code."'")->row();
        $data['total_transaction']   = $this->balance->total_transaction("where ob_code='".$ob_code."'")->result();
        $data['port']=$this->balance->select_data("app.t_mtr_port","where status=1 order by name asc")->result();

        $this->load->view($this->_module.'/detail_modal',$data); 
    }

    public function listDetail(){   

        $booking_code=$this->enc->decode($this->input->post('id'));
        $search=trim($this->input->post('search'));

        $where=" where b.booking_code='".$booking_code."' and b.service_id=2 ";

        if(!empty($search))
        {
            $where .="and (b.ticket_number ilike '%".$search."%' and d.name ilike '%".$search."%')";
        }

        $rows = $this->boarding->listDetail($where)->result();

        $data=array();
        foreach ($rows as $key => $value) 
        {
            $value->booking_code=format_dateTime($value->booking_code);
            $value->id=$this->enc->encode($value->id);
            $data[]=$value;
        }

        echo json_encode($data);

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
