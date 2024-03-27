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

class Dock_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'pelabuhan/dock';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		
		$dock = $this->enc->decode($this->input->post('dock'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));


		if(!empty($this->session->userdata('port_id')))
		{
			$port = $this->session->userdata('port_id');
		}
		else
		{
			$port = $this->enc->decode($this->input->post('port'));
		}


		$field = array(
			0 =>'id',
			1 =>'name',
			2 =>'port_name',
			3 =>'fare',
			4 =>'tambat_fare',
			5 =>'ship_class_name',
			6 =>'status',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) ";

		if(!empty($port))
		{
			$where .=" and (a.port_id=".$port.")"; 
		}

		if(!empty($dock))
		{
			$where .=" and (a.id=".$dock.")"; 
		}

		if (!empty($search['value'])){
			$where .="and (a.name ilike '".$search['value']."' or b.name ilike '".$search['value']."')";	
		}

		$sql 		   = "
							select c.name as ship_class_name, b.name as port_name, a.* from app.t_mtr_dock a 
							left join app.t_mtr_port b on a.port_id=b.id
							left join app.t_mtr_ship_class c on a.ship_class_id=c.id
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
			$row->number = $i;
			$nonaktif = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|0|'.$row->name."|".$row->port_id));
     		$aktif = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1|'.$row->name."|".$row->port_id));

			$id=$this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id}");

     		$row->actions="";

     		$row->actions.= generate_button_new($this->_module, 'edit', $edit_url);

     		if ($row->status==1)
     		{
     			$row->status=success_label("Aktif");
     			$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
     		}
     		else
     		{
     			$row->status=failed_label("Tidak Aktif");	
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="aktifkan"> <i class="fa fa-check"></i> </button> ');
     		}

     		$row->fare=idr_currency($row->fare);
     		$row->tambat_fare=idr_currency($row->tambat_fare);


     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

			$rows[] = $row;
			// unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
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


}
