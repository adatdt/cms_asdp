<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pids_model extends MY_Model
{
    function get_pids($port_id)
    {
        $sql = "SELECT 
                    dock.name dock_name, 
                    trx.* 
                FROM app.t_mtr_dock dock
                LEFT JOIN
                (SELECT 
                    b.NAME AS ship_name,
                     C.schedule_date,
                    c.dock_id,
                    C.ploting_date,
                    C.docking_date,
                    C.open_boarding_date,
                    C.close_boarding_date,
                    C.close_ramp_door_date,
                    C.sail_date
                FROM
                    app.t_trx_schedule C 
                JOIN app.t_mtr_schedule A ON A.schedule_code = C.schedule_code
                LEFT JOIN app.t_mtr_ship b ON C.ship_id = b.id
                WHERE
                    A.status = 1 AND C.status = 1
                    AND A.port_id = {$port_id}
                    AND ((A.schedule_date = CURRENT_DATE) OR (A.schedule_date <= CURRENT_DATE
                    AND ((ploting_date IS NOT NULL AND C.sail_date IS NULL) OR (docking_date IS NOT NULL AND C.sail_date IS NULL) OR (open_boarding_date IS NOT NULL AND C.sail_date IS NULL))))
                    AND (ploting_date IS NOT NULL OR docking_date IS NOT NULL OR open_boarding_date IS NOT NULL OR close_boarding_date IS NOT NULL OR close_ramp_door_date IS NOT NULL)
                AND sail_date IS NULL
                ORDER BY 
                    C.schedule_date ASC, 
                    C.sail_date DESC,
                    C.close_ramp_door_date ASC,
                    C.close_boarding_date ASC,
                    C.open_boarding_date ASC,
                    C.docking_date ASC,
                    C.ploting_date ASC,
                    C.created_on ASC,
                    A.docking_on ASC) trx ON trx.dock_id = dock.id
                WHERE 
                    dock.status = 1 
                    AND dock.port_id = {$port_id} 
                ORDER BY dock.id";

        return $this->db->query($sql)->result();
    }

    function get_video($port_id)
    {
        $sql = 'SELECT
                    path
                FROM
                    app.t_mtr_pids_video
                WHERE
                    port_id = ' . $port_id . '
                    AND status = 1
                ORDER BY
                    "order" ASC';

        return $this->db->query($sql)->result();
    }

    function get_text($port_id)
    {
        $sql = 'SELECT
                    text
                FROM
                    app.t_mtr_pids_text
                WHERE
                    port_id = ' . $port_id . '
                    AND status = 1
                ORDER BY
                    "order" ASC';

        return $this->db->query($sql)->result();
    }
}
