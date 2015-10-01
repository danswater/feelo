<?php

class Whmedia_Model_Category extends Core_Model_Item_Abstract
{
 public function getTable()
  {
    if( is_null($this->_table) )
    {
      $this->_table = Engine_Api::_()->getDbtable('categories', 'whmedia');
    }

    return $this->_table;
  }


}