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

class MemberBlockModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/memberBlock';
	}

  	public function dataList(){

		$dateFrom =$this->input->post('dateFrom');
		$dateTo =$this->input->post('dateTo');
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$status=$this->enc->decode($this->input->post('status'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$totalBooking = $this->input->post('totalBooking');
		$searchName=$this->input->post('searchName');
		$searchData=trim($this->input->post('searchData'));

		$ilike= str_replace(array('"',"'"), "", $searchData);
		
		$field = array(
			0 =>'id',
			1=>'full_name',
			2=>'email',
			3=>'phone_number',
			4=>'tb.total',
			5=>'tby.total',
			6=>'status',
			7=>'mb.created_on'
		);

		$order_column = $field[$order_column];

		$where = " WHERE id is not null and ( mb.created_on::date between '{$dateFrom}' and '{$dateTo}' ) ";
		// $where = " WHERE id is not null  ";

		// if($status != null)
		// {
		// 	$where .=" and is_activation=$status";
		// }

		$paramTotalBooking=1;
		if(!empty($totalBooking))
		{
			$paramTotalBooking=$totalBooking;
		}

		if($status<>"")
		{
			$where .=" and ( mb.status=$status ) ";
		}		

		if(!empty($searchData))
		{
			if($searchName=='email')
			{
				$where .=" and mb.email ilike '%{$ilike}%' ";
			}
			else
			{
				$where .=" and ( concat(mb.firstname,' ',mb.lastname) ilike '%{$ilike}%' )";
			}
		}

		$dateNow=date('Y-m-d');
		$lastDay=date('Y-m-d',strtotime("-$totalBooking days"));
		$sql = "
				SELECT 
						concat(mb.firstname,' ',mb.lastname) as full_name,
						mb.firstname,
						mb.lastname,
						mb.id,
						mb.phone_number,
						mb.email,
						mb.status,
						mb.blocking_expired,
						mb.created_on as tanggal_pendaftaran,
						tb.total as total_booking,
						tby.total as total_booking_bayar
					from app.t_mtr_member mb 
					left join (
							select count(created_by) as total ,created_by  from app.t_trx_booking
							where created_on::date between '{$lastDay}' and '{$dateNow}' and channel <>'ifcs'
							group by created_by
					) tb on mb.email=tb.created_by
					left join (
							select count(created_by) as total ,created_by  from app.t_trx_booking
							where created_on::date between  '{$lastDay}' and '{$dateNow}'  and channel <>'ifcs' and status=2
							group by created_by
					) tby on mb.email=tby.created_by
					
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
			$row->number = $i;

			$nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

			$row->id 	 = $this->enc->encode($row->id);
     		$change_status  = site_url($this->_module."/change_status/{$row->id}");

     		$row->actions  ="";

			$row->actions .= generate_button($this->_module, 'change_status', '<button onclick="showModal(\''.$change_status.'\')" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-pencil"></i></button> ');     		

     		if($row->total_booking==null)
     		{
     			$row->total_booking=0;	
     		}

     		if($row->total_booking_bayar==null)
     		{
     			$row->total_booking_bayar=0;	
     		}

     		if($row->status==0)
     		{
     			$row->status="<span class='label label-danger' >Tidak Aktif</span>";	
     		}
     		else if($row->status==1)
     		{
     			$row->status="<span class='label label-success' >Aktif</span>";	
     		}
     		else if($row->status=="-1")
     		{
     			$row->status="<span class='label label-danger' >Temp Banned</span>";		
     		}
     		else
     		{
     			$row->status="<span class='label label-danger' >Permanent Banned</span>";	
     		}

     		$row->tanggal_pendaftaran=format_date($row->tanggal_pendaftaran)." ".format_time($row->tanggal_pendaftaran);
     		$row->blocking_expired=empty($row->blocking_expired)?"":format_date($row->blocking_expired)." ".format_time($row->blocking_expired);

     		$row->no=$i;



			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'totalBooking'   => $paramTotalBooking,
			'data'           => $rows
		);
	}


	public function select_data($table, $where)
	{
		return $this->db->query("select * from $table $where");
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
