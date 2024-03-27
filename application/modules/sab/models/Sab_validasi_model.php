<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sab_validasi_model extends MY_Model{

	public function __construct() {
		parent::__construct();
				$this->_module = 'sab';
				$this->load->model('quota_model', 'quota');
	}


	function isValidDate(string $date, string $format = 'Y-m-d'): bool
	{
			$dateObj = DateTime::createFromFormat($format, $date);
			if ($date == '24:00:00') {
					return true;
			}
			else {
					return $dateObj && $dateObj->format($format) == $date;
			}
	}

	public function validate_booking_date($data, $module, $config_param) {

			//VALIDASI TANGGAL DAN JAM TIDAK ERROR
			if ($this->isValidDate($data['depart_date']) === false) {
				$response = array(
						"code" => 131,
						"message" => 'Gagal, invalid tanggal keberangkatan.',
						"data" => null,
				);
				echo json_encode($response);
				$created_by   = $this->session->userdata('username');
				$log_url      = site_url() . '' . $module;
				$log_method   = 'POST';
				$log_param    = json_encode($data['depart_date']);
				$log_response = json_encode($response);

				$this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
				exit;
			}

			if ($this->isValidDate($data['depart_time_start'], 'H:i:s') === false || $this->isValidDate($data['depart_time_end'], 'H:i:s') === false)    {
				$response = array(
						"code" => 131,
						"message" => 'Gagal, invalid waktu keberangkatan.',
						"data" => null,
				);
				echo json_encode($response);
				$created_by   = $this->session->userdata('username');
				$log_url      = site_url() . '' . $module;
				$log_method   = 'POST';
				$log_param    = json_encode($data['depart_time']);
				$log_response = json_encode($response);

				$this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
				exit;
			}

			//VALIDASI WAKTU KEBERANGKATAN SESUAI CONFIG PARAM
			$config_param = (object) $config_param;
			$max       = date('Y-m-d H:i', strtotime("+ " . $config_param->max_booking . " day"));
			$max_value = $config_param->max_booking;
			$hour      = ($config_param->min_booking * 24) + $config_param->booking_time;
			$min       = date('Y-m-d H:00', strtotime("+ " . $hour . " hour"));

			$min_value   = $config_param->min_booking;
			$depart_date = $data['depart_date'] . ' ' . $data['depart_time_start'];
			if ($max < $depart_date) {
					$response = [
							"code"    => 131,
							// "message" => 'Gagal. Pemesanan maksimal ' . $max_value . ' hari sebelum keberangkatan',
							"message" => 'Gagal. pemesanan tiket dapat dilakukan ' . $max_value . ' Hari sebelum Jadwal Masuk Pelabuhan',
							"data"    => null,
					];
					echo json_encode($response);
					/* Fungsi Create Log */
					$created_by   = $this->session->userdata('username');
					$log_url      = site_url() . '' . $module;
					$log_method   = 'POST';
					$log_param    = json_encode($depart_date);
					$log_response = json_encode($response);

					$this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
					exit();
			}
			// echo json_encode($depart_date);exit();
			if ($min > $depart_date) {
					$response = [
							"code"    => 131,
							// "message" => 'Gagal. Pemesanan minimal ' . $hour . ' jam sebelum keberangkatan',
							"message" => 'Gagal, pemesanan tiket dapat dilakukan selambat-
				lambatnya ' . $hour . ' Jam sebelum Jadwal Masuk Pelabuhan',
							"data"    => null,
					];
					echo json_encode($response);
					/* Fungsi Create Log */
					$created_by   = $this->session->userdata('username');
					$log_url      = site_url() . '' . $module;
					$log_method   = site_url() . '' . $module;
					$log_param    = json_encode($depart_date);
					$log_response = json_encode($response);

					$this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
					exit();
			}

			//VALIDASI RANGE TIME
			$depart_date_start1 = $data['depart_date']." ".$data['depart_time_start'];
			$depart_date_end1   = $data['depart_date']." ".$data['depart_time_end'];

			$depart_date_start2 = new DateTime($depart_date_start1);
			$depart_date_start  = $depart_date_start2->format('Y-m-d H:i:s');

			$depart_date_end2   = new DateTime($depart_date_end1);
			$depart_date_end    = $depart_date_end2->format('Y-m-d H:i:s');
			if ($data['ship_class'] == 2) {
					if ($depart_date_start != $depart_date_end) {
							$response = array(
									"code"    => 131,
									"message" => 'Gagal, invalid waktu keberangkatan',
									"data"    => null,
							);
							echo json_encode($response);
							/* Fungsi Create Log */
							$created_by   = $this->session->userdata('username');
							$log_url      = site_url() . '' . $module;
							$log_method   = site_url() . '' . $module;
							$log_param    = json_encode($depart_date_end);
							$log_response = json_encode($response);

							$this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
							exit();
					}
					
			}
			else{
					$range_time = (strtotime($depart_date_end) - strtotime($depart_date_start)) /60;
					if ($range_time != 60) {
						$response = array(
								"code"    => 131,
								"message" => 'Gagal, invalid waktu keberangkatan',
								"data"    => null,
						);
						echo json_encode($response);
						/* Fungsi Create Log */
						$created_by   = $this->session->userdata('username');
						$log_url      = site_url() . '' . $module;
						$log_method   = site_url() . '' . $module;
						$log_param    = json_encode($depart_date_end);
						$log_response = json_encode($response);

						$this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
						exit();
					}
			}
	}

	public function get_ship_schedule_reguler($origin, $depart_date, $ship_class, $depart_time_start, $depart_time_end, $config_param)
	{
			$config_param = (object) $config_param;
			$port_id            = $origin;
			$schedule_date      = $this->db->escape($depart_date);
			$depart_time        = $this->db->escape($depart_time_start);
			$limit              = $config_param->ship_schedule_limit;
			$date_start 				= $depart_date . ' ' . $depart_time_start;
			$date_end   				= $depart_date . ' ' . $depart_time_end;

			// $date_start1 = $depart_date . ' ' . $depart_time_start;
			// $date_end1   = $depart_date . ' ' . $depart_time_end;
			// $date1 = new DateTime($date_start1);
			// $date2 = new DateTime($date_end1);
			// $date_start = $date1->format('Y-m-d H:i:s');
			// $date_end   = $date2->format('Y-m-d H:i:s');
			$sql = "SELECT sch.schedule_code,ship.name as ship_name, sch.schedule_date as depart_date,sch.sail_time::time as depart_time
							FROM app.t_mtr_schedule sch 
							LEFT JOIN app.t_mtr_ship ship on sch.ship_id = ship.id
							WHERE sch.port_id = {$port_id} 
							-- AND sch.schedule_date >= {$schedule_date}
							AND sch.ship_class = {$ship_class}
							AND sch.status = 1  
							AND sch.sail_time >=    '{$date_start}'   and sch.sail_time < '{$date_end}'
							order by sch.schedule_date,sch.sail_time, sch.id asc limit {$limit};";
			return $this->db->query($sql)->result();
	}


	public function get_ship_schedule_eksekutif($origin, $depart_date, $ship_class, $depart_time_start, $config_param)
	{
			$config_param = (object) $config_param;
			$port_id            = $origin;
			$schedule_date      = $this->db->escape($depart_date);
			$depart_time        = $this->db->escape($depart_time_start);
			$limit              = $config_param->ship_schedule_limit;
			$date_start 				= $depart_date . ' ' . $depart_time_start;
			$date_end   				= date('Y-m-d H:i:s', strtotime('+ 1 day', strtotime($date_start)));

			// $date_start1 = $depart_date . ' ' . $depart_time_start;
			// $date_end1   = $depart_date . ' ' . $depart_time_end;
			// $date1 = new DateTime($date_start1);
			// $date2 = new DateTime($date_end1);
			// $date_start = $date1->format('Y-m-d H:i:s');
			// $date_end   = $date2->format('Y-m-d H:i:s');

			$sql = "SELECT sch.schedule_code,ship.name as ship_name, sch.schedule_date as depart_date,sch.sail_time::time as depart_time
							FROM app.t_mtr_schedule sch 
							LEFT JOIN app.t_mtr_ship ship on sch.ship_id = ship.id
							WHERE sch.port_id = {$port_id} 
							-- AND sch.schedule_date >= {$schedule_date}
							AND sch.ship_class = {$ship_class}
							AND sch.status = 1  
							AND sch.sail_time =    '{$date_start}' 
							order by sch.schedule_date,sch.sail_time, sch.id asc limit 1 ;";
			return $this->db->query($sql)->result();
	}


	public function validate_vehicle_class_inactivated($port_id, $ship_class, $vehicle_class_id, $depart_date)
	{
			$depart_date      = $this->db->escape($depart_date);

			$sql = "SELECT * from app.t_mtr_vehicle_class_inactivated tmvci 
					where end_date >= {$depart_date} and start_date <= {$depart_date}
					AND port_id = {$port_id} 
					and vehicle_class_id = {$vehicle_class_id} 
					and ship_class =  {$ship_class} 
					and web_admin is true 
					and status = 1 ";
			return $this->db->query($sql)->row();
	}


	public function get_vehicle_class_by_id($port_id, $ship_class, $vehicle_class_id)
	{
		$sql = "SELECT class.id, class.name, type.name as type, min_length, max_length,adult_capacity,child_capacity,infant_capacity,max_capacity,description, class.image_url
						FROM app.t_mtr_vehicle_class class 
						JOIN app.t_mtr_vehicle_type type ON class.type = type.id 
						JOIN app.t_mtr_vehicle_class_activated pvc ON pvc.vehicle_class = class.id AND pvc.status = 1 
						WHERE class.status = 1 
						AND pvc.port_id = {$port_id} 
						AND pvc.ship_class = {$ship_class} 
						AND pvc.vehicle_class = {$vehicle_class_id}
						AND web_admin is true 
						ORDER BY 1 ASC";
		return $this->db->query($sql)->row();
	}

	public function validate_pedestrian_inactivated($port_id, $ship_class,$depart_date) {
		$depart_date      = $this->db->escape($depart_date);

		$sql = "SELECT * from app.t_mtr_pedestrian_inactivated tmvci 
						where end_date > {$depart_date} and start_date <= {$depart_date}
						AND port_id = {$port_id}
						and ship_class =  {$ship_class}
						and web_admin is true 
						and status = 1 ";
		return $this->db->query($sql)->row();
	}


	public function validate_schedule($data, $module, $config_param) {
		$origin							= $data['origin'];
		$depart_date				= $data['depart_date'];
		$depart_time_start	= $data['depart_time_start'];
		$depart_time_end		= $data['depart_time_end'];
		$ship_class					= $data['ship_class'];
		$service						= $data['service'];


		if ($ship_class == 2) {
			$ship_schedule = $this->get_ship_schedule_eksekutif($origin, $depart_date, $ship_class, $depart_time_start, $config_param);
		} else {
				$ship_schedule = $this->get_ship_schedule_reguler($origin, $depart_date, $ship_class, $depart_time_start, $depart_time_end, $config_param);
		}

		// print_r($ship_schedule);exit;
		if (!$ship_schedule) {
				$response = array(
						'code'    => 131,
						'message' => 'GAGAL. JADWAL TIDAK TERSEDIA',
						'data'    => null
				);
				echo json_encode($response);
				$created_by   = $this->session->userdata('username');
				$log_url      = site_url() . '' . $this->_module;
				$log_method   = site_url() . '' . $this->_module;
				$log_param    = json_encode($depart_date);
				$log_response = json_encode($response);

				$this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
				exit();
		}
	}

	public function validate_pembatasan_golongan($data, $module, $config_param) {
		$origin							= $data['origin'];
		$depart_date				= $data['depart_date'];
		$depart_time_start	= $data['depart_time_start'];
		$depart_time_end		= $data['depart_time_end'];
		$ship_class					= $data['ship_class'];
		$service						= $data['service'];
		$vehicle_class_id		= isset($data['vehicle_type']) ? $data['vehicle_type'] : "";


		if ($service == 2) {
				$inactiv = $this->validate_vehicle_class_inactivated($origin, $ship_class, $vehicle_class_id, $depart_date . " " . $depart_time_start);
				if ($inactiv) {
						$response = array(
								'code'    => 131,
								// 'message' => 'GAGAL. JADWAL TIDAK TERSEDIA',
								'message' => 'GAGAL. GOLONGAN SEDANG DI INACTIVE',
								'data'    => null
						);

						echo json_encode($response);
						$created_by   = $this->session->userdata('username');
						$log_url      = site_url() . '' . $this->_module;
						$log_method   = site_url() . '' . $this->_module;
						$log_param    = json_encode($data);
						$log_response = json_encode($response);

						$this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
						exit();
				}
				$inactiv2 = $this->get_vehicle_class_by_id($origin, $ship_class, $vehicle_class_id);
				if (!$inactiv2) {
						$response = array(
								'code'    => 131,
								// 'message' => 'GAGAL. JADWAL TIDAK TERSEDIA',
								'message' => 'GAGAL. GOLONGAN TIDAK DI BUKA',
								'data'    => null
						);

						echo json_encode($response);
						$created_by   = $this->session->userdata('username');
						$log_url      = site_url() . '' . $this->_module;
						$log_method   = site_url() . '' . $this->_module;
						$log_param    = json_encode($data);
						$log_response = json_encode($response);

						$this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
						exit();
				}
		}
		else {
			$inactiv_pedes = $this->validate_pedestrian_inactivated($origin, $ship_class, $depart_date . " " . $depart_time_start);
			if ($inactiv_pedes) {
				$response = array(
						'code'    => 131,
						'message' => 'GAGAL. JADWAL TIDAK TERSEDIA',
						'data'    => null
				);

				echo json_encode($response);
				$created_by   = $this->session->userdata('username');
				$log_url      = site_url() . '' . $this->_module;
				$log_method   = site_url() . '' . $this->_module;
				$log_param    = json_encode($data);
				$log_response = json_encode($response);

				$this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
				exit();
		}
		}
	}

	function validate_booking_quota_pcm($json, $module)
	{
		$quota_exist = $this->quota->get_booking_quota_pcm($json);
		// print_r($quota_exist);exit;
		if (!$quota_exist) {
			$master_quota = $this->quota->get_quota_pcm2($json);
			if ($master_quota) {
				$origin            = $json['origin'];
				$depart_date       = $json['depart_date'];
				$service_id        = $json['service'];
				$ship_class        = $json['ship_class'];
				$depart_time_start = date("H:00:00", strtotime($json['depart_time_start']));
				$max = 32767;
				if ($json['service'] == 2) {
					$reserved = $this->quota->get_quota_pcm_reserved($json);
					// print_r($master_quota);exit;
					$total_reserved1 = 0;
					$total_reserved2 = 0;
					if ($reserved) {
						$total_reserved1 = $total_reserved1 + $reserved->total_quota;
					}
					$total_booking = 1;
					$total_quota = ($master_quota->param_value - ($master_quota->passanger + $total_reserved1));
					$quota_limit = ($max - ($master_quota->param_value - ($master_quota->passanger + $total_reserved1)));
					$data_quota =  array(
						'port_id'        => $origin,
						'ship_class'     => $ship_class,
						'depart_date'    => $depart_date,
						'depart_time'    => $depart_time_start,
						'created_by'     => 'system',
						'quota'          => $master_quota->param_value,
						'total_quota'    => $total_quota,
						'used_quota'     => $master_quota->passanger,
						'quota_limit'    => $quota_limit,
						'quota_reserved' => $total_reserved1,
						'total_lm'       => $master_quota->total_lm,
						'used_lm'        => $master_quota->used_quota->used_lm
					);

					try {
						$insert_quota = $this->quota->insert_quota_pcm_vehicle($data_quota);
					} catch (Exception $e) {
						$insert_quota = null;
					}

					$quota_exist = $this->quota->get_booking_quota_pcm($json);
					$total_quota_exist = $quota_exist->total_quota + $quota_exist->total_quota_reserved;

					if ($total_booking > $total_quota_exist) {
						return null;
					} else {
						$available_quota =  array(
							'quota'                    => $total_quota_exist,
							'used_quota'               => $master_quota->passanger,
							'available_quota'          => $total_quota_exist,
							'id'                       => $quota_exist->id,
							'reserved_id'              => $quota_exist->quota_reserved_id,
							'available_quota_reserved' => $quota_exist->total_quota_reserved
						);
						return $available_quota;
					}
				} else {
					$total_booking = $json['total_booking_quota'];
					$data_quota =  array(
						'port_id'      => $origin,
						'ship_class'   => $ship_class,
						'depart_date'  => $depart_date,
						'depart_time'  => $depart_time_start,
						'created_by'   => 'system',
						'quota'        => $master_quota->param_value,
						'total_quota'  => ($master_quota->param_value - $master_quota->passanger),
						'used_quota'   =>  $master_quota->passanger,
						'quota_limit'  => ($max - ($master_quota->param_value - $master_quota->passanger))
					);


					try {
						$insert_quota = $this->quota->insert_quota_passenger($data_quota);
					} catch (Exception $e) {
						$insert_quota = null;
					}
					if ($insert_quota) {
						$id_quota = $insert_quota;
					} else {
						$id_quota = $this->quota->get_booking_quota_pcm($json)->id;
					}

					if ($total_booking > ($master_quota->param_value - $master_quota->passanger)) {
						$response = array(
							"code"    => 134,
							"message" => 'KUOTA TIKET SUDAH HABIS',
							"data"    => null,
						);
						return null;
					} else {
						$available_quota =  array(
							'quota'           => $master_quota->param_value,
							'used_quota'      => $master_quota->passanger,
							'available_quota' => ($master_quota->param_value -  $master_quota->passanger),
							'id'              =>  $id_quota,
						);
						return $available_quota;
					}
				}
			} else {
				$response = array(
					"code"    => 0,
					"message" => 'KUOTA TIKET BELUM TERSEDIA',
					"data"    => null,
				);
				echo json_encode($response);
				exit();
			}
		} else {
			if ($json['service'] == 2) {
				$total_booking = 1;
				$total_quota_exist = $quota_exist->total_quota + $quota_exist->total_quota_reserved;
				if ($total_booking > $total_quota_exist) {
					return null;
				} else {
					$available_quota =  array(
						'quota'           => $total_quota_exist,
						'used_quota'      => $quota_exist->used_quota,
						'available_quota' => $total_quota_exist,
						'id'              => $quota_exist->id,
						'reserved_id'     => $quota_exist->quota_reserved_id,
						'available_quota_reserved'     => $quota_exist->total_quota_reserved
					);
					return $available_quota;
				}
			} else {
				$total_booking = $json['total_booking_quota'];
				if ($total_booking > $quota_exist->total_quota) {
					return null;
				} else {
					$available_quota =  array(
						'quota'           => $quota_exist->quota,
						'used_quota'      => $quota_exist->used_quota,
						'available_quota' =>  $quota_exist->total_quota,
						'id'              =>  $quota_exist->id,
					);
					return $available_quota;
				}
			}
		}
	}



	function booking_quota_pcm($json, $quota, $try = null)
	{
		if (($json['service'] == 2)  && ($quota['reserved_id']) && ($quota['available_quota_reserved'] >= 1)) {
			try {
				$booked = $this->quota->booking_quota_reserved_pcm($json, $quota);
			} catch (Exception $e) {
				$booked = null;
			}
			if ($booked) {
				return 2;
			}
			else{
				return null;
			}
			
		} else {
			
			try {
				$booked = $this->quota->booking_quota_pcm($json, $quota);
			} catch (Exception $e) {
				$booked = null;
			}
			if ($booked) {
				return 1;
			}
			else{
				return null;
			}
		}
	}

}
