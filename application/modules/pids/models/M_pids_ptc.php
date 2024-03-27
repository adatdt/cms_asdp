<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : M_stc
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class M_pids_ptc extends MY_Model{

	public function __construct() {
		parent::__construct();
		$this->_user = $this->session->userdata('username');
		$this->_module   = 'pids/pids_ptc';
	}

	//list pelabuhan
	function get_list_port(){
		$sql = "SELECT id, name 
		FROM app.t_mtr_port 
		WHERE status = 1 
		ORDER BY name ASC";

		$data = array('' => '');
		$query = $this->db->query($sql)->result();

		foreach ($query as $row) {
			$data[$this->enc->encode($row->id)] = strtoupper($row->name);
		}

		return $data;
	}

	//list kapal
	function get_list_ship($port_id, $selected_ship){
		$date = date('Y-m-d');
		$ship_id = $selected_ship ? " AND C.ship_id NOT IN ({$selected_ship}) " : "";
		// $sql = "SELECT id, name FROM app.t_mtr_ship WHERE status = 1 ORDER BY name ASC";
		$sql = "SELECT
					a.id,
					a.name
				FROM
					app.t_mtr_ship a
					JOIN app.t_mtr_sailing_company D
						ON A.ship_company_id = D.company_id AND D.port_id = {$port_id}
					-- JOIN (SELECT origin, destination FROM app.t_mtr_rute WHERE origin = {$port_id}) E 
					-- 	ON D.port_id = E.origin OR D.port_id = E.destination
					LEFT OUTER JOIN (SELECT
						C.ship_id
					FROM
						app.t_trx_schedule C
					JOIN app.t_mtr_schedule A ON
						A.schedule_code = C.schedule_code
					WHERE
						A.status = 1
						AND C.status = 1
						AND A.port_id = {$port_id}
						{$ship_id}
						AND ((A.schedule_date = '{$date}')
						OR (A.schedule_date <= '{$date}'
						AND ((ploting_date IS NOT NULL
						AND C.sail_date IS NULL)
						OR (docking_date IS NOT NULL
						AND C.sail_date IS NULL)
						OR (open_boarding_date IS NOT NULL
						AND C.sail_date IS NULL))))
						AND C.sail_date IS NULL)
						b ON a.id = b.ship_id
				WHERE
					b.ship_id is null
					AND a.status = 1
				ORDER BY
					a.name ASC";

		return $this->db->query($sql)->result();

	}

	//ambil data id kapal untuk mencocokan di dropdown (selected)
	function get_id_schecule($code){
		$sql = "SELECT 
			ship_id
		FROM app.t_trx_schedule c
		WHERE status = 1 AND  schedule_code = '{$code}'";

		$query = $this->db->query($sql);

		if($query->num_rows()){
			$ship_id = $query->row()->ship_id;
		}else{
			$ship_id = 0;
		}

		return $ship_id;
	}


	//list dermaga
	function get_list_dock(){
		// $post = $this->input->post();
		$port_id = $this->enc->decode($this->input->post('port'));
		$date = trim($this->input->post("date"));

		if($port_id){

				$sql = "SELECT id, name 
				FROM app.t_mtr_dock 
				WHERE status = 1 AND port_id = {$port_id}
				ORDER BY name ASC";

				$data = array();
				$data_summary = array();
				$query = $this->db->query($sql)->result();

				// $count_anchor=$this->stc->select_data("app.t_mtr_");

				foreach ($query as $row) {
					$data[$row->name] = $this->get_pidc_ptc($row->id, $date);

					$count_anchor = $this->get_data_pids_ptc(" where a.status=1 and b.status='5' and a.dock_id=$row->id and date::date='$date' ");

					$count_brocken = $this->get_data_pids_ptc(" where a.status=1 and b.status='3' and a.dock_id=$row->id and date::date='$date' ");					
					
					$count_docking = $this->get_data_pids_ptc(" where a.status=1 and b.status='4' and a.dock_id=$row->id and date::date='$date' ");										

					$count_sail = $this->get_data_pids_ptc(" where a.status=1 and b.status='2' and a.dock_id=$row->id and date::date='$date' ");										

					$count_ship = $this->get_data_pids_ptc(" where a.status=1 and b.status<>'-5' and a.dock_id=$row->id and date::date='$date' ")->result();

					$distinct_ship=array();

					if(!empty($count_ship))
					{
						foreach ($count_ship as $key => $value) {
							
							$distinct_ship[]=$value->ship_name;
						}
					}

					$data_summary[$row->name] =array("count_ship"=>count(array_unique($distinct_ship))<1?"-":count(array_unique($distinct_ship)),
													"count_anchor"=>$count_anchor->num_rows()<1?"-":$count_anchor->num_rows(),
													"count_broken"=>$count_brocken->num_rows()<1?"-":$count_brocken->num_rows(),
													"count_docking"=>$count_docking->num_rows()<1?"-":$count_docking->num_rows(),
													"count_sail"=>$count_sail->num_rows()<1?"-":$count_sail->num_rows()
												);
				}

				$identity_app = $this->cek_identity_app();

	            if($identity_app == 0)
	            {
	                $app_id = true;
	            }
	            else
	            {
	                $row = $this->global_model->selectById('app.t_mtr_port', 'id', $identity_app);
	                if ($row) {
	                    $app_id = $identity_app;
	                }
	                else
	                {
	                    $app_id = false;
	                }
	            }

				$arr = array(
					'dataPids' => $data,
					'summary'=>$data_summary,
					'identity_app' => ($app_id AND $app_id == $port_id) ? 1 : 0,
					'action' => checkBtnAccess('transaction/stc','edit') ? 1 : 0,
				);

				return json_api(1,'Data jadwal',$arr);
		}
		else
		{
			return json_api(0,'Jadwal tidak tersedia');
		}

	}
	// // list kapal
	// function get_ship()
	// {
	// 	$row=$this->db->query(" select * from app.t_mtr_ship order by name desc ");

	// 	$data=array();
	// 	foreach ($row->result() as $key => $value)
	// 	{
	// 		$id=$this->enc->encode($value->id);
	// 		$edit_url 	 = site_url($this->_module."/edit/{$id}");

	// 		$value->actions= generate_button_new($this->_module, 'edit', $edit_url);
	// 		$data[]=$value;	
	// 	}

	// 	return $data;
	// }

	function get_pidc_ptc ($dock_id, $date)
	{
		$row=$this->db->query(" 
			select c.name as ship_name, d.name as ship_backup_name, b.id as detail_id, b.status as detail_status, a.* from app.t_trx_pids_ptc a
			left join app.t_trx_pids_ptc_detail b on a.pids_ptc_code=b.pids_ptc_code
			left join app.t_mtr_ship c on b.ship_id =c.id
			left join app.t_mtr_ship d on b.ship_backup_id =d.id
			where a.dock_id={$dock_id} and a.status=1 and b.status<>'-5' and to_char(date,'yyyy-mm-dd')='{$date}'
			order by  b.status asc, b.order_data asc, b.updated_on desc
		 ");

		$data=array();
		foreach ($row->result() as $key => $value)
		{
			$id=$this->enc->encode($value->id);
			$edit_url 	 = site_url($this->_module."/edit/{$id}");
			$edit_url_detail 	 = site_url($this->_module."/edit/".$this->enc->encode($value->detail_id));

			if($value->detail_status==2 || $value->detail_status==3 || $value->detail_status==4 || $value->detail_status==5 )
			{	
				$value->actions="";
			}
			else
			{
				$value->actions= generate_button_new($this->_module, 'edit', $edit_url_detail);
			}

			if($value->detail_status==5)
			{
				$value->detail_status=failed_label("Anchor");	
			}
			else if($value->detail_status==3)
			{
				$value->detail_status=failed_label("Broken");	
			}
			else if($value->detail_status==4)
			{
				$value->detail_status=failed_label("Docking");	
			}
			else if($value->detail_status==2)
			{
				$value->detail_status=success_label("Berlayar");	
			}			
			else
			{
				$value->detail_status="";	
			}

			$data[]=$value;	
		}

		return $data;		
	}

	function get_data_pids_ptc ($where)
	{
		return $this->db->query(" 
			select c.name as ship_name, c.name as ship_backup_name, b.ship_id, a.* from app.t_trx_pids_ptc a
			left join app.t_trx_pids_ptc_detail b on a.pids_ptc_code=b.pids_ptc_code
			left join app.t_mtr_ship c on b.ship_id =c.id
			left join app.t_mtr_ship d on b.ship_backup_id =d.id
			{$where}
		 ");


	
	}	

	//create baording_code
	function boarding_code($port){
		$front_code="B".$port."".date('ymd');

		$chekCode=$this->db->query("select * from app.t_trx_open_boarding where left(boarding_code,8)='".$front_code."' ")->num_rows();

		if($chekCode<1)
		{
			$boarding_code=$front_code."0001";
			return $boarding_code;
		}
		else
		{
			$max=$this->db->query("select max (boarding_code) as max_code from app.t_trx_open_boarding where left(boarding_code,8)='".$front_code."' ")->row();
			$kode=$max->max_code;
			$noUrut = (int) substr($kode, 8, 4);
			$noUrut++;
			$char = $front_code;
			$kode = $char . sprintf("%04s", $noUrut);
			return $kode;
		}
	}

	function get_ship_pairing($where='')
	{
		return $this->db->query("
			SELECT d.name as ship_company_name, c.name as port_name, b.name as ship_name, a.* from app.t_mtr_ship_area a
			left join app.t_mtr_ship b on a.ship_id=b.id
			left join app.t_mtr_port c on a.port_id=c.id
			left join app.t_mtr_ship_company d on b.ship_company_id=d.id
			{$where}
			");
	}	

	// pengecekam identitas aplikasi
	function cek_identity_app(){
		$this->db->select('*');
        $this->db->limit(1);
        $row = $this->db->get('app.t_mtr_identity_app')->row();

		if($row){
			$port = $row->port_id;
		}else{
			$port = -1;
		}

		return $port;
	}

	public function max_order_data($pids_ptc_code)
	{
		$data= $this->db->query(" select max(order_data) as max_order from app.t_trx_pids_ptc_detail where pids_ptc_code='{$pids_ptc_code}' ")->row();

		return $data->max_order+1;

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
