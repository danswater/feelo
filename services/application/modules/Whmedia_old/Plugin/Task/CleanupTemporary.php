<?php

class Whmedia_Plugin_Task_CleanupTemporary extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
    $storageTable = Engine_Api::_()->getItemTable('storage_file');
    $rName = $storageTable->info('name');
    $selectSecond = $storageTable->select()->where('parent_type = "whmedia_media"');
    $selectMain = $storageTable->select()->from($rName)
                                         ->joinLeft(array('tmp' => $selectSecond), "tmp.file_id = $rName.parent_file_id", array())
                                         ->where($rName.'.parent_type = "temporary"')
                                         ->where('tmp.file_id IS NOT NULL')
                                         ->where($rName.'.creation_date < ?', date('Y-m-d H:i:s', strtotime("-1 week")))
                                         ->order($rName.'.creation_date ASC')
                                         ->limit(100);
    $files = $storageTable->fetchAll($selectMain);
    foreach ($files as $file) {
        $file->remove();
    }
  }
}
