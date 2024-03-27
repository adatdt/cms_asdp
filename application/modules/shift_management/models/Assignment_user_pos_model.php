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

class Assignment_user_pos_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'shift_management/assignment_user_pos';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateTo = $this->input->post('dateTo');
		$dateFrom = $this->input->post('dateFrom');
		
		$team_name= $this->input->post('team');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));



		$field = array(
			0 =>'assignment_date',
			1 =>'assignment_code',
			2 =>'assignment_date',
			3 =>'team_code',
			4 =>'team_name',
			5 =>'port_name',
			6 =>'shift_name',
			7 =>'status',
		);


		$get_identity_app=$this->select_data("app.t_mtr_identity_app","")->row();

		if($get_identity_app->port_id==0)
		{
			if(empty($this->session->userdata('port_id')))
			{
				$port_id= $this->enc->decode($this->input->post('port'));
			}
			else
			{
				$port_id=$this->session->userdata('port_id');
			}
		}
		else
		{
			$port_id=$get_identity_app->port_id;	
		}


		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) ";

		if (!empty($dateTo) and !empty($dateFrom))
		{
			$where .=" and (assignment_date between '".$dateTo."' and '".$dateFrom."' )";
		}

		if(!empty($port_id))
		{
			$where .="and (a.port_id='".$port_id."')";
		}

		if(!empty($team_name))
		{
			$where .="and (b.team_name='".$team_name."')";
		}
	
		if (!empty($search['value'])){
			$where .="and (a.assignment_code ilike '".$search['value']."')";	
		}

		$sql 		   = "select distinct e.shift_name,a.status, c.name as port_name,b.team_name,a.team_code, a.assignment_code,
							a.assignment_date from app.t_trx_assignment_user_pos a
							left join core.t_mtr_team b on a.team_code=b.team_code
							left join app.t_mtr_port c on a.port_id=c.id
							left join core.t_mtr_user d on a.user_id=d.id
							left join app.t_mtr_shift e on a.shift_id=e.id
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
			$code_enc=$this->enc->encode($row->assignment_code);
			$edit_url 	 = site_url($this->_module."/edit/{$code_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$code_enc}");
     		// $detail  = site_url($this->_module."/detail/{$code_enc}");

     		$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

     		// $row->actions .= generate_button_new($this->_module, 'detail', $edit_url);

     		$row->status==2?$row->label=failed_label('Tutup Shift'):$row->label =success_label('Buka Shift');
     		     		
     		$row->no=$i;

	        // ambil data ob_code dari opening balance
	        $get_ob=$this->assignment_user_pos_model->select_data(" app.t_trx_opening_balance"," where assignment_code='{$row->assignment_code}'  ")->result();

	        // check apakah vm sudah melakukan opening balance
	        $check_vm_opening=$this->assignment_user_pos_model->select_data("app.t_trx_opening_balance_vm"," where assignment_code='{$row->assignment_code}'  ");	        

	        $check_error[]=0;
	        foreach ($get_ob as $key => $value)
	        {
	            $check_sell=$this->assignment_user_pos_model->select_data(" app.t_trx_sell", " where ob_code='{$value->ob_code}' ");

	            if($check_sell->num_rows()>0)
	            {
	                $check_error[]=1;
	            }
				

	            $check_checkin=$this->checkTransactionCheckin($value->ob_code)->result(); // checkin apakah dia sudah melakukan pembayaran

	           	if(count((array)$check_checkin)>0)
	            {
	                $check_error[]=1;
	            }

    
	        }

	        if(array_sum($check_error)<1 and $check_vm_opening->num_rows()<1 and $row->assignment_date>=date('Y-m-d'))
	        {

     			$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

	        }


	        $row->assignment_date=format_date($row->assignment_date);
			$rows[] = $row;
			unset($row->assignment_code);

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

	// public function detail($code)
	// {
	// 	return $this->db->query("
	// 							select concat(h.username,' - ',h.first_name,' ',h.last_name ) as cs_name,concat(f.username,' - ',f.first_name,' ',f.last_name ) as spv_name, concat(d.username,' - ',d.first_name,' ',d.last_name ) as full_name, c.name as port_name, b.team_name,a.* from app.t_trx_assignment_user_pos a
	// 							left join core.t_mtr_team b on a.team_code=b.team_code
	// 							left join app.t_mtr_port c on a.port_id=c.id
	// 							left join core.t_mtr_user d on a.user_id=d.id
	// 							left join app.t_trx_assignment_regu e on a.assignment_code=e.assignment_code 
	// 							left join core.t_mtr_user f on e.supervisor_id=f.id
	// 							left join app.t_trx_assignment_cs g on a.assignment_code=g.assignment_code 
	// 							left join core.t_mtr_user h on g.user_id=h.id
	// 							where a.assignment_code='".$code."' and a.status=1
	// 							order by a.id desc
	// 							");
	// }

	public function detail($code, $where="")
	{
		return $this->db->query("
								select e.name as group_name, concat(d.username,' - ',d.first_name,' ',d.last_name ) as full_name, c.name as port_name, b.team_name,a.* from app.t_trx_assignment_user_pos a
								left join core.t_mtr_team b on a.team_code=b.team_code
								left join app.t_mtr_port c on a.port_id=c.id
								left join core.t_mtr_user d on a.user_id=d.id
								left join core.t_mtr_user_group e on d.user_group_id=e.id
								where a.assignment_code='".$code."'
								$where
								order by d.username asc
								");
	}

	public function detail_cs($code, $where="")
	{
		return $this->db->query("
								select e.name as group_name, concat(d.username,' - ',d.first_name,' ',d.last_name ) as full_name, c.name as port_name, b.team_name,a.* from app.t_trx_assignment_cs a
								left join core.t_mtr_team b on a.team_code=b.team_code
								left join app.t_mtr_port c on a.port_id=c.id
								left join core.t_mtr_user d on a.user_id=d.id
								left join core.t_mtr_user_group e on d.user_group_id=e.id
								where a.assignment_code='".$code."'
								$where
								order by d.username asc
								");
	}

	public function detail_ptc_stc($code, $where="")
	{
		return $this->db->query("
								select e.name as group_name, concat(d.username,' - ',d.first_name,' ',d.last_name ) as full_name, c.name as port_name, b.team_name,a.* from app.t_trx_assignment_ptc_stc a
								left join core.t_mtr_team b on a.team_code=b.team_code
								left join app.t_mtr_port c on a.port_id=c.id
								left join core.t_mtr_user d on a.user_id=d.id
								left join core.t_mtr_user_group e on d.user_group_id=e.id
								where a.assignment_code='".$code."'
								$where
								order by d.username asc
								");
	}

	public function detail_vertifikator($code, $where="")
	{
		return $this->db->query("
								select 
									e.name as group_name, 
									concat(d.username,' - ',d.first_name,' ',d.last_name ) as full_name, 
									c.name as port_name, 
									b.team_name,
									a.* 
								from app.t_trx_assignment_verifier a
								left join core.t_mtr_team b on a.team_code=b.team_code
								left join app.t_mtr_port c on a.port_id=c.id
								left join core.t_mtr_user d on a.user_id=d.id
								left join core.t_mtr_user_group e on d.user_group_id=e.id
								where a.assignment_code='".$code."'
								$where
								order by d.username asc
								");
	}	

	public function detailUserComand($code, $where="")
	{
		return $this->db->query("
								select 
									e.name as group_name, 
									concat(d.username,' - ',d.first_name,' ',d.last_name ) as full_name, 
									c.name as port_name, 
									b.team_name,
									a.* 
								from app.t_trx_assignment_command_center a
								left join core.t_mtr_team b on a.team_code=b.team_code
								left join app.t_mtr_port c on a.port_id=c.id
								left join core.t_mtr_user d on a.user_id=d.id
								left join core.t_mtr_user_group e on d.user_group_id=e.id
								where a.assignment_code='".$code."'
								$where
								order by d.username asc
								");
	}
	
	public function detailDeviceComand($code,$shift_code)
	{
		return $this->db->query("
			
			select 
				b.terminal_name,
				a.terminal_code,
				a.*
				from
					app.t_mtr_pairing_device_command_center a
				left join app.t_mtr_device_terminal b on a. terminal_code = b.terminal_code 

			where a.assignment_code='".$code."' and a.shift_code ='".$shift_code."'  and a.status = 1
		");
	}

	public function get_user($where='')
	{
		$qry=" 
		
			SELECT concat(username,' - ',first_name,' ',last_name) as full_name ,a.* from core.t_mtr_user a
			$where
			
		";
		// print_r($qry);exit;
		return $this->db->query($qry);
	}

	public function detail_user($code)
	{
		return $this->db->query("
									select concat(f.username,' - ',f.first_name,' ',f.last_name ) as spv_name, concat(d.username,' - ',d.first_name,' ',d.last_name ) as full_name, c.name as port_name, b.team_name,a.* from app.t_trx_assignment_user_pos a
									left join core.t_mtr_team b on a.team_code=b.team_code
									left join app.t_mtr_port c on a.port_id=c.id
									left join core.t_mtr_user d on a.user_id=d.id
									left join app.t_trx_assignment_regu e on a.assignment_code=e.assignment_code 
									left join core.t_mtr_user f on e.supervisor_id=f.id
									where a.assignment_code='".$code."' and a.status=1 and d.user_group_id=4
									order by a.id desc
								");
	}

	public function checkTransactionCheckin($ob_code)
	{
		return $this->db->query("
			select created_by from app.t_trx_check_in
			where created_by='{$ob_code}'
			union all
			select created_by from app.t_trx_check_in_vehicle
			where created_by='{$ob_code}'
		");
	}

	public function get_shift($port_id)
	{
		return $this->db->query("
						select b.shift_name,a.* from app.t_mtr_shift_time a
						join  app.t_mtr_shift b on a.shift_id=b.id
						where a.port_id={$port_id} and a.status=1 order by id desc
					");
	}

	public function getDropdown($where='', $selected="")
	{
		// $data = $this->select_data($table, " " )->result();
		$data = $this->db->query("SELECT concat(username,' - ',first_name,' ',last_name) as full_name ,a.id from core.t_mtr_user a $where")->result();

		
		// print_r($data);exit;
		$returnData[""]="Pilih";
		$getSelected ="";
		foreach ($data as $key => $value) {
			$encodeId =$value->id;
			if($selected == $value->id)
			{
				$encodeId = $value->id;
				$getSelected = $encodeId;
			}
			$returnData[$encodeId]=$value->full_name;
		}

		if(!empty($selected))
		{
			return  array("data"=>$returnData,"selected"=>$getSelected );
			exit;
		}

		// print_r($returnData);exit;

		return  $returnData;
	}

	public function select_data($table, $where="")
	{
		return $this->db->query("select * from $table $where");
	}

	public function countData($table, $where="")
	{
		return $this->db->query("select id from $table $where");
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