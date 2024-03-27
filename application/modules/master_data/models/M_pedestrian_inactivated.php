<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_pedestrian_inactivated extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/pedestrian_inactivated';
	}

  public function dataList() {
		$start          = $this->input->post('start');
		$length         = $this->input->post('length');
		$draw           = $this->input->post('draw');
		$search         = $this->input->post('search');
		$order          = $this->input->post('order');
		$order_column   = $order[0]['column'];
		$order_dir      = strtoupper($order[0]['dir']);
		$iLike          = trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$port_id           = $this->enc->decode($this->input->post('port_id'));
		$ship_class_id     = $this->enc->decode($this->input->post('ship_class_id'));

        $field = array(
			0 =>'id',
			1 => 'port_id',
			2 => 'ship_class',
			3 => 'start_date',
			4 => 'end_date',
			5 => 'status',
		);

		$order_column = $field[$order_column];

		$where = "WHERE (pd.status NOT IN (-5))";

		if (!empty($port_id)) {
			$where .= " AND (pd.port_id = $port_id)";
		}

		if (!empty($ship_class_id)) {
			$where .= " AND (ship_class = $ship_class_id)";
		}

		if(!empty($search['value'])) {
			$where .=" AND ( (port.name ilike '%".$iLike."%') OR (ship_class.name ilike '%".$iLike."%') )";
		}

		$sql = "SELECT
                    pd.id AS id,
                    port.name AS port,
                    ship_class.name AS class,
                    pd.start_date,
                    pd.end_date,
                    pd.pos_motor_bike,
                    pd.pos_vehicle,
                    pd.web,
                    pd.mobile,
                    pd.b2b,
                    pd.ifcs,
                    pd.web_cs,
                    pd.mpos_vehicle,
                    pd.mpos_motor_bike,
                    pd.status
                FROM app.t_mtr_pedestrian_inactivated pd
                JOIN app.t_mtr_port port ON pd.port_id = port.id
                JOIN app.t_mtr_ship_class ship_class ON pd.ship_class = ship_class.id
				{$where}";

		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$getMerchantB2b="";
		if(array_column($rows_data,"id"))
		{
			$getMerchantB2b = $this->getMerchantB2b(array_column($rows_data,"id"));
		}		

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$id_enc=$this->enc->encode($row->id);

			$row->port 			= empty($row->port) ? '-' : $row->port;
			$row->group 		= empty($row->group) ? '-' : $row->group;
			$row->class 		= empty($row->class) ? '-' : $row->class;
			$row->start_date 	= empty($row->start_date) ? '-' : date('d-m-Y H:i:s', strtotime($row->start_date));
			$row->end_date 		= empty($row->end_date) ? '-' : date('d-m-Y H:i:s', strtotime($row->end_date));

			$row->pos_motor_bike === "t" ? $row->pos_motor_bike = success_label('Aktif') : $row->pos_motor_bike = failed_label('Tidak Aktif');
			$row->pos_vehicle === "t" ? $row->pos_vehicle = success_label('Aktif') : $row->pos_vehicle = failed_label('Tidak Aktif');
			$row->web == "t" ? $row->web = success_label('Aktif') : $row->web = failed_label('Tidak Aktif');
			$row->mobile == "t" ? $row->mobile = success_label('Aktif') : $row->mobile = failed_label('Tidak Aktif');
			// $row->b2b == "t" ? $row->b2b = success_label('Aktif') : $row->b2b = failed_label('Tidak Aktif');
			$row->ifcs == "t" ? $row->ifcs = success_label('Aktif') : $row->ifcs = failed_label('Tidak Aktif');
			$row->web_cs == "t" ? $row->web_cs = success_label('Aktif') : $row->web_cs = failed_label('Tidak Aktif');
			$row->mpos_motor_bike == "t" ? $row->mpos_motor_bike = success_label('Aktif') : $row->mpos_motor_bike = failed_label('Tidak Aktif');
			$row->mpos_vehicle == "t" ? $row->mpos_vehicle = success_label('Aktif') : $row->mpos_vehicle = failed_label('Tidak Aktif');

			if($row->b2b == "t")
			{
				$row->b2b = "<div align='center'>".success_label('Aktif')."</div>";
				if(!empty($getMerchantB2b))
				{
					if(!empty($getMerchantB2b[$row->id]))
					{
						$dataMerchantB2b = array_map(function($x){ return $x."<br>";},$getMerchantB2b[$row->id]);
						// $newValue ="<span style='color:blue' id='show_".$row->id."'>...</span>";
						$newValue2 ="<span style='display:none' id='text_".$row->id."'>";
						$newValue3 ="</span>";
						$newValue4 ="<span id='btndetail_".$row->id."' class='btn btn-link btn-sm' onClick=showText(".$row->id.") style='padding:0px; ' >...</span>";

						if(count($getMerchantB2b[$row->id]) > 4)
						{
							// array_splice($getMerchantB2b[$row->id], 4, 0, $newValue);
							array_splice($dataMerchantB2b, 4, 0, $newValue2);
							array_splice($dataMerchantB2b, count($dataMerchantB2b) , 0, $newValue3);
							array_splice($dataMerchantB2b, count($dataMerchantB2b) , 0, $newValue4);
						}

						$row->b2b = implode("", $dataMerchantB2b);
					}
				}
			}
			else
			{
				$row->b2b = "<div align='center'>".failed_label('Tidak Aktif')."</div>";
			}			

			$now 	= strtotime('now');
			$start 	= strtotime($row->start_date);
			$end 	= strtotime($row->end_date);

			if($now > $start && $now < $end) {
				$row->status = success_label('Aktif');
			}
			else {
				$row->status = failed_label('Tidak Aktif');
			}

			$edit_url 	 	= site_url($this->_module."/edit/{$id_enc}");
			$delete_url  	= site_url($this->_module."/action_delete/{$id_enc}");
			$row->actions 	= generate_button_new($this->_module, 'edit', $edit_url);
			$row->actions 	.= generate_button_new($this->_module, 'delete', $delete_url);

            $rows[] = $row;
			unset($row->id);
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
	public function getMerchantB2b($id)
	{
		$idString = array_map(function($x){ return "'".$x."'"; }, $id);

		$where ="where 
					a.pedestrian_inactivated_id in (".implode(",",$idString).") 
					and a.status=1
					order by b.merchant_name asc ";
		

		$getData  = $this->getDetailMerchant($where);
		
		$returnData=array();
		foreach ($getData as $key => $value) {
			$returnData[$value->pedestrian_inactivated_id][]=$value->merchant_name;
		}

		return $returnData;
	}
	public function getDetailMerchant($where)
	{

		$qry ="SELECT
						a.pedestrian_inactivated_id ,
						a.merchant_id ,
						b.merchant_name 
					from 
						app.t_mtr_pedestrian_inactivated_b2b a
						join app.t_mtr_merchant b on a.merchant_id = b.merchant_id 
						$where
						";
		
		$getData = $this->db->query($qry)->result();	
		return $getData	;

	}

	public function check_data($port_id, $ship_class_id, $param = "") {
		if ($param != "") {
			$sql = $this->db->query("SELECT * FROM app.t_mtr_pedestrian_inactivated WHERE port_id = $port_id AND ship_class = $ship_class_id AND id != $param");
			if ($sql->num_rows() > 0) {
				return true;
			}else{
				return false;
			}
		}
		else {
			$sql = $this->db->query("SELECT * FROM app.t_mtr_pedestrian_inactivated WHERE port_id = $port_id AND ship_class = $ship_class_id");
			if ($sql->num_rows() > 0) {
				return true;
			}else{
				return false;
			}
		}
	}

	public function get_edit($id) {
		return $this->db->query("SELECT * FROM app.t_mtr_pedestrian_inactivated WHERE id = $id")->row();
	}
			
	public function select_data($table, $where) {
        return $this->db->query("SELECT * FROM $table $where");
	}

	public function insert_data($table, $data) {
		$this->db->insert($table, $data);
	}
	public function insert_data_batch($table, $data) {
		$this->db->insert_batch($table, $data);
	}

	public function insert_data_id($table, $data) {
		$this->db->insert($table, $data);
		$insert_id = $this->db->insert_id();
		return  $insert_id;
	}


	public function update_data($table, $data, $where) {
		$this->db->where($where);
		$this->db->update($table, $data);
	}

	public function delete_data($table, $data, $where) {
		$this->db->where($where);
		$this->db->delete($table, $data);
	}
}