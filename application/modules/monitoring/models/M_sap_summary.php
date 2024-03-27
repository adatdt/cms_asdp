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

class M_sap_summary extends MY_Model{

	public function __construct() {
		parent::__construct();
				$this->_module = 'monitoring/sap_summary';
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
							or cast(a.pendapatan as varchar(255)) ilike '%".$iLike."%' 
							or e.description ilike '%".$iLike."%' 
							)";	
		}

		$sql 		   = "select a.id, a.text , a.pendapatan ,a.shift_date ,b.shift_name ,c.name as port ,d.name as ship_class ,
									(case 
									 when a.type = 1
									 then 'TERJUAL NORMAL'
									 when a.type = 2
									 then 'TERTAGIH NORMAL'
									 when a.type = 3
									 then 'TERJUAL MANUAL' 
									 when a.type = 4
									 then 'TERTAGIH MANUAL' end

									 ) as type ,a.created_on ,
									(case
									 when e.description is null
									 then cast(a.status as varchar(10))
									 else e.description end)  as status
										from app.t_trx_summary_sap a
										left join app.t_mtr_shift b on a.shift_id = b.id 
										left join app.t_mtr_port c on a.port_id = c.id
										left join app.t_mtr_ship_class d on a.ship_class = d.id
										left join app.t_mtr_status e on a.status = e.status and e.tbl_name = 't_trx_summary_sap'
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
			$generate = site_url($this->_module."/generate/".$this->enc->encode($row->id.'|'.$row->type));

			$row->actions =generate_button($this->_module, 'edit',  '<button onclick="showModal(\''.$generate.'\')" class="btn btn-sm btn-primary" title="Generate"><i class="fa fa-send"></i> Generate</button> ');

     		$row->transaction_date=empty($row->transaction_date)?"":format_dateTimeHis($row->transaction_date);
     		$row->created_on=empty($row->created_on)?"":format_dateTimeHis($row->created_on);
				$row->pendapatan=idr_currency($row->pendapatan);
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

	public function getSummaryTerjual_06102021 ($date, $port, $shipClass, $shift) {
		$sql = "
				SELECT 
						shift_name,
						SUM(data.produksi) as produksi,
						SUM(data.pendapatan) as pendapatan
				FROM (
						SELECT
								SH.shift_name,
								COUNT(DISTINCT BP.id ) as produksi,
								COUNT(DISTINCT BP.id) * BP.fare as pendapatan
						FROM
								app.t_trx_assignment_user_pos UP
								JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = UP.assignment_code AND AR.status = 2
								JOIN core.t_mtr_team T ON T.team_code = AR.team_code
								JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
								JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
								JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
								JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
								JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1 AND BO.ship_class = {$shipClass}
								JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code AND BP.service_id = 1
								JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1 

								-- AND PT.id != 3 

								JOIN app.t_mtr_port P on P.id = AR.port_id
						WHERE
								UP.assignment_date = '{$date}' 
						AND 
								UP.port_id = {$port} 
						AND 
								UP.shift_id = {$shift}
						AND
								BO.status = 2 
						GROUP BY
								SH.shift_name,
								BP.fare
						UNION ALL
						SELECT
								SH.shift_name,
								COUNT(DISTINCT BV.id) as produksi,
								COUNT(DISTINCT BV.id) * BV.fare as pendapatan
						FROM
								app.t_trx_assignment_user_pos UP
								JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = UP.assignment_code AND AR.status = 2
								JOIN core.t_mtr_team T ON T.team_code = AR.team_code
								JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
								JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
								JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
								JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
								JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1 AND BO.ship_class = {$shipClass}
								JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code
								JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
								JOIN app.t_mtr_port P on P.id = AR.port_id
						WHERE
								UP.assignment_date = '{$date}'
						AND 
								UP.port_id = {$port} 
						AND 
								UP.shift_id = {$shift}
						AND
								BO.status = 2 
						GROUP BY
								SH.shift_name,
								BV.fare
						UNION ALL

						SELECT
								SH.shift_name,
								COUNT(DISTINCT BP.id ) as produksi,
								COUNT(DISTINCT BP.id) * BP.fare as pendapatan
						FROM
								app.t_trx_assignment_user_pos UP
								JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = UP.assignment_code AND AR.status = 2
								JOIN core.t_mtr_team T ON T.team_code = AR.team_code
								JOIN app.t_trx_opening_balance_vm OB ON OB.assignment_code = UP.assignment_code
								JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
								JOIN app.t_trx_sell_vm S ON S.ob_code = OB.ob_code
								JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
								JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1 AND BO.ship_class = {$shipClass}
								JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code AND BP.service_id = 1
								JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1 

								-- AND PT.id != 3 

								JOIN app.t_mtr_port P on P.id = AR.port_id
						WHERE
								UP.assignment_date = '{$date}' 
						AND 
								UP.port_id = {$port} 
						AND 
								UP.shift_id = {$shift}
						AND
								BO.status = 2 
						GROUP BY
								SH.shift_name,
								BP.fare
						UNION ALL
						SELECT
								SH.shift_name,
								COUNT(DISTINCT BV.id) as produksi,
								COUNT(DISTINCT BV.id) * BV.fare as pendapatan
						FROM
								app.t_trx_assignment_user_pos UP
								JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = UP.assignment_code AND AR.status = 2
								JOIN core.t_mtr_team T ON T.team_code = AR.team_code
								JOIN app.t_trx_opening_balance_vm OB ON OB.assignment_code = UP.assignment_code
								JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
								JOIN app.t_trx_sell_vm S ON S.ob_code = OB.ob_code
								JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
								JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1 AND BO.ship_class = {$shipClass}
								JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code
								JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
								JOIN app.t_mtr_port P on P.id = AR.port_id
						WHERE
								UP.assignment_date = '{$date}'
						AND 
								UP.port_id = {$port} 
						AND 
								UP.shift_id = {$shift}
						AND
								BO.status = 2 
						GROUP BY
								SH.shift_name,
								BV.fare
						UNION ALL
						SELECT
								SH.shift_name,
								COUNT(DISTINCT BP.id ) as produksi,
								COUNT(DISTINCT BP.id) * BP.fare as pendapatan
						FROM
								app.t_trx_assignment_user_pos UP
								JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = UP.assignment_code AND AR.status = 2
								JOIN core.t_mtr_team T ON T.team_code = AR.team_code
								JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
								JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
								JOIN app.t_trx_check_in S on S.created_by = OB.ob_code
								JOIN app.t_trx_booking BO on BO.booking_code = S.booking_code AND BO.channel IN ('web', 'mobile','ifcs','b2b') AND BO.ship_class = {$shipClass}
								JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1
								JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code AND BP.service_id = 1 
										AND BP.ticket_number = S.ticket_number
								JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1 

								--AND PT.id != 3 

								JOIN app.t_mtr_port P on P.id = AR.port_id
						WHERE
								UP.assignment_date = '{$date}'
						AND 
								UP.port_id = {$port} 
						AND 
								UP.shift_id = {$shift}
						AND
								BO.status = 2 
						GROUP BY
								SH.shift_name,
								BP.fare
						UNION ALL
						SELECT
								SH.shift_name,
								COUNT(DISTINCT BV.id) as produksi,
								COUNT(DISTINCT BV.id) * BV.fare as pendapatan
						FROM
								app.t_trx_assignment_user_pos UP
								JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = UP.assignment_code AND AR.status = 2
								JOIN core.t_mtr_team T ON T.team_code = AR.team_code
								JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
								JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
								JOIN app.t_trx_check_in_vehicle S on S.created_by = OB.ob_code
								JOIN app.t_trx_booking BO on BO.booking_code = S.booking_code AND BO.channel IN ('web', 'mobile','ifcs','b2b') AND BO.ship_class = {$shipClass}
								JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1
								JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code 
										AND BV.ticket_number = S.ticket_number
								JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
								JOIN app.t_mtr_port P on P.id = AR.port_id
						WHERE
								UP.assignment_date = '{$date}'
						AND 
								UP.port_id = {$port} 
						AND 
								UP.shift_id = {$shift}
						AND
								BO.status = 2 
						GROUP BY
								SH.shift_name,
								BV.fare
						UNION ALL
						SELECT
								SH.shift_name,
								COUNT(DISTINCT BP.id ) as produksi,
								COUNT(DISTINCT BP.id) * BP.fare as pendapatan
						FROM
								app.t_trx_assignment_user_pos UP
								JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = UP.assignment_code AND AR.status = 2
								JOIN core.t_mtr_team T ON T.team_code = AR.team_code
								JOIN app.t_trx_opening_balance_vm OB ON OB.assignment_code = UP.assignment_code
								JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
								JOIN app.t_trx_check_in S on S.created_by = OB.ob_code
								JOIN app.t_trx_booking BO on BO.booking_code = S.booking_code AND BO.channel IN ('web', 'mobile','ifcs','b2b') AND BO.ship_class = {$shipClass}
								JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1
								JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code AND BP.service_id = 1 
										AND BP.ticket_number = S.ticket_number
								JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1 

								-- AND PT.id != 3 

								JOIN app.t_mtr_port P on P.id = AR.port_id
						WHERE
								UP.assignment_date = '{$date}'
						AND 
								UP.port_id = {$port} 
						AND 
								UP.shift_id = {$shift}
						AND
								BO.status = 2 
						GROUP BY
								SH.shift_name,
								BP.fare
				) data 
				group by shift_name

		";

		$result = $this->db->query($sql)->row();

		return $result;
	}

	public function getSummaryTerjual ($date, $port, $shipClass, $shift) {
		$sql1 = "SELECT
					SH.shift_name,
					COUNT(DISTINCT BP.id ) as produksi,
					COUNT(DISTINCT BP.id) * BP.fare as pendapatan
				FROM
					app.t_trx_assignment_regu AR
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
					JOIN app.t_trx_opening_balance OB ON OB.assignment_code = AR.assignment_code
					JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
					JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
					JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1 AND BO.ship_class = {$shipClass}
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code AND BP.service_id = 1
					JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1 
					JOIN app.t_mtr_port P on P.id = AR.port_id
				WHERE
					AR.assignment_date = '{$date}' 
				AND 
					AR.port_id = {$port} 
				AND 
					AR.shift_id = {$shift}
				AND
					BO.status = 2 
				AND 
					AR.status = 2
				GROUP BY
					SH.shift_name,
					BP.fare";

		$sql2 = "SELECT
					SH.shift_name,
					COUNT(DISTINCT BV.id) as produksi,
					COUNT(DISTINCT BV.id) * BV.fare as pendapatan
				FROM
					app.t_trx_assignment_regu AR
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
					JOIN app.t_trx_opening_balance OB ON OB.assignment_code = AR.assignment_code
					JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
					JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
					JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1 AND BO.ship_class = {$shipClass}
					JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code
					JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
					JOIN app.t_mtr_port P on P.id = AR.port_id
				WHERE
					AR.assignment_date = '{$date}'
				AND 
					AR.port_id = {$port} 
				AND 
					AR.shift_id = {$shift}
				AND
					BO.status = 2 
				AND 
					AR.status = 2
				GROUP BY
					SH.shift_name,
					BV.fare";
		$sql3 = "SELECT
					SH.shift_name,
					COUNT(DISTINCT BP.id ) as produksi,
					COUNT(DISTINCT BP.id) * BP.fare as pendapatan
				FROM
					app.t_trx_assignment_regu AR
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
					JOIN app.t_trx_opening_balance_vm OB ON OB.assignment_code = AR.assignment_code
					JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
					JOIN app.t_trx_sell_vm S ON S.ob_code = OB.ob_code
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
					JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1 AND BO.ship_class = {$shipClass}
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code AND BP.service_id = 1
					JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1 
					JOIN app.t_mtr_port P on P.id = AR.port_id
				WHERE
					AR.assignment_date = '{$date}' 
				AND 
					AR.port_id = {$port} 
				AND 
					AR.shift_id = {$shift}
				AND
					BO.status = 2 
				AND 
					AR.status = 2
				GROUP BY
					SH.shift_name,
					BP.fare";
		$sql4 = "SELECT
					SH.shift_name,
					COUNT(DISTINCT BV.id) as produksi,
					COUNT(DISTINCT BV.id) * BV.fare as pendapatan
				FROM
					app.t_trx_assignment_regu AR
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
					JOIN app.t_trx_opening_balance_vm OB ON OB.assignment_code = AR.assignment_code
					JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
					JOIN app.t_trx_sell_vm S ON S.ob_code = OB.ob_code
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
					JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1 AND BO.ship_class = {$shipClass}
					JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code
					JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
					JOIN app.t_mtr_port P on P.id = AR.port_id
				WHERE
					AR.assignment_date = '{$date}'
				AND 
					AR.port_id = {$port} 
				AND 
					AR.shift_id = {$shift}
				AND
					BO.status = 2 
				AND 
					AR.status = 2
				GROUP BY
					SH.shift_name,
					BV.fare";
		$sql5 = "SELECT
					SH.shift_name,
					COUNT(DISTINCT BP.id ) as produksi,
					COUNT(DISTINCT BP.id) * BP.fare as pendapatan
				FROM
					app.t_trx_assignment_regu AR
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
					JOIN app.t_trx_opening_balance OB ON OB.assignment_code = AR.assignment_code
					JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
					JOIN app.t_trx_check_in S on S.created_by = OB.ob_code
					JOIN app.t_trx_booking BO on BO.booking_code = S.booking_code AND BO.channel IN ('web', 'mobile','ifcs','b2b') AND BO.ship_class = {$shipClass}
					JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code AND BP.service_id = 1 
						AND BP.ticket_number = S.ticket_number
					JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1 
					JOIN app.t_mtr_port P on P.id = AR.port_id
				WHERE
					AR.assignment_date = '{$date}'
				AND 
					AR.port_id = {$port} 
				AND 
					AR.shift_id = {$shift}
				AND
					BO.status = 2 
				AND 
					AR.status = 2
				GROUP BY
					SH.shift_name,
					BP.fare";
		$sql6 = "SELECT
					SH.shift_name,
					COUNT(DISTINCT BV.id) as produksi,
					COUNT(DISTINCT BV.id) * BV.fare as pendapatan
				FROM
					app.t_trx_assignment_regu AR
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
					JOIN app.t_trx_opening_balance OB ON OB.assignment_code = AR.assignment_code
					JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
					JOIN app.t_trx_check_in_vehicle S on S.created_by = OB.ob_code
					JOIN app.t_trx_booking BO on BO.booking_code = S.booking_code AND BO.channel IN ('web', 'mobile','ifcs','b2b') AND BO.ship_class = {$shipClass}
					JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1
					JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = BO.booking_code 
						AND BV.ticket_number = S.ticket_number
					JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id AND VC.status=1
					JOIN app.t_mtr_port P on P.id = AR.port_id
				WHERE
					AR.assignment_date = '{$date}'
				AND 
					AR.port_id = {$port} 
				AND 
					AR.shift_id = {$shift}
				AND
					BO.status = 2 
				AND 
					AR.status = 2
				GROUP BY
					SH.shift_name,
					BV.fare";
		$sql7 = "SELECT
					SH.shift_name,
					COUNT(DISTINCT BP.id ) as produksi,
					COUNT(DISTINCT BP.id) * BP.fare as pendapatan
				FROM
					app.t_trx_assignment_regu AR
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
					JOIN app.t_trx_opening_balance_vm OB ON OB.assignment_code = AR.assignment_code
					JOIN app.t_mtr_shift SH ON SH.id = OB.shift_id
					JOIN app.t_trx_check_in S on S.created_by = OB.ob_code
					JOIN app.t_trx_booking BO on BO.booking_code = S.booking_code AND BO.channel IN ('web', 'mobile','ifcs','b2b') AND BO.ship_class = {$shipClass}
					JOIN app.t_mtr_ship_class SC ON SC.id = BO.ship_class AND SC.status = 1
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code AND BP.service_id = 1 
						AND BP.ticket_number = S.ticket_number
					JOIN app.t_mtr_passanger_type PT ON PT.id = BP.passanger_type_id AND PT.status=1 
					JOIN app.t_mtr_port P on P.id = AR.port_id
				WHERE
					AR.assignment_date = '{$date}'
				AND 
					AR.port_id = {$port} 
				AND 
					AR.shift_id = {$shift}
				AND
					BO.status = 2 
				AND 
					AR.status = 2
				GROUP BY
					SH.shift_name,
					BP.fare";

		$result1 = $this->db->query($sql1)->result();
		$result2 = $this->db->query($sql2)->result();
		$result3 = $this->db->query($sql3)->result();
		$result4 = $this->db->query($sql4)->result();
		$result5 = $this->db->query($sql5)->result();
		$result6 = $this->db->query($sql6)->result();
		$result7 = $this->db->query($sql7)->result();
		$result  = (object) array_merge((array) $result1, (array) $result2, (array) $result3, (array) $result4, (array) $result5, (array) $result6, (array) $result7);        
		$arr     = json_decode(json_encode($result), TRUE);
		if (empty($arr)) {
			return null;
		}
		else{
			$x = new stdClass();
			$x->produksi   = 0;
			$x->pendapatan = 0;
			foreach ($result as $key => $value) {
				$x->produksi   = $x->produksi + $value->produksi;
				$x->pendapatan = $x->pendapatan + $value->pendapatan;
				$x->shift_name = $value->shift_name;
			}
			
			return $x;
		}
		
	}

 public function check_summary_sap($port, $class, $shift, $date, $type) {
		$date = $this->db->escape($date);
		$sql = "
				SELECT * FROM app.t_trx_summary_sap WHERE shift_date = {$date} AND status = 1 AND port_id = {$port} AND shift_id = {$shift} AND type = {$type} order by id desc limit 1
		";
		$result = $this->db->query($sql)->row();
		return $result;
}

 public function insert_summary_sap($data) {
		return $this->db->insert(
												'app.t_trx_summary_sap', $data
										);
}




	public function getSummaryTertagih_06102021($date, $port, $class, $shift) {

			$sql = "
					SELECT 
							count(ttbp.ticket_number) as produksi,
							tms.shift_name as shift,
							sum(ttbp.fare) as pendapatan
					FROM app.t_trx_booking ttb
					JOIN app.t_trx_booking_passanger ttbp ON ttbp.booking_code = ttb.booking_code AND ttbp.service_id = 1 
					JOIN app.t_trx_boarding_passanger ttbpas ON ttbpas.ticket_number = ttbp.ticket_number AND ttbpas.status = 1
					JOIN app.t_trx_open_boarding ttob ON ttob.boarding_code = ttbpas.boarding_code AND ttob.shift_date = '{$date}' AND ttob.shift_id = {$shift}
					JOIN app.t_mtr_shift tms ON tms.id = ttob.shift_id
					JOIN app.t_mtr_passanger_type tmpt ON tmpt.id = ttbp.passanger_type_id 
					
					-- AND tmpt.id != 3 

					JOIN app.t_mtr_port tmp ON tmp.id = ttob.port_id
					JOIN app.t_trx_assignment_regu ttar ON ttar.assignment_date = ttob.shift_date AND ttar.shift_id = ttob.shift_id  AND ttar.port_id = {$port}  and ttar.status = 2
					JOIN core.t_mtr_team tmt ON tmt.team_code = ttar.team_code
					JOIN app.t_mtr_ship tmsp ON tmsp.id = ttob.ship_id
					JOIN app.t_mtr_ship_class tmsc ON tmsc.id = tmsp.ship_class
					JOIN app.t_mtr_branch tmb ON tmb.port_id = ttob.port_id AND tmb.ship_class = ttob.ship_class
					JOIN app.t_mtr_ship_company tmscy ON tmscy.id = tmsp.ship_company_id 
					LEFT JOIN app.t_mtr_sailing_company sc ON sc.company_id = tmscy.id  and sc.port_id = tmp.id AND sc.status = 1 
					WHERE tmp.id = {$port}
					AND ttob.ship_class = {$class}
					GROUP BY
							tms.shift_name
					UNION
					SELECT 
							count(ttbv.ticket_number) as produksi,
							tms.shift_name as shift,
							sum(ttbv.fare) as pendapatan
					FROM app.t_trx_booking ttb
					JOIN app.t_trx_booking_vehicle ttbv ON ttbv.booking_code = ttb.booking_code 
					JOIN app.t_trx_boarding_vehicle ttbrv ON ttbrv.ticket_number = ttbv.ticket_number AND ttbrv.status = 1
					JOIN app.t_trx_open_boarding ttob ON ttob.boarding_code = ttbrv.boarding_code AND ttob.shift_date = '{$date}'  AND ttob.shift_id = {$shift}
					JOIN app.t_mtr_shift tms ON tms.id = ttob.shift_id
					JOIN app.t_mtr_vehicle_class tmvc ON tmvc.id = ttbv.vehicle_class_id AND tmvc.status = 1
					JOIN app.t_mtr_port tmp ON tmp.id = ttob.port_id
					JOIN app.t_trx_assignment_regu ttar ON ttar.assignment_date = ttob.shift_date AND ttar.shift_id = ttob.shift_id  AND ttar.port_id = {$port}  and ttar.status = 2
					JOIN core.t_mtr_team tmt ON tmt.team_code = ttar.team_code
					JOIN app.t_mtr_ship tmsp ON tmsp.id = ttob.ship_id
					JOIN app.t_mtr_ship_class tmsc ON tmsc.id = tmsp.ship_class
					JOIN app.t_mtr_branch tmb ON tmb.port_id = ttob.port_id AND tmb.ship_class = ttob.ship_class
					JOIN app.t_mtr_ship_company tmscy ON tmscy.id = tmsp.ship_company_id 
					LEFT JOIN app.t_mtr_sailing_company sc ON sc.company_id = tmscy.id  and sc.port_id = tmp.id AND sc.status = 1 
					WHERE tmp.id = {$port}
					AND ttob.ship_class = {$class}
					GROUP BY
							tms.shift_name
					ORDER BY 
							shift
			";

			$result = $this->db->query($sql)->result();

			return $result;
	}

	public function getSummaryTertagih($date, $port, $class, $shift) {        

        
        $sql1 = "SELECT 
                count(ttbp.ticket_number) as produksi,
                tms.shift_name as shift,
                sum(ttbp.fare) as pendapatan
            FROM app.t_trx_open_boarding ttob
            JOIN app.t_trx_boarding_passanger ttbpas ON ttob.boarding_code = ttbpas.boarding_code AND ttob.shift_date = '{$date}' AND ttob.shift_id = {$shift}
            JOIN app.t_trx_booking_passanger ttbp ON ttbpas.ticket_number = ttbp.ticket_number AND ttbpas.status = 1 AND ttbp.service_id = 1 
            JOIN app.t_trx_booking book ON book.booking_code = ttbp.booking_code 
            JOIN app.t_mtr_shift tms ON tms.id = ttob.shift_id
            JOIN app.t_mtr_passanger_type tmpt ON tmpt.id = ttbp.passanger_type_id 
            JOIN app.t_mtr_port tmp ON tmp.id = ttob.port_id
            JOIN app.t_trx_assignment_regu ttar ON ttar.assignment_date = ttob.shift_date AND ttar.shift_id = ttob.shift_id  AND ttar.port_id = {$port}  and ttar.status = 2
            JOIN core.t_mtr_team tmt ON tmt.team_code = ttar.team_code
            JOIN app.t_mtr_ship tmsp ON tmsp.id = ttob.ship_id
            JOIN app.t_mtr_ship_class tmsc ON tmsc.id = tmsp.ship_class
            JOIN app.t_mtr_branch tmb ON tmb.port_id = ttob.port_id AND tmb.ship_class = ttob.ship_class
            JOIN app.t_mtr_ship_company tmscy ON tmscy.id = tmsp.ship_company_id
            WHERE tmp.id = {$port}
            AND ttob.ship_class = {$class}
            GROUP BY
                tms.shift_name";
        $sql2 = "SELECT 
                count(ttbv.ticket_number) as produksi,
                tms.shift_name as shift,
                sum(ttbv.fare) as pendapatan
            FROM app.t_trx_open_boarding ttob  
            JOIN app.t_trx_boarding_vehicle ttbrv ON ttob.boarding_code = ttbrv.boarding_code AND ttob.shift_date = '{$date}'  AND ttob.shift_id = {$shift} 
            JOIN app.t_trx_booking_vehicle ttbv ON ttbrv.ticket_number = ttbv.ticket_number AND ttbrv.status = 1 
            JOIN app.t_trx_booking book ON book.booking_code = ttbv.booking_code 
            JOIN app.t_mtr_shift tms ON tms.id = ttob.shift_id
            JOIN app.t_mtr_vehicle_class tmvc ON tmvc.id = ttbv.vehicle_class_id AND tmvc.status = 1
            JOIN app.t_mtr_port tmp ON tmp.id = ttob.port_id
            JOIN app.t_trx_assignment_regu ttar ON ttar.assignment_date = ttob.shift_date AND ttar.shift_id = ttob.shift_id  AND ttar.port_id = {$port}  and ttar.status = 2
            JOIN core.t_mtr_team tmt ON tmt.team_code = ttar.team_code
            JOIN app.t_mtr_ship tmsp ON tmsp.id = ttob.ship_id
            JOIN app.t_mtr_ship_class tmsc ON tmsc.id = tmsp.ship_class
            JOIN app.t_mtr_branch tmb ON tmb.port_id = ttob.port_id AND tmb.ship_class = ttob.ship_class
            JOIN app.t_mtr_ship_company tmscy ON tmscy.id = tmsp.ship_company_id 
            WHERE tmp.id = {$port}
            AND ttob.ship_class = {$class}
            GROUP BY
                tms.shift_name
            ORDER BY 
                shift";
        $result1 = $this->db->query($sql1)->result();
        $result2 = $this->db->query($sql2)->result();
        $result  = (object) array_merge((array) $result1, (array) $result2);        
        $arr     = json_decode(json_encode($result), TRUE);
        if (empty($arr)) {
            return null;
        }
        return $result;
    }	

}
