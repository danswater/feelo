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
class Core_Widget_AdminMenuMainController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_admin_main');

    foreach ($this->view->navigation->getPages() as $key => $page)
      if ('Plugins' == $page->getLabel() && 0 == count($page->getPages()))
        $page->setVisible(false);

  }
}