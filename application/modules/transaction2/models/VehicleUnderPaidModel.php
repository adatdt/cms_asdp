<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Adat <adatdt@gmail.com>
 * @copyright  2020
 *
 */

class VehicleUnderPaidModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction2/vehicleUnderPaid';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$shipClass = $this->enc->decode($this->input->post('shipClass'));
		$route= $this->enc->decode($this->input->post('route'));
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
		if($this->get_identity_app()==0)
		{
			if (!empty($this->session->userdata("port_id")))
			{
				$port_origin =$this->session->userdata("port_id");
			}
			else
			{
				$port_origin = $this->enc->decode($this->input->post('port_origin'));
			}
		}
		else
		{
			$port_origin=$this->get_identity_app();
		}

		$field = array(
			0 =>'a.id',
            1  =>"booking_code",
            2  =>"ticket_number",
            3  =>"passanger_type_name",
            4  =>"ship_class_name",
            5  =>"fare",
            6  =>"payment_date",
            7  =>"keberangkatan",
            8  =>"route_name",
            9  =>"description",
            10  =>"time_order",
            11  =>"refund_time",
            12  =>"check_in_time",
            13  =>"gate_in_time",
            14  =>"boarding_time",
		);

		$order_column = $field[$order_column];

		$where = " WHERE c.status <>'-6' and (date(c.depart_date) between '".$dateFrom."' and '".$dateTo."' ) ";


		if(!empty($port_origin))
		{
			$where .= " and (c.origin ='{$port_origin}') ";
		}

		if(!empty($shipClass))
		{
			$where .= " and (c.ship_class ='{$shipClass}') ";
		}

		if(!empty($channel))
		{
			$where .= " and (upper(c.channel) =upper('{$channel}')) ";
		}

		if(!empty($route))
		{
			$getRoute=$this->select_data("app.t_mtr_rute", " where id={$route} ")->row();

			$where .= " and ( c.origin ='{$getRoute->origin}'  and c.destination='{$getRoute->destination}' ) ";
		}

		if(!empty($paymentDateFrom) and empty($paymentDateTo) )
		{
			$where .=" and (date(b.payment_date)='{$paymentDateFrom}' ) ";
		}

		if(empty($paymentDateFrom) and !empty($paymentDateTo) )
		{
			$where .=" and (date(b.payment_date)='{$paymentDateTo}' ) ";
		}

		if(!empty($paymentDateFrom) and !empty($paymentDateTo) )
		{
			$where .=" and (date(b.payment_date) between '{$paymentDateFrom}' and  '{$paymentDateTo}'  ) ";
		}						
							

		if (!empty($search['value'])){
			$where .=" and (
							b.booking_code ilike '%".$iLike."%'
							or d.ticket_number ilike '%".$iLike."%'
							or a.trans_number ilike '%".$iLike."%'
				 		) ";	
		}

		$sql =$this->qry($where);

				// die($sql); exit;

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY  ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$row->fare=idr_currency($row->fare);
			// $row->transfer_dana=idr_currency($row->transfer_dana);

			$row->old_fare=idr_currency($row->old_fare);
			$row->new_fare=idr_currency($row->new_fare);

			$row->payment_date=empty($row->payment_date)?"":format_date($row->payment_date)." ".format_time($row->payment_date);




			$row->keberangkatan=empty($row->keberangkatan)?"":format_date($row->keberangkatan);




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


	public function select_data($table, $where="")
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

	public function qry($where="", $order="")
	{
		return $data="
				SELECT 
				c.depart_date as keberangkatan,
				a.booking_code,
				d.ticket_number,
				d.length,
				e.name as vehicle_class_name,
				b.payment_date,
				f.name as ship_class_name,
				d.fare,
				concat(g.name,'-',h.name) as route_name,
				i.description,
				j.name as old_vehicle_class_name,
				k.name as new_vehicle_class_name,
				a.new_fare,
				a.old_fare,
				b.id,
				b.payment_type ,
				n.trans_code,
				a.trans_number
				from app.t_trx_under_paid a 
				join app.t_trx_payment b on a.trans_number=b.trans_number
				left join app.t_trx_booking c on a.booking_code=c.booking_code
				left join app.t_trx_booking_vehicle d on c.booking_code=d.booking_code
				left join app.t_mtr_vehicle_class e on d.vehicle_class_id=e.id
				left join app.t_mtr_ship_class f on d.ship_class=f.id
				left join app.t_mtr_port g on c.origin =g.id
				left join app.t_mtr_port h on c.destination =h.id
				left join app.t_mtr_status i on c.status=i.status AND tbl_name = 't_trx_booking'
				left join app.t_mtr_vehicle_class j on a.old_vehicle_class=j.id
				left join app.t_mtr_vehicle_class k on a.new_vehicle_class=k.id
				left join app.t_trx_invoice l on a.trans_number=l.trans_number and l.status=2
				LEFT JOIN app.t_mtr_transaction_type m ON  l.transaction_type=m.id
				left join app.t_trx_prepaid n on a.trans_number=n.trans_number

				{$where} 
				-- and a.service_id=2
				{$order}		
		";
	}

	public function download(){


		$start = $this->input->get('start');
		$length = $this->input->get('length');
		$search = trim($this->input->get('search'));
		$shipClass = $this->enc->decode($this->input->get('shipClass'));
		$route= $this->enc->decode($this->input->get('route'));
		$dateTo = trim($this->input->get('dateTo'));
		$dateFrom = trim($this->input->get('dateFrom'));
		$paymentDateFrom = trim($this->input->get('paymentDateFrom'));
		$paymentDateTo = trim($this->input->get('paymentDateTo'));
		$route = $this->enc->decode($this->input->get('route'));
		$iLike  = trim(strtoupper($this->dbView->escape_like_str($search)));

		// cek app get_identity_app
		if($this->get_identity_app()==0)
		{
			if (!empty($this->session->userdata("port_id")))
			{
				$port_origin =$this->session->userdata("port_id");
			}
			else
			{
				$port_origin = $this->enc->decode($this->input->get('port_origin'));
			}
		}
		else
		{
			$port_origin=$this->get_identity_app();
		}

		$field = array(
			0 =>'depart_date',
            1  =>"booking_code",
            2  =>"ticket_number",
            3  =>"passanger_type_name",
            4  =>"ship_class_name",
            5  =>"fare",
            6  =>"payment_date",
            7  =>"keberangkatan",
            8  =>"route_name",
            9  =>"description",
            10  =>"time_order",
            11  =>"refund_time",
            12  =>"check_in_time",
            13  =>"gate_in_time",
            14  =>"boarding_time",
		);

		$where = " WHERE c.status <>'-6' and (date(c.depart_date) between '".$dateFrom."' and '".$dateTo."' ) ";


		if(!empty($port_origin))
		{
			$where .= " and (c.origin ='{$port_origin}') ";
		}

		if(!empty($shipClass))
		{
			$where .= " and (c.ship_class ='{$shipClass}') ";
		}

		if(!empty($channel))
		{
			$where .= " and (upper(c.channel) =upper('{$channel}')) ";
		}

		if(!empty($route))
		{
			$getRoute=$this->select_data("app.t_mtr_rute", " where id={$route} ")->row();

			$where .= " and ( c.origin ='{$getRoute->origin}'  and c.destination='{$getRoute->destination}' ) ";
		}

		if(!empty($paymentDateFrom) and empty($paymentDateTo) )
		{
			$where .=" and (date(b.payment_date)='{$paymentDateFrom}' ) ";
		}

		if(empty($paymentDateFrom) and !empty($paymentDateTo) )
		{
			$where .=" and (date(b.payment_date)='{$paymentDateTo}' ) ";
		}

		if(!empty($paymentDateFrom) and !empty($paymentDateTo) )
		{
			$where .=" and (date(b.payment_date) between '{$paymentDateFrom}' and  '{$paymentDateTo}'  ) ";
		}						
							

		if (!empty($search)){
			$where .=" and (
							b.booking_code ilike '%".$iLike."%'
							or d.ticket_number ilike '%".$iLike."%'
							or a.trans_number ilike '%".$iLike."%'
				 		) ";	
		}

		$order= " order by c.depart_date desc , c.depart_time desc ";
		$sql =$this->qry($where,$order) ;
		
		$query = $this->dbView->query($sql);
		return $query;
	}

	function get_channel(){
		$data  = array(''=>'SEMUA CHANNEL');
		$query = $this->dbView->query(" SELECT DISTINCT channel FROM app.t_trx_invoice where channel<>'' ORDER BY channel asc ")->result();

		foreach ($query as $key => $value) {
		 	$data[$this->enc->encode($value->channel)] = strtoupper(str_replace('_', ' ', $value->channel));
		 } 

		return array_unique($data);
	}

    public function get_identity_app()
    {
        $data=$this->dbView->query("select * from app.t_mtr_identity_app")->row();

        return $data->port_id;
    }

}