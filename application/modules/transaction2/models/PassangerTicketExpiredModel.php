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

class PassangerTicketExpiredModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction2/passangerTicketExpired';
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
		$statusExpired = $this->enc->decode($this->input->post('statusExpired'));
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
            10  =>"description_expired",
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status <>'-6' and (date(a.depart_date) between '".$dateFrom."' and '".$dateTo."' ) ";


		$expired=" 	and (
				(b.status=2 and b.checkin_expired<=now())
				or
				(b.status=3 and b.gatein_expired<=now())
				or
				(b.status in (4,7) and b.boarding_expired<=now())
				or b.status in (10,11,12) ) ";

		if(!empty($statusExpired))
		{
			if($statusExpired==10)
			{
				$expired=" and ((b.status=2 and b.checkin_expired<=now()) or b.status=10 ) ";
			}
			else if($statusExpired==11)
			{
				$expired=" and ((b.status=3 and b.gatein_expired<=now()) or b.status=11 ) ";	
			}
			else
			{
				$expired=" and ((b.status in (4,7) and b.boarding_expired<=now()) or b.status=12 ) ";	
			}
		}


		if(!empty($port_origin))
		{
			$where .= " and (a.origin ='{$port_origin}') ";
		}

		if(!empty($shipClass))
		{
			$where .= " and (a.ship_class ='{$shipClass}') ";
		}

		if(!empty($channel))
		{
			$where .= " and (upper(a.channel) =upper('{$channel}')) ";
		}

		if(!empty($route))
		{
			$getRoute=$this->select_data("app.t_mtr_rute", " where id={$route} ")->row();

			$where .= " and ( a.origin ='{$getRoute->origin}'  and a.destination='{$getRoute->destination}' ) ";
		}

		if(!empty($paymentDateFrom) and empty($paymentDateTo) )
		{
			$where .=" and (date(e.payment_date)='{$paymentDateFrom}' ) ";
		}

		if(empty($paymentDateFrom) and !empty($paymentDateTo) )
		{
			$where .=" and (date(e.payment_date)='{$paymentDateTo}' ) ";
		}

		if(!empty($paymentDateFrom) and !empty($paymentDateTo) )
		{
			$where .=" and (date(e.payment_date) between '{$paymentDateFrom}' and  '{$paymentDateTo}'  ) ";
		}						
							

		if (!empty($search['value'])){
			$where .=" and (
							a.booking_code ilike '%".$iLike."%'
							or b.ticket_number ilike '%".$iLike."%'
				 		) ";	
		}

		$sql =$this->qry($where." ".$expired);

				// die($sql);

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
			$row->transfer_dana=idr_currency($row->transfer_dana);

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
				b.status,
				(
					case
					when b.status=2 and b.checkin_expired<=now() then 'Check in Expired'
					when b.status=10 then 'Check in Expired'
					when b.status=3 and b.gatein_expired<=now() then 'Gate in Expired'
					when b.status=11 then 'Gate in Expired'
					when b.status=4 and b.boarding_expired<=now() then 'Boarding Expired'
					when b.status=12 then 'Boarding Expired'
					else 'Boarding Expired' end
				) as description_expired,
				b.checkin_expired,
				b.gatein_expired,
				b.boarding_expired,
				a.booking_code,
				b.ticket_number,
				c.name as passanger_type_name,
				d.name as service_name,
				b.fare,
				e.payment_date,
				e.amount as transfer_dana,
				a.channel as sales_channel,
				a.trans_number,
				concat(a.depart_date,' ',a.depart_time) as keberangkatan,
				concat(g.name,'-',h.name) as route_name,
				f.description,
				i.name as ship_class_name
				from app.t_trx_booking a 
				left join app.t_trx_booking_passanger b on a.booking_code=b.booking_code
				left join app.t_mtr_passanger_type c on b.passanger_type_id=c.id
				left join app.t_mtr_service d on a.service_id=d.id
				left join app.t_trx_payment e on a.trans_number=e.trans_number
				LEFT JOIN app.t_mtr_status f on a.status = f.status AND tbl_name = 't_trx_booking'
				left join app.t_mtr_port g on a.origin =g.id
				left join app.t_mtr_port h on a.destination =h.id
				left join app.t_mtr_ship_class i on a.ship_class =i.id	
				{$where} 
				--and (upper(a.channel)='WEB' or upper(a.channel)='IFCS') and a.service_id=1
				and a.service_id=1
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
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($search)));


		// cek app get_identity_app
		if($this->get_identity_app()==0)
		{
			if (!empty($this->session->userdata("port_id")))
			{
				$port_origin =$this->session->userdata("port_id");
			}
			else
			{
				$port_origin = $this->enc->decode($this->input->get('port'));
			}
		}
		else
		{
			$port_origin=$this->get_identity_app();
		}


		$where = " WHERE a.status <>'-6' and (date(a.depart_date) between '".$dateFrom."' and '".$dateTo."' ) ";


		$expired=" 	and (
		(b.status=2 and b.checkin_expired<=now())
		or
		(b.status=3 and b.gatein_expired<=now())
		or
		(b.status in (4,7) and b.boarding_expired<=now())
		or b.status in (10,11,12) ) ";

		if(!empty($statusExpired))
		{
			if($statusExpired==10)
			{
				$expired=" and ((b.status=2 and b.checkin_expired<=now()) or b.status=10 )";
			}
			else if($statusExpired==11)
			{
				$expired=" and ((b.status=3 and b.gatein_expired<=now()) or b.status=11 )";	
			}
			else
			{
				$expired=" and ((b.status in (4,7) and b.boarding_expired<=now()) or b.status=12 )";	
			}
		}



		if(!empty($port_origin))
		{
			$where .= " and (a.origin ='{$port_origin}') ";
		}

		if(!empty($shipClass))
		{
			$where .= " and (a.ship_class ='{$shipClass}') ";
		}

		if(!empty($route))
		{
			$getRoute=$this->select_data("app.t_mtr_rute", " where id={$route} ")->row();

			$where .= " and ( a.origin ='{$getRoute->origin}'  and a.destination='{$getRoute->destination}' ) ";
		}

		if(!empty($paymentDateFrom) and empty($paymentDateTo) )
		{
			$where .=" and (date(e.payment_date)='{$paymentDateFrom}' ) ";
		}

		if(empty($paymentDateFrom) and !empty($paymentDateTo) )
		{
			$where .=" and (date(e.payment_date)='{$paymentDateTo}' ) ";
		}

		if(!empty($paymentDateFrom) and !empty($paymentDateTo) )
		{
			$where .=" and (date(e.payment_date) between '{$paymentDateFrom}' and  '{$paymentDateTo}'  ) ";
		}						
							

		if (!empty($search)){
			$where .=" and (
							a.booking_code ilike '%".$iLike."%'
							or b.ticket_number ilike '%".$iLike."%'
				 		) ";	
		}


		$order= " order by a.depart_date desc , a.depart_time desc";
		$sql =$this->qry($where." ".$expired ,$order) ;
		
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