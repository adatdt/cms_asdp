<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_muntah_kapal extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_module = 'transaction/muntah_kapal';
    }

    public function dataTicketNumberPassenger()
    {
        $start          = $this->input->post('start');
        $length         = $this->input->post('length');
        $draw           = $this->input->post('draw');
        $search         = $this->input->post('search');
        $type           = $this->input->post('type');
        $ticketNumber   = $this->input->post('ticketNumber');
        $order          = $this->input->post('order');
        $order_column   = $order[0]['column'];
        $order_dir      = strtoupper($order[0]['dir']);
        $iLike          = trim(strtoupper($this->db->escape_like_str($search['value'])));
        $service_id     = 0;
        $select         = '';
        $where          = '';
        $check_tickets = array();
        $where_tickets = "('')";
        if (!empty($ticketNumber)) {
            $where_tickets = "('".implode("','", array_map('strtoupper', $this->input->post('ticketNumber')))."')";
        }
        if ($type == 'kendaraan') {
            $service_id = 2;
            $select = "
            SELECT
            aa.ID,
            dd.boarding_date,
            ee.ticket_number,
            ee.NAME AS customer_name,
            ee.gender AS customer_gender,
            ee.age AS customer_age,
            ee.city AS customer_city,
            ff.NAME AS id_type_name,
            ee.id_number,
            gg.name AS port_name,
            hh.name AS dock_name,
            ii.name AS ship_name
            FROM app.t_trx_booking_vehicle aa
            LEFT JOIN app.t_trx_boarding_vehicle bb ON aa.ticket_number = bb.ticket_number AND bb.status = 1
            LEFT JOIN app.t_trx_open_boarding cc ON bb.boarding_code = cc.boarding_code
            RIGHT JOIN app.t_trx_boarding_passanger dd ON bb.boarding_code = dd.boarding_code AND dd.status = 1
            LEFT JOIN app.t_trx_booking_passanger ee ON ee.ticket_number = dd.ticket_number AND aa.booking_code = ee.booking_code
            LEFT JOIN app.t_mtr_passanger_type_id ff ON ee.id_type = ff.ID
            LEFT JOIN app.t_mtr_port gg ON cc.port_id = gg.id
            LEFT JOIN app.t_mtr_dock hh ON cc.dock_id = hh.id
            LEFT JOIN app.t_mtr_ship ii ON cc.ship_id = ii.id
            ";
            $where = " WHERE aa.status IS NOT NULL
            AND ee.service_id = {$service_id}
            AND aa.ticket_number IN {$where_tickets} ";
            if (!empty($search['value'])) {
                $where .= " and (
                    ee.ticket_number ILIKE '%".$iLike."%'
                    OR ee.name ILIKE '%".$iLike."%'
                    OR ee.gender ILIKE '%".$iLike."%'
                    OR ee.age::VARCHAR ILIKE '%".$iLike."%'
                    OR ee.city ILIKE '%".$iLike."%'
                    OR ee.id_number ILIKE '%".$iLike."%'
                )";
            }
        } else {
            $service_id = 1;
            $select = "
            SELECT
            aa.ID,
            cc.boarding_date,
            aa.ticket_number,
            aa.NAME AS customer_name,
            aa.gender AS customer_gender,
            aa.age AS customer_age,
            aa.city AS customer_city,
            bb.NAME AS id_type_name,
            aa.id_number,
            ee.name AS port_name,
            ff.name AS dock_name,
            gg.name AS ship_name
            FROM app.t_trx_booking_passanger aa
            LEFT JOIN app.t_mtr_passanger_type_id bb ON aa.id_type = bb.ID
            LEFT JOIN app.t_trx_boarding_passanger cc ON aa.ticket_number = cc.ticket_number AND cc.status = 1
            LEFT JOIN app.t_trx_open_boarding dd ON cc.boarding_code = dd.boarding_code
            LEFT JOIN app.t_mtr_port ee ON dd.port_id = ee.id
            LEFT JOIN app.t_mtr_dock ff ON dd.dock_id = ff.id
            LEFT JOIN app.t_mtr_ship gg ON dd.ship_id = gg.id
            ";
            $where = " WHERE aa.status IS NOT NULL
            AND aa.service_id = {$service_id}
            AND aa.ticket_number IN {$where_tickets} ";
            if (!empty($search['value'])) {
                $where .= " and (
                    aa.name ILIKE '%".$iLike."%'
                    OR aa.gender ILIKE '%".$iLike."%'
                    OR aa.age::VARCHAR ILIKE '%".$iLike."%'
                    OR aa.city ILIKE '%".$iLike."%'
                    OR aa.id_number ILIKE '%".$iLike."%'
                )";
            }
        }

        $field = array(
            0 => 'id',
            1 => 'boarding_date',
            2 => 'ticket_number',
            3 => 'customer_name',
            4 => 'customer_gender',
            5 => 'customer_age',
            6 => 'customer_city',
            7 => 'id_type_name',
            8 => 'id_number',
            9 => 'port_name',
            10 => 'dock_name',
            11 => 'ship_name',
        );

        $order_column = $field[$order_column];

        $sql = "
        $select
        $where
        ";

        $query         = $this->db->query($sql);
        $records_total = $query->num_rows();
        $sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

        if ($length != -1) {
            $sql .=" LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->db->query($sql);
        $rows_data = $query->result();

        $rows   = array();
        $i      = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number = $i;
            if ($row->customer_gender == 'L') {
                $row->customer_gender = 'Laki-laki';
            } else {
                $row->customer_gender = 'Perempuan';
            }
            $rows[] = $row;
            unset($row->id);
            $i++;
        }

        return array(
            'draw'           => $draw,
            'recordsTotal'   => $records_total,
            'recordsFiltered'=> $records_total,
            'data'           => $rows
        );
    }

    public function dataTicketNumberVehicle()
    {
        $start          = $this->input->post('start');
        $length         = $this->input->post('length');
        $draw           = $this->input->post('draw');
        $search         = $this->input->post('search');
        $type           = $this->input->post('type');
        $ticketNumber   = $this->input->post('ticketNumber');
        $order          = $this->input->post('order');
        $order_column   = $order[0]['column'];
        $order_dir      = strtoupper($order[0]['dir']);
        $iLike          = trim(strtoupper($this->db->escape_like_str($search['value'])));
        $where_tickets  = "('')";
        if (!empty($ticketNumber)) {
            $where_tickets = "('".implode("','", array_map('strtoupper', $this->input->post('ticketNumber')))."')";
        }

        $field = array(
            0 => 'id',
            1 => 'boarding_date',
            2 => 'ticket_number',
            3 => 'id_number',
            4 => 'name',
            5 => 'length',
            6 => 'height',
            7 => 'weight',
            8 => 'port_name',
            9 => 'dock_name',
            10 => 'ship_name',
        );

        $order_column = $field[$order_column];

        $where = " WHERE aa.status IS NOT NULL AND aa.ticket_number IN {$where_tickets} ";

        if (!empty($search['value'])) {
            $where .= " and (
                aa.ticket_number ILIKE '%".$iLike."%'
                OR bb.id_number ILIKE '%".$iLike."%'
                OR bb.length::VARCHAR ILIKE '%".$iLike."%'
                OR bb.height::VARCHAR ILIKE '%".$iLike."%'
                OR bb.weight::VARCHAR ILIKE '%".$iLike."%'
                OR cc.name ILIKE '%".$iLike."%'
            )";
        }

        $sql = "
        SELECT
        aa.ID,
        bb.boarding_date,
        aa.ticket_number,
        cc.NAME,
        aa.LENGTH,
        aa.height,
        aa.weight,
        aa.id_number,
        ee.name AS port_name,
        ff.name AS dock_name,
        gg.name AS ship_name
        FROM app.t_trx_booking_vehicle aa
        LEFT JOIN app.t_trx_boarding_vehicle bb ON aa.ticket_number = bb.ticket_number AND bb.status = 1
        LEFT JOIN app.t_mtr_vehicle_class cc ON aa.vehicle_class_id = cc.ID
        LEFT JOIN app.t_trx_open_boarding dd ON bb.boarding_code = dd.boarding_code
        LEFT JOIN app.t_mtr_port ee ON dd.port_id = ee.id
        LEFT JOIN app.t_mtr_dock ff ON dd.dock_id = ff.id
        LEFT JOIN app.t_mtr_ship gg ON dd.ship_id = gg.id
        $where
        ";

        $query          = $this->db->query($sql);
        $records_total  = $query->num_rows();
        $sql            .= " ORDER BY ".$order_column." {$order_dir}";

        if ($length != -1) {
            $sql .=" LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->db->query($sql);
        $rows_data = $query->result();

        $rows   = array();
        $i      = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number = $i;
            $rows[] = $row;
            unset($row->id);
            $i++;
        }

        return array(
            'draw'           => $draw,
            'recordsTotal'   => $records_total,
            'recordsFiltered'=> $records_total,
            'data'           => $rows
        );
    }

    public function getBoardedPassengerByVehicleTicketNumber($ticketNumber)
    {
        $where_tickets = "('')";
        if (!empty($ticketNumber)) {
            $where_tickets = "('".implode("','", array_map('strtoupper', $this->input->post('ticketNumber')))."')";
        }
        $sql = "
        SELECT
        cc.id AS open_boarding_id,
        dd.id AS boarding_id,
        ee.id AS booking_id,
        cc.ship_id,
        dd.boarding_date,
        gg.port_code,
        cc.boarding_code,
        cc.schedule_code,
        ee.ticket_number,
        cc.shift_id,
        cc.shift_date
        FROM app.t_trx_booking_vehicle aa
        LEFT JOIN app.t_trx_boarding_vehicle bb ON aa.ticket_number = bb.ticket_number
        LEFT JOIN app.t_trx_open_boarding cc ON bb.boarding_code = cc.boarding_code
        RIGHT JOIN app.t_trx_boarding_passanger dd ON bb.boarding_code = dd.boarding_code
        LEFT JOIN app.t_trx_booking_passanger ee ON ee.ticket_number = dd.ticket_number AND aa.booking_code = ee.booking_code
        LEFT JOIN app.t_mtr_port gg ON cc.port_id = gg.id
        WHERE aa.status IS NOT NULL
        AND ee.service_id = 2
        AND aa.ticket_number IN {$where_tickets}
        ORDER BY cc.id, dd.id, ee.id;
        ";
        $data = $this->db->query($sql);
        if ($data->num_rows() > 0) {
            return $data->result();
        } else {
            return false;
        }
    }

    public function getBoardedPassengerByTicketNumber($ticketNumber)
    {
        $where_tickets = "('')";
        if (!empty($ticketNumber)) {
            $where_tickets = "('".implode("','", array_map('strtoupper', $this->input->post('ticketNumber')))."')";
        }
        $sql = "
        SELECT
        dd.id AS open_boarding_id,
        cc.id AS boarding_id,
        aa.id AS booking_id,
        dd.ship_id,
        cc.boarding_date,
        gg.port_code,
        dd.boarding_code,
        dd.schedule_code,
        aa.ticket_number,
        dd.shift_id,
        dd.shift_date
        FROM app.t_trx_booking_passanger aa
        LEFT JOIN app.t_trx_boarding_passanger cc ON aa.ticket_number = cc.ticket_number
        LEFT JOIN app.t_trx_open_boarding dd ON cc.boarding_code = dd.boarding_code
        LEFT JOIN app.t_mtr_port gg ON cc.port_id = gg.id
        WHERE aa.status IS NOT NULL
        AND aa.service_id = 1
        AND aa.ticket_number IN {$where_tickets}
        ORDER BY dd.id, cc.id, aa.id;
        ";
        $data = $this->db->query($sql);
        if ($data->num_rows() > 0) {
            return $data->result();
        } else {
            return false;
        }
    }

    public function getBoardedVehicleByTicketNumber($ticketNumber)
    {
        $where_tickets = "('')";
        if (!empty($ticketNumber)) {
            $where_tickets = "('".implode("','", array_map('strtoupper', $this->input->post('ticketNumber')))."')";
        }
        $sql = "
        SELECT
        aa.id AS open_boarding_id,
        bb.id AS boarding_id,
        cc.id AS booking_id,
        cc.booking_code,
        aa.ship_id,
        bb.boarding_date,
        dd.port_code,
        aa.boarding_code,
        aa.schedule_code,
        bb.ticket_number,
        aa.shift_id,
        aa.shift_date
        FROM app.t_trx_open_boarding aa
        RIGHT JOIN app.t_trx_boarding_vehicle bb ON aa.boarding_code = bb.boarding_code
        LEFT JOIN app.t_trx_booking_vehicle cc ON bb.ticket_number = cc.ticket_number
        LEFT JOIN app.t_mtr_port dd ON aa.port_id = dd.id
        WHERE aa.status IS NOT NULL
        AND cc.ticket_number IN {$where_tickets}
        ORDER BY aa.id, bb.id, cc.id;
        ";
        $data = $this->db->query($sql);
        if ($data->num_rows() > 0) {
            return $data->result();
        } else {
            return false;
        }
    }

    public function setSwitchShipData_15012021($header, $detail, $type)
    {
        switch ($type) {
            case 'passenger':
                $header_table = 'app.t_trx_switch_ship_passanger';
                $detail_table = 'app.t_trx_switch_ship_passanger_detail';
                break;

            case 'vehicle':
                $header_table = 'app.t_trx_switch_ship_vehicle';
                $detail_table = 'app.t_trx_switch_ship_vehicle_detail';
                break;
        }
        $this->db->trans_begin();
        try {
            $this->db->insert($header_table, $header);
            $header_id = $this->db->insert_id();
            $i = 0;
            foreach ($detail as $row) {
                $detail[$i]['switch_ship_id'] = $header_id;
                $i++;
            }
            $this->db->insert_batch($detail_table, $detail);
        } catch (Exception $e) {
            $this->db->trans_rollback();
        	return false;
        }

		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
         	return true;
      	} else {
         	$this->db->trans_rollback();
        	return false;
      	}
    }

    public function setSwitchShipData($header, $detail, $type)
    {
        switch ($type) {
            case 'passenger':
                $header_table = 'app.t_trx_switch_ship_passanger';
                $detail_table = 'app.t_trx_switch_ship_passanger_detail';
                break;

            case 'vehicle':
                $header_table = 'app.t_trx_switch_ship_vehicle';
                $detail_table = 'app.t_trx_switch_ship_vehicle_detail';
                break;
        }
        $this->db->trans_begin();
        try {

            $boarding_code=$header['boarding_code'];

            $checkHeader=$this->db->query(" select * from $header_table where boarding_code='{$boarding_code}' ")->row();
            
            if(count((array)$checkHeader)<1) // jika datanya hedernya belum terbentuk a
            {
                $this->db->insert($header_table, $header);
                $header_id = $this->db->insert_id();
            }
            else
            {
                $header_id = $checkHeader->id;
            }

            $i = 0;
            foreach ($detail as $row) {
                $detail[$i]['switch_ship_id'] = $header_id;
                $i++;
            }
            $this->db->insert_batch($detail_table, $detail);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }    

    public function cekBoardingStatus($type, $ticketNumber)
    {
        if ($type == 'kendaraan') {
            $table = 'app.t_trx_boarding_vehicle aa';
        } else {
            $table = 'app.t_trx_boarding_passanger aa';
            $service_id = 1;
            $this->db->join('app.t_trx_booking_passanger bb', 'aa.ticket_number = bb.ticket_number', 'left');
            $this->db->where('bb.service_id', $service_id);
        }
        $this->db->where('aa.boarding_date IS NOT NULL');
        $this->db->where('aa.status', 1);
        $this->db->where_in('aa.ticket_number', $ticketNumber);
        $data = $this->db->get($table);
        if ($data->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function select_data($table, $where="")
    {
        return $this->db->query("select * from $table $where");
    }

    public function insert_data($table, $data)
    {
        $this->db->trans_begin();

        $this->db->insert($table, $data);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }

    public function update_data($table, $data, $where)
    {
        $this->db->trans_begin();

        $this->db->where($where);
        $this->db->update($table, $data);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }

    public function delete_data($table, $where)
    {
        $this->db->trans_begin();

        $this->db->where($where);
        $this->db->delete($table);

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }

    public function unboardAndUnbook_back($ticket_numbers, $type='penumpang')
    {

        // mulai proses di db lokal
        $this->db->trans_begin();        
        if ($type == 'kendaraan')
        {
            $table_booking = 'app.t_trx_booking_vehicle';
            $table_boarding = 'app.t_trx_boarding_vehicle';
            $tblName='t_trx_booking_vehicle';            

        } 
        else 
        {
            $table_booking = 'app.t_trx_booking_passanger';
            $table_boarding = 'app.t_trx_boarding_passanger';
            $tblName='t_trx_booking_passanger';

        }

        foreach ($ticket_numbers as $value) 
        {
            $dataTrxStatus[]=array(
                'tbl_name'=>$tblName,
                'transaction_code'=>$value,
                'status'=>7,
                'created_by'=>$this->_username,
                'created_on'=>date('Y-m-d H:i:s'),
            );
        }        

        // insert batch ke t_trx_status
        $this->db->insert_batch("app.t_trx_status", $dataTrxStatus);

        // proses unbook passenger di db lokal
        $this->db->set('status', 7);
        $this->db->set('updated_by', $this->_username);
        $this->db->set('updated_on', date('Y-m-d H:i:s'));
        $this->db->where_in('ticket_number', $ticket_numbers);
        $this->db->update($table_booking);
        
        // proses unboard passenger di db lokal
        // $this->db->where_in('ticket_number', $ticket_numbers);
        // $this->db->delete($table_boarding);

        $ymdhis=date('ymdhis');

        foreach ($ticket_numbers as $key => $value)
        {
            $this->db->query( "update {$table_boarding} set
                                updated_on='".date('Y-m-d H:i:s')."', 
                                updated_by='".$this->session->userdata('username')."',
                                ticket_number=concat(ticket_number,'-','".$ymdhis."'),
                                status='-5'
                                where
                                ticket_number='{$value}'
                            "  );
        }


        if ($this->db->trans_status() === false) {
            // kalo di lokal aja udah gagal langsung rollback dan return error
            $this->db->trans_rollback();
            return false;
        }

        // mulai proses di cloud
        $cloud_db = $this->load->database('cloud', true);
        $cloud_db->trans_begin();
        // // proses unbook passenger di db lokal
        // $cloud_db->set('status', 7);
        // $this->db->set('updated_by', $this->_username);
        // $this->db->set('updated_on', date('Y-m-d H:i:s'));
        // $cloud_db->where_in('ticket_number', $ticket_numbers);
        // $cloud_db->update($table_booking);
        // proses unboard passenger di db cloud

        // $cloud_db->where_in('ticket_number', $ticket_numbers);
        // $cloud_db->delete($table_boarding);

        foreach ($ticket_numbers as $key => $value)
        {

            $cloud_db->query( "update {$table_boarding} set
                                updated_on='".date('Y-m-d H:i:s')."', 
                                updated_by='".$this->session->userdata('username')."',
                                ticket_number=concat(ticket_number,'-','".$ymdhis."'),
                                status='-5'
                                where
                                ticket_number ='{$value}'
                            "  );                
        }
        if ($cloud_db->trans_status() === true && $this->db->trans_status() === true) {
            // kalo proses di cloud dan lokal sukses baru commit
            $cloud_db->trans_commit();
            $this->db->trans_commit();
            return true;
        } 
        else 
        {
            // kalo salah satu proses cloud maupun lokal ada yang gagal, rollback dan return error
            $cloud_db->trans_rollback();
            $this->db->trans_rollback();
            return false;
        }
    }

    public function unboardAndUnbook_29012021($ticket_numbers, $type='penumpang')
    {

        // mulai proses di db lokal
        $this->db->trans_begin();
        if ($type == 'kendaraan')
        {
            $table_booking = 'app.t_trx_booking_vehicle';
            $table_boarding = 'app.t_trx_boarding_vehicle';
            $tblName='t_trx_booking_vehicle';            

        } 
        else 
        {
            $table_booking = 'app.t_trx_booking_passanger';
            $table_boarding = 'app.t_trx_boarding_passanger';
            $tblName='t_trx_booking_passanger';

        }

        foreach ($ticket_numbers as $value) 
        {
            $dataTrxStatus[]=array(
                'tbl_name'=>$tblName,
                'transaction_code'=>$value,
                'status'=>7,
                'created_by'=>$this->_username,
                'created_on'=>date('Y-m-d H:i:s'),
            );

            // proses unbook passenger di db lokal
            $this->db->set('status', 7);
            $this->db->set('updated_by', $this->_username);
            $this->db->set('updated_on', date('Y-m-d H:i:s'));
            $this->db->where('ticket_number', $value);
            $this->db->update($table_booking);            
        }        

        // insert batch ke t_trx_status
        // $this->db->insert_batch("app.t_trx_status", $dataTrxStatus);  takout sementara karna table belum ready


        
        // proses unboard passenger di db lokal
        // $this->db->where_in('ticket_number', $ticket_numbers);
        // $this->db->delete($table_boarding);

        $ymdhis=date('ymdhis');

        foreach ($ticket_numbers as $key => $value)
        {
            $this->db->query( "update {$table_boarding} set
                                updated_on='".date('Y-m-d H:i:s')."', 
                                updated_by='".$this->session->userdata('username')."',
                                ticket_number=concat(ticket_number,'-','".$ymdhis."'),
                                status='-5'
                                where
                                ticket_number='{$value}'
                            "  );
        }


        if ($this->db->trans_status() === false) {
            // kalo di lokal aja udah gagal langsung rollback dan return error
            $this->db->trans_rollback();
            return false;
        }

        // mulai proses di cloud
        $cloud_db = $this->load->database('cloud', true);
        $cloud_db->trans_begin();
        // // proses unbook passenger di db lokal
        // $cloud_db->set('status', 7);
        // $this->db->set('updated_by', $this->_username);
        // $this->db->set('updated_on', date('Y-m-d H:i:s'));
        // $cloud_db->where_in('ticket_number', $ticket_numbers);
        // $cloud_db->update($table_booking);
        // proses unboard passenger di db cloud

        // $cloud_db->where_in('ticket_number', $ticket_numbers);
        // $cloud_db->delete($table_boarding);

        foreach ($ticket_numbers as $key => $value)
        {

            $cloud_db->query( "update {$table_boarding} set
                                updated_on='".date('Y-m-d H:i:s')."', 
                                updated_by='".$this->session->userdata('username')."',
                                ticket_number=concat(ticket_number,'-','".$ymdhis."'),
                                status='-5'
                                where
                                ticket_number ='{$value}'
                            "  );                
        }
        if ($cloud_db->trans_status() === true && $this->db->trans_status() === true) {
            // kalo proses di cloud dan lokal sukses baru commit
            $cloud_db->trans_commit();
            $this->db->trans_commit();
            return true;
        } 
        else 
        {
            // kalo salah satu proses cloud maupun lokal ada yang gagal, rollback dan return error
            $cloud_db->trans_rollback();
            $this->db->trans_rollback();
            return false;
        }
    }        
    public function unboardAndUnbook($ticket_numbers, $type='penumpang',$header, $detail, $type2, $booking_code="")
    {

        // mulai proses di db lokal
        $this->db->trans_begin();
        $ymdhis=date('ymdhis');

        $insertHeader=$this->setSwitchShipData($header, $detail, $type2);

        if($insertHeader) // jika headernya tidak ada error
        {
        
            if ($type == 'kendaraan')
            {
                $table_booking = 'app.t_trx_booking_vehicle';
                $table_boarding = 'app.t_trx_boarding_vehicle';
                $tblName='t_trx_booking_vehicle';  
                
                // update ticket 
                if(!empty($booking_code))
                {
                    $bookingString = array_map(function($a){ return "'".$a."'"; }, $booking_code);
                    $getTicketNumberPassVeh = $this->select_data("app.t_trx_booking_passanger"," where booking_code in (".implode(",",$bookingString).") and status !='-5' ")->result();
                    $getTicketNumberPassVeh2 = array_map(function($a){ return "'".$a."'"; }, array_column($getTicketNumberPassVeh,"ticket_number"));

                    $this->db->query( "update app.t_trx_boarding_manifest_vehicle set
                        updated_on='".date('Y-m-d H:i:s')."', 
                        updated_by='".$this->session->userdata('username')."',
                        ticket_number=concat(ticket_number,'-','".$ymdhis."'),
                        status='-5'
                        where
                        ticket_number in (".$getTicketNumberPassVeh2.")
                "  );
                }

            } 
            else 
            {
                $table_booking = 'app.t_trx_booking_passanger';
                $table_boarding = 'app.t_trx_boarding_passanger';
                $tblName='t_trx_booking_passanger';

            }

            foreach ($ticket_numbers as $value) 
            {
                $dataTrxStatus[]=array(
                    'tbl_name'=>$tblName,
                    'transaction_code'=>$value,
                    'status'=>7,
                    'created_by'=>$this->_username,
                    'created_on'=>date('Y-m-d H:i:s'),
                );

                // proses unbook passenger di db lokal
                $this->db->set('status', 7);
                $this->db->set('updated_by', $this->_username);
                $this->db->set('updated_on', date('Y-m-d H:i:s'));
                $this->db->where('ticket_number', $value);
                $this->db->update($table_booking);            
            }        

            // insert batch ke t_trx_status
            // $this->db->insert_batch("app.t_trx_status", $dataTrxStatus);  takout sementara karna table belum ready


            
            // proses unboard passenger di db lokal
            // $this->db->where_in('ticket_number', $ticket_numbers);
            // $this->db->delete($table_boarding);
            

            foreach ($ticket_numbers as $key => $value)
            {
                $this->db->query( "update {$table_boarding} set
                                    updated_on='".date('Y-m-d H:i:s')."', 
                                    updated_by='".$this->session->userdata('username')."',
                                    ticket_number=concat(ticket_number,'-','".$ymdhis."'),
                                    status='-5'
                                    where
                                    ticket_number='{$value}'
                                "  );
            }


            if ($this->db->trans_status() === false) {
                // kalo di lokal aja udah gagal langsung rollback dan return error
                $this->db->trans_rollback();
                return false;
            }

            // mulai proses di cloud
            $cloud_db = $this->load->database('cloud', true);
            $cloud_db->trans_begin();
            // // proses unbook passenger di db lokal
            // $cloud_db->set('status', 7);
            // $this->db->set('updated_by', $this->_username);
            // $this->db->set('updated_on', date('Y-m-d H:i:s'));
            // $cloud_db->where_in('ticket_number', $ticket_numbers);
            // $cloud_db->update($table_booking);
            // proses unboard passenger di db cloud

            // $cloud_db->where_in('ticket_number', $ticket_numbers);
            // $cloud_db->delete($table_boarding);

            foreach ($ticket_numbers as $key => $value)
            {

                $cloud_db->query( "update {$table_boarding} set
                                    updated_on='".date('Y-m-d H:i:s')."', 
                                    updated_by='".$this->session->userdata('username')."',
                                    ticket_number=concat(ticket_number,'-','".$ymdhis."'),
                                    status='-5'
                                    where
                                    ticket_number ='{$value}'
                                "  );                
            }
            if ($cloud_db->trans_status() === true && $this->db->trans_status() === true) {
                // kalo proses di cloud dan lokal sukses baru commit
                $cloud_db->trans_commit();
                $this->db->trans_commit();
                return true;
            } 
            else 
            {
                // kalo salah satu proses cloud maupun lokal ada yang gagal, rollback dan return error
                $cloud_db->trans_rollback();
                $this->db->trans_rollback();
                return false;
            }
        }
        else
        {
            $this->db->trans_rollback();
            return false;
        }   

    }



    public function dataList()
    {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');
        // $service_id = $this->enc->decode($this->input->post('service'));
        $dateTo = trim($this->input->post('dateTo'));
        $dateFrom = trim($this->input->post('dateFrom'));
        $port = $this->enc->decode($this->input->post('port'));
        $ship = $this->enc->decode($this->input->post('ship'));
        $order = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir = strtoupper($order[0]['dir']);
        $searchData=$this->input->post('searchData');
        $searchName=$this->input->post('searchName');
        $iLike  = trim(strtoupper($this->db->escape_like_str($searchData)));

        $field = array(
            0 =>'boarding_code',
            1 =>'date',
            2 =>'ship_name',
            3 =>'port_name',
            4 =>'boarding_code',
            5 =>'schedule_code'
        );

        $order_column = $field[$order_column];

        $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE ttssp.status = 1 and ttssp.created_on >= '". $dateFrom . "' and ttssp.created_on < '" . $dateToNew . "'";
        // $where = " WHERE ttssp.status = 1 AND date(ttssp.created_on)  between '".$dateFrom."' and '".$dateTo."' ";

        if(!empty($port))
        {
            $where .=" AND (ttssp.port_code='".$port."')";
        }

        if(!empty($ship))
        {
            $where .=" AND (ttssp.ship_id=".$ship.")";
        }


        // if (!empty($search['value'])){
        //     $where .="and (tms.name ilike '%".$iLike."%' or tmp.name ilike '%".$iLike."%' or
        //                     ttssp.booking_code ilike '%".$iLike."%' or ttssp.schedule_code ilike '%".$iLike."%'
        //                     )";
        // }

        if(!empty($searchData))
        {
            if($searchName=='shipName')
            {
                $where .=" and (tms.name ilike '%".$iLike."%' ) ";
            }
            else if($searchName=='boardingCode')
            {
                $where .=" and (boarding_code ilike '%".$iLike."%' ) ";
            }
            else
            {
                $where .=" and (schedule_code ilike '%".$iLike."%' ) ";
            }
        }

        $sql = "SELECT DISTINCT ON (boarding_code) boarding_code, id, ship_name, date, port_name, schedule_code, service_name
                FROM
                (SELECT DISTINCT ON (boarding_code) boarding_code, ttssp.id, tms.name AS ship_name, date(ttssp.created_on) as date, tmp.name AS port_name, schedule_code, 'PENUMPANG' AS service_name
                    FROM app.t_trx_switch_ship_passanger ttssp
                    JOIN app.t_mtr_ship tms ON ttssp.ship_id = tms.id
                    JOIN app.t_mtr_port tmp ON ttssp.port_code = tmp.port_code
                    $where
                    UNION ALL
                    SELECT DISTINCT ON (boarding_code) boarding_code, ttssp.id, tms.name AS ship_name, date(ttssp.created_on) as date, tmp.name AS port_name, schedule_code, 'KENDARAAN' AS service_name
                    FROM app.t_trx_switch_ship_vehicle ttssp
                    JOIN app.t_mtr_ship tms ON ttssp.ship_id = tms.id
                    JOIN app.t_mtr_port tmp ON ttssp.port_code = tmp.port_code
                    $where) trx";

        $sqlCount ="SELECT count(cnt.id) as countdata from (SELECT DISTINCT ON (boarding_code) boarding_code, id, ship_name, date, port_name, schedule_code, service_name
                        FROM
                    (SELECT DISTINCT ON (boarding_code) boarding_code, ttssp.id, tms.name AS ship_name, date(ttssp.created_on) as date, tmp.name AS port_name, schedule_code, 'PENUMPANG' AS service_name
                        FROM app.t_trx_switch_ship_passanger ttssp
                        JOIN app.t_mtr_ship tms ON ttssp.ship_id = tms.id
                        JOIN app.t_mtr_port tmp ON ttssp.port_code = tmp.port_code
                        $where
                        UNION ALL
                        SELECT DISTINCT ON (boarding_code) boarding_code, ttssp.id, tms.name AS ship_name, date(ttssp.created_on) as date, tmp.name AS port_name, schedule_code, 'KENDARAAN' AS service_name
                        FROM app.t_trx_switch_ship_vehicle ttssp
                        JOIN app.t_mtr_ship tms ON ttssp.ship_id = tms.id
                        JOIN app.t_mtr_port tmp ON ttssp.port_code = tmp.port_code
                        $where) trx) cnt";

        $queryCount         = $this->db->query($sqlCount)->row();
        $records_total = $queryCount->countdata;

        // $query         = $this->db->query($sql);
        // $records_total = $query->num_rows();
        $sql          .= " ORDER BY ".$order_column." {$order_dir}";

        if($length != -1){
            $sql .=" LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->db->query($sql);
        $rows_data = $query->result();

        $rows   = array();
        $i      = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number = $i;

            $id=$this->enc->encode($row->boarding_code);
            $detail_url     = site_url($this->_module."/detail/{$id}");

            $row->actions = generate_button_new('transaction/muntah_kapal/history', 'detail', $detail_url);

            $row->date=format_date($row->date);
            $row->no=$i;

            $rows[] = $row;
            unset($row->id);

            $i++;
        }

        return array(
            'draw'           => $draw,
            'recordsTotal'   => $records_total,
            'recordsFiltered'=> $records_total,
            'data'           => $rows
        );
    }

    public function getDetailSwitchShip($boarding_code)
    {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');
        // $service_id = $this->enc->decode($this->input->post('service'));
        // $dateTo = trim($this->input->post('dateTo'));
        // $dateFrom = trim($this->input->post('dateFrom'));
        // $port = $this->enc->decode($this->input->post('port'));
        // $ship = $this->enc->decode($this->input->post('ship'));
        $order = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir = strtoupper($order[0]['dir']);
        $iLike  = trim(strtoupper($this->db->escape_like_str($search['value'])));

        $field = array(
            0 =>'booking_code',
            1 =>'ticket_number',
            2 =>'name',
            3 =>'id_number',
            4 =>'service_name'
        );

        $order_column = $field[$order_column];

        $where = " WHERE ttssp.boarding_code = '{$boarding_code}' ";

        if (!empty($search['value'])){
            $where .=" and (booking_code ilike '%".$iLike."%' or ttbp.name ilike '%".$iLike."%' or
                            ttsspd.ticket_number ilike '%".$iLike."%'
                            or id_number ilike '%".$iLike."%'
                            )";
        }

        $sql = "
        SELECT booking_code, ttsspd.ticket_number, ttbp.name, id_number, tms.name AS service_name
        FROM app.t_trx_switch_ship_passanger ttssp
        JOIN app.t_trx_switch_ship_passanger_detail ttsspd ON ttssp.id = ttsspd.switch_ship_id
        JOIN app.t_trx_booking_passanger ttbp ON ttsspd.ticket_number = ttbp.ticket_number AND service_id = 1
        JOIN app.t_mtr_service tms ON ttbp.service_id = tms.id
        {$where}
        UNION ALL
        SELECT booking_code, ttsspd.ticket_number, tmvc.name, id_number, tms.name AS service_name
        FROM app.t_trx_switch_ship_vehicle ttssp
        JOIN app.t_trx_switch_ship_vehicle_detail ttsspd ON ttssp.id = ttsspd.switch_ship_id
        JOIN app.t_trx_booking_vehicle ttbv ON ttsspd.ticket_number = ttbv.ticket_number AND service_id = 2
        JOIN app.t_mtr_vehicle_class tmvc ON ttbv.vehicle_class_id = tmvc.id
        JOIN app.t_mtr_service tms ON ttbv.service_id = tms.id
        {$where}";

        $sqlCount = "SELECT count(cnt.booking_code) as countdata from (".$sql.") cnt";

        $queryCount         = $this->db->query($sqlCount)->row();
        $records_total = $queryCount->countdata;
        // $query         = $this->db->query($sql);
        // $records_total = $query->num_rows();
        $sql          .= " ORDER BY ".$order_column." {$order_dir}";

        if($length != -1){
            $sql .=" LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->db->query($sql);
        $rows_data = $query->result();

        $rows   = array();
        $i      = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number = $i;
            $row->no=$i;
            $rows[] = $row;
            unset($row->id);
            $i++;
        }

        return array(
            'draw'           => $draw,
            'recordsTotal'   => $records_total,
            'recordsFiltered'=> $records_total,
            'data'           => $rows
        );
    }

    public function getTicketNumberBoardingData($type, $ticketNumber)
    {
        if ($type == 'kendaraan') {
            $table = 'app.t_trx_boarding_vehicle aa';
        } else {
            $table = 'app.t_trx_boarding_passanger aa';
            $service_id = 1;
            $this->db->join('app.t_trx_booking_passanger bb', 'aa.ticket_number = bb.ticket_number', 'left');
            $this->db->where('bb.service_id', $service_id);
        }
        $this->db->where('aa.boarding_date IS NOT NULL');
        $this->db->where('aa.status', 1);
        $this->db->where_in('aa.ticket_number', $ticketNumber);
        $data = $this->db->get($table);
        return $data->result();
    }

    public function cekManifestStatus($type, $ticketNumber)
    {
        if ($type == 'kendaraan') {
            $table = 'app.t_trx_boarding_vehicle aa';
        } else {
            $table = 'app.t_trx_boarding_passanger aa';
            $service_id = 1;
            $this->db->join('app.t_trx_booking_passanger bb', 'aa.ticket_number = bb.ticket_number', 'left');
            $this->db->where('bb.service_id', $service_id);
        }
        $this->db->join('app.t_trx_approval_ship_officer cc', 'aa.boarding_code = cc.boarding_code');
        $this->db->where('cc.schedule_code IS NOT NULL');
        $this->db->where('cc.status', 1);
        $this->db->where('aa.boarding_date IS NOT NULL');
        $this->db->where('aa.status', 1);
        $this->db->where_in('aa.ticket_number', $ticketNumber);
        $data = $this->db->get($table);
        if ($data->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
