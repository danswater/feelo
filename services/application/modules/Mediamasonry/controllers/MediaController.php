<?php

class Mediamasonry_MediaController extends Core_Controller_Action_Standard {

    public function showAction() {
      if( !$this->_helper->requireAuth()->setAuthParams('whmedia_project', null, 'view')->isValid() ) return;
      $this->_helper->layout->setLayout('default-simple');
      $viewer = Engine_Api::_()->user()->getViewer();
      $contentTable = Engine_Api::_()->getDbtable('content', 'core');
      $widget_id = (int)$this->_getParam('widget_id');
      $id = (int)$this->_getParam('id');
      $row = $contentTable->find($widget_id)->current();
      if (empty ($id)) {
          return $this->_helper->Message('Content was not found.', false, false)->setError();
      }
      if( null === $row ) {
          return $this->_helper->Message('Content was not found.', false, false)->setError();
      }
      $widget_name = str_replace('mediamasonry.', '', $row->name);
      if (in_array($widget_name, array('featured-media', 'media', 'popular-media', 'profile-fmedia'))) {
          $media = Engine_Api::_()->getItem('whmedia_media', $id);
          if (empty ($media)) {
              return $this->_helper->Message('Content was not found.', false, false)->setError();
          }
          return $this->_forward('show-media', 'index', 'whmedia', array('format' => 'smoothbox',
                                                                         'media' => $media->getIdentity()));
      }
      else {
          $project = Engine_Api::_()->getItem('whmedia_project', $id);
          if (empty ($project)) {
              return $this->_helper->Message('Content was not found.', false, false)->setError();
          }
          $coverMedia = $project->getCoverMedia();
          if (($coverMedia === null and $project->getMediasCount()) or $coverMedia->invisible == 1) {
              $coverMedia = $project->getMedias()->rewind()->current();
          }
          if (empty ($coverMedia)) {
              return $this->_helper->Message('Content was not found.', false, false)->setError();
          }
          return $this->_forward('show-media', 'index', 'whmedia', array('format' => 'smoothbox',
                                                                         'media' => $coverMedia->getIdentity()));
      }

  }

}
