<?php
/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Sail extends MY_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('global_model');
	//	$this->load->model('menu/menu_model');
		$this->load->model('sail_model');
		$this->load->helper('nutech_helper');
        logged_in();
    }

    public function index()
    {
		 $this->check_access('sail', 'view');
    	if ($this->input->server('REQUEST_METHOD') == 'POST')
    	{
    		$rows = $this->sail_model->sailList();

            echo json_encode($rows);
            exit;
    	}

    	$data = array(
    		'home' => 'Dashboard',
    		'url_home' => site_url(),
    		'title' => 'Approve',
    		'content' => 'index',
    	);

    	$this->load->view ('default', $data);
    }
	
	 public function detail()
    {
		 $this->check_access('sail', 'detail');
	$departure= $this->uri->segment(3);
	$depart_date =  $this->uri->segment(4);
	$ship_id =  $this->uri->segment(5);

	$x=$this->db->query("
	 	select b.departure , a.* from app.t_trx_sail a
			left join app.t_mtr_schedule_time b on a.schedule_time_id=b.id
			where departure='$departure' and to_char(depart_date,'yyyy-mm-dd')='$depart_date' and ship_id=$ship_id
	 ")->num_rows();
	
	if($x<1)
	{
		$button="<button class='btn btn-warning' type='submit'>Approve</button>";
	}
	else
	{
		$button="<a class='label label-info'>sudah approve</a>";
	}

		 $this->check_access('sail', 'detail'); 
		 
		  $data  = array(
			'home'        => 'Home',
			'url_home'    => site_url('home'),
			'parent1'     => 'Sail',
			'url_parent1' => site_url('sail'),
			'title'       => 'Detail jadwal',
			'content'     => 'detail',
			'tab'     => 'passanger',
			'passanger'  => $this->sail_model->getPassanger($departure, $depart_date, $ship_id)->result(),
			'vehicle'  => $this->sail_model->getVehicle($departure, $depart_date, $ship_id)->result(),
			'vehicle2'  => $this->sail_model->getVehicle2($departure, $depart_date, $ship_id)->result(),
			'approve'	=>$button,
			'departure1'=>$departure,
			'depart_date1'=>$depart_date,
			'ship_id1'=>$ship_id,
		  );	
		  $this->load->view('default', $data); 
    }
	
	
	function approve()
	{
		$schedule_time_id=$this->input->post('schedule_time_id');
		$ship_id=$this->input->post('ship_id');
		$depart_date=$this->input->post('depart_date');
		$departure=$this->input->post('departure');
		
		
		$maxsail=$this->db->query("select max(id) as maxid from app.t_trx_sail ")->row_array();
		$maxsailid=$maxsail['maxid']+1;
		
		$datasail=array(
			'id'=>$maxsailid,
			'schedule_time_id'=>$schedule_time_id,
			'created_by'=>$this->session->userdata('id'),
			'status'=>1,
			'updated_by'=>0,
			'ship_id'=>$ship_id,
			'depart_date'=>$depart_date
		);
		
		$this->db->insert("app.t_trx_sail",$datasail);
		
		$booking_passanger_id=$this->input->post('booking_passanger_id[]');
		$booking_passanger_id2=$this->input->post('booking_passanger_id2[]');
		$booking_vehicle_id=$this->input->post('booking_vehicle_id[]');
		
		
		$maxid_pass=$this->db->query("select max(id)as maxid from app.t_trx_sail_passanger")->row_array();
		$max_id_pass=$maxid_pass['maxid']+1;

		
		$maxid_pass2=$this->db->query("select max(id)as maxid from app.t_trx_sail_vehicle")->row_array();
		$max_id_pass2=$maxid_pass2['maxid']+1;

		// validasiin
		if(!empty($booking_passanger_id))
		{
		
			foreach ($booking_passanger_id as $booking_passanger_id)
			{
				$insert[]=array('id'=>$max_id_pass,
								'booking_passanger_id'=>$booking_passanger_id,
								'status'=>1,
								'created_by'=>$this->session->userdata('id'),
								'updated_by'=>0,
								'sail_id'=>$maxsailid,
								);
				$max_id_pass+=1;
			}
			
			$datapassanger2=$this->sail_model->getPassanger($departure, $depart_date, $ship_id)->result();
			
			foreach($datapassanger2 as $datapassanger2 )
			{
				$wherepassanger=array('ticket_number'=>$datapassanger2->ticket_number);
				$update_booking_passanger=array('status'=>4,
												'updated_on'=>date('Y-m-d H:i:s'),
												'updated_by'=>$this->session->userdata('id'));
				$this->sail_model->update($wherepassanger, 'app.t_trx_booking_passanger', $update_booking_passanger);
			}
			
			$this->db->insert_batch("app.t_trx_sail_passanger",$insert);
		}
		
		$maxid_pass3=$this->db->query("select max(id)as maxid from app.t_trx_sail_passanger")->row_array();
		$max_id_pass3=$maxid_pass3['maxid']+1;

		// validasiin jika pasanger penumpang ada 
		if(!empty($booking_passanger_id2))
		{
		
			foreach ($booking_passanger_id2 as $booking_passanger_id2)
			{
				$insert2[]=array('id'=>$max_id_pass3,
								'booking_passanger_id'=>$booking_passanger_id2,
								'status'=>1,
								'created_by'=>$this->session->userdata('id'),
								'updated_by'=>0,
								'sail_id'=>$maxsailid,
								);
				$max_id_pass3 +=1;
			}
			
			$datapassanger3=$this->sail_model->getVehicle($departure, $depart_date, $ship_id)->result();
			
			foreach($datapassanger3 as $datapassanger3 )
			{
				$wherepassanger=array('id'=>$datapassanger3->booking_passanger_id);
				$update_booking_passanger=array('status'=>4,
												'updated_on'=>date('Y-m-d H:i:s'),
												  'updated_by'=>$this->session->userdata('id')
												);
				$this->sail_model->update($wherepassanger, 'app.t_trx_booking_passanger', $update_booking_passanger);
			}
			
			$this->db->insert_batch("app.t_trx_sail_passanger",$insert2);
		}

		
		if (!empty($booking_vehicle_id))
		{
		
			foreach ($booking_vehicle_id as $booking_vehicle_id)
			{
				$insert3[]=array('id'=>$max_id_pass2,
								'booking_vehicle_id'=>$booking_vehicle_id,
								'status'=>1,
								'created_by'=>$this->session->userdata('id'),
								'updated_by'=>0,
								'sail_id'=>$maxsailid,
								);
				$max_id_pass2 +=1;
			}
			
				$datavehicle2=$this->sail_model->getVehicle2($departure, $depart_date, $ship_id)->result();
				
				foreach($datavehicle2 as $datavehicle2 )
				{
					$wherevehicle=array('ticket_number'=>$datavehicle2->ticket_number);
					$update_booking_vehicle=array('status'=>4,
												  'updated_on'=>date('Y-m-d H:i:s'),
												  'updated_by'=>$this->session->userdata('id')
					);
					$this->sail_model->update($wherevehicle, 'app.t_trx_booking_vehicle', $update_booking_vehicle);
				}
			
			$this->db->insert_batch("app.t_trx_sail_vehicle",$insert3);
		}
		
		
		$this->session->set_flashdata('status', '<b> <i class="fa fa-check-circle"></i> Sukses! </b>');
        $this->session->set_flashdata('message', 'Data Sudah Di Approve.');
		
		redirect('sail');
		
		
	}

}
