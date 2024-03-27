<?php
/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Sandar extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('sandar_model');
        $this->_module   = 'sandar';
    }

    public function index(){
		checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
    		$rows = $this->sandar_model->sandarList();
            echo json_encode($rows);
            exit;
    	}

    	$data = array(
            'home'        => 'Home',
            'url_home'    => site_url('home'),
            'parent'      => 'Booking Management',
            'url_parent'  => '#',
    		'title'       => 'Kapal Sandar',
    		'content'     => 'index',
    	);

    	$this->load->view ('default', $data);
    }

    public function add(){   
        checkUrlAccess($this->_module,'add');
        $data = array(
            'home'        => 'Beranda',
            'url_home'    => site_url('home'),
            'parent1'     => 'Kapal Sandar',
            'url_parent1' => site_url('sandar'),
            'title'       => 'Tambah Data',
            'content'     => 'add',
            'ship' => $this->sandar_model->selectData('app.t_mtr_ship','name','asc')->result(),
            'dermaga' => $this->sandar_model->selectData('app.t_mtr_dock','name','asc')->result(),
            'port' => $this->db->query("select * from app.t_mtr_port where id=2")->row());

        $this->load->view('default', $data);
    }

/*	
	public function gateInVehicleList()
	{
		 if ($this->input->is_ajax_request())
    	{
    		$rows = $this->gatein_model->gateInVehicleList();

            echo json_encode($rows);
            exit;
    	}
	}
    */
    public function save()
    {

       $ship_id=$this->input->post('ship');
       $port_id=$this->input->post('port');
       $dermaga_id=$this->input->post('dermaga');
       $id=$this->sandar_model->getMaxId('app.t_trx_dock','id');


       $data=array(
                    'id'=>$id->max_id+1,
                    'ship_id'=>$ship_id,
                    'port_id'=>$port_id,
                    'dock_id'=>$dermaga_id,

       );

       $insert=$this->db->insert('app.t_trx_dock',$data);

        if ($insert) {
            $date=date('Y-m-d H:i:s');
            $status=1;   

            //$this->sandar_model->update($ship_id,$status,$date,$dermaga_id,$port_id);

            $this->db->query(" update app.t_tmp_passanger_info set ship_id=$ship_id, status=1 ,total_passanger=0, total_vehicle=0 ,date='".date('Y-m-d H:i:s')."' where dock_id=$dermaga_id ");

          $this->session->set_flashdata('status', '<b> <i class="fa fa-check-circle"></i> Sukses! </b>');
          $this->session->set_flashdata('message', 'berhasil ditambah.');
        } 
        else {
          
          $this->session->set_flashdata('status', '<b><i class="fa fa-times-circle"></i> Gagal! </b>');
          $this->session->set_flashdata('message', 'gagal ditambah.');
          
        }
        redirect('sandar');

    }


}
