<?php
class Api_Helper_DetermineFeedType {
    const LINK  = 'LINK';
    const VIDEO = 'VIDEO';
    const PHOTO = 'PHOTO';

	public static function execute ( $media ) {
        $localMedia = '';

        if ( is_object( $media ) ) {
            $localMedia[ 'url' ]        = $media->url;
            $localMedia[ 'media_code' ] = $media->media_code;
        } else {
            $localMedia = $media;
        }

        if ( $localMedia[ 'url' ] != 'null' ) {
            return self::LINK;
        }

        if ( $localMedia[ 'media_code' ] != 'null' ) {
            return self::VIDEO;
        }

        return self::PHOTO;

	}
}