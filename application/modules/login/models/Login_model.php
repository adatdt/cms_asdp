<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends MY_Model
{

	public function get_login($username, $password)
    {
		//,po.id AS po_id
        $this->db->select('mg.id as group_id, ms.*');
        $this->db->join('core.t_mtr_user_group mg', 'ms.user_group_id = mg.id');
        //$this->db->join('t_mtr_po po', 'po.po_code = ms.po_code', 'left');
        $this->db->where('ms.username', $username);
        $this->db->where('ms.status = 1 ');
        $this->db->limit(1);
        $q   = $this->db->get('core.t_mtr_user ms');
        $res ='failed';

        if ($q->num_rows() > 0) {
            $user = $q->row();

            if ($this->bcrypt->check_password(strtoupper(md5($password)), $user->password))
             {
                $this->db->select('*');
                $this->db->limit(1);
                $app_id = $this->db->get('app.t_mtr_identity_app')->row();                

                // pengecekan apakah dia punya hak akses
                if($user->admin_pannel_login == false)
                {
                    $message['error'] = 'Anda tidak punya akses. ';
                    echo json_encode($message);   
                }

                // cek identity app
                elseif(!$app_id){
                    $message['error'] = 'Aplikasi tidak teridentifikasi';
                    echo json_encode($message);
                }

                // cek port
                elseif($app_id->port_id AND !$this->global_model->selectById('app.t_mtr_port', 'id', $app_id->port_id)){
                    $message['error'] = 'Pelabuhan tidak ditemukan';
                    echo json_encode($message);
                }

                // pengecekan port && identity app
                elseif($user->port_id AND $app_id->port_id AND $user->port_id != $app_id->port_id)
                {   
                    $message['error'] = 'Anda tidak punya akses di server ';
                    if ($app_id->port_id) {
                        $message['error'] .= $this->global_model->selectById('app.t_mtr_port', 'id', $app_id->port_id)->name;
                    } else {
                        $message['error'] .= 'ini';
                    }

                    echo json_encode($message);
                }

                else
                {
                    $session = array(
                        'logged_in'    => 1,
                        'id'           => $user->id,
                        'group_id'     => $user->group_id,
                        'firstname'    => $user->first_name,
                        'lastname'     => $user->last_name,
                        'username'     => $user->username,
                        'port_id'      => $user->port_id,
                        'stc_ship_date'=> "",
                    );

                    $this->session->set_userdata($session);

                    $message['success'] = 'Login success. ';
                    echo json_encode($message);

                    // $data_param=$user->id."|".$user->group_id."|".$user->first_name."|".$user->last_name."|".$user->username."|".$user->port_id;

                    // $this->check_user($data_param);
                }
                
            } 

            else 
            {
                $message['error'] = 'Username Atau Password Tidak Cocok. ';
                echo json_encode($message);
            }
        } 
        else 
        {
            $message['error'] = 'Username Atau Password  Cocok. ';
            echo json_encode($message);
        }
        
    }


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
        return $app_id = $this->db->get('app.t_mtr_identity_app')->row();          
    }    


    public function check_user_ptc($user_id)
    {
        return $this->db->query(" select * from core.t_mtr_user  where id={$user_id} and user_group_id in (10, 2)  " )->row();        
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

    // public function check_user_ptc2($data_param)
    // {

    //     $explode=explode("|",$data_param);

    //     $user_id=$explode[0];
    //     $user_group_id=$explode[1];
    //     $user_first_name=$explode[2];
    //     $user_last_name=$explode[3];
    //     $user_username=$explode[4];
    //     $port_id=$explode[5];

    //     $user_ptc_stc=$this->db->query(" select * from core.t_mtr_user  where id={$user_id} and user_group_id in (10, 2)  " )->row();



    //     if(count($user_ptc_stc)>0)
    //     {
    //         // check apakah sudah melakukan assignment
    //         $check_ptc=$this->db->query(
    //             "  SELECT distinct on (assignment_date) assignment_date ,min(created_on), a.shift_code  from app.t_trx_assignment_ptc_stc a
    //             where port_id={$user_ptc_stc->port_id} and user_id={$user_ptc_stc->id} 
    //             and a.assignment_date::date between (now()::date + '-1 day'::interval) and now()
    //             and a.status=1 
    //             and assignment_date=(
    //                 select min(assignment_date) from app.t_trx_assignment_ptc_stc 
    //                 where port_id={$user_ptc_stc->port_id} and user_id={$user_ptc_stc->id} 
    //                 and assignment_date::date between (now()::date + '-1 day'::interval) and now()
    //                 and status=1 
    //             )
    //             group by assignment_date, assignment_code, a.shift_code"
    //         )->row();

    // }

    public function select_data($table, $where="")
    {
        return $this->db->query("select * from $table $where");
    }

}
