<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Ihaab <ihaabmunabbih@gmail.com>
 * @copyright  2020
 *
 */

class M_data_capture_sensor extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/data_capture_sensor';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		// $search = $this->input->post('search');
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
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        		= trim(strtoupper($this->dbView->escape_like_str($searchData)));

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
			0 =>'id',
			1 => 'created_on',
			2 =>'origin',
			3 =>'booking_code',
			4 =>'ticket_number',
			5 =>'id_number',
			6 =>'vehicle_class_booking',
			7 =>'length_cam',
			8 =>'height_cam',
			9 =>'width_cam',
			10 =>'weighbridge',
			11 =>'vehicle_length_cam',
			12 =>'status_vehicle',
			13 =>'id_image',
            // 9  =>"description",
            // 10  =>"time_order",
            // 11  =>"refund_time",
            // 12  =>"check_in_time",
            // 13  =>"gate_in_time",
            // 14  =>"boarding_time",
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.id is not null and a.status<>'-6' and b.service_id='2' and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "' and h.name not in ('Golongan I', 'Golongan II', 'Golongan III')";
		// $where = " WHERE a.id is not null and a.status<>'-6' and b.service_id='2' and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' ) 
		// 			and h.name not in ('Golongan I', 'Golongan II', 'Golongan III')"; 


		if(!empty($port_origin))
		{
			$where .= " and (b.origin ='{$port_origin}') ";
		}

		// if(!empty($shipClass))
		// {
		// 	$where .= " and (c.ship_class ='{$shipClass}') ";
		// }

		// if(!empty($channel))
		// {
		// 	$where .= " and (upper(c.channel) =upper('{$channel}')) ";
		// }

		// if(!empty($route))
		// {
		// 	$getRoute=$this->select_data("app.t_mtr_rute", " where id={$route} ")->row();

		// 	$where .= " and ( c.origin ='{$getRoute->origin}'  and c.destination='{$getRoute->destination}' ) ";
		// }

		// if(!empty($paymentDateFrom) and empty($paymentDateTo) )
		// {
		// 	$where .=" and (date(b.payment_date)='{$paymentDateFrom}' ) ";
		// }

		// if(empty($paymentDateFrom) and !empty($paymentDateTo) )
		// {
		// 	$where .=" and (date(b.payment_date)='{$paymentDateTo}' ) ";
		// }

		// if(!empty($paymentDateFrom) and !empty($paymentDateTo) )
		// {
		// 	$where .=" and (date(b.payment_date) between '{$paymentDateFrom}' and  '{$paymentDateTo}'  ) ";
		// }

		// if (!empty($search['value'])){
		// 	$where .=" and (
		// 					b.booking_code ilike '%".$iLike."%'
		// 					or d.ticket_number ilike '%".$iLike."%'
		// 		 		) ";	
		// }

		if(!empty($searchData))
		{
			if($searchName=="plat")
			{
				$where .= "and (c.id_number ilike '%" . $iLike . "%')";

			}
			else if($searchName=="booking")
			{
				$where .= " and (b.booking_code ilike '%".$iLike."%')";
			}
			else if($searchName=="ticket")
			{
				$where .= " and (c.ticket_number ilike '%".$iLike."%')";
			}
		}

		$sql =$this->qry($where);

				// die($sql);

		$sqlCount 		   = $this->qryCount($where);

		// $query         = $this->dbView->query($sql);
		// $records_total = $query->num_rows();

		$countdata         = $this->dbView->query($sqlCount)->row();
		$records_total = $countdata->countdata;

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

			// $row->fare=idr_currency($row->fare);
			// // $row->transfer_dana=idr_currency($row->transfer_dana);

			// $row->old_fare=idr_currency($row->old_fare);
			// $row->new_fare=idr_currency($row->new_fare);

			// $row->payment_date=empty($row->payment_date)?"":format_date($row->payment_date)." ".format_time($row->payment_date);


			// $row->status_vehicle="";
			$d = explode(', ', $row->vehicle_length_cam);

			if(in_array($row->vehicle_class_name, $d)){
				$row->status_vehicle = success_label("Sesuai");
			}
			else {
				$row->status_vehicle= failed_label("Tidak Sesuai");
			}

			$image = explode(';', $row->id_image);
			$link = array();
			foreach ($image as $val) {
				$info = pathinfo($val);
				$link[] = '<a href="'.$val.'" target="_blank">'.$info['basename'].'</a>';
			}
			$row->id_image = implode("<br> ",$link);
			// $row->keberangkatan=empty($row->keberangkatan)?"":format_date($row->keberangkatan);



			$lenCam  = strlen($row->length_cam);
			$lenVehicleLengthCam  = strlen($row->vehicle_length_cam);

			$row->length_cam = $lenCam >=7? substr($row->length_cam,0, -3 ):$row->length_cam;
			$row->vehicle_length_cam = $lenVehicleLengthCam >=7? substr($row->vehicle_length_cam,0, -3  ):$row->vehicle_length_cam;


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
		return $data="SELECT 
		a.booking_code, array_to_string(array_agg(i.name order by i.id), ', ') as vehicle_length_cam, h.name as vehicle_class_name,
		c.id_number, g.name as origin,
		a.* from app.t_trx_check_in_vehicle a
		left join app.t_trx_booking b on a.booking_code=b.booking_code
		left join app.t_trx_booking_vehicle c on a.booking_code=c.booking_code
		left join app.t_trx_invoice d on b.trans_number=d.trans_number
		left join app.t_mtr_service e on b.service_id=e.id
		left join app.t_mtr_device_terminal f on a.terminal_code=f.terminal_code  
		left join app.t_mtr_port g on b.origin =g.id
		left join app.t_mtr_vehicle_class h on c.vehicle_class_id=h.id
		left join app.t_mtr_vehicle_class i on (a.length_cam) between i.min_length and i.max_length and i.status = 1 and i.name not in ('Golongan I', 'Golongan II', 'Golongan III')
		{$where}
		group by a.booking_code, h.name ,c.id_number, a.id, g.name
		";
	}

	public function qryCount($where)
	{
		$data="SELECT count(sensor.booking_code) as countdata from	(SELECT 
		a.booking_code, array_to_string(array_agg(i.name order by i.id), ', ') as vehicle_length_cam, h.name as vehicle_class_name,
		c.id_number, g.name as origin
		 from app.t_trx_check_in_vehicle a
		left join app.t_trx_booking b on a.booking_code=b.booking_code
		left join app.t_trx_booking_vehicle c on a.booking_code=c.booking_code
		left join app.t_trx_invoice d on b.trans_number=d.trans_number
		left join app.t_mtr_service e on b.service_id=e.id
		left join app.t_mtr_device_terminal f on a.terminal_code=f.terminal_code  
		left join app.t_mtr_port g on b.origin =g.id
		left join app.t_mtr_vehicle_class h on c.vehicle_class_id=h.id
		left join app.t_mtr_vehicle_class i on (a.length_cam) between i.min_length and i.max_length and i.status = 1 and i.name not in ('Golongan I', 'Golongan II', 'Golongan III')
		$where
		group by a.booking_code, h.name ,c.id_number, a.id, g.name) sensor";

		return $data;
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
				 		) ";	
		}

		$order= " order by c.depart_date desc , c.depart_time desc ";
		$sql =$this->qry($where,$order) ;
		
		$query = $this->dbView->query($sql);
		return $query;
	}

	
    public function get_identity_app()
    {
        $data=$this->dbView->query("select * from app.t_mtr_identity_app")->row();

        return $data->port_id;
    }

}