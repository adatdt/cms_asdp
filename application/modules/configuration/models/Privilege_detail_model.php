<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Privilege_detail_model extends CI_Model {

	public function delete($data)
	{
		$query = $this->db->query("UPDATE core.t_mtr_privilege_detail
									set status = -5,
									updated_by = '$data[updated_by]',
									updated_on = NOW()
									where privilege_id = $data[id]");

		if($this->db->trans_status() === FALSE) 
      	{ 
        	$this->db->trans_rollback(); 
        	return false;
      	}
		else 
      	{ 
         	$this->db->trans_commit(); 
         	return true;
      	}
	}

	public function deletePrivilege($id)
	{
		$query = $this->db->query("DELETE from core.t_mtr_privilege_detail where privilege_id = $id");

		if($this->db->trans_status() === FALSE) 
      	{ 
        	$this->db->trans_rollback(); 
        	return false;
      	}
		else 
      	{ 
         	$this->db->trans_commit(); 
         	return true;
      	}
	}

}
