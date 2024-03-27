<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_status extends MY_Model{

	public function __construct() {
		parent::__construct();
		$this->_module = 'monitoring/status';
		// $this->_dbdef = $this->load->database('default', TRUE);
		// $this->_dbmerak = $this->load->database('dbmerak', TRUE);
	}

	public function get_count_data()
	{
		$data_form['server_id'] = $this->input->post('server_id');
		$data_form['tbl_name'] = $this->input->post('tbl_name');
		$data_form['start_date'] = $this->input->post('start_date');
		$data_form['end_date'] = $this->input->post('end_date');

		$this->_dbdef = $this->load->database('cloud', TRUE);
		if($data_form['server_id'] == '02'){
			$init_database = 'dbmerak';
		}elseif($data_form['server_id'] == '03'){
			$init_database = 'dbbakau';
		}elseif($data_form['server_id'] == '04'){
			$init_database = 'dbgilimanuk';
		}elseif($data_form['server_id'] == '05'){
			$init_database = 'dbketapang';
		}else{
			$init_database = 'default';
		}

		$this->_dblocal = $this->load->database($init_database, TRUE);

		$status_cloud = 1;
		$status_local = 0;
		
		$database_local = $this->_dblocal;
		$database_cloud = $this->_dbdef;

		// $countTable = count($this->get_table());

		$data_local = $database_local->query($this->script_query($data_form))->result();
		$data_could = $database_cloud->query($this->script_query($data_form))->result();
		$data_status = $database_cloud->query($this->get_status($data_form['tbl_name']))->result();

		$array = array(
			'local'	=> $data_local,
			'cloud'	=> $data_could,
			// 'bar'	=> $this->list_bar($data_local,$data_could),
			// 'num'	=> $countTable
			'status_data' => $data_status

		);
		//$json = json_encode($array);
		return $array;
		// return $this->generate_script();
	}


	private function script_query($data){

		$table_name = $data['tbl_name'];
		$server_id = $data['server_id'];
		$start_date = $data['start_date'];
		$end_date = $data['end_date'];

		$where = " AND aa.created_on BETWEEN '{$start_date}' AND '{$end_date}'";

		$sql = "SELECT 
		(
			SELECT COUNT(*) 
			FROM app.{$table_name} aa 
			WHERE aa.server_id = '00' AND aa.status = mstat.status {$where}
		) AS jumlah_on_server,
		(
			SELECT COUNT(*) 
			FROM app.{$table_name} aa
			WHERE aa.server_id = '{$server_id}' AND aa.status = mstat.status $where
		) AS jumlah_on_local,
		mstat.status, 
		mstat.description 
		
		FROM app.t_mtr_status mstat
		WHERE mstat.tbl_name = '{$table_name}' 
		GROUP BY mstat.status, mstat.description";

		return $sql;


	}

	public function server_local(){
		$array = array(
			'Merak' => '02',
			'Bakau' => '03',
			'Gilimanuk' => '04',
			'Ketapang' => '05'
		);

		return $array;
	}

	public function get_table()
	{
		$table = array(
			't_trx_invoice',
			't_trx_booking',
			't_trx_booking_passanger',
			't_trx_booking_vehicle',
		);

		return $table;
	}

	public function get_status($table_name){
		
		$sql = "SELECT 
		mstat.status, 
		mstat.description 
		
		FROM app.t_mtr_status mstat
		WHERE mstat.tbl_name = '{$table_name}' 
		GROUP BY mstat.status, mstat.description";

		return $sql;


	}
	
	// public function list_bar($data1, $data2)
	// {

	// 	$d_local = $data1;
	// 	$d_cloud = $data2;
	// 	$get_table = $this->get_table();
		

	// 	$array = array(
	// 		'list_table' => $get_table,
	// 		'data_ctl_local' => $this->get_data_bar($d_local,0,'ctl'),
	// 		'data_ctl_cloud' => $this->get_data_bar($d_cloud,1,'ctl'),
	// 		'data_ltc_local' => $this->get_data_bar($d_local,0,'ltc'),
	// 		'data_ltc_cloud' => $this->get_data_bar($d_cloud,1,'ltc')
	// 	);

	// 	return $array;
	// }

	// private function get_data_bar($data,$status,$ket)
	// {
	// 	$get_table = $this->get_table();
	// 	if($status == 1){
	// 		$s = 'local';
	// 	}else{
	// 		$s = 'cloud';
	// 	}

	// 	$data_array = [];

	// 	foreach($get_table as $key){
	// 		$field = $ket."_".$s."_".$key;
	// 		$data_array[] = $data[0]->$field;
	// 	}

	// 	return $data_array;
	// }




	// private function script_query(){
	// 	$sql = " SELECT * FROM(
		
	// 	select 
	// 	count(*) AS jumlah_data, tbook.status, mst.description as status_name, mst.tbl_name
	// 	from t_trx_booking tbook
	// 	JOIN t_mtr_status mst ON tbook.status = mst.status AND mst.tbl_name = 't_trx_booking'
	// 	GROUP BY tbook.status, mst.tbl_name, mst.description
		
	// 	UNION ALL
		
	// 	select 
	// 	count(*) AS jumlah_data, tbokpas.status, mst.description as status_name, mst.tbl_name
	// 	from t_trx_booking_passanger tbokpas
	// 	JOIN t_mtr_status mst ON tbokpas.status = mst.status AND mst.tbl_name = 't_trx_booking_passanger'
	// 	GROUP BY tbokpas.status, mst.tbl_name, mst.description
		
	// 	UNION ALL
		
	// 	select 
	// 	count(*) AS jumlah_data, tinv.status, mst.description as status_name, mst.tbl_name
	// 	from t_trx_invoice tinv
	// 	JOIN t_mtr_status mst ON tinv.status = mst.status AND mst.tbl_name = 't_trx_invoice'
	// 	GROUP BY tinv.status, mst.tbl_name, mst.description
		
	// 	UNION ALL
		
	// 	select 
	// 	count(*) AS jumlah_data, tbov.status, mst.description as status_name, mst.tbl_name
	// 	from t_trx_booking_vehicle tbov
	// 	JOIN t_mtr_status mst ON tbov.status = mst.status AND mst.tbl_name = 't_trx_booking_vehicle'
	// 	GROUP BY tbov.status, tbov.server_id, mst.tbl_name, mst.description
		
		
	// 	) aa 
	// 	ORDER BY aa.tbl_name, aa.status";

	// 	return $sql;

	// }



}
