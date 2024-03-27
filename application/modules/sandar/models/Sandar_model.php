<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Sandar_model extends CI_Model {


  public function sandarList() 
  {
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
    $datefrom = $this->input->post('datefrom');
    $dateto = $this->input->post('dateto');
    $iLike  = trim(strtoupper($this->db->escape_like_str($search['value'])));

    $field = array(
        1 => 'ship_name',
        2 => 'created_on',
		3 => 'port_name',
		4 => 'dock_name',
    );

    $order_column = $field[$order_column];

    $where = "";

    if ((!empty($datefrom) and empty($dateto) )||(empty($datefrom) and !empty($dateto) ))
    {
        if(!empty($search['value']) )
        {
          $where = " where (
                    d.name ilike '%".$iLike."%'
                    or c.name ilike '%".$iLike."%'
                    or b.name ilike '%".$iLike."%'
                )
                and 
                (
                to_char(a.created_on,'yyyy-mm-dd')='$datefrom'
                or to_char(a.created_on,'yyyy-mm-dd')='$dateto'
                )
            ";  
        }
        else
        {
            $where =" where
                (
                to_char(a.created_on,'yyyy-mm-dd')='$datefrom'
                or to_char(a.created_on,'yyyy-mm-dd')='$dateto'
                )
            ";
        }
    }
    else if (!empty($datefrom) and !empty($dateto))
     {
       if(!empty($search['value']) )
        {
          $where = " where (
                    d.name ilike '%".$iLike."%'
                    or c.name ilike '%".$iLike."%'
                    or b.name ilike '%".$iLike."%'
                )
                and                 
                to_char(a.created_on,'yyyy-mm-dd') between '$datefrom'
                and '$dateto'
                
            ";  
        }
        else
        {
            $where =" where
                to_char(a.created_on,'yyyy-mm-dd') between '$datefrom'
                and '$dateto'
            ";
        }
     }
     else
     {
        if(!empty($search['value']) )
        {
          $where = " where (
                    d.name ilike '%".$iLike."%'
                    or c.name ilike '%".$iLike."%'
                    or b.name ilike '%".$iLike."%'
                )
            ";  
        }
        else
        {
            $where="";
        }
     }   
/*
    if (!empty($search['value'])) {
      $where = " where (
                    d.name ilike '%".trim($search['value'])."%'
                    or c.name ilike '%".trim($search['value'])."%'
                    or b.name ilike '%".trim($search['value'])."%'
				)
			";
    }
/*
    if (!empty($departdate))
    {
        $where.="
            and ( b.depart_date='$departdate')
        ";
    }

    if (!empty($gatein))
    {
        $where.="
            and ( to_char(a.created_on,'yyyy-mm-dd')='$gatein')
        ";
    }
    */

    $sql = "
            select c.id as dock_mtr_id, d.name as ship_name, c.name as dock_name, b.name as port_name, a.* from app.t_trx_dock a
            join app.t_mtr_port b on a.port_id = b.id
            join app.t_mtr_dock c on a.dock_id=c.id
            join app.t_mtr_ship d on a.ship_id=d.id
            {$where}

		";

    $query = $this->db->query($sql);
    $records_total = $query->num_rows();

    $sql .=" ORDER BY " . $order_column . " {$order_dir}";

    if ($length != -1) {
      $sql .=" LIMIT {$length} OFFSET {$start};";
    }

    $query = $this->db->query($sql);
    $rows_data = $query->result();

    $rows = array();
    $i = ($start + 1);
    foreach ($rows_data as $row) {
      $row->number = $i;
      $row->created_on = format_dateTime($row->created_on);
      $rows[] = $row;

      $i++;
    }

    return array(
        'draw' => $draw,
        'recordsTotal' => $records_total,
        'recordsFiltered' => $records_total,
        'data' => $rows
    );
  }
    public function selectData($table,$field,$sort)
    {
        return $this->db->order_by($field,$sort)->get($table);
    }  

    public function getMaxId($table,$field)
    {
        return $this->db->query("select max($field) as max_id from $table")->row();

    }
    public function insert($table,$data)
    {
        $this->db->insert("select max($field) as max_id from $table")->row();

        if ($this->db->trans_status() === FALSE)
        {
                $this->db->trans_rollback();
                return FALSE;
        }
        else
        {
                $this->db->trans_commit();
                return TRUE;
        }

    }

    public function update($ship_id,$status,$date,$dermaga_id,$port_id)
    {
        
        $this->db->query("update app.t_tmp_passanger_info set ship_id=$ship_id , status=$status, date='".$date."' 
            where dock_id=$dermaga_id and port_id=$port_id");

        if ($this->db->trans_status() === FALSE)
        {
                $this->db->trans_rollback();
                return FALSE;
        }
        else
        {
                $this->db->trans_commit();
                return TRUE;
        }

    }
}
