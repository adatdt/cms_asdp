<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ------------------------
 * CLASS NAME : Users_model
 * ------------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Users_model extends CI_Model {

	public function __construct() {
		parent::__construct();
        $this->_module = 'configuration/users';
	}

	public function userList(){
		$start        = $this->input->post('start');
		$length       = $this->input->post('length');
		$draw         = $this->input->post('draw');
		$search       = $this->input->post('search');
		$order        = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir    = strtoupper($order[0]['dir']);
		$user_group_id=$this->enc->decode($this->input->post('user_group'));
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');		
		$port=$this->enc->decode($this->input->post('port'));
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));

		$field = array(
			0=>"id",
			1=>"first_name",
			2=>"last_name",
			3=>"username",
			4=>"group_name",
			5=>"port_name",
			6=>"admin_pannel_login",
			7=>"validator_login",
			8=>"e_ktp_reader_login",
			9=>"cs_login",
			10=>"pos_login",
			11=>"verifier_login",
			12=>"status",
		);

		$order_column = $field[$order_column];

		$getWhereGroup=$this->session->userdata("group_id")==1?"":" and u.user_group_id<>1 ";

		$where = "WHERE u.status not in (-5)  ".$getWhereGroup." ";

		if(!empty($user_group_id))
		{
			$where .="and (u.user_group_id='".$user_group_id."')";
		}

		if(!empty($port))
		{
			// echo $port; exit;
			if($port == "a")
			{
				$where .="and u.port_id is null   ";
			}
			else
			{
				$where .="and u.port_id='".$port."' ";
			}
			
		}		

		if(!empty($searchData))
		{
			if($searchName=="username")
			{
				$where .= " and username ILIKE '%".$iLike."%' ESCAPE '!' " ;
			}
			else if($searchName=="firstName")
			{
				$where .= " and first_name ILIKE '%".$iLike."%' ESCAPE '!' " ;
			}
			else if($searchName=="lastName")
			{
				$where .= " and last_name ILIKE '%".$iLike."%' ESCAPE '!' " ;
			}
			else if($searchName=="usernamePhone")
			{
				$where .= " and u.username_phone ILIKE '%".$iLike."%' ESCAPE '!' " ;
			}
			else if($searchName=="extentionPhone")
			{
				$where .= " and u.extension_phone ILIKE '%".$iLike."%' ESCAPE '!' " ;
			} 
		}

		$sql = "SELECT p.name as port_name, u.*, ug.name AS group_name 
				FROM core.t_mtr_user u
				LEFT JOIN core.t_mtr_user_group ug ON ug.id = u.user_group_id 
				LEFT JOIN app.t_mtr_port p on u.port_id=p.id 
				{$where}";

		// die($sql); exit;

		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";			
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();
		$rows 	   = array();
		$i 		   = ($start + 1);

		foreach ($rows_data as $row) {
			$row->number= $i;
			$row->id 	= $this->enc->encode($row->id);
     		$edit_url 	= site_url($this->_module."/edit/{$row->id}");
     		$delete_url = site_url($this->_module."/action_delete/{$row->id}");
     		$reset_url 	= site_url($this->_module."/reset_password/{$row->id}");


		    $nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|0'));
		    $aktif       = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

     		$row->actions  ="";
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

     		if($row->status == 1)
     		{
	        	$row->status   = success_label('Aktif');
	        	$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
	        	$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
	      	}else
	      	{
	        	$row->status   = failed_label('Tidak Aktif');
	        	$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
	      	}

     		$row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-warning" title="Ganti Password" onclick="showModal(\''.$reset_url.'\')"> <i class="fa fa-lock"></i></button>');
     		
     		$row->port_name==""?$row->port_name="SEMUA PELABUHAN":$row->port_name=strtoupper($row->port_name);

    		
    		if($row->admin_pannel_login==true)
    		{
    			$row->admin_pannel_login="<i class='fa fa-check-circle text-success' style='font-size:150%' ></i>";	
    		}
    		else
    		{
    			$row->admin_pannel_login="<i class='fa fa-times-circle text-danger' style='font-size:150%'></i>";	
    		}

    		 if($row->validator_login==true)
    		{
    			$row->validator_login="<i class='fa fa-check-circle text-success' style='font-size:150%' ></i>";	
    		}
    		else
    		{
    			$row->validator_login="<i class='fa fa-times-circle text-danger' style='font-size:150%'></i>";	
    		}

    		if($row->e_ktp_reader_login==true)
    		{
    			$row->e_ktp_reader_login="<i class='fa fa-check-circle text-success' style='font-size:150%'></i>";	
    		}
    		else
    		{
    			$row->e_ktp_reader_login="<i class='fa fa-times-circle text-danger' style='font-size:150%'></i>";	
    		}

    		if($row->cs_login==true)
    		{
    			$row->cs_login="<i class='fa fa-check-circle text-success' style='font-size:150%' ></i>";	
    		}
    		else
    		{
    			$row->cs_login="<i class='fa fa-times-circle text-danger' style='font-size:150%'></i>";	
    		}

    		if($row->pos_login==true)
    		{
    			$row->pos_login="<i class='fa fa-check-circle  text-success' style='font-size:150%'></i>";	
    		}
    		else
    		{
    			$row->pos_login="<i class='fa fa-times-circle text-danger' style='font-size:150%'></i>";	
    		}

			
			if($row->verifier_login==true)
			{
				$row->verifier_login="<i class='fa fa-check-circle text-success' style='font-size:150%'></i>";	
			}
			else
			{
				$row->verifier_login="<i class='fa fa-times-circle text-danger' style='font-size:150%'></i>";	
			}	

			if($row->command_center_login==true)
			{
				$row->command_center_login="<i class='fa fa-check-circle text-success' style='font-size:150%'></i>";	
			}
			else
			{
				$row->command_center_login="<i class='fa fa-times-circle text-danger' style='font-size:150%'></i>";	
			}				
			

			$rows[] = $row;
			unset($row->id);
			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	public function select_data($table, $where="")
	{
		return $this->db->query("select * from $table $where");
	}

	public function select_data_field($table, $field, $where="")
	{
		return $this->db->query("select $field from $table $where");
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
	public function download()
	{
		return $this->db->query("select p.name as port_name, u.*, ug.name AS group_name 
		FROM core.t_mtr_user u LEFT JOIN core.t_mtr_user_group ug ON ug.id = u.user_group_id 
		LEFT JOIN app.t_mtr_port p on u.port_id=p.id 
		where u.status not in (-5)
		order by ug.name asc
		");
	}

	public function download_excel(){


		$user_group=$this->enc->decode($this->input->get("user_group"));
		$port=$this->enc->decode($this->input->post('port'));
		$searchData = $this->input->get('searchData');
		$searchName = $this->input->get('searchName');				
        $iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));


        $getWhereGroup=$this->session->userdata("group_id")==1?"":" and u.user_group_id<>1 ";

		$where = "WHERE u.status not in (-5) ".$getWhereGroup." ";

		if(!empty($user_group))
		{
			$where .="and (u.user_group_id='".$user_group."')";
		}

		if(!empty($port))
		{
			// echo $port; exit;
			if($port == "a")
			{
				$where .="and u.port_id is null   ";
			}
			else
			{
				$where .="and u.port_id='".$port."' ";
			}
			
		}		

		if(!empty($searchData))
		{
			if($searchName=="username")
			{
				$where .= " and username ILIKE '%".$iLike."%' ESCAPE '!' " ;
			}
			else if($searchName=="firstName")
			{
				$where .= " and first_name ILIKE '%".$iLike."%' ESCAPE '!' " ;
			}
			else if($searchName=="lastName")
			{
				$where .= " and last_name ILIKE '%".$iLike."%' ESCAPE '!' " ;
			}
			else if($searchName=="usernamePhone")
			{
				$where .= " and u.username_phone ILIKE '%".$iLike."%' ESCAPE '!' " ;
			}
			else if($searchName=="extentionPhone")
			{
				$where .= " and u.extension_phone ILIKE '%".$iLike."%' ESCAPE '!' " ;
			} 
		}

		$sql = "SELECT p.name as port_name, u.*, ug.name AS group_name 
				FROM core.t_mtr_user u
				LEFT JOIN core.t_mtr_user_group ug ON ug.id = u.user_group_id 
				LEFT JOIN app.t_mtr_port p on u.port_id=p.id 
				{$where}
				order by u.first_name asc
				";

		$query     = $this->db->query($sql);

		$rows=array();
		foreach ($query->result() as $row) {

     		if($row->status == 1)
     		{
	        	$row->status   = 'Aktif';
	      	}
	      	else
	      	{
	        	$row->status   ='Tidak Aktif';
	      	}

	      	$rows[]=$row;

		}

		return $rows;
	}

	public function getDataExt($param="")
	{
		$explode = explode("|",$param);
		$portId = $explode[0];
		$usernameExt = $explode[1];

		$whereIsExt ="";
		$idExt = "";
		if(!empty($usernameExt))
		{
			$checkExt = $this->select_data("app.t_mtr_phone_extension", " where username_phone='$usernameExt' and status=1 ")->row();
			if($checkExt)
			{
				$whereIsExt =" or a.id= ".$checkExt->id;
				$idExt = $checkExt->id;
			}
		}

		$getPortId = $portId == "" ? "" : " and a.port_id= ".$portId;
		$field = "b.name as port_name, a.id, a.port_id, a.password_phone, a.extension_phone, username_phone";		
		$where = " where a.status = 1 ".$getPortId." and a.is_used = 0 ".$whereIsExt."  order by 
		a.username_phone asc";

		$qry = "SELECT
						$field
					from APP.t_mtr_phone_extension a
					left join app.t_mtr_port b on a.port_id =b.id
					$where ";

		$data = $this->db->query($qry)->result();

		$return = [];
		$selected ="";
		foreach ($data as $key => $value) {

			$value->id = $this->enc->encode($value->id);
			if($idExt == $this->enc->decode($value->id))
			{
				$selected = $value->id ;
			}
			$return[] = $value;
		}
		return array("data"=>$return,"selectData"=> $selected);
	}


}
