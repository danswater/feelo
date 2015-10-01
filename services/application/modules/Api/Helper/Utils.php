<?php
class Api_Helper_Utils {
	public static function stringnull ( &$object ) {
		foreach( $object as $key => $value ) {
			if ( is_null( $value ) ) {
				$object->$key = 'null';
			}
		}
	}

	public static function formatResponse ( $object = null, $errorMsg = null ) {
		if ( is_null( $errorMsg ) ) {
			$error = array();
		} else {
			$object  = array();
			$error[] = $errorMsg;
		}

		return array(
			'data'  => $object,
			'error' => $error
		);
	}

	public static function addToCollection ( &$collection, Engine_Db_Table_Rowset $rowSet ) {
		foreach( $rowSet as $key => $value ) {
			$collection->add( $value );
		}
	}

    /**
     * Convert timestamp into human readable time
	 * 
     * @param  string|int  	$date   		raw timestamp data
     * @param string|int 	$granularity	???
     * @return string
     */		
	public static function timeAgo($date, $granularity = 2) {
		$date = strtotime ( $date );
		$difference = time () - $date;
		$periods = array (
				'decade' => 315360000,
				'year' => 31536000,
				'month' => 2628000,
				'week' => 604800,
				'day' => 86400,
				'hour' => 3600,
				'minute' => 60,
				'second' => 1 
		);
		if ($difference < 5) {
			$retval = "posted just now";
			return $retval;
		} else {
			foreach ( $periods as $key => $value ) {
				if ($difference >= $value) {
					$time = floor ( $difference / $value );
					$difference %= $value;
					$retval .= ($retval ? ' ' : '') . $time . ' ';

					$retval .= (($time > 1) ? $key . 's' : $key);
					$granularity --;
				}
				if ($granularity == '0') {
					break;
				}
			}
			
			$arrPrefix = explode( ' ', $retval );
			
			for( $i = 0; $i < 2; $i++ ) {
				array_pop( $arrPrefix );
			}

			return implode( ' ', $arrPrefix );
		}
	}

	public static function removeFromCollection ( $collection, $callback ) {
		foreach( $collection as $key => $entity ) {
			if ( !call_user_func( $callback, $entity ) ) {
				$collection[ $key ] = $entity;
			}
		}
		return $collection;
	}
}