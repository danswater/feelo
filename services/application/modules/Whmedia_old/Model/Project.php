<?php     

class Whmedia_Model_Project extends Core_Model_Item_Abstract
{
   protected $_parent_type = 'user';
   protected $_parent_is_owner = true;
   protected $_searchTriggers = array('title', 'description', 'search');
   protected $_medias;
   protected $_cover;

   public function getHref() {
    $params = array('project_id' => $this->project_id,
                    'slug' => $this->getSlug());
    return Zend_Controller_Front::getInstance()->getRouter()
                                               ->assemble($params, 'whmedia_project_view', true);
  }

  public function tags() {
    if ($this->user_id)
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
    else throw new Core_Model_Item_Exception('Not possible to get tags.');
  }
  public function gettags() {
    return $this->tags()->getTagMaps();
  }
  public function getMedias(array $addition_filtr = null) {
    $filtr = array('project_id = ?' => $this->project_id, 'invisible = 0');
    if (!empty ($addition_filtr)) {
        $filtr = array_merge($addition_filtr, $filtr);
        return Engine_Api::_()->getDbtable('medias', 'whmedia')->fetchAll($filtr, 'order ASC');
    }
    if ($this->_medias === null) {
        $this->_medias = Engine_Api::_()->getDbtable('medias', 'whmedia')->fetchAll($filtr, 'order ASC');
    }
        return $this->_medias;
  }

  public function getMediasCount($filtr = array()) {
    return $this->getMedias($filtr)->count();
  }

  public function setCover(Whmedia_Model_Media $media) {
      if (is_int($this->cover_file_id)) {
          $cover_media = $this->getMedias()->getRowMatching('media_id', $this->cover_file_id);
          if ( $cover_media === null or !is_object($cover_media))
              Engine_Api::_()->getDbtable('medias', 'whmedia')->fetchRow(array('project_id = ?' => $this->project_id,
                                                                               'media_id = ?' => $this->cover_file_id))->delete();
      }
      $this->cover_file_id = $media->media_id;
      if ($this->isReadOnly()) {
          $project = Engine_Api::_()->getItem('whmedia_project', $this->getIdentity());
          $project->cover_file_id = $this->cover_file_id;
          $project->save();
      }
      else
        $this->save();
      return $this;
  }

  public function getPhotoUrl($thumbWidth = null, $thumbHeight = null, $crop = true) {
      $crop = (int)$crop;
      if (is_string($thumbWidth) and $thumbHeight === null) {
          $size = Engine_Api::_()->whmedia()->getThumbTypeSize($thumbWidth);
          $thumbWidth = $size['width'];
          $thumbHeight = $size['height'];
      }

      $get_params = '&cz=' . $crop;
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
      

      $cover_media = $this->getCoverMedia();
      if (empty($cover_media)) {
          return Zend_Registry::get('StaticBaseUrl') . "whshow_thumb.php?src=./application/modules/Whmedia/externals/images/no_photo_project.png{$get_params}";
      }
      
      if ( $cover_media !== null and is_object($cover_media))
          return $cover_media->getThumb($thumbWidth, $thumbHeight, $crop);
      else
          return Engine_Api::_()->getDbtable('medias', 'whmedia')->fetchRow(array('project_id = ?' => $this->project_id,
                                                                                  'media_id = ?' => $this->cover_file_id))->getThumb($thumbWidth, $thumbHeight, $crop);
  }

  public function getCoverMedia () {
      if (!empty ($this->_cover)) {
          return $this->_cover;
      }
      $cover_file_id = (int)$this->cover_file_id;
      if (empty($cover_file_id) and $this->getMediasCount(array('is_text = 0')) > 0) {
          $tmp_media = $this->getMedias(array('is_text = 0'));
          foreach ($tmp_media as $tmp_m)  {
              switch ($tmp_m->getMediaType()) {
                  case 'audio':
                      break;
                  case 'video':
                      if ($tmp_m->encode) {
                          if ($tmp_m->encode <= 2) {
                              break 2;
                          }
                          else {
                              break;
                          }
                      }
                  default:
                      $this->setCover($tmp_m);
                      $cover_file_id = (int)$this->cover_file_id;
                      break 2;
              }
          }
      }
      if (!empty($cover_file_id)) {
          $cover_media = Engine_Api::_()->getDbtable('medias', 'whmedia')->findRow($cover_file_id);
          if ($cover_media === null) {
              return null;
          }
          else {
              return $this->_cover = $cover_media;
          }
      }
      else
          return null;
  }

  public function getSlug() {
    $str = $this->getTitle();
    $str = rtrim($str, '.');
    $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    $str = trim($str, '-');
    if( !$str ) {
      $str = '-';
    }
    return $str;
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


  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  protected function _delete() {
      parent::_delete();
      $medias = Engine_Api::_()->getDbtable('medias', 'whmedia')->fetchAll(array('project_id = ?' => $this->project_id));
      foreach ($medias as $media) {
          $media->delete();
      }
      unset ($medias);
      
      $comments =  $this->comments()->getAllComments();
      foreach ($comments as $comment) {
          $comment->delete();
      }
      unset ($comments);
      $likes =  $this->likes()->getAllLikes();
      foreach ($likes as $like) {
          $like->delete();
      }
      unset ($likes);

      $tags =  $this->gettags();
      foreach ($tags as $tag) {
          $tag->delete();
      }
      unset ($tags);
    }
}