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
class User_Form_Signup_Element extends Zend_Form_Element_Xhtml
{
	/**
	 * Default form view helper to use for rendering
	 * @var string
	 */
	 public $helper = 'formNote';
	 
	 public function isValid ( $value, $context = null ) {
		// for now lets just return this to true
		// so that we can create custom element
		return true;
	 } 
	 
}
