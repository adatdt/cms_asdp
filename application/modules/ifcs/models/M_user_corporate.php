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

class M_user_corporate  extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'ifcs/user_corporate';
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
			1 =>'corporate_name',
			2 =>'corporate_code',
			3 =>'member_type',
			4 =>'name',
			5 =>'nik',
			6 =>'nip',
			7 =>'phone',
			8 =>'email',
			9 =>'branch_name',
			10 =>'position',
			11=>'booking',
			12=>'reedem',
			13=>'topup_deposit',
			14=>'deposit',
			15=>'cash_out_deposit',
			16 =>'is_activation',
			17 =>'status',


		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) ";


		if(!empty($search['value']))
		{
			$where .="and (
					a.corporate_code ilike '%".$iLike."%'
					or b.corporate_name ilike '%".$iLike."%'
					or a.phone ilike '%".$iLike."%'
					or a.email ilike '%".$iLike."%'
					or a.nik ilike '%".$iLike."%'
					or a.nip ilike '%".$iLike."%'
					or a.branch_code ilike '%".$iLike."%'
					or a.name ilike '%".$iLike."%'
					or d.description ilike '%".$iLike."%'
					or corporate_address ilike '%".$iLike."%'
					)";
		}

		$sql 		   = "
							select d.description as branch_name, c.name as member_type_name, b.corporate_name, a.* from app.t_mtr_member_ifcs a
							left join app.t_mtr_corporate_ifcs b on a.corporate_code=b.corporate_code
							left join app.t_mtr_member_ifcs_type c on a.member_type=c.id
							left join app.t_mtr_branch_ifcs d on a.branch_code=d.branch_code
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
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
     		$reset_url 	= site_url($this->_module."/reset_password/{$id_enc}");

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

			if($row->is_activation==true)
    		{
    			$row->is_activation=success_label("<i class='fa fa-check-circle'></i>");	
    		}
    		else
    		{
    			$row->is_activation=failed_label("<i class='fa fa-times-circle'></i>");	
    		}


			if($row->booking=='t')
    		{
    			$row->booking=success_label("<i class='fa fa-check-circle'></i>");	
    		}
    		else
    		{
    			$row->booking=failed_label("<i class='fa fa-times-circle'></i>");	
    		}                    

			if($row->reedem=='t')
    		{
    			$row->reedem=success_label("<i class='fa fa-check-circle'></i>");	
    		}
    		else
    		{
    			$row->reedem=failed_label("<i class='fa fa-times-circle'></i>");	
    		}  

			if($row->topup_deposit=='t')
    		{
    			$row->topup_deposit=success_label("<i class='fa fa-check-circle'></i>");	
    		}
    		else
    		{
    			$row->topup_deposit=failed_label("<i class='fa fa-times-circle'></i>");	
    		}      		                  

			if($row->deposit=='t')
    		{
    			$row->deposit=success_label("<i class='fa fa-check-circle'></i>");	
    		}
    		else
    		{
    			$row->deposit=failed_label("<i class='fa fa-times-circle'></i>");	
    		} 

			if($row->cash_out_deposit=='t')
    		{
    			$row->cash_out_deposit=success_label("<i class='fa fa-check-circle'></i>");	
    		}
    		else
    		{
    			$row->cash_out_deposit=failed_label("<i class='fa fa-times-circle'></i>");	
    		}     		     		


			$row->actions .= generate_button($this->_module, 'edit', '<button class="btn btn-sm btn-warning" title="Ganti Password" onclick="showModal(\''.$reset_url.'\')"> <i class="fa fa-lock"></i></button>');

     		$row->no=$i;
     		// $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

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
