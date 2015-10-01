<?php

abstract class Whmedia_Library_PlayerDriver_Abstract {

    protected $_FlashIsSupport = false;
    protected $_AppleVideoIsSupport = false;
    protected $_HTML5VideoIsSupport = false;
    protected $_AppleAudioIsSupport = false;
    protected $_HTML5AudioMp3IsSupport = false;
    protected $_HTML5AudioOggIsSupport = false;

    protected $_videoWidth = 320;
    protected $_videoHeight = 240;

    public function  __construct(array $settings = null) {
        $this->setSettings($settings);
    }

    public function  __call($name, $arguments) {
        if (strstr($name,'IsSupport')) {
            $key = '_' . $name;
            if (array_key_exists($key,get_object_vars($this)))
                return (bool) $this->$key;
            else return false;
        }
        throw new Engine_Exception(sprintf('Method "%s" not supported', $name));
    }

    public function setSettings(array $settings = null) {

        if ($settings === null) { // Set Default Video Dimension from plugin settings
            $settings = Engine_Api::_()->getApi('settings', 'core');
                $this->_videoHeight = $settings->getSetting('video_height', '240');
                $this->_videoWidth = $settings->getSetting('video_width', '320');
            }
        else {
            foreach ($settings as $key => $value) {
                $key = '_' . $key;
                if (array_key_exists($key,get_object_vars($this)))
                    $this->$key = $value;
            }
        }

    }

    protected function _get_videothumb_etalon(Whmedia_Model_Media &$media) {
        $file_out = $media->getFile()->getChildren()->getRowMatching('type', 'thumb.etalon');
        $baseURL = Zend_Registry::get('StaticBaseUrl');
        if ($file_out !== null) {
              if ($file_out->getStorageService() instanceof  Storage_Service_Local) {
                  $file_path = $baseURL . $file_out->storage_path;
              }
              else {
                  $file_path = $file_out->map();
              }
        }
        else $file_path = $baseURL . '/application/modules/Whmedia/externals/images/no_photo_project.png';
        return $file_path;
    }

    protected function _getShowSize(Whmedia_Model_Media &$media) {
        $size = Engine_Api::_()->whmedia()->getMediaSize($media);
        if (is_array($size) && key_exists('width', $size) && key_exists('height', $size)) {
            return $size;
        }
        else {
            return array('width' => $this->_videoWidth, 'height' => $this->_videoHeight);
        }
    }

    abstract public function getFlashVideoEmbeded(Whmedia_Model_Media &$media);

    abstract public function getAppleVideoEmbeded(Whmedia_Model_Media &$media);

    abstract public function getHTML5VideoEmbeded(Whmedia_Model_Media &$media);

    abstract public function getFlashAudioEmbeded(Whmedia_Model_Media &$media);

    abstract public function getAppleAudioEmbeded(Whmedia_Model_Media &$media);

    abstract public function HTML5AudioMp3Embeded(Whmedia_Model_Media &$media);

    abstract public function HTML5AudioOggEmbeded(Whmedia_Model_Media &$media);
}