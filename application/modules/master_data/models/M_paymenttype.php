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

class M_paymenttype extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/payment_type';
	}

    public function dataList(){


		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$method=$this->enc->decode($this->input->post('method'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$field = array(
			0 =>'id',
			1 => 'name',
			2 => 'payment_type',
			3 => 'method_name',
			4 => 'extra_fee' ,
			5 => 'status_web',
			6 => 'status_mobile',
			7 => 'status_mpos',
			8 => 'status_vm',
			9 => 'status_pos_passanger',
			10 => 'status_pos_vehicle',
			10 => 'status_ifcs',
			11 => 'status_b2b',
			12 => 'status_web_cs',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.id is not null ";

		if(!empty($method))
		{
			$where .="and (a.payment_method_id='".$method."')";
		}

		if (!empty($search['value'])){
			$where .="and (a.name ilike '%".$iLike."%' or a.payment_type ilike '%".$iLike."%'  
							)";
		}

		$sql = "select d.name as pay_type_name, c.bank_name, b.name as method_name, a.* from app.t_mtr_payment_type a
				left join app.t_mtr_payment_method b on a.payment_method_id=b.id
				left join core.t_mtr_bank c on a.bank_id=c.id
				left join app.t_mtr_pay_type d on a.type_id=d.id
				{$where}";

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
			$nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
     		$aktif       = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

			$row->id 	 = $this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$row->id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$row->id}");

     		$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

			// if($row->status == 1){
			// 	$row->status   = success_label('Aktif');
			// 	$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', '."'".$nonaktif."'".')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			// }
			// else
			// {
			// 	$row->status   = failed_label('Tidak Aktif');
			// 	$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', '."'".$aktif."'".')" title="Aktif"> <i class="fa fa-check"></i> </button> ');
			// }
     		
     		$row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

     		$row->extra_fee= idr_currency($row->extra_fee);
     		$row->status_mpos==1?$row->status_mpos=success_label('Aktif'):$row->status_mpos=failed_label('Tidak Aktif');
     		$row->status_vm==1?$row->status_vm=success_label('Aktif'):$row->status_vm=failed_label('Tidak Aktif'); 
     		$row->status_mobile==1?$row->status_mobile=success_label('Aktif'):$row->status_mobile=failed_label('Tidak Aktif');
     		$row->status_web==1?$row->status_web=success_label('Aktif'):$row->status_web=failed_label('Tidak Aktif');
     		$row->status_pos_passanger==1?$row->status_pos_passanger=success_label('Aktif'):$row->status_pos_passanger=failed_label('Tidak Aktif');
     		$row->status_pos_vehicle==1?$row->status_pos_vehicle=success_label('Aktif'):$row->status_pos_vehicle=failed_label('Tidak Aktif');
     		$row->status_ifcs==1?$row->status_ifcs=success_label('Aktif'):$row->status_ifcs=failed_label('Tidak Aktif');
     		$row->status_b2b==1?$row->status_b2b=success_label('Aktif'):$row->status_b2b=failed_label('Tidak Aktif');
     		$row->status_web_cs==1?$row->status_web_cs=success_label('Aktif'):$row->status_web_cs=failed_label('Tidak Aktif');


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

	public function get_byCode($code){
		$query = $this->db->query("SELECT port_code FROM t_mtr_port WHERE port_code = '$code' and status = 1");
		return $query->row();
	}
	
	public function get_prov(){
		return $this->db->query("select * from public.t_mtr_province order by name asc")->result();
	}
	
	public function get_area($id){
		return $this->db->query("select * from public.t_mtr_city where province_id='$id' order by name asc")->result();
	}
	
	public function get_district($id){
		return $this->db->query("select * from public.t_mtr_district where city_id='$id' order by name asc")->result();
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