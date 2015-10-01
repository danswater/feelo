<?php

class Whmedia_Model_DbTable_Favcircleitems extends Engine_Db_Table
{
  protected $_rowClass = 'Whmedia_Model_Favcircleitem';    


  public function favcircleProjects(User_Model_User $user, $favcircle_id) {
      $user_id = $user->getIdentity();
      $select = new Zend_Db_Table_Select(Engine_Api::_()->getItemTable('whmedia_project'));
      /*
      $select->from(new Zend_Db_Expr("
        (SELECT DISTINCT followers.* FROM( SELECT project_id FROM engine4_whmedia_favcircleitems WHERE user_id = $user_id AND favcircle_id = $favcircle_id) AS followers )
       AS tmp_stream_projects, `engine4_whmedia_projects`
      ") ) */
     $select->from(new Zend_Db_Expr("
        (SELECT DISTINCT followers.* FROM( SELECT project_id FROM engine4_whmedia_favcircleitems WHERE favcircle_id = $favcircle_id) AS followers )
       AS tmp_stream_projects, `engine4_whmedia_projects`
      ") )
      ->setIntegrityCheck(FALSE)  
      ->where(new Zend_Db_Expr('t.`project_id` = `tmp_stream_projects`.`project_id`'));
      return $select;      
  }
}