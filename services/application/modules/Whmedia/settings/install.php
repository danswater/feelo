<?php

class Whmedia_Installer extends Engine_Package_Installer_Module {
    
  public function onPreInstall() {
      parent::onPreInstall();
      $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'whmedia_cache';
      if( !is_dir($tmpDir) ) {
          if( !mkdir($tmpDir, 0777, true) ) {
              return $this->_error('Media cache directory did not exist and could not be created.');
          }
      }
      if( !is_writable($tmpDir) ) {
          return $this->_error('Media cache directory is not writable.');
      }
  }

  public function onInstall() {
        parent::onInstall();
        $this->_addBrowsePage();
        $this->_addManagePage();
        $this->_addViewPage();
        $this->_addPopularPage();
        $this->_addLivefeedPage();
        $this->_addActivityFeedPage();
        if( !empty($this->_currentVersion) and version_compare($this->_currentVersion, '4.2.7', '<') ) {
            $this->_update427();
        }
  }

  private function _getStructure(array $option) {
        $db     = $this->getDb();
        $db->insert('engine4_core_pages', $option);
        $page_id = $db->lastInsertId('engine4_core_pages');

          // Insert top
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'top',
            'page_id' => $page_id,
            'order' => 1
          ));
        $top_id = $db->lastInsertId();

          // Insert main
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'container',
            'name' => 'main',
            'order' => 2
          ));
        $main_id = $db->lastInsertId('engine4_core_content');

        // Insert top-middle
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $top_id,
          ));
        $top_middle_id = $db->lastInsertId();

        // Insert main-middle
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $main_id,
            'order' => 2,
          ));
        $main_middle_id = $db->lastInsertId();

        // Insert main-right
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'right',
            'page_id' => $page_id,
            'parent_content_id' => $main_id,
            'order' => 1
          ));
        $main_right_id = $db->lastInsertId();

        // Insert menu
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'whmedia.browse-menu',
            'page_id' => $page_id,
            'parent_content_id' => $top_middle_id,
            'order' => 1
        ));

        // middle column
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'core.content',
            'parent_content_id' => $main_middle_id,
            'order' => 1
        ));

        return array('page_id' => $page_id,
                     'top_id' => $top_id,
                     'main_id' => $main_id,
                     'top_middle_id' => $top_middle_id,
                     'main_middle_id' => $main_middle_id,
                     'main_right_id' => $main_right_id);
    }

  private function _pageEmpty($name) {
        $db     = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
          ->from('engine4_core_pages')
          ->where('name = ?', $name)
          ->limit(1);
          ;
        $info = $select->query()->fetch();
        return empty($info);
  }

  private function _addBrowsePage() {

        if($this->_pageEmpty('whmedia_index_index')) {
            $db     = $this->getDb();
            $structure = $this->_getStructure(array('name' => 'whmedia_index_index',
                                                    'displayname' => 'Media Plugin: Browse Page',
                                                    'title' => 'Media Plugin: Browse Page',
                                                    'description' => 'Show all media projects on your site.',
                                                    'custom' => 0));
            // Add widget 'Q&A Browse Search'
            $db->insert('engine4_core_content', array('page_id' => $structure['page_id'],
                                                      'type' => 'widget',
                                                      'name' => 'whmedia.browse-search',
                                                      'parent_content_id' => $structure['main_right_id'],
                                                      'order' => 1
                                                      ));

            // Add widget 'Create Project'
            $db->insert('engine4_core_content', array('page_id' => $structure['page_id'],
                                                      'type' => 'widget',
                                                      'name' => 'whmedia.create-project',
                                                      'parent_content_id' => $structure['main_right_id'],
                                                      'order' => 2
                                                      ));

        }

  }

  private function _addManagePage() {

        if($this->_pageEmpty('whmedia_index_manage')) {
            $db     = $this->getDb();
            $structure = $this->_getStructure(array('name' => 'whmedia_index_manage',
                                                    'displayname' => 'Media Plugin: Manage Projects',
                                                    'title' => 'Media Plugin: Manage Projects',
                                                    'description' => 'Show members of theirs media projects.',
                                                    'custom' => 0));
            // Add widget 'Q&A Browse Search'
            $db->insert('engine4_core_content', array('page_id' => $structure['page_id'],
                                                      'type' => 'widget',
                                                      'name' => 'whmedia.browse-search',
                                                      'parent_content_id' => $structure['main_right_id'],
                                                      'order' => 1
                                                      ));

            // Add widget 'Create Project'
            $db->insert('engine4_core_content', array('page_id' => $structure['page_id'],
                                                      'type' => 'widget',
                                                      'name' => 'whmedia.create-project',
                                                      'parent_content_id' => $structure['main_right_id'],
                                                      'order' => 2
                                                      ));

        }

  }

  private function _addViewPage() {

        if($this->_pageEmpty('whmedia_index_view')) {
            $db     = $this->getDb();
            $structure = $this->_getStructure(array('name' => 'whmedia_index_view',
                                                    'displayname' => 'Media Plugin: View a project',
                                                    'title' => 'Media Plugin: View a project',
                                                    'description' => 'Show page view a media projects.',
                                                    'custom' => 0));
            // Add widget 'Social Sharing'
            $db->insert('engine4_core_content', array('page_id' => $structure['page_id'],
                                                      'type' => 'widget',
                                                      'name' => 'whmedia.share-social',
                                                      'parent_content_id' => $structure['main_middle_id'],
                                                      'order' => 2,
                                                      'params'  => '{"title":"Share with Social"}',
                                                      ));
            // Add widget 'Projects slider'
            $db->insert('engine4_core_content', array('page_id' => $structure['page_id'],
                                                      'type' => 'widget',
                                                      'name' => 'whmedia.projects-slider',
                                                      'parent_content_id' => $structure['main_middle_id'],
                                                      'order' => 3,
                                                      'params'  => '{"title":"User projects scroller","titleCount":false,"count_item":"6","nomobile":"0"}'
                                                      ));
            // Add widget 'Core Comments'
            $db->insert('engine4_core_content', array('page_id' => $structure['page_id'],
                                                      'type' => 'widget',
                                                      'name' => 'core.comments',
                                                      'parent_content_id' => $structure['main_middle_id'],
                                                      'order' => 4
                                                      ));

        }

  }
  
  private function _update427() {
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select->from('engine4_whmedia_projects')
           ->where("`description` != ''") ;
    $projects = $select->query()->fetchAll();

    foreach ($projects as $project) {
        $db->update('engine4_whmedia_medias', array('order' => new Zend_Db_Expr("`order` + 2")), array('project_id = ?' => $project['project_id']));
        $db->insert('engine4_whmedia_medias', array('project_id' => $project['project_id'],
                                                    'description' => $project['description'],
                                                    'is_text' => 1,
                                                    'order' => 1));
    }
  }
  
  private function _addPopularPage() {

        if($this->_pageEmpty('whmedia_index_popular')) {
            $db     = $this->getDb();
            
            $option = array('name' => 'whmedia_index_popular',
                            'displayname' => 'Media Plugin: Popular projects',
                            'title' => 'Media Plugin: Popular projects',
                            'description' => 'Show page popular projects.',
                            'custom' => 0);
            
            $db->insert('engine4_core_pages', $option);
            $page_id = $db->lastInsertId('engine4_core_pages');
            
            // Insert main
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'order' => 1
              ));
            $main_id = $db->lastInsertId('engine4_core_content');

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
              ));
            $main_middle_id = $db->lastInsertId();

            // middle column
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.content',
                'parent_content_id' => $main_middle_id,
                'order' => 1
            ));         

        }

  }
  
  private function _addLivefeedPage() {

        if($this->_pageEmpty('whmedia_index_livefeed')) {
            $db     = $this->getDb();
            
            $option = array('name' => 'whmedia_index_livefeed',
                            'displayname' => 'Media Plugin: Livefeed',
                            'title' => 'Media Plugin: Livefeed',
                            'description' => 'Show page livefeed.',
                            'custom' => 0);
            
            $db->insert('engine4_core_pages', $option);
            $page_id = $db->lastInsertId('engine4_core_pages');
            
            // Insert main
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'order' => 1
              ));
            $main_id = $db->lastInsertId('engine4_core_content');

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
              ));
            $main_middle_id = $db->lastInsertId();

            // middle column
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.content',
                'parent_content_id' => $main_middle_id,
                'order' => 1
            ));         

        }

  }
  
  private function _addActivityFeedPage() {

        if($this->_pageEmpty('whmedia_index_activity-feed')) {
            $db     = $this->getDb();
            
            $option = array('name' => 'whmedia_index_activity-feed',
                            'displayname' => 'Media Plugin: Activity Feed',
                            'title' => 'Media Plugin: Activity Feed',
                            'description' => 'Show page activity feed.',
                            'custom' => 0);
            
            $db->insert('engine4_core_pages', $option);
            $page_id = $db->lastInsertId('engine4_core_pages');
            
            // Insert main
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'order' => 1
              ));
            $main_id = $db->lastInsertId('engine4_core_content');

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
              ));
            $main_middle_id = $db->lastInsertId();

            // middle column
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'mediamasonry.activity-feed-lazy',
                'parent_content_id' => $main_middle_id,
                'order' => 1,
                'params' => '{"title":"Activity Feed","titleCount":false,"thumb_width":"160","itemCountPerPage":"8","nomobile":"0","name":"mediamasonry.activity-feed-lazy"}'
            ));         

        }

  }
}
?>