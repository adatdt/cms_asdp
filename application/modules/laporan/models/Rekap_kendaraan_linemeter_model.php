<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Rekap_kendaraan_linemeter_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function list_data($port, $datefrom, $ship_class, $time)
    {
        $where_port       = "";
        $where_port2       = "";
        $where_shift       = "";
        $where_ship_class = "";
        $where_time1 = "";
        $where_time2 = "";

        if ($port != "") {
            $where_port = " AND book.origin = $port";
            $where_port2 = " AND q.port_id= $port";
        }

        // if ($shift != "") {
        //     $where_shift = " AND ob.shift_id = $shift";
        // }

        if ($ship_class != "") {
            $where_ship_class = " AND book.ship_class = $ship_class";
            $where_ship_class2 = " AND q.ship_class = $ship_class";
        }

        if ($time != "") {
            // $where_time1 = " AND pass.depart_time_start  = '{$time}'";
            $where_time2 = " AND q.depart_time  = '{$time}'";
        }

        $sql = "SELECT
                    q.depart_date,
                    q.depart_time,
                    q.total_lm,
                    EXTRACT ( HOUR FROM q.depart_time ) AS hours,
                    (
                    COALESCE ( used_quota.used_lm, 0 ) - COALESCE ( expired.used_lm, 0 )) AS pengguna,
                    used_quota.total as produksi,
                    used_quota.name as golongan,
                    used_quota.wide_lm as lebar,
                    used_quota.length_lm as panjang,
                    used_quota.total_lm as luas,
                    (used_quota.total_lm * used_quota.total ) as jumlah
                FROM
                    app.t_trx_quota_pcm_vehicle q
                    LEFT JOIN (
                        SELECT
                            book.depart_date,
                            EXTRACT ( HOUR FROM pass.depart_time_start ) AS depart_time,
                            COALESCE ( COUNT ( book.ID ), 0 ) AS total,
                            COALESCE ( SUM ( vclass.total_lm ), 0 ) AS used_lm,
                            book.origin,
                            vclass.name,
                            vclass.wide_lm,
                            vclass.length_lm,
                            vclass.total_lm 
                        FROM
                            app.t_trx_booking book
                            JOIN app.t_trx_booking_vehicle pass ON pass.booking_code = book.booking_code
                            JOIN app.t_mtr_vehicle_class vclass ON vclass.ID = pass.vehicle_class_id 
                        WHERE
                            book.status IN ( 0, 1, 2 )
                            {$where_port} 
                            AND book.depart_date = '{$datefrom}' 
                            {$where_ship_class}
                            {$where_time1}
                        GROUP BY
                            book.depart_date,
                            book.origin,
                            vclass.name,
                            vclass.wide_lm,
                            vclass.length_lm,
                            vclass.total_lm, 
                            EXTRACT ( HOUR FROM pass.depart_time_start ) 
                        ) AS used_quota ON EXTRACT ( HOUR FROM q.depart_time ) = used_quota.depart_time AND q.depart_date = used_quota.depart_date
                    LEFT JOIN (
                        SELECT
                            book.depart_date,
                            EXTRACT ( HOUR FROM pass.depart_time_start ) AS depart_time,
                            COALESCE ( COUNT ( book.ID ), 0 ) AS total,
                            COALESCE ( SUM ( vclass.total_lm ), 0 ) AS used_lm,
                            book.origin 
                        FROM
                            app.t_trx_booking book
                            JOIN app.t_trx_invoice inv ON inv.trans_number = book.trans_number
                            JOIN app.t_trx_booking_vehicle pass ON pass.booking_code = book.booking_code
                            JOIN app.t_mtr_vehicle_class vclass ON vclass.ID = pass.vehicle_class_id 
                            WHERE
                            inv.status NOT IN ( 2 ) 
                            AND inv.due_date :: TIMESTAMP < now() 
                            AND book.depart_date = '{$datefrom}' 
                            {$where_port}  
                            {$where_ship_class}
                            {$where_time1}
                        GROUP BY
                            book.depart_date,
                            book.origin,
                            EXTRACT ( HOUR FROM pass.depart_time_start ) 
                        ) AS expired ON EXTRACT ( HOUR FROM q.depart_time ) = expired.depart_time AND q.depart_date = expired.depart_date 
                WHERE
                    q.depart_date = '{$datefrom}' {$where_port2} {$where_ship_class2} {$where_time2}
                    AND ( COALESCE ( used_quota.used_lm, 0 ) - COALESCE ( expired.used_lm, 0 )) NOT IN ( 0 )
                ";

        // $sql = "SELECT
        //             pcm.depart_date,
        //             pcm.depart_time,
        //             pcm.port_id,
        //             pcm.ship_class,
        //             A.used_lms AS digunakan,
        //             A.total as produksi,
        //             A.name AS golongan,
        //             A.wide_lm as lebar,
        //             A.length_lm as panjang,
        //             A.total_lm as luas,
        //             (A.total_lm * A.total) as jumlah,
        //             pcm.total_lm,
        //             (
        //             COALESCE ( pcm.total_lm, 0 ) - COALESCE ( A.used_lms, 0 )) AS ketersediaan 
        //         FROM
        //             app.t_trx_quota_pcm_vehicle pcm
        //             LEFT JOIN (
        //             SELECT
        //                 book.depart_date,
        //                 book.depart_time_start,
        //                 book.ship_class,
        //                 COALESCE ( COUNT ( book.ID ), 0 ) AS total,
        //                 COALESCE ( SUM ( vclass.total_lm ), 0 ) AS used_lms,
        //                 book.origin,
        //                 vclass.name,
        //                 vclass.wide_lm,
        //                 vclass.length_lm,
        //                 vclass.total_lm
        //             FROM
        //                 app.t_trx_booking book
        //                 JOIN app.t_trx_invoice inv ON inv.trans_number = book.trans_number
        //                 JOIN app.t_trx_booking_vehicle pass ON pass.booking_code = book.booking_code
        //                 JOIN app.t_mtr_vehicle_class vclass ON vclass.ID = pass.vehicle_class_id 
        //             WHERE
        //                 book.trans_number NOT IN (
        //                 SELECT
        //                     trans_number 
        //                 FROM
        //                     app.t_trx_invoice inc 
        //                 WHERE
        //                     inc.status NOT IN ( 2 ) 
        //                     AND inc.due_date :: TIMESTAMP < now() 
        //                     AND inc.trans_number = book.trans_number 
        //                 ) 
        //             GROUP BY
        //                 book.origin,
        //                 book.depart_date,
        //                 book.depart_time_start,
        //                 book.ship_class,
        //                 vclass.name,
        //                 vclass.wide_lm,
        //                 vclass.length_lm,
        //                 vclass.total_lm	
        //             ) AS A ON A.origin = pcm.port_id 
        //             AND A.depart_date = pcm.depart_date 
        //             AND A.depart_time_start = pcm.depart_time 
        //             AND A.ship_class = pcm.ship_class 
        //         WHERE
        //             pcm.depart_date = '{$datefrom}' 
        //             AND pcm.depart_time BETWEEN '00:00:00' 
        //             AND '23:59:59' 
        //             {$where_ship_class}
        //             {$where_port}
        //             AND A.name is NOT NULL 
        //         ORDER BY
        //             pcm.depart_time ASC";
        // echo $sql; die();

        $cek_ada = $this->db->query($sql)->num_rows();

        if ($cek_ada > 0) {
            return $this->db->query($sql)->result();
        } else {
            return false;
        }
        // }
    }

    public function getport()
    {
        return $this->db->query("SELECT * FROM app.t_mtr_port WHERE status=1")->result();
    }

    public function getclass()
    {
        return $this->db->query("SELECT * FROM app.t_mtr_ship_class WHERE status=1")->result();
    }

    public function get_lintasan($port, $datefrom, $dateto, $ship_class, $shift)
    {
        $where_port = "";
        $where_ship_class = "";

        if ($port != "") {
            $where_port = "AND UP.port_id = $port";
        }

        if ($ship_class != "") {
            $where_ship_class = "AND BO.ship_class = $ship_class";
        }

        $sql = "SELECT DISTINCT
					PO.name as port_origin,
					PD.name as port_destination
				FROM
					app.t_trx_assignment_user_pos UP
					JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
					JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number $where_ship_class
					JOIN app.t_mtr_port PO ON PO.id = BO.origin
					JOIN app.t_mtr_port PD ON PD.id = BO.destination
				WHERE
					UP.assignment_date = '{$datefrom}'
					$where_port";

        if ($this->db->query($sql)->num_rows() > 0) {
            return $this->db->query($sql);
        } else {
            return false;
        }
    }

    public function get_team($port, $datefrom, $dateto, $ship_class)
    {
        $where_port = "";
        $where_ship_class = "";

        if ($port != "") {
            $where_port = "AND UP.port_id = $port";
        }

        if ($ship_class != "") {
            $where_ship_class = "AND BO.ship_class = $ship_class";
        }

        $sql = "SELECT DISTINCT
					PO.name as origin,
					T.team_name,
					B.branch_name
				FROM
					app.t_trx_assignment_user_pos UP
					JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
					JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number $where_ship_class
					JOIN app.t_mtr_port PO ON PO.id = BO.origin
					JOIN app.t_mtr_port PD ON PD.id = BO.destination
					JOIN core.t_mtr_team T ON T.team_code = UP.team_code
					JOIN app.t_mtr_branch B ON B.branch_code = BO.branch_code
				WHERE
					UP.assignment_date = '{$datefrom}'
					$where_port";

        if ($this->db->query($sql)->num_rows() == 1) {
            return $this->db->query($sql)->row();
        } else {
            return false;
        }
    }

    public function getClassBySession($type = 'cek')
    {
        $session_shift_class = $this->session->userdata('ship_class_id');
        $sql = "SELECT * FROM app.t_mtr_ship_class WHERE status=1";

        if ($type == 'option') {
            $result = array();
            if ($session_shift_class != '') {
                $data = $this->db->query($sql . " and id = {$session_shift_class}");
                if ($data->num_rows() > 0) {
                    $getData  = $data->row();
                    $result[] = array('id' => $getData->id, 'name' => $getData->name);
                }
            } else {
                $data     = $this->db->query($sql)->result();
                // $result[] = array('id' => '', 'name' => 'Semua');
                foreach ($data as $key => $value) {
                    $result[] = array('id' => $value->id, 'name' => $value->name);
                }
            }
            return $result;
        } else {
            if ($session_shift_class != '') {
                $data = $this->db->query($sql . " and id = {$session_shift_class}");
                if ($data->num_rows() > 0) {
                    $getData = $data->row();
                    return array('id' => $getData->id, 'name' => $getData->name);
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}
