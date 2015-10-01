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
        if (!$list || $list->user_id != $viewer->getIdentity()) {
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
        $t = (string) $select;
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
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            // we will add the category
            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                // add category to the database
                // Transaction
                $table = Engine_Api::_()->getDbtable('circles', 'whmedia');

                $row = $table->createRow($values);
                $row->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                $row->save();

                if ($id = $this->_getParam('id')) {
                    $row->add(Engine_Api::_()->user()->getUser($id));
                }

                $db->commit();
            } catch (Exception $e) {
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
        if ($request->isPost()) {
            $postdata = $request->getPost();
            if (!empty($postdata['user_id'])) {
                $list_id = (int) $this->_getParam('circle');
                $friend_id = (int) $this->_getParam('user_id');

                $user = $viewer;
                $friend = Engine_Api::_()->getItem('user', $friend_id);

                $status = true;
                $message = '';
                if (!$user->getIdentity() || empty($friend) || empty($list_id)) {
                    $status = false;
                    $error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
                }

                // Check list
                $listTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
                $list = $listTable->find($list_id)->current();
                if (!$list || $list->user_id != $user->getIdentity()) {
                    $status = false;
                    $error = Zend_Registry::get('Zend_Translate')->_('Missing list/not authorized');
                }

                if ($postdata['action'] == 'add' && $status) {
                    // Check if already target status
                    if ($list->has($friend)) {
                        $status = false;
                        $error = Zend_Registry::get('Zend_Translate')->_('Already in list');
                    }
                } else {
                    // Check if already target status
                    if (!$list->has($friend)) {
                        $this->view->status = false;
                        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already not in list');
                        return;
                    }
                }

                if ($postdata['action'] == 'add' && $status) {
                    $list->add($friend);
                    $message = Zend_Registry::get('Zend_Translate')->_('Member added to list.');
                } elseif ($postdata['action'] == 'remove' && $status) {
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
                ->joinLeftUsing($usersTableName, "user_id")
                ->where('follower_id = ?', $viewer->getIdentity());

        $displayname = $this->_getParam('displayname', '');

        // Add displayname
        if (!empty($displayname)) {
            $select->where("(`{$usersTableName}`.`username` LIKE ? || `{$usersTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
        }
        $t = (string) $select;
        $this->view->friends = $followings = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $totalUsers = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage(20);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $result['pagination_control'] = $this->view->paginationControl($paginator, null, array("pagination/circlesPagination.tpl", "whmedia"));

        // Get stuff
        $ids = array();
        $friends_array = array();
        $index = 0;
        foreach ($followings as $friend_user) {
            $ids[] = $friend_user->getIdentity();
            $user_metaname = 'user' . $index;
            $friends_array[$user_metaname]['index'] = $index;
            $friends_array[$user_metaname]['user_id'] = $friend_user->getIdentity();
            $friends_array[$user_metaname]['name'] = $friend_user->getTitle();
            $photo_url = $friend_user->getPhotoUrl('thumb.icon');
            if (!$photo_url) {
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
            $user_metaname = 'user' . $index;
            $allfriends_array[$user_metaname]['index'] = $index;
            $allfriends_array[$user_metaname]['user_id'] = $allfriend_user->getIdentity();
            $allfriends_array[$user_metaname]['name'] = $allfriend_user->getTitle();
            $photo_url = $allfriend_user->getPhotoUrl('thumb.icon');
            if (!$photo_url) {
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
            $circles_array['group' . $list->getIdentity()]['index'] = $circles_index;
            $circles_array['group' . $list->getIdentity()]['name'] = $list->title;

            $listItems = array();
            $listsByUser = array();
            $circles_array_members = array();
            $listItemSelect = $listItemTable->select()->where('circle_id = ?', $list->getIdentity())->where('user_id IN(?)', $ids);
            $listItems = $listItemTable->fetchAll($listItemSelect);
            foreach ($listItems as $listItem) {
                $listsByUser[] = $listItem->user_id;
            }
            foreach ($followings as $friend) {
                if (in_array($friend->getIdentity(), $listsByUser)) {
                    $circles_array_members[] = 1;
                } else {
                    $circles_array_members[] = 0;
                }
            }
            $circles_array['group' . $list->getIdentity()]['members'] = $circles_array_members;

            $circles_index++;
        }

        $result['circles'] = $circles_array;
        print_r(Zend_Json::encode($result));
        die();
    }

    public function removeAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $list_id = (int) $this->_getParam('box_id');
        $user_id = (int) $this->_getParam('user_id');
        $this->view->delete_title = 'Remove user?';
        $this->view->delete_description = 'Are you sure that you want to remove this user from box?';
        $this->view->button = 'Remove';
        // Check method/data validate
        if ($this->getRequest()->isPost()) {

            // Check list
            $circlesTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
            $list = $circlesTable->find($list_id)->current();
            $friend = Engine_Api::_()->getItem('user', $user_id);
            if (!$list || $list->user_id != $viewer->getIdentity()) {
                return $this->_helper->Message('Missing box.', false, false)->setError();
            }

            if (!$friend->getIdentity()) {
                return $this->_helper->Message('Missing user.', false, false)->setError();
            }


            // Check if already target status
            if (!$list->has($friend)) {
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
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            if (!$list || $list->user_id != $viewer->getIdentity()) {
                return $this->_helper->Message('Missing box.', false, false)->setError();
            }
            // we will add the category
            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                $list->setFromArray($values);
                $list->save();

                $db->commit();
            } catch (Exception $e) {
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
        if ($this->getRequest()->isPost()) {
            $circlesTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
            $list = $circlesTable->find($list_id)->current();
            if (!$list || $list->user_id != $viewer->getIdentity()) {
                return $this->_helper->Message('Missing box.', false, false)->setError();
            }

            $list->delete();
            return $this->_helper->Message("Box deleted.");
        }

        // Output
        $this->renderScript('etc/delete.tpl');
    }

}

?>
