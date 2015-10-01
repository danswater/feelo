<?php

class Grandopening_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Radio', 'grandopening_enable', array(
          'label' => 'Enable Grand Opening',
          'description' => 'Do you want to enable grand opening?',
          'multiOptions' => array(
            1 => 'Yes, enable grand opening.',
            0 => 'No.'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('grandopening_enable', 0),
    ));
    $this->addElement('Radio', 'grandopening_getname', array(
          'label' => 'Fields for notifications',
          'description' => 'What fields do you want to show in form?',
          'multiOptions' => array(
            1 => 'Name and Email',
            0 => 'Email only'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('grandopening_getname', 0),
    ));
    $this->addElement('Radio', 'use_date', array(
      'label' => 'Countdown',
      'description' => 'Do you want to show counter on GrandOpening Page?',
      'multiOptions' => array(
        1 => 'Yes.',
        0 => 'No.'
      ),
      'onchange' => "javascript:set_date(this);",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('use_date', 0),
    ));
    // End time
    $end = new Engine_Form_Element_CalendarDateTime('grandopening_endtime');
    $end->setLabel("Countdown till");
    date_default_timezone_set(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'UTC'));
    $date_format = ($end->useMilitaryTime) ? 'd-m-Y H:i' : 'd-m-Y h:i A' ;
    $end->setDescription('Server Time: ' . date($date_format) . ' (Timezone: ' . substr_replace(date('O'),":",3,0) . ')');
    $end->setAllowEmpty(true);
    $end->setValue(Engine_Api::_()->getApi('settings', 'core')->getSetting('grandopening_endtime', 0));
    $end->getDecorator('Description')->setOption('placement', 'APPEND');
    $end->addValidator(new Grandopening_Validate_GoEndDate());
    $this->addElement($end);

    // Element: inviteonly
    $this->addElement('Radio', 'inviteonly', array(
      'label' => 'Invite Only?',
      'description' => 'whUSER_FORM_ADMIN_SIGNUP_INVITEONLY_DESCRIPTION',
      'multiOptions' => array(
        2 => 'Yes, admins and members must invite new members before they can signup.',
        1 => 'Yes, admins must invite new members before they can signup.',
        0 => 'No, disable the invite only feature.',
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('user_signup.inviteonly', 0)
    ));
    
    // Element: checkemail
    $this->addElement('Radio', 'checkemail', array(
      'label' => 'Check Invite Email?',
      'description' => 'USER_FORM_ADMIN_SIGNUP_CHECKEMAIL_DESCRIPTION',
      'multiOptions' => array(
        1 => "Yes, check that a member's email address was invited.",
        0 => "No, anyone with an invite code can signup.",
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('user_signup.checkemail', 0)
    ));
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
    $this->hideCheck();
  }

  public function hideCheck() {
      $style = ($this->use_date->getValue()) ? 'block' : 'none';
      $this->grandopening_endtime->getDecorator('HtmlTag2')->setOption('style', "display:$style;");
  }
}
