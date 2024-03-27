<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Models_crm extends MY_Model{

	public function __construct() {
		parent::__construct();
		$this->_module = 'crm';
		$this->_db = $this->load->database('default', TRUE);
		// $this->_dbmerak = $this->load->database('dbmerak', TRUE);
	}

	public function get_data_crm()
	{
		
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$id_number = $this->input->post('id_number');

		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$where = " WHERE bop.id_number = '{$id_number}' ";

		if(!empty($search['value'])){
			$where .= " AND (
				bop.ticket_number ilike '%{$iLike}%'
				OR bop.name ilike '%{$iLike}%'
				OR bop.gender ilike '%{$iLike}%'
				OR port1.name ilike '%{$iLike}%'
				OR port2.name ilike '%{$iLike}%'
				OR ms.description ilike '%{$iLike}%'
				OR boar.boarding_code ilike '%{$iLike}%'
			) ";
		}


		$sql = " SELECT bop.id_number
		,bop.name
		,bop.ticket_number
		,bop.birth_date
		,bop.gender
		,port1.name AS origin
		,port2.name AS destination
		,opbor.ship_id
		,ship.name AS ship_name
		,ship_class.name AS ship_class
		,boar.boarding_code
		,boar.boarding_date
		,bok.booking_code
		,bok.created_on AS booking_date
		,ser.name AS service_name
		,veh.id_number AS plat_number
		,bop.status AS status_code
		,ms.description AS status_name
		FROM app.t_trx_booking_passanger bop 
		JOIN app.t_trx_booking bok ON bok.booking_code = bop.booking_code AND bop.status IN (2,3,4,5) 
		JOIN app.t_trx_invoice inv ON inv.trans_number = bok.trans_number AND inv.status = 2 
		JOIN app.t_mtr_port port1 ON port1.id = bop.origin 
		JOIN app.t_mtr_port port2 ON port2.id = bop.destination 
		JOIN app.t_mtr_status ms ON ms.tbl_name = 't_trx_booking_passanger' AND bop.status = ms.status
		LEFT JOIN app.t_trx_boarding_passanger boar ON boar.ticket_number = bop.ticket_number
		LEFT JOIN app.t_trx_booking_vehicle veh ON bok.booking_code = veh.booking_code
		JOIN app.t_mtr_service ser ON bok.service_id = ser.id
		LEFT JOIN app.t_trx_open_boarding opbor ON boar.boarding_code = opbor.boarding_code
		LEFT JOIN app.t_mtr_ship ship ON opbor.ship_id = ship.id
	  	LEFT JOIN app.t_mtr_ship_class ship_class ON ship.ship_class = ship_class.id
		
		{$where}
		-- ORDER BY bop.id DESC
		
		";

		$sql2 = $sql;
		$query = $this->_db->query($sql);
		$records_total = $query->num_rows();
		if($order_column == 0){
			$sql .= " ORDER BY bop.id DESC";
		}else{
			$sql .= " ORDER BY {$order_column} {$order_dir}";
		}

		if($length != -1){
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}

		$query = $this->_db->query($sql);
		$rows_data = $query->result();

		$num = 0;
		$data = array();
		foreach($rows_data as $row){
			$num++;
			$row->no = $num;
			if($row->boarding_date != null){
				$row->boarding_date = format_dateTime($row->boarding_date);
			}

			if($row->booking_date != null){
				$row->booking_date = format_dateTime($row->booking_date);
			}
			
			// if($row->boarding_date != null){
			// 	$rows['boarding_date'] = format_dateTime($row->boarding_date);
			// }else{
			// 	$rows['boarding_date'] = '-';
			// }

			$data[] = $row;
		}

		$s =  $sql2 . " AND boar.boarding_date IS NOT NULL";
		$jml_boarding = $this->_db->query($s)->num_rows();

		$sql2 .= " ORDER BY bop.id DESC LIMIT 1";

		$sql_single_data = $this->_db->query($sql2)->result();
		$sql_single_data['jml_perjalanan'] = $jml_boarding;

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $data,
			'data_s'		 => $sql_single_data
		);
	}

	public function download(){
		$id_number = $this->input->get('id_number');
		
	 	$sql = "SELECT bop.id_number
		 ,bop.name
		 ,bop.ticket_number
		 ,bop.birth_date
		 ,bop.gender
		 ,port1.name AS origin
		 ,port2.name AS destination
		 ,opbor.ship_id
		 ,ship.name AS ship_name
		 ,ship_class.name AS ship_class
		 ,boar.boarding_code
		 ,boar.boarding_date
		 ,bok.booking_code
		 ,bok.created_on AS booking_date
		 ,ser.name AS service_name
		 ,veh.id_number AS plat_number
		 ,bop.status AS status_code
		 ,ms.description AS status_name
		 FROM app.t_trx_booking_passanger bop 
		 JOIN app.t_trx_booking bok ON bok.booking_code = bop.booking_code AND bop.status IN (2,3,4,5) 
		 JOIN app.t_trx_invoice inv ON inv.trans_number = bok.trans_number AND inv.status = 2 
		 JOIN app.t_mtr_port port1 ON port1.id = bop.origin 
		 JOIN app.t_mtr_port port2 ON port2.id = bop.destination 
		 JOIN app.t_mtr_status ms ON ms.tbl_name = 't_trx_booking_passanger' AND bop.status = ms.status
		 LEFT JOIN app.t_trx_boarding_passanger boar ON boar.ticket_number = bop.ticket_number
		 LEFT JOIN app.t_trx_booking_vehicle veh ON bok.booking_code = veh.booking_code
		 JOIN app.t_mtr_service ser ON bok.service_id = ser.id
		 LEFT JOIN app.t_trx_open_boarding opbor ON boar.boarding_code = opbor.boarding_code
		 LEFT JOIN app.t_mtr_ship ship ON opbor.ship_id = ship.id
		 LEFT JOIN app.t_mtr_ship_class ship_class ON ship.ship_class = ship_class.id
		 WHERE bop.id_number = '{$id_number}'
		 ";

	 	$query = $this->_db->query($sql);

	 	return $query;
	}

	// public function get_date_query()
	// {
	// 	$sql = " SELECT aa.id_number, aa.name, aa.birth_date, aa.gender, bb.ticket_number, bb.boarding_date::DATE
	// 	FROM app.t_trx_booking_passanger aa 
	// 	INNER JOIN app.t_trx_boarding_passanger bb ON aa.ticket_number = bb.ticket_number
	// 	WHERE aa.id_number = '1234' 
	// 	AND bb.boarding_date IS NOT null
	// 	ORDER BY bb.boarding_date DESC LIMIT 1
	// 	";
	// 	$sql2 = " SELECT * FROM (
	// 		WITH RECURSIVE t AS(
	// 			SELECT 1 n UNION SELECT n + 1 FROM t WHERE n < 12
	// 		)
	// 		SELECT to_char(to_timestamp(n::text,'MM'),'Month') as name_month, n as no_month FROM t
	// 	) aa
	// 	LEFT JOIN(
	// 		SELECT to_char(bor.boarding_date, 'Month') as bulan,
	// 		COUNT(bok.id) as jml_id,
	// 		EXTRACT(MONTH FROM bor.boarding_date) as bln
	// 		FROM app.t_trx_booking_passanger bok
	// 		INNER JOIN app.t_trx_boarding_passanger bor ON bok.ticket_number = bor.ticket_number
	// 		WHERE bok.id_number = '1234'
	// 		-- AND date_part('year', bor.boarding_date) <= '2019'
	// 		GROUP BY to_char(bor.boarding_date, 'Month'), EXTRACT(MONTH FROM bor.boarding_date)
	// 	) bb ON bb.bln = aa.no_month
	// 	ORDER BY aa.no_month
	// 	";

	// 	$run = $this->_db->query($sql)->result();
	// 	$run2 = $this->_db->query($sql2)->result();
	// 	return array(
	// 		'profile' => $run,
	// 		'data'	  => $run2);
	// }

	
}
