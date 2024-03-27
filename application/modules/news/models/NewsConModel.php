<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * -----------------------
 * CLASS NAME : NewsModel
 * -----------------------
 *
 * @author     Adat <adatdt@gmail.com>
 * @copyright  2022
 *
 */

class NewsConModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'news/newsCon';

		$this->_url_api = $this->config->item('url_push_notif');
		$this->_user_api = $this->config->item('user_push_notif');
		$this->_pass_api = $this->config->item('pass_push_notif');
	}

    public function getDataList(){

		
		$dateFrom = $this->input->post("dateFrom");
		$dateTo = $this->input->post("dateTo");
		$startPublish = $this->input->post("startPublish");


		$sendData['start_created_on']=$dateFrom;
		$sendData['end_created_on']=$dateTo;
		$sendData['type']=3;

		if(!empty($startPublish))
		{
			$sendData['start_published']=$startPublish;
		}

		$url="master_notification/list_notification";

		$getList=$this->postData($url,$sendData);
		// print_r($getList);exit;
		$rows=array();
		foreach ($getList->data as $key => $value) {
			
			$value->no="";
			$value->title='<div style = "width:500px; word-wrap: break-word">'.$value->title.'</div>';
			$value->sub_title='<div style = "width:700px; word-wrap: break-word">'.$value->sub_title.'</div>';
			// $last_edited=" nama edit - ".$value->updated_on;

			$id_enc=$this->enc->encode($value->id);
			$nonaktif    = site_url($this->_module."/action_change/".$this->enc->encode($value->id.'|0|'.$value->type));
     		$aktif       = site_url($this->_module."/action_change/".$this->enc->encode($value->id.'|1|'.$value->type));
			
			$edit_url 	 = site_url($this->_module."/edit/".$this->enc->encode($value->id."|".$value->type));
     		$delete_url  = site_url($this->_module."/action_delete/".$this->enc->encode($value->id."|".$value->type));			

			$value->last_edited="";
			if(!empty($value->updated_by))
			{
				$value->last_edited=$value->updated_by."-".format_date($value->updated_on)." ".format_time($value->updated_on);
			}
			
			// $value->dataVideo="";
			// if(!empty($value->video))
			// {
			// 	$value->dataVideo=$value->video;
			// }
			$dateNow= date('Y-m-d H:i');	

			$value->actions  =" ";

			// $buttonEdit = '<button onclick="showModal(\''.$url.'\')" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-pencil"></i></button> ';
			
			if($value->end_published < $dateNow && $value->status==1){
				$value->actions  .= generate_button($this->_module, 'delete', '<button onclick="showModalNew2(\''.$edit_url.'\')" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-pencil"></i></button> ');				
				$value->status   = warning_label('Expired');
				$value->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else if($value->status==1)
			{
				$value->actions  .= generate_button($this->_module, 'delete', '<button onclick="showModalNew2(\''.$edit_url.'\')" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-pencil"></i></button> ');				
				$value->status   = success_label('Aktif');
				$value->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$value->status   = failed_label('Tidak Aktif');
				$value->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

			$value->actions .= generate_button_new($this->_module, 'delete', $delete_url);

			// $value->created_on=format_date($value->created_on)." ".format_time($value->created_on);
			$value->image ="<div style ='width:600px; word-wrap: break-word'><a onclick=myData.showModal('".$value->image->detail."') >".$value->image->detail."</a></div>";

			$value->priode = format_date($value->start_published)." ".format_time($value->start_published)." s/d ".format_date($value->end_published)." ".format_time($value->end_published);

			switch ($value->type) {
				case '1':
					$value->typeData="Info";
					break;
				case '2':
					$value->typeData="Promo";
					break;								
				default:
					$value->typeData="Berita";
					break;
			}

			$value->video ="<div style ='width:400px; word-wrap: break-word'><a onclick=myData.showModalVideo('".$value->video."') ".$value->video."</a></div>";

			$contenData= "";
			$br="";
			$value->link_image ="";

			if (!empty($value->content_images)){
				$content=$value->content_images;
				
					foreach($content as $k =>$v ){
						// $no = $k +1 ; //odering
						if($br != $content[$k] ){
							$breakLine ="<br>";
						}else{
							$breakLine ="";
						}

						$contenData .="<div style ='width:700px; word-wrap: break-word'><a onclick=myData.showModal('".$content[$k]."') >".$content[$k]."</a></div>";
						
						$value->link_image = $contenData ;
					}

			}else{
				$value->link_image ="";
			}
			
			$rows[]=$value;
			
		}
		return array(
			'data'          => $rows,
			'status'		=>$getList->status,
			'message'		=>$getList->message

		);
	}
	public function postData($url, $data)
    {		
		
        $dataHeader = array(
            "cache-control: no-cache",
            "Content-Type:application/json; charset=utf-8"
        );

		$arraySetting =array(
            CURLOPT_URL => $this->_url_api.$url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => 1,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_USERPWD => $this->_user_api . ':' . $this->_pass_api,            
            CURLOPT_HTTPHEADER => $dataHeader
        );
		// print_r($arraySetting); exit;

		$curl = curl_init();        
        curl_setopt_array($curl,$arraySetting);

        $response = curl_exec($curl);
        $err = curl_error($curl);	


        if ($response === false) {

            return json_decode($err);
        } else {			
            return json_decode($response); 

        }
		
    }
	
	public function select_data($table, $where="")
	{
		return $this->db->query(" select * from {$table}  {$where} ");
	}
	
	
}
