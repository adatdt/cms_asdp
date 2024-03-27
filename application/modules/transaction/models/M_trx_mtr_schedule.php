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

class M_trx_mtr_schedule extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/trx_mtr_schedule';
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
				0=> "id",
                1=>"trx_schedule_code",
                2=>"schedule_date",
                3=>"port_name",
                4=>"dock_name",
                5=>"port_destination",
                6=>"pl_ship_name",
                7=>"rl_ship_name",
                8=>"ploting_date",
                9=>"docking_on",
                10=>"rl_docking",
                11=>"open_boarding_on",
                12=>"rl_open_boarding",
                13=>"close_boarding_on",
                14=>"rl_close_boarding",
                15=>"close_rampdoor_on",
                16=>"rl_close_ramp_door",
                17=>"sail_time",
                18=>"rl_sail",
                19=>"trip",
                20=>"call", 

		);

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

		$where = " WHERE aa.status not in (-5)  and 
					(	a.ploting_date is not null 
						or a.docking_date is not null
						or a.open_boarding_date is not null 
					)
					 and
					(to_char(aa.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )" ;

		// if (!empty($dateTo) and !empty($dateFrom))
		// {
		// 	$where .="and (to_char(aa.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		// }
		// else if(empty($dateFrom) and !empty($dateTo))
		// {
		// 	$where .="and (to_char(aa.schedule_date,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		// }
		// else if (!empty($dateFrom) and empty($dateTo))
		// {
		// 	$where .="and (to_char(aa.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		// }
		// else
		// {
		// 	$where .="and (to_char(aa.schedule_date,'yyyy-mm-dd') between '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		// }

		if(!empty($port))
		{
			$where .="and (aa.port_id=".$port.")";
		}

		if(!empty($dock))
		{
			$where .="and (aa.dock_id=".$dock.")";
		}

		if (!empty($search['value'])){
			$where .="and (f.name ilike '%".$iLike."%' 
							or e.name ilike '%".$iLike."%' 
							or f.name ilike '%".$iLike."%' 
							or a.schedule_code ilike '%".$iLike."%' 
						)";	
		}

		$sql 		   = "
						select 
						(
						case 
						when a.ploting_date is null then null
						else e.name  end) as rl_ship_name,
						a.schedule_code as trx_schedule_code,
						a.call,  f.name as pl_ship_name,a.ploting_date, a.docking_date as rl_docking, a.open_boarding_date as rl_open_boarding, 
						a.close_boarding_date as rl_close_boarding, a.close_ramp_door_date as rl_close_ramp_door, a.sail_date as rl_sail,
						d.name as dock_name, c.name as port_destination, b.name as port_name, aa.* 
						from app.t_trx_schedule a 
						left join app.t_mtr_schedule aa on a.schedule_code=aa.schedule_code
						left join app.t_mtr_port b on aa.port_id=b.id
						left join app.t_mtr_port c on a.destination_port_id=c.id
						left join app.t_mtr_dock d on a.dock_id=d.id
						left join app.t_mtr_ship e on a.ship_id=e.id
						left join app.t_mtr_ship f on aa.ship_id=f.id
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
     		$row->ploting_date=empty($row->ploting_date)?"":format_dateTimeHis($row->ploting_date);
     		$row->docking_on=empty($row->docking_on)?"":format_dateTimeHis($row->docking_on);
     		$row->rl_docking=empty($row->rl_docking)?"":format_dateTimeHis($row->rl_docking);
     		$row->open_boarding_on=empty($row->open_boarding_on)?"":format_dateTimeHis($row->open_boarding_on);
     		$row->rl_open_boarding=empty($row->rl_open_boarding)?"":format_dateTimeHis($row->rl_open_boarding);
     		$row->close_boarding_on=empty($row->close_boarding_on)?"":format_dateTimeHis($row->close_boarding_on);
     		$row->rl_open_boarding=empty($row->rl_open_boarding)?"":format_dateTimeHis($row->rl_open_boarding);
     		$row->close_boarding_on=empty($row->close_boarding_on)?"":format_dateTimeHis($row->rl_open_boarding);
     		$row->rl_close_boarding=empty($row->rl_close_boarding)?"":format_dateTimeHis($row->rl_close_boarding);
     		$row->close_rampdoor_on=empty($row->close_rampdoor_on)?"":format_dateTimeHis($row->close_rampdoor_on);
     		$row->rl_close_ramp_door=empty($row->rl_close_ramp_door)?"":format_dateTimeHis($row->rl_close_ramp_door);
     		$row->sail_time=empty($row->sail_time)?"":format_dateTimeHis($row->sail_time);
     		$row->rl_sail=empty($row->rl_sail)?"":format_dateTimeHis($row->rl_sail);


     		$row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

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

	public function  download()
	{
		
		$get = $this->input->get();

		$dateFrom= $get['sortFrom'];
		$dateTo= $get['sortTo'];
		$search= trim($this->input->get('search'));
		$port= $this->enc->decode($this->input->get('port'));
		$dock= $this->enc->decode($this->input->get('dock'));

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

		

		$where = " WHERE aa.status not in (-5)  and 
					(a.ploting_date is not null) and
					(to_char(aa.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )" ;

		// if (!empty($dateTo) and !empty($dateFrom))
		// {
		// 	$where .="and (to_char(aa.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		// }
		// else if(empty($dateFrom) and !empty($dateTo))
		// {
		// 	$where .="and (to_char(aa.schedule_date,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		// }
		// else if (!empty($dateFrom) and empty($dateTo))
		// {
		// 	$where .="and (to_char(aa.schedule_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		// }
		// else
		// {
		// 	$where .="and (to_char(aa.schedule_date,'yyyy-mm-dd') between '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		// }

		if(!empty($port))
		{
			$where .="and (aa.port_id=".$port.")";
		}

		if(!empty($dock))
		{
			$where .="and (aa.dock_id=".$dock.")";
		}

		if (!empty($search['value'])){
			$where .="and (f.name ilike '%".$iLike."%' 
							or e.name ilike '%".$iLike."%' 
							or f.name ilike '%".$iLike."%' 
							or a.schedule_code ilike '%".$iLike."%' 
						)";	
		}

		$sql 		   = "
						select 
						(
						case 
						when a.ploting_date is null then null
						else e.name  end) as rl_ship_name,
						a.schedule_code as trx_schedule_code,
						a.call,  f.name as pl_ship_name,a.ploting_date, a.docking_date as rl_docking, a.open_boarding_date as rl_open_boarding, 
						a.close_boarding_date as rl_close_boarding, a.close_ramp_door_date as rl_close_ramp_door, a.sail_date as rl_sail,
						d.name as dock_name, c.name as port_destination, b.name as port_name, aa.* 
						from app.t_trx_schedule a 
						left join app.t_mtr_schedule aa on a.schedule_code=aa.schedule_code
						left join app.t_mtr_port b on aa.port_id=b.id
						left join app.t_mtr_port c on a.destination_port_id=c.id
						left join app.t_mtr_dock d on a.dock_id=d.id
						left join app.t_mtr_ship e on a.ship_id=e.id
						left join app.t_mtr_ship f on aa.ship_id=f.id
						$where
						order by aa.schedule_date desc 
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
