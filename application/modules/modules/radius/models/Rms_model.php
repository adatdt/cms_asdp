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

class Rms_model extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'radius/rms';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$dateFrom =$this->db->escape_str($this->input->post("dateFrom"));
		$dateTo =$this->db->escape_str($this->input->post("dateTo"));
		$reservation_date =$this->db->escape_str($this->input->post("reservation_date"));
		$port =$this->enc->decode($this->input->post("port"));
		$serchData=$this->db->escape_str(trim($this->input->post("searchData")));
		$searchName=$this->db->escape_str(trim($this->input->post("searchName")));
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',

		);

		$order_column = $field[$order_column];
		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		// $where = " WHERE status not in (-5) and (start_date>='".$dateFrom."' and end_date<='".$dateTo."')  ";
		$where = " WHERE status not in (-5) and created_on >= ".$this->db->escape($dateFrom). " and created_on < " .$this->db->escape($dateToNew)." and type=1 ";

		if(!empty($port))
		{
			$where .="and  port_id =  " .$this->db->escape($port)." ";
		}

		if(!empty($serchData))
		{
			if($searchName == "rmsCode")
			{
				$where .="and rms_code =  '" .$serchData."' ";
			}
		}
		if (!empty($reservation_date)) {
			$where .= "and reservation_date::date=".$this->db->escape($reservation_date)."";
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
		$radiusTypeIndex[1]="KM";
		$radiusTypeIndex[2]="M";

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
			$radiusType = @$radiusTypeIndex[$row->radius_type];

			$row->port_name = @$dataPort[$row->port_id];
			// $row->radius = $row->radius." ".$radiusType;
			$row->view_map ="<a onclick=myData.showModal('".$row->latitude."','".$row->longitude."','".$row->rms_code."') id='view_".$row->rms_code."' data-radius='".$radius."' data-radiusType='".$radiusType."' > Lihat Map </a>";
			$row->radius = $row->radius." ".@$radiusTypeIndex[$row->radius_type];
     		$row->actions  =" ";
			$row->rms_code_enc=$rms_code_enc;

			$row->btn_add_detail_user_exp ="";
			$row->btn_add_detail_user_ifcs_exp ="";
			if($row->status == 1){
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
	
				if($channelWeb > 0)
				{
					$btn_add_detail_user_exp = '<div class="col-md-6" >
					<div class="pull-right btn-add-padding add-user-email" id="add-user-web-email"><button type="button" class="btn btn-sm btn-warning pull-right" data-backdrop="static" data-toggle="modal" data-target="#modalUserWebDetail" data-id="'.$rms_code_enc.'" id="add_detail_web_'.$row->id.'" ><i class="fa fa-plus"  ></i>Tambah</button></div></div>';
	
					$row->btn_add_detail_user_exp .= generate_button($this->_module, 'delete', $btn_add_detail_user_exp) ;
				}
					
				if($channelIfcs > 0)
				{
					$btn_add_detail_user_ifcs_exp = '<div class="col-md-6" >
					<div class="pull-right btn-add-padding add-user-email" id="add-user-web-email"><button type="button" class="btn btn-sm btn-warning pull-right" data-backdrop="static" data-toggle="modal" data-target="#modalUserIfcsDetail" data-id="'.$rms_code_enc.'" id="add_detail_ifcs_'.$row->id.'" ><i class="fa fa-plus"  ></i>Tambah</button></div></div>';
					
					$row->btn_add_detail_user_ifcs_exp .= generate_button($this->_module, 'delete', $btn_add_detail_user_ifcs_exp) ;
				}		

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

			$row->layanan = "";

			if($row->is_vehicle =="t")
			{
				$row->layanan .= "- Kendaraan <br>";
			}

			if($row->is_pedestrian =="t")
			{
				$row->layanan .= "- Pejalan Kaki <br>";
			}		

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

    public function detailUserWebExp(){
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
			1 =>'email',

		);

		$order_column = $field[$order_column];

		$where = " WHERE a.rms_code='".$rmsCode."' and a.status not in (-5) ";

		if(!empty($search['value']))
		{
			$where .="and  a.account_id ='".$iLike."'"; 
		}

		$sql 		   = "
							select 
								a.*,
								b.status as status_rms 
							from
								app.t_mtr_exception_rms a
								left join  app.t_mtr_rms b on a.rms_code = b.rms_code  
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
			$id_enc = $this->enc->encode($row->id."|app.t_mtr_exception_rms|".$row->rms_code);
			$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
			
			if($row->status_rms == 1){
				$btn_delete ='<button class="btn btn-sm btn-danger" onclick="myData.confirmationAction('."'".'Apakah Anda yakin menghapus data ini ?'."'".','."'".$delete_url ."'".' )" title="Hapus"> <i class="fa fa-trash-o"></i> </button>';
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
    public function detailUserIfcsExp(){
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
			1 =>'email',

		);

		$order_column = $field[$order_column];

		$where = " WHERE a.rms_code='".$rmsCode."' and a.status not in (-5) ";

		if(!empty($search['value']))
		{
			$where .="and  a.account_id ='".$iLike."'"; 
		}

		$sql 		   =  " 
							select 
								a.*,
								b.status as status_rms 
							from
								app.t_mtr_exception_rms_ifcs a
								left join  app.t_mtr_rms b on a.rms_code = b.rms_code  
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
			
			if($row->status_rms == 1){
				$btn_delete ='<button class="btn btn-sm btn-danger" onclick="myData.confirmationAction('."'".'Apakah Anda yakin menghapus data ini ?'."'".','."'".$delete_url ."'".',2 )" title="Hapus"> <i class="fa fa-trash-o"></i> </button>';
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
			0 =>'a.id',
			1 =>'name',

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
	
	public function checkOverlaps($startDate, $endDate,$port, $rmsCode="")
    {
		$where ="";

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
			and status = 1 and type=1 and port_id = '{$port}'
			{$where}
        ";

		// die($qry); exit;
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
