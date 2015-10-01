<?php
class Api_Model_Media extends Whmedia_Model_Media {
	public function getVideoStructure () {
		return unserialize( $this->code );
	}
}