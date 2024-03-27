<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**

 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  2018
 *
 */

class M_extend_ticket extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/extend_ticket';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
			1 =>'ticket_number',
			2 =>'booking_code',
			3 =>'passanger_name',
			4 =>'id_number',
			5 =>'age',
			6 =>'gender',
			7 =>'passanger_type_name',
			8 =>'service_name',
			9 =>'old_gatein_expired',
			10 =>'new_gatein_expired',
			11 =>'old_boarding_expired',
			12=>'new_boarding_expired',
			13 =>'service_name',
			14=>'total_time',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not IN (-5)";
	
		if (!empty($search['value']))
		{
			$where .="and ( a.ticket_number ilike '%".$iLike."%' 
							or b.booking_code ilike '%".$iLike."%'
							or b.name ilike '%".$iLike."%'
							or b.id_number ilike '%".$iLike."%'

							)";
		}

		$sql 		   = "
						SELECT e.name as passanger_type_name, d.name as service_name, b.booking_code,
						b.name as passanger_name, b.gender, b.age,b.id_number,
						a.* from app.t_trx_extend_time_passanger a
						left join app.t_trx_booking_passanger b on a.ticket_number=b.ticket_number
						left join app.t_trx_booking c on b.booking_code=c.booking_code
						left join app.t_mtr_service d on c.service_id=d.id
						left join app.t_mtr_passanger_type e on b.passanger_type_id=e.id
						{$where}

						";
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

			$row->id 	 = $this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$row->id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$row->id}");

			$row->old_gatein_expired=empty($row->old_gatein_expired)?"":format_date($row->old_gatein_expired)." ".format_time($row->old_gatein_expired);
			$row->new_gatein_expired=empty($row->new_gatein_expired)?"":format_date($row->new_gatein_expired)." ".format_time($row->new_gatein_expired);
			$row->old_boarding_expired=empty($row->old_boarding_expired)?"":format_date($row->old_boarding_expired)." ".format_time($row->old_boarding_expired);
			empty($row->new_boarding_expired)?$row->new_boarding_epxired="":$row->new_boarding_expired=format_date($row->new_boarding_expired)." ".format_time($row->new_boarding_expired);

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

    public function dataListVehicle(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
			1 =>'booking_code',
			2 =>'plat_number',
			3 =>'driver_name',
			4 =>'age',
			5 =>'gender',
			6 =>'vehicle_class_name',
			7 =>'service_name',
			8 =>'old_gatein_expired',
			9 =>'new_gatein_expired',
			10 =>'old_boarding_expired',
			11 =>'new_boarding_expired',
			12=>'total_time',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not IN (-5)";
	
		if (!empty($search['value']))
		{
			$where .="and ( a.ticket_number ilike '%".$iLike."%' 
							or c.booking_code ilike '%".$iLike."%'
							or e.name ilike '%".$iLike."%'
							or b.id_number ilike '%".$iLike."%'
							or f.name ilike '%".$iLike."%'

							)";
		}

		// $sql 		   = "
		// 				SELECT
		// 				c.booking_code, d.name as service_name,
		// 				f.name as driver_name, f.age,f.gender, b.id_number as plat_number, e.name as vehicle_class_name,
		// 				a.* from app.t_trx_extend_time_vehicle a
		// 				left join app.t_trx_booking_vehicle b on a.ticket_number=b.ticket_number
		// 				left join app.t_trx_booking c on b.booking_code=c.booking_code
		// 				left join app.t_mtr_service d on c.service_id=d.id
		// 				left join app.t_mtr_vehicle_class e on b.vehicle_class_id=e.id
		// 				left join (
		// 					select min(ticket_number), booking_code, name, age, gender
		// 					from app.t_trx_booking_passanger
		// 					group by booking_code, name, age, gender
		// 				) f on c.booking_code=f.booking_code
		// 				{$where}

		// 				";

		$sql 		   = "
						SELECT
							c.booking_code, 
							d.name as service_name,
							f.name as driver_name, 
							f.age,f.gender,
							b.id_number as plat_number,
							e.name as vehicle_class_name,
							a.* 
						from app.t_trx_extend_time_vehicle a
						left join app.t_trx_booking_vehicle b on a.ticket_number=b.ticket_number
						left join app.t_trx_booking c on b.booking_code=c.booking_code
						left join app.t_mtr_service d on c.service_id=d.id
						left join app.t_mtr_vehicle_class e on b.vehicle_class_id=e.id
						left join app.t_trx_booking_passanger f on c.booking_code=f.booking_code and f.ticket_number=concat(left(f.ticket_number,-2),'02')
						{$where}

						";



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

			$row->old_gatein_expired=empty($row->old_gatein_expired)?"":format_date($row->old_gatein_expired)." ".format_time($row->old_gatein_expired);
			$row->new_gatein_expired=empty($row->new_gatein_expired)?"":format_date($row->new_gatein_expired)." ".format_time($row->new_gatein_expired);
			$row->old_boarding_expired=empty($row->old_boarding_expired)?"":format_date($row->old_boarding_expired)." ".format_time($row->old_boarding_expired);
			empty($row->new_boarding_expired)?$row->new_boarding_epxired="":$row->new_boarding_expired=format_date($row->new_boarding_expired)." ".format_time($row->new_boarding_expired);
			
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


	public function check_ticket($ticket_number)
	{
		return $this->db->query("
			SELECT a.service_id , b.ticket_number ,a.booking_code , b.ship_class, b.status from app.t_trx_booking a
			left join app.t_trx_booking_passanger b on a.booking_code=b.booking_code
			left join app.t_trx_booking_vehicle c on a.booking_code=c.booking_code
			where b.ticket_number='{$ticket_number}' or c.ticket_number='{$ticket_number}' 
		");
	}

	public function get_data_passanger($ticket_number)
	{
		return $this->db->query("SELECT 
								e.name as ship_class_name, c.name as service_name, d.name as passanger_type_name,
								a.* from app.t_trx_booking_passanger a
								left join app.t_trx_booking b on a.booking_code=b.booking_code
								left join app.t_mtr_service c on b.service_id=c.id
								left join app.t_mtr_passanger_type d on a.passanger_type_id=d.id 
								left join app.t_mtr_ship_class e on a.ship_class=e.id
								where a.ticket_number='{$ticket_number}' "
							);
	}


	public function get_data_vehicle($booking_code)
	{
		return $this->db->query(" 
						SELECT 
						d.name as ship_class_name, e.name, e.age, e.gender , c.name as service_name, f.name as passanger_type_name, a.* from app.t_trx_booking_vehicle a
						left join app.t_trx_booking b on a.booking_code=b.booking_code
						left join app.t_mtr_service c on b.service_id=c.id
						left join app.t_mtr_ship_class d on a.ship_class=d.id
						left join 
						(
							select min(ticket_number), name, passanger_type_id,booking_code, age, gender from app.t_trx_booking_passanger
							where booking_code='{$booking_code}'
							group by name, passanger_type_id, booking_code , age, gender
						) e on a.booking_code=e.booking_code
						left join app.t_mtr_passanger_type f on e.passanger_type_id=f.id 
						where a.booking_code='{$booking_code}' "
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

	public function insert_batch_data($table,$data)
	{
		$this->db->insert_batch($table, $data);
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
}
