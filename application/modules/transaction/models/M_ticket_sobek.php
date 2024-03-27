<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : M_ticket_sobek
 * -----------------------
 *
 * @author     Adat <adatdt@gmail.com>
 * @copyright  2020
 *
 */

class M_ticket_sobek extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/ticket_sobek';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');

		$shift_id= $this->enc->decode($this->input->post('shift'));
		$ship_class= $this->enc->decode($this->input->post('ship_class'));
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
			1 =>'created_on',
			2 =>'trx_date',
			3 =>'trans_number',
			4 =>'name',
			5 =>'ticket_number_manual',
			6 =>'ticket_number',
			7 =>'gender',
			8 =>'address',
			9 =>'username',
			10 =>'shift_name',
			11 =>'ship_class_name',
			12 =>'port_name',
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

		// if(!empty($search['value']))
		// {
		// 	$where .=" and (a.name ilike '%".$iLike."%' 
		// 					or a.ticket_number ilike '%".$iLike."%'
		// 					or a.ticket_number_manual ilike '%".$iLike."%'
		// 					or a.trans_number ilike '%".$iLike."%'
		// 					or e.username ilike '%".$iLike."%' 
		// 					or a.address ilike '%".$iLike."%' 

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
                $where .=" and (a.address ilike '%".$iLike."%' ) ";
            }
        }

		$sql 		   = "
							SELECT h.boarding_expired , g.boarding_code, f.name as ship_class_name, e.username, c.shift_name, b.name as port_name, a.* from app.t_trx_ticket_manual a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_shift c on a.shift_id=c.id
							left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
							left join core.t_mtr_user e on d.user_id=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_trx_boarding_passanger g on a.ticket_number=g.ticket_number 
							left join app.t_trx_booking_passanger h on a.ticket_number=h.ticket_number
							{$where}
						 ";
		
		$sqlCount	   = "SELECT count(a.id) as countdata
							from app.t_trx_ticket_manual a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_shift c on a.shift_id=c.id
							left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
							left join core.t_mtr_user e on d.user_id=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_trx_boarding_passanger g on a.ticket_number=g.ticket_number 
							left join app.t_trx_booking_passanger h on a.ticket_number=h.ticket_number
							{$where}
						 ";				 

		$queryCount         = $this->db->query($sqlCount)->row();
		$records_total 			= $queryCount->countdata;
		// $query         = $this->db->query($sql);
		// $records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
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

			if($row->status == 1)
			{
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
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


    public function get_data_vehicle(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');

		$shift_id= $this->enc->decode($this->input->post('shift'));
		$ship_class= $this->enc->decode($this->input->post('ship_class'));
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


		
		// $field = array(
		// 	0 =>'id',
		// 	1 =>'created_on',
		// 	2 =>'trx_date',
		// 	3 =>'name',
		// 	4 =>'ticket_number_manual',
		// 	5 =>'ticket_number',
		// 	6 =>'trans_number',
		// 	7 =>'gender',
		// 	8 =>'address',
		// 	9 =>'username',
		// 	10 =>'shift_name',
		// 	11 =>'ship_class_name',
		// 	12 =>'port_name',
		// );

		$field=array(
			 0 =>'id',
             1=>"created_on",
             2=>"trx_date",
             3=>"trans_number",
             4=>"name",
             5=>"ticket_number_manual",
             6=>"ticket_number",
             7=>"id_number",
             8=>"vehicle_class_name",
             9=>"username",
             10=>"shift_name",
             11=> "ship_class_name",
             12=>"port_name",
             13=>"total_passanger"
             )	;

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
								g.name as vehicle_class_name, 
								f.name as ship_class_name, 
								e.username, c.shift_name, 
								b.name as port_name, 
							a.* from app.t_trx_ticket_vehicle_manual a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_shift c on a.shift_id=c.id
							left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
							left join core.t_mtr_user e on d.user_id=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_mtr_vehicle_class g on a.vehicle_class=g.id
							{$where}
						 ";

		$sqlCount 		   = "SELECT count(a.id) as countdata from app.t_trx_ticket_vehicle_manual a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_shift c on a.shift_id=c.id
							left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
							left join core.t_mtr_user e on d.user_id=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_mtr_vehicle_class g on a.vehicle_class=g.id
							{$where}
						 ";
		
		$queryCount         = $this->db->query($sqlCount)->row();
		$records_total 			= $queryCount->countdata;
		// $query         = $this->db->query($sql);
		// $records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
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

			if($row->status == 1)
			{
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
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





	public function get_identity_app()
	{
		$data=$this->db->query("select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}

	public function get_shift($port_id)
	{
		return $this->db->query("select b.shift_name, a.* from app.t_mtr_shift_time a
								left join app.t_mtr_shift b on a.shift_id=b.id
								where a.port_id={$port_id} and a.status=1
								");
	}

	public function get_trans_number($code)
	{
		return $this->db->query("SELECT '{$code}'||SUBSTRING(EXTRACT(YEAR FROM now())::varchar, 3,2)||
								to_char(EXTRACT(DOY FROM now()), 'fm000')||to_char(EXTRACT(HOURS FROM now()), 'fm000')||
								(to_char(nextval('app.trans_number_seq'), 'fm0000') ) as trans_number ")->row();
	}

	public function get_ob_27092021($port_id,$trx_date,$shift_id) // ob code yang hanya menggunakan hanya  open shift saja
	{
		return $this->db->query("
								select distinct concat(b.first_name,' ',b.last_name) as full_name,  b.username, a.* from app.t_trx_opening_balance a
								join core.t_mtr_user b on a.user_id=b.id
								join app.t_trx_assignment_user_pos c on a.assignment_code=c.assignment_code
								and a.status =1 
								and c.port_id={$port_id} and a.shift_id={$shift_id} and  a.trx_date='{$trx_date}' order by b.username ");
	}

	public function get_ob($port_id,$trx_date,$shift_id) // ob code yang  menggunakan baik yang sudah tutup dengan close boarding
	{
		return $this->db->query("
								select distinct concat(b.first_name,' ',b.last_name) as full_name,  b.username, a.* from app.t_trx_opening_balance a
								join core.t_mtr_user b on a.user_id=b.id
								join app.t_trx_assignment_user_pos c on a.assignment_code=c.assignment_code
								and a.status <>'-5' 
								and c.port_id={$port_id} and a.shift_id={$shift_id} and  a.trx_date='{$trx_date}' order by b.username ");
	}	

	public function get_route ($where)
	{
		return $this->db->query("
								select concat(b.name,' - ',c.name) as route_name, 
								c.name as destination_name, b.name as origin_name, a.* from app.t_mtr_rute a
								left join app.t_mtr_port b on a.origin=b.id
								left join app.t_mtr_port c on a.destination=c.id
								{$where}
								");		
	}

	public function edit_total_cash( $amount_cash, $ob_code)
	{
		$updated_by=$this->session->userdata("username");
		$updated_on=date("Y-m-d H:i:s");

		$this->db->query(" update app.t_trx_opening_balance set total_cash=(total_cash+{$amount_cash}) , updated_by='{$updated_by}', updated_on='{$updated_on}'  where ob_code='{$ob_code}' ");
	}


    public function download(){


		$dateFrom = $this->input->get('dateFrom');
		$dateTo = $this->input->get('dateTo');
		$shift_id= $this->enc->decode($this->input->get('shift'));
		$ship_class= $this->enc->decode($this->input->get('ship_class'));
		$cari = $this->input->get('cari');
		$searchName = $this->input->get('searchName');
		$iLike        = trim(strtoupper($this->db->escape_like_str($cari)));


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


		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status != -5 and a.trx_date >= '". $dateFrom . "' and a.trx_date < '" . $dateToNew . "'";
		// $where = " WHERE a.status not in (-5) and ( a.trx_date between '".$dateFrom."' and '".$dateTo."') ";

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

		if(!empty($cari))
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
							SELECT f.name as ship_class_name, e.username, c.shift_name, b.name as port_name, a.* from app.t_trx_ticket_manual a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_shift c on a.shift_id=c.id
							left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
							left join core.t_mtr_user e on d.user_id=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							{$where}
							order by a.id asc
						 ";

		return $this->db->query($sql);

	}


    public function download2(){


		$dateFrom = $this->input->get('dateFrom');
		$dateTo = $this->input->get('dateTo');
		$shift_id= $this->enc->decode($this->input->get('shift'));
		$ship_class= $this->enc->decode($this->input->get('ship_class'));
		$cari = $this->input->get('cari');
		$searchName = $this->input->get('searchName');
		$iLike        = trim(strtoupper($this->db->escape_like_str($cari)));


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

		if(!empty($cari))
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
							SELECT g.name as vehicle_class_name, f.name as ship_class_name, e.username, c.shift_name, b.name as port_name, 
							a.* from app.t_trx_ticket_vehicle_manual a
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_shift c on a.shift_id=c.id
							left join app.t_trx_opening_balance d on a.ob_code=d.ob_code
							left join core.t_mtr_user e on d.user_id=e.id
							left join app.t_mtr_ship_class f on a.ship_class=f.id
							left join app.t_mtr_vehicle_class g on a.vehicle_class=g.id
							{$where}
							order by a.id asc
						 ";

		return $this->db->query($sql);

	}
	public function getTerminalCode($portId)
	{
		// 13 hard cord terminal untuk tiket manual/ sobek
		$data=$this->db->query("SELECT a.* from app.t_mtr_device_terminal  a
										 left join app.t_mtr_device_terminal_type b on a.terminal_type=b.terminal_type_id
										where a.terminal_type=13 and a.port_id={$portId} AND a.status=1 and b.status=1 
										")->row();
		return $data->terminal_code;
	}			


	public function select_data($table, $where)
	{
		return $this->db->query("select * from $table $where");
	}


	public function query_update_data($table, $amount, $obCode, $totalCash)
	{
		$userName=$this->session->userdata("username");

		// check apakah sudah ke create data dengan payment type cash dan transaction type nya 1 (pembelian tiket)
		$checkPaymentCash=$this->select_data($table, " where ob_code='{$obCode}' and payment_type='cash' and transaction_type=1 ");

		if($checkPaymentCash->num_rows()>0)
		{
			// jika paymnet typenya sudah ada yang bertipe cash maka di update
			$this->db->query(" update {$table} set amount=amount+{$amount} ,total_transaction=total_transaction+{$totalCash} , updated_on=now(), updated_by='{$userName}'  where ob_code='{$obCode}' and payment_type='cash' ");
		}
		else
		{

			$data =array("ob_code"=>$obCode,
						"amount"=>$amount,
						"total_transaction"=>$totalCash,
						"payment_type"=>"cash",
						"status"=>1,
						"transaction_type"=>1, // tipe transaksi pembelian tiket ini di hardcord
						"created_on"=>date("Y-m-d H:i:s"),
						"created_by"=>$this->session->userdata("username")
			
					);

			$this->insert_data($table, $data);

		}

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
