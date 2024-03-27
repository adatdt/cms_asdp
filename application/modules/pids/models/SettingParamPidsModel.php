<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Adat <adatdt@gmail.com>
 * @copyright  2021
 *
 */

class SettingParamPidsModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_module = 'pids/settingParamPids';
    }

    public function dataList()
    {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');
        $order = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir = strtoupper($order[0]['dir']);
        // $iLike = trim(strtoupper($this->db->escape_like_str($search['value'])));

        $searchData = $this->input->post('searchData');
        $searchName = $this->input->post('searchName');
        $iLike        = trim(str_replace(array("'",'"'),"",$searchData));

        $appIdentity=$this->select_data("app.t_mtr_identity_app", "" )->row();

        if($appIdentity->port_id<>0) // jika aplokasi bukan di cloude
        {
            $port=$appIdentity->port_id;
        }
        else
        {
            if(!empty($this->session->userdata("port_id")))
            {
                $port=$this->session->userdata("port_id");
            }
            else
            {
                $port=$this->enc->decode($this->input->post("port"));
            }
        }

        $field = array(
            0 => 'created_on',
            1=>"param_name",
            2=>"param_value",
            3=>"param_type",
            4=>"info",
            5=>"port_name",
            6=>"status",

        );

        $order_column = $field[$order_column];

        $where = " WHERE pids.status not in (-5) ";
        
        if(!empty($port))
        {
            $where .= " and (pids.port_id={$port}) ";
        }

        if ( ! empty($search['value']))
        {
            $where .= "and (
                        pids.param_name ilike '%".trim($search['value'])."%' 
                        or pids.param_value ilike '%".trim($search['value'])."%'  
                        or pids.value_type ilike '%".trim($search['value'])."%'
                        or pids.info ilike '%".trim($search['value'])."%'
              )";
        }

        if(!empty($searchData))
        {

            if($searchName=='name')
            {
                $where .= "and ( pids.param_name ilike '%".$iLike."%')";

            }
            else if($searchName=='valueParam')
            {

                $where .= "and (pids.param_value ilike '%".$iLike."%' )";                

            }
            else if($searchName=='typeParam')
            {
                $where .= "and (pids.param_type ilike '%".$iLike."%')";

            }            
            else if($searchName=='info')
            {
                $where .= "and (pids.info ilike '%".$iLike."%')";
            }
            else
            {
                $where .= "";
            }                        
        }


        $sql = "
                SELECT 
                    pr.name as port_name,
                    pids.id,
                    pids.param_name,
                    pids.param_value,
                    pids.param_type,
                    pids.status,
                    pids.info,
                    pids.created_on
                from app.t_mtr_custom_param_pids pids
                left join app.t_mtr_port pr on pids.port_id=pr.id
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
            $id_enc = $this->enc->encode($row->id);
            $row->number = $i;
            $nonaktif = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
            $aktif = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

            $row->id = $row->id;
            $edit_url = site_url($this->_module."/edit/{$id_enc}");
            $delete_url = site_url($this->_module."/action_delete/{$id_enc}");

            $row->actions ="";

            $row->category_name="";

            if ($row->status == 1)
            {
                $row->actions = generate_button_new($this->_module, 'edit', $edit_url);
                $row->status = success_label('Aktif');
                $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction2(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
            }
            else
            {
                $row->status = failed_label('Tidak Aktif');
                $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction2(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
            }

            $row->no = $i;
            // $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
            
            // button hapus
            $row->actions .=  generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction2(\'Apakah Anda yakin menghapus data ini ?\', \''.$delete_url.'\')" title="Hapus"> <i class="fa fa-trash-o"></i> </button> ');

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

