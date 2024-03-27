<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'booking';
	}

	public function bookingList(){
		$start 		  = $this->input->post('start');
		$length 	  = $this->input->post('length');
		$draw 		  = $this->input->post('draw');
		$search 	  = $this->input->post('search');
		$order 		  = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir 	  = strtoupper($order[0]['dir']);
		$dateFrom 	  = $this->input->post('dateFrom');
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$field = array(
			0=>'a.id',
			1=>'customer_name',
			2=>'service',
			3=>'code',
			4=>'origin_port_name',
			5=>'destination_port_name',
			6=>'departure',
			7=>'depart_date',
			8=>'booking_date',
		);

		$order_column 	= $field[$order_column];		
		$where 			= "WHERE a.status is not null ";
	
		if (!empty($search['value'])){
			$where .= "AND (a.customer_name ilike '%".$iLike."%' or a.code ilike '%".$iLike."%')";
		}
		
		if (!empty($dateFrom)){
			$where .= "AND (to_char(a.depart_date,'yyyy-mm-dd')='".$dateFrom."')";
		}

		$sql = "SELECT a.created_on as booking_date, c.departure, b.name as service, e.name as ship_name, f.name as origin_port_name,g.name as destination_port_name, a.* from app.t_trx_booking a
				left join app.t_mtr_service b on a.service_id=b.id
				left join app.t_mtr_schedule_time c on a.schedule_time_id=c.id
				left join app.t_mtr_schedule d on c.schedule_id=d.id
				left join app.t_mtr_ship e on d.ship_id=e.id
				left join app.t_mtr_port f on d.origin_port_id=f.id
				left join app.t_mtr_port g on d.destination_port_id=g.id
				{$where}";
		
		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();

		$sql .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();
							
		$rows = array();
		$i    = ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->departure = format_time($row->departure);	
			$row->depart_date = format_date($row->depart_date);
			$row->booking_date = format_date($row->booking_date);
			$row->id = $this->enc->encode($row->id);
      		$detail_url = site_url($this->_module."/detail/{$row->id}");
      		$row->actions = generate_button_new($this->_module, 'detail', $detail_url);

			// $row->actions = generate_button($this->_module, 'detail', '<a href="'.site_url('booking/detail/'.$row->id).'" class="btn btn-xs btn-warning" title="Detail"><i class="fa fa-search-plus"></i></a>');

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);

	}
	
	public function getDetail($id){
		return $this->db->query("
			SELECT g.name as destination_name, f.name as origin_name,e.name as ship_name, c.departure, b.name, a.* from app.t_trx_booking a
			left join app.t_mtr_service b on a.service_id=b.id
			left join app.t_mtr_schedule_time c on a.schedule_time_id=c.id
			left join app.t_mtr_schedule d on c.schedule_id=d.id
			left join app.t_mtr_ship e on d.ship_id=e.id
			left join app.t_mtr_port f on d.origin_port_id=f.id
			left join app.t_mtr_port g on d.destination_port_id=g.id
			where a.id=$id
		");
	}
	
	public function getDetailPassanger($id){
		return $this->db->query("
			SELECT c.name as type_name,a.fare as fare_jenis, count(a.passanger_type_id) as jumlah_passenger_type, sum(a.fare)as fare from app.t_trx_booking_passanger a
			left join  app.t_trx_booking b on a.booking_id=b.id
			left join app.t_mtr_passanger_type c on a.passanger_type_id=c.id where a.booking_id=$id group by c.name, fare_jenis
				
		");
	}
	
	public function getNamePassanger($id){
		return $this->db->query("
			SELECT b.name as class_name, a.* from app.t_trx_booking_passanger a
			left join app.t_mtr_passanger_type b on a.passanger_type_id=b.id
			where a.booking_id=$id order by a.name asc
		");
	}
	
	public function getById($id){
		return $this->db->query("SELECT * from app.t_trx_booking where id=$id");
	}
	
	public function getVehicleClass($id){
		/*return $this->db->query("
			select c.name as vehicle_name , a.* from app.t_trx_booking_vehicle a
			left join app.t_trx_booking b on a.booking_id=b.id
			left join app.t_mtr_vehicle_class c on a.vehicle_class_id=c.id
			where a.booking_id=$id
		");
		*/
		
		return $this->db->query("
			select c.name as vehicle_class_name ,a.fare, sum(a.fare)as sum_fare, count(vehicle_class_id), a.id_number from app.t_trx_booking_vehicle a
			left join app.t_trx_booking b on a.booking_id=b.id
			left join app.t_mtr_vehicle_class c on a.vehicle_class_id=c.id
			where booking_id=$id
			group by vehicle_class_name, fare, a.id_number 
		");
	}
	
	public function getBookingVehicle($id){
		return $this->db->query("select * from app.t_trx_booking_vehicle
		where booking_id=$id");
	}
	
}
