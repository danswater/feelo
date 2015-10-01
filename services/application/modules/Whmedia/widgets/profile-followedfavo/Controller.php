<?php 

class Whmedia_Widget_ProfileFollowedfavoController extends Engine_Content_Widget_Abstract {

	public function  indexAction() {

    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    $this->view->following = true;

		$page = (int) $this->_getParam('page', 1);
    $this->view->pageTitle = 'My Favorite Box';
    $itempercount = 20 * $page;

    $favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
    $favcircleName = $favcircleTable->info('name');

    $fallowcircleTable = Engine_Api::_()->getDbTable('followfav', 'whmedia');
    $fallowcircleName = $fallowcircleTable->info('name');
    $select = $fallowcircleTable->select()
      ->from($fallowcircleName)
      ->setIntegrityCheck(false) 
      ->joinLeftUsing ($favcircleName, "favcircle_id")
      ->where("{$fallowcircleName}.follower_id = ?", $subject->getIdentity())
      ->where("{$favcircleName}.private = ?", "0")
      ->order("{$fallowcircleName}.favcircle_id desc");

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

      $filterData[] = array(
        "photos" => $photoArray,
        "title" => $paging->title,
        "category" => $paging->category,
        "favcircle_id" => $paging->favcircle_id,
        "user_id" => $paging->user_id
      );
    }
    $page++;
    $this->view->favcircle = $filterData;
    $this->view->current_user_id = $view_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->page = $page;
    $this->view->totalitem = $paginator->getTotalItemCount();
    $this->view->itempercount = $itempercount;

    if( $view_id != $subject->getIdentity() )
      $this->view->following = false;
       
	}

}