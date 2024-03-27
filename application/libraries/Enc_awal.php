<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Enc
{
    var $needle = array(
        '/', '\\', '+', '='
    );

    var $replacing_str = array(
        'ss11', 'bs12', 'ps13', 'eq14' 
    );

	public function __construct()
    {
        $CI =& get_instance();
        $CI->load->library('encrypt');
    }

    function encode($the_message)
    {
        $CI =& get_instance();

    	$text = $CI->encrypt->encode($the_message);
        $the_fog = $text;
        for($i=0; $i<4; $i++)
        {
            $the_fog = str_replace($this->needle[$i], $this->replacing_str[$i], $the_fog);
        }

    	return $the_fog;
    }

    function decode($the_fog)
    {
        $CI =& get_instance();

        for($i=0; $i<4; $i++)
        {
            $the_fog = str_replace($this->replacing_str[$i], $this->needle[$i], $the_fog);
        }

        $the_message = $CI->encrypt->decode($the_fog);

    	return $the_message;
    }
}