<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 *
 */

class M_ftp_brilink extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_module = 'monitoring/ftp_brilink';
    }

    public function dataList()
    {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
				$draw = $this->input->post('draw');
				$dateFrom = $this->input->post('dateFrom');
				$dateTo = $this->input->post('dateTo');
				$category_name = $this->input->post('category');
				$tipe = $this->enc->decode($this->input->post('tipe'));
        $team_name = $this->input->post('team');
        $order = $this->input->post('order');
        $order_column = $order[0]['column'];
				$order_dir = strtoupper($order[0]['dir']);
				
				$searchName=$this->input->post('searchName');
				$searchData=trim($this->input->post('searchData'));
				$ilike= str_replace(array('"',"'"), "", $searchData);
        // $iLike = trim(strtoupper($this->db->escape_like_str($search['value'])));

        $field = array(
            0 => 'id',
            1 => 'file_name',
            2 => 'transaction_date',
            3 => 'description',
            4 => 'ftp_type',
            5 => 'created_on',

        );

        $order_column = $field[$order_column];

        $where = " WHERE (to_char(a.transaction_date,'yyyy-mm-dd') between '".$dateFrom."' and '".$dateTo."' )";

				if(!empty($tipe))
				{
					$where .= " and (upper(a.ftp_type)  =upper('".$tipe."') )";
				}

				if(!empty($searchData))
				{
					if($searchName=='file_name')
					{
						$where .=" and a.file_name ilike '%{$ilike}%' ";
					}
				}

        $sql = "SELECT 
									a.*, b.description
								from 
									app.t_trx_monitoring_ftp_brilink a
								left join app.t_mtr_status b on b.status = a.status and tbl_name = 't_trx_monitoring_ftp_brilink'
									{$where}
							  ";

        $query = $this->db->query($sql);
        $records_total = $query->num_rows();
        $sql .= " ORDER BY ".$order_column." {$order_dir}";

        if ($length != -1)
        {
            $sql .= " LIMIT {$length} OFFSET {$start}";
        }

        $query = $this->db->query($sql);
        $rows_data = $query->result();

        $rows = array();
        $i = ($start + 1);

        foreach ($rows_data as $row)
        {
					$row->no = $i;
					$row->created_on=format_dateTimeHis($row->created_on);
					$rows[] = $row;
					$i++;
        }

        return array(
            'draw' => $draw,
            'recordsTotal' => $records_total,
            'recordsFiltered' => $records_total,
            'data' => $rows,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
        );
    }

    public function select_data($table, $where)
    {
        return $this->db->query("select * from $table $where");
    }

    public function insert_data($table, $data)
    {
        $this->db->insert($table, $data);
    }

    public function update_data($table, $data, $where)
    {
        $this->db->where($where);
        $this->db->update($table, $data);
    }

    public function delete_data($table, $data, $where)
    {
        $this->db->where($where);
        $this->db->delete($table, $data);
    }

}

