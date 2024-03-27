<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_master_license extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'device_management/master_license';
	}

    public function dataList() {
		$start          = $this->input->post('start');
		$length         = $this->input->post('length');
		$draw           = $this->input->post('draw');
		$search         = $this->input->post('search');
		$order          = $this->input->post('order');
		$order_column   = $order[0]['column'];
		$order_dir      = strtoupper($order[0]['dir']);
		$iLike          = trim(strtoupper($this->db->escape_like_str($search['value'])));
		$license_number = trim($this->input->post('license_number'));
		$imei 			= trim($this->input->post('imei'));

        $field = array(
			0 =>'id',
			1 =>'license_number',
			2 =>'created_by',
			3 =>'created_on',
			4 =>'status',
		);

		$order_column   = $field[$order_column];
		$where          = "WHERE status NOT IN ('-5') ";

		if(!empty($search['value'])) {
			$where .=" AND ( (license_number ilike '%".$iLike."%') OR (imei ilike '%".$iLike."%') )";
		}

		if (!empty($license_number)) {
			$where .= " AND (license_number = '$license_number')";
		}

		if (!empty($imei)) {
			$where .= " AND (imei = '$imei')";
		}

		$sql = "SELECT * 
                FROM app.t_mtr_license
                {$where}";

        $query         = $this->db->query($sql);
		$records_total = $query->num_rows();
		$sql 		  .= " ORDER BY ".$order_column." {$order_dir}";

		if($length != -1){
			$sql .=" LIMIT {$length} OFFSET {$start}";
		}

		$query      = $this->db->query($sql);
		$rows_data  = $query->result();
		$rows 	    = array();
		$i  	    = ($start + 1);

		foreach ($rows_data as $row) {
			$id_enc                 = $this->enc->encode($row->id);
            $row->no                = $i;
            $row->license_number    = empty($row->license_number) ? '-' : $row->license_number;
            $row->imei    			= empty($row->imei) ? '-' : $row->imei;
            $row->created_by        = empty($row->created_by) ? '-' : $row->created_by;
            $row->created_on        = empty($row->created_on) ? '-' : date('d-m-Y H:i:s', strtotime($row->created_on));
            $nonaktif               = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|-1'));
            $aktif                  = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));
            $row->actions           = '';
			if($row->status == 1){
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'change_status', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
            }else{
				$row->status 	= failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'change_status', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}
			$rows[] = $row;
			$i++;
		}

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	public function select_data($table, $where) {
		return $this->db->query("SELECT * FROM $table $where");
	}

	public function insert_data($table, $data) {
		$this->db->insert($table, $data);
	}

	public function update_data($table, $data, $where) {
		$this->db->where($where);
		$this->db->update($table, $data);
	}

	public function delete_data($table, $data, $where) {
		$this->db->where($where);
		$this->db->delete($table, $data);
	}
}
