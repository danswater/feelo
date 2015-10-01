<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Var.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Activity_Model_Helper_Favopic extends Activity_Model_Helper_Abstract
{
  /**
   * 
   * @param string $value
   * @return string
   */
  public function direct($arr)
  {
    $value = "";
  	if(is_array($arr) && isset($arr["favcircle_id"])){

        $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
        $favcircleName = $favcircleTable->info('name');
        $select = $favcircleTable->select()
          ->from($favcircleName)
          ->where("{$favcircleName}.favcircle_id = ?",$arr["favcircle_id"]);

        $favResult = $favcircleTable->fetchAll($select)->toArray();
        $record = $favResult[0];

        $storagePhoto = Engine_Api::_()->getItem('storage_file', $record[ "photo_id" ] );
        $children = $storagePhoto->getChildren();
        $photoArray = array();
        foreach($children as $child){
          $photoArray[$child["type"]] = $child["storage_path"];
        }

        $uri = Zend_Controller_Front::getInstance()->getRouter()->assemble( array( "controller" => "favboxes", "action" => "menprojectlist", "favcircle_id" => $arr["favcircle_id"] ), "default", true );

  		$value = "<a href=\"" . $uri . "\">" 
  				. "<img src=\"" . $photoArray["icon"] . "\" alt=\"" . $record["title"] . "\" />"
  				. "</a>";
  	}
    return $value;
  }
}