<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 *
 *
 */

class M_sap_pengiriman extends MY_Model{

	public function __construct() {
		parent::__construct();
				$this->_module = 'monitoring/sap_pengiriman';
				// $this->dbView=$this->load->database("dbView",TRUE);
				$this->dbView=checkReplication();
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');

		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$port = $this->enc->decode($this->input->post('port'));
		$ship_class = $this->enc->decode($this->input->post('ship_class'));
		$type = $this->enc->decode($this->input->post('type'));

		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));



		$field = array(
			0 =>'id',
			1 =>'text',
			2 =>'response',
			3 =>'shift_date',
			4 =>'shift_name',
			5 =>'port',
			6 =>'ship_class',
			4 =>'type',
			5 =>'created_oon',
			6 =>'status',
			7 =>'actions'
		);

		$order_column = $field[$order_column];

		$where = " WHERE (to_char(a.shift_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";

		if (!empty($port)){
			$where .= "  and (a.port_id  ='".$port."' )";
		}

		if (!empty($ship_class)){
			$where .= "  and (a.ship_class  ='".$ship_class."' )";
		}

		if (!empty($type)){
			$where .= "  and (a.type  ='".$type."' )";
		}

		if (!empty($search['value'])){
			$where .="and (
							b.shift_name ilike '%".$iLike."%'
							or a.text ilike '%".$iLike."%'
							or c.name ilike '%".$iLike."%'
							or d.name ilike '%".$iLike."%'
							or a.response ilike '%".$iLike."%' 
							or e.description ilike '%".$iLike."%' 
							)";	
		}

		$sql 		   = "select a.id, a.text , a.response ,a.shift_date ,b.shift_name ,c.name as port ,d.name as ship_class ,
									(case 
									 when a.type = 1
									 then 'TERJUAL NORMAL'
									 when a.type = 2
									 then 'TERTAGIH NORMAL' 
									 when a.type = 3
									 then 'TERJUAL MANUAL'
									 when a.type = 4
									 then 'TERTAGIH MANUAL' 
									 
									 end) as type ,a.created_on , e.description  as status
										from app.t_log_sync_sap a
										left join app.t_mtr_shift b on a.shift_id = b.id 
										left join app.t_mtr_port c on a.port_id = c.id
										left join app.t_mtr_ship_class d on a.ship_class = d.id
										left join app.t_mtr_status e on a.status = e.status and e.tbl_name = 't_log_sync_sap'
										{$where}";

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$resend = site_url($this->_module."/resend/".$this->enc->encode($row->id));
			
			if ($row->status == "Sudah terkirim")
			{
				$row->status = success_label("Sudah Terkirim");
				$row->actions = '-';

			}
			else if($row->status == "Sukses"){
				$row->status = success_label("Sukses");
				$row->actions = '-';
			}
			else {
				$row->status = failed_label("Gagal");
				$row->actions =generate_button($this->_module, 'edit',  '<button onclick="showModal(\''.$resend.'\')" class="btn btn-sm btn-primary" title="Resend"><i class="fa fa-send"></i> Resend</button> ');
			}
			

     		$row->created_on=empty($row->created_on)?"":format_dateTimeHis($row->created_on);

     		$row->no=$i;

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

	// public function listDetail($where=""){

	// 	return $this->db->query("
	// 						select g.name as service_name,f.name as shift_class_name, e.name as destination_name, d.name as origin_name, 
	// 						c.name as special_service_name, b.name as passenger_type_name, a.* from app.t_trx_booking_passanger a
	// 						left join  app.t_mtr_passanger_type b on a.passanger_type_id=b.id
	// 						left join app.t_mtr_special_service c on a.special_service_id=c.id
	// 						left join app.t_mtr_port d on a.origin=d.id
	// 						left join app.t_mtr_port e on a.destination=e.id
	// 						left join app.t_mtr_ship_class f on a.ship_class=f.id
	// 						left join app.t_mtr_service g on a.service_id=g.id	
	// 						$where
	// 						 ");
	// }

	// public function listVehicle($where=""){

	// 	return $this->db->query("
	// 						select g.name as service_name,f.name as shift_class_name, e.name as destination_name, d.name as origin_name,
	// 						 b.name as vehicle_class_name, a.* from app.t_trx_booking_vehicle a
	// 						left join  app.t_mtr_vehicle_class b on a.vehicle_class_id=b.id
	// 						left join app.t_mtr_port d on a.origin=d.id
	// 						left join app.t_mtr_port e on a.destination=e.id
	// 						left join app.t_mtr_ship_class f on a.ship_class=f.id
	// 						left join app.t_mtr_service g on a.service_id=g.id	
	// 						$where
	// 						 ");
	// }

	public function select_data($table, $where="")
	{
		return $this->dbView->query("select * from $table $where");
	}

	

	public function get_identity_app()
	{
		$data=$this->dbView->query("select * from app.t_mtr_identity_app")->row();
		return $data->port_id;
	}

}
