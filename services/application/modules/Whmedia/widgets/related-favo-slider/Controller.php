<?php

class Whmedia_Widget_RelatedFavoSliderController extends Engine_Content_Widget_Abstract
{
  
	public function indexAction()  {
		if (!Engine_Api::_()->core()->hasSubject())  {
	      return $this->setNoRender();
	    }
	    $subject = Engine_Api::_()->core()->getSubject();
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
	      return $this->setNoRender();
	    }
	    if (!($subject instanceof Whmedia_Model_Project)) return $this->setNoRender();

	    $ftable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
        $fname = $ftable->info('name');

	    $fcTable = Engine_Api::_()->getDbTable('favcircleitems', 'whmedia');
        $fcName = $fcTable->info('name');

	    $rtable = Engine_Api::_()->getDbtable('projects', 'whmedia');
	    $rName = $rtable->info('name');
	    
	    $tagTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
	    $tagName = $tagTable->info('name');
	    $select_tags = $tagTable->select();
	    $select_tags->from($tagName, array('tag_id'))
	                ->where($tagName.'.resource_id = ?', $subject->getIdentity());

		/*	   
	     $select = $rtable->select()->from($rName, array('*', new Zend_Db_Expr("{$fcName}.*"), 'similarity' => 'COUNT(tag_id)'))
                              ->joinLeft($tagName, 'resource_type="whmedia_project" AND ' . $tagName . '.resource_id = ' . $rName.'.project_id', array())
                              ->joinInner($fcName, "{$fcName}.project_id = {$rName}.project_id", array())
                              ->where($tagName.".`resource_type` = 'whmedia_project'")
                              ->where($tagName.".`resource_id` != ?", $subject->getIdentity())
                              ->where($tagName.'.tag_id in (?)', $select_tags)
                              ->group("{$tagName}.resource_id")
                              ->order('similarity DESC')
                              ->order($rName . '.creation_date DESC')
							  ->limit(5);   
    	$this->view->user_favo_projects = $rtable->fetchAll($select);
    	*/
	 	//$select = $fcTable->select()->from($fcName, array("*", 'similarity' => 'COUNT(tag_id)'))
	 	/*
    	$select = $ftable->select()->from($fname, array("*", 'similarity' => 'COUNT(tag_id)'))
    							  ->distinct()
	 							  ->joinInner($fcName, "{$fcName}.favcircle_id={$fname}.favcircle_id", array())
	 							  ->joinLeft($rName, $rName.'.project_id = '.$fcName.'.project_id', array())
	 							  ->joinLeft($tagName, 'resource_type="whmedia_project" AND ' . $tagName . '.resource_id = ' . $rName.'.project_id', array())
	                              ->where($tagName.".`resource_type` = 'whmedia_project'")
	                              ->where($tagName.".`resource_id` != ?", $subject->getIdentity())
	                              ->where($tagName.'.tag_id in (?)', $select_tags)
	                              ->group("{$tagName}.resource_id")
	                              ->order('similarity DESC')
	                              ->order($rName . '.creation_date DESC')
								  ->limit(5); 
	    $results = $fcTable->fetchAll($select);
	    $filterData = array();
	    foreach($results as $result):
	    	$dataArray = $result->toArray();
	    	$storagePhoto = Engine_Api::_()->getItem('storage_file', $dataArray["photo_id"]);
            $children = $storagePhoto->getChildren();
            $photoArray = array();
            foreach($children as $child){
                $photoArray[$child["type"]] = $child["storage_path"];
            }

            $filterData[] = array(
               	"photos" => $photoArray,
                "title" => $dataArray["title"],
                "category" => $dataArray["category"],
                "favcircle_id" => $dataArray["favcircle_id"]
            );
	    endforeach;
	    $this->view->favo_projects = $filterData;


	    if (!$results->count()) return $this->setNoRender();
	    */
	}	
}
