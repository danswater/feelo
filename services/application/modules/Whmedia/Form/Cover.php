<?php

class Whmedia_Form_Cover extends Engine_Form
{

  public function init()
  {
    $this
      ->setMethod('post')
      ->setAttrib('class', 'global_form_box')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setTitle('Upload New Post Cover');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->addElement('File', 'cover', array(
      'destination' => APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary',
      'required' => true,
      'label' => 'Select Cover',
      'allowEmpty' => false,
      'validators' => array('IsImage',
                            array('validator' => 'Count', 'options' => array(false, 1)),
                            array('validator' => 'Size', 'options' => array(false, 'max' => 2097152)),
                            array('validator' => 'ImageSize', 'options' => array(false,
                                                                                 'minwidth' => $settings->getSetting('thumb_width', 100),
                                                                                 'minheight' => $settings->getSetting('thumb_height', 100)
                                                                                 )
                                 ),
                           )
    ));
    $this->addElement('Dummy','file-desc', array(
        'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("Image minimal width: %dpx, minimal height: %dpx."), $settings->getSetting('thumb_width', 100), $settings->getSetting('thumb_height', 100))
    ));


    $this->addElement('Hidden', 'task', array('value' => 'upload_cover'));
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => "Upload",
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

   
  }


}
