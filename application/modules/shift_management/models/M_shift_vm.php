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

class M_shift_vm extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'shift_management/shift_vm';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$port_id= $this->enc->decode($this->input->post('port'));
		$shift_id= $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$field = array(
			0 =>'id',
			1 =>'terminal_code',
			2 =>'terminal_name',
			3 =>'terminal_type_name',
			4 =>'port_name',
			5 =>'shift_name',
			6 =>'shift_login',
			7 =>'shift_logout',
			8 =>'status'
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) ";

		if(!empty($shift_id))
		{
			$where .="and (a.shift_id=".$shift_id.")";
		}

		if(!empty($port_id))
		{
			$where .="and (b.port_id=".$port_id.")";
		}

		if(!empty($search['value']))
		{
			$where .="and (a.terminal_code ilike '%".$search['value']."%' or b.terminal_name ilike '%".$iLike."%' )";
		}

		$sql 		   = "
						select e.name as port_name,d.shift_name, c.terminal_type_name, b.terminal_name, a.* from app.t_mtr_shift_vending a
						left join app.t_mtr_device_terminal b on a.terminal_code=b.terminal_code
						left join app.t_mtr_device_terminal_type c on b.terminal_type=c.terminal_type_id
						left join app.t_mtr_shift d on a.shift_id=d.id
						left join app.t_mtr_port e on b.port_id=e.id
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

			$nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
     		$aktif       = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));


			$row->number = $i;

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
     		$close_url   = site_url($this->_module."/action_close_balance/{$id_enc}");

     		$row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

			 $row->shift_logout = date("H:i",strtotime($row->shift_logout));
			 $row->shift_login = date("H:i",strtotime($row->shift_login));

			if($row->status == 1){
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', '."'".$nonaktif."'".')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');

			}else{
				$row->status   = failed_label('Tidak AKtif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', '."'".$aktif."'".')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
			}


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

	public function select_data($table, $where="")
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


	public function get_detail($where="")
	{
		return $this->db->query("
							select b.port_id as port_id,d.shift_name, c.terminal_type_name, b.terminal_name, a.* from app.t_mtr_shift_vending a
							left join app.t_mtr_device_terminal b on a.terminal_code=b.terminal_code
							left join app.t_mtr_device_terminal_type c on b.terminal_type=c.terminal_type_id
							left join app.t_mtr_shift d on a.shift_id=d.id
							left join app.t_mtr_port e on b.port_id=e.id
							$where
			");
	}

	public function get_device_code($port_id)
	{
		$qry="
			select 
				a.terminal_code,
				a.terminal_name,
				b.terminal_type_name 
			from 
			app.t_mtr_device_terminal a
			left join app.t_mtr_device_terminal_type b on a.terminal_type  = b.terminal_type_id 
			where a.status =1 
			and a.terminal_type in(3,21)  
			and port_id=".$port_id."		
		";

        $data=array();
        if(!empty($port_id))
        {    
			$row = $this->db->query($qry)->result();
            foreach ($row as $key => $value) {
                $value->full_code=$value->terminal_code." - ".$value->terminal_name;
				$value->terminal_code2 =$value->terminal_code;
                $value->terminal_code=$this->enc->encode($value->terminal_code);
                $data[]=$value;
            }
        }		

		return $data;

	}	

}
