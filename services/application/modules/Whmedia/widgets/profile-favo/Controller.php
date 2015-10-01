<?php 

class Whmedia_Widget_ProfileFavoController extends Engine_Content_Widget_Abstract {

	public function  indexAction() {

        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject();
        if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
          return $this->setNoRender();
        }

        if($subject->getIdentity() != Engine_Api::_()->user()->getViewer()->getIdentity()){
            $this->view->following = true;
        }

		$page = (int) $this->_getParam('page', 1);
        $this->view->pageTitle = 'My Favorite Box';
        $user_id = $subject->getIdentity(); 
        $itempercount = 20 * $page;

        $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
        $favcircleName = $favcircleTable->info('name');
        $select = $favcircleTable->select()
            ->from($favcircleName)
            ->where("{$favcircleName}.user_id = ?", $user_id)
            ->where("{$favcircleName}.private = ?", "0")
            ->order("{$favcircleName}.favcircle_id desc");


        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($itempercount);
        $paginator->setCurrentPageNumber(1); 

        $filterData = array();
        foreach ($paginator as $paging) {

            $storagePhoto = Engine_Api::_()->getItem('storage_file', $paging->photo_id);
            $children = $storagePhoto->getChildren();
            $photoArray = array();
            foreach($children as $child){
                $photoArray[$child["type"]] = $child["storage_path"];
            }
            $followed = false;

            $followfavTable = Engine_Api::_()->getDbTable('followfav', 'whmedia');
            $followfavName = $followfavTable->info('name');

            $select = $followfavTable->select()
                ->from($followfavName)
                ->where("{$followfavName}.user_id = ?", $user_id)
                ->where("{$followfavName}.favcircle_id = ?", $paging->favcircle_id)
                ->where("{$followfavName}.follower_id = ?", Engine_Api::_()->user()->getViewer()->getIdentity());
                 
            $result = $followfavTable->fetchAll($select)->toArray();
            if( count($result) === 0){    
                $followed = true;
            }
            $filterData[] = array(
                "photos" => $photoArray,
                "title" => $paging->title,
                "category" => $paging->category,
                "favcircle_id" => $paging->favcircle_id,
                "isFollow" => $followed
            );
        }
        $page++;
        $this->view->favcircle = $filterData;
        $this->view->current_user_id = $user_id;
        $this->view->page = $page;
        $this->view->totalitem = $paginator->getTotalItemCount();
        $this->view->itempercount = $itempercount;
			
	}

}