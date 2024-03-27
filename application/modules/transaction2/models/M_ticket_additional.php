<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_Ticket_additional extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/bank';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$port_name= $this->input->post('port');
		$team_name= $this->input->post('team');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
			1 =>'bank_abbr',
			2 =>'bank_name',
			3 =>'transfer_fee',
			4 =>'status',

		);

		$order_column = $field[$order_column];

		$where = " WHERE status not in (-5) ";

		if(!empty($search['value']))
		{
			$where .="and ( name ilike '%".$iLike."%')";
		}

		$sql 		   = "
							select * from core.t_mtr_bank
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
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions  =" ";

			if($row->status == 1){
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

			$row->transfer_fee=idr_currency($row->transfer_fee);

     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

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

	public function getService($where="")
	{
		return $this->db->query(
								"
									SELECT c.ticket_number, b.ticket_number, a.* from app.t_trx_booking a
									left join app.t_trx_booking_passanger b on a.booking_code=b.booking_code
									left join app.t_trx_booking_vehicle c on a.booking_code=c.booking_code
									{$where}
									limit 500

								");
	}

	public function getDataPassanger($serviceId,$ticketNumber="", $bookingCode="")	
	{

		$where="";

		if(!empty($ticketNumber))
		{
			$where .=" and a.ticket_number='{$ticketNumber}' ";
		}

		if(!empty($bookingCode))
		{
			$where .=" and a.booking_code='{$bookingCode}' ";
		}		


		return $this->db->query(
						"
							SELECT  e.name as identitas_name, c.name as service_name, d.name as passanger_type_name, a.* from app.t_trx_booking_passanger a
							left join app.t_trx_booking b on a.booking_code=b.booking_code
							left join app.t_mtr_service c on b.service_id=c.id
							left join app.t_mtr_passanger_type d on a.passanger_type_id=d.id
							left join app.t_mtr_passanger_type_id e on a.id_type=e.id
							where a.service_id='{$serviceId}' 
							{$where}

						");
		
	}

	public function getDataVehicle($bookingCode)
	{
		return $this->db->query(
						"
							SELECT  b.total_passanger, e.name as driver_name, d.name as ship_class_name, c.name as vehicle_class_name, a.* from app.t_trx_booking_vehicle a
							left join app.t_trx_booking b on a.booking_code=b.booking_code
							left join app.t_mtr_vehicle_class c on a.vehicle_class_id=c.id
							left join app.t_mtr_ship_class d on b.ship_class=d.id
							left join 
							(
							      select distinct on (booking_code) booking_code, name,  min(ticket_number) from app.t_trx_booking_passanger
							      where status !='-5'
							      group by booking_code, ticket_number, name
							) e on e.booking_code=b.booking_code
							where  a.booking_code='{$bookingCode}'

						");
		
	}	

	public function getBookingCode($ticket_number)
	{
		return $this->db->query("
									SELECT distinct a.booking_code from app.t_trx_booking a
									left join app.t_trx_booking_passanger b on a.booking_code=b.booking_code
									left join app.t_trx_booking_vehicle c on a.booking_code=c.booking_code
									where c.ticket_number='{$ticket_number}' or b.ticket_number='{$ticket_number}'
								");
	}

	public function select_data($table, $where)
	{
		return $this->db->query("select * from $table $where");
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
