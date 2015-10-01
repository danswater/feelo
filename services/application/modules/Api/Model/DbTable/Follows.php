<?php

class Api_Model_DbTable_Follows extends Engine_Db_Table
{
  protected $_rowClass = 'Whmedia_Model_Follow';
  protected $_name     = 'whmedia_follow';
  
  public function isFollow(User_Model_User $target, User_Model_User $user) {
      $row = $this->fetchRow(array(
    'user_id = ?' => $user->getIdentity(),
        'follower_id = ?' => $target->getIdentity(),
    'pending_approval = ?' => 0  
    ));
      return (!empty($row));
  }
  
  public function isForPendingApproval ( $viewer, $subject ) {
  $row = $this->fetchRow( array(
    'user_id = ?' => $subject->getIdentity(),
    'follower_id = ?' => $viewer->getIdentity(),
    'pending_approval = ?' => 1
  ) );

  return (!empty( $row ) );
  
  }
  
  public function unFollow(User_Model_User $target, User_Model_User $user) {
      $this->delete(array('user_id = ?' => $user->getIdentity(),
                          'follower_id = ?' => $target->getIdentity()));
      $this->getDefaultAdapter()->query("DELETE FROM `engine4_whmedia_circleitems` USING `engine4_whmedia_circleitems`, (
                                        SELECT `circleitem_id` FROM `engine4_whmedia_circleitems`
                                        LEFT JOIN `engine4_whmedia_circles` ON `engine4_whmedia_circles`.`circle_id` = `engine4_whmedia_circleitems`.`circle_id`
                                        WHERE `engine4_whmedia_circles`.`user_id` = ? AND `engine4_whmedia_circleitems`.`user_id` = ?) AS `circleitems`
                                        WHERE `circleitems`.`circleitem_id` = `engine4_whmedia_circleitems`.`circleitem_id`", array($user->getIdentity(), $target->getIdentity()));
      
  }
  
  public function Follow(User_Model_User $target, User_Model_User $user, $status ) {

    try {
         $row =  $this->createRow( array(
          'user_id' => $user->getIdentity(),
          'follower_id' => $target->getIdentity(),
          'pending_approval' => $status
        ) )->save();
      }
      catch( Exception $e ) {
        if ($e->getCode() != 1062) {
            throw $e;
        }
      }
    
    return $row;
  }
  
  public function getFollowersCount(User_Model_User $target) {
      return $this->getFollowers($target)->count();
  }
  
  public function getFollowingCount(User_Model_User $target) {
      return $this->getFollowing($target)->count();
  }
  
  public function getFollowers(User_Model_User $target) {
      return $this->fetchFollowers($target->getIdentity());
  }
  
  public function getFollowing(User_Model_User $target) {
      return $this->fetchFollowing($target->getIdentity());
  }

  public function fetchFollowerPaginator( $user_id ) {
  
    $select = $this->select()
    ->where('user_id = ?', $user_id);
    
    return Zend_Paginator::factory($select);
  }
  
  public function fetchFollowers($user_id){
    return $this->fetchAll(array('user_id = ?' => $user_id));
  }

  public function fetchFollowing($follower_id){
     return $this->fetchAll(array('follower_id = ?' => $follower_id));
  }
  
  public function fetchFollowByUserAndFollow ( $target, $user ) {
      $row = $this->fetchRow(array('user_id = ?' => $target->getIdentity(),
                                   'follower_id = ?' => $user->getIdentity()));
  }
}