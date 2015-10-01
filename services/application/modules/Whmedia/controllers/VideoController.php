<?php

class Whmedia_VideoController extends Core_Controller_Action_Standard {

    protected $_isAjax = false;


    public function init() {
        $this->_helper->layout->setLayout('default-simple');
        $this->_isAjax = $isAjax = (bool)$this->_getParam('isajax', false);
        if (!$this->_helper->requireUser()->setNoForward()->isValid())
            return $this->_helper->Message('Please login.', false, false)->setError()->setAjax($isAjax);
        $video_id = (int)$this->_getParam('video_id', 0);
        if (empty ($video_id)) {
            return $this->_helper->Message('Incorrect video ID.', false, false)->setError()->setAjax($isAjax);
        }
        $video = Engine_Api::_()->getItem('whmedia_media', $video_id);
        if ($video !== null) {
             Engine_Api::_()->core()->setSubject($video);
        }
        if (!$this->_helper->requireSubject('whmedia_media')->setNoForward()->isValid())
            return $this->_helper->Message('Incorrect video file.', false, false)->setError()->setAjax($isAjax);
        if (!$video->isOwner(Engine_Api::_()->user()->getViewer()))
            return $this->_helper->Message('Only owner can select cover.', false, false)->setError()->setAjax($isAjax);
        if ($video->getMediaType() != 'video')
            return $this->_helper->Message('Media is not video.', false, false)->setError()->setAjax($isAjax);
        if ($isAjax and !$this->getRequest()->isPost()) {
            return $this->_helper->Message("Invalid Data.", false, false)->setError()->setAjax(true);
        }
    }

    public function indexAction() {
        $this->view->form_cover = $form_cover = new Whmedia_Form_Cover();
        
        $form_cover->setTitle('Upload New Video Cover');
        $this->view->video = $video = Engine_Api::_()->core()->getSubject('whmedia_media');
        if ( $this->getRequest()->isPost() ) {
          if ( $this->getRequest()->getPost('task') == 'upload_cover' && $form_cover->isValid($this->getRequest()->getPost()) ) {
              $form_cover->cover->receive();
              try {
                $this->_setVideoCover($form_cover->cover->getFileName());
              }
              catch (Engine_Exception $exc) {
                return $this->_helper->Message($exc->getMessage(), false, false)->setError();
              }             
              return $this->renderScript('video/_success.tpl');
          }
        }
        $this->view->video_duration = $duration = Engine_Api::_()->whmedia()->getVideoDuration($video);
        $this->view->show_video_slider = !empty($duration) && $video->getFile()->getStorageService() instanceof  Storage_Service_Local;
        
    }

    public function getFrameAction() {
        $time = (int)$this->_getParam('time', 0);
        try {
            $frame = Engine_Api::_()->whmedia()->getFrame(Engine_Api::_()->core()->getSubject('whmedia_media'), $time);
        } catch (Engine_Exception $exc) {
            return $this->_helper->Message($exc->getMessage(), false, false)->setError()->setAjax($this->_isAjax);
        }

        return $this->_helper->json(array('status' => true,
                                          'src' => $frame->storage_path,
                                          'file_id' => $frame->getIdentity()));
    }

    public function setCoverAction() {
        $file_id = (int)$this->_getParam('id', 0);
        if (empty ($file_id)) {
            return $this->_helper->Message("Invalid file ID", false, false)->setError()->setAjax($this->_isAjax);
        }
        $file = Engine_Api::_()->getItemTable('storage_file')->find($file_id)->current();
        if ($file == null) {
            return $this->_helper->Message("Invalid file ID", false, false)->setError()->setAjax($this->_isAjax);
        }
        $video = Engine_Api::_()->core()->getSubject('whmedia_media');
        $file_video_id = $video->getFile()->getIdentity();
        if ($file->mime_major != 'image' or $file->parent_type != 'temporary' or $file->parent_id != $video->getIdentity() or $file->parent_file_id != $file_video_id) {
            return $this->_helper->Message("Invalid file ID", false, false)->setError()->setAjax($this->_isAjax);
        }
        try {
            $file_path    = APPLICATION_PATH . DIRECTORY_SEPARATOR . $file->storage_path;
            $this->_setVideoCover($file_path);
        } catch (Engine_Exception $exc) {
            return $this->_helper->Message($exc->getMessage(), false, false)->setError()->setAjax($this->_isAjax);
        }

        return $this->_helper->json(array('status' => true,
                                          'src' => $frame->storage_path));
    }

    protected function _setVideoCover($file_path) {
        if (!file_exists($file_path)) {
            throw new Engine_Exception("Invalid cover file");
        }
        $video = Engine_Api::_()->core()->getSubject('whmedia_media');
        $file_video_id = $video->getFile()->getIdentity();
        $thumbPathSave = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'whmedia' . DIRECTORY_SEPARATOR . $video->getIdentity() . '_cthumb.jpg';
        $image = Engine_Image::factory(array('quality' => 100));
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $image_width = $settings->getSetting('image_width', '600');
        $image_height = $settings->getSetting('image_height', '900');
        $image->open($file_path)
              ->resize($image_width, $image_height)
              ->write($thumbPathSave)
              ->destroy();
        $file_out = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'whmedia_media',
                                                                                  'parent_id = ?' => $video->getIdentity(),
                                                                                  'type = ?' => 'thumb.etalon',
                                                                                  'parent_file_id = ?' => $file_video_id ));
        if ($file_out != null) {
            $file_out->remove();
        }
        $thumbFileRow = Engine_Api::_()->storage()->create($thumbPathSave, array('parent_id' => $video->getIdentity(),
                                                                                 'parent_type' => $video->getType(),
                                                                                 'user_id' => $video->getOwner()->getIdentity(),
                                                                                 'type' => 'thumb.etalon',
                                                                                 'parent_file_id' => $file_video_id));
        unlink($thumbPathSave);
        return $thumbFileRow;
    }
}
