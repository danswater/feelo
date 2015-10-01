<?php

class Whcore_Widget_ShareSocialController extends Engine_Content_Widget_Abstract
{
  
  public function indexAction()  {    
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $mediaUseFacebook = (bool) $settings->getSetting('wh.facebook.type', 0);
    if ($mediaUseFacebook) 
        $this->view->facebookAppId = $settings->getSetting('core.facebook.appid', '');
    else {
        $this->view->facebookAppId = $settings->getSetting('wh.facebook.appid', '');
    }
  }

}