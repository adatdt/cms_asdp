<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sab_model2 extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'sab';
	}

	function get_by_ticket_number($ticket_number) {
		$ticket_number = $this->db->escape($ticket_number);
		$sql = "SELECT 
			bop.ticket_number
			,bop.origin
			,port.name as origin_name
			,bop.destination
			,port2.name as destination_name
			,bop.depart_date
			,bop.status
			,bop.depart_time_start
			,bop.depart_time_end
			,bop.ship_class 
			,sclass.name as ship_class_name
			,bop.service_id
			,srv.name as service_name
			,bop.booking_code
			,bop.boarding_expired
			,type.name as passanger_type 
			,bop.status 
			,bop.name as manifest 
			,bop.ship_class
			,bop.gatein_expired
			, ship.name as ship_name
			, bv.boarding_date
			FROM app.t_trx_booking_passanger bop 
			JOIN app.t_mtr_port port ON port.id = bop.origin  
			JOIN app.t_mtr_port port2 ON port2.id = bop.destination 
			JOIN app.t_trx_booking book ON bop.booking_code = book.booking_code AND book.status = 2 
			JOIN app.t_trx_invoice inv ON inv.trans_number = book.trans_number AND inv.status = 2  
			JOIN app.t_mtr_service srv ON srv.id = bop.service_id 
			JOIN app.t_mtr_ship_class sclass ON sclass.id = bop.ship_class  
			JOIN app.t_mtr_passanger_type type ON type.id = bop.passanger_type_id 
			LEFT JOIN app.t_trx_boarding_passanger bv ON bv.ticket_number = bop.ticket_number 
			LEFT JOIN app.t_trx_open_boarding ob ON ob.boarding_code = bv.boarding_code 
			LEFT JOIN app.t_mtr_ship ship ON ship.id = ob.ship_id 
			WHERE bop.ticket_number = {$ticket_number} AND bop.status in (2,3,4,5,6,7,10,11,12) ";
		return $this->db->query($sql)->row();
	}

	function get_terminal_data($terminal_code) {
		$terminal_code = $this->db->escape($terminal_code);
		$sql = "SELECT 
				trm.terminal_code
				,trm.ship_class
				,trm.port_id as origin
				,rute.destination 
				,service_id 
				,trm.dock_id
				,type.terminal_type_name
				FROM app.t_mtr_device_terminal trm 
				JOIN app.t_mtr_device_terminal_type type ON type.terminal_type_id = trm.terminal_type AND type.status = 1 
				JOIN app.t_mtr_port port ON port.id = trm.port_id AND port.status = 1  
				JOIN app.t_mtr_rute rute ON rute.origin = trm.port_id AND rute.status = 1  
				WHERE trm.terminal_code = {$terminal_code}";
		return $this->db->query($sql)->row();
	}

	function get_schedule($gate) {
		$sql = "SELECT 
				sch.schedule_code
				,sch.schedule_date
				,boarding_code
				,boarding.ship_class
				,ship.name as ship_name
				,boarding.ship_class
				FROM app.t_trx_schedule sch  
				JOIN app.t_trx_open_boarding boarding ON boarding.schedule_code = sch.schedule_code AND boarding.status = 1  
				JOIN app.t_mtr_ship ship ON ship.id = boarding.ship_id
				WHERE sch.status  = 1 
				AND boarding.port_id = {$gate->origin} 
				AND sch.dock_id = {$gate->dock_id}  order by boarding.id ";
		return $this->db->query($sql)->row();
	}

	function add($data) {
		return $this->db->insert('app.t_trx_boarding_passanger', $data);
	}

	function add_temp_data($data) {
		return $this->db->insert('app.t_temp_boarding_passanger', $data);
	}

	function update_status($ticket_number, $data){
		$this->db->where('ticket_number', $ticket_number);
		$this->db->update('app.t_trx_booking_passanger', $data);
	}
}
