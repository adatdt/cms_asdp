<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : entranceFeeModel
 * -----------------------
 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  2023
 *
 */

class EntranceFeeModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'fare/entranceFee';
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

		$port = $this->enc->decode($this->input->post('port'));
		$passangerType = $this->enc->decode($this->input->post('passangerType'));

		
		$field = array(
			0 =>'id',
			1 => 'passanger_type_id',
			2 => 'port_id',
			3 => 'entrance_fee',
			4 => 'status',

		);

		$order_column = $field[$order_column];

		$where = " WHERE tmefp.status not in (-5) ";

		if(!empty($port))
		{
			$where .=" and  tmefp.port_id={$port} ";
		}

		if(!empty($passangerType))
		{
			$where .=" and  tmefp.passanger_type_id={$passangerType} ";
		}		

		if(!empty($search['value']))
		{
			$where .="and  tmp.name ilike '%".$iLike."%' ESCAPE '!'
						or tmsc.name ilike '%".$iLike."%' ESCAPE '!'
					";
		}

		$sql 		   = "
							select 
								tmefp.entrance_fee ,
								tmefp.status ,
								tmefp.id ,
								tmp.name as port_name,
								tmsc.name as passanger_type_name
							from app.t_mtr_entrance_fee_passanger tmefp 
							join app.t_mtr_port tmp on tmefp.port_id = tmp.id
							join app.t_mtr_passanger_type tmsc on tmefp.passanger_type_id =tmsc.id
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

			$row->entrance_fee=idr_currency($row->entrance_fee);

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
			'data'           => $rows,
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),

		);
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
