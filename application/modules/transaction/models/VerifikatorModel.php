<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  2021
 *
 */

class VerifikatorModel extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'transaction/verifikator';
	}

	public function dataList()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);

		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$dataJam = trim($this->input->post('dataJam'));
		$port = $this->enc->decode($this->input->post('port'));
		$shipClass = $this->enc->decode($this->input->post('shipClass'));
		$service = $this->enc->decode($this->input->post('service'));
		$vehicleClass = $this->enc->decode($this->input->post('vehicleClass'));
		$passangerType = $this->enc->decode($this->input->post('passangerType'));
		$statusTicket = $this->enc->decode($this->input->post('statusTicket'));
		$dataValidasi = $this->enc->decode($this->input->post('dataValidasi'));

		$searchData = str_replace(array("'",'"',","),"",trim($this->input->post('searchData')));
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));


		$field = array(
			0=>"id",
			1=>"booking_code",
			2=>"ticket_number",
			3=>"origin_name",
			4=>"service_name",
			5=>"ship_class_name",
			6=>"golongan_knd",
			7=>"golongan_pnp",                        
			8=>"plat_no",
			9=>"passanger_name",
			10=>"id_type_name",
			11=>"no_identitas",
			12=>"age",
			13=>"gender",
			14=>"city",
			15=>"tanggal_masuk_pelabuhan",
			16=>"depart_time_start",
			17=>"status_ticket",
			18=>"checkin_date",
			19=>"gatein_date",
			20=>"boarding_date",
			21=>"approved_status",
			22=>"user_verified", 
			23=>"approved_date",
			24=>"terminal_name",
			25=>"terminal_code",       
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		
		$where = " WHERE ttbp.status !='-5' and bk.depart_date >= '". $dateFrom . "' and bk.depart_date < '" . $dateToNew . "'";
		$where2 ="";

		if(!empty($port))
		{
			$where .= " and ttbp.origin={$port}  ";
		}

		if(!empty($shipClass))
		{
			$where .= " and ttbp.ship_class={$shipClass}  ";
		}	
		
		if(!empty($service))
		{
			$where .= " and ttbp.service_id={$service}  ";
		}
		
		if(!empty($vehicleClass))
		{
			$where .= " and ttbv.vehicle_class_id={$vehicleClass}  ";
		}
		
		if(!empty($passangerType))
		{
			$where .= " and ttbp.passanger_type_id={$passangerType}  ";
		}

		if(!empty($statusTicket))
		{
			$where .= " and tms2.description='{$statusTicket}'  ";
		}	

		if(!empty($dataJam))
        {
            $where .=" and ( bk.depart_time_start between '{$dataJam}:00' and '{$dataJam}:59' ) ";   
        } 			
		
		if(!empty($dataValidasi))
		{
			if($dataValidasi=='t')
			{

				$where .= " and ttva.created_by is not null  ";
			}
			else
			{
				$where .= " and ttva.created_by is null ";

			}
		}			

	
		
		if(!empty($searchData))
		{
			if($searchName=="bookingCode")
			{
				$where .= " and ttbp.booking_code ilike '%".$searchData."%' ";
			}				
			else if($searchName=="ticketNumber")
			{
				$where .= " and ttbp.ticket_number ilike '%".$searchData."%' ";
			}
			else if($searchName=="platNumber")
			{
				$where .= " and ttbv.id_number ilike '%".$searchData."%' ";
			}
			else if($searchName=="passangerName")
			{
				$where .= " and ttbp.name ilike '%".$searchData."%' ";
			}	
			else if($searchName=="identitas")
			{
				$where .= " and ttbp.id_number ilike '%".$searchData."%' ";
			}
			else if($searchName=="verificationUser")
			{
				$where2 .= " where user_verified ilike '%".$searchData."%' ";
			}													
			else
			{
				$where .= "";
			}
		}


		$sql = $this->qry($where,$where2);

		// die($sql); exit;

		$sqlCount = $this->countQry($where,$where2);

		$recordCount        = $this->dbView->query($sqlCount)->row();
		$records_total = $recordCount->countdata;
		// $records_total = $this->dbView->query($sql)->num_rows();
		

		$sql 		  .= " ORDER BY  " . $order_column . " {$order_dir}";


		if ($length != -1) {
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$booking_code = $this->enc->encode($row->booking_code);

			$row->actions = "";
			$row->keterangan = "";

			$row->approved_status= $row->created_by !=''?success_label('Approved'):failed_label('Not Validated');
			$row->tanggal_masuk_pelabuhan=format_date($row->tanggal_masuk_pelabuhan);
			$row->checkin_date=empty($row->checkin_date)?"":format_date($row->checkin_date)." ".format_time($row->checkin_date);
			$row->gatein_date=empty($row->gatein_date)?"":format_date($row->gatein_date)." ".format_time($row->gatein_date);
			$row->boarding_date=empty($row->boarding_date)?"":format_date($row->boarding_date)." ".format_time($row->boarding_date);
			$row->approved_date=empty($row->approved_date)?"":format_date($row->approved_date)." ".format_time($row->approved_date);
						

			$row->no = $i;

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'data'           => $rows
		);
	}



	public function select_data($table, $where = "")
	{
		return $this->dbView->query("select * from $table $where");
	}

    public function download()
    {

		$dateTo = trim($this->input->get('dateTo'));
		$dateFrom = trim($this->input->get('dateFrom'));
		$dataJam = trim($this->input->get('dataJam'));
		$port = $this->enc->decode($this->input->get('port'));
		$shipClass = $this->enc->decode($this->input->get('shipClass'));
		$service = $this->enc->decode($this->input->get('service'));
		$vehicleClass = $this->enc->decode($this->input->get('vehicleClass'));
		$passangerType = $this->enc->decode($this->input->get('passangerType'));
		$statusTicket = $this->enc->decode($this->input->get('statusTicket'));
		$dataValidasi = $this->enc->decode($this->input->get('dataValidasi'));

		// $searchData = $this->input->get('searchData');
		$searchData = str_replace(array("'",'"',","),"",trim($this->input->get('searchData')));
		$searchName = $this->input->get('searchName');

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));
		$where = " WHERE ttbp.status !='-5' and  bk.depart_date >= '". $dateFrom . "' and bk.depart_date < '" . $dateToNew . "'";
		$where2 ="";

		if(!empty($port))
		{
			$where .= " and ttbp.origin={$port}  ";
		}

		if(!empty($shipClass))
		{
			$where .= " and ttbp.ship_class={$shipClass}  ";
		}	
		
		if(!empty($service))
		{
			$where .= " and ttbp.service_id={$service}  ";
		}
		
		if(!empty($vehicleClass))
		{
			$where .= " and ttbv.vehicle_class_id={$vehicleClass}  ";
		}
		
		if(!empty($passangerType))
		{
			$where .= " and ttbp.passanger_type_id={$passangerType}  ";
		}

		if(!empty($statusTicket))
		{
			$where .= " and tms2.description='{$statusTicket}'  ";
		}	

		if(!empty($dataJam))
        {
            $where .=" and ( bk.depart_time_start between '{$dataJam}:00' and '{$dataJam}:59' ) ";   
        } 			
		
		if(!empty($dataValidasi))
		{
			if($dataValidasi=='t')
			{

				$where .= " and ttva.created_by is not null  ";
			}
			else
			{
				$where .= " and ttva.created_by is null ";

			}
		}			

	
		
		if(!empty($searchData))
		{
			if($searchName=="bookingCode")
			{
				$where .= " and ttbp.booking_code ilike '%".$searchData."%' ";
			}				
			else if($searchName=="ticketNumber")
			{
				$where .= " and ttbp.ticket_number ilike '%".$searchData."%' ";
			}
			else if($searchName=="platNumber")
			{
				$where .= " and ttbv.id_number ilike '%".$searchData."%' ";
			}
			else if($searchName=="passangerName")
			{
				$where .= " and ttbp.name ilike '%".$searchData."%' ";
			}	
			else if($searchName=="identitas")
			{
				$where .= " and ttbp.id_number ilike '%".$searchData."%' ";
			}
			else if($searchName=="verificationUser")
			{
				$where2 .= " where user_verified ilike '%".$searchData."%' ";
			}													
			else
			{
				$where .= "";
			}
		}



		$sql = $this->qry($where." order by  bk.depart_date , bk.depart_time_start desc  " ,$where2);
		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$data 	= array();
		$i  	= 1;
		foreach ($rows_data as $row) {
			$row->number = $i;

			$booking_code = $this->enc->encode($row->booking_code);

			$row->actions = "";
			$row->keterangan = "";

			$row->approved_status= $row->created_by !=''?'Approved':'Not Validated';

			$row->tanggal_masuk_pelabuhan=format_date($row->tanggal_masuk_pelabuhan);
			$row->checkin_date=empty($row->checkin_date)?"":format_date($row->checkin_date)." ".format_time($row->checkin_date);
			$row->gatein_date=empty($row->gatein_date)?"":format_date($row->gatein_date)." ".format_time($row->gatein_date);
			$row->boarding_date=empty($row->boarding_date)?"":format_date($row->boarding_date)." ".format_time($row->boarding_date);
			$row->approved_date=empty($row->approved_date)?"":format_date($row->approved_date)." ".format_time($row->approved_date);			
			

			$row->no = $i;

			$data[] = $row;
			unset($row->id);

			$i++;
		}

		return $data;
    }

	public function get_identity_app()
	{
		$data = $this->dbView->query("select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}

	public function get_merchant()
	{
		$data = $this->dbView->query("SELECT DISTINCT merchant_name, merchant_id FROM app.t_mtr_merchant WHERE status = 1");
		return $data->result();
	}

	public function qry_11112021($where , $where2)
	{
		$data="
		select * from (
			select 
				ttva.id,
				ttbp.booking_code,
				ttva.ticket_number,
				tmp.name as origin_name,
				tmp2.name as destination_name,
				tms.name as service_name,
				tmsc.name as ship_class_name,
				tmpt.name as golongan_pnp,
				tmvc.name as golongan_knd,
				ttbv.id_number as plat_no,
				ttbp.name as passanger_name,
				ttbp.id_number as no_identitas,
				ttbp.age,
				ttbp.gender,
				ttbp.city,
				bk.depart_date  as tanggal_masuk_pelabuhan,
				bk.depart_time_start,
				tms2.description as status_ticket,
				pti.name as id_type_name,
				ttva.approved_status,
				(
					select 
						tmu.username as user_verified
					from app.t_trx_opening_balance ttob 
					join core.t_mtr_user tmu on ttob .user_id = tmu.id 
					where ttob.ob_code = ttva.approved_by
					union 
					select 
						tmu.username as user_verified
					from app.t_trx_assignment_verifier ttav 
					join core.t_mtr_user tmu on ttav .user_id = tmu.id
					where ttav.shift_code = ttva.approved_by
					union 
					select 
						tmdt.terminal_name  as user_verified
					from app.t_trx_opening_balance_vm ttobv 
					join app.t_mtr_device_terminal tmdt on ttobv .terminal_code = tmdt.terminal_code
					where ttobv.ob_code = ttva.approved_by
					
				) as user_verified,
				ttva.approved_by,
				ttci.created_on as checkin_date,
				ttgi.created_on as gatein_date,
				ttbp2.boarding_date 
			from 
				app.t_trx_vaccine_approval ttva 
				left join app.t_trx_booking_passanger ttbp on ttva.ticket_number =ttbp.ticket_number 
				left join app.t_trx_booking bk on ttbp.booking_code=bk.booking_code
				left join app.t_mtr_port tmp on ttbp.origin =tmp.id 
				left join app.t_mtr_port tmp2 on ttbp.destination =tmp2.id 
				left join app.t_mtr_service tms on ttbp.service_id = tms.id
				left join app.t_mtr_ship_class tmsc on ttbp.ship_class =tmsc.id 
				left join app.t_mtr_passanger_type tmpt on ttbp.passanger_type_id = tmpt.id 
				left join app.t_trx_booking_vehicle ttbv on ttbp.booking_code =ttbv.booking_code 
				left join app.t_mtr_vehicle_class tmvc on ttbv.vehicle_class_id =tmvc.id
				left join app.t_mtr_status tms2 on ttbp.status =tms2.status and tms2.tbl_name ='t_trx_booking_passanger'
				left join app.t_mtr_passanger_type_id pti on ttbp.id_type=pti.id
				left join app.t_trx_check_in ttci on ttva.ticket_number =ttci.ticket_number 
				left join app.t_trx_gate_in ttgi on ttva.ticket_number =ttgi.ticket_number 
				left join app.t_trx_boarding_passanger ttbp2 on ttva .ticket_number = ttbp2.ticket_number 
				
				$where
				
				) vericator
				$where2
		";

		return $data;
	}

	public function qry($where , $where2)
	{
		$data="
		select * from (
			select 
				ttbp.id,
				ttbp.booking_code,
				ttbp.ticket_number,
				tmp.name as origin_name,
				tmp2.name as destination_name,
				tms.name as service_name,
				tmsc.name as ship_class_name,
				tmpt.name as golongan_pnp,
				tmvc.name as golongan_knd,
				ttbv.id_number as plat_no,
				ttbp.name as passanger_name,
				ttbp.id_number as no_identitas,
				ttbp.age,
				ttbp.gender,
				ttbp.city,
				bk.depart_date  as tanggal_masuk_pelabuhan,
				bk.depart_time_start,
				tms2.description as status_ticket,
				pti.name as id_type_name,
				(
					select 
						tmu.username as user_verified
					from app.t_trx_opening_balance ttob 
					join core.t_mtr_user tmu on ttob .user_id = tmu.id 
					where ttob.ob_code = ttva.created_by
					union 
					select 
						tmu.username as user_verified
					from app.t_trx_assignment_verifier ttav 
					join core.t_mtr_user tmu on ttav .user_id = tmu.id
					where ttav.shift_code = ttva.created_by
					union 
					select 
						tmdt.terminal_name  as user_verified
					from app.t_trx_opening_balance_vm ttobv 
					join app.t_mtr_device_terminal tmdt on ttobv .terminal_code = tmdt.terminal_code
					where ttobv.ob_code = ttva.created_by
					
				) as user_verified,
				ttva.created_on as approved_date,
				ttva.created_by ,
				ttva.terminal_code,
				ttci.created_on as checkin_date,
				ttgi.created_on as gatein_date,
				tmdt.terminal_name ,				
				ttbp2.boarding_date 
			from
				app.t_trx_booking_passanger ttbp 
				left join app.t_trx_approval_manifest ttva on  ttbp.ticket_number = ttva.ticket_number
				left join app.t_trx_booking bk on ttbp.booking_code=bk.booking_code
				left join app.t_mtr_port tmp on ttbp.origin =tmp.id 
				left join app.t_mtr_port tmp2 on ttbp.destination =tmp2.id 
				left join app.t_mtr_service tms on ttbp.service_id = tms.id
				left join app.t_mtr_ship_class tmsc on ttbp.ship_class =tmsc.id 
				left join app.t_mtr_passanger_type tmpt on ttbp.passanger_type_id = tmpt.id 
				left join app.t_trx_booking_vehicle ttbv on ttbp.booking_code =ttbv.booking_code 
				left join app.t_mtr_vehicle_class tmvc on ttbv.vehicle_class_id =tmvc.id
				left join app.t_mtr_status tms2 on ttbp.status =tms2.status and tms2.tbl_name ='t_trx_booking_passanger'
				left join app.t_mtr_passanger_type_id pti on ttbp.id_type=pti.id
				left join app.t_trx_check_in ttci on ttbp.ticket_number =ttci.ticket_number 
				left join app.t_trx_gate_in ttgi on ttbp.ticket_number =ttgi.ticket_number 
				left join app.t_trx_boarding_passanger ttbp2 on ttbp .ticket_number = ttbp2.ticket_number 
				left join app.t_mtr_device_terminal tmdt on ttva.terminal_code =tmdt.terminal_code 
				
				$where
				
				) vericator
				$where2
		";

		return $data;
	}	

	public function countQry($where, $where2)
	{
		$data="
			SELECT
				count (id) as countdata from
				(
					select 
					ttbp.id,
					ttbp.booking_code,
					ttbp.ticket_number,
					tmp.name as origin_name,
					tmp2.name as destination_name,
					tms.name as service_name,
					tmsc.name as ship_class_name,
					tmpt.name as golongan_pnp,
					tmvc.name as golongan_knd,
					ttbv.id_number as plat_no,
					ttbp.name as passanger_name,
					ttbp.id_number as no_identitas,
					ttbp.age,
					ttbp.gender,
					ttbp.city,
					bk.depart_date  as tanggal_masuk_pelabuhan,
					bk.depart_time_start,
					tms2.description as status_ticket,
					pti.name as id_type_name,
					(
						select 
							tmu.username as user_verified
						from app.t_trx_opening_balance ttob 
						join core.t_mtr_user tmu on ttob .user_id = tmu.id 
						where ttob.ob_code = ttva.created_by
						union 
						select 
							tmu.username as user_verified
						from app.t_trx_assignment_verifier ttav 
						join core.t_mtr_user tmu on ttav .user_id = tmu.id
						where ttav.shift_code = ttva.created_by
						union 
						select 
							tmdt.terminal_name  as user_verified
						from app.t_trx_opening_balance_vm ttobv 
						join app.t_mtr_device_terminal tmdt on ttobv .terminal_code = tmdt.terminal_code
						where ttobv.ob_code = ttva.created_by
						
					) as user_verified,
					ttva.created_by ,
					ttva.terminal_code,
					ttva.created_on as approved_date,
					ttci.created_on as checkin_date,
					ttgi.created_on as gatein_date,
					tmdt.terminal_name ,
					ttbp2.boarding_date 
				from
					app.t_trx_booking_passanger ttbp 
					left join app.t_trx_approval_manifest ttva on  ttbp.ticket_number = ttva.ticket_number
					left join app.t_trx_booking bk on ttbp.booking_code=bk.booking_code
					left join app.t_mtr_port tmp on ttbp.origin =tmp.id 
					left join app.t_mtr_port tmp2 on ttbp.destination =tmp2.id 
					left join app.t_mtr_service tms on ttbp.service_id = tms.id
					left join app.t_mtr_ship_class tmsc on ttbp.ship_class =tmsc.id 
					left join app.t_mtr_passanger_type tmpt on ttbp.passanger_type_id = tmpt.id 
					left join app.t_trx_booking_vehicle ttbv on ttbp.booking_code =ttbv.booking_code 
					left join app.t_mtr_vehicle_class tmvc on ttbv.vehicle_class_id =tmvc.id
					left join app.t_mtr_status tms2 on ttbp.status =tms2.status and tms2.tbl_name ='t_trx_booking_passanger'
					left join app.t_mtr_passanger_type_id pti on ttbp.id_type=pti.id
					left join app.t_trx_check_in ttci on ttbp.ticket_number =ttci.ticket_number 
					left join app.t_trx_gate_in ttgi on ttbp.ticket_number =ttgi.ticket_number 
					left join app.t_trx_boarding_passanger ttbp2 on ttbp .ticket_number = ttbp2.ticket_number 
					left join app.t_mtr_device_terminal tmdt on ttva.terminal_code =tmdt.terminal_code 
					
					$where
				)  vericator
				$where2
		";

		return $data;
	}	

	public function statusTicket()
	{
		return $this->dbView->query("
			SELECT 
				distinct description as description 
			from app.t_mtr_status  where tbl_name='t_trx_booking_passanger' order by description asc
		
		")->result();
	}
}
