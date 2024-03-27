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

class Schedule_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'pelabuhan/schedule';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateTo = $this->input->post('dateTo');
		$dateFrom = $this->input->post('dateFrom');
		$port_name= $this->input->post('port');
		$team_name= $this->input->post('team');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$field = array(
			0 =>'assignment_date',
			1 =>'team_name',
			2 =>'team_code',
			3 =>'port_name',
			4 =>'assignment_code',
			5 =>'assignment_date',
			// 6 =>'status',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) ";

		if (!empty($dateTo) and !empty($dateFrom))
		{
			$where .=" and (assignment_date between '".$dateTo."' and '".$dateFrom."' )";
		}

		if(!empty($port_name))
		{
			$where .="and (c.name='".$port_name."')";
		}

		if(!empty($team_name))
		{
			$where .="and (b.team_name='".$team_name."')";
		}
	
		if (!empty($search['value'])){
			$where .="and (a.assignment_code ilike '".$search['value']."')";	
		}

		$sql 		   = "select distinct  c.name as port_name,b.team_name,a.team_code, a.assignment_code, a.assignment_date 				from app.t_mtr_assignment_user_pos a
						left join core.t_mtr_team b on a.team_code=b.team_code
						left join app.t_mtr_port c on a.port_id=c.id
						left join core.t_mtr_user d on a.user_id=d.id
						$where
						 ";

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
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->assignment_code.'|-1'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->assignment_code.'|1'));

			$row->code =$row->assignment_code;
			$row->assignment_date=format_date($row->assignment_date);
			$code_enc=$this->enc->encode($row->assignment_code);
			$edit_url 	 = site_url($this->_module."/edit/{$code_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$code_enc}");
     		// $detail  = site_url($this->_module."/detail/{$code_enc}");

     		$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

     		// $row->actions .= generate_button_new($this->_module, 'detail', $edit_url);
     		
     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

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

	public function detail($code)
	{
		return $this->db->query("
									select concat(d.username,' - ',d.first_name,' ',d.last_name ) as full_name, c.name as port_name, b.team_name,a.* from app.t_mtr_assignment_user_pos a
									left join core.t_mtr_team b on a.team_code=b.team_code
									left join app.t_mtr_port c on a.port_id=c.id
									left join core.t_mtr_user d on a.user_id=d.id
									where a.assignment_code='".$code."' and a.status=1
									order by a.id desc
								");
	}

	public function select_data($table, $where="")
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
