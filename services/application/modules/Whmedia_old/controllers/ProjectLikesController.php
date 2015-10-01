<?php
class Whmedia_ProjectLikesController extends Core_Controller_Action_User {

    public function init() {
        $this->_helper->layout->disableLayout(true);
        $identity = $this->_getParam('id');
        if( $identity ) {
          $item = Engine_Api::_()->getItem('whmedia_project', $identity);
          if( $item instanceof Whmedia_Model_Project ) {
            if( !Engine_Api::_()->core()->hasSubject() ) {
              Engine_Api::_()->core()->setSubject($item);
            }
          }
        }

        $this->_helper->requireSubject();
    }

    public function toggleLikeAction() {
        
        if( !$this->getRequest()->isPost() ) {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
          return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        
        // Process
        $db = $subject->likes()->getAdapter();
        $db->beginTransaction();

        try {
          if ($subject->likes()->isLike($viewer)) {
              $subject->likes()->removeLike($viewer);
              $islike = false;
          }
          else {
            $islike = true;
            $subject->likes()->addLike($viewer);
            // Add notification
            $owner = $subject->getOwner();
            $this->view->owner = $owner->getGuid();
            if( $owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity() ) {
              $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
              $notifyApi->addNotification($owner, $viewer, $subject, 'liked', array(
                'label' => $subject->getShortType()
              ));
            }
          }
          
          $db->commit();
        }
        catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }

        $this->_helper->json(array('status' => true,
                                   'islike' => $islike,
                                   'count_likes' => $subject->likes()->getLikeCount()));
  }  

}
?>
