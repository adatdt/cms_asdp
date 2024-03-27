<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : PembatasanQuotaModel
 * -----------------------
 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  2022
 *
 */

class PembatasanQuotaModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module   = 'master_data2/pembatasanQuota';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$dateFrom = trim($this->input->post('dateFrom'));
		$dateTo = trim($this->input->post('dateTo'));
		$shipClass = trim($this->enc->decode($this->input->post('shipClass')));
		$portId = trim($this->enc->decode($this->input->post('portId')));
		$vehicleClassId = trim($this->enc->decode($this->input->post('vehicleClassId')));
		
		$field = array(
			0 =>'id',
			1=>"port_name",
			2=>"ship_class_name",
			3=>"jenis_pj",
			4=>"golongan",
			5=>"quota",
			6=>"total_lm",
			7=>"start_date",
			8=>"end_date",
			9=>"depart_time",
			10=>"status",
			11=>"actions",			
		);

		$order_column = $field[$order_column];

		$where = " WHERE rsc.status not in (-5) and start_date <= '{$dateFrom}' and end_date >= '{$dateTo}' ";

		if(!empty($portId))
		{
			$where .= " and rsc.port_id='{$portId}' ";
		}

		if(!empty($shipClass))
		{
			$where .= " and rsc.ship_class='{$shipClass}' ";
		}

		if(!empty($vehicleClassId))
		{
			$where .= " and rsc.vehicle_class_id='{$vehicleClassId}' ";
		}

		$sql=$this->qry($where);

		// die($sql); exit;

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
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;

			$row->actions  =" ";

			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
  	
			if($row->status == 1){
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url); // edit belom di devlop karena tidak ada di requirement
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

			$row->start_date=format_date($row->start_date);
			$row->end_date=format_date($row->end_date);

			$row->quota=idr_currency($row->quota);
			$row->total_lm=idr_currency($row->total_lm);

     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
			

			$rows[] = $row;
			unset($row->assignment_code);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}
    public function download(){

		$dateFrom = trim($this->input->get('dateFrom'));
		$dateTo = trim($this->input->get('dateTo'));
		$shipClass = trim($this->enc->decode($this->input->get('shipClass')));
		$portId = trim($this->enc->decode($this->input->get('portId')));
		$vehicleClassId = trim($this->enc->decode($this->input->get('vehicleClassId')));
		
		$where = " WHERE rsc.status not in (-5) and start_date <= '{$dateFrom}' and end_date >= '{$dateTo}' ";

		if(!empty($portId))
		{
			$where .= " and rsc.port_id='{$portId}' ";
		}

		if(!empty($shipClass))
		{
			$where .= " and rsc.ship_class='{$shipClass}' ";
		}

		if(!empty($vehicleClassId))
		{
			$where .= " and rsc.vehicle_class_id='{$vehicleClassId}' ";
		}

		$where .=" order by rsc.start_date desc ";

		$sql = $this->qry($where);

		$rows_data = $this->db->query($sql)->result();

		$rows 	= array();
		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);


			$row->actions  =" ";

			$row->start_date=format_date($row->start_date);
			$row->end_date=format_date($row->end_date);

			$row->quota=idr_currency($row->quota);
			$row->total_lm=idr_currency($row->total_lm);

			if($row->status == 1){
				$row->status   = 'Aktif';
			}
			else
			{
				$row->status   = 'Tidak Aktif';
			}
			

			$rows[] = $row;
			unset($row->assignment_code);
			
		}

		return $rows;
	}

	public function qry($where="")
	{
		$qry="SELECT
			rsc.id,
			rsc.status,
			tmp.name as port_name,
			tmsc.name as ship_class_name,
			tmvc.name as golongan,
			rsc.quota,
			rsc.total_lm,
			rsc.start_date,
			(
				select 
					array_agg(depart_time::text)
				from app.t_mtr_quota_pcm_vehicle_restrictions_detail 
				where restriction_quota_code=rsc.restriction_quota_code	and status = 1
			) as depart_time,
			'KENDARAAN' as jenis_pj,
			rsc.end_date,
			rsc.restriction_quota_code
			from app.t_mtr_quota_pcm_vehicle_restrictions rsc
			left join app.t_mtr_port tmp on rsc.port_id=tmp.id
			left join app.t_mtr_ship_class tmsc on rsc.ship_class=tmsc.id
			left join app.t_mtr_vehicle_class tmvc on rsc.vehicle_class_id = tmvc.id
			$where
		";
		return $qry;
	}
    public function pembatasanQuotaDetail(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$port_name= $this->input->post('port');
		$team_name= $this->input->post('team');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
			

		);

		$order_column = $field[$order_column];

		$where = " WHERE rsc.status not in (-5) ";

		$sql=$this->qryDetail($where);

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
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;

			$row->actions  =" ";

			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

			 $row->quota=idr_currency($row->quota);
			 $row->used_quota=idr_currency($row->used_quota);
			 $row->total_quota=idr_currency($row->total_quota);
     		

			if($row->status == 1){
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

			$row->depart_date=format_date($row->depart_date);
			$row->depart_time=format_time($row->depart_time);

     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
			

			$rows[] = $row;
			unset($row->assignment_code);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	public function qryDetail($where="")
	{
		$qry="SELECT
			tmp.name as port_name,
			tmsc.name as ship_class_name,
			tmvc.name as golongan,
			rsc.quota,
			rsc.depart_date,
			rsc.depart_time,
			rsc.used_quota,
			rsc.id,
			rsc.status,
			rsc.total_quota,
			rsc.restriction_quota_code
			from app.t_trx_quota_pcm_vehicle_restrictions rsc
			left join app.t_mtr_port tmp on rsc.port_id=tmp.id
			left join app.t_mtr_ship_class tmsc on rsc.ship_class=tmsc.id
			left join app.t_mtr_vehicle_class tmvc on rsc.vehicle_class_id = tmvc.id
			$where
		";
		return $qry;
	}	
	public function select_data($table, $where)
	{
		return $this->dbView->query("select * from $table $where");
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

	public function checkOverlaps($startDate, $endDate, $port,$shipClass,$vehicleClass="",$jam="" ,$restriction_quota_code="")
    {
		$where ="";
		if(!empty($restriction_quota_code))
		{
			$where .=" and dr.restriction_quota_code <> '".$restriction_quota_code."'";
		}

		if(!empty($vehicleClass))
		{
			$where .="and r.vehicle_class_id in ($vehicleClass)";
		}
		if(!empty($jam))
		{
			$where .="and dr.depart_time in ($jam)";
		}

        $qry="        
            select  dr.depart_time, r.* from app.t_mtr_quota_pcm_vehicle_restrictions r
			join app.t_mtr_quota_pcm_vehicle_restrictions_detail dr on r.restriction_quota_code=dr.restriction_quota_code
            where 
			(
				(r.start_date between '{$startDate}' and '{$endDate}')
				or
				(r.end_date between '{$startDate}' and '{$endDate}')
				or
				(r.start_date<='{$startDate}' and r.end_date >='{$endDate}')
			)
			and r.status = 1
			and dr.status = 1
			and r.ship_class='$shipClass'
			and r.port_id='$port'
			{$where}
			order by vehicle_class_id asc
        ";

		// die($qry); exit;
        return $this->db->query($qry)->result();
    }
	public function checkQuotaGlobal($dateFrom, $dateTo, $portId, $shipClass)
	{
		// mencari batas awal range  master quota
		$qry2="SELECT 
				depart_date, 
				quota, 
				total_lm, 
				port_id, 
				ship_class  
					from app.t_mtr_quota_pcm_vehicle r
					where r.depart_date <= '{$dateFrom}'
					and port_id='{$portId}'
					and ship_class = '{$shipClass}'
				order by id desc limit 1 ";
		// die($qry2); exit;
		$data2 = $this->dbView->query($qry2)->row();
		// print_r($data2); exit;
		$start = $data2->depart_date;
		$end = $dateTo;


		// mendapatkan master quota untuk dibandingin
		$qry="SELECT 
			depart_date,
			quota,
			total_lm,
			port_id,
			ship_class
		from app.t_mtr_quota_pcm_vehicle r
			where r.depart_date between '{$start}' and '{$end}'
			and port_id='{$portId}'
			and ship_class = '{$shipClass}'
		order by id desc";

		$data = $this->dbView->query($qry)->result();

		return $data;
	}
	public function getVehicleClass()
	{
		return $this->select_data("app.t_mtr_vehicle_class"," where status=1 order by name asc ")->result();
	}
	public function getPort()
	{
		return $this->select_data("app.t_mtr_port"," where status=1 order by name asc ")->result();
	}
	public function getShipClass()
	{
		return $this->select_data("app.t_mtr_ship_class"," where status=1 order by name asc ")->result();
	}
	public function insertEdit($param)
	{
		$quota = $param["quota"];
		$total_lm = $param["total_lm"];
		$restrictionQuotaCode = $param["restrictionQuotaCode"];
		$updated_on = $param["updated_on"];
		$updated_by = $param["updated_by"];
		$action = $param["action"];

		$getDataUpdate=$this->select_data("app.t_trx_quota_pcm_vehicle_restrictions", " where restriction_quota_code='{$restrictionQuotaCode}' and status <> '-5' " )->result();
				
		// print_r($getDataUpdate); exit;
		if($getDataUpdate)
		{
			// jika aksinya min
			if($action==2)
			{

				foreach ($getDataUpdate as $key => $value) {
					if(($value->total_quota - $quota) >=0 ) // yang di update hanya data yang dingurangi tidak 0, di samakan dengan rule pcm
					{
						$this->db->query("							
								update app.t_trx_quota_pcm_vehicle_restrictions  
									set quota=quota-{$quota},
									total_quota=total_quota-{$quota},
									quota_limit=quota_limit+{$quota},
									total_lm={$total_lm},
									updated_on='{$updated_on}',
									updated_by='{$updated_by}' 
								where id='{$value->id}' 
								");
					}

				}
					
													
			}
			else // jika dia nambah data
			{
				foreach ($getDataUpdate as $key => $value) 
				{

					$this->db->query("							
					update app.t_trx_quota_pcm_vehicle_restrictions 
						set quota=quota+{$quota},
						total_quota=total_quota+{$quota},
						quota_limit=quota_limit-{$quota},
						total_lm={$total_lm},
						updated_on='{$updated_on}',
						updated_by='{$updated_by}' 
						where id='{$value->id}'
					");
				}
			}
		}
	} 

}
