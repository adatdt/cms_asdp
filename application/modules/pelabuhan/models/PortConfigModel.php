<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : portConfigModel
 * -----------------------
 *
 * @author     Robai <adatdt@gmail.com>
 * @copyright  2023
 *
 */

class portConfigModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'pelabuhan/portConfig';
	}

    public function portList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);

		$searchData=$this->input->post("searchData");
		$searchName=$this->input->post("searchName");
		$port=$this->enc->decode($this->input->post("port"));
		$iLike        = trim(strtoupper(str_replace(array("'",'"'),"", $searchData) ));
		
		$field = array(
			0 =>'id',
			1 =>'config_name',
			2 =>'config_group',
			3 =>'port_name',
			4 =>'value',
			5 =>'status',
		);

		$order_column = $field[$order_column];

		$where = " WHERE cf.status not IN (-5)";
		
		if(!empty($port))
		{
			$where .=" and cf.port_id=$port ";
		}

		if(!empty($searchData))
		{
			if($searchName=='name')
			{
				$where .=" and cf.config_name ilike '%{$iLike}%' ";
			}
			else if ($searchName=='configGroup') {

				$where .=" and  cf.config_group ilike '%{$iLike}%' ";
			}
			else if($searchName=='valueData')
			{
				$where .=" and  cf.value ilike '%{$iLike}%' ";
			}
			else
			{
				$where .="";
			}
		}

		$query="
				select 
					pt.name as port_name,
					cf.status,
					cf.config_group,
					cf.config_name,
					cf.value,
					cf.id 
				from app.t_mtr_port_config cf
				join app.t_mtr_port pt on cf.port_id = pt.id 
				{$where}
		";

		$queryCount="
				select 
					count(cf.id) as count_data
				from app.t_mtr_port_config cf
				join app.t_mtr_port pt on cf.port_id = pt.id 
				{$where}
		";		

		$sql 		   = $query;
		$query         = $this->db->query($sql);
		$records_total = $this->db->query($queryCount)->row()->count_data;
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
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));


			$row->id 	 = $this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$row->id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$row->id}");

     		$row->actions  = " ";

			if($row->status == 1){
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}else{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

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
