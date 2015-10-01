<?php

class Whmedia_View_Helper_TimeDuration extends Zend_View_Helper_Abstract {
  
    public function timeDuration($time) {
        $h = (int)($time/3600);
        $m = (int)(($time - $h*3600)/60);
        $s = (int)($time - $h*3600 - $m*60);
        return $this->_format($h) . ':' . $this->_format($m) . ':' . $this->_format($s);
    }

    protected function _format($data) {
        if ($data < 10)
            return '0' . $data;
        else {
            return $data;
        }
    }
}