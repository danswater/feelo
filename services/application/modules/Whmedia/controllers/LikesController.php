<?php
class Whmedia_LikesController extends Core_Controller_Action_Standard {

    public function init() {
        $this->_helper->layout->disableLayout(true);
        $viewer = Engine_Api::_()->user()->getViewer();
        $type = $this->_getParam('type');
        $identity = $this->_getParam('id');
        if( $type && $identity ) {
          $item = Engine_Api::_()->getItem($type, $identity);
          if( $item instanceof Whmedia_Model_Media ) {
            if( !Engine_Api::_()->core()->hasSubject() ) {
              Engine_Api::_()->core()->setSubject($item);
            }
          }
        }

        $this->_helper->requireSubject();
    }

    public function likeAction() {
        if( !$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        if( !$this->getRequest()->isPost() ) {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
          return;
        }

        // Process
        $db = $subject->likes()->getAdapter();
        $db->beginTransaction();

        try {

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

          $db->commit();
        }
        catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }
        $this->view->media = $subject;

        $this->_helper->json(array('status' => true,
                                   'message' => Zend_Registry::get('Zend_Translate')->_('Like added'),
                                   'body' => $this->view->render('likes/_setlike.tpl')));
  }

  public function unlikeAction() {
    if( !$this->_helper->requireUser()->isValid() ) return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    
    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
  
    // Process
    $db = $subject->likes()->getAdapter();
    $db->beginTransaction();

    try {
      $subject->likes()->removeLike($viewer);

      $db->commit();
    }
    catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    $this->view->media = $subject;
    
    $this->_helper->json(array('status' => true,
                               'message' => Zend_Registry::get('Zend_Translate')->_('Like deleted'),
                               'body' => $this->view->render('likes/_setlike.tpl')));
  }

  public function getLikesAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject();
    else throw new Engine_Exception('No media founded.');

    $likes = $subject->likes()->getAllLikesUsers();
    $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
      count($likes)), strip_tags($this->view->fluentList($likes)));
    $this->view->status = true;
  }
}
?>
