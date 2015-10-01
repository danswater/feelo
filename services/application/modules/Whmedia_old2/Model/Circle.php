<?php

class Whmedia_Model_Circle extends Core_Model_Item_Abstract
{
    protected $_searchTriggers = false;  
    protected $_circleitemsTable;
    

    public function has(User_Model_User $user) {        
        return (bool)$this->_getCircleitemsTable()->fetchRow(array('circle_id = ?' => $this->getIdentity(),
                                                                   'user_id = ?' => $user->getIdentity()));
    }
    
    public function add(User_Model_User $user) {
        $row = $this->_getCircleitemsTable()->createRow(array('circle_id' => $this->getIdentity(),
                                                              'user_id' => $user->getIdentity()));
        $row->save();
        return $row;
    }
    
    public function remove(User_Model_User $user) {
        $row = $this->_getCircleitemsTable()->fetchRow(array('circle_id = ?' => $this->getIdentity(),
                                                             'user_id = ?' => $user->getIdentity()));
        if ($row instanceof Core_Model_Item_Abstract) {
            $row->delete();
        }
    }
    
    protected function _getCircleitemsTable() {
        if ($this->_circleitemsTable == NULL) {
            $this->_circleitemsTable = Engine_Api::_()->getDbTable('circleitems', 'whmedia');
        }
        return $this->_circleitemsTable;
    }
}