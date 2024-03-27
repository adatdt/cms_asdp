<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------

 * @author     adat <adatdt@gmail.com>
 * @copyright  2021
 *
 */

class AssessmentParamModel extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'master_data/assessmentParam';
	}

	public function dataList()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = trim($this->input->post('searchData'));
		$searchName = $this->input->post('searchName');
		$iLike        = str_replace(array("'", '"'), "", $searchData);


		$field = array(
			0 => 'id',
			1 => "type",
			2 => "title_text",
			3 => "info_text",
			4 => "group_type",
			5 => "instructions_text",
			6 => "status"
		);

		$where = " WHERE id is not null  and status !='-5' ";
		$order_column = $field[$order_column];

		if (!empty($searchData)) {
			if ($searchName == 'type') {
				$where .= " and ( type ilike '%{$iLike}%' )";
			} else if ($searchName == 'titleText') {
				$where .= " and ( title_text ilike '%{$iLike}%' )";
			} else if ($searchName == 'info') {
				$where .= " and ( info_text ilike '%{$iLike}%' )";
			} else if ($searchName == 'instruction') {
				$where .= " and ( instructions_text ilike '%{$iLike}%' )";
			} else {
				$where .= "";
			}
		}



		$sql 		   = "
							SELECT 
								* 
							from  
							app.t_mtr_assessment_param
							{$where} 
							
						 ";

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
			$id_enc = $this->enc->encode($row->id);
			$row->number = $i;
			$nonaktif    = site_url($this->_module . "/action_change/" . $this->enc->encode($row->id . '|0'));
			$aktif       = site_url($this->_module . "/action_change/" . $this->enc->encode($row->id . '|1'));

			$row->id = $row->id;
			$edit_url 	 = site_url($this->_module . "/edit/{$id_enc}");
			$delete_url  = site_url($this->_module . "/action_delete/{$id_enc}");

			$row->actions  = " ";

			if ($row->status == 1) {
				// $row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->actions  .= generate_button($this->_module, 'delete', '<button onclick="showModal2(\'' . $edit_url . '\')" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-pencil"></i></button>');
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \'' . $nonaktif . '\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			} else {
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \'' . $aktif . '\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

			$row->no = $i;
			$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

			$rows[] = $row;
			unset($row->assignment_code);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'data'           => $rows,
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
		);
	}

	public function select_data($table, $where)
	{
		return $this->db->query("select * from $table $where");
	}

	public function insert_data($table, $data)
	{
		$this->db->insert($table, $data);
	}

	public function insertGetId($table, $data)
	{
		$this->db->insert($table, $data);
		return $insert_id = $this->db->insert_id();
	}

	public function update_data($table, $data, $where)
	{
		$this->db->where($where);
		$this->db->update($table, $data);
	}

	public function delete_data($table, $where)
	{
		$this->db->where($where);
		$this->db->delete($table);
	}
}
