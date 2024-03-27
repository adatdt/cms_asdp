<?php

/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Home_model extends CI_Model {
  public function countBooking_backup($status) {
    $y = date('Y');
    $m = date('m');
    $d = date('d');

    return $this->db->query("
      SELECT 
      COALESCE(SUM(qty), 0) as qty
      FROM t_trx_booking 
      WHERE booking_status = '{$status}'
      AND EXTRACT(YEAR FROM booking_date) = '{$y}'
      AND EXTRACT(MONTH FROM booking_date) = '{$m}'
      AND EXTRACT(DAY FROM booking_date) = '{$d}'
      ")->row()->qty;
  }

  /**
   * Count schedule
   * 
   * @return integer
   * 
   * @author gambas on 18 Jan 2018
   */
   
  public function get_schedule_count() {

    $date = date('Y-m-d');
    $where ="";
    if ($this->session->group_id == 4) {
      $where .=" AND bus.po_id = {$this->session->po_id} ";
   } 
   
    $sql = "SELECT count(sch.id) AS total  
    FROM app.t_mtr_schedule sch 
    JOIN t_mtr_bus bus on bus.bus_code = sch.bus_code AND bus.status = 1 {$where}
    where sch.status = 1 AND sch.approval = 1  AND (end_date >= now()::date OR end_date IS NULL) 
       ;";


  return $this->db->query($sql)->row();

}

// public function get_sale_count() {
//    $where= "";
//    if ($this->session->group_id == 4) {
//       $where= " WHERE bus.po_id = '{$this->session->po_id}' ";
//    }
//     $sql = "
//     SELECT count(*) as ticket,sum(tik.price) as total   FROM  t_trx_booking bok 
//         JOIN t_trx_payment pay on pay.trans_number = bok.trans_number  
//         JOIN t_trx_booking_detail bod on bod.booking_id = bok.id  
//         JOIN t_trx_ticket tik on tik.booking_detail_id = bod.id  AND dep_date::DATE = now()::DATE 
//         AND tik.ticket_number NOT IN (
//               SELECT ccd.ticket_number FROM t_trx_cancelation_detail ccd
//               JOIN t_trx_ticket tik on tik.ticket_number = ccd.ticket_number               

//               WHERE dep_date::DATE = now()::DATE
//           )
//         AND tik.ticket_number NOT IN (
//               SELECT rsd.old_ticket_number FROM t_trx_reschedule_detail rsd
//               JOIN t_trx_ticket tik on tik.ticket_number = rsd.old_ticket_number               

//         WHERE dep_date::DATE = now()::DATE
//                         ) 
//         JOIN t_mtr_schedule sch on sch.schedule_code = bod.schedule_code 
//         JOIN t_mtr_bus bus on bus.bus_code = sch.bus_code 
//         {$where}
//     ";
      
  
//   return $this->db->query($sql)->row();
// }

public function get_income() {
   $where= "";
   if ($this->session->group_id == 4) {
      $where= " WHERE bus.po_id = '{$this->session->po_id}' ";
   }
    $sql = "
    SELECT count(*) as ticket,sum(tik.price) as total   FROM  t_trx_booking bok 
        JOIN t_trx_payment pay on pay.trans_number = bok.trans_number  
        JOIN t_trx_booking_detail bod on bod.booking_id = bok.id  
        JOIN t_trx_ticket tik on tik.booking_detail_id = bod.id  AND dep_date::DATE = now()::DATE 
        AND tik.ticket_number NOT IN (
              SELECT ccd.ticket_number FROM t_trx_cancelation_detail ccd
              JOIN t_trx_ticket tik on tik.ticket_number = ccd.ticket_number               

              WHERE dep_date::DATE = now()::DATE
          )
        AND tik.ticket_number NOT IN (
              SELECT rsd.old_ticket_number FROM t_trx_reschedule_detail rsd
              JOIN t_trx_ticket tik on tik.ticket_number = rsd.old_ticket_number               

        WHERE dep_date::DATE = now()::DATE
                        ) 
        JOIN t_mtr_schedule sch on sch.schedule_code = bod.schedule_code 
        JOIN t_mtr_schedule_detail scd on scd.schedule_code = sch.schedule_code  AND tik.origin = scd.terminal_code  AND scd.depart_time <= now()::TIME 
        JOIN t_mtr_bus bus on bus.bus_code = sch.bus_code 
        {$where}
    ";
      
  
  return $this->db->query($sql)->row();
}
public function get_cancel_count() {
    $where = "";
  if ($this->session->group_id == 4) {
     $where .= " AND bus.po_id = '{$this->session->po_id}' ";
    }
    $sql="SELECT count(tik.id) as ticket,COALESCE(sum(cncd.cancelation_fee),0) as total 
          FROM t_trx_cancelation cnc 
          JOIN t_trx_cancelation_detail cncd ON cnc.id = cncd.cancel_id 
          LEFT JOIN t_trx_ticket tik ON cncd.ticket_number = tik.ticket_number  
          JOIN t_trx_booking_detail  bod ON bod.id = tik.booking_detail_id 
          JOIN t_trx_booking bok ON bok.id = bod.booking_id 
          JOIN t_trx_payment pay ON bok.trans_number = pay.trans_number 
          JOIN t_mtr_schedule sch ON bod.schedule_code =  sch.schedule_code 
          JOIN t_mtr_bus bus on sch.bus_code = bus.bus_code 
          WHERE tik.dep_date::date = now()::date 
          {$where} ";
  return $this->db->query($sql)->row();
}

public function get_reschedule_count() {
    $where = "";
  if ($this->session->group_id == 4) {
     $where .= " AND bus.po_id = '{$this->session->po_id}' ";
    }
    $sql="SELECT count(tik.id) as ticket,COALESCE(sum(rscd.reschedule_fee),0) as total 
          FROM t_trx_reschedule rsc 
          JOIN t_trx_reschedule_detail  rscd ON rsc.id = rscd.reschedule_id  
          LEFT JOIN t_trx_ticket tik ON rscd.old_ticket_number = tik.ticket_number  
          JOIN t_trx_booking_detail  bod ON bod.id = tik.booking_detail_id 
          JOIN t_trx_booking bok ON bok.id = bod.booking_id 
          JOIN t_trx_payment pay ON bok.trans_number = pay.trans_number 
          JOIN t_mtr_schedule sch ON bod.schedule_code =  sch.schedule_code 
          JOIN t_mtr_bus bus on sch.bus_code = bus.bus_code 
          WHERE tik.dep_date::date = now()::date 
          {$where} ";
  return $this->db->query($sql)->row();
}

public function getTicketSalesSummaryNow() {
  $y = date('Y');
  $m = date('m');
  $d = date('d');

  return $this->db->query("
    SELECT
    arm.bus_name,
    sum(book.total) AS total
    FROM t_trx_booking book
    JOIN t_mtr_schedule sch ON sch.schedule_code = book.schedule_code AND sch.actived = 1
    JOIN t_mtr_bus arm ON arm.bus_code = sch.bus_code AND arm.actived = 1
    WHERE book.booking_status = 2
    AND EXTRACT(YEAR FROM booking_date) = '{$y}'
    AND EXTRACT(MONTH FROM booking_date) = '{$m}'
    AND EXTRACT(DAY FROM booking_date) = '{$d}'
    GROUP BY
    arm.bus_code,
    arm.bus_name
    ORDER BY arm.bus_name;
    ") ->result();
}

  /**
   * Summary Ticket sales today each PO
   * 
   * @return object
   * 
   * @author gambas on 18 Jan 2018
   */
  public function get_ticket_sales_summary($po_id,$stat) {
    $status = $stat;
    if ($po_id == NULL) {
     $sql = "SELECT bus.po_id, po.po_name  as label, count(*) as ticket,COALESCE(sum(tik.price),0)  as value,  COALESCE(count(tik.id), 0) as qty   FROM  t_trx_booking bok 
          JOIN t_trx_payment pay on pay.trans_number = bok.trans_number AND pay.created_on::DATE = now()::DATE
          JOIN t_trx_booking_detail bod on bod.booking_id = bok.id  
          JOIN t_trx_ticket tik on tik.booking_detail_id = bod.id   AND ({$status}) 
          AND tik.ticket_number NOT IN (
              SELECT ccd.ticket_number FROM t_trx_cancelation_detail ccd
              JOIN t_trx_ticket tik on tik.ticket_number = ccd.ticket_number  
              JOIN t_trx_cancelation cnc on cnc.id = ccd.cancel_id              

          )

          AND tik.ticket_number NOT IN (
              SELECT rsd.new_ticket_number FROM t_trx_reschedule_detail rsd
              JOIN t_trx_ticket tik on tik.ticket_number = rsd.new_ticket_number  
              JOIN t_trx_reschedule rsc on rsc.id = rsd.reschedule_id              

          )
          JOIN t_mtr_schedule sch on sch.schedule_code = bod.schedule_code 
          JOIN t_mtr_bus bus on bus.bus_code = sch.bus_code 
          RIGHT JOIN  t_mtr_po po on po.id = bus.po_id AND po.status = 1 

          GROUP BY bus.po_id,po.po_name ";

          // $sql=""
   }
   else{
          $where =" WHERE po.id = {$this->session->po_id}";
          $sql="SELECT date_part('day', gs.gs) AS id, to_char(gs.gs, 'DD Mon YYYY') AS label,COALESCE(sale.value, 0) as value 
          FROM generate_series(date(now()) - 6, date(now()), '1 day') AS gs 
          LEFT JOIN (
         
              SELECT  sum(tik.price) as value, bok.created_on::DATE FROM t_trx_ticket tik 
                JOIN t_trx_booking_detail bod on bod.id = tik.booking_detail_id 
                JOIN t_trx_booking bok on bok.id = bod.booking_id AND booking_status = 2 
                JOIN t_trx_payment pay on pay.trans_number = bok.trans_number 
                JOIN t_mtr_schedule sch on sch.schedule_code = bod.schedule_code 
                JOIN t_mtr_bus bus on bus.bus_code = sch.bus_code 
                RIGHT JOIN  t_mtr_po po on po.id = bus.po_id AND po.status = 1 
                {$where} AND {$stat}  
                AND tik.ticket_number NOT IN (
                  SELECT ccd.ticket_number FROM t_trx_cancelation_detail ccd
                  JOIN t_trx_ticket tik on tik.ticket_number = ccd.ticket_number  
                  JOIN t_trx_cancelation cnc on cnc.id = ccd.cancel_id     
              )
                AND tik.ticket_number NOT IN (
                  SELECT rsd.new_ticket_number FROM t_trx_reschedule_detail rsd
                  JOIN t_trx_ticket tik on tik.ticket_number = rsd.new_ticket_number  
                  JOIN t_trx_reschedule rsc on rsc.id = rsd.reschedule_id              

              )
              
              
        
            GROUP BY bok.created_on::DATE 


          ) as sale on sale.created_on::date = gs.gs::date 

            
          ";




   }
    
    return $this->db->query($sql)->result();
  }

  public function get_income_summary($po_id,$stat) {
    $status = $stat;
    if ($po_id == NULL) {
     $sql = "SELECT bus.po_id, po.po_name  as label, count(*) as ticket,COALESCE(sum(tik.price),0)  as value,  COALESCE(count(tik.id), 0) as qty   FROM  t_trx_booking bok 
          JOIN t_trx_payment pay on pay.trans_number = bok.trans_number  
          JOIN t_trx_booking_detail bod on bod.booking_id = bok.id 
          JOIN t_mtr_schedule sch on sch.schedule_code = bod.schedule_code 

          JOIN t_mtr_schedule_detail scd on scd.schedule_code = sch.schedule_code  
          JOIN t_trx_ticket tik on tik.booking_detail_id = bod.id  AND dep_date::DATE = now()::DATE AND ({$status})  AND tik.origin = scd.terminal_code  AND scd.depart_time <= now()::TIME 
          AND tik.ticket_number NOT IN (
          SELECT ccd.ticket_number FROM t_trx_cancelation_detail ccd
          JOIN t_trx_ticket tik on tik.ticket_number = ccd.ticket_number               
          
          WHERE dep_date::DATE = now()::DATE
          )
          AND tik.ticket_number NOT IN (
          SELECT rsd.old_ticket_number FROM t_trx_reschedule_detail rsd
          JOIN t_trx_ticket tik on tik.ticket_number = rsd.old_ticket_number               
          
          WHERE dep_date::DATE = now()::DATE
          ) 
          
          JOIN t_mtr_bus bus on bus.bus_code = sch.bus_code 
          RIGHT JOIN  t_mtr_po po on po.id = bus.po_id AND po.status = 1 

          GROUP BY bus.po_id,po.po_name ";
   }
   else{
          $where =" WHERE po.id = {$this->session->po_id}";
          //  $sql ="SELECT date_part('day', gs.gs) AS id, to_char(gs.gs, 'DD Mon YYYY') AS label,COALESCE(sale.value, 0) as value 
          //  FROM generate_series(date(now()) - 6, date(now()), '1 day') AS gs 
          //  LEFT JOIN (


          // SELECT  sum(tik.price) as value, tik.dep_date, scd.depart_time FROM t_trx_ticket tik 
          //       JOIN t_trx_booking_detail bod on bod.id = tik.booking_detail_id 
          //       JOIN t_trx_booking bok on bok.id = bod.booking_id AND booking_status = 2 
          //       JOIN t_trx_payment pay on pay.trans_number = bok.trans_number 
          //       JOIN t_mtr_schedule sch on sch.schedule_code = bod.schedule_code 
          //       JOIN t_mtr_schedule_detail scd on scd.schedule_code = sch.schedule_code  AND tik.origin = scd.terminal_code  
          //       -- AND scd.depart_time <= now()::TIME 

          //       JOIN t_mtr_bus bus on bus.bus_code = sch.bus_code 
          //       RIGHT JOIN  t_mtr_po po on po.id = bus.po_id AND po.status = 1 

          //        {$where} AND ({$stat})
                    
          //       AND tik.ticket_number NOT IN (
          //       SELECT ccd.ticket_number FROM t_trx_cancelation_detail ccd
          //       JOIN t_trx_ticket tik on tik.ticket_number = ccd.ticket_number  
          //       JOIN t_trx_cancelation cnc on cnc.id = ccd.cancel_id     
          //       )
          //       AND tik.ticket_number NOT IN (
          //       SELECT rsd.old_ticket_number FROM t_trx_reschedule_detail rsd
          //       JOIN t_trx_ticket tik on tik.ticket_number = rsd.old_ticket_number 
          //       JOIN t_trx_reschedule rsc on rsc.id = rsd.reschedule_id              
                
          //       )
                                
                                
                    
          //      GROUP BY tik.dep_date ) as sale on sale.dep_date::date = gs.gs::date

          //          ";
          

          $sql ="SELECT date_part('day', gs.gs) AS id, to_char(gs.gs, 'DD Mon YYYY') AS label,COALESCE(sale.value, 0) as value
           FROM generate_series(date(now()) - 6, date(now()), '1 day') AS gs 
           LEFT JOIN (


          SELECT  sum(tik.price) as value, tik.dep_date FROM t_trx_ticket tik 
                JOIN t_trx_booking_detail bod on bod.id = tik.booking_detail_id 
                JOIN t_trx_booking bok on bok.id = bod.booking_id AND booking_status = 2 
                JOIN t_trx_payment pay on pay.trans_number = bok.trans_number 
                JOIN t_mtr_schedule sch on sch.schedule_code = bod.schedule_code 
                JOIN t_mtr_schedule_detail scd on scd.schedule_code = sch.schedule_code  AND tik.origin = scd.terminal_code  
               

                JOIN t_mtr_bus bus on bus.bus_code = sch.bus_code 
                RIGHT JOIN  t_mtr_po po on po.id = bus.po_id AND po.status = 1 

                 {$where} AND ({$stat})
                    
                AND tik.ticket_number NOT IN (
                SELECT ccd.ticket_number FROM t_trx_cancelation_detail ccd
                JOIN t_trx_ticket tik on tik.ticket_number = ccd.ticket_number  
                JOIN t_trx_cancelation cnc on cnc.id = ccd.cancel_id     
                )
                AND tik.ticket_number NOT IN (
                SELECT rsd.old_ticket_number FROM t_trx_reschedule_detail rsd
                JOIN t_trx_ticket tik on tik.ticket_number = rsd.old_ticket_number 
                JOIN t_trx_reschedule rsc on rsc.id = rsd.reschedule_id              
                
                )

                AND tik.ticket_number
                NOT IN ( 
                SELECT  tik.ticket_number FROM t_trx_ticket tik 
                JOIN t_trx_booking_detail bod on bod.id = tik.booking_detail_id 
                JOIN t_trx_booking bok on bok.id = bod.booking_id AND booking_status = 2                
                JOIN t_mtr_schedule sch on sch.schedule_code = bod.schedule_code 
                JOIN t_mtr_schedule_detail scd on scd.schedule_code = sch.schedule_code  AND tik.origin = scd.terminal_code  
                WHERE tik.dep_date = now()::DATE AND scd.depart_time > now()::TIME 
                  )
                                
                                
                    
               GROUP BY tik.dep_date ) as sale on sale.dep_date::date = gs.gs::date

                   ";




   }
    
   $res =$this->db->query($sql)->result();
//     foreach ($res as $key) {
//        print_r($key->depart_time); 
//     }
// exit();
    return $res;
  }

  // public function getGateInSummaryNow() {
  //   $y = date('Y');
  //   $m = date('m');
  //   $d = date('d');

  //   return $this->db
  //   ->query("
  //     SELECT
  //     arm.bus_name,
  //     count(boos.*) AS total
  //     FROM t_trx_booking_detail boos
  //     JOIN t_trx_booking book ON book.id = boos.booking_id AND book.booking_status = 2
  //     JOIN t_mtr_schedule sch ON sch.schedule_code = book.schedule_code AND sch.actived = 1
  //     JOIN t_mtr_bus arm ON arm.bus_code = sch.bus_code AND arm.actived = 1
  //     WHERE boos.gate_in = 1
  //     AND EXTRACT(YEAR FROM booking_date) = '{$y}'
  //     AND EXTRACT(MONTH FROM booking_date) = '{$m}'
  //     AND EXTRACT(DAY FROM booking_date) = '{$d}'
  //     GROUP BY arm.bus_name
  //     ORDER BY arm.bus_name;
  //     ")
  //   ->result();
  // }

  /**
   * Summary Gate IN today each PO
   * 
   * @return object
   * 
   * @author gambas on 18 Jan 2018
   */
  public function get_gate_in_summary($po_id) {
    if ($po_id == NULL) {
      // $sql = "SELECT po.id, po.po_name AS label, count(boos.*) AS value
      // FROM t_mtr_po po 
      // LEFT JOIN t_mtr_bus arm ON po.id = arm.po_id
      // LEFT JOIN t_mtr_schedule sch ON sch.bus_code = arm.bus_code
      // LEFT JOIN t_trx_booking book ON book.schedule_code = sch.schedule_code AND book.booking_status = 2 AND date(booking_date) = date(now())
      // LEFT JOIN t_trx_booking_detail boos ON boos.booking_id = book.id AND boos.gate_in = 1
      // WHERE po.actived = 1
      // GROUP BY po.id, po_name
      // ORDER BY po.po_name";
       $sql = "SELECT po.id,po.po_name as label,COALESCE(po1.ticket, 0) as value  from t_mtr_po po 
            LEFT JOIN(SELECT bus.po_id as id, sum(bok.price) as total, count(bos.id) as ticket from t_trx_booking_detail bos 
            JOIN t_trx_booking bok on bos.booking_id = bok.id 
            JOIN t_trx_booking_header bdh on bok.booking_header_id = bdh.id AND bdh.status = 2 AND bdh.updated_on::DATE = now()::date 
            JOIN t_mtr_schedule sch ON sch.schedule_code = bok.schedule_code 
            JOIN t_mtr_bus bus ON bus.bus_code = sch.bus_code  
            where  bos.status = 1 GROUP BY bus.po_id) as po1 on po1.id = po.id WHERE po.status = 1";
    } else {
      // $sql = "SELECT date_part('day', gs.gs) AS id, to_char(gs.gs, 'DD Mon YYYY') AS label, COALESCE(bb.total, 0) AS value
      // FROM  generate_series(date(now()) - 6, date(now()), '1 day') AS gs
      // LEFT JOIN (
      // SELECT  date(booking_date) AS booking_date, count(boos.id) AS total
      // FROM t_trx_booking book
      // JOIN t_trx_booking_detail boos ON boos.booking_id = book.id AND boos.gate_in = 1
      // JOIN t_mtr_schedule sch ON sch.schedule_code = book.schedule_code AND sch.actived = 1
      // JOIN  t_mtr_bus arm ON arm.bus_code = sch.bus_code AND arm.actived = 1 AND arm.po_id = {$po_id}
      // JOIN  t_mtr_po po ON po.id = arm.po_id AND po.actived = 1
      // WHERE book.booking_status = 2 
      // GROUP BY date(booking_date)
      // ) AS bb ON bb.booking_date = gs.gs
      // ORDER BY gs.gs";
       $sql="SELECT date_part('day', gs.gs) AS id, to_char(gs.gs, 'DD Mon YYYY') AS label,COALESCE(booking.ticket, 0) as value
        FROM generate_series(date(now()) - 6, date(now()), '1 day') AS gs 
        LEFT JOIN (SELECT bdh.updated_on::date as updated_on,sum(bok.price) as total, count(bos.id) as ticket from t_trx_booking_detail bos 
        JOIN t_trx_booking bok on bos.booking_id = bok.id 
        JOIN t_trx_booking_header bdh on bok.booking_header_id = bdh.id AND bdh.status = 2 
        JOIN t_mtr_schedule sch ON sch.schedule_code = bok.schedule_code 
        JOIN t_mtr_bus bus ON bus.bus_code = sch.bus_code AND bus.po_id = {$po_id} 
        where  bos.status = 1 GROUP BY bdh.updated_on::date) as booking on booking.updated_on::date = gs.gs::date";
    }

    return $this->db->query($sql)->result();
  }

  
  function get_vehicle_pos() {
    $sql = "SELECT to_char(( TIMESTAMP WITH TIME ZONE 'epoch' + INTERVAL '1 second' * FLOOR(EXTRACT('epoch' FROM tt.time) / 600) * 600 ) ::TIMESTAMP(0), 'HH24:MI') AS time,
      COUNT(bok.id) AS total
      FROM (SELECT generate_series(now()::TIMESTAMP - INTERVAL '12 hours', now()::TIMESTAMP, '10 minutes')::TIMESTAMP(0) AS time) AS tt
      LEFT JOIN app.t_trx_booking bok ON ( TIMESTAMP WITH TIME ZONE 'epoch' + INTERVAL '1 second' * FLOOR(EXTRACT('epoch' FROM bok.created_on) / 600) * 600 ) = ( TIMESTAMP WITH TIME ZONE 'epoch' + INTERVAL '1 second' * FLOOR(EXTRACT('epoch' FROM tt.time) / 600) * 600 )
      GROUP BY tt.time
      ORDER BY tt.time";
    return $this->db->query($sql)->result();
  }
  
  function get_total_vehicle() {
    $sql = "SELECT COUNT(bov.id) AS total_vehicle
            FROM app.t_trx_booking bok
            JOIN app.t_trx_booking_vehicle bov ON bov.booking_id = bok.id
            WHERE bok.service_id = 2 AND date(bok.tx_date) = CURRENT_DATE AND bok.status = 4";
    return $this->db->query($sql)->row()->total_vehicle;
  }
  
  function get_total_passanger() {
    $sql = "SELECT COUNT(bop.id) AS total_passanger
            FROM app.t_trx_booking bok
            JOIN app.t_trx_booking_passanger bop ON bop.booking_id = bok.id
            WHERE bok.service_id = 1 AND date(bok.tx_date) = CURRENT_DATE AND bok.status = 4";
    return $this->db->query($sql)->row()->total_passanger;
  }

  function get_profile()
  {
    $id=$this->session->userdata('id');
    
    return $this->db->query("select c.name as group_name, b.name as port_name, concat(a.first_name,' ',a.last_name) as 
                            full_name , a.* from core.t_mtr_user a
                            left join app.t_mtr_port b on a.port_id=b.id
                            left join core.t_mtr_user_group c on a.user_group_id=c.id
                           where a.id=$id");
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
