<?php

class Whmedia_Widget_FeaturedProjectController extends Whmedia_Library_WidgetController
{
  
  public function indexAction()
  {
    $params = array('page' => $this->_getParam('page', 1),
                    'limit' => $this->_getParam('count_media', 5),
                    'fuser' => Engine_Api::_()->whmedia()->getUserFlag(),
                    'is_published' => true);
    
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getWhmediaPaginator($params);

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
    $this->view->thumb_width = (int)$this->_getParam('thumb_width', 100);
    $this->view->thumb_height = (int)$this->_getParam('thumb_height', 100);

  }

}