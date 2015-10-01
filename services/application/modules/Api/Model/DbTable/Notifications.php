<?php
class Api_Model_DbTable_Notifications extends Activity_Model_DbTable_Notifications {
	protected $_name     = 'activity_notifications';
	protected $_rowClass = 'Api_Model_Notification';
	
	public function fetchById ( $user, $params ) {
		$suffix = $params[ 'offset' ] . '0';

		$select = $this->select();
		$select->where( 'user_id = ?', $user->getIdentity() );
		$select->where( 'type != "friend_follow_request" ' );
		$select->order( array( 'date DESC' ) );
		$select->limit( 10, $suffix );

		$rowSet = $this->fetchAll( $select );
//  print_r( $rowSet ); exit;
		return $rowSet;
	}
	
	public function getNotificationCount ( $user ) {
		$select = $this->select();
		$select->where( 'user_id = ?', $user->getIdentity() );

		$rowSet = $this->fetchAll( $select );
		
		$count = 0;
		foreach( $rowSet as $key => $notification ) {
			if ( $notification->read == 0 ) {
				$count++;
			}
		}

		return $count;
	}
	
	public function fetchRequestNotification ( $user ) {
		$suffix = $params[ 'offset' ] . '0';
		
		$select = $this->select();
		$select->where( 'user_id = ?', $user->getIdentity() );
		$select->where( 'type = "friend_follow_request"' );
		$select->where( 'mitigated = 0 ' );
		$select->order( array( 'date DESC' ) );
		$select->limit( 10, $suffix );

		$rowSet = $this->fetchAll( $select );
		
		return $rowSet;
	}
	
	public function readNotifcationCount ( $user ) {		
		$select = $this->select();
		$select->from( $this );
		$select->where( 'user_id = ?', $user->getIdentity() );

		$notifications = $this->fetchAll( $select );

		$ret = 0;
		$test = array();
		foreach( $notifications as $notification ) {
			if ( $notification->read == 0  ) {
				$ret += 1;
			}
		}
		return $ret;
	}
	
	public function readNewGeneralNotification ( $user ) {
		$select = $this->select();
		$select->from( $this );
		$select->where( 'user_id = ?', $user->getIdentity() );
		$select->where( 'type != ?', 'friend_follow_request' );	

		$notifications = $this->fetchAll( $select );

		$ret = 0;
		foreach( $notifications as $notification ) {
			if ( $notification->read == 0 ) {
				$ret = 1;
				break;
			}
		}
		return $ret;
	}
	
	public function readNewRequestNotification ( $user ) {
		$select = $this->select();
		$select->from( $this );
		$select->where( 'user_id = ?', $user->getIdentity() );
		$select->where( 'type = ?', 'friend_follow_request' );	

		$notifications = $this->fetchAll( $select );
		
		$ret = 0;
		foreach( $notifications as $notification ) {
			if ( $notification->read == 0 && $notification->mitigated == 0 ) {
				$ret = 1;
				break;
			}
		}
		return $ret;
	}
	
	public function updateToReadNotification ( $user ) {
		$where = array(
		  '`user_id` = ?' => $user->getIdentity(),
		  '`read` = ?' => 0
		);

		if( !empty($ids) ) {
		  $where['`notification_id` IN(?)'] = $ids;
		}

		
		$ret = $this->update(array('read' => 1), $where);
		
		return $ret;
	}
	
}