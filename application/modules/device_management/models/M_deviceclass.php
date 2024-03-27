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

class M_deviceclass extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'device_management/device_class';
	}

    public function dataList(){


		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$service=$this->enc->decode($this->input->post('service'));
		$port=$this->enc->decode($this->input->post('port'));
		$device_type_id=$this->enc->decode($this->input->post('device_type'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));
		
		$field = array(
			0 =>'device_terminal_id',
			1 =>'terminal_code',
			2 =>'terminal_name',
			3 =>'terminal_type_id',
			4 =>'terminal_type_name',
			5 =>'service_name',
			6 =>'channel',
			7 =>'port_name',
			8 =>'dock_name',
			9 =>'imei',
			10 =>'a2.terminal_name',
			11 =>'class_ship_name',
			12 =>'cross_class',
			13 =>'username_phone',
			14 =>'extension_phone',
			15 =>'cctv_path',
			16 =>'status',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5)";

		if(!empty($service))
		{
			$where .="and (b.service_id='".$service."')";
		}

		if(!empty($port))
		{
			$where .="and (a.port_id='".$port."')";
		}

		if(!empty($device_type_id))
		{
			$where .="and (a.terminal_type='".$device_type_id."')";
		}


		if(!empty($searchData))
		{
			if($searchName=="deviceName")
			{
				$where .= " and a.terminal_name ilike '%".$iLike."%' ESCAPE '!' ";
			}
			else if($searchName=="terminalCode")
			{
				$where .= " and a.terminal_code ilike '%".$iLike."%' ESCAPE '!' ";
			}
			else if($searchName=="serialNumber")
			{
				$where .= " and a.imei ilike '%".$iLike."%' ESCAPE '!' ";
			}
		}		

		$sql = "SELECT b.terminal_type_id,f.name as class_ship_name,
				(case
					when e.name is null then 'UMUM'
					else  upper(e.name)
				 end) as service_name,
				d.name as dock_name, 
				c.name as port_name,
				b.terminal_type_name,
				b.channel,
				a2.terminal_name as pairing_pos_name,
				 a.* 
			from app.t_mtr_device_terminal a
			left join app.t_mtr_device_terminal_type b on a.terminal_type=b.terminal_type_id
			left join app.t_mtr_port c on a.port_id=c.id
			left join app.t_mtr_dock d on a.dock_id=d.id
			left join app.t_mtr_service e on b.service_id=e.id
			left join app.t_mtr_ship_class f on a.ship_class=f.id
			left join app.t_mtr_device_terminal a2 on a.pairing_pos=a2.terminal_code
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

		$dataVehicleClass = $this->getMaster("t_mtr_vehicle_class","id","name"); 
		// print_r($dataVehicleClass); exit;

		foreach ($rows_data as $row) {
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->device_terminal_id.'|0'));
     		$aktif       = site_url($this->_module."/enable/".$this->enc->encode($row->device_terminal_id.'|1'));

			$row->device_terminal_id 	 = $this->enc->encode($row->device_terminal_id);
			$edit_url 	 = site_url($this->_module."/edit/{$row->device_terminal_id}");
			$edit_pass 	 = site_url($this->_module."/edit_pass/{$row->device_terminal_id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$row->device_terminal_id}");

			
			if($row->terminal_type == 1 || $row->terminal_type  == 2 || $row->terminal_type  == 3 || $row->terminal_type  == 12 
			|| $row->terminal_type == 16 || $row->terminal_type == 17 || $row->terminal_type  == 15)
			{
				$row->cross_class  = $row->cross_class == 't' ?"Iya":"Tidak";
			}
			else
			{
				$row->cross_class  = "";
			}

     		$row->actions  = "";

			if($row->status == 1){
				$row->status   = success_label('Aktif');
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				
				if($row->terminal_type_id ==19 or $row->terminal_type_id ==18){
					$row->actions .="";
				}
				else
				{
					$row->actions .='<button onclick="showModal(\''.$edit_pass.'\')" class="btn btn-sm btn-primary" title="Reset pass"><i class="fa fa-lock"></i></button> ';
				}

				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', '."'".$nonaktif."'".')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', '."'".$aktif."'".')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
			}

			if(!empty($row->cctv_path))
			{

				$arr = json_decode($row->cctv_path);
				$totalCctv = count($arr);
				$cctvArr = array_map(function($key, $x) use ($totalCctv){
						if($key < $totalCctv - 1 )					
							{return "".$x.", ";}
						else
							{return $x;}
						
					},array_keys($arr),$arr);

				if(count($cctvArr)>4)
				{
					$newValue2 ="<span style='display:none' id='text_".$i."'>";
					$newValue3 ="</span>";
					$newValue4 ="<span id='btndetail_".$i."' class='btn btn-link btn-sm' onClick=showText(".$i.") style='padding:0px; ' >...</span>";
					
					array_splice($cctvArr, 4, 0, $newValue2);
					array_splice($cctvArr, $totalCctv +=1 , 0, $newValue3);
					array_splice($cctvArr, $totalCctv +=1 , 0, $newValue4);
				}

				if(!empty($row->vehicle_class_id))
				{
					$arrVehicle = json_decode($row->vehicle_class_id);
					$totalVehicleClass = count($arrVehicle);
					$arrVehicle2 = array_map(function($key, $x) use ($totalVehicleClass, $dataVehicleClass){
							if($key < $totalVehicleClass - 1 )					
								{return "".@$dataVehicleClass[$x].", ";}
							else
								{return @$dataVehicleClass[$x];}
							
						},array_keys($arrVehicle),$arrVehicle);	
						
					$row->vehicle_class_id = implode(" ",$arrVehicle2);

				}

				
				$row->cctv_path = implode("",$cctvArr);
			}
     		
			$yes = '<i class="fa fa-check-circle text-success" style="font-size:150%"></i>';
			$no = '<i class="fa fa-times-circle text-danger" style="font-size:150%"></i>';		
			// jika self service maka tampilan seperti ini
			if($row->terminal_type_id == 21){
				$row->enable_overpaid_underpaid = $row->enable_overpaid_underpaid=='t'?$yes:$no;
				$row->enable_sensor = $row->enable_sensor=='t'?$yes:$no;
			}
			else
			{
				$row->enable_overpaid_underpaid = "";
				$row->enable_sensor = "";				
			}
			

     		$row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);


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

	public function getMaster($table,$id,$name)
	{
		
		$service =  $this->select_data("app.$table"," where status != '-5' ")->result() ;
        $checkSession = $this->session->userdata("app.".$table); 

        if($checkSession)
        {
            $dataReturn = $checkSession;
        }
        else
        {

            $dataReturn=array();    
            foreach ($service as $key => $value) {
                $dataReturn[$value->$id]= $value->$name;
            }

            $this->session->set_userdata(array("app.".$table => $dataReturn));
        }

		return $dataReturn ;

	}     	

	public function getPosVehicle($portId)
	{

		$qry="
				SELECT 
				* from 
				app.t_mtr_device_terminal
				where terminal_type in (2,12)
				and port_id={$portId}
				order by terminal_name asc

		";

		$data=$this->db->query($qry)->result();

		return $data;
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
	public function get_byCode($code){
		$query = $this->db->query("SELECT port_code FROM t_mtr_port WHERE port_code = '$code' and status = 1");
		return $query->row();
	}
	
	public function get_prov(){
		return $this->db->query("select * from public.t_mtr_province order by name asc")->result();
	}
	
	public function get_area($id){
		return $this->db->query("select * from public.t_mtr_city where province_id='$id' order by name asc")->result();
	}
	
	public function get_district($id){
		return $this->db->query("select * from public.t_mtr_district where city_id='$id' order by name asc")->result();
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
