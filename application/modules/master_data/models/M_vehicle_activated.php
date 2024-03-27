<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_vehicle_activated extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/vehicle_activated';
	}

	public function check_data($vehicle_class, $port_id, $ship_class, $param = "") {
		if ($param != "") {
			$sql = $this->db->query("SELECT * FROM app.t_mtr_vehicle_class_activated WHERE vehicle_class = $vehicle_class AND port_id = $port_id AND ship_class = $ship_class AND id != $param");
			if ($sql->num_rows() > 0) {
				return true;
			}else{
				return false;
			}
		}else{
			$sql = $this->db->query("SELECT * FROM app.t_mtr_vehicle_class_activated WHERE vehicle_class = $vehicle_class AND port_id = $port_id AND ship_class = $ship_class");
			if ($sql->num_rows() > 0) {
				return true;
			}else{
				return false;
			}
		}
	}

    public function dataList() {
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');

		$port = $this->enc->decode($this->input->post('port'));
		$vehicle_class = $this->enc->decode($this->input->post('vehicle_class'));
		$ship_class = $this->enc->decode($this->input->post('ship_class'));

		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike = trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$field = array(
			0 =>'id',
			1 => 'port_id',
			2 => 'vehicle_class',
			3 => 'ship_class',
			4 => 'pos_motor_bike',
			5 => 'pos_vehicle',
			6 => 'web',
			7 => 'mobile',
			8 => 'b2b',
			9 => 'ifcs',
			10 => 'mpos_motor_bike',
			11 => 'mpos_vehicle',
			12 => 'web_cs'
		);

		$order_column = $field[$order_column];

		$where = " WHERE MVC.status = 1 ";

		if ($port != "") {
			$where .= " AND port_id = $port";
		}

		if ($vehicle_class != "") {
			$where .= " AND vehicle_class = $vehicle_class";
		}

		if ($ship_class != "") {
			$where .= " AND ship_class = $ship_class";
		}

		$sql = "SELECT
					P.name as port_name,
					VC.name as golongan,
					SC.name as class,
					MVC.*
				FROM
					app.t_mtr_vehicle_class_activated MVC
					JOIN app.t_mtr_port P ON P.id = MVC.port_id
					JOIN app.t_mtr_vehicle_class VC ON VC.id = MVC.vehicle_class
					JOIN app.t_mtr_ship_class SC ON SC.id = MVC.ship_class
				{$where}";
		// print_r($sql);exit;
		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$getMerchantB2b="";
		if(array_column($rows_data,"id"))
		{
			$getMerchantB2b = $this->getMerchantB2b(array_column($rows_data,"id"));
		}

		// print_r($getMerchantB2b); exit;

		$rows 	= array();
		$i  	= ($start + 1);

		// print_r($rows_data);exit;

		foreach ($rows_data as $row) {
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
     		$aktif       = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

			$row->id 	 = $this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$row->id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$row->id}");

     		$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);
     		$row->no=$i;

     		$row->pos_motor_bike === "t" ? $row->pos_motor_bike = success_label('Aktif') : $row->pos_motor_bike = failed_label('Tidak Aktif');
     		$row->pos_vehicle === "t" ? $row->pos_vehicle = success_label('Aktif') : $row->pos_vehicle = failed_label('Tidak Aktif');
     		$row->web == "t" ? $row->web = success_label('Aktif') : $row->web = failed_label('Tidak Aktif');
     		$row->mobile == "t" ? $row->mobile = success_label('Aktif') : $row->mobile = failed_label('Tidak Aktif');
     		$row->ifcs == "t" ? $row->ifcs = success_label('Aktif') : $row->ifcs = failed_label('Tidak Aktif');
     		$row->web_cs == "t" ? $row->web_cs = success_label('Aktif') : $row->web_cs = failed_label('Tidak Aktif');
     		$row->mpos_motor_bike == "t" ? $row->mpos_motor_bike = success_label('Aktif') : $row->mpos_motor_bike = failed_label('Tidak Aktif');
     		$row->mpos_vehicle == "t" ? $row->mpos_vehicle = success_label('Aktif') : $row->mpos_vehicle = failed_label('Tidak Aktif');

			$merchantId = $this->enc->decode($row->id);
			$dataMerchant[]="";
			$merchant= "";

			if($row->b2b == "t")
			{
				$row->b2b = "<div align='center'>".success_label('Aktif')."</div>";

				if(!empty($getMerchantB2b[$merchantId]))
				{
					// $getMerchant = $this->select_data("app.t_mtr_vehicle_class_activated_b2b"," where vehicle_class_activated_id = '$merchantId'  and status = 1 order by id asc ")->result();
					$dataMerchantB2b = array_map(function($x){ return $x."<br>";},$getMerchantB2b[$merchantId]);
				
					$newValue2 ="<span style='display:none' id='text_".$merchantId."'>";
					$newValue3 ="</span>";
					$newValue4 ="<span id='btndetail_".$merchantId."' class='btn btn-link btn-sm' onClick=showText(".$merchantId.") style='padding:0px; ' >...</span>";

					if(count($getMerchantB2b[$merchantId]) > 4)
					{
						// array_splice($getMerchantB2b[$row->id], 4, 0, $newValue);
						array_splice($dataMerchantB2b, 4, 0, $newValue2);
						array_splice($dataMerchantB2b, count($dataMerchantB2b) , 0, $newValue3);
						array_splice($dataMerchantB2b, count($dataMerchantB2b) , 0, $newValue4);
					}
					$row->b2b = implode("", $dataMerchantB2b);
				}

			}
			else
			{
				$row->b2b = "<div align='center'>".failed_label('Tidak Aktif')."</div>";
			}
			
			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows,
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
		);
	}

	public function getDropdown($table, $id, $name, $selected="")
	{
		$data = $this->select_data($table, " where status =1 order by $name asc" )->result();

		$returnData[""]="Pilih";
		$getSelected ="";
		foreach ($data as $key => $value) {
			$encodeId = $this->enc->encode($value->$id);
			if($selected == $value->$id)
			{
				$encodeId = $this->enc->encode($value->$id);
				$getSelected = $encodeId;
			}
			$returnData[$encodeId]=$value->$name;
		}

		if(!empty($selected))
		{
			return  array("data"=>$returnData,"selected"=>$getSelected );
			exit;
		}
		return  $returnData;
	}

	public function getMerchantB2b($id)
	{
		$idString = array_map(function($x){ return "'".$x."'"; }, $id);

		$where ="where 
					a.vehicle_class_activated_id in (".implode(",",$idString).") 
					and a.status=1
					order by b.merchant_name asc ";
		

		$getData  = $this->getDetailMerchant($where);
		
		$returnData=array();
		foreach ($getData as $key => $value) {
			$returnData[$value->vehicle_class_activated_id][]=$value->merchant_name;
		}

		return $returnData;
	}

	public function getDetailMerchant($where)
	{

		$qry ="SELECT
						a.vehicle_class_activated_id ,
						a.merchant_id ,
						b.merchant_name 
					from 
						app.t_mtr_vehicle_class_activated_b2b a
						join app.t_mtr_merchant b on a.merchant_id = b.merchant_id 
						$where
						";
		
		$getData = $this->db->query($qry)->result();	
		return $getData	;

	}

	public function getMerchantDetail($id)
	{
		$qry ="
			select 
				vehicle_class_activated_id,
				merchant_id
			from
				app.t_mtr_vehicle_class_activated_b2b
			where
				vehicle_class_activated_id ='$id'
				and status = 1
		";
		// die($qry);exit;
		return $this->db->query($qry)->result();
	}	

	public function get_edit($id) {
		return $this->db->query("SELECT * FROM app.t_mtr_vehicle_class_activated WHERE id = $id")->row();
	}

	public function get_byCode($code) {
		$query = $this->db->query("SELECT port_code FROM t_mtr_port WHERE port_code = '$code' and status = 1");
		return $query->row();
	}
	
	public function get_prov() {
		return $this->db->query("select * from public.t_mtr_province order by name asc")->result();
	}
	
	public function get_area($id) {
		return $this->db->query("select * from public.t_mtr_city where province_id='$id' order by name asc")->result();
	}
	
	public function get_district($id) {
		return $this->db->query("select * from public.t_mtr_district where city_id='$id' order by name asc")->result();
	}

	public function select_data($table, $where) {
		return $this->db->query("select * from $table $where");
	}

	public function insert_data($table,$data) {
		$this->db->insert($table, $data);
	}

	public function update_data($table,$data,$where) {
		$this->db->where($where);
		$this->db->update($table, $data);
	}

	public function delete_data($table,$data,$where) {
		$this->db->where($where);
		$this->db->delete($table, $data);
	}

	public function insert_data_batch($table,$data)
	{
		$this->db->insert_batch($table, $data);
	}	
}