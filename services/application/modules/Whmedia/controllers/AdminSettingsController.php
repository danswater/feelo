<?php

class Whmedia_AdminSettingsController extends Whmedia_controllers_AdminController
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('whmedia_admin_main', array(), 'whmedia_admin_main_settings');

    $this->view->form = $form = new  Whmedia_Form_Admin_Global();

    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))  {
        $values = $form->getValues();

        // Check ffmpeg path
        if( !empty($values['whvideo_ffmpeg_path']) ) {
          if( function_exists('exec') ) {
            $ffmpeg_path = $values['whvideo_ffmpeg_path'];
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);
            if( $return > 0 ) {
              $form->whvideo_ffmpeg_path->addError("FFMPEG path ({$values['whvideo_ffmpeg_path']}) is not valid or does not exist");
              $values['whvideo_ffmpeg_path'] = '';
            }
          } else {
            $form->whvideo_ffmpeg_path->addError('The exec() function is not available. The ffmpeg path has not been saved.');
            $values['whvideo_ffmpeg_path'] = '';
          }
        }
        // Check flvtool2 path
        if( !empty($values['whvideo_flvtool2_path']) ) {
          if( function_exists('exec') ) {
            $flvtool_path = $values['whvideo_flvtool2_path'];
            $output = null;
            $return = null;
            exec($flvtool_path . ' -version', $output, $return);
            if( $return > 0 ) {
              $form->whvideo_flvtool2_path->addError("FLVtool2 path ({$values['whvideo_flvtool2_path']}) is not valid or does not exist");
              $values['whvideo_flvtool2_path'] = '';
            }
          } else {
            $form->whvideo_flvtool2_path->addError('The exec() function is not available. The flvtool2 path has not been saved.');
            $values['whvideo_flvtool2_path'] = '';
          }
        }
        // Check FAAD path
        if( !empty($values['whvideo_flvtool2_faad']) ) {
          if( function_exists('exec') ) {
            $faad_path = $values['whvideo_flvtool2_faad'];
            $output = null;
            $return = null;
            exec($faad_path . ' -h', $output, $return);
            if( $return != 1 ) {
              $form->whvideo_flvtool2_path->addError("FAAD path ({$values['whvideo_flvtool2_faad']}) is not valid or does not exist");
              $values['whvideo_flvtool2_faad'] = '';
            }
          } else {
            $form->whvideo_flvtool2_path->addError('The exec() function is not available. The FAAD path has not been saved.');
            $values['whvideo_flvtool2_faad'] = '';
          }
        }
        // Check Embed.ly API key
        if( !empty($values['embed_ly_key']) ) {
          $client = new Zend_Http_Client('http://api.embed.ly/1/oembed?key='.$values['embed_ly_key'], array('maxredirects' => 0));
          $response = $client->request(Zend_Http_Client::GET);
          if ($response->getStatus() == 401) {
              $form->embed_ly_key->addError("Embed.ly API key ({$values['embed_ly_key']}) is not valid.");
              $values['embed_ly_key'] = '';
          }

        }
        
        // Check ffmpeg path
        if( $values['both_video_format'] ) {
          if (empty($values['whvideo_ffmpeg_path']) or !trim($values['whvideo_ffmpeg_path'])) {
              $values['both_video_format'] = 0;
              $form->both_video_format->addError('The ffmpeg function is not configured.');
          }
          else {
              $output = null;
              $output = shell_exec($values['whvideo_ffmpeg_path'] . ' -version 2>&1 | grep "enable-libx264"');
              if( !trim($output)) {
                  $values['both_video_format'] = 0;
                  $form->both_video_format->addError('Ffmpeg configured without enable mp4 codec.');
              }
          }
        }
        
        switch ($form->mime_info_method) {
            case 1:
                if (!function_exists('mime_content_type')) {
                    $form->mime_info_method->addError("PHP function 'mime_content_type' was not found.");
                }

                break;

            case 2:
                if (!function_exists('finfo_file')) {
                    $form->mime_info_method->addError("PHP function 'finfo_file' was not found.");
                }
                
                break;
            
            case 3:
                if (function_exists('exec')) {
                    $output = null;
                    $return = null;
                    exec('file -v', $output, $return);
                    if( $return == 1 ) {
                        break;
                    }                    
                }
                $form->mime_info_method->addError("External shell command 'file' can not be used.");
                break;
        }
      $setting_tmp = Engine_Api::_()->getApi('settings', 'core');
      foreach ($values as $key => $value){
          if ($key == 'video_resolution') {
              switch ($value) {
                  case 2:
                      $video_width = 480;
                      $video_height = 360;
                      break;
                  case 3:
                      $video_width = 640;
                      $video_height = 480;
                      break;
                  case 4:
                      $video_width = 480;
                      $video_height = 270;
                      break;
                  case 5:
                      $video_width = 640;
                      $video_height = 360;
                      break;
                  case 1:
                  default :
                      $video_width = 320;
                      $video_height = 240;
                      break;

              }
              $setting_tmp->setSetting('video_width', $video_width);
              $setting_tmp->setSetting('video_height', $video_height);
              
          }
          else $setting_tmp->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
      if (function_exists('apache_get_modules')) {
          $output = apache_get_modules();
          if( !in_array('mod_flvx', $output)) {
              $form->addNotice('Warning! Streaming module flvx_module is not setup. Streaming for flv video will not work.');
          }
          if( !in_array('mod_h264_streaming', $output)) {
              $form->addNotice('Warning! Streaming module h264_streaming_module is not setup. Streaming for Ipad (mp4) video will not work.');
          }
      }
      else {
          $form->addNotice('Warning! PHP is not setup as apache module.');
      }
      if(!Engine_Api::_()->whmedia()->check_mime_info()) {
          $form->addNotice('Warning! Cann\'t check mime info. Members will not be able to upload audio and video files  Please, setup Fileinfo (http://www.php.net/manual/en/book.fileinfo.php)');
      }
    }
  }

  public function categoriesAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                                             ->getNavigation('whmedia_admin_main', array(), 'whmedia_admin_main_categories');
    $this->view->categories = Engine_Api::_()->whmedia()->getCategories();
  }

  
  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Whmedia_Form_Admin_Category();

    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // add category to the database
        // Transaction
        $table = Engine_Api::_()->getDbtable('categories', 'whmedia');

        $row = $table->createRow();
        $row->category_name = $values["label"];
        $row->url = $values["url"];
        $row->save();

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        if ($e->getCode() == 1062) {
            $form->url->addError('Duplicate entry.');
            $this->renderScript('admin-settings/form.tpl');
            return; 
        }
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-settings/form.tpl');
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->delete_title = 'Delete Media Category?';
    $this->view->delete_description = 'Are you sure that you want to delete this category? It will not be recoverable after being deleted.';
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $row = Engine_Api::_()->whmedia()->getCategory($id);
       
        $row->delete();

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('etc/delete.tpl');
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $form = $this->view->form = new Whmedia_Form_Admin_Category();
    
    // Must have an id
    if( !($id = $this->_getParam('id')) )
    {
      die('No identifier specified');
    }

    // Generate and assign form
    $category = Engine_Api::_()->whmedia()->getCategory($id);
    $form->setField($category);
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      // Ok, we're good to add field
      $values = $form->getValues();
      
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // edit category in the database
        // Transaction
        $row = Engine_Api::_()->whmedia()->getCategory($values["id"]);

        $row->category_name = $values["label"];
        $row->url = $values["url"];
        $row->save();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        if ($e->getCode() == 1062) {
            $form->url->addError('Duplicate entry.');
            $this->renderScript('admin-settings/form.tpl');
            return; 
        }
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    
    // Output
    $this->renderScript('admin-settings/form.tpl');
  }

  public function orderAction() {
    if (!$this->getRequest()->isPost()) { return; }
    $table = Engine_Api::_()->getDbtable('categories', 'whmedia');

    $params = $this->getRequest()->getParams();
    $cats = $table->fetchAll($table->select());

    foreach ($cats as $cat)
    {
      $cat->order = $this->getRequest()->getParam('step_' . $cat->category_id);
      $cat->save();
    }
    return;
  }

  public function delCacheAction() {
      // In smoothbox
    $this->view->delete_title = 'Clear Image Cache?';
    $this->view->delete_description = 'You can clean Media Plugins image cache. This operation will free disk space from obsolete thumbnails. Note, system will need to create new cache, so thumbnails may load slower for a first time.';
    // Check post
    if( $this->getRequest()->isPost()) {
        $dir = 'temporary/whmedia_cache/';
        $op_dir=opendir($dir);
        while( $file = readdir($op_dir )) {

            if($file != "." && $file != ".." && $file != 'index.php' ) {
                unlink ($dir.$file);
            }
        }
        closedir($dir);
        return $this->_forward('success', 'utility', 'core', array('messages' => $this->view->translate('Operation has been completed.'),
                                                                   'smoothboxClose' => true,
                                                                   'parentRefresh' => false,
                                                                  ));

    }
    $this->renderScript('etc/delete.tpl');
  }
}