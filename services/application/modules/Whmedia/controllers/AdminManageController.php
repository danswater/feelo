<?php

class Whmedia_AdminManageController extends Whmedia_controllers_AdminController
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                                                           ->getNavigation('whmedia_admin_main', array(), 'whmedia_admin_main_manage');
      
    $this->view->paginator = $paginator =  Engine_Api::_()->whmedia()->getWhmediaPaginator();
    $paginator->setCurrentPageNumber( $this->_getParam('page'));
    $paginator->setItemCountPerPage(30);
  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->delete_title = 'Delete Media Project?';
    $this->view->delete_description = 'Are you sure that you want to delete this media project? It will not be recoverable after being deleted.';
    $id = $this->_getParam('id');
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $row = Engine_Api::_()->getItem('whmedia_project', $id);
        $row->delete();
     
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      if (($forward = $this->_getParam('forward', false)) == false) {
          return $this->_forward('success', 'utility', 'core', array('smoothboxClose' => true,
                                                                     'parentRefresh'=> true,
                                                                     'messages' => array($this->view->translate ('Media Project has been deleted.'))
                                                                    ));
      }
      else {
          return $this->_forward('success', 'utility', 'core', array('smoothboxClose' => true,
                                                                     'parentRedirect'=> Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'activity-feed'), 'whmedia_default', true),
                                                                     'messages' => array($this->view->translate ('Media Project has been deleted.'))
                                                                  ));
      }
    }

    // Output
    $this->renderScript('etc/delete.tpl');
  }

}