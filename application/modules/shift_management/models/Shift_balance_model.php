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

class Shift_balance_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'pelabuhan/port';
	}

    public function portList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$field = array(
			1 =>'name',
			2 =>'city',
			3 =>'status',
		);

		$order_column = $field[$order_column];

		$where = " WHERE status IN (0,1)";
	
		if (!empty($search['value'])){
			$where .="and (UPPER(name) ilike '%".$iLike."%' or UPPER(city) ilike '%".$iLike."%')";
		}

		$sql 		   = "SELECT id, name, city, status FROM app.t_mtr_port {$where}";
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
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

			$row->id 	 = $this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$row->id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$row->id}");

     		$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

			if($row->status == 1){
				$row->status   = success_color('Aktif');
				$row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-warning" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}else{
				$row->status   = failed_color('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-success" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
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
}
