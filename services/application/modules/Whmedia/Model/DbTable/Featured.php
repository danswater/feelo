<?php

class Whmedia_Model_DbTable_Featured extends Engine_Db_Table
{
  protected $_rowClass = 'Whmedia_Model_Featured';  
  
  public function isFeatured(User_Model_User $user) {
      $row = $this->findRow($user->getIdentity());
      return (!empty($row));
  }
  
  public function unFeatured(User_Model_User $user) {
      $this->delete(array('featured_id = ?' => $user->getIdentity()));
  }
  
  public function Featured(User_Model_User $user) {
      try {
          $this->createRow(array('featured_id' => $user->getIdentity()))->save();
      }
      catch( Exception $e ) {
        if ($e->getCode() != 1062) {
            throw $e;
        }
      }
  }
}