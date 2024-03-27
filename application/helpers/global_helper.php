<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* jika user belum login maka akan di redirect ke halaman login
* @param $ci
* @return this
*/

if ( !function_exists('logged_in') ) {

  function logged_in() {
    $ci =& get_instance();
    $ci->load->library('session');
    if ($ci->session->userdata('logged_in') == FALSE){redirect('login');}
    
  }

//   function cetak($str){
//     echo htmlentities($str, ENT_QUOTES, 'UTF-8');
// }

// function checkUrl($g_id,$current_url){	
// 	if($current_url==''){
// 		return true;
// 	}
// 	else{
// 		$a =explode('/',$current_url);
// 	$i=count($a);
// 	$arr=null;
// 	$arrayName = array('view','add','edit','delete','approval' );
// 	for($x=0;$x<$i;$x++){
		
// 		$arr=array_search($a[$x],$arrayName);
// 		if($arr != null){
// 			$x=$i;
// 		}
		
// 	}
	
// 	$slug = substr($current_url, 0, strpos($current_url, '/'.$arrayName[$arr]));
// 	// print($current_url);echo "<br>";
// 	// print_r($slug);
// 	if($arr==null){
// 		$access = array('g_id'=> $g_id ,'slug' => $current_url ,'action' => '' );
// 	}
// 	else{
// 	    	$access = array('g_id'=> $g_id ,'slug' => $slug ,'action' => $arrayName[$arr] );
// 	    }
// 	$THIS = get_instance();
// 	$THIS->load->model('global_model','global');
// 	return $THIS->global->getAccess($access);
// 	}
	

// }

}

if(!function_exists('createBtnDownloadByClass')){
    function createBtnDownloadByClass($url){
        $btn = '';

        $access = array(
            'excel' => checkBtnAccess($url,'download_excel'), 
            'pdf' => checkBtnAccess($url,'download_pdf')
        );

        $check = array_search(1,$access);
        
        if($check){ 
            $btn .= '
            <div class="btn-group pull-right download" style="display: none">
            <a class="btn green btn-outline" href="javascript:;" data-toggle="dropdown">
            <i class="fa fa-share"></i>
            <span> Download </span>
            <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">';

            if($access['pdf']){
                $btn  .= '
                <li>
                <a href="javascript:;" data-action="2" class="tool-action pdf">
                <i class="icon-doc"></i> PDF
                </a>
                </li>';
            }

            if($access['excel']){
                $btn .= '
                <li>
                <a href="javascript:;" data-action="3" class="tool-action excel">
                <i class="icon-paper-clip"></i> Excel
                </a>
                </li>';
            }
            $btn .= '</ul></div>';
        }

        return $btn;
    }
}
