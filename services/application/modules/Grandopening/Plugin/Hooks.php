<?php

class Grandopening_Plugin_Hooks {

    public function onUserLoginAfter($event) {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('grandopening_enable', 0))
            return;
        if (Engine_Api::_()->grandopening()->isAdmin($event->getPayload())) {
            setcookie('whGOadmin', 1);
        }
    }

    public function onUserLogoutBefore($event) {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('grandopening_enable', 0))
            return;
        if (Engine_Api::_()->grandopening()->isAdmin($event->getPayload())) {
            setcookie('whGOadmin', 0);
        }
    }

}