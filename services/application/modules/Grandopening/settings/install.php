<?php

class Grandopening_Installer extends Engine_Package_Installer_Module {

    public function onPreInstall() {
        parent::onPreInstall();
        $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'opening_cover';
        if (!is_dir($tmpDir)) {
            if (!mkdir($tmpDir, 0777, true)) {
                return $this->_error('Grand Opening cover directory did not exist and could not be created.');
            }
        }
        if (!is_writable($tmpDir)) {
            return $this->_error('Grand Opening cover directory is not writable.');
        }
    }

    function onInstall() {
        parent::onInstall();
        $this->_addPage();
    }

    private function _addPage() {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'grandopening')
                ->limit(1);
        ;
        $info = $select->query()->fetch();

        if (empty($info)) {

            $db->insert('engine4_core_pages', array(
                'name' => 'grandopening',
                'displayname' => 'Grand Opening',
                'url' => 'grandopening',
                'title' => 'Grand Opening Page',
                'description' => 'This page is displayed when Grand Opening plugin is enabled.',
                'custom' => 0,
                'layout' => 'default-simple'
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 6,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // middle column
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'grandopening.countdown-timer',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"title":"Site will be launched in","name":"grandopening.countdown-timer"}',
            ));

            // middle column
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'grandopening.email-form',
                'parent_content_id' => $middle_id,
                'order' => 6,
                'params' => '["[]"]',
            ));

            // middle column
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.menu-logo',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '',
            ));

            // middle column
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.html-block',
                'parent_content_id' => $middle_id,
                'order' => 5,
                'params' => '{"title":"An introduction text","data":"We are currently building a new site which will be ready soon.","name":"core.html-block"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'whcore.share-social',
                'parent_content_id' => $middle_id,
                'order' => 999,
                'params' => '{"title":""}',
            ));
        }
    }

}

?>