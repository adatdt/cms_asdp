<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Global_model extends CI_Model
{

    public function getMenu()
    {
        /* query untuk mencari menu_di di table core.t_mtr_privilege */
        $group_id = $this->session->userdata('group_id');
        $sql1 = "SELECT mpd.menu_id
        FROM core.t_mtr_privilege mp
        JOIN core.t_mtr_privilege_detail mpd ON mp.id = mpd.privilege_id
        WHERE mp.group_id IN ('{$group_id}')
        AND mp.active IS TRUE";

        $groups = $this->db->query($sql1)->result();

        $t = array();

        foreach($groups as $group)
        {
            $t[] = $group->menu_id;
        }

        $str = "'".implode("','",$t)."'";

        /* query untuk mencari data di table core.t_mtr_menu sesuai privilege yang di setting */
        $sql2  = "SELECT *
        FROM core.t_mtr_menu
        WHERE id IN({$str})
        AND active IS TRUE";

        $pages = $this->db->query($sql2)->result_array();

        $array = array();

        foreach ($pages as $page) {
            if (!$page['parent']) {
                $array[$page['id']] = $page;
            } else {
                $array[$page['parent']]['children'][] = $page;
            }
        }

        return $array;
    }

    public function insert($table_name,$data)
    {
        $this->db->trans_begin();

        $this->db->insert($table_name,$data);

        if($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return array('status' => true, 'id' => $this->db->insert_id());
        }
    }

    public function delete($data)
    {
        $query = $this->db->query("
        UPDATE $data[table]
        set $data[set]
        where $data[where]");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    /**
    * @param [varchar] $table_name
    * @param [array]   $data        Berisi nama column beserta nilainya
    */
    public function insertDb($table_name,$data)
    {
        $this->db->trans_begin();

        $this->db->insert($table_name,$data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array(
                'status' => ' ',
                'pesan'  => 'Gagal menyimpan data'
            ));
        } else {
            $this->db->trans_commit();
            echo json_encode(array(
                'status' => 'OK',
                'pesan'  => 'Berhasil menyimpan data'
            ));
        }
    }

    /**
    * @param [varchar] $table_name
    */
    public function checkTable($table_name)
    {
        $query = $this->db->query("SELECT count(*)
        FROM information_schema.tables
        WHERE table_name = '$table_name'");

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['count'];
        } else {
            return null;
        }
    }

    /**
    * @param [JSON]    $activity   Untuk keseragaman, format harus dalam array yang sudah di json_encode()
    * @param [string]  $menu       Menu, gunakan huruf kecil
    * @param [int]     $client_id  1=web, 2=mobile
    * @param [varchar] $created_by user
    */
    public function logActivity($menu, $activity)
    {
        $log = array(
            'menu'       => $menu,
            'activity'   => $activity,
            'client_id'  => 1,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'created_by' => $this->session->userdata('username')
        );

        $t_log_activity = 't_log_activity_'.date("Y_m");

        //check table if exist
        $exists = $this->checkTable($t_log_activity);

        //if table t_log_activity
        if ($exists > 0) {
            $this->insert($t_log_activity,$log);
        } else {
            //create table t_log_activity
            $this->createTableLogActivity($t_log_activity,$log);
        }
    }

    public function createTableLogActivity($table_name,$data)
    {
        $this->db->trans_begin();

        $this->db->query("
        CREATE TABLE public.$table_name(
            log_id bigserial primary key,
            menu varchar(50),
            activity text,
            client_id int2,
            ip_address varchar(15),
            created_by varchar(30),
            created_on timestamp(6) DEFAULT now()
        );
        ");

        $this->db->insert($table_name,$data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function getAccess($access){
            $sql = "SELECT bb.privilege_id,bb.menu_id,bb.view,bb.add,bb.edit,bb.delete,bb.detail,bb.approval FROM core.t_mtr_privilege aa 
                LEFT JOIN core.t_mtr_privilege_detail bb on aa.id = bb.privilege_id 
                JOIN core.t_mtr_user_group dd on aa.group_id = dd.id 
                LEFT JOIN core.t_mtr_menu cc on bb.menu_id = cc.id  
                        WHERE dd.id ='".$access['g_id']."' AND cc.slug = '".$access['slug']."' AND bb.".$access['action']." ='t'";
            // if($access['action'] != ''){
            //     $sql .= "AND bb.".$access['action']." ='t'";
            // }
            $q = $this->db->query($sql);

            // return $q->result();
            if(count($q->result())<1){
                return false;
                }
                else{
                   return true; 
                }
            
          }


    public function delete2($table,$id)
    {
        $username=$this->session->userdata('username');
        $date=date('Y-m-d H:i:s');
        $query = $this->db->query("UPDATE $table 
            set status = 0, updated_by ='$username',updated_on='$date' 
            where id = '$id' and status = 1");
        if($this->db->trans_status() === FALSE) 
        { 
            $this->db->trans_rollback(); 
            return false;
        }
        else 
        { 
            $this->db->trans_commit(); 
            return true;
        }
    }

    /* Robai */
    function selectData($table, $where){
        $this->db->where('status', 1);
        return $this->db->get_where($table, $where)->result();
    }

    function saveData($table, $data){
        if ($this->input->is_cli_request()){
            $user = 0;
        }else{
            $user = $this->session->userdata('username');
        }
        
        $data['created_by'] = $user;
        $data['created_on'] = date('Y-m-d H:i:s');
        $this->db->insert($table, $data);

        if($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $this->db->insert_id();
        }
    } 

    function updateData($table, $data, $key){
        if ($this->input->is_cli_request()){
            $user = 0;
        }else{
            $user = $this->session->userdata('id');
        }

        $this->db->trans_begin();
        $data['updated_by'] = $user;
        $data['updated_on'] = date('Y-m-d H:i:s');

        $this->db->where($key,$data[$key]);
        unset($data[$key]);
        $this->db->update($table,$data);

        if($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            return true;
        }
    }

    function checkData($table, $where, $fieldId='', $id=''){
        $this->db->where('status', 1);

        if($fieldId != '' && $id != ''){
            $this->db->where("{$fieldId} != {$id}");
        }

        $this->db->limit(1);
        return $this->db->get_where($table, $where)->result();
    }

    function selectById($table, $where, $field){
        $this->db->where($where, $field);
        $this->db->where('status', 1);
        return $this->db->get($table)->row();
    }

    function selectAll($table) {
        $this->db->where('status', 1);
        return $this->db->get($table)->result();
    }

    function insertBatch($table, $arr){
        if ($this->input->is_cli_request()){
            $user = 0;
        }else{
            $user = $this->session->userdata('id');
        }

        $data = array();
        foreach ($arr as $key => $value) {
            $value['created_by'] = $user;
            $value['created_on'] = date('Y-m-d H:i:s');

            $data[] = $value;
        }

        $this->db->insert_batch($table, $data);
        if($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }else{
            return true;
        }
    }

    function updateBatch($table, $arr, $id){
        if ($this->input->is_cli_request()){
            $user = 0;
        }else{
            $user = $this->session->userdata('id');
        }

        $data = array();
        foreach ($arr as $key => $value) {
            $value['updated_by'] = $user;
            $value['updated_on'] = date('Y-m-d H:i:s');

            $data[] = $value;
        }

        $this->db->update_batch($table, $data, $id);
        if($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }else{
            return true;
        }
    } 

    function deleteDataMultiple($table,$where,$field){
        $this->db->where_in($where, $field);
        $this->db->delete($table);
        return $this->db->affected_rows();
    }


    function checkAccessMenuAction($url,$action_name)
    {

        $group_id=$this->session->userdata('group_id');

        $sql = "SELECT cc.id, cc.parent_id, cc.name, icon, slug, cc.order FROM
            core.t_mtr_privilege_web aa
        JOIN core.t_mtr_user_group bb ON bb.id = aa.user_group_id AND bb.status = 1 AND bb.id = $group_id
        JOIN core.t_mtr_menu_web cc ON cc.id = aa.menu_id AND cc.status = 1
        JOIN core.t_mtr_menu_detail_web dd ON dd.id = aa.menu_detail_id AND dd.status = 1
        JOIN core.t_mtr_menu_action ee ON ee.id = dd.action_id AND ee.status = 1 AND LOWER(ee.action_name) = '{$action_name}'
        WHERE aa.status = 1 and slug='{$url}' ORDER BY cc.order ASC";

        $data=$this->db->query($sql)->num_rows();

        if($data<=0)
        {
            redirect('error_404');
            // $this->load->view('error_404');
            exit;
        }


    }

    function select_data($table, $where="")
    {
        return $this->db->query("select * from $table $where");
    }

    public function getport()
    {
        return $this->db->query("SELECT * FROM app.t_mtr_port WHERE status=1")->result();
    }

    public function getregu()
    {
        return $this->db->query("SELECT * FROM core.t_mtr_team WHERE status=1")->result();
    }

    public function getpetugas()
    {
        return $this->db->query("SELECT * FROM core.t_mtr_user WHERE user_group_id = 4 AND status=1")->result();
    }

    public function getshift()
    {
        return $this->db->query("SELECT * FROM app.t_mtr_shift WHERE status=1")->result();
    }

    public function getclass()
    {
        return $this->db->query("SELECT * FROM app.t_mtr_ship_class WHERE status=1")->result();
    }

    function getconfigParam_byType($type) {
        $where ='';
        
        $type = $this->db->escape($type);
        $sql = "SELECT param_name, param_value FROM app.t_mtr_custom_param WHERE type = {$type} {$where} AND status = 1";
        $row = $this->db->query($sql)->result();
        if ($row) {
          return $row;
        } else {
          die('custom param  not found!');
        }
        
    }

    public function get_report_code($report_name)
    {
        $sql = $this->db->query("SELECT report_code FROM app.t_mtr_report WHERE report_name = '$report_name'");
        if ($sql->num_rows() > 0) {
            $sql = $sql->row();
            return $sql->report_code;
        }
    }

    public function status_approve($report_code, $port, $datefrom, $dateto, $shift, $ship_class = "")
    {
        $where_port = "";
        $where_shift = "";
        $where_ship_class = "";

        if ($port != "") {
            $where_port = "AND AR.port_id = $port";
        }

        if ($shift != "") {
            $where_shift = "AND AR.shift_id = $shift";
        }

        if ($ship_class != "") {
            $where_ship_class = "AND AR.ship_class = $ship_class";
        }

        $sql = "SELECT
                    DISTINCT(status)
                FROM
                    app.t_trx_approval_report AR
                WHERE
                    AR.report_code = '$report_code'
                    AND status <>'-5'
                    AND AR.report_date BETWEEN '$datefrom' AND '$dateto'
                    $where_shift
                    $where_port
                    $where_ship_class
                ORDER BY
                    status ASC";

        if ($this->db->query($sql)->num_rows() > 0) {
            $result = $this->db->query($sql)->row();
            return $result->status;
        }
    }

    public function get_rute($port_id)
    {
        $sql = "SELECT
                    ori.name AS origin,
                    des.name AS destination
                FROM
                    app.t_mtr_rute R
                    JOIN app.t_mtr_port ori ON ori.id = R.origin
                    JOIN app.t_mtr_port des ON des.id = R.destination
                WHERE
                    r.origin = $port_id";

        return $this->db->query($sql)->row();
    }

    public function get_branch($port_id, $ship_class)
    {
        $where_ship_class = "";

        if ($ship_class != "") {
            $where_ship_class = "AND ship_class = $ship_class";
        }
        $sql = "SELECT
                    json_agg(branch_name) AS branch_name
                FROM
                    app.t_mtr_branch b
                WHERE
                    port_id = $port_id
                    $where_ship_class";

        return $this->db->query($sql)->row();
    }

    public function get_team($port_id, $datefrom, $dateto, $shift_id)
    {
        $where_shift = "";

        if ($shift_id != "") {
            $where_shift = "AND AR.shift_id = $shift_id";
        }

        $sql = "SELECT
                    json_agg(team_name) AS team_name
                FROM
                    core.t_mtr_team
                WHERE
                    team_code IN (SELECT DISTINCT
                                    AR.team_code 
                                FROM
                                    app.t_trx_assignment_regu AR 
                                WHERE
                                    AR.port_id = $port_id
                                    AND assignment_date BETWEEN '$datefrom' AND '$dateto'
				    AND AR.status = 2
                                    $where_shift)";

        return $this->db->query($sql)->row();
    }

}