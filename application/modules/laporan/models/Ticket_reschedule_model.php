<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ticket_reschedule_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_data()
    {
        if ($this->input->post()) {
            $channel = $this->input->post('channel');
        } else {
            $channel = $this->input->get('channel');
        }

        $return_data = array();

        if ($channel == 'web') {
            array_push($return_data, $this->get_channel_web());
        } elseif ($channel == 'ifcs') {
            array_push($return_data, $this->get_channel_ifcs());
        } else {
            array_push($return_data, $this->get_channel_web(), $this->get_channel_ifcs());
        }

        return $return_data;
    }

    private function get_channel_web()
    {
        return array(
            'title' => 'Tiket Online',
            'data'  => array(
                array(
                    'title' => 'I. Penumpang',
                    'data'  => $this->dbView->query($this->get_penumpang('web'))->result()
                ),
                array(
                    'title' => 'II. Kendaraan',
                    'data'  => $this->dbView->query($this->get_kendaraan('web'))->result()
                ),
            )
        );
    }

    private function get_channel_ifcs()
    {
        return array(
            'title' => 'Tiket IFCS',
            'data'  => array(
                array(
                    'title' => 'I. Penumpang',
                    'data'  => $this->dbView->query($this->get_penumpang('ifcs'))->result()
                ),
                array(
                    'title' => 'II. Kendaraan',
                    'data'  => $this->dbView->query($this->get_kendaraan('ifcs'))->result()
                ),
            )
        );
    }

    private function get_kendaraan($channel)
    {
        if ($this->input->post()) {
            $datefrom   = $this->input->post('datefrom');
            $dateto     = $this->input->post('dateto');
            $port       = $this->enc->decode($this->input->post('port'));
            $cek_sc     = $this->getClassBySession();
            if ($cek_sc == false) {
                $ship_class = $this->enc->decode($this->input->post('ship_class'));
            } else {
                $ship_class = $cek_sc['id'];
            }
        } else {
            $datefrom   = $this->input->get('datefrom');
            $dateto     = $this->input->get('dateto');
            $port       = $this->enc->decode($this->input->get('port'));
            $cek_sc     = $this->getClassBySession();
            if ($cek_sc == false) {
                $ship_class = $this->enc->decode($this->input->get('ship_class'));
            } else {
                $ship_class = $cek_sc['id'];
            }
        }

        $where = "WHERE bk.service_id = 2 AND sc.ID = 1 ";

        if ($channel) {
            $where .= " AND inv.channel = '{$channel}'";
        }

        if ($datefrom and $dateto) {
            $where .= " AND rf.created_on::date between '{$datefrom}' and '{$dateto}'";
        }

        if ($port) {
            $where .= " AND bps.origin = $port";
        }

        if ($ship_class) {
            $where .= " AND bk.ship_class = $ship_class";
        }


        $sql = "SELECT
        vehicle_type. NAME AS golongan,
        COALESCE (fare, 0) AS fare,
        COALESCE (produksi, 0) AS produksi,
        COALESCE (adm_fee, 0) AS adm_fee,
        COALESCE (reschedule_fee, 0) AS reschedule_fee,
        COALESCE (charge_amount, 0) AS charge_amount,
        COALESCE (charge_amount + reschedule_fee, 0) AS total_amount
    FROM
        app.t_mtr_vehicle_class vehicle_type
    LEFT JOIN (
        SELECT
            ty. ID AS vehicle_class_id,
            COUNT (bps. ID) AS produksi,
            bps.fare,
            SUM (rf.adm_fee) AS adm_fee,
            SUM (rf.reschedule_fee) AS reschedule_fee,
            SUM (rf.charge_amount) AS charge_amount
        FROM
            app.t_trx_reschedule rf
            JOIN app.t_trx_booking bk ON rf.booking_code = bk.booking_code
            JOIN app.t_trx_invoice inv ON inv.trans_number = bk.trans_number
            JOIN app.t_trx_payment pay ON pay.trans_number = bk.trans_number
            JOIN app.t_trx_booking_vehicle bps ON bps.booking_code = bk.booking_code
            JOIN app.t_mtr_vehicle_class ty ON ty.ID = bps.vehicle_class_id
            JOIN app.t_mtr_ship_class sc ON sc.ID = bk.ship_class 
        {$where}
        GROUP BY
            bps.fare,
            ty. ID
    ) trx_vehicle ON vehicle_type. ID = trx_vehicle.vehicle_class_id
    ORDER BY
        vehicle_type. NAME ASC";

        return $sql;
    }

    private function get_penumpang($channel)
    {
        if ($this->input->post()) {
            $datefrom   = $this->input->post('datefrom');
            $dateto     = $this->input->post('dateto');
            $port       = $this->enc->decode($this->input->post('port'));
            $cek_sc     = $this->getClassBySession();
            if ($cek_sc == false) {
                $ship_class = $this->enc->decode($this->input->post('ship_class'));
            } else {
                $ship_class = $cek_sc['id'];
            }
        } else {
            $datefrom   = $this->input->get('datefrom');
            $dateto     = $this->input->get('dateto');
            $port       = $this->enc->decode($this->input->get('port'));
            $cek_sc     = $this->getClassBySession();
            if ($cek_sc == false) {
                $ship_class = $this->enc->decode($this->input->get('ship_class'));
            } else {
                $ship_class = $cek_sc['id'];
            }
        }


        $where1 = "WHERE bp.service_id = 1";

        $where2 = "where bk.service_id = 1 and sc.id = 2";

        if ($datefrom and $dateto) {
            $where1 .= " AND  rf.created_on::date between '{$datefrom}' and '{$dateto}'";
            $where2 .= " AND rf.created_on::date between '{$datefrom}' and '{$dateto}'";
        }

        if ($ship_class) {
            $where1 .= " AND bk.ship_class = {$ship_class}";
            $where2 .= " AND bk.ship_class = {$ship_class}";
        }

        if ($port) {
            $where1 .= " AND bp.origin = {$port}";
            $where2 .= " AND bps.origin = {$port}";
        }

        if ($channel) {
            $where2 .= " AND inv.channel = '{$channel}'";
        }

        $sql = "SELECT
        p_type.NAME AS golongan,
        COALESCE ( fare, 0 ) AS fare,
        COALESCE ( produksi, 0 ) AS produksi,
        COALESCE ( adm_fee, 0 ) AS adm_fee,
        COALESCE ( reschedule_fee, 0 ) AS reschedule_fee,
        COALESCE ( reschedule_fee + adm_fee, 0 ) AS total_amount 
    FROM
        app.t_mtr_passanger_type p_type
        LEFT JOIN (
            select	
                    pt.id as passanger_type_id,
                    count(bps.id) as produksi,
                    bps.fare,
                    SUM ( fee.adm_fee ) AS adm_fee,
					SUM ( fee.reschedule_fee ) AS reschedule_fee,
					SUM ( fee.charge_amount ) AS charge_amount,
					SUM ( fee.adm_fee ) AS adm_fee2 
                FROM
                app.t_trx_reschedule rf
					JOIN app.t_trx_booking bk ON rf.booking_code = bk.booking_code
					JOIN app.t_trx_invoice inv ON inv.trans_number = bk.trans_number
					JOIN app.t_trx_payment pay ON pay.trans_number = bk.trans_number
					JOIN app.t_trx_booking_passanger bps ON bps.booking_code = bk.booking_code
					JOIN app.t_mtr_passanger_type pt ON pt.ID = bps.passanger_type_id
					JOIN app.t_mtr_ship_class sc ON sc.ID = bk.ship_class
					LEFT JOIN (SELECT *
                        FROM (
                            SELECT bp.ticket_number,
                                    bp.booking_code, 
                                    bp.fare,
                                    rf.adm_fee,
                                    rf.reschedule_fee,
                                    rf.charge_amount,
                                    rank() over (partition by bp.booking_code order by bp.id asc) as rank
                            FROM app.t_trx_booking_passanger  bp
                                JOIN app.t_trx_booking bk ON bp.booking_code = bk.booking_code
                                join app.t_trx_reschedule rf on rf.booking_code = bp.booking_code 
                                JOIN app.t_mtr_ship_class sc ON sc.id = bk.ship_class 
                            {$where1}
                        ) t
                        where t.rank = 1) fee on fee.ticket_number = bps.ticket_number	
            {$where2} 
            group by bps.fare,pt.id 
    )trx_passanger ON p_type.ID = trx_passanger.passanger_type_id";
        return $sql;
    }

    public function getport()
    {
        return $this->dbView->query("SELECT * FROM app.t_mtr_port WHERE status=1")->result();
    }

    public function getclass()
    {
        return $this->dbView->query("SELECT * FROM app.t_mtr_ship_class WHERE status=1")->result();
    }

    public function getClassBySession($type = 'cek')
    {
        $session_shift_class = $this->session->userdata('ship_class_id');
        $sql = "SELECT * FROM app.t_mtr_ship_class WHERE status=1";

        if ($type == 'option') {
            $result = array();
            if ($session_shift_class != '') {
                $data = $this->dbView->query($sql . " and id = {$session_shift_class}");
                if ($data->num_rows() > 0) {
                    $getData  = $data->row();
                    $result[] = array('id' => $getData->id, 'name' => $getData->name);
                }
            } else {
                $data     = $this->dbView->query($sql)->result();
                $result[] = array('id' => '', 'name' => 'Semua');
                foreach ($data as $key => $value) {
                    $result[] = array('id' => $value->id, 'name' => $value->name);
                }
            }
            return $result;
        } else {
            if ($session_shift_class != '') {
                $data = $this->dbView->query($sql . " and id = {$session_shift_class}");
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
