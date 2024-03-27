<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_model extends MY_Model
{
	public function __construct() {
	    parent::__construct();
	    $this->_module = 'email';
	}
 
 	public function emailList()
	{
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		
		$field = array(	
			0=>'id',
			1=>'recipient',
			2=>'subject',
			3=>'status',
			4=>'created_by'
		);
		
		$order_column = $field[$order_column];

		$where = " where recipient !='' and (to_char(created_on,'yyyy-mm-dd') between '".$dateFrom."' and  '".$dateTo."') ";
		
		if (!empty($search['value']))
		{
			$where .=" and (recipient ilike '%".trim($search['value'])."%' or subject ilike '%".trim($search['value'])."%') ";
		}

		$sql="
			select * from core.t_trx_email {$where}
		";
		
		$query         = $this->db->query($sql);
		$records_total = $query->num_rows();

		$sql .=" ORDER BY ".$order_column." {$order_dir}";

		if($length != -1)
		{
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query     = $this->db->query($sql);
		$rows_data = $query->result();

		$rows = array();
		
		$i = ($start + 1);
		foreach ($rows_data as $row) {
			$row->id 		= $this->enc->encode($row->id);
			$detail_url 	= site_url($this->_module."/detail/{$row->id}");
			$change_url 	= site_url($this->_module."/change_status/{$row->id}");
			$row->number 	= $i;
			$row->recipient = $row->recipient;
			$row->body 		= $row->body;
			// $row->status 	= ($row->status == 1) ? success_color('Terkirim') : pending_color('Pending');

			$akses_send=checkBtnAccess($this->_module,'change_status');

			if($row->status==1)
            {
            	$row->status=success_label('Terkirim');
            }
            else if($row->status==0)
            {
            	$row->status=warning_label('Pending');	
            }
            else
            {
            	$row->status=failed_label('Gagal');	
            }

            $row->actions   = "";

            if($akses_send)
            {
				$row->actions .='<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin ingin mengirim ulang email ini?\', \''.$change_url.'\')" title="Ganti Status"> <i class="fa fa-exchange"></i> </button>';            	
            }

      		$row->actions  .= generate_button_new($this->_module, 'detail', $detail_url);

			$row->created_on= format_datetime($row->created_on);
			$rows[] 		= $row;

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
	
	function getData($table,$where)
	{
		return $this->db->select("*")->where($where)->get($table);
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
