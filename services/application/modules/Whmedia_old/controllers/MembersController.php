<?php

class Whmedia_MembersController extends Whmedia_Controller_Action_Follow
{
  
  public function init()
  {
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.portal', 1)){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }   
  }  
  
  public function  postDispatch() {
    parent::postDispatch();
    $this->_getNavigation();
    $this->view->pageTitle = 'Follow Members / Members Page';
    if ($this->_helper->contextSwitch->getCurrentContext() === null) {
        $this->getResponse()->appendBody($this->view->render('etc/head.tpl'));
    }
  }
  
  public function searchAction()
  {
    $this->_executeSearch();
  }
  
  public function featuredAction()
  {
    $userTable = Engine_Api::_()->getItemTable('user');
    $userName = $userTable->info('name');
    $featuredTable = Engine_Api::_()->getDbtable('featured', 'whmedia');
    $featuredName = $featuredTable->info('name');
    
    $select = $userTable->select()->from($userName)
                                  ->setIntegrityCheck(false)
                                  ->joinLeft($featuredName, $userName.'.user_id = ' . $featuredName.'.featured_id', array())
                                  ->where("`{$featuredName}`.`featured_id` is not null")
                                  ->order( $featuredName.'.creation_date DESC');  

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $this->view->paginator = $paginator->setCurrentPageNumber((int)$this->_getParam('page', 1));
  }

  public function mostActiveAction()
  {
    $userTable = Engine_Api::_()->getItemTable('user');
    $userName = $userTable->info('name');
    $projectTable = Engine_Api::_()->getItemTable('whmedia_project');
    $projectName = $projectTable->info('name');
    
    $select = $userTable->select()->from($userName)
                                  ->setIntegrityCheck(false)
                                  ->joinLeftUsing($projectName, 'user_id', array('activity' => 'COUNT(*)'))
                                  ->group($userName.'.user_id')  
                                  ->having('activity > 0')
                                  ->order( 'activity DESC');  
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $this->view->paginator = $paginator->setCurrentPageNumber((int)$this->_getParam('page', 1));
  }
  
  public function followSuggestionAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();  
    if( !$viewer->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }  
    $paginator = Zend_Paginator::factory(Engine_Api::_()->whmedia()->getFollowSuggestionSelect($viewer));
    $paginator->setItemCountPerPage(10);
    $this->view->paginator = $paginator->setCurrentPageNumber((int)$this->_getParam('page', 1));
  }
  
  public function newAction()
  {
    $userTable = Engine_Api::_()->getItemTable('user');
    $select = $userTable->select()->where('creation_date > ?', date( 'Y-m-d H:i:s', time() - 2592000 ))
                                  ->order( 'creation_date DESC');  
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $this->view->paginator = $paginator->setCurrentPageNumber((int)$this->_getParam('page', 1));
  }
 
  protected function _executeSearch()
  {
    // Check form
    $this->view->form = $form = new User_Form_Search(array(
      'type' => 'user'
    ));
    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    $form->extra->done->setAttrib('type', 'submit');

    
    if( !$form->isValid($this->_getAllParams()) ) {
      $this->view->error = true;
      $this->view->totalUsers = 0; 
      $this->view->userCount = 0; 
      $this->view->page = 1;
      return false;
    }

    $this->view->form = $form;

    // Get search params
    $page = (int)  $this->_getParam('page', 1);
    $format = (bool) $this->_getParam('format');
    $ajax = false;
    if ($format == 'html')
        $ajax = true;
    
    $options = $form->getValues();
    
    // Process options
    $tmp = array();
    $originalOptions = $options;
    foreach( $options as $k => $v ) {
      if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
        continue;
      } else if( false !== strpos($k, '_field_') ) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if( false !== strpos($k, '_alias_') ) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $options = $tmp;

    // Get table info
    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
    $searchTableName = $searchTable->info('name');

    //extract($options); // displayname
    $profile_type = @$options['profile_type'];
    $displayname = @$options['displayname'];
    if (!empty($options['extra'])) {
      extract($options['extra']); // is_online, has_photo, submit
    }

    // Contruct query
    $select = $table->select()
      //->setIntegrityCheck(false)
      ->from($userTableName)
      ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
      //->group("{$userTableName}.user_id")
      ->where("{$userTableName}.search = ?", 1)
      ->where("{$userTableName}.enabled = ?", 1)
      ->order("{$userTableName}.displayname ASC");

    // Build the photo and is online part of query
    if( isset($has_photo) && !empty($has_photo) ) {
      $select->where($userTableName.'.photo_id != ?', "0");
      $additionalData['has_photo'] = 1;
    }

    if( isset($is_online) && !empty($is_online) ) {
      $select
        ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
        ->group("engine4_user_online.user_id")
        ->where($userTableName.'.user_id != ?', "0");
        $additionalData['is_online'] = 1;
    }

    // Add displayname
    if( !empty($displayname) ) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
      $additionalData['displayname'] = $displayname;
    }

    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
    foreach( $searchParts as $k => $v ) {
      $select->where("`{$searchTableName}`.{$k}", $v);
    }
    
    if (isset($additionalData) && is_array($additionalData) && count($additionalData) > 0) {
        foreach ($additionalData as $key => $elem) {
            $dataArr[] = $key . ':\'' . $elem . '\'';
        }
        $this->view->addition_data = '{' . implode(',', $dataArr) . '}';
    }
    
    // Build paginator
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($page);
    
    $this->view->page = $page;
    $this->view->ajax = $ajax;
    $this->view->paginator = $paginator;
    $this->view->totalUsers = $paginator->getTotalItemCount();
    $this->view->userCount = $paginator->getCurrentItemCount();
    $this->view->topLevelId = $form->getTopLevelId();
    $this->view->topLevelValue = $form->getTopLevelValue();
    $this->view->formValues = array_filter($originalOptions);

    return true;
  }
  
  protected function _getNavigation() {
    $this->view->navigation = $navigation = new Zend_Navigation();

    $navigation->addPage(array(
        'label' =>  $this->view->translate('Search'),
        'route' => 'whmedia_members',
        'action' => 'search'
    ));	
    
    $navigation->addPage(array(
        'label' =>  $this->view->translate('Featured'),
        'route' => 'whmedia_members',
        'action' => 'featured'
    ));	
    
    $navigation->addPage(array(
        'label' =>  $this->view->translate('Most Active'),
        'route' => 'whmedia_members',
        'action' => 'most-active'
    ));	    
    
    $navigation->addPage(array(
        'label' =>  $this->view->translate('New'),
        'route' => 'whmedia_members',
        'action' => 'new'
    ));	
    
    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
        $navigation->addPage(array(
            'label' =>  $this->view->translate('Follow Suggestion'),
            'route' => 'whmedia_members',
            'action' => 'follow-suggestion'
        ));	
    }
  }
}