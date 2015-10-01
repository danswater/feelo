<?php

class Whmedia_Widget_MediaController extends Whmedia_Library_WidgetController
{
  
  public function indexAction()
  {
    $params = array('page' => $this->_getParam('page', 1),
                    'limit' => $this->_getParam('count_media', 5),
                    'is_text' => false,
                    'invisible' => false);
    if ($this->_getParam('show_media', 'newest') == 'random') {
        $params['orderby'] = 'random';
    }
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getMediaPaginator($params);

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
    $this->view->thumb_width = (int)$this->_getParam('thumb_width', 100);
    $this->view->thumb_height = (int)$this->_getParam('thumb_height', 100);
  }

  
}