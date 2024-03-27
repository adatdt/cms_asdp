<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * --------------------------
 * CLASS NAME : Service_model
 * --------------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Service_model extends MY_Model{

	public function __construct() {
	    parent::__construct();
	    $this->_module = 'pelabuhan/service';
	}

	public function serviceList(){
		$start 		  = $this->input->post('start');
		$length 	  = $this->input->post('length');
		$draw 		  = $this->input->post('draw');
		$search 	  = $this->input->post('search');
		$order 		  = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir 	  = strtoupper($order[0]['dir']);
    	$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			1 => 'name',
			2 => 'status',
		);

		$order_column = $field[$order_column];
		$where = "WHERE status not IN (-5)";
	
		if (!empty($search['value'])){
			$where .="and (name ilike '%".trim($search['value'])."%')";
		}

		$sql = "SELECT id, name, status FROM app.t_mtr_service {$where}";
		
		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows = array();
		$i = ($start + 1);
		foreach ($rows_data as $row) {

			$row->number = $i;
			// $row->status = ($row->status == 1) ? success_color('Aktif') : failed_color('Tidak Aktif');
			$row->id     = $this->enc->encode($row->id);
		    $edit_url    = site_url($this->_module."/edit/{$row->id}");
		    $delete_url  = site_url($this->_module."/action_delete/{$row->id}");

		    $nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
      		$aktif = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

		    $row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

		    if($row->status == 1){
	        $row->status   = success_label('Aktif');
	        $row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
	      	}
	      	else
	      	{
		        $row->status   = failed_label('Tidak Aktif');
		        $row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
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