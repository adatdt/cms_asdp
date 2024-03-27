<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class M_refund_force_majeure extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/refund_force_majeure';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
			// 1 =>'force_majeure_code',
			// 2 =>'date',
			// 3 =>'force_majeure_type',
			// 4 =>'remark',
			// 5 =>'port_name',
			// 6 =>'extend_param',
			// 7 =>'status',
		);

		$order_column = $field[$order_column];

		// dalam penampilan user, jika salah satu user -5 maka data tidak muncul
		$where = " WHERE a.status !='-5' ";

		// pengecekan port di dapan denga usernya dia
		// if(!empty($this->session->userdata("port_id")))
		// {
		// 	$port_id=$this->session->userdata("port_id");
		// }
		// else
		// {
		// 	$port_id=$this->enc->decode($this->input->post("port"));
		// }

		// if(!empty($port_id))
		// {
		// 	$where .=" and (a.port_id={$port_id}) ";
		// }

		// if(!empty($search['value']))
		// {
		// 	$where .="and (b.name ilike '%".$iLike."%'
		// 	 			   or a.force_majeure_code ilike '%".$iLike."%' 
		// 	 			   or a.remark ilike '%".$iLike."%' 
		// 					)";
		// }

		$sql 		   = "
							SELECT * from app.t_trx_refund_force_majeure a
							$where					
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
			$code_enc=$this->enc->encode($row->force_majeure_code);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));


			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		// $delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions="";

     		$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);

			if($row->status == 1){
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}


			$check=checkBtnAccess($this->_module,'detail');
			if($check)
			{
				// jika tipe force majeurenya eksekutif (1), pengecekan tipe force majeure
				if($row->force_majeure_type==1)
				{
					$row->actions .="<a href='".site_url()."transaction/force_majeure/list_manifest/".$code_enc."' class='btn btn-primary btn-sm' title='Detail Manifest'> <i class='fa fa-search-plus'></i> </a> ";	
				}
				else
				{
					$row->actions .="<a href='".site_url()."transaction/force_majeure/list_manifest_general/".$code_enc."' class='btn btn-primary btn-sm' title='Detail Manifest'> <i class='fa fa-search-plus'></i> </a> ";
				}
			}

     		$row->no=$i;
     		$row->date=empty($row->date)?"":format_date($row->date);
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

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

	public function check_passanger_type($ticket_number)
	{
		return $this->db->query(
				"SELECT c.name as passanger_type_name, b.service_id,a.* from app.t_trx_booking_passanger a
				left join app.t_trx_booking b on a.booking_code=b.booking_code
				left join app.t_mtr_passanger_type c on a.passanger_type_id=c.id
				left join app.t_trx_booking_vehicle d on a.booking_code=d.booking_code
				where (a.ticket_number='{$ticket_number}' or d.ticket_number='{$ticket_number}')
				and (a.status !='-5' or d.status !='-5' ) ");
	}

	public function get_passanger($where)
	{
		return $this->db->query("select c.name as passanger_type_name, b.service_id,a.* from app.t_trx_booking_passanger a
		join app.t_trx_booking b on a.booking_code=b.booking_code
		join app.t_mtr_passanger_type c on a.passanger_type_id=c.id
		{$where}

		");
	}

	public function get_data_vehicle($where)
	{
		return $this->db->query("
				select d.name as vehicle_class_name, c.name as name, c.gender, c.age, b.service_id,a.* from app.t_trx_booking_vehicle a
				left join app.t_trx_booking b on a.booking_code=b.booking_code
				left join (
						select distinct on (booking_code) booking_code, name, gender,age, min(ticket_number) from app.t_trx_booking_passanger
						where status !='-5'
						group by booking_code, ticket_number, name, gender, age
				) c on a.booking_code=c.booking_code
				left join app.t_mtr_vehicle_class d on a.vehicle_class_id=d.id
				{$where}
			");
	}

	public function get_sail_date($ticket_number)
	{
		return $this->db->query("
			SELECT e.sail_date, c.boarding_code, a.* from app.t_trx_booking_passanger a
			left join app.t_trx_booking b on a.booking_code=b.booking_code
			left join app.t_trx_boarding_passanger c on a.ticket_number=c.ticket_number
			left join app.t_trx_open_boarding d on c.boarding_code=d.boarding_code
			left join app.t_trx_schedule e on d.schedule_code=e.schedule_code
			where a.ticket_number='{$ticket_number}'
			");
	}

	public function get_sail_date_vehicle($ticket_number)
	{
		return $this->db->query("
			SELECT e.sail_date,e.schedule_code, c.boarding_code,
			a.* from app.t_trx_booking_vehicle a
			left join app.t_trx_booking b on a.booking_code=b.booking_code
			left join app.t_trx_boarding_vehicle c on a.ticket_number=c.ticket_number
			left join app.t_trx_open_boarding d on c.boarding_code=d.boarding_code
			left join app.t_trx_schedule e on d.schedule_code=e.schedule_code
			where a.ticket_number='{$ticket_number}'
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

	public function insert_data_batch($table,$data)
	{
		$this->db->insert_batch($table, $data);
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