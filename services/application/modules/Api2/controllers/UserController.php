<?php
class Api2_UserController extends Zend_Rest_Controller {

	public function init() {
		$this->getHelper( 'layout' )->getLayoutInstance()->disableLayout();
		$this->getHelper( 'viewRenderer' )->setNoRender();

		$this->getHelper( 'AjaxContext' )
			->addActionContext( 'get', 'json' )
			->addActionContext ( 'post', 'json' )
			->addActionContext ( 'edit', 'json' )
			->addActionContext ( 'put', 'json' )
			->addActionContext ( 'delete', 'json' )
			->initContext ( 'json' );
	}

	public function indexAction() {
	    $this->getResponse()->setHttpResponseCode( 200 );
	    $this->getHelper( 'json' )->sendJson( array(
	    	'message' => 'Hello World from index'
	    ) );
	}

	public function postAction() {

		$token = $this->_getParam ( 'token', "" );

		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		$result = array( "data" => array(), "error" => array() );
		$auth = $table->fetchRow ( $select );
		$api2User = Engine_Api::_()->getApi( 'user', 'api2' );
		$values = $this->getRequest()->getPost();
		$params = $values;



		if (count ( $auth ) != 1) {

			// for dont need to authorize
			switch( $params[ "method"] ){

				case 'forgotPassword':
					$result = $api2User->forgotPassword( $params );
				break;

				case 'resetPassword':
					$result = $api2User->resetPassword( $params );
				break;

				default:
					$result = array( "error" => 'Unauthorized 101' ) ;
				break;

			}

			$this->getHelper( 'json' )->sendJson( $result );
			return;
		}

		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );

		switch( $params[ "method"] ){

			case "fetchByUsername":
				try {
					$result[ "data" ] = $api2User->fetchByUsername( $user, $params );
					$result[ "error" ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			case "fetchPosts":
				try {
					$api2Project = Engine_Api::_()->getApi( 'project', 'api2' );
					$result[ 'data' ] = $api2Project->fetchFeedByUserId( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			case "fetchHashtags":
				try {
					$api2Hashtag = Engine_Api::_()->getApi( 'hashtag', 'api2' );
					$result[ 'data' ] = $api2Hashtag->fetchHashtagsByUserId( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			case "fetchLikePosts":
				try {
					$api2Project = Engine_Api::_()->getApi( 'project', 'api2' );
					$result[ 'data' ] = $api2Project->fetchLikesByUserId( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			case "fetchFavos":
				try {
					$api2Project = Engine_Api::_()->getApi( 'favo', 'api2' );
					$result[ 'data' ] = $api2Project->fetchFavosByUserId( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error'] = $e->getMessage();
				}
			break;

			case 'createFavo' :
				try {
					$api2Favo = Engine_Api::_()->getApi( 'favo', 'api2' );
					$result[ 'data' ] = $api2Favo->createFavo( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			case 'createFavo2' :
				try {
					$api2Favo = Engine_Api::_()->getApi( 'favo', 'api2' );
					$result[ 'data' ] = $api2Favo->createFavo2( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			case 'updateFavo' :
				try {
					$api2Favo = Engine_Api::_()->getApi( 'favo', 'api2' );
					$result[ 'data' ] = $api2Favo->updateFavo( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			case 'deleteFavo' :
				try {
					$api2Favo = Engine_Api::_()->getApi( 'favo', 'api2' );
					$result[ 'data' ] = $api2Favo->deleteFavo( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			case 'deleteFavo2' :
				try {
					$api2Favo = Engine_Api::_()->getApi( 'favo', 'api2' );
					$result[ 'data' ] = $api2Favo->deleteFavo2( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			case 'fetchFavoPosts' :
				try {
					$api2Favo = Engine_Api::_()->getApi( 'favo', 'api2' );
					$result[ 'data' ] = $api2Favo->fetchFavoPosts( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			case 'addToFavo' :
				try {
					$api2Favo = Engine_Api::_()->getApi( 'favo', 'api2' );
					$result[ 'data' ] = $api2Favo->addToFavo( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'removeFromFavo' :
				try {
					$api2Favo = Engine_Api::_()->getApi( 'favo', 'api2' );
					$result[ 'data' ] = $api2Favo->removeFromFavo( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'followFavo' :
				try {
					$api2Favo = Engine_Api::_()->getApi( 'favo', 'api2' );
					$result[ 'data' ] = $api2Favo->followFavo( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'unFollowFavo' :
				try {
					$api2Favo = Engine_Api::_()->getApi( 'favo', 'api2' );
					$result[ 'data' ] = $api2Favo->unFollowFavo( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'fetchGroupList' :
				try {
					$api2Box = Engine_Api::_()->getApi( 'box', 'api2' );
					$result[ 'data' ]  = $api2Box->fetchBoxByUserId( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ]  = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'fetchPostByGroupId' :
				try {
					$api2Box = Engine_Api::_()->getApi( 'box', 'api2' );
					$result[ 'data' ]  = $api2Box->fetchFeedByGroupId( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ]  = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'createGroup' :
				try {
					$api2Box = Engine_Api::_()->getApi( 'box', 'api2' );
					$result[ 'data' ] = $api2Box->createGroup( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'updateGroup' :
				try {
					$api2Box = Engine_Api::_()->getApi( 'box', 'api2' );
					$result[ 'data' ] = $api2Box->updateGroup( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'deleteGroup' :
				try {
					$api2Box = Engine_Api::_()->getApi( 'box', 'api2' );
					$result [ 'data' ] = $api2Box->deleteGroup( $user, $params );
					$result [ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'addToGroup' :
				try {
					$api2Box = Engine_Api::_()->getApi( 'box', 'api2' );
					$result [ 'data' ] = $api2Box->addToGroup( $user, $params );
					$result [ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'removeToGroup' :
				try {
					$api2Box = Engine_Api::_()->getApi( 'box', 'api2' );
					$result [ 'data' ] = $api2Box->removeToGroup( $user, $params );
					$result [ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			default:
				$result = array(
					"data"  => array(),
					"error" => "No method found."
				);
			break;

		}

		$this->getHelper( 'json' )->sendJson( $result );

	}

	public function getAction( ) {
	}

	public function putAction() {
	}

	public function deleteAction() {
	}

}
