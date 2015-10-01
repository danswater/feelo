<?php

class Whmedia_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction() {
    // Make form
    $this->view->form = $form = Whmedia_Form_Search::getInstance();
  }
}
