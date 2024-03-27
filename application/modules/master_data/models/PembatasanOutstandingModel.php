<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**

 *
 * @author     Adat <adatdt@gmail.com>
 * @copyright  2022
 *
 */

class PembatasanOutstandingModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/pembatasanOutstanding';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		
		$dateTo= $this->input->post('$dateTo');
		$dateFrom= $this->input->post('dateFrom');
		$shipClass=$this->enc->decode($this->input->post('shipClass'));

		
		$field = array(
			0 =>'id',
			1=>"start_date",
			2=>"end_date",
			3=>"ship_class_name",
			4=>"value",       
			5=>"status",
		);

		$order_column = $field[$order_column];

		$where = " WHERE ot.status not in (-5) ";

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

		if(!empty($shipClass))
		{
			$where .= " and ot.ship_class='".$shipClass."' ";
		}


		$sql 		   =$this->qry($where);
		$sqlCount 		   =$this->qryCount($where);

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
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change_active/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

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

			if(empty($row->ship_class_name))
			{
				$row->ship_class_name='Semua';
			}

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

	public function qry($where)
	{
		$qry="
			select 
				tmsc.name as ship_class_name, 
				ot.* 
			from app.t_mtr_limit_outstanding_transaction ot
			left join app.t_mtr_ship_class tmsc on ot.ship_class = tmsc.id 
			{$where}
		";

		return $qry;
	}

	public function qryCount($where)
	{
		$qry="
			select 
				count(ot.id) as count_data 
			from app.t_mtr_limit_outstanding_transaction ot
			left join app.t_mtr_ship_class tmsc on ot.ship_class = tmsc.id 
			{$where}
		";

		return $qry;
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

    public function checkOverlaps($startDate, $endDate, $shipClass,$id)
    {
		$where ="";

		if(!empty($id))
		{
			$where .=" and id <> '".$id."'";
		}

		if($shipClass !=0 )
		{
			$where .= "and ship_class in ('{$shipClass}',0)  ";
		}

        $qry="        
            select * from app.t_mtr_limit_outstanding_transaction  
            where 
			(
				(start_date between '{$startDate}' and '{$endDate}')
				or
				(end_date between '{$startDate}' and '{$endDate}')   
			)			
			and status = 1
			{$where}
        ";

        return $this->db->query($qry);
    }		


}
