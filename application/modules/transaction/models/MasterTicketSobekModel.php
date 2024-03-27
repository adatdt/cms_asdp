<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Tiket sobek
 * -----------------------
 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  2021
 *
 */

class MasterTicketSobekModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/masterTicketSobek';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);


	    $port=$this->enc->decode($this->input->post("port"));
	    $golongan=trim(strtoupper($this->input->post("golongan")));
	    $ship_class=$this->enc->decode($this->input->post("ship_class"));
	    $service=$this->enc->decode($this->input->post("service"));
	    $status=$this->enc->decode($this->input->post("status"));


	    $searchData=$this->input->post("searchData");
        $searchName=$this->input->post("searchName");

		$iLike        = trim(strtoupper($this->db->escape_like_str($searchName)));

		$field = array
		(
			0 =>'created_on',
			1=>'ticket_number',
			2=>'port_name',
			3=>'layanan',
			4=>'service_name',
			5=>'golongan',
			6=>'created_on',
			7=>'created_by',
			8=>'used_on',
			9=>'status',
		);


		$order_column = $field[$order_column];

		$where = " WHERE tm.status not in (-5) ";

		if(!empty($port))
		{
			$where .=" and (tm.port_id=".$port.")"; 
		}

		if(!empty($ship_class))
		{
			$where .=" and (tm.ship_class=".$ship_class.")"; 
		}

		if(!empty($service))
		{
			$where .=" and (tm.service_id=".$service.")"; 
		}

		if($status!="")
		{
			$where .=" and (tm.status=".$status.")"; 
		}		

		if(!empty($golongan))
		{
			$where .=" and (upper(gl.name)='".$golongan."' )"; 
		}		

		if(!empty($searchData))
		{
			if($searchName=="ticketNumber")
			{
				$where .=" and (tm.ticket_number ilike '%".$searchData."%')";
			}
		}						


		$sql 		   = $this->qry($where);

		$query         = $this->db->query($sql);
		// $records_total = $query->num_rows();
		$records_total = $this->qryCount($where);
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$row->number = $i;

			$id=$this->enc->encode($row->id);
			$service_id=$this->enc->encode($row->service_id);

			$edit_url 	 = site_url($this->_module."/edit/{$id}/{$service_id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id}/{$service_id}");

     		$row->actions="";     		

     		if ($row->status==1)
     		{
     			$row->status=success_label("Aktif");     			
     			$row->actions.= generate_button_new($this->_module, 'edit', $edit_url);
     			$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);



     		}
     		else
     		{
     			$row->status=failed_label("Tidak Aktif");	

     		}

     		$row->created_on=format_date($row->created_on)." ".format_time($row->created_on);
     		$row->updated_on=!empty($row->updated_on)?format_date($row->updated_on)." ".format_time($row->updated_on):"";
			$row->used_on=!empty($row->used_on)?format_date($row->used_on)." ".format_time($row->used_on):"";



     		$row->no=$i;
     		

			$rows[] = $row;
			// unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

    function qry($where="")
    {
        $qry="            
                SELECT * from 
                (
                    select 
                        sv.name as service_name,
                        sv.id as service_id,
                        gl.name as golongan,
                        sc.name as layanan,
                        sc.id as ship_class,
                        p.name as port_name,
                        tm.ticket_number,
                        tm.created_on,
                        tm.created_by,
                        tm.updated_on,
                        tm.status,
                        tm.port_id,
						tm.used_on,
                        tm.id
                    from app.t_mtr_ticket_manual_passanger tm
                    left join app.t_mtr_service sv on tm.service_id=sv.id
                    left join app.t_mtr_passanger_type gl on tm.passanger_type_id=gl.id
                    left join app.t_mtr_ship_class sc on tm.ship_class=sc.id
                    left join app.t_mtr_port p on tm.port_id=p.id
                    {$where}
                    union 
                    select 
                        sv.name as service_name,
                        sv.id as service_id,
                        gl.name as golongan,
                        sc.name as layanan,
                        sc.id as ship_class,
                        p.name as port_name,
                        tm.ticket_number,
                        tm.created_on,
                        tm.created_by,
                        tm.updated_on,
                        tm.status,
                        tm.port_id,
						tm.used_on,
                        tm.id
                    from app.t_mtr_ticket_manual_vehicle tm
                    left join app.t_mtr_service sv on tm.service_id=sv.id
                    left join app.t_mtr_vehicle_class gl on tm.vehicle_class_id=gl.id
                    left join app.t_mtr_ship_class sc on tm.ship_class=sc.id
                    left join app.t_mtr_port p on tm.port_id=p.id
                    $where
                ) dt


        ";

        return $qry;
    }

    function qryCount($where="")
    {
        $qryPassanger="            
                    SELECT
                        count(tm.id) as count_data
                    from app.t_mtr_ticket_manual_passanger tm
                    left join app.t_mtr_service sv on tm.service_id=sv.id
                    left join app.t_mtr_passanger_type gl on tm.passanger_type_id=gl.id
                    left join app.t_mtr_ship_class sc on tm.ship_class=sc.id
                    left join app.t_mtr_port p on tm.port_id=p.id
                    {$where}
                "  ;
                
        $qryVehicle="
        			SELECT
                        count(tm.id) as count_data
                    from app.t_mtr_ticket_manual_vehicle tm
                    left join app.t_mtr_service sv on tm.service_id=sv.id
                    left join app.t_mtr_vehicle_class gl on tm.vehicle_class_id=gl.id
                    left join app.t_mtr_ship_class sc on tm.ship_class=sc.id
                    left join app.t_mtr_port p on tm.port_id=p.id
                    {$where} ";

        $countPassanger=$this->db->query($qryPassanger)->row();
        $countVehicle=$this->db->query($qryVehicle)->row();

        return $countPassanger->count_data + $countVehicle->count_data;
    }

	public function select_data($table, $where="")
	{
		return $this->db->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->db->insert($table, $data);
	}
	public function insert_data_batch($table,$data)
	{

		$this->db->insert_batch($table, $data);	
	}
	public function update_data($table,$data,$where)
	{
		$this->db->where($where);
		$this->db->update($table, $data);
	}

	public function delete_data($table,$data,$where)
	{
		$this->db->where($where);
		$this->db->delete($table, $data);
	}


}
