<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: CommentController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Whcomments_CommentController extends Core_Controller_Action_Standard {

    public function init() {
        $type = $this->_getParam('type');
        $identity = $this->_getParam('id');
        if ($type && $identity) {
            $item = Engine_Api::_()->getItem($type, $identity);
            if ($item instanceof Core_Model_Item_Abstract && (method_exists($item, 'comments') || method_exists($item, 'likes')))
                if (!Engine_Api::_()->core()->hasSubject())
                    Engine_Api::_()->core()->setSubject($item);
        }
        $this->_helper->requireSubject();
    }

    public function listAction() {
        $page = (int) $this->_getParam('page', 0);
        $page++;
        $comment_limit = 10;

        $page_render = ($comment_limit * $page);


        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();

        // Perms
        $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
        $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

        // Likes
        $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
        $this->view->likes = $likes = $subject->likes()->getLikePaginator();

        // Comments
        $commentsTable = Engine_Api::_()->getDbTable('comments', 'whcomments');
        $commentSelect = $commentsTable->getCommentSelect($subject);
        $commentSelect->order('lt DESC');
        $commentSelect->where("node.deleted = ?", "0");
        $commentSelect->where("node.parent_id IS NULL");
        $tree = $commentsTable->fetchTree($subject, null, $commentSelect);

        $this->view->comments = $paginator = Zend_Paginator::factory($tree);
        $this->view->total_comments = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($page_render);
        $paginator->setCurrentPageNumber(1); 

        $treeComment = array();
        $pageLimit = $paginator->getTotalItemCount() > $page_render ? $page_render : $paginator->getTotalItemCount() ; 
        foreach($paginator as $paging){
            $treeComment[ ( $pageLimit - 1) ] = $paging->toArray();
             $pageLimit--;
        }



        $this->view->comments = $treeComment;
        $this->view->comment_limit = $page_render;
        $this->view->comment_type = $subject->getType();
        $this->view->comment_id = $subject->getIdentity();
        $this->view->comment_page = $page;

        if ($viewer->getIdentity() && $canComment) {
            $this->view->form = $form = new Whcomments_Form_Comment_Create();
            $form->populate(array(
                'identity' => $subject->getIdentity(),
                'type' => $subject->getType(),
            ));
        }
    }

    public function createAction() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        $this->view->form = $form = new Core_Form_Comment_Create();

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");
            ;
            return;
        }

        if (!$form->isValid($this->_getAllParams())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
            return;
        }

        // Process
        // Filter HTML
        $filter = new Zend_Filter();
        $filter->addFilter(new Engine_Filter_Censor());
        $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

        $body = $form->getValue('body');
        $body = $filter->filter($body);

        if($body == ""){
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Empty comment");
            return;
        }

        switch ($body) {
            case 'happy':
                $body = '<img src="application/modules/Whcomments/externals/images/happy.png" alt="Happy" /> HAPPY';
                break;
            case 'nice':
                $body = '<img src="application/modules/Whcomments/externals/images/nice.png" alt="Nice" /> NICE';
                break;
            case 'omg':
                $body = '<img src="application/modules/Whcomments/externals/images/omg.png" alt="Omg" /> OMG';
                break;
            case 'sad':
                $body = '<img src="application/modules/Whcomments/externals/images/sad.png" alt="Sad" /> SAD';
                break;
            default:
                preg_match_all('/@[^\s]+/', $body, $userTags);

                if (isset($userTags[0]) && count($userTags[0]) > 0) {
                    foreach ($userTags[0] as $userTag) {
                        $user = Engine_Api::_()->user()->getUser(mb_substr($userTag, 1));
                        if(!$user->getIdentity()){
                            $user = Engine_Api::_()->getItemTable('user')->fetchRow(array(
                                'displayname = ?' => mb_substr($userTag, 1),
                            ));
                        }
                        if($user != null){
                            if ($user->getIdentity()) {
                                $href = $this->view->htmlLink($user->getHref(), $userTag);
                                $body = str_replace($userTag, $href, $body);

                                // send notification
                                 Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, "tagged", array( 'url1' => $subject->getHref(), ));
                            }
                        }
                    }
                }
                break;
        }

        $parent_id = (int) $this->_getParam('parent_id', 0);
        if ($parent_id == 0)
            $parent_id = null;


        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try {
            Engine_Api::_()->getDbTable('comments', 'whcomments')->addComment($subject, $viewer, $body, $parent_id);

            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $subjectOwner = $subject->getOwner('user');

            // Activity
            $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
                'owner' => $subjectOwner->getGuid(),
                'body' => $body
            ));

            //$activityApi->attachActivity($action, $subject);
            // Notifications
            // Add notification for owner (if user and not viewer)
            $this->view->subject = $subject->getGuid();
            $this->view->owner = $subjectOwner->getGuid();
            if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity()) {
                $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
                    'label' => $subject->getShortType()
                ));
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            //$commentedUserNotifications = array();
            /* foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
              if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
              continue;

              // Don't send a notification if the user both commented and liked this
              $commentedUserNotifications[] = $notifyUser->getIdentity();

              $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
              'label' => $subject->getShortType()
              ));
              } */

            // Add a notification for all users that liked
            // @todo we should probably limit this
            /* foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
              // Skip viewer and owner
              if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
              continue;

              // Don't send a notification if the user both commented and liked this
              if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
              continue;

              $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
              'label' => $subject->getShortType()
              ));
              } */

            // Increment comment count
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = 'Comment added';
        $this->view->body = $this->view->action('list', 'comment', 'whcomments', array(
            'type' => $this->_getParam('type'),
            'id' => $this->_getParam('id'),
            'format' => 'html',
            'page' => $this->_getParam('page', 1)
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function deleteAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        // Comment id
        $comment_id = $this->_getParam('comment_id');
        if (!$comment_id) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment');
            return;
        }

        // Comment
        $comment = Engine_Api::_()->getItem('whcomments_comment', $comment_id);
        if (!$comment) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment or wrong parent');
            return;
        }

        // Authorization
        if (!$subject->authorization()->isAllowed($viewer, 'edit') &&
                ($comment->poster_type != $viewer->getType() ||
                $comment->poster_id != $viewer->getIdentity())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
            return;
        }

        // Method
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        // Process
        $db = Engine_Api::_()->getDbTable('comments', 'whcomments')->getAdapter();
        $db->beginTransaction();

        try {
            $comment->deleted = true;
            $comment->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = 'Comment deleted';
        $this->view->body = $this->view->action('list', 'comment', 'whcomments', array(
            'type' => $this->_getParam('type'),
            'id' => $this->_getParam('id'),
            'format' => 'html',
        ));
        
        $this->_helper->contextSwitch->initContext();
    }

    public function likeAction() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $comment_id = $this->_getParam('comment_id');

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();

        try {

            $commentedItem->likes()->addLike($viewer);

            // Add notification
            $owner = $commentedItem->getOwner();
            $this->view->owner = $owner->getGuid();
            if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
                    'label' => $commentedItem->getShortType()
                ));
            }

            // Stats
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like added');
        $this->view->body = $this->view->action('list', 'comment', 'core', array(
            'type' => $type,
            'id' => $id,
            'format' => 'html',
            'page' => 1,
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function unlikeAction() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $comment_id = $this->_getParam('comment_id');

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();

        try {
            $commentedItem->likes()->removeLike($viewer);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like removed');
        $this->view->body = $this->view->action('list', 'comment', 'core', array(
            'type' => $type,
            'id' => $id,
            'format' => 'html',
            'page' => 1,
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function getLikesAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        $likes = $subject->likes()->getAllLikesUsers();
        $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
            count($likes)), strip_tags($this->view->fluentList($likes)));
        $this->view->status = true;
    }

}