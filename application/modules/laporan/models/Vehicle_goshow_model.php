<?php

/*
  Document   : Vehicle_pos_model
  Created on : Oct 16, 2018 1:29:26 PM
  Author     : Andedi
  Description: Purpose of the PHP File follows.
 */

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Description of Vehicle_pos_model
 *
 * @author Andedi
 */
class Vehicle_goshow_model extends MY_Model {

  function get_list($param) {
    $start = $param['start'];
    $length = $param['length'];
    $draw = $param['draw'];
//    $search = $this->input->post('search');
    $order = $param['order'];
    $order_column = $order[0]['column'];
    $order_dir = strtoupper($order[0]['dir']);
    
    $where = '';
    if ($param['datefrom'] != '' && $param['dateto'] != '') {
      $start_date = $this->db->escape($param['datefrom']);
      $end_date = $this->db->escape($param['dateto']);
      $where = " AND date(tx_date) BETWEEN {$start_date} AND {$end_date} ";
    }
    
    $sql = "SELECT bok.id, tx_date, bok.shift, bok.officer, bov.id_number, vc.name AS class_name, bov.length, bov.height, bov.weight, bov.fare
            FROM app.t_trx_booking bok
            JOIN app.t_trx_booking_vehicle bov ON bov.booking_id = bok.id
            JOIN app.t_mtr_vehicle_class vc ON vc.id = bov.vehicle_class_id
            WHERE client_id = 3 AND bok.status >= 1 {$where}";

    $query = $this->db->query($sql);
    $records_total = $query->num_rows();

//    $sql .=" ORDER BY " . $order_column . " {$order_dir}";
    $sql .=" ORDER BY tx_date $order_dir";
//    if ($length != -1) {
//      $sql .=" LIMIT {$length} OFFSET {$start};";
//    }

    $query = $this->db->query($sql);
    $rows_data = $query->result();
    $i = 1;
    $total_income = 0;

    foreach ($rows_data as $key => $value) {
      $rows_data[$key]->no = $i++;
      $total_income      += $rows_data[$key]->fare;
      $rows_data[$key]->length = idr_currency($rows_data[$key]->length);
      $rows_data[$key]->height = idr_currency($rows_data[$key]->height);
      $rows_data[$key]->weight = idr_currency($rows_data[$key]->weight);
      $rows_data[$key]->fare = idr_currency($rows_data[$key]->fare);
    }
    

    return array(
        'draw' => $draw,
        'recordsTotal' => $records_total,
        'recordsFiltered' => $records_total,
        'data' => $rows_data,
        'total' => idr_currency($total_income)
    );
  }

}

?>
