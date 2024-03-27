<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class TransactionStatusModel extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'transaction/transactionStatus';
	}

	public function dataList()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));
		$depart_date = trim($this->input->post('depart_date'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		// $iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));
		$iLike        = trim(strtoupper(str_replace(array("'",'"','`'), "", $searchData)));


		$field = array(
			0 => 'created_on',
			1 => 'transaction_code',
			2 => 'status',
			3 => 'created_on',
			4 => 'created_by',
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE status is not null and created_on >= '". $dateFrom . "' and created_on < '" . $dateToNew . "'";
		// $where = " WHERE status is not null and (created_on::date between '" . $dateFrom . "' and '" . $dateTo . "' ) ";


		if(!empty($searchData))
		{
			if($searchName=="tblName")
			{
				$where .= "and (tbl_name ilike '%" . $iLike . "%')";

			}
			else if($searchName=="transactionCode")
			{
				$where .= "and (transaction_code ilike '%".$iLike."%')";
			}
			// else if($searchName=="statusCode")
			// {
			// 	$where .= "and (status ilike '%" . $iLike . "%' )";
			// }
			else if($searchName=="createdBy")
			{
				$where .= "and (created_by ilike '%" . $iLike . "%' )";
			}												
			else
			{
				$where .= " ";
			}
		}


		$sql = $this->qry($where);
		$sqlCount =$this->qryCount($where);

				// die($sql);

		$queryCount         = $this->dbView->query($sqlCount)->row();
    $records_total 			= $queryCount->countdata;

		// $query         = $this->dbView->query($sql);
		// $records_total = $query->num_rows();
		$sql 		  .= " ORDER BY  " . $order_column . " {$order_dir}";

		if ($length != -1) {
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->no = $i;

			$row->created_on=format_date($row->created_on)." ".date("H:i:s",strtotime ($row->created_on));

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered' => $records_total,
			'data'           => $rows
		);
	}

	public function select_data($table, $where = "")
	{
		return $this->dbView->query("select * from $table $where");
	}

	public function get_identity_app()
	{
		$data = $this->dbView->query("select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}

	public function qry($where)
	{
		$data="SELECT * from app.t_trx_status
				{$where}
				";

		return $data;
	}

	public function qryCount($where)
	{
		$data="SELECT count(id) as countdata from app.t_trx_status
				{$where}
				";

		return $data;
	}
}


