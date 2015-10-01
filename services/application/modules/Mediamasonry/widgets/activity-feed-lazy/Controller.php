<?php

class Mediamasonry_Widget_ActivityFeedLazyController extends Engine_Content_Widget_Abstract
{
    protected $_user;


    public function __construct() {
      if( Engine_Api::_()->core()->hasSubject('user') ) {
          $this->_user = Engine_Api::_()->core()->getSubject('user');
      }
      else {
          $this->_user = Engine_Api::_()->user()->getViewer();
      }
    }

    public function  indexAction() {
      if (!$this->_user->getIdentity()) return $this->setNoRender();
      /* get the class Whmedia_Model_DbTable_Stream */
      $select = Engine_Api::_()->getDbtable('stream', 'whmedia')->selectStreamProjects($this->_user);
      //$select->order('tmp_stream_projects.creation_date DESC');
      //$select->order('t.project_views DESC');
      $select->group("tmp_stream_projects.project_id"); 
      $select->order('t.creation_date DESC');
      $box_id = (int)Zend_Controller_Front::getInstance()->getRequest()->getParam('box_id');
      if (!empty($box_id)) {
          $box = Engine_Api::_()->getDbTable('circles', 'whmedia')->fetchRow(array('user_id = ?' => $this->_user->getIdentity(),
                                                                                   'circle_id = ?' => $box_id));
          if (!empty($box)) {
              $select->where(new Zend_Db_Expr("t.`user_id` IN (SELECT user_id FROM `engine4_whmedia_circleitems` WHERE `circle_id` = {$box_id})"));
              $this->view->addition_data = "{box_id:{$box_id}}";
          }
      }

      //echo $select->assemble();
      //die();

      /*
      SELECT `t`.* FROM 
        (SELECT `project_id`, `creation_date` FROM 
          ( SELECT * FROM `engine4_whmedia_stream` WHERE user_id = 1 OR user_id IN 
            (SELECT `user_id` FROM `engine4_whmedia_follow` WHERE `follower_id` = 1) 
          ORDER BY `creation_date` DESC) 
        AS tmp_stream GROUP BY tmp_stream.`project_id`) 
      AS tmp_stream_projects, `engine4_whmedia_projects` AS `t` 
      WHERE (t.`project_id` = `tmp_stream_projects`.`project_id`) 
      ORDER BY `tmp_stream_projects`.`creation_date` DESC

      SELECT `t`.project_views,  `t`.`creation_date` FROM 
        (SELECT DISTINCT followers.* FROM
          ( SELECT `project_id`, `creation_date` FROM 
            ( SELECT * FROM `engine4_whmedia_stream` WHERE user_id = 1 OR user_id IN 
              (SELECT `user_id` FROM `engine4_whmedia_follow` WHERE `follower_id` = 1) 
            ORDER BY `creation_date` DESC) AS tmp_stream GROUP BY tmp_stream.`project_id` 
            
            UNION 
            
            SELECT `tm`.`resource_id` AS `project_id`, `fh`.`creation_date` AS `creation_date` 
            FROM engine4_whmedia_followhashtag AS fh 
            JOIN engine4_core_tags AS t ON fh.hashtag_id=t.tag_id 
            JOIN engine4_core_tagmaps AS tm ON tm.tag_id=t.tag_id 
            WHERE follower_id=1 ORDER BY creation_date DESC
          ) AS followers
        ) AS tmp_stream_projects, `engine4_whmedia_projects` AS `t`
      WHERE (t.`project_id` = `tmp_stream_projects`.`project_id`) 
      GROUP BY `tmp_stream_projects`.`project_id` 
      ORDER BY `t`.`creation_date` DESC;
       
      */

      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
      $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
      $pageNumber = $this->_getParam('page', 1);
      $paginator->setCurrentPageNumber($pageNumber);
      
      $this->view->thumb_width = (int)$this->_getParam('thumb_width', 160);
      $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
      $this->view->sendScript = ($pageNumber > 1) ? false : true;
      if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext()  == 'html') {
        $this->view->only_items = true;
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      }
      else {
        $this->view->only_items = false;
      }
      $this->getElement()->removeDecorator('Title');
	  
	$this->view->requestFirst = 0; // default is zero, it means dont need for approval
	// check if auto follow or need to request
	
	
  }
  
  //public function getCacheKey()  {
  //  return 'WhActivityFeedLazy_' . $this->_user->getIdentity();
  //}
}