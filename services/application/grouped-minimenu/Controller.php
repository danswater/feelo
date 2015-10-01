<?php

class Widget_GroupedMinimenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
      $viewer = Engine_Api::_()->user()->getViewer();
      if ($viewer->getIdentity() <= 0) 
          return $this->setNoRender();
  }
}