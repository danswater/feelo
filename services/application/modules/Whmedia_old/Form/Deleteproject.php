<?php

class Whmedia_Form_Deleteproject extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Delete Project')
      ->setDescription('Are you sure you want to delete this project? All media files will be deleted.')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    // Element: token
    $this->addElement('Hash', 'token');

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Yes, delete this project',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    
    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'execute',
      'cancel',
    ), 'buttons');
    
  }
}