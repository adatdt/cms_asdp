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

class M_sailing_company extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/sailing_company';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$port = $this->enc->decode($this->input->post('port'));
		$company = $this->enc->decode($this->input->post('company'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$field = array(
			0 =>'id',
			1 =>'segment',
			2 =>'segment_code',
			3 =>'company_name',
			4 =>'port_name',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) ";

		if(!empty($port))
		{
			$where .=" and (a.port_id=".$port.")"; 
		}

		if(!empty($company))
		{
			$where .=" and (a.company_id=".$company.")"; 
		}

		if (!empty($search['value'])){
			$where .="and (a.segment ilike '".$search['value']."' or a.segment_code ilike '".$search['value']."')";	
		}

		$sql 		   = "
						select c.name as port_name, b.name as company_name, a.* from app.t_mtr_sailing_company a
						left join app.t_mtr_ship_company b on a.company_id=b.id
						left join app.t_mtr_port c on a.port_id=c.id
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
			$row->number = $i;
			$nonaktif = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
     		$aktif = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

			$id=$this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id}");

     		$row->actions="";

     		if ($row->status==1)
     		{
     			$row->actions.= generate_button_new($this->_module, 'edit', $edit_url);
     			$row->status=success_label("Aktif");
     			$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
     		}
     		else
     		{
     			$row->status=failed_label("Tidak Aktif");	
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="aktifkan"> <i class="fa fa-check"></i> </button> ');
     		}

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
