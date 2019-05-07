<?php
class db_account_flip{
  public $mysql = null;
  public $conn = null;
  private $i18n = null;
  function __construct($data = array()){
    $data = new PluginWfArray($data);
    $this->conn = $data->get('conn');
    wfPlugin::includeonce('wf/mysql');
    $this->mysql = new PluginWfMysql();
    wfPlugin::includeonce('i18n/translate_v1');
    $this->i18n = new PluginI18nTranslate_v1();
  }
  public function db_open(){
    $this->mysql->open($this->conn);
  }
  public function sql_get($key){
    $sql = new PluginWfYml(__DIR__.'/sql.yml', $key);
    $replace = new PluginWfYml(__DIR__.'/sql.yml', 'replace');
    /**
     * Replace sql.
     */
    if($replace->get()){
      foreach ($replace->get() as $key => $value) {
        if(!is_array($value)){
          $temp = $sql->get('sql');
          $temp = str_replace('['.$key.']', $value, $temp);
          $sql->set('sql', $temp);
        }
      }
    }
    /**
     * Replace select.
     */
    if($replace->get()){
      if($sql->get('select') && !is_array($sql->get('select'))){
        $sql->set('select',    $replace->get($sql->get('select')) );
      }
    }
    return $sql;
  }
  /**
   */
  public function account_flip_select_by_id(){
    $this->db_open();
    $sql = $this->sql_get('account_flip_select_by_id');
    $this->mysql->execute($sql->get());
    return $this->mysql->getOne();
  }
  public function account_flip_insert_by_id(){
    $id = wfCrypt::getUid();
    $this->db_open();
    $sql = $this->sql_get('account_flip_insert_by_id');
    $sql->setByTag(array('flip_key' => $id));
    $this->mysql->execute($sql->get());
    return $id;
  }
  public function account_flip_insert($data){
    $this->db_open();
    $sql = $this->sql_get('account_flip_insert');
    $sql->setByTag($data);
    $this->mysql->execute($sql->get());
    return null;
  }
  public function account_flip_delete($data){
    $this->db_open();
    $sql = $this->sql_get('account_flip_delete');
    $sql->setByTag($data);
    $this->mysql->execute($sql->get());
    return null;
  }
  public function account_flip_delete_by_id($data){
    $this->db_open();
    $sql = $this->sql_get('account_flip_delete_by_id');
    $sql->setByTag($data);
    $this->mysql->execute($sql->get());
    return null;
  }
  public function account_flip_select_by_flip_key($data){
    $this->db_open();
    $sql = $this->sql_get('account_flip_select_by_flip_key');
    $sql->setByTag($data);
    $this->mysql->execute($sql->get());
    return $this->mysql->getMany();
  }
  private function getSelect($sql){
    $rs = array();
    foreach ($sql->get('select') as $key => $value) {
      $rs[$value] = null;
    }
    return new PluginWfArray($rs);
  }
  /**
   * 
   */
  private function random_string($n = 10) { 
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $randomString = ''; 
    for ($i = 0; $i < $n; $i++) { 
      $index = rand(0, strlen($characters) - 1); 
      $randomString .= $characters[$index]; 
    } 
    return $randomString; 
  }
}
