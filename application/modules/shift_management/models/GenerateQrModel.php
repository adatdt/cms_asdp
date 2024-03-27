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

class GenerateQrModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'shift_management/team';
	}

	public function getShift($portId){

		$qry="
			SELECT tms.id, tms.shift_name as name  from app.t_mtr_shift_time tmst 
			left join app.t_mtr_shift tms on tmst.shift_id =tms.id  and tms.status=1
			where tmst .port_id ={$portId} and tmst.status=1
			order by tms.shift_name  asc 		
		";

		return $this->db->query($qry)->result();
	}

	public function getQr($param)
	{
		if($param['userGroup']==33)// 33 user id verivikator
		{
			$qry="
			SELECT 
				ttav.shift_code ,
				ttav.assignment_date ,
				-- tmu.username ,
				concat(tmu.first_name,' ',tmu.last_name) as full_name,
				pt.name as port_name,
				st.shift_name,
				tmug.name as group_name
			from app.t_trx_assignment_verifier ttav 
			join core.t_mtr_user tmu on ttav.user_id=tmu.id 
			join core.t_mtr_user_group tmug on tmu.user_group_id =tmug.id 
			join app.t_mtr_port pt on ttav.port_id= pt.id
			join app.t_mtr_shift st on ttav.shift_id=st.id
			where ttav.assignment_date='".$param['dateFrom']."'
			and ttav.port_id='".$param['port']."'
			and ttav.shift_id='".$param['shift']."'
			--and ttav.status !='-5'
			and ttav.status =1
			";
		}
		else
		{
			$qry="
				SELECT 
					ttav.ob_code as shift_code,
					ttaup.assignment_date ,
					-- tmu.username ,
					concat(tmu.first_name,' ',tmu.last_name) as full_name,
					pt.name as port_name,
					st.shift_name,
					tmug.name as group_name
				from app.t_trx_assignment_user_pos ttaup
				join app.t_trx_opening_balance ttav  on ttaup.assignment_code =ttav.assignment_code
					and  ttaup.user_id=ttav.user_id  
					--and ttav.status !='-5'
					and ttav.status =1				
				join core.t_mtr_user tmu on ttav.user_id=tmu.id 			
				join core.t_mtr_user_group tmug on tmu.user_group_id =tmug.id 
				join app.t_mtr_port pt on ttaup.port_id= pt.id
				join app.t_mtr_shift st on ttav.shift_id=st.id
				where ttaup.assignment_date='".$param['dateFrom']."'
				and ttaup.port_id='".$param['port']."'
				and ttaup.shift_id='".$param['shift']."'
				and tmug.id ='".$param['userGroup']."'
				--and ttaup.status !='-5'
				and ttaup.status =1			
			
			";
		}
		// die($qry); exit;		

		return $this->db->query($qry)->result();
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
