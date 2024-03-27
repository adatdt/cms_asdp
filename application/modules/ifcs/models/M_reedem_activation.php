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

class M_reedem_activation  extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'ifcs/reedem_activation';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$vehicle_class=$this->enc->decode($this->input->post('vehicle_class'));
		$ship_class= $this->enc->decode($this->input->post('ship_class'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
		
		$field = array(
			0 =>'id',
			1 =>'port_name',
			2 =>'vehicle_class_name',
			3 =>'ship_class_name',
			// 4 =>'is_reedem',
			4 =>'is_redeem',
			5 =>'status',
		);

		$order_column = $field[$order_column];

		$identity_app=$this->select_data("app.t_mtr_identity_app","")->row();

		// validasi dengan identity app
		if($identity_app->port_id==0)
		{
			if(empty($this->session->userdata("port_id")))
			{
				$port_id=$this->enc->decode($this->input->post("port"));
			}
			else
			{
				$port_id=$this->session->userdata("port_id");
			}
		}
		else
		{
			$port_id=$identity_app->port_id;
		}

		$where = " WHERE a.status not in (-5) ";

		if(!empty($port_id))
		{
			$where .=" and (a.port_id={$port_id} ) ";
		}

		if(!empty($ship_class))
		{
			$where .=" and (a.ship_class={$ship_class} ) ";
		}		

		if(!empty($vehicle_class))
		{
			$where .=" and (a.vehicle_class={$vehicle_class} ) ";
		}		

		if(!empty($search['value']))
		{
			$where .="and (
						b.name ilike '%".$iLike."%'
						or c.name ilike '%".$iLike."%'
						or d.name ilike '%".$iLike."%'		
					)";
		}

		$sql 		   = "
							SELECT b.name as vehicle_class_name, c.name as ship_class_name, d.name as port_name,
							a.* from app.t_mtr_vehicle_class_reedem_activated a
							left join app.t_mtr_vehicle_class b on a.vehicle_class=b.id
							left join app.t_mtr_ship_class c on a.ship_class=c.id
							left join app.t_mtr_port d on a.port_id=d.id
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
     		$detail_url  = site_url($this->_module."/detail/{$id_enc}");

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

     		// $row->is_reedem=$row->is_reedem=='t'?'<span class="label label-success"><i class="fa fa-check-circle"></i><span></span></span>':'<span class="label label-danger"><i class="fa fa-times-circle"></i><span></span></span>';

     		$row->is_redeem=$row->is_redeem=='t'?'<span class="label label-success"><i class="fa fa-check-circle"></i><span></span></span>':'<span class="label label-danger"><i class="fa fa-times-circle"></i><span></span></span>';


     		// check apakah dia punya akses detail
			$row->actions .= generate_button($this->_module, 'detail', '<a class="btn btn-sm btn-primary" href="'.$detail_url.'" title="Aktifkan"><i class="fa fa-search-plus"></i></a>');

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
