<?php

class Whmedia_Widget_PopularProjectController extends Whmedia_Library_WidgetController
{
  
  public function indexAction()
  {
    $params = array('page' => $this->_getParam('page', 1),
                    'limit' => $this->_getParam('count_media', 5),
                    'is_published' => true,
                    'orderby' => 'count_likes');
    switch ($this->_getParam('period_time', 5)) {
        case 'today':
            $res_time = 86400;
            break;
        case 'month':
            $res_time = 2592000;
            break;
        case 'week':
        default :
            $res_time = 604800;
            break;

    }

    $params['start_date'] = date( 'Y-m-d H:i:s', time() - $res_time );
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