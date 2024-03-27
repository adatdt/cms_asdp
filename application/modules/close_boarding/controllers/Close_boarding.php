<?php
/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Close_boarding extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('global_model');
		$this->load->model('menu/menu_model');
		$this->load->model('close_boarding_model');
		$this->load->helper('nutech_helper');
        logged_in();
    }

    public function index()
    {
		 $this->check_access('open_boarding', 'view');
    	if ($this->input->server('REQUEST_METHOD') == 'POST')
    	{
    		$rows = $this->close_boarding_model->closeBoardingList();

            echo json_encode($rows);
            exit;
    	}

    	$data = array(
    		'home' => 'Beranda',
    		'url_home' => site_url(),
    		'title' => 'Close Boarding',
    		'content' => 'index',
			//'tab'=>'passanger'
    	);

    $this->load->view ('default', $data);
    }

    public function edit($id)
    {   

        $this->check_access('open_boarding', 'edit');

        $data = array(
        'home'        => 'Dashboard',
        'url_home'    => site_url('dashboard'),
        'parent1'     => 'Close Boarding',
        'url_parent1' => site_url('Update'),
        'title'       => 'Update Close Boarding',
        'content'     => 'edit',
        'dock'        =>$this->close_boarding_model->getboarding($id)->row($id),
       // 'menus'       => $this->menu_model->getMenu(),
        //'edit'        => $this->menu_model->getMenuById($id)
        );

        $this->load->view ('default', $data);
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

       $dock_id=$this->input->post('dock_id');
       $open_date=$this->input->post('open_date');

       $data=array(
                    'status'=>2,
                    'created_on'=>$open_date,

       );

       

       $insert=$this->db->insert('app.t_trx_boarding',$data);

        if ($insert) {      
          $this->session->set_flashdata('status', '<b> <i class="fa fa-check-circle"></i> Sukses! </b>');
          $this->session->set_flashdata('message', 'berhasil ditambah.');
        } 
        else {
          
          $this->session->set_flashdata('status', '<b><i class="fa fa-times-circle"></i> Gagal! </b>');
          $this->session->set_flashdata('message', 'gagal ditambah.');
          
        }
        redirect('open_boarding');
  
    }

}
