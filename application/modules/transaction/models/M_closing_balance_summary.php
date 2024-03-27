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

class M_closing_balance_summary extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/closing_balance_summary';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$service_id = $this->enc->decode($this->input->post('service'));
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$port = $this->enc->decode($this->input->post('port'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$field = array(
			0 =>'close_date',
			1 =>'close_date',
			2 =>'port_name',
			3 =>'ob_code',
			4 =>'total_pendapatan',
			5 =>'total_transaksi',
			6 =>'username',
			7 =>'full_name',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.ob_code is not null ";

		if(!empty($port))
		{
			$where .="and (b.port_id=".$port.")";
		}

		if (!empty($dateTo) and !empty($dateFrom))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		}
		else if(empty($dateFrom) and !empty($dateTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		}
		else if (!empty($dateFrom) and empty($dateTo))
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		}
		else
		{
			$where .="and (to_char(a.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		}


		if (!empty($search['value'])){
			$where .="and (b.ob_code ilike '%".$iLike."%' or d.username ilike '%".$iLike."%' or
							concat(d.first_name,' ',d.last_name)ilike '%".$iLike."%' )";	
		}

		$sql 		   = "
						select sum(a.amount) as total_pendapatan, sum(a.total_transaction) as total_transaksi, a.ob_code, 
						b.port_id,c.name as port_name, b.user_id, d.username, concat(d.first_name,' ',d.last_name) as full_name,
						to_char(a.created_on,'yyyy-mm-dd') as close_date
						from app.t_trx_closing_balance_pos a
						left join (
							select distinct ab.port_id, aa.ob_code, aa.user_id from app.t_trx_opening_balance aa
							left join (select distinct assignment_code, port_id from app.t_trx_assignment_user_pos) ab on aa.assignment_code=ab.assignment_code
						) b on a.ob_code=b.ob_code
						left join app.t_mtr_port c on b.port_id=c.id
						left join core.t_mtr_user d on b.user_id=d.id
						$where
						group by a.ob_code, b.port_id,c.name, b.user_id , d.username , concat(d.first_name,' ',d.last_name),
						to_char(a.created_on,'yyyy-mm-dd')
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

			$id=$this->enc->encode($row->ob_code);
			$detail_url 	= site_url($this->_module."/detail/{$id}");

     		$row->actions="";
     		$row->actions.= generate_button_new($this->_module, 'detail', $detail_url);

     		$row->close_date= format_date($row->close_date);
     		$row->total_pendapatan= idr_currency($row->total_pendapatan);
     		$row->total_transaksi= round($row->total_transaksi);

     		// $row->actions.= generate_button_new($this->_module, 'detail', $detail_url);

     		     	
     		// $row->created_on=format_dateTimeHis($row->created_on);
     		$row->no=$i;

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

	public function select_data($table, $where="")
	{
		return $this->db->query("select * from $table $where");
	}

	public function get_detail($where)
	{
		return $this->db->query("
								select b.terminal_name, a.* from app.t_trx_sell a
								left join app.t_mtr_device_terminal b on a.terminal_code=b.terminal_code
								$where
								");
	}

	public function get_name($where)
	{
		return $this->db->query(
								"
								select sum(a.amount) as total_pendapatan, sum(a.total_transaction) as total_transaksi, a.ob_code, 
								b.port_id,c.name as port_name, b.user_id, d.username, concat(d.first_name,' ',d.last_name) as full_name,
								to_char(a.created_on,'yyyy-mm-dd') as close_date
								from app.t_trx_closing_balance_pos a
								left join (
									select distinct ab.port_id, aa.ob_code, aa.user_id from app.t_trx_opening_balance aa
									left join (select distinct assignment_code, port_id from app.t_trx_assignment_user_pos) ab on aa.assignment_code=ab.assignment_code
								) b on a.ob_code=b.ob_code
								left join app.t_mtr_port c on b.port_id=c.id
								left join core.t_mtr_user d on b.user_id=d.id
								$where
								group by a.ob_code, b.port_id,c.name, b.user_id , d.username , concat(d.first_name,' ',d.last_name),
								to_char(a.created_on,'yyyy-mm-dd')
								"
		);
	}

	public function total_transaction($where="")
	{
		return $this->db->query("select sum(amount) as amount, count(payment_type) as total_transaction ,ob_code, payment_type  from 
			app.t_trx_sell $where
			group by ob_code, payment_type
			order by payment_type
			");
	}

}
