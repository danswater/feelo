<?php

class Whmedia_ShareProjectController extends Core_Controller_Action_Standard {

    public function init() {
        $id = (int)$this->_getParam('project_id', false);
        if (empty ($id)) {
            return $this->_helper->Message('Incorrect project ID.', false, false)->setError();
        }
        $this->view->project = $project = Engine_Api::_()->getItem('whmedia_project', $id);
        if ($project == null) {
             return $this->_helper->Message('Incorrect project ID.', false, false)->setError();
        }
        Engine_Api::_()->core()->setSubject($project);
    }

    public function indexAction() {
        
    }    
    
    public function repostAction() {
        if( !$this->_helper->requireUser()->isValid() ) return;       
        // In smoothbox
        $this->view->delete_title = 'Repost Project?';
        $this->view->delete_description = 'Are you sure that you want to repost this media project?';
        $this->view->button = 'Repost';

        // Check post
        if( $this->getRequest()->isPost()) {
          $project_id =Engine_Api::_()->core()->getSubject('whmedia_project')->getIdentity();  
          $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
          $streamTable = Engine_Api::_()->getDbtable('stream', 'whmedia');
          
          $streamRow = $streamTable->fetchRow(array('user_id = ?' => $viewer_id,
                                                    'project_id = ?' => $project_id));
          if (empty($streamRow)) {
              $streamTable->createRow(array('user_id' => $viewer_id,
                                            'project_id' => $project_id))->save();
          }
          return $this->_helper->Message('Done.', true, false);
        }

        // Output
        $this->renderScript('etc/delete.tpl');
    }

}
