<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Perbandingan_linemeter_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function list_data($port, $golongan, $ket, $datefrom, $dateto, $ship_class, $time)
    {
        $where              = "";
        $where_ket          = "";

        if ($datefrom and $dateto != "") {
            $where .= " AND depart_date::date between '{$datefrom}' and '{$dateto}'";
        }

        if ($port != "") {
            $where .= " AND origin = $port";
        }

        if ($ship_class != "") {
            $where .= " AND ship_class = $ship_class";
            $where_ship_class2 = " AND q.ship_class = $ship_class";
        }

        if ($golongan != "") {
            $where .= " AND vehicle_class_id = $golongan";
        }
        if ($ket != "") {
            $where_ket = " AND keterangan = '$ket' ";
        } 

        if ($time != "") {
            // $where_time1 = " AND pass.depart_time_start  = '{$time}'";
            $where .= " AND depart_time_start  = '{$time}'";
        }

        $sql = "SELECT
                    * 
                FROM
                    (
                    SELECT
                        tbv.booking_code,
                        tbv.depart_date,
                        tbv.depart_time_start,
                        mvc.NAME,
                        tbv.origin,
                        tbv.ship_class,
                        tbv.vehicle_class_id,
                        tbv.booking_code AS kode_booking,
                        mvc.wide_lm AS lebar_default,
                        mvc.length_lm AS panjang_default,
                        mvc.wide_lm AS lebar_pengisian,
                        tbv.length_booking AS panjang_pengisian,
                        tbv.width AS lebar_real,
                        tbv.length AS panjang_real,
                        '0' AS keterangan 
                    FROM
                        app.t_trx_booking_vehicle tbv
                        LEFT JOIN app.t_mtr_vehicle_class mvc ON mvc.ID = tbv.vehicle_class_id 
                    WHERE
                    tbv.booking_code NOT IN ( SELECT booking_code FROM app.t_trx_under_paid WHERE booking_code IS NOT NULL )
                    and tbv.booking_code NOT IN (SELECT booking_code FROM app.t_trx_over_paid WHERE booking_code IS NOT NULL )
                UNION ALL
                    SELECT
                        op.booking_code,
                        tbv.depart_date,
                        tbv.depart_time_start,
                        mvc.NAME,
                        tbv.origin,
                        tbv.ship_class,
                        tbv.vehicle_class_id,
                        tbv.booking_code AS kode_booking,
                        mvc.wide_lm AS lebar_default,
                        mvc.length_lm AS panjang_default,
                        mvc.wide_lm AS lebar_pengisian,
                        tbv.length_booking AS panjang_pengisian,
                        tbv.width AS lebar_real,
                        tbv.LENGTH AS panjang_real,
                        '1' AS keterangan 
                    FROM
                        app.t_trx_booking_vehicle tbv
                        LEFT JOIN app.t_mtr_vehicle_class mvc ON mvc.ID = tbv.vehicle_class_id
                        LEFT JOIN app.t_trx_over_paid op ON tbv.booking_code = op.booking_code 
                    WHERE
                        tbv.booking_code = op.booking_code 
                        AND tbv.booking_code NOT IN( SELECT booking_code FROM app.t_trx_under_paid where booking_code is not null ) 
                    UNION ALL
                    SELECT
                        underpaid.booking_code,
                        tbv.depart_date,
                        tbv.depart_time_start,
                        mvc.NAME,
                        tbv.origin,
                        tbv.ship_class,
                        tbv.vehicle_class_id,
                        tbv.booking_code AS kode_booking,
                        mvc.wide_lm AS lebar_default,
                        mvc.length_lm AS panjang_default,
                        mvc.wide_lm AS lebar_pengisian,
                        tbv.length_booking AS panjang_pengisian,
                        tbv.width AS lebar_real,
                        tbv.LENGTH AS panjang_real,
                        '2' AS keterangan 
                    FROM
                        app.t_trx_booking_vehicle tbv
                        LEFT JOIN app.t_mtr_vehicle_class mvc ON mvc.ID = tbv.vehicle_class_id
                        LEFT JOIN (
                            SELECT
                                tup.booking_code 
                            FROM
                                app.t_trx_under_paid tup
                                JOIN app.t_trx_payment ttp ON ttp.trans_number = tup.trans_number 
                            GROUP BY
                                tup.booking_code 
                            ) AS underpaid ON tbv.booking_code = underpaid.booking_code  
                    WHERE
                        tbv.booking_code = underpaid.booking_code 
                        AND tbv.booking_code NOT IN( SELECT booking_code FROM app.t_trx_over_paid where booking_code is not null ) 
                    
                    ) A 
                WHERE
                    booking_code IS NOT NULL $where_ket $where
                ORDER BY depart_date, depart_time_start ";

        $cek_ada = $this->db->query($sql)->num_rows();

        if ($cek_ada > 0) {
            return $this->db->query($sql)->result();
        } else {
            return false;
        }
    }

    public function getport()
    {
        return $this->db->query("SELECT * FROM app.t_mtr_port WHERE status=1 ORDER BY name")->result();
    }

    public function getgolongan()
    {
        return $this->db->query("SELECT * FROM app.t_mtr_vehicle_class WHERE status=1 ORDER BY name")->result();
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
                $result[] = array('id' => '', 'name' => 'Semua');
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
