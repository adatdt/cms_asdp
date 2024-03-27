<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PembatasanTransaksiModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module   = 'master_data2/pembatasanTransaksi';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData=$this->input->post("searchData");
		$searchName=$this->input->post("searchName");
		$dateFrom=$this->input->post("dateFrom");
		$dateTo=$this->input->post("dateTo");
		$iLike        = trim(str_replace(array("'",'"'),"",$searchData));
		
		$field = array(
			0 =>'id',
			1 =>'id',
			2=>"limit_transaction_code",
			3=>"start_date",
			4=>"end_date",
			5=>"jenis_pj",
			6=>"golongan",
			7=>"limit_type",
			8=>"value",
			9=>"custom_type",
			10=>"custom_value",
			11=>"status",	
			
		);

		$order_column = $field[$order_column];
		$where = " where a.status <> -5 ";
		if(empty($dateFrom) and empty($dateTo))
		{
			$where .= " ";
		}
		else if(!empty($dateFrom) and empty($dateTo))
		{
			$where .= " and start_date >='{$dateFrom}' ";
		}
		else if(empty($dateFrom) and !empty($dateTo))
		{
			$where .= " and end_date <= '{$dateTo}' ";
		}
		else
		{
			$where .= " and (start_date >='{$dateFrom}' and   end_date <='{$dateTo}') ";
		}		

		if(!empty($searchData))
		{
			if($searchName=="limitTransactionCode")
			{
				$where .=" and limit_transaction_code ilike '%".$iLike."%' ";
			}
		}		

		$sql 		   = "select 
							a .*,
							(
								case 
								when a.service_id = 2
								then vc.name
								else '' end
							) as golongan ,
							b.name as jenis_pj

						from app.t_mtr_limit_certain_group_transaction a 
						left join app.t_mtr_service b on a.service_id=b.id
						left join app.t_mtr_vehicle_class vc on vc.id = a.vehicle_class_id{$where}";

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
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change_active/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions="";

			$row->add_detail_vehicle="";
			if($row->status == 1)
			{
				$row->status   = success_label('Aktif');
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
				$row->add_detail_vehicle .=generate_button_new($this->_module, 'add',  site_url($this->_module."/add_detail_vehicle/".$this->enc->encode($row->id)));
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

			if(!empty($row->limit_type))
			{
				$row->limit_type= $this->masterLimitType($row->limit_type);
			}
			
			if($row->custom_type=="t")
			{				
				$row->custom_type= success_label("Iya");
			}
			else
			{
				$row->custom_type= failed_label("Tidak");
			}						

     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
			$row->start_date=format_date($row->start_date)." ".format_time($row->start_date);
			$row->end_date=format_date($row->end_date)." ".format_time($row->end_date);
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
	
	public function getDetailTransaksi(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$vaccineParamId=$this->input->post('vaccineParamId');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$limitTransactionCode=$this->input->post('limitTransactionCode');
		$iLike        = trim(str_replace(array("'",'"'),"",($search['value'])));
		$settingCustom = $this->input->post("settingCustom");

		$searchData=trim(str_replace(array("'",'"'),"",($this->input->post("searchData"))) );
		$searchName=$this->input->post("searchName");
		
		$field = array(
			0 =>'id',
		);

		$order_column = $field[$order_column];
		$where = " where b.status = 1 and  a.limit_transaction_code='".$limitTransactionCode."' ";
		if($settingCustom==1)
		{
			$where .= " and b.value <> a.value  ";
		}
		else
		{
			$where .= " and b.value = a.value  ";
		}

		if(!empty($searchData))
		{
			if($searchName=="email")
			{
				$where .=" and b.email ilike '%".$searchData."%' ";
			}
		}	

		$sql 		   = $this->qryDetailLimitTransaksi()." ".$where;

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

		// search header 
		$dataHeader= $this->select_data("app.t_mtr_limit_certain_group_transaction"," where limit_transaction_code='{$limitTransactionCode}' ")->row();

		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$exception    = site_url($this->_module."/action_change_detail_limit_member_except/".$this->enc->encode($limitTransactionCode.'|-5|'.$row->email));
			$limit       = site_url($this->_module."/action_change_detail_limit_member/".$this->enc->encode($limitTransactionCode.'|1|'.$row->email));

			$row->id =$row->id;
			$idDetailMember=$this->enc->encode($row->id_detail_member);
			$edit_url 	 = site_url($this->_module."/edit_detail_pembatasan/{$idDetailMember}");

     		$row->actions="";
			
			if(empty($row->id_detail_member))
			{
				$row->status= failed_label("Pengecualian");
				if($dataHeader->status==1)
				{

					$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="myData.confirmationAction2(\'Apakah Anda yakin akan batasi user ini ?\', \''.$limit.'\',\'detailDataTables_'.$limitTransactionCode.'\')" title="Batasi User"> <i class="fa fa-ban"></i> </button> ');
				}
			}
			else
			{
				$row->status= success_label("Pembatasan");
				if($dataHeader->status==1)
				{
					$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-warning" onclick="myData.confirmationAction2(\'Apakah Anda yakin mengecualikan user ini ?\', \''.$exception.'\',\''.$limitTransactionCode.'\')" title="User Pengecualian"> <i class="fa fa-check"></i> </button> ');
					$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				}
			}	
			
			if(!empty($row->limit_type))
			{
				$row->limit_type= $this->masterLimitType($row->limit_type);
			}			
						
			if($row->custom_type=="t")
			{
				$row->custom_type= success_label("Iya");
				
			}
			else
			{
				$row->custom_type= failed_label("Tidak");

			}			

     		$row->no=$i;
			

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

	public function getDetailTransaksiExcept(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$limitTransactionCode=$this->input->post('limitTransactionCode');

		$searchData=trim(str_replace(array("'",'"'),"",($this->input->post("searchData"))) );
		$searchName=$this->input->post("searchName");

		$iLike        = trim(str_replace(array("'",'"'),"",($search['value'])));

		$searchData=trim(str_replace(array("'",'"'),"",($this->input->post("searchData"))) );
		$searchName=$this->input->post("searchName");
		
		$field = array(
			0 =>'id',
			1 =>"email", 
			2 => "status",

		);

		$order_column = $field[$order_column];

		/*
		$where = " where tmom.status=1 and
					tmom.email not in (
						select email from app.t_mtr_limit_transaction_detail where limit_transaction_code='".$limitTransactionCode."'
						and status =1
					)
					";
		*/

		$where = " where tmom.status=1 and  tddt2.limit_transaction_code is null ";					

		if(!empty($searchData))
		{
			if($searchName=="email")
			{
				$where .=" and tmom.email ilike '%".$searchData."%' ";
			}
		}	

		$sql 		   = $this->qryDetailExcept($limitTransactionCode, $where);
		$sqlCount 		   = $this->qryDetailExceptCount($limitTransactionCode, $where);
		// die($sql); exit;
		$query         = $this->db->query($sql);
		$queryCount         = $this->db->query($sqlCount)->row();

		$records_total = $queryCount->count_data;
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i  	= ($start + 1);

		// search header 
		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$limit       = site_url($this->_module."/action_change_detail_limit_member/".$this->enc->encode($limitTransactionCode.'|1|'.$row->email));

			$row->id =$row->id;
			$idDetailMember=$this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit_detail_pembatasan/{$idDetailMember}");

     		$row->actions="";
			$row->status=failed_label("Pengecualian");
			$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="myData.confirmationAction2(\'Apakah Anda yakin membatasi user ini ?\', \''.$limit.'\',\''.$limitTransactionCode.'\')" title="Pembatasan"> <i class="fa fa-ban"></i> </button> ');
     		$row->no=$i;
			
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

	public function qryDetailLimitTransaksi()
	{
		$qry="
		select 
			a .id,
			a .limit_transaction_code,
			a .status,	
			a . limit_type,
			a . custom_type,
			a .custom_value,
			b .email ,
			b .value,
			b.id as id_detail_member,
			b.status as status_detail,
			(
				case 
				when a.service_id = 2
				then vc.name
				else 'Dewasa' end
			) as golongan ,
			c.name as jenis_pj
		
		from app.t_mtr_limit_certain_group_transaction a 
		left join app.t_mtr_limit_certain_group_transaction_detail b on a.limit_transaction_code =b .limit_transaction_code
		left join app.t_mtr_service c on a.service_id=c.id
		left join app.t_mtr_vehicle_class vc on vc.id = a.vehicle_class_id
		";
		return $qry;
	}			

	public function qryDetailExcept($limitCode, $where){

		$qry ="
			select 
				tmom.id,
				tmom.email 	
			from app.t_mtr_member tmom 
			left join 
			(	
				select 
				tddt.email, 
				tddt.limit_transaction_code 
				from app.t_mtr_limit_certain_group_transaction_detail tddt 
				where tddt.status=1 and  tddt.limit_transaction_code ='$limitCode'
			) tddt2 on  tmom .email = tddt2.email
			$where
			";
		return $qry;
	}

	public function qryDetailExceptCount($limitCode, $where){
		$qry ="
					select 
						count(tmom.id) as count_data	
					from app.t_mtr_member tmom 
					left join
					(	
						select 
						tddt.email, 
						tddt.limit_transaction_code 
						from app.t_mtr_limit_certain_group_transaction_detail tddt 
						where tddt.status=1 and  tddt.limit_transaction_code ='$limitCode'
					) tddt2 on  tmom .email = tddt2.email
					$where
				";
		return $qry;
	}	
		
	public function getUser(){

		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$idQuotaExcept=$this->input->post("idQuotaExcept[]");
		$search = str_replace(array("'",'"'),"",$this->input->post("search[value]"));
		$idData = $this->input->post("idData");
		
		$field = array(
			0 =>'email',
			1 =>'email',		
		);

		$order_column = $field[$order_column];

		$where = " where status <> '-5'  ";
		
		if(!empty($search))
		{ $where .= " and email ilike '%".trim($search)."%' "; }
		
		if($idData==1)
		{
			if(count((array)$idQuotaExcept)>0)
			{
				$getIdQuotaExcept=array();
				foreach ($idQuotaExcept as $value) {
					$getIdQuotaExcept[]="'".$value."'";
				}
				$where .= " and id not in (".implode(",",$getIdQuotaExcept).") ";
			}
		}
		else
		{
			if(count((array)$idQuotaExcept)>0)
			{
				$getIdQuotaExcept=array();
				foreach ($idQuotaExcept as $value) {
					$getIdQuotaExcept[]="'".$value."'";
				}
				$where .= " and id in (".implode(",",$getIdQuotaExcept).") ";
			}
			else
			{ $where .= " and id is null "; }
		}
		
		$sql = "
					select 
						id, 
						email
					from app.t_mtr_member
					{$where} ";

		$sqlCount = "
					  select 
						  count(id) as count_data 
					  from app.t_mtr_member
					  {$where} ";


		$records_total = $this->db->query($sqlCount)->row()->count_data;
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

			$row->actions="
							<div class='btn btn-primary-outline  transferData btnPembatasan' title='Pindah Ke Pengecualian' data-id='".$row->id."' onClick=myData.toException('".$row->id."','".$row->email."') >
								<i class='fa fa-arrow-right' aria-hidden='true' style='color:#f64e60' ></i>
							</div>
						";
							
			$row->number = $i;
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

	public function getUserExcept(){

		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$idQuotaExcept=$this->input->post("idQuotaExcept[]");
		$search = str_replace(array("'",'"'),"",$this->input->post("search[value]"));
		$idData = $this->input->post("idData");
		
		$field = array(
			0 =>'email',
			1 =>'email',					
		);

		$where = " where status <> '-5'  ";
		
		if(!empty($search))
		{ $where .= " and email ilike '%".trim($search)."%' "; }

		$order_column = $field[$order_column];

		if($idData==1)
		{
			if(count((array)$idQuotaExcept)>0)
			{
				$getIdQuotaExcept=array();
				foreach ($idQuotaExcept as $value) {
					$getIdQuotaExcept[]="'".$value."'";
				}
				$where .= " and id in  (".implode(",",$getIdQuotaExcept).") ";
			}
			else
			{ $where .= " and id is null "; }
		}
		else
		{
			if(count((array)$idQuotaExcept)>0)
			{
				$getIdQuotaExcept=array();
				foreach ($idQuotaExcept as $value) {
					$getIdQuotaExcept[]="'".$value."'";
				}
				$where .= " and id not in (".implode(",",$getIdQuotaExcept).") ";
			}
		}
		
		$sql = "
					select 
						id, 
						email
					from app.t_mtr_member
					{$where} ";

		$sqlCount = "
					  select 
						  count(id) as count_data 
					  from app.t_mtr_member
					  {$where}
				";

		// die($sql); exit;
		$records_total = $this->db->query($sqlCount)->row()->count_data;
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

			$row->actions="
							<div class='btn btn-primary-outline transferData' title='Pindah Ke Pembatasan' data-id='".$row->id."' onClick=myData.toLimit('".$row->id."','".$row->email."') >
								<i class='fa fa-arrow-left' aria-hidden='true' style='color:#f64e60' ></i>
							</div>

						";

			$row->number = $i;
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
	
	public function getUserDEtailPembatasan($id)
	{
		$qry="
		
			select 
			tmltd .limit_transaction_code,
			tmltd .email,
			tmltd .value,
			tmlt.limit_type ,
			tmlt.custom_value ,
			tmlt.custom_type ,
			tmltd.id 
		from 		 
			app.t_mtr_limit_certain_group_transaction tmlt 
			left join app.t_mtr_limit_certain_group_transaction_detail tmltd on tmlt.limit_transaction_code = tmltd.limit_transaction_code
			where tmltd.id='{$id}'		
		";

		return $this->db->query($qry);
	}

	public function select_data($table, $where)
	{
		return $this->db->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->db->insert($table, $data);
	}

	public function insert_detail( $data, $where, $status )
	{

        // print_r($data);exit;
		/*
		$data['limit_transaction_code']      
		$data['start_date']
		$data['end_date']             
		$data['value']
		$data['limit_type']
		$data['custom_value']
		$data['custom_type']
		*/

		$this->db->query("
			insert into  app.t_mtr_limit_certain_group_transaction_detail(
				limit_transaction_code,
				 email, 
				 limit_type, 
				 value, 
				 status, 
				 created_by, 
				 created_on
				 )
			select 			
				'".$data['limit_transaction_code'] ."' as limit_transaction_code,
				email,
				'".$data['limit_type']."' as limit_type,
				'".$data['value']."' as value,
				'".$status."' as status,
				'".$this->session->userdata('username')."' as created_by,
				'".date('Y-m-d H:i:s')."' as created_on			
			from
			app.t_mtr_member
			$where 
		");

	}	

	public function insert_detail_01032022( $data, $where, $status )
	{

		/*
		$data['limit_transaction_code']      
		$data['start_date']
		$data['end_date']             
		$data['value']
		$data['limit_type']
		$data['custom_value']
		$data['custom_type']
		*/
		if(!empty($data['custom_type'])) 
		{
			// $addField = " 
			// 	'{$customType}' as custom_type,
			// 	'{$customValue}' as custom_value,
			// ";

			$addField = " 
			'".$data['custom_value']."' as custom_value,
			'".$data['custom_type']."'  as custom_type ,
		";

			$fieldName = " 			
				custom_value,
				custom_type, 
			";
		}
		else
		{
			$addField =" ";
			$fieldName= " ";
		}


		$this->db->query("
			insert into  app.t_mtr_limit_transaction_detail(
				limit_transaction_code,
				 email, 
				 limit_type, 
				 value, 
				 ".$fieldName."
				 status, 
				 created_by, 
				 created_on
				 )
			select 			
				'".$data['limit_transaction_code'] ."' as limit_transaction_code,
				email,
				'".$data['limit_type']."' as limit_type,
				'".$data['value']."' as value,
				".$addField."
				'".$status."' as status,
				'".$this->session->userdata('username')."' as created_by,
				'".date('Y-m-d H:i:s')."' as created_on			
			from
			app.t_mtr_member
			$where 
		");

	}	

	public function insert_data_id($table,$data)
	{
		$this->db->insert($table, $data);
		$id = $this->db->insert_id();

		return $id;
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

	public function insert_data_batch($table,$data)
	{
		$this->db->insert_batch($table, $data);
	}

	public function masterLimitType($index)
	{
		$limitType=array(
			1=>'Per Jam',
			2=>'Per Hari',
			3=>'Per Bulan',
			4=>'Per Tahun',
		);


		return $limitType[$index];
	}

	public function checkOverlapsEdit($startDate, $endDate,$vehicleClass="",$service_id, $id)
    {
		$where ="";
		if(!empty($id))
		{
			$where .=" and id <> '".$id."'";

		}

		if(!empty($vehicleClass))
		{
			$where .="and vehicle_class_id ='".$vehicleClass."'";
			$where .="and service_id = '".$service_id."' " ;

		}

		if(empty($vehicleClass))
		{
			$where .="and service_id = '".$service_id."' " ;
		}

        $qry="        
            select * from app.t_mtr_limit_certain_group_transaction
            where 
			(
				(start_date between '{$startDate}' and '{$endDate}')
				or
				(end_date between '{$startDate}' and '{$endDate}')   	
			)		
			and status = 1
			{$where}
        ";

		// die($qry); exit;
        return $this->db->query($qry);
    }		

    public function checkOverlaps($startDate, $endDate,$vehicleClass="",$kendaraan,$pejalanKaki, $id)
    {
		$where ="";
		if(!empty($id))
		{
			$where .=" and id <> '".$id."'";

		}

		if(!empty($vehicleClass) && !empty($pejalanKaki) )
		
		{
			$where .="and vehicle_class_id in ($vehicleClass)";
			$where .="and service_id in ('".$kendaraan."','".$pejalanKaki."' )";
		}

		if(!empty($vehicleClass) && empty($pejalanKaki) )
		{
			$where .="and vehicle_class_id in ($vehicleClass)";
			$where .="and service_id = '".$kendaraan."' " ;
			
		}

		if(!empty($pejalanKaki)  && empty($vehicleClass)  )
		{
			$where .="and service_id = '".$pejalanKaki."' " ;

		}



        $qry="        
            select * from app.t_mtr_limit_certain_group_transaction
            where 
			(
				(start_date between '{$startDate}' and '{$endDate}')
				or
				(end_date between '{$startDate}' and '{$endDate}')
				or
				(start_date<='{$startDate}' and end_date >='{$endDate}')   	
			)		
			and status = 1
			{$where}
        ";

		// die($qry); exit;
        return $this->db->query($qry);
    }	

    public function checkTransaksi($startDate, $endDate)
    {

        $qry=" 	select value  from app.t_mtr_limit_transaction
             where 
			 start_date  ='2022-03-31 15:06:00.000' and end_date ='2022-11-25 11:11:00.000'	
		     and status != '-5' and status = '1'
        ";
		// die($qry); exit;
        return $this->db->query($qry);
    }

    public function download(){

		$dateFrom = trim($this->input->get('dateFrom'));
		$dateTo = trim($this->input->get('dateTo'));
	
		
		$where = " where a.status <> -5 ";
		if(empty($dateFrom) and empty($dateTo))
		{
			$where .= " ";
		}
		else if(!empty($dateFrom) and empty($dateTo))
		{
			$where .= " and start_date >='{$dateFrom}' ";
		}
		else if(empty($dateFrom) and !empty($dateTo))
		{
			$where .= " and end_date <= '{$dateTo}' ";
		}
		else
		{
			$where .= " and (start_date >='{$dateFrom}' and   end_date <='{$dateTo}') ";
		}		
	

		$sql = $this->qry($where);

		$rows_data = $this->db->query($sql)->result();

		$rows 	= array();
		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);


			$row->actions  =" ";

			$row->start_date=format_date($row->start_date);
			$row->end_date=format_date($row->end_date);

			if($row->status == 1){
				$row->status   = 'Aktif';
			}
			else
			{
				$row->status   = 'Tidak Aktif';
			}

			if(!empty($row->limit_type))
			{
				$row->limit_type= $this->masterLimitType($row->limit_type);
			}
			
			if($row->custom_type=="t")
			{				
				$row->custom_type= ("Iya");
			}
			else
			{
				$row->custom_type=("Tidak");
			}
			

			$rows[] = $row;
			unset($row->assignment_code);
			
		}

		return $rows;
	}

	public function qry($where="")
	{
		$qry="select 
				a .*,
				(
					case 
					when a.service_id = 2
					then vc.name
					else '' end
				) as golongan ,
				b.name as jenis_pj

			from app.t_mtr_limit_certain_group_transaction a 
			left join app.t_mtr_service b on a.service_id=b.id
			left join app.t_mtr_vehicle_class vc on vc.id = a.vehicle_class_id
			$where
		";
		// die($qry); exit;
		return $qry;
	}	
}