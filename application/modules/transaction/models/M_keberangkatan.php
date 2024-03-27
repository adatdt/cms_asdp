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

class M_keberangkatan extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'transaction/keberangkatan';
	}

	public function dataList()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$ship_class = $this->enc->decode($this->input->post('ship_class'));		
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($searchData)));

		if ($this->get_identity_app() == 0) {
			// mengambil port berdasarkan port di user menggunakan session
			if (!empty($this->session->userdata('port_id'))) {
				$port_origin = $this->session->userdata('port_id');
			} else {
				$port_origin = $this->enc->decode($this->input->post('port_origin'));
			}
		} else {
			$port_origin = $this->get_identity_app();
		}

		$field = array(
			// 0 => 'id',
			0 => 'id',
			1 => 'booking_code',
			2 => 'port_name',
			3 => 'ship_class',
			4 => 'depart_date',
			5 => 'depart_time_start',
			6 => 'vehicle_class',
			7 => 'reservation_date',
			8 => 'reservation_time',
			9 => 'checkin_date',
			10 => 'checkin_time',
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		// $where = " WHERE status is not null and aa.depart_date >= '". $dateFrom . "' and aa.depart_date < '" . $dateToNew . "'";
		$where = " WHERE
										aa.depart_date >= '". $dateFrom . "' and aa.depart_date < '" . $dateToNew . "'
										AND aa.status = 2
										AND aa.origin = '".$port_origin."'
										AND aa.service_id = 2
										AND aa.ship_class = '".$ship_class."'
										AND (aa.channel = 'web' OR aa.channel = 'mobile' OR aa.channel = 'ifcs' OR aa.channel = 'b2b')";


		if(!empty($searchData))
		{
			if($searchName=="bookingCode")
			{
				$where .= "and (aa.booking_code ilike '%" . $iLike . "%')";

			}
			
		}

		$sql = $this->qry($where);
		$sqlCount =$this->qryCount($where);

				// die($sql);

		$queryCount         = $this->dbView->query($sqlCount)->row();
    $records_total 			= $queryCount->countdata;

		// $query         = $this->dbView->query($sql);
		// $records_total = $query->num_rows();
		$sql 		  .= " ORDER BY aa.depart_date DESC, aa.depart_time_start DESC, " . $order_column . " {$order_dir}";

		if ($length != -1) {
			$sql .= " LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;
			// if ($row->status == 1) {
			// 	$row->status = success_label("Aktif");
			// 	$row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \'' . $nonaktif . '\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			// } else {
			// 	$row->status = failed_label("Tidak Aktif");
			// 	$row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \'' . $aktif . '\')" title="aktifkan"> <i class="fa fa-check"></i> </button> ');
			// }

			$row->no = $i;

			$rows[] = $row;
			// unset($row->id);

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

	public function insert_data($table, $data)
	{
		$this->dbAction->insert($table, $data);
	}

	public function update_data($table, $data, $where)
	{
		$this->dbAction->where($where);
		$this->dbAction->update($table, $data);
	}

	public function delete_data($table, $data, $where)
	{
		$this->dbAction->where($where);
		$this->dbAction->delete($table, $data);
	}

	public function download()
	{
		$dateFrom = $this->input->get("dateFrom");
		$dateTo = $this->input->get("dateTo");
		$ship_class = $this->enc->decode($this->input->get("ship_class"));
		$searchData = $this->input->get("searchData");
		$searchName = $this->input->get("searchName");
		$iLike = trim($this->dbView->escape_like_str($searchData));

		// mengambil port berdasarkan port di user menggunakan session
		if ($this->get_identity_app() == 0) 
		{
			// mengambil port berdasarkan port di user menggunakan session
			if (!empty($this->session->userdata('port_id')))
			{
				$port_origin = $this->session->userdata('port_id');
			}
			 else 
			 {
				$port_origin = $this->enc->decode($this->input->get('port_origin'));
			}
		} else {
			$port_origin = $this->get_identity_app();
		}

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		// $where = " WHERE status is not null and aa.depart_date >= '". $dateFrom . "' and aa.depart_date < '" . $dateToNew . "'";
		$where = " WHERE
										aa.depart_date >= '". $dateFrom . "' and aa.depart_date < '" . $dateToNew . "'
										AND aa.status = 2
										AND aa.origin = '".$port_origin."'
										AND aa.service_id = 2
										AND aa.ship_class = '".$ship_class."'
										AND (aa.channel = 'web' OR aa.channel = 'mobile' OR aa.channel = 'ifcs' OR aa.channel = 'b2b')";


		if(!empty($searchData))
		{
			if($searchName=="bookingCode")
			{
				$where .= "and (aa.booking_code ilike '%" . $iLike . "%')";

			}
			
		}

		$sql =$this->qry($where)." ORDER BY aa.depart_date DESC, aa.depart_time_start DESC, cc.id ASC, gg.created_on DESC";			

		$query     = $this->dbView->query($sql);

		return $query;
	}

	public function get_identity_app()
	{
		$data = $this->dbView->query(" select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}

	public function qry($where){

		$qry="SELECT aa.id, aa.booking_code,
		dd.name as port_name,
		ff.name as ship_class,
		aa.depart_date,
		aa.depart_time_start,
		cc.name as vehicle_class,
		date(aa.created_on) as reservation_date,
		TO_CHAR(aa.created_on,'HH24:MI:SS') as reservation_time,
		date(gg.created_on) as checkin_date,
		TO_CHAR(gg.created_on,'HH24:MI:SS') as checkin_time	
	FROM
		app.t_trx_booking aa
		JOIN app.t_trx_booking_vehicle bb ON aa.booking_code = bb.booking_code
		JOIN app.t_mtr_vehicle_class cc ON bb.vehicle_class_id = cc.id
		JOIN app.t_mtr_port dd ON aa.origin = dd.id
		JOIN app.t_mtr_ship_class ff ON aa.ship_class = ff.id
		LEFT JOIN app.t_trx_check_in_vehicle gg ON aa.booking_code = gg.booking_code
		{$where}
		-- ORDER BY aa.depart_date,aa.depart_time,cc.id ASC
		";

		return $qry;
	}

	public function qryCount($where){

		$qry="SELECT 
						count(aa.id) as countdata
					FROM
						app.t_trx_booking aa
						JOIN app.t_trx_booking_vehicle bb ON aa.booking_code = bb.booking_code
						JOIN app.t_mtr_vehicle_class cc ON bb.vehicle_class_id = cc.id
						JOIN app.t_mtr_port dd ON aa.origin = dd.id
						JOIN app.t_mtr_ship_class ff ON aa.ship_class = ff.id
						LEFT JOIN app.t_trx_check_in_vehicle gg ON aa.booking_code = gg.booking_code
						{$where}
						-- ORDER BY aa.depart_date,aa.depart_time,cc.id ASC
						";

		return $qry;
	}
}
