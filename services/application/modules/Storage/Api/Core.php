<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Storage_Api_Core extends Core_Api_Abstract
{
  const SPACE_LIMIT_REACHED_CODE = 3999;
  
  public function getService($serviceIdentity = null)
  {
    return Engine_Api::_()->getDbtable('services', 'storage')
      ->getService($serviceIdentity);
  }

  public function get($id, $relationship = null)
  {
    return Engine_Api::_()->getItemTable('storage_file')
        ->getFile($id, $relationship);
  }

  public function lookup($id, $relationship)
  {
    return Engine_Api::_()->getItemTable('storage_file')
        ->lookupFile($id, $relationship);
  }

  public function create($file, $params)
  {
    $original =  Engine_Api::_()->getItemTable('storage_file')
        ->createFile($file, $params);


    if( strtolower( $original->extension ) == "jpg" || 
        strtolower( $original->extension ) == "jpeg" || 
        strtolower( $original->extension ) == "gif" ||
        strtolower( $original->extension ) == "png" ){
      // create 4 iamges // 
      $fileStorageTable = Engine_Api::_()->getDbtable('files', 'storage');

      $xl_image = Engine_Image::factory();
      $xl_image->open( $file )
        ->resize( 1600, 1200, false )
        ->write( $file )
        ->destroy(); 
      Engine_Api::_()->getItemTable('storage_file')
        ->createFile( $file, array_merge ($params, array(
            'type' => 'extra_large',
        ) ) );

      $l_image = Engine_Image::factory();
      $l_image->open( $file )
        ->resize( 960, 720, false )
        ->write( $file )
        ->destroy();
      Engine_Api::_()->getItemTable('storage_file')
        ->createFile( $file, array_merge ($params, array(
            'type' => 'large',
        ) ) );  

      $m_image = Engine_Image::factory();
      $m_image->open( $file )
        ->resize( 640, 480, false )
        ->write( $file )
        ->destroy();
      Engine_Api::_()->getItemTable('storage_file')
        ->createFile( $file, array_merge ($params, array(
            'type' => 'medium',
        ) ) );

      $s_image = Engine_Image::factory();
      $s_image->open( $file )
        ->resize( 300, 225, false )
        ->write( $file )
        ->destroy();
      Engine_Api::_()->getItemTable('storage_file')
        ->createFile( $file, array_merge ($params, array(
            'type' => 'small',
        ) ) );

    }
    // create 4 iamges //

    return $original;
  }

  public function getStorageLimits()
  {
    return Engine_Api::_()->getItemTable('storage_file')
        ->getStorageLimits();
  }
}