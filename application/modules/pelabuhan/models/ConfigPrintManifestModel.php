<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : ConfigPrintManifestModel
 * -----------------------
 *
 * @author     Adat <adatdt@gmail.com>
 * @copyright  2023
 *
 */

class ConfigPrintManifestModel extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_module   = 'pelabuhan/configPrintManifest';
    }

    public function shipList() {
        $start        = $this->input->post('start');
        $length       = $this->input->post('length');
        $draw         = $this->input->post('draw');
        $search       = $this->input->post('search');
        $order        = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir    = strtoupper($order[0]['dir']);
        $iLike        = trim(str_replace(array("`",",","'",'"',";"),"",$search['value']));

        $field = array(
            0 => 'id',
            1 => 'port_id',
            2 => 'ship_class',
            3 => 'configuration_status'
        );

        $order_column = $field[$order_column];
        $where        = " WHERE a.status not in (-5) ";

        if (!empty($search['value'])) {
            $where .= " AND ( tmp.name ilike '%".$iLike."%' or tmsc.name ilike '%".$iLike."%' ) ";
        }


        $sql = "SELECT
                    a.configuration_status,
                    a.id,
                    a.status ,
                    tmp.name as port_name,
                    tmsc.name as ship_class_name
                from  app.t_mtr_config_print_manifest a
                join app.t_mtr_port tmp on a.port_id = tmp.id 
                join app.t_mtr_ship_class tmsc on a.ship_class = tmsc.id 
                {$where}
            ";

        $query         = $this->db->query($sql);
        $records_total = $query->num_rows();
        $sql          .= " ORDER BY " . $order_column . " {$order_dir}";

        if ($length != -1){
            $sql .=" LIMIT {$length} OFFSET {$start}";
        }

        $query     = $this->db->query($sql);
        $rows_data = $query->result();
        $rows      = array();
        $i         = ($start + 1);

        foreach ($rows_data as $row) {
            $row->number = $i;
            $row->id    = $this->enc->encode($row->id);
            $edit_url   = site_url($this->_module."/edit/{$row->id}");
            $delete_url = site_url($this->_module."/action_delete/{$row->id}");

            $nonaktif    = site_url($this->_module."/disable/".$this->enc->encode($row->id.'|0'));
            $aktif       = site_url($this->_module."/enable/".$this->enc->encode($row->id.'|1'));

            $row->actions  = '';

            $row->actions .= generate_button_new($this->_module, 'edit', $edit_url);
            if($row->configuration_status == 't'){
                $row->configuration_status   = success_label('Aktif');
            }
            else
            {
                $row->configuration_status   = failed_label('Tidak Aktif');

            }

            $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
            $rows[] = $row;
            unset($row->id);

            $i++;
        }

        return array(
            'draw' => $draw,
            'recordsTotal' => $records_total,
            'recordsFiltered' => $records_total,
            'data' => $rows_data
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
