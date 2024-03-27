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

class M_close_balance_spv extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/close_balance_spv';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom     = date("Y-m-d", strtotime(trim($this->input->post('dateFrom'))));
		$dateTo       = date("Y-m-d", strtotime(trim($this->input->post('dateTo'))));
		$shift = $this->enc->decode($this->input->post('shift'));

		$team_name= $this->input->post('team');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));

		
		$field = array(
			0 =>'assignment_date',
			1 =>'assignment_date',
			2 =>'assignment_code',
			3 =>'port_name',
			4 =>'shift_name',
			5 =>'nama_regu',
			6 =>'full_name',
			7 =>'username',
			8 =>'status',

		);

		if($this->get_identity_app()==0)
		{
			if(empty($this->session->userdata("port_id")))
			{
				$port_id=$this->enc->decode($this->input->post('port'));;
			}
			else
			{
				$port_id=$this->session->userdata("port_id");
			}
		}
		else
		{
			$port_id=$this->get_identity_app();
		}

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) and (date(a.assignment_date) between ".$this->db->escape($dateFrom)." and ".$this->db->escape($dateTo)." ) ";

		if(!empty($port_id))
		{
			$where .=" and a.port_id=".$this->db->escape($port_id);
		}

		if(!empty($shift))
		{
			$where .=" and  a.shift_id= ".$this->db->escape($port_id);
		}


		if(!empty($iLike))
		{
			$where .="and (a.assignment_code ilike '%".$iLike ."%' ESCAPE '!'
						or b.username ilike '%".$iLike."%' ESCAPE '!' )";
		}

		$sql 		   = "SELECT
								 e.name as port_name, 
								 d.shift_name,
								  c.team_name as nama_regu, 
								  b.username, concat(first_name,' ',last_name) as full_name,
								  a.* 
							from app.t_trx_assignment_regu a
							left join core.t_mtr_user b on a.supervisor_id=b.id
							left join core.t_mtr_team c on a.team_code=c.team_code
							left join app.t_mtr_shift d on a.shift_id=d.id
							left join app.t_mtr_port e on a.port_id=e.id
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
			$id_enc=$this->enc->encode($row->assignment_code);

			$row->number = $i;

			$row->id =$row->id;
     		$close_url   = site_url($this->_module."/action_close_balance/{$id_enc}");

     		$row->actions  ="";

     		// check apakah punya hak akses
     		$tutup=checkBtnAccess($this->_module,'close_balance');


			if($row->status == 1){
				$row->status   = success_label('Buka Shift');
				
				// jika punya hak akses akan muncul
				if($tutup)
				{
					$row->actions .='<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin tutup shift data ini ?\', \''.$close_url.'\')" title="Tutup Shift"> <i class="fa fa-close"></i> </button>';
				}
			    // $row->actions .= generate_button_new($this->_module, 'close_balance', $close_url);
 
			}
			else
			{
				$row->status   = failed_label('Tutup Shift');
			}


     		$row->no=$i;
     		$row->assignment_date=format_date($row->assignment_date);

			$rows[] = $row;
			// unset($row->assignment_code);

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

	public function get_summary($ob_code)
	{
		return $this->db->query("
				select sum(amount) as amount, count(payment_type) as total_transaction ,ob_code, payment_type  from 
				app.t_trx_sell where ob_code='".$ob_code."'
				group by ob_code, payment_type
			");
	}

	public function select_data($table, $where="")
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

	public function get_assignment_code($where="")
	{
		return $this->db->query("select distinct d.shift_name ,a.shift_id, a.status,c.team_name, b.name as port_name, a.team_code, a.assignment_code, a.port_id, a.assignment_date from app.t_trx_assignment_user_pos a
			left join app.t_mtr_port b on a.port_id =b.id
			left join core.t_mtr_team c on a.team_code=c.team_code
			left join app.t_mtr_shift d on a.shift_id=d.id
					 $where");
	}

	public function get_user_list($where="")
	{
		return $this->db->query("select b.username , a.* from app.t_trx_assignment_user_pos a
		left join core.t_mtr_user b on a.user_id=b.id $where");
	}

	public function get_detail($where="")
	{
		return $this->db->query("
				select g.team_name , f.shift_name, a.assignment_code as code, e.username, concat(e.first_name,' ',e.last_name) as full_name, d.shift_name, c.name as port_name, a.* from app.t_trx_opening_balance a
							left join (
								select distinct team_code, assignment_code, port_id, assignment_date from app.t_trx_assignment_user_pos
							) as b on a.assignment_code=b.assignment_code
							left join app.t_mtr_port c on b.port_id=c.id
							left join app.t_mtr_shift d on a.shift_id=d.id
							left join core.t_mtr_user e on a.user_id = e.id
							left join app.t_mtr_shift f on a.shift_id =f.id
							left join core.t_mtr_team g on b.team_code=g.team_code
							$where
			");
	}

	public function get_identity_app()
	{
		$data=$this->db->query("select * from app.t_mtr_identity_app")->row();

		return $data->port_id;

	}



}
