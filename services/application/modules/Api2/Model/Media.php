<?php
class Api2_Model_Media {

    const DEFAULT_IMAGE_XL = 'public/no-image-xl.jpg';
    const DEFAULT_IMAGE_L  = 'public/no-image-l.jpg';
    const DEFAULT_IMAGE_M  = 'public/no-image-m.jpg';
    const DEFAULT_IMAGE_S  = 'public/no-image-s.jpg';

    public $media_id;
    public $title;
    public $project_id;
    public $url;
    public $storage_path;
    public $extra_large;
    public $large;
    public $medium;
    public $small;
    public $type;
    public $media_code;
    public $image_width;
    public $image_height;

    public function __construct () {
        $this->media_id     = 0;
        $this->title        = '';
        $this->project_id   = 0;
        $this->url          = '';
        $this->storage_path = self::DEFAULT_IMAGE_M;
        $this->extra_large  = '';
        $this->large        = '';
        $this->medium       = '';
        $this->small        = '';
        $this->type         = 'null';
        $this->media_code   = 'null';
        $this->image_width  = 0;
        $this->image_height = 0;

        return $this;
    }

    public function initWithValues($params) {
        $this->setMediaId( $params['media_id'] );
        $this->setTitle( $params['title'] );
        $this->setProjectId( $params['project_id'] );
        $this->setUrl( $params['url'] );
        $this->setStoragePath( $params['storage_path'] );
        $this->setExtraLarge( $params[ 'extra_large' ] );
        $this->setLarge( $params[ 'large' ] );
        $this->setMedium( $params[ 'medium' ] );
        $this->setSmall( $params[ 'small' ] );
        $this->setTypee( $params['type'] );
        $this->setMediaCode( $params['media_code'] );
        $this->setImageWidth( $params['image_width'] );
        $this->setImageHeight( $params['image_height'] );

        return $this;
    }

    public function getVideoStructure () {
        return unserialize( $this->code );
    }

    public function setMediaId($param){ $this->media_id = $param;}
    public function getMediaId(){ return $this->media_id;}

    public function setTitle($param){
        if ( empty( $param ) || is_null( $param ) ) {
            $this->title = '';
        }else{
            $this->title = utf8_encode( $param );
        }
    }
    public function getTitle(){ return $this->title;}

    public function setProjectId($param){$this->project_id = $param;}
    public function getProjectId(){ return $this->project_id;}

    public function setUrl($param){
        if ( empty( $param ) || is_null( $param ) ) {
            $this->url = '';
        }else{
            $this->url = $param;
        }
    }
    public function getUrl(){ return $this->url;}

    public function setStoragePath($param){
        // TODO refactor logic
        if ( empty( $param ) || is_null( $param ) ) {
            $this->storage_path = '';
        }else{
            if ( $param != 'null' ) {
                $this->storage_path = $param;
            }
        }
    }
    public function getStoragePath(){ return $this->storage_path;}

    public function setExtraLarge ( $param ) {
        // Todo find a way to remove $param == 'null'
        if ( !empty( $param ) || !is_null( $param ) ) {
            if ( $param != 'null' ) {
                $this->extra_large = $param;
            }
        }
    }
    public function getExtraLarge () {
        return $this->extra_large;
    }

    public function setLarge ( $param ) {
        // Todo find a way to remove $param == 'null'
        if ( !empty( $param ) || !is_null( $param ) ) {
            if ( $param != 'null' ) {
                $this->large = $param;
            }
        }
    }
    public function getLarge () {
        return $this->large;
    }

    public function setMedium ( $param ) {
        // Todo find a way to remove $param == 'null'
        if ( !empty( $param ) || !is_null( $param ) ) {
            if ( $param != 'null' ) {
                $this->medium = $param;
            }
        }
    }
    public function getMedium () {
        return $this->medium;
    }

    public function setSmall ( $param ) {
        // Todo find a way to remove $param == 'null'
        if ( !empty( $param ) || !is_null( $param ) ) {
            if ( $param != 'null' ) {
                $this->small = $param;
            }
        }
    }
    public function getSmall () {
        return $this->small;
    }

    public function setTypee($param){$this->type = $param;}
    public function getTypee(){ return $this->type;}

    public function setMediaCode($param){
        if ( !is_null( $param ) && !empty( $param ) ) {
            $this->media_code = $param;
        }
    }
    public function getMediaCode(){ return $this->media_code;}

    public function setImageWidth($param){
        if ( empty( $param ) || is_null( $param ) ) {
            $this->image_width = 0;
        }else{
            $this->image_width = $param;
        }
    }
    public function getImageWidth(){ return $this->image_width;}

    public function setImageHeight($param){
        if ( empty( $param ) || is_null( $param ) ) {
            $this->image_height = 0;
        }else{
            $this->image_height = $param;
        }
    }
    public function getImageHeight(){ return $this->image_height;}
}