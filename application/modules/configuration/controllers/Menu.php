<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/**
 * -----------------
 * CLASS NAME : Menu
 * -----------------
 *
 * @author     Robai <robai.rastim@gmail.com>
 * @copyright  2018
 *
 */

class Menu extends MY_Controller {

	public function __construct() {
		parent::__construct();

    logged_in();

    $this->load->model('menu_model');
    $this->_table    = 'core.t_mtr_menu_web';
    $this->_username = $this->session->userdata('username');
    $this->_module   = 'configuration/menu';
  }

  function index() {
    checkUrlAccess($this->_module,'view');
    $data = array(
      'home'      => 'Home',
      'url_home'  => site_url('home'),
      'parent'    => 'Konfigurasi Sistem',
      'url_parent'=> '#',
      'title'     => 'Menu',
      'content'   => 'menu/index',
      'btn_add'   => generate_button_new($this->_module, 'add',  site_url($this->_module.'/add'))
    );

    $this->load->view ( 'default', $data );
  }

  function add(){   
    validate_ajax();
    $data['title']   = 'Tambah Menu';
    $data['icon']    = $this->dropdown_icon();
    $data['actions'] = $this->dropdown_action();
    $this->load->view($this->_module.'/add',$data);
  }

  function edit($param){   
    validate_ajax();
    $id = $this->enc->decode($param);
    $where       = array('menu_id' => $id);
    $menu_detail = $this->global_model->selectData('core.t_mtr_menu_detail_web', $where);

    $arr_detail = array();
    foreach ($menu_detail as $val) {
      $arr_detail[] = $val->action_id;
    }
    
    $data['title'] = 'Edit Menu';
    $data['id']    = $param;
    $data['icon']    = $this->dropdown_icon();
    $data['actions'] = $this->dropdown_action();
    $data['row']   = $this->global_model->selectById($this->_table, 'id', $id);
    $data['menuaction']= json_encode($arr_detail);
    
    $this->load->view($this->_module.'/edit',$data);
  }

  function action_add(){
    validate_ajax();
    /* validation */
    $this->form_validation->set_rules('name', 'nama menu', 'trim|required|max_length[50]|callback_special_char', array('special_char' => 'name memuat invalid karakter'));
    $this->form_validation->set_rules('menuAction', 'Aksi Menu', 'trim|required|numeric');
    $this->form_validation->set_rules('ordering', 'ordering', 'trim|required|numeric');
    $this->form_validation->set_rules('parent', 'parent', 'trim|numeric');
    $this->form_validation->set_rules('actions[]', 'action', 'trim|required');
    $this->form_validation->set_rules('icon', 'icon', 'trim|max_length[50]|callback_special_char', array('special_char' => 'icon memuat invalid karakter'));
    $this->form_validation->set_rules('link', 'link', 'trim|max_length[50]|callback_special_char_url', array('special_char_url' => 'link memuat invalid karakter'));

    $this->form_validation->set_message('required', '%s harus diisi!');
    /* data post */
    if ($this->form_validation->run() == FALSE) {
        echo $res = json_api(0, validation_errors());
        exit;
    } 
    foreach($this->input->post("actions") as $key => $value){
        $this->form_validation->set_rules("actions[" . $key . "]", 'actions', 'required|numeric');
    }

    if ($this->form_validation->run() == FALSE) {
        echo $res = json_api(0, validation_errors());
        exit;
    } 
    
    $post = $this->input->post();
    

    $parent = $post['parent'] == '' ? 0 : $post['parent'];
    $link   = $post['link'] == '' ? '#' : $post['link'];

    /* data post */
    $data = array(
      'name' => $post['name'], 
      'icon' => $post['icon'],
      'order' => $post['ordering'],
      'slug' => $link,
      'parent_id' => $parent,
    );

    
    /* check url */
    $checkMenuUrl = false;
    if($post['link'] != '#'){
      $where = array(
        'LOWER(slug)' => strtolower($post['link']),
      );

      $checkMenuUrl = $this->global_model->checkData($this->_table, $where);        
    }

    $checkOrder = $this->menu_model->checkOrder($parent,$post['ordering']);

    if($checkMenuUrl){
      $response = json_api(0,'Link '.$post['link'].' Sudah digunakan'); 
    }elseif($checkOrder){
      $response = json_api(0,'Order '.$post['ordering'].' Sudah digunakan'); 
    }else{
      $save = $this->global_model->saveData($this->_table, $data);
      if($save){
        $menu_detail = array();
        foreach ($post['actions'] as $val) {
          $menu_detail[] = array(
            'menu_id' => (int) $save, 
            'action_id' => $val, 
            'created_by' => $this->session->userdata('id'),
            'created_on' => date('Y-m-d H:i:s'),
          );
        }
        $this->db->insert_batch('core.t_mtr_menu_detail_web', $menu_detail);
        $response = json_api(1,'Simpan Data Berhasil');
      }else{
        $response = json_encode($this->db->error()); 
      }
    }   
  

    $this->log_activitytxt->createLog($this->_username, uri_string(), 'ADD', json_encode($data), $response); 
    echo $response;   
  }

  function action_edit(){
    validate_ajax();
    $this->form_validation->set_rules('id', 'id', 'trim|required|callback_validate_decode_param', array('validate_decode_param' => 'Invalid id'));
    $this->form_validation->set_rules('name', 'nama menu', 'trim|required|max_length[50]|callback_special_char', array('special_char' => 'name memuat invalid karakter'));
    $this->form_validation->set_rules('menuAction', 'Aksi Menu', 'trim|required|numeric');
    $this->form_validation->set_rules('ordering', 'ordering', 'trim|required|numeric');
    $this->form_validation->set_rules('parent', 'parent', 'trim|numeric');
    $this->form_validation->set_rules('actions[]', 'action', 'trim|required');
    $this->form_validation->set_rules('icon', 'icon', 'trim|max_length[50]|callback_special_char', array('special_char' => 'icon memuat invalid karakter'));
    $this->form_validation->set_rules('link', 'link', 'trim|max_length[50]|callback_special_char_url', array('special_char_url' => 'link memuat invalid karakter'));

    $this->form_validation->set_message('required', '%s harus diisi!');
    /* data post */
    if ($this->form_validation->run() == FALSE) {
        echo $res = json_api(0, validation_errors());
        exit;
    } 
    foreach($this->input->post("actions") as $key => $value){
        $this->form_validation->set_rules("actions[" . $key . "]", 'actions', 'required|numeric');
    }

    if ($this->form_validation->run() == FALSE) {
        echo $res = json_api(0, validation_errors());
        exit;
    } 

    

    $post = $this->input->post();


    $parent = $post['parent'] == '' ? 0 : $post['parent'];
    $link   = $post['link'] == '' ? '#' : $post['link'];
    $id     = $this->enc->decode($post['id']);

    /* data post */
    $data = array(
      'id' => $id, 
      'name' => $post['name'], 
      'icon' => $post['icon'],
      'order' => $post['ordering'],
      'slug' => $link,
      'parent_id' => $parent,
    );

   
      /* check url */
      $checkMenuUrl = false;
      if($post['link'] != '#' )
      {
        $where = array(
          'LOWER(slug)' => strtolower($post['link']),
        );

        $checkMenuUrl = $this->global_model->checkData($this->_table, $where, 'id', $id);        
      }

      $checkOrder = $this->menu_model->checkOrder($parent,$post['ordering'],$id);
      if($checkMenuUrl)
      {
        $response = json_api(0,'Link '.$post['link'].' Sudah digunakan'); 
      }
      elseif($checkOrder)
      {
        $response = json_api(0,'Order '.$post['ordering'].' Sudah digunakan'); 
      }
      else
      {
        $update = $this->global_model->updateData($this->_table, $data, 'id');
        if($update)
        {
          $list_detail= $this->menu_model->select_menu_detail($id);

          $arr_diff   = array_diff($list_detail, $post['actions']);
          if($arr_diff){
            $detail_id = array();

            foreach ($arr_diff as $key => $val) {
              $detail_id[] = $key;
            }
            $this->global_model->deleteDataMultiple('core.t_mtr_menu_detail_web','id',$detail_id);
            if($this->menu_model->select_menu_privilege($id,$detail_id))
            {
              $this->menu_model->deletePrivilege($id,$detail_id);
            }
          }

          $arr_diff2 = array_diff($post['actions'],$list_detail);

          if($arr_diff2){
            $menu_detail = array();
            foreach ($arr_diff2 as $val) {
              $menu_detail[] = array(
                'menu_id' => $id, 
                'action_id' => $val, 
                'created_by' => $this->session->userdata('id'),
                'created_on' => date('Y-m-d H:i:s'),
              );
            }
            $this->db->insert_batch('core.t_mtr_menu_detail_web', $menu_detail);
          }
          $response = json_api(1,'Update Data Berhasil');
        }else{
          $response = json_encode($this->db->error()); 
        }
      }  
    

    $this->log_activitytxt->createLog($this->_username, uri_string(), 'EDIT', json_encode($data), $response); 
    echo $response;   
  }

  function action_delete($param){
    validate_ajax();
    $id = $this->enc->decode($param);

    /* data */
    $data = array('id' => $id,'status' => -5);

    $query = $this->global_model->updateData($this->_table, $data, 'id');

    if($query)
    {
      $checkParent = $this->global_model->checkData($this->_table, array('parent_id' => $id));

      if($checkParent)
      {
        $data_parent = array('parent' => $id,'status' => -5);
        $this->global_model->updateData($this->_table, $data_parent, 'parent_id');
      }
      
      $response = json_api(1,'Delete Data Berhasil');
    }
    else
    {
      $response = json_encode($this->db->error()); 
    }

    $this->log_activitytxt->createLog($this->_username, uri_string(), 'delete', json_encode($data), $response); 
    echo $response;
  }

  function get_list(){
    validate_ajax();
    $rows = $this->menu_model->get_list();
    $rows["tokenHash"] = $this->security->get_csrf_hash();
    $rows["csrfName"] = $this->security->get_csrf_token_name();
    echo json_encode($rows);
  }

  private function dropdown_action(){
    $datas = $this->global_model->selectAll('core.t_mtr_menu_action');
    $data = array();
    foreach($datas as $row){
      $data[$row->id] = $row->action_name;
    }
    return $data;
  }

  private function dropdown_icon(){
    $datas  = $this->global_model->selectAll('core.t_mtr_icon');
    $data   = array();
    if($datas){
      foreach($datas as $row){
        if (substr(trim($row->name), 1, 3) == 'svg') {
          $data[$row->name] = htmlspecialchars($row->name);
        } else {   
          $data[$row->name] = $row->name;
        }
      }
    }
    
    return $data;
  }
}
