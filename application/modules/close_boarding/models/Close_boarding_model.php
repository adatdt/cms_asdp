<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Close_boarding_model extends CI_Model {


  public function closeBoardingList() 
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

    $field = array(
        1 => 'ship_name',
        2 => 'created_on',
		3 =>'port_name',
		4 =>'dock_name',
    );

    $order_column = $field[$order_column];

    $where = "where a.id in (select dock_id from app.t_trx_boarding)";

    $sql = "
select aa.updated_on as close_boarding_date, aa.id as boarding_id, a.id as dock_id2, d.name as ship_name, c.name as dock_name, b.name as port_name,(select created_on from app.t_trx_boarding where dock_id=a.id  ) as open_boarding_date, a.*
from app.t_trx_boarding aa
join app.t_trx_dock a on aa.dock_id=a.id
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

     if (empty($row->close_boarding_date))
     {
        $row->action='<a href="'.site_url('close_boarding/edit/'.$row->boarding_id).'" class="btn btn-warning btn-sm" >Close Boarding</a>';
     }
     else
     {
        $row->action="<span class='label label-sm btn-info' >Sudah Berangkat</span>" ; 
     }

      $row->number = $i;
      $row->created_on = format_dateTime($row->created_on);
      $row->open_boarding_date =$row->open_boarding_date==''?'':format_dateTime($row->open_boarding_date);
      $row->status =$row->close_boarding_date==''?'Sedang Boarding':'Sudah Berangkat';
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
  public function getboarding($id)
  {
    return $this->db->query("select aa.updated_on as close_boarding_date, aa.id as boarding_id, a.id as dock_id2, d.name as ship_name, c.name as dock_name, b.name as port_name,(select created_on from app.t_trx_boarding where dock_id=a.id  ) as open_boarding_date, a.*
from app.t_trx_boarding aa
join app.t_trx_dock a on aa.dock_id=a.id
join app.t_mtr_port b on a.port_id = b.id
join app.t_mtr_dock c on a.dock_id=c.id
join app.t_mtr_ship d on a.ship_id=d.id
            ");
  }
    public function selectData($table,$field,$sort)
    {
        return $this->db->order_by($field,$sort)->get($table);
    }  

    public function selectById($table,$id)
    {
        return $this->db->where('id',$id)->get($table);
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
    public function update($table,$data,$where)
    {
        $this->db->where($where);
        $this->db->update($table,$data);

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
