<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  26/10/2023
 *
 */

class Rms_b2b_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'radius/rms_b2b';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$activeDate =$this->db->escape_str($this->input->post("activeDate"));
		$dateFrom =$this->convertFormateDate($this->db->escape_str($this->input->post("dateFrom")));
		$dateTo =$this->convertFormateDate($this->db->escape_str($this->input->post("dateTo")));
		$port =$this->enc->decode($this->input->post("port"));
		$searchData=$this->db->escape_str(trim($this->input->post("searchData")));
		$searchName=$this->db->escape_str(trim($this->input->post("searchName")));
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
		);

		$order_column = $field[$order_column];

		// type 2 = b2b l
		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));
		// $where = " WHERE status not in (-5) and (start_date>='".$dateFrom."' and end_date<='".$dateToNew."')  and type=2 ";
		$where = " WHERE status not in (-5) and created_on >= '". $dateFrom . "' and created_on < '" . $dateToNew . "' and type=2 ";

		if(!empty($port))
		{
			$where .="and  port_id =  '" .$port."' ";
		}

		if(!empty($activeDate))
		{
			$where .=" and reservation_date::date ='".$this->convertFormateDate($activeDate)."' "	;
		}

		if(!empty($searchData))
		{
			if($searchName == "rmsCode")
			{
				$where .="and  rms_code =  '" .$searchData."' ";
			}
		}

		$sql 		   = "
							select * from app.t_mtr_rms
							{$where}
						 ";

		// die($sql); exit;

		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}
		$query     = $this->db->query($sql);
		$rows_data = $query->result();
		$rmsCode = @array_column($rows_data,"rms_code");

		$dataChannel[""][]="";
		if(!empty($rmsCode))
		{
			$rmsCodeString = array_map(function($x){ return "'".$x."'";}, $rmsCode);

			$getChannel = $this->select_data("app.t_mtr_rms_channel"," where rms_code in (".implode(",",$rmsCodeString).") and status = 1 order by channel asc ")->result();

			foreach ($getChannel as $keyGetChannel => $valueGetChannel) {
				$dataChannel[$valueGetChannel->rms_code][]= $valueGetChannel->channel;
			}
		}
		
		
		$dataPort = $this->getMaster("t_mtr_port","id","name"); 
		
		$rows 	= array();
		$i  	= ($start + 1);

		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);
			$rms_code_enc=$this->enc->encode($row->rms_code);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$rms_code_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

			$dataAllChannel = @$dataChannel[$row->rms_code];

			$row->layanan = "";

			if($row->is_vehicle =="t")
			{
				$row->layanan .= "- Kendaraan <br>";
			}

			if($row->is_pedestrian =="t")
			{
				$row->layanan .= "- Pejalan Kaki <br>";
			}			

			$row->channel = "";
			$channelWeb =0;
			$channelIfcs =0; 
			if(!empty($dataAllChannel))
			{
				$row->channel = implode(", ", $dataAllChannel);

				// untuk validasi tombbol add pada detail
				foreach ($dataAllChannel as $key => $value) {
					if($value=='web' || $value=='mobile')
					{
						$channelWeb +=1;
					}
					else 
					{
						$channelIfcs +=1; 
					}
				}
			}
	
			$radius = $row->radius;
			$radiusType = $row->radius_type==1?"KM":"M";

			$row->port_name = @$dataPort[$row->port_id];
			$row->radius = $row->radius." ".$radiusType;
			$row->view_map ="<a onclick=myData.showModal('".$row->latitude."','".$row->longitude."','".$row->rms_code."') id='view_".$row->rms_code."' data-radius='".$radius."' data-radiusType='".$radiusType."' > Lihat Map </a>";

     		$row->actions  =" ";
			$row->rms_code_enc=$rms_code_enc;

			// $row->btn_add_detail_user_exp ="";
			
			$row->btn_add_detail_user_ifcs_exp ="";
			$row->btn_add_detail_outlet ="";
			if($row->status == 1){
				$row->btn_add_detail_outlet = generate_button_new($this->_module, 'add',  site_url($this->_module."/add_detail_outlet/{$rms_code_enc}"));
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');				

			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}
     		$row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
			$dateNow = date("Y-m-d H:i");
			$row->ket ="";
			if($row->end_date <= $dateNow  )
			{
				$row->ket = '<span class="label label-warning">EXPIRED</span>';
			}

			 $row->created_on = format_date($row->created_on)." ".format_time($row->created_on);
			 $row->reservation_date = format_date($row->reservation_date)." ".format_time($row->reservation_date);
			 $row->start_date = format_date($row->start_date)." ".format_time($row->start_date);
			 $row->end_date = format_date($row->end_date)." ".format_time($row->end_date);


			$rows[] = $row;

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

    public function detailOutlet(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$search = $this->input->post('search');
		$draw = $this->input->post('draw');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$rmsCode = $this->db->escape_str($this->enc->decode($this->input->post('rmsCode')));
		$iLike        = trim(($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'bb.id',
			1 =>'rms_code',
			2 =>'merchant_name',
			3 =>'outlet_id',

		);

		$order_column = $field[$order_column];

		$where = " WHERE bb.rms_code='".$rmsCode."' and bb.status not in (-5) ";

		if(!empty($iLike))
		{
			$where .=" and ( tmm.merchant_name ilike '%".$iLike."%' ESCAPE '!'
							or bb.outlet_id ilike '%".$iLike."%' ESCAPE '!' ) "; 
		}

		$sql 		   = "SELECT 
									tmm.merchant_name ,
									bb.outlet_id ,
									bb.id ,
									bb.rms_code ,
									rms.status,									
									tmom.description 
								from app.t_mtr_rms_outlet_b2b bb
								join app.t_mtr_rms rms on bb.rms_code = rms.rms_code
								join app.t_mtr_merchant tmm on bb.merchant_id = tmm.merchant_id 
								join app.t_mtr_outlet_merchant tmom on bb.outlet_id = tmom.outlet_id 	and bb.merchant_id = tmom.merchant_id 
							{$where}
						 ";

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

			$row->actions ="";
			$id_enc = $this->enc->encode($row->id."|app.t_mtr_rms_outlet_b2b|".$row->rms_code."| mengecualikan outlet");
			$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
			$btn_delete ='<button class="btn btn-sm btn-primary" onclick="myData.confirmationAction('."'".'Apakah Anda yakin mengecualikan outlet ini ?'."'".','."'".$delete_url ."'".' )" title="Kecualikan"> <i class="fa fa-check"></i> </button>';
			

			if($row->status == 1)
			{
				$row->actions .= generate_button($this->_module, 'delete', $btn_delete) ;
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
			'data'           => $rows,
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
		);
	}	
    public function detailOutletExcept(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$search = $this->input->post('search');
		$draw = $this->input->post('draw');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$rmsCode = $this->db->escape_str($this->enc->decode($this->input->post('rmsCode')));
		$iLike        = trim(($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'bb.id',
			1 =>'rms_code',
			2 =>'merchant_name',
			3 =>'outlet_id',

		);

		$order_column = $field[$order_column];

		$where = " WHERE mrc.rms_code='".$rmsCode."' 
							and mrc.status=1  
							and bb.status=1
							and mrc.is_custom_outlet is true
							and tmom.id is  null ";

		if(!empty($iLike))
		{
			$where .=" and ( tmm.merchant_name ilike '%".$iLike."%' ESCAPE '!'
							or bb.outlet_id ilike '%".$iLike."%' ESCAPE '!' ) "; 
		}

		$sql 		   = "SELECT 
								tmm.merchant_name ,
								tmom.id as id_outlet_rms,	
								mrc.rms_code ,
								rms.status,
								bb.outlet_id ,
								bb.merchant_id ,
								bb.description,
								bb.id  
							from app.t_mtr_outlet_merchant  bb
							left join app.t_mtr_rms_merchant_b2b mrc on bb.merchant_id = mrc.merchant_id  
							left join  app.t_mtr_rms_outlet_b2b tmom on bb.outlet_id = tmom.outlet_id and tmom.status=1 and bb.merchant_id = tmom.merchant_id  and tmom.rms_code='".$rmsCode."' 
							join app.t_mtr_rms rms on mrc.rms_code = rms.rms_code
							join app.t_mtr_merchant tmm on bb.merchant_id = tmm.merchant_id 
							{$where}
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

			$row->actions ="";
			$id_enc = $this->enc->encode($row->id."|app.t_mtr_rms_outlet_b2b|".$row->rms_code);
			$delete_url  = site_url($this->_module."/action_limit/{$id_enc}");
			$btn_delete ='<button class="btn btn-sm btn-danger"  onclick="myData.confirmationAction('."'".'Apakah Anda yakin membatasi outlet ini ?'."'".','."'".$delete_url ."'".' )" title="Pembatasan"> <i class="fa fa-ban"></i> </button>';
			

			if($row->status == 1)
			{
				$row->actions .= generate_button($this->_module, 'delete', $btn_delete) ;
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
			'data'           => $rows,
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
		);
	}		
    public function detailMerchant(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$search = $this->input->post('search');
		$draw = $this->input->post('draw');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$rmsCode = $this->db->escape_str($this->enc->decode($this->input->post('rmsCode')));
		$iLike        = trim(($this->db->escape_like_str($search['value'])));
		
		$field = array(
			0 =>'id',
			1 =>'rms_code',
			2 => 'merchant_name',
			3 => 'outlet_id',

		);

		$order_column = $field[$order_column];

		$where = " WHERE bb.rms_code='".$rmsCode."' and bb.status not in (-5) ";

		if(!empty($search['value']))
		{
			$where .="and  tmm.merchant_name  ilike '%".$iLike."%' ESCAPE '!' "; 
		}

		$sql 		   = "SELECT 
								bb.rms_code ,
								bb.merchant_id ,
								bb.id,
								bb.is_custom_outlet,
								tmm.merchant_name 
							from app.t_mtr_rms_merchant_b2b bb
							join app.t_mtr_merchant tmm on bb.merchant_id = tmm.merchant_id 
							{$where}
						 ";

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

			$row->actions ="";
			$id_enc = $this->enc->encode($row->id."|app.t_mtr_exception_rms_ifcs|".$row->rms_code);
			$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
			$btn_delete ='<button class="btn btn-sm btn-danger" onclick="myData.confirmationAction('."'".'Apakah Anda yakin menghapus data ini ?'."'".','."'".$delete_url ."'".',4 )" title="Hapus"> <i class="fa fa-trash-o"></i> </button>';			

			$row->is_outlet = $row->is_custom_outlet == 't'? '<span class="label label-success"><i class="fa fa-check-circle"></i><span></span></span>':'<span class="label label-danger"><i class="fa fa-times-circle"></i><span></span></span>';

			$row->actions .= generate_button($this->_module, 'delete', $btn_delete) ;
			$row->no=$i;


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
    public function detailGolongan(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$search = $this->input->post('search');
		$draw = $this->input->post('draw');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$rmsCode = $this->db->escape_str($this->enc->decode($this->input->post('rmsCode')));
		$iLike        = trim(($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'vehicle_class_name',
			1 =>'vehicle_class_name',

		);

		$order_column = $field[$order_column];

		$where = " WHERE a.rms_code='".$rmsCode."' and a.status not in (-5) ";

		if(!empty($search['value']))
		{
			$where .="and  b.name ilike '%".$iLike."%' ESCAPE '!' "; 
		}

		$sql 		   = "
								select 
								a.class_id ,
								a.id ,
								a.rms_code ,
								b.name as vehicle_class_name
							from app.t_mtr_rms_detail_class a
							left join app.t_mtr_vehicle_class b on a.class_id =b.id
							{$where}
						 ";

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

		$dataVehicleClass = $this->getMaster("t_mtr_vehicle_class","id","name"); 

		foreach ($rows_data as $row) {

			$row->actions ="";
			$id_enc = $this->enc->encode($row->id."|app.t_mtr_exception_rms_ifcs|".$row->rms_code);
			$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
			$btn_delete ='<button class="btn btn-sm btn-danger" onclick="myData.confirmationAction('."'".'Apakah Anda yakin menghapus data ini ?'."'".','."'".$delete_url ."'".',2 )" title="Hapus"> <i class="fa fa-trash-o"></i> </button>';
			

			$row->actions .= generate_button($this->_module, 'delete', $btn_delete) ;
			$row->no=$i;

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
	public function getOutletImpact(){

		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$idMemberImpact=$this->input->post("idMemberImpact[]");
		$rmsCode = $this->enc->decode($this->input->post("rmsCode"));
		$search = str_replace(array("'",'"'),"",$this->input->post("search[value]"));
		$idData = $this->input->post("idData");
		
		$field = array(
			0 =>'tmom.id',
			1 =>'tmom.id',		
		);

		$order_column = $field[$order_column];

		$where = " where tmom.status <> '-5' and tmom.id is null "; // handling jika kode rms encode di edit2 client
		if(!empty($rmsCode))
		{
			// echo $rmsCode; exit;
			$dataRmsOutlet = $this->select_data("app.t_mtr_rms_merchant_b2b", " where rms_code='".$rmsCode."' and status=1 and is_custom_outlet is true  ")->result();

			// print_r($dataRmsOutlet); exit;
			$merchatUnique =array_unique(array_column($dataRmsOutlet,"merchant_id"));
			$getMerchantId = array_map(function($x){ return "'".$x."'"; },$merchatUnique);
			$where = " where tmom.status <> '-5' ";
			
			if(!empty($merchatUnique))
			{
				$where .="and  tmom.merchant_id in (". implode(",", $getMerchantId) .") ";
			}
			else
			{
				$where .="and  tmom.id is null";
			}

			$getOutlet = $this->select_data("app.t_mtr_rms_outlet_b2b", " where rms_code='".$rmsCode."' and status=1 ")->result();
			if(!empty($getOutlet))
			{
				$where .=" 
					and tmom.outlet_id not in (
						select outlet_id from app.t_mtr_rms_outlet_b2b where rms_code='".$rmsCode."' and status=1
					)				
				";
			}				
		}

		
		if(!empty($search))
		{ 
			$where .= " 
			 and (
				tmm.merchant_name ='".$search."'
				or tmom .outlet_id ='".$search."' ) "; 
		}		
		
		if($idData==1)
		{
			if(count((array)$idMemberImpact)>0)
			{
				$getIdMemberImpact=array();
				foreach ($idMemberImpact as $value) {
					$getIdMemberImpact[]="'".$value."'";
				}
				$where .= " and tmom.id  in (".implode(",",$getIdMemberImpact).") ";
			}
			else
			{ $where .= " and tmom.id is null "; }
		}
		else
		{

			if(count((array)$idMemberImpact)>0)
			{
				$getIdMemberImpact=array();
				foreach ($idMemberImpact as $value) {
					$getIdMemberImpact[]="'".$value."'";
				}
				$where .= " and tmom.id  not in (".implode(",",$getIdMemberImpact).") ";
			}			
		}
		
		$sql = "
					SELECT 
						tmm.merchant_name ,
						tmom .outlet_id,
						tmom.id 
					FROM app.t_mtr_outlet_merchant tmom 
					join app.t_mtr_merchant tmm on tmom.merchant_id =tmm.merchant_id 
					{$where} ";
		// die($sql); exit;

		$sqlCount = "
					  select 
						  count(tmom.id) as count_data 
						  FROM app.t_mtr_outlet_merchant tmom 
						  join app.t_mtr_merchant tmm on tmom.merchant_id =tmm.merchant_id 
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
							<div class='btn btn-primary-outline  transferData btnPembatasan' title='Pindah Ke Pengecualian' data-id='".$row->id."' onClick=myData.toException('".$row->id."','".$row->outlet_id."') >
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

	public function getOutletExcept(){

		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$idMemberImpact=$this->input->post("idMemberImpact[]");
		$rmsCode = $this->enc->decode($this->input->post("rmsCode"));
		$search = trim($this->db->escape_str(str_replace(array("'",'"'),"",$this->input->post("search[value]"))));
		$idData = $this->input->post("idData");
		
		$field = array(
			0 =>'merchant_name',
			1 =>'outlet_id',		
		);		

		$where = " where tmom.status <> '-5'  "; // handling jika kode rms encode di edit2 client
		// kode rms encode di edit2 client
		if(!empty($rmsCode))
		{
			// echo $rmsCode; exit;
			$dataRmsOutlet = $this->select_data("app.t_mtr_rms_merchant_b2b", " where rms_code='".$rmsCode."' and status=1 and is_custom_outlet is true  ")->result();

			// print_r($dataRmsOutlet); exit;
			$merchatUnique =array_unique(array_column($dataRmsOutlet,"merchant_id"));
			$getMerchantId = array_map(function($x){ return "'".$x."'"; },$merchatUnique);

			$where = " where tmom.status <> '-5' ";
			
			if(!empty($merchatUnique))
			{
				$where .="and  tmom.merchant_id in (". implode(",", $getMerchantId) .") ";
			}
			else
			{
				$where .="and  tmom.id is null";
			}

			$getOutlet = $this->select_data("app.t_mtr_rms_outlet_b2b", " where rms_code='".$rmsCode."' and status=1 ")->result();
			if(!empty($getOutlet))
			{
				$where .=" 
					and tmom.outlet_id not in (
						select outlet_id from app.t_mtr_rms_outlet_b2b where rms_code='".$rmsCode."' and status=1
					)
				
				";
			}			
		}
		

		if(!empty($search))
		{ 
			$where .= " 
			 and (
				tmm.merchant_name ='".$search."'
				or tmom .outlet_id ='".$search."' ) "; 
		}	
		
		// if(!empty($search))
		// { $where .= " and email ilike '%".trim($search)."%' "; }
		if(!empty($search))
		{ 
			$where .= " 
			 and (
				tmm.merchant_name ='".$search."'
				or tmom .outlet_id ='".$search."' ) "; 
		}		

		$order_column = $field[$order_column];

		if($idData ==1)
		{
			if(count((array)$idMemberImpact)>0)
			{
				$getIdMemberImpact=array();
				foreach ($idMemberImpact as $value) {
					$getIdMemberImpact[]="'".$value."'";
				}
				$where .= " and tmom.id  not in (".implode(",",$getIdMemberImpact).") ";
			}
		}
		else
		{

			if(count((array)$idMemberImpact)>0)
			{
				$getIdMemberImpact=array();
				foreach ($idMemberImpact as $value) {
					$getIdMemberImpact[]="'".$value."'";
				}
				$where .= " and tmom.id  in  (".implode(",",$getIdMemberImpact).") ";
			}
			else
			{
				$where .= " and tmom.id is null "; 
			}

		}
		
		$sql = "SELECT 
						tmm.merchant_name ,
						tmom .outlet_id,
						tmom.id 
					FROM app.t_mtr_outlet_merchant tmom 
					join app.t_mtr_merchant tmm on tmom.merchant_id =tmm.merchant_id 
					{$where} ";
		// die($sql); exit;

		$sqlCount = "
					  select 
						  count(tmom.id) as count_data 
						  FROM app.t_mtr_outlet_merchant tmom 
						  join app.t_mtr_merchant tmm on tmom.merchant_id =tmm.merchant_id 
					  {$where} ";

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
							<div class='btn btn-primary-outline transferData' title='Pindah Ke Pembatasan' data-id='".$row->outlet_id."' onClick=myData.toLimit('".$row->id."') >
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

	public function getMaster($table,$id,$name)
	{		
		$service =  $this->select_data("app.$table"," where status != '-5' ")->result() ;
        $checkSession = $this->session->userdata("app.".$table.$name); 

        if($checkSession)
        {
            $dataReturn = $checkSession;
        }
        else
        {

            $dataReturn=array();    
            foreach ($service as $key => $value) {
                $dataReturn[$value->$id]= $value->$name;
            }

            $this->session->set_userdata(array("app.".$table => $dataReturn));
        }

		return $dataReturn ;

	}     	

	public function getDropdown($table, $id, $name, $selected="")
	{
		$data = $this->select_data($table, " where status =1 order by $name asc" )->result();

		$returnData[""]="Pilih";
		$getSelected ="";
		foreach ($data as $key => $value) {
			$encodeId = $this->enc->encode($value->$id);
			if($selected == $value->$id)
			{
				$encodeId = $this->enc->encode($value->$id);
				$getSelected = $encodeId;
			}
			$returnData[$encodeId]=$value->$name;
		}

		if(!empty($selected))
		{
			return  array("data"=>$returnData,"selected"=>$getSelected );
			exit;
		}
		return  $returnData;
	}
	public function getDropdown2($table, $id, $name, $selected="")
	{
		$data = $this->select_data($table, " where status !='-5' order by $name asc" )->result();

		$returnData[""]="Pilih";
		$getSelected ="";
		foreach ($data as $key => $value) {
			$encodeId = $this->enc->encode($value->$id);
			if($selected == $value->$id)
			{
				$encodeId = $this->enc->encode($value->$id);
				$getSelected = $encodeId;
			}
			$returnData[$encodeId]=$value->$name;
		}

		if(!empty($selected))
		{
			return  array("data"=>$returnData,"selected"=>$getSelected );
			exit;
		}
		return  $returnData;

	}	

    public function checkOverlaps($startDate, $endDate, $portId, $rmsCode="")
    {
		
		$where =" and status=1 and port_id='".$portId."' and type =2 ";

		if(!empty($rmsCode))
		{
			$where .=" and rms_code != '".$rmsCode."' ";
		}

        $qry="        
            select * from app.t_mtr_rms  
            where 
			(
				(start_date between '{$startDate}' and '{$endDate}')
				or
				(end_date between '{$startDate}' and '{$endDate}')   
				or
				(start_date<='{$startDate}' and end_date >='{$endDate}')      	
			)			
			{$where}
        ";
		// die($qry); exit;
        return $this->db->query($qry);
    }		


	public function getRmsHeader($code)
	{
		$qry ="select 
			rms_code,
			type,
			port_id,
			reservation_date ,
			start_date ,
			end_date ,
			latitude ,
			longitude ,
			radius ,
			radius_type ,
			is_pedestrian ,
			is_vehicle 
		from app.t_mtr_rms tmr
		where rms_code='$code'
		";
		
		return $this->db->query($qry)->row();
	}
	public function getRmsVehicleDetail($code)
	{
		$qry ="
				select 
					rms_code,
					class_id 
				from app.t_mtr_rms_detail_class
				where rms_code='$code' and status=1
		";
		return $this->db->query($qry)->result();
	}
	public function insert_detail( $data, $where, $status=1 )
	{

		/**
			$data= array(
				"idData" =>$idData,
				"rmsCode" =>$rmsCode,
				"id_outlet" =>$idMemberImpact,
				"created_by"=>$this->session->userdata("username"),
				"created_on"=>date("Y-m-d H:i:s"),
			);
		*/

		$this->db->query("
			insert into  app.t_mtr_rms_outlet_b2b(
				 rms_code,
				 outlet_id, 
				 merchant_id,  
				 status, 
				 created_by, 
				 created_on
				 )
				 select 			
					'".$data['rmsCode']."' as rms_code,
					outlet_id ,
					merchant_id ,
					'".$status."' as status,
					'".$data['created_by']."' as created_by ,
					'".$data['created_on']."' as created_on
				 from
				 	app.t_mtr_outlet_merchant
			$where 
		");

	}

	public function getRmsChannel($code)
	{
		$qry ="
				select 
					rms_code,
					channel 
				from app.t_mtr_rms_channel
				where rms_code='$code' and status=1
		";
		return $this->db->query($qry)->result();
	}	
	public function getRmsMerchantDetail($code)
	{
		$qry ="
				select 
					rms_code,
					merchant_id,
					is_custom_outlet
				from app.t_mtr_rms_merchant_b2b
				where rms_code='$code' and status=1
		";
		return $this->db->query($qry)->result();
	}		
	public function select_data($table, $where)
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

	public function convertFormateDate($date)
	{
		$newDate = date("Y-m-d", strtotime(trim($date)));
		$returnDate = $newDate;
		if($newDate != $date)
		{
			$returnDate = date("Y-m-d");
		}

		return $returnDate;
	}


}
