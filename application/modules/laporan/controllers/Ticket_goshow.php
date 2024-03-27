<?php
/**
 * Module	: Reports
 * Author	: ttg <blekedeg@gmail.com>
 */
class Ticket_goshow extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('ticket_goshow_model');
        $this->_module   = 'laporan/ticket_goshow';
    }

    public function index(){
        checkUrlAccess(uri_string(),'view');
        if($this->input->is_ajax_request()){
    		$rows = $this->ticket_goshow_model->ticketGoshowList();
            echo json_encode($rows);
            exit;
    	}

    	$data = array(
    		'home' => 'Beranda',
    		'url_home' => site_url('home'),
    		'title' => 'Ticket Go Show',
    		'content' => 'ticket_goshow/index',
            'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
            'download_excel' => checkBtnAccess($this->_module,'download_excel'),
    	);

    	$this->load->view ('default', $data);
    }

    public function detail($param){
        checkUrlAccess($this->_module,'detail');
        $get = $this->enc->decode($param);
        $exp = explode('|', $get);

        if($this->input->is_ajax_request()){
            $rows = $this->ticket_goshow_model->getDetail($exp[0],$exp[1],$exp[2]);
            echo json_encode($rows);
            exit;
        }
        
        $data = array(
            'home'        => 'Home',
            'url_home'    => site_url('home'),
            'parent1'     => 'Laporan',
            'url_parent1' => '#',
            'parent2'     => 'Pendapatan Go Show',
            'url_parent2' => site_url('laporan/ticket_goshow'),
            'title'       => 'Detail Pendapatan Go-Show',
            'date'        => $exp[0],
            'origin'      => $this->ticket_goshow_model->getPort($exp[1]),
            'destination' => $this->ticket_goshow_model->getPort($exp[2]),
            'url'         => site_url('laporan/ticket_goshow/detail/'.$param),
            'download_pdf' => checkBtnAccess($this->_module,'download_pdf'),
            'download_excel' => checkBtnAccess($this->_module,'download_excel'),
            'content'     => 'ticket_goshow/detail',
        );

        $this->load->view('default', $data);
    }
}
