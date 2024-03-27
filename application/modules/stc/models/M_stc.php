<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : M_stc
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class M_stc extends MY_Model
{
	// $param[0] => schedule_code
	// $param[1] => schedule_id trx
	// $param[2] => schedule_id mtr
	// $param[3] => ship_id
	// $param[4] => nama dermaga
	// $param[5] => plot_date
	// $param[6] => dock_date
	// $param[7] => open_boarding_date
	// $param[8] => close_boarding_date
	// $param[9] => close_ramp_door_date
	// $param[10] => sail_date
	// $param[11] => dock_id

	public function __construct()
	{
		parent::__construct();
		$this->_user = $this->session->userdata('username');
	}

	//list pelabuhan
	function get_list_port()
	{
		$sql = "SELECT id, name 
		FROM app.t_mtr_port 
		WHERE status = 1 
		ORDER BY name ASC";

		$data = array('' => '');
		// $query = $this->db->query($sql)->result();
		$query = $this->dbView->query($sql)->result();

		foreach ($query as $row) {
			$data[$this->enc->encode($row->id)] = strtoupper($row->name);
		}

		return $data;
	}

	//list kapal
	function get_list_ship($port_id, $selected_ship)
	{
		$date = date('Y-m-d');
		$ship_id = $selected_ship ? " AND c.ship_id NOT IN ({$selected_ship}) " : "";

		// ini versi terbaru menggunakan ship area saat milih kapal
		$sql_new = "SELECT
							b.id,
							b.name
							, sc.name as ship_class_name
						FROM
							app.t_mtr_ship_area a
						LEFT JOIN app.t_mtr_ship b ON
							a.ship_id = b.id
							AND b.status = 1
						left join app.t_mtr_ship_class sc on a.ship_class = sc.id
						LEFT JOIN app.t_mtr_ship_company c ON
							b.ship_company_id = c.id
							AND c.status = 1
						JOIN (
							SELECT
								DISTINCT ON
								(company_id) company_id,
								port_id,
								status
							FROM
								app.t_mtr_sailing_company
							WHERE
								port_id = '{$port_id}'
								AND status = 1
							GROUP BY
								company_id,
								port_id ,
								status ) d ON
							b.ship_company_id = d.company_id
						WHERE
							a.port_id = '{$port_id}'
							AND a.status = 1";

		// ini versi lama tidak menggunakan ship area saat milih kapal
		$sql = "SELECT
							a.id
							, a.name
							, sc.name as ship_class_name
						FROM
							app.t_mtr_ship a
						left join app.t_mtr_ship_class sc on a.ship_class = sc.id
						JOIN app.t_mtr_sailing_company b ON
							a.ship_company_id = b.company_id
							AND b.port_id = {$port_id}
						LEFT OUTER JOIN (
							SELECT DISTINCT
								c.ship_id
							FROM
								app.t_trx_schedule c
							JOIN app.t_mtr_schedule d ON
								d.schedule_code = c.schedule_code
							WHERE
								d.status = 1
								AND c.status = 1
								AND d.port_id = {$port_id} 
								{$ship_id}
								AND (
									-- (d.schedule_date = '{$date}') OR --issue Gilimanuk 
								(d.schedule_date <= '{$date}'
								AND ((ploting_date IS NOT NULL
								AND c.sail_date IS NULL)
								OR (docking_date IS NOT NULL
								AND c.sail_date IS NULL)
								OR (open_boarding_date IS NOT NULL
								AND c.sail_date IS NULL))))
								AND c.sail_date IS NULL) e ON
							a.id = e.ship_id
						WHERE
							e.ship_id IS NULL
							AND a.status = 1
						ORDER BY
							a.name ASC";

		return $this->dbView->query($sql)->result();
	}

	//ambil data id kapal untuk mencocokan di dropdown (selected)
	function get_id_schecule($code)
	{
		$sql = "SELECT 
			ship_id
		FROM app.t_trx_schedule c
		WHERE status = 1 AND  schedule_code = '{$code}'";

		// $query = $this->db->query($sql);
		$query = $this->dbView->query($sql);

		if ($query->num_rows()) {
			$ship_id = $query->row()->ship_id;
		} else {
			$ship_id = 0;
		}

		return $ship_id;
	}

	//ambil data id kapal untuk mencocokan di dropdown (selected)
	function get_id_schecule_old($code)
	{
		$sql = "SELECT 
			ship_id, 
			schedule_code,
			C.ploting_date AS real_plot_date,
			C.docking_date AS real_dock_date,
			C.open_boarding_date AS real_open_boarding_date,
			C.close_boarding_date AS real_close_boarding_date,
			C.close_ramp_door_date AS real_ramp_door_date,
			C.sail_date AS real_sail_close_date
		FROM app.t_trx_schedule C
		WHERE status = 1 AND  schedule_code = '{$code}'";

		// $query = $this->db->query($sql)->result();
		$query = $this->dbView->query($sql)->result();

		$data = array();

		foreach ($query as $row) {
			if (empty($row->real_plot_date)) {
				$row->ship_id = $row->ship_id;
			} else if (!empty($row->real_plot_date) and empty($row->real_dock_date)) {
				$row->ship_id = $this->get_ship_name('app.t_trx_plot', $row->schedule_code)->ship_id;
			} else if (!empty($row->real_plot_date) and !empty($row->real_dock_date) and empty($row->real_open_boarding_date)) {
				$row->ship_id = $this->get_ship_name('app.t_trx_docking', $row->schedule_code)->ship_id;
			} else if (!empty($row->real_plot_date) and !empty($row->real_dock_date) and !empty($row->real_open_boarding_date) and empty($row->real_close_boarding_date)) {
				$row->ship_id = $this->get_ship_name('app.t_trx_open_boarding', $row->schedule_code)->ship_id;
			} else if (!empty($row->real_plot_date) and !empty($row->real_dock_date) and !empty($row->real_open_boarding_date) and !empty($row->real_close_boarding_date) and empty($row->real_ramp_door_date)) {
				$row->ship_id = $this->get_ship_name('app.t_trx_close_boarding', $row->schedule_code)->ship_id;
			} else if (!empty($row->real_plot_date) and !empty($row->real_dock_date) and !empty($row->real_open_boarding_date) and !empty($row->real_close_boarding_date) and !empty($row->real_ramp_door_date) and empty($row->real_sail_close_date)) {
				$row->ship_id = $this->get_ship_name('app.t_trx_close_ramp_door', $row->schedule_code)->ship_id;
			} else {
				$row->ship_id = $this->get_ship_name('app.t_trx_sail', $row->schedule_code)->ship_id;
			}

			$data[] = $row;
		}

		return $data ? $data[0]->ship_id : 0;
	}

	//ambil data trx schedule
	function get_trx_schecule($id)
	{
		$sql = "SELECT * 
		FROM app.t_trx_schedule 
		WHERE status = 1 AND  id = '{$id}'";

		// return $this->db->query($sql)->row();
		return $this->dbView->query($sql)->row();
	}

	//list dermaga
	function get_list_dock()
	{
		$post = $this->input->post();
		$port_id = $this->enc->decode($post['port']);
		$date = date('Y-m-d');

		if ($port_id) {
			$sql_check = "SELECT id 
						FROM app.t_trx_schedule 
						WHERE port_id = {$port_id}
						AND ((schedule_date::date = '{$date}') OR (schedule_date <= '{$date}'
						AND ploting_date IS NOT NULL AND sail_date IS NULL))
						LIMIT 1";

			// $check_schedule = $this->db->query($sql_check)->result();
			$check_schedule = $this->dbView->query($sql_check)->result();

			if ($check_schedule) {	
				$sql = "SELECT id, name
				FROM app.t_mtr_dock
				WHERE status = 1 AND port_id = {$this->enc->decode($post['port'])}
				ORDER BY name ASC";

				$data = array();
				// $query = $this->db->query($sql)->result();
				$query = $this->dbView->query($sql)->result();

				foreach ($query as $row) {

					$data[$row->name] = array(
						'schedule' =>	 $this->get_list_schedule($row->id)
					);
				}

				$identity_app = $this->cek_identity_app();

				if ($identity_app == -1) {
					$app_id = false;
				} else {
					if ($identity_app == 0) {
						$app_id = true;
					} else {
						$row = $this->global_model->selectById('app.t_mtr_port', 'id', $identity_app);
						if ($row) {
							$app_id = $identity_app;
						} else {
							$app_id = false;
						}
					}
				}

				$arr = array(
					'schedule' => $data,
					'identity_app' => ($app_id and $app_id == $port_id) ? 1 : 0,
					'action' => checkBtnAccess('stc', 'edit') ? 1 : 0,
					'anchor' => $this->get_list_problem('app.t_trx_anchor'),
					'docking' => $this->get_list_problem('app.t_trx_docking_maintenance'),
					'broken' => $this->get_list_problem('app.t_trx_broken')
				);
				// echo json_encode($arr);

				return json_api(1, 'Data jadwal', $arr);
			} else {
				return json_api(2, 'Jadwal tidak tersedia');
			}
		} else {
			return json_api(0, 'Forbidden access!');
		}
	}

	//list jadwal 
	function get_list_schedule($dock)
	{
		$post = $this->input->post();
		$date = date('Y-m-d');

		$sql = "SELECT 
		C.port_id,
		A.id AS schedule_id_master,
		C.id AS schedule_id,
		C.ploting_date AS real_plot_date,
		C.docking_date AS real_dock_date,
		C.open_boarding_date AS real_open_boarding_date,
		C.close_boarding_date AS real_close_boarding_date,
		C.close_ramp_door_date AS real_ramp_door_date,
		C.sail_date AS real_sail_close_date,
		A.docking_on::time as docking_time,
		A.schedule_code,
		b.NAME AS ship_name,
		C.schedule_date,
		b.id AS ship_id,
		d.id AS dock_id,
		d.name AS dock_name
		FROM
		app.t_trx_schedule C 
		JOIN app.t_mtr_schedule A ON A.schedule_code = C.schedule_code
		LEFT JOIN app.t_mtr_ship b ON C.ship_id = b.id
		LEFT JOIN app.t_mtr_dock d ON d.id = a.dock_id
		WHERE
		A.status = 1 AND C.status = 1
		AND A.port_id = {$this->enc->decode($post['port'])}
		AND ((A.schedule_date = '{$date}') OR (A.schedule_date <= '{$date}'
		-- AND ploting_date IS NOT NULL AND C.sail_date IS NULL))
		AND ((ploting_date IS NOT NULL AND C.sail_date IS NULL) OR (docking_date IS NOT NULL AND C.sail_date IS NULL) OR (open_boarding_date IS NOT NULL AND C.sail_date IS NULL))))
		AND A.dock_id = {$dock}
		AND left(A.schedule_code,1) = 'J' 
		ORDER BY 
			C.schedule_date ASC, 
			C.sail_date DESC,
			C.close_ramp_door_date ASC,
			C.close_boarding_date ASC,
			C.open_boarding_date ASC,
			C.docking_date ASC,
			C.ploting_date ASC,
			C.created_on ASC,
			A.docking_on ASC";

		// $query = $this->db->query($sql)->result();
		$query = $this->dbView->query($sql)->result();

		$data = array();
		foreach ($query as $key => $row) {
			// 5 6 7 8 9 10
			$real_date = "{$row->real_plot_date}|{$row->real_dock_date}|{$row->real_open_boarding_date}|{$row->real_close_boarding_date}|{$row->real_ramp_door_date}|{$row->real_sail_close_date}";

			// 11 12
			// tambah parameter baru port_id (index 12)
			$row->param = $this->enc->encode($row->schedule_code . '|' . $row->schedule_id . '|' . $row->schedule_id_master . '|' . $row->ship_id . '|' . $row->dock_name . '|' . $real_date . '|' . $row->dock_id . '|' . $row->port_id);

			$row->url = site_url("stc/edit/" . $row->param);

			//disabled button
			if ($key == 0 || $row->real_sail_close_date) {
				$row->disabled = '';
			} else {
				$row->disabled = '';
			}

			if (empty($row->real_plot_date)) {
				$row->ship_name = $row->ship_name;
			} else if (!empty($row->real_plot_date) and empty($row->real_dock_date)) {
				$row->ship_name = $this->get_ship_name('app.t_trx_plot', $row->schedule_code)->ship_name;
			} else if (!empty($row->real_plot_date) and !empty($row->real_dock_date) and empty($row->real_open_boarding_date)) {
				$row->ship_name = $this->get_ship_name('app.t_trx_docking', $row->schedule_code)->ship_name;
			} else if (!empty($row->real_plot_date) and !empty($row->real_dock_date) and !empty($row->real_open_boarding_date) and empty($row->real_close_boarding_date)) {
				$row->ship_name = $this->get_ship_name('app.t_trx_open_boarding', $row->schedule_code)->ship_name;
			} else if (!empty($row->real_plot_date) and !empty($row->real_dock_date) and !empty($row->real_open_boarding_date) and !empty($row->real_close_boarding_date) and empty($row->real_ramp_door_date)) {
				$row->ship_name = $this->get_ship_name('app.t_trx_close_boarding', $row->schedule_code)->ship_name;
			} else if (!empty($row->real_plot_date) and !empty($row->real_dock_date) and !empty($row->real_open_boarding_date) and !empty($row->real_close_boarding_date) and !empty($row->real_ramp_door_date) and empty($row->real_sail_close_date)) {
				$row->ship_name = $this->get_ship_name('app.t_trx_close_ramp_door', $row->schedule_code)->ship_name;
			} else {
				$row->ship_name = $this->get_ship_name('app.t_trx_sail', $row->schedule_code)->ship_name;
			}

			unset($row->schedule_code);
			unset($row->schedule_id);

			$data[] = $row;
		}

		return $data;
	}

	// aksi transaksi ptc
	function trx_schedule()
	{
		$this->db->trans_start();
		$post   = $this->input->post();
		$param  = explode('|', $this->enc->decode($post['param']));
		$type   = $this->enc->decode($post['type']);
		$time   = date('Y-m-d H:i:s');
		$date_time = date('Y-m-d H:i:s');
		$ship = $this->enc->decode($post['ship']);

		// echo $ship; exit;

		//tabel name
		$t_plot = 'app.t_trx_plot';
		$t_docking = 'app.t_trx_docking';
		$t_ship = 'app.t_mtr_ship';
		$t_open_boarding = 'app.t_trx_open_boarding';
		$t_close_boarding = 'app.t_trx_close_boarding';
		$t_end_unload = 'app.t_trx_end_unload';
		$t_close_ramp = 'app.t_trx_close_ramp_door';
		$t_sail = 'app.t_trx_sail';

		// data untuk update
		$update_by_on = array(
			'updated_by' => $this->_user,
			'updated_on' => $date_time
		);

		// data untuk insert
		$default = array(
			'created_by' => $this->_user,
			'created_on' => $date_time,
			'ship_id' 	=> $ship,
			'status'  	=> 1
		);

		// ambil data master schedule untuk insert ke trx ptc dari masuk alur sampai berlayar
		$mtr_schedule = $this->db->select('
			port_id,
			dock_id,
			schedule_code,
			schedule_date
			')
			->where('id', $param[2])->get('app.t_mtr_schedule')
			->result_array();

		$insert = $mtr_schedule[0];
		$insert['ploting_date'] = $time;

		$where = array(
			'origin' => $insert['port_id'],
			'status' => 1
		);

		$insert['destination_port_id'] = $this->db->select('destination')->where($where)->get('app.t_mtr_rute')->row()->destination;

		// cek data ploting_code di app.t_trx_plot
		$plot = $this->db
			->select('plot_code')
			->where('schedule_code', $param[0])->get($t_plot)
			->row();

		if ($this->check_trx_schedule($mtr_schedule[0]['dock_id'], $param[0])) {
			return 2;
		} else {
			switch ($type) {

					//plot => insert table app.t_trx_plot
				case 1:
					$data_plot  = $mtr_schedule[0];

					// $substr 	= substr($data_plot['schedule_code'],0,8);
					// $data_plot['plot_code'] = $this->createCode($substr);

					if ($docking_ = $this->check_by_schedule_code($t_docking, $param[0])) {
						$default['status'] = 0;
						$this->update_trx_previous($t_docking, $param[0], $update_by_on);
						$data_plot['plot_code'] = $docking_->plot_code;
					} elseif ($open_boarding = $this->check_by_schedule_code($t_open_boarding, $param[0])) {
						$data_plot['plot_code'] = $open_boarding->plot_code;
					} else {
						$data_plot['plot_code'] = $this->createCode("J" . $data_plot['port_id'] . date("ymd") . "P", 1);
						$update['ship_id'] = $ship;
					}

					$this->db->insert($t_plot, array_merge($data_plot, $default));

					// update destination_port_id in t_trx_schedule berdasarkan ship class kapal
					$getShipClass = $this->db->query("select ship_class from app.t_mtr_ship where id = {$ship} ")->row();

					$qryGetDestination="SELECT 
											a.origin , 
											a.destination, 
											b.ship_class 
										from app.t_mtr_rute a
										join app.t_mtr_rute_class b on a.id = b.rute_id 
										where a.origin =".$insert['port_id']." and b.ship_class ={$getShipClass->ship_class} ";

					$getDestination = $this->db->query($qryGetDestination)->row();
					
					$updateDataDestination = array(
						"updated_by" => $this->session->userdata("username"),
						"updated_on" => date("Y-m-d H:i:s"),
						"destination_port_id" => $getDestination->destination
					);

					$this->db->where("schedule_code='".$insert['schedule_code']."' ");
					$this->db->update("app.t_trx_schedule", $updateDataDestination);

					$update['ploting_date'] = $time;
					break;

					//docking => insert table app.t_trx_docking
				case 2:
					$data_docking = $mtr_schedule[0];

					if ($open_boarding = $this->check_by_schedule_code($t_open_boarding, $param[0])) {
						//update status t_trx_plot
						$this->update_trx_previous($t_plot, $param[0], $update_by_on);
						$data_docking['plot_code'] = $open_boarding->plot_code;
						$default['status'] = 0;
					} elseif ($plot) {
						//update status t_trx_plot
						$this->update_trx_previous($t_plot, $param[0], $update_by_on);
						$data_docking['plot_code'] = $plot->plot_code;
					} else {
						$data_docking['plot_code'] = $this->createCode("J" . $data_docking['port_id'] . date("ymd") . "D", 2);
						$update['ship_id'] = $ship;
					}

					$this->db->insert($t_docking, array_merge($data_docking, $default));
					$update['docking_date'] = $time;
					break;

					//open boarding => insert table app.t_trx_open_boarding
				case 3:
					$data_open_boarding  = $mtr_schedule[0];
					$max_dock = $this->check_max_dock($mtr_schedule[0]['port_id'], $mtr_schedule[0]['schedule_date']);
					// var_dump($max_dock);exit;
					if ($max_dock === true) {
						return 3;
					}

					$data_shiftku = $this->get_shift($mtr_schedule[0]['port_id']);

					$data_open_boarding['shift_id'] = $data_shiftku->id;
					$data_open_boarding['shift_date'] = $data_shiftku->shift_date;
					$data_open_boarding['boarding_code'] = $this->boarding_code($mtr_schedule[0]['port_id']);

					// ambil data kapal
					$data_open_boarding['ship_class'] =  $this->db
						->select('ship_class')
						->where('id', $ship)->get($t_ship)
						->row()->ship_class;

					$data_end_unload = $mtr_schedule[0];

					if ($plot) {
						$data_end_unload['plot_code']  	 = $plot->plot_code;
						$data_open_boarding['plot_code'] = $plot->plot_code;

						$this->update_trx_previous($t_docking, $param[0], $update_by_on);
					} else {
						$plot_code_ob = $this->createCode("J" . $data_open_boarding['port_id'] . date("ymd") . "B", 3);

						$data_end_unload['plot_code'] 	 = $plot_code_ob;
						$data_open_boarding['plot_code'] = $plot_code_ob;
						$update['ship_id'] = $ship;
					}


					// handling agar tidak terjadi double data
					$querynya = $this->db->query(" select count(0) from " . $t_open_boarding . " where schedule_code='" . $param[0] . "' and status <>'-5' ")->row();

					if ($querynya->count < 1) {
						//update status t_trx_docking
						$this->db->insert($t_open_boarding, array_merge($data_open_boarding, $default)); // check disini
						$this->db->insert($t_end_unload, array_merge($data_end_unload, $default)); // check disini

					}

					$where1 = array(
						'dock_id' => $mtr_schedule[0]['dock_id'],
						'port_id' => $mtr_schedule[0]['port_id'],
						'status' => 1
					);
					// check data temp_bording_passanger
					$temp_passanger = $this->db->select('*')->where($where1)->get('app.t_temp_boarding_passanger')->result();

					// check data temp_bording_vehicle
					$temp_vehicle = $this->db->select('*')->where($where1)->get('app.t_temp_boarding_vehicle')->result();

					$boarding_code = $this->db
						->select('boarding_code')
						->where('schedule_code', $param[0])->get($t_open_boarding)
						->row()->boarding_code;

					if ($temp_passanger) {
						//memasukan data temp ke transaksi bording passanger
						foreach ($temp_passanger as $key => $value) {
							$data_trx_passanger = array(
								'dock_id' 		=> $value->dock_id,
								'port_id' 		=> $value->port_id,
								'schedule_date' => $mtr_schedule[0]['schedule_date'],
								// 'boarding_code' => $this->boarding_code($mtr_schedule[0]['port_id']),
								'boarding_code' => $boarding_code,
								'ticket_number' => $value->ticket_number,
								'status' 		=> $value->status,
								'created_by' 	=> $value->created_by,
								'created_on' 	=> $value->created_on,
								'updated_on' 	=> $value->updated_on,
								'updated_by' 	=> $value->updated_by,
								'terminal_code' => $value->terminal_code,
								'boarding_date' => $value->boarding_date,
								'ship_class' 	=> $value->ship_class,
							);

							$this->db->insert('app.t_trx_boarding_passanger', $data_trx_passanger);
						}

						// update status tempt jadi 2 jika sudh di pindahkan
						$update_temp_pass['status'] = 2;
						$this->db->where('status', 1);
						$this->db->where('dock_id', $mtr_schedule[0]['dock_id']);
						$this->db->where('port_id', $mtr_schedule[0]['port_id']);
						$this->db->update('app.t_temp_boarding_passanger', array_merge($update_temp_pass, $update_by_on));
					}

					if ($temp_vehicle) {
						//memasukan data temp ke transaksi bording vehicle
						foreach ($temp_vehicle as $key => $value) {
							$data_trx_vehicle = array(
								'dock_id' 		=> $value->dock_id,
								'port_id' 		=> $value->port_id,
								'schedule_date' => $mtr_schedule[0]['schedule_date'],
								// 'boarding_code' => $this->boarding_code($mtr_schedule[0]['port_id']),
								'boarding_code' => $boarding_code,
								'ticket_number' => $value->ticket_number,
								'status' 		=> $value->status,
								'created_by' 	=> $value->created_by,
								'created_on' 	=> $value->created_on,
								'updated_on' 	=> $value->updated_on,
								'updated_by' 	=> $value->updated_by,
								'terminal_code' => $value->terminal_code,
								'boarding_date' => $value->boarding_date,
								'ship_class' 	=> $value->ship_class,
							);

							$this->db->insert('app.t_trx_boarding_vehicle', $data_trx_vehicle);
						}

						// update status tempt jadi 2 jika sudh di pindahkan
						$update_temp_vehc['status'] = 2;
						$this->db->where('status', 1);
						$this->db->where('dock_id', $mtr_schedule[0]['dock_id']);
						$this->db->where('port_id', $mtr_schedule[0]['port_id']);
						$this->db->update('app.t_temp_boarding_vehicle', array_merge($update_temp_vehc, $update_by_on));
					}

					$update['open_boarding_date'] = $time;
					break;

					//close boarding => insert table app.t_trx_close_boarding
				case 4:
					$data_close_boarding  = $mtr_schedule[0];

					$data_close_boarding['plot_code'] = $plot->plot_code;
					// $data_close_boarding['boarding_code'] = $this->boarding_code($mtr_schedule[0]['port_id']);
					$data_close_boarding['boarding_code'] = $this->db
						->select('boarding_code')
						->where('schedule_code', $param[0])->get($t_open_boarding)
						->row()->boarding_code;

					$data_close_boarding['ship_class'] =  $this->db
						->select('ship_class')
						->where('id', $ship)->get($t_ship)
						->row()->ship_class;

					//update status t_trx_open_boarding
					// $this->update_trx_previous($t_open_boarding, $param[0], $update_by_on);

					// handling agar tidak terjadi double data
					$querynya = $this->db->query(" select count(0) from " . $t_close_boarding . " where schedule_code='" . $param[0] . "' and status <>'-5' ")->row();

					if ($querynya->count < 1) {

						$this->db->insert($t_close_boarding, array_merge($data_close_boarding, $default)); // validasi disini
					}

					$update['close_boarding_date'] = $time;
					break;

					//close ramp door => insert table app.t_trx_close_ramp_door
				case 5:
					$data_close_ramp  = $mtr_schedule[0];
					$data_close_ramp['plot_code'] = $plot->plot_code;

					//update status t_trx_close_boarding
					$this->update_trx_previous($t_open_boarding, $param[0], $update_by_on);
					$this->update_trx_previous($t_close_boarding, $param[0], $update_by_on);
					$this->db->insert($t_close_ramp, array_merge($data_close_ramp, $default));

					$update['close_ramp_door_date'] = $time;

					$row = $this->get_schecule($param);

					// update close gate => do insert t_trx_close_manless_gate 
					if ($row && $row['plot_code']) {
						$listTerminalCode = $this->get_list_manless($row['port_id'], $row['dock_id']);
						$common = array_merge($row, $default);
						$insertManless = [];
						foreach ($listTerminalCode as $val) {
							array_push($insertManless, array_merge($common, array('terminal_code' => $val->terminal_code)));
						}

						// print_r($insertManless);exit;
						if (!empty($insertManless)) {
							$this->db->insert_batch('app.t_trx_close_manless_gate', $insertManless);
						}
					}
					break;

					//sail => insert table app.t_trx_sail
				case 6:
					$data_sail  = $mtr_schedule[0];
					$data_sail['plot_code'] = $plot->plot_code;

					//update status t_trx_close_ramp_door
					$this->update_trx_previous($t_close_ramp, $param[0], $update_by_on);
					$this->db->insert($t_sail, array_merge($data_sail, $default));

					$update['sail_date'] = $time;
					break;
			}

			$table_name = 'app.t_trx_schedule';

			if (!$param[1]) {
				// ini fungsi hampir tidak di pakai karen kondisi jika tidak ada schedule code di t_trx_schedule maka insert ke t_trx_schedule
				$this->db->insert($table_name, array_merge($insert, $default));
			} else {
				if (isset($post['call'])) {
					$dock_id = $mtr_schedule[0]['dock_id'];
					$dock = $this->global_model->selectById('app.t_mtr_dock', 'id', $dock_id);

					$update['call'] = $post['call'];
					// $update['call_anchor'] = $post['call_anchor'];
					$update['ship_grt'] = $this->global_model->selectById('app.t_mtr_ship', 'id', $param[3])->grt;
					$update['dock_fare'] = $dock->fare;
					$update['tambat_fare'] = $dock->tambat_fare;
				}

				$this->db->where('id', $param[1]);
				$this->db->update($table_name, array_merge($update, $update_by_on));
			}
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return 0;
			} else {
				$this->db->trans_commit();
				return 1;
			}
		}
	}

	function insert_problem()
	{
		$post   = $this->input->post();
		$param  = explode('|', $this->enc->decode($post['param']));
		$type   = $this->enc->decode($post['type']);
		$ploting   = $this->enc->decode($post['ploting']);
		$ship = $this->enc->decode($post['ship']);

		//$param[0] code
		switch ($ploting) {
				//ploting 2 -> app.t_trx_plot
			case 2:
				$tbl = 'app.t_trx_plot';
				break;

				//ploting 3 -> app.t_trx_docking
			case 3:
				$tbl = 'app.t_trx_docking';
				break;

				//ploting 4 -> app.t_trx_open_boarding
			case 4:
				$tbl = 'app.t_trx_open_boarding';
				break;

				//ploting 5 -> app.t_trx_close_boarding
			case 5:
				$tbl = 'app.t_trx_close_boarding';
				break;

				//ploting 6 -> app.t_trx_close_ramp_door
			case 6:
				$tbl = 'app.t_trx_close_ramp_door';
				break;

			case 7:
				$tbl = 'app.t_trx_sail';
				break;
		}

		$schedules = $this->db->select('
			schedule_code,
			plot_code,
			port_id,
			dock_id,
			ship_id,
			schedule_date,
		')
			->where('schedule_code', $param[0])->get($tbl)
			->result_array();

		$date_time = date('Y-m-d H:i:s');
		$schedule = $schedules[0];
		$schedule['created_on'] = $date_time;
		$schedule['created_by'] = $this->_user;
		$schedule['status'] = 1;

		$data_shift = $this->get_shift($schedule['port_id']);
		$schedule['shift_id'] = $data_shift->id;
		$schedule['shift_date'] = $data_shift->shift_date;
		switch ($type) {
				//anchor
			case 2:
				$table = 'app.t_trx_anchor';
				$dock_id = $schedule['dock_id'];

				$update['call_anchor'] 	= 1;
				$update['ship_grt'] 	= $this->global_model->selectById('app.t_mtr_ship', 'id', $param[3])->grt;
				$update['dock_fare'] 	= $this->global_model->selectById('app.t_mtr_dock', 'id', $dock_id)->fare;
				break;

				//docking
			case 3:
				$table = 'app.t_trx_docking_maintenance';
				break;

				//broken
			case 4:
				$table = 'app.t_trx_broken';
				break;
		}

		$update['updated_by'] = $this->_user;
		$update['updated_on'] = $date_time;
		$update['status'] 	  = $type;

		$this->db->insert($table, $schedule);

		$this->db->where('id', $param[1]);
		$this->db->update('app.t_trx_schedule', $update);

		// default data untuk insert insert t_trx_close_manless_gate 
		$default = array(
			'created_by' => $this->_user,
			'created_on' => $date_time,
			'ship_id' 	=> $ship,
			'status'  	=> 1
		);

		$row = $this->get_schecule($param);

		// update close gate => do insert t_trx_close_manless_gate 
		if ($row && $row['plot_code']) {
			$listTerminalCode = $this->get_list_manless($row['port_id'], $row['dock_id']);
			$common = array_merge($row, $default);
			$insertManless = [];
			foreach ($listTerminalCode as $val) {
				array_push($insertManless, array_merge($common, array('terminal_code' => $val->terminal_code)));
			}

			if (!empty($insertManless)) {
				$this->db->insert_batch('app.t_trx_close_manless_gate', $insertManless);
			}
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return false;
		} else {
			$this->db->trans_commit();
			return true;
		}
	}

	function insert_problem2()
	{

		$post   = $this->input->post();
		// print_r($post);exit();
		$type   = $this->enc->decode($post['type']);
		$ship_id   = $this->enc->decode($post['ship_id']);
		$dock_id   = $this->enc->decode($post['dock_id']);

		$date_time = date('Y-m-d H:i:s');
		// $schedule = $schedules[0]; 
		$schedule['created_on'] = $date_time;
		$schedule['created_by'] = $this->_user;
		$schedule['status'] = 1;
		$schedule['ship_id'] = $ship_id;
		$schedule['port_id'] = $this->session->userdata('port_id');
		$schedule['dock_id'] = $dock_id;

		switch ($type) {
				//anchor
			case 2:
				$table = 'app.t_trx_anchor';
				// $dock_id = $schedule['dock_id'];

				$update['call_anchor'] 	= 1;
				$update['ship_grt'] 	= $this->global_model->selectById('app.t_mtr_ship', 'id', $ship_id)->grt;
				$update['dock_fare'] 	= $this->global_model->selectById('app.t_mtr_dock', 'id', $dock_id)->fare;
				break;

				//docking
			case 3:
				$table = 'app.t_trx_docking_maintenance';
				break;

				//broken
			case 4:
				$table = 'app.t_trx_broken';
				break;
		}

		$update['updated_by'] = $this->_user;
		$update['updated_on'] = $date_time;
		$update['status'] 	  = $type;

		$this->db->insert($table, $schedule);

		// $this->db->where('id', $param[1]);
		// $this->db->update('app.t_trx_schedule', $update);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return false;
		} else {
			$this->db->trans_commit();
			return true;
		}
	}

	// update trx sebelumnya
	function update_trx_previous($table, $where, $data)
	{
		$status['status'] = 0;
		$this->db->where('schedule_code', $where);
		$this->db->update($table, array_merge($status, $data));
	}

	// create ploting_code
	function createCode($param, $create_one)
	{
		$front_code = "P" . $param;
		$strLen 	= strlen($front_code);

		$sqlCheck = "SELECT
			b.plot_code, c.plot_code plot_code_docking, d.plot_code plot_code_boarding
		FROM
			app.t_trx_schedule a
		LEFT JOIN app.t_trx_plot b ON b.schedule_code = a.schedule_code AND b.status != -5
		LEFT JOIN app.t_trx_docking c ON c.schedule_code = a.schedule_code AND c.status != -5
		LEFT JOIN app.t_trx_open_boarding d ON d.schedule_code = a.schedule_code AND d.status != -5
		WHERE LEFT(b.plot_code, {$strLen}) = '{$front_code}' OR LEFT(c.plot_code, {$strLen}) = '{$front_code}' OR LEFT(d.plot_code, {$strLen}) = '{$front_code}' 
		LIMIT 1";

		if (!$this->db->query($sqlCheck)->row()) {
			return $front_code . "001";
		} else {
			$sqlMax = "SELECT
				MAX(CASE WHEN LEFT(b.plot_code, {$strLen}) = '{$front_code}' THEN b.plot_code END) plot_code,
				MAX(CASE WHEN LEFT(c.plot_code, {$strLen}) = '{$front_code}' THEN c.plot_code END) plot_code_dock,
				MAX(CASE WHEN LEFT(d.plot_code, {$strLen}) = '{$front_code}' THEN d.plot_code END) plot_code_boarding
			FROM
				app.t_trx_schedule a
			LEFT JOIN app.t_trx_plot b ON b.schedule_code = a.schedule_code AND b.status != -5
			LEFT JOIN app.t_trx_docking c ON c.schedule_code = a.schedule_code AND c.status != -5
			LEFT JOIN app.t_trx_open_boarding d ON d.schedule_code = a.schedule_code AND d.status != -5";

			$max = $this->db->query($sqlMax)->row();

			// saat ploting
			if ($create_one == 1) {
				$kode = $max->plot_code;
			}

			// saat docking
			elseif (($create_one == 2)) {
				$kode = $max->plot_code_dock;
			}

			// saat open boarding
			elseif (($create_one == 3)) {
				$kode = $max->plot_code_boarding;
			}

			$noUrut = (int) substr($kode, strlen($front_code), 3);

			$noUrut++;

			return $front_code . sprintf("%03s", $noUrut);
		}
	}

	//create baording_code
	function boarding_code($port)
	{
		$front_code = "B" . $port . "" . date('ymd');

		$chekCode = $this->db->query("select * from app.t_trx_open_boarding where left(boarding_code,8)='" . $front_code . "' ")->num_rows();

		if ($chekCode < 1) {
			$boarding_code = $front_code . "0001";
			return $boarding_code;
		} else {
			$max = $this->db->query("select max (boarding_code) as max_code from app.t_trx_open_boarding where left(boarding_code,8)='" . $front_code . "' ")->row();
			$kode = $max->max_code;
			$noUrut = (int) substr($kode, 8, 4);
			$noUrut++;
			$char = $front_code;
			$kode = $char . sprintf("%04s", $noUrut);
			return $kode;
		}
	}

	// nama kapal per trx
	function get_ship_name($table, $code)
	{
		$sql = "SELECT
				a.ship_id,
				b.name AS ship_name
			FROM
				{$table} a 
				LEFT JOIN app.t_mtr_ship b ON b.id = a.ship_id 
			WHERE
				schedule_code = '{$code}' AND a.status != -5";

		// return $this->db->query($sql)->row();
		return $this->dbView->query($sql)->row();
	}

	// list masalah anchor, docking, broken di tabel(app.t_trx_anchor, app.t_trx_docking_maintanancem, app.t_trx_broken)
	function get_list_problem($table)
	{
		$post = $this->input->post();
		$date = date('Y-m-d');

		$sql = "SELECT
				b.name AS ship_name,
				schedule_code,
				a.created_on				
		FROM
			{$table} a
		LEFT JOIN app.t_mtr_ship b ON b.id = a.ship_id
		WHERE a.port_id = {$this->enc->decode($post['port'])}
		-- AND a.schedule_date = '{$date}'
		AND a.created_on >= date_trunc('month', current_date-interval '1' month)
		AND a.status = 1
		ORDER BY a.id DESC";

		$data = array();
		// $query = $this->db->query($sql)->result();
		$query = $this->dbView->query($sql)->result();

		foreach ($query as $key => $row) {
			switch ($table) {
					//anchor
				case 'app.t_trx_anchor':
					$type = 2;
					break;

					//docking
				case 'app.t_trx_docking_maintenance':
					$type = 3;
					break;

					//broken
				case 'app.t_trx_broken':
					$type = 4;
					break;
			}
			$row->param = $this->enc->encode($row->schedule_code . '|' . $type);

			unset($row->schedule_code);
			$data[] = $row;
		}

		return $data;
	}

	// cek transaksi di satu dermaga tidak boleh lebih 1 trx
	function check_trx_schedule($dock_id, $schedule_code)
	{
		$sql = "SELECT
			trx.schedule_code, trx.status, dock_id
		FROM
			( SELECT status, schedule_code FROM app.t_trx_plot 
			UNION ALL 
			SELECT status, schedule_code FROM app.t_trx_docking
			UNION ALL 
			SELECT status, schedule_code FROM app.t_trx_open_boarding
			UNION ALL 
			SELECT status, schedule_code FROM app.t_trx_close_boarding
			UNION ALL 
			SELECT status, schedule_code FROM app.t_trx_close_ramp_door
			) trx
		JOIN app.t_trx_schedule s ON s.schedule_code = trx.schedule_code AND s.status = 1
		WHERE
			trx.status = 1 AND dock_id = $dock_id AND trx.schedule_code != '{$schedule_code}'";

		// return $this->db->query($sql)->result();
		return $this->dbView->query($sql)->result();
	}

	// ambil shift yang berjalan saat trx
	function get_shift($port_id = "")
	{
		$where_port = "";

		if ($port_id != "") {
			$where_port = " AND st.port_id = '$port_id'";
		}

		// QUERY LAWAS //

		// $sql = "SELECT
		// 			id,
		// 			CASE
		// 				WHEN night = TRUE AND CURRENT_TIME < shift_logout
		// 				THEN CURRENT_DATE - 1 
		// 				ELSE CURRENT_DATE
		// 			END shift_date
		// 		FROM
		// 			app.t_mtr_shift 
		// 		WHERE
		// 			status = 1
		// 			AND ((CURRENT_TIME BETWEEN shift_login AND shift_logout) OR ((CURRENT_TIME > shift_login OR CURRENT_TIME < shift_logout ) AND night = TRUE))";

		$sql = "SELECT
					shift.id,
					CASE
						WHEN st.night = TRUE AND CURRENT_TIME < st.shift_logout
						THEN CURRENT_DATE - 1 
						ELSE CURRENT_DATE
					END shift_date
				FROM
					app.t_mtr_shift shift
					JOIN app.t_mtr_shift_time st on st.shift_id = shift.id AND st.status = 1
				WHERE
					shift.status = 1 $where_port
					AND ((CURRENT_TIME BETWEEN st.shift_login AND st.shift_logout) OR ((CURRENT_TIME > st.shift_login OR CURRENT_TIME < st.shift_logout ) AND st.night = TRUE))";

		// $query = $this->db->query($sql);
		$query = $this->dbView->query($sql);

		if ($query->num_rows() == 1) {
			$return = $query->row();
		} else {
			$return = false;
		}

		return $return;
	}

	// pengecekam identitas aplikasi
	function cek_identity_app()
	{
		$this->db->select('*');
		$this->db->limit(1);
		$row = $this->db->get('app.t_mtr_identity_app')->row();

		if ($row) {
			$port = $row->port_id;
		} else {
			$port = -1;
		}

		return $port;
	}

	//cek di suatu dermaga apakah sudah melakukan pelayanan
	function check_open_boarding($schedule_code)
	{
		// $sql = "SELECT
		// 		a.id, a.boarding_code
		// 	FROM
		// 		app.t_trx_open_boarding a
		// 	LEFT JOIN app.t_trx_boarding_passanger b ON b.boarding_code = a.boarding_code
		// 	LEFT JOIN app.t_trx_boarding_vehicle c ON c.boarding_code = a.boarding_code
		// 	WHERE
		// 		schedule_code = '{$schedule_code}' AND (b.boarding_code = a.boarding_code OR c.boarding_code = a.boarding_code)
		// 	LIMIT 1";

		$sql = "SELECT
				id, boarding_code
			FROM
				app.t_trx_open_boarding
			WHERE
				schedule_code = '{$schedule_code}'
			LIMIT 1";

		if ($row = $this->db->query($sql)->row()) {
			return array(
				'status' => 'Sudah Mulai Pelayanan',
				'boarding_code' => $row->boarding_code
			);
		} else {
			return array();
		}
	}

	// cek di suatu dermaga apakah pelaynan nya sudah disetujui
	function check_approval($schedule_code)
	{
		$sql = "SELECT
				id 
			FROM
				app.t_trx_approval_ship_officer 
			WHERE
				schedule_code = '{$schedule_code}'
			LIMIT 1";

		// if ($this->db->query($sql)->row()) 
		if ($this->dbView->query($sql)->row()) {
			return 'Sudah disetujui';
		} else {
			return '';
		}
	}

	// cari imei validator
	function get_imei_validator($dock_id, $port_id)
	{
		$sql = "SELECT
				imei 
			FROM
				app.t_mtr_device_terminal
			WHERE
				terminal_type = 6 AND
				port_id = {$port_id} AND
				dock_id = {$dock_id} AND
				status = 1";

		$data = array();
		// foreach ($this->db->query($sql)->result() as $row) 
		foreach ($this->dbView->query($sql)->result() as $row) {
			$data[] = $row->imei;
		}

		return $data;
	}

	// ambil semua data gate boarding untuk dropdown buka & tutup gate
	function dropdown_gate($port_id, $dock_id)
	{
		$sql = "SELECT terminal_code, terminal_name 
        FROM app.t_mtr_device_terminal 
        WHERE terminal_type IN (8) AND port_id = {$port_id} AND dock_id = {$dock_id} AND status = 1";

		// $data = array('' => '');
		// $result = $this->db->query($sql)->result();
		$result = $this->dbView->query($sql)->result();
		if ($result) {
			foreach ($result as $row) {
				$data[$row->terminal_code] = strtoupper($row->terminal_name);
			}
		} else {
			$data = array('' => '');
		}

		return $data;
	}

	function gate($port_id, $dock_id)
	{
		$sql = "SELECT terminal_code 
        FROM app.t_mtr_device_terminal 
        WHERE terminal_type IN (8) AND port_id = {$port_id} AND dock_id = {$dock_id} AND status = 1";

		// $data = $this->db->query($sql)->row();
		$data = $this->dbView->query($sql)->row();

		return $data;
	}

	function listGate($port_id)
	{
		$where = "";
		if ($port_id) {
			$where = " AND port_id = {$port_id}";
		}
		$sql = "SELECT terminal_code 
        FROM app.t_mtr_device_terminal 
        WHERE terminal_type IN (8) AND status = 1 {$where} ";

		// $data = $this->db->query($sql)->result();
		$data = $this->dbView->query($sql)->result();

		return $data;
	}

	// cek plot code di suatu tabel pada trx pertama (masuk alur, sandar, atau mulai pelayanan)
	function check_by_schedule_code($table, $code)
	{
		return $this->db
			->select('plot_code')
			->where('schedule_code', $code)->get($table)
			->row();
	}

	function get_schecule($param)
	{
		$querySchedule = "SELECT
			a.schedule_date,
			a.schedule_code,
			a.port_id,
			a.dock_id,
			a.ship_id,
			CASE 
				WHEN b.plot_code IS NOT NULL THEN b.plot_code
				WHEN c.plot_code IS NOT NULL THEN c.plot_code 
				WHEN d.plot_code IS NOT NULL THEN d.plot_code 
			END plot_code
		FROM
			app.t_trx_schedule a
		LEFT JOIN app.t_trx_plot b ON b.schedule_code = a.schedule_code
		LEFT JOIN app.t_trx_docking c ON c.schedule_code = a.schedule_code
		LEFT JOIN app.t_trx_open_boarding d ON d.schedule_code = a.schedule_code
		WHERE
			a.id = {$param[1]}";

		// $row = $this->db->query($querySchedule)->result_array()[0];
		$row = $this->dbView->query($querySchedule)->result_array()[0];
		return $row;
	}

	function gate_action($param)
	{
		$row = $this->get_schecule($param);

		if ($row && $row['plot_code']) {

			$this->db->trans_start();

			$row['terminal_code'] = $this->input->post('terminal_code');
			$row['created_on'] = date('Y-m-d H:i:s');
			$row['created_by'] = $this->_user;
			$row['status'] = 1;

			if ($this->input->post('code')) {
				$this->db->insert('app.t_trx_open_manless_gate', $row);
			} else {
				$this->db->insert('app.t_trx_close_manless_gate', $row);
			}

			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				return 0;
			} else {
				$this->db->trans_commit();
				return 1;
			}
		} else {
			return 2;
		}
	}

	// function createCode($param){
	// 	$front_code="P".$param;

	// 	$chekCode=$this->db->query("select * from app.t_trx_plot where left(plot_code,9)='".$front_code."' ")->num_rows();

	// 	if($chekCode<1)
	// 	{
	// 		$plotCode=$front_code."001";
	// 		return $plotCode;
	// 	}
	// 	else
	// 	{
	// 		$max=$this->db->query("select max (plot_code) as max_code from app.t_trx_plot where left(plot_code,9)='".$front_code."' ")->row();
	// 		$kode=$max->max_code;
	// 		$noUrut = (int) substr($kode, 9, 3);
	// 		$noUrut++;
	// 		$char = $front_code;
	// 		$kode = $char . sprintf("%03s", $noUrut);
	// 		return $kode;
	// 	}
	// }

	// function createCode($param){
	// 	$front_code="P".$param;

	// 	$chekCode=$this->db->query("select * from app.t_trx_plot where left(plot_code,".strlen($front_code).")='".$front_code."' ")->num_rows();

	// 	//$chekCode=$this->db->query("select * from app.t_trx_plot where left(schedule_code,".strlen($front_code).")='".$front_code."' ")->num_rows();


	// 	if($chekCode<1)
	// 	{
	// 		$plotCode=$front_code."001";
	// 		return $plotCode;
	// 	}
	// 	else
	// 	{
	// 		$max=$this->db->query("select max (plot_code) as max_code from app.t_trx_plot where left(plot_code,".strlen($front_code).")='".$front_code."' ")->row();
	// 		$kode=$max->max_code;
	// 		$noUrut = (int) substr($kode, strlen($front_code), 3);
	// 		$noUrut++;
	// 		$char = $front_code;
	// 		$kode = $char . sprintf("%03s", $noUrut);
	// 		return $kode;
	// 	}
	// }

	function check_gate($table, $terminal_code)
	{
		$sql = "SELECT max(created_on) AS created_on FROM {$table} WHERE status = 1 AND terminal_code = '{$terminal_code}'";
		// $result = $this->db->query($sql)->row();
		$result = $this->dbView->query($sql)->row();
		return $result;
	}

	function get_list_manless_10112020($port_id, $dock_id = "")
	{
		$dock = "";
		if ($dock_id != "") {
			$dock = " and a.dock_id = {$dock_id} ";
		}
		// $sql = "SELECT
		// 				a.terminal_code,
		// 				a.terminal_name,
		// 				max(b.created_on) as last_open,
		// 				max(c.created_on) as last_close
		// 			from
		// 				app.t_mtr_device_terminal a
		// 			left join app.t_trx_open_manless_gate b on
		// 				a.terminal_code = b.terminal_code
		// 				and b.status = 1
		// 			left join app.t_trx_close_manless_gate c on
		// 				a.terminal_code = c.terminal_code
		// 				and c.status = 1
		// 			where
		// 				terminal_type in (8)
		// 				and a.port_id = {$port_id} 
		// 				{$dock}
		// 				and a.status = 1					
		// 			group by
		// 				a.terminal_code,
		// 				a.terminal_name";

		$sql = "SELECT
						a.terminal_code,
						a.terminal_name,
						max(b.created_on) as last_open,
						max(c.created_on) as last_close
					from
						app.t_mtr_device_terminal a
					left join 
						( select * from app.t_trx_open_manless_gate 
							order by created_on desc limit 1
						) b on a.terminal_code = b.terminal_code and b.status = 1
					left join 
					(
						select * from app.t_trx_close_manless_gate 
						order by created_on desc limit 1
					) c on
						a.terminal_code = c.terminal_code
						and c.status = 1
					where
						terminal_type in (8)
						and a.port_id = {$port_id} 
						{$dock}
						and a.status = 1					
					group by
						a.terminal_code,
						a.terminal_name";




		$data = $this->db->query($sql)->result();

		return $data;
	}

	function get_list_manless($port_id, $dock_id = "")
	{
		$previous_week = strtotime("-7 day");
		$start_date = date("Y-m-d", $previous_week);
		$end_date = date("Y-m-d");

		$dock = "";
		if ($dock_id != "") {
			$dock = " and a.dock_id = {$dock_id} ";
		}

		$sql = "SELECT
						a.terminal_code,
						a.terminal_name,
						max(b.created_on) as last_open,
						max(c.created_on) as last_close
					from
						app.t_mtr_device_terminal a
					left join (
						select created_on from app.t_trx_open_manless_gate 
						where dock_id = {$dock_id} and schedule_date between '{$start_date}' and '{$end_date}'
					) b on a.terminal_code = b.terminal_code and b.status = 1
					left join (
						select created_on from app.t_trx_close_manless_gate 
						where dock_id = {$dock_id} and schedule_date between '{$start_date}' and '{$end_date}'
					) c on a.terminal_code = c.terminal_code
						and c.status = 1
					where
						terminal_type in (8)
						and a.port_id = {$port_id} 
						{$dock}
						and a.status = 1					
					group by
						a.terminal_code,
						a.terminal_name";

		$sql_new = "SELECT
									a.terminal_code
									, a.terminal_name
									, b.created_on AS last_open
									, c.created_on AS last_close
								FROM
									app.t_mtr_device_terminal a
								LEFT JOIN LATERAL (
									SELECT
										*
									FROM
										app.t_trx_open_manless_gate
									WHERE
										terminal_code = a.terminal_code
									ORDER BY
										created_on DESC
									LIMIT 1 ) b ON
									TRUE
								LEFT JOIN LATERAL (
									SELECT
										*
									FROM
										app.t_trx_close_manless_gate
									WHERE
										terminal_code = a.terminal_code
									ORDER BY
										created_on DESC
									LIMIT 1 ) c ON
									TRUE
								WHERE
									terminal_type IN (8)
									AND a.port_id = {$port_id} 
									{$dock}
									AND a.status = 1
								ORDER BY
									a.created_on";

		$data = $this->db->query($sql_new)->result();

		return $data;
	}

	function get_ship_class_id($ship_id)
	{
		$sql = "SELECT
				ship_class
			FROM
				app.t_mtr_ship
			WHERE
				id = {$ship_id} AND status != -5";

		return $this->dbView->query($sql)->row();
	}
	
	function get_ship_class_id_by_dock($dock_id)
	{
		$sql = "SELECT
				ship_class_id as ship_class
			FROM
				app.t_mtr_dock
			WHERE
				id = {$dock_id} AND status != -5";

		return $this->dbView->query($sql)->row();
	}		

	function check_max_dock($port_id, $schedule_date) {
		$max = $this->db->select('
														param_name,
														param_value
														')
														->where('param_name', 'max_open_boarding_ptc')->get('app.t_mtr_custom_param_pids')
														->result_array();
		$query = "SELECT
									*
							from
									app.t_trx_open_boarding
							where
									port_id = $port_id
									and schedule_date = '$schedule_date'
									and status = 1";
		$count = $this->db->query($query)->num_rows();
		// echo $count;exit;
		if ($count >= $max[0]['param_value']) {
			return true;
		}

		return false;
	}


}
