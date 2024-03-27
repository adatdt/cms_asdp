<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Menu_model
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Menu_model extends CI_Model {

	public function __construct() {
		parent::__construct();
        $this->_module = 'configuration/menu';
	}

	function get_list() {
		$result = array();
		$items  = array();

		$sql 	= "SELECT 
					(select pw.menu_id from core.t_mtr_privilege_web pw where pw.menu_id= m.id and status=1 limit 1 ) as idprivilege,
					parent_id, id, name, m.order, icon, slug
				   FROM core.t_mtr_menu_web m
				   WHERE  status = 1 
				   ORDER BY m.order ASC";
		$query 	= $this->db->query($sql)->result();

		$dataParent=array();
		$dataChild=array();
		$dataIdChild=array();
		foreach ($query as $key => $value) {
			if($value->parent_id==0 or $value->parent_id==null or $value->parent_id=="" )
			{
				$dataParent[]=$value;
			}
			else
			{
				$dataChild[]=$value;
				$dataIdChild[]=$value->parent_id;
			}
		}

		// print_r(array_unique($dataIdChild)); exit;

		$status    = '<span class="label bg-green">status</span>';
        $nonstatus = '<span class="label bg-red">Not status</span>';
		
		$getBtnEdit = generate_button_new($this->_module, 'edit', $this->_module."/edit/");
		$getBtnDelete = generate_button_new($this->_module, 'delete', $this->_module."/delete/");

		if($dataParent){
			foreach ($dataParent as $row){
				// $searchChild 	  = array_search($row->id, $dataIdChild);
				// $has_child 	  = $searchChild !=""?true:false ;
				$searchChild = 0;
				foreach (array_unique($dataIdChild) as $keyIChild => $valueIdChild) {
					if($row->id==$valueIdChild)
					{
						$searchChild +=1;
					}
				}
				$has_child 	  = $searchChild >0?true:false ;					
				
				if (substr(trim($row->icon), 1, 3) == 'svg') {
					$row->iconCls = 'fa fa-folder-open';
				} else {
					$row->iconCls = 'fa fa-' . $row->icon;
				}
				$id 	   	  = $this->enc->encode($row->id);
	     		$edit_url     = site_url($this->_module."/edit/{$id}");
	     		$delete_url   = site_url($this->_module."/action_delete/{$id}");

	            if($has_child){
					// $row->state = 'closed';
					$row->children = $this->get_list_children($row->id, $dataChild,$getBtnEdit,$getBtnDelete);
				}

				if(!empty($getBtnEdit))
				{
					$row->action = '<button onclick="showModal(\''.$edit_url.'\')" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-pencil"></i></button> ';
				}

	            if(strtolower($row->slug) != $this->_module && strtolower($row->slug) != 'privilege'){
					if(empty($row->idprivilege)){
						if(!empty($getBtnDelete)){
							$row->action .='<button class="btn btn-sm btn-danger" title="Hapus" onclick="delete_menu(\'Apakah Anda yakin menghapus data ini ?\', \''.$delete_url.'\')" title="Hapus"> <i class="fa fa-trash-o"></i> </button> ';
						}
					}
	            }
					
				array_push($items, $row);
			}
		}
		
		// print_r($items); exit;

		$result["rows"] = $items;
		
		return $result;
	}

	function get_list_children($pi, $child,$getBtnEdit,$getBtnDelete) {
		$items  = array();
		$getChild=array();
		$dataChild=array();
		$dataIdChild=array();
		foreach ($child as $key => $value) {
			if($value->parent_id==$pi)
			{
				$getChild[]=$value;
			}
			else
			{
				$dataChild[]=$value;
				$dataIdChild[]=$value->parent_id;
			}
		}

		$status    = '<span class="label bg-green">status</span>';
        $nonstatus = '<span class="label bg-red">Not status</span>';
		
		if($getChild){
			foreach ($getChild as $row){
				// $searchChild 	  = array_search($row->id, $dataIdChild);
				// $has_child 	  = $searchChild !=""?true:false ;		
				
				$searchChild = 0;
				foreach (array_unique($dataIdChild) as $keyIChild => $valueIdChild) {
					if($row->id==$valueIdChild)
					{
						$searchChild +=1;
					}
				}
				$has_child 	  = $searchChild >0?true:false ;	
								
				$row->iconCls = 'fa fa-angle-double-right';
				$id 	   	  = $this->enc->encode($row->id);
	     		$edit_url     = site_url("configuration/menu/edit/{$id}");
	     		$delete_url   = site_url("configuration/menu/action_delete/{$id}");

	            if($has_child){
					// $row->state = 'closed';
					$row->children = $this->get_list_children($row->id, $dataChild,$getBtnEdit,$getBtnDelete);
				}

				if(!empty($getBtnEdit))
				{
					$row->action = '<button onclick="showModal(\''.$edit_url.'\')" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-pencil"></i></button> ';
				}

	            if(strtolower($row->slug) != $this->_module && strtolower($row->slug) != 'privilege'){
	            	if(empty($row->idprivilege)){ // jika tida ada privilege maka bisa di apus

						if(!empty($getBtnDelete)){
							$row->action .='<button class="btn btn-sm btn-danger" title="Hapus" onclick="delete_menu(\'Apakah Anda yakin menghapus data ini ?\', \''.$delete_url.'\')" title="Hapus"> <i class="fa fa-trash-o"></i> </button> ';
						}						
		            }
	            }
					
				array_push($items, $row);
			}
		}
		
		return $items;
	}

	function check_parent($id){
		$sql   = "SELECT * FROM core.t_mtr_menu_web WHERE parent_id = $id";
		$query = $this->db->query($sql);
		$row   = $query->num_rows();

		return $row > 0 ? true : false;
	}

	function checkOrder($parent,$order,$id=''){
		if($parent == 0){
			$where = "(parent_id IS NULL OR parent_id = 0)";
		}else{
			$where = "parent_id = {$parent}";
		}

		if($id != ''){
			$where2 = "AND id != {$id}";
		}else{
			$where2 = '';
		}

		$sql = "SELECT * FROM core.t_mtr_menu_web m
				WHERE {$where} AND m.order = $order AND status = 1 {$where2}";
		$query = $this->db->query($sql);
		return $query->result();
	}

	function select_menu_detail($menuid){
        $data= array();
		$sql = "SELECT id, action_id
				FROM core.t_mtr_menu_detail_web 
				WHERE status = 1 AND menu_id = $menuid 
				ORDER BY action_id ASC";

		$result = $this->db->query($sql)->result();

		if($result){
            foreach ($result as $row) {
                $data[$row->id] = $row->action_id;
            }
        }

        return $data;
	}

	function select_menu_privilege($menuid,$menudetail){
		$this->db->where('menu_id', $menuid);
		$this->db->where_in('menu_detail_id', $menudetail);
		return $this->db->get('core.t_mtr_privilege_web')->result();
	}

	function deletePrivilege($menuid,$menudetail){
		$this->db->where('menu_id', $menuid);
		$this->db->where_in('menu_detail_id', $menudetail);
		$this->db->delete('core.t_mtr_privilege_web');
	    return $this->db->affected_rows();
	}
}