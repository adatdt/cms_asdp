<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**

 * @author     adat <adatdt.@gmail.com>
 * @copyright  2020
 *
 */

class M_pcm_trx extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'pelabuhan/pcm_trx';
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
		$vehicleClass= $this->enc->decode($this->input->post('vehicleClass'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));

		
		$field = array(
					0=>"id",
                    1=>"port_name",
                    2=>"vehicle_class_name",
                    3=>"ship_class_name",
                    4=>"depart_date",
                    5=>"depart_time",
                    6=>"quota",
                    7=>"total_quota",
                    8=>"used_quota",
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

		if(!empty($vehicleClass))
		{
			$where .="and ( a.vehicle_class_id='{$vehicleClass}' )";	
		}				

		if(!empty($time))
		{
			$where .="and ( a.depart_time='{$time}' )";	
		}				

		if(!empty($search['value']))
		{
			$where .="and ( b.name ilike '%".$iLike."%'
							or c.name ilike '%".$iLike."%'
							or d.name ilike '%".$iLike."%'
						)";
		}

		$sql 		   = "
							SELECT c.name as ship_class_name,
							b.name as port_name,
							d.name as vehicle_class_name,
							a.* from  app.t_trx_quota_pcm_vehicle_reserved a
							left join app.t_mtr_port b on a.port_id=b.id and b.status=1
							left join app.t_mtr_ship_class c on a.ship_class=c.id and c.status=1
							left join app.t_mtr_vehicle_class d on a.vehicle_class_id=d.id and d.status=1
							{$where}
						 ";

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

     		 if($row->depart_date." ".$row->depart_time>=date('Y-m-d H').":00" )
			{

				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
			}

			if($row->status == 1){
				// $row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				// $row->status   = success_label('Aktif');
				// $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				// $row->status   = failed_label('Tidak Aktif');
				// $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

     		$row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);


     		$row->depart_date=empty($row->depart_date)?"":format_date($row->depart_date);

			$rows[] = $row;
			unset($row->assignment_code);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows,
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
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
	public function updateInsert($data)
	{
		$portId=$data['portId'];
		strlen($portId)>1?$idPort=$portId:$idPort="0".$portId;
		$logType=3; // log type 3 data update  yaang berasal dari trx pcm khusus reserved
		$createCode=$idPort.$logType.date('YmdHIs');

		if ($data['type']=='a')
		{
			$totalQuota=0;
			$aliasTotalQuota="ab.total_quota";
			$quotaLimit=" a.quota_limit+ab.total_quota ";
			$trxGlobalQuota="ab.total_quota";
		}
		else
		{
			$totalQuota="a.total_quota - ".$data['quotaInput'];
			$aliasTotalQuota=$data['quotaInput']." as total_quota";
			$quotaLimit=" a.quota_limit+".$data['quotaInput'];
			$trxGlobalQuota="a.quota_reserved+".$data['quotaInput'];	
		}

		$user=$this->session->userdata("username");
		$date=date("Y-m-d H:i:s");

		   $this->dbAction->query("WITH upd AS (
                        UPDATE app.t_trx_quota_pcm_vehicle a
                           SET 
                                updated_on ='".$date."',
                                updated_by='".$user."',
                                total_quota=".$totalQuota.",
                                quota_limit=".$quotaLimit.",
                                quota_reserved=".$trxGlobalQuota."
                           from app.t_trx_quota_pcm_vehicle  ab 
                           where a.id='".$data['id']."' and ab.id='".$data['id']."'
                           RETURNING  
			   					".$aliasTotalQuota.",
			   					a.total_quota as total_quota2 ,
			   					ab.total_quota as remaining_quota ,
			   					ab.depart_date,
			   					ab.depart_time,
			   					a.port_id,
			   					a.ship_class,
			   					a.total_lm as lm,
			   					ab.total_lm as remaining_lm,
			   					a.quota
                    ),
				insrt as (
				insert into app.t_log_update_quota_pcm_vehicle 
									(
										port_id,
										 ship_class,
										depart_date,
										 depart_time,
										 quota,
										 remaining_quota,
										 total_quota,
										 action_type,
										 status,
										 created_by,
										 created_on,
										 transaction_code,
										 transaction_type,
										 lm,
										 remaining_lm,
										 log_type
										)  
							select
								port_id,
								 ship_class,
								depart_date,
								 depart_time,
								 quota,
								 remaining_quota,
								 total_quota2 as total_quota,
								 'Kurang' as action_type,
								 1 as status,
				                '".$user."' as created_by,
				                '".$date."' as created_on,
				                '{$createCode}' as transaction_code,
				                'Add' as transaction_type,
				                lm,
				                remaining_lm,
				                3 as log_type
								from upd				
				
			)
            INSERT INTO app.t_trx_quota_pcm_vehicle_reserved 
            					(port_id,
                                 ship_class,
                                 vehicle_class_id,
                                 depart_date,
                                 depart_time,
                                 quota,
                                 status,
                                 created_by,
                                 created_on,
                                 total_quota,
                                 quota_limit,
                                 quota_reserved
                                 )
                 SELECT 
                 ".$data['portId']." as port_id,
                 ".$data['shipClass']." as ship_class,
                 ".$data['vehicleClassId']." as vehicle_class_id,
                depart_date,
                depart_time,
                ".$data['quotaInput']." as quota,
                1 as status,
                '".$user."' as created_by,
                '".$date."' as created_on,
                total_quota,
                (32767-total_quota) as quota_limit,
                total_quota as quota_reserved
                from  upd");   
	}

	public function updateUpdatePlus($data)
	{
		$portId=$data['portId'];
		strlen($portId)>1?$idPort=$portId:$idPort="0".$portId;
		$logType=3; // log type 3 data update  yaang berasal dari trx pcm khusus reserved
		$createCode=$idPort.$logType.date('YmdHIs');

		if ($data['type']=='a')// jika yang diinpul lebih besar dari quota global trx
		{
			$totalQuota=0;
			$quotaLimit=" a.quota_limit+a2.total_quota ";
			$quota=" , a2.total_quota";
			$trxReserved=" a.quota_reserved+a2.total_quota";

			$totalQuotaReserved="b.total_quota+(select total_quota from updt)";
			$quotaLimitReserved=" b.quota_limit-(select total_quota from updt)";
			$quotaReserved="b.quota+".$data['quotaInput'];
			$quotaReservedKhusus="b.quota_reserved+(select total_quota from updt)";
		}
		else // jika yang diinpul lebih kecil dari quota global trx
		{
			$totalQuota="a.total_quota-".$data['quotaInput'];
			$quotaLimit="a.quota_limit+".$data['quotaInput'];	
			// $quota=" , 0 as total_quota ";
			$quota=" , a.total_quota as total_quota ";
			$trxReserved=" a.quota_reserved+".$data['quotaInput'];

			$totalQuotaReserved="b.total_quota+".$data['quotaInput'];
			$quotaLimitReserved=" b.quota_limit-".$data['quotaInput'];
			$quotaReserved="b.quota+".$data['quotaInput'];
			$quotaReservedKhusus="b.quota_reserved+".$data['quotaInput'];

		}

		$this->dbAction->query("
							with updt as (
							update app.t_trx_quota_pcm_vehicle a 
							set total_quota=".$totalQuota.",
							 	quota_limit=".$quotaLimit.",
								updated_on='".$data['updated_on']."',
								updated_by='".$data['updated_by']."',
								quota_reserved=".$trxReserved."
							from  app.t_trx_quota_pcm_vehicle a2
							where a.id='".$data['id_pcm']."' and a2.id='".$data['id_pcm']."'
							returning 
							a.port_id,
							a.ship_class,
							a.depart_date,
							a.depart_time,
							a2.total_quota as remaining_quota,
							a.updated_on,
							a.quota,
							a.updated_by 
							{$quota} 
						),
						updt2 as ( 
							update app.t_trx_quota_pcm_vehicle_reserved b 
							set quota=".$quotaReserved.",
								total_quota=".$totalQuotaReserved.",
								quota_limit=".$quotaLimitReserved.",
								updated_on=(select updated_on from updt),
								updated_by=(select updated_by from updt),
								quota_reserved=".$quotaReservedKhusus."
							from app.t_trx_quota_pcm_vehicle_reserved b2 
							where b.id='".$data['id_pcm_reserved']."' and b2.id='".$data['id_pcm_reserved']."'
							returning 

								b.port_id,
								 b.ship_class,
								 b.depart_date,
								 b.depart_time,
								 b2.total_quota as remaining_quota,
								 b.total_quota,
								 b.vehicle_class_id,								
								 b.updated_by ,
								 b.updated_on,
								 b.quota

						),
						isrt as (
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
										 created_by,
										 created_on,
										 log_type,
										 transaction_code,
										 transaction_type
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
								 updated_by as created_by,
								 updated_on as created_on,
								 3 as log_type,
								 '".$createCode."' as transaction_code,
								 'Update' as transaction_type
								from updt								
						)
						
					insert into  app.t_log_update_quota_pcm_vehicle_reserved (
								port_id,
								 ship_class,
								depart_date,
								 depart_time,
								 quota,
								 remaining_quota,
								 total_quota,
								 action_type,
								 vehicle_class_id,
								 status,
								 created_by,
								 created_on,
								 log_type,
								transaction_code,
								transaction_type
								)

								 select
								port_id,
								 ship_class,
								depart_date,
								 depart_time,
								 quota,
								 remaining_quota,
								 total_quota,
								 'Tambah' as  action_type,
								 vehicle_class_id,
								 1 as status,
								 updated_by as created_by,
								 updated_on as created_on,
								 3 as log_type,
								 '".$createCode."' as transaction_code,
								 'Update' as transaction_type
								from updt2	
			");


	}

	public function updateUpdateMin($data)
	{

		$portId=$data['portId'];
		strlen($portId)>1?$idPort=$portId:$idPort="0".$portId;
		$logType=3; // log type 3 data update  yaang berasal dari trx pcm khusus reserved
		$createCode=$idPort.$logType.date('YmdHIs');

		$this->dbAction->query("
					with updt as (
							update app.t_trx_quota_pcm_vehicle a 
							set total_quota= a.total_quota+".$data['quotaInput']." ,
							 	quota_limit=a.quota_limit-".$data['quotaInput'].",
								updated_on='".$data['updated_on']."',
								updated_by='".$data['updated_by']."',
								quota_reserved=a.quota_reserved-".$data['quotaInput']."
							from  app.t_trx_quota_pcm_vehicle a2
							where a.id='".$data['id_pcm']."' and a2.id='".$data['id_pcm']."'
							returning 
							a.updated_on,
							a.updated_by,
							a.port_id,
							a.ship_class,
							a.depart_date,
							a.depart_time,
							a2.total_quota as remaining_quota,
							a.total_quota ,
							a.quota
						),
						insrt as (
								insert into app.t_log_update_quota_pcm_vehicle 
									(
										port_id,
										 ship_class,
										depart_date,
										 depart_time,
										 quota,
										 remaining_quota,
										 total_quota,
										 action_type,
										 status,
										 created_by,
										 created_on,
										 log_type,
										 transaction_code,
										transaction_type
										)  
							select
								port_id,
								 ship_class,
								depart_date,
								 depart_time,
								 quota,
								 remaining_quota,
								 total_quota,
								 'tambah' as action_type,
								 1 as status,
								 updated_by as created_by,
								 updated_on as created_on,
								 3 as log_type,
								 '".$createCode."' as transaction_code,
								 'Update' as transaction_type
								from updt						
						),
						updt2 as (
						update app.t_trx_quota_pcm_vehicle_reserved b 
						set quota=b.quota-".$data['quotaInput'].",
							total_quota=b.total_quota-".$data['quotaInput'].",
							quota_limit=b.quota_limit+".$data['quotaInput'].",
							updated_on=(select updated_on from updt),
							updated_by=(select updated_by from updt),
							quota_reserved=b.quota_reserved-".$data['quotaInput']."
							from  app.t_trx_quota_pcm_vehicle_reserved b2 
							where b.id='".$data['id_pcm_reserved']."' and b2.id='".$data['id_pcm_reserved']."'     
							
							returning
							b.port_id,
							b.ship_class,
							b.depart_date,
							b.depart_time,
							b2.total_quota as remaining_quota,
							b.total_quota,
							b.vehicle_class_id,
							b.updated_by,
							b.updated_on,
							b.quota
						)
					 	insert into  app.t_log_update_quota_pcm_vehicle_reserved (
								port_id,
								 ship_class,
								depart_date,
								 depart_time,
								 quota,
								 remaining_quota,
								 total_quota,
								 action_type,
								 vehicle_class_id,
								 status,
								 created_by,
								 created_on,
								 log_type,
								 transaction_code,
								transaction_type
								 )

								 select
								port_id,
								 ship_class,
								depart_date,
								 depart_time,
								 quota,
								 remaining_quota,
								 total_quota,
								 'Kurang' as  action_type,
								 vehicle_class_id,
								 1 as status,
								 updated_by as created_by,
								 updated_on as created_on,
								 3 as log_type,
								 '".$createCode."' as transaction_code,
								 'Update' as transaction_type
								from updt2	
		");
	}	
}
