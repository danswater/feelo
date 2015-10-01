<?php
class Api_Model_File extends Storage_Model_File {
  public function getStoragePath () {
    return $this->storage_path;
  }

  public function getImageDimension () {
    $data = array();
    list( $data[ 'image_width' ], $data[ 'image_height' ] ) = getimagesize ( $this->storage_path );
    return $data; 
  }
}