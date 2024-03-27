<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : asset device
 * -----------------------
 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  2024
 *
 */

class AssetDeviceModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data2/assetDevice';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom= $this->input->post('dateFrom');
		$dateTo= $this->input->post('dateTo');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		// $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$portId = $this->enc->decode($this->input->post('port'));
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));

		
		$field = array(
			0 =>'start_date',

		);

		$order_column = $field[$order_column];
		$where = " WHERE a.status not in (-5) ";		

		$identityApp = $this->select_data("app.t_mtr_identity_app"," ")->row();
		if($identityApp->port_id != 0)
		{
			$where .=" and a.port_id = ".$identityApp->port_id;
		}
		else
		{
			$groupPortId = $this->session->userdata("port_id");			
			if(!empty($groupPortId))
			{
				$where .=" and a.port_id = ".$groupPortId;
			}

		}		

		if(!empty($portId))
		{
			$where .=" and a.port_id = ".$portId;
		}

		if(!empty($dateFrom) && !empty($dateTo) )
		{			
			$where .=" and (start_date between '".$this->checkDate($dateFrom)."' and '".$this->checkDate($dateTo)."'  ) ";
		}

		if(!empty($searchData))
		{
			if($searchName=="groupCode")
			{
				$where .= "and a.group_code_assets  ='" . $iLike . "' ";

			}
			else if($searchName=="module")
			{
				$where .= "and a.module ilike '%" . $iLike . "%' ";
			}
			else if($searchName=="name")
			{
				$where .= "and a.name ilike '%" . $iLike . "%' ";

			}
			else if($searchName=="ip")
			{
				$where .= "and a.ip_local  ='" . $iLike . "' ";
			}
		
		}		

		// if(!empty($search['value']))
		// {
		// 	$where .="and  group_code_assets ilike  '%" .$iLike."%' ESCAPE '!' 
		// 					or module ilike  '%" .$iLike."%' ESCAPE '!'  
		// 					or name ilike  '%" .$iLike."%' ESCAPE '!'  
		// 					or ".'"desc"'." ilike  '%" .$iLike."%' ESCAPE '!' ";
		// }

		$sql 		   = 'SELECT
									b.name as port_name,
									module,
									start_date,
									group_code_assets,
									file_type,
									a.name,
									path,
									"desc",
									a."order",
									ip_local,
									is_sync,
									a.status,									
									a.id
							from app.t_mtr_assets_device a 
							left join app.t_mtr_port b on a.port_id = b.id
							'.$where ;

		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir} , ".'"order"'." asc ";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		$masterType = $this->getFilleType();
		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);
			$code_enc=$this->enc->encode($row->group_code_assets);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$code_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions  =" ";

			if($row->status == 1){
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				// $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				// $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

			if($row->is_sync == 't')
			{
				$row->is_sync = '<i class="fa fa-check-circle text-success" style="font-size:150%"></i>';
			}
			else
			{
				$row->is_sync = '<i class="fa fa-times-circle text-danger" style="font-size:150%"></i>';

			}

			$row->file_type = @$masterType[$row->file_type];

     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
			$row->start_date = format_date($row->start_date)." ".format_time($row->start_date);

			$rows[] = $row;
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

	function getFilleType()
    {
        $arr[1]="Gambar";
        $arr[2]="Video";

        return $arr;
    }

	public function checkDate($date)
	{
		$getDate = date("Y-m-d", strtotime($date));

		$data = $getDate;
		if( $getDate != $date)
		{
			$data = date("Y-m-d");
		}
		return $data;
	}

	public function getModule($selected="")
	{
		$data = $this->select_data("app.t_mtr_custom_param" , " where param_name ='master_module_asset_device' " )->row();
		$explode = explode(",", $data->param_value);
		asort($explode);

		$return[""]="Pilih";
		$getSelected ="";
		foreach ($explode as $key => $value) {

			$keyEnc = $this->enc->encode($value); 
			if(strtoupper($selected) == strtoupper($value))
			{
				$getSelected = $this->enc->encode($value); 
				$keyEnc = $getSelected; 
			}
			$return[$keyEnc]=$value;
		}

		return array("data"=>$return, "selected"=>$getSelected)	;

	}
	public function getPortId($selected="")
	{
		$identityApp = $this->select_data("app.t_mtr_identity_app"," ")->row();

		
		$return[""] = "Pilih";
		if($identityApp->port_id != 0)
		{
			$where =" where id='".$identityApp->port_id."' order by name asc";
		}
		else
		{
			$groupPortId = $this->session->userdata("port_id");			
			if(!empty($groupPortId))
			{
				$where =" where id='".$groupPortId."' order by name asc";
			}
			else
			{
				
				$where =" where status=1 order by name asc";

			}
		}

		$getPort = $this->select_data("app.t_mtr_port", $where)->result();
		$getSelected ="";
		foreach ($getPort as $key => $value) {
			$keyEnc = $this->enc->encode($value->id); 
			if(strtoupper($selected) == strtoupper($value->id))
			{
				$getSelected = $this->enc->encode($value->id); 
				$keyEnc = $getSelected; 
			}
			$return[$keyEnc]=$value->name;			
		}

		return array("data"=>$return, "selected"=>$getSelected)	;
		

	}
	public function getPortIdIndex($selected="")
	{
		$identityApp = $this->select_data("app.t_mtr_identity_app"," ")->row();

		
		$return = array();
		if($identityApp->port_id != 0)
		{
			$where =" where id='".$identityApp->port_id."' order by name asc";
		}
		else
		{
			$groupPortId = $this->session->userdata("port_id");			
			if(!empty($groupPortId))
			{
				$where =" where id='".$groupPortId."' order by name asc";
			}
			else
			{
				$return[""] = "Pilih";
				$where =" where status=1 order by name asc";

			}
		}

		$getPort = $this->select_data("app.t_mtr_port", $where)->result();
		$getSelected ="";
		foreach ($getPort as $key => $value) {
			$keyEnc = $this->enc->encode($value->id); 
			if(strtoupper($selected) == strtoupper($value->id))
			{
				$getSelected = $this->enc->encode($value->id); 
				$keyEnc = $getSelected; 
			}
			$return[$keyEnc]=$value->name;			
		}

		return array("data"=>$return, "selected"=>$getSelected)	;
		

	}	

	public function select_data($table, $where)
	{
		return $this->db->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->db->insert($table, $data);
	}

	public function insert_data_batch($table,$data)
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
