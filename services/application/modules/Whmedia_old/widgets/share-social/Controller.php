<?php

class Whmedia_Widget_ShareSocialController extends Engine_Content_Widget_Abstract
{
  
  public function indexAction()  {
    // Get subject and check auth
    if (!Engine_Api::_()->core()->hasSubject('whmedia_project'))  {
      return $this->setNoRender();
    }
    $subject = Engine_Api::_()->core()->getSubject('whmedia_project');
    if ($subject instanceof Whmedia_Model_Project )
        $this->view->project = $subject;
    else
        return $this->setNoRender();
  }

}