<?php
class Api_Api_Base extends Core_Api_Abstract { 

	protected function _quoteInto( $objDb, $where, $values = array() ) {

		foreach( $values as $value ) {
			$where = $objDb->quoteInto( $where, $value, '', 1 ); 
		}

		return $where;

	}
	
    /**
     * Check if value in array is null
     * if null then convert it to string null ( for ios issue )
	 *
     * @param  array  &$rowSet   feed data

     * @void
     */		
	protected function isArrValNull( &$rowSet ) {
		foreach( $rowSet as $key => $value ) {
			if( is_null( $value ) ) {
				$rowSet[ $key ] = 'null';
			}
		}	
	}

}