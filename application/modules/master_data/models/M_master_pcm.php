<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : M_master_pcm 
 * -----------------------
 *
 * @author     adat <adatdt.@gmail.com>
 * @copyright  2020
 *
 */

class M_master_pcm extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/master_pcm';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateTo = $this->input->post('dateTo');
		$dateFrom = $this->input->post('dateFrom');
		$port= $this->enc->decode($this->input->post('port'));
		$shipClass= $this->enc->decode($this->input->post('shipClass'));
		$time= $this->enc->decode($this->input->post('time'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));


		$field = array(
			0 =>'depart_date',
			1 =>'port_name',
			2 =>'ship_class',
			3 =>'quota',
			4 =>'depart_date',
			5 =>'depart_time',
			6 =>'total_lm',
			7 =>'status'

		);

		$order_column = $field[$order_column];

		// $where = " 
		// 			WHERE a.status not in (-5) 
		// 			and ( concat(a.depart_date,' ',a.depart_time) between '{$dateFrom}' and '{$dateTo}')
		// 		";

		if(empty($dateFrom) and empty($dateTo))
		{
			$departDate= "  ";
		}
		else if (!empty($dateFrom) and empty($dateTo))
		{
			$departDate="  and (a.depart_date between ".$this->db->escape($dateFrom)." and ". $this->db->escape($dateFrom).") ";
		}
		else if(empty($dateFrom) and !empty($dateTo))
		{
			$departDate="  and (a.depart_date between ".$this->db->escape($dateTo)." and ".$this->db->escape($dateTo).") ";	
		}
		else
		{
			$departDate=" and ( a.depart_date between ".$this->db->escape($dateFrom)." and ".$this->db->escape($dateTo).") ";
		}

		$where = " WHERE a.status not in (-5) ".$departDate;

		if(!empty($port))
		{
			$where .=" and (a.port_id='{$port}') ";
		}

		if(!empty($shipClass))
		{
			$where .=" and (a.ship_class='{$shipClass}') ";
		}		

		if(!empty($time))
		{
			$where .=" and (a.depart_time='{$time}') ";
		}				

		if(!empty($search['value']))
		{
			$where .="and ( b.name ilike '%".$iLike."%' ESCAPE '!'
							or c.name ilike '%".$iLike."%' ESCAPE '!'
							)";
		}

		$sql 		   = "
							select
							 c.name as ship_class_name,
							  b.name as port_name,
							   a.*
							    from app.t_mtr_quota_pcm_vehicle a
							left join app.t_mtr_port b on a.port_id=b.id and b.status=1
							left join app.t_mtr_ship_class c on a.ship_class=c.id and c.status=1
							{$where}
						 ";

		$query         = $this->dbView->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->dbView->query($sql);
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

			if($row->status == 1){

				if($row->depart_date." ".$row->depart_time>=date('Y-m-d H').":00" )
				{

					$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				}
				$row->status   = success_label('Aktif');
				// $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				// $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

     		$row->depart_date=empty($row->depart_date)?"":format_date($row->depart_date);			

     		$row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

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

	public function qryDataTrx($where)
	{
		$data=$this->dbView->query("
				select
				 c.name as ship_class_name,
				  b.name as port_name,
				   a.*
				    from app.t_trx_quota_pcm_vehicle a
				left join app.t_mtr_port b on a.port_id=b.id and b.status=1
				left join app.t_mtr_ship_class c on a.ship_class=c.id and c.status=1
				{$where}
			");
		return $data;
	}

	public function editData($param)
	{

		$action=$param['action'];
        $quota=$param['quota'];
        $idMaster=$param['idMaster'];
        $time=$param['time'];
        $totalLm=$param['lineMeter'];
        $updatedBy=$param['updatedBy'];
        $updatedOn=$param['updatedOn']; 		
	
		$getMaster=$this->select_data("app.t_mtr_quota_pcm_vehicle", " where id=".$idMaster)->row();
		$portId=$getMaster->port_id;
		$shipClass=$getMaster->ship_class;

		$concatDate=$getMaster->depart_date." ".$getMaster->depart_time;
		      	// checking range date
      	$rangeDate=$this->select_data("app.t_mtr_quota_pcm_vehicle", " where
					case
						when depart_date is null then
							depart_date>'{$getMaster->depart_date}'
						else
							concat(depart_date,' ',depart_time)::timestamp without time zone>'{$concatDate}'
						end
						and status=1 and port_id={$portId} and ship_class={$shipClass}
				order by concat(depart_date,' ',depart_time) asc
				limit 1 ");

      	if($rangeDate->num_rows()>0)
      	{
      		$where=" where port_id={$portId} and (concat(depart_date,' ', depart_time)::timestamp with time zone >='{$concatDate}' and concat(depart_date,' ', depart_time)::timestamp with time zone <'".$rangeDate->row()->depart_date." ".$rangeDate->row()->depart_time."' )and ship_class={$shipClass} ";

      		$maxDate=$rangeDate->row()->depart_date." ".$rangeDate->row()->depart_time;
      		$getMaxDate=date('Y-m-d H:i', strtotime($maxDate.'-1 hour'));
      		$rangeDate="Tanggal Berlaku ".format_date($concatDate)." ".format_time($concatDate)." s/d ".format_date($getMaxDate)." ".format_time($getMaxDate);
      	}
      	else
      	{
      		$where=" where port_id={$portId} and concat(depart_date,' ', depart_time)::timestamp with time zone >='{$concatDate}' and ship_class={$shipClass} ";

      		$rangeDate="Tanggal Berlaku ".$concatDate." Hingga seterusnya";
      	}

		$getDataUpdate=$this->select_data("app.t_trx_quota_pcm_vehicle", $where )->result();
		
		$idData=array();
		strlen($portId)>1?$idPort=$portId:$idPort="0".$portId;
		$logType=1; // log type 1 data update  yaang berasal dari master pcm
		$createCode=$idPort.$logType.date('YmdHIs');
		if($getDataUpdate)
		{
									
			// jika aksinya min
			if($action==2)
			{
				foreach ($getDataUpdate as $key => $value) {
					if((($value->total_quota)-$quota)>=0)
					{
						// insert data di log dan update data di trx pcm
						$this->dbAction->query("

							with updt as (
								update app.t_trx_quota_pcm_vehicle a 
									set quota=a.quota-{$quota},
									total_quota=a.total_quota-{$quota},
									quota_limit=a.quota_limit+{$quota},
									total_lm={$totalLm},
									updated_on='{$updatedOn}',
									updated_by='{$updatedBy}' 
								from app.t_trx_quota_pcm_vehicle b 
								where a.id={$value->id} and b.id={$value->id}
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
								'{$updatedOn}' as created_on,
								'{$updatedBy}' as created_by,
								'1' as log_type,
								'Update' as transaction_type,
								'{$createCode}' as transaction_code,
								lm,
								remaining_lm  
								from updt"
						);

					}
					else
					{
						// tampung data yang tidak bisa ke update
						$idData[]=$value->id;
					}						
				}
					
				// update ke master pcm 						
				$this->dbAction->query("
							with updt as (	
								update app.t_mtr_quota_pcm_vehicle a set
								quota =a.quota-".$quota.",
								total_lm='".$totalLm."',
								updated_by='".$updatedBy."',
								updated_on='".$updatedOn."'
								from app.t_mtr_quota_pcm_vehicle b
								where a.id={$idMaster} and b.id={$idMaster}
								returning 
									a.port_id,
									a.ship_class,
									a.quota,
									b.quota as remaining_quota,
									a.quota as total_quota,
									a.total_lm as lm,
									b.total_lm as remaining_lm
							)
							insert into app.t_log_update_mtr_quota_pcm_vehicle 
							(port_id,
							ship_class,
							quota,
							remaining_quota,
							total_quota, action_type,
							log_type,
							status,
							created_by,
							created_on,
							transaction_type,
							transaction_code,
							lm,
							remaining_lm
							 )

						select  port_id,
								ship_class,
								quota,
								remaining_quota,
								total_quota,
								'Kurang' as action_type,
								1 as log_type, 
								1 as status,
								'".$updatedBy."' as created_by,
								'".$updatedOn."' as created_on,
								'Update' as transaction_type,
								'".$createCode."' as transaction_code,
								lm,
								remaining_lm 
						from updt 
				");									
			}
			else // jika dia nambah data
			{
				foreach ($getDataUpdate as $key => $value) 
				{

					// insert data di log dan update data di trx pcm
						$this->dbAction->query("

							with updt as (
								update app.t_trx_quota_pcm_vehicle a 
									set quota=a.quota+{$quota},
									total_quota=a.total_quota+{$quota},
									quota_limit=a.quota_limit-{$quota},
									total_lm={$totalLm},
									updated_on='{$updatedOn}',
									updated_by='{$updatedBy}' 
								from app.t_trx_quota_pcm_vehicle b 
								where a.id={$value->id} and b.id={$value->id}
								returning 
									a.port_id,
									a.quota,
									a.ship_class,
									a.depart_date,
									a.depart_time,
									b.total_quota as remaining_quota,
									a.total_quota,
									a.total_lm as lm,
									b.total_lm as remaining_lm 										
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
								'{$updatedOn}' as created_on,
								'{$updatedBy}' as created_by,
								'1' as log_type,
								'Update' as transaction_type,
								'{$createCode}' as transaction_code,
								lm,
								remaining_lm  
								from updt"
						);					
				}
					$this->dbAction->query("
								with updt as (	
									update app.t_mtr_quota_pcm_vehicle a set
									quota =a.quota+".$quota.",
									total_lm='".$totalLm."',
									updated_by='".$updatedBy."',
									updated_on='".$updatedOn."'
									from app.t_mtr_quota_pcm_vehicle b
									where a.id={$idMaster} and b.id={$idMaster}
									returning 
										a.port_id,
										a.ship_class,
										a.quota,
										b.quota as remaining_quota,
										a.quota as total_quota,
										a.total_lm as lm,
										b.total_lm as remaining_lm
								)
								insert into app.t_log_update_mtr_quota_pcm_vehicle 
								(port_id,
								ship_class,
								quota,
								remaining_quota,
								total_quota, action_type,
								log_type,
								status,
								created_by,
								created_on,
								transaction_type,
								transaction_code,
								lm,
								remaining_lm
								 )

							select  port_id,
									ship_class,
									quota,
									remaining_quota,
									total_quota,
									'Tambah' as action_type,
									1 as log_type, 
									1 as status,
									'".$updatedBy."' as created_by,
									'".$updatedOn."' as created_on,
									'Update' as transaction_type,
									'".$createCode."' as transaction_code,
									lm,
									remaining_lm 
							from updt" );						
			}			
		}

		// created session untuk menyimpan data yang kosong
		$this->session->set_userdata('notUpdated', $idData);
		$this->session->set_userdata('rangeDatePcm', $rangeDate);
		return $idData;		      	

	}

	public function actionAddData($data)
	{
		$portId=$data['port_id'];
        $shipClass=$data['ship_class'];
        $quota=$data['quota'];
        $totalLm=$data['total_lm'];
        $departTime=$data['depart_time'];
        $departDate=$data['depart_date'];
        $status=$data['status'];
        $updatedBy=$data['created_by'];
        $updatedOn=$data['created_on'];

        $concatDate=$departDate." ".$departTime;

      	// checking range date
      	$rangeDate=$this->select_data("app.t_mtr_quota_pcm_vehicle", " where
					case
						when depart_date is null then
							depart_date>'{$departDate}'
						else
							concat(depart_date,' ',depart_time)::timestamp without time zone>'{$concatDate}'
						end
						and status=1 and port_id={$portId} and ship_class={$shipClass}
				order by concat(depart_date,' ',depart_time) asc
				limit 1 ");


      	if($rangeDate->num_rows()>0)
      	{
      		$where=" where port_id={$portId} and (concat(depart_date,' ', depart_time)::timestamp with time zone >='{$concatDate}' and concat(depart_date,' ', depart_time)::timestamp with time zone <'".$rangeDate->row()->depart_date." ".$rangeDate->row()->depart_time."' )and ship_class={$shipClass} ";

      		$maxDate=$rangeDate->row()->depart_date." ".$rangeDate->row()->depart_time;
      		$getMaxDate=date('Y-m-d H:i', strtotime($maxDate.'-1 hour'));
      		$rangeDate="Tanggal Berlaku ".format_date($concatDate)." ".format_time($concatDate)." s/d ".format_date($getMaxDate)." ".format_time($getMaxDate);
      	}
      	else
      	{
      		$where=" where port_id={$portId} and concat(depart_date,' ', depart_time)::timestamp with time zone >='{$concatDate}' and ship_class={$shipClass} ";

      		$rangeDate="Tanggal Berlaku ".$concatDate." Hingga seterusnya";
      	}
		

		$getDataUpdate=$this->select_data("app.t_trx_quota_pcm_vehicle", $where )->result();
		
		$idData=array();
		strlen($portId)>1?$idPort=$portId:$idPort="0".$portId;
		$logType=1; // log type 1 data update  yaang berasal dari master pcm
		$createCode=$idPort.$logType.date('YmdHIs');

		if($getDataUpdate)
		{				
			foreach ($getDataUpdate as $key => $value) {
					
				// jika menambahkan kuota
				if($value->quota<=$quota)
				{
					$selisihQuota=$quota-$value->quota;
					// insert data di log dan update data di trx pcm (tambah value)
					$this->dbAction->query("

										with updt as (
											update app.t_trx_quota_pcm_vehicle a 
												set quota=a.quota+{$selisihQuota},
												total_quota=a.total_quota+{$selisihQuota},
												quota_limit=a.quota_limit-{$selisihQuota},
												total_lm={$totalLm},
												updated_on='{$updatedOn}',
												updated_by='{$updatedBy}' 
											from app.t_trx_quota_pcm_vehicle b 
											where a.id={$value->id} and b.id={$value->id}
											returning 
												a.port_id,
												a.quota,
												a.ship_class,
												a.depart_date,
												a.depart_time,
												b.total_quota as remaining_quota,
												a.total_quota,
												a.total_lm as lm,
												b.total_lm as remaining_lm 										
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
											'{$updatedOn}' as created_on,
											'{$updatedBy}' as created_by,
											'1' as log_type,
											'Add' as transaction_type,
											'{$createCode}' as transaction_code,
											lm,
											remaining_lm  
											from updt"
									);											

				}
				else if($value->quota>$quota)
				{
					$selisihQuota=$value->quota-$quota;

					if((($value->total_quota)-$selisihQuota)>=0) // jika estimasi nilai tidak 0 maka insert data
					{
						// insert data di log dan update data di trx pcm (kurang )
						$this->dbAction->query("

							with updt as (
								update app.t_trx_quota_pcm_vehicle a 
									set quota=a.quota-{$selisihQuota},
									total_quota=a.total_quota-{$selisihQuota},
									quota_limit=a.quota_limit+{$selisihQuota},
									total_lm={$totalLm},
									updated_on='{$updatedOn}',
									updated_by='{$updatedBy}' 
								from app.t_trx_quota_pcm_vehicle b 
								where a.id={$value->id} and b.id={$value->id}
								returning 
									a.port_id,
									a.quota,
									a.ship_class,
									a.depart_date,
									a.depart_time,
									b.total_quota as remaining_quota,
									a.total_quota,
									a.total_lm as lm,
									b.total_lm as remaining_lm										
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
								'{$updatedOn}' as created_on,
								'{$updatedBy}' as created_by,
								'1' as log_type,
								'Add' as transaction_type,
								'{$createCode}' as transaction_code,
								lm,
								remaining_lm  
								from updt"
						);							

					}
					else
					{
						// tampung data yang tidak bisa ke update
						$idData[]=$value->id;
					}
				}											
			}					
		}

		// created session untuk menyimpan data yang kosong
		$this->session->set_userdata('notUpdated', $idData);
		$this->session->set_userdata('rangeDatePcm', $rangeDate);
		return $idData;

	}

    function createCode($port,$log_type)
    {
    	strlen($port)>1?$idPort=$port:$idPort="0".$port;
        $front_code=$idPort."".date('ymd');

        $total_length=strlen($front_code);

        $chekCode=$this->db->query("select * from app.t_mtr_schedule where left(schedule_code,".$total_length.")='".$front_code."' ")->num_rows();

        if($chekCode<1)
        {
            $shelterCode=$front_code."0001";
            return $shelterCode;
        }
        else
        {
            $max=$this->db->query("select max (schedule_code) as max_code from app.t_mtr_schedule where left(schedule_code,".$total_length.")='".$front_code."' ")->row();
            $kode=$max->max_code;
            $noUrut = (int) substr($kode, $total_length, 4);
            $noUrut++;
            $char = $front_code;
            $kode = $char . sprintf("%04s", $noUrut);
            return $kode;
        }
    }	

	public function select_data($table, $where)
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
