<?php

class Whmedia_Library_PlayerDriver_Drivers_Projekktor extends Whmedia_Library_PlayerDriver_Abstract {

    protected $_FlashIsSupport = true;
    protected $_AppleVideoIsSupport = false;
    protected $_HTML5VideoIsSupport = true;
    /*protected $_AppleAudioIsSupport = false;
    protected $_HTML5AudioMp3IsSupport = true;
    protected $_HTML5AudioOggIsSupport = true;*/

    protected $_ScriptsSet = false;

    public function  __construct(array $settings = null) {
        parent::__construct($settings);
        $this->_setScripts();
    }


    public function getFlashVideoEmbeded(Whmedia_Model_Media &$media) {
       return $this->getHTML5VideoEmbeded($media);
    }

    public function getAppleVideoEmbeded(Whmedia_Model_Media &$media) {
        return $this->getHTML5VideoEmbeded($media);
    }

    public function getHTML5VideoEmbeded(Whmedia_Model_Media &$media) {
        $file = $media->getFile();
        $embedded = "<video id='wh_video_{$media->media_id}' class='projekktor' poster='{$file->getChildren()->getRowMatching('type', 'thumb.etalon')->map()}' width='{$this->_videoWidth}' height='{$this->_videoHeight}' controls>
                        <source src='{$file->getChildren()->getRowMatching('type', 'video.html5')->map()}' type='video/mp4' />
                        <source src='{$file->getChildren()->getRowMatching('type', 'video.html5')->map()}' type='video/mp4' />
                        <source src='{$file->getChildren()->getRowMatching('type', 'video.html5')->map()}' type='video/mp4' />
                    </video>
                    <script type='text/javascript'>
                        jQuery(document).ready(function() {
                            projekktor('#wh_video_{$media->media_id}', {
                                playerFlashMP4: '" .Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/jarisplayer.swf'
                            });
                        });
                    </script>";
        return $embedded;
    }

    public function getFlashAudioEmbeded(Whmedia_Model_Media &$media) {
        $file = $media->getFile();
        return "<div id='wh_audio_{$media->media_id}' class='speakkerSmall'>
                        <source src='{$file->map()}' type='audio/mp3' />
                        <source src='{$file->getChildren()->getRowMatching('type', 'audio.html5')->map()}' type='audio/ogg' />
                </div>
                <script type='text/javascript'>
                        jQuery(document).ready(function() {
                            jQuery('#wh_audio_{$media->media_id}').speakker({
                                            file: '{$file->map()}',
                            });
                        });
                    </script>
                    ";
    }

    public function getAppleAudioEmbeded(Whmedia_Model_Media &$media) {
        
    }

    public function HTML5AudioMp3Embeded(Whmedia_Model_Media &$media) {
        
    }

    public function HTML5AudioOggEmbeded(Whmedia_Model_Media &$media) {
        
    }

    protected function _setScripts() {
        if (!$this->_ScriptsSet) {
            $view = Zend_Registry::get('Zend_View');
            $view->headScript()->appendFile($view->baseUrl() . '/application/modules/Whmedia/externals/scripts/jquery-1.6.2.min.js');
            $view->headScript()->appendFile($view->baseUrl() . '/application/modules/Whmedia/externals/scripts/projekktor.min.js');
            $view->headScript()->appendFile($view->baseUrl() . '/application/modules/Whmedia/externals/scripts/speakker.min.js');
            $view->headLink()->appendStylesheet($view->baseUrl() . '/application/modules/Whmedia/externals/styles/theme/style.css');
            $view->headLink()->appendStylesheet($view->baseUrl() . '/application/modules/Whmedia/externals/styles/speaker_css/speakker.css');
            $view->headLink()->appendStylesheet($view->baseUrl() . '/application/modules/Whmedia/externals/styles/speaker_css/mspeakker.css');
            $script = <<<EOF
                        jQuery.noConflict();
EOF;
            $view->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
        }
    }

}