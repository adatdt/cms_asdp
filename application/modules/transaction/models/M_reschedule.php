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

class M_reschedule extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/reschedule';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$service = $this->enc->decode($this->input->post('service'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchData=$this->input->post('searchData');
		$searchName=$this->input->post('searchName');
		$iLike  = trim(strtoupper($this->db->escape_like_str($searchData)));


		$field = array(
			0 =>'id',
			1 =>'created_on',
			2 =>'booking_code',
			3 =>'new_booking_code',
			4 =>'reschedule_code',
			5 =>'service_name',
			6 =>'old_depart_date',
			7 =>'old_depart_time_start',
			8 =>'new_depart_date',
			9 =>'new_depart_time_start',
			10 =>'amount',

		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE a.status is not null and a.created_on >= '". $dateFrom . "' and a.created_on < '" . $dateToNew . "'";
		// $where = " WHERE a.status is not null ";

		// if (!empty($dateTo) and !empty($dateFrom))
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";
		// }
		// else if(empty($dateFrom) and !empty($dateTo))
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateTo."' and '".$dateTo."' )";	
		// }
		// else if (!empty($dateFrom) and empty($dateTo))
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateFrom."' )";	
		// }
		// else
		// {
		// 	$where .="and (to_char(a.created_on,'yyyy-mm-dd') '".date("Y-m-d")."' and '".date('Y-m-d',strtotime("-7 days"))."' )";		
		// }

		if(!empty($service))
		{
			$where .="and (c.id=".$service.")";
		}

		// if (!empty($search['value'])){
		// 	$where .="and (a.booking_code ilike '%".$iLike."%' or a.reschedule_code ilike '%".$iLike."%' 
		// 					or a.new_booking_code ilike '%".$iLike."%'  )";	
		// }

		if(!empty($searchData))
        {
            if($searchName=='rescheduleCode')
            {
                $where .=" and (a.reschedule_code ilike '%".$iLike."%' ) ";
            }
            else if($searchName=='bookingCode')
            {
                $where .=" and (a.booking_code ilike '%".$iLike."%' ) ";
            }
            else
            {
                $where .=" and (a.new_booking_code ilike '%".$iLike."%' ) ";
            }
        }

		$sql 		   = "
							SELECT 
								name as service_name, 
								b.trans_number, 
								b.depart_date as new_depart_date,
								b.depart_time_start as new_depart_time_start,
								b.depart_time_end as new_depart_time_end,
								d.depart_date as old_depart_date,
								d.depart_time_start as old_depart_time_start,
								d.depart_time_end as old_depart_time_end,								
								a.* from app.t_trx_reschedule a
							left join app.t_trx_booking b on a.new_booking_code=b.booking_code
							left join app.t_mtr_service c on b.service_id=c.id
							left join app.t_trx_booking d on a.booking_code=d.booking_code
							$where
						 ";

		$sqlCount = "SELECT
						count(a.id) as countdata 
					from app.t_trx_reschedule a
							left join app.t_trx_booking b on a.new_booking_code=b.booking_code
							left join app.t_mtr_service c on b.service_id=c.id
							left join app.t_trx_booking d on a.booking_code=d.booking_code
							$where";


		$queryCount         = $this->db->query($sqlCount)->row();
    $records_total 			= $queryCount->countdata;
		// $query         = $this->db->query($sql);
		// $records_total = $query->num_rows();
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
			$nonaktif = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
     		$aktif = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

			$id=$this->enc->encode($row->id);
			$edit_url 	 = site_url($this->_module."/edit/{$id}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id}");

     		$row->actions="";

     		if ($row->status==1)
     		{
     			$row->status=success_label("Aktif");
     			$row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
     		}
     		else
     		{
     			$row->status=failed_label("Tidak Aktif");	
				$row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="aktifkan"> <i class="fa fa-check"></i> </button> ');
     		}

     		$row->actions.= generate_button_new($this->_module, 'edit', $edit_url);

     		$row->new_time=$row->new_depart_time_start." - ".$row->new_depart_time_start;
     		$row->old_time=$row->old_depart_time_start." - ".$row->old_depart_time_start;

     		
     		
     		$row->amount=idr_currency($row->amount);
     		$row->created_on=format_dateTimeHis($row->created_on);
     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
     		
     		$row->old_depart_date=format_date($row->old_depart_date);
			$row->new_depart_date=format_date($row->new_depart_date);;

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


	public function select_data($table, $where="")
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
