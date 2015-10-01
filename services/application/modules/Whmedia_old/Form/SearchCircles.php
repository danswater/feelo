<?php

class Whmedia_Form_SearchCircles extends Engine_Form
{
  public function init()
  {
    parent::init();

    // Add custom elements
    $this->addElement('Text', 'displayname', array(
        'label' => 'Name',
        //'onkeypress' => 'return submitEnter(event)',
    ));

    $this->addElement('Button', 'done', array(
        'label' => 'Search',
        'onclick' => 'friends_in_circle.searchMembers()',
        'ignore' => true,
    ));

    $this->setAttrib('onsubmit', 'friends_in_circle.searchMembers();return false;');
    //hidden for page
    $page_el = new Zend_Form_Element_Hidden('page');
    $page_el->setValue('1');
    $page_el->removeDecorator('Label');
    $page_el->removeDecorator('HtmlTag');
    $this->addElement($page_el, 'page');
  }
}