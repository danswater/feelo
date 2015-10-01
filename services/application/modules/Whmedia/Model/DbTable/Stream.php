<?php

class Whmedia_Model_DbTable_Stream extends Engine_Db_Table
{
  protected $_rowClass = 'Whmedia_Model_Stream';
  
  public function addStream(Whmedia_Model_Project $project, User_Model_User $user = null) {
      if (empty($user)) {
          $user = $project->getOwner();
      }
      $this->createRow(array('user_id' => $user->getIdentity(),
                             'project_id' => $project->getIdentity() ))->save();
  }
  
  public function selectStreamProjects(User_Model_User $user) {
      $user_id = $user->getIdentity();
      $select = new Zend_Db_Table_Select(Engine_Api::_()->getItemTable('whmedia_project'));

      $select->from(new Zend_Db_Expr("
        (SELECT DISTINCT followers.* FROM(
        SELECT `project_id`, `creation_date` FROM 
          ( SELECT * FROM `engine4_whmedia_stream` WHERE user_id = $user_id OR user_id IN 
            (SELECT `user_id` FROM `engine4_whmedia_follow` WHERE `follower_id` = $user_id) 
          ORDER BY `creation_date` DESC) 
        AS tmp_stream GROUP BY tmp_stream.`project_id`
        UNION
        SELECT `tm`.`resource_id` as `project_id`, `fh`.`creation_date` as `creation_date` FROM engine4_whmedia_followhashtag AS fh
          JOIN engine4_core_tags AS t ON fh.hashtag_id=t.tag_id
          JOIN engine4_core_tagmaps AS tm ON tm.tag_id=t.tag_id 
          WHERE follower_id=$user_id ORDER BY creation_date DESC
        ) AS followers)
       AS tmp_stream_projects, `engine4_whmedia_projects`
      ") )
      ->setIntegrityCheck(FALSE)  
      ->where(new Zend_Db_Expr('t.`project_id` = `tmp_stream_projects`.`project_id`'));

      /*
      $select->from(new Zend_Db_Expr("(SELECT `project_id`, `creation_date` 
                                                FROM ( SELECT * FROM `engine4_whmedia_stream`
                                                        WHERE user_id = $user_id OR user_id IN (SELECT `user_id` FROM `engine4_whmedia_follow` WHERE `follower_id` = $user_id) 
                                                        ORDER BY `creation_date` DESC) AS tmp_stream
                                                GROUP BY tmp_stream.`project_id`) AS tmp_stream_projects, 
                                                `engine4_whmedia_projects` ") )
             ->setIntegrityCheck(FALSE)  
             ->where(new Zend_Db_Expr('t.`project_id` = `tmp_stream_projects`.`project_id`'));
      */
      return $select;      
  }
}