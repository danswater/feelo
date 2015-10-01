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
						// check wether browser supports html5 video
						function supports_video() {
						  return !!document.createElement('video').canPlayType;
						}				
				
                      en4.core.runonce.add(function() {
						if ( supports_video() ) {
                            Cookie.write('HTMLVideoIsEnabled', 'h264', {duration: 1,
                                                                        path: '{$view->baseUrl()}/' });

                            Cookie.write('FlashIsEnabled', 0, {duration: 1,
                                                               path: '{$view->baseUrl()}/' });																		
						} else {
                            Cookie.write('FlashIsEnabled', 1, {duration: 1,
                                                               path: '{$view->baseUrl()}/' });

                            Cookie.write('HTMLVideoIsEnabled', 0, {duration: 1,
                                                               path: '{$view->baseUrl()}/' });															   
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
