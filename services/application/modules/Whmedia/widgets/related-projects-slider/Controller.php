<?php

class Whmedia_Widget_RelatedProjectsSliderController extends Engine_Content_Widget_Abstract
{
  
  public function indexAction()  {

    // Get subject and check auth
    if (!Engine_Api::_()->core()->hasSubject())  {
      return $this->setNoRender();
    }
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    if (!($subject instanceof Whmedia_Model_Project)) return $this->setNoRender();

    /*
     * SELECT *, COUNT(tag_id) AS similarity FROM `engine4_whmedia_projects`
LEFT JOIN `engine4_core_tagmaps` ON resource_type='whmedia_project' AND resource_id = `project_id`

WHERE resource_type='whmedia_project' AND tag_id IN (SELECT tag_id FROM `engine4_core_tagmaps` WHERE resource_id = 11) AND resource_id != 11

GROUP BY resource_id

ORDER BY similarity DESC, engine4_whmedia_projects.`creation_date` DESC
     */
    /*
    $table = Engine_Api::_()->getDbtable('projects', 'whmedia');
    $rName = $table->info('name');
    
    $tagTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tagName = $tagTable->info('name');
    $select_tags = $tagTable->select();
    $select_tags->from($tagName, array('tag_id'))
                ->where($tagName.'.resource_id = ?', $subject->getIdentity());
    
    $select = $table->select()->from($rName, array('*', 'similarity' => 'COUNT(tag_id)'))
                              ->joinLeft($tagName, 'resource_type="whmedia_project" AND ' . $tagName . '.resource_id = ' . $rName.'.project_id', array())
                              ->where($tagName.".`resource_type` = 'whmedia_project'")
                              ->where($tagName.".`resource_id` != ?", $subject->getIdentity())
                              ->where($tagName.'.tag_id in (?)', $select_tags)
                              ->group("{$tagName}.resource_id")
                              ->order('similarity DESC')
                              ->order($rName . '.creation_date DESC')
							  ->limit(5);   
    $this->view->user_projects = $table->fetchAll($select);
    
    if (!$this->view->user_projects->count()) return $this->setNoRender();
    */
  }
  
  public function getCacheKey() {
    if (Engine_Api::_()->core()->hasSubject()) {  
        return 'related_project_' . Engine_Api::_()->core()->getSubject()->getIdentity();
    }
    else return null;
  }

}