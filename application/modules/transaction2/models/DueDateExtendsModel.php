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

class DueDateExtendsModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction2/dueDateExtens';
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
			0 =>'id',
            1  =>"created_on",
            2  =>"trans_number",
            3  =>"booking_code",
            4  =>"route_name",
            5  =>"extends_time",
            6  =>"old_due_date",
            7  =>"new_due_date",
		);

		$order_column = $field[$order_column];

		$where = " WHERE dt.status <>'-5' and (date(dt.created_on) between '".$dateFrom."' and '".$dateTo."' ) ";


		if (!empty($search['value'])){
			$where .=" and (
							b.booking_code ilike '%".$iLike."%'
							or a.trans_number ilike '%".$iLike."%'
				 		) ";	
		}

		$sql =$this->qry($where);

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


			$row->created_on=empty($row->created_on)?"":format_date($row->created_on)." ".format_time($row->created_on);

			$row->old_due_date=empty($row->old_due_date)?"":format_date($row->old_due_date)." ".format_time($row->old_due_date);

			$row->new_due_date=empty($row->new_due_date)?"":format_date($row->new_due_date)." ".format_time($row->new_due_date);

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
				concat(e.name,' - ',f.name) as route_name, 
				d.name as ship_class_name,
				c.name as service_name,
				b.booking_code,
				a.customer_name, 
				dt.*
				from  app.t_trx_duedate_extends dt
				join  app.t_trx_invoice a on dt.trans_number=a.trans_number
				join app.t_trx_booking b on dt.trans_number=b.trans_number
				join app.t_mtr_service c on b.service_id=c.id
				join app.t_mtr_ship_class d on b.ship_class=d.id
				join app.t_mtr_port e on b.origin=e.id
				join app.t_mtr_port f on b.destination=f.id
				{$where} 
				{$order} ";
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

    public function searchData($data)
    {
    	return $this->dbView->query("SELECT 
						concat(e.name,' - ',f.name) as route_name, 
						d.name as ship_class_name,
						c.name as service_name,
						b.booking_code,
						a.*
						from  app.t_trx_invoice a
						join app.t_trx_booking b on a.trans_number=b.trans_number
						join app.t_mtr_service c on b.service_id=c.id
						join app.t_mtr_ship_class d on b.ship_class=d.id
						join app.t_mtr_port e on b.origin=e.id
						join app.t_mtr_port f on b.destination=f.id
    	    			where a.status=1 and a.trans_number='{$data}' 
    	    				-- and (a.trans_number='{$data}' or b.booking_code='{$data}')
    	    			");
    }

	public function select_data($table, $where="")
	{
		return $this->dbView->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->dbAction->insert($table, $data);
	}

	public function update_data($table,$data,$where)
	{
		$this->dbAction->where($where);
		$this->dbAction->update($table, $data);
	}

	public function delete_data($table,$data,$where)
	{
		$this->dbAction->where($where);
		$this->dbAction->delete($table, $data);  
	} 

}