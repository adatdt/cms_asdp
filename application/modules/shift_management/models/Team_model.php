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

class Team_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'shift_management/team';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$port=$this->enc->decode($this->input->post('port'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$field = array(
			0 =>'id',
			1 =>'team_name',
			2 =>'team_code',
			3 =>'port_name',
			4 =>'status',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5)";
	
		if (!empty($search['value'])){
			$where .="and (a.team_name ilike '%".$iLike."%' or b.name ilike '%".$iLike."%'
							or a.team_code ilike '%".$iLike."%'
							)";
		}

		if (!empty($port))
		{
			$where.=" and (a.port_id='".$port."')";
		}

		$sql 		   = "select b.name as port_name, a.* from core.t_mtr_team a
							join app.t_mtr_port b on a.port_id=b.id  {$where}";

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
			$nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
     		$aktif       = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1|'.$row->team_name."|".$row->port_id));

			$row->id 	 = $this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$row->id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$row->id}");

     		$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

			if($row->status == 1){
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', '."'".$nonaktif."'".')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', '."'".$aktif."'".')" title="Aktif"> <i class="fa fa-check"></i> </button> ');
			}
     		
     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

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

	public function get_byCode($code){
		$query = $this->db->query("SELECT port_code FROM t_mtr_port WHERE port_code = '$code' and status = 1");
		return $query->row();
	}
	
	public function get_prov(){
		return $this->db->query("select * from public.t_mtr_province order by name asc")->result();
	}
	
	public function get_area($id){
		return $this->db->query("select * from public.t_mtr_city where province_id='$id' order by name asc")->result();
	}
	
	public function get_district($id){
		return $this->db->query("select * from public.t_mtr_district where city_id='$id' order by name asc")->result();
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
