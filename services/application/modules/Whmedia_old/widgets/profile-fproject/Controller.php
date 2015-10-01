<?php

class Whmedia_Widget_ProfileFprojectController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
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
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getWhmediaPaginator(array('fuser' => $subject,
                                                                                                'is_published' => true,
                                                                                                'page' => $this->_getParam('page', 1),
                                                                                                'limit' => $this->_getParam('itemCountPerPage', 8) ));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    $this->view->thumb_width = (int)$this->_getParam('thumb_width', 130);
    $this->view->thumb_height = (int)$this->_getParam('thumb_height', 100);
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}