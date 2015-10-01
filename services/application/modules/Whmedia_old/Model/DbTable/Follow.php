<?php

class Whmedia_Model_DbTable_Follow extends Engine_Db_Table
{
  protected $_rowClass = 'Whmedia_Model_Follow';
  
  public function isFollow(User_Model_User $target, User_Model_User $user) {
      $row = $this->fetchRow(array('user_id = ?' => $target->getIdentity(),
                                   'follower_id = ?' => $user->getIdentity()));
      return (!empty($row));
  }
  
  public function unFollow(User_Model_User $target, User_Model_User $user) {
      $this->delete(array('user_id = ?' => $target->getIdentity(),
                          'follower_id = ?' => $user->getIdentity()));
      $this->getDefaultAdapter()->query("DELETE FROM `engine4_whmedia_circleitems` USING `engine4_whmedia_circleitems`, (
                                        SELECT `circleitem_id` FROM `engine4_whmedia_circleitems`
                                        LEFT JOIN `engine4_whmedia_circles` ON `engine4_whmedia_circles`.`circle_id` = `engine4_whmedia_circleitems`.`circle_id`
                                        WHERE `engine4_whmedia_circles`.`user_id` = ? AND `engine4_whmedia_circleitems`.`user_id` = ?) AS `circleitems`
                                        WHERE `circleitems`.`circleitem_id` = `engine4_whmedia_circleitems`.`circleitem_id`", array($user->getIdentity(), $target->getIdentity()));
      
  }
  
  public function Follow(User_Model_User $target, User_Model_User $user) {
      try {
          $this->createRow(array('user_id' => $target->getIdentity(),
                                 'follower_id' => $user->getIdentity() ))->save();
      }
      catch( Exception $e ) {
        if ($e->getCode() != 1062) {
            throw $e;
        }
      }
  }
  
  public function getFollowersCount(User_Model_User $target) {
      return $this->getFollowers($target)->count();
  }
  
  public function getFollowingCount(User_Model_User $target) {
      return $this->getFollowing($target)->count();
  }
  
  public function getFollowers(User_Model_User $target) {
      return $this->fetchAll(array('user_id = ?' => $target->getIdentity()));
  }
  
  public function getFollowing(User_Model_User $target) {
      return $this->fetchAll(array('follower_id = ?' => $target->getIdentity()));
  }
}