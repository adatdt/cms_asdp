<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Scheduler_model
 * -----------------------
 *
 * @author     arif rudianto
 * @copyright  2019
 *
 */

class Scheduler_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->_module = 'scheduler';
    }

    public function get_list_ship_image()
    {
        $sql = "SELECT * FROM app.t_mtr_ship_image WHERE status=1";

        return $this->db->query($sql)->result();
    }

    public function getFileAssets($iplocal)
    {
        $sql = "SELECT * FROM app.t_mtr_assets_device WHERE status = 1 AND ip_local  not in ('$iplocal') AND is_sync IS NOT TRUE";

        return $this->db->query($sql)->result();
    }


    public function updateStatusSyncFileAssets($id)
    {
        $sql = "UPDATE app.t_mtr_assets_device SET is_sync = true WHERE id = $id";

        return $this->db->query($sql);
    }
}
