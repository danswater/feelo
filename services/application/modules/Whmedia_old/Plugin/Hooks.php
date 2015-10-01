<?php

class Whmedia_Plugin_Hooks
{
    public function onRenderLayoutDefault($event) {
        // Arg should be an instance of Zend_View
        $view = $event->getPayload();
       
        if($view instanceof Zend_View ) {
            if (!Engine_Api::_()->whmedia()->isApple() and Zend_Controller_Front::getInstance()->getRequest()->getCookie('FlashIsEnabled') === null) {
                $RequestRequest = Zend_Controller_Front::getInstance()->getRequest();
                if ($RequestRequest->getModuleName() == 'whmedia' and $RequestRequest->getControllerName() == 'index' and $RequestRequest->getActionName() == 'view')
                    $reload = 'window.location.href=window.location.href;';
                else
                    $reload = '';
                $script = <<<EOF
                
                      en4.core.runonce.add(function() {
                        if(Browser.Plugins.Flash && Browser.Plugins.Flash.version > 8) {
                            Cookie.write('FlashIsEnabled', 1, {duration: 1,
                                                               path: '{$view->baseUrl()}/' });
                        }
                        else {
                            Cookie.write('FlashIsEnabled', 0, {duration: 1,
                                                               path: '{$view->baseUrl()}/' });
                            if (Browser.Features.video == false) {
                                Cookie.write('HTMLVideoIsEnabled', 0, {duration: 1,
                                                                       path: '{$view->baseUrl()}/' });
                            }
                            else {
                                if (Browser.Features.audio.h264 == 'probably')
                                    Cookie.write('HTMLVideoIsEnabled', 'h264', {duration: 1,
                                                                           path: '{$view->baseUrl()}/' });
                                else
                                    Cookie.write('HTMLVideoIsEnabled', 'ogg', {duration: 1,
                                                                           path: '{$view->baseUrl()}/' });
                            }
                            if (Browser.Features.audio == false) {
                                Cookie.write('HTMLAudioIsEnabled', 0, {duration: 1,
                                                                       path: '{$view->baseUrl()}/' });
                            }
                            else {
                                if (Browser.Features.audio.mp3 == 'probably')
                                    Cookie.write('HTMLAudioIsEnabled', 'mp3', {duration: 1,
                                                                           path: '{$view->baseUrl()}/' });
                                else
                                    Cookie.write('HTMLAudioIsEnabled', 'ogg', {duration: 1,
                                                                           path: '{$view->baseUrl()}/' });
                            }
                            $reload
                        }
                      });
EOF;

                $view->headScript()->appendFile('application/modules/Whmedia/externals/scripts/mooModernizr-min.js')
                                   ->appendScript($script);
            }
        }
  }

}
?>
