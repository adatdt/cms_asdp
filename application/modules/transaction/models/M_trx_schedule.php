<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class M_trx_schedule extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/trx_schedule';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		
		$dock = $this->enc->decode($this->input->post('dock'));
		$service = $this->enc->decode($this->input->post('service'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));


		$field = array(
			0 =>'id',
			1=>'schedule_code',
			2 =>'ship_name',
			3 =>'schedule_date',
			4 =>'port_name',
			5 =>'dock_name',
			6 =>'port_destination',
			7 =>'ploting_date',
			8 =>'docking_date',
			9 =>'open_boarding_date',
			10 =>'close_boarding_date',
			11 =>'close_ramp_door_date',
			12 =>'sail_date',
		);

		// check app identity
		$app_identity=$this->select_data("app.t_mtr_identity_app","")->row();

		if($app_identity->port_id==0)
		{
			if(empty($this->session->userdata('port_id')))
			{
				$port = $this->enc->decode($this->input->post('port'));
			}
			else
			{
				$port = $this->session->userdata('port_id');
			}
		}
		else
		{
			$port=$app_identity->port_id;
		}

		$order_column = $field[$order_column];

		$where = " WHERE a.status is not null and (a.ploting_date is not null 
						or a.docking_date is not null
						or a.open_boarding_date is not null 
					)
					and
					(to_char(a.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )

			";

		// if (!empty($dateTo) and !empty($dateFrom))
		// {
		// 	$where .="and (to_char(a.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		// }
		// else if(empty($dateFrom) and !empty($dateTo))
		// {
		// 	$where .="and (to_char(a.schedule_date,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		// }
		// else if (!empty($dateFrom) and empty($dateTo))
		// {
		// 	$where .="and (to_char(a.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		// }
		// else
		// {
		// 	$where .="and (to_char(a.schedule_date,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		// }

		if(!empty($port))
		{
			$where .="and (a.port_id=".$port.")";
		}

		if(!empty($dock))
		{
			$where .="and (a.dock_id=".$dock.")";
		}

		if (!empty($search['value'])){
			$where .="and (
						e.name ilike '%".$iLike."%' 
						or a.schedule_code ilike '".$iLike."'
						)";	
		}

		$sql 		   = "
						select e.name as ship_name, d.name as dock_name, c.name as port_destination, b.name as port_name, a.* from app.t_trx_schedule a
						left join app.t_mtr_port b on a.port_id=b.id
						left join app.t_mtr_port c on a.destination_port_id=c.id
						left join app.t_mtr_dock d on a.dock_id=d.id
						left join app.t_mtr_ship e on a.ship_id=e.id
						$where
						 ";

		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
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
			$nonaktif = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
     		$aktif = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

			$id=$this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id}");

     		$row->actions="";

     		$row->actions.= generate_button_new($this->_module, 'edit', $edit_url);

     		$row->schedule_date=empty($row->schedule_date)?"":format_date($row->schedule_date);
     		$row->created_on=empty($row->created_on)?"":format_dateTimeHis($row->created_on);

     		$row->ploting_date=empty($row->ploting_date)?"":format_dateTimeHis($row->ploting_date);
            $row->docking_date=empty($row->docking_date)?"":format_dateTimeHis($row->docking_date);
            $row->open_boarding_date=empty($row->open_boarding_date)?"":format_dateTimeHis($row->open_boarding_date);
            $row->close_boarding_date=empty($row->close_boarding_date)?"":format_dateTimeHis($row->close_boarding_date);
            $row->close_ramp_door_date=empty($row->close_ramp_door_date)?"":format_dateTimeHis($row->close_ramp_door_date);
            $row->sail_date=empty($row->sail_date)?"":format_dateTimeHis($row->sail_date);

     		$row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

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

	public function  download()
	{
		
		$get = $this->input->get();

		$dateFrom= $get['sortFrom'];
		$dateTo= $get['sortTo'];
		$search= trim($this->input->get('search'));

		$dock= $this->enc->decode($this->input->get('dock'));

		
		// check app identity
		$app_identity=$this->select_data("app.t_mtr_identity_app","")->row();

		if($app_identity->port_id==0)
		{
			if(empty($this->session->userdata('port_id')))
			{
				$port = $this->enc->decode($this->input->get('port'));
			}
			else
			{
				$port = $this->session->userdata('port_id');
			}
		}
		else
		{
			$port=$app_identity->port_id;
		}



		$where = " WHERE a.status is not null and (a.ploting_date is not null 
						or a.docking_date is not null
						or a.open_boarding_date is not null 
					)
					and
					(to_char(a.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )

			";

		// if (!empty($dateTo) and !empty($dateFrom))
		// {
		// 	$where .="and (to_char(a.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		// }
		// else if(empty($dateFrom) and !empty($dateTo))
		// {
		// 	$where .="and (to_char(a.schedule_date,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		// }
		// else if (!empty($dateFrom) and empty($dateTo))
		// {
		// 	$where .="and (to_char(a.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		// }
		// else
		// {
		// 	$where .="and (to_char(a.schedule_date,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		// }

		if(!empty($port))
		{
			$where .="and (a.port_id=".$port.")";
		}

		if(!empty($dock))
		{
			$where .="and (a.dock_id=".$dock.")";
		}

		if (!empty($search['value'])){
			$where .="and (e.name ilike '%".$iLike."%' )";	
		}

		$sql 		   = "
						select e.name as ship_name, d.name as dock_name, c.name as port_destination, b.name as port_name, a.* from app.t_trx_schedule a
						left join app.t_mtr_port b on a.port_id=b.id
						left join app.t_mtr_port c on a.destination_port_id=c.id
						left join app.t_mtr_dock d on a.dock_id=d.id
						left join app.t_mtr_ship e on a.ship_id=e.id
						$where order by a.updated_on  desc
						 ";


		return $this->db->query($sql);
	}

	public function select_data($table, $where="")
	{
		return $this->db->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->db->insert($table, $data);
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
