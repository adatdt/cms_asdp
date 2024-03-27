<?php

// use function GuzzleHttp\json_encode;

defined('BASEPATH') or exit('No direct script access allowed');

class Stc extends MY_Controller
{
    // $param[0] => schedule_code
    // $param[1] => schedule_id trx
    // $param[2] => schedule_id mtr
    // $param[3] => ship_id
    // $param[4] => nama dermaga
    // $param[5] => plot_date
    // $param[6] => dock_date
    // $param[7] => open_boarding_date
    // $param[8] => close_boarding_date
    // $param[9] => close_ramp_door_date
    // $param[10] => sail_date
    // $param[11] => dock_id

    public function __construct()
    {
        parent::__construct();

        logged_in();
        $this->load->model('m_stc', 'stc');
        $this->load->model('info/pids_model', 'pids');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'transaction/stc';

        $this->dbView=checkReplication(); // seting db  untuk ke slave

        header("Access-Control-Allow-Origin: *");
    }

    public function index()
    {
        checkUrlAccess(uri_string(), 'view');
        $identity_app = $this->stc->cek_identity_app();
        $sess_port    = $this->session->userdata('port_id');

        if ($identity_app == -1) {
            $port_name = 'Aplikasi tidak teridenfikasi';
        } else {
            if ($identity_app == 0) {
                $port_name = 'SERVER CLOUD';
            } else {
                $row = $this->global_model->selectById('app.t_mtr_port', 'id', $identity_app);
                if ($row) {
                    $port_name = 'PELABUHAN '.$row->name;
                } else {
                    $port_name = 'Pelabuhan tidak ditemukan';
                }
            }
        }

        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'BOARDING KAPAL',
            'content'  => 'stc/index',
            'port'     => $this->stc->get_list_port(),
            'port_id'  => $this->enc->encode($sess_port),
            'port_name' => $port_name,
            'socket_url' => $this->config->item('socket_url'),
            // 'socket_url' => $_SERVER['HTTP_HOST'].':3000',
            // 'socket_url' => 'http://dev.nutech-integrasi.com:3000',
            // 'socket_url' => 'http://10.20.10.5:3000',
            'problem' => array(
                array('id' => 'list-anchor','problem' => $this->enc->encode(2),'list' => 'List Anchor', 'title' => 'Anchor'),
                array('id' => 'list-docking','problem' => $this->enc->encode(3),'list' => 'List Docking', 'title' => 'Docking'),
                array('id' => 'list-broken','problem' => $this->enc->encode(4),'list' => 'List Rusak', 'title' => 'Rusak'),
            ),
            'url_problem' => site_url($this->_module.'/problem'),
            'list_gate' => $this->stc->listGate($this->session->userdata('port_id'))
        );

        $this->load->view('default', $data);
    }

    public function edit($param)
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');

        if ($param) {
            $params = explode('|', $this->enc->decode($param));
            $status_boarding = '';
            $boarding_code = '';

            if (($params[7] || $params[8] || $params[9]) && !$params[10]) {
                $boarding = $this->stc->check_open_boarding($params[0]);
                $check_approval = $this->stc->check_approval($params[0]);

                if ($check_approval) {
                    $status_boarding = $check_approval;
                    $boarding_code = $boarding['boarding_code'];
                } else {
                    if ($boarding) {
                        $status_boarding = $boarding['status'];
                        $boarding_code = $boarding['boarding_code'];
                    }
                }
            }

            //check status gate
            $terminal_code =  $this->stc->gate($this->session->userdata('port_id'), $params[11]);
            $status_gate = 0;
            $last_update_gate = "";
            if ($terminal_code) {
                $open_gate = $this->stc->check_gate("app.t_trx_open_manless_gate", $terminal_code->terminal_code);
                $close_gate = $this->stc->check_gate("app.t_trx_close_manless_gate", $terminal_code->terminal_code);

                if ($open_gate && $close_gate) {
                    if ($open_gate->created_on < $close_gate->created_on) {
                        $status_gate = 0;
                        $last_update_gate = $close_gate->created_on;
                    } else {
                        $status_gate = 1;
                        $last_update_gate = $open_gate->created_on;
                    }
                } elseif ($open_gate) {
                    $status_gate = 1;
                    $last_update_gate = $open_gate->created_on;
                } elseif ($close_gate) {
                    $status_gate = 0;
                    $last_update_gate = $close_gate->created_on;
                } else {
                    $status_gate = 0;
                    $last_update_gate = "";
                }
            }

            $data = array(
                'title' => strtoupper('Kapal Masuk'),
                'ship'  => $this->stc->get_list_ship($this->session->userdata('port_id'), $this->stc->get_id_schecule($params[0])),
                'id_ship' => $this->stc->get_id_schecule($params[0]),
                'schedule' => $this->stc->get_trx_schecule($params[1]),
                'serial_number' => $this->stc->get_imei_validator($params[11], $this->session->userdata('port_id')),
                'dock_id' => $params[11],
                'dock_name' => $params[4],
                'status_boarding' => $status_boarding,
                'date_ploting' => (!$params[5] && !$params[7]) ? false : true,
                'gate' => $this->stc->dropdown_gate($this->session->userdata('port_id'), $params[11]),
                'manless' => $this->getManlessGate($this->session->userdata('port_id'), $params[11]),
                'socket_url' => $this->config->item('socket_url'),
                // 'socket_url' => 'http://dev.nutech-integrasi.com:3000',
                // 'socket_url' => 'http://10.30.10.5:3000',
                'param' => $param,
                'boarding_code' => $boarding_code,
                'schedule_code' => $params[0],
                'status_gate' => $status_gate,
                'last_update_gate' => $last_update_gate,
            );

            $this->load->view($this->_module.'/edit', $data);
        } else {
            show_404();
        }
    }

    public function action_edit()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');

        $post   = $this->input->post();
        $param  = explode('|', $this->enc->decode($post['param']));
        // echo json_encode($param[12]);exit;
        $type   = $this->enc->decode($post['type']);
        $ship   = $this->enc->decode($post['ship']);

        if ($this->enc->decode($post['param']) && $type && $ship) {
            if (isset($post['call'])) {
                if ($post['call'] == '') {
                    $res = json_api(0, 'Call sandar tidak boleh kosong');
                } elseif ($post['call'] < 0) {
                    $res = json_api(0, 'Call sandar tidak boleh lebih kecil dari 0');
                } else {
                    $query = $this->stc->trx_schedule();
                    if ($query) {
                        $res = json_api(1, 'Update Data Berhasil', array('post' => $post, 'pids' => $this->pids->get_pids($this->session->userdata('port_id'))));
                        // $res = json_api(1, 'Update Data Berhasil', array('post' => $post, 'pids' => ""));
                    } else {
                        $res = json_encode($this->db->error());
                    }
                }
            } else {
                if ($type == 3 and !$this->stc->get_shift($param[12])) {
                    $res = json_api(0, 'Shift tidak ada atau shift lebih dari satu');
                } else {
                    $query = $this->stc->trx_schedule();
                    if ($query == 1) {
                        $res = json_api(1, 'Update Data Berhasil', array('post' => $post, 'pids' => $this->pids->get_pids($this->session->userdata('port_id'))));
                        // $res = json_api(1, 'Update Data Berhasil', array('post' => $post, 'pids' =>""));
                    } elseif ($query == 2) {
                        $res = json_api(0, 'Ada jadwal lain yang belum berlayar');
                    } elseif ($query) {
                        $res = json_encode($this->db->error());
                    }
                }
            }
        } else {
            $res = json_api(0, 'Data yang Anda kirim salah');
        }

        $createdBy   = $this->session->userdata('username');
        $logUrl      = uri_string();
        $logMethod   = 'EDIT';
        $logParam    = json_encode($post);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        echo $res;
    }

    public function problem($param)
    {
        validate_ajax();
        $params = explode('|', $this->enc->decode($param));
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');

        $data = array(
                'title' => strtoupper('Kapal '.$params[1]),
                'ship'  => $this->stc->get_list_ship($this->session->userdata('port_id'), 0),
                'problem_id' => $this->enc->encode($params[0]),
                'dock' => $this->global_model->select_data("app.t_mtr_dock", "where port_id={$this->session->userdata("port_id")} ")->result(),
                'socket_url' => $_SERVER['HTTP_HOST'].':3000',
            );
        $this->load->view($this->_module.'/problem', $data);
    }

    public function set_problem()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');

        $post   = $this->input->post();
        $param  = explode('|', $this->enc->decode($post['param']));
        $type   = $this->enc->decode($post['type']);
        $ploting   = $this->enc->decode($post['ploting']);

        // echo $param[0];exit;

        if ($param && $type && $ploting) {
            if ($ploting == 1) {
                $res = json_api(0, 'Kapal belum masuk alur');
            } elseif ($ploting == 2 and $type == 2) {
                $res = json_api(0, 'Kapal harus sandar terlebih dahulu');
            } elseif ($ploting == 4 || $ploting == 5) {
                $res = json_api(0, 'Silahkan tutup ramdor terlebih dahulu');
            } elseif ($ploting == 7) {
                $res = json_api(0, 'Kapal sudah berlayar');
            } else {
                $query = $this->stc->insert_problem();
                if ($query) {
                    $res = json_api(1, 'Insert data berhasil');
                } else {
                    $res = json_encode($this->db->error());
                }
            }
        } else {
            $res = json_api(0, 'Data yang Anda kirim salah');
        }

        $createdBy   = $this->session->userdata('username');
        $logUrl      = uri_string();
        $logMethod   = 'ADD';
        $logParam    = json_encode($post);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        echo $res;
    }

    public function set_problem2()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');

        $post   = $this->input->post();
        // $param  = explode('|', $this->enc->decode($post['param']));
        $type   = $this->enc->decode($post['type']);
        $ship_id   = $this->enc->decode($post['ship_id']);

        $query = $this->stc->insert_problem2();
        if ($query) {
            $res = json_api(1, 'Insert data berhasil');
        } else {
            $res = json_encode($this->db->error());
        }

        $createdBy   = $this->session->userdata('username');
        $logUrl      = uri_string();
        $logMethod   = 'ADD';
        $logParam    = json_encode($post);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        echo $res;
    }

    public function list_dock()
    {
        validate_ajax();
        echo $this->stc->get_list_dock();
    }

    public function action_gate()
    {
        validate_ajax();
        $this->global_model->checkAccessMenuAction($this->_module, 'edit');

        $post   = $this->input->post();

        $param  = explode('|', $this->enc->decode($post['param']));
      
        if (!$this->enc->decode($post['param'])) {
            $res = json_api(0, 'Data yang dikirim salah');
        } else {
            $query = $this->stc->gate_action($param);
            $manless = $this->getManlessGate($this->session->userdata('port_id'), $param[11]);
            if ($query == 1) {
                $res = json_api($query, 'Action berhasil', $manless);
            } elseif ($query == 2) {
                $res = json_api($query, 'Kapal belum ada di dermaga');
            } else {
                $res = json_encode($this->db->error());
            }
        }

        $createdBy   = $this->session->userdata('username');
        $logUrl      = uri_string();
        $logMethod   = 'action gate';
        $logParam    = json_encode($post);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
        echo $res;
    }

    public function getManlessGate($port, $dock)
    {
        $data = $this->stc->listManlessGate($port, $dock);
        $obj = array();
        foreach ($data as $key => $value) {
            if ($value->last_open && $value->last_close) {
                if ($value->last_open < $value->last_close) {
                    $status_gate = 0;
                    $last_update_gate = $value->last_close;
                } else {
                    $status_gate = 1;
                    $last_update_gate = $value->last_open;
                }
            } elseif ($value->last_open) {
                $status_gate = 1;
                $last_update_gate = $value->last_open;
            } elseif ($value->last_close) {
                $status_gate = 0;
                $last_update_gate = $value->last_close;
            } else {
                $status_gate = 0;
                $last_update_gate = "";
            }

            $obj[$key]['name'] = $value->terminal_name;
            $obj[$key]['terminal_code'] = $value->terminal_code;
            $obj[$key]['status'] = $status_gate;
            $obj[$key]['last_update'] = $last_update_gate;
        }
        
        return $obj;
    }
}
