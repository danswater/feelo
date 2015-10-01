<?php

class Mediamasonry_Widget_ProfileLivefeedController extends Engine_Content_Widget_Abstract {
  public function  indexAction() {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');
    $this->view->followApi = $followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
    $following = $followApi->getFollowing($viewer);
    $params = array('is_published' => true,
                    'page' => $this->_getParam('page', 1),
                    'limit' => $this->_getParam('itemCountPerPage', 8) );
    if ($following->count() > 0) {
        $params['users'] = array();
        foreach ($following as $following_one) {
            $params['users'][] = $following_one->user_id;
        }
    }
    else return $this->setNoRender();
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getWhmediaPaginator($params);

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    $this->view->thumb_width = (int)$this->_getParam('thumb_width', 130);

    if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext()  == 'html') {
        $this->view->only_items = true;
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
    }
    else {
        $this->view->only_items = false;
    }
    $this->view->sendScript = ($this->_getParam('page', 1) > 1) ? false : true;
    
  }
  
  public function getChildCount()
  {
    return $this->_childCount;
  }
}