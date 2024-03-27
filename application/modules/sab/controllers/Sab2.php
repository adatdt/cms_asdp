<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Sab2 extends MY_Controller{
  public function __construct(){
    parent::__construct();

    logged_in();
    $this->load->model('sab_model2', 'sab_model');
    $this->load->model('global_model');
  }

  public function index(){
    die('cuuukk');
  }

  public function boarding_passanger($get_ticket_number) {
    $tjancok = TRUE;
    $config_param = get_config_param('reservation');
    $req['ticket_number'] = PHP_AES_Cipher::encrypt2($config_param['aes_key'],$config_param['aes_iv'],$get_ticket_number);
    $req['terminal_code'] = '06001050';
    // $req['terminal_code'] = '04001006';
    if ($tjancok == FALSE) {
      $response = array(
        'code'    => 102,
        'message' => validation_errors(),
        'data'    => null
      );
      die('cuk');
    }
    else {
      $gate   = $this->validate_device($req);
      $config_param = get_config_param('reservation');
      $ticket_number = strtoupper(PHP_AES_Cipher::decrypt2($config_param['aes_key'],$config_param['aes_iv'],$req['ticket_number']));
      $req['ticket'] = $ticket_number;
      valid_aes_qr($ticket_number,$req,'boarding_passanger');
      if(substr($ticket_number, 0,3) == 'MST'){
        die('hadeeeh');
      }
      else{

        $ticket = $this->validate_ticket($req,$ticket_number); 
// die($gate->origin .' = '. $ticket->origin);
// die($gate->destination .' = '. $ticket->destination);
        if($gate->origin == $ticket->origin && $gate->destination == $ticket->destination){
          // if($gate->ship_class == $ticket->ship_class && $gate->service_id == $ticket->service_id){
          if($gate->service_id == $ticket->service_id){
            $now          = strtotime(date('Y-m-d H:i:s'));
            $max_boarding = strtotime($ticket->boarding_expired);
            // jika eksekutif validasi gate in
              if ($ticket->ship_class == 2) {
                  $max_gatein = strtotime($ticket->gatein_expired);
                  if ($now > $max_gatein) {
                      $response = array(
                          'code'    => 0,
                          'message' => 'TIKET EXPIRED',
                          'data'    => null
                        );
                      echo json_encode($response);
                      exit();
                  }
              }
              // end
            if($now > $max_boarding && $ticket->ship_class == 1){
              $response = array(
                'code'    => 0,
                'message' => 'TIKET EXPIRED',
                'data'    => null
              );
            }

            else{
              $this->db->trans_start();

              // $schedule = $this->sab_model->get_schedule($gate);

              $schedule = $this->sab_model->get_schedule($gate,$ticket);
              if($schedule){
                  // $this->move_temp_data_passanger($req,$gate,$schedule);
                  // $this->move_temp_data_vehicle($req,$gate,$schedule);
                  // die($schedule->ship_class .' = '. $ticket->ship_class);
                if($schedule->ship_class == $ticket->ship_class){
                  $data =  array(
                    'ticket_number'      => $ticket->ticket_number
                    , 'created_by'       => $req['terminal_code']
                    , 'boarding_code'    => $schedule->boarding_code
                    , 'schedule_date'    => $schedule->schedule_date
                    , 'ship_class'       => $ticket->ship_class
                    , 'port_id'          => $gate->origin
                    , 'dock_id'          => $gate->dock_id
                    , 'boarding_date'    => date('Y-m-d H:i:s') 
                    , 'terminal_code'    => $req['terminal_code']
                  );
                  $this->sab_model->add($data);
                  $ticket_data =  array(
                    'status'       => 5
                    , 'updated_by' => $req['terminal_code']
                    , 'updated_on' => date('Y-m-d H:i:s')
                  );

                  $this->sab_model->update_status($ticket->ticket_number, $ticket_data);
                  $this->db->trans_complete();
                  if ($this->db->trans_status() == TRUE) {
                    $response = array(
                      'code'    => 1,
                      'message' => 'Sukses',
                      'data'    => $ticket
                    );
                  }
                  else {
                    $response = array(
                      "code" => 200,
                      "message" => 'GAGAL INPUT DATA.',
                      "data" => null,
                    );
                  }
                }
                else{
                  $response = array(
                    'code'    => 0,
                    'message' => 'KELAS KAPAL TIDAK VALID',
                    'data'    => null
                  );
                }
              }
              else{
                $config_param_boarding = get_config_param('boarding');
                if ($config_param_boarding['temporary_passanger'] == 'false') {
                  // jika tidak ada status open boarding
                  $response = array(
                    "code"    => 126,
                    "message" => 'BELUM ADA LAYANAN',
                    "data"    => null,
                  );
                }
                else{
                  $data =  array(
                    'ticket_number'      => $ticket->ticket_number
                    , 'created_by'       => 'system'
                    , 'ship_class'       => $ticket->ship_class
                    , 'port_id'          => $gate->origin
                    , 'dock_id'          => $gate->dock_id
                    , 'boarding_date'    => date('Y-m-d H:i:s') 
                    , 'terminal_code'    => $req['terminal_code']
                  );
                  $this->sab_model->add_temp_data($data);
                  $ticket_data =  array(
                    'status'       => 6
                    , 'updated_by' => 'system'
                    , 'updated_on' => date('Y-m-d H:i:s')
                  );

                  $this->sab_model->update_status($ticket->ticket_number, $ticket_data);
                  $this->db->trans_complete();
                  if ($this->db->trans_status() == TRUE) {
                    $response = array(
                      'code'    => 1,
                      'message' => 'Sukses',
                      'data'    => $ticket
                    );
                  } 
                  else {
                    $response = array(
                      "code" => 200,
                      "message" => 'GAGAL INPUT DATA.',
                      "data" => null,
                    );
                  }
                }
              }
            }
          }
          else{
            $response = array(
              'code'    => 0,
              'message' => 'Invalid ship service',
              'data'    => null
            );
          }
        }
        else{
          $response = array(
            'code'    => 0,
            'message' => 'Invalid rute',
            'data'    => null
          );
        }
      }
    }
    echo json_encode($response);
  }

  public function validate_ticket($req,$ticket_number) {

    $ticket = $this->sab_model->get_by_ticket_number($ticket_number);
    if(!$ticket){
      $response = array(
        'code'    => 131,
        'message' => 'INVALID TIKET',
        'data'    => null
      );
      echo json_encode($response);
      exit();
    }
    if($ticket->service_id == 2){
      $response = array(
        'code'    => 131,
        'message' => 'HANYA BERLAKU UNTUK TIKET PEJALAN KAKI',
        'data'    => null
      );
      echo json_encode($response);
      exit();
    }
    if($ticket->status == 2){
      $response = array(
        'code'    => 107,
        'message' => 'SILAHKAN CHECK IN  ,  TERLEBIH DAHULU',
        'data'    => null
      );
      echo json_encode($response);
      exit();
    }
    else if($ticket->status == 3){
      if($ticket->ship_class == 1){
        $response = array(
          'code'    => 107,
          'message' => 'SILAHKAN GATE IN  ,  TERLEBIH DAHULU',
          'data'    => null
        );
        echo json_encode($response);
        exit();
  
      }
      
    }
  
    else if($ticket->status == 5 || $ticket->status == 6){
      $response = array(
        'code'    => 137,
        'message' => 'TIKET SUDAH BOARDING',
        'data'    => $ticket
      );
      echo json_encode($response);
      exit();
    }
  
    return $ticket;
  }

  public function validate_device($req) {
    $gate   = $this->sab_model->get_terminal_data($req['terminal_code']);
      // print_r($gate);
      // exit();
    if(!$gate){
      $response = array(
        'code'    => 131,
        'message' => 'GATE/PERANGKAT TIDAK VALID',
        'data'    => null
      );
      echo json_encode($response);
      exit();
    }
    return $gate;
  }

}
