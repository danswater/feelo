<?php

class Grandopening_Form_Admin_Mail extends Engine_Form
{

  public function init()
  {
    $link = Zend_Controller_Front::getInstance()->getRouter()->assemble(array("controller" => "mail", "action" => "settings"), "admin_default", true);
    $description = 'Using this form, you will be able to send an email out to all of your subscribers.  Emails are
      sent out using a queue system, so they will be sent out over time. Before sent mail, please setup <a href="'.$link.'">Mail Settings</a>.';
    $this
      ->setTitle('Send Announcement Emails')
      ->setDescription($description);
    $settings = Engine_Api::_()->getApi('settings', 'core')->core_mail;

    
    $this->addElement('Text', 'from_address', array(
      'label' => 'From:',
      'value' => (!empty($settings['from']) ? $settings['from'] : 'noreply@' . $_SERVER['HTTP_HOST']),
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'EmailAddress',
      )
    ));

    $this->addElement('Text', 'from_name', array(
      'label' => 'From (name):',
      'required' => true,
      'allowEmpty' => false,
      'value' => (!empty($settings['name']) ? $settings['name'] : 'Site Administrator'),
    ));

        
    $this->addElement('Text', 'subject', array(
      'label' => 'Subject:',
      'required' => true,
      'allowEmpty' => false,
    ));

    $this->addElement('Textarea', 'body', array(
      'label' => 'Body',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'Available Placeholders: [recipient_title], [recipient_email]. (HTML or Plain Text)',
    ));
    $this->body->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Send Emails',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
  
  public function loadDefaultDecorators() {
      parent::loadDefaultDecorators();
      $this->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'class' => 'form-description', 'escape' => false));
  }
}