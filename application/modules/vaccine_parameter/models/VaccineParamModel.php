<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Adat <adatdt@gmail.com>
 * @copyright  2021
 *
 */

class VaccineParamModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module   = 'vaccine_parameter/vaccineParam';
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
			2=>"assessment_type",
			3=>"assessment_type_test",
			4=>"start_date",
			5=>"end_date",
			6=>"min_age_detail",
			7=>"under_age_reason",
			8=>"pedestrian",
			9=>"vehicle",   
			10=>"web",
			11=>"mobile",
			12=>"ifcs",
			13=>"b2b",
			14=>"pos_vehicle",
			15=>"pos_passanger",
			16=>"mpos",
			17=>"vm",
			18=>"verifikator",
			19=>"web_cs",
			// 20=>"vaccine_active",
			20=>"test_covid_active",
			21=>"status",
		);


		$order_column = $field[$order_column];

		$where = " where status <> -5 ";

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
			if($searchName=="assessmentType")
			{
				$where .=" and assessment_type ilike '%".$iLike."%' ";
			}
			else if($searchName=="assessmentTestType")
			{
				$where .=" and  assessment_type_test ilike '%".$iLike."%' ";
			}
			else
			{
				$where .=" ";	
			}
		}		

		$sql = "
					select
					(
						select  
							array_agg(concat(da.min_age,'-', pd.vaccine_status,'-', pd.test_status,'-',tc.test_type)::TEXT) as min_age_detail
						from app.t_mtr_vaccine_param_detail_age da
						join app.t_mtr_vaccine_param_detail pd on da.vaccine_param_detail_code = pd.vaccine_param_detail_code
						join app.t_mtr_test_covid tc on pd.test_status = tc.order_value
						where da.status =1 and da.vaccine_param_id = tmvp.id and pd.status=1
					),
					tmvp.* 
					from app.t_mtr_vaccine_param tmvp
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
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0|app.t_mtr_vaccine_param'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1|app.t_mtr_vaccine_param'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions="";

			$row->detail_port=$this->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where vaccine_param_id=".$row->id )->result();
			$row->detail_vehicle=$this->select_data("app.t_mtr_vaccine_param_detail_vehicle", " where vaccine_param_id=".$row->id )->result();

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



			if($row->vehicle=='t')
			{
				$row->vehicle='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';   
			}
			else
			{
				$row->vehicle='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>'; 
			}   

			if($row->web=='t')
			{
				$row->web='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->web='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}

			if($row->mobile=='t')
			{
				$row->mobile='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->mobile='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}

			if($row->ifcs=='t')
			{
				$row->ifcs='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->ifcs='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';				
			}

			if($row->b2b=='t')
			{
				$row->b2b='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->b2b='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}

			if($row->pedestrian=='t')
			{
				$row->pedestrian='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->pedestrian='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}			
			if($row->vaccine_active=='t')
			{
				$row->vaccine_active='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->vaccine_active='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}

			if($row->test_covid_active=='t')
			{
				$row->test_covid_active='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->test_covid_active='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}
			
			if($row->pos_vehicle=='t')
			{
				$row->pos_vehicle='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->pos_vehicle='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}

			if($row->pos_passanger=='t')
			{
				$row->pos_passanger='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->pos_passanger='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}

			if($row->mpos=='t')
			{
				$row->mpos='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->mpos='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}

			if($row->vm=='t')
			{
				$row->vm='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}			
			else
			{
				$row->vm='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}

			if($row->verifikator=='t')
			{
				$row->verifikator='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->verifikator='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}
			
			if($row->web_cs=='t')
			{
				$row->web_cs='<span class="label label-success"><i class="fa fa-check-circle"></i></span>';
			}
			else
			{
				$row->web_cs='<span class="label label-danger"><i class="fa fa-times-circle"></i></span>';
			}
						


			$row->start_date= format_date($row->start_date)." ".format_time($row->start_date);
			$row->end_date= format_date($row->end_date)." ".format_time($row->end_date);

			$explode = explode(",",str_replace(array("{","}"),"",$row->min_age_detail));

			$minUsiaDetail= "";

			if(!empty($explode))
			{
				asort($explode);

				$brUp="";
				foreach($explode as $explode2 )
				{
					$explodDetailUsia = explode("-",$explode2);
					
					/*
					$explodDetailUsia[0] // usia
					$explodDetailUsia[1] // id vaksin
					$explodDetailUsia[2] // id test covid
					$explodDetailUsia[3] // test covid type
					*/
					

					if(count($explodDetailUsia) > 1)
					{

						if($brUp != $explodDetailUsia[0] )
						{
							$breakLine ="<br>";
						}
						else
						{
							$breakLine ="";
						}


						$minUsiaDetail .= $breakLine."- Min Usia : ".$explodDetailUsia[0]."Thn,";
						if($explodDetailUsia[1] == 0)
						{
							$minUsiaDetail .= " Tidak Vaksin, ";
						}
						else
						{
							$minUsiaDetail .= " Vaksin ke ".$explodDetailUsia[1].",";
						}
		
						if($explodDetailUsia[2] == 0)
						{
							$minUsiaDetail .= " Tidak Wajib Tes! ";
						}
						else
						{
							$minUsiaDetail .= " Harus Tes ".$explodDetailUsia[3]."!";
						}
						
						$minUsiaDetail .="<br>" ;

						$brUp = $explodDetailUsia[0];
					}
					
				}
			}

			$row->minUsiaDetail=$minUsiaDetail;
						
     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

			$rows[] = $row;
			unset($row->assignment_code);

			$i++;
		}

		// exit;
		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	public function getPortDetail(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$vaccineParamId=$this->input->post('vaccineParamId');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		// $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		$iLike        = trim(str_replace(array("'",'"'),"",($search['value'])));

		
		$field = array(
			0 =>'id',
			1 =>"port_name",                    
			2 =>"status",			
		);

		$order_column = $field[$order_column];

		$where = " where dp.vaccine_param_id={$vaccineParamId} and dp.status !='-5' ";

		if(!empty($search['value']))
		{
			$where .=" and p.name  ilike '%".$iLike."%'  ";
		}

		$sql 		   =$this->qryDetailPort()." ".$where;

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
			$nonaktif    = site_url($this->_module."/action_change_detail_port/".$this->enc->encode($row->id.'|0|app.t_mtr_vaccine_param_detail_port'));
     		$aktif       = site_url($this->_module."/action_change_detail_port/".$this->enc->encode($row->id.'|1|app.t_mtr_vaccine_param_detail_port'));
			
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");

     		$row->actions="";
	

			 if($row->status == 1)
			 {
				 $row->status   = success_label('Aktif');
				 if($row->param_status==1)
				 {
					 $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="myData.confirmationAction2(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\',\'portDataTables_'.$row->vaccine_param_id.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
				 }
			 }
			 else
			 {
				 $row->status   = failed_label('Tidak Aktif');

				 if($row->param_status==1)
				 {
				 	$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="myData.confirmationAction2(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\',\'portDataTables_'.$row->vaccine_param_id.'\')" title="aktifkan"> <i class="fa fa-check"></i> </button> ');
				 }
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

	public function qryDetailPort()
	{
		$qry="SELECT
		p.name as port_name,
		tmvp.assessment_type ,
		tmvp.status as param_status,
		dp.*
		from app.t_mtr_vaccine_param_detail_port dp 
		left join app.t_mtr_port p on dp.port_id =p.id
		left join app.t_mtr_vaccine_param tmvp on dp.vaccine_param_id=tmvp.id
		";
		return $qry;
	}


	public function getVehicleDetail(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$vaccineParamId=$this->input->post('vaccineParamId');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		// $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		$iLike        = trim(str_replace(array("'",'"'),"",($search['value'])));

		
		$field = array(
			0 =>'id',
			1 =>'vehicle_class_name',
			2 =>'status'
		);

		$order_column = $field[$order_column];

		$where = " where dp.vaccine_param_id={$vaccineParamId} and dp.status !='-5' ";

		if(!empty($search['value']))
		{
			$where .=" and p.name  ilike '%".$iLike."%'  ";
		}

		$sql 		   = $this->qryDetailVehicle()." ".$where;

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
			$nonaktif    = site_url($this->_module."/action_change_detail_vehicle/".$this->enc->encode($row->id.'|0|app.t_mtr_vaccine_param_detail_vehicle'));
     		$aktif       = site_url($this->_module."/action_change_detail_vehicle/".$this->enc->encode($row->id.'|1|app.t_mtr_vaccine_param_detail_vehicle'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete_detail_vehicle/{$id_enc}");

     		$row->actions="";
	
			if($row->status == 1)
			{
				 $row->status   = success_label('Aktif');
				 if($row->param_status==1)
				 {
				 	$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="myData.confirmationAction2(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\',\'vehicleDataTables_'.$row->vaccine_param_id.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
				 }
			}
			else
			{
				 $row->status   = failed_label('Tidak Aktif');
				 if($row->param_status==1)
				 {
				 	$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="myData.confirmationAction2(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\',\'vehicleDataTables_'.$row->vaccine_param_id.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
				 }
			}
			
			
			

     		$row->no=$i;

			if($row->param_status==1)
			{
     			$row->actions .=generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="myData.confirmationAction2(\'Apakah Anda yakin menghapus data ini ?\', \''. $delete_url.'\',\'vehicleDataTables_'.$row->vaccine_param_id.'\')" title="Hapus"><i class="fa fa-trash-o"></i>  </button> ');
			}
			//  generate_button_new($this->_module, 'delete', $delete_url);

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

	public function qryDetailVehicle()
	{
		$qry="
			SELECT 
				p.name as vehicle_class_name,
				tmvp.assessment_type ,
				tmvp.status as param_status,
				dp.*
			from app.t_mtr_vaccine_param_detail_vehicle dp 
			left join app.t_mtr_vehicle_class p on dp.vehicle_class_id =p.id
			left join app.t_mtr_vaccine_param tmvp on dp.vaccine_param_id =tmvp.id
		";

		return $qry;
	}

	public function getPortDetail_old($vaccineParamId)
	{
		$sql=" 
			SELECT 
				p.name as port_name,
				dp.*
			from app.t_mtr_vaccine_param_detail_port dp 
			left join app.t_mtr_port p on dp.port_id =p.id
			where dp.vaccine_param_id={$vaccineParamId} and dp.status !='-5'
			order by dp.id desc
		
		";

		$data=$this->db->query($sql)->result();

		$returnData=array();

		foreach($data as $key=>$value)
		{
			$value->status   = success_label('Aktif');

			if($value->status != 1)
			{
				$value->status   = failed_label('Tidak Aktif');
			}

			$returnData[]=$value;
		}

		return $returnData;
	}

	public function getAssesmentType($where="")
	{
		$qry="
			SELECT
				distinct(TYPE)  
			from 
				app.t_mtr_assessment_param tmap
				{$where} 	
				ORDER by type asc
		
		";

		return $this->db->query($qry)->result();
	}

	public function getPortDetailVaccine($id)
	{
		$qry= "
				select 
					a.port_id ,
					b.name as port_name
				from
				app.t_mtr_vaccine_param_detail_port a
				join app.t_mtr_port b on a.port_id=b.id
				where vaccine_param_id={$id} and a.status=1
				order by port_id  asc
			";

		 $data=$this->db->query($qry)->result();	
		 
		 $portId=array();
		 $portName=array();
		 if($data)
		 {
			foreach($data as $key=>$value )
			{
				$portId[]=$value->port_id;
				$portName[]=$value->port_name;
			}
		 }
		 
		 return $returnData= array("portId"=>$portId,
		 							"portName"=>$portName);
	}

	public function getVehicleDetailVaccine($id)
	{
		$qry= "
				select 
					a.vehicle_class_id,
					b.name as vehicle_class_name
				from
				app.t_mtr_vaccine_param_detail_vehicle a
				join app.t_mtr_vehicle_class b on a.vehicle_class_id=b.id
				where vaccine_param_id={$id} and a.status=1
				order by vehicle_class_id  asc
			";

		$data= $this->db->query($qry)->result();
		
		$vehicleClassId=array();
		$vehicleClassName=array();
		if($data)
		{
		   foreach($data as $key=>$value )
		   {
			   $vehicleClassId[]=$value->vehicle_class_id;
			   $vehicleClassName[]=$value->vehicle_class_name;
		   }
		}
		
		return $returnData= array("vehicleClassId"=>$vehicleClassId,
								"vehicleClassName"=>$vehicleClassName );
	}
	public function getDetailMinAge($id)
	{
		$qry="SELECT
				da.min_age,
				da.vaccine_param_detail_code,
				pd.id as id_vaccine_test,
				pd.vaccine_status,
				pd.test_status,
				tc.test_type
			from app.t_mtr_vaccine_param_detail_age da
			join app.t_mtr_vaccine_param tmvp on da.vaccine_param_id = tmvp.id
			join app.t_mtr_vaccine_param_detail pd on da.vaccine_param_detail_code = pd.vaccine_param_detail_code
			join app.t_mtr_test_covid tc on pd.test_status = tc.order_value
				where da.status =1
				and da.vaccine_param_id = tmvp.id 
				and pd.status=1
				and da.vaccine_param_id = $id
			order by da.id, pd.id  asc
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


}
