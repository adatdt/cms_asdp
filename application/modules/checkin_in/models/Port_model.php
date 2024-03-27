<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Port_model extends MY_Model
{
	protected $_table = 't_mtr_port';

    public function portList()
    {
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		
		$field = array(
			
			
			1=>'name',
			2=>'city',
			3=>'status',

		);

		$order_column = $field[$order_column];

		$where = " where status = 1 ";
	
		if (!empty($search['value']))
		{
			$where .="and (name ilike '%".trim($search['value'])."%' or city ilike '%".trim($search['value'])."%')";
		}

		$sql="
			select * from app.t_mtr_port {$where}
		";
		
		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();

		$sql .=" ORDER BY ".$order_column." {$order_dir}";

		if($length != -1)
		{
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows = array();
		$i = ($start + 1);
		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->name=$row->name;
			$row->city=$row->city;
			$row->status = ($row->status == 1) ? 'Aktif' : 'Tidak Aktif';
			$row->actions ='
				<a class="btn-warning btn-sm" style=" color:black"> <i class="fa fa-pencil"></i> </a>&nbsp;
				<a class="btn-warning btn-sm" style=" color:black"> <i class="fa fa-trash"></i> </a>
			';
			

			$rows[] = $row;

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	public function getPortById($id)
	{
		$query = $this->db->query("
			SELECT id, port_code, port_name, port_city
			from t_mtr_port
			where id = {$id} AND status = 1;
		");

		return $query->row();
	}
	public function get_byCode($code)
	{
		$query = $this->db->query("SELECT port_code 
			from t_mtr_port
			where port_code = '$code' and status = 1 ");
		return $query->row();
	}
	// public function update($data)
	// {
	// 	$this->db->trans_begin();

	// 	$this->db->where("id", $data['id']);
	// 	unset($data['id']);
	// 	$this->db->update("t_mtr_terminal", $data);

	// 	if ($this->db->trans_status() === FALSE) {
 //        	$this->db->trans_rollback();
 //        	return false;
 //      	} else {
 //         	$this->db->trans_commit();
 //         	return true;
 //      	}
	// }

 //    public function getAllTerminal()
	// {
	// 	$query = $this->db->query("
	// 		SELECT terminal_code, terminal_name, terminal_city
	// 		FROM t_mtr_terminal
	// 		WHERE actived = 1
	// 		ORDER BY terminal_name ASC;
	// 		");

	// 	return $query->result();
	// }
	
	public function get_prov()
	{
		return $this->db->query("select * from public.t_mtr_province order by name asc")->result();
	}
	
	public function get_area($id)
	{
		return $this->db->query("select * from public.t_mtr_city where province_id='$id' order by name asc")->result();
	}
	
	public function get_district($id)
	{
		return $this->db->query("select * from public.t_mtr_district where city_id='$id' order by name asc")->result();
	}
	
	public function delete($id)
	{
		$this->db->query("delete from t_mtr_port where id=$id ");
		
		if($this->db->trans_status() === FALSE) 
        { 
            $this->db->trans_rollback(); 
            return false;
        }
        else 
        { 
            $this->db->trans_commit(); 
            return true;
        }
	}


}
