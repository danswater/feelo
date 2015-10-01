<?php     

class Whmedia_Model_Media extends Core_Model_Item_Abstract
{
   protected $_parent_type = 'whmedia_project';
   protected $_parent_is_owner = false;
   protected $_searchTriggers = array();
   protected $_file;
   protected $_project;
   protected $_originalFile;

   static private $_thumb_settings;

   protected function _delete() {
      $files = Engine_Api::_()->getItemTable('storage_file')->fetchAll(array('parent_type = ?' => 'whmedia_media',
                                                                             'parent_id = ?' => $this->media_id));
      if (!count($files)) return;

      foreach ($files as $file) {
          $file->remove();
      }
        
    }

   public function getThumb($thumbWidth = null, $thumbHeight = null, $crop = true) {
      $baseURL = Zend_Registry::get('StaticBaseUrl');
      $get_params = '&cz=' . (int)$crop;
      if (empty($thumbWidth) and $thumbWidth !== false) {
          $get_params .= '&w=' . Whmedia_Model_Media::getThumbDimension('width');
      }
      else {
          $get_params .= '&w=' . $thumbWidth;
      }
      if (empty($thumbHeight) and $thumbHeight !== false) {
          $get_params .= '&h=' . Whmedia_Model_Media::getThumbDimension('height');
      }
      else if ($thumbHeight === false) {
          $get_params .= '';
      }
      else {
          $get_params .= '&h=' . $thumbHeight;
      }
      if ($this->encode) {
        $file = $this->getFile();
        if ($file->mime_major == 'video')
                $html_out = $baseURL . "whshow_thumb.php?src=./application/modules/Whmedia/externals/images/converting_video.png{$get_params}";
        if ($file->mime_major == 'audio')
                $html_out = $baseURL . "whshow_thumb.php?src=./application/modules/Whmedia/externals/images/converting_audio.png{$get_params}";
        return $html_out;
      }
      if (!in_array($this->getMediaType(), array('text', 'url')) and $this->code === null) {
          $file = $this->getFile();
          if ($file->mime_major == 'image')
            $file_out = $file;
          if ($file->mime_major == 'audio')
            $file_path = 'application/modules/Whmedia/externals/images/thumb_audio.png';
          if ($file->mime_major == 'ppt')
            $file_path = 'application/modules/Whmedia/externals/images/thumb_ppt.png';
          if ($file->mime_major == 'video')
            $file_out = $file->getChildren()->getRowMatching('type', 'thumb.etalon');
          if ($file->mime_major == 'pdf') {
            $file_out = $file->getChildren()->getRowMatching('type', 'thumb.etalon');  
            if ($file_out == null)
                $file_path = 'application/modules/Whmedia/externals/images/thumb_pdf.png';
          }
      }
      else {
          $file_out = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'whmedia_media',
                                                                                    'parent_id = ?' => $this->media_id,
                                                                                    'type = ?' => 'thumb.etalon'));
      }
      if (empty ($file_path)) {
          if ($file_out !== null) {
              if ($file_out->getStorageService() instanceof  Storage_Service_Local) {
                  $file_path = $file_out->storage_path;
              }
              else {
                  $file_path = $file_out->map();
              }
          }
          else $file_path = 'application/modules/Whmedia/externals/images/no_photo_project.png';
      }

      return $baseURL . "whshow_thumb.php?src=$file_path{$get_params}";
   }

   static public function getThumbDimension($dimension) {
       if ($dimension != 'width' and $dimension != 'height')
           throw new Core_Model_Exception(sprintf('Unknown dimension %1$s.', $dimension));
       if (Whmedia_Model_Media::$_thumb_settings === null) {
           $settings = Engine_Api::_()->getApi('settings', 'core');
           Whmedia_Model_Media::$_thumb_settings['width'] = $settings->getSetting('thumb_width', '100');
           Whmedia_Model_Media::$_thumb_settings['height'] = $settings->getSetting('thumb_height', '100');
       }
       return Whmedia_Model_Media::$_thumb_settings[$dimension];
   }

   public function Embedded() {
       if ($this->is_text)
           return $this->getDescription ();
       if ($this->getMediaType() == 'url') {
           return $this->_urlEmbedded();
       }
       $settings = Engine_Api::_()->getApi('settings', 'core');
       $baseURL = Zend_Controller_Front::getInstance()->getBaseUrl();
       if ($this->encode) {
           $translate = Zend_Registry::get('Zend_Translate');
           if ($this->encode <= 2)
            $encode_msg = $translate->_("Processing (may take few minutes).");
           else
            $encode_msg = $translate->_("Processing error.");
           $error_msg = '';
           if ($this->encode > 2) {
               $tmp_msg = $translate->_(Engine_Api::_()->whmedia()->getErrorMessage($this->encode));
               $error_msg = <<<EOF
		                <span class='failedmedia'>
                                    {$tmp_msg}
		                </span>
EOF;
           }
           return  '<div class="mediaprocessbl"> 
                        <img src="' . $this->getThumb(320, 240) .  'alt="' . $translate->_("Video Encode") . '" />' .
                        '<div class="media_processing">' .
                            $encode_msg . $error_msg .
                        '</div>
                    </div>';
       }
       if ($this->code !== null) {
           return $this->videoServices_Embedded();
       }
       $file = $this->getFile();
       if ($file->mime_major == 'image') {
           $img_width = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('image_width', '600');
           return '<img alt="Media" src="'.$file->map() .'" style="max-width: ' . $img_width . 'px;" />';
       }
       if ($file->mime_major == 'video') {
           return $this->_getFlowplayerVideoEmbedded($file);
       }
       if ($file->mime_major == 'audio') {
           return $this->_getFlowplayerAudioEmbedded($file);
       }
       if ($file->mime_major == 'pdf' or $file->mime_major == 'ppt') {
           return $this->_PDF_Embedded($file);
       }
       return '';
   }
   public function getFile() {
       if ($this->_file === null) {
           $this->_file = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'whmedia_media',
                                                                                        'parent_id = ?' => $this->media_id,
                                                                                        'type is null'));
       }
       return $this->_file;
   }

   protected function videoServices_Embedded() {
       $data = unserialize($this->code);
       $emb = '';
       try {
           if ($data['type'] == 'youtube')
               $emb = $this->compileYouTube($data['code']);
           if ($data['type'] == 'vimeo')
               $emb = $this->compileVimeo($data['code']);

       }
       catch (Exception $e) {
           $translate = Zend_Registry::get('Zend_Translate');
           $emb = <<<EOF
		   	<div class="mediaprocessbl">
                            <img src="{$this->getThumb(320, 240)}" alt="{$translate->_("This video is no longer available on YouTube")}" />
                            <div class="media_processing">{$translate->_("Video was delete from service.")}</div>
			</div>		
EOF;
       }
       if ($data['type'] == 'embed_ly')
               $emb = $data['params']['html'];
       return $emb;
   }

   public function compileYouTube($code) {

      $yt = new Zend_Gdata_YouTube();
      $videoFeed = $yt->getVideoEntry($code);
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $emb = <<<EOF
                    <object width="{$settings->getSetting('video_width', '320')}" height="{$settings->getSetting('video_height', '240')}">
                      <param name="movie" value="{$videoFeed->getFlashPlayerUrl()}"></param>
                      <param name="allowFullScreen" value="true"></param>
                      <embed src="{$videoFeed->getFlashPlayerUrl()}" width="{$settings->getSetting('video_width', '320')}" height="{$settings->getSetting('video_height', '240')}" type="application/x-shockwave-flash" wmode="transparent" allowfullscreen="true">
                      </embed>
                    </object>
EOF;
      return $emb;
  }

  public function compileVimeo($code)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $embedded = <<<EOF
                <iframe src="http://player.vimeo.com/video/{$code}" width="{$settings->getSetting('video_width', '320')}" height="{$settings->getSetting('video_height', '240')}" frameborder="0"></iframe>
EOF;

    return $embedded;
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function getProject() {
      if ($this->_project === null) {
          $this->_project = $this->getParent();
      }
      return $this->_project;
  }
  
  public function  getOwner() {
        return $this->getProject()->getOwner();
  }

  public function  getHref() {
      return $this->getProject()->getHref() . '#whmedia_' . $this->getIdentity();
  }

  public function getFullHref() {
      return $this->getFullSiteURL() . $this->getHref();
  }

  private function _getFlowplayerVideoEmbedded($file) {
    return Whmedia_Library_Player::getInstance()->getVideoEmbeded($this);
  }

  private function _getFlowplayerAudioEmbedded($file) {
    return Whmedia_Library_Player::getInstance()->getAudioEmbeded($this);
  }

  public function getPhotoUrl($type_thumbWidth = null, $thumbHeight = null, $crop = true) {
      if ((is_int($type_thumbWidth) or $type_thumbWidth === false) and (is_int($thumbHeight) or $thumbHeight === false)) {
          return $this->getThumb($type_thumbWidth, $thumbHeight, $crop);
      }
      $size = Engine_Api::_()->whmedia()->getThumbTypeSize($type_thumbWidth);
      return $this->getThumb($size['width'], $size['height'], $crop);
  }

  /**
   * Get a generic media type. Values:
   * audio, image, video, pdf, youtube, vimeo, text, url, embed_ly
   *
   * @return string
   */
  public function  getMediaType() {
    if ($this->code !== null) {
        $data = unserialize($this->code);
        return $data['type'];
    }
    else if ($this->is_text) {
        return 'text';
    }
    else if (!empty($this->is_url)) {
        return 'url';
    }
    return $this->getFile()->mime_major;
  }

  public function getEmbeddedCode() {
      $type = $this->getMediaType();
      $baseURL = $this->getFullSiteURL();
      $file = $this->getFile();
      $settings = Engine_Api::_()->getApi('settings', 'core');
      if ($type == 'youtube' or $type == 'vimeo')
          return '';
      if ($type == 'embed_ly') {
          $params = unserialize($this->code);
          return $params['params']['html'];;
      }
      if ($type == 'video') {
        $height = ((int)$settings->getSetting('video_height', '240')) + 20;
        $width = $settings->getSetting('video_width', '320') + 30;
      }
      elseif ($type == 'image') {
        return '<frame frameborder="0"><img src="'.$baseURL.$file->map() .'" /></frame>';
      }
      elseif ($type == 'audio') {
        $width = 520;
        $height = 40;
      }
      elseif ($type == 'pdf' or $type == 'ppt') {
        return $this->_PDF_Embedded($file);
      }
      else
          return '';
      $url_media = $baseURL . Zend_Registry::get('StaticBaseUrl') . WHMEDIA_URL_WORLD . '/share/embedded/media_id/' . $this->getIdentity();
      return "<iframe width='{$width}px' height='{$height}px' src='{$url_media}' frameborder='0'></iframe>";
  }

  public function issetOriginal() {
      return (bool) $this->getOriginal();
  }

  public function getOriginal() {
    if (in_array($this->getMediaType(), array('text', 'url'))) return null;
    if ($this->_originalFile === null) {
        if ($this->code !== null) {
           return $this->_originalFile = false;
        }
        $original = $this->getFile()->getChildren()->getRowMatching('type', 'original');
        if (empty ($original))
            $this->_originalFile = false;
        else
            $this->_originalFile = $original;
    }
    return $this->_originalFile;
  }

  protected function _PDF_Embedded($file) {
      $img_width = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('pdf_width', '650');
      $img_height = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('pdf_height', '794');
      if ($file->getStorageService() instanceof  Storage_Service_Local) {
          $baseURL = $this->getFullSiteURL();
          return "<iframe src='http://docs.google.com/gview?url={$baseURL}{$file->map()}&embedded=true' style='width:{$img_width}px; height:{$img_height}px; position:inherit!important; top:inherit; left:inherit' frameborder='0'></iframe>";
      }
      else {
          return "<iframe src='http://docs.google.com/gview?url={$file->map()}&embedded=true' style='width:{$img_width}px; height:{$img_height}px; position:inherit!important; top:inherit; left:inherit' frameborder='0'></iframe>";
      }
  }

  protected function getFullSiteURL() {
      $request = Zend_Controller_Front::getInstance()->getRequest();
      return $request->getScheme() . '://' . $request->getHttpHost();
  }
  
  protected function _urlEmbedded() {
      $file = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'whmedia_media',
                                                                            'parent_id = ?' => $this->media_id,
                                                                            'type = ?' => 'thumb.etalon'));
      $out = '';
      if (!empty($file)) {        
        $img_width = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('image_width', '600');
        $out .= '<img alt="Media" src="'. $file->map() .'" style="max-width: ' . $img_width . 'px;" /><br/>';
      }
      if (!empty($this->title)) {
          $out .= '<div class="media_url_title">' . $this->getTitle() . '</div>';
      }
      if (!empty($this->description)) {
          $out .= '<div class="media_url_description">' . $this->getDescription() . '</div>';
      }

      $out .= '<div class="media_url_url">' . Zend_Registry::get('Zend_Translate')->_("URL:") . ' <a target="_blank" href="' . $this->is_url . '">' . $this->is_url . '</a></div>';
      return $out;

  }
}
