<?php
class Api2_Helpers_Utils {
	/**
     * Converts a null valued object to a specified string
	 * 
     * @param  the variable to be replaced
     * @param  the value that will replace the first parameter
     * @return string
     */
	public static function nullToString( &$data, $replace) {
		if( !is_null( $data ) ) {
			throw new Exception("Value must be NULL");
		} 
		else if ( !isset( $replace ) || $replace != NULL) {
			throw new Exception("Value must contain a non-empty string");
		}
		else {
			$data = $replace;
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

    public static function formatNumber( $n ) {

		if( $n == 'null' ) {
			return '';
		}
		
		if( !is_int( $n ) ) {
			$n = count( $n );
		}
		
        // first strip any formatting;
        $n = (0+str_replace(",","",$n));

        // is this a number?
        if(!is_numeric($n)) return false;

        // now filter it;
        if ( $n >= 1000000000000 ) {

        	return round( ( $n / 1000000000000 ), 2 ).'t';
        
        } else if ( $n >= 1000000000 ) {

        	return round( ( $n / 1000000000 ), 2 ).'b';
        
        } else if ( $n >= 1000000 ) {

        	return round( ( $n / 1000000 ), 2 ).'m';
        
        } else if ( $n >= 1000 ) {

        	return round( ( $n / 1000 ), 2 ).'k';
        }

        return number_format( $n );
    }

}