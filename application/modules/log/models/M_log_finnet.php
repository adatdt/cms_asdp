<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Fajar Rasia A <alfajrduta@gmail.com>
 * @copyright  2020
 *
 */

class M_log_finnet extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'log/log_finnet';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		// $dateTo = trim($this->input->post('dateTo'));
		// $dateFrom = trim($this->input->post('dateFrom'));

		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));



		$field = array(
			0 =>'id',
            1 =>'req',
            2 =>'created_on',

		);

		$order_column = $field[$order_column];

		$where = "";


		if (!empty($search['value'])){
			$where .="WHERE (
							req ilike '%".$iLike."%'
							)";	
		}

		$sql 		   = " 
							SELECT * FROM app.t_trx_log
							{$where}
							
                        ";
        

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

		foreach ($rows_data as $row) {
			$row->number = $i;

     		$row->transaction_date=empty($row->transaction_date)?"":format_dateTimeHis($row->transaction_date);
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
			'data'           => $rows
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
		return $this->db->query("select * from $table $where");
	}

	// public function download(){
	
	// 	$dateFrom=$this->input->get("dateFrom");
    //     $dateTo=$this->input->get("dateTo");
    //     $search=$this->input->get("search");
    //     $iLike = trim($this->db->escape_like_str($search));

	// 	$field = array(
	// 		0 =>'id',
	// 		1 =>'transaction_date',
	// 		2 =>'terminal_code',
	// 		3 =>'terminal_name',
	// 		4 =>'terminal_type_name',
	// 		5 =>'state',
	// 		6 =>'data_transaction',
	// 	);

	// 	$where = " WHERE a.status=1 and (to_char(a.transaction_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) ";


	// 	if (!empty($search)){
	// 		$where .="and (
	// 						c.terminal_type_name ilike '%".$iLike."%'
	// 						or b.terminal_name ilike '%".$iLike."%'
	// 						or a.terminal_code ilike '%".$iLike."%' 
	// 						or a.data_transaction ilike '%".$iLike."%' 
	// 						)";	
	// 	}

	// 	$sql 		   = " 
	// 						select c.terminal_type_name, b.terminal_name, a.* from app.t_logs_transaction a
	// 						left join app.t_mtr_device_terminal b on a.terminal_code=b.terminal_code
	// 						left join app.t_mtr_device_terminal_type c on b.terminal_type=c.terminal_type_id
	// 						{$where}  order by a.id desc
							
	// 					 ";

	// 	$query         = $this->db->query($sql);

	// 	return $query;

	// }

	public function get_identity_app()
	{
		$data=$this->db->query("select * from app.t_mtr_identity_app")->row();
		return $data->port_id;
	}

}
