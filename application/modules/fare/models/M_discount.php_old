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

class M_discount extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'fare/discount';
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
			1 =>'schema_code',
			2 =>'schema_name',
			3 =>'discount_code',
			4 =>'port_name',
			5 =>'description',
			6 =>'start_date',
			7 =>'end_date',
			8 =>'pos_passanger',
			9 =>'pos_vehicle',
			10 =>'vm',
			11 =>'mobile',
			12 =>'web',
			13 =>'b2b',
			14 =>'ifcs',
			15 =>'status',

		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status !='-5' ";

		if(!empty($search['value']))
		{
			$where .="and (a.schema_code ilike '%".$iLike."%'
							or a.discount_code ilike '%".$iLike."%'
							or a.description ilike '%".$iLike."%'
							or b.description ilike '%".$iLike."%'
							)";
		}


		$sql 		   = "
							SELECT d.name as port_name, c.discount_code, b.slug, b.description as schema_name, a.* from app.t_mtr_discount a
							left join app.t_mtr_discount_schema b on a.schema_code=b.schema_code 
							left join 
							(select distinct discount_code, port_id from app.t_mtr_discount_detail_port) 
							c on a.discount_code=c.discount_code
							left join app.t_mtr_port d on c.port_id=d.id
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
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|-1|non aktif'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1|aktif'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$this->enc->encode($row->discount_code)}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions  ="";

			if($row->status == 1){
				$row->status   = success_label('Aktif');
				$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}else{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
			} 

			$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

			$check=checkBtnAccess($this->_module,'detail');

			if($check)
			{

				if($row->slug=='fare_update')
				{
					$row->actions .="<a href='".site_url()."fare/discount/detail1/".$this->enc->encode($row->discount_code)."' class='btn btn-primary btn-sm' title='Detail Manifest'> <i class='fa fa-search-plus'></i> </a> ";	

				}
				else
				{

					$row->actions .="<a href='".site_url()."fare/discount/detail2/".$this->enc->encode($row->discount_code)."' class='btn btn-primary btn-sm' title='Detail Manifest'> <i class='fa fa-search-plus'></i> </a> ";	

				}

			}




			// $row->actions  .= generate_button_new($this->_module, 'detail', $detail_url);


			$true='<span class="label label-success"><i class="fa fa-check-circle"></i><span></span>';
			$false='<span class="label label-danger"><i class="fa fa-times-circle"></i><span></span>';	

			$row->pos_passanger=='t'?$row->pos_passanger=$true:$row->pos_passanger=$false;
			$row->pos_vehicle=='t'?$row->pos_vehicle=$true:$row->pos_vehicle=$false;
			$row->vm =='t'?$row->vm=$true:$row->vm=$false;
			$row->mobile=='t'?$row->mobile=$true:$row->mobile=$false;
			$row->web=='t'?$row->web=$true:$row->web=$false;
			$row->b2b=='t'?$row->b2b=$true:$row->b2b=$false;
			$row->ifcs=='t'?$row->ifcs=$true:$row->ifcs=$false;

			empty($row->start_date)?$row->start_date="":$row->start_date=format_date($row->start_date);
			empty($row->end_date)?$row->end_date="":$row->end_date=format_date($row->end_date);

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

    public function fare_passanger(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		
		$ship_class= $this->enc->decode($this->input->post('ship_class'));
		$discount_code= $this->enc->decode($this->input->post('discount_code'));
		$port_destination= $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
			1 =>'discount_code',
			2 =>'origin_name',
			3 =>'destination_name',
			4 =>'type_name',
			5 =>'entry_fee',
			6=>'dock_fee',
			7 =>'ifpro_fee',
			8 =>'responsibility_fee',
			9 =>'insurance_fee',
			10 =>'trip_fee',
			11 =>'fare',
			12 =>'ship_class_name',

		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) and (a.discount_code='{$discount_code}')";

		if(!empty($search['value']))
		{
			$where .="and (c.name ilike '%".$iLike."%'
							or d.name ilike '%".$iLike."%'
							or e.name ilike '%".$iLike."%'
							or f.name ilike '%".$iLike."%'
							)";
		}

		if (!empty($ship_class))
		{
			$where .= "and (a.ship_class=".$ship_class.")";
		}

		$sql 		   = "select f.name as ship_class_name, e.name as type_name, c.name as origin_name, d.name as
							destination_name, a.* from 
							app.t_mtr_discount_fare_passanger a
							left join app.t_mtr_rute b on a.rute_id=b.id
							left join app.t_mtr_port c on b.origin=c.id
							left join app.t_mtr_port d on b.destination=d.id
							left join app.t_mtr_passanger_type e on a.passanger_type=e.id
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
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|-1|non aktif'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1|aktif'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions  = "";
     		$row->fare=idr_currency($row->fare);

			if($row->status == 1){
				$row->status   = success_label('Aktif');
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}else{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
			}

			$row->entry_fee=idr_currency($row->entry_fee);
			$row->dock_fee=idr_currency($row->dock_fee);
			$row->ifpro_fee=idr_currency($row->ifpro_fee);
			$row->responsibility_fee=idr_currency($row->responsibility_fee);
			$row->insurance_fee=idr_currency($row->insurance_fee);
			$row->trip_fee=idr_currency($row->trip_fee); 


     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

			$rows[] = $row;
			unset($row->assignment_code);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	public function fare_vehicle(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		
		$discount_code= $this->enc->decode($this->input->post('discount_code'));
		$ship_class= $this->enc->decode($this->input->post('ship_class'));
		$port_destination= $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
			1 =>'discount_code',
			2 =>'origin_name',
			3 =>'destination_name',
			4 =>'type_name',
			5 =>'entry_fee',
			6=>'dock_fee',
			7 =>'ifpro_fee',
			8 =>'responsibility_fee',
			9 =>'insurance_fee',
			10 =>'trip_fee',
			11 =>'fare',
			12 =>'ship_class_name',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) and ( a.discount_code='{$discount_code}' )";

		if(!empty($search['value']))
		{
			$where .="and (c.name ilike '%".$iLike."%'
							or d.name ilike '%".$iLike."%'
							or e.name ilike '%".$iLike."%'
							or f.name ilike '%".$iLike."%'
							)";
		}

		if(!empty($ship_class))
		{
			$where .="and (a.ship_class =".$ship_class.")";
		}

		$sql 		   = " select f.name as ship_class_name, e.name as type_name, c.name as origin_name, d.name as 
							destination_name, a.* from 
							app.t_mtr_discount_fare_vehicle a left join app.t_mtr_rute b on a.rute_id=b.id
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
			'data'           => $rows
		);
	}

	public function payment_type(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		
		$discount_code= $this->enc->decode($this->input->post('discount_code'));
		$ship_class= $this->enc->decode($this->input->post('ship_class'));
		$port_destination= $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
			1 =>'discount_code',
			2 =>'payment_type',
			3 =>'value',
			4 =>'value_type',

		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) and ( a.discount_code='{$discount_code}' )";

		if(!empty($search['value']))
		{
			$where .="and (
							a.payment_type ilike '%{$iLike}%'
							a.discount_code ilike '%{$iLike}%'
						)";
		}

		$sql 		   = " select b.name as value_type_name, a.* from app.t_mtr_discount_detail a
							left join app.t_mtr_discount_value_type b on a.value_type=b.id
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


	public function get_route($order_by='')
	{
		return $this->db->query("select concat(b.name,' - ',c.name) as route_name, a.* from app.t_mtr_rute a 
								left join app.t_mtr_port b on a.origin=b.id
								left join app.t_mtr_port c on a.destination=c.id $order_by ");
	}

	public function get_detail_port($where)
	{

		return $this->db->query(" select concat(d.name,'-',e.name) as route_name, b.name as port_name, a.* from app.t_mtr_discount_detail_port a
								left join app.t_mtr_port b on a.port_id=b.id
								left join app.t_mtr_rute c on a.rute_id=c.id
								left join app.t_mtr_port d on c.origin=d.id
								left join app.t_mtr_port e on c.destination=e.id $where ");
	}

	public function get_fare_passanger($route_id, $ship_class)
	{
		return $this->db->query("select c.name as passanger_type_name,b.name as ship_class_name, a.* from app.t_mtr_fare_passanger a
						left join app.t_mtr_ship_class b on a.ship_class=b.id
						left join app.t_mtr_passanger_type c on a.passanger_type=c.id where a.ship_class={$ship_class} and a.status=1 and a.rute_id={$route_id} order by id desc
						");
	}

	public function get_fare_vehicle($route_id, $ship_class)
	{
		return $this->db->query("select c.name as vehicle_class_name,b.name as ship_class_name, a.* from app.t_mtr_fare_vehicle a	
							left join app.t_mtr_ship_class b on a.ship_class=b.id
							left join app.t_mtr_vehicle_class c on a.vehicle_class_id=c.id where
							 a.ship_class={$ship_class} and a.status=1 and a.rute_id={$route_id} order by id desc
						");
	}

	public function get_discount_fare_passanger($discount_code, $ship_class)
	{
		return $this->db->query("select c.name as passanger_type_name,b.name as ship_class_name, a.* from app.t_mtr_discount_fare_passanger a
						left join app.t_mtr_ship_class b on a.ship_class=b.id
						left join app.t_mtr_passanger_type c on a.passanger_type=c.id where a.ship_class={$ship_class} and a.status=1 and a.discount_code='{$discount_code}' order by id desc
						");
	}

	public function get_discount_fare_vehicle($discount_code, $ship_class)
	{
		return $this->db->query("select c.name as vehicle_class_name,b.name as ship_class_name, a.* from app.t_mtr_discount_fare_vehicle a	
							left join app.t_mtr_ship_class b on a.ship_class=b.id
							left join app.t_mtr_vehicle_class c on a.vehicle_class_id=c.id where
							 a.ship_class={$ship_class} and a.status=1 and a.discount_code='{$discount_code}' order by id desc
						");
	}


	public function get_schema($discount_code)
	{
		return $this->db->query("select b.slug, a.*  from app.t_mtr_discount a
							left join app.t_mtr_discount_schema b on a.schema_code=b.schema_code
							where a.discount_code='{$discount_code}' ");
	}

	public function select_data($table, $where='')
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
