<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * -----------------------
 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  2022
 *
 */

class PembatasanMemberB2bModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module   = 'master_data/pembatasanMemberB2b';
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
			0 =>'tmltbb.id',
			1 =>'tmltbb.id',
			2=>"limit_transaction_code",
			3=>"merchant_name",
			4=>"start_date",
			5=>"end_date",
			6=>"limit_type",
			7=>"value",
			8=>"custom_type",
			9=>"custom_value",
			10=>"status",			
		);


		$order_column = $field[$order_column];

		$where = " where tmltbb.status <> -5 ";

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
			else //$searchName=="merchantName"
			{
				$where .=" and merchant_name ilike '%".$iLike."%' ";
			}

		}		

		$sql 		   = $this->qry()." ".$where;
		$sqlCount 		   = $this->qryCount()." ".$where;

		// die($sql); exit;

		$query         = $this->db->query($sql);
		// $records_total = $query->num_rows();
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


	public function getDetailMember(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$limitTransactionCode=$this->input->post('limitTransactionCode');
		$settingCustom = $this->input->post('settingCustom');
		
		$iLike        = trim(str_replace(array("'",'"'),"",($search['value'])));

		$searchData=trim(str_replace(array("'",'"'),"",($this->input->post("searchData"))) );
		$searchName=$this->input->post("searchName");

		
		$field = array(
			0 =>'id',
			1 => "limit_transaction_code",
			2 => "merchant_name", 
			3 => "outlet_id", 
			4 => "limit_type",      
			5 => "value",      
			6 => "custom_type",                                                 
			7 => "custom_value",           

		);

		$order_column = $field[$order_column];

		$where = " where tmltdbb.limit_transaction_code='{$limitTransactionCode}' and tmltdbb2.status=1 ";

		if($settingCustom==1)
		{
			$where .= " and tmltdbb2.value <> tmltdbb.value  ";
		}
		else
		{
			$where .= " and tmltdbb2.value = tmltdbb.value  ";
		}

		if(!empty($searchData))
		{
			if($searchName=="outletId")
			{
				$where .= "  and tmltdbb2.outlet_id ='".$searchData."' ";
			}
		}

		$sql 		   = $this->qryDetail()." ".$where;

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

		// search header 

		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$exception    = site_url($this->_module."/action_change_detail_limit_member_except/".$this->enc->encode($row->id_detail.'|-5'));
			$limit       = site_url($this->_module."/action_change_detail_limit_member/".$this->enc->encode($row->id_detail.'|1|'));

			$row->id =$row->id;
			$idDetailMember=$this->enc->encode($row->id_detail);
			$edit_url 	 = site_url($this->_module."/edit_detail_pembatasan/{$idDetailMember}");


     		$row->actions="";

		    
			$row->status_detail= success_label("Pembatasan");
			if($row->status == 1 )
			{

					$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-warning" onclick="myData.confirmationAction2(\'Apakah Anda yakin mengecualikan user ini ?\', \''.$exception.'\',\''.$limitTransactionCode.'\')" title="ke Pengecualian"> <i class="fa fa-check"></i> </button> ');
					$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				
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

	public function qryDetail(){

		$qry ="
				select 
					tmltdbb .id,
					tmltdbb .limit_transaction_code,
					tmltdbb .status,
					tmltdbb .merchant_id ,
					tmltdbb . limit_type,
					tmltdbb . custom_type,
					tmltdbb .custom_value,
					tmltdbb2 .value,
					tmltdbb2.id as id_detail,
					tmltdbb2.status as status_detail ,
					tmltdbb2 .outlet_id ,
					tmm.merchant_name					
				from app.t_mtr_limit_transaction_b2b tmltdbb 
				left join app.t_mtr_limit_transaction_detail_b2b tmltdbb2 
				on tmltdbb.limit_transaction_code =tmltdbb2 .limit_transaction_code 
				left join app.t_mtr_merchant tmm on tmltdbb .merchant_id = tmm.merchant_id 				
		";

		return $qry;
	}
	public function qryDetailCount(){

		$qry ="
				select 
					count(tmltdbb .id) as count_data
				from app.t_mtr_limit_transaction_b2b tmltdbb 
				left join app.t_mtr_limit_transaction_detail_b2b tmltdbb2 
				on tmltdbb.limit_transaction_code =tmltdbb2 .limit_transaction_code 
				left join app.t_mtr_merchant tmm on tmltdbb .merchant_id = tmm.merchant_id 				
		";

		return $qry;
	}

	public function getDetailMemberExcept(){
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
		
		// $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		$iLike        = trim(str_replace(array("'",'"'),"",($search['value'])));

		$searchData=trim(str_replace(array("'",'"'),"",($this->input->post("searchData"))) );
		$searchName=$this->input->post("searchName");
		
		$field = array(
			0 =>'id',
			1 =>"merchant_name", 
			2 =>"outlet_id",        
			3 => "status",

		);

		$order_column = $field[$order_column];

		$getHeader=$this->select_data("app.t_mtr_limit_transaction_b2b"," where limit_transaction_code='".$limitTransactionCode."' ")->row();

		$where = " where tmom.status=1 
					and tmom.merchant_id='".$getHeader->merchant_id."'
					and
					outlet_id not in (

						select outlet_id 
							from 
						app.t_mtr_limit_transaction_detail_b2b 
						where limit_transaction_code='".$limitTransactionCode."'
						and status = 1
					)		
		";


		if(!empty($searchData))
		{
			if($searchName=="outletId")
			{
				$where .= "  and tmom.outlet_id ='".$searchData."' ";
			}
		}


		$sql 		   = $this->qryDetailExcept()." ".$where;
		$sqlCount 		   = $this->qryDetailExceptCount()." ".$where;

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
			$limit       = site_url($this->_module."/action_change_detail_limit_member/".$this->enc->encode($limitTransactionCode.'|1|'.$row->outlet_id));
			$row->id =$row->id;
			$idDetailMember=$this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit_detail_pembatasan/{$idDetailMember}");
     		$row->actions="";
			$row->status=failed_label("Pengecualian");
			$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="myData.confirmationAction2(\'Apakah Anda yakin membatasi user ini ?\', \''.$limit.'\',\''.$limitTransactionCode.'\')" title="ke Pembatasan"> <i class="fa fa-ban"></i> </button> ');
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


	public function qryDetailExcept(){

		$qry ="
			select 
				tmm.merchant_name ,
				tmom.* 
			from app.t_mtr_outlet_merchant tmom 
			left join app.t_mtr_merchant tmm on tmom .merchant_id = tmm.merchant_id 		
		";

		return $qry;
	}
	public function qryDetailExceptCount(){

		$qry ="
				select 
					count(tmom .id) as count_data
				from app.t_mtr_outlet_merchant tmom 
				left join app.t_mtr_merchant tmm on tmom .merchant_id = tmm.merchant_id 				
		";

		return $qry;
	}	
	
	public function getDetailMemberPembatasanCustom(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$vaccineParamId=$this->input->post('vaccineParamId');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$limitTransactionCode=$this->input->post('limitTransactionCode');
		// $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		$iLike        = trim(str_replace(array("'",'"'),"",($search['value'])));

		$searchData=trim(str_replace(array("'",'"'),"",($this->input->post("searchData"))) );
		$searchName=$this->input->post("searchName");

		
		$field = array(
			0 =>'id',
			1=>"email",
			2=>"limit_type", 
			3=>"value",      
			4=>"custom_type",                                                 
			5=>"custom_value",   	
			6=>"status",   			

		);

		$order_column = $field[$order_column];

		$where = " where mc.id is not null ";

		if(!empty($searchData))
		{
			if($searchName=="email")
			{
				$where .=" and mc.email ilike '%".$searchData."%' ";
			}

		}	

		$sql 		   = $this->qryDetailLimitMemeber($limitTransactionCode)." ".$where;

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

		// search header 
		$dataHeader= $this->select_data("app.t_mtr_limit_transaction"," where limit_transaction_code='{$limitTransactionCode}' ")->row();

		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$exception    = site_url($this->_module."/action_change_detail_limit_member_except/".$this->enc->encode($row->id_detail.'|-5|'));
			$limit       = site_url($this->_module."/action_change_detail_limit_member/".$this->enc->encode($row->id_detail.'|1|'));

			$row->id =$row->id;
			$idDetailMember=$this->enc->encode($row->id_detail);
			$edit_url 	 = site_url($this->_module."/edit_detail_pembatasan/{$idDetailMember}");


     		$row->actions="";

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

	public function qryDetailLimitMemeber($limitTransactionCode)
	{
		$qry="
		select 
		tmom.outlet_id as id_outlet_tbl_merchant,
		mc.*
		from app.t_mtr_outlet_merchant tmom 
		left join 
		(
			select 
				tmltdbb.id,
				tmltdbb.outlet_id,
				tmltdbb.status as status_detail,
				tmltdbb.limit_transaction_code ,
				tb.status,
				tb.value,
				tb.custom_type ,
				tb.custom_value ,
				tmm.merchant_name
			from
			app.t_mtr_limit_transaction_detail_b2b tmltdbb 
			join app.t_mtr_limit_transaction_b2b tb on tmltdbb .limit_transaction_code=tb.limit_transaction_code
			join app.t_mtr_merchant tmm on tmltdbb .merchant_id =tmm.merchant_id 
			where tmltdbb.status = 1
			and  tmltdbb.limit_transaction_code='{$limitTransactionCode}'
		
		) mc on tmom.outlet_id = mc.outlet_id 
			 			
		";

		return $qry;
	}

	public function qry()
	{
		$qry="
		
			select 
				tmm.username,
				tmm.merchant_name ,
				tmltbb .*
			from app.t_mtr_limit_transaction_b2b tmltbb 
			left join app.t_mtr_merchant tmm on tmltbb .merchant_id = tmm.merchant_id 			
		
		";

		return $qry;
	}


	public function qryCount()
	{
		$qry="
		
			select 
				count(tmm.id) as count_data
			from app.t_mtr_limit_transaction_b2b tmltbb 
			left join app.t_mtr_merchant tmm on tmltbb .merchant_id = tmm.merchant_id 			
		
		";

		return $qry;
	}	

	public function getMemberLimit(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$vaccineParamId=$this->input->post('vaccineParamId');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);

		$merchantId=$this->enc->decode($this->input->post('merchantId'));
		$userId=$this->input->post("userId[]");
		$idData=$this->input->post("idData");

		$iLike        = trim(str_replace(array("'",'"'),"",($search['value'])));

		// $searchData=trim(str_replace(array("'",'"'),"",($this->input->post("searchData"))) );
		// $searchName=$this->input->post("searchName");

		
		$field = array(
			0 =>'outlet_id',
			1 =>'outlet_id',
		);

		$order_column = $field[$order_column];

		$where = " where merchant_id ='{$merchantId}' and status <>'-5' ";

		if($idData==1) 
		{
			if(count((array)$userId)>0)
			{	
				$userIdData=array();
				foreach ($userId as $value) {
					$userIdData[]="'".$value."'";
				}

				$where .= " and  id not in (".implode(",",$userIdData).") ";
			}
		}
		else
		{
			if(count((array)$userId)>0) // jika idData 0 dan terdapat data iduser yg dsimpan
			{	
				$userIdData=array();
				foreach ($userId as $value) {
					$userIdData[]="'".$value."'";
				}

				$where .= " and id in (".implode(",",$userIdData).") ";
			}
			else  // jika idData 0 dan tidak terdapat data iduser yg dsimpan
			{
				$where .= " and id is null ";
			}
		}

		if(!empty($iLike))
		{
			$where .= " and outlet_id ilike '%".$iLike."%' ";
		}

		$sql 		   = "
							SELECT  
								* 
							from app.t_mtr_outlet_merchant 
							{$where}
						";

		$sqlCount 		   = "
								SELECT  
									count(id) as count_data
								from app.t_mtr_outlet_merchant 
								{$where}
							";						

		// die($sql); exit;
		$query         = $this->db->query($sql);
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
			$row->number = $i;
     		$row->actions="
			 
				<div class='btn btn-primary-outline  transferData btnPembatasan' title='Pindah Ke Pengecualian' data-id='".$row->id."' onClick=myData.toExcept('".$row->id."') >
					<i class='fa fa-arrow-right text-danger' aria-hidden='true' style='color:#f64e60'  ></i>
				</div> 
			 
			 ";
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

	public function getMemberExcept(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$vaccineParamId=$this->input->post('vaccineParamId');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$userId=$this->input->post("userId[]");
		$idData=$this->input->post("idData");


		$merchantId=$this->enc->decode($this->input->post('merchantId'));
		$iLike        = trim(str_replace(array("'",'"'),"",($search['value'])));


		
		$field = array(
			0 =>'outlet_id',
			1 =>'outlet_id',
		);

		$order_column = $field[$order_column];

		$where = " where merchant_id ='{$merchantId}' and status<>'-5'  ";

		if($idData != 1) 
		{
			if(count((array)$userId)>0)
			{	
				$userIdData=array();
				foreach ($userId as $value) {
					$userIdData[]="'".$value."'";
				}

				$where .= " and  id not in (".implode(",",$userIdData).") ";
			}
		}
		else
		{
			if(count((array)$userId)>0) // jika idData 0 dan terdapat data iduser yg dsimpan
			{	
				$userIdData=array();
				foreach ($userId as $value) {
					$userIdData[]="'".$value."'";
				}

				$where .= " and id in (".implode(",",$userIdData).") ";
			}
			else  // jika idData 0 dan tidak terdapat data iduser yg dsimpan
			{
				$where .= " and id is null ";
			}
		}		

		if(!empty($iLike))
		{
			$where .= " and outlet_id ilike '%".$iLike."%' ";
		}		

		$sql 		   = "
							SELECT  
								* 
							from app.t_mtr_outlet_merchant 
							{$where}
						";

		$sqlCount 		   = "
								SELECT  
									count(id) as count_data
								from app.t_mtr_outlet_merchant 
								{$where}
							";						

		// die($sql); exit;
		$query         = $this->db->query($sql);
		$records_total = $this->db->query($sqlCount)->row()->count_data;
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

     		$row->actions="
			 
				<div class='btn btn-primary-outline transferData btnPembatasan' title='Pindah Ke Pembatasan' data-id='".$row->id."' onClick=myData.toLimit('".$row->id."') >
					<i class='fa fa-arrow-left' aria-hidden='true' style='color:#f64e60'  ></i>
				</div> 
			 
			 ";			 
			 

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


	public function getUser(){

		$qry="
				select 
					id, 
					email
				from app.t_mtr_member
				order by email asc
		";

		return $this->dbView->query($qry)->result();
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

		/*
		$data['limit_transaction_code']                   
		$data['value']
		*/

		$this->db->query("
			insert into  app.t_mtr_limit_transaction_detail_b2b 
			(
				limit_transaction_code,
				merchant_id, 
				outlet_id, 
				value,  
				status, 
				created_by, 
				created_on
			)
			select 
			
				'".$data['limit_transaction_code'] ."' as limit_transaction_code,
				merchant_id,
				outlet_id,
				'".$data['value']."' as value,
				'".$status."' as status,
				'".$this->session->userdata('username')."' as created_by,
				'".date('Y-m-d H:i:s')."' as created_on			
			from
			app.t_mtr_outlet_merchant 
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

    public function checkOverlaps($startDate, $endDate, $merchant,$id)
    {
        $qry_23022022="        
            select * from app.t_mtr_limit_transaction_b2b tmltbb 
            where 
			(
				(start_date between '{$startDate}' and '{$endDate}')
				or
				(end_date between '{$startDate}' and '{$endDate}')   
			)
			and merchant_id='{$merchant}'
			and status != '-5'
        ";


		$where ="";
		if(!empty($id))
		{
			$where .=" and id <> '".$id."'";
		}

        $qry="        
            select * from app.t_mtr_limit_transaction_b2b tmltbb 
            where 
			(
				(start_date between '{$startDate}' and '{$endDate}')
				or
				(end_date between '{$startDate}' and '{$endDate}')   
			)
			and merchant_id='{$merchant}'
			and status = 1
			{$where}
        ";		

		// print_r($qry); exit;

        return $this->db->query($qry);
    }	


}
