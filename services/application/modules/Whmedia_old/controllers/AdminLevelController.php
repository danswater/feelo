<?php

class Whmedia_AdminLevelController extends Whmedia_controllers_AdminController
{
  public function indexAction()
  {
 
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                                                           ->getNavigation('whmedia_admin_main', array(), 'whmedia_admin_main_level');
    // Get level id
    if( null !== ($level_id = $this->_getParam('level_id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $level_id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $level_id = $level->level_id;

    // Make form
    $this->view->form = $form = new Whmedia_Form_Admin_Level(array('public'=>( in_array($level->type, array('public')) )));

    $form->level_id->setValue($level_id);

    // Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('whmedia_project', $level_id, array_keys($form->getValues())));
    
    // Process form
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
          // Process

        $values = $form->getValues();

        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();

        try
        {
          // Set permissions
          $permissionsTable->setAllowed('whmedia_project', $level_id, $values);

          // Commit
          $db->commit();
        }

        catch( Exception $e )
        {
          $db->rollBack();
          throw $e;
        }
        $form->addNotice('Your changes have been saved.')
             ->hideCheck();
    }
  }
}