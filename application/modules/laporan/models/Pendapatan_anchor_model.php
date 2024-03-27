<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ------------------------------------
 * CLASS NAME : Pendapatan_anchor_model
 * ------------------------------------
 *
 * @author     Robai
 * @copyright  2019
 *
 */

class Pendapatan_anchor_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'laporan/pendapatan_anchor';
	}

    public function dataList(){
    	$post = $this->input->post();

		$sql = "SELECT
					a.created_on date,
					UPPER(c.name) dock_name,
					b.call_anchor,
					b.ship_grt,
					b.dock_fare,
					b.dock_fare * b.ship_grt total,
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
					a.created_on::date BETWEEN '{$post['start_date']}' AND '{$post['end_date']}'
					AND a.ship_id = {$this->enc->decode($post['ship'])} AND a.port_id = {$this->enc->decode($post['port'])}";

		$data  		= array();
		$query 		= $this->dbView->query($sql)->result();
		$total_anchor = 0;
		$total_grt	= 0;
		$sub_total 	= 0;

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

		return array (
			'data' 		=> $data,
			'detail' 	=> $query[0],
			'post' 		=> $post,
			'tanggal' 	=> tgl_indo($post['start_date']).' - '.tgl_indo($post['end_date']),
			'total_anchor' => $total_anchor,
			'total_grt' => $total_grt,
			'sub_total' => $sub_total,
		);
	}
}
