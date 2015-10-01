<?php

class Whmedia_Library_PlayerDriver_Drivers_Flow extends Whmedia_Library_PlayerDriver_Abstract {

    protected $_FlashIsSupport = true;
    protected $_AppleVideoIsSupport = true;
    protected $_HTML5VideoIsSupport = true;
    protected $_AppleAudioIsSupport = false;
    protected $_HTML5AudioMp3IsSupport = true;
    protected $_HTML5AudioOggIsSupport = true;

    protected $_FlashScriptsSet = false;
    protected $_AppleVideoScriptsSet = false;

    public function getFlashVideoEmbeded(Whmedia_Model_Media &$media) {
        $this->_setFlashScripts();
        if ($media->getFile()->getChildren()->getRowMatching('type', 'video.hd') !== null) {
            return $this->_getFlashVideoHDEmbeded($media);
        }
        else return $this->_getFlashVideoSCEmbeded($media);
    }

    public function getAppleVideoEmbeded(Whmedia_Model_Media &$media) {
        $file = $media->getFile();
        $size = $this->_getShowSize($media);
        $embedded = "<video id='wh_video_{$media->media_id}' class='projekktor' poster='{$this->_get_videothumb_etalon($media)}' width='{$size['width']}' height='{$size['height']}' controls>
                        <source src='{$file->getChildren()->getRowMatching('type', 'video.html5')->map()}' type='video/mp4' />
                    </video>";
        return $embedded;
    }

    public function getHTML5VideoEmbeded(Whmedia_Model_Media &$media) {
        return $this->getAppleVideoEmbeded($media);
    }

    public function getFlashAudioEmbeded(Whmedia_Model_Media &$media) {
        $this->_setFlashScripts();
        $file = $media->getFile();
        $embedded = "<a id='wh_video_{$media->media_id}' style='display:block;width:520px;height:200px;'>
                    </a>
                    <script type='text/javascript'>
                        window.addEvent('domready',function(){
                                                        flowplayer('wh_video_{$media->media_id}', {src: '" .Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/flowplayer-3.2.7.swf', wmode: 'opaque'}, {
                                                                                                                                                                                                    clip:  {
                                                                                                                                                                                                        autoPlay: false,
                                                                                                                                                                                                        autoBuffering: false,
                                                                                                                                                                                                        url: '{$file->map()}',
                                                                                                                                                                                                        onStart: function() {
                                                                                                                                                                                                            pause_all(this);
                                                                                                                                                                                                        },
                                                                                                                                                                                                        onResume: function() {
                                                                                                                                                                                                            pause_all(this);
                                                                                                                                                                                                        }
                                                                                                                                                                                                    },
                                                                                                                                                                                                    onBeforeLoad: function(clip) {
                                                                                                                                                                                                        \$('wh_video_{$media->media_id}').setStyle('height', 30);
                                                                                                                                                                                                    },
                                                                                                                                                                                                    plugins: {
                                                                                                                                                                                                        controls:{  url: '" .Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/flowplayer.controls-3.2.5.swf',
                                                                                                                                                                                                                    buttonColor: 'rgba(0, 0, 0, 0.9)',
                                                                                                                                                                                                                    buttonOverColor: '#000000',
                                                                                                                                                                                                                    backgroundColor: '#D7D7D7',
                                                                                                                                                                                                                    backgroundGradient: 'medium',
                                                                                                                                                                                                                    sliderColor: '#FFFFFF',

                                                                                                                                                                                                                    sliderBorder: '1px solid #808080',
                                                                                                                                                                                                                    volumeSliderColor: '#FFFFFF',
                                                                                                                                                                                                                    volumeBorder: '1px solid #808080',

                                                                                                                                                                                                                    timeColor: '#000000',
                                                                                                                                                                                                                    durationColor: '#535353',

                                                                                                                                                                                                                    fullscreen: false,
                                                                                                                                                                                                                    height: 30,
                                                                                                                                                                                                                    autoHide: false
                                                                                                                                                                                                                }
                                                                                                                                                                                                     }
                                                                                                                                                                                                });
                                                       });
                    </script>";
        return $embedded;
    }

    public function getAppleAudioEmbeded(Whmedia_Model_Media &$media) {
        return '';
    }

    public function HTML5AudioMp3Embeded(Whmedia_Model_Media &$media) {
        return "<audio id='wh_video_{$media->media_id}' src='" . "{$media->getFile()->map()}' controls='controls' ></audio>";
                                                                                                                                                                                                                }

    public function HTML5AudioOggEmbeded(Whmedia_Model_Media &$media) {
        return "<audio id='wh_video_{$media->media_id}' src='{$media->getFile()->getChildren()->getRowMatching('type', 'audio.html5')->map()}' controls='controls' style='width:300px;height:100px;'></audio>";
    }

    protected function _getFlashVideoHDEmbeded(Whmedia_Model_Media &$media) {
        $file = $media->getFile();
        $size = $this->_getShowSize($media);
        $file_thumb = $this->_get_videothumb_etalon($media);
        $embedded = "<div id='wh_video_{$media->media_id}' style='display:block;width:{$size['width']}px;height:{$size['height']}px;'></div>";
        $embedded .= "
                    <script type='text/javascript'>
                        window.addEvent('domready',function(){
                                               flowplayer('wh_video_{$media->media_id}', {src: '" .Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/flowplayer-3.2.7.swf', wmode: 'opaque'}, {
                                                                                                                                                                                                        clip:  {
                                                                                                                                                                                                            autoPlay: false,
                                                                                                                                                                                                            autoBuffering: false,
                                                                                                                                                                                                            provider: 'lighttpd',
                                                                                                                                                                                                            urlResolvers: 'bwcheck',
                                                                                                                                                                                                            onStart: function() {
                                                                                                                                                                                                                pause_all(this);
                                                                                                                                                                                                            },
                                                                                                                                                                                                            onResume: function() {
                                                                                                                                                                                                                pause_all(this);
                                                                                                                                                                                                            },
                                                                                                                                                                                                            bitrates: [
                                                                                                                                                                                                                          { url: '{$file->map()}', normal: true, isDefault: true },
                                                                                                                                                                                                                          // HD
                                                                                                                                                                                                                          { url: '{$file->getChildren()->getRowMatching('type', 'video.hd')->map()}', hd: true }
                                                                                                                                                                                                                      ]
                                                                                                                                                                                                        },
                                                                                                                                                                                                        playlist: [
                                                                                                                                                                                                          
                                                                                                                                                                                                                {url: '{$file_thumb}', scaling: 'scale', autoPlay: true},
                                                                                                                                                                                                                {url: '{$file->map()}'},
                                                                                                                                                                                                                {url: '{$file_thumb}', scaling: 'scale', autoPlay: true}

                                                                                                                                                                                                        ],
                                                                                                                                                                                                        // streaming plugins are configured normally under the plugins node
                                                                                                                                                                                                        plugins: {
                                                                                                                                                                                                                lighttpd: {
                                                                                                                                                                                                                        url: '" .Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/flowplayer.pseudostreaming-3.2.7.swf'
                                                                                                                                                                                                                },
                                                                                                                                                                                                                bwcheck: {
                                                                                                                                                                                                                        url: '" .Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/flowplayer.bwcheck-3.2.5.swf',
                                                                                                                                                                                                                        netConnectionUrl: '" .Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/flowplayer-3.2.7.swf',
                                                                                                                                                                                                                        // enable the HD toggle button
                                                                                                                                                                                                                            hdButton: true

                                                                                                                                                                                                                    },
                                                                                                                                                                                                                controls:{  url: '" .Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/flowplayer.controls-3.2.5.swf',
                                                                                                                                                                                                                            buttonColor: 'rgba(0, 0, 0, 0.9)',
                                                                                                                                                                                                                            buttonOverColor: '#000000',
                                                                                                                                                                                                                            backgroundColor: '#D7D7D7',
                                                                                                                                                                                                                            backgroundGradient: 'medium',
                                                                                                                                                                                                                            sliderColor: '#FFFFFF',

                                                                                                                                                                                                                            sliderBorder: '1px solid #808080',
                                                                                                                                                                                                                            volumeSliderColor: '#FFFFFF',
                                                                                                                                                                                                                            volumeBorder: '1px solid #808080',

                                                                                                                                                                                                                            timeColor: '#000000',
                                                                                                                                                                                                                            durationColor: '#535353'
                                                                                                                                                                                                               }
                                                                                                                                                                                                        }
                                                                                                                                                                                                    }).controls('hulu');

                                                       });
                    </script>";
        return $embedded;
    }

    protected function _getFlashVideoSCEmbeded(Whmedia_Model_Media &$media) {
        $file = $media->getFile();
        $file_thumb = $this->_get_videothumb_etalon($media);
        $size = $this->_getShowSize($media);
        $embedded = "<div id='wh_video_{$media->media_id}' style='display:block;width:{$size['width']}px;height:{$size['height']}px;'></div>";
        $embedded .= "
                    <script type='text/javascript'>
                        window.addEvent('domready',function(){
                                               flowplayer('wh_video_{$media->media_id}', {src: '" .Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/flowplayer-3.2.7.swf', wmode: 'opaque'}, {
                                                                                                                                                                                                        clip:  {
                                                                                                                                                                                                            autoPlay: false,
                                                                                                                                                                                                            autoBuffering: false,
                                                                                                                                                                                                            provider: 'lighttpd',
                                                                                                                                                                                                            onStart: function() {
                                                                                                                                                                                                                pause_all(this);
                                                                                                                                                                                                            },
                                                                                                                                                                                                            onResume: function() {
                                                                                                                                                                                                                pause_all(this);
                                                                                                                                                                                                            }
                                                                                                                                                                                                        },
                                                                                                                                                                                                        playlist: [

                                                                                                                                                                                                                {url: '{$file_thumb}', scaling: 'scale', autoPlay: true},
                                                                                                                                                                                                                {url: '{$file->map()}'},
                                                                                                                                                                                                                {url: '{$file_thumb}', scaling: 'scale', autoPlay: true}

                                                                                                                                                                                                        ],
                                                                                                                                                                                                        // streaming plugins are configured normally under the plugins node
                                                                                                                                                                                                        plugins: {
                                                                                                                                                                                                                lighttpd: {
                                                                                                                                                                                                                        url: '" .Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/flowplayer.pseudostreaming-3.2.7.swf'
                                                                                                                                                                                                                },
                                                                                                                                                                                                                controls:{  url: '" .Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/flowplayer.controls-3.2.5.swf',
                                                                                                                                                                                                                            buttonColor: 'rgba(0, 0, 0, 0.9)',
                                                                                                                                                                                                                            buttonOverColor: '#000000',
                                                                                                                                                                                                                            backgroundColor: '#D7D7D7',
                                                                                                                                                                                                                            backgroundGradient: 'medium',
                                                                                                                                                                                                                            sliderColor: '#FFFFFF',

                                                                                                                                                                                                                            sliderBorder: '1px solid #808080',
                                                                                                                                                                                                                            volumeSliderColor: '#FFFFFF',
                                                                                                                                                                                                                            volumeBorder: '1px solid #808080',

                                                                                                                                                                                                                            timeColor: '#000000',
                                                                                                                                                                                                                            durationColor: '#535353'
                                                                                                                                                                                                                }
                                                                                                                                                                                                        }
                                                                                                                                                                                                    });

                                                       });
                    </script>";
        return $embedded;
    }

    protected function _setFlashScripts() {
        if (!$this->_FlashScriptsSet) {
            $view = Zend_Registry::get('Zend_View');
            $view->headScript()->appendFile($view->baseUrl() . '/application/modules/Whmedia/externals/scripts/flowplayer-3.2.6.min.js')
                               ->appendFile($view->baseUrl() . '/application/modules/Whmedia/externals/scripts/flowplayer.controls-3.0.2.min.js');
            $script = "function pause_all(bind) {
                                                \$f('*').each(function(index,player) {
                                                     if (bind != player && player.isPlaying()) {
                                                        player.pause();
                                                     }
                                                  });
                       }";
            $view->headScript()->appendScript($script, $type = 'text/javascript', $attrs = array());
        }
    }

    protected function _setAppleVideoScripts() {
        if (!$this->_AppleVideoScriptsSet) {
            $view = Zend_Registry::get('Zend_View');
            $view->headScript()->appendFile($view->baseUrl() . '/application/modules/Whmedia/externals/scripts/flowplayer.ipad-3.2.2.min.js');
        }
    }
}
