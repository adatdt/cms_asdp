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

class M_routeclass extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'fare/route_class';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');

		$port_destination= $this->enc->decode($this->input->post('port_destination'));
		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));


		$get_identity_app=$this->select_data(" app.t_mtr_identity_app","")->row();
		// filter berdasarkan port di user

		if ($get_identity_app->port_id==0) 
		{
			if(!empty($this->session->userdata('port_id')))
			{
				$port_origin= $this->session->userdata('port_id');
			}
			else
			{
				$port_origin= $this->enc->decode($this->input->post('port_origin'));
			}
		}
		else
		{
			$port_origin= $get_identity_app->port_id;
		}

		$field = array(
			0 =>'id',
			1 =>'route_name',
			2 =>'ship_class_name',
			3 =>'ifcs',
			4 =>'status',

		);

		$order_column = $field[$order_column];

		$where = " WHERE a.status not in (-5) ";

		if(!empty($search['value']))
		{
			$where .="and (concat(c.name,'-',d.name) ilike 
									'%".$iLike."%' ESCAPE '!' 
									or e.name ilike 
									'%".$iLike."%' ESCAPE '!'
									)";
		}

		if(!empty($port_origin))
		{
			$where .="and (b.origin=".$port_origin.")";
		}

		if(!empty($port_destination))
		{
			$where .="and (b.destination=".$port_destination.")";
		}

		$sql 		   = "
							select e.name as ship_class_name, concat(c.name,'-',d.name) as route_name, a.* from app.t_mtr_rute_class a
							left join app.t_mtr_rute b on a.rute_id=b.id
							left join app.t_mtr_port c on b.origin=c.id
							left join app.t_mtr_port d on b.destination=d.id
							left join app.t_mtr_ship_class e on a.ship_class=e.id 
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
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|-1|non aktif'));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1|aktif'));

			$row->id =$row->id;
			$edit_url 	 = site_url($this->_module."/edit/{$id_enc}");
     		$delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

     		$row->actions  =" ";

			if($row->status == 1){
				$row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
				$row->status   = success_label('Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}else{
				$row->status   = failed_label('Tidak Aktif');
				$row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Nonaktifkan"> <i class="fa fa-check"></i> </button> ');
			}

			if($row->ifcs=='t')
			{
				$row->ifcs='<span class="label label-success"><i class="fa fa-check-circle"></i><span>';
			}
			else
			{
				$row->ifcs='<span class="label label-danger"><i class="fa fa-times-circle"></i><span>';
			}


     		$row->no=$i;
     		$row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

			$rows[] = $row;
			unset($row->id);

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

	public function get_route($where)
	{
		$qry ="SELECT concat(b.name,'-',c.name) as route_name , a.* from app.t_mtr_rute a
		left join app.t_mtr_port  b on a.origin=b.id
		left join app.t_mtr_port c on a.destination=c.id
		where a.status not in (-5) $where
		order by concat(b.name,'-',c.name) desc

		";

		// die($qry); exit;
		$row=$this->db->query($qry);

		
		// mencegah error foreach jika data kosong
		if($row->num_rows()<1)
		{
			return $row->result();
		}
		else
		{
			$data=array();
			foreach($row->result() as $key=>$value )
			{
				$value->id=$this->enc->encode($value->id);
				$data[]=$value;
			}
			return $data;
		}
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
