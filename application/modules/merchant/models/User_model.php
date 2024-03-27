<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : User_model
 * -----------------------
 *
 * @author     Rama <ramaleksana@gmail.com>
 * @copyright  2020
 *
 */

class User_model extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'merchant/user';
	}

	public function listData()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$field = array(
			0 => 'id',
			1 => 'username',
			2 => 'merchant_name',
			3 => 'merchant_id',
			4 => 'merchant_key',
			5 => 'merchant_prefix',
			6 => 'mitra_code',
			7 => 'status'
		);

		$order_column = $field[$order_column];

		$where = " WHERE status not IN (-5)";

		if (!empty($search['value'])) {
			$where .= "and (UPPER(username) ilike '%" . $iLike . "%' 
							or merchant_name ilike '%" . $iLike ."%' 
							or merchant_id ilike '%" . $iLike ."%' 
							or merchant_key ilike '%" . $iLike . "%' 
							or mitra_code ilike '%" . $iLike ."%' )";
		}

		$sql 		   = "SELECT * FROM app.t_mtr_merchant {$where}";
		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY " . $order_column . " {$order_dir}";

		if ($length != -1) {
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$nonaktif    = site_url($this->_module . "/action_change/" . $this->enc->encode($row->id . '|0'));
			$aktif       = site_url($this->_module . "/action_change/" . $this->enc->encode($row->id . '|1'));

			$row->id 	 = $this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module . "/edit/{$row->id}");
			$delete_url  = site_url($this->_module . "/action_delete/{$row->id}");

			$row->actions  = " ";

			if ($row->status == 1) {
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \'' . $nonaktif . '\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			} else {
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \'' . $aktif . '\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}


			$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'data'           => $rows
		);
	}

	public function option_user($where = '')
	{
		return $this->db->query("SELECT u.username, u.user_group_id, um.id, um.username AS u2 FROM core.t_mtr_user u 
		LEFT JOIN app.t_mtr_merchant um ON u.username = um.username
	WHERE u.user_group_id = 29 AND um.username is null {$where}")->result();
	}

	public function select_data($table, $where)
	{
		return $this->db->query("select * from $table $where");
	}

	public function insert_data($table, $data)
	{
		$this->db->insert($table, $data);
	}

	public function update_data($table, $data, $where)
	{
		$this->db->where($where);
		$this->db->update($table, $data);
	}

	public function delete_data($table, $data, $where)
	{
		$this->db->where($where);
		$this->db->delete($table, $data);
	}
}
