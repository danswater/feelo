<?php

class Mediamasonry_Widget_ProfileHashtagController extends Engine_Content_Widget_Abstract {


	public function  indexAction() {

		// Don't render this if not authorized

		$subject = Engine_Api::_()->core()->getSubject();
        if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
          return $this->setNoRender();
        }

        if($subject->getIdentity() != Engine_Api::_()->user()->getViewer()->getIdentity()){
            $this->view->notUser = true;
        }

	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }


		$tableFollow = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' ); 
	    $dbFollow = $tableFollow->getAdapter();
	    $select = $tableFollow->select()
	    					->distinct()
	    					->from(array('fh' => 'engine4_whmedia_followhashtag'), array("fh.hashtag_id"))
	    					->join(array("t"=>"engine4_core_tags"), 'fh.hashtag_id=t.tag_id', array('t.text'))
	    					->where("fh.follower_id = ?", $subject->getIdentity());
	   	$select->setIntegrityCheck(false);

	   	$query = $select->query();
	    $this->view->tags = $hashtag = $query->fetchAll();

	}

}