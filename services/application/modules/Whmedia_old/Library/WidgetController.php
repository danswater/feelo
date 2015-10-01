<?php

abstract class Whmedia_Library_WidgetController extends Engine_Content_Widget_Abstract
{
  
  public function renderScript()
  {
    if (!($this->_getParam('page') > 1)) {  
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            $listsTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
            $this->view->boxes = $boxes = $listsTable->fetchAll(array('user_id = ?' => $viewer->getIdentity()));
            //if (count($boxes)) {
                $this->appendContent($this->view->render('application/modules/Whmedia/views/scripts/etc/follow.tpl'));
            //}
        }  
    }
    if ($this->_getParam('show_type', 'list') == 'list') {
        return parent::renderScript();
    }
    else {
        $this->view->count_item = $this->_getParam('slider_show_items', 3);
        return $this->getView()->render('application/modules/Whmedia/views/scripts/etc/widgetSlider.tpl');
    }
  }

}