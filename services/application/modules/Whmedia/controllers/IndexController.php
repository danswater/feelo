<?php

class Whmedia_IndexController extends Whmedia_Controller_Action_Follow
{
  public function  init() {
    parent::init();
    // Render
    $this->_helper->content->setEnabled();
  }
  public function indexAction() {
    return $this->_helper->redirector->gotoRoute(array( 'controller' => 'search', 'type' => 'tags' ), 'default', true); // should not be here. redirect to explorer


    if( !$this->_helper->requireAuth()->setAuthParams('whmedia_project', null, 'view')->isValid() ) return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->whmedia_title = Zend_Registry::get('Zend_Translate')->_('Media');
    $this->view->form = $form = Whmedia_Form_Search::getInstance();
    $url_cat = $this->getFrontController()->getRouter()->assemble(array('category' => ''), 'whmedia_category', true);
    $url = $this->getFrontController()->getRouter()->assemble(array(), 'whmedia_default', true);


    // Populate form
    $this->view->categories = $categories = Engine_Api::_()->whmedia()->getCategories();
     // Process form
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
        $values = $form->getValues();  
    }
    else {
        $values = array();
        if ($this->_hasParam('category')) {
            $category = $this->_getParam('category');
            $tmp_categories = $categories->getRowMatching('url', $category);
            if (empty($tmp_categories))  {
                return $this->_helper->redirector->gotoRouteAndExit(array(), 'whmedia_default');
            }
            else {
                $values['category'] = $category;
                $form->category->setValue($category);
            }
        }
    }

    if ($user_whmedia_id = $this->_getParam('user_id', false)) {
        $values['user_id'] = $user_whmedia_id;
        $this->view->owner = Engine_Api::_()->getItem('user', $user_whmedia_id);
    }

    if( $values[ 'tags' ] ) {
      $resultRow = Engine_Api::_()->whmedia()->getTagId( $values[ 'tags' ] );
      $values[ 'tag_id' ] = $resultRow[ 'tag_id' ];

      $values[ 'followed' ] = Engine_Api::_()->whmedia()->isFollowed( $viewer, $values );
    }

    // Do the show thingy
    if( @$values['show'] == 2 )
    {

      $table = Engine_Api::_()->getItemTable('user');
      $select = $viewer->membership()->getMembersSelect('user_id');
      $friends = $table->fetchAll($select);
      // Get stuff
      $ids = array();
      foreach( $friends as $friend )
      {
        $ids[] = $friend->user_id;
      }
      //unset($values['show']);
      $values['users'] = $ids;
    }

    $this->view->assign($values);
    $values['is_published'] = true;
    $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getWhmediaPaginator($values);
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('media_per_page', 15);
    $paginator->setCurrentPageNumber( $this->_getParam('page'));
    $paginator->setItemCountPerPage($items_per_page);
    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('whmedia_project', null, 'create')->checkRequire();
    
    $this->view->thumb_width = 264;
    $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
    if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext()  == 'html') {
        $this->view->only_items = true;
        $this->_helper->layout->disableLayout(true);
        $this->_helper->content->setEnabled(false);
    }
    else {
        $this->view->only_items = false;
    }

  }

  public function viewAction() {
	
    $project = Engine_Api::_()->getItem('whmedia_project', $this->_getParam('project_id'));
	$viewer = Engine_Api::_()->user()->getViewer();

	if( $viewer->isBlockedBy(  $project->getOwner() ) && !$viewer->isAdmin() ) {
      return $this->_forward('requireauth', 'error', 'core');
    }
	
    if( !Engine_Api::_()->core()->hasSubject('whmedia_project') and $project instanceof Whmedia_Model_Project)
    {
      Engine_Api::_()->core()->setSubject($project);
    }
    if( !$this->_helper->requireSubject()->isValid() ) return;
   
    if (!$project->is_published) {
        if (!$project->isOwner($viewer) and !Engine_Api::_()->whmedia()->isAdmin($viewer)) {
            Engine_Api::_()->core()->clearSubject();
            $this->_helper->content->setEnabled(false);
            return $this->_helper->Message("Post isn't published.", false, false)->setError();
        }
    }
    if (!Engine_Api::_()->whmedia()->isAdmin($viewer)) {
        if( !$this->_helper->requireAuth()->setAuthParams($project, $viewer, 'view')->isValid()) {
            Engine_Api::_()->core()->clearSubject();
            $this->_helper->content->setEnabled(false);
            return;
        }
    }
    $project->project_views++;
    $project->save();
	
	$description = $project->description;
	if( empty( $description ) ) {
		$description = ' ';
	}

	$view = Zend_Registry::get('Zend_View');
	$view->headMeta()->appendProperty( 'og:site_name', 'Yamba' );
	$view->headMeta()->appendProperty( 'og:title', $project->title );
	$view->headMeta()->appendProperty( 'og:description', $description );
	$view->headMeta()->appendProperty( 'og:image', 'http://119.9.79.29' . $project->getPhotoUrl( '200px', '200px' ) );


    $this->view->project = $project;
    $this->view->categories = Engine_Api::_()->whmedia()->getCategories();
    $this->view->img_width = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('image_width', '600')+90;
    $this->view->isOwner = $isOwner = $project->isOwner($viewer);
    $addition_media_filtr = array();
    if (!$isOwner and !Engine_Api::_()->whmedia()->isAdmin($viewer)) {
        $addition_media_filtr[] = 'encode <= 1';
    }
    $this->view->medias = $project->getMedias($addition_media_filtr);
    $this->view->allow_d_orig = Engine_Api::_()->authorization()->context->isAllowed($project, 'everyone', 'allow_d_orig');
    $this->view->isMobile = Engine_Api::_()->whmedia()->isMobile();
  }

  public function manageAction() {
      if( !$this->_helper->requireUser()->isValid() ) return;
      if (!$can_create = $this->_helper->requireAuth()->setAuthParams('whmedia_project', null, 'create')->checkRequire()) return;
      $viewer = Engine_Api::_()->user()->getViewer();
      $this->view->whmedia_title = Zend_Registry::get('Zend_Translate')->_('Media');
      $this->view->form = $form = Whmedia_Form_Search::getInstance();
      $form->removeElement('show');
      $multi = $form->orderby->getMultiOptions();
      unset($multi['project_user']);
      $form->orderby->setMultiOptions($multi);
       // Process form
      $values = array();
      if ($form->isValid($this->getRequest()->getPost()))
              $values = $form->getValues();
      $values['user_id'] = $viewer->getIdentity();
      $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getWhmediaPaginator($values);
      $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('media_per_page', 15);
      $paginator->setCurrentPageNumber( $this->_getParam('page'));
      $paginator->setItemCountPerPage($items_per_page);
      $this->view->can_create = $can_create;
      $this->view->isApple = Engine_Api::_()->whmedia()->isApple();
      $this->view->isMobile = Engine_Api::_()->whmedia()->isMobile();
      
      $this->view->thumb_width = 264;
      $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
      if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext()  == 'html') {
            $this->view->only_items = true;
            $this->_helper->layout->disableLayout(true);
            $this->_helper->content->setEnabled(false);
      }
      else {
            $this->view->only_items = false;
      }
  }

  public function showMediaAction() {
      if( !$this->_helper->requireAuth()->setAuthParams('whmedia_project', null, 'view')->isValid() ) return;
      $viewer = Engine_Api::_()->user()->getViewer();
      $media = (int)$this->_getParam('media', false);
      if (empty ($media)) {
          return $this->_helper->Message('Media is empty.', false, false)->setError();
      }
      $this->view->media = $media = Engine_Api::_()->getItem('whmedia_media', $media);
      if (empty ($media)) {
          return $this->_helper->Message('Media is empty.', false, false)->setError();
      }
      $this->view->project = $project = $media->getProject();
      if( !$this->_helper->requireAuth()->setAuthParams($project, $viewer, 'view')->isValid()) return;
      $this->_helper->content->setEnabled(false);
      $project_medias = $project->getMedias(array('is_text = 0'));
      $project_medias_count = $project_medias->count(array('is_text = 0'));
      $this->view->previous = false;
      $this->view->next = false;
      $this->view->hot_keys_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('arrow_sliding', 1);
      if ($project_medias_count > 1) {
        foreach ($project_medias as $project_media) {          
            if ($project_media->getIdentity() == $media->getIdentity() ) {
                $key = $project_medias->key();
                if ($key > 0) {
                    $this->view->previous = array('route' => 'whmedia_default', 'action' => 'show-media', 'media' => $project_medias->getRow($key - 1)->getIdentity(), 'format' => 'smoothbox');
                }
                if ($key < ($project_medias_count - 1)) {
                    $this->view->next = array('route' => 'whmedia_default', 'action' => 'show-media', 'media' => $project_medias->getRow($key + 1)->getIdentity(), 'format' => 'smoothbox');
                }
                break;
            }
        }
      }

  }
  
  public function popularAction() {
        $params = array('page' => $this->_getParam('page', 1),
                        'limit' => 10,
                        'is_published' => true,
                        'orderby' => 'project_views');
                        //'orderby' => 'count_likes');
        switch ($this->_getParam('time_period')) {
            case 'today':
                $res_time = 86400;
                break;
            case 'month':
                $res_time = 2592000;
                break;
            case 'week':
            default :
                $res_time = 604800;
                break;

        }
        if (!empty($res_time))
            $params['start_date'] = date( 'Y-m-d H:i:s', time() - $res_time );
        // Get paginator
        $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getWhmediaPaginator($params);

        $this->view->thumb_width = 259;
        $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
        if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext()  == 'html') {
            $this->view->only_items = true;
            $this->_helper->layout->disableLayout(true);
            $this->_helper->content->setEnabled(false);
        }
        else {
            $this->view->only_items = false;
            $this->view->pageTitle = 'Popular Posts';
            $navigation = $this->view->navigation = new Zend_Navigation();

            $navigation->addPage(array(
                'label' =>  Zend_Registry::get('Zend_Translate')->_('Today'),
                'route' => 'whmedia_project_popular',
                'action' => 'popular',
                'params' => array('time_period' => 'today')
                ));	
            $navigation->addPage(array(
                'label' =>  Zend_Registry::get('Zend_Translate')->_('This week'),
                'route' => 'whmedia_project_popular',
                'action' => 'popular',
                'params' => array('time_period' => 'week')
                ));
            $navigation->addPage(array(
                'label' =>  Zend_Registry::get('Zend_Translate')->_('This month'),
                'route' => 'whmedia_project_popular',
                'action' => 'popular',
                'params' => array('time_period' => 'month')
                ));	
            $navigation->addPage(array(
                'label' =>  Zend_Registry::get('Zend_Translate')->_('Overall'),
                'route' => 'whmedia_project_popular',
                'action' => 'popular',
                'params' => array('time_period' => 'overall')
                ));
        }
  }
  
  public function livefeedAction() {
        if( !$this->_helper->requireUser()->isValid() ) return;
        $params = array('page' => $this->_getParam('page', 1),
                        'limit' => 10,
                        'is_published' => true);
        $this->view->followApi = $followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
        $viewer = Engine_Api::_()->user()->getViewer();
        $following = $followApi->getFollowing($viewer);
        $this->view->pageTitle = 'Live Feed';
        if ($following->count() > 0) {
            $params['users'] = array();
            foreach ($following as $following_one) {
                $params['users'][] = $following_one->user_id;
            }
        }
        else {
            return $this->renderScript('index/_nofollowing.tpl');
        }
        // Get paginator
        $this->view->paginator = $paginator = Engine_Api::_()->whmedia()->getWhmediaPaginator($params);

        $this->view->thumb_width = 350;
        $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
        if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext()  == 'html') {
            $this->view->only_items = true;
            $this->_helper->layout->disableLayout(true);
            $this->_helper->content->setEnabled(false);
        }
        else {
            $this->view->only_items = false;
            
        }
        $this->renderScript('index/popular.tpl');
  }
  
  public function activityfeedAction() {
        if( !$this->_helper->requireUser()->isValid() ){ 
          return $this->_helper->redirector->gotoRoute(array( 'controller' => 'login' ), 'default', true);; 
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $lists = Engine_Api::_()->getDbTable('circles', 'whmedia')->fetchAll(array('user_id = ?' => $viewer->getIdentity()));
        if (count($lists)) {
            $this->view->navigation = $navigation = new Zend_Navigation();
            $this->view->menuList = $lists;
            $viewer = Engine_Api::_()->user()->getViewer();

            $box_id = $this->_getParam('box_id');

            $navigation->addPage(array(
                    'label' =>  Zend_Registry::get('Zend_Translate')->_('All'),
                    'route' => 'whmedia_project_activityfeed',
                    'active' => (!$box_id)
                    ));	

            foreach ($lists as $box) {
                $navigation->addPage(array(
                    'label' =>  Zend_Registry::get('Zend_Translate')->_($box->getTitle()),
                    'route' => 'whmedia_project_activityfeed',
                    'params' => array('box_id' => $box->getIdentity()),
                    'active' => ($box_id == $box->getIdentity())
                ));
            }
        }
  }

  public function circlesAction(){
    if( !$this->_helper->requireUser()->isValid() ) return;      
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $lists = Engine_Api::_()->getDbTable('circles', 'whmedia')->fetchAll(array('user_id = ?' => $viewer->getIdentity()));
    $result = array();
    if (count($lists)) {
      $result = $lists->toArray();
    }

    echo json_encode($result);

    $this->_helper->layout->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
  }
  
  public function hashtagAction() {

    //Check if current viwer is a valid user
    if( !$this->_helper->requireUser->isValid() ) {
      return;
    }

    $results = array(
      'httpStatus' => 200,
      'status' => 0,
      'message' => 'Invalid method request'
    );

    if( $this->getRequest()->isPost() ) {

      $params = $this->getRequest()->getParam( 'id' );
      $viewer = Engine_Api::_()->user()->getViewer()->toArray();

      $tableFollow = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
      $dbFollow = $tableFollow->getAdapter();

      $follow[ 'condition' ]  = 'hashtag_id='. $params .' AND ';
      $follow[ 'condition' ] .= 'follower_id='. $viewer[ 'user_id' ] .'';
      $select = $tableFollow->select()->where( $follow[ 'condition' ] );
      $query = $select->query();
      $rowCount = $query->rowCount();
      $hashtag = $query->fetchAll();

      if( $rowCount == 0 ) {  
        $row              = $tableFollow->createRow();
        $row->hashtag_id  = $params;
        $row->follower_id = $viewer[ 'user_id' ];

        $row->save();

        $results  = array(
          'httpStatus'   => 200,
          'status' => 1,
          'message' => 'unfollow'
        );
      }
      else {
      
        $tableFollow->delete( 'hashtag_id='. $params  .' AND follower_id='. $viewer[ 'user_id' ] .'' );

        $results = array(
          'httpStatus' => 200,
          'status' => 1,
          'message' => 'follow'
        );
      }

    }

    $this->_helper->json( $results );

  }

  public function followingHashtagAction(){

    if( $this->getRequest()->isPost() ) {

      $tableFollow = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' ); 
      $dbFollow = $tableFollow->getAdapter();
      $viewer = Engine_Api::_()->user()->getViewer()->toArray();

      $follow = array();
      $unfollow = "(";

      foreach($this->getRequest()->getParams() as $key => $val){
        if(strpos($key, "tag_follow") !== false){
          $follow[] = $val;
        }
        if(strpos($key, "tag_unfollow") !== false){
          if($unfollow != "(") $unfollow .= ",";
          $unfollow .= mysql_real_escape_string($val);
        }
      }
      $unfollow .= ")";

      if(count($follow) != 0){
        $insertValue = "";
        foreach($follow as $key => $val){
          if($insertValue != "") $insertValue .= ",";
          $insertValue .= "(" . mysql_real_escape_string($val) . ", {$viewer['user_id']}, NOW())";
        }
        $insertQuery = "INSERT INTO " . $tableFollow->info('name') . "(hashtag_id, follower_id, creation_date) VALUES " . $insertValue; 

        $dbFollow->query($insertQuery);
      }

      if($unfollow != "()"){
        $deleteWhere = "hashtag_id IN {$unfollow} AND follower_id = {$viewer['user_id']} ";
        $tableFollow->delete($deleteWhere);
      }

    }

    $this->_helper->layout->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
  }

  public function myfollowerAction(){
    $followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
    $this->_helper->content->setEnabled(false);
    $subject_user_id = $this->getRequest()->getParam( 'subject_user_id' );

    $this->view->followers = $followApi->fetchFollowers($subject_user_id);     
    $this->view->userApi = $userApi = Engine_Api::_()->getDbtable('Users', 'User');
  }

  public function myfollowingAction(){
    $followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
    $this->_helper->content->setEnabled(false);
    $subject_user_id = $this->getRequest()->getParam( 'subject_user_id' );

    $this->view->following = $followApi->fetchFollowing($subject_user_id);
    $this->view->userApi = $userApi = Engine_Api::_()->getDbtable('Users', 'User');
    

  }

}
