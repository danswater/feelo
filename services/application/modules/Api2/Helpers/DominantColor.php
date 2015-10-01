<?php
use ColorThief\ColorThief;

class Api2_Helpers_DominantColor {
	public static function execute ( $storagePath ) {

        $path = APPLICATION_PATH . DS . $storagePath;
        if ( file_exists( $path )) {
            $color = ColorThief::getColor( $path );
            $ret = array(
                'R' => $color[ 0 ],
                'G' => $color[ 1 ],
                'B' => $color[ 2 ]
            );

        }

        return $ret;
    }

    public static function secondExecution ( $projectId, $storagePath ) {

        $dbTableColors = Engine_Api::_()->getDbTable( 'colors', 'api2' );
        $colors = $dbTableColors->readByProjectId( $projectId );

        if ( $colors ) {
            return $colors;
        }

        $path = APPLICATION_PATH . DS . $storagePath;
        if ( file_exists( $path )) {
            $color = ColorThief::getColor( $path );
            $ret = array(
                'R' => $color[ 0 ],
                'G' => $color[ 1 ],
                'B' => $color[ 2 ]
            );

            $dbTableColors->create( $projectId, $ret );
        }

        return $ret;
    }

}