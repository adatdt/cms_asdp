<?php

class Ticket_goshow_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->_module = 'laporan/ticket_goshow';
    }

    public function ticketGoshowList() {
        $start          = $this->input->post('start');
        $length         = $this->input->post('length');
        $draw           = $this->input->post('draw');
        $search         = $this->input->post('search');
        $order          = $this->input->post('order');
        $order_column   = $order[0]['column'];
        $order_dir      = strtoupper($order[0]['dir']);
        $incomegoshow   = $this->input->post('incomegoshow');
        $dateTo         = $this->input->post('dateTo');
        
        $field = array(
            1 => 'origin',
            2 => 'destination',
    		3 => 'payment_date',
    		4 => 'total',
        );

        $order_column = $field[$order_column];
    
        if(empty($incomegoshow) and empty($dateTo)){
            $where = " where to_char(a.payment_date,'yyyy-mm')=to_char(timestamp 'now','yyyy-mm') ";
        }

        else if ((!empty($incomegoshow) and empty($dateTo))||(empty($incomegoshow) and !empty($dateTo))){
            $where = " where (to_char(a.payment_date,'yyyy-mm-dd')='$incomegoshow') or  (to_char(a.payment_date,'yyyy-mm-dd')='$dateTo')";
        }

        else {
            $where = " where to_char(a.payment_date,'yyyy-mm-dd') between '$incomegoshow' and  '$dateTo'";   
        }

        $sql = "
            select g.id as origin_id, h.id as dest_id, g.name as origin, h.name as destination,  to_char(a.payment_date,'yyyy-mm-dd')as payment_date,sum(a.amount) as total
            from app.t_trx_payment_emoney a
            join app.t_trx_booking c on a.booking_id =c.id
            join app.t_mtr_schedule_time d on c.schedule_time_id=d.id
            join app.t_mtr_schedule f on d.schedule_id= f.id
            join app.t_mtr_port g on f.origin_port_id=g.id
            join app.t_mtr_port h on f.destination_port_id=h.id
            {$where}
            group by g.name, h.name, g.id, h.id, to_char(a.payment_date,'yyyy-mm-dd')		
		";

        $query          = $this->db->query($sql);
        $records_total  = $query->num_rows();
        $sql           .= " ORDER BY " . $order_column . " {$order_dir}";

        // if ($length != -1) {
        //   $sql .=" LIMIT {$length} OFFSET {$start};";
        // }

        $query      = $this->db->query($sql);
        $rows_data  = $query->result();
        $rows       = array();
        $i          = ($start + 1);
        $total_income = 0;

        foreach ($rows_data as $row) {
            $passanger = $this->db->query("select *  from app.t_trx_payment_emoney aa
                join app.t_trx_booking ab on aa.booking_code=ab.code
                join app.t_trx_booking_passanger ac on ab.id=ac.booking_id
                JOIN app.t_mtr_schedule_time d ON ab.schedule_time_id = d.id
                JOIN app.t_mtr_schedule f ON d.schedule_id = f.id
                JOIN app.t_mtr_port g ON f.origin_port_id = g.id
                JOIN app.t_mtr_port h ON f.destination_port_id = h.id
                where ab.service_id=1 and to_char(aa.payment_date,'yyyy-mm-dd')='{$row->payment_date}' AND g.id = {$row->origin_id} AND h.id = {$row->dest_id}")->num_rows();
            
            $vehicle = $this->db->query("select *  from app.t_trx_payment_emoney aa
                join app.t_trx_booking ab on aa.booking_code=ab.code
                join app.t_trx_booking_vehicle ac on ab.id=ac.booking_id
                JOIN app.t_mtr_schedule_time d ON ab.schedule_time_id = d.id
                JOIN app.t_mtr_schedule f ON d.schedule_id = f.id
                JOIN app.t_mtr_port g ON f.origin_port_id = g.id
                JOIN app.t_mtr_port h ON f.destination_port_id = h.id
                where to_char(aa.payment_date,'yyyy-mm-dd')='{$row->payment_date}' AND g.id = {$row->origin_id} AND h.id = {$row->dest_id}")->num_rows();

            $count             = $passanger+$vehicle;
            $total_income      += $row->total;
            $row->number       = $i;
            $get               = "{$row->payment_date}|{$row->origin_id}|{$row->dest_id}";
            $param             = $this->enc->encode($get);
            $row->payment_date = format_date($row->payment_date);
            $row->total        = idr_currency($row->total);
            $row->count        = idr_currency($count);
            $detail_url        = site_url($this->_module."/detail/{$param}");
            $row->actions      = generate_button_new($this->_module, 'detail', $detail_url);
                    
            $rows[] = $row;
            unset($row->origin_id);
            unset($row->dest_id);
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

     public function getDetail($date,$origin,$dest){
        $start          = $this->input->post('start');
        $length         = $this->input->post('length');
        $draw           = $this->input->post('draw');
        $order          = $this->input->post('order');
        $order_column   = $order[0]['column'];
        $order_dir      = strtoupper($order[0]['dir']);

        $field = array(
            2 => 'name',
            3 => 'production',
            4 => 'income'
        );

        $order_column = $field[$order_column];

        $sql = "SELECT 
                pt.id, pt.name, '{$date}' AS date, 

                (SELECT COUNT(cc.id) FROM app.t_trx_payment_emoney aa
                JOIN app.t_trx_booking bb ON bb.id = aa.booking_id AND service_id = 1
                JOIN app.t_trx_booking_passanger cc ON cc.booking_id = bb.id AND passanger_type_id = pt.id
                JOIN app.t_mtr_schedule_time d ON bb.schedule_time_id = d.id
                JOIN app.t_mtr_schedule f ON d.schedule_id = f.id
                JOIN app.t_mtr_port g ON f.origin_port_id = g.id
                JOIN app.t_mtr_port h ON f.destination_port_id = h.id
                WHERE to_char(aa.payment_date,'yyyy-mm-dd') = '{$date}' AND g.id = $origin AND h.id = $dest) AS production, 

                (SELECT SUM(fare) FROM app.t_trx_payment_emoney aa
                JOIN app.t_trx_booking bb ON bb.id = aa.booking_id AND service_id = 1
                JOIN app.t_trx_booking_passanger cc ON cc.booking_id = bb.id AND passanger_type_id = pt.id
                JOIN app.t_mtr_schedule_time d ON bb.schedule_time_id = d.id
                JOIN app.t_mtr_schedule f ON d.schedule_id = f.id
                JOIN app.t_mtr_port g ON f.origin_port_id = g.id
                JOIN app.t_mtr_port h ON f.destination_port_id = h.id
                WHERE to_char(aa.payment_date,'yyyy-mm-dd') = '{$date}' AND g.id = $origin AND h.id = $dest) AS income
                FROM app.t_mtr_passanger_type pt WHERE pt.status = 1

                UNION ALL

                SELECT vc.id, vc.name, '{$date}' AS date, 

                (SELECT COUNT(cc.id) FROM app.t_trx_payment_emoney aa
                JOIN app.t_trx_booking bb ON bb.id = aa.booking_id
                JOIN app.t_trx_booking_vehicle cc ON cc.booking_id = bb.id AND vehicle_class_id = vc.id 
                JOIN app.t_mtr_schedule_time d ON bb.schedule_time_id = d.id
                JOIN app.t_mtr_schedule f ON d.schedule_id = f.id
                JOIN app.t_mtr_port g ON f.origin_port_id = g.id
                JOIN app.t_mtr_port h ON f.destination_port_id = h.id
                WHERE to_char(aa.payment_date,'yyyy-mm-dd') = '{$date}' AND g.id = $origin AND h.id = $dest) AS production,

                (SELECT SUM(fare) FROM app.t_trx_payment_emoney aa
                JOIN app.t_trx_booking bb ON bb.id = aa.booking_id
                JOIN app.t_trx_booking_vehicle cc ON cc.booking_id = bb.id AND vehicle_class_id = vc.id
                JOIN app.t_mtr_schedule_time d ON bb.schedule_time_id = d.id
                JOIN app.t_mtr_schedule f ON d.schedule_id = f.id
                JOIN app.t_mtr_port g ON f.origin_port_id = g.id
                JOIN app.t_mtr_port h ON f.destination_port_id = h.id
                WHERE to_char(aa.payment_date,'yyyy-mm-dd') = '{$date}' AND g.id = $origin AND h.id = $dest) AS income
                FROM app.t_mtr_vehicle_class vc WHERE vc.status = 1

                ORDER BY {$order_column} {$order_dir}";

        $query          = $this->db->query($sql);
        $records_total  = $query->num_rows();
        $query          = $this->db->query($sql);
        $rows_data      = $query->result();
        $rows           = array();
        $i              = ($start + 1);
        $total_income   = 0;

        foreach ($rows_data as $row) {
            $row->number = $i;
            
            if($row->income == null){
                $row->income = 0;
            }

            $total_income += $row->income;
            $row->income   = idr_currency($row->income);
            $rows[]        = $row;
            $i++;
        }

        return array(
            'draw' => $draw,
            'recordsTotal' => $records_total,
            'recordsFiltered' => $records_total,
            'data' => $rows,
            'total' => idr_currency($total_income)
        );
    }

    public function getPort($id){
        $sql = "SELECT name FROM app.t_mtr_port WHERE id = $id";
        return $this->db->query($sql)->row()->name;
    }
}
