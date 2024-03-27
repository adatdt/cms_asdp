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

class Port_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'pelabuhan/port';
	}

    public function portList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);

		$searchData=$this->input->post("searchData");
		$searchName=$this->input->post("searchName");
		$iLike        = trim(strtoupper(str_replace(array("'",'"'),"", $searchData) ));
		
		$field = array(
			0 =>'id',
			1 =>'name',
			2 =>'city',
			3 =>'port_code',
			4 =>'profit_center',
			5 =>'weight_limit',
			6 =>'cross_class',
			7 =>'ifcs',
			8 =>'time_zone',
			9 =>'order',
			// 9 =>'port_id_bmkg',
			10 =>'username_siwasops',
			11 =>'url_login_siwasops',
			12 =>'url_siwasops',
			13 =>'status',
		);

		$order_column = $field[$order_column];

		$where = " WHERE status not IN (-5)";
	
		// if (!empty($search['value'])){
		// 	$where .="and (UPPER(name) ilike '%".$iLike."%' or UPPER(city) ilike '%".$iLike."%')";
		// }

		if(!empty($searchData))
		{
			if($searchName=='name')
			{
				$where .=" and ( name ilike '%{$iLike}%')";
			}
			else if ($searchName=='city') {

				$where .=" and ( city ilike '%{$iLike}%')";
			}
			else if($searchName=='portCode')
			{
				$where .=" and ( port_code ilike '%{$iLike}%')";
			}
			else if($searchName=='profitCenter')
			{
				$where .=" and ( profit_center ilike '%{$iLike}%')";
			}
			else if($searchName=='ordering')
			{
				$where .=' and ( "order" ilike '."'%".$iLike."%' )";
			}
			else if($searchName=='portIdBmkg')
			{
				$where .=" and ( port_id_bmkg ilike '%{$iLike}%' )";

			}
			else
			{
				$where .="";
			}
		}

		$sql 		   = 'SELECT  * FROM app.t_mtr_port '.$where.' ';
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
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|-1'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

     		// check apakah datanya port terpairing di dermaga
     		$check_dermaga=$this->port_model->select_data("app.t_mtr_dock", " where port_id={$row->id} and status not in (-5) ");

     		// check apakah datanya port terpairingh dengan route
     		$check_route=$this->port_model->select_data("app.t_mtr_rute", " where status not in (-5) and (origin={$row->id} or destination={$row->id} ) ");

			$row->id 	 = $this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$row->id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$row->id}");

     		$row->actions  = " ";

			if($row->status == 1){
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}else{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}


			// jika datanya tidak terpairing maka tidak muncul
			// if ($check_dermaga->num_rows()<1 and $check_route->num_rows()<1 )
			// {
			// 	$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
			// }

			$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
			$row->cross_class=$row->cross_class=='t'?"Ya":"Tidak";
			$row->ifcs=$row->ifcs=='t'?"Ya":"Tidak";

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