<?php

class Whmedia_ProjectController extends Core_Controller_Action_Standard
{
    
  protected $_media;
    
  public function init()
  {
    if ($this->getRequest()->getActionName() == 'create') return;
    if( 0 !== ($project_id = (int) $this->_getParam('project_id')) &&
        null !== ($project = Engine_Api::_()->getItem('whmedia_project', $project_id)) ) {
      Engine_Api::_()->core()->setSubject($project);
    }

    $isAjax = (bool)$this->_getParam('isajax', false);

    if( !$isAjax) {
        if (!$this->_helper->requireSubject('whmedia_project')->isValid()) return;
        if (!$this->_helper->requireUser()->isValid() ) return;
        $viewer = Engine_Api::_()->user()->getViewer();
        if(!$project->isOwner($viewer)) 
            return $this->_helper->requireAuth();
    }
    else {
        try {

            if (!$this->_helper->requireSubject('whmedia_project')->setNoForward()->isValid())
                throw new Engine_Exception('Incorrect project.');
            if( !$this->_helper->requireUser()->setNoForward()->isValid() )
                throw new Engine_Exception('User require.');

            $viewer = Engine_Api::_()->user()->getViewer();
            if(!$project->isOwner($viewer))
                throw new Engine_Exception('You do not have permission to view this private page.');
            if (in_array($this->getRequest()->getActionName(), array('delmedia', 'editmediatitle', 'setcover', 'edit-text', 'get-media-content'))) {
                $media_id = (int)$this->_getParam('media_id');
                if (!$media_id)
                    throw new Engine_Exception('Incorrect media id.');
                $media = Engine_Api::_()->getItem('whmedia_media', $media_id);
                if ($media === null)
                    throw new Engine_Exception('Incorrect media id.');
                if ($media->project_id != Engine_Api::_()->core()->getSubject()->project_id)
                    throw new Engine_Exception('Incorrect project.');
                $this->_media = $media;
             }
        }
        catch (Exception $e) {
            $this->_helper->json(array('status' => false,
                                       'error' => Zend_Registry::get('Zend_Translate')->_($e->getMessage()) ));
            return;
        }

    }

    $this->view->navigation = $this->_getNavigation();
    $this->view->pageTitle = $this->view->translate ('Manage Project');
    $this->view->filters = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('whfilters');

  }
 
  public function createAction() {
      if( !$this->_helper->requireAuth()->setAuthParams('whmedia_project', null, 'create')->isValid()) return;

      $this->view->form = $form = new Whmedia_Form_Create();
      if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

          $projectTable = Engine_Api::_()->getDbtable('projects', 'whmedia');;
          $values = $form->getValues();
          $viewer = Engine_Api::_()->user()->getViewer();

          // Begin database transaction
          $db = $projectTable->getAdapter();
          $db->beginTransaction();

          try
          {

            $projectTableRow = $projectTable->createRow();
            $projectTableRow->setFromArray($values);
            $projectTableRow->user_id = $viewer->getIdentity();
            $projectTableRow->owner_type = $viewer->getType();
            $projectTableRow->save();

            // Auth
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            if( empty($values['auth_view']) ) {
              $values['auth_view'] = 'everyone';
            }

            if( empty($values['auth_comment']) ) {
              $values['auth_comment'] = 'everyone';
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach( $roles as $i => $role ) {
              $auth->setAllowed($projectTableRow, $role, 'view', ($i <= $viewMax));
              $auth->setAllowed($projectTableRow, $role, 'comment', ($i <= $commentMax));
            }
            $auth->setAllowed($projectTableRow, 'everyone', 'allow_d_orig', (isset($form->allow_download_original) and (bool)$form->allow_download_original->getValue()));
            // Add tags
            $tags = array_filter(preg_split('/[,]+/', $values['whtags']), "trim");
            if (count($tags)) $projectTableRow->tags()->addTagMaps($viewer, $tags);
            
            $db->commit();
          }

          catch( Exception $e )
          {
            $db->rollBack();
            throw $e;
          }

          $this->_helper->redirector->gotoRoute(array('project_id' => $projectTableRow->project_id), 'add_whmedia', true);
    }
  }

  public function uploadAction() {

    $translate = Zend_Registry::get('Zend_Translate');
    try {
        
        
        if( !$this->getRequest()->isPost() ) {
           throw new Engine_Exception('Invalid request method');
        }

        $values = $this->getRequest()->getPost();

        if( empty($values['Filename']) )
        {
          throw new Engine_Exception('No file');
        }
        if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
        {
          throw new Engine_Exception('Invalid Upload or file too large');
        }
        $max_files = (($MediasCount = Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'whmedia_project', 'medias_count')) > 0)
                                                                        ? (($MediasCount - Engine_Api::_()->core()->getSubject()->getMediasCount(array('is_text = 0'))) > 0)
                                                                        : true;
        if (!$max_files)
            throw new Engine_Exception('You cann\'t upload more files.');

    }
    catch (Exception $e) {
        $this->_helper->json(array('status' => false,
                                   'error' => $translate->_($e->getMessage()) ));
        return;
    }

    $order = Zend_Json::decode($this->_getParam('order'));
    $project_medias = Engine_Api::_()->core()->getSubject()->getMedias();
    try
    {
      $file_id = Engine_Api::_()->whmedia()->uploadmedia($_FILES['Filedata']);
      $this->view->media = $media = Engine_Api::_()->getItem('whmedia_media', $file_id);
      if (Engine_Api::_()->core()->getSubject()->is_published) {
        $wh_session = new Zend_Session_Namespace('whmedia_new_media');
        $session_key = 'activity_' . Engine_Api::_()->core()->getSubject()->getIdentity();
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        if (!isset ($wh_session->$session_key)) {
            $wh_session->$session_key = $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), Engine_Api::_()->core()->getSubject(), 'whmedia_media_new', null);
        }
        else {
            $action = $wh_session->$session_key;
        }
        $api->attachActivity($action, $media, Activity_Model_Action::ATTACH_MULTI);
      }
      $media->order = array_search('current', $order);
      $media->save();
      
      foreach ($project_medias as $project_media) {               
        $project_media->order = array_search('whmedia_' . $project_media->getIdentity(), $order);                                    
        $project_media->save();
      }
      

      $this->_helper->json(array('status' => true,
                                 'html' => $this->view->render('project/_media_embedded.tpl'),
                                 'name' => $_FILES['Filedata']['name'],
                                 'media_id' => $file_id ));
    }

    catch( Exception $e )
    {
      $this->_helper->json(array('status' => false,
                                 'error' => $translate->_("DataBase error.") ));
      return;
    }

  }

  public function delmediaAction() {
      $translate = Zend_Registry::get('Zend_Translate');
      try {
          $this->_media->delete();
      }
      catch (Exception $e) {
        $this->_helper->json(array('status' => false,
                                   'error' => $translate->_($e->getMessage()) ));
        return;
    }
    $this->_helper->json(array('status' => true));
  }

  public function editAction() {
      if( !$this->_helper->requireAuth()->setAuthParams('whmedia_project', null, 'create')->isValid()) return;
      $project = Engine_Api::_()->core()->getSubject();
      $this->view->form = $form = new Whmedia_Form_Create();
      $this->view->form_cover = $form_cover = new Whmedia_Form_Cover();

      $form->setDescription(Zend_Registry::get('Zend_Translate')->_('You can edit project details'))
         ->setTitle('Edit Project')
         ->populate($project->toArray())
         ->submit->setLabel("Save");

      $tagStr = '';
      foreach( $project->tags()->getTagMaps() as $tagMap ) {
          $tag = $tagMap->getTag();
          if( !isset($tag->text) ) continue;
          if( '' !== $tagStr ) $tagStr .= ', ';
          $tagStr .= $tag->text;
      }
      $form->whtags->setValue($tagStr);
      $auth = Engine_Api::_()->authorization()->context;

      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      foreach( $roles as $role ) {
          if ($form->auth_view){
            if( $auth->isAllowed($project, $role, 'view') ) {
             $form->auth_view->setValue($role);
            }
          }

          if ($form->auth_comment){
            if( $auth->isAllowed($project, $role, 'comment') ) {
              $form->auth_comment->setValue($role);
            }
          }
      }
      if (isset ($form->allow_download_original)) {
          $form->allow_download_original->setValue($auth->isAllowed($project, 'everyone', 'allow_d_orig'));
      }
      if ( $this->getRequest()->isPost() ) {
          if ( $this->getRequest()->getPost('task') == 'save_project' && $form->isValid($this->getRequest()->getPost()) ) {

              $projectTable = Engine_Api::_()->getDbtable('projects', 'whmedia');;
              $values = $form->getValues();
              $viewer = Engine_Api::_()->user()->getViewer();

              // Begin database transaction
              $db = $projectTable->getAdapter();
              $db->beginTransaction();

              try
              {

                $projectTableRow = $project;
                $projectTableRow->setFromArray($values);
                $projectTableRow->save();

                // Auth
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if( empty($values['auth_view']) ) {
                  $values['auth_view'] = 'everyone';
                }

                if( empty($values['auth_comment']) ) {
                  $values['auth_comment'] = 'everyone';
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach( $roles as $i => $role ) {
                  $auth->setAllowed($projectTableRow, $role, 'view', ($i <= $viewMax));
                  $auth->setAllowed($projectTableRow, $role, 'comment', ($i <= $commentMax));
                }
                $auth->setAllowed($projectTableRow, 'everyone', 'allow_d_orig', (isset($form->allow_download_original) and (bool)$form->allow_download_original->getValue()));
                // Add tags
                $tags = array_filter(preg_split('/[,]+/', $values['whtags']), "trim");
                $projectTableRow->tags()->setTagMaps($viewer, $tags);

                $db->commit();
              }

              catch( Exception $e )
              {
                $db->rollBack();
                throw $e;
              }

              $form->addNotice('Your changes have been saved.');
        }
        if ( $this->getRequest()->getPost('task') == 'upload_cover' && $form_cover->isValid($this->getRequest()->getPost()) && $form_cover->cover->isUploaded()) {
            $form_cover->cover->receive();
            $file_id = Engine_Api::_()->whmedia()->uploadmedia($form_cover->cover, true);
            $project->setCover(Engine_Api::_()->getItem('whmedia_media', $file_id));
        }
    }
  }

  public function editmediatitleAction() {
     try {
         $this->_media->title = trim($this->_getParam('mediatitle', ''));
         $this->_media->save();
     }
      catch (Exception $e) {
        $this->_helper->json(array('status' => false,
                                   'error' => Zend_Registry::get('Zend_Translate')->_($e->getMessage()) ));
        return;
    }
    $this->_helper->json(array('status' => true));
  }

  public function setcoverAction() {
     try {
         Engine_Api::_()->core()->getSubject()->setCover($this->_media);
     }
     catch (Exception $e) {
         $this->_helper->json(array('status' => false,
                                    'error' => Zend_Registry::get('Zend_Translate')->_($e->getMessage()) ));
         return;
     }
    $this->_helper->json(array('status' => true));
  }

  public function orderAction() {
    if (!$this->getRequest()->isPost()) { 
         $this->_helper->json(array('status' => false,
                                    'error' => Zend_Registry::get('Zend_Translate')->_('Invalid Data.') ));
        return;
    }
    $order = $this->getRequest()->getParam('order');
    if (!is_array($order) or !count($order)) {
        $this->_helper->json(array('status' => false,
                                   'error' => Zend_Registry::get('Zend_Translate')->_('Invalid Order Data.') ));
        return;
    }
        
    $medias = Engine_Api::_()->core()->getSubject()->getMedias();
    
    // Begin database transaction

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
        foreach ($medias as $media) {
            $media->order = array_search('whmedia_' . $media->getIdentity(), $order);
            $media->save();
        }
        $db->commit();
    }
    catch (Exception $e) {
        $db->rollBack();
        $this->_helper->json(array('status' => false,
                                   'error' => $translate->_($e->getMessage()) ));
        return;
    }
    $this->_helper->json(array('status' => true));
  }

  public function delprojectAction() {
      // Form
    $this->view->form = $form = new Whmedia_Form_Deleteproject();
    $CurrentContext = $this->_helper->contextSwitch->getCurrentContext();
    
    if( $this->getRequest()->isPost() and $form->isValid($this->getRequest()->getPost()) ) {

        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try
        {
          Engine_Api::_()->core()->getSubject()->delete();
          $db->commit();
          Engine_Api::_()->core()->clearSubject();
        }

        catch( Exception $e )
        {
          $db->rollBack();
          throw $e;
        }
        if ($CurrentContext == 'smoothbox') {
            if (($forward = $this->_getParam('forward', false)) == false) {
                  return $this->_forward('success', 'utility', 'core', array(
                                                                      'smoothboxClose' => true,
                                                                      'parentRefresh'=> true,
                                                                      'messages' => array('Project deleted.')
                                                                  ));
            }
            else {
                  return $this->_forward('success', 'utility', 'core', array(
                                                                  'smoothboxClose' => true,
                                                                  'parentRedirect'=> Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => $forward), 'whmedia_default', true),
                                                                  'messages' => array('Project deleted.')
                                                              ));  
            }
            
        }
        else
            return $this->_helper->redirector->gotoRoute(array('controller' => 'index',
                                                               'action' => 'manage'), 'whmedia_default', true);
    }
    if ($CurrentContext == 'smoothbox') {
        $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format' => 'smoothbox')));
        $form->cancel->setAttrib('onclick', 'javascript:parent.Smoothbox.close()');
        return $this->renderScript('project/delproject_smoothbox.tpl');
    }
  }

  public function videourlpreviewAction() {
    $this->_helper->layout->setLayout('default-simple');

    if (!$this->getRequest()->isPost()) {
        $this->view->error = 'Incorrect input data format.';
        $this->renderScript('etc/url_error.tpl');
        return;
    }
    $this->view->block_id = $block_id = (int)$this->_getParam('block_id');
    
    if (empty($block_id)) {
        $this->view->error = 'Incorrect input block id.';
        $this->renderScript('etc/url_error.tpl');
        return;
    }
    
    $this->view->url = $url = $this->getRequest()->getPost('url', '');
    if (!Zend_Uri::check($url)) {
        $this->view->error = 'Incorrect URL.';
        $this->renderScript('etc/url_error.tpl');
        return;
    }
    $this->view->video_info = $video_info = Engine_Api::_()->whmedia()->getVideoURL_info($url);
    if ($video_info['error'] !== false) {
        $this->view->error = $video_info['error'];
        $this->renderScript('etc/url_error.tpl');
        return;
    }
  }

  public function videourladdAction() {
     try {
         if( !$this->getRequest()->isPost()) {
            throw new Engine_Exception('Incorrect data method.');
         }
         $max_files = (($MediasCount = Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'whmedia_project', 'medias_count')) > 0)
                                                                        ? (($MediasCount - Engine_Api::_()->core()->getSubject()->getMediasCount(array('is_text = 0'))) > 0)
                                                                        : true;
        if (!$max_files)
            throw new Engine_Exception('You cann\'t upload more files.');

        $type = $this->getRequest()->getPost('type', '');
        $code = $this->getRequest()->getPost('code', '');
        $title = $this->getRequest()->getPost('title', '');
        if ($type == 'embed_ly') {
            $video_info = Engine_Api::_()->whmedia()->getVideoURL_info($code);
        }
        else {
            $video_info = Engine_Api::_()->whmedia()->getVideoURL_info(array('type' => $type,
                                                                             'code' => $code));
        }

        // Now try to create thumbnail
        
        if (!empty($video_info['thumbnail'])) {
            $thumbnail = $video_info['thumbnail'];
            
                    
            $ext = (Zend_Uri::check($thumbnail)) ? 'jpg' : ltrim(strrchr($thumbnail, '.'), '.');

            $tmp_file = APPLICATION_PATH . '/temporary/link_'.md5($thumbnail).'.'.$ext;
            $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_'.md5($thumbnail).'.'.$ext;

            $src_fh = fopen($thumbnail, 'r');
            $tmp_fh = fopen($tmp_file, 'w');
            stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
            $settings = Engine_Api::_()->getApi('settings', 'core');
            $image_width = $settings->getSetting('image_width', '600');
            $image_height = $settings->getSetting('image_height', '900');
            $image = Engine_Image::factory(array('quality' => 100));
            $image->open($tmp_file)
                  ->resize($image_width, $image_height)
                  ->write($thumb_file)
                  ->destroy();
        }

     }
     catch (Exception $e) {
         $this->_helper->json(array('status' => false,
                                    'error' => Zend_Registry::get('Zend_Translate')->_($e->getMessage()) ));
         return;
     }
     $order = $this->_getParam('order');
     $project_medias = Engine_Api::_()->core()->getSubject()->getMedias();
     // Process
     $db = Engine_Db_Table::getDefaultAdapter();
     $db->beginTransaction();

     try {
        $table_media = Engine_Api::_()->getItemTable('whmedia_media');
        $media_row = $table_media->createRow();
        $media_row->project_id = Engine_Api::_()->core()->getSubject()->project_id;
        $media_row->order = array_search('current', $order);
        if ($type == 'embed_ly') {
            $media_row->code = serialize(array('type' => $video_info['type'],
                                               'params' => $video_info['params']));
        }
        else {
            $media_row->code = serialize(array('type' => $video_info['type'],
                                               'code' => $video_info['code']));
        }
        if (trim($title))
            $media_row->title = trim($title);
        $media_row->save();
        
        foreach ($project_medias as $project_media) {               
            $project_media->order = array_search('whmedia_' . $project_media->getIdentity(), $order);                                    
            $project_media->save();
        }
        
        $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array('type' => 'thumb.etalon',
                                                                              'parent_type' => 'whmedia_media',
                                                                              'parent_id' => $media_row->media_id));
        $db->commit();
        // Remove temp file
        @unlink($thumb_file);
        @unlink($tmp_file);
     }
     catch( Exception $e ) {
         $db->rollBack();
         $this->_helper->json(array('status' => false,
                                    'error' => Zend_Registry::get('Zend_Translate')->_($e->getMessage()) ));
         return;
     }
     if (Engine_Api::_()->core()->getSubject()->is_published) {
        $wh_session = new Zend_Session_Namespace('whmedia_new_media');
        $session_key = 'activity_' . Engine_Api::_()->core()->getSubject()->getIdentity();
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        if (!isset ($wh_session->$session_key)) {
            $wh_session->$session_key = $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), Engine_Api::_()->core()->getSubject(), 'whmedia_media_new', null);
        }
        else {
            $action = $wh_session->$session_key;
        }
        $api->attachActivity($action, $media_row, Activity_Model_Action::ATTACH_MULTI);
     }
     $this->view->media = $media_row;
     $this->_helper->json(array('status' => true,
                                'html' => $this->view->render('project/_media_embedded.tpl'),
                                'media_id' => $media_row->media_id ));
  }

  public function indexAction() {
      $this->view->project = Engine_Api::_()->core()->getSubject();
      $this->view->categories = Engine_Api::_()->whmedia()->getCategories();
      $this->view->medias = Engine_Api::_()->core()->getSubject()->getMedias();
      $this->view->medias_count = Engine_Api::_()->core()->getSubject()->getMediasCount(array('is_text = 0'));
      $this->view->max_files = Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'whmedia_project', 'medias_count');
      $viewer = Engine_Api::_()->user()->getViewer();
      
      $file_types = Zend_Json::decode(Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'whmedia_project', 'file_type'));
      $type_name = '';
      $exts = '';
      $types_array = array();
      if (in_array('image', $file_types)) {
        $type_name .= 'images,';
        $exts .= '*.jpg; *.jpeg; *.gif; *.png; ';
        $types_array[] = 'Images (*.jpg; *.jpeg; *.gif; *.png)';
      }
      if (in_array('video', $file_types)) {
        $type_name .= 'videos,';
        $exts .= '*.mpeg; *.mp4; *.mkv; *.mpg; *.mpe; *.qt; *.mov; *.avi; ';
        $types_array[] = 'Videos (*.mpeg; *.mp4; *.mkv; *.mpg; *.mpe; *.qt; *.mov; *.avi)';
      }
      if (in_array('audio', $file_types)) {
        $type_name .= 'audios,';
        $exts .= '*.mp3; ';
        $types_array[] = 'Audios (*.mp3)';
      }
      if (in_array('pdf', $file_types)) {
        $type_name .= 'pdf,';
        $exts .= '*.pdf; ';
        $types_array[] = 'Portable Document Format (*.pdf)';
      }
      if (in_array('ppt', $file_types)) {
        $type_name .= 'ppt,';
        $exts .= '*.ppt; *.pptx; ';
        $types_array[] = 'PowerPoint (*.ppt; *.pptx)';
      }
      $type_name = rtrim($type_name, ',');
      $max_up = @ini_get('upload_max_filesize');
      $max_post = @ini_get('post_max_size');
      if ( ( (int) $max_up) < ( (int) $max_post ) ) {
        $fileSizeMax = $max_up;
      }
      else {
        $fileSizeMax = $max_post;
      }
      $fileSizeMax = trim($fileSizeMax);

      switch (strtolower(substr($fileSizeMax, -1))) {
          case 'g':
            $fileSizeMax = $fileSizeMax*1024*1024*1024;
            break;
          case 'm':
            $fileSizeMax = $fileSizeMax*1024*1024;
            break;
          case 'k':
            $fileSizeMax = $fileSizeMax*1024;
            break;
      }

      $this->view->file_types = "{'$type_name': '$exts'}";
      $this->view->fileSizeMax = $fileSizeMax;
      $this->view->file_types_array = $types_array;
      $language = $this->view->locale()->getLocale()->__toString();
      $languages = array(
                            'en', 'ar', 'ca', 'el', 'fr', 'hy', 'ka', 'ml', 'pl', 'si', 'te', 'vi',
                            'az', 'ch', 'gl', 'ia', 'kl', 'mn', 'ps', 'sk', 'th', 'zh', 'be', 'cs',
                            'es', 'gu', 'id', 'ko', 'ms', 'pt', 'sl', 'tr', 'zu', 'bg', 'cy', 'et',
                            'he', 'ii', 'lb', 'nb', 'ro', 'sq', 'tt', 'bn', 'da', 'eu', 'hi', 'is',
                            'lt', 'nl', 'ru', 'sr', 'tw', 'br', 'de', 'fa', 'hr', 'it', 'lv', 'nn',
                            'sc', 'sv', 'uk', 'bs', 'dv', 'fi', 'hu', 'ja', 'mk', 'no', 'se', 'ta',
                            'ur',
                         );
      if( !in_array($language, $languages) ) {
        list($language) = explode('_', $language);
        if( !in_array($language, $languages) ) {
              $this->view->language = 'en'; 
        }
        else {
            $this->view->language = $language; 
        }
      }
      else {
          $this->view->language = $language; 
      }
      
  }

  public function addTextAction() {
      if (!$this->getRequest()->isPost()) { 
         $this->_helper->json(array('status' => false,
                                    'error' => Zend_Registry::get('Zend_Translate')->_('Invalid Data.') ));
        return;
      }
      $body = $this->_getParam('body');
      $order = $this->_getParam('order');
      $textarea = new Zend_Form_Element_Textarea('body', array(
                                                                'filters' => array( 'StringTrim',
                                                                                    new Engine_Filter_Censor(),
                                                                                    new Engine_Filter_Html())
                                                              ));

      if ($textarea->isValid($body)) {
          $table_media = Engine_Api::_()->getItemTable('whmedia_media');
          // Begin database transaction
          $db = Engine_Db_Table::getDefaultAdapter();
          $db->beginTransaction();
          $project_medias = Engine_Api::_()->core()->getSubject()->getMedias();
          try {
            $media_row = $table_media->createRow();
            $media_row->project_id = Engine_Api::_()->core()->getSubject()->project_id;
            $media_row->description = $textarea->getValue();
            $media_row->title = '';
            $media_row->order = array_search('current', $order);
            $media_row->is_text = 1;
            $media_row->save();

            foreach ($project_medias as $project_media) {               
                $project_media->order = array_search('whmedia_' . $project_media->getIdentity(), $order);                                    
                $project_media->save();
            }
            $db->commit();
          }
          catch (Exception $e) {
            $db->rollBack();
            $this->_helper->json(array('status' => false,
                                       'error' => $translate->_($e->getMessage()) ));
            return;
          }
          $this->view->media = $media_row;
          $this->_helper->json(array('status' => true,
                                     'id' => $media_row->getIdentity(),
                                     'html' => $this->view->render('project/_media_embedded.tpl') ));
      }
      else {
          $this->_helper->json(array('status' => false,
                                     'error' => $this->view->translate("Invalid text data") ));
          return;
      }
  }
  
  public function publishAction() {
    $project = Engine_Api::_()->core()->getSubject();  
    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
        $project->is_published = ((int)$this->_getParam('is_published')) ? 1 : 0;
        $project->save();
        if ($project->is_published) {
            Engine_Api::_()->getDbtable('stream', 'whmedia')->addStream($project);
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            $select = $api->select()->where('object_type = ?', $project->getType())
                                    ->where('object_id = ?', $project->getIdentity())
                                    ->where("type = 'whmedia_project_publish'");
     
            $action = $api->fetchAll($select);

            if ($action->count() == 0) {
                unset($action);
                $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), Engine_Api::_()->core()->getSubject(), 'whmedia_project_publish', null);
                $medias = Engine_Api::_()->core()->getSubject()->getMedias(array('is_text = 0'));
                $i = 0;
                foreach ($medias as $media) {
                    if ($i > 3) break;
                    $api->attachActivity($action, $media, Activity_Model_Action::ATTACH_MULTI);
                    $i++;
                }
            }
        }
        $db->commit();
        Engine_Api::_()->core()->clearSubject();
    }

    catch( Exception $e ) {
        $db->rollBack();
        return $this->_helper->json(array('status' => false,
                                          'error' => $translate->_($e->getMessage()) ));
    }
    return $this->_helper->json(array('status' => true));
  }

  public function editTextAction() {
      $body = $this->_getParam('body');
      $textarea = new Zend_Form_Element_Textarea('body', array(
                                                                'filters' => array( 'StringTrim',
                                                                                    new Engine_Filter_Censor(),
                                                                                    new Engine_Filter_Html())
                                                              ));
      if ($textarea->isValid($body)) {
        try {
            $this->_media->description = $textarea->getValue();
            $this->_media->save();
        }
        catch (Exception $e) {
            $this->_helper->json(array('status' => false,
                                       'error' => $this->view->translate($e->getMessage()) ));
            return;
        }
      }
      else {
          $this->_helper->json(array('status' => false,
                                     'error' => $this->view->translate("Invalid text data") ));
          return;
      }
      $this->_helper->json(array('status' => true));
  }
  
  public function getMediaContentAction() {
      $this->view->media = $this->_media;
      $this->_helper->json(array('status' => true,
                                 'html' => $this->view->render('project/_media_embedded.tpl'),                                 
                                 'media_id' => $file_id ));
      return;
  }

  public function getUrlContentAction() {

    if (!$this->getRequest()->isPost()) {
        $this->view->error = 'Incorrect input data format.';
        $this->renderScript('etc/url_error.tpl');
        return;
    }
    $this->view->block_id = $block_id = (int)$this->_getParam('block_id');

    if (empty($block_id)) {
        $this->view->error = 'Incorrect input block id.';
        $this->renderScript('etc/url_error.tpl');
        return;
    }

    $this->view->url = $url = $this->getRequest()->getPost('url', '');
    if (!Zend_Uri::check($url)) {
        $this->view->error = 'Incorrect URL.';
        $this->renderScript('etc/url_error.tpl');
        return;
    }
    
  }

  public function saveUrlAction() {
      try {
         if( !$this->getRequest()->isPost()) {
            throw new Engine_Exception('Incorrect data method.');
         }
         $max_files = (($MediasCount = Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'whmedia_project', 'medias_count')) > 0)
                                                                        ? (($MediasCount - Engine_Api::_()->core()->getSubject()->getMediasCount(array('is_text = 0'))) > 0)
                                                                        : true;
        if (!$max_files)
            throw new Engine_Exception('You cann\'t upload more files.');

        $url = $this->getRequest()->getPost('url', '');
        if (!Zend_Uri::check($url)) {
            throw new Engine_Exception('Incorrect URL.');
        }
        $thumbnail = $this->getRequest()->getPost('thumb');
        if (!empty($thumbnail) and Zend_Uri::check($thumbnail)) {
            // Now try to create thumbnail
            $ext = ltrim(strrchr($thumbnail, '.'), '.');
            $tmp_file = APPLICATION_PATH . '/temporary/link_'.md5($thumbnail).'.'.$ext;
            $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_'.md5($thumbnail).'.'.$ext;

            $src_fh = fopen($thumbnail, 'r');
            $tmp_fh = fopen($tmp_file, 'w');
            stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
            $settings = Engine_Api::_()->getApi('settings', 'core');
            $image_width = $settings->getSetting('image_width', '600');
            $image_height = $settings->getSetting('image_height', '900');
            $image = Engine_Image::factory(array('quality' => 100));
            $image->open($tmp_file)
                  ->resize($image_width, $image_height)
                  ->write($thumb_file)
                  ->destroy();
        }

     }
     catch (Exception $e) {
         $this->_helper->json(array('status' => false,
                                    'error' => Zend_Registry::get('Zend_Translate')->_($e->getMessage()) ));
         return;
     }
     $order = $this->_getParam('order');
     $project_medias = Engine_Api::_()->core()->getSubject()->getMedias();
     $title = $this->getRequest()->getPost('title');
     $description = $this->getRequest()->getPost('description');
     // Process
     $db = Engine_Db_Table::getDefaultAdapter();
     $db->beginTransaction();

     try {
        $table_media = Engine_Api::_()->getItemTable('whmedia_media');
        $media_row = $table_media->createRow();
        $media_row->project_id = Engine_Api::_()->core()->getSubject()->project_id;
        $media_row->order = array_search('current', $order);
        $media_row->is_url = $url;
        if (trim($title))
            $media_row->title = trim($title);
        if (trim($description))
            $media_row->description = trim($description);
        $media_row->save();
        
        foreach ($project_medias as $project_media) {               
            $project_media->order = array_search('whmedia_' . $project_media->getIdentity(), $order);                                    
            $project_media->save();
        }
        if (!empty($thumb_file)) {
            Engine_Api::_()->storage()->create($thumb_file, array('type' => 'thumb.etalon',
                                                                  'parent_type' => 'whmedia_media',
                                                                  'parent_id' => $media_row->media_id));
            @unlink($thumb_file);
            @unlink($tmp_file);
        }
        $db->commit();
        // Remove temp file
        
     }
     catch( Exception $e ) {
         $db->rollBack();
         $this->_helper->json(array('status' => false,
                                    'error' => Zend_Registry::get('Zend_Translate')->_($e->getMessage()) ));
         return;
     }

     $this->view->media = $media_row;
     $this->_helper->json(array('status' => true,
                                'html' => $this->view->render('project/_media_embedded.tpl'),
                                'media_id' => $media_row->media_id ));
  }

  private function _getNavigation()
  {
      return Engine_Api::_()->whmedia()->getManageNavigation(Engine_Api::_()->core()->getSubject());
  }

}
