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

class M_quota_vehicle extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/quota_vehicle';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$port_id= $this->enc->decode($this->input->post('port'));
		$ship_class_id= $this->enc->decode($this->input->post('ship_class'));
		$vehicle_type= $this->enc->decode($this->input->post('vehicle_type'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
			1 =>'port_name',
			2 =>'ship_class_name',
			3 =>'vehicle_type_name',
			4 =>'param_value',
			5 =>'status',			

		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) ";

		if(!empty($ship_class_id))
		{
			$where .=" and (a.ship_class=".$ship_class_id.")"; 
		}

		if(!empty($port_id))
		{
			$where .=" and (a.port_id=".$port_id.")"; 
		}

		if(!empty($vehicle_type))
		{
			$where .=" and (a.vehicle_type=".$vehicle_type.")"; 
		}

		if(!empty($search['value']))
		{
			if(is_numeric($search['value']))
			{
				$where .="and (a.param_value =".trim($search['value'])."::INTEGER )";
			}
		}

		$sql 		   = "
						select d.name as vehicle_type_name, c.name as ship_class_name, b.name as port_name, a.* from app.t_mtr_booking_quota_vehicle a
						left join app.t_mtr_port b on a.port_id=b.id
						left join app.t_mtr_ship_class c on a.ship_class=c.id
						left join app.t_mtr_vehicle_type d on a.vehicle_type =d.id
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
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|-1'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

			if($row->status == 1){
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}else{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}


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
