<?php

class Whmedia_Widget_FollowSuggestionController extends Engine_Content_Widget_Abstract
{
 
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) return $this->setNoRender();
    $this->view->follow_suggestion = $follow_suggestion = Zend_Paginator::factory(Engine_Api::_()->whmedia()->getFollowSuggestionSelect($viewer, 4));
    if (!count($follow_suggestion)) return $this->setNoRender();
    $this->view->followApi = $followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
  }
  
  public function getCacheKey() {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();  
    if ($viewer_id) {  
        return 'follow_suggestion_' . $viewer_id;
    }
    return 'follow_suggestion_0';
  }
}