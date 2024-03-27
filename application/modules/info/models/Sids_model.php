<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sids_model extends MY_Model
{
    function get_sids($port_id){
        $sql = "SELECT name FROM app.t_mtr_dock WHERE port_id = {$port_id} ORDER BY id";

        return $this->db->query($sql)->result();
    }
}