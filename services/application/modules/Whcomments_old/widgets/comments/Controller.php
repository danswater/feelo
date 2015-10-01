<?php

class Whcomments_Widget_CommentsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $page = (int) $this->_getParam('page', 0);
        $page++;
        $comment_limit = 10;

        // Get subject
        $subject = null;
        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject();
        else if (($type = $this->_getParam('type')) && ($id = $this->_getParam('id')))
            $subject = Engine_Api::_()->getItem($type, $id);
        else if (($subject = $this->_getParam('subject'))) {
            list($type, $id) = explode('_', $subject);
            $subject = Engine_Api::_()->getItem($type, $id);
        }

        if (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity() || (!method_exists($subject, 'comments') && !method_exists($subject, 'likes')))
            return $this->setNoRender();
        
        $this->view->subject = $subject;

        // Perms
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
        $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

        // Likes
        //$this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
        //$this->view->likes = $likes = $subject->likes()->getLikePaginator();

        // Comments
        $commentsTable = Engine_Api::_()->getDbTable('comments', 'whcomments');
        $commentSelect = $commentsTable->getCommentSelect($subject);
        $commentSelect->where("node.deleted = ?", "0");
        $commentSelect->where("node.parent_id IS NULL");
        $commentSelect->order('lt DESC');

        
        $tree = $commentsTable->fetchTree($subject, null, $commentSelect);

        $this->view->comments = $paginator = Zend_Paginator::factory($tree);
        $this->view->total_comments = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($comment_limit);
        $paginator->setCurrentPageNumber($page); 

        $treeComment = array();
        $pageLimit = $paginator->getTotalItemCount() > $comment_limit ? $comment_limit : $paginator->getTotalItemCount() ; 

        foreach($paginator as $paging){
            $treeComment[ ( $pageLimit - 1) ] = $paging->toArray();
            $pageLimit--;
        }   




        $this->view->comments = $treeComment;
        $this->view->comment_limit = $comment_limit;
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

}