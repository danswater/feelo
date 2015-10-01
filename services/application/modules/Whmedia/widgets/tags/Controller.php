<?php

class Whmedia_Widget_TagsController extends Engine_Content_Widget_Abstract
{
  
  public function indexAction()
  {
    $this->view->populartags = $populartags = Engine_Api::_()->whmedia()->getPopularTags($this->_getParam('count_item', 5));

    // Do not render if nothing to show
    if( count($populartags) <= 0 ) {
      return $this->setNoRender();
    }

  }

  
}