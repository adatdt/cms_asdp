<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : M_boarding_ticket_manual
 * -----------------------
 *
 * @author     Adat <adatdt@gmail.com>
 * @copyright  2020
 *
 */

class M_boarding_ticket_manual extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/boarding_ticket_manual';

	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$order = $this->input->post('order');
		$dateTo = $this->input->post('dateTo');
		$shift = $this->enc->decode($this->input->post('shift'));
		$shipClass = $this->enc->decode($this->input->post('ship_class'));
		$statusBoarding = $this->enc->decode($this->input->post('statusBoarding'));
		$dateFrom = $this->input->post('dateFrom');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData=$this->input->post('searchData');
		$searchName=$this->input->post('searchName');
		$iLike  = trim(strtoupper($this->db->escape_like_str($searchData)));

		
		$field = array(
			0=> "id",
			1=> "created_on",
			2=> "trx_date",
			3=> "trans_number",
			4=> "name", 
			5=> "ticket_number_manual",
			6=> "ticket_number",
			7=>"ship_name",
			8=> "gender", 
			9=> "address",
			10=> "username", 
			11=> "shift_name",
			12=> "ship_class_name",
			13=> "port_name",
			14=> "boarding_code",

		);


		if($this->get_identity_app()==0)
		{
			if(!empty($this->session->userdata('port_id')))
			{
				$port_id= $this->session->userdata('port_id');
			}
			else
			{
				$port_id= $this->enc->decode($this->input->post('port'));
			}
		}
		else
		{
			$port_id= $this->get_identity_app();
		}


		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status != -5 and a.trx_date >= '". $dateFrom . "' and a.trx_date < '" . $dateToNew . "'";
		// $where = " WHERE a.status not IN (-5) and (a.trx_date::date between '{$dateFrom}' and '{$dateTo}' )";

		if(!empty($port_id))
		{
			$where .=" and (a.port_id='{$port_id}') ";
		}

		if(!empty($shift))
		{
			$where .=" and (a.shift_id='{$shift}') ";
		}		

		if(!empty($shipClass))
		{
			$where .=" and (a.ship_class='{$shipClass}') ";
		}				

		if($statusBoarding!="")
		{
			switch ($statusBoarding) {
				case  1 :
					$where .=" and ( g.boarding_code is not null ) ";
					break;
				
				default:
					$where .=" and ( g.boarding_code is null ) ";
					break;
			}

		}						
	
		// if (!empty($search['value'])){
		// 	$where .="and (
		// 					e.username ilike '%".$iLike."%'
		// 					 or a.name ilike '%".$iLike."%'
		// 					 or e.username ilike '%".$iLike."%'
		// 					 or a.ticket_number_manual ilike '%".$iLike."%'
		// 					 or a.ticket_number ilike '%".$iLike."%'
		// 					 or a.address ilike '%".$iLike."%')";
		// }

		if(!empty($searchData))
        {
            if($searchName=='name')
            {
                $where .=" and (a.name ilike '%".$iLike."%' ) ";
            }
            else if($searchName=='ticketNumber')
            {
                $where .=" and (a.ticket_number_manual ilike '%".$iLike."%' ) ";
						}
						else if($searchName=='newTicket')
            {
                $where .=" and (a.ticket_number ilike '%".$iLike."%' ) ";
						}
						else if($searchName=='invoice')
            {
                $where .=" and (a.trans_number ilike '%".$iLike."%' ) ";
						}
						else if($searchName=='penjual')
            {
                $where .=" and (e.username ilike '%".$iLike."%' ) ";
            }
            else
            {
                $where .=" and (a.address ilike '%".$iLike."%' ) ";
            }
        }

		$sql 		   = "
						SELECT 
							h.boarding_expired , 
							g.boarding_code, 
							f.name as ship_class_name,
							e.username, 
							c.shift_name,
							b.name as port_name,
							sp.name as ship_name,
							a.* from app.t_trx_ticket_manual a
						left join app.t_mtr_port b on a.port_id=b.id
						left join app.t_mtr_shift c on a.shift_id=c.id
						left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
						left join core.t_mtr_user e on d.user_id=e.id
						left join app.t_mtr_ship_class f on a.ship_class=f.id
						left join app.t_trx_boarding_passanger g on a.ticket_number=g.ticket_number 
						left join app.t_trx_booking_passanger h on a.ticket_number=h.ticket_number
						left join app.t_trx_open_boarding obr on g.boarding_code= obr.boarding_code
						left join app.t_mtr_ship sp on obr.ship_id =sp.id
						 {$where}";

		$sqlCount 		   = "SELECT count(a.id) as countdata from app.t_trx_ticket_manual a
						left join app.t_mtr_port b on a.port_id=b.id
						left join app.t_mtr_shift c on a.shift_id=c.id
						left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
						left join core.t_mtr_user e on d.user_id=e.id
						left join app.t_mtr_ship_class f on a.ship_class=f.id
						left join app.t_trx_boarding_passanger g on a.ticket_number=g.ticket_number 
						left join app.t_trx_booking_passanger h on a.ticket_number=h.ticket_number
						left join app.t_trx_open_boarding obr on g.boarding_code= obr.boarding_code
						left join app.t_mtr_ship sp on obr.ship_id =sp.id
						 {$where}";

		$queryCount         = $this->dbView->query($sqlCount)->row();
		$records_total 			= $queryCount->countdata;
		// $query         = $this->dbView->query($sql);
		// $records_total = $query->num_rows();

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

			$row->status_boarding=failed_label('Belum Boarding');

			if (!empty($row->boarding_code)) 
			{
				$row->status_boarding=success_label('Boarding');
			}

			$row->trx_date=empty($row->trx_date)?"":format_date($row->trx_date);
			$row->created_on=empty($row->created_on)?"":format_dateTime($row->created_on);

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

    public function get_data_vehicle()
    {
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');

		$shift_id= $this->enc->decode($this->input->post('shift'));
		$ship_class= $this->enc->decode($this->input->post('ship_class'));
		$statusBoarding= $this->enc->decode($this->input->post('statusBoarding'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData=$this->input->post('searchData');
		$searchName=$this->input->post('searchName');
		$iLike  = trim(strtoupper($this->db->escape_like_str($searchData)));


		// filter berdasarkan port di user

		if($this->get_identity_app()==0)
		{
			if(!empty($this->session->userdata('port_id')))
			{
				$port_id= $this->session->userdata('port_id');
			}
			else
			{
				$port_id= $this->enc->decode($this->input->post('port'));
			}
		}
		else
		{
			$port_id= $this->get_identity_app();
		}


		
		$field = array(
			0 =>'id',
            1=>"created_on",
            2=>"trx_date",
            3=>"trans_number",
            4=>"name",
            5=>"ticket_number_manual",
            6=>"ticket_number",
            7=>"ship_name",
            8=>"id_number",
            9=>"vehicle_class_name",
            10=>"username",
            11=>"shift_name",
            12=>"ship_class_name",
            13=>"port_name",
            14=>"boarding_code",
            15=>"total_passanger"
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status != -5 and a.trx_date >= '". $dateFrom . "' and a.trx_date < '" . $dateToNew . "'";
		// $where = " WHERE a.status not in (-5) and ( a.trx_date between '{$dateFrom}' and '{$dateTo}') ";

		if(!empty($port_id))
		{
			$where .="and (a.port_id=".$port_id.")";
		}

		if(!empty($ship_class))
		{
			$where .="and (a.ship_class=".$ship_class.")";
		}		

		if(!empty($shift_id))
		{
			$where .="and (a.shift_id=".$shift_id.")";
		}

		if($statusBoarding!="")
		{
			switch ($statusBoarding) {
				case  1 :
					$where .=" and ( g.boarding_code is not null ) ";
					break;
				
				default:
					$where .=" and ( g.boarding_code is null ) ";
					break;
			}

		}								

		// if(!empty($search['value']))
		// {
		// 	$where .=" and (a.name ilike '%".$iLike."%' 
		// 					or a.ticket_number ilike '%".$iLike."%'
		// 					or a.ticket_number_manual ilike '%".$iLike."%'
		// 					or a.trans_number ilike '%".$iLike."%'
		// 					or e.username ilike '%".$iLike."%' 
		// 					or a.id_number ilike '%".$iLike."%' 
		// 					or g.name ilike '%".$iLike."%' 

		// 				)";
		// }

		if(!empty($searchData))
		{
				if($searchName=='name')
				{
						$where .=" and (a.name ilike '%".$iLike."%' ) ";
				}
				else if($searchName=='ticketNumber')
				{
						$where .=" and (a.ticket_number_manual ilike '%".$iLike."%' ) ";
				}
				else if($searchName=='newTicket')
				{
						$where .=" and (a.ticket_number ilike '%".$iLike."%' ) ";
				}
				else if($searchName=='invoice')
				{
						$where .=" and (a.trans_number ilike '%".$iLike."%' ) ";
				}
				else if($searchName=='penjual')
				{
						$where .=" and (e.username ilike '%".$iLike."%' ) ";
				}
				else
				{
						$where .=" and (a.id_number ilike '%".$iLike."%' ) ";
				}
		}

		$sql 		   = "
							SELECT 
								h.boarding_code,
								g.name as vehicle_class_name,
								f.name as ship_class_name,
								e.username,
								c.shift_name,
								b.name as port_name,
								sp.name as ship_name,
								a.* from app.t_trx_ticket_vehicle_manual a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_shift c on a.shift_id=c.id
							left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
							left join core.t_mtr_user e on d.user_id=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_mtr_vehicle_class g on a.vehicle_class=g.id
							left join app.t_trx_boarding_vehicle h on a.ticket_number=h.ticket_number
							left join app.t_trx_open_boarding obr on h.boarding_code= obr.boarding_code
							left join app.t_mtr_ship sp on obr.ship_id =sp.id
							{$where}
						 ";

		$sqlCount		   = "
							SELECT
								count(a.id) as countdata 
							from app.t_trx_ticket_vehicle_manual a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_shift c on a.shift_id=c.id
							left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
							left join core.t_mtr_user e on d.user_id=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_mtr_vehicle_class g on a.vehicle_class=g.id
							left join app.t_trx_boarding_vehicle h on a.ticket_number=h.ticket_number
							left join app.t_trx_open_boarding obr on h.boarding_code= obr.boarding_code
							left join app.t_mtr_ship sp on obr.ship_id =sp.id 
							{$where}
						 ";

		$queryCount         = $this->dbView->query($sqlCount)->row();
		$records_total 			= $queryCount->countdata;
		// $query         = $this->dbView->query($sql);
		// $records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|-1|non aktif'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1|aktif'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions  =" ";

			$row->status_boarding=failed_label('Belum Boarding');

			if (!empty($row->boarding_code)) 
			{
				$row->status_boarding=success_label('Boarding');
			}


     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

     		$row->created_on=empty($row->created_on)?"":format_dateTime($row->created_on);
     		$row->trx_date=empty($row->trx_date)?"":format_date($row->trx_date);

			$rows[] = $row;
			unset($row->assignment_code);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}


	public function get_byCode($code){
		$query = $this->dbView->query("SELECT port_code FROM t_mtr_port WHERE port_code = '$code' and status = 1");
		return $query->row();
	}
	
	public function get_prov(){
		return $this->dbView->query("select * from public.t_mtr_province order by name asc")->result();
	}
	
	public function get_area($id){
		return $this->dbView->query("select * from public.t_mtr_city where province_id='$id' order by name asc")->result();
	}
	
	public function get_district($id){
		return $this->dbView->query("select * from public.t_mtr_district where city_id='$id' order by name asc")->result();
	}


	public function manual_passanger($where)
	{
		$sql 		   = "SELECT h.boarding_expired , g.boarding_code, f.name as ship_class_name, e.username, 
				c.shift_name, b.name as port_name, a.* from app.t_trx_ticket_manual a
				left join app.t_mtr_port b on a.port_id=b.id
				left join app.t_mtr_shift c on a.shift_id=c.id
				left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
				left join core.t_mtr_user e on d.user_id=e.id
				left join app.t_mtr_ship_class f on a.ship_class=f.id
				left join app.t_trx_boarding_passanger g on a.ticket_number=g.ticket_number 
				left join app.t_trx_booking_passanger h on a.ticket_number=h.ticket_number
				 {$where}";

		return $this->dbView->query($sql);				 
	}


	public function manual_vehicle($where)
	{
		$sql 		  = " SELECT h.boarding_code, g.name as vehicle_class_name, f.name as ship_class_name, e.username, c.shift_name, b.name as port_name, 
								a.* from app.t_trx_ticket_vehicle_manual a
								left join app.t_mtr_port b on a.port_id=b.id
								left join app.t_mtr_shift c on a.shift_id=c.id
								left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
								left join core.t_mtr_user e on d.user_id=e.id
								left join app.t_mtr_ship_class f on a.ship_class=f.id
								left join app.t_mtr_vehicle_class g on a.vehicle_class=g.id
								left join app.t_trx_boarding_vehicle h on a.ticket_number= h.ticket_number
				 {$where}";

		return $this->dbView->query($sql);				 
	}

	public function get_schedule($schedule_date, $ship_class,$port_id, $categorySchedule="", $dockSchedule="", $schedule_date2="")	
	{
		$where ="";

		if (!empty($schedule_date2))
		{
			$where .="and a.schedule_date between '{$schedule_date}' and  '{$schedule_date2}'";
		}
		else
		{
			$where .="and a.schedule_date='{$schedule_date}'";

		}

		if(!empty($dockSchedule))
		{
			$where .=" and ( a.dock_id='{$dockSchedule}' )";
		}

		if(!empty($categorySchedule))
		{
			if($categorySchedule=='M')
			{

				$where .=" and left(a.schedule_code,'1')='M' ";
			}
			else
			{
				$where .=" and left(a.schedule_code,'1')='J' ";	
			}
		}

		$sql="
			SELECT f.name as port_name, 
				a.schedule_date, 
				e.name, 
				c.name as ship_name ,
				d.name as dock_name , 
				a.docking_date, 
				a.sail_date, 
				a.schedule_code 
				from app.t_trx_schedule a
			join app.t_mtr_schedule b on a.schedule_code=b.schedule_code
			join app.t_mtr_ship c on a.ship_id=c.id
			join app.t_mtr_dock d on a.dock_id=d.id
			join app.t_mtr_ship_class e on c.ship_class=e.id
			join app.t_mtr_port f on a.port_id=f.id
			where a.open_boarding_date is not null and a.status=1
			-- and a.schedule_date='{$schedule_date}'
			-- and a.schedule_date between '{$schedule_date}' and  '{$schedule_date2}'
			and c.ship_class='{$ship_class}'
			and a.port_id='{$port_id}'
			{$where}
			order by a.docking_date asc
		";

		// die($sql); exit;
		return $this->dbView->query($sql);	
	}

	public function get_identity_app()
	{
		$data=$this->dbView->query("select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}

	public function get_pass_array($schedule_code)
	{
		return $this->dbView->query("
				select dock_id, port_id, schedule_date, boarding_code, ship_class from app.t_trx_open_boarding
				where schedule_code='".$schedule_code."' and status in (1,0);

			");
	}

	public function getBookingVehicle($ticketVehicle)
	{
		return $this->dbView->query("SELECT b.ticket_number as ticket_number_passanger, a.* from app.t_trx_booking a
								left join app.t_trx_booking_passanger b on a.booking_code=b.booking_code
								left join app.t_trx_booking_vehicle c on a.booking_code=c.booking_code
								where c.ticket_number='{$ticketVehicle}'
				");
	}

	public function checkSheduleShipClass_07102021($scheduleCode, $port)
	{
		return $this->dbView->query("
						SELECT sp.ship_class, sc.* from app.t_trx_schedule sc
						join app.t_mtr_ship sp on sc.ship_id=sp.id
						where sc.schedule_code='{$scheduleCode}'
						and  sail_date is not null 
						and sc.port_id=$port

		");
	}

	public function checkSheduleShipClass($scheduleCode, $port)
	{
		return $this->dbView->query("
						SELECT sp.ship_class, sc.* from app.t_trx_schedule sc
						join app.t_mtr_ship sp on sc.ship_id=sp.id
						where sc.schedule_code='{$scheduleCode}'
						and  open_boarding_date is not null 
						and sc.port_id=$port

		");
	}	

	public function getTerminalCode($portId)
	{
		// 16 hard cord terminal untuk tiket manual/ sobek
		$data=$this->dbView->query("SELECT a.* from app.t_mtr_device_terminal  a
										 left join app.t_mtr_device_terminal_type b on a.terminal_type=b.terminal_type_id
										where a.terminal_type=13 and a.port_id={$portId} AND a.status=1 and b.status=1 
										")->row();
		return $data->terminal_code;
	}						

	public function getService($ticketNumber)
	{
		$qry="
			SELECT 
				ticket_number, 
				service_id
			from app.t_trx_booking_passanger
			where ticket_number='{$ticketNumber}'
			union
			SELECT
				ticket_number, 
				service_id
			from app.t_trx_booking_vehicle
			where ticket_number='{$ticketNumber}'

		";

		return $this->db->query($qry)->row();
	}

	public function select_data($table, $where)
	{
		return $this->dbView->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->dbAction->insert($table, $data);
	}

	public function update_data($table,$data,$where)
	{
		$this->dbAction->where($where);
		$this->dbAction->update($table, $data);
	}

	public function delete_data($table,$data,$where)
	{
		$this->dbAction->where($where);
		$this->dbAction->delete($table, $data);
	}
}
