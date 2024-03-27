<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library curl
 * @author adopabianko@gmail.com
 */

class Restcurl
{
    public function post($url, $data, $custHeader = array())
    {
        $curl = curl_init();

        $stdHeader = array(
            "cache-control: no-cache",
            "content-type: application/json"
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge($stdHeader, $custHeader),
        ));

        $response = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);


        $err = curl_error($curl);

        curl_close($curl);
        // echo $response;

        if ($err) {
            $res = (object) array(
                "status" => 99,
                "message" => "cURL Error #: " . $err,
                "data" => null
            );
        } else {
            $res = json_decode($body);

            if ($res == null) {
                $res = (object) array(
                    "status" => 99,
                    "message" => "Response null",
                    "data" => null
                );
            }
        }

        $res->headers = $headers;

        return $res;
    }

    public function get($url, $custHeader = array())
    {
        $curl = curl_init();

        $header = array(
            "cache-control: no-cache",
            "content-type: application/json"
        );

        $header = array_merge($header, $custHeader);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        // echo $response;

        if ($err) {
            $res = array(
                "code" => 99,
                "message" => "cURL Error #: " . $err,
                "data" => null
            );
        } else {
            $res = json_decode($response);

            if ($res == null) {
                $res = array(
                    "code" => 99,
                    "message" => "Response null",
                    "data" => null
                );
            }
        }

        return (object) $res;
    }

    public function postLogin($url, $data, $custHeader = array())
    {
        $curl = curl_init();

        $stdHeader = array(
            "cache-control: no-cache",
            "content-type: application/json"
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 35,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge($stdHeader, $custHeader),
        ));
        // set_time_limit(15);
        $start = date('Y-m-d H:i:s');
        $response = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);


        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        $end = date('Y-m-d H:i:s');
            $res = (object) array(
                "code" => 99,
                "success" => false,
                "message" => "cURL Error #: " . $err,
                "data" => null
            );

            $res->success = 'error';
        } else {
            $res = json_decode($body);


            if ($res == null) {
                $res = (object) array(
                    "code" => 99,
                    "success" => false,
                    "message" => "Response null",
                    "data" => null
                );
            }


        }

        $res->headers = $headers;

        return $res;
    }

    public function postSiwasops($url, $data, $custHeader = array())
    {
        $curl = curl_init();

        $stdHeader = array(
            "cache-control: no-cache",
            "content-type: application/json"
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 35,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge($stdHeader, $custHeader),
        ));

        $response = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);


        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            $res = (object) array(
                "code" => 99,
                "success" => false,
                "message" => "cURL Error #: " . $err,
                "data" => null
            );

            $res->success = 'failed';
        } else {
            $res = json_decode($body);

            if ($res == null) {
                $res = (object) array(
                    "code" => 99,
                    "success" => false,
                    "message" => "Response null",
                    "data" => null
                );
            }

            
        }

        $res->headers = $headers;

        return $res;
    }
}
