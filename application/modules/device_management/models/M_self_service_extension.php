<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * @author      <dayungjaya.nutech@gmail.com>
 * @copyright  2024
 *
*/

class M_self_service_extension extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module   = 'device_management/self_service_extension';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$port = $this->enc->decode($this->input->post('port'));
		$iLike        = trim($this->db->escape_like_str($searchData));
		
		$field = array(
			0 =>'id',
			1 =>'status',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5)";

		if(!empty($port))
		{
			$where .= " and a.port_id = ".$port;
		}

		if(!empty($searchData))
		{
			if($searchName=="usernamePhone")
			{
				$where .= " and a.username_phone ilike  '%".$iLike."%'   ESCAPE '!' ";
			}
			else if($searchName=="extensionPhone")
			{
				$where .= " and a.extension_phone ilike  '%".$iLike."%'  ESCAPE '!' ";
			}
			else if($searchName=="used")
			{
				$where .= " and a.used_by ilike  '%".$iLike."%'  ESCAPE '!' ";
			}

		}		

		$sql 		   = "select b.name as port_name , a.* 
                            from app.t_mtr_phone_extension a 
                            left join app.t_mtr_port b on a.port_id=b.id  {$where}";
		
        $query        = $this->db->query($sql);
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
			$nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1|'));

			$row->id 	 = $this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$row->id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$row->id}");
			 
			// yang bisa di hapus dan edit hanya data yang belum terpairing 
			 $row->actions ="";
			 if($row->is_used == 0)
			 {
				 $row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				 $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

			 }

			 /*
			if($row->status == 1){
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', '."'".$nonaktif."'".')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', '."'".$aktif."'".')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
			}
			*/
     		
     		$row->no=$i;
     		

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
