<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_cloud extends MY_Model{

	public function __construct() {
		parent::__construct();
		$this->_module = 'monitoring/cloud';
		// $this->_dbdef = $this->load->database('default', TRUE);
		// $this->_dbmerak = $this->load->database('dbmerak', TRUE);
	}

	public function get_count_data()
	{
		$data_form['server_id'] = $this->input->post('server_id');
		$data_form['start_date'] = $this->input->post('start_date');
		$data_form['end_date'] = $this->input->post('end_date');

		$this->_dbdef = $this->load->database('default', TRUE);
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

		$countTable = count($this->get_table());

		$data_local = $database_local->query($this->generate_sql($status_local, $data_form))->result();
		$data_could = $database_cloud->query($this->generate_sql($status_cloud, $data_form))->result();

		$array = array(
			'local'	=> $data_local,
			'cloud'	=> $data_could,
			'bar'	=> $this->list_bar($data_local,$data_could),
			'num'	=> $countTable
		);
		//$json = json_encode($array);
		return $array;
		// return $this->generate_script();
	}

	public function get_table()
	{
		$table = array(
			't_trx_assignment_regu',
			't_trx_boarding_passanger',
			't_trx_boarding_vehicle',
			't_trx_booking',
			't_trx_booking_passanger',
			't_trx_booking_vehicle',
			't_trx_invoice',
			't_trx_open_boarding',
			't_trx_payment',
			't_trx_prepaid',
			't_trx_sell'
		);

		return $table;
	}
	
	public function list_bar($data1, $data2)
	{

		$d_local = $data1;
		$d_cloud = $data2;
		$get_table = $this->get_table();
		

		$array = array(
			'list_table' => $get_table,
			'data_ctl_local' => $this->get_data_bar($d_local,0,'ctl'),
			'data_ctl_cloud' => $this->get_data_bar($d_cloud,1,'ctl'),
			'data_ltc_local' => $this->get_data_bar($d_local,0,'ltc'),
			'data_ltc_cloud' => $this->get_data_bar($d_cloud,1,'ltc')
		);

		return $array;
	}

	private function get_data_bar($data,$status,$ket)
	{
		$get_table = $this->get_table();
		if($status == 1){
			$s = 'local';
		}else{
			$s = 'cloud';
		}

		$data_array = [];

		foreach($get_table as $key){
			$field = $ket."_".$s."_".$key;
			$data_array[] = $data[0]->$field;
		}

		return $data_array;
	}

	private function generate_sql($status,$data)
	{
		$sql = " SELECT ".$this->generate_script($status,$data);

		return $sql;
	}

	private function generate_script($status,$data)
	{

		$start_date = $data['start_date'];
		$end_date = $data['end_date'];
		$server_id = $data['server_id'];

		if($status == 1){
			$s = 'local';
		}else{
			$s = 'cloud';
		}

		$get_table = $this->get_table();

		$sql = "";

		$where = " AND created_on BETWEEN '{$start_date}' AND '{$end_date}'";

		foreach($get_table as $data)
		{
			$sql .= "(SELECT COUNT(*) FROM app.{$data} WHERE server_id = '{$server_id}' {$where}) as ltc_{$s}_{$data}, ";
			$sql .= "(SELECT COUNT(*) FROM app.{$data} WHERE server_id = '00' {$where}) as ctl_{$s}_{$data}, ";
		}

		$sql = rtrim($sql, ', ');

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

}
