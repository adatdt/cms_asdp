<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends MY_Model
{

    public function check_user($username)
    {
        //,po.id AS po_id
        $this->db->select('mg.id as group_id, ms.*');
        $this->db->join('core.t_mtr_user_group mg', 'ms.user_group_id = mg.id');
        //$this->db->join('t_mtr_po po', 'po.po_code = ms.po_code', 'left');
        $this->db->where('ms.username', $username);
        $this->db->where('ms.status = 1 ');
        $this->db->limit(1);
    return $q   =  $this->db->get('core.t_mtr_user ms');        
    }

    public function identity_app()
    {
        $this->db->select('*');
        $this->db->limit(1);
        return $app_id = $this->db->get('app.t_mtr_identity_app');          
    }    


    public function check_user_ptc($user_id)
    {
        return $this->db->query(" select * from core.t_mtr_user  where id={$user_id} and user_group_id in (10, 2)  " );        
    }
    public function check_assignment_ptc($port_id,$id)
    {
            return $this->db->query(
                "  SELECT distinct on (assignment_date) assignment_date ,min(created_on), a.shift_code  from app.t_trx_assignment_ptc_stc a
                where port_id={$port_id} and user_id={$id} 
                and a.assignment_date::date between (now()::date + '-1 day'::interval) and now()
                and a.status=1 
                and assignment_date=(
                    select min(assignment_date) from app.t_trx_assignment_ptc_stc 
                    where port_id={$port_id} and user_id={$id} 
                    and assignment_date::date between (now()::date + '-1 day'::interval) and now()
                    and status=1 
                )
                group by assignment_date, assignment_code, a.shift_code"
            )->row();       
    }    

    public function select_data($table, $where="")
    {
        return $this->db->query("select * from $table $where");
    }

}
