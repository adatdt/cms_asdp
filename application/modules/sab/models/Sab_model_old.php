<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sab_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'sab';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$port_origin = $this->enc->decode($this->input->post('port_origin'));
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$field = array(
			0 =>'id',
			1 =>'customer_name',
			2 =>'service_name',
			3 =>'trans_number',
			4 =>'booking_code',
			5 =>'port_destination',
			6 =>'port_origin',
			7 =>'depart_date',
			8 =>'total_passanger',
		);

		$order_column = $field[$order_column];

		$where = " WHERE B.status not in (-5) and B.ticket_type = 2";
	
		if(!empty($service_id))
		{
			$where .="and (B.service_id=".$service_id.")";
		}

		if(!empty($port_origin))
		{
			$where .="and (B.origin=".$port_origin.")";
		}

		if(!empty($port_destination))
		{
			$where .="and (B.destination=".$port_destination.")";
		}

		if (!empty($dateTo) and !empty($dateFrom))
		{
			$where .="and (to_char(B.depart_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		}
		else if(empty($dateFrom) and !empty($dateTo))
		{
			$where .="and (to_char(B.depart_date,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		}
		else if (!empty($dateFrom) and empty($dateTo))
		{
			$where .="and (to_char(B.depart_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		}
		else
		{
			$where .="and (to_char(B.depart_date,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		}

		if (!empty($search['value'])){
			$where .="and (B.booking_code ilike '%".$iLike."%')";
		}

		$sql 		   = "SELECT B.*, S.id as service_id, S.name as service_name, booking_code, P.name as port_destination, P2.name as port_origin, depart_date, total_passanger, I.customer_name
						FROM app.t_trx_booking B
						LEFT JOIN app.t_mtr_port P2 ON P2.id = B.origin
						LEFT JOIN app.t_mtr_port P ON P.id = B.destination
						LEFT JOIN app.t_trx_invoice I ON B.trans_number = I.trans_number
						LEFT JOIN app.t_mtr_service S ON S.id = B.service_id
						{$where}";
		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->created_on=format_dateTime($row->created_on);
			$row->depart_date=format_date($row->depart_date);
			$row->port_origin=strtoupper($row->port_origin);
			$row->port_destination=strtoupper($row->port_destination);
			$row->id 	 = $this->enc->encode($row->id);

			$booking_code=$this->enc->encode($row->booking_code);
			$service_id=$this->enc->encode($row->service_id);

			$detail_url 	 = site_url($this->_module."/detail/{$booking_code}");
			$print_url  = site_url($this->_module."/download_pdf/{$booking_code}/{$service_id}");

			$row->actions = '';
			$row->actions  = generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions .= generate_button_new($this->_module, 'download_pdf', $print_url);

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

	public function select_data($table, $where)
	{
		return $this->db->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->db->insert($table, $data);
	}

	public function update_data($table,$data,$where)
	{
		$this->db->where($where);
		$this->db->update($table, $data);
	}

	public function delete_data($table,$data,$where)
	{
		$this->db->where($where);
		$this->db->delete($table, $data);
	}

	public function get_route($origin)
	{
		return $this->db->query("select P.name, R.destination
								from app.t_mtr_rute R
								join app.t_mtr_port P on R.destination = P.id
								where origin = ".$origin."");
	}

	public function listDetail($where=""){

		return $this->db->query("
							select g.name as service_name,f.name as ship_class_name, e.name as destination_name, d.name as origin_name, 
							c.name as special_service_name, b.name as passenger_type_name, a.* from app.t_trx_booking_passanger a
							left join  app.t_mtr_passanger_type b on a.passanger_type_id=b.id
							left join app.t_mtr_special_service c on a.special_service_id=c.id
							left join app.t_mtr_port d on a.origin=d.id
							left join app.t_mtr_port e on a.destination=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_mtr_service g on a.service_id=g.id
							$where
							");
	}

	public function listVehicle($where=""){

		return $this->db->query("
							select g.name as service_name,f.name as ship_class_name, e.name as destination_name, d.name as origin_name,
							b.name as vehicle_class_name, a.* from app.t_trx_booking_vehicle a
							left join  app.t_mtr_vehicle_class b on a.vehicle_class_id=b.id
							left join app.t_mtr_port d on a.origin=d.id
							left join app.t_mtr_port e on a.destination=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_mtr_service g on a.service_id=g.id
							$where
							");
	}

	public function ticket($where=""){

		return $this->db->query("
							select BP.name as penumpang, BP.id_number, P.name as origin, P2.name as destination, B.depart_date, S.name as service, BP.gender, BP.age, SC.name as ship_class, BP.ticket_number
							from app.t_trx_booking B
							left join app.t_trx_booking_passanger BP on B.booking_code = BP.booking_code
							left join app.t_mtr_port P on P.id = B.origin
							left join app.t_mtr_port P2 on P2.id = B.destination
							left join app.t_mtr_service S on S.id = B.service_id
							left join app.t_mtr_ship_class SC on SC.id = B.ship_class
							$where
							");
	}

	public function ticket_vehicle($booking_code){
		// die($booking_code);
		return $this->db->query("
							select BP.name as penumpang, BV.id_number, P.name as origin, P2.name as destination, B.depart_date, S.name as service, SC.name as ship_class, BV.ticket_number, VC.name as vehicle_name
							from app.t_trx_booking B
							left join app.t_trx_booking_passanger BP on B.booking_code = BP.booking_code
							left join app.t_trx_booking_vehicle BV on B.booking_code = BV.booking_code
							left join app.t_mtr_port P on P.id = B.origin
							left join app.t_mtr_port P2 on P2.id = B.destination
							left join app.t_mtr_service S on S.id = B.service_id
							left join app.t_mtr_ship_class SC on SC.id = B.ship_class
							left join app.t_mtr_vehicle_class VC on VC.id = BV.vehicle_class_id
							WHERE B.status != -5 AND BV.service_id = 2 AND BP.id IN(SELECT MIN(BP2.id) FROM app.t_trx_booking_passanger BP2 WHERE BP2.booking_code=BP.booking_code) AND BV.booking_code = '{$booking_code}'
							");
	}



	public function initialize(){
		$port = $this->db->query("SELECT id, name, city
								FROM app.t_mtr_port
								WHERE status = 1 ORDER BY id ASC")->result();

		$service = $this->db->query("SELECT id, name
								FROM app.t_mtr_service
								WHERE status = 1 ORDER BY id ASC")->result();

		$ship_class = $this->db->query("SELECT id, name
								FROM app.t_mtr_ship_class
								WHERE status = 1 ORDER BY id ASC")->result();

		$custom_param = $this->db->query("SELECT param_name
								FROM app.t_mtr_custom_param
								WHERE status = 1")->result();

		$passanger_type = $this->db->query("SELECT id, name, description
								FROM app.t_mtr_passanger_type
								WHERE status = 1 ORDER BY id ASC")->result();

		$passanger_type_id = $this->db->query("SELECT id, name
								FROM app.t_mtr_passanger_type_id
								WHERE status = 1 ORDER BY id ASC")->result();

		$ship_class = $this->db->query("SELECT id, name
								FROM app.t_mtr_ship_class
								WHERE status = 1 ORDER BY id ASC")->result();
		$data = array(
			'service'			=> $service,
			'origin_port'		=> $port,
			'ship_class'		=> $ship_class,
			'config_param'		=> $custom_param,
			'passanger_type'	=> $passanger_type,
			'passanger_type_id'	=> $passanger_type_id,
			'ship_class'		=> $ship_class
		);

		return json_decode(json_encode(array(
			'code'		=> 1,
			'message'	=> 'Parameter Initialization.',
			'data'		=> $data
		)));
	}

	public function getPortListDestination($origin){
		$query = $this->db->query("SELECT R.destination as destination_port_id, P.city as destination_city, P.name as destination_port_name
								FROM app.t_mtr_rute R
								JOIN app.t_mtr_port P ON R.destination = P.id
								WHERE origin = ".$origin."");
		$total = $query->num_rows();

		$data = array(
			'schedule' => $query->result(),
		);

		return json_decode(json_encode(array(
			'code'		=> 1,
			'message'	=> $total.' data found',
			'data'		=> $data
		)));
	}

	public function getVehicleClass(){
		$query = $this->db->query("SELECT VC.id, VC.name, VT.name as type, min_length, max_length, adult_capacity, child_capacity, infant_capacity, max_capacity, description
									FROM app.t_mtr_vehicle_class VC
									JOIN app.t_mtr_vehicle_type VT
									ON VT.id = VC.type
									WHERE VC.status = 1");
		$total = $query->num_rows();

		$data = array(
			'vehicle_class' => $query->result(),
		);

		return json_decode(json_encode(array(
			'code'		=> 1,
			'message'	=> $total.' data kelas kendaraan.',
			'data'		=> $data
		)));
	}

}
