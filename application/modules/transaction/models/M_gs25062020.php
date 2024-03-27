<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class M_gs extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/gs';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$port_destination = $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));


		if(!empty($this->session->userdata('port_id')))
		{
			$port_origin=$this->session->userdata("port_id");
		}
		else
		{
			$port_origin = $this->enc->decode($this->input->post('port_origin'));
		}


		$field = array(
			0 =>'id',
			1 =>'created_on',
			2 =>'boarding_code',
			3 =>'schedule_date',
			4 =>'ship_name',
			5 =>'port_name',
			6 =>'dock_name',
			7 =>'port_destination',
			8 =>'ship_class_name',
			9 =>'sail_date',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status is not null and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";

		if(!empty($port_origin))
		{
			$where .="and (e.port_id=".$port_origin.")";
		}

		if(!empty($port_destination))
		{
			$where .="and (e.destination_port_id=".$port_destination.")";
		}

		// if (!empty($dateTo) and !empty($dateFrom))
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		// }
		// else if(empty($dateFrom) and !empty($dateTo))
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		// }
		// else if (!empty($dateFrom) and empty($dateTo))
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		// }
		// else
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		// }

		if (!empty($search['value'])){
			$where .="and (a.boarding_code ilike '%".$iLike."%'
						or d.name ilike '%".$iLike."%'
						or f.name ilike '%".$iLike."%'
						)";	
		}

		$sql 		   = "
							select g.name as port_destination, e.sail_date, f.name as ship_name, d.name as ship_class_name ,
							 c.name as dock_name, b.name as port_name, a.* from app.t_trx_open_boarding a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_dock c on a.dock_id=c.id
							left join app.t_mtr_ship_class d on a.ship_class=d.id
							left join app.t_trx_schedule e on a.schedule_code=e.schedule_code
							left join app.t_mtr_ship f on a.ship_id=f.id
							left join app.t_mtr_port g on e.destination_port_id=g.id
							$where
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

			$code=$this->enc->encode($row->boarding_code);
			$detail_url 	= site_url($this->_module."/detail/{$code}");

     		$row->actions="";

     		if ($row->status==1)
     		{
     			$row->status=success_label("Aktif");
     		}
     		else
     		{
     			$row->status=failed_label("Tidak Aktif");	
     		}

     		$row->actions.= generate_button_new($this->_module, 'detail', $detail_url);

     		$row->created_on=format_dateTime($row->created_on);
     		$row->sail_date=empty($row->sail_date)?"":date("H:i:s",strtotime ($row->sail_date));
     		$row->port_origin=strtoupper($row->port_name);
     		$row->schedule_date=format_date($row->schedule_date);
     		$row->port_destination=strtoupper($row->port_destination);

     		     	
     		// $row->created_on=format_dateTimeHis($row->created_on);
     		$row->no=$i;

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

	public function list_detail_passanger($where=""){

		return $this->db->query("
				select l.name as passanger_type_name, b.ticket_number, c.booking_code, k.created_on as sail_date, 
				(select gender from app.t_trx_booking_passanger
				 where ticket_number=b.ticket_number and gender='L') as pria,
				 (select gender from app.t_trx_booking_passanger
				 where ticket_number=b.ticket_number and gender='P') as wanita,
				j.name as ship_class_name, i.name as dock_name, h.name as port_destination,g.name as port_origin,
				f.name as service_name,c.id_number, c.name as passanger_name,c.city,c.age, c.gender, d.name as ship_name,b.boarding_code, b.ticket_number,  a.* 
				from app.t_trx_open_boarding a
				left join app.t_trx_boarding_passanger b on a.boarding_code=b.boarding_code
				left join app.t_trx_booking_passanger c on b.ticket_number=c.ticket_number
				left join app.t_mtr_ship d on a.ship_id=d.id
				left join app.t_trx_booking e on c.booking_code=e.booking_code
				left join app.t_mtr_service f on e.service_id=f.id
				left join app.t_mtr_port g on e.origin=g.id
				left join app.t_mtr_port h on e.destination=h.id
				left join app.t_mtr_dock i on a.dock_id=i.id
				left join app.t_mtr_ship_class j on a.ship_class=j.id
				left join app.t_trx_sail k on a.schedule_code=k.schedule_code
				left join app.t_mtr_passanger_type l on c.passanger_type_id=l.id
				$where
				order by l.name asc
		");
	}

// function lama tanggal 23-06-2020
// 	public function list_detail_passanger_vehicle($where=""){

// 		return $this->db->query("
// select m.id_number as plate_number, l.name as passanger_type_name, b.ticket_number, c.booking_code, k.created_on as sail_date, j.name as ship_class_name, i.name as dock_name, h.name as port_destination,g.name as port_origin,
//  			f.name as service_name,c.id_number, c.name as passanger_name,c.city,c.age, c.gender, d.name as ship_name,b.boarding_code, b.ticket_number,  a.* 
//  			from app.t_trx_open_boarding a
// 			left join app.t_trx_boarding_passanger b on a.boarding_code=b.boarding_code
// 			left join app.t_trx_booking_passanger c on b.ticket_number=c.ticket_number
// 			left join app.t_mtr_ship d on a.ship_id=d.id
// 			left join app.t_trx_booking e on c.booking_code=e.booking_code
// 			left join app.t_mtr_service f on e.service_id=f.id
// 			left join app.t_mtr_port g on e.origin=g.id
// 			left join app.t_mtr_port h on e.destination=h.id
// 			left join app.t_mtr_dock i on a.dock_id=i.id
// 			left join app.t_mtr_ship_class j on a.ship_class=j.id
// 			left join app.t_trx_sail k on a.schedule_code=k.schedule_code
// 			left join app.t_mtr_passanger_type l on c.passanger_type_id=l.id
// 			left join app.t_trx_booking_vehicle m on c.booking_code=m.booking_code
// 			$where
// 			order by l.name asc
// 			");
// 	}


	public function list_detail_passanger_vehicle($where=""){

		return $this->db->query("SELECT 
			m.id_number as plate_number,
			l.name as passanger_type_name,
			b.ticket_number,
			c.booking_code,
			k.created_on as sail_date,
			j.name as ship_class_name,
			i.name as dock_name,
			h.name as port_destination,
			g.name as port_origin,
 			f.name as service_name,c.id_number, c.name as passanger_name,c.city,c.age, c.gender, d.name as ship_name,b.boarding_code, b.ticket_number,  a.* 
 			from app.t_trx_open_boarding a
			left join app.t_trx_boarding_vehicle b on a.boarding_code=b.boarding_code
			left join app.t_trx_booking_vehicle m on b.ticket_number=m.ticket_number
			left join app.t_trx_booking_passanger c on m.booking_code=c.booking_code
			left join app.t_mtr_ship d on a.ship_id=d.id
			left join app.t_trx_booking e on c.booking_code=e.booking_code
			left join app.t_mtr_service f on e.service_id=f.id
			left join app.t_mtr_port g on e.origin=g.id
			left join app.t_mtr_port h on e.destination=h.id
			left join app.t_mtr_dock i on a.dock_id=i.id
			left join app.t_mtr_ship_class j on a.ship_class=j.id
			left join app.t_trx_sail k on a.schedule_code=k.schedule_code
			left join app.t_mtr_passanger_type l on c.passanger_type_id=l.id
			{$where}
			order by l.name asc
			");
	}



	public function list_detail_vehicle($where=""){

		return $this->db->query("
			select 
			(
				select count(ba.booking_code) from app.t_trx_boarding_passanger aa
				join app.t_trx_booking_passanger ba on aa.ticket_number=ba.ticket_number
				where ba.booking_code=c.booking_code
				and aa.status=1
			) as total_manifest_vehicle,
			e.total_passanger,
			c.id_number as plate_number, l.name as golongan, b.ticket_number, c.booking_code, k.created_on as sail_date, j.name as ship_class_name,
	 		i.name as dock_name, h.name as port_destination,g.name as port_origin,
	 		f.name as service_name,d.name as ship_name,b.boarding_code, b.ticket_number,  a.* 
	 		from app.t_trx_open_boarding a
			left join app.t_trx_boarding_vehicle b on a.boarding_code=b.boarding_code
			left join app.t_trx_booking_vehicle c on b.ticket_number=c.ticket_number
			left join app.t_mtr_ship d on a.ship_id=d.id
			left join app.t_trx_booking e on c.booking_code=e.booking_code
			left join app.t_mtr_service f on e.service_id=f.id
			left join app.t_mtr_port g on e.origin=g.id
			left join app.t_mtr_port h on e.destination=h.id
			left join app.t_mtr_dock i on a.dock_id=i.id
			left join app.t_mtr_ship_class j on a.ship_class=j.id
			left join app.t_trx_sail k on a.schedule_code=k.schedule_code
			left join app.t_mtr_vehicle_class l on c.vehicle_class_id=l.id
			$where
			order by l.name asc
							 ");
	}

	public function get_ship_name($boarding_code)
	{
		return $this->db->query("select b.name as ship_name, a.* from app.t_trx_open_boarding a
								left join app.t_mtr_ship b on a.ship_id=b.id where a.boarding_code='".$boarding_code."' ");
	}

	public function select_data($table, $where="")
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

	// public function summary_boarding_passanger()
	// {
	// 	return $this->db->query("

	// 	select count(b.ship_name) as total_penumpang, b.ship_name , a.name as tipe_name 
	// ,b.ship_id, b.boarding_code, a.id
	// from app.t_mtr_passanger_type a
	// left join(
	// 	select br.boarding_code, br.ship_id, bk.passanger_type_id, sc.schedule_date,bk.gender, sp.name as ship_name 
	// 	from app.t_trx_open_boarding br
	// 	join app.t_trx_boarding_passanger bp on br.boarding_code=bp.boarding_code
	// 	join app.t_trx_booking_passanger bk on bp.ticket_number=bk.ticket_number
	// 	join app.t_trx_booking bo on bk.booking_code=bo.booking_code
	// 	join app.t_trx_schedule sc on br.schedule_code=sc.schedule_code
	// 	join app.t_mtr_ship sp on br.ship_id = sp.id
	// 	where br.boarding_code='B21905030003' and bo.service_id=1
	// ) b on a.id=b.passanger_type_id
	// where a.id not in (3) 
	// group by  b.ship_name , a.name , b.ship_id, b.boarding_code, a.id


	// 		");
	// }

	public function get_sum_penumpang($boarding_code,$gender,$type_id)
	{

		return $this->db->query(
		
			"select count(b.ship_name) as total_penumpang, b.gender, b.ship_name , a.name as tipe_name 
			,b.ship_id, b.boarding_code, a.id
			from app.t_mtr_passanger_type a
			left join(
				select br.boarding_code, br.ship_id, bk.passanger_type_id, sc.schedule_date,bk.gender, sp.name as ship_name 
				from app.t_trx_open_boarding br
				join app.t_trx_boarding_passanger bp on br.boarding_code=bp.boarding_code
				join app.t_trx_booking_passanger bk on bp.ticket_number=bk.ticket_number
				join app.t_trx_booking bo on bk.booking_code=bo.booking_code
				join app.t_trx_schedule sc on br.schedule_code=sc.schedule_code
				join app.t_mtr_ship sp on br.ship_id = sp.id
				where br.boarding_code='".$boarding_code."' 
				and bp.status=1 and bo.service_id=1
				and bk.gender  in ('".$gender."')
			) b on a.id=b.passanger_type_id
			where a.id  in (".$type_id.")  
			group by  b.ship_name , a.name , b.ship_id, b.boarding_code, a.id ,b.gender"
		);

	}

	public function get_sum_anak($boarding_code , $type_id)
	{

		return $this->db->query(
		
			"select count(b.ship_name) as total_penumpang, b.ship_name , a.name as tipe_name 
			,b.ship_id, b.boarding_code, a.id
			from app.t_mtr_passanger_type a
			left join(
				select br.boarding_code, br.ship_id, bk.passanger_type_id, sc.schedule_date,bk.gender, sp.name as ship_name 
				from app.t_trx_open_boarding br
				join app.t_trx_boarding_passanger bp on br.boarding_code=bp.boarding_code
				join app.t_trx_booking_passanger bk on bp.ticket_number=bk.ticket_number
				join app.t_trx_booking bo on bk.booking_code=bo.booking_code
				join app.t_trx_schedule sc on br.schedule_code=sc.schedule_code
				join app.t_mtr_ship sp on br.ship_id = sp.id
				where br.boarding_code='".$boarding_code."' 
				and bp.status=1 and bo.service_id=1
			) b on a.id=b.passanger_type_id
			where a.id  in (".$type_id.")  
			group by  b.ship_name , a.name , b.ship_id, b.boarding_code, a.id "
		);

	}

	public function get_sum_vehicle($boarding_code)
	{
		return $this->db->query("
			select sum(b.total_passanger) as total_penumpang, count(b.ship_name) as total_kendaraan, b.ship_name , a.name as tipe_name 
	,b.ship_id, b.boarding_code, a.id
	from app.t_mtr_vehicle_class a
	left join(
		select bo.total_passanger, br.boarding_code, br.ship_id, bk.vehicle_class_id, sc.schedule_date, sp.name as ship_name 
		from app.t_trx_open_boarding br
		join app.t_trx_boarding_vehicle bp on br.boarding_code=bp.boarding_code
		join app.t_trx_booking_vehicle bk on bp.ticket_number=bk.ticket_number
		join app.t_trx_booking bo on bk.booking_code=bo.booking_code
		join app.t_trx_schedule sc on br.schedule_code=sc.schedule_code
		join app.t_mtr_ship sp on br.ship_id = sp.id
		where br.boarding_code='".$boarding_code."' and bo.service_id=2 and bp.status=1

			) b on  a.id=b.vehicle_class_id 
			where a.status not in (-5)
			group by b.ship_name , a.name , b.ship_id, b.boarding_code, a.id
			order by id asc");
	}

	public function get_detail($boarding_code)
	{
		return $this->db->query("
		select h.name as approved_ship_name, g.name as approved_name, f.created_on as jam_berangkat, e.created_on as jam_tiba, d.name as dock_name, c.name as port_name , b.name as ship_name ,a.* from app.t_trx_open_boarding a
		left join app.t_mtr_ship b on a.ship_id=b.id
		left join app.t_mtr_port c on a.port_id=c.id
		left join app.t_mtr_dock d on a.dock_id=d.id
		left join app.t_trx_docking e on a.schedule_code=e.schedule_code
		left join app.t_trx_sail f on a.schedule_code=f.schedule_code
		left join app.t_trx_approval_port_officer g on a.schedule_code=g.schedule_code
		left join app.t_trx_approval_ship_officer h on a.schedule_code=h.schedule_code
				where a.boarding_code='".$boarding_code."'
			");
	}

	public function get_detail_passanger_vehicle($boarding_code)
	{
		return $this->db->query("
						select 
						(select gender from app.t_trx_booking_passanger
						 where ticket_number=bv.ticket_number and gender='L') as pria,
						 (select gender from app.t_trx_booking_passanger
						 where ticket_number=bv.ticket_number and gender='P') as wanita,
						bp.booking_code, b.total_passanger,
						bp.id_number, bp.name ,bp.city, bp.age, bp.gender, vc.name as vehicle_class_name,
						bh.id_number as plate_number, op.* from app.t_trx_open_boarding op
						left join app.t_trx_boarding_passanger bv on op.boarding_code=bv.boarding_code
						left join app.t_trx_booking_passanger bp on bv.ticket_number=bp.ticket_number
						left join app.t_trx_booking b on bp.booking_code=b.booking_code
						left join app.t_trx_booking_vehicle bh  on bp.booking_code=bh.booking_code
						left join app.t_mtr_vehicle_class vc on bh.vehicle_class_id=vc.id
						where bh.service_id=2 and op.boarding_code='".$boarding_code."' and bv.status=1
						and bp.id in ( select min(bp2.id) from app.t_trx_booking_passanger bp2 where bp2.booking_code=bp.booking_code )
			");
	}

	//// function lama 23-06-2020
	// public function total_dalam_kendaraan($code)
	// {
	// 	return $this->db->query("

	// 		select distinct da.booking_code, aa.boarding_code, da.total_passanger as tot_pass from app.t_trx_open_boarding aa
	// 		join app.t_trx_boarding_passanger ba on aa.boarding_code=ba.boarding_code
	// 		join app.t_trx_booking_passanger ca on ba.ticket_number=ca.ticket_number
	// 		join app.t_trx_booking da on ca.booking_code=da.booking_code
	// 		where aa.boarding_code='".$code."' and da.service_id=2 and ba.status=1
			

	// 		");
	// }

	public function total_dalam_kendaraan($code)
	{
		return $this->db->query("

				select distinct da.booking_code, aa.boarding_code, da.total_passanger as tot_pass 
				from app.t_trx_open_boarding aa
				join app.t_trx_boarding_vehicle ba on aa.boarding_code=ba.boarding_code
				join app.t_trx_booking_vehicle bv on ba.ticket_number=bv.ticket_number
				join app.t_trx_booking_passanger ca on bv.booking_code=ca.booking_code
				join app.t_trx_booking da on ca.booking_code=da.booking_code
				where aa.boarding_code='".$code."' and da.service_id=2 and ba.status=1	and ca.status <>'-5'		
			");
	}	

	public function download(){

		$dateFrom=$this->input->get("dateFrom"); 		
        $dateTo=$this->input->get("dateTo");
        // $port_origin=$this->enc->decode($this->input->get("port_origin"));
        $port_destination=$this->enc->decode($this->input->get("port_destination"));
        $search=$this->input->get("search");
        $iLike        = trim(strtoupper($this->db->escape_like_str($search)));

        if(!empty($this->session->userdata('port_id')))
		{
			$port_origin=$this->session->userdata("port_id");
		}
		else
		{
			$port_origin = $this->enc->decode($this->input->get('port_origin'));
		}

		// check apakah dia operator kapal;
		$check=$this->db->query("select * from app.t_mtr_user_ship a
							join core.t_mtr_user b on a.user_id=b.id 
							where user_id='".$this->session->userdata('id')."' and a.status=1");

		// $where = " WHERE a.status = 1 ";

		$where = " WHERE a.status is not null and (date(a.created_on) between '".$dateFrom."' and '".$dateTo."' ) ";

		// validasi jika dia operator kapal maka hanya operator kapal saja yang bisa liat kapal2nya
		if($check->num_rows()>0)
		{
			$ship_company=$check->row();
			$where .="and (h.id=".$ship_company->company_id.")";
		}

		if(!empty($port_origin))
		{
			$where .="and (e.port_id=".$port_origin.")";
		}

		if(!empty($port_destination))
		{
			$where .="and (e.destination_port_id=".$port_destination.")";
		}


		if (!empty($search)){
			$where .="and (a.boarding_code ilike '%".$iLike."%'
						or d.name ilike '%".$iLike."%'
						or f.name ilike '%".$iLike."%'
						)";	
		}

		$sql 		   = "
						select h.name, g.name as port_destination, e.sail_date, f.name as ship_name, d.name as ship_class_name ,
						c.name as dock_name, b.name as port_name, a.* from app.t_trx_open_boarding a
						join app.t_mtr_port b on a.port_id=b.id
						join app.t_mtr_dock c on a.dock_id=c.id
						join app.t_mtr_ship_class d on a.ship_class=d.id
						join app.t_trx_schedule e on a.schedule_code=e.schedule_code
						join app.t_mtr_ship f on a.ship_id=f.id
						join app.t_mtr_port g on e.destination_port_id=g.id
						join app.t_mtr_ship_company h on f.ship_company_id=h.id
						$where
						order by a.id desc
						 ";

		$query         = $this->db->query($sql);
		return $query;
	}


}
