<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Open_shift_gs_model
 * -----------------------
 *
 * @author     arif rudianto
 * @copyright  2019
 *
 */

class Open_shift_gs_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'shift_gs/open_shift_gs';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateTo = $this->input->post('dateTo');
		$dateFrom = $this->input->post('dateFrom');
		$port = $this->enc->decode($this->input->post('port'));
		$shift = $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$field = array(
			0 =>'shift_gs_date',
			1 =>'username',
			2 =>'port_name',
			3 =>'shift_name',
			4 =>'status'
		);

		$order_column = $field[$order_column];

		$where = " WHERE osg.status not in (-5) ";

		if (!empty($dateTo) and !empty($dateFrom))
		{
			$where .=" and (shift_gs_date between '".$dateTo."' and '".$dateFrom."' )";
		}

		if(!empty($port))
		{
			$where .=" and (osg.port_id='".$port."')";
		}

		if(!empty($shift))
		{
			$where .=" and (osg.shift_id='".$shift."')";
		}
	
		if (!empty($search['value'])){
			$where .=" and (osg.username ilike '".$search['value']."' or port.name ilike '".$search['value']."' or shift.name ilike '".$search['value']."')";	
		}

		$sql = "select shift_gs_date, shift_gs_code, username, port.name as port_name, shift.shift_name, osg.status
			from app.t_trx_open_shift_gs osg 
			join app.t_mtr_shift shift on osg.shift_id = shift.id
			join app.t_mtr_port port on osg.port_id = port.id
			{$where}";

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
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->shift_gs_code.'|-1'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->shift_gs_code.'|1'));

			$row->code =$row->shift_gs_code;
			$row->shift_gs_date=format_date($row->shift_gs_date);
			$code_enc=$this->enc->encode($row->shift_gs_code);
			$edit_url 	 = site_url($this->_module."/edit/{$code_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$code_enc}");
     		$close_url   = site_url($this->_module."/action_close_shift/{$code_enc}");

     		$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

     		// check apakah punya hak akses
     		$tutup=checkBtnAccess($this->_module,'close_balance');
     		if($row->status == 1){
				$row->status   = success_label('Open Shift');
				
				// jika punya hak akses akan muncul
				if($tutup)
				{
					$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
					$row->actions .='<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin tutup shift data ini ?\', \''.$close_url.'\')" title="Tutup Shift"> <i class="fa fa-close"></i> </button>';
				}
 
			}
			else
			{
				$row->status   = failed_label('Close Shift');
			}

     		// $row->actions .= generate_button_new($this->_module, 'detail', $edit_url);
     		// 
     		$row->no=$i;
     		
			$rows[] = $row;
			unset($row->shift_gs_code);

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
		return $this->db->query("select gs.shift_gs_code, shift_gs_date, gs.port_id, port.name, gs.shift_id, shift.shift_name, gs.username
							from app.t_trx_open_shift_gs gs 
							join app.t_mtr_port port on gs.port_id = port.id
							join app.t_mtr_shift shift on gs.shift_id = shift.id
							where gs.shift_gs_code='".$code."'");
	}

	// public function detail($code, $where="")
	// {
	// 	return $this->db->query("
	// 							select e.name as group_name, concat(d.username,' - ',d.first_name,' ',d.last_name ) as full_name, c.name as port_name, b.team_name,a.* from app.t_trx_assignment_user_pos a
	// 							left join core.t_mtr_team b on a.team_code=b.team_code
	// 							left join app.t_mtr_port c on a.port_id=c.id
	// 							left join core.t_mtr_user d on a.user_id=d.id
	// 							left join core.t_mtr_user_group e on d.user_group_id=e.id
	// 							where a.assignment_code='".$code."'
	// 							$where
	// 							order by a.id desc
	// 							");
	// }

	// public function detail_cs($code, $where="")
	// {
	// 	return $this->db->query("
	// 							select e.name as group_name, concat(d.username,' - ',d.first_name,' ',d.last_name ) as full_name, c.name as port_name, b.team_name,a.* from app.t_trx_assignment_cs a
	// 							left join core.t_mtr_team b on a.team_code=b.team_code
	// 							left join app.t_mtr_port c on a.port_id=c.id
	// 							left join core.t_mtr_user d on a.user_id=d.id
	// 							left join core.t_mtr_user_group e on d.user_group_id=e.id
	// 							where a.assignment_code='".$code."'
	// 							$where
	// 							order by a.id desc
	// 							");
	// }

	// public function get_user($where='')
	// {
	// 	return $this->db->query(" select concat(username,' - ',first_name,' ',last_name) as full_name ,a.* from core.t_mtr_user a
	// 						$where
	// 					");
	// }

	// public function detail_user($code)
	// {
	// 	return $this->db->query("
	// 								select concat(f.username,' - ',f.first_name,' ',f.last_name ) as spv_name, concat(d.username,' - ',d.first_name,' ',d.last_name ) as full_name, c.name as port_name, b.team_name,a.* from app.t_trx_assignment_user_pos a
	// 								left join core.t_mtr_team b on a.team_code=b.team_code
	// 								left join app.t_mtr_port c on a.port_id=c.id
	// 								left join core.t_mtr_user d on a.user_id=d.id
	// 								left join app.t_trx_assignment_regu e on a.assignment_code=e.assignment_code 
	// 								left join core.t_mtr_user f on e.supervisor_id=f.id
	// 								where a.assignment_code='".$code."' and a.status=1 and d.user_group_id=4
	// 								order by a.id desc
	// 							");
	// }

	public function select_data($table, $where="")
	{
		return $this->db->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->db->insert($table, $data);
	}

	public function insert_batch_data($table,$data)
	{
		$this->db->insert_batch($table, $data);
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
