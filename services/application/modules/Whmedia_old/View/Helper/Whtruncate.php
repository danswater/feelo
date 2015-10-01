<?php

class Whmedia_View_Helper_Whtruncate extends Engine_View_Helper_String
{
  public function whtruncate($string, $width = null)
  {
      $width = (int) $width;
      $result_width = (empty ($width)) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('thumb_width', '100') : $width;
      $division = ($width < 200) ? 8 : 7;
      $chars = $result_width/$division;
      return parent::truncate($string, (int)$chars);
  }
}