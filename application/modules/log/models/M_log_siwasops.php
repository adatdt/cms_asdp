<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Fajar Rasia A <alfajrduta@gmail.com>
 * @copyright  2020
 *
 */

class M_log_siwasops extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'log/log_siwasops';
	}

    public function dataList(){
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$draw = $this->input->post('draw');
		$search = $this->input->post('search');
		$searchData = $this->input->post('searchData');
		$searchName = $this->input->post('searchName');
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));


		$dateFrom = trim($this->input->post('dateFrom'));
		$dateTo = trim($this->input->post('dateTo'));

		$order = $this->input->post('order');
		$order_column = $order[0]['column'];
		$order_dir = strtoupper($order[0]['dir']);
		$iLike        = trim(strtoupper($this->db->escape_like_str($searchData)));

		// mengambil port di port id user
		if($this->get_identity_app()==0)
		{
			if(!empty($this->session->userdata("port_id")))
			{
				$port = $this->session->userdata("port_id");
			} else {
				$port = $this->enc->decode($this->input->post('port'));
			}
		}
		else
		{
			$port = $this->get_identity_app();
		}



		$field = array(
			0 =>'id',
            1 =>'boarding_code',
            2 =>'boarding_date_start',
			3 => 'kapal',
			4 => 'pelabuhan',
			5 => 'dermaga',
			6 => 'created_on',
			7 => 'status'
		);

		$order_column = $field[$order_column];

		$where = "";
		$where .= " WHERE a.boarding_date_start between '" . $dateFrom . "' AND '" . $dateTo . " 23:59:59'   ";

		if (!empty($port))
		{
			$where .="AND (a.port_id=".$port.")";
		}
		
		if(!empty($searchData))
		{
			if($searchName=="boardingCode")
			{
				$where .= "and (a.boarding_code = '" . $iLike . "')";

			}
			else if($searchName=="shipName")
			{
				$where .= "and (b.name ilike '%" . $iLike . "%')";

			}
			else if($searchName=="dockName")
			{
				$where .= "and (d.name ilike '%" . $iLike . "%')";

			}
			else
			{
				$where .= "and (a.boarding_code = '" . $iLike . "')";
			}
		}

		$sql 		   = " 
						SELECT * FROM (

							SELECT 
							DISTINCT ON (a.boarding_code) a.id,
							a.boarding_code ,
							a.ship_id ,
							a.boarding_date_start ,
							a.dock_id ,
							a.port_id, 
							a.status,
							a.created_on,
							b.name AS kapal, c.name AS pelabuhan, d.name AS dermaga 
							FROM app.t_log_siwasops a 
							LEFT JOIN app.t_mtr_ship b ON a.ship_id=b.id 
							LEFT JOIN app.t_mtr_port c ON a.port_id=c.id 
							LEFT JOIN app.t_mtr_dock d ON a.dock_id=d.id 
							{$where} 
							ORDER BY a.boarding_code, a.id DESC
		
							) as data_log

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
			$row->number = $i;

            $row->boarding_date = empty($row->boarding_date_start) ? "" : format_dateTimeHis($row->boarding_date_start);
            $row->send_date = empty($row->created_on) ? "" : format_dateTimeHis($row->created_on);


			$detail_url 	 = site_url($this->_module."/detail/{$row->boarding_code}");
            $row->actions  = generate_button_new($this->_module, 'detail', $detail_url);

            if($row->status == 1){
				$row->status   = success_label('Success');
			}else{
				$row->status   = failed_label('Failed');
                $row->actions .= '<button class="btn btn-sm btn-warning" onclick="ConfirmResend(`Apakah anda ingin melakukan resend data manifest ?`, `'.$row->boarding_code.'`)" title="Resend" id="btnresend'.$row->boarding_code.'">Resend</button>';
            }
			

     		$row->no=$i;

			$rows[] = $row;
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


	public function select_data($table, $where="")
	{
		return $this->db->query("select * from $table $where");
	}

    public function get_identity_app()
	{
		$data=$this->db->query("select * from app.t_mtr_identity_app")->row();
		return $data->port_id;
	}

    public function data_boarding($boarding_code)
    {
        $sql = "SELECT 
        a.boarding_code,
        b.name AS kapal,
        c.name AS pelabuhan,
        d.name AS dermaga
        FROM app.t_trx_open_boarding a
        LEFT JOIN app.t_mtr_ship b ON a.ship_id=b.id
        LEFT JOIN app.t_mtr_port c ON a.port_id=c.id
        LEFT JOIN app.t_mtr_dock d ON a.dock_id=d.id 
        WHERE a.boarding_code='{$boarding_code}'  
        ";

        return $this->db->query($sql);
    }

    public function list_log_siwasops($boarding_code)
    {
        $sql = "SELECT * FROM app.t_log_siwasops WHERE boarding_code='{$boarding_code}' ORDER BY created_on DESC";
        return $this->db->query($sql);
    }

}