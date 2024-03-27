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

class M_fare_vehicle extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'fare/fare_vehicle';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		
		$ship_class= $this->enc->decode($this->input->post('ship_class'));
		$port_destination= $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		if(!empty($this->session->userdata('port_id'))) {
			$port_origin=$this->session->userdata("port_id");
		}
		else
		{
			$port_origin= $this->enc->decode($this->input->post('port_origin'));
		}
		
		$field = array(
			0 =>'id',
			1 =>'origin_name',
			2 =>'destination_name',
			3 =>'type_name',
			4 =>'entry_fee',
			5=>'dock_fee',
			6 =>'ifpro_fee',
			7 =>'responsibility_fee',
			8 =>'insurance_fee',
			9 =>'trip_fee',
			10 =>'fare',
			11 =>'ship_class_name',
			12=>'status',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) ";

		if(!empty($search['value']))
		{
			$where .="and (c.name ilike '%".$search['value']."%' or e.name ilike '%".$search['value']."%' 
							or d.name ilike '%".$search['value']."%'
							)";
		}

		if(!empty($port_origin))
		{
			$where .="and (b.origin =".$port_origin.")";
		}

		if(!empty($ship_class))
		{
			$where .="and (a.ship_class =".$ship_class.")";
		}

		if(!empty($port_destination))
		{
			$where .="and (b.destination =".$port_destination.")";
		}

		$sql 		   = " select f.name as ship_class_name, e.name as type_name, c.name as origin_name, d.name as 
							destination_name, a.* from 
							app.t_mtr_fare_vehicle a left join app.t_mtr_rute b on a.rute_id=b.id
							left join app.t_mtr_port c on b.origin=c.id
							left join app.t_mtr_port d on b.destination=d.id
							left join app.t_mtr_vehicle_class e on a.vehicle_class_id=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
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
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|-1'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->fare=idr_currency($row->fare);

     		$row->actions  = " ";

			if($row->status == 1){

				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}else{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
			}


     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
     		$row->entry_fee =idr_currency($row->entry_fee);
     		$row->dock_fee =idr_currency($row->dock_fee);
     		$row->ifpro_fee =idr_currency($row->ifpro_fee);
     		$row->responsibility_fee =idr_currency($row->responsibility_fee);
     		$row->insurance_fee =idr_currency($row->insurance_fee);
     		$row->trip_fee =idr_currency($row->trip_fee);

			$rows[] = $row;
			unset($row->assignment_code);

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

	// public function detail($code)
	// {
	// 	return $this->db->query("
	// 								select d.username, c.name as port_name, b.team_name,a.* from app.t_mtr_assignment_user_pos a
	// 								left join core.t_mtr_team b on a.team_code=b.team_code
	// 								left join app.t_mtr_port c on a.port_id=c.id
	// 								left join core.t_mtr_user d on a.user_id=d.id
	// 								where a.assignment_code='".$code."'
	// 								order by a.id desc
	// 							");
	// }

	public function get_route($order_by='')
	{
		return $this->db->query("select concat(b.name,' - ',c.name) as route_name, a.* from app.t_mtr_rute a 
								left join app.t_mtr_port b on a.origin=b.id
								left join app.t_mtr_port c on a.destination=c.id $order_by ");
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


}
