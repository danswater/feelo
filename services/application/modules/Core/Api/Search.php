<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Search.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_Api_Search extends Core_Api_Abstract
{
  protected $_types;
  
  public function index(Core_Model_Item_Abstract $item)
  {
    // Check if not search allowed
    if( isset($item->search) && !$item->search )
    {
      return false;
    }

    // Get info
    $type = $item->getType();
    $id = $item->getIdentity();
    $title = substr(trim($item->getTitle()), 0, 255);
    $description = substr(trim($item->getDescription()), 0, 255);
    $keywords = substr(trim($item->getKeywords()), 0, 255);
    $hiddenText = substr(trim($item->getHiddenSearchData()), 0, 255);
    
    // Ignore if no title and no description
    if( !$title && !$description )
    {
      return false;
    }

    // Check if already indexed
    $table = Engine_Api::_()->getDbtable('search', 'core');
    $select = $table->select()
      ->where('type = ?', $type)
      ->where('id = ?', $id)
      ->limit(1);

    $row = $table->fetchRow($select);

    if( null === $row )
    {
      $row = $table->createRow();
      $row->type = $type;
      $row->id = $id;
    }

    $row->title = $title;
    $row->description = $description;
    $row->keywords = $keywords;
    $row->hidden = $hiddenText;
    $row->save();
  }

  public function unindex(Core_Model_Item_Abstract $item)
  {
    $table = Engine_Api::_()->getDbtable('search', 'core');

    $table->delete(array(
      'type = ?' => $item->getType(),
      'id = ?' => $item->getIdentity(),
    ));

    return $this;
  }

  public function getPaginator($text, $type = null)
  {
    return Zend_Paginator::factory($this->getSelect($text, $type));
  }

  public function getSelect($text, $type = null)
  {
    // Build base query
    $table = Engine_Api::_()->getDbtable('search', 'core');
    $db = $table->getAdapter();
    $select = $table->select()
      ->where(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (? IN BOOLEAN MODE)', $text)))
      ->order(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (?) DESC', $text)));

    // Filter by item types
    $availableTypes = Engine_Api::_()->getItemTypes();
    if( $type && in_array($type, $availableTypes) ) {
      $select->where('type = ?', $type);
    } else {
      $select->where('type IN(?)', $availableTypes);
    }
    
    return $select;
  }
  
  public function getHashTag( $keyword, $type = null, $filter = null ) {
    $result[ 'keyword' ]         = $keyword;
    $result[ 'type' ]            = $type;
    $result[ 'user' ]            = array();
    $result[ 'favo' ]            = array();
    $result[ 'whmedia_project' ] = array();

    switch ( $type ) {
      case 'favo':
         $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
         $favcircleName = $favcircleTable->info('name');
         $select = $favcircleTable->select()
            ->from($favcircleName)
            ->where("{$favcircleName}.private = ?", '0')
            ->where("{$favcircleName}.title LIKE ?", '%' . $keyword . '%')
            ->order("{$favcircleName}.favcircle_id desc");
          $favcricles = $favcircleTable->fetchAll($select)->toArray();

          $filterData = array();
          foreach ($favcricles as $circle) {

                $storagePhoto = Engine_Api::_()->getItem('storage_file', $circle["photo_id"]);
                $children = $storagePhoto->getChildren();
                $photoArray = array();
                foreach($children as $child){
                    $photoArray[$child["type"]] = $child["storage_path"];
                }

                $filterData[] = array(
                    "photos" => $photoArray,
                    "title" => $circle["title"],
                    "category" => $circle["category"],
                    "favcircle_id" => $circle["favcircle_id"]
                );
          }
          $result['favo'] = $filterData;

      break;

      case 'user':
        $table = Engine_Api::_()->getItemTable( 'user' );
        $db = $table->getAdapter();
        $select = $table->select()
          ->where( 'username LIKE "%'. $keyword .'%"' );
        $result[ 'user' ] = $select->query()->fetchAll();
      break;

      case 'whmedia_project' :
        //$objResult = $this->getSelect( $keyword, $type );
        //$result[ 'whmedia_project' ] = $objResult->query()->fetchAll();
    $projects  = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
    $where     = "title LIKE ?";
    $selectedFrom[ 'whmm' ] = 'engine4_whmedia_projects';
    $result[ 'whmedia_project' ] = $projects->select()
                                  ->from( $selectedFrom )
                                          ->where( 'title LIKE "%'. $keyword .'%"' )
                        ->query()
                        ->fetchAll();

    foreach( $result[ 'whmedia_project' ]  as $key => $value ) {
      $result[ 'whmedia_project' ][ $key ][ 'type' ] = 'whmedia_project'; 
      $result[ 'whmedia_project' ][ $key ][ 'id' ] = $value[ 'project_id' ];  
    }

      break;

      case 'tags' :
        $tags = Engine_Api::_()->getDbTable( 'tags', 'core' );
        $result[ 'tags' ] = $tags->fetchAll( 'text = "'. $keyword .'"' )->toArray();

    //$result[ 'whmedia_project' ] = Engine_Api::_()->whmedia()->getWhmediaPaginator( array( 'tags' => $keyword, 'bytime' => $filter ) );
          
    foreach( $result[ 'tags' ] as $key => $value ) {
      if (!empty($filter)) {
        $curr_time = time();
        switch ($filter) {
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
          $start_date = date('Y-m-d', $time);
        }
      }
      
      $tagMaps = Engine_Api::_()->getDbTable( 'tagMaps', 'core' );
      $select = $tagMaps->select()
                 ->from( array( 'tagmap' => 'engine4_core_tagmaps' ), array( '' ) )
                 ->joinLeft( array( 'project' => 'engine4_whmedia_projects' ), 'tagmap.resource_id = project.project_id', array( 'project.*' ) )
                 ->where( 'tagmap.tag_id ='. $value[ 'tag_id' ] )
                 ->setIntegrityCheck( false )
                 ->order( 'project.project_views desc' );

      if( !empty( $start_date ) ) {
        $select->where( ' project.creation_date >="'. $start_date .'"');
      }
      
            if ($filter == 'featured') {
                $select->joinLeft('engine4_whmedia_featured', 'project.project_id = engine4_whmedia_featured.featured_id', array())
                       ->where('engine4_whmedia_featured.featured_id is not null');
            }
      
      //Zend_Debug::dump( $select->query() ); exit;
      $result[ 'raw_whmedia_project' ] = $select->query()->fetchAll();                 
    }
    $result[ 'whmedia_project' ] = $result[ 'raw_whmedia_project' ];
    unset( $result[ 'raw_whmedia_project' ] );
    
    foreach( $result[ 'whmedia_project' ]  as $key => $value ) {
      if( is_null( $value[ 'project_id' ] ) ) {
        unset( $result[ 'whmedia_project' ][ $key ] );
      } else {
        $result[ 'whmedia_project' ][ $key ][ 'type' ] = 'whmedia_project'; 
        $result[ 'whmedia_project' ][ $key ][ 'id' ]   = $value[ 'project_id' ];  
      }
    }

        $whmedia = Engine_Api::_()->getApi( 'core', 'whmedia' );
        $viewer = Engine_Api::_()->user()->getViewer();

        $tag_id = $result[ 'tags' ][ 0 ][ 'tag_id' ];
        
        $result[ 'isFollowed' ] = false;
        if( $whmedia->isFollowed( $viewer, array( 'tag_id' => $tag_id ) ) ) {
          $result[ 'isFollowed' ] = true;
        }      

      break;

      default :
	   $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getItemTable( 'user' );
        $db = $table->getAdapter();
        $select = $table->select()
          ->where( 'username LIKE "%'. $keyword .'%" OR displayname LIKE "%'. $keyword .'%"' );
        $result[ 'userRaw' ] = $select->query()->fetchAll();
    $result[ 'userCount' ] = count( $result[ 'userRaw' ] );
    
    // Limit number to display
    if( $result[ 'userCount' ] != 0 ) {
      for( $i = 0; $i < $result[ 'userCount' ]; $i++ ) {
		
		$rawSubject = Engine_Api::_()->user()->getUser( $result[ "userRaw" ][ $i ][ "user_id" ] );
		// raw subject
		if( !$viewer->isBlockedBy($rawSubject) ) {
			
			$result[ 'user' ][ $i ] = $result[ 'userRaw' ][ $i ];
		}
	  
	  }
      $result[ 'userMore' ] = $result[ 'userCount' ] - 5;
    }
    
    // Remove empty
    foreach( $result[ 'user' ] as $user ) {
      if( empty( $user ) ) {
        array_pop( $result[ 'user' ] );
      }
    }

        $followHashtagTable = Engine_Api::_()->getDbTable( 'followhashtag', 'whmedia' );
    
    $projects    = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
    
    $where     = "title LIKE ?";
    $selectedFrom[ 'whmm' ] = 'engine4_whmedia_projects';
    $objResult = $projects->select()
                ->from( $selectedFrom )
                        ->where( 'title LIKE "%'. $keyword .'%"' );   
    
        //$objResult = $this->getSelect( $keyword, $type );
      
    $result[ 'whmedia_projectRaw' ]   = $objResult->query()->fetchAll();
    $result[ 'whmedia_projectCount' ] = count( $result[ 'whmedia_projectRaw' ] );

    if( $result[ 'whmedia_projectCount' ] != 0 ) {
      for( $i = 0; $i < 5; $i++ ) {
        $result[ 'whmedia_project' ][ $i ] = $result[ 'whmedia_projectRaw' ][ $i ];
      }
      $result[ 'whmedia_projectMore' ] = $result[ 'whmedia_projectCount' ] - 5;
    }
    
    foreach( $result[ 'whmedia_project' ] as $project ) {
      if( empty( $project ) ) {
        array_pop( $result[ 'whmedia_project' ] );        
      }
    }
    
    foreach( $result[ 'whmedia_project' ]  as $key => $value ) {
      $result[ 'whmedia_project' ][ $key ][ 'type' ] = 'whmedia_project'; 
      $result[ 'whmedia_project' ][ $key ][ 'id' ] = $value[ 'project_id' ];  
    }
    
    
        $table = Engine_Api::_()->getDbTable( 'tags', 'core' );
        $db = $table->getAdapter();
        $select = $table->select()
          ->where( 'text LIKE "%'. $keyword .'%"' );
        $result[ 'tagsRaw' ] = $select->query()->fetchAll();
    $result[ 'tagsCount' ] = count($result[ 'tagsRaw' ] ); 
    
    if( $result[ 'tagsCount' ] != 0 ) {
      for( $i = 0; $i < 5; $i++ ) {
        $result[ 'tags' ][ $i ] = $result[ 'tagsRaw' ][ $i ];
      }
      $result[ 'tagsMore' ] = $result[ 'tagsCount' ] - 5;
    }
    
    foreach( $result[ 'tags' ] as $tag ) {
      if( empty( $tag ) ) {
        array_pop( $result[ 'tags' ] );
      }
    }
      break;
    }

    return $result;
  }

  public function getAvailableTypes()
  {
    if( null === $this->_types ) {
      $this->_types = Engine_Api::_()->getDbtable('search', 'core')->getAdapter()
        ->query('SELECT DISTINCT `type` FROM `engine4_core_search`')
        ->fetchAll(Zend_Db::FETCH_COLUMN);
      $this->_types = array_intersect($this->_types, Engine_Api::_()->getItemTypes());
    }

    return $this->_types;
  }
}