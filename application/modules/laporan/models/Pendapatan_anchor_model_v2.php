<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pendapatan_anchor_model_v2 extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'laporan/pendapatan_anchor_v2';
	}

	public function dataList()
	{
		$post = $this->input->post();

		$shift = $this->enc->decode($post['shift']);
		$shift_name = "Semua";
		$session_shift_class = $this->session->userdata('ship_class_id');

		if ($shift != "") {
			$select_shift = $this->db->query("SELECT shift_name FROM app.t_mtr_shift WHERE id = {$shift}")->row();
			$shift_name = $select_shift->shift_name;
		}

		if ($shift != "") {
			$where_shift = " AND a.shift_id = $shift";
		}

		if (!empty($session_shift_class)) {
			$where .= " and d.ship_class = {$session_shift_class}";
		}

		$sql = "SELECT
					a.created_on date,
					UPPER(c.name) dock_name,
					COALESCE ( b.call_anchor, 0 ) AS call_anchor,
					COALESCE ( b.ship_grt, 0 ) AS ship_grt,
					COALESCE ( b.dock_fare, 0 ) AS dock_fare,
					COALESCE ( b.dock_fare * b.ship_grt, 0 ) AS total,
					UPPER(d.name) ship_name,
					UPPER(e.name) class_name,
					UPPER(f.name) company_name,
					UPPER(g.name) origin,
					UPPER(h.name) destination				
				FROM
					app.t_trx_anchor a 
				JOIN app.t_trx_schedule b ON b.schedule_code = a.schedule_code
				JOIN app.t_mtr_dock c ON c.id = a.dock_id
				JOIN app.t_mtr_ship d ON d.id = a.ship_id
				JOIN app.t_mtr_ship_class e ON e.id = d.ship_class
				JOIN app.t_mtr_ship_company f ON f.id = d.ship_company_id
				JOIN app.t_mtr_port g ON g.id = a.port_id
				JOIN app.t_mtr_port h ON h.id = b.destination_port_id
				WHERE 
					a.shift_date BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'
	AND a.ship_id = {$this->enc->decode($post['ship'])} AND a.port_id = {$this->enc->decode($post['port'])} {$where_shift} {$where}";
		$data  			= array();
		$query 			= $this->db->query($sql)->result();
		$total_anchor 	= 0;
		$total_grt		= 0;
		$sub_total 		= 0;

		foreach ($query as $key => $row) {
			$row->no 		= $key + 1;

			$total_anchor 	+= $row->call_anchor;
			$total_grt 		+= $row->ship_grt;
			$sub_total 		+= $row->total;

			$row->ship_grt 	= $row->ship_grt;
			$row->dock_fare = $row->dock_fare;
			$row->total 	= $row->total;

			$data[] 		= $row;
		}

		return array(
			'data' 		 	=> $data,
			'detail' 	 	=> $query[0],
			'shift_name' 	=> $shift_name,
			'post' 		 	=> $post,
			'tanggal' 	 	=> tgl_indo($post['start_date']) . ' - ' . tgl_indo($post['end_date']),
			'total_anchor' 	=> $total_anchor,
			'total_grt' 	=> $total_grt,
			'sub_total' 	=> $sub_total,
		);
	}
}
