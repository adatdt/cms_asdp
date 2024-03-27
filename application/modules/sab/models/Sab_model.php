<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sab_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'sab';
	}

    public function listPenumpang(){

		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateFrom     = date("Y-m-d", strtotime(trim($this->input->post('dateFrom'))));
		$dateTo       = date("Y-m-d", strtotime(trim($this->input->post('dateTo'))));
		$port_origin = $this->enc->decode($this->input->post('port_origin'));
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));
		
		$field = array(
			0 =>'id',
			1 =>'gatein_date',
			2 =>'ticket_number',
			3 =>'penumpang',
			4 =>'golongan',
			5 =>'instansi',
			6 =>'service',
			7 =>'port',
			8 =>'ship',
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE B.status not in (-5) and B.ticket_type = 2 and B.created_on >= '". $dateFrom . "' and B.created_on < '" . $dateToNew . "'";
		// $where = " WHERE B.status not in (-5) and B.ticket_type = 2";
	
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

		// if (!empty($dateTo) and !empty($dateFrom))
		// {
		// 	$where .="and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		// }
		// else if(empty($dateFrom) and !empty($dateTo))
		// {
		// 	$where .="and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		// }
		// else if (!empty($dateFrom) and empty($dateTo))
		// {
		// 	$where .="and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		// }
		// else
		// {
		// 	$where .="and (to_char(B.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		// }

		if(!empty($searchData))
		{
			if($searchName=='ticketNumber')
			{
				$where .= " and ( BP.ticket_number ilike '%".$iLike."%' ESCAPE '!' ) ";
			}
			else if ($searchName=='passName') 
			{
				$where .= " and ( BP.name ilike '%".$iLike."%' ESCAPE '!') ";				
			}
			else
			{
				$where .= " and ( I.customer_name ilike '%".$iLike."%' ESCAPE '!') ";
			}
		}

		$sql 		   = "SELECT 
						B.id, B.booking_code,
						S.id as service_id,
						GI.created_on AS gatein_date,
						BP.ticket_number,
						BP.name as penumpang,
						PT.name as golongan,
						I.customer_name as instansi,
						S.name as service,
						P.name as port,
						K.name as ship
						FROM 
						app.t_trx_booking B
						JOIN app.t_trx_booking_passanger BP ON B.booking_code = BP.booking_code AND BP.service_id = 1
						JOIN app.t_trx_invoice I ON I.trans_number = B.trans_number
						LEFT JOIN app.t_trx_gate_in GI ON BP.ticket_number = GI.ticket_number
						LEFT JOIN app.t_mtr_service S ON S.id = BP.service_id
						LEFT JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
						LEFT JOIN app.t_mtr_port P ON P.id = BP.origin
						LEFT JOIN app.t_trx_open_boarding OB ON OB.boarding_code = BRP.boarding_code
						LEFT JOIN app.t_mtr_ship K ON K.id = OB.ship_id
						LEFT JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id
						{$where}";

		$sqlCount = "SELECT 
										count(b.id) as countdata
								FROM 
										app.t_trx_booking B
										JOIN app.t_trx_booking_passanger BP ON B.booking_code = BP.booking_code AND BP.service_id = 1
										JOIN app.t_trx_invoice I ON I.trans_number = B.trans_number
										LEFT JOIN app.t_trx_gate_in GI ON BP.ticket_number = GI.ticket_number
										LEFT JOIN app.t_mtr_service S ON S.id = BP.service_id
										LEFT JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
										LEFT JOIN app.t_mtr_port P ON P.id = BP.origin
										LEFT JOIN app.t_trx_open_boarding OB ON OB.boarding_code = BRP.boarding_code
										LEFT JOIN app.t_mtr_ship K ON K.id = OB.ship_id
										LEFT JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id
								{$where}";

		$queryCount         = $this->db->query($sqlCount)->row();
		$records_total = $queryCount->countdata;
		
		// $query         = $this->db->query($sql);
		// $records_total = $query->num_rows();
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
			if($row->gatein_date){
				$row->gatein_date=format_dateTime($row->gatein_date);
			} else {
				$row->gatein_date = '';
			}

			$url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
			$row->ticket_number = '<a href="'.$url.'" target="_blank">'.$row->ticket_number.'</a>';

			$row->id 	 = $this->enc->encode($row->id);
			$booking_code=$this->enc->encode($row->booking_code);
			$service_id=$this->enc->encode($row->service_id);

			// $detail_url 	 = site_url($this->_module."/detail/{$booking_code}");
			$print_url  	= site_url($this->_module."/download_pdf/{$booking_code}/{$service_id}");

			$row->actions = '';
			// $row->actions  = generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions .= generate_button_new($this->_module, 'download_pdf', $print_url);

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows,
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
		);
	}

	public function listKendaraan(){

		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateFrom     = date("Y-m-d", strtotime(trim($this->input->post('dateFrom'))));
		$dateTo       = date("Y-m-d", strtotime(trim($this->input->post('dateTo'))));
		$port_origin = $this->enc->decode($this->input->post('port_origin'));
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));
		
		$field = array(
			0 =>'id',
			1 =>'gatein_date',
			2 =>'ticket_number',
			3 =>'plat',
			4 =>'golongan',
			5 =>'instansi',
			6 =>'service',
			7 =>'port',
			8 =>'ship',
			9 =>'total_passanger',
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE B.status not in (-5) and B.ticket_type = 2 and B.created_on >= '". $dateFrom . "' and B.created_on < '" . $dateToNew . "'";
		// $where = " WHERE B.status not in (-5) and B.ticket_type = 2";
	
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

		// if (!empty($dateTo) and !empty($dateFrom))
		// {
		// 	$where .="and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		// }
		// else if(empty($dateFrom) and !empty($dateTo))
		// {
		// 	$where .="and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		// }
		// else if (!empty($dateFrom) and empty($dateTo))
		// {
		// 	$where .="and (to_char(B.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		// }
		// else
		// {
		// 	$where .="and (to_char(B.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		// }

		if(!empty($searchData))
		{
			if($searchName=='ticketNumber')
			{
				$where .= " and ( BV.ticket_number ilike '%".$iLike."%' ESCAPE '!' ) ";
			}
			else
			{
				$where .= " and ( I.customer_name ilike '%".$iLike."%' ESCAPE '!' ) ";
			}
		}		

		$sql 		   = "SELECT 
						B.id, B.booking_code,
						S.id as service_id,
						GI.created_on as gatein_date,
						BV.ticket_number,
						BV.id_number as plat,
						VC.name as golongan,
						I.customer_name as instansi,
						S.name as service,
						P.name as port,
						K.name as ship,
						total_passanger
						FROM 
						app.t_trx_booking B
						JOIN app.t_trx_booking_vehicle BV ON B.booking_code = BV.booking_code AND BV.service_id = 2
						JOIN app.t_trx_invoice I ON I.trans_number = B.trans_number
						LEFT JOIN app.t_trx_gate_in_vehicle GI ON BV.ticket_number = GI.ticket_number
						LEFT JOIN app.t_mtr_service S ON S.id = BV.service_id
						LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
						LEFT JOIN app.t_mtr_port P ON P.id = BV.origin
						LEFT JOIN app.t_trx_open_boarding OB ON OB.boarding_code = BRV.boarding_code
						LEFT JOIN app.t_mtr_ship K ON K.id = OB.ship_id
						LEFT JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
						{$where}";

		$sqlCount = "SELECT 
										count(B.id) as countdata
								FROM 
										app.t_trx_booking B
										JOIN app.t_trx_booking_vehicle BV ON B.booking_code = BV.booking_code AND BV.service_id = 2
										JOIN app.t_trx_invoice I ON I.trans_number = B.trans_number
										LEFT JOIN app.t_trx_gate_in_vehicle GI ON BV.ticket_number = GI.ticket_number
										LEFT JOIN app.t_mtr_service S ON S.id = BV.service_id
										LEFT JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
										LEFT JOIN app.t_mtr_port P ON P.id = BV.origin
										LEFT JOIN app.t_trx_open_boarding OB ON OB.boarding_code = BRV.boarding_code
										LEFT JOIN app.t_mtr_ship K ON K.id = OB.ship_id
										LEFT JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
								{$where}";

		$queryCount         = $this->db->query($sqlCount)->row();
		$records_total = $queryCount->countdata;
		// $query         = $this->db->query($sql);
		// $records_total = $query->num_rows();
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
			if($row->gatein_date){
				$row->gatein_date=format_dateTime($row->gatein_date);
			} else {
				$row->gatein_date = '';
			}

			$url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
			$row->ticket_number = '<a href="'.$url.'" target="_blank">'.$row->ticket_number.'</a>';

			$row->id 	 = $this->enc->encode($row->id);
			$booking_code=$this->enc->encode($row->booking_code);
			$service_id=$this->enc->encode($row->service_id);

			// $detail_url 	 = site_url($this->_module."/detail/{$booking_code}");
			$print_url  	= site_url($this->_module."/download_pdf/{$booking_code}/{$service_id}");

			$row->actions = '';
			// $row->actions  = generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions .= generate_button_new($this->_module, 'download_pdf', $print_url);

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows,
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
		);
	}
	public function getPassangerType($origin, $shipClass)
	{
		$query=" 
				select tmpt.* from app.t_mtr_passanger_type tmpt 
				join app.t_mtr_fare_passanger tmfp on tmpt.id = tmfp.passanger_type  and tmfp.status =1
				join app.t_mtr_rute tmr on tmfp.rute_id = tmr.id and tmr.status =1
				where tmr.origin = {$origin} and tmfp.ship_class ={$shipClass}
				and tmpt.status=1
				order by tmpt.ordering asc
		";

		return $this->db->query($query)->result();
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

	public function ticket_21112022($where=""){

		return $this->db->query("
							select BP.name as penumpang, BP.id_number, P.name as origin, P2.name as destination, B.depart_date, S.name as service, BP.gender, BP.age, SC.name as ship_class, BP.ticket_number, customer_name as instansi
							from app.t_trx_booking B
							left join app.t_trx_invoice I on I.trans_number = B.trans_number
							left join app.t_trx_booking_passanger BP on B.booking_code = BP.booking_code
							left join app.t_mtr_port P on P.id = B.origin
							left join app.t_mtr_port P2 on P2.id = B.destination
							left join app.t_mtr_service S on S.id = B.service_id
							left join app.t_mtr_ship_class SC on SC.id = B.ship_class
							$where
							");
	}

	public function ticket($where=""){

		return $this->db->query("SELECT
								BP.name as penumpang, 
								BP.id_number, 
								P.name as origin, 
								p.city as origin_city,	
								P2.name as destination,
								p2.city as destination_city,
								B.depart_date, 
								B.depart_time_start, 
								B.depart_time_end, 
								S.name as service,
								BP.gender,
								BP.age,
								SC.name as ship_class,
								SC.id as ship_class_id,  
								BP.ticket_number,
								I.customer_name as instansi,
								I.email,
								I.created_by,
								pt.name as passanger_type,
								I.phone_number
							from app.t_trx_booking B
							left join app.t_trx_invoice I on I.trans_number = B.trans_number
							left join app.t_trx_booking_passanger BP on B.booking_code = BP.booking_code
							left join app.t_mtr_port P on P.id = B.origin
							left join app.t_mtr_port P2 on P2.id = B.destination
							left join app.t_mtr_service S on S.id = B.service_id
							left join app.t_mtr_ship_class SC on SC.id = B.ship_class
							left join app.t_mtr_passanger_type pt on BP.passanger_type_id = pt.id
							$where
							order by BP.ticket_number asc
							");
	}	

	public function ticket_vehicle_21112022($booking_code){
		// die($booking_code);
		return $this->db->query("
							select BP.name as penumpang, BV.id_number, P.name as origin, P2.name as destination, B.depart_date, S.name as service, SC.name as ship_class, BV.ticket_number, VC.name as vehicle_name, customer_name as instansi
							from app.t_trx_booking B
							left join app.t_trx_invoice I on I.trans_number = B.trans_number
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

	public function ticket_vehicle($booking_code){
		// die($booking_code);
		return $this->db->query("SELECT
								BP.name as penumpang,
								BP.id_number as identity_number,
								BP.depart_time_start ,
								BP.depart_time_end ,
								BV.id_number, 
								P.name as origin, 
								P.city as origin_city, 
								P2.name as destination, 
								P2.city as destination_city, 
								B.depart_date,
								B.booking_code,
								S.name as service, 
								SC.name as ship_class, 
								SC.id as ship_class_id, 
								BV.ticket_number, 
								pt.name as passanger_type,
								VC.name as vehicle_name, 
								I.customer_name as instansi,
								I.email,
								I.created_by,
								pt.name as passanger_type,
								I.phone_number
							from app.t_trx_booking B
							left join app.t_trx_invoice I on I.trans_number = B.trans_number
							left join app.t_trx_booking_passanger BP on B.booking_code = BP.booking_code
							left join app.t_trx_booking_vehicle BV on B.booking_code = BV.booking_code
							left join app.t_mtr_port P on P.id = B.origin
							left join app.t_mtr_port P2 on P2.id = B.destination
							left join app.t_mtr_service S on S.id = B.service_id
							left join app.t_mtr_ship_class SC on SC.id = B.ship_class
							left join app.t_mtr_vehicle_class VC on VC.id = BV.vehicle_class_id
							left join app.t_mtr_passanger_type pt on BP.passanger_type_id = pt.id
							WHERE B.status != -5 AND BV.service_id = 2 
							AND B.booking_code = '{$booking_code}'
							order by bp.ticket_number asc
							");
	}	



	public function initialize(){
		$port = $this->db->query("SELECT id, name, city
								FROM app.t_mtr_port
								WHERE status = 1 ORDER BY id ASC")->result();

		$service = $this->db->query("SELECT id, name
								FROM app.t_mtr_service
								WHERE 
								--status = 1
								status <>'-5' 
								ORDER BY id ASC")->result();

		$ship_class = $this->db->query("SELECT id, name
								FROM app.t_mtr_ship_class
								WHERE status = 1 ORDER BY id ASC")->result();

		$custom_param = $this->db->query("SELECT param_name
								FROM app.t_mtr_custom_param
								WHERE status = 1")->result();

		$max_booking_pass = $this->db->query("SELECT param_value
						FROM app.t_mtr_custom_param
						WHERE status = 1 and param_name='max_booking_passanger' ")->row();

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
			'ship_class'		=> $ship_class,
			'max_booking_pass'  => $max_booking_pass
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
								WHERE origin = ".$origin."
								ORDER BY P.id ASC");
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

	public function get_ship_class_rute($origin,$destination) {
    $origin      = $this->db->escape($origin);
		$destination = $this->db->escape($destination);

    $sql = "SELECT  sclass.id, sclass.name as ship_class_name
            FROM  app.t_mtr_rute  rute 
            JOIN app.t_mtr_rute_class rclass ON rclass.rute_id = rute.id AND rclass.status = 1
            JOIN app.t_mtr_ship_class sclass ON rclass.ship_class = sclass.id 
            WHERE rute.origin = {$origin} and rute.destination = {$destination} and rute.status = 1
            group by sclass.id,sclass.id, sclass.name,rclass.ship_class 
            ORDER BY  name";
    return $this->db->query($sql)->result();
	}

	// bug fixing cross date schedule express
  function get_time($origin, $destination,$depart_date,$ship_class,$now = null) {
    $origin = $this->db->escape($origin);
    $destination = $this->db->escape($destination);
    $date = date('Y-m-d');
    if ($depart_date == $date) {
        $end = date('Y-m-d 00:00:00', strtotime($depart_date . "+ 1 days"));
        $now = $this->db->escape($now);
        $end = $this->db->escape($end);
        $where = " AND sch.sail_time >= {$now} AND sch.sail_time < {$end} ";
    }
    else{
        if ($now > $depart_date) {
            $start = $this->db->escape($now);
        }
        else{
            $start = $this->db->escape($depart_date);
        }
        $end = date('Y-m-d H:i:s', strtotime($depart_date . "+ 1 days"));
        $depart_date = $this->db->escape($depart_date);
        $end = $this->db->escape($end);
        $where = " AND sch.sail_time >= {$start} AND sch.sail_time < {$end} ";
    }

    $sql = "SELECT sail_time as docking_on,ship.name as ship_name,sch.schedule_code
            FROM app.t_mtr_schedule sch 
            LEFT JOIN app.t_mtr_ship ship on ship.id = sch.ship_id
            WHERE sch.port_id = {$origin} 
            -- AND sch.schedule_date = '{$depart_date}' 
            and sch.status = 1 AND sch.ship_class = {$ship_class} 
              AND sch.sail_time IS NOT NULL  
            $where 
            ORDER BY sail_time asc";
    return $this->db->query($sql)->result();
  }
  // end bug fixing cross date schedule express

	public function getVehicleClass($portId, $shipClass){
		
		$query = $this->db->query("SELECT VC.id, VC.name, VT.name as type, min_length, max_length, adult_capacity, child_capacity, infant_capacity, max_capacity, description
									FROM app.t_mtr_vehicle_class VC
									JOIN app.t_mtr_vehicle_type VT
									ON VT.id = VC.type
									WHERE 
									-- VC.status = 1
									VC.status <> '-5'
									ORDER BY VC.name ASC
									");
		

		/*
		$query = $this->db->query("
									SELECT 
									VC.id, VC.name, VT.name as type, min_length, max_length, adult_capacity, child_capacity, infant_capacity, max_capacity, description
									FROM app.t_mtr_vehicle_class VC
									JOIN app.t_mtr_vehicle_type VT ON VT.id = VC.type
									join app.t_mtr_vehicle_class_activated AC on VC.id = AC.vehicle_class 
									WHERE 
									-- VC.status = 1
									VC.status <> '-5'
									and AC.port_id ={$portId}
									and AC.ship_class ={$shipClass}
									and AC.web_admin = true 

									ORDER BY VC.name ASC");
		*/

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

	function get_info_by_booking_code($booking_code) {
        $booking_code = $this->db->escape($booking_code);
        $sql = "SELECT 
        book.id
        ,  book.booking_code
        , inc.customer_name
        , inc.phone_number, inc.email 
        , book.created_on
        , inc.service_id
        , inc.due_date
        , book.status as booking_status
        , inc.status as invoice_status
        , book.depart_date
        , ori.name AS origin_name
        , des.name As destination_name
        , book.service_id, book.created_on, book.status 
        , book.trans_number
        , srv.name
        FROM app.t_trx_booking book 
        JOIN app.t_trx_invoice inc ON inc.trans_number = book.trans_number 
        JOIN app.t_mtr_service srv on srv.id = inc.service_id
        LEFT JOIN app.t_mtr_port ori ON ori.id = book.origin
        LEFT JOIN app.t_mtr_port des ON des.id = book.destination
        WHERE booking_code = {$booking_code};";
        return $this->db->query($sql)->row();
	}

	public function add($param, $cc = '', $bcc = '', $attachment = array()) {
		$this->db->trans_start();
	
		// Email
		$data = array(
			'recipient'  => $param['recipient'],
			'subject'    => $param['subject'],
			'body'       => $param['body'],
			'status'     => $param['status'],
			'created_by' => $param['created_by']
		);
		$this->db->insert('core.t_trx_email', $data);
		$email_id = $this->db->insert_id();
	
		// Recipient CC 
		if (is_array($cc)){
			foreach ($cc as $key => $value) {
						$data_cc = array(
							'email_id'  => $email_id,
							'recipient' => $value
						);
						$this->db->insert('core.t_trx_email_cc', $data_cc);
					}
		}
		else if (trim($cc) != '') {
			$data_cc = array(
					'email_id'  => $email_id,
					'recipient' => $cc
				);
			$this->db->insert('core.t_trx_email_cc', $data_cc);
			
		}
	
		// Recipient BCC 
		if (trim($bcc) != '') {
			$data_bcc = array(
				'email_id' => $email_id,
				'recipient' => $bcc
			);
			$this->db->insert('core.t_trx_email_bcc', $data_bcc);
		}
	
		// Attachment
		if (count($attachment) >= 1) {
			foreach ($attachment as $value) {
				$data_atc = array(
					'email_id' => $email_id,
					'file' => $value
				);
				$this->db->insert('core.t_trx_email_attachment', $data_atc);
			}
		}
	
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	// function get_pending($limit = 10) {
	// 	$sql = "SELECT e.*, cc.recipient AS recipient_cc, bcc.recipient AS recipient_bcc
	// 			FROM core.t_trx_email e
	// 			LEFT JOIN core.t_trx_email_cc cc ON cc.email_id = e.id
	// 			LEFT JOIN core.t_trx_email_bcc bcc ON cc.email_id = e.id
	// 			WHERE e.status = 0 AND e.id > 5848
	// 			ORDER BY e.id ASC
	// 			LIMIT {$limit}";
	// 	return $this->db->query($sql)->result();
	// }

	function get_pending($email) {
		$sql = "SELECT e.*, cc.recipient AS recipient_cc, bcc.recipient AS recipient_bcc
				FROM core.t_trx_email e
				LEFT JOIN core.t_trx_email_cc cc ON cc.email_id = e.id
				LEFT JOIN core.t_trx_email_bcc bcc ON cc.email_id = e.id
				WHERE e.status = 0 AND e.recipient = '{$email}'";
		return $this->db->query($sql)->result();
	}

	function get_attachment($email_id) {
		$sql = "SELECT file FROM core.t_trx_email_attachment WHERE email_id = {$email_id} ORDER BY file";
		return $this->db->query($sql)->result();
	}
	
	public function send($param) {
		$this->load->library('email');
	
		$config['protocol'] = "smtp";
		$config['smtp_host'] = $this->config->item('host','email');
		$config['smtp_port'] = $this->config->item('port','email');
		$config['smtp_user'] = $this->config->item('username','email');
		$config['smtp_pass'] = $this->config->item('password','email');
		$config['charset'] = "utf-8";
		$config['mailtype'] = "html";
		$config['newline'] = "\r\n";
	
	
		$this->email->clear(TRUE);
		$this->email->initialize($config);
		$this->email->from($this->user, 'PT ASDP Indonesia Ferry');
		$this->email->to($param->recipient);
		$this->email->subject($param->subject);
		$this->email->message($param->body);
	
		if ($param->recipient_cc != '') {
		  $this->email->cc($param->recipient_cc);
		}
	
		if ($param->recipient_bcc != '') {
		  $this->email->bcc($param->recipient_bcc);
		}
	
		foreach ($param->files as $value) {
		  $this->email->attach($value->file);
		}
	
		return $this->email->send();
	  }
	
	  public function set_status($email_id, $status) {
		$this->db->where('id', $email_id);
		$data = array(
			'status' => $status,
			'updated_on' => date('Y-m-d H:i:s')
		);
		return $this->db->update('core.t_trx_email', $data);
	  }

    function saveData($table, $data){

        $data['created_by'] =$this->session->userdata('username');
        $data['created_on'] = date('Y-m-d H:i:s');
        $this->db->insert($table, $data);

    }
	
    function selectData($table, $where=""){

        return $this->db->query(" select * from ".$table." ".$where);
    }	

    function insertBatch($table, $arr){

        foreach ($arr as $key => $value) {
            $value['created_by'] = $this->session->userdata('username');
            $value['created_on'] = date('Y-m-d H:i:s');

            $data[] = $value;
        }

        $this->db->insert_batch($table, $data);

    }

    function insertBatch2($table, $data){

        $this->db->insert_batch($table, $data);

		}

    function get_passanger_type() {
			$sql = "SELECT 
					id
					,  name
					, description
					, min_age 
					, max_age
					FROM app.t_mtr_passanger_type 
					WHERE status = 1   
					group by 
					id
					,  name
					ORDER BY id";
			return $this->db->query($sql)->result();
	}


}
