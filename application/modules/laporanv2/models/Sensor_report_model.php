<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sensor_report_model extends MY_Model{

	public function __construct() {
		parent::__construct();
		$this->_module   = 'laporan/menu_rekonsiliasi';

        // $this->dbView=$this->load->database("dbView",TRUE);
        $this->dbView=checkReplication();
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$port = $this->enc->decode($this->input->post('port'));
		$shipclass = $this->enc->decode($this->input->post('class'));
		$status = $this->enc->decode($this->input->post('status'));
		$shift = $this->enc->decode($this->input->post('shift'));
		$regu = $this->enc->decode($this->input->post('regu'));
		$petugas = $this->enc->decode($this->input->post('petugas'));
		$loket = $this->enc->decode($this->input->post('loket'));
		$keter = $this->enc->decode($this->input->post('keter'));

		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$searchName=$this->input->post('searchName');
		$searchData=trim($this->input->post('searchData'));
		$ilike= str_replace(array('"',"'"), "", $searchData);

		if($this->get_identity_app()==0)
		{
			// mengambil port berdasarkan port di user menggunakan session
	        if(!empty($this->session->userdata('port_id')))
	        {
	            $port_origin=$this->session->userdata('port_id');
	        }
	        else
	        {
	            $port_origin = $this->enc->decode($this->input->post('port_origin'));
	        }
		}
		else
		{
			$port_origin=$this->get_identity_app();
		}

		$field = array(
			0 =>'id',
			1 =>'booking_code',
			2 =>'ticket_number',
			3 =>'ship_class',
			4 =>'nopol_bok',
			5 =>'nopol_cek',
			6 => 'panjang_bok',
			7 => 'panjang_cek',
			8 => 'lebar_bok',
			9 => 'lebar_cek',
			10 => 'tinggi_bok',
			11 => 'tinggi_cek',
			12 =>'batasan',
			13 =>'hasil_timbang',
			14 =>'gol_bok',
			15 =>'gol_cek',
			16 =>'user_petugas_loket',
			17 =>'nama_loket',
			18 => 'nama_spv',
			19 => 'status',
			20 => 'nama_kapal',
			21 => 'waktu',
			22 => 'keterangan',
		);

		$order_column = $field[$order_column];

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE cv.status = 1 and cv.created_on >= '". $dateFrom . "' and cv.created_on < '" . $dateToNew . "'";

		if(!empty($port))
		{
			$where .= " and (bv.origin = ".$port.")";
		}

		if(!empty($shipclass))
		{
			$where .= " and (bv.ship_class = ".$shipclass.")";
		}

		if(!empty($shift))
		{
			$where .= " and (obo.shift_id = ".$shift.")";
		}

		if(!empty($regu))
		{
			$where .= " and (asr.team_code = '".$regu."')";
		}

		if(!empty($petugas))
		{
			$where .= " and (usr_lkt.id = ".$petugas.")";
		}

		if(!empty($loket))
		{
			$where .= " and (dt.terminal_code = '".$loket."')";
		}

		if ($status != "") {
			if ($status == 4) {
					$status = "4, 7";
			}
			if ($status == 5) {
					$status = "5, 8";
			}
			$where .= " AND bv.status IN ({$status}) ";
		}

		if(!empty($keter))
		{
			if ($keter == 1) {
				$where .= " and (ovpd.booking_code is not null)";
			}
			if ($keter == 2) {
				$where .= " and (unpd.booking_code is not null)";
			}
		}

		if(!empty($searchData))
		{
			if($searchName=='bookingCode')
			{
				$where .=" and cv.booking_code ilike '%{$ilike}%' ";
			}
			else if($searchName=='ticketNumber')
			{
				$where .=" and cv.ticket_number ilike '%{$ilike}%' ";
			}
			else if($searchName=='nopol')
			{
				$where .=" and cv.id_number_booking ilike '%{$ilike}%' or cv.id_number_checkin ilike '%{$ilike}%' ";
			}
		}


		$sql = $this->qry($where);

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
			$row->number = $i;

			$row->checkBox ="";
			$row->status_approved ="";

			$url = site_url("transaction/ticket_tracking/index/{$row->ticket_number}");
			$row->ticket_number = '<a href="'.$url.'" target="_blank">'.$row->ticket_number.'</a>';

			if (isset($row->nopol_cek)) {
				if ($row->nopol_man == $row->nopol_cek) {
					$row->nopol_comp = 'Sesuai';
				}
				else {
					$row->nopol_comp = 'Tidak Sesuai';
				}
			}
			else {
				$row->nopol_cek = '-';
				$row->nopol_comp = '-';
			}

			$row->lebar_bok = '-';

			$row->panjang = ((int)$row->panjang_man) - ((int)$row->panjang_cek);
			if ($row->panjang < 0) {
				$row->panjang = "<span style='color:red'>".$row->panjang."<span>";
			}

			$row->lebar = ((int)$row->lebar_man) - ((int)$row->lebar_cek);
			if ($row->lebar < 0) {
				$row->lebar = "<span style='color:red'>".$row->lebar."<span>";
			}

			$row->tinggi = ((int)$row->tinggi_man) - ((int)$row->tinggi_cek);
			if ($row->tinggi < 0) {
				$row->tinggi = "<span style='color:red'>".$row->tinggi."<span>";
			}

			if ($row->hasil_timbang <= $row->batasan) {
				$row->berat_status = 'Sesuai';
			}
			else {
				$row->berat_status = 'Overload';
			}

			$d = explode(', ', $row->gol_cek);

			if(in_array($row->gol_man, $d)){
				$row->gol_comp = success_label("Sesuai");
			}
			else {
				$row->gol_comp= failed_label("Tidak Sesuai");
			}

			if ($row->keterangan == '-') {
				$row->appr_status = '-';
				$row->appr_user = '-';
				$row->appr_tanggal = '-';
				$row->appr_aksi = '-';
			}
			else {
				$row->appr_status = failed_label('Belum Approve');
				$row->appr_user = '-';
				$row->appr_tanggal = '-';
				$row->appr_aksi = '-';
				$row->checkBox .='	<div class="checkbox">
		                            	<label>
		                                	<input type="checkbox" value="'.($row->id).'" name="check" class="myCheck"  >
		                                	<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
		                            	</label>
	                                </div>' ;
			}

			// $nonaktif = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|-1'));
     	// 	$aktif = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

			// $id=$this->enc->encode($row->id);
			// $edit_url 	 = site_url($this->_module."/edit/{$id}");
     	// 	$delete_url  = site_url($this->_module."/action_delete/{$id}");

     		
     	// 	// $row->total_amount=idr_currency($row->total_amount);
     	// 	$row->created_on=format_dateTimeHis($row->created_on);
     		$row->no=$i;

			$rows[] = $row;
			// unset($row->id);

			$i++;
		}
		// print_r($rows);exit;

		return array(
			'draw'           => $draw,
			'recordsTotal'   => $records_total,
			'recordsFiltered'=> $records_total,
			'data'           => $rows
		);
	}

	public function download() {

		$dateFrom = $this->input->get('dateFrom');
		$dateTo = $this->input->get('dateTo');
		$port = $this->enc->decode($this->input->get('port'));
		$shipclass = $this->enc->decode($this->input->get('class'));
		$status = $this->enc->decode($this->input->get('status'));
		$shift = $this->enc->decode($this->input->get('shift'));
		$regu = $this->enc->decode($this->input->get('regu'));
		$petugas = $this->enc->decode($this->input->get('petugas'));
		$loket = $this->enc->decode($this->input->get('loket'));
		$keter = $this->enc->decode($this->input->get('keter'));

		$searchName=$this->input->get('searchName');
		$searchData=trim($this->input->get('searchData'));
		$ilike= str_replace(array('"',"'"), "", $searchData);

		if($this->get_identity_app()==0)
		{
			// mengambil port berdasarkan port di user menggunakan session
	        if(!empty($this->session->userdata('port_id')))
	        {
	            $port=$this->session->userdata('port_id');
	        }
	        else
	        {
	            $port = $this->enc->decode($this->input->get('port'));
	        }
		}
		else
		{
			$port=$this->get_identity_app();
		}

		$dateToNew = date('Y-m-d', strtotime($dateTo . ' +1 day'));

		$where = " WHERE cv.status = 1 and cv.created_on >= '". $dateFrom . "' and cv.created_on < '" . $dateToNew . "'";

		if(!empty($port))
		{
			$where .= " and (bv.origin = ".$port.")";
		}

		if(!empty($shipclass))
		{
			$where .= " and (bv.ship_class = ".$shipclass.")";
		}

		if(!empty($shift))
		{
			$where .= " and (obo.shift_id = ".$shift.")";
		}

		if(!empty($regu))
		{
			$where .= " and (asr.team_code = '".$regu."')";
		}

		if(!empty($petugas))
		{
			$where .= " and (usr_lkt.id = ".$petugas.")";
		}

		if(!empty($loket))
		{
			$where .= " and (dt.terminal_code = '".$loket."')";
		}

		if ($status != "")
		{
			if ($status == 4) {
					$status = "4, 7";
			}
			if ($status == 5) {
					$status = "5, 8";
			}
			$where .= " AND bv.status IN ({$status}) ";
		}

		if(!empty($keter))
		{
			if ($keter == 1) {
				$where .= " and (ovpd.booking_code is not null)";
			}
			if ($keter == 2) {
				$where .= " and (unpd.booking_code is not null)";
			}
		}

		if(!empty($searchData))
		{
			if($searchName=='bookingCode')
			{
				$where .=" and cv.booking_code ilike '%{$ilike}%' ";
			}
			else if($searchName=='ticketNumber')
			{
				$where .=" and cv.ticket_number ilike '%{$ilike}%' ";
			}
			else if($searchName=='nopol')
			{
				$where .=" and cv.id_number_booking ilike '%{$ilike}%' or cv.id_number_checkin ilike '%{$ilike}%' ";
			}
		}


		$sql = $this->qry($where) . "ORDER BY ID DESC";

		$query = $this->dbView->query($sql);
		$rows_data = $query->result();

		$rows 	= array();
		$i = 1;
		foreach ($rows_data as $row) {
			$row->number = $i;

			if (isset($row->nopol_cek)) {
				if ($row->nopol_man == $row->nopol_cek) {
					$row->nopol_comp = 'Sesuai';
				}
				else {
					$row->nopol_comp = 'Tidak Sesuai';
				}
			}
			else {
				$row->nopol_cek = '-';
				$row->nopol_comp = '-';
			}

			if (isset($row->panjang_man)) {
				$row->panjang = ((int)$row->panjang_man) - ((int)$row->panjang_cek);
			}
			else {
				$row->panjang_man = '-';
				$row->panjang = '-';
			}


			$row->lebar = ((int)$row->lebar_man) - ((int)$row->lebar_cek);

			$row->tinggi = ((int)$row->tinggi_man) - ((int)$row->tinggi_cek);

			if ($row->hasil_timbang <= $row->batasan) {
				$row->berat_status = 'Sesuai';
			}
			else {
				$row->berat_status = 'Overload';
			}

			$d = explode(', ', $row->gol_cek);

			if(in_array($row->gol_man, $d)){
				$row->gol_comp = "Sesuai";
			}
			else {
				$row->gol_comp= "Tidak Sesuai";
			}

			if ($row->keterangan == '-') {
				$row->appr_status = '-';
				$row->appr_user = '-';
				$row->appr_tanggal = '-';
				$row->appr_aksi = '-';
			}
			else {
				$row->appr_status = '-';
				$row->appr_user = '-';
				$row->appr_tanggal = '-';
				$row->appr_aksi = '-';
			}

     		$row->no=$i;

			$rows[] = $row;
			// unset($row->id);

			$i++;
		}
		return $rows;
	}

	public function qry ($where) {
		return $data = "SELECT
											cv.id,
											cv.booking_code,
											cv.ticket_number,
											sc.name as ship_class,
											cv.id_number_booking as nopol_bok,
											cv.id_number_checkin as nopol_man,
											bv.length as panjang_bok,
											cv.length_cam as panjang_cek,
											cv.length as panjang_man,
											-- bv.width  as lebar_bok,
											cv.width_cam as lebar_cek,
											cv.width  as lebar_man,
											bv.height as tinggi_bok,
											cv.height_cam as tinggi_cek,
											cv.height as tinggi_man,
											vc.max_weight as batasan,
											cv.weighbridge as hasil_timbang,
											vc1.name as gol_bok,
											array_to_string(array_agg(DISTINCT vc2.name), ', ') as gol_cek,
											vc.name as gol_man,
											array_to_string(array_agg(usr_lkt.username), ', ') as user_petugas_loket,
											dt.terminal_name as nama_loket,
											usr_spv.username as nama_spv, 
											st.description as status,
											dc.name as dermaga,
											ship.name as nama_kapal,
											bv.updated_on as waktu,
											(
												case
												when ovpd.booking_code is not null
													then 'Over Paid'
												when unpd.booking_code is not null
													then 'Under Paid'
												when ovpd.booking_code is not null and unpd.booking_code is not null
													then 'Over Paid and Under Paid'
												else '-' end
											) as keterangan,
											obo.shift_id 
										from
											app.t_trx_check_in_vehicle cv
											left join app.t_trx_booking_vehicle bv on bv.ticket_number = cv.ticket_number
											left join app.t_mtr_ship_class sc on sc.id = bv.ship_class
											left join app.t_mtr_vehicle_class vc on vc.id = cv.vehicle_class_checkin::int
											left join app.t_mtr_vehicle_class vc1 on vc1.id = cv.vehicle_class_booking::int
											left join app.t_mtr_vehicle_class vc2 on (cv.length_cam) between vc2.min_length and vc2.max_length and vc2.status = 1 and vc2.name not in ('Golongan I', 'Golongan II', 'Golongan III')
											left join app.t_trx_opening_balance obo on obo.ob_code = cv.created_by
											left join app.t_trx_assignment_regu asr on asr.assignment_code = obo.assignment_code
											left join core.t_mtr_user usr_spv on usr_spv.id = asr.supervisor_id
											left join app.t_mtr_device_terminal dt on dt.terminal_code = obo.terminal_code 
											left join app.t_trx_assignment_user_pos aup on aup.assignment_code = obo.assignment_code and aup.user_id != asr.supervisor_id
											left join core.t_mtr_user usr_lkt on usr_lkt.id = aup.user_id 
											left join app.t_mtr_status st on bv.status = st.status and st.tbl_name = 't_trx_booking_vehicle'
											left join app.t_trx_boarding_vehicle bov on bov.ticket_number = cv.ticket_number
											left join app.t_mtr_dock dc on dc.id = bov.dock_id and dc.port_id = bov.port_id
											left join app.t_trx_open_boarding ob on ob.boarding_code = bov.boarding_code 
											left join app.t_trx_schedule scd on scd.schedule_code = ob.schedule_code 
											left join app.t_mtr_ship ship on ship.id = scd.ship_id
											left join app.t_trx_over_paid ovpd on ovpd.booking_code = cv.booking_code
										--	left join app.t_trx_under_paid unpd on unpd.booking_code = cv.booking_code
											left join (
												select
													u.booking_code, u.trans_number
												from
													app.t_trx_under_paid u
												left join app.t_trx_payment p on p.trans_number = u.trans_number
											) unpd on unpd.booking_code = cv.booking_code
										{$where}
										group by 
										cv.booking_code,
										cv.ticket_number,
										sc.name, 
										cv.id_number_booking,
										cv.id_number_checkin,
										bv.length,
										cv.length,
										cv.length_cam,
										-- bv.width,
										cv.width,
										cv.width_cam,
										bv.height,
										cv.height,
										cv.height_cam,
										vc.max_weight,
										cv.weighbridge,
										vc1.name,
										vc.name,
										usr_spv.username,
										dt.terminal_name,
										st.description,
										dc.name,
										ship.name,
										bv.updated_on,
										ovpd.booking_code,
										unpd.booking_code,
										cv.id,
										obo.shift_id 
											";
	}

	public function getport()
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_port WHERE status=1")->result();
	}

	public function getregu()
	{
		return $this->dbView->query("SELECT * FROM core.t_mtr_team WHERE status=1")->result();
	}

	public function getpetugas()
	{
		return $this->dbView->query("SELECT * FROM core.t_mtr_user WHERE user_group_id = 4 AND status=1")->result();
	}

	public function getshift()
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_shift WHERE status=1")->result();
	}

	public function getclass()
	{
		return $this->dbView->query("SELECT * FROM app.t_mtr_ship_class WHERE status=1")->result();
	}

	public function get_team($port_id)
	{
		return $this->dbView->query("SELECT team_code,team_name FROM core.t_mtr_team WHERE port_id = $port_id and status = 1 ORDER BY team_name ASC")->result();
	}

	public function get_loket($port_id)
	{
		return $this->dbView->query("SELECT terminal_code,terminal_name FROM app.t_mtr_device_terminal WHERE port_id = $port_id and status = 1 ORDER BY terminal_name ASC")->result();
	}

	public function get_lintasan($port, $datefrom, $dateto, $ship_class)
	{
		$where_port = "";
		$where_ship_class = "";

		if ($port != "") {
			$where_port = " AND UP.port_id = $port";
		}

		if ($ship_class != "") {
			$where_ship_class = " AND BO.ship_class = $ship_class";
		}

		$sql = "SELECT DISTINCT
					PO.name as origin,
					PD.name as destination
				FROM
					app.t_trx_assignment_user_pos UP
					JOIN app.t_trx_opening_balance OB ON OB.assignment_code = UP.assignment_code
					JOIN app.t_trx_sell S ON S.ob_code = OB.ob_code
					JOIN app.t_trx_booking BO ON BO.trans_number = S.trans_number $where_ship_class
					JOIN app.t_mtr_port PO ON PO.id = BO.origin
					JOIN app.t_mtr_port PD ON PD.id = BO.destination
				WHERE
					UP.assignment_date BETWEEN '$datefrom' AND '$dateto'
				$where_port";

		if ($this->dbView->query($sql)->num_rows() > 0) {
			return $this->dbView->query($sql);
		} else {
			return false;
		}
	}


	public function select_data($table, $where="")
	{
		return $this->dbView->query("select * from $table $where");
	}

	public function insert_data($table,$data)
	{
		$this->dbView->insert($table, $data);
	}

	public function update_data($table,$data,$where)
	{
		$this->dbView->where($where);
		$this->dbView->update($table, $data);
	}

	public function delete_data($table,$data,$where)
	{
		$this->dbView->where($where);
		$this->dbView->delete($table, $data);
	}

	public function get_identity_app()
	{
		$data=$this->dbView->query(" select * from app.t_mtr_identity_app")->row();

		return $data->port_id;
	}

	function get_channel(){
		$data  = array(''=>'Pilih');
		$query = $this->dbView->query(" SELECT DISTINCT channel FROM app.t_trx_invoice where channel<>'' ORDER BY channel asc ")->result();

		foreach ($query as $key => $value) {
		 	$data[$this->enc->encode($value->channel)] = strtoupper(str_replace('_', ' ', $value->channel));
		 } 

		return array_unique($data);
	}

}