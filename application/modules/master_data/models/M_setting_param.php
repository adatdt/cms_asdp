<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class M_setting_param extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_module = 'master_data/setting_param';
    }

    public function dataList()
    {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');
        // $category_name = $this->input->post('category');
        // $team_name = $this->input->post('team');
        $order = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir = strtoupper($order[0]['dir']);

        // $iLike = trim(strtoupper($this->db->escape_like_str($search['value'])));
        $searchData=$this->input->post("searchData");
        $searchName=$this->input->post("searchName");
        $iLike        = trim(strtoupper(str_replace(array("'",'"'),"", $searchData) ));

        $field = array(
            0 => 'param_id',
            1 => 'param_name',
            2 => 'param_value',
            3 => 'type',
            4 => 'value_type',
            5 => 'info',
            // 6 => 'category_name',

        );

        $order_column = $field[$order_column];

        $where = " WHERE status not in (-5) ";

        // if ( ! empty($search['value']))
        // {
        //     $where .= "and (param_name ilike '%".trim($search['value'])."%' or param_value ilike '%".trim($search['value'])."%'  or
        //       type ilike '%".trim($search['value'])."%' or value_type ilike '%".trim($search['value'])."%'
        //       or info ilike '%".trim($search['value'])."%'
        //       )";
        // }

        // if (!empty($category_name)){
        //     if ($category_name == "lainnya") {
        //         $where .= "and category_name is null";
        //     }
        //     else {
        //         $where .= "and category_name = '". $category_name ."'";
        //     }
            
        // }

         if(!empty($searchData))
        {
            if($searchName=='param_name')
            {
                $where .=" and ( param_name ilike '%{$iLike}%')";
            }
            else if ($searchName=='param_value') {

                $where .=" and ( param_value ilike '%{$iLike}%')";
            }
            else if ($searchName=='type') {

                $where .=" and ( type ilike '%{$iLike}%')";
            }
            else if ($searchName=='value_type') {

                $where .=" and ( value_type ilike '%{$iLike}%')";
            }
            else if ($searchName=='info') {

                $where .=" and ( info ilike '%{$iLike}%')";
            }
            else
            {
                $where .="";
            }
        }
        // $sql = "
        //       select * from app.t_mtr_custom_param
        //       {$where}
        //      ";

        $sql           = 'SELECT  * FROM app.t_mtr_custom_param '.$where.' ';

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
            $id_enc = $this->enc->encode($row->param_id);
            $row->number = $i;
            $nonaktif = site_url($this->_module."/action_change/".$this->enc->encode($row->param_id.'|-1'));
            $aktif = site_url($this->_module."/action_change/".$this->enc->encode($row->param_id.'|1'));

            $row->param_id = $row->param_id;
            $edit_url = site_url($this->_module."/edit/{$id_enc}");
            $delete_url = site_url($this->_module."/action_delete/{$id_enc}");

            $row->actions ="";

            if ($row->category_name == null) {
				$row->category_name = 'Lainnya';
			}

            if ($row->status == 1)
            {
                $row->actions = generate_button_new($this->_module, 'edit', $edit_url);
                $row->status = success_label('Aktif');
                $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
            }
            else
            {
                $row->status = failed_label('Tidak Aktif');
                $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
            }

            $row->no = $i;
            $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);

            $rows[] = $row;
            unset($row->assignment_code);

            $i++;
        }

        return array(
            'draw' => $draw,
            'recordsTotal' => $records_total,
            'recordsFiltered' => $records_total,
            'data' => $rows,
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

