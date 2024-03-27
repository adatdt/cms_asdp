<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : ListMemberDeleteModel
 * -----------------------
 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  2022
 *
 */

class ListMemberDeleteModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/listMemberDelete';
	}

  public function dataList(){

		$dateFrom =$this->input->post('dateFrom');
		$dateTo =$this->input->post('dateTo');
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchName=$this->input->post('searchName');
		$searchData=trim($this->input->post('searchData'));

		$ilike= str_replace(array('"',"'"), "", $searchData);
		
		$field = array(
			0 =>'id',
			1 =>'email',
			2 =>'firstname',
			3 =>'phone_number',
			4 =>'account_created_on',
			5 =>'created_on',
			6 =>'reason_text_selected',
		);

		$order_column = $field[$order_column];
		$where = " WHERE id is not null and ( created_on::date between ".$this->db->escape($dateFrom)." and ".$this->db->escape($dateTo)." ) ";


		if(!empty($searchData))
		{
			if($searchName=='account')
			{
				$where .=" and email ilike '%".$this->db->escape_like_str($ilike)."%' ESCAPE '!' ";
			}
			else if($searchName=='name')
			{
				$where .=" and concat(firstname,' ',lastname) ilike '%".$this->db->escape_like_str($ilike)."%' ESCAPE '!' ";
			}
			else
			{
				$where .=" and phone_number ilike '%".$this->db->escape_like_str($ilike)."%' ESCAPE '!' ";
			}

		}

		$sql = $this->qry($where);
		$sqlCount = $this->qryCount($where);

		$queryCount         = $this->dbView->query($sqlCount)->row();
		$records_total = $queryCount->count_data;
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

     		$row->no=$i;

			$row->account_created_on = empty($row->account_created_on)?"":format_date($row->account_created_on)." ".format_time($row->account_created_on);

			$row->account_delete_date = empty($row->account_delete_date)?"":format_date($row->account_delete_date)." ".format_time($row->account_delete_date);

			$row->fullname = $row->firstname." ".$row->lastname;

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

	public function qry($where)
	{
		$qry="
			select 
				dc.id,
				dc.email,
				dc.firstname ,
				dc.lastname ,
				dc.phone_number ,
				dc.account_created_on,
				dc.created_on as account_delete_date,
				dc.reason_text_selected
			from  app.t_trx_delete_account dc		
			{$where}
		";
		return $qry;
	}

	public function qryCount($where)
	{
		$qry="
			select 
				count(dc.id) as count_data
			from  app.t_trx_delete_account dc		
			{$where}
		";

		return $qry;
	}	
	
	public function download(){

		$dateFrom =$this->input->get('dateFrom', true);
		$dateTo =$this->input->get('dateTo', true);
		$searchName=$this->input->get('searchName', true);
		$searchData=trim($this->input->get('searchData', true));
		$ilike= str_replace(array('"',"'"), "", $searchData);
				
		$where = " WHERE id is not null and ( created_on::date between ".$this->db->escape($dateFrom)." and ".$this->db->escape($dateTo)." )  ";

		if(!empty($searchData))
		{
			if($searchName=='account')
			{
				$where .=" and email ilike '%".$this->db->escape_like_str($ilike)."%' ESCAPE '!' ";
			}
			else if($searchName=='name')
			{
				$where .=" and concat(firstname,' ',lastname) ilike '%".$this->db->escape_like_str($ilike)."%' ESCAPE '!' ";
			}
			else
			{
				$where .=" and phone_number ilike '%".$this->db->escape_like_str($ilike)."%' ESCAPE '!' ";
			}
		}

		$sql = $this->qry($where);
		$sqlCount = $this->qryCount($where);

		$queryCount         = $this->dbView->query($sqlCount)->row();
		$sql 		  .= " ORDER BY id asc";
		$query     = $this->db->query($sql);
		$rows_data = $query->result();		

		foreach ($rows_data as $row) {

			$row->account_created_on = empty($row->account_created_on)?"":format_date($row->account_created_on)." ".format_time($row->account_created_on);

			$row->account_delete_date = empty($row->account_delete_date)?"":format_date($row->account_delete_date)." ".format_time($row->account_delete_date);

			$row->fullname = $row->firstname." ".$row->lastname;

			$rows[] = $row;
			unset($row->id);
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
