<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Log_website extends MY_Controller{
	public function __construct(){
		parent::__construct();

        logged_in();
        // $this->load->model('M_log','logs');
        $this->load->model('global_model');
        $this->load->library('log_activitytxt');

        // $this->_table    = 'app.t_mtr_dock';
        $this->_username = $this->session->userdata('username');
        $this->_module   = 'log/log_website';
	}

	public function index(){   
        checkUrlAccess(uri_string(),'view');
        
        $data = array(
            'home'     => 'Home',
            'url_home' => site_url('home'),
            'title'    => 'Log Website',
            // 'tampil'   => $this->readFile(),
            'table'    => '',
            'option'   => '',
            'date_p'   =>  date('Y-m-d'),
            'content'  => 'log_website/index'
        );

		$this->load->view('default', $data);
    }

    public function readFile(){

        $date_post = $this->input->post('date_from');
        
        $get_tahun = substr($date_post,0,4);
        $get_bulan = substr($date_post,5,2);
        $get_tanggal = substr($date_post,8,2);

        $dir_file = 'logs/'.$get_tahun.'/'.$get_bulan.'/';
        $name_file = 'log_api_'.$get_tanggal.'_'.$get_bulan.'_'.$get_tahun.'.txt';
        $filedir = $dir_file.$name_file;
        // $filedir = 'http://dev.nutech-integrasi.com/asdp_api/logs/2018/09/log_api_10_09_2018.log';
        $data = array();

        // if($this->does_url_exists($filedir)){
        if(file_exists($filedir)){

            $file = file($filedir);

                    
            foreach($file as $line_num => $line){
                $to_array = explode('|',$line);
                $count_array = count($to_array);
                // echo $count_array;
                // echo $to_array[0];
                // echo '<br>';
                if($to_array[0] > 1){
                    
                    $datetime = $to_array[0];

                    $date = substr($datetime,0,6);
                    $time = substr($datetime,6,6);

                    $to_date = substr(date('Y'),0,2).substr($date,0,2).'-'.substr($date,2,2).'-'.substr($date,4,6);
                    $to_time = substr($time,0,2).':'.substr($time,2,2).':'.substr($time,4,2);

                    $data_send['datetime'] = $to_date." ".$to_time;

                    $data_send['created_by'] = $to_array[1];
                    $data_send['url_response'] = str_replace(base_url(),'',$to_array[2]);
                    $data_send['methode'] = $to_array[3];
                    $string_array = $to_array[4];
                    $replace_ = str_replace(array('{','}'),array('',''),$string_array);
                    $arr_to_arr = explode(',',$replace_);
                    $da = array();
                    if($string_array != ""){
                        if(count($arr_to_arr) > 0){
                            foreach($arr_to_arr as $arr){
                                $replace = str_replace('"','',$arr);
                                $explode = explode(':', $replace);
                                $count_data = count($explode);
                                
                                if($count_data == 2){
                                    $arr_dat[$explode[0]] = $explode[1];
                                }else{
                                    $arr_dat = array();
                                }
                                $da[] = $arr_dat;
                            }
                        }
                    }
    
                    $data_send['array_data'] = $da;
                    $replace_res = str_replace(array('{','}'),array('',''),$to_array[5]);
                    $explode_res = explode(',',$replace_res);
                    if(count($explode_res) == 2){
                        $kode = explode(':',str_replace('"','',$explode_res[0]));
                        if(count($kode) == 2){
                            $kodenya = $kode[1];
                        }else{
                            $kodenya = null;
                        }
                        
                        $pesan = explode(':',str_replace('"','',$explode_res[1]));
                        if($pesan == 2){
                            $message = $pesan[1];
                        }else{
                            $message = '';
                        }
    
                        $a = array(
                            'code' => $kodenya,
                            'message' => $pesan[1]
                        );
                        $data_send['response_code'] = $kodenya;
                        $data_send['response_message'] = $pesan[1];
        
                    }else{
                        $data_send['response_code'] = '';
                        $data_send['response_message'] = '';
                    }
                    
    
                    $data[] = $data_send;
                }
    
            }
    
        }

        // $coy = array();
        // foreach($file as $line_num => $line){
        //     $ex = explode('|',$line);
        //     if(count($ex) > 1){
        //         $coy[] = $ex[4];
        //     }
        // }
        return array('data' => $data,'name' => $filedir);
    }

    function does_url_exists($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

    public function get_data(){
        validate_ajax();
        $array = $this->readFile();
        echo json_encode($array);
    }
}
