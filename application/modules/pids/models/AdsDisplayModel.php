<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : LabelPidsModel
 * -----------------------
 *
 * @author     adat <adatdt@gmail.com>
 * @copyright  2021
 *
 */

class AdsDisplayModel extends MY_Model{

  public function __construct() {
    parent::__construct();
        $this->_module   = 'pids/adsDisplay';
  }

    public function dataList(){
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $draw = $this->input->post('draw');
    $search = $this->input->post('search');
    $port= $this->enc->decode($this->input->post('port'));
    $order = $this->input->post('order');
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
    // $iLike        = trim(strtoupper($this->db->escape_like_str($search['value'])));
    $searchData = $this->input->post('searchData');
    $searchName = $this->input->post('searchName');
    $iLike        = trim(str_replace(array("'",'"'),"",$searchData));


    
    $field = array(
        0 =>'ads.id',
        1=>"ads_display_code",
        2=>"ads_name",
        3=>"duration",
        4=>"port_name",
        5=>'"order"',
        6=>"status",
    );

    $order_column = $field[$order_column];

    $where = " WHERE ads.status not in (-5) ";

    if(!empty($port))
    {
        $where .=" and ( ads.port_id={$port}) ";
    }

    if(!empty($searchData))
    {
        if($searchName=='name')
        {
            $where .="and ( ads.ads_name ilike '%".$iLike."%')";
        }
        else if($searchName=='adsDisplayCode')
        {
            $where .="and ( ads.ads_display_code ilike '%".$iLike."%')";   
        }
        else
        {
          $where .="";
        }
      
    }

    $sql  = "
                SELECT 
                p.name as port_name,
                ads.* 
                from app.t_mtr_ads_display ads
                left join app.t_mtr_port p on ads.port_id=p.id
                {$where}
                
             ";

    $query         = $this->db->query($sql);
    $records_total = $query->num_rows();
    $sql      .= " ORDER BY ".$order_column." {$order_dir}";

    if($length != -1){
      $sql .=" LIMIT {$length} OFFSET {$start}";
    }

    $query     = $this->db->query($sql);
    $rows_data = $query->result();

    $rows   = array();
    $i    = ($start + 1);

    foreach ($rows_data as $row) {
      $id_enc=$this->enc->encode($row->id);
      $row->number = $i;
      $nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|0'));
        $aktif       = site_url($this->_module."/action_change/".$this->enc->encode($row->id.'|1'));

      $row->id =$row->id;
      $edit_url    = site_url($this->_module."/edit/{$id_enc}");
        $delete_url  = site_url($this->_module."/action_delete/{$id_enc}");

        $row->actions  =" ";

      if($row->status == 1){
        $row->actions  .= generate_button_new($this->_module, 'edit', $edit_url);
        $row->status   = success_label('Aktif');
        $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction2(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
      }
      else
      {
        $row->status   = failed_label('Tidak Aktif');
        $row->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction2(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
      }

        // $row->actions .= generate_button_new($this->_module, 'delete', $delete_url);
        
        // button hapus
        $row->actions .=  generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction2(\'Apakah Anda yakin menghapus data ini ?\', \''.$delete_url.'\')" title="Hapus"> <i class="fa fa-trash-o"></i> </button> ');
        
        $row->no=$i;

      $rows[] = $row;
      unset($row->assignment_code);

      $i++;
    }

    return array(
      'draw'           => $draw,
      'recordsTotal'   => $records_total,
      'recordsFiltered'=> $records_total,
      'data'           => $rows
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

  public function insert_data_batch($table,$data)
  {
    $this->db->insert_batch($table, $data);
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
