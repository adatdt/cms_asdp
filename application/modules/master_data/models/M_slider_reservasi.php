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

class M_slider_reservasi extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'master_data/slider_reservasi';
        $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
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
			1 =>'name',
			2 =>'module',
			3 =>'path',
			4 =>'"desc"',
			5 =>'active',
			6 => '"order"',
			7 => 'url_target',
		);

		$order_column = $field[$order_column];

		$where = " WHERE a.module in ( 'slider_web_reservasi','slider_popup') and status = 1";

		if(!empty($search['value']))
		{
			$where .="and (
							a.name ilike '%".$iLike."%'
							or 	a.path ilike '%".$iLike."%'
							or 	a.desc ilike '%".$iLike."%' 
						)";
		}

		$sql 		   = "
									select 
										a.id, 
										a.name, 
										a.path, 
										a.desc, 
										a.active, 
										a.order, 
										a.url_target,
										a.module 
									from app.t_mtr_banner a
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

		foreach ($rows_data as $row) {
			$id_enc=$this->enc->encode($row->id);
			$row->number = $i;
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|false|'.$row->module));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|true|'.$row->module));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");
			
		 	$row->actions="";
			//  $row->detail  = generate_button($this->_module, 'delete', '<a href="#" class="btn-modal-info" type="button" title="Informasi Detail" data-image="'.$row->path.'">Lihat</a>');
			 $row->detail  = generate_button($this->_module, 'delete', '<a><img id="detailgambar" src="'.site_url($row->path).'" alt="Detail Gambar" style="max-width: 100%" data-image="'.$row->path.'"></a>');

			if($row->active == 't'){
				$row->active   = success_label('Aktif');
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}else{
				$row->active 	= failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}


     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

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
		return $this->dbView->query("select * from $table $where");
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

	// public function select_join($table, $where){
	// 	return $this->db->query("SELECT b.merchant_name as merchant_name, a.* FROM $table a left join app.t_mtr_merchant b ON a.merchant_id = b.merchant_id $where");

	// }

}
