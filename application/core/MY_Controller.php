<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        logged_in();
    }

    public function check_access($slug, $action)
    {
        $g_id       = $this->session->userdata('group_id');
        $access     = array('slug' => $slug, 'g_id' => $g_id, 'action' => $action);
        $getAccess  = $this->global_model->getAccess($access);
        if ($getAccess == false) {
            redirect('error_401');
        }
    }

    /*
    Document   : Pelabuhan
    Created on : 10 juli, 2023
    Author     : adi
    Description: Enhancement pasca angleb 2023
    */
    protected function get_json_data()
    {
        $json = json_decode(file_get_contents("php://input"), true);
        $post = $this->input->post();
        if ($json != NULL) {
            $_POST = $json;
            return $json;
        } else if ($post != NULL) {
            return $post;
        } else {
            echo json_encode(array('success' => 'false', 'message' => 'Parameter tidak ditemukan.'));
            exit();
        }
    }
    protected function validate_param_req($set, $message, $rules, $module, $errors = array())
    {
        $this->form_validation->set_rules($set, $message, $rules, $errors);
        if ($this->form_validation->run() == FALSE) {
            $response = array(
                'code'    => 102,
                'message' => validation_errors(),
                'data'    => null
            );
            $json_request = $this->get_json_data();
            /* Fungsi Create Log */
            $created_by   = 'system';
            $log_url      = site_url() . $module;
            $log_method   = 'POST';
            $log_param    = json_encode($json_request);
            $log_response = json_encode($response);

            $this->log_activitytxt->createLog($created_by, $log_url, $log_method, $log_param, $log_response);
            echo json_encode($response);
            exit;
        }
    }

    protected function validate_param_datatable($data, $module, $errors = array())
    {
        $value = "value";
        $this->form_validation->set_rules('start', 'start', 'trim|required|numeric');
        $this->form_validation->set_rules('length', 'length', 'trim|required|numeric');
        $this->form_validation->set_rules('order[]', 'order', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            // echo json_api(102, validation_errors(), []);
            $res = json_decode(json_api(102, validation_errors(), []));
            $res->recordsFiltered = 0;
            $res->recordsTotal = 0;
            echo json_encode($res);
            exit;
        }

        $column = "column";
        $dir = "dir";
        $this->form_validation->set_rules("order[0][" . $column . "]", 'trim|required|numeric');
        $this->form_validation->set_rules("order[0][" . $dir . "]", 'dir', 'trim|required|alpha');
        if ($this->form_validation->run() == FALSE) {
            // echo json_api(102, validation_errors(), []);
            $res = json_decode(json_api(102, validation_errors(), []));
            $res->recordsFiltered = 0;
            $res->recordsTotal = 0;
            echo json_encode($res);
            exit;
        }
        $this->form_validation->set_rules('search[' . $value . ']', 'search', 'trim|callback_special_char', array('special_char' => 'search has contain invalid characters'));
        if ($this->form_validation->run() == FALSE) {
            $res = json_decode(json_api(102, validation_errors(), []));
            $res->recordsFiltered = 0;
            $res->recordsTotal = 0;
            // print_r($res);
            echo json_encode($res);
            exit;
        }

        return true;
    }

    function special_char($str)
    {
        if (preg_match('/[^a-zA-Z0-9\s\-_\.,\?\/@.=+:]/', $str)) {

            return FALSE;
        }
        return  TRUE;
    }

    function special_char_url($str)
    {
        if (preg_match('/[^a-zA-Z0-9\s\-_\.,\?%#\/@.=+:\\\\]/', $str)) {


            return FALSE;
        }
        return  TRUE;
    }

    function validate_decode_param($str)
    {
        if (!$this->enc->decode($str)) {
            return FALSE;
        }


        return true;
    }

    public function validate_date_time($str)
    {
        if (isset($str) && !preg_match("/(^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\s([01]?[0-9]|2[0-4]):[0-5][0-9])$/", $str)) {
            return FALSE;
        }

        $str = explode(" ", $str);
        $date = explode("-", $str[0]);
        $valid = checkdate($date[1], $date[2], $date[0]);

        if (!$valid) {
            return FALSE;
        }

        return TRUE;
    }

    public function validate_date($str)
    {
        if (isset($str) && !preg_match("/(^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])|\'[0-9]{4}\-[0-9]{2}\-[0-9]{2}\'|\"[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\")$/", $str)) {
            return FALSE;
        }

        $str = explode("-", $str);
        $valid = checkdate($str[1], $str[2], $str[0]);

        if (!$valid) {
            return FALSE;
        }

        return TRUE;
    }

    function validate_date_time_format($str){
        $format = "Y-m-d H:i:s";
        $dateObj1 = DateTime::createFromFormat($format, $str);
        if (!($dateObj1 && $dateObj1->format($format) == $str)) {
                return FALSE; 
            }
        return  TRUE;
        
    }

    function letter_number_val ($num) {
        return ( ! preg_match("/^([a-zA-Z0-9-(_)+&!*?%.@=:#,\s])+$/D", $num)) ? FALSE : TRUE;
    }


    function valid_time_format($str){
        $format = 'H:i';
        $dateObj1 = DateTime::createFromFormat($format, $str);
        if (!($dateObj1 && $dateObj1->format($format) == $str)) {
            return FALSE; 
        }
        return  TRUE;
    
    }

    function validate_date_time_minutes($str){
        $format = "Y-m-d H:i";
        $dateObj1 = DateTime::createFromFormat($format, $str);
        if (!($dateObj1 && $dateObj1->format($format) == $str)) {
                return FALSE; 
            }
        return  TRUE;
        
    }

    function special_char_news($str)
    {
        if (preg_match('/[<>*]/', $str)) {

            return FALSE;
        }
        return  TRUE;
    }

    /*
    Document   : Pelabuhan
    Created on : 10 juli, 2023
    Author     : adi
    Description: end Enhancement pasca angleb 2023
    */
}
