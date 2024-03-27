<?php
/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Open_boarding extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('open_boarding_model');
    $this->_module = 'open_boarding';
  }

  public function index(){
		checkUrlAccess(uri_string(),'view');
    if($this->input->is_ajax_request()){
      $rows = $this->open_boarding_model->sandarList();
      echo json_encode($rows);
      exit;
    }

    $data = array(
    	'home'       => 'Home',
      'url_home'   => site_url('home'),
      'parent'     => 'Booking Management',
      'url_parent' => '#',
    	'title'      => 'Open Boarding',
    	'content'    => 'index',
    );

    $this->load->view ('default', $data);
  }

  public function detail($id=''){
    checkUrlAccess($this->_module,'detail');

      $decode_id=$this->enc->decode($id);
        $passanger=$this->db->query("select b.* from app.t_trx_boarding_detail a
            join app.t_trx_booking c on a.booking_id=c.id and c.status = 4
            join app.t_trx_booking_passanger b on a.ticket_number=b.ticket_number where a.boarding_id=$decode_id and a.status=1 ")->result();
        
        $passangervehicle=$this->db->query("select e.name as vehicle_name, d.id_number as no_pol, a.ticket_number as ticket, a.boarding_id, c.* from app.t_trx_boarding_detail a
            join app.t_trx_booking b on a.booking_id=b.id and b.status = 4
            join app.t_trx_booking_passanger c on b.id=c.booking_id
            join app.t_trx_booking_vehicle d on a.ticket_number=d.ticket_number
            left join app.t_mtr_vehicle_class  e on d.vehicle_class_id=e.id 
           where a.boarding_id=$decode_id and a.status=1 " )->result();
        
        $vehicle=$this->db->query("select e.name as vehicle_name, a.ticket_number as ticket, a.boarding_id, d.* from app.t_trx_boarding_detail a
          join app.t_trx_booking b on a.booking_id=b.id and b.status = 4
          join app.t_trx_booking_vehicle d on a.ticket_number=d.ticket_number
          join app.t_mtr_vehicle_class e on d.vehicle_class_id=e.id
          where a.boarding_id=$decode_id and a.status=1 ")->result();

        if($this->db->_error_message())
        {
          redirect('error_401');
          exit;
        }

        $data = array(
          'home'       => 'Home',
          'url_home'   => site_url('home'),
          'parent'     => 'Booking Management',
          'url_parent' => '#',
          'parent1'     => 'Open Boarding',
          'url_parent1' => site_url('detail'),
          'title'       => 'Detail',
          'content'     => 'detail',
          'tab'         =>'passanger',
          'manifestpassanger' =>$passanger,
          'passangervehicle'=>$passangervehicle,
          'vehicle'   =>$vehicle,
        );

        $this->load->view ('default', $data);
    }

    public function edit($id)
    {   

        $this->check_access('open_boarding', 'edit');

        $data = array(
        'home'        => 'Dashboard',
        'url_home'    => site_url('dashboard'),
        'parent1'     => 'Open Boarding',
        'url_parent1' => site_url('Update'),
        'title'       => 'Update Open Boarding',
        'content'     => 'edit',
        'dock'        =>$this->open_boarding_model->getdock($id)->row($id),
       // 'menus'       => $this->menu_model->getMenu(),
        //'edit'        => $this->menu_model->getMenuById($id)
        );

        $this->load->view ('default', $data);
    }

    public function save($id)
    {

       $dock_id=$this->input->post('dock_id');
       $open_date=$this->input->post('open_date');

       $data=array(
                    'dock_id'=>$id,
                    'status'=>1,
                    'created_on'=>date('Y-m-d H:i:s'),
                    'created_by'=>$this->session->userdata('id'),
                   );

       // $insert=$this->db->insert('app.t_trx_boarding',$data);
       $this->db->trans_begin();
       $this->open_boarding_model->insert_data('app.t_trx_boarding',$data);

      $selectdata=$this->db->query("select * from app.t_trx_dock where id=$id")->row();

          $status=2;
          $dock_id=$selectdata->dock_id;
          $port_id=$selectdata->port_id;
          $ship_id=$selectdata->ship_id;

      $updatedata=array('ship_id'=>$selectdata->ship_id,
                        'status'=>2,
                        'date'=>date("Y-m-d H:i:s"),
                        );
      
      $where="port_id=$port_id and dock_id=$dock_id";
      
      $this->open_boarding_model->update("app.t_tmp_passanger_info",$updatedata,$where);

      if ($this->db->trans_status() === FALSE)
      {
        $this->db->trans_rollback();
        echo json_encode($this->db->error());
      }
      else
      {
        $this->db->trans_commit();
        echo json_api(1,'Berhasil Open Boarding');
      }
    }

    public function update($id)
    {
       $data=array(
                    'status'=>2,
                    'updated_on'=>date('Y-m-d H:i:s'),
                    'updated_by'=>$this->session->userdata('id'),
       );

      $this->db->trans_begin();
      $update=$this->open_boarding_model->update('app.t_trx_boarding',$data,"id=$id");    

      $selectdata=$this->db->query("select b.dock_id, b.ship_id, port_id  from app.t_trx_boarding a
                                    join app.t_trx_dock b on a.dock_id=b.id where a.id=$id ")->row();

       $updatedata=array('ship_id'=>$selectdata->ship_id,
                         'status'=>3,
                         'date'=>date("Y-m-d H:i:s"),
                        );

      $where="port_id=$selectdata->port_id and dock_id=$selectdata->dock_id";
      $this->open_boarding_model->update('app.t_tmp_passanger_info',$updatedata,$where); 

      if ($this->db->trans_status() === FALSE)
      {
        $this->db->trans_rollback();
        echo json_encode($this->db->error());
      }
      else
      {
        $this->db->trans_commit();
        echo json_api(1,'Berhasil Close Boarding');
      }
          
    }

    public function updateSail($id)
    {
      
      $boarding=$this->db->query("select b.dock_id, b.ship_id, port_id, a.created_on as depart_date, a.id as boarding_id  from app.t_trx_boarding a
        join app.t_trx_dock b on a.dock_id=b.id
        where a.id=$id
        ")->row();


      $datasail=array(
            'schedule_time_id'=>NULL,
            'status'=>1,
            'created_by'=>1,
            'updated_by'=>0,
            'ship_id'=>$boarding->ship_id,
            'depart_date'=>date('Y-m-d H:i:s'),
            'boarding_id'=>$id,
      );

    $this->db->trans_begin();
     $this->open_boarding_model->insertdata('app.t_trx_sail',$datasail);

     $this->db->query("update app.t_tmp_passanger_info set ship_id=".$boarding->ship_id.", status=4 , date='".date("Y-m-d H:i:s")."'where port_id=".$boarding->port_id." and dock_id=".$boarding->dock_id." ");

      //update passanger
      $caridata=$this->db->query("select * from app.t_trx_boarding_detail where boarding_id=$id")->result();
      foreach($caridata as $caridata)
      {
       
        $this->db->query(" update app.t_trx_booking_passanger set status=4,
        updated_on='".date('Y-m-d H:i:s')."' , updated_by=".$this->session->userdata('id')." where booking_id=".$caridata->booking_id." and ticket_number='".$caridata->ticket_number."' ");

      }

      //update vehicle passanger

      $caridata2=$this->db->query("select * from app.t_trx_boarding_detail where boarding_id=$id")->result();
      foreach($caridata2 as $caridata2)
      {
        $this->db->query(" update app.t_trx_booking_passanger set status=4,
        updated_on='".date('Y-m-d H:i:s')."', updated_by=".$this->session->userdata('id')." where booking_id=".$caridata->booking_id." ");
      }

      // update vehicle
      $caridata2=$this->db->query("select * from app.t_trx_boarding_detail where boarding_id=$id")->result();
      foreach($caridata2 as $caridata2)
      {
        $this->db->query(" update app.t_trx_booking_vehicle set status=4,
        updated_on='".date('Y-m-d H:i:s')."' , updated_by=".$this->session->userdata('id')." where booking_id=".$caridata->booking_id."
          and ticket_number='".$caridata->ticket_number."'
         ");
      }

      if ($this->db->trans_status() === FALSE)
        {
              $this->db->trans_rollback();
              echo json_encode($this->db->error());
        }
        else
        {
          $this->db->trans_commit();
          echo json_api(1,'Kapal Berhasil Berangkat');
        }

    }
}
