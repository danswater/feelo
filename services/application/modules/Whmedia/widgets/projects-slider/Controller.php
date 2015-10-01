<?php

class Whmedia_Widget_ProjectsSliderController extends Engine_Content_Widget_Abstract
{
  
  public function indexAction()  {
    // Get subject and check auth
    if (!Engine_Api::_()->core()->hasSubject())  {
      return $this->setNoRender();
    }
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    $owner = $subject->getOwner();
    if (!($owner instanceof User_Model_User)) {
      return $this->setNoRender();
    }


    $user_project_select = Engine_Api::_()->whmedia()->getWhmediaSelect(array('user' => $owner, 'is_published' => true));
    $this->view->user_projects = $user_project_select->getTable()->fetchAll($user_project_select);
    if ($subject instanceof Whmedia_Model_Project ) {
        if ($this->view->user_projects->count() <= 1)
            return $this->setNoRender();
        $this->view->project = $subject;
    }
    else {
        if (!$this->view->user_projects->count())
            return $this->setNoRender();
    }
    $this->view->count_item = $this->_getParam('count_item', 5);
  }

}