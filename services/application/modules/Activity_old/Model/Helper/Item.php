<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Item.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Activity_Model_Helper_Item extends Activity_Model_Helper_Abstract
{
  /**
   * Generates text representing an item
   * 
   * @param mixed $item The item or item guid
   * @param string $text (OPTIONAL)
   * @param string $href (OPTIONAL)
   * @return string
   */
  public function direct($item, $text = null, $href = null)
  {
    $item = $this->_getItem($item, false);

    // Check to make sure we have an item
    if( !($item instanceof Core_Model_Item_Abstract) )
    {
      return false;
    }

    if( !isset($text) )
    {
      $text = $item->getTitle();
    }
    // translate text
    $translate = Zend_Registry::get('Zend_Translate');
    if( $translate instanceof Zend_Translate ) {
      $text = $translate->translate($text);
      // if the value is pluralized, only use the singular
      if (is_array($text))
        $text = $text[0];
    }


    if( !isset($href) )
    {
      $href = $item->getHref();
    }

    /* modification */
    if(get_class($item) == "User_Model_User" && $text == "photo"){
      $src = $item->getPhotoUrl("thumb.icon1");
      if(!$src){
        $src = Zend_Registry::get('StaticBaseUrl') . 'application/modules/User/externals/images/nophoto_user_thumb_icon.png';
      }

      $text = '<img src="' . $src . '" class="circular-mini" /> ';

      if(!isset($item->user_id))
        return false;

    }

    if(get_class($item) == "Whmedia_Model_Project" && $text == "photo"){
      $text = '<img src="' . $item->getPhotoUrl(50, false, false) . '" class=""  />';
    }
    if($text == "project")
      $text = "post";
    /* modification */
    
    $split = explode("-", $text);
    if( isset($split[1]) ){
        if( $split[1] == "subject_id" || $split[1] == "notification_id")
            return $split[0];
    }
    
    return '<a '
      . 'class="feed_item_username" '
      . ( $href ? 'href="'.$href.'"' : '' )
      . '>'
      . $text
      . '</a>';
  }
}