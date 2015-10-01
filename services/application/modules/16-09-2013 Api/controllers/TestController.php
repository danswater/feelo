<?php

class Api_TestController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->_helper->AjaxContext()
                ->addActionContext('get', 'json')
                ->addActionContext('post', 'json')
                ->addActionContext('new', 'json')
                ->addActionContext('edit', 'json')
                ->addActionContext('put', 'json')
                ->addActionContext('delete', 'json')
                ->initContext('json');
    }

    public function indexAction() {
        $this->_helper->json(array('action' => 'index'));
    }

    public function getAction() {
        $email = $this->_getParam('id', null);
		
        $viewer = Engine_Api::_()->user()->getUser($email);
        Engine_Api::_()->user()->setViewer($viewer);
		
        $select = Engine_Api::_()->getDbtable('stream', 'whmedia')->selectStreamProjects($viewer);
        $select->order('tmp_stream_projects.creation_date DESC');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
        $pageNumber = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($pageNumber);
        
        foreach ($paginator as $item) {
            $activity['project_id:'.$item->getIdentity()] = array(
            	
                'href' => $item->getHref(),
                'image' => $item->getPhotoUrl($this->_getParam('thumb_width', 160), null),
                'author' => array(
                    'username' => $item->getParent()->getTitle(),
                    'href' => $item->getParent()->getHref(),
                    'image' => $item->getParent()->getPhotoUrl(),
                ),
                'likes' => $item->likes()->getLikeCount(),
                'comments' => $item->project_views
            );
        }



        $out = array('Activity_Feed' => $activity);
        
        $this->_helper->json($out);
    }

    public function newAction() {

        $this->_forward('index');
    }

    public function postAction() {
        $this->_helper->json(array('action' => 'post'));
    }

    public function editAction() {

        $this->_forward('index');
    }

    public function putAction() {

        $this->_forward('index');
    }

    public function deleteAction() {

        $this->_forward('index');
    }

    public function headAction() {
        
    }

    public function assAction() {
        $this->_helper->json(array('action' => 'qa'));
    }

}
