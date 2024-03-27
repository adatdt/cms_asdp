<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_ticket_tracking extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function get_ticket(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			// 0 =>'B.id',
			1 =>'created_on',
			2 =>'ordering',
		);

		$order_column = $field[$order_column];

		$where = "";

		if (!empty($cari)){
			$where .=" WHERE ticket_number = '$like' ";
		}

		$sql = "SELECT * FROM(SELECT created_on, BP.status, BP.ticket_number, 'booking' as table_name, 'passanger' as service, BP.service_id as service_id, '1' as ordering
				FROM app.t_trx_booking_passanger BP
				WHERE BP.status != -5 and bp.ticket_number='{$like}'  
				UNION
				SELECT P.created_on, P.status, BP.ticket_number, 'payment' as table_name, 'passanger' as service, BP.service_id as service_id, '2' as ordering
				FROM app.t_trx_booking B
				JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code and bp.status !='-5'
				JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
				WHERE P.status = 1  and bp.ticket_number='{$like}'
				UNION
				SELECT CI.created_on, CI.status, BP.ticket_number, 'cetak boarding pass' as table_name, 'passanger' as service, BP.service_id as service_id, '3' as ordering
				FROM app.t_trx_booking_passanger BP
				JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
				WHERE CI.status = 1 and bp.ticket_number='{$like}'
				UNION
				SELECT GI.created_on, GI.status, BP.ticket_number, 'gate in' as table_name, 'passanger' as service, BP.service_id as service_id, '4' as ordering
				FROM app.t_trx_booking_passanger BP
				JOIN app.t_trx_gate_in GI ON GI.ticket_number = BP.ticket_number
				WHERE GI.status = 1 and bp.ticket_number='{$like}'
				UNION
				SELECT BRP.created_on, BRP.status, BP.ticket_number, 'boarding' as table_name, 'passanger' as service, BP.service_id as service_id, '5' as ordering
				FROM app.t_trx_booking_passanger BP
				JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
				WHERE BRP.status = 1 and bp.ticket_number='{$like}'
				UNION
				SELECT SPD.created_on, SPD.status, BP.ticket_number, 'muntah kapal' as table_name, 'passanger' as service, BP.service_id as service_id, '6' as ordering
				FROM app.t_trx_booking_passanger BP
				JOIN app.t_trx_switch_ship_passanger_detail SPD ON SPD.ticket_number = BP.ticket_number
				WHERE SPD.status = 1 and bp.ticket_number='{$like}'
				UNION
				SELECT SAPD.created_on, SAPD.status, BP.ticket_number, 'pindah kapal' as table_name, 'passanger' as service, BP.service_id as service_id, '7' as ordering
				FROM app.t_trx_booking_passanger BP
				JOIN app.t_trx_switch_ship_all_passanger_detail SAPD ON SAPD.ticket_number = BP.ticket_number
				WHERE SAPD.status = 1 and bp.ticket_number='{$like}'
				UNION
				select RF.created_on, RF.status, BP.ticket_number, 'refund' as table_name, 'passanger' as service,
					BP.service_id as service_id, '8' as ordering
				from app.t_trx_booking_passanger BP 
				JOIN app.t_trx_refund RF on BP.booking_code=RF.booking_code
				where BP.ticket_number='{$like}'
				UNION
				select RF.created_on, RF.status, BP.ticket_number, 'reschedule' as table_name, 'passanger' as service,
					BP.service_id as service_id, '9' as ordering
				from app.t_trx_booking_passanger BP 
				JOIN app.t_trx_reschedule RF on BP.booking_code=RF.booking_code
				where BP.ticket_number='{$like}'

			) A {$where} ORDER BY created_on, ordering ASC";

			// die($sql); exit;

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();

		if($records_total == 0){
			$sql = "SELECT * FROM(SELECT created_on, BV.status, BV.ticket_number, 'booking' as table_name, 'vehicle' as service, BV.service_id as service_id, '1' as ordering
					FROM app.t_trx_booking_vehicle BV
					WHERE BV.status != -5  and BV.ticket_number='{$like}'
					UNION
					SELECT P.created_on, P.status, BV.ticket_number, 'payment' as table_name, 'vehicle' as service, BV.service_id as service_id, '2' as ordering
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
					JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
					WHERE P.status = 1 and BV.ticket_number='{$like}'
					UNION
					SELECT CI.created_on, CI.status, BV.ticket_number, 'cetak boarding pass' as table_name, 'vehicle' as service, BV.service_id as service_id, '3' as ordering
					FROM app.t_trx_booking_vehicle BV
					JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
					WHERE CI.status = 1 and BV.ticket_number='{$like}'
					UNION
					SELECT GI.created_on, GI.status, BV.ticket_number, 'gate in' as table_name, 'vehicle' as service, BV.service_id as service_id, '4' as ordering
					FROM app.t_trx_booking_vehicle BV
					JOIN app.t_trx_gate_in_vehicle GI ON GI.ticket_number = BV.ticket_number
					WHERE GI.status = 1 and BV.ticket_number='{$like}'
					UNION
					SELECT BRV.created_on, BRV.status, BV.ticket_number, 'boarding' as table_name, 'vehicle' as service, BV.service_id as service_id, '5' as ordering
					FROM app.t_trx_booking_vehicle BV
					JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
					WHERE BRV.status = 1 and BV.ticket_number='{$like}'
					UNION
					SELECT SVD.created_on, SVD.status, BV.ticket_number, 'muntah kapal' as table_name, 'vehicle' as service, BV.service_id as service_id, '6' as ordering
					FROM app.t_trx_booking_vehicle BV
					JOIN app.t_trx_switch_ship_vehicle_detail SVD ON SVD.ticket_number = BV.ticket_number
					WHERE SVD.status = 1 and BV.ticket_number='{$like}'
					UNION
					SELECT SAVD.created_on, SAVD.status, BV.ticket_number, 'pindah kapal' as table_name, 'vehicle' as service, BV.service_id as service_id, '7' as ordering
					FROM app.t_trx_booking_vehicle BV
					JOIN app.t_trx_switch_ship_all_vehicle_detail SAVD ON SAVD.ticket_number = BV.ticket_number
					WHERE SAVD.status = 1 and BV.ticket_number='{$like}'
					UNION
					select 
						RF.created_on, RF.status, BV.ticket_number, 'refund' as table_name, 'vehicle' as service, BV.service_id as service_id, '8' as ordering
					from app.t_trx_booking_vehicle BV 
					JOIN app.t_trx_refund RF on BV.booking_code=RF.booking_code
					where BV.ticket_number='{$like}'
					UNION
					select 
						RF.created_on, RF.status, BV.ticket_number, 'reschedule' as table_name, 'vehicle' as service, BV.service_id as service_id, '9' as ordering
					from app.t_trx_booking_vehicle BV 
					JOIN app.t_trx_reschedule RF on BV.booking_code=RF.booking_code
					where BV.ticket_number='{$like}'

				) B {$where} ORDER BY created_on, ordering ASC";

			$query         = $this->dbView->query($sql);
			$records_total = $query->num_rows();
		}
			// die($sql);
		// $sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		$dataLength = count((array)$rows_data);
		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->created_on=format_dateTimeHis($row->created_on);

			// $ticket_number=$this->enc->encode($row->ticket_number);
			// $detail_url 	 = site_url($this->_module."/detail/{$ticket_number}/{$row->table_name}");

			// $row->actions = '';
			// $row->actions  = generate_button_new($this->_module, 'detail', $detail_url);
			if($dataLength == $i){
				$row->dot = '<i class="dot" style="height: 25px; width: 25px; background-color: green; border-radius: 50%; display: inline-block;"></i>';
			}else{
				$row->dot = '<i class="dot" style="height: 25px; width: 25px; background-color: grey; border-radius: 50%; display: inline-block;"></i>';
			}

			$row->table_name = strtoupper($row->table_name);
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

	public function get_booking_track(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			// 0 =>'B.id',
			1 =>'created_on',
			2 =>'ordering',
		);

		$order_column = $field[$order_column];


		$sql = "

			SELECT 
			  	created_on, 
			  	B.status, 
			  	B.booking_code, 
			  	'booking' as table_name,
			  	-- 'passanger' as service,
			  	(
			  		case
			  	    when B.service_id=1 then 'passanger'
			  	    else 'vehicle'
			  	    end

			  	) as service, 
			  	B.service_id as service_id, 
			  	'1' as ordering
			FROM app.t_trx_booking B
			WHERE 
			  B.status != -5 and
			  B.booking_code='$like'  
			UNION
			SELECT 
			  	P.created_on, 
			  	P.status, 
			  	B.booking_code, 
			  	'payment' as table_name, 
			  	-- 'passanger' as service, 
			  	(
			  		case
			  	    when B.service_id=1 then 'passanger'
			  	    else 'vehicle'
			  	    end

			  	) as service,
			  	B.service_id as service_id, 
			  	'2' as ordering
			FROM app.t_trx_booking B
				JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
				WHERE P.status = 1  and B.booking_code='$like'
			UNION
			SELECT 
				R.created_on, 
				R.status, 
				B.booking_code, 
				'refund' as table_name, 
				(
					case
					when B.service_id=1 then 'passanger'
					else 'vehicle'
					end

				) as service,
				B.service_id as service_id, 
				'3' as ordering
				FROM app.t_trx_booking B
				JOIN app.t_trx_refund R ON R.booking_code = B.booking_code
				WHERE  B.booking_code='{$like}'
			UNION
			SELECT 
			R.created_on, 
			R.status, 
			B.booking_code, 
			'reschedule' as table_name, 
			(
				case
				when B.service_id=1 then 'passanger'
				else 'vehicle'
				end

			) as service,
			B.service_id as service_id, 
			'4' as ordering
			FROM app.t_trx_booking B
			JOIN app.t_trx_reschedule R ON R.booking_code = B.booking_code
			WHERE  B.booking_code='{$like}'




			ORDER BY created_on, ordering ASC";

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();

		// if($records_total == 0){
		// 	$sql = "
		// 		SELECT 
		// 		  	created_on, 
		// 		  	B.status, 
		// 		  	B.booking_code, 
		// 		  	'booking' as table_name,
		// 		  	'vehicle' as service, 
		// 		  	B.service_id as service_id, 
		// 		  	'1' as ordering
		// 		FROM app.t_trx_booking B
		// 		WHERE 
		// 		  B.status != -5 and
		// 		  B.booking_code='$like'  
		// 		UNION
		// 		SELECT 
		// 		  	P.created_on, 
		// 		  	P.status, 
		// 		  	B.booking_code, 
		// 		  	'payment' as table_name, 
		// 		  	'vehicle' as service, 
		// 		  	B.service_id as service_id, 
		// 		  	'2' as ordering
		// 		FROM app.t_trx_booking B
		// 			JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
		// 			WHERE P.status = 1  and B.booking_code='$like'
		// 		ORDER BY created_on, ordering ASC 
		// 	";

		// 	$query         = $this->dbView->query($sql);
		// 	$records_total = $query->num_rows();
		// }

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		$dataLength = count((array)$rows_data);
		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->created_on=format_dateTimeHis($row->created_on);

			if($dataLength == $i){
				$row->dot = '<i class="dot" style="height: 25px; width: 25px; background-color: green; border-radius: 50%; display: inline-block;"></i>';
			}else{
				$row->dot = '<i class="dot" style="height: 25px; width: 25px; background-color: grey; border-radius: 50%; display: inline-block;"></i>';
			}

			$row->table_name = strtoupper($row->table_name);
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



	public function get_data_ticket(){
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$searchCari = $this->input->post('searchCari');		

		$getParamCheckIn = $this->select_data("app.t_mtr_custom_param", " where param_name in('checkin_expired_web_eksekutif','checkin_expired_web') ")->result();
		$masterCheckinExpired = array_combine(array_column($getParamCheckIn,"param_name"),array_column($getParamCheckIn,"param_value") );

		if($searchCari=='ticketNumber')
		{

			$ticket_number = trim(strtoupper($this->dbView->escape_like_str($cari)));

			if($service == "passanger"){
				$sql = "SELECT 
								BP.created_on,
								B.trans_number, 
								BP.booking_code, 
								BP.ticket_number, 
								S.name as service, 
								SC.name as ship_class,
								P.name as origin, 
								P2.name as destination,
								BP.service_id,
								BP.depart_date,
								BP.depart_time_start,
								BP.ticket_type,
								BP.status,
								BP.checkin_expired,
								BP.gatein_expired,
								BP.boarding_expired,
								fare,
								entry_fee,
								dock_fee,
								trip_fee,
								responsibility_fee,
								insurance_fee,
								ifpro_fee,
								 adm_fee,
								 BP.name as customer,
								 PT.name as type,
								 BP.id_number, BP.birth_date, BP.gender, BP.city
						FROM app.t_trx_booking B
						JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
						JOIN app.t_mtr_port P ON P.id = B.origin
						JOIN app.t_mtr_port P2 ON P2.id = B.destination
						JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
						JOIN app.t_mtr_service S ON S.id = BP.service_id
						-- JOIN app.t_mtr_passanger_type_id PT ON PT.id = BP.passanger_type_id
						left JOIN app.t_mtr_passanger_type_id PT ON PT.id = BP.id_type
						WHERE B.status != -5 AND (BP.ticket_number) = '$ticket_number'";						
						$masterStatus = $this->getStatus("t_trx_booking_passanger");
			}else{
				$sql = "SELECT 
							BV.created_on,
							B.trans_number,
							BV.booking_code,
							BV.ticket_number,
							S.name as service,
							SC.name as ship_class,
							P.name as origin,
							P2.name as destination,
							BV.depart_date,
							BV.depart_time_start,
							BV.ticket_type,
							BV.status,
							BV.checkin_expired,
							BV.service_id,
							BV.gatein_expired,
							BV.boarding_expired,
							VC.name as golongan,
							BV.fare,
							BV.entry_fee,
							BV.dock_fee,
							BV.trip_fee,
							BV.responsibility_fee,
							 BV.insurance_fee,
							  BV.ifpro_fee, BV.adm_fee, 
							  BP.name as customer,
							   PT.name as type, BP.id_number, BP.birth_date, BP.gender, BP.city, BV.id_number as plat, length, height, weight
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
					JOIN app.t_mtr_port P ON P.id = B.origin
					JOIN app.t_mtr_port P2 ON P2.id = B.destination
					JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
					JOIN app.t_mtr_service S ON S.id = BV.service_id
					JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
					-- JOIN app.t_mtr_passanger_type_id PT ON PT.id = BP.passanger_type_id
					left JOIN app.t_mtr_passanger_type_id PT ON PT.id = BP.id_type					
					WHERE B.status != -5 
					--AND BP.id IN(SELECT MIN(BP2.id) FROM app.t_trx_booking_passanger BP2 WHERE BP2.booking_code=BP.booking_code)
					 AND (BV.ticket_number) = '$ticket_number'";
					 $masterStatus = $this->getStatus("t_trx_booking_vehicle");
			}

			// die($sql); exit;
			$query     = $this->dbView->query($sql);
			$data = $query->row();

			$getDriversAssign = $this->getDriversAssign($data->booking_code);
			$dataDriversAssign[""] =""; 
			$dataDriversAssignBirthDate[""] =""; 
			if(!empty($getDriversAssign))
			{
				$dataDriversAssign += array_combine(array_column($getDriversAssign,"booking_code"), array_column($getDriversAssign,"name") );

				$dataDriversAssignBirthDate += array_combine(array_column($getDriversAssign,"booking_code"), array_column($getDriversAssign,"birth_date") );
			}

			// expired checkin
			$paramCheckinExpired = $data->service_id==2?$masterCheckinExpired['checkin_expired_web_eksekutif']:$masterCheckinExpired['checkin_expired_web'];
			$checkin_expired_date = $data->depart_date." ".$data->depart_time_start;

			// echo $checkin_expired_date; exit;

			$data->checkin_expired = 	 date('Y-m-d H:i:s ', strtotime($checkin_expired_date . ' +'.$paramCheckinExpired .' hours'));

			$data->checkin_expired = empty($data->checkin_expired )?"":format_dateTimeHis($data->checkin_expired) ;			

									
			$birth_date1 = empty($data->birth_date)?"":format_date($data->birth_date);
			$data->birth_date = empty($dataDriversAssignBirthDate[$data->booking_code])?$birth_date1:format_date($dataDriversAssignBirthDate[$data->booking_code]);

			$data->customer = empty($dataDriversAssign[$data->booking_code])?$data->customer:$dataDriversAssign[$data->booking_code];

			$data->depart_date=format_date($data->depart_date).' '.format_time($data->depart_time_start);
			$data->created_on=format_dateTimeHis($data->created_on);
			
			$data->gatein_expired ? $data->gatein_expired=format_dateTimeHis($data->gatein_expired) : '';
			$data->boarding_expired ? $data->boarding_expired=format_dateTimeHis($data->boarding_expired) : '';
			$data->fare=idr_currency($data->fare);
			$data->trip_fee=idr_currency($data->trip_fee);
			$data->dock_fee=idr_currency($data->dock_fee);
			$data->ifpro_fee=idr_currency($data->ifpro_fee);
			$data->adm_fee=idr_currency($data->adm_fee);
			$data->insurance_fee=idr_currency($data->insurance_fee);
			$data->entry_fee=idr_currency($data->entry_fee);
			$data->responsibility_fee=idr_currency($data->responsibility_fee);
			$data->service = strtoupper($data->service);
			$data->ship_class = strtoupper($data->ship_class);
			$data->length=number_format($data->length);
			$data->height=number_format($data->height);
			$data->weight=number_format($data->weight);
			// $data->ticket_type == 1 ? $data->ticket_type = 'NORMAL' : $data->ticket_type = 'SAB';

			if($data->ticket_type==1)
			{
				$data->ticket_type = 'NORMAL';
			}
			else if($data->ticket_type==3)
			{
				$data->ticket_type = 'TICKET SOBEK';
			}
			else
			{
				$data->ticket_type = 'SKPT';				
			}

			/*
			if($data->status == 0){
				$data->status = '<span class="label label-success">Booking</span>';
			} elseif ($data->status == 1) {
				// $data->status = '<span class="label label-success">Update Payment</span>';
				$data->status = '<span class="label label-success">Menunggu Pembayaran</span>';
			} elseif ($data->status == 2) {
				$data->status = '<span class="label label-success">Payment</span>';
			} elseif ($data->status == 3) {
				$data->status = '<span class="label label-success">Cetak Boarding Pass</span>';
			} elseif ($data->status == 4) {
				$data->status = '<span class="label label-success">Gate In</span>';
			} elseif ($data->status == 5) {
				$data->status = '<span class="label label-success">Boarding</span>';
			} elseif ($data->status == 6) {
				$data->status = '<span class="label label-success">Temporary Boarding</span>';
			} elseif ($data->status == 7) {
				$data->status = '<span class="label label-success">Gate In</span>';
			} elseif ($data->status == 8) {
				$data->status = '<span class="label label-success">Boarding</span>';
			} elseif ($data->status == 9) {
				$data->status = '<span class="label label-success">Force Majeure</span>';
			} elseif ($data->status == 10 || $data->status == 11 || $data->status == 12) {
				$data->status = '<span class="label label-success">Ticket Expired</span>';
			} elseif ($data->status == 16) {
				$data->status = '<span class="label label-success">Refund</span>';
			} elseif ($data->status == 17) {
				$data->status = '<span class="label label-success">Refund Force Majeure</span>';
			} elseif ($data->status == 18) {
				$data->status = '<span class="label label-success">Reschedule</span>';
			} elseif ($data->status == -3) {
				$data->status = '<span class="label label-success">Cancel Underpaid</span>';
			}

			*/

			$data->status = empty($masterStatus[$data->status])?"-":'<span class="label label-success">'.$masterStatus[$data->status].'</span>';
			$return=array("data"=>$data, "searchCari"=>"ticketNumber");
		}
		else
		{
			$booking_code = trim(strtoupper($this->dbView->escape_like_str($cari)));


			$sql="SELECT 
					B.created_on, 
					B.trans_number, 
					B.booking_code, 
					S.name as service, 
					SC.name as ship_class,
					P.name as origin, 
					P2.name as destination, 
					IV.total_amount as fare, 
					TS.description as status,
					B.checkin_expired,
					B.depart_date,
					B.depart_time_start,
					B.service_id,
					B.gatein_expired,
					B.depart_date,
					IV.customer_name as customer 
				FROM app.t_trx_booking B
				JOIN app.t_trx_invoice IV on B.trans_number=IV.trans_number
				JOIN app.t_mtr_port P ON P.id = B.origin
				JOIN app.t_mtr_port P2 ON P2.id = B.destination
				JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
				JOIN app.t_mtr_service S ON S.id = B.service_id
				join app.t_mtr_status TS on B.status=TS.status and tbl_name='t_trx_booking'
				WHERE B.status != -5 AND B.booking_code = '{$booking_code}' ";

			$query     = $this->dbView->query($sql);
			$row = $query->row();
			// $data = $query->row();

			$getDriversAssign = $this->getDriversAssign($row->booking_code);
			$dataDriversAssign[""] =""; 
			if(!empty($getDriversAssign))
			{
				$dataDriversAssign += array_combine(array_column($getDriversAssign,"booking_code"), array_column($getDriversAssign,"name") );
			}

			$row->customer = empty($dataDriversAssign[$row->booking_code])?$row->customer:$dataDriversAssign[$row->booking_code];		
			
			// expired checkin
			$paramCheckinExpired = $row->service_id==2?$masterCheckinExpired['checkin_expired_web_eksekutif']:$masterCheckinExpired['checkin_expired_web'];
			$checkin_expired_date = $row->depart_date." ".$row->depart_time_start;

			$row->checkin_expired = 	 date('Y-m-d H:i:s ', strtotime($checkin_expired_date . ' +'.$paramCheckinExpired .' day'));


			$data=array(
					"created_on"=>empty($row->created_on)?"":format_dateTimeHis($row->created_on), 
					"trans_number"=>$row->trans_number, 
					"booking_code"=>$row->booking_code, 
					"service"=>$row->service, 
					"ship_class"=>$row->ship_class,
					"origin"=>$row->origin, 
					"destination"=>$row->destination, 
					"fare"=>idr_currency($row->fare), 
					"status"=>'<span class="label label-success">'.$row->status.'</span>',
					"checkin_expired"=>empty($row->checkin_expired)?"":format_dateTimeHis($row->checkin_expired) ,
					"gatein_expired"=>empty($row->gatein_expired)?"":format_dateTimeHis($row->gatein_expired),
					"customer"=>$row->customer,
					"depart_date"=>format_date($row->depart_date)
			);


			
			$sqlPassanger = "SELECT BP.created_on, BP.age, B.trans_number, BP.booking_code, BP.ticket_number, S.name as service, SC.name as ship_class, P.name as origin, P2.name as destination, BP.depart_date, BP.depart_time_start, BP.ticket_type, BP.status, BP.checkin_expired, BP.gatein_expired, BP.boarding_expired, fare, entry_fee, dock_fee, trip_fee, responsibility_fee, insurance_fee, ifpro_fee, adm_fee, BP.name as customer, PT.name as type, BP.id_number, BP.birth_date, BP.gender, PTY.name as golongan_pnp, BP.city
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
					JOIN app.t_mtr_port P ON P.id = B.origin
					JOIN app.t_mtr_port P2 ON P2.id = B.destination
					JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
					JOIN app.t_mtr_service S ON S.id = BP.service_id
					JOIN app.t_mtr_passanger_type PTY ON PTY.id = BP.passanger_type_id
					-- JOIN app.t_mtr_passanger_type_id PT ON PT.id = BP.passanger_type_id
					left JOIN app.t_mtr_passanger_type_id PT ON PT.id = BP.id_type
					WHERE B.status != -5 AND B.booking_code = '$booking_code'";

			$queryPassanger = $this->dbView->query($sqlPassanger)->result();
			
			$queryVehicle =array();
			if($service != "passanger")
			{
				$sqlVehicle = "SELECT BV.created_on, B.trans_number, BV.booking_code, BV.ticket_number, S.name as service, SC.name as ship_class, P.name as origin, P2.name as destination, BV.depart_date, BV.depart_time_start, BV.ticket_type, BV.status, BV.checkin_expired, BV.gatein_expired, BV.boarding_expired, VC.name as golongan, BV.fare, BV.entry_fee, BV.dock_fee, BV.trip_fee, BV.responsibility_fee, BV.insurance_fee, BV.ifpro_fee, BV.adm_fee, BP.name as customer, PT.name as type, BP.id_number, BP.birth_date, BP.gender, PTY.name as golongan_pnp , BP.city, BV.id_number as plat, length, height, weight
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
					JOIN app.t_mtr_port P ON P.id = B.origin
					JOIN app.t_mtr_port P2 ON P2.id = B.destination
					JOIN app.t_mtr_ship_class SC ON SC.id = B.ship_class
					JOIN app.t_mtr_service S ON S.id = BV.service_id
					JOIN app.t_mtr_vehicle_class VC ON VC.id = BV.vehicle_class_id
					-- JOIN app.t_mtr_passanger_type_id PT ON PT.id = BP.passanger_type_id
					JOIN app.t_mtr_passanger_type PTY ON PTY.id = BP.passanger_type_id
					JOIN app.t_mtr_passanger_type_id PT ON PT.id = BP.id_type
					WHERE B.status != -5 					
					 AND  B.booking_code = '$booking_code'
					 order by BP.ticket_number limit 1
					 ";

				$rowVehicle = $this->dbView->query($sqlVehicle)->result();				
				foreach ($rowVehicle as $key => $valueVehicle) {
					$valueVehicle->customer = empty($dataDriversAssign[$valueVehicle->booking_code])?$valueVehicle->customer:$dataDriversAssign[$valueVehicle->booking_code];

					$queryVehicle[] = $valueVehicle;
				}

				
			}			

			$return=array("data"=>$data, "searchCari"=>"bookingCode", "dataPassanger"=>$queryPassanger, "dataVehicle"=>$queryVehicle );	
		}

		return $return;
	}
	public function getStatus($tblName)
	{
		$getData = $this->select_data("app.t_mtr_status", " where tbl_name='".$tblName."'" )->result();

		$return = array_combine(array_column($getData,"status"),array_column($getData,"description"));
		return $return;

	}
	public function getDriversAssign($bookingCode)
	{
		$sqlDriverAssign = " SELECT 
												a.name, 
												a.booking_code,
												b.birth_date 
											from app.t_trx_print_manifest_vehicle a
											join app.t_trx_booking_passanger b on a.ticket_number = b.ticket_number
											where a.booking_code ='$bookingCode'
											and a.driver = true 
			";

			return $this->dbView->query($sqlDriverAssign)->result();
	}

	public function get_booking(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$searchCari = $this->input->post("searchCari");
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			// 0 =>'B.id',
			1 =>'created_on',
			2 =>'status',
		);

		$order_column = $field[$order_column];

		$where = "";

		if($service == "passanger"){
			if (!empty($cari))
			{
				if($searchCari=='ticketNumber')
				{
					$where .=" WHERE BP.status != -5 AND BP.ticket_number = '$like' ";
				}
				else
				{
					$where .=" WHERE BP.status != -5 AND B.booking_code = '$like'  ";
				}
			}
			$sql = "SELECT DISTINCT U.first_name as pemesan, BP.channel, I.terminal_code, DT.terminal_name, BP.created_on
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
					JOIN app.t_trx_invoice I ON I.trans_number = B.trans_number
					JOIN app.t_trx_sell S ON S.trans_number = I.trans_number
					JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
					JOIN core.t_mtr_user U ON U.id = OB.user_id
					LEFT JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = I.terminal_code
					{$where}";
		}else

		{
			if (!empty($cari))
			{
				

				if($searchCari=='ticketNumber')
				{
					$where .=" WHERE BV.status != -5 AND BV.ticket_number = '$like' ";
				}
				else
				{
					$where .=" WHERE BV.status != -5 AND BV.booking_code = '$like'  ";
				}
			}
			$sql = "
				SELECT DISTINCT U.first_name as pemesan, BV.channel, I.terminal_code, DT.terminal_name, BV.created_on
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
					JOIN app.t_trx_invoice I ON I.trans_number = B.trans_number
					JOIN app.t_trx_sell S ON S.trans_number = I.trans_number
					JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
					JOIN core.t_mtr_user U ON U.id = OB.user_id
					LEFT JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = I.terminal_code
					{$where}";
		}

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();

		if($records_total == 0){
			if($service == "passanger"){
				$sql = "SELECT DISTINCT I.customer_name as pemesan, BP.channel, I.terminal_code, DT.terminal_name, BP.created_on
						FROM app.t_trx_booking B
						JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
						JOIN app.t_trx_invoice I ON I.trans_number = B.trans_number
						LEFT JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = I.terminal_code
						{$where}";
			}else{
				$sql = "SELECT DISTINCT I.customer_name as pemesan, BV.channel, I.terminal_code, DT.terminal_name, BV.created_on
						FROM app.t_trx_booking B
						JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
						JOIN app.t_trx_invoice I ON I.trans_number = B.trans_number
						LEFT JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = I.terminal_code
						{$where}";
			}

			$query         = $this->dbView->query($sql);
			$records_total = $query->num_rows();
		}

		// $sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->pemesan = strtoupper($row->pemesan);
			$row->channel = strtoupper($row->channel);
			$row->terminal_code = strtoupper($row->terminal_code);
			$row->terminal_name = strtoupper($row->terminal_name);
			$row->created_on = format_dateTimeHis($row->created_on);

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

	public function get_payment(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$searchCari =$this->input->post("searchCari");
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			// 0 =>'B.id',
			1 =>'created_on',
			2 =>'status',
		);

		$order_column = $field[$order_column];

		$where = "";

		if($service == "passanger"){
			if (!empty($cari)){

				if($searchCari=='ticketNumber')
				{
					$where .=" WHERE P.status = 1 AND BP.ticket_number = '$like' ";
				}
				else
				{
					$where .=" WHERE P.status = 1 AND B.booking_code = '$like' ";
				}
			}
			$sql = "SELECT DISTINCT P.payment_type, P.amount, P.created_on, I.customer_name
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
					JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
					JOIN app.t_trx_invoice I ON I.trans_number = B.trans_number
					{$where}";
		}else{
			if (!empty($cari)){

				if($searchCari=='ticketNumber')
				{
					$where .=" WHERE P.status = 1 AND BV.ticket_number = '$like' ";
				}
				else
				{
					$where .=" WHERE P.status = 1 AND B.booking_code = '$like' ";
				}

				
			}
			$sql = "SELECT DISTINCT P.payment_type, P.amount, P.created_on, I.customer_name
					FROM app.t_trx_booking B
					JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
					JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
					JOIN app.t_trx_invoice I ON I.trans_number = B.trans_number
					{$where}";
		}

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();
		// $sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			if($service == "passanger"){
				if(strpos($row->payment_type, 'prepaid') !== false){
					$sql = "SELECT DISTINCT PR.trans_code
							FROM app.t_trx_booking B
							JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
							LEFT JOIN app.t_trx_prepaid PR ON PR.trans_number = B.trans_number
							WHERE (BP.ticket_number) = '$like'";
					$query     = $this->dbView->query($sql);
					$data = $query->row();
					$row->payment_code = $data->trans_code;
				}else if (strpos($row->payment_type, 'cash') !== false){
					$row->payment_code = '';
				}else{
					$sql = "SELECT DISTINCT P.invoice_number
							FROM app.t_trx_booking B
							JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
							JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
							WHERE (BP.ticket_number) = '$like'";
					$query     = $this->dbView->query($sql);
					$data = $query->row();
					$row->payment_code = $data->invoice_number;
				}
			}else{
				if(strpos($row->payment_type, 'prepaid') !== false){
					$sql = "SELECT DISTINCT PR.trans_code
							FROM app.t_trx_booking B
							JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
							LEFT JOIN app.t_trx_prepaid PR ON PR.trans_number = B.trans_number
							WHERE (BV.ticket_number) = '$like'";
					$query     = $this->dbView->query($sql);
					$data = $query->row();
					$row->payment_code = $data->trans_code;
				}else if (strpos($row->payment_type, 'cash') !== false){
					$row->payment_code = '';
				}else{
					$sql = "SELECT DISTINCT P.invoice_number
							FROM app.t_trx_booking B
							JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
							JOIN app.t_trx_payment P ON P.trans_number = B.trans_number
							WHERE (BV.ticket_number) = '$like'";
					$query     = $this->dbView->query($sql);
					$data = $query->row();
					$row->payment_code = $data->invoice_number;
				}
			}

			$row->number = $i;
			$row->created_on = format_dateTimeHis($row->created_on);
			$row->payment_type = strtoupper($row->payment_type);
			$row->amount = 'Rp'.idr_currency($row->amount);

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

	public function get_check_in(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			// 0 =>'B.id',
			1 =>'created_on',
			2 =>'status',
		);

		$order_column = $field[$order_column];

		$where = "";

		if($service == "passanger"){
			if (!empty($cari)){
				$where .=" WHERE CI.status = 1 AND BP.ticket_number = '$like' ";
			}
			$sql = "SELECT DISTINCT 
						CI.terminal_code,
						DT.terminal_name,
						CI.reprint,
						CI.updated_on,
						CI.created_on
					FROM app.t_trx_booking_passanger BP
					JOIN app.t_trx_check_in CI ON CI.ticket_number = BP.ticket_number
					LEFT JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = CI.terminal_code
					{$where}";
		}else{
			if (!empty($cari)){
				$where .=" WHERE CI.status = 1 AND BV.ticket_number = '$like' ";
			}
			$sql = "SELECT DISTINCT 
						CI.terminal_code,
						DT.terminal_name,
						CI.reprint,
						CI.updated_on,
						CI.created_on
					FROM app.t_trx_booking_vehicle BV
					JOIN app.t_trx_check_in_vehicle CI ON CI.ticket_number = BV.ticket_number
					LEFT JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = CI.terminal_code
					{$where}";
		}

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();

		// $sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->created_on = format_dateTimeHis($row->created_on);
			if(!empty($row->updated_on))
			{
				$row->updated_on = format_dateTimeHis($row->updated_on);
			}
			
			$row->terminal_code = strtoupper($row->terminal_code);
			$row->terminal_name = strtoupper($row->terminal_name);
			$row->reprint = $row->reprint.'x';

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

	public function get_refund(){

		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$searchCari =$this->input->post("searchCari");
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			1 =>'created_on',
			2 =>'status',
		);

		$order_column = $field[$order_column];

		$where = "";

		if($service == "passanger"){
			if (!empty($cari)){

				if($searchCari=='ticketNumber')
				{
					$where .=" WHERE BP.ticket_number = '$like' ";
				}
				else
				{
					$where .=" WHERE  BP.booking_code = '$like' ";
				}
			}

			$sql = " SELECT DISTINCT
						RF.created_on, 
						-- RF.status, 
						RF.booking_code,
						-- BP.ticket_number,
						RF.refund_code
					from app.t_trx_booking_passanger BP 
					JOIN app.t_trx_refund RF on BP.booking_code=RF.booking_code
					{$where}";
		}
		else
		{
			if (!empty($cari)){

				if($searchCari=='ticketNumber')
				{
					$where .=" WHERE BV.ticket_number = '$like' ";
				}
				else
				{
					$where .=" WHERE  BV.booking_code = '$like' ";
				}

				
			}
			$sql = "SELECT DISTINCT
						RF.created_on, 
						-- RF.status, 
						RF.booking_code,
						-- BV.ticket_number
						RF.refund_code
					from app.t_trx_booking_vehicle BV 
					JOIN app.t_trx_refund RF on BV.booking_code=RF.booking_code
					{$where}";
		}

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {

			$row->number = $i;
			$row->created_on = format_dateTimeHis($row->created_on);

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


	public function get_reschedule(){

		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$searchCari =$this->input->post("searchCari");
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			1 =>'created_on',
			2 =>'status',
		);

		$order_column = $field[$order_column];

		$where = "";

		if($service == "passanger"){
			if (!empty($cari)){

				if($searchCari=='ticketNumber')
				{
					$where .=" WHERE BP.ticket_number = '$like' ";
				}
				else
				{
					$where .=" WHERE  BP.booking_code = '$like' ";
				}
			}

			$sql = " SELECT DISTINCT
						RF.created_on, 
						-- RF.status, 
						RF.booking_code,
						-- BP.ticket_number,
						RF.new_booking_code,
						RF.reschedule_code
					from app.t_trx_booking_passanger BP 
					JOIN app.t_trx_reschedule RF on BP.booking_code=RF.booking_code
					{$where}";
		}
		else
		{
			if (!empty($cari)){

				if($searchCari=='ticketNumber')
				{
					$where .=" WHERE BV.ticket_number = '$like' ";
				}
				else
				{
					$where .=" WHERE  BV.booking_code = '$like' ";
				}

				
			}
			$sql = "SELECT DISTINCT
						RF.created_on, 
						-- RF.status, 
						RF.booking_code,
						RF.new_booking_code,
						-- BV.ticket_number
						RF.reschedule_code
					from app.t_trx_booking_vehicle BV 
					JOIN app.t_trx_reschedule RF on BV.booking_code=RF.booking_code
					{$where}";
		}

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {

			$row->number = $i;
			$row->created_on = format_dateTimeHis($row->created_on);

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



	public function get_gate_in(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			// 0 =>'B.id',
			1 =>'created_on',
			2 =>'status',
		);

		$order_column = $field[$order_column];

		$where = "";

		if($service == "passanger"){
			if (!empty($cari)){
				$where .=" WHERE GI.status = 1 AND BP.ticket_number = '$like' ";
			}
			$sql = "SELECT DISTINCT GI.terminal_code, DT.terminal_name, GI.created_on
					FROM app.t_trx_booking_passanger BP
					JOIN app.t_trx_gate_in GI ON GI.ticket_number = BP.ticket_number
					LEFT JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = GI.terminal_code
					{$where}";
		}else{
			if (!empty($cari)){
				$where .=" WHERE GI.status = 1 AND BV.ticket_number = '$like' ";
			}
			$sql = "SELECT DISTINCT GI.terminal_code, DT.terminal_name, GI.created_on
					FROM app.t_trx_booking_vehicle BV
					JOIN app.t_trx_gate_in_vehicle GI ON GI.ticket_number = BV.ticket_number
					LEFT JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = GI.terminal_code
					{$where}";
		}

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();

		// $sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->created_on = format_dateTimeHis($row->created_on);
			$row->terminal_code = strtoupper($row->terminal_code);
			$row->terminal_name = strtoupper($row->terminal_name);

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

	public function get_boarding(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			// 0 =>'B.id',
			1 =>'created_on',
			2 =>'status',
		);

		$order_column = $field[$order_column];

		$where = "";

		if($service == "passanger"){
			if (!empty($cari)){
				$where .=" WHERE BRP.status = 1 AND BP.ticket_number = '$like' ";
			}
			$sql = "SELECT DISTINCT BRP.boarding_code, S.shift_name as shift, OB.shift_date, D.name as dock, K.name as ship, SC.name as ship_class, C.name as company, BRP.terminal_code, DT.terminal_name, BRP.schedule_date, BRP.created_on, BRP.created_by as petugas
					FROM app.t_trx_booking_passanger BP
					FULL JOIN app.t_trx_boarding_passanger BRP ON BRP.ticket_number = BP.ticket_number
					FULL JOIN app.t_trx_open_boarding OB ON OB.boarding_code = BRP.boarding_code
					FULL JOIN app.t_mtr_ship K ON K.id = OB.ship_id
					FULL JOIN app.t_mtr_dock D ON D.id = BRP.dock_id
					FULL JOIN app.t_mtr_ship_company C ON C.id = K.ship_company_id
					FULL JOIN app.t_mtr_ship_class SC ON SC.id = BRP.ship_class
					FULL JOIN app.t_mtr_shift S ON S.id = OB.shift_id
					FULL JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = BRP.terminal_code
					{$where}";
		}else{
			if (!empty($cari)){
				$where .=" WHERE BRV.status = 1 AND BV.ticket_number = '$like' ";
			}
			$sql = "SELECT DISTINCT BRV.boarding_code, S.shift_name as shift, OB.shift_date, D.name as dock, K.name as ship, SC.name as ship_class, C.name as company, BRV.terminal_code, DT.terminal_name, BRV.schedule_date, BRV.created_on, BRV.created_by as petugas
					FROM app.t_trx_booking_vehicle BV
					FULL JOIN app.t_trx_boarding_vehicle BRV ON BRV.ticket_number = BV.ticket_number
					FULL JOIN app.t_trx_open_boarding OB ON OB.boarding_code = BRV.boarding_code
					FULL JOIN app.t_mtr_ship K ON K.id = OB.ship_id
					FULL JOIN app.t_mtr_dock D ON D.id = BRV.dock_id
					FULL JOIN app.t_mtr_ship_company C ON C.id = K.ship_company_id
					FULL JOIN app.t_mtr_ship_class SC ON SC.id = BRV.ship_class
					FULL JOIN app.t_mtr_shift S ON S.id = OB.shift_id
					FULL JOIN app.t_mtr_device_terminal DT ON DT.terminal_code = BRV.terminal_code
					{$where}";
		}

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();

		// $sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->schedule_date = format_dateTimeHis($row->schedule_date);
			$row->created_on = format_dateTimeHis($row->created_on);
			$row->petugas = strtoupper($row->petugas);
			$row->boarding_code = strtoupper($row->boarding_code);
			$row->shift = strtoupper($row->shift);
			$row->shift_date = format_date($row->shift_date);
			$row->dock = strtoupper($row->dock);
			$row->ship = strtoupper($row->ship);
			$row->ship_class = strtoupper($row->ship_class);
			$row->company = strtoupper($row->company);
			$row->terminal_code = strtoupper($row->terminal_code);
			$row->terminal_name = strtoupper($row->terminal_name);

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

	public function get_muntah_15012021(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			// 0 =>'B.id',
			1 =>'created_on',
			2 =>'status',
		);

		$order_column = $field[$order_column];

		$where = "";

		if($service == "passanger"){
			if (!empty($cari)){
				$where .=" WHERE SPD.status = 1 AND SPD.ticket_number = '$like' ";
			}
			$sql = "SELECT SP.created_on, SP.boarding_code as boarding_before, K.name as ship_before, C.name as company_before, S.shift_name as shift_before, SP.shift_date as shift_date_before, SPD.created_on as ticket_boarding_before, OB.created_on as kapal_boarding_before, SP.created_by as petugas_before, BRP.boarding_code as boarding_after, K2.name as ship_after, C2.name as company_after, S2.shift_name as shift_after, OB2.shift_date as shift_date_after, BRP.created_on as ticket_boarding_after, OB2.created_on as kapal_boarding_after, BRP.created_by as petugas_after
					FROM app.t_trx_boarding_passanger BRP
					FULL JOIN app.t_trx_switch_ship_passanger_detail SPD ON SPD.ticket_number = BRP.ticket_number
					FULL JOIN app.t_trx_switch_ship_passanger SP ON SP.id = SPD.switch_ship_id
					FULL JOIN app.t_trx_open_boarding OB2 ON OB2.boarding_code = BRP.boarding_code
					FULL JOIN app.t_mtr_ship K2 ON K2.id = OB2.ship_id
					FULL JOIN app.t_mtr_ship_company C2 ON C2.id = K2.ship_company_id
					FULL JOIN app.t_mtr_shift S2 ON S2.id = OB2.shift_id
					FULL JOIN app.t_trx_open_boarding OB ON OB.boarding_code = SP.boarding_code
					FULL JOIN app.t_mtr_ship K ON K.id = SP.ship_id
					FULL JOIN app.t_mtr_ship_company C ON C.id = K.ship_company_id
					FULL JOIN app.t_mtr_shift S ON S.id = SP.shift_id
					{$where}";
		}else{
			if (!empty($cari)){
				$where .=" WHERE SV.status = 1 AND SVD.ticket_number = '$like' ";
			}
			$sql = "SELECT SV.created_on, SV.boarding_code as boarding_before, K.name as ship_before, C.name as company_before, S.shift_name as shift_before, SV.shift_date as shift_date_before, SVD.created_on as ticket_boarding_before, OB.created_on as kapal_boarding_before, SV.created_by as petugas_before, BRV.boarding_code as boarding_after, K2.name as ship_after, C2.name as company_after, S2.shift_name as shift_after, OB2.shift_date as shift_date_after, BRV.created_on as ticket_boarding_after, OB2.created_on as kapal_boarding_after, BRV.created_by as petugas_after
					FROM app.t_trx_boarding_vehicle BRV
					FULL JOIN app.t_trx_switch_ship_vehicle_detail SVD ON SVD.ticket_number = BRV.ticket_number
					FULL JOIN app.t_trx_switch_ship_vehicle SV ON SV.id = SVD.switch_ship_id
					FULL JOIN app.t_trx_open_boarding OB2 ON OB2.boarding_code = BRV.boarding_code
					FULL JOIN app.t_mtr_ship K2 ON K2.id = OB2.ship_id
					FULL JOIN app.t_mtr_ship_company C2 ON C2.id = K2.ship_company_id
					FULL JOIN app.t_mtr_shift S2 ON S2.id = OB2.shift_id
					FULL JOIN app.t_trx_open_boarding OB ON OB.boarding_code = SV.boarding_code
					FULL JOIN app.t_mtr_ship K ON K.id = SV.ship_id
					FULL JOIN app.t_mtr_ship_company C ON C.id = K.ship_company_id
					FULL JOIN app.t_mtr_shift S ON S.id = SV.shift_id
					{$where}";
		}

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();

		// $sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			if($row->created_on) {$row->created_on = format_dateTimeHis($row->created_on);} else { $row->created_on = '';};
			if($row->ticket_boarding_before) {$row->ticket_boarding_before = format_dateTimeHis($row->ticket_boarding_before);} else { $row->ticket_boarding_before = '';};
			if($row->kapal_boarding_before) {$row->kapal_boarding_before = format_dateTimeHis($row->kapal_boarding_before);} else { $row->kapal_boarding_before = '';};
			if($row->ticket_boarding_after) {$row->ticket_boarding_after = format_dateTimeHis($row->ticket_boarding_after);} else { $row->ticket_boarding_after = '';};
			if($row->kapal_boarding_after) {$row->kapal_boarding_after = format_dateTimeHis($row->kapal_boarding_after);} else { $row->kapal_boarding_after = '';};
			if($row->shift_date_before) {$row->shift_date_before = format_date($row->shift_date_before);} else { $row->shift_date_before = '';};
			if($row->shift_date_after) {$row->shift_date_after = format_date($row->shift_date_after);} else { $row->shift_date_after = '';};

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


	public function get_muntah(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			// 0 =>'B.id',
			1 =>'created_on',
			2 =>'status',
		);

		$order_column = $field[$order_column];

		$where = "";

		if($service == "passanger"){
			if (!empty($cari)){
				$where .=" WHERE SPD.status = 1 AND SPD.ticket_number = '$like' ";
			}
			$sql = "SELECT distinct SPD.created_on, SP.boarding_code as boarding_before, K.name as ship_before, C.name as company_before, S.shift_name as shift_before, SP.shift_date as shift_date_before, SPD.created_on as ticket_boarding_before, OB.created_on as kapal_boarding_before, SP.created_by as petugas_before, BRP.boarding_code as boarding_after, K2.name as ship_after, C2.name as company_after, S2.shift_name as shift_after, OB2.shift_date as shift_date_after, BRP.created_on as ticket_boarding_after, OB2.created_on as kapal_boarding_after, BRP.created_by as petugas_after
					FROM app.t_trx_boarding_passanger BRP
					FULL JOIN app.t_trx_switch_ship_passanger_detail SPD ON SPD.ticket_number = BRP.ticket_number
					-- FULL JOIN app.t_trx_switch_ship_passanger SP ON SP.id = SPD.switch_ship_id
					FULL JOIN app.t_trx_switch_ship_passanger SP ON SP.switch_ship_code = SPD.switch_ship_code
					FULL JOIN app.t_trx_open_boarding OB2 ON OB2.boarding_code = BRP.boarding_code
					FULL JOIN app.t_mtr_ship K2 ON K2.id = OB2.ship_id
					FULL JOIN app.t_mtr_ship_company C2 ON C2.id = K2.ship_company_id
					FULL JOIN app.t_mtr_shift S2 ON S2.id = OB2.shift_id
					FULL JOIN app.t_trx_open_boarding OB ON OB.boarding_code = SP.boarding_code
					FULL JOIN app.t_mtr_ship K ON K.id = SP.ship_id
					FULL JOIN app.t_mtr_ship_company C ON C.id = K.ship_company_id
					FULL JOIN app.t_mtr_shift S ON S.id = SP.shift_id
					{$where}";
		}else{
			if (!empty($cari)){
				$where .=" WHERE SV.status = 1 AND SVD.ticket_number = '$like'  ";
			}
			$sql = "SELECT distinct SVD.created_on, SV.boarding_code as boarding_before, K.name as ship_before, C.name as company_before, S.shift_name as shift_before, SV.shift_date as shift_date_before, SVD.created_on as ticket_boarding_before, OB.created_on as kapal_boarding_before, SV.created_by as petugas_before, BRV.boarding_code as boarding_after, K2.name as ship_after, C2.name as company_after, S2.shift_name as shift_after, OB2.shift_date as shift_date_after, BRV.created_on as ticket_boarding_after, OB2.created_on as kapal_boarding_after, BRV.created_by as petugas_after
					FROM app.t_trx_boarding_vehicle BRV
					FULL JOIN app.t_trx_switch_ship_vehicle_detail SVD ON SVD.ticket_number = BRV.ticket_number
					-- FULL JOIN app.t_trx_switch_ship_vehicle SV ON SV.id = SVD.switch_ship_id
					FULL JOIN app.t_trx_switch_ship_vehicle SV ON SV.switch_ship_code = SVD.switch_ship_code
					FULL JOIN app.t_trx_open_boarding OB2 ON OB2.boarding_code = BRV.boarding_code
					FULL JOIN app.t_mtr_ship K2 ON K2.id = OB2.ship_id
					FULL JOIN app.t_mtr_ship_company C2 ON C2.id = K2.ship_company_id
					FULL JOIN app.t_mtr_shift S2 ON S2.id = OB2.shift_id
					FULL JOIN app.t_trx_open_boarding OB ON OB.boarding_code = SV.boarding_code
					FULL JOIN app.t_mtr_ship K ON K.id = SV.ship_id
					FULL JOIN app.t_mtr_ship_company C ON C.id = K.ship_company_id
					FULL JOIN app.t_mtr_shift S ON S.id = SV.shift_id
					{$where}";
		}

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();

		// $sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			if($row->created_on) {$row->created_on = format_dateTimeHis($row->created_on);} else { $row->created_on = '';};
			if($row->ticket_boarding_before) {$row->ticket_boarding_before = format_dateTimeHis($row->ticket_boarding_before);} else { $row->ticket_boarding_before = '';};
			if($row->kapal_boarding_before) {$row->kapal_boarding_before = format_dateTimeHis($row->kapal_boarding_before);} else { $row->kapal_boarding_before = '';};
			if($row->ticket_boarding_after) {$row->ticket_boarding_after = format_dateTimeHis($row->ticket_boarding_after);} else { $row->ticket_boarding_after = '';};
			if($row->kapal_boarding_after) {$row->kapal_boarding_after = format_dateTimeHis($row->kapal_boarding_after);} else { $row->kapal_boarding_after = '';};
			if($row->shift_date_before) {$row->shift_date_before = format_date($row->shift_date_before);} else { $row->shift_date_before = '';};
			if($row->shift_date_after) {$row->shift_date_after = format_date($row->shift_date_after);} else { $row->shift_date_after = '';};

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
	public function get_pindah_15012021(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			// 0 =>'B.id',
			1 =>'created_on',
			2 =>'status',
		);

		$order_column = $field[$order_column];

		$where = "";

		if($service == "passanger"){
			if (!empty($cari)){
				$where .=" WHERE SPD.status = 1 AND SPD.ticket_number = '$like' ";
			}
			$sql = "SELECT SP.created_on, SP.boarding_code as boarding_before, K.name as ship_before, C.name as company_before, S.shift_name as shift_before, SP.shift_date as shift_date_before, SPD.created_on as ticket_boarding_before, OB.created_on as kapal_boarding_before, SP.created_by as petugas_before, BRP.boarding_code as boarding_after, K2.name as ship_after, C2.name as company_after, S2.shift_name as shift_after, OB2.shift_date as shift_date_after, BRP.created_on as ticket_boarding_after, OB2.created_on as kapal_boarding_after, BRP.created_by as petugas_after
					FROM app.t_trx_boarding_passanger BRP
					FULL JOIN app.t_trx_switch_ship_all_passanger_detail SPD ON SPD.ticket_number = BRP.ticket_number
					FULL JOIN app.t_trx_switch_ship_all_passanger SP ON SP.id = SPD.switch_ship_id
					FULL JOIN app.t_trx_open_boarding OB2 ON OB2.boarding_code = BRP.boarding_code
					FULL JOIN app.t_mtr_ship K2 ON K2.id = OB2.ship_id
					FULL JOIN app.t_mtr_ship_company C2 ON C2.id = K2.ship_company_id
					FULL JOIN app.t_mtr_shift S2 ON S2.id = OB2.shift_id
					FULL JOIN app.t_trx_open_boarding OB ON OB.boarding_code = SP.boarding_code
					FULL JOIN app.t_mtr_ship K ON K.id = SP.ship_id
					FULL JOIN app.t_mtr_ship_company C ON C.id = K.ship_company_id
					FULL JOIN app.t_mtr_shift S ON S.id = SP.shift_id
					{$where}";
		}else{
			if (!empty($cari)){
				$where .=" WHERE SV.status = 1 AND SVD.ticket_number = '$like' ";
			}
			$sql = "SELECT SV.created_on, SV.boarding_code as boarding_before, K.name as ship_before, C.name as company_before, S.shift_name as shift_before, SV.shift_date as shift_date_before, SVD.created_on as ticket_boarding_before, OB.created_on as kapal_boarding_before, SV.created_by as petugas_before, BRV.boarding_code as boarding_after, K2.name as ship_after, C2.name as company_after, S2.shift_name as shift_after, OB2.shift_date as shift_date_after, BRV.created_on as ticket_boarding_after, OB2.created_on as kapal_boarding_after, BRV.created_by as petugas_after
					FROM app.t_trx_boarding_vehicle BRV
					FULL JOIN app.t_trx_switch_ship_all_vehicle_detail SVD ON SVD.ticket_number = BRV.ticket_number
					FULL JOIN app.t_trx_switch_ship_all_vehicle SV ON SV.id = SVD.switch_ship_id
					FULL JOIN app.t_trx_open_boarding OB2 ON OB2.boarding_code = BRV.boarding_code
					FULL JOIN app.t_mtr_ship K2 ON K2.id = OB2.ship_id
					FULL JOIN app.t_mtr_ship_company C2 ON C2.id = K2.ship_company_id
					FULL JOIN app.t_mtr_shift S2 ON S2.id = OB2.shift_id
					FULL JOIN app.t_trx_open_boarding OB ON OB.boarding_code = SV.boarding_code
					FULL JOIN app.t_mtr_ship K ON K.id = SV.ship_id
					FULL JOIN app.t_mtr_ship_company C ON C.id = K.ship_company_id
					FULL JOIN app.t_mtr_shift S ON S.id = SV.shift_id
					{$where}";
		}

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();

		// $sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->created_on = format_dateTimeHis($row->created_on);
			$row->ticket_boarding_before = format_dateTimeHis($row->ticket_boarding_before);
			$row->kapal_boarding_before = format_dateTimeHis($row->kapal_boarding_before);
			$row->ticket_boarding_after = format_dateTimeHis($row->ticket_boarding_after);
			$row->kapal_boarding_after = format_dateTimeHis($row->kapal_boarding_after);
			if($row->shift_date_before) {$row->shift_date_before = format_date($row->shift_date_before);} else { $row->shift_date_before = '';};
			if($row->shift_date_after) {$row->shift_date_after = format_date($row->shift_date_after);} else { $row->shift_date_after = '';};

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

	public function get_pindah(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$cari = $this->input->post('cari');
		$service = $this->input->post('service');
		$like = trim(strtoupper($this->dbView->escape_like_str($cari)));

		$field = array(
			// 0 =>'B.id',
			1 =>'created_on',
			2 =>'status',
		);

		$order_column = $field[$order_column];

		$where = "";

		if($service == "passanger"){
			if (!empty($cari)){
				$where .=" WHERE SPD.status = 1 AND SPD.ticket_number = '$like'  ";
			}
			$sql = "SELECT distinct SPD.created_on, SP.boarding_code as boarding_before, K.name as ship_before, C.name as company_before, S.shift_name as shift_before, SP.shift_date as shift_date_before, SPD.created_on as ticket_boarding_before, OB.created_on as kapal_boarding_before, SP.created_by as petugas_before, BRP.boarding_code as boarding_after, K2.name as ship_after, C2.name as company_after, S2.shift_name as shift_after, OB2.shift_date as shift_date_after, BRP.created_on as ticket_boarding_after, OB2.created_on as kapal_boarding_after, BRP.created_by as petugas_after
					FROM app.t_trx_boarding_passanger BRP
					FULL JOIN app.t_trx_switch_ship_all_passanger_detail SPD ON SPD.ticket_number = BRP.ticket_number
					-- FULL JOIN app.t_trx_switch_ship_all_passanger SP ON SP.id = SPD.switch_ship_id
					FULL JOIN app.t_trx_switch_ship_all_passanger SP ON SP.switch_ship_code = SPD.switch_ship_code
					FULL JOIN app.t_trx_open_boarding OB2 ON OB2.boarding_code = BRP.boarding_code
					FULL JOIN app.t_mtr_ship K2 ON K2.id = OB2.ship_id
					FULL JOIN app.t_mtr_ship_company C2 ON C2.id = K2.ship_company_id
					FULL JOIN app.t_mtr_shift S2 ON S2.id = OB2.shift_id
					FULL JOIN app.t_trx_open_boarding OB ON OB.boarding_code = SP.boarding_code
					FULL JOIN app.t_mtr_ship K ON K.id = SP.ship_id
					FULL JOIN app.t_mtr_ship_company C ON C.id = K.ship_company_id
					FULL JOIN app.t_mtr_shift S ON S.id = SP.shift_id
					{$where}";
		}else{
			if (!empty($cari)){
				$where .=" WHERE SV.status = 1 AND SVD.ticket_number = '$like' ";
			}
			$sql = "SELECT distinct SVD.created_on, SV.boarding_code as boarding_before, K.name as ship_before, C.name as company_before, S.shift_name as shift_before, SV.shift_date as shift_date_before, SVD.created_on as ticket_boarding_before, OB.created_on as kapal_boarding_before, SV.created_by as petugas_before, BRV.boarding_code as boarding_after, K2.name as ship_after, C2.name as company_after, S2.shift_name as shift_after, OB2.shift_date as shift_date_after, BRV.created_on as ticket_boarding_after, OB2.created_on as kapal_boarding_after, BRV.created_by as petugas_after
					FROM app.t_trx_boarding_vehicle BRV
					FULL JOIN app.t_trx_switch_ship_all_vehicle_detail SVD ON SVD.ticket_number = BRV.ticket_number
					-- FULL JOIN app.t_trx_switch_ship_all_vehicle SV ON SV.id = SVD.switch_ship_id
					FULL JOIN app.t_trx_switch_ship_all_vehicle SV ON SV.switch_ship_code = SVD.switch_ship_code
					FULL JOIN app.t_trx_open_boarding OB2 ON OB2.boarding_code = BRV.boarding_code
					FULL JOIN app.t_mtr_ship K2 ON K2.id = OB2.ship_id
					FULL JOIN app.t_mtr_ship_company C2 ON C2.id = K2.ship_company_id
					FULL JOIN app.t_mtr_shift S2 ON S2.id = OB2.shift_id
					FULL JOIN app.t_trx_open_boarding OB ON OB.boarding_code = SV.boarding_code
					FULL JOIN app.t_mtr_ship K ON K.id = SV.ship_id
					FULL JOIN app.t_mtr_ship_company C ON C.id = K.ship_company_id
					FULL JOIN app.t_mtr_shift S ON S.id = SV.shift_id
					{$where}";
		}

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();

		// $sql 		  .= " ORDER BY ".$order_column." {$order_dir}";
		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->created_on = format_dateTimeHis($row->created_on);
			$row->ticket_boarding_before = format_dateTimeHis($row->ticket_boarding_before);
			$row->kapal_boarding_before = format_dateTimeHis($row->kapal_boarding_before);
			$row->ticket_boarding_after = format_dateTimeHis($row->ticket_boarding_after);
			$row->kapal_boarding_after = format_dateTimeHis($row->kapal_boarding_after);
			if($row->shift_date_before) {$row->shift_date_before = format_date($row->shift_date_before);} else { $row->shift_date_before = '';};
			if($row->shift_date_after) {$row->shift_date_after = format_date($row->shift_date_after);} else { $row->shift_date_after = '';};

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
	public function select_data($table, $where)
	{
		return $this->db->query("select * from $table $where");
	}

}