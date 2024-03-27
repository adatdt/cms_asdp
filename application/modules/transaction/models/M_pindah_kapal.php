<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_pindah_kapal extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_module = 'transaction/pindah_kapal';
    }

    public function dataScheduleCodePassenger()
    {
        $start          = $this->input->post('start');
        $length         = $this->input->post('length');
        $draw           = $this->input->post('draw');
        $search         = $this->input->post('search');
        $ticketNumber   = strtoupper(trim($this->input->post('ticketNumber')));
        $order          = $this->input->post('order');
        $order_column   = $order[0]['column'];
        $order_dir      = strtoupper($order[0]['dir']);
        $iLike          = trim(strtoupper($this->db->escape_like_str($search['value'])));

        $scheduleCode   = $this->getScheduleCodeByTicketNumber($ticketNumber);

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
            12 => 'schedule_code',
        );

        $order_column = $field[$order_column];

        $where = " WHERE aa.status IS NOT NULL AND aa.schedule_code = '{$scheduleCode}' and bb.status<>'-5' ";

        if (!empty($search['value'])) {
            $where .= " and (
                bb.ticket_number ILIKE '%".$iLike."%'
                OR cc.name ILIKE '%".$iLike."%'
                OR cc.id_number ILIKE '%".$iLike."%'
            )";
        }

        $sql = "
        SELECT
        aa.id,
        bb.boarding_date,
        bb.ticket_number,
        cc.name AS customer_name,
        cc.gender AS customer_gender,
        cc.age AS customer_age,
        cc.city AS customer_city,
        dd.name AS id_type_name,
        cc.id_number,
        ee.name AS port_name,
        ff.name AS dock_name,
        gg.name AS ship_name,
        aa.schedule_code
        FROM app.t_trx_open_boarding aa
        RIGHT JOIN app.t_trx_boarding_passanger bb ON aa.boarding_code = bb.boarding_code
        LEFT JOIN app.t_trx_booking_passanger cc ON bb.ticket_number = cc.ticket_number
        LEFT JOIN app.t_mtr_passanger_type_id dd ON cc.id_type = dd.id
        LEFT JOIN app.t_mtr_port ee ON aa.port_id = ee.id
        LEFT JOIN app.t_mtr_dock ff ON aa.dock_id = ff.id
        LEFT JOIN app.t_mtr_ship gg ON aa.ship_id = gg.id
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

    public function dataScheduleCodeVehicle()
    {
        $start          = $this->input->post('start');
        $length         = $this->input->post('length');
        $draw           = $this->input->post('draw');
        $search         = $this->input->post('search');
        $ticketNumber   = strtoupper(trim($this->input->post('ticketNumber')));
        $order          = $this->input->post('order');
        $order_column   = $order[0]['column'];
        $order_dir      = strtoupper($order[0]['dir']);
        $iLike          = trim(strtoupper($this->db->escape_like_str($search['value'])));

        $scheduleCode   = $this->getScheduleCodeByTicketNumber($ticketNumber);

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
            11 => 'schedule_code',
        );

        $order_column = $field[$order_column];

        $where = " WHERE aa.status IS NOT NULL AND aa.schedule_code = '{$scheduleCode}'  and bb.status<>'-5' ";

        if (!empty($search['value'])) {
            $where .= " and (
                bb.ticket_number ILIKE '%".$iLike."%'
                OR cc.id_number ILIKE '%".$iLike."%'
                OR dd.name ILIKE '%".$iLike."%'
            )";
        }

        $sql = "
        SELECT
        aa.id,
        bb.boarding_date,
        bb.ticket_number,
        cc.id_number,
        dd.name,
        cc.length,
        cc.height,
        cc.weight,
        ee.name AS port_name,
        ff.name AS dock_name,
        gg.name AS ship_name,
        aa.schedule_code
        FROM app.t_trx_open_boarding aa
        RIGHT JOIN app.t_trx_boarding_vehicle bb ON aa.boarding_code = bb.boarding_code
        LEFT JOIN app.t_trx_booking_vehicle cc ON bb.ticket_number = cc.ticket_number
        LEFT JOIN app.t_mtr_vehicle_class dd ON cc.vehicle_class_id = dd.id
        LEFT JOIN app.t_mtr_port ee ON aa.port_id = ee.id
        LEFT JOIN app.t_mtr_dock ff ON aa.dock_id = ff.id
        LEFT JOIN app.t_mtr_ship gg ON aa.ship_id = gg.id
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

    public function getBoardedPassengerByScheduleCode($scheduleCode)
    {
        $sql = "
        SELECT
        aa.id AS open_boarding_id,
        bb.id AS boarding_id,
        cc.id AS booking_id,
        aa.ship_id,
        dd.port_code,
        aa.boarding_code,
        aa.schedule_code,
        bb.ticket_number,
        bb.boarding_date,
        aa.shift_id,
        aa.shift_date
        FROM app.t_trx_open_boarding aa
        RIGHT JOIN app.t_trx_boarding_passanger bb ON aa.boarding_code = bb.boarding_code
        LEFT JOIN app.t_trx_booking_passanger cc ON bb.ticket_number = cc.ticket_number
        LEFT JOIN app.t_mtr_port dd ON aa.port_id = dd.id
        WHERE
        aa.status IS NOT NULL and bb.status<>'-5'
        AND aa.schedule_code = '{$scheduleCode}'
        ORDER BY aa.id, bb.id, cc.id;
        ";
        $data = $this->db->query($sql);
        if ($data->num_rows() > 0) {
            return $data->result();
        } else {
            return false;
        }
    }

    public function getBoardedVehicleByScheduleCode($scheduleCode)
    {
        $sql = "
        SELECT
        aa.id AS open_boarding_id,
        bb.id AS boarding_id,
        cc.id AS booking_id,
        cc.booking_code,
        aa.ship_id,
        dd.port_code,
        aa.boarding_code,
        aa.schedule_code,
        bb.ticket_number,
        bb.boarding_date,
        aa.shift_id,
        aa.shift_date
        FROM app.t_trx_open_boarding aa
        RIGHT JOIN app.t_trx_boarding_vehicle bb ON aa.boarding_code = bb.boarding_code
        LEFT JOIN app.t_trx_booking_vehicle cc ON bb.ticket_number = cc.ticket_number
        LEFT JOIN app.t_mtr_port dd ON aa.port_id = dd.id
        WHERE aa.status IS NOT NULL and bb.status<>'-5'
        AND aa.schedule_code = '{$scheduleCode}'
        ORDER BY aa.id, bb.id, cc.id;
        ";
        $data = $this->db->query($sql);
        if ($data->num_rows() > 0) {
            return $data->result();
        } else {
            return false;
        }
    }

    public function setSwitchShipData($header, $detail, $type)
    {


        switch ($type) {
            case 'passenger':
                $header_table = 'app.t_trx_switch_ship_all_passanger';
                $detail_table = 'app.t_trx_switch_ship_all_passanger_detail';
                break;

            case 'vehicle':
                $header_table = 'app.t_trx_switch_ship_all_vehicle';
                $detail_table = 'app.t_trx_switch_ship_all_vehicle_detail';
                break;
        }
        $this->db->trans_begin();
        try {

            //  save pindah
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

    public function cekSwitchShipAllAvailability($scheduleCode)
    {
        $sql = "
        SELECT COUNT(*) FROM (
        SELECT * FROM app.t_trx_switch_ship_all_passanger
        WHERE schedule_code = '{$scheduleCode}'
        UNION
        SELECT * FROM app.t_trx_switch_ship_all_vehicle
        WHERE schedule_code = '{$scheduleCode}'
        ) AS switch_ship_all;
        ";
        $data = $this->db->query($sql)->row();
        if ($data->count <= 0) {
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
        $this->db->insert($table, $data);
    }

    public function update_data($table, $data, $where)
    {
        $this->db->where($where);
        $this->db->update($table, $data);
    }

    public function delete_data($table, $data, $where)
    {
        $this->db->where($where);
        $this->db->delete($table, $data);
    }

    public function getScheduleCodeByTicketNumber($ticket_number)
    {
        $sql_schedule_code = "
        SELECT *
        FROM (
            SELECT aa.ticket_number, aa.boarding_code, bb.schedule_code
            FROM app.t_trx_boarding_passanger aa
            LEFT JOIN app.t_trx_open_boarding bb ON aa.boarding_code = bb.boarding_code
            WHERE aa.status = 1
            AND aa.ticket_number = '{$ticket_number}'
            UNION
            SELECT aa.ticket_number, aa.boarding_code, bb.schedule_code
            FROM app.t_trx_boarding_vehicle aa
            LEFT JOIN app.t_trx_open_boarding bb ON aa.boarding_code = bb.boarding_code
            WHERE aa.status = 1
            AND aa.ticket_number = '{$ticket_number}'
        ) AS all_boarding
        LIMIT 1;";
        $data_schedule_code = $this->db->query($sql_schedule_code)->row();
        if (empty($data_schedule_code)) {
            $schedule_code   = null;
        } else {
            $schedule_code   = $data_schedule_code->schedule_code;
        }
        return $schedule_code;
    }

    public function unboardAndUnbook_backup($ticket_numbers, $type='penumpang')
    {

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
        // $this->db->insert_batch("app.t_trx_status", $dataTrxStatus); 

        // mulai proses di db lokal        
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
        

        foreach ($ticket_numbers as $key => $value) //tes
        { 
                   
            $this->db->query( "update {$table_boarding} set
                                updated_on='".date('Y-m-d H:i:s')."', 
                                updated_by='".$this->session->userdata('username')."',
                                ticket_number=concat(ticket_number,'-','".$ymdhis."'),
                                status='-5'
                                where
                                ticket_number = '{$value}'
                            "  );           
        }


        if ($this->db->trans_status() === false) {
            // kalo di lokal aja udah gagal langsung rollback dan return error
            $this->db->trans_rollback();
            return false;
        }

        // mulai proses di cloud
        $cloud_db = $this->load->database('cloud', true);
        // $cloud_db->trans_begin();
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
                                ticket_number = '{$value}'
                    "  );                
        }

 
        return true;

        // if ($cloud_db->trans_status() === true && $this->db->trans_status() === true) {
        //     // kalo proses di cloud dan lokal sukses baru commit
        //     $cloud_db->trans_commit();
        //     $this->db->trans_commit();
        //     return true;
        // } else {
        //     // kalo salah satu proses cloud maupun lokal ada yang gagal, rollback dan return error
        //     $cloud_db->trans_rollback();
        //     $this->db->trans_rollback();
        //     return false;
        // }
    }

    public function unboardAndUnbook($ticket_numbers, $type='penumpang', $boarding_code="")
    {

        
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

        // print_r($dataTrxStatus); exit;
        $this->db->trans_begin();

        // insert batch ke t_trx_status
        // $this->db->insert_batch("app.t_trx_status", $dataTrxStatus);  take out sementara karna table belum tersedia 

        // proses unbook passenger di db lokal
        $this->db->set('status', 7); 
        $this->db->set('updated_by', $this->_username); 
        $this->db->set('updated_on', date('Y-m-d H:i:s')); 
        $this->db->where_in('ticket_number', $ticket_numbers);  
        $this->db->update($table_booking);   


        $ymdhis=date('ymdhis');
        
        foreach ($ticket_numbers as $key => $value) //tes
        { 

                   
            $this->db->query( "update {$table_boarding} set
                                updated_on='".date('Y-m-d H:i:s')."', 
                                updated_by='".$this->session->userdata('username')."',
                                ticket_number=concat(ticket_number,'-','".$ymdhis."'),
                                status='-5'
                                where
                                ticket_number = '{$value}'
                            "  );           
        }
        
        // update data manifest from data passanger in vehicle
        if ($type == 'kendaraan')
        {
            $this->db->query( "update app.t_trx_boarding_manifest_vehicle set
                updated_on='".date('Y-m-d H:i:s')."', 
                updated_by='".$this->session->userdata('username')."',
                ticket_number=concat(ticket_number,'-','".$ymdhis."'),
                status='-5'
                where
                boarding_code = '{$boarding_code}'
            "  );           
        }

        // mulai proses di cloud
        $cloud_db = $this->load->database('cloud', true);

        foreach ($ticket_numbers as $key => $value) 
        {        

            $cloud_db->query( "update {$table_boarding} set
                                updated_on='".date('Y-m-d H:i:s')."', 
                                updated_by='".$this->session->userdata('username')."',
                                ticket_number=concat(ticket_number,'-','".$ymdhis."'),
                                status='-5'
                                where
                                ticket_number = '{$value}'
                    "  );                
        }


        if($this->db->trans_status() === true)
        {
            if($cloud_db->trans_status() === true)
            {
                $cloud_db->trans_commit();
                $this->db->trans_commit();
                return true;
            }
            else
            {

                $cloud_db->trans_rollback();
                $this->db->trans_rollback();
                return false;
            }
        }
        else
        {
            $cloud_db->trans_rollback();
            $this->db->trans_rollback();
            return false;
        }
    }    
}
