<?php

class Widget_ProfileImagesController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        if (!Engine_Api::_()->core()->hasSubject('user'))
            return $this->setNoRender();

        $subject = Engine_Api::_()->core()->getSubject();

        $table = Engine_Api::_()->getItemTable('storage_file');
        $select = $table->select();
        /* 
        $select->distinct()
                ->where('parent_type = ?', 'whmedia_media')
                ->where('user_id = ?', $subject->getIdentity())
                ->where('mime_major LIKE (?)', 'image%')
                ->where('type is NULL')
                ->order('creation_date DESC')
                ->limit(14);
        */               
        $select->distinct()
                ->where('parent_type = "video" OR parent_type = ?', 'whmedia_media')
                ->where('user_id = ?', $subject->getIdentity())
                ->where('extension LIKE (?)', '%jpg%')
                ->where('size > (?)', '100000')
                ->where('mime_major LIKE (?)', 'image%')
                ->where('type is NULL')
                ->order('RAND()')
                ->limit(20);
        $images = $table->fetchAll($select);

        $this->view->images = $images;
    }

}