<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------------
 * CLASS NAME : Settlement_model
 * -----------------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Settlement_model extends CI_Model {

	function __construct() {
        parent::__construct();
        $this->_dbstm = $this->load->database('stm', TRUE);
    }
	
	public function getList(){
		$start_date   = $this->input->post('start_date');
		$end_date     = $this->input->post('end_date');
		$status       = $this->input->post('status');
		$date_type    = $this->input->post('date_type');
		$bank    	  = $this->input->post('bank');
		$shift    	  = $this->input->post('shift');
		$port    	  = $this->session->userdata('port_id') != '' ? $this->session->userdata('port_id') : $this->enc->decode($this->input->post('port'));

		$start        = $this->input->post('start');
		$length       = $this->input->post('length');
		$draw         = $this->input->post('draw');
		$order        = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir    = strtoupper($order[0]['dir']);

		$field = array(
			1 => 'transaction_date',
			2 => 'settlement_date',
			3 => 'shift_date',
		);

		$order_column = $field[$order_column];
		
		if ($date_type == 1){
			$where = " WHERE date(transaction_date) BETWEEN '{$start_date}' AND '{$end_date}'";
		}else if ($date_type == 2){
			$where = " WHERE date(settlement_date) BETWEEN '{$start_date}' AND '{$end_date}'";
		}else{
			$where = " WHERE date(shift_date) BETWEEN '{$start_date}' AND '{$end_date}'";
		}

		// validation if is user bank
		if($this->checkUserBank()->num_rows() > 0)
		{
			$bank_user=$this->checkUserBank()->row();
			$where .=" AND trx.bank_id = '{$bank_user->id}' ";
		} else if ($bank != '')
		{
			$where .= " AND trx.bank_id = '{$bank}' ";
		}

		if ($port != ''){
			$where .= " AND trx.port_id = {$port} ";
		}

		if ($status != ''){
			$where .= " AND trx.status = {$status} ";
		}

		if ($shift != ''){
			$where .= " AND trx.shift_id = {$shift} ";
		}
		
		$field_name =
				'settlement_id,
				 transaction_code,
				 amount,
				 terminal_id,
				 transaction_date,
				 merchant_id,
				 location_id,
				 bank_id,
				 status,
				 filename,
				 return_transaction_code,
				 status_code,
				 created_by,
				 created_on,
				 updated_by,
				 updated_on,
				 created_on_main,
				 return_file_name,
				 settlement_date,
				 branch_code,
				 port_id,
				 shift_date,
				 shift_id,
				 channel';
		$sql = "SELECT 
				*
			FROM (
				-- SELECT * FROM trx.t_trx_settlement_bca
				-- UNION ALL
				SELECT 
					$field_name
				 FROM trx.t_trx_settlement_bni
				UNION ALL
				SELECT $field_name FROM trx.t_trx_settlement_bri
				UNION ALL
				SELECT $field_name FROM trx.t_trx_settlement_mandiri) AS trx
			LEFT JOIN master.t_mtr_bank b ON b.bank_id = trx.bank_id AND b.status = 1
			LEFT JOIN master.t_mtr_status_settlement ss ON ss.status_code = trx.status AND ss.status = 1
			-- LEFT JOIN opr.t_mtr_shelter s ON s.shelter_code_var = trx.shelter_code AND s.status_int = 1
			-- LEFT JOIN opr.t_mtr_corridor c ON c.corridor_code_var = s.corridor_code_var AND c.status_int = 1
			-- LEFT JOIN sot.t_mtr_card_type ct ON ct.id_int = trx.card_type AND ct.status_int = 1 AND product_id_int IN (0,1)
				{$where}";

		$sql2 = "SELECT SUM(amount)
			FROM (
				-- SELECT * FROM trx.t_trx_settlement_bca
				-- UNION ALL
				SELECT $field_name FROM trx.t_trx_settlement_bni
				UNION ALL
				SELECT $field_name FROM trx.t_trx_settlement_bri
				UNION ALL
				SELECT $field_name FROM trx.t_trx_settlement_mandiri) AS trx
			LEFT JOIN master.t_mtr_bank b ON b.bank_id = trx.bank_id AND b.status = 1
			LEFT JOIN master.t_mtr_status_settlement ss ON ss.status_code = trx.status AND ss.status = 1
			-- LEFT JOIN opr.t_mtr_shelter s ON s.shelter_code_var = trx.shelter_code AND s.status_int = 1
			-- LEFT JOIN opr.t_mtr_corridor c ON c.corridor_code_var = s.corridor_code_var AND c.status_int = 1
			-- LEFT JOIN sot.t_mtr_card_type ct ON ct.id_int = trx.card_type AND ct.status_int = 1 AND product_id_int IN (0,1)
				{$where}";

		$query         = $this->_dbstm->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";			
		}

		$query     = $this->_dbstm->query($sql);
		$rows_data = $query->result();
		$rows 	   = array();
		$i 		   = ($start + 1);

		foreach ($rows_data as $row) {
			$row->no= $i;
			$row->amount = number_format($row->amount,0,',','.');

			$rows[] = $row;
			$i++;
		}

		$total_amount = $this->_dbstm->query($sql2)->row()->sum;

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows,
			'total' 		 => $total_amount ? number_format($total_amount,0,',','.') : 0
		);
	}

	public function summarySettlemet($status,$post){
		$field_name ='settlement_id,
				 transaction_code,
				 amount,
				 terminal_id,
				 transaction_date,
				 merchant_id,
				 location_id,
				 bank_id,
				 status,
				 filename,
				 return_transaction_code,
				 status_code,
				 created_by,
				 created_on,
				 updated_by,
				 updated_on,
				 created_on_main,
				 return_file_name,
				 settlement_date,
				 branch_code,
				 port_id,
				 shift_date,
				 shift_id,
				 channel';
		$where = " WHERE trx.status = {$status}";

		if ($post['date_type'] == 1){
			$where .= " AND date(transaction_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
		}else if ($post['date_type'] ==2){
			$where .= " AND date(settlement_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
		}else{
			$where .= " AND date(shift_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
		}

		// validation if is user bank
		if($this->checkUserBank()->num_rows() > 0)
		{
			$bank_user=$this->checkUserBank()->row();
			$where .=" AND trx.bank_id = '{$bank_user->id}' ";
		} else if ($post['bank'] != '')
		{
			$where .= " AND trx.bank_id = '{$post['bank']}' ";
		}
		$port = $this->session->userdata('port_id') != '' ? $this->session->userdata('port_id') : $this->enc->decode($post['port']);
		if ($port != ''){
			$where .= " AND trx.port_id = {$port} ";
		}
		if ($post['shift'] != ''){
			$where .= " AND trx.shift_id = {$this->enc->decode($post['shift'])} ";
		}

		$sql = "SELECT 
					COUNT(amount), SUM(amount)
				FROM (
					-- SELECT * FROM trx.t_trx_settlement_bca
					-- UNION ALL
					SELECT $field_name FROM trx.t_trx_settlement_bni
					UNION ALL
					SELECT $field_name FROM trx.t_trx_settlement_bri
					UNION ALL
					SELECT $field_name FROM trx.t_trx_settlement_mandiri) AS trx
				LEFT JOIN master.t_mtr_bank b ON b.bank_id = trx.bank_id AND b.status = 1
				LEFT JOIN master.t_mtr_status_settlement ss ON ss.status_code = trx.status AND ss.status = 1
				{$where}";
				// echo $sql;exit();
		return $this->_dbstm->query($sql)->row();
	}

	public function listSummarySettlement(){
		$sql = "SELECT status_code, status_name FROM master.t_mtr_status_settlement WHERE status = 1 ORDER BY status_code ASC";
		$data = array();
		$total_volume = 0;
		$total_revenue = 0;

		foreach ($this->_dbstm->query($sql)->result() as $key => $row) {
			$sett = $this->summarySettlemet($row->status_code,$this->input->post());
			$sum  = $sett->sum ? $sett->sum : 0;

			$row->count = $sett->count;
			$row->sum   = $sum;
			$total_volume += $sett->count;
			$total_revenue += $sum;

			$data[] = $row;
		}

		return array(
			'data' => $data,
			'total' => array(
				'total_volume' => $total_volume,
				'total_revenue' => $total_revenue
			)
		);
	}
	
	public function listDetailSettlement(){
		$post = $this->input->post();

		if ($post['date_type'] == 1){
			$where = "WHERE date(transaction_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'transaction_date';
		}else if ($post['date_type'] == 2){
			$where = "WHERE date(settlement_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'settlement_date';
		}else{
			$where = "WHERE date(shift_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'shift_date';
		}

		// validation if is user bank
		if($this->checkUserBank()->num_rows() > 0)
		{
			$bank_user=$this->checkUserBank()->row();
			$where .=" AND trx.bank_id = '{$bank_user->id}' ";
		} else if ($post['bank'] != '')
		{
			$where .= " AND trx.bank_id = '{$post['bank']}' ";
		}
		$port = $this->session->userdata('port_id') != '' ? $this->session->userdata('port_id') : $this->enc->decode($post['port']);
		if ($port != ''){
			$where .= " AND trx.port_id = {$port} ";
		}
		if ($post['shift'] != ''){
			$where .= " AND trx.shift_id = {$this->enc->decode($post['shift'])} ";
		}

		$sql = "SELECT  
			dates,
			json_object_agg ( status, total ORDER BY  status) AS status
		FROM (
		SELECT 
			trx.status,
			{$select_date}::DATE AS dates, 
			COUNT ( amount ) :: VARCHAR || '-' || SUM ( amount ) :: VARCHAR  AS total
			FROM (
			-- SELECT {$select_date}, amount, status, bank_id FROM trx.t_trx_settlement_bca
			-- UNION ALL
			SELECT {$select_date}, amount, status, bank_id, port_id, shift_id FROM trx.t_trx_settlement_bni
			UNION ALL
			SELECT {$select_date}, amount, status, bank_id, port_id, shift_id FROM trx.t_trx_settlement_bri
			UNION ALL
			SELECT {$select_date}, amount, status, bank_id, port_id, shift_id FROM trx.t_trx_settlement_mandiri) AS trx
		LEFT JOIN master.t_mtr_bank b ON b.bank_id = trx.bank_id AND b.status = 1
		JOIN master.t_mtr_status_settlement ss ON ss.status_code = trx.status AND ss.status = 1
		-- LEFT JOIN opr.t_mtr_shelter s ON s.shelter_code_var = trx.shelter_code AND s.status_int = 1
		-- LEFT JOIN opr.t_mtr_corridor c ON c.corridor_code_var = s.corridor_code_var AND c.status_int = 1
		-- LEFT JOIN sot.t_mtr_card_type ct ON ct.id_int = trx.card_type AND ct.status_int = 1 AND product_id_int IN (0,1)
		{$where}
		GROUP BY dates, trx.status) as sett
			GROUP BY dates
			ORDER BY dates DESC";

		// die($sql);

		$arr = array();
		$arrB = array();
		$sumArrayTrx = array();
		$sumArrayNom = array();

		$total_trx = 0;
		$total_nom = 0;

		$data = $this->_dbstm->query($sql)->result();
		$status = $this->list_status();

		foreach ($status as $b) {
			$arrB[] = $b->status_code;
		}

		foreach ($data as $k => $row) {
			$row->total_trx = 0;
			$row->total_nom = 0;

			$dataTrx = json_decode($row->status);

			$arrB2 = array();
			foreach ($dataTrx as $key => $val) {
				$arrB2[] = $key;
			}

			$diff = array_diff($arrB, $arrB2);

			if($diff){
				foreach ($diff as $dKey => $d) {
					$dataTrx->$d = '0-0';
				}
			}

			$trxStatus = array();
			foreach ($dataTrx as $t => $r) {
				$exp = explode('-', $r);
				$trxStatus[$t] = array(
					'trx' => $exp[0],
					'nominal' => $exp[1]
				);
				$row->total_trx += (int) $exp[0];
				$row->total_nom += (int) $exp[1];
			}

			foreach ($status as $st) {
				$sumArrayTrx[$st->status_code][] = $trxStatus[$st->status_code]['trx'];
				$sumArrayNom[$st->status_code][] = $trxStatus[$st->status_code]['nominal'];
			}

			$row->status = $trxStatus;
			$row->dates = date('d M Y', strtotime($row->dates));

			$total_trx += $row->total_trx;
			$total_nom += $row->total_nom;

			$arr[] = $row;
		}

		// print_r($sumArrayTrx);exit;

		if($sumArrayTrx){
			$sumTrx = array();
			foreach ($status as $x => $c) {
				$sumTrx[$c->status_code] = array_sum($sumArrayTrx[$c->status_code]);
			}
			$sumTrx['total'] = $total_trx;
		}else{
			$sumTrx = array();
		}

		if($sumArrayNom){
			$sumNom = array();
			foreach ($status as $x => $c) {
				$sumNom[$c->status_code] = array_sum($sumArrayNom[$c->status_code]);
			}
			$sumNom['total'] = $total_nom;
		}else{
			$sumNom = array();
		}

		return array(
			'data' => $arr,
			'total' => array(
				'trx' => $sumTrx,
				'nominal' => $sumNom,
			)
		);
	}
	
	public function listDetailSettlementPerbulan(){
		$post = $this->input->post();

		if ($post['date_type'] == 1){
			$where = "WHERE date(transaction_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'transaction_date';
		}else if ($post['date_type'] == 2){
			$where = "WHERE date(settlement_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'settlement_date';
		}else{
			$where = "WHERE date(shift_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'shift_date';
		}

		// validation if is user bank
		if($this->checkUserBank()->num_rows() > 0)
		{
			$bank_user=$this->checkUserBank()->row();
			$where .=" AND trx.bank_id = '{$bank_user->id}' ";
		} else if ($post['bank'] != '')
		{
			$where .= " AND trx.bank_id = '{$post['bank']}' ";
		}
		$port = $this->session->userdata('port_id') != '' ? $this->session->userdata('port_id') : $this->enc->decode($post['port']);
		if ($port != ''){
			$where .= " AND trx.port_id = {$port} ";
		}
		if ($post['shift'] != ''){
			$where .= " AND trx.shift_id = {$this->enc->decode($post['shift'])} ";
		}

		$sql = "SELECT  
			dates,
			json_object_agg ( status, total ORDER BY  status) AS status
		FROM (
		SELECT 
			trx.status,
			date_trunc('month', $select_date::DATE) AS dates, 
			COUNT ( amount ) :: VARCHAR || '-' || SUM ( amount ) :: VARCHAR  AS total
			FROM (
			SELECT {$select_date}, amount, status, bank_id, port_id, shift_id FROM trx.t_trx_settlement_bni
			UNION ALL
			SELECT {$select_date}, amount, status, bank_id, port_id, shift_id FROM trx.t_trx_settlement_bri
			UNION ALL
			SELECT {$select_date}, amount, status, bank_id, port_id, shift_id FROM trx.t_trx_settlement_mandiri) AS trx
		LEFT JOIN master.t_mtr_bank b ON b.bank_id = trx.bank_id AND b.status = 1
		JOIN master.t_mtr_status_settlement ss ON ss.status_code = trx.status AND ss.status = 1
		{$where}
		GROUP BY dates, trx.status) as sett
			GROUP BY dates
			ORDER BY dates DESC";

			// die($sql);

		$arr = array();
		$arrB = array();
		$sumArrayTrx = array();
		$sumArrayNom = array();

		$total_trx = 0;
		$total_nom = 0;

		$data = $this->_dbstm->query($sql)->result();
		$status = $this->list_status();

		foreach ($status as $b) {
			$arrB[] = $b->status_code;
		}

		foreach ($data as $k => $row) {
			$row->total_trx = 0;
			$row->total_nom = 0;

			$dataTrx = json_decode($row->status);

			$arrB2 = array();
			foreach ($dataTrx as $key => $val) {
				$arrB2[] = $key;
			}

			$diff = array_diff($arrB, $arrB2);

			if($diff){
				foreach ($diff as $dKey => $d) {
					$dataTrx->$d = '0-0';
				}
			}

			$trxStatus = array();
			foreach ($dataTrx as $t => $r) {
				$exp = explode('-', $r);
				$trxStatus[$t] = array(
					'trx' => $exp[0],
					'nominal' => $exp[1]
				);
				$row->total_trx += (int) $exp[0];
				$row->total_nom += (int) $exp[1];
			}

			foreach ($status as $st) {
				$sumArrayTrx[$st->status_code][] = $trxStatus[$st->status_code]['trx'];
				$sumArrayNom[$st->status_code][] = $trxStatus[$st->status_code]['nominal'];
			}

			$row->status = $trxStatus;
			$row->dates = date('M Y', strtotime($row->dates));

			$total_trx += $row->total_trx;
			$total_nom += $row->total_nom;

			$arr[] = $row;
		}

		// print_r($sumArrayTrx);exit;

		if($sumArrayTrx){
			$sumTrx = array();
			foreach ($status as $x => $c) {
				$sumTrx[$c->status_code] = array_sum($sumArrayTrx[$c->status_code]);
			}
			$sumTrx['total'] = $total_trx;
		}else{
			$sumTrx = array();
		}

		if($sumArrayNom){
			$sumNom = array();
			foreach ($status as $x => $c) {
				$sumNom[$c->status_code] = array_sum($sumArrayNom[$c->status_code]);
			}
			$sumNom['total'] = $total_nom;
		}else{
			$sumNom = array();
		}

		return array(
			'data' => $arr,
			'total' => array(
				'trx' => $sumTrx,
				'nominal' => $sumNom,
			)
		);
	}

	public function listDetailBankSettlement(){
		$post = $this->input->post();

		if ($post['date_type'] == 1){
			$where = "WHERE date(transaction_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'transaction_date';
		}else if ($post['date_type'] == 2){
			$where = "WHERE date(settlement_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'settlement_date';
		}else{
			$where = "WHERE date(shift_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'shift_date';
		}

		// validation if is user bank
		if($this->checkUserBank()->num_rows() > 0)
		{
			$bank_user=$this->checkUserBank()->row();
			$where .=" AND trx.bank_id = '{$bank_user->id}' ";
		} else if ($post['bank'] != '')
		{
			$where .= " AND trx.bank_id = '{$post['bank']}' ";
		}
		$port = $this->session->userdata('port_id') != '' ? $this->session->userdata('port_id') : $this->enc->decode($post['port']);
		if ($port != ''){
			$where .= " AND trx.port_id = {$port} ";
		}
		if ($post['shift'] != ''){
			$where .= " AND trx.shift_id = {$this->enc->decode($post['shift'])} ";
		}

		$sql = "SELECT 
		  dates, 
		  bank_name,
		--   port_id, 
		--   id_int, 
		  json_object_agg (
		    status, 
		    total 
		    ORDER BY 
		      status
		  ) AS status 
		FROM 
		  (
		    SELECT 
		      trx.status, 
		      bank_name,
		--       ct.id_int, 
		      {$select_date} :: DATE AS dates, 
		      COUNT (amount) :: VARCHAR || '-' || SUM (amount) :: VARCHAR AS total 
		    FROM 
		      (
		--         SELECT 
		--           {$select_date}, 
		--           amount, 
		--           status, 
		-- --           card_type, 
		-- --           shelter_code, 
		--           bank_id 
		--         FROM 
		--           trx.t_trx_settlement_bca 
		--         UNION ALL 
		        SELECT 
		          {$select_date}, 
		          amount, 
		          status, 
		--           card_type, 
		--           shelter_code, 
		          bank_id,
				  port_id,
				  shift_id 
		        FROM 
		          trx.t_trx_settlement_bni 
		        UNION ALL 
		        SELECT 
		          {$select_date}, 
		          amount, 
		          status, 
		--           card_type, 
		--           shelter_code, 
		          bank_id,
				  port_id,
				  shift_id 
		        FROM 
		          trx.t_trx_settlement_bri 
		        UNION ALL 
		        SELECT 
		          {$select_date}, 
		          amount, 
		          status, 
		--           card_type, 
		--           shelter_code, 
		          bank_id,
				  port_id,
				  shift_id 
		        FROM 
		          trx.t_trx_settlement_mandiri
		      ) AS trx 
		      LEFT JOIN master.t_mtr_bank b ON b.bank_id = trx.bank_id 
		      AND b.status = 1 
		      JOIN master.t_mtr_status_settlement ss ON ss.status_code = trx.status 
		      AND ss.status = 1 
		      -- LEFT JOIN opr.t_mtr_shelter s ON s.shelter_code_var = trx.shelter_code AND s.status_int = 1 
		      -- LEFT JOIN opr.t_mtr_corridor c ON c.corridor_code_var = s.corridor_code_var AND c.status_int = 1 
		      -- LEFT JOIN sot.t_mtr_card_type ct ON ct.id_int = trx.card_type AND ct.status_int = 1 AND product_id_int IN (0,1) 
		 
					{$where}
					GROUP BY 
		      dates, 
		      trx.status, 
		      bank_name
		--       ct.card_type_var, 
		--       ct.id_int 
		--     ORDER BY 
		--       ct.id_int ASC
		  ) as sett 
		GROUP BY 
		  dates,
		  bank_name
		--   id_int, 
		--   card_type_var 
		ORDER BY 
		  dates DESC";

		// die($sql);

		$arr = array();
		$arrB = array();
		$sumArrayTrx = array();
		$sumArrayNom = array();

		$total_trx = 0;
		$total_nom = 0;

		$data = $this->_dbstm->query($sql)->result();
		$status = $this->list_status();

		foreach ($status as $b) {
			$arrB[] = $b->status_code;
		}

		foreach ($data as $k => $row) {
			$row->total_trx = 0;
			$row->total_nom = 0;

			$dataTrx = json_decode($row->status);

			$arrB2 = array();
			foreach ($dataTrx as $key => $val) {
				$arrB2[] = $key;
			}

			$diff = array_diff($arrB, $arrB2);

			if($diff){
				foreach ($diff as $dKey => $d) {
					$dataTrx->$d = '0-0';
				}
			}

			$trxStatus = array();
			foreach ($dataTrx as $t => $r) {
				$exp = explode('-', $r);
				$trxStatus[$t] = array(
					'trx' => $exp[0],
					'nominal' => $exp[1]
				);
				$row->total_trx += (int) $exp[0];
				$row->total_nom += (int) $exp[1];
			}

			foreach ($status as $st) {
				$sumArrayTrx[$st->status_code][] = $trxStatus[$st->status_code]['trx'];
				$sumArrayNom[$st->status_code][] = $trxStatus[$st->status_code]['nominal'];
			}

			$row->status = $trxStatus;
			$row->dates = date('d M Y', strtotime($row->dates));

			$total_trx += $row->total_trx;
			$total_nom += $row->total_nom;

			$arr[] = $row;
		}

		if($sumArrayTrx){
			$sumTrx = array();
			foreach ($status as $x => $c) {
				$sumTrx[$c->status_code] = array_sum($sumArrayTrx[$c->status_code]);
			}
			$sumTrx['total'] = $total_trx;
		}else{
			$sumTrx = array();
		}

		if($sumArrayNom){
			$sumNom = array();
			foreach ($status as $x => $c) {
				$sumNom[$c->status_code] = array_sum($sumArrayNom[$c->status_code]);
			}
			$sumNom['total'] = $total_nom;
		}else{
			$sumNom = array();
		}

		return array(
			'data' => $arr,
			'total' => array(
				'trx' => $sumTrx,
				'nominal' => $sumNom,
			)
		);
	}

	public function listDetailBankSettlementPerbulan(){
		$post = $this->input->post();

		if ($post['date_type'] == 1){
			$where = "WHERE date(transaction_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'transaction_date';
		}else if ($post['date_type'] == 2){
			$where = "WHERE date(settlement_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'settlement_date';
		}else{
			$where = "WHERE date(shift_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'shift_date';
		}

		// validation if is user bank
		if($this->checkUserBank()->num_rows() > 0)
		{
			$bank_user=$this->checkUserBank()->row();
			$where .=" AND trx.bank_id = '{$bank_user->id}' ";
		} else if ($post['bank'] != '')
		{
			$where .= " AND trx.bank_id = '{$post['bank']}' ";
		}
		$port = $this->session->userdata('port_id') != '' ? $this->session->userdata('port_id') : $this->enc->decode($post['port']);
		if ($port != ''){
			$where .= " AND trx.port_id = {$port} ";
		}
		if ($post['shift'] != ''){
			$where .= " AND trx.shift_id = {$this->enc->decode($post['shift'])} ";
		}

		$sql = "SELECT 
		  dates, 
		  bank_name,
		  json_object_agg (
		    status, 
		    total 
		    ORDER BY 
		      status
		  ) AS status 
		FROM 
		  (
		    SELECT 
		      trx.status, 
		      bank_name,
		      date_trunc('month', {$select_date}::DATE) AS dates, 
		      COUNT (amount) :: VARCHAR || '-' || SUM (amount) :: VARCHAR AS total 
		    FROM 
		      (
		        SELECT 
		          {$select_date}, 
		          amount, 
		          status,
		          bank_id,
				  port_id,
				  shift_id 
		        FROM 
		          trx.t_trx_settlement_bni 
		        UNION ALL 
		        SELECT 
		          {$select_date}, 
		          amount, 
		          status,
		          bank_id,
				  port_id,
				  shift_id 
		        FROM 
		          trx.t_trx_settlement_bri 
		        UNION ALL 
		        SELECT 
		          {$select_date}, 
		          amount, 
		          status,
		          bank_id,
				  port_id,
				  shift_id 
		        FROM 
		          trx.t_trx_settlement_mandiri
		      ) AS trx 
		      LEFT JOIN master.t_mtr_bank b ON b.bank_id = trx.bank_id 
		      AND b.status = 1 
		      JOIN master.t_mtr_status_settlement ss ON ss.status_code = trx.status 
		      AND ss.status = 1		 
					{$where}
					GROUP BY 
		      dates, 
		      trx.status, 
		      bank_name
		  ) as sett 
		GROUP BY 
		  dates,
		  bank_name
		ORDER BY 
		  dates DESC";

		// die($sql);

		$arr = array();
		$arrB = array();
		$sumArrayTrx = array();
		$sumArrayNom = array();

		$total_trx = 0;
		$total_nom = 0;

		$data = $this->_dbstm->query($sql)->result();
		$status = $this->list_status();

		foreach ($status as $b) {
			$arrB[] = $b->status_code;
		}

		foreach ($data as $k => $row) {
			$row->total_trx = 0;
			$row->total_nom = 0;

			$dataTrx = json_decode($row->status);

			$arrB2 = array();
			foreach ($dataTrx as $key => $val) {
				$arrB2[] = $key;
			}

			$diff = array_diff($arrB, $arrB2);

			if($diff){
				foreach ($diff as $dKey => $d) {
					$dataTrx->$d = '0-0';
				}
			}

			$trxStatus = array();
			foreach ($dataTrx as $t => $r) {
				$exp = explode('-', $r);
				$trxStatus[$t] = array(
					'trx' => $exp[0],
					'nominal' => $exp[1]
				);
				$row->total_trx += (int) $exp[0];
				$row->total_nom += (int) $exp[1];
			}

			foreach ($status as $st) {
				$sumArrayTrx[$st->status_code][] = $trxStatus[$st->status_code]['trx'];
				$sumArrayNom[$st->status_code][] = $trxStatus[$st->status_code]['nominal'];
			}

			$row->status = $trxStatus;
			$row->dates = date('M Y', strtotime($row->dates));

			$total_trx += $row->total_trx;
			$total_nom += $row->total_nom;

			$arr[] = $row;
		}

		if($sumArrayTrx){
			$sumTrx = array();
			foreach ($status as $x => $c) {
				$sumTrx[$c->status_code] = array_sum($sumArrayTrx[$c->status_code]);
			}
			$sumTrx['total'] = $total_trx;
		}else{
			$sumTrx = array();
		}

		if($sumArrayNom){
			$sumNom = array();
			foreach ($status as $x => $c) {
				$sumNom[$c->status_code] = array_sum($sumArrayNom[$c->status_code]);
			}
			$sumNom['total'] = $total_nom;
		}else{
			$sumNom = array();
		}

		return array(
			'data' => $arr,
			'total' => array(
				'trx' => $sumTrx,
				'nominal' => $sumNom,
			)
		);
	}

	public function listRekapFS(){
		$post = $this->input->post();

		if ($post['date_type'] == 1){
			$where = "WHERE date(transaction_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'transaction_date';
		}else if ($post['date_type'] == 2){
			$where = "WHERE date(settlement_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'settlement_date';
		}else{
			$where = "WHERE date(shift_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'shift_date';
		}

		// validation if is user bank
		if($this->checkUserBank()->num_rows() > 0)
		{
			$bank_user=$this->checkUserBank()->row();
			$where .=" AND trx.bank_id = '{$bank_user->id}' ";
		} else if ($post['bank'] != '')
		{
			$where .= " AND trx.bank_id = '{$post['bank']}' ";
		}

		$port = $this->session->userdata('port_id') != '' ? $this->session->userdata('port_id') : $this->enc->decode($post['port']);
		if ($port != ''){
			$where .= " AND trx.port_id = {$port} ";
		}
		if ($post['shift'] != ''){
			$where .= " AND trx.shift_id = {$this->enc->decode($post['shift'])} ";
		}
		if ($post['search'] != ''){
			$where .= " AND trx.filename ilike '%{$post['search']}%' ";
		}

		$sql = "";

		if ($post['bank'] != '')
		{
			$sql = "SELECT
					dates,
					filename,
					bank_name,
					json_object_agg ( status, total ORDER BY  status) AS status
				FROM (SELECT 
							trx.status,
							{$select_date} :: DATE AS dates,
							filename,
							bank_name,
							COUNT ( amount ) :: VARCHAR || '-' || SUM ( amount ) :: VARCHAR  AS total
						FROM (SELECT {$select_date}, filename, amount, status, bank_id, port_id, shift_id FROM trx.t_trx_settlement_bni WHERE status NOT IN (-1, -2)

								UNION ALL

								SELECT {$select_date}, filename, amount, status, bank_id, port_id, shift_id FROM trx.t_trx_settlement_bri WHERE status NOT IN (-1, -2)
						
								UNION ALL

								SELECT {$select_date}, filename, amount, status, bank_id, port_id, shift_id FROM trx.t_trx_settlement_mandiri WHERE status NOT IN (-1, -2)
							) AS trx
							LEFT JOIN master.t_mtr_bank b ON b.bank_id = trx.bank_id AND b.status = 1
							JOIN master.t_mtr_status_settlement ss ON ss.status_code = trx.status AND ss.status = 1 AND ss.status_code NOT IN (-1, -2)
						{$where}
						AND filename IS NOT NULL
					GROUP BY
						dates,
						filename,
						bank_name,
						trx.status) as sett
				GROUP BY
					dates,
					filename,
					bank_name
				ORDER BY
					dates DESC";
		}

		// die($sql);

		$arr = array();
		$arrB = array();
		$sumArrayTrx = array();
		$sumArrayNom = array();

		$total_trx = 0;
		$total_nom = 0;

		$data = "";
		if ($post['bank'] != '') {
			$data = $this->_dbstm->query($sql)->result();
			$status = $this->list_status_rf();

			foreach ($status as $b) {
				$arrB[] = $b->status_code;
			}
		}

		foreach ($data as $k => $row) {
			$row->total_trx = 0;
			$row->total_nom = 0;

			$dataTrx = json_decode($row->status);

			$arrB2 = array();
			foreach ($dataTrx as $key => $val) {
				$arrB2[] = $key;
			}

			$diff = array_diff($arrB, $arrB2);

			if($diff){
				foreach ($diff as $dKey => $d) {
					$dataTrx->$d = '0-0';
				}
			}

			$trxStatus = array();
			foreach ($dataTrx as $t => $r) {
				$exp = explode('-', $r);
				$trxStatus[$t] = array(
					'trx' => $exp[0],
					'nominal' => $exp[1]
				);
				$row->total_trx += (int) $exp[0];
				$row->total_nom += (int) $exp[1];
			}

			foreach ($status as $st) {
				$sumArrayTrx[$st->status_code][] = $trxStatus[$st->status_code]['trx'];
				$sumArrayNom[$st->status_code][] = $trxStatus[$st->status_code]['nominal'];
			}

			$row->status = $trxStatus;
			$row->dates = date('d M Y', strtotime($row->dates));

			$total_trx += $row->total_trx;
			$total_nom += $row->total_nom;

			$arr[] = $row;
		}

		// print_r($sumArrayTrx);exit;

		if($sumArrayTrx){
			$sumTrx = array();
			foreach ($status as $x => $c) {
				$sumTrx[$c->status_code] = array_sum($sumArrayTrx[$c->status_code]);
			}
			$sumTrx['total'] = $total_trx;
		}else{
			$sumTrx = array();
		}

		if($sumArrayNom){
			$sumNom = array();
			foreach ($status as $x => $c) {
				$sumNom[$c->status_code] = array_sum($sumArrayNom[$c->status_code]);
			}
			$sumNom['total'] = $total_nom;
		}else{
			$sumNom = array();
		}

		return array(
			'data' => $arr,
			'total' => array(
				'trx' => $sumTrx,
				'nominal' => $sumNom,
			)
		);
	}

	public function listDownloadDetails(){
		$post = $this->input->post();
		if ($post['date_type'] == 1){
			$where = "WHERE date(transaction_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'transaction_date';
		}else if ($post['date_type'] == 2){
			$where = "WHERE date(settlement_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'settlement_date';
		}else{
			$where = "WHERE date(shift_date) BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'";
			$select_date = 'shift_date';
		}
		
		// validation if is user bank
		if($this->checkUserBank()->num_rows() > 0)
		{
			$bank_user=$this->checkUserBank()->row();
			$where .=" AND trx.bank_id = '{$bank_user->id}' ";
		} else if ($post['bank'] != '')
		{
			$where .= " AND trx.bank_id = '{$post['bank']}' ";
		}

		if ($post['status'] != ''){
			$where .= " AND trx.status = {$post['status']} ";
		}

		$port = $this->session->userdata('port_id') != '' ? $this->session->userdata('port_id') : $this->enc->decode($post['port']);
		if ($port != ''){
			$where .= " AND trx.port_id = {$port} ";
		}
		if ($post['shift'] != ''){
			$where .= " AND trx.shift_id = {$this->enc->decode($post['shift'])} ";
		}

		$field_name =
				'settlement_id,
				 transaction_code,
				 amount,
				 terminal_id,
				 transaction_date,
				 merchant_id,
				 location_id,
				 bank_id,
				 status,
				 filename,
				 return_transaction_code,
				 status_code,
				 created_by,
				 created_on,
				 updated_by,
				 updated_on,
				 created_on_main,
				 return_file_name,
				 settlement_date,
				 branch_code,
				 port_id,
				 shift_date,
				 shift_id,
				 channel';
		$sql = "SELECT 
				*
			FROM (
				-- SELECT * FROM trx.t_trx_settlement_bca
				-- UNION ALL
				SELECT 
					$field_name
				 FROM trx.t_trx_settlement_bni
				UNION ALL
				SELECT $field_name FROM trx.t_trx_settlement_bri
				UNION ALL
				SELECT $field_name FROM trx.t_trx_settlement_mandiri) AS trx
			LEFT JOIN master.t_mtr_bank b ON b.bank_id = trx.bank_id AND b.status = 1
			LEFT JOIN master.t_mtr_status_settlement ss ON ss.status_code = trx.status AND ss.status = 1
			-- LEFT JOIN opr.t_mtr_shelter s ON s.shelter_code_var = trx.shelter_code AND s.status_int = 1
			-- LEFT JOIN opr.t_mtr_corridor c ON c.corridor_code_var = s.corridor_code_var AND c.status_int = 1
			-- LEFT JOIN sot.t_mtr_card_type ct ON ct.id_int = trx.card_type AND ct.status_int = 1 AND product_id_int IN (0,1)
				{$where}";

		$sql2 = "SELECT SUM(amount)
			FROM (
				-- SELECT * FROM trx.t_trx_settlement_bca
				-- UNION ALL
				SELECT $field_name FROM trx.t_trx_settlement_bni
				UNION ALL
				SELECT $field_name FROM trx.t_trx_settlement_bri
				UNION ALL
				SELECT $field_name FROM trx.t_trx_settlement_mandiri) AS trx
			LEFT JOIN master.t_mtr_bank b ON b.bank_id = trx.bank_id AND b.status = 1
			LEFT JOIN master.t_mtr_status_settlement ss ON ss.status_code = trx.status AND ss.status = 1
			-- LEFT JOIN opr.t_mtr_shelter s ON s.shelter_code_var = trx.shelter_code AND s.status_int = 1
			-- LEFT JOIN opr.t_mtr_corridor c ON c.corridor_code_var = s.corridor_code_var AND c.status_int = 1
			-- LEFT JOIN sot.t_mtr_card_type ct ON ct.id_int = trx.card_type AND ct.status_int = 1 AND product_id_int IN (0,1)
				{$where}
				ORDER BY transaction_date DESC";

		return $this->_dbstm->query($sql)->result();
	}

	public function dropdown_status(){
		$sql = "SELECT status_code, status_name FROM master.t_mtr_status_settlement WHERE status = 1";
		$sql = $this->_dbstm->query($sql)->result();
		$data[''] = 'All Status';

		foreach ($sql as $row) {
			$data[$row->status_code] = $row->status_name;
		}

		return $data;
	}

	public function dropdown_bank(){
		$sql = "SELECT bank_id, bank_name FROM master.t_mtr_bank WHERE status = 1";
		$sql = $this->_dbstm->query($sql)->result();
		$data[''] = 'All Bank';

		foreach ($sql as $row) {
			$data[$row->bank_id] = $row->bank_name;
		}

		return $data;
	}

	public function list_status(){
		$sql = "SELECT status_code, status_name 
			FROM master.t_mtr_status_settlement 
			WHERE status = 1 ORDER BY status_code ASC";

		return $this->_dbstm->query($sql)->result();
	}

	public function list_status_rf(){
		$sql = "SELECT status_code, status_name 
			FROM master.t_mtr_status_settlement 
			WHERE status = 1 AND status_code NOT IN (-1, -2) ORDER BY status_code ASC";

		return $this->_dbstm->query($sql)->result();
	}

	public function checkUserBank(){
		$sql = "select c.id from core.t_mtr_user_bank a
							join core.t_mtr_user b on a.user_id=b.id 
							JOIN core.t_mtr_bank c ON a.bank_abbr = c.bank_abbr
							where user_id='".$this->session->userdata('id')."' and a.status=1";
		
		return $this->db->query($sql);
	}
}
