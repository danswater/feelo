<?php

class Widget_ProfileStatusController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        $subject = Engine_Api::_()->core()->getSubject('user');

        $this->view->auth = ( $subject->authorization()->isAllowed(null, 'view') );

        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsValuesByAlias($subject);
        if (!empty($fieldsByAlias['about_me'])) {
            $this->view->aboutMe = $value = $fieldsByAlias['about_me'];
        }
    }

}