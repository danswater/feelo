<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_Widget_MenuLogoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	/** newly added */

	$require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if(!$require_check){
      if( $viewer->getIdentity()){
        $this->view->search_check = true;
      }
      else{
        $this->view->search_check = false;
      }
    }
    else $this->view->search_check = true;

  	/** newly added */
    $this->view->logo = $this->_getParam('logo');

    $this->view->cur_module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
    $this->view->cur_controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
    $this->view->cur_action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

  }

  public function getCacheKey()
  {
    //return true;
  }
}