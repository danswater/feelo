<?php

class Whmedia_Library_Player {

    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Whmedia_Library_Player
     */
    protected static $_instance = null;

    protected $_FlashIsEnabled;
    protected $_HTML5Video;
    protected $_HTML5Audio;
    protected $_isApple;
    protected $_defaultPlayerDriver;

    /**
     * Constructor
     *
     * Instantiate using {@link getInstance()}; Whmedia_Library_Player is a singleton
     * object.
     *
     *
     * @return void
     */
    protected function __construct() {
        $this->setDefaultPlayerDriver('Whmedia_Library_PlayerDriver_Drivers_Jwplayer');
    }

    public function setDefaultPlayerDriver($driver = null) {
        if ($driver === null)
            $this->_defaultPlayerDriver = new Whmedia_Library_PlayerDriver_Drivers_Flow ();
        else if ($driver instanceof Whmedia_Library_PlayerDriver_Abstract )
            $this->_defaultPlayerDriver = $driver;
        else if (is_string($driver))
            $this->_defaultPlayerDriver = new $driver();
        else $this->setDefaultPlayerDriver ();
    }

    public function getDefaultPlayerDriver() {
        return $this->_defaultPlayerDriver;
    }

    /**
     * Singleton instance
     *
     * @return Whmedia_Library_Player
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getVideoEmbeded( Whmedia_Model_Media &$media) {
        if ($this->FlashIsEnabled()) {
            if ($this->_defaultPlayerDriver->FlashIsSupport()) {
                return $this->_defaultPlayerDriver->getFlashVideoEmbeded($media);
            }
        }
        if ($media->getFile()->type == 'video.html5') {
            if ($this->isApple()) {
                if ($this->_defaultPlayerDriver->AppleVideoIsSupport()) {
                    return $this->_defaultPlayerDriver->getAppleVideoEmbeded($media);
                }
                if ($this->_defaultPlayerDriver->HTML5VideoIsSupport()) {
                    return $this->_defaultPlayerDriver->getHTML5VideoEmbeded($media);
                }
            }
            if ($this->HTML5VideoIsEnabled()) {
                if ($this->getHTML5Video_codec_support() == 'h264' and $this->_defaultPlayerDriver->HTML5VideoIsSupport()) {
                    return $this->_defaultPlayerDriver->getHTML5VideoEmbeded($media);
                }
            }
        }
        return $this->_getViewContentError($media);
    }

    public function getAudioEmbeded( Whmedia_Model_Media &$media) {
        if ($this->FlashIsEnabled()) {
            if ($this->_defaultPlayerDriver->FlashIsSupport()) {
                return $this->_defaultPlayerDriver->getFlashAudioEmbeded($media);
            }
        }
        if ($this->isApple()) {
            if ($this->_defaultPlayerDriver->AppleAudioIsSupport()) {
                return $this->_defaultPlayerDriver->getAppleAudioEmbeded($media);
            }
            if ($this->_defaultPlayerDriver->HTML5AudioMp3IsSupport()) {
                return $this->_defaultPlayerDriver->HTML5AudioMp3Embeded($media);
            }
        }
        if ($this->HTML5AudioIsEnabled()) {
            if ($this->getHTML5Audio_codec_support() == 'mp3'and $this->_defaultPlayerDriver->HTML5AudioMp3IsSupport())
                return $this->_defaultPlayerDriver->HTML5AudioMp3Embeded($media);
            if ($media->getFile()->getChildren()->getRowMatching('type', 'audio.html5') !== null) {
                if ($this->getHTML5Audio_codec_support() == 'ogg'and $this->_defaultPlayerDriver->HTML5AudioOggIsSupport()) {
                    return $this->_defaultPlayerDriver->HTML5AudioOggEmbeded($media);
                }
            }
        }
        return $this->_getViewContentError($media);
    }

    public function FlashIsEnabled() {
        if ($this->_FlashIsEnabled === null) {
            if ($this->isApple())
                return ($this->_FlashIsEnabled = false);
            $cookie = Zend_Controller_Front::getInstance()->getRequest()->getCookie('FlashIsEnabled');
            if ($cookie === null) {
                $this->_FlashIsEnabled = true;
            }
            else $this->_FlashIsEnabled = (bool)$cookie;
        }
        return $this->_FlashIsEnabled;
    }

    public function HTML5VideoIsEnabled () {
        if ($this->_HTML5Video === null)
            $this->_HTML5Video = Zend_Controller_Front::getInstance()->getRequest()->getCookie('HTMLVideoIsEnabled');
        return (bool)  $this->_HTML5Video;
    }

    public function getHTML5Video_codec_support() {
        if ($this->HTML5VideoIsEnabled() === true)
            return $this->_HTML5Video;
        return false;
    }

    public function HTML5AudioIsEnabled () {
        if ($this->_HTML5Audio === null)
            $this->_HTML5Audio = Zend_Controller_Front::getInstance()->getRequest()->getCookie('HTMLAudioIsEnabled');
        return (bool)  $this->_HTML5Audio;
    }

    public function getHTML5Audio_codec_support() {
        if ($this->HTML5AudioIsEnabled() === true)
            return $this->_HTML5Audio;
        return false;
    }

    public function isApple() {
        if ($this->_isApple === null)
            $this->_isApple = Engine_Api::_()->whmedia()->isApple();
        return $this->_isApple;
    }

    protected function _getViewContentError(Whmedia_Model_Media $media) {
        return "<div id='wh_video_{$media->media_id}'><p>Sorry, but this content cannot be displayed.</p></div>";
    }

}
