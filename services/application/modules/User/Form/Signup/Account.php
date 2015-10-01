<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Account.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Form_Signup_Account extends Engine_Form
{
  public function init()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $inviteSession = new Zend_Session_Namespace('invite');
    $tabIndex = 1;
    
    // Init form
    $this->setTitle('Create Account');

    // Element: email
    $this->addElement('Text', 'email', array(
      'label' => 'Email Address',
      'description' => 'You will use your email address to login.',
      'required' => true,
      'allowEmpty' => false,
      'placeholder' => "Email Address",
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
        array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'email'))
      ),
      'filters' => array(
        'StringTrim'
      ),
      // fancy stuff
      'inputType' => 'email',
      'autofocus' => 'autofocus',
      'tabindex' => $tabIndex++,
    ));
    $this->email->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
    $this->email->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');
    $this->email->getValidator('Db_NoRecordExists')->setMessage('Someone has already registered this email address, please use another one.', 'recordFound');

    // Add banned email validator
    $bannedEmailValidator = new Engine_Validate_Callback(array($this, 'checkBannedEmail'), $this->email);
    $bannedEmailValidator->setMessage("This email address is not available, please use another one.");
    $this->email->addValidator($bannedEmailValidator);
    
    if( !empty($inviteSession->invite_email) ) {
      $this->email->setValue($inviteSession->invite_email);
    }

    if( $settings->getSetting('user.signup.random', 0) == 0 && 
        empty($_SESSION['facebook_signup']) && 
        empty($_SESSION['twitter_signup']) && 
        empty($_SESSION['janrain_signup']) ) {

      // Element: password
      $this->addElement('Password', 'password', array(
        'label' => 'Password',
        'placeholder' => "Password",
        'description' => 'Passwords must be at least 6 characters in length.',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
          array('NotEmpty', true),
          array('StringLength', false, array(6, 32)),
        ),
        'tabindex' => $tabIndex++,
      ));
      $this->password->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
      $this->password->getValidator('NotEmpty')->setMessage('Please enter a valid password.', 'isEmpty');
    }
	
	// added for usernameElement
	if( $settings->getSetting('user.signup.username', 1) > 0 ) {
      $description = Zend_Registry::get('Zend_Translate')
          ->_('This will be the end of your profile link, for example: <br /> ' .
              '<span id="profile_address">http://%s</span>');
      $description = sprintf($description, $_SERVER['HTTP_HOST']
          . Zend_Controller_Front::getInstance()->getRouter()
          ->assemble(array('id' => 'yourname'), 'user_profile'));

      $this->addElement('Text', 'username', array(
        'label' => 'Profile Address',
        'placeholder' => "Profile Address",
        'description' => $description,
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
          array('NotEmpty', true),
          array('Alnum', true),
          array('StringLength', true, array(4, 64)),
          array('Regex', true, array('/^[a-z][a-z0-9]*$/i')),
          array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'username'))
        ),
        'tabindex' => $tabIndex++,
          //'onblur' => 'var el = this; en4.user.checkUsernameTaken(this.value, function(taken){ el.style.marginBottom = taken * 100 + "px" });'
      ));
      $this->username->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
      $this->username->getValidator('NotEmpty')->setMessage('Please enter a valid profile address.', 'isEmpty');
      $this->username->getValidator('Db_NoRecordExists')->setMessage('Someone has already picked this profile address, please use another one.', 'recordFound');
      $this->username->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
      $this->username->getValidator('Alnum')->setMessage('Profile addresses must be alphanumeric.', 'notAlnum');

      // Add banned username validator
      $bannedUsernameValidator = new Engine_Validate_Callback(array($this, 'checkBannedUsername'), $this->username);
      $bannedUsernameValidator->setMessage("This profile address is not available, please use another one.");
      $this->username->addValidator($bannedUsernameValidator);
    }
    
    // Element: language
    // Languages
    $translate = Zend_Registry::get('Zend_Translate');
    $languageList = $translate->getList();

    //$currentLocale = Zend_Registry::get('Locale')->__toString();
    // Prepare default langauge
    $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
    if( !in_array($defaultLanguage, $languageList) ) {
      if( $defaultLanguage == 'auto' && isset($languageList['en']) ) {
        $defaultLanguage = 'en';
      } else {
        $defaultLanguage = null;
      }
    }

    // Prepare language name list
    $localeObject = Zend_Registry::get('Locale');
    
    $languageNameList = array();
    $languageDataList = Zend_Locale_Data::getList($localeObject, 'language');
    $territoryDataList = Zend_Locale_Data::getList($localeObject, 'territory');

    foreach( $languageList as $localeCode ) {
      $languageNameList[$localeCode] = Zend_Locale::getTranslation($localeCode, 'language', $localeCode);
      if( empty($languageNameList[$localeCode]) ) {
        list($locale, $territory) = explode('_', $localeCode);
        $languageNameList[$localeCode] = "{$territoryDataList[$territory]} {$languageDataList[$locale]}";
      }
    }
    $languageNameList = array_merge(array(
      $defaultLanguage => $defaultLanguage
    ), $languageNameList);

    if(count($languageNameList)>1){
      $this->addElement('Select', 'language', array(
        'label' => 'Language',
        'placeholder' => "Language",
        'multiOptions' => $languageNameList,
        'tabindex' => $tabIndex++,
      ));
      $this->language->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
    }
    else{
      $this->addElement('Hidden', 'language', array(
        'value' => current((array)$languageNameList) 
      ));
    }

    // Element: captcha
    if( Engine_Api::_()->getApi('settings', 'core')->core_spam_signup ) {
      $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions(array(
        'tabindex' => $tabIndex++,
      )));
    }
    
    if( $settings->getSetting('user.signup.terms', 1) == 1 ) {
      // Element: terms
      $description = Zend_Registry::get('Zend_Translate')->_('I have read and agree to the <a target="_blank" href="%s/help/terms">terms of service</a>.');
      $description = sprintf($description, Zend_Controller_Front::getInstance()->getBaseUrl());

      $this->addElement('Checkbox', 'terms', array(
        'label' => 'Terms of Service',
        'description' => $description,
        'required' => true,
        'validators' => array(
          'notEmpty',
          array('GreaterThan', false, array(0)),
        ),
        'tabindex' => $tabIndex++,
      ));
      $this->terms->getValidator('GreaterThan')->setMessage('You must agree to the terms of service to continue.', 'notGreaterThan');
      //$this->terms->getDecorator('Label')->setOption('escape', false);

      $this->terms->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::APPEND, 'tag' => 'label', 'class' => 'null', 'escape' => false, 'for' => 'terms'))
          ->addDecorator('DivDivDivWrapper');

      //$this->terms->setDisableTranslator(true);
    }

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Sign Up',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => $tabIndex++,
    ));
	
	// add facebook signup button
	if( $settings->getSetting('user.signup.facebook', 1) == 1 ) {
		$tag = new User_Form_Signup_Element( 'facebookSignup' );
		$tag->setValue( '
			<div style="text-align: center; font-size: 15px"><p style="padding-left: 18px">or connect using</p></div>
			<a class="facebook-tag" href="user/auth/facebook">
				<div class="hw-box fbbg"> 
					<img src="application/modules/User/externals/images/facebook-logo.png"/>
					<span> facebook </span>
				</div>
			</a>' 
		);
		$this->addElement( $tag );
	}
    
    if( empty($_SESSION['facebook_signup']) ){
      // Init facebook login link
//      if( 'none' != $settings->getSetting('core_facebook_enable', 'none')
//          && $settings->core_facebook_secret ) {
//        $this->addElement('Dummy', 'facebook', array(
//          'content' => User_Model_DbTable_Facebook::loginButton(),
//        ));
//      }
    }
    
    if( empty($_SESSION['twitter_signup']) ){
      // Init twitter login link
//      if( 'none' != $settings->getSetting('core_twitter_enable', 'none')
//          && $settings->core_twitter_secret ) {
//        $this->addElement('Dummy', 'twitter', array(
//          'content' => User_Model_DbTable_Twitter::loginButton(),
//        ));
//      }
    }
	
    // this will contain the timzone of user
    $this->addElement('hidden', 'timezone', array(
    'id' => 'timezone'
    ) );
	
    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_signup', true));
  }

  public function checkPasswordConfirm($value, $passwordElement)
  {
    return ( $value == $passwordElement->getValue() );
  }

  public function checkInviteCode($value, $emailElement)
  {
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    $select = $inviteTable->select()
      ->from($inviteTable->info('name'), 'COUNT(*)')
      ->where('code = ?', $value)
      ;
      
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.checkemail') ) {
      $select->where('recipient LIKE ?', $emailElement->getValue());
    }
    
    return (bool) $select->query()->fetchColumn(0);
  }

  public function checkBannedEmail($value, $emailElement)
  {
    $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');
    return !$bannedEmailsTable->isEmailBanned($value);
  }

  public function checkBannedUsername($value, $usernameElement)
  {
    $bannedUsernamesTable = Engine_Api::_()->getDbtable('BannedUsernames', 'core');
    return !$bannedUsernamesTable->isUsernameBanned($value);
  }
}
