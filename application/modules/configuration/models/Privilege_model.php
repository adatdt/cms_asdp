<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Privilege_model extends CI_Model {
	function get_list() {
		$result = array();
		$items  = array();
		$group  = $this->input->post('group');

		$sql 	= "SELECT 
						parent_id, id, name, m.order, icon, slug, parent_id
				   FROM core.t_mtr_menu_web m
				   WHERE  status = 1 
				   ORDER BY m.order ASC
				   ";
		$query 	= $this->db->query($sql)->result();


		$sqlMenuDetail="SELECT 
							bb.name, 
							bb.id AS menu_id, 
							bb.parent_id,
							aa.id AS detail_id, dd.id AS action_id,  dd.action_name, cc.id AS privilege_id, cc.user_group_id AS user_group_id, cc.status
						FROM core.t_mtr_menu_detail_web aa
						LEFT JOIN core.t_mtr_menu_web bb ON aa.menu_id = bb.id AND bb.status = 1 
						LEFT JOIN core.t_mtr_privilege_web cc ON aa.id = cc.menu_detail_id AND cc.user_group_id = $group
						LEFT JOIN core.t_mtr_menu_action dd ON dd.id = aa.action_id AND dd.status = 1
						WHERE aa.status = 1 
						ORDER BY dd.id ASC				
		
		";

		$queryMenuDetail 	= $this->db->query($sqlMenuDetail)->result();		
		
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

		if($query){
			foreach ($dataParent as $row){		
				// $has_child 	  = $this->check_parent($row->id);
				// $searchChild 	  = array_search($row->id, $dataIdChild);
				$searchChild 	  = "";				
				foreach ($dataIdChild as  $valueChild) {
					if($valueChild == $row->id )
					{
						$searchChild = 1;
					}
				}

				$has_child 	  = $searchChild !=""?true:false ;

				if (substr(trim($row->icon), 1, 3) == 'svg') {
					$row->iconCls = 'fa fa-folder-open';
				} else {
					$row->iconCls = 'fa fa-'.$row->icon;
				}

				$row->menu_id = '<label style="padding-top: 7px;"><input type="checkbox" class="act menu" value="'.$row->id.'"></label>';
	            // $row->action  = $this->select_menu_detail($row->id,$row->parent_id,$group);
				$actionMenuDetailParent=array();
				$actionMenuDetailChild=array();

				foreach ($queryMenuDetail as $key => $value) {
					if($row->id== $value->menu_id &&  $row->parent_id==$value->parent_id  ){
						$actionMenuDetailParent[]=$value;
					}
					else
					{
						$actionMenuDetailChild[]=$value;
					}
				}

				// print_r($actionMenuDetailParent); exit;

				$row->action  = $this->select_menu_detail($row->id,$row->parent_id,$group,$actionMenuDetailParent);				

				if($has_child){
					// $row->state = 'closed';
					$row->children = $this->get_list_children($row->id,$dataChild,$actionMenuDetailChild);
				}			

				array_push($items, $row);
			}
		}
		
		$result['rows'] = $items;
		// $result['privilege'] = $this->global_model->selectById('core.t_mtr_privilege', 'group_id', $group);
		
		return $result;
	}

	function get_list_children($pi, $child,$queryMenuDetail) {
		$group  = $this->input->post('group');
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
		
		if($getChild){
			foreach ($getChild as $row){
				// $searchChild 	  = array_search($row->id, $dataIdChild);
				$searchChild 	  = "";				
				foreach ($dataIdChild as  $valueChild) {
					if($valueChild == $row->id )
					{
						$searchChild = 1;
					}
				}
				$has_child 	  = $searchChild !=""?true:false ;			
				$row->iconCls = 'fa fa-angle-double-right';


				$row->menu_id = '<label style="padding-top: 7px;"><input type="checkbox" class="act menu" value="'.$row->id.'"></label>';
	            // $row->action  = $this->select_menu_detail($row->id,$row->parent_id,$group);

				// $row->action  = $this->select_menu_detail($row->id,$row->parent_id,$group);
				$actionMenuDetailParent=array();
				$actionMenuDetailChild=array();

				foreach ($queryMenuDetail as $key => $value) {
					if($row->id== $value->menu_id &&  $row->parent_id==$value->parent_id ){
						$actionMenuDetailParent[]=$value;
					}
					else
					{
						$actionMenuDetailChild[]=$value;
					}
				}

				$row->action  = $this->select_menu_detail($row->id,$row->parent_id,$group,$actionMenuDetailParent);						
				
				if($has_child){
					// $row->state = 'closed';
					$row->children = $this->get_list_children($row->id,$dataChild,$actionMenuDetailChild);
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

	function select_action($menu_id,$group_id){
		$sql = "SELECT view, add, edit, delete, detail, approval FROM core.t_mtr_privilege_detail pd
				JOIN core.t_mtr_privilege p ON p.id = pd.privilege_id AND p.group_id = $group_id
				WHERE menu_id = $menu_id";
		return $this->db->query($sql)->result();
	}

	function privilege_detail($privilege_id){
		$sql = "SELECT id, menu_id FROM core.t_mtr_privilege_detail WHERE privilege_id  = $privilege_id";
		return $this->db->query($sql)->result();
	}

	function privilege_detail_by_group($group_id){
		$sql = "SELECT pd.id FROM core.t_mtr_privilege_detail pd
				JOIN core.t_mtr_privilege p ON p.id = pd.privilege_id WHERE p.group_id = $group_id";
		return $this->db->query($sql)->result();
	}

	function privilege_web_by_group($group_id){
		$sql = "SELECT id FROM core.t_mtr_privilege_web pd WHERE user_group_id = $group_id";
		return $this->db->query($sql)->result();
	}

	function checkBox($val,$name,$id){
		$checked = '';
		if($val == 't'){
			$checked = 'checked';
		}
		$html = '<label style="padding-top: 7px;">';
		$html .= '<input '.$checked.' type="checkbox" class="act act_'.$id.'" name="action['.$name.'_'.$id.']" value="'.$id.'"></label>';
		$html .= '<label>';

		return $html;
	}

	function select_menu_detail($menuid,$pi,$group, $result){
        $data= array();
		$html = "<div class='form-group' style='margin: auto;'>";

		if($result){
            foreach ($result as $row) {
            	$checked   = '';
            	$privilege = $row->privilege_id;

            	if($row->privilege_id == null || $row->privilege_id == ''){
            		$privilege  = 0;
            	}

            	if($row->status == 1){
            		$checked  = 'checked';
            	}

            	$action_name = strtoupper($row->action_name);

            	$html .= "<label style='padding-top: 7px;'><input type='checkbox' data-menu_id='{$row->menu_id}' data-detail_id='{$row->detail_id}' data-privilege='{$privilege}' data-status='{$row->status}' class='actions act act_{$row->menu_id}' {$checked}> {$action_name}</label> ";
            	$data[] = $html;
            }
        }
        
        $html .= "</div>";

        return $html;
	}


	function select_menu_detail_12102022($menuid,$pi,$group){
        $data= array();
		$sql = "SELECT bb.name, bb.id AS menu_id, aa.id AS detail_id, dd.id AS action_id,  dd.action_name, cc.id AS privilege_id, cc.user_group_id AS user_group_id, cc.status
				FROM core.t_mtr_menu_detail_web aa
				LEFT JOIN core.t_mtr_menu_web bb ON aa.menu_id = bb.id AND bb.status = 1 AND bb.id = $menuid
				LEFT JOIN core.t_mtr_privilege_web cc ON aa.id = cc.menu_detail_id AND cc.user_group_id = $group
				LEFT JOIN core.t_mtr_menu_action dd ON dd.id = aa.action_id AND dd.status = 1
				WHERE aa.status = 1 AND bb.parent_id = $pi
				ORDER BY dd.id ASC";
				// die($sql);
		$html = "<div class='form-group' style='margin: auto;'>";
		$result = $this->db->query($sql)->result();

		if($result){
            foreach ($result as $row) {
            	$checked   = '';
            	$privilege = $row->privilege_id;

            	if($row->privilege_id == null || $row->privilege_id == ''){
            		$privilege  = 0;
            	}

            	if($row->status == 1){
            		$checked  = 'checked';
            	}

            	$action_name = strtoupper($row->action_name);

            	$html .= "<label style='padding-top: 7px;'><input type='checkbox' data-menu_id='{$row->menu_id}' data-detail_id='{$row->detail_id}' data-privilege='{$privilege}' data-status='{$row->status}' class='actions act act_{$row->menu_id}' {$checked}> {$action_name}</label> ";
            	$data[] = $html;
            }
        }
        
        $html .= "</div>";

        return $html;
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

    function updateData($table, $data, $key){
        if ($this->input->is_cli_request()){
            $user = 0;
        }else{
            $user = $this->session->userdata('id');
        }

        $data['updated_by'] = $user;
        $data['updated_on'] = date('Y-m-d H:i:s');

        $this->db->where($key,$data[$key]);
        unset($data[$key]);
        $this->db->update($table,$data);

    }

    function insertBatch($table, $arr){
        if ($this->input->is_cli_request()){
            $user = 0;
        }else{
            $user = $this->session->userdata('id');
        }

        $data = array();
        foreach ($arr as $key => $value) {
            $value['created_by'] = $user;
            $value['created_on'] = date('Y-m-d H:i:s');

            $data[] = $value;
        }

        $this->db->insert_batch($table, $data);
    }

    function updateBatch($table, $arr, $id){
        if ($this->input->is_cli_request()){
            $user = 0;
        }else{
            $user = $this->session->userdata('id');
        }

        $data = array();
        foreach ($arr as $key => $value) {
            $value['updated_by'] = $user;
            $value['updated_on'] = date('Y-m-d H:i:s');

            $data[] = $value;
        }

        $this->db->update_batch($table, $data, $id);

    } 

    function getDetailMenu($id)
    {

    	return $this->db->query("
    		SELECT
    			mn.name, 
    			ac.action_name,
    			md.access_cloud,
    			md.access_local  
    		from core.t_mtr_menu_detail_web md
	    	left join core.t_mtr_menu_web mn on md.menu_id=mn.id
	    	left join core.t_mtr_menu_action ac on md.action_id=ac.id
	    	where md.id={$id}");
    }    

}
