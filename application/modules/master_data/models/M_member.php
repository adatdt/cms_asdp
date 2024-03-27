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

class M_member extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/member';
	}

  public function dataList(){

		$dateFrom =$this->input->post('dateFrom');
		$dateTo =$this->input->post('dateTo');
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$status=$this->enc->decode($this->input->post('status'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchName=$this->input->post('searchName');
		$searchData=trim($this->input->post('searchData'));

		$ilike= str_replace(array('"',"'"), "", $searchData);
		
		$field = array(
			0 =>'id',
			1 =>'nik',
			2 =>'full_name',
			3 =>'date_of_birth',
			4 =>'address',
			5 =>'phone_number',
			6 =>'email',
			7 =>'created_on',
			8 =>'is_activation',
		);

		$order_column = $field[$order_column];

		$where = " WHERE id is not null and ( created_on::date between '{$dateFrom}' and '{$dateTo}' ) ";

		if($status != null)
		{
			$where .=" and is_activation=$status";
		}

		if (!empty($search['value'])){
			$where .="and ( concat(firstname,' ',lastname) ilike '%".$iLike."%' or address ilike '%".$iLike."%'
						   or phone_number ilike '%".$iLike."%' or email ilike '%".$iLike."%'
						   or nik ilike '%".$iLike."%'    
							)";
		}

		if(!empty($searchData))
		{
			if($searchName=='nik')
			{
				$where .=" and nik ilike '%{$ilike}%' ";
			}
			else if($searchName=='nama')
			{
				$where .=" and concat(firstname,' ',lastname) ilike '%{$ilike}%' ";
			}
			else if($searchName=='alamat')
			{
				$where .=" and address ilike '%{$ilike}%' ";
			}
			else if($searchName=='telpon')
			{
				$where .=" and phone_number ilike '%{$ilike}%' ";
			}
			else
			{
				$where .="and email ilike '%{$ilike}%' ";
			}
		}

		$sql = "SELECT concat(firstname,' ',lastname) as full_name, * from app.t_mtr_member
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
			// $nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
			$nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

			$row->id 	 = $this->enc->encode($row->id);
     		$delete_url  = site_url($this->_module."/action_delete/{$row->id}");

     		$row->actions  ="";

			// if($row->is_activation == 1){
			// 	$row->status   = success_label('Aktif');
			// 	$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', '."'".$nonaktif."'".')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			// }
			// else
			// {
			// 	$row->status   = failed_label('Tidak Aktif');
			// 	$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', '."'".$aktif."'".')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
			// }

			if($row->status == 1)
			{
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', '."'".$nonaktif."'".')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else if($row->status == 0)
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', '."'".$aktif."'".')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
			}
			else if($row->status == '-1') 
			{
				$row->status   = failed_label('Temp Banned');
			}
			else if($row->status == '-2')
			{
				$row->status   = failed_label('Permanent Banned');
			}
			else
			{
				$row->status   = failed_label('Delete Account');
			}

     		
     		$row->date_of_birth=format_date($row->date_of_birth);
     		$row->created_on=format_dateTime($row->created_on);
     		$row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);


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


	public function download(){

		$dateFrom =$this->input->get('dateFrom');
		$dateTo =$this->input->get('dateTo');
		$draw = $this->input->post('draw');
		$status=$this->enc->decode($this->input->get('status'));
		$searchName=$this->input->get('searchName');
		$searchData=trim($this->input->get('searchData'));
		$ilike= str_replace(array('"',"'"), "", $searchData);
		
		$field = array(
			0 =>'id',
			1 =>'nik',
			2 =>'full_name',
			3 =>'date_of_birth',
			4 =>'address',
			5 =>'phone_number',
			6 =>'email',
			7 =>'created_on',
			8 =>'is_activation',
		);


		$where = " WHERE id is not null and ( created_on::date between '{$dateFrom}' and '{$dateTo}' )  ";

		if($status != null)
		{
			$where .=" and is_activation=$status";
		}

		if (!empty($search['value'])){
			$where .="and ( concat(firstname,' ',lastname) ilike '%".$iLike."%' or address ilike '%".$iLike."%'
						   or phone_number ilike '%".$iLike."%' or email ilike '%".$iLike."%'
						   or nik ilike '%".$iLike."%'    
							)";
		}

		if(!empty($searchData))
		{
			if($searchName=='nik')
			{
				$where .=" and nik ilike '%{$ilike}%' ";
			}
			else if($searchName=='nama')
			{
				$where .=" and concat(firstname,' ',lastname) ilike '%{$ilike}%' ";
			}
			else if($searchName=='alamat')
			{
				$where .=" and address ilike '%{$ilike}%' ";
			}
			else if($searchName=='telpon')
			{
				$where .=" and phone_number ilike '%{$ilike}%' ";
			}
			else
			{
				$where .="and email ilike '%{$ilike}%' ";
			}
		}

		$sql = "SELECT concat(firstname,' ',lastname) as full_name, * from app.t_mtr_member
						{$where}";
		$sql 		  .= " ORDER BY id desc ";

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		// $i  	= ($start + 1);

		foreach ($rows_data as $row) {
			// $row->number = $i;
			$nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
     		$aktif       = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));
     		$delete_url  = site_url($this->_module."/action_delete/{$row->id}");

     		$row->actions  ="";

			if($row->is_activation == 1){
				$row->status   = 'Aktif';
			}
			else
			{
				$row->status   = 'Tidak Aktif';
			}
     		
     		$row->date_of_birth=format_date($row->date_of_birth);
     		$row->created_on=format_dateTime($row->created_on);
     		// $row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);


			$rows[] = $row;
		}

		return $rows;
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
