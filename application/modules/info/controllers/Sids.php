<?php
/**
 * -----------------
 * CLASS NAME : Sids
 * -----------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2019
 *
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Sids extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('sids_model', 'model');
        header("Access-Control-Allow-Origin: *");
    }

	public function index(){
        if(!isset($_POST['port_id'])){
            show_404();
        }
        echo json_encode($this->model->get_sids($this->input->post('port_id')));
	}
}
