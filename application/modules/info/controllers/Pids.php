<?php
/**
 * -----------------
 * CLASS NAME : Pids
 * -----------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2019
 *
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Pids extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('pids_model', 'model');
        header("Access-Control-Allow-Origin: *");
    }

    public function index(){
        if($this->input->server('REQUEST_METHOD') != 'POST'){
            show_404();
        }

        echo json_encode($this->model->get_pids($this->input->post('port_id')));
    }

    public function isDateBetweenDates($date, $startDate, $endDate) {
        return $date > $startDate && $date < $endDate;
    }

    public function getWeather(){
        if($this->input->server('REQUEST_METHOD') != 'POST'){
            show_404();
        }

        $xmlUrl = $this->input->post('url_weather');
        // $xmlUrl = base_url().'assets/sample.xml';
        $xmlStr = file_get_contents($xmlUrl);
        $xmlObj = simplexml_load_string($xmlStr);
        $arrXml = $this->objectsIntoArray($xmlObj);
        $data   = array();

        if($arrXml){
            $area = $arrXml['forecast']['area'];
            foreach ($area as $key => $row) {
                if($row['@attributes']['id'] == $this->input->post('code_area')){
                    // $data['all'] = $row;
                    $data['city'] = $row['name'][0].',<br>'.$row['name'][1];

                    foreach ($row['parameter'] as $k => $r) {
                        if($r['@attributes']['id'] == 'weather'){
                            $weather = $this->getValueHours($r['timerange']);

                            if($weather){
                                $data['weather'] = $this->getIconWeater($weather['value'],$weather['hour']);
                            }
                        }

                        if($r['@attributes']['id'] == 't'){
                            $t = $this->getValueHours($r['timerange']);
                            if($t){
                                $data['temperature'] = $t['value'];
                            }
                        }
                    }
                }
            }
        }

        echo json_encode($data);
    }

    function objectsIntoArray($arrObjData, $arrSkipIndices = array()){
        $arrData = array();

        if (is_object($arrObjData)) {
            $arrObjData = get_object_vars($arrObjData);
        }

        if (is_array($arrObjData)) {
            foreach ($arrObjData as $index => $value) {
                if (is_object($value) || is_array($value)) {
                    $value = $this->objectsIntoArray($value, $arrSkipIndices);
                }

                if (in_array($index, $arrSkipIndices)) {
                    continue;
                }

                $arrData[$index] = $value;
            }
        }
        return $arrData;
    }

    function getValueHours($data){
        $arr   = array();
        $hours = array('0000','1800');

        foreach ($data as $kk => $rr) {
            $dateNow    = date('YmdHi');
            $h          = $rr['@attributes']['h'];
            $startDate  = $rr['@attributes']['datetime'];
            $endDate    = date('YmdHi',strtotime('+6 hour',strtotime($startDate)));

            if($this->isDateBetweenDates($dateNow, $startDate, $endDate)){
                $hour = "siang";

                if(in_array(substr($startDate, 8), $hours)){
                    $hour = "malam";
                }

                $arr['hour']  = $hour;
                $arr['value'] = $rr['value'];
            }
        }

        return $arr;
    }

    function getIconWeater($value,$w){
        // 0 : Cerah
        // 1 : Cerah Berawan
        // 2 : Cerah Berawan
        // 3 : Berawan
        // 4 : Berawan Tebal
        // 5 : Udara Kabur
        // 10 : Asap
        // 45 : Kabut
        // 60 : Hujan Ringan
        // 61 : Hujan Sedang
        // 63 : Hujan Lebat
        // 80 : Hujan Lokal
        // 95 : Hujan Petir
        // 97 : Hujan Petir
        // 100 Cerah
        // 101 Cerah Berawan
        // 102 Cerah Berawan
        // 103 Berawan
        // 104 Berawan Tebal
        $array = array();
        $day = $w == "siang" ? "day" : "night-alt";
        switch ($value) {
            case '0':
            case '100':
                $array['icon'] = 'icon-cerah-'.$w.'.png';
                $array['description'] = 'Cerah';
                $array['font_icon'] = 'wi-'.$day.'-sunny';
            break;

            case '1':
            case '2';
            case '101':
            case '102':
                $array['icon'] = 'icon-cerah-berawan-'.$w.'.png';
                $array['description'] = 'Cerah Berawan';
                $array['font_icon'] = 'wi-' . $day . '-cloudy';
            break;

            case '3':
            case '103':
                $array['icon'] = 'icon-berawan.png';
                $array['description'] = 'Berawan';
                $array['font_icon'] = 'wi-cloud';
            break;

            case '4':
            case '104':
                $array['icon'] = 'icon-berawan-tebal.png';
                $array['description'] = 'Berawan Tebal';
                $array['font_icon'] = 'wi-cloudy';
            break;

            case '5':
                $array['icon'] = 'icon-udara-kabur-'.$w.'.png';
                $array['description'] = 'Udara Kabur';
                $array['font_icon'] = 'wi-day-haze';                
            break;

            case '10':
                $array['icon'] = 'icon-asap.png';
                $array['description'] = 'Asap';
                $array['font_icon'] = 'wi-dust';
            break;

            case '45':
                $array['icon'] = 'icon-kabut-'.$w.'.png';
                $array['description'] = 'Kabut';
                $array['font_icon'] = 'wi-fog';
            break;

            case '60':
                $array['icon'] = 'icon-hujan-ringan.png';
                $array['description'] = 'Hujan Ringan';
                $array['font_icon'] = 'wi-showers';
            break;

            case '61':
                $array['icon'] = 'icon-hujan-sedang.png';
                $array['description'] = 'Hujan Sedang';
                $array['font_icon'] = 'wi-rain-mix';
            break;

            case '63':
                $array['icon'] = 'icon-hujan-lebat.png';
                $array['description'] = 'Hujan Lebat';
                $array['font_icon'] = 'wi-rain';
            break;

            case '80':
                $array['icon'] = 'icon-hujan-local-'.$w.'.png';
                $array['description'] = 'Hujan Lokal';
                $array['font_icon'] = 'wi-' . $day . '-rain-mix';
            break;

            case '95':
            case '97':
                $array['icon'] = 'icon-hujan-petir.png';
                $array['description'] = 'Hujan Petir';
                $array['font_icon'] = 'wi-storm-showers';
            break;

            default;
                $array['icon'] = null;
                $array['description'] = null;
            break;
        }

        return $array;
    }

    public function getVRS()
    {
        if($this->input->server('REQUEST_METHOD') != 'POST'){
            show_404();
        }

        $port_id = $this->input->post('port_id');

        $data   = array();
        $data_video = $this->model->get_video($port_id);
        $data_text = $this->model->get_text($port_id);

        $path = array();
        $text = array();
        $slider = array();

        if ($data_video) {
            foreach ($data_video as $key => $value) {
                $path[] = $value->path;
            }
        }

        if ($data_text) {
            foreach ($data_text as $key => $value) {
                $text[] = $value->text;
            }
        }

        $data = array(
            'video' => $path,
            'slider' => $slider,
            'running_text' => $text,
        );

        echo json_encode($data);
    }
}
