<?php

class Ship_income_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		$this->_module = 'laporan/ship_income';
	}

	public function shipIncomeList() {
		$start        = $this->input->post('start');
		$length       = $this->input->post('length');
		$draw         = $this->input->post('draw');
		$search       = $this->input->post('search');
		$order        = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir    = strtoupper($order[0]['dir']);
		$datefrom     = $this->input->post('datefrom');
		$dateto       = $this->input->post('dateto');

		$field = array(
			1 => 'name',
			2 => 'ab.schedule_date',
			3 => 'countpassanger',
			4 => 'sum_passanger',
			5 => 'countvehicle',
			6 => 'sum_vehicle',
			7 => 'total',
		);

		$order_column = $field[$order_column];
		$where = " WHERE ab.schedule_date IS NOT NULL";

		if ((!empty($datefrom) and empty($dateto))||(empty($datefrom) and !empty($dateto))){
			$where .=" AND ab.schedule_date ='$datefrom' or ab.schedule_date ='$dateto'";
		}

		if(!empty($datefrom) and !empty($dateto)){
			$where .=" AND ab.schedule_date between '$datefrom' and  '$dateto'";   
		}

		$sql = "SELECT
					SH.NAME AS ship_name,
					SH.id as ship_id,
					OBO.boarding_code,
					ab.* 
				FROM
					app.t_trx_sail ab
					JOIN app.t_trx_schedule ts ON ts.schedule_code = ab.schedule_code
					JOIN app.t_mtr_ship SH ON SH.ID = ab.ship_id
					JOIN app.t_trx_open_boarding OBO ON OBO.schedule_code = ab.schedule_code {$where}";

		$query          = $this->db->query($sql);
		$records_total  = $query->num_rows();

		$sql .=" ORDER BY " . $order_column . " {$order_dir}";

		// if ($length != -1) {
		//   $sql .=" LIMIT {$length} OFFSET {$start};";
		// }

		$query      = $this->db->query($sql);
		$rows_data  = $query->result();
		$rows       = array();
		$i          = ($start + 1);
		$total_income = 0;

		foreach ($rows_data as $row) {
			$row->number        = $i;
			$row->id            = $this->enc->encode($row->id);
			$sum_passanger = $this->count_passanger($row->boarding_code,'sum');
			$sum_vehicle = $this->count_vehicle($row->boarding_code,'sum');

			$row->countpassanger = idr_currency($this->count_passanger($row->boarding_code,'jumlah'));
			$row->sum_passanger = idr_currency($sum_passanger);

			$row->countvehicle = idr_currency($this->count_vehicle($row->boarding_code,'jumlah'));
			$row->sum_vehicle = idr_currency($sum_vehicle);

			$detail_url         = site_url($this->_module."/detail/{$this->enc->encode($row->boarding_code)}/{$row->id}");
			// $row->actions       = generate_button_new($this->_module, 'detail', $detail_url);

			if (checkBtnAccess($this->_module,'download_pdf')) {
				$row->actions  = '<a href="'.$detail_url.'" class="btn btn-primary"><i class="fa fa-search-plus" title="Detail"></i></a>';
			}
			// $total_income      += $row->total;
			// $row->depart_date   = format_dateTime($row->depart_date);
			$row->total         = idr_currency($sum_passanger + $sum_vehicle);
			// $row->sum_passanger = idr_currency($row->sum_passanger);
			// $row->sum_vehicle   = idr_currency($row->sum_vehicle);

			$rows[] = $row;
			unset($row->id);
			
			$i++;
		}

		return array(
			'draw'            => $draw,
			'recordsTotal'    => $records_total,
			'recordsFiltered' => $records_total,
			'data'            => $rows,
			'total'           => idr_currency($total_income)

		);
	}

	private function count_passanger($boarding_code,$param)
	{
		if ($param === 'jumlah') {
			$sql = $this->db->query("SELECT
				COUNT( BOP.ID ) AS jumlah 
				FROM
				app.t_trx_boarding_passanger BOP
				JOIN app.t_trx_booking_passanger BP ON BP.ticket_number = BOP.ticket_number
				WHERE
				BOP.boarding_code = '$boarding_code'")->row();

			return $sql->jumlah;
		}else if ($param === 'sum') {
			$sql = $this->db->query("SELECT
				COALESCE(SUM( BP.fare ),0) AS jumlah 
				FROM
				app.t_trx_boarding_passanger BOP
				JOIN app.t_trx_booking_passanger BP ON BP.ticket_number = BOP.ticket_number AND BP.service_id = 1
				WHERE
				BOP.boarding_code = '$boarding_code'")->row();

			return $sql->jumlah;
		}
	}

	private function count_vehicle($boarding_code,$param)
	{
		if ($param === 'jumlah') {
			$sql = $this->db->query("SELECT
				COUNT( BOV.id ) AS jumlah 
				FROM
				app.t_trx_boarding_vehicle BOV
				JOIN app.t_trx_booking_vehicle BV ON BV.ticket_number = BOV.ticket_number
				WHERE
				BOV.boarding_code = '$boarding_code'")->row();
			return $sql->jumlah;
		}else if ($param === 'sum') {
			$sql = $this->db->query("SELECT
				COALESCE(SUM( BV.fare ),0) AS jumlah 
				FROM
				app.t_trx_boarding_vehicle BOV
				JOIN app.t_trx_booking_vehicle BV ON BV.ticket_number = BOV.ticket_number
				WHERE
				BOV.boarding_code = '$boarding_code'")->row();
			return $sql->jumlah;
		}
	}

	public function detail_passanger(){
		return $passanger=$this->db->query("select b.* from app.t_trx_boarding_detail a
		join app.t_trx_booking_passanger b on a.ticket_number=b.ticket_number where a.boarding_id=$id and a.status=1 ");
	}

	public function getDetailHeader($id){
		$sql = "SELECT *
				FROM app.t_trx_sail s 
				LEFT JOIN app.t_mtr_ship sh ON sh.id = s.ship_id WHERE s.id = $id";

		return $this->db->query($sql)->row();
	}

	function newdetail($boarding_code) 
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');

		$sql = "SELECT PTT.name as golongan,COALESCE(J.jumlah,0) as produksi,COALESCE(J.pendapatan,0) as pendapatan FROM app.t_mtr_passanger_type PTT LEFT JOIN(
		SELECT
		SH.id as ship_id,
		SH.name as ship_name,
		BP.passanger_type_id,
		PT.name,
		COUNT(BP.id) as jumlah,
		SUM(BP.fare) as pendapatan
		FROM
		app.t_mtr_passanger_type PT
		LEFT JOIN app.t_trx_booking_passanger BP ON BP.passanger_type_id = PT.id AND BP.service_id = 1
		JOIN app.t_trx_boarding_passanger BOP ON BOP.ticket_number = BP.ticket_number AND BOP.boarding_code = '$boarding_code'
		JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BOP.boarding_code
		JOIN app.t_trx_sail SA ON SA.schedule_code = OBO.schedule_code
		JOIN app.t_mtr_ship SH ON SH.id = SA.ship_id
		WHERE
		PT.status = 1
		GROUP BY
		SH.id,
		SH.name,
		BP.passanger_type_id,
		PT.name) J ON J.passanger_type_id = PTT.id
		WHERE PTT.status=1

		UNION ALL

		SELECT VCC.name as golongan,COALESCE(JX.jumlah,0) as produksi,COALESCE(JX.pendapatan,0) as pendapatan FROM app.t_mtr_vehicle_class VCC LEFT JOIN(
		SELECT
		SH.name as ship_name,
		BV.vehicle_class_id,
		VC.name,
		COUNT(BV.id) as jumlah,
		SUM(BV.fare) as pendapatan
		FROM
		app.t_mtr_vehicle_class VC
		LEFT JOIN app.t_trx_booking_vehicle BV ON BV.vehicle_class_id = VC.id
		JOIN app.t_trx_boarding_vehicle BOV ON BOV.ticket_number = BV.ticket_number AND BOV.boarding_code = '$boarding_code'
		JOIN app.t_trx_open_boarding OBO ON OBO.boarding_code = BOV.boarding_code
		JOIN app.t_trx_sail SA ON SA.schedule_code = OBO.schedule_code
		JOIN app.t_mtr_ship SH ON SH.id = SA.ship_id
		WHERE
		VC.status=1
		GROUP BY
		SH.name,
		BV.vehicle_class_id,
		VC.name) JX ON JX.vehicle_class_id = VCC.id
		WHERE
		VCC.status=1 ORDER BY golongan ASC";

		// $result = $this->db->query($sql)->result();

		$query = $this->db->query($sql);
		$records_total = $query->num_rows();

		// $sql .=" ORDER BY " . $order_column . " {$order_dir}";

		// if ($length != -1) {
		// 	$sql .=" LIMIT {$length} OFFSET {$start};";
		// }

		$query = $this->db->query($sql);
		$rows_data = $query->result();

		$rows = array();
		$i = ($start + 1);
		$total_income = 0;

		foreach ($rows_data as $row) {
			$row->number = $i;
			$rows[] = $row;
			$total_income += $row->pendapatan;
			$row->produksi = idr_currency($row->produksi);
			$row->pendapatan = idr_currency($row->pendapatan);
			// $grand_total += $value->amount;
			// $result[$key]->total = idr_currency($value->total);
			// $result[$key]->amount = idr_currency($value->amount);
		}

		return array(
			// 'draw' => $this->input->post('draw'),
			// 'recordsTotal' => count($result),
			// 'recordsFiltered' => count($result),
			// 'data' => $row,
			// 'total' => idr_currency($grand_total)
			'draw' => $draw,
			'recordsTotal' => $records_total,
			'recordsFiltered' => $records_total,
			'data' => $rows,
			'total_semua'=> idr_currency($total_income),
		);
	}

	public function getDetail($id){
		$start        = $this->input->post('start');
		$length       = $this->input->post('length');
		$draw         = $this->input->post('draw');
		$order        = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir    = strtoupper($order[0]['dir']);

		$field = array(
			3 => 'name',
			4 => 'production',
			5 => 'income'
		);

		$order_column = $field[$order_column];
		
		$sql = "SELECT 
				pt.id, pt.name, 
				(SELECT(SELECT DISTINCT ad.fare FROM 
						app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_passanger ad ON ac.ticket_number = ad.ticket_number AND ad.passanger_type_id = pt.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.schedule_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS fare, 

				(SELECT(SELECT COUNT(ad.fare) FROM app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_passanger ad ON ac.ticket_number = ad.ticket_number AND ad.passanger_type_id = pt.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.schedule_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS production, 

				(SELECT(SELECT SUM(ad.fare) FROM app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_passanger ad ON ac.ticket_number = ad.ticket_number AND ad.passanger_type_id = pt.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.schedule_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS income
				FROM app.t_mtr_passanger_type pt WHERE pt.status = 1

				UNION ALL

				SELECT vc.id, vc.name, 
				(SELECT(SELECT DISTINCT ad.fare FROM app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_vehicle ad ON ac.ticket_number = ad.ticket_number AND ad.vehicle_class_id = vc.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.schedule_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS fare, 

				(SELECT(SELECT COUNT(ad.fare) FROM app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_vehicle ad ON ac.ticket_number = ad.ticket_number AND ad.vehicle_class_id = vc.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.schedule_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS production, 

				(SELECT(SELECT SUM(ad.fare) FROM app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_vehicle ad ON ac.ticket_number = ad.ticket_number AND ad.vehicle_class_id = vc.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.schedule_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS income  
				FROM app.t_mtr_vehicle_class vc WHERE vc.status = 1

				ORDER BY {$order_column} {$order_dir}";

		// die($sql);

		$query          = $this->db->query($sql);
		$records_total  = $query->num_rows();
		$query          = $this->db->query($sql);
		$rows_data      = $query->result();
		$rows           = array();
		$i              = ($start + 1);
		$total_income   = 0;

		foreach ($rows_data as $row) {
			if($row->income == null){
				$row->income = 0;
			}

			if($row->fare == null){
				$row->fare = 0;
			}

			$total_income += $row->income;
			$row->date     = $this->getDetailHeader($id)->schedule_date;
			$row->ship     = $this->getDetailHeader($id)->name;
			$row->number   = $i;
			$row->fare     = idr_currency($row->fare);
			$row->income   = idr_currency($row->income);
			$rows[]        = $row;
			$i++;
		}

		return array(
			'draw'              => $draw,
			'recordsTotal'      => $records_total,
			'recordsFiltered'   => $records_total,
			'data'              => $rows,
			'total'             => idr_currency($total_income)
		);
	}

	function get_pdf_penumpang($id)
	{
		$sql = "SELECT 
				pt.id, pt.name, 
				(SELECT(SELECT DISTINCT ad.fare FROM app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_passanger ad ON ac.ticket_number = ad.ticket_number AND ad.passanger_type_id = pt.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.depart_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS fare, 

				(SELECT(SELECT COUNT(ad.fare) FROM app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_passanger ad ON ac.ticket_number = ad.ticket_number AND ad.passanger_type_id = pt.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.depart_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS production, 

				(SELECT(SELECT SUM(ad.fare) FROM app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_passanger ad ON ac.ticket_number = ad.ticket_number AND ad.passanger_type_id = pt.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.depart_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS income
				FROM app.t_mtr_passanger_type pt WHERE pt.status = 1";

		return $this->db->query($sql);
	}

	function get_pdf_kendaraan($id)
	{
		$sql=("SELECT vc.id, vc.name, 
				(SELECT(SELECT DISTINCT ad.fare FROM app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_vehicle ad ON ac.ticket_number = ad.ticket_number AND ad.vehicle_class_id = vc.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.depart_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS fare, 

				(SELECT(SELECT COUNT(ad.fare) FROM app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_vehicle ad ON ac.ticket_number = ad.ticket_number AND ad.vehicle_class_id = vc.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.depart_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS production, 

				(SELECT(SELECT SUM(ad.fare) FROM app.t_trx_boarding aa
						JOIN app.t_trx_sail ab ON aa.id = ab.boarding_id
						JOIN app.t_trx_boarding_detail ac ON aa.id = ac.boarding_id AND ac.status = 1
						JOIN app.t_trx_booking bo ON bo.id = ac.booking_id AND bo.status = 4
						JOIN app.t_trx_booking_vehicle ad ON ac.ticket_number = ad.ticket_number AND ad.vehicle_class_id = vc.id AND ad.status = 4
						WHERE ab.ship_id = a.ship_id AND to_char(ab.depart_date,'yyyy-mm-dd') = to_char(a.created_on,'yyyy-mm-dd')) FROM app.t_trx_sail a WHERE a.id = $id) AS income  
				FROM app.t_mtr_vehicle_class vc WHERE vc.status = 1");

		return $this->db->query($sql);
	}
}