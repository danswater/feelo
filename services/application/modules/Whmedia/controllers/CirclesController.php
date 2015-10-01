<?php

    class Whmedia_CirclesController extends Core_Controller_Action_User {
        
        public function indexAction() {
            $this->view->form = new Whmedia_Form_SearchCircles();
            $this->view->pageTitle = 'My Boxes';
            $this->view->count_friends = Engine_Api::_()->getDbTable('follow', 'whmedia')->getFollowingCount(Engine_Api::_()->user()->getViewer());
        }

        
        
        public function viewAction() {
            $viewer = Engine_Api::_()->user()->getViewer();
            $list_id = (int) $this->_getParam('box_id', 0);
            $page = (int) $this->_getParam('page', 1);

            $usersTable = Engine_Api::_()->getDbTable('users', 'user');
            $usersTableName = $usersTable->info('name');
            
            $listsTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
            $listsTableName = $listsTable->info('name');
            
            $listitemsTable = Engine_Api::_()->getDbTable('circleitems', 'whmedia');
            $listitemsTableName = $listitemsTable->info('name');

            $this->view->list = $list = $listsTable->find($list_id)->current();
            if( !$list || $list->user_id != $viewer->getIdentity() ) {
                return $this->_forward('requireauth', 'error', 'core');
            }

            // Contruct query
            $select = $usersTable->select()
              //->setIntegrityCheck(false)
              ->from($usersTableName)
              ->joinLeft($listitemsTableName, "`{$listitemsTableName}`.`user_id` = `{$usersTableName}`.`user_id`", null)              
              ->where("{$listitemsTableName}.circle_id = ?", $list_id)
              ->order("{$usersTableName}.displayname ASC")
            ;
              $t = (string)$select;
            // Build paginator
            $this->view->users = $paginator = Zend_Paginator::factory($select);
            $this->view->totalUsers = $paginator->getTotalItemCount();
            $paginator->setItemCountPerPage(20);
            $paginator->setCurrentPageNumber($page);    
        }

        public function createAction() {
            // Generate and assign form
            $form = $this->view->form = new Whmedia_Form_Circle();

            // Check post
            if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
            {
              // we will add the category
              $values = $form->getValues();

              $db = Engine_Db_Table::getDefaultAdapter();
              $db->beginTransaction();

              try
              {
                // add category to the database
                // Transaction
                $table = Engine_Api::_()->getDbtable('circles', 'whmedia');

                $row = $table->createRow($values);
                $row->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                $box_id = $row->save();

                $db->commit();
                
                // ** auto add if the box id exist ** 
                $addnew = (int) $this->_getParam('addnew' , false);
                $_subject = Engine_Api::_()->user()->getUser($addnew);   
                if( $_subject->getIdentity() )
                {
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $listsTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
                    $box = $listsTable->fetchRow(array('user_id = ?' => $viewer->getIdentity(),
                                                       'circle_id = ?' => $box_id ));

                    $box->add($_subject);
                }
                // ** auto add if the box id exist ** 
              }

              catch( Exception $e )
              {
                $db->rollBack();                
                throw $e;
              }

              return $this->_helper->Message("Box added.");
            }

            // Output
            $this->renderScript('admin-settings/form.tpl');
        }
        
        public function getFriendsAction() {
            $usersTable = Engine_Api::_()->getDbTable('users', 'user');
            $usersTableName = $usersTable->info('name');
            $membershipTable = Engine_Api::_()->getDbTable('follow', 'whmedia');
            $membershipTableName = $membershipTable->info('name');
            // Don't render this if not authorized
            $viewer = Engine_Api::_()->user()->getViewer();

            $request = Zend_Controller_Front::getInstance()->getRequest();
            if($request->isPost()){
                $postdata = $request->getPost();
                if(!empty($postdata['user_id'])){
                    $list_id = (int) $this->_getParam('circle');
                    $friend_id = (int) $this->_getParam('user_id');

                    $user = $viewer;
                    $friend = Engine_Api::_()->getItem('user', $friend_id);

                    $status = true;
                    $message = '';
                    if( !$user->getIdentity() || empty($friend) || empty($list_id) )
                    {
                      $status = false;
                      $error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
                    }

                    // Check list
                    $listTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
                    $list = $listTable->find($list_id)->current();
                    if( !$list || $list->user_id != $user->getIdentity() ) {
                      $status = false;
                      $error = Zend_Registry::get('Zend_Translate')->_('Missing list/not authorized');
                    }

                    if($postdata['action'] == 'add' && $status){
                        // Check if already target status
                        if( $list->has($friend) )
                        {
                          $status = false;
                          $error = Zend_Registry::get('Zend_Translate')->_('Already in list');
                        }
                    }else{
                        // Check if already target status
                        if( !$list->has($friend) )
                        {
                          $this->view->status = false;
                          $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already not in list');
                          return;
                        }
                    }

                    if($postdata['action'] == 'add' && $status){
                        $list->add($friend);
                        $message = Zend_Registry::get('Zend_Translate')->_('Member added to list.');
                    }elseif($postdata['action'] == 'remove' && $status){
                        $list->remove($friend);
                        $message = Zend_Registry::get('Zend_Translate')->_('Member removed from list.');
                    }
                }
            }

            $result = array();
            //General Friend settings
            $this->view->make_list = Engine_Api::_()->getApi('settings', 'core')->user_friends_lists;

            // Don't render this if friendships are disabled
            if (!Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible) {
                return;
            }

            // Multiple friend mode
            $select = $usersTable->select();
            $select->from($membershipTableName, array())
                   ->setIntegrityCheck(false) 
                   ->joinLeftUsing ($usersTableName, "user_id")
                   ->where('follower_id = ?', $viewer->getIdentity()) ;

            $displayname = $this->_getParam('displayname', '');

            // Add displayname
            if( !empty($displayname) ) {
              $select->where("(`{$usersTableName}`.`username` LIKE ? || `{$usersTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
            }
            $t = (string)$select;
            $this->view->friends = $followings = $paginator = Zend_Paginator::factory($select);

            // Set item count per page and current page number
            $totalUsers = $paginator->getTotalItemCount();
            $paginator->setItemCountPerPage(20);
            $paginator->setCurrentPageNumber($this->_getParam('page', 1));
            $result['pagination_control'] = $this->view->paginationControl($paginator, null, array("pagination/circlesPagination.tpl","whmedia"));

            // Get stuff
            $ids = array();
            $friends_array = array();
            $index = 0;
            foreach ($followings as $friend_user) {
                $ids[] = $friend_user->getIdentity();                
                $user_metaname = 'user'.$index;
                $friends_array[$user_metaname]['index'] = $index;
                $friends_array[$user_metaname]['user_id'] = $friend_user->getIdentity();
                $friends_array[$user_metaname]['name'] = $friend_user->getTitle();
                $photo_url = $friend_user->getPhotoUrl('thumb.icon');
                if(!$photo_url){
                    $photo_url = $this->view->getHelper('itemPhoto')->getNoPhoto($friend_user, 'thumb.icon');
                }
                $friends_array[$user_metaname]['userpic'] = $photo_url;
                $friends_array[$user_metaname]['profile'] = $friend_user->username;
                $index++;
            }
            $result['error'] = "0";
            $result['friends'] = $friends_array;

            // Set item count per page and current page number
            $paginator->setItemCountPerPage($totalUsers);

            // Get stuff
            $ids = array();
            $allfriends_array = array();
            $index = 0;
            foreach ($followings as $allfriend_user) {
                $ids[] = $allfriend_user->getIdentity();
                $user_metaname = 'user'.$index;
                $allfriends_array[$user_metaname]['index'] = $index;
                $allfriends_array[$user_metaname]['user_id'] = $allfriend_user->getIdentity();
                $allfriends_array[$user_metaname]['name'] = $allfriend_user->getTitle();
                $photo_url = $allfriend_user->getPhotoUrl('thumb.icon');
                if(!$photo_url){
                    $photo_url = $this->view->getHelper('itemPhoto')->getNoPhoto($allfriend_user, 'thumb.icon');
                }
                $allfriends_array[$user_metaname]['userpic'] = $photo_url;
                $allfriends_array[$user_metaname]['profile'] = $allfriend_user->username;
                $index++;
            }
            $result['error'] = "0";
            $result['allfriends'] = $allfriends_array;

            $this->view->lists = $lists = Engine_Api::_()->getDbTable('circles', 'whmedia')->fetchAll(array('user_id = ?' => $viewer->getIdentity()));
            $listItemTable = Engine_Api::_()->getDbTable('circleitems', 'whmedia');
            $circles_array = array();
            $circles_index = 0;
            $listIds = array();
            foreach ($lists as $list) {
                $listIds[] = $list->getIdentity();
                $circles_array['group'.$list->getIdentity()]['index'] = $circles_index;
                $circles_array['group'.$list->getIdentity()]['name'] = $list->title;

                $listItems = array();
                $listsByUser = array();
                $circles_array_members = array();
                $listItemSelect = $listItemTable->select()->where('circle_id = ?', $list->getIdentity())->where('user_id IN(?)', $ids);
                $listItems = $listItemTable->fetchAll($listItemSelect);
                foreach ($listItems as $listItem) {
                    $listsByUser[] = $listItem->user_id;
                }
                foreach ($followings as $friend) {
                    if(in_array($friend->getIdentity(), $listsByUser)){
                        $circles_array_members[] = 1;
                    }else{
                        $circles_array_members[] = 0;
                    }
                }
                $circles_array['group'.$list->getIdentity()]['members'] = $circles_array_members;

                $circles_index++;
            }

            $result['circles'] = $circles_array;
            print_r(Zend_Json::encode($result));die();
        }
        
        public function removeAction() {
            $viewer = Engine_Api::_()->user()->getViewer();
            $list_id = (int) $this->_getParam('box_id');
            $user_id = (int) $this->_getParam('user_id');
            $this->view->delete_title = 'Remove user?';
            $this->view->delete_description = 'Are you sure that you want to remove this user from box?';
            $this->view->button = 'Remove';
            // Check method/data validate
            if( $this->getRequest()->isPost() ) {
                           
                // Check list
                $circlesTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
                $list = $circlesTable->find($list_id)->current();
                $friend = Engine_Api::_()->getItem('user', $user_id);
                if( !$list || $list->user_id != $viewer->getIdentity() ) {
                  return $this->_helper->Message('Missing box.', false, false)->setError();
                }

                if (!$friend->getIdentity()) {
                    return $this->_helper->Message('Missing user.', false, false)->setError();                 
                }

  
                // Check if already target status
                if( !$list->has($friend) )
                {
                  return $this->_helper->Message('This user isn`t in this box', false, false)->setError();  
                }

                $list->remove($friend);
                return $this->_helper->Message("User has been removed from box.");
            }
            $this->renderScript('etc/delete.tpl');
        }
        
        public function editAction() {
            // Generate and assign form
            $form = $this->view->form = new Whmedia_Form_Circle();
            $form->submit->setLabel('Edit Box');
            $list_id = (int) $this->_getParam('box_id');
            $circlesTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
            $viewer = Engine_Api::_()->user()->getViewer();
            /* @var $list Whmedia_Model_Circle */
            $list = $circlesTable->find($list_id)->current();
            $form->populate($list->toArray());
            
            // Check post
            if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
            {
              if( !$list || $list->user_id != $viewer->getIdentity() ) {
                return $this->_helper->Message('Missing box.', false, false)->setError();
              }  
              // we will add the category
              $values = $form->getValues();

              $db = Engine_Db_Table::getDefaultAdapter();
              $db->beginTransaction();

              try
              {
                
                $list->setFromArray($values);
                $list->save();

                $db->commit();
              }

              catch( Exception $e )
              {
                $db->rollBack();                
                throw $e;
              }

              return $this->_helper->Message("Changes saved.");
            }

            // Output
            $this->renderScript('admin-settings/form.tpl');
        }
        
        public function deleteAction() {
            $viewer = Engine_Api::_()->user()->getViewer();
            $list_id = (int) $this->_getParam('box_id');
            $this->view->delete_title = 'Delete box?';
            $this->view->delete_description = 'Are you sure that you want to delete this box?';
            
            // Check post
            if( $this->getRequest()->isPost() )
            {
              $circlesTable = Engine_Api::_()->getDbTable('circles', 'whmedia');  
              $list = $circlesTable->find($list_id)->current();  
              if( !$list || $list->user_id != $viewer->getIdentity() ) {
                return $this->_helper->Message('Missing box.', false, false)->setError();
              }  
              
              $list->delete();
              return $this->_helper->Message("Box deleted.");
            }

            // Output
            $this->renderScript('etc/delete.tpl');
        }


        public function favboxesAction(){
            $page = (int) $this->_getParam('page', 1);
            $this->view->pageTitle = 'My Favorite Box';
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();


            $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
            $favcircleName = $favcircleTable->info('name');
            $select = $favcircleTable->select()
              ->from($favcircleName)
              ->where("{$favcircleName}.user_id = ?", $user_id)
              ->order("{$favcircleName}.favcircle_id desc");


            $paginator = Zend_Paginator::factory($select);
            $paginator->setItemCountPerPage(20);
            $paginator->setCurrentPageNumber($page); 

            $filterData = array();
            foreach ($paginator as $paging) {

                $storagePhoto = Engine_Api::_()->getItem('storage_file', $paging->photo_id);
                $children = $storagePhoto->getChildren();
                $photoArray = array();
                foreach($children as $child){
                    $photoArray[$child["type"]] = $child["storage_path"];
                }

                $filterData[] = array(
                    "photos" => $photoArray,
                    "title" => $paging->title,
                    "category" => $paging->category,
                    "favcircle_id" => $paging->favcircle_id
                );
            }
            $this->view->favcircle = $filterData;
            $this->view->pagination = $paginator;

        }

        public function addfavprojectAction(){
            $page = (int) $this->_getParam('page', 1);
            $project_id = (int) $this->_getParam('project_id', 0);
            $favcircle_id = (int) $this->_getParam('favcircle_id', 0);

            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
            $favcircleName = $favcircleTable->info('name');
            $select = $favcircleTable->select()
              ->from($favcircleName)
              ->where("{$favcircleName}.user_id = ?", $user_id)
              ->order("{$favcircleName}.favcircle_id desc");

            $paginator = Zend_Paginator::factory($select);
            $paginator->setItemCountPerPage(100);
            $paginator->setCurrentPageNumber($page); 

            $this->view->favcircle = $paginator;
            $this->view->project_id = $project_id; 

            $project = Engine_Api::_()->getItem('whmedia_project', 1000);

            $ret = $this->addProjectToFavList($project_id, $favcircle_id);

            if( $ret ) 
                echo '<script type="text/javascript">parent.Smoothbox.close(); </script>';
            else if( $favcircle_id == 0 && $this->getRequest()->isPost() )
                $this->view->errorMsg = "Please select favo box.";
            
        }

        public function delfavprojectAction(){
            $project_id =  (int) $this->_getParam('project_id', false);
            $favcircle_id =  (int) $this->_getParam('favcircle_id', false);
            $viewer = Engine_Api::_()->user()->getViewer();

            $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
            $cfavcircle = $favcircleTable->find($favcircle_id)->current();  


            if( !$cfavcircle || $cfavcircle->user_id != $viewer->getIdentity() ) {
                 return $this->_helper->redirector->gotoRoute(array('controller' => 'whmedia',
                            'action' => 'activity-feed'), 'whmedia_default', true);
            }  

            $favcircleTable = Engine_Api::_()->getDbTable('favcircleitems', 'whmedia');
            $favcircleitemName = $favcircleTable->info('name');
            $select = $favcircleTable->select()
                ->from($favcircleitemName)
                ->where("{$favcircleitemName}.project_id = ?", $project_id)
                ->where("{$favcircleitemName}.favcircle_id = ?", $favcircle_id)
                ->where("{$favcircleitemName}.user_id = ?", $viewer->getIdentity()); 


            $result = $favcircleTable->fetchAll($select)->toArray();

            if(count($result) !== 0){
                $favcircleProjectTable = Engine_Api::_()->getDbTable('favcircleitems', 'whmedia');
                $list = $favcircleProjectTable->find($result[0]["favcircleitem_id"])->current(); 
                $list->delete(); 
            }
            
            $this->_helper->redirector->gotoRoute(array("controller"=>"favboxes", "action" => "favprojectlist","favcircle_id" => $favcircle_id), 'default', true);

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

        }

        public function favprojectlistAction(){
            $favcircle_id = (int) $this->_getParam('favcircle_id', false);
            $viewer = Engine_Api::_()->user()->getViewer();

            $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
            $cfavcircle = $favcircleTable->find($favcircle_id)->current();  

            if( !$cfavcircle || $cfavcircle->user_id != $viewer->getIdentity() ) {
                 return $this->_helper->redirector->gotoRoute(array('controller' => 'whmedia',
                            'action' => 'activity-feed'), 'whmedia_default', true);
            }  

            $select = Engine_Api::_()->getDbTable('favcircleitems', 'whmedia')->favcircleProjects($viewer,  $favcircle_id);

            $this->view->paginator = $paginator = Zend_Paginator::factory($select);
            $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 20));
            $pageNumber = $this->_getParam('page', 1);
            $paginator->setCurrentPageNumber($pageNumber);

            $this->view->thumb_width = (int)$this->_getParam('thumb_width', 160);
            $this->view->favcircle_id = $favcircle_id;
 



            //echo $select->assemble();

            /*
            $page = (int) $this->_getParam('page', 1);
            $favcircle_id = (int) $this->_getParam('favcircle_id', false);

            $favcircleTable = Engine_Api::_()->getDbTable('favcircleitems', 'whmedia');
            $favcircleitemName = $favcircleTable->info('name');
            
            $projectTable = Engine_Api::_()->getDbTable('projects', 'whmedia');
            $projectName = $projectTable->info('name');
            
            $select = new Zend_Db_Select(Engine_Db_Table::getDefaultAdapter());

            $select->from(array("a" => $favcircleitemName))
                ->joinLeft(array("b" => $projectName), "a.project_id=b.project_id", array('b.*'))
                ->where("a.favcircle_id = ?", $favcircle_id)
                ->where("a.user_id = ?",  Engine_Api::_()->user()->getViewer()->getIdentity());
            
            $paginator = Zend_Paginator::factory($select);
            $paginator->setItemCountPerPage(100);
            $paginator->setCurrentPageNumber($page); 
            */
 

        }

        public function createfavboxAction(){
            $reload = $this->_getParam("rel", false);
            $project_id = (int) $this->_getParam('project_id', false);
            $favcircle_id = (int) $this->_getParam('favid', false);
            $private_circle = (int) $this->_getParam("privacy", false);
            $viewer = Engine_Api::_()->user()->getViewer();
            $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
            $cfavcircle = $favcircleTable->find($favcircle_id)->current();  
            $authorize = true;
            if( !$cfavcircle || $cfavcircle->user_id != $viewer->getIdentity() ) {
                $authorize = false;
            }  


            if( $this->getRequest()->isPost() || $authorize) {

                $file_id =  $this->_getParam('file_id', "");

                if($authorize && $file_id == ""){
                    $file_id = $cfavcircle->photo_id;
                }
                if($authorize && $private_circle == false){
                    $this->view->isprivate = $cfavcircle->private;
                }else if($private_circle != false){
                     $this->view->isprivate = $private_circle;
                }

                if($file_id != ""){
                    $storagePhoto = Engine_Api::_()->getItem('storage_file', $file_id);
                    $children = $storagePhoto->getChildren();

                    $arrayFilter = array("file_id" => $file_id);
                    foreach($children as $child){
                        $arrayFilter[$child["type"]] = $child["storage_path"];
                    }
                    $this->view->photo =  $arrayFilter;
                }
                $post = array("boxname" => "title", "privacy" => "private", "category" => "category");
                $filtPost = array();
                $errorMsg = "";
                foreach ($post as $key => $dbField) {
                    $val = $this->_getParam($key, "");
                    if($authorize && $val == ""){
                        $val = $cfavcircle->$dbField;
                    }
                    if($val === ""){
                        $errorMsg = ucfirst($key) . " is empty";
                        break;
                    }
                    $this->view->$key = $val; 
                    $filtPost[$dbField] = $val;
                }

                if($file_id == ""){
                    $errorMsg = "Cover Photo is empty";
                }

                if($errorMsg != ""){
                    $this->view->errorMsg = $errorMsg;
                }

                $saving = $this->_getParam("saving", "");

                if($errorMsg == "" && $saving != ""){
                    $db = Engine_Db_Table::getDefaultAdapter();
                    $db->beginTransaction();
                    try
                    {
                        if($authorize){
                            $filtPost["photo_id"] = $file_id ;
                            $cfavcircle->setFromArray($filtPost);
                            $cfavcircle->save();
                        }else{
                            $table = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
                            $row = $table->createRow($filtPost);
                            $row->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                            $row->photo_id = $file_id;
                            $favcircle_id = $row->save();
                        }
                        $db->commit();
                        $this->addProjectToFavList($project_id, $favcircle_id);
                        if($reload == false)
                            echo '<script type="text/javascript"> parent.Smoothbox.close(); </script>';
                        else
                            echo '<script type="text/javascript"> parent.location.reload(); </script>';
                    }catch( Exception $e ){
                        $db->rollBack();                
                        throw $e;
                    }
                }
            }

            $coverForm = new Whmedia_Form_CoverPhoto();
            $this->view->form = $coverForm->coverPhotoForm();
        }

        public function addProjectToFavList($project_id, $box_id){
            if($box_id != false && $this->getRequest()->isPost() && null !== ($project = Engine_Api::_()->getItem('whmedia_project', $project_id))){

                $viewer = Engine_Api::_()->user()->getViewer();
                $project = Engine_Api::_()->getItem('whmedia_project', $project_id);
                $projectOwner = Engine_Api::_()->getItemTable('user')->fetchRow(array(
                                'user_id = ?' => $project->user_id,
                            ));



                //echo $viewer->getIdentity() . " " . $project->user_id;

                $favcircleTable = Engine_Api::_()->getDbTable('favcircleitems', 'whmedia');
                $favcircleitemName = $favcircleTable->info('name');
                $select = $favcircleTable->select()
                  ->from($favcircleitemName)
                  ->where("{$favcircleitemName}.project_id = ?", $project_id)
                  ->where("{$favcircleitemName}.favcircle_id = ?", $box_id)
                  ->where("{$favcircleitemName}.user_id = ?", $viewer->getIdentity());

                if( count($favcircleTable->fetchAll($select)->toArray()) === 0){
                    $db = Engine_Db_Table::getDefaultAdapter();
                    $db->beginTransaction();
                    try{
                        $table = Engine_Api::_()->getDbTable('favcircleitems', 'whmedia');
                        $row = $table->createRow(array(
                            "project_id" => $project_id,
                            "favcircle_id" => $box_id
                        ));
                        $row->user_id = $viewer->getIdentity();
                        $row->save();
                        $db->commit();
                        
                        $followfavTable = Engine_Api::_()->getDbTable('followfav', 'whmedia');
                        $followfavName = $followfavTable->info('name');

                        $select = $followfavTable->select()
                            ->from($followfavName)
                            ->where("{$followfavName}.user_id = ?", $viewer->getIdentity())
                            ->where("{$followfavName}.favcircle_id = ?",  $box_id);
                         
                        $followers = $followfavTable->fetchAll($select)->toArray();

                        foreach($followers as $follower){
                            $followerUser = Engine_Api::_()->getItemTable('user')->fetchRow(array(
                                'user_id = ?' => $follower["follower_id"],
                            ));

                            if($followerUser != null){
                                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($followerUser, $viewer, $project, "follower_favo", array(
                                    "favcircle_id" => $box_id
                                ));
                            }
                        }
                     
                        
                    }catch( Exception $e ){
                        $db->rollBack();                
                        throw $e;
                    }   
                }
                return true;
            }
            return false;
    
        }

        public function menprojectlistAction(){
            $favcircle_id = (int) $this->_getParam('favcircle_id', false);
            $viewer = Engine_Api::_()->user()->getViewer();

            $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
            $cfavcircle = $favcircleTable->find($favcircle_id)->current();  

            $subjectUser = Engine_Api::_()->getItem('user', $cfavcircle->user_id);

            $select = Engine_Api::_()->getDbTable('favcircleitems', 'whmedia')->favcircleProjects($viewer,  $favcircle_id);

            $this->view->favcircle = $cfavcircle;
            $this->view->userCircle = $subjectUser;


            // if already followed

            $followfavTable = Engine_Api::_()->getDbTable('followfav', 'whmedia');
            $followfavName = $followfavTable->info('name');
            

            $followedSelect = $followfavTable->select()
                ->from($followfavName)
                ->where("{$followfavName}.user_id = ?", $cfavcircle->user_id)
                ->where("{$followfavName}.favcircle_id = ?", $cfavcircle->favcircle_id)
                ->where("{$followfavName}.follower_id = ?", $viewer->getIdentity());
            
            $followed = false;      
            if( count($followfavTable->fetchAll($followedSelect)->toArray()) === 0){    
                $followed = true;
            }

            $this->view->followed = $followed;
            // if already followed


            $this->view->paginator = $paginator = Zend_Paginator::factory($select);
            $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 20));
            $pageNumber = $this->_getParam('page', 1);
            $paginator->setCurrentPageNumber($pageNumber);


            $this->view->thumb_width = (int)$this->_getParam('thumb_width', 259);
            $this->view->favcircle_id = $favcircle_id;

            $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
            $this->view->sendScript = ($pageNumber > 1) ? false : true;

            if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext()  == 'html') {
                $this->view->only_items = true;
            }
            else {
                $this->view->only_items = false;
            }
            $this->view->identity = 645;

            if($cfavcircle->user_id != $viewer->getIdentity())
                $this->view->hasFollowed = true;
          
        }

        public function deletefavboxAction(){
            $favcircle_id = (int) $this->_getParam('fid', "");
            $viewer = Engine_Api::_()->user()->getViewer();
            $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
            $list = $favcircleTable->find($favcircle_id)->current();  

            $authorize = true;
            if( !$list || $list->user_id != $viewer->getIdentity() ) {
                $authorize = false;
            }  
            if($authorize){
                $list->delete();
            }
            $this->_helper->redirector->gotoRoute(array("controller"=>"boxes", "action" => "favboxes"), 'default', true);
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
        }


        public function followfavAction(){
            $favcircle_id = (int) $this->_getParam('favcircle_id', false);
            $favu_id = (int) $this->_getParam('favuser', false);

            if($favcircle_id === false && $favu_id === false) return;

            $followfavTable = Engine_Api::_()->getDbTable('followfav', 'whmedia');
            $followfavName = $followfavTable->info('name');
            $select = $followfavTable->select()
                ->from($followfavName)
                ->where("{$followfavName}.user_id = ?", $favu_id)
                ->where("{$followfavName}.favcircle_id = ?", $favcircle_id)
                ->where("{$followfavName}.follower_id = ?", Engine_Api::_()->user()->getViewer()->getIdentity());
            $result = $followfavTable->fetchAll($select)->toArray();
            if( count($result) === 0){
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try{
                    $table = Engine_Api::_()->getDbTable('followfav', 'whmedia');
                    $row = $table->createRow(array(
                        "user_id" => $favu_id,
                        "favcircle_id" => $favcircle_id
                    ));
                    $row->follower_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                    $row->save();
                    $db->commit();

                    $viewer = Engine_Api::_()->user()->getViewer();
                    $dummyProject = Engine_Api::_()->getItem('whmedia_project', 1);
                    $projectOwner = Engine_Api::_()->getItemTable('user')->fetchRow(array(
                                'user_id = ?' => $favu_id,
                    ));

                    // owner
                    if( $viewer->getIdentity() != $projectOwner->getIdentity()){
                        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($projectOwner, $viewer, $dummyProject, "followed_favo", array(
                            "favcircle_id" => $favcircle_id,
                        ));
                   }

                }catch( Exception $e ){
                    $db->rollBack();                
                    throw $e;
                } 
            }else if( count($result) !== 0){
                $followfavTable = Engine_Api::_()->getDbTable('followfav', 'whmedia');
                $list = $followfavTable->find($result[0]["followfav_id"])->current(); 
                $list->delete(); 
            }

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
        }

        public function favphotoAction(){

            if( !$this->getRequest()->isPost() ) {
              return;
            }

            $coverForm = new Whmedia_Form_CoverPhoto();
            $form = $coverForm->coverPhotoForm();
                
            if(!$form->isValid($this->getRequest()->getParams()))
                return;
            
            if(!$form->cover_photo->receive())
                return;

            if($form->cover_photo->isUploaded()){
                $values = $form->getValues();
                $fileName = $form->cover_photo->getFileName();

                $userIdentity = Engine_Api::_()->user()->getViewer()->getIdentity();
                $params = array(
                  'parent_type' => "favphoto",
                  'parent_id' => $userIdentity,
                  'user_id' => $userIdentity,
                  'name' => basename($fileName),
                );

                $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

                $extension = ltrim(strrchr(basename($fileName), '.'), '.');
                $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
                $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

                // main
                $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
                $image = Engine_Image::factory();
                $image->open($fileName)
                  ->resize(720, 720)
                  ->write($mainPath)
                  ->destroy();

                // profile thumb
                $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
                $image = Engine_Image::factory();
                $image->open($fileName)
                  ->resize(200, 400)
                  ->write($profilePath)
                  ->destroy();


                // cover_photo
                $coverPhotoPath = $path . DIRECTORY_SEPARATOR . $base . '_cp.' . $extension;
                $image = Engine_Image::factory();
                $image->open($fileName)
                  ->resize(80, 80)
                  ->write($coverPhotoPath)
                  ->destroy();


                // icon image
                $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
                $image = Engine_Image::factory();
                $image->open($fileName);

                $size = min($image->height, $image->width);
                $x = ($image->width - $size) / 2;
                $y = ($image->height - $size) / 2;

                $image->resample($x, $y, $size, $size, 48, 48)
                  ->write($squarePath)
                  ->destroy();  

                $iMain = $filesTable->createFile($mainPath, $params);
                $iProfile = $filesTable->createFile($profilePath, $params);
                $iCoverPhoto = $filesTable->createFile($coverPhotoPath, $params);
                $iSquare = $filesTable->createFile($squarePath, $params);

                $iMain->bridge($iProfile, 'thumb');
                $iMain->bridge($iSquare, 'icon');
                $iMain->bridge($iCoverPhoto, 'cover');

                @unlink($mainPath);
                @unlink($profilePath);
                @unlink($squarePath);

                $storagePhoto = Engine_Api::_()->getItem('storage_file', $iMain->file_id);
                $children = $storagePhoto->getChildren();

                $arrayFilter = array("file_id" => $iMain->file_id);
                foreach($children as $child){
                    $arrayFilter[$child["type"]] = $child["storage_path"];
                }
                echo json_encode($arrayFilter);
          
            }

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
        }

    }
?>
