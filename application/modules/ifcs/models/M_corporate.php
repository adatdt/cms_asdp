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

class M_corporate  extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'ifcs/corporate';
	}

    public function dataList(){
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
			1 =>'corporate_code',
			2 =>'corporate_name',
			3 =>'phone',
			4 =>'email',
			5 =>'corporate_address',
			6 =>'description',
			7 =>'pic_name',
			8 =>'pic_position',
			9 =>'pic_email',
			10 =>'pic_phone',
			11 =>'status',
			12=>'status_contract'


		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) ";


		if(!empty($search['value']))
		{
			$where .="and (
					a.corporate_code ilike '%".$iLike."%'
					or a.corporate_name ilike '%".$iLike."%'
					or a.phone ilike '%".$iLike."%'
					or a.email ilike '%".$iLike."%'
					or a.corporate_address ilike '%".$iLike."%'
					or a.pic_name ilike '%".$iLike."%'
					or a.pic_email ilike '%".$iLike."%'
					or a.pic_phone ilike '%".$iLike."%'
					or a.pic_position ilike '%".$iLike."%'
					or b.description ilike '%".$iLike."%'
					)";
		}

		$sql 		   = "
							SELECT b.description, 
							e.count_contract_active, c.count_will_pass, d.count_contract_exist,
							 (
							  case 
							  when e.count_contract_active >0 
							  then 
							  	case 
							  	when c.count_will_pass > 0 then 'akan_habis_kontrak'
							  	else
							  		case 
							  		when  d.count_contract_exist is null  then 'habis_kontrak'
							  		else null
							  		end
							  	end
							  else
							  null
							  end
							 ) as status_contract,

							a.* from app.t_mtr_corporate_ifcs a
							left join app.t_mtr_business_sector_ifcs b on a.business_sector_code=b.business_sector_code
							left join 
							(
								SELECT  count(end_date) as count_will_pass, corporate_code  from app.t_mtr_corporate_agreement
												where is_active=1 
												and (start_date <= now() and end_date - interval '2 month' <= now() )
												and end_date >= now()
												group by corporate_code
							) c
							 on a.corporate_code=c.corporate_code
							 left join 
							 (
							 	SELECT count(end_date) as count_contract_exist , corporate_code  from app.t_mtr_corporate_agreement
								where is_active=1
								and (start_date <= now() and end_date >= now() )
								group by corporate_code
							 )d
							 on a.corporate_code=d.corporate_code
							  left join 
							 (
							 	SELECT count(end_date) as count_contract_active , corporate_code  from app.t_mtr_corporate_agreement
								where is_active=1 and status <>'-5' 
								group by corporate_code
							 )e
							 on a.corporate_code=e.corporate_code
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
			$id_enc=$this->enc->encode($row->id);
			$corporate_code_enc=$this->enc->encode($row->corporate_code);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0|app.t_mtr_corporate_ifcs'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1|app.t_mtr_corporate_ifcs'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
     		$detail_url  = site_url($this->_module."/detail/{$corporate_code_enc}");

     		$row->actions  =" ";

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

     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

     		$date_now=date("Y-m-d");

     		//cari kontrak yang aktif
     		$check_agreement=$this->select_data(" app.t_mtr_corporate_agreement ", " where corporate_code='{$row->corporate_code}'
     		and is_active=1 and status <>'-5' ");


     		if($row->status_contract=='akan_habis_kontrak')
     		{
     			$row->notif=warning_label("Kontrak Akan Habis");
     		}
     		else if($row->status_contract=='habis_kontrak')
     		{
     			$row->notif=failed_label("Habis Kontrak");	
     		}
     		else
     		{
     			$row->notif="";
     		}



     		// check apakah dia punya akses detail
     		// $row->actions  .= generate_button_new($this->_module, 'detail', $detail_url);
			$row->actions .= generate_button($this->_module, 'detail', '<a class="btn btn-sm btn-primary" href="'.$detail_url.'" title="Detail"><i class="fa fa-search-plus"></i></a>');

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

    public function dataListBranch(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$corporate_code=$this->input->post('corporate_code');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
			1 =>'corporate_code',
			2 =>'corporate_name',
			3 =>'branch_code',
			4 =>'description',
			5 =>'status',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) and a.corporate_code='{$corporate_code}' ";

		if(!empty($search['value']))
		{
			$where .="and (
					b.corporate_code ilike '%".$iLike."%'
					or b.corporate_name ilike '%".$iLike."%'
					or a.branch_code ilike '%".$iLike."%'
					or a.description ilike '%".$iLike."%'
					)";
		}

		$sql 		   = "
							SELECT b.corporate_name, b.corporate_code, a.* from  app.t_mtr_branch_ifcs a
							left join app.t_mtr_corporate_ifcs b on a.corporate_code=b.corporate_code
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
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0|app.t_mtr_branch_ifcs'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1|app.t_mtr_branch_ifcs'));

			$row->id =$row->id;

     		$row->actions  =" ";

			if($row->status == 1){
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction2(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction2(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
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


    public function dataListContract(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$corporate_code=$this->input->post('corporate_code');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'id',
			1 =>'agreement_number',
			2 =>'corporate_name',
			3 =>'corporate_code',
			4 =>'start_date',
			5 =>'end_date',
			6 =>'is_active',
			7 =>'file_upload',
			8 =>'order_number',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) and a.corporate_code='{$corporate_code}' ";

		if(!empty($search['value']))
		{
			$where .="and (
					a.corporate_code ilike '%".$iLike."%'
					or a.agreement_code ilike '%".$iLike."%'
					or a.agreement_number ilike '%".$iLike."%'
					or b.corporate_name ilike '%".$iLike."%'
					)";
		}

		$sql = "
					SELECT b.corporate_name, a.* from app.t_mtr_corporate_agreement a
					left join app.t_mtr_corporate_ifcs b on a.corporate_code=b.corporate_code
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
			$id_enc=$this->enc->encode($row->id);
			$code_enc=$this->enc->encode($row->corporate_code);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/activation_contract/".$this->enc->encode($row->id.'|0|app.t_mtr_corporate_agreement'));
     		$aktif       = site_url($this->_module."/activation_contract/".$this->enc->encode($row->id.'|1|app.t_mtr_corporate_agreement'));
     		$edit_url 	 = site_url($this->_module."/edit_contract/{$id_enc}");
     		$detail_url  = site_url($this->_module."/detail_contract/{$row->agreement_code}");

			$row->id =$row->id;

			$check_transaction=$this->get_transaction_ifcs($row->corporate_code,$row->start_date,$row->end_date)->num_rows();

     		$row->actions  ="";


 			if($row->is_active == 1)
 			{
				$row->is_active   = success_label('Contract Aktif');
			}
			else
			{
				$row->is_active   = failed_label('Contract Non Active');
				$row->actions .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan kontrak"> <i class="fa fa-check"></i> </button> ');				
			}

            if(empty($row->file_upload))
            {
            	$row->file_pdf="";
            }
            else
            {

            	$file_array=explode("/",$row->file_upload);
            	$file_key=max(array_keys($file_array));

            	$row->file_pdf="<a href='".base_url($row->file_upload)."' target='_blank' >{$file_array[$file_key]}</a>";	
            }


			$row->actions .= generate_button_new($this->_module, 'detail', $detail_url);


			$row->end_date=empty($row->end_date)?"":format_date($row->end_date);
			$row->start_date=empty($row->start_date)?"":format_date($row->start_date);

     		$row->no=$i;

			$rows[] = $row;
			// unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}	


   public function detailContract(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		// $agreement_code=$this->input->post('agreement_code');
		$corporate_code=$this->input->post('corporate_code');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$field = array(
			0 =>'id',
			1 =>'corporate_code',
			2 =>'corporate_name',
			3 =>'contract_number',
			4 =>'reward_code',
			5 =>'start_date',
			6 =>'end_date',
			7 =>'adjustment_date',
			8 =>'start_date_reward',
			9 =>'end_date_reward',
			10 =>'reward',
			11 =>'total_transaction',
			12=>'adjustment_transaction',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) and a.corporate_code='{$corporate_code}' ";

		if(!empty($search['value']))
		{
			$where .="and (
						a.corporate_code ilike '%".$iLike."%'
						or a.reward_code ilike '%".$iLike."%'
						or c.corporate_name ilike '%".$iLike."%'
					)";
		}

		$sql = "
						SELECT b.agreement_number as contract_number, c.corporate_name ,a.* from app.t_mtr_corporate_agreement_detail a
						left join app.t_mtr_corporate_agreement b on a.agreement_code=b.agreement_code
						left join app.t_mtr_corporate_ifcs c on a.corporate_code=c.corporate_code 
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
			$id_enc=$this->enc->encode($row->id);
			$code_enc=$this->enc->encode($row->corporate_code);
			$row->number = $i;
     		$edit_url 	 = site_url($this->_module."/edit_reward/{$id_enc}");
     		$detail_url  = site_url($this->_module."/detail_contract/{$id_enc}");


            $row->action="";
            // if($row->start_date_reward<=date("Y-m-d") and $row->end_date_reward>=date("Y-m-d") and $row->status==2)
            if($row->start_date_reward<=date("Y-m-d") and $row->end_date_reward >=date("Y-m-d"))
            {

            	$row->action .=generate_button_new($this->_module,"edit",$edit_url); 
            }


            $row->start_date=empty($row->start_date)?"":format_date($row->start_date);
            $row->end_date=empty($row->end_date)?"":format_date($row->end_date);
            $row->adjustment_date=empty($row->adjustment_date)?"":format_date($row->adjustment_date);
            $row->start_date_reward=empty($row->start_date_reward)?"":format_date($row->start_date_reward);  
            $row->end_date_reward=empty($row->end_date_reward)?"":format_date($row->end_date_reward);  
            $row->reward=empty($row->reward)?"0":idr_currency($row->reward); 
            $row->total_reward=empty($row->total_reward)?"0":idr_currency($row->total_reward); 
            $row->total_transaction=empty($row->total_transaction)?"0":idr_currency($row->total_transaction); 
            $row->adjustment_transaction=empty($row->adjustment_transaction)?"0":idr_currency($row->adjustment_transaction); 


			$row->id =$row->id;

     		$row->no=$i;

			$rows[] = $row;
			// unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}	

   public function detailContract2(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$agreement_code=$this->input->post('agreement_code');
		$corporate_code=$this->input->post('corporate_code');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$field = array(
			0 =>'id',
			1 =>'corporate_code',
			2 =>'corporate_name',
			3 =>'contract_number',
			4 =>'reward_code',
			5 =>'start_date',
			6 =>'end_date',
			7 =>'adjustment_date',
			8 =>'start_date_reward',
			9 =>'end_date_reward',
			10 =>'reward',
			11 =>'total_transaction',
			12=>'adjustment_transaction',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) and a.agreement_code='{$agreement_code}' ";

		if(!empty($search['value']))
		{
			$where .="and (
						a.corporate_code ilike '%".$iLike."%'
						or a.reward_code ilike '%".$iLike."%'
						or c.corporate_name ilike '%".$iLike."%'
					)";
		}

		$sql = "
						SELECT b.agreement_number as contract_number, c.corporate_name ,a.* from app.t_mtr_corporate_agreement_detail a
						left join app.t_mtr_corporate_agreement b on a.agreement_code=b.agreement_code
						left join app.t_mtr_corporate_ifcs c on a.corporate_code=c.corporate_code 
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
			$id_enc=$this->enc->encode($row->id);
			$code_enc=$this->enc->encode($row->corporate_code);
			$row->number = $i;
     		$edit_url 	 = site_url($this->_module."/edit_reward/{$id_enc}");
     		$detail_url  = site_url($this->_module."/detail_contract/{$id_enc}");


            $row->action="";
            // if($row->start_date_reward<=date("Y-m-d") and $row->end_date_reward>=date("Y-m-d") and $row->status==2)
            if($row->start_date_reward<=date("Y-m-d") and $row->end_date_reward >=date("Y-m-d"))
            {

            	$row->action .=generate_button_new($this->_module,"edit",$edit_url); 
            }


            $row->start_date=empty($row->start_date)?"":format_date($row->start_date);
            $row->end_date=empty($row->end_date)?"":format_date($row->end_date);
            $row->adjustment_date=empty($row->adjustment_date)?"":format_date($row->adjustment_date);
            $row->start_date_reward=empty($row->start_date_reward)?"":format_date($row->start_date_reward);  
            $row->end_date_reward=empty($row->end_date_reward)?"":format_date($row->end_date_reward);  
            $row->reward=empty($row->reward)?"0":idr_currency($row->reward); 
            $row->total_reward=empty($row->total_reward)?"0":idr_currency($row->total_reward); 


			$row->id =$row->id;

     		$row->no=$i;

			$rows[] = $row;
			// unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}		

	public function get_max_contract($where)
	{
		return $this->db->query("
			SELECT max (reward_code) as reward_code, corporate_code from app.t_mtr_corporate_ifcs_detail
			{$where}
			group by corporate_code
		");
	}

	public function get_max_contract2($where)
	{
		return $this->db->query("
			SELECT max (reward_code) as reward_code, corporate_code from app.t_mtr_corporate_agreement_detail
			{$where}
			group by corporate_code
		");
	}

	public function get_min_contract2($where)
	{
		return $this->db->query("
			SELECT min (reward_code) as reward_code, corporate_code from app.t_mtr_corporate_agreement_detail
			{$where}
			group by corporate_code
		");
	}


	public function get_last_agreement($corporate_code)
	{
		return $this->db->query("
			SELECT max(agreement_code) as agreement_code, corporate_code  from app.t_mtr_corporate_agreement
			where corporate_code='{$corporate_code}'
			group by corporate_code
		")->row();
	}	




	public function get_transaction_ifcs($corporate_code,$start_date,$end_date)
	{
		return $this->db->query(
			"SELECT * from app.t_trx_invoice
			where to_char(invoice_date,'yyyy-mm-dd') between '{$start_date}' and '{$end_date}'
			and upper(channel)=upper('ifcs')
			and email in ( 
				select email from app.t_mtr_member_ifcs 
				where corporate_code='{$corporate_code}'
			)"
		);
	}	

	public function get_corporate_detail($id)
	{

		return $this->db->query("
			SELECT  c.agreement_number as contract_number, b.corporate_name, a.* from app.t_mtr_corporate_agreement_detail a
			left join app.t_mtr_corporate_ifcs b on a.corporate_code=b.corporate_code
			left join app.t_mtr_corporate_agreement c on a.corporate_code=c.corporate_code
			where a.id={$id}
		");
	}

	public function select_data($table, $where)
	{
		return $this->db->query("select * from $table $where");
	}

	public function get_max($field, $table, $where)
	{
		return $this->db->query("select max ($field) as $field from $table $where")->row();
	}

	public function get_min($field, $table, $where)
	{
		return $this->db->query("select min ($field) as $field from $table $where")->row();
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
