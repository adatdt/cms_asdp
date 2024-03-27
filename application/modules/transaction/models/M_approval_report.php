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

class M_approval_report extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/approval_report';
        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
        $this->dbAction=$this->load->database("dbAction",TRUE);
	}

    public function dataList(){
    	$shift_id=$this->enc->decode($this->input->post('shift'));
    	$dateTo=$this->input->post('dateTo');
    	$dateFrom=$this->input->post('dateFrom');
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->dbView->escape_like_str($search['value'])));
		
		$field = array(
			0 =>'id',
			1 =>'report_code',
			2 =>'report_name',
			3 =>'report_date',
			4 =>'port_name',
			5 =>'shift_name',
			6 =>'ship_class_name',
			7 =>'approve_date_spv',
			8 =>'approve_date_manager',
		);

		$check_port=$this->select_data("app.t_mtr_identity_app")->row();
		 // jika identity app nya 0 (data cloude), maka usernya mengambil port berdasarkan session yang di ambil dari port user
        if($check_port->port_id==0)
        {
            if(empty($this->session->userdata('port_id')))
            {
                $port_id=$this->enc->decode($this->input->post('port'));
            }
            else
            {
                $port_id=$this->session->userdata('port_id');
            }
        }
        else
        {
            $port_id=$check_port->port_id;
        }

		$order_column = $field[$order_column];

		$where = " WHERE a.status!='-5' and report_date between '$dateFrom' and '$dateTo' ";

		if(!empty($port_id))
		{
			$where .=" and (a.port_id={$port_id}) ";
		}

		if(!empty($shift_id))
		{
			$where .=" and (a.shift_id={$shift_id}) ";
		}

		if(!empty($search['value']))
		{
			$where .="and (
							a.report_code ilike '%".$iLike."%' 
							or a.report_name ilike '%".$iLike."%' 
						)";
		}

		$sql 		   = "
							select e.name as ship_class_name,c.name as port_name, d.shift_name, b.report_name, a.* from app.t_trx_approval_report a
							left join app.t_mtr_report b on a.report_code=b.report_code 
							left join app.t_mtr_port c on a.port_id=c.id
							left join app.t_mtr_shift d on a.shift_id=d.id
							left join app.t_mtr_ship_class e on a.ship_class=e.id
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

		$check_user_group=$this->select_data("core.t_mtr_user"," where id=".$this->session->userdata('id'))->row();

		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;

			$row->id =$row->id;
     		$approve_url= site_url($this->_module."/approve_manager/{$id_enc}");
     		$delete_url = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions="";

     		// hardcord data yang muncul hanya yang usergroup 1 dan 27 (admin dan manager usaha)
     		// if($check_user_group->user_group_id==1 or $check_user_group->user_group_id==27 )
     		// {
     			if(empty($row->approve_date_manager))
     			{
     				$row->actions .=generate_button($this->_module,"edit",'<button class="btn btn-sm btn-warning" onclick="confirmationAction(\'Apakah Anda yakin approve data ini ?\', \''.$approve_url.'\')" title="approve"><i class="fa fa-plus"></i> Approve Manager</button>');	
     			}
     			else
     			{

				$row->actions .='<button class="btn btn-sm btn-warning" title="approve" disabled><i class="fa fa-plus"></i> Approve Manager</button>';     	
     			}
			// }
    
     		$row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
     		$row->approve_date_spv=empty($row->approve_date_spv)?"":format_dateTime($row->approve_date_spv);
     		$row->approve_date_manager=empty($row->approve_date_manager)?"":format_dateTime($row->approve_date_manager);
     		$row->report_date=empty($row->report_date)?"":format_date($row->report_date);

			$rows[] = $row;
			unset($row->id);

			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	public function select_data($table, $where='')
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
