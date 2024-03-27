<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : Port_model
 * -----------------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class CmsCiLogModel extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->_module = 'transaction/booking';
	}

	public function dataList()
	{

		$dateTo = trim($this->input->post('dateTo'));
		$dateFrom = trim($this->input->post('dateFrom'));

		// handling
		$getStartYear = $dateTo >= $dateFrom ? $dateFrom : $dateTo; // mencari year terkecil
		$getEndYear = $dateTo <= $dateFrom ? $dateFrom : $dateTo; // mencari year terbesar

		$range = $this->dateRange($getStartYear, $getEndYear); // get range date in array

		// print_r($range); exit;
		$data = array();
		foreach ($range as $key => $dateRange) {

			$getYear = date("Y", strtotime($dateRange));
			$getMonth = date("m", strtotime($dateRange));

			// $filePath = APPPATH.'logs/'.$getYear.'/'.$getMonth.'/log-'.$dateRange.'.php';
			$filePath = APPPATH . '../logs/errors/' . $getYear . '/' . $getMonth . '/log-' . $dateRange . '.php';
			if (file_exists($filePath)) {
				// $getLogData = $this->getLogFile($filePath);
				$getLogData = $this->processLogs($this->getLogs($filePath));
				foreach ($getLogData as $key => $value) {
					$data[] = $value;
				}
			}
		}

		return array(
			'data'      => 	$data
		);
	}

	public function getLogFile($file_path)
	{
		// $file_path = APPPATH.'logs/2021/02/log-2021-02-23.php';

		$myfile = fopen("$file_path", "r") or die("Unable to open file!");

		$getDataFile = fread($myfile, filesize($file_path));

		$explode = explode('ERROR -', $getDataFile);

		$logDetailExplode = array();
		foreach ($explode as $key => $value) {

			if ($key != 0) // ke 0 kosong 
			{
				$explodeDetail = explode('-->', $value);

				$logDetailExplode[] = array(

					"data" => $value,
					"date" => $explodeDetail[0] // ambil tanggal
				);
			}
		}

		// fclose($myfile);

		return $logDetailExplode;
	}

	function dateRange($first, $last, $step = '+1 day', $format = 'Y-m-d')
	{

		$dates = array();
		$current = strtotime($first);
		$last = strtotime($last);

		while ($current <= $last) {

			$dates[] = date($format, $current);
			$current = strtotime($step, $current);
		}

		return $dates;
	}


	public function log($currentFile)
	{
		$logs = $this->processLogs($this->getLogs($currentFile));
		return $logs;
	}

	private $CI;

	private static $levelsIcon = [
		// 'INFO'  => 'glyphicon glyphicon-info-sign',
		// 'ERROR' => 'glyphicon glyphicon-warning-sign',
		// 'DEBUG' => 'glyphicon glyphicon-exclamation-sign',
		// 'ALL'   => 'glyphicon glyphicon-minus',
		'INFO'  => 'icon-info',
		'ERROR' => 'icon-shield',
		'DEBUG' => 'icon-fire',
		'ALL'   => 'icon-puzzle',
	];

	private static $levelClasses = [
		'INFO'  => 'info',
		'ERROR' => 'danger',
		'DEBUG' => 'warning',
		'ALL'   => 'muted',
	];


	const LOG_LINE_START_PATTERN = "/((INFO)|(ERROR)|(DEBUG)|(ALL))[\s\-\d:\.\/]+(--> )/";
	const LOG_DATE_PATTERN = ["/^((ERROR)|(INFO)|(DEBUG)|(ALL))\s\-\s/", "/\s(-->)/"];
	const LOG_LEVEL_PATTERN = "/^((ERROR)|(INFO)|(DEBUG)|(ALL))/";
	const MAX_LOG_SIZE = 52428800; //50MB
	const MAX_STRING_LENGTH = 300; //300 chars

	/*
     * This function will process the logs. Extract the log level, icon class and other information
     * from each line of log and then arrange them in another array that is returned to the view for processing
     *
     * @params logs. The raw logs as read from the log file
     * @return array. An [[], [], [] ...] where each element is a processed log line
     * */
	function processLogs($logs)
	{

		if (is_null($logs)) {
			return null;
		}

		$superLog = [];

		foreach ($logs as $log) {

			//get the logLine Start
			$logLineStart = $this->getLogLineStart($log);

			if (!empty($logLineStart)) {
				//this is actually the start of a new log and not just another line from previous log
				$level = $this->getLogLevel($logLineStart);
				$data = [
					"level" => $level,
					"date" => $this->getLogDate($logLineStart),
					"icon" => self::$levelsIcon[$level],
					"class" => self::$levelClasses[$level],
				];

				$logMessage = preg_replace(self::LOG_LINE_START_PATTERN, '', $log);

				if (strlen($logMessage) > self::MAX_STRING_LENGTH) {
					$data['content'] = substr($logMessage, 0, self::MAX_STRING_LENGTH);
					$data["extra"] = (substr($logMessage, (self::MAX_STRING_LENGTH + 1)));
				} else {
					$data["content"] = $logMessage;
				}

				array_push($superLog, $data);
			} else if (!empty($superLog)) {
				//this log line is a continuation of previous logline
				//so let's add them as extra
				$prevLog = $superLog[count($superLog) - 1];
				$extra = (array_key_exists("extra", $prevLog)) ? $prevLog["extra"] : "";
				$prevLog["extra"] = ($extra . $log);
				$superLog[count($superLog) - 1] = $prevLog;
			} 
			// else {
			// 	//this means the file has content that are not logged
			// 	//using log_message()
			// 	//they may be sensitive! so we are just skipping this
			// 	//other we could have just insert them like this
			// 	array_push($superLog, [
			// 		"level" => "INFO",
			// 		"date" => "",
			// 		"icon" => self::$levelsIcon["INFO"],
			// 		"class" => self::$levelClasses["INFO"],
			// 		"content" => $log
			// 	]);
			// }
		}

		return $superLog;
	}

	/*
     * extract the log level from the logLine
     * @param $logLineStart - The single line that is the start of log line.
     * extracted by getLogLineStart()
     *
     * @return log level e.g. ERROR, DEBUG, INFO
     * */
	function getLogLevel($logLineStart)
	{
		preg_match(self::LOG_LEVEL_PATTERN, $logLineStart, $matches);
		return $matches[0];
	}

	function getLogDate($logLineStart)
	{
		return preg_replace(self::LOG_DATE_PATTERN, '', $logLineStart);
	}

	function getLogLineStart($logLine)
	{
		preg_match(self::LOG_LINE_START_PATTERN, $logLine, $matches);
		if (!empty($matches)) {
			return $matches[0];
		}
		return "";
	}

	/*
     * returns an array of the file contents
     * each element in the array is a line
     * in the underlying log file
     * @returns array | each line of file contents is an entry in the returned array.
     * @params complete fileName
     * */
	private function getLogs($fileName)
	{
		$size = filesize($fileName);
		if (!$size || $size > self::MAX_LOG_SIZE)
			return null;
		// return file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		return file($fileName, FILE_SKIP_EMPTY_LINES);
	}
}
