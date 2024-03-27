<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Masteroutletmodel extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'master_data/masteroutlet';
	}

	public function dataList()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$port_name = $this->input->post('port');
		$team_name = $this->input->post('team');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));


		$field = array(
			0 => 'id',
			1 => 'merchant_id',
			2 => 'outlet_id',
			3 => 'description',
			4 => 'status',

		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) ";

		if (!empty($search['value'])) {
			$where .= "
				and (

				b.merchant_name ilike '%" . $this->db->escape_str($search['value']) . "%' ESCAPE '!' or
				a.outlet_id ilike '%" . $this->db->escape_str($search['value']) . "%' ESCAPE '!'  or
				a.description ilike '%" . $this->db->escape_str($search['value']) . "%' ESCAPE '!'
				)
			";
		}

		$sql 		   = "
		select a.id, 
b.merchant_name, 
a.outlet_id, 
a.description, 
a.status
from app.t_mtr_outlet_merchant a
inner join app.t_mtr_merchant b on a.merchant_id = b.merchant_id
							{$where}
						 ";
		//  echo $sql;
		//  exit;

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
			// $delete_url  = site_url($this->_module . "/action_delete/{$id_enc}");

			$row->actions  = "";

			if ($row->status == 1) {

				// $row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \'' . $nonaktif . '\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			} else {
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \'' . $aktif . '\')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
			}


			$row->no = $i;
			// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

			$rows[] = $row;
			unset($row->assignment_code);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'data'           => $rows
		);
	}

	public function checkMerchantdanOutletExist($merchantid, $outletid)
	{
		$sql = "select * from app.t_mtr_outlet_merchant tmom 
		where tmom.merchant_id = '$merchantid' 
		and outlet_id = '$outletid'";

		//echo $sql;
		//exit;
		$query = $this->db->query($sql)->result();
		return $query;
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

	// public function delete_data($table,$data,$where)
	// {
	// 	$this->db->where($where);
	// 	$this->db->delete($table, $data);
	// }


}
