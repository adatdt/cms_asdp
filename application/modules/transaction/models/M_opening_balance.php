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

class M_opening_balance extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'transaction/opening_balance';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom= date("Y-m-d", strtotime($this->input->post('dateFrom')));
		$dateTo= date("Y-m-d", strtotime($this->input->post('dateTo')));
		// $port_id= $this->enc->decode($this->input->post('port'));
		$shift_id= $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		

		$field = array(
			0 =>'id',
			1 =>'trx_date',
			2 =>'ob_code',
			3 =>'username',
			4 =>'full_name',
			5 =>'shift_name',
			6 =>'port_name',
			7 =>'code',
			8 =>'terminal_name',
			9 =>'total_cash',
			10 =>'total_non_tunai',
			11 =>'status',

		);

		// validasi port

		if($this->get_identity_app()->port_id==0)
		{
			if(empty($this->session->userdata("port_id")))
			{
				$port_id= $this->enc->decode($this->input->post('port'));
			}	
			else
			{
				$port_id= $this->session->userdata("port_id");	
			}
		}
		else
		{
			$port_id= $this->get_identity_app()->port_id;	

		}

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) AND a.trx_date BETWEEN ".$this->db->escape($dateFrom)." AND ".$this->db->escape($dateTo)." ";

		if(!empty($port_id))
		{
			$where .=" and b.port_id=".$this->db->escape($port_id)." ";
		}

		if(!empty($shift_id))
		{
			$where .=" and  a.shift_id = ".$this->db->escape($shift_id)."  ";
		}

		if(!empty($search['value']))
		{
			$where .="and (
					e.username ilike '%".$iLike."%' ESCAPE '!'
					or concat(e.first_name,' ',e.last_name) ilike '%".$iLike."%' ESCAPE '!' 
					or a.assignment_code ilike '%".$iLike."%'  ESCAPE '!'
					or c.name ilike  '%".$iLike."%' ESCAPE '!' 
					or d.shift_name ilike  '%".$iLike."%' ESCAPE '!' 
					or f.terminal_name ilike  '%".$iLike."%' ESCAPE '!'
					or a.ob_code ilike  '%".$iLike."%' ESCAPE '!'
			 )";
		}

		$sql = "SELECT 
				DISTINCT a.*,
				(
					select cast(sum(amount) as numeric(18,2)) from
					app.t_trx_sell
					where payment_type !='cash' and ob_code=a.ob_code	
				)as total_non_tunai,
				(
					select cast(sum(amount) as numeric(18,2)) from
					app.t_trx_sell
					where ob_code=a.ob_code	
				)as grand_total,
				a.assignment_code as code,
				e.username,
				CONCAT(e.first_name,' ',e.last_name) as full_name,
				d.shift_name, c.name as port_name,
				f.terminal_name 
				FROM app.t_trx_opening_balance a
				JOIN app.t_trx_assignment_user_pos b ON a.assignment_code = b.assignment_code
				JOIN app.t_mtr_port c ON b.port_id = c.id
				JOIN app.t_mtr_shift d ON a.shift_id = d.id
				JOIN core.t_mtr_user e ON a.user_id = e.id
				LEFT JOIN app.t_mtr_device_terminal f ON a.terminal_code = f.terminal_code
				{$where}";

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
			$code_enc=$this->enc->encode($row->ob_code);

			$row->number = $i;

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$code_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
     		$close_url   = site_url($this->_module."/action_close_balance/{$code_enc}");
     		$close_force_url   = site_url($this->_module."/action_force_logoff/{$code_enc}");

     		// $row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

     		$row->actions  = "";
     		$row->total_cash = idr_currency($row->total_cash);
     		$row->total_non_tunai = idr_currency($row->total_non_tunai);

     		// check apakah punya hak akses
     		$tutup=checkBtnAccess($this->_module,'close_balance');

			if($row->status == 1){
				$row->status   = success_label('Berdinas');
				
				// jika punya hak akses akan muncul
				if($tutup)
				{
					$row->actions .='<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin Tutup dinas data ini ?\', \''.$close_url.'\')" title="tutup dinas"> <i class="fa fa-close"></i> </button>';

					// force log off jika statusnya masih 1
				    if(!empty($row->terminal_code) )
					{
						$row->actions .='<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin Logoff User ini ?\', \''.$close_force_url.'\')" title="force logoff"> <i class="fa fa-sign-out"></i> </button>';					
					}					
				}
			    // $row->actions .= generate_button_new($this->_module, 'close_balance', $close_url);
 
			}
			else
			{
				$row->status   = failed_label('Tutup Dinas');
			}


     		$row->no=$i;
     		$row->trx_date=format_date($row->trx_date);
     		$row->total_non_tunai=empty($row->total_non_tunai)?"0.00":$row->total_non_tunai;
     		$row->grand_total=empty($row->grand_total)?"0.00":$row->grand_total;

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


    public function data_cs(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom= date("Y-m-d", strtotime($this->input->post('dateFrom')));
		$dateTo= date("Y-m-d", strtotime($this->input->post('dateTo')));
		// $port_id= $this->enc->decode($this->input->post('port'));
		$shift_id= $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		

		$field = array(
			0 =>'id',
			1 =>'assignment_date',
			2 =>'shift_code',
			3 =>'username',
			4 =>'full_name',
			5 =>'shift_id',
			6 =>'port_id',
			7 =>'assignment_code',
			8 =>'terminal_code',
			9 =>'status',

		);

		// validasi port

		if($this->get_identity_app()->port_id==0)
		{
			if(empty($this->session->userdata("port_id")))
			{
				$port_id= $this->enc->decode($this->input->post('port'));
			}	
			else
			{
				$port_id= $this->session->userdata("port_id");	
			}
		}
		else
		{
			$port_id= $this->get_identity_app()->port_id;	

		}

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) AND a.assignment_date BETWEEN ".$this->db->escape($dateFrom)." AND ".$this->db->escape($dateTo)." ";

		if(!empty($port_id))
		{
			$where .=" and a.port_id=".$this->db->escape($port_id)."  ";
		}

		if(!empty($shift_id))
		{
			$where .=" and a.shift_id=".$this->db->escape($shift_id)."  ";
		}

		if(!empty($search['value']))
		{
			$where .="and (
					d.username ilike '%".$iLike."%' ESCAPE '!'
					or e.terminal_name ilike '%".$iLike."%' ESCAPE '!'
					or concat(d.first_name,' ',d.last_name) ilike '%".$iLike."%' ESCAPE '!'
					or a.assignment_code ilike '%".$iLike."%' ESCAPE '!'
					or a.shift_code ilike '%".$iLike."%' ESCAPE '!'
			 )";
		}


		$sql = "SELECT
					 b.name as port_name,
					  d.username, c.shift_name,
					e.terminal_name,
					concat(d.first_name,' ',d.last_name) as full_name ,
					 a.* 
				from app.t_trx_assignment_cs a
				left JOIN app.t_mtr_port b ON a.port_id = b.id
				left JOIN app.t_mtr_shift c ON a.shift_id = c.id
				left JOIN core.t_mtr_user d ON a.user_id = d.id
				LEFT JOIN app.t_mtr_device_terminal e ON a.terminal_code = e.terminal_code
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
			$code_enc=$this->enc->encode($row->shift_code);

			$row->number = $i;

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$code_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
     		$close_url   = site_url($this->_module."/action_close_balance2/{$code_enc}/cs");

     		// $row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

     		$row->actions  = "";


     		// check apakah punya hak akses
     		$tutup=checkBtnAccess($this->_module,'close_balance');

			if($row->status == 1){
				$row->status   = success_label('Berdinas');
				
				// jika punya hak akses akan muncul
				if($tutup)
				{
					$row->actions .='<button class="btn btn-sm btn-danger" onclick="confAct(\'Apakah Anda yakin Tutup dinas data ini ?\', \''.$close_url.'\')" title="tutup dinas"> <i class="fa fa-close"></i> </button>';
				}
			    // $row->actions .= generate_button_new($this->_module, 'close_balance', $close_url);
 
			}
			else
			{
				$row->status   = failed_label('Tutup Dinas');
			}


     		$row->no=$i;
     		$row->assignment_date=format_date($row->assignment_date);

			$rows[] = $row;
			unset($row->id);
			unset($row->shift_id);
			unset($row->user_id);
			unset($row->port_id);

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

    public function data_ptcstc(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom= date("Y-m-d", strtotime($this->input->post('dateFrom')));
		$dateTo= date("Y-m-d", strtotime($this->input->post('dateTo')));
		// $port_id= $this->enc->decode($this->input->post('port'));
		$shift_id= $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		

		$field = array(
			0 =>'id',
			1 =>'assignment_date',
			2 =>'shift_code',
			3 =>'username',
			4 =>'full_name',
			5 =>'shift_id',
			6 =>'port_id',
			7 =>'assignment_code',
			8 =>'terminal_code',
			9 =>'status',

		);

		// validasi port

		if($this->get_identity_app()->port_id==0)
		{
			if(empty($this->session->userdata("port_id")))
			{
				$port_id= $this->enc->decode($this->input->post('port'));
			}	
			else
			{
				$port_id= $this->session->userdata("port_id");	
			}
		}
		else
		{
			$port_id= $this->get_identity_app()->port_id;	

		}

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) AND a.assignment_date BETWEEN ".$this->db->escape($dateFrom)." AND ".$this->db->escape($dateTo)." ";

		if(!empty($port_id))
		{
			$where .=" and a.port_id=".$this->db->escape($port_id)." ";
		}

		if(!empty($shift_id))
		{
			$where .=" and a.shift_id=".$this->db->escape($shift_id)." ";
		}

		if(!empty($search['value']))
		{
			$where .="and (
					d.username ilike '%".$iLike."%' ESCAPE '!'
					or e.terminal_name ilike '%".$iLike."%' ESCAPE '!'
					or concat(d.first_name,' ',d.last_name) ilike '%".$iLike."%' ESCAPE '!'
					or a.assignment_code ilike '%".$iLike."%' ESCAPE '!'
					or a.shift_code ilike '%".$iLike."%' ESCAPE '!'
			 )";
		}


		$sql = "SELECT 
					b.name as port_name, 
					d.username, 
					c.shift_name,
					 e.terminal_name,
					concat(d.first_name,' ',d.last_name) as full_name ,
					 a.* 
				from app.t_trx_assignment_ptc_stc a
				left JOIN app.t_mtr_port b ON a.port_id = b.id
				left JOIN app.t_mtr_shift c ON a.shift_id = c.id
				left JOIN core.t_mtr_user d ON a.user_id = d.id
				LEFT JOIN app.t_mtr_device_terminal e ON a.terminal_code = e.terminal_code
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
			$code_enc=$this->enc->encode($row->shift_code);

			$row->number = $i;

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$code_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
     		$close_url   = site_url($this->_module."/action_close_balance2/{$code_enc}/ptcstc");

     		// $row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

     		$row->actions  = "";


     		// check apakah punya hak akses
     		$tutup=checkBtnAccess($this->_module,'close_balance');

			if($row->status == 1){
				$row->status   = success_label('Berdinas');
				
				// jika punya hak akses akan muncul
				if($tutup)
				{
					$row->actions .='<button class="btn btn-sm btn-danger" onclick="confAct(\'Apakah Anda yakin Tutup dinas data ini ?\', \''.$close_url.'\')" title="tutup dinas"> <i class="fa fa-close"></i> </button>';
				}
			    // $row->actions .= generate_button_new($this->_module, 'close_balance', $close_url);
 
			}
			else
			{
				$row->status   = failed_label('Tutup Dinas');
			}


     		$row->no=$i;
     		$row->assignment_date=format_date($row->assignment_date);

			$rows[] = $row;
			unset($row->id);
			unset($row->shift_id);
			unset($row->user_id);
			unset($row->port_id);

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

    public function data_verifikator(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom= date("Y-m-d", strtotime($this->input->post('dateFrom')));
		$dateTo= date("Y-m-d", strtotime($this->input->post('dateTo')));
		// $port_id= $this->enc->decode($this->input->post('port'));
		$shift_id= $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		

		$field = array(
			0 =>'id',
			1 =>'assignment_date',
			2 =>'shift_code',
			3 =>'username',
			4 =>'full_name',
			5 =>'shift_id',
			6 =>'port_id',
			7 =>'assignment_code',
			8 =>'terminal_code',
			9 =>'status',

		);

		// validasi port

		if($this->get_identity_app()->port_id==0)
		{
			if(empty($this->session->userdata("port_id")))
			{
				$port_id= $this->enc->decode($this->input->post('port'));
			}	
			else
			{
				$port_id= $this->session->userdata("port_id");	
			}
		}
		else
		{
			$port_id= $this->get_identity_app()->port_id;	

		}

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) AND a.assignment_date BETWEEN ".$this->db->escape($dateFrom)." AND ".$this->db->escape($dateTo)." ";

		if(!empty($port_id))
		{
			$where .=" and a.port_id=".$this->db->escape($port_id)."  ";
		}

		if(!empty($shift_id))
		{
			$where .=" and a.shift_id=".$this->db->escape($shift_id)."  ";
		}

		if(!empty($search['value']))
		{
			$where .="and (
					d.username ilike '%".$iLike."%' ESCAPE '!'
					or e.terminal_name ilike '%".$iLike."%' ESCAPE '!'
					or concat(d.first_name,' ',d.last_name) ilike '%".$iLike."%' ESCAPE '!'
					or a.assignment_code ilike '%".$iLike."%' ESCAPE '!'
					or a.shift_code ilike '%".$iLike."%' ESCAPE '!'
			 )";
		}


		$sql = "SELECT 
					b.name as port_name, 
					d.username, 
					c.shift_name, 
					e.terminal_name,
					concat(d.first_name,' ',d.last_name) as full_name , 
				a.* 
				from app.t_trx_assignment_verifier a
				left JOIN app.t_mtr_port b ON a.port_id = b.id
				left JOIN app.t_mtr_shift c ON a.shift_id = c.id
				left JOIN core.t_mtr_user d ON a.user_id = d.id
				LEFT JOIN app.t_mtr_device_terminal e ON a.terminal_code = e.terminal_code
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
			$code_enc=$this->enc->encode($row->shift_code);

			$row->number = $i;

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$code_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
     		$close_url   = site_url($this->_module."/action_close_balance2/{$code_enc}/verifikator");

     		// $row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

     		$row->actions  = "";


     		// check apakah punya hak akses
     		$tutup=checkBtnAccess($this->_module,'close_balance');

			if($row->status == 1){
				$row->status   = success_label('Berdinas');
				
				// jika punya hak akses akan muncul
				if($tutup)
				{
					$row->actions .='<button class="btn btn-sm btn-danger" onclick="confAct(\'Apakah Anda yakin Tutup dinas data ini ?\', \''.$close_url.'\')" title="tutup dinas"> <i class="fa fa-close"></i> </button>';
				}
			    // $row->actions .= generate_button_new($this->_module, 'close_balance', $close_url);
 
			}
			else
			{
				$row->status   = failed_label('Tutup Dinas');
			}


     		$row->no=$i;
     		$row->assignment_date=format_date($row->assignment_date);

			$rows[] = $row;
			unset($row->id);
			unset($row->shift_id);
			unset($row->user_id);
			unset($row->port_id);

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

	public function data_comand_center(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom= date("Y-m-d", strtotime($this->input->post('dateFrom')));
		$dateTo= date("Y-m-d", strtotime($this->input->post('dateTo')));
		// $port_id= $this->enc->decode($this->input->post('port'));
		$shift_id= $this->enc->decode($this->input->post('shift'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		

		$field = array(
			0 =>'id',
			1 =>'assignment_date',
			2 =>'shift_code',
			3 =>'username',
			4 =>'full_name',
			5 =>'shift_id',
			6 =>'port_id',
			7 =>'assignment_code',
			8 =>'terminal_code',
			9 =>'status',

		);

		// validasi port

		if($this->get_identity_app()->port_id==0)
		{
			if(empty($this->session->userdata("port_id")))
			{
				$port_id= $this->enc->decode($this->input->post('port'));
			}	
			else
			{
				$port_id= $this->session->userdata("port_id");	
			}
		}
		else
		{
			$port_id= $this->get_identity_app()->port_id;	

		}

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) AND a.assignment_date BETWEEN ".$this->db->escape($dateFrom)." AND ".$this->db->escape($dateTo)." ";

		if(!empty($port_id))
		{
			$where .=" and a.port_id=".$this->db->escape($port_id)."  ";
		}

		if(!empty($shift_id))
		{
			$where .=" and a.shift_id=".$this->db->escape($shift_id)."  ";
		}

		if(!empty($search['value']))
		{
			$where .="and (
					d.username ilike '%".$iLike."%' ESCAPE '!'
					or e.terminal_name ilike '%".$iLike."%' ESCAPE '!'
					or concat(d.first_name,' ',d.last_name) ilike '%".$iLike."%' ESCAPE '!'
					or a.assignment_code ilike '%".$iLike."%' ESCAPE '!'
					or a.shift_code ilike '%".$iLike."%' ESCAPE '!'
			 )";
		}


		$sql = "SELECT 
					b.name as port_name, 
					d.username, 
					c.shift_name, 
					e.terminal_name,
					concat(d.first_name,' ',d.last_name) as full_name , 
				a.* 
				from app.t_trx_assignment_command_center a
				left JOIN app.t_mtr_port b ON a.port_id = b.id
				left JOIN app.t_mtr_shift c ON a.shift_id = c.id
				left JOIN core.t_mtr_user d ON a.user_id = d.id
				LEFT JOIN app.t_mtr_device_terminal e ON a.team_code = e.terminal_code
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
			$code_enc=$this->enc->encode($row->shift_code);

			$row->number = $i;

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$code_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
     		$close_url   = site_url($this->_module."/action_close_balance2/{$code_enc}/comand_center");

     		// $row->actions  = generate_button_new($this->_module, 'edit', $edit_url);

     		$row->actions  = "";


     		// check apakah punya hak akses
     		$tutup=checkBtnAccess($this->_module,'close_balance');

			if($row->status == 1){
				$row->status   = success_label('Berdinas');
				
				// jika punya hak akses akan muncul
				if($tutup)
				{
					$row->actions .='<button class="btn btn-sm btn-danger" onclick="confAct(\'Apakah Anda yakin Tutup dinas data ini ?\', \''.$close_url.'\')" title="tutup dinas"> <i class="fa fa-close"></i> </button>';
				}
			    // $row->actions .= generate_button_new($this->_module, 'close_balance', $close_url);
 
			}
			else
			{
				$row->status   = failed_label('Tutup Dinas');
			}


     		$row->no=$i;
     		$row->assignment_date=format_date($row->assignment_date);

			$rows[] = $row;
			unset($row->id);
			unset($row->shift_id);
			unset($row->user_id);
			unset($row->port_id);

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
					SELECT 
					count(sell.id) as total_transaction
					,coalesce(sum(sell.amount),0) as amount 
					,sell.payment_type 
					,sell.ob_code
					,inv.transaction_type
					FROM app.t_trx_sell sell 
					JOIN app.t_trx_invoice inv ON inv.trans_number = sell.trans_number 
					where sell.ob_code = '{$ob_code}'  
					group by sell.payment_type ,sell.ob_code,inv.transaction_type
			");
	}	

	public function get_summary_05102021($ob_code)
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
		return $this->db->query(" select * from app.t_mtr_identity_app ")->row();
	}



}
