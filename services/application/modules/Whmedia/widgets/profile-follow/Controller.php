<?php

class Whmedia_Widget_ProfileFollowController extends Engine_Content_Widget_Abstract
{
 
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
     $this->view->requestFirst = 0;
    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if (!($subject instanceof User_Model_User)) return $this->setNoRender();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      $this->view->requestFirst = 1;
      //return $this->setNoRender();
    }
    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');
    $this->view->followApi = $followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
    
    if ($viewer->getIdentity() and !$subject->isOwner($viewer)) {
        $listsTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
        $this->view->boxes = $boxes = $listsTable->fetchAll(array('user_id = ?' => $viewer->getIdentity()));
        if (count($boxes)) {
            $this->appendContent($this->view->render('application/modules/Whmedia/views/scripts/etc/follow.tpl'));
        }
    }
  }
  
  
}