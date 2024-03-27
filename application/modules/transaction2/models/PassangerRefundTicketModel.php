<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     fajar <alfajrduta@gmail.com>
 * @copyright  2020
 *
 */

class PassangerRefundTicketModel extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->_module = 'transaction2/passangerRefundTicket';
    }

    public function dataList()
    {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');
        $shipClass = $this->enc->decode($this->input->post('shipClass'));
        $route = $this->enc->decode($this->input->post('route'));
        $bank = $this->enc->decode($this->input->post('bank'));
        $statusRefunded = $this->input->post('statusRefunded');
        $dateTo = trim($this->input->post('dateTo'));
        $dateFrom = trim($this->input->post('dateFrom'));
        $paymentDateFrom = trim($this->input->post('paymentDateFrom'));
        $paymentDateTo = trim($this->input->post('paymentDateTo'));
        $route = $this->enc->decode($this->input->post('route'));
        $order = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir = strtoupper($order[0]['dir']);
        $iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

        // cek app get_identity_app
        if ($this->get_identity_app() == 0) {
            if (!empty($this->session->userdata("port_id"))) {
                $port_origin = $this->session->userdata("port_id");
            } else {
                $port_origin = $this->enc->decode($this->input->post('port_origin'));
            }
        } else {
            $port_origin = $this->get_identity_app();
        }

        $field = array(
            0   => 'rf.id',
            1   => "booking_code",
            2   => "ticket_number",
            3   => "passanger_type_name",
            4   => "ship_class_name",
            5   => "fare",
            6   => "payment_date",
            7   => "depart_date",
            8   => "route_name",
            9   => "status_booking",
            10  => "status_refund",
            11  => "tanggal_approve",
            12  => "total_biaya",
            13  => "biaya_admin",
            14  => "biaya_refund",
            15  => "bank_tujuan",
            16  => "no_rekening",
            17  => "biaya_refund",
        );

        $order_column = $field[$order_column];

        $where = " WHERE bk.service_id = 1 and (date(bk.depart_date) between '" . $dateFrom . "' and '" . $dateTo . "' ) ";


        if (!empty($port_origin)) {
            $where .= " and (bk.origin ='{$port_origin}') ";
        }

        if (!empty($shipClass)) {
            $where .= " and (bk.ship_class ='{$shipClass}') ";
        }

        if (!empty($bank)) {
            $where .= " and (upper(rf.bank) =upper('{$bank}')) ";
        }

        if (!empty($statusRefunded)) {
			if ($statusRefunded == 1) {
				$where .= " and (rf.status = 1 AND (rf.is_approved IS NULL OR rf.is_approved IS NULL))";
			} elseif ($statusRefunded == 2) {
				$where .= " and (rf.status = 1 AND rf.is_approved IS TRUE)";
			} elseif ($statusRefunded == 3) {
				$where .= "and (rf.status = 3 AND rf.is_approved IS TRUE)";
			} elseif ($statusRefunded == 4) {
				$where .= "and (rf.status = 2 AND rf.is_approved IS TRUE)";
			} else {
				$where .= "";
			}
		}

        if (!empty($route)) {
            $getRoute = $this->select_data("app.t_mtr_rute", " where id={$route} ")->row();

            $where .= " and ( bk.origin ='{$getRoute->origin}'  and bk.destination='{$getRoute->destination}' ) ";
        }

        if (!empty($paymentDateFrom) and empty($paymentDateTo)) {
            $where .= " and (date(payment_date)='{$paymentDateFrom}' ) ";
        }

        if (empty($paymentDateFrom) and !empty($paymentDateTo)) {
            $where .= " and (date(payment_date)='{$paymentDateTo}' ) ";
        }

        if (!empty($paymentDateFrom) and !empty($paymentDateTo)) {
            $where .= " and (date(payment_date) between '{$paymentDateFrom}' and  '{$paymentDateTo}'  ) ";
        }

        if (!empty($search['value'])) {
            $where .= " and (
							rf.booking_code ilike '%" . $iLike . "%'
							or bps.ticket_number ilike '%" . $iLike . "%'
			) ";
        }

        $sql            = $this->qry($where);
        $query          = $this->dbView->query($sql);
        $records_total  = $query->num_rows();
        $sql           .= " ORDER BY  " . $order_column . " {$order_dir}";

        if ($length != -1) {
            $sql .= " LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->dbView->query($sql);
        $rows_data = $query->result();

        $rows     = array();
        $i      = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number        = $i;
            $row->fare          = idr_currency($row->fare);
            $row->payment_date  = empty($row->payment_date) ? "" : format_date($row->payment_date) . " " . format_time($row->payment_date);
            $row->refund_time   = empty($row->refund_time) ? "" : format_date($row->refund_time) . " " . format_time($row->refund_time);
            $row->check_in_time = empty($row->check_in_time) ? "" : format_date($row->check_in_time) . " " . format_time($row->check_in_time);
            $row->gate_in_time  = empty($row->gate_in_time) ? "" : format_date($row->gate_in_time) . " " . format_time($row->gate_in_time);
            $row->boarding_time = empty($row->boarding_time) ? "" : format_date($row->boarding_time) . " " . format_time($row->boarding_time);
            $row->time_order    = empty($row->time_order) ? "" : format_date($row->time_order) . " " . format_time($row->time_order);
            $row->keberangkatan = empty($row->keberangkatan) ? "" : format_date($row->keberangkatan);
            $row->total_biaya   = idr_currency($row->total_biaya);
            $row->biaya_admin   = idr_currency($row->biaya_admin);
            $row->biaya_refund  = idr_currency($row->biaya_refund);
            $row->total_amount  = idr_currency($row->total_amount);

            $row->no = $i;
            $rows[] = $row;
            unset($row->id);
            $i++;
        }

        return array(
            'draw'              => $draw,
            'recordsTotal'      => $records_total,
            'recordsFiltered'   => $records_total,
            'data'              => $rows
        );
    }


    public function select_data($table, $where = "")
    {
        return $this->dbView->query("select * from $table $where");
    }

    public function getRoute($portId)
    {
        return $this->dbView->query("
            SELECT concat(b.name,'-',c.name) as route_name, a.* from app.t_mtr_rute a
            left join app.t_mtr_port b on a.origin=b.id and b.status<>'-5'
            left join app.t_mtr_port c on a.destination=c.id and c.status<>'-5'
            where a.origin ='{$portId}' and a.status <>'-5'
        ");
    }

    public function qry($where = "", $order = "")
    {
        return $data = "
        SELECT
            bk.booking_code,
            bps.ticket_number,
            ty.NAME AS passanger_type_name,
            sc.NAME AS ship_class_name,
            bps.fare,
            pay.created_on AS payment_date,
            bk.depart_date,
            bk.depart_time_start,
            bk.depart_time_end,
            bk.origin AS origin,
            bk.destination AS destination,
            concat ( port.NAME, '-', port2.NAME ) AS route_name,
            ( CASE 
                WHEN rf.status = 1 
                    THEN 'PROSES REFUND' 
                WHEN rf.status = 2 
                    THEN 'REFUNDED' ELSE 'GAGAL' END ) AS status_booking,
            pay.trans_number AS invoice_number,
            rf.bank AS bank_tujuan,
            rf.account_number AS no_rekening,
            rf.updated_on AS tanggal_approve,
            rf.adm_fee AS biaya_admin,
            rf.refund_fee AS biaya_refund,
            rf.charge_amount AS total_biaya,
            rf.total_amount AS pengembalian,
            inv.channel,
            inv.payment_type,
            rf.total_amount,
            ( CASE  
                WHEN rf.status = 1 AND ( rf.is_approved IS NULL OR rf.is_approved IS NOT NULL ) 
                    THEN 'PROSES MANUS' 
                WHEN rf.status = 1 AND rf.is_approved IS TRUE 
                    THEN 'PROSES MANKEU' 
                WHEN rf.status = 3 AND rf.is_approved IS TRUE 
                    THEN 'NEED TO RESUBMIT' 
                WHEN rf.status = 2 AND rf.is_approved IS TRUE 
                    THEN 'REFUNDED' ELSE'' END ) AS status_refund 
        FROM
            app.t_trx_refund rf
            JOIN app.t_trx_booking bk ON rf.booking_code = bk.booking_code
            JOIN app.t_trx_invoice inv ON inv.trans_number = bk.trans_number
            JOIN app.t_trx_payment pay ON pay.trans_number = bk.trans_number
            JOIN app.t_trx_booking_passanger bps ON bps.booking_code = bk.booking_code
            LEFT JOIN app.t_mtr_status st ON bps.status = st.status 
            AND tbl_name = 't_trx_booking_passanger'
            JOIN app.t_mtr_passanger_type ty ON ty.ID = bps.passanger_type_id
            JOIN app.t_mtr_ship_class sc ON sc.ID = bk.ship_class
            JOIN app.t_mtr_port port ON port.ID = bps.origin
            JOIN app.t_mtr_port port2 ON port2.ID = bps.destination 
        {$where}
		{$order}		
		";
    }

    public function download()
    {
        $start              = $this->input->get('start');
        $length             = $this->input->get('length');
        $search             = trim($this->input->get('search'));
        $shipClass          = $this->enc->decode($this->input->get('shipClass'));
        $route              = $this->enc->decode($this->input->get('route'));
        $dateTo             = trim($this->input->get('dateTo'));
        $dateFrom           = trim($this->input->get('dateFrom'));
        $paymentDateFrom    = trim($this->input->get('paymentDateFrom'));
        $paymentDateTo      = trim($this->input->get('paymentDateTo'));
        $route              = $this->enc->decode($this->input->get('route'));
        $bank               = trim($this->input->get('bank'));
        $statusRefunded     = trim($this->input->get('statusRefunded'));
        
        // cek app get_identity_app
        if ($this->get_identity_app() == 0) {
            if (!empty($this->session->userdata("port_id"))) {
                $port_origin = $this->session->userdata("port_id");
            } else {
                $port_origin = $this->enc->decode($this->input->post('port_origin'));
            }
        } else {
            $port_origin = $this->get_identity_app();
        }
        
        $where = " WHERE bk.service_id = 1 and (date(bk.depart_date) between '" . $dateFrom . "' and '" . $dateTo . "' ) ";
        
        if (!empty($port_origin)) {
            $where .= " and (a.origin ='{$port_origin}') ";
        }
        
        if (!empty($shipClass)) {
            $where .= " and (a.ship_class ='{$shipClass}') ";
        }
        
        if (!empty($route)) {
            $getRoute = $this->select_data("app.t_mtr_rute", " where id={$route} ")->row();
            $where .= " and ( bk.origin ='{$getRoute->origin}'  and bk.destination='{$getRoute->destination}' ) ";
        }
        
        if (!empty($bank)) {
            $where .= " and (upper(rf.bank) =upper('{$bank}')) ";
        }
        
        if (!empty($statusRefunded)) {
			if ($statusRefunded == 1) {
				$where .= " and (rf.status = 1 AND (rf.is_approved IS NULL OR rf.is_approved IS NULL))";
			} elseif ($statusRefunded == 2) {
				$where .= " and (rf.status = 1 AND rf.is_approved IS TRUE)";
			} elseif ($statusRefunded == 3) {
				$where .= "and (rf.status = 3 AND rf.is_approved IS TRUE)";
			} elseif ($statusRefunded == 4) {
				$where .= "and (rf.status = 2 AND rf.is_approved IS TRUE)";
			} else {
				$where .= "";
			}
		}
        
        if (!empty($paymentDateFrom) and empty($paymentDateTo)) {
            $where .= " and (date(payment_date)='{$paymentDateFrom}' ) ";
        }
        
        if (empty($paymentDateFrom) and !empty($paymentDateTo)) {
            $where .= " and (date(payment_date)='{$paymentDateTo}' ) ";
        }
        
        if (!empty($paymentDateFrom) and !empty($paymentDateTo)) {
            $where .= " and (date(payment_date) between '{$paymentDateFrom}' and  '{$paymentDateTo}'  ) ";
        }
        
        if (!empty($search['value'])) {
            $where .= " and (
							rf.booking_code ilike '%" . $iLike . "%'
							or bps.ticket_number ilike '%" . $iLike . "%'
			) ";
        }
        
        $order = " order by bk.depart_date , bk.depart_time ";
        $sql = $this->qry($where, $order);
        $query = $this->dbView->query($sql);
        return $query;
    }

    function get_bank()
    {
        $data  = array('' => 'SEMUA BANK');
        $query = $this->dbView->query(" SELECT DISTINCT bank FROM app.t_trx_refund WHERE bank <> '' ORDER BY bank ASC ")->result();

        foreach ($query as $key => $value) {
            $data[$this->enc->encode($value->bank)] = strtoupper($value->bank);
        }

        return array_unique($data);
    }

    public function get_identity_app()
    {
        $data = $this->dbView->query("select * from app.t_mtr_identity_app")->row();

        return $data->port_id;
    }
}
