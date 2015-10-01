<?php

class Whcomments_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    public function __construct($application) {
        parent::__construct($application);
        $this->initViewHelperPath();
    }

}