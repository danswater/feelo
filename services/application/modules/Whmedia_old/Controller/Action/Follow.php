<?php

    class Whmedia_Controller_Action_Follow extends Core_Controller_Action_Standard {

        public function  postDispatch() {
            parent::postDispatch();
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($viewer->getIdentity()) {
                $listsTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
                $this->view->boxes = $boxes = $listsTable->fetchAll(array('user_id = ?' => $viewer->getIdentity()));
                $this->getResponse()->appendBody($this->view->render('application/modules/Whmedia/views/scripts/etc/follow.tpl'));
            }
        }
    }
?>
