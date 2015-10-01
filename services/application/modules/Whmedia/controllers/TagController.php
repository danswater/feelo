<?php

class Whmedia_TagController extends Core_Controller_Action_Standard {


	public function getHashById($hash_id){
        $tableTag = Engine_Api::_()->getDbtable( 'tags', 'core' );
        $dbTag = $tableTag->getAdapter();

        $select = $tableTag->select()
          ->where( 
            new Zend_Db_Expr( 
              $dbTag->quoteInto( 'tag_id = ?',  $hash_id ) 
              ) 
        );
        
        return $select->query()->fetch();
	}


	public function indexAction(){

		$allTags = array();
		$followed = array();
		$tags = array();
	    $incond = array();

		$project_id = $this->_getParam('project_id', false);
		$hash_id = $this->_getParam('hash_id', false);

		if($project_id !== false){
	    	$allTags =  Engine_Api::_()->getItem('whmedia_project', $project_id)->gettags();
	    	foreach ($allTags as $tag) {
		    	$incond[] = $tag->getTag()->tag_id;
		    	$tags[] = array("tag_id" => $tag->getTag()->tag_id, "text" =>  $tag->getTag()->text);
		    }
	    }elseif($hash_id !== false){
	    	$allTags = $this->getHashById($hash_id);
		    $incond[] = $allTags["tag_id"];
		    $tags[] = array("tag_id" => $allTags["tag_id"], "text" =>  $allTags["text"]);
	    }
	    
	    $this->view->tags = $tags;
	    
	    if(count($incond) > 0){
		    $tableFollow = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' ); 
	     	$dbFollow = $tableFollow->getAdapter();
	      	$viewer = Engine_Api::_()->user()->getViewer()->toArray();
	        $select = $tableFollow->select()->where("hashtag_id IN (?)", $incond )->where("follower_id = ?", $viewer[ 'user_id' ]);
	        $query = $select->query();
	        $hashtag = $query->fetchAll();
	        foreach($hashtag as $hash){
	        	$followed[$hash["hashtag_id"]] = $hash["follow_id"]; 
	        }
    	}
        $this->view->followed = $followed;

	}

	public function hashtagpostAction(){

		$hashtag_id = $this->_getParam('tid', false);

		$tableFollow = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' ); 
	    $dbFollow = $tableFollow->getAdapter();
	    $viewer = Engine_Api::_()->user()->getViewer()->toArray();
	    $select = $tableFollow->select()->where("hashtag_id = ?", $hashtag_id )->where("follower_id = ?", $viewer[ 'user_id' ]);
	    $query = $select->query();
	    $hashtag = $query->fetchAll();

	    if(count($hashtag) == 0){
	    	// redirect
	    	return $this->_helper->redirector->gotoRoute(array( "module" => "whmedia", "controller" => "activity-feed" ), 'default', true);
	    }

	    $tmTable = Engine_Api::_()->getDbtable('tags', 'core');
        $select_tags = $tmTable->select()->where("tag_id = ?", $hashtag_id);
        $hashtag_query = $select_tags->query();
        $hashtag = $hashtag_query->fetchAll();
       
	    $this->view->paginator = $paginator =  Engine_Api::_()->whmedia()->getWhmediaPaginator( array( 'tags' => $hashtag[0]["text"] ) );
	    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 20));
        $pageNumber = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($pageNumber);

        $this->view->hashtag = $hashtag[0];

	    $this->view->thumb_width = (int)$this->_getParam('thumb_width', 259);

   		$this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
        $this->view->sendScript = ($pageNumber > 1) ? false : true;

        $this->view->followed = true;

        if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext()  == 'html') {
            $this->view->only_items = true;
        }
       else {
           $this->view->only_items = false;
        }

        $this->view->identity = 645;

	}

}