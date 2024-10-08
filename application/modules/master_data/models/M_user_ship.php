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

class M_user_ship extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/user_ship';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$port_name= $this->input->post('port');
		$team_name= $this->input->post('team');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'username',
			1 =>'username',
			2 =>'full_name',
			3 =>'group_name',
			4 =>'ship_company_name',
			5 =>'status_user',

		);

		$order_column = $field[$order_column];

		// $where = " WHERE (a.status not in (-5) or b.status !='-5')";
		$where = " WHERE (a.status not in (-5) and b.status !='-5')";

		if(!empty($search['value']))
		{
			$where .="and (b.username ilike '%".$iLike."%' or concat(b.first_name,' ',b.last_name) ilike '%".$iLike."%' or d.name ilike '%".$iLike."%' or c.name ilike '%".$iLike."%' 
							)";
		}

		$sql 		   = "
							select b.status as status_user, d.name as group_name, c.name as ship_company_name, b.username, concat(b.first_name,' ',b.last_name) as full_name, a.* from app.t_mtr_user_ship a
							join core.t_mtr_user b on a.user_id=b.id
							left join app.t_mtr_ship_company c on a.company_id=c.id
							left join core.t_mtr_user_group d on b.user_group_id=d.id
							{$where}
						 ";

		// die($sql); exit;
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
     		// $delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

			if($row->status_user == 1){
				$row->status_user   = success_label('Aktif');
				// $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}else{
				$row->status_user   = failed_label('Tidak Aktif');
				// $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}


     		$row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

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
