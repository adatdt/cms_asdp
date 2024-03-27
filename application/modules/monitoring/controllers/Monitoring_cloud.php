<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Monitoring_cloud extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        $this->load->model('M_cloud','cloud');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        $this->_table    = 'app.t_mtr_dock';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'monitoring/M_cloud';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        
        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Monitoring',
            //'tampil'   => $this->get_data(),
            'table'    => $this->create_table(),
            'option'   => $this->create_option(),
            // 'date_p'   =>  date('Y-m-d', strtotime("-1 day", strtotime(date("Y-m-d")))),
            'date_p'   =>  date('Y-m-d H:i:s',time() - 3600),
            'content'  => 'monitoring_cloud/index'
        );

		$this->load->view('default', $data);
    }

    public function get_data(){
        validate_ajax();
        $this->form_validation->set_rules('start_date', 'start_date', 'trim|required|callback_validate_date_time_format', array('validate_date_time_format' => 'Invalid format tanggal tanggal mulai'));
        $this->form_validation->set_rules('end_date', 'end_date', 'trim|required|callback_validate_date_time_format', array('validate_date_time_format' => 'Invalid format tanggal tanggal akhir'));
        $this->form_validation->set_rules('server_id', 'server_id', 'trim|required|alpha_numeric|max_length[4]');
        if ($this->form_validation->run() == FALSE) 
        {
            echo $res = json_api(0, validation_errors(),[]);
            exit;
        }
        $json = $this->cloud->get_count_data();
        $json["tokenHash"] = $this->security->get_csrf_hash();
        $json["csrfName"] = $this->security->get_csrf_token_name();
        echo json_encode($json);
    }

    private function create_option(){
        $html = '';
        $data = $this->cloud->server_local();
        foreach($data as $key => $val){
            $html .= '<option value="'.$val.'">'.$key.'</option>';
        }

        return $html;
    }

    private function create_table(){
        $table = $this->cloud->get_table();
        $thead = '';
        $trltc_local = '';
        $trltc_cloud = '';
        $trltc_status = '';
        $trctl_local = '';
        $trctl_cloud = '';
        $trctl_status = '';
        $num = 0;
        foreach($table as $data_table){
            $num++;
            $thead .= "<th>{$data_table}</th>";
            $trltc_local .= "<td align='center' data-id='ltc_local_{$num}' data-status='ltc_local_{$data_table}'>0</td>";
            $trltc_cloud .= "<td align='center' data-id='ltc_cloud_{$num}' data-status='ltc_cloud_{$data_table}'>0</td>";
            $trltc_status .= "<td align='center' data-id='ltc_status_{$num}'>-</td>";



            $trctl_local .= "<td align='center' data-id='ctl_local_{$num}' data-status='ctl_local_{$data_table}'>0</td>";
            $trctl_cloud .= "<td align='center' data-id='ctl_cloud_{$num}' data-status='ctl_cloud_{$data_table}'>0</td>";
            $trctl_status .= "<td align='center' data-id='ctl_status_{$num}'>-</td>";
        }
        $rowtable = count($table);
        $dd = $rowtable + 2;

        $html = "<thead>
        <tr>
            <th rowspan='2'>Status</th>
            <th rowspan='2'>Server</th>
            <th colspan='{$rowtable}'>Table</th>
        </tr>
        <tr>
            {$thead}
        </tr>
    </thead>
    <tbody>
        <tr>
            <th style='text-align: center;' rowspan='2'>Local To Cloud</th>
            <td>Local</td>
            {$trltc_local}
        </tr>
        <tr>
            <td>Cloud</td>
            {$trltc_cloud}
        </tr>
        <tr>
            <th style='text-align: center;' colspan='2'>Status Data</th>
            {$trltc_status}
        </tr>
        <tr id='center-tbl'>
            <th colspan='{$dd}'></th>
        </tr>
        <tr>
            <th style='text-align: center;' rowspan='2'>Cloud To Local</th>
            <td>Cloud</td>
            {$trctl_cloud}
        </tr>
        <tr>
            <td>Local</td>
            {$trctl_local}
        </tr>
        <tr>
            <th style='text-align: center;' colspan='2'>Status Data</th>
            {$trctl_status}
        </tr>

    </tbody>
    
    ";

        return $html;
    }

}
