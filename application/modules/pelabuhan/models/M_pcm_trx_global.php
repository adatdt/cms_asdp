<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**

 * @author     adat <adatdt.@gmail.com>
 * @copyright  2020
 *
 */

class M_pcm_trx_global extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'pelabuhan/pcm_trx_global';

	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateTo=$this->input->post('dateTo');
		$dateFrom=$this->input->post('dateFrom');
		$port= $this->enc->decode($this->input->post('port'));
		$time= $this->enc->decode($this->input->post('time'));
		$shipClass= $this->enc->decode($this->input->post('shipClass'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		$vehicleClass="";

		
		$field = array(
					0=>"id",
					1=>"id",
                    2=>"port_name",
                    3=>"ship_class_name",
                    4=>"depart_date",
                    5=>"depart_time",
                    6=>"quota",
                    7=>"total_quota",
                    8=>"used_quota",
                    9=>"total_lm",
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) and (a.depart_date between '{$dateFrom}' and '{$dateTo}')";

		if(!empty($port))
		{
			$where .="and ( a.port_id='{$port}' )";	
		}

		if(!empty($shipClass))
		{
			$where .="and ( a.ship_class='{$shipClass}' )";	
		}

		if(!empty($time))
		{
			$where .="and ( a.depart_time='{$time}' )";	
		}				

		if(!empty($search['value']))
		{
			$where .="and ( b.name ilike '%".$iLike."%'
							or c.name ilike '%".$iLike."%'
						)";
		}

		$getDataTrxRestrict=$this->getDataTrxRestrict($dateFrom, $dateTo, $port,$shipClass,$vehicleClass,$time);
		$sql = $this->qry($where);


		// $query         = $this->db->query($sql);
		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		// $query     = $this->db->query($sql);
		$query     =$this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions  =" ";
			$row->tgl_depart_date = $row->depart_date;
     		$param =array(
     						'portId'=>$row->port_id,
     						'departTime'=>$row->depart_time,
     						'departDate'=>$row->depart_date,
     						'shipClass'=>$row->ship_class );

     		$getLm=$this->getLmStatus($param);

     		$row->lmTersedia=str_replace(".",",",$getLm->ketersediaan);
     		$row->lmDigunakan=str_replace(".",",",$getLm->sudahdigunain);

			$row->quota_restrict ="";
			$row->used_quota_restrict ="";
			$row->total_quota_restrict ="";
			$row->vehicle_class_id ="";
			$row->vehicle_class_name ="";

			foreach ($getDataTrxRestrict as $key2=> $getDataTrxRestrict2) {
				if($getDataTrxRestrict2['port_id']==$row->port_id &&
				$getDataTrxRestrict2['ship_class']==$row->ship_class &&
				$getDataTrxRestrict2['depart_time']==$row->depart_time &&
				$getDataTrxRestrict2['depart_date']==$row->depart_date )
				{
					$row->quota_restrict =$getDataTrxRestrict2['quota'];
					$row->used_quota_restrict =$getDataTrxRestrict2['used_quota'];
					$row->total_quota_restrict =$getDataTrxRestrict2['total_quota'];
					$row->vehicle_class_id =$getDataTrxRestrict2['vehicle_class_id'];
					$row->vehicle_class_name =$getDataTrxRestrict2['vehicle_class_name'];
				}
			}

     		$row->no=$i;

			 $row->id_quota_restriction = empty($row->id_quota_restriction)?"":'<span  class="label label-success klik-detail"><i class="fa fa-plus" aria-hidden="true"></i></span>';

     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
 			if($row->depart_date." ".$row->depart_time>=date('Y-m-d H').":00" )
			{

				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
			}
     		// $row->actions .= generate_button_new($this->_module, 'edit', $edit_url);
     		$row->depart_date=empty($row->depart_date)?"":format_date($row->depart_date);

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

	public function pembatasanQuotaDetail_15122022(){
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

		$shipClass=$this->input->post('shipClass');
		$portId=$this->input->post('portId');
		$departDate=$this->input->post('departDate');
		$departTime=$this->input->post('departTime');
		$idTable=$this->input->post('idTable');
		$vehicleClass=$this->enc->decode($this->input->post('vehicleClass'));
		
		$field = array(
			0 =>'depart_date',
			1 =>'vehicle_class_id',
			2 =>'quota',
			3 =>'used_quota',
			4 =>'total_quota',
		);


		$order_column = $field[$order_column];

		// $where = " WHERE rsc.status not in (-5) ";

		// $sql=$this->qryDetail($where);

		$sqlTrx ="SELECT  
				vc.name as vehicle_class_name,
				r.quota,
				r.id,
				r.total_lm,
				r.vehicle_class_id,
				r.total_quota,
				r.depart_date,
				r.depart_time,
				r.status,
				r.ship_class,
				r.port_id,
				r.restriction_quota_code,
				r.used_quota 		
			 from app.t_trx_quota_pcm_vehicle p
			 join app.t_trx_quota_pcm_vehicle_restrictions r			 
				on p.port_id = r.port_id
				and p.depart_time = r.depart_time 
				and p.ship_class = r.ship_class 
				and p.depart_date = r.depart_date
			join app.t_mtr_vehicle_class vc on r.vehicle_class_id = vc.id	
			join app.t_mtr_quota_pcm_vehicle_restrictions h on r.restriction_quota_code = h.restriction_quota_code
			where p.depart_time='{$departTime}'
			and p.depart_date='{$departDate}'
			and p.ship_class='{$shipClass}'
			and p.port_id='{$portId}'
			and h.status=1
			and r.status=1
		";

		$sqlMaster ="SELECT
				p.quota,
				p.vehicle_class_id,
				p.port_id,
				p.ship_class,
				'{$departDate}' as depart_date,
				p.id,
				p.total_lm,
				p.quota as total_quota,
				p.quota as used_quota,
				pd.depart_time,
				p.restriction_quota_code,
				p.status
			from app.t_mtr_quota_pcm_vehicle_restrictions p
			left join app.t_mtr_quota_pcm_vehicle_restrictions_detail pd on pd.restriction_quota_code = p.restriction_quota_code 
			where (start_date <= '{$departDate}' and end_date >= '{$departDate}')
			and p.port_id ='{$portId}'
			and p.ship_class ='{$shipClass}'
			and p.status = 1
			and pd.status = 1
			and pd.depart_time ='{$departTime}'		
		";

		// die ($sqlMaster);
		// die ($sqlTrx);
		// exit;
		$dataMaster = $this->db->query($sqlMaster)->result();
		$dataTrx = $this->db->query($sqlTrx)->result();

		print_r($dataMaster); exit;
		// print_r($dataTrx); exit;

		$getData=array();
		if($dataMaster)
		{
			foreach ($dataMaster as $keydataMaster => $valuedataMaster) {
				if($dataTrx)
				{
					
					foreach ($dataTrx as $keydataTrx => $valuedataTrx) {

						
						if($valuedataTrx->depart_date == $valuedataMaster->depart_date &&
						  $valuedataTrx->depart_time == $valuedataMaster->depart_time &&
						  $valuedataTrx->ship_class == $valuedataMaster->ship_class &&
						  $valuedataTrx->port_id == $valuedataMaster->port_id &&
						  $valuedataTrx->vehicle_class_id == $valuedataMaster->vehicle_class_id 
						)
						{
							$getData[]=" ('{$valuedataTrx->depart_date}', 
										'{$valuedataTrx->depart_time}',
										'{$valuedataTrx->ship_class}',
										'{$valuedataTrx->port_id}',
										{$valuedataTrx->vehicle_class_id}, 
										'{$valuedataTrx->quota}',
										'{$valuedataTrx->used_quota}',
										'{$valuedataTrx->total_quota}',
										'{$valuedataTrx->status}',
										'{$valuedataTrx->total_lm}'
										)";							
						}
						else
						{
							// ini take out dulu karena buat data double
							$getData[]=" ('{$valuedataMaster->depart_date}',
							'{$valuedataMaster->depart_time}',
							'{$valuedataMaster->ship_class}',
							'{$valuedataMaster->port_id}',
							{$valuedataMaster->vehicle_class_id},
							'{$valuedataMaster->quota}',
							0,
							'{$valuedataMaster->total_quota}',
							'{$valuedataMaster->status}',
							'{$valuedataMaster->total_lm}'
							)";
						}
					}
				}
				else
				{
					$getData[]=" ('{$valuedataMaster->depart_date}',
					'{$valuedataMaster->depart_time}',
					'{$valuedataMaster->ship_class}',
					'{$valuedataMaster->port_id}',
					{$valuedataMaster->vehicle_class_id}, 
					'{$valuedataMaster->quota}',
					0,
					'{$valuedataMaster->total_quota}',
					'{$valuedataMaster->status}',
					'{$valuedataMaster->total_lm}'
					)";
				}
			}
		}
		else
		{
			$getData[]=" (null,
			null,
			null,
			null,
			null::integer,
			null,
			null,
			null,
			null,
			null
			)";
		}

		// exit;


		

		$where = " where p.depart_date is not null ";

		if(!empty($vehicleClass))
		{
			$where .=" and p.vehicle_class_id='{$vehicleClass}' "; 
		}

		$sql=" select vc.name as vehicle_class_name, p.* from (values ".implode(", ",$getData)." ) p(depart_date, depart_time, ship_class, port_id, vehicle_class_id, quota, used_quota, total_quota, status, total_lm) 
		left join app.t_mtr_vehicle_class vc on p.vehicle_class_id = vc.id
			$where
		";
		
		// print_r($getData);
		// die($sql); 
		// exit;

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
			// $id_enc=$this->enc->encode($row->id);
			$id_enc=$this->enc->encode($row->depart_date);
			$row->number = $i;

			$row->actions  =" ";

			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->depart_date.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->depart_date.'|1'));

			// $row->id =$row->id;
			$param =$row->depart_date."|".$row->ship_class."|".$row->vehicle_class_id;
			$param .="|".$row->depart_time."|".$row->port_id;
			$param .="|".$row->quota."|".$row->total_quota."|".$row->used_quota."|".$row->total_lm;
		

			$edit_url 	 = site_url($this->_module."/edit_restrict/{$this->enc->encode($param)}/{$idTable}");
     		$delete_url  = site_url($this->_module."/action_delete/{$this->enc->encode($param)}/{$idTable}");



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

		$shipClass=$this->input->post('shipClass');
		$portId=$this->input->post('portId');
		$departDate=$this->input->post('departDate');
		$departTime=$this->input->post('departTime');
		$idTable=$this->input->post('idTable');
		$vehicleClass=$this->enc->decode($this->input->post('vehicleClass'));
		
		$field = array(
			0 =>'depart_date',
			1 =>'vehicle_class_id',
			2 =>'quota',
			3 =>'used_quota',
			4 =>'total_quota',
		);


		$order_column = $field[$order_column];

		// $where = " WHERE rsc.status not in (-5) ";

		// $sql=$this->qryDetail($where);

		$sqlTrx ="SELECT  
				vc.name as vehicle_class_name,
				r.quota,
				r.id,
				r.total_lm,
				r.vehicle_class_id,
				r.total_quota,
				r.depart_date,
				r.depart_time,
				r.status,
				r.ship_class,
				r.port_id,
				r.restriction_quota_code,
				r.used_quota 		
			 from app.t_trx_quota_pcm_vehicle p
			 join app.t_trx_quota_pcm_vehicle_restrictions r			 
				on p.port_id = r.port_id
				and p.depart_time = r.depart_time 
				and p.ship_class = r.ship_class 
				and p.depart_date = r.depart_date
			join app.t_mtr_vehicle_class vc on r.vehicle_class_id = vc.id	
			join app.t_mtr_quota_pcm_vehicle_restrictions h on r.restriction_quota_code = h.restriction_quota_code
			where p.depart_time='{$departTime}'
			and p.depart_date='{$departDate}'
			and p.ship_class='{$shipClass}'
			and p.port_id='{$portId}'
			and h.status=1
			and r.status=1
		";

		$sqlMaster ="SELECT
				p.quota,
				p.vehicle_class_id,
				p.port_id,
				p.ship_class,
				'{$departDate}' as depart_date,
				p.id,
				p.total_lm,
				p.quota as total_quota,
				p.quota as used_quota,
				pd.depart_time,
				p.restriction_quota_code,
				p.status
			from app.t_mtr_quota_pcm_vehicle_restrictions p
			left join app.t_mtr_quota_pcm_vehicle_restrictions_detail pd on pd.restriction_quota_code = p.restriction_quota_code 
			where (start_date <= '{$departDate}' and end_date >= '{$departDate}')
			and p.port_id ='{$portId}'
			and p.ship_class ='{$shipClass}'
			and p.status = 1
			and pd.status = 1
			and pd.depart_time ='{$departTime}'		
		";

		// die ($sqlMaster);
		// die ($sqlTrx);
		// exit;
		$dataMaster = $this->db->query($sqlMaster)->result();
		$dataTrx = $this->db->query($sqlTrx)->result();

		// print_r($dataMaster); exit;
		// print_r($dataTrx); exit;

		$getData=array();
		if($dataMaster)
		{
			foreach ($dataMaster as $keydataMaster => $valuedataMaster) {
				if($dataTrx)
				{
					
					foreach ($dataTrx as $keydataTrx => $valuedataTrx) {

						
						if($valuedataTrx->depart_date == $valuedataMaster->depart_date &&
						  $valuedataTrx->depart_time == $valuedataMaster->depart_time &&
						  $valuedataTrx->ship_class == $valuedataMaster->ship_class &&
						  $valuedataTrx->port_id == $valuedataMaster->port_id &&
						  $valuedataTrx->vehicle_class_id == $valuedataMaster->vehicle_class_id 
						)
						{
							$getData[]=" ('{$valuedataTrx->depart_date}', 
										'{$valuedataTrx->depart_time}',
										'{$valuedataTrx->ship_class}',
										'{$valuedataTrx->port_id}',
										{$valuedataTrx->vehicle_class_id}, 
										'{$valuedataTrx->quota}',
										'{$valuedataTrx->used_quota}',
										'{$valuedataTrx->total_quota}',
										'{$valuedataTrx->status}',
										'{$valuedataTrx->total_lm}',
										'{$valuedataTrx->restriction_quota_code}'
										
										)";							
						}
					}
				}
				else
				{
					$getData[]=" ('{$valuedataMaster->depart_date}',
					'{$valuedataMaster->depart_time}',
					'{$valuedataMaster->ship_class}',
					'{$valuedataMaster->port_id}',
					{$valuedataMaster->vehicle_class_id}, 
					'{$valuedataMaster->quota}',
					0,
					'{$valuedataMaster->total_quota}',
					'{$valuedataMaster->status}',
					'{$valuedataMaster->total_lm}',
					'{$valuedataMaster->restriction_quota_code}'
					)";
				}
			}
		}
		else
		{
			$getData[]=" (null,
			null,
			null,
			null,
			null::integer,
			null,
			null,
			null,
			null,
			null,
			null
			)";
		}

		// exit;


		

		$where = " where p.depart_date is not null ";
		$where2 = " ";

		if(!empty($vehicleClass))
		{
			$where2 =" and p.vehicle_class_id='{$vehicleClass}' "; 
		}	$where .= $where2;

		$sqllll=" select vc.name as vehicle_class_name, p.* from (values ".implode(", ",$getData)." ) p(depart_date, depart_time, ship_class, port_id, vehicle_class_id, quota, used_quota, total_quota, status, total_lm, restriction_quota_code) 
		left join app.t_mtr_vehicle_class vc on p.vehicle_class_id = vc.id
			$where
		";

		$sql ="SELECT
			p.vehicle_class_id,
			p.port_id,
			p.ship_class,
			vc.name as vehicle_class_name,
			'{$departDate}' as depart_date,
			p.id,
			case
				when data_trx.quota is null then p.quota::text
				else data_trx.quota
			end as quota,

			CASE
			WHEN data_trx.total_lm is null THEN p.total_lm::text 
			ELSE data_trx.total_lm
			END AS total_lm, 
			
			CASE
			WHEN data_trx.total_quota is null THEN p.quota::text 
			ELSE data_trx.total_quota
			END AS total_quota, 
			
			CASE
			WHEN data_trx.used_quota is null THEN '0' 
			ELSE data_trx.used_quota
			END AS used_quota, 
			pd.depart_time,
			p.restriction_quota_code,
			p.status
		from app.t_mtr_quota_pcm_vehicle_restrictions p
		left join app.t_mtr_quota_pcm_vehicle_restrictions_detail pd on pd.restriction_quota_code = p.restriction_quota_code 
		left join(	
			SELECT vc.name as vehicle_class_name, p.* from (values ".implode(", ",$getData)." ) p(depart_date, depart_time, ship_class, port_id, vehicle_class_id, quota, used_quota, total_quota, status, total_lm, restriction_quota_code) 
			left join app.t_mtr_vehicle_class vc on p.vehicle_class_id = vc.id
			{$where}
		) data_trx on data_trx.restriction_quota_code = p.restriction_quota_code
		left join app.t_mtr_vehicle_class vc on p.vehicle_class_id = vc.id
		where (start_date <= '{$departDate}' and end_date >= '{$departDate}')
		and p.port_id ='{$portId}'
		and p.ship_class ='{$shipClass}'
		and p.status = 1
		and pd.status = 1
		and pd.depart_time ='{$departTime}' 
		$where2
		"; 

		
		// print_r($getData);
		// die($sql);
		// exit;

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
			// $id_enc=$this->enc->encode($row->id);
			$id_enc=$this->enc->encode($row->depart_date);
			$row->number = $i;

			$row->actions  =" ";

			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->depart_date.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->depart_date.'|1'));

			// $row->id =$row->id;
			$param =$row->depart_date."|".$row->ship_class."|".$row->vehicle_class_id;
			$param .="|".$row->depart_time."|".$row->port_id;
			$param .="|".$row->quota."|".$row->total_quota."|".$row->used_quota."|".$row->total_lm;
		

			$edit_url 	 = site_url($this->_module."/edit_restrict/{$this->enc->encode($param)}/{$idTable}");
     		$delete_url  = site_url($this->_module."/action_delete/{$this->enc->encode($param)}/{$idTable}");



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

	public function select_data($table, $where)
	{
		// return $this->db->query("select * from $table $where");
		return $this->dbView->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		// $this->db->insert($table, $data);
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

	public function updateData($data)
	{
		$portId=$data['portId'];
		strlen($portId)>1?$idPort=$portId:$idPort="0".$portId;
		$logType=2; // log type 2 data update  yaang berasal dari trx pcm global
		$createCode=$idPort.$logType.date('YmdHIs');

		if($data['action']==1)
		{
			$quota="aa.quota+".$data['quota'];
			$totalQuota="aa.total_quota+".$data['quota'];
			$quotaLimit="aa.quota_limit-".$data['quota'];
			$updatedOn=$data['updated_on'];
			$updatedBy=$data['updated_by'];
			$id=$data['id'];
			$action='Tambah';
		}
		else
		{
			$quota="aa.quota-".$data['quota'];
			$totalQuota="aa.total_quota-".$data['quota'];
			$quotaLimit="aa.quota_limit+".$data['quota'];
			$updatedOn=$data['updated_on'];
			$updatedBy=$data['updated_by'];
			$id=$data['id'];
			$action='Kurang';
		}

		$this->dbAction->query( "
				with updt as (
					update  app.t_trx_quota_pcm_vehicle aa set
					quota=".$quota.", 
					total_quota=".$totalQuota.",
					quota_limit=".$quotaLimit.",
					updated_on='".$updatedOn."',
					updated_by='".$updatedBy."',
					total_lm='".$data['lineMeter']."'

					from  app.t_trx_quota_pcm_vehicle ab
					where aa.id=".$id." and ab.id=".$id."
					returning	ab.port_id,
							ab.ship_class,
							ab.depart_date,
							ab.depart_time,
							aa.quota as quota,
							ab.total_quota as remaining_quota,
							aa.total_quota,
							'".$action."' as action_type,
							1 as status,
							aa.total_lm as lm,
							ab.total_lm as remaining_lm
				)
				insert into app.t_log_update_quota_pcm_vehicle (port_id,
							 ship_class,
							 depart_date,
							 depart_time,
							 quota,
							 remaining_quota,
							 total_quota,
							 action_type,
								status,
							created_on,
							created_by,
							log_type,
							transaction_type,
							transaction_code,
							lm,
							remaining_lm
							) 

				select port_id,
							ship_class,
							depart_date,
							depart_time,
							quota,
							remaining_quota,
							total_quota,
							action_type,
							status,
							'".$updatedOn."',
							'".$updatedBy."',
							2 as log_type,
							'Update' as transaction_type,
							'".$createCode."' as transaction_code,
							lm,
							remaining_lm  					
				from updt			

		 ");
	}

	public function action_edit_import_excel($data,$action,$portId)
	{
		$idData=array();
		strlen($portId)>1?$idPort=$portId:$idPort="0".$portId;
		$logType=2; // log type 1 data update  yaang berasal dari trx pcm global
		$createCode=$idPort.$logType.date('YmdHIs');

		if($action==2) // data yang di kurang
		{
			foreach ($data as $value) {
				$check=$this->select_data("app.t_trx_quota_pcm_vehicle", " where id=".$value['id'])->row();
				
				if(($check->total_quota-$value['quotaInput'])>=0)
				{
					$this->dbAction->query("
						with updt as (
						update app.t_trx_quota_pcm_vehicle a 
							set quota=a.quota-".$value['quotaInput'].",
							total_quota=a.total_quota-".$value['quotaInput'].",
							quota_limit=a.quota_limit+".$value['quotaInput'].",
							total_lm=".$value['lineMeter'].",
							updated_on='".$value['updatedOn']."',
							updated_by='".$value['updatedBy']."' 
						from app.t_trx_quota_pcm_vehicle b 
						where a.id=".$value['id']." and b.id=".$value['id']."
						returning 
							a.port_id,
							a.ship_class,
							a.depart_date,
							a.depart_time,
							b.total_quota as remaining_quota,
							a.total_quota,
							a.total_lm as lm,
							b.total_lm as remaining_lm,
							a.quota 										
						)
						insert into app.t_log_update_quota_pcm_vehicle 
							(port_id,
							ship_class,
							depart_date,
							depart_time,
							quota,
							remaining_quota,
							total_quota,
							action_type,
							status,
							created_on,
							created_by,
							log_type,
							transaction_type,
							transaction_code,
							lm,
							remaining_lm
						) 								
						select
							port_id,
							ship_class,
							depart_date,
							depart_time,
							quota,
							remaining_quota,
							total_quota,
							'Kurang' as action_type,
							1 as status,
							'".$value['updatedOn']."' as created_on,
							'".$value['updatedBy']."' as created_by,
							'2' as log_type,
							'Update_excel' as transaction_type,
							'".$createCode."' as transaction_code,
							lm,
							remaining_lm  
							from updt "
					);
				}
				else
				{
					$idData[]=$value['id']; // tampung data yang tidak ke update
				}

				// $idData[]=$value['id'];
			}			
		}
		else // data yang di Tambah
		{
			foreach ($data as  $value) {

					$this->dbAction->query("
						with updt as (
						update app.t_trx_quota_pcm_vehicle a 
							set quota=a.quota+".$value['quotaInput'].",
							total_quota=a.total_quota+".$value['quotaInput'].",
							quota_limit=a.quota_limit-".$value['quotaInput'].",
							total_lm=".$value['lineMeter'].",
							updated_on='".$value['updatedOn']."',
							updated_by='".$value['updatedBy']."' 
						from app.t_trx_quota_pcm_vehicle b 
						where a.id=".$value['id']." and b.id=".$value['id']."
						returning 
							a.port_id,
							a.ship_class,
							a.depart_date,
							a.depart_time,
							b.total_quota as remaining_quota,
							a.total_quota,
							a.total_lm as lm,
							b.total_lm as remaining_lm,
							a.quota 										
					)
					insert into app.t_log_update_quota_pcm_vehicle 
						(port_id,
						ship_class,
						depart_date,
						depart_time,
						quota,
						remaining_quota,
						total_quota,
						action_type,
						status,
						created_on,
						created_by,
						log_type,
						transaction_type,
						transaction_code,
						lm,
						remaining_lm
					) 								
					select
						port_id,
						ship_class,
						depart_date,
						depart_time,
						quota,
						remaining_quota,
						total_quota,
						'Tambah' as action_type,
						1 as status,
						'".$value['updatedOn']."' as created_on,
						'".$value['updatedBy']."' as created_by,
						'2' as log_type,
						'Update_excel' as transaction_type,
						'".$createCode."' as transaction_code,
						lm,
						remaining_lm  
						from updt "
					);
			}
		}

		$this->session->set_userdata('notUpdated', $idData);
		return $idData;
		

	}
	public function download(){
		
		$dateTo=$this->input->get('dateTo');
		$dateFrom=$this->input->get('dateFrom');
		$port= $this->enc->decode($this->input->get('port'));
		$time= $this->enc->decode($this->input->get('time'));
		$shipClass= $this->enc->decode($this->input->get('shipClass'));
		$iLike        = trim($this->input->get('search'));

	
		$where = " WHERE a.status not in (-5) and (a.depart_date between '{$dateFrom}' and '{$dateTo}')";

		if(!empty($port))
		{
			$where .="and ( a.port_id='{$port}' )";	
		}

		if(!empty($shipClass))
		{
			$where .="and ( a.ship_class='{$shipClass}' )";	
		}

		if(!empty($time))
		{
			$where .="and ( a.depart_time='{$time}' )";	
		}				

		if(!empty($search['value']))
		{
			$where .="and ( b.name ilike '%".$iLike."%'
							or c.name ilike '%".$iLike."%'
						)";
		}

		$where .=" order by a.port_id asc, a.depart_date asc, a.depart_time asc ";

		$sql 		   = $this->qry($where);

		$query     = $this->db->query($sql);


	    $data;
	    foreach ($query->result() as $key => $row ) {

		    $param =array(
					'portId'=>$row->port_id,
					'departTime'=>$row->depart_time,
					'departDate'=>$row->depart_date,
					'shipClass'=>$row->ship_class );

     		$getLm=$this->getLmStatus($param);

	     	$row->lmTersedia=str_replace(".",",",$getLm->ketersediaan);
		    $row->lmDigunakan=str_replace(".",",",$getLm->sudahdigunain);

		    $data[]=$row;

	    	
	    }

		return $data;

	}


	function qry($param)
	{
		$qry="SELECT 
				c.name as ship_class_name,
				(
					select qr.id from app.t_mtr_quota_pcm_vehicle_restrictions qr
					join app.t_mtr_quota_pcm_vehicle_restrictions_detail dt on qr.restriction_quota_code=dt.restriction_quota_code
					where qr.port_id = a.port_id
					and qr.ship_class = a.ship_class
					and (qr.start_date<=a.depart_date and qr.end_date >=a.depart_date)
					and dt.depart_time = a.depart_time 
					and dt.status=1
					and qr.status=1
					limit 1
				) as id_quota_restriction,
				b.name as port_name,
				a.* 
			from  app.t_trx_quota_pcm_vehicle a
				left join app.t_mtr_port b on a.port_id=b.id and b.status=1
				left join app.t_mtr_ship_class c on a.ship_class=c.id and c.status=1
				{$param}
			";

		// die($qry); exit;
		return $qry;
	}

	function getDataPcmGlobal($where)
	{
		$qry="SELECT 
				c.name as ship_class_name,
				b.name as port_name,
				a.depart_date,
				a.depart_time,
				a.quota,
				a.used_quota,
				a.quota_reserved,
				a.total_lm,
				a.port_id ,
				a.ship_class ,
				a.id
				from  app.t_trx_quota_pcm_vehicle a
				left join app.t_mtr_port b on a.port_id=b.id and b.status=1
				left join app.t_mtr_ship_class c on a.ship_class=c.id and c.status=1
				{$where}
			";

		return $this->dbView->query($qry)->result();
	}	

	public function getLmStatus($data)
	{
		$shipClass=$data['shipClass'];
		$departDate=$data['departDate'];
		$departTime=$data['departTime'];
		$portId=$data['portId'];

		return $this->dbView->query("

			SELECT
		    q.depart_date,
		    q.depart_time,
		    q.total_lm,q,ship_class,
		    EXTRACT ( HOUR FROM q.depart_time ) AS hours,
		    (
				COALESCE ( used_quota.used_lm, 0 ) - COALESCE ( expired.used_lm, 0 )) AS sudahdigunain,
				q.total_lm - ( COALESCE ( used_quota.used_lm, 0 ) - COALESCE ( expired.used_lm, 0 )) AS ketersediaan
		  FROM
		    app.t_trx_quota_pcm_vehicle q
		    LEFT JOIN 
			(
				SELECT
				  book.depart_date,
				  EXTRACT ( HOUR FROM pass.depart_time_start ) AS depart_time,
				  COALESCE ( COUNT ( book.ID ), 0 ) AS total,
				  COALESCE ( SUM ( vclass.total_lm ), 0 ) AS used_lm,
				  book.origin 
				FROM
				  app.t_trx_booking book
				  JOIN app.t_trx_booking_vehicle pass ON pass.booking_code = book.booking_code
				  JOIN app.t_mtr_vehicle_class vclass ON vclass.ID = pass.vehicle_class_id 
				WHERE
				  book.depart_date = '".$departDate."' 
				  and book.origin = ".$portId."
				  AND book.status IN ( 0, 1, 2 ) 
				  AND book.ship_class = ".$shipClass." 
				GROUP BY
				  book.depart_date,
				  book.origin,
				  EXTRACT 
					( HOUR FROM pass.depart_time_start ) 
		    ) AS used_quota ON EXTRACT ( HOUR FROM q.depart_time ) = used_quota.depart_time 
		    AND q.depart_date = used_quota.depart_date
		    LEFT JOIN (
		    SELECT
		      book.depart_date,
		      EXTRACT ( HOUR FROM pass.depart_time_start ) AS depart_time,
		      COALESCE ( COUNT ( book.ID ), 0 ) AS total,
		      COALESCE ( SUM ( vclass.total_lm ), 0 ) AS used_lm,
		      book.origin 
		    FROM
		      app.t_trx_booking book
		      JOIN app.t_trx_invoice inv ON inv.trans_number = book.trans_number
		      JOIN app.t_trx_booking_vehicle pass ON pass.booking_code = book.booking_code
		      JOIN app.t_mtr_vehicle_class vclass ON vclass.ID = pass.vehicle_class_id 
		    WHERE
		      inv.status NOT IN ( 2,7 ) 
		      AND inv.due_date :: TIMESTAMP < now() 
		      AND book.depart_date = '".$departDate."' 
		      and book.origin = ".$portId."
		      AND book.ship_class = ".$shipClass." 
		    GROUP BY
		      book.depart_date,
		      book.origin,
		      EXTRACT ( HOUR FROM pass.depart_time_start ) 
		    ) AS expired ON EXTRACT ( HOUR FROM q.depart_time ) = expired.depart_time 
		    AND q.depart_date = expired.depart_date 
		  WHERE
		    q.depart_date = '".$departDate."' 
			and q.depart_time = '".$departTime."' 
		    AND q.ship_class = ".$shipClass."
		    and q.port_id = ".$portId."
		    and q.status = 1"

		)->row();
	}	
	public function getDataTrxRestrict($startDate, $endDate, $port="",$shipClass="",$vehicleClass="",$jam="")
    {
		$where ="";
		$where2 ="";
		if(!empty($id))
		{
			$where .=" and r.id <> '".$id."'";
		}

		if(!empty($vehicleClass))
		{
			$where .="and r.vehicle_class_id =$vehicleClass";
		}
		if(!empty($jam))
		{
			$where2 .="and dr.depart_time ='$jam'";
		}
		if(!empty($shipClass))
		{
			$where .=" and r.ship_class='$shipClass' ";
		}
		if(!empty($port))
		{
			$where .=" and r.port_id='$port' ";
		}

        $qry="
            select vc.name as vehicle_class_name, dr.total_quota, dr.used_quota, dr.depart_date, dr.depart_time, r.*
			from app.t_mtr_quota_pcm_vehicle_restrictions r
			left join app.t_trx_quota_pcm_vehicle_restrictions dr on r.restriction_quota_code=dr.restriction_quota_code
			left join app.t_mtr_vehicle_class vc on r.vehicle_class_id = vc.id
            where 
			(
				(r.start_date between '{$startDate}' and '{$endDate}')
				or
				(r.end_date between '{$startDate}' and '{$endDate}')
				or
				(r.start_date<='{$startDate}' and r.end_date >='{$endDate}')
			)
			and r.status = 1
			{$where}
			{$where2}
			order by vehicle_class_id, depart_date,depart_time asc
        ";

		// die($qry); exit;
        $data = $this->dbView->query($qry)->result();

        $qryMasterRestict="
            select vc.name as vehicle_class_name, dr.depart_time, r.start_date, r.end_date, r.vehicle_class_id, r.port_id, r.ship_class, r.quota, r.total_lm
			from app.t_mtr_quota_pcm_vehicle_restrictions r
            join app.t_mtr_quota_pcm_vehicle_restrictions_detail dr on r.restriction_quota_code=dr.restriction_quota_code
			left join app.t_mtr_vehicle_class vc on r.vehicle_class_id = vc.id
			where
			(
				(r.start_date between '{$startDate}' and '{$endDate}')
				or
				(r.end_date between '{$startDate}' and '{$endDate}')
				or
				(r.start_date<='{$startDate}' and r.end_date >='{$endDate}')
			)
			and r.status = 1
			{$where}
			order by vehicle_class_id asc
        ";
				// die($qryMasterRestict); exit;
		$newDataTrx=array();		
        $dataMasterRestict = $this->dbView->query($qryMasterRestict)->result();		
		$totalDays =$this->countDate($startDate,$endDate); 
		$newDate=$startDate;

		// mencari tanggal yang masu dalam range data tersebut
		$return=array();
		
		for($i=0; $i<=$totalDays; $i++)
		{			
			if($dataMasterRestict)
			{
				foreach ($dataMasterRestict as $keyMasterRestict => $valueMasterRestict) 
				{
					if($valueMasterRestict->start_date <= $newDate && 
					$valueMasterRestict->end_date >= $newDate )
					{
						$array=array(
							"depart_time"=>$valueMasterRestict->depart_time,
							"depart_date"=>$newDate,
							"vehicle_class_id"=>$valueMasterRestict->vehicle_class_id,
							"ship_class"=>$valueMasterRestict->ship_class,
							"port_id"=>$valueMasterRestict->port_id,
							"quota"=>$valueMasterRestict->quota,
							"total_quota"=>$valueMasterRestict->quota,
							"used_quota"=>$valueMasterRestict->quota,
							"total_lm"=>"",
							"vehicle_class_name"=>$valueMasterRestict->vehicle_class_name,
						);
						
						$newDataTrx[]=$array;
						$return[]=$array;


					}	
					
				}
			}

			$newDate=date('Y-m-d', strtotime($startDate. " + {$i} days"));
		}
			// echo $startDate." ".$endDate;
			// print_r($newDataTrx);
			// print_r($data);
			// print_r($tes);
			// print_r($return);
			// exit;
		if(!empty($newDataTrx) && !empty($data))
		{
			foreach ($data as $keyData => $valueData) {
				foreach ($newDataTrx as $keyMasterRestict => $valueMasterRestict) 
				{

					if($valueMasterRestict['depart_date'] == $valueData->depart_date && 
							$valueMasterRestict['depart_time'] == $valueData->depart_time &&
							$valueMasterRestict['port_id'] == $valueData->port_id &&
							$valueMasterRestict['ship_class'] == $valueData->ship_class &&
							$valueMasterRestict['vehicle_class_id'] == $valueData->vehicle_class_id 
					)
					{
						
						$return[]=array(
							"depart_time"=>$valueData->depart_time,
							"depart_date"=>$valueData->depart_date,
							"vehicle_class_id"=>$valueData->vehicle_class_id,
							"ship_class"=>$valueData->ship_class,
							"port_id"=>$valueData->port_id,
							"quota"=>$valueData->quota,
							"total_quota"=>$valueData->total_quota,
							"used_quota"=>$valueData->used_quota,
							"total_lm"=>$valueData->total_lm,
							"vehicle_class_name"=>$valueData->vehicle_class_name,
						);
					}
				}
			}
		}
		else
		{
			$return []=array(
				"depart_time"=>"",
				"depart_date"=>"",
				"vehicle_class_id"=>"",
				"ship_class"=>"",
				"port_id"=>"",
				"quota"=>"",
				"total_quota"=>"",
				"used_quota"=>"",
				"total_lm"=>"",
				"vehicle_class_name"=>"",
			);
		}
		
		// print_r($return); exit;
		return $return;				
    }

	function countDate($dateFrom,$dateTo)
	{
		$startTimeStamp = strtotime($dateFrom);
		$endTimeStamp = strtotime($dateTo);

		$timeDiff = abs($endTimeStamp - $startTimeStamp);

		$numberDays = $timeDiff/86400;  // 86400 seconds in one day

		// and you might want to convert to integer
		return intval($numberDays);
	}

	function checkMasterRestrict($departDate, $departTime, $portId, $shipClass, $vehicleClass )
	{
		$qry ="SELECT
				a.port_id ,
				a.restriction_quota_code,
				a.ship_class ,
				a.vehicle_class_id,
				b.depart_time ,
				a.quota,
				a.total_lm
			from 
			app.t_mtr_quota_pcm_vehicle_restrictions a
			left join app.t_mtr_quota_pcm_vehicle_restrictions_detail b on a.restriction_quota_code = b.restriction_quota_code
			where a.start_date <= '$departDate' and a.end_date >= '$departDate'
			and b.depart_time ='$departTime'
			and a.ship_class ='$shipClass'
			and a.vehicle_class_id ='$vehicleClass'
			and a.port_id ='$portId'
			and a.status = 1 and b.status = 1
		
		";

		return $this->dbView->query($qry)->row();
	}

	
}
