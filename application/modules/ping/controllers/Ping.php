<?php

defined('BASEPATH') or exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

class Ping extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        //ipAuth();
    }

    function ping($host, $port, $timeout) 
    { 
        $tB = microtime(true); 
        $fP = fSockOpen($host, $port, $errno, $errstr, $timeout); 
  
        if (!$fP) 
        { 
            return "rto"; 
        } 
  
        $tA = microtime(true); 
        return round((($tA - $tB) * 1000), 0)." ms"; 
    }

    function logPing($log)
    {      
        $directory = APPPATH.'logs/ping/';
        $fileName = 'log_ping_'.date('Ymd').'.log';

        if (! is_dir($directory))
        {
            mkdir($directory, 0777, TRUE);
        }

        $createdOn = date('Y-m-d H:i:s');

        $message = '['.$createdOn.'] - ';        
        $message .= 'RESPONSE: '.$log."\n";        
        
        $logFile = $directory.DIRECTORY_SEPARATOR.$fileName;

        write_file($logFile, $message, 'a');

        chmod($logFile, 0777);
        chown($logFile, 'apache');
        chgrp($logFile, 'apache');       
    }

    function doPing_post()
    {
        $result = $this->ping("36.92.28.90", 80, 10);
        $this->logPing($result);
        // echo "end";
        exit;
    }
}