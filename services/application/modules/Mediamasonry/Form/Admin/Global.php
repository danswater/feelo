<?php

class Mediamasonry_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setTitle('Masonry Add-on Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Radio', 'mediamasonry_enable', array(
      'label' => 'Enable masonry layout',
      'description' => "Do you want to enable masonry layout for 'browse projects' and 'my projects' pages.",
      'multiOptions' => array(
        1 => 'Yes, do that.',
        0 => 'No, thanks.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('mediamasonry_enable', 1),
    ));

// Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));

  }
}
