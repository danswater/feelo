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
class Activity_Model_Helper_Favo extends Activity_Model_Helper_Abstract
{
  /**
   * 
   * @param string $value
   * @return string
   */
  public function direct($arr)
  {

  	$baseURL = Zend_Registry::get('StaticBaseUrl');
  	$value = "";
  	if(is_array($arr) && isset($arr["favcircle_id"])){

        $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
        $favcircleName = $favcircleTable->info('name');
        $select = $favcircleTable->select()
          ->from($favcircleName)
          ->where("{$favcircleName}.favcircle_id = ?",$arr["favcircle_id"]);

        $favResult = $favcircleTable->fetchAll($select)->toArray();

          $uri = Zend_Controller_Front::getInstance()->getRouter()->assemble( array( "controller" => "favboxes", "action" => "menprojectlist", "favcircle_id" => $arr["favcircle_id"] ), "default", true );

  		$value = "<a href=\"" . $uri . "\">" 
  				. $favResult[0]["title"]
  				. "</a>";
  	}
    return $value;
  }
}