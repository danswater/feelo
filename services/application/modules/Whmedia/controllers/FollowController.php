<?php
class Whmedia_FollowController extends Core_Controller_Action_User {

    protected $_subject;


    public function init() {
        $this->_helper->layout->disableLayout(true);
        if( !$this->getRequest()->isPost() ) {
          return $this->_helper->Message('Invalid request method', false, false)->setAjax()->setError();      
        }
        $user_id = (int)$this->_getParam('user_id');
        if (empty($user_id)) {
            return $this->_helper->Message('Invalid user id', false, false)->setAjax()->setError();    
        }
        $this->_subject = Engine_Api::_()->user()->getUser($user_id);       
        if (!$this->_subject->getIdentity()) {
            return $this->_helper->Message('Invalid user id', false, false)->setAjax()->setError(); 
        }
    }

    public function toggleFollowAction() {
        
        $viewer = Engine_Api::_()->user()->getViewer();     

        //$is_req = (int)$this->_getParam('is_req');
		
		$is_req = 0;
		if( !$this->_subject->authorization()->isAllowed( $viewer, 'view' ) ){
			$is_req = 1;
		}
		
        if ($this->_subject->getIdentity() == $viewer->getIdentity()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("You can't follow yourself.");
            return;
        }
        
        $followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
        
        // Process
        $db = $followApi->getAdapter();
        $db->beginTransaction();

        try {
          $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
            ->getNotificationBySubjectAndType($this->_subject, $viewer, 'friend_follow_request');

          if ($followApi->isFollow($this->_subject, $viewer) || $notification) {
              $followApi->unFollow($this->_subject, $viewer);
              $isFollow = false;

              if( $is_req == 1 && $notification){
                $notification->delete();
              }
          }
          else {            
            $isFollow = true;
            if( $is_req == 1){
              Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($this->_subject, $viewer, $this->_subject, 'friend_follow_request');
            }else{
              $followApi->Follow($this->_subject, $viewer);
              Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($this->_subject, $viewer, $this->_subject, 'whmedia_following');
            }
          }
          
          $db->commit();
        }
        catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }
        return $this->view->assign(array('status' => true,
		                                 'isfollow' => $isFollow,
										 'is_req' => $is_req,
                                         'count_following' => $followApi->getFollowersCount($this->_subject),
                                         'user_subject' => $this->_subject->getIdentity()));
  }  
  
    public function toggleFeaturedAction() {
        
        
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->isAdmin()) {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_('Only admin can select featured members');
          return;
        }
                     
        $Api = Engine_Api::_()->getDbtable('featured', 'whmedia');
        
        // Process
        $db = $Api->getAdapter();
        $db->beginTransaction();

        try {
          if ($Api->isFeatured($this->_subject, $viewer)) {
              $Api->unFeatured($this->_subject, $viewer);
              $isFeatured = false;
          }
          else {
            $isFeatured = true;
            $Api->Featured($this->_subject, $viewer);
          }
          
          $db->commit();
        }
        catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }
        return $this->view->assign(array('status' => true,
                                         'isfeatured' => $isFeatured));
  }  

    public function toggleBoxAction() {
        $box_id = (int)$this->_getParam('box_id');
        if (empty($box_id)) {
            return $this->_helper->Message('Invalid box id', false, false)->setAjax()->setError(); 
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $listsTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
        $box = $listsTable->fetchRow(array('user_id = ?' => $viewer->getIdentity(),
                                           'circle_id = ?' => $box_id ));
        if (empty($box)) {
            return $this->_helper->Message('Invalid box id', false, false)->setAjax()->setError(); 
        }
        if ($box->has($this->_subject)) {
            $box->remove($this->_subject);
            return $this->view->assign(array('status' => true,
                                             'inbox' => false));
        }
        else {
            $box->add($this->_subject);
            return $this->view->assign(array('status' => true,
                                             'inbox' => true));
        }
    }
}
?>
