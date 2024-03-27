<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Open_boarding_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->_module = 'open_boarding';
    }

    public function sandarList(){
        $start          = $this->input->post('start');
        $length         = $this->input->post('length');
        $draw           = $this->input->post('draw');
        $search         = $this->input->post('search');
        $order          = $this->input->post('order');
        $order_column   = $order[0]['column'];
        $order_dir      = strtoupper($order[0]['dir']);
        $datefrom       = $this->input->post('datefrom');
        $dateto         = $this->input->post('dateto');
        $iLike          = trim(strtoupper($this->db->escape_like_str($search['value'])));

        $field = array(
            0 =>'aa.id',
            1 => 'ship_name',
            2 => 'created_on',
            3 => 'open_boarding_date',
            4 => 'open_boarding_date',
            5 => 'depart_date',
    		6 =>'port_name',
    		7 =>'dock_name',

        );

        $order_column = $field[$order_column];

        $where = "";

        if ((!empty($datefrom) and empty($dateto) )||(empty($datefrom) and !empty($dateto) )){
            if(!empty($search['value']) ){
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
            }else{
                $where =" where
                    (
                    to_char(a.created_on,'yyyy-mm-dd')='$datefrom'
                    or to_char(a.created_on,'yyyy-mm-dd')='$dateto'
                    )
                ";
            }
        }else if (!empty($datefrom) and !empty($dateto)){
           if(!empty($search['value']) ){
              $where = " where (
                        d.name ilike '%".$iLike."%'
                        or c.name ilike '%".$iLike."%'
                        or b.name ilike '%".$iLike."%'
                    )
                    and                 
                    to_char(a.created_on,'yyyy-mm-dd') between '$datefrom'
                    and '$dateto'
                    
                ";  
            }else{
                $where =" where
                    to_char(a.created_on,'yyyy-mm-dd') between '$datefrom'
                    and '$dateto'
                ";
            }
        }
        else
        {
            if(!empty($search['value']) ){
              $where = " where (
                        d.name ilike '%".$iLike."%'
                        or c.name ilike '%".$iLike."%'
                        or b.name ilike '%".$iLike."%'
                    )
                ";  
            }else{
                $where="";
            }
        }   

        $sql = "SELECT e.depart_date, e.boarding_id ,aa.updated_on AS close_boarding_date, aa.id AS boarding_id, a.id AS dock_id2, d.name as ship_name, c.name AS dock_name, b.name AS port_name,
            aa.created_on AS open_boarding_date, a.*
            FROM app.t_trx_dock a 
            LEFT JOIN app.t_trx_boarding aa on a.id=aa.dock_id
            JOIN app.t_mtr_port b on a.port_id =b.id
            JOIN app.t_mtr_dock c on a.dock_id=c.id
            JOIN app.t_mtr_ship d on a.ship_id=d.id
            LEFT JOIN app.t_trx_sail e on aa.id = e.boarding_id
            {$where}";

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

            $id_data_boarding=$this->enc->encode($row->boarding_id);
            $detail_url     = site_url($this->_module."/detail/{$id_data_boarding}");
            
            if (empty($row->open_boarding_date))
            {
                $url=site_url('open_boarding/save/'.$row->dock_id2);
                $row->action = generate_button_new($this->_module, 'open_boarding', $url);

                $row->status='Bersandar';
            }

            else if (empty($row->close_boarding_date) and !empty($row->open_boarding_date))
            {

                $url=site_url('open_boarding/update/'.$row->boarding_id);
                $row->action = generate_button_new($this->_module, 'close_boarding', $url)."".generate_button_new($this->_module, 'detail', $detail_url);

                $row->status='Sedang Boarding';
            }
            else if (empty($row->depart_date))
            {

                $url=site_url('open_boarding/updateSail/'.$row->boarding_id);
                $row->action = generate_button_new($this->_module, 'sailing_ship', $url)."".generate_button_new($this->_module, 'detail', $detail_url);

                $row->status='Siap Berangkat';
            }
            else 
            {

                $row->action = generate_button_new($this->_module, 'detail', $detail_url);


                $row->status='Sukses Boarding';
             }


          $row->number = $i;
          $row->depart_date=$row->depart_date==''?'':format_dateTime($row->depart_date);
          $row->created_on = format_dateTime($row->created_on);
          $row->close_boarding_date=$row->close_boarding_date==''?'':format_dateTime($row->close_boarding_date);
          $row->open_boarding_date =$row->open_boarding_date==''?'':format_dateTime($row->open_boarding_date);
         // $row->status =$row->open_boarding_date==''?'Bersandar':'Open Boarding';
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
  
  public function getdock($id)
  {
    return $this->db->query("select aa.updated_on as close_boarding_date, aa.id as boarding_id, a.id as dock_id2, d.name as ship_name, c.name as dock_name, b.name as port_name,
(select created_on from app.t_trx_boarding where dock_id=a.id  ) as open_boarding_date, a.*
from app.t_trx_dock a 
left join app.t_trx_boarding aa on a.id=aa.dock_id
join app.t_mtr_port b on a.port_id = b.id
join app.t_mtr_dock c on a.dock_id=c.id
join app.t_mtr_ship d on a.ship_id=d.id
            where a.id=$id
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

    public function insertdata($table,$data)
    {
        $this->db->insert($table,$data);

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
    // public function update($table,$data,$id)
    // {
    //     $this->db->where('id',$id);
    //     $this->db->update($table,$data);

    //     if ($this->db->trans_status() === FALSE)
    //     {
    //             $this->db->trans_rollback();
    //             return FALSE;
    //     }
    //     else
    //     {
    //             $this->db->trans_commit();
    //             return TRUE;
    //     }
    // }

    public function boardingList()
    {
        $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
   // $datefrom = $this->input->post('datefrom');
   // $dateto = $this->input->post('dateto');

    $field = array(
        0=>'a.id',
        1 => 'ticket_number',
        2 => 'name',
        3 => 'id_number',
        4 => 'gender',
        5 => 'city',
        6 =>'age',
    );

    $order_column = $field[$order_column];

    $where = "";
    $sql = "
            select b.* from app.t_trx_boarding_detail a
            join app.t_trx_booking_passanger b on a.ticket_number=b.ticket_number 
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

    public function passangerVehicleList()
    {
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $draw = $this->input->post('draw');
        $search = $this->input->post('search');
        $order = $this->input->post('order');
        $order_column = $order[0]['column'];
        $order_dir = strtoupper($order[0]['dir']);
       // $datefrom = $this->input->post('datefrom');
       // $dateto = $this->input->post('dateto');

        $field = array(
            0=>'a.id',
            1 => 'ticket_number',
            2 => 'name',
            3 => 'id_number',
            4 => 'gender',
            5 => 'city',
            6 =>'age',
        );

        $order_column = $field[$order_column];

        $where = "";
        $sql = "
              select c.* from app.t_trx_boarding_detail a
              join app.t_trx_booking b on a.booking_id=b.id
              join app.t_trx_booking_passanger c on a.id=c.booking_id
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

    public function update($table,$data,$where)
    {
        
        $this->db->where($where);
        $this->db->update($table,$data);
    }

    function insert_data($table,$data)
    {        
        $this->db->insert($table,$data);
    }
}
