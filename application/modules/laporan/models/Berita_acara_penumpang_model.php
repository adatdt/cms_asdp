<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Berita_acara_penumpang_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
	}

	public function list_data() {
		$start        = $this->input->post('start');
		$length       = $this->input->post('length');
		$draw         = $this->input->post('draw');
		$search       = $this->input->post('search');
		$order        = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir    = strtoupper($order[0]['dir']);
		$date     	  = $this->input->post('date');
		$port     	  = $this->enc->decode($this->input->post('port'));
		$regu     	  = $this->enc->decode($this->input->post('regu'));
		$shift     	  = $this->enc->decode($this->input->post('shift'));

		$field = array(
			1 => 'AR.id',
		);

		// $order_column = $field[$order_column];
		$where = " WHERE AR.id IS NOT NULL";

		if(!empty($date)){
			$where .=" AND AR.assignment_date = '$date'";
		}

		if(!empty($port)){
			$where .=" and AR.port_id = '$port'";
		}

		if(!empty($regu)){
			$where .=" and AR.team_code = '$regu'";
		}

		if(!empty($shift)){
			$where .=" and AR.shift_id = '$shift'";
		}

		$sql = "SELECT
					DISTINCT AR.id,
					AR.supervisor_id,
					AR.assignment_code,
					AR.assignment_date,
					AR.port_id,
					AR.team_code,
					AR.shift_id,
					P.name AS port_name,
					T.team_name,
					SH.shift_name
				FROM
					app.t_trx_assignment_regu AR
					JOIN app.t_trx_opening_balance OB ON OB.assignment_code = AR.assignment_code
					JOIN app.t_mtr_port P ON P.id = AR.port_id
					JOIN core.t_mtr_team T ON T.team_code = AR.team_code
					JOIN app.t_mtr_shift SH ON SH.id = AR.shift_id
				{$where}";

		// die($sql);

		$query          = $this->db->query($sql);
		$records_total  = $query->num_rows();

		// $sql .=" ORDER BY " . $order_column . " {$order_dir}";

		$query      = $this->db->query($sql);
		$rows_data  = $query->result();
		$rows       = array();
		$i          = ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			$row->actions  = "";

			if (checkBtnAccess('laporan/berita_acara_penumpang','download_pdf')) {
				// $parameter = "{$row->port_id}|{$row->created_on}|{$row->team_code}|{$row->shift_id}";
				$row->actions  = '<a href="'.site_url('laporan/berita_acara_penumpang/detail/'.$this->enc->encode($row->assignment_code)).'" class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="tblgoshow"><span>PDF</span></a>';
			}

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'            => $draw,
			'recordsTotal'    => $records_total,
			'recordsFiltered' => $records_total,
			'data'            => $rows,
		);
	}

	public function get_data_waktu($assignment_code)
	{
		$sql = $this->db->query("SELECT DISTINCT(assignment_date) FROM app.t_trx_assignment_regu WHERE assignment_code='$assignment_code'")->row();
		// echo $sql;
		if ($sql) {
			return $sql->assignment_date;
		}else{
			return false;
		}
	}

	public function get_spv_name($assignment_code)
	{
		$sql = "SELECT
					DISTINCT(U.id),
					U.first_name || ' ' || U.last_name as full_name
				FROM
					app.t_trx_opening_balance OB
					JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code
					JOIN core.t_mtr_user U ON U.ID = AR.supervisor_id
				WHERE
					OB.assignment_code = '$assignment_code'";
		return $this->db->query($sql)->row()->full_name;
	}

	public function get_team_name($assignment_code)
	{
		$sql = "SELECT
					AR.assignment_code,
					T.team_name
				FROM
					app.t_trx_assignment_regu AR
				JOIN core.t_mtr_team T ON T.team_code = AR.team_code
					WHERE AR.assignment_code = '$assignment_code'";
		return $this->db->query($sql)->row()->team_name;
	}

	public function get_shift_name($assignment_code)
	{
		$sql = "SELECT
					S.shift_name
				FROM
					app.t_trx_assignment_regu AR
				JOIN app.t_mtr_shift S ON S.id = AR.shift_id
					WHERE AR.assignment_code = '$assignment_code'";
		return $this->db->query($sql)->row()->shift_name;
	}

	public function get_detail($assignment_code,$param)
	{
		$select = "";
		$where  = "";

		if ($param == 'loket_tunai_dewasa') {
			$select = "COUNT(BP.id) as jumlah";
			$where = " AND BP.passanger_type_id= 1 AND (LOWER(PY.channel) like '%pos%' OR LOWER(PY.channel) = 'vm' OR LOWER(PY.channel) = 'cash')";
		}

		if ($param == 'loket_tunai_anak') {
			$select = "COUNT(BP.id) as jumlah";
			$where = " AND BP.passanger_type_id= 2 AND (LOWER(PY.channel) like '%pos%' OR LOWER(PY.channel) = 'vm' OR LOWER(PY.channel) = 'cash')";
		}

		if ($param == 'loket_non_tunai_dewasa') {
			$select = "COUNT(BP.id) as jumlah";
			$where = " AND BP.passanger_type_id= 1 AND LOWER(PY.channel) like '%prepaid%'";
		}

		if ($param == 'loket_non_tunai_anak') {
			$select = "COUNT(BP.id) as jumlah";
			$where = " AND BP.passanger_type_id= 2 AND LOWER(PY.channel) like '%prepaid%'";
		}

		if ($param == 'loket_online_dewasa') {
			$select = "COUNT(BP.id) as jumlah";
			$where = " AND BP.passanger_type_id= 1 AND LOWER(PY.channel) IN ('web','mobile')";
		}

		if ($param == 'loket_online_anak') {
			$select = "COUNT(BP.id) as jumlah";
			$where = " AND BP.passanger_type_id= 2 AND LOWER(PY.channel) IN ('web','mobile')";
		}

		if ($param == 'tarif_dewasa') {
			$select = "DISTINCT(BP.fare)";
			$where = " AND BP.passanger_type_id= 1";
		}

		if ($param == 'tarif_anak') {
			$select = "DISTINCT(BP.fare)";
			$where = " AND BP.passanger_type_id= 2";
		}

		if ($param == 'tunai_dewasa') {
			$select = "SUM(BP.fare) as jumlah";
			$where = " AND BP.passanger_type_id= 1 AND (LOWER(PY.channel) like '%pos%' OR LOWER(PY.channel) = 'vm' OR LOWER(PY.channel) = 'cash')";
		}

		if ($param == 'tunai_anak') {
			$select = "SUM(BP.fare) as jumlah";
			$where = " AND BP.passanger_type_id= 2 AND (LOWER(PY.channel) like '%pos%' OR LOWER(PY.channel) = 'vm' OR LOWER(PY.channel) = 'cash')";
		}

		if ($param == 'online_dewasa') {
			$select = "SUM(BP.fare) as jumlah";
			$where = " AND BP.passanger_type_id= 1 AND LOWER(PY.channel) IN ('web','mobile')";
		}

		if ($param == 'online_anak') {
			$select = "SUM(BP.fare) as jumlah";
			$where = " AND BP.passanger_type_id= 2 AND LOWER(PY.channel) IN ('web','mobile')";
		}

		$sql = "SELECT 
                    $select
                FROM
                    app.t_trx_opening_balance OB
                    JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
                    JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
                    JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
                    JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number 
                WHERE
                    OB.assignment_code = '$assignment_code'
                    AND BO.service_id = 1
                    $where";

		return $this->db->query($sql)->row();
	}

	public function old_get_detail($param)
	{
		$waktu = date('Y-m-d', strtotime($param[1]));

		// $sql = "SELECT
		// DISTINCT(S.id) as id_sell,
		// BP.id as id_bp,
		// T.name as team_name,
		// SH.shift_name as shift_name,
		// BP.passanger_type_id,
		// BO.service_id,
		// AR.team_code,
		// AR.shift_id,
		// S.*
		// FROM
		// app.t_trx_sell S
		// JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
		// JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code
		// JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
		// JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
		// JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
		// JOIN app.t_mtr_team T ON T.team_code = AR.team_code
		// JOIN app.t_mtr_shift SH ON SH.id = AR.shift_id
		// WHERE
		// BO.service_id=1
		// AND to_char(S.created_on,'yyyy-mm-dd') = '{$waktu}'
		// AND AR.port_id = {$param[0]}
		// AND AR.team_code = '{$param[2]}'
		// AND AR.shift_id = {$param[3]}";

		// die($sql);exit;

		$sql = "SELECT *, COALESCE(cash,0)+COALESCE(non_cash,0) AS total_loket, qty_cash+qty_non_cash+qty_online AS total_tiket,COALESCE(cash,0)+COALESCE(non_cash,0)+COALESCE(online,0) AS total_pendapatan FROM (SELECT 
			name, 
			{$this->report_sum($param,'count','p.id','cash')} AS qty_cash,
			{$this->report_sum($param,'count','p.id','online')} AS qty_online,
			{$this->report_sum($param,'count','p.id','prepaid')} AS qty_non_cash,
			{$this->report_sum($param,'sum','p.id','cash')} AS cash,
			{$this->report_sum($param,'sum','p.id','online')} AS online,
			{$this->report_sum($param,'sum','p.id','prepaid')} AS non_cash,
			{$this->report_sum($param,'fare','p.id')} AS harga
			FROM app.t_mtr_passanger_type p
			WHERE status = 1) a";
			// die($sql);
		$rows_data = $this->db->query($sql)->result();

		$sql2 = "SELECT DISTINCT
			U.first_name || ' ' || U.last_name as spv_name
			FROM
			app.t_trx_sell S
			JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
			JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code
			JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
			JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
			JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
			JOIN core.t_mtr_user U ON U.id = AR.supervisor_id
			WHERE
			BO.service_id = 1 
			AND to_char(S.created_on,'yyyy-mm-dd') = '{$waktu}'
			AND AR.port_id = {$param[0]}
			AND AR.team_code = '{$param[2]}'
			AND AR.shift_id = {$param[3]}";

		$sql3 = "SELECT DISTINCT
			TT.team_name
			FROM
			app.t_trx_sell S
			JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
			JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code
			JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
			JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
			JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
			JOIN core.t_mtr_team TT ON TT.team_code = AR.team_code
			WHERE
			BO.service_id = 1 
			AND to_char(S.created_on,'yyyy-mm-dd') = '{$waktu}'
			AND AR.port_id = {$param[0]}
			AND AR.team_code = '{$param[2]}'
			AND AR.shift_id = {$param[3]}";

		$sql4 = "SELECT DISTINCT
			SS.shift_name
			FROM
			app.t_trx_sell S
			JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
			JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code
			JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
			JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
			JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number
			JOIN app.t_mtr_shift SS ON SS.id = AR.shift_id
			WHERE
			BO.service_id = 1 
			AND to_char(S.created_on,'yyyy-mm-dd') = '{$waktu}'
			AND AR.port_id = {$param[0]}
			AND AR.team_code = '{$param[2]}'
			AND AR.shift_id = {$param[3]}";

		$total = 0;
		foreach ($rows_data as $row) {
			$row->cash = $row->cash ? $row->cash : 0;
			$row->non_cash = $row->non_cash ? $row->non_cash : 0;
			$row->online = $row->online ? $row->online : 0;
			// if ($row->passanger_type_id == 1) {
			// 	$row->dewasa = $row->amount;
			// }
			// $row->dewasa()

			// unset($row->id_sell);
			// unset($row->id_bp);
			// unset($row->created_by);
			$total += $row->cash + $row->non_cash + $row->online;
			$rows[] = $row;
		}

		return array(
			'data' => $rows,
			'spv_name' => $this->db->query($sql2)->row()->spv_name,
			'team_name' => $this->db->query($sql3)->row()->team_name,
			'shift_name' => $this->db->query($sql4)->row()->shift_name,
			'total' => $total,
		);
	}

	public function port_list()
	{
		return $this->db->query('SELECT * FROM app.t_mtr_port WHERE status=1')->result();
	}

	public function team_list()
	{
		return $this->db->query('SELECT * FROM core.t_mtr_team WHERE status=1')->result();
	}

	public function shift_list()
	{
		return $this->db->query('SELECT * FROM app.t_mtr_shift WHERE status=1')->result();
	}

	public function report_sum($param,$type_count,$pass_type,$pay_type=FALSE){
		if($type_count == 'count'){
			$select = 'count(BP.id)';
		}elseif ($type_count == 'fare') {
			$select = 'DISTINCT BP.fare';
		}elseif($type_count == 'sum'){
			$select = 'sum(fare)';
		}

		if($pay_type == 'cash'){
			$where = "AND (LOWER(PY.channel) like '%pos%' OR LOWER(PY.channel) = 'vm' OR LOWER(PY.channel) = 'cash')";
		}elseif($pay_type == 'online'){
			$where = " AND LOWER(PY.channel) IN ('web','mobile')";
		}elseif ($pay_type == 'prepaid') {
			$where = "AND LOWER(PY.channel) like '%prepaid%'";
		}else{
			$where = "";
		}

		$waktu = date('Y-m-d', strtotime($param[1]));

		$sql = "(SELECT {$select}
			FROM
				app.t_trx_sell S
				JOIN app.t_trx_opening_balance OB ON OB.ob_code = S.ob_code
				JOIN app.t_trx_assignment_regu AR ON AR.assignment_code = OB.assignment_code
				JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number
				JOIN app.t_trx_booking_passanger BP ON BP.booking_code = BO.booking_code
				JOIN app.t_trx_payment PY ON PY.trans_number = BO.trans_number 
			WHERE
				BO.service_id = 1 
				AND to_char(S.created_on,'yyyy-mm-dd') = '{$waktu}'
				AND AR.port_id = {$param[0]}
				AND AR.team_code = '{$param[2]}'
				AND AR.shift_id = {$param[3]}
				AND BP.passanger_type_id = {$pass_type}
				{$where})";

		return $sql;
	}
}