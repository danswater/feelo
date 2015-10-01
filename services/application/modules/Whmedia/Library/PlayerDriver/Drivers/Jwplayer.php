<?php

class Whmedia_Library_PlayerDriver_Drivers_Jwplayer extends Whmedia_Library_PlayerDriver_Abstract {

    protected $_FlashIsSupport = true;
    protected $_AppleVideoIsSupport = false;
    protected $_HTML5VideoIsSupport = true;
    
    //Audio support
    protected $_AppleAudioIsSupport = false;
    protected $_HTML5AudioMp3IsSupport = false;
    protected $_HTML5AudioOggIsSupport = false;
    
    
    protected $_ScriptsSet = false;
	protected $_LinksSet = false;

    public function  __construct(array $settings = null) {
        parent::__construct($settings);
        $this->_setScripts();
		$this->_setLinks();
    }


    public function getFlashVideoEmbeded(Whmedia_Model_Media &$media) {
       $file = $media->getFile();
       $file_thumb = $this->_get_videothumb_etalon($media);
       $size = $this->_getShowSize($media);
       $embedded = "<div id='wh_video_{$media->media_id}' style='display:block;width:{$size['width']}px;height:{$size['height']}px;'></div>";
       $embedded .= "
                    <script type='text/javascript'>
                        window.addEvent('domready',function(){
                            jwplayer('wh_video_{$media->media_id}').setup({
                                                                        flashplayer: '" . Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/jwplayer.swf',
                                                                        file: '{$file->map()}',
                                                                        image: '{$file_thumb}',
                                                                        origHeight: {$size['height']},
                                                                        origWeight: {$size['width']},
                                                                        plugins: {
                                                                            'viral-2': {
                                                                                onpause: false,
                                                                                callout: 'none'
                                                                            }
                                                                        },
                                                                        skin: '" . Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/modieus.zip'    
                                                                        
                                                                        });
                        });
                    </script>";
        return $embedded;
    }

    public function getAppleVideoEmbeded(Whmedia_Model_Media &$media) {
        
    }

    public function getHTML5VideoEmbeded(Whmedia_Model_Media &$media) {
       $file = $media->getFile();
       $file_thumb = $this->_get_videothumb_etalon($media);
       $size = $this->_getShowSize($media);
    /*   
	   $embedded = "
           <video
                src='{$file->map()}'
                height='{$size['height']}'
                id='wh_video_{$media->media_id}'
                poster='{$file_thumb}'
                width='{$size['height']}'>
           </video>
           ";
       $embedded .= "
                    <script type='text/javascript'>
                        window.addEvent('domready',function(){
                            jwplayer('wh_video_{$media->media_id}').setup({
                                                        flashplayer: '" . Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/jwplayer.swf'
                            });
                        });
                    </script>";
	*/

        if ( $size[ 'height' ] > $size[ 'width' ] ) {
            //portrait
			$size[ 'height' ] = 500;
			$size[ 'width' ] = 650;
        } else {
            $size[ 'height' ] = 360;
            $size[ 'width' ]  = 650;
        }

		$embedded = '<video style="background-color:#000" height="'. $size[ 'height' ]. '" width="'. $size[ 'width' ] .'" poster="'. $file_thumb .'" controls>
						<source src="'. $file->map() .'" type="video/mp4">
						Your browser does not support HTML5 video.
					</video>';

		return $embedded;
    }

    public function getFlashAudioEmbeded(Whmedia_Model_Media &$media) {
       $file = $media->getFile();

       $embedded = "<div id='wh_video_{$media->media_id}' style='display:block;'></div>";
       $embedded .= "
                    <script type='text/javascript'>
                        window.addEvent('domready',function(){
                            jwplayer('wh_video_{$media->media_id}').setup({
                                                                        flashplayer: '" . Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/jwplayer.swf',
                                                                        file: '{$file->map()}',
                                                                        'width': '470',
                                                                        'height': '30',
                                                                        'controlbar': 'bottom',
                                                                        skin: '" . Zend_Registry::get('Zend_View')->baseUrl() . "/application/modules/Whmedia/externals/swf/modieus.zip'    
                                                                        
                                                                        });
                        });
                    </script>";
        return $embedded;
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
            $view->headScript()->appendFile($view->baseUrl() . '/application/modules/Whmedia/externals/scripts/jwplayer.js');
        }
    }
	
	protected function _setLinks () {
		if ( !$this->_LinksSet ) {
			$view = Zend_Registry::get( 'Zend_View' );
		}
	}

}