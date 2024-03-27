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

class NewsMasterListModel extends MY_Model{

	public function __construct() {
		parent::__construct();
        $this->_module = 'news/newsMasterList';

        $this->_url_api = $this->config->item('url_push_notif');
		$this->_user_api = $this->config->item('user_push_notif');
		$this->_pass_api = $this->config->item('pass_push_notif');
	}

    public function getDataList(){
		
		$sendData=array();
		$url="master_notification/transaction/list_notification";

		// print_r($sendData); exit;
		$getList=$this->getData($url,$sendData);
		
		$rows=array();
		$getDataType=$this->getDataType();
		foreach ($getList->data as $key => $value) {
			
			$value->no="";
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

			$value->actions ="";
			$buttonEdit = '<button onclick="showModal(\''.$url.'\')" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-pencil"></i></button> ';

			if($value->status==1)
			{
				$value->actions  .= generate_button($this->_module, 'edit', '<button onclick="showModalNew2(\''.$edit_url.'\')" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-pencil"></i></button> ');				
				$value->status   = success_label('Aktif');
				$value->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-danger" onclick="confirmationAction(\'Apakah Anda yakin akan menonaktifkan data ini ?\', \''.$nonaktif.'\')" title="Nonaktifkan"> <i class="fa fa-ban"></i> </button> ');
			}
			else
			{
				$value->status   = failed_label('Tidak Aktif');
				$value->actions .= generate_button($this->_module, 'delete', '<button class="btn btn-sm btn-primary" onclick="confirmationAction(\'Apakah Anda yakin mengaktifkan data ini ?\', \''.$aktif.'\')" title="Aktifkan"> <i class="fa fa-check"></i> </button> ');
			}

			$value->type_name = $getDataType[$value->type];
			$value->actions .= generate_button_new($this->_module, 'delete', $delete_url);
			$value->created_on=format_date($value->created_on)." ".format_time($value->created_on);


			$rows[]=$value;
			
		}
				
		return array(
			'data'           => $rows,
			'status'		 =>$getList->status,
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

	public function getData($url, $data)
    {		
        $dataHeader = array(
            "cache-control: no-cache",
            "Content-Type:application/json; charset=utf-8"
        );
		        
        // auth basic
        // $password="4asddfghmjkl1zxcnvbnAqwe5rtyDuiopPm";
        // $userName="admin";

        $password=$this->_pass_api;
        $userName=$this->_user_api;

        // $this->_url_api = $this->config->item('url_push_notif');
		// $this->_user_api = $this->config->item('user_push_notif');
		// $this->_pass_api = $this->config->item('pass_push_notif');
        
		// url api 40
		// $urlApi="https://asdp-notification.devops-nutech.com/"; 
        $urlApi=$this->_url_api;

		// // url api 71
		// $urlApi= 'https://asdp-api-message.nutech-integrasi.app/;

		$arraySetting =array(
            CURLOPT_URL => $urlApi.$url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => 1,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_USERPWD => $userName . ':' . $password,            
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
	
    public function getDataType(){

        $data["1"] = "tagihan pemesanan";
        $data["2"] = "waktu pembayaran akan segera berakhir";
        $data["3"] = "pembayaran berhasil";
        $data["4"] = "waktu mulai checkin";
        $data["5"] = "tiket segera expired";
        $data["6"] = "tiket anda expired";

        return $data;
    }
	
	
	
}
