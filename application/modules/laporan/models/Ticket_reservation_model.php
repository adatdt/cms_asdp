<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ticket_reservation_model extends CI_Model
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
            'title' => 'Tiket Online Lintasan Pergi',
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
            'title' => 'Tiket IFCS Lintasan Pergi',
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

        $where = "WHERE bk.service_id = 2 AND sc.ID = 1 AND inv.transaction_type = 1 ";

        if ($channel) {
            $where .= " AND inv.channel = '{$channel}'";
        }

        if ($datefrom and $dateto) {
            $where .= " AND pay.payment_date::date between '{$datefrom}' and '{$dateto}'";
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
        COALESCE ( fare * produksi, 0 ) AS pendapatan
    FROM
        app.t_mtr_vehicle_class vehicle_type
    LEFT JOIN (
        SELECT
            ty. ID AS vehicle_class_id,
            COUNT (bps. ID) AS produksi,
            bps.fare
        FROM
		app.t_trx_invoice inv
			JOIN app.t_trx_booking bk ON inv.trans_number = bk.trans_number
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
            $ship_class = $this->enc->decode($this->input->post('ship_class'));
        } else {
            $datefrom   = $this->input->get('datefrom');
            $dateto     = $this->input->get('dateto');
            $port       = $this->enc->decode($this->input->get('port'));
            $ship_class = $this->enc->decode($this->input->get('ship_class'));
        }

        $where = "where bk.service_id = 1 and sc.id = 2 and inv.transaction_type = 1";

        if ($datefrom and $dateto) {
            $where .= " AND pay.payment_date::date between '{$datefrom}' and '{$dateto}'";
        }

        if ($ship_class) {
            $where .= " AND bk.ship_class = {$ship_class}";
        }

        if ($port) {
            $where .= " AND bps.origin = {$port}";
        }

        if ($channel) {
            $where .= " AND inv.channel = '{$channel}'";
        }

        $sql = "SELECT
        p_type.NAME AS golongan,
        COALESCE ( fare, 0 ) AS fare,
        COALESCE ( produksi, 0 ) AS produksi,
        COALESCE ( fare * produksi, 0 ) AS pendapatan 
    FROM
        app.t_mtr_passanger_type p_type
        LEFT JOIN (
        SELECT
            pt.ID AS passanger_type_id,
            COUNT ( bps.ID ) AS produksi,
            bps.fare 
        FROM
            app.t_trx_invoice inv
            JOIN app.t_trx_booking bk ON inv.trans_number = bk.trans_number
            JOIN app.t_trx_payment pay ON pay.trans_number = bk.trans_number
            JOIN app.t_trx_booking_passanger bps ON bps.booking_code = bk.booking_code
            JOIN app.t_mtr_passanger_type pt ON pt.ID = bps.passanger_type_id
            JOIN app.t_mtr_ship_class sc ON sc.ID = bk.ship_class
			{$where}
		GROUP BY
			bps.fare,
		pt.ID 
		) trx_passanger ON p_type.ID = trx_passanger.passanger_type_id";
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

    public function getRoute($port)
    {
        return $this->dbView->query("
            SELECT concat(b.name,'-',c.name) as route_name, a.* from app.t_mtr_rute a
            left join app.t_mtr_port b on a.origin=b.id and b.status<>'-5'
            left join app.t_mtr_port c on a.destination=c.id and c.status<>'-5'
            where a.origin ='{$port}' and a.status <>'-5'
        ");
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
