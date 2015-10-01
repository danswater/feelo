<?php

class Grandopening_Widget_EmailFormController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->form = new Grandopening_Form_Collection();
  }
}