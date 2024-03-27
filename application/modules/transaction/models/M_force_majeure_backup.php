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

class M_force_majeure extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/user_bank';
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
			0 =>'username',
			1 =>'username',
			2 =>'full_name',
			3 =>'group_name',
			4 =>'bank_name',
			5 =>'user_status',
		);

		$order_column = $field[$order_column];


		// dalam penampilan user, jika salah satu user -5 maka data tidak muncul
		$where = " WHERE (a.status !='-5' or b.status='-5') ";

		if(!empty($search['value']))
		{
			$where .="and (b.username ilike '%".$iLike."%' or concat(b.first_name,' ',b.last_name) ilike '%".$iLike."%' or d.name ilike '%".$iLike."%' or c.bank_name ilike '%".$iLike."%' 
							)";
		}

		$sql 		   = "
							select d.name as group_name, c.bank_name, b.username, b.status as status_user, 
							concat(b.first_name,' ',b.last_name) as full_name, a.* from core.t_mtr_user_bank a
							left join core.t_mtr_user b on a.user_id=b.id
							left join core.t_mtr_bank c on a.bank_abbr=c.bank_abbr
							left join core.t_mtr_user_group d on b.user_group_id=d.id		
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
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|-1'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		// $delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

			if($row->status_user == 1){
				$row->status_user   = success_label('Aktif');
				// $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}else{
				$row->status_user   = failed_label('Tidak Aktif');
				// $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

     		$row->no=$i;
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
						select distinct on (booking_code) booking_code, name, gender,age, ticket_number from app.t_trx_booking_passanger
						where status !='-5'
						group by booking_code, ticket_number, name, gender, age
						having min(ticket_number)=ticket_number
				) c on a.booking_code=c.booking_code
				left join app.t_mtr_vehicle_class d on a.vehicle_class_id=d.id
				{$where}
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
