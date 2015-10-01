<?php

class Whmedia_Api_Core extends Core_Api_Abstract
{
    protected $_manageNavigation;
    
    public function getCategories()
      {
        return Engine_Api::_()->getDbtable('categories', 'whmedia')->fetchAll(null, 'order ASC');
      }
  
   public function getCategory($category_id)
      {
        return $this->getCategories()->getRowMatching('category_id', $category_id);
      }

   public function isAdmin(User_Model_User $user) {
        // Not logged in, not an admin
        if( !$user->getIdentity() || empty($user->level_id) ) {
          return false;
        }

        // Check level
        $level = Engine_Api::_()->getItem('authorization_level', $user->level_id);
        if( $level->type == 'admin' || $level->type == 'moderator' ) {
          return true;
        }

        return false;
   }

   public function uploadmedia($file, $invisible = false) {
      $table_media = Engine_Api::_()->getItemTable('whmedia_media');
      $media_row = $table_media->createRow();
      $media_row->project_id = Engine_Api::_()->core()->getSubject()->project_id;
      if ($invisible)
          $media_row->invisible = 1;
      $media_row->save();
      // Get image info and resize
      if ($file instanceof Engine_Form_Element_File ) {
          $mainName = $file->getFileName();
          $file = array('tmp_name' => $mainName,
                        'name' => basename($mainName));
      }
      else {
          $name = basename($file['tmp_name']);
          $path = dirname($file['tmp_name']);
          $extension = ltrim(strrchr($file['name'], '.'), '.');
          $mainName  = $path.'/'.$name . '.' . $extension;
          rename($file['tmp_name'], $mainName);
      }
      // Store photos
      $photo_params = array(
        'parent_id'  => $media_row->media_id,
        'parent_type'=> 'whmedia_media',
      );

      try {
          $photoFile = Engine_Api::_()->getDbtable('files', 'storage')->createFile($mainName,  $photo_params);
         
          $photoFile->name = $file['name'];
          if( !$this->check_mime_info() ) {
              throw new Engine_Exception('Media type has not detected. Please contact admin.');
          }
          $content_mime_type = $this->get_content_mime_type($mainName);
          // Remove temp files
          @unlink($file['tmp_name']);
          @unlink($mainName);

          $photoFile->mime_major = $this->getTypeFile($content_mime_type);
          if ($photoFile->mime_minor == 'unknown') {
            $photoFile->mime_minor = $photoFile->extension;
          }
          $photoFile->save();

          $file_types = Zend_Json::decode(Engine_Api::_()->authorization()->getPermission(Engine_Api::_()->user()->getViewer()->level_id, 'whmedia_project', 'file_type'));
         
          if (!in_array($photoFile->mime_major, $file_types)) {
                throw new Engine_Exception('Media type is not permitted.');
          }
          $settings = Engine_Api::_()->getApi('settings', 'core');
          if ($photoFile->mime_major == 'video') {
              $media_row->encode = 1;
              $media_row->save();
              // Add to jobs
              Engine_Api::_()->getDbtable('jobs', 'core')->addJob('whmedia_encode', array(
                                                                                            'media_id' => $media_row->media_id,
                                                                                          ));
          }
          else if ($photoFile->mime_major == 'image') {
              $file_tmp = $photoFile->temporary();
              if (Engine_Api::_()->authorization()->isAllowed('whmedia_project', null, 'save_original')) {
                  $original_params = $photoFile->toArray();
                  unset ($original_params['storage_path'], $original_params['service_id'], $original_params['file_id']);
                  $original_params['type'] = 'original';
                  $original_params['parent_file_id'] = $photoFile->getIdentity();
                  $original_file = Engine_Api::_()->storage()->create($file_tmp, $original_params);
                  $original_file->name = $photoFile->name;
                  $original_file->mime_major = $content_mime_type;
                  $original_file->save();
              }
              $image = Engine_Image::factory(array('quality' => 100));
              $image->open($file_tmp);
              $settingHeight = $settings->getSetting('image_height', '900');
              $settingWidth = $settings->getSetting('image_width', '720');
              if ($image->getHeight() > $settingHeight or $image->getWidth() > $settingWidth) {
                  $image->resize($settingWidth, $settingHeight)
                        ->write($file_tmp);
                  $image->destroy();
                  $photoFile->store($file_tmp);
              }
              $media_row->size = json_encode(array('width' => $image->getWidth(), 'height' => $image->getHeight()));
              $media_row->save();


              /***** 4 types of image sezes *********/
             
              /***** 4 types of image sezes *********/
          }
          else if ($photoFile->mime_major == 'pdf' and class_exists('Imagick', false)) {
              $file_tmp = $photoFile->temporary();
              $file_tmp_thumb = $file_tmp.'.jpg';
              $im = new imagick($file_tmp.'[0]');
              $im->setImageFormat( "jpg" );
              $im->writeImage($file_tmp_thumb);
              $im->destroy();
              $image = Engine_Image::factory(array('quality' => 100));
              $image->open($file_tmp_thumb)
                    ->resize($settings->getSetting('image_width', '720'), $settings->getSetting('image_height', '900'))
                    ->write($file_tmp_thumb)
                    ->destroy();
              $thumbFileRow = Engine_Api::_()->storage()->create($file_tmp_thumb, array('parent_id'  => $media_row->media_id,
                                                                                        'parent_type'=> 'whmedia_media',
                                                                                        'type' => 'thumb.etalon',
                                                                                        'parent_file_id' => $photoFile->file_id));
              file_exists($file_tmp_thumb) && unlink($file_tmp_thumb);
              file_exists($file_tmp) && unlink($file_tmp);
          }
          else if ($photoFile->mime_major == 'audio') {
              if ($settings->getSetting('both_video_format', 0)) {
                  $media_row->encode = 1;
              }
              $media_row->title = $file['name'];
              $media_row->save();
              // Add to jobs
              Engine_Api::_()->getDbtable('jobs', 'core')->addJob('whmedia_encode', array(
                                                                                            'media_id' => $media_row->media_id,
                                                                                          ));
          }
    } catch (Exception $e) {
        $media_row->delete();
        throw $e;
    }
    return $media_row->media_id;

  }

  public function getTypeFile($mime) {
      $array_mime = array('video/mpeg',
                          'video/mp2p',
                          'video/mp4',
                          'video/x-matroska',
                          'video/avi',
                          'video/vnd.rn-realvideo',
                          'video/x-vif',
                          'video/x-tango',
                          'video/x-sgi-movie',
                          'video/x-msvideo',
                          'video/x-bamba',
                          'video/vnd.vivo',
                          'video/quicktime');
      if (in_array($mime, $array_mime)) return 'video';

      $array_mime = array('audio/x-wav',
                          'audio/x-realaudio',
                          'audio/x-pn-realaudio-plugin',
                          'audio/x-pn-realaudio',
                          'audio/x-aiff',
                          'audio/mpg',
                          'audio/mpeg',
                          'audio/midi');
      if (in_array($mime, $array_mime)) return 'audio';

      $array_mime = array('image/x-ms-bmp',
                          'image/tiff',
                          'image/x-png',
                          'image/png',
                          'image/pjpeg',
                          'image/jpeg',
                          'image/gif',
                          'image/bmp');
      if (in_array($mime, $array_mime)) return 'image';
      $array_mime = array('application/pdf',
                          'application/x-pdf',
                          'application/acrobat',
                          'applications/vnd.pdf',
                          'text/pdf',
                          'text/x-pdf');
      if (in_array($mime, $array_mime)) return 'pdf';
      $array_mime = array('application/vnd.ms-powerpoint',
                          'application/vnd.ms-office',
                          'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                          'application/mspowerpoint',
                          'application/ms-powerpoint',
                          'application/mspowerpnt',
                          'application/vnd-mspowerpoint',
                          'application/powerpoint',
                          'application/x-powerpoint',
                          'application/x-m',
                          'application/zip');
      if (in_array($mime, $array_mime)) return 'ppt';
      return 'other';
  }

    public function getWhmediaPaginator($params = array())
    {
    $paginator = Zend_Paginator::factory($this->getWhmediaSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
    }

    public function getWhmediaSelect($params = array())
      {
        $table = Engine_Api::_()->getDbtable('projects', 'whmedia');
        $rName = $table->info('name');
        $select = $table->select()->from($rName, array('*', 'count_media' => 'COUNT(DISTINCT `engine4_whmedia_medias`.`media_id`)'))
                                  ->joinLeft('engine4_whmedia_medias', $rName.'.project_id = engine4_whmedia_medias.project_id and engine4_whmedia_medias.invisible = 0', array())
                                  ->group("$rName.project_id");
        if (!empty($params['orderby'])) {
            if ($params['orderby'] == 'title')
              $select->order( 'title ASC');
            elseif ($params['orderby'] == 'project_user') {
              $select->joinLeftUsing('engine4_users', 'user_id', array())
                     ->order( 'username ASC');
            }
            else 
               $select->order( $params['orderby'].' DESC');

            if ($params['orderby'] == 'count_likes') {
                $select->setIntegrityCheck(false)
                       ->joinLeft('engine4_core_likes', $rName.'.project_id = engine4_core_likes.resource_id and engine4_core_likes.resource_type = \'whmedia_project\' and engine4_core_likes.poster_type = \'user\'', array('count_likes' => 'COUNT(DISTINCT `engine4_core_likes`.`like_id`)'))
                       ->having('count_likes > 0');
            }
            if ($params['orderby'] == 'count_comments') {
                $select->setIntegrityCheck(false)
                       ->joinLeft('engine4_core_comments', $rName.'.project_id = engine4_core_comments.resource_id and engine4_core_comments.resource_type = \'whmedia_project\' and engine4_core_comments.poster_type = \'user\'', array('count_comments' => 'COUNT(DISTINCT `engine4_core_comments`.`comment_id`)'))
                       ->having('count_comments > 0');
            }
        }
        else
          $select->order( 'creation_date DESC' );

        if( !empty($params['user_id']) && is_numeric($params['user_id']) )
        {
          $select->where($rName.'.user_id = ?', $params['user_id']);
        }

        if( !empty($params['user']) && $params['user'] instanceof User_Model_User )
        {
          $select->where($rName.'.user_id = ?', $params['user']->getIdentity());
        }
        if (!empty($params['bytime'])) {
            $curr_time = time();
            switch ($params['bytime']) {
                case 'today':
                    $time = strtotime("today");
                    break;
                case 'week':
                    $time = strtotime("last Monday");
                    break;
                case 'month':
                    $time = strtotime("1 ".date("M", $curr_time)." ".date("Y", $curr_time));
                    break;
            }
            if (!empty($time)) {
                $params['start_date'] = date('Y-m-d H:i:s', $time);
            }
            if ($params['bytime'] == 'featured') {
                $select->joinLeft('engine4_whmedia_featured', $rName.'.project_id = engine4_whmedia_featured.featured_id', array())
                       ->where('engine4_whmedia_featured.featured_id is not null');
            }
        }
        if( !empty($params['start_date']) ) {
            if (strtotime($params['start_date'])) {
                $select->where($rName.'.creation_date >= ?', $params['start_date']);
            }
        }
        if( !empty($params['users']) )
        {
            if (is_array($params['users']) and count($params['users']) > 0) {
                $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
                $select->where($rName.'.user_id in (?)', new Zend_Db_Expr($str));
            }
            else {
                $select->where($rName.'.user_id in ', '()');
            }

        }

        if( !empty($params['category']) )
        {
          $category_id = (int)$params['category'];
          if (empty($category_id)) {
              $tableCategories = Engine_Api::_()->getDbtable('categories', 'whmedia');
              $CategoriesName = $tableCategories->info('name');
              $selectCategories = $tableCategories->select()->from($CategoriesName, array('category_id'))
                                                            ->where('url = ?', substr($params['category'], 0, 33))
                                                            ->limit(1);
              $select->where($rName.'.category_id = ?', $selectCategories);
          }
          else {
            $select->where($rName.'.category_id = ?', $category_id);
          }
        }
        if( !empty($params['project_id']) )
        {
          $select->where($rName.'.project_id = ?', $params['project_id']);
        }

        // Could we use the search indexer for this?
        if( !empty($params['search']) )
        {
          $select->where("LOWER(".$rName.".title) LIKE ? ", '%'.strtolower($params['search']).'%');
        }
        if( !empty($params['tags']) && is_string($params['tags']) ) {
            $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
            $tmName = $tmTable->info('name');
            $select_tags = $tmTable->select();
            $select_tags->setIntegrityCheck(false)
                        ->from($tmName, array('resource_id'))
                        ->joinLeftUsing('engine4_core_tags', 'tag_id', array())
                        ->where($tmName.'.resource_type = ?', 'whmedia_project');
            $tags_array = explode(',' , $params['tags']) ;
            $tag_str = '';
            $i = 0;
            foreach ($tags_array as $tag_str_tmp) {
                if (!trim($tag_str_tmp)) continue;
                $tag_str .= "'" . trim(trim($tag_str_tmp), ',') . "',";
                $i++;
            }
            $tag_str = rtrim($tag_str, ',');
            if (trim($tag_str)) {
                $select_tags->where('engine4_core_tags.text in (?)', new Zend_Db_Expr($tag_str))
                            ->group(new Zend_Db_Expr('`engine4_core_tagmaps`.`resource_id`'))
                            ->having(new Zend_Db_Expr("count(`engine4_core_tagmaps`.`resource_id`) = $i"));
                $select->setIntegrityCheck(false)
                            ->where($rName.'.project_id in ?', $select_tags);
            }
        }

        if( !empty($params['fuser'] ) ) {
            $tmTable = Engine_Api::_()->getDbtable('Likes', 'core');
            $tmName = $tmTable->info('name');
            $select_likes = $tmTable->select();
            $select_likes->setIntegrityCheck(false)
                         ->from($tmName, array('resource_id'))
                         ->where($tmName.'.resource_type = ?', 'whmedia_project')
                         ->where($tmName.'.poster_type = ?', 'user');
            if ($params['fuser'] instanceof User_Model_User)
                $select_likes->where($tmName.'.poster_id = ?', $params['fuser']->getIdentity());
            elseif ($params['fuser'] instanceof SeekableIterator) {
                $fu_where = '';
                foreach ($params['fuser'] as $fuser) {
                    if ($fuser instanceof User_Model_User )
                        $fu_where .= $fuser->getIdentity () . ',';
                    if (is_int($fuser))
                        $fu_where .= $fuser . ',';
                }
                $fu_where = rtrim($fu_where, ',');
                $select_likes->where($tmName.'.poster_id IN (?)', new Zend_Db_Expr($fu_where));
            }
            $select->setIntegrityCheck(false)
                   ->where($rName.'.project_id in ?', $select_likes);
        }
        
        if (isset($params['is_published'])) {
            $select->where($rName.'.is_published = ?', (int)(bool)$params['is_published'] );
        }
		
		// to make it sure it has cover file id if not that is a bad post
		$select->where( "engine4_whmedia_projects.cover_file_id != ?", 'NULL' );

		//echo $select->assemble();
		//die();
        // gocotano comment

        /*
        
        SELECT `engine4_whmedia_projects`.*, 
          COUNT(DISTINCT `engine4_whmedia_medias`.`media_id`) AS `count_media`, 
          COUNT(DISTINCT `engine4_core_likes`.`like_id`) AS `count_likes` 
        FROM `engine4_whmedia_projects`  
          LEFT JOIN `engine4_whmedia_medias` ON engine4_whmedia_projects.project_id = engine4_whmedia_medias.project_id and engine4_whmedia_medias.invisible = 0  
          LEFT JOIN `engine4_core_likes` ON engine4_whmedia_projects.project_id = engine4_core_likes.resource_id and engine4_core_likes.resource_type = 'whmedia_project' and engine4_core_likes.poster_type = 'user' 
        WHERE (engine4_whmedia_projects.creation_date >= '2014-02-24 03:23:46') AND (engine4_whmedia_projects.is_published = 1) 
        GROUP BY `engine4_whmedia_projects`.`project_id` 
        HAVING (count_likes > 0) ORDER BY `count_likes` DESC

        */
        return $select;
      }
    
      public function getTagId( $keyword ) {
        $tag_str = rtrim($keyword, ',');
        $tableTag = Engine_Api::_()->getDbtable( 'tags', 'core' );
        $dbTag = $tableTag->getAdapter();

        $select = $tableTag->select()
          ->where( 
            new Zend_Db_Expr( 
              $dbTag->quoteInto( 'MATCH(`text`) AGAINST(?)',  $tag_str ) 
              ) 
        );
        
        return $select->query()->fetch();

      }

      public function isFollowed( $viewer, $params = array() ) {
        $tableTag = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
        $dbTag = $tableTag->getAdapter();

        $id = (int)$viewer->user_id;
        $tagId = (int)$params[ 'tag_id' ];
        $select = $tableTag->select()
          ->where( 'hashtag_id='. $tagId .' AND follower_id='. $id .'');
        
        $rowSet = $select->query()->fetch();

        if( is_array( $rowSet ) ) {
          return true;
        }

        return false;

      }

    public function getVideoURL_info($url) {
        $out_data = array('error' => false);
        $setting_tmp = Engine_Api::_()->getApi('settings', 'core');
        if (is_string($url)) {
            $url_array = parse_url($url);
            if ($url_array['host'] == 'youtu.be') {
                $client = new Zend_Http_Client($url, array('maxredirects' => 0));
                $response = $client->request(Zend_Http_Client::GET);
                if ($response->getStatus() == 302) {
                    $url = $response->getHeader('Location');
                    $url_array = parse_url($url);
                }
                else
                    return array('error' => 'Video service is not supporting.');
            }
            if($url_array['host'] == 'www.youtube.com' || $url_array['host'] == 'youtube.com'){
                $out_data['type'] = 'youtube';
            }
            else if($url_array['host'] == 'www.vimeo.com' || $url_array['host'] == 'vimeo.com'){
                $out_data['type'] = 'vimeo';
            }
            else if ($setting_tmp->getSetting('embed_ly_key', '')) {
                $settingHeight = $setting_tmp->getSetting('image_height', '900');
                $settingWidth = $setting_tmp->getSetting('image_width', '720');

                $client = new Zend_Http_Client('http://api.embed.ly/1/oembed?key='.$setting_tmp->getSetting('embed_ly_key', '').'&url='. urlencode($url) . '&maxwidth=' . $settingWidth . '&maxheight=' . $settingHeight, array('maxredirects' => 0));
                try {
                    $response = $client->request(Zend_Http_Client::GET);
                }
                catch (Zend_Http_Client_Exception $e) {
                    return array('error' => 'Connection timeout. Requested server is not responding.');
                }
                catch (Exception $e) {
                    return array('error' => 'Video service is not supporting.');
                }
                if ($response->getStatus() == 200) {
                    $body = Zend_Json::decode($response->getBody());
                    if (in_array($body['type'], array('photo', 'video', 'rich')) and !empty($body['html'])) {
                        $out_data['type'] = 'embed_ly';
                        $out_data['code'] = $url;
                        $out_data['thumbnail'] = $body['thumbnail_url'];
                        $information = array();
                        $information['title'] =  $body['title'];
                        $information['description'] = $body['description'];                        
                        $out_data['information'] = $information;
                        $out_data['params'] = $body;
                        return $out_data;
                    }
                }
                return array('error' => 'Video service is not supporting.');
                
            }
            else {
                return array('error' => 'Video service is not supporting.');
            }
            switch ($out_data['type']) {
              //youtube
              case "youtube":
                // change new youtube URL to old one
                $new_code = @pathinfo($url);
                $url_y = preg_replace("/#!/", "?", $url);

                // get v variable from the url
                $arr = array();
                $arr = @parse_url($url_y);
                $code = "code";
                $parameters = $arr["query"];
                parse_str($parameters, $data);
                $code = $data['v'];
                if($code == "") {
                  $code = $new_code['basename'];
                }
                $out_data['code'] = $code;
                break;
              //vimeo
              case "vimeo":
              // get the first variable after slash
                $code = @pathinfo($url);
                $out_data['code'] = $code['basename'];
                break;
            }
        }
        else if (is_array($url) and isset ($url['type']) and isset ($url['code'])) {
            $out_data['code'] = $url['code'];
            $out_data['type'] = $url['type'];
        }
        else return array('error' => 'Incorrect inout type data..');
        
        // Check code
        // YouTube
        if ($out_data['type'] == 'youtube') {
            if (!$data = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/".$out_data['code'])) return array('error' => 'Incorrect video code.');
            if ($data == "Video not found") return array('error' => 'Incorrect video code.');
        }

        // Vimeo 
        if ($out_data['type'] == 'vimeo') {
            //http://www.vimeo.com/api/docs/simple-api
            //http://vimeo.com/api/v2/video
            $data = @simplexml_load_file("http://vimeo.com/api/v2/video/".$out_data['code'].".xml");
            $id = count($data->video->id);
            if ($id == 0) return array('error' => 'Incorrect video code.');
        }

        switch ($out_data['type']) {
          //youtube
          case "youtube":
            //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
            $yt = new Zend_Gdata_YouTube();
            $youtube_video = $yt->getVideoEntry($out_data['code']);
            $thumbs = $youtube_video->getVideoThumbnails();
            $out_data['thumbnail'] = $thumbs[0]['url'];
            break;
          //vimeo
          case "vimeo":
            //thumbnail_medium
            $data = simplexml_load_file("http://vimeo.com/api/v2/video/".$out_data['code'].".xml");
            $thumbnail = $data->video->thumbnail_large;
            $out_data['thumbnail'] = $thumbnail;
            break;
        }

        //Get video info

        switch ($out_data['type']) {
              //youtube
              case "youtube":            
                $information = array();
                $information['title'] = $youtube_video->getTitle();
                $information['description'] = $youtube_video->getVideoDescription();
                $information['duration'] = $youtube_video->getVideoDuration();
                //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
                $out_data['information'] = $information;
                break;
              //vimeo
              case "vimeo":
                //thumbnail_medium
                $thumbnail = $data->video->thumbnail_medium;
                $information = array();
                $information['title'] =  $data->video->title;
                $information['description'] = $data->video->description;
                $information['duration'] = $data->video->duration;
                //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
                $out_data['information'] = $information;
                break;
            }
        return $out_data;
    }

    public function getMediaPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this->getMediaSelect($params));
        if( !empty($params['page']) ) {
          $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
          $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    public function getMediaSelect($params = array()) {
        
        $table = Engine_Api::_()->getItemTable('whmedia_media');
        $rName = $table->info('name');
        $select = $table->select();
        if (!empty($params['orderby'])) {
            if ($params['orderby'] == 'random') {
                $select->order(new Zend_Db_Expr('RAND()'));
            }
            else
               $select->order( $params['orderby'].' DESC');

            if ($params['orderby'] == 'count_likes') {
                $select->from($rName, array('*', 'count_likes' => 'COUNT(DISTINCT `engine4_core_likes`.`like_id`)'))
                       ->joinLeft('engine4_core_likes', $rName.'.media_id = engine4_core_likes.resource_id and engine4_core_likes.resource_type = \'whmedia_media\' and engine4_core_likes.poster_type = \'user\'', array())
                       ->group("$rName.media_id")
                       ->having('count_likes > 0');
            }
        }
        else
          $select->order( 'creation_date DESC' );
        
        if( !empty($params['fuser'] ) ) {
            $tmTable = Engine_Api::_()->getDbtable('Likes', 'core');
            $tmName = $tmTable->info('name');
            $select_likes = $tmTable->select();
            $select_likes->setIntegrityCheck(false)
                         ->from($tmName, array('resource_id'))
                         ->where($tmName.'.resource_type = ?', 'whmedia_media')
                         ->where($tmName.'.poster_type = ?', 'user');
            if ($params['fuser'] instanceof User_Model_User)
                $select_likes->where($tmName.'.poster_id = ?', $params['fuser']->getIdentity());
            elseif ($params['fuser'] instanceof SeekableIterator) {
                $fu_where = '';
                foreach ($params['fuser'] as $fuser) {
                    if ($fuser instanceof User_Model_User )
                        $fu_where .= $fuser->getIdentity () . ',';
                    if (is_int($fuser))
                        $fu_where .= $fuser . ',';
                }
                $fu_where = rtrim($fu_where, ',');
                $select_likes->where($tmName.'.poster_id IN (?)', new Zend_Db_Expr($fu_where));
            }
            $select->setIntegrityCheck(false)
                   ->where($rName.'.media_id in ?', $select_likes);
        }
        if( !empty($params['start_date']) ) {
            if (strtotime($params['start_date'])) {
                $select->where($rName.'.creation_date >= ?', $params['start_date']);
            }
        }
        /*if parameter invisible empty return all media (invisible and not invisible)
         * if true - only invisible
         * else - only not invisible
         */

        if (isset($params['invisible'])) {
            if ($params['invisible'] === true) {
                $select->where('invisible = 1');
            }
            else {
                $select->where('invisible = 0');
            }
        }
        
        if (isset($params['is_text'])) {
            if ($params['is_text']) {
                $select->where('is_text = 1');
            }
            else {
                $select->where('is_text = 0');
            }
        }
        return $select;
      }

      public function getUserFlag($flag = 'superadmin') {
        $tableUsers = Engine_Api::_()->getItemTable('user');
        $tumName = $tableUsers->info('name');
        $select = $tableUsers->select()
                             ->from($tumName, array($tumName.'.*'))
                             ->joinLeftUsing('engine4_authorization_levels', 'level_id', array())
                             ->where('flag = ?', $flag);
        return $tableUsers->fetchAll($select);

      }

      public function getPopularTags($limit = 5) {
          $tagTable = Engine_Api::_()->getDbtable('tags', 'core');
          $tagName = $tagTable->info('name');
          $select_tags = $tagTable->select();
          $select_tags->from($tagName,array('*', 'count_tag' => "COUNT(`$tagName`.`tag_id`)"))
                      ->joinLeftUsing('engine4_core_tagmaps', 'tag_id', array())
                      ->where('engine4_core_tagmaps.resource_type = ?', 'whmedia_project')
                      ->having('count_tag > 0')
                      ->group("$tagName.tag_id")
                      ->order('count_tag DESC')
                      ->limit($limit);
          return $select_tags->query()->fetchAll();
      }

      public function isApple() {
          return (strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPod') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad'));
      }

      /**
       *
       * @return integer
       * 1 - use function mime_content_type
       * 2 - use finfo_file
       * 3 - use console file -ib
       */
      public function check_mime_info($update = false) {
        $setting_tmp = Engine_Api::_()->getApi('settings', 'core');
        if ($setting_tmp->getSetting('mime_info_method', NULL) === NULL or $update) {
            if (function_exists('mime_content_type'))
                $setting_tmp->setSetting('mime_info_method', 1);
            elseif (function_exists('finfo_file')) {
                $setting_tmp->setSetting('mime_info_method', 2);
            }
            elseif (function_exists('exec')) {
                $output = null;
                $return = null;
                exec('file -v', $output, $return);
                if( $return == 1 ) {
                    $setting_tmp->setSetting('mime_info_method', 3);
                }
                else
                    $setting_tmp->setSetting('mime_info_method', 0);
            }
            else {
                $setting_tmp->setSetting('mime_info_method', 0);
            }
        }
        return $setting_tmp->getSetting('mime_info_method', 0);
      }

      public function get_content_mime_type($file) {
        $setting_tmp = Engine_Api::_()->getApi('settings', 'core')->getSetting('mime_info_method', 0);

        switch ($setting_tmp) {
            case 1:
                $out = mime_content_type($file);
                break;
            case 2:
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $result = finfo_file($finfo, $file);
                finfo_close($finfo);
                $out = $result;
                break;
            case 3:
                exec("file -i -b {$file}", $file_types);
                $out = strtok(current($file_types), ';');
                break;
            default:
                throw new Engine_Exception('Media type has not detected. Please contact admin.');
        }
        if ((strstr($out, 'text') !== false and $setting_tmp != 3) or empty($out)) {
            if (function_exists('exec')) {
                $output = null;
                $return = null;
                exec('file -v', $output, $return);
                if( $return == 1 ) {
                    exec("file -i -b {$file}", $file_types);
                    $out_test = strtok(current($file_types), ';');
                    if (trim($out_test) and $out != $out_test) {
                        Engine_Api::_()->getApi('settings', 'core')->setSetting('mime_info_method', 3);
                        $out = $out_test;
                    }
                }
            }
        }
        if ($out != 'application/octet-stream')
            return $out;
        
        $fileext = substr(strrchr($file, '.'), 1);
        if (empty($fileext)) return $out;
        $regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i";
        $lines = file(APPLICATION_PATH . "/application/modules/Whmedia/Library/mime.types");
        foreach($lines as $line) {
           if (substr($line, 0, 1) == '#') continue; // skip comments
           $line = rtrim($line) . " ";
           if (!preg_match($regex, $line, $matches)) continue; // no match to the extension
           return ($matches[1]);
        }
        return $out;
      }

      public function getVideoDuration(Whmedia_Model_Media $file) {
          $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->whvideo_ffmpeg_path;
          if( !$ffmpeg_path ) {
            throw new Engine_Exception('Ffmpeg not configured');
          }
          if ($file->getMediaType() != 'video'  ) {
              return false;
          }
          if (!empty($file->duration )) {
              return $file->duration;
          }
          if ($file->duration === 0) {
              return false;
          }
          $videoFile = $file->getFile();
          if (!($videoFile->getStorageService() instanceof  Storage_Service_Local)) {
              return false;
          }
          $file_path = $videoFile->storage_path;
          $shellCommand = $ffmpeg_path . ' '
              . '-i ' . escapeshellarg(APPLICATION_PATH . DIRECTORY_SEPARATOR . $file_path) . ' '
              . '2>&1'
              ;

              // Process thumbnail
          $videoOutput = shell_exec($shellCommand);
          if( preg_match('/Duration:\s+(.*?)[.]/i', $videoOutput, $matches) ) {
            list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
            $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
          }
          else {
            $duration = 0; // Hmm
          }
          $file->duration = $duration;
          $file->save();
          return (empty ($duration)) ? false : $duration;
      }

      public function getMediaSize(Whmedia_Model_Media $file) {
          $size = $file->size;
          if (!empty ($size)) {
              $size = Zend_Json::decode($size);
              if (is_array($size) && key_exists('width', $size) && key_exists('height', $size))
                return $size;
          }
          $mediaFile = $file->getFile();
          if (empty ($mediaFile)) {
              return false;
          }
          if (!($mediaFile->getStorageService() instanceof  Storage_Service_Local)) {
              return false;
          }
          switch ($file->getMediaType()) {
              case 'video':
                  try {
                    $size = $this->getVideoDimension($mediaFile->storage_path);
                    if (!(is_array($size) && key_exists('width', $size) && key_exists('height', $size)))
                        return false;
                  }
                  catch (Exception $e) {
                    return false;
                  }
                  break;
              case 'image':
                  $image = Engine_Image::factory(array('quality' => 100));
                  $image->open($mediaFile->storage_path);
                  $size = array('width' => $image->getWidth(), 'height' => $image->getHeight());
                  $image->destroy();
                  break;
              default :
                  return false;
          }
          $file->size = json_encode($size);
          $file->save();
          return $size;
      }

      public function getFrame(Whmedia_Model_Media $video, $time) {
          if ($video->getMediaType() != 'video') {
              throw new Engine_Exception('Media must be video type.');
          }
          $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->whvideo_ffmpeg_path;
          if( !$ffmpeg_path ) {
            throw new Engine_Exception('Ffmpeg not configured');
          }
          if ($time > $this->getVideoDuration($video)) {
              throw new Engine_Exception('Time more then video duration');
          }
          $videoFile = $video->getFile();
          $temp_name = $video->getIdentity() . '_' . $time . '_cover.jpg';
          $tmp_file_row = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'temporary',
                                                                                        'parent_id = ?' => $video->getIdentity(),
                                                                                        'parent_file_id = ?' => $videoFile->getIdentity(),
                                                                                        'name = ?' => $temp_name));
          if ($tmp_file_row !== null) {
              return $tmp_file_row;
          }
          if ($videoFile->getStorageService() instanceof  Storage_Service_Local) {
              $thumbPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'whmedia' . DIRECTORY_SEPARATOR . $temp_name;
              // Thumbnail proccess command
              $thumbCommand = $ffmpeg_path . ' '
              . '-i ' . escapeshellarg(APPLICATION_PATH . DIRECTORY_SEPARATOR . $videoFile->storage_path) . ' '
              . '-an -r 1' . ' '
              . '-ss '. $time . ' '
              . '-t 00:00:01 -v 2' . ' '
              . '-y ' . escapeshellarg($thumbPath) . ' '
              . '2>&1'
              ;

              // Process thumbnail
              $thumbOutput = shell_exec($thumbCommand);
              if( preg_match('/video:0kB/i', $thumbOutput) ) {
                return false;
              }
              if (!file_exists($thumbPath)) {
                return false;
              }

              $FileRow = Engine_Api::_()->getDbtable('files', 'storage')->createRow( array('parent_id' => $video->getIdentity(),
                                                                                           'parent_type' => 'temporary',
                                                                                           'user_id' => null,
                                                                                           'parent_file_id' => $videoFile->getIdentity()
                                                                                          ));
              $FileRow->store($thumbPath);
              $FileRow->save();
              return $FileRow;
          }
      }

      public function getThumbTypeSize($type = null) {
          switch ($type) {
              case 'thumb.icon':
                  $width = 45;
                  $height = 45;
                  break;
        case 'thumb.large':
        $width = 135;
        $height = 135;
        break;
        case 'thumb.extralarge':
        $width = 180;
        $height = 180;
        break;
              case 'thumb.normal':
              default :
                  $width = 90;
                  $height = 90;
          }
          return array('width' => $width,
                       'height' => $height);
      }

      public function getErrorMessage($code) {
          switch ($code) {
              case 3:
                  return "Audio format is not supported.";
              case 4:
                  return "Video format is not supported.";
              case 5:
                  return "Incorrect video format.";
              case 7:
                  return "Conversion failed. You may be over the site upload limit.";
              case 8:
                  return "There was server error during the video processing. Contact administrator for details.";
              case 9:
                  return 'Video storage file was missing';
              case 10:
                  return 'Could not pull to temporary file';
              default:
                  return "Unknown encoding error.";

          }
      }

      public function getVideoDimension ($src_filepath) {
         $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->whvideo_ffmpeg_path;
         if( !$ffmpeg_path ) {
            throw new Engine_Exception('Ffmpeg not configured');
         }
   $commandline = $ffmpeg_path." -i ".$src_filepath . ' 2>&1';
         // Execute video encode command
         $exec_return = shell_exec($commandline);
         if (!$exec_return)
             throw new Engine_Exception("Cann't determine video dimension.");
         $exec_return_content  = explode ("\n" , $exec_return);

         //Traitement du retour
         if( $error_line_id = $this->_array_search('error', $exec_return_content) )
         {
             //Erreur, retourne status = -1 et error_msg = message d'erreur
             $error_line = trim($exec_return_content[$error_line_id]);
             throw new Engine_Exception($error_line);
         }
         else
         {

             //Decodage des infos codec video
             if($infos_line_id = $this->_array_search('Video:', $exec_return_content))
             {
                 $infos_line     = trim($exec_return_content[$infos_line_id]);
                 $infos_cleaning = explode (': ', $infos_line);
                 $infos_datas    = explode (',', $infos_cleaning[2]);
                 $vdo_res    = trim($infos_datas[2]);
                 $res = explode("x", $vdo_res);
                 $res2 = explode(" ", $res[1]);
                 $res[1] = $res2[0];
                 return array('width' => $res[0],
                              'height' => $res[1]);
             }
             else throw new Engine_Exception("Cann't determine video dimension.");

         }

         return $return_array;

      }
    
    public function getVideoOrientation( $src_filepath ) {
      $cmd_getOrientation =  'mediainfo '.$src_filepath.' | grep Rotation';
      $currentOrientation = exec($cmd_getOrientation);
      return $currentOrientation = filter_var($currentOrientation, FILTER_SANITIZE_NUMBER_INT);
    }
    
      public function getAudioInfo ($src_filepath) {
         $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->whvideo_ffmpeg_path;
         if( !$ffmpeg_path ) {
            throw new Engine_Exception('Ffmpeg not configured');
         }
   $commandline = $ffmpeg_path." -i ".$src_filepath . ' 2>&1';
         // Execute video encode command
         $exec_return = shell_exec($commandline);
         if (!$exec_return)
             throw new Engine_Exception("Cann't determine audio info.");
         $exec_return_content  = explode ("\n" , $exec_return);

         //Traitement du retour
         if( $error_line_id = $this->_array_search('error', $exec_return_content) )
         {
             $error_line = trim($exec_return_content[$error_line_id]);
             throw new Engine_Exception($error_line);
         }
         else
         {

             //Decodage des infos codec video
             if($infos_line_id = $this->_array_search('Audio:', $exec_return_content))
             {
                 $infos_line     = trim($exec_return_content[$infos_line_id]);
                 $infos_cleaning = explode (': ', $infos_line);
                 $infos_datas    = explode (',', $infos_cleaning[2]);
                 return array('codec' => $infos_datas[0],
                              'rate' => $infos_datas[1],
                              'channels' => $infos_datas[2],
                              'format' => $infos_datas[3],
                              'bitrate' => $infos_datas[4]);
             }
             else throw new Engine_Exception("Cann't determine audio info.");

         }

      }
      
      public function getManageNavigation(Whmedia_Model_Project $project) {
          if( is_null($this->_manageNavigation) ) {
                $navigation = $this->_manageNavigation = new Zend_Navigation();
                $project_id = $project->project_id;
                
                $navigation->addPage(array(
                    'label' =>  Zend_Registry::get('Zend_Translate')->_('Edit Project'),
                    'route' => 'whmedia_project',
                    'action' => 'index',
                    'params' => array('project_id' => $project_id)
                    )); 
                $navigation->addPage(array(
                    'label' =>  Zend_Registry::get('Zend_Translate')->_('Details'),
                    'route' => 'whmedia_project',
                    'action' => 'edit',
                    'params' => array('project_id' => $project_id)
                    ));
                $navigation->addPage(array(
                    'label' =>  Zend_Registry::get('Zend_Translate')->_('View Project'),
                    'uri' => Engine_Api::_()->core()->getSubject()->getHref()
                    ));
                $navigation->addPage(array(
                    'label' =>  Zend_Registry::get('Zend_Translate')->_('Delete Project'),
                    'route' => 'whmedia_project',
                    'action' => 'delproject',
                    'params' => array('project_id' => $project_id)
                    )); 
                
          }
          return $this->_manageNavigation;
      }
      
      public function isMobile() {
          $session = new Zend_Session_Namespace('mobile');
          $mobile = $session->mobile;
          return (bool) $mobile ;
      }

      public function getFollowSuggestionSelect (User_Model_User $user, $limit = null) {
          $user_id = $user->getIdentity();
          $tableUsers = Engine_Api::_()->getItemTable('user');
          $tumName = $tableUsers->info('name');
          $select = $tableUsers->select()
                               ->from($tumName, array($tumName.'.*'))
                               ->joinLeft(array('mutual' => new Zend_Db_Expr(" (SELECT `engine4_whmedia_follow`.`user_id`, SUM(`users_main`.`count_mutual`) AS `sum_count_mutual`, COUNT(*) AS `count_coincidence` FROM `engine4_whmedia_follow`
                                                                                LEFT JOIN (SELECT `engine4_whmedia_follow`.`user_id`, `engine4_whmedia_follow`.`follower_id`, `engine4_whmedia_follow`.`creation_date`, COUNT(*) AS `count_mutual` FROM `engine4_whmedia_follow`
                                                                                               LEFT JOIN (
                                                                                               SELECT `user_id` FROM `engine4_whmedia_follow`
                                                                                               WHERE follower_id = {$user_id} 
                                                                                               ORDER BY `engine4_whmedia_follow`.`creation_date` DESC
                                                                                               LIMIT 20
                                                                                                       ) AS `users_main` ON `engine4_whmedia_follow`.`user_id` = users_main.user_id
                                                                                               WHERE follower_id != {$user_id} AND users_main.user_id IS NOT NULL
                                                                                               GROUP BY follower_id 
                                                                                               ORDER BY `count_mutual` DESC
                                                                                       ) AS `users_main` 
                                                                                       ON `engine4_whmedia_follow`.`follower_id` = users_main.follower_id

                                                                                WHERE `engine4_whmedia_follow`.`user_id` NOT IN (SELECT `user_id` FROM `engine4_whmedia_follow` WHERE `engine4_whmedia_follow`.`follower_id` = {$user_id} ) AND `users_main`.`count_mutual` IS NOT NULL
                                                                                GROUP BY `engine4_whmedia_follow`.`user_id`)")), new Zend_Db_Expr('`mutual`.`user_id` = `engine4_users`.`user_id`'), array())
                               ->where(new Zend_Db_Expr("(`mutual`.`sum_count_mutual` IS NOT NULL OR `mutual`.`count_coincidence` IS NOT NULL) AND `engine4_users`.`user_id` != {$user_id} "))
                               ->order(new Zend_Db_Expr('`sum_count_mutual` DESC, `count_coincidence` DESC'));
          if (!empty($limit)) {
              $select->limit($limit);
          }         

          return $select;
      }

      protected function _array_search($needle, $array_lines) {
         $return_val = false;
         reset ($array_lines);
         foreach( $array_lines as $num_line => $line_content ) {
            if( strpos($line_content, $needle) !== false )
                        {
                            return($num_line);
                        }

         }
         return($return_val);
     }
}
