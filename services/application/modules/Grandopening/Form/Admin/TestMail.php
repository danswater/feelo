<?php

class Grandopening_Form_Admin_TestMail extends Engine_Form
{

  public function init()
  {
   
    $this
      ->setTitle('Send Test Email');

    
    $this->addElement('Text', 'email', array(
      'label' => 'E-mail:',     
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'EmailAddress',
      )
    ));

    $this->addElement('hidden', 'task', array('value' => 'test'));
    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Send',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
  
}