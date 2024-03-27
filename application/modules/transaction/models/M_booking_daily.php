<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_booking_daily extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_module = 'transaction/booking_daily';
    }

    public function listPenumpang()
    {
        $start        = $this->input->post('start');
        $length       = $this->input->post('length');
        $draw         = $this->input->post('draw');
        $dateTo       = trim($this->input->post('dateTo'));
        $dateFrom     = trim($this->input->post('dateFrom'));
        // $port         = $this->enc->decode($this->input->post('port'));
        $kelas         = $this->enc->decode($this->input->post('kelas'));
        $shift         = $this->enc->decode($this->input->post('shift'));
        $order        = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir    = strtoupper($order[0]['dir']);
		$searchData=$this->input->post('searchData');
		$searchName=$this->input->post('searchName');
		$iLike  = trim(strtoupper($this->db->escape_like_str($searchData)));

        if ($this->get_identity_app() == 0) {
            if (!empty($this->session->userdata("port_id"))) 
            {
                $port = $this->session->userdata("port_id");
            } else {
                $port = $this->enc->decode($this->input->post('port'));
            }
        } else {
            $port = $this->get_identity_app();
        }

        $field = array(
            0 => 'a.id',
            1 => 'booking_code',
            2 => 'depart_date',
            3 => 'status',
            4 => 'customer_name',
            5 => 'nik',
            6 => 'jenis_identitas',
            7 => 'gender',
            8 => 'tipe_penumpang',
            9 => 'kelas',
            10 => 'port_origin',
            11 => 'service_name',
            12 => 'fare',
            13 => 'shift',
        );

        $order_column = $field[$order_column];

        $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status != -5 and a.depart_date >= '". $dateFrom . "' and a.depart_date < '" . $dateToNew . "'";
        // $where = " WHERE a.status <>'-5' and (date(a.depart_date) between '" . $dateFrom . "' and '" . $dateTo . "' ) ";

        if (!empty($port)) {
            $where .= "and (a.origin=" . $port . ")";
        }

        if (!empty($kelas)) {
            $where .= "and (sc.name='" . $kelas . "')";
        }
        if(!empty($shift))
        {
            $where .= "and (upper(SFT.shift)=upper('" . $shift . "'))";
        }


        // if (!empty($search['value'])) {
        //     $where .= " and (
        //                     a.booking_code ilike '%" . $iLike . "%' 
		// 					or ptp.number ilike '%" . $iLike . "%'
		// 					or b.name ilike '%" . $iLike . "%'
		// 					or d.customer_name ilike '%" . $iLike . "%'
		// 					or sc.name ilike '%" . $iLike . "%'
		// 					or top.booking_code ilike '%" . $iLike . "%'
		// 					or d.email ilike '%" . $iLike . "%' 
		// 					or d.phone_number ilike '%" . $iLike . "%' 
		// 					or d.channel ilike '%" . $iLike . "%' 
        //                     or ttp.card_no ilike '%" . $iLike . "%' 
        //                     or d.terminal_code ilike '%" . $iLike . "%' 
        //                     or g.terminal_name ilike '%" . $iLike . "%' 
        //                 ) ";
        // }

        if(!empty($searchData))
        {
            if($searchName=='bookingCode')
            {
                $where .=" and (a.booking_code ilike '%".$iLike."%' ) ";
            }
            else if($searchName=='customerName' )
            {
                $where .=" and (d.customer_name ilike '%".$iLike."%' ) ";
            }
            else
            {
                $where .=" and (BP.id_number ilike '%".$iLike."%' ) ";
            }
        }

        $sql = "SELECT
                    A.id,
                    A.booking_code,
                    A.origin,
                    A.depart_date,
                    A.status,
                    A.created_on,
                    d.customer_name,
                    BP.id_number AS nik,
                    pt.NAME AS jenis_identitas,
                    bp.gender,
                    PTP.NAME AS tipe_penumpang,
                    sc.NAME AS kelas,
                    b.NAME AS port_origin,
                    e.NAME AS service_name,
                    BP.fare,
                    SFT.shift
                FROM
                    app.t_trx_booking A 
                    LEFT JOIN app.t_trx_booking_passanger BP ON BP.booking_code = A.booking_code
                    LEFT JOIN app.t_mtr_passanger_type ptp ON ptp.ID = BP.passanger_type_id
                    LEFT JOIN app.t_mtr_passanger_type_id pt ON pt.ID = BP.id_type
                    LEFT JOIN app.t_mtr_ship_class SC ON SC.ID = A.ship_class
                    LEFT JOIN app.t_mtr_port b ON A.origin = b.ID 
                    LEFT JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number
                    LEFT JOIN app.t_mtr_service e ON A.service_id = e.ID 
                    LEFT JOIN app.t_mtr_status f ON A.status = f.status
                    AND tbl_name = 't_trx_booking' 
                    LEFT JOIN(  SELECT
                            A2.booking_code,
                            (SELECT shift_name
                                FROM
                                app.t_mtr_shift shift
                                JOIN app.t_mtr_shift_time st on st.shift_id = shift.id AND st.status = 1
                                WHERE
                                shift.status = 1 and st.port_id=A2.origin
                                AND ((A2.created_on::time BETWEEN st.shift_login AND st.shift_logout) OR 
                                ((A2.created_on::time> st.shift_login OR A2.created_on::time < st.shift_logout ) 
                                AND st.night = TRUE)) )
                                as shift

                            FROM
                                app.t_trx_booking A2
                                WHERE A2.status <>'-5' and A2.depart_date >= ' {$dateFrom} ' and A2.depart_date < '{$dateToNew} '     
                        ) SFT ON A.booking_code=SFT.booking_code
                        $where";

        $sqlCount = "SELECT
                    count(a.id) as countdata
                FROM
                    app.t_trx_booking A 
                    LEFT JOIN app.t_trx_booking_passanger BP ON BP.booking_code = A.booking_code
                    LEFT JOIN app.t_mtr_passanger_type ptp ON ptp.ID = BP.passanger_type_id
                    LEFT JOIN app.t_mtr_passanger_type_id pt ON pt.ID = BP.id_type
                    LEFT JOIN app.t_mtr_ship_class SC ON SC.ID = A.ship_class
                    LEFT JOIN app.t_mtr_port b ON A.origin = b.ID 
                    LEFT JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number
                    LEFT JOIN app.t_mtr_service e ON A.service_id = e.ID 
                    LEFT JOIN app.t_mtr_status f ON A.status = f.status
                    AND tbl_name = 't_trx_booking' 
                    LEFT JOIN(  SELECT
                            A2.booking_code,
                            (SELECT shift_name
                                FROM
                                app.t_mtr_shift shift
                                JOIN app.t_mtr_shift_time st on st.shift_id = shift.id AND st.status = 1
                                WHERE
                                shift.status = 1 and st.port_id=A2.origin
                                AND ((A2.created_on::time BETWEEN st.shift_login AND st.shift_logout) OR 
                                ((A2.created_on::time> st.shift_login OR A2.created_on::time < st.shift_logout ) 
                                AND st.night = TRUE)) )
                                as shift
                            FROM
                                app.t_trx_booking A2
                                WHERE A2.status <>'-5' and A2.depart_date >= ' {$dateFrom} ' and A2.depart_date < '{$dateToNew} '     
                        ) SFT ON A.booking_code=SFT.booking_code
                        $where";

        $queryCount         = $this->db->query($sqlCount)->row();
        $records_total 			= $queryCount->countdata;
        // $query         = $this->db->query($sql);
        // $records_total = $query->num_rows();
        $sql           .= " ORDER BY  " . $order_column . " {$order_dir}";

        if ($length != -1) {
            $sql .= " LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->db->query($sql);
        $rows_data = $query->result();

        $rows     = array();
        $i      = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number        = $i;
            $row->depart_date   = format_dateTime($row->depart_date);
            $row->depart_date   = format_date($row->depart_date);
            $row->ship_class    = strtoupper($row->ship_class);
            $row->no            = $i;
            $row->fare          = idr_currency($row->fare);
            $row->shift         = $this->getShift($row->created_on, $row->origin);
            $row->created_on    =empty($row->created_on)?"":format_date($row->created_on)." ".format_time($row->created_on);

            $rows[]             = $row;


            unset($row->id);
            $i++;
        }

        return array(
            'draw'           => $draw,
            'recordsTotal'   => $records_total,
            'recordsFiltered' => $records_total,
            'data'           => $rows
        );
    }

    public function listKendaraan()
    {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $order = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir = strtoupper($order[0]['dir']);
        $dateFrom = trim($this->input->post('dateFrom'));
        $dateTo = trim($this->input->post('dateTo'));
        // $port = $this->enc->decode($this->input->post('port'));
        $kelas = $this->enc->decode($this->input->post('kelas'));
        $shift = $this->enc->decode($this->input->post('shift'));
		$searchData=$this->input->post('searchData');
		$searchName=$this->input->post('searchName');
		$iLike  = trim(strtoupper($this->db->escape_like_str($searchData)));

        if ($this->get_identity_app() == 0) {
            if (!empty($this->session->userdata("port_id"))) 
            {
                $port = $this->session->userdata("port_id");
            } else {
                $port = $this->enc->decode($this->input->post('port'));
            }
        } else {
            $port = $this->get_identity_app();
        }        

        $field = array(
            0 => 'b.id',
            1 => 'booking_code',
            2 => 'depart_date',
            3 => 'plat',
            4 => 'vehicle_class',
            5 => 'ship_class',
            6 => 'customer',
            7 => 'origin',
            8 => 'weighbridge',
            9 => 'fare',
            10 => 'shift',

        );

        $order_column = $field[$order_column];

        // $where = " WHERE B.status != -5 AND BV.service_id = 2 AND BP.id IN(SELECT MIN(BP2.id) FROM app.t_trx_booking_passanger BP2 WHERE BP2.booking_code=BP.booking_code)";

        $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE B.status != -5 AND BV.service_id = 2 and B.depart_date >= '". $dateFrom . "' and B.depart_date < '" . $dateToNew . "'";
        // $where = " WHERE B.status != -5 AND BV.service_id = 2 and (date(B.depart_date) between '" . $dateFrom . "' and '" . $dateTo . "' ) " ;


        if (!empty($port)) {
            $where .= " and (B.origin='" . $port . "')";
        }

        if (!empty($kelas)) {
            $where .= " and (SC.name='" . $kelas . "')";
        }

        if(!empty($shift))
        {
            $where .= "and (upper(SFT.shift)=upper('" . $shift . "'))";
        }        

        

        if(!empty($searchData))
        {
            if($searchName=='bookingCode')
            {
                $where .=" and (B.booking_code ilike '%".$iLike."%' ) ";
            }
            else if($searchName=='customerName' )
            {
                $where .=" and (i.name ilike '%".$iLike."%' ) ";
            }
            else
            {
                $where .=" and (BV.id_number ilike '%".$iLike."%' ) ";
            }
        }

        $sql = "SELECT DISTINCT
                    B.ID,
                    B.depart_date,
                    B.CREATED_ON,
                    BV.booking_code,
                    -- BP.NAME AS customer,
                    BV.id_number AS plat,
                    i.name as customer,
                    SC.NAME AS kelas,
                    VC.NAME AS vehicle_class,
                    P.NAME AS origin,
                    BV.fare,
                    BV.weighbridge,
                    SFT.shift
                FROM
                    app.t_trx_booking B
                    JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
                    -- JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
                    JOIN app.t_mtr_port P ON P.ID = B.origin
                    JOIN app.t_mtr_ship_class SC ON SC.ID = B.ship_class
                    LEFT JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
                    left join
                    (
                    select distinct on (booking_code) booking_code, name,  min(ticket_number) from app.t_trx_booking_passanger
                        where status !='-5'
                        group by booking_code, ticket_number, name
                    ) i on B.booking_code=i.booking_code
                    LEFT JOIN(  SELECT
                            A2.booking_code,
                            (SELECT shift_name
                                FROM
                                app.t_mtr_shift shift
                                JOIN app.t_mtr_shift_time st on st.shift_id = shift.id AND st.status = 1
                                WHERE
                                shift.status = 1 and st.port_id=A2.origin
                                AND ((A2.created_on::time BETWEEN st.shift_login AND st.shift_logout) OR 
                                ((A2.created_on::time> st.shift_login OR A2.created_on::time < st.shift_logout ) 
                                AND st.night = TRUE)) )
                                as shift
                            FROM
                                app.t_trx_booking A2
                                WHERE A2.status <>'-5' and A2.depart_date >= ' {$dateFrom} ' and A2.depart_date < '{$dateToNew} '     
                        ) SFT ON B.booking_code=SFT.booking_code                    
                    {$where}";

        $sqlCount = "SELECT DISTINCT
                        count(B.id) as countdata
                    FROM
                        app.t_trx_booking B
                        JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
                        -- JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
                        JOIN app.t_mtr_port P ON P.ID = B.origin
                        JOIN app.t_mtr_ship_class SC ON SC.ID = B.ship_class
                        LEFT JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
                        left join
                        (
                        select distinct on (booking_code) booking_code, name,  min(ticket_number) from app.t_trx_booking_passanger
                            where status !='-5'
                            group by booking_code, ticket_number, name
                        ) i on B.booking_code=i.booking_code
                        LEFT JOIN(  SELECT
                                A2.booking_code,
                                (SELECT shift_name
                                    FROM
                                    app.t_mtr_shift shift
                                    JOIN app.t_mtr_shift_time st on st.shift_id = shift.id AND st.status = 1
                                    WHERE
                                    shift.status = 1 and st.port_id=A2.origin
                                    AND ((A2.created_on::time BETWEEN st.shift_login AND st.shift_logout) OR 
                                    ((A2.created_on::time> st.shift_login OR A2.created_on::time < st.shift_logout ) 
                                    AND st.night = TRUE)) )
                                    as shift
                                FROM
                                    app.t_trx_booking A2
                                    WHERE A2.status <>'-5' and A2.depart_date >= ' {$dateFrom} ' and A2.depart_date < '{$dateToNew} '     
                            ) SFT ON B.booking_code=SFT.booking_code                    
                        {$where}";

        $queryCount         = $this->db->query($sqlCount)->row();
        $records_total 			= $queryCount->countdata;
        // $query         = $this->db->query($sql);
        // $records_total = $query->num_rows();
        $sql           .= " ORDER BY " . $order_column . " {$order_dir}";
        if ($length != -1) {
            $sql .= " LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->db->query($sql);
        $rows_data = $query->result();

        $rows     = array();
        $i      = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number        = $i;
            $row->depart_date   = format_date($row->depart_date);
            $row->ship_class    = strtoupper($row->ship_class);
            $row->id_number != 0 ? $row->id_number = $row->id_number : $row->id_number = '-';
            $row->fare          = idr_currency($row->fare);
            $row->weighbridge=empty($row->weighbridge)?"0":$row->weighbridge;
            $row->created_on    =empty($row->created_on)?"":format_date($row->created_on)." ".format_time($row->created_on);
            $rows[]             = $row;
            unset($row->id);
            $i++;
        }

        return array(
            'draw'           => $draw,
            'recordsTotal'   => $records_total,
            'recordsFiltered' => $records_total,
            'data'           => $rows
        );
    }

    function get_kelas()
    {
        $data  = array('' => 'Pilih');
        $query = $this->db->query("SELECT DISTINCT name FROM app.t_mtr_ship_class ORDER BY name")->result();

        foreach ($query as $key => $value) {
            $data[$this->enc->encode($value->name)] = strtoupper(str_replace('_', ' ', $value->name));
        }

        return $data;
    }

    function get_channel()
    {
        $data  = array('' => 'All');
        $query = $this->db->query("SELECT DISTINCT channel FROM app.t_trx_payment ORDER BY channel")->result();

        foreach ($query as $key => $value) {
            $data[$this->enc->encode($value->channel)] = strtoupper(str_replace('_', ' ', $value->channel));
        }

        return $data;
    }

    function get_payment_type()
    {
        $data  = array('' => 'All');
        $query = $this->db->query("SELECT DISTINCT payment_type FROM app.t_trx_payment ORDER BY payment_type")->result();

        foreach ($query as $key => $value) {
            $data[$this->enc->encode($value->payment_type)] = strtoupper(str_replace('-', ' ', $value->payment_type));
        }

        return $data;
    }

    public function list_data($port, $kelas, $dateFrom, $dateTo, $type, $shift, $cari, $searchName)
    {
		$iLike  = trim(strtoupper($this->db->escape_like_str($cari)));
        if ($type === 'penumpang') {
            $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		    $where = " WHERE a.status != -5 and a.depart_date >= '". $dateFrom . "' and a.depart_date < '" . $dateToNew . "'";
            // $where = " WHERE a.status <>'-5' and (date(a.depart_date) between '" . $dateFrom . "' and '" . $dateTo . "' ) ";

            if (!empty($port)) {
                $where .= "and (a.origin=" . $port . ")";
            }

            if (!empty($kelas)) {
                $where .= "and (sc.name='" . $kelas . "')";
            }

            if(!empty($shift))
            {
                $where .= "and (upper(SFT.shift)=upper('" . $shift . "'))";
            }


            if(!empty($cari))
            {
                if($searchName=='bookingCode')
                {
                    $where .=" and (a.booking_code ilike '%".$iLike."%' ) ";
                }
                else if($searchName=='customerName' )
                {
                    $where .=" and (d.customer_name ilike '%".$iLike."%' ) ";
                }
                else
                {
                    $where .=" and (BP.id_number ilike '%".$iLike."%' ) ";
                }
            }

            $sql = "SELECT
                    A.booking_code,
                    A.origin,
                    A.depart_date,
                    A.status,
                    A.created_on,
                    d.customer_name,
                    BP.id_number AS nik,
                    pt.NAME AS jenis_identitas,
                    bp.gender,
                    PTP.NAME AS tipe_penumpang,
                    sc.NAME AS kelas,
                    b.NAME AS port_origin,
                    e.NAME AS service_name,
                    BP.fare,
                    SFT.shift
                FROM
                    app.t_trx_booking A 
                    LEFT JOIN app.t_trx_booking_passanger BP ON BP.booking_code = A.booking_code
                    LEFT JOIN app.t_mtr_passanger_type ptp ON ptp.ID = BP.passanger_type_id
                    LEFT JOIN app.t_mtr_passanger_type_id pt ON pt.ID = BP.id_type
                    LEFT JOIN app.t_mtr_ship_class SC ON SC.ID = A.ship_class
                    LEFT JOIN app.t_mtr_port b ON A.origin = b.ID 
                    LEFT JOIN app.t_trx_invoice d ON A.trans_number = d.trans_number
                    LEFT JOIN app.t_mtr_service e ON A.service_id = e.ID 
                    LEFT JOIN app.t_mtr_status f ON A.status = f.status
                    AND tbl_name = 't_trx_booking' 
                    LEFT JOIN(  SELECT
                            A2.booking_code,
                            (SELECT shift_name
                                FROM
                                app.t_mtr_shift shift
                                JOIN app.t_mtr_shift_time st on st.shift_id = shift.id AND st.status = 1
                                WHERE
                                shift.status = 1 and st.port_id=A2.origin
                                AND ((A2.created_on::time BETWEEN st.shift_login AND st.shift_logout) OR 
                                ((A2.created_on::time> st.shift_login OR A2.created_on::time < st.shift_logout ) 
                                AND st.night = TRUE)) )
                                as shift

                            FROM
                                app.t_trx_booking A2
                                WHERE A2.status <>'-5' and A2.depart_date >= ' {$dateFrom} ' and A2.depart_date < '{$dateToNew} '     
                        ) SFT ON A.booking_code=SFT.booking_code
                        $where order by A.id desc";

            $check = $this->db->query($sql)->num_rows();

            if ($check > 0) {
                return $this->db->query($sql)->result();
            } else {
                return false;
            }
        }

        if ($type === 'kendaraan') {
            $dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		    $where = " WHERE B.status != -5 AND BV.service_id = 2 and B.depart_date >= '". $dateFrom . "' and B.depart_date < '" . $dateToNew . "'";
            // $where = " WHERE B.status != -5 AND BV.service_id = 2 and (date(B.depart_date) between '" . $dateFrom . "' and '" . $dateTo . "' ) " ;


            if (!empty($port)) {
                $where .= " and (B.origin='" . $port . "')";
            }

            if (!empty($kelas)) {
                $where .= " and (SC.name='" . $kelas . "')";
            }

            if(!empty($shift))
            {
                $where .= "and (upper(SFT.shift)=upper('" . $shift . "'))";
            }            

            if(!empty($cari))
            {
                if($searchName=='bookingCode')
                {
                    $where .=" and (B.booking_code ilike '%".$iLike."%' ) ";
                }
                else if($searchName=='customerName' )
                {
                    $where .=" and (i.name ilike '%".$iLike."%' ) ";
                }
                else
                {
                    $where .=" and (BV.id_number ilike '%".$iLike."%' ) ";
                }
            }

            $sql = "SELECT DISTINCT
                        B.ID,
                        B.depart_date,
                        BV.booking_code,
                        -- BP.NAME AS customer,
                        BV.id_number AS plat,
                        i.name as customer,
                        SC.NAME AS kelas,
                        VC.NAME AS vehicle_class,
                        P.NAME AS origin,
                        BV.fare,
                        BV.weighbridge,
                        SFT.shift
                    FROM
                        app.t_trx_booking B
                        JOIN app.t_trx_booking_vehicle BV ON BV.booking_code = B.booking_code
                        -- JOIN app.t_trx_booking_passanger BP ON BP.booking_code = B.booking_code
                        JOIN app.t_mtr_port P ON P.ID = B.origin
                        JOIN app.t_mtr_ship_class SC ON SC.ID = B.ship_class
                        LEFT JOIN app.t_mtr_vehicle_class VC ON VC.ID = BV.vehicle_class_id
                        left join
                        (
                        select distinct on (booking_code) booking_code, name,  min(ticket_number) from app.t_trx_booking_passanger
                            where status !='-5'
                            group by booking_code, ticket_number, name
                        ) i on B.booking_code=i.booking_code
                       LEFT JOIN(  SELECT
                                A2.booking_code,
                                (SELECT shift_name
                                    FROM
                                    app.t_mtr_shift shift
                                    JOIN app.t_mtr_shift_time st on st.shift_id = shift.id AND st.status = 1
                                    WHERE
                                    shift.status = 1 and st.port_id=A2.origin
                                    AND ((A2.created_on::time BETWEEN st.shift_login AND st.shift_logout) OR 
                                    ((A2.created_on::time> st.shift_login OR A2.created_on::time < st.shift_logout ) 
                                    AND st.night = TRUE)) )
                                    as shift

                                FROM
                                    app.t_trx_booking A2
                                    WHERE A2.status <>'-5' and A2.depart_date >= ' {$dateFrom} ' and A2.depart_date < '{$dateToNew} '    
                            ) SFT ON B.booking_code=SFT.booking_code                        

                        {$where} order by B.id desc ";


            $check = $this->db->query($sql)->num_rows();

            if ($check > 0) {
                return $this->db->query($sql)->result();
            } else {
                return false;
            }
        }
    }

    public function getShift($createdOn, $port)
    {
        $date=date("Y-m-d", strtotime($createdOn));
        $time=date("H:i:s", strtotime($createdOn));
        $qry= $this->db->query("
            SELECT shift_name, shift.id,
                CASE
                WHEN st.night = TRUE AND '{$time}' < st.shift_logout
                 THEN '{$date}'::date - 1 
                ELSE '{$date}'::date
                    EnD shift_date
                FROM
                app.t_mtr_shift shift
                JOIN app.t_mtr_shift_time st on st.shift_id = shift.id AND st.status = 1
                WHERE
                shift.status = 1 and st.port_id='{$port}'
                AND (('{$time}' BETWEEN st.shift_login AND st.shift_logout) OR 
                (('{$time}'> st.shift_login OR '{$time}' < st.shift_logout ) AND st.night = TRUE))


            ")->row();
            
            return $qry->shift_name;
    }
    public function select_data($table, $where)
    {
        return $this->db->query("select * from $table $where");
    }

    public function get_identity_app()
    {
        $data = $this->db->query("select * from app.t_mtr_identity_app")->row();

        return $data->port_id;
    }
}
