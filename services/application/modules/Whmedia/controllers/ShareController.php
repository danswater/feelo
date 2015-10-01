<?php

class Whmedia_ShareController extends Core_Controller_Action_Standard {

    public function init() {
     //   $this->_helper->layout->setLayout('default-simple');
        $media_id = (int)$this->_getParam('media_id', false);
        if (empty ($media_id)) {
            return $this->_helper->Message('Incorrect media ID.', false, false)->setError();
        }
        $this->view->media = $media = Engine_Api::_()->getItem('whmedia_media', $media_id);
        if ($media == null) {
             return $this->_helper->Message('Incorrect media file.', false, false)->setError();
        }
        Engine_Api::_()->core()->setSubject($media);

        if (!$this->_helper->requireSubject('whmedia_media')->setNoForward()->isValid())
            return $this->_helper->Message('Incorrect media file.', false, false)->setError();
    }

    public function getCodeAction() {
        if( !$this->_helper->requireUser()->isValid() ) return;
        $media = Engine_Api::_()->core()->getSubject('whmedia_media');
        if (!$media->isOwner(Engine_Api::_()->user()->getViewer()))
            return $this->_helper->Message('Only owner can get a embedded code.', false, false)->setError();
    }

    public function embeddedAction() {
        $this->_helper->layout->disableLayout();
        return;
    }

    public function downloadAction() {
        $media = Engine_Api::_()->core()->getSubject('whmedia_media');
        if (!Engine_Api::_()->authorization()->context->isAllowed($media->getProject(), 'everyone', 'allow_d_orig')) {
            return $this->_helper->Message('You do not have permission to download this media.', false, false)->setError();
        }
        if (!$media->issetOriginal() ) {
            return $this->_helper->Message('Original file is empty.', false, false)->setError();
        }
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $originalFile = $media->getOriginal();
        if ($originalFile->getStorageService() instanceof  Storage_Service_Local) {
            $file_path = $originalFile->storage_path;
        }
        else {
            $file_path = $originalFile->temporary();
        }
        $fileName = $originalFile->name;
        $ftime   = date('D, d M Y H:i:s T', strtotime($originalFile->modified_date));
        $fsize   = $originalFile->size;
        $mimetype = $originalFile->mime_major;
        $fd      =@fopen($file_path, 'rb');
        if (isset($_SERVER['HTTP_RANGE'])) {
            $range            =$_SERVER['HTTP_RANGE'];
            $range            =str_replace('bytes=', '', $range);
            list($range, $end)=explode('-', $range);

            if (!empty($range)) {
                fseek($fd, $range);
            }
        }
        else
        {
            $range=0;
        }

        $response = $this->getResponse();
        if ($range) {
            $response->setHttpResponseCode(206)
                     ->setHeader($_SERVER['SERVER_PROTOCOL'], '206 Partial Content', true);
        }
        else {
            $response->setHttpResponseCode(200)
                     ->setHeader($_SERVER['SERVER_PROTOCOL'], '200 OK', true);
        }

        $response->setHeader('Pragma', 'public', true)
                 ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                 ->setHeader('Content-type', $mimetype, true)
                 ->setHeader('Content-Length', $fsize - $range)
                 ->setHeader('Content-Disposition', 'attachment; filename='.$fileName)
                 ->setHeader('Last-Modified', $ftime)
                 ->setHeader('Accept-Ranges', 'bytes')
                 ->setHeader('Content-Range', "bytes $range-" . ($fsize - 1) . '/' . $fsize)
                 ->setHeader('Content-transfer-encoding', 'binary')
                 ->clearBody();
        $response->sendHeaders();
        fpassthru($fd);
        fclose($fd);
        exit;
    }

}
