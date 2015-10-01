<?php

class Grandopening_Widget_CountdownTimerController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $settings = Engine_Api::_()->getApi('settings', 'core');

        if (!$settings->getSetting('use_date', 0))
            return $this->setNoRender();

        if (!$settings->getSetting('grandopening_enable', 0))
            return $this->setNoRender();

        $time = Engine_Api::_()->grandopening()->getEndTime();
        if ($time < time()) {
            return $this->setNoRender();
        }
        $this->view->endtime = $time;
    }

}