<?php

class Grandopening_Form_Collection extends Engine_Form
{
  public function init()
  {
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'grandopening', 'controller'=>'email', 'action'=>'add'), 'default'))
         ->setAttrib('id', 'form-email-collection')
         ->setAttrib('onsubmit', 'sendmail(this);return false;');

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('grandopening_getname', 0)) {
        $this->addElement('Text', 'username', array(
            'label' => 'Name',
            'required' => false,
            'allowEmpty' => true
          ));
    }
    // Element: email
    $this->addElement('Text', 'email', array(
      'label' => 'Email',
      'description' => 'Leave your email to get notified when site is open.',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
        array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'grandopening_collections', 'email'))
      ),
    ));
    $this->email->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
    $this->email->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');
    $this->email->getValidator('Db_NoRecordExists')->setMessage('Someone has already registered this email address, please use another one.', 'recordFound');


    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Request Invite',
      'type' => 'submit',
      'ignore' => true
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'Login',
      'link' => true,
      'prependText' => ' or ',
      'href' => 'login',      
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}
