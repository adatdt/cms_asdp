<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quota_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'sab';
	}


	public function get_booking_quota_pcm($data) {
			$origin            = $data['origin'];
			$depart_date       = $this->db->escape($data['depart_date']);
			$service_id        = $data['service'];
			$ship_class        = $data['ship_class'];
			// quota perjam
			$depart_time_start = $this->db->escape(date("H:00:00", strtotime($data['depart_time_start'])));
			$end_time          = date("H:00:00", strtotime($data['depart_time_start'].'+1 hours'));
			$depart_time_end   = ($end_time === '00:00:00') ? $this->db->escape('24:00:00') : $this->db->escape($end_time);
			
			if($service_id == 2){
				$vehicle_class = $data['vehicle_type'];   
								
								$sql2 = "SELECT 
										q.id
										,q.quota as param_value
										,q.used_quota
										,q.total_quota
										,q.quota_limit
										,res.id as quota_reserved_id
										,res.vehicle_class_id
										,coalesce(res.quota,0) as quota_reserved
										,coalesce(res.used_quota,0) as used_quota_reserved
										,coalesce(res.total_quota,0) as total_quota_reserved
										,coalesce(res.quota_limit,0) as quota_limit_reserved
										from app.t_trx_quota_pcm_vehicle q  
										left join app.t_trx_quota_pcm_vehicle_reserved res  
										ON q.ship_class = res.ship_class 
										AND q.port_id = res.port_id
										AND q.depart_date = res.depart_date 
										AND q.depart_time = res.depart_time 
										AND res.vehicle_class_id = {$vehicle_class}
										WHERE  q.ship_class = {$ship_class} AND q.port_id = {$origin} and q.status = 1 
										AND q.depart_date = {$depart_date} AND q.depart_time = {$depart_time_start} 

										";
								$quota = $this->db->query($sql2)->row();

									return $quota;
								

			}
			else if($service_id == 1){
							$sql = "SELECT
										*
										from app.t_trx_quota_passanger q  
										WHERE  q.ship_class = {$ship_class} AND q.port_id = {$origin} and q.status = 1 
										AND q.depart_date = {$depart_date} AND q.depart_time = {$depart_time_start}
										";
						$quota = $this->db->query($sql)->row();
						return $quota;
			}
			else{
					die('service id not valid!');
			}
	}

	public function get_quota_pcm2($data) {
			$origin            = $data['origin'];
			$depart_date       = $this->db->escape($data['depart_date']);
			$service_id        = $data['service'];
			$ship_class        = $data['ship_class'];
			// quota perjam
			$depart_time_start = $this->db->escape(date("H:00:00", strtotime($data['depart_time_start'])));
			$end_time          = date("H:00:00", strtotime($data['depart_time_start'].'+1 hours'));
			$depart_time_end   = ($end_time === '00:00:00') ? $this->db->escape('24:00:00') : $this->db->escape($end_time);
			if($service_id == 2){
								$sql = "SELECT        
														q.depart_date
														,q.depart_time
														,q.quota as param_value, q.port_id 
														,q.ship_class,q.id
														,'Kendaraan' as service_type
														,0 as passanger
														,q.total_lm                  
												from app.t_mtr_quota_pcm_vehicle q 
												WHERE  q.ship_class = {$ship_class} AND q.port_id = {$origin} and q.status = 1 
												AND (depart_date::date + depart_time::time) <= ({$depart_date}::date + {$depart_time_start}::time)  order by depart_date desc, depart_time  desc 
												limit 1
												";
								$quota = $this->db->query($sql)->row();
								if (!$quota) {
										return null;
								}
								else{
									$sql2 = "SELECT 
									port.id as origin, 
									coalesce(used_quota.total,0) as total_booking, 
									coalesce(expired.total,0) as total_expired,
									(coalesce(used_quota.used_lm,0) - coalesce(expired.used_lm,0)) as used_lm 
									FROM app.t_mtr_port port 
									LEFT JOIN(
												SELECT coalesce(count(book.id),0) as total ,coalesce(sum(vclass.total_lm),0) as used_lm , book.origin 
														FROM app.t_trx_booking book 
														join app.t_trx_booking_vehicle pass on pass.booking_code = book.booking_code 
														join app.t_mtr_vehicle_class vclass on vclass.id = pass.vehicle_class_id
														WHERE  book.origin = {$origin} 

														and book.depart_date = {$depart_date} 
														and (book.depart_time_start >= {$depart_time_start} and book.depart_time_start < {$depart_time_end} )
														and book.status  in (0,1,2) 
														and book.ship_class = {$ship_class} 
														and book.channel in ('web','ifcs','b2b','mobile')
														group by book.origin
									)as used_quota on used_quota.origin = port.id
									LEFT JOIN (
												SELECT coalesce(count(book.id),0) as total,coalesce(sum(vclass.total_lm),0) as used_lm , book.origin 
														from app.t_trx_booking book
														join app.t_trx_invoice inv on inv.trans_number = book.trans_number 
														join app.t_trx_booking_vehicle pass on pass.booking_code = book.booking_code 
														join app.t_mtr_vehicle_class vclass on vclass.id = pass.vehicle_class_id
														where inv.status NOT IN (2) and inv.due_date::timestamp < now() 
														and book.origin = {$origin} 

														and (book.depart_time_start >= {$depart_time_start} and book.depart_time_start < {$depart_time_end} )
														and book.depart_date = {$depart_date} 
														and book.ship_class = {$ship_class} 
														and book.channel in ('web','ifcs','b2b','mobile')  
														group by book.origin
									) as expired on expired.origin = port.id
									WHERE  port.id = {$origin}
									";

									$used_quota = $this->db->query($sql2)->row();
									if ($used_quota) {
											$quota->passanger = ($used_quota->total_booking - $used_quota->total_expired);
											$quota->used_quota = $used_quota;

									}
									return $quota;
								}

			}
			else if($service_id == 1){
					
							// quota perjam
							$sql = "SELECT coalesce(bok.passanger,0) as passanger , bks.param_value, bks.port_id , bks.ship_class, 'Penumpang' as service_type,coalesce(expired.total,0) as expired
							from app.t_mtr_booking_quota_passanger bks 
							JOIN app.t_mtr_port port ON bks.port_id = port.id 
							LEFT JOIN (
											SELECT count(book.id) as passanger , book.origin  
											FROM app.t_trx_booking book 
											JOIN app.t_mtr_port port ON book.origin = port.id 
											join app.t_trx_booking_passanger pass on pass.booking_code = book.booking_code AND pass.service_id = 1  AND pass.passanger_type_id not in (3) 
											AND book.origin = {$origin} 
											and book.depart_date = {$depart_date} and (book.depart_time_start >= {$depart_time_start} and book.depart_time_start < {$depart_time_end} )
											and book.status not in (3,4,5) 
											and book.server_id = '00' 
											and book.ship_class = {$ship_class}
											group by book.origin

							) bok ON bok.origin =  port.id 
							LEFT JOIN (
													select count(book.id) as total , book.origin
													from app.t_trx_booking book
													join app.t_trx_invoice inv on inv.trans_number = book.trans_number 
													join app.t_trx_booking_passanger pass on pass.booking_code = book.booking_code AND pass.service_id = 1 AND pass.passanger_type_id not in (3) 
													where inv.status NOT IN (2) and inv.due_date::timestamp < now() and book.origin = {$origin}  
													and book.depart_date = {$depart_date} and (book.depart_time_start >= {$depart_time_start} and book.depart_time_start < {$depart_time_end} ) 
													and book.server_id = '00' 
													and book.ship_class = {$ship_class}
													group by book.origin
							) expired ON expired.origin =  port.id 
							WHERE  bks.ship_class = {$ship_class} AND bks.port_id = {$origin} and bks.status = 1 

							";
							$result = $this->db->query($sql)->row();
							if ($result) {
									$result->passanger = $result->passanger - $result->expired;
							}
							return $result;
			}
			else{
					die('service id not valid!');
			}
			
	}

	function get_quota_pcm_reserved($data) {
    $origin            = $data['origin'];
    $depart_date       = $this->db->escape($data['depart_date']);
    $ship_class        = $data['ship_class'];
    $depart_time_start = $this->db->escape(date("H:00:00", strtotime($data['depart_time_start'])));
    $end_time          = date("H:00:00", strtotime($data['depart_time_start'].'+1 hours'));
    $depart_time_end   = ($end_time === '00:00:00') ? $this->db->escape('24:00:00') : $this->db->escape($end_time);
  
            $sql = "SELECT 
                sum(total_quota) as total_quota,res.port_id,res.ship_class,res.depart_date,res.depart_time
                from  app.t_trx_quota_pcm_vehicle_reserved res 
                WHERE  res.depart_date = {$depart_date} 
                AND res.depart_time = {$depart_time_start} 
                AND  res.ship_class = {$ship_class} AND res.port_id = {$origin} and res.status = 1 
                group by res.port_id,res.ship_class,res.depart_date,res.depart_time
                ";
            $quota = $this->db->query($sql)->row();
            return $quota;
  }

  function insert_quota_pcm_vehicle($data) {
      $insert =  $this->db->insert('app.t_trx_quota_pcm_vehicle', $data);
      if ($insert) {
           return $this->db->insert_id();
      }
      else{
        return null;
      }
	}


	function insert_quota_passenger($data) {
			$insert = $this->db->insert('app.t_trx_quota_passanger', $data);
			if ($insert) {
					return $this->db->insert_id();
			}
			else{
				return null;
			}
	}

	function booking_quota_reserved_pcm($data,$quota,$quota_type = null) {
    $total_booking = 1 ;
    $quota_id      =   $quota['reserved_id'];
    $vehicle_class = $data['vehicle_type'];
    $vehicle       = $this->db->query("SELECT id,wide_lm,length_lm,coalesce(total_lm,0) as total_lm from app.t_mtr_vehicle_class where id = ".$vehicle_class." ")->row();
    $sql = "UPDATE  
           app.t_trx_quota_pcm_vehicle_reserved set total_quota = (total_quota-{$total_booking}) , used_quota = (used_quota+{$total_booking}), quota_limit = (quota_limit+{$total_booking}) 
           , used_lm = (used_lm + {$vehicle->total_lm}),updated_on = now() , updated_by = 'system'
        WHERE id = {$quota_id}  
        ";
    $update = $this->db->query($sql);
    return $update;
	}
	
	function booking_quota_pcm($data,$quota,$quota_type = null) {
    $service_id        = $data['service'];
    if($service_id == 2){
      $vehicle = $this->db->query("SELECT id,wide_lm,length_lm,coalesce(total_lm,0) as total_lm from app.t_mtr_vehicle_class where id = '".$data['vehicle_type']."'")->row();
      $total_booking = 1 ;
      $quota_id =   $quota['id'];
              $sql = "UPDATE  
                     app.t_trx_quota_pcm_vehicle set total_quota = (total_quota-{$total_booking}) , used_quota = (used_quota+{$total_booking}), quota_limit = (quota_limit+{$total_booking}) 
                       , used_lm = (used_lm + {$vehicle->total_lm})
                       , updated_on = now() , updated_by = 'system'
                  WHERE id = {$quota_id}  
                  ";
              $update = $this->db->query($sql);
              return $update;

    }
    else if($service_id == 1){
                    $total_booking = $data['total_booking_quota'] ;
                    $quota_id =   $quota['id'];
                    $sql = "UPDATE  
                        app.t_trx_quota_passanger set total_quota = (total_quota-{$total_booking}) , used_quota = (used_quota+{$total_booking}), quota_limit = (quota_limit+{$total_booking}),updated_on = now() , updated_by = 'system'
                        WHERE id = {$quota_id}  
                    ";
                    $update = $this->db->query($sql);

          return $update;
    }
  }

}
