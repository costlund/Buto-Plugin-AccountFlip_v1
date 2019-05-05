<?php
class PluginAccountFlip_v1{
  public $db = null;
  function __construct() {
    require_once __DIR__.'/mysql/db.php';
    $plugin_settings = wfPlugin::getPluginSettings('account/flip_v1', true);
    wfPlugin::includeonce('wf/yml');
    wfPlugin::includeonce('i18n/translate_v1');
    $i18n = new PluginI18nTranslate_v1();
    $i18n->set_path('/plugin/account/flip_v1/i18n');
    $this->db = new db_account_flip(array(
      'conn' => $plugin_settings->get('settings/mysql')
            ));
  }
  private function getData(){
    $flip = $this->db->account_flip_select_by_id();
    if(is_null($flip->get())){
      $flip_key = $this->db->account_flip_insert_by_id();
      $flip = $this->db->account_flip_select_by_id();
    }
    $data = new PluginWfArray();
    $data->set('current', $flip->get());
    $data->set('accounts', $this->db->account_flip_select_by_flip_key($flip->get()));
    return $data;
  }
  public function page_view(){
    $data = $this->getData();
    $accounts = array();
    foreach ($data->get('accounts') as $key => $value) {
      if($value['account_id']!=$data->get('current/account_id')){
        $account = new PluginWfYml(__DIR__.'/element/account.yml');
      }else{
        $account = new PluginWfYml(__DIR__.'/element/account_current.yml');
      }
      $account->setByTag($value);
      $accounts[] = $account->get();
    }
    $element = new PluginWfYml(__DIR__.'/element/view.yml');
    $element->setByTag($data->get('current'), 'flip');
    $element->setByTag(array('accounts' => $accounts), 'elements');
    wfDocument::renderElement($element->get());
  }
  public function page_signin(){
    $data = $this->getData();
    $json = new PluginWfArray();
    $json->set('success', true);
    foreach ($data->get('accounts') as $key => $value) {
      $i = new PluginWfArray($value);
      if($i->get('username')==wfRequest::get('u')){
        $json->set('account', $value);
      }
    }
    if($json->get('account')){
      wfPlugin::includeonce('wf/account2');
      $obj = new PluginWfAccount2();
      $obj->sign_in_external($json->get('account/account_id'));
    }else{
      $json->set('success', false);
    }
    exit(json_encode($json->get()));
  }
  public function page_add(){
    $form = new PluginWfYml(__DIR__.'/form/add.yml');
    $widget = wfDocument::createWidget('form/form_v1', 'render', $form->get());
    wfDocument::renderElement(array($widget));
  }
  public function page_capture(){
    $form = new PluginWfYml(__DIR__.'/form/add.yml');
    $widget = wfDocument::createWidget('form/form_v1', 'capture', $form->get());
    wfDocument::renderElement(array($widget));
  }
  public function capture($form){
    wfPlugin::includeonce('wf/account2');
    $obj = new PluginWfAccount2();
    $user = $obj->verify_account(array('username' => wfRequest::get('username'), 'password' => wfRequest::get('password')));
    if($user){
      $data = $this->getData();
      $exist = false;
      foreach ($data->get('accounts') as $key => $value) {
        $i = new PluginWfArray($value);
        if($i->get('account_id')==$user->get('id')){
          $exist = true;
          break;
        }
      }
      if($data->get('current/account_id')==$user->get('id')){
        $exist = true;
      }
      if($exist){
        return array("alert('Account already exist!')");
      }else{
        $this->db->account_flip_insert(array('account_id' => $user->get('id'), 'flip_key' => $data->get('current/flip_key')));
        return array("$('#modal_account_flip_add').modal('hide');PluginWfAjax.update('modal_account_flip_body');");
      }
    }else{
      return array("alert('Could not find account!')");
    }
  }
  public function page_delete(){
    $data = $this->getData();
    $json = new PluginWfArray();
    $json->set('success', true);
    foreach ($data->get('accounts') as $key => $value) {
      $i = new PluginWfArray($value);
      if($i->get('username')==wfRequest::get('u')){
        $json->set('account', $value);
      }
    }
    if($json->get('account')){
      $this->db->account_flip_delete($json->get('account'));
    }else{
      $json->set('success', false);
    }
    exit(json_encode($json->get()));
  }
}
